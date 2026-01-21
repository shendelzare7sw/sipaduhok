@extends('layouts.sneat')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola data seluruh siswa aktif dan alumni')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        /* Card & Layout */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 12px 12px 0 0;
        }

        /* Table Styles */
        .table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            padding: 12px 16px;
            text-align: left;
        }

        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            font-size: 14px;
        }

        .table tr:hover td {
            background: #f8fafc;
        }

        /* Buttons */
        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: white;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
            text-decoration: none;
        }

        /* Action Icon Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn.view {
            background: #eff6ff;
            color: #3b82f6;
        }

        .action-btn.view:hover {
            background: #dbeafe;
        }

        .action-btn.edit {
            background: #fffbeb;
            color: #f59e0b;
        }

        .action-btn.edit:hover {
            background: #fef3c7;
        }

        .action-btn.delete {
            background: #fef2f2;
            color: #ef4444;
        }

        .action-btn.delete:hover {
            background: #fee2e2;
        }

        /* Badges */
        .badge-class {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-status.aktif {
            background: #dcfce7;
            color: #166534;
        }

        .badge-status.lulus {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-status.pindah {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status.keluar {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Jenjang Badges */
        .badge-kb {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-tka {
            background: #fed7aa;
            color: #9a3412;
        }

        .badge-tkb {
            background: #fecaca;
            color: #991b1b;
        }

        .badge-sd {
            background: #dcfce7;
            color: #166534;
        }

        .badge-smp {
            background: #e0f2fe;
            color: #075985;
        }

        .badge-sma {
            background: #f3e8ff;
            color: #7c3aed;
        }

        /* Search Form */
        .search-form {
            display: flex;
            gap: 8px;
            align-items: center;
            position: relative;
        }

        .search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 8px 36px 8px 36px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            width: 280px;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            pointer-events: none;
        }

        .clear-search {
            position: absolute;
            right: 8px;
            background: #f1f5f9;
            border: none;
            border-radius: 4px;
            color: #64748b;
            cursor: pointer;
            padding: 4px 8px;
            font-size: 12px;
            transition: all 0.2s;
            display: none;
        }

        .clear-search:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .clear-search.show {
            display: block;
        }

        .btn-search {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-search:hover {
            background: #1d4ed8;
        }

        .search-info {
            padding: 12px 16px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            color: #1e40af;
        }

        .search-info .search-term {
            font-weight: 600;
        }

        .btn-clear-all {
            background: white;
            border: 1px solid #3b82f6;
            color: #2563eb;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-clear-all:hover {
            background: #eff6ff;
            text-decoration: none;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-dialog {
            position: relative;
            width: auto;
            max-width: 500px;
            margin: 1.75rem auto;
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 0;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }

        .modal-header.bg-danger {
            background: #dc2626 !important;
            border-bottom-color: rgba(255, 255, 255, 0.2);
        }

        .modal-header .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .modal-header.bg-danger .modal-title {
            color: white;
        }

        .modal-header .btn-close,
        .modal-header .btn-close-white {
            background: transparent;
            border: none;
            font-size: 24px;
            line-height: 1;
            color: #6b7280;
            cursor: pointer;
            padding: 8px;
            width: 40px;
            height: 40px;
            transition: all 0.2s;
            margin: 0 !important;
            opacity: 1;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 300;
        }

        .modal-header .btn-close-white {
            color: #ffffff !important;
            opacity: 1 !important;
            filter: brightness(1.2);
        }

        .modal-header .btn-close:hover,
        .modal-header .btn-close-white:hover {
            opacity: 0.8 !important;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .modal-body {
            padding: 24px;
            color: #374151;
            font-size: 14px;
            line-height: 1.6;
        }

        .modal-body strong {
            color: #111827;
        }

        .modal-body .text-muted {
            color: #6b7280;
            font-size: 13px;
        }

        .student-info-box {
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            margin: 12px 0;
            border: 1px solid #e5e7eb;
        }

        .student-info-box .student-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .student-info-box .student-details {
            font-size: 13px;
            color: #64748b;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .student-info-box .detail-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
        }

        .btn-modal-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-modal-secondary:hover {
            background: #e5e7eb;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        /* Alert/Success Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
    </style>

    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        {{-- Success Message --}}

        {{-- Search/Filter Info --}}
        @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
            <div class="search-info">
                <div>
                    <i class="fas fa-filter"></i>
                    Filter aktif:
                    @if(request('search'))
                        <span class="search-term">Pencarian: "{{ request('search') }}"</span>
                    @endif
                    @if(request('jenjang'))
                        <span class="search-term">Jenjang: {{ request('jenjang') }}</span>
                    @endif
                    @if(request('cabang_id'))
                        <span class="search-term">Cabang: {{ $cabangList->find(request('cabang_id'))->nama_cabang ?? '-' }}</span>
                    @endif
                    @if(request('status'))
                        <span class="search-term">Status: {{ ucfirst(request('status')) }}</span>
                    @endif
                    <small style="color: #64748b; margin-left: 8px;">({{ $siswa->total() }} data ditemukan)</small>
                </div>
                <a href="{{ route('admin.users.siswa') }}" class="btn-clear-all">
                    <i class="fas fa-times"></i>
                    Hapus Semua Filter
                </a>
            </div>
        @endif

        <div class="card">
            <div class="card-header" style="flex-direction: column; align-items: stretch;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Daftar Pengguna
                        </a>
                        <div>
                            <h5 style="margin: 0; font-weight: 700; color: #111827;">Daftar Siswa</h5>
                            <small style="color: #64748b;">Total: {{ $siswa->total() }} siswa</small>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.users.import-siswa') }}" class="btn-secondary"
                            style="background: #dcfce7; border-color: #86efac; color: #166534;">
                            <i class="fas fa-file-import"></i>
                            Import Excel
                        </a>
                        <a href="{{ route('admin.users.create-siswa') }}" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Tambah Siswa
                        </a>
                    </div>
                </div>

                {{-- Filter Form --}}
                <form action="{{ route('admin.users.siswa') }}" method="GET" id="filterForm">
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        {{-- Search --}}
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="searchInput" class="search-input"
                                placeholder="Cari nama, NIS, atau NISN..." value="{{ request('search') }}"
                                autocomplete="off" style="width: 250px;">
                            <button type="button" class="clear-search {{ request('search') ? 'show' : '' }}"
                                id="clearSearch" title="Hapus pencarian">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Filter Jenjang --}}
                        <select name="jenjang" class="search-input" style="width: 150px;">
                            <option value="">Semua Jenjang</option>
                            @foreach($jenjangs as $j)
                                <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>

                        {{-- Filter Cabang --}}
                        <select name="cabang_id" class="search-input" style="width: 180px;">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangList as $cabang)
                                <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filter Status --}}
                        <select name="status" class="search-input" style="width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>

                        <button type="submit" class="btn-search">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS / NISN</th>
                            <th style="width: 80px;">Jenjang</th>
                            <th>Kelas</th>
                            <th>Cabang</th>
                            <th style="width: 100px;">Status</th>
                            <th style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $s)
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: #64748b;">
                                    {{ $siswa->firstItem() + $index }}</td>
                                <td>
                                    <div style="font-weight: 600; color: #111827;">{{ $s->nama_lengkap }}</div>
                                    <small style="color: #64748b;">
                                        <i class="fas fa-{{ $s->jenis_kelamin == 'L' ? 'mars' : 'venus' }}"
                                            style="font-size: 10px;"></i>
                                        {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </small>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #111827; font-family: 'Courier New', monospace;">
                                        {{ $s->nis }}</div>
                                    <small style="color: #64748b; font-family: 'Courier New', monospace;">{{ $s->nisn }}</small>
                                </td>
                                <td>
                                    @if($s->kelas)
                                        @php
                                            $jenjangBadge = [
                                                'KB' => 'badge-kb',
                                                'TKA' => 'badge-tka',
                                                'TKB' => 'badge-tkb',
                                                'SD' => 'badge-sd',
                                                'SMP' => 'badge-smp',
                                                'SMA' => 'badge-sma',
                                            ][$s->kelas->jenjang] ?? 'badge-class';
                                        @endphp
                                        <span class="badge {{ $jenjangBadge }}"
                                            style="padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                            {{ $s->kelas->jenjang }}
                                        </span>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($s->kelas)
                                        <span class="badge-class">
                                            <i class="fas fa-door-open" style="font-size: 10px;"></i>
                                            {{ $s->kelas->nama_kelas }}
                                        </span>
                                    @else
                                        <span style="color: #ef4444; font-size: 12px;">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Belum masuk kelas
                                        </span>
                                    @endif
                                </td>
                                <td style="color: #475569;">{{ $s->cabang->nama_cabang ?? '-' }}</td>
                                <td>
                                    <span class="badge-status {{ $s->status }}">
                                        @if($s->status === 'aktif')
                                            <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                        @elseif($s->status === 'lulus')
                                            <i class="fas fa-graduation-cap" style="font-size: 10px;"></i>
                                        @elseif($s->status === 'pindah')
                                            <i class="fas fa-exchange-alt" style="font-size: 10px;"></i>
                                        @else
                                            <i class="fas fa-times-circle" style="font-size: 10px;"></i>
                                        @endif
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; justify-content: center; gap: 6px;">
                                        {{-- View Button --}}
                                        <a href="{{ route('admin.users.show-siswa', $s->id) }}" class="action-btn view"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.users.edit-siswa', $s->id) }}" class="action-btn edit"
                                            title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete Button --}}
                                        <button type="button" class="action-btn delete" title="Hapus Data"
                                            data-bs-toggle="modal" data-bs-target="#deleteSiswaModal{{ $s->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fas fa-user-graduate fa-3x" style="margin-bottom: 12px; opacity: 0.5;"></i>
                                    <div style="font-size: 16px; font-weight: 500;">
                                        @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
                                            Tidak ada data siswa yang sesuai dengan filter yang dipilih
                                        @else
                                            Belum ada data siswa
                                        @endif
                                    </div>
                                    <small>
                                        @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
                                            Coba filter lain atau <a href="{{ route('admin.users.siswa') }}"
                                                style="color: #2563eb; text-decoration: underline;">hapus semua filter</a>
                                        @else
                                            Silakan tambah data siswa baru
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswa->hasPages())
                <div style="padding: 20px;">
                    {{ $siswa->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modals for Siswa --}}
    @foreach($siswa as $s)
        <div class="modal fade" id="deleteSiswaModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus data siswa:</p>
                        <div class="student-info-box">
                            <div class="student-name">
                                <i class="fas fa-user-graduate" style="color: #3b82f6;"></i>
                                {{ $s->nama_lengkap }}
                            </div>
                            <div class="student-details">
                                <div class="detail-item">
                                    <i class="fas fa-id-card" style="color: #64748b; font-size: 11px;"></i>
                                    <span>NIS: {{ $s->nis }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-hashtag" style="color: #64748b; font-size: 11px;"></i>
                                    <span>NISN: {{ $s->nisn }}</span>
                                </div>
                                @if($s->kelas)
                                    <div class="detail-item">
                                        <i class="fas fa-door-open" style="color: #64748b; font-size: 11px;"></i>
                                        <span>{{ $s->kelas->nama_kelas }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p style="margin-top: 12px;">
                            <i class="fas fa-info-circle" style="color: #dc2626;"></i>
                            <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait
                                termasuk akun login siswa.</small>
                        </p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <form action="{{ route('admin.users.delete-siswa', $s->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');

        // Show/hide clear button
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (this.value.length > 0) {
                    clearSearch.classList.add('show');
                } else {
                    clearSearch.classList.remove('show');
                }
            });
        }

        // Clear search
        if (clearSearch) {
            clearSearch.addEventListener('click', function () {
                searchInput.value = '';
                clearSearch.classList.remove('show');
                searchInput.focus();
            });
        }
    </script>
@endsection