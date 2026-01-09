<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKBM House Of Knowledge - Beranda</title>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
    body { font-family: 'Poppins', sans-serif; }

    /* Hero Section */
    .hero-overlay {
        background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.85) 100%);
    }

    /* Decorative Frame Tilt Style */
.decorative-frame-tilt {
    position: relative;
    padding: 20px;
}

.decorative-frame-tilt::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 100%;
    height: 100%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-radius: 1rem;
    z-index: -1;
    transform: rotate(6deg);
    transition: transform 0.5s ease;
}

.decorative-frame-tilt::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -30px;
    width: 70%;
    height: 70%;
    background: linear-gradient(135deg, #287f3b 0%, #165fac 100%);
    border-radius: 1rem;
    z-index: -2;
    opacity: 0.4;
    transform: rotate(-6deg);
    transition: transform 0.5s ease;
}

.decorative-frame-tilt:hover::before {
    transform: rotate(3deg);
}

.decorative-frame-tilt:hover::after {
    transform: rotate(-3deg);
}

    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    .float-animation { animation: float 6s ease-in-out infinite; }
    .bounce-animation { animation: bounce 2s infinite; }

    /* Card Hover */
    .card-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    /* 3D Carousel */
    .carousel-3d {
        perspective: 1000px;
        height: 450px;
    }

    .carousel-item {
        position: absolute;
        width: 280px;
        height: 380px;
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    }

    .carousel-item.center {
        transform: translateX(-50%) scale(1.1);
        left: 50%;
        z-index: 5;
        opacity: 1;
    }

    .carousel-item.left-1 {
        transform: translateX(-50%) scale(0.9) rotateY(15deg);
        left: 25%;
        z-index: 4;
        opacity: 0.9;
    }

    .carousel-item.left-2 {
        transform: translateX(-50%) scale(0.75) rotateY(25deg);
        left: 5%;
        z-index: 3;
        opacity: 0.6;
    }

    .carousel-item.right-1 {
        transform: translateX(-50%) scale(0.9) rotateY(-15deg);
        left: 75%;
        z-index: 4;
        opacity: 0.9;
    }

    .carousel-item.right-2 {
        transform: translateX(-50%) scale(0.75) rotateY(-25deg);
        left: 95%;
        z-index: 3;
        opacity: 0.6;
    }

    .carousel-item.hidden-item {
        opacity: 0;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .carousel-3d { height: 350px; }
        .carousel-item { width: 200px; height: 280px; }
        .carousel-item.left-2, .carousel-item.right-2 { opacity: 0.4; }
    }

    /* Gallery Hover */
    .gallery-item {
        overflow: hidden;
    }
    .gallery-item img {
        transition: transform 0.5s ease;
    }
    .gallery-item:hover img {
        transform: scale(1.1);
    }

    /* Contact Card Hover */
    .contact-hover:hover {
        transform: translateX(10px);
    }

    /* Scroll Indicator Animation */
.scroll-indicator {
    animation: float-gentle 3s ease-in-out infinite;
    opacity: 1;
    transition: opacity 0.5s ease;
}

@keyframes float-gentle {
    0%, 100% {
        transform: translate(-50%, 0);
    }
    50% {
        transform: translate(-50%, 10px);
    }
}

/* Mouse wheel scroll animation */
.scroll-wheel {
    animation: scroll-down 2s ease-in-out infinite;
}

