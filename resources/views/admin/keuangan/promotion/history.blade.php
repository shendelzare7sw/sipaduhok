@extends('layouts.sneat')

@section('title', 'Admin - Riwayat Dispensasi')
@section('page-title', 'Riwayat Dispensasi')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* Responsive Styles - Mobile Only */
@media (max-width: 768px) {
    .table-responsive.text-nowrap {
        white-space: normal !important;
        overflow-x: visible !important;
    }
    .table-card-mobile { white-space: normal !important; }
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block; border: 1px solid #e5e7eb; border-radius: 12px;
        margin-bottom: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff; position: relative;
    }
    .table-card-mobile tbody td {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border: none !important;
        border-bottom: 1px solid #f3f4f6 !important; text-align: right;
        white-space: normal !important; word-break: break-word;
    }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label); font-weight: 700; font-size: 10px;
        text-transform: uppercase; color: #9ca3af; letter-spacing: 0.5px;
        text-align: left; flex-shrink: 0; margin-right: 12px;
    }
    .table-card-mobile .mobile-card-head {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        font-weight: 700; font-size: 15px; padding: 14px !important;
        border-bottom: 2px solid #e0e7ff !important; display: block !important;
        text-align: left;
    }
    .mobile-text-end { text-align: right; }
    .desktop-only-cell { display: none !important; }
    .force-d-flex-mobile { display: flex !important; }
}
@media (min-width: 769px) {
    .mobile-only-cell { display: none !important; }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Pengajuan Dispensasi
                    </h5>
                    <a href="{{ route('admin.keuangan.promotion.validation.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.keuangan.promotion.validation.history') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Cari Siswa</label>
                                <input type="text" name="q" class="form-control" placeholder="Nama atau NIS..." value="{{ $filters['q'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang" class="form-select">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}" {{ ($filters['cabang'] ?? '') == $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ ($filters['kelas'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="DISETUJUI" {{ ($filters['status'] ?? '') == 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="DITOLAK" {{ ($filters['status'] ?? '') == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover table-card-mobile align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Siswa</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Diajukan Oleh</th>
                                    <th>Disetujui/Ditolak Oleh</th>
                                    <th>Tanggal Keputusan</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                <tr>
                                    <td class="mobile-card-head">
                                        <div class="d-flex justify-content-between align-items-center gap-2" style="width: 100%;">
                                            <span class="text-wrap text-break lh-sm fw-semibold" style="flex: 1;">{{ $item->nama_siswa }}</span>
                                            <span class="mobile-only-cell flex-shrink-0 ms-auto">
                                                @if($item->status == 'DISETUJUI')
                                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i></span>
                                                @elseif($item->status == 'DITOLAK')
                                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i></span>
                                                @else
                                                    <span class="text-secondary"><i class="bi bi-clock-fill"></i></span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Tgl Pengajuan" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->locale('id')->translatedFormat('d F Y') }}</div>
                                    </td>
                                    <td data-label="Kelas" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ $item->nama_kelas }}</div>
                                    </td>
                                    <td data-label="Status" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">
                                            @if($item->status == 'DISETUJUI')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                            @elseif($item->status == 'DITOLAK')
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->status }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Diajukan Oleh" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ $item->pengaju }}</div>
                                    </td>
                                    <td data-label="Disetujui Oleh" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ $item->penyetuju ?? '-' }}</div>
                                    </td>
                                    <td data-label="Tgl Keputusan" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">
                                            @if($item->tanggal_persetujuan)
                                                {{ \Carbon\Carbon::parse($item->tanggal_persetujuan)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Catatan" class="force-d-flex-mobile">
                                        <div class="mobile-text-end text-wrap text-break lh-sm">{{ $item->catatan_ketua ?? '-' }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada riwayat pengajuan.
                                    </td>
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
