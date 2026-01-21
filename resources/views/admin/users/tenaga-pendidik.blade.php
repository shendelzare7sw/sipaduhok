@extends('layouts.sneat')

@section('title', 'Data Tenaga Pendidik')
@section('page-title', 'Data Tenaga Pendidik')
@section('page-subtitle', 'Kelola akun Ketua PKBM, Sekretaris, Bendahara, Wali Kelas, dan Guru')

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
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary:hover {
            background: #f8fafc;
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
        .badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-role {
            background: #e0f2fe;
            color: #0284c7;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
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
            z-index: 1055;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
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
        @if(request('search') || request('role'))
            <div class="search-info">
                <div>
                    @if(request('search'))
                        <i class="fas fa-search"></i>
                        Menampilkan hasil pencarian untuk: <span class="search-term">"{{ request('search') }}"</span>
                    @endif
                    @if(request('role'))
                        @if(request('search')) • @endif
                        <i class="fas fa-filter"></i>
                        Role: <span class="search-term">{{ $roles[request('role')] ?? request('role') }}</span>
                    @endif
                    <small style="color: #64748b; margin-left: 8px;">({{ $tenagaPendidik->total() }} data ditemukan)</small>
                </div>
                <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn-clear-all">
                    <i class="fas fa-times"></i>
                    Hapus Filter
                </a>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Daftar Pengguna
                    </a>
                    <div>
                        <h5 style="margin: 0; font-weight: 700; color: #111827;">Daftar Tenaga Pendidik</h5>
                        <small style="color: #64748b;">Total: {{ $tenagaPendidik->total() }} tenaga pendidik</small>
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <form action="{{ route('admin.users.tenaga-pendidik') }}" method="GET" class="search-form">
                        <select name="role" id="roleFilter" class="search-input" style="width: 200px; padding-right: 12px;"
                            onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            @foreach($roles as $roleKey => $roleLabel)
                                <option value="{{ $roleKey }}" {{ request('role') == $roleKey ? 'selected' : '' }}>
                                    {{ $roleLabel }}
                                </option>
                            @endforeach
                        </select>
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="searchInput" class="search-input"
                                placeholder="Cari nama atau NIP..." value="{{ request('search') }}" autocomplete="off">
                            <button type="button" class="clear-search {{ request('search') ? 'show' : '' }}"
                                id="clearSearch" title="Hapus pencarian">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i>
                            Cari
                        </button>
                    </form>
                    <a href="{{ route('admin.users.import-tenaga-pendidik') }}" class="btn-secondary"
                        style="background: #ede9fe; border-color: #c4b5fd; color: #6d28d9;">
                        <i class="fas fa-file-import"></i>
                        Import Excel
                    </a>
                    <a href="{{ route('admin.users.create-tenaga-pendidik') }}" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah Baru
                    </a>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Role / Jabatan</th>
                            <th>Email</th>
                            <th style="width: 100px;">Status</th>
                            <th style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenagaPendidik as $index => $tp)
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: #64748b;">
                                    {{ $tenagaPendidik->firstItem() + $index }}</td>
                                <td>
                                    <div style="font-weight: 600; color: #111827;">{{ $tp->nama_lengkap }}</div>
                                    <small style="color: #64748b;">
                                        <i class="fas fa-phone" style="font-size: 10px;"></i>
                                        {{ $tp->telepon ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span
                                        style="font-family: 'Courier New', monospace; color: #475569;">{{ $tp->nip ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-role">{{ ucwords(str_replace('_', ' ', $tp->user->role)) }}</span>
                                </td>
                                <td style="color: #475569;">{{ $tp->email }}</td>
                                <td>
                                    @if($tp->user->is_active)
                                        <span class="badge badge-active">
                                            <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-inactive">
                                            <i class="fas fa-times-circle" style="font-size: 10px;"></i>
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; justify-content: center; gap: 6px;">
                                        {{-- View Button --}}
                                        <a href="{{ route('admin.users.show-tenaga-pendidik', $tp->id) }}"
                                            class="action-btn view" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.users.edit-tenaga-pendidik', $tp->id) }}"
                                            class="action-btn edit" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete Button --}}
                                        <button type="button" class="action-btn delete" title="Hapus Data"
                                            data-bs-toggle="modal" data-bs-target="#deleteTenagaPendidikModal{{ $tp->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fas fa-chalkboard-teacher fa-3x" style="margin-bottom: 12px; opacity: 0.5;"></i>
                                    <div style="font-size: 16px; font-weight: 500;">
                                        @if(request('search'))
                                            Tidak ada tenaga pendidik yang sesuai dengan pencarian "{{ request('search') }}"
                                        @else
                                            Tidak ada data ditemukan
                                        @endif
                                    </div>
                                    <small>
                                        @if(request('search'))
                                            Coba kata kunci lain atau <a href="{{ route('admin.users.tenaga-pendidik') }}"
                                                style="color: #2563eb; text-decoration: underline;">hapus filter</a>
                                        @else
                                            Silakan tambah data tenaga pendidik baru
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tenagaPendidik->hasPages())
                <div style="padding: 20px;">
                    {{ $tenagaPendidik->appends(['search' => request('search'), 'role' => request('role')])->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modals for Tenaga Pendidik --}}
    @foreach($tenagaPendidik as $tp)
        <div class="modal fade" id="deleteTenagaPendidikModal{{ $tp->id }}" tabindex="-1" aria-hidden="true">
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
                        <p>Apakah Anda yakin ingin menghapus data tenaga pendidik:</p>
                        <div
                            style="background: #f9fafb; padding: 12px; border-radius: 8px; margin: 12px 0; border: 1px solid #e5e7eb;">
                            <div style="font-weight: 600; color: #111827; margin-bottom: 4px;">
                                <i class="fas fa-chalkboard-teacher" style="color: #3b82f6;"></i>
                                {{ $tp->nama_lengkap }}
                            </div>
                            <small
                                style="color: #64748b;">{{ $tp->user->role ? ucwords(str_replace('_', ' ', $tp->user->role)) : '-' }}
                                • {{ $tp->email }}</small>
                        </div>
                        <p style="margin-top: 12px;">
                            <i class="fas fa-info-circle" style="color: #dc2626;"></i>
                            <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait
                                termasuk akun login.</small>
                        </p>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <form action="{{ route('admin.users.delete-tenaga-pendidik', $tp->id) }}" method="POST"
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
    </script>
@endsection