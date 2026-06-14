{{--
    Sidebar Menu untuk Ketua PKBM - Sneat Version
    File: resources/views/ketua/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Bootstrap 5 Template
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'ketua.dashboard' ? 'active' : '' }}">
    <a href="{{ route('ketua.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC15: Memproses Dispensasi Keuangan          -->
<!-- UC16: Memproses Dispensasi Kenaikan Kelas    -->
<!-- UC20: Validasi Rapor Tingkat Akhir           -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Persetujuan & Validasi</span>
</li>

<!-- UC16: Memproses Dispensasi Kenaikan Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.promotion.approval') ? 'active' : '' }}">
    <a href="{{ route('ketua.promotion.approval.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-double"></i>
        <div>Dispensasi Kenaikan</div>
    </a>
</li>

<!-- UC21: Validasi Rapor Tingkat Akhir -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.validasi-rapor') ? 'active' : '' }}">
    <a href="{{ route('ketua.validasi-rapor.index') }}" class="menu-link">
        <i class="menu-icon fas fa-certificate"></i>
        <div>Validasi Rapor</div>
    </a>
</li>

<!-- UC15: Memproses Dispensasi Keuangan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.dispensasi') ? 'active' : '' }}">
    <a href="{{ route('ketua.dispensasi.index') }}" class="menu-link">
        <i class="menu-icon fas fa-hand-holding-heart"></i>
        <div>Dispensasi Keuangan</div>
        @php $pendingDispensasi = \App\Models\PengajuanRaporKetua::where('status', 'menunggu')->count(); @endphp
        @if($pendingDispensasi > 0)
            <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingDispensasi }}</span>
        @endif
    </a>
</li>

<!-- ============================================ -->
<!-- UC35: Monitoring Sistem Terpadu              -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring</span>
</li>

<!-- UC35: Monitoring Sistem Terpadu -->
<li class="menu-item {{ $currentRoute == 'ketua.monitoring.pengguna' || $currentRoute == 'ketua.monitoring.wali-kelas' || $currentRoute == 'ketua.monitoring.guru-pengajar' || $currentRoute == 'ketua.monitoring.siswa' || Str::startsWith($currentRoute, 'ketua.monitoring.lms') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Monitoring Sistem</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'ketua.monitoring.pengguna' ? 'active' : '' }}">
            <a href="{{ route('ketua.monitoring.pengguna') }}" class="menu-link">
                <div>Data Pengguna</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'ketua.monitoring.wali-kelas' ? 'active' : '' }}">
            <a href="{{ route('ketua.monitoring.wali-kelas') }}" class="menu-link">
                <div>Data Wali Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'ketua.monitoring.guru-pengajar' ? 'active' : '' }}">
            <a href="{{ route('ketua.monitoring.guru-pengajar') }}" class="menu-link">
                <div>Data Guru Pengajar</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'ketua.monitoring.siswa' ? 'active' : '' }}">
            <a href="{{ route('ketua.monitoring.siswa') }}" class="menu-link">
                <div>Data Siswa</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.monitoring.lms') ? 'active' : '' }}">
            <a href="{{ route('ketua.monitoring.lms.index') }}" class="menu-link">
                <div>Monitoring LMS</div>
            </a>
        </li>
    </ul>
</li>

<!-- ============================================ -->
<!-- UC36: Kelola Laporan & Catatan               -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Laporan & Komunikasi</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.laporan') || Str::startsWith($currentRoute, 'ketua.catatan') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Laporan & Catatan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.laporan') ? 'active' : '' }}">
            <a href="{{ route('ketua.laporan.index') }}" class="menu-link">
                <div>Cetak Laporan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'ketua.catatan') ? 'active' : '' }}">
            <a href="{{ route('ketua.catatan.index') }}" class="menu-link">
                <div>Kirim Catatan</div>
            </a>
        </li>
    </ul>
</li>
