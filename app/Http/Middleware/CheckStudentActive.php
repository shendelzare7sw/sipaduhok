<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStudentActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is authenticated and has 'siswa' role
        if ($user && $user->isSiswa()) {
            $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();

            if ($siswa && $siswa->status === 'lulus') {
                // List of allowed routes for alumni (Dashboard, Logout)
                $allowedRoutes = [
                    'siswa.sia.dashboard',
                    'logout',
                    'siswa.alumni.dashboard' // Alias if any
                ];

                // Check if current route is NOT in allowed list
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    // Redirect to alumni dashboard (which will handle auto-logout)
                    return redirect()->route('siswa.sia.dashboard');
                }
            }
        }

        return $next($request);
    }
}
