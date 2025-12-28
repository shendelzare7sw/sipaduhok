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
        if ($user->role_id && $user->roleRelation) {
            $userRole = $user->roleRelation->name;
        } else {
            $userRole = $user->attributes['role'] ?? null;
        }

        // Check if user role matches any of the allowed roles
        if (!in_array($userRole, $roles)) {
            // Auto-logout untuk kemudahan testing
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error',
                'Anda tidak memiliki akses ke halaman ini. Role Anda: ' . ($userRole ?? 'N/A') . '. Role yang dibutuhkan: ' . implode(', ', $roles)
            );
        }

        return $next($request);
    }
}