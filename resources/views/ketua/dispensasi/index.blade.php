@extends('layouts.sneat')

@section('title', 'Dispensasi Keuangan')
@section('page-title', 'Dispensasi Keuangan')
@section('page-subtitle', 'Kelola pengajuan dispensasi dari Bendahara')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/ketua/dispensasi/index.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ALUR INFO --}}
    <div class="alert alert-warning alert-dismissible d-flex align-items-baseline" role="alert">
        <span class="alert-icon alert-icon-lg text-warning me-2">
            <i class="bx bx-info-circle bx-sm"></i>
        </span>
        <div class="d-flex flex-column ps-1">
            <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Informasi Dispensasi</h6>
            <span><strong>Dispensasi Keuangan</strong> diajukan oleh Bendahara untuk siswa yang belum lunas pembayaran tetapi perlu akses ujian/rapor. Jika <strong>disetujui</strong>, siswa otomatis mendapat akses. Jika <strong>ditolak</strong>, siswa harus melunasi pembayaran.</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    {{-- STATISTIK (Sneat Widgets) --}}
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time-five"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $stats['menunggu'] }}</h4>
                    </div>
                    <p class="mb-1 fw-medium">Menunggu Keputusan</p>
                    <p class="mb-0 text-muted small">Pengajuan yang perlu ditinjau</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-double"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $stats['disetujui'] }}</h4>
                    </div>
                    <p class="mb-1 fw-medium">Disetujui</p>
                    <p class="mb-0 text-muted small">Total pengajuan yang di-ACC</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $stats['ditolak'] }}</h4>
                    </div>
                    <p class="mb-1 fw-medium">Ditolak</p>
                    <p class="mb-0 text-muted small">Total pengajuan ditolak</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & TABEL DISPENSASI --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="card-title m-0 fw-bold"><i class="fas fa-hand-holding-heart me-2 text-primary"></i>Daftar Pengajuan Dispensasi</h5>
            
            <form action="{{ route('ketua.dispensasi.index') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2">
                <select name="status" class="form-select form-select-sm" style="min-width: 140px;">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <select name="tipe" class="form-select form-select-sm" style="min-width: 120px;">
                    <option value="">Semua Tipe</option>
                    <option value="ujian" {{ request('tipe') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                    <option value="rapor" {{ request('tipe') == 'rapor' ? 'selected' : '' }}>Rapor</option>
                </select>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('ketua.dispensasi.index') }}" class="btn btn-label-secondary btn-sm"><i class="fas fa-redo"></i></a>
                </div>
            </form>
        </div>

        @if($stats['menunggu'] > 0)
            <div class="card-body border-bottom py-3 bg-lighter">
                <div class="d-flex gap-2 align-items-center">
                    <span class="text-muted small fw-bold me-2">Aksi Massal:</span>
                    <button type="button" class="btn btn-success btn-sm fw-bold" data-bulk-action data-action="{{ route('ketua.dispensasi.approve') }}" data-label="Setujui" data-button-class="btn-success">
                        <i class="fas fa-check-double me-1"></i> Setujui Terpilih
                    </button>
                    <button type="button" class="btn btn-danger btn-sm fw-bold" data-bulk-action data-action="{{ route('ketua.dispensasi.reject') }}" data-label="Tolak" data-button-class="btn-danger">
                        <i class="fas fa-times me-1"></i> Tolak Terpilih
                    </button>
                </div>
            </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="40"><input type="checkbox" id="select-all" class="form-check-input"></th>
                        <th>NO</th>
                        <th>SISWA</th>
                        <th>TIPE</th>
                        <th>PERIODE</th>
                        <th>ALASAN</th>
                        <th>DIAJUKAN OLEH</th>
                        <th>TANGGAL</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($dispensasiList as $index => $d)
                        <tr>
                            <td class="text-center align-middle">
                                @if($d->status === 'menunggu')
                                    <input type="checkbox" class="disp-checkbox form-check-input" value="{{ $d->id }}">
                                @endif
                            </td>
                            <td class="align-middle fw-medium">{{ $dispensasiList->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-heading">{{ $d->siswa->nama_lengkap ?? '-' }}</span>
                                    <small class="text-muted">{{ $d->siswa->kelas->nama_kelas ?? '-' }}</small>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge {{ $d->tipe === 'ujian' ? 'bg-label-info' : 'bg-label-primary' }}">{{ strtoupper($d->tipe ?? '-') }}</span>
                            </td>
                            <td class="align-middle">
                                <span class="small fw-bold">{{ $d->periode ? strtoupper(str_replace('_', ' ', $d->periode)) : '-' }}</span>
                            </td>
                            <td class="align-middle text-wrap" style="min-width: 200px;">
                                <span class="small">{{ Str::limit($d->alasan, 60) }}</span>
                            </td>
                            <td class="align-middle small">{{ $d->pengaju->name ?? '-' }}</td>
                            <td class="align-middle small">{{ $d->tanggal_pengajuan ? $d->tanggal_pengajuan->format('d/m/Y') : '-' }}</td>
                            <td class="align-middle">
                                @if($d->status === 'menunggu')
                                    <span class="badge bg-warning"><i class="fas fa-clock me-1"></i> Menunggu</span>
                                @elseif($d->status === 'disetujui')
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Disetujui</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($d->status === 'menunggu')
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-icon btn-success rounded-circle" title="Setujui"
                                            data-single-action
                                            data-action="{{ route('ketua.dispensasi.approve') }}"
                                            data-id="{{ $d->id }}"
                                            data-message="Setujui dispensasi untuk {{ $d->siswa->nama_lengkap ?? '' }}?"
                                            data-label="Setujui"
                                            data-button-class="btn-success">
                                            <i class="bx bx-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-danger rounded-circle" title="Tolak"
                                            data-single-action
                                            data-action="{{ route('ketua.dispensasi.reject') }}"
                                            data-id="{{ $d->id }}"
                                            data-message="Tolak dispensasi untuk {{ $d->siswa->nama_lengkap ?? '' }}?"
                                            data-label="Tolak"
                                            data-button-class="btn-danger">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small">
                                        @if($d->catatan_ketua)
                                            <i class="fas fa-comment-dots text-primary cursor-pointer" title="{{ $d->catatan_ketua }}" data-bs-toggle="tooltip"></i>
                                        @endif
                                        {{ $d->tanggal_keputusan ? $d->tanggal_keputusan->format('d/m/Y') : '' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-5 text-muted fst-italic">Belum ada pengajuan dispensasi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dispensasiList->hasPages())
        <div class="card-footer border-top pt-3 pb-0">
            <div class="d-flex justify-content-center">
                {{ $dispensasiList->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
{{-- MODAL KONFIRMASI AKSI --}}
<div class="modal fade" id="catatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="catatanForm" method="POST">
                @csrf
                <div class="modal-header border-bottom pb-3" id="catatanModalHeader">
                    <h5 class="modal-title fw-bold" id="catatanModalTitle">
                        <i class="fas fa-question-circle me-2 text-primary" id="catatanModalIcon"></i> Konfirmasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-3 fs-6" id="catatanModalMessage">Apakah Anda yakin?</p>
                    <div id="catatanBulkIds"></div>
                    <div class="mb-0">
                        <label class="form-label fw-medium">Catatan (opsional)</label>
                        <textarea name="catatan_ketua" class="form-control" rows="3" placeholder="Tambahkan pesan/alasan untuk bendahara..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="catatanSubmitBtn">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PERINGATAN --}}
<div class="modal fade" id="peringatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-4 px-3">
                <i class="bx bx-error-circle text-warning mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mb-2">Perhatian</h5>
                <p class="mb-4">Pilih minimal 1 pengajuan terlebih dahulu.</p>
                <button type="button" class="btn btn-warning w-100" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/ketua/dispensasi/index.js'])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
