<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\FinancialAuditLog;
use App\Models\GuruPengajarKelas;
use App\Models\InfoPembayaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Rapor;
use App\Models\RequestDownloadRapor;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\TugasSiswa;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use App\Services\NotificationService;
use App\Services\TunggakanCarryoverService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class SITClosureDomainIntegrationTest extends TestCase
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
        DB::connection('mysql')->beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::connection('mysql')->rollBack();
        parent::tearDown();
    }

    public function test_observer_submission_tugas_dan_ujian_menyinkronkan_rekap_nilai(): void
    {
        $context = $this->makeAcademicContext();

        $tugas = Tugas::create([
            'kelas_id' => $context['kelas']->id,
            'mata_pelajaran_id' => $context['mapel']->id,
            'guru_id' => $context['guru']->id,
            'jenis_tugas' => 'tugas',
            'urutan' => 1,
            'judul_tugas' => 'Tugas SIT Closure',
            'deskripsi' => 'Verifikasi observer tugas ke nilai.',
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_deadline' => now()->addDay()->toDateString(),
        ]);

        $submission = TugasSiswa::create([
            'tugas_id' => $tugas->id,
            'siswa_id' => $context['siswa']->id,
            'jawaban_text' => 'Jawaban teknis',
            'status' => 'dikerjakan',
        ]);
        $submission->update(['nilai' => 84.5, 'status' => 'dinilai']);

        $nilai = Nilai::where('siswa_id', $context['siswa']->id)
            ->where('mata_pelajaran_id', $context['mapel']->id)
            ->where('kelas_id', $context['kelas']->id)
            ->firstOrFail();
        $this->assertSame('84.50', $nilai->tugas_1);
        $this->assertSame('84.50', $nilai->tugas_1_guru);

        $ujian = new Ujian([
            'kelas_id' => $context['kelas']->id,
            'mata_pelajaran_id' => $context['mapel']->id,
            'guru_id' => $context['guru']->id,
            'judul_ujian' => 'Ulangan SIT Closure',
            'tipe_ujian' => Ujian::TIPE_ULANGAN_HARIAN,
            'tanggal_mulai' => now()->subHour(),
            'tanggal_selesai' => now()->addHour(),
            'durasi_menit' => 60,
            'is_active' => true,
        ]);
        $ujian->urutan = 1;
        $ujian->save();

        $hasil = UjianSiswa::create([
            'ujian_id' => $ujian->id,
            'siswa_id' => $context['siswa']->id,
            'waktu_mulai' => now()->subMinutes(30),
            'status' => 'sedang_mengerjakan',
        ]);
        $hasil->update([
            'nilai' => 91,
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        $nilai->refresh();
        $this->assertSame('91.00', $nilai->uh_1);
        $this->assertSame('91.00', $nilai->uh_1_guru);
        $this->assertNotNull($nilai->guru_terakhir_simpan_at);
    }

    public function test_carryover_menghitung_sisa_membuat_link_asal_tujuan_dan_idempotent(): void
    {
        $context = $this->makeAcademicContext();
        $tahunLama = TahunAjaran::where('id', '!=', $context['tahun']->id)->firstOrFail();
        $eksekutor = $this->makeUser('bendahara');

        $tagihanAsal = Tagihan::create([
            'siswa_id' => $context['siswa']->id,
            'tahun_ajaran_id' => $tahunLama->id,
            'jenis_tagihan' => 'spp_januari',
            'keterangan' => 'SPP lama SIT',
            'jumlah' => 1000000,
            'tanggal_jatuh_tempo' => now()->subMonth()->toDateString(),
            'status' => 'cicilan',
        ]);
        Pembayaran::create([
            'tagihan_id' => $tagihanAsal->id,
            'siswa_id' => $context['siswa']->id,
            'kode_pembayaran' => 'SIT-CARRY-PAY-'.uniqid(),
            'jumlah_bayar' => 250000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'tunai',
            'status_validasi' => 'disetujui',
        ]);

        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('notifyTunggakanDialihkan')->once();
        $service = new TunggakanCarryoverService($notifications);

        $preview = $service->previewCarryover([$context['siswa']->id], $context['tahun']->id);
        $this->assertSame(1, $preview['total_tagihan']);
        $this->assertSame(750000.0, $preview['grand_total']);

        $result = $service->executeCarryover([$context['siswa']->id], $context['tahun']->id, $eksekutor);
        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['created']);

        $tagihanAsal->refresh();
        $tagihanBaru = Tagihan::findOrFail($tagihanAsal->dialihkan_ke_id);
        $this->assertSame($tagihanAsal->id, $tagihanBaru->tagihan_asal_id);
        $this->assertSame($context['tahun']->id, $tagihanBaru->tahun_ajaran_id);
        $this->assertSame('750000.00', $tagihanBaru->jumlah);
        $this->assertNotNull($tagihanAsal->dialihkan_pada);

        $second = $service->executeCarryover([$context['siswa']->id], $context['tahun']->id, $eksekutor);
        $this->assertFalse($second['success']);
        $this->assertSame(0, $second['created']);
        $this->assertSame(1, Tagihan::where('tagihan_asal_id', $tagihanAsal->id)->count());
    }

    public function test_webhook_berulang_idempotent_membatalkan_pending_duplikat_dan_mencatat_audit(): void
    {
        $context = $this->makeAcademicContext();
        $serverKey = 'sit-server-key-'.uniqid();
        $orderId = 'SIT-ORDER-'.uniqid();
        $grossAmount = '1000000.00';

        $info = InfoPembayaran::getInstance();
        $info->update([
            'midtrans_merchant_id' => 'SIT-MERCHANT',
            'midtrans_server_key' => Crypt::encryptString($serverKey),
            'midtrans_client_key' => 'SIT-CLIENT',
            'midtrans_enabled' => true,
            'midtrans_is_production' => false,
        ]);

        $tagihan = Tagihan::create([
            'siswa_id' => $context['siswa']->id,
            'tahun_ajaran_id' => $context['tahun']->id,
            'jenis_tagihan' => 'spp_agustus',
            'jumlah' => 1000000,
            'tanggal_jatuh_tempo' => now()->addMonth()->toDateString(),
            'status' => 'belum_bayar',
        ]);
        $successful = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $context['siswa']->id,
            'kode_pembayaran' => 'SIT-WEBHOOK-'.uniqid(),
            'jumlah_bayar' => 1000000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'order_id' => $orderId,
            'status_validasi' => 'pending',
        ]);
        $duplicate = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $context['siswa']->id,
            'kode_pembayaran' => 'SIT-DUPLICATE-'.uniqid(),
            'jumlah_bayar' => 1000000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'order_id' => 'SIT-DUP-ORDER-'.uniqid(),
            'status_validasi' => 'pending',
        ]);

        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('notifyPembayaranDigitalBerhasil')->once();
        $this->app->instance(NotificationService::class, $notifications);

        $payload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => hash('sha512', $orderId.'200'.$grossAmount.$serverKey),
            'transaction_id' => 'SIT-TRX-'.uniqid(),
            'payment_type' => 'bank_transfer',
        ];

        $this->postJson('/midtrans/notification', $payload)->assertOk();

        $successful->refresh();
        $duplicate->refresh();
        $tagihan->refresh();
        $this->assertSame('disetujui', $successful->status_validasi);
        $this->assertSame('ditolak', $duplicate->status_validasi);
        $this->assertSame('sudah_bayar', $tagihan->status);
        $auditCount = FinancialAuditLog::where('model_type', 'Pembayaran')
            ->where('model_id', $successful->id)
            ->count();
        $this->assertSame(2, $auditCount);

        $this->postJson('/midtrans/notification', $payload)->assertOk();
        $this->assertSame($auditCount, FinancialAuditLog::where('model_type', 'Pembayaran')
            ->where('model_id', $successful->id)
            ->count());
        $this->assertSame('disetujui', $successful->fresh()->status_validasi);
        $this->assertSame('ditolak', $duplicate->fresh()->status_validasi);
    }

    public function test_request_rapor_disetujui_menghasilkan_token_valid_yang_hanya_bisa_dipakai_pemilik(): void
    {
        $context = $this->makeAcademicContext();
        $parent = $this->makeUser('orang_tua');
        $context['siswa']->parents()->attach($parent->id, [
            'relationship' => 'ayah',
            'is_primary' => true,
            'is_financial_responsible' => true,
            'can_access_academic' => true,
        ]);

        $waliUser = $this->makeUser('wali_kelas');
        $wali = TenagaPendidik::create([
            'user_id' => $waliUser->id,
            'nama_lengkap' => 'Wali SIT Closure',
            'jenis_kelamin' => 'L',
        ]);
        WaliKelasAssignment::create([
            'tenaga_pendidik_id' => $wali->id,
            'kelas_id' => $context['kelas']->id,
            'assigned_at' => now(),
        ]);

        $rapor = Rapor::create([
            'siswa_id' => $context['siswa']->id,
            'kelas_id' => $context['kelas']->id,
            'tahun_ajaran_id' => $context['tahun']->id,
            'semester' => 'ganjil',
            'jenis_rapor' => 'akhir_semester',
            'status' => 'diterbitkan',
        ]);

        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('notifyRequestDownloadRapor')->once();
        $notifications->shouldReceive('notifyKeputusanDownloadRapor')->once();
        $this->app->instance(NotificationService::class, $notifications);

        $this->actingAs($parent)->withoutMiddleware()
            ->from('/wali-siswa/rapor/detail/'.$rapor->id)
            ->post(route('wali-siswa.rapor.request-download', $rapor->id), ['alasan' => 'Arsip keluarga'])
            ->assertRedirect('/wali-siswa/rapor/detail/'.$rapor->id);

        $downloadRequest = RequestDownloadRapor::where('rapor_id', $rapor->id)
            ->where('user_id', $parent->id)
            ->firstOrFail();
        $this->assertSame('menunggu', $downloadRequest->status);

        $this->actingAs($waliUser)->withoutMiddleware()
            ->from(route('wali.rapor.request-download.index'))
            ->post(route('wali.rapor.request-download.approve', $downloadRequest->id), [
                'catatan_admin' => 'Disetujui untuk arsip.',
            ])->assertRedirect(route('wali.rapor.request-download.index'));

        $downloadRequest->refresh();
        $this->assertSame('disetujui', $downloadRequest->status);
        $this->assertSame($waliUser->id, $downloadRequest->diputuskan_oleh);
        $this->assertNotNull($downloadRequest->download_token);
        $this->assertFalse($downloadRequest->isExpired());

        $this->actingAs($parent)->withoutMiddleware()
            ->get(route('wali-siswa.rapor.download', $downloadRequest->download_token))
            ->assertOk()
            ->assertViewIs('wali-kelas.rapor.print-pas');

        $otherParent = $this->makeUser('orang_tua');
        $this->actingAs($otherParent)->withoutMiddleware()
            ->get(route('wali-siswa.rapor.download', $downloadRequest->download_token))
            ->assertRedirect(route('wali-siswa.dashboard'));

        $this->actingAs($otherParent)->withoutMiddleware()
            ->get(route('wali-siswa.presensi.anak', $context['siswa']->id))
            ->assertRedirect(route('wali-siswa.dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke data siswa ini.');
    }

    private function makeAcademicContext(): array
    {
        $suffix = substr(md5(uniqid('', true)), 0, 8);
        $cabang = Cabang::firstOrFail();
        $tahun = TahunAjaran::where('is_active', true)->firstOrFail();
        $guru = TenagaPendidik::firstOrFail();

        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => 'SIT Domain '.$suffix,
            'jenjang' => 'SMP',
            'kode_kelas' => 'SD'.$suffix,
            'kuota_siswa' => 30,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'SM'.$suffix,
            'nama_mapel' => 'Mapel SIT '.$suffix,
            'jenjang' => 'SMP',
            'is_active' => true,
        ]);
        GuruPengajarKelas::create([
            'tenaga_pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
        ]);

        $studentUser = $this->makeUser('siswa');
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => 'SITN'.$suffix,
            'nis' => 'SITI'.$suffix,
            'nama_lengkap' => 'Siswa Domain '.$suffix,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => 'Alamat SIT',
            'agama' => 'Islam',
            'tanggal_masuk' => now()->toDateString(),
            'status' => 'aktif',
        ]);

        return compact('cabang', 'tahun', 'guru', 'kelas', 'mapel', 'siswa', 'studentUser');
    }

    private function makeUser(string $role): User
    {
        $suffix = substr(md5(uniqid('', true)), 0, 10);

        return User::create([
            'name' => 'SIT Domain '.$suffix,
            'email' => 'sit.domain.'.$suffix.'@test.local',
            'username' => 'sitdomain'.$suffix,
            'password' => bcrypt('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
