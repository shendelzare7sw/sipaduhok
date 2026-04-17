@extends('layouts.sneat')

@section('title', 'Dashboard SIA')
@section('page-title', 'Sistem Informasi Akademik')
@section('page-subtitle', 'Selamat datang, ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@section('styles')
<style>
    :root {
        --s-primary: #4361ee;
        --s-success: #10b981;
        --s-warning: #f59e0b;
        --s-danger: #ef4444;
        --s-info: #06b6d4;
        --s-purple: #8b5cf6;
        --s-surface: #ffffff;
        --s-bg: #f8fafc;
        --s-border: #e2e8f0;
        --s-text: #1e293b;
        --s-muted: #64748b;
        --s-radius: 12px;
    }

    /* Fix sidebar overlap */
    .layout-menu { z-index: 1045 !important; }
    .menu-item .menu-link { pointer-events: auto !important; }

    .s-card {
        background: var(--s-surface);
        border: 1px solid var(--s-border);
        border-radius: var(--s-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .s-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .s-card-header {
        background: transparent;
        border-bottom: 1px solid var(--s-border);
        padding: 1.15rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .s-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--s-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Profile Banner */
    .profile-banner {
        background: linear-gradient(135deg, #4361ee 0%, #3b82f6 50%, #06b6d4 100%);
        border-radius: var(--s-radius);
        padding: 1.5rem 2rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .profile-banner::after {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .profile-banner::before {
        content: '';
        position: absolute;
        bottom: -40px; left: 40%;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .profile-avatar {
        width: 60px; height: 60px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 700;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        overflow: hidden;
        background: rgba(255,255,255,0.2);
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-info { position: relative; z-index: 1; flex: 1; min-width: 0; }
    .profile-name { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.15rem; }
    .profile-meta {
        font-size: 0.82rem;
        opacity: 0.85;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .profile-meta span { display: flex; align-items: center; gap: 0.3rem; }

    /* Stat Cards */
    .stat-widget {
        padding: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    .stat-icon-box {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .stat-details { flex-grow: 1; min-width: 0; }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--s-text);
        line-height: 1.2;
        margin-bottom: 0.15rem;
    }
    .stat-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--s-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Jadwal List */
    .jadwal-list { padding: 0; margin: 0; list-style: none; }
    .jadwal-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem 1.5rem;
        border-bottom: 1px solid var(--s-border);
        transition: background 0.15s ease;
    }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-item:hover { background: var(--s-bg); }
    .jadwal-time {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--s-primary);
        white-space: nowrap;
        min-width: 45px;
        flex-shrink: 0;
    }
    .jadwal-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--s-primary);
        flex-shrink: 0;
        opacity: 0.5;
    }
    .jadwal-body { flex: 1; min-width: 0; }
    .jadwal-mapel {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--s-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .jadwal-guru { font-size: 0.75rem; color: var(--s-muted); }

    /* Tugas Items */
    .tugas-list { padding: 0; margin: 0; list-style: none; max-height: 380px; overflow-y: auto; }
    .tugas-list::-webkit-scrollbar { width: 5px; }
    .tugas-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .tugas-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.5rem;
        border-bottom: 1px solid var(--s-border);
        transition: background 0.15s ease;
    }
    .tugas-item:last-child { border-bottom: none; }
    .tugas-item:hover { background: var(--s-bg); }
    .tugas-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .tugas-body { flex: 1; min-width: 0; }
    .tugas-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--s-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tugas-meta { font-size: 0.72rem; color: var(--s-muted); }

    /* Quick Links */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        padding: 1.25rem;
    }
    .quick-link-item {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 1.15rem 0.75rem;
        border-radius: 10px;
        border: 1px solid var(--s-border);
        background: var(--s-surface);
        color: var(--s-text);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
        gap: 0.5rem;
    }
    .quick-link-item:hover {
        background: var(--s-bg);
        border-color: var(--s-primary);
        color: var(--s-primary);
    }
    .quick-link-item i { font-size: 1.3rem; color: var(--s-muted); transition: color 0.2s ease; }
    .quick-link-item:hover i { color: var(--s-primary); }
    .quick-link-text { font-size: 0.75rem; font-weight: 600; line-height: 1.3; }

    /* LMS Banner */
    .lms-banner {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: var(--s-radius);
        padding: 1.25rem 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .lms-banner-info { display: flex; align-items: center; gap: 1rem; }
    .lms-banner-icon { font-size: 2rem; opacity: 0.7; }
    .lms-banner h6 { font-weight: 700; margin-bottom: 0.15rem; }
    .lms-banner p { font-size: 0.82rem; opacity: 0.8; margin: 0; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: var(--s-muted);
    }
    .empty-state i { font-size: 2rem; opacity: 0.3; margin-bottom: 0.75rem; }
    .empty-state-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.2rem; }
    .empty-state-desc { font-size: 0.82rem; }

    /* Pengumuman */
    .announcement-banner {
        background: linear-gradient(135deg, #4361ee 0%, #224abe 100%);
        border-radius: var(--s-radius);
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .announcement-item {
        background: rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 0.85rem 1rem;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .announcement-item h6 { font-size: 0.85rem; font-weight: 700; margin-bottom: 0.25rem; }
    .announcement-item p { font-size: 0.78rem; opacity: 0.8; margin-bottom: 0.25rem; }
    .announcement-item small { font-size: 0.7rem; opacity: 0.7; }

    /* Flyer Modal */
    .modal-flyer { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; backdrop-filter: blur(5px); }
    .modal-flyer.show { display: flex; }
    .modal-flyer-content { background: white; border-radius: 20px; max-width: 450px; width: 90%; overflow: hidden; position: relative; animation: zoomIn 0.3s ease; }
    @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .flyer-close { position: absolute; right: 15px; top: 15px; background: #fff; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-banner { padding: 1.25rem; gap: 1rem; }
        .profile-avatar { width: 48px; height: 48px; font-size: 1.2rem; }
        .profile-name { font-size: 1.05rem; }
        .profile-meta { font-size: 0.75rem; gap: 0.5rem; }
        .stat-value { font-size: 1.25rem; }
        .stat-widget { padding: 1rem; gap: 0.75rem; }
        .stat-icon-box { width: 38px; height: 38px; font-size: 1rem; }
        .jadwal-item { padding: 0.7rem 1.15rem; gap: 0.7rem; }
        .jadwal-time { font-size: 0.72rem; min-width: 40px; }
        .tugas-item { padding: 0.7rem 1.15rem; gap: 0.6rem; }
        .quick-links-grid { padding: 1rem; gap: 0.5rem; grid-template-columns: repeat(3, 1fr); }
        .quick-link-item { padding: 0.85rem 0.4rem; }
        .quick-link-item i { font-size: 1.1rem; }
        .quick-link-text { font-size: 0.68rem; }
        .s-card-header { padding: 1rem 1.15rem; }
        .s-card-title { font-size: 0.9rem; }
        .lms-banner { padding: 1rem 1.25rem; }
        .lms-banner-icon { font-size: 1.5rem; }
    }
    @media (max-width: 480px) {
        .profile-banner { flex-direction: column; text-align: center; }
        .profile-meta { justify-content: center; }
        .stat-value { font-size: 1.1rem; }
        .stat-label { font-size: 0.65rem; }
        .quick-links-grid { grid-template-columns: repeat(3, 1fr); }
    }
</style>
@endsection

@section('content')

    <!-- Profile Banner -->
    <div class="profile-banner">
        <div class="profile-avatar">
            @if(auth()->user()->foto_profil)
                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="user">
            @elseif($siswa->foto)
                <img src="{{ asset('storage/' . $siswa->foto) }}" alt="user">
            @else
                {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-name">Halo, {{ explode(' ', $siswa->nama_lengkap)[0] }}! 👋</div>
            <div class="profile-meta">
                <span><i class="fas fa-school"></i> {{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                <span><i class="fas fa-calendar-alt"></i> {{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                <span><i class="fas fa-id-card"></i> {{ $siswa->nis ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Pengumuman --}}
    @if(isset($pengumuman) && $pengumuman->count() > 0)
        <div class="announcement-banner">
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size: 0.9rem; font-weight: 700;">
                <i class="fas fa-bullhorn"></i> Pengumuman
            </div>
            <div class="row g-2">
                @foreach($pengumuman->take(2) as $item)
                    <div class="col-md-6">
                        <div class="announcement-item">
                            <h6>{{ $item->judul }}</h6>
                            <p>{{ Str::limit($item->isi_pengumuman, 100) }}</p>
                            <small><i class="far fa-calendar-alt me-1"></i>{{ $item->tanggal_pengumuman->format('d M Y') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Attendance Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box" style="color: #10b981; background: #ecfdf5;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['hadir'] ?? 0 }}</div>
                        <div class="stat-label">Hadir</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['sakit'] ?? 0 }}</div>
                        <div class="stat-label">Sakit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box" style="color: #f59e0b; background: #fffbeb;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['izin'] ?? 0 }}</div>
                        <div class="stat-label">Izin</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box" style="color: #ef4444; background: #fef2f2;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['alpha'] ?? 0 }}</div>
                        <div class="stat-label">Alpha</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">
        <!-- Left: Jadwal + Tugas -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- Jadwal Hari Ini -->
            <div class="s-card">
                <div class="s-card-header">
                    <h5 class="s-card-title">
                        <i class="fas fa-clock text-warning"></i> Jadwal Hari Ini
                        <span class="badge bg-label-primary ms-1" style="font-size: 0.7rem;">{{ now()->locale('id')->translatedFormat('l') }}</span>
                    </h5>
                    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                        <a href="{{ route('siswa.lms.jadwal') }}" class="text-primary fw-semibold text-decoration-none" style="font-size: 0.8rem;">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>
                @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                    <ul class="jadwal-list">
                        @foreach($jadwalHariIni as $jadwal)
                            <li class="jadwal-item">
                                <div class="jadwal-time">
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                </div>
                                <div class="jadwal-dot"></div>
                                <div class="jadwal-body">
                                    <div class="jadwal-mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                    <div class="jadwal-guru">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</div>
                                </div>
                                @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                                    <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}"
                                       class="btn btn-sm btn-outline-primary px-3" style="font-size: 0.72rem; font-weight: 600; border-radius: 6px; white-space: nowrap;">
                                        Masuk
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="fas fa-coffee d-block"></i>
                        <div class="empty-state-title">Tidak Ada Jadwal</div>
                        <div class="empty-state-desc">Tidak ada pelajaran hari ini. Selamat istirahat!</div>
                    </div>
                @endif
            </div>

            {{-- Tugas & Nilai (LMS only) --}}
            @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))

                <div class="row g-4 flex-grow-1">
                    <!-- Tugas & Deadline -->
                    <div class="col-md-6 d-flex">
                        <div class="s-card d-flex flex-column w-100">
                            <div class="s-card-header">
                                <h5 class="s-card-title">
                                    <i class="fas fa-tasks text-danger"></i> Tugas
                                </h5>
                            </div>
                            @if($tugasList->count() > 0)
                                <ul class="tugas-list flex-grow-1">
                                    @foreach($tugasList as $tugas)
                                        @php
                                            $deadline = \Carbon\Carbon::parse($tugas->tanggal_deadline);
                                            $diffDays = now()->diffInDays($deadline, false);
                                            $isUrgent = $diffDays <= 1;
                                            $isWarning = $diffDays > 1 && $diffDays <= 3;
                                        @endphp
                                        <li class="tugas-item">
                                            <div class="tugas-icon" style="background: {{ $isUrgent ? '#fef2f2' : ($isWarning ? '#fffbeb' : '#f0f9ff') }}; color: {{ $isUrgent ? '#ef4444' : ($isWarning ? '#f59e0b' : '#3b82f6') }};">
                                                <i class="fas {{ $isUrgent ? 'fa-exclamation-triangle' : ($isWarning ? 'fa-clock' : 'fa-file-alt') }}"></i>
                                            </div>
                                            <div class="tugas-body">
                                                <div class="tugas-title">{{ $tugas->judul_tugas }}</div>
                                                <div class="tugas-meta">
                                                    {{ $tugas->mataPelajaran->nama_mapel }} · {{ $deadline->locale('id')->isoFormat('D MMM') }}
                                                    @if($isUrgent)
                                                        <span class="badge bg-danger ms-1" style="font-size: 0.55rem;">Urgent</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('siswa.lms.mapel.show', $tugas->mata_pelajaran_id) }}"
                                               class="btn btn-sm btn-outline-primary" style="font-size: 0.68rem; border-radius: 6px; white-space: nowrap; padding: 0.3rem 0.6rem;">
                                                Lihat
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-state flex-grow-1 d-flex flex-column justify-content-center">
                                    <i class="fas fa-check-circle d-block text-success"></i>
                                    <div class="empty-state-title text-success">Semua Selesai!</div>
                                    <div class="empty-state-desc">Tidak ada tugas yang harus dikerjakan.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Nilai Terbaru -->
                    <div class="col-md-6 d-flex">
                        <div class="s-card d-flex flex-column w-100">
                            <div class="s-card-header">
                                <h5 class="s-card-title">
                                    <i class="fas fa-trophy text-warning"></i> Nilai Terbaru
                                </h5>
                                <a href="{{ route('siswa.sia.penilaian') }}" class="text-primary fw-semibold text-decoration-none" style="font-size: 0.78rem;">
                                    Semua <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            @if($nilaiTerbaru->count() > 0)
                                <ul class="tugas-list flex-grow-1">
                                    @foreach($nilaiTerbaru as $nilai)
                                        <li class="tugas-item">
                                            <div class="tugas-icon" style="background: #fffbeb; color: #f59e0b;">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="tugas-body">
                                                <div class="tugas-title">{{ $nilai->mataPelajaran->nama_mapel }}</div>
                                                <div class="tugas-meta">
                                                    @if($nilai->jenis_penilaian == 'tugas')
                                                        <span class="badge bg-info" style="font-size: 0.55rem;">Tugas</span>
                                                    @elseif($nilai->jenis_penilaian == 'uts')
                                                        <span class="badge bg-warning" style="font-size: 0.55rem;">UTS</span>
                                                    @elseif($nilai->jenis_penilaian == 'uas')
                                                        <span class="badge bg-danger" style="font-size: 0.55rem;">UAS</span>
                                                    @else
                                                        <span class="badge bg-secondary" style="font-size: 0.55rem;">{{ ucfirst($nilai->jenis_penilaian) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="fw-bold" style="font-size: 1rem; color: var(--s-text);">{{ $nilai->nilai ?? '-' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-state flex-grow-1 d-flex flex-column justify-content-center">
                                    <i class="fas fa-file-alt d-block"></i>
                                    <div class="empty-state-title">Belum Ada Nilai</div>
                                    <div class="empty-state-desc">Nilai akan muncul setelah guru menilai tugas Anda.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @endif

        </div>

        <!-- Right: Quick Links + LMS -->
        <div class="col-lg-4 d-flex flex-column gap-4">

            <!-- Quick Links -->
            <div class="s-card">
                <div class="s-card-header">
                    <h5 class="s-card-title">
                        <i class="fas fa-bolt text-warning"></i> Akses Cepat
                    </h5>
                </div>
                <div class="quick-links-grid">
                    <a href="{{ route('siswa.sia.presensi.index') }}" class="quick-link-item">
                        <i class="fas fa-user-check text-primary"></i>
                        <span class="quick-link-text">Presensi</span>
                    </a>
                    <a href="{{ route('siswa.sia.penilaian') }}" class="quick-link-item">
                        <i class="fas fa-chart-line text-success"></i>
                        <span class="quick-link-text">Nilai</span>
                    </a>
                    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                        <a href="{{ route('siswa.lms.dashboard') }}" class="quick-link-item">
                            <i class="fas fa-graduation-cap text-info"></i>
                            <span class="quick-link-text">LMS</span>
                        </a>
                    @else
                        <a href="{{ route('siswa.sia.dashboard') }}" class="quick-link-item">
                            <i class="fas fa-home text-info"></i>
                            <span class="quick-link-text">Home</span>
                        </a>
                    @endif
                </div>
            </div>

            @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                <!-- LMS Banner -->
                <div class="lms-banner">
                    <div class="lms-banner-info">
                        <i class="fas fa-graduation-cap lms-banner-icon d-none d-sm-block"></i>
                        <div>
                            <h6>HOK-LMS</h6>
                            <p>Kerjakan tugas & materi online hari ini.</p>
                        </div>
                    </div>
                    <a href="{{ route('siswa.lms.dashboard') }}" class="btn btn-light fw-bold text-success rounded-pill px-3 shadow-sm" style="font-size: 0.82rem; white-space: nowrap;">
                        MASUK LMS
                    </a>
                </div>
            @endif

            <!-- Progres Belajar -->
            @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                <div class="s-card flex-grow-1">
                    <div class="s-card-header">
                        <h5 class="s-card-title">
                            <i class="fas fa-chart-pie text-purple"></i> Progres Belajar
                        </h5>
                    </div>
                    <div class="p-3">
                        <div style="height: 180px;">
                            <canvas id="performaChart"></canvas>
                        </div>
                        <div class="row text-center mt-3 g-0">
                            <div class="col-4 border-end">
                                <div style="font-size: 0.65rem; font-weight: 700; color: var(--s-success); text-transform: uppercase;">LULUS</div>
                                <div style="font-size: 1.1rem; font-weight: 700;">{{ $performa['tugas']['selesai'] ?? 0 }}</div>
                            </div>
                            <div class="col-4 border-end">
                                <div style="font-size: 0.65rem; font-weight: 700; color: var(--s-warning); text-transform: uppercase;">PROSES</div>
                                <div style="font-size: 1.1rem; font-weight: 700;">{{ ($performa['tugas']['total'] ?? 0) - ($performa['tugas']['selesai'] ?? 0) }}</div>
                            </div>
                            <div class="col-4">
                                <div style="font-size: 0.65rem; font-weight: 700; color: var(--s-danger); text-transform: uppercase;">TUNDA</div>
                                <div style="font-size: 1.1rem; font-weight: 700;">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Flyer Popup --}}
    @if(isset($flyers) && $flyers->count() > 0)
        <div class="modal-flyer" id="flyerModal">
            <div class="modal-flyer-content shadow-lg">
                <span class="flyer-close" onclick="closeFlyerModal()"><i class="fas fa-times"></i></span>
                @foreach($flyers->take(1) as $flyer)
                    <img src="{{ $flyer->gambar_url }}" style="width: 100%; height: 250px; object-fit: cover;">
                    <div class="p-4 text-center">
                        <h5 class="fw-bold text-primary mb-2">{{ $flyer->judul }}</h5>
                        <p class="small text-muted mb-3">{{ $flyer->deskripsi }}</p>
                        @if($flyer->link_url)
                            <a href="{{ $flyer->link_url }}" target="_blank" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">LIHAT SELENGKAPNYA</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection

@section('scripts')
@if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performaChart');
            if (ctx) {
                const selesai = {{ $performa['tugas']['selesai'] ?? 0 }};
                const total = {{ $performa['tugas']['total'] ?? 0 }};
                const proses = total - selesai;
                const tunda = 0;

                let chartData = [selesai, proses, tunda];
                let bgColors = ['#10b981', '#f6c23e', '#e74a3b'];
                let chartLabels = ['Lulus', 'Proses', 'Tunda'];
                let tooltipCallback = null;

                if (selesai === 0 && proses === 0 && tunda === 0) {
                    chartData = [1];
                    bgColors = ['#e2e8f0'];
                    chartLabels = ['Belum ada data'];
                    tooltipCallback = function() { return 'Belum ada data'; };
                }

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: bgColors,
                            borderWidth: 0,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: tooltipCallback || function(context) {
                                        return context.label + ': ' + context.raw;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Flyer Logic
            const userId = '{{ auth()->id() }}';
            if (!localStorage.getItem('flyerShown_' + userId)) {
                setTimeout(() => {
                    const modal = document.getElementById('flyerModal');
                    if (modal) modal.classList.add('show');
                }, 1200);
            }
        });

        function closeFlyerModal() {
            document.getElementById('flyerModal').classList.remove('show');
            localStorage.setItem('flyerShown_' + '{{ auth()->id() }}', 'true');
        }
    </script>
@else
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = '{{ auth()->id() }}';
            if (!localStorage.getItem('flyerShown_' + userId)) {
                setTimeout(() => {
                    const modal = document.getElementById('flyerModal');
                    if (modal) modal.classList.add('show');
                }, 1200);
            }
        });
        function closeFlyerModal() {
            document.getElementById('flyerModal').classList.remove('show');
            localStorage.setItem('flyerShown_' + '{{ auth()->id() }}', 'true');
        }
    </script>
@endif
@endsection