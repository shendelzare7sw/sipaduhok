@extends('layouts.sneat')

@section('title', 'Monitoring Guru Pengajar')

@section('page-title', 'Monitoring Guru Pengajar')
@section('page-subtitle', 'Pantau aktivitas dan jadwal guru pengajar')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-tie text-primary me-2"></i>
                Data Guru Pengajar
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Total Jadwal Mengajar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guruPengajar as $index => $guru)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $guru->nama_lengkap }}</strong>
                                <br>
                                <small class="text-muted">
                                    <span class="badge bg-label-{{ $guru->user->role == 'wali_kelas' ? 'success' : 'info' }}">
                                        {{ ucwords(str_replace('_', ' ', $guru->user->role)) }}
                                    </span>
                                </small>
                            </td>
                            <td>{{ $guru->nip ?? '-' }}</td>
                            <td>{{ $guru->email }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $guru->total_jadwal }} Jadwal</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $guru->user->is_active ? 'success' : 'danger' }}">
                                    {{ $guru->user->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data guru pengajar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
