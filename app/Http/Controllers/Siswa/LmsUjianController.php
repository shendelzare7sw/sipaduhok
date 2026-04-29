<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\SoalUjian;
use App\Models\JawabanSiswa;

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

        // Cek validasi akses ujian untuk semester (PTS/PAS/UTS/UAS)
        if ($ujian->requiresValidation()) {
            $aksesService = app(\App\Services\ValidasiAksesService::class);
            if (!$aksesService->cekAksesUjian($siswa) || !$siswa->validasi_ujian_wali) {
                return redirect()->route('siswa.lms.mapel.show', $mapelId)
                    ->with('error', 'Belum Memiliki Akses Ujian. Pastikan pembayaran sudah lunas (Bendahara) dan disetujui Wali Kelas.');
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
            ]);

            // Hapus semua jawaban siswa yang sudah dijawab
            JawabanSiswa::where('ujian_siswa_id', $ujianSiswa->id)->delete();

            \Log::warning("Ujian ID {$ujianId} ditarik guru. Ujian siswa ID {$ujianSiswa->id} direset ke belum_mulai");

            return redirect()->route('siswa.lms.mapel.show', $mapelId)
                ->with('error', 'Maaf, ujian ini telah ditarik kembali oleh guru. Sesi ujian Anda telah dibatalkan. Semua jawaban yang telah Anda masukkan telah dihapus.');
        }

        // Cek apakah ujian sedang berlangsung
        $isOngoing = $ujian->isOngoing();

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

        // Use different view for Latihan (Worksheet Style)
        if ($ujian->tipe_ujian === 'latihan') {
            return view('siswa.lms.mata-pelajaran.ujian.show_latihan', compact(
                'siswa',
                'ujian',
                'ujianSiswa',
                'isOngoing',
                'soalList',
                'mataPelajaran'
            ));
        }

        return view('siswa.lms.mata-pelajaran.ujian.show', compact(
            'siswa',
            'ujian',
            'ujianSiswa',
            'isOngoing',
            'soalList',
            'mataPelajaran'
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

        // Cek validasi akses untuk ujian semester (PTS/PAS/UTS/UAS)
        if ($ujian->requiresValidation()) {
            $aksesService = app(\App\Services\ValidasiAksesService::class);
            if (!$aksesService->cekAksesUjian($siswa) || !$siswa->validasi_ujian_wali) {
                return back()->with('error', 'Belum Memiliki Akses Ujian. Pastikan pembayaran sudah lunas (Bendahara) dan disetujui Wali Kelas.');
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
            ]);
        } else {
            // Jika sudah ada tapi belum mulai, update status
            if ($ujianSiswa->status !== 'sedang_mengerjakan') {
                $ujianSiswa->update([
                    'waktu_mulai' => now(),
                    'status' => 'sedang_mengerjakan',
                ]);
            }
        }

        // Force refresh dari database
        $ujianSiswa = $ujianSiswa->fresh();

        $durasiMsg = ($ujian->durasi_menit == 0) ? 'Tanpa Batas' : $ujian->durasi_menit . ' menit';
        return redirect()->route('siswa.lms.mapel.ujian.show', [$mapelId, $ujianId])
            ->with('success', 'Ujian dimulai. Waktu: ' . $durasiMsg);
    }

    /**
     * Submit jawaban ujian
     */
    public function submit(Request $request, $mapelId, $ujianId)
    {
        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|string',
        ]);

        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $ujian = Ujian::where('id', $ujianId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('mata_pelajaran_id', $mapelId)
            ->firstOrFail();

        $ujianSiswa = UjianSiswa::where('ujian_id', $ujianId)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        // Cek waktu habis
        if ($ujianSiswa->isTimeUp()) {
            $ujianSiswa->update([
                'status' => 'selesai',
                'waktu_selesai' => now(),
            ]);
            return back()->with('error', 'Waktu ujian telah habis');
        }

        // Simpan jawaban
        $totalNilai = 0;
        $perluKoreksiManual = false;
        $soalList = SoalUjian::where('ujian_id', $ujianId)->get();

        foreach ($request->jawaban as $soalId => $jawaban) {
            $soal = $soalList->where('id', $soalId)->first();

            if ($soal) {
                $jawabanSiswa = JawabanSiswa::updateOrCreate(
                    [
                        'ujian_siswa_id' => $ujianSiswa->id,
                        'soal_ujian_id' => $soalId,
                    ],
                    [
                        'jawaban' => $jawaban,
                    ]
                );

                // Auto-grade untuk semua tipe yang bisa di-auto-grade
                $result = $soal->checkAnswer($jawaban);

                if ($result !== null) {
                    // Tipe auto-gradable: pilgan, pilgan_kompleks, benar_salah, isian
                    $score = $soal->calculatePartialScore($jawaban);
                    $jawabanSiswa->update(['nilai_soal' => $score]);
                    $totalNilai += $score;
                } else {
                    // Tipe uraian/essay → perlu koreksi manual oleh guru
                    $perluKoreksiManual = true;
                }
            }
        }

        // Normalisasi nilai ke skala 0-100
        $totalBobot = $soalList->sum('bobot_nilai');
        $nilaiNormalized = $totalBobot > 0 ? round(($totalNilai / $totalBobot) * 100, 1) : 0;

        // Hitung nilai terbaik
        $nilaiTerbaik = $ujianSiswa->nilai_terbaik ?? 0;
        if ($nilaiNormalized > $nilaiTerbaik) {
            $nilaiTerbaik = $nilaiNormalized;
        }

        // Update status ujian siswa
        $ujianSiswa->update([
            'waktu_selesai' => now(),
            'nilai' => $nilaiNormalized,
            'nilai_terbaik' => $nilaiTerbaik,
            'status' => 'selesai',
        ]);

        // Notify guru about ujian completion
        $ujianSiswa->load(['ujian', 'siswa']);
        app(\App\Services\NotificationService::class)->notifyUjianSelesai($ujianSiswa);

        $tipeLabel = ($ujian->tipe_ujian === 'latihan') ? 'Latihan' : 'Ujian';
        $msg = "$tipeLabel berhasil dikumpulkan!";
        if ($perluKoreksiManual) {
            $msg .= ' Nilai sementara: ' . number_format($nilaiNormalized, 1) . '/100 (beberapa soal menunggu koreksi guru)';
        } else {
            $msg .= ' Nilai: ' . number_format($nilaiNormalized, 1) . '/100';
        }

        return redirect()->route('siswa.lms.mapel.show', $mapelId)
            ->with('success', $msg);
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

            // Reset status ujian siswa tapi biarkan jawaban sebelumnya
            $ujianSiswa->update([
                'status' => 'belum_mulai',
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'nilai' => 0,
                'pengulangan_ke' => $ujianSiswa->pengulangan_ke + 1,
                'nilai_terbaik' => $nilaiTerbaik,
            ]);
        }

        $routePrefix = $ujian->tipe_ujian === 'latihan' ? 'latihan' : 'ujian';
        return redirect()->route('siswa.lms.mapel.' . $routePrefix . '.show', [$mapelId, $ujianId])
            ->with('success', 'Ujian telah di-reset. Silakan kerjakan ulang! Nilai sebelumnya telah disimpan sebagai nilai terbaik jika lebih tinggi.');
    }
}
