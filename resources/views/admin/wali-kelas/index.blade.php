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
                <div class="stat-icon-bg">📚</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Sudah Ada Wali</div>
                    <div class="stat-number">{{ $stats['kelasWithWali'] }}</div>
                    <div class="stat-desc">Kelas dengan wali</div>
                </div>
                <div class="stat-icon-bg">✅</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Belum Ada Wali</div>
                    <div class="stat-number">{{ $stats['kelasWithoutWali'] }}</div>
                    <div class="stat-desc">Perlu ditunjuk</div>
                </div>
                <div class="stat-icon-bg">⏳</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Total Wali Kelas</div>
                    <div class="stat-number">{{ $stats['totalWaliKelas'] }}</div>
                    <div class="stat-desc">Tenaga pendidik</div>
                </div>
                <div class="stat-icon-bg">👨‍🏫</div>
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
                    <table class="table">
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
                                <td>
                                    <div class="kelas-info">
                                        <span class="kelas-nama">{{ $kelas->nama_kelas }}</span>
                                        <span class="kelas-kode">{{ $kelas->kode_kelas }}</span>
                                    </div>
                                </td>
                                <td>
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
                                <td>{{ $kelas->cabang->nama_cabang ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $kelas->siswa_count }} siswa</span>
                                </td>
                                <td class="wali-kelas-cell">
                                    <form action="{{ route('admin.wali-kelas.assign', $kelas) }}" method="POST" class="quick-assign-form">
                                        @csrf
                                        <select name="wali_kelas_id" class="wali-kelas-select {{ $kelas->wali_kelas_id ? 'has-wali' : '' }}" onchange="this.form.submit()">
                                            <option value="">-- Pilih Wali Kelas --</option>
                                            @foreach($waliKelasOptions as $wk)
                                                <option value="{{ $wk->id }}" {{ $kelas->wali_kelas_id == $wk->id ? 'selected' : '' }}>
                                                    {{ $wk->nama_lengkap }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <div class="action-buttons">
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
@endsection
