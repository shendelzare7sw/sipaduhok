<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\TenagaPendidik;
use Barryvdh\DomPDF\Facade\Pdf;

class NilaiController extends Controller
{
    /**
     * Display nilai siswa page with optional filter
     */
    public function index(Request $request)
    {
        // Get wali kelas data
        $wali = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }
        
        // Get kelas yang dipegang wali kelas ini
        $kelas = Kelas::where('wali_kelas_id', $wali->id)
            ->with(['siswa', 'tahunAjaran'])
            ->first();
        
        if (!$kelas) {
            return view('wali-kelas.nilai.index', [
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
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
        
        // ✅ SELALU DEFINE selectedMapelId (ini yang penting!)
        $selectedMapelId = $request->get('mata_pelajaran_id', null);
        
        // Initialize variables
        $selectedMapel = null;
        $nilaiData = [];
        $rataRataKelas = 0;
        $nilaiTertinggi = 0;
        $nilaiTerendah = 0;
        $jumlahTuntas = 0;
        
        // Jika ada mata pelajaran dipilih, ambil datanya
        if ($selectedMapelId) {
            $selectedMapel = MataPelajaran::find($selectedMapelId);
            
            if ($selectedMapel) {
                // Get nilai data untuk mata pelajaran ini
                $nilaiQuery = Nilai::where('kelas_id', $kelas->id)
                    ->where('mata_pelajaran_id', $selectedMapelId)
                    ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                    ->with('siswa')
                    ->get();
                
                // Convert to keyed collection by siswa_id
                $nilaiData = $nilaiQuery->keyBy('siswa_id');
                
                // Calculate statistics
                if ($nilaiQuery->count() > 0) {
                    $nilaiAkhirArray = $nilaiQuery->pluck('nilai_akhir')->filter()->values();
                    
                    if ($nilaiAkhirArray->count() > 0) {
                        $rataRataKelas = $nilaiAkhirArray->avg();
                        $nilaiTertinggi = $nilaiAkhirArray->max();
                        $nilaiTerendah = $nilaiAkhirArray->min();
                        
                        // Hitung jumlah tuntas (nilai >= 70)
                        $jumlahTuntas = $nilaiAkhirArray->filter(function($nilai) {
                            return $nilai >= 70;
                        })->count();
                    }
                }
            }
        }
        
        // Return view dengan SEMUA variable yang dibutuhkan
        return view('wali-kelas.nilai.index', compact(
            'kelas',
            'siswaList',
            'mataPelajaranList',
            'selectedMapelId',      // ✅ HARUS ADA
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
        // Get wali kelas data
        $wali = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }
        
        // Get kelas
        $kelas = Kelas::where('wali_kelas_id', $wali->id)
            ->with(['siswa', 'tahunAjaran'])
            ->first();
        
        if (!$kelas) {
            return redirect()->route('wali.nilai.index')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }
        
        // Get siswa
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->firstOrFail();
        
        // Get all mata pelajaran for this jenjang
        $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get();
        
        // Get all nilai for this siswa
        $nilaiData = Nilai::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->with('mataPelajaran', 'guru')
            ->get()
            ->keyBy('mata_pelajaran_id');
        
        // Calculate statistics
        $totalNilai = $nilaiData->pluck('nilai_akhir')->filter()->count();
        $rataRataSiswa = $totalNilai > 0 ? $nilaiData->pluck('nilai_akhir')->filter()->avg() : 0;
        $jumlahTuntas = $nilaiData->filter(function($nilai) {
            return $nilai->nilai_akhir && $nilai->nilai_akhir >= 70;
        })->count();
        $persentaseTuntas = $totalNilai > 0 ? ($jumlahTuntas / $totalNilai) * 100 : 0;
        
        return view('wali-kelas.nilai.show', compact(
            'siswa',
            'kelas',
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
        // Get wali kelas data
        $wali = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }
        
        // Get kelas
        $kelas = Kelas::where('wali_kelas_id', $wali->id)
            ->with(['siswa', 'tahunAjaran', 'cabang'])
            ->first();
        
        if (!$kelas) {
            abort(404, 'Kelas tidak ditemukan');
        }
        
        // Get siswa
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        
        $selectedMapelId = $request->get('mata_pelajaran_id', null);
        
        // Jika ada filter mata pelajaran
        if ($selectedMapelId) {
            $selectedMapel = MataPelajaran::find($selectedMapelId);
            
            if (!$selectedMapel) {
                abort(404, 'Mata pelajaran tidak ditemukan');
            }
            
            // Get nilai untuk mata pelajaran ini
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
            // Print semua mata pelajaran
            $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
                ->where('is_active', true)
                ->orderBy('nama_mapel', 'asc')
                ->get();
            
            // Get semua nilai untuk kelas ini
            $allNilai = Nilai::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->with('mataPelajaran')
                ->get();
            
            // Group by siswa_id dan mata_pelajaran_id
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
     * Edit nilai siswa (optional - untuk input manual)
     */
    public function edit($siswaId)
    {
        $wali = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$wali) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }
        
        $kelas = Kelas::where('wali_kelas_id', $wali->id)->first();
        
        if (!$kelas) {
            return redirect()->route('wali.nilai.index')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }
        
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
        
        $wali = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $wali->id)->first();
        
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
            
            // Auto calculate nilai akhir
            $nilai->hitungNilaiAkhir();
        }
        
        return redirect()->route('wali.nilai.index')
            ->with('success', 'Nilai siswa berhasil diperbarui.');
    }
}