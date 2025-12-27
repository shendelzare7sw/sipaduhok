<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\WaliKelasController;
use App\Http\Controllers\Admin\GuruPengajarController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\CetakLaporanController;
use App\Http\Controllers\Ketua\KetuaController;
use App\Http\Controllers\Sekretaris\SekretarisController;

use App\Http\Controllers\Bendahara\BendaharaController;
use App\Http\Controllers\Bendahara\TagihanController;
use App\Http\Controllers\Bendahara\PembayaranController;
use App\Http\Controllers\Bendahara\ValidasiAksesController;
use App\Http\Controllers\Bendahara\LaporanPembayaranController;

/*
| PUBLIC ROUTES - Accessible to everyone
*/

// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Profil Section
Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/tentang-sekolah', function () {
        return view('tentang-sekolah');
    })->name('tentang-sekolah');
    
    Route::get('/visi-misi', function () {
        return view('visi-misi');
    })->name('visi-misi');
    
    Route::get('/struktur-organisasi', function () {
        return view('struktur-organisasi');
    })->name('struktur-organisasi');
    
    Route::get('/profil-guru', function () {
        return view('profil-guru');
    })->name('profil-guru');
});

// Program Section
Route::prefix('program')->name('program.')->group(function () {
    Route::get('/paud-tk', function () {
        return view('program-paud-tk');
    })->name('paud-tk');
    
    Route::get('/sd-sma', function () {
        return view('program-sd-sma');
    })->name('sd-sma');
    
    Route::get('/inklusi', function () {
        return view('program-inklusi');
    })->name('inklusi');
    
    Route::get('/terapi', function () {
        return view('program-terapi');
    })->name('terapi');
});

// Other Public Pages
Route::get('/fasilitas', function () {
    return view('fasilitas');
})->name('fasilitas');

Route::get('/ppdb', function () {
    return view('ppdb');
})->name('ppdb');

Route::get('/galeri', function () {
    return view('galeri');
})->name('galeri');

Route::get('/kontak', function () {
    return view('kontak');
})->name('kontak');

// Berita Public Page
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');

/*
| AUTHENTICATION ROUTES
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

/*
| PROTECTED ROUTES - Require Authentication
*/

