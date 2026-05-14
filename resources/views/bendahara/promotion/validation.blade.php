@extends('layouts.sneat')

@section('title', 'Admin - Validasi Dispensasi')
@section('page-title', 'Validasi Dispensasi')
@section('page-subtitle', 'Ajukan izin khusus untuk kandidat naik kelas dengan tunggakan')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --disp-primary: #4361ee;
        --disp-success: #10b981;
        --disp-warning: #f59e0b;
        --disp-danger: #ef4444;
        --disp-info: #06b6d4;
        --disp-purple: #8b5cf6;
        --disp-surface: #ffffff;
        --disp-bg: #f8fafc;
        --disp-border: #e2e8f0;
        --disp-text: #1e293b;
        --disp-muted: #64748b;
        --disp-radius: 12px;
    }

    .disp-shell {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .stat-widget,
    .disp-card {
        background: var(--disp-surface);
        border: 1px solid var(--disp-border);
        border-radius: var(--disp-radius);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .stat-widget {
        padding: 1.35rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.2s ease;
    }

    .stat-widget:hover { transform: translateY(-2px); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--disp-text);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--disp-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--disp-muted);
        margin-top: 0.15rem;
    }

    .disp-card {
        overflow: hidden;
    }

    .disp-card-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--disp-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .disp-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--disp-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .disp-card-subtitle {
        color: var(--disp-muted);
        font-size: 0.82rem;
        margin-top: 0.25rem;
    }

    .disp-toolbar {
        background: var(--disp-bg);
        border-bottom: 1px solid var(--disp-border);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .selected-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #eff6ff;
        color: var(--disp-primary);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .table-clean {
        margin: 0;
    }

    .table-clean th {
        background: var(--disp-bg);
        border-bottom: 1px solid var(--disp-border);
        color: var(--disp-muted);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.25rem;
        white-space: nowrap;
    }

    .table-clean td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--disp-border);
        color: var(--disp-text);
        font-size: 0.9rem;
    }

    .table-clean tbody tr:hover {
        background: #f8fafc;
    }

    .table-clean tbody tr:last-child td {
        border-bottom: none;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .student-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.12);
        color: var(--disp-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
        flex-shrink: 0;
    }

    .student-name {
        font-weight: 700;
        color: var(--disp-text);
        line-height: 1.25;
        word-break: break-word;
    }

    .amount-danger {
        color: var(--disp-danger);
        font-weight: 700;
        white-space: nowrap;
    }

    .action-btns {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-soft {
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .empty-state {
        min-height: 260px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--disp-muted);
        padding: 2rem;
    }

    .empty-state i {
        color: #cbd5e1;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .mobile-select-all,
    .mobile-only-cell {
        display: none !important;
    }

    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .disp-card-header { align-items: stretch; }
        .disp-card-header > div,
        .disp-card-header .btn { width: 100%; }
        .disp-toolbar { align-items: stretch; }
        .disp-toolbar > div { width: 100%; }
        .disp-toolbar .btn { width: 100%; }
        .mobile-select-all { display: flex !important; }
        .desktop-only-cell { display: none !important; }
        .mobile-only-cell { display: inline-block !important; }

        .table-responsive { overflow-x: visible; }
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: flex;
            flex-direction: column;
            border-bottom: 2px solid var(--disp-border);
            background: #fff;
        }
        .table-clean tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            text-align: right;
            white-space: normal;
        }
        .table-clean tbody td::before {
            content: attr(data-label);
            color: var(--disp-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            flex-shrink: 0;
        }
        .table-clean tbody td.mobile-card-head {
            background: var(--disp-bg);
            align-items: flex-start;
            text-align: left;
            padding: 1rem;
        }
        .table-clean tbody td.mobile-card-head::before,
        .table-clean tbody td.mobile-card-actions::before {
            display: none;
        }
        .student-info {
            width: 100%;
            align-items: flex-start;
        }
        .mobile-card-actions {
            justify-content: flex-end !important;
            background: #f8fafc;
        }
        .action-btns { width: 100%; }
        .action-btns .btn { width: 100%; }
    }
