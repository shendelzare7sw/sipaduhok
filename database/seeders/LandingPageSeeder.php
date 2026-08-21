<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\LandingPageSection;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. HOME PAGE
        // ==========================================
        $home = LandingPage::updateOrCreate(
            ['slug' => 'home'],
            ['title' => 'Beranda', 'order' => 1]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'badge' => 'Selamat Datang di SipaduHOK!',
                    'title_1' => 'Sistem',
                    'title_highlight_1' => 'Pembelajaran',
                    'title_2' => 'dan',
                    'title_highlight_2' => 'Akademik',
                    'description' => 'House Of Knowledge menyediakan media pembelajaran dan akademik berbasis website "SipaduHOK" sebagai media pembelajaran online yang lebih fleksibel',
                    'button_text' => 'Jelajahi Sekarang',
                    'button_link' => '#program',
                    'image' => 'img/hero-homeschooling.webp',
                    'background_image' => 'img/hero-bg.jpg',
                    'experience_years' => '14+',
                    'active_students' => '200+'
                ]
            ]
        );

        // Stats Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'stats'],
            [
                'type' => 'list',
                'order' => 2,
                'content' => [
                    [
                        'value' => '200+',
                        'label' => 'Siswa Aktif',
                        'icon_color' => 'primary',
                        'icon' => null
                    ],
                    [
                        'value' => '50+',
                        'label' => 'Tenaga Pengajar',
                        'icon_color' => 'secondary',
                        'icon' => null
                    ],
                    [
                        'value' => '14+',
                        'label' => 'Tahun Pengalaman',
                        'icon_color' => 'accent-yellow',
                        'icon' => null
                    ],
                    [
                        'value' => '98%',
                        'label' => 'Tingkat Kelulusan',
                        'icon_color' => 'accent-orange',
                        'icon' => null
                    ]
                ]
            ]
        );

        // Program Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'program'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Program Kami',
                        'title' => 'Program PKBM House Of Knowledge',
                        'description' => 'Alasan kenapa harus memilih untuk bergabung dengan PKBM House Of Knowledge?'
                    ],
                    'items' => [
                        [
                            'title' => 'Pendidikan Inklusi',
                            'description' => 'Program pendidikan untuk anak berkebutuhan khusus dengan pendekatan individual',
                            'link' => '/program-inklusi',
                            'color' => 'accent-orange',
                            'icon' => null
                        ],
                        [
                            'title' => 'Pendidikan Kesetaraan',
                            'description' => 'Program Paket A, B, dan C untuk kesetaraan pendidikan SD, SMP, dan SMA',
                            'link' => '/program-sd-sma',
                            'color' => 'primary',
                            'icon' => null
                        ],
                        [
                            'title' => 'Konseling Anak Berkebutuhan Khusus',
                            'description' => 'Layanan konseling profesional untuk mendukung tumbuh kembang anak',
                            'link' => '/program-terapi',
                            'color' => 'secondary',
                            'icon' => null
                        ],
                        [
                            'title' => 'Pendidikan Anak Usia Dini',
                            'description' => 'Program PAUD dengan metode bermain sambil belajar yang menyenangkan',
                            'link' => '/program-paud-tk',
                            'color' => 'accent-yellow',
                            'icon' => null
                        ]
                    ]
                ]
            ]
        );

        // About Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'about'],
            [
                'type' => 'rich_text',
                'order' => 4,
                'content' => [
                    'badge' => 'Tentang Kami',
                    'title' => 'PKBM House Of Knowledge',
                    'description_1' => 'House Of Knowledge adalah lembaga pendidikan non-formal yang berkomitmen untuk memberikan layanan pendidikan berkualitas bagi semua kalangan, termasuk anak-anak berkebutuhan khusus.',
                    'description_2' => 'Dengan pengalaman lebih dari 14 tahun, kami telah membantu ribuan siswa mencapai potensi terbaik mereka melalui pendekatan pembelajaran yang inovatif dan personal.',
                    'image' => 'img/about-img.jpg',
                    'features' => [
                        'Kurikulum terakreditasi nasional',
                        'Tenaga pengajar berpengalaman dan bersertifikasi',
                        'Fasilitas lengkap dan ramah anak'
                    ],
                    'button_text' => 'Selengkapnya',
                    'button_link' => '/tentang-sekolah'
                ]
            ]
        );

        // News Header
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'news_header'],
            [
                'type' => 'rich_text',
                'order' => 5,
                'content' => [
                    'badge' => 'Berita Terbaru',
                    'title' => 'News',
                    'description' => 'Ikuti perkembangan terbaru dari kegiatan dan prestasi PKBM House Of Knowledge'
                ]
            ]
        );

        // Gallery Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'gallery_section'],
            [
                'type' => 'list',
                'order' => 6,
                'content' => [
                    'header' => [
                        'badge' => 'Galeri Kami',
                        'title' => 'Galeri',
                        'description' => 'Berisi Kegiatan Siswa Dan Siswi'
                    ],
                    'items' => [
                        ['image' => 'img/gallery-1.jpg'],
                        ['image' => 'img/gallery-2.jpg'],
                        ['image' => 'img/gallery-3.jpg'],
                        ['image' => 'img/gallery-4.jpg'],
                        ['image' => 'img/gallery-5.jpg'],
                        ['image' => 'img/gallery-6.jpg']
                    ]
                ]
            ]
        );

        // Contact Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'contact_section'],
            [
                'type' => 'list',
                'order' => 7,
                'content' => [
                    'header' => [
                        'badge' => 'Lokasi Kami',
                        'title' => 'Kunjungi Cabang Terdekat',
                        'description' => 'PKBM House Of Knowledge hadir di 3 lokasi strategis untuk memudahkan akses pendidikan bagi putra-putri Anda.'
                    ],
                    'items' => [
                        [
                            'title' => 'Gedung Utama PKBM House Of Knowledge',
                            'subtitle' => 'Pamulang Barat',
                            'address' => 'Komplek Ruko Reni Jaya Baru Jl.Ketapang III Blok AF 5 No 22-23 Pamulang Barat, Tangerang Selatan',
                            'color' => 'accent-orange',
                            'shade_color' => 'orange',
                            'icon' => null
                        ],
                        [
                            'title' => 'PAUD House Of Knowledge',
                            'subtitle' => 'Pamulang',
                            'address' => 'Jl. Bratasena I, Pondok Benda Pamulang, Tangerang Selatan Banten 15417',
                            'color' => 'primary',
                            'shade_color' => 'blue',
                            'icon' => null
                        ],
                        [
                            'title' => 'House Of Knowledge Cimanggis',
                            'subtitle' => 'Ciputat',
                            'address' => 'Jl. Otista Raya Blok A25 Ruko Prima Ciputat, Tangerang Selatan Banten 15417',
                            'color' => 'secondary',
                            'shade_color' => 'green',
                            'icon' => null
                        ]
                    ]
                ]
            ]
        );

        // CTA Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $home->id, 'section_key' => 'cta_section'],
            [
                'type' => 'rich_text',
                'order' => 8,
                'content' => [
                    'title' => 'Siap Bergabung Bersama Kami?',
                    'description' => 'Daftarkan putra-putri Anda sekarang dan berikan mereka pendidikan terbaik untuk masa depan yang cerah.',
                    'button_text_1' => 'Daftar Sekarang',
                    'button_link_1' => '/pendaftaran',
                    'button_text_2' => 'Hubungi Kami',
                    'button_link_2' => '/kontak'
                ]
            ]
        );

        // ==========================================
        // 2. TENTANG SEKOLAH
        // ==========================================
        $about = LandingPage::updateOrCreate(
            ['slug' => 'tentang-sekolah'],
            ['title' => 'Tentang Sekolah', 'order' => 2]
        );

        // Hero
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $about->id, 'section_key' => 'hero'],
            [
                'type' => 'text',
                'order' => 1,
                'content' => [
                    'title' => 'Tentang Sekolah',
                    'background_image' => 'img/hero-bg.jpg'
                ]
            ]
        );

        // Intro
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $about->id, 'section_key' => 'intro'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Tentang Kami',
                    'title' => 'PKBM House Of Knowledge',
                    'content' => 'PKBM House Of Knowledge adalah lembaga pendidikan yang berdedikasi untuk memberikan pendidikan berkualitas bagi semua kalangan. Kami percaya bahwa setiap anak memiliki potensi unik yang perlu dikembangkan dengan pendekatan yang tepat.
                    
