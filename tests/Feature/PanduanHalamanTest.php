<?php

namespace Tests\Feature;

use App\Support\Panduan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Setiap halaman pascalogin (SIA & LMS) wajib memiliki panduan agar halaman baru tidak terlewat,
 * dan setiap route hanya boleh terdaftar sekali di seluruh sumber panduan.
 */
class PanduanHalamanTest extends TestCase
{
    /** Route GET yang bukan halaman berpanduan, beserta alasannya. */
    private const BUKAN_HALAMAN = [
        'admin.keuangan.info-pembayaran.legacy-index' => 'redirect',
        'siswa.dashboard' => 'redirect ke SIA',
        'siswa.lms.mapel.forum.create' => 'redirect (topik hanya dibuat guru)',
        'siswa.lms.mapel.tugas.index' => 'redirect ke daftar tugas',
        'notifications.recent' => 'JSON',
        'notifications.today' => 'JSON',
        'admin.akademik.kalender.bulanan' => 'JSON data kalender',
        'siswa.lms.mapel.latihan.show' => 'zona preservasi Latihan/Ujian siswa',
        'siswa.lms.mapel.ujian.show' => 'zona preservasi Latihan/Ujian siswa',
        'siswa.lms.mapel.latihan.review' => 'zona preservasi Latihan/Ujian siswa',
        'siswa.lms.mapel.ujian.review' => 'zona preservasi Latihan/Ujian siswa',
        'sekretaris.kalender.bulanan' => 'JSON data kalender',
        'admin.recovery' => 'layout auth',
        'admin.security.setup' => 'layout auth',
        'admin.laporan.guru-pengajar' => 'dokumen cetak',
        'admin.laporan.kelas' => 'dokumen cetak',
        'admin.laporan.rekap' => 'dokumen cetak',
        'admin.laporan.siswa' => 'dokumen cetak',
        'admin.laporan.tenaga-pendidik' => 'dokumen cetak',
        'admin.laporan.wali-kelas' => 'dokumen cetak',
        'ketua.laporan.guru-pengajar' => 'dokumen cetak',
        'ketua.laporan.kelas' => 'dokumen cetak',
        'ketua.laporan.rekap' => 'dokumen cetak',
        'ketua.laporan.rekap-akademik' => 'dokumen cetak',
        'ketua.laporan.siswa' => 'dokumen cetak',
        'ketua.laporan.tenaga-pendidik' => 'dokumen cetak',
        'ketua.laporan.wali-kelas' => 'dokumen cetak',
    ];

    /** Nama route unduhan, ekspor, JSON, dan aksi pendukung yang tidak merender halaman. */
    private const POLA_BUKAN_HALAMAN = '/(export|download-template|\.print|print-|cetak|pdf|excel|json|\.api\.|preview|stream|ajax|search|autocomplete|poll|unread|latest|sync|refresh|ping|heartbeat|kwitansi|invoice|serve|view-document|lampiran|foto|avatar|get-|fetch|lookup|realtime|suggest|\.data$|download$|bukti|template$|\.generate$|generate-(?!spp)|\.count$)/';

    public function test_setiap_halaman_memiliki_panduan(): void
    {
        $tanpaPanduan = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (! $name || ! in_array('GET', $route->methods(), true)) {
                continue;
            }
            $halaman = preg_match('/^(admin|ketua|waka|sekretaris|bendahara|wali|guru|siswa|wali-siswa|notifications)\./', $name) === 1
                || in_array($name, ['profile.index', 'account.settings'], true);
            if (! $halaman || isset(self::BUKAN_HALAMAN[$name]) || preg_match(self::POLA_BUKAN_HALAMAN, $name) === 1) {
                continue;
            }
            if (Panduan::untuk($name) === null) {
                $tanpaPanduan[] = $name;
            }
        }

        $this->assertSame([], $tanpaPanduan, "Halaman tanpa panduan:\n".implode("\n", $tanpaPanduan));
    }

    public function test_setiap_route_hanya_terdaftar_sekali_dan_isinya_lengkap(): void
    {
        $sumber = (new \ReflectionClassConstant(Panduan::class, 'SUMBER'))->getValue();
        $terdaftar = [];
        $ganda = [];
        $cacat = [];

        foreach (['admin', 'wakil_kepala_sekolah', 'ketua_pkbm', 'guru_pengajar', 'siswa', null] as $role) {
            foreach ($sumber as $kelas) {
                foreach ($kelas::daftar($role) as $kunci => $isi) {
                    if (blank($isi['judul'] ?? null) || blank($isi['isi'] ?? null)) {
                        $cacat[] = $kunci;
                    }
                    if ($role !== 'admin') {
                        continue;
                    }
                    foreach (array_map('trim', explode(',', $kunci)) as $nama) {
                        if (isset($terdaftar[$nama])) {
                            $ganda[] = "$nama ($terdaftar[$nama] & $kelas)";
                        }
                        $terdaftar[$nama] = $kelas;
                        $this->assertTrue(Route::has($nama), "Route panduan tidak ada: $nama");
                    }
                }
            }
        }

        $this->assertSame([], $ganda, "Route terdaftar ganda:\n".implode("\n", $ganda));
        $this->assertSame([], array_unique($cacat), 'Panduan tanpa judul/isi: '.implode(', ', array_unique($cacat)));
    }
}
