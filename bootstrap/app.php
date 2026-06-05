<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust Cloudflare Tunnel proxy headers (X-Forwarded-Proto, X-Forwarded-For, etc.)
        // Without this, Laravel sees HTTP instead of HTTPS, causing CSRF mismatch (419)
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RejectEmailHeaderInjection::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\CheckAdminSecuritySetup::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'role.level' => \App\Http\Middleware\EnsureRoleLevel::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'lms.access' => \App\Http\Middleware\CheckLmsAccess::class,
            'siswa.mapel.access' => \App\Http\Middleware\CheckSiswaMapelAccess::class,
            'student.active' => \App\Http\Middleware\CheckStudentActive::class,
        ]);

        // Exclude Midtrans webhook from CSRF verification
        // This is required because Midtrans sends POST requests from external servers
        $middleware->validateCsrfTokens(except: [
            'midtrans/*',
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('recovery/reset/*') || $request->is('recovery')) {
                return redirect()->back()->with('error', 'Sesi verifikasi telah berakhir demi keamanan. Halaman telah disegarkan otomatis, silakan coba lagi.');
            }
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
        });

        // Handle other 419 cases if any
        $exceptions->respond(function (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419) {
                return redirect()->back()->with('error', 'Sesi telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.');
            }
            return $response;
        });
    })
    ->withSchedule(function ($schedule) {
        // Google Sheets Sync Scheduler
        if (!config('google-sheets.enabled')) {
            return;
        }
        
        // Tier 1: Daily sync at 00:30 (6 modules)
        // Modules: siswa, guru, kelas, jadwal_pelajaran, presensi, nilai
        $tier1Modules = ['siswa', 'guru', 'kelas', 'jadwal_pelajaran', 'presensi', 'nilai'];
        foreach ($tier1Modules as $module) {
            $schedule->job(\App\Jobs\SyncModuleToSheet::class, 'default', [
                'module' => $module,
                'direction' => 'push'
            ])
                ->dailyAt('00:30')
                ->name('google-sheets-sync-' . $module . '-daily')
                ->onOneServer()
                ->withoutOverlapping(600);
        }
        
        // Tier 2: Weekly sync on Sunday at 01:00 (4 modules)
        // Modules: tagihan, pembayaran, siswa_belum_lunas, rekap_keuangan
        $tier2Modules = ['tagihan', 'pembayaran', 'siswa_belum_lunas', 'rekap_keuangan'];
        foreach ($tier2Modules as $module) {
            $schedule->job(\App\Jobs\SyncModuleToSheet::class, 'default', [
                'module' => $module,
                'direction' => 'push'
            ])
                ->weeklyOn(0, '01:00')  // 0 = Sunday
                ->name('google-sheets-sync-' . $module . '-weekly')
                ->onOneServer()
                ->withoutOverlapping(600);
        }
    })->create();
