@php
    $sections = [
        [
            'label' => 'Mulai',
            'items' => [
                ['label' => 'Dashboard', 'description' => 'Ringkasan keuangan hari ini', 'icon' => 'fa-house', 'route' => 'bendahara.dashboard', 'patterns' => ['bendahara.dashboard']],
            ],
        ],
        [
            'label' => 'Keuangan',
            'hint' => 'Kelola kewajiban dan transaksi siswa.',
            'items' => [[
                'label' => 'Tagihan & pembayaran', 'description' => 'Tagihan, tunggakan, dan validasi', 'icon' => 'fa-file-invoice-dollar', 'patterns' => ['bendahara.tagihan.*', 'bendahara.pembayaran.*'],
                'children' => [
                    ['label' => 'Kelola tagihan', 'route' => 'bendahara.tagihan.index', 'patterns' => ['bendahara.tagihan.*'], 'exclude' => ['bendahara.tagihan.carryover*']],
                    ['label' => 'Tarik tunggakan', 'route' => 'bendahara.tagihan.carryover', 'patterns' => ['bendahara.tagihan.carryover*']],
                    ['label' => 'Kelola pembayaran', 'route' => 'bendahara.pembayaran.index', 'patterns' => ['bendahara.pembayaran.*']],
                ],
            ]],
        ],
        [
            'label' => 'Validasi & dispensasi',
            'items' => [
                ['label' => 'Validasi ujian & rapor', 'description' => 'Periksa kelayakan akses siswa', 'icon' => 'fa-circle-check', 'route' => 'bendahara.validasi-akses.index', 'patterns' => ['bendahara.validasi-akses.*']],
                ['label' => 'Validasi dispensasi', 'description' => 'Keputusan kenaikan kelas', 'icon' => 'fa-hand-holding-dollar', 'route' => 'bendahara.kenaikan-kelas.validation.index', 'patterns' => ['bendahara.kenaikan-kelas.validation.*']],
            ],
        ],
        [
            'label' => 'Laporan',
            'items' => [[
                'label' => 'Laporan keuangan', 'description' => 'Rekap pembayaran dan tunggakan', 'icon' => 'fa-chart-column', 'patterns' => ['bendahara.laporan.*'],
                'children' => [
                    ['label' => 'Laporan pembayaran', 'route' => 'bendahara.laporan.index', 'patterns' => ['bendahara.laporan.index', 'bendahara.laporan.cetak']],
                    ['label' => 'Rekap tagihan', 'route' => 'bendahara.laporan.rekap-tagihan', 'patterns' => ['bendahara.laporan.rekap-tagihan', 'bendahara.laporan.cetak-rekap-tagihan']],
                    ['label' => 'Siswa belum lunas', 'route' => 'bendahara.laporan.belum-lunas', 'patterns' => ['bendahara.laporan.belum-lunas', 'bendahara.laporan.cetak-belum-lunas']],
                ],
            ]],
        ],
    ];
@endphp

@foreach($sections as $section)
    <li class="mb-5">
        <div class="mb-2 px-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>
            @isset($section['hint'])
                <p class="mt-1 text-[10px] leading-4 text-sky-100/75">{{ $section['hint'] }}</p>
            @endisset
        </div>

        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php
                    $isActive = request()->routeIs(...($item['patterns'] ?? []));
                    $hasChildren = ! empty($item['children']);
                    $targetId = 'bendahara-menu-'.$loop->parent->index.'-'.$loop->index;
                    $baseClasses = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition';
                    $toneClasses = ['!bg-white/[0.07]', '!bg-sky-300/[0.15]', '!bg-cyan-200/[0.12]', '!bg-indigo-200/[0.13]'];
                    $toneClass = $toneClasses[($loop->parent->index + $loop->index) % count($toneClasses)];
                    $stateClasses = $isActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                @endphp

                <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                    @if($hasChildren)
                        <button type="button" class="menu-link menu-toggle bg-transparent {{ $baseClasses }} {{ $stateClasses }}" data-menu-toggle aria-controls="{{ $targetId }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate">{{ $item['label'] }}</span>
                                <span class="mt-0.5 block truncate text-[10px] font-medium {{ $isActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform {{ $isActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i>
                        </button>

                        <ul id="{{ $targetId }}" class="menu-sub mt-1 space-y-1 pl-8 {{ $isActive ? '' : 'hidden' }}">
                            @foreach($item['children'] as $child)
                                @php
                                    $childActive = request()->routeIs(...($child['patterns'] ?? []))
                                        && (empty($child['exclude']) || ! request()->routeIs(...$child['exclude']));
                                @endphp
                                <li class="menu-item {{ $childActive ? 'active' : '' }}">
                                    <a href="{{ route($child['route']) }}" class="menu-link flex min-h-10 items-center rounded-lg border px-3 py-2 text-sm transition {{ $childActive ? 'border-white/30 bg-white/20 font-semibold text-white' : 'border-white/10 bg-white/[0.03] text-blue-100/80 hover:border-white/20 hover:bg-white/10 hover:text-white' }}">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ route($item['route']) }}" class="menu-link {{ $baseClasses }} {{ $stateClasses }}">
                            <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate">{{ $item['label'] }}</span>
                                <span class="mt-0.5 block truncate text-[10px] font-medium {{ $isActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span>
                            </span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
