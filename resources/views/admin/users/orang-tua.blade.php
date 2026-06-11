@extends('layouts.sneat')

@section('title', 'Data Orang Tua')
@section('page-title', 'Data Orang Tua')
@section('page-subtitle', 'Kelola akun login orang tua siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/orang-tua.css'])
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
                                <h5 class="mb-0 fw-bold text-dark">Daftar Orang Tua</h5>
                                <small class="text-muted">Total: {{ $orangTua->total() }} akun orang tua</small>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <form action="{{ route('admin.users.orang-tua') }}" method="GET" id="filterForm" class="search-form d-flex gap-2 align-items-center w-100-mobile">
                        {{-- Filter Dropdown --}}
                        <div class="dropdown filter-dropdown w-100-mobile">
                            <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false" 
                                data-bs-auto-close="outside" data-bs-display="static">
                                <span><i class="fas fa-filter me-1"></i> Filter</span>
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0 filter-dropdown-menu" aria-labelledby="filterDropdown">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>

                                {{-- Filter Jenjang Anak --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Jenjang Anak</label>
                                    <select name="jenjang" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang Anak</option>
                                        @foreach($jenjangs as $j)
                                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Cabang --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Cabang</label>
                                    <select name="cabang_id" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangList as $cabang)
                                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Status Akun --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Status Akun</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                                placeholder="Cari..." value="{{ request('search') }}"
                                autocomplete="off">
                            <button type="button" class="clear-search {{ request('search') ? 'show' : '' }}"
                                id="clearSearch" title="Hapus pencarian">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right Group: Actions --}}
                <div class="d-flex gap-2 action-group-mobile">
                    <form action="{{ route('admin.users.bulk-delete-orang-tua') }}" method="POST" id="bulkDeleteForm" class="bulk-delete-form">
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
                                <a href="{{ route('admin.users.orang-tua.print') }}?{{ http_build_query(request()->all()) }}" class="dropdown-item" target="_blank">
                                    <i class="fas fa-print me-2"></i> Cetak Data (PDF)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.import-orang-tua') }}" class="dropdown-item">
                                    <i class="fas fa-file-import me-2"></i> Import Excel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.orang-tua-template') }}" class="dropdown-item">
                                    <i class="fas fa-download me-2"></i> Download Template
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.users.orang-tua.create') }}" class="btn-primary btn-nowrap">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-md-inline">Tambah Orang Tua</span>
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
                            <th>Nama Orang Tua</th>
                            <th>Username / Email</th>
                            <th>Anak (Siswa)</th>
                            <th class="th-status">Status Akun</th>
                            <th class="th-actions th-actions-wide">Aksi</th>
                        </tr>
                    </thead>
                        <tbody>
                            @forelse($orangTua as $index => $ortu)
                                <tr>
                                <td class="text-center mobile-card-checkbox">
                                    <input type="checkbox" name="ids[]" class="form-check-input select-item" value="{{ $ortu->id }}">
                                </td>
                                <td class="mobile-hide row-number">
                                    {{ $orangTua->firstItem() + $index }}</td>
                                    <td class="mobile-card-head">
                                        <div class="parent-name-cell">{{ $ortu->name }}</div>
                                        <small class="cell-muted">
                                            <i class="fas fa-user-friends icon-xs"></i>
                                            Orang Tua
                                        </small>
                                    </td>
                                    <td data-label="Username / Email">
                                        <div class="parent-username">
                                            {{ $ortu->username }}</div>
                                        <small class="cell-muted">{{ $ortu->email }}</small>
                                    </td>
                                    <td data-label="Anak">
                                        @if($ortu->studentParents->count() > 0)
                                            <div class="children-list">
                                                @foreach($ortu->studentParents as $sp)
                                                    <div class="child-row">
                                                        @php
                                                            $jenjangBadge = isset($sp->siswa->kelas->jenjang) ? [
                                                                'KB' => 'badge-kb',
                                                                'TKA' => 'badge-tka',
                                                                'TKB' => 'badge-tkb',
                                                                'SD' => 'badge-sd',
                                                                'SMP' => 'badge-smp',
                                                                'SMA' => 'badge-sma',
                                                            ][$sp->siswa->kelas->jenjang] ?? 'badge-class' : 'badge-class';
                                                        @endphp
                                                        @if($sp->siswa->kelas)
                                                            <span class="badge {{ $jenjangBadge }}"
                                                                class="child-class-badge">
                                                                {{ $sp->siswa->kelas->jenjang }}
                                                            </span>
                                                        @endif
                                                        <span
                                                            class="child-name">{{ $sp->siswa->nama_lengkap }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="orphan-warning">
                                                <i class="fas fa-exclamation-circle"></i>
                                                Belum ada anak terdaftar
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge-status {{ $ortu->is_active ? 'aktif' : 'nonaktif' }}">
                                            <i class="fas fa-{{ $ortu->is_active ? 'check-circle' : 'times-circle' }}"
                                                class="icon-xs"></i>
                                            {{ $ortu->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="mobile-card-actions">
                                        <div class="action-buttons">
                                            {{-- Detail Button --}}
                                            <a href="{{ route('admin.users.show-orang-tua', $ortu->id) }}" class="action-btn"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Edit Button --}}
                                            <a href="{{ route('admin.users.edit-orang-tua', $ortu->id) }}" class="action-btn"
                                                title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- Toggle Status Button --}}
                                            <button type="button"
                                                class="action-btn {{ $ortu->is_active ? 'toggle-inactive' : 'toggle-active' }}"
                                                title="{{ $ortu->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                data-bs-toggle="modal" data-bs-target="#toggleStatusModal{{ $ortu->id }}">
                                                <i class="fas fa-{{ $ortu->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                            {{-- Delete Button --}}
                                            <button type="button" class="action-btn delete" title="Hapus Akun"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ortu->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-table-cell">
                                        <i class="fas fa-users fa-3x empty-table-icon"></i>
                                        <div class="empty-table-title">
                                            @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
                                                Tidak ada data orang tua yang sesuai dengan filter yang dipilih
                                            @else
                                                Belum ada data orang tua
                                            @endif
                                        </div>
                                        <small>
                                            @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
                                                Coba filter lain atau <a href="{{ route('admin.users.orang-tua') }}"
                                                    class="reset-filter-link">hapus semua filter</a>
                                            @else
                                                Orang tua akan terdaftar otomatis saat menambahkan siswa baru
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orangTua->hasPages())
                    <div class="pagination-wrap">
                        {{ $orangTua->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Toggle Status Modals --}}
        @foreach($orangTua as $ortu)
            <div class="modal fade" id="toggleStatusModal{{ $ortu->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header {{ $ortu->is_active ? 'bg-warning' : 'bg-success' }} text-white">
                            <h5 class="modal-title fw-bold modal-title-white">
                                <i class="fas fa-{{ $ortu->is_active ? 'ban' : 'check' }} me-2"></i>
                                Konfirmasi {{ $ortu->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Akun
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin {{ $ortu->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun orang tua:
                            </p>
                            <div class="info-box">
                                <div class="info-name">
                                    <i class="fas fa-user text-primary"></i>
                                    {{ $ortu->name }}
                                </div>
                                <div class="info-details">
                                    <div>
                                        <i class="fas fa-at detail-icon"></i>
                                        Username: {{ $ortu->username }}
                                    </div>
                                    @if($ortu->studentParents->count() > 0)
                                        <div>
                                            <i class="fas fa-child detail-icon"></i>
                                            Anak: {{ $ortu->studentParents->pluck('siswa.nama_lengkap')->join(', ') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($ortu->is_active)
                                <p class="modal-note">
                                    <i class="fas fa-info-circle text-warning"></i>
                                    <small class="text-muted">Orang tua yang dinonaktifkan tidak dapat login ke sistem.</small>
                                </p>
                            @else
                                <p class="modal-note">
                                    <i class="fas fa-info-circle text-success"></i>
                                    <small class="text-muted">Orang tua yang diaktifkan dapat login dan mengakses sistem
                                        kembali.</small>
                                </p>
                            @endif
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Batal
                            </button>
                            <form action="{{ route('admin.users.toggle-orang-tua-status', $ortu->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn {{ $ortu->is_active ? 'btn-warning btn-toggle-warning' : 'btn-primary btn-toggle-success' }}">
                                    <i class="fas fa-{{ $ortu->is_active ? 'ban' : 'check' }}"></i>
                                    Ya, {{ $ortu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Delete Modals --}}
        @foreach($orangTua as $ortu)
            <div class="modal fade" id="deleteModal{{ $ortu->id }}" tabindex="-1" aria-hidden="true">
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
                            <p>Apakah Anda yakin ingin menghapus akun orang tua:</p>
                            <div class="info-box">
                                <div class="info-name">
                                    <i class="fas fa-user text-primary"></i>
                                    {{ $ortu->name }}
                                </div>
                                <div class="info-details">
                                    <div>
                                        <i class="fas fa-at detail-icon"></i>
                                        Username: {{ $ortu->username }}
                                    </div>
                                    @if($ortu->studentParents->count() > 0)
                                        <div>
                                            <i class="fas fa-child detail-icon"></i>
                                            Anak: {{ $ortu->studentParents->pluck('siswa.nama_lengkap')->join(', ') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <p class="modal-note">
                                <i class="fas fa-info-circle text-danger"></i>
                                <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus akun login
                                    orang tua.</small>
                            </p>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Batal
                            </button>
                            <form action="{{ route('admin.users.delete-orang-tua', $ortu->id) }}" method="POST"
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
