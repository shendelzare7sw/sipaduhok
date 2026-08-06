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
 * Regresi notifikasi pembayaran digital (Midtrans).
 *
 * Pembayaran lunas lewat Midtrans bisa diselesaikan oleh DUA jalur:
 *   1. webhook  (POST /midtrans/notification, bertanda tangan - otoritatif)
 *   2. snapFinish (redirect balik ke wali siswa, status diverifikasi ke API)
 *
 * Yang mana pun jalan lebih dulu, dialah yang mengubah status jadi "disetujui".
 * Dulu HANYA webhook yang mengirim notifikasi, dan webhook itu pun hanya
 * mengirim kalau status berubah. Jadi kalau snapFinish menang duluan (kasus
 * paling umum, karena wali langsung diarahkan balik setelah bayar), webhook
 * menyusul dengan status yang sudah sama -> dianggap "tidak ada perubahan" ->
 * notifikasi ke Admin/Bendahara/Wali tidak pernah terkirim sama sekali.
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

    public function test_jalur_snapfinish_juga_mengirim_notifikasi(): void
    {
        // Penjaga struktural: snapFinish menyetujui pembayaran (memanggil
        // updateStatusBayar dan menulis audit log), jadi ia WAJIB ikut
        // mengirim notifikasi. Kalau tidak, pembayaran yang diselesaikan
        // lewat jalur ini akan senyap total.
        $isi = file_get_contents(app_path('Http/Controllers/OrangTua/OrangTuaController.php'));

        $this->assertStringContainsString(
            'notifyPembayaranDigitalBerhasil',
            $isi,
            'snapFinish harus mengirim notifikasi pembayaran digital - jalur ini sering '
            . 'menyelesaikan transaksi lebih dulu daripada webhook.'
        );
    }

    public function test_webhook_memeriksa_status_per_pembayaran(): void
    {
        // Dulu webhook memutuskan berdasarkan status pembayaran PERTAMA saja.
        // Pada pembayaran borongan, kalau item pertama sudah disetujui lebih
        // dulu (mis. oleh snapFinish) sementara sisanya masih pending, seluruh
        // proses dilewati dan item sisanya tidak pernah ikut diperbarui.
        $isi = file_get_contents(app_path('Http/Controllers/MidtransWebhookController.php'));

        $this->assertStringNotContainsString(
            'if ($oldStatus !== $newStatus) {',
            $isi,
            'Webhook tidak boleh memutuskan berdasarkan status pembayaran pertama saja; '
            . 'periksa per pembayaran agar item lain di order yang sama tidak terlewat.'
        );

        $this->assertStringContainsString(
            '$baruDisetujui',
            $isi,
            'Webhook harus melacak pembayaran yang BARU disetujui agar notifikasi '
            . 'tidak terkirim ganda saat Midtrans mengirim ulang webhook.'
        );
    }

    public function test_audit_log_mencatat_status_lama_yang_benar(): void
    {
        // getOriginal() SESUDAH update() sudah berisi nilai baru (Laravel
        // menyinkronkan original setiap kali save berhasil), sehingga audit
        // log dulu mencatat old == new dan jejaknya jadi tidak berguna.
        $isi = file_get_contents(app_path('Http/Controllers/MidtransWebhookController.php'));

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
            'kode_pembayaran' => 'UJI-' . uniqid(),
            'jumlah_bayar' => 500000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'transfer',
            'payment_gateway' => 'midtrans',
            'order_id' => 'ORDER-UJI-' . uniqid(),
            'payment_type' => 'qris',
            'status_validasi' => 'disetujui',
        ]);

        return [$siswa, $pembayaran];
    }
}
