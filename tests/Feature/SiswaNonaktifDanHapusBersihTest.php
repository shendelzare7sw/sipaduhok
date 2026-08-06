<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\UserController;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Dua kontrak yang gampang rusak diam-diam kalau ada yang mengubah query siswa:
 *
 * 1. Siswa dengan AKUN NONAKTIF (users.is_active = 0) harus HILANG dari seluruh
 *    query aplikasi - detail kelas, presensi wali, penilaian guru, ujian, LMS,
 *    sampai keuangan. Dulu dia tetap nongol karena menu-menu itu memfilter
 *    siswa.status (yang tetap 'aktif'), bukan status akunnya. Sekarang dijamin
 *    lewat AkunAktifScope (global scope), dan halaman Kelola Siswa admin
 *    membukanya kembali lewat scope termasukNonaktif().
 *
 * 2. Hapus permanen harus BENAR-BENAR bersih - tidak boleh menyisakan satupun
 *    baris yang menyebut siswa itu (termasuk jadwal_pelajaran.siswa_ids yang
 *    berupa kolom JSON TANPA foreign key, jadi tidak ikut ON DELETE CASCADE).
 *    Sisa record semacam itu jadi sampah yang bisa memunculkan bug.
 *
 * Menjalankan: php artisan test --filter=SiswaNonaktifDanHapusBersihTest
 */
