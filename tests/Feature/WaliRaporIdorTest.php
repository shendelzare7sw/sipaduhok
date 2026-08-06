<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-11: wali kelas hanya boleh mengelola rapor pada kelas yang ia ampu.
 * Mengubah rapor kelas lain lewat raporId sembarang harus ditolak, bukan diubah.
 */
class WaliRaporIdorTest extends TestCase
{
    public function test_wali_tidak_bisa_mengubah_rapor_kelas_lain(): void
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
            $waliUser->name = 'Wali F11';
            $waliUser->email = "wali.f11.$suffix@test.local";
            $waliUser->role = 'wali_kelas';
            $waliUser->password = bcrypt('password');
            $waliUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $waliUser->id;
            $tenaga->nama_lengkap = 'Wali F11';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelasOwn = $this->makeKelas($cabang->id, $ta->id, 'A' . $suffix);   // diampu wali
            $kelasForeign = $this->makeKelas($cabang->id, $ta->id, 'B' . $suffix); // BUKAN diampu

            $assign = new WaliKelasAssignment();
            $assign->tenaga_pendidik_id = $tenaga->id;
            $assign->kelas_id = $kelasOwn->id;
            $assign->assigned_at = now();
            $assign->save();

            $raporOwn = $this->makeRapor($this->makeSiswa($cabang->id, $kelasOwn->id, 'O' . $suffix)->id, $kelasOwn->id, $ta->id);
            $raporForeign = $this->makeRapor($this->makeSiswa($cabang->id, $kelasForeign->id, 'F' . $suffix)->id, $kelasForeign->id, $ta->id);

            $this->actingAs($waliUser)->withoutMiddleware();

            $payload = [
                'jumlah_sakit' => 0,
                'jumlah_izin' => 0,
                'jumlah_alpha' => 0,
                'catatan_wali_kelas' => 'CATATANTEST',
            ];

            // Positif: rapor kelas sendiri boleh diubah (behavior-preserving).
            $this->put(route('wali.rapor.update', $raporOwn->id), $payload)->assertRedirect();
            $this->assertSame('CATATANTEST', $raporOwn->fresh()->catatan_wali_kelas);

            // Negatif (F-11): rapor kelas lain harus ditolak & tidak berubah.
            $this->put(route('wali.rapor.update', $raporForeign->id), $payload)->assertNotFound();
            $this->assertNull($raporForeign->fresh()->catatan_wali_kelas);
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
        $s->save();
        return $s;
    }

    private function makeRapor(int $siswaId, int $kelasId, int $taId): Rapor
    {
        $r = new Rapor();
        $r->siswa_id = $siswaId;
        $r->kelas_id = $kelasId;
        $r->tahun_ajaran_id = $taId;
        $r->semester = 'ganjil';
        $r->status = 'draft';
        $r->save();
        return $r;
    }
}
