@extends('layouts.sneat')

@section('title', 'Dashboard Admin')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Dashboard Admin</h5>
                    <small class="text-muted">Selamat datang, {{ $user->name }}</small>
                </div>
                <div class="card-body">
                    @if($user->isSuperAdmin())
                        <div class="alert alert-info">
                            <i class="bx bx-shield-alt-2"></i> Anda login sebagai <strong>Admin</strong> dengan akses penuh ke seluruh sistem.
                        </div>
                    @endif
                    
                    <div class="row">
                        <!-- Total Users -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-user fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Total Users</small>
                                            <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Students -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="bx bx-group fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Siswa Aktif</small>
                                            <h4 class="mb-0">{{ $stats['total_students'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payments Today -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="bx bx-wallet fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Pembayaran Hari Ini</small>
                                            <h4 class="mb-0">{{ $stats['total_payments_today'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pending Payments -->
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class="bx bx-error-circle fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Tagihan Pending</small>
                                            <h4 class="mb-0">{{ $stats['pending_payments'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Logins -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Aktivitas Login Terakhir</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Waktu Login</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($recent_logins as $login)
                                        <tr>
                                            <td>{{ $login->name }}</td>
                                            <td>{{ $login->email }}</td>
                                            <td>
                                                @if($login->role_id && $login->roleRelation)
                                                    <span class="badge bg-primary">{{ $login->roleRelation->display_name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $login->role ?? 'N/A')) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $login->last_login_at ? $login->last_login_at->diffForHumans() : '-' }}</td>
                                            <td><code>{{ $login->last_login_ip ?? '-' }}</code></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada aktivitas login</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
