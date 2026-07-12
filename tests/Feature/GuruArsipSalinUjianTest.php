<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\SoalUjian;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Ujian;
use App\Models\User;
use App\Services\GuruLmsArsipService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi: salin ujian/latihan dari arsip ke kelas aktif.
 * Dulu gagal karena menyisipkan kolom 'acak_soal' yang tidak ada di tabel `ujian`
 * (SQLSTATE 42S22). Sekarang harus berhasil: ujian baru dibuat (is_active=false)
 * beserta seluruh soal (kunci ikut, tanpa jawaban siswa).
 */
class GuruArsipSalinUjianTest extends TestCase
{
    public function test_salin_ujian_berhasil_dan_menyalin_soal(): void
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
            $taAktif = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($taAktif);

            $guruUser = $this->makeUser('guru_pengajar', "guru.$suffix@test.local");
            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru ' . $suffix;
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $mapel = $this->makeMapel($suffix);

            // Kelas tujuan di TA AKTIF + penugasan guru (syarat validateKelasMapelTujuan).
            $kelasTujuan = $this->makeKelas($cabang->id, $taAktif->id, $suffix);
            GuruPengajarKelas::create([
                'tenaga_pendidik_id' => $tenaga->id,
                'kelas_id' => $kelasTujuan->id,
                'mata_pelajaran_id' => $mapel->id,
            ]);

            // Ujian SUMBER (arsip) milik guru + 2 soal.
            $sumber = Ujian::create([
                'kelas_id' => $kelasTujuan->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $tenaga->id,
                'judul_ujian' => 'Ujian Lama ' . $suffix,
                'deskripsi' => 'desc',
                'tanggal_mulai' => now()->subYear(),
                'tanggal_selesai' => now()->subYear()->addDays(7),
                'durasi_menit' => 90,
                'is_active' => true,
                'tampilkan_nilai' => true,
                'bisa_diulang' => false,
                'batas_pengulangan' => 1,
                'tampilkan_riwayat' => false,
                'tipe_ujian' => Ujian::TIPE_LATIHAN,
            ]);

            foreach ([1, 2] as $i) {
                SoalUjian::create([
                    'ujian_id' => $sumber->id,
                    'urutan' => $i,
                    'pertanyaan' => "Soal $i $suffix",
                    'tipe_soal' => 'pilihan_ganda',
                    'jumlah_pilihan' => 4,
                    'pilihan_jawaban' => ['A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd'],
                    'kunci_jawaban' => 'A',
                    'bobot_nilai' => 10,
                ]);
            }

            $svc = new GuruLmsArsipService();

            // Aksi: salin ke kelas+mapel tujuan (TA aktif).
            $baru = $svc->salinUjian($sumber->id, $kelasTujuan->id, $mapel->id, $tenaga->id, true);

            // Ujian baru dibuat, nonaktif, di kelas tujuan.
            $this->assertNotEquals($sumber->id, $baru->id);
            $this->assertSame($kelasTujuan->id, $baru->kelas_id);
            $this->assertSame($mapel->id, $baru->mata_pelajaran_id);
            $this->assertFalse((bool) $baru->is_active, 'Ujian salinan harus nonaktif');
            $this->assertSame('Ujian Lama ' . $suffix, $baru->judul_ujian);

            // Soal ikut tersalin (2), lengkap dengan kunci.
            $soalBaru = SoalUjian::where('ujian_id', $baru->id)->get();
            $this->assertCount(2, $soalBaru);
            $this->assertSame('A', $soalBaru->first()->kunci_jawaban);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeUser(string $role, string $email): User
    {
        $u = new User();
        $u->name = 'User ' . $email;
        $u->email = $email;
        $u->role = $role;
        $u->password = bcrypt('password');
        $u->save();
        return $u;
    }

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'K' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMA';
        $m->save();
        return $m;
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas ' . $suffix;
        $k->jenjang = 'SMA';
        $k->kode_kelas = 'K' . $suffix;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
    }
}
