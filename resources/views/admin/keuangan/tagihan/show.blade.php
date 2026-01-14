@extends('layouts.sneat')

@section('title', 'Detail Tagihan - ' . $siswa->nama_lengkap)
@section('page-title', 'Detail Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .info-table td { padding: 8px 0; }
    .info-table td:first-child { color: #64748b; width: 140px; }
    .stat-box { padding: 16px; border-radius: 8px; }
    .stat-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
    .stat-value { font-size: 20px; font-weight: 700; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="text-primary text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
        </a>
    </div>

    {{-- Info Siswa & Ringkasan --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-user-graduate me-2"></i>Informasi Siswa
                    </h6>
                </div>
                <div class="card-body">
                    <table class="info-table w-100">
                        <tr>
                            <td>Nama Lengkap</td>
                            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                        </tr>
                        <tr>
                            <td>NISN</td>
                            <td>{{ $siswa->nisn }}</td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>{{ $siswa->kelas->nama_kelas ?? '-' }} ({{ $siswa->kelas->jenjang ?? '-' }})</td>
                        </tr>
                        <tr>
                            <td>Cabang</td>
                            <td>{{ $siswa->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-calculator me-2"></i>Ringkasan Keuangan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <div class="stat-box bg-primary bg-opacity-10">
                            <div class="stat-label">Total Tagihan</div>
                            <div class="stat-value text-primary">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                        </div>
                        <div class="stat-box bg-success bg-opacity-10">
                            <div class="stat-label">Sudah Bayar</div>
                            <div class="stat-value text-success">Rp {{ number_format($tagihanLunas, 0, ',', '.') }}</div>
                        </div>
                        <div class="stat-box {{ $sisaTagihan > 0 ? 'bg-danger' : 'bg-success' }} bg-opacity-10">
                            <div class="stat-label">Sisa Tagihan</div>
                            <div class="stat-value {{ $sisaTagihan > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.keuangan.tagihan.edit', $siswa->id) }}" class="btn btn-warning shadow-sm">
            <i class="fas fa-edit me-1"></i> Edit Tagihan
        </a>
        <a href="{{ route('admin.keuangan.pembayaran.create', $siswa->id) }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus me-1"></i> Input Pembayaran
        </a>
        <a href="{{ route('admin.keuangan.pembayaran.riwayat-siswa', $siswa->id) }}" class="btn btn-info shadow-sm">
            <i class="fas fa-history me-1"></i> Riwayat Pembayaran
        </a>
        <a href="{{ route('admin.keuangan.tagihan.cetak', $siswa->id) }}" class="btn btn-secondary shadow-sm" target="_blank">
            <i class="fas fa-print me-1"></i> Cetak Tagihan
        </a>
    </div>

    {{-- Rincian Tagihan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-warning">
                <i class="fas fa-file-invoice-dollar me-2"></i>Rincian Tagihan
            </h6>
        </div>
        <div class="card-body">
            @if($tagihan->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-4x mb-3 opacity-50"></i>
                    <p class="mb-3">Belum ada tagihan untuk siswa ini.</p>
                    <a href="{{ route('admin.keuangan.tagihan.edit', $siswa->id) }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Tagihan
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Jenis Tagihan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-center">Jatuh Tempo</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tagihan as $index => $item)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $jenisTagihan[$item->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</strong>
                                    </td>
                                    <td class="align-middle text-end fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td class="align-middle text-center">{{ $item->tanggal_jatuh_tempo ? $item->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td>
                                    <td class="align-middle text-center">
                                        @if($item->status === 'sudah_bayar')
                                            <span class="badge bg-success shadow-sm">Lunas</span>
                                        @elseif($item->status === 'terlambat')
                                            <span class="badge bg-danger shadow-sm">Terlambat</span>
                                        @else
                                            <span class="badge bg-warning shadow-sm">Belum Bayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-bold">Total</td>
                                <td class="fw-bold text-end">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
</div>
@endsection