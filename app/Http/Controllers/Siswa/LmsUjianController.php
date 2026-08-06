<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Siswa;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\SoalUjian;
use App\Models\JawabanSiswa;
use App\Models\MataPelajaran;
use App\Models\UjianPengawasanLog;
use App\Models\UjianSiswaSoalStatus;

class LmsUjianController extends Controller
{
    /**
     * Tampilkan detail ujian
     */
    public function show($mapelId, $ujianId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $ujian = Ujian::where('id', $ujianId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['mataPelajaran', 'guru', 'soalUjian'])
            ->firstOrFail();

        // Cek validasi akses ujian untuk semester (PTS/PAS/UTS/UAS).
        //
        // Akses ujian ditentukan MURNI dari sisi keuangan (lunas / validasi
        // Bendahara / dispensasi yang disetujui) - lihat cekAksesUjian().
        // Dulu di sini masih ada syarat tambahan `validasi_ujian_wali`, padahal
        // halaman Wali Kelas sudah dijadikan read-only dan tidak punya tombol
        // untuk menyalakan flag itu lagi. Akibatnya SEMUA siswa terkunci: sudah
        // lunas, di layar wali tertulis "Akses Terbuka", tapi tetap ditolak saat
        // membuka ujian, dan tidak ada satu pun jalan di UI untuk membukanya.
        if ($ujian->requiresValidation()) {
            $aksesService = app(\App\Services\ValidasiAksesService::class);
            if (!$aksesService->cekAksesUjian($siswa)) {
                return redirect()->route('siswa.lms.mapel.show', $mapelId)
                    ->with('error', 'Belum Memiliki Akses Ujian. Pembayaran belum lunas - silakan hubungi Bendahara untuk pelunasan atau pengajuan dispensasi.');
            }
        }

        // Cek apakah sudah mengerjakan
        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->first();

        // HANDLE: Ujian ditarik guru saat siswa sedang mengerjakan
        if ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan' && !$ujian->is_active) {
            // Reset ujian ke belum_mulai karena guru menarik ujian
            $ujianSiswa->update([
                'status' => 'belum_mulai',
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'nilai' => null,
                'current_soal_ujian_id' => null,
                'current_nomor_soal' => null,
                'last_activity_at' => null,
                'last_heartbeat_at' => null,
                'answered_count' => 0,
                'doubt_count' => 0,
                'visited_count' => 0,
                'focus_lost_count' => 0,
                'focus_lost_total_seconds' => 0,
                'active_focus_lost_at' => null,
            ]);

            // Hapus semua jawaban siswa yang sudah dijawab
            JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)->delete();
            $ujianSiswa->soalStatuses()->delete();
            $ujianSiswa->pengawasanLogs()->delete();

            \Log::warning("Ujian ID {$ujianId} ditarik guru. Ujian siswa ID {$ujianSiswa->id} direset ke belum_mulai");

