@extends('layouts.sneat')

@section('title', 'Manajemen Tahun Ajaran')

@section('page-title', 'Manajemen Tahun Ajaran')
@section('page-subtitle', 'Kelola data tahun ajaran dan periode akademik aktif')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
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
    .table-card-mobile .mobile-hide { display: none !important; }
    .desktop-only-cell { display: none !important; }
    .mobile-card-actions {
        display: flex !important;
        justify-content: flex-end;
        gap: 6px;
        padding: 10px 14px !important;
        background: #f9fafb;
    }
    .mobile-card-actions::before { display: none !important; }
    .mobile-card-actions .btn-group { box-shadow: none !important; }
}
@media (min-width: 768px) {
    .mobile-only-cell { display: none !important; }
}
</style>
<div class="container-fluid px-0">

    {{-- 1. RINGKASAN STATISTIK (DITAMBAHKAN UNTUK MEMPERCANTIK) --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Periode</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tahunAjarans->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Status Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tahunAjarans->where('is_active', 1)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tahun Ajaran Saat Ini</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $tahunAjarans->where('is_active', 1)->first()->nama_tahun_ajaran ?? 'Tidak ada yang aktif' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. FILTER CARD --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i> Filter Status</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tahun-ajaran.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-9 mb-2">
                        <label class="small font-weight-bold text-dark">STATUS TAHUN AJARAN</label>
                        <select class="form-control shadow-sm border-left-primary" name="status">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            <i class="fas fa-search mr-1"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. FILTER INFO BADGE --}}
    @if(request('status') !== null)
        <div class="alert alert-info shadow-sm d-flex justify-content-between align-items-center border-left-info">
            <div>
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Filter Aktif:</strong> Status {{ request('status') == '1' ? 'Aktif' : 'Tidak Aktif' }}
                <span class="badge badge-light ml-2">{{ $tahunAjarans->total() }} data</span>
            </div>
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-sm btn-light text-primary font-weight-bold">
                <i class="fas fa-times mr-1"></i> Hapus Filter
            </a>
        </div>
    @endif

    {{-- 4. DATA TABLE CARD --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tahun Ajaran</h6>
            <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Tahun Ajaran
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-card-mobile">
                    <thead class="bg-light text-primary small font-weight-bold">
                        <tr>
                            <th class="text-center" width="50">NO</th>
                            <th>TAHUN AJARAN</th>
                            <th class="text-center">TANGGAL MULAI</th>
                            <th class="text-center">TANGGAL SELESAI</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center" width="180">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahunAjarans as $index => $tahunAjaran)
                        <tr>
                            <td class="text-center align-middle font-weight-bold mobile-hide">{{ $tahunAjarans->firstItem() + $index }}</td>
                            {{-- Desktop: Tahun Ajaran --}}
                            <td class="align-middle text-dark font-weight-bold desktop-only-cell">{{ $tahunAjaran->nama_tahun_ajaran }}</td>
                            {{-- Mobile: Card Head --}}
                            <td class="mobile-only-cell mobile-card-head">
                                <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                                @if($tahunAjaran->is_active)
                                    <span class="badge badge-success ms-1" style="font-size: 10px;">AKTIF</span>
                                @else
                                    <span class="badge badge-secondary ms-1" style="font-size: 10px;">TIDAK AKTIF</span>
                                @endif
                            </td>
                            <td class="text-center align-middle small" data-label="Tanggal Mulai">{{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->format('d M Y') }}</td>
                            <td class="text-center align-middle small" data-label="Tanggal Selesai">{{ \Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)->format('d M Y') }}</td>
                            {{-- Desktop: Status --}}
                            <td class="text-center align-middle desktop-only-cell">
                                @if($tahunAjaran->is_active)
                                    <span class="badge badge-success px-3 py-2 shadow-sm">
                                        <i class="fas fa-check-circle mr-1"></i> AKTIF
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-3 py-2">
                                        <i class="fas fa-times-circle mr-1"></i> TIDAK AKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="text-center align-middle mobile-card-actions">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('admin.tahun-ajaran.show', $tahunAjaran->id) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.tahun-ajaran.edit', $tahunAjaran->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>

                                    @if(!$tahunAjaran->is_active)
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#activateModal{{ $tahunAjaran->id }}" title="Aktifkan">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    @endif

                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $tahunAjaran->id }}, '{{ addslashes($tahunAjaran->nama_tahun_ajaran) }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-calendar-alt fa-3x text-gray-200 mb-3"></i>
                                <h6 class="text-gray-500">Data tahun ajaran belum tersedia</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tahunAjarans->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                {{ $tahunAjarans->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- 5. MODALS SECTION --}}
@foreach($tahunAjarans as $tahunAjaran)
    {{-- Activate Modal --}}
    @if(!$tahunAjaran->is_active)
    <div class="modal fade" id="activateModal{{ $tahunAjaran->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i>Aktifkan Tahun Ajaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="mb-1">Anda akan mengaktifkan tahun ajaran:</p>
                    <h5 class="fw-bold text-dark">{{ $tahunAjaran->nama_tahun_ajaran }}</h5>
                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Penting:</strong> Tahun ajaran aktif lainnya akan dinonaktifkan secara otomatis.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.tahun-ajaran.activate', $tahunAjaran->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm px-4 shadow">Ya, Aktifkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

{{-- Delete Modal (Single Reusable) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-1">Apakah Anda yakin ingin menghapus tahun ajaran:</p>
                <h5 class="fw-bold text-danger" id="deleteTahunAjaranName"></h5>
                <p class="text-muted small mt-2">Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4 shadow">Ya, Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    // Set the tahun ajaran name in the modal
    document.getElementById('deleteTahunAjaranName').textContent = name;

    // Set the form action URL
    const form = document.getElementById('deleteForm');
    form.action = "{{ route('admin.tahun-ajaran.index') }}/" + id;

    // Show the modal using Bootstrap 5 API
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection