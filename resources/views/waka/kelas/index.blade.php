@extends('layouts.sneat')

@section('title', 'Data Kelas')

@section('page-title', 'Data Kelas')
@section('page-subtitle')
Kelola data kelas {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Stats Cards */
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 38px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1.2;
}

.stat-desc {
    font-size: 13px;
    opacity: 0.8;
}

.stat-icon-bg {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 70px;
    opacity: 0.15;
    z-index: 1;
}

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-gradient-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.bg-gradient-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

/* Grid System */
.row { display: flex; flex-wrap: wrap; margin: -12px; }
.col-md-3 { flex: 0 0 25%; max-width: 25%; padding: 12px; }
.col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; padding: 12px; }
.col-12 { flex: 0 0 100%; max-width: 100%; padding: 12px; }

@media (max-width: 992px) {
    .col-md-3, .col-md-4 { flex: 0 0 50%; max-width: 50%; }
}

@media (max-width: 576px) {
    .col-md-3, .col-md-4 { flex: 0 0 100%; max-width: 100%; }
}

/* Card */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
    border: none;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.card-body {
    padding: 24px;
}

/* Filter Section */
.filter-section {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 24px;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 10px 16px 10px 42px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    width: 220px;
    transition: all 0.3s;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.filter-select {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    min-width: 160px;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
}

/* Table */
.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 14px 16px;
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    font-size: 14px;
    vertical-align: middle;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover td {
    background: #f9fafb;
}

/* Badges */
.badge {
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-info { background: #e0f2fe; color: #075985; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }
.badge-pink { background: #fce7f3; color: #be185d; }
.badge-teal { background: #ccfbf1; color: #0d9488; }

/* Jenjang Badges */
.badge-kb { background: #fef3c7; color: #92400e; }
.badge-tka { background: #fed7aa; color: #9a3412; }
.badge-tkb { background: #fecaca; color: #991b1b; }
.badge-sd { background: #dcfce7; color: #166534; }
.badge-smp { background: #e0f2fe; color: #075985; }
.badge-sma { background: #f3e8ff; color: #7c3aed; }

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 8px;
}

.btn-light-primary { background: #eff6ff; color: #3b82f6; border: none; }
.btn-light-primary:hover { background: #dbeafe; }

.btn-light-warning { background: #fffbeb; color: #d97706; border: none; }
.btn-light-warning:hover { background: #fef3c7; }

.btn-light-danger { background: #fef2f2; color: #dc2626; border: none; }
.btn-light-danger:hover { background: #fee2e2; }

.btn-light-success { background: #f0fdf4; color: #16a34a; border: none; }
.btn-light-success:hover { background: #dcfce7; }

.btn-light-purple { background: #faf5ff; color: #7c3aed; border: none; }
.btn-light-purple:hover { background: #f3e8ff; }

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

/* Kelas Info */
.kelas-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.kelas-nama {
    font-weight: 600;
    color: #111827;
    font-size: 15px;
}

.kelas-kode {
    font-size: 11px;
    color: #6b7280;
    font-family: 'Monaco', 'Consolas', monospace;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
    width: fit-content;
}

/* Wali Kelas */
.wali-kelas-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.wali-avatar {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
    flex-shrink: 0;
}

.wali-name {
    font-weight: 500;
    color: #111827;
}

/* Kuota Progress */
.kuota-progress {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.kuota-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    width: 100px;
}

.kuota-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s;
}

.kuota-bar-fill.low { background: #10b981; }
.kuota-bar-fill.medium { background: #f59e0b; }
.kuota-bar-fill.high { background: #ef4444; }

.kuota-text {
    font-size: 12px;
    color: #6b7280;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 18px;
    color: #6b7280;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
    margin-bottom: 24px;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    flex-wrap: wrap;
    gap: 16px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-dialog {
    width: 90%;
    max-width: 450px;
    margin: auto;
}

.modal-content {
    background: white;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    overflow: hidden;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);
}

.modal-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #dc2626;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-body {
    padding: 24px;
    text-align: center;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #f9fafb;
}

.btn-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
}

.btn-secondary {
    background: #6b7280;
    color: white;
    padding: 10px 20px;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 10px 20px;
}

/* Print Button */
.btn-print {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
}

.btn-print:hover {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
}

/* ── Mobile Responsive ── */
@media (max-width: 767.98px) {
    /* Card header: wrap buttons */
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .card-header > div:last-child {
        flex-wrap: wrap !important;
        width: 100%;
    }
    .card-header > div:last-child .btn {
        flex: 1 1 auto;
        min-width: 120px;
        justify-content: center;
        font-size: 13px;
        padding: 8px 12px;
    }

    /* Filter: stack vertically */
    .filter-section {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .filter-section .search-box {
        width: 100%;
    }
    .filter-section .search-box input {
        width: 100% !important;
    }
    .filter-section .filter-select,
    .filter-section .btn {
        width: 100% !important;
    }

    /* Stat cards */
    .stat-card { padding: 16px; }
    .stat-number { font-size: 28px; }

    /* Table → Card per row */
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        position: relative;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
    }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f3f4f6 !important;
        min-height: 44px;
        font-size: 13px;
    }
    .table-card-mobile tbody td.mobile-card-head {
        background: #f8fafc;
        padding: 12px 14px;
        border-bottom: 2px solid #e5e7eb !important;
        justify-content: flex-start;
        gap: 10px;
        font-size: 14px;
        font-weight: 600;
    }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        padding-right: 10px;
        min-width: 75px;
    }
    .table-card-mobile tbody td.mobile-card-full {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    .table-card-mobile tbody td.mobile-card-full::before { min-width: unset; }
    .table-card-mobile tbody td.mobile-card-actions {
        border-bottom: none !important;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 6px;
        padding: 10px 14px;
    }
    /* Kuota bar: adapt on mobile */
    .kuota-bar { width: 100%; max-width: 160px; }
    /* Action buttons */
    .action-buttons { justify-content: flex-end; }
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    {{-- Stats Section --}}
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-md-3">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Kelas</div>
                    <div class="stat-number">{{ $stats['totalKelas'] }}</div>
                    <div class="stat-desc">{{ $currentTahunAjaran ? $currentTahunAjaran->nama_tahun_ajaran : 'Semua Tahun' }}</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Total Siswa</div>
                    <div class="stat-number">{{ $stats['totalSiswa'] }}</div>
                    <div class="stat-desc">Siswa aktif terdaftar</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Ada Wali Kelas</div>
                    <div class="stat-number">{{ $stats['kelasWithWali'] }}</div>
                    <div class="stat-desc">Kelas sudah ada wali</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Belum Ada Wali</div>
                    <div class="stat-number">{{ $stats['kelasWithoutWali'] }}</div>
                    <div class="stat-desc">Perlu ditunjuk wali</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h5><i class="fas fa-chalkboard" style="color: #3b82f6; margin-right: 10px;"></i>Daftar Kelas</h5>
                <small style="color: #6b7280;">Kelola data kelas per tahun ajaran</small>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('waka.kelas.import') }}" class="btn btn-outline" style="background: #dcfce7; border-color: #86efac; color: #166534;">
                    <i class="fas fa-file-import"></i> Import Excel
                </a>
                <a href="{{ route('waka.kelas.print', request()->query()) }}" class="btn btn-print" target="_blank">
                    <i class="fas fa-print"></i> Cetak
                </a>
                <a href="{{ route('waka.kelas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </a>
            </div>
        </div>
        
        <div class="card-body">
            {{-- Filter Section --}}
            <form action="{{ route('waka.kelas.index') }}" method="GET">
                <div class="filter-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Cari kelas..." value="{{ request('search') }}">
                    </div>
                    
                    <select name="tahun_ajaran_id" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="jenjang" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Jenjang</option>
                        @foreach($jenjangs as $j)
                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="btn btn-outline">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'jenjang']) || (request('tahun_ajaran_id') && request('tahun_ajaran_id') != $currentTahunAjaran?->id))
                        <a href="{{ route('waka.kelas.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($kelas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jenjang</th>
                                <th>Cabang</th>
                                <th>Wali Kelas</th>
                                <th>Siswa / Kuota</th>
                                <th>Tahun Ajaran</th>
                                <th style="text-align: center; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas as $k)
                            <tr>
                                <td class="mobile-card-head">
                                    <div class="kelas-info">
                                        <span class="kelas-nama">{{ $k->nama_kelas }}</span>
                                        <span class="kelas-kode">{{ $k->kode_kelas }}</span>
                                    </div>
                                </td>
                                <td data-label="Jenjang">
                                    @php
                                        $jenjangClass = [
                                            'KB' => 'badge-kb',
                                            'TKA' => 'badge-tka',
                                            'TKB' => 'badge-tkb',
                                            'SD' => 'badge-sd',
                                            'SMP' => 'badge-smp',
                                            'SMA' => 'badge-sma',
                                        ][$k->jenjang] ?? 'badge-info';
                                    @endphp
                                    <span class="badge {{ $jenjangClass }}">{{ $k->jenjang }}</span>
                                </td>
                                <td data-label="Cabang">
                                    <span style="color: #6b7280;">{{ $k->cabang->nama_cabang ?? '-' }}</span>
                                </td>
                                <td data-label="Wali Kelas" class="mobile-card-full">
                                    @if($k->waliKelasAssignments->count() > 0)
                                        @foreach($k->waliKelasAssignments as $assignment)
                                            <div class="wali-kelas-info" style="margin-bottom: 4px;">
                                                <div class="wali-avatar">{{ strtoupper(substr($assignment->tenagaPendidik->nama_lengkap, 0, 1)) }}</div>
                                                <span class="wali-name">{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation-circle"></i> Belum ditunjuk
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Siswa">
                                    @php
                                        $percentage = $k->kuota_siswa > 0 ? ($k->siswa_count / $k->kuota_siswa) * 100 : 0;
                                        $barClass = $percentage < 50 ? 'low' : ($percentage < 80 ? 'medium' : 'high');
                                    @endphp
                                    <div class="kuota-progress">
                                        <div class="kuota-bar">
                                            <div class="kuota-bar-fill {{ $barClass }}" style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                        <span class="kuota-text">{{ $k->siswa_count }} / {{ $k->kuota_siswa }} siswa</span>
                                    </div>
                                </td>
                                <td data-label="T. Ajaran">
                                    <span class="badge badge-info">{{ $k->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('waka.kelas.show', $k) }}" class="btn btn-icon btn-light-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('waka.kelas.manage-siswa', $k) }}" class="btn btn-icon btn-light-success" title="Kelola Siswa">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="{{ route('waka.kelas.edit', $k) }}" class="btn btn-icon btn-light-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-light-danger" title="Hapus" onclick="confirmDelete({{ $k->id }}, '{{ addslashes($k->nama_kelas) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($kelas->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Menampilkan {{ $kelas->firstItem() }} - {{ $kelas->lastItem() }} dari {{ $kelas->total() }} kelas
                        </div>
                        <div>
                            {{ $kelas->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-chalkboard"></i>
                    <h3>Belum Ada Data Kelas</h3>
                    <p>Silakan tambahkan data kelas untuk tahun ajaran ini.</p>
                    <a href="{{ route('waka.kelas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kelas Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Modal (Single Reusable) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden;">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 16px; background: rgba(0,0,0,0.05); border: none; color: #6b7280; transition: all 0.2s; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; z-index: 10;" onmouseover="this.style.background='rgba(0,0,0,0.1)'; this.style.color='#1f2937'" onmouseout="this.style.background='rgba(0,0,0,0.05)'; this.style.color='#6b7280'">
                <i class="fas fa-times" style="font-size: 16px;"></i>
            </button>
            <div class="modal-body" style="padding: 40px 30px 30px; text-align: center;">
                <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 36px; color: #ef4444;"></i>
                </div>
                
                <h4 style="font-weight: 700; color: #111827; margin-bottom: 12px;">Konfirmasi Hapus</h4>
                
                <p style="color: #4b5563; font-size: 15px; margin-bottom: 24px; line-height: 1.6;">
                    Apakah Anda yakin ingin menghapus kelas:<br>
                    <strong style="color: #111827; font-size: 18px;" id="deleteKelasName"></strong>?
                </p>
                
                <div style="background: #fef2f2; border: 1px dashed #fca5a5; border-radius: 12px; padding: 16px; margin-bottom: 28px; text-align: left; display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 20px; margin-top: 2px;"></i>
                    <p style="color: #b91c1c; font-size: 13px; margin: 0; line-height: 1.5; font-weight: 500;">
                        Perhatian: Data kelas yang dihapus beserta seluruh data terkait di dalamnya tidak dapat dikembalikan.
                    </p>
                </div>
                
                <div style="display: flex; gap: 16px;">
                    <button type="button" class="btn" data-bs-dismiss="modal" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; background: #f1f5f9; color: #475569; border: none; transition: all 0.2s; justify-content: center;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">
                        Batal
                    </button>
                    <form id="deleteForm" method="POST" style="flex: 1; margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%; padding: 12px; border-radius: 12px; font-weight: 600; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); justify-content: center;">
                            <i class="fas fa-trash me-2"></i> Ya, Hapus Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    // Set the kelas name in the modal
    document.getElementById('deleteKelasName').textContent = name;

    // Set the form action URL
    const form = document.getElementById('deleteForm');
    form.action = "{{ route('waka.kelas.index') }}/" + id;

    // Show the modal using Bootstrap 5 API
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
