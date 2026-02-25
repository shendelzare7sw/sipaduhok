@extends('layouts.sneat')

@section('title', 'Kelola Guru Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Guru Pengajar Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas . ' - ' . $kelas->tahunAjaran->nama_tahun_ajaran)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #14b8a6;
        }

        .breadcrumb .current {
            color: #111827;
            font-weight: 500;
        }

        .info-banner {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            border-radius: 16px;
            padding: 24px 32px;
            color: white;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-banner h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .info-banner-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .info-banner-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.9;
        }

        .info-banner-stats {
            display: flex;
            gap: 24px;
        }

        .info-banner-stat {
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            padding: 12px 20px;
            border-radius: 10px;
        }

        .info-banner-stat-value {
            font-size: 28px;
            font-weight: 700;
        }

        .info-banner-stat-label {
            font-size: 12px;
            opacity: 0.8;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 992px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h5 i {
            color: #14b8a6;
        }

        .card-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        .form-group select:focus {
            outline: none;
            border-color: #14b8a6;
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        }

        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
            border: none;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .btn-outline {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .btn-outline:hover {
            background: #f9fafb;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 12px 16px;
            background: #f9fafb;
            color: #4b5563;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 14px;
        }

        .table tr:hover td {
            background: #f9fafb;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-teal {
            background: #ccfbf1;
            color: #0d9488;
        }

        .guru-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .guru-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
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
        <div class="card" style="border: 1px solid #bfdbfe; background: #eff6ff; margin-bottom: 20px;">
            <div class="card-body" style="padding: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 20px; margin-top: 2px;"></i>
                    <div>
                        <strong style="color: #1e40af;">Penugasan Otomatis dari Jadwal Pelajaran</strong>
                        <p style="margin: 4px 0 8px; color: #1e3a5f; font-size: 13px;">
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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas->guruPengajar as $assignment)
                                    <tr>
                                        <td>
                                            <div class="guru-info">
                                                <div class="guru-avatar">
                                                    {{ strtoupper(substr($assignment->tenagaPendidik->nama_lengkap, 0, 1)) }}
                                                </div>
                                                <span>{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                            </div>
                                        </td>
                                        <td>
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
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalList as $jadwal)
                                        <tr>
                                            <td>{{ $jadwal->hari }}</td>
                                            <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                            <td>{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</td>
                                            <td>{{ $jadwal->guru->nama_lengkap ?? '-' }}</td>
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

        <div style="margin-top: 24px;">
            <a href="{{ route('waka.guru-pengajar.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
            </a>
        </div>
    </div>
@endsection