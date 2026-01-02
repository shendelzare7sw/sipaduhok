<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visi & Misi - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-overlay { background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%); }
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
                <span class="font-semibold">Visi & Misi</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Visi & Misi</h1>
        </div>
    </section>

    <!-- Visi Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Visi Kami</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Visi PKBM House Of Knowledge</h2>
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-l-4 border-[#165fac]">
                        <p class="text-gray-700 text-lg leading-relaxed italic">
                            "Membentuk manusia yang memiliki kualitas iman dan taqwa, mandiri, disiplin, bertanggung jawab dan berpandangan positif dalam menghadapi hidup"
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ asset('img/visi-misi.jpg') }}" alt="Visi" class="rounded-2xl shadow-xl w-full h-[350px] object-cover">
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-[#fac030] rounded-2xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Misi Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-[#287f3b]/10 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">Misi Kami</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Misi PKBM House Of Knowledge</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Misi 1 -->
                <div class="bg-gray-50 rounded-2xl p-6 border-t-4 border-[#165fac] hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">1</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Meningkatkan Iman dan Taqwa Peserta didik</h3>
                    <p class="text-gray-600">Dengan pendidikan yang mengutamakan pendidikan keagamanaan.</p>
                </div>
                <!-- Misi 2 -->
                <div class="bg-gray-50 rounded-2xl p-6 border-t-4 border-[#287f3b] hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-[#287f3b] text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">2</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Menanamkan semangat cinta kasih terhadap orang lain</h3>
                    <p class="text-gray-600">Kepedulian dan cinta kasih kepada sesama teman, guru maupun orang tua murid.</p>
                </div>
                <!-- Misi 3 -->
                <div class="bg-gray-50 rounded-2xl p-6 border-t-4 border-[#d45930] hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-[#d45930] text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">3</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Memperbaiki prilaku dan moral peserta didik</h3>
                    <p class="text-gray-600">Menanamkan perilaku dan moral yang terpuji menjadi keutamaan.</p>
                </div>
                <!-- Misi 4 -->
                <div class="bg-gray-50 rounded-2xl p-6 border-t-4 border-[#fac030] hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-[#fac030] text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">4</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Mengembangkan rasa percaya diri pada diri peserta didik</h3>
                    <p class="text-gray-600">Dengan berbagai kegiatan mengembangkan rasa percaya diri siswa.</p>
                </div>
                <!-- Misi 5 -->
                <div class="bg-gray-50 rounded-2xl p-6 border-t-4 border-[#165fac] hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">5</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Memperluas wawasan peserta didik terhadap perkembangan ilmu pengetahuan</h3>
                    <p class="text-gray-600">Pengalaman belajar dengan guru-guru berkompeten.</p>
                </div>
                <!-- Misi 6 -->
                <div class="bg-gray-50 rounded-2xl p-6 border-t-4 border-[#287f3b] hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-[#287f3b] text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">6</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Memperluas kesempatan belajar bagi peserta didik yang tidak bisa masuk ke sekolah formal</h3>
                    <p class="text-gray-600">Memberi kesempatan belajar lebih luas dan lebih baik.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-[#165fac]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Nilai-Nilai Kami</h2>
                <p class="text-white/80">Prinsip yang menjadi landasan setiap aktivitas kami</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-3"><i class="fas fa-bullseye"></i></div>
                    <h3 class="text-white font-bold">Integritas</h3>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-3"><i class="fas fa-lightbulb"></i></div>
                    <h3 class="text-white font-bold">Inovasi</h3>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-3"><i class="fas fa-handshake"></i></div>
                    <h3 class="text-white font-bold">Kolaborasi</h3>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-3"><i class="fas fa-heart"></i></div>
                    <h3 class="text-white font-bold">Pengembangan Bakat</h3>
                </div>
            </div>
        </div>
    </section>

    <x-footer></x-footer>

</body>
</html>
