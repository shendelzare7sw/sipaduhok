@extends('layouts.app')

@section('title', 'Kelola Mata Pelajaran')
@section('page-title', 'Kelola Mata Pelajaran')
@section('page-subtitle', 'Manajemen Mata Pelajaran untuk Semua Jenjang')


@section('styles')
    @vite(['resources/css/waka/mata-pelajaran/index.css'])
@endsection

@section('content')

    @if(session('import_warnings'))
        <div class="alert alert-warning import-warning">
            <div class="fw-bold mb-2">
                <i class="fas fa-exclamation-triangle me-1"></i> Beberapa baris import dilewati
            </div>
            <ul class="mb-0 ps-3">
                @foreach(session('import_warnings') as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Stats Chips -->
    <div class="stat-scroll">
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-total">Total</div>
            <div class="stat-chip-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-kb">KB</div>
            <div class="stat-chip-value">{{ $stats['kb'] }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-tka">TKA</div>
            <div class="stat-chip-value">{{ $stats['tka'] }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-tkb">TKB</div>
            <div class="stat-chip-value">{{ $stats['tkb'] }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-sd">SD</div>
            <div class="stat-chip-value">{{ $stats['sd'] }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-smp">SMP</div>
            <div class="stat-chip-value">{{ $stats['smp'] }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-label stat-label-sma">SMA</div>
            <div class="stat-chip-value">{{ $stats['sma'] }}</div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="mp-card">
        <div class="mp-card-header">
            <h5 class="mp-card-title">
                <i class="fas fa-book mp-card-title-icon"></i> Daftar Mata Pelajaran
                @if($jenjang)
                    <span class="badge bg-primary ms-2">{{ $jenjang }}</span>
                @endif
                @if($search !== '')
                    <span class="badge bg-secondary ms-2 search-badge">Cari: {{ Str::limit($search, 36) }}</span>
                @endif
            </h5>
            <div class="header-actions d-flex gap-2">
                <a href="{{ route('waka.mata-pelajaran.print', request()->only(['jenjang', 'search'])) }}" target="_blank" class="btn btn-secondary text-white btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
                <a href="{{ route('waka.mata-pelajaran.import') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1 text-white">
                    <i class="fas fa-file-import"></i> <span class="d-none d-sm-inline">Import</span>
                </a>
                <a href="{{ route('waka.mata-pelajaran.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Tambah</span>
                </a>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" class="mb-0 filter-form">
            <div class="filter-wrapper">
                <div class="filter-field filter-field-search">
                    <label for="mapel-search" class="filter-label">Cari Mapel</label>
                    <div class="search-control">
                        <i class="fas fa-search search-icon"></i>
                        <input type="search"
                               name="search"
                               id="mapel-search"
                               class="form-control filter-search"
                               value="{{ $search }}"
                               placeholder="Cari nama, kode, deskripsi, atau agama..."
                               autocomplete="off">
                    </div>
                </div>

                <div class="filter-field filter-field-jenjang">
                    <label for="jenjang-filter" class="filter-label">Jenjang</label>
                    <select name="jenjang" id="jenjang-filter" class="form-select filter-select" data-auto-submit>
                        <option value="">Semua Jenjang</option>
                        <option value="KB" {{ $jenjang == 'KB' ? 'selected' : '' }}>KB</option>
                        <option value="TKA" {{ $jenjang == 'TKA' ? 'selected' : '' }}>TKA</option>
                        <option value="TKB" {{ $jenjang == 'TKB' ? 'selected' : '' }}>TKB</option>
                        <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i> <span>Cari</span>
                    </button>
                    @if($jenjang || $search !== '')
                        <a href="{{ route('waka.mata-pelajaran.index') }}" class="btn btn-outline-danger btn-sm filter-reset-btn">
                            <i class="fas fa-times"></i> <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        @if($mataPelajaranList->count() > 0)
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th width="28%">Nama Mata Pelajaran</th>
                            <th width="10%">Jenjang</th>
                            <th width="12%">Filter Agama</th>
                            <th width="20%">Deskripsi</th>
                            <th width="10%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mataPelajaranList as $index => $mapel)
                            <tr>
                                <td data-label="No">{{ $mataPelajaranList->firstItem() + $index }}</td>
                                <td data-label="Kode">
                                    @if($mapel->kode_mapel)
                                        <span class="kode-badge">{{ $mapel->kode_mapel }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="mobile-card-head" data-label="Nama">
                                    <strong class="mapel-name">{{ $mapel->nama_mapel }}</strong>
                                </td>
                                <td data-label="Jenjang">
                                    @php
                                        $jnjClass = [
                                            'KB' => 'bg-jnj-kb',
                                            'TKA' => 'bg-jnj-tka',
                                            'TKB' => 'bg-jnj-tkb',
                                            'SD' => 'bg-jnj-sd',
                                            'SMP' => 'bg-jnj-smp',
                                            'SMA' => 'bg-jnj-sma',
                                        ][$mapel->jenjang] ?? 'bg-jnj-kb';
                                    @endphp
                                    <span class="badge {{ $jnjClass }} badge-jnj">{{ $mapel->jenjang }}</span>
                                </td>
                                <td data-label="Filter Agama">
                                    @if($mapel->filter_agama)
                                        <span class="agama-badge">{{ $mapel->filter_agama }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td data-label="Deskripsi">
                                    <small class="text-muted description-text">{{ $mapel->deskripsi ? Str::limit($mapel->deskripsi, 50) : '-' }}</small>
                                </td>
                                <td class="td-actions text-end">
                                    <div class="d-flex justify-content-end gap-1 action-btns">
                                        <a href="{{ route('waka.mata-pelajaran.show', $mapel) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('waka.mata-pelajaran.edit', $mapel) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $mapel->id }}" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($mataPelajaranList->hasPages())
            <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap pagination-footer">
                <span class="text-muted small">Menampilkan {{ $mataPelajaranList->firstItem() ?? 0 }} - {{ $mataPelajaranList->lastItem() ?? 0 }} dari {{ $mataPelajaranList->total() }} mapel</span>
                <div class="mt-2 mt-sm-0 pagination-links">
                    {{ $mataPelajaranList->withQueryString()->links() }}
                </div>
            </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <h3>
                    @if($search !== '' && $jenjang)
                        Tidak ada mata pelajaran untuk "{{ $search }}" di jenjang {{ $jenjang }}.
                    @elseif($search !== '')
                        Tidak ada mata pelajaran untuk "{{ $search }}".
                    @elseif($jenjang)
                        Belum ada mata pelajaran untuk jenjang {{ $jenjang }}.
                    @else
                        Belum ada data mata pelajaran.
                    @endif
                </h3>
                <div class="empty-actions">
                    @if($jenjang || $search !== '')
                        <a href="{{ route('waka.mata-pelajaran.index') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                            <i class="fas fa-times me-1"></i> Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('waka.mata-pelajaran.create') }}" class="btn btn-primary btn-sm px-3 rounded-pill">
                        <i class="fas fa-plus me-1"></i> {{ ($jenjang || $search !== '') ? 'Tambah Mata Pelajaran' : 'Tambah Mata Pelajaran Pertama' }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Delete Modals --}}
    @foreach($mataPelajaranList as $mapel)
        <div class="modal fade" id="deleteModal{{ $mapel->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow delete-modal-content">
                    <div class="modal-header border-bottom-0 pb-0">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-0 pb-4">
                        <div class="mb-3">
                            <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center delete-modal-icon">
                                <i class="fas fa-trash fs-3 text-danger"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2">Hapus Mata Pelajaran?</h5>
                        <p class="text-muted mb-1 delete-modal-subtitle">
                            <strong class="mapel-name">{{ $mapel->nama_mapel }}</strong>
                        </p>
                        <p class="text-muted mb-3 delete-modal-meta">
                            @if($mapel->kode_mapel) Kode: {{ $mapel->kode_mapel }} &bull; @endif Jenjang: {{ $mapel->jenjang }}
                        </p>
                        <p class="text-danger small mb-0"><i class="fas fa-info-circle me-1"></i> Tindakan ini tidak dapat dibatalkan!</p>

                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary fw-medium px-4 delete-modal-action" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('waka.mata-pelajaran.destroy', $mapel) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger fw-medium px-4 d-flex align-items-center gap-2 text-white delete-modal-action">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    @vite(['resources/js/waka/mata-pelajaran/index.js'])
@endsection
