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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-09: manajemen soal ujian hanya boleh pada ujian milik guru
 * di kelas+mapel yang ia ajar. Mengelola soal ujian mapel lain harus ditolak.
 */
class GuruUjianSoalIdorTest extends TestCase
{
    public function test_guru_tidak_bisa_menambah_soal_ke_ujian_mapel_lain(): void
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
            $ta = TahunAjaran::first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta);

            $guruUser = new User();
            $guruUser->name = 'Guru F09';
            $guruUser->email = "guru.f09.$suffix@test.local";
            $guruUser->role = 'guru_pengajar';
            $guruUser->password = bcrypt('password');
            $guruUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru F09';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelas = new Kelas();
            $kelas->cabang_id = $cabang->id;
            $kelas->tahun_ajaran_id = $ta->id;
            $kelas->nama_kelas = 'Kelas F09';
            $kelas->jenjang = 'SMP';
            $kelas->kode_kelas = 'KF09' . $suffix;
            $kelas->kuota_siswa = 30;
            $kelas->save();

            $mapelX = $this->makeMapel('X' . $suffix); // diajar guru
            $mapelY = $this->makeMapel('Y' . $suffix); // TIDAK diajar guru

            $gpk = new GuruPengajarKelas();
            $gpk->tenaga_pendidik_id = $tenaga->id;
            $gpk->kelas_id = $kelas->id;
            $gpk->mata_pelajaran_id = $mapelX->id;
            $gpk->save();

            $ujianOwn = $this->makeUjian($kelas->id, $mapelX->id, $tenaga->id);
            $ujianForeign = $this->makeUjian($kelas->id, $mapelY->id, $tenaga->id);

            $this->actingAs($guruUser)->withoutMiddleware();

            $payload = ['tipe_soal' => 'uraian', 'pertanyaan' => 'Soal uji', 'bobot' => 1, 'urutan' => 1];

            // Positif: menambah soal ke ujian mapel yang diajar => berhasil.
            $this->post(route('guru.lms.ujian.soal.store', [$kelas->id, $mapelX->id, $ujianOwn->id]), $payload)
                ->assertRedirect();
            $this->assertSame(1, SoalUjian::where('ujian_id', $ujianOwn->id)->count());

            // Negatif (F-09): menambah soal ke ujian mapel yang TIDAK diajar => ditolak, tidak dibuat.
            $this->post(route('guru.lms.ujian.soal.store', [$kelas->id, $mapelX->id, $ujianForeign->id]), $payload)
                ->assertNotFound();
            $this->assertSame(0, SoalUjian::where('ujian_id', $ujianForeign->id)->count());
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'M' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMP';
        $m->save();
        return $m;
    }

    private function makeUjian(int $kelasId, int $mapelId, int $guruId): Ujian
    {
        $u = new Ujian();
        $u->kelas_id = $kelasId;
        $u->mata_pelajaran_id = $mapelId;
        $u->guru_id = $guruId;
        $u->judul_ujian = 'Ujian Test';
        $u->tipe_ujian = 'ulangan_harian';
        $u->tanggal_mulai = now();
        $u->tanggal_selesai = now()->addHours(2);
        $u->durasi_menit = 60;
        $u->save();
        return $u;
    }
}
