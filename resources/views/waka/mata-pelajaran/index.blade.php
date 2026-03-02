@extends('layouts.sneat')

@section('title', 'Kelola Mata Pelajaran')
@section('page-title', 'Kelola Mata Pelajaran')
@section('page-subtitle', 'Manajemen Mata Pelajaran untuk Semua Jenjang')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        .stats-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 8px; }
        .stat-card {
            min-width: 100px; flex: 1; background: #fff; border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 14px 10px; text-align: center;
        }
        .stat-card .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
        .stat-card .stat-value { font-size: 20px; font-weight: 700; color: #111827; }
        .filter-action-bar {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
        }
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }

        @media (max-width: 767.98px) {
            .stat-card { min-width: 80px; padding: 10px 6px; }
            .stat-card .stat-value { font-size: 16px; }
            .filter-action-bar { flex-direction: column; align-items: stretch; }
            .action-buttons { justify-content: stretch; }
            .action-buttons .btn { flex: 1; justify-content: center; font-size: 12px; padding: 8px 10px; }
            .action-buttons .btn .btn-text { display: none; }

            .table-card-mobile thead { display: none; }
            .table-card-mobile tbody tr {
                display: block; background: #fff; border-radius: 10px;
                box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 14px; margin-bottom: 10px;
            }
            .table-card-mobile tbody td {
                display: flex; justify-content: space-between; align-items: center;
                padding: 6px 0; border: none; font-size: 13px;
            }
            .table-card-mobile tbody td::before {
                content: attr(data-label); font-weight: 600; color: #6b7280; margin-right: 12px; white-space: nowrap;
            }
            .table-card-mobile tbody td.td-actions {
                justify-content: flex-end; padding-top: 10px;
                border-top: 1px solid #f3f4f6; margin-top: 6px;
            }
            .table-card-mobile tbody td.td-actions::before { display: none; }
        }
    </style>

    {{-- Stats Cards --}}
    <div class="stats-scroll mb-4">
        <div class="stat-card">
            <div class="stat-label text-primary">Total</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label text-secondary">KB</div>
            <div class="stat-value">{{ $stats['kb'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label" style="color:#343a40">TKA</div>
            <div class="stat-value">{{ $stats['tka'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label text-danger">TKB</div>
            <div class="stat-value">{{ $stats['tkb'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label text-success">SD</div>
            <div class="stat-value">{{ $stats['sd'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label text-info">SMP</div>
            <div class="stat-value">{{ $stats['smp'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label text-warning">SMA</div>
            <div class="stat-value">{{ $stats['sma'] }}</div>
        </div>
    </div>

    {{-- Filter & Action Card --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="filter-action-bar">
                {{-- Filter --}}
                <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                    <label class="form-label mb-0 me-2">Filter Jenjang:</label>
                    <select name="jenjang" class="form-select form-select-sm" style="width: auto;"
                        onchange="this.form.submit()">
                        <option value="">Semua Jenjang</option>
                        <option value="KB" {{ $jenjang == 'KB' ? 'selected' : '' }}>KB</option>
                        <option value="TKA" {{ $jenjang == 'TKA' ? 'selected' : '' }}>TKA</option>
                        <option value="TKB" {{ $jenjang == 'TKB' ? 'selected' : '' }}>TKB</option>
                        <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                    </select>
                    @if($jenjang)
                        <a href="{{ route('waka.mata-pelajaran.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- Action Buttons --}}
                <div class="action-buttons">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        onclick="window.open('{{ route('waka.mata-pelajaran.print', request()->only('jenjang')) }}', '_blank')">
                        <i class="fas fa-print me-1"></i><span class="btn-text"> Cetak</span>
                    </button>
                    <a href="{{ route('waka.mata-pelajaran.import') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-import me-1"></i><span class="btn-text"> Import</span>
                    </a>
                    <a href="{{ route('waka.mata-pelajaran.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i><span class="btn-text"> Tambah</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mata Pelajaran Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list text-primary me-2"></i>Daftar Mata Pelajaran
                @if($jenjang)
                    <span class="badge bg-primary">{{ $jenjang }}</span>
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($mataPelajaranList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-card-mobile">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="30%">Nama Mata Pelajaran</th>
                                <th width="10%">Jenjang</th>
                                <th width="25%">Deskripsi</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mataPelajaranList as $index => $mapel)
                                <tr>
                                    <td data-label="No">{{ $mataPelajaranList->firstItem() + $index }}</td>
                                    <td data-label="Kode">
                                        @if($mapel->kode_mapel)
                                            <span class="badge bg-secondary">{{ $mapel->kode_mapel }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td data-label="Nama">
                                        <strong>{{ $mapel->nama_mapel }}</strong>
                                    </td>
                                    <td data-label="Jenjang">
                                        @if($mapel->jenjang == 'KB')
                                            <span class="badge bg-secondary">KB</span>
                                        @elseif($mapel->jenjang == 'TKA')
                                            <span class="badge bg-dark">TKA</span>
                                        @elseif($mapel->jenjang == 'TKB')
                                            <span class="badge bg-danger">TKB</span>
                                        @elseif($mapel->jenjang == 'SD')
                                            <span class="badge bg-success">SD</span>
                                        @elseif($mapel->jenjang == 'SMP')
                                            <span class="badge bg-info">SMP</span>
                                        @elseif($mapel->jenjang == 'SMA')
                                            <span class="badge bg-warning">SMA</span>
                                        @endif
                                    </td>
                                    <td data-label="Deskripsi">
                                        <small class="text-muted">{{ $mapel->deskripsi ? Str::limit($mapel->deskripsi, 50) : '-' }}</small>
                                    </td>
                                    <td class="text-center td-actions">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('waka.mata-pelajaran.show', $mapel) }}" class="btn btn-sm btn-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('waka.mata-pelajaran.edit', $mapel) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
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

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $mataPelajaranList->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">
                        @if($jenjang)
                            Belum ada mata pelajaran untuk jenjang {{ $jenjang }}.
                        @else
                            Belum ada data mata pelajaran.
                        @endif
                    </p>
                    <a href="{{ route('waka.mata-pelajaran.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Mata Pelajaran Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modals --}}
    @foreach($mataPelajaranList as $mapel)
        <div class="modal fade" id="deleteModal{{ $mapel->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $mapel->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel{{ $mapel->id }}">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus mata pelajaran:</p>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;">
                            <div style="font-weight: 600; font-size: 16px; color: #212529; margin-bottom: 8px;">
                                <i class="fas fa-book text-danger me-2"></i>
                                {{ $mapel->nama_mapel }}
                            </div>
                            <div style="font-size: 13px; color: #6c757d;">
                                @if($mapel->kode_mapel)
                                    <i class="fas fa-tag me-1"></i> Kode: <strong>{{ $mapel->kode_mapel }}</strong> •
                                @endif
                                <i class="fas fa-layer-group me-1"></i> Jenjang: <strong>{{ $mapel->jenjang }}</strong>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">
                            <i class="fas fa-info-circle text-danger me-1"></i>
                            <small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <form action="{{ route('waka.mata-pelajaran.destroy', $mapel) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection