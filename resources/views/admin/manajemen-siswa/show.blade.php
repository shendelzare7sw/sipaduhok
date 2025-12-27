@extends('layouts.sneat')

@section('title', 'Detail Siswa - ' . $siswa->nama_lengkap)

@section('page-title', 'Detail Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

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

.breadcrumb a { color: #6b7280; text-decoration: none; }
.breadcrumb a:hover { color: #3b82f6; }
.breadcrumb .current { color: #111827; font-weight: 500; }

.header-card {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 20px;
    padding: 32px;
    color: white;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}

.header-card.female {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
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

.header-content { position: relative; z-index: 2; }

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
    width: 90px;
    height: 90px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
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
    background: rgba(255,255,255,0.2);
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
}

.header-actions { display: flex; gap: 10px; }

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

.btn-white { background: white; color: #2563eb; }
.btn-white:hover { background: #f0f9ff; }

.btn-white-outline {
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.5);
}
.btn-white-outline:hover {
    background: rgba(255,255,255,0.1);
    border-color: white;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-sm { padding: 8px 14px; font-size: 13px; }

.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 24px;
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

.card-header h5 i { color: #3b82f6; }

.card-body { padding: 24px; }

.grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

@media (max-width: 992px) {
    .grid-2 { grid-template-columns: 1fr; }
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

@media (max-width: 576px) {
    .info-grid { grid-template-columns: 1fr; }
}

.info-item { display: flex; flex-direction: column; gap: 4px; }

.info-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    font-weight: 600;
}

.info-value { font-size: 15px; color: #111827; }

.kelas-current {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #bbf7d0;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
}

.kelas-current.no-kelas {
    background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
    border-color: #fde047;
}

.kelas-current h4 {
    font-size: 14px;
    color: #166534;
    margin-bottom: 8px;
    font-weight: 600;
}

.kelas-current.no-kelas h4 { color: #a16207; }

.kelas-current .kelas-name {
    font-size: 24px;
    font-weight: 700;
    color: #15803d;
    margin-bottom: 8px;
}

.kelas-current.no-kelas .kelas-name {
    font-size: 18px;
    color: #ca8a04;
}

.kelas-current .kelas-meta {
    font-size: 13px;
    color: #16a34a;
}

.form-group { margin-bottom: 16px; }

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.form-group select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
}

.form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.manajemen-siswa.index') }}">Manajemen Siswa</a>
        <span>/</span>
        <span class="current">{{ $siswa->nama_lengkap }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="header-card {{ $siswa->jenis_kelamin == 'P' ? 'female' : '' }}">
        <div class="header-content">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
                    <div class="header-text">
                        <h1>{{ $siswa->nama_lengkap }}</h1>
                        <div class="header-meta">
                            <span class="header-badge">
                                <i class="fas fa-id-card"></i> NISN: {{ $siswa->nisn }}
                            </span>
                            @if($siswa->nis)
                            <span class="header-badge">
                                <i class="fas fa-hashtag"></i> NIS: {{ $siswa->nis }}
                            </span>
                            @endif
                            <span class="header-badge">
                                <i class="fas fa-{{ $siswa->jenis_kelamin == 'L' ? 'mars' : 'venus' }}"></i>
                                {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.manajemen-siswa.print-kartu', $siswa) }}" class="btn btn-white" target="_blank">
                        <i class="fas fa-id-card"></i> Cetak Kartu
                    </a>
                    <a href="{{ route('admin.manajemen-siswa.index') }}" class="btn btn-white-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        {{-- Data Pribadi --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> Data Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value">{{ $siswa->nama_lengkap }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">NISN</span>
                        <span class="info-value" style="font-family: monospace; color: #3b82f6;">{{ $siswa->nisn }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">NIS</span>
                        <span class="info-value">{{ $siswa->nis ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Kelamin</span>
                        <span class="info-value">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tempat, Tanggal Lahir</span>
                        <span class="info-value">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Alamat</span>
                        <span class="info-value">{{ $siswa->alamat }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Ayah</span>
                        <span class="info-value">{{ $siswa->nama_ayah ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Ibu</span>
                        <span class="info-value">{{ $siswa->nama_ibu ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Telepon Orang Tua</span>
                        <span class="info-value">{{ $siswa->telepon_orangtua ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Masuk</span>
                        <span class="info-value">{{ $siswa->tanggal_masuk->format('d F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="badge {{ $siswa->status == 'aktif' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($siswa->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cabang</span>
                        <span class="info-value">{{ $siswa->cabang->nama_cabang ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kelas & Penempatan --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-graduation-cap"></i> Kelas & Penempatan</h5>
            </div>
            <div class="card-body">
                <div class="kelas-current {{ !$siswa->kelas ? 'no-kelas' : '' }}">
                    @if($siswa->kelas)
                        <h4>Kelas Saat Ini</h4>
                        <div class="kelas-name">{{ $siswa->kelas->nama_kelas }}</div>
                        <div class="kelas-meta">
                            <i class="fas fa-layer-group"></i> {{ $siswa->kelas->jenjang }} •
                            <i class="fas fa-building"></i> {{ $siswa->kelas->cabang->nama_cabang ?? '-' }} •
                            <i class="fas fa-calendar"></i> {{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                            @if($siswa->kelas->waliKelas)
                                <br><i class="fas fa-user-tie"></i> Wali Kelas: {{ $siswa->kelas->waliKelas->nama_lengkap }}
                            @endif
                        </div>
                    @else
                        <h4>⚠️ Belum Ada Kelas</h4>
                        <div class="kelas-name">Siswa ini belum ditempatkan di kelas manapun</div>
                    @endif
                </div>

                <form action="{{ route('admin.manajemen-siswa.assign-kelas', $siswa) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="kelas_id">{{ $siswa->kelas ? 'Pindahkan ke Kelas Lain' : 'Tempatkan ke Kelas' }}</label>
                        <select name="kelas_id" id="kelas_id">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList->groupBy('jenjang') as $jenjang => $kelasGroup)
                                <optgroup label="{{ $jenjang }}">
                                    @foreach($kelasGroup as $k)
                                        @php
                                            $sisaKuota = $k->kuota_siswa - $k->siswa_count;
                                        @endphp
                                        <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }} {{ $sisaKuota <= 0 && $siswa->kelas_id != $k->id ? 'disabled' : '' }}>
                                            {{ $k->nama_kelas }} - {{ $k->cabang->nama_cabang ?? '' }} (Sisa: {{ $sisaKuota }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
