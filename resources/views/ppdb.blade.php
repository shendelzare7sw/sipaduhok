<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Pendaftaran - Sipadu Homescholing" description="Informasi alur dan persyaratan pendaftaran Sipadu Homescholing."></x-seo-meta>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/ppdb.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="bg-white">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $steps = [
            [
                'title' => 'Konsultasi Awal',
                'description' => 'Hubungi kontak HOK untuk menyampaikan kebutuhan dan tujuan belajar anak.',
                'icon' => 'fa-comments',
                'color' => 'primary',
            ],
            [
                'title' => 'Lengkapi Data',
                'description' => 'Siapkan data dasar peserta didik dan riwayat belajar yang tersedia.',
                'icon' => 'fa-file-lines',
                'color' => 'secondary',
            ],
            [
                'title' => 'Asesmen Kebutuhan',
                'description' => 'Kebutuhan, ritme, dan bentuk pendampingan belajar disusun secara personal.',
                'icon' => 'fa-clipboard-check',
                'color' => 'accent-orange',
            ],
            [
                'title' => 'Aktivasi Akun',
                'description' => 'Akun diaktifkan agar peserta dapat menggunakan LMS dan orang tua melihat tagihan.',
                'icon' => 'fa-user-check',
                'color' => 'accent-yellow',
            ],
        ];

        $requirements = [
            'Data identitas peserta didik',
            'Data orang tua atau wali',
            'Kartu Keluarga dan Akta Kelahiran',
            'Riwayat atau hasil belajar terakhir jika tersedia',
            'Informasi kebutuhan belajar anak',
        ];
    @endphp

    <x-navbar></x-navbar>

    <main>
        <section class="relative min-h-[680px] flex items-center bg-cover bg-center bg-no-repeat overflow-hidden"
            style="background-image: linear-gradient(135deg, rgba(22,95,172,0.84), rgba(40,127,59,0.78)), url('{{ asset($heroContent['background_image'] ?? 'img/bg-ppdb.jpg') }}');">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle at 20% 20%, white 0 2px, transparent 3px); background-size: 42px 42px;">
            </div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-24 text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight mb-6">
                    Pendaftaran<br>
                    <span class="text-accent-yellow">Homeschooling</span>
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-9 max-w-2xl mx-auto">
                    Mulai dengan konsultasi agar pendampingan dan akses belajar dapat disiapkan sesuai kebutuhan anak.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="https://wa.me/6285811258534?text=Halo%20HOK%20Homeschooling,%20saya%20ingin%20konsultasi%20pendaftaran"
                        target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <i class="fab fa-whatsapp"></i>
                        Konsultasi Pendaftaran
                    </a>
                    <a href="#alur"
                        class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white hover:bg-white hover:text-primary font-semibold rounded-full transition-all duration-300">
                        Lihat Alur
                    </a>
                </div>
            </div>
        </section>

        <section class="relative -mt-10 z-20 pb-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-center p-5 bg-gradient-to-br from-primary/10 to-primary/5 rounded-2xl">
                        <div class="text-3xl text-primary mb-2"><i class="fas fa-calendar-check"></i></div>
                        <p class="text-sm text-gray-600 mb-1">Tahap Pertama</p>
                        <p class="text-lg font-bold text-gray-800">Konsultasi Kebutuhan</p>
                    </div>
                    <div class="text-center p-5 bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-2xl">
                        <div class="text-3xl text-secondary mb-2"><i class="fas fa-user-graduate"></i></div>
                        <p class="text-sm text-gray-600 mb-1">Bentuk Layanan</p>
                        <p class="text-lg font-bold text-gray-800">Pendampingan Personal</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="alur" class="py-20 bg-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">Langkah Pendaftaran</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Alur yang Ringkas dan Personal</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Setiap pendaftaran dimulai dengan memahami kebutuhan dan tujuan belajar anak.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
                    @foreach($steps as $index => $step)
                        <article class="card-hover bg-white rounded-2xl shadow-lg p-7 text-center border-t-4 border-{{ $step['color'] }}">
                            <div class="w-14 h-14 mx-auto mb-5 bg-{{ $step['color'] }}/10 text-{{ $step['color'] }} rounded-2xl flex items-center justify-center text-2xl">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </div>
                            <p class="text-sm font-bold text-primary mb-2">Langkah {{ $index + 1 }}</p>
                            <h3 class="text-lg font-bold text-gray-800 mb-3">{{ $step['title'] }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-block px-4 py-2 bg-secondary/10 text-secondary text-sm font-medium rounded-full mb-4">Persiapan Awal</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-5">Data yang Perlu Disiapkan</h2>
                        <p class="text-gray-600 leading-relaxed">Dokumen digunakan untuk mengenali peserta didik dan menyiapkan akses layanan. Informasi tambahan akan disampaikan melalui kontak terkait bila diperlukan.</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($requirements as $requirement)
                            <div class="flex items-start gap-3 bg-cream rounded-xl p-4">
                                <span class="w-7 h-7 bg-secondary rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text-gray-700">{{ $requirement }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="biaya" class="py-20 bg-cream">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="inline-block px-4 py-2 bg-accent-orange/10 text-accent-orange text-sm font-medium rounded-full mb-4">Kisaran Terjangkau</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Rincian Biaya Homeschooling</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Kisaran berikut menjadi gambaran awal dan dapat disesuaikan dengan kebutuhan pendampingan belajar.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                    <article class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-accent-yellow">
                        <p class="text-sm font-semibold text-gray-500 mb-2">Tahap Dasar</p>
                        <p class="text-2xl font-bold text-gray-800 mb-3">Rp300–450 ribu</p>
                        <p class="text-sm text-gray-600">per bulan</p>
                    </article>
                    <article class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-primary">
                        <p class="text-sm font-semibold text-gray-500 mb-2">Tahap Pengembangan</p>
                        <p class="text-2xl font-bold text-gray-800 mb-3">Rp400–550 ribu</p>
                        <p class="text-sm text-gray-600">per bulan</p>
                    </article>
                    <article class="card-hover bg-white rounded-2xl shadow-lg p-8 text-center border-t-4 border-secondary">
                        <p class="text-sm font-semibold text-gray-500 mb-2">Tahap Lanjutan</p>
                        <p class="text-2xl font-bold text-gray-800 mb-3">Rp500–700 ribu</p>
                        <p class="text-sm text-gray-600">per bulan</p>
                    </article>
                </div>

                <p class="text-center text-sm text-gray-500 mt-8">Konfirmasi rincian dan pilihan pendampingan melalui kontak Sipadu Homescholing.</p>
            </div>
        </section>

        <section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Siap Memulai Konsultasi?</h2>
                <p class="text-white/90 mb-8">Hubungi kontak Sipadu Homescholing untuk membicarakan kebutuhan belajar anak Anda.</p>
                <a href="https://wa.me/6285811258534?text=Halo%20HOK%20Homeschooling,%20saya%20ingin%20konsultasi%20pendaftaran"
                    target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-full hover:bg-gray-100 transition">
                    <i class="fab fa-whatsapp"></i>
                    Hubungi via WhatsApp
                </a>
            </div>
        </section>
    </main>

    <x-footer></x-footer>
    @vite(['resources/js/navbar.js'])
</body>

</html>
