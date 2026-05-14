@extends('layouts.sneat')

@section('title', 'Dashboard Wali Kelas')
@section('page-title', 'Dashboard Wali Kelas')
@section('page-subtitle', 'Kelola kelas dan siswa Anda')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
<style>
    /* ===================== BASE TOKENS ===================== */
    :root {
        --wk-primary: #4361ee;
        --wk-success: #10b981;
        --wk-warning: #f59e0b;
        --wk-danger: #ef4444;
        --wk-info: #06b6d4;
        --wk-purple: #8b5cf6;
        --wk-surface: #ffffff;
        --wk-bg: #f8fafc;
        --wk-border: #e2e8f0;
        --wk-text: #1e293b;
        --wk-muted: #64748b;
        --wk-radius: 12px;
    }

    /* ===================== CARD BASE ===================== */
    .wk-card {
        background: var(--wk-surface);
        border: 1px solid var(--wk-border);
        border-radius: var(--wk-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .wk-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .wk-card-header {
        background: transparent;
        border-bottom: 1px solid var(--wk-border);
        padding: 1.15rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wk-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--wk-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ===================== STAT CARDS ===================== */
    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }
    .stat-icon-box {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .stat-details { flex-grow: 1; min-width: 0; }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--wk-text);
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }
    .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--wk-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-footer {
        margin: 0 1.5rem;
        padding: 0.85rem 0;
        border-top: 1px dashed var(--wk-border);
        font-size: 0.8rem;
        color: var(--wk-muted);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* ===================== CLASS INFO BANNER ===================== */
    .class-info-banner {
        background: linear-gradient(135deg, #4361ee 0%, #3b82f6 50%, #06b6d4 100%);
        border-radius: var(--wk-radius);
        padding: 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .class-info-banner::after {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .class-info-banner::before {
        content: '';
        position: absolute;
        bottom: -40px; left: 40%;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .class-info-icon {
        width: 56px; height: 56px;
        background: rgba(255,255,255,0.2);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    .class-info-details { position: relative; z-index: 1; min-width: 0; flex: 1; }
    .class-info-name { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.15rem; }
    .class-info-meta {
        font-size: 0.85rem;
        opacity: 0.85;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .class-info-meta span { display: flex; align-items: center; gap: 0.35rem; }

    /* ===================== PRESENSI RING ===================== */
    .presensi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        padding: 1.25rem;
    }
    .presensi-item {
        text-align: center;
        padding: 1rem 0.5rem;
        border-radius: 10px;
        border: 1px solid var(--wk-border);
        background: var(--wk-bg);
        transition: transform 0.15s ease;
    }
    .presensi-item:hover { transform: scale(1.03); }
    .presensi-number {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }
    .presensi-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--wk-muted);
    }

    /* ===================== JADWAL LIST ===================== */
    .jadwal-list { padding: 0; margin: 0; list-style: none; }
    .jadwal-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid var(--wk-border);
        transition: background 0.15s ease;
    }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-item:hover { background: #f8fafc; }
    .jadwal-time {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--wk-primary);
        white-space: nowrap;
        min-width: 90px;
        flex-shrink: 0;
    }
    .jadwal-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--wk-primary);
        flex-shrink: 0;
        opacity: 0.5;
    }
    .jadwal-body { flex: 1; min-width: 0; }
    .jadwal-mapel {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--wk-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .jadwal-guru {
        font-size: 0.78rem;
        color: var(--wk-muted);
    }

    /* ===================== QUICK LINKS ===================== */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.85rem;
        padding: 1.25rem;
    }
    .quick-link-item {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 1.15rem 0.75rem;
        border-radius: 10px;
        border: 1px solid var(--wk-border);
        background: var(--wk-surface);
        color: var(--wk-text);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
        gap: 0.65rem;
        position: relative;
    }
    .quick-link-item:hover {
        background: var(--wk-bg);
        border-color: var(--wk-primary);
        color: var(--wk-primary);
    }
    .quick-link-item i {
        font-size: 1.4rem;
        color: var(--wk-muted);
        transition: color 0.2s ease;
    }
    .quick-link-item:hover i { color: var(--wk-primary); }
    .quick-link-text {
        font-size: 0.8rem; font-weight: 600; line-height: 1.3;
    }

    /* ===================== EMPTY STATE ===================== */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: var(--wk-muted);
    }
    .empty-state i { font-size: 2rem; opacity: 0.3; margin-bottom: 0.75rem; }
    .empty-state-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.2rem; }
    .empty-state-desc { font-size: 0.82rem; }

    /* ===================== ERROR STATE ===================== */
    .error-card {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--wk-surface);
        border: 1px solid var(--wk-border);
        border-radius: var(--wk-radius);
        border-left: 4px solid var(--wk-danger);
    }
    .error-card i { font-size: 3.5rem; color: #e2e8f0; margin-bottom: 1rem; }
    .error-card h4 { font-weight: 700; color: var(--wk-danger); margin-bottom: 0.5rem; }
    .error-card p { color: var(--wk-muted); font-size: 0.9rem; }

    /* ===================== STATUS PROGRESS ===================== */
    .status-list { padding: 0; margin: 0; list-style: none; }
    .status-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--wk-border);
    }
    .status-item:last-child { border-bottom: none; }
    .status-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .status-body { flex: 1; min-width: 0; }
    .status-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.4rem;
    }
    .status-label { font-size: 0.82rem; font-weight: 600; color: var(--wk-text); }
    .status-value { font-size: 0.78rem; font-weight: 700; }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .class-info-banner { padding: 1.25rem; gap: 1rem; }
        .class-info-icon { width: 44px; height: 44px; font-size: 1.2rem; border-radius: 10px; }
        .class-info-name { font-size: 1.1rem; }
        .class-info-meta { font-size: 0.78rem; gap: 0.65rem; }

        .stat-value { font-size: 1.35rem; }
        .stat-widget { padding: 1.15rem; gap: 0.85rem; }
        .stat-icon-box { width: 40px; height: 40px; font-size: 1.1rem; }
        .stat-footer { margin: 0 1.15rem; font-size: 0.75rem; }

        .presensi-grid { gap: 0.5rem; padding: 1rem; }
        .presensi-item { padding: 0.75rem 0.35rem; }
        .presensi-number { font-size: 1.25rem; }
        .presensi-label { font-size: 0.65rem; }

        .jadwal-item { padding: 0.75rem 1.15rem; gap: 0.75rem; }
        .jadwal-time { font-size: 0.75rem; min-width: 75px; }
        .jadwal-mapel { font-size: 0.85rem; }

        .quick-links-grid { padding: 1rem; gap: 0.6rem; }
        .quick-link-item { padding: 0.85rem 0.5rem; }
        .quick-link-item i { font-size: 1.2rem; }
        .quick-link-text { font-size: 0.72rem; }

        .wk-card-header { padding: 1rem 1.15rem; }
        .wk-card-title { font-size: 0.9rem; }
    }

    @media (max-width: 480px) {
        .stat-value { font-size: 1.15rem; }
        .stat-label { font-size: 0.7rem; }
        .stat-icon-box { width: 36px; height: 36px; font-size: 1rem; }

        .class-info-banner { flex-direction: column; text-align: center; gap: 0.75rem; }
        .class-info-meta { justify-content: center; }

        .presensi-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')

    @if(isset($message))
        <div class="error-card">
            <i class="fas fa-user-slash d-block"></i>
            <h4>Akses Terbatas</h4>
            <p>{{ $message }}</p>
        </div>
    @elseif(!$kelas)
        <div class="error-card">
            <i class="fas fa-user-slash d-block"></i>
            <h4>Akses Terbatas</h4>
            <p>Anda belum ditugaskan sebagai wali kelas. Silakan hubungi bagian Admin Kurikulum.</p>
        </div>
    @else

        <!-- Class Info Banner -->
        <div class="class-info-banner">
            <div class="class-info-icon">
                <i class="fas fa-school"></i>
            </div>
            <div class="class-info-details">
                <div class="class-info-name">Kelas {{ $kelas->nama_kelas }}</div>
                <div class="class-info-meta">
                    <span><i class="fas fa-layer-group"></i> {{ strtoupper($kelas->jenjang) }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4">
            <!-- Total Siswa -->
            <div class="col-6 col-xl-3">
                <div class="wk-card">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $totalSiswa }}</div>
                            <div class="stat-label">Total Siswa</div>
                        </div>
                        <div class="stat-icon-box" style="color: #3b82f6; background: #eff6ff;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span>Kelas {{ $kelas->nama_kelas }}</span>
                        <i class="fas fa-users opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Hadir Hari Ini -->
            <div class="col-6 col-xl-3">
                <div class="wk-card">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $presensiStats['hadir'] }}</div>
                            <div class="stat-label">Hadir Hari Ini</div>
                        </div>
                        <div class="stat-icon-box" style="color: #10b981; background: #ecfdf5;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @php $persenHadir = $totalSiswa > 0 ? round(($presensiStats['hadir'] / $totalSiswa) * 100) : 0; @endphp
                        <span class="{{ $persenHadir >= 80 ? 'text-success' : ($persenHadir >= 50 ? 'text-warning' : 'text-danger') }}">
                            <i class="fas fa-chart-line me-1"></i>{{ $persenHadir }}% kehadiran
                        </span>
                        <i class="fas fa-clock opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Izin Pending -->
            <div class="col-6 col-xl-3">
                <div class="wk-card" style="{{ $izinMenungguValidasi > 0 ? 'border-color: #fde68a;' : '' }}">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $izinMenungguValidasi }}</div>
                            <div class="stat-label">Izin Pending</div>
                        </div>
                        <div class="stat-icon-box" style="color: #f59e0b; background: #fffbeb;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($izinMenungguValidasi > 0)
                            <span class="text-warning fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>Perlu Validasi</span>
                        @else
                            <span class="text-success"><i class="fas fa-check me-1"></i>Semua Clear</span>
                        @endif
                        <i class="fas fa-bell opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Rapor Draft -->
            <div class="col-6 col-xl-3">
                <div class="wk-card">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $raporBelumSelesai }}</div>
                            <div class="stat-label">Rapor Draft</div>
                        </div>
                        <div class="stat-icon-box" style="color: #8b5cf6; background: #f5f3ff;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($raporBelumSelesai > 0)
                            <span class="text-warning"><i class="fas fa-edit me-1"></i>Belum selesai</span>
                        @else
                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Semua selesai</span>
                        @endif
                        <i class="fas fa-file opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout Grid -->
        <div class="row g-4 mb-4">

            <!-- Left Column: Jadwal & Presensi -->
            <div class="col-lg-8 d-flex flex-column gap-4">

                <!-- Jadwal Pelajaran Hari Ini -->
                <div class="wk-card">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-calendar-day text-warning"></i> Jadwal Hari Ini
                            <span class="badge bg-label-primary ms-1" style="font-size: 0.7rem;">{{ now()->locale('id')->translatedFormat('l') }}</span>
                        </h5>
                        <a href="{{ route('wali.jadwal.index') }}" class="text-primary fw-semibold text-decoration-none" style="font-size: 0.8rem;">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @if($jadwalHariIni->count() > 0)
                        <ul class="jadwal-list">
                            @foreach($jadwalHariIni as $jadwal)
                                <li class="jadwal-item">
                                    <div class="jadwal-time">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </div>
                                    <div class="jadwal-dot"></div>
                                    <div class="jadwal-body">
                                        <div class="jadwal-mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                        <div class="jadwal-guru">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : 'Belum ditentukan' }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-coffee d-block"></i>
                            <div class="empty-state-title">Tidak Ada Jadwal</div>
                            <div class="empty-state-desc">Tidak ada pelajaran terjadwal untuk hari ini.</div>
                        </div>
                    @endif
                </div>

                <!-- Rekap Presensi Hari Ini -->
                <div class="wk-card">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-clipboard-list text-info"></i> Rekap Presensi
                        </h5>
                        <span class="text-muted" style="font-size: 0.8rem;">{{ now()->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="presensi-grid">
                        <div class="presensi-item">
                            <div class="presensi-number text-success">{{ $presensiStats['hadir'] }}</div>
                            <div class="presensi-label">Hadir</div>
                        </div>
                        <div class="presensi-item">
                            <div class="presensi-number text-warning">{{ $presensiStats['sakit'] }}</div>
                            <div class="presensi-label">Sakit</div>
                        </div>
                        <div class="presensi-item">
                            <div class="presensi-number text-primary">{{ $presensiStats['izin'] }}</div>
                            <div class="presensi-label">Izin</div>
                        </div>
                        <div class="presensi-item">
                            <div class="presensi-number text-danger">{{ $presensiStats['alpha'] }}</div>
                            <div class="presensi-label">Alpha</div>
                        </div>
                    </div>
                    @php $totalPresensi = array_sum($presensiStats); @endphp
                    @if($totalPresensi > 0 && $totalSiswa > 0)
                        <div class="px-4 pb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.78rem; color: var(--wk-muted);">
                                <span>Progress Input ({{ $totalPresensi }}/{{ $totalSiswa }})</span>
                                <span class="fw-bold">{{ round(($totalPresensi / $totalSiswa) * 100) }}%</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($presensiStats['hadir'] / $totalSiswa) * 100 }}%; border-radius: 3px 0 0 3px;"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($presensiStats['sakit'] / $totalSiswa) * 100 }}%;"></div>
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($presensiStats['izin'] / $totalSiswa) * 100 }}%;"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($presensiStats['alpha'] / $totalSiswa) * 100 }}%; border-radius: 0 3px 3px 0;"></div>
                            </div>
                        </div>
                    @elseif($totalPresensi == 0)
                        <div class="text-center pb-3">
                            <a href="{{ route('wali.presensi.index') }}" class="btn btn-sm btn-outline-primary px-4" style="font-size: 0.8rem; font-weight: 600; border-radius: 8px;">
                                <i class="fas fa-clipboard-check me-1"></i> Input Presensi Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Status Kelas -->
                @php
                    $persenHadir = $totalSiswa > 0 ? round(($presensiStats['hadir'] / $totalSiswa) * 100) : 0;
                    $persenRapor = $totalSiswa > 0 ? round((($totalSiswa - $raporBelumSelesai) / $totalSiswa) * 100) : 0;
                @endphp
                <div class="wk-card flex-grow-1">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-chart-bar text-purple"></i> Status Kelas
                        </h5>
                    </div>
                    <ul class="status-list">
                        <li class="status-item">
                            <div class="status-icon" style="background: #ecfdf5; color: #10b981;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Kehadiran Hari Ini</span>
                                    <span class="status-value {{ $persenHadir >= 80 ? 'text-success' : ($persenHadir >= 50 ? 'text-warning' : 'text-danger') }}">{{ $persenHadir }}%</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px;">
                                    <div class="progress-bar {{ $persenHadir >= 80 ? 'bg-success' : ($persenHadir >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $persenHadir }}%; border-radius: 3px;"></div>
                                </div>
                            </div>
                        </li>
                        <li class="status-item">
                            <div class="status-icon" style="background: #f5f3ff; color: #8b5cf6;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Penyelesaian Rapor</span>
                                    <span class="status-value {{ $persenRapor >= 100 ? 'text-success' : ($persenRapor >= 50 ? 'text-warning' : 'text-danger') }}">{{ $persenRapor }}%</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px;">
                                    <div class="progress-bar {{ $persenRapor >= 100 ? 'bg-success' : ($persenRapor >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $persenRapor }}%; border-radius: 3px;"></div>
                                </div>
                            </div>
                        </li>
                        <li class="status-item">
                            <div class="status-icon" style="background: #fffbeb; color: #f59e0b;">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Izin Menunggu</span>
                                    <span class="status-value {{ $izinMenungguValidasi == 0 ? 'text-success' : 'text-warning' }}">{{ $izinMenungguValidasi }} pengajuan</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px;">
                                    <div class="progress-bar {{ $izinMenungguValidasi == 0 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $izinMenungguValidasi > 0 ? 100 : 0 }}%; border-radius: 3px;"></div>
                                </div>
                            </div>
                        </li>
                        <li class="status-item">
                            <div class="status-icon" style="background: #eff6ff; color: #3b82f6;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Siswa Terdaftar</span>
                                    <span class="status-value text-primary">{{ $totalSiswa }} siswa</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px;">
                                    <div class="progress-bar bg-primary" style="width: 100%; border-radius: 3px;"></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Right Column: Aksi Cepat -->
            <div class="col-lg-4 d-flex flex-column gap-4">

                <!-- Quick Links -->
                <div class="wk-card flex-grow-1">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-bolt text-warning"></i> Akses Cepat
                        </h5>
                    </div>
                    <div class="quick-links-grid">
                        <a href="{{ route('wali.presensi.index') }}" class="quick-link-item">
                            <i class="fas fa-clipboard-check text-primary"></i>
                            <span class="quick-link-text">Input<br>Presensi</span>
                        </a>
                        <a href="{{ route('wali.presensi.validasi-izin') }}" class="quick-link-item">
                            <div class="position-relative">
                                <i class="fas fa-check-circle text-warning"></i>
                                @if($izinMenungguValidasi > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.6rem; padding: 0.3em 0.5em; transform: translate(-30%, -30%) !important;">
                                        {{ $izinMenungguValidasi > 99 ? '99+' : $izinMenungguValidasi }}
                                    </span>
                                @endif
                            </div>
                            <span class="quick-link-text">Validasi<br>Izin</span>
                        </a>
                        <a href="{{ route('wali.nilai.index') }}" class="quick-link-item">
                            <i class="fas fa-chart-line text-info"></i>
                            <span class="quick-link-text">Lihat<br>Nilai</span>
                        </a>
                        <a href="{{ route('wali.rapor.index') }}" class="quick-link-item">
                            <div class="position-relative">
                                <i class="fas fa-file-alt text-success"></i>
                                @if($raporBelumSelesai > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning shadow-sm" style="font-size: 0.6rem; padding: 0.3em 0.5em; transform: translate(-30%, -30%) !important; color: #fff;">
                                        {{ $raporBelumSelesai }}
                                    </span>
                                @endif
                            </div>
                            <span class="quick-link-text">Kelola<br>Rapor</span>
                        </a>
                        <a href="{{ route('wali.presensi.rekap-harian') }}" class="quick-link-item">
                            <i class="fas fa-calendar-day text-purple"></i>
                            <span class="quick-link-text">Rekap<br>Harian</span>
                        </a>
                        <a href="{{ route('wali.presensi.riwayat') }}" class="quick-link-item">
                            <i class="fas fa-history text-secondary"></i>
                            <span class="quick-link-text">Riwayat<br>Presensi</span>
                        </a>
                        <a href="{{ route('wali.jadwal.index') }}" class="quick-link-item">
                            <i class="fas fa-calendar-week text-warning"></i>
                            <span class="quick-link-text">Jadwal<br>Pelajaran</span>
                        </a>
                        <a href="{{ route('wali.promotion.prediction') }}" class="quick-link-item">
                            <i class="fas fa-chart-bar text-danger"></i>
                            <span class="quick-link-text">Prediksi<br>Kenaikan</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    @endif

@endsection
