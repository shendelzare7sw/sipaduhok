@extends('layouts.sneat')

@section('title', 'Validasi Dispensasi Naik Kelas')
@section('page-title', 'Validasi Dispensasi')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* Responsive Styles - Mobile Only */
@media (max-width: 768px) {
    /* Mobile Card Pattern for Tables */
    .table-responsive.text-nowrap {
        white-space: normal !important;
        overflow-x: visible !important;
    }
    .table-card-mobile { white-space: normal !important; }
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block; border: 1px solid #e5e7eb; border-radius: 12px;
        margin-bottom: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff; position: relative;
    }
    .table-card-mobile tbody td {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border: none !important;
        border-bottom: 1px solid #f3f4f6 !important; text-align: right;
        white-space: normal !important; word-break: break-word;
    }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label); font-weight: 700; font-size: 10px;
        text-transform: uppercase; color: #9ca3af; letter-spacing: 0.5px;
        text-align: left; flex-shrink: 0; margin-right: 12px;
    }
    .table-card-mobile .mobile-card-head {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        font-weight: 700; font-size: 15px; padding: 14px !important;
        border-bottom: 2px solid #e0e7ff !important; display: block !important;
        text-align: left;
    }
    .table-card-mobile .mobile-card-actions {
        display: flex !important; justify-content: flex-end;
        padding: 10px 14px !important; background: #f9fafb;
    }
    .desktop-only-cell { display: none !important; }

    /* Mobile select all */
    .mobile-select-all { display: flex !important; }

    .mobile-text-end { text-align: right; }
    .mobile-text-start { text-align: left; }

    /* Force displays on mobile */
    .force-d-block-mobile { display: block !important; }
    .force-d-flex-mobile { display: flex !important; }
}
@media (min-width: 769px) {
    .mobile-only-cell { display: none !important; }
    .mobile-select-all { display: none !important; }
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header title removed as it duplicates layout's header title -->

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0 fs-6">Kandidat Dispensasi <span class="d-none d-md-inline">(Akademik Tuntas, Keuangan Belum Lunas)</span></h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm" id="btnBulkDispensasi" style="display: none !important;" data-bs-toggle="modal" data-bs-target="#modalBulkDispensasi">
                    <i class='bx bx-send'></i> <span class="d-none d-sm-inline">Ajukan</span> Terpilih (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('bendahara.promotion.validation.history') }}" class="btn btn-primary btn-sm">
                    <i class='bx bx-history'></i> <span class="d-none d-sm-inline">Riwayat</span>
                </a>
            </div>
        </div>

        {{-- Mobile Select All --}}
        <div class="mobile-select-all mb-2 align-items-center gap-2 px-3 pt-3">
            <input type="checkbox" id="selectAllMobile" class="form-check-input" onclick="toggleSelectAllMobile()">
            <label for="selectAllMobile" class="form-label mb-0 small fw-bold">Pilih Semua</label>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-card-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="select-all" class="form-check-input" onclick="toggleSelectAll()"></th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Status Akademik</th>
                        <th>Tunggakan</th>
                        <th>Status Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                    <tr>
                        <td class="ps-3 desktop-only-cell">
                            @if(!$candidate['pending_request'])
                                <input type="checkbox" class="form-check-input siswa-checkbox" value="{{ $candidate['siswa']->id }}" onchange="syncCheckbox(this)">
                            @endif
                        </td>
                        <td class="mobile-card-head">
                            <div class="d-flex justify-content-between align-items-start gap-2" style="width: 100%;">
                                <div class="d-flex align-items-center gap-2" style="flex: 1; min-width: 0;">
                                    @if(!$candidate['pending_request'])
                                        <input type="checkbox" class="form-check-input siswa-checkbox mobile-only-cell flex-shrink-0" value="{{ $candidate['siswa']->id }}" onchange="syncCheckbox(this)" style="margin-top: 2px;">
                                    @endif
                                    <span class="text-wrap text-break lh-sm fw-semibold" style="flex: 1;">{{ $candidate['siswa']->nama_lengkap }}</span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Kelas" class="force-d-flex-mobile">
                            <div class="mobile-text-end">{{ $candidate['siswa']->kelas->nama_kelas ?? '-' }}</div>
                        </td>
                        <td data-label="Status Akademik" class="force-d-flex-mobile">
                            <div class="mobile-text-end">
                                <span class="badge bg-success">Tuntas ({{ $candidate['academic']['percentage'] }}%)</span>
                            </div>
                        </td>
                        <td data-label="Tunggakan" class="force-d-flex-mobile">
                            <div class="mobile-text-end">
                                <span class="text-danger fw-semibold">Rp {{ number_format($candidate['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td data-label="Status Pengajuan" class="force-d-flex-mobile">
                            <div class="mobile-text-end">
                                @if($candidate['pending_request'])
                                    <span class="badge bg-warning">{{ $candidate['pending_request']->status }}</span>
                                @else
                                    <span class="badge bg-secondary">Belum Diajukan</span>
                                @endif
                            </div>
                        </td>
                        <td class="mobile-card-actions">
                            @if(!$candidate['pending_request'])
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDispensasi{{ $candidate['siswa']->id }}">
                                    <span class="d-none d-sm-inline">Ajukan</span> Dispensasi
                                </button>

                                <!-- Modal Individual -->
                                <div class="modal fade" id="modalDispensasi{{ $candidate['siswa']->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <form action="{{ route('bendahara.promotion.validation.store') }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ajukan Izin Khusus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="siswa_id" value="{{ $candidate['siswa']->id }}">
                                                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                                                    <p class="text-wrap">Mengajukan izin naik kelas untuk siswa <strong>{{ $candidate['siswa']->nama_lengkap }}</strong> meskipun masih memiliki tunggakan.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Pengajuan</label>
                                                        <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Kirim ke Ketua PKBM</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <small class="text-muted">Menunggu persetujuan</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada siswa yang memerlukan dispensasi saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bulk Dispensasi -->
<div class="modal fade" id="modalBulkDispensasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('bendahara.promotion.validation.bulk-store') }}" method="POST" id="bulkDispensasiForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-send me-2"></i>Ajukan Dispensasi Terpilih</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                    <div id="bulk-siswa-ids"></div>

                    <div class="alert alert-info d-flex align-items-center gap-2">
                        <i class="bx bx-info-circle fs-5"></i>
                        <div>Anda akan mengajukan dispensasi naik kelas untuk <strong><span id="modalSelectedCount">0</span> siswa</strong> sekaligus.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pengajuan <span class="text-muted fw-normal">(berlaku untuk semua)</span></label>
                        <textarea name="alasan" class="form-control" rows="4" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
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
    // Only count unique checked values
    const checkedBoxes = Array.from(document.querySelectorAll('.siswa-checkbox:checked'));
    const uniqueCheckedValues = new Set(checkedBoxes.map(cb => cb.value));
    const count = uniqueCheckedValues.size;
    
    // Get unique total available checkboxes
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
    // Sync all checkboxes with the same value (mobile vs desktop)
    const checkboxes = document.querySelectorAll('.siswa-checkbox[value="' + source.value + '"]');
    checkboxes.forEach(cb => {
        if(cb !== source) cb.checked = source.checked;
    });
    updateSelectedCount();
}

document.getElementById('modalBulkDispensasi').addEventListener('show.bs.modal', function () {
    const container = document.getElementById('bulk-siswa-ids');
    container.innerHTML = '';
    
    // Use Set for unique IDs so we don't submit duplicates
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
