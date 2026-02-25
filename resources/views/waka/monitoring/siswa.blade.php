{{-- resources/views/waka/monitoring/siswa.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Monitoring Siswa')

@section('page-title', 'Monitoring Data Siswa')
@section('page-subtitle', 'Lihat status tugas dan tagihan siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

/* Mini Status Box */
.status-box {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.status-lunas {
    background: #d1fae5;
    color: #065f46;
}

.status-belum-lunas {
    background: #fef3c7;
    color: #92400e;
}

/* Progress Bar Mini */
.progress-mini {
    width: 100px;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
}

.progress-mini-fill {
    height: 100%;
    transition: width 0.3s;
}

.progress-high { background: #10b981; }
.progress-medium { background: #f59e0b; }
.progress-low { background: #ef4444; }

.filter-group {
    display: flex;
    gap: 12px;
    align-items: center;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
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
        <div class="card-header">
            <h5><i class="fas fa-user-graduate"></i> Monitoring Siswa - Tugas & Keuangan</h5>
            
            <form action="{{ route('waka.monitoring.siswa') }}" method="GET" class="filter-group">
                <!-- Search -->
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Siswa..." value="{{ request('search') }}" style="width: 200px;">

                <!-- Filter Kelas -->
                <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>

                <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
                
                @if(request()->anyFilled(['search', 'kelas_id']))
                    <a href="{{ route('waka.monitoring.siswa') }}" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Reset</a>
                @endif
            </form>
        </div>
        <div class="card-body">
            @if($siswa->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th style="text-align: center;">Tugas Selesai</th>
                            <th style="text-align: center;">Progress</th>
                            <th style="text-align: right;">Total Tagihan</th>
                            <th style="text-align: right;">Terbayar</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa as $s)
                        <tr>
                            <td><strong>{{ $s->nama_lengkap }}</strong></td>
                            <td>
                                @if($s->kelas)
                                    <span class="badge badge-info">{{ $s->kelas->nama_kelas }}</span>
                                    <br><small class="text-muted">{{ $s->kelas->cabang->nama_cabang ?? '-' }}</small>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <strong style="font-size: 16px;">{{ $s->tugas_selesai }}</strong> / {{ $s->total_tugas }}
                            </td>
                            <td style="text-align: center;">
                                <div class="progress-mini">
                                    <div class="progress-mini-fill 
                                        @if($s->progress_tugas >= 75) progress-high
                                        @elseif($s->progress_tugas >= 50) progress-medium
                                        @else progress-low
                                        @endif" 
                                        style="width: {{ $s->progress_tugas }}%">
                                    </div>
                                </div>
                                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                    {{ number_format($s->progress_tugas, 0) }}%
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <strong>Rp {{ number_format($s->total_tagihan, 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: right;">
                                <strong style="color: #10b981;">Rp {{ number_format($s->total_bayar, 0, ',', '.') }}</strong>
                            </td>
                            <td style="text-align: center;">
                                @if($s->status_bayar === 'lunas')
                                    <span class="status-box status-lunas"><i class="fas fa-check-circle"></i> Lunas</span>
                                @else
                                    <span class="status-box status-belum-lunas">
                                        <i class="fas fa-exclamation-triangle"></i> Sisa: Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $siswa->withQueryString()->links() }}
            </div>

            @else
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <p style="font-weight: 500; font-size: 16px; margin-bottom: 8px;">Tidak Ada Data Siswa Ditemukan</p>
                <small>Coba ubah filter pencarian Anda</small>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection