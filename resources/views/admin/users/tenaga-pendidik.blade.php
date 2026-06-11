@extends('layouts.sneat')

@section('title', 'Data Tenaga Pendidik')
@section('page-title', 'Data Tenaga Pendidik')
@section('page-subtitle', 'Kelola akun Ketua PKBM, Sekretaris, Bendahara, Wali Kelas, dan Guru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection
@section('styles')
    @vite(['resources/css/admin/users/tenaga-pendidik.css'])
@endsection

@section('content')
<div class="user-list-shell">
        {{-- Success Message --}}

        {{-- Import Warnings --}}
        @if(session('import_warnings'))
            <div class="alert alert-warning import-warning">
                <div class="import-warning-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    Beberapa data dilewati saat import:
                </div>
                <ul class="import-warning-list">
                    @foreach(session('import_warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search/Filter Info --}}
        @if(request('search') || request('role'))
            <div class="search-info">
                <div>
                    @if(request('search'))
                        <i class="fas fa-search"></i>
                        Menampilkan hasil pencarian untuk: <span class="search-term">"{{ request('search') }}"</span>
                    @endif
                    @if(request('role'))
                        @if(request('search')) • @endif
                        <i class="fas fa-filter"></i>
                        Role: <span class="search-term">{{ $roles[request('role')] ?? request('role') }}</span>
                    @endif
                    <small class="search-result-count">({{ $tenagaPendidik->total() }} data ditemukan)</small>
                </div>
                <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn-clear-all">
                    <i class="fas fa-times"></i>
                    Hapus Filter
                </a>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                {{-- Left Group: Title & Filter --}}
                <div class="d-flex flex-wrap align-items-center gap-3 w-100-mobile">
                    {{-- Title Group --}}
                    <div class="d-flex gap-2 align-items-center justify-content-between w-100-mobile">
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Daftar Tenaga Pendidik</h5>
                                <small class="text-muted">Total: {{ $tenagaPendidik->total() }} tenaga pendidik</small>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <form action="{{ route('admin.users.tenaga-pendidik') }}" method="GET" class="search-form d-flex gap-2 align-items-center w-100-mobile">
                        {{-- Filter Dropdown --}}
                        <div class="dropdown filter-dropdown w-100-mobile">
                            <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false" 
                                data-bs-auto-close="outside" data-bs-display="static">
                                <span><i class="fas fa-filter me-1"></i> Filter</span>
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0 filter-dropdown-menu" aria-labelledby="filterDropdown">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Role / Jabatan</label>
                                    <select name="role" id="roleFilter" class="form-select form-select-sm" data-auto-submit>
                                        <option value="">Semua Role</option>
                                        @foreach($roles as $roleKey => $roleLabel)
                                            <option value="{{ $roleKey }}" {{ request('role') == $roleKey ? 'selected' : '' }}>
                                                {{ $roleLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                </div>
                            </div>
                        </div>

                        {{-- Search Input --}}
                        <div class="search-input-wrapper w-100-mobile">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="searchInput" class="search-input"
                                placeholder="Cari..." value="{{ request('search') }}" autocomplete="off">
                            <button type="button" class="clear-search {{ request('search') ? 'show' : '' }}"
                                id="clearSearch" title="Hapus pencarian">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right Group: Actions --}}
                <div class="d-flex gap-2 action-group-mobile">
                    <form action="{{ route('admin.users.bulk-delete-tenaga-pendidik') }}" method="POST" id="bulkDeleteForm" class="bulk-delete-form">
                        @csrf
                        <input type="hidden" name="ids" id="bulkDeleteIds">
                        <button type="button" class="btn btn-danger" data-show-bulk-delete-modal>
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    <!-- Dropdown Menu Aksi -->
                    <div class="btn-group">
                        <button type="button" class="btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> <span class="d-none d-md-inline">Menu Aksi</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('admin.users.tenaga-pendidik.print') }}?{{ http_build_query(request()->all()) }}" class="dropdown-item" target="_blank">
                                    <i class="fas fa-print me-2"></i> Cetak Data (PDF)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.import-tenaga-pendidik') }}" class="dropdown-item">
                                    <i class="fas fa-file-import me-2"></i> Import Excel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.tenaga-pendidik-template') }}" class="dropdown-item">
                                    <i class="fas fa-download me-2"></i> Download Template
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.users.create-tenaga-pendidik') }}" class="btn-primary btn-nowrap">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-md-inline">Tambah Tenaga Pendidik</span>
                        <span class="d-md-none">Tambah</span>
                    </a>
                </div>
            </div>

            <div class="d-md-none mobile-select-all-bar">
                <input type="checkbox" id="selectAllMobile" class="form-check-input">
                <label for="selectAllMobile" class="select-all-label">Pilih Semua</label>
            </div>

            <div class="table-scroll">
                <table class="table table-card-mobile">
                    <thead>
                        <tr>
                            <th class="text-center th-checkbox">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th class="th-number">No</th>
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Role / Jabatan</th>
                            <th>Email</th>
                            <th class="th-status">Status</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenagaPendidik as $index => $tp)
                            <tr>
                                <td class="text-center mobile-card-checkbox">
                                    <input type="checkbox" name="ids[]" class="form-check-input select-item" value="{{ $tp->id }}">
                                </td>
                                <td class="mobile-hide row-number">
                                    {{ $tenagaPendidik->firstItem() + $index }}</td>
                                <td class="mobile-card-head">
                                    <div class="teacher-name-cell">{{ $tp->name }}</div>
                                    <small class="cell-muted">
                                        <i class="fas fa-phone icon-xs"></i>
                                        {{ $tp->tenagaPendidik->telepon ?? $tp->phone ?? '-' }}
                                    </small>
                                </td>
                                <td data-label="NIP">
                                    <span class="teacher-nip">{{ $tp->tenagaPendidik->nip ?? '-' }}</span>
                                </td>
                                <td data-label="Role">
                                    <span class="badge badge-role">{{ ucwords(str_replace('_', ' ', $tp->role)) }}</span>
                                </td>
                                <td data-label="Email" class="teacher-email">{{ $tp->email }}</td>
                                <td data-label="Status">
                                    @if($tp->is_active)
                                        <span class="badge badge-active">
                                            <i class="fas fa-check-circle icon-xs"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-inactive">
                                            <i class="fas fa-times-circle icon-xs"></i>
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-buttons">
                                        {{-- View Button --}}
                                        <a href="{{ route('admin.users.show-tenaga-pendidik', $tp->id) }}"
                                            class="action-btn view" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.users.edit-tenaga-pendidik', $tp->id) }}"
                                            class="action-btn edit" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete Button --}}
                                        <button type="button" class="action-btn delete" title="Hapus Data"
                                            data-bs-toggle="modal" data-bs-target="#deleteTenagaPendidikModal{{ $tp->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-table-cell">
                                    <i class="fas fa-chalkboard-teacher fa-3x empty-table-icon"></i>
                                    <div class="empty-table-title">
                                        @if(request('search'))
                                            Tidak ada tenaga pendidik yang sesuai dengan pencarian "{{ request('search') }}"
                                        @else
                                            Tidak ada data ditemukan
                                        @endif
                                    </div>
                                    <small>
                                        @if(request('search'))
                                            Coba kata kunci lain atau <a href="{{ route('admin.users.tenaga-pendidik') }}"
                                                class="reset-filter-link">hapus filter</a>
                                        @else
                                            Silakan tambah data tenaga pendidik baru
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tenagaPendidik->hasPages())
                <div class="pagination-wrap">
                    {{ $tenagaPendidik->appends(['search' => request('search'), 'role' => request('role')])->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modals for Tenaga Pendidik --}}
    {{-- Delete Modals for Tenaga Pendidik --}}
    @foreach($tenagaPendidik as $tp)
        <div class="modal fade" id="deleteTenagaPendidikModal{{ $tp->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus data tenaga pendidik:</p>
                        <div class="teacher-summary-box">`r`n                            <div class="teacher-summary-title">`r`n                                <i class="fas fa-chalkboard-teacher text-primary"></i>`r`n                                {{ $tp->tenagaPendidik->nama_lengkap ?? $tp->name }}`r`n                            </div>`r`n                            <small class="teacher-summary-meta">{{ $tp->role ? ucwords(str_replace('_', ' ', $tp->role)) : '-' }}`r`n                                • {{ $tp->email }}</small>`r`n                        </div>
                        <p class="modal-danger-note">
                            <i class="fas fa-info-circle"></i>
                            <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait
                                termasuk akun login.</small>
                        </p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <form action="{{ route('admin.users.delete-tenaga-pendidik', $tp->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
<!-- Modal Konfirmasi Bulk Delete -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <span id="selectedCount" class="selected-count"></span> data terpilih? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" data-submit-bulk-delete>Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/list.js'])
@endsection
