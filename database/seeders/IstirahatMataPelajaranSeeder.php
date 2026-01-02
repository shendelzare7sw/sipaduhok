<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DEPRECATED - Seeder ini sudah tidak digunakan lagi
 *
 * Sebelumnya: Membuat mata pelajaran "Istirahat" yang bisa dipilih saat membuat jadwal
 * Sekarang: Menggunakan sistem Pengaturan Istirahat (pengaturan_istirahat table)
 *
 * Waktu istirahat sekarang dikelola melalui:
 * - Admin > Pengaturan Istirahat
 * - Auto-inject ke jadwal cetak
 * - Auto-block waktu saat membuat jadwal
 */
class IstirahatMataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @deprecated Gunakan PengaturanIstirahat model dan controller
     */
    public function run(): void
    {
        $this->command->warn("⚠ DEPRECATED: Seeder ini sudah tidak digunakan lagi!");
        $this->command->info("→ Gunakan menu Admin > Pengaturan Istirahat untuk mengatur waktu istirahat");

        // Tidak melakukan apa-apa, seeder deprecated
        return;
    }
}
