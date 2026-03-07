@extends('layouts.sneat')

@section('title', 'Detail Cabang - ' . $cabang->nama_cabang)

@section('page-title', 'Detail Cabang')
@section('page-subtitle', $cabang->nama_cabang)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Breadcrumb */
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
    transition: color 0.2s;
}

.breadcrumb a:hover {
    color: #3b82f6;
}

.breadcrumb span {
    color: #9ca3af;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

/* Header Card */
.header-card {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
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
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.header-card::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
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

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
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

.header-code {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: rgba(255,255,255,0.2);
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.header-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

@media (max-width: 992px) {
    .header-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .header-stats {
        grid-template-columns: 1fr;
    }
    
    .header-info {
        flex-direction: column;
        text-align: center;
    }
}

.header-stat {
    background: rgba(255,255,255,0.15);
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

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
}

.status-badge.active {
    background: rgba(16, 185, 129, 0.2);
    color: #a7f3d0;
}

.status-badge.inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #fecaca;
}

/* Buttons */
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
    color: #3b82f6;
}

.btn-white:hover {
    background: #f0f9ff;
    transform: translateY(-1px);
}

.btn-white-outline {
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.5);
}

.btn-white-outline:hover {
    background: rgba(255,255,255,0.1);
    border-color: white;
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

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Card Styles */
.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    border: none;
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
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
    color: #3b82f6;
}

.card-body {
    padding: 24px;
}

/* Grid Layout */
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

/* Info Grid */
.info-grid {
    display: grid;
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
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
    line-height: 1.6;
}

.info-value.highlight {
    font-weight: 600;
    color: #3b82f6;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
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
    vertical-align: middle;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover td {
    background: #f9fafb;
}

/* Badge Styles */
.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-info {
    background: #e0f2fe;
    color: #075985;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-purple {
    background: #f3e8ff;
    color: #7c3aed;
}

/* Empty State */
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

.empty-state p {
    font-size: 14px;
    margin: 0;
}

/* User Avatar */
.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-name {
    font-weight: 600;
    color: #111827;
}

.user-email {
    font-size: 12px;
    color: #6b7280;
}

/* Tabs */
.tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 24px;
}

.tab-btn {
    padding: 12px 20px;
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    position: relative;
    transition: all 0.2s;
}

.tab-btn:hover {
    color: #3b82f6;
}

.tab-btn.active {
    color: #3b82f6;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #3b82f6;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    padding-top: 16px;
}

.pagination {
    display: flex;
    gap: 4px;
}

.pagination .page-link {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.pagination .page-link:hover {
    background: #f3f4f6;
}

.pagination .page-item.active .page-link {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

/* Mobile Card Pattern for Tables */
@media (max-width: 767.98px) {
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
        position: relative;
    }
    .table-card-mobile tbody tr:hover td { background: transparent; }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f3f4f6 !important;
        white-space: normal;
        text-align: right;
    }
    .table-card-mobile tbody td:last-child { border-bottom: none !important; }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        margin-right: 12px;
        text-align: left;
    }
    .table-card-mobile .mobile-card-head {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        padding: 14px !important;
        border-bottom: 2px solid #e0e7ff !important;
        display: block !important;
        text-align: left;
    }
    .table-card-mobile .mobile-card-head::before { display: none !important; }
    .table-card-mobile .mobile-hide { display: none !important; }
    .desktop-only-cell { display: none !important; }
    .mobile-only-cell { display: flex !important; }
    .table-card-mobile .user-info { text-align: left; }
}
@media (min-width: 768px) {
    .mobile-only-cell { display: none !important; }
}
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.cabang.index') }}">Manajemen Cabang</a>
        <span>/</span>
        <span class="current">{{ $cabang->nama_cabang }}</span>
    </div>

    {{-- Header Card --}}
    <div class="header-card">
        <div class="header-content">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="header-text">
                        <h1>{{ $cabang->nama_cabang }}</h1>
                        <div class="header-meta">
                            <span class="header-code">
                                <i class="fas fa-tag"></i> {{ $cabang->kode_cabang }}
                            </span>
                            <span class="status-badge {{ $cabang->is_active ? 'active' : 'inactive' }}">
                                <i class="fas {{ $cabang->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $cabang->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.cabang.edit', $cabang) }}" class="btn btn-white">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.cabang.index') }}" class="btn btn-white-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['totalSiswa'] }}</div>
                    <div class="header-stat-label">Total Siswa</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['siswaAktif'] }}</div>
                    <div class="header-stat-label">Siswa Aktif</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['totalKelas'] }}</div>
                    <div class="header-stat-label">Jumlah Kelas</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['totalTenagaPendidik'] }}</div>
                    <div class="header-stat-label">Tenaga Pendidik</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid-2">
        {{-- Detail Cabang --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Cabang</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Kode Cabang</span>
                        <span class="info-value highlight">{{ $cabang->kode_cabang }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Cabang</span>
                        <span class="info-value">{{ $cabang->nama_cabang }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Alamat Lengkap</span>
                        <span class="info-value">{{ $cabang->alamat }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nomor Telepon</span>
                        <span class="info-value">{{ $cabang->telepon ?: '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            @if($cabang->is_active)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Aktif</span>
                            @else
                                <span class="badge badge-warning"><i class="fas fa-pause"></i> Non-Aktif</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dibuat Pada</span>
                        <span class="info-value">{{ $cabang->created_at->format('d F Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tenaga Pendidik --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Tenaga Pendidik</h5>
                <span class="badge badge-info">{{ $users->count() }} orang</span>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users->take(5) as $user)
                                <tr>
                                    <td class="mobile-card-head">
                                        <div class="user-info">
                                            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                            <div>
                                                <div class="user-name">{{ $user->name }}</div>
                                                <div class="user-email">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Role">
                                        <span class="badge badge-purple">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($users->count() > 5)
                        <div style="text-align: center; padding-top: 12px;">
                            <a href="{{ route('admin.users.tenaga-pendidik') }}?cabang_id={{ $cabang->id }}" class="btn btn-outline btn-sm">
                                Lihat Semua ({{ $users->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Belum ada tenaga pendidik di cabang ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kelas & Siswa Tabs --}}
    <div class="card">
        <div class="card-body">
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('kelas')">
                    <i class="fas fa-chalkboard"></i> Daftar Kelas ({{ $kelas->count() }})
                </button>
                <button class="tab-btn" onclick="showTab('siswa')">
                    <i class="fas fa-user-graduate"></i> Daftar Siswa ({{ $siswa->total() }})
                </button>
            </div>

            {{-- Tab Kelas --}}
            <div class="tab-content active" id="tab-kelas">
                @if($kelas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile">
                            <thead>
                                <tr>
                                    <th>Kode Kelas</th>
                                    <th>Nama Kelas</th>
                                    <th>Jenjang</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Wali Kelas</th>
                                    <th>Kuota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas as $k)
                                <tr>
                                    {{-- Desktop: Kode --}}
                                    <td class="desktop-only-cell"><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $k->kode_kelas }}</code></td>
                                    {{-- Desktop: Nama --}}
                                    <td class="desktop-only-cell"><strong>{{ $k->nama_kelas }}</strong></td>
                                    {{-- Mobile: Card Head --}}
                                    <td class="mobile-only-cell mobile-card-head">
                                        <strong>{{ $k->nama_kelas }}</strong>
                                        <span style="display: flex; gap: 6px; align-items: center; margin-top: 4px;">
                                            <code style="background: rgba(0,0,0,0.08); padding: 2px 8px; border-radius: 4px; font-size: 11px;">{{ $k->kode_kelas }}</code>
                                            <span class="badge badge-info" style="font-size: 10px;">{{ $k->jenjang }}</span>
                                        </span>
                                    </td>
                                    <td class="desktop-only-cell"><span class="badge badge-info">{{ $k->jenjang }}</span></td>
                                    <td data-label="Tahun Ajaran">{{ $k->tahunAjaran->nama_tahun_ajaran ?? '-' }}</td>
                                    <td data-label="Wali Kelas">{{ $k->waliKelas->nama_lengkap ?? '-' }}</td>
                                    <td data-label="Kuota">{{ $k->kuota_siswa }} siswa</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard"></i>
                        <p>Belum ada kelas di cabang ini</p>
                    </div>
                @endif
            </div>

            {{-- Tab Siswa --}}
            <div class="tab-content" id="tab-siswa">
                @if($siswa->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile">
                            <thead>
                                <tr>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswa as $s)
                                <tr>
                                    {{-- Desktop: NIS --}}
                                    <td class="desktop-only-cell"><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $s->nis }}</code></td>
                                    {{-- Desktop: Nama --}}
                                    <td class="desktop-only-cell">
                                        <div class="user-info">
                                            <div class="user-avatar" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $s->nama_lengkap }}</div>
                                                <div class="user-email">NISN: {{ $s->nisn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Mobile: Card Head --}}
                                    <td class="mobile-only-cell mobile-card-head">
                                        <div class="user-info">
                                            <div class="user-avatar" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $s->nama_lengkap }}</div>
                                                <div class="user-email">NIS: {{ $s->nis }} | NISN: {{ $s->nisn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Kelas">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                    <td data-label="Jenis Kelamin">{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td data-label="Status">
                                        <span class="badge badge-success">{{ ucfirst($s->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($siswa->hasPages())
                        <div class="pagination-wrapper">
                            {{ $siswa->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-graduate"></i>
                        <p>Belum ada siswa aktif di cabang ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
    
    // Add active to clicked button
    event.target.classList.add('active');
}
</script>
@endsection
