@extends('layouts.sneat')

@section('title', 'Manajemen Cabang')

@section('page-title', 'Data Cabang')
@section('page-subtitle', 'Kelola data lokasi dan cabang PKBM House of Knowledge')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Styling variables inherited from Sneat layout or custom */
    :root {
        --cb-primary: #4361ee;
        --cb-success: #10b981;
        --cb-warning: #f59e0b;
        --cb-danger: #ef4444;
        --cb-info: #06b6d4;
        --cb-purple: #8b5cf6;
        --cb-surface: #ffffff;
        --cb-bg: #f8fafc;
        --cb-border: #e2e8f0;
        --cb-text: #1e293b;
        --cb-muted: #64748b;
        --cb-radius: 12px;
    }

    .cb-card {
        background: var(--cb-surface);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .cb-card-header {
        background: transparent;
        border-bottom: 1px solid var(--cb-border);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .cb-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--cb-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Stat Cards */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: var(--cb-surface);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease;
    }

    .stat-widget:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-details {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--cb-text);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--cb-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Table Improvements */
    .table-clean {
        margin: 0;
    }
    
    .table-clean th {
        background: var(--cb-bg);
        border-bottom: 1px solid var(--cb-border);
        color: var(--cb-muted);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
    }

    .table-clean td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--cb-border);
        color: var(--cb-text);
        font-size: 0.9rem;
    }

    .table-clean tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-clean tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-status {
        padding: 0.4em 0.8em;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
    }

    .action-btns .btn {
        padding: 0.35rem 0.6rem;
        font-size: 0.8rem;
        border-radius: 6px;
    }

    /* Filters */
    .filter-wrapper {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .filter-select {
        min-width: 150px;
        font-size: 0.85rem;
        padding: 0.4rem 2rem 0.4rem 0.75rem;
        border-color: var(--cb-border);
        border-radius: 8px;
    }

    /* Location styles */
    .location-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .location-name {
        font-weight: 600;
        color: var(--cb-text);
    }
    .location-code {
        font-size: 0.75rem;
        color: var(--cb-muted);
        background: var(--cb-bg);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
        font-family: inherit;
    }
    .stats-mini {
        display: flex;
        gap: 12px;
    }
    .stats-mini-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.85rem;
        color: var(--cb-muted);
    }
    .stats-mini-item .count {
        font-weight: 600;
        color: var(--cb-text);
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .cb-card-header { flex-direction: column; align-items: stretch; }
        .filter-wrapper { flex-wrap: wrap; }
        .filter-select { flex-grow: 1; }
        
        /* Mobile Card Table */
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: block;
            border-bottom: none;
            padding: 1rem;
            border-bottom: 1px solid var(--cb-border);
        }
        .table-clean tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border: none;
        }
        .table-clean tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--cb-muted);
            text-transform: uppercase;
        }
        .td-actions {
            margin-top: 1rem;
            padding-top: 1rem !important;
            border-top: 1px dashed var(--cb-border) !important;
            justify-content: center !important;
        }
        .stats-mini { justify-content: flex-end; }
        .location-info { align-items: flex-end; }
    }
