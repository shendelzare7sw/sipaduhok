<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixStrukturalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Memperbaiki data struktural (Ketua PKBM, Sekretaris, Bendahara):
     * - Sinkronkan nama user dengan tenaga_pendidik
     * - Pastikan email dan username konsisten
     * - Buat data tenaga_pendidik untuk ketua PKBM
     */
    public function run(): void
    {
        $this->command->info("🔧 Memperbaiki data struktural PKBM...\n");

        // Data struktural yang benar
        $strukturalData = [
            [
                'role' => 'ketua_pkbm',
                'nama_lengkap' => 'Dr. Budi Santoso, M.Pd',
                'email' => 'ketua@sipaduhok.com',
                'username' => 'ketua_pkbm',
                'nip' => '196801011990031001',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1968-01-01',
                'alamat' => 'Jl. Raya Pamulang No. 1, Tangerang Selatan',
                'telepon' => '081234567801',
                'pendidikan_terakhir' => 'S3 Pendidikan',
                'cabang_id' => 1, // PKBM HOK Pamulang
            ],
            [
                'role' => 'sekretaris',
                'nama_lengkap' => 'Siti Nurhaliza, S.Pd',
                'email' => 'sekretaris@sipaduhok.com',
                'username' => 'sekretaris',
                'nip' => '197205101995122001',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1972-05-10',
                'alamat' => 'Jl. Raya Pamulang No. 2, Tangerang Selatan',
                'telepon' => '081234567802',
                'pendidikan_terakhir' => 'S1 Administrasi',
                'cabang_id' => 1,
            ],
            [
                'role' => 'bendahara',
                'nama_lengkap' => 'Ahmad Dahlan, S.E',
                'email' => 'bendahara@sipaduhok.com',
                'username' => 'bendahara',
                'nip' => '197503151996031002',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1975-03-15',
                'alamat' => 'Jl. Raya Pamulang No. 3, Tangerang Selatan',
                'telepon' => '081234567803',
                'pendidikan_terakhir' => 'S1 Ekonomi',
                'cabang_id' => 1,
            ],
        ];

        foreach ($strukturalData as $data) {
            // Cari atau buat user
            $user = DB::table('users')->where('email', $data['email'])->first();

            if ($user) {
                // Update user yang sudah ada
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'name' => $data['nama_lengkap'],
                        'username' => $data['username'],
                        'role' => $data['role'],
                        'cabang_id' => $data['cabang_id'],
                        'is_active' => true,
                        'updated_at' => now(),
                    ]);

                $this->command->info("✓ Update user: {$data['email']}");
                $userId = $user->id;
            } else {
                // Buat user baru
                $userId = DB::table('users')->insertGetId([
                    'name' => $data['nama_lengkap'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'password' => Hash::make('password123'),
                    'role' => $data['role'],
                    'cabang_id' => $data['cabang_id'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✓ Buat user baru: {$data['email']}");
            }

            // Cari atau buat tenaga_pendidik
            $tenagaPendidik = DB::table('tenaga_pendidik')->where('user_id', $userId)->first();

            if ($tenagaPendidik) {
                // Update tenaga_pendidik yang sudah ada
                DB::table('tenaga_pendidik')
                    ->where('user_id', $userId)
                    ->update([
                        'nip' => $data['nip'],
                        'nama_lengkap' => $data['nama_lengkap'],
                        'jenis_kelamin' => $data['jenis_kelamin'],
                        'tempat_lahir' => $data['tempat_lahir'],
                        'tanggal_lahir' => $data['tanggal_lahir'],
                        'alamat' => $data['alamat'],
                        'telepon' => $data['telepon'],
                        'email' => $data['email'],
                        'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                        'updated_at' => now(),
                    ]);

                $this->command->info("  ✓ Update tenaga pendidik: {$data['nama_lengkap']}");
            } else {
                // Buat tenaga_pendidik baru
                DB::table('tenaga_pendidik')->insert([
                    'user_id' => $userId,
                    'nip' => $data['nip'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'alamat' => $data['alamat'],
                    'telepon' => $data['telepon'],
                    'email' => $data['email'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("  ✓ Buat tenaga pendidik baru: {$data['nama_lengkap']}");
            }
        }

        $this->command->info("\n═══════════════════════════════════════════════════");
        $this->command->info("✓ Data struktural berhasil diperbaiki!");
        $this->command->info("═══════════════════════════════════════════════════\n");

        // Tampilkan ringkasan
        $this->command->info("Akun yang tersedia:");
        $this->command->info("1. Ketua PKBM:");
        $this->command->info("   Email: ketua@sipaduhok.com | Username: ketua_pkbm | Password: password123");
        $this->command->info("   Nama: Dr. Budi Santoso, M.Pd\n");

        $this->command->info("2. Sekretaris:");
        $this->command->info("   Email: sekretaris@sipaduhok.com | Username: sekretaris | Password: password123");
        $this->command->info("   Nama: Siti Nurhaliza, S.Pd\n");

        $this->command->info("3. Bendahara:");
        $this->command->info("   Email: bendahara@sipaduhok.com | Username: bendahara | Password: password123");
        $this->command->info("   Nama: Ahmad Dahlan, S.E\n");

        $this->command->info("═══════════════════════════════════════════════════\n");
    }
}
