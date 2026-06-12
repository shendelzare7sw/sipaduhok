@extends('layouts.lms-guru')

@section('title', 'Beranda LMS')
@section('page-title', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)
@section('page-subtitle', 'Kelola pembelajaran untuk ' . $jumlahSiswa . ' siswa')

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/dashboard.css'])
@endpush

@section('content')
<div class="guru-lms-dashboard-page">
    {{-- Quick Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Total Siswa</div>
                <div class="stat-value">{{ $jumlahSiswa }}</div>
            </div>
            <div class="stat-icon siswa">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Materi</div>
                <div class="stat-value">{{ $jumlahMateri }}</div>
            </div>
            <div class="stat-icon materi">
                <i class="fas fa-book"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Tugas</div>
                <div class="stat-value">{{ $jumlahTugas }}</div>
            </div>
            <div class="stat-icon tugas">
                <i class="fas fa-tasks"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Perlu Koreksi</div>
                <div class="stat-value">{{ $tugasBelumDikoreksi }}</div>
            </div>
            <div class="stat-icon koreksi">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card-custom premium-section">
        <div class="d-flex align-items-center mb-4">
            <div class="title-icon-wrapper">
                <i class="fas fa-bolt text-white"></i>
            </div>
            <h5 class="mb-0 fw-bold quick-actions-title">Quick Actions</h5>
        </div>
        
        <div class="premium-actions-grid">
            <a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-materi">
                <div class="icon-circle">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Materi</span>
                    <span class="card-subtitle">Kelola bahan ajar</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-tugas">
                <div class="icon-circle">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Tugas</span>
                    <span class="card-subtitle">Kelola tugas</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-latihan">
                <div class="icon-circle">
                    <i class="fas fa-pencil-ruler"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Latihan</span>
                    <span class="card-subtitle">Kelola soal latihan</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-ujian">
                <div class="icon-circle">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Ujian</span>
                    <span class="card-subtitle">Kelola ujian</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-forum">
                <div class="icon-circle">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Forum</span>
                    <span class="card-subtitle">Ruang diskusi siswa</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-meeting">
                <div class="icon-circle">
                    <i class="fas fa-video"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Virtual Class</span>
                    <span class="card-subtitle">Jadwalkan meeting</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.nilai.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-nilai">
                <div class="icon-circle">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Nilai Siswa</span>
                    <span class="card-subtitle">Rekap penilaian</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="content-grid">
        <div class="card-custom">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-book me-2"></i>Materi Terbaru
                </h6>
            </div>
            <div class="activity-section-body">
                @if($materiTerbaru->count() > 0)
                    <div class="activity-list">
                        @foreach($materiTerbaru as $materi)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start flex-wrap activity-row">
                                <div class="activity-main">
                                    <h6 class="activity-title">
                                        {{ $materi->judul_materi }}
                                    </h6>
                                    <p class="activity-meta">
                                        <i class="fas fa-calendar me-1"></i>
                                        <span>{{ $materi->tanggal_upload->format('d M Y') }}</span>
                                    </p>
                                </div>
                                <span class="badge bg-info activity-badge">
                                    {{ strtoupper($materi->tipe_file) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-outline-primary w-100 fw-bold activity-link">
                        Lihat Semua Materi
                    </a>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox empty-state-icon"></i>
                        <p class="text-muted mb-0">Belum ada materi</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-tasks me-2"></i>Tugas Terbaru
                </h6>
            </div>
            <div class="activity-section-body">
                @if($tugasTerbaru->count() > 0)
                    <div class="activity-list">
                        @foreach($tugasTerbaru as $tugas)
                        <div class="activity-item tugas">
                            <div class="d-flex justify-content-between align-items-start flex-wrap activity-row">
                                <div class="activity-main">
                                    <h6 class="activity-title">
                                        {{ $tugas->judul_tugas }}
                                    </h6>
                                    <p class="activity-meta">
                                        <i class="fas fa-clock me-1"></i>
                                        <span>Deadline: {{ $tugas->tanggal_deadline->format('d M Y') }}</span>
                                    </p>
                                </div>
                                @if($tugas->isOverdue())
                                    <span class="badge bg-danger activity-badge">Lewat</span>
                                @else
                                    <span class="badge bg-success activity-badge">Aktif</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-outline-primary w-100 fw-bold activity-link">
                        Lihat Semua Tugas
                    </a>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox empty-state-icon"></i>
                        <p class="text-muted mb-0">Belum ada tugas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
