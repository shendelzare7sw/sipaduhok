@extends('layouts.sneat')

@section('title', 'Manajemen Cabang')

@section('page-title', 'Manajemen Cabang')
@section('page-subtitle', 'Kelola data cabang/lokasi PKBM House of Knowledge')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Stats Cards */
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
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
    font-size: 38px;
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

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-gradient-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

/* Grid System */
.row { display: flex; flex-wrap: wrap; margin: -12px; }
.col-md-3 { flex: 0 0 25%; max-width: 25%; padding: 12px; }
.col-12 { flex: 0 0 100%; max-width: 100%; padding: 12px; }

@media (max-width: 992px) {
    .col-md-3 { flex: 0 0 50%; max-width: 50%; }
}

@media (max-width: 576px) {
    .col-md-3 { flex: 0 0 100%; max-width: 100%; }
}

/* Card */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
    border: none;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.card-body {
    padding: 24px;
}

/* Search & Filter */
.filter-section {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 10px 16px 10px 42px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    width: 280px;
    transition: all 0.3s;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.filter-select {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    min-width: 150px;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
}

/* Table */
.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 14px 16px;
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    font-size: 14px;
    vertical-align: middle;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover td {
    background: #f9fafb;
}

/* Badges */
.badge {
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #e0f2fe;
    color: #075985;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 8px;
}

.btn-light-primary {
    background: #eff6ff;
    color: #3b82f6;
    border: none;
}

.btn-light-primary:hover {
    background: #dbeafe;
}

.btn-light-warning {
    background: #fffbeb;
    color: #d97706;
    border: none;
}

.btn-light-warning:hover {
    background: #fef3c7;
}

.btn-light-danger {
    background: #fef2f2;
    color: #dc2626;
    border: none;
}

.btn-light-danger:hover {
    background: #fee2e2;
}

.btn-light-success {
    background: #f0fdf4;
    color: #16a34a;
    border: none;
}

.btn-light-success:hover {
    background: #dcfce7;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

/* Location Info */
.location-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.location-name {
    font-weight: 600;
    color: #111827;
}

.location-code {
    font-size: 12px;
    color: #6b7280;
    font-family: 'Monaco', 'Consolas', monospace;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
    width: fit-content;
}

.location-address {
    font-size: 13px;
    color: #6b7280;
    max-width: 300px;
    line-height: 1.4;
}

/* Stats in Table */
.stats-mini {
    display: flex;
    gap: 16px;
}

.stats-mini-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6b7280;
}

.stats-mini-item i {
    font-size: 14px;
}

.stats-mini-item .count {
    font-weight: 600;
    color: #111827;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 18px;
    color: #6b7280;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
    margin-bottom: 24px;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    flex-wrap: wrap;
    gap: 16px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

.pagination {
    display: flex;
    gap: 4px;
}

.pagination .page-link {
    padding: 8px 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.pagination .page-link:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination .page-item.active .page-link {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #d1d5db;
    cursor: not-allowed;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1055;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    position: relative;
    width: auto;
    max-width: 500px;
    margin: 1.75rem auto;
    animation: slideDown 0.3s;
}

@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-content {
    position: relative;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    padding: 0;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    position: relative;
}

.modal-header.bg-danger {
    background: #dc2626 !important;
    border-bottom-color: rgba(255, 255, 255, 0.2);
}

.modal-header .modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.modal-header.bg-danger .modal-title {
    color: white;
}

.modal-header .btn-close,
.modal-header .btn-close-white {
    background: transparent;
    border: none;
    font-size: 24px;
    line-height: 1;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
    width: 40px;
    height: 40px;
    transition: all 0.2s;
    margin: 0 !important;
    opacity: 1;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 300;
}

.modal-header .btn-close-white {
    color: #ffffff !important;
    opacity: 1 !important;
    filter: brightness(1.2);
}

.modal-header .btn-close:hover,
.modal-header .btn-close-white:hover {
    opacity: 0.8 !important;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.modal-body {
    padding: 24px;
    color: #374151;
    font-size: 14px;
    line-height: 1.6;
}

.modal-body strong {
    color: #111827;
}

.modal-body .text-muted {
    color: #6b7280;
    font-size: 13px;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
}

.modal-footer .btn-secondary {
    background: #f3f4f6;
    color: #374151;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.modal-footer .btn-secondary:hover {
    background: #e5e7eb;
}

.modal-footer .btn-danger {
    background: #dc2626;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.modal-footer .btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .filter-section {
        width: 100%;
    }

    .search-box input {
        width: 100%;
    }

    .stats-mini {
        flex-direction: column;
        gap: 8px;
    }
}

/* Mobile Card Pattern */
@media (max-width: 767.98px) {
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
        position: relative;
    }
    .table-card-mobile tbody tr:hover td { background: transparent; }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f3f4f6 !important;
        white-space: normal;
        text-align: right;
    }
    .table-card-mobile tbody td:last-child { border-bottom: none !important; }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        margin-right: 12px;
        text-align: left;
    }
    .table-card-mobile .mobile-card-head {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        padding: 14px !important;
        border-bottom: 2px solid #e0e7ff !important;
        display: block !important;
        text-align: left;
    }
    .table-card-mobile .mobile-card-head::before { display: none !important; }
    .table-card-mobile .mobile-card-head .location-info { gap: 2px; }
    .table-card-mobile .mobile-card-head .location-name { font-size: 15px; }
    .table-card-mobile .mobile-hide { display: none !important; }
    .desktop-only-cell { display: none !important; }
    .mobile-only-cell { display: flex !important; }
    .mobile-card-actions {
        display: flex !important;
        justify-content: flex-end;
        gap: 6px;
        padding: 10px 14px !important;
        background: #f9fafb;
    }
    .mobile-card-actions::before { display: none !important; }
    .table-card-mobile .stats-mini {
        flex-direction: row;
        gap: 16px;
    }
}
@media (min-width: 768px) {
    .mobile-only-cell { display: none !important; }
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    {{-- Stats Section --}}
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-md-3">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Cabang</div>
                    <div class="stat-number">{{ $stats['totalCabang'] }}</div>
                    <div class="stat-desc">Lokasi terdaftar</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Cabang Aktif</div>
                    <div class="stat-number">{{ $stats['cabangAktif'] }}</div>
                    <div class="stat-desc">Beroperasi normal</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Cabang Non-Aktif</div>
                    <div class="stat-number">{{ $stats['cabangNonAktif'] }}</div>
                    <div class="stat-desc">Tidak beroperasi</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Total Siswa</div>
                    <div class="stat-number">{{ $stats['totalSiswaSemuaCabang'] }}</div>
                    <div class="stat-desc">Di semua cabang</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h5><i class="fas fa-building" style="color: #3b82f6; margin-right: 10px;"></i>Daftar Cabang</h5>
                <small style="color: #6b7280;">Kelola lokasi/cabang PKBM House of Knowledge</small>
            </div>
            <a href="{{ route('admin.cabang.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Cabang
            </a>
        </div>
        
        <div class="card-body">
            {{-- Filter Section --}}
            <form action="{{ route('admin.cabang.index') }}" method="GET" style="margin-bottom: 24px;">
                <div class="filter-section">
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    <button type="submit" class="btn btn-outline">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.cabang.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($cabangs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Cabang</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Data Terkait</th>
                                <th>Status</th>
                                <th style="text-align: center; width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cabangs as $cabang)
                            <tr>
                                {{-- Desktop: Cabang --}}
                                <td class="desktop-only-cell">
                                    <div class="location-info">
                                        <span class="location-name">{{ $cabang->nama_cabang }}</span>
                                        <span class="location-code">{{ $cabang->kode_cabang }}</span>
                                    </div>
                                </td>
                                {{-- Mobile: Card Head --}}
                                <td class="mobile-only-cell mobile-card-head">
                                    <div class="location-info">
                                        <span class="location-name">{{ $cabang->nama_cabang }}</span>
                                        <span>
                                            <span class="location-code">{{ $cabang->kode_cabang }}</span>
                                            @if($cabang->is_active)
                                                <span class="badge badge-success" style="margin-left: 6px; font-size: 10px;">Aktif</span>
                                            @else
                                                <span class="badge badge-danger" style="margin-left: 6px; font-size: 10px;">Non-Aktif</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td data-label="Alamat">
                                    <div class="location-address">{{ Str::limit($cabang->alamat, 80) }}</div>
                                </td>
                                <td data-label="Telepon">
                                    @if($cabang->telepon)
                                        <span style="color: #374151;">{{ $cabang->telepon }}</span>
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td data-label="Data Terkait">
                                    <div class="stats-mini">
                                        <div class="stats-mini-item" title="Jumlah Siswa">
                                            <i class="fas fa-user-graduate" style="color: #3b82f6;"></i>
                                            <span class="count">{{ $cabang->siswa_count }}</span>
                                        </div>
                                        <div class="stats-mini-item" title="Jumlah Kelas">
                                            <i class="fas fa-chalkboard" style="color: #10b981;"></i>
                                            <span class="count">{{ $cabang->kelas_count }}</span>
                                        </div>
                                        <div class="stats-mini-item" title="Jumlah User">
                                            <i class="fas fa-users" style="color: #8b5cf6;"></i>
                                            <span class="count">{{ $cabang->users_count }}</span>
                                        </div>
                                    </div>
                                </td>
                                {{-- Desktop: Status --}}
                                <td class="desktop-only-cell">
                                    @if($cabang->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i> Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.cabang.show', $cabang) }}" class="btn btn-icon btn-light-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.cabang.edit', $cabang) }}" class="btn btn-icon btn-light-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-light-danger" title="Hapus" onclick="confirmDelete({{ $cabang->id }}, '{{ addslashes($cabang->nama_cabang) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($cabangs->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Menampilkan {{ $cabangs->firstItem() }} - {{ $cabangs->lastItem() }} dari {{ $cabangs->total() }} data
                        </div>
                        <div class="pagination">
                            {{ $cabangs->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h3>Belum Ada Data Cabang</h3>
                    <p>Silakan tambahkan data cabang untuk memulai.</p>
                    <a href="{{ route('admin.cabang.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Cabang Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Modal (Single Reusable) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus cabang:</p>
                <p><strong style="color: #111827; font-size: 16px;" id="deleteCabangName"></strong></p>
                <p class="text-muted" style="margin-top: 8px;">
                    <i class="fas fa-exclamation-circle" style="color: #dc2626;"></i>
                    <small>Data yang sudah dihapus tidak dapat dikembalikan.</small>
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    // Set the cabang name in the modal
    document.getElementById('deleteCabangName').textContent = name;

    // Set the form action URL
    const form = document.getElementById('deleteForm');
    form.action = "{{ route('admin.cabang.index') }}/" + id;

    // Show the modal using Bootstrap 5 API
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
