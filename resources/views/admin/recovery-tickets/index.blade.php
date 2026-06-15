@extends('layouts.sneat')

@section('title', 'Manajemen Tiket Pemulihan Akun')
@section('page-title', 'Manajemen Tiket Pemulihan')
@section('page-subtitle', 'Kelola antrean permohonan pemulihan akses akun')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/recovery-tickets/index.css'])
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
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 admin-wa-icon">
                    <i class="bx bxl-whatsapp fs-3"></i>
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
                    <div class="input-group admin-wa-input-group">
                        <span class="input-group-text fw-semibold bg-light">+62</span>
                        <input type="text" name="admin_wa_number" class="form-control form-control-lg admin-wa-input" placeholder="8123456789" value="{{ ltrim($adminWa, '620') }}" required>
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
            <div class="bulk-toolbar d-none mb-3 p-2 rounded d-flex align-items-center flex-wrap gap-2" id="bulkToolbar">
                <span class="fw-semibold text-primary small" id="bulkCount">0 dipilih</span>
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-secondary" data-bulk-action="resolve">
                        <i class="bx bx-archive-in me-1"></i> Arsipkan/Tutup Terpilih
                    </button>
                </div>
            </div>

            <div class="d-md-none mobile-select-all-bar">
                <input type="checkbox" id="selectAllMobile" class="form-check-input" data-select-all>
                <label for="selectAllMobile" class="mobile-select-all-label">Pilih Semua</label>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-card-mobile">
                    <thead>
                        <tr>
                            <th class="ticket-checkbox-col">
                                <input type="checkbox" class="form-check-input" id="selectAllCb" data-select-all>
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
                                <input type="checkbox" class="form-check-input ticket-check" data-id="{{ $ticket->id }}">
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
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-info" title="Copy Link Reset" data-copy-link="{{ route('password.reset.ticket', $ticket->token_reset) }}">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Modal Resend -->
                                @if($ticket->user->personal_email)
                                <div class="modal fade" id="resendModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto recovery-resend-dialog" role="document">
                                        <div class="modal-content border-0 shadow-lg recovery-modal-content">
                                            <form action="{{ route('admin.recovery-tickets.resend', $ticket) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bx bx-send text-primary me-2"></i>Kirim Ulang Email
                                                    </h5>
                                                    <button type="button" class="btn-close recovery-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body pt-3 px-4 pb-2">
                                                    <p class="text-wrap text-break mb-3 recovery-modal-text">
                                                        Email pemulihan akan dikirim ulang ke:<br>
                                                        <strong class="recovery-modal-email">{{ $ticket->user->personal_email }}</strong>
                                                    </p>
                                                    <div class="alert alert-info text-wrap d-flex align-items-start gap-2 mb-0 recovery-info-alert">
                                                        <i class="bx bx-info-circle fs-5 flex-shrink-0 recovery-info-icon"></i>
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
                                <i class="bx bx-check-circle text-success mb-3 empty-ticket-icon"></i>
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

<!-- Dynamic Action Modal (single ticket) -->
<div class="modal fade" id="actionTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto recovery-modal-dialog">
        <div class="modal-content border-0 shadow-lg recovery-modal-content">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="actionTicketTitle">Konfirmasi</h5>
                <button type="button" class="btn-close recovery-modal-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3 px-4 pb-2">
                <p class="mb-0 text-muted recovery-modal-text" id="actionTicketBody"></p>
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
    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto recovery-modal-dialog">
        <div class="modal-content border-0 shadow-lg recovery-modal-content">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="bulkModalTitle">Konfirmasi</h5>
                <button type="button" class="btn-close recovery-modal-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3 px-4 pb-2">
                <p class="mb-0 text-muted recovery-modal-text" id="bulkModalBody"></p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-end gap-2 pt-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmBulkBtn">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden bulk form -->
<form id="bulkForm" method="POST" class="hidden-bulk-form" data-resolve-url="{{ route('admin.recovery-tickets.bulk-resolve') }}" data-reject-url="{{ route('admin.recovery-tickets.bulk-reject') }}">
    @csrf
    <div id="bulkIdsContainer"></div>
</form>

@endsection

@section('scripts')
    @vite(['resources/js/admin/recovery-tickets/index.js'])
@endsection
