<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixUserNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * KONSEP BARU:
     * - Username TIDAK TERIKAT dengan jenjang (SD/SMP/SMA/PAUD)
     * - Wali kelas bisa di-assign ke kelas manapun oleh admin
     * - Username menggunakan format: wali.namaguru@sipaduhok.com
     * - Username simple, tidak ada embel-embel jenjang
     */
    public function run(): void
    {
        $this->command->info("🔧 Memperbaiki sistem username - menghapus ketergantungan jenjang...\n");

        // DATA BARU: Username berdasarkan nama, bukan jenjang
        $correctData = [
            // STRUKTURAL (tidak berubah)
            ['tp_id' => 1, 'user_id' => 5, 'nama' => 'Siti Nurhaliza, S.Pd', 'email' => 'sekretaris@sipaduhok.com', 'username' => 'sekretaris', 'role' => 'sekretaris'],
            ['tp_id' => 2, 'user_id' => 6, 'nama' => 'Ahmad Dahlan, S.E', 'email' => 'bendahara@sipaduhok.com', 'username' => 'bendahara', 'role' => 'bendahara'],
            ['tp_id' => 25, 'user_id' => 4, 'nama' => 'Dr. Budi Santoso, M.Pd', 'email' => 'ketua@sipaduhok.com', 'username' => 'ketua_pkbm', 'role' => 'ketua_pkbm'],

            // WALI KELAS - Format: wali.namaguru@sipaduhok.com
            ['tp_id' => 3, 'user_id' => 7, 'nama' => 'Hendra Kusuma, S.Pd', 'email' => 'wali.hendrakusuma@sipaduhok.com', 'username' => 'wali_hendrakusuma', 'role' => 'wali_kelas'],
            ['tp_id' => 7, 'user_id' => 11, 'nama' => 'Rudi Hartono, S.Pd', 'email' => 'wali.rudihartono@sipaduhok.com', 'username' => 'wali_rudihartono', 'role' => 'wali_kelas'],
            ['tp_id' => 11, 'user_id' => 15, 'nama' => 'Dewi Lestari, S.Pd', 'email' => 'wali.dewilestari@sipaduhok.com', 'username' => 'wali_dewilestari', 'role' => 'wali_kelas'],
            ['tp_id' => 5, 'user_id' => 9, 'nama' => 'Arif Budiman, S.Pd', 'email' => 'wali.arifbudiman@sipaduhok.com', 'username' => 'wali_arifbudiman', 'role' => 'wali_kelas'],
            ['tp_id' => 9, 'user_id' => 13, 'nama' => 'Faisal Rahman, S.Pd', 'email' => 'wali.faisalrahman@sipaduhok.com', 'username' => 'wali_faisalrahman', 'role' => 'wali_kelas'],
            ['tp_id' => 13, 'user_id' => 17, 'nama' => 'Yoga Pratama, S.Pd', 'email' => 'wali.yogapratama@sipaduhok.com', 'username' => 'wali_yogapratama', 'role' => 'wali_kelas'],
            ['tp_id' => 4, 'user_id' => 8, 'nama' => 'Nurul Hidayah, S.Paud', 'email' => 'wali.nurulhidayah@sipaduhok.com', 'username' => 'wali_nurulhidayah', 'role' => 'wali_kelas'],
            ['tp_id' => 8, 'user_id' => 12, 'nama' => 'Mega Puspita, S.Paud', 'email' => 'wali.megapuspita@sipaduhok.com', 'username' => 'wali_megapuspita', 'role' => 'wali_kelas'],
            ['tp_id' => 12, 'user_id' => 16, 'nama' => 'Lilis Suryani, S.Paud', 'email' => 'wali.lilissuryani@sipaduhok.com', 'username' => 'wali_lilissuryani', 'role' => 'wali_kelas'],
            ['tp_id' => 6, 'user_id' => 10, 'nama' => 'Linda Permata, S.Pd', 'email' => 'wali.lindapermata@sipaduhok.com', 'username' => 'wali_lindapermata', 'role' => 'wali_kelas'],
            ['tp_id' => 10, 'user_id' => 14, 'nama' => 'Dian Anggraini, S.Pd', 'email' => 'wali.diananggraini@sipaduhok.com', 'username' => 'wali_diananggraini', 'role' => 'wali_kelas'],
            ['tp_id' => 14, 'user_id' => 18, 'nama' => 'Sri Mulyani, S.Pd', 'email' => 'wali.srimulyani@sipaduhok.com', 'username' => 'wali_srimulyani', 'role' => 'wali_kelas'],

            // GURU PENGAJAR - Format: guru.namaguru@sipaduhok.com
            ['tp_id' => 15, 'user_id' => 19, 'nama' => 'Bambang Sutrisno, S.Si', 'email' => 'guru.bambangsutrisno@sipaduhok.com', 'username' => 'guru_bambangsutrisno', 'role' => 'guru_pengajar'],
            ['tp_id' => 16, 'user_id' => 20, 'nama' => 'Ratna Sari, S.Pd', 'email' => 'guru.ratnasari@sipaduhok.com', 'username' => 'guru_ratnasari', 'role' => 'guru_pengajar'],
            ['tp_id' => 17, 'user_id' => 21, 'nama' => 'Agus Setiawan, M.Si', 'email' => 'guru.agussetiawan@sipaduhok.com', 'username' => 'guru_agussetiawan', 'role' => 'guru_pengajar'],
            ['tp_id' => 18, 'user_id' => 22, 'nama' => 'Wulan Dari, S.Pd', 'email' => 'guru.wulandari@sipaduhok.com', 'username' => 'guru_wulandari', 'role' => 'guru_pengajar'],
            ['tp_id' => 19, 'user_id' => 23, 'nama' => 'Hendro Wijaya, S.Si', 'email' => 'guru.hendrowijaya@sipaduhok.com', 'username' => 'guru_hendrowijaya', 'role' => 'guru_pengajar'],
            ['tp_id' => 20, 'user_id' => 24, 'nama' => 'Sinta Dewi, S.Pd', 'email' => 'guru.sintadewi@sipaduhok.com', 'username' => 'guru_sintadewi', 'role' => 'guru_pengajar'],
            ['tp_id' => 21, 'user_id' => 25, 'nama' => 'Yudi Santoso, M.Pd', 'email' => 'guru.yudisantoso@sipaduhok.com', 'username' => 'guru_yudisantoso', 'role' => 'guru_pengajar'],
            ['tp_id' => 22, 'user_id' => 26, 'nama' => 'Ani Susanti, S.Pd', 'email' => 'guru.anisusanti@sipaduhok.com', 'username' => 'guru_anisusanti', 'role' => 'guru_pengajar'],
            ['tp_id' => 23, 'user_id' => 27, 'nama' => 'Budi Prasetyo, S.E', 'email' => 'guru.budiprasetyo@sipaduhok.com', 'username' => 'guru_budiprasetyo', 'role' => 'guru_pengajar'],
            ['tp_id' => 24, 'user_id' => 28, 'nama' => 'Maya Kartika, S.Pd', 'email' => 'guru.mayakartika@sipaduhok.com', 'username' => 'guru_mayakartika', 'role' => 'guru_pengajar'],
        ];

        $updated = 0;
        foreach ($correctData as $data) {
            // Update USERS
            DB::table('users')->where('id', $data['user_id'])->update([
                'name' => $data['nama'],
                'email' => $data['email'],
                'username' => $data['username'],
                'role' => $data['role'],
                'password' => Hash::make('password123'),
            ]);

            // Update TENAGA_PENDIDIK
            DB::table('tenaga_pendidik')->where('id', $data['tp_id'])->update([
                'nama_lengkap' => $data['nama'],
                'email' => $data['email'],
                'user_id' => $data['user_id'],
            ]);

            $updated++;
            $this->command->info("✓ {$data['nama']} → {$data['email']}");
        }

        $this->command->info("\n═══════════════════════════════════════════════════");
        $this->command->info("✅ USERNAME SYSTEM BERHASIL DIPERBAIKI!");
        $this->command->info("   {$updated} record berhasil diupdate");
        $this->command->info("   Username sekarang fleksibel, tidak terikat jenjang");
        $this->command->info("   Wali kelas bisa di-assign ke kelas manapun");
        $this->command->info("═══════════════════════════════════════════════════\n");

        $this->command->info("🔑 Password default untuk semua user: password123\n");
    }
}
