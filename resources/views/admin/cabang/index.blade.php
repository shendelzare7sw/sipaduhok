@extends('layouts.sneat')

@section('title', 'Manajemen Cabang')

@section('page-title', 'Data Cabang')
@section('page-subtitle', 'Kelola data lokasi dan cabang PKBM House of Knowledge')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/admin/cabang/index.css')
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <!-- Total Cabang -->
        <div class="stat-widget">
            <div class="stat-icon stat-icon-primary">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalCabang'] }}</div>
                <div class="stat-label">Total Cabang</div>
            </div>
        </div>

        <!-- Cabang Aktif -->
        <div class="stat-widget">
            <div class="stat-icon stat-icon-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['cabangAktif'] }}</div>
                <div class="stat-label">Aktif Beroperasi</div>
            </div>
        </div>

        <!-- Cabang Non-Aktif -->
        <div class="stat-widget">
            <div class="stat-icon stat-icon-danger">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['cabangNonAktif'] }}</div>
                <div class="stat-label">Non-Aktif</div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="stat-widget">
            <div class="stat-icon stat-icon-purple">
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
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4 border-0 shadow-sm filter-alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter"></i>
            <span>Menampilkan filter pencarian</span>
            <span class="badge bg-primary ms-2 rounded-pill">{{ $cabangs->total() }} data</span>
        </div>
        <a href="{{ route('admin.cabang.index') }}" class="btn btn-sm btn-light text-primary fw-bold rounded-sm">
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
                    <select class="form-select filter-select" name="status" data-auto-submit>
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </form>
                <a href="{{ route('admin.cabang.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-md">
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
                                <span class="text-muted small-muted">
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
                        <td class="td-actions text-end" data-label="Aksi">
                            <div class="d-flex justify-content-end gap-1 action-btns">
                                <a href="{{ route('admin.cabang.show', $cabang) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.cabang.edit', $cabang) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button type="button" class="btn btn-sm btn-danger text-white" data-delete-id="{{ $cabang->id }}" data-delete-name="{{ $cabang->nama_cabang }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-building fs-1 mb-3 empty-icon"></i>
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
                        <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center delete-icon-wrap">
                            <i class="fas fa-trash-alt fs-3 text-danger"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">Hapus Cabang?</h5>
                    <p class="text-muted mb-4 delete-modal-text">Cabang <span id="deleteItemName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteForm" method="POST" data-delete-base-url="{{ route('admin.cabang.index') }}">
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
    @vite('resources/js/admin/cabang/index.js')
@endsection
