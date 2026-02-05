{{--
    Sidebar Menu untuk Guru Dashboard (Sneat Template)
    File: resources/views/guru/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'guru.dashboard' ? 'active' : '' }}">
    <a href="{{ route('guru.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Jadwal Mengajar -->
<li class="menu-item {{ $currentRoute == 'guru.jadwal.index' ? 'active' : '' }}">
    <a href="{{ route('guru.jadwal.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Jadwal Mengajar</div>
    </a>
</li>

<!-- Pembelajaran Section -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Pembelajaran</span>
</li>

<!-- Semua Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'guru.kelas') ? 'active' : '' }}">
    <a href="{{ route('guru.kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-list"></i>
        <div>Semua Kelas</div>
    </a>
</li>

<!-- Kelas Saya (Dynamic) -->
@if(isset($sidebarKelas) && count($sidebarKelas) > 0)
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Kelas Saya (Akses Cepat)</span>
    </li>
    
    @foreach($sidebarKelas as $kelasId => $items)
        @php
            $kelas = $items->first()->kelas;
            $isActive = Str::startsWith($currentRoute, 'guru.lms') && request()->route('kelas') == $kelasId;
        @endphp
        <li class="menu-item {{ $isActive ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon fas fa-chalkboard"></i>
                <div>{{ $kelas->nama_kelas }}</div>
            </a>
            <ul class="menu-sub">
                @foreach($items as $item)
                    @php
                        // Check if mapel relation exists
                        $mapel = $item->mataPelajaran;
                        $mapelId = $item->mata_pelajaran_id;
                        $isSubActive = $isActive && request()->route('mapel') == $mapelId;
                    @endphp
                    @if($mapel)
                        <li class="menu-item {{ $isSubActive ? 'active' : '' }}">
                            <a href="{{ route('guru.lms.dashboard', [$kelasId, $mapelId]) }}" class="menu-link">
                                <div class="text-wrap" style="line-height: 1.2;">{{ $mapel->nama_mapel }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
    @endforeach
@endif
