@extends('layouts.sneat')

@section('title', 'Manajemen Tahun Ajaran')

@section('page-title', 'Tahun Ajaran')
@section('page-subtitle', 'Kelola periode akademik dan status aktif')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Styling variables inherited from Sneat layout or custom */
    :root {
        --ta-primary: #4361ee;
        --ta-success: #10b981;
        --ta-warning: #f59e0b;
        --ta-danger: #ef4444;
        --ta-info: #06b6d4;
        --ta-surface: #ffffff;
        --ta-bg: #f8fafc;
        --ta-border: #e2e8f0;
        --ta-text: #1e293b;
        --ta-muted: #64748b;
        --ta-radius: 12px;
    }

    .ta-card {
        background: var(--ta-surface);
        border: 1px solid var(--ta-border);
        border-radius: var(--ta-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .ta-card-header {
        background: transparent;
        border-bottom: 1px solid var(--ta-border);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .ta-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--ta-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Stat Cards */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: var(--ta-surface);
        border: 1px solid var(--ta-border);
        border-radius: var(--ta-radius);
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
        color: var(--ta-text);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--ta-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Table Improvements */
    .table-clean {
        margin: 0;
    }
    
    .table-clean th {
        background: var(--ta-bg);
        border-bottom: 1px solid var(--ta-border);
        color: var(--ta-muted);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
    }

    .table-clean td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--ta-border);
        color: var(--ta-text);
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
        border-color: var(--ta-border);
        border-radius: 8px;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .ta-card-header { flex-direction: column; align-items: stretch; }
        .filter-wrapper { flex-wrap: wrap; }
        .filter-select { flex-grow: 1; }
        
        /* Mobile Card Table */
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: block;
            border-bottom: none;
            padding: 1rem;
            border-bottom: 1px solid var(--ta-border);
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
            color: var(--ta-muted);
            text-transform: uppercase;
        }
        .td-actions {
            margin-top: 1rem;
            padding-top: 1rem !important;
            border-top: 1px dashed var(--ta-border) !important;
            justify-content: center !important;
        }
    }
</style>
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <!-- Total Periode -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $tahunAjarans->total() }}</div>
                <div class="stat-label">Total Periode</div>
            </div>
        </div>

        <!-- Status Aktif -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $tahunAjarans->where('is_active', 1)->count() }}</div>
                <div class="stat-label">Periode Aktif</div>
            </div>
        </div>

        <!-- Tahun Saat Ini -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value" style="font-size: 1.15rem; margin-top: 0.2rem;">
                    {{ $tahunAjarans->where('is_active', 1)->first()->nama_tahun_ajaran ?? 'Tidak ada' }}
                </div>
                <div class="stat-label">Tahun Berjalan</div>
            </div>
        </div>
    </div>

    <!-- Filter Badge -->
    @if(request('status') !== null)
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4 border-0 shadow-sm" style="background: #eff6ff; color: #1e3a8a; border-radius: 10px;">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter"></i>
            <span>Menampilkan filter status: <strong>{{ request('status') == '1' ? 'Aktif' : 'Tidak Aktif' }}</strong></span>
            <span class="badge bg-primary ms-2 rounded-pill">{{ $tahunAjarans->total() }} data</span>
        </div>
        <a href="{{ route('waka.tahun-ajaran.index') }}" class="btn btn-sm btn-light text-primary fw-bold" style="border-radius: 6px;">
            <i class="fas fa-times me-1"></i> Reset
        </a>
    </div>
    @endif

    <!-- Data Table Card -->
    <div class="ta-card">
        <div class="ta-card-header">
            <h5 class="ta-card-title">
                <i class="fas fa-list text-primary"></i> Daftar Tahun Ajaran
            </h5>
            <div class="filter-wrapper">
                <form action="{{ route('waka.tahun-ajaran.index') }}" method="GET" class="d-flex gap-2 mb-0">
                    <select class="form-select filter-select" name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </form>
                <a href="{{ route('waka.tahun-ajaran.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tahun Ajaran</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-clean table-hover">
                <thead>
                    <tr>
                        <th width="80" class="text-center">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Periode</th>
                        <th class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjarans as $index => $ta)
                    <tr>
                        <td class="text-center text-muted" data-label="No">{{ $tahunAjarans->firstItem() + $index }}</td>
                        <td class="fw-bold" data-label="Tahun Ajaran">{{ $ta->nama_tahun_ajaran }}</td>
                        <td data-label="Periode">
                            <i class="far fa-calendar-alt text-muted me-1"></i>
                            {{ \Carbon\Carbon::parse($ta->tanggal_mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($ta->tanggal_selesai)->format('d M Y') }}
                        </td>
                        <td class="text-center" data-label="Status">
                            @if($ta->is_active)
                                <span class="badge bg-label-success badge-status"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                            @else
                                <span class="badge bg-label-secondary badge-status"><i class="fas fa-power-off me-1"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td class="td-actions" data-label="Aksi">
                            <div class="d-flex justify-content-center gap-1 action-btns">
                                <a href="{{ route('waka.tahun-ajaran.show', $ta->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('waka.tahun-ajaran.edit', $ta->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if(!$ta->is_active)
                                <button type="button" class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#activateModal{{ $ta->id }}" title="Aktifkan">
                                    <i class="fas fa-power-off"></i>
                                </button>
                                @endif
                                
                                <button type="button" class="btn btn-sm btn-danger text-white" onclick="confirmDelete({{ $ta->id }}, '{{ addslashes($ta->nama_tahun_ajaran) }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-calendar-times fs-1 mb-3" style="color: #e2e8f0;"></i>
                                <h6 class="mb-1">Tidak Ada Data</h6>
                                <p class="small mb-0">Belum ada tahun ajaran yang ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tahunAjarans->hasPages())
        <div class="border-top p-3 d-flex justify-content-center">
            {{ $tahunAjarans->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Modals Section -->
    @foreach($tahunAjarans as $ta)
        @if(!$ta->is_active)
        <div class="modal fade" id="activateModal{{ $ta->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-0 pb-4">
                        <div class="mb-3">
                            <div class="rounded-circle bg-label-success d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-power-off fs-3 text-success"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1">Aktifkan Periode?</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem;">Tahun Ajaran <span class="fw-bold text-dark">{{ $ta->nama_tahun_ajaran }}</span></p>
                        <div class="alert alert-warning py-2 px-3 small text-start mx-2 mb-4" style="border-radius: 8px;">
                            <i class="fas fa-info-circle me-1"></i> Periode aktif lainnya otomatis dinonaktifkan.
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('waka.tahun-ajaran.activate', $ta->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success px-4">Aktifkan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

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
                    <h5 class="fw-bold mb-1">Hapus Data?</h5>
                    <p class="text-muted mb-4" style="font-size: 0.9rem;">Data <span id="deleteItemName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>
                    
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
        form.action = "{{ route('waka.tahun-ajaran.index') }}/" + id;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endsection