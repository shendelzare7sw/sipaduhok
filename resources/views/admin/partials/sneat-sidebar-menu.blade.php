{{--
    Sidebar Menu untuk Admin Dashboard - Sneat Version
    File: resources/views/admin/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'admin.dashboard' ? 'active' : '' }}">
    <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Manajemen Pengguna -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Pengguna</span>
</li>

<!-- Manajemen User (dengan Submenu) -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.users') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-users"></i>
        <div>Manajemen User</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'admin.users.tenaga-pendidik' ? 'active' : '' }}">
            <a href="{{ route('admin.users.tenaga-pendidik') }}" class="menu-link">
                <div>Tenaga Pendidik</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.users.siswa' ? 'active' : '' }}">
            <a href="{{ route('admin.users.siswa') }}" class="menu-link">
                <div>Siswa</div>
            </a>
        </li>
    </ul>
</li>

<!-- Tahun Ajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.tahun-ajaran') ? 'active' : '' }}">
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Tahun Ajaran</div>
    </a>
</li>

<!-- Cabang -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.cabang') ? 'active' : '' }}">
    <a href="{{ route('admin.cabang.index') }}" class="menu-link">
        <i class="menu-icon fas fa-building"></i>
        <div>Manajemen Cabang</div>
    </a>
</li>

<!-- Menu Header - Data Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Data Akademik</span>
</li>

<!-- Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.kelas') ? 'active' : '' }}">
    <a href="{{ route('admin.kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-school"></i>
        <div>Data Kelas</div>
    </a>
</li>

<!-- Wali Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.wali-kelas') ? 'active' : '' }}">
    <a href="{{ route('admin.wali-kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Data Wali Kelas</div>
    </a>
</li>

<!-- Guru Pengajar -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.guru-pengajar') ? 'active' : '' }}">
    <a href="{{ route('admin.guru-pengajar.index') }}" class="menu-link">
        <i class="menu-icon fas fa-user-tie"></i>
        <div>Data Guru Pengajar</div>
    </a>
</li>

<!-- Manajemen Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.manajemen-siswa') ? 'active' : '' }}">
    <a href="{{ route('admin.manajemen-siswa.index') }}" class="menu-link">
        <i class="menu-icon fas fa-user-graduate"></i>
        <div>Manajemen Siswa</div>
    </a>
</li>

<!-- Menu Header - Laporan -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Laporan</span>
</li>

<!-- Cetak Laporan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.cetak-laporan') ? 'active' : '' }}">
    <a href="{{ route('admin.cetak-laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-pdf"></i>
        <div>Cetak Laporan</div>
    </a>
</li>
