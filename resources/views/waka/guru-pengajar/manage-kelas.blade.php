@extends('layouts.sneat')

@section('title', 'Kelola Guru Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Guru Pengajar Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas . ' - ' . $kelas->tahunAjaran->nama_tahun_ajaran)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/waka/guru-pengajar/manage-kelas.css')
@endsection

@section('content')
    <div class="page-shell">
        <div class="breadcrumb">
            <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
            <span>/</span>
            <a href="{{ route('waka.guru-pengajar.index') }}">Data Guru Pengajar</a>
            <span>/</span>
            <span class="current">Kelas {{ $kelas->nama_kelas }}</span>
        </div>

        <div class="info-banner">
            <div>
                <h2>Kelas {{ $kelas->nama_kelas }}</h2>
                <div class="info-banner-meta">
                    <div class="info-banner-item">
                        <i class="fas fa-building"></i>
                        {{ $kelas->cabang->nama_cabang ?? '-' }}
                    </div>
                    <div class="info-banner-item">
                        <i class="fas fa-layer-group"></i>
                        {{ $kelas->jenjang }}
                    </div>
                    <div class="info-banner-item">
                        <i class="fas fa-calendar"></i>
                        {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                    </div>
                </div>
            </div>
            <div class="info-banner-stats">
                <div class="info-banner-stat">
                    <div class="info-banner-stat-value">{{ $kelas->guruPengajar->count() }}</div>
                    <div class="info-banner-stat-label">Guru Pengajar</div>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="card info-card">
            <div class="card-body compact-card-body">
                <div class="info-row">
                    <i class="fas fa-info-circle info-icon"></i>
                    <div>
                        <strong class="info-title">Penugasan Otomatis dari Jadwal Pelajaran</strong>
                        <p class="info-text">
                            Penugasan guru dikelola otomatis dari Jadwal Pelajaran. Untuk menambah atau mengubah, buat/edit jadwal.
                        </p>
                        <a href="{{ route('waka.jadwal-pelajaran.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-alt me-1"></i> Buka Jadwal Pelajaran
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Guru di Kelas Ini --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Guru di Kelas Ini</h5>
            </div>
            <div class="card-body">
                @if($kelas->guruPengajar->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile">
                            <thead>
                                <tr>
                                    <th class="mobile-card-head">Guru</th>
                                    <th data-label="Mata Pelajaran">Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas->guruPengajar as $assignment)
                                    <tr>
                                        <td class="mobile-card-head">
                                            <div class="guru-info">
                                                <div class="guru-avatar">
                                                    {{ strtoupper(substr($assignment->tenagaPendidik->nama_lengkap, 0, 1)) }}
                                                </div>
                                                <span>{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Mata Pelajaran">
                                            <span class="badge badge-teal">{{ $assignment->mataPelajaran->nama_mapel }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Jadwal Terkait --}}
                    @if($jadwalList->count() > 0)
                        <h6 class="mt-4 mb-2"><i class="fas fa-calendar-alt"></i> Jadwal di Kelas Ini</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-card-mobile">
                                <thead>
                                    <tr>
                                        <th class="mobile-card-head">Hari</th>
                                        <th data-label="Jam">Jam</th>
                                        <th data-label="Mata Pelajaran">Mata Pelajaran</th>
                                        <th data-label="Guru">Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalList as $jadwal)
                                        <tr>
                                            <td class="mobile-card-head">{{ $jadwal->hari }}</td>
                                            <td data-label="Jam">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                            <td data-label="Mata Pelajaran">{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</td>
                                            <td data-label="Guru">{{ $jadwal->guru->nama_lengkap ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p>Belum ada guru ditugaskan di kelas ini</p>
                        <small class="text-muted">Buat jadwal pelajaran untuk kelas ini untuk menambahkan penugasan.</small>
                    </div>
                @endif
            </div>
        </div>

        <div class="footer-actions">
            <a href="{{ route('waka.guru-pengajar.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
            </a>
        </div>
    </div>
@endsection
