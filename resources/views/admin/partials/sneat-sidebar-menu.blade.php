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
        <li class="menu-item {{ $currentRoute == 'admin.users.orang-tua' ? 'active' : '' }}">
            <a href="{{ route('admin.users.orang-tua') }}" class="menu-link">
                <div>Orang Tua</div>
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

<!-- Mata Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.mata-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-book"></i>
        <div>Mata Pelajaran</div>
    </a>
</li>

<!-- Jadwal Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.jadwal-pelajaran') || Str::startsWith($currentRoute, 'admin.pengaturan-istirahat') ? 'active' : '' }}">
    <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- Manajemen Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.manajemen-siswa') ? 'active' : '' }}">
    <a href="{{ route('admin.manajemen-siswa.index') }}" class="menu-link">
        <i class="menu-icon fas fa-user-graduate"></i>
        <div>Manajemen Siswa</div>
    </a>
</li>

<!-- Menu Header - Keuangan -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Keuangan</span>
</li>

<!-- Tagihan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.tagihan') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.tagihan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-invoice-dollar"></i>
        <div>Tagihan</div>
    </a>
</li>

<!-- Pembayaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.pembayaran') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-money-bill-wave"></i>
        <div>Pembayaran</div>
    </a>
</li>

<!-- Laporan Keuangan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.laporan') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Laporan Keuangan</div>
    </a>
</li>

<!-- Validasi Akses -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-circle"></i>
        <div>Validasi Akses</div>
    </a>
</li>

<!-- Menu Header - Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Akademik</span>
</li>

<!-- Kalender Akademik -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.kalender') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.kalender.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar"></i>
        <div>Kalender Akademik</div>
    </a>
</li>

<!-- Pengumuman -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.pengumuman') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.pengumuman.index') }}" class="menu-link">
        <i class="menu-icon fas fa-bullhorn"></i>
        <div>Pengumuman</div>
    </a>
</li>

<!-- Berita -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.berita') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.berita.index') }}" class="menu-link">
        <i class="menu-icon fas fa-newspaper"></i>
        <div>Berita</div>
    </a>
</li>

<!-- Flyer -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.flyer') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.flyer.index') }}" class="menu-link">
        <i class="menu-icon fas fa-images"></i>
        <div>Flyer</div>
    </a>
</li>

<!-- Menu Header - Monitoring -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring & Analitik</span>
</li>

<!-- Monitoring -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.monitoring') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Monitoring</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.pengguna' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.pengguna') }}" class="menu-link">
                <div>Pengguna</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.wali-kelas' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.wali-kelas') }}" class="menu-link">
                <div>Wali Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.guru-pengajar' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.guru-pengajar') }}" class="menu-link">
                <div>Guru Pengajar</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.siswa' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.siswa') }}" class="menu-link">
                <div>Siswa</div>
            </a>
        </li>
    </ul>
</li>

<!-- Laporan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.laporan') && !Str::startsWith($currentRoute, 'admin.keuangan.laporan') ? 'active' : '' }}">
    <a href="{{ route('admin.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Laporan</div>
    </a>
</li>

<!-- Catatan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.catatan') ? 'active' : '' }}">
    <a href="{{ route('admin.catatan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-sticky-note"></i>
        <div>Catatan</div>
    </a>
</li>
