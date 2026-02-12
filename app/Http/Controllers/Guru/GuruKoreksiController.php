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
            ->get()
            ->filter(function($submission) use ($mataPelajaran) {
                 return $submission->siswa && $submission->siswa->canAccessMapel($mataPelajaran);
            });

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
            'tugasSiswa' => $submission,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Simpan nilai
     */
    /**
     * Simpan nilai
     */
    public function store(Request $request, $kelasId, $mapelId, $tugasId, $submissionId): RedirectResponse
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
            ->route('guru.lms.tugas.koreksi', [$kelasId, $mapelId, $tugasId])
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
            ->route('guru.lms.tugas.koreksi', [$kelasId, $mapelId, $tugasId])
            ->with('success', count($validated['siswa_ids']) . ' siswa berhasil dinilai');
    }

    /**
     * Get AI Suggestion for Assignment Grading (Multimodal)
     */
    public function getAiAssignmentSuggestion(Request $request, $kelasId, $mapelId, $tugasId, $submissionId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $submission = TugasSiswa::findOrFail($submissionId);
        $tugas = Tugas::findOrFail($tugasId);

        $aiService = new \App\Services\AiGradingService();

        // Check if there is a file and if it's an image
        if ($submission->file_jawaban) {
            $path = storage_path('app/public/' . $submission->file_jawaban);
            
            // Check mime type
            if (file_exists($path)) {
                $mime = mime_content_type($path);
                if (str_starts_with($mime, 'image/')) {
                    // Vision AI
                    $result = $aiService->evaluateImage(
                        $tugas->judul_tugas . "\n\n" . $tugas->deskripsi,
                        $path,
                        $tugas->deskripsi // Use description as context/key
                    );
                    return response()->json($result);
                }
            }
        }

        // Fallback to text if no image or text-only submission
        if ($submission->jawaban_text) {
             $result = $aiService->evaluate(
                $tugas->judul_tugas . "\n\n" . $tugas->deskripsi,
                $submission->jawaban_text,
                $tugas->deskripsi // Context
            );
            return response()->json($result);
        }

        return response()->json([
            'error' => true,
            'feedback' => 'Tidak ada jawaban teks atau gambar yang valid untuk dianalisis AI.'
        ]);
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