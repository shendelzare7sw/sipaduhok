<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect authenticated users based on their role
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->isKetuaPKBM()) {
                    return redirect()->route('ketua.dashboard');
                } elseif ($user->isSekretaris()) {
                    return redirect()->route('sekretaris.dashboard');
                } elseif ($user->isBendahara()) {
                    return redirect()->route('bendahara.dashboard');
                } elseif ($user->isWaliKelas()) {
                    return redirect()->route('wali.dashboard');
                } elseif ($user->isGuruPengajar()) {
                    return redirect()->route('guru.dashboard');
                } elseif ($user->isSiswa()) {
                    return redirect()->route('siswa.dashboard');
                } elseif ($user->isOrangTua()) {
                    return redirect()->route('orang-tua.dashboard');
                }

                // Default fallback - redirect to general dashboard
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
