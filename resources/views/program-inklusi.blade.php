<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Program Inklusi - PKBM House Of Knowledge" description="Program pendidikan inklusif PKBM House Of Knowledge memberikan akses pendidikan berkualitas untuk anak-anak berkebutuhan khusus dengan dukungan terapi dan bimbingan khusus." keywords="pendidikan inklusi, anak berkebutuhan khusus, ABK, sekolah inklusi, pendidikan khusus"></x-seo-meta>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                @foreach($servicesItems as $index => $item)
                    @php
                        $colorValue = $item['color'] ?? 'blue';
                        if (str_starts_with($colorValue, '#')) {
                            $styles = ['border' => $colorValue, 'bg' => $colorValue, 'text' => $colorValue];
                        } else {
                            $styles = $colorMap[$colorValue] ?? $colorMap['blue'];
                        }
                    @endphp

                    <div class="card-hover bg-gray-50 rounded-2xl p-8 border-t-4"
                        style="border-color: {{ $styles['border'] }}">

                        <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-4"
                             style="background-color: {{ $styles['bg'] }}1A;">
                             @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-8 h-8 object-contain">
                             @else
                                <i class="{{ $item['icon'] ?? 'fas fa-info-circle' }} text-2xl" style="color: {{ $styles['text'] }}"></i>
                             @endif
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $item['title'] }}</h3>
                        <p class="text-gray-600">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Tim -->
    @php
        $teamSection = $page->getSection('team');
        $teamContent = $teamSection->content ?? [];
        $teamItems = $teamContent['items'] ?? [];
        $ctaSection = $page->getSection('cta');
        $ctaContent = $ctaSection->content ?? [];
    @endphp
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $teamContent['header']['title'] ?? 'Tim Profesional Kami' }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($teamItems as $item)
                    @php
                        $colorValue = $item['color'] ?? 'blue';
                        if (str_starts_with($colorValue, '#')) {
                            $styles = ['border' => $colorValue, 'bg' => $colorValue, 'text' => $colorValue];
                        } else {
                            $styles = $colorMap[$colorValue] ?? $colorMap['blue'];
                        }
                    @endphp
                    <a href="{{ url($item['link'] ?? '/profil-guru') }}"
                        class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow cursor-pointer">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                             style="background-color: {{ $styles['bg'] }}1A;">
                             @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-8 h-8 object-contain">
                             @else
                                <i class="{{ $item['icon'] ?? 'fas fa-user' }} text-3xl" style="color: {{ $styles['text'] }}"></i>
                             @endif
                        </div>
                        <h3 class="font-bold text-gray-800">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $item['description'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, {{ $ctaContent['background_gradient_start'] ?? '#d45930' }} 0%, {{ $ctaContent['background_gradient_end'] ?? '#fac030' }} 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">{{ $ctaContent['title'] ?? 'Setiap Anak Berhak Mendapat Pendidikan' }}</h2>
            <p class="text-white/90 mb-8">{{ $ctaContent['description'] ?? 'Konsultasikan kebutuhan anak Anda dengan tim ahli kami secara gratis.' }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url($ctaContent['button_link_1'] ?? '/ppdb') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-[#d45930] font-semibold rounded-full hover:bg-gray-100 transition"
                    style="color: {{ $ctaContent['background_gradient_start'] ?? '#d45930' }}">
                    {{ $ctaContent['button_text_1'] ?? 'Daftar Sekarang' }}
                </a>
                <a href="{{ url($ctaContent['button_link_2'] ?? '/kontak') }}"
                    class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-[#d45930] transition"
                   onmouseover="this.style.color='{{ $ctaContent['background_gradient_start'] ?? '#d45930' }}'"
                   onmouseout="this.style.color='white'">
                    {{ $ctaContent['button_text_2'] ?? 'Konsultasi Gratis' }}
                </a>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

    @vite(['resources/js/navbar.js'])
</body>

</html>
