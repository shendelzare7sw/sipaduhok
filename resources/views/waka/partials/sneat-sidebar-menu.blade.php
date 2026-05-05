{{--
    Sidebar Menu untuk Wakil Kepala Sekolah - Sneat Version
    File: resources/views/waka/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'waka.dashboard' ? 'active' : '' }}">
    <a href="{{ route('waka.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Manajemen Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Akademik</span>
</li>

<!-- Tahun Ajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.tahun-ajaran') ? 'active' : '' }}">
    <a href="{{ route('waka.tahun-ajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Tahun Ajaran</div>
    </a>
</li>

<!-- Mata Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.mata-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('waka.mata-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-book"></i>
        <div>Mata Pelajaran</div>
    </a>
</li>

<!-- Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kelas') ? 'active' : '' }}">
    <a href="{{ route('waka.kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-school"></i>
        <div>Manajemen Kelas</div>
    </a>
</li>

<!-- Manajemen Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.manajemen-siswa') ? 'active' : '' }}">
    <a href="{{ route('waka.manajemen-siswa.index') }}" class="menu-link">
        <i class="menu-icon fas fa-users"></i>
        <div>Manajemen Siswa</div>
    </a>
</li>

<!-- Wali Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.wali-kelas') ? 'active' : '' }}">
    <a href="{{ route('waka.wali-kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-user-check"></i>
        <div>Penugasan Wali Kelas</div>
    </a>
</li>

<!-- Jadwal Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.jadwal-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('waka.jadwal-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-clipboard-list"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- Guru Pengajar -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.guru-pengajar') ? 'active' : '' }}">
    <a href="{{ route('waka.guru-pengajar.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Guru Pengajar</div>
    </a>
</li>

<!-- Menu Header - Kenaikan Kelas -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Kenaikan Kelas</span>
</li>

<!-- Pengaturan KKM -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.promotion.kkm') ? 'active' : '' }}">
    <a href="{{ route('waka.promotion.kkm.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Pengaturan KKM</div>
    </a>
</li>

<!-- Pengaturan Kenaikan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.promotion.settings') ? 'active' : '' }}">
    <a href="{{ route('waka.promotion.settings.index') }}" class="menu-link">
        <i class="menu-icon fas fa-cogs"></i>
        <div>Pengaturan Naik Kelas</div>
    </a>
</li>

<!-- Rekap Kenaikan (Report) -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.promotion.report') ? 'active' : '' }}">
    <a href="{{ route('waka.promotion.report') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Proses & Rekap</div>
    </a>
</li>
<!-- Menu Header - Monitoring -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring</span>
</li>

<!-- Monitoring Wali Kelas -->
<li class="menu-item {{ $currentRoute == 'waka.monitoring.wali-kelas' ? 'active' : '' }}">
    <a href="{{ route('waka.monitoring.wali-kelas') }}" class="menu-link">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Monitoring Wali Kelas</div>
    </a>
</li>

<!-- Monitoring Guru Pengajar -->
<li class="menu-item {{ $currentRoute == 'waka.monitoring.guru-pengajar' ? 'active' : '' }}">
    <a href="{{ route('waka.monitoring.guru-pengajar') }}" class="menu-link">
        <i class="menu-icon fas fa-user-tie"></i>
        <div>Monitoring Guru Pengajar</div>
    </a>
</li>

<!-- Monitoring Siswa -->
<li class="menu-item {{ $currentRoute == 'waka.monitoring.siswa' ? 'active' : '' }}">
    <a href="{{ route('waka.monitoring.siswa') }}" class="menu-link">
        <i class="menu-icon fas fa-user-graduate"></i>
        <div>Monitoring Siswa</div>
    </a>
</li>

<!-- Monitoring LMS (cabang-scoped) -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.monitoring.lms') ? 'active' : '' }}">
    <a href="{{ route('waka.monitoring.lms.index') }}" class="menu-link">
        <i class="menu-icon fas fa-desktop"></i>
        <div>Monitoring LMS</div>
    </a>
</li>

<!-- Menu Header - Komunikasi -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Komunikasi</span>
</li>

<!-- Kirim Catatan/Teguran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.catatan') ? 'active' : '' }}">
    <a href="{{ route('waka.catatan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-sticky-note"></i>
        <div>Kirim Catatan</div>
    </a>
</li>
