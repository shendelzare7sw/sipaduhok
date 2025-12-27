{{--
    Sidebar Menu untuk Bendahara - Sneat Version
    File: resources/views/bendahara/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'bendahara.dashboard' ? 'active' : '' }}">
    <a href="{{ route('bendahara.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Keuangan -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Keuangan</span>
</li>

<!-- Kelola Tagihan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.tagihan') ? 'active' : '' }}">
    <a href="{{ route('bendahara.tagihan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-invoice-dollar"></i>
        <div>Kelola Tagihan</div>
    </a>
</li>

<!-- Kelola Pembayaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.pembayaran') ? 'active' : '' }}">
    <a href="{{ route('bendahara.pembayaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-money-bill-wave"></i>
        <div>Kelola Pembayaran</div>
    </a>
</li>

<!-- Info Pembayaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.info-pembayaran') ? 'active' : '' }}">
    <a href="{{ route('bendahara.info-pembayaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-cog"></i>
        <div>Info Pembayaran</div>
    </a>
</li>

<!-- Menu Header - Validasi Akses -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi Akses</span>
</li>

<!-- Validasi Ujian & Rapor -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('bendahara.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-circle"></i>
        <div>Validasi Ujian & Rapor</div>
    </a>
</li>

<!-- Menu Header - Laporan -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Laporan</span>
</li>

<!-- Laporan Pembayaran -->
<li class="menu-item {{ $currentRoute == 'bendahara.laporan.index' || $currentRoute == 'bendahara.laporan.cetak' ? 'active' : '' }}">
    <a href="{{ route('bendahara.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Laporan Pembayaran</div>
    </a>
</li>

<!-- Rekap Tagihan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.laporan.rekap-tagihan') ? 'active' : '' }}">
    <a href="{{ route('bendahara.laporan.rekap-tagihan') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Rekap Tagihan</div>
    </a>
</li>

<!-- Siswa Belum Lunas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.laporan.belum-lunas') ? 'active' : '' }}">
    <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="menu-link">
        <i class="menu-icon fas fa-exclamation-triangle"></i>
        <div>Siswa Belum Lunas</div>
    </a>
</li>
