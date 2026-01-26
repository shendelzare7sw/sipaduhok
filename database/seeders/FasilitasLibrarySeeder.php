<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;
use App\Models\LandingPageSection;

class FasilitasLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Find the 'fasilitas' LandingPage
        $fasilitas = LandingPage::where('slug', 'fasilitas')->first();

        if (!$fasilitas) {
            $this->command->error("Landing Page 'fasilitas' not found. Please run LandingPageSeeder first.");
            return;
        }

        $this->command->info("Found Fasilitas Page ID: " . $fasilitas->id);

        // 2. Add 'perpustakaan' section
        LandingPageSection::updateOrCreate(
            [
                'landing_page_id' => $fasilitas->id,
                'section_key' => 'perpustakaan'
            ],
            [
                'type' => 'rich_text',
                'order' => 5, // Order after Area Bermain (which is 4)
                'content' => [
                    'badge' => 'Fasilitas Edukasi',
                    'title' => 'Perpustakaan',
                    'description' => 'Perpustakaan lengkap dengan koleksi buku yang beragam untuk menunjang kegiatan literasi dan pembelajaran siswa. Dilengkapi dengan area baca yang nyaman dan tenang.',
                    'feature_1_title' => 'Koleksi Lengkap',
                    'feature_1_desc' => 'Buku pelajaran, fiksi, ensiklopedia, dan buku referensi lainnya',
                    'feature_2_title' => 'Area Baca Nyaman',
                    'feature_2_desc' => 'Dilengkapi bean bag dan karpet untuk kenyamanan membaca',
                    'feature_3_title' => 'Digital Corner',
                    'feature_3_desc' => 'Akses ke perpustakaan digital dan sumber belajar online',
                ]
            ]
        );

        $this->command->info("Section 'perpustakaan' created/updated successfully.");
    }
}
