<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKBM House Of Knowledge - Fasilitas</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#165fac',
                        'secondary': '#287f3b',
                        'accent-orange': '#d45930',
                        'accent-yellow': '#fac030',
                        'accent-bright': '#ffe400',
                        'cream': '#e8e7e2'
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-overlay {
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 20%, rgba(40, 127, 59, 0.85) 80%);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .scale-in {
            animation: scaleIn 0.6s ease-out forwards;
        }

        .facility-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .facility-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .facility-card:hover::before {
            left: 100%;
        }

        .facility-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        .facility-card img {
            transition: transform 0.6s ease;
        }

        .facility-card:hover img {
            transform: scale(1.1);
        }

        .icon-float {
            animation: float 3s ease-in-out infinite;
        }

        .feature-item {
            opacity: 0;
            transform: translateX(-20px);
            animation: slideInLeft 0.5s ease-out forwards;
        }

        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .feature-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .feature-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .feature-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .feature-item:nth-child(5) {
            animation-delay: 0.5s;
        }

        .gallery-image {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        .gallery-image img {
            transition: all 0.5s ease;
        }

        .gallery-image:hover img {
            transform: scale(1.15) rotate(2deg);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
        }

        .gallery-image:hover .gallery-overlay {
            opacity: 1;
        }

        html {
            scroll-behavior: smooth;
        }

        section {
            scroll-margin-top: 100px;
        }

        .decorative-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: blobFloat 8s ease-in-out infinite;
        }

        @keyframes blobFloat {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -30px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        /* ==================== CAROUSEL STYLES ==================== */
        .carousel-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .carousel-wrapper {
            display: flex;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .carousel-slide {
            min-width: 100%;
            height: 500px;
            position: relative;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            opacity: 0;
            pointer-events: none;
        }

        .carousel-container:hover .carousel-nav {
            opacity: 1;
            pointer-events: auto;
        }

        .carousel-nav:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .carousel-nav.prev {
            left: 20px;
        }

        .carousel-nav.next {
            right: 20px;
        }

        .carousel-nav svg {
            width: 60px;
            height: 24px;
            color: #2016ac;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-indicator.active {
            background: white;
            width: 32px;
            border-radius: 6px;
        }

        .carousel-indicator:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        /* Image caption overlay */
        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            color: white;
            padding: 30px 20px 60px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .carousel-slide:hover .carousel-caption {
            opacity: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .carousel-slide {
                height: 300px;
            }

            .carousel-nav {
                width: 40px;
                height: 40px;
            }

            .carousel-nav.prev {
                left: 10px;
            }

            .carousel-nav.next {
                right: 10px;
            }
        }
    </style>
</head>

<body class="bg-white">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $ruangBelajarSection = $page->getSection('ruang_belajar');
        $ruangBelajarContent = $ruangBelajarSection->content ?? [];

        $ruangTerapiSection = $page->getSection('ruang_terapi');
        $ruangTerapiContent = $ruangTerapiSection->content ?? [];

        $areaBermainSection = $page->getSection('area_bermain');
        $areaBermainContent = $areaBermainSection->content ?? [];
    @endphp

    <x-navbar></x-navbar>

    <!-- ==================== HERO SECTION ==================== -->
    <section class="relative min-h-screen flex items-center" style="
            background-image: url('{{ asset($heroContent['background_image'] ?? 'img/bg-fasilitas.jpg') }}'); 
            background-size: cover;
            background-position: center;
        ">

        <div class="hero-overlay absolute inset-0"></div>

        <!-- Decorative Blobs -->
        <div class="decorative-blob absolute top-20 left-10 w-96 h-96 bg-accent-yellow" style="animation-delay: 0s;">
        </div>
        <div class="decorative-blob absolute bottom-20 right-20 w-80 h-80 bg-secondary" style="animation-delay: 2s;">
        </div>

        <!-- Decorative Elements -->
        <div
            class="absolute top-32 left-16 w-20 h-20 border-4 border-white/20 rounded-full float-animation hidden lg:block">
        </div>
        <div class="absolute bottom-40 left-32 w-12 h-12 bg-accent-yellow/30 rounded-full float-animation hidden lg:block"
            style="animation-delay: 1s;"></div>
        <div class="absolute top-48 right-40 w-16 h-16 border-4 border-white/30 rounded-lg rotate-45 float-animation hidden lg:block"
            style="animation-delay: 2s;"></div>
        <div class="absolute bottom-32 right-16 w-10 h-10 bg-white/20 rounded-full float-animation hidden lg:block"
            style="animation-delay: 1.5s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-20">
            <div class="text-center">
                <span
                    class="inline-block px-6 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-medium rounded-full mb-6 fade-in-up">
                    {{ $heroContent['badge'] ?? 'Fasilitas Lengkap & Modern' }}
                </span>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6 fade-in-up"
                    style="animation-delay: 0.2s;">
                    {{ $heroContent['title'] ?? 'Fasilitas' }}<br>
                    <span class="text-accent-yellow">{{ $heroContent['title_highlight'] ?? 'Terbaik' }}</span> Untuk<br>
                    Pembelajaran Optimal
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-12 leading-relaxed max-w-3xl mx-auto fade-in-up"
                    style="animation-delay: 0.4s;">
                    {{ $heroContent['subtitle'] ?? 'PKBM House Of Knowledge menyediakan fasilitas lengkap dan modern untuk mendukung proses belajar mengajar yang efektif dan menyenangkan' }}
                </p>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto scale-in"
                    style="animation-delay: 0.6s;">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                        <p class="text-4xl font-bold text-white mb-2">{{ $heroContent['stat_1_value'] ?? '20+' }}</p>
                        <p class="text-white/80 text-sm">{{ $heroContent['stat_1_label'] ?? 'Ruang Kelas' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                        <p class="text-4xl font-bold text-white mb-2">{{ $heroContent['stat_2_value'] ?? '30+' }}</p>
                        <p class="text-white/80 text-sm">{{ $heroContent['stat_2_label'] ?? 'Alat Terapi' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                        <p class="text-4xl font-bold text-white mb-2">{{ $heroContent['stat_3_value'] ?? '3+' }}</p>
                        <p class="text-white/80 text-sm">{{ $heroContent['stat_3_label'] ?? 'Area Bermain' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                        <p class="text-4xl font-bold text-white mb-2">{{ $heroContent['stat_4_value'] ?? '1000+' }}</p>
                        <p class="text-white/80 text-sm">{{ $heroContent['stat_4_label'] ?? 'Koleksi Buku' }}</p>
                    </div>
                </div>

                <div class="mt-12 fade-in-up" style="animation-delay: 0.8s;">
                    <a href="#fasilitas"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full shadow-2xl transition-all duration-300 hover:-translate-y-1">
                        Jelajahi Fasilitas
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </section>

    <!-- ==================== MAIN FACILITIES SECTION ==================== -->
    <section id="fasilitas" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ===== 1. RUANG BELAJAR ===== -->
            <div class="mb-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Carousel -->
                    <div class="carousel-container" data-carousel="ruang-belajar">
                        <div class="carousel-wrapper">
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-belajar-1.jpg') }}" alt="Ruang Belajar 1">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Ruang Kelas Modern</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-belajar-2.jpg') }}" alt="Ruang Belajar 2">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Suasana Belajar Kondusif</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-belajar-3.jpg') }}" alt="Ruang Belajar 3">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Ruang Kelas Dilengkapi Ac</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-belajar-4.jpg') }}" alt="Ruang Belajar 4">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Furniture Ergonomis dan Nyaman</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button class="carousel-nav prev" data-action="prev">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="carousel-nav next" data-action="next">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button class="carousel-indicator active" data-index="0"></button>
                            <button class="carousel-indicator" data-index="1"></button>
                            <button class="carousel-indicator" data-index="2"></button>
                            <button class="carousel-indicator" data-index="3"></button>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center icon-float">
                                <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                </svg>
                            </div>
                            <span
                                class="px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full">{{ $ruangBelajarContent['badge'] ?? 'Fasilitas Utama' }}</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                            {{ $ruangBelajarContent['title'] ?? 'Ruang Belajar' }}</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            {{ $ruangBelajarContent['description'] ?? 'Ruang belajar kami dirancang dengan konsep modern dan nyaman untuk menciptakan suasana belajar yang kondusif. Dilengkapi dengan teknologi pembelajaran terkini dan tata ruang yang mendukung interaksi optimal antara guru dan siswa.' }}
                        </p>

                        <div class="space-y-4">
                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">
                                        {{ $ruangBelajarContent['feature_1_title'] ?? 'Kapasitas 8-15 Siswa' }}</h4>
                                    <p class="text-gray-600 text-sm">
                                        {{ $ruangBelajarContent['feature_1_desc'] ?? 'Ukuran kelas ideal untuk pembelajaran personal' }}
                                    </p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">
                                        {{ $ruangBelajarContent['feature_2_title'] ?? 'Ruangan Ber AC' }}</h4>
                                    <p class="text-gray-600 text-sm">
                                        {{ $ruangBelajarContent['feature_2_desc'] ?? 'Setiap ruangan dilengkapi dengan AC untuk kenyamanan siswa.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">
                                        {{ $ruangBelajarContent['feature_3_title'] ?? 'Furniture Ergonomis' }}</h4>
                                    <p class="text-gray-600 text-sm">
                                        {{ $ruangBelajarContent['feature_3_desc'] ?? 'Meja dan kursi yang nyaman untuk belajar' }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 2. RUANG TERAPI ===== -->
            <div class="mb-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="order-2 lg:order-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 bg-accent-orange/10 rounded-2xl flex items-center justify-center icon-float">
                                <svg class="w-8 h-8 text-accent-orange" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span
                                class="px-4 py-2 bg-accent-orange/10 text-accent-orange text-sm font-medium rounded-full">Program
                                Terapi</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Ruang Terapi</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            Menyediakan berbagai alat terapi yang digunakan khusus untuk mendukung perkembangan motorik
                            dan sensorik pada anak-anak berkebutuhan khusus.
                        </p>

                        <div class="space-y-4">
                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-accent-orange rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Banyak Variasi</h4>
                                    <p class="text-gray-600 text-sm">Disesuaikan Kebutuhan Siswa</p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-accent-orange rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Warna dan Bentuk Menarik</h4>
                                    <p class="text-gray-600 text-sm">Menarik perhatian siswa</p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-accent-orange rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Aman Digunakan</h4>
                                    <p class="text-gray-600 text-sm">Terjamin menggunakan alat terapi yang aman</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Carousel -->
                    <div class="carousel-container order-1 lg:order-2" data-carousel="ruang-terapi">
                        <div class="carousel-wrapper">
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-terapi-1.jpg') }}" alt="Ruang Terapi 1">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Ruang Terapi Lengkap</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-terapi-2.jpg') }}" alt="Ruang Terapi 2">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Alat Terapi Sensorik</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-terapi-3.jpg') }}" alt="Ruang Terapi 3">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Ruang Terapi Anak Berkebutuhan Khusus</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-ruang-terapi-4.jpg') }}" alt="Ruang Terapi 4">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Fasilitas Ruang Terapi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button class="carousel-nav prev" data-action="prev">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="carousel-nav next" data-action="next">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button class="carousel-indicator active" data-index="0"></button>
                            <button class="carousel-indicator" data-index="1"></button>
                            <button class="carousel-indicator" data-index="2"></button>
                            <button class="carousel-indicator" data-index="3"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 3. AREA BERMAIN ===== -->
            <div class="mb-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Carousel -->
                    <div class="carousel-container" data-carousel="area-bermain">
                        <div class="carousel-wrapper">
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-area-bermain-1.jpg') }}" alt="Area Bermain 1">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Playground Outdoor</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-area-bermain-2.jpg') }}" alt="Area Bermain 2">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Area Bermain Indoor</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-area-bermain-3.jpg') }}" alt="Area Bermain 3">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Zona Bermain Aman</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-area-bermain-4.jpg') }}" alt="Area Bermain 4">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Permainan Edukatif</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button class="carousel-nav prev" data-action="prev">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="carousel-nav next" data-action="next">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button class="carousel-indicator active" data-index="0"></button>
                            <button class="carousel-indicator" data-index="1"></button>
                            <button class="carousel-indicator" data-index="2"></button>
                            <button class="carousel-indicator" data-index="3"></button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 bg-accent-yellow/10 rounded-2xl flex items-center justify-center icon-float">
                                <svg class="w-8 h-8 text-accent-yellow" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <span
                                class="px-4 py-2 bg-accent-yellow/10 text-accent-yellow text-sm font-medium rounded-full">Fasilitas
                                Rekreasi</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Area Bermain</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            Area bermain yang luas dan aman untuk mengembangkan motorik kasar anak. Dilengkapi dengan
                            berbagai permainan edukatif yang mendukung perkembangan fisik dan sosial anak.
                        </p>

                        <div class="space-y-4">
                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Playground Aman</h4>
                                    <p class="text-gray-600 text-sm">Fasilitas bermain dengan standar keamanan tinggi
                                    </p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Permainan Edukatif</h4>
                                    <p class="text-gray-600 text-sm">Bermain sambil belajar untuk perkembangan optimal
                                    </p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Area Luas & Bersih</h4>
                                    <p class="text-gray-600 text-sm">Ruang bermain yang lapang dan terawat</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 4. PERPUSTAKAAN ===== -->
            <div class="mb-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="order-2 lg:order-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 bg-secondary/10 rounded-2xl flex items-center justify-center icon-float">
                                <svg class="w-8 h-8 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                </svg>
                            </div>
                            <span
                                class="px-4 py-2 bg-secondary/10 text-secondary text-sm font-medium rounded-full">Literasi</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Perpustakaan</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            Perpustakaan dengan koleksi lengkap untuk menumbuhkan minat baca dan literasi siswa. Ruangan
                            yang nyaman dengan koleksi buku yang terus diperbarui.
                        </p>

                        <div class="space-y-4">
                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-secondary rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">1000+ Koleksi Buku</h4>
                                    <p class="text-gray-600 text-sm">Beragam buku pelajaran, fiksi, dan non-fiksi</p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-secondary rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Ruang Baca Nyaman</h4>
                                    <p class="text-gray-600 text-sm">Suasana tenang untuk membaca dan belajar</p>
                                </div>
                            </div>

                            <div class="feature-item flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-secondary rounded-full flex items-center justify-center mt-1">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">Sistem Peminjaman Mudah</h4>
                                    <p class="text-gray-600 text-sm">Akses mudah untuk meminjam dan mengembalikan buku
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Carousel -->
                    <div class="carousel-container order-1 lg:order-2" data-carousel="perpustakaan">
                        <div class="carousel-wrapper">
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-perpustakaan-1.jpg') }}" alt="Perpustakaan 1">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Koleksi Buku Lengkap</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-perpustakaan-2.jpg') }}" alt="Perpustakaan 2">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Ruang Baca yang Nyaman</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-perpustakaan-3.jpg') }}" alt="Perpustakaan 3">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Area Literasi Interaktif</p>
                                </div>
                            </div>
                            <div class="carousel-slide">
                                <img src="{{ asset('img/fasilitas-perpustakaan-4.jpg') }}" alt="Perpustakaan 4">
                                <div class="carousel-caption">
                                    <p class="font-semibold text-lg">Pojok Baca Anak</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button class="carousel-nav prev" data-action="prev">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="carousel-nav next" data-action="next">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button class="carousel-indicator active" data-index="0"></button>
                            <button class="carousel-indicator" data-index="1"></button>
                            <button class="carousel-indicator" data-index="2"></button>
                            <button class="carousel-indicator" data-index="3"></button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ==================== GALLERY SECTION (KEEP ORIGINAL) ==================== -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-6 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    Galeri Fasilitas
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Fasilitas Lainnya</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Lihat berbagai fasilitas pendukung lainnya yang kami sediakan untuk kenyamanan belajar siswa
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="gallery-image h-64">
                    <img src="{{ asset('img/gallery-fasilitas-1.jpg') }}" alt="Fasilitas 1"
                        class="w-full h-full object-cover">
                    <div class="gallery-overlay">
                        <p class="text-white font-semibold">Aula</p>
                    </div>
                </div>

                <div class="gallery-image h-64">
                    <img src="{{ asset('img/gallery-fasilitas-2.jpg') }}" alt="Fasilitas 2"
                        class="w-full h-full object-cover">
                    <div class="gallery-overlay">
                        <p class="text-white font-semibold">Area Bermain Anak</p>
                    </div>
                </div>

                <div class="gallery-image h-64">
                    <img src="{{ asset('img/gallery-fasilitas-3.jpg') }}" alt="Fasilitas 3"
                        class="w-full h-full object-cover">
                    <div class="gallery-overlay">
                        <p class="text-white font-semibold">Alat Terapi</p>
                    </div>
                </div>

                <div class="gallery-image h-64">
                    <img src="{{ asset('img/gallery-fasilitas-4.jpg') }}" alt="Fasilitas 4"
                        class="w-full h-full object-cover">
                    <div class="gallery-overlay">
                        <p class="text-white font-semibold">Perpustakaan</p>
                    </div>
                </div>

                <div class="gallery-image h-64">
                    <img src="{{ asset('img/gallery-fasilitas-5.jpg') }}" alt="Fasilitas 5"
                        class="w-full h-full object-cover">
                    <div class="gallery-overlay">
                        <p class="text-white font-semibold">Ruang Serbaguna</p>
                    </div>
                </div>

                <div class="gallery-image h-64">
                    <img src="{{ asset('img/gallery-fasilitas-6.jpg') }}" alt="Fasilitas 6"
                        class="w-full h-full object-cover">
                    <div class="gallery-overlay">
                        <p class="text-white font-semibold">Lapangan Upacara</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== STATS SECTION (KEEP ORIGINAL) ==================== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Fasilitas Dalam Angka</h2>
                <p class="text-gray-600">Komitmen kami dalam menyediakan fasilitas terbaik</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-primary/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                        </svg>
                    </div>
                    <p class="stat-number text-4xl font-bold text-gray-800 mb-2">20+</p>
                    <p class="text-gray-600">Ruang Belajar</p>
                </div>

                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto mb-4 bg-accent-orange/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-accent-orange" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="stat-number text-4xl font-bold text-gray-800 mb-2">20+</p>
                    <p class="text-gray-600">Alat Terapi</p>
                </div>

                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto mb-4 bg-accent-yellow/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-accent-yellow" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <p class="stat-number text-4xl font-bold text-gray-800 mb-2">3+</p>
                    <p class="text-gray-600">Area Bermain</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-secondary/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                        </svg>
                    </div>
                    <p class="stat-number text-4xl font-bold text-gray-800 mb-2">1000+</p>
                    <p class="text-gray-600">Koleksi Buku</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== CTA SECTION (KEEP ORIGINAL) ==================== -->
    <section class="py-20" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Ingin Melihat Fasilitas Kami Langsung?
            </h2>
            <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                Kunjungi kami dan lihat sendiri bagaimana fasilitas terbaik kami mendukung perkembangan putra-putri Anda
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/kontak') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Jadwalkan Kunjungan
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/ppdb') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-semibold rounded-full transition-all duration-300">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Component -->
    <x-footer></x-footer>

    <!-- ==================== CAROUSEL JAVASCRIPT ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==================== CAROUSEL FUNCTIONALITY ====================
            class Carousel {
                constructor(container) {
                    this.container = container;
                    this.wrapper = container.querySelector('.carousel-wrapper');
                    this.slides = container.querySelectorAll('.carousel-slide');
                    this.prevBtn = container.querySelector('.carousel-nav.prev');
                    this.nextBtn = container.querySelector('.carousel-nav.next');
                    this.indicators = container.querySelectorAll('.carousel-indicator');
                    this.currentIndex = 0;
                    this.autoPlayInterval = null;

                    this.init();
                }

                init() {
                    // Prev button
                    this.prevBtn.addEventListener('click', () => this.prev());

                    // Next button
                    this.nextBtn.addEventListener('click', () => this.next());

                    // Indicators
                    this.indicators.forEach((indicator, index) => {
                        indicator.addEventListener('click', () => this.goTo(index));
                    });

                    // Touch/Swipe support
                    this.addSwipeSupport();

                    // Auto play
                    this.startAutoPlay();

                    // Pause on hover
                    this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
                    this.container.addEventListener('mouseleave', () => this.startAutoPlay());
                }

                goTo(index) {
                    this.currentIndex = index;
                    this.updateCarousel();
                }

                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.slides.length;
                    this.updateCarousel();
                }

                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
                    this.updateCarousel();
                }

                updateCarousel() {
                    // Update wrapper position
                    this.wrapper.style.transform = `translateX(-${this.currentIndex * 100}%)`;

                    // Update indicators
                    this.indicators.forEach((indicator, index) => {
                        if (index === this.currentIndex) {
                            indicator.classList.add('active');
                        } else {
                            indicator.classList.remove('active');
                        }
                    });
                }

                startAutoPlay() {
                    this.autoPlayInterval = setInterval(() => this.next(), 5000); // Change slide every 5 seconds
                }

                stopAutoPlay() {
                    if (this.autoPlayInterval) {
                        clearInterval(this.autoPlayInterval);
                        this.autoPlayInterval = null;
                    }
                }

                addSwipeSupport() {
                    let startX = 0;
                    let endX = 0;

                    this.container.addEventListener('touchstart', (e) => {
                        startX = e.touches[0].clientX;
                    }, { passive: true });

                    this.container.addEventListener('touchmove', (e) => {
                        endX = e.touches[0].clientX;
                    }, { passive: true });

                    this.container.addEventListener('touchend', () => {
                        const diff = startX - endX;

                        if (Math.abs(diff) > 50) { // Minimum swipe distance
                            if (diff > 0) {
                                this.next();
                            } else {
                                this.prev();
                            }
                        }
                    });
                }
            }

            // Initialize all carousels
            const carousels = document.querySelectorAll('[data-carousel]');
            carousels.forEach(container => {
                new Carousel(container);
            });

            // ==================== SMOOTH SCROLL ====================
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');

                    if (targetId === '#') return;

                    const target = document.querySelector(targetId);
                    if (target) {
                        const offset = 100;
                        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // ==================== INTERSECTION OBSERVER FOR ANIMATIONS ====================
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe facility cards
            document.querySelectorAll('.carousel-container').forEach(card => {
                observer.observe(card);
            });

            // Observe gallery images
            document.querySelectorAll('.gallery-image').forEach(image => {
                observer.observe(image);
            });

            // ==================== NUMBER COUNTER ANIMATION ====================
            const animateCount = (element, target) => {
                const duration = 2000;
                const start = 0;
                const increment = target / (duration / 16);
                let current = start;

                const originalText = element.textContent;
                const hasPlus = originalText.includes('+');
                const hasSquare = originalText.includes('m²');

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target + (hasPlus ? '+' : '') + (hasSquare ? 'm²' : '');
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current) + (hasPlus ? '+' : '') + (hasSquare ? 'm²' : '');
                    }
                }, 16);
            };

            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statNumbers = entry.target.querySelectorAll('.stat-number');
                        statNumbers.forEach(stat => {
                            const text = stat.textContent;
                            const number = parseInt(text.replace(/\D/g, ''));
                            if (!isNaN(number)) {
                                stat.textContent = '0';
                                animateCount(stat, number);
                            }
                        });
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            const statsSection = document.querySelector('.py-20.bg-white');
            if (statsSection) {
                statsObserver.observe(statsSection);
            }

            // ==================== GALLERY LIGHTBOX ====================
            const galleryImages = document.querySelectorAll('.gallery-image');

            galleryImages.forEach(image => {
                image.addEventListener('click', function () {
                    const img = this.querySelector('img');
                    const lightbox = document.createElement('div');
                    lightbox.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4 cursor-pointer';
                    lightbox.style.animation = 'fadeIn 0.3s ease';

                    const lightboxImg = document.createElement('img');
                    lightboxImg.src = img.src;
                    lightboxImg.alt = img.alt;
                    lightboxImg.className = 'max-w-full max-h-full object-contain rounded-lg';
                    lightboxImg.style.animation = 'scaleIn 0.3s ease';

                    const closeBtn = document.createElement('button');
                    closeBtn.innerHTML = '×';
                    closeBtn.className = 'absolute top-4 right-4 text-white text-5xl font-light hover:text-gray-300 transition-colors';

                    lightbox.appendChild(lightboxImg);
                    lightbox.appendChild(closeBtn);
                    document.body.appendChild(lightbox);
                    document.body.style.overflow = 'hidden';

                    const closeLightbox = () => {
                        lightbox.style.animation = 'fadeOut 0.3s ease';
                        setTimeout(() => {
                            document.body.removeChild(lightbox);
                            document.body.style.overflow = 'auto';
                        }, 300);
                    };

                    lightbox.addEventListener('click', closeLightbox);
                    closeBtn.addEventListener('click', closeLightbox);
                    lightboxImg.addEventListener('click', (e) => e.stopPropagation());

                    document.addEventListener('keydown', function escapeHandler(e) {
                        if (e.key === 'Escape') {
                            closeLightbox();
                            document.removeEventListener('keydown', escapeHandler);
                        }
                    });
                });
            });

            // ==================== FEATURE ITEMS ANIMATION ====================
            const featureObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const featureItems = entry.target.querySelectorAll('.feature-item');
                        featureItems.forEach((item, index) => {
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'translateX(0)';
                            }, index * 100);
                        });
                        featureObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            document.querySelectorAll('.space-y-4').forEach(section => {
                featureObserver.observe(section);
            });

            // ==================== HERO STATS COUNTER ====================
            const heroStatsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const stats = entry.target.querySelectorAll('.text-4xl');
                        stats.forEach(stat => {
                            const text = stat.textContent;
                            const number = parseInt(text.replace(/\D/g, ''));
                            if (!isNaN(number)) {
                                let current = 0;
                                const increment = number / 50;
                                const timer = setInterval(() => {
                                    current += increment;
                                    if (current >= number) {
                                        stat.textContent = text;
                                        clearInterval(timer);
                                    } else {
                                        stat.textContent = Math.floor(current) + (text.includes('+') ? '+' : '');
                                    }
                                }, 30);
                            }
                        });
                        heroStatsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            const heroStatsGrid = document.querySelector('.grid.grid-cols-2.md\\:grid-cols-4');
            if (heroStatsGrid) {
                heroStatsObserver.observe(heroStatsGrid);
            }

            // ==================== SCROLL PROGRESS INDICATOR ====================
            const progressBar = document.createElement('div');
            progressBar.className = 'fixed top-0 left-0 h-1 bg-gradient-to-r from-primary to-secondary z-50 transition-all duration-300';
            progressBar.style.width = '0%';
            document.body.appendChild(progressBar);

            window.addEventListener('scroll', () => {
                const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (window.pageYOffset / windowHeight) * 100;
                progressBar.style.width = scrolled + '%';
            });

            // ==================== BACK TO TOP BUTTON ====================
            const backToTop = document.createElement('button');
            backToTop.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    `;
            backToTop.className = 'fixed bottom-8 right-8 w-14 h-14 bg-primary text-white rounded-full shadow-lg hover:bg-secondary transition-all duration-300 z-40 opacity-0 pointer-events-none flex items-center justify-center';
            document.body.appendChild(backToTop);

            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 500) {
                    backToTop.style.opacity = '1';
                    backToTop.style.pointerEvents = 'auto';
                } else {
                    backToTop.style.opacity = '0';
                    backToTop.style.pointerEvents = 'none';
                }
            });

            backToTop.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // ==================== ADD ANIMATIONS STYLE ====================
            if (!document.querySelector('#lightbox-animations')) {
                const style = document.createElement('style');
                style.id = 'lightbox-animations';
                style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
                document.head.appendChild(style);
            }

            console.log('PKBM House Of Knowledge - Facilities Page with Carousels Loaded Successfully');
        });
    </script>
</body>

</html>