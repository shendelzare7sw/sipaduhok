<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legalitas - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-overlay { background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%); }
        .doc-card { transition: all 0.3s ease; }
        .doc-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
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
                <span class="font-semibold">Legalitas</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">Legalitas</h1>
        </div>
    </section>

    <!-- Intro -->
    <section class="py-20">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Dokumen Resmi</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Legalitas PKBM House Of Knowledge</h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                PKBM House Of Knowledge adalah lembaga pendidikan resmi yang telah terdaftar dan memiliki izin operasional dari instansi berwenang. Berikut adalah dokumen legalitas kami.
            </p>
        </div>
    </section>

    <!-- Documents Grid -->
    <section class="py-10 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Document 1 -->
                <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-[#165fac] to-[#287f3b] flex items-center justify-center">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-xl mb-2">Akta Pendirian Yayasan</h3>
                        <p class="text-gray-600 text-sm mb-4">Nomor: AHU-0012345.AH.01.04.Tahun 2014</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            <a href="#" class="text-[#165fac] font-medium text-sm hover:underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Document 2 -->
                <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-[#d45930] to-[#fac030] flex items-center justify-center">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-xl mb-2">Izin Operasional PKBM</h3>
                        <p class="text-gray-600 text-sm mb-4">Nomor: 421.9/1234/Disdik/2024</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            <a href="#" class="text-[#165fac] font-medium text-sm hover:underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Document 3 -->
                <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-[#287f3b] to-[#165fac] flex items-center justify-center">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-xl mb-2">Akreditasi PKBM</h3>
                        <p class="text-gray-600 text-sm mb-4">Peringkat: B (Baik)</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            <a href="#" class="text-[#165fac] font-medium text-sm hover:underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Document 4 -->
                <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-[#fac030] to-[#d45930] flex items-center justify-center">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-xl mb-2">NPWP Yayasan</h3>
                        <p class="text-gray-600 text-sm mb-4">Nomor: 12.345.678.9-012.000</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            <a href="#" class="text-[#165fac] font-medium text-sm hover:underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Document 5 -->
                <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-[#165fac] to-[#fac030] flex items-center justify-center">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-xl mb-2">Surat Keterangan Domisili</h3>
                        <p class="text-gray-600 text-sm mb-4">Nomor: 474/123/Kel.PB/2024</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            <a href="#" class="text-[#165fac] font-medium text-sm hover:underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Document 6 -->
                <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-[#287f3b] to-[#fac030] flex items-center justify-center">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-xl mb-2">NIB (Nomor Induk Berusaha)</h3>
                        <p class="text-gray-600 text-sm mb-4">Nomor: 1234567890123</p>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Aktif
                            </span>
                            <a href="#" class="text-[#165fac] font-medium text-sm hover:underline">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download All -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Unduh Semua Dokumen</h3>
            <p class="text-gray-600 mb-6">Dapatkan semua dokumen legalitas dalam satu file ZIP</p>
            <a href="#" class="inline-flex items-center px-8 py-4 bg-[#165fac] text-white font-semibold rounded-full hover:bg-[#287f3b] transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                Download Semua (PDF)
            </a>
        </div>
    </section>

    <x-footer></x-footer>

</body>
</html>
