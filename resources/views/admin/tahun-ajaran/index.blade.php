@extends('layouts.sneat')

@section('title', 'Manajemen Tahun Ajaran')

@section('page-title', 'Tahun Ajaran')
@section('page-subtitle', 'Kelola periode akademik dan status aktif')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/tahun-ajaran/index.css'])
@endsection

@section('content')
<div class="tahun-ajaran-index-page">
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-total">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $tahunAjarans->total() }}</div>
                <div class="stat-label">Total Periode</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-active">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $tahunAjarans->where('is_active', 1)->count() }}</div>
                <div class="stat-label">Periode Aktif</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-current">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value stat-value-current">
                    {{ $tahunAjarans->where('is_active', 1)->first()->nama_tahun_ajaran ?? 'Tidak ada' }}
                </div>
                <div class="stat-label">Tahun Berjalan</div>
            </div>
        </div>
    </div>

    @if(request('status') !== null)
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4 border-0 shadow-sm filter-alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter"></i>
            <span>Menampilkan filter status: <strong>{{ request('status') == '1' ? 'Aktif' : 'Tidak Aktif' }}</strong></span>
            <span class="badge bg-primary ms-2 rounded-pill">{{ $tahunAjarans->total() }} data</span>
        </div>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-sm btn-light text-primary fw-bold btn-reset-filter">
            <i class="fas fa-times me-1"></i> Reset
        </a>
    </div>
    @endif

    <div class="ta-card">
        <div class="ta-card-header">
            <h5 class="ta-card-title">
                <i class="fas fa-list text-primary"></i> Daftar Tahun Ajaran
            </h5>
            <div class="filter-wrapper">
                <form action="{{ route('admin.tahun-ajaran.index') }}" method="GET" class="d-flex gap-2 mb-0">
                    <select class="form-select filter-select" name="status" data-auto-submit>
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </form>
                <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-primary d-flex align-items-center gap-2 btn-create-ta">
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
                        <td class="td-actions text-end" data-label="Aksi">
                            <div class="d-flex justify-content-end gap-1 action-btns">
                                <a href="{{ route('admin.tahun-ajaran.show', $ta->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.tahun-ajaran.edit', $ta->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if(!$ta->is_active)
                                <button type="button" class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#activateModal{{ $ta->id }}" title="Aktifkan">
                                    <i class="fas fa-power-off"></i>
                                </button>
                                @endif

                                <button type="button"
                                        class="btn btn-sm btn-danger text-white"
                                        data-delete-ta
                                        data-delete-url="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}"
                                        data-delete-name="{{ $ta->nama_tahun_ajaran }}"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-calendar-times fs-1 mb-3 empty-table-icon"></i>
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
</div>

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
                        <div class="rounded-circle bg-label-success d-inline-flex align-items-center justify-content-center modal-icon-circle">
                            <i class="fas fa-power-off fs-3 text-success"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">Aktifkan Periode?</h5>
                    <p class="text-muted mb-3 modal-text-small">Tahun Ajaran <span class="fw-bold text-dark">{{ $ta->nama_tahun_ajaran }}</span></p>
                    <div class="alert alert-warning py-2 px-3 small text-start mx-2 mb-4 modal-warning-note">
                        <i class="fas fa-info-circle me-1"></i> Periode aktif lainnya otomatis dinonaktifkan.
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('admin.tahun-ajaran.activate', $ta->id) }}" method="POST">
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

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center modal-icon-circle">
                        <i class="fas fa-trash-alt fs-3 text-danger"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Data?</h5>
                <p class="text-muted mb-4 modal-text-small">Data <span id="deleteItemName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>

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
    @vite(['resources/js/admin/tahun-ajaran/index.js'])
@endsection
