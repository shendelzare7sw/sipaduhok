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
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaywuzSelectedChannelFlowTest extends TestCase
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
        $this->assertStringContainsString("\$gatewayStatus === 'failed'", $view);
        $this->assertStringContainsString("\$gatewayStatus === 'expired'", $view);
        $this->assertStringContainsString("\$gatewayStatus === 'cancelled'", $view);
    }

    public function test_pilihan_va_dari_form_mengganti_transaksi_qris_pending(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
            'services.paywuz.base_url' => 'https://api.paywuz.id/v1',
        ]);
        DB::purge('mysql');
        Cache::flush();

        DB::connection('mysql')->beginTransaction();

        try {
            [$parent, $siswa, $tagihan] = $this->makeBillingFixture();
            $apiKey = 'pk_sand_'.str_repeat('a', 32);

            InfoPembayaran::getInstance()->update([
                'paywuz_sandbox_api_key' => Crypt::encryptString($apiKey),
                'paywuz_is_production' => false,
                'paywuz_enabled' => true,
                'paywuz_fee_by_merchant' => false,
            ]);

            $oldPayment = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'siswa_id' => $siswa->id,
                'paid_by_parent_id' => $parent->id,
                'kode_pembayaran' => 'PAY-OLD-QRIS',
                'jumlah_bayar' => 50000,
                'tanggal_bayar' => now(),
                'metode_pembayaran' => 'paywuz',
                'payment_gateway' => 'paywuz',
                'payment_type' => 'QRIS',
                'payment_environment' => 'sandbox',
                'order_id' => 'SPH-OLD-QRIS',
                'transaction_id' => 'trx-old-qris',
                'payment_url' => 'https://paywuz.id/pay/trx-old-qris',
                'gateway_status' => 'pending',
                'payment_expires_at' => now()->addHour(),
                'status_validasi' => 'pending',
            ]);

            Http::fake(function (Request $request) use ($oldPayment) {
                if ($request->method() === 'GET' && str_ends_with($request->url(), '/payment-methods')) {
                    return Http::response(['data' => [
                        [
                            'code' => 'QRIS',
                            'name' => 'QRIS',
                            'type' => 'qris',
                            'limits' => ['minIdr' => 1000, 'maxIdr' => 10000000],
                        ],
                        [
                            'code' => 'VA',
                            'name' => 'Virtual Account (Pilih Bank)',
                            'type' => 'meta',
                            'limits' => ['minIdr' => 10000, 'maxIdr' => 50000000],
                        ],
                    ]]);
                }

                if ($request->method() === 'GET' && str_ends_with($request->url(), '/transactions/SPH-OLD-QRIS')) {
                    return Http::response(['data' => $this->transactionData(
                        'trx-old-qris',
                        'SPH-OLD-QRIS',
                        'QRIS',
                        'pending',
                        $oldPayment->payment_url,
                    )]);
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/transactions/SPH-OLD-QRIS/cancel')) {
                    return Http::response(['data' => $this->transactionData(
                        'trx-old-qris',
                        'SPH-OLD-QRIS',
                        'QRIS',
                        'cancelled',
                        $oldPayment->payment_url,
                    )]);
                }

                if ($request->method() === 'POST' && str_ends_with($request->url(), '/transactions')) {
                    return Http::response(['data' => $this->transactionData(
                        'trx-new-va',
                        (string) $request['orderId'],
                        (string) $request['paymentMethod'],
                        'pending',
                        'https://paywuz.id/pay/trx-new-va',
                    )]);
                }

                return Http::response(['message' => 'Unexpected request'], 500);
            });

            $response = $this->actingAs($parent)->withoutMiddleware()->post(
                route('wali-siswa.tagihan.bulk-pay', $siswa->id),
                [
                    'items' => [[
                        'tagihan_id' => $tagihan->id,
                        'jumlah_bayar' => 50000,
                    ]],
                    'total_bayar' => 50000,
                    'metode_pembayaran' => 'paywuz',
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
            $this->assertSame('VA', $newPayment->payment_type);
            $this->assertSame('trx-new-va', $newPayment->transaction_id);

            Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
                && str_ends_with($request->url(), '/transactions')
                && $request['paymentMethod'] === 'VA');
        } finally {
            DB::connection('mysql')->rollBack();
        }
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
            'keterangan' => 'Tagihan uji pilihan VA',
            'jumlah' => 50000,
            'tanggal_jatuh_tempo' => now()->addDays(7),
            'status' => 'belum_bayar',
        ]);

        return [$parent, $siswa, $tagihan];
    }

    /** @return array<string, mixed> */
    private function transactionData(
        string $id,
        string $orderId,
        string $paymentMethod,
        string $status,
        string $paymentUrl,
    ): array {
        return [
            'id' => $id,
            'orderId' => $orderId,
            'amount' => 50000,
            'totalPayment' => 50360,
            'paymentMethod' => $paymentMethod,
            'status' => $status,
            'paymentUrl' => $paymentUrl,
            'expiresAt' => now()->addHour()->toIso8601String(),
        ];
    }
}
