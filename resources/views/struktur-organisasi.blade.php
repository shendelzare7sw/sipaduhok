<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-overlay { background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%); }
        .org-card { transition: all 0.3s ease; }
        .org-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .connector-vertical { position: absolute; left: 50%; transform: translateX(-50%); width: 3px; background: #165fac; }
        .connector-horizontal { position: absolute; height: 3px; background: #165fac; }
    </style>
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
                <span>Profil</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">Struktur Organisasi</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Struktur Organisasi</h1>
        </div>
    </section>

    <!-- Org Chart Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Organisasi</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Struktur Organisasi PKBM</h2>
                <p class="text-gray-600 mt-4">House Of Knowledge</p>
            </div>

            <!-- Org Chart -->
            <div class="flex flex-col items-center">
                <!-- Ketua Yayasan -->
                <div class="org-card bg-[#165fac] text-white rounded-2xl p-6 text-center shadow-xl mb-8">
                    <img src="{{ asset('img/ketua-yayasan.png') }}" alt="Ketua Yayasan" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white">
                    <h3 class="font-bold text-lg">Ivan Janitra, S. I. Kom</h3>
                    <p class="text-white/80 text-sm">Ketua Yayasan</p>
                </div>

                <!-- Connector -->
                <div class="w-1 h-12 bg-[#165fac]"></div>

                <!-- Kepala Sekolah -->
                <div class="org-card bg-[#287f3b] text-white rounded-2xl p-6 text-center shadow-xl mb-8">
                    <img src="{{ asset('img/kepala-sekolah.png') }}" alt="Kepala Sekolah" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white">
                    <h3 class="font-bold text-lg">Fransisda Tiodora F., S. Psi</h3>
                    <p class="text-white/80 text-sm">Kepala PKBM</p>
                </div>

                <!-- Connector -->
                <div class="w-1 h-12 bg-[#165fac]"></div>

                <!-- Wakil & Koordinator -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl mb-8">
                    <div class="org-card bg-white rounded-2xl p-6 text-center shadow-lg border-t-4 border-[#d45930]">
                        <img src="{{ asset('img/sekretaris.png') }}" alt="Wakil" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="font-bold text-gray-800">Charoline Revlecya, S. Mat</h3>
                        <p class="text-gray-600 text-sm">Sekretaris</p>
                    </div>
                    <div class="org-card bg-white rounded-2xl p-6 text-center shadow-lg border-t-4 border-[#165fac]">
                        <img src="{{ asset('img/bendahara.png') }}" alt="Wakil" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="font-bold text-gray-800">Linawati Rozali</h3>
                        <p class="text-gray-600 text-sm">Bendahara</p>
                    </div>
                    <div class="org-card bg-white rounded-2xl p-6 text-center shadow-lg border-t-4 border-[#287f3b]">
                        <img src="{{ asset('img/administrator.png') }}" alt="Wakil" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="font-bold text-gray-800">Delia Parsaulani, S. K. M</h3>
                        <p class="text-gray-600 text-sm">Administrator</p>
                    </div>
                </div>

                <!-- Koordinator Program -->
                <div class="w-full max-w-5xl">
                    <h3 class="text-xl font-bold text-center text-gray-800 mb-6">Koordinator Program</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="org-card bg-gray-50 rounded-xl p-4 text-center shadow hover:bg-white">
                            <img src="{{ asset('img/koordinator-1.png') }}" alt="Koordinator" class="w-16 h-16 rounded-full mx-auto mb-3 object-cover">
                            <h4 class="font-semibold text-gray-800 text-sm">Stefany Angelina, S.Pd</h4>
                            <p class="text-gray-500 text-xs">Bidang Kesetaraan</p>
                        </div>
                        <div class="org-card bg-gray-50 rounded-xl p-4 text-center shadow hover:bg-white">
                            <img src="{{ asset('img/koordinator-2.jpg') }}" alt="Koordinator" class="w-16 h-16 rounded-full mx-auto mb-3 object-cover">
                            <h4 class="font-semibold text-gray-800 text-sm">Robert Yinaidi Lay</h4>
                            <p class="text-gray-500 text-xs">Bidang Sarpas</p>
                        </div>
                        <div class="org-card bg-gray-50 rounded-xl p-4 text-center shadow hover:bg-white">
                            <img src="{{ asset('img/koordinator-3.png') }}" alt="Koordinator" class="w-16 h-16 rounded-full mx-auto mb-3 object-cover">
                            <h4 class="font-semibold text-gray-800 text-sm">Aviana Margaretta T</h4>
                            <p class="text-gray-500 text-xs">Bidang Kesiswaan</p>
                        </div>
                        <div class="org-card bg-gray-50 rounded-xl p-4 text-center shadow hover:bg-white">
                            <img src="{{ asset('img/koordinator-4.png') }}" alt="Koordinator" class="w-16 h-16 rounded-full mx-auto mb-3 object-cover">
                            <h4 class="font-semibold text-gray-800 text-sm">Debora Zephania E. M., S.S</h4>
                            <p class="text-gray-500 text-xs">Bidang Lifeskill</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <x-footer></x-footer>

</body>
</html>
