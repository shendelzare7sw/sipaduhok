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
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.presensi') ? 'active' : '' }}">
    <a href="{{ route('wali.presensi.index') }}" class="menu-link">
        <i class="menu-icon fas fa-clipboard-check"></i>
        <div>Presensi Siswa</div>
    </a>
</li>

<!-- Nilai Siswa -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.nilai') ? 'active' : '' }}">
    <a href="{{ route('wali.nilai.index') }}" class="menu-link">
        <i class="menu-icon fas fa-chart-line"></i>
        <div>Nilai Siswa</div>
    </a>
</li>

<!-- Kelola Rapor -->
<li class="menu-item {{ Str::startsWith($currentRoute, 'wali.rapor') ? 'active' : '' }}">
    <a href="{{ route('wali.rapor.index') }}" class="menu-link">
        <i class="menu-icon fas fa-file-alt"></i>
        <div>Kelola Rapor</div>
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
