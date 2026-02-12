@extends('layouts.sneat')

@section('title', 'Validasi Akses Rapor')
@section('page-title', 'Validasi Akses Rapor - Ketua PKBM')
@section('page-subtitle', 'Level 3 Validation (Final Approval)')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Validasi Akses Rapor - Ketua PKBM</h1>
            <p class="text-muted mb-0">Level 3 Validation (Final Approval)</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Validasi Ketua
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingTotal'] }}</div>
                            <small class="text-muted">Siswa yang sudah divalidasi Bendahara & Wali Kelas</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Divalidasi Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['validatedToday'] }}</div>
                            <small class="text-muted">Siswa yang divalidasi oleh Ketua hari ini</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ketua.validasi-rapor.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas_id" class="form-control">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status Ketua</label>
                            <select name="status_ketua" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status_ketua') == 'pending' ? 'selected' : '' }}>Belum Divalidasi</option>
                                <option value="validated" {{ request('status_ketua') == 'validated' ? 'selected' : '' }}>Sudah Divalidasi</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pencarian (Nama/NIS)</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIS..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('ketua.validasi-rapor.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
            <div>
                <button type="button" class="btn btn-success btn-sm" onclick="validasiSemua()">
                    <i class="fas fa-check-double"></i> Validasi Semua
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="validasiTerpilih()">
                    <i class="fas fa-check"></i> Validasi Terpilih
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th width="50">No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th width="80" class="text-center">Bendahara</th>
                            <th width="80" class="text-center">Wali Kelas</th>
                            <th width="80" class="text-center">Ketua PKBM</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $siswa)
                        <tr>
                            <td>
                                @if(!$siswa->validasi_rapor_ketua)
                                <input type="checkbox" class="siswa-checkbox" value="{{ $siswa->id }}">
                                @endif
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="text-center">
                                @if($siswa->validasi_rapor_bendahara)
                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($siswa->validasi_rapor_wali)
                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($siswa->validasi_rapor_ketua)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Valid</span>
                                    <br><small class="text-muted">{{ $siswa->tanggal_validasi_rapor_ketua ? $siswa->tanggal_validasi_rapor_ketua->format('d/m/Y') : '' }}</small>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($siswa->validasi_rapor_ketua)
                                    <form action="{{ route('ketua.validasi-rapor.batalkan', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan validasi untuk {{ $siswa->nama_lengkap }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Batalkan
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('ketua.validasi-rapor.validasi', $siswa->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Validasi
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data siswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $siswaList->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Check all functionality
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Validasi terpilih
function validasiTerpilih() {
    const selected = Array.from(document.querySelectorAll('.siswa-checkbox:checked')).map(cb => cb.value);

    if (selected.length === 0) {
        alert('Pilih minimal 1 siswa untuk divalidasi');
        return;
    }

    if (!confirm(`Validasi ${selected.length} siswa terpilih?`)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("ketua.validasi-rapor.bulk-validasi") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'siswa_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

// Validasi semua
function validasiSemua() {
    if (!confirm('Validasi SEMUA siswa yang pending?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("ketua.validasi-rapor.validasi-semua") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
