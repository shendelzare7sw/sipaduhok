@extends('layouts.sneat')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')
@section('page-subtitle', 'Ringkasan aktivitas mengajar hari ini')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/dashboard/guru.css'])
@endsection

@section('content')
<div class="guru-dashboard-page">

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="g-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $kelasYangDiajar->count() ?? 0 }}</div>
                        <div class="stat-label">Kelas Diampu</div>
                    </div>
                    <div class="stat-icon-box stat-icon-primary">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Kelas aktif</span>
                    <i class="fas fa-chalkboard opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="g-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $totalSiswa ?? 0 }}</div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                    <div class="stat-icon-box stat-icon-success">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Siswa yang diajar</span>
                    <i class="fas fa-users opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="g-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $jadwalHariIni->count() ?? 0 }}</div>
                        <div class="stat-label">Jadwal Hari Ini</div>
                    </div>
                    <div class="stat-icon-box stat-icon-warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>{{ now()->locale('id')->translatedFormat('l, d M Y') }}</span>
                    <i class="fas fa-clock opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">

        <!-- Left: Jadwal Mengajar -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- Jadwal Hari Ini -->
            <div class="g-card">
                <div class="g-card-header">
                    <h5 class="g-card-title">
                        <i class="fas fa-calendar-day text-warning"></i> Jadwal Mengajar
                        <span class="badge bg-primary ms-1 dashboard-day-badge">{{ now()->locale('id')->translatedFormat('l') }}</span>
                    </h5>
                    <a href="{{ route('guru.jadwal.index') }}" class="text-primary fw-semibold text-decoration-none dashboard-link-sm">
                        Jadwal Lengkap <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @if(isset($jadwalHariIni) && $jadwalHariIni->count())
                    <ul class="jadwal-list">
                        @foreach($jadwalHariIni as $jadwal)
                            <li class="jadwal-item">
                                <div class="jadwal-time">
                                    {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}
                                </div>
                                <div class="jadwal-dot"></div>
                                <div class="jadwal-body">
                                    <div class="jadwal-mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                    <div class="jadwal-kelas">Kelas {{ $jadwal->kelas->nama_kelas }}</div>
                                </div>
                                <a href="{{ route('guru.lms.dashboard', [$jadwal->kelas->id, $jadwal->link_mapel_id]) }}"
                                   class="btn btn-sm btn-outline-primary px-3 dashboard-action-button">
                                    <i class="fas fa-door-open me-1"></i>Masuk LMS
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="fas fa-coffee d-block"></i>
                        <div class="empty-state-title">Tidak Ada Jadwal</div>
                        <div class="empty-state-desc">Tidak ada jadwal mengajar hari ini. Istirahat sejenak!</div>
                    </div>
                @endif
            </div>

            <!-- Kelas yang Diampu -->
            <div class="g-card">
                <div class="g-card-header">
                    <h5 class="g-card-title">
                        <i class="fas fa-chalkboard-teacher text-info"></i> Kelas yang Diampu
                    </h5>
                    <a href="{{ route('guru.kelas.index') }}" class="text-primary fw-semibold text-decoration-none dashboard-link-sm">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @if(isset($kelasYangDiajar) && $kelasYangDiajar->count())
                    <ul class="kelas-grid">
                        @foreach($kelasYangDiajar as $item)
                            <li class="kelas-item">
                                <div class="kelas-icon">{{ $item['kelas']->nama_kelas }}</div>
                                <div class="kelas-body">
                                    <div class="kelas-name">Kelas {{ $item['kelas']->nama_kelas }}</div>
                                    <div class="kelas-meta">
                                        <span><i class="fas fa-users me-1"></i>{{ $item['jumlah_siswa'] }} siswa</span>
                                        <span>·</span>
                                        <span><i class="fas fa-book me-1"></i>{{ $item['jumlah_mapel'] }} mapel</span>
                                    </div>
                                </div>
                                <a href="{{ route('guru.kelas.mapel', $item['kelas']->id) }}"
                                   class="btn btn-sm btn-outline-info px-3 dashboard-action-button">
                                    <i class="fas fa-arrow-right me-1"></i>Kelola
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox d-block"></i>
                        <div class="empty-state-title">Belum Ada Kelas</div>
                        <div class="empty-state-desc">Belum ada kelas yang diampu saat ini.</div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right: Quick Links -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <div class="g-card flex-grow-1">
                <div class="g-card-header">
                    <h5 class="g-card-title">
                        <i class="fas fa-bolt text-warning"></i> Akses Cepat
                    </h5>
                </div>
                <div class="quick-links-grid">
                    <a href="{{ route('guru.kelas.index') }}" class="quick-link-item">
                        <i class="fas fa-list text-primary"></i>
                        <span class="quick-link-text">Semua<br>Kelas</span>
                    </a>
                    <a href="{{ route('guru.jadwal.index') }}" class="quick-link-item">
                        <i class="fas fa-calendar-alt text-success"></i>
                        <span class="quick-link-text">Jadwal<br>Mengajar</span>
                    </a>
                    @if(isset($kelasYangDiajar) && $kelasYangDiajar->count() > 0)
                        @php $firstKelas = $kelasYangDiajar->first(); @endphp
                        <a href="{{ route('guru.kelas.mapel', $firstKelas['kelas']->id) }}" class="quick-link-item">
                            <i class="fas fa-chalkboard text-warning"></i>
                            <span class="quick-link-text">Kelas<br>Pertama</span>
                        </a>
                    @else
                        <a href="{{ route('guru.kelas.index') }}" class="quick-link-item">
                            <i class="fas fa-chalkboard text-warning"></i>
                            <span class="quick-link-text">Kelola<br>Kelas</span>
                        </a>
                    @endif
                    <a href="{{ route('guru.dashboard') }}" class="quick-link-item">
                        <i class="fas fa-sync-alt text-info"></i>
                        <span class="quick-link-text">Refresh<br>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
