@extends('layouts.sneat')

@section('title', 'Data Kelas')

@section('page-title', 'Data Kelas')
@section('page-subtitle')
Kelola data kelas di cabang Anda {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/kelas/index.css'])
@endsection

@section('content')
<div class="kelas-index-page">
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-total">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalKelas'] }}</div>
                <div class="stat-label">Total Kelas</div>
                <div class="stat-desc">{{ $currentTahunAjaran ? $currentTahunAjaran->nama_tahun_ajaran : 'Semua Tahun' }}</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-siswa">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalSiswa'] }}</div>
                <div class="stat-label">Siswa Terdaftar</div>
                <div class="stat-desc">Di tahun akademik pilihan</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-wali">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['kelasWithWali'] }}</div>
                <div class="stat-label">Terisi Wali Kelas</div>
                <div class="stat-desc">Telah di-assign wali</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-warning">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['kelasWithoutWali'] }}</div>
                <div class="stat-label">Belum Ada Wali</div>
                <div class="stat-desc text-warning">Perlu tindakan</div>
            </div>
        </div>
    </div>

    <div class="kls-card">
        <div class="kls-card-header">
            <h5 class="kls-card-title">
                <i class="fas fa-list text-primary"></i> Daftar Kelas
            </h5>
            <div class="header-actions d-flex gap-2">
                <a href="{{ route('waka.kelas.print', request()->query()) }}" class="btn btn-secondary text-white d-flex align-items-center gap-1" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
                <a href="{{ route('waka.kelas.import') }}" class="btn btn-success text-white d-flex align-items-center gap-1">
                    <i class="fas fa-file-import"></i> <span class="d-none d-sm-inline">Import Excel</span>
                </a>
                <a href="{{ route('waka.kelas.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Kelas Baru</span>
                </a>
            </div>
        </div>

        <form action="{{ route('waka.kelas.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama atau kode kelas..." value="{{ request('search') }}">
                </div>

                <select name="tahun_ajaran_id" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <select name="jenjang" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $j)
                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm px-3 btn-filter">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'jenjang']) || (request('tahun_ajaran_id') && request('tahun_ajaran_id') != $currentTahunAjaran?->id))
                    <a href="{{ route('waka.kelas.index') }}" class="btn btn-outline-danger btn-sm px-3 btn-reset">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th width="280">Info Kelas</th>
                        <th>Jenjang</th>
                        <th>Wali Kelas</th>
                        <th width="120">Kuota / Siswa</th>
                        <th class="text-end" width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $k)
                    <tr>
                        <td class="mobile-card-head" data-label="Kelas">
                            <div class="kelas-info">
                                <span class="kelas-nama">{{ $k->nama_kelas }}</span>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="kelas-kode">{{ $k->kode_kelas }}</span>
                                    <span class="text-muted cabang-label">
                                        <i class="fas fa-building me-1"></i>{{ $k->cabang->nama_cabang ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Jenjang">
                            @php
                                $jenjangClass = [
                                    'KB' => 'bg-jnj-kb',
                                    'TKA' => 'bg-jnj-tka',
                                    'TKB' => 'bg-jnj-tkb',
                                    'SD' => 'bg-jnj-sd',
                                    'SMP' => 'bg-jnj-smp',
                                    'SMA' => 'bg-jnj-sma',
                                ][$k->jenjang] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $jenjangClass }} badge-jnj">{{ $k->jenjang }}</span>
                        </td>
                        <td data-label="Wali Kelas">
                            @if($k->waliKelasAssignments->count() > 0)
                                <div class="d-flex flex-column gap-1">
                                @foreach($k->waliKelasAssignments as $assignment)
                                    <div class="wali-wrapper">
                                        <div class="wali-avatar">{{ strtoupper(substr($assignment->tenagaPendidik->nama_lengkap, 0, 1)) }}</div>
                                        <span class="fw-medium text-dark wali-name-small">{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                    </div>
                                @endforeach
                                </div>
                            @else
                                <span class="badge bg-label-warning px-2 py-1"><i class="fas fa-exclamation-circle me-1"></i> Belum ada</span>
                            @endif
                        </td>
                        <td data-label="Kuota Siswa">
                            @php
                                $percentage = $k->kuota_siswa > 0 ? ($k->siswa_count / $k->kuota_siswa) * 100 : 0;
                                $kuotaStatus = $percentage < 50 ? 'low' : ($percentage < 80 ? 'mid' : 'high');
                            @endphp
                            <div class="kuota-progress">
                                <span class="kuota-text">
                                    <span class="kuota-count-{{ $kuotaStatus }}">{{ $k->siswa_count }}</span> / {{ $k->kuota_siswa ?: '-' }}
                                </span>
                                <div class="kuota-bar">
                                    <div class="kuota-fill kuota-fill-{{ $kuotaStatus }}" data-kuota-width="{{ min($percentage, 100) }}"></div>
                                </div>
                            </div>
                        </td>
                        <td class="td-actions text-end" data-label="Aksi">
                            <div class="d-flex justify-content-end gap-1 action-btns">
                                <a href="{{ route('waka.kelas.show', $k->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('waka.kelas.edit', $k->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger text-white"
                                        data-delete-kelas
                                        data-delete-url="{{ route('waka.kelas.destroy', $k->id) }}"
                                        data-delete-name="{{ $k->nama_kelas }}"
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
                                <i class="fas fa-chalkboard fs-1 mb-3 empty-table-icon"></i>
                                <h6 class="mb-1">Tidak Ada Data Kelas</h6>
                                <p class="small mb-0">Belum ada kelas yang ditambahkan atau tidak ada hasil pencarian.</p>
                                <a href="{{ route('waka.kelas.create') }}" class="btn btn-primary btn-sm mt-3 px-3 rounded-pill">
                                    <i class="fas fa-plus me-1"></i> Tambah Kelas Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kelas->hasPages())
        <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-muted small">Menampilkan {{ $kelas->firstItem() ?? 0 }} - {{ $kelas->lastItem() ?? 0 }} dari total {{ $kelas->total() }}</span>
            <div class="mt-2 mt-sm-0">
                {{ $kelas->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center delete-icon-circle">
                        <i class="fas fa-trash-alt fs-3 text-danger"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Kelas?</h5>
                <p class="text-muted mb-4 delete-modal-text">Kelas <span id="deleteItemName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>

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
    @vite(['resources/js/waka/kelas/index.js'])
@endsection