@keyframes scroll-down {
    0% {
        opacity: 1;
        top: 0.75rem;
    }
    50% {
        opacity: 0.3;
        top: 1.5rem;
    }
    100% {
        opacity: 0;
        top: 1.5rem;
    }
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Scroll margin for sections */
section {
    scroll-margin-top: 100px;
}

/* Fade out scroll indicator on scroll - HANYA opacity */
.scroll-indicator.fade-out {
    opacity: 0 !important;
}
</style>
</head>
<body class="bg-white">

    <!-- Navbar Component -->
    <x-navbar></x-navbar>

    <!-- ==================== HERO SECTION ==================== -->
    <section class="relative min-h-screen flex items-center" style="background-image: url('{{ asset('img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>

        <!-- Decorative Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 border-4 border-white/20 rounded-full float-animation hidden lg:block"></div>
        <div class="absolute bottom-40 left-20 w-10 h-10 bg-accent-yellow/30 rounded-full float-animation hidden lg:block" style="animation-delay: 1s;"></div>
        <div class="absolute top-40 right-40 w-16 h-16 border-4 border-secondary/30 rounded-lg rotate-45 float-animation hidden lg:block" style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <span class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-medium rounded-full mb-6">
                        Selamat Datang di SipaduHOK!
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                        Sistem<br>
                        <span class="text-accent-yellow">Pembelajaran</span><br>
                        dan
                        <span class="text-accent-yellow">Akademik</span>
                    </h1>
                    <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed max-w-xl">
                        House Of Knowledge menyediakan media pembelajaran dan akademik berbasis website "SipaduHOK" sebagai media pembelajaran online yang lebih fleksibel
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#program" class="inline-flex items-center justify-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full shadow-lg transition-all duration-300 hover:-translate-y-1">
                            Jelajahi Sekarang
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <!--tambahan button
                        <a href="{{ url('/ppdb-alur') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full shadow-lg transition-all duration-300 hover:-translate-y-1">
                            Daftar Sekarang
                        </a> -->
                    </div>
                </div>

                <!-- Right Content - Decorative Image -->
                <div class="hidden lg:block">
                    <div class="decorative-frame-tilt relative">
                        <img src="{{ asset('img/hero-img.jpg') }}" alt="PKBM House of Knowledge" class="rounded-2xl shadow-2xl w-full h-[400px] object-cover transform rotate-6 hover:rotate-3 transition-transform duration-500">

                        <!-- Floating Badge 1 -->
                        <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl p-4 shadow-xl transform -rotate-6 hover:rotate-0 transition-transform duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-secondary rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800">14+</p>
                                    <p class="text-sm text-gray-500">Tahun Pengalaman</p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badge 2 -->
                        <div class="absolute -top-4 -right-4 bg-primary rounded-2xl p-4 shadow-xl transform rotate-6 hover:rotate-0 transition-transform duration-300">
                            <div class="text-center text-white">
                                <p class="text-2xl font-bold">200+</p>
                                <p class="text-xs">Siswa Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-20 left-1/2 transform -translate-x-1/2 z-20">
            <a href="#stats" class="scroll-indicator flex flex-col items-center text-white/80 hover:text-white transition-all duration-300 group">
                <span class="text-sm mb-3 font-medium tracking-wide">Scroll Down</span>
                <div class="relative w-8 h-12 border-2 border-white rounded-full flex items-center justify-center group-hover:border-accent-yellow transition-colors duration-300">
                    <div class="scroll-wheel absolute w-1 h-3 bg-white rounded-full top-3 group-hover:bg-accent-yellow transition-colors duration-300"></div>
                </div>
            </a>
        </div>
    </section>

    <!-- ==================== STATS SECTION ==================== -->
    <section id="stats" class="relative -mt-16 z-20 pb-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl p-8 grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Stat 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">200+</p>
                    <p class="text-gray-500 text-sm">Siswa Aktif</p>
                </div>
                <!-- Stat 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-secondary/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">50+</p>
                    <p class="text-gray-500 text-sm">Tenaga Pengajar</p>
                </div>
                <!-- Stat 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-accent-yellow/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-accent-yellow" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">14+</p>
                    <p class="text-gray-500 text-sm">Tahun Pengalaman</p>
                </div>
                <!-- Stat 4 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-accent-orange/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-accent-orange" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">98%</p>
                    <p class="text-gray-500 text-sm">Tingkat Kelulusan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== PROGRAM SECTION ==================== -->
    <section id="program" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    Program Kami
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Program PKBM<br>House Of Knowledge
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Alasan kenapa harus memilih untuk bergabung dengan PKBM House Of Knowledge?
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 - Pendidikan Inklusi -->
                <div class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-accent-orange group">
                    <div class="w-20 h-20 mx-auto mb-6 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:bg-accent-orange transition-colors duration-300">
                        <svg class="w-10 h-10 text-accent-orange group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pendidikan Inklusi</h3>
                    <p class="text-gray-600 text-sm mb-4">Program pendidikan untuk anak berkebutuhan khusus dengan pendekatan individual</p>
                    <a href="{{ url('/program-inklusi') }}" class="text-primary font-semibold hover:text-secondary transition inline-flex items-center">
                        Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Card 2 - Pendidikan Kesetaraan -->
                <div class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-primary group">
                    <div class="w-20 h-20 mx-auto mb-6 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-primary transition-colors duration-300">
                        <svg class="w-10 h-10 text-primary group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pendidikan Kesetaraan</h3>
                    <p class="text-gray-600 text-sm mb-4">Program Paket A, B, dan C untuk kesetaraan pendidikan SD, SMP, dan SMA</p>
                    <a href="{{ url('/program-sd-sma') }}" class="text-primary font-semibold hover:text-secondary transition inline-flex items-center">
                        Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Card 3 - Konseling ABK -->
                <div class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-secondary group">
                    <div class="w-20 h-20 mx-auto mb-6 bg-green-50 rounded-2xl flex items-center justify-center group-hover:bg-secondary transition-colors duration-300">
                        <svg class="w-10 h-10 text-secondary group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Konseling Anak Berkebutuhan Khusus</h3>
                    <p class="text-gray-600 text-sm mb-4">Layanan konseling profesional untuk mendukung tumbuh kembang anak</p>
                    <a href="{{ url('/program-terapi') }}" class="text-primary font-semibold hover:text-secondary transition inline-flex items-center">
                        Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Card 4 - PAUD -->
                <div class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-accent-yellow group">
                    <div class="w-20 h-20 mx-auto mb-6 bg-yellow-50 rounded-2xl flex items-center justify-center group-hover:bg-accent-yellow transition-colors duration-300">
                        <svg class="w-10 h-10 text-accent-yellow group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pendidikan Anak Usia Dini</h3>
                    <p class="text-gray-600 text-sm mb-4">Program PAUD dengan metode bermain sambil belajar yang menyenangkan</p>
                    <a href="{{ url('/program-paud-tk') }}" class="text-primary font-semibold hover:text-secondary transition inline-flex items-center">
                        Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== ABOUT SECTION ==================== -->
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left - Image -->
                <div class="relative">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                        <img src="{{ asset('img/about-img.jpg') }}" alt="Tentang PKBM" class="w-full h-[450px] object-cover">
                    </div>

                    <!-- Experience Badge -->
                    <div class="absolute -bottom-6 -right-6 bg-primary text-white p-6 rounded-2xl shadow-xl hidden md:block">
                        <p class="text-4xl font-bold">14+</p>
                        <p class="text-sm">Tahun<br>Pengalaman</p>
                    </div>
                </div>

                <!-- Right - Content -->
                <div>
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                        Tentang Kami
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        PKBM House Of Knowledge
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        House Of Knowledge adalah lembaga pendidikan non-formal yang berkomitmen untuk memberikan layanan pendidikan berkualitas bagi semua kalangan, termasuk anak-anak berkebutuhan khusus.
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Dengan pengalaman lebih dari 14 tahun, kami telah membantu ribuan siswa mencapai potensi terbaik mereka melalui pendekatan pembelajaran yang inovatif dan personal.
                    </p>

                    <!-- Features -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Kurikulum terakreditasi nasional</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Tenaga pengajar berpengalaman dan bersertifikasi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-secondary rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Fasilitas lengkap dan ramah anak</span>
                        </div>
                    </div>

                    <a href="{{ url('/tentang-sekolah') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                        Selengkapnya
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== NEWS SECTION (3D CAROUSEL) ==================== -->
    <section class="py-20" style="background: linear-gradient(135deg, #165fac 0%, #7cb5ec 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-2 bg-white/20 text-white text-sm font-medium rounded-full mb-4">
                    Berita Terbaru
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">News</h2>
                <p class="text-white/80 max-w-2xl mx-auto">
                    Ikuti perkembangan terbaru dari kegiatan dan prestasi PKBM House Of Knowledge
                </p>
            </div>

            <!-- 3D Carousel -->
            <div class="carousel-3d relative flex items-center justify-center overflow-hidden">
                <!-- Navigation Left -->
                <button id="carouselPrev" class="absolute left-4 md:left-8 z-10 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl hover:bg-primary hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Carousel Items -->
                <div id="carouselTrack" class="relative w-full h-full">
                    <!-- Item 1 -->
                    <div class="carousel-item" data-index="0">
                        <img src="{{ asset('img/news-1.jpg') }}" alt="Kegiatan Pembelajaran" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                            <span class="inline-block px-3 py-1 bg-secondary text-white text-xs rounded-full mb-2">Kegiatan</span>
                            <h3 class="text-white font-bold">Kegiatan Pembelajaran Aktif</h3>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="carousel-item" data-index="1">
                        <img src="{{ asset('img/news-2.jpg') }}" alt="Prestasi Siswa" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                            <span class="inline-block px-3 py-1 bg-accent-yellow text-gray-800 text-xs rounded-full mb-2">Prestasi</span>
                            <h3 class="text-white font-bold">Prestasi Siswa Berprestasi</h3>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="carousel-item" data-index="2">
                        <img src="{{ asset('img/news-3.jpg') }}" alt="Program Terapi" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                            <span class="inline-block px-3 py-1 bg-accent-orange text-white text-xs rounded-full mb-2">Terapi</span>
                            <h3 class="text-white font-bold">Program Terapi Anak</h3>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="carousel-item" data-index="3">
                        <img src="{{ asset('img/news-4.jpg') }}" alt="Ekstrakurikuler" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                            <span class="inline-block px-3 py-1 bg-primary text-white text-xs rounded-full mb-2">Ekskul</span>
                            <h3 class="text-white font-bold">Kegiatan Ekstrakurikuler</h3>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="carousel-item" data-index="4">
                        <img src="{{ asset('img/news-5.jpg') }}" alt="Workshop Guru" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                            <span class="inline-block px-3 py-1 bg-secondary text-white text-xs rounded-full mb-2">Workshop</span>
                            <h3 class="text-white font-bold">Workshop Guru</h3>
                        </div>
                    </div>
                </div>

                <!-- Navigation Right -->
                <button id="carouselNext" class="absolute right-4 md:right-8 z-10 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl hover:bg-primary hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Info Display -->
            <div id="newsInfo" class="text-center mt-8">
                <h3 class="text-xl md:text-2xl font-bold text-white mb-2" id="newsTitle">Kegiatan Pembelajaran Aktif</h3>
                <p class="text-white/80" id="newsCategory">Kegiatan</p>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-8">
                <a href="{{ url('/berita') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Lihat Semua Berita
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== GALLERY SECTION ==================== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    Galeri Kami
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Galeri</h2>
                <p class="text-gray-600">Berisi Kegiatan Siswa Dan Siswi</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="gallery-item rounded-2xl shadow-lg">
                    <img src="{{ asset('img/gallery-1.jpg') }}" alt="Gallery 1" class="w-full h-64 object-cover rounded-2xl">
                </div>
                <div class="gallery-item rounded-2xl shadow-lg">
                    <img src="{{ asset('img/gallery-2.jpg') }}" alt="Gallery 2" class="w-full h-64 object-cover rounded-2xl">
                </div>
                <div class="gallery-item rounded-2xl shadow-lg">
                    <img src="{{ asset('img/gallery-3.jpg') }}" alt="Gallery 3" class="w-full h-64 object-cover rounded-2xl">
                </div>
                <div class="gallery-item rounded-2xl shadow-lg">
                    <img src="{{ asset('img/gallery-4.jpg') }}" alt="Gallery 4" class="w-full h-64 object-cover rounded-2xl">
                </div>
                <div class="gallery-item rounded-2xl shadow-lg">
                    <img src="{{ asset('img/gallery-5.jpg') }}" alt="Gallery 5" class="w-full h-64 object-cover rounded-2xl">
                </div>
                <div class="gallery-item rounded-2xl shadow-lg">
                    <img src="{{ asset('img/gallery-6.jpg') }}" alt="Gallery 6" class="w-full h-64 object-cover rounded-2xl">
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ url('/galeri') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                    Lihat Semua Galeri
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== CONTACT SECTION ==================== -->
    <section class="py-20 bg-cream relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-64 h-64 bg-secondary/5 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    Lokasi Kami
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Kunjungi Cabang Terdekat
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    PKBM House Of Knowledge hadir di 3 lokasi strategis untuk memudahkan akses pendidikan bagi putra-putri Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="card-hover bg-white rounded-2xl shadow-xl p-8 border-b-4 border-accent-orange group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 text-accent-orange group-hover:bg-accent-orange group-hover:text-white transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-1">Gedung Utama PKBM House Of Knowledge </h3>
                        <p class="text-sm text-accent-orange font-medium uppercase tracking-wider mb-4">Pamulang Barat</p>

                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Jl. Ruko Reni Jaya Blok AF No. 22-23<br>
                            Pamulang Barat, Tangerang Selatan<br>
                            Banten 15417
                        </p>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-2xl shadow-xl p-8 border-b-4 border-primary group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-1">PAUD House Of Knowledge</h3>
                        <p class="text-sm text-primary font-medium uppercase tracking-wider mb-4">Pamulang</p>

                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Jl. Bratasena I, Pondok Benda<br>
                            Pamulang, Tangerang Selatan<br>
                            Banten 15417
                        </p>
                    </div>
                </div>

                <div class="card-hover bg-white rounded-2xl shadow-xl p-8 border-b-4 border-secondary group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6 text-secondary group-hover:bg-secondary group-hover:text-white transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-1">House Of Knowledge Cimanggis</h3>
                        <p class="text-sm text-secondary font-medium uppercase tracking-wider mb-4">Ciputat</p>

                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Jl. Otista Raya Blok A25<br>
                            Ruko Prima Ciputat, Tangerang Selatan<br>
                            Banten 15417
                        </p>
                    </div>
                </div>

            </div>

            <div class="text-center mt-12">
                <a href="{{ url('/kontak') }}" class="inline-flex items-center px-8 py-4 bg-primary hover:bg-secondary text-white font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg group">
                    <span>Lihat Peta & Kontak Lengkap</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
                <p class="mt-4 text-sm text-gray-500">Klik tombol di atas untuk melihat detail peta masing-masing cabang</p>
            </div>
        </div>
    </section>

    <x-cta></x-cta>

    <x-footer></x-footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ========== 3D Carousel Script ==========
        const items = document.querySelectorAll('.carousel-item');
        const prevBtn = document.getElementById('carouselPrev');
        const nextBtn = document.getElementById('carouselNext');
        const newsTitle = document.getElementById('newsTitle');
        const newsCategory = document.getElementById('newsCategory');

        const newsData = [
            { title: 'Kegiatan Pembelajaran Aktif', category: 'Kegiatan' },
            { title: 'Prestasi Siswa Berprestasi', category: 'Prestasi' },
            { title: 'Program Terapi Anak', category: 'Terapi' },
            { title: 'Kegiatan Ekstrakurikuler', category: 'Ekskul' },
            { title: 'Workshop Guru', category: 'Workshop' }
        ];

        let currentIndex = 0;
        const totalItems = items.length;

        function updateCarousel() {
            items.forEach((item, index) => {
                item.classList.remove('center', 'left-1', 'left-2', 'right-1', 'right-2', 'hidden-item');

                const diff = (index - currentIndex + totalItems) % totalItems;

                if (diff === 0) {
                    item.classList.add('center');
                } else if (diff === 1) {
                    item.classList.add('right-1');
                } else if (diff === 2) {
                    item.classList.add('right-2');
                } else if (diff === totalItems - 1) {
                    item.classList.add('left-1');
                } else if (diff === totalItems - 2) {
                    item.classList.add('left-2');
                } else {
                    item.classList.add('hidden-item');
                }
            });

            // Update info
            newsTitle.textContent = newsData[currentIndex].title;
            newsCategory.textContent = newsData[currentIndex].category;
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % totalItems;
            updateCarousel();
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + totalItems) % totalItems;
            updateCarousel();
        }

        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') prevSlide();
            if (e.key === 'ArrowRight') nextSlide();
        });

        // Auto rotate
        let autoRotate = setInterval(nextSlide, 5000);

        const carouselSection = document.querySelector('.carousel-3d');
        carouselSection.addEventListener('mouseenter', () => clearInterval(autoRotate));
        carouselSection.addEventListener('mouseleave', () => {
            autoRotate = setInterval(nextSlide, 5000);
        });

        // Initialize carousel
        updateCarousel();

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');

                if (targetId === '#') return;

                const target = document.querySelector(targetId);
                if (target) {
                    const offset = 100;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        const scrollIndicator = document.querySelector('.scroll-indicator');

        if (scrollIndicator) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    scrollIndicator.classList.add('fade-out');
                } else {
                    scrollIndicator.classList.remove('fade-out');
                }
            });
        }
    });
</script>
</body>
</html>
