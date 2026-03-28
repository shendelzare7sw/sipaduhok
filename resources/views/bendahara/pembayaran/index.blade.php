@extends('layouts.sneat')

@section('title', 'Kelola Pembayaran')
@section('page-title', 'Kelola Pembayaran')
@section('page-subtitle', 'Daftar dan validasi pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        /* Stat Card Vibrant */
        .stat-card {
            padding: 24px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            height: 100%;
            color: white;
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-content {
            position: relative;
            z-index: 2;
        }

        .stat-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .stat-label-sub {
            font-size: 13px;
            opacity: 0.8;
        }

        .stat-icon-bg {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 60px;
            opacity: 0.15;
            z-index: 1;
        }

        .bg-gradient-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

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

        /* Table Styling */
        .table thead th {
            background-color: #f8f9fc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #4e73df;
            border-bottom: 2px solid #e3e6f0;
            vertical-align: middle;
        }

        .currency-font {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
        }

        .badge-custom {
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 11px;
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

            .table-responsive {
                border: none;
            }
            .table thead {
                display: none;
            }
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                padding: 10px;
                border: 1px solid #e5e7eb;
            }
            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: none;
                padding: 8px 0;
                border-bottom: 1px dashed #e5e7eb;
                text-align: right;
            }
            .table tbody td > div {
                text-align: right;
            }
            .table tbody td:last-child {
                border-bottom: none;
                justify-content: center;
                gap: 10px;
                padding-top: 15px;
            }
            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #64748b;
                font-size: 0.75rem;
                text-transform: uppercase;
                margin-right: 15px;
                text-align: left;
            }
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">

            {{-- Statistik Cards --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="stat-card bg-gradient-orange shadow">
                        <div class="stat-content">
                            <div class="stat-title">Menunggu Validasi</div>
                            <div class="stat-number">{{ $stats['pending'] }}</div>
                            <div class="stat-label-sub">Transaksi Pending</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-history"></i></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card bg-gradient-green shadow">
                        <div class="stat-content">
                            <div class="stat-title">Pembayaran Disetujui</div>
                            <div class="stat-number">{{ $stats['disetujui'] }}</div>
                            <div class="stat-label-sub">Tervalidasi Sistem</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card bg-gradient-red shadow">
                        <div class="stat-content">
                            <div class="stat-title">Pembayaran Ditolak</div>
                            <div class="stat-number">{{ $stats['ditolak'] }}</div>
                            <div class="stat-label-sub">Transaksi Dibatalkan</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>


            {{-- Tabel Pembayaran --}}
            <div class="card shadow mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    {{-- Left Group: Title & Filter --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 w-100-mobile">
                        {{-- Title --}}
                        <div>
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-list me-2"></i>Rincian Transaksi Masuk
                            </h6>
                            <small class="text-muted">Total: {{ $pembayaranList->total() }} transaksi</small>
                        </div>

                        {{-- Filter Form --}}
                        <form action="{{ route('bendahara.pembayaran.index') }}" method="GET" id="filterForm" class="d-flex gap-2 align-items-center w-100-mobile">
                            {{-- Filter Dropdown --}}
                            <div class="dropdown filter-dropdown w-100-mobile">
                                <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    data-bs-auto-close="outside" data-bs-display="static">
                                    <span><i class="fas fa-filter me-1"></i> Filter</span>
                                </button>
                                <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="filterDropdown" style="z-index: 9999;">
                                    <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>

                                    {{-- Filter Status --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Status Validasi</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="">Semua Status</option>
                                            <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="disetujui" {{ ($filters['status'] ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="ditolak" {{ ($filters['status'] ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </div>

                                    {{-- Filter Metode --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Metode Pembayaran</label>
                                        <select name="metode" class="form-select form-select-sm">
                                            <option value="">Semua Metode</option>
                                            <option value="tunai" {{ ($filters['metode'] ?? '') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                            <option value="transfer" {{ ($filters['metode'] ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                            <option value="midtrans" {{ ($filters['metode'] ?? '') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                                        </select>
                                    </div>

                                    {{-- Filter Kelas --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Kelas</label>
                                        <select name="kelas_id" class="form-select form-select-sm">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}" {{ ($filters['kelas_id'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                                    {{ $kelas->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filter Tanggal --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Dari Tanggal</label>
                                        <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ $filters['tanggal_dari'] ?? '' }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                                        <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ $filters['tanggal_sampai'] ?? '' }}">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                        <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Search Input --}}
                            <div class="search-input-wrapper w-100-mobile">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" id="searchInput" class="search-input"
                                    placeholder="Cari siswa/kode..." value="{{ $filters['search'] ?? '' }}"
                                    autocomplete="off">
                                <button type="button" class="clear-search {{ ($filters['search'] ?? '') ? 'show' : '' }}"
                                    id="clearSearch" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($pembayaranList->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p>Tidak ada data pembayaran yang ditemukan.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">NO</th>
                                        <th>KODE</th>
                                        <th>IDENTITAS SISWA</th>
                                        <th>JENIS TAGIHAN</th>
                                        <th class="text-end">JUMLAH</th>
                                        <th class="text-center">METODE</th>
                                        <th>TANGGAL</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" width="80">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembayaranList as $index => $pembayaran)
                                        <tr>
                                            <td data-label="NO" class="text-center align-middle fw-bold text-gray-600">
                                                {{ $pembayaranList->firstItem() + $index }}</td>
                                            <td data-label="KODE" class="align-middle text-start text-md-center">
                                                <code class="fw-bold text-primary small">{{ $pembayaran->kode_pembayaran }}</code>
                                                @if($pembayaran->order_id && $pembayaran->group_transactions_count > 1)
                                                    <div class="mt-1">
                                                        <span class="badge"
                                                            style="background-color: #e0f2fe; color: #0284c7; font-size: 10px; border: 1px solid #bae6fd;"
                                                            data-bs-toggle="tooltip"
                                                            title="Bagian dari transaksi gabungan ({{ $pembayaran->group_transactions_count }} tagihan)">
                                                            <i class="fas fa-layer-group me-1"></i> Gabungan
                                                            ({{ $pembayaran->group_transactions_count }})
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td data-label="IDENTITAS SISWA" class="align-middle text-end text-md-start">
                                                <div class="fw-bold text-gray-900">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</div>
                                                <small class="text-muted fw-bold text-uppercase">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</small>
                                            </td>
                                            <td data-label="JENIS TAGIHAN" class="align-middle small fw-bold text-muted text-end text-md-start">
                                                {{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}
                                            </td>
                                            <td data-label="JUMLAH" class="align-middle currency-font text-dark text-end">
                                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                            </td>
                                            <td data-label="METODE" class="align-middle text-end text-md-center">
                                                @if($pembayaran->metode_pembayaran === 'tunai')
                                                    <span class="badge bg-primary badge-custom shadow-sm">TUNAI</span>
                                                @elseif($pembayaran->metode_pembayaran === 'transfer')
                                                    <span class="badge bg-success badge-custom shadow-sm">TRANSFER</span>
                                                @else
                                                    <span class="badge bg-info badge-custom shadow-sm">MIDTRANS</span>
                                                @endif
                                            </td>
                                            <td data-label="TANGGAL" class="align-middle small fw-bold text-end text-md-start">
                                                {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y') : '-' }}
                                            </td>
                                            <td data-label="STATUS" class="align-middle text-end text-md-center">
                                                @if($pembayaran->status_validasi === 'pending')
                                                    @if($pembayaran->metode_pembayaran === 'midtrans')
                                                        @php
                                                            $isExpired = $pembayaran->created_at < now()->subHours(24);
                                                        @endphp
                                                        @if($isExpired)
                                                            <span class="badge bg-secondary badge-custom shadow-sm"><i
                                                                    class="fas fa-times-circle me-1"></i> KADALUARSA</span>
                                                        @else
                                                            <span class="badge bg-info badge-custom shadow-sm"><i
                                                                    class="fas fa-hourglass-half me-1"></i> MENUNGGU BAYAR</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-warning badge-custom text-white shadow-sm"><i
                                                                class="fas fa-clock me-1"></i> MENUNGGU VALIDASI</span>
                                                    @endif
                                                @elseif($pembayaran->status_validasi === 'disetujui')
                                                    <span class="badge bg-success badge-custom shadow-sm"><i
                                                            class="fas fa-check-circle me-1"></i> DISETUJUI</span>
                                                @else
                                                    <span class="badge bg-danger badge-custom shadow-sm"><i
                                                            class="fas fa-times-circle me-1"></i> DITOLAK</span>
                                                @endif
                                            </td>
                                            <td data-label="AKSI" class="text-center align-middle">
                                                <a href="{{ route('bendahara.pembayaran.show', $pembayaran->id) }}"
                                                    class="btn btn-info btn-sm rounded-circle shadow-sm" title="Validasi / Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light py-3 border-top">
                            <div class="d-flex justify-content-center">
                                {{ $pembayaranList->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');

        // Show/hide clear button
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearSearch.classList.add('show');
                } else {
                    clearSearch.classList.remove('show');
                }
            });
        }

        // Clear search
        if (clearSearch) {
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                clearSearch.classList.remove('show');
                searchInput.focus();
            });
        }
    </script>
@endsection