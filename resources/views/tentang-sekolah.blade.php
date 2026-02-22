<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sekolah - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/about.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-50">

    <x-navbar></x-navbar>

    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
    @endphp
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
                <span class="font-semibold">Tentang Sekolah</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Tentang Sekolah' }}</h1>
        </div>
    </section>

    @php
        $aboutSection = $page->getSection('intro');
        $aboutContent = $aboutSection->content ?? [];

        $historySection = $page->getSection('history');
        $historyContent = $historySection->content ?? [];
        $historyHeader = $historyContent['header'] ?? [];
        $historyItems = $historyContent['items'] ?? [];

        $whySection = $page->getSection('why_choose_us');
        $whyContent = $whySection->content ?? [];
        $whyHeader = $whyContent['header'] ?? [];
        $whyItems = $whyContent['items'] ?? [];

        $ctaSection = $page->getSection('cta');
        $ctaContent = $ctaSection->content ?? [];
    @endphp
    <!-- About Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Image -->
                <div class="relative">
                    <img src="{{ asset($aboutContent['image'] ?? 'img/about-school.jpg') }}" alt="Tentang Sekolah"
                        class="rounded-2xl shadow-xl w-full h-[400px] object-cover">
                    <div
                        class="absolute -bottom-6 -right-6 bg-[#165fac] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                        <p class="text-4xl font-bold">{{ $aboutContent['stats_years'] ?? '14+' }}</p>
                        <p class="text-sm">Tahun Pengalaman</p>
                    </div>
                </div>

                <!-- Text -->
                <div>
                    <span
                        class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $aboutContent['badge'] ?? 'Tentang Kami' }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        {{ $aboutContent['title'] ?? 'PKBM House Of Knowledge' }}</h2>
                    <div class="text-gray-600 mb-6 leading-relaxed space-y-4">
                        {!! nl2br(e($aboutContent['content'] ?? 'PKBM House Of Knowledge adalah lembaga pendidikan yang berdedikasi untuk memberikan pendidikan berkualitas bagi semua kalangan.')) !!}
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Kurikulum Terbaru</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Guru Profesional</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Fasilitas Lengkap</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Lingkungan Nyaman</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- History Section -->
    @if(!empty($historyItems))
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">
                {{ $historyHeader['badge'] ?? 'Perjalanan Kami' }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">{{ $historyHeader['title'] ?? 'Sejarah PKBM House Of Knowledge' }}</h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                {{ $historyHeader['description'] ?? '' }}
            </p>
        </div>
    </section>

    <!-- Timeline -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 relative">

            <div class="timeline-line hidden md:block"></div>

            <div class="space-y-12">

                @foreach($historyItems as $index => $item)
                    <div class="flex flex-col md:flex-row{{ $index % 2 != 0 ? '-reverse' : '' }} items-center gap-8">
                        <div class="md:w-1/2 {{ $index % 2 == 0 ? 'md:text-right md:pr-12' : 'md:text-left md:pl-12' }}">
                            <div class="bg-gray-50 rounded-2xl p-6 shadow-lg border-l-4 {{ $index % 2 == 0 ? 'md:border-l-0 md:border-r-4' : '' }}" 
                                 style="border-color: {{ $item['color'] ?? '#165fac' }}">
                                <span class="inline-block text-white px-4 py-1 rounded-full text-sm font-bold mb-3"
                                      style="background-color: {{ $item['color'] ?? '#165fac' }}">
                                    {{ $item['year'] ?? '' }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $item['title'] ?? '' }}</h3>
                                <p class="text-gray-600">{{ $item['description'] ?? '' }}</p>
                            </div>
                        </div>
                        
                        <div class="hidden md:flex w-8 h-8 rounded-full items-center justify-center z-10"
                             style="background-color: {{ $item['color'] ?? '#165fac' }}">
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        </div>

                        <div class="md:w-1/2 {{ $index % 2 == 0 ? 'md:pl-12' : 'md:pr-12' }}">
                            <img src="{{ asset($item['image'] ?? 'img/placeholder.jpg') }}"
                                class="rounded-2xl shadow-lg w-full h-48 object-cover">
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </section>
    @endif


    <!-- Why Choose Us -->
    @if(!empty($whyItems))
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $whyHeader['badge'] ?? 'Keunggulan Kami' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $whyHeader['title'] ?? 'Mengapa Memilih Kami?' }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($whyItems as $item)
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: {{ $item['icon_color'] ?? '#165fac' }}1A;"> 
                         @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                             <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] ?? 'Icon' }}" class="w-8 h-8 object-contain">
                         @else
                             <svg class="w-8 h-8" style="color: {{ $item['icon_color'] ?? '#165fac' }};" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                             </svg>
                         @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $item['title'] ?? '' }}</h3>
                    <p class="text-gray-600">{{ $item['description'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">{{ $ctaContent['title'] ?? 'Tertarik Bergabung?' }}</h2>
            <p class="text-white/90 mb-8">{{ $ctaContent['description'] ?? 'Daftarkan putra-putri Anda sekarang dan berikan pendidikan terbaik untuk masa depan yang cerah' }}</p>
            <a href="{{ $ctaContent['button_link'] ?? url('/ppdb') }}"
                class="inline-flex items-center px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition">
                {{ $ctaContent['button_text'] ?? 'Daftar Sekarang' }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </section>

    <x-footer></x-footer>

</body>

</html>