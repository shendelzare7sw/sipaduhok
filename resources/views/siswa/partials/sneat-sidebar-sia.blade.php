{{--
    Sidebar Menu untuk Siswa SIA (Sneat Template)
    File: resources/views/siswa/partials/sneat-sidebar-sia.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
    // Logika pengecekan akses LMS
    $siswa = \App\Models\Siswa::where('user_id', auth()->id())->with('kelas')->first();
    $showLms = $siswa && $siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']);
@endphp

<!-- Dashboard SIA -->
<li class="menu-item {{ request()->routeIs('siswa.sia.dashboard') ? 'active' : '' }}">
    <a href="{{ route('siswa.sia.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard SIA</div>
    </a>
</li>

@if($showLms)
    <!-- LMS Section -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Learning Management</span>
    </li>

    <li class="menu-item {{ request()->routeIs('siswa.lms.*') ? 'active' : '' }}">
        <a href="{{ route('siswa.lms.dashboard') }}" class="menu-link">
            <i class="menu-icon fas fa-graduation-cap"></i>
            <div class="fw-bold">HOK-LMS</div>
        </a>
    </li>
@endif

<!-- Akademik Section -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Akademik</span>
</li>

<li class="menu-item {{ request()->routeIs('siswa.sia.presensi.*') ? 'active' : '' }}">
    <a href="{{ route('siswa.sia.presensi.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-check"></i>
        <div>Presensi</div>
    </a>
</li>

<li class="menu-item {{ request()->routeIs('siswa.sia.penilaian') ? 'active' : '' }}">
    <a href="{{ route('siswa.sia.penilaian') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Data Penilaian</div>
    </a>
</li>

<li class="menu-item {{ request()->routeIs('siswa.sia.rapor.*') ? 'active' : '' }}">
    <a href="{{ route('siswa.sia.rapor.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Rapor</div>
    </a>
</li>

<!-- Keuangan Section -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Keuangan</span>
</li>

<li class="menu-item {{ request()->routeIs('siswa.sia.pembayaran.*') ? 'active' : '' }}">
    <a href="{{ route('siswa.sia.pembayaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-credit-card"></i>
        <div>Pembayaran</div>
    </a>
</li>
