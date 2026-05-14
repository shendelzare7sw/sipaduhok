@extends('layouts.sneat')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')
@section('page-subtitle', 'Ringkasan aktivitas mengajar hari ini')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --g-primary: #4361ee;
        --g-success: #10b981;
        --g-warning: #f59e0b;
        --g-danger: #ef4444;
        --g-info: #06b6d4;
        --g-purple: #8b5cf6;
        --g-surface: #ffffff;
        --g-bg: #f8fafc;
        --g-border: #e2e8f0;
        --g-text: #1e293b;
        --g-muted: #64748b;
        --g-radius: 12px;
    }
    .g-card {
        background: var(--g-surface);
        border: 1px solid var(--g-border);
        border-radius: var(--g-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .g-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .g-card-header {
        background: transparent;
        border-bottom: 1px solid var(--g-border);
        padding: 1.15rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .g-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--g-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Stat Cards */
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
        color: var(--g-text);
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }
    .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--g-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-footer {
        margin: 0 1.5rem;
        padding: 0.85rem 0;
        border-top: 1px dashed var(--g-border);
        font-size: 0.8rem;
        color: var(--g-muted);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Jadwal List */
    .jadwal-list { padding: 0; margin: 0; list-style: none; }
    .jadwal-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid var(--g-border);
        transition: background 0.15s ease;
    }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-item:hover { background: var(--g-bg); }
    .jadwal-time {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--g-primary);
        white-space: nowrap;
        min-width: 90px;
        flex-shrink: 0;
    }
    .jadwal-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--g-primary);
        flex-shrink: 0;
        opacity: 0.5;
    }
    .jadwal-body { flex: 1; min-width: 0; }
    .jadwal-mapel {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--g-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .jadwal-kelas {
        font-size: 0.78rem;
        color: var(--g-muted);
    }

    /* Kelas Grid */
    .kelas-grid { padding: 0; margin: 0; list-style: none; }
    .kelas-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid var(--g-border);
        transition: background 0.15s ease;
    }
    .kelas-item:last-child { border-bottom: none; }
    .kelas-item:hover { background: var(--g-bg); }
    .kelas-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        background: #eff6ff;
        color: var(--g-primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        font-weight: 700;
    }
    .kelas-body { flex: 1; min-width: 0; }
    .kelas-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--g-text);
    }
    .kelas-meta {
        font-size: 0.78rem;
        color: var(--g-muted);
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* Quick Links */
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
        border: 1px solid var(--g-border);
        background: var(--g-surface);
        color: var(--g-text);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
        gap: 0.65rem;
    }
    .quick-link-item:hover {
        background: var(--g-bg);
        border-color: var(--g-primary);
        color: var(--g-primary);
    }
    .quick-link-item i {
        font-size: 1.4rem;
        color: var(--g-muted);
        transition: color 0.2s ease;
    }
    .quick-link-item:hover i { color: var(--g-primary); }
    .quick-link-text { font-size: 0.8rem; font-weight: 600; line-height: 1.3; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: var(--g-muted);
    }
    .empty-state i { font-size: 2rem; opacity: 0.3; margin-bottom: 0.75rem; }
    .empty-state-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.2rem; }
    .empty-state-desc { font-size: 0.82rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-value { font-size: 1.35rem; }
        .stat-widget { padding: 1.15rem; gap: 0.85rem; }
        .stat-icon-box { width: 40px; height: 40px; font-size: 1.1rem; }
        .stat-footer { margin: 0 1.15rem; font-size: 0.75rem; }
        .jadwal-item { padding: 0.75rem 1.15rem; gap: 0.75rem; }
        .jadwal-time { font-size: 0.75rem; min-width: 75px; }
        .kelas-item { padding: 0.75rem 1.15rem; gap: 0.75rem; }
        .kelas-icon { width: 36px; height: 36px; font-size: 0.85rem; }
        .quick-links-grid { padding: 1rem; gap: 0.6rem; }
        .quick-link-item { padding: 0.85rem 0.5rem; }
        .quick-link-item i { font-size: 1.2rem; }
        .quick-link-text { font-size: 0.72rem; }
        .g-card-header { padding: 1rem 1.15rem; }
        .g-card-title { font-size: 0.9rem; }
    }
    @media (max-width: 480px) {
        .stat-value { font-size: 1.15rem; }
        .stat-label { font-size: 0.7rem; }
        .stat-icon-box { width: 36px; height: 36px; font-size: 1rem; }
    }
</style>
@endsection

