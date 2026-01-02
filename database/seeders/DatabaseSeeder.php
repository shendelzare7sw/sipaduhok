<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // New role-based system seeders (run first)
            RoleSeeder::class,
            SuperAdminSeeder::class,

            // Existing seeders
            CabangSeeder::class,
            TahunAjaranSeeder::class,
            UserSeeder::class,
            TenagaPendidikSeeder::class,
            MataPelajaranSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,

            // OrangTuaSeeder dibuat otomatis oleh SiswaSeeder
            // OrangTuaSeeder::class, // DISABLED - konflik dengan SiswaSeeder
        ]);
    }
}