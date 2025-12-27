<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendidikan PAUD - TK - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f9fafb;
        }

        .hero-overlay { 
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.88) 0%, rgba(40, 127, 59, 0.85) 100%); 
        }

        .card-hover { 
            transition: all 0.3s ease; 
        }
        .card-hover:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 24px 48px rgba(0,0,0,0.12); 
        }

        :root {
            --blue-main: #165fac;
            --green-main: #287f3b;
            --orange-main: #d45930;
            --yellow-main: #fac030;
        }

        .badge {
            padding: 0.4rem 1rem;
            display: inline-block;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

</head>
<body class="bg-gray-50">
    
    <!-- Navbar -->
    <x-navbar />

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center bg-cover bg-center"
        style="background-image: url('{{ asset('img/hero-bg.jpg') }}');">
        
        <div class="hero-overlay absolute inset-0"></div>

        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="/" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Program</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">Pendidikan Anak Usia Dini</span>
            </nav>

            <h1 class="text-4xl md:text-5xl font-bold">Pendidikan Anak Usia Dini</h1>
            <p class="mt-4 text-lg text-white/90">Program PAUD, KB, dan TK (Usia 2–6 Tahun)</p>
        </div>
    </section>



    <!-- PAUD – TK Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- FOTO-FOTO (3 GAMBAR) -->
                <div class="relative w-full h-[420px] md:h-[460px]">
                    <!-- Gambar utama -->
                    <img src="{{ asset('img/tk-main.jpg') }}"
                        class="absolute top-0 left-0 w-2/3 h-[280px] md:h-[300px] object-cover rounded-2xl shadow-xl 
                        transform rotate-[-4deg] hover:rotate-0 transition duration-700 ease-out z-[30]">

                    <!-- Gambar kedua -->
                    <img src="{{ asset('img/tk-aktif.jpg') }}"
                        class="absolute bottom-4 left-6 w-1/2 h-[220px] md:h-[240px] object-cover rounded-2xl shadow-lg 
                        transform rotate-[3deg] hover:rotate-0 transition duration-700 ease-out z-[20]">

                    <!-- Gambar ketiga -->
                    <img src="{{ asset('img/tk-belajar.jpg') }}"
                        class="absolute top-10 right-0 w-1/2 h-[240px] md:h-[260px] object-cover rounded-2xl shadow-lg 
                        transform rotate-[6deg] hover:rotate-0 transition duration-700 ease-out z-[10]">

                    <!-- Kotak teks -->
                    <div class="absolute -bottom-6 -right-6 bg-[#287f3b] text-white p-6 rounded-2xl shadow-lg hidden md:block animate-fade-up">
                        <p class="text-lg font-bold">Belajar Sambil Bermain</p>
                        <p class="text-sm">Setiap Hari Penuh Ceria</p>
                    </div>
                </div>


                <!-- KONTEN PAUD – TK -->
                <div>
                    <span class="inline-block bg-[#287f3b]/20 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        Program PAUD – TK
                    </span>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        Belajar Menyenangkan untuk Usia 2–6 Tahun
                    </h2>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Kami percaya bahwa masa usia dini adalah waktu terbaik bagi anak untuk mulai
                        mengenal dunia dengan cara yang paling natural: bermain. Di program PAUD–TK kami,
                        setiap hari dirancang agar anak merasa aman, senang, dan bebas bereksplorasi sesuai ritme mereka.
                    </p>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Guru-guru kami mendampingi anak dengan penuh perhatian dan kehangatan,
                        membantu mereka berkembang dalam aspek sosial, motorik, bahasa, serta membangun
                        rasa percaya diri sejak dini. Belajar tanpa tekanan — hanya keceriaan dan pengalaman baru setiap hari.
                    </p>

                    <a href="{{ url('/kontak') }}"
                        class="inline-flex items-center px-6 py-3 bg-[#287f3b] text-white font-semibold rounded-full hover:bg-[#1f6a31] transition">
                        Konsultasi Program
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- Overview Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-[#fac030]/20 text-[#d45930] px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Program Lengkap
                </span>

                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                    Tiga Jenjang Pendidikan Berkelanjutan
                </h2>

                <p class="text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Kami menyediakan program pendidikan anak usia dini yang komprehensif dan berkelanjutan,
                    dari PAUD hingga TK, dengan pendekatan bermain sambil belajar yang menyenangkan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- PAUD -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border-t-4 border-[#d45930]">
                    <div class="w-16 h-16 bg-[#d45930]/10 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-[#d45930]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17 1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3 text-center">PAUD</h3>
                    <p class="text-center text-3xl font-bold text-[#d45930] mb-4">2-4 Tahun</p>
                    <p class="text-gray-600 text-center mb-6">Pendidikan Anak Usia Dini dengan fokus bermain sambil belajar</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#d45930] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Rasio guru 1:5</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#d45930] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Pengembangan motorik</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#d45930] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Stimulasi sosial-emosional</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#d45930] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Senin-Jumat, 07:30-11:00</span>
                        </li>
                    </ul>
                </div>

                <!-- KB -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border-t-4 border-[#165fac]">
                    <div class="w-16 h-16 bg-[#165fac]/10 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3 text-center">KB</h3>
                    <p class="text-center text-3xl font-bold text-[#165fac] mb-4">4-5 Tahun</p>
                    <p class="text-gray-600 text-center mb-6">Kelompok Bermain dengan pengenalan literasi dasar</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#165fac] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Pengenalan huruf & angka</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#165fac] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Pengembangan kreativitas</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#165fac] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Kegiatan seni & musik</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#165fac] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Senin-Jumat, 07:30-11:00</span>
                        </li>
                    </ul>
                </div>

                <!-- TK -->
                <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border-t-4 border-[#287f3b]">
                    <div class="w-16 h-16 bg-[#287f3b]/10 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3 text-center">TK</h3>
                    <p class="text-center text-3xl font-bold text-[#287f3b] mb-4">5-6 Tahun</p>
                    <p class="text-gray-600 text-center mb-6">Taman Kanak-kanak persiapan sekolah dasar</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#287f3b] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Membaca & menulis</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#287f3b] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Matematika dasar</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#287f3b] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Bahasa Inggris dasar</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#287f3b] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Senin-Jumat, 07:30-11:00</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Kurikulum -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Kurikulum</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Area Pengembangan</h2>
                <p class="text-gray-600 mt-4">Kurikulum komprehensif yang disesuaikan dengan tahap perkembangan anak</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4 border-[#d45930]">
                    <div class="w-14 h-14 bg-[#d45930]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-[#d45930]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Nilai Agama & Moral</h3>
                    <p class="text-gray-600">Pengenalan nilai-nilai agama dan moral sejak dini melalui pembiasaan sehari-hari.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4 border-[#165fac]">
                    <div class="w-14 h-14 bg-[#165fac]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Kognitif</h3>
                    <p class="text-gray-600">Stimulasi kemampuan berpikir, mengenal angka, huruf, bentuk, dan warna.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4 border-[#287f3b]">
                    <div class="w-14 h-14 bg-[#287f3b]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Bahasa</h3>
                    <p class="text-gray-600">Pengembangan kemampuan berbahasa reseptif dan ekspresif melalui cerita dan lagu.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4 border-[#fac030]">
                    <div class="w-14 h-14 bg-[#fac030]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-[#fac030]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Sosial Emosional</h3>
                    <p class="text-gray-600">Pengembangan kemampuan bersosialisasi dan mengelola emosi dengan baik.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4 border-[#165fac]">
                    <div class="w-14 h-14 bg-[#165fac]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17 1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Motorik Halus</h3>
                    <p class="text-gray-600">Latihan koordinasi tangan-mata melalui kegiatan melipat, menggunting, dan mewarnai.</p>
                </div>
                <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4 border-[#d45930]">
                    <div class="w-14 h-14 bg-[#d45930]/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-[#d45930]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Motorik Kasar</h3>
                    <p class="text-gray-600">Aktivitas fisik untuk mengembangkan koordinasi tubuh dan keseimbangan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Jadwal -->
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-block bg-[#287f3b]/10 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">Jadwal</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Jadwal Kegiatan Harian</h2>
                <p class="text-gray-600 mt-4">Contoh jadwal untuk program PAUD (KB & TK memiliki durasi yang disesuaikan)</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-[#165fac] text-white">
                        <tr>
                            <th class="px-6 py-4 text-left">Waktu</th>
                            <th class="px-6 py-4 text-left">Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">07:30 - 08:00</td>
                            <td class="px-6 py-4 text-gray-600">Penyambutan & Free Play</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">08:00 - 08:30</td>
                            <td class="px-6 py-4 text-gray-600">Circle Time & Doa</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">08:30 - 09:30</td>
                            <td class="px-6 py-4 text-gray-600">Kegiatan Inti (Tema)</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">09:30 - 10:00</td>
                            <td class="px-6 py-4 text-gray-600">Snack Time</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">10:00 - 10:30</td>
                            <td class="px-6 py-4 text-gray-600">Outdoor Play</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">10:30 - 11:00</td>
                            <td class="px-6 py-4 text-gray-600">Recalling & Penutup</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Fasilitas -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-[#fac030]/20 text-[#d45930] px-4 py-2 rounded-full text-sm font-semibold mb-4">Fasilitas</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Fasilitas Lengkap</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="w-14 h-14 bg-[#165fac]/10 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-7 h-7 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-2">Ruang Kelas Nyaman</h4>
                    <p class="text-sm text-gray-600">AC, pencahayaan baik, furnitur ramah anak</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="w-14 h-14 bg-[#287f3b]/10 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-7 h-7 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-2">Area Bermain Outdoor</h4>
                    <p class="text-sm text-gray-600">Aman, bersih, dan terawat</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="w-14 h-14 bg-[#d45930]/10 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-7 h-7 text-[#d45930]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-4">Perpustakaan Mini</h4>
                    <p class="text-sm text-gray-600">Koleksi buku anak lengkap</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="w-14 h-14 bg-[#fac030]/10 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-7 h-7 text-[#fac030]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-2">Alat Permainan Edukatif</h4>
                    <p class="text-sm text-gray-600">APE berkualitas dan variatif</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #fac030 0%, #d45930 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Daftarkan Anak Anda Sekarang</h2>
            <p class="text-white/90 mb-8">Berikan pendidikan terbaik untuk tumbuh kembang anak Anda sejak usia dini hingga siap memasuki SD.</p>
            <a href="/ppdb-formulir" class="inline-flex items-center px-8 py-4 bg-white text-[#d45930] font-semibold rounded-full hover:bg-gray-100 transition">
                Daftar Sekarang
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>

    <x-footer></x-footer>

</body>
</html>