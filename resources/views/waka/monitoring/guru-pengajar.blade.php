{{-- resources/views/waka/monitoring/guru-pengajar.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Monitoring Guru Pengajar')

@section('page-title', 'Monitoring Data Guru Pengajar')
@section('page-subtitle', 'Lihat progress penilaian dan pembuatan soal ujian')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .card-body {
            padding: 24px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: #f9fafb;
        }

        .table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Stats Mini dalam Tabel */
        .stat-mini-inline {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-mini-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .stat-mini-row:nth-child(even) {
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .stat-label-small {
            font-size: 11px;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-value-small {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
        }

        /* Progress Circle */
        .progress-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .progress-100 {
            background: #d1fae5;
            color: #065f46;
        }

        .progress-100::before {
            border-color: #10b981;
        }

        .progress-75 {
            background: #dbeafe;
            color: #1e40af;
        }

        .progress-75::before {
            border-color: #3b82f6;
        }

        .progress-50 {
            background: #fef3c7;
            color: #92400e;
        }

        .progress-50::before {
            border-color: #f59e0b;
        }

        .progress-25 {
            background: #fee2e2;
            color: #991b1b;
        }

        .progress-25::before {
            border-color: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <h5><i class="fas fa-chalkboard-teacher"></i> Monitoring Guru Pengajar & Progress Tugas</h5>
                
                <form action="{{ route('waka.monitoring.guru-pengajar') }}" method="GET" class="filter-group" style="display: flex; gap: 12px; align-items: center;">
                    <!-- Search -->
                    <input type="text" name="search" class="form-control" placeholder="Cari nama guru..." value="{{ request('search') }}"
                        style="width: 200px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">

                    <!-- Filter Cabang -->
                    <select name="cabang_id" class="form-control" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Cabang</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>

                    <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
                    
                    @if(request()->anyFilled(['search', 'cabang_id']))
                        <a href="{{ route('waka.monitoring.guru-pengajar') }}" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Reset</a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                @if($guruPengajar->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Guru</th>
                                <th>Kelas & Mata Pelajaran</th>
                                <th style="width: 200px;">Status Penilaian</th>
                                <th style="text-align: center;">Soal Ujian</th>
                                <th style="text-align: center; width: 100px;">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guruPengajar as $guru)
                                <tr>
                                    <td><strong>{{ $guru->nama_lengkap }}</strong></td>
                                    <td>
                                        @foreach($guru->guruKelas as $gk)
                                            <span class="badge badge-info" style="margin: 2px;">
                                                {{ $gk->kelas->nama_kelas }} - {{ $gk->mataPelajaran->nama_mapel }}
                                            </span>
                                            @if(!$loop->last)<br>@endif
                                        @endforeach
                                        <br>
                                        <small class="text-muted">
                                            {{ $guru->guruKelas->first()?->kelas->cabang->nama_cabang ?? '' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="stat-mini-inline">
                                            <div class="stat-mini-row">
                                                <span class="stat-label-small"><i class="fas fa-check-circle"></i> Sudah
                                                    Dinilai</span>
                                                <span class="stat-value-small"
                                                    style="color: #10b981;">{{ $guru->nilai_sudah_diisi }}</span>
                                            </div>
                                            <div class="stat-mini-row">
                                                <span class="stat-label-small">⏳ Belum Dinilai</span>
                                                <span class="stat-value-small"
                                                    style="color: #ef4444;">{{ $guru->total_nilai_harus_diisi }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong style="font-size: 24px; color: #3b82f6;">{{ $guru->soal_ujian_dibuat }}</strong>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">Soal dibuat</div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="progress-circle 
                                            @if($guru->progress_nilai == 100) progress-100
                                            @elseif($guru->progress_nilai >= 75) progress-75
                                            @elseif($guru->progress_nilai >= 50) progress-50
                                            @else progress-25
                                            @endif">
                                            {{ number_format($guru->progress_nilai, 0) }}%
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $guruPengajar->withQueryString()->links() }}
                </div>

                @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p style="font-weight: 500; font-size: 16px; margin-bottom: 8px;">Tidak Ada Data Guru Pengajar</p>
                        <small>Data guru pengajar akan muncul setelah ditugaskan oleh Admin</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Info Card --}}
        <div class="card" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
            <div class="card-body" style="padding: 20px;">
                <div style="display: flex; align-items: start; gap: 16px;">
                    <div style="font-size: 32px;"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <strong style="color: #166534; display: block; margin-bottom: 8px;">Informasi Monitoring
                            Guru</strong>
                        <ul style="color: #166534; margin: 0; padding-left: 20px; line-height: 1.8;">
                            <li><strong>Progress Nilai</strong>: Dihitung dari nilai yang sudah diisi vs yang belum</li>
                            <li><strong>Soal Ujian</strong>: Total soal ujian (Harian, UTS, UAS) yang sudah dibuat</li>
                            <li>Progress 100% = Semua nilai sudah diisi</li>
                            <li>Progress <50%=Perlu follow-up ke guru terkait</li>
                        </ul>
                </div>
            </div>
        </div>
    </div>
@endsection