</style>
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <!-- Total Cabang -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalCabang'] }}</div>
                <div class="stat-label">Total Cabang</div>
            </div>
        </div>

        <!-- Cabang Aktif -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['cabangAktif'] }}</div>
                <div class="stat-label">Aktif Beroperasi</div>
            </div>
        </div>

        <!-- Cabang Non-Aktif -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['cabangNonAktif'] }}</div>
                <div class="stat-label">Non-Aktif</div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalSiswaSemuaCabang'] }}</div>
                <div class="stat-label">Total Siswa</div>
            </div>
        </div>
    </div>

    <!-- Filter Badge -->
    @if(request('status') || request('search'))
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4 border-0 shadow-sm" style="background: #eff6ff; color: #1e3a8a; border-radius: 10px;">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter"></i>
            <span>Menampilkan filter pencarian</span>
            <span class="badge bg-primary ms-2 rounded-pill">{{ $cabangs->total() }} data</span>
        </div>
        <a href="{{ route('admin.cabang.index') }}" class="btn btn-sm btn-light text-primary fw-bold" style="border-radius: 6px;">
            <i class="fas fa-times me-1"></i> Reset
        </a>
    </div>
    @endif

    <!-- Data Table Card -->
    <div class="cb-card">
        <div class="cb-card-header">
            <h5 class="cb-card-title">
                <i class="fas fa-list text-primary"></i> Daftar Cabang
            </h5>
            <div class="filter-wrapper">
                <form action="{{ route('admin.cabang.index') }}" method="GET" class="d-flex gap-2 mb-0">
                    <select class="form-select filter-select" name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </form>
                <a href="{{ route('admin.cabang.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Cabang Baru</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-clean table-hover">
                <thead>
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Info Cabang</th>
                        <th>Kontak & Alamat</th>
                        <th>Statistik</th>
                        <th class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cabangs as $index => $cabang)
                    <tr>
                        <td class="text-center text-muted" data-label="No">{{ $cabangs->firstItem() + $index }}</td>
                        <td data-label="Cabang">
                            <div class="location-info">
                                <span class="location-name">{{ $cabang->nama_cabang }}</span>
                                <span class="location-code">Kode: {{ $cabang->kode_cabang }}</span>
                            </div>
                        </td>
                        <td data-label="Kontak">
                            <div class="d-flex flex-column gap-1">
                                @if($cabang->telepon)
                                    <span class="text-dark small"><i class="fas fa-phone-alt text-muted me-1"></i> {{ $cabang->telepon }}</span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-phone-alt text-muted me-1"></i> -</span>
                                @endif
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ Str::limit($cabang->alamat, 40) }}
                                </span>
                            </div>
                        </td>
                        <td data-label="Data">
                            <div class="stats-mini">
                                <div class="stats-mini-item" title="Jumlah Siswa" data-bs-toggle="tooltip">
                                    <i class="fas fa-user-graduate text-primary"></i>
                                    <span class="count">{{ $cabang->siswa_count }}</span>
                                </div>
                                <div class="stats-mini-item" title="Jumlah Kelas" data-bs-toggle="tooltip">
                                    <i class="fas fa-chalkboard text-success"></i>
                                    <span class="count">{{ $cabang->kelas_count }}</span>
                                </div>
                                <div class="stats-mini-item" title="Jumlah User" data-bs-toggle="tooltip">
                                    <i class="fas fa-users text-purple"></i>
                                    <span class="count">{{ $cabang->users_count }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center" data-label="Status">
                            @if($cabang->is_active)
                                <span class="badge bg-label-success badge-status"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                            @else
                                <span class="badge bg-label-secondary badge-status"><i class="fas fa-power-off me-1"></i> Non-Aktif</span>
                            @endif
                        </td>
                        <td class="td-actions" data-label="Aksi">
                            <div class="d-flex justify-content-center gap-1 action-btns">
                                <a href="{{ route('admin.cabang.show', $cabang) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.cabang.edit', $cabang) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button type="button" class="btn btn-sm btn-danger text-white" onclick="confirmDelete({{ $cabang->id }}, '{{ addslashes($cabang->nama_cabang) }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-building fs-1 mb-3" style="color: #e2e8f0;"></i>
                                <h6 class="mb-1">Tidak Ada Data Cabang</h6>
                                <p class="small mb-0">Belum ada cabang yang ditambahkan atau sesuai filter.</p>
                                <a href="{{ route('admin.cabang.create') }}" class="btn btn-primary btn-sm mt-3">
                                    <i class="fas fa-plus me-1"></i> Tambah Cabang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($cabangs->hasPages())
        <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-muted small">Menampilkan {{ $cabangs->firstItem() ?? 0 }} - {{ $cabangs->lastItem() ?? 0 }} dari total {{ $cabangs->total() }}</span>
            <div class="mt-2 mt-sm-0">
                {{ $cabangs->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0 pb-4">
                    <div class="mb-3">
                        <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-trash-alt fs-3 text-danger"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">Hapus Cabang?</h5>
                    <p class="text-muted mb-4" style="font-size: 0.9rem;">Cabang <span id="deleteItemName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger px-4">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteItemName').textContent = name;
        const form = document.getElementById('deleteForm');
        form.action = "{{ route('admin.cabang.index') }}/" + id;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
