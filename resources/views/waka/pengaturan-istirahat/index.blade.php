@extends('layouts.sneat')

@section('title', 'Pengaturan Waktu Istirahat')
@section('page-title', 'Pengaturan Waktu Istirahat')
@section('page-subtitle', 'Kelola waktu istirahat per jenjang pendidikan')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
{{-- Breadcrumb / Back Button --}}
<div class="mb-3">
    <a href="{{ route('waka.jadwal-pelajaran.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Jadwal Pelajaran
    </a>
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

{{-- Info Card --}}
<div class="card mb-4 border-left-info">
    <div class="card-body">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle text-info fa-2x me-3"></i>
            <div>
                <h6 class="mb-2"><strong>Informasi Pengaturan Istirahat</strong></h6>
                <ul class="mb-0 small text-muted">
                    <li>Waktu istirahat dikonfigurasi per jenjang (KB, TKA, TKB, SD, SMP, SMA)</li>
                    <li>Maksimal 2 waktu istirahat per jenjang</li>
                    <li>Waktu istirahat akan otomatis memblokir slot waktu saat membuat jadwal</li>
                    <li>Waktu istirahat akan otomatis muncul di cetak jadwal dengan highlight kuning</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Action Button --}}
<div class="mb-4">
    <a href="{{ route('waka.pengaturan-istirahat.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Waktu Istirahat
    </a>
</div>

{{-- Pengaturan per Jenjang --}}
@foreach($jenjangList as $jenjang)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-clock text-primary me-2"></i>
            Waktu Istirahat Jenjang
            @if($jenjang == 'KB')
                <span class="badge bg-secondary">KB</span>
            @elseif($jenjang == 'TKA')
                <span class="badge bg-dark">TKA</span>
            @elseif($jenjang == 'TKB')
                <span class="badge bg-danger">TKB</span>
            @elseif($jenjang == 'SD')
                <span class="badge bg-success">SD</span>
            @elseif($jenjang == 'SMP')
                <span class="badge bg-info">SMP</span>
            @elseif($jenjang == 'SMA')
                <span class="badge bg-warning">SMA</span>
            @endif
        </h5>
        @if($pengaturanPerJenjang[$jenjang]->count() < 2)
            <a href="{{ route('waka.pengaturan-istirahat.create', ['jenjang' => $jenjang]) }}"
               class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Istirahat
            </a>
        @endif
    </div>
    <div class="card-body">
        @if($pengaturanPerJenjang[$jenjang]->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">Urutan</th>
                        <th width="20%">Nama Istirahat</th>
                        <th width="15%">Jam Mulai</th>
                        <th width="15%">Jam Selesai</th>
                        <th width="25%">Hari Aktif</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaturanPerJenjang[$jenjang] as $pengaturan)
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $pengaturan->urutan }}</span>
                        </td>
                        <td>
                            <strong>{{ $pengaturan->nama_istirahat }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-clock me-1 text-success"></i>{{ substr($pengaturan->jam_mulai, 0, 5) }}
                        </td>
                        <td>
                            <i class="fas fa-clock me-1 text-danger"></i>{{ substr($pengaturan->jam_selesai, 0, 5) }}
                        </td>
                        <td>
                            @foreach($pengaturan->hari_aktif as $hari)
                                <span class="badge bg-secondary me-1">{{ $hari }}</span>
                            @endforeach
                        </td>
                        <td class="text-center">
                            <form action="{{ route('waka.pengaturan-istirahat.toggle-status', $pengaturan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $pengaturan->is_active ? 'btn-success' : 'btn-secondary' }}" title="Klik untuk toggle status">
                                    @if($pengaturan->is_active)
                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                    @else
                                        <i class="fas fa-times-circle me-1"></i>Nonaktif
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('waka.pengaturan-istirahat.edit', $pengaturan) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $pengaturan->id }}"
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
        @else
        <div class="text-center py-4">
            <i class="fas fa-coffee fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada waktu istirahat untuk jenjang {{ $jenjang }}.</p>
        </div>
        @endif
    </div>
</div>
@endforeach

{{-- Delete Modals --}}
@foreach($jenjangList as $jenjang)
    @foreach($pengaturanPerJenjang[$jenjang] as $pengaturan)
    <div class="modal fade" id="deleteModal{{ $pengaturan->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $pengaturan->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel{{ $pengaturan->id }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Apakah Anda yakin ingin menghapus waktu istirahat:</p>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;">
                        <div style="font-weight: 600; font-size: 16px; color: #212529; margin-bottom: 8px;">
                            <i class="fas fa-coffee text-danger me-2"></i>
                            {{ $pengaturan->nama_istirahat }}
                        </div>
                        <div style="font-size: 13px; color: #6c757d;">
                            <i class="fas fa-layer-group me-1"></i> Jenjang: <strong>{{ $pengaturan->jenjang }}</strong> •
                            <i class="fas fa-clock me-1"></i> Waktu: <strong>{{ substr($pengaturan->jam_mulai, 0, 5) }} - {{ substr($pengaturan->jam_selesai, 0, 5) }}</strong>
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
                    <form action="{{ route('waka.pengaturan-istirahat.destroy', $pengaturan) }}" method="POST" class="d-inline">
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
@endforeach
@endsection
