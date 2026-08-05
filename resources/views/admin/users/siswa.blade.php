@extends('layouts.sneat')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola data seluruh siswa aktif dan alumni')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/siswa.css'])
@endsection

@section('content')
<div class="user-list-shell">
        {{-- KONFIRMASI KEDUA HAPUS PERMANEN --}}
        {{-- Tahap 1 (peringatan berisi rincian data terkait) sudah dirender sebagai
             flash 'error' oleh layouts.sneat. Blok ini tahap 2: penegasan terakhir
             sebelum data benar-benar dimusnahkan. --}}
        @if(session('hapus_siswa_konfirmasi'))
            @php $konf = session('hapus_siswa_konfirmasi'); @endphp
            <div class="alert alert-danger border-danger border-3 shadow-sm">
                <h5 class="fw-bold mb-2">
                    <i class="fas fa-triangle-exclamation me-2"></i>Konfirmasi Terakhir - Hapus Permanen
                </h5>
                <p class="mb-2">
                    Anda akan menghapus <strong>{{ $konf['nama'] }}</strong>
                    @if(!empty($konf['nis'])) (NIS: {{ $konf['nis'] }}) @endif
                    beserta seluruh data berikut, <strong>permanen dan tidak bisa dikembalikan</strong>:
                </p>
                <ul class="mb-3">
                    @foreach($konf['blockers'] as $b)
                        <li>{{ $b }}</li>
                    @endforeach
                </ul>
                <p class="mb-3 small">
                    Data keuangan (tagihan &amp; pembayaran) dan akademik (nilai, presensi, rapor, ujian)
                    milik siswa ini akan ikut terhapus dari seluruh menu. Pastikan ini memang yang Anda inginkan.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <form action="{{ route('admin.users.delete-siswa', $konf['id']) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="konfirmasi_permanen" value="1">
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="fas fa-trash me-1"></i> Ya, Saya Yakin - Hapus Permanen
                        </button>
                    </form>
                    <a href="{{ route('admin.users.siswa') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Batal, Jangan Hapus
                    </a>
                </div>
            </div>
        @endif

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
                                <h5 class="mb-0 fw-bold text-dark">Daftar Siswa</h5>
                                <small class="text-muted">Total: {{ $siswa->total() }} siswa</small>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form (Moved to header line) --}}
                    <form action="{{ route('admin.users.siswa') }}" method="GET" id="filterForm" class="d-flex gap-2 align-items-center w-100-mobile">
                        {{-- Filter Dropdown --}}
                        <div class="dropdown filter-dropdown w-100-mobile">
                            <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false" 
                                data-bs-auto-close="outside" data-bs-display="static">
                                <span><i class="fas fa-filter me-1"></i> Filter</span>
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0 filter-dropdown-menu" aria-labelledby="filterDropdown">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>

                                {{-- Filter Cabang (First Priority) --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Cabang</label>
                                    <select name="cabang_id" id="cabangSelect" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangList as $cabang)
                                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Jenjang (Dependent on Cabang) --}}
                                <div class="mb-2" id="jenjangFilterContainer">
                                    <label class="form-label small fw-bold">Jenjang</label>
                                    <select name="jenjang" id="jenjangSelect" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang</option>
                                        @foreach($jenjangs as $j)
                                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Kelas (Dependent on Jenjang) --}}
                                <div class="mb-2" id="kelasFilterContainer">
                                    <label class="form-label small fw-bold">Kelas</label>
                                    <select name="kelas_nama" id="kelasSelect" class="form-select form-select-sm">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->nama_kelas }}"
                                                    data-cabang="{{ $kelas->cabang_id }}"
                                                    data-jenjang="{{ $kelas->jenjang }}"
                                                    {{ request('kelas_nama') == $kelas->nama_kelas ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Status --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Status Siswa</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                        <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                </div>
                            </div>
                        </div>

                        {{-- Search Input (Next to Filter) --}}
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
                    <form action="{{ route('admin.users.bulk-delete-siswa') }}" method="POST" id="bulkDeleteForm" class="bulk-delete-form">
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
                                <a href="{{ route('admin.users.siswa.print') }}?{{ http_build_query(request()->all()) }}" class="dropdown-item" target="_blank">
                                    <i class="fas fa-print me-2"></i> Cetak Data (PDF)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.import-siswa') }}" class="dropdown-item">
                                    <i class="fas fa-file-import me-2"></i> Import Excel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.siswa-template') }}" class="dropdown-item">
                                    <i class="fas fa-download me-2"></i> Download Template
                                </a>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.users.create-siswa') }}" class="btn-primary btn-nowrap">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-md-inline">Tambah Siswa</span>
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
                            <th>Nama Siswa</th>
                            <th>NIS / NISN</th>
                            <th class="th-jenjang">Jenjang</th>
                            <th>Kelas</th>
                            <th>Cabang</th>
                            <th class="th-status">Status</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $s)
                            <tr>
                                <td class="text-center mobile-card-checkbox">
                                    <input type="checkbox" name="ids[]" class="form-check-input select-item" value="{{ $s->id }}">
                                </td>
                                <td class="mobile-hide row-number">
                                    {{ $siswa->firstItem() + $index }}</td>
                                <td class="mobile-card-head">
                                    <div class="student-name-cell">{{ $s->user->name ?? $s->nama_lengkap }}</div>
                                    <small class="cell-muted">
                                        <i class="fas fa-{{ $s->jenis_kelamin == 'L' ? 'mars' : 'venus' }} icon-xs"></i>
                                        {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </small>
                                </td>
                                <td data-label="NIS / NISN">
                                    <div class="student-id">
                                        {{ $s->nis }}</div>
                                    <small class="student-id-sub">{{ $s->nisn }}</small>
                                </td>
                                <td data-label="Jenjang">
                                    @if($s->kelas)
                                        @php
                                            $jenjangBadge = [
                                                'KB' => 'badge-kb',
                                                'TKA' => 'badge-tka',
                                                'TKB' => 'badge-tkb',
                                                'SD' => 'badge-sd',
                                                'SMP' => 'badge-smp',
                                                'SMA' => 'badge-sma',
                                            ][$s->kelas->jenjang] ?? 'badge-class';
                                        @endphp
                                        <span class="badge {{ $jenjangBadge }} badge-compact">
                                            {{ $s->kelas->jenjang }}
                                        </span>
                                    @else
                                        <span class="placeholder-dash">-</span>
                                    @endif
                                </td>
                                <td data-label="Kelas">
                                    @if($s->kelas)
                                        <span class="badge-class">
                                            <i class="fas fa-door-open icon-xs"></i>
                                            {{ $s->kelas->nama_kelas }}
                                        </span>
                                    @elseif($s->status === 'lulus')
                                        <span class="class-status graduated">
                                            <i class="fas fa-graduation-cap"></i>
                                            Lulus
                                        </span>
                                    @else
                                        <span class="class-status unassigned">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Belum masuk kelas
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Cabang" class="cabang-cell">{{ $s->cabang->nama_cabang ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="badge-status {{ $s->status }}">
                                        @if($s->status === 'aktif')
                                            <i class="fas fa-check-circle icon-xs"></i>
                                        @elseif($s->status === 'lulus')
                                            <i class="fas fa-graduation-cap icon-xs"></i>
                                        @elseif($s->status === 'pindah')
                                            <i class="fas fa-exchange-alt icon-xs"></i>
                                        @else
                                            <i class="fas fa-times-circle icon-xs"></i>
                                        @endif
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-buttons">
                                        {{-- View Button --}}
                                        <a href="{{ route('admin.users.show-siswa', $s->id) }}" class="action-btn view"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.users.edit-siswa', $s->id) }}" class="action-btn edit"
                                            title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete Button --}}
                                        <button type="button" class="action-btn delete" title="Hapus Data"
                                            data-bs-toggle="modal" data-bs-target="#deleteSiswaModal{{ $s->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-table-cell">
                                    <i class="fas fa-user-graduate fa-3x empty-table-icon"></i>
                                    <div class="empty-table-title">
                                        @if(request('search') || request('jenjang') || request('kelas_nama') || request('cabang_id') || request('status'))
                                            Tidak ada data siswa yang sesuai dengan filter yang dipilih
                                        @else
                                            Belum ada data siswa
                                        @endif
                                    </div>
                                    <small>
                                        @if(request('search') || request('jenjang') || request('kelas_nama') || request('cabang_id') || request('status'))
                                            Coba filter lain atau <a href="{{ route('admin.users.siswa') }}" class="reset-filter-link">hapus semua filter</a>
                                        @else
                                            Silakan tambah data siswa baru
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswa->hasPages())
                <div class="pagination-wrap">
                    {{ $siswa->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modals for Siswa --}}
    @foreach($siswa as $s)
        <div class="modal fade" id="deleteSiswaModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
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
                        <p>Apakah Anda yakin ingin menghapus data siswa:</p>
                        <div class="student-info-box">
                            <div class="student-name">
                                <i class="fas fa-user-graduate text-primary"></i>
                                {{ $s->nama_lengkap }}
                            </div>
                            <div class="student-details">
                                <div class="detail-item">
                                    <i class="fas fa-id-card detail-icon"></i>
                                    <span>NIS: {{ $s->nis }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-hashtag detail-icon"></i>
                                    <span>NISN: {{ $s->nisn }}</span>
                                </div>
                                @if($s->kelas)
                                    <div class="detail-item">
                                        <i class="fas fa-door-open detail-icon"></i>
                                        <span>{{ $s->kelas->nama_kelas }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p class="modal-danger-note">
                            <i class="fas fa-info-circle"></i>
                            <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait
                                termasuk akun login siswa.</small>
                        </p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <form action="{{ route('admin.users.delete-siswa', $s->id) }}" method="POST" class="d-inline">
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
