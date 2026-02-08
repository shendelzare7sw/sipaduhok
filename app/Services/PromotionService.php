<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Check if a student is eligible for promotion.
     */
    public function checkEligibility(Siswa $siswa, $tahunAjaranId = null)
    {
        if (!$tahunAjaranId) {
            $ta = TahunAjaran::where('is_active', true)->first();
            $tahunAjaranId = $ta ? $ta->id : null;
        }

        if (!$tahunAjaranId) return ['eligible' => false, 'reason' => 'No Active Year'];

        // 1. Check Financial Status
        $financialStatus = $this->checkFinancial($siswa, $tahunAjaranId);

        // 2. Check Academic Status
        $academicStatus = $this->checkAcademic($siswa, $tahunAjaranId);

        // 3. Determine Eligibility
        // Eligible if: (Financial OK OR Dispensasi) AND Academic OK
        $isEligible = ($financialStatus['status'] === 'LUNAS' || $financialStatus['is_dispensasi']) 
                      && $academicStatus['is_tuntas'];

        return [
            'eligible' => $isEligible,
            'financial' => $financialStatus,
            'academic' => $academicStatus,
        ];
    }

    private function checkFinancial($siswa, $tahunAjaranId)
    {
        // Check unpaid bills
        $unpaid = Tagihan::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->sum('jumlah');

        $isLunas = $unpaid <= 0;
        $dispensasi = false;

        if (!$isLunas) {
            // Check for approved dispensation
            $dispensasi = DB::table('izin_naik_kelas_khusus')
                ->where('siswa_id', $siswa->id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->where('status', 'DISETUJUI')
                ->exists();
        }

        return [
            'status' => $isLunas ? 'LUNAS' : 'BELUM_LUNAS',
            'is_dispensasi' => $dispensasi,
            'unpaid_amount' => $unpaid
        ];
    }

    private function checkAcademic($siswa, $tahunAjaranId)
    {
        $batasTuntas = $this->getPassingThreshold($tahunAjaranId); // e.g. 70%

        // Get all grades and group by mata pelajaran (handles ganjil+genap semesters)
        $gradesByMapel = Nilai::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('kelas_id', $siswa->kelas_id)
            ->get()
            ->groupBy('mata_pelajaran_id');

        if ($gradesByMapel->isEmpty()) {
            return [
                'is_tuntas' => false,
                'percentage' => 0,
                'tuntas_count' => 0,
                'total_mapel' => 0
            ];
        }

        $totalMapel = $gradesByMapel->count(); // Jumlah mapel unik
        $tuntasCount = 0;
        $jenjang = $siswa->kelas->jenjang ?? 'SMP';

        foreach ($gradesByMapel as $mapelId => $semesterGrades) {
            // Rata-rata nilai_akhir dari semester ganjil + genap
            $avgNilaiAkhir = $semesterGrades->avg('nilai_akhir');
            $kkm = $this->getKKM($mapelId, $tahunAjaranId, $jenjang);
            if ($avgNilaiAkhir >= $kkm) {
                $tuntasCount++;
            }
        }

        $percentage = ($tuntasCount / $totalMapel) * 100;

        return [
            'is_tuntas' => $percentage >= $batasTuntas,
            'percentage' => round($percentage, 2),
            'tuntas_count' => $tuntasCount,
            'total_mapel' => $totalMapel,
            'threshold' => $batasTuntas
        ];
    }

    private function getKKM($mapelId, $taId, $jenjang)
    {
        // Try to get dynamic KKM
        $setting = DB::table('pengaturan_kkm')
            ->where('tahun_ajaran_id', $taId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('jenjang', $jenjang)
            ->first();

        if ($setting) return $setting->nilai_kkm;

        // Fallback checks for Math (default rule)
        // Need to check mapel name
        $mapel = MataPelajaran::find($mapelId);
        if ($mapel && stripos($mapel->nama_mapel, 'matematika') !== false) {
            return 65;
        }

        return 70; // Global Default
    }

    private function getPassingThreshold($taId)
    {
        $setting = DB::table('pengaturan_naik_kelas')
            ->where('tahun_ajaran_id', $taId)
            ->first();

        return $setting ? $setting->persentase_minimal_tuntas : 70;
    }

    /**
     * Execute promotion for a single student.
     * Can be called in batch.
     */
    public function executeStudentPromotion($siswa, $tahunAjaranId, $executionDate)
    {
        $eligibility = $this->checkEligibility($siswa, $tahunAjaranId);
        
        $statusKelulusan = 'TIDAK_NAIK_KELAS';
        $finalStatusPembayaran = $eligibility['financial']['status'];
        
        if ($eligibility['eligible']) {
            if ($this->isFinalYear($siswa)) {
                $statusKelulusan = 'LULUS';
            } else {
                $statusKelulusan = 'NAIK_KELAS';
            }
            
            // Mark if promoted via dispensation
            if ($eligibility['financial']['status'] !== 'LUNAS' && $eligibility['financial']['is_dispensasi']) {
                 $statusKelulusan = 'NAIK_KELAS_TUNGGAKAN';
            }
        }

        // Determine destination class
        $kelasAsalId = $siswa->kelas_id;
        $kelasAsalNama = $siswa->kelas->nama_kelas ?? '-';
        $kelasTujuanId = null;
        $kelasTujuanNama = null;

        if (in_array($statusKelulusan, ['NAIK_KELAS', 'NAIK_KELAS_TUNGGAKAN'])) {
            // Find next class in the TARGET academic year
            $nextClass = $this->findNextClass($siswa->kelas, $tahunAjaranId);
            if ($nextClass) {
                $kelasTujuanId = $nextClass->id;
                $kelasTujuanNama = $nextClass->nama_kelas;
                
                // Update Student
                $siswa->kelas_id = $kelasTujuanId;
                $siswa->save();
            }
        } elseif ($statusKelulusan === 'TIDAK_NAIK_KELAS') {
            // RETENTION LOGIC:
            // Find class with SAME grade/name in the TARGET academic year
            // e.g. "7A" (2025) -> "7A" (2026)
            $sameClass = $this->findSameClass($siswa->kelas, $tahunAjaranId);
            
            if ($sameClass) {
                $kelasTujuanId = $sameClass->id;
                $kelasTujuanNama = $sameClass->nama_kelas;
                
                // Update Student to new year's class (Retention)
                $siswa->kelas_id = $kelasTujuanId;
                $siswa->save();
            } else {
                // If same class not found in new year, 
                // Set to NULL so they appear in "Unassigned" list for Admin to fix
                // rather than staying hidden in old year class.
                $siswa->kelas_id = null;
                $siswa->save();
                $kelasTujuanNama = 'BELUM DITENTUKAN';
            }
        } elseif ($statusKelulusan === 'LULUS') {
            $siswa->status = 'lulus';
            $siswa->kelas_id = null; // Detach from class for alumni
            $siswa->save();
            $kelasTujuanNama = 'ALUMNI';
        }

        // Record History
        DB::table('status_naik_kelas_siswa')->updateOrInsert(
            [
                'siswa_id' => $siswa->id, 
                'tahun_ajaran_id' => $tahunAjaranId
            ],
            [
                'kelas_asal' => $kelasAsalNama,
                'kelas_tujuan' => $kelasTujuanNama,
                'original_kelas_id' => $kelasAsalId, // Store for rollback
                'status_pembayaran' => $finalStatusPembayaran,
                'persentase_nilai_tuntas' => $eligibility['academic']['percentage'],
                'jumlah_mapel_tuntas' => $eligibility['academic']['tuntas_count'],
                'total_mapel' => $eligibility['academic']['total_mapel'],
                'status_kelulusan' => $statusKelulusan,
                'izin_khusus_ketua' => $eligibility['financial']['is_dispensasi'],
                'tanggal_eksekusi' => $executionDate,
                'is_processed' => true, // Mark as processed
                'rolled_back_at' => null, // Clear any previous rollback
                'rolled_back_by' => null,
                'updated_at' => now(),
                'created_at' => now() // Only on insert
            ]
        );

        return $statusKelulusan;
    }

    private function isFinalYear($siswa)
    {
        if (!$siswa->kelas) return false;
        $nama = strtoupper($siswa->kelas->nama_kelas);
        // Check for 9/IX or 12/XII
        return preg_match('/\b(9|IX|12|XII)\b/', $nama);
    }

    private function findNextClass($currentKelas, $currentYearId)
    {
        if (!$currentKelas) return null;
        
        // Find the TARGET academic year based on current year ID
        // Note: The $currentYearId passed here is actually the "Context Year" (Source).
        // BUT logic assumes checkEligibility passes the Active/Target year...
        // WAIT: The executeStudentPromotion passes $tahunAjaranId.
        // If $tahunAjaranId is 2025/2026 (Source/Active), we need to find class in 2026/2027 (Next).
        
        // Let's refine logic based on implementation:
        // executeStudentPromotion is called with $activeYear->id.
        // So we are looking for Next Class relative to Current Class, BUT inside the NEXT Year?
        // OR is it simply looking for a class named "8A" inside the SAME $tahunAjaranId?
        
        // CORRECTION: 
        // Logic should be: 
        // 1. Get Target Year (Next Year after $tahunAjaranId)
        // 2. Find Class in Target Year.
        
        // Current implementation of 'findNextClass' did:
        // $nextClass = Kelas::where...->where('tahun_ajaran_id', $targetTahunAjaranId)...
        // This implies $tahunAjaranId passed to execute is the TARGET year? NO.
        // checkEligibility uses $activeYear->id (Current).
        
        // FIX: We need to find the NEXT TA first.
        $targetTA = TahunAjaran::where('is_active', false)
            ->where('id', '!=', $currentYearId) 
            ->where('tanggal_mulai', '>', function($q) use ($currentYearId) {
                $q->select('tanggal_mulai')->from('tahun_ajaran')->where('id', $currentYearId);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->first();
            
        if (!$targetTA) return null; // No new year created yet

        $name = $currentKelas->nama_kelas;
        
        // Try Arabic (e.g., 7A -> 8A)
        if (preg_match('/(\d+)/', $name, $matches)) {
            $level = intval($matches[1]);
            $nextLevel = $level + 1;
            $nextNamePattern = preg_replace('/'.$level.'/', $nextLevel, $name, 1);
            
            return Kelas::where('nama_kelas', $nextNamePattern)
                ->where('tahun_ajaran_id', $targetTA->id)
                ->first();
        }
        
        return null;
    }

    private function findSameClass($currentKelas, $currentYearId)
    {
        if (!$currentKelas) return null;

        // Find the TARGET academic year (Same as above)
        $targetTA = TahunAjaran::where('is_active', false)
            ->where('id', '!=', $currentYearId) 
            ->where('tanggal_mulai', '>', function($q) use ($currentYearId) {
                $q->select('tanggal_mulai')->from('tahun_ajaran')->where('id', $currentYearId);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->first();

        if (!$targetTA) return null;

        // Search for class with SAME NAME in Target Year
        // "7A" -> "7A"
        return Kelas::where('nama_kelas', $currentKelas->nama_kelas)
            ->where('tahun_ajaran_id', $targetTA->id)
            ->first();
    }

    /**
     * Rollback a student's promotion.
     * Restores the student to their original class.
     * 
     * @param int $statusId - ID from status_naik_kelas_siswa table
     * @param int $userId - User performing the rollback
     * @return array - Result with success flag and message
     */
    public function rollbackStudent($statusId, $userId)
    {
        $status = DB::table('status_naik_kelas_siswa')->where('id', $statusId)->first();
        
        if (!$status) {
            return ['success' => false, 'message' => 'Data status tidak ditemukan.'];
        }
        
        if ($status->rolled_back_at) {
            return ['success' => false, 'message' => 'Siswa ini sudah pernah di-rollback.'];
        }
        
        if (!$status->original_kelas_id) {
            return ['success' => false, 'message' => 'Tidak ada data kelas asal untuk rollback.'];
        }
        
        DB::beginTransaction();
        try {
            $siswa = Siswa::find($status->siswa_id);
            if (!$siswa) {
                throw new \Exception('Siswa tidak ditemukan.');
            }
            
            // Restore original class
            $siswa->kelas_id = $status->original_kelas_id;
            
            // If status was LULUS, restore to aktif
            if ($status->status_kelulusan === 'LULUS') {
                $siswa->status = 'aktif';
            }
            $siswa->save();
            
            // Mark as rolled back
            DB::table('status_naik_kelas_siswa')
                ->where('id', $statusId)
                ->update([
                    'is_processed' => false,
                    'rolled_back_at' => now(),
                    'rolled_back_by' => $userId,
                    'updated_at' => now(),
                ]);
            
            DB::commit();
            return ['success' => true, 'message' => 'Rollback berhasil untuk siswa ' . $siswa->nama_lengkap];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Gagal rollback: ' . $e->getMessage()];
        }
    }

    /**
     * Promote selected students individually (for those who failed initial batch).
     * 
     * @param array $siswaIds - Array of siswa IDs to promote
     * @param int $tahunAjaranId - Current academic year
     * @return array - Results with count and any errors
     */
    public function promoteSelectedStudents(array $siswaIds, $tahunAjaranId)
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        DB::beginTransaction();
        try {
            foreach ($siswaIds as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if (!$siswa) {
                    $results['failed']++;
                    $results['errors'][] = "Siswa ID {$siswaId} tidak ditemukan.";
                    continue;
                }

                // Re-check eligibility
                $eligibility = $this->checkEligibility($siswa, $tahunAjaranId);

                if (!$eligibility['eligible']) {
                    $results['failed']++;
                    $results['errors'][] = "{$siswa->nama_lengkap}: Belum memenuhi syarat (Keuangan: {$eligibility['financial']['status']}, Akademik: {$eligibility['academic']['percentage']}%).";
                    continue;
                }

                // Execute promotion
                $this->executeStudentPromotion($siswa, $tahunAjaranId, now());
                $results['success']++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = 'Gagal memproses: ' . $e->getMessage();
        }

        return $results;
    }
}

