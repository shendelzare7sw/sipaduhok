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
use App\Models\TahunAjaran;
use App\Models\GuruPengajarKelas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WaliKelas\NilaiPerSiswaTemplateExport;
use App\Imports\WaliKelas\NilaiPerSiswaImport;

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
        
        // Get semester from request or default to current
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);
        
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
                'semester' => $semester,
                'currentSemester' => $currentSemester,
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
        // Use active tahun ajaran to match Guru's input
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        // Get mapel IDs that actually have grades for this student/class in this active year and semester
        $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        // Get mata pelajaran untuk filter
        $mataPelajaranList = MataPelajaran::where(function($q) use ($kelas, $existingNilaiMapelIds) {
                $q->where('jenjang', $kelas->jenjang)
                  ->orWhereIn('id', $existingNilaiMapelIds);
            })
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
        $jumlahGuruUpdate = 0;

        if ($selectedMapelId) {
            $selectedMapel = MataPelajaran::find($selectedMapelId);

            if ($selectedMapel) {
                // Filter siswa that can access this mapel
                $siswaList = $siswaList->filter(function($siswa) use ($selectedMapel) {
                    return $siswa->canAccessMapel($selectedMapel);
                });

                // $tahunAjaranAktif already defined above
                $nilaiQuery = Nilai::where('kelas_id', $kelas->id)
                    ->where('mata_pelajaran_id', $selectedMapelId)
                    ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
                    ->where('semester', $semester)
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

                    $jumlahGuruUpdate = $nilaiQuery->filter(function ($nilai) {
                        return $nilai->hasGuruUpdate()
                            && $nilai->guru_terakhir_simpan_at
                            && (!$nilai->wali_terakhir_edit_at
                                || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
                    })->count();
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
            'jumlahTuntas',
            'jumlahGuruUpdate',
            'semester',
            'currentSemester'
        ));
    }
    
    /**
     * Show detail nilai siswa
     */
    public function show(Request $request, $siswaId)
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

        // Get semester from request or default to current
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);

        $kelasList = $this->getKelasWali($wali);
        $kelas->load(['siswa', 'tahunAjaran']);
        
        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->firstOrFail();
        
        // Use active tahun ajaran to match Guru's input
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        // Get mapel IDs that actually have grades for this student/class in this active year and semester
        $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        $mataPelajaranList = MataPelajaran::where(function($q) use ($kelas, $existingNilaiMapelIds) {
                $q->where('jenjang', $kelas->jenjang)
                  ->orWhereIn('id', $existingNilaiMapelIds);
            })
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get()
            ->filter(fn($mapel) => $siswa->canAccessMapel($mapel));
        $nilaiData = Nilai::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
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
            'totalNilai',
            'semester',
            'currentSemester'
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
        $cabang = $kelas->cabang;

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

            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            $semester = $request->get('semester', Nilai::getCurrentSemester());

            $nilaiData = Nilai::where('kelas_id', $kelas->id)
                ->where('mata_pelajaran_id', $selectedMapelId)
                ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
                ->where('semester', $semester)
                ->get()
                ->keyBy('siswa_id');

            /*
            $pdf = Pdf::loadView('wali-kelas.nilai.print-detail', compact(
                'kelas',
                'siswaList',
                'selectedMapel',
                'nilaiData',
                'wali',
                'cabang'
            ));

            return $pdf->stream('Rekap_Nilai_' . $selectedMapel->nama_mapel . '_' . $kelas->nama_kelas . '.pdf', ['Attachment' => 0]);
            */

            return view('wali-kelas.nilai.print-detail', compact(
                'kelas',
                'siswaList',
                'selectedMapel',
                'nilaiData',
                'wali',
                'cabang',
                'semester'
            ));

        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            $semester = $request->get('semester', Nilai::getCurrentSemester());

            // Get mapel IDs that actually have grades for this student/class in this active year
            $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
                ->where('semester', $semester)
                ->pluck('mata_pelajaran_id')
                ->unique()
                ->toArray();

            $mataPelajaranList = MataPelajaran::where(function($q) use ($kelas, $existingNilaiMapelIds) {
                    $q->where('jenjang', $kelas->jenjang)
                      ->orWhereIn('id', $existingNilaiMapelIds);
                })
                ->where('is_active', true)
                ->orderBy('nama_mapel', 'asc')
                ->get();

            $allNilai = Nilai::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
                ->where('semester', $semester)
                ->with('mataPelajaran')
                ->get();

            $allNilaiData = $allNilai;

            /*
            $pdf = Pdf::loadView('wali-kelas.nilai.print-all', compact(
                'kelas',
                'siswaList',
                'mataPelajaranList',
                'allNilaiData',
                'wali',
                'cabang'
            ));

            return $pdf->stream('Rekap_Nilai_Semua_Mapel_' . $kelas->nama_kelas . '.pdf', ['Attachment' => 0]);
            */

            return view('wali-kelas.nilai.print-all', compact(
                'kelas',
                'siswaList',
                'mataPelajaranList',
                'allNilaiData',
                'wali',
                'cabang',
                'semester'
            ));
        }
    }
    
    /**
     * Print rekap nilai per siswa (standalone print view with logo)
     */
    public function printSiswa(Request $request, $siswaId)
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

        $kelas->load(['tahunAjaran', 'cabang']);
        $cabang = $kelas->cabang;

        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $semester = $request->get('semester', Nilai::getCurrentSemester());

        $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        $mataPelajaranList = MataPelajaran::where(function($q) use ($kelas, $existingNilaiMapelIds) {
                $q->where('jenjang', $kelas->jenjang)
                  ->orWhereIn('id', $existingNilaiMapelIds);
            })
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get()
            ->filter(fn($mapel) => $siswa->canAccessMapel($mapel));

        $nilaiData = Nilai::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->get()
            ->keyBy('mata_pelajaran_id');

        return view('wali-kelas.nilai.print', compact(
            'siswa',
            'kelas',
            'mataPelajaranList',
            'nilaiData',
            'cabang',
            'semester'
        ));
    }

    /**
     * Edit nilai siswa
     */
    public function edit(Request $request, $siswaId)
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

        // Use active tahun ajaran to match Guru's input
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $currentSemester = Nilai::getCurrentSemester();
        $semester = in_array($request->get('semester'), ['ganjil', 'genap'])
            ? $request->get('semester')
            : $currentSemester;

        // Get mapel IDs that actually have grades for this student/class in this active year
        $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        $mataPelajaranList = MataPelajaran::where(function($q) use ($kelas, $existingNilaiMapelIds) {
                $q->where('jenjang', $kelas->jenjang)
                  ->orWhereIn('id', $existingNilaiMapelIds);
            })
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get()
            ->filter(fn($mapel) => $siswa->canAccessMapel($mapel));
        $nilaiData = Nilai::where('siswa_id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->get()
            ->keyBy('mata_pelajaran_id');

        return view('wali-kelas.nilai.edit', compact(
            'siswa',
            'kelas',
            'kelasList',
            'mataPelajaranList',
            'nilaiData',
            'semester',
            'currentSemester'
        ));
    }
    
    /**
     * Update nilai siswa
     */
    public function update(Request $request, $siswaId)
    {
        $rules = [
            'nilai' => 'required|array',
            'nilai.*.mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'nilai.*.pts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.pas' => 'nullable|numeric|min:0|max:100',
            // Tingkat Akhir fields
            'nilai.*.to_1' => 'nullable|numeric|min:0|max:100',
            'nilai.*.to_2' => 'nullable|numeric|min:0|max:100',
            'nilai.*.to_3' => 'nullable|numeric|min:0|max:100',
            'nilai.*.upk' => 'nullable|numeric|min:0|max:100',
            'nilai.*.ujian_praktek' => 'nullable|numeric|min:0|max:100',
        ];

        // Add validation for tugas, latihan, uh (1-5)
        foreach (range(1, 5) as $i) {
            $rules["nilai.*.tugas_$i"] = 'nullable|numeric|min:0|max:100';
            $rules["nilai.*.latihan_$i"] = 'nullable|numeric|min:0|max:100';
            $rules["nilai.*.uh_$i"] = 'nullable|numeric|min:0|max:100';
        }

        $validated = $request->validate($rules);

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

        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $currentSemester = Nilai::getCurrentSemester();
        $semester = in_array($request->input('semester'), ['ganjil', 'genap'])
            ? $request->input('semester')
            : $currentSemester;

        $mapelTanpaGuru = [];

        foreach ($request->nilai as $nilaiInput) {
            $dataToUpdate = [
                'edited_by_wali_id' => $wali->id,
                'wali_terakhir_edit_at' => now(),
                'pts' => $nilaiInput['pts'] ?? null,
                'pas' => $nilaiInput['pas'] ?? null,
            ];

            foreach (range(1, 5) as $i) {
                $dataToUpdate["tugas_$i"] = $nilaiInput["tugas_$i"] ?? null;
                $dataToUpdate["latihan_$i"] = $nilaiInput["latihan_$i"] ?? null;
                $dataToUpdate["uh_$i"] = $nilaiInput["uh_$i"] ?? null;
            }

            $dataToUpdate['to_1'] = $nilaiInput['to_1'] ?? null;
            $dataToUpdate['to_2'] = $nilaiInput['to_2'] ?? null;
            $dataToUpdate['to_3'] = $nilaiInput['to_3'] ?? null;
            $dataToUpdate['upk'] = $nilaiInput['upk'] ?? null;
            $dataToUpdate['ujian_praktek'] = $nilaiInput['ujian_praktek'] ?? null;

            $criteria = [
                'siswa_id' => $siswa->id,
                'mata_pelajaran_id' => $nilaiInput['mata_pelajaran_id'],
                'kelas_id' => $kelas->id,
                'tahun_ajaran_id' => $tahunAjaranAktif?->id,
                'semester' => $semester,
            ];

            // guru_id NOT NULL di tabel nilai. Kalau belum ada baris nilai sama sekali
            // untuk kombinasi ini (guru belum pernah menyentuh mapel ini), updateOrCreate()
            // di bawah akan INSERT baris baru — tanpa guru_id, insert itu gagal (500).
            // Ambil guru_id dari penugasan GuruPengajarKelas, sumber kebenaran yang sama
            // dipakai GuruNilaiController::verifyAccess().
            if (!Nilai::where($criteria)->exists()) {
                $guruId = GuruPengajarKelas::where('kelas_id', $kelas->id)
                    ->where('mata_pelajaran_id', $nilaiInput['mata_pelajaran_id'])
                    ->value('tenaga_pendidik_id');

                if (!$guruId) {
                    // Tidak ada guru pengajar ditugaskan untuk mapel ini di kelas ini —
                    // lewati daripada memaksa insert yang pasti gagal.
                    $mapelTanpaGuru[] = $nilaiInput['mata_pelajaran_id'];
                    continue;
                }

                $dataToUpdate['guru_id'] = $guruId;
            }

            $nilai = Nilai::updateOrCreate($criteria, $dataToUpdate);

            $nilai->hitungNilaiAkhir();
        }

        if (!empty($mapelTanpaGuru)) {
            $namaMapel = MataPelajaran::whereIn('id', $mapelTanpaGuru)->pluck('nama_mapel')->implode(', ');
            return redirect()->route('wali.nilai.index')
                ->with('warning', "Nilai lainnya tersimpan. Mapel berikut dilewati karena belum ada guru pengajar yang ditugaskan di kelas ini: {$namaMapel}.");
        }

        return redirect()->route('wali.nilai.index')
            ->with('success', 'Nilai siswa berhasil diperbarui.');
    }

    /**
     * Clear specific nilai field(s)
     */
    public function clearNilai(Request $request, $nilaiId)
    {
        $wali = $this->getTenagaPendidik();
        
        if (!$wali) {
            return response()->json(['error' => 'Data tenaga pendidik tidak ditemukan.'], 403);
        }

        $nilai = Nilai::findOrFail($nilaiId);
        
        // Verify wali has access to this class
        $kelas = $this->getSelectedKelas($wali);
        if (!$kelas || $nilai->kelas_id !== $kelas->id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $field = $request->input('field');
        
        // Validate field name
        $allowedFields = [
            'tugas_1', 'tugas_2', 'tugas_3', 'tugas_4', 'tugas_5',
            'latihan_1', 'latihan_2', 'latihan_3', 'latihan_4', 'latihan_5',
            'uh_1', 'uh_2', 'uh_3', 'uh_4', 'uh_5',
            'pts', 'pas',
            'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Field tidak valid.'], 400);
        }

        // Set to null
        $nilai->$field = null;
        $nilai->save();

        // Recalculate averages and final score
        $nilai->hitungNilaiAkhir();

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil dihapus.',
            'rata_tugas' => $nilai->rata_tugas,
            'rata_latihan' => $nilai->rata_latihan,
            'rata_uh' => $nilai->rata_uh,
            'nilai_akhir' => $nilai->nilai_akhir,
        ]);
    }

    /**
     * Download template Excel untuk import nilai per siswa
     */
    public function downloadTemplate(Request $request, $siswaId)
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

        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $currentSemester  = Nilai::getCurrentSemester();
        $semester = in_array($request->get('semester'), ['ganjil', 'genap'])
            ? $request->get('semester')
            : $currentSemester;

        $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        $mataPelajaranList = MataPelajaran::where(function ($q) use ($kelas, $existingNilaiMapelIds) {
                $q->where('jenjang', $kelas->jenjang)
                  ->orWhereIn('id', $existingNilaiMapelIds);
            })
            ->where('is_active', true)
            ->orderBy('nama_mapel', 'asc')
            ->get()
            ->filter(fn($mapel) => $siswa->canAccessMapel($mapel))
            ->values();

        $isKelasAkhir = $kelas->isTingkatAkhir();

        $fileName = 'Template_Nilai_' . Str::slug($siswa->nama_lengkap) . '_' . $kelas->nama_kelas . '_' . $semester . '.xlsx';

        return Excel::download(
            new NilaiPerSiswaTemplateExport($mataPelajaranList, $siswa, $kelas, $semester, $isKelasAkhir),
            $fileName
        );
    }

    /**
     * Import nilai per siswa dari file Excel
     */
    public function importExcel(Request $request, $siswaId)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

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

        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $currentSemester  = Nilai::getCurrentSemester();
        $semester = in_array($request->input('semester'), ['ganjil', 'genap'])
            ? $request->input('semester')
            : $currentSemester;

        $existingNilaiMapelIds = Nilai::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->where('semester', $semester)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        $mapelCollection = MataPelajaran::where(function ($q) use ($kelas, $existingNilaiMapelIds) {
                $q->where('jenjang', $kelas->jenjang)
                  ->orWhereIn('id', $existingNilaiMapelIds);
            })
            ->where('is_active', true)
            ->get()
            ->filter(fn($mapel) => $siswa->canAccessMapel($mapel))
            ->values();

        try {
            $import = new NilaiPerSiswaImport(
                $siswa->id,
                $kelas->id,
                $tahunAjaranAktif->id,
                $semester,
                $wali->id,
                $mapelCollection
            );

            Excel::import($import, $request->file('file'));

            $errors   = $import->getErrors();
            $failures = $import->getFailures();

            if (!empty($errors) || !empty($failures)) {
                $messages = array_merge(
                    $errors,
                    array_map(fn($f) => "Baris {$f['row']}: " . implode(', ', $f['errors']), $failures)
                );
                return redirect()
                    ->route('wali.nilai.edit', $siswaId)
                    ->with('warning', 'Import selesai dengan peringatan: ' . implode(' | ', array_slice($messages, 0, 5)));
            }

            return redirect()
                ->route('wali.nilai.edit', $siswaId)
                ->with('success', 'Import nilai berhasil.');

        } catch (\Exception $e) {
            return redirect()
                ->route('wali.nilai.edit', $siswaId)
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function syncFromGuru(Request $request, $nilaiId)
    {
        $wali = $this->getTenagaPendidik();
        if (!$wali) {
            return redirect()->route('wali.dashboard')->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);
        if (!$kelas) {
            return redirect()->route('wali.nilai.index')->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $nilai = Nilai::where('id', $nilaiId)
            ->where('kelas_id', $kelas->id)
            ->firstOrFail();

        $nilai->syncFromGuru();

        $siswaId = $request->input('siswa_id', $nilai->siswa_id);
        $semester = $request->input('semester', $nilai->semester);

        return redirect()
            ->route('wali.nilai.edit', ['siswa' => $siswaId, 'semester' => $semester])
            ->with('success', 'Nilai mapel berhasil disinkronkan dengan snapshot dari guru pengajar.');
    }

}
