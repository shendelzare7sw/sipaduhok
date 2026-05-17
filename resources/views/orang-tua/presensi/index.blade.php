@extends('layouts.sneat')

@section('title', 'Presensi - ' . $siswa->nama_lengkap)
@section('page-title', 'Presensi')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .attendance-page {
        --parent-blue: #465fe8;
        --parent-ink: #25324a;
        --parent-muted: #6b7890;
        --parent-line: #dde4f0;
        --parent-soft: #f6f8fc;
    }

    .page-heading {
        background: #fff;
        border: 1px solid var(--parent-line);
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(37, 50, 74, .06);
        padding: 22px 24px;
    }

    .page-heading-title {
        color: var(--parent-ink);
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .student-panel,
    .history-panel,
    .empty-panel {
        background: #fff;
        border: 1px solid var(--parent-line);
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(37, 50, 74, .06);
    }

    .student-panel {
        padding: 24px;
        margin-bottom: 18px;
    }

    .student-avatar {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(70, 95, 232, .22);
        flex: 0 0 auto;
    }

    .student-avatar img,
    .student-avatar .avatar-initial {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-name {
        color: var(--parent-ink);
        font-size: 1.22rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .student-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        color: var(--parent-muted);
        font-size: .88rem;
        font-weight: 600;
    }

    .action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-attendance-primary {
        background: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 8px 18px rgba(245, 158, 11, .24);
    }

    .btn-attendance-primary:hover {
        background: #d97706;
        border-color: #d97706;
        color: #fff;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 18px;
    }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border: 1px solid var(--parent-line);
        border-radius: 8px;
        padding: 18px;
        min-height: 112px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 22px rgba(37, 50, 74, .05);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--stat-color);
    }

    .stat-title {
        color: var(--parent-muted);
        font-size: .75rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .stat-number {
        color: var(--parent-ink);
        display: block;
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1;
    }

    .stat-icon {
        align-items: center;
        background: color-mix(in srgb, var(--stat-color) 12%, white);
        border-radius: 8px;
        color: var(--stat-color);
        display: flex;
        height: 46px;
        justify-content: center;
        position: absolute;
        right: 16px;
        top: 16px;
        width: 46px;
    }

    .stat-green { --stat-color: #10b981; }
    .stat-orange { --stat-color: #f59e0b; }
    .stat-blue { --stat-color: #3b82f6; }
    .stat-red { --stat-color: #ef4444; }

    .history-panel-header {
        align-items: center;
        border-bottom: 1px solid var(--parent-line);
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: space-between;
        padding: 18px 20px;
    }

    .history-title {
        color: var(--parent-ink);
        font-size: 1rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .week-label {
        background: var(--parent-soft);
        border-bottom: 1px solid var(--parent-line);
        color: var(--parent-blue);
        font-size: .78rem;
        font-weight: 800;
        padding: 12px 20px;
        text-transform: uppercase;
    }

    .empty-panel {
        padding: 48px 24px;
        text-align: center;
    }

    .empty-icon {
        align-items: center;
        background: var(--parent-soft);
        border-radius: 8px;
        color: #a7b0bf;
        display: inline-flex;
        font-size: 2rem;
        height: 64px;
        justify-content: center;
        margin-bottom: 18px;
        width: 64px;
    }

    .table thead th {
        background: var(--parent-soft);
        color: var(--parent-muted);
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        border: none;
    }

    .table td {
        color: var(--parent-ink);
        vertical-align: middle;
    }
</style>
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
            <a href="{{ route('orang-tua.presensi.ajukan-izin', $siswa->id) }}" class="btn btn-attendance-primary">
                <i class="fas fa-file-medical me-1"></i>Ajukan Izin / Sakit
            </a>
            <a href="{{ route('orang-tua.dashboard') }}" class="btn btn-outline-secondary btn-sm">
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
                    <span class="avatar-initial bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="font-size: 1.9rem;">
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

    <!-- Header with Action Button -->
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
            <a href="{{ route('orang-tua.presensi.ajukan-izin', $siswa->id) }}"
               class="btn btn-attendance-primary">
                <i class="fas fa-file-medical me-1"></i>Ajukan Izin / Sakit
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
