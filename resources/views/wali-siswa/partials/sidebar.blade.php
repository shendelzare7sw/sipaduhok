{{--
    Navigasi CleanFlow untuk Wali Siswa.
--}}

@php
    $currentRoute = Route::currentRouteName();
    $user = Auth::user();
    $children = $user->children()->with(['kelas', 'cabang'])->get();

    /* ── Static sections ── */
    $staticSections = [
        [
            'label' => 'Mulai',
            'items' => [
                ['label' => 'Dashboard', 'description' => 'Ringkasan anak & keuangan', 'icon' => 'fa-house', 'route' => 'wali-siswa.dashboard', 'patterns' => ['wali-siswa.dashboard']],
            ],
        ],
    ];

    $baseClasses   = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition';
    $toneClasses   = ['!bg-white/[0.07]', '!bg-sky-300/[0.15]', '!bg-cyan-200/[0.12]', '!bg-indigo-200/[0.13]'];
    $toneIdx       = 0;
@endphp

{{-- Static sections --}}
@foreach($staticSections as $section)
    <li class="mb-5">
        <div class="mb-2 px-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>
        </div>

        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php
                    $isActive    = request()->routeIs(...($item['patterns'] ?? []));
                    $toneClass   = $toneClasses[$toneIdx % count($toneClasses)];
                    $toneIdx++;
                    $stateClasses = $isActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                @endphp
                <li class="menu-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route($item['route']) }}" class="menu-link {{ $baseClasses }} {{ $stateClasses }}">
                        <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate">{{ $item['label'] }}</span>
                            <span class="mt-0.5 block truncate text-[10px] font-medium {{ $isActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach

{{-- Per-child monitoring sections --}}
@if($children->isNotEmpty())
    <li class="mb-5">
        <div class="mb-2 px-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Monitoring Anak</p>
        </div>

        <ul class="space-y-1">
            @foreach($children as $child)
                @php
                    $isChildActive = (
                        (Str::startsWith($currentRoute, 'wali-siswa.tagihan') && request()->route('siswa') == $child->id) ||
                        (Str::startsWith($currentRoute, 'wali-siswa.rapor') && request()->route('siswa') == $child->id) ||
                        (Str::startsWith($currentRoute, 'wali-siswa.presensi') && request()->route('siswa') == $child->id) ||
                        (Str::startsWith($currentRoute, 'wali-siswa.pembayaran') && false)
                    );
                    $toneClass   = $toneClasses[$toneIdx % count($toneClasses)];
                    $toneIdx++;
                    $stateClasses = $isChildActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                    $targetId = 'wali-siswa-child-' . $child->id;
                @endphp
                <li class="menu-item {{ $isChildActive ? 'active open' : '' }}">
                    <button type="button" class="menu-link menu-toggle bg-transparent {{ $baseClasses }} {{ $stateClasses }}" data-menu-toggle aria-controls="{{ $targetId }}" aria-expanded="{{ $isChildActive ? 'true' : 'false' }}">
                        <i class="fa-solid fa-user-graduate w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate">{{ Str::limit($child->nama_lengkap, 20) }}</span>
                            <span class="mt-0.5 block truncate text-[10px] font-medium {{ $isChildActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $child->kelas->nama_kelas ?? '-' }} · {{ $child->cabang->nama_cabang ?? '-' }}</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform {{ $isChildActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i>
                    </button>

                    <ul id="{{ $targetId }}" class="menu-sub mt-1 space-y-1 pl-8 {{ $isChildActive ? '' : 'hidden' }}">
                        @php
                            $childMenus = [];
                            if ((bool) $child->pivot->can_access_academic) {
                                $childMenus[] = ['label' => 'Presensi', 'route' => 'wali-siswa.presensi.anak', 'patterns' => ['wali-siswa.presensi.*']];
                                $childMenus[] = ['label' => 'Rapor', 'route' => 'wali-siswa.rapor.anak', 'patterns' => ['wali-siswa.rapor.*']];
                            }
                            if ((bool) $child->pivot->is_financial_responsible) {
                                $childMenus[] = ['label' => 'Tagihan', 'route' => 'wali-siswa.tagihan.anak', 'patterns' => ['wali-siswa.tagihan.*']];
                            }
                        @endphp
                        @foreach($childMenus as $childMenu)
                            @php
                                $childActive = request()->routeIs(...$childMenu['patterns']) && request()->route('siswa') == $child->id;
                            @endphp
                            <li class="menu-item {{ $childActive ? 'active' : '' }}">
                                <a href="{{ route($childMenu['route'], $child->id) }}" class="menu-link flex min-h-10 items-center rounded-lg border px-3 py-2 text-sm transition {{ $childActive ? 'border-white/30 bg-white/20 font-semibold text-white' : 'border-white/10 bg-white/[0.03] text-blue-100/80 hover:border-white/20 hover:bg-white/10 hover:text-white' }}">
                                    {{ $childMenu['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </li>
@else
    <li class="mb-5">
        <div class="mb-2 px-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Monitoring Anak</p>
        </div>
        <ul class="space-y-1">
            <li>
                <span class="flex min-h-11 w-full items-center gap-3 rounded-xl !border-0 !ring-0 !bg-white/[0.05] px-3 py-2.5 text-sm text-blue-100/60 cursor-default">
                    <i class="fa-solid fa-circle-info w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate">Belum Ada Data Anak</span>
                    </span>
                </span>
            </li>
        </ul>
    </li>
@endif
