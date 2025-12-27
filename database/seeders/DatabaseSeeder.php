<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CabangSeeder::class,
            TahunAjaranSeeder::class,
            UserSeeder::class,
            TenagaPendidikSeeder::class,
            MataPelajaranSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
        ]);
    }
}