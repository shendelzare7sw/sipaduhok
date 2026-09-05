@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')
@section('page-subtitle', isset($kelas) ? 'Lihat jadwal pelajaran kelas ' . $kelas->nama_kelas : 'Kelola jadwal pelajaran')


@section('styles')
    @vite(['resources/css/wali-kelas/jadwal/index.css', 'resources/js/wali-kelas/jadwal/index.js'])
@endsection

@section('content')
<div class="wk-page">
<div class="container-fluid px-0">

    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @endif

    {{-- HEADER ACTIONS --}}
    @if($kelas)
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Kelas {{ $kelas->nama_kelas }}
                    </h4>
                    <p class="text-muted small mb-0 mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Jadwal pelajaran dikelola oleh Admin. Anda dapat melihat dan mencetak jadwal.
                    </p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('wali.jadwal.print') }}" target="_blank" class="btn btn-primary shadow-sm">
                        <i class="fas fa-print me-1"></i> Cetak Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- JADWAL PER HARI --}}
    @if($kelas)
    <div class="row">
        @foreach($hariList as $hari)
        <div class="col-lg-6 mb-4">
            <div class="card shadow card-hari h-100">
                <div class="header-hari d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-gray-800">
                        <i class="fas fa-clock me-2 text-primary"></i>{{ $hari }}
                    </h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        {{ $jadwalPerHari[$hari]->count() }} Pelajaran
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($jadwalPerHari[$hari]->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-jadwal wk-card-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Pengajar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalPerHari[$hari] as $jadwal)
                                <tr>
                                    <td class="ps-4 align-middle">
                                        <div class="jam-badge small">
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-800">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                        <small class="text-muted">{{ $jadwal->mataPelajaran->kode_mapel }}</small>
                                    </td>
                                    <td class="align-middle small fw-bold text-gray-600">
                                        {{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-gray-200 mb-3"></i>
                        <p class="text-gray-500 small">Belum ada jadwal hari ini</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
</div>

@endsection
