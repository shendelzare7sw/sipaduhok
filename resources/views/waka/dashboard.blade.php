@extends('layouts.sneat')

@section('title', 'Dashboard Wakil Kepala Sekolah')

@section('page-title', 'Dashboard Wakil Kepala Sekolah')
@section('page-subtitle', 'Manajemen akademik dan monitoring kegiatan pendidikan')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
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

    .welcome-card {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
        color: white;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
    }

    .welcome-card .card-body {
        padding: 2rem;
    }

    .alert-card {
        border-left: 4px solid #ffc107;
        background: #fff8e1;
    }

    .table-card {
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-2">Selamat Datang, {{ auth()->user()->name }}!</h4>
                            <p class="text-white-50 mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Tahun Ajaran:
                                @if($tahunAjaranAktif)
                                    <strong>{{ $tahunAjaranAktif->tahun_ajaran }} - {{ ucfirst($tahunAjaranAktif->semester) }}</strong>
                                @else
                                    <span class="badge bg-warning">Tidak ada tahun ajaran aktif</span>
                                @endif
                            </p>
                        </div>
                        <div class="d-none d-md-block">
                            <i class="fas fa-user-tie" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-label-primary me-3">
                            <i class="fas fa-user-graduate text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Siswa Aktif</div>
                            <div class="stat-number text-primary">{{ number_format($stats['totalSiswa']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-label-success me-3">
                            <i class="fas fa-chalkboard-teacher text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Guru</div>
                            <div class="stat-number text-success">{{ number_format($stats['totalGuru']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-label-info me-3">
                            <i class="fas fa-school text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Kelas</div>
                            <div class="stat-number text-info">{{ number_format($stats['totalKelas']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-label-warning me-3">
                            <i class="fas fa-book text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Total Mata Pelajaran</div>
                            <div class="stat-number text-warning">{{ number_format($stats['totalMapel']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wali Kelas Status -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-label-success me-3">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Kelas dengan Wali Kelas</div>
                            <div class="stat-number text-success">{{ number_format($stats['kelasWithWali']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-label-danger me-3">
                            <i class="fas fa-user-times text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Kelas tanpa Wali Kelas</div>
                            <div class="stat-number text-danger">{{ number_format($stats['kelasWithoutWali']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Kelas Tanpa Wali -->
        @if($kelasWithoutWali->count() > 0)
        <div class="col-lg-6">
            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Kelas Belum Ada Wali Kelas
                    </h5>
                    <a href="{{ route('waka.wali-kelas.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Kelola
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Jenjang</th>
                                    <th>Cabang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelasWithoutWali as $kelas)
                                <tr>
                                    <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                                    <td><span class="badge bg-info">{{ $kelas->jenjang }}</span></td>
                                    <td>{{ $kelas->cabang->nama_cabang ?? '-' }}</td>
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
        <div class="col-lg-6">
            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2 text-primary"></i>
                        Siswa Terbaru
                    </h5>
                    <a href="{{ route('waka.manajemen-siswa.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSiswa as $siswa)
                                <tr>
                                    <td>
                                        <strong>{{ $siswa->nama_lengkap }}</strong>
                                        <br><small class="text-muted">{{ $siswa->nis }}</small>
                                    </td>
                                    <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $siswa->status == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($siswa->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data siswa</td>
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
@endsection
