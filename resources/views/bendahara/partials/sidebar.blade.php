{{-- 
    Sidebar Menu untuk Bendahara Dashboard
    File: resources/views/bendahara/partials/sidebar.blade.php
    
    Compatible dengan SB Admin 2 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Nav Item - Dashboard -->
<li class="nav-item {{ $currentRoute == 'bendahara.dashboard' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Keuangan
</div>

<!-- Nav Item - Kelola Tagihan -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'bendahara.tagihan') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.tagihan.index') }}">
        <i class="fas fa-fw fa-file-invoice-dollar"></i>
        <span>Kelola Tagihan</span>
    </a>
</li>

<!-- Nav Item - Kelola Pembayaran -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'bendahara.pembayaran') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.pembayaran.index') }}">
        <i class="fas fa-fw fa-money-bill-wave"></i>
        <span>Kelola Pembayaran</span>
    </a>
</li>

<!-- Nav Item - Info Pembayaran -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'bendahara.info-pembayaran') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.info-pembayaran.index') }}">
        <i class="fas fa-fw fa-cog"></i>
        <span>Info Pembayaran</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Validasi Akses
</div>

<!-- Nav Item - Validasi Ujian & Rapor -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'bendahara.validasi-akses') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.validasi-akses.index') }}">
        <i class="fas fa-fw fa-check-circle"></i>
        <span>Validasi Ujian & Rapor</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Laporan
</div>

<!-- Nav Item - Laporan Pembayaran -->
<li class="nav-item {{ $currentRoute == 'bendahara.laporan.index' || $currentRoute == 'bendahara.laporan.cetak' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.laporan.index') }}">
        <i class="fas fa-fw fa-chart-bar"></i>
        <span>Laporan Pembayaran</span>
    </a>
</li>

<!-- Nav Item - Rekap Tagihan -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'bendahara.laporan.rekap-tagihan') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.laporan.rekap-tagihan') }}">
        <i class="fas fa-fw fa-file-alt"></i>
        <span>Rekap Tagihan</span>
    </a>
</li>

<!-- Nav Item - Siswa Belum Lunas -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'bendahara.laporan.belum-lunas') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('bendahara.laporan.belum-lunas') }}">
        <i class="fas fa-fw fa-exclamation-triangle"></i>
        <span>Siswa Belum Lunas</span>
    </a>
</li>