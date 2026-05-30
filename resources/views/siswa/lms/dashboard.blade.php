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
                <p class="welcome-subtitle">Siap untuk belajar hari ini? Cek jadwal dan tugas terbarumu.</p>
            </div>
            <div class="welcome-actions">
                <a href="{{ route('siswa.sia.dashboard') }}" class="btn btn-glass">
                    <i class="fas fa-external-link-alt me-2"></i>Akses SIA
                </a>
            </div>
        </div>
        <div class="welcome-decoration">
            <i class="fas fa-shapes"></i>
        </div>
    </div>

    {{-- STATISTIK SINGKAT --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-bg primary">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Kehadiran</p>
                <h3 class="stat-value text-primary">{{ $persenKehadiran }}%</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-bg warning">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Tugas Pending</p>
                <h3 class="stat-value text-warning">{{ $tugasPending }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-bg info">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Agenda Bulan Ini</p>
                <h3 class="stat-value text-info">{{ $agendaBulanIni }}</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-bg purple">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Ujian Mendatang</p>
                <h3 class="stat-value text-purple">{{ $ujianMendatang->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="dashboard-grid">
        {{-- LEFT COLUMN --}}
        <div class="dashboard-main">
            {{-- Jadwal Hari Ini --}}
            <div class="section-container">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-clock text-primary me-2"></i>Jadwal Hari Ini
                        <span class="badge badge-date ms-2">{{ now()->locale('id')->dayName }}</span>
                    </div>
                    <a href="{{ route('siswa.lms.jadwal') }}" class="btn-link-custom">Lihat Semua</a>
                </div>
                
                <div class="timeline-container">
                    @forelse($jadwalHariIni as $jadwal)
                        <div class="timeline-item">
                            <div class="timeline-time">
                                <span class="time-start">{{ date('H:i', strtotime($jadwal->jam_mulai)) }}</span>
                                <span class="time-end">{{ date('H:i', strtotime($jadwal->jam_selesai)) }}</span>
                            </div>
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="timeline-card">
                                    <div class="timeline-info">
                                        <h5 class="timeline-subject">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</h5>
                                        <p class="timeline-teacher">
                                            <i class="fas fa-chalkboard-teacher me-1"></i>{{ $jadwal->guru->nama_lengkap ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="timeline-action">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-mug-hot"></i>
                            <p>Tidak ada jadwal pelajaran hari ini. Selamat beristirahat!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tugas Tenggat Terdekat --}}
            <div class="section-container">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-tasks text-warning me-2"></i>Tugas dengan Tenggat Terdekat
                    </div>
                    <a href="{{ route('siswa.lms.tugas.index') }}" class="btn-link-custom">Lihat Semua</a>
                </div>

                <div class="task-grid">
                    @forelse($tugasDeadline as $tugas)
                        <div class="task-card-modern">
                            <div class="task-priority {{ $tugas->tanggal_deadline->diffInDays(now()) <= 1 ? 'urgent' : 'normal' }}">
                            </div>
                            <div class="task-details">
                                <div class="task-badge {{ $tugas->jenis_tugas === 'latihan' ? 'badge-blue' : 'badge-orange' }}">
                                    {{ ucfirst($tugas->jenis_tugas) }}
                                </div>
                                <h5 class="task-name">{{ $tugas->judul_tugas }}</h5>
                                <p class="task-subject">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }}</p>
                                <div class="task-footer">
                                    <span class="task-due">
                                        <i class="far fa-clock me-1"></i> {{ $tugas->tanggal_deadline->copy()->locale('id')->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]) }}" class="btn-task-action">
                                        Kerjakan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <i class="fas fa-check-circle text-success" style="opacity: 1;"></i>
                            <p>Semua tugas aman! Tidak ada deadline mendesak.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Mata Pelajaran --}}
            <div class="section-container">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-book text-primary me-2"></i>Mata Pelajaran
                    </div>
                </div>
                <div class="courses-grid">
                    @forelse($mataPelajaranList as $jadwal)
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="course-card">
                            <div class="course-icon">
                                <span class="course-initial">
                                    {{ substr($jadwal->mataPelajaran->nama_mapel ?? '?', 0, 1) }}
                                </span>
                            </div>
                            <div class="course-info">
                                <h6 class="course-name">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</h6>
                                <p class="course-teacher">{{ Str::limit($jadwal->guru->nama_lengkap ?? 'N/A', 20) }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state w-100" style="grid-column: 1 / -1;">
                            <i class="fas fa-book-open"></i>
                            <p>Belum ada mata pelajaran.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="dashboard-sidebar">
            {{-- Pengumuman --}}
            <div class="sidebar-widget widget-announcement">
                <div class="widget-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bullhorn"></i> Pengumuman</span>
                    <a href="{{ route('siswa.lms.pengumuman.index') }}" class="text-white-50 small text-decoration-none" style="font-size: 0.8rem;">
                        Lihat Semua <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="widget-body p-0">
                    @forelse($pengumumanList as $ann)
                        <div class="announcement-item p-3 border-bottom border-white-10">
                            @if($ann->prioritas == 'tinggi')
                                <span class="badge bg-danger mb-2">PENTING</span>
                            @endif
                            <h5 class="ann-title mb-2 text-white" style="font-size: 0.95rem;">{{ $ann->judul }}</h5>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white-50 small" style="font-size: 0.75rem;">
                                    {{ $ann->created_at->copy()->locale('id')->diffForHumans() }}
                                </span>
                                <a href="{{ route('siswa.lms.pengumuman.show', $ann->id) }}" class="btn btn-sm btn-light py-1 px-3" style="font-size: 0.75rem;">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-mini p-4 text-white-50">
                            <i class="fas fa-inbox mb-2"></i>
                            <p>Tidak ada pengumuman baru</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kegiatan Minggu Ini --}}
            <div class="sidebar-widget">
                <div class="widget-header-simple">
                    <h6><i class="fas fa-calendar-week me-2 text-info"></i>Minggu Ini</h6>
                    <a href="{{ route('siswa.lms.kalender') }}" class="small-link">Kalender</a>
                </div>
                <div class="events-list">
                    @forelse($kalenderMingguIni as $event)
                        <div class="event-row">
                            <div class="event-date-box">
                                <span class="event-d">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d') }}</span>
                                <span class="event-m">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('M') }}</span>
                            </div>
                            <div class="event-info">
                                <h6 class="event-name">{{ $event->nama_kegiatan }}</h6>
                                <span class="event-tag {{ $event->jenis_kegiatan === 'libur' ? 'tag-red' : 'tag-blue' }}">
                                    {{ format_jenis_kegiatan($event->jenis_kegiatan) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-mini">
                            <p>Tidak ada agenda minggu ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Guru Pengajar --}}
            <div class="sidebar-widget">
                <div class="widget-header-simple">
                    <h6><i class="fas fa-users me-2 text-success"></i>Pengajar</h6>
                    <a href="{{ route('siswa.lms.guru') }}" class="small-link">Semua</a>
                </div>
                <div class="teachers-list">
                    @forelse($guruPengajar as $guru)
                        <div class="teacher-row">
                            <div class="teacher-img">
                                @if($guru->foto)
                                    <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->nama_lengkap }}">
                                @else
                                    <span class="initial">{{ substr($guru->nama_lengkap, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="teacher-detail">
                                <h6 class="t-name">{{ Str::limit($guru->nama_lengkap, 18) }}</h6>
                                <p class="t-mapel">{{ $guru->guruKelas->first()->mataPelajaran->nama_mapel ?? 'Pengajar' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="empty-mini">
                            <p>Data guru belum tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5, #3730a3);
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --card-hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            --border-radius: 16px;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            padding: 30px 40px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
        }

        .welcome-decoration {
            position: absolute;
            right: -20px;
            bottom: -40px;
            font-size: 15rem;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        .welcome-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .welcome-title {
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .welcome-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0;
            font-weight: 300;
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-glass:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        .stat-icon-bg {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-icon-bg.primary { background: #e0e7ff; color: #4338ca; }
        .stat-icon-bg.warning { background: #ffedd5; color: #ea580c; }
        .stat-icon-bg.info { background: #cffafe; color: #0891b2; }
        .stat-icon-bg.purple { background: #ede9fe; color: #7c3aed; }

        .stat-content {
            flex-grow: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .text-purple { color: #7c3aed; }

        /* Dashboard Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
        }

        .dashboard-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .dashboard-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Section Styling */
        .section-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
        }

        .badge-date {
            background: #e0e7ff;
            color: #4338ca;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-link-custom {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .btn-link-custom:hover {
            color: #312e81;
            text-decoration: underline;
        }

        /* Timeline Schedule */
        .timeline-container {
            position: relative;
            padding-left: 10px;
        }

        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            position: relative;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 87px;
            top: 25px;
            bottom: -30px;
            width: 2px;
            background: #e5e7eb;
            z-index: 1;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-time {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 60px;
            padding-top: 5px;
        }

        .time-start {
            font-weight: 700;
            color: #1f2937;
            font-size: 1rem;
        }

        .time-end {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .timeline-marker {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid #e0e7ff;
            z-index: 2;
            margin-top: 8px;
            flex-shrink: 0;
        }

        .timeline-content {
            flex-grow: 1;
        }

        .timeline-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
            padding: 16px 20px;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .timeline-card:hover {
            background: white;
            border-color: #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transform: translateX(4px);
        }

        .timeline-subject {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: #111827;
        }

        .timeline-teacher {
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0;
        }

        .timeline-action {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .timeline-card:hover .timeline-action {
            opacity: 1;
        }

        /* Task Grid */
        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .task-card-modern {
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            position: relative;
            transition: all 0.3s ease;
        }

        .task-card-modern:hover {
            box-shadow: var(--card-hover-shadow);
            border-color: #e5e7eb;
        }

        .task-priority {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 4px;
        }

        .task-priority.urgent { background: #ef4444; }
        .task-priority.normal { background: #10b981; }

        .task-details {
            padding: 16px 16px 16px 24px;
        }

        .task-badge {
            display: inline-block;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .badge-blue { background: #e0f2fe; color: #0284c7; }
        .badge-orange { background: #ffedd5; color: #ea580c; }

        .task-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .task-subject {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 12px;
        }

        .task-due {
            font-size: 0.75rem;
            color: #ef4444;
            font-weight: 500;
        }

        .btn-task-action {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-task-action:hover {
            background: #111827;
            color: white;
        }

        /* Courses Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .course-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }

        .course-card:hover {
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .course-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .course-info {
            overflow: hidden;
        }

        .course-name {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .course-teacher {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0;
        }

        /* Sidebar Widgets */
        .sidebar-widget {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .widget-announcement {
            background: var(--primary-gradient);
            color: white;
        }

        .widget-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .widget-body {
            padding: 20px;
        }

        .ann-title {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .ann-text {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 16px;
            line-height: 1.6;
            color: #e0e7ff;
        }

        .ann-actions {
            display: flex;
            gap: 10px;
        }

        .btn-ann-action {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-ann-action.primary {
            background: white;
            color: var(--primary);
        }

        .btn-ann-action.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-ann-action:hover {
            transform: translateY(-2px);
        }

        /* Simple Widget Header */
        .widget-header-simple {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .widget-header-simple h6 {
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
            color: #374151;
        }

        .small-link {
            font-size: 0.75rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        /* Events List */
        .events-list, .teachers-list {
            padding: 10px 0;
        }

        .event-row {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .event-row:last-child {
            border-bottom: none;
        }

        .event-date-box {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 6px 10px;
            text-align: center;
            min-width: 50px;
            display: flex;
            flex-direction: column;
        }

        .event-d {
            font-weight: 800;
            font-size: 1.1rem;
            color: #1f2937;
            line-height: 1;
        }

        .event-m {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            margin-top: 2px;
        }

        .event-info {
            flex-grow: 1;
        }

        .event-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .event-tag {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .tag-red { background: #fee2e2; color: #ef4444; }
        .tag-blue { background: #e0f2fe; color: #0284c7; }

        /* Teachers List */
        .teacher-row {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .teacher-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .teacher-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .teacher-img .initial {
            width: 100%;
            height: 100%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .teacher-detail {
            overflow: hidden;
        }

        .t-name {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1px;
            color: #374151;
        }

        .t-mapel {
            font-size: 0.75rem;
            color: #9ca3af;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }

        /* Empty States */
        .empty-mini {
            padding: 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .empty-state {
            padding: 30px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

             .welcome-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .welcome-actions {
                width: 100%;
            }
            
            .btn-glass {
                width: 100%;
                text-align: center;
                display: block;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .stat-card {
                padding: 14px 16px;
            }

            .section-container {
                padding: 16px;
            }

            .welcome-banner {
                padding: 20px;
            }

            .welcome-title {
                font-size: 1.3rem;
            }

            .welcome-subtitle {
                font-size: 0.875rem;
            }

            .timeline-item {
                gap: 12px;
            }

            .timeline-item::before {
                left: 77px;
            }

            .task-grid {
                grid-template-columns: 1fr;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 16px;
            }
        }
    </style>
@endpush
