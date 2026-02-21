<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB - PKBM House Of Knowledge</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.95) 0%, rgba(40, 127, 59, 0.9) 100%);
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
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

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .animate-slide-left {
            animation: slideInLeft 0.8s ease-out;
        }

        .animate-slide-right {
            animation: slideInRight 0.8s ease-out;
        }

        .animate-fade-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .step-connector {
            position: relative;
        }

        .step-connector::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, #165fac, #287f3b);
            transform: translateY(-50%);
            z-index: -1;
        }

        @media (max-width: 768px) {
            .step-connector::after {
                display: none;
            }
        }

        .requirement-check {
            transition: all 0.3s ease;
        }

        .requirement-check:hover {
            transform: translateX(5px);
        }

        html {
            scroll-behavior: smooth;
        }

        section {
            scroll-margin-top: 100px;
        }

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);
            color: white;
        }

        .tab-content {
            display: none;
            animation: fadeInUp 0.5s ease-out;
        }

        .tab-content.active {
            display: block;
        }

        .price-badge {
            position: relative;
            overflow: hidden;
        }

        .price-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .price-badge:hover::before {
            left: 100%;
        }

        .form-input {
            transition: all 0.3s ease;
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(22, 95, 172, 0.15);
        }
    </style>
</head>

<body class="bg-white">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $quickInfoSection = $page->getSection('quick_info');
        $quickInfoContent = $quickInfoSection->content ?? [];

        $alurSection = $page->getSection('alur');
        $alurContent = $alurSection->content ?? [];

        // Investasi & Biaya Sections
        $investasiSection = $page->getSection('investasi');
        $investasiContent = $investasiSection->content ?? [];

        $biayaPaudSection = $page->getSection('biaya_paud');
        $biayaPaudContent = $biayaPaudSection->content ?? [];
        $biayaPaudItems = $biayaPaudContent['items'] ?? [];

        $biayaSdSection = $page->getSection('biaya_sd');
        $biayaSdContent = $biayaSdSection->content ?? [];
        $biayaSdItems = $biayaSdContent['items'] ?? [];

        $biayaSmpSection = $page->getSection('biaya_smp');
        $biayaSmpContent = $biayaSmpSection->content ?? [];
        $biayaSmpItems = $biayaSmpContent['items'] ?? [];

        $biayaSmaSection = $page->getSection('biaya_sma');
        $biayaSmaContent = $biayaSmaSection->content ?? [];
        $biayaSmaItems = $biayaSmaContent['items'] ?? [];
    @endphp
    <x-navbar></x-navbar>

    <section class="relative min-h-screen flex items-center bg-cover bg-center bg-no-repeat"
        style="background-image: linear-gradient(135deg, rgba(22,95,172,0.75) 45%, rgba(40,127,59,0.75) 20%), url('{{ asset($heroContent['background_image'] ?? 'img/bg-ppdb.jpg') }}');">

        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg width=" 60"
            height="60" xmlns="http://www.w3.org/2000/svg">
            <path d="M30 0l30 30-30 30L0 30z" fill="white" /></svg>'); background-size: 60px 60px;">
        </div>

        <div
            class="absolute top-20 left-10 w-20 h-20 border-4 border-white/20 rounded-full float-animation hidden lg:block">
        </div>
        <div class="absolute bottom-20 right-20 w-16 h-16 bg-accent-yellow/30 rounded-full float-animation hidden lg:block"
            style="animation-delay: 1s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-20">
            <div class="text-center">
                <span
                    class="inline-block px-6 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-medium rounded-full mb-6 animate-fade-up">
                    <i class="fas fa-books"></i> {{ $heroContent['tahun_ajaran'] ?? 'Tahun Ajaran 2025/2026' }}
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-up"
                    style="animation-delay: 0.1s;">
                    {{ $heroContent['title'] ?? 'Penerimaan Peserta' }}<br>
                    <span class="text-accent-bright">{{ $heroContent['title_highlight'] ?? 'Didik Baru' }}</span>
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-8 max-w-2xl mx-auto animate-fade-up"
                    style="animation-delay: 0.2s;">
                    {{ $heroContent['subtitle'] ?? 'Bergabunglah bersama kami dan raih masa depan yang cerah melalui pendidikan berkualitas' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-up"
                    style="animation-delay: 0.3s;">
                    <a href="{{ url($heroContent['cta_link'] ?? '/kontak') }}"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full shadow-lg transition-all duration-300 hover:-translate-y-1">
                        {{ $heroContent['cta_text'] ?? 'Daftar Sekarang' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#alur"
                        class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-semibold rounded-full transition-all duration-300">
                        Lihat Panduan
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- Quick Info Banner -->
    <section class="relative -mt-10 z-20 pb-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gradient-to-br from-primary/10 to-primary/5 rounded-2xl">
                        <div class="text-3xl mb-2"><i class="fas fa-calendar"></i></div>
                        <p class="text-sm text-gray-600 mb-1">
                            {{ $quickInfoContent['periode_label'] ?? 'Periode Pendaftaran' }}
                        </p>
                        <p class="text-lg font-bold text-gray-800">
                            {{ $quickInfoContent['periode_value'] ?? '1 Jan - 31 Mei 2026' }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-2xl">
                        <div class="text-3xl mb-2"><i class="fas fa-money-bill-wave"></i></div>
                        <p class="text-sm text-gray-600 mb-1">
                            {{ $quickInfoContent['biaya_label'] ?? 'Biaya Pendaftaran' }}
                        </p>
                        <p class="text-lg font-bold text-gray-800">{{ $quickInfoContent['biaya_value'] ?? '200 Ribu' }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-accent-orange/10 to-accent-orange/5 rounded-2xl">
                        <div class="text-3xl mb-2"><i class="fas fa-graduation-cap"></i></div>
                        <p class="text-sm text-gray-600 mb-1">{{ $quickInfoContent['kuota_label'] ?? 'Kuota Tersedia' }}
                        </p>
                        <p class="text-lg font-bold text-gray-800">{{ $quickInfoContent['kuota_value'] ?? '100 Siswa' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Pendaftaran -->
    <section id="alur" class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    {{ $alurContent['badge'] ?? 'Langkah Mudah' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $alurContent['title'] ?? 'Alur Pendaftaran' }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $alurContent['description'] ?? 'Ikuti 5 langkah mudah untuk mendaftar sebagai peserta didik baru' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-8 relative">
                <!-- Step 1 -->
                <div class="step-connector relative">
                    <div class="card-hover bg-white rounded-2xl shadow-lg p-6 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            1
                        </div>
                        <div class="w-12 h-12 mx-auto mb-4 bg-primary/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Isi Formulir</h3>
                        <p class="text-sm text-gray-600">Lengkapi formulir pendaftaran online dengan data yang benar</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-connector relative">
                    <div class="card-hover bg-white rounded-2xl shadow-lg p-6 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            2
                        </div>
                        <div class="w-12 h-12 mx-auto mb-4 bg-secondary/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Upload Dokumen</h3>
                        <p class="text-sm text-gray-600">Unggah berkas persyaratan yang diperlukan</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-connector relative">
                    <div class="card-hover bg-white rounded-2xl shadow-lg p-6 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            3
                        </div>
                        <div
                            class="w-12 h-12 mx-auto mb-4 bg-accent-yellow/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent-yellow" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Verifikasi</h3>
                        <p class="text-sm text-gray-600">Tim kami akan memverifikasi data dan dokumen Anda</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-connector relative">
                    <div class="card-hover bg-white rounded-2xl shadow-lg p-6 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            4
                        </div>
                        <div
                            class="w-12 h-12 mx-auto mb-4 bg-accent-orange/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent-orange" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Wawancara</h3>
                        <p class="text-sm text-gray-600">Ikuti sesi wawancara singkat dengan tim kami</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="relative">
                    <div class="card-hover bg-white rounded-2xl shadow-lg p-6 text-center border-2 border-secondary">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-secondary to-primary rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            5
                        </div>
                        <div
                            class="w-12 h-12 mx-auto mb-4 bg-accent-bright/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Pengumuman</h3>
                        <p class="text-sm text-gray-600">Terima pengumuman hasil dan mulai belajar!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Syarat Pendaftaran -->
    <section id="syarat" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-2 bg-secondary/10 text-secondary text-sm font-medium rounded-full mb-4">
                    Persyaratan
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Syarat Pendaftaran
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Siapkan dokumen-dokumen berikut untuk melengkapi pendaftaran Anda
                </p>
            </div>

            <!-- Tabs -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button class="tab-button active px-6 py-3 rounded-full font-semibold bg-gray-100" data-tab="paud">
                    PAUD
                </button>
                <button class="tab-button px-6 py-3 rounded-full font-semibold bg-gray-100" data-tab="paket-a">
                    SD (Paket A)
                </button>
                <button class="tab-button px-6 py-3 rounded-full font-semibold bg-gray-100" data-tab="paket-b">
                    SMP (Paket B)
                </button>
                <button class="tab-button px-6 py-3 rounded-full font-semibold bg-gray-100" data-tab="paket-c">
                    SMA (Paket C)
                </button>
                <button class="tab-button px-6 py-3 rounded-full font-semibold bg-gray-100" data-tab="inklusi">
                    Pendidikan Inklusi
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="max-w-4xl mx-auto">
                <!-- PAUD -->
                <div class="tab-content active" id="paud">
                    <div class="bg-gradient-to-br from-accent-yellow/10 to-accent-bright/10 rounded-3xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center text-white mr-3"><i
                                    class="fas fa-book"></i></span>
                            Syarat PAUD
                        </h3>
                        <div class="space-y-4">
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Akta Kelahiran (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Kartu Keluarga (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy KTP Orang Tua (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Pas foto anak 3x4 (4 lembar, background merah)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Usia minimal 3 tahun</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket A -->
                <div class="tab-content" id="paket-a">
                    <div class="bg-gradient-to-br from-primary/10 to-primary/5 rounded-3xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white mr-3"><i
                                    class="fas fa-graduation-cap"></i></span>
                            Syarat Paket A (Setara SD)
                        </h3>
                        <div class="space-y-4">
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Ijazah PAUD/TK atau Surat Keterangan (2
                                    lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Akta Kelahiran (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Kartu Keluarga (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy KTP Orang Tua (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Pas foto 3x4 (6 lembar, background merah)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Usia minimal 7 tahun atau maksimal 12 tahun</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket B -->
                <div class="tab-content" id="paket-b">
                    <div class="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-3xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white mr-3"><i
                                    class="fas fa-book-open"></i></span>
                            Syarat Paket B (Setara SMP)
                        </h3>
                        <div class="space-y-4">
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Ijazah SD/Paket A (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy SKHUN SD (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Akta Kelahiran (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Kartu Keluarga (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy KTP atau KTP Orang Tua (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Pas foto 3x4 (6 lembar, background biru)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paket C -->
                <div class="tab-content" id="paket-c">
                    <div class="bg-gradient-to-br from-accent-orange/10 to-accent-orange/5 rounded-3xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-accent-orange rounded-full flex items-center justify-center text-white mr-3"><i
                                    class="fas fa-bullseye"></i></span>
                            Syarat Paket C (Setara SMA)
                        </h3>
                        <div class="space-y-4">
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Ijazah SMP/Paket B (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy SKHUN SMP (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Akta Kelahiran (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy Kartu Keluarga (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Fotocopy KTP Peserta Didik (2 lembar)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Pas foto 3x4 (6 lembar, background merah)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Pilih jurusan: IPA atau IPS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inklusi -->
                <div class="tab-content" id="inklusi">
                    <div class="bg-gradient-to-br from-purple-100 to-pink-50 rounded-3xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <span
                                class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white mr-3"><i
                                    class="fas fa-heart"></i></span>
                            Syarat Pendidikan Inklusi
                        </h3>
                        <div class="space-y-4">
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Persyaratan dokumen sesuai jenjang yang diambil</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Surat keterangan dari dokter/psikolog (jika ada)</span>
                            </div>
                            <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                <div
                                    class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">Asesmen awal kemampuan peserta didik</span>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl">
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    <strong>Catatan:</strong> Pendidikan inklusi kami dirancang untuk memberikan
                                    kesempatan belajar yang setara bagi anak berkebutuhan khusus. Kami menyediakan
                                    pendampingan khusus dan kurikulum yang disesuaikan dengan kebutuhan setiap peserta
                                    didik.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Biaya Section -->
    <section id="biaya" class="py-20 bg-gradient-to-br from-cream to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-2 bg-accent-orange/10 text-accent-orange text-sm font-medium rounded-full mb-4">
                    {{ $investasiContent['badge'] ?? 'Investasi Pendidikan' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $investasiContent['title'] ?? 'Detail Biaya Pendidikan' }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $investasiContent['description'] ?? 'Biaya terjangkau dengan kualitas pendidikan terbaik' }}
                </p>
            </div>

            @php
                $colorMap = [
                    'yellow' => ['border' => 'border-accent-yellow', 'bg' => 'bg-accent-yellow/10', 'gradient' => 'from-accent-yellow to-accent-bright'],
                    'blue' => ['border' => 'border-primary', 'bg' => 'bg-primary/10', 'gradient' => 'from-primary to-blue-600'],
                    'green' => ['border' => 'border-secondary', 'bg' => 'bg-secondary/10', 'gradient' => 'from-secondary to-green-600'],
                    'orange' => ['border' => 'border-accent-orange', 'bg' => 'bg-accent-orange/10', 'gradient' => 'from-accent-orange to-red-600'],
                ];
                $iconMap = [
                    'yellow' => 'fa-palette',
                    'blue' => 'fa-book',
                    'green' => 'fa-book-open',
                    'orange' => 'fa-graduation-cap',
                ];
                $biayaSections = [
                    ['content' => $biayaPaudContent, 'items' => $biayaPaudItems, 'modalId' => 'costModal', 'modalFunc' => 'showCostModal'],
                    ['content' => $biayaSdContent, 'items' => $biayaSdItems, 'modalId' => 'costModalPaketA', 'modalFunc' => 'showCostModalPaketA'],
                    ['content' => $biayaSmpContent, 'items' => $biayaSmpItems, 'modalId' => 'costModalPaketB', 'modalFunc' => 'showCostModalPaketB'],
                    ['content' => $biayaSmaContent, 'items' => $biayaSmaItems, 'modalId' => 'costModalPaketC', 'modalFunc' => 'showCostModalPaketC'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($biayaSections as $section)
                    @php
                        $header = $section['content']['header'] ?? [];
                        $items = $section['items'] ?? [];
                        $color = $header['color'] ?? 'blue';
                        $borderClass = $colorMap[$color]['border'] ?? 'border-primary';
                        $bgClass = $colorMap[$color]['bg'] ?? 'bg-primary/10';
                        $gradientClass = $colorMap[$color]['gradient'] ?? 'from-primary to-blue-600';
                        $icon = $iconMap[$color] ?? 'fa-book';
                        $image = $header['image'] ?? null;

                        // Calculate totals from items
                        $pokokItems = collect($items)->where('type', 'pokok');
                        $tambahanItems = collect($items)->where('type', 'tambahan');
                    @endphp
                    <div class="card-hover bg-white rounded-3xl shadow-xl p-8 border-t-4 {{ $borderClass }}">
                        <div class="text-center mb-6">
                            <div
                                class="w-16 h-16 mx-auto mb-4 {{ $bgClass }} rounded-full flex items-center justify-center overflow-hidden">
                                @if($image)
                                    <img src="{{ asset($image) }}" alt="{{ $header['title'] ?? 'Icon' }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-3xl"><i class="fas {{ $icon }}"></i></span>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $header['title'] ?? 'Program' }}</h3>
                            <p class="text-sm text-gray-600">{{ $header['subtitle'] ?? '' }}</p>
                        </div>
                        <div class="space-y-4 mb-6">
                            @foreach($pokokItems as $index => $item)
                                <div
                                    class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-100' }}">
                                    <span class="text-gray-600">{{ $item['name'] ?? '' }}</span>
                                    <span
                                        class="font-semibold {{ $index == 0 ? 'text-secondary' : 'text-gray-800' }}">{{ $item['price'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="price-badge bg-gradient-to-r {{ $gradientClass }} text-white text-center py-3 rounded-xl font-bold cursor-pointer"
                            onclick="{{ $section['modalFunc'] }}()">
                            {{ $header['badge_text'] ?? 'Lihat Detail' }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 bg-gradient-to-r from-primary/10 to-secondary/10 rounded-3xl p-8 text-center">
                <h3 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-lightbulb"></i> Informasi Penting
                </h3>
                <div class="grid md:grid-cols-3 gap-6 text-left">
                    <div class="bg-white rounded-xl p-6">
                        <div class="text-2xl mb-2"><i class="fas fa-check-circle"></i></div>
                        <h4 class="font-bold text-gray-800 mb-2">Pendaftaran 200rb </h4>
                        <p class="text-sm text-gray-600">Biaya Pendaftaran Mulai Dari 200 Ribu Untuk Semua Jenjang
                            Pendidikan</p>
                    </div>
                    <div class="bg-white rounded-xl p-6">
                        <div class="text-2xl mb-2"><i class="fas fa-credit-card"></i></div>
                        <h4 class="font-bold text-gray-800 mb-2">Cicilan Tersedia</h4>
                        <p class="text-sm text-gray-600">Pembayaran dapat dicicil setiap bulan untuk memudahkan orang
                            tua</p>
                    </div>
                    <div class="bg-white rounded-xl p-6">
                        <div class="text-2xl mb-2"><i class="fas fa-gift"></i></div>
                        <h4 class="font-bold text-gray-800 mb-2">Beasiswa</h4>
                        <p class="text-sm text-gray-600">Tersedia program beasiswa untuk siswa berprestasi dan kurang
                            mampu</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulir Section
    <section id="formulir" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    Daftar Sekarang
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Formulir Pendaftaran Online
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Lengkapi formulir di bawah ini untuk memulai proses pendaftaran
                </p>
            </div>

            <form id="registrationForm" class="bg-gradient-to-br from-cream/50 to-white rounded-3xl shadow-2xl p-8 md:p-12"> -->
    <!-- Data Peserta Didik
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 text-sm">1</span>
                        Data Peserta Didik
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Masukkan nama lengkap">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin *</label>
                            <select required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tempat Lahir *</label>
                            <input type="text" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Kota tempat lahir">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lahir *</label>
                            <input type="date" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap *</label>
                            <textarea required rows="3" class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Alamat lengkap sesuai KTP"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon/HP *</label>
                            <input type="tel" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="08xx-xxxx-xxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="email@contoh.com">
                        </div>
                    </div>
                </div> -->

    <!-- Pilihan Program
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-white mr-3 text-sm">2</span>
                        Pilihan Program
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenjang Pendidikan *</label>
                            <select id="jenjangSelect" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                                <option value="">Pilih jenjang</option>
                                <option value="paud">PAUD</option>
                                <option value="paket-a">Paket A (Setara SD)</option>
                                <option value="paket-b">Paket B (Setara SMP)</option>
                                <option value="paket-c">Paket C (Setara SMA)</option>
                                <option value="inklusi">Pendidikan Inklusi</option>
                            </select>
                        </div>
                        <div id="jurusanField" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilihan Jurusan (Paket C) *</label>
                            <select class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                                <option value="">Pilih jurusan</option>
                                <option value="ipa">IPA</option>
                                <option value="ips">IPS</option>
                            </select>
                        </div>
                    </div>
                </div> -->

    <!-- Data Orang Tua
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-accent-orange rounded-full flex items-center justify-center text-white mr-3 text-sm">3</span>
                        Data Orang Tua/Wali
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Ayah/Wali *</label>
                            <input type="text" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Nama lengkap ayah/wali">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Ibu *</label>
                            <input type="text" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Nama lengkap ibu">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pekerjaan Ayah/Wali *</label>
                            <input type="text" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Pekerjaan ayah/wali">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pekerjaan Ibu *</label>
                            <input type="text" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Pekerjaan ibu">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP Orang Tua *</label>
                            <input type="tel" required class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="08xx-xxxx-xxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Penghasilan Orang Tua/Bulan</label>
                            <select class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none">
                                <option value="">Pilih range penghasilan</option>
                                <option value="< 1jt">< Rp 1.000.000</option>
                                <option value="1-3jt">Rp 1.000.000 - Rp 3.000.000</option>
                                <option value="3-5jt">Rp 3.000.000 - Rp 5.000.000</option>
                                <option value="> 5jt">> Rp 5.000.000</option>
                            </select>
                        </div>
                    </div>
                </div> -->

    <!-- Informasi Tambahan
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center text-white mr-3 text-sm">4</span>
                        Informasi Tambahan
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Asal Sekolah/Lembaga Sebelumnya</label>
                            <input type="text" class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Nama sekolah/lembaga terakhir">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motivasi Mendaftar</label>
                            <textarea rows="4" class="form-input w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:outline-none" placeholder="Ceritakan motivasi Anda mendaftar di PKBM House Of Knowledge"></textarea>
                        </div>
                        <div class="flex items-start gap-3">
                            <input type="checkbox" required id="agreement" class="mt-1 w-5 h-5 text-primary border-2 border-gray-300 rounded focus:ring-primary">
                            <label for="agreement" class="text-sm text-gray-700">
                                Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan. Saya bersedia mengikuti seluruh proses seleksi dan aturan yang berlaku di PKBM House Of Knowledge. *
                            </label>
                        </div>
                    </div>
                </div> -->

    <!-- Submit Button
                <div class="text-center">
                    <button type="submit" class="inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kirim Pendaftaran
                    </button>
                    <p class="text-sm text-gray-600 mt-4">* Wajib diisi</p>
                </div>
            </form>
        </div>
    </section> -->

    <!-- Success Modal
    <div id="successModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 text-center animate-fade-up">
            <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Pendaftaran Berhasil!</h3>
            <p class="text-gray-600 mb-6">Terima kasih telah mendaftar. Tim kami akan segera menghubungi Anda untuk proses selanjutnya.</p>
            <button onclick="closeModal()" class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-full hover:shadow-lg transition-all">
                Tutup
            </button>
        </div>
    </div> -->

    <!-- Cost Detail Modal PAUD-->
    @php
        $paudPokokItems = collect($biayaPaudItems)->where('type', 'pokok');
        $paudTambahanItems = collect($biayaPaudItems)->where('type', 'tambahan');
        $paudPokokTotal = 0;
        $paudTambahanTotal = 0;
        foreach ($paudPokokItems as $item) {
            $paudPokokTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
        foreach ($paudTambahanItems as $item) {
            $paudTambahanTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
    @endphp
    <div id="costModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 animate-fade-up max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span
                        class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center text-white mr-3 overflow-hidden">
                        @if(isset($biayaPaudContent['header']['image']) && $biayaPaudContent['header']['image'])
                            <img src="{{ asset($biayaPaudContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-money-bill-wave"></i>
                        @endif
                    </span>
                    Rincian Biaya {{ $biayaPaudContent['header']['title'] ?? 'PAUD' }}
                </h3>
                <button onclick="closeCostModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Biaya Pokok -->
                <div class="bg-gradient-to-r from-accent-yellow/10 to-accent-bright/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-book"></i></span>
                        Biaya Pokok Pendidikan
                    </h4>
                    <div class="space-y-3">
                        @foreach($paudPokokItems as $item)
                            <div
                                class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span
                                    class="font-semibold {{ $loop->first ? 'text-secondary' : 'text-gray-800' }}">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-accent-yellow/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Pokok</span>
                            <span class="font-bold text-accent-yellow">Rp
                                {{ number_format($paudPokokTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Biaya Tambahan -->
                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-palette"></i></span>
                        Biaya Tambahan (Opsional)
                    </h4>
                    <div class="space-y-3">
                        @foreach($paudTambahanItems as $item)
                            <div
                                class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-gray-800">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-primary/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Tambahan</span>
                            <span class="font-bold text-primary">Rp
                                {{ number_format($paudTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total Keseluruhan -->
                <div
                    class="bg-gradient-to-r from-secondary/10 to-accent-orange/10 rounded-2xl p-6 border-2 border-secondary">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white mr-3"><i
                                class="fas fa-diamond"></i></span>
                        Estimasi Total Biaya
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Pokok</span>
                            <span class="font-semibold text-gray-800">Rp
                                {{ number_format($paudPokokTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Tambahan</span>
                            <span class="font-semibold text-gray-800">Rp
                                {{ number_format($paudTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-3 bg-secondary/10 rounded-lg px-3 border-2 border-secondary">
                            <span class="text-lg font-bold text-gray-800">Total Estimasi</span>
                            <span class="text-lg font-bold text-secondary">Rp
                                {{ number_format($paudPokokTotal + $paudTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h5 class="font-bold text-gray-800 mb-2"><i class="fas fa-lightbulb"></i> Informasi Penting:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>• Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>• Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>• Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
                    </ul>
                </div>

                <div class="text-center">
                    <button onclick="closeCostModal()"
                        class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-full hover:shadow-lg transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Detail Modal SMA (Paket C) -->
    @php
        $smaPokokItems = collect($biayaSmaItems)->where('type', 'pokok');
        $smaTambahanItems = collect($biayaSmaItems)->where('type', 'tambahan');
        $smaPokokTotal = 0;
        $smaTambahanTotal = 0;
        foreach ($smaPokokItems as $item) {
            $smaPokokTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
        foreach ($smaTambahanItems as $item) {
            $smaTambahanTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
    @endphp
    <div id="costModalPaketC"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 animate-fade-up max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span
                        class="w-10 h-10 bg-accent-orange rounded-full flex items-center justify-center text-white mr-3 overflow-hidden">
                        @if(isset($biayaSmaContent['header']['image']) && $biayaSmaContent['header']['image'])
                            <img src="{{ asset($biayaSmaContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-money-bill-wave"></i>
                        @endif
                    </span>
                    Rincian Biaya {{ $biayaSmaContent['header']['title'] ?? 'SMA' }}
                </h3>
                <button onclick="closeCostModalPaketC()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Biaya Pokok -->
                <div class="bg-gradient-to-r from-accent-orange/10 to-red-600/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-accent-orange rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-book"></i></span>
                        Biaya Pokok Pendidikan
                    </h4>
                    <div class="space-y-3">
                        @foreach($smaPokokItems as $item)
                            <div class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-secondary">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-accent-yellow/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Pokok</span>
                            <span class="font-bold text-accent-yellow">Rp {{ number_format($smaPokokTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Biaya Tambahan -->
                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-graduation-cap"></i></span>
                        Biaya Tambahan (Opsional)
                    </h4>
                    <div class="space-y-3">
                        @foreach($smaTambahanItems as $item)
                            <div class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-gray-800">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-primary/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Tambahan</span>
                            <span class="font-bold text-primary">Rp {{ number_format($smaTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total Keseluruhan -->
                <div
                    class="bg-gradient-to-r from-secondary/10 to-accent-orange/10 rounded-2xl p-6 border-2 border-secondary">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white mr-3"><i
                                class="fas fa-diamond"></i></span>
                        Estimasi Total Biaya
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Pokok</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($smaPokokTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Tambahan</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($smaTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-3 bg-secondary/10 rounded-lg px-3 border-2 border-secondary">
                            <span class="text-lg font-bold text-gray-800">Total Estimasi</span>
                            <span class="text-lg font-bold text-secondary">Rp {{ number_format($smaPokokTotal + $smaTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h5 class="font-bold text-gray-800 mb-2"><i class="fas fa-lightbulb"></i> Informasi Penting:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>• Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>• Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>• Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
                    </ul>
                </div>

                <div class="text-center">
                    <button onclick="closeCostModalPaketC()"
                        class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-full hover:shadow-lg transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Detail Modal SD (Paket A) -->
    @php
        $sdPokokItems = collect($biayaSdItems)->where('type', 'pokok');
        $sdTambahanItems = collect($biayaSdItems)->where('type', 'tambahan');
        $sdPokokTotal = 0;
        $sdTambahanTotal = 0;
        foreach ($sdPokokItems as $item) {
            $sdPokokTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
        foreach ($sdTambahanItems as $item) {
            $sdTambahanTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
    @endphp
    <div id="costModalPaketA"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 animate-fade-up max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white mr-3 overflow-hidden">
                        @if(isset($biayaSdContent['header']['image']) && $biayaSdContent['header']['image'])
                            <img src="{{ asset($biayaSdContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-money-bill-wave"></i>
                        @endif
                    </span>
                    Rincian Biaya {{ $biayaSdContent['header']['title'] ?? 'SD' }}
                </h3>
                <button onclick="closeCostModalPaketA()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Biaya Pokok -->
                <div class="bg-gradient-to-r from-primary/10 to-blue-600/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-book"></i></span>
                        Biaya Pokok Pendidikan
                    </h4>
                    <div class="space-y-3">
                        @foreach($sdPokokItems as $item)
                            <div class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-secondary">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-accent-yellow/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Pokok</span>
                            <span class="font-bold text-accent-yellow">Rp {{ number_format($sdPokokTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Biaya Tambahan -->
                <div class="bg-gradient-to-r from-secondary/10 to-green-600/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-graduation-cap"></i></span>
                        Biaya Tambahan (Opsional)
                    </h4>
                    <div class="space-y-3">
                        @foreach($sdTambahanItems as $item)
                            <div class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-gray-800">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-primary/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Tambahan</span>
                            <span class="font-bold text-primary">Rp {{ number_format($sdTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total Keseluruhan -->
                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-2xl p-6 border-2 border-primary">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white mr-3"><i
                                class="fas fa-diamond"></i></span>
                        Estimasi Total Biaya
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Pokok</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($sdPokokTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Tambahan</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($sdTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-3 bg-primary/10 rounded-lg px-3 border-2 border-primary">
                            <span class="text-lg font-bold text-gray-800">Total Estimasi</span>
                            <span class="text-lg font-bold text-primary">Rp {{ number_format($sdPokokTotal + $sdTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h5 class="font-bold text-gray-800 mb-2"><i class="fas fa-lightbulb"></i> Informasi Penting:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>• Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>• Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>• Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
                    </ul>
                </div>

                <div class="text-center">
                    <button onclick="closeCostModalPaketA()"
                        class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-full hover:shadow-lg transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Detail Modal SMP (Paket B) -->
    @php
        $smpPokokItems = collect($biayaSmpItems)->where('type', 'pokok');
        $smpTambahanItems = collect($biayaSmpItems)->where('type', 'tambahan');
        $smpPokokTotal = 0;
        $smpTambahanTotal = 0;
        foreach ($smpPokokItems as $item) {
            $smpPokokTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
        foreach ($smpTambahanItems as $item) {
            $smpTambahanTotal += (int) preg_replace('/[^0-9]/', '', $item['price'] ?? '0');
        }
    @endphp
    <div id="costModalPaketB"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 animate-fade-up max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span
                        class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white mr-3 overflow-hidden">
                        @if(isset($biayaSmpContent['header']['image']) && $biayaSmpContent['header']['image'])
                            <img src="{{ asset($biayaSmpContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-money-bill-wave"></i>
                        @endif
                    </span>
                    Rincian Biaya {{ $biayaSmpContent['header']['title'] ?? 'SMP' }}
                </h3>
                <button onclick="closeCostModalPaketB()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Biaya Pokok -->
                <div class="bg-gradient-to-r from-secondary/10 to-green-600/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-book"></i></span>
                        Biaya Pokok Pendidikan
                    </h4>
                    <div class="space-y-3">
                        @foreach($smpPokokItems as $item)
                            <div class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-secondary">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-secondary/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Pokok</span>
                            <span class="font-bold text-secondary">Rp {{ number_format($smpPokokTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Biaya Tambahan -->
                <div class="bg-gradient-to-r from-primary/10 to-blue-600/10 rounded-2xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white mr-3 text-sm"><i
                                class="fas fa-book-open"></i></span>
                        Biaya Tambahan (Opsional)
                    </h4>
                    <div class="space-y-3">
                        @foreach($smpTambahanItems as $item)
                            <div class="flex justify-between items-center py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                                <span class="text-gray-700">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold text-gray-800">{{ $item['price'] ?? '' }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 bg-primary/5 rounded-lg px-3">
                            <span class="font-bold text-gray-800">Total Biaya Tambahan</span>
                            <span class="font-bold text-primary">Rp {{ number_format($smpTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total Keseluruhan -->
                <div class="bg-gradient-to-r from-secondary/10 to-primary/10 rounded-2xl p-6 border-2 border-secondary">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span
                            class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white mr-3"><i
                                class="fas fa-diamond"></i></span>
                        Estimasi Total Biaya
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Pokok</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($smpPokokTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-300">
                            <span class="text-gray-700">Biaya Tambahan</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($smpTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-3 bg-secondary/10 rounded-lg px-3 border-2 border-secondary">
                            <span class="text-lg font-bold text-gray-800">Total Estimasi</span>
                            <span class="text-lg font-bold text-secondary">Rp {{ number_format($smpPokokTotal + $smpTambahanTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h5 class="font-bold text-gray-800 mb-2"><i class="fas fa-lightbulb"></i> Informasi Penting:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>• Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>• Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>• Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
                    </ul>
                </div>

                <div class="text-center">
                    <button onclick="closeCostModalPaketB()"
                        class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-full hover:shadow-lg transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-footer></x-footer>

    <script>
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Show/hide jurusan field based on jenjang selection
        const jenjangSelect = document.getElementById('jenjangSelect');
        const jurusanField = document.getElementById('jurusanField');

        jenjangSelect.addEventListener('change', function () {
            if (this.value === 'paket-c') {
                jurusanField.classList.remove('hidden');
            } else {
                jurusanField.classList.add('hidden');
            }
        });

        // Form submission
        const form = document.getElementById('registrationForm');
        const modal = document.getElementById('successModal');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Show success modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Reset form
            form.reset();
            jurusanField.classList.add('hidden');

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Add scroll effect to navbar
        const navbar = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });

        // Cost modal functions
        function showCostModal() {
            const costModal = document.getElementById('costModal');
            costModal.classList.remove('hidden');
            costModal.classList.add('flex');
        }

        function closeCostModal() {
            const costModal = document.getElementById('costModal');
            costModal.classList.add('hidden');
            costModal.classList.remove('flex');
        }

        function showCostModalPaketA() {
            const costModal = document.getElementById('costModalPaketA');
            costModal.classList.remove('hidden');
            costModal.classList.add('flex');
        }

        function closeCostModalPaketA() {
            const costModal = document.getElementById('costModalPaketA');
            costModal.classList.add('hidden');
            costModal.classList.remove('flex');
        }

        function showCostModalPaketB() {
            const costModal = document.getElementById('costModalPaketB');
            costModal.classList.remove('hidden');
            costModal.classList.add('flex');
        }

        function closeCostModalPaketB() {
            const costModal = document.getElementById('costModalPaketB');
            costModal.classList.add('hidden');
            costModal.classList.remove('flex');
        }

        function showCostModalPaketC() {
            const costModal = document.getElementById('costModalPaketC');
            costModal.classList.remove('hidden');
            costModal.classList.add('flex');
        }

        function closeCostModalPaketC() {
            const costModal = document.getElementById('costModalPaketC');
            costModal.classList.add('hidden');
            costModal.classList.remove('flex');
        }
    </script>

</body>

</html>