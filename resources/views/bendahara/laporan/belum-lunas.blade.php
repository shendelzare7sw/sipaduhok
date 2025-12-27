@extends('layouts.sneat')

@section('title', 'Laporan Siswa Belum Lunas')
@section('page-title', 'Laporan Siswa Belum Lunas')
@section('page-subtitle', 'Daftar siswa dengan tagihan belum lunas')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* === CSS DARI REFERENSI MANAJEMEN CABANG === */
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
    font-size: 24px; /* Disesuaikan untuk nominal Rp agar tidak overflow */
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

/* Table Styling */
.table thead th {
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.progress-bar-custom {
    height: 8px;
    border-radius: 4px;
    background: #e5e7eb;
    overflow: hidden;
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('bendahara.laporan.belum-lunas') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="form-label fw-bold small text-muted">KELAS</label>
                    <select name="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border:none;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('bendahara.laporan.cetak-belum-lunas', request()->query()) }}" class="btn btn-success ms-2" target="_blank" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border:none;">
                        <i class="fas fa-print me-1"></i> Cetak Laporan
                    </a>
                    <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="btn btn-outline-secondary border ms-2">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards Section (PENERAPAN GAYA REFERENSI) --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-gradient-red">
                <div class="stat-content">
                    <div class="stat-title">Siswa Belum Lunas</div>
                    <div class="stat-number">{{ $siswaList->count() }}</div>
                    <div class="stat-desc">Siswa yang menunggak</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Total Sisa Tagihan</div>
                    <div class="stat-number">Rp {{ number_format($siswaList->sum('sisa_tagihan'), 0, ',', '.') }}</div>
                    <div class="stat-desc">Piutang berjalan</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Tagihan</div>
                    <div class="stat-number">Rp {{ number_format($siswaList->sum('total_tagihan'), 0, ',', '.') }}</div>
                    <div class="stat-desc">Target keseluruhan</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Total Terbayar</div>
                    <div class="stat-number">Rp {{ number_format($siswaList->sum('total_bayar'), 0, ',', '.') }}</div>
                    <div class="stat-desc">Sudah divalidasi</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Section (Field Utuh Sesuai Permintaan) --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list-ul me-2 text-primary"></i>Daftar Rincian Tunggakan</h5>
            <span class="badge bg-danger">{{ $siswaList->count() }} Data Ditemukan</span>
        </div>
        <div class="card-body p-0">
            @if($siswaList->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">NISN</th>
                                <th>Kelas</th>
                                <th>Total Tagihan</th>
                                <th>Sudah Bayar</th>
                                <th>Sisa Tagihan</th>
                                <th width="120">Persentase</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $index => $siswa)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-primary fw-bold">{{ $siswa->cabang->nama_cabang ?? '-' }}</small>
                                    </td>
                                    <td class="text-center align-middle">{{ $siswa->nisn }}</td>
                                    <td class="align-middle fw-bold text-gray-800">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="align-middle fw-bold">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="align-middle text-success fw-bold">Rp {{ number_format($siswa->total_bayar, 0, ',', '.') }}</td>
                                    <td class="align-middle text-danger fw-bold">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td>
                                    <td class="align-middle">
                                        @php
                                            $persentase = $siswa->total_tagihan > 0 ? round(($siswa->total_bayar / $siswa->total_tagihan) * 100, 1) : 0;
                                            $progColor = $persentase >= 75 ? '#10b981' : ($persentase >= 50 ? '#f59e0b' : '#ef4444');
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-bar-custom w-100">
                                                <div style="width: {{ $persentase }}%; background: {{ $progColor }}; height: 100%;"></div>
                                            </div>
                                            <span class="small fw-bold" style="color: {{ $progColor }}; min-width: 35px;">{{ $persentase }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-sm btn-info" title="Detail"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $siswa->id) }}" class="btn btn-sm btn-success" title="Riwayat"><i class="fas fa-history"></i></a>
                                            <a href="{{ route('bendahara.pembayaran.create', $siswa->id) }}" class="btn btn-sm btn-warning" title="Input Bayar"><i class="fas fa-plus"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-double fa-3x text-success opacity-25 mb-3"></i>
                    <h5 class="text-muted">Luar Biasa! Semua Tagihan Telah Lunas</h5>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection