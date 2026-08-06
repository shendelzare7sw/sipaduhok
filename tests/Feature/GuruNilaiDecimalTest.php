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
 * Aturan desimal nilai: TITIK. Guru boleh mengetik koma; harus dinormalisasi ke
 * titik. Dulu updateBatch pakai floatval("9,8") = 9.0 (korup). Sekarang 9,8 -> 9.8.
 */
class GuruNilaiDecimalTest extends TestCase
{
    public function test_input_koma_dinormalisasi_ke_titik(): void
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

            $nilai = new Nilai();
            $nilai->siswa_id = $siswa->id;
            $nilai->mata_pelajaran_id = $mapel->id;
            $nilai->kelas_id = $kelas->id;
            $nilai->tahun_ajaran_id = $ta->id;
            $nilai->semester = 'ganjil';
            $nilai->guru_id = $tenaga->id;
            $nilai->save();

            $this->actingAs($guruUser)->withoutMiddleware();

            // Guru mengetik "9,8" (koma) untuk tugas_1.
            $res = $this->post(route('guru.lms.nilai.updateBatch', [$kelas->id, $mapel->id]), [
                'nilai' => [
                    $nilai->id => ['tugas_1' => '9,8'],
                ],
            ]);
            $res->assertRedirect();

            $nilai->refresh();
            // HARUS 9.8, bukan 9.0 (bukti koma tidak dipangkas floatval).
            $this->assertEquals(9.8, (float) $nilai->tugas_1, 'Koma harus dinormalisasi ke titik (9,8 -> 9.8)');
            $this->assertEquals(9.8, (float) $nilai->tugas_1_guru);
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
