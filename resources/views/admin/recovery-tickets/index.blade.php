@extends('layouts.sneat')

@section('title', 'Manajemen Tiket Pemulihan Akun')
@section('page-title', 'Manajemen Tiket Pemulihan')
@section('page-subtitle', 'Kelola antrean permohonan pemulihan akses akun')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Tab Navigation --}}
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.recovery-tickets.index') }}">
                <i class="bx bx-list-ul me-1"></i> Antrean
                @if($tickets->total() > 0)
                    <span class="badge bg-danger ms-1">{{ $tickets->total() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.recovery-tickets.history') }}">
                <i class="bx bx-history me-1"></i> Riwayat
            </a>
        </li>
    </ul>

    <!-- Admin WA Configuration -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #dcfce7;">
                    <i class="bx bxl-whatsapp fs-3" style="color: #16a34a;"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold">Nomor WhatsApp Bantuan</h5>
                    <p class="mb-0 text-muted">Nomor ini ditampilkan di halaman Login sebagai kontak bantuan bagi user yang tidak bisa recovery mandiri (misal: email & telepon belum terdaftar).</p>
                </div>
            </div>
            <form action="{{ route('admin.recovery-tickets.update-admin-wa') }}" method="POST">
                @csrf
                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                    <label class="fw-semibold text-nowrap mb-0">Nomor Admin:</label>
                    <div class="input-group" style="max-width: 360px;">
                        <span class="input-group-text fw-semibold bg-light">+62</span>
                        <input type="text" name="admin_wa_number" class="form-control form-control-lg" placeholder="8123456789" value="{{ ltrim($adminWa, '620') }}" required
                            style="font-size: 1.1rem; letter-spacing: 0.5px;">
                    </div>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bx bx-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Daftar Antrean Permintaan</h5>
            <small class="text-muted">Tiket Lupa Username/Password User Biasa</small>
        </div>
        
        <div class="card-body">

            {{-- Bulk Toolbar --}}
            <div class="bulk-toolbar d-none mb-3 p-2 rounded d-flex align-items-center flex-wrap gap-2" id="bulkToolbar" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                <span class="fw-semibold text-primary small" id="bulkCount">0 dipilih</span>
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="bulkAction('resolve')">
                        <i class="bx bx-archive-in me-1"></i> Arsipkan/Tutup Terpilih
                    </button>
                </div>
            </div>

            <div class="d-md-none mobile-select-all-bar">
                <input type="checkbox" id="selectAllMobile" class="form-check-input" onchange="toggleSelectAll(this)">
                <label for="selectAllMobile" style="margin: 0; cursor: pointer;">Pilih Semua</label>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-card-mobile">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAllCb" onchange="toggleSelectAll(this)">
                            </th>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>User Peminta</th>
                            <th>Kendala</th>
                            <th>Email</th>
                            <th>Status API</th>
                            <th>Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($tickets as $key => $ticket)
                        <tr>
                            <td class="mobile-card-checkbox">
                                <input type="checkbox" class="form-check-input ticket-check" data-id="{{ $ticket->id }}" onchange="updateBulkToolbar()">
                            </td>
                            <td class="mobile-hide">{{ $tickets->firstItem() + $key }}</td>
                            <td class="desktop-only-cell">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            <td class="desktop-only-cell">
                                <strong>{{ $ticket->user->name }}</strong><br>
                                <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role)) }}</span>
                            </td>
                            <td class="mobile-only-cell mobile-card-head">
                                <strong>{{ $ticket->user->name }}</strong>
                                <span class="badge bg-label-info ms-1">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role)) }}</span>
                                <br><small class="text-muted"><i class="bx bx-time-five"></i> {{ $ticket->created_at->format('d M Y H:i') }}</small>
                            </td>
                            <td data-label="Kendala">
                                @if($ticket->tipe_recovery == 'lupa_username')
                                    <span class="badge bg-label-secondary"><i class="bx bx-user me-1"></i> Lupa Username</span>
                                @elseif($ticket->tipe_recovery == 'lupa_password')
                                    <span class="badge bg-label-warning"><i class="bx bx-key me-1"></i> Lupa Password</span>
                                @else
                                    <span class="badge bg-label-danger"><i class="bx bx-error-circle me-1"></i> Lupa Keduanya</span>
                                @endif
                            </td>
                            <td data-label="Email">
                                @if($ticket->user->personal_email)
                                    <a href="mailto:{{ $ticket->user->personal_email }}" class="text-primary"><i class="bx bx-envelope"></i> {{ $ticket->user->personal_email }}</a>
                                @else
                                    <span class="text-danger small"><i class="bx bx-x"></i> Belum diisi</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($ticket->status == 'sent')
                                    <span class="badge bg-success">Terkirim Otomatis</span>
                                @elseif($ticket->status == 'processing')
                                    <span class="badge bg-primary">Sedang Proses</span>
                                @elseif($ticket->status == 'pending_admin')
                                    <span class="badge bg-danger pulse-warning">Butuh Bantuan Anda</span>
                                @elseif($ticket->status == 'failed')
                                    <span class="badge bg-danger">Gagal Email</span>
                                @endif
                            </td>
                            <td class="mobile-card-actions">
                                <div class="d-flex flex-wrap gap-2">
                                    @if($ticket->user->personal_email)
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resendModal{{ $ticket->id }}">
                                            <i class="bx bx-refresh"></i> Kirim Ulang
                                        </button>
                                    @endif
                                    
                                    <form action="{{ route('admin.recovery-tickets.resolve', $ticket) }}" method="POST" class="d-inline m-0 p-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <i class="bx bx-archive-in"></i> Tutup Tiket
                                        </button>
                                    </form>
                                    
                                    @if($ticket->token_reset)
                                        <button class="btn btn-sm btn-icon btn-outline-info" title="Copy Link Reset" 
                                            onclick="copyToClipboard('{{ route('password.reset.ticket', $ticket->token_reset) }}')">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Modal Resend -->
                                @if($ticket->user->personal_email)
                                <div class="modal fade" id="resendModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto" role="document" style="max-width: 440px; padding: 0 15px;">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                            <form action="{{ route('admin.recovery-tickets.resend', $ticket) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bx bx-send text-primary me-2"></i>Kirim Ulang Email
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-top: -10px; margin-right: -5px;"></button>
                                                </div>
                                                <div class="modal-body pt-3 px-4 pb-2">
                                                    <p class="text-wrap text-break mb-3" style="font-size: 0.95rem; line-height: 1.5; color: #6b7280;">
                                                        Email pemulihan akan dikirim ulang ke:<br>
                                                        <strong style="color: #111827;">{{ $ticket->user->personal_email }}</strong>
                                                    </p>
                                                    <div class="alert alert-info text-wrap d-flex align-items-start gap-2 mb-0" style="word-break: break-word; border-radius: 8px; font-size: 0.875rem;">
                                                        <i class="bx bx-info-circle fs-5 flex-shrink-0" style="margin-top: 1px;"></i>
                                                        <span>Link reset password baru akan dikirim. Link lama yang belum kedaluwarsa tetap valid.</span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-end gap-2 pt-2">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary"><i class="bx bx-send me-1"></i> Kirim Email</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bx bx-check-circle text-success mb-3" style="font-size: 5rem;"></i>
                                <h6 class="text-muted">Semua tiket sudah ditangani. Tidak ada antrean baru.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.pulse-warning {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(255, 62, 29, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 62, 29, 0); }
}

