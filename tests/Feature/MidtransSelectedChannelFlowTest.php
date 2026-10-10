<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\InfoPembayaran;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\MidtransPaymentStatusService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransSelectedChannelFlowTest extends TestCase
{
    public function test_halaman_pembayaran_hanya_memakai_satu_notifikasi_status(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrangTua/PembayaranDigitalController.php'));
        $view = file_get_contents(resource_path('views/wali-siswa/pembayaran/digital.blade.php'));

        $this->assertStringNotContainsString('Status pembayaran telah diperbarui.', $controller);
        $this->assertSame(
            1,
            substr_count($view, 'Pembayaran telah dikonfirmasi otomatis dan tagihan siswa sudah diperbarui.'),
        );
        $this->assertStringContainsString("in_array(\$gatewayStatus, ['deny', 'failure'], true)", $view);
        $this->assertStringContainsString("\$gatewayStatus === 'expire'", $view);
        $this->assertStringContainsString("\$gatewayStatus === 'cancel'", $view);
    }

    public function test_bayar_baru_membuat_snap_dengan_kanal_terpilih(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();

            Http::fake(function (Request $request) {
                if ($request->method() === 'POST' && str_ends_with($request->url(), '/snap/v1/transactions')) {
                    return $this->snapResponse('snap-new-qris');
                }

                return Http::response(['status_message' => 'Unexpected request'], 500);
            });

            $response = $this->actingAs($parent)->withoutMiddleware()->post(
                route('wali-siswa.tagihan.bulk-pay', $siswa->id),
                [
                    'items' => [['tagihan_id' => $tagihan->id, 'jumlah_bayar' => 50000]],
                    'total_bayar' => 50000,
                    'metode_pembayaran' => 'midtrans',
                    'payment_method' => 'QRIS',
                ],
            );

            $payment = Pembayaran::query()->where('tagihan_id', $tagihan->id)->sole();

            $response->assertRedirect(route('wali-siswa.pembayaran.digital', $payment));
            $this->assertSame('midtrans', $payment->payment_gateway);
            $this->assertSame('midtrans', $payment->metode_pembayaran);
            $this->assertSame('QRIS', $payment->payment_type);
            $this->assertSame('pending', $payment->status_validasi);
            $this->assertSame('https://app.sandbox.midtrans.com/snap/v4/redirection/snap-new-qris', $payment->payment_url);
            $this->assertNotNull($payment->payment_expires_at);
            $this->assertSame(1, $payment->gateway_attempts);

            Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
                && $request['enabled_payments'] === ['qris']
                && data_get($request->data(), 'transaction_details.order_id') === $payment->order_id
                && data_get($request->data(), 'item_details.0.id') === 'TAG-'.$tagihan->id
                && data_get($request->data(), 'customer_details.first_name') === $parent->name
                && data_get($request->data(), 'customer_details.email') === null);
        });
    }

    public function test_pilihan_va_mengganti_snap_qris_yang_belum_dipakai(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $oldPayment = $this->makePendingPayment($parent, $siswa, $tagihan);

            Http::fake(function (Request $request) use ($oldPayment) {
                if ($request->method() === 'GET' && str_ends_with($request->url(), '/'.$oldPayment->order_id.'/status')) {
                    // Snap belum dipakai memilih kanal: Core API belum punya transaksi.
                    return Http::response(['status_code' => '404', 'status_message' => "Transaction doesn't exist."], 404);
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/snap/v1/transactions')) {
                    return $this->snapResponse('snap-new-va');
                }

                return Http::response(['status_message' => 'Unexpected request'], 500);
            });

            $response = $this->actingAs($parent)->withoutMiddleware()->post(
                route('wali-siswa.tagihan.bulk-pay', $siswa->id),
                [
                    'items' => [['tagihan_id' => $tagihan->id, 'jumlah_bayar' => 50000]],
                    'total_bayar' => 50000,
                    'metode_pembayaran' => 'midtrans',
                    'payment_method' => 'VA',
                ],
            );

            $newPayment = Pembayaran::query()
                ->where('tagihan_id', $tagihan->id)
                ->whereKeyNot($oldPayment->id)
                ->latest('id')
                ->firstOrFail();

            $response->assertRedirect(route('wali-siswa.pembayaran.digital', $newPayment));
            $this->assertSame('ditolak', $oldPayment->fresh()->status_validasi);
            $this->assertSame('cancel', $oldPayment->fresh()->gateway_status);
            $this->assertSame('VA', $newPayment->payment_type);
            $this->assertNotSame($oldPayment->order_id, $newPayment->order_id);
            $this->assertStringContainsString('snap-new-va', (string) $newPayment->payment_url);

            Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
                && str_ends_with($request->url(), '/snap/v1/transactions')
                && in_array('bca_va', $request['enabled_payments'], true));
        });
    }

    public function test_kode_bayar_aktif_di_midtrans_ditutup_dengan_expire_sebelum_kanal_baru(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $oldPayment = $this->makePendingPayment($parent, $siswa, $tagihan, ['payment_type' => 'BCAVA']);

            Http::fake(function (Request $request) use ($oldPayment) {
                if ($request->method() === 'GET' && str_ends_with($request->url(), '/'.$oldPayment->order_id.'/status')) {
                    return Http::response($this->coreStatus((string) $oldPayment->order_id, 'pending', 'bank_transfer'));
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/'.$oldPayment->order_id.'/expire')) {
                    return Http::response($this->coreStatus((string) $oldPayment->order_id, 'expire', 'bank_transfer', '407'));
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/snap/v1/transactions')) {
                    return $this->snapResponse('snap-after-expire');
                }

                return Http::response(['status_message' => 'Unexpected request'], 500);
            });

            $response = $this->actingAs($parent)->withoutMiddleware()->post(
                route('wali-siswa.pembayaran.change-method', $oldPayment),
                ['payment_method' => 'QRIS'],
            );

            $newPayment = Pembayaran::query()
                ->whereKeyNot($oldPayment->id)
                ->where('tagihan_id', $tagihan->id)
                ->latest('id')
                ->firstOrFail();

            $response->assertRedirect(route('wali-siswa.pembayaran.digital', $newPayment));
            $this->assertSame('ditolak', $oldPayment->fresh()->status_validasi);
            $this->assertSame('expire', $oldPayment->fresh()->gateway_status);
            $this->assertSame('QRIS', $newPayment->payment_type);
            Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
                && str_ends_with($request->url(), '/'.$oldPayment->order_id.'/expire'));
        });
    }

    public function test_gagal_menutup_remote_tidak_menolak_lokal_atau_membuat_transaksi_baru(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $oldPayment = $this->makePendingPayment($parent, $siswa, $tagihan);

            Http::fake(function (Request $request) use ($oldPayment) {
                if ($request->method() === 'GET' && str_ends_with($request->url(), '/'.$oldPayment->order_id.'/status')) {
                    return Http::response($this->coreStatus((string) $oldPayment->order_id, 'pending', 'qris'));
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/'.$oldPayment->order_id.'/expire')) {
                    return Http::response(['status_message' => 'Temporary gateway failure'], 503);
                }

                return Http::response(['status_message' => 'Unexpected request'], 500);
            });

            $response = $this->from(route('wali-siswa.tagihan.anak', $siswa->id))
                ->actingAs($parent)
                ->withoutMiddleware()
                ->post(route('wali-siswa.tagihan.bulk-pay', $siswa->id), [
                    'items' => [['tagihan_id' => $tagihan->id, 'jumlah_bayar' => 50000]],
                    'total_bayar' => 50000,
                    'metode_pembayaran' => 'midtrans',
                    'payment_method' => 'VA',
                ]);

            $response->assertRedirect(route('wali-siswa.tagihan.anak', $siswa->id));
            $response->assertSessionHas('error');
            $this->assertSame('pending', $oldPayment->fresh()->status_validasi);
            $this->assertSame(1, Pembayaran::query()->where('tagihan_id', $tagihan->id)->count());
            Http::assertNotSent(fn (Request $request): bool => str_ends_with($request->url(), '/snap/v1/transactions'));
        });
    }

    public function test_percobaan_gagal_dibuka_ulang_dengan_order_id_baru(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $payment = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'payment_url' => null,
                'payment_expires_at' => null,
                'gateway_status' => null,
                'gateway_attempts' => 1,
                'gateway_error' => 'Kanal pembayaran belum dapat dibuat.',
            ]);
            $oldOrderId = (string) $payment->order_id;

            Http::fake(function (Request $request) use ($oldOrderId) {
                if ($request->method() === 'GET' && str_ends_with($request->url(), '/'.$oldOrderId.'/status')) {
                    return Http::response(['status_code' => '404', 'status_message' => "Transaction doesn't exist."], 404);
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/snap/v1/transactions')) {
                    return $this->snapResponse('snap-retry');
                }

                return Http::response(['status_message' => 'Unexpected request'], 500);
            });

            $this->actingAs($parent)->withoutMiddleware()
                ->post(route('wali-siswa.pembayaran.continue', $payment))
                ->assertRedirect(route('wali-siswa.pembayaran.digital', $payment));

            $payment->refresh();
            $this->assertNotSame($oldOrderId, $payment->order_id);
            $this->assertStringContainsString('snap-retry', (string) $payment->payment_url);
            $this->assertNull($payment->gateway_error);
            Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/snap/v1/transactions')
                && data_get($request->data(), 'transaction_details.order_id') === $payment->order_id);
        });
    }

    public function test_snap_kedaluwarsa_tanpa_dipakai_ditutup_lokal(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $payment = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'payment_expires_at' => now()->subHour(),
            ]);
            $fresh = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'order_id' => 'SPH-FRESH-'.strtoupper(substr(md5(uniqid('', true)), 0, 8)),
                'kode_pembayaran' => 'PAY-FRESH-'.uniqid(),
                'payment_expires_at' => now()->addHour(),
            ]);

            Http::fake(['*' => Http::response(['status_code' => '404', 'status_message' => "Transaction doesn't exist."], 404)]);

            $service = app(MidtransPaymentStatusService::class);
            $this->assertTrue($service->sync((string) $payment->order_id));
            $this->assertFalse($service->sync((string) $fresh->order_id));

            $this->assertSame('ditolak', $payment->fresh()->status_validasi);
            $this->assertSame('expire', $payment->fresh()->gateway_status);
            $this->assertSame('Kedaluwarsa', $payment->fresh()->payment_status_label);
            $this->assertSame('pending', $fresh->fresh()->status_validasi);
        });
    }

    public function test_status_remote_memperbaiki_penolakan_lokal_yang_keliru(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $payment = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'status_validasi' => 'ditolak',
                'gateway_status' => 'cancel',
                'tanggal_validasi' => now(),
            ]);

            Http::fake(['*' => Http::response($this->coreStatus((string) $payment->order_id, 'pending', 'qris'))]);

            $this->assertTrue(app(MidtransPaymentStatusService::class)->sync((string) $payment->order_id));
            $this->assertSame('pending', $payment->fresh()->status_validasi);
            $this->assertSame('pending', $payment->fresh()->gateway_status);
            $this->assertNull($payment->fresh()->tanggal_validasi);
        });
    }

    public function test_konfirmasi_bayar_terlambat_tetap_melunasi_dan_tidak_bisa_diturunkan(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $payment = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'status_validasi' => 'ditolak',
                'gateway_status' => 'cancel',
                'tanggal_validasi' => now(),
            ]);

            Http::fakeSequence()
                ->push($this->coreStatus((string) $payment->order_id, 'settlement', 'gopay'))
                ->push($this->coreStatus((string) $payment->order_id, 'expire', 'gopay', '407'));

            $service = app(MidtransPaymentStatusService::class);
            $this->assertTrue($service->sync((string) $payment->order_id));
            $this->assertSame('disetujui', $payment->fresh()->status_validasi);
            $this->assertSame('settlement', $payment->fresh()->gateway_status);
            $this->assertSame('GOPAY', $payment->fresh()->payment_type);
            $this->assertSame('sudah_bayar', $tagihan->fresh()->status);

            // Notifikasi/status terlambat tidak boleh menurunkan status lunas.
            $this->assertFalse($service->sync((string) $payment->order_id));
            $this->assertSame('disetujui', $payment->fresh()->status_validasi);
            $this->assertSame('settlement', $payment->fresh()->gateway_status);
        });
    }

    public function test_halaman_pembayaran_wali_admin_dan_bendahara_tampil_dengan_kanal_midtrans(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $payment = $this->makePendingPayment($parent, $siswa, $tagihan, ['payment_type' => 'GOPAY']);
            $legacy = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'kode_pembayaran' => 'PAY-LEGACY-'.uniqid(),
                'order_id' => 'SPH-LEGACY-'.uniqid(),
                'metode_pembayaran' => 'paywuz',
                'payment_gateway' => 'paywuz',
                'status_validasi' => 'disetujui',
                'gateway_status' => 'success',
            ]);

            Http::fake(['*' => Http::response(['status_code' => '404', 'status_message' => "Transaction doesn't exist."], 404)]);

            $this->actingAs($parent)->get(route('wali-siswa.tagihan.anak', $siswa->id))
                ->assertOk()
                ->assertSee('value="midtrans"', false)
                ->assertSee('GoPay')
                ->assertSee('Virtual Account (Pilih Bank)')
                ->assertSee('Tanpa biaya tambahan')
                ->assertSee('Lanjut Bayar');

            $this->actingAs($parent)->get(route('wali-siswa.pembayaran.digital', $payment))
                ->assertOk()
                ->assertSee('Selesaikan Pembayaran')
                ->assertSee('https://app.sandbox.midtrans.com/snap/v4/redirection/old-token', false)
                ->assertSee('GoPay');

            $admin = User::query()->where('role', 'admin')->firstOrFail();
            $this->actingAs($admin)->get(route('admin.keuangan.info-pembayaran.index'))
                ->assertOk()
                ->assertSee('Payment Gateway Midtrans')
                ->assertSee(route('midtrans.webhook'), false)
                ->assertSee('name="midtrans_sandbox_server_key"', false)
                ->assertDontSee('paywuz', false);

            $bendahara = User::query()->where('role', 'bendahara')->firstOrFail();
            $this->actingAs($bendahara)->get(route('bendahara.pembayaran.index', ['metode' => 'midtrans']))
                ->assertOk()
                ->assertSee($payment->kode_pembayaran)
                ->assertSee($legacy->kode_pembayaran);
            $this->actingAs($bendahara)->get(route('bendahara.pembayaran.show', $payment))->assertOk();
            $this->actingAs($bendahara)->get(route('bendahara.laporan.index', ['metode' => 'midtrans']))->assertOk();
            $this->actingAs($admin)->get(route('admin.keuangan.laporan.index'))->assertOk();
        });
    }

    public function test_admin_menyimpan_server_key_midtrans_terenkripsi_dan_validasi_format(): void
    {
        $this->withMysql(function (): void {
            $admin = User::query()->where('role', 'admin')->firstOrFail();

            $this->actingAs($admin)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                ->post(route('admin.keuangan.info-pembayaran.update'), [
                    'type' => 'midtrans',
                    'midtrans_sandbox_server_key' => 'pk_sand_bukanMidtrans',
                    'midtrans_environment' => 'sandbox',
                ])
                ->assertSessionHasErrors('midtrans_sandbox_server_key');

            $this->actingAs($admin)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                ->post(route('admin.keuangan.info-pembayaran.update'), [
                    'type' => 'midtrans',
                    'midtrans_sandbox_server_key' => 'SB-Mid-server-adminSaveTest123',
                    'midtrans_environment' => 'sandbox',
                ])
                ->assertSessionHasNoErrors();

            $info = InfoPembayaran::getInstance()->fresh();
            $this->assertNotSame('SB-Mid-server-adminSaveTest123', $info->midtrans_sandbox_server_key);
            $this->assertSame('SB-Mid-server-adminSaveTest123', $info->getMidtransServerKey('sandbox'));
            $this->assertTrue($info->isMidtransEnabled());

            $this->actingAs($admin)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                ->post(route('admin.keuangan.info-pembayaran.update'), ['type' => 'midtrans_toggle'])
                ->assertSessionHasNoErrors();
            $this->assertFalse(InfoPembayaran::getInstance()->fresh()->isMidtransEnabled());
        });
    }

    public function test_rekonsiliasi_terjadwal_hanya_menyinkronkan_order_pending(): void
    {
        $this->withMysql(function (): void {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $this->enableMidtrans();
            $pending = $this->makePendingPayment($parent, $siswa, $tagihan);
            $final = $this->makePendingPayment($parent, $siswa, $tagihan, [
                'kode_pembayaran' => 'PAY-FINAL-'.uniqid(),
                'order_id' => 'SPH-FINAL-'.uniqid(),
                'status_validasi' => 'ditolak',
                'gateway_status' => 'expire',
            ]);

            Http::fake(fn (Request $request) => Http::response(
                $this->coreStatus((string) $pending->order_id, 'settlement', 'qris'),
            ));

            $this->artisan('midtrans:reconcile', ['--order' => null])->assertSuccessful();

            $this->assertSame('disetujui', $pending->fresh()->status_validasi);
            $this->assertSame('ditolak', $final->fresh()->status_validasi);
            Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), (string) $final->order_id));
        });
    }

    private function withMysql(callable $callback): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
        Cache::flush();

        DB::connection('mysql')->beginTransaction();

        try {
            $callback();
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function enableMidtrans(): void
    {
        config(['services.midtrans.channels' => ['QRIS', 'GOPAY', 'SHOPEEPAY', 'VA']]);

        InfoPembayaran::getInstance()->update([
            'midtrans_sandbox_server_key' => Crypt::encryptString('SB-Mid-server-flowTestKey12345'),
            'midtrans_is_production' => false,
            'midtrans_enabled' => true,
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function makePendingPayment(User $parent, Siswa $siswa, Tagihan $tagihan, array $overrides = []): Pembayaran
    {
        return Pembayaran::create(array_replace([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $siswa->id,
            'paid_by_parent_id' => $parent->id,
            'kode_pembayaran' => 'PAY-OLD-'.uniqid(),
            'jumlah_bayar' => 50000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'payment_type' => 'QRIS',
            'payment_environment' => 'sandbox',
            'order_id' => 'SPH-OLD-'.strtoupper(substr(md5(uniqid('', true)), 0, 8)),
            'payment_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/old-token',
            'gateway_status' => 'pending',
            'payment_expires_at' => now()->addHour(),
            'status_validasi' => 'pending',
        ], $overrides));
    }

    private function snapResponse(string $token): mixed
    {
        return Http::response([
            'token' => $token,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/'.$token,
        ], 201);
    }

    /** @return array<string, mixed> */
    private function coreStatus(string $orderId, string $status, string $paymentType, string $statusCode = '201'): array
    {
        return array_filter([
            'status_code' => in_array($status, ['settlement', 'capture'], true) ? '200' : $statusCode,
            'transaction_id' => 'trx-'.md5($orderId),
            'order_id' => $orderId,
            'gross_amount' => '50000.00',
            'payment_type' => $paymentType,
            'va_numbers' => $paymentType === 'bank_transfer' ? [['bank' => 'bca', 'va_number' => '12345']] : null,
            'transaction_status' => $status,
            'fraud_status' => 'accept',
            'expiry_time' => now('Asia/Jakarta')->addHour()->format('Y-m-d H:i:s'),
        ]);
    }

    /** @return array{0: User, 1: Siswa, 2: Tagihan} */
    private function makeBillingFixture(): array
    {
        $suffix = substr(md5(uniqid('', true)), 0, 8);
        $cabang = Cabang::query()->firstOrFail();
        $tahunAjaran = TahunAjaran::query()->where('is_active', true)->firstOrFail();

        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama_kelas' => 'Kelas '.$suffix,
            'jenjang' => 'SMP',
            'kode_kelas' => 'K'.$suffix,
            'kuota_siswa' => 30,
        ]);
        $parent = User::create([
            'name' => 'Wali '.$suffix,
            'email' => 'wali.'.$suffix.'@test.local',
            'role' => 'orang_tua',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $studentUser = User::create([
            'name' => 'Siswa '.$suffix,
            'email' => 'siswa.'.$suffix.'@test.local',
            'role' => 'siswa',
            'password' => bcrypt('password'),
        ]);
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => 'N'.$suffix,
            'nama_lengkap' => 'Siswa '.$suffix,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => '-',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => '-',
            'tanggal_masuk' => now(),
            'status' => 'aktif',
        ]);

        DB::table('student_parents')->insert([
            'parent_id' => $parent->id,
            'siswa_id' => $siswa->id,
            'relationship' => 'ayah_kandung',
            'is_primary' => true,
            'is_financial_responsible' => true,
            'can_access_academic' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis_tagihan' => 'uang_pendaftaran',
            'keterangan' => 'Tagihan uji Midtrans',
            'jumlah' => 50000,
            'tanggal_jatuh_tempo' => now()->addDays(7),
            'status' => 'belum_bayar',
        ]);

        return [$parent, $siswa, $tagihan];
    }
}
