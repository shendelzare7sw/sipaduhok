<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-12: wali kelas hanya boleh memvalidasi akses ujian/rapor untuk
 * siswa pada kelas yang ia ampu. Memvalidasi siswa kelas lain lewat siswaId sembarang
 * harus ditolak (404), bukan diproses.
 */
class WaliValidasiAksesIdorTest extends TestCase
{
    public function test_wali_tidak_bisa_validasi_akses_siswa_kelas_lain(): void
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
            $this->assertNotNull($ta, 'Butuh tahun ajaran aktif (getKelasWali men-scope ke TA aktif)');

            $waliUser = new User();
            $waliUser->name = 'Wali F12';
            $waliUser->email = "wali.f12.$suffix@test.local";
            $waliUser->role = 'wali_kelas';
            $waliUser->password = bcrypt('password');
            $waliUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $waliUser->id;
            $tenaga->nama_lengkap = 'Wali F12';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelasOwn = $this->makeKelas($cabang->id, $ta->id, 'A' . $suffix);   // diampu wali
            $kelasForeign = $this->makeKelas($cabang->id, $ta->id, 'B' . $suffix); // BUKAN diampu

            $assign = new WaliKelasAssignment();
            $assign->tenaga_pendidik_id = $tenaga->id;
            $assign->kelas_id = $kelasOwn->id;
            $assign->assigned_at = now();
            $assign->save();

            // Kedua siswa sudah lolos validasi bendahara supaya validasiUjian akan memproses
            // jika tidak diblok. Dengan begitu yang membedakan positif vs negatif murni
            // gerbang kepemilikan kelas (F-12), bukan syarat bendahara.
            $siswaOwn = $this->makeSiswa($cabang->id, $kelasOwn->id, 'O' . $suffix);
            $siswaForeign = $this->makeSiswa($cabang->id, $kelasForeign->id, 'F' . $suffix);

            $this->actingAs($waliUser)->withoutMiddleware();

            // ===== Ujian =====
            // Positif: siswa kelas sendiri boleh divalidasi (behavior-preserving).
            $this->post(route('wali.validasi-akses.validasi-ujian', $siswaOwn->id))->assertRedirect();
            $this->assertTrue((bool) $siswaOwn->fresh()->validasi_ujian_wali, 'Siswa kelas sendiri seharusnya tervalidasi');

            // Negatif (F-12): siswa kelas lain harus ditolak & tidak berubah.
            $this->post(route('wali.validasi-akses.validasi-ujian', $siswaForeign->id))->assertNotFound();
            $this->assertFalse((bool) $siswaForeign->fresh()->validasi_ujian_wali, 'Siswa kelas lain TIDAK boleh tervalidasi');

            // ===== Rapor =====
            // Positif: rapor siswa kelas sendiri boleh dikirim ke ketua.
            $this->post(route('wali.validasi-akses.validasi-rapor', $siswaOwn->id))->assertRedirect();
            $this->assertTrue((bool) $siswaOwn->fresh()->validasi_rapor_wali, 'Rapor siswa kelas sendiri seharusnya tervalidasi');

            // Negatif (F-12): rapor siswa kelas lain harus ditolak & tidak berubah.
            $this->post(route('wali.validasi-akses.validasi-rapor', $siswaForeign->id))->assertNotFound();
            $this->assertFalse((bool) $siswaForeign->fresh()->validasi_rapor_wali, 'Rapor siswa kelas lain TIDAK boleh tervalidasi');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas ' . $suffix;
        $k->jenjang = 'SMP';
        $k->kode_kelas = 'K' . $suffix;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
    }

    private function makeSiswa(int $cabangId, int $kelasId, string $suffix): Siswa
    {
        $u = new User();
        $u->name = 'Siswa ' . $suffix;
        $u->email = "siswa.$suffix@test.local";
        $u->role = 'siswa';
        $u->password = bcrypt('password');
        $u->save();

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
        $s->validasi_ujian_bendahara = true;
        $s->save();
        return $s;
    }
}
