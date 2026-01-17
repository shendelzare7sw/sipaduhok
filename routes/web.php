<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\MidtransWebhookController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\WaliKelasController as AdminWaliKelasController;
use App\Http\Controllers\Admin\GuruPengajarController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\CetakLaporanController;

// Ketua PKBM Controllers
use App\Http\Controllers\Ketua\KetuaController;

// Wakil Kepala Sekolah Controllers
use App\Http\Controllers\WakilKepalaSekolah\WakilKepalaSekolahController;
use App\Http\Controllers\WakilKepalaSekolah\TahunAjaranController as WakaTahunAjaranController;
use App\Http\Controllers\WakilKepalaSekolah\MataPelajaranController as WakaMataPelajaranController;
use App\Http\Controllers\WakilKepalaSekolah\KelasController as WakaKelasController;
use App\Http\Controllers\WakilKepalaSekolah\ManajemenSiswaController as WakaManajemenSiswaController;
use App\Http\Controllers\WakilKepalaSekolah\WaliKelasController as WakaWaliKelasController;
use App\Http\Controllers\WakilKepalaSekolah\JadwalPelajaranController as WakaJadwalPelajaranController;
use App\Http\Controllers\WakilKepalaSekolah\PengaturanIstirahatController as WakaPengaturanIstirahatController;

// Sekretaris Controllers
use App\Http\Controllers\Sekretaris\SekretarisController;

// Bendahara Controllers
use App\Http\Controllers\Bendahara\BendaharaController;
use App\Http\Controllers\Bendahara\TagihanController;
use App\Http\Controllers\Bendahara\PembayaranController;
use App\Http\Controllers\Bendahara\InfoPembayaranController;
use App\Http\Controllers\Bendahara\ValidasiAksesController as BendaharaValidasiAksesController;
use App\Http\Controllers\Bendahara\LaporanPembayaranController;

// Wali Kelas Controllers
use App\Http\Controllers\WaliKelas\WaliKelasController;
use App\Http\Controllers\WaliKelas\PilihKelasController;
use App\Http\Controllers\WaliKelas\JadwalPelajaranController;
use App\Http\Controllers\WaliKelas\PresensiController;
use App\Http\Controllers\WaliKelas\NilaiController as WaliKelasNilaiController;
use App\Http\Controllers\WaliKelas\RaporController;
use App\Http\Controllers\WaliKelas\ValidasiAksesController as WaliKelasValidasiAksesController;

// Guru Pengajar Controllers
use App\Http\Controllers\Guru\GuruKelasController;
use App\Http\Controllers\Guru\GuruLmsController;
use App\Http\Controllers\Guru\GuruMateriController;
use App\Http\Controllers\Guru\GuruTugasController;
use App\Http\Controllers\Guru\GuruKoreksiController;
use App\Http\Controllers\Guru\GuruUjianController;
use App\Http\Controllers\Guru\GuruNilaiController;
use App\Http\Controllers\Guru\GuruForumController;

// Siswa Controllers
use App\Http\Controllers\Siswa\SiswaDashboardController;
use App\Http\Controllers\Siswa\SiaDashboardController;
use App\Http\Controllers\Siswa\SiaPresensiController;
use App\Http\Controllers\Siswa\SiaPembayaranController;
use App\Http\Controllers\Siswa\SiaRaporController;
use App\Http\Controllers\Siswa\LmsDashboardController;

// Orang Tua Controllers
use App\Http\Controllers\OrangTua\OrangTuaController;
use App\Http\Controllers\Siswa\LmsMateriController;
use App\Http\Controllers\Siswa\LmsTugasController;
use App\Http\Controllers\Siswa\LmsUjianController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES - Accessible to everyone
|--------------------------------------------------------------------------
*/

// Midtrans Webhook (must be outside auth middleware)
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'notification'])->name('midtrans.notification');

// Homepage
Route::get('/', [\App\Http\Controllers\LandingPageController::class, 'home'])->name('home');

// Menu Profil
Route::get('/tentang-sekolah', [\App\Http\Controllers\LandingPageController::class, 'tentangSekolah']);

Route::get('/visi-misi', [\App\Http\Controllers\LandingPageController::class, 'visiMisi']);

Route::get('/struktur-organisasi', [\App\Http\Controllers\LandingPageController::class, 'strukturOrganisasi']);

Route::get('/profil-guru', [\App\Http\Controllers\LandingPageController::class, 'profilGuru']);

// Menu Program
Route::get('/program-paud-tk', [\App\Http\Controllers\LandingPageController::class, 'programPaudTk']);
Route::get('/program-sd-sma', [\App\Http\Controllers\LandingPageController::class, 'programSdSma']);
Route::get('/program-inklusi', [\App\Http\Controllers\LandingPageController::class, 'programInklusi']);
Route::get('/program-terapi', [\App\Http\Controllers\LandingPageController::class, 'programTerapi']);

// Other Public Pages
Route::get('/fasilitas', [\App\Http\Controllers\LandingPageController::class, 'fasilitas'])->name('fasilitas');

Route::get('/ppdb', [\App\Http\Controllers\LandingPageController::class, 'ppdb'])->name('ppdb');

Route::get('/galeri', [\App\Http\Controllers\LandingPageController::class, 'galeri'])->name('galeri');

Route::get('/kontak', [\App\Http\Controllers\LandingPageController::class, 'kontak'])->name('kontak');

