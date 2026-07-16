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
        // Proxy tepercaya HANYA loopback (127.0.0.1/::1).
        // Arsitektur: Cloudflare (proxied) → Nginx → PHP-FPM di VPS yang sama.
        // IP asli pengunjung disediakan oleh Nginx real_ip (set_real_ip_from <rentang
        // Cloudflare> + real_ip_header CF-Connecting-IP) → REMOTE_ADDR sudah = IP asli.
        // Karena itu Laravel cukup memercayai loopback: request memakai REMOTE_ADDR (IP asli,
        // tak bisa dipalsukan via X-Forwarded-For dari luar). Deteksi HTTPS dari
        // `fastcgi_param HTTPS on` di blok SSL Nginx (Cloudflare SSL mode = Full/Strict).
        // JANGAN pakai 'at: *' (berbahaya: siapa pun bisa memalsukan header IP).
        // Penting: firewall origin agar 80/443 HANYA menerima IP Cloudflare (cegah bypass).
        $middleware->trustProxies(at: ['127.0.0.1', '::1'], headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
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
    })->create();
