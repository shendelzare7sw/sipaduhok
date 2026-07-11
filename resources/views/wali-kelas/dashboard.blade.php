@extends('layouts.sneat')

@section('title', 'Dashboard Wali Kelas')
@section('page-title', 'Dashboard Wali Kelas')
@section('page-subtitle', 'Kelola kelas dan siswa Anda')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-kelas/dashboard.css', 'resources/js/wali-kelas/dashboard.js'])
@endsection

@section('content')

    @if(isset($message))
        <div class="error-card">
            <i class="fas fa-user-slash d-block"></i>
            <h4>Akses Terbatas</h4>
            <p>{{ $message }}</p>
        </div>
    @elseif(!$kelas)
        <div class="error-card">
            <i class="fas fa-user-slash d-block"></i>
            <h4>Akses Terbatas</h4>
            <p>Anda belum ditugaskan sebagai wali kelas. Silakan hubungi bagian Admin Kurikulum.</p>
        </div>
    @else

        <!-- Class Info Banner -->
        <div class="class-info-banner">
            <div class="class-info-icon">
                <i class="fas fa-school"></i>
            </div>
            <div class="class-info-details">
                <div class="class-info-name">Kelas {{ $kelas->nama_kelas }}</div>
                <div class="class-info-meta">
                    <span><i class="fas fa-layer-group"></i> {{ strtoupper($kelas->jenjang) }}</span>
                    <span><i class="fas fa-calendar-alt"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4">
            <!-- Total Siswa -->
            <div class="col-6 col-xl-3">
                <div class="wk-card">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $totalSiswa }}</div>
                            <div class="stat-label">Total Siswa</div>
                        </div>
                        <div class="stat-icon-box stat-icon-primary-soft">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span>Kelas {{ $kelas->nama_kelas }}</span>
                        <i class="fas fa-users opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Hadir Hari Ini -->
            <div class="col-6 col-xl-3">
                <div class="wk-card">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $presensiStats['hadir'] }}</div>
                            <div class="stat-label">Hadir Hari Ini</div>
                        </div>
                        <div class="stat-icon-box stat-icon-success-soft">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @php $persenHadir = $totalSiswa > 0 ? round(($presensiStats['hadir'] / $totalSiswa) * 100) : 0; @endphp
                        <span class="{{ $persenHadir >= 80 ? 'text-success' : ($persenHadir >= 50 ? 'text-warning' : 'text-danger') }}">
                            <i class="fas fa-chart-line me-1"></i>{{ $persenHadir }}% kehadiran
                        </span>
                        <i class="fas fa-clock opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Izin Pending -->
            <div class="col-6 col-xl-3">
                <div class="wk-card {{ $izinMenungguValidasi > 0 ? 'wk-card-warning-border' : '' }}">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $izinMenungguValidasi }}</div>
                            <div class="stat-label">Izin Pending</div>
                        </div>
                        <div class="stat-icon-box stat-icon-warning-soft">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($izinMenungguValidasi > 0)
                            <span class="text-warning fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>Perlu Validasi</span>
                        @else
                            <span class="text-success"><i class="fas fa-check me-1"></i>Semua Clear</span>
                        @endif
                        <i class="fas fa-bell opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Rapor Draft -->
            <div class="col-6 col-xl-3">
                <div class="wk-card">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $raporBelumSelesai }}</div>
                            <div class="stat-label">Rapor Draft</div>
                        </div>
                        <div class="stat-icon-box stat-icon-purple-soft">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($raporBelumSelesai > 0)
                            <span class="text-warning"><i class="fas fa-edit me-1"></i>Belum selesai</span>
                        @else
                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Semua selesai</span>
                        @endif
                        <i class="fas fa-file opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout Grid -->
        <div class="row g-4 mb-4">

            <!-- Left Column: Jadwal & Presensi -->
            <div class="col-lg-8 d-flex flex-column gap-4">

                <!-- Jadwal Pelajaran Hari Ini -->
                <div class="wk-card">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-calendar-day text-warning"></i> Jadwal Hari Ini
                            <span class="badge bg-label-primary ms-1 wk-day-badge">{{ now()->locale('id')->translatedFormat('l') }}</span>
                        </h5>
                        <a href="{{ route('wali.jadwal.index') }}" class="text-primary fw-semibold text-decoration-none wk-small-link">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @if($jadwalHariIni->count() > 0)
                        <ul class="jadwal-list">
                            @foreach($jadwalHariIni as $jadwal)
                                <li class="jadwal-item">
                                    <div class="jadwal-time">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </div>
                                    <div class="jadwal-dot"></div>
                                    <div class="jadwal-body">
                                        <div class="jadwal-mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                        <div class="jadwal-guru">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : 'Belum ditentukan' }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-coffee d-block"></i>
                            <div class="empty-state-title">Tidak Ada Jadwal</div>
                            <div class="empty-state-desc">Tidak ada pelajaran terjadwal untuk hari ini.</div>
                        </div>
                    @endif
                </div>

                <!-- Rekap Presensi Hari Ini -->
                <div class="wk-card">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-clipboard-list text-info"></i> Rekap Presensi
                        </h5>
                        <span class="text-muted wk-date-text">{{ now()->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="presensi-grid">
                        <div class="presensi-item">
                            <div class="presensi-number text-success">{{ $presensiStats['hadir'] }}</div>
                            <div class="presensi-label">Hadir</div>
                        </div>
                        <div class="presensi-item">
                            <div class="presensi-number text-warning">{{ $presensiStats['sakit'] }}</div>
                            <div class="presensi-label">Sakit</div>
                        </div>
                        <div class="presensi-item">
                            <div class="presensi-number text-primary">{{ $presensiStats['izin'] }}</div>
                            <div class="presensi-label">Izin</div>
                        </div>
                        <div class="presensi-item">
                            <div class="presensi-number text-danger">{{ $presensiStats['alpha'] }}</div>
                            <div class="presensi-label">Alpha</div>
                        </div>
                    </div>
                    @php $totalPresensi = array_sum($presensiStats); @endphp
                    @if($totalPresensi > 0 && $totalSiswa > 0)
                        <div class="px-4 pb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 progress-meta">
                                <span>Progress Input ({{ $totalPresensi }}/{{ $totalSiswa }})</span>
                                <span class="fw-bold">{{ round(($totalPresensi / $totalSiswa) * 100) }}%</span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-success progress-segment-start" role="progressbar" data-progress-width="{{ ($presensiStats['hadir'] / $totalSiswa) * 100 }}"></div>
                                <div class="progress-bar bg-warning" role="progressbar" data-progress-width="{{ ($presensiStats['sakit'] / $totalSiswa) * 100 }}"></div>
                                <div class="progress-bar bg-primary" role="progressbar" data-progress-width="{{ ($presensiStats['izin'] / $totalSiswa) * 100 }}"></div>
                                <div class="progress-bar bg-danger progress-segment-end" role="progressbar" data-progress-width="{{ ($presensiStats['alpha'] / $totalSiswa) * 100 }}"></div>
                            </div>
                        </div>
                    @elseif($totalPresensi == 0)
                        <div class="text-center pb-3">
                            <a href="{{ route('wali.presensi.index') }}" class="btn btn-sm btn-outline-primary px-4 presensi-action-btn">
                                <i class="fas fa-clipboard-check me-1"></i> Input Presensi Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Status Kelas -->
                @php
                    $persenHadir = $totalSiswa > 0 ? round(($presensiStats['hadir'] / $totalSiswa) * 100) : 0;
                    $persenRapor = $totalSiswa > 0 ? round((($totalSiswa - $raporBelumSelesai) / $totalSiswa) * 100) : 0;
                @endphp
                <div class="wk-card flex-grow-1">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-chart-bar text-purple"></i> Status Kelas
                        </h5>
                    </div>
                    <ul class="status-list">
                        <li class="status-item">
                            <div class="status-icon stat-icon-success-soft">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Kehadiran Hari Ini</span>
                                    <span class="status-value {{ $persenHadir >= 80 ? 'text-success' : ($persenHadir >= 50 ? 'text-warning' : 'text-danger') }}">{{ $persenHadir }}%</span>
                                </div>
                                <div class="progress progress-thinner">
                                    <div class="progress-bar progress-rounded {{ $persenHadir >= 80 ? 'bg-success' : ($persenHadir >= 50 ? 'bg-warning' : 'bg-danger') }}" data-progress-width="{{ $persenHadir }}"></div>
                                </div>
                            </div>
                        </li>
                        <li class="status-item">
                            <div class="status-icon stat-icon-purple-soft">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Penyelesaian Rapor</span>
                                    <span class="status-value {{ $persenRapor >= 100 ? 'text-success' : ($persenRapor >= 50 ? 'text-warning' : 'text-danger') }}">{{ $persenRapor }}%</span>
                                </div>
                                <div class="progress progress-thinner">
                                    <div class="progress-bar progress-rounded {{ $persenRapor >= 100 ? 'bg-success' : ($persenRapor >= 50 ? 'bg-warning' : 'bg-danger') }}" data-progress-width="{{ $persenRapor }}"></div>
                                </div>
                            </div>
                        </li>
                        <li class="status-item">
                            <div class="status-icon stat-icon-warning-soft">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Izin Menunggu</span>
                                    <span class="status-value {{ $izinMenungguValidasi == 0 ? 'text-success' : 'text-warning' }}">{{ $izinMenungguValidasi }} pengajuan</span>
                                </div>
                                <div class="progress progress-thinner">
                                    <div class="progress-bar progress-rounded {{ $izinMenungguValidasi == 0 ? 'bg-success' : 'bg-warning' }}" data-progress-width="{{ $izinMenungguValidasi > 0 ? 100 : 0 }}"></div>
                                </div>
                            </div>
                        </li>
                        <li class="status-item">
                            <div class="status-icon stat-icon-primary-soft">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="status-body">
                                <div class="status-header">
                                    <span class="status-label">Siswa Terdaftar</span>
                                    <span class="status-value text-primary">{{ $totalSiswa }} siswa</span>
                                </div>
                                <div class="progress progress-thinner">
                                    <div class="progress-bar bg-primary progress-rounded" data-progress-width="100"></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Right Column: Aksi Cepat -->
            <div class="col-lg-4 d-flex flex-column gap-4">

                <!-- Quick Links -->
                <div class="wk-card flex-grow-1">
                    <div class="wk-card-header">
                        <h5 class="wk-card-title">
                            <i class="fas fa-bolt text-warning"></i> Akses Cepat
                        </h5>
                    </div>
                    <div class="quick-links-grid">
                        <a href="{{ route('wali.presensi.index') }}" class="quick-link-item">
                            <i class="fas fa-clipboard-check text-primary"></i>
                            <span class="quick-link-text">Input<br>Presensi</span>
                        </a>
                        <a href="{{ route('wali.presensi.validasi-izin') }}" class="quick-link-item">
                            <div class="position-relative">
                                <i class="fas fa-check-circle text-warning"></i>
                                @if($izinMenungguValidasi > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm quick-link-badge">
                                        {{ $izinMenungguValidasi > 99 ? '99+' : $izinMenungguValidasi }}
                                    </span>
                                @endif
                            </div>
                            <span class="quick-link-text">Validasi<br>Izin</span>
                        </a>
                        <a href="{{ route('wali.nilai.index') }}" class="quick-link-item">
                            <i class="fas fa-chart-line text-info"></i>
                            <span class="quick-link-text">Lihat<br>Nilai</span>
                        </a>
                        <a href="{{ route('wali.rapor.index') }}" class="quick-link-item">
                            <div class="position-relative">
                                <i class="fas fa-file-alt text-success"></i>
                                @if($raporBelumSelesai > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning shadow-sm quick-link-badge quick-link-badge-warning">
                                        {{ $raporBelumSelesai }}
                                    </span>
                                @endif
                            </div>
                            <span class="quick-link-text">Kelola<br>Rapor</span>
                        </a>
                        <a href="{{ route('wali.presensi.rekap-harian') }}" class="quick-link-item">
                            <i class="fas fa-calendar-day text-purple"></i>
                            <span class="quick-link-text">Rekap<br>Harian</span>
                        </a>
                        <a href="{{ route('wali.presensi.riwayat') }}" class="quick-link-item">
                            <i class="fas fa-history text-secondary"></i>
                            <span class="quick-link-text">Riwayat<br>Presensi</span>
                        </a>
                        <a href="{{ route('wali.jadwal.index') }}" class="quick-link-item">
                            <i class="fas fa-calendar-week text-warning"></i>
                            <span class="quick-link-text">Jadwal<br>Pelajaran</span>
                        </a>
                        <a href="{{ route('wali.kenaikan-kelas.prediction') }}" class="quick-link-item">
                            <i class="fas fa-chart-bar text-danger"></i>
                            <span class="quick-link-text">Prediksi<br>Kenaikan</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    @endif

@endsection
