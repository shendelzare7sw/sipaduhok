<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sekolah - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-overlay { background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }

        /* Timeline dari halaman sejarah */
        .timeline-line { position: absolute; left: 50%; transform: translateX(-50%); width: 4px; height: 100%; background: linear-gradient(to bottom, #165fac, #287f3b); }
        @media (max-width: 768px) {
            .timeline-line { left: 20px; }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center" 
             style="background-image: url('{{ asset('img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Profil</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">Tentang Sekolah</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Tentang Sekolah</h1>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Image -->
                <div class="relative">
                    <img src="{{ asset('img/about-school.jpg') }}" alt="Tentang Sekolah" 
                         class="rounded-2xl shadow-xl w-full h-[400px] object-cover">
                    <div class="absolute -bottom-6 -right-6 bg-[#165fac] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                        <p class="text-4xl font-bold">14+</p>
                        <p class="text-sm">Tahun Pengalaman</p>
                    </div>
                </div>

                <!-- Text -->
                <div>
                    <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Tentang Kami</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">PKBM House Of Knowledge</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        PKBM House Of Knowledge adalah lembaga pendidikan yang berdedikasi untuk memberikan pendidikan berkualitas bagi semua kalangan.
                        Kami percaya bahwa setiap anak memiliki potensi unik yang perlu dikembangkan dengan pendekatan yang tepat.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Dengan pengalaman lebih dari 14 tahun dalam bidang pendidikan, kami telah membantu ribuan siswa mencapai potensi terbaik mereka melalui program pendidikan yang inovatif dan inklusif.
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                     d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                     clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Kurikulum Terbaru</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                     d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                     clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Guru Profesional</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                     d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                     clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Fasilitas Lengkap</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                     d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                     clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">Lingkungan Nyaman</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ✔✔✔ SEJARAH DIGABUNG DI SINI -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">
                Perjalanan Kami
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Sejarah PKBM House Of Knowledge</h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                PKBM House Of Knowledge didirikan dengan semangat untuk memberikan pendidikan berkualitas yang dapat diakses oleh semua kalangan.
                Berikut adalah perjalanan kami dari awal hingga saat ini.
            </p>
        </div>
    </section>

    <!-- Timeline -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 relative">

            <div class="timeline-line hidden md:block"></div>
            
            <div class="space-y-12">

                <!-- 2014 -->
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="md:w-1/2 md:text-right md:pr-12">
                        <div class="bg-gray-50 rounded-2xl p-6 shadow-lg border-l-4 md:border-l-0 md:border-r-4 border-[#165fac]">
                            <span class="inline-block bg-[#165fac] text-white px-4 py-1 rounded-full text-sm font-bold mb-3">2014</span>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Awal Pendirian</h3>
                            <p class="text-gray-600">PKBM House Of Knowledge didirikan dengan 5 orang guru dan 20 siswa pertama. Dimulai dari sebuah rumah sederhana dengan cita-cita besar.</p>
                        </div>
                    </div>
                    <div class="hidden md:flex w-8 h-8 bg-[#165fac] rounded-full items-center justify-center z-10">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <div class="md:w-1/2 md:pl-12">
                        <img src="{{ asset('img/sejarah-1.jpg') }}" class="rounded-2xl shadow-lg w-full h-48 object-cover">
                    </div>
                </div>

                <!-- 2016 -->
                <div class="flex flex-col md:flex-row-reverse items-center gap-8">
                    <div class="md:w-1/2 md:text-left md:pl-12">
                        <div class="bg-gray-50 rounded-2xl p-6 shadow-lg border-l-4 border-[#287f3b]">
                            <span class="inline-block bg-[#287f3b] text-white px-4 py-1 rounded-full text-sm font-bold mb-3">2016</span>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Pengembangan Program</h3>
                            <p class="text-gray-600">Membuka program pendidikan kesetaraan Paket A, B, dan C. Jumlah siswa meningkat menjadi 100 orang.</p>
                        </div>
                    </div>
                    <div class="hidden md:flex w-8 h-8 bg-[#287f3b] rounded-full items-center justify-center z-10">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <div class="md:w-1/2 md:pr-12">
                        <img src="{{ asset('img/sejarah-2.jpg') }}" class="rounded-2xl shadow-lg w-full h-48 object-cover">
                    </div>
                </div>

                <!-- 2018 -->
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="md:w-1/2 md:text-right md:pr-12">
                        <div class="bg-gray-50 rounded-2xl p-6 shadow-lg border-l-4 md:border-l-0 md:border-r-4 border-[#d45930]">
                            <span class="inline-block bg-[#d45930] text-white px-4 py-1 rounded-full text-sm font-bold mb-3">2018</span>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Program Inklusi</h3>
                            <p class="text-gray-600">Meluncurkan program pendidikan inklusi untuk anak berkebutuhan khusus dengan fasilitas terapi lengkap.</p>
                        </div>
                    </div>
                    <div class="hidden md:flex w-8 h-8 bg-[#d45930] rounded-full items-center justify-center z-10">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <div class="md:w-1/2 md:pl-12">
                        <img src="{{ asset('img/sejarah-3.jpg') }}" class="rounded-2xl shadow-lg w-full h-48 object-cover">
                    </div>
                </div>

                <!-- 2020 -->
                <div class="flex flex-col md:flex-row-reverse items-center gap-8">
                    <div class="md:w-1/2 md:text-left md:pl-12">
                        <div class="bg-gray-50 rounded-2xl p-6 shadow-lg border-l-4 border-[#fac030]">
                            <span class="inline-block bg-[#fac030] text-white px-4 py-1 rounded-full text-sm font-bold mb-3">2023</span>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Gedung Baru</h3>
                            <p class="text-gray-600">Pindah ke gedung baru yang lebih luas di Pamulang dengan fasilitas modern dan lengkap.</p>
                        </div>
                    </div>
                    <div class="hidden md:flex w-8 h-8 bg-[#fac030] rounded-full items-center justify-center z-10">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <div class="md:w-1/2 md:pr-12">
                        <img src="{{ asset('img/sejarah-4.jpg') }}" class="rounded-2xl shadow-lg w-full h-48 object-cover">
                    </div>
                </div>

                <!-- 2024 -->
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="md:w-1/2 md:text-right md:pr-12">
                        <div class="bg-gray-50 rounded-2xl p-6 shadow-lg border-l-4 md:border-l-0 md:border-r-4 border-[#165fac]">
                            <span class="inline-block bg-[#165fac] text-white px-4 py-1 rounded-full text-sm font-bold mb-3">2025</span>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Saat Ini</h3>
                            <p class="text-gray-600">Melayani lebih dari 200 siswa dengan 50+ tenaga pengajar profesional. Terus berkembang dan berinovasi.</p>
                        </div>
                    </div>
                    <div class="hidden md:flex w-8 h-8 bg-[#165fac] rounded-full items-center justify-center z-10">
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <div class="md:w-1/2 md:pl-12">
                        <img src="{{ asset('img/sejarah-5.jpg') }}" class="rounded-2xl shadow-lg w-full h-48 object-cover">
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- Why Choose Us -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Keunggulan Kami</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Mengapa Memilih Kami?</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Card 1 -->
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-[#d45930]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#d45930]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pendidikan Berkualitas</h3>
                    <p class="text-gray-600">Kurikulum yang dirancang untuk memaksimalkan potensi setiap siswa dengan metode pembelajaran modern.</p>
                </div>

                <!-- Card 2 -->
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-[#165fac]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Tenaga Pengajar Ahli</h3>
                    <p class="text-gray-600">Guru-guru berpengalaman dan terlatih dalam menangani berbagai kebutuhan belajar siswa.</p>
                </div>

                <!-- Card 3 -->
                <div class="card-hover bg-gray-50 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-[#287f3b]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" 
                                  d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pendidikan Inklusif</h3>
                    <p class="text-gray-600">Menerima dan mendukung anak berkebutuhan khusus dengan program yang disesuaikan.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Tertarik Bergabung?</h2>
            <p class="text-white/90 mb-8">Daftarkan putra-putri Anda sekarang dan berikan pendidikan terbaik untuk masa depan yang cerah</p>
            <a href="{{ url('/ppdb-formulir') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition">
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
