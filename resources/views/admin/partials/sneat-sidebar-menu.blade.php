{{--
Sidebar Menu untuk Admin Dashboard - Sneat Version
File: resources/views/admin/partials/sneat-sidebar-menu.blade.php

Compatible dengan Sneat Bootstrap 5 Template
Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
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

<!-- ============================================ -->
<!-- UC09: Kelola Landing Page                    -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Konten</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.landing-pages') ? 'active' : '' }}">
    <a href="{{ route('admin.landing-pages.index') }}" class="menu-link">
        <i class="menu-icon fas fa-globe"></i>
        <div>Landing Page</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC10: Kelola Konten Publikasi                -->
<!-- (Kalender, Pengumuman, Flyer, Berita)        -->
<!-- ============================================ -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.kalender') || Str::startsWith($currentRoute, 'admin.akademik.pengumuman') || Str::startsWith($currentRoute, 'admin.akademik.flyer') || Str::startsWith($currentRoute, 'admin.akademik.berita') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-bullhorn"></i>
        <div>Konten Publikasi</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.kalender') ? 'active' : '' }}">
            <a href="{{ route('admin.akademik.kalender.index') }}" class="menu-link">
                <div>Kalender Akademik</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.pengumuman') ? 'active' : '' }}">
            <a href="{{ route('admin.akademik.pengumuman.index') }}" class="menu-link">
                <div>Pengumuman</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.flyer') ? 'active' : '' }}">
            <a href="{{ route('admin.akademik.flyer.index') }}" class="menu-link">
                <div>Flyer / Iklan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.akademik.berita') ? 'active' : '' }}">
            <a href="{{ route('admin.akademik.berita.index') }}" class="menu-link">
                <div>Kelola Berita</div>
            </a>
        </li>
    </ul>
</li>

<!-- ============================================ -->
<!-- UC01: Kelola Data Pengguna                   -->
<!-- (Tenaga Pendidik, Siswa, Wali Murid)         -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Pengguna</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.users') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-users"></i>
        <div>Kelola Data Pengguna</div>
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

<!-- UC02: Kelola Tiket Pemulihan Akun -->
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

<!-- ============================================ -->
<!-- UC03: Kelola Pengaturan Sistem               -->
<!-- (Pengaturan LMS + Pengaturan AI)             -->
<!-- ============================================ -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.lms-settings') || Str::startsWith($currentRoute, 'admin.ai-settings') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-cogs"></i>
        <div>Pengaturan Sistem</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.lms-settings') ? 'active' : '' }}">
            <a href="{{ route('admin.lms-settings.index') }}" class="menu-link">
                <div>Pengaturan LMS</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.ai-settings') ? 'active' : '' }}">
            <a href="{{ route('admin.ai-settings.index') }}" class="menu-link">
                <div>Pengaturan AI</div>
            </a>
        </li>
    </ul>
</li>

<!-- ============================================ -->
<!-- UC04: Kelola Tahun Ajaran                    -->
<!-- UC05: Kelola Data Cabang                     -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Data Master</span>
</li>

<!-- UC04: Kelola Tahun Ajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.tahun-ajaran') ? 'active' : '' }}">
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Tahun Ajaran</div>
    </a>
</li>

<!-- UC05: Kelola Data Cabang -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.cabang') ? 'active' : '' }}">
    <a href="{{ route('admin.cabang.index') }}" class="menu-link">
        <i class="menu-icon fas fa-building"></i>
        <div>Manajemen Cabang</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC06: Kelola Data Kelas & Penugasan          -->
<!-- (Kelas, Wali Kelas, Guru Pengajar, Siswa)    -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Data Akademik</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.kelas') || Str::startsWith($currentRoute, 'admin.wali-kelas') || Str::startsWith($currentRoute, 'admin.guru-pengajar') || Str::startsWith($currentRoute, 'admin.manajemen-siswa') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Data Kelas & Penugasan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.kelas') ? 'active' : '' }}">
            <a href="{{ route('admin.kelas.index') }}" class="menu-link">
                <div>Data Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.wali-kelas') ? 'active' : '' }}">
            <a href="{{ route('admin.wali-kelas.index') }}" class="menu-link">
                <div>Data Wali Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.guru-pengajar') ? 'active' : '' }}">
            <a href="{{ route('admin.guru-pengajar.index') }}" class="menu-link">
                <div>Data Guru Pengajar</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.manajemen-siswa') ? 'active' : '' }}">
            <a href="{{ route('admin.manajemen-siswa.index') }}" class="menu-link">
                <div>Manajemen Siswa</div>
            </a>
        </li>
    </ul>
</li>

