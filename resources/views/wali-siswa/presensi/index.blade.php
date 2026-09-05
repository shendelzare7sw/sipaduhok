@extends('layouts.app')

@section('title', 'Presensi - ' . $siswa->nama_lengkap)
@section('page-title', 'Presensi')


@section('styles')
    @vite(['resources/css/wali-siswa/presensi/index.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y attendance-page">

    <!-- Page Header -->
    <div class="page-heading d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="page-heading-title">Presensi Kehadiran</div>
            <p class="text-muted mb-0">Pantau rekap kehadiran anak pada bulan berjalan.</p>
        </div>
        <div class="action-row">
            <a href="{{ route('wali-siswa.presensi.ajukan-izin', $siswa->id) }}" class="btn btn-attendance-primary">
                <i class="fas fa-file-medical me-1"></i>Ajukan Izin / Sakit
            </a>
            <a href="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" class="btn btn-attendance-soft">
                <i class="fas fa-calendar-alt me-1"></i>Riwayat Presensi
            </a>
            <a href="{{ route('wali-siswa.presensi.riwayat-izin', $siswa->id) }}" class="btn btn-attendance-secondary">
                <i class="fas fa-history me-1"></i>Riwayat Pengajuan
            </a>
            <a href="{{ route('wali-siswa.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="student-panel">
        <div class="d-flex align-items-center gap-3">
            <div class="student-avatar">
                @if($siswa->user && $siswa->user->foto_profil)
                    <img src="{{ asset('storage/' . $siswa->user->foto_profil) }}" alt="avatar">
                @elseif($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="avatar">
                @else
                    <span class="avatar-initial bg-primary text-white fw-bold d-flex align-items-center justify-content-center student-avatar-initial">
                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div>
                <div class="student-name">{{ $siswa->nama_lengkap }}</div>
                <div class="student-meta">
                    <span><i class="fas fa-id-card me-1"></i>NISN: {{ $siswa->nisn }}</span>
                    <span><i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                    <span><i class="fas fa-building me-1"></i>{{ $siswa->cabang->nama_cabang ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card stat-green">
            <div class="stat-title">Hari Hadir</div>
            <div class="stat-number">{{ $rekap['hadir'] }}</div>
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        </div>

        <div class="stat-card stat-orange">
            <div class="stat-title">Hari Sakit</div>
            <div class="stat-number">{{ $rekap['sakit'] }}</div>
            <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
        </div>

        <div class="stat-card stat-blue">
            <div class="stat-title">Hari Izin</div>
            <div class="stat-number">{{ $rekap['izin'] }}</div>
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        </div>

        <div class="stat-card stat-red">
            <div class="stat-title">Hari Alpha</div>
            <div class="stat-number">{{ $rekap['alpha'] }}</div>
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>

    <!-- History Header -->
    <div class="history-panel mb-3">
        <div class="history-panel-header">
            <div>
                <div class="history-title">
                    <i class="fas fa-calendar-check me-2 text-primary"></i>Riwayat Presensi Bulan Ini
                </div>
                <small class="text-muted">
                    Bulan {{ now()->translatedFormat('F Y') }}
                </small>
            </div>
            <a href="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}"
               class="btn btn-attendance-soft btn-sm">
                <i class="fas fa-calendar-alt me-1"></i>Lihat Semua Riwayat
            </a>
        </div>
    </div>

    <!-- Presensi List -->
    @forelse($presensi as $minggu => $dataList)
    <div class="history-panel mb-3">
        <div class="week-label">
            <i class="fas fa-calendar-week me-2"></i>Minggu ke-{{ $minggu }}
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Hari</th>
                        <th class="text-center">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataList as $item)
                    <tr>
                        <td class="ps-4 fw-bold text-dark align-middle">{{ $item->tanggal->format('d F Y') }}</td>
                        <td class="align-middle text-muted">{{ ucfirst($item->tanggal->locale('id')->dayName) }}</td>
                        <td class="text-center align-middle">
                            @php
                                $statusLabel = strtoupper($item->status);
                                $badgeClass = 'secondary';
                                
                                if ($item->status === 'hadir') {
                                    $badgeClass = 'success';
                                } elseif ($item->status === 'sakit' || $item->status === 'izin') {
                                    if ($item->status_validasi === 'disetujui') {
                                        $badgeClass = ($item->status === 'sakit') ? 'warning' : 'info';
                                    } elseif ($item->status_validasi === 'ditolak') {
                                        $statusLabel = 'DITOLAK';
                                        $badgeClass = 'danger';
                                    } else {
                                        $statusLabel = 'MENUNGGU VALIDASI';
                                        $badgeClass = 'warning';
                                    }
                                } elseif ($item->status === 'alpha') {
                                    if ($item->status_validasi === 'ditolak') {
                                        $statusLabel = 'DITOLAK';
                                    }
                                    $badgeClass = 'danger';
                                }
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-bold bg-{{ $badgeClass }} shadow-sm">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="align-middle small text-muted">
                            <div>{{ $item->keterangan ?? 'Tidak ada catatan' }}</div>
                            @if($item->bukti_file)
                                <a href="{{ asset('storage/' . $item->bukti_file) }}" target="_blank" class="text-primary fw-bold text-decoration-none mt-1 d-inline-block">
                                    <i class="fas fa-paperclip me-1"></i>Lihat Bukti
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="empty-panel">
        <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
        <h5 class="text-muted fw-bold">Belum Ada Data Presensi Bulan Ini</h5>
        <p class="text-muted mb-0 small">Data presensi akan diperbarui otomatis oleh wali kelas setelah pembelajaran.</p>
    </div>
    @endforelse

</div>
@endsection
