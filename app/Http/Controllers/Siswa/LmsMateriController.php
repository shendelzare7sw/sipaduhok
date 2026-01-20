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

        // Ambil semua pertemuan dengan eager loading resources
        $pertemuans = \App\Models\Pertemuan::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['materi', 'tugas', 'ujian', 'forumDiskusi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil resource yang TIDAK terkait pertemuan (General Resources)
        $materiList = Materi::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->whereNull('pertemuan_id')
            ->orderBy('tanggal_upload', 'desc')
            ->get();

        $tugasList = Tugas::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->whereNull('pertemuan_id')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $ujianList = Ujian::where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->whereNull('pertemuan_id')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('siswa.lms.mata-pelajaran.show', compact(
            'siswa',
            'mataPelajaran',
            'pertemuans',
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