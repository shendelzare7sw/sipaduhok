<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Ujian;

class LmsMateriController extends Controller
{
    /**
     * Tampilkan detail mata pelajaran
     */
    public function show($mapelId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        // Cek akses agama
        if (!$siswa->canAccessMapel($mataPelajaran)) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Akses ditolak: Mata pelajaran ini tidak sesuai dengan agama Anda.');
        }

        // Ambil semua materi
        $materiList = Materi::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->orderBy('tanggal_upload', 'desc')
            ->get();

        $tugasList = Tugas::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $ujianList = Ujian::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('siswa.lms.mata-pelajaran.show', compact(
            'siswa',
            'mataPelajaran',
            'materiList',
            'tugasList',
            'ujianList'
        ));
    }

    /**
     * Lihat detail materi
     */
    public function lihatMateri($mapelId, $materiId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $materi = Materi::where('id', $materiId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['mataPelajaran', 'guru'])
            ->firstOrFail();

        // Cek akses agama
        if (!$siswa->canAccessMapel($materi->mataPelajaran)) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Akses ditolak: Mata pelajaran ini tidak sesuai dengan agama Anda.');
        }

        return view('siswa.lms.mata-pelajaran.materi', compact('siswa', 'materi'));
    }

    /**
     * Forum diskusi
     */
    public function forum($mapelId)
    {
        // Placeholder - Forum akan dikembangkan lebih lanjut
        return back()->with('info', 'Fitur forum diskusi sedang dalam pengembangan');
    }

    /**
     * Post ke forum
     */
    public function postForum(Request $request, $mapelId)
    {
        // Placeholder - Forum akan dikembangkan lebih lanjut
        return back()->with('info', 'Fitur forum diskusi sedang dalam pengembangan');
    }
}
