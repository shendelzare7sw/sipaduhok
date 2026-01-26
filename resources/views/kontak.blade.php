<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - PKBM House Of Knowledge</title>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#165fac',
                        'secondary': '#287f3b',
                        'accent-orange': '#d45930',
                        'accent-yellow': '#fac030',
                        'accent-bright': '#ffe400',
                        'cream': '#e8e7e2'
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(22, 95, 172, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(22, 95, 172, 0.6);
            }
        }

        @keyframes slide-in-left {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slide-in-right {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .slide-in-left {
            animation: slide-in-left 0.6s ease-out;
        }

        .slide-in-right {
            animation: slide-in-right 0.6s ease-out;
        }

        /* Contact Card Hover Effects */
        .contact-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .contact-card:hover {
            transform: translateY(-8px) scale(1.02);
        }

        .contact-card:hover .icon-wrapper {
            transform: rotate(360deg) scale(1.1);
        }

        .icon-wrapper {
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Social Media Hover */
        .social-btn {
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            transform: translateY(-5px) scale(1.1);
        }

        /* Map Container */
        .map-container {
            position: relative;
            overflow: hidden;
        }

        .map-container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.1) 0%, rgba(40, 127, 59, 0.1) 100%);
            z-index: 1;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .map-container:hover::before {
            opacity: 1;
        }

        /* Decorative Elements */
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        /* Form Styles */
        .form-input {
            transition: all 0.3s ease;
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 95, 172, 0.2);
        }
    </style>
</head>

<body class="bg-white">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $contactSection = $page->getSection('contact_info');
        $contactContent = $contactSection->content ?? [];
        $contactItems = $contactContent['items'] ?? [];

        $locationsSection = $page->getSection('locations');
        $locationsContent = $locationsSection->content ?? [];
        $locationsHeader = $locationsContent['header'] ?? [];
        $locationItems = $locationsContent['items'] ?? [];

        $socialSection = $page->getSection('social_media');
        $socialContent = $socialSection->content ?? [];
        $socialHeader = $socialContent['header'] ?? [];
        $socialItems = $socialContent['items'] ?? [];
    @endphp

    <!-- Navbar Component -->
    <x-navbar></x-navbar>
    <!-- ==================== HERO SECTION ==================== -->
    <section class="relative py-20 overflow-hidden bg-cover bg-center bg-no-repeat"
        style="background-image: linear-gradient(135deg, rgba(22, 95, 172, 0.85) 0%, rgba(40, 127, 59, 0.85) 100%), url('{{ asset($heroContent['background_image'] ?? 'img/bg-kontak.jpg') }}');">
        <!-- Decorative Background Elements -->
        <div class="decorative-circle w-64 h-64 bg-white/10 top-10 -right-20"></div>
        <div class="decorative-circle w-96 h-96 bg-white/10 bottom-0 -left-32"></div>
        <div class="decorative-circle w-40 h-40 bg-accent-yellow/30 top-1/2 right-1/4 float-animation"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <span
                    class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-medium rounded-full mb-6 slide-in-left">
                    {{ $heroContent['badge'] ?? 'Hubungi Kami' }}
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 slide-in-right">
                    {{ $heroContent['title'] ?? 'Kontak' }} <span
                        class="text-accent-yellow">{{ $heroContent['title_highlight'] ?? 'Kami' }}</span>
                </h1>
                <p class="text-lg md:text-xl text-white/90 max-w-3xl mx-auto slide-in-left"
                    style="animation-delay: 0.2s;">
                    {{ $heroContent['subtitle'] ?? 'Kami siap membantu Anda! Jangan ragu untuk menghubungi kami melalui berbagai cara yang tersedia' }}
                </p>
            </div>
        </div>
    </section>

    <!-- ==================== CONTACT CARDS SECTION ==================== -->
    <section class="py-20 bg-cream relative overflow-hidden">
        <div class="absolute top-20 right-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-72 h-72 bg-secondary/5 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                    @php
                        $contactConfig = [
                            'whatsapp' => [
                                'bg_hover' => 'hover:bg-green-50/50',
                                'bar_color' => 'bg-green-500',
                                'icon_bg' => 'bg-green-100',
                                'icon_text' => 'text-green-600',
                                'icon_hover_bg' => 'group-hover:bg-green-600',
                                'text_color' => 'text-green-600',
                                'svg_viewbox' => '0 0 24 24',
                                'icon_path' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />'
                            ],
                            'phone' => [
                                'bg_hover' => 'hover:bg-blue-50/50',
                                'bar_color' => 'bg-primary',
                                'icon_bg' => 'bg-blue-100',
                                'icon_text' => 'text-primary',
                                'icon_hover_bg' => 'group-hover:bg-primary',
                                'text_color' => 'text-primary',
                                'svg_viewbox' => '0 0 20 20',
                                'icon_path' => '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />'
                            ],
                            'email' => [
                                'bg_hover' => 'hover:bg-green-50/50',
                                'bar_color' => 'bg-secondary',
                                'icon_bg' => 'bg-green-100',
                                'icon_text' => 'text-secondary',
                                'icon_hover_bg' => 'group-hover:bg-secondary',
                                'text_color' => 'text-secondary',
                                'svg_viewbox' => '0 0 20 20',
                                'icon_path' => '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />'
                            ]
                        ];
                    @endphp

                    @foreach($contactItems as $item)
                        @php
                            $config = $contactConfig[$item['type'] ?? 'whatsapp'] ?? $contactConfig['whatsapp'];
                        @endphp
                        <a href="{{ $item['link'] ?? '#' }}" target="_blank"
                            class="group relative p-10 text-center transition-all duration-300 {{ $config['bg_hover'] }}">
                            <div
                                class="absolute top-0 left-0 w-full h-1 {{ $config['bar_color'] }} transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                            </div>
                            <div
                                class="w-16 h-16 mx-auto mb-6 {{ $config['icon_bg'] }} {{ $config['icon_text'] }} rounded-2xl flex items-center justify-center {{ $config['icon_hover_bg'] }} group-hover:text-white group-hover:scale-110 transition-all duration-300">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="{{ $config['svg_viewbox'] }}">
                                    {!! $config['icon_path'] !!}
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $item['title'] ?? 'Contact' }}</h3>
                            <div
                                class="inline-flex items-center gap-2 {{ $config['text_color'] }} font-semibold group-hover:gap-3 transition-all mb-3">
                                <span>{{ $item['value'] ?? '' }}</span>
                            </div>
                            <p class="text-gray-500 text-sm">{{ $item['note'] ?? '' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <div class="text-center mb-12">
                <span
                    class="inline-block px-4 py-2 bg-accent-orange/10 text-accent-orange text-sm font-medium rounded-full mb-4">
                    {{ $locationsHeader['badge'] ?? 'Lokasi Kami' }}
                </span>
                <h2 class="text-3xl font-bold text-gray-800">
                    {{ $locationsHeader['title'] ?? 'Kunjungi Cabang Terdekat' }}
                </h2>
                <p class="text-gray-600 mt-2">
                    {{ $locationsHeader['subtitle'] ?? 'Kami hadir di 3 lokasi strategis untuk melayani Anda lebih baik' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $colorClasses = [
                        'orange' => ['border' => 'border-accent-orange', 'text' => 'text-accent-orange', 'hover' => 'hover:bg-accent-orange'],
                        'blue' => ['border' => 'border-primary', 'text' => 'text-primary', 'hover' => 'hover:bg-primary'],
                        'green' => ['border' => 'border-secondary', 'text' => 'text-secondary', 'hover' => 'hover:bg-secondary'],
                    ];
                @endphp

                @foreach($locationItems as $location)
                    @php
                        $color = $colorClasses[$location['color'] ?? 'orange'] ?? $colorClasses['orange'];
                    @endphp
                    <div
                        class="contact-card bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col group border-b-4 {{ $color['border'] }}">
                        <div class="map-container relative h-64 w-full">
                            <iframe src="{{ $location['map_embed'] ?? '' }}" width="100%" height="100%" style="border:0;"
                                allowfullscreen="" loading="lazy" class="w-full h-full object-cover">
                            </iframe>

                            <a href="{{ $location['map_link'] ?? '#' }}" target="_blank"
                                class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-xl shadow-lg flex items-center justify-center gap-2 {{ $color['text'] }} font-bold text-sm {{ $color['hover'] }} hover:text-white transition-all duration-300 z-10">
                                Buka di Google Maps
                            </a>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-gray-800">{{ $location['name'] ?? '' }}</h3>
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-4">
                                {{ $location['area'] ?? '' }}
                            </p>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                {{ $location['address'] ?? '' }}
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- ==================== SOCIAL MEDIA SECTION ==================== -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                {{ $socialHeader['badge'] ?? 'Media Sosial' }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                {{ $socialHeader['title'] ?? 'Ikuti Kami di Sosial Media' }}
            </h2>
            <p class="text-gray-600 mb-12">
                {{ $socialHeader['subtitle'] ?? 'Dapatkan update terbaru tentang kegiatan dan informasi sekolah' }}
            </p>

            <div class="flex flex-wrap justify-center gap-6">
                <!-- Facebook -->
                <a href="https://www.facebook.com/profile.php?id=100054416532781 " target="_blank"
                    class="social-btn w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-2xl">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                </a>

                <!-- Instagram -->
                <a href="https://www.instagram.com/hok_homeschool?igsh=MTE1Ymg1Mmt0NzNzdA==" target="_blank"
                    class="social-btn w-16 h-16 bg-gradient-to-br from-purple-600 via-pink-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-2xl">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                    </svg>
                </a>

                <!-- YouTube -->
                <a href="https://youtube.com/@houseofknowledgepamulang5963?si=iGVJsrKalXFsNcaw " target="_blank"
                    class="social-btn w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-2xl">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                    </svg>
                </a>

                <!-- TikTok -->
                <a href="https://www.tiktok.com/@hokhomeschool?_r=1&_t=ZS-92IrquFuJOr" target="_blank"
                    class="social-btn w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-2xl">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== CTA SECTION ==================== -->
    <section class="py-20 bg-gradient-to-br from-primary via-blue-600 to-secondary relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-accent-yellow/10 rounded-full blur-3xl"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Siap Bergabung dengan Kami?
            </h2>
            <p class="text-lg text-white/90 mb-8 max-w-2xl mx-auto">
                Hubungi kami sekarang untuk informasi lebih lanjut tentang program pendidikan dan pendaftaran
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/6281234567890?text=Halo%20PKBM%20House%20Of%20Knowledge,%20saya%20ingin%20mendaftar"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    Hubungi via WhatsApp
                </a>
                <a href="tel:02174427521"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-primary transition-all duration-300">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    Telepon Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Component -->
    <x-footer></x-footer>

    <!-- Smooth Scroll Script -->
    <script>
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-in-left');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.contact-card').forEach(card => {
            observer.observe(card);
        });
    </script>

</body>

</html>