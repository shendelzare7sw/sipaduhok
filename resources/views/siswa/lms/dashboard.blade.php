@extends('layouts.lms')

@section('title', 'Beranda LMS')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Selamat datang di HOK Learning Management System')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/dashboard.css'])
@endpush

@section('content')
<div class="siswa-lms-dashboard-page">

    {{-- ALERT SELAMAT DATANG --}}
    <div class="welcome-banner">
        <div class="welcome-content">
            <div class="welcome-text">
                <h4 class="welcome-title">Halo, {{ $siswa->nama_lengkap }}!</h4>
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
                        <div class="empty-state span-all">
                            <i class="fas fa-check-circle text-success opacity-full"></i>
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
                        <div class="empty-state w-100 span-all">
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
                    <a href="{{ route('siswa.lms.pengumuman.index') }}" class="text-white-50 small text-decoration-none announcement-all-link">
                        Lihat Semua <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="widget-body p-0">
                    @forelse($pengumumanList as $ann)
                        <div class="announcement-item p-3 border-bottom border-white-10">
                            @if($ann->prioritas == 'tinggi')
                                <span class="badge bg-danger mb-2">PENTING</span>
                            @endif
                            <h5 class="ann-title ann-title-sm mb-2 text-white">{{ $ann->judul }}</h5>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white-50 small ann-time">
                                    {{ $ann->created_at->copy()->locale('id')->diffForHumans() }}
                                </span>
                                <a href="{{ route('siswa.lms.pengumuman.show', $ann->id) }}" class="btn btn-sm btn-light py-1 px-3 ann-action-btn">
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
</div>
@endsection
