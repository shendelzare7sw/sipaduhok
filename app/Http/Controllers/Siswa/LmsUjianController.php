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

        // Cek validasi akses ujian untuk semester (bukan ulangan harian)
        if (in_array($ujian->tipe_ujian, ['uts', 'uas'])) {
            if (!$siswa->validasi_ujian_bendahara || !$siswa->validasi_ujian_wali) {
                return redirect()->route('siswa.lms.mapel.show', $mapelId)
                    ->with('error', 'Belum Memiliki Akses Ujian. Silakan Periksa Tagihan Anda.');
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

        $ujian = Ujian::findOrFail($ujianId);

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

        return redirect()->route('siswa.lms.mapel.ujian.show', [$mapelId, $ujianId])
            ->with('success', 'Ujian dimulai. Waktu: ' . $ujian->durasi_menit . ' menit');
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

        $ujian = Ujian::findOrFail($ujianId);

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

                // Auto-grade untuk pilihan ganda
                if ($soal->tipe_soal === 'pilihan_ganda') {
                    $jawabanSiswa->autoGrade();
                    $totalNilai += $jawabanSiswa->nilai_soal ?? 0;
                }
            }
        }

        // Update status ujian siswa
        $ujianSiswa->update([
            'waktu_selesai' => now(),
            'nilai' => $totalNilai,
            'status' => 'selesai',
        ]);

        // Notify guru about ujian completion
        $ujianSiswa->load(['ujian', 'siswa']);
        app(\App\Services\NotificationService::class)->notifyUjianSelesai($ujianSiswa);

        return redirect()->route('siswa.lms.mapel.show', $mapelId)
            ->with('success', 'Ujian berhasil dikumpulkan! Nilai: ' . $totalNilai);
    }
}
