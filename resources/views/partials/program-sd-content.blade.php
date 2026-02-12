@php
    $content = $content ?? [];
    $features = isset($content['features']) ? explode('|', $content['features']) : ['Ijazah Resmi', 'Jadwal Fleksibel', 'Kelas Kecil', 'Bimbingan Intensif'];
@endphp

<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <img src="{{ asset($content['image'] ?? 'img/sd-main.jpg') }}" alt="SD"
                    class="rounded-2xl shadow-xl w-full h-[400px] object-cover"
                    onerror="this.src='https://via.placeholder.com/800x400/165fac/ffffff?text=Paket+A+(SD)'">

                <div
                    class="absolute -bottom-6 -right-6 bg-[#165fac] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                    <p class="text-2xl font-bold">{{ $content['label'] ?? 'Paket A' }}</p>
                    <p class="text-sm">{{ $content['label_subtitle'] ?? 'Setara SD' }}</p>
                </div>
            </div>
            <!-- Rest of content -->
            <div>
                <span
                    class="inline-block bg-[#165fac]/20 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $content['badge'] ?? 'Pendidikan Kesetaraan' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                    {{ $content['title'] ?? 'Paket A (Setara SD)' }}</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_1'] ?? 'Program Paket A adalah program pendidikan kesetaraan yang setara dengan Sekolah Dasar (SD). Program ini diperuntukkan bagi anak-anak yang tidak dapat mengikuti pendidikan formal karena berbagai alasan.' }}
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_2'] ?? 'Dengan kurikulum yang disesuaikan dan metode pembelajaran yang fleksibel, siswa dapat belajar sesuai dengan kecepatan masing-masing sambil tetap mencapai kompetensi yang diharapkan.' }}
                </p>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($features as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mata Pelajaran -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span
                class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $mataPelajaran['header']['badge'] ?? 'Kurikulum' }}</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $mataPelajaran['header']['title'] ?? 'Mata Pelajaran' }}</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 p-4">
            @php $items = $mataPelajaran['items'] ?? []; @endphp
            @foreach($items as $item)
                @php
                    $cardColor = $item['card_color'] ?? '#ffffff';
                    // Determine text color based on card color (simple heuristic or passed from DB if needed)
                    // For now assuming colors are light, so text is dark/colored.
                    // We can also use icon_color for the icon text class if it's a class string.
                    $iconColor = $item['icon_color'] ?? 'blue';
                    $iconClassMap = [
                        'orange' => 'text-orange-600',
                        'blue' => 'text-blue-600',
                        'green' => 'text-emerald-600',
                        'yellow' => 'text-amber-600',
                        'red' => 'text-rose-600',
                    ];
                    $textColorClass = $iconClassMap[$iconColor] ?? 'text-gray-600';
                @endphp
                <div
                    class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100"
                    style="background-color: {{ $cardColor }}; border-color: {{ $cardColor }}">
                    <div
                        class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center mb-4" >
                         @if(!empty($item['icon']) && str_contains($item['icon'], '/'))
                            <img src="{{ asset($item['icon']) }}" alt="{{ $item['title'] }}" class="w-10 h-10 object-contain">
                         @elseif(!empty($item['icon']) && (str_starts_with($item['icon'], 'fa') || str_starts_with($item['icon'], 'bx')))
                            <i class="{{ $item['icon'] }} text-2xl {{ $textColorClass }}"></i>
                         @else
                            @php
                                $title = strtolower($item['title'] ?? '');
                            @endphp
                            @if(str_contains($title, 'indonesia') || str_contains($title, 'bahasa'))
                                {{-- Book/Language Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            @elseif(str_contains($title, 'matematika') || str_contains($title, 'math'))
                                {{-- Calculator/Math Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm0 2v4h10V4H7zm0 6v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2z"/>
                                </svg>
                            @elseif(str_contains($title, 'ipa') || str_contains($title, 'sains') || str_contains($title, 'science'))
                                {{-- Science/Flask Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M9 3v6.5L5.5 15c-1.3 2-.2 4.5 2 4.5h9c2.2 0 3.3-2.5 2-4.5L15 9.5V3h1a1 1 0 100-2H8a1 1 0 100 2h1zm2 0h2v7l3.5 5.5c.4.6.1 1.5-.7 1.5h-7.6c-.8 0-1.1-.9-.7-1.5L11 10V3z"/>
                                </svg>
                            @elseif(str_contains($title, 'ips') || str_contains($title, 'sosial') || str_contains($title, 'social'))
                                {{-- Globe/Social Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            @elseif(str_contains($title, 'agama') || str_contains($title, 'religion'))
                                {{-- Praying Hands Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 4c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm7 14v-3c0-1.1-.9-2-2-2h-2.5l-1.5-3v-1h2c.55 0 1-.45 1-1s-.45-1-1-1h-4c-.55 0-1 .45-1 1s.45 1 1 1h1v1l-1.5 3H7c-1.1 0-2 .9-2 2v3h2v-3h4v3h2v-3h2v3h2z"/>
                                </svg>
                            @elseif(str_contains($title, 'inggris') || str_contains($title, 'english'))
                                {{-- Globe/Language Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2s.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2s.07-1.35.16-2h4.68c.09.65.16 1.32.16 2s-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2s-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"/>
                                </svg>
                            @else
                                {{-- Default Book Icon --}}
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="#374151">
                                    <path d="M12 4.5C9.24 4.5 6.5 5.5 4 7.08V19.5c2.5-1.42 5.24-2.5 8-2.5s5.5 1.08 8 2.5V7.08C17.5 5.5 14.76 4.5 12 4.5zm0 12c-2.03 0-4.04.37-5.96 1.12V8.13C8.04 7.37 10.03 7 12 7s3.96.37 5.96 1.13v9.49C16.04 16.87 14.03 16.5 12 16.5z"/>
                                </svg>
                            @endif
                         @endif
                    </div>
                    <h3 class="font-bold text-gray-800 text-center">{{ $item['title'] }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Syarat -->
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Syarat Pendaftaran</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <ul class="space-y-4">
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        1</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Usia Minimal 7 Tahun</h4>
                        <p class="text-gray-600 text-sm">Calon siswa berusia minimal 7 tahun pada saat mendaftar</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        2</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Fotokopi Akta Kelahiran</h4>
                        <p class="text-gray-600 text-sm">Menyerahkan fotokopi akta kelahiran yang dilegalisir</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        3</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Fotokopi Kartu Keluarga</h4>
                        <p class="text-gray-600 text-sm">Menyerahkan fotokopi KK yang masih berlaku</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        4</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Pas Foto 3x4</h4>
                        <p class="text-gray-600 text-sm">Menyerahkan pas foto berwarna ukuran 3x4 sebanyak 4 lembar</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">{{ $content['cta_title'] ?? 'Mulai Pendidikan Anda Sekarang' }}
        </h2>
        <p class="text-white/90 mb-8">
            {{ $content['cta_description'] ?? 'Dapatkan ijazah resmi setara SD dengan program Paket A kami.' }}</p>
        <a href="/ppdb"
            class="inline-block px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition"
            style="cursor: pointer;">
            Daftar Sekarang
        </a>
    </div>
</section>