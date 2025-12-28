{{--
    Sidebar Menu untuk Orang Tua - Sneat Version
    File: resources/views/orang-tua/partials/sidebar.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
    $user = Auth::user();
    $children = $user->children()->with(['kelas', 'cabang'])->get();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'orang-tua.dashboard' ? 'active' : '' }}">
    <a href="{{ route('orang-tua.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Dashboard">Dashboard</div>
    </a>
</li>

<!-- Menu Header - Monitoring Anak -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring Anak</span>
</li>

<!-- Daftar Anak dengan Submenu -->
@if($children->isNotEmpty())
    @foreach($children as $child)
        @php
            $isChildActive = (
                (Str::startsWith($currentRoute, 'orang-tua.tagihan') && request()->route('siswa') == $child->id) ||
                (Str::startsWith($currentRoute, 'orang-tua.rapor') && request()->route('siswa') == $child->id) ||
                (Str::startsWith($currentRoute, 'orang-tua.presensi') && request()->route('siswa') == $child->id)
            );
        @endphp
        <li class="menu-item {{ $isChildActive ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-user-circle"></i>
                <div data-i18n="{{ $child->nama_lengkap }}">{{ Str::limit($child->nama_lengkap, 20) }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $currentRoute == 'orang-tua.tagihan.anak' && request()->route('siswa') == $child->id ? 'active' : '' }}">
                    <a href="{{ route('orang-tua.tagihan.anak', $child->id) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-credit-card"></i>
                        <div data-i18n="Tagihan & Pembayaran">Tagihan & Pembayaran</div>
                    </a>
                </li>
                <li class="menu-item {{ Str::startsWith($currentRoute, 'orang-tua.rapor') && request()->route('siswa') == $child->id ? 'active' : '' }}">
                    <a href="{{ route('orang-tua.rapor.anak', $child->id) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Rapor">Rapor</div>
                    </a>
                </li>
                <li class="menu-item {{ Str::startsWith($currentRoute, 'orang-tua.presensi') && request()->route('siswa') == $child->id ? 'active' : '' }}">
                    <a href="{{ route('orang-tua.presensi.anak', $child->id) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                        <div data-i18n="Presensi">Presensi</div>
                    </a>
                </li>
            </ul>
        </li>
    @endforeach
@else
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link disabled">
            <i class="menu-icon tf-icons bx bx-info-circle"></i>
            <div data-i18n="Belum Ada Data Anak">Belum Ada Data Anak</div>
        </a>
    </li>
@endif
