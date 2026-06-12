@extends('layouts.sneat')

@section('title', 'Dashboard SIA')
@section('page-title', 'Sistem Informasi Akademik')
@section('page-subtitle', 'Selamat datang, ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@push('styles')
    @vite(['resources/css/siswa/sia/dashboard.css'])
@endpush

@section('content')
    <div class="sia-dashboard-page"
        data-sia-dashboard
        data-chart-enabled="{{ $siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']) ? 'true' : 'false' }}"
        data-chart-completed="{{ $performa['tugas']['selesai'] ?? 0 }}"
        data-chart-total="{{ $performa['tugas']['total'] ?? 0 }}"
        data-flyer-user-id="{{ auth()->id() }}">

    <!-- Profile Banner -->
    <div class="profile-banner">
        <div class="profile-avatar">
            @if(auth()->user()->foto_profil)
                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="user">
            @elseif($siswa->foto)
                <img src="{{ asset('storage/' . $siswa->foto) }}" alt="user">
            @else
                {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-name">Halo, {{ explode(' ', $siswa->nama_lengkap)[0] }}!</div>
            <div class="profile-meta">
                <span><i class="fas fa-school"></i> {{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                <span><i class="fas fa-calendar-alt"></i> {{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                <span><i class="fas fa-id-card"></i> {{ $siswa->nis ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Pengumuman --}}
    @if(isset($pengumuman) && $pengumuman->count() > 0)
        <div class="announcement-banner">
            <div class="announcement-title">
                <i class="fas fa-bullhorn"></i> Pengumuman
            </div>
            <div class="row g-2">
                @foreach($pengumuman->take(2) as $item)
                    <div class="col-md-6">
                        <div class="announcement-item">
                            <h6>{{ $item->judul }}</h6>
                            <p>{{ Str::limit($item->isi_pengumuman, 100) }}</p>
                            <small><i class="far fa-calendar-alt me-1"></i>{{ $item->tanggal_pengumuman->format('d M Y') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Attendance Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box stat-icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['hadir'] ?? 0 }}</div>
                        <div class="stat-label">Hadir</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box stat-icon-primary">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['sakit'] ?? 0 }}</div>
                        <div class="stat-label">Sakit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box stat-icon-warning">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['izin'] ?? 0 }}</div>
                        <div class="stat-label">Izin</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="s-card">
                <div class="stat-widget">
                    <div class="stat-icon-box stat-icon-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ $absensi['alpha'] ?? 0 }}</div>
                        <div class="stat-label">Alpha</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">
        <!-- Left: Jadwal + Tugas -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- Jadwal Hari Ini -->
            <div class="s-card">
                <div class="s-card-header">
                    <h5 class="s-card-title">
                        <i class="fas fa-clock text-warning"></i> Jadwal Hari Ini
                        <span class="badge bg-label-primary ms-1 badge-xs-md">{{ now()->locale('id')->translatedFormat('l') }}</span>
                    </h5>
                    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                        <a href="{{ route('siswa.lms.jadwal') }}" class="text-primary fw-semibold text-decoration-none link-small">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>
                @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                    <ul class="jadwal-list">
                        @foreach($jadwalHariIni as $jadwal)
                            <li class="jadwal-item">
                                <div class="jadwal-time">
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                </div>
                                <div class="jadwal-dot"></div>
                                <div class="jadwal-body">
                                    <div class="jadwal-mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                    <div class="jadwal-guru">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</div>
                                </div>
                                @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                                    <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}"
                                       class="btn btn-sm btn-outline-primary px-3 btn-sia-action">
                                        Masuk
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <i class="fas fa-coffee d-block"></i>
                        <div class="empty-state-title">Tidak Ada Jadwal</div>
                        <div class="empty-state-desc">Tidak ada pelajaran hari ini. Selamat istirahat!</div>
                    </div>
                @endif
            </div>

            {{-- Tugas & Nilai (LMS only) --}}
            @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))

                <div class="row g-4 flex-grow-1">
                    <!-- Tugas & Deadline -->
                    <div class="col-md-6 d-flex">
                        <div class="s-card d-flex flex-column w-100">
                            <div class="s-card-header">
                                <h5 class="s-card-title">
                                    <i class="fas fa-tasks text-danger"></i> Tugas
                                </h5>
                            </div>
                            @if($tugasList->count() > 0)
                                <ul class="tugas-list flex-grow-1">
                                    @foreach($tugasList as $tugas)
                                        @php
                                            $deadline = \Carbon\Carbon::parse($tugas->tanggal_deadline);
                                            $diffDays = now()->diffInDays($deadline, false);
                                            $isUrgent = $diffDays <= 1;
                                            $isWarning = $diffDays > 1 && $diffDays <= 3;
                                            $tugasTone = $isUrgent ? 'danger' : ($isWarning ? 'warning' : 'primary');
                                        @endphp
                                        <li class="tugas-item">
                                            <div class="tugas-icon tugas-icon-{{ $tugasTone }}">
                                                <i class="fas {{ $isUrgent ? 'fa-exclamation-triangle' : ($isWarning ? 'fa-clock' : 'fa-file-alt') }}"></i>
                                            </div>
                                            <div class="tugas-body">
                                                <div class="tugas-title">{{ $tugas->judul_tugas }}</div>
                                                <div class="tugas-meta">
                                                    {{ $tugas->mataPelajaran->nama_mapel }} - {{ $deadline->locale('id')->isoFormat('D MMM') }}
                                                    @if($isUrgent)
                                                        <span class="badge bg-danger ms-1 badge-xxs">Urgent</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('siswa.lms.mapel.show', $tugas->mata_pelajaran_id) }}"
                                               class="btn btn-sm btn-outline-primary btn-sia-mini">
                                                Lihat
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-state flex-grow-1 d-flex flex-column justify-content-center">
                                    <i class="fas fa-check-circle d-block text-success"></i>
                                    <div class="empty-state-title text-success">Semua Selesai!</div>
                                    <div class="empty-state-desc">Tidak ada tugas yang harus dikerjakan.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Nilai Terbaru -->
                    <div class="col-md-6 d-flex">
                        <div class="s-card d-flex flex-column w-100">
                            <div class="s-card-header">
                                <h5 class="s-card-title">
                                    <i class="fas fa-trophy text-warning"></i> Nilai Terbaru
                                </h5>
                                <a href="{{ route('siswa.sia.penilaian') }}" class="text-primary fw-semibold text-decoration-none link-small">
                                    Semua <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            @if($nilaiTerbaru->count() > 0)
                                <ul class="tugas-list flex-grow-1">
                                    @foreach($nilaiTerbaru as $nilai)
                                        <li class="tugas-item">
                                            <div class="tugas-icon tugas-icon-warning">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="tugas-body">
                                                <div class="tugas-title">{{ $nilai->mataPelajaran->nama_mapel }}</div>
                                                <div class="tugas-meta">
                                                    @if($nilai->jenis_penilaian == 'tugas')
                                                        <span class="badge bg-info badge-xxs">Tugas</span>
                                                    @elseif($nilai->jenis_penilaian == 'uts')
                                                        <span class="badge bg-warning badge-xxs">UTS</span>
                                                    @elseif($nilai->jenis_penilaian == 'uas')
                                                        <span class="badge bg-danger badge-xxs">UAS</span>
                                                    @else
                                                        <span class="badge bg-secondary badge-xxs">{{ ucfirst($nilai->jenis_penilaian) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="fw-bold score-value">{{ $nilai->nilai ?? '-' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-state flex-grow-1 d-flex flex-column justify-content-center">
                                    <i class="fas fa-file-alt d-block"></i>
                                    <div class="empty-state-title">Belum Ada Nilai</div>
                                    <div class="empty-state-desc">Nilai akan muncul setelah guru menilai tugas Anda.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @endif

        </div>

        <!-- Right: Quick Links + LMS -->
        <div class="col-lg-4 d-flex flex-column gap-4">

            <!-- Quick Links -->
            <div class="s-card">
                <div class="s-card-header">
                    <h5 class="s-card-title">
                        <i class="fas fa-bolt text-warning"></i> Akses Cepat
                    </h5>
                </div>
                <div class="quick-links-grid">
                    <a href="{{ route('siswa.sia.presensi.index') }}" class="quick-link-item">
                        <i class="fas fa-user-check text-primary"></i>
                        <span class="quick-link-text">Presensi</span>
                    </a>
                    <a href="{{ route('siswa.sia.penilaian') }}" class="quick-link-item">
                        <i class="fas fa-chart-line text-success"></i>
                        <span class="quick-link-text">Nilai</span>
                    </a>
                    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                        <a href="{{ route('siswa.lms.dashboard') }}" class="quick-link-item">
                            <i class="fas fa-graduation-cap text-info"></i>
                            <span class="quick-link-text">LMS</span>
                        </a>
                    @else
                        <a href="{{ route('siswa.sia.dashboard') }}" class="quick-link-item">
                            <i class="fas fa-home text-info"></i>
                            <span class="quick-link-text">Home</span>
                        </a>
                    @endif
                </div>
            </div>

            @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                <!-- LMS Banner -->
                <div class="lms-banner">
                    <div class="lms-banner-info">
                        <i class="fas fa-graduation-cap lms-banner-icon d-none d-sm-block"></i>
                        <div>
                            <h6>HOK-LMS</h6>
                            <p>Kerjakan tugas & materi online hari ini.</p>
                        </div>
                    </div>
                    <a href="{{ route('siswa.lms.dashboard') }}" class="btn-lms-enter">
                        MASUK LMS
                    </a>
                </div>
            @endif

            <!-- Progres Belajar -->
            @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                <div class="s-card flex-grow-1">
                    <div class="s-card-header">
                        <h5 class="s-card-title">
                            <i class="fas fa-chart-pie text-purple"></i> Progres Belajar
                        </h5>
                    </div>
                    <div class="p-3">
                        <div class="chart-box">
                            <canvas id="performaChart"></canvas>
                        </div>
                        <div class="row text-center mt-3 g-0">
                            <div class="col-4 border-end">
                                <div class="progress-label progress-label-success">LULUS</div>
                                <div class="progress-value">{{ $performa['tugas']['selesai'] ?? 0 }}</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="progress-label progress-label-warning">PROSES</div>
                                <div class="progress-value">{{ ($performa['tugas']['total'] ?? 0) - ($performa['tugas']['selesai'] ?? 0) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="progress-label progress-label-danger">TUNDA</div>
                                <div class="progress-value">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Flyer Popup --}}
    @if(isset($flyers) && $flyers->count() > 0)
        <div class="modal-flyer" id="flyerModal">
            <div class="modal-flyer-content shadow-lg">
                <span class="flyer-close" data-flyer-close><i class="fas fa-times"></i></span>
                @foreach($flyers->take(1) as $flyer)
                    <img src="{{ $flyer->gambar_url }}" class="flyer-image" alt="{{ $flyer->judul }}">
                    <div class="p-4 text-center">
                        <h5 class="fw-bold text-primary mb-2">{{ $flyer->judul }}</h5>
                        <p class="small text-muted mb-3">{{ $flyer->deskripsi }}</p>
                        @if($flyer->link_url)
                            <a href="{{ $flyer->link_url }}" target="_blank" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">LIHAT SELENGKAPNYA</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    </div>
@endsection

@push('scripts')
    @vite(['resources/js/siswa/sia/dashboard.js'])
@endpush
