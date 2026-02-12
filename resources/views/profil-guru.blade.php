<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
    @endphp
    <title>{{ $heroContent['title'] ?? 'Profil Guru & Tenaga Ahli' }} - PKBM House Of Knowledge</title>
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

        .teacher-card {
            transition: all 0.3s ease;
        }

        .teacher-card:hover {
            transform: translateY(-8px);
        }

        .teacher-card:hover .teacher-overlay {
            opacity: 1;
        }

        .teacher-overlay {
            transition: all 0.3s ease;
            opacity: 0;
        }
    </style>
</head>

<body class="bg-gray-50">

    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.png') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Profil</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Profil Guru & Tenaga Ahli' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Profil Guru & Tenaga Ahli' }}</h1>
        </div>
    </section>

    @php
        $staffSection = $page->getSection('staff_list');
        $content = $staffSection->content ?? [];

        // Handle both structures: direct array or {items: [...]}
        if (isset($content['items']) && is_array($content['items'])) {
            $staffList = $content['items'];
        } else {
            // Filter out non-numeric keys and only get staff items
            $staffList = collect($content)->filter(function ($item, $key) {
                return is_numeric($key) && is_array($item) && isset($item['name']);
            })->values()->all();
        }

        // Get unique categories for filter buttons
        $categories = collect($staffList)->pluck('category')->filter()->unique()->values()->all();

        $categoryColors = [
            'guru' => '#165fac',
            'terapis' => '#d45930',
            'psikolog' => '#fac030',
        ];

        $categoryLabels = [
            'guru' => 'Guru',
            'terapis' => 'Terapis',
            'psikolog' => 'Psikolog',
        ];
    @endphp

    <!-- Filter Tabs -->
    <section class="py-8 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4">
                <button class="filter-btn px-6 py-2 bg-[#165fac] text-white rounded-full font-medium"
                    onclick="filterTeachers('all')">Semua</button>
                @foreach($categories as $cat)
                    <button
                        class="filter-btn px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-[#165fac] hover:text-white transition"
                        onclick="filterTeachers('{{ $cat }}')">
                        {{ $categoryLabels[$cat] ?? ucfirst($cat) }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Teachers Grid -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" id="teachersGrid">
                @foreach($staffList as $index => $staff)
                    @php
                        $badgeColor = $categoryColors[$staff['category'] ?? 'guru'] ?? '#165fac';
                        $categoryLabel = $categoryLabels[$staff['category'] ?? 'guru'] ?? ucfirst($staff['category'] ?? 'Staff');
                    @endphp
                    <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden"
                        data-category="{{ $staff['category'] ?? 'guru' }}">
                        <div class="relative">
                            <img src="{{ asset($staff['image'] ?? 'img/default-person.png') }}" alt="{{ $staff['name'] }}"
                                class="w-full h-64 object-cover">
                            <span class="absolute top-4 right-4 text-white px-3 py-1 rounded-full text-xs font-medium"
                                style="background-color: {{ $badgeColor }}">
                                {{ $categoryLabel }}
                            </span>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="font-bold text-gray-800 text-lg">{{ $staff['name'] ?? 'Staff' }}</h3>
                            <p class="text-gray-500 text-sm">{{ $staff['position'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Join Team CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Bergabung Bersama Kami</h2>
            <p class="text-white/90 mb-8">Apakah Anda tertarik menjadi bagian dari tim kami? Kirimkan lamaran Anda
                sekarang.</p>
            <a href="{{ url('/kontak') }}"
                class="inline-flex items-center px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition">
                Hubungi Kami
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </section>

    <x-footer></x-footer>

    <script>
        function filterTeachers(category) {
            const cards = document.querySelectorAll('.teacher-card');
            const buttons = document.querySelectorAll('.filter-btn');

            buttons.forEach(btn => {
                btn.classList.remove('bg-[#165fac]', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            event.target.classList.remove('bg-gray-100', 'text-gray-700');
            event.target.classList.add('bg-[#165fac]', 'text-white');

            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
    }
    </script>

</body>

</html>