/* Mobile Card Pattern */
@media (max-width: 767.98px) {
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
        position: relative;
    }
    .table-card-mobile tbody tr:hover td { background: transparent; }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f3f4f6 !important;
        white-space: normal;
        text-align: right;
    }
    .table-card-mobile tbody td:last-child { border-bottom: none !important; }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        margin-right: 12px;
        text-align: left;
    }
    .table-card-mobile .mobile-card-head {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        padding: 14px 40px 14px 14px !important;
        border-bottom: 2px solid #e0e7ff !important;
        display: block !important;
        text-align: left;
    }
    .table-card-mobile .mobile-card-head::before { display: none !important; }
    .table-card-mobile .mobile-card-actions {
        justify-content: center !important;
        padding: 12px 14px !important;
        background: #f9fafb;
        flex-wrap: wrap;
    }
    .table-card-mobile .mobile-card-actions::before { display: none !important; }
    .table-card-mobile .mobile-card-actions .d-flex { justify-content: center; }
    .table-card-mobile .mobile-hide { display: none !important; }
    .table-card-mobile .mobile-card-checkbox {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 0 !important;
        border: none !important;
        border-bottom: none !important;
        background: transparent !important;
        z-index: 2;
        display: block !important;
    }
    .table-card-mobile .mobile-card-checkbox::before { display: none !important; }
    .mobile-select-all-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
    }
    .mobile-select-all-bar .form-check-input { margin: 0; }
    .desktop-only-cell { display: none !important; }
}
@media (min-width: 768px) {
    .mobile-only-cell { display: none !important; }
}
</style>

