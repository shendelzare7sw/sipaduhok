@extends('layouts.sneat')

@section('title', 'Detail Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Detail Kelas')
@section('page-subtitle', $kelas->nama_kelas)

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
    color: #3b82f6;
}

.breadcrumb span {
    color: #9ca3af;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

.header-card {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(255,255,255,0.2);
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
    color: #7c3aed;
}

.btn-white:hover {
    background: #f5f3ff;
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

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

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
    color: #8b5cf6;
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
    color: #8b5cf6;
}

.wali-kelas-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border-radius: 12px;
    border: 1px solid #ddd6fe;
}

.wali-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
}

.wali-info h4 {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.wali-info p {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
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

.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-info { background: #e0f2fe; color: #075985; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.user-name {
    font-weight: 600;
    color: #111827;
}

.user-nisn {
    font-size: 12px;
    color: #6b7280;
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

.empty-state p {
    font-size: 14px;
    margin: 0;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    padding-top: 16px;
}
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.kelas.index') }}">Data Kelas</a>
        <span>/</span>
        <span class="current">{{ $kelas->nama_kelas }}</span>
    </div>

    <div class="header-card">
        <div class="header-content">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div class="header-text">
                        <h1>{{ $kelas->nama_kelas }}</h1>
                        <div class="header-meta">
                            <span class="header-badge">
                                <i class="fas fa-tag"></i> {{ $kelas->kode_kelas }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-layer-group"></i> {{ $kelas->jenjang }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-calendar"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.kelas.manage-siswa', $kelas) }}" class="btn btn-success">
                        <i class="fas fa-users"></i> Kelola Siswa
                    </a>
                    <a href="{{ route('admin.kelas.edit', $kelas) }}" class="btn btn-white">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-white-outline">
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
                    <div class="header-stat-value">{{ $stats['siswaLaki'] }}</div>
                    <div class="header-stat-label">Laki-laki</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['siswaPerempuan'] }}</div>
                    <div class="header-stat-label">Perempuan</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['sisaKuota'] }}</div>
                    <div class="header-stat-label">Sisa Kuota</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Kelas</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Kode Kelas</span>
                        <span class="info-value highlight">{{ $kelas->kode_kelas }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Kelas</span>
                        <span class="info-value">{{ $kelas->nama_kelas }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenjang</span>
                        <span class="info-value">{{ $kelas->jenjang }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cabang</span>
                        <span class="info-value">{{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tahun Ajaran</span>
                        <span class="info-value">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kuota Siswa</span>
                        <span class="info-value">{{ $kelas->kuota_siswa }} siswa</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-tie"></i> Wali Kelas</h5>
            </div>
            <div class="card-body">
                @if($kelas->waliKelas)
                    <div class="wali-kelas-card">
                        <div class="wali-avatar">{{ strtoupper(substr($kelas->waliKelas->nama_lengkap, 0, 1)) }}</div>
                        <div class="wali-info">
                            <h4>{{ $kelas->waliKelas->nama_lengkap }}</h4>
                            <p><i class="fas fa-id-badge"></i> NIP: {{ $kelas->waliKelas->nip ?? '-' }}</p>
                            <p><i class="fas fa-phone"></i> {{ $kelas->waliKelas->telepon ?? '-' }}</p>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <p>Belum ada wali kelas yang ditunjuk</p>
                        <a href="{{ route('admin.kelas.edit', $kelas) }}" class="btn btn-success" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i> Tunjuk Wali Kelas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-user-graduate"></i> Daftar Siswa</h5>
            <span class="badge badge-info">{{ $siswa->total() }} siswa</span>
        </div>
        <div class="card-body">
            @if($siswa->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $index => $s)
                            <tr>
                                <td>{{ $siswa->firstItem() + $index }}</td>
                                <td><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $s->nis }}</code></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                        <div>
                                            <div class="user-name">{{ $s->nama_lengkap }}</div>
                                            <div class="user-nisn">NISN: {{ $s->nisn }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($s->jenis_kelamin == 'L')
                                        <span class="badge badge-info"><i class="fas fa-mars"></i> Laki-laki</span>
                                    @else
                                        <span class="badge badge-purple"><i class="fas fa-venus"></i> Perempuan</span>
                                    @endif
                                </td>
                                <td>
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
                    <i class="fas fa-users"></i>
                    <p>Belum ada siswa di kelas ini</p>
                    <a href="{{ route('admin.kelas.manage-siswa', $kelas) }}" class="btn btn-success" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
