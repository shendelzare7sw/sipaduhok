@extends('layouts.sneat')

@section('title', 'Data Orang Tua')
@section('page-title', 'Data Orang Tua')
@section('page-subtitle', 'Kelola akun login orang tua siswa')

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

        .action-btn[style*="background: #eff6ff"]:hover {
            background: #dbeafe !important;
        }

        .action-btn[style*="background: #fef3c7"]:hover {
            background: #fde68a !important;
        }

        .action-btn.toggle-active {
            background: #dcfce7;
            color: #166534;
        }

        .action-btn.toggle-active:hover {
            background: #bbf7d0;
        }

        .action-btn.toggle-inactive {
            background: #fef3c7;
            color: #92400e;
        }

        .action-btn.toggle-inactive:hover {
            background: #fef9c7;
        }

        .action-btn.delete {
            background: #fef2f2;
            color: #ef4444;
        }

        .action-btn.delete:hover {
            background: #fee2e2;
        }

        /* Badges */
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

        .badge-status.nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-class {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
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

        .modal-header.bg-warning {
            background: #f59e0b !important;
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

        .modal-header.bg-danger .modal-title,
        .modal-header.bg-warning .modal-title {
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

        .info-box {
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            margin: 12px 0;
            border: 1px solid #e5e7eb;
        }

        .info-box .info-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box .info-details {
            font-size: 13px;
            color: #64748b;
            display: flex;
            flex-direction: column;
            gap: 6px;
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

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
    /* Responsive Styles */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 16px !important;
                padding: 16px;
            }

            .card-header > div {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            /* Title section on mobile */
            .card-header > div:first-child {
                margin-bottom: 8px;
            }
            
            .w-100-mobile {
                width: 100% !important;
            }

            /* Filter form on mobile */
            .search-form {
                flex-direction: column;
                width: 100%;
                align-items: stretch !important;
                gap: 12px !important;
            }

            .filter-dropdown .dropdown-menu {
                width: 100%;
                max-width: none;
            }
            
            .dropdown {
                width: 100%;
            }
            
            .dropdown-toggle {
                width: 100%;
                justify-content: space-between;
                display: flex;
                align-items: center;
            }

            .search-input-wrapper {
                width: 100%;
            }

            .search-input {
                width: 100% !important;
            }
            
            .btn-search {
                width: 100%;
                justify-content: center;
            }

            /* Action buttons on mobile */
            .card-header > div:last-child {
                flex-direction: row;
                gap: 8px !important;
                justify-content: stretch;
            }

            .card-header > div:last-child .btn,
            .card-header > div:last-child .btn-group {
                flex: 1;
                justify-content: center;
            }
            
            .btn-group {
                width: auto; 
                flex: 0 0 auto !important;
            }
            
            /* Make "Tambah" text shorter on mobile if needed or hide icon */
            /* Removed CSS hack in favor of HTML classes */
        }
    </style>

    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        {{-- Success Message --}}

        {{-- Import Warnings --}}
        @if(session('import_warnings'))
            <div class="alert alert-warning" style="background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; display: block;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Beberapa data dilewati saat import:
                </div>
                <ul style="margin: 0; padding-left: 24px; font-size: 13px;">
                    @foreach(session('import_warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                {{-- Left Group: Title & Filter --}}
                <div class="d-flex flex-wrap align-items-center gap-3 w-100-mobile">
                    {{-- Title Group --}}
                    <div class="d-flex gap-2 align-items-center justify-content-between w-100-mobile">
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Daftar Orang Tua</h5>
                                <small class="text-muted">Total: {{ $orangTua->total() }} akun orang tua</small>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <form action="{{ route('admin.users.orang-tua') }}" method="GET" id="filterForm" class="search-form d-flex gap-2 align-items-center w-100-mobile">
                        {{-- Filter Dropdown --}}
                        <div class="dropdown filter-dropdown w-100-mobile">
                            <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false" 
                                data-bs-auto-close="outside" data-bs-display="static">
                                <span><i class="fas fa-filter me-1"></i> Filter</span>
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="filterDropdown" style="min-width: 300px; z-index: 9999;">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>

                                {{-- Filter Jenjang Anak --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Jenjang Anak</label>
                                    <select name="jenjang" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang Anak</option>
                                        @foreach($jenjangs as $j)
                                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Cabang --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Cabang</label>
                                    <select name="cabang_id" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangList as $cabang)
                                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Status Akun --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Status Akun</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                </div>
                            </div>
                        </div>

                        {{-- Search Input --}}
                        <div class="search-input-wrapper w-100-mobile">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="searchInput" class="search-input"
                                placeholder="Cari..." value="{{ request('search') }}"
                                autocomplete="off" style="width: 200px;">
                            <button type="button" class="clear-search {{ request('search') ? 'show' : '' }}"
                                id="clearSearch" title="Hapus pencarian">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Right Group: Actions --}}
                <div class="d-flex gap-2 action-group-mobile">
                    <form action="{{ route('admin.users.bulk-delete-orang-tua') }}" method="POST" id="bulkDeleteForm" style="display: none;">
                        @csrf
                        <input type="hidden" name="ids" id="bulkDeleteIds">
                        <button type="button" class="btn btn-danger" onclick="showBulkDeleteModal()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    <!-- Dropdown Menu Aksi -->
                    <div class="btn-group">
                        <button type="button" class="btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> <span class="d-none d-md-inline">Menu Aksi</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('admin.users.orang-tua.print') }}?{{ http_build_query(request()->all()) }}" class="dropdown-item" target="_blank">
                                    <i class="fas fa-print me-2"></i> Cetak Data (PDF)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.import-orang-tua') }}" class="dropdown-item">
                                    <i class="fas fa-file-import me-2"></i> Import Excel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.orang-tua-template') }}" class="dropdown-item">
                                    <i class="fas fa-download me-2"></i> Download Template
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.users.orang-tua.create') }}" class="btn-primary" style="white-space: nowrap;">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-md-inline">Tambah Orang Tua</span>
                        <span class="d-md-none">Tambah</span>
                    </a>
                </div>
            </div>

                <div style="overflow-x: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th style="width: 60px;">No</th>
                            <th>Nama Orang Tua</th>
                            <th>Username / Email</th>
                            <th>Anak (Siswa)</th>
                            <th style="width: 100px;">Status Akun</th>
                            <th style="text-align: center; width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                        <tbody>
                            @forelse($orangTua as $index => $ortu)
                                <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" class="form-check-input select-item" value="{{ $ortu->id }}">
                                </td>
                                <td style="text-align: center; font-weight: 600; color: #64748b;">
                                    {{ $orangTua->firstItem() + $index }}</td>
                                    <td>
                                        <div style="font-weight: 600; color: #111827;">{{ $ortu->name }}</div>
                                        <small style="color: #64748b;">
                                            <i class="fas fa-user-friends" style="font-size: 10px;"></i>
                                            Orang Tua
                                        </small>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #111827; font-family: 'Courier New', monospace;">
                                            {{ $ortu->username }}</div>
                                        <small style="color: #64748b;">{{ $ortu->email }}</small>
                                    </td>
                                    <td>
                                        @if($ortu->studentParents->count() > 0)
                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                @foreach($ortu->studentParents as $sp)
                                                    <div style="display: flex; align-items: center; gap: 6px;">
                                                        @php
                                                            $jenjangBadge = isset($sp->siswa->kelas->jenjang) ? [
                                                                'KB' => 'badge-kb',
                                                                'TKA' => 'badge-tka',
                                                                'TKB' => 'badge-tkb',
                                                                'SD' => 'badge-sd',
                                                                'SMP' => 'badge-smp',
                                                                'SMA' => 'badge-sma',
                                                            ][$sp->siswa->kelas->jenjang] ?? 'badge-class' : 'badge-class';
                                                        @endphp
                                                        @if($sp->siswa->kelas)
                                                            <span class="badge {{ $jenjangBadge }}"
                                                                style="padding: 2px 6px; font-size: 10px;">
                                                                {{ $sp->siswa->kelas->jenjang }}
                                                            </span>
                                                        @endif
                                                        <span
                                                            style="font-size: 13px; color: #475569;">{{ $sp->siswa->nama_lengkap }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="color: #ef4444; font-size: 12px;">
                                                <i class="fas fa-exclamation-circle"></i>
                                                Belum ada anak terdaftar
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-status {{ $ortu->is_active ? 'aktif' : 'nonaktif' }}">
                                            <i class="fas fa-{{ $ortu->is_active ? 'check-circle' : 'times-circle' }}"
                                                style="font-size: 10px;"></i>
                                            {{ $ortu->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; justify-content: center; gap: 6px;">
                                            {{-- Detail Button --}}
                                            <a href="{{ route('admin.users.show-orang-tua', $ortu->id) }}" class="action-btn"
                                                style="background: #eff6ff; color: #1e40af;" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- Edit Button --}}
                                            <a href="{{ route('admin.users.edit-orang-tua', $ortu->id) }}" class="action-btn"
                                                style="background: #fef3c7; color: #92400e;" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- Toggle Status Button --}}
                                            <button type="button"
                                                class="action-btn {{ $ortu->is_active ? 'toggle-inactive' : 'toggle-active' }}"
                                                title="{{ $ortu->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                data-bs-toggle="modal" data-bs-target="#toggleStatusModal{{ $ortu->id }}">
                                                <i class="fas fa-{{ $ortu->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                            {{-- Delete Button --}}
                                            <button type="button" class="action-btn delete" title="Hapus Akun"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ortu->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="fas fa-users fa-3x" style="margin-bottom: 12px; opacity: 0.5;"></i>
                                        <div style="font-size: 16px; font-weight: 500;">
                                            @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
                                                Tidak ada data orang tua yang sesuai dengan filter yang dipilih
                                            @else
                                                Belum ada data orang tua
                                            @endif
                                        </div>
                                        <small>
                                            @if(request('search') || request('jenjang') || request('cabang_id') || request('status'))
                                                Coba filter lain atau <a href="{{ route('admin.users.orang-tua') }}"
                                                    style="color: #2563eb; text-decoration: underline;">hapus semua filter</a>
                                            @else
                                                Orang tua akan terdaftar otomatis saat menambahkan siswa baru
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orangTua->hasPages())
                    <div style="padding: 20px;">
                        {{ $orangTua->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Toggle Status Modals --}}
        @foreach($orangTua as $ortu)
            <div class="modal fade" id="toggleStatusModal{{ $ortu->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header {{ $ortu->is_active ? 'bg-warning' : 'bg-success' }} text-white">
                            <h5 class="modal-title fw-bold" style="color: white !important;">
                                <i class="fas fa-{{ $ortu->is_active ? 'ban' : 'check' }} me-2"></i>
                                Konfirmasi {{ $ortu->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Akun
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin {{ $ortu->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun orang tua:
                            </p>
                            <div class="info-box">
                                <div class="info-name">
                                    <i class="fas fa-user" style="color: #3b82f6;"></i>
                                    {{ $ortu->name }}
                                </div>
                                <div class="info-details">
                                    <div>
                                        <i class="fas fa-at" style="color: #64748b; font-size: 11px;"></i>
                                        Username: {{ $ortu->username }}
                                    </div>
                                    @if($ortu->studentParents->count() > 0)
                                        <div>
                                            <i class="fas fa-child" style="color: #64748b; font-size: 11px;"></i>
                                            Anak: {{ $ortu->studentParents->pluck('siswa.nama_lengkap')->join(', ') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($ortu->is_active)
                                <p style="margin-top: 12px;">
                                    <i class="fas fa-info-circle" style="color: #f59e0b;"></i>
                                    <small class="text-muted">Orang tua yang dinonaktifkan tidak dapat login ke sistem.</small>
                                </p>
                            @else
                                <p style="margin-top: 12px;">
                                    <i class="fas fa-info-circle" style="color: #10b981;"></i>
                                    <small class="text-muted">Orang tua yang diaktifkan dapat login dan mengakses sistem
                                        kembali.</small>
                                </p>
                            @endif
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Batal
                            </button>
                            <form action="{{ route('admin.users.toggle-orang-tua-status', $ortu->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn {{ $ortu->is_active ? 'btn-warning' : 'btn-primary' }}"
                                    style="background: {{ $ortu->is_active ? '#f59e0b' : '#10b981' }};">
                                    <i class="fas fa-{{ $ortu->is_active ? 'ban' : 'check' }}"></i>
                                    Ya, {{ $ortu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Delete Modals --}}
        @foreach($orangTua as $ortu)
            <div class="modal fade" id="deleteModal{{ $ortu->id }}" tabindex="-1" aria-hidden="true">
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
                            <p>Apakah Anda yakin ingin menghapus akun orang tua:</p>
                            <div class="info-box">
                                <div class="info-name">
                                    <i class="fas fa-user" style="color: #3b82f6;"></i>
                                    {{ $ortu->name }}
                                </div>
                                <div class="info-details">
                                    <div>
                                        <i class="fas fa-at" style="color: #64748b; font-size: 11px;"></i>
                                        Username: {{ $ortu->username }}
                                    </div>
                                    @if($ortu->studentParents->count() > 0)
                                        <div>
                                            <i class="fas fa-child" style="color: #64748b; font-size: 11px;"></i>
                                            Anak: {{ $ortu->studentParents->pluck('siswa.nama_lengkap')->join(', ') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <p style="margin-top: 12px;">
                                <i class="fas fa-info-circle" style="color: #dc2626;"></i>
                                <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus akun login
                                    orang tua.</small>
                            </p>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                                Batal
                            </button>
                            <form action="{{ route('admin.users.delete-orang-tua', $ortu->id) }}" method="POST"
                                style="display: inline;">
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

            // Bulk Selection Logic
            document.addEventListener('DOMContentLoaded', function() {
                const selectAll = document.getElementById('selectAll');
                const selectItems = document.querySelectorAll('.select-item');
                const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                const bulkDeleteIds = document.getElementById('bulkDeleteIds');

                function updateBulkDeleteButton() {
                    const selectedCount = document.querySelectorAll('.select-item:checked').length;
                    if (selectedCount > 0) {
                        bulkDeleteForm.style.display = 'block';
                    } else {
                        bulkDeleteForm.style.display = 'none';
                    }
                }

                if(selectAll) {
                    selectAll.addEventListener('change', function() {
                        selectItems.forEach(item => {
                            item.checked = this.checked;
                        });
                        updateBulkDeleteButton();
                    });
                }

                selectItems.forEach(item => {
                    item.addEventListener('change', function() {
                        const allChecked = document.querySelectorAll('.select-item:checked').length === selectItems.length;
                        if(selectAll) selectAll.checked = allChecked;
                        updateBulkDeleteButton();
                    });
                });
            });

            function showBulkDeleteModal() {
                const selectedItems = document.querySelectorAll('.select-item:checked');
                if (selectedItems.length === 0) return;

                const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
                document.getElementById('selectedCount').textContent = selectedItems.length;
                modal.show();
            }

            function submitBulkDelete() {
                const selectedItems = document.querySelectorAll('.select-item:checked');
                const ids = Array.from(selectedItems).map(item => item.value);
                
                const form = document.getElementById('bulkDeleteForm');
                // Clear existing hidden inputs for ids
                const existingInputs = form.querySelectorAll('input[name="ids[]"]');
                existingInputs.forEach(input => input.remove());

                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                form.submit();
            }
        </script>

    <!-- Modal Konfirmasi Bulk Delete -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <span id="selectedCount" style="font-weight: bold;"></span> data terpilih? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="submitBulkDelete()">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection