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
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TugasSiswa;
use App\Models\UjianSiswa;
use App\Models\TahunAjaran;

class GuruNilaiController extends Controller
{
    /**
     * Tampilkan tabel nilai siswa
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        // Ambil semua siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();
        
        // Buat atau ambil nilai untuk setiap siswa
        $nilaiList = [];
        foreach ($siswaList as $siswa) {
            $nilai = Nilai::firstOrCreate([
                'siswa_id' => $siswa->id,
                'mata_pelajaran_id' => $mapelId,
                'kelas_id' => $kelasId,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'guru_id' => $tenagaPendidik->id,
            ]);
            
            // Calculate nilai if empty
            if (!$nilai->nilai_akhir) {
                $this->calculateNilai($nilai);
            }
            
            // Load siswa relation on nilai
            $nilai->siswa = $siswa;
            $nilaiList[] = $nilai;
        }
        
        // Convert to collection for pagination support
        $nilaiList = collect($nilaiList);
        
        return view('guru.lms.nilai.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'nilaiList' => $nilaiList,
            'guru' => $tenagaPendidik,
        ]);
    }
    
    /**
     * Update nilai manual
     */
    /**
     * Update nilai manual
     */
    public function update(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $rules = [
            'nilai_id' => 'required|exists:nilai,id',
            'pts' => 'nullable|numeric|min:0|max:100',
            'pas' => 'nullable|numeric|min:0|max:100',
            'keterampilan' => 'nullable|numeric|min:0|max:100',
            'to_1' => 'nullable|numeric|min:0|max:100',
            'to_2' => 'nullable|numeric|min:0|max:100',
            'to_3' => 'nullable|numeric|min:0|max:100',
            'upk' => 'nullable|numeric|min:0|max:100',
            'ujian_praktek' => 'nullable|numeric|min:0|max:100',
        ];

        // Add validations for 1-5
        foreach (range(1, 5) as $i) {
            $rules["tugas_$i"] = 'nullable|numeric|min:0|max:100';
            $rules["latihan_$i"] = 'nullable|numeric|min:0|max:100';
            $rules["uh_$i"] = 'nullable|numeric|min:0|max:100';
        }

        $validated = $request->validate($rules);
        
        $nilai = Nilai::findOrFail($validated['nilai_id']);
        
        // Remove nilai_id from validated array before update
        $dataToUpdate = collect($validated)->except(['nilai_id'])->toArray();
        
        $nilai->update($dataToUpdate);
        
        // Recalculate averages and final score
        $nilai->hitungSemuaRata();
        $nilai->hitungNilaiAkhir();
        
        return redirect()
            ->route('guru.lms.nilai.index', [$kelasId, $mapelId])
            ->with('success', 'Nilai berhasil diperbarui');
    }
    
    /**
     * Hitung ulang semua nilai dari tugas dan ujian
     */
    public function recalculate($kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        $nilaiList = Nilai::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('guru_id', $tenagaPendidik->id)
            ->get();
        
        foreach ($nilaiList as $nilai) {
            $this->calculateNilai($nilai);
        }
        
        return redirect()
            ->route('guru.lms.nilai.index', [$kelasId, $mapelId])
            ->with('success', 'Semua nilai berhasil dihitung ulang');
    }
    
    /**
     * Export nilai ke Excel (placeholder)
     */
    public function export($kelasId, $mapelId)
    {
        // TODO: Implement Excel export using Laravel Excel
        return redirect()
            ->route('guru.lms.nilai.index', [$kelasId, $mapelId])
            ->with('info', 'Fitur export Excel akan segera tersedia');
    }
    
    /**
     * Calculate nilai from tugas and ujian
     */
    /**
     * Calculate nilai from tugas and ujian
     */
    private function calculateNilai($nilai)
    {
        // 1. Fetch Assignments (Tugas & Latihan)
        $tugasSiswa = TugasSiswa::whereHas('tugas', function($q) use ($nilai) {
                $q->where('mata_pelajaran_id', $nilai->mata_pelajaran_id)
                  ->where('kelas_id', $nilai->kelas_id);
            })
            ->with('tugas')
            ->where('siswa_id', $nilai->siswa_id)
            ->where('status', 'dinilai')
            ->get();

        $updateData = [];

        // Map Tugas 1-5 & Latihan 1-5
        foreach ($tugasSiswa as $ts) {
            $jenis = $ts->tugas->jenis_tugas ?? 'tugas'; // tugas or latihan
            $urutan = $ts->tugas->urutan ?? 1;
            
            if ($urutan >= 1 && $urutan <= 5) {
                $column = "{$jenis}_{$urutan}"; // e.g., tugas_1, latihan_2
                $updateData[$column] = $ts->nilai;
            }
        }

        // 2. Fetch Exams (UH, PTS, PAS)
        $ujianSiswa = UjianSiswa::whereHas('ujian', function($q) use ($nilai) {
                $q->where('mata_pelajaran_id', $nilai->mata_pelajaran_id)
                  ->where('kelas_id', $nilai->kelas_id);
            })
            ->with('ujian')
            ->where('siswa_id', $nilai->siswa_id)
            ->where('status', 'selesai')
            ->get();

        foreach ($ujianSiswa as $us) {
            $tipe = $us->ujian->tipe_ujian; // uh, uts, uas
            
            // Map UTS -> PTS, UAS -> PAS
            if ($tipe === 'uts') {
                $updateData['pts'] = $us->nilai;
            } elseif ($tipe === 'uas') {
                $updateData['pas'] = $us->nilai;
            } elseif ($tipe === 'uh') {
                // Assuming UH has urutan or we take latest? 
                // For now, let's assume UH works similarly if 'ujian' table has 'urutan' or name parsing.
                // If 'ujian' doesn't have 'urutan', we might need to rely on 'nama_ujian' or created_at.
                // Checking previous analysis: `ujian` table exists but `urutan` column check needed.
                // If missing, we skip mapping UH automatically for now or use name 'UH 1'.
                // Let's check name:
                if (preg_match('/(UH|Ulangan Harian)\s*(\d+)/i', $us->ujian->nama_ujian, $matches)) {
                    $urutan = intval($matches[2]);
                    if ($urutan >= 1 && $urutan <= 5) {
                        $updateData["uh_{$urutan}"] = $us->nilai;
                    }
                }
            }
        }

        if (!empty($updateData)) {
            $nilai->update($updateData);
        }
        
        // Auto-calculate properties
        $nilai->hitungSemuaRata();
        $nilai->hitungNilaiAkhir();
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