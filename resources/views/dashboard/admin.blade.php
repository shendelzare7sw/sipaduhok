@extends('layouts.sneat')

@section('title', 'Dashboard Admin')

@section('page-title', 'Overview')
@section('page-subtitle', 'Pantau aktivitas dan statistik sekolah')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Card Styling */
    .dashboard-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        overflow: visible;
    }
    
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    }

    .card-header-clean {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-clean {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title-icon {
        color: var(--primary-color);
    }

    /* Stat Cards */
    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(67, 97, 238, 0.08);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-details {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-footer {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed var(--border-color);
        font-size: 0.8rem;
        color: var(--secondary-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Charts */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        padding: 1rem;
    }

    .mini-chart-container {
        position: relative;
        height: 220px;
        width: 100%;
        padding: 1rem;
    }

    /* Quick Links Grid */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 1rem;
        padding: 1.5rem;
    }

    .quick-link-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.25rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--surface-color);
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
        gap: 0.75rem;
    }

    .quick-link-item:hover {
        background: var(--background-color);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .quick-link-item i {
        font-size: 1.5rem;
        color: var(--secondary-color);
        transition: color 0.2s ease;
    }

    .quick-link-item:hover i {
        color: var(--primary-color);
    }

    .quick-link-text {
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.3;
    }

    /* Activity Feed */
    .activity-feed {
        padding: 1rem 1.5rem;
        max-height: 280px;
        overflow-y: auto;
    }
    
    .activity-feed::-webkit-scrollbar {
        width: 6px;
    }
    .activity-feed::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
        margin-right: 5px;
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
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: var(--text-main);
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .activity-content {
        flex-grow: 1;
    }

    .activity-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.2rem;
    }

    .activity-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .filter-select {
        font-size: 0.85rem;
        padding: 0.25rem 2rem 0.25rem 0.75rem;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    /* Developer Alert */
    .dev-alert {
        background: var(--surface-color);
        border-left: 4px solid var(--primary-color);
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .stat-value { font-size: 1.35rem; }
        .stat-label { font-size: 0.75rem; }
        .stat-widget { padding: 1.15rem; gap: 0.85rem; }
        .stat-icon-wrapper { width: 40px; height: 40px; font-size: 1.1rem; }
        .stat-footer { margin-top: 0.75rem; padding-top: 0.75rem; font-size: 0.75rem; }

        .chart-container { height: 260px !important; padding: 0.75rem; }
        .mini-chart-container { height: 180px; padding: 0.75rem; }

        .card-header-clean { padding: 1rem 1.15rem; }
        .card-title-clean { font-size: 0.9rem; }
        .filter-select { font-size: 0.78rem; padding: 0.2rem 1.5rem 0.2rem 0.5rem; }

        .quick-links-grid { grid-template-columns: repeat(2, 1fr); padding: 1rem; gap: 0.6rem; }
        .quick-link-item { padding: 0.85rem 0.5rem; gap: 0.5rem; }
        .quick-link-item i { font-size: 1.2rem; }
        .quick-link-text { font-size: 0.75rem; }

        .activity-feed { padding: 0.75rem 1.15rem; max-height: 240px; }
        .activity-item { gap: 0.75rem; padding: 0.75rem 0; }
        .activity-avatar { width: 34px; height: 34px; font-size: 0.78rem; }
        .activity-title { font-size: 0.82rem; }
        .activity-meta { font-size: 0.72rem; }

        .dev-alert { flex-direction: column; gap: 0.75rem; align-items: flex-start; padding: 1rem; }
        .dev-alert .btn { width: 100%; text-align: center; }
    }

    @media (max-width: 480px) {
        .stat-value { font-size: 1.15rem; }
        .stat-label { font-size: 0.68rem; }
        .stat-icon-wrapper { width: 36px; height: 36px; font-size: 1rem; }

        .chart-container { height: 220px !important; }
        .mini-chart-container { height: 160px; }

        .quick-links-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
    }
</style>
@endsection

@section('content')

    <!-- Kontak Developer Alert -->
    <div class="dev-alert">
        <div class="d-flex align-items-center gap-3">
            <div class="text-primary fs-4"><i class="fas fa-headset"></i></div>
            <div>
                <h6 class="mb-1 fw-bold text-dark">Butuh Bantuan Teknis?</h6>
                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Laporkan kendala/bug pada sistem untuk peningkatan kualitas.</p>
            </div>
        </div>
        <a href="https://wa.me/6282113100791?text=Halo%20Developer,%20saya%20menemukan%20kendala/bug%20pada%20sistem" target="_blank" class="btn btn-sm btn-outline-primary fw-medium px-3">
            <i class="fab fa-whatsapp me-2"></i>Kontak Developer
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <!-- Total Siswa -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($totalSiswa) }}</div>
                        <div class="stat-label">Siswa Aktif</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span>
                        <i class="fas fa-arrow-up text-success me-1"></i>
                        <span class="text-success fw-medium">{{ $siswaBaruBulanIni }}</span> bulan ini
                    </span>
                    <i class="fas fa-users text-muted opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($totalGuru) }}</div>
                        <div class="stat-label">Tenaga Pendidik</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #10b981; background: #ecfdf5;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span>Terdaftar aktif di sistem</span>
                    <i class="fas fa-check-circle text-muted opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($totalKelas) }}</div>
                        <div class="stat-label">Total Kelas</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #f59e0b; background: #fffbeb;">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span>Avg. {{ $totalKelas > 0 ? round($totalSiswa / $totalKelas) : 0 }} siswa/kelas</span>
                    <i class="fas fa-layer-group text-muted opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Total Users (Akun) -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
                        <div class="stat-label">Akun Pengguna</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #8b5cf6; background: #f5f3ff;">
                        <i class="fas fa-id-badge"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span>Telah memiliki kredensial</span>
                    <i class="fas fa-shield-alt text-muted opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="row g-4 mb-4">
        
        <!-- Left Column (Charts) -->
        <div class="col-lg-8 d-flex flex-column gap-4">
            
            <!-- Main Chart -->
            <div class="dashboard-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-chart-area card-title-icon"></i> Grafik Pendaftaran Siswa
                    </h5>
                    <select id="timeFilter" class="form-select filter-select w-auto">
                        <option value="1_tahun">1 Tahun Terakhir</option>
                        <option value="6_bulan" selected>6 Bulan Terakhir</option>
                        <option value="3_bulan">3 Bulan Terakhir</option>
                    </select>
                </div>
                <div class="card-body p-0">
                    <div class="chart-container" style="height: 380px;">
                        <canvas id="registrationChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Minor Charts Row -->
            <div class="row g-4 flex-grow-1">
                <div class="col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header-clean">
                            <h5 class="card-title-clean">
                                <i class="fas fa-chart-pie card-title-icon"></i> Distribusi Gender
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="mini-chart-container d-flex justify-content-center">
                                <canvas id="genderChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header-clean">
                            <h5 class="card-title-clean">
                                <i class="fas fa-chart-bar card-title-icon"></i> Siswa per Kelas
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="mini-chart-container">
                                <canvas id="classChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column (Links & Activities) -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <!-- Quick Links -->
            <div class="dashboard-card">
                <div class="card-header-clean border-bottom-0 pb-2">
                    <h5 class="card-title-clean">
                        <i class="fas fa-th-large card-title-icon"></i> Akses Modul Utama
                    </h5>
                </div>
                <!-- Custom Tabs Fixed -->
                <div class="px-2 pb-2 border-bottom" style="border-color: var(--border-color) !important;">
                    <ul class="nav nav-pills nav-justified custom-nav-pills flex-column flex-sm-row" role="tablist" style="gap: 0.25rem; font-size: 0.8rem;">
                        <li class="nav-item">
                            <button type="button" class="nav-link active py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-akdmk" aria-selected="true" style="font-weight: 600;">Akademik</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-kuang" aria-selected="false" style="font-weight: 600;">Keuangan</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sistem" aria-selected="false" style="font-weight: 600;">Sistem</button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content p-0" style="background: transparent; border: none; box-shadow: none;">
                    <!-- Akademik Tab -->
                    <div class="tab-pane fade show active" id="tab-akdmk" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('admin.kelas.index') }}" class="quick-link-item">
                                <i class="fas fa-school text-primary"></i>
                                <span class="quick-link-text">Data Kelas</span>
                            </a>
                            <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="quick-link-item">
                                <i class="fas fa-calendar-alt text-success"></i>
                                <span class="quick-link-text">Jadwal</span>
                            </a>
                            <a href="{{ route('admin.mata-pelajaran.index') }}" class="quick-link-item">
                                <i class="fas fa-book text-warning"></i>
                                <span class="quick-link-text">Mata Pelajaran</span>
                            </a>
                            <a href="{{ route('admin.akademik.kalender.index') }}" class="quick-link-item">
                                <i class="fas fa-calendar-check text-info"></i>
                                <span class="quick-link-text">Kalender</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Keuangan Tab -->
                    <div class="tab-pane fade" id="tab-kuang" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('admin.keuangan.tagihan.index') }}" class="quick-link-item">
                                <i class="fas fa-file-invoice-dollar text-warning"></i>
                                <span class="quick-link-text">Daftar Tagihan</span>
                            </a>
                            <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="quick-link-item">
                                <i class="fas fa-money-bill-wave text-success"></i>
                                <span class="quick-link-text">Pembayaran</span>
                            </a>
                            <a href="{{ route('admin.keuangan.laporan.index') }}" class="quick-link-item">
                                <i class="fas fa-clipboard-list text-primary"></i>
                                <span class="quick-link-text">Lap. Keuangan</span>
                            </a>
                            <a href="{{ route('admin.keuangan.info-pembayaran.index') }}" class="quick-link-item">
                                <i class="fas fa-cogs text-secondary"></i>
                                <span class="quick-link-text">Config Payment</span>
                            </a>
                        </div>
                    </div>

                    <!-- Sistem & Pengguna Tab -->
                    <div class="tab-pane fade" id="tab-sistem" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('admin.users.siswa') }}" class="quick-link-item">
                                <i class="fas fa-user-graduate text-primary"></i>
                                <span class="quick-link-text">Data Siswa</span>
                            </a>
                            <a href="{{ route('admin.users.tenaga-pendidik') }}" class="quick-link-item">
                                <i class="fas fa-chalkboard-teacher text-success"></i>
                                <span class="quick-link-text">Tenaga Pendidik</span>
                            </a>
                            <a href="{{ route('admin.lms-settings.index') }}" class="quick-link-item">
                                <i class="fas fa-sliders-h text-secondary"></i>
                                <span class="quick-link-text">Pengaturan LMS</span>
                            </a>
                            <a href="{{ route('admin.ai-settings.index') }}" class="quick-link-item">
                                <i class="fas fa-robot text-info"></i>
                                <span class="quick-link-text">Setting AI</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Logins -->
            <div class="dashboard-card flex-grow-1">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-history card-title-icon"></i> Login Pengguna Terbaru
                    </h5>
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
                        <p class="mb-0">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Styling Defaults for minimal look
    Chart.defaults.font.family = "'Inter', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.scale.grid.color = '#e2e8f0';

    // Data passed from backend
    const chartData = @json($chartPendaftaran);
    const genderData = @json($genderData);
    const classLabels = @json($kelasLabels);
    const classCounts = @json($kelasCounts);

    // 1. Registration Line Chart
    const ctxReg = document.getElementById('registrationChart');
    let regChart;

    if (ctxReg) {
        const initRegChart = (timeframe) => {
            const dataObj = chartData[timeframe];
            
            if(regChart) regChart.destroy();

            regChart = new Chart(ctxReg, {
                type: 'line',
                data: {
                    labels: dataObj.labels,
                    datasets: [{
                        label: 'Siswa Baru',
                        data: dataObj.data,
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4361ee',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#334155',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            border: { dash: [4, 4] },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        };

        // Initialize with default 6 months
        initRegChart('6_bulan');

        // Handle Filter Change
        document.getElementById('timeFilter').addEventListener('change', function(e) {
            initRegChart(e.target.value);
        });
    }

    // 2. Gender Doughnut Chart
    const ctxGender = document.getElementById('genderChart');
    if (ctxGender) {
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [genderData['L'] || 0, genderData['P'] || 0],
                    backgroundColor: ['#4361ee', '#ec4899'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                    }
                }
            }
        });
    }

    // 3. Class Bar Chart
    const ctxClass = document.getElementById('classChart');
    if (ctxClass) {
        new Chart(ctxClass, {
            type: 'bar',
            data: {
                labels: classLabels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: classCounts,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    barThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: { precision: 0, display: false },
                        grid: { display: false }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endsection
