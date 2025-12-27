{{--
    Sidebar Menu untuk Guru Dashboard (Sneat Template)
    File: resources/views/guru/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'guru.dashboard' ? 'active' : '' }}">
    <a href="{{ route('guru.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Pembelajaran Section -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Pembelajaran</span>
</li>

<!-- Daftar Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'guru.kelas') ? 'active' : '' }}">
    <a href="{{ route('guru.kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Daftar Kelas</div>
    </a>
</li>
