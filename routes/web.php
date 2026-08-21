<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PaywuzWebhookController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AiSettingController; // Added
use App\Http\Controllers\AiChatbotController; // AI Chatbot General Assistant
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\WaliKelasController as AdminWaliKelasController;
use App\Http\Controllers\Admin\GuruPengajarController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\CetakLaporanController;

// Ketua PKBM Controllers
use App\Http\Controllers\Ketua\KetuaController;
use App\Http\Controllers\Ketua\ValidasiRaporController as KetuaValidasiRaporController;

// Wakil Kepala Sekolah Controllers
use App\Http\Controllers\WakilKepalaSekolah\WakilKepalaSekolahController;
use App\Http\Controllers\WakilKepalaSekolah\TahunAjaranController as WakaTahunAjaranController;
use App\Http\Controllers\WakilKepalaSekolah\MataPelajaranController as WakaMataPelajaranController;
use App\Http\Controllers\WakilKepalaSekolah\KelasController as WakaKelasController;
use App\Http\Controllers\WakilKepalaSekolah\ManajemenSiswaController as WakaManajemenSiswaController;
use App\Http\Controllers\WakilKepalaSekolah\WaliKelasController as WakaWaliKelasController;
use App\Http\Controllers\WakilKepalaSekolah\JadwalPelajaranController as WakaJadwalPelajaranController;
use App\Http\Controllers\WakilKepalaSekolah\PengaturanIstirahatController as WakaPengaturanIstirahatController;
use App\Http\Controllers\WakilKepalaSekolah\GuruPengajarController as WakaGuruPengajarController;

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
// use App\Http\Controllers\Siswa\SiaRaporController; // Disabled - Siswa tidak berhak akses rapor
use App\Http\Controllers\Siswa\LmsDashboardController;

// Wali Siswa Controllers
use App\Http\Controllers\OrangTua\OrangTuaController;
use App\Http\Controllers\OrangTua\PembayaranDigitalController;
use App\Http\Controllers\Siswa\LmsMateriController;
use App\Http\Controllers\Siswa\LmsTugasController;
use App\Http\Controllers\Siswa\LmsUjianController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES - Accessible to everyone
|--------------------------------------------------------------------------
*/

// Webhook Paywuz berada di luar autentikasi; keasliannya diverifikasi dengan HMAC.
Route::post('/payments/paywuz/webhook', PaywuzWebhookController::class)->name('paywuz.webhook');

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

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

// Legal Pages
Route::get('/kebijakan-privasi', [\App\Http\Controllers\LandingPageController::class, 'kebijakanPrivasi'])->name('kebijakan-privasi');
Route::get('/syarat-ketentuan', [\App\Http\Controllers\LandingPageController::class, 'syaratKetentuan'])->name('syarat-ketentuan');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // Admin Recovery Routes
    Route::post('/admin-recovery/unlock', [\App\Http\Controllers\Auth\AdminRecoveryController::class, 'unlock'])->name('admin.recovery.unlock');
    Route::get('/admin-recovery', [\App\Http\Controllers\Auth\AdminRecoveryController::class, 'showLinkRequestForm'])->name('admin.recovery');
    Route::post('/admin-recovery', [\App\Http\Controllers\Auth\AdminRecoveryController::class, 'reset'])->name('admin.recovery.reset')
        ->middleware('throttle:5,1');

    // Public User Recovery Routes (Phase 3)
    Route::get('/recovery', [\App\Http\Controllers\Auth\UserRecoveryController::class, 'index'])->name('user.recovery');
    Route::post('/recovery', [\App\Http\Controllers\Auth\UserRecoveryController::class, 'store'])->name('user.recovery.store')
        ->middleware('throttle:5,1');

    // Recovery Password Reset via Link
    Route::get('/recovery/reset/{token}', function ($token) {
        $ticket = \App\Models\RecoveryTicket::where('token_reset', $token)
            ->whereNotIn('status', ['resolved', 'rejected', 'expired'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$ticket) {
            return redirect()->route('login')->with('error', 'Tautan reset password ini tidak valid, kedaluwarsa, atau sudah pernah digunakan.');
        }

        return view('auth.reset-password-ticket', ['token' => $token]);
    })->name('password.reset.ticket');

    Route::post('/recovery/reset', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()],
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $ticket = \App\Models\RecoveryTicket::where('token_reset', $request->token)
                ->whereNotIn('status', ['resolved', 'rejected', 'expired'])
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (!$ticket) {
                return redirect()->route('login')->with('error', 'Tautan reset sudah tidak valid atau kedaluwarsa.');
            }

            $user = $ticket->user;

            // Password baru tidak boleh sama dengan password lama
            if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
            }

            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();

            $ticket->update(['status' => 'resolved', 'token_reset' => null]);

            return redirect()->route('login')->with('success', 'Password Anda berhasil diubah! Silakan login dengan password baru Anda.');
        });
    })->name('password.reset.ticket.submit')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Admin Security Setup Routes (forced after login if not set)
    Route::get('/admin/security-setup', [\App\Http\Controllers\Auth\AdminSecuritySetupController::class, 'showSetupForm'])->name('admin.security.setup');
    Route::post('/admin/security-setup', [\App\Http\Controllers\Auth\AdminSecuritySetupController::class, 'store'])->name('admin.security.setup.store');
});

