@extends('layouts.sneat')

@section('title', 'Detail Guru Pengajar - ' . $guruPengajar->nama_lengkap)

@section('page-title', 'Kelola Penugasan Guru')
@section('page-subtitle', $guruPengajar->nama_lengkap)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
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

        .breadcrumb span {
            color: #9ca3af;
        }

        .breadcrumb .current {
            color: #111827;
            font-weight: 500;
        }

        .header-card {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            border-radius: 20px;
            padding: 32px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .header-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
        }

        .header-text h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .header-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .header-stats {
                grid-template-columns: 1fr;
            }
        }

        .header-stat {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 16px 20px;
            backdrop-filter: blur(10px);
        }

        .header-stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .header-stat-label {
            font-size: 13px;
            opacity: 0.9;
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

        .btn-white {
            background: white;
            color: #0d9488;
        }

        .btn-white:hover {
            background: #f0fdfa;
        }

        .btn-white-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-white-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
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

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            border: none;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
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

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        @media (max-width: 992px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .info-grid {
            display: grid;
            gap: 16px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 600;
        }

        .info-value {
            font-size: 15px;
            color: #111827;
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

        .badge-purple {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .badge-blue {
            background: #e0f2fe;
            color: #0284c7;
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

        .filter-tahun {
            margin-bottom: 20px;
        }

        .filter-tahun select {
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
            <span>/</span>
            <a href="{{ route('admin.guru-pengajar.index') }}">Data Guru Pengajar</a>
            <span>/</span>
            <span class="current">{{ $guruPengajar->nama_lengkap }}</span>
        </div>

        <div class="header-card">
            <div class="header-content">
                <div class="header-top">
                    <div class="header-info">
                        <div class="header-avatar">{{ strtoupper(substr($guruPengajar->nama_lengkap, 0, 1)) }}</div>
                        <div class="header-text">
                            <h1>{{ $guruPengajar->nama_lengkap }}</h1>
                            <div class="header-meta">
                                <span class="header-badge">
                                    <i class="fas fa-id-badge"></i> {{ $guruPengajar->nip ?? 'NIP: -' }}
                                </span>
                                <span class="header-badge">
                                    <i class="fas fa-user-tag"></i>
                                    {{ ucfirst(str_replace('_', ' ', $guruPengajar->user->role ?? '-')) }}
                                </span>
                                <span class="header-badge">
                                    <i class="fas fa-circle"
                                        style="font-size: 8px; color: {{ $guruPengajar->user->is_active ? '#4ade80' : '#f87171' }}"></i>
                                    {{ $guruPengajar->user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.guru-pengajar.index') }}" class="btn btn-white-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="header-stats">
                    <div class="header-stat">
                        <div class="header-stat-value">{{ $stats['totalKelas'] }}</div>
                        <div class="header-stat-label">Kelas Diajar</div>
                    </div>
                    <div class="header-stat">
                        <div class="header-stat-value">{{ $stats['totalMapel'] }}</div>
                        <div class="header-stat-label">Mata Pelajaran</div>
                    </div>
                    <div class="header-stat">
                        <div class="header-stat-value">{{ $stats['totalPenugasan'] }}</div>
                        <div class="header-stat-label">Total Penugasan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-2">
            {{-- Info Guru --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Informasi Guru</h5>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value">{{ $guruPengajar->nama_lengkap }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIP</span>
                            <span class="info-value" style="font-family: monospace;">{{ $guruPengajar->nip ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $guruPengajar->user->email ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telepon</span>
                            <span class="info-value">{{ $guruPengajar->telepon ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Pendidikan Terakhir</span>
                            <span class="info-value">{{ $guruPengajar->pendidikan_terakhir ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Box: Penugasan Otomatis dari Jadwal --}}
            <div class="card" style="border: 1px solid #bfdbfe; background: #eff6ff;">
                <div class="card-body" style="padding: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 20px; margin-top: 2px;"></i>
                        <div>
                            <strong style="color: #1e40af;">Penugasan Otomatis dari Jadwal Pelajaran</strong>
                            <p style="margin: 4px 0 8px; color: #1e3a5f; font-size: 13px;">
                                Penugasan guru ke kelas dan mata pelajaran dikelola otomatis dari Jadwal Pelajaran.
                                Untuk menambah atau mengubah penugasan, buat atau edit jadwal di menu Jadwal Pelajaran.
                            </p>
                            <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar-alt me-1"></i> Buka Jadwal Pelajaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Penugasan --}}
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h5><i class="fas fa-tasks"></i> Daftar Penugasan</h5>
                <form action="" method="GET" style="display: flex; gap: 10px;">
                    <select name="tahun_ajaran_id" onchange="this.form.submit()"
                        style="padding: 8px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                @if($guruPengajar->guruKelas->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kelas</th>
                                    <th>Jenjang</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Cabang</th>
                                    <th>Tahun Ajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guruPengajar->guruKelas as $index => $assignment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $assignment->kelas->nama_kelas }}</strong></td>
                                        <td><span class="badge badge-blue">{{ $assignment->kelas->jenjang }}</span></td>
                                        <td><span class="badge badge-teal">{{ $assignment->mataPelajaran->nama_mapel }}</span></td>
                                        <td>{{ $assignment->kelas->cabang->nama_cabang ?? '-' }}</td>
                                        <td>{{ $assignment->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Jadwal Terkait --}}
                    @if($jadwalList->count() > 0)
                        <h6 class="mt-4 mb-2"><i class="fas fa-calendar-alt"></i> Jadwal Mengajar</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalList as $jadwal)
                                        <tr>
                                            <td>{{ $jadwal->hari }}</td>
                                            <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                            <td>{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</td>
                                            <td>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Belum ada penugasan untuk guru ini</p>
                        <small class="text-muted">Buat jadwal pelajaran dengan guru ini untuk menambahkan penugasan secara otomatis.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection