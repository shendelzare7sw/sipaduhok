@extends('layouts.sneat')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di SIPADUHOK')

@section('sidebar-menu')
    <!-- Dashboard -->
    <li class="menu-item active">
        <a href="{{ route('dashboard') }}" class="menu-link">
            <i class="menu-icon fas fa-home"></i>
            <div data-i18n="Dashboard">Dashboard</div>
        </a>
    </li>

    <!-- Menu Header -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Menu Utama</span>
    </li>

    <!-- Contoh Menu Item dengan Submenu -->
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon fas fa-users"></i>
            <div data-i18n="Pengguna">Pengguna</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Daftar Pengguna">Daftar Pengguna</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Tambah Pengguna">Tambah Pengguna</div>
                </a>
            </li>
        </ul>
    </li>

    <!-- Menu Item Biasa -->
    <li class="menu-item">
        <a href="#" class="menu-link">
            <i class="menu-icon fas fa-school"></i>
            <div data-i18n="Kelas">Kelas</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="#" class="menu-link">
            <i class="menu-icon fas fa-user-graduate"></i>
            <div data-i18n="Siswa">Siswa</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="#" class="menu-link">
            <i class="menu-icon fas fa-chalkboard-teacher"></i>
            <div data-i18n="Guru">Guru</div>
        </a>
    </li>

    <!-- Menu Header Lainnya -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Laporan</span>
    </li>

    <li class="menu-item">
        <a href="#" class="menu-link">
            <i class="menu-icon fas fa-file-alt"></i>
            <div data-i18n="Cetak Laporan">Cetak Laporan</div>
        </a>
    </li>
@endsection

@section('content')
    <!-- Stats Cards Row -->
    <div class="row">
        <!-- Card 1 -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stat border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Siswa</span>
                            <h3 class="card-title mb-2">1,234</h3>
                            <small class="text-success fw-semibold">
                                <i class="fas fa-arrow-up"></i> +12%
                            </small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="fas fa-user-graduate fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stat border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Kelas</span>
                            <h3 class="card-title mb-2">24</h3>
                            <small class="text-success fw-semibold">
                                <i class="fas fa-arrow-up"></i> +3%
                            </small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="fas fa-school fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stat border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Guru</span>
                            <h3 class="card-title mb-2">48</h3>
                            <small class="text-danger fw-semibold">
                                <i class="fas fa-arrow-down"></i> -2%
                            </small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="fas fa-chalkboard-teacher fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card card-stat border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold d-block mb-1">Cabang Aktif</span>
                            <h3 class="card-title mb-2">3</h3>
                            <small class="text-muted fw-semibold">
                                Tetap
                            </small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="fas fa-building fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Welcome Card -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-rocket text-primary me-2"></i>
                        Selamat Datang di SIPADUHOK!
                    </h5>
                    <p class="mb-3">
                        Sistem Informasi Akademik dan Learning Management System PKBM House Of Knowledge.
                    </p>
                    <p class="text-muted mb-0">
                        Dashboard ini menggunakan <strong>Sneat Bootstrap 5 Template</strong> yang modern, responsif, dan mudah dikustomisasi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            Tambah Siswa Baru
                        </button>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fas fa-file-alt me-2"></i>
                            Lihat Laporan
                        </button>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Jadwal Pelajaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>10 menit lalu</td>
                                    <td>Ahmad Yani</td>
                                    <td>Menambah siswa baru: Budi Santoso</td>
                                    <td><span class="badge bg-success">Berhasil</span></td>
                                </tr>
                                <tr>
                                    <td>25 menit lalu</td>
                                    <td>Siti Nurhaliza</td>
                                    <td>Update jadwal mata pelajaran Matematika</td>
                                    <td><span class="badge bg-success">Berhasil</span></td>
                                </tr>
                                <tr>
                                    <td>1 jam lalu</td>
                                    <td>Dedi Kurniawan</td>
                                    <td>Upload materi pembelajaran Bahasa Indonesia</td>
                                    <td><span class="badge bg-success">Berhasil</span></td>
                                </tr>
                                <tr>
                                    <td>2 jam lalu</td>
                                    <td>Rina Susanti</td>
                                    <td>Validasi pembayaran SPP siswa kelas X</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    console.log('Dashboard Sneat berhasil dimuat!');
</script>
@endsection
