<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelasData = [
            // Cabang Ruko (ID: 1) - 5 kelas
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3, // 2025/2026
                'wali_kelas_id' => 1, // Dewi Lestari
                'nama_kelas' => 'SMP Paket A',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-SMP-A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 2, // Rina Wijaya
                'nama_kelas' => 'SMA Paket A',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-SMA-A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 3, // Hendra Kusuma
                'nama_kelas' => 'SD Paket A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-SD-A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 7, // Rudi Hartono
                'nama_kelas' => 'SD Paket B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-SD-B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 9, // Faisal Rahman
                'nama_kelas' => 'SMP Paket B',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-SMP-B-2025',
                'kuota_siswa' => 30,
            ],

            // Cabang PAUD (ID: 2) - 4 kelas
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 4, // Nurul Hidayah
                'nama_kelas' => 'Kelompok Bermain A',
                'jenjang' => 'PAUD',
                'kode_kelas' => 'PAUD-KB-A-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 8, // Mega Puspita
                'nama_kelas' => 'TK A',
                'jenjang' => 'PAUD',
                'kode_kelas' => 'PAUD-TKA-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 12, // Lilis Suryani
                'nama_kelas' => 'TK B',
                'jenjang' => 'PAUD',
                'kode_kelas' => 'PAUD-TKB-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'Kelompok Bermain B',
                'jenjang' => 'PAUD',
                'kode_kelas' => 'PAUD-KB-B-2025',
                'kuota_siswa' => 20,
            ],

            // Cabang Cimanggis (ID: 3) - 5 kelas
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 5, // Arif Budiman
                'nama_kelas' => 'SMP Paket A',
                'jenjang' => 'SMP',
                'kode_kelas' => 'CMNGS-SMP-A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 6, // Linda Permata
                'nama_kelas' => 'SMA Paket A',
                'jenjang' => 'SMA',
                'kode_kelas' => 'CMNGS-SMA-A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 10, // Dian Anggraini
                'nama_kelas' => 'SMA Paket B',
                'jenjang' => 'SMA',
                'kode_kelas' => 'CMNGS-SMA-B-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 11, // Yoga Pratama
                'nama_kelas' => 'SD Paket A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-SD-A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'SMP Paket B',
                'jenjang' => 'SMP',
                'kode_kelas' => 'CMNGS-SMP-B-2025',
                'kuota_siswa' => 30,
            ],
        ];

        foreach ($kelasData as $kelas) {
            Kelas::create($kelas);
        }
    }
}