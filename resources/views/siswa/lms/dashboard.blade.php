@extends('layouts.lms')

@section('title', 'Beranda LMS')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Selamat datang di HOK Learning Management System')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')

    {{-- ALERT SELAMAT DATANG --}}
    <div class="welcome-banner">
        <div class="welcome-content">
            <div class="welcome-text">
                <h4 class="welcome-title">Halo, {{ $siswa->nama_lengkap }}! 👋</h4>
                <p class="welcome-subtitle">Selamat belajar! Jangan lupa cek jadwal dan tugas hari ini.</p>
            </div>
            <div class="welcome-actions">
                <a href="{{ route('siswa.sia.dashboard') }}" class="btn btn-light">
                    <i class="fas fa-external-link-alt me-2"></i>Akses SIA
                </a>
            </div>
        </div>
        <div class="welcome-illustration">
            <i class="fas fa-graduation-cap"></i>
        </div>
    </div>

    {{-- STATISTIK SINGKAT --}}
    <div class="stats-grid">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="stat-icon" style="background: rgba(22, 95, 172, 0.1); color: var(--primary);">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Kehadiran</div>
                <div class="stat-value" style="color: var(--primary);">{{ $persenKehadiran }}%</div>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Tugas Pending</div>
                <div class="stat-value" style="color: #ea580c;">{{ $tugasPending }}</div>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #06b6d4;">
            <div class="stat-icon" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Agenda Bulan Ini</div>
                <div class="stat-value" style="color: #0891b2;">{{ $agendaBulanIni }}</div>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ujian Mendatang</div>
                <div class="stat-value" style="color: #7c3aed;">{{ $ujianMendatang->count() }}</div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="dashboard-grid">
        {{-- LEFT COLUMN --}}
        <div class="dashboard-main">
            {{-- Jadwal Hari Ini --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-clock me-2 text-primary"></i>Jadwal Hari Ini
                        <span class="badge bg-primary ms-2">{{ now()->locale('id')->dayName }}</span>
                    </h6>
                    <a href="{{ route('siswa.lms.jadwal') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="schedule-list">
                    @forelse($jadwalHariIni as $jadwal)
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="schedule-item">
                            <div class="schedule-time">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }}
                            </div>
                            <div class="schedule-content">
                                <div class="schedule-title">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</div>
                                <div class="schedule-teacher">
                                    <i class="fas fa-user-tie me-1"></i>{{ $jadwal->guru->nama_lengkap ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="schedule-action">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Tidak ada jadwal hari ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tugas Deadline Terdekat --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-tasks me-2 text-warning"></i>Tugas Deadline Terdekat
                    </h6>
                    <a href="{{ route('siswa.lms.tugas.index') }}" class="btn btn-sm btn-outline-warning">
                        Lihat Semua
                    </a>
                </div>
                <div class="task-list">
                    @forelse($tugasDeadline as $tugas)
                        <div class="task-item">
                            <div class="task-icon {{ $tugas->jenis_tugas === 'latihan' ? 'bg-info' : 'bg-warning' }}">
                                <i class="fas {{ $tugas->jenis_tugas === 'latihan' ? 'fa-dumbbell' : 'fa-edit' }}"></i>
                            </div>
                            <div class="task-content">
                                <div class="task-title">{{ $tugas->judul_tugas }}</div>
                                <div class="task-meta">
                                    <span class="badge bg-light text-dark">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }}</span>
                                    <span
                                        class="task-deadline {{ $tugas->tanggal_deadline->diffInDays(now()) <= 1 ? 'text-danger' : 'text-muted' }}">
                                        <i class="fas fa-clock me-1"></i>{{ $tugas->tanggal_deadline->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]) }}"
                                class="btn btn-sm btn-primary">
                                Kerjakan
                            </a>
                        </div>
                    @empty
                        <div class="empty-state small-empty">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>Tidak ada tugas yang mendesak</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Mata Pelajaran --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-book me-2 text-primary"></i>Mata Pelajaran
                    </h6>
                </div>
                <div class="mapel-grid">
                    @forelse($mataPelajaranList as $jadwal)
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="mapel-card">
                            <div class="mapel-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="mapel-name">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</div>
                            <div class="mapel-teacher">{{ Str::limit($jadwal->guru->nama_lengkap ?? 'N/A', 20) }}</div>
                        </a>
                    @empty
                        <div class="empty-state full-width">
                            <p class="text-muted small">Belum ada mata pelajaran</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="dashboard-sidebar">
            {{-- Pengumuman --}}
            <div class="card-custom announcement-card">
                <div class="announcement-header">
                    <i class="fas fa-bullhorn me-2"></i>Pengumuman
                </div>
                @if($pengumuman)
                    <div class="announcement-body">
                        <span
                            class="badge bg-warning text-dark mb-2">{{ strtoupper($pengumuman->prioritas ?? 'Normal') }}</span>
                        <h6 class="announcement-title">{{ $pengumuman->judul }}</h6>
                        <p class="announcement-text">{{ Str::limit($pengumuman->isi_pengumuman, 100) }}</p>
                        @if($pengumuman->file_lampiran)
                            <a href="{{ asset('storage/' . $pengumuman->file_lampiran) }}" target="_blank"
                                class="btn btn-sm btn-light w-100 mb-2">
                                <i class="fas fa-file-pdf me-1"></i>Lihat Lampiran
                            </a>
                        @endif
                        <a href="{{ route('siswa.lms.kalender') }}" class="btn btn-light w-100">
                            <i class="fas fa-calendar-alt me-1"></i>Lihat Kalender
                        </a>
                    </div>
                @else
                    <div class="announcement-body empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Tidak ada pengumuman</p>
                    </div>
                @endif
            </div>

            {{-- Notifikasi Hari Ini --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-bell me-2 text-danger"></i>Notifikasi
                    </h6>
                    <a href="{{ route('notifications.index') }}" class="text-primary small">Semua</a>
                </div>
                <div class="notification-list">
                    @forelse($notifikasiHariIni as $notif)
                        <a href="{{ $notif->link ?? '#' }}" class="notification-item {{ $notif->read_at ? '' : 'unread' }}">
                            <div class="notification-icon bg-{{ $notif->color ?? 'secondary' }}">
                                <i class="{{ $notif->icon ?? 'fas fa-bell' }}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">{{ $notif->judul }}</div>
                                <div class="notification-time">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state small-empty">
                            <i class="fas fa-bell-slash text-muted"></i>
                            <p>Tidak ada notifikasi hari ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kegiatan Minggu Ini --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-week me-2 text-info"></i>Minggu Ini
                    </h6>
                    <a href="{{ route('siswa.lms.kalender') }}" class="text-primary small">Kalender</a>
                </div>
                <div class="event-list">
                    @forelse($kalenderMingguIni as $event)
                        <div class="event-item">
                            <div class="event-date">
                                <div class="event-day">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d') }}</div>
                                <div class="event-month">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('M') }}</div>
                            </div>
                            <div class="event-content">
                                <div class="event-title">{{ $event->nama_kegiatan }}</div>
                                <div class="event-type">
                                    <span class="badge bg-{{ $event->jenis_kegiatan === 'libur' ? 'danger' : 'info' }}">
                                        {{ ucfirst($event->jenis_kegiatan) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state small-empty">
                            <i class="fas fa-calendar-check text-muted"></i>
                            <p>Tidak ada kegiatan minggu ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Daftar Guru --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-chalkboard-teacher me-2 text-success"></i>Guru Pengajar
                    </h6>
                    <a href="{{ route('siswa.lms.guru') }}" class="text-primary small">Semua</a>
                </div>
                <div class="teacher-list">
                    @forelse($guruPengajar as $guru)
                        <div class="teacher-item">
                            <div class="teacher-avatar">
                                @if($guru->foto)
                                    <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->nama_lengkap }}">
                                @else
                                    <div class="avatar-placeholder">{{ substr($guru->nama_lengkap, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="teacher-info">
                                <div class="teacher-name">{{ Str::limit($guru->nama_lengkap, 18) }}</div>
                                <div class="teacher-subject">
                                    {{ $guru->guruKelas->first()->mataPelajaran->nama_mapel ?? '-' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state small-empty">
                            <i class="fas fa-users text-muted"></i>
                            <p>Belum ada guru pengajar</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark, #0d3f7a));
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex: 1;
            gap: 16px;
            flex-wrap: wrap;
        }

        .welcome-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .welcome-subtitle {
            opacity: 0.9;
            margin: 0;
            font-size: 14px;
        }

        .welcome-illustration {
            font-size: 80px;
            opacity: 0.15;
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
        }

        .dashboard-main {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .dashboard-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Schedule List */
        .schedule-list {
            padding: 0;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
        }

        .schedule-item:hover {
            background: #f9fafb;
        }

        .schedule-time {
            font-weight: 700;
            font-size: 15px;
            color: var(--primary);
            min-width: 50px;
        }

        .schedule-content {
            flex: 1;
        }

        .schedule-title {
            font-weight: 600;
            color: #1f2937;
        }

        .schedule-teacher {
            font-size: 13px;
            color: #6b7280;
        }

        .schedule-action {
            color: #9ca3af;
        }

        /* Task List */
        .task-list {
            padding: 0;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .task-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .task-content {
            flex: 1;
        }

        .task-title {
            font-weight: 600;
            font-size: 14px;
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 4px;
            font-size: 12px;
        }

        .task-deadline {
            font-size: 12px;
        }

        /* Mapel Grid */
        .mapel-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            padding: 16px;
        }

        .mapel-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }

        .mapel-card:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .mapel-icon {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .mapel-card:hover .mapel-icon {
            color: white;
        }

        .mapel-name {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .mapel-teacher {
            font-size: 11px;
            opacity: 0.7;
        }

        /* Announcement Card */
        .announcement-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark, #0d3f7a));
            color: white;
        }

        .announcement-header {
            padding: 16px 20px;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .announcement-body {
            padding: 20px;
        }

        .announcement-title {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .announcement-text {
            font-size: 13px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        /* Notification List */
        .notification-list {
            padding: 0;
        }

        .notification-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s;
        }

        .notification-item:hover {
            background: #f9fafb;
        }

        .notification-item.unread {
            background: #eff6ff;
        }

        .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .notification-title {
            font-size: 13px;
            font-weight: 500;
        }

        .notification-time {
            font-size: 11px;
            color: #9ca3af;
        }

        /* Event List */
        .event-list {
            padding: 0;
        }

        .event-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .event-date {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 8px 12px;
            text-align: center;
            min-width: 50px;
        }

        .event-day {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .event-month {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .event-title {
            font-size: 13px;
            font-weight: 500;
        }

        /* Teacher List */
        .teacher-list {
            padding: 0;
        }

        .teacher-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .teacher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .teacher-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark, #0d3f7a));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .teacher-name {
            font-size: 13px;
            font-weight: 600;
        }

        .teacher-subject {
            font-size: 11px;
            color: #6b7280;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .empty-state.small-empty {
            padding: 24px 16px;
        }

        .empty-state.small-empty i {
            font-size: 28px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-sidebar {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .mapel-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-sidebar {
                grid-template-columns: 1fr;
            }

            .welcome-banner {
                padding: 20px;
            }

            .welcome-illustration {
                display: none;
            }
        }
    </style>
@endpush