Dengan pengalaman lebih dari 14 tahun dalam bidang pendidikan, kami telah membantu ribuan siswa mencapai potensi terbaik mereka melalui program pendidikan yang inovatif dan inklusif.',
                    'image' => 'img/about-school.jpg',
                    'stats_years' => '14+'
                ]
            ]
        );

        // History
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $about->id, 'section_key' => 'history'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Perjalanan Kami',
                        'title' => 'Sejarah PKBM House Of Knowledge',
                        'description' => 'PKBM House Of Knowledge didirikan dengan semangat untuk memberikan pendidikan berkualitas yang dapat diakses oleh semua kalangan. Berikut adalah perjalanan kami dari awal hingga saat ini.'
                    ],
                    'items' => [
                        [
                            'year' => '2014',
                            'title' => 'Awal Pendirian',
                            'description' => 'PKBM House Of Knowledge didirikan dengan 5 orang guru dan 20 siswa pertama. Dimulai dari sebuah rumah sederhana dengan cita-cita besar.',
                            'image' => 'img/sejarah-1.jpg',
                            'color' => '#165fac'
                        ],
                        [
                            'year' => '2016',
                            'title' => 'Pengembangan Program',
                            'description' => 'Membuka program pendidikan kesetaraan Paket A, B, dan C. Jumlah siswa meningkat menjadi 100 orang.',
                            'image' => 'img/sejarah-2.jpg',
                            'color' => '#287f3b'
                        ],
                        [
                            'year' => '2018',
                            'title' => 'Program Inklusi',
                            'description' => 'Meluncurkan program pendidikan inklusi untuk anak berkebutuhan khusus dengan fasilitas terapi lengkap.',
                            'image' => 'img/sejarah-3.jpg',
                            'color' => '#d45930'
                        ],
                        [
                            'year' => '2023',
                            'title' => 'Gedung Baru',
                            'description' => 'Pindah ke gedung baru yang lebih luas di Pamulang dengan fasilitas modern dan lengkap.',
                            'image' => 'img/sejarah-4.jpg',
                            'color' => '#fac030'
                        ],
                        [
                            'year' => '2025',
                            'title' => 'Saat Ini',
                            'description' => 'Melayani lebih dari 200 siswa dengan 50+ tenaga pengajar profesional. Terus berkembang dan berinovasi.',
                            'image' => 'img/sejarah-5.jpg',
                            'color' => '#165fac'
                        ]
                    ]
                ]
            ]
        );

        // Why Choose Us
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $about->id, 'section_key' => 'why_choose_us'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'badge' => 'Keunggulan Kami',
                        'title' => 'Mengapa Memilih Kami?'
                    ],
                    'items' => [
                        [
                            'title' => 'Pendidikan Berkualitas',
                            'description' => 'Kurikulum yang dirancang untuk memaksimalkan potensi setiap siswa dengan metode pembelajaran modern.',
                            'icon_color' => '#d45930',
                            'icon' => null
                        ],
                        [
                            'title' => 'Tenaga Pengajar Ahli',
                            'description' => 'Guru-guru berpengalaman dan terlatih dalam menangani berbagai kebutuhan belajar siswa.',
                            'icon_color' => '#165fac',
                            'icon' => null
                        ],
                        [
                            'title' => 'Pendidikan Inklusif',
                            'description' => 'Menerima dan mendukung anak berkebutuhan khusus dengan program yang disesuaikan.',
                            'icon_color' => '#287f3b',
                            'icon' => null
                        ]
                    ]
                ]
            ]
        );

        // CTA
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $about->id, 'section_key' => 'cta'],
            [
                'type' => 'rich_text',
                'order' => 5,
                'content' => [
                    'title' => 'Tertarik Bergabung?',
                    'description' => 'Daftarkan putra-putri Anda sekarang dan berikan pendidikan terbaik untuk masa depan yang cerah',
                    'button_text' => 'Daftar Sekarang',
                    'button_link' => '/pendaftaran'
                ]
            ]
        );

        // ==========================================
        // 3. PROFIL GURU (Example of List)
        // ==========================================
        $guru = LandingPage::updateOrCreate(
            ['slug' => 'profil-guru'],
            ['title' => 'Profil Guru', 'order' => 5]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $guru->id, 'section_key' => 'hero'],
            [
                'type' => 'text',
                'order' => 1,
                'content' => [
                    'title' => 'Profil Guru & Tenaga Ahli',
                    'background_image' => 'img/hero-bg.png'
                ]
            ]
        );

        // We will seed defaults here, but admin can add more.
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $guru->id, 'section_key' => 'staff_list'],
            [
                'type' => 'list',
                'order' => 2,
                'content' => [
                    [
                        'name' => 'Charolline R. S.Mat',
                        'position' => 'Guru Sosiologi',
                        'category' => 'guru',
                        'image' => 'img/guru-1.png'
                    ],
                    [
                        'name' => 'Dyah Yossie, S.Pd',
                        'position' => 'Guru Bahasa Indonesia',
                        'category' => 'guru',
                        'image' => 'img/guru-2.png'
                    ],
                    [
                        'name' => 'Muthi',
                        'position' => 'Terapis Okupasi',
                        'category' => 'terapis',
                        'image' => 'img/guru-3.png'
                    ],
                    [
                        'name' => 'Fransisda T. F., M.Psi',
                        'position' => 'Psikolog Anak',
                        'category' => 'psikolog',
                        'image' => 'img/guru-4.png'
                    ],
                    [
                        'name' => 'Berliana Agustin',
                        'position' => 'Guru IPA',
                        'category' => 'guru',
                        'image' => 'img/guru-5.png'
                    ],
                    [
                        'name' => 'Delia Parsaulani, S.K.M',
                        'position' => 'Guru IPS',
                        'category' => 'guru',
                        'image' => 'img/guru-6.png'
                    ],
                    [
                        'name' => 'Debora Zephania, S.S',
                        'position' => 'Terapis Wicara',
                        'category' => 'terapis',
                        'image' => 'img/guru-7.png'
                    ],
                    [
                        'name' => 'Meini',
                        'position' => 'Guru PAUD',
                        'category' => 'guru',
                        'image' => 'img/guru-8.png'
                    ],
                ]
            ]
        );

        // ==========================================
        // 4. VISI MISI PAGE
        // ==========================================
        $visiMisi = LandingPage::updateOrCreate(
            ['slug' => 'visi-misi'],
            ['title' => 'Visi & Misi', 'order' => 3]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $visiMisi->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'title' => 'Visi & Misi',
                    'background_image' => 'img/hero-bg.jpg',
                ]
            ]
        );

        // Visi Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $visiMisi->id, 'section_key' => 'visi'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Visi Kami',
                    'title' => 'Visi PKBM House Of Knowledge',
                    'content' => 'Membentuk manusia yang memiliki kualitas iman dan taqwa, mandiri, disiplin, bertanggung jawab dan berpandangan positif dalam menghadapi hidup',
                    'image' => 'img/visi-misi.jpg',
                ]
            ]
        );

        // Misi Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $visiMisi->id, 'section_key' => 'misi'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Misi Kami',
                        'title' => 'Misi PKBM House Of Knowledge',
                    ],
                    'items' => [
                        [
                            'title' => 'Meningkatkan Iman dan Taqwa Peserta didik',
                            'description' => 'Dengan pendidikan yang mengutamakan pendidikan keagamanaan.',
                            'color' => 'primary',
                        ],
                        [
                            'title' => 'Menanamkan semangat cinta kasih terhadap orang lain',
                            'description' => 'Kepedulian dan cinta kasih kepada sesama teman, guru maupun orang tua murid.',
                            'color' => 'secondary',
                        ],
                        [
                            'title' => 'Memperbaiki prilaku dan moral peserta didik',
                            'description' => 'Menanamkan perilaku dan moral yang terpuji menjadi keutamaan.',
                            'color' => 'accent-orange',
                        ],
                        [
                            'title' => 'Mengembangkan rasa percaya diri pada diri peserta didik',
                            'description' => 'Dengan berbagai kegiatan mengembangkan rasa percaya diri siswa.',
                            'color' => 'accent-yellow',
                        ],
                        [
                            'title' => 'Memperluas wawasan peserta didik terhadap perkembangan ilmu pengetahuan',
                            'description' => 'Pengalaman belajar dengan guru-guru berkompeten.',
                            'color' => 'primary',
                        ],
                        [
                            'title' => 'Memperluas kesempatan belajar bagi peserta didik yang tidak bisa masuk ke sekolah formal',
                            'description' => 'Memberi kesempatan belajar lebih luas dan lebih baik.',
                            'color' => 'secondary',
                        ],
                    ]
                ]
            ]
        );

        // Values Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $visiMisi->id, 'section_key' => 'values'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'title' => 'Nilai-Nilai Kami',
                        'description' => 'Prinsip yang menjadi landasan setiap aktivitas kami',
                    ],
                    'items' => [
                        ['title' => 'Integritas', 'icon' => 'fas fa-bullseye', 'icon_color' => 'orange'], // 'icon' can be class string or file path
                        ['title' => 'Inovasi', 'icon' => 'fas fa-lightbulb', 'icon_color' => 'yellow'],
                        ['title' => 'Kolaborasi', 'icon' => 'fas fa-handshake', 'icon_color' => 'green'],
                        ['title' => 'Pengembangan Bakat', 'icon' => 'fas fa-heart', 'icon_color' => 'red'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 5. STRUKTUR ORGANISASI PAGE
        // ==========================================
        $struktur = LandingPage::updateOrCreate(
            ['slug' => 'struktur-organisasi'],
            ['title' => 'Struktur Organisasi', 'order' => 4]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $struktur->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'title' => 'Struktur Organisasi',
                    'background_image' => 'img/hero-bg.jpg',
                ]
            ]
        );

        // Header Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $struktur->id, 'section_key' => 'header'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Organisasi',
                    'title' => 'Struktur Organisasi PKBM',
                    'subtitle' => 'House Of Knowledge',
                ]
            ]
        );

        // Leaders Section (Ketua Yayasan & Kepala PKBM)
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $struktur->id, 'section_key' => 'leaders'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    ['name' => 'Ivan Janitra, S. I. Kom', 'position' => 'Ketua Yayasan', 'image' => 'img/ketua-yayasan.png', 'color' => 'primary'],
                    ['name' => 'Fransisda Tiodora F., S. Psi', 'position' => 'Kepala PKBM', 'image' => 'img/kepala-sekolah.png', 'color' => 'secondary'],
                ]
            ]
        );

        // Staff Section (Sekretaris, Bendahara, Administrator)
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $struktur->id, 'section_key' => 'staff'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    ['name' => 'Charoline Revlecya, S. Mat', 'position' => 'Sekretaris', 'image' => 'img/sekretaris.png', 'color' => 'accent-orange'],
                    ['name' => 'Linawati Rozali', 'position' => 'Bendahara', 'image' => 'img/bendahara.png', 'color' => 'primary'],
                    ['name' => 'Delia Parsaulani, S. K. M', 'position' => 'Administrator', 'image' => 'img/administrator.png', 'color' => 'secondary'],
                ]
            ]
        );

        // Coordinators Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $struktur->id, 'section_key' => 'coordinators'],
            [
                'type' => 'list',
                'order' => 5,
                'content' => [
                    'header' => [
                        'title' => 'Koordinator Program',
                    ],
                    'items' => [
                        ['name' => 'Stefany Angelina, S.Pd', 'department' => 'Bidang Kesetaraan', 'image' => 'img/koordinator-1.png'],
                        ['name' => 'Robert Yinaidi Lay', 'department' => 'Bidang Sarpas', 'image' => 'img/koordinator-2.jpg'],
                        ['name' => 'Aviana Margaretta T', 'department' => 'Bidang Kesiswaan', 'image' => 'img/koordinator-3.png'],
                        ['name' => 'Debora Zephania E. M., S.S', 'department' => 'Bidang Lifeskill', 'image' => 'img/koordinator-4.png'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 6. PROGRAM PAUD-TK PAGE
        // ==========================================
        $paudTk = LandingPage::updateOrCreate(
            ['slug' => 'program-paud-tk'],
            ['title' => 'Program PAUD-TK', 'order' => 6]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $paudTk->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'title' => 'Pendidikan Anak Usia Dini',
                    'subtitle' => 'Program PAUD, KB, dan TK (Usia 2–6 Tahun)',
                    'background_image' => 'img/hero-bg.jpg',
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $paudTk->id, 'section_key' => 'about'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Program PAUD – TK',
                    'title' => 'Belajar Menyenangkan untuk Usia 2–6 Tahun',
                    'description_1' => 'Kami percaya bahwa masa usia dini adalah waktu terbaik bagi anak untuk mulai mengenal dunia dengan cara yang paling natural: bermain. Di program PAUD–TK kami, setiap hari dirancang agar anak merasa aman, senang, dan bebas bereksplorasi sesuai ritme mereka.',
                    'description_2' => 'Guru-guru kami mendampingi anak dengan penuh perhatian dan kehangatan, membantu mereka berkembang dalam aspek sosial, motorik, bahasa, serta membangun rasa percaya diri sejak dini. Belajar tanpa tekanan — hanya keceriaan dan pengalaman baru setiap hari.',
                    'image_1' => 'img/tk-main.jpg',
                    'image_2' => 'img/tk-aktif.jpg',
                    'image_3' => 'img/tk-belajar.jpg',
                    'button_text' => 'Konsultasi Program',
                    'button_link' => '/kontak',
                ]
            ]
        );

        // Programs Section (PAUD, KB, TK cards)
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $paudTk->id, 'section_key' => 'programs'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Program Lengkap',
                        'title' => 'Tiga Jenjang Pendidikan Berkelanjutan',
                        'description' => 'Kami menyediakan program pendidikan anak usia dini yang komprehensif dan berkelanjutan, dari PAUD hingga TK, dengan pendekatan bermain sambil belajar yang menyenangkan.',
                    ],
                    'items' => [
                        [
                            'name' => 'PAUD',
                            'age_range' => '2-4 Tahun',
                            'description' => 'Pendidikan Anak Usia Dini dengan fokus bermain sambil belajar',
                            'color' => 'orange',
                            'features' => 'Rasio guru 1:5|Pengembangan motorik|Stimulasi sosial-emosional|Senin-Jumat, 07:30-11:00',
                            'icon' => null,
                        ],
                        [
                            'name' => 'KB',
                            'age_range' => '4-5 Tahun',
                            'description' => 'Kelompok Bermain dengan pengenalan literasi dasar',
                            'color' => 'blue',
                            'features' => 'Pengenalan huruf & angka|Pengembangan kreativitas|Kegiatan seni & musik|Senin-Jumat, 07:30-11:00',
                            'icon' => null,
                        ],
                        [
                            'name' => 'TK',
                            'age_range' => '5-6 Tahun',
                            'description' => 'Taman Kanak-kanak persiapan sekolah dasar',
                            'color' => 'green',
                            'features' => 'Membaca & menulis|Matematika dasar|Bahasa Inggris dasar|Senin-Jumat, 07:30-11:00',
                            'icon' => null,
                        ],
                    ]
                ]
            ]
        );

        // Kurikulum Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $paudTk->id, 'section_key' => 'kurikulum'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'badge' => 'Kurikulum',
                        'title' => 'Area Pengembangan',
                        'description' => 'Kurikulum komprehensif yang disesuaikan dengan tahap perkembangan anak',
                    ],
                    'items' => [
                        ['title' => 'Nilai Agama & Moral', 'description' => 'Pengenalan nilai-nilai agama dan moral sejak dini melalui pembiasaan sehari-hari.', 'color' => 'orange', 'icon' => null],
                        ['title' => 'Kognitif', 'description' => 'Stimulasi kemampuan berpikir, mengenal angka, huruf, bentuk, dan warna.', 'color' => 'blue', 'icon' => null],
                        ['title' => 'Bahasa', 'description' => 'Pengembangan kemampuan berbahasa reseptif dan ekspresif melalui cerita dan lagu.', 'color' => 'green', 'icon' => null],
                        ['title' => 'Sosial Emosional', 'description' => 'Pengembangan kemampuan bersosialisasi dan mengelola emosi dengan baik.', 'color' => 'yellow', 'icon' => null],
                        ['title' => 'Motorik Halus', 'description' => 'Latihan koordinasi tangan-mata melalui kegiatan melipat, menggunting, dan mewarnai.', 'color' => 'blue', 'icon' => null],
                        ['title' => 'Motorik Kasar', 'description' => 'Aktivitas fisik untuk mengembangkan koordinasi tubuh dan keseimbangan.', 'color' => 'orange', 'icon' => null],
                    ]
                ]
            ]
        );

        // Jadwal Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $paudTk->id, 'section_key' => 'jadwal'],
            [
                'type' => 'list',
                'order' => 5,
                'content' => [
                    'header' => [
                        'badge' => 'Jadwal',
                        'title' => 'Jadwal Kegiatan Harian',
                        'description' => 'Contoh jadwal untuk program PAUD (KB & TK memiliki durasi yang disesuaikan)',
                    ],
                    'items' => [
                        ['time' => '07:30 - 08:00', 'activity' => 'Penyambutan & Free Play'],
                        ['time' => '08:00 - 08:30', 'activity' => 'Circle Time & Doa'],
                        ['time' => '08:30 - 09:30', 'activity' => 'Kegiatan Inti (Tema)'],
                        ['time' => '09:30 - 10:00', 'activity' => 'Snack Time'],
                        ['time' => '10:00 - 10:30', 'activity' => 'Outdoor Play'],
                        ['time' => '10:30 - 11:00', 'activity' => 'Recalling & Penutup'],
                    ]
                ]
            ]
        );

        // Fasilitas Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $paudTk->id, 'section_key' => 'fasilitas'],
            [
                'type' => 'list',
                'order' => 6,
                'content' => [
                    'header' => [
                        'badge' => 'Fasilitas',
                        'title' => 'Fasilitas Lengkap',
                    ],
                    'items' => [
                        ['title' => 'Ruang Kelas Nyaman', 'description' => 'AC, pencahayaan baik, furnitur ramah anak', 'color' => 'blue', 'icon' => null],
                        ['title' => 'Area Bermain Outdoor', 'description' => 'Aman, bersih, dan terawat', 'color' => 'green', 'icon' => null],
                        ['title' => 'Perpustakaan Mini', 'description' => 'Koleksi buku anak lengkap', 'color' => 'orange', 'icon' => null],
                        ['title' => 'Alat Permainan Edukatif', 'description' => 'APE berkualitas dan variatif', 'color' => 'yellow', 'icon' => null],
                    ]
                ]
            ]
        );

        // ==========================================
        // 7. PROGRAM SD-SMA PAGE
        // ==========================================
        $sdSma = LandingPage::updateOrCreate(
            ['slug' => 'program-sd-sma'],
            ['title' => 'Program SD-SMP-SMA', 'order' => 7]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'title' => 'Program Pendidikan Kesetaraan',
                    'subtitle' => 'Paket A • Paket B • Paket C',
                    'background_image' => 'img/hero-bg.jpg',
                ]
            ]
        );

        // Paket A (SD) Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'paket_a'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Pendidikan Kesetaraan',
                    'title' => 'Paket A (Setara SD)',
                    'label' => 'Paket A',
                    'label_subtitle' => 'Setara SD',
                    'image' => 'img/sd-main.jpg',
                    'description_1' => 'Program Paket A adalah program pendidikan kesetaraan yang setara dengan Sekolah Dasar (SD). Program ini diperuntukkan bagi anak-anak yang tidak dapat mengikuti pendidikan formal karena berbagai alasan.',
                    'description_2' => 'Dengan kurikulum yang disesuaikan dan metode pembelajaran yang fleksibel, siswa dapat belajar sesuai dengan kecepatan masing-masing sambil tetap mencapai kompetensi yang diharapkan.',
                    'features' => 'Ijazah Resmi|Jadwal Fleksibel|Kelas Kecil|Bimbingan Intensif',
                    'cta_title' => 'Mulai Pendidikan Anda Sekarang',
                    'cta_description' => 'Dapatkan ijazah resmi setara SD dengan program Paket A kami.',
                ]
            ]
        );
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'mata_pelajaran_a'],
            [
                'type' => 'list',
                'order' => 5,
                'content' => [
                    'header' => [
                        'badge' => 'Kurikulum',
                        'title' => 'Mata Pelajaran',
                    ],
                    'items' => [
                        ['title' => 'Bahasa Indonesia', 'icon' => 'fas fa-book', 'icon_color' => 'orange', 'card_color' => '#fef3c7'], 
                        ['title' => 'Matematika', 'icon' => 'fas fa-calculator', 'icon_color' => 'blue', 'card_color' => '#dbeafe'],
                        ['title' => 'IPA', 'icon' => 'fas fa-microscope', 'icon_color' => 'green', 'card_color' => '#d1fae5'],
                        ['title' => 'IPS', 'icon' => 'fas fa-globe-asia', 'icon_color' => 'yellow', 'card_color' => '#fef3c7'],
                        ['title' => 'Agama', 'icon' => 'fas fa-mosque', 'icon_color' => 'red', 'card_color' => '#ffe4e6'],
                        ['title' => 'B. Inggris', 'icon' => 'fas fa-language', 'icon_color' => 'blue', 'card_color' => '#e0f2fe'],
                    ]
                ]
            ]
        );

        // Paket B (SMP) Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'paket_b'],
            [
                'type' => 'rich_text',
                'order' => 3,
                'content' => [
                    'badge' => 'Pendidikan Kesetaraan',
                    'title' => 'Paket B (Setara SMP)',
                    'label' => 'Paket B',
                    'label_subtitle' => 'Setara SMP',
                    'image' => 'img/smp-main.jpg',
                    'description_1' => 'Program Paket B adalah program pendidikan kesetaraan yang setara dengan Sekolah Menengah Pertama (SMP). Program ini memberikan kesempatan bagi mereka yang ingin melanjutkan pendidikan ke jenjang yang lebih tinggi.',
                    'description_2' => 'Lulusan Paket B dapat melanjutkan ke jenjang SMA/SMK atau Paket C, serta dapat digunakan untuk melamar pekerjaan yang mensyaratkan ijazah SMP.',
                    'tag_1' => 'Ijazah Resmi Kemendikbud',
                    'tag_2' => 'Setara SMP Formal',
                    'cta_title' => 'Raih Ijazah SMP Anda',
                    'cta_description' => 'Daftarkan diri Anda sekarang dan mulai perjalanan pendidikan baru.',
                ]
            ]
        );
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'mata_pelajaran_b'],
            [
                'type' => 'list',
                'order' => 6,
                'content' => [
                    'header' => [
                        'title' => 'Mata Pelajaran',
                    ],
                    'items' => [
                        ['title' => 'Bahasa Indonesia', 'icon' => 'fas fa-book', 'icon_color' => 'blue', 'card_color' => '#165fac'], 
                        ['title' => 'Matematika', 'icon' => 'fas fa-calculator', 'icon_color' => 'green', 'card_color' => '#287f3b'],
                        ['title' => 'IPA', 'icon' => 'fas fa-microscope', 'icon_color' => 'orange', 'card_color' => '#d45930'],
                        ['title' => 'IPS', 'icon' => 'fas fa-globe-asia', 'icon_color' => 'yellow', 'card_color' => '#fac030'],
                        ['title' => 'Bahasa Inggris', 'icon' => 'fas fa-language', 'icon_color' => 'green', 'card_color' => '#287f3b'],
                        ['title' => 'Agama', 'icon' => 'fas fa-mosque', 'icon_color' => 'blue', 'card_color' => '#165fac'],
                        ['title' => 'PKn', 'icon' => 'fas fa-flag', 'icon_color' => 'orange', 'card_color' => '#d45930'],
                        ['title' => 'Bahasa Mandarin', 'icon' => 'fas fa-language', 'icon_color' => 'yellow', 'card_color' => '#fac030'],
                    ]
                ]
            ]
        );
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'keunggulan_b'],
            [
                'type' => 'list',
                'order' => 7,
                'content' => [
                    'header' => [
                        'title' => 'Keunggulan Program',
                    ],
                    'items' => [
                        ['title' => 'Jadwal Fleksibel', 'description' => 'Waktu belajar yang dapat disesuaikan dengan aktivitas lain', 'icon' => 'fas fa-clock', 'icon_color' => 'blue'], // #165fac
                        ['title' => 'Kelas Kecil', 'description' => 'Maksimal 15 siswa per kelas untuk pembelajaran optimal', 'icon' => 'fas fa-users', 'icon_color' => 'green'], // #287f3b
                        ['title' => 'Ijazah Resmi', 'description' => 'Ijazah diakui setara dengan SMP formal oleh negara', 'icon' => 'fas fa-certificate', 'icon_color' => 'orange'], // #d45930
                    ]
                ]
            ]
        );

        // Paket C (SMA) Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'paket_c'],
            [
                'type' => 'rich_text',
                'order' => 4,
                'content' => [
                    'badge' => 'Pendidikan Kesetaraan',
                    'title' => 'Paket C (Setara SMA)',
                    'label' => 'Paket C',
                    'label_subtitle' => 'Setara SMA',
                    'image' => 'img/sma-main.jpg',
                    'description_1' => 'Program Paket C adalah program pendidikan kesetaraan tertinggi yang setara dengan Sekolah Menengah Atas (SMA). Ijazah Paket C dapat digunakan untuk melanjutkan ke perguruan tinggi atau melamar pekerjaan.',
                    'description_2' => 'Program ini cocok untuk mereka yang ingin menyelesaikan pendidikan setingkat SMA dengan waktu yang lebih fleksibel tanpa mengorbankan kualitas pendidikan.',
                    'tag_1' => 'Bisa Kuliah',
                    'tag_2' => 'Bisa Kerja',
                    'tag_3' => 'Ijazah Resmi',
                    'cta_title' => 'Wujudkan Impian Anda',
                    'cta_description' => 'Dapatkan ijazah SMA dan buka pintu menuju masa depan yang lebih cerah.',
                ]
            ]
        );
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'jurusan_c'],
            [
                'type' => 'list',
                'order' => 8,
                'content' => [
                    'header' => [
                        'title' => 'Pilihan Jurusan',
                    ],
                    'items' => [
                        [
                            'title' => 'Jurusan IPA', 
                            'description' => 'Fokus pada mata pelajaran sains seperti Matematika, Fisika, Kimia, dan Biologi.', 
                            'icon' => 'fas fa-atom', 
                            'features' => 'Matematika|Fisika|Kimia|Biologi',
                            'card_gradient_start' => '#1e5f8a',
                            'card_gradient_end' => '#2e8b57'
                        ],
                        [
                            'title' => 'Jurusan IPS', 
                            'description' => 'Fokus pada ilmu sosial seperti Ekonomi, Geografi, Sosiologi, dan Sejarah.', 
                            'icon' => 'fas fa-globe', 
                            'features' => 'Ekonomi|Geografi|Sosiologi|Sejarah', 
                            'card_gradient_start' => '#ea8c38',
                            'card_gradient_end' => '#facc15'
                        ],
                    ]
                ]
            ]
        );
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $sdSma->id, 'section_key' => 'prospek_c'],
            [
                'type' => 'list',
                'order' => 9,
                'content' => [
                    'header' => [
                        'title' => 'Prospek Setelah Lulus',
                    ],
                    'items' => [
                        ['title' => 'Kuliah', 'subtitle' => 'Lanjut ke Perguruan Tinggi Negeri/Swasta', 'icon' => 'fas fa-graduation-cap', 'icon_color' => 'slate'],
                        ['title' => 'Kerja', 'subtitle' => 'Melamar pekerjaan dengan ijazah SMA', 'icon' => 'fas fa-briefcase', 'icon_color' => 'amber'],
                        ['title' => 'Wirausaha', 'subtitle' => 'Memulai usaha sendiri', 'icon' => 'fas fa-store', 'icon_color' => 'blue'],
                        ['title' => 'CPNS', 'subtitle' => 'Daftar seleksi CPNS', 'icon' => 'fas fa-user-tie', 'icon_color' => 'orange'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 8. PROGRAM INKLUSI PAGE
        // ==========================================
        $inklusi = LandingPage::updateOrCreate(
            ['slug' => 'program-inklusi'],
            ['title' => 'Program Inklusi', 'order' => 8]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $inklusi->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'title' => 'Program Inklusi',
                    'subtitle' => 'Pendidikan untuk Anak Berkebutuhan Khusus',
                    'background_image' => 'img/hero-bg.jpg',
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $inklusi->id, 'section_key' => 'about'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Program Inklusi',
                    'title' => 'Pendidikan Inklusif',
                    'description_1' => 'Program Inklusi kami dirancang khusus untuk anak-anak berkebutuhan khusus (ABK) agar dapat belajar bersama dengan anak-anak lainnya dalam lingkungan yang inklusif dan mendukung.',
                    'description_2' => 'Dengan pendekatan individual dan dukungan dari tenaga ahli, setiap anak mendapatkan kesempatan yang sama untuk berkembang sesuai dengan potensinya masing-masing.',
                    'image' => 'img/inklusi-main.jpg',
                    'button_text' => 'Konsultasi Gratis',
                    'button_link' => '/kontak',
                ]
            ]
        );

        // Services Section (Types of needs served)
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $inklusi->id, 'section_key' => 'services'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Layanan Kami',
                        'title' => 'Jenis Kebutuhan yang Kami Layani',
                    ],
                    'items' => [
                        [
                            'title' => 'Autisme (ASD)', 
                            'description' => 'Program khusus untuk anak dengan gangguan spektrum autisme dengan pendekatan terstruktur.', 
                            'color' => 'blue',
                            'icon' => 'fas fa-puzzle-piece',
                        ],
                        [
                            'title' => 'ADHD', 
                            'description' => 'Pendekatan pembelajaran khusus untuk anak dengan gangguan pemusatan perhatian dan hiperaktivitas.', 
                            'color' => 'green',
                            'icon' => 'fas fa-bolt',
                        ],
                        [
                            'title' => 'Disleksia', 
                            'description' => 'Metode pembelajaran multisensori untuk anak dengan kesulitan membaca dan menulis.', 
                            'color' => 'orange',
                            'icon' => 'fas fa-book-open',
                        ],
                        [
                            'title' => 'Down Syndrome', 
                            'description' => 'Program stimulasi dan pembelajaran yang disesuaikan untuk anak down syndrome.', 
                            'color' => 'yellow',
                            'icon' => 'fas fa-child',
                        ],
                        [
                            'title' => 'Speech Delay', 
                            'description' => 'Terapi wicara dan program stimulasi bahasa untuk anak dengan keterlambatan bicara.', 
                            'color' => 'blue',
                            'icon' => 'fas fa-comments',
                        ],
                        [
                            'title' => 'Slow Learner', 
                            'description' => 'Pendekatan pembelajaran bertahap untuk anak dengan kecepatan belajar yang berbeda.', 
                            'color' => 'green',
                            'icon' => 'fas fa-hourglass-half',
                        ],
                    ]
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $inklusi->id, 'section_key' => 'team'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'title' => 'Tim Profesional Kami',
                    ],
                    'items' => [
                        ['title' => 'Psikolog Anak', 'description' => 'Asesmen dan konseling psikologis', 'icon' => 'fas fa-brain', 'color' => 'blue', 'link' => '/profil-guru'],
                        ['title' => 'Terapis Okupasi', 'description' => 'Terapi motorik dan sensori', 'icon' => 'fas fa-hands-helping', 'color' => 'green', 'link' => '/profil-guru'],
                        ['title' => 'Terapis Wicara', 'description' => 'Terapi bicara dan bahasa', 'icon' => 'fas fa-microphone', 'color' => 'orange', 'link' => '/profil-guru'],
                        ['title' => 'Guru Pendamping', 'description' => 'Shadow teacher terlatih', 'icon' => 'fas fa-graduation-cap', 'color' => 'yellow', 'link' => '/profil-guru'],
                    ]
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $inklusi->id, 'section_key' => 'cta'],
            [
                'type' => 'rich_text',
                'order' => 5,
                'content' => [
                    'title' => 'Setiap Anak Berhak Mendapat Pendidikan',
                    'description' => 'Konsultasikan kebutuhan anak Anda dengan tim ahli kami secara gratis.',
                    'button_text_1' => 'Daftar Sekarang',
                    'button_link_1' => '/pendaftaran',
                    'button_text_2' => 'Konsultasi Gratis',
                    'button_link_2' => '/kontak',
                    'background_gradient_start' => '#d45930',
                    'background_gradient_end' => '#fac030'
                ]
            ]
        );

        // ==========================================
        // 9. PROGRAM TERAPI PAGE
        // ==========================================
        $terapi = LandingPage::updateOrCreate(
            ['slug' => 'program-terapi'],
            ['title' => 'Program Terapi', 'order' => 9]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $terapi->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'title' => 'Program Terapi',
                    'subtitle' => 'Layanan Terapi Profesional untuk Tumbuh Kembang Anak',
                    'background_image' => 'img/hero-bg.jpg',
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $terapi->id, 'section_key' => 'about'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'content' => [
                    'badge' => 'Program Terapi',
                    'title' => 'Layanan Terapi Profesional',
                    'description_1' => 'Program Terapi kami menyediakan berbagai layanan terapi untuk mendukung tumbuh kembang anak. Setiap sesi terapi dilakukan oleh tenaga profesional yang berpengalaman dan tersertifikasi.',
                    'description_2' => 'Kami menyediakan ruang terapi yang nyaman dan dilengkapi dengan peralatan modern untuk memaksimalkan hasil terapi.',
                    'image' => 'img/terapi-img.jpg',
                    'stat_1_value' => '5+',
                    'stat_1_label' => 'Jenis Terapi',
                    'stat_2_value' => '10+',
                    'stat_2_label' => 'Terapis Ahli',
                    'stat_3_value' => '500+',
                    'stat_3_label' => 'Klien Terbantu',
                ]
            ]
        );

        // Therapy Types Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $terapi->id, 'section_key' => 'therapy_types'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'title' => 'Jenis Layanan Terapi',
                        'description' => 'Berbagai layanan terapi profesional untuk kebutuhan anak',
                    ],
                    'items' => [
                        ['title' => 'Terapi Wicara', 'description' => 'Membantu anak mengembangkan kemampuan berbicara, bahasa, dan komunikasi secara efektif.', 'color' => 'blue', 'features' => 'Artikulasi dan pengucapan|Pengembangan bahasa|Komunikasi sosial'],
                        ['title' => 'Terapi Okupasi', 'description' => 'Mengembangkan keterampilan motorik halus dan kemandirian dalam aktivitas sehari-hari.', 'color' => 'green', 'features' => 'Motorik halus|Kemandirian (ADL)|Koordinasi tangan-mata'],
                        ['title' => 'Sensori Integrasi', 'description' => 'Membantu anak memproses dan merespons informasi sensorik dengan lebih baik.', 'color' => 'orange', 'features' => 'Pengolahan sensorik|Regulasi emosi|Keseimbangan tubuh'],
                        ['title' => 'Terapi Perilaku (ABA)', 'description' => 'Applied Behavior Analysis untuk mengembangkan perilaku positif dan mengurangi perilaku yang tidak diinginkan.', 'color' => 'yellow', 'features' => 'Modifikasi perilaku|Keterampilan sosial|Penguatan positif'],
                        ['title' => 'Fisioterapi', 'description' => 'Meningkatkan kemampuan motorik kasar, keseimbangan, dan kekuatan otot anak.', 'color' => 'blue', 'features' => 'Motorik kasar|Kekuatan & fleksibilitas|Postur tubuh'],
                        ['title' => 'Konseling Psikologi', 'description' => 'Layanan konseling untuk membantu anak mengatasi masalah emosional dan psikologis.', 'color' => 'green', 'features' => 'Asesmen psikologi|Konseling keluarga|Manajemen emosi'],
                    ]
                ]
            ]
        );

        // Alur Layanan Terapi Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $terapi->id, 'section_key' => 'alur_terapi'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'title' => 'Alur Layanan Terapi',
                        'description' => 'Proses terapi yang terstruktur untuk hasil optimal',
                    ],
                    'items' => [
                        ['title' => 'Konsultasi Awal', 'description' => 'Diskusi dengan orang tua mengenai kondisi dan kebutuhan anak'],
                        ['title' => 'Asesmen', 'description' => 'Evaluasi menyeluruh untuk menentukan jenis terapi yang tepat'],
                        ['title' => 'Sesi Terapi', 'description' => 'Pelaksanaan terapi sesuai program yang telah dirancang'],
                        ['title' => 'Evaluasi & Laporan', 'description' => 'Monitoring berkala dan laporan perkembangan untuk orang tua'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 10. FASILITAS PAGE
        // ==========================================
        $fasilitas = LandingPage::updateOrCreate(
            ['slug' => 'fasilitas'],
            ['title' => 'Fasilitas', 'order' => 10]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $fasilitas->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'badge' => 'Fasilitas Lengkap & Modern',
                    'title' => 'Fasilitas Terbaik',
                    'title_highlight' => 'Untuk Pembelajaran Optimal',
                    'subtitle' => 'PKBM House Of Knowledge menyediakan fasilitas lengkap dan modern untuk mendukung proses belajar mengajar yang efektif dan menyenangkan',
                    'background_image' => 'img/bg-fasilitas.jpg',
                ]
            ]
        );

        // Stats Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $fasilitas->id, 'section_key' => 'stats'],
            [
                'type' => 'list',
                'order' => 1, // Order same as hero? No, let's keep it separate data-wise but rendered inside Hero view.
                'content' => [
                     // Using list items for dynamic stats with icons
                     ['value' => '20+', 'label' => 'Ruang Kelas', 'icon' => 'chalkboard'],
                     ['value' => '30+', 'label' => 'Alat Terapi', 'icon' => 'shapes'],
                     ['value' => '3+', 'label' => 'Area Bermain', 'icon' => 'smile'],
                     ['value' => '1000+', 'label' => 'Koleksi Buku', 'icon' => 'book'],
                ]
            ]
        );

        // Ruang Belajar Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $fasilitas->id, 'section_key' => 'ruang_belajar'],
            [
                'type' => 'list',
                'order' => 2,
                'content' => [
                    'header' => [
                        'badge' => 'Fasilitas Utama',
                        'title' => 'Ruang Belajar',
                        'description' => 'Ruang belajar kami dirancang dengan konsep modern dan nyaman untuk menciptakan suasana belajar yang kondusif. Dilengkapi dengan teknologi pembelajaran terkini dan tata ruang yang mendukung interaksi optimal antara guru dan siswa.',
                        'icon' => 'chalkboard-teacher', // Default Icon
                        'icon_color' => 'primary',
                        'feature_1_title' => 'Kapasitas 8-15 Siswa',
                        'feature_1_desc' => 'Ukuran kelas ideal untuk pembelajaran personal',
                        'feature_2_title' => 'Ruangan Ber AC',
                        'feature_2_desc' => 'Setiap ruangan dilengkapi dengan AC untuk kenyamanan siswa.',
                        'feature_3_title' => 'Furniture Ergonomis',
                        'feature_3_desc' => 'Meja dan kursi yang nyaman untuk belajar',
                    ],
                    'items' => [
                         ['image' => 'img/fasilitas-ruang-belajar-1.jpg', 'title' => 'Ruang Kelas Modern'],
                         ['image' => 'img/fasilitas-ruang-belajar-2.jpg', 'title' => 'Suasana Belajar Kondusif'],
                         ['image' => 'img/fasilitas-ruang-belajar-3.jpg', 'title' => 'Ruang Kelas Dilengkapi Ac'],
                         ['image' => 'img/fasilitas-ruang-belajar-4.jpg', 'title' => 'Furniture Ergonomis dan Nyaman'],
                    ]
                ]
            ]
        );

        // Ruang Terapi Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $fasilitas->id, 'section_key' => 'ruang_terapi'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Program Terapi',
                        'title' => 'Ruang Terapi',
                        'description' => 'Menyediakan berbagai alat terapi yang digunakan khusus untuk mendukung perkembangan motorik dan sensorik pada anak-anak berkebutuhan khusus.',
                        'icon' => 'heartbeat', // Default Icon
                        'icon_color' => 'accent-orange',
                        'feature_1_title' => 'Banyak Variasi',
                        'feature_1_desc' => 'Disesuaikan Kebutuhan Siswa',
                        'feature_2_title' => 'Warna dan Bentuk Menarik',
                        'feature_2_desc' => 'Menarik perhatian siswa',
                        'feature_3_title' => 'Aman Digunakan',
                        'feature_3_desc' => 'Terjamin menggunakan alat terapi yang aman',
                    ],
                    'items' => [
                        ['image' => 'img/fasilitas-ruang-terapi-1.jpg', 'title' => 'Ruang Terapi Lengkap'],
                        ['image' => 'img/fasilitas-ruang-terapi-2.jpg', 'title' => 'Alat Terapi Sensorik'],
                        ['image' => 'img/fasilitas-ruang-terapi-3.jpg', 'title' => 'Ruang Terapi Anak Berkebutuhan Khusus'],
                        ['image' => 'img/fasilitas-ruang-terapi-4.jpg', 'title' => 'Fasilitas Ruang Terapi'],
                    ]
                ]
            ]
        );

        // Area Bermain Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $fasilitas->id, 'section_key' => 'area_bermain'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'badge' => 'Fasilitas Rekreasi',
                        'title' => 'Area Bermain',
                        'description' => 'Area bermain yang luas dan aman untuk mengembangkan motorik kasar anak. Dilengkapi dengan berbagai permainan edukatif yang mendukung perkembangan fisik dan sosial anak.',
                        'icon' => 'shapes', // Default Icon
                        'icon_color' => 'accent-yellow',
                        'feature_1_title' => 'Playground Aman',
                        'feature_1_desc' => 'Fasilitas bermain dengan standar keamanan tinggi',
                        'feature_2_title' => 'Indoor & Outdoor',
                        'feature_2_desc' => 'Area bermain dalam dan luar ruangan',
                        'feature_3_title' => 'Permainan Edukatif',
                        'feature_3_desc' => 'Bermain sambil belajar',
                    ],
                    'items' => [
                        ['image' => 'img/fasilitas-area-bermain-1.jpg', 'title' => 'Playground Outdoor'],
                        ['image' => 'img/fasilitas-area-bermain-2.jpg', 'title' => 'Area Bermain Indoor'],
                        ['image' => 'img/fasilitas-area-bermain-3.jpg', 'title' => 'Zona Bermain Aman'],
                        ['image' => 'img/fasilitas-area-bermain-4.jpg', 'title' => 'Permainan Edukatif'],
                    ]
                ]
            ]
        );

        // Gallery Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $fasilitas->id, 'section_key' => 'gallery'],
            [
                'type' => 'list',
                'order' => 6, // Order after Perpustakaan (which is 5 in LibrarySeeder)
                'content' => [
                    'header' => [
                        'badge' => 'Galeri Fasilitas',
                        'title' => 'Fasilitas Lainnya',
                        'description' => 'Lihat berbagai fasilitas pendukung lainnya yang kami sediakan untuk kenyamanan belajar siswa',
                    ],
                    'items' => [
                        ['image' => 'img/gallery-fasilitas-1.jpg', 'title' => 'Aula'],
                        ['image' => 'img/gallery-fasilitas-2.jpg', 'title' => 'Ruang Musik'],
                        ['image' => 'img/gallery-fasilitas-3.jpg', 'title' => 'Laboratorium Komputer'],
                        ['image' => 'img/gallery-fasilitas-4.jpg', 'title' => 'Kantin Sehat'],
                        ['image' => 'img/gallery-fasilitas-5.jpg', 'title' => 'UKS'],
                        ['image' => 'img/gallery-fasilitas-6.jpg', 'title' => 'Musholla'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 11. PPDB PAGE
        // ==========================================
        $ppdb = LandingPage::updateOrCreate(
            ['slug' => 'ppdb'],
            ['title' => 'PPDB', 'order' => 11]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'is_visible' => true,
                'content' => [
                    'tahun_ajaran' => 'Tahun Ajaran 2025/2026',
                    'title' => 'Penerimaan Peserta',
                    'title_highlight' => 'Didik Baru',
                    'subtitle' => 'Bergabunglah bersama kami dan raih masa depan yang cerah melalui pendidikan berkualitas',
                    'background_image' => 'img/bg-ppdb.jpg',
                    'cta_text' => 'Daftar Sekarang',
                    'cta_link' => '/kontak',
                ]
            ]
        );

        // Quick Info Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'quick_info'],
            [
                'type' => 'rich_text',
                'order' => 2,
                'is_visible' => true,
                'content' => [
                    'periode_label' => 'Periode Pendaftaran',
                    'periode_value' => '1 Jan - 31 Mei 2026',
                    'biaya_label' => 'Biaya Pendaftaran',
                    'biaya_value' => '200 Ribu',
                    'kuota_label' => 'Kuota Tersedia',
                    'kuota_value' => '100 Siswa',
                ]
            ]
        );

        // Alur Pendaftaran Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'alur'],
            [
                'type' => 'list',
                'order' => 3,
                'is_visible' => true,
                'content' => [
                    'header' => [
                        'badge' => 'Langkah Mudah',
                        'title' => 'Alur Pendaftaran',
                        'description' => 'Ikuti langkah mudah untuk mendaftar sebagai peserta didik baru',
                    ],
                    'items' => [
                        ['title' => 'Isi Formulir', 'description' => 'Datang ke cabang Gedung Utama dan mengisi formulir yang diberikan administrator.'],
                        ['title' => 'Melengkapi Dokumen', 'description' => 'Melengkapi berkas persyaratan yang diperlukan'],
                        ['title' => 'Verifikasi', 'description' => 'Tim kami akan memverifikasi data dan dokumen Anda'],
                        ['title' => 'Wawancara', 'description' => 'Ikuti sesi wawancara singkat dengan tim kami'],
                        ['title' => 'Pengumuman', 'description' => 'Terima pengumuman hasil dan mulai belajar!'],
                    ],
                ]
            ]
        );

        // ===== SYARAT PENDAFTARAN SECTIONS =====
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'syarat_header'],
            [
                'type' => 'rich_text',
                'order' => 4,
                'is_visible' => true,
                'content' => [
                    'badge' => 'Persyaratan',
                    'title' => 'Syarat Pendaftaran',
                    'description' => 'Siapkan dokumen-dokumen berikut untuk melengkapi pendaftaran Anda',
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'syarat_paud'],
            [
                'type' => 'list',
                'order' => 5,
                'is_visible' => true,
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
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'syarat_paket_a'],
            [
                'type' => 'list',
                'order' => 6,
                'is_visible' => true,
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
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'syarat_paket_b'],
            [
                'type' => 'list',
                'order' => 7,
                'is_visible' => true,
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
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'syarat_paket_c'],
            [
                'type' => 'list',
                'order' => 8,
                'is_visible' => true,
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
                ]
            ]
        );

        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'syarat_inklusi'],
            [
                'type' => 'list',
                'order' => 9,
                'is_visible' => true,
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
                ]
            ]
        );

        // Investasi Pendidikan Section Header
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'investasi'],
            [
                'type' => 'rich_text',
                'order' => 10,
                'is_visible' => false,
                'content' => [
                    'badge' => 'Investasi Pendidikan',
                    'title' => 'Detail Biaya Pendidikan',
                    'description' => 'Biaya terjangkau dengan kualitas pendidikan terbaik',
                ]
            ]
        );

        // Biaya PAUD Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'biaya_paud'],
            [
                'type' => 'list',
                'order' => 11,
                'is_visible' => false,
                'content' => [
                    'header' => [
                        'title' => 'PAUD',
                        'subtitle' => 'Pendidikan Anak Usia Dini',
                        'badge_text' => 'Mulai Rp 600rb/bln',
                        'color' => 'yellow',
                        'icon' => 'fa-palette',
                    ],
                    'items' => [
                        ['name' => 'Pendaftaran', 'price' => 'Rp 200.000', 'type' => 'pokok'],
                        ['name' => 'SPP/Bulan', 'price' => 'Rp 500.000', 'type' => 'pokok'],
                        ['name' => 'Buku & Alat Tulis', 'price' => 'Rp 500.000', 'type' => 'tambahan'],
                        ['name' => 'Seragam (2 stel)', 'price' => 'Rp 550.000', 'type' => 'tambahan'],
                        ['name' => 'Kegiatan Ekstrakurikuler', 'price' => 'Rp 900.000', 'type' => 'tambahan'],
                        ['name' => 'Foto Rapot', 'price' => 'Rp 100.000', 'type' => 'tambahan'],
                        ['name' => 'Wisuda & Ijazah', 'price' => 'Rp 350.000', 'type' => 'tambahan'],
                    ]
                ]
            ]
        );

        // Biaya SD Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'biaya_sd'],
            [
                'type' => 'list',
                'order' => 12,
                'is_visible' => false,
                'content' => [
                    'header' => [
                        'title' => 'SD',
                        'subtitle' => 'Sekolah Dasar',
                        'badge_text' => 'Mulai Rp 1 Jt/bln',
                        'color' => 'blue',
                        'icon' => 'fa-book',
                    ],
                    'items' => [
                        ['name' => 'Pendaftaran', 'price' => 'Rp 200.000', 'type' => 'pokok'],
                        ['name' => 'SPP/Bulan', 'price' => 'Rp 1.000.000', 'type' => 'pokok'],
                        ['name' => 'Buku & Alat Tulis', 'price' => 'Rp 600.000', 'type' => 'tambahan'],
                        ['name' => 'Seragam (2 stel)', 'price' => 'Rp 600.000', 'type' => 'tambahan'],
                        ['name' => 'Kegiatan Ekstrakurikuler', 'price' => 'Rp 1.000.000', 'type' => 'tambahan'],
                        ['name' => 'Foto Rapot', 'price' => 'Rp 100.000', 'type' => 'tambahan'],
                        ['name' => 'Wisuda & Ijazah', 'price' => 'Rp 500.000', 'type' => 'tambahan'],
                    ]
                ]
            ]
        );

        // Biaya SMP Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'biaya_smp'],
            [
                'type' => 'list',
                'order' => 13,
                'is_visible' => false,
                'content' => [
                    'header' => [
                        'title' => 'SMP',
                        'subtitle' => 'Sekolah Menengah Pertama',
                        'badge_text' => 'Mulai Rp 1.3 Jt/bln',
                        'color' => 'green',
                        'icon' => 'fa-book-open',
                    ],
                    'items' => [
                        ['name' => 'Pendaftaran', 'price' => 'Rp 200.000', 'type' => 'pokok'],
                        ['name' => 'SPP/Bulan', 'price' => 'Rp 1.300.000', 'type' => 'pokok'],
                        ['name' => 'Buku & Alat Tulis', 'price' => 'Rp 700.000', 'type' => 'tambahan'],
                        ['name' => 'Seragam (2 stel)', 'price' => 'Rp 650.000', 'type' => 'tambahan'],
                        ['name' => 'Kegiatan Ekstrakurikuler', 'price' => 'Rp 1.200.000', 'type' => 'tambahan'],
                        ['name' => 'Foto Rapot', 'price' => 'Rp 100.000', 'type' => 'tambahan'],
                        ['name' => 'Wisuda & Ijazah', 'price' => 'Rp 600.000', 'type' => 'tambahan'],
                    ]
                ]
            ]
        );

        // Biaya SMA Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $ppdb->id, 'section_key' => 'biaya_sma'],
            [
                'type' => 'list',
                'order' => 14,
                'is_visible' => false,
                'content' => [
                    'header' => [
                        'title' => 'SMA',
                        'subtitle' => 'Sekolah Menengah Atas',
                        'badge_text' => 'Mulai Rp 1.5 Jt/bln',
                        'color' => 'orange',
                        'icon' => 'fa-graduation-cap',
                    ],
                    'items' => [
                        ['name' => 'Pendaftaran', 'price' => 'Rp 200.000', 'type' => 'pokok'],
                        ['name' => 'SPP/Bulan', 'price' => 'Rp 1.300.000', 'type' => 'pokok'],
                        ['name' => 'Buku & Alat Tulis', 'price' => 'Rp 800.000', 'type' => 'tambahan'],
                        ['name' => 'Seragam (2 stel)', 'price' => 'Rp 700.000', 'type' => 'tambahan'],
                        ['name' => 'Kegiatan Ekstrakurikuler', 'price' => 'Rp 1.500.000', 'type' => 'tambahan'],
                        ['name' => 'Foto Rapot', 'price' => 'Rp 100.000', 'type' => 'tambahan'],
                        ['name' => 'Wisuda & Ijazah', 'price' => 'Rp 750.000', 'type' => 'tambahan'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 12. GALERI PAGE
        // ==========================================
        $galeri = LandingPage::updateOrCreate(
            ['slug' => 'galeri'],
            ['title' => 'Galeri', 'order' => 12]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $galeri->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'badge' => 'Galeri Kami',
                    'title' => 'Galeri Kegiatan',
                    'subtitle' => 'Dokumentasi kegiatan pembelajaran, prestasi, dan momen berharga siswa-siswi PKBM House Of Knowledge',
                    'background_image' => 'img/bg-galeri.jpg',
                ]
            ]
        );

        // Categories Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $galeri->id, 'section_key' => 'categories'],
            [
                'type' => 'list',
                'order' => 2,
                'content' => [
                    'header' => [
                        'title' => 'Kategori Galeri',
                    ],
                    'items' => [
                        ['key' => 'pembelajaran', 'label' => 'Pembelajaran', 'color' => 'primary'],
                        ['key' => 'prestasi', 'label' => 'Prestasi', 'color' => 'accent-yellow'],
                        ['key' => 'ekstrakurikuler', 'label' => 'Ekstrakurikuler', 'color' => 'secondary'],
                        ['key' => 'terapi', 'label' => 'Terapi', 'color' => 'accent-orange'],
                        ['key' => 'acara', 'label' => 'Acara Khusus', 'color' => 'accent-bright'],
                    ]
                ]
            ]
        );

        // Gallery Items Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $galeri->id, 'section_key' => 'gallery_items'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'title' => 'Koleksi Galeri',
                    ],
                    'items' => [
                        ['image' => 'img/gallery-1.jpg', 'category' => 'pembelajaran', 'title' => 'Kegiatan Belajar Interaktif', 'date' => '15 November 2024'],
                        ['image' => 'img/gallery-2.jpg', 'category' => 'prestasi', 'title' => 'Juara Olimpiade Sains', 'date' => '10 November 2024'],
                        ['image' => 'img/gallery-3.jpg', 'category' => 'ekstrakurikuler', 'title' => 'Kegiatan Olahraga', 'date' => '5 November 2024'],
                        ['image' => 'img/gallery-4.jpg', 'category' => 'terapi', 'title' => 'Sesi Terapi Anak', 'date' => '1 November 2024'],
                        ['image' => 'img/gallery-5.jpg', 'category' => 'pembelajaran', 'title' => 'Pembelajaran Kreatif', 'date' => '28 Oktober 2024'],
                        ['image' => 'img/gallery-6.jpg', 'category' => 'acara', 'title' => 'Perayaan Hari Pendidikan', 'date' => '25 Oktober 2024'],
                        ['image' => 'img/gallery-1.jpg', 'category' => 'ekstrakurikuler', 'title' => 'Pentas Seni Musik', 'date' => '20 Oktober 2024'],
                        ['image' => 'img/gallery-2.jpg', 'category' => 'prestasi', 'title' => 'Juara Lomba Pidato', 'date' => '15 Oktober 2024'],
                        ['image' => 'img/gallery-3.jpg', 'category' => 'pembelajaran', 'title' => 'Kegiatan Praktikum', 'date' => '10 Oktober 2024'],
                        ['image' => 'img/gallery-4.jpg', 'category' => 'acara', 'title' => 'Kunjungan Edukasi', 'date' => '5 Oktober 2024'],
                        ['image' => 'img/gallery-5.jpg', 'category' => 'terapi', 'title' => 'Terapi Sensori Integrasi', 'date' => '1 Oktober 2024'],
                        ['image' => 'img/gallery-6.jpg', 'category' => 'ekstrakurikuler', 'title' => 'Latihan Tari Tradisional', 'date' => '28 September 2024'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 13. KONTAK PAGE
        // ==========================================
        $kontak = LandingPage::updateOrCreate(
            ['slug' => 'kontak'],
            ['title' => 'Kontak', 'order' => 13]
        );

        // Hero Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $kontak->id, 'section_key' => 'hero'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'badge' => 'Hubungi Kami',
                    'title' => 'Kontak',
                    'title_highlight' => 'Kami',
                    'subtitle' => 'Kami siap membantu Anda! Jangan ragu untuk menghubungi kami melalui berbagai cara yang tersedia',
                    'background_image' => 'img/bg-kontak.jpg',
                ]
            ]
        );

        // Contact Info Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $kontak->id, 'section_key' => 'contact_info'],
            [
                'type' => 'list',
                'order' => 2,
                'content' => [
                    'header' => ['title' => 'Informasi Kontak'],
                    'items' => [
                        ['type' => 'whatsapp', 'title' => 'WhatsApp', 'value' => '+62 858-1125-8534', 'link' => 'https://wa.me/6285811258534', 'note' => 'Respon cepat via chat'],
                        ['type' => 'phone', 'title' => 'Telepon', 'value' => '+62 858-1125-8534', 'link' => 'tel:6285811258534', 'note' => 'Senin - Jumat: 08:00 - 14:00'],
                        ['type' => 'email', 'title' => 'Email', 'value' => 'hokhomeschool@gmail.com', 'link' => 'https://mail.google.com/mail/?view=cm&fs=1&to=hokhomeschool@gmail.com', 'note' => 'Respon dalam 1x24 jam'],
                    ]
                ]
            ]
        );

        // Locations Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $kontak->id, 'section_key' => 'locations'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => [
                        'badge' => 'Lokasi Kami',
                        'title' => 'Kunjungi Cabang Terdekat',
                        'subtitle' => 'Kami hadir di 3 lokasi strategis untuk melayani Anda lebih baik',
                    ],
                    'items' => [
                        [
                            'name' => 'Gedung Utama PKBM House Of Knowledge',
                            'area' => 'Pamulang Barat',
                            'address' => 'Komplek Ruko Reni Jaya Baru Jl.Ketapang III Blok AF 5 No 22-23 Pamulang Barat, Tangerang Selatan',
                            'map_embed' => 'https://www.google.com/maps?q=-6.353980078518493,106.7322059&hl=id&z=15&output=embed',
                            'map_link' => 'https://maps.app.goo.gl/FKRUXijm2vaeMkEf6',
                            'color' => 'orange',
                        ],
                        [
                            'name' => 'PAUD House Of Knowledge',
                            'area' => 'Pamulang',
                            'address' => 'PAUD House Of Knowledge, Jl. Bratasena I, Pondok Benda, Pondok Benda, Tangerang Selatan, Banten',
                            'map_embed' => 'https://www.google.com/maps?q=-6.352712690063536,106.72825486873509&hl=id&z=15&output=embed',
                            'map_link' => 'https://maps.app.goo.gl/zyKggvw4UVFWvLS59',
                            'color' => 'blue',
                        ],
                        [
                            'name' => 'House Of Knowledge Cimanggis',
                            'area' => 'Ciputat',
                            'address' => 'Ruko Prima Ciputat, Jl. Otista Raya Blok A25, Ruko Prima Ciputat, Tangerang Selatan, Banten',
                            'map_embed' => 'https://www.google.com/maps?q=-6.323863492841053,106.74431087466765&hl=id&z=15&output=embed',
                            'map_link' => 'https://maps.app.goo.gl/z1d7DxypJP2ibjdY6',
                            'color' => 'green',
                        ],
                    ]
                ]
            ]
        );

        // Social Media Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $kontak->id, 'section_key' => 'social_media'],
            [
                'type' => 'list',
                'order' => 4,
                'content' => [
                    'header' => [
                        'badge' => 'Media Sosial',
                        'title' => 'Ikuti Kami di Sosial Media',
                        'subtitle' => 'Dapatkan update terbaru tentang kegiatan dan informasi sekolah',
                    ],
                    'items' => [
                        ['platform' => 'Facebook', 'link' => 'https://www.facebook.com/profile.php?id=100054416532781', 'color' => 'blue-600'],
                        ['platform' => 'Instagram', 'link' => 'https://www.instagram.com/hok_homeschool?igsh=MTE1Ymg1Mmt0NzNzdA==', 'color' => 'instagram'],
                        ['platform' => 'YouTube', 'link' => 'https://youtube.com/@houseofknowledgepamulang5963?si=iGVJsrKalXFsNcaw', 'color' => 'red-600'],
                        ['platform' => 'TikTok', 'link' => 'https://www.tiktok.com/@hokhomeschool?_r=1&_t=ZS-92IrquFuJOr', 'color' => 'gray-900'],
                    ]
                ]
            ]
        );

        // ==========================================
        // 14. FOOTER COMPONENT
        // ==========================================
        $footer = LandingPage::updateOrCreate(
            ['slug' => 'footer'],
            ['title' => 'Footer Component', 'order' => 99]
        );

        // About Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $footer->id, 'section_key' => 'about'],
            [
                'type' => 'rich_text',
                'order' => 1,
                'content' => [
                    'logo' => 'img/logo.png',
                    'name' => 'House Of Knowledge',
                    'description' => 'Tempat mencetak penerus bangsa yang berkualitas dan berprestasi di segala bidang yang dapat bersaing di dunia internasional.',
                    'facebook_url' => 'https://www.facebook.com/profile.php?id=100054416532781',
                    'youtube_url' => 'https://youtube.com/@houseofknowledgepamulang5963?si=iGVJsrKalXFsNcaw',
                    'tiktok_url' => 'https://www.tiktok.com/@hokhomeschool?_r=1&_t=ZS-92IrquFuJOr',
                    'instagram_url' => 'https://www.instagram.com/hok_homeschool?igsh=MTE1Ymg1Mmt0NzNzdA==',
                ]
            ]
        );

        // Quick Links Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $footer->id, 'section_key' => 'quick_links'],
            [
                'type' => 'list',
                'order' => 2,
                'content' => [
                    'header' => ['title' => 'Link Cepat'],
                    'items' => [
                        ['label' => 'Beranda', 'url' => '/'],
                        ['label' => 'Tentang Kami', 'url' => '/tentang-sekolah'],
                        ['label' => 'Program', 'url' => '/program-sd-sma'],
                        ['label' => 'Fasilitas', 'url' => '/fasilitas'],
                        ['label' => 'Pendaftaran', 'url' => '/pendaftaran'],
                        ['label' => 'Galeri', 'url' => '/galeri'],
                        ['label' => 'Berita', 'url' => '/berita'],
                        ['label' => 'Kontak', 'url' => '/kontak'],
                    ]
                ]
            ]
        );

        // Program Links Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $footer->id, 'section_key' => 'program_links'],
            [
                'type' => 'list',
                'order' => 3,
                'content' => [
                    'header' => ['title' => 'Program Pendidikan'],
                    'items' => [
                        ['label' => 'PAUD - TK', 'url' => '/program-paud-tk'],
                        ['label' => 'SD - SMA', 'url' => '/program-sd-sma'],
                        ['label' => 'Program Inklusi', 'url' => '/program-inklusi'],
                        ['label' => 'Program Terapi', 'url' => '/program-terapi'],
                    ]
                ]
            ]
        );

        // Contact Info Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $footer->id, 'section_key' => 'contact_info'],
            [
                'type' => 'rich_text',
                'order' => 4,
                'content' => [
                    'title' => 'Hubungi Kami',
                    'address' => 'Komplek Ruko Reni Jaya Baru Jl.Ketapang III Blok AF 5 No 22-23 Pamulang Barat, Tangerang Selatan',
                    'phone' => '+62 858-1125-8534',
                    'email' => 'hokhomeschool@gmail.com',
                    'hours' => 'Senin - Jumat: 08:00 - 14:00',
                ]
            ]
        );

        // Bottom Footer Section
        LandingPageSection::updateOrCreate(
            ['landing_page_id' => $footer->id, 'section_key' => 'bottom'],
            [
                'type' => 'rich_text',
                'order' => 5,
                'content' => [
                    'copyright' => 'PKBM House Of Knowledge. All rights reserved.',
                    'privacy_url' => '#',
                    'privacy_label' => 'Kebijakan Privasi',
                    'terms_url' => '#',
                    'terms_label' => 'Syarat & Ketentuan',
                    'sitemap_url' => '#',
                    'sitemap_label' => 'Sitemap',
                ]
            ]
        );
    }
}
