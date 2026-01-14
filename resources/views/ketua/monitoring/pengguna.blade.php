{{-- resources/views/ketua/monitoring/pengguna.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Monitoring Data Pengguna')

@section('page-title', 'Monitoring Data Pengguna')
@section('page-subtitle', 'Lihat status aktif dan data pengguna')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
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
                <div class="filter-group" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <input type="text" id="searchTP" placeholder="Cari nama..."
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px;">
                    <select id="filterRoleTP"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Role</option>
                        <option value="ketua_pkbm">Ketua PKBM</option>
                        <option value="sekretaris">Sekretaris</option>
                        <option value="bendahara">Bendahara</option>
                        <option value="waka">Waka</option>
                        <option value="wali_kelas">Wali Kelas</option>
                        <option value="guru_pengajar">Guru Pengajar</option>
                    </select>
                    <select id="filterStatusTP"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
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
                                        <span class="badge badge-success">✓ Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">✗ Non-Aktif</span>
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

                @if($tenagaPendidik->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $tenagaPendidik->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabel Siswa --}}
        <div class="card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <h5><i class="fas fa-user-graduate"></i> Data Siswa</h5>
                <div class="filter-group" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <input type="text" id="searchSiswa" placeholder="Cari nama siswa..."
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; width: 180px;">
                    <select id="filterStatusSiswa"
                        style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
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
                                        <span class="badge badge-success">✓ Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($s->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($s->user->is_active)
                                        <span class="badge badge-success">✓ Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">✗ Non-Aktif</span>
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

                @if($siswa->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $siswa->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Filter for Tenaga Pendidik table
        const searchTP = document.getElementById('searchTP');
        const filterRoleTP = document.getElementById('filterRoleTP');
        const filterStatusTP = document.getElementById('filterStatusTP');
        const tpTable = document.querySelector('.card:nth-child(2) tbody');

        function filterTPTable() {
            const search = searchTP.value.toLowerCase();
            const role = filterRoleTP.value.toLowerCase();
            const status = filterStatusTP.value;
            const rows = tpTable.querySelectorAll('tr');

            rows.forEach(row => {
                let show = true;
                const nama = row.cells[1]?.textContent.toLowerCase() || '';
                const roleCell = row.cells[2]?.textContent.toLowerCase() || '';
                const statusCell = row.cells[4]?.textContent.toLowerCase() || '';

                if (search && !nama.includes(search)) show = false;
                if (role && !roleCell.includes(role.replace('_', ' '))) show = false;
                if (status === 'aktif' && !statusCell.includes('aktif')) show = false;
                if (status === 'nonaktif' && statusCell.includes('aktif') && !statusCell.includes('non')) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        searchTP.addEventListener('input', filterTPTable);
        filterRoleTP.addEventListener('change', filterTPTable);
        filterStatusTP.addEventListener('change', filterTPTable);

        // Filter for Siswa table
        const searchSiswa = document.getElementById('searchSiswa');
        const filterStatusSiswa = document.getElementById('filterStatusSiswa');
        const siswaTable = document.querySelector('.card:nth-child(3) tbody');

        function filterSiswaTable() {
            const search = searchSiswa.value.toLowerCase();
            const status = filterStatusSiswa.value;
            const rows = siswaTable.querySelectorAll('tr');

            rows.forEach(row => {
                let show = true;
                const nama = row.cells[1]?.textContent.toLowerCase() || '';
                const statusAkun = row.cells[4]?.textContent.toLowerCase() || '';

                if (search && !nama.includes(search)) show = false;
                if (status === 'aktif' && !statusAkun.includes('aktif')) show = false;
                if (status === 'nonaktif' && statusAkun.includes('aktif') && !statusAkun.includes('non')) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        searchSiswa.addEventListener('input', filterSiswaTable);
        filterStatusSiswa.addEventListener('change', filterSiswaTable);
    </script>
@endsection