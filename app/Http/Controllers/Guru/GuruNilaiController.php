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
    public function update(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $validated = $request->validate([
            'nilai_id' => 'required|exists:nilai,id',
            'nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
        ]);
        
        $nilai = Nilai::findOrFail($validated['nilai_id']);
        
        $nilai->update([
            'nilai_tugas' => $validated['nilai_tugas'],
            'nilai_uts' => $validated['nilai_uts'],
            'nilai_uas' => $validated['nilai_uas'],
        ]);
        
        // Recalculate nilai_akhir
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
    private function calculateNilai($nilai)
    {
        // Hitung rata-rata nilai tugas
        $avgTugas = TugasSiswa::whereHas('tugas', function($q) use ($nilai) {
                $q->where('mata_pelajaran_id', $nilai->mata_pelajaran_id)
                  ->where('kelas_id', $nilai->kelas_id);
            })
            ->where('siswa_id', $nilai->siswa_id)
            ->where('status', 'dinilai')
            ->avg('nilai');
        
        // Hitung rata-rata UTS
        $avgUts = UjianSiswa::whereHas('ujian', function($q) use ($nilai) {
                $q->where('mata_pelajaran_id', $nilai->mata_pelajaran_id)
                  ->where('kelas_id', $nilai->kelas_id)
                  ->where('tipe_ujian', 'uts');
            })
            ->where('siswa_id', $nilai->siswa_id)
            ->where('status', 'selesai')
            ->avg('nilai');
        
        // Hitung rata-rata UAS
        $avgUas = UjianSiswa::whereHas('ujian', function($q) use ($nilai) {
                $q->where('mata_pelajaran_id', $nilai->mata_pelajaran_id)
                  ->where('kelas_id', $nilai->kelas_id)
                  ->where('tipe_ujian', 'uas');
            })
            ->where('siswa_id', $nilai->siswa_id)
            ->where('status', 'selesai')
            ->avg('nilai');
        
        $nilai->update([
            'nilai_tugas' => $avgTugas,
            'nilai_uts' => $avgUts,
            'nilai_uas' => $avgUas,
        ]);
        
        // Auto-calculate nilai_akhir via model method
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