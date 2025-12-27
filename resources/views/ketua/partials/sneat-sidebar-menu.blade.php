{{--
    Sidebar Menu untuk Ketua PKBM - Sneat Version
    File: resources/views/ketua/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'ketua.dashboard' ? 'active' : '' }}">
    <a href="{{ route('ketua.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Monitoring -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring</span>
</li>

<!-- Data Pengguna -->
<li class="menu-item {{ $currentRoute == 'ketua.monitoring.pengguna' ? 'active' : '' }}">
    <a href="{{ route('ketua.monitoring.pengguna') }}" class="menu-link">
        <i class="menu-icon fas fa-users"></i>
        <div>Data Pengguna</div>
    </a>
</li>

<!-- Data Wali Kelas -->
<li class="menu-item {{ $currentRoute == 'ketua.monitoring.wali-kelas' ? 'active' : '' }}">
    <a href="{{ route('ketua.monitoring.wali-kelas') }}" class="menu-link">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Data Wali Kelas</div>
    </a>
</li>

<!-- Data Guru Pengajar -->
<li class="menu-item {{ $currentRoute == 'ketua.monitoring.guru-pengajar' ? 'active' : '' }}">
    <a href="{{ route('ketua.monitoring.guru-pengajar') }}" class="menu-link">
        <i class="menu-icon fas fa-user-tie"></i>
        <div>Data Guru Pengajar</div>
    </a>
</li>

<!-- Data Siswa -->
<li class="menu-item {{ $currentRoute == 'ketua.monitoring.siswa' ? 'active' : '' }}">
    <a href="{{ route('ketua.monitoring.siswa') }}" class="menu-link">
        <i class="menu-icon fas fa-user-graduate"></i>
        <div>Data Siswa</div>
    </a>
</li>

<!-- Menu Header - Laporan & Catatan -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Laporan & Catatan</span>
</li>

<!-- Laporan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.laporan') ? 'active' : '' }}">
    <a href="{{ route('ketua.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-pdf"></i>
        <div>Cetak Laporan</div>
    </a>
</li>

<!-- Kirim Catatan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.catatan') ? 'active' : '' }}">
    <a href="{{ route('ketua.catatan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-sticky-note"></i>
        <div>Kirim Catatan</div>
    </a>
</li>
