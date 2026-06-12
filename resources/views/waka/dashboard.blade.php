@extends('layouts.sneat')

@section('title', 'Dashboard Wakil Kepala Sekolah')

@section('page-title', 'Dashboard Wakil Kepala Sekolah')
@section('page-subtitle', 'Manajemen akademik dan monitoring kegiatan pendidikan')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/dashboard.css'])
@endsection

@section('content')

    <!-- Top Header & Date -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start gap-3 mb-4">
        <h5 class="mb-0 fw-bold text-dark dashboard-heading-title">
            <i class="fas fa-chart-pie me-2 text-primary"></i> Ringkasan Akademik
        </h5>
        
        <div class="d-flex flex-wrap gap-2">
            @if($tahunAjaranAktif)
                <span class="badge bg-white text-dark px-3 py-2 fs-6 rounded-pill shadow-sm border dashboard-pill">
                    <i class="fas fa-flag-checkered me-2 text-primary"></i> TA: {{ $tahunAjaranAktif->nama_tahun_ajaran }}
                </span>
            @endif
            <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill shadow-sm border dashboard-pill">
                <i class="fas fa-calendar-alt me-2"></i> {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">
        
        <!-- Left Column: Tasks / Information / Tables -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- Information Alert -->
            <div class="alert alert-primary d-flex align-items-center rounded-3 shadow-none border-0 m-0 dashboard-info-alert" role="alert">
                <i class="fas fa-info-circle fs-4 me-3 text-primary"></i>
                <div class="dashboard-alert-text">
                    <strong>Pusat Pengawasan Akademik:</strong> Anda bertanggung jawab mengontrol kegiatan akademik. Gunakan pintasan di sebelah kanan untuk akses cepat.
                </div>
            </div>

            <!-- Stats Grid within Main Column -->
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="dashboard-card border-0 shadow-sm h-100">
                        <div class="stat-widget h-100">
                            <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($stats['totalSiswa']) }}</div>
                                <div class="stat-label">Siswa Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 col-sm-6">
                    <div class="dashboard-card border-0 shadow-sm h-100">
                        <div class="stat-widget h-100">
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($stats['totalGuru']) }}</div>
                                <div class="stat-label">Total Guru</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="dashboard-card border-0 shadow-sm h-100">
                        <div class="stat-widget h-100">
                            <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($stats['totalKelas']) }}</div>
                                <div class="stat-label">Total Kelas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="dashboard-card border-0 shadow-sm h-100">
                        <div class="stat-widget h-100">
                            <div class="stat-icon-wrapper bg-secondary bg-opacity-10 text-secondary">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($stats['totalMapel']) }}</div>
                                <div class="stat-label">Mata Pelajaran</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="dashboard-card border-0 shadow-sm h-100">
                        <div class="stat-widget h-100">
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($stats['kelasWithWali']) }}</div>
                                <div class="stat-label">Punya Wali Kelas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="dashboard-card border-0 shadow-sm h-100 {{ $stats['kelasWithoutWali'] > 0 ? 'border border-danger border-2' : '' }}">
                        <div class="stat-widget h-100">
                            <div class="stat-icon-wrapper {{ $stats['kelasWithoutWali'] > 0 ? 'bg-danger text-danger' : 'bg-secondary text-secondary' }} bg-opacity-10">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value {{ $stats['kelasWithoutWali'] > 0 ? 'text-danger' : '' }}">{{ number_format($stats['kelasWithoutWali']) }}</div>
                                <div class="stat-label">Tanpa Wali Kelas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Section Moved Inside Left Column -->
            <div class="row g-3 flex-grow-1">
                <!-- Kelas Tanpa Wali -->
                @if($kelasWithoutWali->count() > 0)
                <div class="col-md-6">
                    <div class="dashboard-card h-100 d-flex flex-column border-danger border-opacity-50">
                        <div class="card-header-clean d-flex justify-content-between align-items-center bg-danger bg-opacity-10 border-bottom-0">
                            <h5 class="card-title-clean text-danger dashboard-title-sm">
                                <i class="fas fa-exclamation-triangle card-title-icon text-danger"></i> Kelas Tanpa Wali
                            </h5>
                            <a href="{{ route('waka.wali-kelas.index') }}" class="btn btn-sm btn-danger shadow-sm dashboard-action-sm">Kelola</a>
                        </div>
                        <div class="card-body p-0 flex-grow-1">
                            <div class="table-responsive table-scroll">
                                <table class="table table-sm table-hover mb-0 dashboard-table-sm">
                                    <thead class="table-light position-sticky top-0 dashboard-sticky-head">
                                        <tr>
                                            <th class="ps-3">Kelas</th>
                                            <th>Cabang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kelasWithoutWali as $kelas)
                                        <tr>
                                            <td class="fw-bold ps-3">
                                                {{ $kelas->nama_kelas }}<br>
                                                <span class="badge bg-label-info dashboard-badge-xs">{{ $kelas->jenjang }}</span>
                                            </td>
                                            <td class="align-middle">{{ Str::limit($kelas->cabang->nama_cabang ?? '-', 20) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Students -->
                <div class="{{ $kelasWithoutWali->count() > 0 ? 'col-md-6' : 'col-12' }}">
                    <div class="dashboard-card h-100 d-flex flex-column">
                        <div class="card-header-clean d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <h5 class="card-title-clean dashboard-title-sm">
                                <i class="fas fa-user-graduate card-title-icon text-primary"></i> Siswa Pendaftar
                            </h5>
                            <a href="{{ route('waka.manajemen-siswa.index') }}" class="btn btn-sm btn-outline-primary shadow-sm dashboard-action-sm">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0 flex-grow-1">
                            <div class="table-responsive table-scroll">
                                <table class="table table-sm table-hover mb-0 dashboard-table-sm">
                                    <thead class="table-light position-sticky top-0 dashboard-sticky-head">
                                        <tr>
                                            <th class="ps-3">Siswa</th>
                                            <th>Status / Kelas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSiswa as $siswa)
                                        <tr>
                                            <td class="ps-3">
                                                <span class="fw-bold text-dark">{{ $siswa->nama_lengkap }}</span><br>
                                                <small class="text-muted">{{ $siswa->nis ?? '-' }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-label-{{ $siswa->status == 'aktif' ? 'success' : 'secondary' }} d-block mb-1 dashboard-status-badge">
                                                    {{ ucfirst($siswa->status) }}
                                                </span>
                                                <span class="fw-medium">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-4">Belum ada data.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
                <div class="px-2 pb-2 border-bottom dashboard-border-muted">
                    <ul class="nav nav-pills nav-justified custom-nav-pills flex-column flex-sm-row" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-akademik" aria-selected="true">Akademik</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-nilai" aria-selected="false">Kenaikan</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link py-2 px-1" role="tab" data-bs-toggle="tab" data-bs-target="#tab-monitor" aria-selected="false">Monitoring</button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content p-0 flex-grow-1 dashboard-tab-content">
                    
                    <!-- Akademik Tab -->
                    <div class="tab-pane fade show active h-100" id="tab-akademik" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('waka.kelas.index') }}" class="quick-link-item">
                                <i class="fas fa-school text-primary"></i>
                                <span class="quick-link-text">Data Kelas</span>
                            </a>
                            <a href="{{ route('waka.mata-pelajaran.index') }}" class="quick-link-item">
                                <i class="fas fa-book text-info"></i>
                                <span class="quick-link-text">Mata Pelajaran</span>
                            </a>
                            <a href="{{ route('waka.jadwal-pelajaran.index') }}" class="quick-link-item">
                                <i class="fas fa-clipboard-list text-warning"></i>
                                <span class="quick-link-text">Jadwal Belajar</span>
                            </a>
                            <a href="{{ route('waka.wali-kelas.index') }}" class="quick-link-item">
                                <i class="fas fa-user-check text-success"></i>
                                <span class="quick-link-text">Penugasan Wali</span>
                            </a>
                        </div>
                    </div>

                    <!-- Kenaikan & Nilai Tab -->
                    <div class="tab-pane fade h-100" id="tab-nilai" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('waka.promotion.kkm.index') }}" class="quick-link-item">
                                <i class="fas fa-chart-line text-success"></i>
                                <span class="quick-link-text">Pengaturan KKM</span>
                            </a>
                            <a href="{{ route('waka.promotion.settings.index') }}" class="quick-link-item">
                                <i class="fas fa-cogs text-secondary"></i>
                                <span class="quick-link-text">Setting Naik Kelas</span>
                            </a>
                            <a href="{{ route('waka.promotion.report') }}" class="quick-link-item quick-link-wide">
                                <i class="fas fa-file-signature text-primary"></i>
                                <span class="quick-link-text">Proses & Rekap Kenaikan</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Monitoring Tab -->
                    <div class="tab-pane fade h-100" id="tab-monitor" role="tabpanel">
                        <div class="quick-links-grid align-content-start pb-4">
                            <a href="{{ route('waka.monitoring.siswa') }}" class="quick-link-item">
                                <i class="fas fa-user-graduate text-primary"></i>
                                <span class="quick-link-text">Monitor Siswa</span>
                            </a>
                            <a href="{{ route('waka.monitoring.guru-pengajar') }}" class="quick-link-item">
                                <i class="fas fa-user-tie text-success"></i>
                                <span class="quick-link-text">Monitor Guru</span>
                            </a>
                            <a href="{{ route('waka.monitoring.wali-kelas') }}" class="quick-link-item">
                                <i class="fas fa-chalkboard-teacher text-info"></i>
                                <span class="quick-link-text">Monitor Wali</span>
                            </a>
                            <a href="{{ route('waka.catatan.index') }}" class="quick-link-item">
                                <i class="fas fa-sticky-note text-warning"></i>
                                <span class="quick-link-text">Kirim Catatan</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Kontak Developer Warning -->
                <div class="p-3 mx-2 mb-3 mt-auto rounded dashboard-bug-card">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-laptop-code text-info me-2"></i>
                        <span class="fw-bold text-info dashboard-bug-title">Menemukan Bug?</span>
                    </div>
                    <a href="https://wa.me/6282113100791?text=Halo%20Developer,%20saya%20menemukan%20kendala/bug%20pada%20sistem" target="_blank" class="btn btn-sm btn-info text-white w-100 fw-medium">
                        <i class="fab fa-whatsapp me-1"></i> Hubungi Developer
                    </a>
                </div>
                
            </div>
        </div>
    </div>
@endsection
