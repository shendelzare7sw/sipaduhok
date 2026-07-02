<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="PPDB - PKBM House Of Knowledge" description="Penerimaan Peserta Didik Baru (PPDB) PKBM House Of Knowledge. Daftar sekarang untuk masa depan pendidikan yang lebih baik."></x-seo-meta>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/ppdb.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-white">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $quickInfoSection = $page->getSection('quick_info');
        $quickInfoContent = $quickInfoSection->content ?? [];

        $alurSection = $page->getSection('alur');
        $alurContent = $alurSection->content ?? [];
        $alurHeader = $alurContent['header'] ?? $alurContent;
        $alurItems = $alurContent['items'] ?? [];
        if (empty($alurItems)) {
            $alurItems = [
                ['title' => 'Isi Formulir', 'description' => 'Datang ke cabang Gedung Utama dan mengisi formulir yang diberikan administrator.'],
                ['title' => 'Melengkapi Dokumen', 'description' => 'Melengkapi berkas persyaratan yang diperlukan'],
                ['title' => 'Verifikasi', 'description' => 'Tim kami akan memverifikasi data dan dokumen Anda'],
                ['title' => 'Wawancara', 'description' => 'Ikuti sesi wawancara singkat dengan tim kami'],
                ['title' => 'Pengumuman', 'description' => 'Terima pengumuman hasil dan mulai belajar!'],
            ];
        }
        $alurIconSet = [
            ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['bg' => 'bg-secondary/10', 'text' => 'text-secondary', 'path' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
            ['bg' => 'bg-accent-yellow/10', 'text' => 'text-accent-yellow', 'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['bg' => 'bg-accent-orange/10', 'text' => 'text-accent-orange', 'path' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
            ['bg' => 'bg-accent-bright/20', 'text' => 'text-secondary', 'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];

        // Syarat Pendaftaran Sections
        $syaratHeaderSection = $page->getSection('syarat_header');
        $syaratHeaderContent = $syaratHeaderSection->content ?? [];

        $syaratSectionKeys = ['syarat_paud', 'syarat_paket_a', 'syarat_paket_b', 'syarat_paket_c', 'syarat_inklusi'];
        $syaratTabs = collect($syaratSectionKeys)
            ->map(fn($key) => $page->getSection($key))
            ->filter(fn($s) => $s && $s->is_visible)
            ->values();

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

    <section class="relative min-h-screen flex items-center bg-cover bg-center bg-no-repeat overflow-hidden"
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
                    {{ $alurHeader['badge'] ?? 'Langkah Mudah' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $alurHeader['title'] ?? 'Alur Pendaftaran' }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $alurHeader['description'] ?? 'Ikuti langkah mudah untuk mendaftar sebagai peserta didik baru' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-8 relative">
                @foreach($alurItems as $idx => $step)
                    @php
                        $icon = $alurIconSet[$idx % count($alurIconSet)];
                        $isLast = $loop->last;
                        $gradient = $isLast ? 'from-secondary to-primary' : 'from-primary to-secondary';
                    @endphp
                    <div class="{{ $isLast ? 'relative' : 'step-connector relative' }}">
                        <div class="card-hover bg-white rounded-2xl shadow-lg p-6 text-center {{ $isLast ? 'border-2 border-secondary' : '' }}">
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br {{ $gradient }} rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                {{ $idx + 1 }}
                            </div>
                            <div class="w-12 h-12 mx-auto mb-4 {{ $icon['bg'] }} rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 {{ $icon['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $icon['path'] }}" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $step['title'] ?? '' }}</h3>
                            <p class="text-sm text-gray-600">{{ $step['description'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- Syarat Pendaftaran -->
    @if(($syaratHeaderSection?->is_visible ?? true) && $syaratTabs->isNotEmpty())
    @php
        $syaratTabKey = fn($sectionKey) => str_replace('_', '-', preg_replace('/^syarat_/', '', $sectionKey));
    @endphp
    <section id="syarat" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-2 bg-secondary/10 text-secondary text-sm font-medium rounded-full mb-4">
                    {{ $syaratHeaderContent['badge'] ?? 'Persyaratan' }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    {{ $syaratHeaderContent['title'] ?? 'Syarat Pendaftaran' }}
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $syaratHeaderContent['description'] ?? 'Siapkan dokumen-dokumen berikut untuk melengkapi pendaftaran Anda' }}
                </p>
            </div>

            <!-- Tabs -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                @foreach($syaratTabs as $tabIdx => $tab)
                    @php
                        $tabKey = $syaratTabKey($tab->section_key);
                        $tabLabel = $tab->content['header']['tab_label'] ?? ucfirst($tabKey);
                    @endphp
                    <button class="tab-button {{ $tabIdx === 0 ? 'active' : '' }} px-6 py-3 rounded-full font-semibold bg-gray-100" data-tab="{{ $tabKey }}">
                        {{ $tabLabel }}
                    </button>
                @endforeach
            </div>

            <!-- Tab Contents -->
            <div class="max-w-4xl mx-auto">
                @foreach($syaratTabs as $tabIdx => $tab)
                    @php
                        $tabKey = $syaratTabKey($tab->section_key);
                        $tabHeader = $tab->content['header'] ?? [];
                        $tabItems = $tab->content['items'] ?? [];
                        $tabTitle = $tabHeader['title'] ?? '';
                        $tabColor = $tabHeader['color'] ?? '#165fac';
                        $tabIcon = $tabHeader['icon'] ?? 'fa-book';
                        if (!str_starts_with($tabIcon, 'fa')) {
                            $tabIcon = 'fas fa-' . ltrim($tabIcon, 'fa-');
                        } elseif (str_starts_with($tabIcon, 'fa-')) {
                            $tabIcon = 'fas ' . $tabIcon;
                        }
                        $tabNote = $tabHeader['note'] ?? null;
                    @endphp
                    <div class="tab-content {{ $tabIdx === 0 ? 'active' : '' }}" id="{{ $tabKey }}">
                        <div class="rounded-3xl p-8" style="background: linear-gradient(135deg, {{ $tabColor }}1a, {{ $tabColor }}0d);">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                <span class="w-10 h-10 rounded-full flex items-center justify-center text-white mr-3"
                                    style="background-color: {{ $tabColor }};">
                                    <i class="{{ $tabIcon }}"></i>
                                </span>
                                {{ $tabTitle }}
                            </h3>
                            <div class="space-y-4">
                                @foreach($tabItems as $item)
                                    @php $itemText = is_array($item) ? ($item['text'] ?? '') : $item; @endphp
                                    @if($itemText !== '')
                                        <div class="requirement-check flex items-start gap-3 bg-white p-4 rounded-xl">
                                            <div
                                                class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-gray-700">{{ $itemText }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                @if($tabNote)
                                    <div class="p-4 rounded-xl" style="background-color: {{ $tabColor }}1a;">
                                        <p class="text-sm text-gray-700 leading-relaxed">
                                            <strong>Catatan:</strong> {{ $tabNote }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Biaya Section -->
    @if($investasiSection?->is_visible)
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
                    'yellow' => ['hex' => '#fac030', 'border' => 'border-accent-yellow', 'bg' => 'bg-accent-yellow/10', 'gradient' => 'from-accent-yellow to-accent-bright'],
                    'blue' => ['hex' => '#165fac', 'border' => 'border-primary', 'bg' => 'bg-primary/10', 'gradient' => 'from-primary to-blue-600'],
                    'green' => ['hex' => '#287f3b', 'border' => 'border-secondary', 'bg' => 'bg-secondary/10', 'gradient' => 'from-secondary to-green-600'],
                    'orange' => ['hex' => '#d45930', 'border' => 'border-accent-orange', 'bg' => 'bg-accent-orange/10', 'gradient' => 'from-accent-orange to-red-600'],
                ];
                $iconMap = [
                    'yellow' => 'fa-palette',
                    'blue' => 'fa-book',
                    'green' => 'fa-book-open',
                    'orange' => 'fa-graduation-cap',
                ];
                $biayaSections = [
                    ['section' => $biayaPaudSection, 'content' => $biayaPaudContent, 'items' => $biayaPaudItems, 'modalId' => 'costModal', 'modalFunc' => 'showCostModal'],
                    ['section' => $biayaSdSection, 'content' => $biayaSdContent, 'items' => $biayaSdItems, 'modalId' => 'costModalPaketA', 'modalFunc' => 'showCostModalPaketA'],
                    ['section' => $biayaSmpSection, 'content' => $biayaSmpContent, 'items' => $biayaSmpItems, 'modalId' => 'costModalPaketB', 'modalFunc' => 'showCostModalPaketB'],
                    ['section' => $biayaSmaSection, 'content' => $biayaSmaContent, 'items' => $biayaSmaItems, 'modalId' => 'costModalPaketC', 'modalFunc' => 'showCostModalPaketC'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($biayaSections as $section)
                    @if(!($section['section']?->is_visible))
                        @continue
                    @endif
                    @php
                        $header = $section['content']['header'] ?? [];
                        $items = $section['items'] ?? [];
                        $color = $header['color'] ?? 'blue';

                        // Support both named colors and hex colors from admin editor
                        $isHex = str_starts_with($color, '#');
                        $hexColor = $isHex ? $color : ($colorMap[$color]['hex'] ?? '#165fac');

                        // Use inline styles for hex colors, CSS classes for named colors
                        $useInlineStyle = $isHex || !isset($colorMap[$color]);
                        $borderClass = $useInlineStyle ? '' : ($colorMap[$color]['border'] ?? '');
                        $bgClass = $useInlineStyle ? '' : ($colorMap[$color]['bg'] ?? '');
                        $gradientClass = $useInlineStyle ? '' : ($colorMap[$color]['gradient'] ?? '');

                        $icon = $header['icon'] ?? ($iconMap[$color] ?? 'fa-book');
                        // Prepend 'fas ' if icon doesn't start with 'fa'
                        if ($icon && !str_starts_with($icon, 'fa')) {
                            $icon = 'fas fa-' . ltrim($icon, 'fa-');
                        } elseif ($icon && str_starts_with($icon, 'fa-')) {
                            $icon = 'fas ' . $icon;
                        }
                        $image = $header['image'] ?? null;

                        // Calculate totals from items
                        $pokokItems = collect($items)->where('type', 'pokok');
                        $tambahanItems = collect($items)->where('type', 'tambahan');
                    @endphp
                    <div class="card-hover bg-white rounded-3xl shadow-xl p-8 border-t-4 {{ $borderClass }}"
                        @if($useInlineStyle) style="border-top-color: {{ $hexColor }}" @endif>
                        <div class="text-center mb-6">
                            <div
                                class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center overflow-hidden {{ $bgClass }}"
                                @if($useInlineStyle) style="background-color: {{ $hexColor }}15" @endif>
                                @if($image)
                                    <img loading="lazy" decoding="async" src="{{ asset($image) }}" alt="{{ $header['title'] ?? 'Icon' }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-3xl" @if($useInlineStyle) style="color: {{ $hexColor }}" @endif><i class="{{ $icon }}"></i></span>
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
                        <div class="price-badge text-white text-center py-3 rounded-xl font-bold cursor-pointer {{ $gradientClass ? 'bg-gradient-to-r ' . $gradientClass : '' }}"
                            @if($useInlineStyle) style="background: linear-gradient(to right, {{ $hexColor }}, {{ $hexColor }}cc)" @endif
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
    @endif

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
                            <img loading="lazy" decoding="async" src="{{ asset($biayaPaudContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
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
                        <li>â€¢ Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>â€¢ Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>â€¢ Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>â€¢ Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
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
                            <img loading="lazy" decoding="async" src="{{ asset($biayaSmaContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
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
                        <li>â€¢ Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>â€¢ Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>â€¢ Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>â€¢ Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
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
                            <img loading="lazy" decoding="async" src="{{ asset($biayaSdContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
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
                        <li>â€¢ Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>â€¢ Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>â€¢ Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>â€¢ Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
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
                            <img loading="lazy" decoding="async" src="{{ asset($biayaSmpContent['header']['image']) }}" alt="Icon" class="w-full h-full object-cover">
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
                        <li>â€¢ Biaya tambahan bersifat opsional dan dapat disesuaikan dengan kebutuhan siswa</li>
                        <li>â€¢ Tersedia program cicilan bulanan untuk memudahkan pembayaran</li>
                        <li>â€¢ Beasiswa tersedia untuk siswa berprestasi dan kurang mampu</li>
                        <li>â€¢ Biaya dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya</li>
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
    @vite(['resources/js/navbar.js', 'resources/js/pages/ppdb.js'])
</body>

</html>
