@php
    $navigation = [
        ['label' => 'Utama', 'items' => [
            ['label' => 'Dashboard', 'route' => 'waka.dashboard', 'patterns' => ['waka.dashboard'], 'icon' => 'fa-home'],
        ]],
        ['label' => 'Manajemen Akademik', 'items' => [
            ['label' => 'Tahun Ajaran', 'route' => 'waka.tahun-ajaran.index', 'patterns' => ['waka.tahun-ajaran.*'], 'icon' => 'fa-calendar-alt'],
            [
                'label' => 'Data Kelas & Penugasan',
                'patterns' => ['waka.kelas.*', 'waka.wali-kelas.*', 'waka.guru-pengajar.*', 'waka.manajemen-siswa.*'],
                'icon' => 'fa-chalkboard-teacher',
                'children' => [
                    ['label' => 'Data Kelas', 'route' => 'waka.kelas.index', 'patterns' => ['waka.kelas.*']],
                    ['label' => 'Data Wali Kelas', 'route' => 'waka.wali-kelas.index', 'patterns' => ['waka.wali-kelas.*']],
                    ['label' => 'Data Guru Pengajar', 'route' => 'waka.guru-pengajar.index', 'patterns' => ['waka.guru-pengajar.*']],
                    ['label' => 'Manajemen Siswa', 'route' => 'waka.manajemen-siswa.index', 'patterns' => ['waka.manajemen-siswa.*']],
                ],
            ],
            ['label' => 'Mata Pelajaran', 'route' => 'waka.mata-pelajaran.index', 'patterns' => ['waka.mata-pelajaran.*'], 'icon' => 'fa-book'],
            ['label' => 'Jadwal Pelajaran', 'route' => 'waka.jadwal-pelajaran.index', 'patterns' => ['waka.jadwal-pelajaran.*'], 'icon' => 'fa-calendar-week'],
        ]],
        ['label' => 'Kenaikan Kelas', 'items' => [
            [
                'label' => 'Pengaturan Kenaikan',
                'patterns' => ['waka.kenaikan-kelas.kkm.*', 'waka.kenaikan-kelas.settings.*'],
                'icon' => 'fa-cogs',
                'children' => [
                    ['label' => 'Pengaturan KKM', 'route' => 'waka.kenaikan-kelas.kkm.index', 'patterns' => ['waka.kenaikan-kelas.kkm.*']],
                    ['label' => 'Pengaturan Kenaikan', 'route' => 'waka.kenaikan-kelas.settings.index', 'patterns' => ['waka.kenaikan-kelas.settings.*']],
                ],
            ],
            ['label' => 'Proses & Rekap', 'route' => 'waka.kenaikan-kelas.report', 'patterns' => ['waka.kenaikan-kelas.report'], 'icon' => 'fa-tasks'],
        ]],
        ['label' => 'Monitoring & Analitik', 'items' => [
            [
                'label' => 'Monitoring Sistem',
                'patterns' => ['waka.monitoring.*'],
                'icon' => 'fa-chart-bar',
                'children' => [
                    ['label' => 'Wali Kelas', 'route' => 'waka.monitoring.wali-kelas', 'patterns' => ['waka.monitoring.wali-kelas']],
                    ['label' => 'Guru Pengajar', 'route' => 'waka.monitoring.guru-pengajar', 'patterns' => ['waka.monitoring.guru-pengajar']],
                    ['label' => 'Siswa', 'route' => 'waka.monitoring.siswa', 'patterns' => ['waka.monitoring.siswa']],
                    ['label' => 'Monitoring LMS', 'route' => 'waka.monitoring.lms.index', 'patterns' => ['waka.monitoring.lms.*']],
                ],
            ],
        ]],
        ['label' => 'Komunikasi', 'items' => [
            ['label' => 'Catatan', 'route' => 'waka.catatan.index', 'patterns' => ['waka.catatan.*'], 'icon' => 'fa-sticky-note'],
        ]],
    ];
@endphp

@foreach($navigation as $section)
    <li class="mb-5">
        <div class="menu-header mb-2 px-3">
            <p class="menu-header-text text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>
        </div>
        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php
                    $isActive = request()->routeIs(...($item['patterns'] ?? []));
                    $hasChildren = ! empty($item['children']);
                    $targetId = 'waka-menu-'.$loop->parent->index.'-'.$loop->index;
                    $toneClasses = ['!bg-white/[0.07]', '!bg-sky-300/[0.15]', '!bg-cyan-200/[0.12]', '!bg-indigo-200/[0.13]'];
                    $toneClass = $toneClasses[($loop->parent->index + $loop->index) % count($toneClasses)];
                    $stateClasses = $isActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                @endphp
                <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                    @if($hasChildren)
                        <button type="button" class="menu-link menu-toggle group flex min-h-11 w-full items-center gap-3 rounded-xl bg-transparent px-3 py-2.5 text-left text-sm font-semibold transition {{ $stateClasses }}" data-menu-toggle aria-controls="{{ $targetId }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform {{ $isActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i>
                        </button>
                        <ul id="{{ $targetId }}" class="menu-sub mt-1 space-y-1 pl-8 {{ $isActive ? '' : 'hidden' }}">
                            @foreach($item['children'] as $child)
                                @php($childActive = request()->routeIs(...($child['patterns'] ?? [])))
                                <li class="menu-item {{ $childActive ? 'active' : '' }}">
                                    <a href="{{ route($child['route']) }}" class="menu-link flex min-h-10 items-center rounded-lg border px-3 py-2 text-sm transition {{ $childActive ? 'border-white/30 bg-white/20 font-semibold text-white' : 'border-white/10 bg-white/[0.03] text-blue-100/80 hover:border-white/20 hover:bg-white/10 hover:text-white' }}">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ route($item['route']) }}" class="menu-link group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition {{ $stateClasses }}">
                            <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
