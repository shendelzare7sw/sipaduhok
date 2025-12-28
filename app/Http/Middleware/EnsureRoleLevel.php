<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $level  Maximum level allowed (lower number = higher privilege)
     */
    public function handle(Request $request, Closure $next, int $level): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Check if user has role_id (new system)
        if ($user->role_id && $user->role) {
            if ($user->role->level > $level) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
        } else {
            // Fallback: If no role_id, deny access for security
            abort(403, 'Akses ditolak. Role Anda belum dikonfigurasi dengan benar.');
        }

        return $next($request);
    }
}
