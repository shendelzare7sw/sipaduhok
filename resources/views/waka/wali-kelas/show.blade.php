@extends('layouts.sneat')

@section('title', 'Detail Wali Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Detail Wali Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas)

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
    color: #8b5cf6;
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
    flex-wrap: wrap;
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

.wali-kelas-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border-radius: 16px;
    border: 2px solid #ddd6fe;
}

.wali-avatar {
    width: 80px;
    height: 80px;
    min-width: 80px;
    min-height: 80px;
    flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 28px;
}

.wali-info h3 {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
}

.wali-info p {
    font-size: 14px;
    color: #6b7280;
    margin: 4px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.wali-info p i {
    color: #8b5cf6;
    width: 16px;
}

.empty-wali {
    text-align: center;
    padding: 40px;
    background: #fef3c7;
    border-radius: 16px;
    border: 2px dashed #fcd34d;
}

.empty-wali i {
    font-size: 48px;
    color: #f59e0b;
    margin-bottom: 12px;
}

.empty-wali h4 {
    color: #92400e;
    margin-bottom: 8px;
}

.empty-wali p {
    color: #b45309;
    font-size: 14px;
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

.assign-form {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.assign-form label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.assign-form select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 16px;
}

.assign-form select:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
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
    font-size: 12px;
    font-weight: 600;
}

.badge-info { background: #e0f2fe; color: #075985; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
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

@media (max-width: 767.98px) {
    .header-card { padding: 20px; }
    .header-icon { width: 56px; height: 56px; font-size: 24px; border-radius: 12px; }
    .header-text h1 { font-size: 20px; }
    .header-info { flex-wrap: wrap; }
    .header-actions { width: 100%; }
    .header-actions .btn { flex: 1; justify-content: center; }

    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        background: white;
    }
    .table-card-mobile tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 4px;
        border: none;
        font-size: 13px;
    }
    .table-card-mobile tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6b7280;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        margin-right: 8px;
    }
    .table-card-mobile tbody td.mobile-card-hide { display: none; }
    .table-card-mobile tbody td.mobile-card-head {
        display: flex;
        align-items: center;
        padding-bottom: 8px;
        margin-bottom: 4px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        font-weight: 600;
    }
    .table-card-mobile tbody td.mobile-card-head::before { display: none; }
}
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    <div class="breadcrumb">
        <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('waka.wali-kelas.index') }}">Data Wali Kelas</a>
        <span>/</span>
        <span class="current">{{ $kelas->nama_kelas }}</span>
    </div>

    <div class="header-card">
        <div class="header-content">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-icon"><i class="fas fa-books"></i></div>
                    <div class="header-text">
                        <h1>Kelas {{ $kelas->nama_kelas }}</h1>
                        <div class="header-meta">
                            <span class="header-badge">
                                <i class="fas fa-tag"></i> {{ $kelas->kode_kelas }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-layer-group"></i> {{ $kelas->jenjang }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('waka.kelas.show', $kelas) }}" class="btn btn-white">
                        <i class="fas fa-eye"></i> Detail Kelas
                    </a>
                    <a href="{{ route('waka.wali-kelas.index') }}" class="btn btn-white-outline">
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
            </div>
        </div>
    </div>

    <div class="grid-2">
        {{-- Wali Kelas Info --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-tie"></i> Wali Kelas</h5>
            </div>
            <div class="card-body">
                @if($kelas->waliKelas)
                    <div class="wali-kelas-card">
                        <div class="wali-avatar">{{ strtoupper(substr($kelas->waliKelas->nama_lengkap, 0, 1)) }}</div>
                        <div class="wali-info">
                            <h3>{{ $kelas->waliKelas->nama_lengkap }}</h3>
                            <p><i class="fas fa-id-badge"></i> NIP: {{ $kelas->waliKelas->nip ?? '-' }}</p>
                            <p><i class="fas fa-phone"></i> {{ $kelas->waliKelas->telepon ?? '-' }}</p>
                            <p><i class="fas fa-envelope"></i> {{ $kelas->waliKelas->user->email ?? '-' }}</p>
                        </div>
                    </div>
                @else
                    <div class="empty-wali">
                        <i class="fas fa-user-slash"></i>
                        <h4>Belum Ada Wali Kelas</h4>
                        <p>Silakan tunjuk wali kelas untuk kelas ini</p>
                    </div>
                @endif

                <div class="assign-form">
                    <form action="{{ route('waka.wali-kelas.assign', $kelas) }}" method="POST">
                        @csrf
                        <label for="wali_kelas_id">
                            {{ $kelas->waliKelas ? 'Ganti Wali Kelas' : 'Tunjuk Wali Kelas' }}
                        </label>
                        <select name="wali_kelas_id" id="wali_kelas_id">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($waliKelasOptions as $wk)
                                <option value="{{ $wk->id }}" {{ $kelas->wali_kelas_id == $wk->id ? 'selected' : '' }}>
                                    {{ $wk->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kelas Info --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Kelas</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Kode Kelas</span>
                        <span class="info-value" style="font-family: monospace; color: #8b5cf6; font-weight: 600;">{{ $kelas->kode_kelas }}</span>
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
    </div>

    {{-- Daftar Siswa --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-user-graduate"></i> Daftar Siswa ({{ $stats['totalSiswa'] }})</h5>
        </div>
        <div class="card-body">
            @if($kelas->siswa->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th class="mobile-card-hide">No</th>
                                <th data-label="NIS">NIS</th>
                                <th class="mobile-card-head">Nama Siswa</th>
                                <th data-label="Jenis Kelamin">Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->siswa->sortBy('nama_lengkap') as $index => $siswa)
                            <tr>
                                <td class="mobile-card-hide">{{ $index + 1 }}</td>
                                <td data-label="NIS"><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $siswa->nis }}</code></td>
                                <td class="mobile-card-head">
                                    <div class="user-info">
                                        <div class="user-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
                                        <span>{{ $siswa->nama_lengkap }}</span>
                                    </div>
                                </td>
                                <td data-label="Jenis Kelamin">
                                    <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Belum ada siswa di kelas ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
