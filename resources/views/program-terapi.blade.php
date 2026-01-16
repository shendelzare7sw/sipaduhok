<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Terapi - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-overlay {
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
        $aboutSection = $page->getSection('about');
        $aboutContent = $aboutSection->content ?? [];
        $therapySection = $page->getSection('therapy_types');
        $therapyContent = $therapySection->content ?? [];
        $therapyHeader = $therapyContent['header'] ?? [];
        $therapyItems = $therapyContent['items'] ?? [];

        // Color mapping
        $colorMap = [
            'orange' => ['border' => '#d45930', 'bg' => '#d45930', 'text' => '#d45930'],
            'blue' => ['border' => '#165fac', 'bg' => '#165fac', 'text' => '#165fac'],
            'green' => ['border' => '#287f3b', 'bg' => '#287f3b', 'text' => '#287f3b'],
            'yellow' => ['border' => '#fac030', 'bg' => '#fac030', 'text' => '#fac030'],
        ];
    @endphp

    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Program</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Program Terapi' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Program Terapi' }}</h1>
            <p class="mt-4 text-lg text-white/90">
                {{ $heroContent['subtitle'] ?? 'Layanan Terapi Profesional untuk Tumbuh Kembang Anak' }}
            </p>
        </div>
    </section>

    <!-- About -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-block bg-[#287f3b]/20 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $aboutContent['badge'] ?? 'Program Terapi' }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        {{ $aboutContent['title'] ?? 'Layanan Terapi Profesional' }}
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_1'] ?? 'Program Terapi kami menyediakan berbagai layanan terapi untuk mendukung tumbuh kembang anak. Setiap sesi terapi dilakukan oleh tenaga profesional yang berpengalaman dan tersertifikasi.' }}
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_2'] ?? 'Kami menyediakan ruang terapi yang nyaman dan dilengkapi dengan peralatan modern untuk memaksimalkan hasil terapi.' }}
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-[#287f3b]">{{ $aboutContent['stat_1_value'] ?? '5+' }}</p>
                            <p class="text-gray-600 text-sm">{{ $aboutContent['stat_1_label'] ?? 'Jenis Terapi' }}</p>
                        </div>
                        <div class="w-px h-12 bg-gray-300"></div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-[#165fac]">{{ $aboutContent['stat_2_value'] ?? '10+' }}
                            </p>
                            <p class="text-gray-600 text-sm">{{ $aboutContent['stat_2_label'] ?? 'Terapis Ahli' }}</p>
                        </div>
                        <div class="w-px h-12 bg-gray-300"></div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-[#d45930]">{{ $aboutContent['stat_3_value'] ?? '500+' }}
                            </p>
                            <p class="text-gray-600 text-sm">{{ $aboutContent['stat_3_label'] ?? 'Klien Terbantu' }}</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ asset($aboutContent['image'] ?? 'img/terapi-img.jpg') }}" alt="Program Terapi"
                        class="rounded-2xl shadow-xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Jenis Terapi -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $therapyHeader['title'] ?? 'Jenis Layanan Terapi' }}</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $therapyHeader['description'] ?? 'Berbagai layanan terapi profesional untuk kebutuhan anak' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Terapi Wicara -->
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-48 bg-[#165fac]/10 flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#165fac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Terapi Wicara</h3>
                        <p class="text-gray-600 text-sm mb-4">Membantu anak mengembangkan kemampuan berbicara, bahasa,
                            dan komunikasi secara efektif.</p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Artikulasi dan pengucapan
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Pengembangan bahasa
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Komunikasi sosial
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Terapi Okupasi -->
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-48 bg-[#287f3b]/10 flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#287f3b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Terapi Okupasi</h3>
                        <p class="text-gray-600 text-sm mb-4">Mengembangkan keterampilan motorik halus dan kemandirian
                            dalam aktivitas sehari-hari.</p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Motorik halus
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Kemandirian (ADL)
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Koordinasi tangan-mata
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Terapi Sensori Integrasi -->
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-48 bg-[#d45930]/10 flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#d45930]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Sensori Integrasi</h3>
                        <p class="text-gray-600 text-sm mb-4">Membantu anak memproses dan merespons informasi sensorik
                            dengan lebih baik.</p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Pengolahan sensorik
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Regulasi emosi
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Keseimbangan tubuh
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Terapi Perilaku -->
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-48 bg-[#fac030]/10 flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#fac030]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Terapi Perilaku (ABA)</h3>
                        <p class="text-gray-600 text-sm mb-4">Applied Behavior Analysis untuk mengembangkan perilaku
                            positif dan mengurangi perilaku yang tidak diinginkan.</p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Modifikasi perilaku
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Keterampilan sosial
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Penguatan positif
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Fisioterapi -->
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-48 bg-[#165fac]/10 flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#165fac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Fisioterapi</h3>
                        <p class="text-gray-600 text-sm mb-4">Meningkatkan kemampuan motorik kasar, keseimbangan, dan
                            kekuatan otot anak.</p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Motorik kasar
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Kekuatan & fleksibilitas
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Postur tubuh
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Konseling Psikologi -->
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-48 bg-[#287f3b]/10 flex items-center justify-center">
                        <svg class="w-20 h-20 text-[#287f3b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Konseling Psikologi</h3>
                        <p class="text-gray-600 text-sm mb-4">Layanan konseling untuk membantu anak mengatasi masalah
                            emosional dan psikologis.</p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Asesmen psikologi
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Konseling keluarga
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Manajemen emosi
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Terapi -->
    <section class="py-20 bg-gradient-to-r from-[#165fac] to-[#287f3b]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Alur Layanan Terapi</h2>
                <p class="text-white/80 max-w-2xl mx-auto">Proses terapi yang terstruktur untuk hasil optimal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-[#165fac]">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Konsultasi Awal</h3>
                    <p class="text-white/80 text-sm">Diskusi dengan orang tua mengenai kondisi dan kebutuhan anak</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-[#165fac]">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Asesmen</h3>
                    <p class="text-white/80 text-sm">Evaluasi menyeluruh untuk menentukan jenis terapi yang tepat</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-[#165fac]">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Sesi Terapi</h3>
                    <p class="text-white/80 text-sm">Pelaksanaan terapi sesuai program yang telah dirancang</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-[#165fac]">4</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Evaluasi & Laporan</h3>
                    <p class="text-white/80 text-sm">Monitoring berkala dan laporan perkembangan untuk orang tua</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-[#165fac]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Konsultasikan Kebutuhan Anak Anda</h2>
            <p class="text-white/80 mb-8 text-lg">Tim terapis profesional kami siap membantu tumbuh kembang anak Anda
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/kontak') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition">
                    Hubungi Kami
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ url('/ppdb')}}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-[#287f3b] text-white font-semibold rounded-full hover:bg-[#1f6a31] transition">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

</body>

</html>