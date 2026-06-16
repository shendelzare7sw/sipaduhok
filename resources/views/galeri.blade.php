<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="PKBM House Of Knowledge - Galeri" description="Galeri foto dan dokumentasi kegiatan pembelajaran serta fasilitas di PKBM House Of Knowledge."></x-seo-meta>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/galeri.css'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-white">
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
    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 bg-cover bg-center bg-no-repeat"
        style="background-image: linear-gradient(135deg, rgba(22,95,172,0.75) 0%, rgba(40,127,59,0.75) 100%), url('{{ asset($heroContent['background_image'] ?? 'img/bg-galeri.jpg') }}');">

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

    <!-- Filter Section -->
    <section class="py-8 bg-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center gap-3">
                <button
                    class="filter-btn active px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-full hover:bg-gray-200"
                    data-filter="all">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button
                        class="filter-btn px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-full hover:bg-gray-200"
                        data-filter="{{ $category['key'] ?? '' }}">
                        {{ $category['label'] ?? '' }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Gallery Grid Section -->
    <section class="py-16 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="masonry-grid" id="galleryGrid">

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
                    <div class="masonry-item scroll-reveal gallery-item rounded-2xl overflow-hidden shadow-lg bg-white"
                        data-category="{{ $item['category'] ?? '' }}">
                        <img loading="lazy" decoding="async" src="{{ asset($item['image'] ?? 'img/gallery-1.jpg') }}" alt="{{ $item['title'] ?? '' }}"
                            class="w-full h-auto object-cover">
                        <div class="gallery-overlay"></div>
                        <div class="gallery-info">
                            <span class="inline-block px-3 py-1 {{ $badgeClass }} text-xs rounded-full mb-2">{{ $categoryLabel }}</span>
                            <h3 class="text-white font-bold text-lg mb-1">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-white/80 text-sm">{{ $item['date'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach

            </div>

            <!-- Load More Button
            <div class="text-center mt-12">
                <button id="loadMoreBtn" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Muat Lebih Banyak
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>-->
        </div>
    </section>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span
            class="absolute top-6 right-6 text-white text-5xl font-light cursor-pointer hover:text-accent-yellow transition z-10"
            id="closeModal">&times;</span>
        <button id="prevImage"
            class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button id="nextImage"
            class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <img class="modal-content rounded-lg" id="modalImage" src="" alt="Gallery Image">
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-center text-white">
            <h3 id="modalTitle" class="text-xl font-bold mb-1"></h3>
            <p id="modalDate" class="text-sm text-white/80"></p>
        </div>
    </div>

    <!-- CTA Section -->
    <section class="py-20" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
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

    <x-footer></x-footer>
    @vite(['resources/js/navbar.js', 'resources/js/pages/galeri.js'])
</body>

</html>