class SiswaNonaktifDanHapusBersihTest extends TestCase
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

    public function test_siswa_berakun_nonaktif_hilang_dari_semua_query(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();

        try {
            $siswa = Siswa::termasukNonaktif()
                ->whereHas('user', fn ($q) => $q->where('is_active', 1))
                ->whereNotNull('kelas_id')
                ->first();

            if (! $siswa) {
                $this->markTestSkipped('Butuh minimal 1 siswa aktif yang punya kelas.');
            }

            $kelasId = $siswa->kelas_id;
            $sebelumGlobal = Siswa::count();
            $sebelumKelas = Siswa::where('kelas_id', $kelasId)->count();
            $sebelumAdmin = Siswa::termasukNonaktif()->count();

            $siswa->user->update(['is_active' => 0]);

            $this->assertSame($sebelumGlobal - 1, Siswa::count(),
                'Siswa berakun nonaktif harus hilang dari query global');
            $this->assertSame($sebelumKelas - 1, Siswa::where('kelas_id', $kelasId)->count(),
                'Harus hilang dari daftar siswa per kelas (mis. /admin/kelas/show)');
            $this->assertNull(Siswa::find($siswa->id),
                'find() biasa tidak boleh menemukannya');

            // Tapi halaman pengelolaan admin WAJIB tetap bisa melihat & mengelolanya,
            // kalau tidak, akun nonaktif jadi mustahil diaktifkan kembali / dihapus.
            $this->assertSame($sebelumAdmin, Siswa::termasukNonaktif()->count(),
                'Kelola Siswa admin harus tetap menampilkannya');
            $this->assertNotNull(Siswa::termasukNonaktif()->find($siswa->id));
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_hapus_permanen_tidak_menyisakan_record_sampah(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();

        try {
            // Ambil siswa yang datanya paling banyak supaya ujiannya berat.
            $target = DB::table('siswa')
                ->leftJoin('nilai', 'nilai.siswa_id', '=', 'siswa.id')
                ->leftJoin('presensi', 'presensi.siswa_id', '=', 'siswa.id')
                ->selectRaw('siswa.id, siswa.user_id, count(distinct nilai.id) + count(distinct presensi.id) total')
                ->groupBy('siswa.id', 'siswa.user_id')
                ->orderByRaw('count(distinct nilai.id) + count(distinct presensi.id) DESC')
                ->first();

            if (! $target || $target->total < 1) {
                $this->markTestSkipped('Butuh siswa yang punya nilai/presensi.');
            }

            $sid = $target->id;
            $raporIds = DB::table('rapor')->where('siswa_id', $sid)->pluck('id');
            $ujianSiswaIds = DB::table('ujian_siswa')->where('siswa_id', $sid)->pluck('id');

            // Sisipkan ID siswa ke kolom JSON tanpa FK - ini yang dulu jadi sampah.
            $jadwal = \App\Models\JadwalPelajaran::first();
            if ($jadwal) {
                $jadwal->siswa_ids = [$sid, 999999];
                $jadwal->save();
            }

            $siswa = Siswa::termasukNonaktif()->findOrFail($sid);
            $method = new ReflectionMethod(UserController::class, 'hapusSiswaPermanen');
            $method->setAccessible(true);
            $method->invoke(app(UserController::class), $siswa);

            foreach ([
                'nilai', 'presensi', 'tagihan', 'pembayaran', 'rapor', 'ujian_siswa',
                'tugas_siswa', 'student_parents', 'status_naik_kelas_siswa',
                'izin_naik_kelas_khusus', 'pengajuan_rapor_ketua', 'request_download_rapor',
            ] as $tabel) {
                $this->assertSame(0, DB::table($tabel)->where('siswa_id', $sid)->count(),
                    "Masih ada sisa data siswa di tabel {$tabel}");
            }

            if ($raporIds->isNotEmpty()) {
                $this->assertSame(0, DB::table('rapor_nilai')->whereIn('rapor_id', $raporIds)->count());
                $this->assertSame(0, DB::table('rapor_kegiatan_ekstra')->whereIn('rapor_id', $raporIds)->count());
            }
            if ($ujianSiswaIds->isNotEmpty()) {
                $this->assertSame(0, DB::table('jawaban_siswa')->whereIn('ujian_siswa_id', $ujianSiswaIds)->count());
                $this->assertSame(0, DB::table('ujian_siswa_soal_statuses')->whereIn('ujian_siswa_id', $ujianSiswaIds)->count());
            }

            $this->assertSame(0, DB::table('siswa')->where('id', $sid)->count());
            $this->assertSame(0, DB::table('users')->where('id', $target->user_id)->count(),
                'Akun user siswa harus ikut terhapus');

            // sessions & notifications tidak punya FK yg menutup kasus ini:
            // - sessions.user_id sama sekali tanpa FK -> sesi yatim.
            // - notifications milik user lain yg membicarakan siswa ini (data->siswa_id)
            //   tidak ikut cascade -> referensi menggantung di kotak Admin/Bendahara.
            $this->assertSame(0, DB::table('sessions')->where('user_id', $target->user_id)->count(),
                'Sesi login milik akun yang dihapus tidak boleh menggantung');
            $this->assertSame(0, DB::table('notifications')->where('data->siswa_id', $sid)->count(),
                'Notifikasi yang menunjuk siswa terhapus harus ikut dibersihkan');
            foreach (['ujian_siswa_id', 'tugas_siswa_id', 'presensi_id'] as $kunci) {
                $this->assertSame(0,
                    DB::table('notifications')
                        ->whereNotNull('data->'.$kunci)
                        ->whereNotExists(function ($q) use ($kunci) {
                            $tabel = ['ujian_siswa_id' => 'ujian_siswa', 'tugas_siswa_id' => 'tugas_siswa', 'presensi_id' => 'presensi'][$kunci];
                            $q->select(DB::raw(1))->from($tabel)->whereColumn(
                                $tabel.'.id',
                                DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(notifications.data, \'$."'.$kunci.'"\')) AS UNSIGNED)')
                            );
                        })->count(),
                    "Notifikasi yatim lewat {$kunci} harus ikut dibersihkan"
                );
            }

            if ($jadwal) {
                $sisa = \App\Models\JadwalPelajaran::find($jadwal->id)->siswa_ids ?? [];
                $this->assertNotContains($sid, $sisa,
                    'ID siswa harus dibersihkan dari jadwal_pelajaran.siswa_ids (kolom tanpa FK)');
                $this->assertContains(999999, $sisa,
                    'ID lain yang tidak terkait tidak boleh ikut terhapus');
            }
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_akun_admin_dan_ketua_pkbm_tidak_bisa_dihapus(): void
    {
        $this->useMysql();

        $method = new ReflectionMethod(UserController::class, 'tolakHapusRoleDilindungi');
        $method->setAccessible(true);
        $controller = app(UserController::class);

        foreach (['admin', 'ketua_pkbm'] as $role) {
            $user = User::where('role', $role)->first();
            if (! $user) {
                continue;
            }
            $this->assertNotNull($method->invoke($controller, $user),
                "Akun {$role} seharusnya dilindungi dari penghapusan");
        }

        foreach (['bendahara', 'guru_pengajar', 'wali_kelas'] as $role) {
            $user = User::where('role', $role)->first();
            if (! $user) {
                continue;
            }
            $this->assertNull($method->invoke($controller, $user),
                "Akun {$role} tidak seharusnya ikut diblokir");
        }
    }
}
