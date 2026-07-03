@extends('layouts.sneat')

@section('title', 'Bendahara - Validasi Dispensasi')
@section('page-title', 'Validasi Dispensasi')
@section('page-subtitle', 'Ajukan izin khusus untuk kandidat naik kelas dengan tunggakan')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/promotion/validation.css'])
@endsection

@section('content')
@php
    $candidateCollection = is_object($candidates) && method_exists($candidates, 'items') ? collect($candidates->items()) : collect($candidates);
    $totalCandidates = $candidateCollection->count();
    $readyCandidates = $candidateCollection->filter(function ($candidate) {
        return empty($candidate['pending_request']);
    })->count();
    $pendingCandidates = $totalCandidates - $readyCandidates;
    $totalUnpaid = $candidateCollection->sum(function ($candidate) {
        return $candidate['financial']['unpaid_amount'] ?? 0;
    });
@endphp

<div class="disp-shell">
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-candidates">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalCandidates }}</div>
                <div class="stat-label">Kandidat</div>
                <div class="stat-desc">Akademik tuntas, keuangan belum lunas</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-ready">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <div class="stat-value">{{ $readyCandidates }}</div>
                <div class="stat-label">Siap Diajukan</div>
                <div class="stat-desc">Belum memiliki pengajuan aktif</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-pending">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingCandidates }}</div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-desc">Sudah masuk antrean persetujuan</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-debt">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <div class="stat-label">Total Tunggakan</div>
                <div class="stat-desc">Dari kandidat yang tampil</div>
            </div>
        </div>
    </div>

    <div class="disp-card">
        <div class="disp-card-header">
            <div>
                <h5 class="disp-card-title"><i class="fas fa-clipboard-check disp-title-icon"></i> Kandidat Dispensasi</h5>
                <div class="disp-card-subtitle">Daftar siswa yang akademiknya tuntas namun masih memiliki sisa tagihan.</div>
            </div>
            <a href="{{ route('bendahara.promotion.validation.history') }}" class="btn btn-primary btn-sm btn-soft">
                <i class="bx bx-history"></i> <span>Riwayat</span>
            </a>
        </div>

        <div class="disp-toolbar">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="mobile-select-all align-items-center gap-2">
                    <input type="checkbox" id="selectAllMobile" class="form-check-input">
                    <label for="selectAllMobile" class="mb-0 small fw-semibold text-secondary">Pilih semua</label>
                </div>
                <div class="selected-badge">
                    <i class="fas fa-check-circle"></i>
                    <span><span id="selectedCount">0</span> siswa terpilih</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-primary btn-sm btn-soft bulk-action-button is-hidden" id="btnBulkDispensasi" data-bs-toggle="modal" data-bs-target="#modalBulkDispensasi">
                    <i class="bx bx-send"></i> Ajukan Terpilih
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-clean align-middle">
                <thead>
                    <tr>
                        <th width="44" class="text-center"><input type="checkbox" id="select-all" class="form-check-input"></th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Status Akademik</th>
                        <th>Tunggakan</th>
                        <th>Status Pengajuan</th>
                        <th class="text-end" width="170">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                        <tr>
                            <td class="text-center desktop-only-cell">
                                @if(!$candidate['pending_request'])
                                    <input type="checkbox" class="form-check-input siswa-checkbox" value="{{ $candidate['siswa']->id }}">
                                @endif
                            </td>
                            <td class="mobile-card-head" data-label="Siswa">
                                <div class="student-info">
                                    @if(!$candidate['pending_request'])
                                        <input type="checkbox" class="form-check-input siswa-checkbox mobile-only-cell flex-shrink-0 mobile-checkbox-offset" value="{{ $candidate['siswa']->id }}">
                                    @endif
                                    <div class="student-avatar">{{ strtoupper(substr($candidate['siswa']->nama_lengkap, 0, 1)) }}</div>
                                    <div class="student-name-wrap">
                                        <div class="student-name">{{ $candidate['siswa']->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $candidate['siswa']->nisn ?? 'NISN belum tersedia' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Kelas">
                                <span class="badge bg-primary text-white px-2 py-1">{{ $candidate['siswa']->kelas->nama_kelas ?? '-' }}</span>
                            </td>
                            <td data-label="Akademik">
                                <span class="badge bg-success px-2 py-1">
                                    <i class="fas fa-check-circle me-1"></i>{{ $candidate['academic']['percentage'] }}%
                                </span>
                            </td>
                            <td data-label="Tunggakan">
                                <span class="amount-danger">Rp {{ number_format($candidate['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                            </td>
                            <td data-label="Pengajuan">
                                @if($candidate['pending_request'])
                                    <span class="badge bg-warning text-dark px-2 py-1">{{ $candidate['pending_request']->status }}</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">Belum Diajukan</span>
                                @endif
                            </td>
                            <td class="mobile-card-actions" data-label="Aksi">
                                <div class="action-btns">
                                    @if(!$candidate['pending_request'])
                                        <button type="button" class="btn btn-sm btn-primary btn-soft" data-bs-toggle="modal" data-bs-target="#modalDispensasi{{ $candidate['siswa']->id }}">
                                            <i class="bx bx-send"></i> Ajukan
                                        </button>

                                        <div class="modal fade" id="modalDispensasi{{ $candidate['siswa']->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content border-0 shadow disp-modal-content">
                                                    <form action="{{ route('bendahara.promotion.validation.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-0 disp-modal-header">
                                                            <h5 class="modal-title text-white"><i class="bx bx-send me-2"></i>Ajukan Izin Khusus</h5>
                                                            <button type="button" class="btn-close btn-close-white disp-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="siswa_id" value="{{ $candidate['siswa']->id }}">
                                                            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                                                            <div class="alert alert-info border-0 small">
                                                                Pengajuan untuk <strong>{{ $candidate['siswa']->nama_lengkap }}</strong> akan dikirim ke Ketua PKBM.
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Alasan Pengajuan</label>
                                                                <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Wali siswa berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary"><i class="bx bx-send me-1"></i>Kirim ke Ketua PKBM</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Menunggu persetujuan</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-check-circle"></i>
                                    <h6 class="mb-1">Tidak ada kandidat dispensasi</h6>
                                    <p class="small mb-0">Belum ada siswa yang memerlukan dispensasi saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulkDispensasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow disp-modal-content">
            <form action="{{ route('bendahara.promotion.validation.bulk-store') }}" method="POST" id="bulkDispensasiForm">
                @csrf
                <div class="modal-header border-0 disp-modal-header">
                    <h5 class="modal-title text-white"><i class="bx bx-send me-2"></i>Ajukan Dispensasi Terpilih</h5>
                    <button type="button" class="btn-close btn-close-white disp-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                    <div id="bulk-siswa-ids"></div>

                    <div class="alert alert-info border-0 d-flex align-items-start gap-2">
                        <i class="bx bx-info-circle fs-5 mt-1"></i>
                        <div>Pengajuan akan dikirim untuk <strong><span id="modalSelectedCount">0</span> siswa</strong> sekaligus.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pengajuan <span class="text-muted fw-normal">(berlaku untuk semua)</span></label>
                        <textarea name="alasan" class="form-control" rows="4" required placeholder="Contoh: Wali siswa berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-send me-1"></i>Kirim ke Ketua PKBM</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/bendahara/promotion/validation.js'])
@endsection