Route::middleware(['auth'])->group(function () {
    
    /*
    | ADMIN DASHBOARD
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            
            // Tenaga Pendidik
            Route::get('/tenaga-pendidik', [UserController::class, 'tenagaPendidik'])->name('tenaga-pendidik');
            Route::get('/tenaga-pendidik/create', [UserController::class, 'createTenagaPendidik'])->name('create-tenaga-pendidik'); 
            Route::post('/tenaga-pendidik', [UserController::class, 'storeTenagaPendidik'])->name('store-tenaga-pendidik');
            Route::get('/tenaga-pendidik/{id}/edit', [UserController::class, 'editTenagaPendidik'])->name('edit-tenaga-pendidik');
            Route::put('/tenaga-pendidik/{id}', [UserController::class, 'updateTenagaPendidik'])->name('update-tenaga-pendidik');
            Route::delete('/tenaga-pendidik/{id}', [UserController::class, 'deleteTenagaPendidik'])->name('delete-tenaga-pendidik');
            Route::get('/tenaga-pendidik/{id}', [UserController::class, 'showTenagaPendidik'])->name('show-tenaga-pendidik'); 
            
            // Siswa
            Route::get('/siswa', [UserController::class, 'siswa'])->name('siswa');
            Route::get('/siswa/create', [UserController::class, 'createSiswa'])->name('create-siswa'); 
            Route::post('/siswa', [UserController::class, 'storeSiswa'])->name('store-siswa');
            Route::get('/siswa/{id}/edit', [UserController::class, 'editSiswa'])->name('edit-siswa');
            Route::put('/siswa/{id}', [UserController::class, 'updateSiswa'])->name('update-siswa');
            Route::delete('/siswa/{id}', [UserController::class, 'deleteSiswa'])->name('delete-siswa');
            Route::get('/siswa/{id}', [UserController::class, 'showSiswa'])->name('show-siswa'); 
        });
        
        // Tahun Ajaran
        Route::resource('tahun-ajaran', TahunAjaranController::class);
        Route::post('tahun-ajaran/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])->name('tahun-ajaran.activate');

        // Cabang
        Route::resource('cabang', CabangController::class);
        Route::post('cabang/{cabang}/toggle-status', [CabangController::class, 'toggleStatus'])->name('cabang.toggle-status');
        
        // Kelas
        Route::get('kelas/print', [KelasController::class, 'printDaftarKelas'])->name('kelas.print');
        Route::get('kelas/{kela}/manage-siswa', [KelasController::class, 'manageSiswa'])->name('kelas.manage-siswa');
        Route::post('kelas/{kela}/add-siswa', [KelasController::class, 'addSiswa'])->name('kelas.add-siswa');
        Route::post('kelas/{kela}/remove-siswa', [KelasController::class, 'removeSiswa'])->name('kelas.remove-siswa');
        Route::post('kelas/{kela}/assign-wali', [KelasController::class, 'assignWaliKelas'])->name('kelas.assign-wali');
        Route::resource('kelas', KelasController::class);

        // Wali Kelas
        Route::get('wali-kelas', [WaliKelasController::class, 'index'])->name('wali-kelas.index');
        Route::get('wali-kelas/print', [WaliKelasController::class, 'print'])->name('wali-kelas.print');
        Route::get('wali-kelas/{kelas}', [WaliKelasController::class, 'show'])->name('wali-kelas.show');
        Route::post('wali-kelas/{kelas}/assign', [WaliKelasController::class, 'assign'])->name('wali-kelas.assign');
        Route::post('wali-kelas/bulk-assign', [WaliKelasController::class, 'bulkAssign'])->name('wali-kelas.bulk-assign');
        
        // Guru Pengajar
        Route::get('guru-pengajar', [GuruPengajarController::class, 'index'])->name('guru-pengajar.index');
        Route::get('guru-pengajar/print', [GuruPengajarController::class, 'print'])->name('guru-pengajar.print');
        Route::get('guru-pengajar/kelas/{kelas}', [GuruPengajarController::class, 'manageKelas'])->name('guru-pengajar.manage-kelas');
        Route::post('guru-pengajar/kelas/{kelas}/assign', [GuruPengajarController::class, 'assignToKelas'])->name('guru-pengajar.assign-to-kelas');
        Route::post('guru-pengajar/kelas/{kelas}/remove', [GuruPengajarController::class, 'removeFromKelas'])->name('guru-pengajar.remove-from-kelas');
        Route::get('guru-pengajar/{guruPengajar}', [GuruPengajarController::class, 'show'])->name('guru-pengajar.show');
        Route::post('guru-pengajar/{guruPengajar}/assign', [GuruPengajarController::class, 'assign'])->name('guru-pengajar.assign');
        Route::post('guru-pengajar/{guruPengajar}/remove-assignment', [GuruPengajarController::class, 'removeAssignment'])->name('guru-pengajar.remove-assignment');
        
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
        
        // Cetak Laporan
        Route::get('cetak-laporan', [CetakLaporanController::class, 'index'])->name('cetak-laporan.index');
        Route::get('cetak-laporan/siswa', [CetakLaporanController::class, 'siswa'])->name('cetak-laporan.siswa');
        Route::get('cetak-laporan/tenaga-pendidik', [CetakLaporanController::class, 'tenagaPendidik'])->name('cetak-laporan.tenaga-pendidik');
        Route::get('cetak-laporan/kelas', [CetakLaporanController::class, 'kelas'])->name('cetak-laporan.kelas');
        Route::get('cetak-laporan/wali-kelas', [CetakLaporanController::class, 'waliKelas'])->name('cetak-laporan.wali-kelas');
        Route::get('cetak-laporan/guru-pengajar', [CetakLaporanController::class, 'guruPengajar'])->name('cetak-laporan.guru-pengajar');
        Route::get('cetak-laporan/rekap', [CetakLaporanController::class, 'rekap'])->name('cetak-laporan.rekap');
    });
    
    /*
    | KETUA PKBM DASHBOARD
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
        Route::prefix('laporan')->name('laporan.')->group(function() {
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
    | SEKRETARIS DASHBOARD
    */
    Route::middleware(['role:sekretaris'])->prefix('sekretaris')->name('sekretaris.')->group(function () {
        Route::get('/dashboard', [SekretarisController::class, 'dashboard'])->name('dashboard');

        // Kalender Akademik
    Route::prefix('kalender')->name('kalender.')->group(function () {
    Route::get('/', [SekretarisController::class, 'kalenderIndex'])->name('index');
    Route::get('/bulanan', [SekretarisController::class, 'kalenderBulanan'])->name('bulanan');
    
    // ⭐ ROUTE BARU - Cetak Kalender
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
    | BENDAHARA DASHBOARD
    */
Route::middleware(['role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [BendaharaController::class, 'dashboard'])->name('dashboard');
    
    /*
    | TAGIHAN (Billing Management)
    */
    Route::prefix('tagihan')->name('tagihan.')->group(function () {
        Route::get('/', [TagihanController::class, 'index'])->name('index');
        Route::get('/bulk-create', [TagihanController::class, 'bulkCreate'])->name('bulk-create');
        Route::post('/bulk-create', [TagihanController::class, 'bulkCreate'])->name('bulk-create.store');
        Route::get('/{siswa}', [TagihanController::class, 'show'])->name('show');
        Route::get('/{siswa}/edit', [TagihanController::class, 'edit'])->name('edit');
        Route::put('/{siswa}', [TagihanController::class, 'update'])->name('update');
        Route::get('/{siswa}/cetak', [TagihanController::class, 'cetak'])->name('cetak');
    });
    
    /*
    | PEMBAYARAN (Payment Management)
    */
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
    
    /*
    | VALIDASI AKSES UJIAN & RAPOR
    */
    Route::prefix('validasi-akses')->name('validasi-akses.')->group(function () {
        Route::get('/', [ValidasiAksesController::class, 'index'])->name('index');
        
        // Validasi individual
        Route::post('/{siswa}/validasi-ujian', [ValidasiAksesController::class, 'validasiUjian'])->name('validasi-ujian');
        Route::post('/{siswa}/batalkan-ujian', [ValidasiAksesController::class, 'batalkanUjian'])->name('batalkan-ujian');
        Route::post('/{siswa}/validasi-rapor', [ValidasiAksesController::class, 'validasiRapor'])->name('validasi-rapor');
        Route::post('/{siswa}/batalkan-rapor', [ValidasiAksesController::class, 'batalkanRapor'])->name('batalkan-rapor');
        
        // Bulk validasi per kelas
        Route::post('/kelas/{kelas}/bulk-validasi-ujian', [ValidasiAksesController::class, 'bulkValidasiUjian'])->name('bulk-validasi-ujian');
        Route::post('/kelas/{kelas}/bulk-validasi-rapor', [ValidasiAksesController::class, 'bulkValidasiRapor'])->name('bulk-validasi-rapor');
        
        // Bulk validasi siswa terpilih
        Route::post('/bulk-validasi-selected', [ValidasiAksesController::class, 'bulkValidasiSelected'])->name('bulk-validasi-selected');
        
        // Reset validasi (untuk tahun ajaran baru)
        Route::post('/reset', [ValidasiAksesController::class, 'resetValidasi'])->name('reset');
    });
    
    /*
    | LAPORAN PEMBAYARAN
    */
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // Laporan pembayaran bulanan
        Route::get('/', [LaporanPembayaranController::class, 'index'])->name('index');
        Route::get('/cetak', [LaporanPembayaranController::class, 'cetak'])->name('cetak');
        
        // Rekap tagihan per kelas
        Route::get('/rekap-tagihan', [LaporanPembayaranController::class, 'rekapTagihan'])->name('rekap-tagihan');
        Route::get('/cetak-rekap-tagihan', [LaporanPembayaranController::class, 'cetakRekapTagihan'])->name('cetak-rekap-tagihan');
        
        // Daftar siswa belum lunas
        Route::get('/belum-lunas', [LaporanPembayaranController::class, 'belumLunas'])->name('belum-lunas');
        Route::get('/cetak-belum-lunas', [LaporanPembayaranController::class, 'cetakBelumLunas'])->name('cetak-belum-lunas');
    });
});

    /*
    | WALI KELAS DASHBOARD
    */
    Route::middleware(['role:wali_kelas'])->prefix('wali')->name('wali.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'waliKelas'])->name('dashboard');
    });

    /*
    | GURU PENGAJAR DASHBOARD
    */
    Route::middleware(['role:guru_pengajar'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'guru'])->name('dashboard');
    });

    /*
    | SISWA DASHBOARD
    */
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'siswa'])->name('dashboard');
    });

    /*
    | FALLBACK DASHBOARD
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});