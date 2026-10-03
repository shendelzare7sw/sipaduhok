@extends('layouts.landing')

@section('seo')
    <x-seo-meta title="Program Pendidikan SD - SMP - SMA | PKBM House Of Knowledge" description="Program pendidikan SD, SMP, dan SMA di PKBM House Of Knowledge menggabungkan kurikulum formal dengan pembelajaran praktis untuk mempersiapkan masa depan cerah." keywords="SD, SMP, SMA, program pendidikan, sekolah menengah, kurikulum nasional, Kejar Paket"></x-seo-meta>
@endsection

@section('body_class', 'bg-gray-50')

@section('content')

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


    <!-- HERO -->
    <section class="relative h-[350px] flex items-center justify-center">
        <img src="{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-center">
        <div class="bg-[linear-gradient(135deg,rgba(22,95,172,0.9)_0%,rgba(40,127,59,0.8)_100%)] absolute inset-0"></div>
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

    <div x-data="{ tab: 'sd' }">
    <!-- SWITCH TAB -->
    <section class="py-10 bg-white shadow-sm border-b">
        <div class="max-w-5xl mx-auto px-4 flex justify-center gap-4">
            @foreach(['sd' => 'Paket A (SD)', 'smp' => 'Paket B (SMP)', 'sma' => 'Paket C (SMA)'] as $kunciTab => $labelTab)
                <button type="button" x-on:click="tab = '{{ $kunciTab }}'" :class="tab === '{{ $kunciTab }}' && 'bg-[#165fac] text-white'" class="px-6 py-3 rounded-full border font-medium">{{ $labelTab }}</button>
            @endforeach
        </div>
    </section>

    @php
        $transisiTab = 'x-transition:enter="transition ease-out duration-[350ms]" x-transition:enter-start="opacity-0 translate-y-2.5" x-transition:enter-end="opacity-100 translate-y-0"';
    @endphp
    <!-- SD • Paket A -->
    <div x-show="tab === 'sd'" {!! $transisiTab !!}>
        @include('partials.program-sd-content', ['content' => $paketAContent, 'mataPelajaran' => $mataPelajaranAContent])
    </div>

    <!-- SMP • Paket B -->
    <div x-show="tab === 'smp'" x-cloak {!! $transisiTab !!}>
        @include('partials.program-smp-content', ['content' => $paketBContent, 'mataPelajaran' => $mataPelajaranBContent, 'keunggulan' => $keunggulanBContent])
    </div>

    <!-- SMA • Paket C -->
    <div x-show="tab === 'sma'" x-cloak {!! $transisiTab !!}>
        @include('partials.program-sma-content', ['content' => $paketCContent, 'jurusan' => $jurusanCContent, 'prospek' => $prospekCContent])
    </div>
    </div>
@endsection
