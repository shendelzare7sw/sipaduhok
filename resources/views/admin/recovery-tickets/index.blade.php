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
    <div class="card mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h6 class="mb-1 text-primary"><i class="bx bxl-whatsapp text-success me-1"></i> Nomor Bantuan Administrator</h6>
                <p class="mb-0 text-muted small">Nomor ini akan ditampilkan di halaman Login bagi user yang butuh bantuan manual.</p>
            </div>
            <form action="{{ route('admin.recovery-tickets.update-admin-wa') }}" method="POST" class="d-flex gap-2 align-items-center">
                @csrf
                <div class="input-group input-group-sm mb-0" style="max-width: 300px;">
                    <span class="input-group-text bg-white">+62</span>
                    <input type="text" name="admin_wa_number" class="form-control" placeholder="812345678" value="{{ ltrim($adminWa, '620') }}" required>
                    <button class="btn btn-primary" type="submit"><i class="bx bx-save"></i> Simpan</button>
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
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('resolve')">
                        <i class="bx bx-check-double me-1"></i> Tutup/Selesaikan Terpilih
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('reject')">
                        <i class="bx bx-x me-1"></i> Tolak Terpilih
                    </button>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAllCb" onchange="toggleSelectAll(this)">
                            </th>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>User Peminta</th>
                            <th>Kendala</th>
                            <th>Email Pribadi</th>
                            <th>Status API</th>
                            <th>Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($tickets as $key => $ticket)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input ticket-check" data-id="{{ $ticket->id }}" onchange="updateBulkToolbar()">
                            </td>
                            <td>{{ $tickets->firstItem() + $key }}</td>
                            <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <strong>{{ $ticket->user->name }}</strong><br>
                                <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role)) }}</span>
                            </td>
                            <td>
                                @if($ticket->tipe_recovery == 'lupa_username')
                                    <span class="badge bg-label-secondary"><i class="bx bx-user me-1"></i> Lupa Username</span>
                                @elseif($ticket->tipe_recovery == 'lupa_password')
                                    <span class="badge bg-label-warning"><i class="bx bx-key me-1"></i> Lupa Password</span>
                                @else
                                    <span class="badge bg-label-danger"><i class="bx bx-error-circle me-1"></i> Lupa Keduanya</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->user->personal_email)
                                    <a href="mailto:{{ $ticket->user->personal_email }}" class="text-primary"><i class="bx bx-envelope"></i> {{ $ticket->user->personal_email }}</a>
                                @else
                                    <span class="text-danger small"><i class="bx bx-x"></i> Belum diisi</span>
                                @endif
                            </td>
                            <td>
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
                            <td>
                                <div class="btn-group">
                                    @if(in_array($ticket->status, ['pending_admin', 'failed']))
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resendModal{{ $ticket->id }}">
                                            <i class="bx bx-refresh"></i> Kirim Ulang
                                        </button>
                                    @endif
                                    @if($ticket->status == 'sent')
                                        {{-- Only show Archive for Auto-Sent tickets (no confirmation needed) --}}
                                        <form action="{{ route('admin.recovery-tickets.resolve', $ticket) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="bx bx-archive-in"></i> Tutup Tiket
                                            </button>
                                        </form>
                                    @else
                                        {{-- Selesai and Tolak buttons for manual intervention tickets --}}
                                        <button type="button" class="btn btn-sm btn-outline-success mx-1" onclick="confirmTicketAction('{{ route('admin.recovery-tickets.resolve', $ticket) }}', 'resolve', '{{ addslashes($ticket->user->name) }}')">
                                            <i class="bx bx-check"></i> Selesai
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmTicketAction('{{ route('admin.recovery-tickets.reject', $ticket) }}', 'reject', '{{ addslashes($ticket->user->name) }}')">
                                            <i class="bx bx-x"></i> Tolak
                                        </button>
                                    @endif
                                    
                                    @if($ticket->token_reset)
                                        <button class="btn btn-sm btn-icon btn-outline-info ms-1" title="Copy Link Reset" 
                                            onclick="copyToClipboard('{{ route('password.reset.ticket', $ticket->token_reset) }}')">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Modal Resend -->
                                @if(in_array($ticket->status, ['pending_admin', 'failed']))
                                <div class="modal fade" id="resendModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Kirim Ulang Email Pemulihan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.recovery-tickets.resend', $ticket) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Email pemulihan akan dikirim ulang ke <strong>{{ $ticket->user->personal_email ?? 'N/A' }}</strong>.</p>
                                                    @if(empty($ticket->user->personal_email))
                                                        <div class="alert alert-warning">
                                                            <i class="bx bx-error-circle"></i> User ini belum memiliki Email Pribadi. Silakan tambahkan melalui halaman edit user terlebih dahulu.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary" {{ empty($ticket->user->personal_email) ? 'disabled' : '' }}><i class="bx bx-send"></i> Kirim Email</button>
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
                            <td colspan="8" class="text-center py-5">
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
    updateBulkToolbar();
}

function updateBulkToolbar() {
    const checked = document.querySelectorAll('.ticket-check:checked');
    const toolbar = document.getElementById('bulkToolbar');
    const countEl = document.getElementById('bulkCount');

    if (checked.length > 0) {
        toolbar.classList.remove('d-none');
        toolbar.classList.add('d-flex');
        countEl.textContent = checked.length + ' tiket dipilih';
    } else {
        toolbar.classList.add('d-none');
        toolbar.classList.remove('d-flex');
        document.getElementById('selectAllCb').checked = false;
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
