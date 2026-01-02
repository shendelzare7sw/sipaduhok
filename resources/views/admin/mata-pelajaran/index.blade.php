@extends('layouts.sneat')

@section('title', 'Kelola Mata Pelajaran')
@section('page-title', 'Kelola Mata Pelajaran')
@section('page-subtitle', 'Manajemen Mata Pelajaran untuk Semua Jenjang')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-secondary shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">KB</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['kb'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-dark shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">TKA</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['tka'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">TKB</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['tkb'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-success shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">SD</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sd'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">SMP</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['smp'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100">
            <div class="card-body">
                <div class="text-center">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">SMA</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sma'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter & Action Card --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            {{-- Filter --}}
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <label class="form-label mb-0 me-2">Filter Jenjang:</label>
                <select name="jenjang" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    <option value="KB" {{ $jenjang == 'KB' ? 'selected' : '' }}>KB</option>
                    <option value="TKA" {{ $jenjang == 'TKA' ? 'selected' : '' }}>TKA</option>
                    <option value="TKB" {{ $jenjang == 'TKB' ? 'selected' : '' }}>TKB</option>
                    <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
                @if($jenjang)
                    <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>

            {{-- Add Button --}}
            <a href="{{ route('admin.mata-pelajaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Mata Pelajaran
            </a>
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
            <table class="table table-hover">
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
                        <td>{{ $mataPelajaranList->firstItem() + $index }}</td>
                        <td>
                            @if($mapel->kode_mapel)
                                <span class="badge bg-secondary">{{ $mapel->kode_mapel }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $mapel->nama_mapel }}</strong>
                        </td>
                        <td>
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
                        <td>
                            <small class="text-muted">{{ $mapel->deskripsi ? Str::limit($mapel->deskripsi, 50) : '-' }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.mata-pelajaran.show', $mapel) }}"
                                   class="btn btn-sm btn-info"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.mata-pelajaran.edit', $mapel) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $mapel->id }}"
                                        title="Hapus">
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
            <a href="{{ route('admin.mata-pelajaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Mata Pelajaran Pertama
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Delete Modals --}}
@foreach($mataPelajaranList as $mapel)
<div class="modal fade" id="deleteModal{{ $mapel->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $mapel->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel{{ $mapel->id }}">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <form action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}" method="POST" class="d-inline">
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
