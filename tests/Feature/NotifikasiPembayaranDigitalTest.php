<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi notifikasi pembayaran digital yang dipicu status terverifikasi.
 *
 * Menjalankan: php artisan test --filter=NotifikasiPembayaranDigitalTest
 */
class NotifikasiPembayaranDigitalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
    }

    public function test_notifikasi_terkirim_ke_admin_bendahara_dan_wali(): void
    {
        DB::connection('mysql')->beginTransaction();
        try {
            [$siswa, $pembayaran] = $this->buatPembayaranUji();

            $sebelum = Notification::count();
            app(NotificationService::class)->notifyPembayaranDigitalBerhasil(collect([$pembayaran]));
            $baru = Notification::latest('id')->take(Notification::count() - $sebelum)->get();

            $this->assertGreaterThan(0, $baru->count(), 'Notifikasi harus dibuat');

            $peranPenerima = User::whereIn('id', $baru->pluck('user_id'))->pluck('role')->unique();

            $this->assertTrue($peranPenerima->contains('admin'), 'Admin harus menerima notifikasi');
            $this->assertTrue($peranPenerima->contains('bendahara'), 'Bendahara harus menerima notifikasi');
            $this->assertTrue(
                $peranPenerima->contains('orang_tua'),
                'Wali siswa harus menerima notifikasi'
            );
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_status_service_mengirim_notifikasi_pembayaran_digital(): void
    {
        $isi = file_get_contents(app_path('Services/PaywuzPaymentStatusService.php'));

        $this->assertStringContainsString(
            'notifyPembayaranDigitalBerhasil',
            $isi,
            'Status settlement/success harus mengirim notifikasi pembayaran digital.'
        );
    }

    public function test_webhook_memeriksa_status_per_pembayaran(): void
    {
        $isi = file_get_contents(app_path('Services/PaywuzPaymentStatusService.php'));

        $this->assertStringNotContainsString(
            'if ($oldStatus !== $newStatus) {',
            $isi,
            'Status grup tidak boleh diputuskan berdasarkan pembayaran pertama saja.'
        );

        $this->assertStringContainsString(
            '$baruDisetujui',
            $isi,
            'Webhook harus melacak pembayaran yang BARU disetujui agar notifikasi '
            .'tidak terkirim ganda saat webhook dikirim ulang.'
        );
    }

    public function test_audit_log_mencatat_status_lama_yang_benar(): void
    {
        // getOriginal() SESUDAH update() sudah berisi nilai baru (Laravel
        // menyinkronkan original setiap kali save berhasil), sehingga audit
        // log dulu mencatat old == new dan jejaknya jadi tidak berguna.
        $isi = file_get_contents(app_path('Services/PaywuzPaymentStatusService.php'));

        $this->assertStringNotContainsString(
            "getOriginal('status_validasi')",
            $isi,
            'Nilai lama harus disimpan SEBELUM update(), bukan lewat getOriginal() sesudahnya.'
        );
    }

    /** @return array{0: Siswa, 1: Pembayaran} */
    private function buatPembayaranUji(): array
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $this->assertNotNull($ta);

        // Siswa yang punya wali terhubung, supaya jalur notifikasi wali teruji.
        $siswa = Siswa::whereHas('orangTua')->first();
        $this->assertNotNull($siswa, 'Butuh minimal satu siswa yang punya wali');

        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
            'jenis_tagihan' => 'spp_juli',
            'keterangan' => 'Tagihan uji notifikasi',
            'jumlah' => 500000,
            'tanggal_jatuh_tempo' => now()->addDays(7),
            'status' => 'belum_bayar',
        ]);

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $siswa->id,
            'kode_pembayaran' => 'UJI-'.uniqid(),
            'jumlah_bayar' => 500000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'transfer',
            'payment_gateway' => 'paywuz',
            'order_id' => 'ORDER-UJI-'.uniqid(),
            'payment_type' => 'qris',
            'status_validasi' => 'disetujui',
        ]);

        return [$siswa, $pembayaran];
    }
}
