<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Tugas;
use App\Models\TugasSiswa;
use App\Models\Siswa;

class GuruKoreksiController extends Controller
{
    /**
     * Tampilkan daftar siswa yang mengumpulkan tugas
     */
    public function index($kelasId, $mapelId, $tugasId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $tugas = Tugas::findOrFail($tugasId);

        $submissions = TugasSiswa::with('siswa')
            ->where('tugas_id', $tugasId)
            ->get();

        // Statistik
        $stats = [
            'total' => $submissions->count(),
            'belum_dikerjakan' => $submissions->where('status', 'belum_dikerjakan')->count(),
            'dikerjakan' => $submissions->where('status', 'dikerjakan')->count(),
            'terlambat' => $submissions->where('status', 'terlambat')->count(),
            'dinilai' => $submissions->where('status', 'dinilai')->count(),
        ];

        $submitted = $submissions->where('status', '!=', 'belum_dikerjakan')->count();
        $belumDinilai = $submissions->whereIn('status', ['dikerjakan', 'terlambat'])->count();

        return view('guru.lms.tugas.koreksi', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'tugas' => $tugas,
            'daftarSiswa' => $submissions,
            'stats' => $stats,
            'guru' => $tenagaPendidik,
            'submitted' => $submitted,
            'belumDinilai' => $belumDinilai,
        ]);
    }

    /**
     * Form koreksi individual
     */
    public function show($kelasId, $mapelId, $tugasId, $submissionId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $tugas = Tugas::findOrFail($tugasId);
        $submission = TugasSiswa::with('siswa')->findOrFail($submissionId);

        return view('guru.lms.tugas.koreksi-show', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'tugas' => $tugas,
            'submission' => $submission,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Simpan nilai
     */
    public function grade(Request $request, $kelasId, $mapelId, $tugasId, $submissionId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback_guru' => 'nullable|string',
        ]);

        $submission = TugasSiswa::findOrFail($submissionId);
        $submission->update([
            'nilai' => $validated['nilai'],
            'feedback_guru' => $validated['feedback_guru'],
            'status' => 'dinilai',
        ]);

        return redirect()
            ->route('guru.lms.koreksi.index', [$kelasId, $mapelId, $tugasId])
            ->with('success', 'Nilai berhasil disimpan');
    }

    /**
     * Bulk grading (beri nilai yang sama untuk beberapa siswa)
     */
    public function bulkGrade(Request $request, $kelasId, $mapelId, $tugasId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:tugas_siswa,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback_guru' => 'nullable|string',
        ]);

        TugasSiswa::whereIn('id', $validated['siswa_ids'])
            ->update([
                'nilai' => $validated['nilai'],
                'feedback_guru' => $validated['feedback_guru'],
                'status' => 'dinilai',
            ]);

        return redirect()
            ->route('guru.lms.koreksi.index', [$kelasId, $mapelId, $tugasId])
            ->with('success', count($validated['siswa_ids']) . ' siswa berhasil dinilai');
    }

    /**
     * Verifikasi akses guru
     */
    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        $access = GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();

        if (!$access) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }
}