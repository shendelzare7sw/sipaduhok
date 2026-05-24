<?php

use App\Models\LandingPage;
use App\Models\LandingPageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $ppdb = LandingPage::where('slug', 'ppdb')->first();
        if (!$ppdb) {
            return;
        }

        $sections = [
            [
                'section_key' => 'syarat_header',
                'type' => 'rich_text',
                'order' => 4,
                'content' => [
                    'badge' => 'Persyaratan',
                    'title' => 'Syarat Pendaftaran',
                    'description' => 'Siapkan dokumen-dokumen berikut untuk melengkapi pendaftaran Anda',
                ],
            ],
            [
                'section_key' => 'syarat_paud',
                'type' => 'list',
                'order' => 5,
                'content' => [
                    'header' => [
                        'tab_label' => 'PAUD',
                        'title' => 'Syarat PAUD',
                        'color' => '#fac030',
                        'icon' => 'fa-book',
                    ],
                    'items' => [
                        ['text' => 'Fotocopy Akta Kelahiran (2 lembar)'],
                        ['text' => 'Fotocopy Kartu Keluarga (2 lembar)'],
                        ['text' => 'Fotocopy KTP Orang Tua (2 lembar)'],
                        ['text' => 'Pas foto anak 3x4 (4 lembar, background merah)'],
                        ['text' => 'Usia minimal 3 tahun'],
                    ],
                ],
            ],
            [
                'section_key' => 'syarat_paket_a',
                'type' => 'list',
                'order' => 6,
                'content' => [
                    'header' => [
                        'tab_label' => 'SD (Paket A)',
                        'title' => 'Syarat Paket A (Setara SD)',
                        'color' => '#165fac',
                        'icon' => 'fa-graduation-cap',
                    ],
                    'items' => [
                        ['text' => 'Fotocopy Ijazah PAUD/TK atau Surat Keterangan (2 lembar)'],
                        ['text' => 'Fotocopy Akta Kelahiran (2 lembar)'],
                        ['text' => 'Fotocopy Kartu Keluarga (2 lembar)'],
                        ['text' => 'Fotocopy KTP Orang Tua (2 lembar)'],
                        ['text' => 'Pas foto 3x4 (6 lembar, background merah)'],
                        ['text' => 'Usia minimal 7 tahun atau maksimal 12 tahun'],
                    ],
                ],
            ],
            [
                'section_key' => 'syarat_paket_b',
                'type' => 'list',
                'order' => 7,
                'content' => [
                    'header' => [
                        'tab_label' => 'SMP (Paket B)',
                        'title' => 'Syarat Paket B (Setara SMP)',
                        'color' => '#287f3b',
                        'icon' => 'fa-book-open',
                    ],
                    'items' => [
                        ['text' => 'Fotocopy Ijazah SD/Paket A (2 lembar)'],
                        ['text' => 'Fotocopy SKHUN SD (2 lembar)'],
                        ['text' => 'Fotocopy Akta Kelahiran (2 lembar)'],
                        ['text' => 'Fotocopy Kartu Keluarga (2 lembar)'],
                        ['text' => 'Fotocopy KTP atau KTP Orang Tua (2 lembar)'],
                        ['text' => 'Pas foto 3x4 (6 lembar, background biru)'],
                    ],
                ],
            ],
            [
                'section_key' => 'syarat_paket_c',
                'type' => 'list',
                'order' => 8,
                'content' => [
                    'header' => [
                        'tab_label' => 'SMA (Paket C)',
                        'title' => 'Syarat Paket C (Setara SMA)',
                        'color' => '#d45930',
                        'icon' => 'fa-bullseye',
                    ],
                    'items' => [
                        ['text' => 'Fotocopy Ijazah SMP/Paket B (2 lembar)'],
                        ['text' => 'Fotocopy SKHUN SMP (2 lembar)'],
                        ['text' => 'Fotocopy Akta Kelahiran (2 lembar)'],
                        ['text' => 'Fotocopy Kartu Keluarga (2 lembar)'],
                        ['text' => 'Fotocopy KTP Peserta Didik (2 lembar)'],
                        ['text' => 'Pas foto 3x4 (6 lembar, background merah)'],
                        ['text' => 'Pilih jurusan: IPA atau IPS'],
                    ],
                ],
            ],
            [
                'section_key' => 'syarat_inklusi',
                'type' => 'list',
                'order' => 9,
                'content' => [
                    'header' => [
                        'tab_label' => 'Pendidikan Inklusi',
                        'title' => 'Syarat Pendidikan Inklusi',
                        'color' => '#a855f7',
                        'icon' => 'fa-heart',
                        'note' => 'Pendidikan inklusi kami dirancang untuk memberikan kesempatan belajar yang setara bagi anak berkebutuhan khusus. Kami menyediakan pendampingan khusus dan kurikulum yang disesuaikan dengan kebutuhan setiap peserta didik.',
                    ],
                    'items' => [
                        ['text' => 'Persyaratan dokumen sesuai jenjang yang diambil'],
                        ['text' => 'Surat keterangan dari dokter/psikolog (jika ada)'],
                        ['text' => 'Asesmen awal kemampuan peserta didik'],
                    ],
                ],
            ],
        ];

        foreach ($sections as $s) {
            LandingPageSection::firstOrCreate(
                ['landing_page_id' => $ppdb->id, 'section_key' => $s['section_key']],
                [
                    'type' => $s['type'],
                    'order' => $s['order'],
                    'content' => $s['content'],
                    'is_visible' => true,
                ]
            );
        }

        // Pindahkan order investasi & biaya ke 10–14 supaya tidak bentrok dengan syarat
        $reorder = [
            'investasi' => 10,
            'biaya_paud' => 11,
            'biaya_sd' => 12,
            'biaya_smp' => 13,
            'biaya_sma' => 14,
        ];
        foreach ($reorder as $key => $order) {
            LandingPageSection::where('landing_page_id', $ppdb->id)
                ->where('section_key', $key)
                ->update(['order' => $order]);
        }
    }

    public function down(): void
    {
        $ppdb = LandingPage::where('slug', 'ppdb')->first();
        if (!$ppdb) {
            return;
        }

        LandingPageSection::where('landing_page_id', $ppdb->id)
            ->whereIn('section_key', ['syarat_header', 'syarat_paud', 'syarat_paket_a', 'syarat_paket_b', 'syarat_paket_c', 'syarat_inklusi'])
            ->delete();
    }
};
