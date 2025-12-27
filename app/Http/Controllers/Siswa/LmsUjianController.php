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

        // Cek apakah ujian sedang berlangsung
        $isOngoing = $ujian->isOngoing();

        return view('siswa.lms.mata-pelajaran.ujian.show', compact(
            'siswa',
            'ujian',
            'ujianSiswa',
            'isOngoing'
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
        }

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

        return redirect()->route('siswa.lms.mapel.show', $mapelId)
            ->with('success', 'Ujian berhasil dikumpulkan! Nilai: ' . $totalNilai);
    }
}