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

        // Get all grades
        $grades = Nilai::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('kelas_id', $siswa->kelas_id)
            ->get();

        if ($grades->isEmpty()) {
            return [
                'is_tuntas' => false,
                'percentage' => 0,
                'tuntas_count' => 0,
                'total_mapel' => 0
            ];
        }

        $totalMapel = $grades->count();
        $tuntasCount = 0;

        foreach ($grades as $grade) {
            $kkm = $this->getKKM($grade->mata_pelajaran_id, $tahunAjaranId, $siswa->kelas->jenjang ?? 'SMP');
            if ($grade->nilai_akhir >= $kkm) {
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
            if (!$eligibility['financial']['status'] === 'LUNAS' && $eligibility['financial']['is_dispensasi']) {
                 $statusKelulusan = 'NAIK_KELAS_TUNGGAKAN';
            }
        }

        // Determine destination class
        $kelasAsalId = $siswa->kelas_id;
        $kelasAsalNama = $siswa->kelas->nama_kelas ?? '-';
        $kelasTujuanId = null;
        $kelasTujuanNama = null;

        if (in_array($statusKelulusan, ['NAIK_KELAS', 'NAIK_KELAS_TUNGGAKAN'])) {
            // Find next class in the TARGET academic year ($tahunAjaranId is the target)
            $nextClass = $this->findNextClass($siswa->kelas, $tahunAjaranId);
            if ($nextClass) {
                $kelasTujuanId = $nextClass->id;
                $kelasTujuanNama = $nextClass->nama_kelas;
                
                // Update Student
                $siswa->kelas_id = $kelasTujuanId;
                $siswa->save();
            } else {
                // Warning: Promoted but no class found
                // Don't update siswa kelas_id, wait for admin
            }
        } elseif ($statusKelulusan === 'LULUS') {
            $siswa->status = 'lulus';
            // Optional: $siswa->kelas_id = null; // Or keep for history
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
        return preg_match('/(9|IX|12|XII)/', $nama);
        // Note: Better regex or logic if needed, but this covers standard defaults
    }

    private function findNextClass($currentKelas, $targetTahunAjaranId)
    {
        if (!$currentKelas) return null;
        
        // Simple logic: Increment integer in name. Keep suffix.
        // ex: "7A" -> "8A", "VII-A" -> "VIII-A", "Kelas 10" -> "Kelas 11"
        $name = $currentKelas->nama_kelas;
        
        // Helper to convert Roman to Int and back could be complex. 
        // Let's assume standard Arabic numerals first id: 36
        
        // Try Arabic (e.g., 7A, 8B, Kelas 10)
        if (preg_match('/(\d+)/', $name, $matches)) {
            $level = intval($matches[1]);
            $nextLevel = $level + 1;
            
            // Reconstruct name with new level
            // We need to be careful to only replace the level number
            // "Kelas 10 IPA 1" -> "Kelas 11 IPA 1"
            // "7A" -> "8A"
            $nextNamePattern = preg_replace('/'.$level.'/', $nextLevel, $name, 1);
            
            // Search in TARGET Year
            $nextClass = Kelas::where('nama_kelas', $nextNamePattern)
                ->where('tahun_ajaran_id', $targetTahunAjaranId)
                ->first();
                
            return $nextClass;
        }
        
        // TODO: Handle Roman Numerals if necessary (VII -> VIII)
        
        return null;
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
        
        return $results;
    }
}

