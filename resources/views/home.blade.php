<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Sipadu Homescholing - LMS & Pembayaran" description="Portal Sipadu Homescholing untuk pembelajaran daring serta pembayaran tagihan orang tua." keywords="Sipadu Homescholing, LMS homeschooling, pembayaran tagihan homeschooling"></x-seo-meta>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/home.css'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-white">

    <!-- Navbar Component -->
    <x-navbar></x-navbar>

    <!-- ==================== HERO SECTION ==================== -->
    @php
        $hero = $page->getSection('hero');
        $heroContent = $hero->content ?? [];
        $heroContent = array_merge($heroContent, [
            'title_1' => 'Belajar',
            'title_highlight_1' => 'Fleksibel',
            'title_2' => 'dalam Satu',
            'title_highlight_2' => 'Portal',
            'description' => 'Akses LMS untuk kegiatan belajar dan pembayaran tagihan melalui satu akun.',
            'button_text' => 'Masuk ke Portal',
            'button_link' => route('login'),
        ]);

        $program = $page->getSection('program');
        $programContent = $program->content ?? [];
        $programHeader = [
            'badge' => 'Layanan Utama',
            'title' => 'Portal Sipadu Homescholing',
            'description' => 'Layanan digital difokuskan pada pembelajaran homeschooling dan pembayaran tagihan orang tua.',
        ];
        $programItems = [
            [
                'title' => 'LMS Pembelajaran',
                'description' => 'Materi, tugas, ujian, dan aktivitas belajar tersedia sesuai akun masing-masing.',
                'link' => '/login',
                'color' => 'primary',
                'icon' => null,
            ],
            [
                'title' => 'Pembayaran Tagihan',
                'description' => 'Orang tua dapat melihat dan membayar tagihan yang tersedia melalui akun masing-masing.',
                'link' => '/login',
                'color' => 'secondary',
                'icon' => null,
            ],
        ];

        $galeriPage = \App\Models\LandingPage::where('slug', 'galeri')->with('sections')->first();
        $gallerySection = $galeriPage ? $galeriPage->getSection('gallery_items') : null;
        $galleryContent = $gallerySection->content ?? [];
        $galleryHeader = ['badge' => 'Galeri Kami', 'title' => 'Galeri', 'description' => 'Berisi Kegiatan Siswa Dan Siswi']; // Fallback header

        // Ambil data item, balik urutannya (terbaru di awal), dan batasi maksimal 6
        $allGalleryItems = $galleryContent['items'] ?? [];
        $galleryItems = array_slice(array_reverse($allGalleryItems), 0, 6);

        $ctaSection = $page->getSection('cta_section');
        $ctaContent = $ctaSection->content ?? [];
    @endphp
    <section class="relative min-h-screen flex items-center overflow-hidden" style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>

        <!-- Decorative Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 border-4 border-white/20 rounded-full float-animation hidden lg:block"></div>
        <div class="absolute bottom-40 left-20 w-10 h-10 bg-accent-yellow/30 rounded-full float-animation hidden lg:block" style="animation-delay: 1s;"></div>
        <div class="absolute top-40 right-40 w-16 h-16 border-4 border-secondary/30 rounded-lg rotate-45 float-animation hidden lg:block" style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                        {{ $heroContent['title_1'] ?? 'Sistem' }}<br>
                        <span class="text-accent-yellow">{{ $heroContent['title_highlight_1'] ?? 'Pembelajaran' }}</span><br>
                        {{ $heroContent['title_2'] ?? 'dan' }}
                        <span class="text-accent-yellow">{{ $heroContent['title_highlight_2'] ?? 'Akademik' }}</span>
                    </h1>
                    <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed max-w-xl">
                        {{ $heroContent['description'] ?? 'House Of Knowledge menyediakan media pembelajaran dan akademik berbasis website "SipaduHOK" sebagai media pembelajaran online yang lebih fleksibel' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ $heroContent['button_link'] ?? '#program' }}" class="inline-flex items-center justify-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full shadow-lg transition-all duration-300 hover:-translate-y-1">
                            {{ $heroContent['button_text'] ?? 'Jelajahi Sekarang' }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Content - Decorative Image -->
                <div class="hidden lg:block">
                    <div class="decorative-frame-tilt relative">
                        <img loading="lazy" decoding="async" src="{{ asset($heroContent['image'] ?? 'img/hero-img.jpg') }}" alt="Sipadu Homescholing" class="rounded-2xl shadow-2xl w-full h-[400px] object-cover transform rotate-6 hover:rotate-3 transition-transform duration-500">

                        <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl px-5 py-4 shadow-xl transform -rotate-3">
                            <p class="font-bold text-gray-800">LMS & Pembayaran</p>
                            <p class="text-sm text-gray-500">Akses sesuai akun</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-28 sm:bottom-24 md:bottom-20 left-1/2 transform -translate-x-1/2 z-20">
            <a href="#program" class="scroll-indicator flex flex-col items-center text-white/80 hover:text-white transition-all duration-300 group">
                <span class="text-xs sm:text-sm mb-2 sm:mb-3 font-medium tracking-wide">Scroll Down</span>
                <div class="relative w-6 h-10 sm:w-8 sm:h-12 border-2 border-white rounded-full flex items-center justify-center group-hover:border-accent-yellow transition-colors duration-300">
                    <div class="scroll-wheel absolute w-1 h-2 sm:h-3 bg-white rounded-full top-2 sm:top-3 group-hover:bg-accent-yellow transition-colors duration-300"></div>
                </div>
            </a>
        </div>
    </section>

    <!-- ==================== PROGRAM SECTION ==================== -->
    <section id="program" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Logic moved to top php block --}}

            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    {{ $programHeader['badge'] ?? 'Program Kami' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $programHeader['title'] ?? 'Program PKBM House Of Knowledge' }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $programHeader['description'] ?? 'Alasan kenapa harus memilih untuk bergabung dengan PKBM House Of Knowledge?' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($programItems as $item)
                @php
                    $isHex = isset($item['color']) && substr($item['color'], 0, 1) === '#';
                    $themeColor = $item['color'] ?? 'primary';
                    $shadeMap = ['primary' => 'blue', 'secondary' => 'green', 'accent-orange' => 'orange', 'accent-yellow' => 'yellow'];
                    $shadeColor = $shadeMap[$themeColor] ?? 'blue';

                    // Fallback classes
                    $borderClass = $isHex ? '' : 'border-' . $themeColor;
                    $bgIconClass = $isHex ? '' : 'bg-' . $shadeColor . '-50';
                    $groupHoverIconBgClass = $isHex ? '' : 'group-hover:bg-' . $themeColor;
                    $iconColorClass = $isHex ? '' : 'text-' . $themeColor;

                    // Inline styles
                    $cardStyle = $isHex ? "border-top-color: $themeColor;" : "";
                    $iconBgStyle = $isHex ? "background-color: {$themeColor}10;" : ""; // ~6% opacity
                    $iconStyle = $isHex ? "color: $themeColor;" : "";

                    $programIconSource = strtolower(($item['title'] ?? '') . ' ' . ($item['link'] ?? ''));
                    $programIconKey = match (true) {
                        str_contains($programIconSource, 'inklusi') => 'inklusi',
                        str_contains($programIconSource, 'kesetaraan') || str_contains($programIconSource, 'sd-sma') => 'kesetaraan',
                        str_contains($programIconSource, 'konseling') || str_contains($programIconSource, 'terapi') => 'konseling',
                        str_contains($programIconSource, 'usia dini') || str_contains($programIconSource, 'paud') || str_contains($programIconSource, 'tk') => 'usia_dini',
                        default => 'program',
                    };
                @endphp
                <div class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 {{ $borderClass }} group" style="{{ $cardStyle }}">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center {{ $bgIconClass }} {{ $groupHoverIconBgClass }} transition-colors duration-300"
                         style="{{ $iconBgStyle }} {{ $isHex ? 'border: 1px solid '.$themeColor.'20;' : '' }}">
                        {{-- Custom image icon or relevant program fallback icon. --}}
                        @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                             <img loading="lazy" decoding="async" src="{{ asset($item['icon']) }}" alt="{{ $item['title'] ?? 'Program' }}" class="w-10 h-10 object-contain">
                        @else
                            @switch($programIconKey)
                                @case('inklusi')
                                    <svg class="w-10 h-10 {{ $iconColorClass }} group-hover:text-white transition-colors duration-300" style="{{ $iconStyle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s-7-4.35-7-10a4 4 0 017-2.65A4 4 0 0119 11c0 5.65-7 10-7 10z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v5m-2.5-2.5h5" />
                                    </svg>
                                    @break

                                @case('kesetaraan')
                                    <svg class="w-10 h-10 {{ $iconColorClass }} group-hover:text-white transition-colors duration-300" style="{{ $iconStyle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16M5 7h14" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7l-3 6h6L7 7zm10 0l-3 6h6l-3-6z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 20h8" />
                                    </svg>
                                    @break

                                @case('konseling')
                                    <svg class="w-10 h-10 {{ $iconColorClass }} group-hover:text-white transition-colors duration-300" style="{{ $iconStyle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 14a6 6 0 118 0c-.8.8-1.2 1.6-1.2 2.6H9.2c0-1-.4-1.8-1.2-2.6z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.5 20h5M10.5 8.5h.01M13.5 8.5h.01M10 11.5c1.1.9 2.9.9 4 0" />
                                    </svg>
                                    @break

                                @case('usia_dini')
                                    <svg class="w-10 h-10 {{ $iconColorClass }} group-hover:text-white transition-colors duration-300" style="{{ $iconStyle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12.5a4 4 0 100-8 4 4 0 000 8z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 21a6.5 6.5 0 0113 0" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4l.7-1.5L6.4 4 8 4.7l-1.6.7L5.7 7 5 5.4l-1.6-.7L5 4z" />
                                    </svg>
                                    @break

                                @default
                                    <svg class="w-10 h-10 {{ $iconColorClass }} group-hover:text-white transition-colors duration-300" style="{{ $iconStyle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.5l7 3.5-7 3.5-7-3.5 7-3.5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 14l7 3.5 7-3.5" />
                                    </svg>
                            @endswitch
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $item['title'] }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $item['description'] }}</p>
                    <a href="{{ url($item['link'] ?? '#') }}" class="font-semibold hover:opacity-80 transition inline-flex items-center {{ $isHex ? '' : 'text-primary hover:text-secondary' }}" style="{{ $isHex ? 'color: '.$themeColor : '' }}">
                        Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ==================== ABOUT SECTION ==================== -->
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $aboutSection = $page->getSection('about');
                $aboutContent = $aboutSection->content ?? [];
                $aboutContent = array_merge($aboutContent, [
                    'badge' => 'Tentang Layanan',
                    'title' => 'Sipadu Homescholing',
                    'description_1' => 'Layanan pendampingan homeschooling dengan proses belajar yang fleksibel dan lebih personal sesuai kebutuhan anak.',
                    'description_2' => 'Portal ini digunakan untuk kegiatan LMS serta akses pembayaran tagihan orang tua melalui akun masing-masing.',
                    'button_text' => 'Lihat Program',
                    'button_link' => '/program-homeschooling',
                ]);
                $features = [
                    'Pembelajaran fleksibel dan personal',
                    'Materi serta aktivitas belajar dalam LMS',
                    'Tagihan orang tua tersedia setelah login',
                ];
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left - Image -->
                <div class="relative">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                        <img loading="lazy" decoding="async" src="{{ asset($aboutContent['image'] ?? 'img/about-img.jpg') }}" alt="Sipadu Homescholing" class="w-full h-[450px] object-cover">
                    </div>

                    <!-- Experience Badge -->
                    <div class="absolute -bottom-6 -right-6 bg-primary text-white p-6 rounded-2xl shadow-xl hidden md:block">
                        <p class="text-4xl font-bold">{{ $page->getSection('hero')->content['experience_years'] ?? '14+' }}</p>
                        <p class="text-sm">Tahun<br>Pengalaman</p>
                    </div>
                </div>

                <!-- Right - Content -->
                <div>
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                        {{ $aboutContent['badge'] ?? 'Tentang Kami' }}
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        {{ $aboutContent['title'] ?? 'PKBM House Of Knowledge' }}
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_1'] ?? 'House Of Knowledge adalah lembaga pendidikan non-formal yang berkomitmen untuk memberikan layanan pendidikan berkualitas bagi semua kalangan, termasuk anak-anak berkebutuhan khusus.' }}
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        {{ $aboutContent['description_2'] ?? 'Dengan pengalaman lebih dari 14 tahun, kami telah membantu ribuan siswa mencapai potensi terbaik mereka melalui pendekatan pembelajaran yang inovatif dan personal.' }}
                    </p>

                    <!-- Features -->
                    <div class="space-y-4 mb-8">
                        @foreach($features as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>

                    <a href="{{ url($aboutContent['button_link'] ?? '/tentang-sekolah') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                        {{ $aboutContent['button_text'] ?? 'Selengkapnya' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Berita sekolah disembunyikan pada versi homeschooling. --}}
    @if(false)
    <!-- ==================== NEWS SECTION (3D CAROUSEL) ==================== -->
    <section class="py-12 sm:py-20" style="background: linear-gradient(135deg, #165fac 0%, #7cb5ec 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6 sm:mb-12">
                <span class="inline-block px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-full mb-4">
                    {{ $newsHeader['badge'] ?? 'Berita Terbaru' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $newsHeader['title'] ?? 'News' }}</h2>
                <p class="text-white/80 max-w-2xl mx-auto">
                    {{ $newsHeader['description'] ?? 'Ikuti perkembangan terbaru dari kegiatan dan prestasi PKBM House Of Knowledge' }}
                </p>
            </div>

            <!-- 3D Carousel -->
            <div class="carousel-3d relative flex items-center justify-center overflow-hidden">
                <!-- Navigation Left -->
                <button id="carouselPrev" aria-label="Berita sebelumnya" class="absolute left-3 sm:left-6 md:left-8 z-20 w-9 h-9 sm:w-12 sm:h-12 bg-white/95 backdrop-blur rounded-full flex items-center justify-center shadow-xl hover:bg-primary hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Carousel Items -->
                <div id="carouselTrack" class="relative w-full h-full">
                    @foreach($beritaList as $index => $berita)
                    <div class="carousel-item" data-index="{{ $index }}">
                        <a href="{{ $berita->url_berita }}" target="_blank" class="block w-full h-full">
                            <img loading="lazy" decoding="async" src="{{ $berita->gambar_url }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/85 via-black/50 to-transparent">
                                <span class="inline-block px-2.5 py-0.5 {{ $berita->kategori_badge_class }} text-[11px] sm:text-xs rounded-full mb-2">{{ $berita->kategori_label }}</span>
                                <h3 class="text-white font-bold text-sm sm:text-base leading-snug line-clamp-2">{{ $berita->judul }}</h3>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Navigation Right -->
                <button id="carouselNext" aria-label="Berita berikutnya" class="absolute right-3 sm:right-6 md:right-8 z-20 w-9 h-9 sm:w-12 sm:h-12 bg-white/95 backdrop-blur rounded-full flex items-center justify-center shadow-xl hover:bg-primary hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Pagination Dots -->
            <div id="carouselDots" class="carousel-dots" aria-label="Indikator slide berita"></div>

            <!-- Info Display -->
            <div id="newsInfo" class="text-center mt-8">
                <h3 class="text-xl md:text-2xl font-bold text-white mb-2" id="newsTitle">Kegiatan Pembelajaran Aktif</h3>
                <p class="text-white/80" id="newsCategory">Kegiatan</p>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-8">
                <a href="{{ url('/berita') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Lihat Semua Berita
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Galeri sekolah disembunyikan pada versi homeschooling. --}}
    @if(false)
    <!-- ==================== GALLERY SECTION ==================== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    {{ $galleryHeader['badge'] ?? 'Galeri Kami' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ $galleryHeader['title'] ?? 'Galeri' }}</h2>
                <p class="text-gray-600">{{ $galleryHeader['description'] ?? 'Berisi Kegiatan Siswa Dan Siswi' }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if(!empty($galleryItems))
                    @foreach($galleryItems as $item)
                    <div class="gallery-item rounded-2xl shadow-lg">
                        <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}" alt="Gallery Item" class="w-full h-64 object-cover rounded-2xl">
                    </div>
                    @endforeach
                @else
                    <div class="col-span-3 text-center py-4 text-gray-500">Belum ada galeri.</div>
                @endif
            </div>

            <div class="text-center mt-12">
                <a href="{{ url('/galeri') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Lihat Semua Galeri
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- Lokasi lama disembunyikan karena tempat layanan telah berpindah. --}}
    @if(false)
    <!-- ==================== CONTACT SECTION ==================== -->
    <section class="py-20 bg-cream relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-64 h-64 bg-secondary/5 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    {{ $contactHeader['badge'] ?? 'Lokasi Kami' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $contactHeader['title'] ?? 'Kunjungi Cabang Terdekat' }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $contactHeader['description'] ?? 'PKBM House Of Knowledge hadir di 3 lokasi strategis untuk memudahkan akses pendidikan bagi putra-putri Anda.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $colorClasses = [
                        'orange' => ['border' => 'border-accent-orange', 'text' => 'text-accent-orange', 'bg' => 'bg-orange-50', 'icon_bg' => 'bg-orange-100', 'hover_bg' => 'hover:bg-accent-orange', 'shade' => 'orange'],
                        'blue' => ['border' => 'border-primary', 'text' => 'text-primary', 'bg' => 'bg-blue-50', 'icon_bg' => 'bg-blue-100', 'hover_bg' => 'hover:bg-primary', 'shade' => 'blue'],
                        'green' => ['border' => 'border-secondary', 'text' => 'text-secondary', 'bg' => 'bg-green-50', 'icon_bg' => 'bg-green-100', 'hover_bg' => 'hover:bg-secondary', 'shade' => 'green'],
                    ];
                @endphp

                @foreach($contactItems as $item)
                @php
                    $inputColor = $item['color'] ?? 'orange';
                    $isHex = str_starts_with($inputColor, '#');

                    if (!$isHex) {
                        $colorMap = $colorClasses[$inputColor] ?? $colorClasses['orange'];
                        $borderColorClass = $colorMap['border'];
                        $bgShadeClass = $colorMap['bg'];
                        $iconBgClass = $colorMap['icon_bg'];
                        $textColorClass = $colorMap['text'];
                        $iconHoverBgClass = "group-hover:" . str_replace('text-', 'bg-', $colorMap['text']);
                        $cardStyle = "";
                        $bgStyle = "";
                        $iconBgStyle = "";
                        $textStyle = "";
                    } else {
                        $themeColor = $inputColor;
                        $borderColorClass = '';
                        $bgShadeClass = '';
                        $iconBgClass = '';
                        $textColorClass = '';
                        $iconHoverBgClass = '';

                        $cardStyle = "border-bottom-color: $themeColor;";
                        $bgStyle = "background-color: {$themeColor}10;";
                        $iconBgStyle = "background-color: {$themeColor}20; color: $themeColor;";
                        $textStyle = "color: $themeColor;";
                    }
                @endphp
                <div class="card-hover bg-white rounded-2xl shadow-xl p-8 border-b-4 {{ $borderColorClass }} group relative overflow-hidden"
                     style="{{ $cardStyle }}">

                    {{-- Decorative Background Circle --}}
                    <div class="absolute top-0 right-0 w-24 h-24 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110 {{ $bgShadeClass }}"
                         style="{{ $bgStyle }}"></div>

                    <div class="relative z-10">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 transition-all duration-300 {{ $iconBgClass }} {{ $textColorClass }} {{ $iconHoverBgClass }} group-hover:text-white"
                             style="{{ $iconBgStyle }}">
                             @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                 <img loading="lazy" decoding="async" src="{{ asset($item['icon']) }}" alt="Icon" class="w-8 h-8 object-contain">
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $item['name'] ?? '' }}</h3>
                        <p class="text-sm font-medium uppercase tracking-wider mb-4 {{ $textColorClass }}" style="{{ $textStyle }}">
                            {{ $item['area'] ?? '' }}
                        </p>

                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            {!! nl2br(e($item['address'] ?? '')) !!}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ url('/kontak') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg group">
                    <span>Lihat Peta & Kontak Lengkap</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
                <p class="mt-4 text-sm text-gray-500">Klik tombol di atas untuk melihat detail peta masing-masing cabang</p>
            </div>
        </div>
    </section>
    @endif

    <!-- Dynamic CTA -->
    <section class="py-20" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Akses Layanan Sipadu Homescholing
            </h2>
            <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                Masuk untuk mengikuti pembelajaran atau melihat tagihan yang telah diterbitkan melalui akun Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Masuk LMS
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/kontak') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-semibold rounded-full transition-all duration-300">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <x-footer></x-footer>
    @vite(['resources/js/navbar.js', 'resources/js/pages/home.js'])
</body>
</html>
