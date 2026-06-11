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
<div class="dispensasi-page-shell">
<div class="container-fluid px-0">

    {{-- ALUR INFO --}}
    <div class="alert alert-light border border-warning border-opacity-50 shadow-sm mb-4">
        <div class="small text-muted">
            <i class="fas fa-info-circle text-warning me-1"></i>
            <strong>Dispensasi Keuangan</strong> diajukan oleh Bendahara untuk siswa yang belum lunas pembayaran tetapi perlu akses ujian/rapor.
            Jika <strong>disetujui</strong>, siswa otomatis mendapat akses meskipun belum lunas.
            Jika <strong>ditolak</strong>, siswa harus melunasi pembayaran terlebih dahulu.
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-gradient-yellow">
                <div class="stat-content">
                    <div class="stat-title">Menunggu Keputusan</div>
                    <div class="stat-number">{{ $stats['menunggu'] }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Disetujui</div>
                    <div class="stat-number">{{ $stats['disetujui'] }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-gradient-red">
                <div class="stat-content">
                    <div class="stat-title">Ditolak</div>
                    <div class="stat-number">{{ $stats['ditolak'] }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card shadow mb-4">
        <div class="card-body py-2">
            <form action="{{ route('ketua.dispensasi.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm dispensasi-filter-status">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <select name="tipe" class="form-select form-select-sm dispensasi-filter-type">
                    <option value="">Semua Tipe</option>
                    <option value="ujian" {{ request('tipe') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                    <option value="rapor" {{ request('tipe') == 'rapor' ? 'selected' : '' }}>Rapor</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="{{ route('ketua.dispensasi.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </form>
        </div>
    </div>

    {{-- TABEL DISPENSASI --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-hand-holding-heart me-2"></i>Daftar Pengajuan Dispensasi</h6>
            @if($stats['menunggu'] > 0)
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm fw-bold" data-bulk-action data-action="{{ route('ketua.dispensasi.approve') }}" data-label="Setujui" data-button-class="btn-success">
                        <i class="fas fa-check-double me-1"></i> Setujui Terpilih
                    </button>
                    <button type="button" class="btn btn-danger btn-sm fw-bold" data-bulk-action data-action="{{ route('ketua.dispensasi.reject') }}" data-label="Tolak" data-button-class="btn-danger">
                        <i class="fas fa-times me-1"></i> Tolak Terpilih
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 dispensasi-table">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th>NO</th>
                            <th class="text-start">SISWA</th>
                            <th>TIPE</th>
                            <th>PERIODE</th>
                            <th class="text-start">ALASAN</th>
                            <th>DIAJUKAN OLEH</th>
                            <th>TANGGAL</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispensasiList as $index => $d)
                            <tr>
                                <td class="text-center align-middle">
                                    @if($d->status === 'menunggu')
                                        <input type="checkbox" class="disp-checkbox form-check-input" value="{{ $d->id }}">
                                    @endif
                                </td>
                                <td class="text-center align-middle fw-bold">{{ $dispensasiList->firstItem() + $index }}</td>
                                <td class="align-middle text-start">
                                    <div class="fw-bold">{{ $d->siswa->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted">{{ $d->siswa->kelas->nama_kelas ?? '-' }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $d->tipe === 'ujian' ? 'bg-success' : 'bg-info' }}">{{ strtoupper($d->tipe ?? '-') }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="small fw-bold">{{ $d->periode ? strtoupper(str_replace('_', ' ', $d->periode)) : '-' }}</span>
                                </td>
                                <td class="align-middle text-start">
                                    <span class="small">{{ Str::limit($d->alasan, 60) }}</span>
                                </td>
                                <td class="text-center align-middle small">{{ $d->pengaju->name ?? '-' }}</td>
                                <td class="text-center align-middle small">{{ $d->tanggal_pengajuan ? $d->tanggal_pengajuan->format('d/m/Y') : '-' }}</td>
                                <td class="text-center align-middle">
                                    @if($d->status === 'menunggu')
                                        <span class="badge bg-warning text-white"><i class="fas fa-clock"></i> Menunggu</span>
                                    @elseif($d->status === 'disetujui')
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($d->status === 'menunggu')
                                        <div class="btn-group gap-1">
                                            <button type="button" class="btn btn-sm btn-success rounded-circle" title="Setujui"
                                                data-single-action
                                                data-action="{{ route('ketua.dispensasi.approve') }}"
                                                data-id="{{ $d->id }}"
                                                data-message="Setujui dispensasi untuk {{ $d->siswa->nama_lengkap ?? '' }}?"
                                                data-label="Setujui"
                                                data-button-class="btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger rounded-circle" title="Tolak"
                                                data-single-action
                                                data-action="{{ route('ketua.dispensasi.reject') }}"
                                                data-id="{{ $d->id }}"
                                                data-message="Tolak dispensasi untuk {{ $d->siswa->nama_lengkap ?? '' }}?"
                                                data-label="Tolak"
                                                data-button-class="btn-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            @if($d->catatan_ketua)
                                                <i class="fas fa-comment-dots" title="{{ $d->catatan_ketua }}"></i>
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
            <div class="px-4 py-3 bg-light border-top">
                <div class="d-flex justify-content-center">
                    {{ $dispensasiList->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
{{-- MODAL KONFIRMASI AKSI --}}
<div class="modal fade" id="catatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="catatanForm" method="POST">
                @csrf
                <div class="modal-header" id="catatanModalHeader">
                    <h5 class="modal-title fw-bold" id="catatanModalTitle">
                        <i class="fas fa-question-circle me-2" id="catatanModalIcon"></i>Konfirmasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-3" id="catatanModalMessage">Apakah Anda yakin?</p>
                    <div id="catatanBulkIds"></div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Catatan (opsional)</label>
                        <textarea name="catatan_ketua" class="form-control form-control-sm" rows="2" placeholder="Catatan untuk bendahara..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
                    <button type="submit" class="btn fw-bold" id="catatanSubmitBtn"><i class="fas fa-check me-1"></i> Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PERINGATAN --}}
<div class="modal fade" id="peringatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Perhatian
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-mouse-pointer fa-3x text-warning mb-3"></i>
                <p class="fw-bold mb-0">Pilih minimal 1 pengajuan terlebih dahulu.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-warning fw-bold" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/ketua/dispensasi/index.js'])
@endsection
