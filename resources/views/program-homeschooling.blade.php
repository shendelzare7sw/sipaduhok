<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Homeschooling Perorangan - HOK" description="Layanan HOK Homeschooling Perorangan dengan pendampingan belajar fleksibel, LMS, dan akses tagihan orang tua." keywords="homeschooling perorangan, LMS homeschooling, pendampingan belajar"></x-seo-meta>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">
    
    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center" style="background-image: url('{{ asset('img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Program</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">Homeschooling Perorangan</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Homeschooling Perorangan</h1>
            <p class="mt-4 text-lg text-white/90">Pendampingan belajar fleksibel sesuai kebutuhan anak</p>
        </div>
    </section>

    <!-- About Program -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block bg-[#165fac]/20 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Layanan Perorangan</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Belajar Lebih Personal dan Terarah</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        HOK Homeschooling menyediakan pendampingan belajar yang fleksibel dan personal. Ritme, jadwal, serta aktivitas belajar dapat disesuaikan dengan kebutuhan setiap anak.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Kegiatan pembelajaran didukung melalui LMS, sedangkan orang tua dapat mengakses tagihan yang tersedia setelah login.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-full">
                            <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 text-sm">Jadwal Fleksibel</span>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-full">
                            <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 text-sm">Tutor Pribadi</span>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-full">
                            <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 text-sm">Akses LMS</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <img loading="lazy" decoding="async" src="{{ asset('img/hero-img.jpg') }}" alt="Homeschooling" class="rounded-2xl shadow-xl w-full h-[400px] object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Jenjang -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Fokus Pendampingan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center border-b-4 border-[#fac030]">
                    <div class="w-20 h-20 bg-[#fac030]/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl"><i class="fas fa-books"></i></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tahap Dasar</h3>
                    <p class="text-gray-600 mb-4">Fondasi belajar</p>
                    <p class="text-gray-500 text-sm">Pendampingan untuk membangun kebiasaan, literasi, numerasi, dan rasa ingin tahu.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center border-b-4 border-[#165fac]">
                    <div class="w-20 h-20 bg-[#165fac]/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl"><i class="fas fa-book-open"></i></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tahap Pengembangan</h3>
                    <p class="text-gray-600 mb-4">Belajar mandiri</p>
                    <p class="text-gray-500 text-sm">Pendampingan untuk mengembangkan pemahaman, kemandirian, dan minat anak.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center border-b-4 border-[#287f3b]">
                    <div class="w-20 h-20 bg-[#287f3b]/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl"><i class="fas fa-graduation-cap"></i></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tahap Lanjutan</h3>
                    <p class="text-gray-600 mb-4">Persiapan masa depan</p>
                    <p class="text-gray-500 text-sm">Pendampingan terarah sesuai tujuan belajar, minat, dan rencana lanjutan anak.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Keunggulan Homeschooling</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-[#165fac]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Waktu Fleksibel</h3>
                    <p class="text-gray-600 text-sm">Jadwal belajar dapat disesuaikan dengan aktivitas anak</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-[#287f3b]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Pembelajaran Personal</h3>
                    <p class="text-gray-600 text-sm">Kurikulum disesuaikan dengan gaya belajar anak</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-[#d45930]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-[#d45930]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Belajar di Rumah</h3>
                    <p class="text-gray-600 text-sm">Anak belajar di lingkungan yang nyaman dan aman</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="w-12 h-12 bg-[#fac030]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-[#fac030]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Portal Terintegrasi</h3>
                    <p class="text-gray-600 text-sm">LMS dan informasi tagihan tersedia sesuai hak akses akun</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Mulai Homeschooling Sekarang</h2>
            <p class="text-white/90 mb-8">Hubungi kontak HOK untuk berkonsultasi mengenai kebutuhan belajar anak Anda.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('pendaftaran') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition">
                    Lihat Pendaftaran
                </a>
                <a href="{{ url('/kontak') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-[#165fac] transition">
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

    @vite(['resources/js/navbar.js'])
</body>
</html>
