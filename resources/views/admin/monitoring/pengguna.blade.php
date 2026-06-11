@extends('layouts.sneat')

@section('title', 'Monitoring Data Pengguna')
@section('page-title', 'Monitoring Data Pengguna')
@section('page-subtitle', 'Lihat status akun tenaga pendidik dan siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/monitoring/pengguna.css', 'resources/js/admin/monitoring/pengguna.js'])
@endsection

@section('content')
@php
    $routeBase = 'admin.monitoring';
    $scopeLabel = 'Semua cabang';
    $userActiveRate = ($stats['totalUsers'] ?? 0) > 0
        ? round((($stats['userAktif'] ?? 0) / $stats['totalUsers']) * 100)
        : 0;
@endphp

<div class="container-xxl flex-grow-1 container-p-y monitoring-page">
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon primary"><i class="fas fa-chalkboard-teacher"></i></div>
                <span>Tenaga Pendidik</span>
                <strong>{{ $stats['totalTenagaPendidik'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon success"><i class="fas fa-user-graduate"></i></div>
                <span>Siswa</span>
                <strong>{{ $stats['totalSiswa'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon info"><i class="fas fa-users"></i></div>
                <span>Total Akun</span>
                <strong>{{ $stats['totalUsers'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon warning"><i class="fas fa-user-check"></i></div>
                <span>Akun Aktif</span>
                <strong>{{ $stats['userAktif'] ?? 0 }}</strong>
                <span class="meta-text">{{ $userActiveRate }}% dari total akun</span>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h5 class="mb-1">Tenaga Pendidik</h5>
                <p class="text-muted mb-0">Filter berdasarkan nama, role, dan status akun.</p>
            </div>
            <form action="{{ route($routeBase . '.pengguna') }}" method="GET" class="filter-toolbar">
                @foreach(request()->except(['search_tp', 'role_tp', 'status_tp', 'tp_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <input type="text" name="search_tp" value="{{ request('search_tp') }}" class="form-control" placeholder="Cari nama...">
                <select name="role_tp" class="form-select" data-monitoring-auto-submit>
                    <option value="">Semua Role</option>
                    <option value="ketua_pkbm" {{ request('role_tp') == 'ketua_pkbm' ? 'selected' : '' }}>Ketua PKBM</option>
                    <option value="sekretaris" {{ request('role_tp') == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                    <option value="bendahara" {{ request('role_tp') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                    <option value="wakil_kepala_sekolah" {{ request('role_tp') == 'wakil_kepala_sekolah' ? 'selected' : '' }}>Wakil Kepala Sekolah</option>
                    <option value="wali_kelas" {{ request('role_tp') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                    <option value="guru_pengajar" {{ request('role_tp') == 'guru_pengajar' ? 'selected' : '' }}>Guru Pengajar</option>
                </select>
                <select name="status_tp" class="form-select" data-monitoring-auto-submit>
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status_tp') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status_tp') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Cari</button>
                @if(request()->anyFilled(['search_tp', 'role_tp', 'status_tp']))
                    <a href="{{ route($routeBase . '.pengguna', request()->except(['search_tp', 'role_tp', 'status_tp', 'tp_page'])) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="content-card-body">
            <div class="table-responsive">
                <table class="table table-clean align-middle">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Status Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenagaPendidik as $tp)
                            <tr>
                                <td data-label="NIP"><span class="mobile-cell-value">{{ $tp->nip ?? '-' }}</span></td>
                                <td data-label="Nama">
                                    <span class="entity-title mobile-cell-value">{{ $tp->nama_lengkap }}</span>
                                </td>
                                <td data-label="Role">
                                    <span class="soft-badge primary">{{ ucwords(str_replace('_', ' ', $tp->user->role ?? '-')) }}</span>
                                </td>
                                <td data-label="Email"><span class="mobile-cell-value">{{ $tp->email ?? $tp->user->email ?? '-' }}</span></td>
                                <td data-label="Status Akun">
                                    @if(optional($tp->user)->is_active)
                                        <span class="soft-badge success"><i class="fas fa-check me-1"></i> Aktif</span>
                                    @else
                                        <span class="soft-badge danger"><i class="fas fa-times me-1"></i> Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-user-times"></i>
                                        <h6>Belum ada data tenaga pendidik</h6>
                                        <p>Data akan tampil setelah akun tenaga pendidik tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tenagaPendidik->hasPages())
                <div class="mt-4">{{ $tenagaPendidik->appends(request()->all())->links() }}</div>
            @endif
        </div>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h5 class="mb-1">Siswa</h5>
                <p class="text-muted mb-0">Pantau status siswa dan akses akun LMS/SIA.</p>
            </div>
            <form action="{{ route($routeBase . '.pengguna') }}" method="GET" class="filter-toolbar">
                @foreach(request()->except(['search_siswa', 'status_siswa', 'siswa_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <input type="text" name="search_siswa" value="{{ request('search_siswa') }}" class="form-control" placeholder="Cari siswa...">
                <select name="status_siswa" class="form-select" data-monitoring-auto-submit>
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status_siswa') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status_siswa') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Cari</button>
                @if(request()->anyFilled(['search_siswa', 'status_siswa']))
                    <a href="{{ route($routeBase . '.pengguna', request()->except(['search_siswa', 'status_siswa', 'siswa_page'])) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="content-card-body">
            <div class="table-responsive">
                <table class="table table-clean align-middle">
                    <thead>
                        <tr>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status Siswa</th>
                            <th>Status Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $s)
                            <tr>
                                <td data-label="NISN"><span class="mobile-cell-value">{{ $s->nisn ?? '-' }}</span></td>
                                <td data-label="Nama">
                                    <span class="entity-title mobile-cell-value">{{ $s->nama_lengkap }}</span>
                                </td>
                                <td data-label="Kelas">
                                    @if($s->kelas)
                                        <span class="soft-badge primary">{{ $s->kelas->nama_kelas }}</span>
                                    @else
                                        <span class="soft-badge warning">Belum ada kelas</span>
                                    @endif
                                </td>
                                <td data-label="Status Siswa">
                                    <span class="soft-badge {{ $s->status === 'aktif' ? 'success' : 'warning' }}">{{ ucfirst($s->status ?? '-') }}</span>
                                </td>
                                <td data-label="Status Akun">
                                    @if(optional($s->user)->is_active)
                                        <span class="soft-badge success"><i class="fas fa-check me-1"></i> Aktif</span>
                                    @else
                                        <span class="soft-badge danger"><i class="fas fa-times me-1"></i> Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-user-graduate"></i>
                                        <h6>Belum ada data siswa</h6>
                                        <p>Data siswa akan tampil setelah tersedia di sistem.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswa->hasPages())
                <div class="mt-4">{{ $siswa->appends(request()->all())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
