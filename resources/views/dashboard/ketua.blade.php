@extends('layouts.sneat')

@section('title', 'Dashboard Ketua PKBM')

@section('page-title', 'Dashboard Ketua PKBM')
@section('page-subtitle', 'Monitor dan supervisi kegiatan PKBM House Of Knowledge')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Card Styling */
    .dashboard-card {
        background: var(--surface-color, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.025);
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }

    .dashboard-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
        transform: translateY(-2px);
    }

    .card-header-clean {
        padding: 1.25rem 1.25rem 0.75rem;
        background: transparent;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .card-title-clean {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main, #334155);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title-icon {
        color: var(--primary-color, #4361ee);
        font-size: 1.1rem;
    }



    /* Stat Widgets */
    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-content {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main, #334155);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted, #94a3b8);
        font-weight: 500;
    }

    /* Quick Links Grid */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1.25rem;
    }

    .quick-link-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1rem;
        border-radius: 10px;
        background: var(--background-color, #f8fafc);
        border: 1px solid var(--border-color, #e2e8f0);
        text-decoration: none;
        transition: all 0.2s ease;
        gap: 0.75rem;
    }

    .quick-link-item:hover {
        background: var(--surface-color, #ffffff);
        border-color: var(--primary-light, #4895ef);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(67, 97, 238, 0.1);
    }

    .quick-link-item i {
        font-size: 1.5rem;
    }

    .quick-link-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main, #334155);
    }
    
    /* Custom Badge for Alert Links */
    .badge-alert-dot {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 14px;
        height: 14px;
        border: 2px solid white;
    }

    /* Activity Feed Container */
    .activity-feed {
        padding: 1.25rem;
        max-height: 280px;
        overflow-y: auto;
    }

    .activity-feed::-webkit-scrollbar {
        width: 6px;
    }
    .activity-feed::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .activity-feed::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .activity-feed::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
    }

    .activity-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .activity-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color, #4361ee);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .activity-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main, #334155);
        margin-bottom: 0.2rem;
    }

    .activity-meta {
        font-size: 0.8rem;
        color: var(--text-muted, #94a3b8);
    }
    
    /* Tabs Overrides */
    .custom-nav-pills .nav-link {
        color: var(--text-muted, #94a3b8);
        border-radius: 6px;
        transition: all 0.2s;
    }
    .custom-nav-pills .nav-link:hover {
        color: var(--primary-color, #4361ee);
        background: rgba(67, 97, 238, 0.05);
    }
    .custom-nav-pills .nav-link.active {
        background-color: var(--primary-light, #4895ef) !important;
        color: #fff !important;
        box-shadow: 0 2px 4px rgba(67, 97, 238, 0.2);
    }
</style>
@endsection

@section('content')

    <!-- Top Header & Date -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.15rem;">
            <i class="fas fa-chart-pie me-2 text-primary"></i> Ringkasan Statistik
        </h5>
        <div>
            <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill shadow-sm border" style="border-color: var(--border-color) !important;">
                <i class="fas fa-calendar-alt me-2"></i> {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
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
                    <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
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
                    <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
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
                    <div class="stat-icon-wrapper {{ $pendingDispensasi > 0 ? 'bg-warning text-warning' : 'bg-secondary text-secondary' }} bg-opacity-10">
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
            <div class="alert alert-primary d-flex align-items-center rounded-3 shadow-none border-0" role="alert" style="background-color: rgba(67, 97, 238, 0.08); color: var(--primary-dark);">
                <i class="fas fa-info-circle fs-4 me-3 text-primary"></i>
                <div style="font-size: 0.9rem;">
                    <strong>Mode Pengawasan (Read-only):</strong> Anda memiliki akses pengawasan eksklusif. Data yang ditampilkan adalah untuk keperluan analitik dan supervisi. Segala jenis mutasi data harus dilakukan melalui Staff Admin.
                </div>
            </div>

            <!-- Recent Activity / Logs -->
            <div class="dashboard-card flex-grow-1">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h5 class="card-title-clean">
                        <i class="fas fa-history card-title-icon"></i> Aktivitas Login Civitas
                    </h5>
                    <a href="{{ route('ketua.monitoring.pengguna') }}" class="btn btn-sm btn-outline-primary shadow-sm" style="font-size: 0.8rem;">Lihat Pengguna</a>
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
                                <i class="far fa-clock ms-1 me-1"></i> {{ \Carbon\Carbon::parse($login->last_login_at)->diffForHumans() }}
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
                <div class="px-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                    <ul class="nav nav-pills nav-justified custom-nav-pills flex-column flex-sm-row" role="tablist" style="gap: 0.25rem; font-size: 0.8rem;">
                        <li class="nav-item">
                            <button type="button" class="nav-link active py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-approval" aria-selected="true" style="font-weight: 600;">Approval</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-monitor" aria-selected="false" style="font-weight: 600;">Monitoring</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-laporan" aria-selected="false" style="font-weight: 600;">Laporan</button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content p-0 flex-grow-1" style="background: transparent; border: none; box-shadow: none;">
                    
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
@endsection