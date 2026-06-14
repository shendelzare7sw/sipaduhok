@extends('layouts.sneat')

@section('title', 'Dashboard Ketua PKBM')

@section('page-title', 'Dashboard Ketua PKBM')
@section('page-subtitle', 'Monitor dan supervisi kegiatan PKBM House Of Knowledge')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/dashboard/ketua.css'])
@endsection

@section('content')
<div class="ketua-dashboard-page">

    <!-- Top Header & Date -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold text-dark dashboard-section-title">
            <i class="fas fa-chart-pie me-2 text-primary"></i> Ringkasan Statistik
        </h5>
        <div>
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
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalSiswa }}</div>
                        <div class="stat-label">Siswa Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-success">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalGuru }}</div>
                        <div class="stat-label">Guru Pengajar</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-info">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalKelas }}</div>
                        <div class="stat-label">Total Kelas Aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper {{ $pendingDispensasi > 0 ? 'bg-label-warning' : 'bg-label-secondary' }}">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $pendingDispensasi }}</div>
                        <div class="stat-label">Pending Dispensasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <!-- Left Column: Tasks / Information -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- Information Alert -->
            <div class="alert alert-primary d-flex align-items-center rounded-3 shadow-none border-0 supervision-alert" role="alert">
                <i class="fas fa-info-circle fs-4 me-3 text-primary"></i>
                <div class="supervision-alert-text">
                    <strong>Mode Pengawasan (Hanya Baca):</strong> Anda memiliki akses pengawasan eksklusif. Data yang ditampilkan adalah untuk keperluan analitik dan supervisi. Segala jenis mutasi data harus dilakukan melalui staf admin.
                </div>
            </div>

            <!-- Recent Activity / Logs -->
            <div class="dashboard-card flex-grow-1">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h5 class="card-title-clean">
                        <i class="fas fa-history card-title-icon"></i> Aktivitas Login Civitas
                    </h5>
                    <a href="{{ route('ketua.monitoring.pengguna') }}" class="btn btn-sm btn-outline-primary shadow-sm dashboard-link-button">Lihat Pengguna</a>
                </div>
                <div class="activity-feed">
                    @forelse($recent_logins as $login)
                    <div class="activity-item">
                        <div class="activity-avatar">
                            {{ strtoupper(substr($login->name, 0, 1)) }}
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $login->name }}</div>
                            <div class="activity-meta">
                                <span class="badge bg-label-primary me-1">{{ $login->roleRelation->role_name ?? 'User' }}</span>
                                <i class="far fa-clock ms-1 me-1"></i> {{ \Carbon\Carbon::parse($login->last_login_at)->locale('id')->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox mb-2 fs-2"></i>
                        <p class="mb-0">Belum ada aktivitas terekam.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
        </div>
        
        <!-- Right Column: Quick Links -->
        <div class="col-lg-4">
            <div class="dashboard-card h-100 d-flex flex-column">
                <div class="card-header-clean border-bottom-0 pb-2">
                    <h5 class="card-title-clean">
                        <i class="fas fa-th-large card-title-icon"></i> Akses Modul Utama
                    </h5>
                </div>
                
                <!-- Custom Tabs Fixed -->
                <div class="px-2 pb-2 border-bottom module-tabs-wrapper">
                    <ul class="nav nav-pills nav-justified custom-nav-pills flex-column flex-sm-row module-tabs" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active py-2 px-1 module-tab-button" role="tab" data-bs-toggle="tab" data-bs-target="#tab-approval" aria-selected="true">Approval</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1 module-tab-button" role="tab" data-bs-toggle="tab" data-bs-target="#tab-monitor" aria-selected="false">Monitoring</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1 module-tab-button" role="tab" data-bs-toggle="tab" data-bs-target="#tab-laporan" aria-selected="false">Laporan</button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content p-0 flex-grow-1 module-tab-content">
                    
                    <!-- Approval & Dispensasi Tab -->
                    <div class="tab-pane fade show active h-100" id="tab-approval" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('ketua.promotion.approval.index') }}" class="quick-link-item position-relative">
                                <i class="fas fa-check-double text-success"></i>
                                <span class="quick-link-text">Dispensasi Kenaikan</span>
                            </a>
                            <a href="{{ route('ketua.validasi-rapor.index') }}" class="quick-link-item position-relative">
                                <i class="fas fa-certificate text-primary"></i>
                                <span class="quick-link-text">Validasi Rapor</span>
                            </a>
                            <a href="{{ route('ketua.dispensasi.index') }}" class="quick-link-item position-relative">
                                <i class="fas fa-hand-holding-heart text-warning"></i>
                                <span class="quick-link-text">Dispensasi Keuangan</span>
                                @if($pendingDispensasi > 0)
                                    <span class="badge bg-danger rounded-pill badge-alert-dot"></span>
                                @endif
                            </a>
                        </div>
                    </div>
                    
                    <!-- Monitoring Tab -->
                    <div class="tab-pane fade h-100" id="tab-monitor" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('ketua.monitoring.siswa') }}" class="quick-link-item">
                                <i class="fas fa-user-graduate text-primary"></i>
                                <span class="quick-link-text">Data Siswa</span>
                            </a>
                            <a href="{{ route('ketua.monitoring.wali-kelas') }}" class="quick-link-item">
                                <i class="fas fa-chalkboard-teacher text-info"></i>
                                <span class="quick-link-text">Data Wali Kelas</span>
                            </a>
                            <a href="{{ route('ketua.monitoring.guru-pengajar') }}" class="quick-link-item">
                                <i class="fas fa-user-tie text-success"></i>
                                <span class="quick-link-text">Data Guru</span>
                            </a>
                            <a href="{{ route('ketua.monitoring.pengguna') }}" class="quick-link-item">
                                <i class="fas fa-users text-secondary"></i>
                                <span class="quick-link-text">Semua Pengguna</span>
                            </a>
                        </div>
                    </div>

                    <!-- Laporan & Catatan Tab -->
                    <div class="tab-pane fade h-100" id="tab-laporan" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('ketua.laporan.index') }}" class="quick-link-item">
                                <i class="fas fa-print text-danger"></i>
                                <span class="quick-link-text">Cetak Laporan</span>
                            </a>
                            <a href="{{ route('ketua.catatan.index') }}" class="quick-link-item">
                                <i class="fas fa-comment-dots text-primary"></i>
                                <span class="quick-link-text">Kirim Catatan</span>
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
