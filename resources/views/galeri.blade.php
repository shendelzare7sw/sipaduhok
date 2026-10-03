@extends('layouts.landing')

@section('seo')
    <x-seo-meta title="PKBM House Of Knowledge - Galeri" description="Galeri foto dan dokumentasi kegiatan pembelajaran serta fasilitas di PKBM House Of Knowledge."></x-seo-meta>
@endsection

@section('content')

    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $categoriesSection = $page->getSection('categories');
        $categoriesContent = $categoriesSection->content ?? [];
        $categories = $categoriesContent['items'] ?? [];

        $gallerySection = $page->getSection('gallery_items');
        $galleryContent = $gallerySection->content ?? [];
        $galleryItems = $galleryContent['items'] ?? [];

        // Color mapping for categories
        $colorMap = [
            'primary' => 'bg-primary text-white',
            'secondary' => 'bg-secondary text-white',
            'accent-yellow' => 'bg-accent-yellow text-gray-800',
            'accent-orange' => 'bg-accent-orange text-white',
            'accent-bright' => 'bg-accent-bright text-gray-800',
        ];
    @endphp

    <!-- Navbar Component -->


    <!-- Hero Section -->
    <section class="relative pt-32 pb-20">
        <img src="{{ asset($heroContent['background_image'] ?? 'img/bg-galeri.jpg') }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-center">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(22,95,172,0.75)_0%,rgba(40,127,59,0.75)_100%)]"></div>

        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-10 w-32 h-32 border-4 border-white/10 rounded-full"></div>
            <div class="absolute bottom-20 right-20 w-20 h-20 bg-accent-yellow/20 rounded-full"></div>
            <div class="absolute top-40 right-40 w-24 h-24 border-4 border-white/10 rounded-lg rotate-45"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <span
                    class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-medium rounded-full mb-4">
                    {{ $heroContent['badge'] ?? 'Galeri Kami' }}
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    {{ $heroContent['title'] ?? 'Galeri Kegiatan' }}
                </h1>
                <p class="text-lg md:text-xl text-white/90 max-w-3xl mx-auto">
                    {{ $heroContent['subtitle'] ?? 'Dokumentasi kegiatan pembelajaran, prestasi, dan momen berharga siswa-siswi PKBM House Of Knowledge' }}
                </p>
            </div>
        </div>
    </section>

    <div x-data="galeriPublik">
    <!-- Filter Section -->
    <section class="py-8 bg-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center gap-3">
                <button type="button" x-on:click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-[linear-gradient(135deg,#165fac_0%,#287f3b_100%)] text-white -translate-y-0.5 shadow-[0_10px_20px_rgba(22,95,172,0.3)]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="transition-all duration-300 px-6 py-3 font-medium rounded-full">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button type="button" x-on:click="filter = @js($category['key'] ?? '')"
                        :class="filter === @js($category['key'] ?? '') ? 'bg-[linear-gradient(135deg,#165fac_0%,#287f3b_100%)] text-white -translate-y-0.5 shadow-[0_10px_20px_rgba(22,95,172,0.3)]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="transition-all duration-300 px-6 py-3 font-medium rounded-full">
                        {{ $category['label'] ?? '' }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Gallery Grid Section -->
    <section class="py-16 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="columns-1 gap-6 sm:columns-2 lg:columns-3">

                @php
                    // Build category lookup for labels and colors
                    $categoryLookup = [];
                    foreach ($categories as $cat) {
                        $categoryLookup[$cat['key'] ?? ''] = $cat;
                    }
                @endphp

                @foreach($galleryItems as $item)
                    @php
                        $cat = $categoryLookup[$item['category'] ?? ''] ?? [];
                        $categoryLabel = $cat['label'] ?? ucfirst($item['category'] ?? '');
                        $categoryColor = $cat['color'] ?? 'primary';
                        $badgeClass = $colorMap[$categoryColor] ?? 'bg-primary text-white';
                    @endphp
                    <div class="group relative mb-6 cursor-pointer break-inside-avoid overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-[600ms] ease-[ease]"
                        x-show="tampil(@js($item['category'] ?? ''))" x-muncul="opacity-0 translate-y-[30px]">
                        <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/gallery-1.jpg') }}" alt="{{ $item['title'] ?? '' }}"
                            class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.8),transparent)] opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        <div class="absolute bottom-0 left-0 right-0 translate-y-full p-6 transition-transform duration-300 group-hover:translate-y-0">
                            <span class="inline-block px-3 py-1 {{ $badgeClass }} text-xs rounded-full mb-2">{{ $categoryLabel }}</span>
                            <h3 class="text-white font-bold text-lg mb-1">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-white/80 text-sm">{{ $item['date'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
    </div>

    <!-- CTA Section -->
    <section class="py-20 bg-[linear-gradient(135deg,#165fac_0%,#287f3b_100%)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Bergabunglah dengan Keluarga Besar Kami
            </h2>
            <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                Jadilah bagian dari momen-momen berharga dan prestasi gemilang di PKBM House Of Knowledge
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/ppdb') }}"
                    class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Daftar Sekarang
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
@endsection
