{{--
Sidebar Menu untuk Admin Dashboard - Sneat Version
File: resources/views/admin/partials/sneat-sidebar-menu.blade.php

Compatible dengan Sneat Bootstrap 5 Template
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'admin.dashboard' ? 'active' : '' }}">
    <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header - Manajemen Konten -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Konten</span>
</li>

<!-- Landing Page -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.landing-pages') ? 'active' : '' }}">
    <a href="{{ route('admin.landing-pages.index') }}" class="menu-link">
        <i class="menu-icon fas fa-globe"></i>
        <div>Landing Page</div>
    </a>
</li>

<!-- Menu Header - Manajemen Pengguna -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Pengguna</span>
</li>

<!-- Manajemen User (dengan Submenu) -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.users') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-users"></i>
        <div>Manajemen User</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'admin.users.tenaga-pendidik' ? 'active' : '' }}">
            <a href="{{ route('admin.users.tenaga-pendidik') }}" class="menu-link">
                <div>Tenaga Pendidik</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.users.siswa' ? 'active' : '' }}">
            <a href="{{ route('admin.users.siswa') }}" class="menu-link">
                <div>Siswa</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.users.orang-tua' ? 'active' : '' }}">
            <a href="{{ route('admin.users.orang-tua') }}" class="menu-link">
                <div>Wali Murid</div>
            </a>
        </li>
    </ul>
</li>

<!-- Recovery Tickets (Auto-WA) -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.recovery-tickets') ? 'active' : '' }}">
    <a href="{{ route('admin.recovery-tickets.index') }}" class="menu-link">
        <i class="menu-icon fas fa-life-ring"></i>
        <div>Tiket Pemulihan Akun</div>
        @php
            $pendingTickets = \App\Models\RecoveryTicket::whereIn('status', ['pending_admin', 'failed'])->count();
        @endphp
        @if($pendingTickets > 0)
            <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingTickets }}</span>
        @endif
    </a>
</li>

<!-- Pengaturan LMS -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.lms-settings') ? 'active' : '' }}">
    <a href="{{ route('admin.lms-settings.index') }}" class="menu-link">
        <i class="menu-icon fas fa-cogs"></i>
        <div>Pengaturan LMS</div>
    </a>
</li>

<!-- Pengaturan AI -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.ai-settings') ? 'active' : '' }}">
    <a href="{{ route('admin.ai-settings.index') }}" class="menu-link">
        <i class="menu-icon fas fa-robot"></i>
        <div>Pengaturan AI</div>
    </a>
</li>

{{-- Google Sheets Integration (Sembunyikan sementara)
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.google-sheets') ? 'active' : '' }}">
    <a href="{{ route('admin.google-sheets.index') }}" class="menu-link">
        <i class="menu-icon fas fa-table"></i>
        <div>Google Sheets Sync</div>
    </a>
</li>
--}}

<!-- Tahun Ajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.tahun-ajaran') ? 'active' : '' }}">
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Tahun Ajaran</div>
    </a>
</li>

<!-- Cabang -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.cabang') ? 'active' : '' }}">
    <a href="{{ route('admin.cabang.index') }}" class="menu-link">
        <i class="menu-icon fas fa-building"></i>
        <div>Manajemen Cabang</div>
    </a>
</li>

<!-- Menu Header - Data Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Data Akademik</span>
</li>

<!-- Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.kelas') ? 'active' : '' }}">
    <a href="{{ route('admin.kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-school"></i>
        <div>Data Kelas</div>
    </a>
</li>

<!-- Wali Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.wali-kelas') ? 'active' : '' }}">
    <a href="{{ route('admin.wali-kelas.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Data Wali Kelas</div>
    </a>
</li>

<!-- Guru Pengajar -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.guru-pengajar') ? 'active' : '' }}">
    <a href="{{ route('admin.guru-pengajar.index') }}" class="menu-link">
        <i class="menu-icon fas fa-user-tie"></i>
        <div>Data Guru Pengajar</div>
    </a>
</li>

<!-- Mata Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.mata-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-book"></i>
        <div>Mata Pelajaran</div>
    </a>
</li>

<!-- Jadwal Pelajaran -->
<li
    class="menu-item {{ Str::startsWith($currentRoute, 'admin.jadwal-pelajaran') || Str::startsWith($currentRoute, 'admin.pengaturan-istirahat') ? 'active' : '' }}">
    <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- Manajemen Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.manajemen-siswa') ? 'active' : '' }}">
    <a href="{{ route('admin.manajemen-siswa.index') }}" class="menu-link">
        <i class="menu-icon fas fa-user-graduate"></i>
        <div>Manajemen Siswa</div>
    </a>
