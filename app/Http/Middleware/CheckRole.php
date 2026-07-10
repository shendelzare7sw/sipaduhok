<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Now supports multiple roles!
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Multiple roles can be passed
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // IMPORTANT: Admin has access to ALL routes (level 1 = highest access)
        if ($user->isAdmin()) {
            return $next($request);
        }

        // For non-admin users, check if their role matches
        $userRole = null;

        // Try new role system first (role_id relationship)
        if ($user->role_id && $user->roleRelation) {
            $userRole = $user->roleRelation->name;
        }
        // Fallback to old role field
        elseif ($user->role) {
            $userRole = $user->role;
        }

        // Check if user role matches any of the allowed roles
        if (!$userRole || !in_array($userRole, $roles)) {
            // Auto-logout untuk kemudahan testing
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // F-06: pesan generik — jangan bocorkan role user maupun role yang dibutuhkan.
            return redirect()->route('login')->with('error',
                'Anda tidak memiliki akses ke halaman ini.'
            );
        }

        return $next($request);
    }
}