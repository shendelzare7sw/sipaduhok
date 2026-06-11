@extends('layouts.sneat')

@section('title', 'Detail Cabang - ' . $cabang->nama_cabang)

@section('page-title', 'Detail Cabang')
@section('page-subtitle', $cabang->nama_cabang)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/admin/cabang/show.css')
@endsection

@section('content')

<div class="page-shell">
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
                        <div class="teacher-more-actions">
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
                <button class="tab-btn active" data-tab-target="kelas">
                    <i class="fas fa-chalkboard"></i> Daftar Kelas ({{ $kelas->count() }})
                </button>
                <button class="tab-btn" data-tab-target="siswa">
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
                                    <td class="desktop-only-cell"><code class="code-chip">{{ $k->kode_kelas }}</code></td>
                                    {{-- Desktop: Nama --}}
                                    <td class="desktop-only-cell"><strong>{{ $k->nama_kelas }}</strong></td>
                                    {{-- Mobile: Card Head --}}
                                    <td class="mobile-only-cell mobile-card-head">
                                        <strong>{{ $k->nama_kelas }}</strong>
                                        <span class="mobile-class-meta">
                                            <code class="mobile-code-chip">{{ $k->kode_kelas }}</code>
                                            <span class="badge badge-info badge-compact">{{ $k->jenjang }}</span>
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
                                    <td class="desktop-only-cell"><code class="code-chip">{{ $s->nis }}</code></td>
                                    {{-- Desktop: Nama --}}
                                    <td class="desktop-only-cell">
                                        <div class="user-info">
                                            <div class="user-avatar user-avatar-green">
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
                                            <div class="user-avatar user-avatar-green">
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

@endsection

@section('scripts')
    @vite('resources/js/admin/cabang/show.js')
@endsection
