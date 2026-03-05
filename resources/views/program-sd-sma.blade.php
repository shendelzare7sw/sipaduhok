<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Program Pendidikan SD - SMP - SMA | PKBM House Of Knowledge" description="Program pendidikan SD, SMP, dan SMA di PKBM House Of Knowledge menggabungkan kurikulum formal dengan pembelajaran praktis untuk mempersiapkan masa depan cerah." keywords="SD, SMP, SMA, program pendidikan, sekolah menengah, kurikulum nasional, Kejar Paket"></x-seo-meta>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/program-sd-sma.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body class="bg-gray-50">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        // Get Paket A, B, C sections
        $paketASection = $page->getSection('paket_a');
        $paketAContent = $paketASection->content ?? [];
        $mataPelajaranASection = $page->getSection('mata_pelajaran_a');
        $mataPelajaranAContent = $mataPelajaranASection->content ?? [];

        $paketBSection = $page->getSection('paket_b');
        $paketBContent = $paketBSection->content ?? [];
        $mataPelajaranBSection = $page->getSection('mata_pelajaran_b');
        $mataPelajaranBContent = $mataPelajaranBSection->content ?? [];
        $keunggulanBSection = $page->getSection('keunggulan_b');
        $keunggulanBContent = $keunggulanBSection->content ?? [];

        $paketCSection = $page->getSection('paket_c');
        $paketCContent = $paketCSection->content ?? [];
        $jurusanCSection = $page->getSection('jurusan_c');
        $jurusanCContent = $jurusanCSection->content ?? [];
        $prospekCSection = $page->getSection('prospek_c');
        $prospekCContent = $prospekCSection->content ?? [];
    @endphp

    <x-navbar></x-navbar>

    <!-- HERO -->
    <section class="relative h-[350px] flex items-center justify-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Program Pendidikan' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Program Pendidikan Kesetaraan' }}
            </h1>
            <p class="mt-4 text-white/90">{{ $heroContent['subtitle'] ?? 'Paket A • Paket B • Paket C' }}</p>
        </div>
    </section>

    <!-- SWITCH TAB -->
    <section class="py-10 bg-white shadow-sm border-b">
        <div class="max-w-5xl mx-auto px-4 flex justify-center gap-4">
            <button class="tab-btn px-6 py-3 rounded-full border font-medium" data-tab="sd">Paket A (SD)</button>
            <button class="tab-btn px-6 py-3 rounded-full border font-medium" data-tab="smp">Paket B (SMP)</button>
            <button class="tab-btn px-6 py-3 rounded-full border font-medium" data-tab="sma">Paket C (SMA)</button>
        </div>
    </section>

    <!-- SD • Paket A -->
    <div id="tab-sd" class="tab-content">
        @include('partials.program-sd-content', ['content' => $paketAContent, 'mataPelajaran' => $mataPelajaranAContent])
    </div>

    <!-- SMP • Paket B -->
    <div id="tab-smp" class="tab-content hidden">
        @include('partials.program-smp-content', ['content' => $paketBContent, 'mataPelajaran' => $mataPelajaranBContent, 'keunggulan' => $keunggulanBContent])
    </div>

    <!-- SMA • Paket C -->
    <div id="tab-sma" class="tab-content hidden">
        @include('partials.program-sma-content', ['content' => $paketCContent, 'jurusan' => $jurusanCContent, 'prospek' => $prospekCContent])
    </div>




    <x-footer />

    @vite(['resources/js/navbar.js', 'resources/js/pages/program-sd-sma.js'])
</body>

</html>
