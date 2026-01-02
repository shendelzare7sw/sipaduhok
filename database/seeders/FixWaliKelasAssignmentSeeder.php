<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixWaliKelasAssignmentSeeder extends Seeder
{
    /**
     * Run the migrations.
     *
     * Struktur cabang yang benar:
     * - Cabang 1 (PKBM House Of Knowledge Pamulang): KB, TKA, TKB, SD 1-6, SMP 7-9, SMA 10-12
     * - Cabang 2 (PAUD House Of Knowledge Bratasena): KB, TKA, TKB saja
     * - Cabang 3 (House Of Knowledge Cimanggis): SD 1-6 saja
     */
    public function run(): void
    {
        // Reset semua wali_kelas_id dulu
        DB::table('kelas')->update(['wali_kelas_id' => null]);

        // Mapping berdasarkan username yang logis
        $assignments = [
            // ===== CABANG PAMULANG (ID: 1) - LENGKAP =====

            // KB
            ['kelas_nama' => 'KB1', 'cabang_id' => 1, 'wali_username' => 'wali_paud_a'],  // Linda Permata (tp_id 6)
            ['kelas_nama' => 'KB2', 'cabang_id' => 1, 'wali_username' => 'wali_paud_b'],  // Dian Anggraini (tp_id 10)

            // TKA
            ['kelas_nama' => 'TKA1', 'cabang_id' => 1, 'wali_username' => 'wali_sma_a'],  // Nurul Hidayah (tp_id 4)
            ['kelas_nama' => 'TKA2', 'cabang_id' => 1, 'wali_username' => 'wali_sma_b'],  // Mega Puspita (tp_id 8)

            // TKB
            ['kelas_nama' => 'TKB1', 'cabang_id' => 1, 'wali_username' => 'wali_sma_c'],  // Lilis Suryani (tp_id 12)
            // TKB2 belum ada wali

            // SD
            ['kelas_nama' => '1A', 'cabang_id' => 1, 'wali_username' => 'wali_sd_a'],     // Arif Budiman (tp_id 5)
            ['kelas_nama' => '1B', 'cabang_id' => 1, 'wali_username' => 'wali_sd_b'],     // Faisal Rahman (tp_id 9)
            // 2A-6B belum ada wali

            // SMP
            ['kelas_nama' => '7A', 'cabang_id' => 1, 'wali_username' => 'wali_smp_a'],    // Hendra Kusuma (tp_id 3)
            ['kelas_nama' => '7B', 'cabang_id' => 1, 'wali_username' => 'wali_smp_b'],    // Rudi Hartono (tp_id 7)
            // 8A-9B belum ada wali

            // SMA
            ['kelas_nama' => '10A', 'cabang_id' => 1, 'wali_username' => 'bendahara'],     // Rina Wijaya (tp_id 2)
            // 10B-12B belum ada wali

            // ===== CABANG BRATASENA (ID: 2) - PAUD ONLY =====
            // Belum ada wali yang assigned untuk cabang ini

            // ===== CABANG CIMANGGIS (ID: 3) - SD ONLY =====
            ['kelas_nama' => '1A', 'cabang_id' => 3, 'wali_username' => 'wali_smp_c'],    // Yoga Pratama (tp_id 11)
            // 1B-6B belum ada wali
        ];

        foreach ($assignments as $assignment) {
            // Get user and tenaga_pendidik
            $user = DB::table('users')->where('username', $assignment['wali_username'])->first();

            if (!$user) {
                $this->command->warn("⚠ User {$assignment['wali_username']} tidak ditemukan!");
                continue;
            }

            $tenagaPendidik = DB::table('tenaga_pendidik')->where('user_id', $user->id)->first();

            if (!$tenagaPendidik) {
                $this->command->warn("⚠ Tenaga pendidik untuk user {$assignment['wali_username']} tidak ditemukan!");
                continue;
            }

            // Update kelas
            $updated = DB::table('kelas')
                ->where('nama_kelas', $assignment['kelas_nama'])
                ->where('cabang_id', $assignment['cabang_id'])
                ->update(['wali_kelas_id' => $tenagaPendidik->id]);

            if ($updated) {
                $cabangName = DB::table('cabang')->where('id', $assignment['cabang_id'])->value('nama_cabang');
                $this->command->info("✓ {$assignment['kelas_nama']} ({$cabangName}) → {$tenagaPendidik->nama_lengkap}");
            } else {
                $this->command->warn("⚠ Kelas {$assignment['kelas_nama']} di cabang {$assignment['cabang_id']} tidak ditemukan!");
            }
        }

        $this->command->info("\n═══════════════════════════════════════════════════");
        $this->command->info("✓ Assignment wali kelas berhasil diperbaiki!");
        $this->command->info("Total assignment: " . count($assignments));

        // Show summary
        $totalWithWali = DB::table('kelas')->whereNotNull('wali_kelas_id')->count();
        $totalKelas = DB::table('kelas')->count();
        $this->command->info("Kelas dengan wali: {$totalWithWali}/{$totalKelas}");
    }
}
