<?php

namespace Tests\Feature;

use App\Imports\Guru\NilaiSiswaImport;
use App\Models\Cabang;
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
 * Regresi scoping import nilai (Guru\NilaiSiswaImport).
 *
 * Lookup siswa dulu: where('nis',X)->orWhere('nisn',X)->where('kelas_id',Y)
 * => SQL "nis=X OR (nisn=X AND kelas_id=Y)" -> filter kelas bocor pada cabang nis.
 * Karena nis/nisn unik global, guru bisa menulis nilai ke siswa KELAS LAIN.
 * Setelah fix: OR dikelompokkan -> siswa kelas lain TIDAK ditemukan (return null + error).
 *
 * Menjalankan: php artisan test --filter=NilaiImportScopeTest
 */
class NilaiImportScopeTest extends TestCase
{
    private function useMysql(): void
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
    }

    public function test_nilai_tidak_tertulis_ke_siswa_kelas_lain(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            $sfx = substr(md5(uniqid('', true)), 0, 8);
            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta);

            $mapel = $this->makeMapel($sfx);
            $kelasA = $this->makeKelas($cabang->id, $ta->id, 'A' . $sfx); // kelas guru
            $kelasB = $this->makeKelas($cabang->id, $ta->id, 'B' . $sfx); // kelas lain
            $guru = $this->makeGuru($sfx);

            // Siswa dgn NIS unik, berada di KELAS B (bukan kelas guru)
            $nis = '77' . substr(md5($sfx), 0, 7);
            $siswaB = $this->makeSiswa($cabang->id, $kelasB->id, $sfx, $nis);

            $import = new NilaiSiswaImport($kelasA->id, $mapel->id, $ta->id, 'ganjil', $guru->id);

            // Guru kelas A meng-import nilai untuk NIS milik siswa kelas B
            $result = $import->model(['nisnisn' => $nis, 'uh_1' => 80]);

            // Harus ditolak: siswa tak ada di kelas A
            $this->assertNull($result, 'Nilai tidak boleh dibuat untuk siswa kelas lain');
            $this->assertNotEmpty($import->getErrors());

            // Pastikan TIDAK ada baris nilai untuk siswa B di kelas A
            $bocor = Nilai::where('siswa_id', $siswaB->id)->where('kelas_id', $kelasA->id)->exists();
            $this->assertFalse($bocor, 'Tidak boleh ada nilai siswa kelas B yang tertulis di kelas A');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_nilai_tertulis_untuk_siswa_di_kelas_guru(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            $sfx = substr(md5(uniqid('', true)), 0, 8);
            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();

            $mapel = $this->makeMapel($sfx);
            $kelasA = $this->makeKelas($cabang->id, $ta->id, 'A' . $sfx);
            $guru = $this->makeGuru($sfx);
            $nis = '88' . substr(md5($sfx), 0, 7);
            $siswaA = $this->makeSiswa($cabang->id, $kelasA->id, $sfx, $nis);

            $import = new NilaiSiswaImport($kelasA->id, $mapel->id, $ta->id, 'ganjil', $guru->id);
            $result = $import->model(['nisnisn' => $nis, 'uh_1' => 80]);

            $this->assertNotNull($result, 'Nilai siswa di kelas guru harus terbuat');
            $this->assertEquals($siswaA->id, $result->siswa_id);
            $this->assertEquals($kelasA->id, $result->kelas_id);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeMapel(string $s): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'K' . $s;
        $m->nama_mapel = 'Mapel ' . $s;
        $m->jenjang = 'SMP';
        $m->save();
        return $m;
    }

    private function makeKelas(int $cabangId, int $taId, string $s): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas ' . $s;
        $k->jenjang = 'SMP';
        $k->kode_kelas = 'K' . $s;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
    }

    private function makeGuru(string $s): TenagaPendidik
    {
        $u = new User();
        $u->name = 'Guru ' . $s;
        $u->email = "guru.$s@test.local";
        $u->role = 'guru_pengajar';
        $u->password = bcrypt('password');
        $u->save();
        $g = new TenagaPendidik();
        $g->user_id = $u->id;
        $g->nama_lengkap = 'Guru ' . $s;
        $g->jenis_kelamin = 'L';
        $g->save();
        return $g;
    }

    private function makeSiswa(int $cabangId, int $kelasId, string $s, string $nis): Siswa
    {
        $u = new User();
        $u->name = 'Siswa ' . $s;
        $u->email = "siswa.$s@test.local";
        $u->role = 'siswa';
        $u->password = bcrypt('password');
        $u->save();
        $x = new Siswa();
        $x->user_id = $u->id;
        $x->cabang_id = $cabangId;
        $x->kelas_id = $kelasId;
        $x->nis = $nis;
        $x->nisn = 'N' . substr(md5($nis), 0, 9);
        $x->nama_lengkap = 'Siswa ' . $s;
        $x->jenis_kelamin = 'L';
        $x->tempat_lahir = 'Kota';
        $x->tanggal_lahir = '2011-01-01';
        $x->alamat = 'Alamat';
        $x->tanggal_masuk = now()->toDateString();
        $x->status = 'aktif';
        $x->save();
        return $x;
    }
}
