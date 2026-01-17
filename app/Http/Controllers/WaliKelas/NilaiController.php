<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\TenagaPendidik;
use Barryvdh\DomPDF\Facade\Pdf;

class NilaiController extends Controller
{
    use WaliKelasHelper;

    /**
     * Display nilai siswa page with optional filter
     */
    public function index(Request $request)
    {
        $wali = $this->getTenagaPendidik();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        $kelasList = $this->getKelasWali($wali);
        
        if ($kelasList->isEmpty()) {
            return view('wali-kelas.nilai.index', [
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'kelasList' => collect(),
                'siswaList' => collect(),
                'mataPelajaranList' => collect(),
                'selectedMapelId' => null,
                'selectedMapel' => null,
                'nilaiData' => [],
                'rataRataKelas' => 0,
                'nilaiTertinggi' => 0,
                'nilaiTerendah' => 0,
                'jumlahTuntas' => 0,
            ]);
        }

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        $kelas->load(['siswa', 'tahunAjaran']);
        
        // Get siswa list
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        
        // Get mata pelajaran untuk filter
        $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get();
        
        $selectedMapelId = $request->get('mata_pelajaran_id', null);
        
        // Initialize variables
        $selectedMapel = null;
        $nilaiData = [];
        $rataRataKelas = 0;
        $nilaiTertinggi = 0;
        $nilaiTerendah = 0;
        $jumlahTuntas = 0;
        
        if ($selectedMapelId) {
            $selectedMapel = MataPelajaran::find($selectedMapelId);
            
            if ($selectedMapel) {
                $nilaiQuery = Nilai::where('kelas_id', $kelas->id)
                    ->where('mata_pelajaran_id', $selectedMapelId)
                    ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                    ->with('siswa')
                    ->get();
                
                $nilaiData = $nilaiQuery->keyBy('siswa_id');
                
                if ($nilaiQuery->count() > 0) {
                    $nilaiAkhirArray = $nilaiQuery->pluck('nilai_akhir')->filter()->values();
                    
                    if ($nilaiAkhirArray->count() > 0) {
                        $rataRataKelas = $nilaiAkhirArray->avg();
                        $nilaiTertinggi = $nilaiAkhirArray->max();
                        $nilaiTerendah = $nilaiAkhirArray->min();
                        
                        $jumlahTuntas = $nilaiAkhirArray->filter(function($nilai) {
                            return $nilai >= 70;
                        })->count();
                    }
                }
            }
        }
        
        return view('wali-kelas.nilai.index', compact(
            'kelas',
            'kelasList',
            'siswaList',
            'mataPelajaranList',
            'selectedMapelId',
            'selectedMapel',
            'nilaiData',
            'rataRataKelas',
            'nilaiTertinggi',
            'nilaiTerendah',
            'jumlahTuntas'
        ));
    }
    
    /**
     * Show detail nilai siswa
     */
    public function show($siswaId)
    {
        $wali = $this->getTenagaPendidik();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);
        
        if (!$kelas) {
            return redirect()->route('wali.nilai.index')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $kelasList = $this->getKelasWali($wali);
        $kelas->load(['siswa', 'tahunAjaran']);
        
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->firstOrFail();
        
        $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get();
        
        $nilaiData = Nilai::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->with('mataPelajaran', 'guru')
            ->get()
            ->keyBy('mata_pelajaran_id');
        
        $totalNilai = $nilaiData->pluck('nilai_akhir')->filter()->count();
        $rataRataSiswa = $totalNilai > 0 ? $nilaiData->pluck('nilai_akhir')->filter()->avg() : 0;
        $jumlahTuntas = $nilaiData->filter(function($nilai) {
            return $nilai->nilai_akhir && $nilai->nilai_akhir >= 70;
        })->count();
        $persentaseTuntas = $totalNilai > 0 ? ($jumlahTuntas / $totalNilai) * 100 : 0;
        
        return view('wali-kelas.nilai.show', compact(
            'siswa',
            'kelas',
            'kelasList',
            'mataPelajaranList',
            'nilaiData',
            'rataRataSiswa',
            'jumlahTuntas',
            'persentaseTuntas',
            'totalNilai'
        ));
    }
    