/*
|--------------------------------------------------------------------------
| FILE PREVIEW ROUTES (Moved to auth-protected group below)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - Require Authentication
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // File preview (auth-protected + token diikat ke pemilik; lihat helper preview_url()).
    // Route raw-path lama (/storage-preview?path=) dihapus karena tidak melakukan cek kepemilikan.
    Route::get('/view-document/{id}', [\App\Http\Controllers\FileController::class, 'previewHash'])->name('document.preview');

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
            Route::get('/tenaga-pendidik/print', [UserController::class, 'printTenagaPendidik'])->name('tenaga-pendidik.print');
            Route::get('/tenaga-pendidik', [UserController::class, 'tenagaPendidik'])->name('tenaga-pendidik');
            Route::get('/tenaga-pendidik/create', [UserController::class, 'createTenagaPendidik'])->name('create-tenaga-pendidik');
            Route::post('/tenaga-pendidik', [UserController::class, 'storeTenagaPendidik'])->name('store-tenaga-pendidik');
            Route::get('/tenaga-pendidik/{id}/edit', [UserController::class, 'editTenagaPendidik'])->name('edit-tenaga-pendidik');
            Route::put('/tenaga-pendidik/{id}', [UserController::class, 'updateTenagaPendidik'])->name('update-tenaga-pendidik');
            Route::delete('/tenaga-pendidik/{id}', [UserController::class, 'deleteTenagaPendidik'])->name('delete-tenaga-pendidik');
            Route::get('/tenaga-pendidik/{id}', [UserController::class, 'showTenagaPendidik'])->name('show-tenaga-pendidik');
            Route::post('/tenaga-pendidik/bulk-delete', [UserController::class, 'bulkDeleteTenagaPendidik'])->name('bulk-delete-tenaga-pendidik');

            // Siswa
            Route::get('/siswa/import', [UserController::class, 'importSiswaForm'])->name('import-siswa');
            Route::post('/siswa/import', [UserController::class, 'importSiswa'])->name('import-siswa.store');
            Route::get('/siswa/template', [UserController::class, 'downloadSiswaTemplate'])->name('siswa-template');
            Route::get('/siswa/print', [UserController::class, 'printSiswa'])->name('siswa.print');
            Route::get('/siswa', [UserController::class, 'siswa'])->name('siswa');
            Route::get('/siswa/create', [UserController::class, 'createSiswa'])->name('create-siswa');
            Route::post('/siswa', [UserController::class, 'storeSiswa'])->name('store-siswa');
            Route::get('/siswa/{id}/edit', [UserController::class, 'editSiswa'])->name('edit-siswa');
            Route::put('/siswa/{id}', [UserController::class, 'updateSiswa'])->name('update-siswa');
            Route::delete('/siswa/{id}', [UserController::class, 'deleteSiswa'])->name('delete-siswa');
            Route::get('/siswa/{id}', [UserController::class, 'showSiswa'])->name('show-siswa');
            Route::post('/siswa/bulk-delete', [UserController::class, 'bulkDeleteSiswa'])->name('bulk-delete-siswa');

            // Wali Siswa
            Route::get('/wali-siswa/import', [UserController::class, 'importOrangTuaForm'])->name('import-wali-siswa');
            Route::post('/wali-siswa/import', [UserController::class, 'importOrangTua'])->name('import-wali-siswa.store');
            Route::get('/wali-siswa/template', [UserController::class, 'downloadOrangTuaTemplate'])->name('wali-siswa-template');
            Route::get('/wali-siswa/print', [UserController::class, 'printOrangTua'])->name('wali-siswa.print');
            Route::get('/wali-siswa', [UserController::class, 'orangTua'])->name('wali-siswa');
            Route::get('/wali-siswa/create', [UserController::class, 'createOrangTua'])->name('wali-siswa.create');
            Route::post('/wali-siswa', [UserController::class, 'storeOrangTua'])->name('wali-siswa.store');
            Route::get('/wali-siswa/{id}', [UserController::class, 'showOrangTua'])->name('show-wali-siswa');
            Route::get('/wali-siswa/{id}/edit', [UserController::class, 'editOrangTua'])->name('edit-wali-siswa');
            Route::put('/wali-siswa/{id}', [UserController::class, 'updateOrangTua'])->name('update-wali-siswa');
            Route::post('/wali-siswa/{id}/toggle-status', [UserController::class, 'toggleOrangTuaStatus'])->name('toggle-wali-siswa-status');
            Route::delete('/wali-siswa/{id}', [UserController::class, 'deleteOrangTua'])->name('delete-wali-siswa');
            Route::post('/wali-siswa/bulk-delete', [UserController::class, 'bulkDeleteOrangTua'])->name('bulk-delete-wali-siswa');
        });

        // Pengaturan AI Assistant
        Route::prefix('ai-settings')->name('ai-settings.')->group(function () {
            Route::get('/', [AiSettingController::class, 'index'])->name('index');
            Route::put('/', [AiSettingController::class, 'update'])->name('update');
            Route::post('/test', [AiSettingController::class, 'testConnection'])->name('test');
        });

        // Manajemen Tiket Pemulihan Akun (Phase 3)
        Route::prefix('recovery-tickets')->name('recovery-tickets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'index'])->name('index');
            Route::post('/{ticket}/resend', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'resend'])->name('resend');
            Route::post('/{ticket}/resolve', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'resolve'])->name('resolve');
            Route::post('/{ticket}/reject', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'reject'])->name('reject');
            Route::post('/bulk-resolve', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'bulkResolve'])->name('bulk-resolve');
            Route::post('/bulk-reject', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'bulkReject'])->name('bulk-reject');
            Route::post('/admin-wa', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'updateAdminWa'])->name('update-admin-wa');
            Route::get('/history', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'history'])->name('history');
            Route::post('/history/bulk-delete', [\App\Http\Controllers\Admin\AdminRecoveryTicketController::class, 'bulkDeleteHistory'])->name('history.bulk-delete');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AI CHATBOT ROUTES (All roles EXCEPT siswa - Auth middleware only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('ai-chatbot')->name('ai-chatbot.')->group(function () {
        // Send message (with optional file attachment)
        Route::post('/send-message', [AiChatbotController::class, 'sendMessage'])
            ->name('send-message')
            ->middleware('throttle:10,1'); // 10 requests per minute

        // Get available AI models
        Route::get('/models', [AiChatbotController::class, 'getModels'])
            ->name('models');

        // Get role-specific quick actions
        Route::get('/quick-actions', [AiChatbotController::class, 'getQuickActions'])
            ->name('quick-actions');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (continued)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

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
        Route::post('kelas/copy', [KelasController::class, 'copyClasses'])->name('kelas.copy'); // Route Salin Kelas
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

        // Guru Pengajar (Read-Only Dashboard - derived from Jadwal Pelajaran)
        Route::get('guru-pengajar', [GuruPengajarController::class, 'index'])->name('guru-pengajar.index');
        Route::get('guru-pengajar/print', [GuruPengajarController::class, 'print'])->name('guru-pengajar.print');
        Route::post('guru-pengajar/rebuild', [GuruPengajarController::class, 'rebuildFromJadwal'])->name('guru-pengajar.rebuild');
        Route::get('guru-pengajar/kelas/{kelas}', [GuruPengajarController::class, 'manageKelas'])->name('guru-pengajar.manage-kelas');
        Route::get('guru-pengajar/{guruPengajar}', [GuruPengajarController::class, 'show'])->name('guru-pengajar.show');

        // Mata Pelajaran
        Route::get('mata-pelajaran/import', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'importForm'])->name('mata-pelajaran.import');
        Route::post('mata-pelajaran/import', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'import'])->name('mata-pelajaran.import.store');
        Route::get('mata-pelajaran/template', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'downloadTemplate'])->name('mata-pelajaran.template');
        Route::get('mata-pelajaran/suggest-kode', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'suggestKodeMapel'])->name('mata-pelajaran.suggest-kode');
        Route::get('mata-pelajaran/print', [\App\Http\Controllers\Admin\MataPelajaranController::class, 'print'])->name('mata-pelajaran.print');
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
    Route::get('/kelas/{kelas}/export-excel', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'exportExcelClass'])->name('export-excel-class');
            Route::get('/export-pdf', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'exportPdfAll'])->name('export-pdf');
            Route::get('/export-excel', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'exportExcel'])->name('export-excel');
            Route::post('/duplicate', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'duplicate'])->name('duplicate');
            Route::get('/{jadwalPelajaran}/edit', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'edit'])->name('edit');
            Route::put('/{jadwalPelajaran}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'update'])->name('update');
            Route::delete('/{jadwalPelajaran}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'destroy'])->name('destroy');
            Route::post('/{jadwalPelajaran}/ganti-guru', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'gantiGuru'])->name('ganti-guru');
            Route::post('/bulk-replace-guru', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'bulkReplaceGuru'])->name('bulk-replace-guru');
            Route::post('/bulk-delete', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-update-status', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::get('/get-students/{kelas}', [\App\Http\Controllers\Admin\JadwalPelajaranController::class, 'getStudents'])->name('get-students');
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
        Route::get('cetak-laporan/rekap-akademik', [CetakLaporanController::class, 'rekapAkademik'])->name('cetak-laporan.rekap-akademik');

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

                Route::get('/cetak-laporan', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'cetakLaporan'])->name('cetak-laporan');

                // Reset Tagihan (Admin Only - masa percobaan)
                Route::post('/reset-tagihan', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'resetTagihan'])->name('reset-tagihan');

                // Tarik Tunggakan TA Lama → TA Aktif (carryover)
                Route::get('/carryover', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'carryoverIndex'])->name('carryover');
                Route::post('/carryover/preview', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'carryoverPreview'])->name('carryover.preview');
                Route::post('/carryover/execute', [\App\Http\Controllers\Admin\Keuangan\TagihanController::class, 'carryoverExecute'])->name('carryover.execute');

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
                Route::get('/{pembayaran}/cetak-kwitansi', [\App\Http\Controllers\Admin\Keuangan\PembayaranController::class, 'cetakKwitansi'])->name('cetak-kwitansi');
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

                // Pengaturan batas pembayaran
                Route::post('/batas-pembayaran', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'updateBatasPembayaran'])->name('batas-pembayaran');

                // Dispensasi
                Route::post('/dispensasi', [\App\Http\Controllers\Admin\Keuangan\ValidasiAksesController::class, 'ajukanDispensasi'])->name('dispensasi');
            });

            // Config Pembayaran Digital & Rekening Bank
            Route::redirect('info-pembayaran', '/admin/keuangan/config', 301)->name('info-pembayaran.legacy-index');
            Route::post('info-pembayaran/update', [\App\Http\Controllers\Admin\Keuangan\InfoPembayaranController::class, 'update'])->name('info-pembayaran.legacy-update');
            Route::prefix('config')->name('info-pembayaran.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Keuangan\InfoPembayaranController::class, 'index'])->name('index');
                Route::post('/update', [\App\Http\Controllers\Admin\Keuangan\InfoPembayaranController::class, 'update'])->name('update');
            });

            // Promotion Validation (New Admin Access)
            Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function () {
                Route::get('/validation', [\App\Http\Controllers\Admin\Keuangan\PromotionValidationController::class, 'index'])->name('validation.index');
                Route::post('/validation', [\App\Http\Controllers\Admin\Keuangan\PromotionValidationController::class, 'store'])->name('validation.store');
                Route::post('/validation/bulk', [\App\Http\Controllers\Admin\Keuangan\PromotionValidationController::class, 'bulkStore'])->name('validation.bulk-store');
                Route::get('/validation/history', [\App\Http\Controllers\Admin\Keuangan\PromotionValidationController::class, 'history'])->name('validation.history');
                Route::post('/validation/history/bulk-delete', [\App\Http\Controllers\Admin\Keuangan\PromotionValidationController::class, 'bulkDeleteHistory'])->name('validation.history.bulk-delete');
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
                Route::get('/{id}', [$controller, 'kalenderShow'])->name('show');
                Route::get('/{id}/edit', [$controller, 'kalenderEdit'])->name('edit');
                Route::put('/{id}', [$controller, 'kalenderUpdate'])->name('update');
                Route::delete('/{id}', [$controller, 'kalenderDestroy'])->name('destroy');
                Route::post('/{id}/toggle-visibility', [$controller, 'kalenderToggleVisibility'])->name('toggle-visibility');
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

            // Promotion System (Report, KKM, Settings)
            Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function () {
                Route::get('/report', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'index'])->name('report');
                Route::get('/report/print', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'print'])->name('report.print');
                Route::post('/execute', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'execute'])->name('execute');

                // Individual/Batch Rollback
                Route::post('/rollback/{statusId}', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'rollback'])->name('rollback');
                Route::post('/rollback-selected', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'rollbackSelected'])->name('rollback-selected');

                // Promote Selected (for failed students who now qualify)
                Route::post('/promote-selected', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'promoteSelected'])->name('promote-selected');

                // Scheduling
                Route::post('/cancel-schedule/{id}', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'cancelSchedule'])->name('cancel-schedule');

                // KKM (New Admin Access)
                Route::resource('kkm', \App\Http\Controllers\Admin\Akademik\PromotionKKMController::class)->only(['index', 'store']);

                // Settings (New Admin Access)
                Route::resource('settings', \App\Http\Controllers\Admin\Akademik\PromotionSettingsController::class)->only(['index', 'store']);
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

            // Monitoring LMS
            Route::prefix('lms')->name('lms.')->group(function () use ($monitoringController) {
                Route::get('/', [$monitoringController, 'lmsIndex'])->name('index');
                Route::get('/kelas/{kelas}', [$monitoringController, 'lmsKelas'])->name('kelas');
                Route::get('/preview/{type}/{id}', [$monitoringController, 'lmsPreview'])->name('preview');
                Route::post('/catatan', [$monitoringController, 'lmsKirimCatatan'])->name('catatan');
            });
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
            Route::delete('/{id}', [$monitoringController, 'catatanDestroy'])->name('destroy');
            Route::get('/{id}', [$monitoringController, 'catatanShow'])->name('show');
        });

        // Pengaturan LMS
        Route::prefix('lms-settings')->name('lms-settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LmsSettingController::class, 'index'])->name('index');
            Route::put('/update', [\App\Http\Controllers\Admin\LmsSettingController::class, 'update'])->name('update');
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

            // Monitoring LMS
            Route::prefix('lms')->name('lms.')->group(function () {
                Route::get('/', [KetuaController::class, 'lmsIndex'])->name('index');
                Route::get('/kelas/{kelas}', [KetuaController::class, 'lmsKelas'])->name('kelas');
                Route::get('/preview/{type}/{id}', [KetuaController::class, 'lmsPreview'])->name('preview');
                Route::post('/catatan', [KetuaController::class, 'lmsKirimCatatan'])->name('catatan');
            });
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
            Route::get('/rekap-akademik', [KetuaController::class, 'rekapAkademik'])->name('rekap-akademik');
        });

        // Catatan
        Route::prefix('catatan')->name('catatan.')->group(function () {
            Route::get('/', [KetuaController::class, 'catatanIndex'])->name('index');
            Route::get('/create', [KetuaController::class, 'catatanCreate'])->name('create');
            Route::post('/', [KetuaController::class, 'catatanStore'])->name('store');
            Route::delete('/{id}', [KetuaController::class, 'catatanDestroy'])->name('destroy');
            Route::get('/{id}', [KetuaController::class, 'catatanShow'])->name('show');
        });

        // Promotion Approval
        Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function() {
            Route::get('/approval', [\App\Http\Controllers\Ketua\PromotionApprovalController::class, 'index'])->name('approval.index');
            Route::put('/approval/bulk', [\App\Http\Controllers\Ketua\PromotionApprovalController::class, 'bulkUpdate'])->name('approval.bulk-update');
            Route::put('/approval/{id}', [\App\Http\Controllers\Ketua\PromotionApprovalController::class, 'update'])->name('approval.update');
            Route::get('/approval/history', [\App\Http\Controllers\Ketua\PromotionApprovalController::class, 'history'])->name('approval.history');
            Route::post('/approval/history/bulk-delete', [\App\Http\Controllers\Ketua\PromotionApprovalController::class, 'bulkDeleteHistory'])->name('approval.history.bulk-delete');
        });

        // Validasi Rapor (NEW - 3rd level validation)
        Route::prefix('validasi-rapor')->name('validasi-rapor.')->group(function() {
            Route::get('/', [KetuaValidasiRaporController::class, 'index'])->name('index');
            Route::post('/{siswa}/validasi', [KetuaValidasiRaporController::class, 'validasiRapor'])->name('validasi');
            Route::post('/{siswa}/batalkan', [KetuaValidasiRaporController::class, 'batalkanRapor'])->name('batalkan');
            Route::post('/bulk-validasi', [KetuaValidasiRaporController::class, 'bulkValidasi'])->name('bulk-validasi');
            Route::post('/validasi-semua', [KetuaValidasiRaporController::class, 'validasiSemuaRapor'])->name('validasi-semua');
            Route::get('/{siswa}/preview', [KetuaValidasiRaporController::class, 'previewRapor'])->name('preview');
            Route::post('/{siswa}/minta-revisi', [KetuaValidasiRaporController::class, 'mintaRevisi'])->name('minta-revisi');
        });

        // Dispensasi (Bendahara → Ketua)
        Route::prefix('dispensasi')->name('dispensasi.')->group(function() {
            Route::get('/', [KetuaValidasiRaporController::class, 'dispensasiIndex'])->name('index');
            Route::post('/approve', [KetuaValidasiRaporController::class, 'approveDispensasi'])->name('approve');
            Route::post('/reject', [KetuaValidasiRaporController::class, 'rejectDispensasi'])->name('reject');
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
            Route::post('/{id}/activate', [WakaTahunAjaranController::class, 'toggleActive'])->name('activate');
        });

        // Mata Pelajaran
        Route::get('mata-pelajaran/import', [WakaMataPelajaranController::class, 'import'])->name('mata-pelajaran.import');
        Route::post('mata-pelajaran/import', [WakaMataPelajaranController::class, 'importStore'])->name('mata-pelajaran.import.store');
        Route::get('mata-pelajaran/template', [WakaMataPelajaranController::class, 'downloadTemplate'])->name('mata-pelajaran.template');
        Route::get('mata-pelajaran/suggest-kode', [WakaMataPelajaranController::class, 'suggestKodeMapel'])->name('mata-pelajaran.suggest-kode');
        Route::get('mata-pelajaran/print', [WakaMataPelajaranController::class, 'print'])->name('mata-pelajaran.print');
        Route::resource('mata-pelajaran', WakaMataPelajaranController::class);

        // Kelas
        Route::get('kelas/import', [WakaKelasController::class, 'import'])->name('kelas.import');
        Route::post('kelas/import', [WakaKelasController::class, 'importStore'])->name('kelas.import.store');
        Route::get('kelas/template', [WakaKelasController::class, 'downloadTemplate'])->name('kelas.template');
        Route::get('/kelas/print', [WakaKelasController::class, 'print'])->name('kelas.print');
        Route::get('/kelas/{kelas}/manage-siswa', [WakaKelasController::class, 'manageSiswa'])->name('kelas.manage-siswa');
        Route::post('/kelas/{kelas}/add-siswa', [WakaKelasController::class, 'addSiswa'])->name('kelas.add-siswa');
        Route::post('/kelas/{kelas}/remove-siswa', [WakaKelasController::class, 'removeSiswa'])->name('kelas.remove-siswa');
        Route::post('/kelas/{kelas}/assign-wali', [WakaKelasController::class, 'assignWaliKelas'])->name('kelas.assign-wali');
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
            Route::get('/import', [WakaJadwalPelajaranController::class, 'importForm'])->name('import');
            Route::post('/import', [WakaJadwalPelajaranController::class, 'import'])->name('import.store');
            Route::get('/template', [WakaJadwalPelajaranController::class, 'downloadTemplate'])->name('template');
            Route::get('/', [WakaJadwalPelajaranController::class, 'index'])->name('index');
            Route::get('/create', [WakaJadwalPelajaranController::class, 'create'])->name('create');
            Route::post('/', [WakaJadwalPelajaranController::class, 'store'])->name('store');
            Route::get('/kelas/{kelas}/show', [WakaJadwalPelajaranController::class, 'show'])->name('show');
            Route::get('/kelas/{kelas}/preview-print', [WakaJadwalPelajaranController::class, 'previewPrint'])->name('preview-print');
            Route::get('/kelas/{kelas}/print', [WakaJadwalPelajaranController::class, 'exportPdf'])->name('print');
            Route::get('/kelas/{kelas}/export-excel', [WakaJadwalPelajaranController::class, 'exportExcelClass'])->name('export-excel-class');
            Route::get('/export-pdf', [WakaJadwalPelajaranController::class, 'exportPdfAll'])->name('export-pdf');
            Route::get('/export-excel', [WakaJadwalPelajaranController::class, 'exportExcel'])->name('export-excel');
            Route::post('/duplicate', [WakaJadwalPelajaranController::class, 'duplicate'])->name('duplicate');
            Route::get('/{jadwalPelajaran}/edit', [WakaJadwalPelajaranController::class, 'edit'])->name('edit');
            Route::put('/{jadwalPelajaran}', [WakaJadwalPelajaranController::class, 'update'])->name('update');
            Route::delete('/{jadwalPelajaran}', [WakaJadwalPelajaranController::class, 'destroy'])->name('destroy');
            Route::post('/{jadwalPelajaran}/ganti-guru', [WakaJadwalPelajaranController::class, 'gantiGuru'])->name('ganti-guru');
            Route::post('/bulk-replace-guru', [WakaJadwalPelajaranController::class, 'bulkReplaceGuru'])->name('bulk-replace-guru');
            Route::post('/bulk-delete', [WakaJadwalPelajaranController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-update-status', [WakaJadwalPelajaranController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::get('/get-students/{kelas}', [WakaJadwalPelajaranController::class, 'getStudents'])->name('get-students');
            Route::get('/api/kelas/{kelas}', [WakaJadwalPelajaranController::class, 'getByKelas'])->name('api.by-kelas');
            Route::get('/api/guru/{guru}', [WakaJadwalPelajaranController::class, 'getByGuru'])->name('api.by-guru');
        });

        // Guru Pengajar (read-only, derived from Jadwal Pelajaran, filtered by waka's cabang)
        Route::prefix('guru-pengajar')->name('guru-pengajar.')->group(function () {
            Route::get('/', [WakaGuruPengajarController::class, 'index'])->name('index');
            Route::get('/print', [WakaGuruPengajarController::class, 'print'])->name('print');
            Route::post('/rebuild', [WakaGuruPengajarController::class, 'rebuildFromJadwal'])->name('rebuild');
            Route::get('/kelas/{kelas}', [WakaGuruPengajarController::class, 'manageKelas'])->name('manage-kelas');
            Route::get('/{guruPengajar}', [WakaGuruPengajarController::class, 'show'])->name('show');
        });

        // Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/wali-kelas', [WakilKepalaSekolahController::class, 'monitoringWaliKelas'])->name('wali-kelas');
            Route::get('/guru-pengajar', [WakilKepalaSekolahController::class, 'monitoringGuruPengajar'])->name('guru-pengajar');
            Route::get('/siswa', [WakilKepalaSekolahController::class, 'monitoringSiswa'])->name('siswa');

            // Monitoring LMS (cabang-scoped)
            Route::prefix('lms')->name('lms.')->group(function () {
                Route::get('/', [WakilKepalaSekolahController::class, 'lmsIndex'])->name('index');
                Route::get('/kelas/{kelas}', [WakilKepalaSekolahController::class, 'lmsKelas'])->name('kelas');
                Route::get('/preview/{type}/{id}', [WakilKepalaSekolahController::class, 'lmsPreview'])->name('preview');
                Route::post('/catatan', [WakilKepalaSekolahController::class, 'lmsKirimCatatan'])->name('catatan');
            });
        });

        // Catatan / Teguran
        Route::prefix('catatan')->name('catatan.')->group(function () {
            Route::get('/', [WakilKepalaSekolahController::class, 'catatanIndex'])->name('index');
            Route::get('/create', [WakilKepalaSekolahController::class, 'catatanCreate'])->name('create');
            Route::post('/', [WakilKepalaSekolahController::class, 'catatanStore'])->name('store');
            Route::delete('/{id}', [WakilKepalaSekolahController::class, 'catatanDestroy'])->name('destroy');
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
            Route::patch('/{id}/toggle-status', [WakaPengaturanIstirahatController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Promotion System Settings
        Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function() {
            // Note: We use Admin controllers for shared functionality to ensure consistency
            // Settings and KKM are defined at the end of this group

            // Report Access
            Route::get('/report', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'index'])->name('report');
            Route::get('/report/print', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'print'])->name('report.print');
            Route::post('/execute', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'execute'])->name('execute');

            // Individual/Batch Rollback
            Route::post('/rollback/{statusId}', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'rollback'])->name('rollback');
            Route::post('/rollback-selected', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'rollbackSelected'])->name('rollback-selected');

            // Promote Selected
            Route::post('/promote-selected', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'promoteSelected'])->name('promote-selected');

            // Scheduling
            Route::post('/cancel-schedule/{id}', [\App\Http\Controllers\Admin\Akademik\PromotionReportController::class, 'cancelSchedule'])->name('cancel-schedule');

            // Settings and KKM
            Route::resource('settings', \App\Http\Controllers\Admin\Akademik\PromotionSettingsController::class)->only(['index', 'store']);
            Route::resource('kkm', \App\Http\Controllers\Admin\Akademik\PromotionKKMController::class)->only(['index', 'store']);
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
            Route::get('/{id}', [SekretarisController::class, 'kalenderShow'])->name('show');
            Route::get('/{id}/edit', [SekretarisController::class, 'kalenderEdit'])->name('edit');
            Route::put('/{id}', [SekretarisController::class, 'kalenderUpdate'])->name('update');
            Route::delete('/{id}', [SekretarisController::class, 'kalenderDestroy'])->name('destroy');
            Route::post('/{id}/toggle-visibility', [SekretarisController::class, 'kalenderToggleVisibility'])->name('toggle-visibility');
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

            Route::get('/cetak-laporan', [TagihanController::class, 'cetakLaporan'])->name('cetak-laporan');

            // Tarik Tunggakan TA Lama → TA Aktif (carryover)
            Route::get('/carryover', [TagihanController::class, 'carryoverIndex'])->name('carryover');
            Route::post('/carryover/preview', [TagihanController::class, 'carryoverPreview'])->name('carryover.preview');
            Route::post('/carryover/execute', [TagihanController::class, 'carryoverExecute'])->name('carryover.execute');

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

            // Cetak kwitansi pembayaran
            Route::get('/{pembayaran}/cetak-kwitansi', [PembayaranController::class, 'cetakKwitansi'])->name('cetak-kwitansi');
        });

        // Config Pembayaran
        Route::redirect('info-pembayaran', '/bendahara/config', 301)->name('info-pembayaran.legacy-index');
        Route::post('info-pembayaran/update', [InfoPembayaranController::class, 'update'])->name('info-pembayaran.legacy-update');
        Route::prefix('config')->name('info-pembayaran.')->group(function () {
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

            // Pengaturan batas pembayaran
            Route::post('/batas-pembayaran', [BendaharaValidasiAksesController::class, 'updateBatasPembayaran'])->name('batas-pembayaran');

            // Dispensasi
            Route::post('/dispensasi', [BendaharaValidasiAksesController::class, 'ajukanDispensasi'])->name('dispensasi');
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

        // Promotion Validation (Overrides)
        Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function() {
            Route::get('/validation', [\App\Http\Controllers\Bendahara\PromotionValidationController::class, 'index'])->name('validation.index');
            Route::post('/validation', [\App\Http\Controllers\Bendahara\PromotionValidationController::class, 'store'])->name('validation.store');
            Route::post('/validation/bulk', [\App\Http\Controllers\Bendahara\PromotionValidationController::class, 'bulkStore'])->name('validation.bulk-store');
            Route::get('/validation/history', [\App\Http\Controllers\Bendahara\PromotionValidationController::class, 'history'])->name('validation.history');
            Route::post('/validation/history/bulk-delete', [\App\Http\Controllers\Bendahara\PromotionValidationController::class, 'bulkDeleteHistory'])->name('validation.history.bulk-delete');
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
            Route::get('/preview-bukti/{id}', [PresensiController::class, 'previewBukti'])->name('preview-bukti');
            Route::post('/validasi-izin/{id}', [PresensiController::class, 'prosesValidasiIzin'])->name('proses-validasi-izin');
            Route::get('/print-rekap', [PresensiController::class, 'printRekap'])->name('print-rekap');
            Route::get('/rekap-harian', [PresensiController::class, 'rekapHarian'])->name('rekap-harian');
            Route::get('/show-harian', [PresensiController::class, 'showHarian'])->name('show-harian');
            Route::get('/riwayat', [PresensiController::class, 'riwayat'])->name('riwayat');
            Route::put('/riwayat/{id}', [PresensiController::class, 'updateRiwayat'])->name('riwayat.update');
            // Import Excel
            Route::get('/download-template', [PresensiController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/import-excel', [PresensiController::class, 'importExcel'])->name('import-excel');
        });

        // Promotion Prediction
        Route::get('/kenaikan-kelas/prediction', [\App\Http\Controllers\WaliKelas\PromotionController::class, 'index'])->name('kenaikan-kelas.prediction');

        // Nilai Siswa
        Route::prefix('nilai')->name('nilai.')->group(function () {
            Route::get('/', [WaliKelasNilaiController::class, 'index'])->name('index');
            Route::get('/print', [WaliKelasNilaiController::class, 'print'])->name('print');
            Route::get('/{siswa}', [WaliKelasNilaiController::class, 'show'])->name('show');
            Route::get('/{siswa}/print', [WaliKelasNilaiController::class, 'printSiswa'])->name('print-siswa');
            Route::get('/{siswa}/edit', [WaliKelasNilaiController::class, 'edit'])->name('edit');
            Route::get('/{siswa}/download-template', [WaliKelasNilaiController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/{siswa}/import', [WaliKelasNilaiController::class, 'importExcel'])->name('import');
            Route::put('/{siswa}', [WaliKelasNilaiController::class, 'update'])->name('update');
            Route::post('/{nilaiId}/clear', [WaliKelasNilaiController::class, 'clearNilai'])->name('clear');
            Route::post('/{nilaiId}/sync-guru', [WaliKelasNilaiController::class, 'syncFromGuru'])->name('sync-guru');
        });

        // Rapor Pending Saya (lintas TA — untuk akses rapor draft TA lalu yang masih perlu diselesaikan)
        Route::get('/rapor-pending', [WaliKelasController::class, 'raporPending'])->name('rapor-pending');

        // Arsip Kelas Saya — read-only, lintas TA, untuk kelas yang pernah diwalikan
        Route::prefix('arsip')->name('arsip.')->group(function () {
            Route::get('/', [\App\Http\Controllers\WaliKelas\WaliKelasArsipController::class, 'index'])->name('index');
            Route::get('/{kelas}', [\App\Http\Controllers\WaliKelas\WaliKelasArsipController::class, 'show'])->name('show');
            Route::get('/{kelas}/rapor', [\App\Http\Controllers\WaliKelas\WaliKelasArsipController::class, 'rapor'])->name('rapor');
            Route::get('/{kelas}/presensi', [\App\Http\Controllers\WaliKelas\WaliKelasArsipController::class, 'presensi'])->name('presensi');
            Route::get('/{kelas}/nilai', [\App\Http\Controllers\WaliKelas\WaliKelasArsipController::class, 'nilai'])->name('nilai');
        });

        // Rapor
        Route::prefix('rapor')->name('rapor.')->group(function () {
            Route::get('/', [RaporController::class, 'index'])->name('index');
            Route::post('/generate-all', [RaporController::class, 'generateAll'])->name('generate-all');
            Route::post('/generate-single/{siswa}', [RaporController::class, 'generateSingle'])->name('generate-single'); // NEW - Per student
            Route::post('/create-with-mode', [RaporController::class, 'createWithMode'])->name('create-with-mode'); // NEW
            Route::get('/{rapor}/edit', [RaporController::class, 'edit'])->name('edit');
            Route::put('/{rapor}', [RaporController::class, 'update'])->name('update');
            Route::post('/{rapor}/terbitkan', [RaporController::class, 'terbitkan'])->name('terbitkan');
            Route::post('/{rapor}/tarik-kembali', [RaporController::class, 'tarikKembali'])->name('tarik-kembali');
            Route::delete('/{rapor}', [RaporController::class, 'destroy'])->name('destroy'); // NEW - Delete draft rapor
            Route::get('/{rapor}/preview', [RaporController::class, 'preview'])->name('preview');
            Route::get('/{rapor}/print', [RaporController::class, 'print'])->name('print');
            Route::post('/{rapor}/kehadiran-auto', [RaporController::class, 'autoFillKehadiran'])->name('kehadiran-auto'); // NEW
            Route::get('/{rapor}/export-excel', [RaporController::class, 'exportExcel'])->name('export-excel'); // NEW
            Route::post('/{rapor}/import-excel', [RaporController::class, 'importExcel'])->name('import-excel');
            Route::post('/{rapor}/apply-format', [RaporController::class, 'applyFormat'])->name('apply-format');
            Route::post('/apply-template', [RaporController::class, 'applyTemplate'])->name('apply-template'); // NEW
            Route::post('/apply-template-all', [RaporController::class, 'applyTemplateToAll'])->name('apply-template-all'); // NEW
            Route::post('/apply-template-batch', [RaporController::class, 'applyTemplateBatch'])->name('apply-template-batch'); // Terapkan template per-mapel ke seluruh kelas
            Route::post('/{rapor}/reset-nilai', [RaporController::class, 'resetNilai'])->name('reset-nilai');
            Route::post('/{rapor}/reorder-nilai', [RaporController::class, 'reorderNilai'])->name('reorder-nilai');
            // Kirim validasi ke Ketua PKBM
            Route::post('/{rapor}/kirim-validasi', [RaporController::class, 'kirimValidasi'])->name('kirim-validasi');
            Route::post('/{rapor}/batalkan-kirim-validasi', [RaporController::class, 'batalkanKirimValidasi'])->name('batalkan-kirim-validasi');
            Route::post('/kirim-validasi-semua', [RaporController::class, 'kirimValidasiSemua'])->name('kirim-validasi-semua');
            // Request download rapor
            Route::get('/request-download', [RaporController::class, 'requestDownloadIndex'])->name('request-download.index');
            Route::post('/request-download/{id}/approve', [RaporController::class, 'approveDownload'])->name('request-download.approve');
            Route::post('/request-download/{id}/reject', [RaporController::class, 'rejectDownload'])->name('request-download.reject');
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

        // Template Capaian (NEW)
        Route::prefix('template-capaian')->name('template-capaian.')->group(function () {
            Route::get('/', [\App\Http\Controllers\WaliKelas\TemplateCapaianController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\WaliKelas\TemplateCapaianController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\WaliKelas\TemplateCapaianController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\WaliKelas\TemplateCapaianController::class, 'destroy'])->name('destroy');
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

        // Jadwal Pelajaran
        Route::get('/jadwal', [\App\Http\Controllers\Guru\GuruJadwalController::class, 'index'])->name('jadwal.index');

        /*
        |--------------------------------------------------------------------------
        | LMS GURU - ARSIP (lintas TA, untuk reuse konten lama)
        | Diletakkan SEBELUM group lms/{kelas}/{mapel} agar tidak match wildcard.
        |--------------------------------------------------------------------------
        */
        Route::prefix('lms/arsip')->name('lms.arsip.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Guru\GuruLmsArsipController::class, 'index'])->name('index');
            Route::get('/preview/{type}/{id}', [\App\Http\Controllers\Guru\GuruLmsArsipController::class, 'preview'])->name('preview');
            Route::get('/salin/{type}/{id}', [\App\Http\Controllers\Guru\GuruLmsArsipController::class, 'formSalin'])->name('form-salin');
            Route::post('/salin', [\App\Http\Controllers\Guru\GuruLmsArsipController::class, 'salin'])->name('salin');
            Route::post('/salin-bulk', [\App\Http\Controllers\Guru\GuruLmsArsipController::class, 'salinBulk'])->name('salin-bulk');
        });

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
                Route::post('/{tugas}/koreksi/bulk', [GuruKoreksiController::class, 'bulkGrade'])->name('koreksi.bulk');
                Route::post('/{tugas}/koreksi/{submission}/ai-suggest', [GuruKoreksiController::class, 'getAiAssignmentSuggestion'])->name('koreksi.ai-suggest')->middleware('throttle:30,1');
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
                Route::get('/{ujian}/pengawasan', [GuruUjianController::class, 'pengawasan'])->name('pengawasan');
                Route::get('/{ujian}/pengawasan/data', [GuruUjianController::class, 'pengawasanData'])->name('pengawasan.data');
                Route::get('/{ujian}/koreksi/{ujianSiswa}', [GuruUjianController::class, 'koreksiShow'])->name('koreksi.show');
                Route::post('/{ujian}/koreksi/{ujianSiswa}', [GuruUjianController::class, 'koreksiStore'])->name('koreksi.store');
                Route::post('/{ujian}/koreksi/{soal}/ai-suggest', [GuruUjianController::class, 'getAiSuggestion'])->name('koreksi.ai-suggest')->middleware('throttle:30,1'); // Added

                // Manajemen Soal
                Route::get('/{ujian}/soal', [GuruUjianController::class, 'soal'])->name('soal.index');
                Route::get('/{ujian}/soal/create', [GuruUjianController::class, 'createSoal'])->name('soal.create');
                Route::post('/{ujian}/soal', [GuruUjianController::class, 'storeSoal'])->name('soal.store');
                Route::get('/{ujian}/soal/{soal}/edit', [GuruUjianController::class, 'editSoal'])->name('soal.edit');
                Route::put('/{ujian}/soal/{soal}', [GuruUjianController::class, 'updateSoal'])->name('soal.update');
                Route::delete('/{ujian}/soal/{soal}', [GuruUjianController::class, 'destroySoal'])->name('soal.destroy');

                // AI Question Bank Generator
                Route::post('/{ujian}/ai-generate-questions', [GuruUjianController::class, 'aiGenerateQuestions'])->name('soal.ai-generate')->middleware('throttle:15,1');
                Route::post('/{ujian}/bulk-store-soal', [GuruUjianController::class, 'bulkStoreSoal'])->name('soal.bulk-store');

                // Manajemen Soal (Bulk / Multi-Soal)
                Route::get('/{ujian}/manage-soal', [GuruUjianController::class, 'manageSoal'])->name('soal.manage');
                Route::post('/{ujian}/store-all-soal', [GuruUjianController::class, 'storeAllSoal'])->name('soal.storeAll');
                Route::post('/{ujian}/toggle-status', [GuruUjianController::class, 'toggleStatus'])->name('toggleStatus');
                Route::post('/{ujian}/toggle-result', [GuruUjianController::class, 'toggleResultVisibility'])->name('toggleResult');

                // Import/Export Soal
                Route::get('/{ujian}/soal-template', [GuruUjianController::class, 'downloadSoalTemplate'])->name('soal.template');
                Route::post('/{ujian}/import-soal', [GuruUjianController::class, 'importSoal'])->name('soal.import');
            });

            // Latihan (renamed from Kuis)
            Route::prefix('latihan')->name('latihan.')->group(function () {
                Route::get('/', [GuruUjianController::class, 'index'])->name('index');
                Route::get('/create', [GuruUjianController::class, 'create'])->name('create');
                Route::post('/', [GuruUjianController::class, 'store'])->name('store');
                Route::get('/{ujian}/edit', [GuruUjianController::class, 'edit'])->name('edit');
                Route::put('/{ujian}', [GuruUjianController::class, 'update'])->name('update');
                Route::delete('/{ujian}', [GuruUjianController::class, 'destroy'])->name('destroy');

                // Hasil & Koreksi Latihan
                Route::get('/{ujian}/hasil', [GuruUjianController::class, 'hasil'])->name('hasil');
                Route::get('/{ujian}/koreksi/{ujianSiswa}', [GuruUjianController::class, 'koreksiShow'])->name('koreksi.show');
                Route::post('/{ujian}/koreksi/{ujianSiswa}', [GuruUjianController::class, 'koreksiStore'])->name('koreksi.store');
                Route::post('/{ujian}/koreksi/{soal}/ai-suggest', [GuruUjianController::class, 'getAiSuggestion'])->name('koreksi.ai-suggest')->middleware('throttle:30,1'); // Added

                // Manajemen Soal Latihan
                Route::get('/{ujian}/soal', [GuruUjianController::class, 'soal'])->name('soal.index');
                Route::get('/{ujian}/soal/create', [GuruUjianController::class, 'createSoal'])->name('soal.create');
                Route::post('/{ujian}/soal', [GuruUjianController::class, 'storeSoal'])->name('soal.store');
                Route::get('/{ujian}/soal/{soal}/edit', [GuruUjianController::class, 'editSoal'])->name('soal.edit');
                Route::put('/{ujian}/soal/{soal}', [GuruUjianController::class, 'updateSoal'])->name('soal.update');
                Route::delete('/{ujian}/soal/{soal}', [GuruUjianController::class, 'destroySoal'])->name('soal.destroy');

                // AI Question Bank Generator
                Route::post('/{ujian}/ai-generate-questions', [GuruUjianController::class, 'aiGenerateQuestions'])->name('soal.ai-generate')->middleware('throttle:15,1');
                Route::post('/{ujian}/bulk-store-soal', [GuruUjianController::class, 'bulkStoreSoal'])->name('soal.bulk-store');

                // Manajemen Soal Latihan (Bulk)
                Route::get('/{ujian}/manage-soal', [GuruUjianController::class, 'manageSoal'])->name('soal.manage');
                Route::post('/{ujian}/store-all-soal', [GuruUjianController::class, 'storeAllSoal'])->name('soal.storeAll');
                Route::post('/{ujian}/toggle-status', [GuruUjianController::class, 'toggleStatus'])->name('toggleStatus');
                Route::post('/{ujian}/toggle-result', [GuruUjianController::class, 'toggleResultVisibility'])->name('toggleResult');

                // Import/Export Soal Latihan
                Route::get('/{ujian}/soal-template', [GuruUjianController::class, 'downloadSoalTemplate'])->name('soal.template');
                Route::post('/{ujian}/import-soal', [GuruUjianController::class, 'importSoal'])->name('soal.import');
            });

            // Nilai Siswa
            Route::get('/nilai', [GuruNilaiController::class, 'index'])->name('nilai.index');
            Route::post('/nilai/update', [GuruNilaiController::class, 'update'])->name('nilai.update');
            Route::post('/nilai/update-batch', [GuruNilaiController::class, 'updateBatch'])->name('nilai.updateBatch');
            Route::post('/nilai/recalculate', [GuruNilaiController::class, 'recalculate'])->name('nilai.recalculate');
            Route::get('/nilai/export-excel', [GuruNilaiController::class, 'exportExcel'])->name('nilai.export-excel');
            Route::get('/nilai/download-template', [GuruNilaiController::class, 'downloadTemplate'])->name('nilai.download-template');
            Route::post('/nilai/import-excel', [GuruNilaiController::class, 'importExcel'])->name('nilai.import-excel');

            // Forum Diskusi
            Route::prefix('forum')->name('forum.')->group(function () {
                Route::get('/', [GuruForumController::class, 'index'])->name('index');
                Route::get('/create', [GuruForumController::class, 'create'])->name('create');
                Route::post('/', [GuruForumController::class, 'store'])->name('store');
                Route::get('/{forum}', [GuruForumController::class, 'show'])->name('show');
                Route::post('/{forum}/reply', [GuruForumController::class, 'reply'])->name('reply');
                Route::put('/{forum}/reply/{reply}', [GuruForumController::class, 'updateReply'])->name('reply.update');
                Route::delete('/{forum}/reply/{reply}', [GuruForumController::class, 'destroyReply'])->name('reply.destroy');
                Route::patch('/{forum}/pin', [GuruForumController::class, 'togglePin'])->name('togglePin');
                Route::patch('/{forum}/close', [GuruForumController::class, 'toggleClose'])->name('toggleClose');
                Route::delete('/{forum}', [GuruForumController::class, 'destroy'])->name('destroy');
            });

            // Meeting / Kelas Virtual
            Route::prefix('meeting')->name('meeting.')->group(function () {
                Route::get('/', [App\Http\Controllers\Guru\GuruLmsMeetingController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Guru\GuruLmsMeetingController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Guru\GuruLmsMeetingController::class, 'store'])->name('store');
                Route::get('/{meeting}/edit', [App\Http\Controllers\Guru\GuruLmsMeetingController::class, 'edit'])->name('edit');
                Route::put('/{meeting}', [App\Http\Controllers\Guru\GuruLmsMeetingController::class, 'update'])->name('update');
                Route::delete('/{meeting}', [App\Http\Controllers\Guru\GuruLmsMeetingController::class, 'destroy'])->name('destroy');
            });
        });

        // Catatan Monitoring (notifikasi dari Kepsek/Wakepsek/Admin)
        Route::prefix('lms/catatan-monitoring')->name('lms.catatan-monitoring.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Guru\GuruCatatanMonitoringController::class, 'index'])->name('index');
            Route::get('/{catatan}', [\App\Http\Controllers\Guru\GuruCatatanMonitoringController::class, 'show'])->name('show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SISWA DASHBOARD & ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:siswa', 'student.active'])->prefix('siswa')->name('siswa.')->group(function () {

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

                // Note: Routes ajukan izin di-disable - Fitur dipindahkan ke Wali Siswa
                // Siswa tidak bisa mengajukan izin sendiri, harus melalui wali siswa sebagai bentuk pendampingan
                // Route::get('/ajukan-izin', [SiaPresensiController::class, 'ajukanIzin'])->name('ajukan-izin');
                // Route::post('/ajukan-izin', [SiaPresensiController::class, 'storeIzin'])->name('store-izin');
            });

            // Penilaian Harian
            Route::get('/penilaian', [SiaDashboardController::class, 'penilaian'])->name('penilaian');

            // Pembayaran
            Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
                Route::get('/', [SiaPembayaranController::class, 'index'])->name('index');
                Route::post('/bayar', [SiaPembayaranController::class, 'prosesBayar'])->name('bayar');
                Route::get('/riwayat', [SiaPembayaranController::class, 'riwayat'])->name('riwayat');
                Route::get('/cetak/{pembayaran}', [SiaPembayaranController::class, 'cetakBukti'])->name('cetak');
                // Pembayaran digital hanya dilakukan wali siswa.
            });

            // Rapor - DISABLED: Siswa tidak berhak mengelola rapor, hanya wali siswa
            // Route::prefix('rapor')->name('rapor.')->group(function () {
            //     Route::get('/', [SiaRaporController::class, 'index'])->name('index');
            //     Route::get('/tengah-semester/{rapor}', [SiaRaporController::class, 'tengahSemester'])->name('tengah-semester');
            //     Route::get('/akhir-semester/{rapor}', [SiaRaporController::class, 'akhirSemester'])->name('akhir-semester');
            //     Route::get('/download/{rapor}', [SiaRaporController::class, 'download'])->name('download');
            // });
        });

        /*
        |--------------------------------------------------------------------------
        | LMS (Learning Management System) Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('lms')->name('lms.')->middleware('lms.access')->group(function () {

            // Dashboard LMS
            Route::get('/dashboard', [LmsDashboardController::class, 'index'])->name('dashboard');

            // Kalender Akademik
            Route::get('/kalender', [SiswaDashboardController::class, 'kalenderTahunan'])->name('kalender');
            Route::get('/kalender/{tanggal}', [SiswaDashboardController::class, 'kalenderDetail'])->name('kalender.detail');

            // Pengumuman
            Route::get('/pengumuman', [LmsDashboardController::class, 'pengumumanIndex'])->name('pengumuman.index');
            Route::get('/pengumuman/{id}', [LmsDashboardController::class, 'pengumumanDetail'])->name('pengumuman.show');

            // Jadwal Pelajaran
            Route::get('/jadwal', [LmsDashboardController::class, 'jadwal'])->name('jadwal');
            Route::get('/jadwal/print', [LmsDashboardController::class, 'printJadwal'])->name('jadwal.print');

            // Daftar Guru
            Route::get('/guru', [LmsDashboardController::class, 'guru'])->name('guru');

            // Mata Pelajaran
            Route::prefix('mata-pelajaran')->name('mapel.')->middleware('siswa.mapel.access')->group(function () {

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
                    Route::post('/{ujianId}/retake', [LmsUjianController::class, 'retake'])->name('retake');
                    Route::post('/{ujianId}/autosave', [LmsUjianController::class, 'autosave'])->name('autosave');
                    Route::post('/{ujianId}/monitoring', [LmsUjianController::class, 'monitoring'])->name('monitoring');
                    Route::get('/{ujianId}/review', [LmsUjianController::class, 'review'])->name('review');
                });

                // Latihan
                Route::prefix('{mapelId}/latihan')->name('latihan.')->group(function () {
                    Route::get('/{ujianId}', [LmsUjianController::class, 'show'])->name('show');
                    Route::post('/{ujianId}/mulai', [LmsUjianController::class, 'mulai'])->name('mulai');
                    Route::post('/{ujianId}/submit', [LmsUjianController::class, 'submit'])->name('submit');
                    Route::post('/{ujianId}/retake', [LmsUjianController::class, 'retake'])->name('retake');
                    Route::post('/{ujianId}/autosave', [LmsUjianController::class, 'autosave'])->name('autosave');
                    Route::get('/{ujianId}/review', [LmsUjianController::class, 'review'])->name('review');
                });

                // Forum Diskusi
                Route::prefix('{mapelId}/forum')->name('forum.')->group(function () {
                    Route::get('/', [App\Http\Controllers\Siswa\LmsForumController::class, 'index'])->name('index');
                    Route::get('/create', [App\Http\Controllers\Siswa\LmsForumController::class, 'create'])->name('create');
                    Route::post('/', [App\Http\Controllers\Siswa\LmsForumController::class, 'store'])->name('store');
                    Route::get('/{diskusiId}', [App\Http\Controllers\Siswa\LmsForumController::class, 'show'])->name('show');
                    Route::post('/{diskusiId}/reply', [App\Http\Controllers\Siswa\LmsForumController::class, 'reply'])->name('reply');
                    Route::put('/{diskusiId}/reply/{replyId}', [App\Http\Controllers\Siswa\LmsForumController::class, 'updateReply'])->name('reply.update');
                    Route::delete('/{diskusiId}/reply/{replyId}', [App\Http\Controllers\Siswa\LmsForumController::class, 'destroyReply'])->name('reply.destroy');
                });

                // Meeting / Kelas Virtual
                Route::get('{mapelId}/meeting', [App\Http\Controllers\Siswa\SiswaLmsMeetingController::class, 'index'])->name('meeting.index');
            });

            // Daftar Semua Tugas (Global)
            Route::get('/tugas', [LmsTugasController::class, 'indexAll'])->name('tugas.index');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | WALI SISWA DASHBOARD & ROUTES
    | Note: Wali siswa yang bertanggung jawab untuk pembayaran & monitoring anak
    | Siswa hanya fokus belajar, tidak ada akses pembayaran (mencegah penyembunyian info)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:orang_tua'])->prefix('wali-siswa')->name('wali-siswa.')->group(function () {

        // Dashboard Wali Siswa
        Route::get('/dashboard', [OrangTuaController::class, 'dashboard'])->name('dashboard');

        // Tagihan & Pembayaran Anak
        Route::prefix('tagihan')->name('tagihan.')->group(function () {
            Route::get('/anak/{siswa}', [OrangTuaController::class, 'tagihanAnak'])->name('anak');
            Route::post('/anak/{siswa}/bayar', [PembayaranDigitalController::class, 'prosesBayar'])->name('bayar');
            Route::post('/anak/{siswa}/bulk-pay', [PembayaranDigitalController::class, 'processBulkPay'])->name('bulk-pay');
        });

        // Pembayaran Digital
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/digital/{pembayaran}', [PembayaranDigitalController::class, 'digitalPayment'])->name('digital');
            Route::post('/sync/{pembayaran}', [PembayaranDigitalController::class, 'syncDigitalPayment'])->name('sync');
            Route::post('/continue/{pembayaran}', [PembayaranDigitalController::class, 'continuePayment'])->name('continue');
            Route::get('/{pembayaran}/invoice', [OrangTuaController::class, 'cetakInvoice'])->name('invoice');
        });

        // Monitoring Rapor Anak
        Route::prefix('rapor')->name('rapor.')->group(function () {
            Route::get('/anak/{siswa}', [OrangTuaController::class, 'raporAnak'])->name('anak');
            Route::get('/detail/{rapor}', [OrangTuaController::class, 'detailRapor'])->name('detail');
            Route::post('/request-download/{rapor}', [OrangTuaController::class, 'requestDownloadRapor'])->name('request-download');
            Route::get('/download/{token}', [OrangTuaController::class, 'downloadRapor'])->name('download');
        });

        // Monitoring Presensi & Pengajuan Izin Anak
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/anak/{siswa}', [OrangTuaController::class, 'presensiAnak'])->name('anak');
            Route::get('/anak/{siswa}/ajukan-izin', [OrangTuaController::class, 'ajukanIzin'])->name('ajukan-izin');
            Route::post('/anak/{siswa}/store-izin', [OrangTuaController::class, 'storeIzin'])->name('store-izin');
            Route::get('/anak/{siswa}/riwayat-presensi', [OrangTuaController::class, 'riwayatPresensi'])->name('riwayat-presensi');
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
        Route::put('/security', [App\Http\Controllers\AccountController::class, 'updateSecurity'])->name('update-security');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'index'])->name('index');
        Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::post('/upload-foto', [App\Http\Controllers\ProfileController::class, 'uploadFoto'])->name('upload-foto');
        Route::delete('/delete-foto', [App\Http\Controllers\ProfileController::class, 'deleteFoto'])->name('delete-foto');
    });

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI (Semua Role)
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [App\Http\Controllers\NotificationController::class, 'recent'])->name('recent');
        Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/today', [App\Http\Controllers\NotificationController::class, 'today'])->name('today');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/bulk-action', [App\Http\Controllers\NotificationController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | FALLBACK DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
