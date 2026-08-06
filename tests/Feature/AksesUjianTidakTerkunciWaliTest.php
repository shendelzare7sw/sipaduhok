<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\User;
use App\Services\ValidasiAksesService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi: siswa yang sudah lolos syarat keuangan TIDAK boleh terkunci lagi
 * oleh flag `validasi_ujian_wali`.
 *
 * Halaman Wali Kelas /wali/validasi-akses sudah dijadikan read-only ("validasi
 * akses dilakukan otomatis berdasarkan status keuangan") sehingga tidak ada
 * lagi tombol untuk menyalakan flag tersebut. Tapi gerbang ujian di sisi siswa
 * masih mensyaratkannya, sehingga SELURUH siswa terkunci: sudah lunas, di layar
 * wali tertulis "Akses Terbuka", namun tetap ditolak saat membuka ujian - dan
 * tidak ada jalan apa pun di UI untuk membukanya.
 *
 * Menjalankan: php artisan test --filter=AksesUjianTidakTerkunciWaliTest
 */
class AksesUjianTidakTerkunciWaliTest extends TestCase
{
    public function test_siswa_lunas_bisa_akses_ujian_walau_flag_wali_mati(): void
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

        DB::connection('mysql')->beginTransaction();
        try {
            $suffix = substr(md5(uniqid('', true)), 0, 8);

            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta);

            $kelas = new Kelas();
            $kelas->cabang_id = $cabang->id;
            $kelas->tahun_ajaran_id = $ta->id;
            $kelas->nama_kelas = 'Kelas ' . $suffix;
            $kelas->jenjang = 'SMA';
            $kelas->kode_kelas = 'K' . $suffix;
            $kelas->kuota_siswa = 30;
            $kelas->save();

            $mapel = new MataPelajaran();
            $mapel->kode_mapel = 'M' . $suffix;
            $mapel->nama_mapel = 'Mapel ' . $suffix;
            $mapel->jenjang = 'SMA';
            $mapel->save();

            $siswaUser = new User();
            $siswaUser->name = 'Siswa ' . $suffix;
            $siswaUser->email = "siswa.$suffix@test.local";
            $siswaUser->role = 'siswa';
            $siswaUser->password = bcrypt('password');
            $siswaUser->save();

            $siswa = new Siswa();
            $siswa->user_id = $siswaUser->id;
            $siswa->cabang_id = $cabang->id;
            $siswa->kelas_id = $kelas->id;
            $siswa->nis = 'NIS' . $suffix;
            $siswa->nisn = 'NISN' . $suffix;
            $siswa->nama_lengkap = 'Siswa ' . $suffix;
            $siswa->jenis_kelamin = 'L';
            $siswa->tempat_lahir = 'Kota Uji';
            $siswa->tanggal_lahir = '2010-01-01';
            $siswa->alamat = 'Alamat uji';
            $siswa->tanggal_masuk = now()->toDateString();
            $siswa->status = 'aktif';
            // Inilah kondisi yang dulu mengunci: wali BELUM menyalakan flag.
            $siswa->validasi_ujian_wali = false;
            $siswa->validasi_ujian_bendahara = false;
            $siswa->save();

            $guru = \App\Models\TenagaPendidik::first();
            $this->assertNotNull($guru);

            // Ujian jenis PTS -> termasuk yang butuh validasi akses.
            $ujian = new Ujian();
            $ujian->kelas_id = $kelas->id;
            $ujian->mata_pelajaran_id = $mapel->id;
            $ujian->guru_id = $guru->id;
            $ujian->judul_ujian = 'PTS ' . $suffix;
            $ujian->tipe_ujian = Ujian::TIPE_PTS_GANJIL;
            $ujian->tanggal_mulai = now()->subMinutes(5);
            $ujian->tanggal_selesai = now()->addHour();
            $ujian->durasi_menit = 60;
            $ujian->is_active = true;
            $ujian->save();

            $this->assertTrue($ujian->requiresValidation(), 'PTS harus termasuk ujian yang divalidasi');

            $svc = app(ValidasiAksesService::class);
            $this->assertTrue(
                $svc->cekAksesUjian($siswa),
                'Tanpa aturan batas pembayaran, siswa dianggap lunas sehingga berhak ujian'
            );
            $this->assertFalse((bool) $siswa->validasi_ujian_wali, 'Prasyarat test: flag wali memang mati');

            // Aksi: siswa membuka halaman ujian.
            $this->actingAs($siswaUser)->withoutMiddleware();
            $response = $this->get(route('siswa.lms.mapel.ujian.show', [$mapel->id, $ujian->id]));

            // Harus TIDAK dilempar balik dengan pesan "Belum Memiliki Akses".
            $response->assertSessionMissing('error');
            $this->assertNotEquals(
                route('siswa.lms.mapel.show', $mapel->id),
                $response->headers->get('Location'),
                'Siswa lunas tidak boleh dipentalkan hanya karena flag validasi_ujian_wali mati'
            );
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_gerbang_ujian_tidak_lagi_bergantung_pada_flag_wali(): void
    {
        // Penjaga struktural: kalau suatu saat syarat `validasi_ujian_wali`
        // dipasang lagi di gerbang ujian sementara halaman wali masih read-only,
        // seluruh siswa akan terkunci lagi tanpa jalan keluar di UI.
        $isi = file_get_contents(app_path('Http/Controllers/Siswa/LmsUjianController.php'));

        // Buang komentar supaya penyebutan di dokumentasi kode tidak ikut terhitung.
        $tanpaKomentar = preg_replace('!//.*!', '', $isi);

        $this->assertSame(
            0,
            substr_count($tanpaKomentar, 'validasi_ujian_wali'),
            'Gerbang ujian tidak boleh memakai validasi_ujian_wali - halaman Wali Kelas read-only '
            . 'dan tidak punya cara menyalakan flag itu, jadi semua siswa akan terkunci.'
        );
    }

    public function test_halaman_wali_dan_gerbang_siswa_memakai_sumber_yang_sama(): void
    {
        // Badge di halaman wali dulu memakai `validasi_ujian_bendahara` mentah,
        // sedangkan siswa dinilai lewat cekAksesUjian(). Keduanya bisa berbeda,
        // sehingga layar wali menampilkan status yang tidak dialami siswa.
        $isi = file_get_contents(app_path('Http/Controllers/WaliKelas/ValidasiAksesController.php'));

        $this->assertStringContainsString(
            'cekAksesUjian',
            $isi,
            'Halaman Wali Kelas harus menghitung status akses dengan cekAksesUjian(), '
            . 'sumber yang sama dengan gerbang ujian siswa.'
        );
    }
}
