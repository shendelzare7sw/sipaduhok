@extends('layouts.sneat')

@section('title', 'Dashboard Admin')

{{-- PENTING: Sections ini harus ada untuk menampilkan header --}}
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Kelola sistem secara menyeluruh')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Page Header Enhancement */
    .border-bottom {
        border-color: #e9ecef !important;
    }

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
        background: linear-gradient(135deg, #165fac 0%, #0d3a6b 100%);
        color: white;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(22, 95, 172, 0.2);
    }

    .welcome-card .card-body {
        padding: 2rem;
    }

    /* Quick Actions */
    .quick-action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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

    /* Chart Container */
    .chart-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .chart-container {
        position: relative;
        height: 320px;
        padding: 1rem 0;
    }

    /* Activity Timeline */
    .activity-item {
        padding: 1rem;
        border-left: 3px solid #e9ecef;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        border-radius: 0 8px 8px 0;
        background: white;
    }

    .activity-item:hover {
        background-color: #f8f9fa;
        border-left-color: #165fac;
        transform: translateX(5px);
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Menu Cepat */
    .quick-menu-item {
        padding: 1rem 1.25rem;
        border: none;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        color: #495057;
    }

    .quick-menu-item:hover {
        background-color: #f8f9fa;
        padding-left: 1.5rem;
        color: #165fac;
    }

    .quick-menu-item:last-child {
        border-bottom: none;
    }

    /* System Info */
    .info-item {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    /* Badge Styling */
    .badge-new {
        animation: pulse 2s infinite;
        font-size: 0.7rem;
        padding: 0.35rem 0.6rem;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    .status-online {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        animation: blink 2s infinite;
        margin-right: 0.5rem;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    /* Card Headers */
    .card-header-custom {
        background: transparent;
        border-bottom: 2px solid #f0f0f0;
        padding: 1.25rem 1.5rem;
    }

    .card-header-custom .card-title {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
        color: #344054;
    }

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
        .chart-container {
            height: 280px;
        }
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
        .chart-container {
            height: 250px;
        }
        .quick-action-btn {
            padding: 1.25rem 0.75rem;
        }
        .quick-action-btn i {
            font-size: 1.75rem;
        }

        /* Header responsive */
        .border-bottom h4 {
            font-size: 1.25rem;
        }
        .border-bottom .small {
            font-size: 0.8rem;
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
        .chart-container {
            height: 220px;
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
        .activity-item {
            padding: 0.75rem;
        }
    }

    /* Color Variants */
    .bg-primary-soft { background-color: rgba(22, 95, 172, 0.1); color: #165fac; }
    .bg-success-soft { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
    .bg-warning-soft { background-color: rgba(246, 194, 62, 0.1); color: #f6c23e; }
    .bg-info-soft { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }
    .bg-danger-soft { background-color: rgba(231, 74, 59, 0.1); color: #e74a3b; }
</style>
@endsection

@section('content')

    <!-- Stats Cards Row -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Total Siswa -->
        <div class="col-6 col-lg-3">
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
                        @if($siswaBaruBulanIni > 0)
                            <span class="badge bg-success badge-new float-end">
                                +{{ $siswaBaruBulanIni }} bulan ini
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-6 col-lg-3">
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
        <div class="col-6 col-lg-3">
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
                        <small class="text-muted">
                            Avg: {{ $totalKelas > 0 ? round($totalSiswa / $totalKelas) : 0 }} siswa/kelas
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-6 col-lg-3">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="stat-label text-muted">Total User</div>
                            <div class="stat-number text-info">{{ $totalUser }}</div>
                        </div>
                        <div class="stat-icon bg-info-soft">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <small class="text-muted">User aktif sistem</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-3 g-md-4">
        <!-- Left Column -->
        <div class="col-lg-8">

            <!-- Welcome Card -->
            <div class="card welcome-card mb-3 mb-md-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h4 class="text-white mb-1">
                                <i class="fas fa-hand-wave me-2"></i>Selamat Datang, Admin!
                            </h4>
                            <p class="mb-0 opacity-75" style="font-size: 0.9rem;">
                                Anda memiliki akses penuh ke seluruh sistem SIPADUHOK
                            </p>
                        </div>
                        <span class="badge bg-white text-primary px-3 py-2">
                            <i class="fas fa-calendar-day me-1"></i>{{ now()->format('d M Y') }}
                        </span>
                    </div>

                    @if($siswaBaruBulanIni > 0)
                    <div class="alert alert-light mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Bulan ini ada <strong>{{ $siswaBaruBulanIni }}</strong> siswa baru terdaftar
                    </div>
                    @endif

                    <div class="mt-4">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h6>
                        <div class="quick-action-grid">
                            <a href="{{ route('admin.users.create-siswa') }}" class="quick-action-btn bg-primary">
                                <i class="fas fa-user-plus"></i>
                                <span>Tambah Siswa</span>
                            </a>
                            <a href="{{ route('admin.kelas.create') }}" class="quick-action-btn bg-success">
                                <i class="fas fa-plus-circle"></i>
                                <span>Buat Kelas</span>
                            </a>
                            <a href="{{ route('admin.users.create-tenaga-pendidik') }}" class="quick-action-btn bg-info">
                                <i class="fas fa-user-tie"></i>
                                <span>Tambah Guru</span>
                            </a>
                            <a href="{{ route('admin.cetak-laporan.index') }}" class="quick-action-btn bg-secondary">
                                <i class="fas fa-file-pdf"></i>
                                <span>Cetak Laporan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Card -->
            <div class="card chart-card mb-3 mb-md-4">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Statistik Pendaftaran Siswa
                    </h5>
                    <select class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                        <option>6 Bulan Terakhir</option>
                        <option>3 Bulan Terakhir</option>
                        <option>1 Tahun Terakhir</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="registrationChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribution Charts -->
            <div class="row g-3 g-md-4">
                <div class="col-md-6">
                    <div class="card chart-card h-100">
                        <div class="card-header card-header-custom">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>Siswa per Kelas
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="classChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card chart-card h-100">
                        <div class="card-header card-header-custom">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-pie me-2 text-info"></i>Distribusi Gender
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="genderChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="col-lg-4">

            <!-- Menu Cepat -->
            <div class="card mb-3 mb-md-4">
                <div class="card-header card-header-custom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link me-2 text-primary"></i>Menu Cepat
                    </h5>
                </div>
                <div class="card-body p-0">
                    <a href="{{ route('admin.users.index') }}" class="quick-menu-item">
                        <span>
                            <i class="fas fa-users text-primary me-2"></i>
                            <span class="d-none d-sm-inline">Kelola Pengguna</span>
                            <span class="d-inline d-sm-none">Pengguna</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.tahun-ajaran.index') }}" class="quick-menu-item">
                        <span>
                            <i class="fas fa-calendar-alt text-success me-2"></i>
                            <span class="d-none d-sm-inline">Tahun Ajaran</span>
                            <span class="d-inline d-sm-none">TA</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.kelas.index') }}" class="quick-menu-item">
                        <span>
                            <i class="fas fa-school text-warning me-2"></i>
                            <span class="d-none d-sm-inline">Data Kelas</span>
                            <span class="d-inline d-sm-none">Kelas</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.wali-kelas.index') }}" class="quick-menu-item">
                        <span>
                            <i class="fas fa-chalkboard-teacher text-info me-2"></i>
                            <span class="d-none d-sm-inline">Wali Kelas</span>
                            <span class="d-inline d-sm-none">Wali</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.guru-pengajar.index') }}" class="quick-menu-item">
                        <span>
                            <i class="fas fa-user-graduate text-danger me-2"></i>
                            <span class="d-none d-sm-inline">Guru Pengajar</span>
                            <span class="d-inline d-sm-none">Guru</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="quick-menu-item">
                        <span>
                            <i class="fas fa-calendar-week text-purple me-2"></i>
                            <span class="d-none d-sm-inline">Jadwal Pelajaran</span>
                            <span class="d-inline d-sm-none">Jadwal</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="card mb-3 mb-md-4">
                <div class="card-header card-header-custom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2 text-success"></i>Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                    <div class="activity-item">
                        <div class="d-flex gap-3">
                            <div class="activity-icon bg-success-soft">
                                <i class="fas fa-user-plus text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Siswa baru terdaftar</div>
                                <small class="text-muted d-block">Ahmad Fauzi - Kelas X</small>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>2 jam lalu</small>
                            </div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="d-flex gap-3">
                            <div class="activity-icon bg-primary-soft">
                                <i class="fas fa-edit text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Data kelas diperbarui</div>
                                <small class="text-muted d-block">Kelas XI IPA 1</small>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>5 jam lalu</small>
                            </div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="d-flex gap-3">
                            <div class="activity-icon bg-warning-soft">
                                <i class="fas fa-file-alt text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Laporan dicetak</div>
                                <small class="text-muted d-block">Laporan Siswa Semester 1</small>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>1 hari lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center bg-light">
                    <a href="#" class="text-decoration-none small fw-semibold">
                        Lihat Semua Aktivitas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- System Info -->
            <div class="card">
                <div class="card-header card-header-custom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-info"></i>Info Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-code text-primary me-2"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Versi Sistem</small>
                                <strong>SIPADUHOK v1.0</strong>
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Tahun Ajaran Aktif</small>
                                <strong>2025/2026</strong>
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-server text-info me-2"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Status Server</small>
                                <strong>
                                    <span class="status-online"></span>Online
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Last Login</small>
                                <strong>{{ now()->format('d M Y, H:i') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Responsive font sizes
    const isMobile = window.innerWidth < 768;
    const fontSize = isMobile ? 10 : 12;

    Chart.defaults.font.size = fontSize;

    // Registration Chart
    const ctxReg = document.getElementById('registrationChart');
    if (ctxReg) {
        new Chart(ctxReg, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Siswa Baru',
                    data: [12, 19, 15, 25, 22, {{ $siswaBaruBulanIni }}],
                    borderColor: '#165fac',
                    backgroundColor: 'rgba(22, 95, 172, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: isMobile ? 3 : 4,
                    pointHoverRadius: isMobile ? 5 : 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: !isMobile }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Class Chart
    const ctxClass = document.getElementById('classChart');
    if (ctxClass) {
        new Chart(ctxClass, {
            type: 'bar',
            data: {
                labels: ['Kelas X', 'Kelas XI', 'Kelas XII'],
                datasets: [{
                    label: 'Siswa',
                    data: [45, 38, 32],
                    backgroundColor: ['rgba(22,95,172,0.8)', 'rgba(28,200,138,0.8)', 'rgba(246,194,62,0.8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Gender Chart
    const ctxGender = document.getElementById('genderChart');
    if (ctxGender) {
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [55, 45],
                    backgroundColor: ['rgba(54,162,235,0.8)', 'rgba(255,99,132,0.8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: isMobile ? 'bottom' : 'right' }
                }
            }
        });
    }
});
</script>
@endsection