</li>

<!-- Menu Header - Keuangan -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Keuangan</span>
</li>

<!-- Tagihan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.tagihan') && !Str::contains($currentRoute, 'carryover') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.tagihan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-invoice-dollar"></i>
        <div>Tagihan</div>
    </a>
</li>

<!-- Tarik Tunggakan TA Lama -->
<li class="menu-item {{ Str::contains($currentRoute, 'admin.keuangan.tagihan.carryover') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.tagihan.carryover') }}" class="menu-link">
        <i class="menu-icon fas fa-arrow-circle-right"></i>
        <div>Tarik Tunggakan</div>
    </a>
</li>

<!-- Pembayaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.pembayaran') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-money-bill-wave"></i>
        <div>Pembayaran</div>
    </a>
</li>

<!-- Info Pembayaran (API Midtrans & Rekening Bank) -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.info-pembayaran') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.info-pembayaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-cog"></i>
        <div>Config Pembayaran</div>
    </a>
</li>

<!-- Laporan Keuangan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.laporan') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Laporan Keuangan</div>
    </a>
</li>

<!-- Menu Header - Validasi Akses -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi Akses</span>
</li>

<!-- Validasi Ujian & Rapor -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-circle"></i>
        <div>Validasi Ujian & Rapor</div>
    </a>
</li>

<!-- Menu Header - Kenaikan Kelas -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Kenaikan Kelas</span>
</li>

<!-- Validasi Dispensasi -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.promotion.validation') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.promotion.validation.index') }}" class="menu-link">
        <i class="menu-icon fas fa-hand-holding-usd"></i>
        <div>Validasi Dispensasi</div>
    </a>
</li>

<!-- Pengaturan KKM -->
<li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.kkm.index' ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.promotion.kkm.index') }}" class="menu-link">
        <i class="menu-icon fas fa-ruler-combined"></i>
        <div>Pengaturan KKM</div>
    </a>
</li>

<!-- Pengaturan Kenaikan -->
<li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.settings.index' ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.promotion.settings.index') }}" class="menu-link">
        <i class="menu-icon fas fa-cogs"></i>
        <div>Pengaturan Kenaikan</div>
    </a>
</li>

<!-- Proses & Rekap -->
<li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.report' ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.promotion.report') }}" class="menu-link">
        <i class="menu-icon fas fa-tasks"></i>
        <div>Proses & Rekap</div>
    </a>
</li>

<!-- Menu Header - Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Akademik</span>
</li>

<!-- Kalender Akademik -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.kalender') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.kalender.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar"></i>
        <div>Kalender Akademik</div>
    </a>
</li>

<!-- Pengumuman -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.pengumuman') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.pengumuman.index') }}" class="menu-link">
        <i class="menu-icon fas fa-bullhorn"></i>
        <div>Pengumuman</div>
    </a>
</li>

<!-- Flyer / Iklan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.flyer') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.flyer.index') }}" class="menu-link">
        <i class="menu-icon fas fa-image"></i>
        <div>Flyer / Iklan</div>
    </a>
</li>

<!-- Kelola Berita -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.berita') ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.berita.index') }}" class="menu-link">
        <i class="menu-icon fas fa-newspaper"></i>
        <div>Kelola Berita</div>
    </a>
</li>

<!-- Menu Header - Monitoring -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring & Analitik</span>
</li>

<!-- Monitoring -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.monitoring') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Monitoring</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.pengguna' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.pengguna') }}" class="menu-link">
                <div>Pengguna</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.wali-kelas' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.wali-kelas') }}" class="menu-link">
                <div>Wali Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.guru-pengajar' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.guru-pengajar') }}" class="menu-link">
                <div>Guru Pengajar</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.monitoring.siswa' ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.siswa') }}" class="menu-link">
                <div>Siswa</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.monitoring.lms') ? 'active' : '' }}">
            <a href="{{ route('admin.monitoring.lms.index') }}" class="menu-link">
                <div>Monitoring LMS</div>
            </a>
        </li>
    </ul>
</li>

<!-- Laporan -->
<li
    class="menu-item {{ Str::startsWith($currentRoute, 'admin.laporan') && !Str::startsWith($currentRoute, 'admin.keuangan.laporan') ? 'active' : '' }}">
    <a href="{{ route('admin.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Laporan</div>
    </a>
</li>

<!-- Catatan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.catatan') ? 'active' : '' }}">
    <a href="{{ route('admin.catatan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-sticky-note"></i>
        <div>Catatan</div>
    </a>
</li>
