<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Guru & Tenaga Ahli - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-overlay { background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%); }
        .teacher-card { transition: all 0.3s ease; }
        .teacher-card:hover { transform: translateY(-8px); }
        .teacher-card:hover .teacher-overlay { opacity: 1; }
        .teacher-overlay { transition: all 0.3s ease; opacity: 0; }
    </style>
</head>
<body class="bg-gray-50">

    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center" style="background-image: url('{{ asset('img/hero-bg.png') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Profil</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">Profil Guru & Tenaga Ahli</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Profil Guru & Tenaga Ahli</h1>
        </div>
    </section>

    <!-- Filter Tabs -->
    <section class="py-8 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4">
                <button class="px-6 py-2 bg-[#165fac] text-white rounded-full font-medium" onclick="filterTeachers('all')">Semua</button>
                <button class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-[#165fac] hover:text-white transition" onclick="filterTeachers('guru')">Guru</button>
                <button class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-[#165fac] hover:text-white transition" onclick="filterTeachers('terapis')">Terapis</button>
                <button class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-[#165fac] hover:text-white transition" onclick="filterTeachers('psikolog')">Psikolog</button>
            </div>
        </div>
    </section>

    <!-- Teachers Grid -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" id="teachersGrid">
                <!-- Teacher 1 -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="guru">
                    <div class="relative">
                        <img src="{{ asset('img/guru-1.png') }}" alt="Guru" class="w-full h-64 object-cover">

                        <span class="absolute top-4 right-4 bg-[#165fac] text-white px-3 py-1 rounded-full text-xs font-medium">Guru</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Charolline R. S.Mat</h3>
                        <p class="text-gray-500 text-sm">Guru Sosiologi</p>
                    </div>
                </div>

                <!-- Teacher 2 -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="guru">
                    <div class="relative">
                        <img src="{{ asset('img/guru-2.png') }}" alt="Guru" class="w-full h-64 object-cover">

                        <span class="absolute top-4 right-4 bg-[#287f3b] text-white px-3 py-1 rounded-full text-xs font-medium">Guru</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Dyah Yossie, S.Pd</h3>
                        <p class="text-gray-500 text-sm">Guru Bahasa Indonesia</p>
                    </div>
                </div>

                <!-- Teacher 3 - Terapis -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="terapis">
                    <div class="relative">
                        <img src="{{ asset('img/guru-3.png') }}" alt="Terapis" class="w-full h-64 object-cover">

                        <span class="absolute top-4 right-4 bg-[#d45930] text-white px-3 py-1 rounded-full text-xs font-medium">Terapis</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Muthi</h3>
                        <p class="text-gray-500 text-sm">Terapis Okupasi</p>
                    </div>
                </div>

                <!-- Teacher 4 - Psikolog -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="psikolog">
                    <div class="relative">
                        <img src="{{ asset('img/guru-4.png') }}" alt="Psikolog" class="w-full h-64 object-cover">

                        <span class="absolute top-4 right-4 bg-[#fac030] text-white px-3 py-1 rounded-full text-xs font-medium">Psikolog</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Fransisda T. F., M.Psi</h3>
                        <p class="text-gray-500 text-sm">Psikolog Anak</p>
                    </div>
                </div>

                <!-- Teacher 5 -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="guru">
                    <div class="relative">
                        <img src="{{ asset('img/guru-5.png') }}" alt="Guru" class="w-full h-64 object-cover">
                        <span class="absolute top-4 right-4 bg-[#165fac] text-white px-3 py-1 rounded-full text-xs font-medium">Guru</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Berliana Agustin</h3>
                        <p class="text-gray-500 text-sm">Guru IPA</p>
                    </div>
                </div>

                <!-- Teacher 6 -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="guru">
                    <div class="relative">
                        <img src="{{ asset('img/guru-6.png') }}" alt="Guru" class="w-full h-64 object-cover">
                        <span class="absolute top-4 right-4 bg-[#287f3b] text-white px-3 py-1 rounded-full text-xs font-medium">Guru</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Delia Parsaulani, S.K.M</h3>
                        <p class="text-gray-500 text-sm">Guru IPS</p>
                    </div>
                </div>

                <!-- Teacher 7 - Terapis -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="terapis">
                    <div class="relative">
                        <img src="{{ asset('img/guru-7.png') }}" alt="Terapis" class="w-full h-64 object-cover">
                        <span class="absolute top-4 right-4 bg-[#d45930] text-white px-3 py-1 rounded-full text-xs font-medium">Terapis</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Debora Zephania, S.S</h3>
                        <p class="text-gray-500 text-sm">Terapis Wicara</p>
                    </div>
                </div>

                <!-- Teacher 8 -->
                <div class="teacher-card bg-white rounded-2xl shadow-lg overflow-hidden" data-category="guru">
                    <div class="relative">
                        <img src="{{ asset('img/guru-8.png') }}" alt="Guru" class="w-full h-64 object-cover">
                        <span class="absolute top-4 right-4 bg-[#165fac] text-white px-3 py-1 rounded-full text-xs font-medium">Guru</span>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-bold text-gray-800 text-lg">Meini</h3>
                        <p class="text-gray-500 text-sm">Guru PAUD</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Join Team CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Bergabung Bersama Kami</h2>
            <p class="text-white/90 mb-8">Apakah Anda tertarik menjadi bagian dari tim kami? Kirimkan lamaran Anda sekarang.</p>
            <a href="{{ url('/kontak') }}" class="inline-flex items-center px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition">
                Hubungi Kami
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>

    <x-footer></x-footer>

    <script>
        function filterTeachers(category) {
            const cards = document.querySelectorAll('.teacher-card');
            const buttons = document.querySelectorAll('button');

            buttons.forEach(btn => {
                btn.classList.remove('bg-[#165fac]', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            event.target.classList.remove('bg-gray-100', 'text-gray-700');
            event.target.classList.add('bg-[#165fac]', 'text-white');

            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>
