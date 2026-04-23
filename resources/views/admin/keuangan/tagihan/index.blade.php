@extends('layouts.sneat')

@section('title', 'Kelola Tagihan')
@section('page-title', 'Kelola Tagihan')
@section('page-subtitle', 'Daftar tagihan semua siswa (Admin)')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
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

        /* Styling Tabel & UI */
        .table thead th {
            background: #f8f9fc;
            color: #4e73df;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
            vertical-align: middle;
            text-align: center;
        }

        .currency-font {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 10px;
        }

        /* Perbaikan Visual Identitas */
        .student-name {
            font-weight: 700;
            color: #1e293b;
            display: block;
        }

        .student-nisn {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }

        .cabang-badge {
            font-size: 10px;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 800;
            border: 1px solid #e2e8f0;
        }

        /* Reset Toolbar */
        .reset-toolbar {
            position: fixed;
            bottom: -80px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: bottom 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 90vw;
        }

        .reset-toolbar.show {
            bottom: 30px;
        }

        .reset-toolbar .selected-count {
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
        }

        .reset-toolbar .btn-reset {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .reset-toolbar .btn-reset:hover {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            transform: scale(1.05);
        }

        .reset-toolbar .btn-cancel-select {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .reset-toolbar .btn-cancel-select:hover {
            background: rgba(255,255,255,0.25);
        }

        .checkbox-cell {
            width: 40px;
            text-align: center;
        }

        .checkbox-cell input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        tr.selected-row {
            background-color: #eff6ff !important;
        }

        /* Buttons */
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
            width: 240px;
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

        /* Filter Dropdown */
        .filter-dropdown .dropdown-menu {
            min-width: 320px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px;
        }

        .filter-dropdown .dropdown-header {
            padding: 0 0 8px 0;
            margin-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 16px !important;
                padding: 16px;
            }

            #filterForm {
                flex-direction: column;
                width: 100%;
                align-items: stretch !important;
                gap: 12px !important;
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

            .filter-dropdown .dropdown-menu {
                width: 100%;
                max-width: none;
            }

            .search-input-wrapper {
                width: 100%;
            }

            .search-input {
                width: 100% !important;
            }

            .w-100-mobile {
                width: 100% !important;
            }

            .action-group-mobile {
                flex-wrap: wrap !important;
                width: 100%;
            }

            .action-group-mobile > * {
                flex: 1 1 auto;
                min-width: 0;
            }

            .action-group-mobile .btn {
                font-size: 12px;
                padding: 6px 10px;
                white-space: nowrap;
            }

            .action-group-mobile .btn-group {
                flex: 1 1 auto;
            }

            .action-group-mobile .btn-group .btn {
                width: 100%;
            }

            .table td, .table th {
                font-size: 12px;
                padding: 8px 6px;
            }

            .currency-font {
                font-size: 12px;
            }

            .student-name {
                font-size: 13px;
            }

            .badge-status {
                font-size: 9px;
                padding: 4px 8px;
            }

            .btn-group .btn.btn-sm {
                padding: 4px 8px;
                font-size: 11px;
            }

            .table-responsive {
                border: none !important;
            }
            .table-responsive table {
                border-collapse: separate;
                border-spacing: 0 1rem;
            }
            .table-responsive thead {
                display: none;
            }
            .table-responsive tbody tr {
                display: block;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 1rem;
            }
            .table-responsive tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right !important;
                padding: 0.75rem 1rem;
                border: none;
                border-bottom: 1px dashed #e2e8f0;
            }
            .table-responsive tbody td:last-child {
                border-bottom: none;
                display: block;
            }
            .table-responsive tbody td::before {
                content: attr(data-label);
                display: block;
                font-weight: 700;
                font-size: 0.75rem;
                color: #64748b;
                text-transform: uppercase;
                margin-right: 1rem;
                text-align: left;
            }
            .table-responsive tbody td .student-name {
                text-align: right;
                font-size: 14px;
            }
            .table-responsive tbody td .student-nisn {
                display: block;
                text-align: right;
            }
            .table-responsive tbody td .btn-group {
                display: flex;
                width: 100%;
                gap: 4px;
            }
            .table-responsive tbody td .btn-group .btn {
                flex: 1;
                border-radius: 6px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">
            {{-- ALERT TUNGGAKAN TAHUN SEBELUMNYA --}}
            @if(!empty($tunggakanSummary))
            <div class="alert alert-danger border-start border-danger border-4 shadow-sm mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1 text-danger"></i>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-danger mb-1">Tunggakan Tahun Sebelumnya</h6>
                        <p class="mb-2 small">
                            Terdapat <strong>{{ $tunggakanSummary['jumlah_siswa'] }} siswa</strong> dengan total tunggakan
                            <strong class="text-danger">Rp {{ number_format($tunggakanSummary['total_tunggakan'], 0, ',', '.') }}</strong>
                            dari tahun ajaran sebelumnya.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tunggakanSummary['per_tahun'] as $item)
                                <a href="{{ route('admin.keuangan.tagihan.index', ['tahun_ajaran_id' => $item['tahun_ajaran_id']]) }}"
                                   class="btn btn-outline-danger btn-sm fw-bold">
                                    <i class="fas fa-eye me-1"></i> {{ $item['nama_tahun'] }}
                                    ({{ $item['jumlah_siswa'] }} siswa - Rp {{ number_format($item['total'], 0, ',', '.') }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- TABEL UTAMA --}}
            <div class="card shadow mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    {{-- Left Group: Title & Filter --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 w-100-mobile">
                        {{-- Title --}}
                        <div>
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-list me-2"></i>Daftar Tagihan Siswa
                            </h6>
                            <small class="text-muted">{{ $siswaList ? $siswaList->total() : 0 }} siswa terdaftar</small>
                                <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    data-bs-auto-close="outside" data-bs-display="static">
                                    <span><i class="fas fa-filter me-1"></i> Filter</span>
                                </button>
                                <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="filterDropdown" style="z-index: 9999;">
                                    <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>

                                    {{-- Filter Tahun Ajaran --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Tahun Ajaran</label>
                                        <select name="tahun_ajaran_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @forelse($allTahunAjaran ?? [] as $ta)
                                                <option value="{{ $ta->id ?? '' }}" {{ optional($selectedYear)->id == ($ta->id ?? null) ? 'selected' : '' }}>
                                                    {{ $ta->nama_tahun_ajaran ?? 'Tahun Ajaran' }} {{ optional($ta)->is_active ? '(Aktif)' : '' }}
                                                </option>
                                            @empty
                                                <option value="">Tidak ada tahun ajaran</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    {{-- Filter Kelas --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Kelas</label>
                                        <select name="kelas_id" class="form-select form-select-sm">
                                            <option value="">Semua Kelas</option>
                                            @forelse($kelasList ?? [] as $kelas)
                                                <option value="{{ $kelas->id ?? '' }}" {{ (($filters ?? [])['kelas_id'] ?? '') == ($kelas->id ?? '') ? 'selected' : '' }}>
                                                    {{ $kelas->nama_kelas ?? 'Kelas' }} ({{ $kelas->jenjang ?? '-' }}) - {{ optional($kelas->cabang)->nama_cabang ?? 'Cabang tidak diketahui' }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Search Input --}}
                            <div class="search-input-wrapper w-100-mobile">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" id="searchInput" class="search-input"
                                    placeholder="Cari nama/NISN..." value="{{ ($filters ?? [])['search'] ?? '' }}"
                                    autocomplete="off">
                                <button type="button" class="clear-search {{ (($filters ?? [])['search'] ?? '') ? 'show' : '' }}"
                                    id="clearSearch" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Right Group: Action Buttons --}}
                    <div class="d-flex gap-2 action-group-mobile">
                        <a href="{{ route('admin.keuangan.tagihan.import') }}"
                            class="btn btn-outline-danger btn-sm shadow-sm fw-bold">
                            <i class="fas fa-file-import me-1"></i> Import
                        </a>
                        <a href="{{ route('admin.keuangan.tagihan.cetak-laporan', request()->query()) }}"
                            class="btn btn-outline-secondary btn-sm shadow-sm fw-bold" target="_blank">
                            <i class="fas fa-print me-1"></i> Cetak Laporan
                        </a>
                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-plus-circle me-1"></i> Buat Tagihan
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.bulk-create') }}">
                                    <i class="fas fa-users text-success me-2"></i> Tagihan Massal
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.create-custom') }}">
                                    <i class="fas fa-user-plus text-primary me-2"></i> Tagihan Custom
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.generate-spp') }}">
                                    <i class="fas fa-calendar-alt text-info me-2"></i> Generate SPP
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.keuangan.tagihan.duplicate') }}" class="btn btn-outline-info btn-sm shadow-sm fw-bold">
                            <i class="fas fa-copy me-1"></i> Duplikasi
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(!$siswaList || $siswaList->isEmpty())
                        <div class="text-center py-5 text-muted opacity-50">
                            <i class="fas fa-folder-open fa-4x mb-3"></i>
                            <h5>Data siswa tidak ditemukan</h5>
                        </div>
                    @else
                        {{-- Mobile Select All (Only visible on small screens since thead is hidden) --}}
                        <div class="d-md-none p-3 border-bottom d-flex align-items-center bg-light">
                            <input type="checkbox" id="selectAllMobile" class="me-2" style="width: 18px; height: 18px; accent-color: #3b82f6;" title="Pilih Semua">
                            <label for="selectAllMobile" class="fw-bold text-gray-700 mb-0" style="cursor: pointer;">Pilih Semua Siswa</label>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="checkbox-cell">
                                            <input type="checkbox" id="selectAll" title="Pilih Semua">
                                        </th>
                                        <th width="50">NO</th>
                                        <th class="text-start">IDENTITAS SISWA</th>
                                        <th>NISN</th>
                                        <th>KELAS</th>
                                        <th>CABANG</th>
                                        <th>TOTAL TAGIHAN</th>
                                        <th>SUDAH BAYAR</th>
                                        <th>SISA</th>
                                        <th>STATUS</th>
                                        <th width="120">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaList ?? [] as $index => $siswa)
                                        <tr data-siswa-id="{{ $siswa->id }}" data-siswa-name="{{ $siswa->nama_lengkap }}">
                                            <td class="checkbox-cell align-middle" data-label="PILIH UNTUK RESET">
                                                <input type="checkbox" class="row-checkbox" value="{{ $siswa->id }}">
                                            </td>
                                            <td class="text-center align-middle fw-bold text-gray-600" data-label="NO">
                                                {{ ($siswaList && method_exists($siswaList, 'firstItem')) ? $siswaList->firstItem() + $index : $index + 1 }}</td>
                                            <td class="align-middle" data-label="IDENTITAS SISWA">
                                                <div style="text-align: right;">
                                                    <span class="student-name">{{ $siswa->nama_lengkap }}</span>
                                                    <span class="student-nisn">Siswa Aktif</span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle fw-bold text-gray-800" data-label="NISN">{{ $siswa->nisn }}</td>
                                            <td class="text-center align-middle" data-label="KELAS">
                                                <span class="badge bg-primary px-2 py-1 fw-bold text-uppercase"
                                                    style="font-size: 10px;">
                                                    {{ optional($siswa->kelas)->nama_kelas ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle" data-label="CABANG">
                                                <span class="cabang-badge">{{ optional($siswa->cabang)->kode_cabang ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle currency-font text-dark" data-label="TOTAL TAGIHAN">
                                                Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="align-middle currency-font text-success" data-label="SUDAH BAYAR">
                                                Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}
                                            </td>
                                            <td data-label="SISA"
                                                class="align-middle currency-font {{ $siswa->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle" data-label="STATUS">
                                                @if($siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)
                                                    <span class="badge bg-success badge-status shadow-sm"><i
                                                            class="fas fa-check-circle"></i> LUNAS</span>
                                                @elseif($siswa->total_tagihan == 0)
                                                    <span class="badge bg-light border badge-status text-muted">KOSONG</span>
                                                @else
                                                    <span class="badge bg-danger badge-status shadow-sm"><i
                                                            class="fas fa-times-circle"></i> BELUM LUNAS</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle" data-label="AKSI">
                                                <div class="btn-group shadow-sm">
                                                    <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.tagihan.edit', $siswa->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit Tagihan">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.pembayaran.riwayat-siswa', $siswa->id) }}"
                                                        class="btn btn-sm btn-success" title="Riwayat Bayar">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.tagihan.cetak', $siswa->id) }}"
                                                        class="btn btn-sm btn-secondary" title="Cetak Tagihan" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light py-3 border-top">
                            <div class="d-flex justify-content-center">
                                {{ $siswaList ? $siswaList->withQueryString()->links() : '' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- INFORMASI TAMBAHAN --}}
            <div class="alert alert-warning border-start border-warning border-4 shadow-sm mt-2">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                    <small class="fw-bold text-gray-800">
                        Catatan: Total Tagihan mencakup seluruh kewajiban siswa di periode berjalan. Gunakan fitur "Buat
                        Tagihan Massal" untuk efisiensi waktu jika tagihan per jenjang bersifat seragam.
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Reset Toolbar --}}
    <div class="reset-toolbar" id="resetToolbar">
        <span class="selected-count">
            <i class="fas fa-check-square me-1"></i>
            <span id="selectedCount">0</span> siswa dipilih
        </span>
        <button type="button" class="btn-cancel-select" onclick="clearSelection()">
            <i class="fas fa-times me-1"></i> Batal
        </button>
        <button type="button" class="btn-reset" data-bs-toggle="modal" data-bs-target="#resetTagihanModal">
            <i class="fas fa-trash-restore me-1"></i> Reset Tagihan
        </button>
    </div>

    {{-- Modal Konfirmasi Reset Tagihan --}}
    <div class="modal fade" id="resetTagihanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white;">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>PERINGATAN: Reset Tagihan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3" style="animation: pulse 1.5s infinite;"></i>
                        <h5 class="fw-bold text-danger mb-2">TINDAKAN BERBAHAYA!</h5>
                    </div>

                    <div class="card bg-light border mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold small text-uppercase text-muted mb-2">
                                <i class="fas fa-users me-1"></i> Siswa yang akan direset:
                            </h6>
                            <div id="resetSiswaList" class="small" style="max-height: 150px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batalkan
                    </button>
                    <form id="formResetTagihan" action="{{ route('admin.keuangan.tagihan.reset-tagihan') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="siswa_ids" id="resetSiswaIds">
                        <input type="hidden" name="tahun_ajaran_id" value="{{ optional($selectedYear)->id }}">
                        <button type="button" class="btn btn-danger fw-bold" id="btnExecReset" onclick="executeReset()">
                            <i class="fas fa-trash-restore me-1"></i> Ya, Reset Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearSearch.classList.add('show');
                } else {
                    clearSearch.classList.remove('show');
                }
            });
        }

        if (clearSearch) {
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                clearSearch.classList.remove('show');
                searchInput.focus();
            });
        }

        // === Multi-select & Reset Tagihan Logic ===
        const selectAll = document.getElementById('selectAll');
        const selectAllMobile = document.getElementById('selectAllMobile');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const resetToolbar = document.getElementById('resetToolbar');
        const selectedCountEl = document.getElementById('selectedCount');

        function updateToolbar() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const count = checked.length;
            selectedCountEl.textContent = count;

            if (count > 0) {
                resetToolbar.classList.add('show');
            } else {
                resetToolbar.classList.remove('show');
            }

            // Update select all state
            const isAllSelected = checkboxes.length > 0 && checked.length === checkboxes.length;
            const isIndeterminate = checked.length > 0 && checked.length < checkboxes.length;
            
            if (selectAll) {
                selectAll.checked = isAllSelected;
                selectAll.indeterminate = isIndeterminate;
            }
            if (selectAllMobile) {
                selectAllMobile.checked = isAllSelected;
                selectAllMobile.indeterminate = isIndeterminate;
            }

            // Highlight selected rows
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                if (row) {
                    row.classList.toggle('selected-row', cb.checked);
                }
            });
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateToolbar();
            });
        }
        
        if (selectAllMobile) {
            selectAllMobile.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateToolbar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateToolbar);
        });

        function clearSelection() {
            checkboxes.forEach(cb => cb.checked = false);
            if (selectAll) selectAll.checked = false;
            if (selectAllMobile) selectAllMobile.checked = false;
            updateToolbar();
        }

        // Modal preparation
        const resetModal = document.getElementById('resetTagihanModal');
        if (resetModal) {
            resetModal.addEventListener('show.bs.modal', function() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                const ids = [];
                let listHtml = '';

                checked.forEach((cb, i) => {
                    ids.push(cb.value);
                    const row = cb.closest('tr');
                    const nameCell = row ? row.querySelector('.student-name') : null;
                    const name = nameCell ? nameCell.textContent.trim() : 'Siswa #' + cb.value;
                    listHtml += `<div class="d-flex align-items-center py-1 ${i > 0 ? 'border-top' : ''}">
                        <i class="fas fa-user-minus text-danger me-2"></i>
                        <span>${i + 1}. ${name}</span>
                    </div>`;
                });

                document.getElementById('resetSiswaIds').value = JSON.stringify(ids);
                document.getElementById('resetSiswaList').innerHTML = listHtml;
            });
        }

        function executeReset() {
            document.getElementById('formResetTagihan').submit();
        }
    </script>
@endsection