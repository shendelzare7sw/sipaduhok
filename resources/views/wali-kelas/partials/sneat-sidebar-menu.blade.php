{{--
    Sidebar Menu untuk Wali Kelas Dashboard
    File: resources/views/wali-kelas/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Template (Bootstrap 5)
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'wali.dashboard' ? 'active' : '' }}">
    <a href="{{ route('wali.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Akademik</span>
</li>

<!-- Jadwal Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.jadwal') ? 'active' : '' }}">
    <a href="{{ route('wali.jadwal.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- Presensi Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.presensi') ? 'active' : '' }}">
    <a href="{{ route('wali.presensi.index') }}" class="menu-link">
        <i class="menu-icon fas fa-clipboard-check"></i>
        <div>Presensi Siswa</div>
    </a>
</li>

<!-- Nilai Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.nilai') ? 'active' : '' }}">
    <a href="{{ route('wali.nilai.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Nilai Siswa</div>
    </a>
</li>

<!-- Kelola Rapor -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.rapor') ? 'active' : '' }}">
    <a href="{{ route('wali.rapor.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Kelola Rapor</div>
    </a>
</li>

<!-- Menu Header - Validasi -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi</span>
</li>

<!-- Validasi Akses -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('wali.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-double"></i>
        <div>Validasi Akses</div>
    </a>
</li>
