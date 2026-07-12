<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\SoalUjian;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Fitur salin MASSAL arsip: guru memilih beberapa konten (materi/tugas/ujian)
 * lalu menyalin sekaligus ke satu kelas+mapel tujuan di TA aktif.
 */
class GuruArsipSalinBulkTest extends TestCase
{
    public function test_salin_bulk_materi_tugas_ujian_sekaligus(): void
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
            $kelasSumber = $this->makeKelas($cabang->id, $taAktif->id, 'SRC' . $suffix);
            $kelasTujuan = $this->makeKelas($cabang->id, $taAktif->id, 'DST' . $suffix);

            GuruPengajarKelas::create([
                'tenaga_pendidik_id' => $tenaga->id,
                'kelas_id' => $kelasTujuan->id,
                'mata_pelajaran_id' => $mapel->id,
            ]);

            // Konten sumber (arsip) milik guru di kelas sumber.
            $materi = Materi::create([
                'kelas_id' => $kelasSumber->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $tenaga->id,
                'judul_materi' => 'Materi ' . $suffix,
                'kategori' => 'materi',
                'deskripsi' => 'd',
                'tanggal_upload' => now()->subYear()->toDateString(),
            ]);
            $tugas = Tugas::create([
                'kelas_id' => $kelasSumber->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $tenaga->id,
                'jenis_tugas' => Tugas::JENIS_TUGAS,
                'judul_tugas' => 'Tugas ' . $suffix,
                'deskripsi' => 'd',
                'tanggal_mulai' => now()->subYear()->toDateString(),
                'tanggal_deadline' => now()->subYear()->addWeek()->toDateString(),
            ]);
            $ujian = Ujian::create([
                'kelas_id' => $kelasSumber->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $tenaga->id,
                'judul_ujian' => 'Ujian ' . $suffix,
                'deskripsi' => 'd',
                'tanggal_mulai' => now()->subYear(),
                'tanggal_selesai' => now()->subYear()->addWeek(),
                'durasi_menit' => 60,
                'is_active' => true,
                'tampilkan_nilai' => true,
                'bisa_diulang' => false,
                'batas_pengulangan' => 1,
                'tampilkan_riwayat' => false,
                'tipe_ujian' => Ujian::TIPE_LATIHAN,
            ]);
            SoalUjian::create([
                'ujian_id' => $ujian->id,
                'urutan' => 1,
                'pertanyaan' => 'Soal ' . $suffix,
                'tipe_soal' => 'pilihan_ganda',
                'jumlah_pilihan' => 4,
                'pilihan_jawaban' => ['A' => 'a', 'B' => 'b'],
                'kunci_jawaban' => 'A',
                'bobot_nilai' => 10,
            ]);

            $this->actingAs($guruUser)->withoutMiddleware();

            $res = $this->post(route('guru.lms.arsip.salin-bulk'), [
                'items' => [
                    'materi:' . $materi->id,
                    'tugas:' . $tugas->id,
                    'ujian:' . $ujian->id,
                ],
                'kelas_id' => $kelasTujuan->id,
                'mata_pelajaran_id' => $mapel->id,
                'sertakan_soal' => 1,
            ]);

            $res->assertRedirect();

            // Ketiga konten tersalin ke kelas tujuan.
            $this->assertSame(1, Materi::where('kelas_id', $kelasTujuan->id)
                ->where('judul_materi', 'Materi ' . $suffix)->count());
            $this->assertSame(1, Tugas::where('kelas_id', $kelasTujuan->id)
                ->where('judul_tugas', 'Tugas ' . $suffix)->count());

            $ujianBaru = Ujian::where('kelas_id', $kelasTujuan->id)
                ->where('judul_ujian', 'Ujian ' . $suffix)->first();
            $this->assertNotNull($ujianBaru);
            $this->assertFalse((bool) $ujianBaru->is_active, 'Ujian salinan harus nonaktif');
            $this->assertSame(1, SoalUjian::where('ujian_id', $ujianBaru->id)->count());
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
