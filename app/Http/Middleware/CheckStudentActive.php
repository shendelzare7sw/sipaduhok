<?php

namespace App\Http\Middleware;

use App\Models\Siswa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Efek status siswa (siswa.status) pada area /siswa:
 * - aktif  : akses penuh.
 * - lulus  : alumni, hanya dashboard alumni (read-only) — rute lain dialihkan ke sana.
 * - pindah / keluar : tidak lagi menjadi peserta didik; diperlakukan seperti akun nonaktif
 *   (sesi diakhiri), walau users.is_active masih 1 (mis. data hasil impor).
 * Status akun (users.is_active = 0) sudah ditangani EnsureUserIsActive untuk semua role.
 */
class CheckStudentActive
{
    /** Rute /siswa yang tetap boleh dibuka alumni. siswa.dashboard hanya pengalih ke dashboard SIA. */
    private const RUTE_ALUMNI = [
        'siswa.dashboard',
        'siswa.sia.dashboard',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->isSiswa()) {
            return $next($request);
        }

        $siswa = Siswa::termasukNonaktif()->where('user_id', $user->id)->first();

        if ($siswa && in_array($siswa->status, ['pindah', 'keluar'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->view('errors.account_inactive');
        }

        if ($siswa && $siswa->status === 'lulus' && ! $request->routeIs(...self::RUTE_ALUMNI)) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('info', 'Sebagai alumni, akses Anda terbatas pada riwayat akademik.');
        }

        return $next($request);
    }
}
