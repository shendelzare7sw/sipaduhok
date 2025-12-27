<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaranData = [
            [
                'nama_tahun_ajaran' => '2023/2024',
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2024-06-30',
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2025-06-30',
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2025/2026',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2026-06-30',
                'is_active' => true,
            ],
        ];

        foreach ($tahunAjaranData as $tahunAjaran) {
            TahunAjaran::create($tahunAjaran);
        }
    }
}