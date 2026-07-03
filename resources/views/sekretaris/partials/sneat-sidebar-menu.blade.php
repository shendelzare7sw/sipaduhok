{{--
    Sidebar Menu untuk Sekretaris Dashboard - Sneat Version
    File: resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

{{-- Dashboard --}}
<li class="menu-item {{ $currentRoute == 'sekretaris.dashboard' ? 'active' : '' }}">
    <a href="{{ route('sekretaris.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC10: Kelola Konten Publikasi                --}}
{{-- (Kalender, Pengumuman, Flyer, Berita)        --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Konten</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.kalender') || Str::startsWith($currentRoute, 'sekretaris.pengumuman') || Str::startsWith($currentRoute, 'sekretaris.flyer') || Str::startsWith($currentRoute, 'sekretaris.berita') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-bullhorn"></i>
        <div>Konten Publikasi</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.kalender') ? 'active' : '' }}">
            <a href="{{ route('sekretaris.kalender.index') }}" class="menu-link">
                <div>Kalender Akademik</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.pengumuman') ? 'active' : '' }}">
            <a href="{{ route('sekretaris.pengumuman.index') }}" class="menu-link">
                <div>Pengumuman</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.flyer') ? 'active' : '' }}">
            <a href="{{ route('sekretaris.flyer.index') }}" class="menu-link">
                <div>Flyer / Iklan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'sekretaris.berita') ? 'active' : '' }}">
            <a href="{{ route('sekretaris.berita.index') }}" class="menu-link">
                <div>Kelola Berita</div>
            </a>
        </li>
    </ul>
</li>

