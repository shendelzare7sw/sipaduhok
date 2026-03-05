<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Pendidikan PAUD - TK - PKBM House Of Knowledge" description="Program PAUD dan Taman Kanak-kanak di PKBM House Of Knowledge menggunakan kurikulum modern dengan metode pembelajaran yang menyenangkan dan mendukung perkembangan anak." keywords="PAUD, TK, taman kanak-kanak, pendidikan anak usia dini, program PAUD Tangerang"></x-seo-meta>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/paud-tk.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

</head>

<body class="bg-gray-50">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
        $aboutSection = $page->getSection('about');
        $aboutContent = $aboutSection->content ?? [];

        // New dynamic sections
        $programsSection = $page->getSection('programs');
        $programsContent = $programsSection->content ?? [];
        $programsHeader = $programsContent['header'] ?? [];
        $programsItems = $programsContent['items'] ?? [];

        $kurikulumSection = $page->getSection('kurikulum');
        $kurikulumContent = $kurikulumSection->content ?? [];
        $kurikulumHeader = $kurikulumContent['header'] ?? [];
        $kurikulumItems = $kurikulumContent['items'] ?? [];

        $jadwalSection = $page->getSection('jadwal');
        $jadwalContent = $jadwalSection->content ?? [];
        $jadwalHeader = $jadwalContent['header'] ?? [];
        $jadwalItems = $jadwalContent['items'] ?? [];

        $fasilitasSection = $page->getSection('fasilitas');
        $fasilitasContent = $fasilitasSection->content ?? [];
        $fasilitasHeader = $fasilitasContent['header'] ?? [];
        $fasilitasItems = $fasilitasContent['items'] ?? [];

        // Color mapping
        $colorMap = [
            'orange' => ['border' => '#d45930', 'bg' => '#d45930', 'text' => '#d45930'],
            'blue' => ['border' => '#165fac', 'bg' => '#165fac', 'text' => '#165fac'],
            'green' => ['border' => '#287f3b', 'bg' => '#287f3b', 'text' => '#287f3b'],
            'yellow' => ['border' => '#fac030', 'bg' => '#fac030', 'text' => '#fac030'],
        ];
    @endphp

    <!-- Navbar -->
    <x-navbar />

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center bg-cover bg-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}');">

        <div class="hero-overlay absolute inset-0"></div>

        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="/" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Program</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Pendidikan Anak Usia Dini' }}</span>
            </nav>

            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Pendidikan Anak Usia Dini' }}</h1>
            <p class="mt-4 text-lg text-white/90">
                {{ $heroContent['subtitle'] ?? 'Program PAUD, KB, dan TK (Usia 2–6 Tahun)' }}
            </p>
        </div>
    </section>



    <!-- PAUD – TK Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- FOTO-FOTO (3 GAMBAR) -->
                <div class="relative w-full h-[420px] md:h-[460px]">
                    <!-- Gambar utama -->
                    <img src="{{ asset($aboutContent['image_1'] ?? 'img/tk-main.jpg') }}" class="absolute top-0 left-0 w-2/3 h-[280px] md:h-[300px] object-cover rounded-2xl shadow-xl
                        transform rotate-[-4deg] hover:rotate-0 transition duration-700 ease-out z-[30]">

                    <!-- Gambar kedua -->
                    <img src="{{ asset($aboutContent['image_2'] ?? 'img/tk-aktif.jpg') }}" class="absolute bottom-4 left-6 w-1/2 h-[220px] md:h-[240px] object-cover rounded-2xl shadow-lg
                        transform rotate-[3deg] hover:rotate-0 transition duration-700 ease-out z-[20]">

                    <!-- Gambar ketiga -->
                    <img src="{{ asset($aboutContent['image_3'] ?? 'img/tk-belajar.jpg') }}" class="absolute top-10 right-0 w-1/2 h-[240px] md:h-[260px] object-cover rounded-2xl shadow-lg
                        transform rotate-[6deg] hover:rotate-0 transition duration-700 ease-out z-[10]">

                    <!-- Kotak teks -->
                    <div
                        class="absolute -bottom-6 -right-6 bg-[#287f3b] text-white p-6 rounded-2xl shadow-lg hidden md:block animate-fade-up">
                        <p class="text-lg font-bold">Belajar Sambil Bermain</p>
                        <p class="text-sm">Setiap Hari Penuh Ceria</p>
                    </div>
                </div>


                <!-- KONTEN PAUD – TK -->
                <div>
                    <span
                        class="inline-block bg-[#287f3b]/20 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        {{ $aboutContent['badge'] ?? 'Program PAUD – TK' }}
                    </span>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        {{ $aboutContent['title'] ?? 'Belajar Menyenangkan untuk Usia 2–6 Tahun' }}
                    </h2>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_1'] ?? 'Kami percaya bahwa masa usia dini adalah waktu terbaik bagi anak untuk mulai mengenal dunia dengan cara yang paling natural: bermain. Di program PAUD–TK kami, setiap hari dirancang agar anak merasa aman, senang, dan bebas bereksplorasi sesuai ritme mereka.' }}
                    </p>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $aboutContent['description_2'] ?? 'Guru-guru kami mendampingi anak dengan penuh perhatian dan kehangatan, membantu mereka berkembang dalam aspek sosial, motorik, bahasa, serta membangun rasa percaya diri sejak dini. Belajar tanpa tekanan — hanya keceriaan dan pengalaman baru setiap hari.' }}
                    </p>

                    <a href="{{ url($aboutContent['button_link'] ?? '/kontak') }}"
                        class="inline-flex items-center px-6 py-3 bg-[#287f3b] text-white font-semibold rounded-full hover:bg-[#1f6a31] transition">
                        {{ $aboutContent['button_text'] ?? 'Konsultasi Program' }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- Overview Section (Programs) -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#fac030]/20 text-[#d45930] px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    {{ $programsHeader['badge'] ?? 'Program Lengkap' }}
                </span>

                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                    {{ $programsHeader['title'] ?? 'Tiga Jenjang Pendidikan Berkelanjutan' }}
                </h2>

                <p class="text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    {{ $programsHeader['description'] ?? 'Kami menyediakan program pendidikan anak usia dini yang komprehensif dan berkelanjutan.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($programsItems as $program)
                    @php
                        $inputColor = $program['color'] ?? 'blue';
                        $color = $colorMap[$inputColor] ?? ['border' => $inputColor, 'bg' => $inputColor, 'text' => $inputColor];
                        $features = isset($program['features']) ? explode('|', $program['features']) : [];
                    @endphp
                    <div class="card-hover bg-white rounded-2xl p-8 shadow-lg border-t-4"
                        style="border-color: {{ $color['border'] }}">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 mx-auto"
                            style="background-color: {{ $color['bg'] }}20">
                            @if(!empty($program['icon']) && str_contains($program['icon'], '/'))
                                <img src="{{ asset($program['icon']) }}" alt="{{ $program['name'] }}" class="w-10 h-10 object-contain">
                            @else
                                <svg class="w-8 h-8" style="color: {{ $color['text'] }}" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17 1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3 text-center">{{ $program['name'] }}</h3>
                        <p class="text-center text-3xl font-bold mb-4" style="color: {{ $color['text'] }}">
                            {{ $program['age_range'] }}
                        </p>
                        <p class="text-gray-600 text-center mb-6">{{ $program['description'] }}</p>
                        <ul class="space-y-3 text-sm text-gray-600">
                            @foreach($features as $feature)
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" style="color: {{ $color['text'] }}" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Kurikulum -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $kurikulumHeader['badge'] ?? 'Kurikulum' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                    {{ $kurikulumHeader['title'] ?? 'Area Pengembangan' }}
                </h2>
                <p class="text-gray-600 mt-4">
                    {{ $kurikulumHeader['description'] ?? 'Kurikulum komprehensif yang disesuaikan dengan tahap perkembangan anak' }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($kurikulumItems as $item)
                    @php
                        $inputColor = $item['color'] ?? 'blue';
                        $color = $colorMap[$inputColor] ?? ['border' => $inputColor, 'bg' => $inputColor, 'text' => $inputColor];
                    @endphp
                    <div class="card-hover bg-gray-50 rounded-2xl p-8 border-l-4"
                        style="border-color: {{ $color['border'] }}">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-4"
                            style="background-color: {{ $color['bg'] }}20">
                            @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-9 h-9 object-contain">
                            @else
                                <svg class="w-7 h-7" style="color: {{ $color['text'] }}" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $item['title'] }}</h3>
                        <p class="text-gray-600">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Jadwal -->
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span
                    class="inline-block bg-[#287f3b]/10 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $jadwalHeader['badge'] ?? 'Jadwal' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                    {{ $jadwalHeader['title'] ?? 'Jadwal Kegiatan Harian' }}
                </h2>
                <p class="text-gray-600 mt-4">
                    {{ $jadwalHeader['description'] ?? 'Contoh jadwal untuk program PAUD (KB & TK memiliki durasi yang disesuaikan)' }}
                </p>
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
                        @foreach($jadwalItems as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $schedule['time'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $schedule['activity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Fasilitas -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#fac030]/20 text-[#d45930] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $fasilitasHeader['badge'] ?? 'Fasilitas' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                    {{ $fasilitasHeader['title'] ?? 'Fasilitas Lengkap' }}
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($fasilitasItems as $facility)
                    @php
                        $inputColor = $facility['color'] ?? 'blue';
                        $color = $colorMap[$inputColor] ?? ['border' => $inputColor, 'bg' => $inputColor, 'text' => $inputColor];
                    @endphp
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center mb-4 mx-auto"
                            style="background-color: {{ $color['bg'] }}20">
                            @if(!empty($facility['icon']) && str_contains($facility['icon'], '/'))
                                <img src="{{ asset($facility['icon']) }}" alt="{{ $facility['title'] }}" class="w-9 h-9 object-contain">
                            @else
                                <svg class="w-7 h-7" style="color: {{ $color['text'] }}" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            @endif
                        </div>
                        <h4 class="font-bold text-gray-800 mb-2">{{ $facility['title'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $facility['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #fac030 0%, #d45930 100%);">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Daftarkan Anak Anda Sekarang</h2>
            <p class="text-white/90 mb-8">Berikan pendidikan terbaik untuk tumbuh kembang anak Anda sejak usia dini
                hingga siap memasuki SD.</p>
            <a href="/ppdb"
                class="inline-flex items-center px-8 py-4 bg-white text-[#d45930] font-semibold rounded-full hover:bg-gray-100 transition">
                Daftar Sekarang
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </section>

    <x-footer></x-footer>

    @vite(['resources/js/navbar.js'])
</body>

</html>
