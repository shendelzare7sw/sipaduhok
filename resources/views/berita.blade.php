<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo-meta title="Berita & Artikel - PKBM House Of Knowledge" description="Kumpulan berita, artikel, dan pengumuman terbaru dari kegiatan PKBM House Of Knowledge."></x-seo-meta>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/berita.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-white">

    <x-navbar></x-navbar>

    <!-- PAGE HEADER -->
    <section class="relative py-24"
        style="background-image: url('{{ asset('img/bg-berita.jpg') }}');
               background-size: cover;
               background-position: center;">

        <div class="hero-overlay absolute inset-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <nav class="flex justify-center mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2 text-white/80">
                        <li class="inline-flex items-center">
                            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-white font-medium">Berita</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Berita & Informasi
                </h1>
                <p class="text-lg text-white/90 max-w-2xl mx-auto">
                    Ikuti perkembangan terbaru dari kegiatan dan prestasi PKBM House Of Knowledge
                </p>
            </div>
        </div>
    </section>

    <!-- SEARCH & FILTER SECTION -->
    <section class="py-12 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ url('/berita') }}" class="flex flex-col md:flex-row gap-6 items-center justify-between">

                <!-- Search Box -->
                <div class="w-full md:w-96">
                    <div class="search-box relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari berita..."
                            class="w-full px-6 py-4 pr-12 rounded-full border-2 border-gray-200 focus:border-primary focus:outline-none transition"
                        >
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-3 justify-center">
                    <button type="submit" name="kategori" value="all"
                            class="filter-btn {{ $kategori === 'all' ? 'active' : '' }} px-6 py-3 bg-white rounded-full font-medium shadow-md hover:shadow-lg">
                        Semua
                    </button>
                    <button type="submit" name="kategori" value="kegiatan"
                            class="filter-btn {{ $kategori === 'kegiatan' ? 'active' : '' }} px-6 py-3 bg-white rounded-full font-medium shadow-md hover:shadow-lg">
                        Kegiatan
                    </button>
                    <button type="submit" name="kategori" value="prestasi"
                            class="filter-btn {{ $kategori === 'prestasi' ? 'active' : '' }} px-6 py-3 bg-white rounded-full font-medium shadow-md hover:shadow-lg">
                        Prestasi
                    </button>
                    <button type="submit" name="kategori" value="pengumuman"
                            class="filter-btn {{ $kategori === 'pengumuman' ? 'active' : '' }} px-6 py-3 bg-white rounded-full font-medium shadow-md hover:shadow-lg">
                        Pengumuman
                    </button>
                    <button type="submit" name="kategori" value="artikel"
                            class="filter-btn {{ $kategori === 'artikel' ? 'active' : '' }} px-6 py-3 bg-white rounded-full font-medium shadow-md hover:shadow-lg">
                        Artikel
                    </button>
                    <button type="submit" name="kategori" value="ujian"
                            class="filter-btn {{ $kategori === 'ujian' ? 'active' : '' }} px-6 py-3 bg-white rounded-full font-medium shadow-md hover:shadow-lg">
                        Ujian
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- NEWS GRID SECTION -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($beritaUtama)
            <!-- Featured News -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-gray-800 mb-8 flex items-center">
                    <span class="w-1 h-8 bg-primary mr-3"></span>
                    Berita Utama
                </h2>

                <div class="news-card bg-white rounded-3xl shadow-xl overflow-hidden grid md:grid-cols-2 gap-0">
                    <div class="news-image-wrapper h-80 md:h-auto">
                        <img loading="lazy" decoding="async" src="{{ $beritaUtama->gambar_url }}" alt="{{ $beritaUtama->judul }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-8 md:p-12 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="category-badge inline-block px-4 py-1.5 {{ $beritaUtama->kategori_badge_class }} text-xs font-medium rounded-full">
                                {{ $beritaUtama->kategori_label }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $beritaUtama->tanggal_format_indonesia }}</span>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 hover:text-primary transition">
                            {{ $beritaUtama->judul }}
                        </h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            {{ $beritaUtama->deskripsi_singkat }}
                        </p>
                        <a href="{{ $beritaUtama->url_berita }}" target="_blank" rel="noopener" class="inline-flex items-center text-primary font-semibold hover:text-secondary transition group">
                            Baca Selengkapnya
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- News Grid -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-8 flex items-center">
                    <span class="w-1 h-8 bg-primary mr-3"></span>
                    Berita Terbaru
                </h2>

                @if($beritaList->count() > 0)
                    <div id="newsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($beritaList as $index => $item)
                            <div class="news-card bg-white rounded-2xl shadow-lg overflow-hidden fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                                <div class="news-image-wrapper h-56">
                                    <img loading="lazy" decoding="async" src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="category-badge inline-block px-3 py-1 {{ $item->kategori_badge_class }} text-xs font-medium rounded-full">
                                            {{ $item->kategori_label }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ $item->tanggal_berita->format('d M Y') }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-primary transition">
                                        {{ $item->judul }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                        {{ $item->deskripsi_singkat }}
                                    </p>
                                    <a href="{{ $item->url_berita }}" target="_blank" rel="noopener" class="inline-flex items-center text-primary font-semibold hover:text-secondary transition text-sm group">
                                        Selengkapnya
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($beritaList->hasPages())
                        <div class="mt-12 flex justify-center">
                            {{ $beritaList->appends(['kategori' => $kategori, 'search' => $search])->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <p class="mt-4 text-xl text-gray-500">Tidak ada berita ditemukan</p>
                        <p class="text-gray-400 mt-2">Coba ubah filter atau kata kunci pencarian</p>
                    </div>
                @endif
            </div>

        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-20" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Ingin Tahu Lebih Banyak?
            </h2>
            <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                Hubungi kami untuk informasi lebih lanjut tentang program dan kegiatan di PKBM House Of Knowledge
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/kontak') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Hubungi Kami
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ url('/ppdb') }}" class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-semibold rounded-full transition-all duration-300">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

    @vite(['resources/js/navbar.js'])
</body>
</html>

