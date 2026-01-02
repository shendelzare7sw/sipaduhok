<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixUserTenagaPendidikSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * CRITICAL FIX: Sinkronkan SEMUA data antara users dan tenaga_pendidik
     *
     * MASALAH UTAMA:
     * - Email di tabel users tidak sama dengan email di tenaga_pendidik
     * - Username tidak sesuai dengan role sebenarnya
     * - Admin melihat data yang salah di menu tenaga pendidik
     *
     * SOLUSI:
     * - Tabel USERS = sumber kebenaran untuk credentials (email, username, password)
     * - Tabel TENAGA_PENDIDIK harus mengikuti data di users
     */
    public function run(): void
    {
        $this->command->info("🔧 Memperbaiki TOTAL SINKRONISASI users ↔ tenaga_pendidik...\n");

        // DATA MASTER: Email dan username yang BENAR
        $correctData = [
            // STRUKTURAL
            ['tp_id' => 1, 'user_id' => 5, 'nama' => 'Siti Nurhaliza, S.Pd', 'email' => 'sekretaris@sipaduhok.com', 'username' => 'sekretaris', 'role' => 'sekretaris'],
            ['tp_id' => 2, 'user_id' => 6, 'nama' => 'Ahmad Dahlan, S.E', 'email' => 'bendahara@sipaduhok.com', 'username' => 'bendahara', 'role' => 'bendahara'],
            ['tp_id' => 25, 'user_id' => 4, 'nama' => 'Dr. Budi Santoso, M.Pd', 'email' => 'ketua@sipaduhok.com', 'username' => 'ketua_pkbm', 'role' => 'ketua_pkbm'],

            // WALI KELAS - SMP
            ['tp_id' => 3, 'user_id' => 7, 'nama' => 'Hendra Kusuma, S.Pd', 'email' => 'wali.smp.a@sipaduhok.com', 'username' => 'wali_smp_a', 'role' => 'wali_kelas'],
            ['tp_id' => 7, 'user_id' => 11, 'nama' => 'Rudi Hartono, S.Pd', 'email' => 'wali.smp.b@sipaduhok.com', 'username' => 'wali_smp_b', 'role' => 'wali_kelas'],
            ['tp_id' => 11, 'user_id' => 15, 'nama' => 'Dewi Lestari, S.Pd', 'email' => 'wali.smp.c@sipaduhok.com', 'username' => 'wali_smp_c', 'role' => 'wali_kelas'],

            // WALI KELAS - SD
            ['tp_id' => 5, 'user_id' => 9, 'nama' => 'Arif Budiman, S.Pd', 'email' => 'wali.sd.a@sipaduhok.com', 'username' => 'wali_sd_a', 'role' => 'wali_kelas'],
            ['tp_id' => 9, 'user_id' => 13, 'nama' => 'Faisal Rahman, S.Pd', 'email' => 'wali.sd.b@sipaduhok.com', 'username' => 'wali_sd_b', 'role' => 'wali_kelas'],
            ['tp_id' => 13, 'user_id' => 17, 'nama' => 'Yoga Pratama, S.Pd', 'email' => 'wali.sd.c@sipaduhok.com', 'username' => 'wali_sd_c', 'role' => 'wali_kelas'],

            // WALI KELAS - SMA
            ['tp_id' => 4, 'user_id' => 8, 'nama' => 'Nurul Hidayah, S.Paud', 'email' => 'wali.sma.a@sipaduhok.com', 'username' => 'wali_sma_a', 'role' => 'wali_kelas'],
            ['tp_id' => 8, 'user_id' => 12, 'nama' => 'Mega Puspita, S.Paud', 'email' => 'wali.sma.b@sipaduhok.com', 'username' => 'wali_sma_b', 'role' => 'wali_kelas'],
            ['tp_id' => 12, 'user_id' => 16, 'nama' => 'Lilis Suryani, S.Paud', 'email' => 'wali.sma.c@sipaduhok.com', 'username' => 'wali_sma_c', 'role' => 'wali_kelas'],

            // WALI KELAS - PAUD
            ['tp_id' => 6, 'user_id' => 10, 'nama' => 'Linda Permata, S.Pd', 'email' => 'wali.paud.a@sipaduhok.com', 'username' => 'wali_paud_a', 'role' => 'wali_kelas'],
            ['tp_id' => 10, 'user_id' => 14, 'nama' => 'Dian Anggraini, S.Pd', 'email' => 'wali.paud.b@sipaduhok.com', 'username' => 'wali_paud_b', 'role' => 'wali_kelas'],
            ['tp_id' => 14, 'user_id' => 18, 'nama' => 'Sri Mulyani, S.Pd', 'email' => 'wali.paud.c@sipaduhok.com', 'username' => 'wali_paud_c', 'role' => 'wali_kelas'],

            // GURU PENGAJAR
            ['tp_id' => 15, 'user_id' => 19, 'nama' => 'Bambang Sutrisno, S.Si', 'email' => 'guru.matematika@sipaduhok.com', 'username' => 'guru_matematika', 'role' => 'guru_pengajar'],
            ['tp_id' => 16, 'user_id' => 20, 'nama' => 'Ratna Sari, S.Pd', 'email' => 'guru.bahasa@sipaduhok.com', 'username' => 'guru_bahasa', 'role' => 'guru_pengajar'],
            ['tp_id' => 17, 'user_id' => 21, 'nama' => 'Agus Setiawan, M.Si', 'email' => 'guru.ipa@sipaduhok.com', 'username' => 'guru_ipa', 'role' => 'guru_pengajar'],
            ['tp_id' => 18, 'user_id' => 22, 'nama' => 'Wulan Dari, S.Pd', 'email' => 'guru.ips@sipaduhok.com', 'username' => 'guru_ips', 'role' => 'guru_pengajar'],
            ['tp_id' => 19, 'user_id' => 23, 'nama' => 'Hendro Wijaya, S.Si', 'email' => 'guru.fisika@sipaduhok.com', 'username' => 'guru_fisika', 'role' => 'guru_pengajar'],
            ['tp_id' => 20, 'user_id' => 24, 'nama' => 'Sinta Dewi, S.Pd', 'email' => 'guru.kimia@sipaduhok.com', 'username' => 'guru_kimia', 'role' => 'guru_pengajar'],
            ['tp_id' => 21, 'user_id' => 25, 'nama' => 'Yudi Santoso, M.Pd', 'email' => 'guru.biologi@sipaduhok.com', 'username' => 'guru_biologi', 'role' => 'guru_pengajar'],
            ['tp_id' => 22, 'user_id' => 26, 'nama' => 'Ani Susanti, S.Pd', 'email' => 'guru.inggris@sipaduhok.com', 'username' => 'guru_inggris', 'role' => 'guru_pengajar'],
            ['tp_id' => 23, 'user_id' => 27, 'nama' => 'Budi Prasetyo, S.E', 'email' => 'guru.sejarah@sipaduhok.com', 'username' => 'guru_sejarah', 'role' => 'guru_pengajar'],
            ['tp_id' => 24, 'user_id' => 28, 'nama' => 'Maya Kartika, S.Pd', 'email' => 'guru.geografi@sipaduhok.com', 'username' => 'guru_geografi', 'role' => 'guru_pengajar'],
        ];

        $updated = 0;
        foreach ($correctData as $data) {
            // Update USERS (sumber kebenaran)
            DB::table('users')->where('id', $data['user_id'])->update([
                'name' => $data['nama'],
                'email' => $data['email'],
                'username' => $data['username'],
                'role' => $data['role'],
            ]);

            // Update TENAGA_PENDIDIK (harus mengikuti users)
            DB::table('tenaga_pendidik')->where('id', $data['tp_id'])->update([
                'nama_lengkap' => $data['nama'],
                'email' => $data['email'],
                'user_id' => $data['user_id'],
            ]);

            $updated++;
            $this->command->info("✓ {$data['nama']} → {$data['email']}");
        }

        $this->command->info("\n═══════════════════════════════════════════════════");
        $this->command->info("✅ TOTAL SINKRONISASI SELESAI!");
        $this->command->info("   {$updated} record berhasil diupdate");
        $this->command->info("═══════════════════════════════════════════════════\n");

        // Sekarang perbaiki assignment wali kelas agar sesuai jenjang
        $this->command->info("🔧 Memperbaiki assignment wali kelas...\n");

        // Reset semua assignment dulu
        DB::table('kelas')->update(['wali_kelas_id' => null]);

        // Assignment yang benar berdasarkan jenjang dan cabang
        $assignments = [
            // ===== CABANG PAMULANG (ID: 1) =====

            // KB - PAUD
            ['kelas_nama' => 'KB1', 'cabang_id' => 1, 'wali_username' => 'wali_paud_a'],  // Linda Permata
            ['kelas_nama' => 'KB2', 'cabang_id' => 1, 'wali_username' => 'wali_paud_b'],  // Dian Anggraini

            // TKA - PAUD
            ['kelas_nama' => 'TKA1', 'cabang_id' => 1, 'wali_username' => 'wali_paud_c'],  // Sri Mulyani

            // SD
            ['kelas_nama' => '1A', 'cabang_id' => 1, 'wali_username' => 'wali_sd_a'],      // Arif Budiman
            ['kelas_nama' => '1B', 'cabang_id' => 1, 'wali_username' => 'wali_sd_b'],      // Faisal Rahman

            // SMP
            ['kelas_nama' => '7A', 'cabang_id' => 1, 'wali_username' => 'wali_smp_a'],     // Hendra Kusuma
            ['kelas_nama' => '7B', 'cabang_id' => 1, 'wali_username' => 'wali_smp_b'],     // Rudi Hartono

            // SMA
            ['kelas_nama' => '10A', 'cabang_id' => 1, 'wali_username' => 'wali_sma_a'],    // Nurul Hidayah
            ['kelas_nama' => '12B', 'cabang_id' => 1, 'wali_username' => 'wali_smp_c'],    // Dewi Lestari (SMP wali mengajar SMA, karena bisa random)

            // ===== CABANG CIMANGGIS (ID: 3) - SD ONLY =====
            ['kelas_nama' => '1A', 'cabang_id' => 3, 'wali_username' => 'wali_sd_c'],      // Yoga Pratama
        ];

        foreach ($assignments as $assignment) {
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

            $updated = DB::table('kelas')
                ->where('nama_kelas', $assignment['kelas_nama'])
                ->where('cabang_id', $assignment['cabang_id'])
                ->update(['wali_kelas_id' => $tenagaPendidik->id, 'updated_at' => now()]);

            if ($updated) {
                $cabangName = DB::table('cabang')->where('id', $assignment['cabang_id'])->value('nama_cabang');
                $this->command->info("✓ {$assignment['kelas_nama']} ({$cabangName}) → {$tenagaPendidik->nama_lengkap}");
            } else {
                $this->command->warn("⚠ Kelas {$assignment['kelas_nama']} di cabang {$assignment['cabang_id']} tidak ditemukan!");
            }
        }

        $this->command->info("\n═══════════════════════════════════════════════════");
        $this->command->info("✓ Assignment wali kelas berhasil diperbaiki!");

        $totalAssigned = DB::table('kelas')->whereNotNull('wali_kelas_id')->count();
        $totalKelas = DB::table('kelas')->count();
        $this->command->info("Kelas dengan wali: {$totalAssigned}/{$totalKelas}");
        $this->command->info("═══════════════════════════════════════════════════\n");
    }
}