@section('content')

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="g-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $kelasYangDiajar->count() ?? 0 }}</div>
                        <div class="stat-label">Kelas Diampu</div>
                    </div>
                    <div class="stat-icon-box" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Kelas aktif</span>
                    <i class="fas fa-chalkboard opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="g-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $totalSiswa ?? 0 }}</div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                    <div class="stat-icon-box" style="color: #10b981; background: #ecfdf5;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Siswa yang diajar</span>
                    <i class="fas fa-users opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="g-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $jadwalHariIni->count() ?? 0 }}</div>
                        <div class="stat-label">Jadwal Hari Ini</div>
                    </div>
                    <div class="stat-icon-box" style="color: #f59e0b; background: #fffbeb;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>{{ now()->locale('id')->translatedFormat('l, d M Y') }}</span>
                    <i class="fas fa-clock opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">

        <!-- Left: Jadwal Mengajar -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- Jadwal Hari Ini -->
            <div class="g-card">
                <div class="g-card-header">
                    <h5 class="g-card-title">
                        <i class="fas fa-calendar-day text-warning"></i> Jadwal Mengajar
                        <span class="badge bg-label-primary ms-1" style="font-size: 0.7rem;">{{ now()->locale('id')->translatedFormat('l') }}</span>
                    </h5>
                    <a href="{{ route('guru.jadwal.index') }}" class="text-primary fw-semibold text-decoration-none" style="font-size: 0.8rem;">
                        Jadwal Lengkap <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @if(isset($jadwalHariIni) && $jadwalHariIni->count())
                    <ul class="jadwal-list">
                        @foreach($jadwalHariIni as $jadwal)
                            <li class="jadwal-item">
                                <div class="jadwal-time">
                                    {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}
                                </div>
                                <div class="jadwal-dot"></div>
                                <div class="jadwal-body">
                                    <div class="jadwal-mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                    <div class="jadwal-kelas">Kelas {{ $jadwal->kelas->nama_kelas }}</div>
                                </div>
                                <a href="{{ route('guru.lms.dashboard', [$jadwal->kelas->id, $jadwal->link_mapel_id]) }}"
                                   class="btn btn-sm btn-outline-primary px-3" style="font-size: 0.75rem; font-weight: 600; border-radius: 6px; white-space: nowrap;">
                                    <i class="fas fa-door-open me-1"></i>Masuk LMS
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="fas fa-coffee d-block"></i>
                        <div class="empty-state-title">Tidak Ada Jadwal</div>
                        <div class="empty-state-desc">Tidak ada jadwal mengajar hari ini. Istirahat sejenak!</div>
                    </div>
                @endif
            </div>

            <!-- Kelas yang Diampu -->
            <div class="g-card">
                <div class="g-card-header">
                    <h5 class="g-card-title">
                        <i class="fas fa-chalkboard-teacher text-info"></i> Kelas yang Diampu
                    </h5>
                    <a href="{{ route('guru.kelas.index') }}" class="text-primary fw-semibold text-decoration-none" style="font-size: 0.8rem;">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @if(isset($kelasYangDiajar) && $kelasYangDiajar->count())
                    <ul class="kelas-grid">
                        @foreach($kelasYangDiajar as $item)
                            <li class="kelas-item">
                                <div class="kelas-icon">{{ $item['kelas']->nama_kelas }}</div>
                                <div class="kelas-body">
                                    <div class="kelas-name">Kelas {{ $item['kelas']->nama_kelas }}</div>
                                    <div class="kelas-meta">
                                        <span><i class="fas fa-users me-1"></i>{{ $item['jumlah_siswa'] }} siswa</span>
                                        <span>·</span>
                                        <span><i class="fas fa-book me-1"></i>{{ $item['jumlah_mapel'] }} mapel</span>
                                    </div>
                                </div>
                                <a href="{{ route('guru.kelas.mapel', $item['kelas']->id) }}"
                                   class="btn btn-sm btn-outline-info px-3" style="font-size: 0.75rem; font-weight: 600; border-radius: 6px; white-space: nowrap;">
                                    <i class="fas fa-arrow-right me-1"></i>Kelola
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox d-block"></i>
                        <div class="empty-state-title">Belum Ada Kelas</div>
                        <div class="empty-state-desc">Belum ada kelas yang diampu saat ini.</div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right: Quick Links -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <div class="g-card flex-grow-1">
                <div class="g-card-header">
                    <h5 class="g-card-title">
                        <i class="fas fa-bolt text-warning"></i> Akses Cepat
                    </h5>
                </div>
                <div class="quick-links-grid">
                    <a href="{{ route('guru.kelas.index') }}" class="quick-link-item">
                        <i class="fas fa-list text-primary"></i>
                        <span class="quick-link-text">Semua<br>Kelas</span>
                    </a>
                    <a href="{{ route('guru.jadwal.index') }}" class="quick-link-item">
                        <i class="fas fa-calendar-alt text-success"></i>
                        <span class="quick-link-text">Jadwal<br>Mengajar</span>
                    </a>
                    @if(isset($kelasYangDiajar) && $kelasYangDiajar->count() > 0)
                        @php $firstKelas = $kelasYangDiajar->first(); @endphp
                        <a href="{{ route('guru.kelas.mapel', $firstKelas['kelas']->id) }}" class="quick-link-item">
                            <i class="fas fa-chalkboard text-warning"></i>
                            <span class="quick-link-text">Kelas<br>Pertama</span>
                        </a>
                    @else
                        <a href="{{ route('guru.kelas.index') }}" class="quick-link-item">
                            <i class="fas fa-chalkboard text-warning"></i>
                            <span class="quick-link-text">Kelola<br>Kelas</span>
                        </a>
                    @endif
                    <a href="{{ route('guru.dashboard') }}" class="quick-link-item">
                        <i class="fas fa-sync-alt text-info"></i>
                        <span class="quick-link-text">Refresh<br>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection