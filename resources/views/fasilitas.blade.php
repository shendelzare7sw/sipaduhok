@extends('layouts.landing')

@section('seo')
    <x-seo-meta title="Fasilitas - PKBM House Of Knowledge" description="Fasilitas lengkap dan modern di PKBM House Of Knowledge mendukung proses pembelajaran optimal dengan ruang kelas nyaman, laboratorium, dan area bermain interaktif." keywords="fasilitas sekolah, sarana pendidikan, ruang kelas, laboratorium, fasilitas lengkap"></x-seo-meta>
@endsection

@section('content')
    <div aria-hidden="true" class="pointer-events-none fixed left-0 top-0 z-50 hidden h-1 w-full origin-left bg-gradient-to-r from-primary to-secondary animate-progres-gulir [animation-timeline:scroll()] supports-[animation-timeline:scroll()]:block"></div>
    <button type="button" x-data="{ tampil: false }" x-on:scroll.window.passive="tampil = window.scrollY > 500" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        :class="tampil ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-label="Kembali ke atas"
        class="fixed bottom-8 right-8 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg transition-all duration-300 hover:bg-secondary" x-cloak>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
    </button>

    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $ruangBelajarSection = $page->getSection('ruang_belajar');
        $ruangBelajarContent = $ruangBelajarSection->content ?? [];

        $ruangTerapiSection = $page->getSection('ruang_terapi');
        $ruangTerapiContent = $ruangTerapiSection->content ?? [];

        $areaBermainSection = $page->getSection('area_bermain');
        $areaBermainContent = $areaBermainSection->content ?? [];

        $perpustakaanSection = $page->getSection('perpustakaan');
        $perpustakaanContent = $perpustakaanSection->content ?? [];
        $statsSection = $page->getSection('stats');
        $statsContent = $statsSection->content ?? [];

        $gallerySection = $page->getSection('gallery');
        $galleryContent = $gallerySection->content ?? [];

    @endphp


    <!-- ==================== HERO SECTION ==================== -->
    <section class="relative min-h-screen flex items-center overflow-hidden">
        <img src="{{ asset($heroContent['background_image'] ?? 'img/bg-fasilitas.jpg') }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-center">

        <div class="bg-[linear-gradient(135deg,rgba(22,95,172,0.9)_20%,rgba(40,127,59,0.85)_80%)] absolute inset-0"></div>

        <!-- Decorative Blobs -->
        <div class="absolute top-20 left-10 w-96 h-96 rounded-full bg-accent-yellow opacity-30 blur-[60px] animate-blob [animation-delay:0s]">
        </div>
        <div class="absolute bottom-20 right-20 w-80 h-80 rounded-full bg-secondary opacity-30 blur-[60px] animate-blob [animation-delay:2s]">
        </div>

        <!-- Decorative Elements -->
        <div
            class="absolute top-32 left-16 w-20 h-20 border-4 border-white/20 rounded-full animate-float hidden lg:block">
        </div>
        <div class="absolute bottom-40 left-32 w-12 h-12 bg-accent-yellow/30 rounded-full animate-float [animation-delay:1s] hidden lg:block"></div>
        <div class="absolute top-48 right-40 w-16 h-16 border-4 border-white/30 rounded-lg rotate-45 animate-float [animation-delay:2s] hidden lg:block"></div>
        <div class="absolute bottom-32 right-16 w-10 h-10 bg-white/20 rounded-full animate-float [animation-delay:1.5s] hidden lg:block"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-20">
            <div class="text-center">
                <span
                    class="inline-block px-6 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-medium rounded-full mb-6 animate-fade-in-up-lambat">
                    {{ $heroContent['badge'] ?? 'Fasilitas Lengkap & Modern' }}
                </span>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6 animate-fade-in-up-lambat [animation-delay:0.2s]">
                    {{ $heroContent['title'] ?? 'Fasilitas' }}<br>
                    <span class="text-accent-yellow">{{ $heroContent['title_highlight'] ?? 'Terbaik' }}</span>
                    @if(!empty($heroContent['title_line2']))
                        <br>{{ $heroContent['title_line2'] }}
                    @endif
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-12 leading-relaxed max-w-3xl mx-auto animate-fade-in-up-lambat [animation-delay:0.4s]">
                    {{ $heroContent['subtitle'] ?? 'PKBM House Of Knowledge menyediakan fasilitas lengkap dan modern untuk mendukung proses belajar mengajar yang efektif dan menyenangkan' }}
                </p>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto animate-scale-in [animation-delay:0.6s]">
                        @if(!empty($statsContent) && is_array($statsContent))
                        @foreach($statsContent as $stat)
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                            <p class="text-4xl font-bold text-white mb-2" x-hitung>{{ $stat['value'] ?? '0' }}</p>
                            <p class="text-white/80 text-sm">{{ $stat['label'] ?? 'Statistik' }}</p>
                        </div>
                        @endforeach
                    @else
                        <!-- Fallback to Hero Config if Stats Section is empty -->
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                            <p class="text-4xl font-bold text-white mb-2" x-hitung>{{ $heroContent['stat_1_value'] ?? '20+' }}</p>
                            <p class="text-white/80 text-sm">{{ $heroContent['stat_1_label'] ?? 'Ruang Kelas' }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                            <p class="text-4xl font-bold text-white mb-2" x-hitung>{{ $heroContent['stat_2_value'] ?? '30+' }}</p>
                            <p class="text-white/80 text-sm">{{ $heroContent['stat_2_label'] ?? 'Alat Terapi' }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                            <p class="text-4xl font-bold text-white mb-2" x-hitung>{{ $heroContent['stat_3_value'] ?? '3+' }}</p>
                            <p class="text-white/80 text-sm">{{ $heroContent['stat_3_label'] ?? 'Area Bermain' }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                            <p class="text-4xl font-bold text-white mb-2" x-hitung>{{ $heroContent['stat_4_value'] ?? '1000+' }}</p>
                            <p class="text-white/80 text-sm">{{ $heroContent['stat_4_label'] ?? 'Koleksi Buku' }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-12 animate-fade-in-up-lambat [animation-delay:0.8s]">
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

            @php
                // Helper: Resolve icon_color to hex
                $colorNameMap = [
                    'primary' => '#165fac', 'secondary' => '#287f3b',
                    'accent-orange' => '#d45930', 'accent-yellow' => '#fac030',
                    'orange' => '#d45930', 'blue' => '#165fac',
                    'green' => '#287f3b', 'yellow' => '#fac030',
                ];

                // Resolve colors for each section
                $rbColor = $ruangBelajarContent['header']['icon_color'] ?? $ruangBelajarContent['icon_color'] ?? 'primary';
                $rbHex = str_starts_with($rbColor, '#') ? $rbColor : ($colorNameMap[$rbColor] ?? '#165fac');

                $rtColor = $ruangTerapiContent['header']['icon_color'] ?? $ruangTerapiContent['icon_color'] ?? 'accent-orange';
                $rtHex = str_starts_with($rtColor, '#') ? $rtColor : ($colorNameMap[$rtColor] ?? '#d45930');

                $abColor = $areaBermainContent['header']['icon_color'] ?? $areaBermainContent['icon_color'] ?? 'accent-yellow';
                $abHex = str_starts_with($abColor, '#') ? $abColor : ($colorNameMap[$abColor] ?? '#fac030');

                $ppColor = $perpustakaanContent['header']['icon_color'] ?? $perpustakaanContent['icon_color'] ?? 'secondary';
                $ppHex = str_starts_with($ppColor, '#') ? $ppColor : ($colorNameMap[$ppColor] ?? '#287f3b');
            @endphp

            <!-- ===== 1. RUANG BELAJAR ===== -->
            <div class="mb-32" data-warna="{{ warna_landing($rbHex) }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Carousel -->
                    <div class="group/carousel relative w-full overflow-hidden rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.15)]"
                         x-data="carouselFasilitas" x-saat-terlihat="animate-fade-in-up-lambat" x-on:mouseenter="jeda()" x-on:mouseleave="mulai()"
                         x-on:touchstart.passive="sentuhMulai($event)" x-on:touchmove.passive="sentuhGeser($event)" x-on:touchend="sentuhSelesai()">
                        <div x-ref="jalur" class="flex transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" :class="geser">
                            @if(isset($ruangBelajarContent['items']) && is_array($ruangBelajarContent['items']))
                                @foreach($ruangBelajarContent['items'] as $item)
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}" alt="{{ $item['title'] ?? 'Fasilitas' }}" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">{{ $item['title'] ?? '' }}</p>
                                        @if(!empty($item['description']))
                                        <p class="text-sm mt-1">{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-belajar-1.jpg') }}" alt="Ruang Belajar 1" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Ruang Kelas Modern</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-belajar-2.jpg') }}" alt="Ruang Belajar 2" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Suasana Belajar Kondusif</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-belajar-3.jpg') }}" alt="Ruang Belajar 3" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Ruang Kelas Dilengkapi Ac</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-belajar-4.jpg') }}" alt="Ruang Belajar 4" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Furniture Ergonomis dan Nyaman</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <button type="button" x-on:click="sebelumnya()" aria-label="Foto sebelumnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 left-2.5 md:left-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" x-on:click="berikutnya()" aria-label="Foto berikutnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 right-2.5 md:right-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-2.5">
                            <template x-for="i in jumlah" :key="i">
                                <button type="button" x-on:click="ke(i - 1)" :aria-label="'Foto ' + i" class="h-3 cursor-pointer border-0 transition-all duration-300"
                                    :class="aktif === i - 1 ? 'w-8 rounded-md bg-white' : 'w-3 rounded-full bg-white/50 hover:bg-white/80'"></button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 rounded-2xl flex items-center justify-center animate-float-cepat bg-[color-mix(in_srgb,var(--warna)_8%,transparent)]">
                                @php $rbIcon = $ruangBelajarContent['header']['icon'] ?? $ruangBelajarContent['icon'] ?? null; @endphp
                                @if($rbIcon && str_starts_with($rbIcon, 'fa'))
                                    <i class="{{ $rbIcon }} text-[1.5rem] text-[var(--warna)]"></i>
                                @else
                                    <svg class="w-8 h-8 text-[var(--warna)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                    </svg>
                                @endif
                            </div>
                            <span
                                class="px-4 py-2 text-sm font-medium rounded-full bg-[color-mix(in_srgb,var(--warna)_8%,transparent)] text-[var(--warna)]">{{ $ruangBelajarContent['header']['badge'] ?? $ruangBelajarContent['badge'] ?? 'Fasilitas Utama' }}</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                            {{ $ruangBelajarContent['header']['title'] ?? $ruangBelajarContent['title'] ?? 'Ruang Belajar' }}</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            {{ $ruangBelajarContent['header']['description'] ?? $ruangBelajarContent['description'] ?? 'Ruang belajar kami dirancang dengan konsep modern dan nyaman untuk menciptakan suasana belajar yang kondusif.' }}
                        </p>

                        <div class="space-y-4">
                            @for($i = 1; $i <= 3; $i++)
                            @php
                                $fTitle = $ruangBelajarContent['header']['feature_'.$i.'_title'] ?? $ruangBelajarContent['feature_'.$i.'_title'] ?? null;
                                $fDesc = $ruangBelajarContent['header']['feature_'.$i.'_desc'] ?? $ruangBelajarContent['feature_'.$i.'_desc'] ?? null;
                            @endphp
                            @if($fTitle)
                            <div class="flex items-start gap-4 opacity-0 -translate-x-5 animate-geser-masuk [&:nth-child(1)]:[animation-delay:0.1s] [&:nth-child(2)]:[animation-delay:0.2s] [&:nth-child(3)]:[animation-delay:0.3s]">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-1 bg-[var(--warna)]">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">{{ $fTitle }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $fDesc ?? '' }}</p>
                                </div>
                            </div>
                            @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 2. RUANG TERAPI ===== -->
            <div class="mb-32" data-warna="{{ warna_landing($rtHex) }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="order-2 lg:order-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 rounded-2xl flex items-center justify-center animate-float-cepat bg-[color-mix(in_srgb,var(--warna)_8%,transparent)]">
                                @php $rtIcon = $ruangTerapiContent['header']['icon'] ?? $ruangTerapiContent['icon'] ?? null; @endphp
                                @if($rtIcon && str_starts_with($rtIcon, 'fa'))
                                    <i class="{{ $rtIcon }} text-[1.5rem] text-[var(--warna)]"></i>
                                @else
                                    <svg class="w-8 h-8 text-[var(--warna)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <span
                                class="px-4 py-2 text-sm font-medium rounded-full bg-[color-mix(in_srgb,var(--warna)_8%,transparent)] text-[var(--warna)]">{{ $ruangTerapiContent['header']['badge'] ?? $ruangTerapiContent['badge'] ?? 'Program Terapi' }}</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">{{ $ruangTerapiContent['header']['title'] ?? $ruangTerapiContent['title'] ?? 'Ruang Terapi' }}</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            {{ $ruangTerapiContent['header']['description'] ?? $ruangTerapiContent['description'] ?? 'Menyediakan berbagai alat terapi yang digunakan khusus untuk mendukung perkembangan motorik dan sensorik pada anak-anak berkebutuhan khusus.' }}
                        </p>

                        <div class="space-y-4">
                            @for($i = 1; $i <= 3; $i++)
                            @php
                                $fTitle = $ruangTerapiContent['header']['feature_'.$i.'_title'] ?? $ruangTerapiContent['feature_'.$i.'_title'] ?? null;
                                $fDesc = $ruangTerapiContent['header']['feature_'.$i.'_desc'] ?? $ruangTerapiContent['feature_'.$i.'_desc'] ?? null;
                            @endphp
                            @if($fTitle)
                            <div class="flex items-start gap-4 opacity-0 -translate-x-5 animate-geser-masuk [&:nth-child(1)]:[animation-delay:0.1s] [&:nth-child(2)]:[animation-delay:0.2s] [&:nth-child(3)]:[animation-delay:0.3s]">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-1 bg-[var(--warna)]">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">{{ $fTitle }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $fDesc ?? '' }}</p>
                                </div>
                            </div>
                            @endif
                            @endfor
                        </div>
                    </div>

                    <!-- Carousel -->
                    <div class="group/carousel relative w-full overflow-hidden rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] order-1 lg:order-2"
                         x-data="carouselFasilitas" x-saat-terlihat="animate-fade-in-up-lambat" x-on:mouseenter="jeda()" x-on:mouseleave="mulai()"
                         x-on:touchstart.passive="sentuhMulai($event)" x-on:touchmove.passive="sentuhGeser($event)" x-on:touchend="sentuhSelesai()">
                        <div x-ref="jalur" class="flex transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" :class="geser">
                            @if(isset($ruangTerapiContent['items']) && is_array($ruangTerapiContent['items']))
                                @foreach($ruangTerapiContent['items'] as $item)
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}" alt="{{ $item['title'] ?? 'Fasilitas' }}" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">{{ $item['title'] ?? '' }}</p>
                                        @if(!empty($item['description']))
                                        <p class="text-sm mt-1">{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-terapi-1.jpg') }}" alt="Ruang Terapi 1" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Ruang Terapi Lengkap</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-terapi-2.jpg') }}" alt="Ruang Terapi 2" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Alat Terapi Sensorik</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-terapi-3.jpg') }}" alt="Ruang Terapi 3" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Ruang Terapi Anak Berkebutuhan Khusus</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-ruang-terapi-4.jpg') }}" alt="Ruang Terapi 4" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Fasilitas Ruang Terapi</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <button type="button" x-on:click="sebelumnya()" aria-label="Foto sebelumnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 left-2.5 md:left-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" x-on:click="berikutnya()" aria-label="Foto berikutnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 right-2.5 md:right-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-2.5">
                            <template x-for="i in jumlah" :key="i">
                                <button type="button" x-on:click="ke(i - 1)" :aria-label="'Foto ' + i" class="h-3 cursor-pointer border-0 transition-all duration-300"
                                    :class="aktif === i - 1 ? 'w-8 rounded-md bg-white' : 'w-3 rounded-full bg-white/50 hover:bg-white/80'"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 3. AREA BERMAIN ===== -->
            <div class="mb-32" data-warna="{{ warna_landing($abHex) }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Carousel -->
                    <div class="group/carousel relative w-full overflow-hidden rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.15)]"
                         x-data="carouselFasilitas" x-saat-terlihat="animate-fade-in-up-lambat" x-on:mouseenter="jeda()" x-on:mouseleave="mulai()"
                         x-on:touchstart.passive="sentuhMulai($event)" x-on:touchmove.passive="sentuhGeser($event)" x-on:touchend="sentuhSelesai()">
                        <div x-ref="jalur" class="flex transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" :class="geser">
                            @if(isset($areaBermainContent['items']) && is_array($areaBermainContent['items']))
                                @foreach($areaBermainContent['items'] as $item)
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}" alt="{{ $item['title'] ?? 'Fasilitas' }}" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">{{ $item['title'] ?? '' }}</p>
                                        @if(!empty($item['description']))
                                        <p class="text-sm mt-1">{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-area-bermain-1.jpg') }}" alt="Area Bermain 1" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Playground Outdoor</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-area-bermain-2.jpg') }}" alt="Area Bermain 2" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Area Bermain Indoor</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-area-bermain-3.jpg') }}" alt="Area Bermain 3" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Zona Bermain Aman</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-area-bermain-4.jpg') }}" alt="Area Bermain 4" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Permainan Edukatif</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <button type="button" x-on:click="sebelumnya()" aria-label="Foto sebelumnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 left-2.5 md:left-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" x-on:click="berikutnya()" aria-label="Foto berikutnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 right-2.5 md:right-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-2.5">
                            <template x-for="i in jumlah" :key="i">
                                <button type="button" x-on:click="ke(i - 1)" :aria-label="'Foto ' + i" class="h-3 cursor-pointer border-0 transition-all duration-300"
                                    :class="aktif === i - 1 ? 'w-8 rounded-md bg-white' : 'w-3 rounded-full bg-white/50 hover:bg-white/80'"></button>
                            </template>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 rounded-2xl flex items-center justify-center animate-float-cepat bg-[color-mix(in_srgb,var(--warna)_8%,transparent)]">
                                @php $abIcon = $areaBermainContent['header']['icon'] ?? $areaBermainContent['icon'] ?? null; @endphp
                                @if($abIcon && str_starts_with($abIcon, 'fa'))
                                    <i class="{{ $abIcon }} text-[1.5rem] text-[var(--warna)]"></i>
                                @else
                                    <svg class="w-8 h-8 text-[var(--warna)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endif
                            </div>
                            <span
                                class="px-4 py-2 text-sm font-medium rounded-full bg-[color-mix(in_srgb,var(--warna)_8%,transparent)] text-[var(--warna)]">{{ $areaBermainContent['header']['badge'] ?? $areaBermainContent['badge'] ?? 'Fasilitas Rekreasi' }}</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">{{ $areaBermainContent['header']['title'] ?? $areaBermainContent['title'] ?? 'Area Bermain' }}</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            {{ $areaBermainContent['header']['description'] ?? $areaBermainContent['description'] ?? 'Area bermain yang luas dan aman untuk mengembangkan motorik kasar anak.' }}
                        </p>

                        <div class="space-y-4">
                            @for($i = 1; $i <= 3; $i++)
                            @php
                                $fTitle = $areaBermainContent['header']['feature_'.$i.'_title'] ?? $areaBermainContent['feature_'.$i.'_title'] ?? null;
                                $fDesc = $areaBermainContent['header']['feature_'.$i.'_desc'] ?? $areaBermainContent['feature_'.$i.'_desc'] ?? null;
                            @endphp
                            @if($fTitle)
                            <div class="flex items-start gap-4 opacity-0 -translate-x-5 animate-geser-masuk [&:nth-child(1)]:[animation-delay:0.1s] [&:nth-child(2)]:[animation-delay:0.2s] [&:nth-child(3)]:[animation-delay:0.3s]">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-1 bg-[var(--warna)]">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">{{ $fTitle }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $fDesc ?? '' }}</p>
                                </div>
                            </div>
                            @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== 4. PERPUSTAKAAN ===== -->
            <div class="mb-32" data-warna="{{ warna_landing($ppHex) }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="order-2 lg:order-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-16 h-16 rounded-2xl flex items-center justify-center animate-float-cepat bg-[color-mix(in_srgb,var(--warna)_8%,transparent)]">
                                @php $ppIcon = $perpustakaanContent['header']['icon'] ?? $perpustakaanContent['icon'] ?? null; @endphp
                                @if($ppIcon && str_starts_with($ppIcon, 'fa'))
                                    <i class="{{ $ppIcon }} text-[1.5rem] text-[var(--warna)]"></i>
                                @else
                                    <svg class="w-8 h-8 text-[var(--warna)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                    </svg>
                                @endif
                            </div>
                            <span
                                class="px-4 py-2 text-sm font-medium rounded-full bg-[color-mix(in_srgb,var(--warna)_8%,transparent)] text-[var(--warna)]">{{ $perpustakaanContent['header']['badge'] ?? $perpustakaanContent['badge'] ?? 'Fasilitas Edukasi' }}</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">{{ $perpustakaanContent['header']['title'] ?? $perpustakaanContent['title'] ?? 'Perpustakaan' }}</h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                            {{ $perpustakaanContent['header']['description'] ?? $perpustakaanContent['description'] ?? 'Perpustakaan dengan koleksi lengkap untuk menumbuhkan minat baca dan literasi siswa. Ruangan yang nyaman dengan koleksi buku yang terus diperbarui.' }}
                        </p>

                        <div class="space-y-4">
                            @for($i = 1; $i <= 3; $i++)
                            @php
                                $fTitle = $perpustakaanContent['header']['feature_'.$i.'_title'] ?? $perpustakaanContent['feature_'.$i.'_title'] ?? null;
                                $fDesc = $perpustakaanContent['header']['feature_'.$i.'_desc'] ?? $perpustakaanContent['feature_'.$i.'_desc'] ?? null;
                                if (!$fTitle) {
                                    $fTitle = match($i) {
                                        1 => '1000+ Koleksi Buku',
                                        2 => 'Ruang Baca Nyaman',
                                        3 => 'Sistem Peminjaman Mudah',
                                    };
                                    $fDesc = $fDesc ?? match($i) {
                                        1 => 'Beragam buku pelajaran, fiksi, dan non-fiksi',
                                        2 => 'Suasana tenang untuk membaca dan belajar',
                                        3 => 'Akses mudah untuk meminjam dan mengembalikan buku',
                                    };
                                }
                            @endphp
                            @if($fTitle)
                            <div class="flex items-start gap-4 opacity-0 -translate-x-5 animate-geser-masuk [&:nth-child(1)]:[animation-delay:0.1s] [&:nth-child(2)]:[animation-delay:0.2s] [&:nth-child(3)]:[animation-delay:0.3s]">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-1 bg-[var(--warna)]">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">{{ $fTitle }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $fDesc ?? '' }}</p>
                                </div>
                            </div>
                            @endif
                            @endfor
                        </div>
                    </div>

                    <!-- Carousel -->
                    <div class="group/carousel relative w-full overflow-hidden rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] order-1 lg:order-2"
                         x-data="carouselFasilitas" x-saat-terlihat="animate-fade-in-up-lambat" x-on:mouseenter="jeda()" x-on:mouseleave="mulai()"
                         x-on:touchstart.passive="sentuhMulai($event)" x-on:touchmove.passive="sentuhGeser($event)" x-on:touchend="sentuhSelesai()">
                        <div x-ref="jalur" class="flex transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" :class="geser">
                            @if(isset($perpustakaanContent['items']) && is_array($perpustakaanContent['items']))
                                @foreach($perpustakaanContent['items'] as $item)
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}" alt="{{ $item['title'] ?? 'Fasilitas' }}" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">{{ $item['title'] ?? '' }}</p>
                                        @if(!empty($item['description']))
                                        <p class="text-sm mt-1">{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-perpustakaan-1.jpg') }}" alt="Perpustakaan 1" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Koleksi Buku Lengkap</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-perpustakaan-2.jpg') }}" alt="Perpustakaan 2" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Ruang Baca yang Nyaman</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-perpustakaan-3.jpg') }}" alt="Perpustakaan 3" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Area Literasi Interaktif</p>
                                    </div>
                                </div>
                                <div class="group/slide relative h-[300px] min-w-full md:h-[500px]">
                                    <img loading="lazy" decoding="async" src="{{ asset('img/fasilitas-perpustakaan-4.jpg') }}" alt="Perpustakaan 4" class="h-full w-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] px-5 pb-[60px] pt-[30px] text-white opacity-0 transition-opacity duration-300 group-hover/slide:opacity-100">
                                        <p class="font-semibold text-lg">Pojok Baca Anak</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <button type="button" x-on:click="sebelumnya()" aria-label="Foto sebelumnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 left-2.5 md:left-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" x-on:click="berikutnya()" aria-label="Foto berikutnya" class="absolute top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-white/90 opacity-0 shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-white hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] group-hover/carousel:pointer-events-auto group-hover/carousel:opacity-100 md:h-12 md:w-12 right-2.5 md:right-5">
                            <svg class="h-6 w-[60px] text-[#2016ac]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-2.5">
                            <template x-for="i in jumlah" :key="i">
                                <button type="button" x-on:click="ke(i - 1)" :aria-label="'Foto ' + i" class="h-3 cursor-pointer border-0 transition-all duration-300"
                                    :class="aktif === i - 1 ? 'w-8 rounded-md bg-white' : 'w-3 rounded-full bg-white/50 hover:bg-white/80'"></button>
                            </template>
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
                @if(isset($galleryContent['items']) && is_array($galleryContent['items']))
                    @foreach($galleryContent['items'] as $item)
                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}" alt="{{ $item['title'] ?? 'Fasilitas' }}"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">{{ $item['title'] ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset('img/gallery-fasilitas-1.jpg') }}" alt="Fasilitas 1"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">Aula</p>
                        </div>
                    </div>

                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset('img/gallery-fasilitas-2.jpg') }}" alt="Fasilitas 2"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">Area Bermain Anak</p>
                        </div>
                    </div>

                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset('img/gallery-fasilitas-3.jpg') }}" alt="Fasilitas 3"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">Alat Terapi</p>
                        </div>
                    </div>

                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset('img/gallery-fasilitas-4.jpg') }}" alt="Fasilitas 4"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">Perpustakaan</p>
                        </div>
                    </div>

                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset('img/gallery-fasilitas-5.jpg') }}" alt="Fasilitas 5"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">Ruang Serbaguna</p>
                        </div>
                    </div>

                    <div class="group relative h-64 overflow-hidden rounded-2xl" x-saat-terlihat="animate-fade-in-up-lambat">
                        <img loading="lazy" decoding="async" src="{{ asset('img/gallery-fasilitas-6.jpg') }}" alt="Fasilitas 6"
                            class="w-full h-full object-cover transition-all duration-500 group-hover:rotate-2 group-hover:scale-[1.15]">
                        <div class="absolute inset-0 flex items-end bg-[linear-gradient(to_top,rgba(0,0,0,0.7),transparent)] p-6 opacity-0 transition-opacity duration-[400ms] group-hover:opacity-100">
                            <p class="text-white font-semibold">Lapangan Upacara</p>
                        </div>
                    </div>
                @endif
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
                @if(isset($statsContent) && is_array($statsContent) && count($statsContent) > 0)
                    @foreach($statsContent as $index => $stat)
                        @php
                            $styleIndex = $loop->index % 4;
                            $colors = ['text-primary', 'text-accent-orange', 'text-accent-yellow', 'text-secondary'];
                            $bgColors = ['bg-primary/10', 'bg-accent-orange/10', 'bg-accent-yellow/10', 'bg-secondary/10'];
                        @endphp
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto mb-4 {{ $bgColors[$styleIndex] }} rounded-2xl flex items-center justify-center">
                                <svg class="w-10 h-10 {{ $colors[$styleIndex] }}" fill="currentColor" viewBox="0 0 20 20">
                                    @if($styleIndex == 0)
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                    @elseif($styleIndex == 1)
                                         <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    @elseif($styleIndex == 2)
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    @else
                                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                    @endif
                                </svg>
                            </div>
                            <p class="text-4xl font-bold text-gray-800 mb-2">{{ $stat['value'] ?? '0' }}</p>
                            <p class="text-gray-600">{{ $stat['label'] ?? 'Statistik' }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-primary/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                            </svg>
                        </div>
                        <p class="text-4xl font-bold text-gray-800 mb-2">20+</p>
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
                        <p class="text-4xl font-bold text-gray-800 mb-2">20+</p>
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
                        <p class="text-4xl font-bold text-gray-800 mb-2">3+</p>
                        <p class="text-gray-600">Area Bermain</p>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-secondary/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                            </svg>
                        </div>
                        <p class="text-4xl font-bold text-gray-800 mb-2">1000+</p>
                        <p class="text-gray-600">Koleksi Buku</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- ==================== CTA SECTION (KEEP ORIGINAL) ==================== -->
    <section class="py-20 bg-[linear-gradient(135deg,#165fac_0%,#287f3b_100%)]">
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


@endsection
