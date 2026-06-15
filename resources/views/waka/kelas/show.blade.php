@extends('layouts.sneat')

@section('title', 'Detail Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Detail Kelas')
@section('page-subtitle', $kelas->nama_kelas)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/kelas/show.css'])
@endsection

@section('content')
<div class="kelas-show-page">
    <div class="kelas-breadcrumb">
        <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('waka.kelas.index') }}">Data Kelas</a>
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
                    <a href="{{ route('waka.kelas.manage-siswa', $kelas) }}" class="btn btn-success">
                        <i class="fas fa-users"></i> Kelola Siswa
                    </a>
                    <a href="{{ route('waka.kelas.edit', $kelas) }}" class="btn btn-white">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('waka.kelas.index') }}" class="btn btn-white-outline">
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
                        <a href="{{ route('waka.kelas.edit', $kelas) }}" class="btn btn-success empty-action">
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
                    <table class="table table-card-mobile">
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
                                <td class="mobile-card-hide" data-label="No">{{ $siswa->firstItem() + $index }}</td>
                                <td data-label="NIS"><code class="nis-code">{{ $s->nis }}</code></td>
                                <td class="mobile-card-head">
                                    <div class="user-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                    <div>
                                        <div class="user-name">{{ $s->nama_lengkap }}</div>
                                        <div class="user-nisn">NISN: {{ $s->nisn }}</div>
                                    </div>
                                </td>
                                <td data-label="Jenis Kelamin">
                                    @if($s->jenis_kelamin == 'L')
                                        <span class="badge badge-info"><i class="fas fa-mars"></i> Laki-laki</span>
                                    @else
                                        <span class="badge badge-purple"><i class="fas fa-venus"></i> Perempuan</span>
                                    @endif
                                </td>
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
                    <i class="fas fa-users"></i>
                    <p>Belum ada siswa di kelas ini</p>
                    <a href="{{ route('waka.kelas.manage-siswa', $kelas) }}" class="btn btn-success empty-action">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
