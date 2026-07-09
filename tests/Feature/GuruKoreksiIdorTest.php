<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\TugasSiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-10: koreksi tugas hanya boleh pada tugas di kelas+mapel yang diajar guru.
 * Menilai pengumpulan tugas mapel lain harus ditolak, bukan diubah.
 */
class GuruKoreksiIdorTest extends TestCase
{
    public function test_guru_tidak_bisa_menilai_pengumpulan_tugas_mapel_lain(): void
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
            $guruUser->name = 'Guru F10';
            $guruUser->email = "guru.f10.$suffix@test.local";
            $guruUser->role = 'guru_pengajar';
            $guruUser->password = bcrypt('password');
            $guruUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru F10';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelas = new Kelas();
            $kelas->cabang_id = $cabang->id;
            $kelas->tahun_ajaran_id = $ta->id;
            $kelas->nama_kelas = 'Kelas F10';
            $kelas->jenjang = 'SMP';
            $kelas->kode_kelas = 'KF10' . $suffix;
            $kelas->kuota_siswa = 30;
            $kelas->save();

            $mapelX = $this->makeMapel('X' . $suffix); // diajar
            $mapelY = $this->makeMapel('Y' . $suffix); // TIDAK diajar

            $gpk = new GuruPengajarKelas();
            $gpk->tenaga_pendidik_id = $tenaga->id;
            $gpk->kelas_id = $kelas->id;
            $gpk->mata_pelajaran_id = $mapelX->id;
            $gpk->save();

            $siswaUser = new User();
            $siswaUser->name = 'Siswa F10';
            $siswaUser->email = "siswa.f10.$suffix@test.local";
            $siswaUser->role = 'siswa';
            $siswaUser->password = bcrypt('password');
            $siswaUser->save();

            $siswa = new Siswa();
            $siswa->user_id = $siswaUser->id;
            $siswa->cabang_id = $cabang->id;
            $siswa->kelas_id = $kelas->id;
            $siswa->nisn = 'N' . $suffix;
            $siswa->nama_lengkap = 'Siswa F10';
            $siswa->jenis_kelamin = 'L';
            $siswa->tempat_lahir = '-';
            $siswa->tanggal_lahir = '2010-01-01';
            $siswa->alamat = '-';
            $siswa->tanggal_masuk = now();
            $siswa->status = 'aktif';
            $siswa->save();

            $tugasOwn = $this->makeTugas($kelas->id, $mapelX->id, $tenaga->id);
            $tugasForeign = $this->makeTugas($kelas->id, $mapelY->id, $tenaga->id);
            $subOwn = $this->makeSubmission($tugasOwn->id, $siswa->id);
            $subForeign = $this->makeSubmission($tugasForeign->id, $siswa->id);

            $this->actingAs($guruUser)->withoutMiddleware();

            // Positif: menilai pengumpulan tugas mapel yang diajar => berhasil.
            $this->post(route('guru.lms.tugas.koreksi.store', [$kelas->id, $mapelX->id, $tugasOwn->id, $subOwn->id]), [
                'nilai' => 90,
            ])->assertRedirect();
            $this->assertSame('90.00', (string) $subOwn->fresh()->nilai);

            // Negatif (F-10): menilai pengumpulan tugas mapel yang TIDAK diajar => ditolak.
            $this->post(route('guru.lms.tugas.koreksi.store', [$kelas->id, $mapelX->id, $tugasForeign->id, $subForeign->id]), [
                'nilai' => 90,
            ])->assertNotFound();
            $this->assertNull($subForeign->fresh()->nilai);
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

    private function makeTugas(int $kelasId, int $mapelId, int $guruId): Tugas
    {
        $t = new Tugas();
        $t->kelas_id = $kelasId;
        $t->mata_pelajaran_id = $mapelId;
        $t->guru_id = $guruId;
        $t->judul_tugas = 'Tugas Test';
        $t->deskripsi = 'Deskripsi';
        $t->tanggal_mulai = now()->subDay();
        $t->tanggal_deadline = now()->addDay();
        $t->save();
        return $t;
    }

    private function makeSubmission(int $tugasId, int $siswaId): TugasSiswa
    {
        $s = new TugasSiswa();
        $s->tugas_id = $tugasId;
        $s->siswa_id = $siswaId;
        $s->status = 'dikerjakan';
        $s->save();
        return $s;
    }
}
