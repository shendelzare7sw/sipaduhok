<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelasData = [];

        // ========================================
        // Gedung Utama PKBM House Of Knowledge (ID: 1)
        // KB, TKA, TKB, SD (Paket A), SMP (Paket B), SMA (Paket C)
        // ========================================

        // KB: KB1, KB2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "KB{$i}",
                'jenjang' => 'KB',
                'kode_kelas' => "RUKO-KB{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // TKA: TKA1, TKA2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "TKA{$i}",
                'jenjang' => 'TKA',
                'kode_kelas' => "RUKO-TKA{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // TKB: TKB1, TKB2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 1,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "TKB{$i}",
                'jenjang' => 'TKB',
                'kode_kelas' => "RUKO-TKB{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // SD Paket A: 1A, 1B, 2A, 2B, ..., 6A, 6B
        for ($tingkat = 1; $tingkat <= 6; $tingkat++) {
            foreach (['A', 'B'] as $rombel) {
                $kelasData[] = [
                    'cabang_id' => 1,
                    'tahun_ajaran_id' => 3,
                    'wali_kelas_id' => null,
                    'nama_kelas' => "{$tingkat}{$rombel}",
                    'jenjang' => 'SD',
                    'kode_kelas' => "RUKO-{$tingkat}{$rombel}-2025",
                    'kuota_siswa' => 25,
                ];
            }
        }

        // SMP Paket B: 7A, 7B, 8A, 8B, 9A, 9B
        for ($tingkat = 7; $tingkat <= 9; $tingkat++) {
            foreach (['A', 'B'] as $rombel) {
                $kelasData[] = [
                    'cabang_id' => 1,
                    'tahun_ajaran_id' => 3,
                    'wali_kelas_id' => null,
                    'nama_kelas' => "{$tingkat}{$rombel}",
                    'jenjang' => 'SMP',
                    'kode_kelas' => "RUKO-{$tingkat}{$rombel}-2025",
                    'kuota_siswa' => 30,
                ];
            }
        }

        // SMA Paket C: 10A, 10B, 11A, 11B, 12A, 12B
        for ($tingkat = 10; $tingkat <= 12; $tingkat++) {
            foreach (['A', 'B'] as $rombel) {
                $kelasData[] = [
                    'cabang_id' => 1,
                    'tahun_ajaran_id' => 3,
                    'wali_kelas_id' => null,
                    'nama_kelas' => "{$tingkat}{$rombel}",
                    'jenjang' => 'SMA',
                    'kode_kelas' => "RUKO-{$tingkat}{$rombel}-2025",
                    'kuota_siswa' => 30,
                ];
            }
        }

        // ========================================
        // PAUD House Of Knowledge (ID: 2)
        // KB, TKA, TKB
        // ========================================

        // KB: KB1, KB2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "KB{$i}",
                'jenjang' => 'KB',
                'kode_kelas' => "PAUD-KB{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // TKA: TKA1, TKA2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "TKA{$i}",
                'jenjang' => 'TKA',
                'kode_kelas' => "PAUD-TKA{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // TKB: TKB1, TKB2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 2,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "TKB{$i}",
                'jenjang' => 'TKB',
                'kode_kelas' => "PAUD-TKB{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // ========================================
        // House Of Knowledge Cimanggis (ID: 3)
        // SD (Paket A), KB, TKA, TKB, SMP (Paket B)
        // ========================================

        // SD Paket A: 1A, 1B, 2A, 2B, ..., 6A, 6B
        for ($tingkat = 1; $tingkat <= 6; $tingkat++) {
            foreach (['A', 'B'] as $rombel) {
                $kelasData[] = [
                    'cabang_id' => 3,
                    'tahun_ajaran_id' => 3,
                    'wali_kelas_id' => null,
                    'nama_kelas' => "{$tingkat}{$rombel}",
                    'jenjang' => 'SD',
                    'kode_kelas' => "CMNGS-{$tingkat}{$rombel}-2025",
                    'kuota_siswa' => 25,
                ];
            }
        }

        // KB: KB1, KB2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "KB{$i}",
                'jenjang' => 'KB',
                'kode_kelas' => "CMNGS-KB{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // TKA: TKA1, TKA2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "TKA{$i}",
                'jenjang' => 'TKA',
                'kode_kelas' => "CMNGS-TKA{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // TKB: TKB1, TKB2
        for ($i = 1; $i <= 2; $i++) {
            $kelasData[] = [
                'cabang_id' => 3,
                'tahun_ajaran_id' => 3,
                'wali_kelas_id' => null,
                'nama_kelas' => "TKB{$i}",
                'jenjang' => 'TKB',
                'kode_kelas' => "CMNGS-TKB{$i}-2025",
                'kuota_siswa' => 20,
            ];
        }

        // SMP Paket B: 7A, 7B, 8A, 8B, 9A, 9B
        for ($tingkat = 7; $tingkat <= 9; $tingkat++) {
            foreach (['A', 'B'] as $rombel) {
                $kelasData[] = [
                    'cabang_id' => 3,
                    'tahun_ajaran_id' => 3,
                    'wali_kelas_id' => null,
                    'nama_kelas' => "{$tingkat}{$rombel}",
                    'jenjang' => 'SMP',
                    'kode_kelas' => "CMNGS-{$tingkat}{$rombel}-2025",
                    'kuota_siswa' => 30,
                ];
            }
        }

        foreach ($kelasData as $kelas) {
            Kelas::create($kelas);
        }
    }
}
