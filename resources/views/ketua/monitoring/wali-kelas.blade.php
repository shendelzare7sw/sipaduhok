{{-- resources/views/ketua/monitoring/wali-kelas.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Monitoring Wali Kelas')

@section('page-title', 'Monitoring Data Wali Kelas')
@section('page-subtitle', 'Lihat progress penyelesaian rapor per wali kelas')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
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

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Progress Bar */
        .progress-bar-container {
            width: 100%;
            height: 24px;
            background: #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: white;
            transition: width 0.3s ease;
        }

        .progress-100 {
            background: #10b981;
        }

        .progress-75 {
            background: #3b82f6;
        }

        .progress-50 {
            background: #f59e0b;
        }

        .progress-25 {
            background: #ef4444;
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
                <h5><i class="fas fa-user-tie"></i> Monitoring Wali Kelas & Progress Rapor</h5>
                <div class="filter-group" style="display: flex; gap: 12px; align-items: center;">
                    <input type="text" id="searchWali" class="form-control" placeholder="Cari nama wali kelas..."
                        style="width: 200px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <select id="filterProgress" class="form-control"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Progress</option>
                        <option value="100">Selesai (100%)</option>
                        <option value="below">Belum Selesai (&lt;100%)</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                @if($waliKelas->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Wali Kelas</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Total Siswa</th>
                                <th>Rapor Selesai</th>
                                <th style="width: 250px;">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($waliKelas as $wali)
                                <tr>
                                    <td><strong>{{ $wali->nama_lengkap }}</strong></td>
                                    <td>
                                        <span class="badge badge-info">{{ $wali->kelas_info->nama_kelas }}</span>
                                    </td>
                                    <td>{{ $wali->kelas_info->tahunAjaran->nama_tahun_ajaran }}</td>
                                    <td style="text-align: center;">
                                        <strong style="font-size: 16px;">{{ $wali->total_siswa }}</strong>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong style="font-size: 16px; color: #10b981;">{{ $wali->rapor_selesai }}</strong>
                                    </td>
                                    <td>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar-fill 
                                                @if($wali->progress_rapor == 100) progress-100
                                                @elseif($wali->progress_rapor >= 75) progress-75
                                                @elseif($wali->progress_rapor >= 50) progress-50
                                                @else progress-25
                                                @endif" style="width: {{ $wali->progress_rapor }}%">
                                                {{ number_format($wali->progress_rapor, 1) }}%
                                            </div>
                                        </div>
                                        <small style="color: #6b7280; margin-top: 4px; display: block;">
                                            @if($wali->progress_rapor == 100)
                                                <i class="fas fa-check-circle"></i> Semua rapor sudah selesai
                                            @elseif($wali->progress_rapor >= 75)
                                                <i class="fas fa-file-alt"></i> Hampir selesai
                                            @elseif($wali->progress_rapor >= 50)
                                                ⏳ Sedang proses
                                            @else
                                                <i class="fas fa-exclamation-triangle"></i> Perlu ditindaklanjuti
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-tie"></i>
                        <p style="font-weight: 500; font-size: 16px; margin-bottom: 8px;">Belum Ada Wali Kelas</p>
                        <small>Data wali kelas akan muncul setelah ditugaskan oleh Admin</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Info Card --}}
        <div class="card" style="background: #eff6ff; border: 1px solid #bfdbfe;">
            <div class="card-body" style="padding: 20px;">
                <div style="display: flex; align-items: start; gap: 16px;">
                    <div style="font-size: 32px;">ℹ️</div>
                    <div>
                        <strong style="color: #1e40af; display: block; margin-bottom: 8px;">Informasi Progress
                            Rapor</strong>
                        <ul style="color: #1e40af; margin: 0; padding-left: 20px; line-height: 1.8;">
                            <li>Progress dihitung berdasarkan jumlah rapor yang sudah diterbitkan dibanding total siswa</li>
                            <li>Rapor dengan status "Diterbitkan" dihitung sebagai selesai</li>
                            <li>Warna hijau (100%) = Semua rapor selesai</li>
                            <li>Warna biru (75-99%) = Hampir selesai</li>
                            <li>Warna kuning (50-74%) = Sedang proses</li>
                            <li>Warna merah (<50%)=Perlu perhatian khusus</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter functionality for wali kelas
        const searchWali = document.getElementById('searchWali');
        const filterProgress = document.getElementById('filterProgress');

        function filterTable() {
            const searchTerm = searchWali.value.toLowerCase();
            const progressFilter = filterProgress.value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let showRow = true;
                const namaWali = row.cells[0].textContent.toLowerCase();
                const progressBar = row.cells[5].querySelector('.progress-bar-fill');
                const progressValue = progressBar ? parseFloat(progressBar.textContent) : 0;

                // Search filter
                if (searchTerm && !namaWali.includes(searchTerm)) {
                    showRow = false;
                }

                // Progress filter
                if (progressFilter === '100' && progressValue < 100) {
                    showRow = false;
                } else if (progressFilter === 'below' && progressValue >= 100) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        searchWali.addEventListener('input', filterTable);
        filterProgress.addEventListener('change', filterTable);
    </script>
@endsection