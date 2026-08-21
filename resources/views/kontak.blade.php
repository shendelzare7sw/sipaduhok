<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta title="Kontak - Sipadu Homescholing" description="Hubungi Sipadu Homescholing melalui WhatsApp, telepon, atau email."></x-seo-meta>

    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/landing.css', 'resources/css/navbar.css', 'resources/css/pages/kontak.css'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-white">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        $contactSection = $page->getSection('contact_info');
        $contactContent = $contactSection->content ?? [];
        $contactItems = $contactContent['items'] ?? [];

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

                    @foreach(($contactItems ?? $contactContent['items'] ?? []) as $item)
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
                                class="inline-flex items-center gap-2 {{ $config['text_color'] }} font-semibold group-hover:gap-3 transition-all mb-3 text-sm md:text-base">
                                <span>{{ $item['value'] ?? '' }}</span>
                            </div>
                            <p class="text-gray-500 text-sm">{{ $item['note'] ?? '' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Alamat lama tidak ditampilkan sampai alamat layanan baru dikonfirmasi. --}}
    @if(false)
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
                        $inputColor = $location['color'] ?? 'orange';
                        $isCustom = str_starts_with($inputColor, '#');
                        
                        if (!$isCustom) {
                            $colorMap = $colorClasses[$inputColor] ?? $colorClasses['orange'];
                            $borderClass = $colorMap['border'];
                            $textClass = $colorMap['text'];
                            $hoverClass = $colorMap['hover'];
                            $borderStyle = '';
                            $textStyle = '';
                        } else {
                            $borderClass = '';
                            $textClass = '';
                            $hoverClass = '';
                            $borderStyle = "border-bottom-color: {$inputColor} !important;";
                            $textStyle = "color: {$inputColor} !important;";
                        }
                    @endphp
                    <div
                        class="contact-card bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col group border-b-4 {{ $borderClass }}"
                        style="{{ $borderStyle }}">
                        <div class="map-container relative h-64 w-full">
                            <iframe src="{{ $location['map_embed'] ?? '' }}" width="100%" height="100%" style="border:0;"
                                allowfullscreen="" loading="lazy" class="w-full h-full object-cover">
                            </iframe>

                            <a href="{{ $location['map_link'] ?? '#' }}" target="_blank"
                                class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-xl shadow-lg flex items-center justify-center gap-2 {{ $textClass }} font-bold text-sm {{ $hoverClass }} hover:text-white transition-all duration-300 z-10"
                                style="{{ $textStyle }}"
                                @if($isCustom)
                                    onmouseover="this.style.backgroundColor='{{ $inputColor }}'; this.style.color='#ffffff';"
                                    onmouseout="this.style.backgroundColor='rgba(255,255,255,0.9)'; this.style.color='{{ $inputColor }}';"
                                @endif
                                >
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
    @endif

    <!-- ==================== CTA SECTION ==================== -->
    <section class="py-20 bg-gradient-to-br from-primary via-blue-600 to-secondary relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-accent-yellow/10 rounded-full blur-3xl"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Perlu Informasi Homeschooling?
            </h2>
            <p class="text-lg text-white/90 mb-8 max-w-2xl mx-auto">
                Hubungi kami untuk berkonsultasi mengenai pendampingan belajar dan proses pendaftaran.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/6285811258534?text=Halo%20HOK%20Homeschooling,%20saya%20ingin%20bertanya"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    Hubungi via WhatsApp
                </a>
                <a href="tel:6285811258534"
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
    @vite(['resources/js/navbar.js', 'resources/js/pages/kontak.js'])
</body>

</html>
