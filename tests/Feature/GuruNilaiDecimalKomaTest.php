<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Aturan desimal tunggal (TITIK). Guru boleh mengetik koma; backend menormalkan
 * ke titik. Dulu updateBatch memakai floatval("9,8") = 9.0 (data korup) — kini
 * "9,8" harus tersimpan sebagai 9.8.
 */
class GuruNilaiDecimalKomaTest extends TestCase
{
    public function test_input_koma_disimpan_sebagai_titik(): void
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

            $guruUser = $this->makeUser('guru_pengajar', "guru.$suffix@test.local");
            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru ' . $suffix;
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $mapel = $this->makeMapel($suffix);
            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);
            GuruPengajarKelas::create([
                'tenaga_pendidik_id' => $tenaga->id,
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $mapel->id,
            ]);

            $siswa = $this->makeSiswa($cabang->id, $kelas->id, $suffix);
            $nilai = Nilai::create([
                'siswa_id' => $siswa->id,
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'tahun_ajaran_id' => $ta->id,
                'semester' => Nilai::getCurrentSemester(),
                'guru_id' => $tenaga->id,
            ]);

            $this->actingAs($guruUser)->withoutMiddleware();

            // Guru mengetik koma untuk beberapa field.
            $this->post(route('guru.lms.nilai.updateBatch', [$kelas->id, $mapel->id]), [
                'nilai' => [
                    $nilai->id => [
                        'tugas_1' => '9,8',
                        'pts' => '87,5',
                        'pas' => '90',
                    ],
                ],
            ])->assertRedirect();

            $fresh = $nilai->fresh();
            // Nilai desimal tersimpan utuh (bukan 9.0 / 87.0).
            $this->assertEquals(9.8, (float) $fresh->tugas_1);
            $this->assertEquals(87.5, (float) $fresh->pts);
            $this->assertEquals(90.0, (float) $fresh->pas);
            // Snapshot guru juga benar.
            $this->assertEquals(9.8, (float) $fresh->tugas_1_guru);
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

    private function makeSiswa(int $cabangId, int $kelasId, string $suffix): Siswa
    {
        $u = $this->makeUser('siswa', "siswa.$suffix@test.local");

        $s = new Siswa();
        $s->user_id = $u->id;
        $s->cabang_id = $cabangId;
        $s->kelas_id = $kelasId;
        $s->nisn = 'N' . $suffix;
        $s->nama_lengkap = 'Siswa ' . $suffix;
        $s->jenis_kelamin = 'L';
        $s->tempat_lahir = '-';
        $s->tanggal_lahir = '2010-01-01';
        $s->alamat = '-';
        $s->tanggal_masuk = now();
        $s->status = 'aktif';
        $s->save();
        return $s;
    }
}
