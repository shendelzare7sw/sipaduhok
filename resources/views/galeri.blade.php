<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKBM House Of Knowledge - Galeri</title>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#165fac',
                        'secondary': '#287f3b',
                        'accent-orange': '#d45930',
                        'accent-yellow': '#fac030',
                        'accent-bright': '#ffe400',
                        'cream': '#e8e7e2'
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Gallery Item Hover Effect */
        .gallery-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .gallery-item img {
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.15);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .gallery-info {
            transform: translateY(0);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            animation: zoomIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Filter Button Active State */
        .filter-btn {
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(22, 95, 172, 0.3);
        }

        /* Masonry Grid */
        .masonry-grid {
            column-count: 1;
            column-gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .masonry-grid {
                column-count: 2;
            }
        }

        @media (min-width: 1024px) {
            .masonry-grid {
                column-count: 3;
            }
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1.5rem;
        }

        /* Scroll Animations */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
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
                        <img src="{{ asset($item['image'] ?? 'img/gallery-1.jpg') }}" alt="{{ $item['title'] ?? '' }}"
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Filter Functionality
            const filterBtns = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.masonry-item');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const filter = this.getAttribute('data-filter');

                    // Update active state
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Filter items
                    galleryItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.style.display = 'block';
                            setTimeout(() => {
                                item.classList.add('active');
                            }, 100);
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('active');
                        }
                    });
                });
            });

            // Scroll Reveal Animation
            const revealElements = document.querySelectorAll('.scroll-reveal');

            const revealOnScroll = () => {
                revealElements.forEach(el => {
                    const elementTop = el.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;

                    if (elementTop < windowHeight - 100) {
                        el.classList.add('active');
                    }
                });
            };

            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll();

            // Modal Functionality
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            const modalDate = document.getElementById('modalDate');
            const closeModal = document.getElementById('closeModal');

            let currentImageIndex = 0;
            const galleryItemsArray = Array.from(document.querySelectorAll('.gallery-item'));

            galleryItems.forEach((item, index) => {
                item.addEventListener('click', function () {
                    currentImageIndex = index;
                    openModal(this);
                });
            });

            function openModal(item) {
                const img = item.querySelector('img');
                const title = item.querySelector('h3').textContent;
                const date = item.querySelector('.gallery-info p').textContent;

                modal.classList.add('active');
                modalImg.src = img.src;
                modalTitle.textContent = title;
                modalDate.textContent = date;
                document.body.style.overflow = 'hidden';
            }

            closeModal.addEventListener('click', function () {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });

            // Modal Navigation
            document.getElementById('prevImage').addEventListener('click', function (e) {
                e.stopPropagation();
                currentImageIndex = (currentImageIndex - 1 + galleryItemsArray.length) % galleryItemsArray.length;
                openModal(galleryItemsArray[currentImageIndex]);
            });

            document.getElementById('nextImage').addEventListener('click', function (e) {
                e.stopPropagation();
                currentImageIndex = (currentImageIndex + 1) % galleryItemsArray.length;
                openModal(galleryItemsArray[currentImageIndex]);
            });

            // Keyboard Navigation
            document.addEventListener('keydown', function (e) {
                if (modal.classList.contains('active')) {
                    if (e.key === 'ArrowLeft') {
                        document.getElementById('prevImage').click();
                    } else if (e.key === 'ArrowRight') {
                        document.getElementById('nextImage').click();
                    } else if (e.key === 'Escape') {
                        closeModal.click();
                    }
                }
            });

            // Load More Button (Demo)
            //const loadMoreBtn = document.getElementById('loadMoreBtn');
            //loadMoreBtn.addEventListener('click', function() {
            //alert('Fitur "Muat Lebih Banyak" akan menampilkan galeri tambahan dari database.');
            //});
        });
    </script>
</body>

</html>