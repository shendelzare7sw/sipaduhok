{{-- resources/views/admin/monitoring/pengguna.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Monitoring Data Pengguna')

@section('page-title', 'Monitoring Data Pengguna')
@section('page-subtitle', 'Lihat status aktif dan data pengguna')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .card-body {
            padding: 24px;
        }

        .stats-mini {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-mini-card {
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
        }

        .stat-mini-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-mini-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-mini-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-mini-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .stat-mini-value {
            font-size: 32px;
            font-weight: 700;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: #f9fafb;
        }

        .table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        @media (max-width: 768px) {
            .card-header { flex-direction: column !important; align-items: stretch !important; }
            .card-header h5 { font-size: 16px; margin-bottom: 12px; }
            .filter-group, .card-header form { flex-direction: column !important; width: 100%; }
            .filter-group input, .filter-group select, .card-header form input, .card-header form select { width: 100% !important; }
            .card-body { padding: 16px; }
            .card-header { padding: 16px; }
            .table th, .table td { padding: 10px 8px; font-size: 12px; }
            .stats-mini { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-mini-value { font-size: 24px; }
            .stat-mini-label { font-size: 11px; }
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        {{-- Stats Mini --}}
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-label">Total Tenaga Pendidik</div>
                <div class="stat-mini-value">{{ $stats['totalTenagaPendidik'] }}</div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-label">Total Siswa</div>
                <div class="stat-mini-value">{{ $stats['totalSiswa'] }}</div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-label">Total Users</div>
                <div class="stat-mini-value">{{ $stats['totalUsers'] }}</div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-label">User Aktif</div>
                <div class="stat-mini-value">{{ $stats['userAktif'] }}</div>
            </div>
        </div>

        {{-- Tabel Tenaga Pendidik --}}
        <div class="card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <h5><i class="fas fa-chalkboard-teacher"></i> Data Tenaga Pendidik</h5>
                <form action="{{ route('admin.monitoring.pengguna') }}" method="GET" class="filter-group" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <!-- Preserve parameters from other table if any -->
                    @foreach(request()->except(['search_tp', 'role_tp', 'status_tp', 'tp_page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <input type="text" name="search_tp" value="{{ request('search_tp') }}" placeholder="Cari nama..."
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px;">
                    
                    <select name="role_tp" onchange="this.form.submit()"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Role</option>
                        <option value="ketua_pkbm" {{ request('role_tp') == 'ketua_pkbm' ? 'selected' : '' }}>Ketua PKBM</option>
                        <option value="sekretaris" {{ request('role_tp') == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                        <option value="bendahara" {{ request('role_tp') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                        <option value="wakil_kepala_sekolah" {{ request('role_tp') == 'wakil_kepala_sekolah' ? 'selected' : '' }}>Wakil Kepala Sekolah</option>
                        <option value="wali_kelas" {{ request('role_tp') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="guru_pengajar" {{ request('role_tp') == 'guru_pengajar' ? 'selected' : '' }}>Guru Pengajar</option>
                    </select>

                    <select name="status_tp" onchange="this.form.submit()"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status_tp') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status_tp') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    
                     <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Status Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenagaPendidik as $tp)
                                <tr>
                                    <td>{{ $tp->nip ?? '-' }}</td>
                                    <td><strong>{{ $tp->nama_lengkap }}</strong></td>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ ucwords(str_replace('_', ' ', $tp->user->role)) }}
                                        </span>
                                    </td>
                                    <td>{{ $tp->email ?? $tp->user->email }}</td>
                                    <td>
                                        @if($tp->user->is_active)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Aktif</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i> Non-Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-user-times fa-3x" style="color: #d1d5db; margin-bottom: 16px;"></i>
                                        <p style="color: #9ca3af; margin: 0;">Belum ada data tenaga pendidik</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tenagaPendidik->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $tenagaPendidik->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabel Siswa --}}
        <div class="card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <h5><i class="fas fa-user-graduate"></i> Data Siswa</h5>
                <form action="{{ route('admin.monitoring.pengguna') }}" method="GET" class="filter-group" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                     <!-- Preserve parameters from other table if any -->
                    @foreach(request()->except(['search_siswa', 'status_siswa', 'siswa_page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <input type="text" name="search_siswa" value="{{ request('search_siswa') }}" placeholder="Cari nama siswa..."
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px;">
                    
                    <select name="status_siswa" onchange="this.form.submit()"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status_siswa') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status_siswa') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>

                     <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NISN</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas</th>
                                <th>Status Siswa</th>
                                <th>Status Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $s)
                                <tr>
                                    <td>{{ $s->nisn }}</td>
                                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                                    <td>
                                        @if($s->kelas)
                                            <span class="badge badge-success">{{ $s->kelas->nama_kelas }}</span>
                                        @else
                                            <span class="badge badge-warning">Belum ada kelas</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($s->status === 'aktif')
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($s->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($s->user->is_active)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Aktif</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i> Non-Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-user-times fa-3x" style="color: #d1d5db; margin-bottom: 16px;"></i>
                                        <p style="color: #9ca3af; margin: 0;">Belum ada data siswa</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswa->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $siswa->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection