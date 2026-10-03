@extends('layouts.landing')

@section('seo')
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
    @endphp
    <x-seo-meta title="{{ $heroContent['title'] ?? 'Profil Guru & Tenaga Ahli' }} - PKBM House Of Knowledge" description="Tim guru dan tenaga ahli berpengalaman PKBM House Of Knowledge siap memberikan pembelajaran interaktif dan pembimbingan profesional untuk setiap siswa." keywords="guru profesional, tenaga ahli, tim pendidik, kredensial guru, pengalaman mengajar"></x-seo-meta>
@endsection

@section('body_class', 'bg-gray-50')

@section('content')


    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center">
        <img src="{{ asset($heroContent['background_image'] ?? 'img/hero-bg.png') }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-center">
        <div class="bg-[linear-gradient(135deg,rgba(22,95,172,0.9)_0%,rgba(40,127,59,0.8)_100%)] absolute inset-0"></div>
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

    <div x-data="{ kategori: 'all' }">
    <!-- Filter Tabs -->
    <section class="py-8 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4">
                <button type="button" class="px-6 py-2 rounded-full font-medium"
                    :class="kategori === 'all' ? 'bg-[#165fac] text-white' : 'bg-gray-100 text-gray-700'"
                    x-on:click="kategori = 'all'">Semua</button>
                @foreach($categories as $cat)
                    <button type="button"
                        class="px-6 py-2 rounded-full font-medium hover:bg-[#165fac] hover:text-white transition"
                        :class="kategori === @js($cat) ? 'bg-[#165fac] text-white' : 'bg-gray-100 text-gray-700'"
                        x-on:click="kategori = @js($cat)">
                        {{ $categoryLabels[$cat] ?? ucfirst($cat) }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Teachers Grid -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($staffList as $index => $staff)
                    @php
                        $badgeColor = $categoryColors[$staff['category'] ?? 'guru'] ?? '#165fac';
                        $categoryLabel = $categoryLabels[$staff['category'] ?? 'guru'] ?? ucfirst($staff['category'] ?? 'Staff');
                    @endphp
                    <div class="transition-all duration-300 hover:-translate-y-2 bg-white rounded-2xl shadow-lg overflow-hidden"
                        x-show="kategori === 'all' || kategori === @js($staff['category'] ?? 'guru')">
                        <div class="relative">
                            <img loading="lazy" decoding="async" src="{{ asset($staff['image'] ?? 'img/guru-1.png') }}" alt="{{ $staff['name'] }}"
                                class="w-full h-64 object-cover">
                            <span class="absolute top-4 right-4 text-white px-3 py-1 rounded-full text-xs font-medium bg-[var(--warna)]" data-warna="{{ warna_landing($badgeColor) }}">
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
    </div>

    <!-- Join Team CTA -->
    <section class="py-16 bg-[linear-gradient(135deg,#165fac_0%,#287f3b_100%)]">
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
@endsection
