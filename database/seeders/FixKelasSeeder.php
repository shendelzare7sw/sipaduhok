<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class FixKelasSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua data kelas yang ada
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kelas::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kelasData = [
            // ===== CABANG PAMULANG (ID: 1) - PKBM HOUSE OF KNOWLEDGE =====
            // LENGKAP: KB, TKA, TKB, SD 1-6, SMP 7-9, SMA 10-12

            // KB (Kelompok Bermain)
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 6, // Linda Permata
                'nama_kelas' => 'KB1',
                'jenjang' => 'KB',
                'kode_kelas' => 'RUKO-KB1-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 10, // Dian Anggraini
                'nama_kelas' => 'KB2',
                'jenjang' => 'KB',
                'kode_kelas' => 'RUKO-KB2-2025',
                'kuota_siswa' => 20,
            ],

            // TKA (Taman Kanak-Kanak A)
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 4, // Nurul Hidayah
                'nama_kelas' => 'TKA1',
                'jenjang' => 'TKA',
                'kode_kelas' => 'RUKO-TKA1-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 8, // Mega Puspita
                'nama_kelas' => 'TKA2',
                'jenjang' => 'TKA',
                'kode_kelas' => 'RUKO-TKA2-2025',
                'kuota_siswa' => 20,
            ],

            // TKB (Taman Kanak-Kanak B)
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 12, // Lilis Suryani
                'nama_kelas' => 'TKB1',
                'jenjang' => 'TKB',
                'kode_kelas' => 'RUKO-TKB1-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'TKB2',
                'jenjang' => 'TKB',
                'kode_kelas' => 'RUKO-TKB2-2025',
                'kuota_siswa' => 20,
            ],

            // SD Kelas 1-6
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 5, // Arif Budiman
                'nama_kelas' => '1A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-1A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 9, // Faisal Rahman
                'nama_kelas' => '1B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-1B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '2A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-2A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '2B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-2B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '3A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-3A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '3B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-3B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '4A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-4A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '4B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-4B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '5A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-5A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '5B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-5B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '6A',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-6A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '6B',
                'jenjang' => 'SD',
                'kode_kelas' => 'RUKO-6B-2025',
                'kuota_siswa' => 25,
            ],

            // SMP Kelas 7-9
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 3, // Hendra Kusuma
                'nama_kelas' => '7A',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-7A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 7, // Rudi Hartono
                'nama_kelas' => '7B',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-7B-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '8A',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-8A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '8B',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-8B-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '9A',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-9A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '9B',
                'jenjang' => 'SMP',
                'kode_kelas' => 'RUKO-9B-2025',
                'kuota_siswa' => 30,
            ],

            // SMA Kelas 10-12
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 2, // Rina Wijaya
                'nama_kelas' => '10A',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-10A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '10B',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-10B-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '11A',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-11A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '11B',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-11B-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '12A',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-12A-2025',
                'kuota_siswa' => 30,
            ],
            [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '12B',
                'jenjang' => 'SMA',
                'kode_kelas' => 'RUKO-12B-2025',
                'kuota_siswa' => 30,
            ],

            // ===== CABANG PAUD BRATASENA (ID: 2) - HANYA PAUD =====
            // KB, TKA, TKB SAJA

            // KB (Kelompok Bermain)
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'KB1',
                'jenjang' => 'KB',
                'kode_kelas' => 'PAUD-KB1-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'KB2',
                'jenjang' => 'KB',
                'kode_kelas' => 'PAUD-KB2-2025',
                'kuota_siswa' => 20,
            ],

            // TKA (Taman Kanak-Kanak A)
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'TKA1',
                'jenjang' => 'TKA',
                'kode_kelas' => 'PAUD-TKA1-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'TKA2',
                'jenjang' => 'TKA',
                'kode_kelas' => 'PAUD-TKA2-2025',
                'kuota_siswa' => 20,
            ],

            // TKB (Taman Kanak-Kanak B)
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'TKB1',
                'jenjang' => 'TKB',
                'kode_kelas' => 'PAUD-TKB1-2025',
                'kuota_siswa' => 20,
            ],
            [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => 'TKB2',
                'jenjang' => 'TKB',
                'kode_kelas' => 'PAUD-TKB2-2025',
                'kuota_siswa' => 20,
            ],

            // ===== CABANG CIMANGGIS (ID: 3) - HANYA SD =====
            // SD Kelas 1-6 SAJA

            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => 11, // Yoga Pratama
                'nama_kelas' => '1A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-1A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '1B',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-1B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '2A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-2A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '2B',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-2B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '3A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-3A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '3B',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-3B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '4A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-4A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '4B',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-4B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '5A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-5A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '5B',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-5B-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '6A',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-6A-2025',
                'kuota_siswa' => 25,
            ],
            [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => '6B',
                'jenjang' => 'SD',
                'kode_kelas' => 'CMNGS-6B-2025',
                'kuota_siswa' => 25,
            ],
        ];

        foreach ($kelasData as $kelas) {
            Kelas::create($kelas);
        }

        $this->command->info('✓ Data kelas berhasil diperbaiki sesuai struktur real!');
        $this->command->info('');
        $this->command->info('DISTRIBUSI KELAS PER CABANG:');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📍 PKBM HOUSE OF KNOWLEDGE PAMULANG - LENGKAP');
        $this->command->info('   • KB: 2 kelas (KB1, KB2)');
        $this->command->info('   • TKA: 2 kelas (TKA1, TKA2)');
        $this->command->info('   • TKB: 2 kelas (TKB1, TKB2)');
        $this->command->info('   • SD: 12 kelas (1A-6B)');
        $this->command->info('   • SMP: 6 kelas (7A-9B)');
        $this->command->info('   • SMA: 6 kelas (10A-12B)');
        $this->command->info('   TOTAL: 30 kelas');
        $this->command->info('');
        $this->command->info('📍 PAUD HOUSE OF KNOWLEDGE BRATASENA - PAUD ONLY');
        $this->command->info('   • KB: 2 kelas (KB1, KB2)');
        $this->command->info('   • TKA: 2 kelas (TKA1, TKA2)');
        $this->command->info('   • TKB: 2 kelas (TKB1, TKB2)');
        $this->command->info('   TOTAL: 6 kelas');
        $this->command->info('');
        $this->command->info('📍 HOUSE OF KNOWLEDGE CIMANGGIS - SD ONLY');
        $this->command->info('   • SD: 12 kelas (1A-6B)');
        $this->command->info('   TOTAL: 12 kelas');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('GRAND TOTAL: ' . count($kelasData) . ' kelas');
    }
}
