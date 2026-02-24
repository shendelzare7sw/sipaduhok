@php
    $content = $content ?? [];
@endphp

<!-- About Program -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <img src="{{ asset($content['image'] ?? 'img/sma-main.jpg') }}" alt="SMA"
                    class="rounded-2xl shadow-xl w-full h-[400px] object-cover"
                    onerror="this.src='https://via.placeholder.com/800x400/d45930/ffffff?text=Paket+C+(SMA)'">
                <div
                    class="absolute -bottom-6 -right-6 bg-[#d45930] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                    <p class="text-2xl font-bold">{{ $content['label'] ?? 'Paket C' }}</p>
                    <p class="text-sm">{{ $content['label_subtitle'] ?? 'Setara SMA' }}</p>
                </div>
            </div>
            <div>
                <span
                    class="inline-block bg-[#d45930]/20 text-[#d45930] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $content['badge'] ?? 'Pendidikan Kesetaraan' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                    {{ $content['title'] ?? 'Paket C (Setara SMA)' }}</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_1'] ?? 'Program Paket C adalah program pendidikan kesetaraan tertinggi yang setara dengan Sekolah Menengah Atas (SMA). Ijazah Paket C dapat digunakan untuk melanjutkan ke perguruan tinggi atau melamar pekerjaan.' }}
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_2'] ?? 'Program ini cocok untuk mereka yang ingin menyelesaikan pendidikan setingkat SMA dengan waktu yang lebih fleksibel tanpa mengorbankan kualitas pendidikan.' }}
                </p>
                <div class="flex flex-wrap gap-3">
                    <span
                        class="bg-[#d45930]/10 text-[#d45930] px-4 py-2 rounded-full text-sm font-medium">{{ $content['tag_1'] ?? 'Bisa Kuliah' }}</span>
                    <span
                        class="bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-medium">{{ $content['tag_2'] ?? 'Bisa Kerja' }}</span>
                    <span
                        class="bg-[#287f3b]/10 text-[#287f3b] px-4 py-2 rounded-full text-sm font-medium">{{ $content['tag_3'] ?? 'Ijazah Resmi' }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jurusan -->
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $jurusan['header']['title'] ?? 'Pilihan Jurusan' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @php $jurusanItems = $jurusan['items'] ?? []; @endphp
            @foreach($jurusanItems as $item)
                @php
                    $startColor = $item['card_gradient_start'] ?? '#1e5f8a';
                    $endColor = $item['card_gradient_end'] ?? '#2e8b57';
                    $features = isset($item['features']) ? explode('|', $item['features']) : [];
                    // Support both named colors (legacy) and hex values from admin
                    $rawIconColor = $item['icon_color'] ?? 'white';
                    $iconColorMap = [
                        'blue' => '#165fac', 'green' => '#287f3b',
                        'orange' => '#d45930', 'yellow' => '#fac030',
                        'white' => '#ffffff',
                    ];
                    $iconHex = (str_starts_with($rawIconColor, '#') && strlen($rawIconColor) === 7)
                        ? $rawIconColor
                        : ($iconColorMap[$rawIconColor] ?? '#ffffff');
                @endphp
                <div class="rounded-3xl p-8 text-white shadow-lg"
                     style="background: linear-gradient(to bottom right, {{ $startColor }}, {{ $endColor }});">
                    <div class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-sm">
                         @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                            <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-10 h-10 object-contain">
                         @elseif(!empty($item['icon']) && (str_starts_with($item['icon'], 'fa') || str_starts_with($item['icon'], 'bx')))
                            <i class="{{ $item['icon'] }} text-3xl" style="color: {{ $iconHex }}"></i>
                         @else
                            @php $title = strtolower($item['title'] ?? ''); @endphp
                            @if(str_contains($title, 'ipa') || str_contains($title, 'sains') || str_contains($title, 'mipa'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M9 3v6.5L5.5 15c-1.3 2-.2 4.5 2 4.5h9c2.2 0 3.3-2.5 2-4.5L15 9.5V3h1a1 1 0 100-2H8a1 1 0 100 2h1zm2 0h2v7l3.5 5.5c.4.6.1 1.5-.7 1.5h-7.6c-.8 0-1.1-.9-.7-1.5L11 10V3z"/>
                                </svg>
                            @elseif(str_contains($title, 'ips') || str_contains($title, 'sosial'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            @else
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M12 4.5C9.24 4.5 6.5 5.5 4 7.08V19.5c2.5-1.42 5.24-2.5 8-2.5s5.5 1.08 8 2.5V7.08C17.5 5.5 14.76 4.5 12 4.5zm0 12c-2.03 0-4.04.37-5.96 1.12V8.13C8.04 7.37 10.03 7 12 7s3.96.37 5.96 1.13v9.49C16.04 16.87 14.03 16.5 12 16.5z"/>
                                </svg>
                            @endif
                         @endif
                    </div>
                    <h3 class="text-2xl font-bold mb-3">{{ $item['title'] }}</h3>
                    <p class="mb-8 text-white/90 leading-relaxed">
                        {{ $item['description'] }}
                    </p>
                    <ul class="space-y-3">
                        @foreach($features as $feature)
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="font-medium">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Prospek -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $prospek['header']['title'] ?? 'Prospek Setelah Lulus' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @php $prospekItems = $prospek['items'] ?? []; @endphp
            @foreach($prospekItems as $item)
                @php
                    // Support both named colors (legacy) and hex values from admin
                    $rawIconColor = $item['icon_color'] ?? 'slate';
                    $iconColorMap = [
                        'slate' => '#1e293b', 'amber' => '#78350f',
                        'blue' => '#165fac', 'orange' => '#d45930',
                        'green' => '#287f3b', 'yellow' => '#fac030',
                    ];
                    $iconHex = (str_starts_with($rawIconColor, '#') && strlen($rawIconColor) === 7)
                        ? $rawIconColor
                        : ($iconColorMap[$rawIconColor] ?? '#1e293b');
                @endphp
                <div
                    class="card-hover bg-white rounded-2xl p-6 text-center shadow-lg transition-shadow duration-300 hover:shadow-xl">
                    <div class="mb-6 flex justify-center">
                         @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                            <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-16 h-16 object-contain">
                         @elseif(!empty($item['icon']) && (str_starts_with($item['icon'], 'fa') || str_starts_with($item['icon'], 'bx')))
                            <i class="{{ $item['icon'] }} text-5xl" style="color: {{ $iconHex }}"></i>
                         @else
                            @php $title = strtolower($item['title'] ?? ''); @endphp
                            @if(str_contains($title, 'kuliah') || str_contains($title, 'universitas') || str_contains($title, 'perguruan'))
                                <svg class="w-16 h-16" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                                </svg>
                            @elseif(str_contains($title, 'kerja') || str_contains($title, 'karir') || str_contains($title, 'profesi'))
                                <svg class="w-16 h-16" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                                </svg>
                            @elseif(str_contains($title, 'wirausaha') || str_contains($title, 'usaha') || str_contains($title, 'bisnis'))
                                <svg class="w-16 h-16" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                                </svg>
                            @elseif(str_contains($title, 'pelatihan') || str_contains($title, 'kursus') || str_contains($title, 'skill'))
                                <svg class="w-16 h-16" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
                                </svg>
                            @else
                                <svg class="w-16 h-16" viewBox="0 0 24 24" fill="{{ $iconHex }}">
                                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                                </svg>
                            @endif
                         @endif
                    </div>
                    <h3 class="font-bold text-gray-800 text-xl">{{ $item['title'] }}</h3>
                    <p class="text-gray-600 text-sm mt-3">{{ $item['subtitle'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16" style="background: linear-gradient(135deg, #d45930 0%, #fac030 100%);">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">{{ $content['cta_title'] ?? 'Wujudkan Impian Anda' }}</h2>
        <p class="text-white/90 mb-8">
            {{ $content['cta_description'] ?? 'Dapatkan ijazah SMA dan buka pintu menuju masa depan yang lebih cerah.' }}
        </p>
        <a href="/ppdb"
            class="inline-block px-8 py-4 bg-white text-[#d45930] font-semibold rounded-full hover:bg-gray-100 transition"
            style="cursor: pointer;">
            Daftar Sekarang
        </a>
    </div>
</section>