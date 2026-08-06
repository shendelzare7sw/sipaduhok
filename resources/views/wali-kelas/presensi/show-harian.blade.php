@extends('layouts.sneat')

@section('title', 'Detail Presensi Harian')
@section('page-title', 'Detail Presensi Harian')
@section('page-subtitle', 'Laporan presensi tanggal ' . \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y'))

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-kelas/presensi/show-harian.css', 'resources/js/wali-kelas/presensi/show-harian.js'])
@endsection

@section('content')
<div class="wk-page">
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-start border-primary border-4 no-print">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-gray-900 mb-1">
                        <i class="fas fa-calendar-check me-2 text-primary"></i>
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                    </h5>
                    <p class="text-muted mb-0 small">Kelas {{ $kelas->nama_kelas }}</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" id="btnEditMode" class="btn btn-warning btn-sm shadow-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-print-page>
                        <i class="fas fa-print me-1"></i>Cetak
                    </button>
                    <a href="{{ route('wali.presensi.rekap-harian', ['bulan' => \Carbon\Carbon::parse($tanggal)->month, 'tahun' => \Carbon\Carbon::parse($tanggal)->year]) }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body py-3 text-center">
                    <div class="h3 fw-bold text-success mb-0">{{ $summary['hadir'] }}</div>
                    <small class="text-muted fw-bold">Hadir</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body py-3 text-center">
                    <div class="h3 fw-bold text-warning mb-0">{{ $summary['sakit'] }}</div>
                    <small class="text-muted fw-bold">Sakit</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body py-3 text-center">
                    <div class="h3 fw-bold text-info mb-0">{{ $summary['izin'] }}</div>
                    <small class="text-muted fw-bold">Izin</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-start border-danger border-4">
                <div class="card-body py-3 text-center">
                    <div class="h3 fw-bold text-danger mb-0">{{ $summary['alpha'] }}</div>
                    <small class="text-muted fw-bold">Alpha</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Table --}}
    <form id="formEditPresensi" action="{{ route('wali.presensi.input-harian') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
    </form>

    <div class="card shadow-sm">
        <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Detail Presensi Siswa
            </h6>
            <div id="editActions" class="d-flex gap-2 d-none">
                <button type="submit" form="formEditPresensi" class="btn btn-success btn-sm">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
                <button type="button" id="btnCancelEdit" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover wk-card-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center" width="80">NIS</th>
                            <th class="text-center" width="150">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $index => $siswa)
                            @php
                                $p = $presensiData->get($siswa->id);
                                $status = $p ? $p->status : null;
                            @endphp
                            <tr>
                                <td class="text-center align-middle fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <strong>{{ $siswa->nama_lengkap }}</strong>
                                </td>
                                <td class="text-center align-middle small text-muted">{{ $siswa->nis ?? $siswa->nisn }}</td>
                                <td class="text-center align-middle">
                                    {{-- View Mode --}}
                                    <span class="view-mode">
                                        @if($status === 'hadir')
                                            <span class="status-badge bg-label-success">Hadir</span>
                                        @elseif($status === 'sakit')
                                            <span class="status-badge bg-label-warning">Sakit</span>
                                        @elseif($status === 'izin')
                                            <span class="status-badge bg-label-info">Izin</span>
                                        @elseif($status === 'alpha')
                                            <span class="status-badge bg-label-danger">Alpha</span>
                                        @else
                                            <span class="status-badge bg-label-secondary">Belum Diisi</span>
                                        @endif
                                    </span>
                                    {{-- Edit Mode --}}
                                    <span class="edit-mode d-none">
                                        <input type="hidden" name="presensi[{{ $index }}][siswa_id]" value="{{ $siswa->id }}" form="formEditPresensi" disabled>
                                        <select name="presensi[{{ $index }}][status]" form="formEditPresensi" class="form-select form-select-sm" disabled>
                                            <option value="hadir" {{ $status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="sakit" {{ $status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="izin" {{ $status === 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="alpha" {{ $status === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="view-mode small text-muted">{{ $p->keterangan ?? '-' }}</span>
                                    <span class="edit-mode d-none">
                                        <input type="text" name="presensi[{{ $index }}][keterangan]" form="formEditPresensi" class="form-control form-control-sm" value="{{ $p->keterangan ?? '' }}" placeholder="Keterangan..." disabled>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada data siswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>

@endsection
