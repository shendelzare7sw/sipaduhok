<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateCapaianKompetensi;
use App\Models\MataPelajaran;

class TemplateCapaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define template variations
        $templates = [
            [
                'nama' => 'Sangat Baik',
                'template' => 'Siswa menunjukkan pemahaman yang sangat baik terhadap materi pelajaran. Mampu menerapkan konsep dengan tepat dan menunjukkan kreativitas dalam penyelesaian masalah. Terus pertahankan prestasi yang luar biasa ini.'
            ],
            [
                'nama' => 'Baik',
                'template' => 'Siswa menunjukkan pemahaman yang baik terhadap materi pelajaran. Mampu menerapkan konsep dengan cukup tepat. Terus tingkatkan latihan untuk mencapai hasil yang lebih optimal.'
            ],
            [
                'nama' => 'Cukup',
                'template' => 'Siswa cukup memahami materi pelajaran. Masih perlu bimbingan dalam menerapkan konsep. Diharapkan lebih fokus dan rajin berlatih untuk meningkatkan pemahaman.'
            ],
            [
                'nama' => 'Kurang',
                'template' => 'Siswa masih kurang memahami materi pelajaran. Memerlukan bimbingan ekstra dan latihan intensif. Disarankan untuk lebih aktif bertanya dan mengulang materi yang belum dikuasai.'
            ],
        ];

        // Get first 10 mata pelajaran (subjects)
        $mataPelajaranList = MataPelajaran::orderBy('id')->limit(10)->get();

        if ($mataPelajaranList->isEmpty()) {
            $this->command->warn('No mata_pelajaran found. Please seed mata_pelajaran table first.');
            return;
        }

        $this->command->info('Creating template capaian for ' . $mataPelajaranList->count() . ' subjects...');

        foreach ($mataPelajaranList as $mapel) {
            foreach ($templates as $template) {
                TemplateCapaianKompetensi::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'nama_template' => $template['nama'],
                    'template_text' => $template['template'],
                    'created_by' => null, // System generated
                ]);
            }
            $this->command->info("✓ Created 4 templates for: {$mapel->nama_mapel}");
        }

        $this->command->info('✓ Template capaian seeding completed successfully!');
    }
}
