{{--
    Sidebar Menu untuk Wali Kelas Dashboard
    File: resources/views/wali-kelas/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Template (Bootstrap 5)
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();
    // Get selected kelas from session
    $selectedKelasId = session('wali_kelas_selected');
    $selectedKelas = null;
    $hasMultipleKelas = false;
    
    if ($selectedKelasId) {
        $selectedKelas = \App\Models\Kelas::with('cabang')->find($selectedKelasId);
    }
    
    // Check if wali has multiple kelas
    $tenagaPendidik = \App\Models\TenagaPendidik::where('user_id', auth()->id())->first();
    if ($tenagaPendidik) {
        $kelasCount = \App\Models\WaliKelasAssignment::where('tenaga_pendidik_id', $tenagaPendidik->id)->count();
        $hasMultipleKelas = $kelasCount > 1;
    }
@endphp

@if($selectedKelas && $hasMultipleKelas)
<!-- Current Class Indicator -->
<li class="menu-item">
    <div class="wali-active-class-card">
        <div class="wali-active-class-label">
            <i class="fas fa-school me-1"></i> Kelas Aktif
        </div>
        <div class="wali-active-class-title">
            {{ $selectedKelas->nama_kelas }}
        </div>
        <div class="wali-active-class-meta">
            {{ $selectedKelas->cabang->nama_cabang ?? '' }} - {{ $selectedKelas->jenjang }}
        </div>
        <a href="{{ route('wali.pilih-kelas') }}" 
           class="wali-active-class-switch">
            <i class="fas fa-exchange-alt"></i> Ganti Kelas
        </a>
    </div>
</li>
@endif

<!-- Dashboard -->
<li class="menu-item {{ $currentRoute == 'wali.dashboard' ? 'active' : '' }}">
    <a href="{{ route('wali.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

@if($hasMultipleKelas)
<!-- Pilih Kelas -->
<li class="menu-item {{ $currentRoute == 'wali.pilih-kelas' ? 'active' : '' }}">
    <a href="{{ route('wali.pilih-kelas') }}" class="menu-link">
        <i class="menu-icon fas fa-exchange-alt"></i>
        <div>Pilih Kelas</div>
    </a>
</li>
@endif

<!-- ============================================ -->
<!-- UC38: Melihat Informasi Akademik             -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Akademik</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.jadwal') ? 'active' : '' }}">
    <a href="{{ route('wali.jadwal.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC19: Kelola Presensi Siswa                  -->
<!-- (Input Harian, Validasi Izin, Rekap, Edit)   -->
<!-- ============================================ -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.presensi') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-clipboard-check"></i>
        <div>Kelola Presensi</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'wali.presensi.index' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.index') }}" class="menu-link">
                <i class="fas fa-edit me-2 fa-xs"></i>
                <div>Input Harian</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.presensi.validasi-izin' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.validasi-izin') }}" class="menu-link">
                <i class="fas fa-check-circle me-2 fa-xs"></i>
                <div>Validasi Izin</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.presensi.rekap-harian' || $currentRoute == 'wali.presensi.show-harian' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.rekap-harian') }}" class="menu-link">
                <i class="fas fa-calendar-day me-2 fa-xs"></i>
                <div>Rekap Harian</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.presensi.riwayat' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.riwayat') }}" class="menu-link">
                <i class="fas fa-history me-2 fa-xs"></i>
                <div>Riwayat & Edit</div>
            </a>
        </li>
    </ul>
</li>

<!-- ============================================ -->
<!-- UC20: Kelola Rapor Siswa                     -->
<!-- (Nilai, Rapor, Arsip dalam satu dropdown)    -->
<!-- UC22: Mengelola Permintaan Unduh Rapor       -->
<!-- ============================================ -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.nilai') || Str::startsWith($currentRoute, 'wali.rapor') || Str::startsWith($currentRoute, 'wali.arsip') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Kelola Rapor</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Str::startsWith($currentRoute, 'wali.nilai') ? 'active' : '' }}">
            <a href="{{ route('wali.nilai.index') }}" class="menu-link">
                <i class="fas fa-chart-line me-2 fa-xs"></i>
                <div>Nilai Siswa</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'wali.rapor.') && $currentRoute != 'wali.rapor.request-download.index' ? 'active' : '' }}">
            <a href="{{ route('wali.rapor.index') }}" class="menu-link">
                <i class="fas fa-file-alt me-2 fa-xs"></i>
                <div>Kelola Rapor</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'wali.arsip') ? 'active' : '' }}">
            <a href="{{ route('wali.arsip.index') }}" class="menu-link">
                <i class="fas fa-archive me-2 fa-xs"></i>
                <div>Arsip Kelas Saya</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.rapor.request-download.index' ? 'active' : '' }}">
            <a href="{{ route('wali.rapor.request-download.index') }}" class="menu-link">
                <i class="fas fa-download me-2 fa-xs"></i>
                <div>Permintaan Unduh</div>
                @php $pendingDownload = \App\Models\RequestDownloadRapor::where('status', 'menunggu')->whereHas('siswa', fn($q) => $q->where('kelas_id', session('selected_kelas_id')))->count(); @endphp
                @if($pendingDownload > 0)
                    <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingDownload }}</span>
                @endif
            </a>
        </li>
    </ul>
</li>

<!-- ============================================ -->
<!-- UC24: Melihat Prediksi Kenaikan Kelas        -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Kenaikan Kelas</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.promotion.prediction') ? 'active' : '' }}">
    <a href="{{ route('wali.promotion.prediction') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Prediksi Kenaikan</div>
    </a>
</li>

<!-- ============================================ -->
<!-- UC14: Validasi Akses Ujian dan Rapor          -->
<!-- ============================================ -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi</span>
</li>

<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('wali.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-double"></i>
        <div>Validasi Akses</div>
    </a>
</li>