            return redirect()->route('siswa.lms.mapel.show', $mapelId)
                ->with('error', 'Maaf, ujian ini telah ditarik kembali oleh guru. Sesi ujian Anda telah dibatalkan. Semua jawaban yang telah Anda masukkan telah dihapus.');
        }

        // Cek apakah ujian sedang berlangsung
        $isOngoing = $ujian->isOngoing();

        // PROTEKSI: Jika sedang mengerjakan tapi waktu sudah habis (Server-side check)
        if ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan' && $ujianSiswa->isTimeUp()) {
            $this->selesaikanUjian($ujianSiswa, []); // Selesaikan tanpa jawaban tambahan (yang sudah ada di DB tetap ada)
            return redirect()->route('siswa.lms.mapel.show', $mapelId)
                ->with('error', 'Waktu ujian telah habis. Ujian Anda telah disubmit otomatis.');
        }

        // Get soal ujian - pastikan soal di-load dengan benar
        // Query soal secara langsung dan urutkan
        $soalList = SoalUjian::where('ujian_id', $ujianId)
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Jika tidak ada soal, return empty collection
        if ($soalList->isEmpty()) {
            // Log untuk debugging
            \Log::warning("Tidak ada soal untuk ujian ID: {$ujianId}");
            $soalList = collect();
        }

        $mataPelajaran = $ujian->mataPelajaran;

        // Load existing answers if any
        $existingAnswers = [];
        if ($ujianSiswa) {
            $existingAnswers = \App\Models\JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)
                ->pluck('jawaban', 'soal_ujian_id')
                ->toArray();
        }

        // Use different view for Latihan (Worksheet Style)
        if ($ujian->tipe_ujian === 'latihan') {
            return view('siswa.lms.mata-pelajaran.ujian.show_latihan', compact(
                'siswa',
                'ujian',
                'ujianSiswa',
                'isOngoing',
                'soalList',
                'mataPelajaran',
                'existingAnswers'
            ));
        }

        return view('siswa.lms.mata-pelajaran.ujian.show', compact(
            'siswa',
            'ujian',
            'ujianSiswa',
            'isOngoing',
            'soalList',
            'mataPelajaran',
            'existingAnswers'
        ));
    }

    /**
     * Mulai ujian
     */
    public function mulai(Request $request, $mapelId, $ujianId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $ujian = Ujian::where('id', $ujianId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();

        // Cek validasi akses untuk ujian semester (PTS/PAS/UTS/UAS).
        // Syaratnya sama dengan show(): murni dari sisi keuangan.
        if ($ujian->requiresValidation()) {
            $aksesService = app(\App\Services\ValidasiAksesService::class);
            if (!$aksesService->cekAksesUjian($siswa)) {
                return back()->with('error', 'Belum Memiliki Akses Ujian. Pembayaran belum lunas - silakan hubungi Bendahara untuk pelunasan atau pengajuan dispensasi.');
            }
        }

        // Cek apakah ujian sudah ditarik guru (tidak aktif)
        if (!$ujian->is_active) {
            return back()->with('error', 'Ujian ini telah ditarik kembali oleh guru dan tidak dapat dimulai');
        }

        // Validasi waktu ujian
        if (!$ujian->isOngoing()) {
            return back()->with('error', 'Ujian belum dimulai atau sudah berakhir');
        }

        // Cek apakah sudah pernah mulai
        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->first();

        if (!$ujianSiswa) {
            $ujianSiswa = UjianSiswa::create([
                'ujian_id' => $ujianId,
                'siswa_id' => $siswa->id,
                'waktu_mulai' => now(),
                'status' => 'sedang_mengerjakan',
                'last_activity_at' => now(),
                'last_heartbeat_at' => now(),
            ]);
            $this->recordPengawasanLog($ujianSiswa, $request, 'exam_started', 'Siswa mulai ujian.');
        } else {
            // Jika sudah ada tapi belum mulai, update status
            if ($ujianSiswa->status !== 'sedang_mengerjakan') {
                $ujianSiswa->update([
                    'waktu_mulai' => now(),
                    'waktu_selesai' => null,
                    'status' => 'sedang_mengerjakan',
                    'current_soal_ujian_id' => null,
                    'current_nomor_soal' => null,
                    'last_activity_at' => now(),
                    'last_heartbeat_at' => now(),
                    'answered_count' => 0,
                    'doubt_count' => 0,
                    'visited_count' => 0,
                    'focus_lost_count' => 0,
                    'focus_lost_total_seconds' => 0,
                    'active_focus_lost_at' => null,
                ]);
                $ujianSiswa->soalStatuses()->delete();
                $ujianSiswa->pengawasanLogs()->delete();
                $this->recordPengawasanLog($ujianSiswa->fresh(), $request, 'exam_started', 'Siswa mulai ujian.');
            }
        }

        // Force refresh dari database
        $ujianSiswa = $ujianSiswa->fresh();

        $durasiMsg = ($ujian->durasi_menit == 0) ? 'Tanpa Batas' : $ujian->durasi_menit . ' menit';
        $routePrefix = $ujian->tipe_ujian === 'latihan' ? 'latihan' : 'ujian';
        
        return redirect()->route('siswa.lms.mapel.' . $routePrefix . '.show', [$mapelId, $ujianId])
            ->with('success', ucfirst($routePrefix) . ' dimulai. Waktu: ' . $durasiMsg);
    }

    /**
     * Submit jawaban ujian
     */
    public function submit(Request $request, $mapelId, $ujianId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        $jawabanInput = $request->input('jawaban', []);
        
        // Cek waktu habis
        if ($ujianSiswa->isTimeUp()) {
            $this->selesaikanUjian($ujianSiswa, $jawabanInput);
            return redirect()->route('siswa.lms.mapel.show', $mapelId)
                ->with('error', 'Waktu ujian telah habis. Jawaban yang sempat masuk telah disimpan.');
        }

        $result = $this->selesaikanUjian($ujianSiswa, $jawabanInput);

        $tipeLabel = ($ujianSiswa->ujian->tipe_ujian === 'latihan') ? 'Latihan' : 'Ujian';
        $msg = "$tipeLabel berhasil dikumpulkan!";
        
        if ($result['perluKoreksiManual']) {
            $msg .= ' Nilai sementara: ' . number_format($result['nilai'], 1) . '/100 (beberapa soal menunggu koreksi guru)';
        } else {
            $msg .= ' Nilai: ' . number_format($result['nilai'], 1) . '/100';
        }

        return redirect()->route('siswa.lms.mapel.show', $mapelId)
            ->with('success', $msg);
    }

    /**
     * Logika internal untuk memproses perhitungan nilai dan menutup sesi ujian
     */
    private function selesaikanUjian(UjianSiswa $ujianSiswa, array $jawabanInput)
    {
        $ujianId = $ujianSiswa->ujian_id;
        $soalList = SoalUjian::where('ujian_id', $ujianId)->get();
        $totalNilai = 0;
        $perluKoreksiManual = false;

        // 1. Simpan Jawaban jika ada yang dikirim
        foreach ($jawabanInput as $soalId => $jawaban) {
            $soal = $soalList->where('id', $soalId)->first();
            if ($soal) {
                $jawabanSiswa = JawabanSiswa::updateOrCreate(
                    ['ujian_siswa_id' => $ujianSiswa->id, 'soal_ujian_id' => $soalId],
                    ['jawaban' => $jawaban ?? '-']
                );

                // Auto-grade
                $gradeResult = $soal->checkAnswer($jawaban);
                if ($gradeResult !== null) {
                    $score = $soal->calculatePartialScore($jawaban);
                    $jawabanSiswa->update(['nilai_soal' => $score]);
                } else {
                    $perluKoreksiManual = true;
                }
            }
        }

        // 2. Hitung Total Nilai (termasuk jawaban yang sudah tersimpan sebelumnya di DB)
        $totalNilai = JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)->sum('nilai_soal');

        // 3. Normalisasi nilai ke skala 0-100
        $totalBobot = $soalList->sum('bobot_nilai');
        $nilaiNormalized = $totalBobot > 0 ? round(($totalNilai / $totalBobot) * 100, 1) : 0;

        // 4. Hitung nilai terbaik
        $nilaiTerbaik = $ujianSiswa->nilai_terbaik ?? 0;
        if ($nilaiNormalized > $nilaiTerbaik) {
            $nilaiTerbaik = $nilaiNormalized;
        }

        // 5. Update status ujian siswa
        $this->syncSubmittedAnswersToMonitoring($ujianSiswa, $jawabanInput);
        $this->refreshPengawasanCounts($ujianSiswa);

        $ujianSiswa->update([
            'waktu_selesai' => now(),
            'last_activity_at' => now(),
            'last_heartbeat_at' => now(),
            'active_focus_lost_at' => null,
            'nilai' => $nilaiNormalized,
            'nilai_terbaik' => $nilaiTerbaik,
            'status' => 'selesai',
        ]);
        $this->recordPengawasanLog($ujianSiswa->fresh(), null, 'exam_submitted', 'Siswa mengumpulkan ujian.');

        // 6. Notifikasi ke Guru
        $ujianSiswa->load(['ujian', 'siswa']);
        app(\App\Services\NotificationService::class)->notifyUjianSelesai($ujianSiswa);

        return [
            'nilai' => $nilaiNormalized,
            'perluKoreksiManual' => $perluKoreksiManual
        ];
    }

    /**
     * Kerjakan Ulang Latihan
     */
    public function retake(Request $request, $mapelId, $ujianId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $ujian = Ujian::where('id', $ujianId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();

        // Cek apakah diperbolehkan diulang
        if (!$ujian->bisa_diulang) {
            return back()->with('error', $ujian->tipe_label . ' ini tidak dapat diulang.');
        }

        // Cek apakah aktif dan waktu cocok
        if (!$ujian->is_active) {
            return back()->with('error', $ujian->tipe_label . ' ini ditarik oleh guru.');
        }
        if (!$ujian->isOngoing()) {
            return back()->with('error', $ujian->tipe_label . ' belum dimulai atau sudah berakhir.');
        }

        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($ujianSiswa) {
            // Cek batas pengulangan
            $batas = $ujian->batas_pengulangan;
            if ($batas > 0 && $ujianSiswa->pengulangan_ke > $batas) {
                return back()->with('error', 'Anda sudah mencapai batas maksimal pengulangan (' . $batas . ' kali).');
            }

            // Simpan nilai terbaik
            $nilaiSekarang = $ujianSiswa->nilai ?? 0;
            $nilaiTerbaik = $ujianSiswa->nilai_terbaik ?? 0;
            if ($nilaiSekarang > $nilaiTerbaik) {
                $nilaiTerbaik = $nilaiSekarang;
            }

            // Reset status ujian siswa dan HAPUS jawaban sebelumnya agar mulai dari nol
            \App\Models\JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)->delete();
            
            $ujianSiswa->update([
                'status' => 'belum_mulai',
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'current_soal_ujian_id' => null,
                'current_nomor_soal' => null,
                'last_activity_at' => null,
                'last_heartbeat_at' => null,
                'nilai' => 0,
                'pengulangan_ke' => $ujianSiswa->pengulangan_ke + 1,
                'nilai_terbaik' => $nilaiTerbaik,
                'answered_count' => 0,
                'doubt_count' => 0,
                'visited_count' => 0,
                'focus_lost_count' => 0,
                'focus_lost_total_seconds' => 0,
                'active_focus_lost_at' => null,
            ]);
            $ujianSiswa->soalStatuses()->delete();
            $ujianSiswa->pengawasanLogs()->delete();
        }

        $routePrefix = $ujian->tipe_ujian === 'latihan' ? 'latihan' : 'ujian';
        return redirect()->route('siswa.lms.mapel.' . $routePrefix . '.show', [$mapelId, $ujianId])
            ->with('success', 'Ujian telah di-reset. Silakan kerjakan ulang! Nilai sebelumnya telah disimpan sebagai nilai terbaik jika lebih tinggi.');
    }

    /**
     * Tampilkan riwayat dan pembahasan ujian/latihan
     */
    public function review($mapelId, $ujianId): View|\Illuminate\Http\RedirectResponse
    {
        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();
        
        $ujian = Ujian::with(['soalUjian' => function($q) {
            $q->orderBy('urutan', 'asc');
        }])
            ->where('id', $ujianId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();

        $ujianSiswa = UjianSiswa::with('jawabanSiswa')
            ->where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->first();

        // Check conditions
        if (!$ujianSiswa || !in_array($ujianSiswa->status, ['selesai', 'dinilai'])) {
            return back()->with('error', 'Anda belum menyelesaikan ' . $ujian->tipe_label . ' ini.');
        }

        if (!$ujian->tampilkan_riwayat) {
            return back()->with('error', 'Guru tidak mengizinkan untuk melihat riwayat ' . $ujian->tipe_label . ' ini.');
        }

        $mapel = MataPelajaran::findOrFail($mapelId);

        return view('siswa.lms.mata-pelajaran.ujian.review', compact('ujian', 'ujianSiswa', 'siswa', 'mapel'));
    }

    /**
     * Autosave jawaban siswa via AJAX
     */
    public function autosave(Request $request, $mapelId, $ujianId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan'], 404);
        }

        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'sedang_mengerjakan')
            ->first();

        if (!$ujianSiswa) {
            return response()->json(['success' => false, 'message' => 'Sesi ujian tidak aktif atau sudah selesai'], 403);
        }

        // Cek apakah waktu sudah habis
        if ($ujianSiswa->isTimeUp()) {
            return response()->json(['success' => false, 'message' => 'Waktu sudah habis'], 403);
        }

        $soalId = $request->soal_id;
        $jawaban = $request->jawaban;

        // Validasi soal milik ujian ini
        $soal = SoalUjian::where('id', $soalId)->where('ujian_id', $ujianId)->first();
        if (!$soal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak valid'], 404);
        }

        // Simpan jawaban
        \App\Models\JawabanSiswa::updateOrCreate(
            [
                'ujian_siswa_id' => $ujianSiswa->id,
                'soal_ujian_id' => $soalId,
            ],
            [
                'jawaban' => $jawaban ?? '-',
            ]
        );

        $nomorSoal = $request->integer('nomor_soal') ?: null;
        $this->upsertSoalStatus($ujianSiswa, (int) $soalId, [
            'nomor_soal' => $nomorSoal,
            'is_visited' => true,
            'is_answered' => $this->isJawabanTerisi($jawaban),
        ]);

        $ujianSiswa->update([
            'current_soal_ujian_id' => $soalId,
            'current_nomor_soal' => $nomorSoal,
            'last_activity_at' => now(),
            'last_heartbeat_at' => now(),
        ]);
        $this->refreshPengawasanCounts($ujianSiswa);

        return response()->json(['success' => true]);
    }

    public function monitoring(Request $request, $mapelId, $ujianId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan'], 404);
        }

        $ujian = Ujian::where('id', $ujianId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();

        if ($ujian->tipe_ujian === 'latihan') {
            return response()->json(['success' => true, 'monitoring' => false]);
        }

        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'sedang_mengerjakan')
            ->first();

        if (!$ujianSiswa) {
            return response()->json(['success' => false, 'message' => 'Sesi ujian tidak aktif atau sudah selesai'], 403);
        }

        $validated = $request->validate([
            'event_type' => 'required|string|in:heartbeat,question_opened,answer_saved,doubt_updated,focus_lost,focus_returned',
            'current_soal_id' => 'nullable|integer',
            'current_nomor_soal' => 'nullable|integer|min:1',
            'statuses' => 'nullable|array',
            'statuses.*.soal_id' => 'required_with:statuses|integer',
            'statuses.*.nomor_soal' => 'nullable|integer|min:1',
            'statuses.*.is_visited' => 'nullable|boolean',
            'statuses.*.is_answered' => 'nullable|boolean',
            'statuses.*.is_doubt' => 'nullable|boolean',
            'metadata' => 'nullable|array',
        ]);

        $validSoalIds = SoalUjian::where('ujian_id', $ujianId)->pluck('id')->map(fn($id) => (int) $id)->all();
        $currentSoalId = isset($validated['current_soal_id']) && in_array((int) $validated['current_soal_id'], $validSoalIds, true)
            ? (int) $validated['current_soal_id']
            : null;
        $currentNomorSoal = $validated['current_nomor_soal'] ?? null;

        foreach ($validated['statuses'] ?? [] as $status) {
            $soalId = (int) $status['soal_id'];
            if (!in_array($soalId, $validSoalIds, true)) {
                continue;
            }

            $this->upsertSoalStatus($ujianSiswa, $soalId, [
                'nomor_soal' => $status['nomor_soal'] ?? null,
                'is_visited' => (bool) ($status['is_visited'] ?? false),
                'is_answered' => (bool) ($status['is_answered'] ?? false),
                'is_doubt' => (bool) ($status['is_doubt'] ?? false),
            ]);
        }

        if ($currentSoalId) {
            $this->upsertSoalStatus($ujianSiswa, $currentSoalId, [
                'nomor_soal' => $currentNomorSoal,
                'is_visited' => true,
            ]);
        }

        $updateData = [
            'last_activity_at' => now(),
        ];

        if ($validated['event_type'] === 'heartbeat') {
            $updateData['last_heartbeat_at'] = now();
        }

        if ($currentSoalId) {
            $updateData['current_soal_ujian_id'] = $currentSoalId;
            $updateData['current_nomor_soal'] = $currentNomorSoal;
        }

        $ujianSiswa->update($updateData);
        $ujianSiswa = $ujianSiswa->fresh();

        if ($validated['event_type'] === 'focus_lost') {
            if (!$ujianSiswa->active_focus_lost_at) {
                $ujianSiswa->update([
                    'focus_lost_count' => $ujianSiswa->focus_lost_count + 1,
                    'active_focus_lost_at' => now(),
                ]);
                $this->recordPengawasanLog($ujianSiswa->fresh(), $request, 'focus_lost', 'Halaman ujian kehilangan fokus.', $validated['metadata'] ?? []);
            }
        } elseif ($validated['event_type'] === 'focus_returned') {
            if ($ujianSiswa->active_focus_lost_at) {
                $duration = (int) round(max(0, $ujianSiswa->active_focus_lost_at->diffInSeconds(now())));
                $focusLostLog = UjianPengawasanLog::where('ujian_siswa_id', $ujianSiswa->id)
                    ->where('event_type', 'focus_lost')
                    ->latest('occurred_at')
                    ->first();

                if ($focusLostLog) {
                    $metadata = $focusLostLog->metadata ?: [];
                    $metadata['duration_seconds'] = $duration;
                    $focusLostLog->update(['metadata' => $metadata]);
                }

                $ujianSiswa->update([
                    'focus_lost_total_seconds' => $ujianSiswa->focus_lost_total_seconds + $duration,
                    'active_focus_lost_at' => null,
                ]);
                $this->recordPengawasanLog($ujianSiswa->fresh(), $request, 'focus_returned', 'Siswa kembali fokus ke halaman ujian.', $validated['metadata'] ?? []);
            }
        } elseif (in_array($validated['event_type'], ['question_opened', 'doubt_updated'], true)) {
            $this->recordPengawasanLog($ujianSiswa, $request, $validated['event_type'], $this->eventDescription($validated['event_type']), array_merge($validated['metadata'] ?? [], [
                'nomor_soal' => $currentNomorSoal,
            ]));
        }

        $this->refreshPengawasanCounts($ujianSiswa);
        $ujianSiswa = $ujianSiswa->fresh();

        return response()->json([
            'success' => true,
            'monitoring' => true,
            'summary' => [
                'answered_count' => $ujianSiswa->answered_count,
                'doubt_count' => $ujianSiswa->doubt_count,
                'visited_count' => $ujianSiswa->visited_count,
                'focus_lost_count' => $ujianSiswa->focus_lost_count,
                'focus_lost_total_seconds' => $ujianSiswa->focus_lost_total_seconds,
            ],
        ]);
    }

    private function upsertSoalStatus(UjianSiswa $ujianSiswa, int $soalId, array $data): void
    {
        $existing = UjianSiswaSoalStatus::where('ujian_siswa_id', $ujianSiswa->id)
            ->where('soal_ujian_id', $soalId)
            ->first();

        $payload = [
            'nomor_soal' => $data['nomor_soal'] ?? optional($existing)->nomor_soal,
            'is_visited' => array_key_exists('is_visited', $data) ? (bool) $data['is_visited'] : (bool) optional($existing)->is_visited,
            'is_answered' => array_key_exists('is_answered', $data) ? (bool) $data['is_answered'] : (bool) optional($existing)->is_answered,
            'is_doubt' => array_key_exists('is_doubt', $data) ? (bool) $data['is_doubt'] : (bool) optional($existing)->is_doubt,
        ];

        if ($payload['is_visited']) {
            $payload['last_visited_at'] = now();
        }
        if ($payload['is_answered']) {
            $payload['last_answered_at'] = now();
        }
        if (array_key_exists('is_doubt', $data)) {
            $payload['last_doubt_at'] = now();
        }

        UjianSiswaSoalStatus::updateOrCreate(
            ['ujian_siswa_id' => $ujianSiswa->id, 'soal_ujian_id' => $soalId],
            $payload
        );
    }

    private function refreshPengawasanCounts(UjianSiswa $ujianSiswa): void
    {
        $query = UjianSiswaSoalStatus::where('ujian_siswa_id', $ujianSiswa->id);

        $ujianSiswa->update([
            'answered_count' => (clone $query)->where('is_answered', true)->count(),
            'doubt_count' => (clone $query)->where('is_doubt', true)->count(),
            'visited_count' => (clone $query)->where('is_visited', true)->count(),
        ]);
    }

    private function syncSubmittedAnswersToMonitoring(UjianSiswa $ujianSiswa, array $jawabanInput): void
    {
        if (empty($jawabanInput)) {
            return;
        }

        $nomorMap = SoalUjian::where('ujian_id', $ujianSiswa->ujian_id)
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('id')
            ->values()
            ->mapWithKeys(fn($id, $index) => [(int) $id => $index + 1]);

        foreach ($jawabanInput as $soalId => $jawaban) {
            $soalId = (int) $soalId;
            if (!$nomorMap->has($soalId)) {
                continue;
            }

            $this->upsertSoalStatus($ujianSiswa, $soalId, [
                'nomor_soal' => $nomorMap[$soalId],
                'is_visited' => true,
                'is_answered' => $this->isJawabanTerisi($jawaban),
            ]);
        }
    }

    private function recordPengawasanLog(UjianSiswa $ujianSiswa, ?Request $request, string $eventType, ?string $description = null, array $metadata = []): void
    {
        UjianPengawasanLog::create([
            'ujian_siswa_id' => $ujianSiswa->id,
            'event_type' => $eventType,
            'description' => $description,
            'metadata' => empty($metadata) ? null : $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'occurred_at' => now(),
        ]);
    }

    private function eventDescription(string $eventType): string
    {
        return match ($eventType) {
            'question_opened' => 'Siswa membuka soal.',
            'doubt_updated' => 'Siswa mengubah tanda ragu-ragu.',
            default => 'Aktivitas ujian tercatat.',
        };
    }

    private function isJawabanTerisi($jawaban): bool
    {
        if (is_array($jawaban)) {
            return collect($jawaban)->filter(fn($value) => $value !== null && $value !== '' && $value !== '-')->isNotEmpty();
        }

        if (!is_string($jawaban)) {
            return $jawaban !== null;
        }

        $jawaban = trim($jawaban);
        if ($jawaban === '' || $jawaban === '-') {
            return false;
        }

        $decoded = json_decode($jawaban, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return true;
        }

        if (is_array($decoded)) {
            return collect($decoded)->filter(fn($value) => $value !== null && $value !== '' && $value !== '-')->isNotEmpty();
        }

        return $decoded !== null && $decoded !== '';
    }
}
