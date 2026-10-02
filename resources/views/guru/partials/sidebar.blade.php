{{--
    Navigasi CleanFlow untuk Guru.
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();

    $unreadCm = 0;
    try {
        $tpId = optional(\App\Models\TenagaPendidik::where('user_id', auth()->id())->first())->id;
        if ($tpId) {
            $unreadCm = \App\Models\CatatanMonitoring::forGuru($tpId)->unread()->count();
        }
    } catch (\Throwable $e) { /* table may not exist yet */ }

    $infoAkademikActive = $currentRoute == 'guru.jadwal.index' || Str::startsWith($currentRoute, 'guru.kelas');

    $baseClasses = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition';
    $toneClasses = ['!bg-white/[0.07]', '!bg-sky-300/[0.15]', '!bg-cyan-200/[0.12]', '!bg-indigo-200/[0.13]'];
    $toneIdx = 0;
    $activeState = '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15';
    $idleState = function () use (&$toneIdx, $toneClasses) {
        return '!border-0 !ring-0 '.$toneClasses[$toneIdx++ % count($toneClasses)].' text-blue-50/90 hover:!bg-white/15 hover:text-white';
    };
    $subBase = 'menu-link flex min-h-10 items-center rounded-lg border px-3 py-2 text-sm transition';
    $subActive = 'border-white/30 bg-white/20 font-semibold text-white';
    $subIdle = 'border-white/10 bg-white/[0.03] text-blue-100/80 hover:border-white/20 hover:bg-white/10 hover:text-white';
@endphp

{{-- Dashboard --}}
<li class="mb-5">
    <div class="mb-2 px-3"><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Mulai</p></div>
    <ul class="space-y-1">
        @php $active = $currentRoute == 'guru.dashboard'; @endphp
        <li class="menu-item {{ $active ? 'active' : '' }}">
            <a href="{{ route('guru.dashboard') }}" class="menu-link {{ $baseClasses }} {{ $active ? $activeState : $idleState() }}">
                <i class="fa-solid fa-house w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                <span class="min-w-0 flex-1 truncate">Dashboard</span>
            </a>
        </li>
    </ul>
</li>

{{-- UC38: Melihat Informasi Akademik --}}
<li class="mb-5">
    <div class="mb-2 px-3"><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Akademik</p></div>
    <ul class="space-y-1">
        <li class="menu-item {{ $infoAkademikActive ? 'active open' : '' }}">
            <button type="button" class="menu-link menu-toggle bg-transparent {{ $baseClasses }} {{ $infoAkademikActive ? $activeState : $idleState() }}" data-menu-toggle aria-controls="guru-info-akademik" aria-expanded="{{ $infoAkademikActive ? 'true' : 'false' }}">
                <i class="fa-solid fa-calendar-days w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                <span class="min-w-0 flex-1 truncate">Informasi Akademik</span>
                <i class="fa-solid fa-chevron-down text-[10px] transition-transform {{ $infoAkademikActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i>
            </button>
            <ul id="guru-info-akademik" class="menu-sub mt-1 space-y-1 pl-8 {{ $infoAkademikActive ? '' : 'hidden' }}">
                @foreach([
                    ['label' => 'Jadwal Mengajar', 'route' => 'guru.jadwal.index', 'on' => $currentRoute == 'guru.jadwal.index'],
                    ['label' => 'Semua Kelas', 'route' => 'guru.kelas.index', 'on' => Str::startsWith($currentRoute, 'guru.kelas')],
                ] as $sub)
                    <li class="menu-item {{ $sub['on'] ? 'active' : '' }}">
                        <a href="{{ route($sub['route']) }}" class="{{ $subBase }} {{ $sub['on'] ? $subActive : $subIdle }}">{{ $sub['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>
</li>

{{-- UC27: Kelola Materi Pembelajaran (Arsip) dan UC37: Catatan Monitoring --}}
<li class="mb-5">
    <div class="mb-2 px-3"><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Pembelajaran</p></div>
    <ul class="space-y-1">
        @php $active = Str::startsWith($currentRoute, 'guru.lms.arsip'); @endphp
        <li class="menu-item {{ $active ? 'active' : '' }}">
            <a href="{{ route('guru.lms.arsip.index') }}" class="menu-link {{ $baseClasses }} {{ $active ? $activeState : $idleState() }}">
                <i class="fa-solid fa-box-archive w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                <span class="min-w-0 flex-1 truncate">Arsip LMS</span>
            </a>
        </li>
        @php $active = Str::startsWith($currentRoute, 'guru.lms.catatan-monitoring'); @endphp
        <li class="menu-item {{ $active ? 'active' : '' }}">
            <a href="{{ route('guru.lms.catatan-monitoring.index') }}" class="menu-link {{ $baseClasses }} {{ $active ? $activeState : $idleState() }}">
                <i class="fa-solid fa-comment-dots w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                <span class="min-w-0 flex-1 truncate">Catatan Monitoring</span>
                @if($unreadCm > 0)
                    <span class="rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-extrabold text-white">{{ $unreadCm > 99 ? '99+' : $unreadCm }}</span>
                @endif
            </a>
        </li>
    </ul>
</li>

{{-- Kelas Saya (Akses Cepat ke LMS) --}}
@if(isset($sidebarKelas) && count($sidebarKelas) > 0)
    <li class="mb-5">
        <div class="mb-2 px-3"><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Kelas Saya (Akses Cepat)</p></div>
        <ul class="space-y-1">
            @foreach($sidebarKelas as $kelasId => $items)
                @php
                    $kelas = $items->first()->kelas;
                    $isActive = Str::startsWith($currentRoute, 'guru.lms') && request()->route('kelas') == $kelasId;
                    $targetId = 'guru-kelas-'.$kelasId;
                @endphp
                <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                    <button type="button" class="menu-link menu-toggle bg-transparent {{ $baseClasses }} {{ $isActive ? $activeState : $idleState() }}" data-menu-toggle aria-controls="{{ $targetId }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                        <i class="fa-solid fa-chalkboard w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                        <span class="min-w-0 flex-1 truncate">{{ $kelas->nama_kelas }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform {{ $isActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i>
                    </button>
                    <ul id="{{ $targetId }}" class="menu-sub mt-1 space-y-1 pl-8 {{ $isActive ? '' : 'hidden' }}">
                        @foreach($items as $item)
                            @php
                                $mapel = $item->mataPelajaran;
                                $mapelId = $item->mata_pelajaran_id;
                                $isSubActive = $isActive && request()->route('mapel') == $mapelId;
                            @endphp
                            @if($mapel)
                                <li class="menu-item {{ $isSubActive ? 'active' : '' }}">
                                    <a href="{{ route('guru.lms.dashboard', [$kelasId, $mapelId]) }}" class="{{ $subBase }} {{ $isSubActive ? $subActive : $subIdle }}">
                                        <span class="min-w-0 break-words leading-snug">{{ $mapel->nama_mapel }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </li>
@endif
