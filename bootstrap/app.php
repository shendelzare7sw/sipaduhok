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
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
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
        //
    })->create();
