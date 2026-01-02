<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixAdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Memperbaiki data admin:
     * - Hanya ada 2 admin saja
     * - admin1@sipaduhok.com (username: admin1)
     * - admin2@sipaduhok.com (username: admin2)
     * - Hapus admin@sipaduhok.com
     */
    public function run(): void
    {
        $this->command->info("🔧 Memperbaiki data admin...");

        // Hapus admin ketiga (admin@sipaduhok.com)
        $adminToDelete = DB::table('users')
            ->where('email', 'admin@sipaduhok.com')
            ->orWhere('username', 'admin')
            ->first();

        if ($adminToDelete) {
            DB::table('users')->where('id', $adminToDelete->id)->delete();
            $this->command->info("✓ Menghapus admin: {$adminToDelete->email}");
        }

        // Pastikan 2 admin utama ada dan terstandarisasi
        $admins = [
            [
                'name' => 'Admin Utama 1',
                'email' => 'admin1@sipaduhok.com',
                'username' => 'admin1',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Admin Utama 2',
                'email' => 'admin2@sipaduhok.com',
                'username' => 'admin2',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ],
        ];

        foreach ($admins as $adminData) {
            $existing = DB::table('users')
                ->where('email', $adminData['email'])
                ->first();

            if ($existing) {
                // Update existing admin
                DB::table('users')
                    ->where('id', $existing->id)
                    ->update([
                        'name' => $adminData['name'],
                        'username' => $adminData['username'],
                        'role' => $adminData['role'],
                        'is_active' => $adminData['is_active'],
                        'updated_at' => now(),
                    ]);
                $this->command->info("✓ Update admin: {$adminData['email']}");
            } else {
                // Create new admin
                DB::table('users')->insert(array_merge($adminData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $this->command->info("✓ Buat admin baru: {$adminData['email']}");
            }
        }

        // Hapus admin lain yang tidak seharusnya ada
        $validEmails = ['admin1@sipaduhok.com', 'admin2@sipaduhok.com'];
        $extraAdmins = DB::table('users')
            ->where('role', 'admin')
            ->whereNotIn('email', $validEmails)
            ->get();

        foreach ($extraAdmins as $extra) {
            DB::table('users')->where('id', $extra->id)->delete();
            $this->command->warn("⚠ Menghapus admin ekstra: {$extra->email}");
        }

        $this->command->info("\n═══════════════════════════════════════════════════");
        $this->command->info("✓ Data admin berhasil diperbaiki!");

        $totalAdmins = DB::table('users')->where('role', 'admin')->count();
        $this->command->info("Total admin: {$totalAdmins}");

        $this->command->info("\nAdmin yang tersedia:");
        $this->command->info("1. Email: admin1@sipaduhok.com | Username: admin1 | Password: password123");
        $this->command->info("2. Email: admin2@sipaduhok.com | Username: admin2 | Password: password123");
        $this->command->info("═══════════════════════════════════════════════════\n");
    }
}
