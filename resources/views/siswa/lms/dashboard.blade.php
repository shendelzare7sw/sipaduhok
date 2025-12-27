@extends('layouts.lms')

@section('title', 'Beranda LMS')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Selamat datang di HOK Learning Management System')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    {{-- ALERT SELAMAT DATANG --}}
    <div class="alert border-0 shadow-sm" role="alert" style="background: #fff; border-left: 5px solid var(--primary) !important; margin-bottom: 24px; width: 100%;">
        <div class="row align-items-center g-3">
            <div class="col-md-8 col-12">
                <h5 class="alert-heading fw-bold text-primary mb-2">
                    Halo, {{ $siswa->nama_lengkap }}! 👋
                </h5>
                <p class="mb-0 text-muted small">
                    Selamat belajar! Jangan lupa cek Jadwal Mata Pelajaran hari ini.
                </p>
            </div>
            <div class="col-md-4 col-12 text-md-end text-center">
                <a href="{{ route('siswa.sia.dashboard') }}" class="btn btn-outline-primary fw-bold px-4 py-2">
                    <i class="fas fa-external-link-alt me-2"></i>Akses SIA
                </a>
            </div>
        </div>
    </div>
    
    {{-- STATISTIK SINGKAT - Grid System seperti Bendahara --}}
    <div class="stats-grid">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="stat-label">Kehadiran</div>
            <div class="stat-value" style="color: var(--primary);">{{ $persenKehadiran }}%</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-label">Tugas Pending</div>
            <div class="stat-value" style="color: #ea580c;">{{ $tugasPending }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #06b6d4;">
            <div class="stat-label">Agenda Bulan Ini</div>
            <div class="stat-value" style="color: #0891b2;">{{ $agendaBulanIni }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL DAN PENGUMUMAN - Grid Layout --}}
    <div class="content-grid">
        {{-- Jadwal Hari Ini --}}
        <div class="card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-clock me-2"></i>Jadwal Hari Ini
                </h6>
                <a href="{{ route('siswa.lms.jadwal') }}" class="btn btn-sm btn-outline-secondary">
                    Lihat Semua
                </a>
            </div>
            <div style="overflow-x: auto;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Jam</th>
                            <th style="width: 35%;">Mapel</th>
                            <th style="width: 30%;">Guru</th>
                            <th class="text-center" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalHariIni as $jadwal)
                        <tr>
                            <td class="fw-bold" style="color: var(--primary);">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }}
                            </td>
                            <td>
                                <div class="fw-bold">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</div>
                                <small class="text-muted">Kelas {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $jadwal->guru->nama_lengkap ?? 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" 
                                   class="btn btn-sm btn-primary-custom px-3 rounded-pill">
                                    Masuk
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 60px 20px;">
                                <i class="fas fa-calendar-times" style="font-size: 48px; opacity: 0.2; margin-bottom: 16px; display: block;"></i>
                                <div class="text-muted">Tidak ada jadwal hari ini</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pengumuman --}}
        <div class="card-custom" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white;">
            <div style="padding: 24px;">
                <h6 class="fw-bold mb-4" style="padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-bullhorn me-2"></i>Pengumuman
                </h6>
                @if($pengumuman)
                <div>
                    <span class="badge bg-warning text-dark mb-2" style="font-size: 11px; padding: 4px 8px;">
                        {{ strtoupper($pengumuman->prioritas ?? 'Normal') }}
                    </span>
                    <h6 class="fw-bold mb-2">{{ $pengumuman->judul }}</h6>
                    <p class="small mb-4" style="opacity: 0.85; line-height: 1.6;">
                        {{ Str::limit($pengumuman->isi_pengumuman, 100) }}
                    </p>
                    <a href="{{ route('siswa.lms.kalender') }}" 
                       class="btn btn-light text-primary w-100 fw-bold d-flex align-items-center justify-content-center" 
                       style="gap: 8px;">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Lihat Kalender</span>
                    </a>
                </div>
                @else
                <div class="text-center" style="padding: 60px 20px;">
                    <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.2; margin-bottom: 16px; display: block;"></i>
                    <div style="opacity: 0.6;">Tidak ada pengumuman</div>
                </div>
                @endif
            </div>
            </div>
        </div>
    </div>
@endsection