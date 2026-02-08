<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;

class CheckSiswaMapelAccess
{
    /**
     * Cek apakah siswa memiliki akses ke mata pelajaran berdasarkan jadwal.
     * Mencegah siswa mengakses mapel yang tidak di-assign via URL manipulation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $mapelId = $request->route('mapelId');

        if (!$mapelId) {
            return $next($request);
        }

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa || !$siswa->kelas_id) {
            abort(403, 'Data siswa tidak valid.');
        }

        // Cek apakah mapel ini ada di jadwal untuk kelas siswa
        $hasAccess = JadwalPelajaran::where('mata_pelajaran_id', $mapelId)
            ->whereHas('kelas', fn($q) => $q->where('kelas.id', $siswa->kelas_id))
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        // Cek filter agama
        $mataPelajaran = MataPelajaran::find($mapelId);
        if ($mataPelajaran && !$siswa->canAccessMapel($mataPelajaran)) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Akses ditolak: Mata pelajaran ini tidak sesuai dengan agama Anda.');
        }

        return $next($request);
    }
}
