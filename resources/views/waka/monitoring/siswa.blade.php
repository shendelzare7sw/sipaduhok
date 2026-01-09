@extends('layouts.sneat')

@section('title', 'Monitoring Siswa')

@section('page-title', 'Monitoring Siswa')
@section('page-subtitle', 'Pantau data dan aktivitas siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('waka.monitoring.siswa') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Jenjang</label>
                        <select name="jenjang" class="form-select">
                            <option value="">Semua Jenjang</option>
                            @foreach($jenjangs as $jenjang)
                                <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>
                                    {{ $jenjang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cabang</label>
                        <select name="cabang_id" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangList as $cabang)
                                <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-user-graduate text-primary me-2"></i>
                Data Siswa Aktif
            </h5>
            <span class="badge bg-primary">Total: {{ $siswa->total() }} Siswa</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS/NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Cabang</th>
                            <th>Jenis Kelamin</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $s)
                        <tr>
                            <td>{{ $siswa->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $s->nis }}</strong>
                                <br>
                                <small class="text-muted">NISN: {{ $s->nisn }}</small>
                            </td>
                            <td>
                                <strong>{{ $s->nama_lengkap }}</strong>
                            </td>
                            <td>
                                @if($s->kelas)
                                    {{ $s->kelas->nama_kelas }}
                                    <br>
                                    <span class="badge bg-info">{{ $s->kelas->jenjang }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $s->cabang->nama_cabang ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $s->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                    {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $s->status == 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data siswa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($siswa->hasPages())
            <div class="mt-3">
                {{ $siswa->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
