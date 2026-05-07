{{--
    Sidebar Menu untuk Wali Kelas Dashboard
    File: resources/views/wali-kelas/partials/sneat-sidebar-menu.blade.php

    Compatible dengan Sneat Template (Bootstrap 5)
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
    <div class="px-3 py-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 8px; margin: 8px 12px 8px;">
        <div style="color: rgba(255,255,255,0.8); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
            <i class="fas fa-school me-1"></i> Kelas Aktif
        </div>
        <div style="color: white; font-weight: 600; font-size: 14px;">
            {{ $selectedKelas->nama_kelas }}
        </div>
        <div style="color: rgba(255,255,255,0.7); font-size: 12px;">
            {{ $selectedKelas->cabang->nama_cabang ?? '' }} - {{ $selectedKelas->jenjang }}
        </div>
        <a href="{{ route('wali.pilih-kelas') }}" 
           style="display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; padding: 4px 12px; background: rgba(255,255,255,0.2); color: white; font-size: 12px; border-radius: 4px; text-decoration: none; transition: all 0.2s;">
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

<!-- Menu Header - Akademik -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Akademik</span>
</li>

<!-- Jadwal Pelajaran -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.jadwal') ? 'active' : '' }}">
    <a href="{{ route('wali.jadwal.index') }}" class="menu-link">
        <i class="menu-icon fas fa-calendar-week"></i>
        <div>Jadwal Pelajaran</div>
    </a>
</li>

<!-- Presensi Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.presensi') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-clipboard-check"></i>
        <div>Presensi Siswa</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'wali.presensi.index' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.index') }}" class="menu-link">
                <i class="fas fa-edit me-2" style="font-size: 10px;"></i>
                <div>Input Harian</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.presensi.validasi-izin' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.validasi-izin') }}" class="menu-link">
                <i class="fas fa-check-circle me-2" style="font-size: 10px;"></i>
                <div>Validasi Izin</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.presensi.rekap-harian' || $currentRoute == 'wali.presensi.show-harian' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.rekap-harian') }}" class="menu-link">
                <i class="fas fa-calendar-day me-2" style="font-size: 10px;"></i>
                <div>Rekap Harian</div>
            </a>
        </li>
        <li class="menu-item {{ $currentRoute == 'wali.presensi.riwayat' ? 'active' : '' }}">
            <a href="{{ route('wali.presensi.riwayat') }}" class="menu-link">
                <i class="fas fa-history me-2" style="font-size: 10px;"></i>
                <div>Riwayat & Edit</div>
            </a>
        </li>
    </ul>
</li>

<!-- Nilai Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.nilai') ? 'active' : '' }}">
    <a href="{{ route('wali.nilai.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Nilai Siswa</div>
    </a>
</li>

<!-- Kelola Rapor -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.rapor.') ? 'active' : '' }}">
    <a href="{{ route('wali.rapor.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Kelola Rapor</div>
    </a>
</li>

<!-- Rapor Pending Saya (lintas TA — termasuk dari kelas yang dulu pernah diwalikan) -->
<li class="menu-item {{ $currentRoute === 'wali.rapor-pending' ? 'active' : '' }}">
    <a href="{{ route('wali.rapor-pending') }}" class="menu-link">
        <i class="menu-icon fas fa-history"></i>
        <div>Rapor Pending Saya</div>
        @php
            try {
                $wkTp = \App\Models\TenagaPendidik::where('user_id', auth()->id())->first();
                $kelasIds = $wkTp ? \App\Models\WaliKelasAssignment::where('tenaga_pendidik_id', $wkTp->id)->pluck('kelas_id') : collect();
                $pendingCount = $kelasIds->isNotEmpty()
                    ? \App\Models\Rapor::whereIn('kelas_id', $kelasIds)
                        ->where(fn($q) => $q->where('status', 'draft')->orWhere('status_review_ketua', 'revisi'))
                        ->count()
                    : 0;
            } catch (\Throwable $e) {
                $pendingCount = 0;
            }
        @endphp
        @if($pendingCount > 0)
            <span class="badge bg-warning rounded-pill ms-auto">{{ $pendingCount }}</span>
        @endif
    </a>
</li>

<!-- Request Download Rapor -->
<li class="menu-item {{ $currentRoute == 'wali.rapor.request-download.index' ? 'active' : '' }}">
    <a href="{{ route('wali.rapor.request-download.index') }}" class="menu-link">
        <i class="menu-icon fas fa-download"></i>
        <div>Request Download</div>
        @php $pendingDownload = \App\Models\RequestDownloadRapor::where('status', 'menunggu')->whereHas('siswa', fn($q) => $q->where('kelas_id', session('selected_kelas_id')))->count(); @endphp
        @if($pendingDownload > 0)
            <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingDownload }}</span>
        @endif
    </a>
</li>

<!-- Menu Header - Kenaikan Kelas -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Kenaikan Kelas</span>
</li>

<!-- Prediksi Kenaikan -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.promotion.prediction') ? 'active' : '' }}">
    <a href="{{ route('wali.promotion.prediction') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-bar"></i>
        <div>Prediksi Kenaikan</div>
    </a>
</li>

<!-- Menu Header - Validasi -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Validasi</span>
</li>

<!-- Validasi Akses -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.validasi-akses') ? 'active' : '' }}">
    <a href="{{ route('wali.validasi-akses.index') }}" class="menu-link">
        <i class="menu-icon fas fa-check-double"></i>
        <div>Validasi Akses</div>
    </a>
</li>
