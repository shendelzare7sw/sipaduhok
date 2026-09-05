{{--
    Navigasi CleanFlow untuk Bendahara.
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

{{-- Dashboard --}}
<li class="menu-item {{ $currentRoute == 'bendahara.dashboard' ? 'active' : '' }}">
    <a href="{{ route('bendahara.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC11: Kelola Tagihan & Pembayaran            --}}
{{-- (Tagihan, Tunggakan, Pembayaran, Config)     --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Keuangan</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.tagihan') || Str::startsWith($currentRoute, 'bendahara.pembayaran') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-file-invoice-dollar"></i>
        <div>Tagihan & Pembayaran</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.tagihan') && !Str::contains($currentRoute, 'carryover') ? 'active' : '' }}">
            <a href="{{ route('bendahara.tagihan.index') }}" class="menu-link">
                <div>Kelola Tagihan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::contains($currentRoute, 'bendahara.tagihan.carryover') ? 'active' : '' }}">
            <a href="{{ route('bendahara.tagihan.carryover') }}" class="menu-link">
                <div>Tarik Tunggakan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.pembayaran') ? 'active' : '' }}">
            <a href="{{ route('bendahara.pembayaran.index') }}" class="menu-link">
                <div>Kelola Pembayaran</div>
            </a>
        </li>
    </ul>
</li>

{{-- ============================================ --}}
{{-- UC14: Validasi Akses Ujian dan Rapor         --}}
{{-- UC16: Memproses Dispensasi Kenaikan Kelas    --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi & Dispensasi</span>
</li>

{{-- UC14: Validasi Akses Ujian dan Rapor --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('bendahara.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-circle"></i>
        <div>Validasi Ujian & Rapor</div>
    </a>
</li>

{{-- UC16: Memproses Dispensasi Kenaikan Kelas --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.kenaikan-kelas.validation') ? 'active' : '' }}">
    <a href="{{ route('bendahara.kenaikan-kelas.validation.index') }}" class="menu-link">
        <i class="menu-icon fas fa-hand-holding-usd"></i>
        <div>Validasi Dispensasi</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC12: Lihat Laporan Keuangan                 --}}
{{-- (Laporan Pembayaran, Rekap, Belum Lunas)     --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Laporan</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.laporan') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Laporan Keuangan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'bendahara.laporan.index' || $currentRoute == 'bendahara.laporan.cetak' ? 'active' : '' }}">
            <a href="{{ route('bendahara.laporan.index') }}" class="menu-link">
                <div>Laporan Pembayaran</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.laporan.rekap-tagihan') ? 'active' : '' }}">
            <a href="{{ route('bendahara.laporan.rekap-tagihan') }}" class="menu-link">
                <div>Rekap Tagihan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'bendahara.laporan.belum-lunas') ? 'active' : '' }}">
            <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="menu-link">
                <div>Siswa Belum Lunas</div>
            </a>
        </li>
    </ul>
</li>
