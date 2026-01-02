@extends('layouts.sneat')

@section('title', 'Validasi Akses Siswa')
@section('page-title', 'Validasi Akses')
@section('page-subtitle', isset($kelas) && $kelas ? 'Validasi akses ujian dan rapor siswa kelas ' . $kelas->nama_kelas : 'Validasi akses siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .table-validasi thead th {
        background-color: #f8f9fc;
        text-align: center;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #4e73df;
    }
    .status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 5px;
        padding: 5px 10px;
        background: #f8f9fc;
        border-radius: 5px;
    }
    .stat-card-custom {
        border-radius: 10px;
        border-left: 4px solid;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">
    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @else
        {{-- STATISTICS --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-success border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Ujian Tervalidasi</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['ujianValid'] ?? 0 }}</div>
                                <div class="text-xs text-muted">dari {{ $siswaList->count() }} siswa</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-double fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-info border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Rapor Tervalidasi</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['raporValid'] ?? 0 }}</div>
                                <div class="text-xs text-muted">dari {{ $siswaList->count() }} siswa</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-file-contract fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-warning border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pending Ujian</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['ujianPending'] ?? 0 }}</div>
                                <div class="text-xs text-muted">menunggu wali kelas</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-hourglass-half fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-warning border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pending Rapor</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['raporPending'] ?? 0 }}</div>
                                <div class="text-xs text-muted">menunggu wali kelas</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-hourglass fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BULK ACTIONS --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-bolt me-2 text-warning"></i>Aksi Validasi Massal</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <button type="button" class="btn btn-success w-100 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#validasiSemuaUjianModal">
                            <i class="fas fa-check-circle me-2"></i>Validasi Semua Akses Ujian
                        </button>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="button" class="btn btn-info w-100 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#validasiSemuaRaporModal">
                            <i class="fas fa-check-circle me-2"></i>Validasi Semua Akses Rapor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE LIST --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Status Akses Siswa</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-validasi mb-0">
                        <thead>
                            <tr>
                                <th width="50">NO</th>
                                <th width="120">NIS</th>
                                <th class="text-start">NAMA SISWA</th>
                                <th>STATUS VALIDASI</th>
                                <th width="220">AKSI WALI KELAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaList ?? [] as $index => $siswa)
                                <tr>
                                    <td class="text-center align-middle fw-bold">{{ $index + 1 }}</td>
                                    <td class="text-center align-middle">{{ $siswa->nis }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                    </td>
                                    <td class="align-middle px-4">
                                        {{-- Status Ujian --}}
                                        <div class="status-row">
                                            <small class="fw-bold">Akses Ujian</small>
                                            @if($siswa->validasi_ujian_bendahara && $siswa->validasi_ujian_wali)
                                                <span class="badge bg-success px-2 py-1"><i class="fas fa-check me-1"></i>Valid</span>
                                            @elseif($siswa->validasi_ujian_bendahara)
                                                <span class="badge bg-warning text-white px-2 py-1"><i class="fas fa-clock me-1"></i>Pending Wali</span>
                                            @else
                                                <span class="badge bg-light border px-2 py-1 text-muted"><i class="fas fa-lock me-1"></i>Locked</span>
                                            @endif
                                        </div>
                                        {{-- Status Rapor --}}
                                        <div class="status-row">
                                            <small class="fw-bold">Akses Rapor</small>
                                            @if($siswa->validasi_rapor_bendahara && $siswa->validasi_rapor_wali)
                                                <span class="badge bg-success px-2 py-1"><i class="fas fa-check me-1"></i>Valid</span>
                                            @elseif($siswa->validasi_rapor_bendahara)
                                                <span class="badge bg-warning text-white px-2 py-1"><i class="fas fa-clock me-1"></i>Pending Wali</span>
                                            @else
                                                <span class="badge bg-light border px-2 py-1 text-muted"><i class="fas fa-lock me-1"></i>Locked</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center align-middle py-3">
                                        {{-- UJIAN --}}
                                        @if($siswa->validasi_ujian_bendahara)
                                            <form action="{{ route($siswa->validasi_ujian_wali ? 'wali.validasi-akses.batalkan-ujian' : 'wali.validasi-akses.validasi-ujian', $siswa->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $siswa->validasi_ujian_wali ? 'btn-outline-warning' : 'btn-success' }} mb-1" style="width: 90px">
                                                    <i class="fas {{ $siswa->validasi_ujian_wali ? 'fa-undo' : 'fa-check' }} me-1"></i> Ujian
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-light border text-muted mb-1" disabled style="width: 90px"><i class="fas fa-lock"></i> Ujian</button>
                                        @endif

                                        {{-- RAPOR --}}
                                        @if($siswa->validasi_rapor_bendahara)
                                            <form action="{{ route($siswa->validasi_rapor_wali ? 'wali.validasi-akses.batalkan-rapor' : 'wali.validasi-akses.validasi-rapor', $siswa->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $siswa->validasi_rapor_wali ? 'btn-outline-warning' : 'btn-info' }} mb-1" style="width: 90px">
                                                    <i class="fas {{ $siswa->validasi_rapor_wali ? 'fa-undo' : 'fa-check' }} me-1"></i> Rapor
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-light border text-muted mb-1" disabled style="width: 90px"><i class="fas fa-lock"></i> Rapor</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-gray-500 fst-italic">Tidak ada data siswa ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INSTRUCTIONS --}}
        <div class="card shadow-sm border-start border-warning border-4 bg-light mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-warning"><i class="fas fa-exclamation-circle me-2"></i>Petunjuk Validasi Wali Kelas</h6>
                <div class="row small text-gray-800">
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>Tombol berwarna hijau/biru hanya aktif jika <strong>Bendahara</strong> sudah verifikasi.</li>
                            <li><strong>Pending Wali:</strong> Bendahara sudah OK, menunggu persetujuan Anda.</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li><strong>Locked:</strong> Siswa memiliki tunggakan/belum diverifikasi Bendahara.</li>
                            <li>Gunakan tombol <strong>Batal</strong> jika terjadi kekeliruan validasi.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>

{{-- MODAL KONFIRMASI VALIDASI SEMUA UJIAN --}}
<div class="modal fade" id="validasiSemuaUjianModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Validasi Massal Ujian
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                <h6 class="fw-bold mb-2">Validasi akses ujian untuk semua siswa?</h6>
                <p class="text-muted small mb-0">
                    Sistem akan memberikan akses ujian kepada <strong>semua siswa</strong> yang sudah diverifikasi oleh Bendahara.
                    Pastikan data sudah benar sebelum melanjutkan.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form action="{{ route('wali.validasi-akses.validasi-semua-ujian') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Ya, Validasi Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI VALIDASI SEMUA RAPOR --}}
<div class="modal fade" id="validasiSemuaRaporModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Validasi Massal Rapor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-file-contract fa-3x text-info mb-3"></i>
                <h6 class="fw-bold mb-2">Validasi akses rapor untuk semua siswa?</h6>
                <p class="text-muted small mb-0">
                    Sistem akan memberikan akses rapor kepada <strong>semua siswa</strong> yang sudah diverifikasi oleh Bendahara.
                    Pastikan data sudah benar sebelum melanjutkan.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form action="{{ route('wali.validasi-akses.validasi-semua-rapor') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-check me-1"></i> Ya, Validasi Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
