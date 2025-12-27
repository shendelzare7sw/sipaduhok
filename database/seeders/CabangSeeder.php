<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabang;

class CabangSeeder extends Seeder
{
    public function run(): void
    {
        $cabangData = [
            [
                'kode_cabang' => 'RUKO',
                'nama_cabang' => 'PKBM House Of Knowledge (Gedung Utama)',
                'alamat' => 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan',
                'telepon' => '021-7412345',
                'is_active' => true,
            ],
            [
                'kode_cabang' => 'PAUD',
                'nama_cabang' => 'PAUD House Of Knowledge',
                'alamat' => 'Jl. Bratasena I, Pondok Benda, Pondok Benda Tangerang Selatan, Banten',
                'telepon' => '021-7412346',
                'is_active' => true,
            ],
            [
                'kode_cabang' => 'CMNGS',
                'nama_cabang' => 'House Of Knowledge Cimanggis',
                'alamat' => 'Jl. Otista Raya Blok A25, Ruko Prima Ciputat, Tangerang Selatan, Banten',
                'telepon' => '021-7412347',
                'is_active' => true,
            ],
        ];

        foreach ($cabangData as $cabang) {
            Cabang::create($cabang);
        }
    }
}