    /**
     * Print rekap nilai
     */
    public function print(Request $request)
    {
        $wali = $this->getTenagaPendidik();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);
        
        if (!$kelas) {
            abort(404, 'Kelas tidak ditemukan');
        }

        $kelas->load(['siswa', 'tahunAjaran', 'cabang']);
        
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        
        $selectedMapelId = $request->get('mata_pelajaran_id', null);
        
        if ($selectedMapelId) {
            $selectedMapel = MataPelajaran::find($selectedMapelId);
            
            if (!$selectedMapel) {
                abort(404, 'Mata pelajaran tidak ditemukan');
            }
            
            $nilaiData = Nilai::where('kelas_id', $kelas->id)
                ->where('mata_pelajaran_id', $selectedMapelId)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->get()
                ->keyBy('siswa_id');
            
            $pdf = Pdf::loadView('wali-kelas.nilai.print-detail', compact(
                'kelas',
                'siswaList',
                'selectedMapel',
                'nilaiData',
                'wali'
            ));
            
            return $pdf->stream('Rekap_Nilai_' . $selectedMapel->nama_mapel . '_' . $kelas->nama_kelas . '.pdf');
            
        } else {
            $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
                ->where('is_active', true)
                ->orderBy('nama_mapel', 'asc')
                ->get();
            
            $allNilai = Nilai::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->with('mataPelajaran')
                ->get();
            
            $nilaiData = [];
            foreach ($allNilai as $nilai) {
                $nilaiData[$nilai->siswa_id][$nilai->mata_pelajaran_id] = $nilai;
            }
            
            $pdf = Pdf::loadView('wali-kelas.nilai.print-all', compact(
                'kelas',
                'siswaList',
                'mataPelajaranList',
                'nilaiData',
                'wali'
            ));
            
            return $pdf->stream('Rekap_Nilai_Semua_Mapel_' . $kelas->nama_kelas . '.pdf');
        }
    }
    
    /**
     * Edit nilai siswa
     */
    public function edit($siswaId)
    {
        $wali = $this->getTenagaPendidik();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);
        
        if (!$kelas) {
            return redirect()->route('wali.nilai.index')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $kelasList = $this->getKelasWali($wali);
        
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();
        
        $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get();
        
        $nilaiData = Nilai::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->get()
            ->keyBy('mata_pelajaran_id');
        
        return view('wali-kelas.nilai.edit', compact(
            'siswa',
            'kelas',
            'kelasList',
            'mataPelajaranList',
            'nilaiData'
        ));
    }
    
    /**
     * Update nilai siswa
     */
    public function update(Request $request, $siswaId)
    {
        $validated = $request->validate([
            'nilai' => 'required|array',
            'nilai.*.mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'nilai.*.nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_uas' => 'nullable|numeric|min:0|max:100',
        ]);
        
        $wali = $this->getTenagaPendidik();

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);
        
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();
        
        foreach ($request->nilai as $nilaiInput) {
            $nilai = Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'mata_pelajaran_id' => $nilaiInput['mata_pelajaran_id'],
                    'kelas_id' => $kelas->id,
                    'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                ],
                [
                    'guru_id' => $wali->id,
                    'nilai_tugas' => $nilaiInput['nilai_tugas'] ?? null,
                    'nilai_uts' => $nilaiInput['nilai_uts'] ?? null,
                    'nilai_uas' => $nilaiInput['nilai_uas'] ?? null,
                ]
            );
            
            $nilai->hitungNilaiAkhir();
        }
        
        return redirect()->route('wali.nilai.index')
            ->with('success', 'Nilai siswa berhasil diperbarui.');
    }
}