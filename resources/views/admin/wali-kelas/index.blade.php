@extends('layouts.sneat')

@section('title', 'Data Wali Kelas')

@section('page-title', 'Data Wali Kelas')
@section('page-subtitle')
Kelola penunjukan wali kelas {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
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

.row { display: flex; flex-wrap: wrap; margin: -12px; }
.col-md-3 { flex: 0 0 25%; max-width: 25%; padding: 12px; }
.col-12 { flex: 0 0 100%; max-width: 100%; padding: 12px; }

@media (max-width: 992px) {
    .col-md-3 { flex: 0 0 50%; max-width: 50%; }
}

@media (max-width: 576px) {
    .col-md-3 { flex: 0 0 100%; max-width: 100%; }
}

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
    max-width: 100%;
    transition: all 0.3s;
}

.search-box input:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
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
    min-width: 150px;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #8b5cf6;
}

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

.table tr:hover td {
    background: #f9fafb;
}

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
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-info { background: #e0f2fe; color: #075985; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }

.badge-paud { background: #fef3c7; color: #92400e; }
.badge-sd { background: #dcfce7; color: #166534; }
.badge-smp { background: #e0f2fe; color: #075985; }
.badge-sma { background: #f3e8ff; color: #7c3aed; }

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
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
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
    padding: 8px 14px;
    font-size: 13px;
}

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 8px;
}

.btn-light-primary { background: #eff6ff; color: #3b82f6; border: none; }
.btn-light-primary:hover { background: #dbeafe; }

.btn-light-purple { background: #faf5ff; color: #7c3aed; border: none; }
.btn-light-purple:hover { background: #f3e8ff; }

.btn-print {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
}

.btn-print:hover {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
}

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

.wali-kelas-cell {
    min-width: 250px;
}

.wali-kelas-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
}

.wali-kelas-select:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.wali-kelas-select.has-wali {
    border-color: #10b981;
    background: #f0fdf4;
}

.wali-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.wali-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.wali-details {
    display: flex;
    flex-direction: column;
}

.wali-name {
    font-weight: 600;
    color: #111827;
}

.wali-role {
    font-size: 12px;
    color: #6b7280;
}

.status-unassigned {
    color: #f59e0b;
    font-style: italic;
}

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

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

/* Quick assign form */
.quick-assign-form {
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-assign-form select {
    flex: 1;
}

.quick-assign-form button {
    flex-shrink: 0;
}

/* ── Mobile Responsive ── */
@media (max-width: 767.98px) {
    .search-box {
        width: 100% !important;
    }
    .search-box input {
        width: 100% !important;
    }
    .filter-section {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .filter-section .filter-select,
    .filter-section .btn {
        width: 100% !important;
        min-width: unset !important;
    }
    /* Table → Card per row */
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
    }
    .table-card-mobile tbody tr:hover td { background: transparent !important; }
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
        padding: 14px;
        border-bottom: 2px solid #e5e7eb !important;
        justify-content: flex-start;
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
        min-width: 65px;
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
    }
    .stat-number { font-size: 28px; }
    .stat-card { padding: 16px; }
    /* Stat grid: 2 columns on mobile */
    .col-md-3 { flex: 0 0 50% !important; max-width: 50% !important; }
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
                    <div class="stat-desc">Kelas terdaftar</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-chalkboard"></i></div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Sudah Ada Wali</div>
                    <div class="stat-number">{{ $stats['kelasWithWali'] }}</div>
                    <div class="stat-desc">Kelas dengan wali</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Belum Ada Wali</div>
                    <div class="stat-number">{{ $stats['kelasWithoutWali'] }}</div>
                    <div class="stat-desc">Perlu ditunjuk</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Total Wali Kelas</div>
                    <div class="stat-number">{{ $stats['totalWaliKelas'] }}</div>
                    <div class="stat-desc">Tenaga pendidik</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-school"></i></div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h5><i class="fas fa-user-tie" style="color: #8b5cf6; margin-right: 10px;"></i>Penunjukan Wali Kelas</h5>
                <small style="color: #6b7280;">Kelola penugasan wali kelas per kelas</small>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.wali-kelas.print', request()->query()) }}" class="btn btn-print" target="_blank">
                    <i class="fas fa-print"></i> Cetak
                </a>
            </div>
        </div>
        
        <div class="card-body">
            {{-- Filter Section --}}
            <form action="{{ route('admin.wali-kelas.index') }}" method="GET">
                <div class="filter-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Cari kelas atau wali..." value="{{ request('search') }}">
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
                    
                    <select name="cabang_id" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        @foreach($cabangs as $c)
                            <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Sudah Ada Wali</option>
                        <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Belum Ada Wali</option>
                    </select>
                    
                    <button type="submit" class="btn btn-outline">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'jenjang', 'cabang_id', 'status']))
                        <a href="{{ route('admin.wali-kelas.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($kelasList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jenjang</th>
                                <th>Cabang</th>
                                <th>Jumlah Siswa</th>
                                <th class="wali-kelas-cell">Wali Kelas</th>
                                <th style="text-align: center; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelasList as $kelas)
                            <tr>
                                <td class="mobile-card-head">
                                    <div class="kelas-info">
                                        <span class="kelas-nama">{{ $kelas->nama_kelas }}</span>
                                        <span class="kelas-kode">{{ $kelas->kode_kelas }}</span>
                                    </div>
                                </td>
                                <td data-label="Jenjang">
                                    @php
                                        $jenjangClass = [
                                            'PAUD' => 'badge-paud',
                                            'SD' => 'badge-sd',
                                            'SMP' => 'badge-smp',
                                            'SMA' => 'badge-sma',
                                        ][$kelas->jenjang] ?? 'badge-info';
                                    @endphp
                                    <span class="badge {{ $jenjangClass }}">{{ $kelas->jenjang }}</span>
                                </td>
                                <td data-label="Cabang">{{ $kelas->cabang->nama_cabang ?? '-' }}</td>
                                <td data-label="Siswa">
                                    <span class="badge badge-info">{{ $kelas->siswa_count }} siswa</span>
                                </td>
                                <td data-label="Wali Kelas" class="wali-kelas-cell mobile-card-full">
                                    @if($kelas->waliKelasAssignments->count() > 0)
                                        @foreach($kelas->waliKelasAssignments as $assignment)
                                            <div class="wali-info" style="margin-bottom: 4px;">
                                                <div class="wali-avatar">{{ substr($assignment->tenagaPendidik->nama_lengkap, 0, 2) }}</div>
                                                <div class="wali-details">
                                                    <span class="wali-name">{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                                    <span class="wali-role">Wali Kelas</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="status-unassigned">Belum ada wali kelas</span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-icon btn-light-purple" title="Assign Wali Kelas" onclick="openAssignModal({{ $kelas->id }}, '{{ $kelas->nama_kelas }}', {{ $kelas->wali_kelas_id ?? 'null' }})">
                                            <i class="fas fa-user-tie"></i>
                                        </button>
                                        <a href="{{ route('admin.wali-kelas.show', $kelas) }}" class="btn btn-icon btn-light-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($kelasList->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Menampilkan {{ $kelasList->firstItem() }} - {{ $kelasList->lastItem() }} dari {{ $kelasList->total() }} kelas
                        </div>
                        <div>
                            {{ $kelasList->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-user-tie"></i>
                    <h3>Belum Ada Data Kelas</h3>
                    <p>Silakan buat kelas terlebih dahulu di menu Data Kelas.</p>
                    <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i> Tambah Kelas
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Assign Wali Kelas --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px;">
                <h5 class="modal-title" style="font-weight: 600; color: #111827;">
                    <i class="fas fa-user-tie" style="color: #8b5cf6; margin-right: 10px;"></i>
                    Assign Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Kelas</label>
                        <input type="text" id="kelasName" class="form-control" readonly style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 16px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">
                            <i class="fas fa-search" style="color: #9ca3af; margin-right: 6px;"></i>
                            Cari Wali Kelas
                        </label>
                        <input type="text" id="searchWali" class="form-control" placeholder="Ketik nama wali kelas..." style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; margin-bottom: 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">
                            Filter Cabang
                        </label>
                        <select id="filterCabang" class="form-select" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px;">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Pilih Wali Kelas</label>
                        <div id="waliList" style="max-height: 300px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                            @foreach($waliKelasOptions as $wk)
                                @php
                                    $assignedKelasList = $wk->waliKelasAssignments ?? collect();
                                    $hasAssignments = $assignedKelasList->count() > 0;
                                @endphp
                                <div class="wali-option"
                                     data-id="{{ $wk->id }}"
                                     data-name="{{ strtolower($wk->nama_lengkap) }}"
                                     data-cabang="{{ $wk->user->cabang_id ?? '' }}"
                                     style="padding: 12px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px;">
                                    <input type="radio" name="wali_kelas_id" value="{{ $wk->id }}" style="cursor: pointer;">
                                    <div class="wali-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                        {{ substr($wk->nama_lengkap, 0, 2) }}
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #111827;">{{ $wk->nama_lengkap }}</div>
                                        <div style="font-size: 12px; color: #6b7280;">{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                        @if($hasAssignments)
                                            <div style="font-size: 11px; color: #6366f1; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                                @foreach($assignedKelasList as $assignment)
                                                    <span style="background: #e0e7ff; padding: 2px 6px; border-radius: 4px;">{{ $assignment->kelas->nama_kelas }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="padding: 12px; background: #e0f2fe; border-radius: 8px; border-left: 4px solid #0284c7;">
                        <div style="display: flex; gap: 8px; align-items: start;">
                            <i class="fas fa-info-circle" style="color: #0284c7; margin-top: 2px;"></i>
                            <div style="font-size: 13px; color: #0369a1;">
                                <strong>Multi-Kelas:</strong> Satu wali kelas bisa ditugaskan ke lebih dari satu kelas.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                    <button type="button" id="btnRemoveWali" class="btn" style="background: #fef2f2; color: #dc2626; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-times"></i> Hapus Wali Kelas
                    </button>
                    <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                        Batal
                    </button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Wali Kelas --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px; background: #fef2f2;">
                <h5 class="modal-title" style="font-weight: 600; color: #dc2626;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
                    Konfirmasi Hapus Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="color: #374151; margin-bottom: 16px;">
                    Apakah Anda yakin ingin menghapus wali kelas dari kelas <strong id="deleteKelasName"></strong>?
                </p>
                <div style="padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #f59e0b; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #92400e;">
                            Tindakan ini akan menghapus penugasan wali kelas. Kelas akan menjadi "Belum ada wali kelas".
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    Batal
                </button>
                <button type="button" id="confirmDeleteBtn" class="btn" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-trash"></i> Ya, Hapus Wali Kelas
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal konfirmasi reassign dihapus - sekarang dukung multi-kelas --}}

<script>
let currentKelasId = null;
let currentKelasName = '';

function openAssignModal(kelasId, kelasName, currentWaliId) {
    currentKelasId = kelasId;
    currentKelasName = kelasName;
    document.getElementById('kelasName').value = kelasName;
    document.getElementById('assignForm').action = `/admin/wali-kelas/${kelasId}/assign`;

    // Reset search
    document.getElementById('searchWali').value = '';
    document.getElementById('filterCabang').value = '';

    // Set current wali if exists
    if (currentWaliId) {
        const radio = document.querySelector(`input[name="wali_kelas_id"][value="${currentWaliId}"]`);
        if (radio) radio.checked = true;
    } else {
        // Uncheck all
        document.querySelectorAll('input[name="wali_kelas_id"]').forEach(r => r.checked = false);
    }

    filterWaliList();

    const modal = new bootstrap.Modal(document.getElementById('assignModal'));
    modal.show();
}

// Search & Filter functionality
document.getElementById('searchWali').addEventListener('input', filterWaliList);
document.getElementById('filterCabang').addEventListener('change', filterWaliList);

function filterWaliList() {
    const searchTerm = document.getElementById('searchWali').value.toLowerCase();
    const cabangFilter = document.getElementById('filterCabang').value;
    const waliOptions = document.querySelectorAll('.wali-option');

    waliOptions.forEach(option => {
        const name = option.getAttribute('data-name');
        const cabang = option.getAttribute('data-cabang');

        const matchSearch = searchTerm === '' || name.includes(searchTerm);
        const matchCabang = cabangFilter === '' || cabang === cabangFilter;

        if (matchSearch && matchCabang) {
            option.style.display = 'flex';
        } else {
            option.style.display = 'none';
        }
    });
}

// Hover effect for wali options
document.querySelectorAll('.wali-option').forEach(option => {
    option.addEventListener('mouseenter', function() {
        this.style.background = '#f9fafb';
    });
    option.addEventListener('mouseleave', function() {
        this.style.background = 'transparent';
    });
    option.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
    });
});

// Open confirm delete modal
document.getElementById('btnRemoveWali').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    document.getElementById('deleteKelasName').textContent = currentKelasName;

    const assignModal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
    if (assignModal) {
        assignModal.hide();
    }

    setTimeout(function() {
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmModal.show();
    }, 300);
});

// Confirm delete button
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
    if (confirmModal) {
        confirmModal.hide();
    }

    // Uncheck all radios
    document.querySelectorAll('input[name="wali_kelas_id"]').forEach(r => r.checked = false);

    // Submit form
    setTimeout(function() {
        document.getElementById('assignForm').submit();
    }, 300);
});
</script>

<style>
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

.wali-option input[type="radio"]:checked {
    accent-color: #8b5cf6;
}

.wali-option:has(input:checked) {
    background: #f3e8ff !important;
    border: 1px solid #8b5cf6;
}

#waliList::-webkit-scrollbar {
    width: 8px;
}

#waliList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#waliList::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

#waliList::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
@endsection
