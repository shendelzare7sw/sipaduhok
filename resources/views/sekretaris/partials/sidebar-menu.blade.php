{{-- 
    Sidebar Menu untuk Sekretaris Dashboard
    File: resources/views/sekretaris/partials/sidebar-menu.blade.php
    
    Compatible dengan SB Admin 2 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Nav Item - Dashboard -->
<li class="nav-item {{ $currentRoute == 'sekretaris.dashboard' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('sekretaris.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Manajemen Konten
</div>

<!-- Nav Item - Kalender Akademik -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'sekretaris.kalender') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('sekretaris.kalender.index') }}">
        <i class="fas fa-fw fa-calendar-alt"></i>
        <span>Kalender Akademik</span>
    </a>
</li>

<!-- Nav Item - Pengumuman -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'sekretaris.pengumuman') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('sekretaris.pengumuman.index') }}">
        <i class="fas fa-fw fa-bullhorn"></i>
        <span>Pengumuman</span>
    </a>
</li>

<!-- Nav Item - Flyer / Iklan -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'sekretaris.flyer') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('sekretaris.flyer.index') }}">
        <i class="fas fa-fw fa-image"></i>
        <span>Flyer / Iklan</span>
    </a>
</li>

<!-- Nav Item - Kelola Berita -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'sekretaris.berita') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('sekretaris.berita.index') }}">
        <i class="fas fa-fw fa-newspaper"></i>
        <span>Kelola Berita</span>
    </a>
</li>