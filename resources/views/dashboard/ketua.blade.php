@extends('layouts.sneat')

@section('title', 'Dashboard Ketua PKBM')

@section('page-title', 'Dashboard Ketua PKBM')
@section('page-subtitle', 'Monitor dan supervisi kegiatan PKBM House Of Knowledge')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Stat Cards Optimization */
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        height: 100%;
        border-radius: 12px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .stat-card .card-body {
        padding: 1.5rem;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.75rem;
    }

    .stat-footer {
        font-size: 0.813rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.06);
        margin-top: 1rem;
    }

    /* Welcome Card */
    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .welcome-card .card-body {
        padding: 2rem;
    }

    /* Quick Actions */
    .quick-action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
    }

    .quick-action-btn {
        padding: 1.5rem 1rem;
        border-radius: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        text-decoration: none;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        border-color: rgba(255,255,255,0.3);
        color: white;
    }

    .quick-action-btn i {
        font-size: 2rem;
    }

    /* Info Box */
    .info-alert {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-top: 1.5rem;
    }

    .info-alert i {
        margin-right: 0.5rem;
    }

    /* Color Variants */
    .bg-primary-soft { background-color: rgba(22, 95, 172, 0.1); color: #165fac; }
    .bg-success-soft { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
    .bg-warning-soft { background-color: rgba(246, 194, 62, 0.1); color: #f6c23e; }
    .bg-info-soft { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }

    /* Responsive Optimizations */
    @media (max-width: 1199.98px) {
        .stat-number {
            font-size: 2rem;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }
    }

    @media (max-width: 991.98px) {
        .quick-action-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .stat-card .card-body {
            padding: 1.25rem;
        }
        .stat-number {
            font-size: 1.75rem;
        }
        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 1.25rem;
        }
        .welcome-card .card-body {
            padding: 1.5rem;
        }
        .quick-action-btn {
            padding: 1.25rem 0.75rem;
        }
        .quick-action-btn i {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 575.98px) {
        .stat-card .card-body {
            padding: 1rem;
        }
        .stat-number {
            font-size: 1.5rem;
        }
        .stat-label {
            font-size: 0.7rem;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
        }
        .quick-action-grid {
            grid-template-columns: 1fr;
        }
        .quick-action-btn {
            flex-direction: row;
            justify-content: center;
            gap: 1rem;
            padding: 1rem;
        }
        .quick-action-btn i {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
    <!-- Stats Cards Row -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Total Siswa -->
        <div class="col-6 col-lg-4">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">Total Siswa</div>
                            <div class="stat-number text-primary">{{ $totalSiswa }}</div>
                        </div>
                        <div class="stat-icon bg-primary-soft">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <small class="text-muted">Siswa terdaftar</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-6 col-lg-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">Total Guru</div>
                            <div class="stat-number text-success">{{ $totalGuru }}</div>
                        </div>
                        <div class="stat-icon bg-success-soft">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <small class="text-muted">Tenaga pendidik</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-6 col-lg-4">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">Total Kelas</div>
                            <div class="stat-number text-warning">{{ $totalKelas }}</div>
                        </div>
                        <div class="stat-icon bg-warning-soft">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <small class="text-muted">Kelas aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="card welcome-card mb-3 mb-md-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="text-white mb-1">
                        <i class="fas fa-hand-wave me-2"></i>Selamat Datang, Ketua PKBM!
                    </h4>
                    <p class="mb-0 opacity-75" style="font-size: 0.9rem;">
                        Monitor dan supervisi kegiatan PKBM House Of Knowledge
                    </p>
                </div>
                <span class="badge bg-white text-primary px-3 py-2">
                    <i class="fas fa-calendar-day me-1"></i>{{ now()->format('d M Y') }}
                </span>
            </div>

            <div class="mt-4">
                <h6 class="text-white mb-3">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h6>
                <div class="quick-action-grid">
                    <a href="{{ route('ketua.monitoring.pengguna') }}" class="quick-action-btn bg-primary">
                        <i class="fas fa-users"></i>
                        <span>Monitor Pengguna</span>
                    </a>
                    <a href="{{ route('ketua.monitoring.siswa') }}" class="quick-action-btn bg-success">
                        <i class="fas fa-user-graduate"></i>
                        <span>Data Siswa</span>
                    </a>
                    <a href="{{ route('ketua.monitoring.wali-kelas') }}" class="quick-action-btn bg-info">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Data Wali Kelas</span>
                    </a>
                    <a href="{{ route('ketua.monitoring.guru-pengajar') }}" class="quick-action-btn bg-warning">
                        <i class="fas fa-user-tie"></i>
                        <span>Guru Pengajar</span>
                    </a>
                    <a href="{{ route('ketua.laporan.index') }}" class="quick-action-btn bg-danger">
                        <i class="fas fa-file-pdf"></i>
                        <span>Cetak Laporan</span>
                    </a>
                    <a href="{{ route('ketua.catatan.create') }}" class="quick-action-btn bg-secondary">
                        <i class="fas fa-sticky-note"></i>
                        <span>Kirim Catatan</span>
                    </a>
                </div>
            </div>

            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <strong>Info:</strong> Semua data yang ditampilkan bersifat read-only (hanya lihat).
                Untuk melakukan perubahan data, silakan hubungi Admin.
            </div>
        </div>
    </div>
@endsection