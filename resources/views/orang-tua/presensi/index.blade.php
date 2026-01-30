@extends('layouts.sneat')

@section('title', 'Presensi - ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* === 1. PERBAIKAN STAT CARD VIBRANT (KOTAK BERWARNA SOLID) === */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card-vibrant {
        display: block !important;
        padding: 24px !important;
        border-radius: 15px !important;
        position: relative !important;
        overflow: hidden !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        transition: transform 0.3s ease !important;
        min-height: 120px !important;
    }
    .stat-card-vibrant:hover { transform: translateY(-5px) !important; }

    .stat-number { font-size: 36px !important; font-weight: 800 !important; line-height: 1 !important; display: block !important; margin-bottom: 5px !important; }
    .stat-title { font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; opacity: 0.9 !important; display: block !important; letter-spacing: 1px !important; }
    .stat-icon-bg { position: absolute !important; right: 15px; top: 50% !important; transform: translateY(-50%) !important; font-size: 60px !important; opacity: 0.2 !important; color: white !important; }

    /* Gradien Warna Solid */
    .bg-grad-green  { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; }
    .bg-grad-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; }
    .bg-grad-blue   { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; }
    .bg-grad-red    { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; }

    /* === 2. CARD CUSTOM DESIGN === */
    .card-custom {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .card-header-custom {
        padding: 15px 25px;
        background: #f8f9fc;
        border-bottom: 1px solid #edf2f9;
        color: #4e73df;
    }
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Presensi</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Presensi Kehadiran</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <div>
            <a href="{{ route('orang-tua.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-lg">
                        <div class="avatar-initial rounded-circle bg-label-primary">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h5 class="mb-1">{{ $siswa->nama_lengkap }}</h5>
                    <div class="text-muted small">
                        <span class="me-3">
                            <i class="fas fa-id-card me-1"></i>NISN: {{ $siswa->nisn }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                        </span>
                        <span>
                            <i class="fas fa-building me-1"></i>{{ $siswa->cabang->nama_cabang ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card-vibrant bg-grad-green">
            <div class="stat-content">
                <div class="stat-title">Hari Hadir</div>
                <div class="stat-number">{{ $rekap['hadir'] }}</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
        </div>

        <div class="stat-card-vibrant bg-grad-orange">
            <div class="stat-content">
                <div class="stat-title">Hari Sakit</div>
                <div class="stat-number">{{ $rekap['sakit'] }}</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-notes-medical"></i></div>
        </div>

        <div class="stat-card-vibrant bg-grad-blue">
            <div class="stat-content">
                <div class="stat-title">Hari Izin</div>
                <div class="stat-number">{{ $rekap['izin'] }}</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-file-alt"></i></div>
        </div>

        <div class="stat-card-vibrant bg-grad-red">
            <div class="stat-content">
                <div class="stat-title">Hari Alpha</div>
                <div class="stat-number">{{ $rekap['alpha'] }}</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>

    <!-- Header with Action Button -->
    <div class="card-custom mb-4">
        <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fas fa-calendar-check me-2 text-primary"></i>Riwayat Presensi Bulan Ini
                </h5>
                <small class="text-muted fw-bold">
                    Bulan {{ now()->translatedFormat('F Y') }}
                </small>
            </div>
            <a href="{{ route('orang-tua.presensi.ajukan-izin', $siswa->id) }}"
               class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm text-white">
                <i class="fas fa-file-medical me-2"></i>AJUKAN IZIN / SAKIT
            </a>
        </div>
    </div>

    <!-- Presensi List -->
    @forelse($presensi as $minggu => $dataList)
    <div class="card-custom mb-4 shadow-sm">
        <div class="card-header-custom fw-bold py-3 px-4">
            <i class="fas fa-calendar-week me-2"></i>MINGGU KE-{{ $minggu }}
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Hari</th>
                        <th class="text-center">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataList as $item)
                    <tr>
                        <td class="ps-4 fw-bold text-dark align-middle">{{ $item->tanggal->format('d F Y') }}</td>
                        <td class="align-middle text-muted">{{ ucfirst($item->tanggal->locale('id')->dayName) }}</td>
                        <td class="text-center align-middle">
                            @php
                                $statusLabel = strtoupper($item->status);
                                $badgeClass = 'secondary';
                                
                                if ($item->status === 'hadir') {
                                    $badgeClass = 'success';
                                } elseif ($item->status === 'sakit' || $item->status === 'izin') {
                                    if ($item->status_validasi === 'disetujui') {
                                        $badgeClass = ($item->status === 'sakit') ? 'warning' : 'info';
                                    } elseif ($item->status_validasi === 'ditolak') {
                                        $statusLabel = 'DITOLAK';
                                        $badgeClass = 'danger';
                                    } else {
                                        $statusLabel = 'MENUNGGU VALIDASI';
                                        $badgeClass = 'warning';
                                    }
                                } elseif ($item->status === 'alpha') {
                                    if ($item->status_validasi === 'ditolak') {
                                        $statusLabel = 'DITOLAK';
                                    }
                                    $badgeClass = 'danger';
                                }
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold bg-{{ $badgeClass }} shadow-sm">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="align-middle small text-muted">
                            <div>{{ $item->keterangan ?? 'Tidak ada catatan' }}</div>
                            @if($item->bukti_file)
                                <a href="{{ asset('storage/' . $item->bukti_file) }}" target="_blank" class="text-primary fw-bold text-decoration-none mt-1 d-inline-block">
                                    <i class="fas fa-paperclip me-1"></i>Lihat Bukti
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="card-custom text-center p-5">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3" style="opacity: 0.2;"></i>
        <h5 class="text-muted fw-bold">Belum Ada Data Presensi Bulan Ini</h5>
        <p class="text-muted mb-0 small">Data presensi akan diperbarui otomatis oleh wali kelas setelah pembelajaran.</p>
    </div>
    @endforelse

</div>
@endsection
