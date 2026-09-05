{{--
    Navigasi CleanFlow untuk Wakil Kepala Sekolah.
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

{{-- Dashboard --}}
<li class="menu-item {{ $currentRoute == 'waka.dashboard' ? 'active' : '' }}">
    <a href="{{ route('waka.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC04: Kelola Tahun Ajaran                    --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Manajemen Akademik</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.tahun-ajaran') ? 'active' : '' }}">
    <a href="{{ route('waka.tahun-ajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Tahun Ajaran</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC06: Kelola Data Kelas & Penugasan          --}}
{{-- (Kelas, Wali Kelas, Guru Pengajar, Siswa)    --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Data Akademik</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kelas') || Str::startsWith($currentRoute, 'waka.wali-kelas') || Str::startsWith($currentRoute, 'waka.guru-pengajar') || Str::startsWith($currentRoute, 'waka.manajemen-siswa') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chalkboard-teacher"></i>
        <div>Data Kelas & Penugasan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kelas') ? 'active' : '' }}">
            <a href="{{ route('waka.kelas.index') }}" class="menu-link">
                <div>Data Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.wali-kelas') ? 'active' : '' }}">
            <a href="{{ route('waka.wali-kelas.index') }}" class="menu-link">
                <div>Data Wali Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.guru-pengajar') ? 'active' : '' }}">
            <a href="{{ route('waka.guru-pengajar.index') }}" class="menu-link">
                <div>Data Guru Pengajar</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.manajemen-siswa') ? 'active' : '' }}">
            <a href="{{ route('waka.manajemen-siswa.index') }}" class="menu-link">
                <div>Manajemen Siswa</div>
            </a>
        </li>
    </ul>
</li>

{{-- UC07: Kelola Mata Pelajaran --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.mata-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('waka.mata-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-book"></i>
        <div>Mata Pelajaran</div>
    </a>
</li>

{{-- UC08: Kelola Jadwal Pelajaran --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.jadwal-pelajaran') ? 'active' : '' }}">
    <a href="{{ route('waka.jadwal-pelajaran.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC17: Kelola Pengaturan Kenaikan             --}}
{{-- (KKM + Pengaturan Kenaikan)                  --}}
{{-- UC18: Proses Eksekusi Kenaikan Kelas         --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Kenaikan Kelas</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kenaikan-kelas.kkm') || Str::startsWith($currentRoute, 'waka.kenaikan-kelas.settings') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-cogs"></i>
        <div>Pengaturan Kenaikan</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kenaikan-kelas.kkm') ? 'active' : '' }}">
            <a href="{{ route('waka.kenaikan-kelas.kkm.index') }}" class="menu-link">
                <div>Pengaturan KKM</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kenaikan-kelas.settings') ? 'active' : '' }}">
            <a href="{{ route('waka.kenaikan-kelas.settings.index') }}" class="menu-link">
                <div>Pengaturan Kenaikan</div>
            </a>
        </li>
    </ul>
</li>

{{-- UC18: Proses Eksekusi Kenaikan Kelas --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.kenaikan-kelas.report') ? 'active' : '' }}">
    <a href="{{ route('waka.kenaikan-kelas.report') }}" class="menu-link">
        <i class="menu-icon fas fa-tasks"></i>
        <div>Proses & Rekap</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC35: Monitoring Sistem Terpadu              --}}
{{-- UC36: Kelola Laporan & Catatan               --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Monitoring & Analitik</span>
</li>

{{-- UC35: Monitoring Sistem Terpadu --}}
<li class="menu-item {{ $currentRoute == 'waka.monitoring.wali-kelas' || $currentRoute == 'waka.monitoring.guru-pengajar' || $currentRoute == 'waka.monitoring.siswa' || Str::startsWith($currentRoute, 'waka.monitoring.lms') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Monitoring Sistem</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'waka.monitoring.wali-kelas' ? 'active' : '' }}">
            <a href="{{ route('waka.monitoring.wali-kelas') }}" class="menu-link">
                <div>Wali Kelas</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'waka.monitoring.guru-pengajar' ? 'active' : '' }}">
            <a href="{{ route('waka.monitoring.guru-pengajar') }}" class="menu-link">
                <div>Guru Pengajar</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'waka.monitoring.siswa' ? 'active' : '' }}">
            <a href="{{ route('waka.monitoring.siswa') }}" class="menu-link">
                <div>Siswa</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'waka.monitoring.lms') ? 'active' : '' }}">
            <a href="{{ route('waka.monitoring.lms.index') }}" class="menu-link">
                <div>Monitoring LMS</div>
            </a>
        </li>
    </ul>
</li>

{{-- UC36: Kelola Laporan & Catatan (Waka hanya punya Catatan) --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Komunikasi</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'waka.catatan') ? 'active' : '' }}">
    <a href="{{ route('waka.catatan.index') }}" class="menu-link">
        <i class="menu-icon fas fa-sticky-note"></i>
        <div>Catatan</div>
    </a>
</li>
