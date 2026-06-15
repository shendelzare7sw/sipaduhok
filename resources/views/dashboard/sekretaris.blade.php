@extends('layouts.sneat')

@section('title', 'Dashboard Sekretaris')

@section('page-title', 'Dashboard Sekretaris')
@section('page-subtitle', 'Kelola Kalender Akademik, Pengumuman, dan Flyer')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/dashboard/sekretaris.css'])
@endsection

@section('content')
<div class="sekretaris-dashboard-page"
     data-fullcalendar-src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"
     data-calendar-monthly-url="{{ route('sekretaris.kalender.bulanan') }}"
     data-calendar-edit-base-url="{{ url('sekretaris/kalender') }}">

    <!-- Top Header & Date -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start gap-3 mb-4">
        <h5 class="mb-0 fw-bold text-dark dashboard-section-title">
            <i class="fas fa-clipboard-list me-2 text-primary"></i> Ringkasan Sekretariat
        </h5>
        
        <div class="d-flex flex-wrap gap-2">
            @if(isset($tahunAjaranAktif) && $tahunAjaranAktif)
                <span class="badge bg-white text-dark px-3 py-2 fs-6 rounded-pill shadow-sm border dashboard-date-badge">
                    <i class="fas fa-flag-checkered me-2 text-primary"></i> TA: {{ $tahunAjaranAktif->nama_tahun_ajaran }}
                </span>
            @endif
            <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill shadow-sm border dashboard-date-badge">
                <i class="fas fa-calendar-alt me-2"></i> {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-primary">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['totalKalender'] }}</div>
                        <div class="stat-label">Total Kegiatan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-success">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['kegiatanAktif'] }}</div>
                        <div class="stat-label">Kegiatan Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-info">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['pengumumanAktif'] }}</div>
                        <div class="stat-label">Pengumuman</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-warning">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['flyerAktif'] + $stats['beritaAktif'] }}</div>
                        <div class="stat-label">Flyer & Berita</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">
        <!-- Calendar Section -->
        <div class="col-lg-8">
            <div class="dashboard-card h-100 flex-column d-flex">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-calendar-alt text-primary card-title-icon"></i> Kalender Akademik Utama
                    </h5>
                    <a href="{{ route('sekretaris.kalender.create') }}" class="btn btn-sm btn-primary shadow-sm btn-action">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
                <div class="card-body p-0 flex-grow-1">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Info & Activities -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <!-- Info Sistem Alert -->
            <div class="alert alert-primary d-flex align-items-center rounded-3 shadow-none border-0 m-0 system-info-alert" role="alert">
                <i class="fas fa-robot fs-4 me-3 text-primary"></i>
                <div class="system-info-text">
                    <strong>Info Sistem:</strong> Kegiatan kalender otomatis menjadi <strong class="text-primary">Pengumuman</strong> jika waktu pengerjaan kurang dari 3 hari.
                </div>
            </div>

            <!-- Akses Modul Utama -->
            <div class="dashboard-card border-0 shadow-sm">
                <div class="card-header-clean border-bottom">
                    <h5 class="card-title-clean">
                        <i class="fas fa-bolt text-warning card-title-icon"></i> Akses Modul Utama
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="quick-links-grid">
                        <a href="{{ route('sekretaris.kalender.index') }}" class="quick-link-item">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span class="quick-link-text">Kelola<br>Kalender</span>
                        </a>
                        <a href="{{ route('sekretaris.pengumuman.index') }}" class="quick-link-item">
                            <i class="fas fa-bullhorn text-info"></i>
                            <span class="quick-link-text">Kelola<br>Pengumuman</span>
                        </a>
                        <a href="{{ route('sekretaris.flyer.index') }}" class="quick-link-item">
                            <i class="fas fa-image text-warning"></i>
                            <span class="quick-link-text">Publikasi<br>Flyer</span>
                        </a>
                        <a href="{{ route('sekretaris.berita.index') }}" class="quick-link-item">
                            <i class="fas fa-newspaper text-success"></i>
                            <span class="quick-link-text">Portal<br>Berita</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kegiatan Hari Ini -->
            <div class="dashboard-card flex-grow-1 d-flex flex-column">
                <div class="card-header-clean border-bottom">
                    <h5 class="card-title-clean">
                        <i class="fas fa-clock text-success card-title-icon"></i> Kegiatan Hari Ini
                    </h5>
                </div>
                <div class="card-body p-0 flex-grow-1 table-fixed-height">
                    @if($kegiatanHariIni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <tbody>
                                    @foreach($kegiatanHariIni as $kegiatan)
                                    <tr>
                                        <td class="ps-4 py-3 border-0 border-bottom">
                                            <div class="fw-bold text-dark">{{ $kegiatan->nama_kegiatan }}</div>
                                            <div class="small text-muted mt-1"><i class="far fa-clock me-1"></i>{{ $kegiatan->waktu_mulai ?? 'Seharian' }}</div>
                                        </td>
                                        <td class="text-end pe-4 py-3 align-middle border-0 border-bottom">
                                            <a href="{{ route('sekretaris.kalender.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-warning shadow-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3 empty-calendar-icon">
                                <i class="fas fa-calendar-day fa-2x text-secondary"></i>
                            </div>
                            <p class="text-muted small mb-0 fw-medium">Tidak ada kegiatan hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-primary" id="eventTitle">
                        <i class="fas fa-calendar-check me-2"></i>Detail Kegiatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="eventDetails">
                    <!-- Event Details will be injected here -->
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="editEventBtn" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/dashboard/sekretaris.js'])
@endsection
