<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Inklusi - PKBM House Of Knowledge</title>
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
            cursor: pointer;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Added modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease-in-out;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 1rem 1rem 0 0;
        }

        .close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2rem;
            height: 2rem;
            background-color: white;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }

        .close-button:hover {
            background-color: #f3f4f6;
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gray-50">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
        $aboutSection = $page->getSection('about');
        $aboutContent = $aboutSection->content ?? [];
        $servicesSection = $page->getSection('services');
        $servicesContent = $servicesSection->content ?? [];
        $servicesHeader = $servicesContent['header'] ?? [];
        $servicesItems = $servicesContent['items'] ?? [];

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
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Program Inklusi' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Program Inklusi' }}</h1>
            <p class="mt-4 text-lg text-white/90">
                {{ $heroContent['subtitle'] ?? 'Pendidikan untuk Anak Berkebutuhan Khusus' }}
            </p>
        </div>
    </section>

    <!-- About Program -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <img src="{{ asset($aboutContent['image'] ?? 'img/inklusi-main.jpg') }}" alt="Program Inklusi"
                        class="rounded-2xl shadow-xl w-full h-[400px] object-cover">
                    <div
                        class="absolute -bottom-6 -right-6 bg-[#d45930] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                        <p class="text-lg font-bold">Setiap Anak</p>
                        <p class="text-sm">Berhak Belajar</p>
                    </div>
                </div>
                <div>
                    <span
                        class="inline-block bg-[#d45930]/20 text-[#d45930] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $aboutContent['badge'] ?? 'Program Inklusi' }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        {{ $aboutContent['title'] ?? 'Pendidikan Inklusif' }}
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_1'] ?? 'Program Inklusi kami dirancang khusus untuk anak-anak berkebutuhan khusus (ABK) agar dapat belajar bersama dengan anak-anak lainnya dalam lingkungan yang inklusif dan mendukung.' }}
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_2'] ?? 'Dengan pendekatan individual dan dukungan dari tenaga ahli, setiap anak mendapatkan kesempatan yang sama untuk berkembang sesuai dengan potensinya masing-masing.' }}
                    </p>
                    <a href="{{ url($aboutContent['button_link'] ?? '/kontak') }}"
                        class="inline-flex items-center px-6 py-3 bg-[#d45930] text-white font-semibold rounded-full hover:bg-[#165fac] transition">
                        {{ $aboutContent['button_text'] ?? 'Konsultasi Gratis' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $servicesHeader['badge'] ?? 'Layanan Kami' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                    {{ $servicesHeader['title'] ?? 'Jenis Kebutuhan yang Kami Layani' }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Made cards clickable to open modal -->
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4 border-[#165fac]"
                    onclick="openModal('autism')">
                    <div class="w-14 h-14 bg-[#165fac]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#165fac]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Autisme (ASD)</h3>
                    <p class="text-gray-600">Program khusus untuk anak dengan gangguan spektrum autisme dengan
                        pendekatan terstruktur.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4 border-[#287f3b]"
                    onclick="openModal('adhd')">
                    <div class="w-14 h-14 bg-[#287f3b]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#287f3b]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">ADHD</h3>
                    <p class="text-gray-600">Pendekatan pembelajaran khusus untuk anak dengan gangguan pemusatan
                        perhatian dan hiperaktivitas.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4 border-[#d45930]"
                    onclick="openModal('disleksia')">
                    <div class="w-14 h-14 bg-[#d45930]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#d45930]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Disleksia</h3>
                    <p class="text-gray-600">Metode pembelajaran multisensori untuk anak dengan kesulitan membaca dan
                        menulis.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4 border-[#fac030]"
                    onclick="openModal('down')">
                    <div class="w-14 h-14 bg-[#fac030]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#fac030]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Down Syndrome</h3>
                    <p class="text-gray-600">Program stimulasi dan pembelajaran yang disesuaikan untuk anak down
                        syndrome.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4 border-[#165fac]"
                    onclick="openModal('speech')">
                    <div class="w-14 h-14 bg-[#165fac]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#165fac]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Speech Delay</h3>
                    <p class="text-gray-600">Terapi wicara dan program stimulasi bahasa untuk anak dengan keterlambatan
                        bicara.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4 border-[#287f3b]"
                    onclick="openModal('slow')">
                    <div class="w-14 h-14 bg-[#287f3b]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#287f3b]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Slow Learner</h3>
                    <p class="text-gray-600">Pendekatan pembelajaran bertahap untuk anak dengan kecepatan belajar yang
                        berbeda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Added Modal for each disorder type -->
    <!-- Modal Autisme -->
    <div id="modal-autism" class="modal-overlay" onclick="closeModalOnOverlay(event, 'autism')">
        <div class="modal-content">
            <button onclick="closeModal('autism')" class="close-button">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Updated to use real image -->
            <img src="{{ asset('img/autism.jpg') }}" alt="Autisme" class="modal-image">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Autisme (ASD)</h3>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Gejala Umum:</h4>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Kesulitan dalam komunikasi verbal dan non-verbal</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Kesulitan berinteraksi sosial dan memahami emosi orang lain</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Perilaku repetitif dan minat yang sangat terbatas</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Sensitivitas terhadap rangsangan sensorik (suara, cahaya, tekstur)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Kesulitan beradaptasi dengan perubahan rutinitas</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal ADHD -->
    <div id="modal-adhd" class="modal-overlay" onclick="closeModalOnOverlay(event, 'adhd')">
        <div class="modal-content">
            <button onclick="closeModal('adhd')" class="close-button">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Updated to use real image -->
            <img src="{{ asset('img/adhd.jpg') }}" alt="ADHD" class="modal-image">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">ADHD</h3>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Gejala Umum:</h4>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Kesulitan mempertahankan fokus dan perhatian</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Hiperaktif dan tidak bisa duduk diam</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Impulsif dan bertindak tanpa berpikir terlebih dahulu</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Mudah teralihkan oleh stimulus eksternal</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Kesulitan mengatur waktu dan menyelesaikan tugas</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Disleksia -->
    <div id="modal-disleksia" class="modal-overlay" onclick="closeModalOnOverlay(event, 'disleksia')">
        <div class="modal-content">
            <button onclick="closeModal('disleksia')" class="close-button">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Updated to use real image -->
            <img src="{{ asset('img/disleksia.jpg') }}" alt="Disleksia" class="modal-image">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Disleksia</h3>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Gejala Umum:</h4>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#d45930] mr-2">•</span>
                        <span>Kesulitan membaca dengan lancar dan akurat</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#d45930] mr-2">•</span>
                        <span>Kesulitan mengeja dan menulis</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#d45930] mr-2">•</span>
                        <span>Kesulitan memahami bacaan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#d45930] mr-2">•</span>
                        <span>Membingungkan huruf yang mirip (b dan d, p dan q)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#d45930] mr-2">•</span>
                        <span>Kesulitan mengingat urutan (hari, bulan, alfabet)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Down Syndrome -->
    <div id="modal-down" class="modal-overlay" onclick="closeModalOnOverlay(event, 'down')">
        <div class="modal-content">
            <button onclick="closeModal('down')" class="close-button">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Updated to use real image -->
            <img src="{{ asset('img/downsyndrom.jpg') }}" alt="Down Syndrome" class="modal-image">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Down Syndrome</h3>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Gejala Umum:</h4>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#fac030] mr-2">•</span>
                        <span>Keterlambatan perkembangan motorik dan kognitif</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#fac030] mr-2">•</span>
                        <span>Tonus otot yang lemah (hipotonia)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#fac030] mr-2">•</span>
                        <span>Kesulitan berbicara dan berkomunikasi</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#fac030] mr-2">•</span>
                        <span>Ciri fisik khas (wajah datar, mata miring ke atas)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#fac030] mr-2">•</span>
                        <span>Memerlukan waktu lebih lama untuk belajar keterampilan baru</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Speech Delay -->
    <div id="modal-speech" class="modal-overlay" onclick="closeModalOnOverlay(event, 'speech')">
        <div class="modal-content">
            <button onclick="closeModal('speech')" class="close-button">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Updated to use real image -->
            <img src="{{ asset('img/speechdelay.jpg') }}" alt="Speech Delay" class="modal-image">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Speech Delay</h3>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Gejala Umum:</h4>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Keterlambatan dalam mengucapkan kata-kata pertama</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Kesulitan mengucapkan kata-kata dengan jelas</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Kosakata terbatas untuk usia mereka</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Kesulitan menyusun kalimat sederhana</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#165fac] mr-2">•</span>
                        <span>Lebih banyak menggunakan gestur daripada kata-kata</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Slow Learner -->
    <div id="modal-slow" class="modal-overlay" onclick="closeModalOnOverlay(event, 'slow')">
        <div class="modal-content">
            <button onclick="closeModal('slow')" class="close-button">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Updated to use real image -->
            <img src="{{ asset('img/slowlearner.jpg') }}" alt="Slow Learner" class="modal-image">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Slow Learner</h3>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Gejala Umum:</h4>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Membutuhkan waktu lebih lama untuk memahami konsep baru</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Kesulitan mengikuti instruksi yang kompleks</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Memerlukan pengulangan dan latihan lebih banyak</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Kesulitan dalam pemecahan masalah</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-[#287f3b] mr-2">•</span>
                        <span>Prestasi akademik di bawah rata-rata anak seusianya</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Added JavaScript for modal functionality -->
    <script>
        function openModal(type) {
            const modal = document.getElementById(`modal-${type}`);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            }
        }

        function closeModal(type) {
            const modal = document.getElementById(`modal-${type}`);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling
            }
        }

        function closeModalOnOverlay(event, type) {
            if (event.target.classList.contains('modal-overlay')) {
                closeModal(type);
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const activeModal = document.querySelector('.modal-overlay.active');
                if (activeModal) {
                    activeModal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    </script>

    <!-- Tim -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Tim Profesional Kami</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Added clickable link to profil-guru and psychology brain icon -->
                <a href="{{ url('/profil-guru') }}"
                    class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="w-16 h-16 bg-[#165fac]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#165fac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Psikolog Anak</h3>
                    <p class="text-gray-600 text-sm mt-2">Asesmen dan konseling psikologis</p>
                </a>

                <!-- Added clickable link to profil-guru and hand therapy icon -->
                <a href="{{ url('/profil-guru') }}"
                    class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="w-16 h-16 bg-[#287f3b]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#287f3b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Terapis Okupasi</h3>
                    <p class="text-gray-600 text-sm mt-2">Terapi motorik dan sensori</p>
                </a>

                <!-- Added clickable link to profil-guru and microphone speech icon -->
                <a href="{{ url('/profil-guru') }}"
                    class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="w-16 h-16 bg-[#d45930]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#d45930]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Terapis Wicara</h3>
                    <p class="text-gray-600 text-sm mt-2">Terapi bicara dan bahasa</p>
                </a>

                <!-- Added clickable link to profil-guru and academic cap icon -->
                <a href="{{ url('/profil-guru') }}"
                    class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="w-16 h-16 bg-[#fac030]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#fac030]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13M0 18h.897c.35 0 .684-.188.897-.53l6.5-6.5c.78-.78 2.028-2.865 2.397-3.83M9.5 3L7.5 5h4v6H7.5V7h2.5z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800">Guru Pendamping</h3>
                    <p class="text-gray-600 text-sm mt-2">Shadow teacher terlatih</p>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #d45930 0%, #fac030 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Setiap Anak Berhak Mendapat Pendidikan</h2>
            <p class="text-white/90 mb-8">Konsultasikan kebutuhan anak Anda dengan tim ahli kami secara gratis.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/ppdb') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-[#d45930] font-semibold rounded-full hover:bg-gray-100 transition">
                    Daftar Sekarang
                </a>
                <a href="{{ url('/kontak') }}"
                    class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-[#d45930] transition">
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

</body>

</html>