<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];
    @endphp
    <title>{{ $heroContent['title'] ?? 'Visi & Misi' }} - PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-overlay {
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%);
        }
    </style>
</head>

<body class="bg-gray-50">

    <x-navbar></x-navbar>

    <!-- Hero Section -->
    <section class="relative h-[400px] flex items-center justify-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span>Profil</span>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Visi & Misi' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Visi & Misi' }}</h1>
        </div>
    </section>

    @php
        $visiSection = $page->getSection('visi');
        $visiContent = $visiSection->content ?? [];
    @endphp

    <!-- Visi Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $visiContent['badge'] ?? 'Visi Kami' }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        {{ $visiContent['title'] ?? 'Visi PKBM House Of Knowledge' }}</h2>
                    <div class="bg-white rounded-2xl p-8 shadow-lg border-l-4 border-[#165fac]">
                        <p class="text-gray-700 text-lg leading-relaxed italic">
                            "{{ $visiContent['content'] ?? 'Membentuk manusia yang memiliki kualitas iman dan taqwa, mandiri, disiplin, bertanggung jawab dan berpandangan positif dalam menghadapi hidup' }}"
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ asset($visiContent['image'] ?? 'img/visi-misi.jpg') }}" alt="Visi"
                        class="rounded-2xl shadow-xl w-full h-[350px] object-cover">
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-[#fac030] rounded-2xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    @php
        $misiSection = $page->getSection('misi');
        $misiContent = $misiSection->content ?? [];
        $misiHeader = $misiContent['header'] ?? [];
        $misiItems = $misiContent['items'] ?? [];

        $colorMap = [
            'primary' => '#165fac',
            'secondary' => '#287f3b',
            'accent-orange' => '#d45930',
            'accent-yellow' => '#fac030',
        ];
    @endphp

    <!-- Misi Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span
                    class="inline-block bg-[#287f3b]/10 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $misiHeader['badge'] ?? 'Misi Kami' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">
                    {{ $misiHeader['title'] ?? 'Misi PKBM House Of Knowledge' }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($misiItems as $index => $item)
                    @php
                        $colorValue = $item['color'] ?? 'primary';
                        // Check if it's a hex code
                        if (str_starts_with($colorValue, '#')) {
                            $itemColor = $colorValue;
                        } else {
                            $itemColor = $colorMap[$colorValue] ?? '#165fac';
                        }
                    @endphp
                    <div class="bg-gray-50 rounded-2xl p-6 border-t-4 hover:shadow-lg transition"
                        style="border-color: {{ $itemColor }}">
                        <div class="w-12 h-12 text-white rounded-full flex items-center justify-center font-bold text-xl mb-4"
                            style="background-color: {{ $itemColor }}">{{ $index + 1 }}</div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @php
        $valuesSection = $page->getSection('values');
        $valuesContent = $valuesSection->content ?? [];
        $valuesHeader = $valuesContent['header'] ?? [];
        $valuesItems = $valuesContent['items'] ?? [];

        $iconColorMap = [
            'orange' => 'text-orange-400',
            'yellow' => 'text-yellow-300',
            'green' => 'text-green-500',
            'red' => 'text-red-500',
        ];
    @endphp

    <!-- Values Section -->
    <section class="py-20 bg-[#165fac]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    {{ $valuesHeader['title'] ?? 'Nilai-Nilai Kami' }}</h2>
                <p class="text-white/80">
                    {{ $valuesHeader['description'] ?? 'Prinsip yang menjadi landasan setiap aktivitas kami' }}</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($valuesItems as $item)
                    @php
                        $iconColorValue = $item['icon_color'] ?? 'orange';
                        $isHex = str_starts_with($iconColorValue, '#');
                        $iconClass = $isHex ? '' : ($iconColorMap[$iconColorValue] ?? 'text-orange-400');
                        $iconStyle = $isHex ? "color: {$iconColorValue};" : "";
                    @endphp
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center">
                        <div class="text-4xl mb-3 flex justify-center">
                             @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                                <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] ?? 'Icon' }}" class="w-12 h-12 object-contain">
                             @else
                                <i class="{{ $item['icon'] ?? 'fas fa-star' }} {{ $iconClass }}" style="{{ $iconStyle }}"></i>
                             @endif
                        </div>
                        <h3 class="text-white font-bold">{{ $item['title'] }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-footer></x-footer>

</body>

</html>