<!-- Dynamic Action Modal (single ticket) -->
<div class="modal fade" id="actionTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto" style="max-width: 400px; padding: 0 15px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="actionTicketTitle">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="margin-top: -10px; margin-right: -5px;"></button>
            </div>
            <div class="modal-body pt-3 px-4 pb-2">
                <p class="mb-0 text-muted" id="actionTicketBody" style="font-size: 0.95rem; line-height: 1.5;"></p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-end gap-2 pt-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <form id="actionTicketForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="confirmActionBtn">
                        Ya, Lanjutkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto" style="max-width: 400px; padding: 0 15px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="bulkModalTitle">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="margin-top: -10px; margin-right: -5px;"></button>
            </div>
            <div class="modal-body pt-3 px-4 pb-2">
                <p class="mb-0 text-muted" id="bulkModalBody" style="font-size: 0.95rem; line-height: 1.5;"></p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-end gap-2 pt-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmBulkBtn" onclick="submitBulkAction()">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden bulk form -->
<form id="bulkForm" method="POST" style="display:none;">
    @csrf
    <div id="bulkIdsContainer"></div>
</form>

@push('scripts')
<script>
// === Single ticket confirmation ===
function confirmTicketAction(url, type, username) {
    const modal = new bootstrap.Modal(document.getElementById('actionTicketModal'));
    const form = document.getElementById('actionTicketForm');
    const title = document.getElementById('actionTicketTitle');
    const body = document.getElementById('actionTicketBody');
    const btn = document.getElementById('confirmActionBtn');

    form.action = url;

    if (type === 'resolve') {
        title.innerHTML = '<i class="bx bx-check-circle text-success me-2"></i>Selesaikan Tiket';
        body.innerHTML = 'Yakinkan tiket atas nama <strong>' + username + '</strong> sudah tertangani dan akses user pulih?';
        btn.className = 'btn btn-success';
        btn.innerHTML = '<i class="bx bx-check me-1"></i> Ya, Selesai';
    } else {
        title.innerHTML = '<i class="bx bx-x-circle text-danger me-2"></i>Tolak Tiket';
        body.innerHTML = 'Tolak dan batalkan permohonan pemulihan untuk <strong>' + username + '</strong>?';
        btn.className = 'btn btn-danger';
        btn.innerHTML = '<i class="bx bx-x me-1"></i> Ya, Tolak';
    }

    modal.show();
}

// === Bulk select logic ===
function toggleSelectAll(cb) {
    document.querySelectorAll('.ticket-check').forEach(el => { el.checked = cb.checked; });
    // Sync both select-all checkboxes
    const selectAllCb = document.getElementById('selectAllCb');
    const selectAllMobile = document.getElementById('selectAllMobile');
    if (selectAllCb) selectAllCb.checked = cb.checked;
    if (selectAllMobile) selectAllMobile.checked = cb.checked;
    updateBulkToolbar();
}

