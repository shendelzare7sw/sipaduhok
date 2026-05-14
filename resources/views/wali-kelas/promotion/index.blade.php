@extends('layouts.sneat')

@section('title', 'Prediksi Kenaikan Kelas')
@section('page-title', 'Prediksi Kenaikan Kelas')
@section('page-subtitle', 'Simulasi kelayakan naik kelas berdasarkan data akademik dan keuangan')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
@endsection

@section('content')
<div class="wk-page">
    <div class="wk-toolbar">
        <div class="wk-toolbar-title">
            <span class="wk-toolbar-icon"><i class="fas fa-chart-bar"></i></span>
            <div>
                <h5>Prediksi Kenaikan Kelas</h5>
                <p>Simulasi status siswa berdasarkan ketuntasan akademik dan kondisi keuangan saat ini.</p>
            </div>
        </div>
    </div>

    @isset($error)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @endisset

    @isset($kelas)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-school me-2 text-primary"></i>
                    Kelas {{ $kelas->nama_kelas }} - {{ $tahun->nama_tahun_ajaran }}
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Halaman ini adalah <strong>simulasi</strong> berdasarkan data saat ini. Status akhir tetap ditentukan saat tanggal eksekusi sistem.
                </div>

                <form method="GET" action="{{ route('wali.promotion.prediction') }}" class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-8">
                        <label class="form-label">Cari Siswa</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label">Status Prediksi</label>
                        <select name="status_filter" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="aman" {{ request('status_filter') == 'aman' ? 'selected' : '' }}>Aman / Naik Kelas</option>
                            <option value="rawan" {{ request('status_filter') == 'rawan' ? 'selected' : '' }}>Rawan / Tertunda</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Daftar Prediksi Siswa</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover wk-card-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Status Keuangan</th>
                            <th>Status Akademik</th>
                            <th>Prediksi Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prediction as $p)
                            <tr>
                                <td>
                                    <div class="fw-bold text-gray-900">{{ $p['siswa']->nama_lengkap }}</div>
                                </td>
                                <td>
                                    @if($p['result']['financial']['status'] == 'LUNAS')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                        @if($p['result']['financial']['is_dispensasi'])
                                            <span class="badge bg-warning mt-1">Dispensasi OK</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($p['result']['academic']['is_tuntas'])
                                        <span class="badge bg-success">Aman {{ $p['result']['academic']['percentage'] }}%</span>
                                    @else
                                        <span class="badge bg-danger">Rawan {{ $p['result']['academic']['percentage'] }}%</span>
                                        <div class="small text-muted mt-1">Hanya {{ $p['result']['academic']['tuntas_count'] }} mapel tuntas</div>
                                    @endif
                                </td>
                                <td>
                                    @if($p['result']['eligible'])
                                        @if(preg_match('/(9|IX|12|XII)/', strtoupper($kelas->nama_kelas)))
                                            <span class="fw-bold text-info"><i class="fas fa-graduation-cap me-1"></i>Lulus</span>
                                        @else
                                            <span class="fw-bold text-success"><i class="fas fa-check-circle me-1"></i>Naik Kelas</span>
                                        @endif
                                    @else
                                        <span class="fw-bold text-danger"><i class="fas fa-times-circle me-1"></i>Tertunda</span>
                                        <div class="small text-muted">
                                            {{ !$p['result']['academic']['is_tuntas'] ? 'Nilai Kurang' : 'Tunggakan' }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Tidak ada data prediksi ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endisset
</div>
@endsection
