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
                // Alumni read-only access: dashboard, riwayat LMS (semua tugas/ujian lampau),
                // dan logout. Tidak boleh akses LMS aktif (kerjakan tugas, ikut ujian baru, dll).
                $currentRoute = $request->route()?->getName();
                $allowedExact = [
                    'siswa.sia.dashboard',
                    'siswa.alumni.dashboard',
                    'logout',
                    'profile.show', // Lihat profil sendiri
                    'profile.update',
                    'notifications.index',
                ];

                $allowedPrefixes = [
                    'siswa.lms.riwayat.', // Riwayat LMS lintas TA
                ];

                $isAllowed = in_array($currentRoute, $allowedExact, true);
                if (!$isAllowed && $currentRoute) {
                    foreach ($allowedPrefixes as $prefix) {
                        if (str_starts_with($currentRoute, $prefix)) {
                            $isAllowed = true;
                            break;
                        }
                    }
                }

                if (!$isAllowed) {
                    return redirect()->route('siswa.sia.dashboard')
                        ->with('info', 'Sebagai alumni, akses Anda terbatas pada riwayat akademik.');
                }
            }
        }

        return $next($request);
    }
}
