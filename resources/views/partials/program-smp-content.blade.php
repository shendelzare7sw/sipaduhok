@php
    $content = $content ?? [];
@endphp

<!-- About Program -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span
                    class="inline-block bg-[#287f3b]/20 text-[#287f3b] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $content['badge'] ?? 'Pendidikan Kesetaraan' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                    {{ $content['title'] ?? 'Paket B (Setara SMP)' }}</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_1'] ?? 'Program Paket B adalah program pendidikan kesetaraan yang setara dengan Sekolah Menengah Pertama (SMP). Program ini memberikan kesempatan bagi mereka yang ingin melanjutkan pendidikan ke jenjang yang lebih tinggi.' }}
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_2'] ?? 'Lulusan Paket B dapat melanjutkan ke jenjang SMA/SMK atau Paket C, serta dapat digunakan untuk melamar pekerjaan yang mensyaratkan ijazah SMP.' }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <div class="bg-[#287f3b]/10 px-4 py-2 rounded-full">
                        <span
                            class="text-[#287f3b] font-medium">{{ $content['tag_1'] ?? 'Ijazah Resmi Kemendikbud' }}</span>
                    </div>
                    <div class="bg-[#165fac]/10 px-4 py-2 rounded-full">
                        <span class="text-[#165fac] font-medium">{{ $content['tag_2'] ?? 'Setara SMP Formal' }}</span>
                    </div>
                </div>
            </div>
            <div class="relative">
                <img src="{{ asset($content['image'] ?? 'img/smp-main.jpg') }}" alt="SMP"
                    class="rounded-2xl shadow-xl w-full h-[400px] object-cover"
                    onerror="this.src='https://via.placeholder.com/800x400/287f3b/ffffff?text=Paket+B+(SMP)'">
                <div
                    class="absolute -bottom-6 -left-6 bg-[#287f3b] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                    <p class="text-2xl font-bold">{{ $content['label'] ?? 'Paket B' }}</p>
                    <p class="text-sm">{{ $content['label_subtitle'] ?? 'Setara SMP' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mata Pelajaran -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $mataPelajaran['header']['title'] ?? 'Mata Pelajaran' }}</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php $mpItems = $mataPelajaran['items'] ?? []; @endphp
            @foreach($mpItems as $item)
                @php
                    $cardColor = $item['card_color'] ?? '#ffffff';
                    $iconColor = $item['icon_color'] ?? 'blue';
                    $iconClassMap = [
                        'blue' => 'text-[#165fac]',
                        'green' => 'text-[#287f3b]',
                        'orange' => 'text-[#d45930]',
                        'yellow' => 'text-[#fac030]',
                    ];
                    $textColorClass = $iconClassMap[$iconColor] ?? 'text-gray-600';
                    $borderColor = $iconClassMap[$iconColor] ?? 'border-gray-200'; // Extract color from class? Or just use card color for border?
                    // The original used border-t-4 with specific colors. Let's use card_color for background or border styling.
                    // Actually, the original design had border-t-4 border-[color].
                    // Let's use the 'card_color' field for the top border color if it's a valid hex, or map icon_color.
                    // But in seeder I populated card_color with full hex.
                @endphp
                <div class="card-hover bg-gray-50 rounded-2xl p-6 text-center border-t-4"
                     style="border-color: {{ $cardColor }}">
                    <div class="mb-4 flex justify-center text-3xl">
                         @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                            <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-10 h-10 object-contain">
                         @elseif(!empty($item['icon']) && (str_starts_with($item['icon'], 'fa') || str_starts_with($item['icon'], 'bx')))
                            <i class="{{ $item['icon'] }}" style="color: {{ $cardColor }}"></i>
                         @else
                            @php
                                $title = strtolower($item['title'] ?? '');
                            @endphp
                            @if(str_contains($title, 'indonesia') || str_contains($title, 'bahasa'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            @elseif(str_contains($title, 'matematika') || str_contains($title, 'math'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v4h10V4H7zm0 6v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2z"/>
                                </svg>
                            @elseif(str_contains($title, 'ipa') || str_contains($title, 'sains'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M9 3v6.5L5.5 15c-1.3 2-.2 4.5 2 4.5h9c2.2 0 3.3-2.5 2-4.5L15 9.5V3h1a1 1 0 100-2H8a1 1 0 100 2h1zm2 0h2v7l3.5 5.5c.4.6.1 1.5-.7 1.5h-7.6c-.8 0-1.1-.9-.7-1.5L11 10V3z"/>
                                </svg>
                            @elseif(str_contains($title, 'ips') || str_contains($title, 'sosial'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            @elseif(str_contains($title, 'agama'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 4c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm7 14v-3c0-1.1-.9-2-2-2h-2.5l-1.5-3v-1h2c.55 0 1-.45 1-1s-.45-1-1-1h-4c-.55 0-1 .45-1 1s.45 1 1 1h1v1l-1.5 3H7c-1.1 0-2 .9-2 2v3h2v-3h4v3h2v-3h2v3h2z"/>
                                </svg>
                            @elseif(str_contains($title, 'inggris') || str_contains($title, 'english'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2s.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2s.07-1.35.16-2h4.68c.09.65.16 1.32.16 2s-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2s-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"/>
                                </svg>
                            @elseif(str_contains($title, 'pkn') || str_contains($title, 'kewarganegaraan'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M14 6l-1-2H5v17h2v-7h5l1 2h7V6h-6zm4 8h-4l-1-2H7V6h5l1 2h5v6z"/>
                                </svg>
                            @elseif(str_contains($title, 'seni') || str_contains($title, 'budaya'))
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                </svg>
                            @else
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 4.5C9.24 4.5 6.5 5.5 4 7.08V19.5c2.5-1.42 5.24-2.5 8-2.5s5.5 1.08 8 2.5V7.08C17.5 5.5 14.76 4.5 12 4.5zm0 12c-2.03 0-4.04.37-5.96 1.12V8.13C8.04 7.37 10.03 7 12 7s3.96.37 5.96 1.13v9.49C16.04 16.87 14.03 16.5 12 16.5z"/>
                                </svg>
                            @endif
                         @endif
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $item['title'] }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Keunggulan -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $keunggulan['header']['title'] ?? 'Keunggulan Program' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php $kungItems = $keunggulan['items'] ?? []; @endphp
            @foreach($kungItems as $item)
                @php
                    $iconColor = $item['icon_color'] ?? 'blue';
                    $colorMap = [
                        'blue' => ['bg' => '#165fac', 'text' => '#165fac'],
                        'green' => ['bg' => '#287f3b', 'text' => '#287f3b'],
                        'orange' => ['bg' => '#d45930', 'text' => '#d45930'],
                        'yellow' => ['bg' => '#fac030', 'text' => '#fac030'],
                    ];
                    $c = $colorMap[$iconColor] ?? $colorMap['blue'];
                @endphp
                <div class="bg-white rounded-2xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6"
                         style="background-color: {{ $c['bg'] }}1a"> <!-- 10% opacity hex approximately 1a -->
                         @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                            <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-8 h-8 object-contain">
                         @elseif(!empty($item['icon']) && (str_starts_with($item['icon'], 'fa') || str_starts_with($item['icon'], 'bx')))
                            <i class="{{ $item['icon'] }}" style="color: {{ $c['text'] }}; font-size: 2rem;"></i>
                         @else
                            @php $title = strtolower($item['title'] ?? ''); @endphp
                            @if(str_contains($title, 'guru') || str_contains($title, 'tutor') || str_contains($title, 'pengajar'))
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#165fac">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            @elseif(str_contains($title, 'jadwal') || str_contains($title, 'fleksibel') || str_contains($title, 'waktu'))
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#165fac">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                            @elseif(str_contains($title, 'ijazah') || str_contains($title, 'sertifikat') || str_contains($title, 'resmi'))
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#165fac">
                                    <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/>
                                </svg>
                            @elseif(str_contains($title, 'biaya') || str_contains($title, 'terjangkau') || str_contains($title, 'murah'))
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#165fac">
                                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#165fac">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            @endif
                         @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $item['title'] }}</h3>
                    <p class="text-gray-600">{{ $item['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16" style="background: linear-gradient(135deg, #287f3b 0%, #165fac 100%);">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">{{ $content['cta_title'] ?? 'Raih Ijazah SMP Anda' }}</h2>
        <p class="text-white/90 mb-8">
            {{ $content['cta_description'] ?? 'Daftarkan diri Anda sekarang dan mulai perjalanan pendidikan baru.' }}
        </p>
        <a href="/ppdb"
            class="inline-block px-8 py-4 bg-white text-[#287f3b] font-semibold rounded-full hover:bg-gray-100 transition"
            style="cursor: pointer;">
            Daftar Sekarang
        </a>
    </div>
</section>