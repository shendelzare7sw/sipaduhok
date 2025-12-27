{{-- 
    Sidebar Menu untuk Wali Kelas Dashboard
    File: resources/views/wali/partials/sidebar-menu.blade.php
    
    Compatible dengan SB Admin 2 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Nav Item - Dashboard -->
<li class="nav-item {{ $currentRoute == 'wali.dashboard' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wali.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Akademik
</div>

<!-- Nav Item - Jadwal Pelajaran -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'wali.jadwal') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wali.jadwal.index') }}">
        <i class="fas fa-fw fa-calendar-alt"></i>
        <span>Jadwal Pelajaran</span>
    </a>
</li>

<!-- Nav Item - Presensi Siswa -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'wali.presensi') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wali.presensi.index') }}">
        <i class="fas fa-fw fa-clipboard-check"></i>
        <span>Presensi Siswa</span>
    </a>
</li>

<!-- Nav Item - Nilai Siswa -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'wali.nilai') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wali.nilai.index') }}">
        <i class="fas fa-fw fa-chart-line"></i>
        <span>Nilai Siswa</span>
    </a>
</li>

<!-- Nav Item - Kelola Rapor -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'wali.rapor') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wali.rapor.index') }}">
        <i class="fas fa-fw fa-file-alt"></i>
        <span>Kelola Rapor</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Validasi
</div>

<!-- Nav Item - Validasi Akses -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'wali.validasi-akses') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wali.validasi-akses.index') }}">
        <i class="fas fa-fw fa-check-double"></i>
        <span>Validasi Akses</span>
    </a>
</li>