// Berita Public Page
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - Require Authentication
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');

            // Tenaga Pendidik
            Route::get('/tenaga-pendidik/import', [UserController::class, 'importTenagaPendidikForm'])->name('import-tenaga-pendidik');
            Route::post('/tenaga-pendidik/import', [UserController::class, 'importTenagaPendidik'])->name('import-tenaga-pendidik.store');
            Route::get('/tenaga-pendidik/template', [UserController::class, 'downloadTenagaPendidikTemplate'])->name('tenaga-pendidik-template');
            Route::get('/tenaga-pendidik', [UserController::class, 'tenagaPendidik'])->name('tenaga-pendidik');
            Route::get('/tenaga-pendidik/create', [UserController::class, 'createTenagaPendidik'])->name('create-tenaga-pendidik');
            Route::post('/tenaga-pendidik', [UserController::class, 'storeTenagaPendidik'])->name('store-tenaga-pendidik');
            Route::get('/tenaga-pendidik/{id}/edit', [UserController::class, 'editTenagaPendidik'])->name('edit-tenaga-pendidik');
            Route::put('/tenaga-pendidik/{id}', [UserController::class, 'updateTenagaPendidik'])->name('update-tenaga-pendidik');
            Route::delete('/tenaga-pendidik/{id}', [UserController::class, 'deleteTenagaPendidik'])->name('delete-tenaga-pendidik');
            Route::get('/tenaga-pendidik/{id}', [UserController::class, 'showTenagaPendidik'])->name('show-tenaga-pendidik');

            // Siswa
            Route::get('/siswa/import', [UserController::class, 'importSiswaForm'])->name('import-siswa');
            Route::post('/siswa/import', [UserController::class, 'importSiswa'])->name('import-siswa.store');
            Route::get('/siswa/template', [UserController::class, 'downloadSiswaTemplate'])->name('siswa-template');
            Route::get('/siswa', [UserController::class, 'siswa'])->name('siswa');
            Route::get('/siswa/create', [UserController::class, 'createSiswa'])->name('create-siswa');
            Route::post('/siswa', [UserController::class, 'storeSiswa'])->name('store-siswa');
            Route::get('/siswa/{id}/edit', [UserController::class, 'editSiswa'])->name('edit-siswa');
            Route::put('/siswa/{id}', [UserController::class, 'updateSiswa'])->name('update-siswa');
            Route::delete('/siswa/{id}', [UserController::class, 'deleteSiswa'])->name('delete-siswa');
            Route::get('/siswa/{id}', [UserController::class, 'showSiswa'])->name('show-siswa');

            // Orang Tua
            Route::get('/orang-tua/import', [UserController::class, 'importOrangTuaForm'])->name('import-orang-tua');
            Route::post('/orang-tua/import', [UserController::class, 'importOrangTua'])->name('import-orang-tua.store');
            Route::get('/orang-tua/template', [UserController::class, 'downloadOrangTuaTemplate'])->name('orang-tua-template');
            Route::get('/orang-tua', [UserController::class, 'orangTua'])->name('orang-tua');
            Route::get('/orang-tua/create', [UserController::class, 'createOrangTua'])->name('orang-tua.create');
            Route::post('/orang-tua', [UserController::class, 'storeOrangTua'])->name('orang-tua.store');
            Route::get('/orang-tua/{id}', [UserController::class, 'showOrangTua'])->name('show-orang-tua');
            Route::get('/orang-tua/{id}/edit', [UserController::class, 'editOrangTua'])->name('edit-orang-tua');
            Route::put('/orang-tua/{id}', [UserController::class, 'updateOrangTua'])->name('update-orang-tua');
            Route::post('/orang-tua/{id}/toggle-status', [UserController::class, 'toggleOrangTuaStatus'])->name('toggle-orang-tua-status');
            Route::delete('/orang-tua/{id}', [UserController::class, 'deleteOrangTua'])->name('delete-orang-tua');
        });

        // Tahun Ajaran
        Route::resource('tahun-ajaran', TahunAjaranController::class);
        Route::post('tahun-ajaran/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');

        // Cabang
        Route::resource('cabang', CabangController::class);
        Route::post('cabang/{cabang}/toggle-status', [CabangController::class, 'toggleStatus'])->name('cabang.toggle-status');

        // Kelas
        Route::get('kelas/import', [KelasController::class, 'importForm'])->name('kelas.import');
        Route::post('kelas/import', [KelasController::class, 'import'])->name('kelas.import.store');
        Route::get('kelas/template', [KelasController::class, 'downloadTemplate'])->name('kelas.template');
        Route::get('kelas/print', [KelasController::class, 'printDaftarKelas'])->name('kelas.print');
        Route::get('kelas/{kelas}/manage-siswa', [KelasController::class, 'manageSiswa'])->name('kelas.manage-siswa');
        Route::post('kelas/{kelas}/add-siswa', [KelasController::class, 'addSiswa'])->name('kelas.add-siswa');
        Route::post('kelas/{kelas}/remove-siswa', [KelasController::class, 'removeSiswa'])->name('kelas.remove-siswa');
        Route::post('kelas/{kelas}/assign-wali', [KelasController::class, 'assignWaliKelas'])->name('kelas.assign-wali');
        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);

        // Wali Kelas
        Route::get('wali-kelas', [AdminWaliKelasController::class, 'index'])->name('wali-kelas.index');
        Route::get('wali-kelas/print', [AdminWaliKelasController::class, 'print'])->name('wali-kelas.print');
        Route::get('wali-kelas/{kelas}', [AdminWaliKelasController::class, 'show'])->name('wali-kelas.show');
        Route::post('wali-kelas/{kelas}/assign', [AdminWaliKelasController::class, 'assign'])->name('wali-kelas.assign');
        Route::post('wali-kelas/bulk-assign', [AdminWaliKelasController::class, 'bulkAssign'])->name('wali-kelas.bulk-assign');

        // Guru Pengajar
        Route::get('guru-pengajar', [GuruPengajarController::class, 'index'])->name('guru-pengajar.index');
        Route::get('guru-pengajar/print', [GuruPengajarController::class, 'print'])->name('guru-pengajar.print');
        Route::get('guru-pengajar/kelas/{kelas}', [GuruPengajarController::class, 'manageKelas'])->name('guru-pengajar.manage-kelas');
        Route::post('guru-pengajar/kelas/{kelas}/assign', [GuruPengajarController::class, 'assignToKelas'])->name('guru-pengajar.assign-to-kelas');
        Route::post('guru-pengajar/kelas/{kelas}/remove', [GuruPengajarController::class, 'removeFromKelas'])->name('guru-pengajar.remove-from-kelas');
        Route::get('guru-pengajar/{guruPengajar}', [GuruPengajarController::class, 'show'])->name('guru-pengajar.show');
        Route::post('guru-pengajar/{guruPengajar}/assign', [GuruPengajarController::class, 'assign'])->name('guru-pengajar.assign');
        Route::post('guru-pengajar/{guruPengajar}/remove-assignment', [GuruPengajarController::class, 'removeAssignment'])->name('guru-pengajar.remove-assignment');

        // Mata Pelajaran
        Route::get('mata-pelajaran/import', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'importForm'])->name('mata-pelajaran.import');
        Route::post('mata-pelajaran/import', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'import'])->name('mata-pelajaran.import.store');
        Route::get('mata-pelajaran/template', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'downloadTemplate'])->name('mata-pelajaran.template');
        Route::resource('mata-pelajaran', \App\Http\Controllers\Admin\MataPelajaranController::class);

        // Pengaturan Istirahat
        Route::prefix('pengaturan-istirahat')->name('pengaturan-istirahat.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [\App\Http\Controllers\Admin\PengaturanIstirahatController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Jadwal Pelajaran
        Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
            Route::get('/import', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'importForm'])->name('import');
            Route::post('/import', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'import'])->name('import.store');
            Route::get('/template', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'downloadTemplate'])->name('template');
            Route::get('/', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'store'])->name('store');
            Route::get('/kelas/{kelas}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'show'])->name('show');
            Route::get('/kelas/{kelas}/preview-print', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'previewPrint'])->name('preview-print');
            Route::get('/kelas/{kelas}/print', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'exportPdf'])->name('print');
            Route::get('/export-pdf', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'exportPdfAll'])->name('export-pdf');
            Route::get('/export-excel', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'exportExcel'])->name('export-excel');
            Route::post('/duplicate', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'duplicate'])->name('duplicate');
            Route::get('/{jadwalPelajaran}/edit', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'edit'])->name('edit');
            Route::put('/{jadwalPelajaran}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'update'])->name('update');
            Route::delete('/{jadwalPelajaran}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'destroy'])->name('destroy');
            Route::post('/{jadwalPelajaran}/ganti-guru', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'gantiGuru'])->name('ganti-guru');
            Route::post('/bulk-replace-guru', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'bulkReplaceGuru'])->name('bulk-replace-guru');
            Route::delete('/bulk-delete', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-update-status', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::get('/api/kelas/{kelas}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'getByKelas'])->name('api.by-kelas');
            Route::get('/api/guru/{guru}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'getByGuru'])->name('api.by-guru');
        });

        // Manajemen Siswa
        Route::get('manajemen-siswa', [ManajemenSiswaController::class, 'index'])->name('manajemen-siswa.index');
        Route::get('manajemen-siswa/print', [ManajemenSiswaController::class, 'print'])->name('manajemen-siswa.print');
        Route::post('manajemen-siswa/bulk-assign', [ManajemenSiswaController::class, 'bulkAssign'])->name('manajemen-siswa.bulk-assign');
        Route::get('manajemen-siswa/kelas/{kelas}', [ManajemenSiswaController::class, 'perKelas'])->name('manajemen-siswa.per-kelas');
        Route::post('manajemen-siswa/kelas/{kelas}/add', [ManajemenSiswaController::class, 'addToKelas'])->name('manajemen-siswa.add-to-kelas');
        Route::post('manajemen-siswa/kelas/{kelas}/remove', [ManajemenSiswaController::class, 'removeFromKelas'])->name('manajemen-siswa.remove-from-kelas');
        Route::get('manajemen-siswa/{siswa}', [ManajemenSiswaController::class, 'show'])->name('manajemen-siswa.show');
        Route::get('manajemen-siswa/{siswa}/print-kartu', [ManajemenSiswaController::class, 'printKartu'])->name('manajemen-siswa.print-kartu');
        Route::post('manajemen-siswa/{siswa}/assign-kelas', [ManajemenSiswaController::class, 'assignKelas'])->name('manajemen-siswa.assign-kelas');
        Route::post('manajemen-siswa/{siswa}/attach-parent', [ManajemenSiswaController::class, 'attachParent'])->name('manajemen-siswa.attach-parent');
        Route::delete('manajemen-siswa/{siswa}/detach-parent/{parent}', [ManajemenSiswaController::class, 'detachParent'])->name('manajemen-siswa.detach-parent');

        // Cetak Laporan
        Route::get('cetak-laporan', [CetakLaporanController::class, 'index'])->name('cetak-laporan.index');
        Route::get('cetak-laporan/siswa', [CetakLaporanController::class, 'siswa'])->name('cetak-laporan.siswa');
        Route::get('cetak-laporan/tenaga-pendidik', [CetakLaporanController::class, 'tenagaPendidik'])->name('cetak-laporan.tenaga-pendidik');
        Route::get('cetak-laporan/kelas', [CetakLaporanController::class, 'kelas'])->name('cetak-laporan.kelas');
        Route::get('cetak-laporan/wali-kelas', [CetakLaporanController::class, 'waliKelas'])->name('cetak-laporan.wali-kelas');
        Route::get('cetak-laporan/guru-pengajar', [CetakLaporanController::class, 'guruPengajar'])->name('cetak-laporan.guru-pengajar');
        Route::get('cetak-laporan/rekap', [CetakLaporanController::class, 'rekap'])->name('cetak-laporan.rekap');

        /*
        |--------------------------------------------------------------------------
        | ADMIN KEUANGAN (Copy of Bendahara features for admin access)
        |--------------------------------------------------------------------------
        */
        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            // Tagihan
            Route::prefix('tagihan')->name('tagihan.')->group(function () {
                Route::get('/import', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'importForm'])->name('import');
                Route::post('/import', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'import'])->name('import.store');
                Route::get('/template', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'downloadTemplate'])->name('template');
                Route::get('/', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'index'])->name('index');
                Route::get('/bulk-create', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'bulkCreate'])->name('bulk-create');
                Route::post('/bulk-create', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'bulkCreate'])->name('bulk-create.store');

                // Custom Tagihan
                Route::get('/create-custom', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'createCustom'])->name('create-custom');
                Route::post('/store-custom', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'storeCustom'])->name('store-custom');

                // Generate SPP
                Route::get('/generate-spp', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'generateSppForm'])->name('generate-spp');
                Route::post('/generate-spp', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'generateSpp'])->name('generate-spp.store');

                // Duplicate Tagihan
                Route::get('/duplicate', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'duplicateForm'])->name('duplicate');
                Route::post('/duplicate', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'duplicate'])->name('duplicate.store');

                // API Routes
                Route::get('/api/siswa-by-kelas/{kelas}', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'getSiswaByKelas'])->name('api.siswa-by-kelas');
                Route::get('/api/tagihan-preview/{siswa}', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'getTagihanPreview'])->name('api.tagihan-preview');

                Route::get('/{siswa}', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'show'])->name('show');
                Route::get('/{siswa}/edit', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'edit'])->name('edit');
                Route::put('/{siswa}', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'update'])->name('update');
                Route::delete('/{tagihan}/destroy-item', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'destroyItem'])->name('destroy-item');
                Route::get('/{siswa}/cetak', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'cetak'])->name('cetak');
            });

            // Pembayaran
            Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'index'])->name('index');
                Route::get('/{pembayaran}', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'show'])->name('show')->where('pembayaran', '[0-9]+');
                Route::post('/{pembayaran}/validasi', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'validasi'])->name('validasi');
                Route::get('/siswa/{siswa}/create', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'create'])->name('create');
                Route::post('/siswa/{siswa}', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'store'])->name('store');
                Route::post('/siswa/{siswa}/validasi-langsung', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'validasiLangsung'])->name('validasi-langsung');
                Route::get('/riwayat/{siswa}', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'riwayatSiswa'])->name('riwayat-siswa');
            });

            // Laporan Keuangan
            Route::prefix('laporan')->name('laporan.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Keuangan\LaporanPembayaranController::class, 'index'])->name('index');
                Route::get('/cetak', [\App\Http\Controllers\Admin\Keuangan\LaporanPembayaranController::class, 'cetak'])->name('cetak');
                Route::get('/rekap-tagihan', [\App\Http\Controllers\Admin\Keuangan\LaporanPembayaranController::class, 'rekapTagihan'])->name('rekap-tagihan');
                Route::get('/cetak-rekap-tagihan', [\App\Http\Controllers\Admin\Keuangan\LaporanPembayaranController::class, 'cetakRekapTagihan'])->name('cetak-rekap-tagihan');
                Route::get('/belum-lunas', [\App\Http\Controllers\Admin\Keuangan\LaporanPembayaranController::class, 'belumLunas'])->name('belum-lunas');
                Route::get('/cetak-belum-lunas', [\App\Http\Controllers\Admin\Keuangan\LaporanPembayaranController::class, 'cetakBelumLunas'])->name('cetak-belum-lunas');
            });

            // Validasi Akses
            Route::prefix('validasi-akses')->name('validasi-akses.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'index'])->name('index');

                // Validasi individual
                Route::post('/{siswa}/validasi-ujian', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'validasiUjian'])->name('validasi-ujian');
                Route::post('/{siswa}/batalkan-ujian', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'batalkanUjian'])->name('batalkan-ujian');
                Route::post('/{siswa}/validasi-rapor', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'validasiRapor'])->name('validasi-rapor');
                Route::post('/{siswa}/batalkan-rapor', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'batalkanRapor'])->name('batalkan-rapor');

                // Bulk validasi per kelas
                Route::post('/kelas/{kelas}/bulk-validasi-ujian', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'bulkValidasiUjian'])->name('bulk-validasi-ujian');
                Route::post('/kelas/{kelas}/bulk-validasi-rapor', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'bulkValidasiRapor'])->name('bulk-validasi-rapor');

                // Bulk validasi siswa terpilih
                Route::post('/bulk-validasi-selected', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'bulkValidasiSelected'])->name('bulk-validasi-selected');

                // Reset validasi
                Route::post('/reset', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'resetValidasi'])->name('reset');
            });

            // Info Pembayaran (API Midtrans & Rekening Bank)
            Route::prefix('info-pembayaran')->name('info-pembayaran.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Keuangan\InfoPembayaranController::class, 'index'])->name('index');
                Route::post('/update', [\App\Http\Controllers\Admin\Keuangan\InfoPembayaranController::class, 'update'])->name('update');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | ADMIN AKADEMIK (Copy of Sekretaris features for admin access)
        |--------------------------------------------------------------------------
        */
        Route::prefix('akademik')->name('akademik.')->group(function () {
            $controller = \App\Http\Controllers\Admin\Akademik\AkademikController::class;

            // Kalender Akademik
            Route::prefix('kalender')->name('kalender.')->group(function () use ($controller) {
                Route::get('/', [$controller, 'kalenderIndex'])->name('index');
                Route::get('/bulanan', [$controller, 'kalenderBulanan'])->name('bulanan');
                Route::get('/cetak', [$controller, 'kalenderCetak'])->name('cetak');
                Route::get('/create', [$controller, 'kalenderCreate'])->name('create');
                Route::post('/', [$controller, 'kalenderStore'])->name('store');
                Route::get('/{id}/edit', [$controller, 'kalenderEdit'])->name('edit');
                Route::put('/{id}', [$controller, 'kalenderUpdate'])->name('update');
                Route::delete('/{id}', [$controller, 'kalenderDestroy'])->name('destroy');
            });

            // Pengumuman
            Route::prefix('pengumuman')->name('pengumuman.')->group(function () use ($controller) {
                Route::get('/', [$controller, 'pengumumanIndex'])->name('index');
                Route::get('/create', [$controller, 'pengumumanCreate'])->name('create');
                Route::post('/', [$controller, 'pengumumanStore'])->name('store');
                Route::get('/{id}/edit', [$controller, 'pengumumanEdit'])->name('edit');
                Route::put('/{id}', [$controller, 'pengumumanUpdate'])->name('update');
                Route::delete('/{id}', [$controller, 'pengumumanDestroy'])->name('destroy');
            });

            // Berita
            Route::prefix('berita')->name('berita.')->group(function () use ($controller) {
                Route::get('/', [$controller, 'beritaIndex'])->name('index');
                Route::get('/create', [$controller, 'beritaCreate'])->name('create');
                Route::post('/', [$controller, 'beritaStore'])->name('store');
                Route::get('/{id}/edit', [$controller, 'beritaEdit'])->name('edit');
                Route::put('/{id}', [$controller, 'beritaUpdate'])->name('update');
                Route::delete('/{id}', [$controller, 'beritaDestroy'])->name('destroy');
                Route::post('/{id}/toggle-featured', [$controller, 'beritaToggleFeatured'])->name('toggle-featured');
            });

            // Flyer
            Route::prefix('flyer')->name('flyer.')->group(function () use ($controller) {
                Route::get('/', [$controller, 'flyerIndex'])->name('index');
                Route::get('/create', [$controller, 'flyerCreate'])->name('create');
                Route::post('/', [$controller, 'flyerStore'])->name('store');
                Route::get('/{id}/edit', [$controller, 'flyerEdit'])->name('edit');
                Route::put('/{id}', [$controller, 'flyerUpdate'])->name('update');
                Route::delete('/{id}', [$controller, 'flyerDestroy'])->name('destroy');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | LANDING PAGE MANAGEMENT
        |--------------------------------------------------------------------------
        */
        Route::prefix('landing-pages')->name('landing-pages.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LandingPage\LandingPageController::class, 'index'])->name('index');
            Route::get('/{landingPage:slug}/edit', [\App\Http\Controllers\Admin\LandingPage\LandingPageController::class, 'edit'])->name('edit');
            Route::put('/{landingPage:slug}', [\App\Http\Controllers\Admin\LandingPage\LandingPageController::class, 'update'])->name('update');
            Route::get('/{landingPage:slug}/reset', [\App\Http\Controllers\Admin\LandingPage\LandingPageController::class, 'reset'])->name('reset');
        });

        /*
        |--------------------------------------------------------------------------
        | ADMIN MONITORING & LAPORAN (Copy of Ketua features for admin access)
        |--------------------------------------------------------------------------
        */
        $monitoringController = \App\Http\Controllers\Admin\MonitoringController::class;

        // Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () use ($monitoringController) {
            Route::get('/pengguna', [$monitoringController, 'monitoringPengguna'])->name('pengguna');
            Route::get('/wali-kelas', [$monitoringController, 'monitoringWaliKelas'])->name('wali-kelas');
            Route::get('/guru-pengajar', [$monitoringController, 'monitoringGuruPengajar'])->name('guru-pengajar');
            Route::get('/siswa', [$monitoringController, 'monitoringSiswa'])->name('siswa');
        });

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () use ($monitoringController) {
            Route::get('/', [$monitoringController, 'index'])->name('index');
            Route::get('/cetak-siswa', [$monitoringController, 'siswa'])->name('siswa');
            Route::get('/cetak-tenaga-pendidik', [$monitoringController, 'tenagaPendidik'])->name('tenaga-pendidik');
            Route::get('/cetak-kelas', [$monitoringController, 'kelas'])->name('kelas');
            Route::get('/cetak-wali-kelas', [$monitoringController, 'waliKelas'])->name('wali-kelas');
            Route::get('/cetak-guru-pengajar', [$monitoringController, 'guruPengajar'])->name('guru-pengajar');
            Route::get('/cetak-rekap', [$monitoringController, 'rekap'])->name('rekap');
        });

        // Catatan
        Route::prefix('catatan')->name('catatan.')->group(function () use ($monitoringController) {
            Route::get('/', [$monitoringController, 'catatanIndex'])->name('index');
            Route::get('/create', [$monitoringController, 'catatanCreate'])->name('create');
            Route::post('/', [$monitoringController, 'catatanStore'])->name('store');
            Route::get('/{id}', [$monitoringController, 'catatanShow'])->name('show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | KETUA PKBM DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:ketua_pkbm'])->prefix('ketua')->name('ketua.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'ketua'])->name('dashboard');

        // Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/pengguna', [KetuaController::class, 'monitoringPengguna'])->name('pengguna');
            Route::get('/wali-kelas', [KetuaController::class, 'monitoringWaliKelas'])->name('wali-kelas');
            Route::get('/guru-pengajar', [KetuaController::class, 'monitoringGuruPengajar'])->name('guru-pengajar');
            Route::get('/siswa', [KetuaController::class, 'monitoringSiswa'])->name('siswa');
        });

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [KetuaController::class, 'index'])->name('index');
            Route::get('/cetak-siswa', [KetuaController::class, 'siswa'])->name('siswa');
            Route::get('/cetak-tenaga-pendidik', [KetuaController::class, 'tenagaPendidik'])->name('tenaga-pendidik');
            Route::get('/cetak-kelas', [KetuaController::class, 'kelas'])->name('kelas');
            Route::get('/cetak-wali-kelas', [KetuaController::class, 'waliKelas'])->name('wali-kelas');
            Route::get('/cetak-guru-pengajar', [KetuaController::class, 'guruPengajar'])->name('guru-pengajar');
            Route::get('/cetak-rekap', [KetuaController::class, 'rekap'])->name('rekap');
        });

        // Catatan
        Route::prefix('catatan')->name('catatan.')->group(function () {
            Route::get('/', [KetuaController::class, 'catatanIndex'])->name('index');
            Route::get('/create', [KetuaController::class, 'catatanCreate'])->name('create');
            Route::post('/', [KetuaController::class, 'catatanStore'])->name('store');
            Route::get('/{id}', [KetuaController::class, 'catatanShow'])->name('show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | WAKIL KEPALA SEKOLAH DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:wakil_kepala_sekolah'])->prefix('waka')->name('waka.')->group(function () {
        Route::get('/dashboard', [WakilKepalaSekolahController::class, 'dashboard'])->name('dashboard');

        // Tahun Ajaran
        Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
            Route::get('/', [WakaTahunAjaranController::class, 'index'])->name('index');
            Route::get('/create', [WakaTahunAjaranController::class, 'create'])->name('create');
            Route::post('/', [WakaTahunAjaranController::class, 'store'])->name('store');
            Route::get('/{tahunAjaran}', [WakaTahunAjaranController::class, 'show'])->name('show');
            Route::get('/{tahunAjaran}/edit', [WakaTahunAjaranController::class, 'edit'])->name('edit');
            Route::put('/{tahunAjaran}', [WakaTahunAjaranController::class, 'update'])->name('update');
            Route::delete('/{tahunAjaran}', [WakaTahunAjaranController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-active', [WakaTahunAjaranController::class, 'toggleActive'])->name('toggle-active');
        });

        // Mata Pelajaran
        Route::get('mata-pelajaran/import', [WakaMataPelajaranController::class, 'import'])->name('mata-pelajaran.import');
        Route::post('mata-pelajaran/import', [WakaMataPelajaranController::class, 'importStore'])->name('mata-pelajaran.import.store');
        Route::get('mata-pelajaran/template', [WakaMataPelajaranController::class, 'downloadTemplate'])->name('mata-pelajaran.template');
        Route::resource('mata-pelajaran', WakaMataPelajaranController::class);

        // Kelas
        Route::get('kelas/import', [WakaKelasController::class, 'import'])->name('kelas.import');
        Route::post('kelas/import', [WakaKelasController::class, 'importStore'])->name('kelas.import.store');
        Route::get('kelas/template', [WakaKelasController::class, 'downloadTemplate'])->name('kelas.template');
        Route::get('/kelas/print', [WakaKelasController::class, 'print'])->name('kelas.print');
        Route::get('/kelas/{kelas}/manage-siswa', [WakaKelasController::class, 'manageSiswa'])->name('kelas.manage-siswa');
        Route::post('/kelas/{kelas}/add-siswa', [WakaKelasController::class, 'addSiswa'])->name('kelas.add-siswa');
        Route::post('/kelas/{kelas}/remove-siswa', [WakaKelasController::class, 'removeSiswa'])->name('kelas.remove-siswa');
        Route::resource('kelas', WakaKelasController::class)->parameters(['kelas' => 'kelas']);

        // Manajemen Siswa
        Route::prefix('manajemen-siswa')->name('manajemen-siswa.')->group(function () {

            Route::get('/', [WakaManajemenSiswaController::class, 'index'])->name('index');
            Route::get('/print', [WakaManajemenSiswaController::class, 'print'])->name('print');
            Route::get('/kelas/{kelas}', [WakaManajemenSiswaController::class, 'perKelas'])->name('per-kelas');
            Route::post('/kelas/{kelas}/add-siswa', [WakaManajemenSiswaController::class, 'addToKelas'])->name('add-to-kelas');
            Route::post('/kelas/{kelas}/remove-siswa', [WakaManajemenSiswaController::class, 'removeFromKelas'])->name('remove-from-kelas');
            Route::post('/{siswa}/assign-kelas', [WakaManajemenSiswaController::class, 'assignKelas'])->name('assign-kelas');
            Route::post('/{siswa}/attach-parent', [WakaManajemenSiswaController::class, 'attachParent'])->name('attach-parent');
            Route::delete('/{siswa}/detach-parent/{parent}', [WakaManajemenSiswaController::class, 'detachParent'])->name('detach-parent');
            Route::get('/{siswa}', [WakaManajemenSiswaController::class, 'show'])->name('show');
            Route::get('/{siswa}/print-kartu', [WakaManajemenSiswaController::class, 'printKartu'])->name('print-kartu');
        });

        // Wali Kelas
        Route::prefix('wali-kelas')->name('wali-kelas.')->group(function () {
            Route::get('/', [WakaWaliKelasController::class, 'index'])->name('index');
            Route::get('/print', [WakaWaliKelasController::class, 'print'])->name('print');
            Route::post('/{kelasId}/assign', [WakaWaliKelasController::class, 'assign'])->name('assign');
            Route::get('/{kelas}', [WakaWaliKelasController::class, 'show'])->name('show');
        });

        // Jadwal Pelajaran
        Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
            Route::get('/import', [WakaJadwalPelajaranController::class, 'import'])->name('import');
            Route::post('/import', [WakaJadwalPelajaranController::class, 'importStore'])->name('import.store');
            Route::get('/template', [WakaJadwalPelajaranController::class, 'downloadTemplate'])->name('template');
            Route::get('/', [WakaJadwalPelajaranController::class, 'index'])->name('index');
            Route::get('/create', [WakaJadwalPelajaranController::class, 'create'])->name('create');
            Route::post('/', [WakaJadwalPelajaranController::class, 'store'])->name('store');
            Route::get('/print', [WakaJadwalPelajaranController::class, 'print'])->name('print');
            Route::get('/export-pdf', [WakaJadwalPelajaranController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/export-excel', [WakaJadwalPelajaranController::class, 'exportExcel'])->name('export-excel');
            Route::post('/duplicate', [WakaJadwalPelajaranController::class, 'duplicate'])->name('duplicate');
            Route::post('/bulk-replace-guru', [WakaJadwalPelajaranController::class, 'bulkReplaceGuru'])->name('bulk-replace-guru');
            Route::delete('/bulk-delete', [WakaJadwalPelajaranController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/{jadwalPelajaran}', [WakaJadwalPelajaranController::class, 'show'])->name('show');
            Route::get('/{jadwalPelajaran}/edit', [WakaJadwalPelajaranController::class, 'edit'])->name('edit');
            Route::put('/{jadwalPelajaran}', [WakaJadwalPelajaranController::class, 'update'])->name('update');
            Route::delete('/{jadwalPelajaran}', [WakaJadwalPelajaranController::class, 'destroy'])->name('destroy');
            Route::post('/{jadwalPelajaran}/ganti-guru', [WakaJadwalPelajaranController::class, 'gantiGuru'])->name('ganti-guru');
        });

        // Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/wali-kelas', [WakilKepalaSekolahController::class, 'monitoringWaliKelas'])->name('wali-kelas');
            Route::get('/guru-pengajar', [WakilKepalaSekolahController::class, 'monitoringGuruPengajar'])->name('guru-pengajar');
            Route::get('/siswa', [WakilKepalaSekolahController::class, 'monitoringSiswa'])->name('siswa');
        });

        // Catatan / Teguran
        Route::prefix('catatan')->name('catatan.')->group(function () {
            Route::get('/', [WakilKepalaSekolahController::class, 'catatanIndex'])->name('index');
            Route::get('/create', [WakilKepalaSekolahController::class, 'catatanCreate'])->name('create');
            Route::post('/', [WakilKepalaSekolahController::class, 'catatanStore'])->name('store');
            Route::get('/{id}', [WakilKepalaSekolahController::class, 'catatanShow'])->name('show');
        });

        // Pengaturan Istirahat
        Route::prefix('pengaturan-istirahat')->name('pengaturan-istirahat.')->group(function () {
            Route::get('/', [WakaPengaturanIstirahatController::class, 'index'])->name('index');
            Route::get('/create', [WakaPengaturanIstirahatController::class, 'create'])->name('create');
            Route::post('/', [WakaPengaturanIstirahatController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [WakaPengaturanIstirahatController::class, 'edit'])->name('edit');
            Route::put('/{id}', [WakaPengaturanIstirahatController::class, 'update'])->name('update');
            Route::delete('/{id}', [WakaPengaturanIstirahatController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [WakaPengaturanIstirahatController::class, 'toggleStatus'])->name('toggle-status');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SEKRETARIS DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:sekretaris'])->prefix('sekretaris')->name('sekretaris.')->group(function () {
        Route::get('/dashboard', [SekretarisController::class, 'dashboard'])->name('dashboard');

        // Kalender Akademik
        Route::prefix('kalender')->name('kalender.')->group(function () {
            Route::get('/', [SekretarisController::class, 'kalenderIndex'])->name('index');
            Route::get('/bulanan', [SekretarisController::class, 'kalenderBulanan'])->name('bulanan');
            Route::get('/cetak', [SekretarisController::class, 'kalenderCetak'])->name('cetak');
            Route::get('/create', [SekretarisController::class, 'kalenderCreate'])->name('create');
            Route::post('/', [SekretarisController::class, 'kalenderStore'])->name('store');
            Route::get('/{id}/edit', [SekretarisController::class, 'kalenderEdit'])->name('edit');
            Route::put('/{id}', [SekretarisController::class, 'kalenderUpdate'])->name('update');
            Route::delete('/{id}', [SekretarisController::class, 'kalenderDestroy'])->name('destroy');
        });

        // Pengumuman
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            Route::get('/', [SekretarisController::class, 'pengumumanIndex'])->name('index');
            Route::get('/create', [SekretarisController::class, 'pengumumanCreate'])->name('create');
            Route::post('/', [SekretarisController::class, 'pengumumanStore'])->name('store');
            Route::get('/{id}/edit', [SekretarisController::class, 'pengumumanEdit'])->name('edit');
            Route::put('/{id}', [SekretarisController::class, 'pengumumanUpdate'])->name('update');
            Route::delete('/{id}', [SekretarisController::class, 'pengumumanDestroy'])->name('destroy');
        });

        // Flyer
        Route::prefix('flyer')->name('flyer.')->group(function () {
            Route::get('/', [SekretarisController::class, 'flyerIndex'])->name('index');
            Route::get('/create', [SekretarisController::class, 'flyerCreate'])->name('create');
            Route::post('/', [SekretarisController::class, 'flyerStore'])->name('store');
            Route::get('/{id}/edit', [SekretarisController::class, 'flyerEdit'])->name('edit');
            Route::put('/{id}', [SekretarisController::class, 'flyerUpdate'])->name('update');
            Route::delete('/{id}', [SekretarisController::class, 'flyerDestroy'])->name('destroy');
        });

        // Berita Management
        Route::prefix('berita')->name('berita.')->group(function () {
            Route::get('/', [SekretarisController::class, 'beritaIndex'])->name('index');
            Route::get('/create', [SekretarisController::class, 'beritaCreate'])->name('create');
            Route::post('/', [SekretarisController::class, 'beritaStore'])->name('store');
            Route::get('/{id}/edit', [SekretarisController::class, 'beritaEdit'])->name('edit');
            Route::put('/{id}', [SekretarisController::class, 'beritaUpdate'])->name('update');
            Route::delete('/{id}', [SekretarisController::class, 'beritaDestroy'])->name('destroy');
            Route::post('/{id}/toggle-featured', [SekretarisController::class, 'beritaToggleFeatured'])->name('toggle-featured');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | BENDAHARA DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [BendaharaController::class, 'dashboard'])->name('dashboard');

        // Tagihan
        Route::prefix('tagihan')->name('tagihan.')->group(function () {
            Route::get('/', [TagihanController::class, 'index'])->name('index');
            Route::get('/bulk-create', [TagihanController::class, 'bulkCreate'])->name('bulk-create');
            Route::post('/bulk-create', [TagihanController::class, 'bulkCreate'])->name('bulk-create.store');

            // Custom Tagihan
            Route::get('/create-custom', [TagihanController::class, 'createCustom'])->name('create-custom');
            Route::post('/store-custom', [TagihanController::class, 'storeCustom'])->name('store-custom');

            // Generate SPP
            Route::get('/generate-spp', [TagihanController::class, 'generateSppForm'])->name('generate-spp');
            Route::post('/generate-spp', [TagihanController::class, 'generateSpp'])->name('generate-spp.store');

            // Duplicate Tagihan
            Route::get('/duplicate', [TagihanController::class, 'duplicateForm'])->name('duplicate');
            Route::post('/duplicate', [TagihanController::class, 'duplicate'])->name('duplicate.store');

            // API Routes
            Route::get('/api/siswa-by-kelas/{kelas}', [TagihanController::class, 'getSiswaByKelas'])->name('api.siswa-by-kelas');
            Route::get('/api/tagihan-preview/{siswa}', [TagihanController::class, 'getTagihanPreview'])->name('api.tagihan-preview');

            Route::get('/{siswa}', [TagihanController::class, 'show'])->name('show');
            Route::get('/{siswa}/edit', [TagihanController::class, 'edit'])->name('edit');
            Route::put('/{siswa}', [TagihanController::class, 'update'])->name('update');
            Route::delete('/{tagihan}/destroy-item', [TagihanController::class, 'destroyItem'])->name('destroy-item');
            Route::get('/{siswa}/cetak', [TagihanController::class, 'cetak'])->name('cetak');
        });

        // Pembayaran
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', [PembayaranController::class, 'index'])->name('index');
            Route::get('/{pembayaran}', [PembayaranController::class, 'show'])->name('show');
            Route::post('/{pembayaran}/validasi', [PembayaranController::class, 'validasi'])->name('validasi');

            // Input pembayaran manual
            Route::get('/siswa/{siswa}/create', [PembayaranController::class, 'create'])->name('create');
            Route::post('/siswa/{siswa}', [PembayaranController::class, 'store'])->name('store');
            Route::post('/siswa/{siswa}/validasi-langsung', [PembayaranController::class, 'validasiLangsung'])->name('validasi-langsung');

            // Riwayat per siswa
            Route::get('/riwayat/{siswa}', [PembayaranController::class, 'riwayatSiswa'])->name('riwayat-siswa');
        });

        // Info Pembayaran
        Route::prefix('info-pembayaran')->name('info-pembayaran.')->group(function () {
            Route::get('/', [InfoPembayaranController::class, 'index'])->name('index');
            Route::post('/update', [InfoPembayaranController::class, 'update'])->name('update');
        });

        // Validasi Akses
        Route::prefix('validasi-akses')->name('validasi-akses.')->group(function () {
            Route::get('/', [BendaharaValidasiAksesController::class, 'index'])->name('index');

            // Validasi individual
            Route::post('/{siswa}/validasi-ujian', [BendaharaValidasiAksesController::class, 'validasiUjian'])->name('validasi-ujian');
            Route::post('/{siswa}/batalkan-ujian', [BendaharaValidasiAksesController::class, 'batalkanUjian'])->name('batalkan-ujian');
            Route::post('/{siswa}/validasi-rapor', [BendaharaValidasiAksesController::class, 'validasiRapor'])->name('validasi-rapor');
            Route::post('/{siswa}/batalkan-rapor', [BendaharaValidasiAksesController::class, 'batalkanRapor'])->name('batalkan-rapor');

            // Bulk validasi per kelas
            Route::post('/kelas/{kelas}/bulk-validasi-ujian', [BendaharaValidasiAksesController::class, 'bulkValidasiUjian'])->name('bulk-validasi-ujian');
            Route::post('/kelas/{kelas}/bulk-validasi-rapor', [BendaharaValidasiAksesController::class, 'bulkValidasiRapor'])->name('bulk-validasi-rapor');

            // Bulk validasi siswa terpilih
            Route::post('/bulk-validasi-selected', [BendaharaValidasiAksesController::class, 'bulkValidasiSelected'])->name('bulk-validasi-selected');

            // Reset validasi
            Route::post('/reset', [BendaharaValidasiAksesController::class, 'resetValidasi'])->name('reset');
        });

        // Laporan Pembayaran
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanPembayaranController::class, 'index'])->name('index');
            Route::get('/cetak', [LaporanPembayaranController::class, 'cetak'])->name('cetak');
            Route::get('/rekap-tagihan', [LaporanPembayaranController::class, 'rekapTagihan'])->name('rekap-tagihan');
            Route::get('/cetak-rekap-tagihan', [LaporanPembayaranController::class, 'cetakRekapTagihan'])->name('cetak-rekap-tagihan');
            Route::get('/belum-lunas', [LaporanPembayaranController::class, 'belumLunas'])->name('belum-lunas');
            Route::get('/cetak-belum-lunas', [LaporanPembayaranController::class, 'cetakBelumLunas'])->name('cetak-belum-lunas');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | WALI KELAS DASHBOARD & ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:wali_kelas'])->prefix('wali')->name('wali.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [WaliKelasController::class, 'dashboard'])->name('dashboard');

        // Pilih Kelas (untuk wali kelas yang memegang lebih dari 1 kelas)
        Route::get('/pilih-kelas', [PilihKelasController::class, 'index'])->name('pilih-kelas');
        Route::post('/pilih-kelas/{kelas}', [PilihKelasController::class, 'select'])->name('pilih-kelas.select');

        // Jadwal Pelajaran (READ-ONLY - data dikelola oleh Admin)
        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('/', [JadwalPelajaranController::class, 'index'])->name('index');
            Route::get('/print', [JadwalPelajaranController::class, 'print'])->name('print');
        });

        // Backward compatibility alias untuk route lama
        Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran');

        // Presensi
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [PresensiController::class, 'index'])->name('index');
            Route::post('/update', [PresensiController::class, 'updatePresensi'])->name('update');
            Route::post('/input-harian', [PresensiController::class, 'inputHarian'])->name('input-harian');
            Route::get('/validasi-izin', [PresensiController::class, 'validasiIzin'])->name('validasi-izin');
            Route::post('/validasi-izin/{id}', [PresensiController::class, 'prosesValidasiIzin'])->name('proses-validasi-izin');
            Route::get('/print-rekap', [PresensiController::class, 'printRekap'])->name('print-rekap');
        });

        // Nilai Siswa
        Route::prefix('nilai')->name('nilai.')->group(function () {
            Route::get('/', [WaliKelasNilaiController::class, 'index'])->name('index');
            Route::get('/print', [WaliKelasNilaiController::class, 'print'])->name('print');
            Route::get('/{siswa}', [WaliKelasNilaiController::class, 'show'])->name('show');
            Route::get('/{siswa}/edit', [WaliKelasNilaiController::class, 'edit'])->name('edit');
            Route::put('/{siswa}', [WaliKelasNilaiController::class, 'update'])->name('update');
        });

        // Rapor
        Route::prefix('rapor')->name('rapor.')->group(function () {
            Route::get('/', [RaporController::class, 'index'])->name('index');
            Route::post('/generate-all', [RaporController::class, 'generateAll'])->name('generate-all');
            Route::get('/{rapor}/edit', [RaporController::class, 'edit'])->name('edit');
            Route::put('/{rapor}', [RaporController::class, 'update'])->name('update');
            Route::post('/{rapor}/terbitkan', [RaporController::class, 'terbitkan'])->name('terbitkan');
            Route::get('/{rapor}/preview', [RaporController::class, 'preview'])->name('preview');
            Route::get('/{rapor}/print', [RaporController::class, 'print'])->name('print');
        });

        // Validasi Akses
        Route::prefix('validasi-akses')->name('validasi-akses.')->group(function () {
            Route::get('/', [WaliKelasValidasiAksesController::class, 'index'])->name('index');

            // Validasi Ujian
            Route::post('/{siswa}/validasi-ujian', [WaliKelasValidasiAksesController::class, 'validasiUjian'])->name('validasi-ujian');
            Route::post('/{siswa}/batalkan-ujian', [WaliKelasValidasiAksesController::class, 'batalkanUjian'])->name('batalkan-ujian');
            Route::post('/bulk-validasi-ujian', [WaliKelasValidasiAksesController::class, 'bulkValidasiUjian'])->name('bulk-validasi-ujian');
            Route::post('/validasi-semua-ujian', [WaliKelasValidasiAksesController::class, 'validasiSemuaUjian'])->name('validasi-semua-ujian');

            // Validasi Rapor
            Route::post('/{siswa}/validasi-rapor', [WaliKelasValidasiAksesController::class, 'validasiRapor'])->name('validasi-rapor');
            Route::post('/{siswa}/batalkan-rapor', [WaliKelasValidasiAksesController::class, 'batalkanRapor'])->name('batalkan-rapor');
            Route::post('/bulk-validasi-rapor', [WaliKelasValidasiAksesController::class, 'bulkValidasiRapor'])->name('bulk-validasi-rapor');
            Route::post('/validasi-semua-rapor', [WaliKelasValidasiAksesController::class, 'validasiSemuaRapor'])->name('validasi-semua-rapor');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | GURU PENGAJAR DASHBOARD & ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:guru_pengajar'])->prefix('guru')->name('guru.')->group(function () {

        // Dashboard Overview
        Route::get('/dashboard', [DashboardController::class, 'guru'])->name('dashboard');

        // Daftar Kelas & Mata Pelajaran yang Diajar
        Route::get('/kelas', [GuruKelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/{kelas}/mapel', [GuruKelasController::class, 'showMapel'])->name('kelas.mapel');

        /*
        |--------------------------------------------------------------------------
        | LMS GURU - Per Kelas & Mata Pelajaran
        |--------------------------------------------------------------------------
        */
        Route::prefix('lms/{kelas}/{mapel}')->name('lms.')->group(function () {

            // Dashboard LMS (Beranda)
            Route::get('/dashboard', [GuruLmsController::class, 'dashboard'])->name('dashboard');

            // Materi
            Route::prefix('materi')->name('materi.')->group(function () {
                Route::get('/', [GuruMateriController::class, 'index'])->name('index');
                Route::get('/create', [GuruMateriController::class, 'create'])->name('create');
                Route::post('/', [GuruMateriController::class, 'store'])->name('store');
                Route::get('/{materi}/edit', [GuruMateriController::class, 'edit'])->name('edit');
                Route::put('/{materi}', [GuruMateriController::class, 'update'])->name('update');
                Route::delete('/{materi}', [GuruMateriController::class, 'destroy'])->name('destroy');
            });

            // Tugas & Latihan
            Route::prefix('tugas')->name('tugas.')->group(function () {
                Route::get('/', [GuruTugasController::class, 'index'])->name('index');
                Route::get('/create', [GuruTugasController::class, 'create'])->name('create');
                Route::post('/', [GuruTugasController::class, 'store'])->name('store');
                Route::get('/{tugas}/edit', [GuruTugasController::class, 'edit'])->name('edit');
                Route::put('/{tugas}', [GuruTugasController::class, 'update'])->name('update');
                Route::delete('/{tugas}', [GuruTugasController::class, 'destroy'])->name('destroy');

                // Koreksi Tugas
                Route::get('/{tugas}/koreksi', [GuruKoreksiController::class, 'index'])->name('koreksi');
                Route::get('/{tugas}/koreksi/{tugasSiswa}', [GuruKoreksiController::class, 'show'])->name('koreksi.show');
                Route::post('/{tugas}/koreksi/{tugasSiswa}', [GuruKoreksiController::class, 'store'])->name('koreksi.store');
            });

            // Ujian
            Route::prefix('ujian')->name('ujian.')->group(function () {
                Route::get('/', [GuruUjianController::class, 'index'])->name('index');
                Route::get('/create', [GuruUjianController::class, 'create'])->name('create');
                Route::post('/', [GuruUjianController::class, 'store'])->name('store');
                Route::get('/{ujian}/edit', [GuruUjianController::class, 'edit'])->name('edit');
                Route::put('/{ujian}', [GuruUjianController::class, 'update'])->name('update');
                Route::delete('/{ujian}', [GuruUjianController::class, 'destroy'])->name('destroy');

                // Hasil & Koreksi Ujian
                Route::get('/{ujian}/hasil', [GuruUjianController::class, 'hasil'])->name('hasil');
                Route::post('/{ujian}/koreksi/{ujianSiswa}', [GuruUjianController::class, 'koreksi'])->name('koreksi');
            });

            // Nilai Siswa
            Route::get('/nilai', [GuruNilaiController::class, 'index'])->name('nilai.index');
            Route::post('/nilai/update', [GuruNilaiController::class, 'update'])->name('nilai.update');

            // Forum Diskusi (Fase 2 - placeholder)
            Route::get('/forum', [GuruForumController::class, 'index'])->name('forum.index');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SISWA DASHBOARD & ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

        // Main Dashboard Router
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | SIA (Sistem Informasi Akademik) Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('sia')->name('sia.')->group(function () {

            // Dashboard SIA
            Route::get('/dashboard', [SiaDashboardController::class, 'index'])->name('dashboard');

            // Presensi
            Route::prefix('presensi')->name('presensi.')->group(function () {
                Route::get('/', [SiaPresensiController::class, 'index'])->name('index');

                // Note: Routes ajukan izin di-disable - Fitur dipindahkan ke Orang Tua
                // Siswa tidak bisa mengajukan izin sendiri, harus melalui orang tua sebagai bentuk pendampingan
                // Route::get('/ajukan-izin', [SiaPresensiController::class, 'ajukanIzin'])->name('ajukan-izin');
                // Route::post('/ajukan-izin', [SiaPresensiController::class, 'storeIzin'])->name('store-izin');
            });

            // Penilaian Harian
            Route::get('/penilaian', [SiaDashboardController::class, 'penilaian'])->name('penilaian');

            // Pembayaran
            Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
                Route::get('/', [SiaPembayaranController::class, 'index'])->name('index');
                Route::post('/bayar', [SiaPembayaranController::class, 'bayar'])->name('bayar');
                Route::get('/riwayat', [SiaPembayaranController::class, 'riwayat'])->name('riwayat');
                Route::get('/cetak/{pembayaran}', [SiaPembayaranController::class, 'cetak'])->name('cetak');

                // Midtrans callbacks
                Route::post('/midtrans/notification', [SiaPembayaranController::class, 'midtransNotification'])->name('midtrans-notification');
                Route::get('/midtrans/finish', [SiaPembayaranController::class, 'midtransFinish'])->name('midtrans-finish');
            });

            // Rapor
            Route::prefix('rapor')->name('rapor.')->group(function () {
                Route::get('/', [SiaRaporController::class, 'index'])->name('index');
                Route::get('/tengah-semester/{rapor}', [SiaRaporController::class, 'tengahSemester'])->name('tengah-semester');
                Route::get('/akhir-semester/{rapor}', [SiaRaporController::class, 'akhirSemester'])->name('akhir-semester');
                Route::get('/download/{rapor}', [SiaRaporController::class, 'download'])->name('download');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | LMS (Learning Management System) Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('lms')->name('lms.')->group(function () {

            // Dashboard LMS
            Route::get('/dashboard', [LmsDashboardController::class, 'index'])->name('dashboard');

            // Kalender Akademik
            Route::get('/kalender', [SiswaDashboardController::class, 'kalenderTahunan'])->name('kalender');
            Route::get('/kalender/{tanggal}', [SiswaDashboardController::class, 'kalenderDetail'])->name('kalender.detail');

            // Jadwal Pelajaran
            Route::get('/jadwal', [LmsDashboardController::class, 'jadwal'])->name('jadwal');
            Route::get('/jadwal/print', [LmsDashboardController::class, 'printJadwal'])->name('jadwal.print');

            // Daftar Guru
            Route::get('/guru', [LmsDashboardController::class, 'guru'])->name('guru');

            // Mata Pelajaran
            Route::prefix('mata-pelajaran')->name('mapel.')->group(function () {

                // Detail Mata Pelajaran
                Route::get('/{mapelId}', [LmsMateriController::class, 'show'])->name('show');

                // Materi
                Route::get('/{mapelId}/materi/{materiId}', [LmsMateriController::class, 'lihatMateri'])->name('materi');

                // Tugas
                Route::prefix('{mapelId}/tugas')->name('tugas.')->group(function () {
                    Route::get('/', [LmsTugasController::class, 'index'])->name('index');
                    Route::get('/{tugasId}', [LmsTugasController::class, 'show'])->name('show');
                    Route::post('/{tugasId}/submit', [LmsTugasController::class, 'submit'])->name('submit');
                });

                // Ujian
                Route::prefix('{mapelId}/ujian')->name('ujian.')->group(function () {
                    Route::get('/{ujianId}', [LmsUjianController::class, 'show'])->name('show');
                    Route::post('/{ujianId}/mulai', [LmsUjianController::class, 'mulai'])->name('mulai');
                    Route::post('/{ujianId}/submit', [LmsUjianController::class, 'submit'])->name('submit');
                });

                // Forum Diskusi
                Route::prefix('{mapelId}/forum')->name('forum.')->group(function () {
                    Route::get('/', [LmsMateriController::class, 'forum'])->name('index');
                    Route::post('/', [LmsMateriController::class, 'postForum'])->name('post');
                });
            });

            // Daftar Semua Tugas (Global)
            Route::get('/tugas', [LmsTugasController::class, 'indexAll'])->name('tugas.index');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ORANG TUA DASHBOARD & ROUTES
    | Note: Orang tua yang bertanggung jawab untuk pembayaran & monitoring anak
    | Siswa hanya fokus belajar, tidak ada akses pembayaran (mencegah penyembunyian info)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:orang_tua'])->prefix('orang-tua')->name('orang-tua.')->group(function () {

        // Dashboard Orang Tua
        Route::get('/dashboard', [OrangTuaController::class, 'dashboard'])->name('dashboard');

        // Tagihan & Pembayaran Anak
        Route::prefix('tagihan')->name('tagihan.')->group(function () {
            Route::get('/anak/{siswa}', [OrangTuaController::class, 'tagihanAnak'])->name('anak');
            Route::post('/anak/{siswa}/bayar', [OrangTuaController::class, 'prosesBayar'])->name('bayar');
        });

        // Pembayaran Digital (Midtrans)
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/snap-finish', [OrangTuaController::class, 'snapFinish'])->name('snap.finish');
            Route::get('/snap/{pembayaran}', [OrangTuaController::class, 'snapPayment'])->name('snap');
            Route::post('/continue/{pembayaran}', [OrangTuaController::class, 'continuePayment'])->name('continue');
        });

        // Monitoring Rapor Anak
        Route::prefix('rapor')->name('rapor.')->group(function () {
            Route::get('/anak/{siswa}', [OrangTuaController::class, 'raporAnak'])->name('anak');
            Route::get('/detail/{rapor}', [OrangTuaController::class, 'detailRapor'])->name('detail');
        });

        // Monitoring Presensi & Pengajuan Izin Anak
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/anak/{siswa}', [OrangTuaController::class, 'presensiAnak'])->name('anak');
            Route::get('/anak/{siswa}/ajukan-izin', [OrangTuaController::class, 'ajukanIzin'])->name('ajukan-izin');
            Route::post('/anak/{siswa}/store-izin', [OrangTuaController::class, 'storeIzin'])->name('store-izin');
            Route::get('/anak/{siswa}/riwayat-izin', [OrangTuaController::class, 'riwayatIzin'])->name('riwayat-izin');
            Route::get('/edit-izin/{presensi}', [OrangTuaController::class, 'editIzin'])->name('edit-izin');
            Route::put('/update-izin/{presensi}', [OrangTuaController::class, 'updateIzin'])->name('update-izin');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN AKUN & PROFIL (Semua Role)
    |--------------------------------------------------------------------------
    */
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/settings', [App\Http\Controllers\AccountController::class, 'settings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\AccountController::class, 'updateSettings'])->name('update-settings');
        Route::put('/change-password', [App\Http\Controllers\AccountController::class, 'changePassword'])->name('change-password');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
        Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::post('/upload-foto', [App\Http\Controllers\ProfileController::class, 'uploadFoto'])->name('upload-foto');
        Route::delete('/delete-foto', [App\Http\Controllers\ProfileController::class, 'deleteFoto'])->name('delete-foto');
    });

    /*
    |--------------------------------------------------------------------------
    | FALLBACK DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});