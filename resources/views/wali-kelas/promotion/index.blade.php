@extends('layouts.sneat')

@section('title', 'Prediksi Kenaikan Kelas')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Wali Kelas /</span> Prediksi Kenaikan Kelas</h4>

    @isset($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endisset

    @isset($kelas)
    <div class="card">
        <h5 class="card-header">Kelas: {{ $kelas->nama_kelas }} ({{ $tahun->nama_tahun_ajaran }})</h5>
        <div class="card-body">
                <i class="fas fa-info-circle me-1"></i>
                Halaman ini adalah <strong>SIMULASI</strong> berdasarkan data saat ini. Status akhir ditentukan saat tanggal eksekusi sistem.
            </div>

            <hr class="my-4">

            <form method="GET" action="{{ route('wali.promotion.prediction') }}" class="row g-3 mb-4">
                <div class="col-md-6 col-lg-8">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 col-lg-3">
                    <select name="status_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="aman" {{ request('status_filter') == 'aman' ? 'selected' : '' }}>Aman / Naik Kelas</option>
                        <option value="rawan" {{ request('status_filter') == 'rawan' ? 'selected' : '' }}>Rawan / Tertunda</option>
                    </select>
                </div>
                <div class="col-md-2 col-lg-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </form>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Status Keuangan</th>
                            <th>Status Akademik</th>
                            <th>Prediksi Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prediction as $p)
                        <tr>
                            <td>{{ $p['siswa']->nama_lengkap }}</td>
                            <td>
                                @if($p['result']['financial']['status'] == 'LUNAS')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger">Belum Lunas</span>
                                    @if($p['result']['financial']['is_dispensasi'])
                                        <br><span class="badge bg-warning mt-1">Dispensasi OK</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($p['result']['academic']['is_tuntas'])
                                    <span class="badge bg-success">Aman ({{ $p['result']['academic']['percentage'] }}%)</span>
                                @else
                                    <span class="badge bg-danger">Rawan ({{ $p['result']['academic']['percentage'] }}%)</span>
                                    <br><small>Hanya {{ $p['result']['academic']['tuntas_count'] }} mapel tuntas</small>
                                @endif
                            </td>
                            <td>
                                @if($p['result']['eligible'])
                                    @if(preg_match('/(9|IX|12|XII)/', strtoupper($kelas->nama_kelas)))
                                        <span class="text-info fw-bold"><i class="fas fa-graduation-cap"></i> Lulus</span>
                                    @else
                                        <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Naik Kelas</span>
                                    @endif
                                @else
                                    <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Tertunda</span>
                                    <br><small class="text-muted">
                                        {{ !$p['result']['academic']['is_tuntas'] ? 'Nilai Kurang' : 'Tunggakan' }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endisset
</div>
@endsection
