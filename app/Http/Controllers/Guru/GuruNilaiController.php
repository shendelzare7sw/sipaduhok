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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Guru\NilaiSiswaImport;
use App\Exports\Guru\NilaiSiswaTemplateExport;

class GuruNilaiController extends Controller
{
    /**
     * Tampilkan tabel nilai siswa
     */
    public function index(Request $request, $kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        // Get semester from request or default to current
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);
        
        // Ambil semua siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get()
            ->filter(fn($siswa) => $siswa->canAccessMapel($mataPelajaran));
        
        // Buat atau ambil nilai untuk setiap siswa (per semester)
        $nilaiList = [];
        foreach ($siswaList as $siswa) {
            $nilai = Nilai::firstOrCreate([
                'siswa_id' => $siswa->id,
                'mata_pelajaran_id' => $mapelId,
                'kelas_id' => $kelasId,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => $semester,
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
            'semester' => $semester,
            'currentSemester' => $currentSemester,
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
     * Update batch - save all students' nilai at once
     */
    public function updateBatch(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $nilaiData = $request->input('nilai', []);
        $updatedCount = 0;

        foreach ($nilaiData as $nilaiId => $data) {
            $nilai = Nilai::find($nilaiId);
            if (!$nilai) continue;

            // Prepare data to update
            $dataToUpdate = [];
            
            // Handle tugas, latihan, uh 1-5
            foreach (range(1, 5) as $i) {
                if (isset($data["tugas_$i"])) {
                    $dataToUpdate["tugas_$i"] = $data["tugas_$i"] !== '' ? floatval($data["tugas_$i"]) : null;
                }
                if (isset($data["latihan_$i"])) {
                    $dataToUpdate["latihan_$i"] = $data["latihan_$i"] !== '' ? floatval($data["latihan_$i"]) : null;
                }
                if (isset($data["uh_$i"])) {
                    $dataToUpdate["uh_$i"] = $data["uh_$i"] !== '' ? floatval($data["uh_$i"]) : null;
                }
            }

            // Handle PTS, PAS
            if (isset($data['pts'])) {
                $dataToUpdate['pts'] = $data['pts'] !== '' ? floatval($data['pts']) : null;
            }
            if (isset($data['pas'])) {
                $dataToUpdate['pas'] = $data['pas'] !== '' ? floatval($data['pas']) : null;
            }

            // Handle tingkat akhir fields
            foreach (['to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek'] as $field) {
                if (isset($data[$field])) {
                    $dataToUpdate[$field] = $data[$field] !== '' ? floatval($data[$field]) : null;
                }
            }

            if (!empty($dataToUpdate)) {
                $nilai->update($dataToUpdate);
                $nilai->hitungSemuaRata();
                $nilai->hitungNilaiAkhir();
                $updatedCount++;
            }
        }
        
        return redirect()
            ->route('guru.lms.nilai.index', [$kelasId, $mapelId])
            ->with('success', "Berhasil menyimpan nilai {$updatedCount} siswa.");
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
            ->where('semester', Nilai::getCurrentSemester())
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
    /**
     * Export nilai ke Excel
     */
    public function exportExcel(Request $request, $kelasId, $mapelId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        // Get semester from request or default to current
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);

        // Ambil semua siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get()
            ->filter(fn($siswa) => $siswa->canAccessMapel($mataPelajaran));

        // Buat atau ambil nilai untuk setiap siswa (per semester)
        $nilaiCollection = [];
        foreach ($siswaList as $siswa) {
            $nilai = Nilai::firstOrCreate([
                'siswa_id' => $siswa->id,
                'mata_pelajaran_id' => $mapelId,
                'kelas_id' => $kelasId,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester' => $semester,
                'guru_id' => $tenagaPendidik->id,
            ]);

            // Calculate nilai if empty
            if (!$nilai->nilai_akhir) {
                $this->calculateNilai($nilai);
            }

            // Load siswa relation on nilai
            $nilai->siswa = $siswa;
            $nilaiCollection[] = $nilai;
        }

        $nilaiCollection = collect($nilaiCollection);
        $fileName = 'Nilai_Siswa_' . \Str::slug($kelas->nama_kelas) . '_' . \Str::slug($mataPelajaran->nama_mapel) . '_' . $semester . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Guru\NilaiSiswaExport($nilaiCollection, $kelas, $mataPelajaran, $semester),
            $fileName
        );
    }

    /**
     * Download template Excel untuk import nilai
     */
    public function downloadTemplate(Request $request, $kelasId, $mapelId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        // Get semester from request or default to current
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);

        // Ambil semua siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get()
            ->filter(fn($siswa) => $siswa->canAccessMapel($mataPelajaran));

        $fileName = 'Template_Nilai_' . \Str::slug($kelas->nama_kelas) . '_' . \Str::slug($mataPelajaran->nama_mapel) . '_' . $semester . '.xlsx';

        return Excel::download(
            new NilaiSiswaTemplateExport(collect($siswaList), $kelas, $mataPelajaran, $semester),
            $fileName
        );
    }

    /**
     * Import nilai dari Excel
     */
    public function importExcel(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120', // Max 5MB
        ]);

        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        // Get semester from request or default to current
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);

        try {
            $import = new NilaiSiswaImport(
                $kelasId,
                $mapelId,
                $tahunAjaran->id,
                $semester,
                $tenagaPendidik->id
            );

            Excel::import($import, $request->file('file'));

            // Check for errors
            $errors = $import->getErrors();
            $failures = $import->getFailures();

            if (!empty($errors) || !empty($failures)) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[] = $error;
                }

                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure['row']}: " . implode(', ', $failure['errors']);
                }

                return redirect()
                    ->route('guru.lms.nilai.index', [$kelasId, $mapelId, 'semester' => $semester])
                    ->with('warning', 'Import selesai dengan beberapa error: ' . implode(' | ', $errorMessages));
            }

            return redirect()
                ->route('guru.lms.nilai.index', [$kelasId, $mapelId, 'semester' => $semester])
                ->with('success', 'Nilai berhasil diimpor dari Excel!');

        } catch (\Exception $e) {
            return redirect()
                ->route('guru.lms.nilai.index', [$kelasId, $mapelId, 'semester' => $semester])
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
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