</style>
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
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalCandidates }}</div>
                <div class="stat-label">Kandidat</div>
                <div class="stat-desc">Akademik tuntas, keuangan belum lunas</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <div class="stat-value">{{ $readyCandidates }}</div>
                <div class="stat-label">Siap Diajukan</div>
                <div class="stat-desc">Belum memiliki pengajuan aktif</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingCandidates }}</div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-desc">Sudah masuk antrean persetujuan</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fff1f2; color: #ef4444;">
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
                <h5 class="disp-card-title"><i class="fas fa-clipboard-check" style="color: var(--disp-primary);"></i> Kandidat Dispensasi</h5>
                <div class="disp-card-subtitle">Daftar siswa yang akademiknya tuntas namun masih memiliki sisa tagihan.</div>
            </div>
            <a href="{{ route('bendahara.promotion.validation.history') }}" class="btn btn-primary btn-sm btn-soft">
                <i class="bx bx-history"></i> <span>Riwayat</span>
            </a>
        </div>

        <div class="disp-toolbar">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="mobile-select-all align-items-center gap-2">
                    <input type="checkbox" id="selectAllMobile" class="form-check-input" onclick="toggleSelectAllMobile()">
                    <label for="selectAllMobile" class="mb-0 small fw-semibold text-secondary">Pilih semua</label>
                </div>
                <div class="selected-badge">
                    <i class="fas fa-check-circle"></i>
                    <span><span id="selectedCount">0</span> siswa terpilih</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-primary btn-sm btn-soft" id="btnBulkDispensasi" style="display: none;" data-bs-toggle="modal" data-bs-target="#modalBulkDispensasi">
                    <i class="bx bx-send"></i> Ajukan Terpilih
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-clean align-middle">
                <thead>
                    <tr>
                        <th width="44" class="text-center"><input type="checkbox" id="select-all" class="form-check-input" onclick="toggleSelectAll()"></th>
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
                                    <input type="checkbox" class="form-check-input siswa-checkbox" value="{{ $candidate['siswa']->id }}" onchange="syncCheckbox(this)">
                                @endif
                            </td>
                            <td class="mobile-card-head" data-label="Siswa">
                                <div class="student-info">
                                    @if(!$candidate['pending_request'])
                                        <input type="checkbox" class="form-check-input siswa-checkbox mobile-only-cell flex-shrink-0" value="{{ $candidate['siswa']->id }}" onchange="syncCheckbox(this)" style="margin-top: 0.35rem;">
                                    @endif
                                    <div class="student-avatar">{{ strtoupper(substr($candidate['siswa']->nama_lengkap, 0, 1)) }}</div>
                                    <div style="min-width: 0;">
                                        <div class="student-name">{{ $candidate['siswa']->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $candidate['siswa']->nisn ?? 'NISN belum tersedia' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Kelas">
                                <span class="badge bg-label-primary px-2 py-1">{{ $candidate['siswa']->kelas->nama_kelas ?? '-' }}</span>
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
                                                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                                                    <form action="{{ route('bendahara.promotion.validation.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-0" style="background: #4361ee;">
                                                            <h5 class="modal-title text-white"><i class="bx bx-send me-2"></i>Ajukan Izin Khusus</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="siswa_id" value="{{ $candidate['siswa']->id }}">
                                                            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                                                            <div class="alert alert-info border-0 small">
                                                                Pengajuan untuk <strong>{{ $candidate['siswa']->nama_lengkap }}</strong> akan dikirim ke Ketua PKBM.
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Alasan Pengajuan</label>
                                                                <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
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
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="{{ route('bendahara.promotion.validation.bulk-store') }}" method="POST" id="bulkDispensasiForm">
                @csrf
                <div class="modal-header border-0" style="background: #4361ee;">
                    <h5 class="modal-title text-white"><i class="bx bx-send me-2"></i>Ajukan Dispensasi Terpilih</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <textarea name="alasan" class="form-control" rows="4" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
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
<script>
function toggleSelectAll() {
    const isChecked = document.getElementById('select-all').checked;
    document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = isChecked);
    updateSelectedCount();
}

function toggleSelectAllMobile() {
    const isChecked = document.getElementById('selectAllMobile').checked;
    document.getElementById('select-all').checked = isChecked;
    toggleSelectAll();
}

function updateSelectedCount() {
    const checkedBoxes = Array.from(document.querySelectorAll('.siswa-checkbox:checked'));
    const uniqueCheckedValues = new Set(checkedBoxes.map(cb => cb.value));
    const count = uniqueCheckedValues.size;
    const allBoxes = Array.from(document.querySelectorAll('.siswa-checkbox'));
    const uniqueAllValues = new Set(allBoxes.map(cb => cb.value));

    document.getElementById('selectedCount').textContent = count;
    const btn = document.getElementById('btnBulkDispensasi');
    btn.style.display = count > 0 ? 'inline-flex' : 'none';

    document.getElementById('select-all').indeterminate = count > 0 && count < uniqueAllValues.size;
    document.getElementById('select-all').checked = uniqueAllValues.size > 0 && count === uniqueAllValues.size;

    const mobileSelectAll = document.getElementById('selectAllMobile');
    if (mobileSelectAll) {
        mobileSelectAll.indeterminate = count > 0 && count < uniqueAllValues.size;
        mobileSelectAll.checked = uniqueAllValues.size > 0 && count === uniqueAllValues.size;
    }
}

function syncCheckbox(source) {
    const checkboxes = document.querySelectorAll('.siswa-checkbox[value="' + source.value + '"]');
    checkboxes.forEach(cb => {
        if (cb !== source) cb.checked = source.checked;
    });
    updateSelectedCount();
}

document.getElementById('modalBulkDispensasi').addEventListener('show.bs.modal', function () {
    const container = document.getElementById('bulk-siswa-ids');
    container.innerHTML = '';

    const checkedBoxes = Array.from(document.querySelectorAll('.siswa-checkbox:checked'));
    const uniqueCheckedValues = new Set(checkedBoxes.map(cb => cb.value));

    document.getElementById('modalSelectedCount').textContent = uniqueCheckedValues.size;

    uniqueCheckedValues.forEach(val => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'siswa_ids[]';
        input.value = val;
        container.appendChild(input);
    });
});
</script>
@endsection
