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
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-card-mobile">
                    <thead>
                        <tr>
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
                            <td class="mobile-hide">{{ $tickets->firstItem() + $key }}</td>
                            <td class="desktop-only-cell">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            <td class="desktop-only-cell">
                                <strong>{{ $ticket->user->name ?? '-' }}</strong><br>
                                <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role ?? '-')) }}</span>
                            </td>
                            <td class="mobile-only-cell mobile-card-head">
                                <strong>{{ $ticket->user->name ?? '-' }}</strong>
                                <span class="badge bg-label-info ms-1">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role ?? '-')) }}</span>
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
        </div>
    </div>
</div>
@endsection
