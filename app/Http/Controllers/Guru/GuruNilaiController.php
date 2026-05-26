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
use App\Models\TahunAjaran;
use App\Services\NilaiSyncService;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Guru\NilaiSiswaImport;
use App\Exports\Guru\NilaiSiswaTemplateExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuruNilaiController extends Controller
{
    /**
     * Tampilkan tabel nilai siswa
     */
    public function index(Request $request, int $kelasId, int $mapelId): View
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
    public function update(Request $request, int $kelasId, int $mapelId): RedirectResponse
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
    public function updateBatch(Request $request, int $kelasId, int $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $nilaiData = $request->input('nilai', []);
        $updatedCount = 0;
        $skipPrimaryCount = 0;

        $allowedFields = ['pts', 'pas', 'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek'];
        foreach (range(1, 5) as $i) {
            $allowedFields[] = "tugas_$i";
            $allowedFields[] = "latihan_$i";
            $allowedFields[] = "uh_$i";
        }

        foreach ($nilaiData as $nilaiId => $data) {
            $nilai = Nilai::find($nilaiId);
            if (!$nilai) continue;

            $newValues = [];
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $newValues[$field] = $data[$field] !== '' ? floatval($data[$field]) : null;
                }
            }
            if (empty($newValues)) continue;

            $snapshotChanged = false;
            $dataToUpdate = [];
            foreach ($newValues as $field => $value) {
                $oldSnapshot = $nilai->{$field . '_guru'};
                $oldFloat = $oldSnapshot !== null ? (float) $oldSnapshot : null;
                if ($oldFloat !== $value) {
                    $snapshotChanged = true;
                }
                $dataToUpdate[$field . '_guru'] = $value;
            }

            if ($snapshotChanged) {
                $dataToUpdate['guru_terakhir_simpan_at'] = now();
            }
            $dataToUpdate['guru_id'] = $tenagaPendidik->id;

            $waliPernahEdit = $nilai->wali_terakhir_edit_at !== null;
            if (!$waliPernahEdit) {
                $dataToUpdate = array_merge($dataToUpdate, $newValues);
            } elseif ($snapshotChanged) {
                $skipPrimaryCount++;
            }

            $nilai->update($dataToUpdate);

            if (!$waliPernahEdit) {
                $nilai->hitungNilaiAkhir();
            }
            $updatedCount++;
        }

        $message = "Berhasil menyimpan nilai {$updatedCount} siswa.";
        if ($skipPrimaryCount > 0) {
            $message .= " {$skipPrimaryCount} siswa hanya disimpan ke snapshot guru karena wali kelas sudah mengedit nilainya - wali perlu sinkronisasi manual.";
        }

        return redirect()
            ->route('guru.lms.nilai.index', [$kelasId, $mapelId])
            ->with('success', $message);
    }
    
    /**
     * Hitung ulang semua nilai dari tugas dan ujian
     */
    public function recalculate(int $kelasId, int $mapelId): RedirectResponse
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
     * Export nilai ke Excel
     */
    public function exportExcel(Request $request, int $kelasId, int $mapelId): BinaryFileResponse
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
        $fileName = 'Nilai_Siswa_' . Str::slug($kelas->nama_kelas) . '_' . Str::slug($mataPelajaran->nama_mapel) . '_' . $semester . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Guru\NilaiSiswaExport($nilaiCollection, $kelas, $mataPelajaran, $semester),
            $fileName
        );
    }

    /**
     * Download template Excel untuk import nilai
     */
    public function downloadTemplate(Request $request, int $kelasId, int $mapelId): BinaryFileResponse
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

        $fileName = 'Template_Nilai_' . Str::slug($kelas->nama_kelas) . '_' . Str::slug($mataPelajaran->nama_mapel) . '_' . $semester . '.xlsx';

        return Excel::download(
            new NilaiSiswaTemplateExport(collect($siswaList), $kelas, $mataPelajaran, $semester),
            $fileName
        );
    }

    /**
     * Import nilai dari Excel
     */
    public function importExcel(Request $request, int $kelasId, int $mapelId): RedirectResponse
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
    private function calculateNilai(Nilai $nilai): void
    {
        app(NilaiSyncService::class)->syncForSiswaMapel(
            $nilai->siswa_id,
            $nilai->mata_pelajaran_id,
            $nilai->kelas_id,
            $nilai->semester,
            $nilai->tahun_ajaran_id
        );
    }
    
    /**
     * Verifikasi akses guru
     */
    private function verifyAccess(int $guruId, int $kelasId, int $mapelId): void
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
