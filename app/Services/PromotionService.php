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
        // Semua tagihan yang BELUM lunas dihitung — termasuk 'cicilan' (bayar sebagian) —
        // demi keadilan & konsisten dengan gate ujian/rapor (cekSiswaLunas: != 'sudah_bayar').
        // Sebelumnya 'cicilan' terlewat, sehingga siswa yang baru bayar sebagian salah dianggap LUNAS.
        // Nilai tunggakan memakai SISA sebenarnya (jumlah - pembayaran disetujui), bukan tagihan penuh,
        // agar akurat untuk yang sudah menyicil.
        $unpaid = Tagihan::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->whereIn('status', ['belum_bayar', 'cicilan', 'terlambat'])
            ->get()
            ->sum(fn ($t) => $t->sisa_pembayaran);

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
        // TIDAK difilter kelas_id: siswa_id + tahun_ajaran_id sudah unik per rapor akademik
        // siswa di TA itu. kelas_id di baris nilai hanya relevan untuk scoping akses guru
        // saat input, bukan syarat baca ulang — kalau difilter, nilai jadi "hilang" tiap kali
        // siswa pindah kelas di tengah TA yang sama (transfer manual, dsb).
        $gradesByMapel = Nilai::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get()
            ->groupBy('mata_pelajaran_id');

        if ($gradesByMapel->isEmpty()) {
            // 'unmeasurable' cuma info tambahan, TIDAK mengubah is_tuntas/percentage -
            // status sebelum eksekusi tetap jujur "belum tuntas". Dipakai khusus oleh
            // promoteSelectedStudents() untuk membuka celah override manual bagi siswa
            // yang kelasnya memang belum punya Jadwal Pelajaran sama sekali (jadi tidak
            // ada guru yang bisa/berhak mengisi nilai) - beda dengan siswa yang kelasnya
            // sudah lengkap tapi nilainya belum diisi guru (itu harus tetap gagal).
            $kelasPunyaJadwal = $siswa->kelas && $siswa->kelas->jadwalPelajaran()->exists();

            return [
                'is_tuntas' => false,
                'percentage' => 0,
                'tuntas_count' => 0,
                'total_mapel' => 0,
                'unmeasurable' => ! $kelasPunyaJadwal,
            ];
        }

        $totalMapel = $gradesByMapel->count(); // Jumlah mapel unik
        $tuntasCount = 0;
        $jenjang = $siswa->kelas->jenjang ?? 'SMA';

        foreach ($gradesByMapel as $mapelId => $semesterGrades) {
            // Rata-rata nilai_akhir dari semester ganjil + genap — TAPI cuma semester yang
            // sudah pernah diisi (minimal 1 komponen terisi). Baris semester yang belum
            // pernah disentuh sama sekali punya nilai_akhir=0 (default hitungNilaiAkhir()),
            // dan kalau ikut dirata-rata akan menarik turun nilai semester yang sudah tuntas
            // (mis. ganjil 78 + genap kosong 0 -> rata-rata 39, padahal genap belum dijalani).
            $semesterTerisi = $semesterGrades->filter(function ($nilai) {
                foreach (Nilai::COMPONENT_FIELDS as $field) {
                    if ($nilai->{$field} !== null) return true;
                }
                // Tidak ada komponen terisi sama sekali - baru dianggap "kosong" kalau
                // nilai_akhir juga belum pernah diisi eksplisit (null, atau default 0 hasil
                // hitungNilaiAkhir() saat semua komponen null). nilai_akhir yang diisi
                // langsung tanpa lewat komponen (mis. import/override manual) tetap dihitung.
                return $nilai->nilai_akhir !== null && (float) $nilai->nilai_akhir > 0;
            });

            if ($semesterTerisi->isEmpty()) {
                continue;
            }

            $avgNilaiAkhir = $semesterTerisi->avg('nilai_akhir');
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
            'threshold' => $batasTuntas,
            'unmeasurable' => false,
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
     *
     * @param  bool  $forceAcademicOverride  Hanya dipakai jalur manual "Naikkan Terpilih"
     *      (lihat promoteSelectedStudents()) - meloloskan syarat akademik KHUSUS untuk
     *      siswa yang academic['unmeasurable']=true (kelasnya belum punya Jadwal
     *      Pelajaran sama sekali). Syarat keuangan tetap berlaku normal (lunas/dispensasi).
     *      Proses massal (execute()) TIDAK pernah mengirim true di sini.
     */
    public function executeStudentPromotion($siswa, $tahunAjaranId, $executionDate, bool $forceAcademicOverride = false)
    {
        $eligibility = $this->checkEligibility($siswa, $tahunAjaranId);

        $statusKelulusan = 'TIDAK_NAIK_KELAS';
        $finalStatusPembayaran = $eligibility['financial']['status'];

        $akademikOverrideDipakai = false;
        if (! $eligibility['eligible']
            && $forceAcademicOverride
            && ($eligibility['financial']['status'] === 'LUNAS' || $eligibility['financial']['is_dispensasi'])
            && ($eligibility['academic']['unmeasurable'] ?? false)) {
            $eligibility['eligible'] = true;
            $akademikOverrideDipakai = true;
        }

        if ($eligibility['eligible']) {
            // CRITICAL: Check final year FIRST before dispensation logic
            // Final year students ALWAYS graduate regardless of financial status
            if ($this->isFinalYear($siswa)) {
                // Check if they have financial dispensation
                if ($eligibility['financial']['status'] !== 'LUNAS' && $eligibility['financial']['is_dispensasi']) {
                    $statusKelulusan = 'LULUS_TUNGGAKAN'; // Graduated with outstanding bills
                } else {
                    $statusKelulusan = 'LULUS'; // Normal graduation
                }
            } else {
                // For non-final year students, check dispensation
                if ($eligibility['financial']['status'] !== 'LUNAS' && $eligibility['financial']['is_dispensasi']) {
                    $statusKelulusan = 'NAIK_KELAS_TUNGGAKAN';
                } else {
                    $statusKelulusan = 'NAIK_KELAS';
                }
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
        } elseif ($statusKelulusan === 'LULUS' || $statusKelulusan === 'LULUS_TUNGGAKAN') {
            $siswa->status = 'lulus';
            $siswa->kelas_id = null; // Detach from class for alumni
            $siswa->save();
            
            // Update user account status
            // User requested that alumni MUST be able to login (e.g. to check bills)
            // So we ensure is_active is TRUE, not false.
            // Update user account status
            // User requested that alumni MUST be able to login (e.g. to check bills)
            // So we ensure is_active is TRUE, not false.
            $user = $siswa->user; 
            if ($user) {
                $user->is_active = true;
                $user->save();
            }
            
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
                'akademik_override_tanpa_jadwal' => $akademikOverrideDipakai,
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

        // Notifikasi HASIL kenaikan kelas ke siswa & orang tua.
        // Choke point tunggal: mencakup eksekusi manual, terjadwal, & promote-selected.
        // Hanya hasil positif (NAIK/LULUS) yang dikirim (lihat method).
        app(\App\Services\NotificationService::class)
            ->notifyHasilKenaikanKelas($siswa, $statusKelulusan, $kelasTujuanNama);

        return $statusKelulusan;
    }

    private function isFinalYear($siswa)
    {
        if (!$siswa->kelas) return false;

        $jenjang = $siswa->kelas->jenjang ?? null; // SD, SMP, SMA/SMK
        $nama = strtoupper($siswa->kelas->nama_kelas ?? '');

        // 1. Check by Jenjang with improved regex (word boundaries)
        // \b matches "6" in "6A", "Kelas 6", "6-A", "VI B", etc.

        if ($jenjang === 'SD') {
            // Match 6 or VI as a word boundary (works with "6A", "6-A", "Kelas 6")
            if (preg_match('/\b(6|VI)\b/i', $nama)) return true;
        }
        if ($jenjang === 'SMP') {
            if (preg_match('/\b(9|IX)\b/i', $nama)) return true;
        }
        if ($jenjang === 'SMA' || $jenjang === 'SMA/SMK') {
            if (preg_match('/\b(12|XII)\b/i', $nama)) return true;
        }

        // 2. Fallback: Check by grade number if jenjang is missing or ambiguous
        // More robust - check if the grade number appears anywhere in the name
        if ($jenjang) {
            if ($jenjang == 'SD' && preg_match('/6/', $nama)) return true;
            if ($jenjang == 'SMP' && preg_match('/9/', $nama)) return true;
            if (($jenjang == 'SMA' || $jenjang == 'SMA/SMK') && preg_match('/12/', $nama)) return true;
        }

        return false;
    }

    private function findNextClass($currentKelas, $currentYearId)
    {
        if (!$currentKelas) return null;
        
        // Find the TARGET academic year
        $targetTA = TahunAjaran::where('is_active', false)
            ->where('id', '!=', $currentYearId) 
            ->where('tanggal_mulai', '>', function($q) use ($currentYearId) {
                $q->select('tanggal_mulai')->from('tahun_ajaran')->where('id', $currentYearId);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->first();
            
        if (!$targetTA) return null;

        $name = $currentKelas->nama_kelas;
        $cabangId = $currentKelas->cabang_id; // Branch Isolation
        
        // Try to increment numeric level (e.g., 7A -> 8A)
        // Matches "7" in "7A", "7-A", "Kelas 7"
        if (preg_match('/(\d+)/', $name, $matches)) {
            $level = intval($matches[1]);
            $nextLevel = $level + 1;
            
            // Construct fuzzy search pattern
            // If "7A", we look for "8A"
            // We replace the FIRST occurrence of the level number
            $nextNamePattern = preg_replace('/'.$level.'/', $nextLevel, $name, 1);
            
            // Fix: Strict Cabang filtering
            $query = Kelas::where('tahun_ajaran_id', $targetTA->id)
                ->where('jenjang', $currentKelas->jenjang); // Same Jenjang

            if ($cabangId) {
                $query->where('cabang_id', $cabangId);
            }
                
            $nextClass = $query->where('nama_kelas', $nextNamePattern)->first();
            
            if ($nextClass) return $nextClass;
            
            // Fallback: If exact replace fails (e.g. maybe structure changes?), try wildcards?
            // For now, let's trust the naming convention remains consistent (7A -> 8A).
        }
        
        return null;
    }

    private function findSameClass($currentKelas, $currentYearId)
    {
        if (!$currentKelas) return null;

        $targetTA = TahunAjaran::where('is_active', false)
            ->where('id', '!=', $currentYearId) 
            ->where('tanggal_mulai', '>', function($q) use ($currentYearId) {
                $q->select('tanggal_mulai')->from('tahun_ajaran')->where('id', $currentYearId);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->first();

        if (!$targetTA) return null;

        // Search for class with SAME NAME and SAME BRANCH
        $query = Kelas::where('nama_kelas', $currentKelas->nama_kelas)
            ->where('tahun_ajaran_id', $targetTA->id)
            ->where('jenjang', $currentKelas->jenjang);

        if ($currentKelas->cabang_id) {
            $query->where('cabang_id', $currentKelas->cabang_id);
        }

        return $query->first();
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
            
            // If status was LULUS or LULUS_TUNGGAKAN, restore to aktif and reactivate user account
            if ($status->status_kelulusan === 'LULUS' || $status->status_kelulusan === 'LULUS_TUNGGAKAN') {
                $siswa->status = 'aktif';
                
                // Restore user account access
                $user = $siswa->user;
                if ($user) {
                    $user->is_active = true;
                    $user->save();
                }
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
     * Tentukan apakah syarat akademik boleh dilewatkan manual: HANYA untuk siswa yang
     * keuangannya sudah lunas/dispensasi tapi akademiknya "unmeasurable" (kelasnya
     * belum punya Jadwal Pelajaran sama sekali - memang tidak ada yang bisa diukur,
     * bukan soal nilai kurang). Dipakai bersama oleh promoteSelectedStudents() dan
     * eksekusi massal (PromotionReportController::execute()) supaya konsisten -
     * sebelumnya eksekusi massal tidak memakai celah ini sama sekali, jadi siswa yang
     * seharusnya lolos manual malah tercatat TIDAK_NAIK_KELAS.
     */
    public function computeAcademicOverride(array $eligibility): bool
    {
        $financialOk = $eligibility['financial']['status'] === 'LUNAS' || $eligibility['financial']['is_dispensasi'];

        return ! $eligibility['eligible'] && $financialOk && ($eligibility['academic']['unmeasurable'] ?? false);
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
                $academicOverride = $this->computeAcademicOverride($eligibility);

                if (!$eligibility['eligible'] && !$academicOverride) {
                    $results['failed']++;
                    $results['errors'][] = "{$siswa->nama_lengkap}: Belum memenuhi syarat (Keuangan: {$eligibility['financial']['status']}, Akademik: {$eligibility['academic']['percentage']}%).";
                    continue;
                }

                // Execute promotion
                $this->executeStudentPromotion($siswa, $tahunAjaranId, now(), $academicOverride);
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

