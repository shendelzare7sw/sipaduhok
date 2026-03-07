@extends('layouts.sneat')

@section('title', 'Rekap Harian Presensi')
@section('page-title', 'Rekap Harian Presensi')
@section('page-subtitle', 'Daftar laporan presensi harian kelas ' . ($kelas->nama_kelas ?? ''))

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .date-card { transition: all 0.2s; border-left: 4px solid #4e73df; }
    .date-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; }
    .stat-pill { display: inline-block; padding: 2px 10px; border-radius: 50px; font-size: 12px; font-weight: 700; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-gray-900 mb-1"><i class="fas fa-calendar-day me-2 text-primary"></i>Rekap Harian Presensi</h5>
                    <p class="text-muted mb-0 small">Klik tanggal untuk melihat detail laporan presensi hari tersebut</p>
                </div>
                <a href="{{ route('wali.presensi.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Bulan & Tahun --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('wali.presensi.rekap-harian') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label fw-bold small mb-1">Semester</label>
                    <select name="semester" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                        <option value="">Semua (Per Bulan)</option>
                        <option value="ganjil" {{ ($semester ?? '') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ ($semester ?? '') == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label fw-bold small mb-1">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm" style="width: 160px;" {{ ($semester ?? '') ? 'disabled' : '' }}>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label fw-bold small mb-1">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm" style="width: 100px;" {{ ($semester ?? '') ? 'disabled' : '' }}>
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Date List --}}
    @if($dates->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">Belum ada data presensi untuk bulan ini</h6>
                <p class="text-muted small mb-3">Silakan input presensi harian terlebih dahulu</p>
                <a href="{{ route('wali.presensi.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i>Input Presensi
                </a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($dates as $date)
                @php
                    $tanggalObj = \Carbon\Carbon::parse($date->tanggal);
                    $persen = $date->total_siswa > 0 ? round(($date->hadir / $date->total_siswa) * 100) : 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('wali.presensi.show-harian', ['tanggal' => $date->tanggal]) }}" class="text-decoration-none">
                        <div class="card shadow-sm date-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-gray-900 mb-0">
                                            {{ $tanggalObj->translatedFormat('l') }}
                                        </h6>
                                        <span class="text-primary fw-bold">
                                            {{ $tanggalObj->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $date->total_siswa }} siswa</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <span class="stat-pill bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-check me-1"></i>{{ $date->hadir }}
                                    </span>
                                    <span class="stat-pill bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-thermometer me-1"></i>{{ $date->sakit }}
                                    </span>
                                    <span class="stat-pill bg-info bg-opacity-10 text-info">
                                        <i class="fas fa-envelope me-1"></i>{{ $date->izin }}
                                    </span>
                                    <span class="stat-pill bg-danger bg-opacity-10 text-danger">
                                        <i class="fas fa-times me-1"></i>{{ $date->alpha }}
                                    </span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                                </div>
                                <small class="text-muted">Kehadiran {{ $persen }}%</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>
</div>
@endsection
