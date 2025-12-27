@extends('layouts.sneat')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')
@section('page-subtitle', 'Ringkasan aktivitas mengajar hari ini')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .stat-card-custom {
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .stat-card-custom:hover {
        transform: translateY(-5px);
    }
    .jadwal-item {
        transition: all 0.2s ease;
        border-radius: 8px;
    }
    .jadwal-item:hover {
        background-color: #f8f9fc;
        transform: translateX(5px);
    }
    .kelas-item {
        transition: all 0.2s ease;
        border-radius: 8px;
    }
    .kelas-item:hover {
        background-color: #f0f9ff;
        transform: translateX(5px);
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- STATS CARDS --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow stat-card-custom h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small fw-bold text-uppercase mb-1">Kelas Diampu</div>
                            <div class="h2 fw-bold mb-0 text-white">{{ $kelasYangDiajar->count() ?? 0 }}</div>
                            <div class="small text-white">Kelas aktif</div>
                        </div>
                        <div style="font-size: 3rem; opacity: 0.2;">🏫</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-success text-white shadow stat-card-custom h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small fw-bold text-uppercase mb-1">Total Siswa</div>
                            <div class="h2 fw-bold mb-0 text-white">{{ $totalSiswa ?? 0 }}</div>
                            <div class="small text-white">Siswa diajar</div>
                        </div>
                        <div style="font-size: 3rem; opacity: 0.2;">👨‍🎓</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card bg-warning text-white shadow stat-card-custom h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small fw-bold text-uppercase mb-1">Jadwal Hari Ini</div>
                            <div class="h2 fw-bold mb-0 text-white">{{ $jadwalHariIni->count() ?? 0 }}</div>
                            <div class="small text-white">Jam mengajar</div>
                        </div>
                        <div style="font-size: 3rem; opacity: 0.2;">📅</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- JADWAL MENGAJAR --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-day me-2"></i>Jadwal Mengajar Hari Ini
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($jadwalHariIni) && $jadwalHariIni->count())
                        @foreach($jadwalHariIni as $jadwal)
                        <div class="p-3 mb-3 border rounded jadwal-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-primary">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </div>
                                <div class="small text-muted">
                                    {{ $jadwal->kelas->nama_kelas }} • {{ $jadwal->mataPelajaran->nama_mapel }}
                                </div>
                            </div>
                            <a href="{{ route('guru.lms.dashboard', [$jadwal->kelas_id, $jadwal->mata_pelajaran_id]) }}"
                               class="btn btn-primary btn-sm shadow-sm">
                                <i class="fas fa-door-open me-1"></i>Masuk
                            </a>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-gray-200 mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada jadwal hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KELAS DIAMPU --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Kelas yang Diampu
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($kelasYangDiajar) && $kelasYangDiajar->count())
                        @foreach($kelasYangDiajar as $item)
                        <div class="p-3 mb-2 border-start border-info border-4 rounded kelas-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-gray-800">{{ $item['kelas']->nama_kelas }}</div>
                                <div class="small text-muted">
                                    <i class="fas fa-users me-1"></i>{{ $item['jumlah_siswa'] }} siswa •
                                    <i class="fas fa-book me-1"></i>{{ $item['jumlah_mapel'] }} mapel
                                </div>
                            </div>
                            <a href="{{ route('guru.kelas.mapel', $item['kelas']->id) }}"
                               class="btn btn-info btn-sm shadow-sm">
                                <i class="fas fa-arrow-right me-1"></i>Kelola
                            </a>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-gray-200 mb-3"></i>
                            <p class="text-muted mb-0">Belum ada kelas yang diampu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card shadow-sm border-start border-primary border-4">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3">
                <i class="fas fa-bolt me-2"></i>Aksi Cepat
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <a href="{{ route('guru.kelas.index') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-list me-2"></i>Lihat Semua Kelas
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="#" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-calendar me-2"></i>Jadwal Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection