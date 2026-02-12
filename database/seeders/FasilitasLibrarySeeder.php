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
                'type' => 'list', // Changed to list for carousel items
                'order' => 5, 
                'content' => [
                    'header' => [
                        'badge' => 'Fasilitas Edukasi',
                        'title' => 'Perpustakaan',
                        'description' => 'Perpustakaan dengan koleksi lengkap untuk menumbuhkan minat baca dan literasi siswa. Ruangan yang nyaman dengan koleksi buku yang terus diperbarui.',
                        'icon' => 'book-reader', // Default Icon
                        'icon_color' => 'secondary',
                        'feature_1_title' => 'Koleksi Lengkap',
                        'feature_1_desc' => 'Buku pelajaran, fiksi, ensiklopedia, dan buku referensi lainnya',
                        'feature_2_title' => 'Area Baca Nyaman',
                        'feature_2_desc' => 'Dilengkapi bean bag dan karpet untuk kenyamanan membaca',
                        'feature_3_title' => 'Digital Corner',
                        'feature_3_desc' => 'Akses ke perpustakaan digital dan sumber belajar online',
                    ],
                    'items' => [
                        // Using existing images or placeholders if specific ones aren't available, assuming generic names or reusing existing ones for now as placeholders if actual files don't exist yet, but user asked for dynamic.
                        // I will use placeholders from other sections or generic ones if unavailable, but better to keep it empty or use what's likely there. 
                        // I'll assume some library images exist or use generic ones.
                        ['image' => 'img/fasilitas-perpustakaan-1.jpg', 'title' => 'Koleksi Buku Lengkap', 'description' => 'Ribuan buku berkualitas untuk menunjang pembelajaran.'],
                        ['image' => 'img/fasilitas-perpustakaan-2.jpg', 'title' => 'Area Baca Nyaman', 'description' => 'Tempat yang tenang dan nyaman untuk membaca.'],
                        ['image' => 'img/fasilitas-perpustakaan-3.jpg', 'title' => 'Suasana Tenang', 'description' => 'Lingkungan yang kondusif untuk fokus belajar.'],
                        ['image' => 'img/fasilitas-perpustakaan-4.jpg', 'title' => 'Digital Corner', 'description' => 'Akses komputer dan internet untuk referensi digital.'],
                    ]
                ]
            ]
        );

        $this->command->info("Section 'perpustakaan' created/updated successfully.");
    }
}
