@extends('layouts.sneat')

@section('title', 'Dashboard Bendahara')
@section('page-title', 'Dashboard Bendahara')
@section('page-subtitle', 'Kelola keuangan dan pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* === STAT CARD STYLE === */
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
    border: none;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1.2;
}

.stat-desc {
    font-size: 13px;
    opacity: 0.8;
}

.stat-icon-bg {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 70px;
    opacity: 0.15;
    z-index: 1;
}

/* Gradients */
.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-gradient-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

/* Table Styling */
.table thead th {
    background-color: #f8f9fc;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    color: #4e73df;
    border-bottom: 2px solid #e3e6f0;
    vertical-align: middle;
}

.currency-text {
    font-family: 'Nunito', sans-serif;
    font-weight: 700;
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- STATS GRID (BARIS ATAS) --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Tagihan</div>
                    <div class="stat-number">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-desc">TA: {{ $tahunAjaran->nama_tahun_ajaran ?? '2025/2026' }}</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Total Terbayar</div>
                    <div class="stat-number">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-desc"><i class="fas fa-check-circle me-1"></i>Tervalidasi</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Bulan Ini</div>
                    <div class="stat-number">Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-desc">{{ now()->translatedFormat('F Y') }}</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-red">
                <div class="stat-content">
                    <div class="stat-title">Menunggu Validasi</div>
                    <div class="stat-number">{{ $pembayaranPending ?? 0 }}</div>
                    <div class="stat-desc">Segera Konfirmasi</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTS / INFO CARDS (BARIS KEDUA) --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card border-start border-warning border-4 shadow py-2 h-100">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Tagihan Belum Lunas</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $tagihanBelumLunas ?? 0 }} Siswa</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-start border-danger border-4 shadow py-2 h-100">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Tagihan Terlambat</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $tagihanTerlambat ?? 0 }} Siswa</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-bolt me-2 text-warning"></i>Aksi Cepat Keuangan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-2">
                    <a href="{{ route('bendahara.pembayaran.index', ['status' => 'pending']) }}" class="btn btn-warning w-100 shadow-sm fw-bold text-white py-2">
                        <i class="fas fa-clock me-2"></i> Validasi ({{ $pembayaranPending ?? 0 }})
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <a href="{{ route('bendahara.tagihan.bulk-create') }}" class="btn btn-primary w-100 shadow-sm fw-bold py-2">
                        <i class="fas fa-plus-circle me-2"></i> Buat Tagihan Massal
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <a href="{{ route('bendahara.validasi-akses.index') }}" class="btn btn-success w-100 shadow-sm fw-bold py-2">
                        <i class="fas fa-shield-alt me-2"></i> Validasi Akses
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <a href="{{ route('bendahara.laporan.index') }}" class="btn btn-info w-100 shadow-sm fw-bold py-2">
                        <i class="fas fa-print me-2"></i> Cetak Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DATA SISWA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Data Siswa & Ringkasan Tagihan</h6>
            <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-sm btn-outline-primary fw-bold px-3">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            @if(isset($siswaRecent) && $siswaRecent->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x text-gray-200 mb-3"></i>
                    <p>Belum ada data siswa terbaru.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">NO</th>
                                <th>IDENTITAS SISWA</th>
                                <th>KELAS</th>
                                <th>TOTAL TAGIHAN</th>
                                <th>SUDAH BAYAR</th>
                                <th>SISA</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaRecent as $index => $siswa)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-muted fw-bold">{{ $siswa->nisn }}</small>
                                    </td>
                                    <td class="align-middle fw-bold text-primary text-uppercase small">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="align-middle currency-text">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="align-middle currency-text text-success">Rp {{ number_format($siswa->total_bayar, 0, ',', '.') }}</td>
                                    <td class="align-middle currency-text {{ $siswa->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($siswa->sisa_tagihan <= 0)
                                            <span class="badge bg-success px-3 py-2 shadow-sm fw-bold">
                                                <i class="fas fa-check me-1"></i> LUNAS
                                            </span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2 shadow-sm fw-bold">
                                                <i class="fas fa-times me-1"></i> BELUM LUNAS
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-info btn-sm rounded-circle shadow-sm p-2" title="Lihat Riwayat Tagihan">
                                            <i class="fas fa-eye fa-fw"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
