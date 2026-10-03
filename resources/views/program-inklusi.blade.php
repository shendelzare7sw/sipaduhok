@extends('layouts.landing')

@section('seo')
    <x-seo-meta title="Program Inklusi - PKBM House Of Knowledge" description="Program pendidikan inklusif PKBM House Of Knowledge memberikan akses pendidikan berkualitas untuk anak-anak berkebutuhan khusus dengan dukungan terapi dan bimbingan khusus." keywords="pendidikan inklusi, anak berkebutuhan khusus, ABK, sekolah inklusi, pendidikan khusus"></x-seo-meta>
@endsection

@section('body_class', 'bg-gray-50')

@section('content')

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


    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center">
        <img src="{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-center">
        <div class="bg-[linear-gradient(135deg,rgba(22,95,172,0.9)_0%,rgba(40,127,59,0.8)_100%)] absolute inset-0"></div>
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
                    <img loading="lazy" decoding="async" src="{{ asset($aboutContent['image'] ?? 'img/inklusi-main.jpg') }}" alt="Program Inklusi"
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

                    <div class="transition-all duration-300 hover:-translate-y-[5px] hover:shadow-[0_20px_40px_rgba(0,0,0,0.1)] bg-gray-50 rounded-2xl p-8 border-t-4 border-[var(--warna)]" data-warna="{{ warna_landing($styles['border']) }}">

                        <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-4 bg-[color-mix(in_srgb,var(--warna)_10%,transparent)]">
                             @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                <img loading="lazy" decoding="async" src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-8 h-8 object-contain">
                             @else
                                <i class="{{ $item['icon'] ?? 'fas fa-info-circle' }} text-2xl text-[var(--warna)]"></i>
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
                        class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow cursor-pointer" data-warna="{{ warna_landing($styles['bg']) }}">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-[color-mix(in_srgb,var(--warna)_10%,transparent)]">
                             @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                <img loading="lazy" decoding="async" src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-8 h-8 object-contain">
                             @else
                                <i class="{{ $item['icon'] ?? 'fas fa-user' }} text-3xl text-[var(--warna)]"></i>
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
    <section class="py-16 bg-[linear-gradient(135deg,var(--warna)_0%,var(--warna-akhir)_100%)]" data-warna="{{ warna_landing($ctaContent['background_gradient_start'] ?? '#d45930', '#d45930') }}" data-warna-akhir="{{ warna_landing($ctaContent['background_gradient_end'] ?? '#fac030', '#fac030') }}">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">{{ $ctaContent['title'] ?? 'Setiap Anak Berhak Mendapat Pendidikan' }}</h2>
            <p class="text-white/90 mb-8">{{ $ctaContent['description'] ?? 'Konsultasikan kebutuhan anak Anda dengan tim ahli kami secara gratis.' }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url($ctaContent['button_link_1'] ?? '/ppdb') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-[var(--warna)] font-semibold rounded-full hover:bg-gray-100 transition">
                    {{ $ctaContent['button_text_1'] ?? 'Daftar Sekarang' }}
                </a>
                <a href="{{ url($ctaContent['button_link_2'] ?? '/kontak') }}"
                    class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-[var(--warna)] transition">
                    {{ $ctaContent['button_text_2'] ?? 'Konsultasi Gratis' }}
                </a>
            </div>
        </div>
    </section>
@endsection
