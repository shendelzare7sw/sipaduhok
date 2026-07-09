<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-13/F-14/F-15: wali kelas hanya boleh mengelola presensi & melihat
 * bukti izin untuk siswa pada kelas yang ia ampu.
 *  - updatePresensi / inputHarian : tidak boleh menulis presensi kelas lain.
 *  - previewBukti                 : tidak boleh membuka bukti izin siswa kelas lain.
 */
class WaliPresensiIdorTest extends TestCase
{
    public function test_wali_tidak_bisa_mengelola_presensi_atau_bukti_kelas_lain(): void
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
        $buktiPath = null;
        try {
            $suffix = substr(md5(uniqid('', true)), 0, 8);

            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta, 'Butuh tahun ajaran aktif (getKelasWali men-scope ke TA aktif)');

            $waliUser = new User();
            $waliUser->name = 'Wali F13';
            $waliUser->email = "wali.f13.$suffix@test.local";
            $waliUser->role = 'wali_kelas';
            $waliUser->password = bcrypt('password');
            $waliUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $waliUser->id;
            $tenaga->nama_lengkap = 'Wali F13';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelasOwn = $this->makeKelas($cabang->id, $ta->id, 'A' . $suffix);   // diampu wali
            $kelasForeign = $this->makeKelas($cabang->id, $ta->id, 'B' . $suffix); // BUKAN diampu

            $assign = new WaliKelasAssignment();
            $assign->tenaga_pendidik_id = $tenaga->id;
            $assign->kelas_id = $kelasOwn->id;
            $assign->assigned_at = now();
            $assign->save();

            $siswaOwn = $this->makeSiswa($cabang->id, $kelasOwn->id, 'O' . $suffix);
            $siswaForeign = $this->makeSiswa($cabang->id, $kelasForeign->id, 'F' . $suffix);

            $this->actingAs($waliUser)->withoutMiddleware();
            $tanggal = now()->toDateString();

            // ===== updatePresensi =====
            // Positif: presensi siswa kelas sendiri boleh ditulis.
            $this->post(route('wali.presensi.update'), [
                'siswa_id' => $siswaOwn->id,
                'kelas_id' => $kelasOwn->id,
                'tanggal' => $tanggal,
                'status' => 'hadir',
            ])->assertRedirect();
            $this->assertDatabaseHas('presensi', [
                'siswa_id' => $siswaOwn->id,
                'kelas_id' => $kelasOwn->id,
                'status' => 'hadir',
            ]);

            // Negatif (F-13): presensi kelas lain harus ditolak & tidak tertulis.
            $this->post(route('wali.presensi.update'), [
                'siswa_id' => $siswaForeign->id,
                'kelas_id' => $kelasForeign->id,
                'tanggal' => $tanggal,
                'status' => 'alpha',
            ])->assertForbidden();
            $this->assertDatabaseMissing('presensi', [
                'siswa_id' => $siswaForeign->id,
                'kelas_id' => $kelasForeign->id,
            ]);

            // ===== inputHarian (bulk) =====
            // Negatif (F-14): kelas lain harus ditolak.
            $this->post(route('wali.presensi.input-harian'), [
                'kelas_id' => $kelasForeign->id,
                'tanggal' => $tanggal,
                'presensi' => [
                    ['siswa_id' => $siswaForeign->id, 'status' => 'alpha'],
                ],
            ])->assertForbidden();
            $this->assertDatabaseMissing('presensi', [
                'siswa_id' => $siswaForeign->id,
                'kelas_id' => $kelasForeign->id,
            ]);

            // ===== previewBukti =====
            // Siapkan file bukti nyata untuk kedua presensi.
            $relBukti = "bukti_izin_test_$suffix.txt";
            $buktiPath = storage_path('app/public/' . $relBukti);
            @mkdir(dirname($buktiPath), 0755, true);
            file_put_contents($buktiPath, 'DUMMY BUKTI IZIN');

            $presensiOwn = $this->makePresensiWithBukti($siswaOwn->id, $kelasOwn->id, $tanggal, $relBukti, $waliUser->id);
            $presensiForeign = $this->makePresensiWithBukti($siswaForeign->id, $kelasForeign->id, $tanggal, $relBukti, $waliUser->id);

            // Positif: bukti siswa kelas sendiri boleh dibuka.
            $this->get(route('wali.presensi.preview-bukti', $presensiOwn->id))->assertOk();

            // Negatif (F-15): bukti siswa kelas lain harus ditolak.
            $this->get(route('wali.presensi.preview-bukti', $presensiForeign->id))->assertForbidden();
        } finally {
            DB::connection('mysql')->rollBack();
            if ($buktiPath && file_exists($buktiPath)) {
                @unlink($buktiPath);
            }
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

    private function makePresensiWithBukti(int $siswaId, int $kelasId, string $tanggal, string $bukti, int $inputBy): Presensi
    {
        $p = new Presensi();
        $p->siswa_id = $siswaId;
        $p->kelas_id = $kelasId;
        $p->tanggal = $tanggal;
        $p->status = 'izin';
        $p->keterangan = 'Diajukan oleh wali siswa';
        $p->bukti_file = $bukti;
        $p->status_validasi = 'pending';
        $p->diinput_oleh = $inputBy;
        $p->save();
        return $p;
    }
}
