<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;
use App\Models\LandingPageSection;

class FasilitasImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fasilitas = LandingPage::where('slug', 'fasilitas')->first();

        if (!$fasilitas) {
            $this->command->error("Landing Page 'fasilitas' not found.");
            return;
        }

        $sections = [
            'ruang_belajar' => 'img/fasilitas-ruang-belajar-',
            'ruang_terapi' => 'img/fasilitas-ruang-terapi-',
            'area_bermain' => 'img/fasilitas-area-bermain-',
        ];

        foreach ($sections as $key => $prefix) {
            $section = LandingPageSection::where('landing_page_id', $fasilitas->id)
                ->where('section_key', $key)
                ->first();

            if ($section) {
                $content = $section->content;

                // Add images if not present (or update to ensure keys exist)
                // We use merge to preserve existing text content
                $newContent = array_merge($content ?? [], [
                    'image_1' => $content['image_1'] ?? $prefix . '1.jpg',
                    'image_2' => $content['image_2'] ?? $prefix . '2.jpg',
                    'image_3' => $content['image_3'] ?? $prefix . '3.jpg',
                    'image_4' => $content['image_4'] ?? $prefix . '4.jpg',
                ]);

                $section->content = $newContent;
                $section->save();
                
                $this->command->info("Updated section '$key' with image keys.");
            } else {
                $this->command->warn("Section '$key' not found.");
            }
        }
    }
}
