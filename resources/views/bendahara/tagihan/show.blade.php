@extends('layouts.sneat')

@section('title', 'Detail Tagihan - ' . $siswa->nama_lengkap)
@section('page-title', 'Detail Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .info-table td { padding: 8px 0; }
    .info-table td:first-child { color: #64748b; width: 140px; }
    .stat-box { padding: 16px; border-radius: 8px; }
    .stat-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
    .stat-value { font-size: 20px; font-weight: 700; }
    
    @media (max-width: 768px) {
        .table-responsive {
            border: none !important;
        }
        .table-responsive table {
            border-collapse: separate;
            border-spacing: 0 1rem;
        }
        .table-responsive thead {
            display: none;
        }
        .table-responsive tbody tr {
            display: block;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }
        .table-responsive tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right !important;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px dashed #e2e8f0;
        }
        .table-responsive tfoot tr {
            display: block;
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
        }
        .table-responsive tfoot td {
            display: flex;
            justify-content: space-between;
            padding: 0;
            border: none;
        }
        .table-responsive tbody td:last-child {
            border-bottom: none;
        }
        .table-responsive tbody td::before {
            content: attr(data-label);
            display: block;
            font-weight: 700;
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            margin-right: 1rem;
            text-align: left;
        }
        .action-buttons-wrapper .btn {
            width: 100%;
            margin-bottom: 0.5rem;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('bendahara.tagihan.index') }}" class="text-primary text-decoration-none">
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
    <div class="d-flex flex-wrap gap-2 mb-4 action-buttons-wrapper">
        <a href="{{ route('bendahara.tagihan.edit', $siswa->id) }}" class="btn btn-warning shadow-sm">
            <i class="fas fa-edit me-1"></i> Edit Tagihan
        </a>
        <a href="{{ route('bendahara.pembayaran.create', $siswa->id) }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus me-1"></i> Input Pembayaran
        </a>
        <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $siswa->id) }}" class="btn btn-info shadow-sm">
            <i class="fas fa-history me-1"></i> Riwayat Pembayaran
        </a>
        <a href="{{ route('bendahara.tagihan.cetak', $siswa->id) }}" class="btn btn-secondary shadow-sm" target="_blank">
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
                    <a href="{{ route('bendahara.tagihan.edit', $siswa->id) }}" class="btn btn-primary shadow-sm">
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
                                    <td class="text-center align-middle fw-bold text-gray-600" data-label="No">{{ $loop->iteration }}</td>
                                    <td class="align-middle" data-label="Jenis Tagihan">
                                        <strong>{{ $jenisTagihan[$item->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</strong>
                                        @if($item->tagihan_asal_id && $item->tagihanAsal)
                                            <br><span class="badge" style="background:#fff7ed;color:#c2410c;font-size:10px;font-weight:600;padding:3px 7px;border-radius:6px;">
                                                <i class="fas fa-arrow-right"></i> Carryover dari {{ $item->tagihanAsal->tahunAjaran->nama_tahun_ajaran ?? 'TA lama' }}
                                            </span>
                                        @endif
                                        @if($item->dialihkan_ke_id && $item->tagihanAlihan)
                                            <br><span class="badge" style="background:#dbeafe;color:#1e40af;font-size:10px;font-weight:600;padding:3px 7px;border-radius:6px;">
                                                <i class="fas fa-share"></i> Dialihkan ke {{ $item->tagihanAlihan->tahunAjaran->nama_tahun_ajaran ?? 'TA aktif' }}
                                            </span>
                                        @endif
                                        @if($item->keterangan)
                                            <br><small class="text-muted">{{ $item->keterangan }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-end fw-bold" data-label="Jumlah">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td class="align-middle text-center" data-label="Jatuh Tempo">{{ $item->tanggal_jatuh_tempo ? $item->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td>
                                    <td class="align-middle text-center" data-label="Status">
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
                                <td colspan="2" class="fw-bold d-none d-md-table-cell">Total</td>
                                <div class="d-md-none fw-bold mb-2">Total Semua Tagihan</div>
                                <td class="fw-bold text-end text-primary" style="font-size: 1.1rem;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                                <td colspan="2" class="d-none d-md-table-cell"></td>
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