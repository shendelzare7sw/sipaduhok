<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Kebijakan Privasi - PKBM House Of Knowledge" description="Kebijakan Privasi PKBM House Of Knowledge menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda di platform SipaduHOK." keywords="kebijakan privasi, privacy policy, PKBM House Of Knowledge, perlindungan data, SipaduHOK"></x-seo-meta>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/kebijakan-privasi.css'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 overflow-x-hidden">

    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center" style="background-image: url('{{ asset('img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span class="font-semibold">Kebijakan Privasi</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Kebijakan Privasi</h1>
            <p class="mt-4 text-lg text-gray-200 max-w-2xl mx-auto">Komitmen kami dalam melindungi data pribadi Anda</p>
        </div>
    </section>

    <!-- Last Updated -->
    <section class="py-8 bg-white border-b">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">Pendahuluan</h2>
                        <p class="text-gray-600 leading-relaxed">
                            PKBM House Of Knowledge ("kami") menghargai privasi setiap pengguna platform SipaduHOK. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda ketika menggunakan layanan kami melalui website <strong>app.sipaduhok.id</strong>.
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            Dengan mengakses atau menggunakan platform SipaduHOK, Anda menyetujui praktik yang dijelaskan dalam Kebijakan Privasi ini. Jika Anda tidak menyetujui kebijakan ini, mohon untuk tidak menggunakan layanan kami.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 1: Data yang Dikumpulkan -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Data yang Kami Kumpulkan</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Kami mengumpulkan beberapa jenis informasi untuk menyelenggarakan layanan pendidikan:</p>

                        <h3 class="text-lg font-semibold text-gray-700 mb-2">a. Data Identitas Siswa</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-1 mb-4 ml-4">
                            <li>Nama lengkap, tempat dan tanggal lahir</li>
                            <li>Nomor Induk Siswa Nasional (NISN)</li>
                            <li>Jenis kelamin dan agama</li>
                            <li>Alamat tempat tinggal</li>
                            <li>Foto profil (jika diunggah)</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-700 mb-2">b. Data Wali/Orang Tua</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-1 mb-4 ml-4">
                            <li>Nama lengkap wali/orang tua</li>
                            <li>Nomor telepon dan email</li>
                            <li>Alamat tempat tinggal</li>
                            <li>Hubungan dengan siswa</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-700 mb-2">c. Data Akademik</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-1 mb-4 ml-4">
                            <li>Nilai dan rapor</li>
                            <li>Data presensi/kehadiran</li>
                            <li>Riwayat kelas dan tahun ajaran</li>
                            <li>Hasil ujian dan tugas di LMS</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-700 mb-2">d. Data Keuangan</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-1 mb-4 ml-4">
                            <li>Riwayat tagihan dan pembayaran SPP</li>
                            <li>Bukti transfer pembayaran</li>
                            <li>Status pembayaran</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-700 mb-2">e. Data Teknis</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-1 ml-4">
                            <li>Alamat IP dan jenis browser</li>
                            <li>Data log akses dan aktivitas di platform</li>
                            <li>Cookie dan teknologi pelacakan serupa</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 2: Penggunaan Data -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Penggunaan Data</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Data yang kami kumpulkan digunakan untuk:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li>Mengelola proses pendaftaran dan administrasi siswa</li>
                            <li>Menyediakan layanan pembelajaran melalui Learning Management System (LMS)</li>
                            <li>Mengelola presensi, penilaian, dan rapor siswa</li>
                            <li>Memproses tagihan dan pembayaran SPP</li>
                            <li>Mengirimkan notifikasi terkait kegiatan akademik dan keuangan</li>
                            <li>Berkomunikasi dengan wali/orang tua mengenai perkembangan siswa</li>
                            <li>Meningkatkan kualitas layanan dan pengalaman pengguna platform</li>
                            <li>Memenuhi kewajiban hukum dan regulasi pendidikan yang berlaku</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 3: Perlindungan Data -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">3. Perlindungan Data</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Kami menerapkan langkah-langkah keamanan untuk melindungi data Anda:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li><strong>Enkripsi:</strong> Seluruh komunikasi data menggunakan protokol HTTPS/SSL</li>
                            <li><strong>Autentikasi:</strong> Sistem login yang aman dengan password terenkripsi (bcrypt)</li>
                            <li><strong>Otorisasi:</strong> Akses data dibatasi berdasarkan peran pengguna (role-based access control)</li>
                            <li><strong>Backup:</strong> Data dicadangkan secara berkala untuk mencegah kehilangan data</li>
                            <li><strong>Monitoring:</strong> Pemantauan aktivitas mencurigakan dan perlindungan terhadap serangan siber</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 4: Berbagi Data -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">4. Berbagi Data dengan Pihak Ketiga</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Kami <strong>tidak menjual</strong> data pribadi Anda kepada pihak ketiga. Data hanya dapat dibagikan dalam kondisi berikut:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li><strong>Kementerian Pendidikan:</strong> Pelaporan data siswa sesuai regulasi pendidikan nasional (Dapodik)</li>
                            <li><strong>Payment Gateway:</strong> Data transaksi yang diperlukan untuk memproses pembayaran online (Midtrans)</li>
                            <li><strong>Kewajiban Hukum:</strong> Jika diwajibkan oleh hukum, regulasi, atau proses hukum yang berlaku</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 5: Hak Pengguna -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Hak Pengguna</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Anda memiliki hak-hak berikut terkait data pribadi Anda:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Hak Akses
                                </h4>
                                <p class="text-sm text-gray-600">Meminta salinan data pribadi yang kami simpan</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Hak Koreksi
                                </h4>
                                <p class="text-sm text-gray-600">Meminta perbaikan data yang tidak akurat</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hak Penghapusan
                                </h4>
                                <p class="text-sm text-gray-600">Meminta penghapusan data sesuai ketentuan berlaku</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="font-semibold text-gray-700 mb-1 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Hak Pembatasan
                                </h4>
                                <p class="text-sm text-gray-600">Membatasi pengolahan data dalam kondisi tertentu</p>
                            </div>
                        </div>
                        <p class="text-gray-600 leading-relaxed mt-4">
                            Untuk menggunakan hak-hak di atas, silakan hubungi kami melalui email <strong>hokhomeschool@gmail.com</strong> atau datang langsung ke kantor kami.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 6: Cookie -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Cookie & Teknologi Pelacakan</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">Platform kami menggunakan cookie untuk:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 ml-4">
                            <li><strong>Cookie Sesi:</strong> Menjaga sesi login Anda tetap aktif selama menggunakan platform</li>
                            <li><strong>Cookie Keamanan:</strong> Melindungi akun Anda dari akses yang tidak sah (CSRF token)</li>
                            <li><strong>Cookie Preferensi:</strong> Menyimpan preferensi tampilan dan pengaturan Anda</li>
                        </ul>
                        <p class="text-gray-600 leading-relaxed mt-4">
                            Kami <strong>tidak menggunakan</strong> cookie pelacakan untuk iklan atau analitik pihak ketiga.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 7: Retensi Data -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Retensi Data</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Data pribadi siswa dan akademik akan disimpan selama siswa masih terdaftar dan selama diperlukan untuk keperluan administrasi, pelaporan, dan arsip sesuai dengan peraturan pendidikan yang berlaku. Setelah siswa lulus atau berhenti, data akan diarsipkan dan dapat dihapus atas permintaan setelah jangka waktu yang ditetapkan oleh regulasi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 8: Perubahan Kebijakan -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Perubahan Kebijakan Privasi</h2>
                        <p class="text-gray-600 leading-relaxed">
                            Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan signifikan akan diinformasikan melalui notifikasi di platform atau melalui komunikasi langsung kepada pengguna. Tanggal "Terakhir diperbarui" di bagian atas halaman ini menunjukkan kapan kebijakan ini terakhir direvisi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 9: Kontak -->
            <div class="legal-card bg-white rounded-2xl shadow-lg p-8 md:p-10 mb-8 border-2 border-blue-100">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Hubungi Kami</h2>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Jika Anda memiliki pertanyaan atau kekhawatiran mengenai Kebijakan Privasi ini, silakan hubungi kami:
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

    @vite(['resources/js/navbar.js'])
</body>
</html>
