@extends('layouts.app')

@section('title', 'Rekap Harian Presensi')
@section('page-title', 'Rekap Harian Presensi')
@section('page-subtitle', 'Daftar laporan presensi harian kelas ' . ($kelas->nama_kelas ?? ''))


@section('styles')
    @vite(['resources/css/wali-kelas/presensi/rekap-harian.css', 'resources/js/wali-kelas/presensi/rekap-harian.js'])
@endsection

@section('content')
<div class="wk-page">
<div class="container-fluid px-0">

    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @endif

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

    @if(!($error ?? false))
    {{-- Filter Bulan & Tahun --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('wali.presensi.rekap-harian') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label fw-bold small mb-1">Semester</label>
                    <select name="semester" class="form-select form-select-sm filter-select-md" data-auto-submit>
                        <option value="">Semua (Per Bulan)</option>
                        <option value="ganjil" {{ ($semester ?? '') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ ($semester ?? '') == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label fw-bold small mb-1">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm filter-select-md" {{ ($semester ?? '') ? 'disabled' : '' }}>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label fw-bold small mb-1">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm filter-select-sm" {{ ($semester ?? '') ? 'disabled' : '' }}>
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
    @endif

    @if(!($error ?? false))
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
                    $tanggalParam = $tanggalObj->toDateString();
                    $persen = $date->total_siswa > 0 ? round(($date->hadir / $date->total_siswa) * 100) : 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('wali.presensi.show-harian', ['tanggal' => $tanggalParam]) }}" class="text-decoration-none">
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
                                    <span class="stat-pill bg-label-success">
                                        <i class="fas fa-check me-1"></i>{{ $date->hadir }}
                                    </span>
                                    <span class="stat-pill bg-label-warning">
                                        <i class="fas fa-thermometer me-1"></i>{{ $date->sakit }}
                                    </span>
                                    <span class="stat-pill bg-label-info">
                                        <i class="fas fa-envelope me-1"></i>{{ $date->izin }}
                                    </span>
                                    <span class="stat-pill bg-label-danger">
                                        <i class="fas fa-times me-1"></i>{{ $date->alpha }}
                                    </span>
                                </div>
                                <div class="progress mt-2 progress-thinner">
                                    <div class="progress-bar bg-success" data-progress-width="{{ $persen }}"></div>
                                </div>
                                <small class="text-muted">Kehadiran {{ $persen }}%</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
    @endif

</div>
</div>
@endsection
