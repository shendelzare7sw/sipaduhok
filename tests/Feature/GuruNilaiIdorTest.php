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
 * Regresi-guard F-08: guru hanya boleh mengubah nilai pada kelas+mapel yang ia ajar.
 * Mengirim nilai_id milik mapel lain (yang tidak ia ajar) harus ditolak, bukan diubah.
 */
class GuruNilaiIdorTest extends TestCase
{
    public function test_guru_tidak_bisa_mengubah_nilai_mapel_yang_tidak_diajar(): void
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

            $cabang = Cabang::first() ?? tap(new Cabang(), function ($c) use ($suffix) {
                $c->kode_cabang = 'T' . substr($suffix, 0, 4);
                $c->nama_cabang = 'Cabang Test';
                $c->alamat = '-';
                $c->save();
            });
            $ta = TahunAjaran::first() ?? tap(new TahunAjaran(), function ($t) {
                $t->nama_tahun_ajaran = '2099/2100';
                $t->tanggal_mulai = now();
                $t->tanggal_selesai = now()->addYear();
                $t->save();
            });

            // Guru + tenaga pendidik
            $guruUser = new User();
            $guruUser->name = 'Guru Test';
            $guruUser->email = "guru.$suffix@test.local";
            $guruUser->role = 'guru_pengajar';
            $guruUser->password = bcrypt('password');
            $guruUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru Test';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            // Kelas + dua mapel; guru hanya mengajar mapelX
            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);
            $mapelX = $this->makeMapel('X' . $suffix);
            $mapelY = $this->makeMapel('Y' . $suffix);

            $gpk = new GuruPengajarKelas();
            $gpk->tenaga_pendidik_id = $tenaga->id;
            $gpk->kelas_id = $kelas->id;
            $gpk->mata_pelajaran_id = $mapelX->id;
            $gpk->save();

            // Siswa di kelas tsb
            $siswaUser = new User();
            $siswaUser->name = 'Siswa Test';
            $siswaUser->email = "siswa.$suffix@test.local";
            $siswaUser->role = 'siswa';
            $siswaUser->password = bcrypt('password');
            $siswaUser->save();

            $siswa = new Siswa();
            $siswa->user_id = $siswaUser->id;
            $siswa->cabang_id = $cabang->id;
            $siswa->kelas_id = $kelas->id;
            $siswa->nisn = 'N' . $suffix;
            $siswa->nama_lengkap = 'Siswa Test';
            $siswa->jenis_kelamin = 'L';
            $siswa->tempat_lahir = '-';
            $siswa->tanggal_lahir = '2010-01-01';
            $siswa->alamat = '-';
            $siswa->tanggal_masuk = now();
            $siswa->status = 'aktif';
            $siswa->save();

            $nilaiDiajar = $this->makeNilai($siswa->id, $mapelX->id, $kelas->id, $ta->id, $tenaga->id);
            $nilaiForeign = $this->makeNilai($siswa->id, $mapelY->id, $kelas->id, $ta->id, $tenaga->id);

            $this->actingAs($guruUser)->withoutMiddleware();

            // (1) Positif: nilai mapel yang diajar boleh diubah (behavior-preserving).
            $this->post(route('guru.lms.nilai.update', [$kelas->id, $mapelX->id]), [
                'nilai_id' => $nilaiDiajar->id,
                'pts' => 88,
            ])->assertRedirect();
            $this->assertSame('88.00', (string) $nilaiDiajar->fresh()->pts);

            // (2) Negatif (F-08): nilai mapel yang TIDAK diajar harus ditolak, tidak berubah.
            $this->post(route('guru.lms.nilai.update', [$kelas->id, $mapelX->id]), [
                'nilai_id' => $nilaiForeign->id,
                'pts' => 99,
            ])->assertNotFound();
            $this->assertNull($nilaiForeign->fresh()->pts);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas Test';
        $k->jenjang = 'SMP';
        $k->kode_kelas = 'K' . $suffix;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
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

    private function makeNilai(int $siswaId, int $mapelId, int $kelasId, int $taId, int $guruId): Nilai
    {
        $n = new Nilai();
        $n->siswa_id = $siswaId;
        $n->mata_pelajaran_id = $mapelId;
        $n->kelas_id = $kelasId;
        $n->tahun_ajaran_id = $taId;
        $n->guru_id = $guruId;
        $n->semester = 'ganjil';
        $n->save();
        return $n;
    }
}
