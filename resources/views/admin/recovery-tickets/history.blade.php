@extends('layouts.sneat')

@section('title', 'Riwayat Tiket Pemulihan')
@section('page-title', 'Manajemen Tiket Pemulihan')
@section('page-subtitle', 'Riwayat tiket pemulihan akses akun')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Permintaan</th>
                            <th>User</th>
                            <th>Kendala</th>
                            <th>Nomor WA</th>
                            <th>Status</th>
                            <th>Diselesaikan</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($tickets as $key => $ticket)
                        <tr>
                            <td>{{ $tickets->firstItem() + $key }}</td>
                            <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <strong>{{ $ticket->user->name ?? '-' }}</strong><br>
                                <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $ticket->user->roleRelation->name ?? $ticket->user->role ?? '-')) }}</span>
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
                                @if($ticket->target_phone)
                                    <span class="text-muted"><i class="bx bxl-whatsapp text-success"></i> {{ $ticket->target_phone }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->status == 'resolved')
                                    <span class="badge bg-success"><i class="bx bx-check me-1"></i> Selesai</span>
                                @elseif($ticket->status == 'rejected')
                                    <span class="badge bg-secondary"><i class="bx bx-x me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ $ticket->updated_at->format('d M Y H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bx bx-history text-muted mb-3" style="font-size: 5rem;"></i>
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
