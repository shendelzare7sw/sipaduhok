<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * IDOR lintas-cabang pada jadwal (Wakil Kepala Sekolah).
 *
 * Waka hanya berwenang atas cabangnya. destroy() sudah mengecek kepemilikan cabang,
 * tetapi edit/update/gantiGuru dulu TIDAK -> Waka bisa melihat/mengubah jadwal cabang
 * lain. Test memastikan akses ke jadwal cabang lain kini ditolak 403.
 *
 * Menjalankan: php artisan test --filter=WakaJadwalIdorTest
 */
class WakaJadwalIdorTest extends TestCase
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

    public function test_waka_tidak_bisa_melihat_mengekspor_atau_mengubah_jadwal_cabang_lain(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            $sfx = substr(md5(uniqid('', true)), 0, 8);
            $ta = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($ta, 'Butuh TA aktif (seed).');

            $cabangWaka = Cabang::first();
            $this->assertNotNull($cabangWaka);

            // Cabang LAIN + kelas + jadwal di cabang lain itu
            $cabangLain = new Cabang;
            $cabangLain->kode_cabang = 'X'.substr($sfx, 0, 4);
            $cabangLain->nama_cabang = 'Cabang Lain '.$sfx;
            $cabangLain->alamat = 'Alamat';
            $cabangLain->save();

            $mapel = new MataPelajaran;
            $mapel->kode_mapel = 'K'.$sfx;
            $mapel->nama_mapel = 'Mapel '.$sfx;
            $mapel->jenjang = 'SMP';
            $mapel->save();

            $kelasLain = new Kelas;
            $kelasLain->cabang_id = $cabangLain->id;
            $kelasLain->tahun_ajaran_id = $ta->id;
            $kelasLain->nama_kelas = 'Kelas '.$sfx;
            $kelasLain->jenjang = 'SMP';
            $kelasLain->kode_kelas = 'K'.$sfx;
            $kelasLain->kuota_siswa = 30;
            $kelasLain->save();

            $jadwal = JadwalPelajaran::create([
                'tahun_ajaran_id' => $ta->id,
                'kelas_id' => $kelasLain->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => null,
                'hari' => 'Senin',
                'jam_mulai' => '07:00',
                'jam_selesai' => '08:00',
                'status' => 'kosong',
            ]);
            $jadwal->kelas()->sync([$kelasLain->id]);

            // Waka milik cabang berbeda (cabangWaka), mencoba edit jadwal cabang lain
            $waka = new User;
            $waka->name = 'Waka '.$sfx;
            $waka->email = "waka.$sfx@test.local";
            $waka->role = 'wakil_kepala_sekolah';
            $waka->cabang_id = $cabangWaka->id;
            $waka->password = bcrypt('password');
            $waka->save();

            // Pastikan memang beda cabang
            $this->assertNotEquals($cabangWaka->id, $cabangLain->id);

            $this->actingAs($waka)->withoutMiddleware()
                ->get(route('waka.jadwal-pelajaran.edit', $jadwal->id))
                ->assertForbidden();

            foreach (['show', 'preview-print', 'print', 'export-excel-class'] as $routeName) {
                $this->get(route("waka.jadwal-pelajaran.{$routeName}", [
                    'kelas' => $kelasLain->id,
                    'tahun_ajaran_id' => $ta->id,
                ]))->assertForbidden();
            }

            $this->get(route('waka.jadwal-pelajaran.api.by-kelas', [
                'kelas' => $kelasLain->id,
                'tahun_ajaran_id' => $ta->id,
            ]))->assertForbidden();
            $this->get(route('waka.jadwal-pelajaran.get-students', $kelasLain->id))
                ->assertForbidden();

            $this->get(route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $ta->id]))
                ->assertOk()
                ->assertDontSee($mapel->nama_mapel);
            $this->get(route('waka.jadwal-pelajaran.export-pdf', [
                'tahun_ajaran_id' => $ta->id,
                'kelas_id' => $kelasLain->id,
            ]))->assertOk()->assertDontSee($mapel->nama_mapel)->assertDontSee($kelasLain->nama_kelas);

            $this->post(route('waka.jadwal-pelajaran.bulk-update-status'), [
                'jadwal_ids' => json_encode([$jadwal->id]),
                'status' => 'aktif',
            ])->assertRedirect();
            $this->assertSame('kosong', $jadwal->fresh()->status);

            $this->post(route('waka.jadwal-pelajaran.bulk-delete'), [
                'jadwal_ids' => json_encode([$jadwal->id]),
            ])->assertRedirect();
            $this->assertDatabaseHas('jadwal_pelajaran', ['id' => $jadwal->id]);

            $basePayload = [
                'tahun_ajaran_id' => $ta->id,
                'kelas_ids' => [$kelasLain->id],
                'guru_id' => null,
                'hari' => 'Selasa',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00',
            ];
            $this->post(route('waka.jadwal-pelajaran.store'), $basePayload + [
                'mata_pelajaran_id' => $mapel->id,
            ])->assertForbidden();
            $this->post(route('waka.jadwal-pelajaran.store'), $basePayload + [
                'is_multi_jenjang' => 1,
                'mapel_per_jenjang' => ['SMP' => $mapel->id],
            ])->assertForbidden();

            $this->assertSame(1, JadwalPelajaran::whereHas(
                'kelas',
                fn ($query) => $query->whereKey($kelasLain->id)
            )->count());
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }
}
