{{--
    Navigasi CleanFlow untuk Wali Siswa.
--}}

@php
    $currentRoute = Route::currentRouteName();
    $user = Auth::user();
    $children = $user->children()->with(['kelas', 'cabang'])->get();
@endphp

{{-- Dashboard --}}
<li class="menu-item {{ $currentRoute == 'wali-siswa.dashboard' ? 'active' : '' }}">
    <a href="{{ route('wali-siswa.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Dashboard">Dashboard</div>
    </a>
</li>

{{-- Menu Header - Monitoring Anak --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring Anak</span>
</li>

{{-- Daftar Anak dengan Submenu --}}
@if($children->isNotEmpty())
    @foreach($children as $child)
        @php
            $isChildActive = (
                (Str::startsWith($currentRoute, 'wali-siswa.tagihan') && request()->route('siswa') == $child->id) ||
                (Str::startsWith($currentRoute, 'wali-siswa.rapor') && request()->route('siswa') == $child->id) ||
                (Str::startsWith($currentRoute, 'wali-siswa.presensi') && request()->route('siswa') == $child->id)
            );
        @endphp
        <li class="menu-item {{ $isChildActive ? 'active open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-user-circle"></i>
                <div data-i18n="{{ $child->nama_lengkap }}">{{ Str::limit($child->nama_lengkap, 20) }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Str::startsWith($currentRoute, 'wali-siswa.presensi') && request()->route('siswa') == $child->id ? 'active' : '' }}">
                    <a href="{{ route('wali-siswa.presensi.anak', $child->id) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                        <div data-i18n="Presensi">Presensi</div>
                    </a>
                </li>
                <li class="menu-item {{ $currentRoute == 'wali-siswa.tagihan.anak' && request()->route('siswa') == $child->id ? 'active' : '' }}">
                    <a href="{{ route('wali-siswa.tagihan.anak', $child->id) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-credit-card"></i>
                        <div data-i18n="Tagihan & Pembayaran">Tagihan</div>
                    </a>
                </li>
                <li class="menu-item {{ Str::startsWith($currentRoute, 'wali-siswa.rapor') && request()->route('siswa') == $child->id ? 'active' : '' }}">
                    <a href="{{ route('wali-siswa.rapor.anak', $child->id) }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Rapor">Rapor</div>
                    </a>
                </li>
            </ul>
        </li>
    @endforeach
@else
    <li class="menu-item">
        <a href="#" class="menu-link disabled" aria-disabled="true" tabindex="-1">
            <i class="menu-icon tf-icons bx bx-info-circle"></i>
            <div data-i18n="Belum Ada Data Anak">Belum Ada Data Anak</div>
        </a>
    </li>
@endif
