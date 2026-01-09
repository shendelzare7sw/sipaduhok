@extends('layouts.sneat')

@section('title', 'Monitoring Wali Kelas')

@section('page-title', 'Monitoring Wali Kelas')
@section('page-subtitle', 'Pantau aktivitas dan kinerja wali kelas')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                Data Wali Kelas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Wali Kelas</th>
                            <th>Kelas</th>
                            <th>Jenjang</th>
                            <th>Tahun Ajaran</th>
                            <th>Jumlah Siswa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waliKelas as $index => $wali)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $wali->nama_lengkap }}</strong>
                                <br>
                                <small class="text-muted">{{ $wali->nip ?? '-' }}</small>
                            </td>
                            <td>
                                @if(isset($wali->kelas_info))
                                    <strong>{{ $wali->kelas_info->nama_kelas }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($wali->kelas_info))
                                    <span class="badge bg-info">{{ $wali->kelas_info->jenjang }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($wali->kelas_info->tahunAjaran))
                                    {{ $wali->kelas_info->tahunAjaran->tahun_ajaran }} - {{ ucfirst($wali->kelas_info->tahunAjaran->semester) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $wali->total_siswa ?? 0 }} Siswa</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $wali->user->is_active ? 'success' : 'danger' }}">
                                    {{ $wali->user->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data wali kelas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
