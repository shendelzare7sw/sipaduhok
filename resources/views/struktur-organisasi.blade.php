<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
    @endphp
    <x-seo-meta title="{{ $heroContent['title'] ?? 'Struktur Organisasi' }} - PKBM House Of Knowledge" description="Struktur organisasi dan manajemen PKBM House Of Knowledge yang profesional dalam mendukung operasional pendidikan berkualitas sehari-hari." keywords="struktur organisasi, manajemen sekolah, tim profesional, hierarki pendidikan"></x-seo-meta>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/struktur-organisasi.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-50">

    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Profil</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Struktur Organisasi' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Struktur Organisasi' }}</h1>
        </div>
    </section>

    @php
        $headerSection = $page->getSection('header');
        $headerContent = $headerSection->content ?? [];

        $leadersSection = $page->getSection('leaders');
        $leaders = $leadersSection->content ?? [];

        $staffSection = $page->getSection('staff');
        $staffMembers = $staffSection->content ?? [];

        $coordSection = $page->getSection('coordinators');
        $coordContent = $coordSection->content ?? [];
        $coordHeader = $coordContent['header'] ?? [];
        $coordinators = $coordContent['items'] ?? [];

        $colorMap = [
            'primary' => '#165fac',
            'secondary' => '#287f3b',
            'accent-orange' => '#d45930',
            'accent-yellow' => '#fac030',
        ];
    @endphp

    <!-- Org Chart Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $headerContent['badge'] ?? 'Organisasi' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                    {{ $headerContent['title'] ?? 'Struktur Organisasi PKBM' }}</h2>
                <p class="text-gray-600 mt-4">{{ $headerContent['subtitle'] ?? 'House Of Knowledge' }}</p>
            </div>

            <!-- Org Chart -->
            <div class="flex flex-col items-center">
                @foreach($leaders as $index => $leader)
                    @php
                        $rawColor = $leader['color'] ?? 'primary';
                        $leaderColor = (str_starts_with($rawColor, '#')) ? $rawColor : ($colorMap[$rawColor] ?? '#165fac');
                    @endphp
                    <!-- Leader Card -->
                    <div class="org-card text-white rounded-2xl p-6 text-center shadow-xl mb-8"
                        style="background-color: {{ $leaderColor }}">
                        <img src="{{ asset($leader['image'] ?? 'img/guru-1.png') }}" alt="{{ $leader['name'] }}"
                            class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white">
                        <h3 class="font-bold text-lg">{{ $leader['name'] }}</h3>
                        <p class="text-white/80 text-sm">{{ $leader['position'] }}</p>
                    </div>

                    @if(!$loop->last)
                        <!-- Connector -->
                        <div class="w-1 h-12 bg-[#165fac]"></div>
                    @endif
                @endforeach

                <!-- Connector before staff -->
                <div class="w-1 h-12 bg-[#165fac]"></div>

                <!-- Staff Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl mb-8">
                    @foreach($staffMembers as $staff)
                        @php
                            $rawStaffColor = $staff['color'] ?? 'primary';
                            $staffColor = (str_starts_with($rawStaffColor, '#')) ? $rawStaffColor : ($colorMap[$rawStaffColor] ?? '#165fac');
                        @endphp
                        <div class="org-card bg-white rounded-2xl p-6 text-center shadow-lg border-t-4"
                            style="border-color: {{ $staffColor }}">
                            <img src="{{ asset($staff['image'] ?? 'img/guru-1.png') }}" alt="{{ $staff['name'] }}"
                                class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                            <h3 class="font-bold text-gray-800">{{ $staff['name'] }}</h3>
                            <p class="text-gray-600 text-sm">{{ $staff['position'] }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Coordinators Section -->
                <div class="w-full max-w-5xl">
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-6">
                        {{ $coordHeader['title'] ?? 'Koordinator Program' }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($coordinators as $coord)
                            <div class="org-card bg-gray-50 rounded-xl p-4 text-center shadow hover:bg-white">
                                <img src="{{ asset($coord['image'] ?? 'img/guru-1.png') }}"
                                    alt="{{ $coord['name'] }}" class="w-16 h-16 rounded-full mx-auto mb-3 object-cover">
                                <h4 class="font-semibold text-gray-800 text-sm">{{ $coord['name'] }}</h4>
                                <p class="text-gray-500 text-xs">{{ $coord['department'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

    @vite(['resources/js/navbar.js'])
</body>

</html>
