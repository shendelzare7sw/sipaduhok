<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Guru\GuruMateriController;
use App\Models\GuruPengajarKelas;
use App\Models\TenagaPendidik;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Guru & wali kelas itu peran yang bisa dicopot lalu diganti orang lain. Dua hal
 * yang harus dijaga saat itu terjadi:
 *
 * 1. SERAH TERIMA - guru pengganti yang ditugaskan di kelas+mapel yang sama harus
 *    bisa MELANJUTKAN materi/tugas/ujian buatan guru sebelumnya. Dulu semua daftar
 *    konten LMS difilter guru_id = pembuat, jadi guru baru melihat daftar KOSONG
 *    (terbukti: 0 dari 4 materi, 0 dari 4 tugas, 0 dari 7 ujian) dan praktis tidak
 *    bisa meneruskan kelas. Sekarang akses mengikuti PENUGASAN (GuruPengajarKelas),
 *    sejalan dengan verifyAccess() dan authorizedUjian() yang memang sudah begitu.
 *    guru_id tetap dicatat saat pembuatan, jadi jejak "siapa yang membuat" tidak hilang.
 *
 * 2. TIDAK BOCOR - perluasan akses di atas tidak boleh membuat guru menjangkau kelas
 *    yang tidak ia ampu.
 *
 * Ditambah: guru dengan jejak mengajar tidak boleh bisa dihapus, karena menghapusnya
 * meng-cascade NILAI/UJIAN/TUGAS milik banyak siswa lain (bukan cuma data guru itu).
 *
 * Menjalankan: php artisan test --filter=GuruSerahTerimaKontenTest
 */
class GuruSerahTerimaKontenTest extends TestCase
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

    public function test_guru_pengganti_bisa_melanjutkan_konten_guru_lama(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();

        try {
            $sumber = DB::table('materi')
                ->selectRaw('kelas_id, mata_pelajaran_id, guru_id, count(*) n')
                ->groupBy('kelas_id', 'mata_pelajaran_id', 'guru_id')
                ->orderByDesc('n')->first();

            if (! $sumber || $sumber->n < 1) {
                $this->markTestSkipped('Butuh minimal 1 materi.');
            }

            $guruBaru = TenagaPendidik::where('id', '!=', $sumber->guru_id)->value('id');
            $this->assertNotNull($guruBaru, 'Butuh minimal 2 tenaga pendidik.');

            // Copot guru lama, tugaskan guru pengganti (persis aksi admin).
            GuruPengajarKelas::where('kelas_id', $sumber->kelas_id)
                ->where('mata_pelajaran_id', $sumber->mata_pelajaran_id)->delete();
            GuruPengajarKelas::create([
                'tenaga_pendidik_id' => $guruBaru,
                'kelas_id' => $sumber->kelas_id,
                'mata_pelajaran_id' => $sumber->mata_pelajaran_id,
            ]);

            foreach (['materi', 'tugas', 'ujian'] as $tabel) {
                $total = DB::table($tabel)->where('kelas_id', $sumber->kelas_id)
                    ->where('mata_pelajaran_id', $sumber->mata_pelajaran_id)->count();
                if ($total === 0) {
                    continue;
                }
                // Konten tidak boleh lagi terikat pembuatnya untuk urusan akses.
                $milikGuruBaru = DB::table($tabel)->where('kelas_id', $sumber->kelas_id)
                    ->where('mata_pelajaran_id', $sumber->mata_pelajaran_id)
                    ->where('guru_id', $guruBaru)->count();
                $this->assertLessThan($total, $milikGuruBaru + 1,
                    "Prasyarat: {$tabel} memang dibuat guru lain");
            }

            // kelasDiampu() harus persis mengikuti tabel penugasan.
            $ctl = app(GuruMateriController::class);
            $method = new ReflectionMethod($ctl, 'kelasDiampu');
            $method->setAccessible(true);

            $hasil = $method->invoke($ctl, $guruBaru, $sumber->mata_pelajaran_id);
            sort($hasil);
            $harusnya = GuruPengajarKelas::where('tenaga_pendidik_id', $guruBaru)
                ->where('mata_pelajaran_id', $sumber->mata_pelajaran_id)
                ->pluck('kelas_id')->sort()->values()->all();

            $this->assertSame($harusnya, $hasil,
                'kelasDiampu() harus persis sama dengan tabel penugasan');
            $this->assertContains($sumber->kelas_id, $hasil,
                'Guru pengganti harus menjangkau kelas yang baru ditugaskan padanya');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_kelas_diampu_tidak_bocor_ke_kelas_yang_tidak_ditugaskan(): void
    {
        $this->useMysql();

        $ctl = app(GuruMateriController::class);
        $method = new ReflectionMethod($ctl, 'kelasDiampu');
        $method->setAccessible(true);

        $diperiksa = 0;
        foreach (TenagaPendidik::limit(10)->get() as $guru) {
            foreach (\App\Models\MataPelajaran::limit(4)->get() as $mapel) {
                $hasil = $method->invoke($ctl, $guru->id, $mapel->id);
                sort($hasil);
                $harusnya = GuruPengajarKelas::where('tenaga_pendidik_id', $guru->id)
                    ->where('mata_pelajaran_id', $mapel->id)
                    ->pluck('kelas_id')->sort()->values()->all();
                $this->assertSame($harusnya, $hasil,
                    "Bocor pada guru={$guru->id} mapel={$mapel->id}");
                $diperiksa++;
            }
        }

        $this->assertGreaterThan(0, $diperiksa);
    }

    public function test_guru_berjejak_mengajar_tidak_bisa_dihapus(): void
    {
        $this->useMysql();

        $ctl = app(UserController::class);
        $method = new ReflectionMethod($ctl, 'tenagaPendidikBlockers');
        $method->setAccessible(true);

        // Guru yang punya nilai jelas harus terblokir - menghapusnya akan
        // meng-cascade nilai milik banyak siswa.
        $guruBernilai = DB::table('nilai')->whereNotNull('guru_id')->value('guru_id');
        if ($guruBernilai) {
            $this->assertNotEmpty($method->invoke($ctl, $guruBernilai),
                'Guru yang sudah menginput nilai wajib terblokir dari penghapusan');
        }

        // Tabel CASCADE yang dulu terlewat: guru dengan data di sini pun harus terblokir.
        foreach (['lms_meetings', 'pertemuans', 'catatan_monitoring'] as $tabel) {
            $guruId = DB::table($tabel)->whereNotNull('guru_id')->value('guru_id');
            if (! $guruId) {
                continue;
            }
            $this->assertNotEmpty($method->invoke($ctl, $guruId),
                "Guru dengan data di {$tabel} harus terblokir (tabel ini CASCADE)");
        }
    }
}
