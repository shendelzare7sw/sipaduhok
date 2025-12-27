{{--
    Sidebar Menu untuk Sekretaris Dashboard - Sneat Version
    File: resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'sekretaris.dashboard' ? 'active' : '' }}">
    <a href="{{ route('sekretaris.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Manajemen Konten -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Konten</span>
</li>

<!-- Kalender Akademik -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.kalender') ? 'active' : '' }}">
    <a href="{{ route('sekretaris.kalender.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Kalender Akademik</div>
    </a>
</li>

<!-- Pengumuman -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.pengumuman') ? 'active' : '' }}">
    <a href="{{ route('sekretaris.pengumuman.index') }}" class="menu-link">
        <i class="menu-icon fas fa-bullhorn"></i>
        <div>Pengumuman</div>
    </a>
</li>

<!-- Flyer / Iklan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.flyer') ? 'active' : '' }}">
    <a href="{{ route('sekretaris.flyer.index') }}" class="menu-link">
        <i class="menu-icon fas fa-image"></i>
        <div>Flyer / Iklan</div>
    </a>
</li>

<!-- Kelola Berita -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.berita') ? 'active' : '' }}">
    <a href="{{ route('sekretaris.berita.index') }}" class="menu-link">
        <i class="menu-icon fas fa-newspaper"></i>
        <div>Kelola Berita</div>
    </a>
</li>
