<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Syarat & Ketentuan - PKBM House Of Knowledge" description="Syarat dan Ketentuan penggunaan platform SipaduHOK milik PKBM House Of Knowledge. Pahami hak dan kewajiban Anda sebagai pengguna layanan kami." keywords="syarat dan ketentuan, terms and conditions, PKBM House Of Knowledge, SipaduHOK, ketentuan penggunaan"></x-seo-meta>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/syarat-ketentuan.css'])
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
                <span class="font-semibold">Syarat & Ketentuan</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Syarat & Ketentuan</h1>
            <p class="mt-4 text-lg text-gray-200 max-w-2xl mx-auto">Ketentuan penggunaan platform SipaduHOK</p>
        </div>
    </section>

    <!-- Last Updated -->
    <section class="py-8 bg-white border-b">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="inline-flex items-center bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Terakhir diperbarui: 1 Juli 2025
            </span>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Intro -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">Pendahuluan</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Selamat datang di platform SipaduHOK yang dikelola oleh PKBM House Of Knowledge. Syarat dan Ketentuan ini mengatur penggunaan layanan kami yang dapat diakses melalui <strong>app.sipaduhok.id</strong>. Dengan mendaftar atau menggunakan platform ini, Anda menyetujui untuk terikat dengan ketentuan yang diuraikan di bawah ini.
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            Mohon baca Syarat & Ketentuan ini dengan saksama sebelum menggunakan layanan kami. Jika Anda tidak menyetujui salah satu atau seluruh ketentuan ini, mohon untuk tidak menggunakan platform kami.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 1: Ketentuan Umum -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Ketentuan Umum</h2>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li><strong>"Platform"</strong> merujuk pada sistem informasi SipaduHOK yang dapat diakses melalui website app.sipaduhok.id</li>
                            <li><strong>"Pengguna"</strong> merujuk pada setiap individu yang mengakses atau menggunakan platform, termasuk siswa, wali/orang tua, guru, staf administrasi, dan pihak terkait lainnya</li>
                            <li><strong>"Kami"</strong> atau <strong>"Pengelola"</strong> merujuk pada PKBM House Of Knowledge sebagai penyelenggara platform</li>
                            <li><strong>"Layanan"</strong> merujuk pada seluruh fitur dan fungsi yang tersedia di platform SipaduHOK</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 2: Akun Pengguna -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Akun Pengguna</h2>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Akun pengguna dibuat dan dikelola oleh administrator PKBM House Of Knowledge</li>
                            <li>Setiap pengguna bertanggung jawab untuk menjaga kerahasiaan kredensial login (username dan password)</li>
                            <li>Pengguna wajib segera melaporkan jika mengetahui adanya penggunaan akun yang tidak sah</li>
                            <li>Satu akun hanya boleh digunakan oleh satu orang pengguna yang bersangkutan</li>
                            <li>Kami berhak menonaktifkan akun yang terbukti disalahgunakan atau melanggar ketentuan ini</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 3: Kewajiban Pengguna -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">3. Kewajiban Pengguna</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Setiap pengguna <strong>wajib</strong> untuk:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4 mb-4">
                            <li>Memberikan informasi yang akurat dan terkini saat pendaftaran</li>
                            <li>Menggunakan platform hanya untuk tujuan pendidikan yang sah</li>
                            <li>Menghormati privasi pengguna lain dan menjaga kerahasiaan data yang diakses</li>
                            <li>Mematuhi seluruh peraturan yang berlaku di PKBM House Of Knowledge</li>
                        </ul>
                        <p class="text-gray-600 leading-relaxed mb-4">Pengguna <strong>dilarang</strong> untuk:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Mengakses data atau fitur yang bukan merupakan haknya</li>
                            <li>Mengunggah konten yang mengandung unsur SARA, pornografi, atau kekerasan</li>
                            <li>Melakukan tindakan yang dapat merusak, menonaktifkan, atau membebani platform</li>
                            <li>Menggunakan alat otomatis (bot, scraper) untuk mengakses platform</li>
                            <li>Membagikan kredensial login kepada pihak lain</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 4: Layanan Platform -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">4. Layanan Platform</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Platform SipaduHOK menyediakan layanan yang mencakup namun tidak terbatas pada:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    Manajemen Akademik
                                </h4>
                                <p class="text-sm text-gray-600">Pengelolaan data siswa, kelas, jadwal, dan tahun ajaran</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Learning Management System
                                </h4>
                                <p class="text-sm text-gray-600">Materi pembelajaran, tugas, ujian, dan forum diskusi</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Penilaian & Rapor
                                </h4>
                                <p class="text-sm text-gray-600">Input nilai, generasi rapor, dan validasi akademik</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Manajemen Keuangan
                                </h4>
                                <p class="text-sm text-gray-600">Tagihan SPP, pembayaran online, dan laporan keuangan</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    Presensi
                                </h4>
                                <p class="text-sm text-gray-600">Pencatatan kehadiran dan pengajuan izin</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Notifikasi
                                </h4>
                                <p class="text-sm text-gray-600">Pemberitahuan kegiatan, pengumuman, dan informasi penting</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Pembayaran -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Ketentuan Pembayaran</h2>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Pembayaran SPP dan biaya pendidikan lainnya dilakukan sesuai tagihan yang diterbitkan oleh pihak sekolah</li>
                            <li>Pembayaran dapat dilakukan melalui metode yang tersedia di platform (transfer bank, payment gateway)</li>
                            <li>Bukti pembayaran yang sah akan tercatat otomatis di sistem setelah diverifikasi</li>
                            <li>Keterlambatan pembayaran dapat mengakibatkan pembatasan akses terhadap fitur tertentu di platform</li>
                            <li>Pengembalian dana (refund) mengikuti kebijakan yang ditetapkan oleh PKBM House Of Knowledge</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 6: Hak Kekayaan Intelektual -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Hak Kekayaan Intelektual</h2>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Seluruh konten platform termasuk desain, logo, teks, dan fitur adalah milik PKBM House Of Knowledge</li>
                            <li>Materi pembelajaran yang diunggah oleh guru merupakan hak cipta dari pembuat materi tersebut</li>
                            <li>Pengguna tidak diperkenankan menyalin, mendistribusikan, atau memodifikasi konten platform tanpa izin tertulis</li>
                            <li>Karya siswa yang diunggah ke platform tetap menjadi hak milik siswa yang bersangkutan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 7: Batasan Tanggung Jawab -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Batasan Tanggung Jawab</h2>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Platform disediakan dalam kondisi "sebagaimana adanya" (as is) tanpa jaminan apapun</li>
                            <li>Kami berusaha menjaga ketersediaan platform 24/7, namun tidak menjamin bebas dari gangguan atau error</li>
                            <li>Kami tidak bertanggung jawab atas kerugian yang timbul akibat gangguan teknis, force majeure, atau tindakan pihak ketiga</li>
                            <li>Kami tidak bertanggung jawab atas kehilangan data yang disebabkan oleh kelalaian pengguna dalam menjaga keamanan akun</li>
                            <li>Maintenance dan pembaruan sistem dapat menyebabkan layanan tidak tersedia sementara</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 8: Penangguhan & Penghentian -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Penangguhan & Penghentian Akun</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Kami berhak menangguhkan atau menghentikan akun pengguna jika:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Pengguna melanggar Syarat & Ketentuan ini</li>
                            <li>Terdeteksi aktivitas mencurigakan atau upaya peretasan</li>
                            <li>Siswa sudah lulus, pindah, atau berhenti dari PKBM House Of Knowledge</li>
                            <li>Atas permintaan pengguna yang bersangkutan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 9: Perubahan Ketentuan -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Perubahan Syarat & Ketentuan</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Kami berhak mengubah Syarat & Ketentuan ini dari waktu ke waktu. Perubahan akan berlaku efektif setelah dipublikasikan di halaman ini. Penggunaan platform yang berkelanjutan setelah perubahan dianggap sebagai persetujuan terhadap ketentuan yang diperbarui. Kami akan berusaha menginformasikan perubahan signifikan melalui notifikasi di platform.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 10: Hukum yang Berlaku -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Hukum yang Berlaku</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Syarat & Ketentuan ini tunduk pada dan ditafsirkan sesuai dengan hukum yang berlaku di Republik Indonesia. Segala perselisihan yang timbul dari penggunaan platform akan diselesaikan secara musyawarah terlebih dahulu. Apabila musyawarah tidak mencapai kesepakatan, penyelesaian akan dilakukan melalui pengadilan yang berwenang di wilayah hukum Kota Tangerang Selatan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 11: Kontak -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8 border-2 border-emerald-100">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">11. Hubungi Kami</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Jika Anda memiliki pertanyaan mengenai Syarat & Ketentuan ini, silakan hubungi:
                        </p>
                        <div class="bg-gray-50 rounded-xl p-6 space-y-3">
                            <p class="text-gray-700"><strong>PKBM House Of Knowledge</strong></p>
                            <p class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Jl. Ruko Reni Jaya No.22, RW.23, Pamulang Bar., Kec. Pamulang, Kota Tangerang Selatan
                            </p>
                            <p class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                hokhomeschool@gmail.com
                            </p>
                            <p class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                +62 858-1125-8534
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <x-footer></x-footer>

</body>
</html>