function updateBulkToolbar() {
    const checked = document.querySelectorAll('.ticket-check:checked');
    const toolbar = document.getElementById('bulkToolbar');
    const countEl = document.getElementById('bulkCount');

    const allChecks = document.querySelectorAll('.ticket-check');
    const allChecked = checked.length === allChecks.length && allChecks.length > 0;
    const noneChecked = checked.length === 0;
    const selectAllCb = document.getElementById('selectAllCb');
    const selectAllMobile = document.getElementById('selectAllMobile');
    // Only uncheck select-all when no items selected; don't auto-check it
    if (noneChecked) {
        if (selectAllCb) selectAllCb.checked = false;
        if (selectAllMobile) selectAllMobile.checked = false;
    }

    if (checked.length > 0) {
        toolbar.classList.remove('d-none');
        toolbar.classList.add('d-flex');
        countEl.textContent = checked.length + ' tiket dipilih';
    } else {
        toolbar.classList.add('d-none');
        toolbar.classList.remove('d-flex');
    }
}

let pendingBulkType = '';

function bulkAction(type) {
    const checked = document.querySelectorAll('.ticket-check:checked');
    if (checked.length === 0) return;

    pendingBulkType = type;
    const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
    const title = document.getElementById('bulkModalTitle');
    const body = document.getElementById('bulkModalBody');
    const btn = document.getElementById('confirmBulkBtn');

    if (type === 'resolve') {
        title.innerHTML = '<i class="bx bx-check-circle text-success me-2"></i>Setujui ' + checked.length + ' Tiket';
        body.textContent = 'Yakin ingin menyelesaikan ' + checked.length + ' tiket pemulihan yang dipilih?';
        btn.className = 'btn btn-success';
        btn.innerHTML = '<i class="bx bx-check me-1"></i> Ya, Setujui Semua';
    } else {
        title.innerHTML = '<i class="bx bx-x-circle text-danger me-2"></i>Tolak ' + checked.length + ' Tiket';
        body.textContent = 'Yakin ingin menolak ' + checked.length + ' tiket pemulihan yang dipilih?';
        btn.className = 'btn btn-danger';
        btn.innerHTML = '<i class="bx bx-x me-1"></i> Ya, Tolak Semua';
    }

    modal.show();
}

function submitBulkAction() {
    const checked = document.querySelectorAll('.ticket-check:checked');
    const form = document.getElementById('bulkForm');
    const container = document.getElementById('bulkIdsContainer');

    const url = pendingBulkType === 'resolve'
        ? '{{ route("admin.recovery-tickets.bulk-resolve") }}'
        : '{{ route("admin.recovery-tickets.bulk-reject") }}';

    form.action = url;
    container.innerHTML = '';
    checked.forEach(el => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = el.dataset.id;
        container.appendChild(input);
    });
    form.submit();
}

// === Toast & Copy ===
function showToast(message, type = 'success') {
    let toast = document.createElement('div');
    toast.className = 'alert alert-' + type + ' shadow-sm m-0 d-flex align-items-center';
    toast.style.cssText = 'position:fixed;top:25px;right:25px;z-index:9999;transition:opacity 0.4s;min-width:250px;max-width:90vw;';
    let icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
    toast.innerHTML = '<i class="bx ' + icon + ' fs-4 me-2"></i> <div>' + message + '</div>';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 2500);
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Link reset berhasil disalin!');
        }).catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    var ta = document.createElement("textarea");
    ta.value = text;
    ta.style.cssText = 'top:0;left:0;position:fixed;';
    document.body.appendChild(ta);
    ta.focus(); ta.select();
    try {
        document.execCommand('copy') ? showToast('Link reset berhasil disalin!') : showToast('Gagal menyalin link.', 'danger');
    } catch(e) { showToast('Gagal menyalin link.', 'danger'); }
    document.body.removeChild(ta);
}
</script>
@endpush
@endsection
