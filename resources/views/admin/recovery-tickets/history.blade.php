@extends('layouts.sneat')

@section('title', 'Riwayat Tiket Pemulihan')
@section('page-title', 'Manajemen Tiket Pemulihan')
@section('page-subtitle', 'Riwayat tiket pemulihan akses akun')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection
@section('styles')
    @vite(['resources/css/admin/recovery-tickets/history.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Tab Navigation --}}
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.recovery-tickets.index') }}">
                <i class="bx bx-list-ul me-1"></i> Antrean
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.recovery-tickets.history') }}">
                <i class="bx bx-history me-1"></i> Riwayat
                @if($tickets->total() > 0)
                    <span class="badge bg-secondary ms-1">{{ $tickets->total() }}</span>
                @endif
            </a>
        </li>
    </ul>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Riwayat Tiket Pemulihan</h5>
            <small class="text-muted">Tiket yang sudah diselesaikan atau ditolak</small>
        </div>
        
        <div class="card-body">
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.recovery-tickets.history.bulk-delete') }}">
                @csrf
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmBulkDelete()" id="btnBulkDelete" disabled>
                        <i class="bx bx-trash me-1"></i> Hapus Terpilih
                    </button>
                    
                    <div class="form-check d-md-none">
                        <input class="form-check-input" type="checkbox" id="checkAllMobile">
                        <label class="form-check-label" for="checkAllMobile">
                            Pilih Semua
                        </label>
                    </div>
                </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-card-mobile">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input class="form-check-input" type="checkbox" id="checkAll"></th>
                            <th>No</th>
                            <th>Tanggal Permintaan</th>
                            <th>User</th>
                            <th>Kendala</th>
                            <th>Pemulihan Via</th>
                            <th>Status</th>
                            <th>Waktu Tutup</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($tickets as $key => $ticket)
                        <tr>
                            <td class="mobile-hide text-center"><input class="form-check-input ticket-checkbox" type="checkbox" name="ids[]" value="{{ $ticket->id }}"></td>
                            <td class="mobile-hide">{{ $tickets->firstItem() + $key }}</td>
                            <td class="desktop-only-cell">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            <td class="desktop-only-cell">
                                <strong>{{ $ticket->user->name ?? '-' }}</strong><br>
                                <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role ?? '-')) }}</span>
                            </td>
                            <td class="mobile-only-cell mobile-card-head">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div>
                                        <strong>{{ $ticket->user->name ?? '-' }}</strong>
                                        <span class="badge bg-label-info ms-1">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role ?? '-')) }}</span>
                                        <br><small class="text-muted"><i class="bx bx-time-five"></i> {{ $ticket->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input ticket-checkbox" type="checkbox" name="ids[]" value="{{ $ticket->id }}" style="transform: scale(1.2);">
                                    </div>
                                </div>
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
                            <td data-label="Pemulihan Via">
                                @if($ticket->user->personal_email)
                                    <span class="text-muted"><i class="bx bx-envelope text-primary"></i> {{ $ticket->user->personal_email }}</span>
                                @elseif($ticket->target_phone)
                                    <span class="text-muted"><i class="bx bxl-whatsapp text-success"></i> {{ $ticket->target_phone }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($ticket->status == 'resolved')
                                    <span class="badge bg-success"><i class="bx bx-check-double me-1"></i> Selesai/Ditutup</span>
                                @elseif($ticket->status == 'rejected')
                                    <span class="badge bg-secondary"><i class="bx bx-x me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td data-label="Waktu Tutup">
                                <span class="text-muted small">{{ $ticket->updated_at->format('d M Y H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bx bx-history text-muted mb-3 empty-history-icon"></i>
                                <h6 class="text-muted">Belum ada riwayat tiket pemulihan.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkAllMobile = document.getElementById('checkAllMobile');
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.ticket-checkbox:checked').length;
            if(btnBulkDelete) {
                btnBulkDelete.disabled = checkedCount === 0;
            }
            const allChecked = checkedCount === checkboxes.length && checkboxes.length > 0;
            if (checkAll) {
                checkAll.checked = allChecked;
            }
            if (checkAllMobile) {
                checkAllMobile.checked = allChecked;
            }
        }

        function toggleAll(checked) {
            checkboxes.forEach(cb => cb.checked = checked);
            updateButtonState();
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                toggleAll(this.checked);
            });
        }
        
        if (checkAllMobile) {
            checkAllMobile.addEventListener('change', function() {
                toggleAll(this.checked);
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonState);
        });
    });

    function confirmBulkDelete() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Riwayat tiket yang dipilih akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    }
</script>
@endsection
