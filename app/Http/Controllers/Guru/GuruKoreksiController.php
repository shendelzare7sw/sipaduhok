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
use App\Services\NotificationService;
use Spatie\PdfToText\Pdf;
use Spatie\PdfToImage\Pdf as PdfToImage;

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
        $tugas = $this->authorizedTugas($kelasId, $mapelId, $tugasId);

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
        $tugas = $this->authorizedTugas($kelasId, $mapelId, $tugasId);
        $submission = $this->authorizedSubmission($submissionId, $tugas);

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

        $tugas = $this->authorizedTugas($kelasId, $mapelId, $tugasId);
        $submission = $this->authorizedSubmission($submissionId, $tugas);
        $submission->update([
            'nilai' => $validated['nilai'],
            'feedback_guru' => $validated['feedback_guru'] ?? null,
            'status' => 'dinilai',
        ]);

        // Notify siswa about nilai
        $notificationService = app(NotificationService::class);
        $notificationService->notifyNilaiUpdate($submission);

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

        // IDOR guard: batasi ke pengumpulan milik tugas di kelas+mapel yang diverifikasi.
        $tugas = $this->authorizedTugas($kelasId, $mapelId, $tugasId);

        TugasSiswa::whereIn('id', $validated['siswa_ids'])
            ->where('tugas_id', $tugas->id)
            ->update([
                'nilai' => $validated['nilai'],
                'feedback_guru' => $validated['feedback_guru'] ?? null,
                'status' => 'dinilai',
            ]);

        // Notify each siswa about nilai
        $notificationService = app(NotificationService::class);
        $submissions = TugasSiswa::whereIn('id', $validated['siswa_ids'])
            ->where('tugas_id', $tugas->id)
            ->get();
        foreach ($submissions as $submission) {
            $notificationService->notifyNilaiUpdate($submission);
        }

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

        $tugas = $this->authorizedTugas($kelasId, $mapelId, $tugasId);
        $submission = $this->authorizedSubmission($submissionId, $tugas);

        $aiService = new \App\Services\AiGradingService();

        // Check if there is a file and process based on type
        if ($submission->file_jawaban) {
            $path = storage_path('app/public/' . $submission->file_jawaban);

            // Check mime type
            if (file_exists($path)) {
                $mime = mime_content_type($path);

                // Handle Image files
                if (str_starts_with($mime, 'image/')) {
                    $result = $aiService->evaluateImage(
                        $tugas->judul_tugas . "\n\n" . $tugas->deskripsi,
                        $path,
                        $tugas->deskripsi
                    );
                    return response()->json($result);
                }

                // Handle PDF files
                if ($mime === 'application/pdf') {
                    try {
                        // Strategy 1: Try extract text (Digital PDF)
                        $pdfText = Pdf::getText($path);
                        $pdfText = trim($pdfText);

                        // If we got meaningful text (> 10 chars), use text grading
                        if (strlen($pdfText) > 10) {
                            $result = $aiService->evaluate(
                                $tugas->judul_tugas . "\n\n" . $tugas->deskripsi,
                                $pdfText,
                                $tugas->deskripsi
                            );
                            return response()->json($result);
                        }

                        // Strategy 2: No text found → Scanned/Image PDF
                        // Convert first page to image and use Vision AI
                        $imagePath = storage_path('app/temp/' . uniqid('pdf_') . '.jpg');

                        // Ensure temp directory exists
                        if (!file_exists(storage_path('app/temp'))) {
                            mkdir(storage_path('app/temp'), 0755, true);
                        }

                        $pdf = new PdfToImage($path);
                        $pdf->setPage(1)
                            ->setResolution(150)
                            ->saveImage($imagePath);

                        // Use Vision AI on the converted image
                        $result = $aiService->evaluateImage(
                            $tugas->judul_tugas . "\n\n" . $tugas->deskripsi,
                            $imagePath,
                            $tugas->deskripsi
                        );

                        // Clean up temp image
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }

                        return response()->json($result);

                    } catch (\Exception $e) {
                        \Log::error('PDF Processing Error: ' . $e->getMessage());
                        return response()->json([
                            'error' => true,
                            'feedback' => 'Gagal memproses file PDF. Error: ' . $e->getMessage()
                        ]);
                    }
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
     * Muat tugas & pastikan berada di kelas+mapel yang diajar guru (verifyAccess sudah
     * menjamin akses kelas+mapel route). Cegah IDOR koreksi tugas kelas/mapel lain.
     */
    private function authorizedTugas($kelasId, $mapelId, $tugasId): Tugas
    {
        return Tugas::where('id', $tugasId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();
    }

    /**
     * Muat pengumpulan (TugasSiswa) & pastikan milik tugas yang sudah diotorisasi.
     */
    private function authorizedSubmission($submissionId, Tugas $tugas): TugasSiswa
    {
        return TugasSiswa::with('siswa')
            ->where('id', $submissionId)
            ->where('tugas_id', $tugas->id)
            ->firstOrFail();
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