<!-- UC07: Kelola Mata Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.mata-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-book"></i>
        <div>Mata Pelajaran</div>
    </a>
</li>

<!-- UC08: Kelola Jadwal Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.jadwal-pelajaran') || Str::startsWith($currentRoute, 'admin.pengaturan-istirahat') ? 'active' : '' }}">
    <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC11: Kelola Tagihan & Pembayaran            -->
<!-- (Tagihan, Tunggakan, Pembayaran, Config)     -->
<!-- UC12: Lihat Laporan Keuangan                 -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Keuangan</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.tagihan') || Str::startsWith($currentRoute, 'admin.keuangan.pembayaran') || Str::startsWith($currentRoute, 'admin.keuangan.info-pembayaran') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-file-invoice-dollar"></i>
        <div>Tagihan & Pembayaran</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.tagihan') && !Str::contains($currentRoute, 'carryover') ? 'active' : '' }}">
            <a href="{{ route('admin.keuangan.tagihan.index') }}" class="menu-link">
                <div>Tagihan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::contains($currentRoute, 'admin.keuangan.tagihan.carryover') ? 'active' : '' }}">
            <a href="{{ route('admin.keuangan.tagihan.carryover') }}" class="menu-link">
                <div>Tarik Tunggakan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.pembayaran') ? 'active' : '' }}">
            <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="menu-link">
                <div>Pembayaran</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.info-pembayaran') ? 'active' : '' }}">
            <a href="{{ route('admin.keuangan.info-pembayaran.index') }}" class="menu-link">
                <div>Config Pembayaran</div>
            </a>
        </li>
    </ul>
</li>

<!-- UC12: Lihat Laporan Keuangan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.laporan') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.laporan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Laporan Keuangan</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC14: Validasi Akses Ujian dan Rapor         -->
<!-- UC15: Memproses Dispensasi Keuangan          -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi & Dispensasi</span>
</li>

<!-- UC14: Validasi Akses Ujian dan Rapor -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-circle"></i>
        <div>Validasi Ujian & Rapor</div>
    </a>
</li>

<!-- UC16: Memproses Dispensasi Kenaikan Kelas -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.keuangan.promotion.validation') ? 'active' : '' }}">
    <a href="{{ route('admin.keuangan.promotion.validation.index') }}" class="menu-link">
        <i class="menu-icon fas fa-hand-holding-usd"></i>
        <div>Validasi Dispensasi</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC17: Kelola Pengaturan Kenaikan             -->
<!-- (KKM + Pengaturan Kenaikan)                  -->
<!-- UC18: Proses Eksekusi Kenaikan Kelas         -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Kenaikan Kelas</span>
</li>

<li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.kkm.index' || $currentRoute == 'admin.akademik.promotion.settings.index' ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-cogs"></i>
        <div>Pengaturan Kenaikan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.kkm.index' ? 'active' : '' }}">
            <a href="{{ route('admin.akademik.promotion.kkm.index') }}" class="menu-link">
                <div>Pengaturan KKM</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.settings.index' ? 'active' : '' }}">
            <a href="{{ route('admin.akademik.promotion.settings.index') }}" class="menu-link">
                <div>Pengaturan Kenaikan</div>
            </a>
        </li>
    </ul>
</li>

<!-- UC18: Proses Eksekusi Kenaikan Kelas -->
<li class="menu-item {{ $currentRoute == 'admin.akademik.promotion.report' ? 'active' : '' }}">
    <a href="{{ route('admin.akademik.promotion.report') }}" class="menu-link">
        <i class="menu-icon fas fa-tasks"></i>
        <div>Proses & Rekap</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC35: Monitoring Sistem Terpadu              -->
<!-- UC36: Kelola Laporan & Catatan               -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring & Analitik</span>
</li>

<!-- UC35: Monitoring Sistem Terpadu -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'admin.monitoring') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Monitoring Sistem</div>
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

<!-- UC36: Kelola Laporan & Catatan -->
<li class="menu-item {{ (Str::startsWith($currentRoute, 'admin.laporan') && !Str::startsWith($currentRoute, 'admin.keuangan.laporan')) || Str::startsWith($currentRoute, 'admin.catatan') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Laporan & Catatan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.laporan') && !Str::startsWith($currentRoute, 'admin.keuangan.laporan') ? 'active' : '' }}">
            <a href="{{ route('admin.laporan.index') }}" class="menu-link">
                <div>Laporan</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'admin.catatan') ? 'active' : '' }}">
            <a href="{{ route('admin.catatan.index') }}" class="menu-link">
                <div>Catatan</div>
            </a>
        </li>
    </ul>
</li>
