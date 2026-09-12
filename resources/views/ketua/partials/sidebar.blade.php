@php
    $pendingDispensasi = \App\Models\PengajuanRaporKetua::where('status', 'menunggu')->count();
    $sections = [
        [
            'label' => 'Ringkasan',
            'items' => [
                ['label' => 'Dashboard', 'description' => 'Kondisi PKBM hari ini', 'icon' => 'fa-home', 'route' => 'ketua.dashboard', 'patterns' => ['ketua.dashboard']],
            ],
        ],
        [
            'label' => 'Persetujuan & validasi',
            'hint' => 'Tinjau keputusan secara berurutan.',
            'items' => [
                ['label' => 'Dispensasi kenaikan', 'description' => 'Izin naik kelas khusus', 'icon' => 'fa-check-double', 'route' => 'ketua.kenaikan-kelas.approval.index', 'patterns' => ['ketua.kenaikan-kelas.approval.*']],
                ['label' => 'Validasi rapor', 'description' => 'Review rapor dari wali kelas', 'icon' => 'fa-certificate', 'route' => 'ketua.validasi-rapor.index', 'patterns' => ['ketua.validasi-rapor.*']],
                ['label' => 'Dispensasi keuangan', 'description' => 'Akses ujian dan rapor', 'icon' => 'fa-hand-holding-heart', 'route' => 'ketua.dispensasi.index', 'patterns' => ['ketua.dispensasi.*'], 'count' => $pendingDispensasi],
            ],
        ],
        [
            'label' => 'Monitoring',
            'items' => [[
                'label' => 'Monitoring sistem', 'description' => 'Pengguna, kelas, dan LMS', 'icon' => 'fa-chart-line', 'patterns' => ['ketua.monitoring.*'],
                'children' => [
                    ['label' => 'Data pengguna', 'route' => 'ketua.monitoring.pengguna', 'patterns' => ['ketua.monitoring.pengguna']],
                    ['label' => 'Wali kelas', 'route' => 'ketua.monitoring.wali-kelas', 'patterns' => ['ketua.monitoring.wali-kelas']],
                    ['label' => 'Guru pengajar', 'route' => 'ketua.monitoring.guru-pengajar', 'patterns' => ['ketua.monitoring.guru-pengajar']],
                    ['label' => 'Aktivitas siswa', 'route' => 'ketua.monitoring.siswa', 'patterns' => ['ketua.monitoring.siswa']],
                    ['label' => 'Konten LMS', 'route' => 'ketua.monitoring.lms.index', 'patterns' => ['ketua.monitoring.lms.*']],
                ],
            ]],
        ],
        [
            'label' => 'Laporan & komunikasi',
            'items' => [[
                'label' => 'Laporan & catatan', 'description' => 'Cetak dan tindak lanjut', 'icon' => 'fa-file-lines', 'patterns' => ['ketua.laporan.*', 'ketua.catatan.*'],
                'children' => [
                    ['label' => 'Cetak laporan', 'route' => 'ketua.laporan.index', 'patterns' => ['ketua.laporan.*']],
                    ['label' => 'Kirim catatan', 'route' => 'ketua.catatan.index', 'patterns' => ['ketua.catatan.*']],
                ],
            ]],
        ],
    ];
@endphp

@foreach($sections as $section)
    <li class="mb-5">
        <div class="menu-header mb-2 px-3"><p class="menu-header-text text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>@isset($section['hint'])<p class="mt-1 text-[10px] leading-4 text-sky-100/75">{{ $section['hint'] }}</p>@endisset</div>
        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php
                    $isActive = request()->routeIs(...($item['patterns'] ?? []));
                    $hasChildren = !empty($item['children']);
                    $targetId = 'ketua-menu-'.$loop->parent->index.'-'.$loop->index;
                    $base = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition';
                    $toneClasses = ['!bg-white/[0.07]', '!bg-sky-300/[0.15]', '!bg-cyan-200/[0.12]', '!bg-indigo-200/[0.13]'];
                    $toneClass = $toneClasses[($loop->parent->index + $loop->index) % count($toneClasses)];
                    $state = $isActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                @endphp
                <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                    @if($hasChildren)
                        <button type="button" class="menu-link menu-toggle bg-transparent {{ $base }} {{ $state }}" data-menu-toggle aria-controls="{{ $targetId }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}"><i class="fas {{ $item['icon'] }} w-5 text-center text-sm" aria-hidden="true"></i><span class="min-w-0 flex-1"><span class="block truncate">{{ $item['label'] }}</span><span class="mt-0.5 block truncate text-[10px] font-medium {{ $isActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span></span><i class="fas fa-chevron-down text-[10px] transition-transform {{ $isActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i></button>
                        <ul id="{{ $targetId }}" class="menu-sub mt-1 space-y-1 pl-8 {{ $isActive ? '' : 'hidden' }}">@foreach($item['children'] as $child)@php $childActive=request()->routeIs(...$child['patterns']); @endphp<li class="menu-item {{ $childActive?'active':'' }}"><a href="{{ route($child['route']) }}" class="menu-link flex min-h-10 items-center rounded-lg border px-3 py-2 text-sm transition {{ $childActive?'border-white/30 bg-white/20 font-semibold text-white':'border-white/10 bg-white/[0.03] text-blue-100/80 hover:border-white/20 hover:bg-white/10 hover:text-white' }}">{{ $child['label'] }}</a></li>@endforeach</ul>
                    @else
                        <a href="{{ route($item['route']) }}" class="menu-link {{ $base }} {{ $state }}"><i class="fas {{ $item['icon'] }} w-5 text-center text-sm" aria-hidden="true"></i><span class="min-w-0 flex-1"><span class="block truncate">{{ $item['label'] }}</span><span class="mt-0.5 block truncate text-[10px] font-medium {{ $isActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span></span>@if(($item['count'] ?? 0)>0)<span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-extrabold text-white">{{ $item['count'] }}</span>@endif</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
