{{-- 
    Sidebar Menu untuk Ketua PKBM Dashboard
    File: resources/views/ketua/partials/sidebar-menu.blade.php
    
    Compatible dengan SB Admin 2 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Nav Item - Dashboard -->
<li class="nav-item {{ $currentRoute == 'ketua.dashboard' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Monitoring
</div>

<!-- Nav Item - Data Pengguna -->
<li class="nav-item {{ $currentRoute == 'ketua.monitoring.pengguna' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.monitoring.pengguna') }}">
        <i class="fas fa-fw fa-users"></i>
        <span>Data Pengguna</span>
    </a>
</li>

<!-- Nav Item - Data Wali Kelas -->
<li class="nav-item {{ $currentRoute == 'ketua.monitoring.wali-kelas' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.monitoring.wali-kelas') }}">
        <i class="fas fa-fw fa-chalkboard-teacher"></i>
        <span>Data Wali Kelas</span>
    </a>
</li>

<!-- Nav Item - Data Guru Pengajar -->
<li class="nav-item {{ $currentRoute == 'ketua.monitoring.guru-pengajar' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.monitoring.guru-pengajar') }}">
        <i class="fas fa-fw fa-user-tie"></i>
        <span>Data Guru Pengajar</span>
    </a>
</li>

<!-- Nav Item - Data Siswa -->
<li class="nav-item {{ $currentRoute == 'ketua.monitoring.siswa' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.monitoring.siswa') }}">
        <i class="fas fa-fw fa-user-graduate"></i>
        <span>Data Siswa</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Laporan & Catatan
</div>

<!-- Nav Item - Laporan -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'ketua.laporan') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.laporan.index') }}">
        <i class="fas fa-fw fa-file-alt"></i>
        <span>Laporan</span>
    </a>
</li>

<!-- Nav Item - Kirim Catatan -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'ketua.catatan') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('ketua.catatan.index') }}">
        <i class="fas fa-fw fa-sticky-note"></i>
        <span>Kirim Catatan</span>
    </a>
</li>