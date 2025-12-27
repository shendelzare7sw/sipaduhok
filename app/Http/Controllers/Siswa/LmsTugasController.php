<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Models\TugasSiswa;

class LmsTugasController extends Controller
{
    /**
     * Tampilkan detail tugas
     */
    public function show($mapelId, $tugasId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $tugas = Tugas::where('id', $tugasId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['mataPelajaran', 'guru'])
            ->firstOrFail();

        // Cek apakah sudah submit
        $tugasSiswa = TugasSiswa::where('tugas_id', $tugasId)
            ->where('siswa_id', $siswa->id)
            ->first();

        // Cek apakah sudah deadline
        $isDeadline = now()->gt($tugas->tanggal_deadline);

        return view('siswa.lms.mata-pelajaran.tugas.show', compact(
            'siswa',
            'tugas',
            'tugasSiswa',
            'isDeadline'
        ));
    }

    /**
     * Submit tugas
     */
    public function submit(Request $request, $mapelId, $tugasId)
    {
        $request->validate([
            'jawaban_text' => 'nullable|string',
            'file_jawaban' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,mp4|max:10240',
        ]);

        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $tugas = Tugas::findOrFail($tugasId);

        // Cek deadline
        if (now()->gt($tugas->tanggal_deadline)) {
            return back()->with('error', 'Waktu pengumpulan tugas sudah habis');
        }

        // Upload file jika ada
        $filePath = null;
        if ($request->hasFile('file_jawaban')) {
            $filePath = $request->file('file_jawaban')->store('tugas/jawaban', 'public');
        }

        // Simpan atau update jawaban
        TugasSiswa::updateOrCreate(
            [
                'tugas_id' => $tugasId,
                'siswa_id' => $siswa->id,
            ],
            [
                'jawaban_text' => $request->jawaban_text,
                'file_jawaban' => $filePath ?? null,
                'tanggal_submit' => now(),
                'status' => now()->gt($tugas->tanggal_deadline) ? 'terlambat' : 'dikerjakan',
            ]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}