@extends('layouts.sneat')

@section('title', 'Generate SPP Bulanan')
@section('page-title', 'Generate SPP Bulanan')
@section('page-subtitle', 'Buat 12 tagihan SPP otomatis')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection
{{-- SweetAlert2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .swal2-popup {
        font-family: 'Public Sans', sans-serif;
        border-radius: 1rem;
    }

    .swal2-title {
        font-size: 1.5rem;
        color: #566a7f;
    }

    .swal2-html-container {
        color: #697a8d;
    }
</style>
@section('styles')
    <style>
        /* Desktop Table Styles */
        .siswa-table thead th {
            background: #f8f9fc;
            color: #4e73df;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .siswa-table tbody tr:hover {
            background: #f8f9fa;
        }

        .siswa-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4e73df;
        }

        .selected-count-badge {
            font-size: 14px;
            padding: 8px 16px;
            white-space: nowrap;
        }

        .filter-section input:focus,
        .filter-section select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* ===== Radio Card Selector Styles ===== */
        .radio-card-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .radio-card-group .radio-card {
            position: relative;
            flex: 1 1 0;
            min-width: 130px;
        }

        .radio-card-group .radio-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-card-group .radio-card .radio-card-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
            user-select: none;
        }

        .radio-card-group .radio-card .radio-card-label:hover {
            border-color: #a0b4f0;
            background: #f8faff;
        }

        .radio-card-group .radio-card input[type="radio"]:checked + .radio-card-label {
            border-color: #4e73df;
            background: #eef2ff;
            box-shadow: 0 0 0 1px #4e73df;
        }

        .radio-card-group .radio-card .radio-card-indicator {
            width: 20px;
            height: 20px;
            min-width: 20px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .radio-card-group .radio-card input[type="radio"]:checked + .radio-card-label .radio-card-indicator {
            border-color: #4e73df;
            background: #4e73df;
        }

        .radio-card-group .radio-card input[type="radio"]:checked + .radio-card-label .radio-card-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
        }

        .radio-card-group .radio-card .radio-card-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .radio-card-group .radio-card .radio-card-title {
            font-weight: 700;
            font-size: 14px;
            color: #334155;
            line-height: 1.3;
        }

        .radio-card-group .radio-card .radio-card-desc {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.3;
        }

        .radio-card-group .radio-card input[type="radio"]:checked + .radio-card-label .radio-card-title {
            color: #4e73df;
        }

        /* SPP Type specific (success color) */
        .radio-card-group.spp-type-group .radio-card input[type="radio"]:checked + .radio-card-label {
            border-color: #28a745;
            background: #f0fdf4;
            box-shadow: 0 0 0 1px #28a745;
        }

        .radio-card-group.spp-type-group .radio-card input[type="radio"]:checked + .radio-card-label .radio-card-indicator {
            border-color: #28a745;
            background: #28a745;
        }

        .radio-card-group.spp-type-group .radio-card input[type="radio"]:checked + .radio-card-label .radio-card-title {
            color: #28a745;
        }

        .radio-card-group.spp-type-group .radio-card .radio-card-label:hover {
            border-color: #81c995;
            background: #f8fdf9;
        }

        /* ===== Mobile Responsive Styles ===== */
        @media (max-width: 992px) {
            .generate-spp-page .row {
                flex-direction: column;
            }

            .generate-spp-page .col-lg-7,
            .generate-spp-page .col-lg-5 {
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            /* Target Card Header */
            .target-card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px;
            }

            .target-card-header h6 {
                margin-bottom: 4px !important;
            }

            .radio-card-group {
                width: 100%;
            }

            .radio-card-group .radio-card {
                min-width: 0;
            }

            .radio-card-group .radio-card .radio-card-label {
                padding: 10px 14px;
            }

            .radio-card-group .radio-card .radio-card-title {
                font-size: 13px;
            }

            .radio-card-group .radio-card .radio-card-desc {
                font-size: 11px;
            }

            /* Filter Section Mobile */
            .filter-section {
                padding: 12px 8px !important;
            }

            .filter-section .row {
                flex-direction: column !important;
            }

            .filter-section .col-md-3 {
                width: 100% !important;
                max-width: 100%;
            }

            /* Table Responsive */
            .table-responsive {
                border: none !important;
            }

            .table-responsive table {
                border-collapse: separate;
                border-spacing: 0 1rem;
            }

            .siswa-table thead {
                display: none;
            }

            .siswa-table tbody tr {
                display: block;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 1rem;
                padding: 0;
            }

            .siswa-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 0.75rem 1rem;
                border-bottom: 1px dashed #e2e8f0;
            }

            .siswa-table tbody td:first-child {
                padding: 0.75rem;
                justify-content: flex-start;
                align-items: center;
            }

            .siswa-table tbody td:last-child {
                border-bottom: none;
            }

            .siswa-table tbody td::before {
                content: attr(data-label);
                display: block;
                font-weight: 700;
                font-size: 0.75rem;
                color: #64748b;
                text-transform: uppercase;
                margin-right: 1rem;
                text-align: left;
                white-space: nowrap;
            }

            .siswa-table tbody td:first-child::before {
                content: '';
                display: none;
            }

            /* Select All Row Mobile */
            .select-all-row {
                display: none;
                background: #f8f9fc;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 1rem;
                margin-bottom: 1rem;
                align-items: center;
                gap: 12px;
            }

            .select-all-row.mobile-visible {
                display: flex;
            }

            .select-all-row input[type="checkbox"] {
                width: 20px;
                height: 20px;
                cursor: pointer;
            }

            .select-all-row label {
                margin: 0;
                cursor: pointer;
                font-weight: 600;
                color: #4e73df;
                user-select: none;
            }

            .siswa-table tbody td strong {
                text-align: right;
            }

            .siswa-table tbody td .badge {
                font-size: 11px;
                white-space: nowrap;
            }

            /* Counter Badge Mobile */
            .selected-count-badge {
                font-size: 12px !important;
                padding: 6px 12px !important;
                background: #4e73df !important;
                color: white;
            }

            /* Counter Section Mobile */
            .spp-counter-section {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px;
            }

            .spp-counter-section span:first-child {
                font-size: 12px;
            }

            /* Action Buttons Mobile */
            .spp-action-buttons {
                flex-wrap: wrap;
            }

            .spp-action-buttons .btn {
                flex: 1 1 auto;
                min-width: 120px;
                font-size: 13px;
                padding: 8px 12px;
            }

            /* Form Labels and Inputs */
            .generate-spp-page .form-label {
                font-size: 14px !important;
                margin-bottom: 8px !important;
            }

            .generate-spp-page .form-select,
            .generate-spp-page .form-control {
                font-size: 14px;
                padding: 8px 10px;
            }

            .generate-spp-page .input-group {
                width: 100%;
                flex-wrap: nowrap;
            }

            .generate-spp-page .input-group-text {
                flex-shrink: 0;
            }

            /* Alert Responsive */
            .generate-spp-page .alert {
                font-size: 13px;
            }

            .generate-spp-page .alert ul li {
                margin-bottom: 4px;
            }

            /* Info Box Mobile */
            .generate-spp-page .alert-warning {
                padding: 12px;
                margin-bottom: 12px;
            }

            .generate-spp-page .alert-warning ul {
                padding-left: 18px;
                margin: 8px 0 0 0;
            }

            .generate-spp-page .alert-warning li {
                padding: 4px 0;
            }

            /* Kelas Checkbox Container Mobile */
            #kelasCheckboxContainer {
                max-height: 300px !important;
                padding: 8px !important;
            }

            .kelas-checkbox-item {
                margin-bottom: 10px;
            }

            .kelas-checkbox-item label {
                font-size: 13px;
                margin-bottom: 0;
            }

            .kelas-checkbox-item .badge {
                font-size: 10px;
                padding: 2px 6px;
            }
        }

        /* Extra Small Devices */
        @media (max-width: 480px) {
            .siswa-table tbody td {
                padding: 0.6rem 0.8rem;
                font-size: 12px;
            }

            .siswa-table tbody td::before {
                font-size: 0.7rem;
                margin-right: 0.5rem;
            }

            .spp-action-buttons .btn {
                min-width: 100px;
                font-size: 12px;
                padding: 7px 10px;
            }

            .selected-count-badge {
                font-size: 11px !important;
                padding: 4px 8px !important;
            }

            .radio-card-group .radio-card .radio-card-label {
                padding: 8px 12px;
                gap: 8px;
            }

            .radio-card-group .radio-card .radio-card-title {
                font-size: 12px;
            }

            .radio-card-group .radio-card .radio-card-desc {
                font-size: 10px;
            }

            .radio-card-group .radio-card .radio-card-indicator {
                width: 18px;
                height: 18px;
                min-width: 18px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="generate-spp-page" style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">

            {{-- Breadcrumb --}}
            <div class="mb-3">
                <a href="{{ route('admin.keuangan.tagihan.index') }}" class="text-primary text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
                </a>
            </div>

            {{-- Info Alert --}}
            <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                    <div>
                        <strong>Generate SPP Bulanan</strong>
                        <p class="mb-0 mt-1">Fitur ini akan membuat 12 tagihan SPP bulanan secara otomatis untuk siswa atau
                            kelas yang dipilih. Tagihan yang sudah ada akan di-update dengan nominal baru.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.keuangan.tagihan.generate-spp.store') }}" method="POST" id="generateSppForm">
                @csrf

                <div class="row">
                    {{-- Left Column - Target Selection --}}
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center gap-3 target-card-header">
                                    <h6 class="m-0 fw-bold text-primary" style="white-space: nowrap;">
                                        <i class="fas fa-users me-2"></i>Pilih Target
                                    </h6>
                                    <div class="radio-card-group">
                                        <div class="radio-card">
                                            <input type="radio" name="target_type" id="target_kelas"
                                                value="kelas" checked onchange="toggleTargetType()">
                                            <label class="radio-card-label" for="target_kelas">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title">Per Kelas</span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="radio-card">
                                            <input type="radio" name="target_type" id="target_siswa"
                                                value="siswa" onchange="toggleTargetType()">
                                            <label class="radio-card-label" for="target_siswa">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title">Pilih Siswa</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">

                                {{-- Kelas Selection --}}
                                <div id="kelasSelection" class="p-4">
                                    <label class="form-label fw-bold mb-3">Pilih Kelas</label>
                                    
                                    {{-- Filter Section --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">Filter Cabang</label>
                                            <select id="filterCabangClass" class="form-select form-select-sm">
                                                <option value="">Semua Cabang</option>
                                                @foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)
                                                    <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">Filter Jenjang</label>
                                            <select id="filterJenjangClass" class="form-select form-select-sm">
                                                <option value="">Semua Jenjang</option>
                                                @php
                                                    $jenjangList = $kelasList->pluck('jenjang')->unique()->sort();
                                                @endphp
                                                @foreach($jenjangList as $jenjang)
                                                    <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Select All Checkbox --}}
                                    <div class="mb-3 p-2 bg-light rounded">
                                        <label class="form-check-label fw-bold" style="cursor: pointer;">
                                            <input type="checkbox" id="selectAllClass" class="form-check-input" 
                                                style="cursor: pointer;" onchange="toggleSelectAllKelas()">
                                            <span id="selectAllText">Pilih Semua Kelas</span>
                                        </label>
                                    </div>

                                    {{-- Kelas Checkboxes --}}
                                    <div id="kelasCheckboxContainer" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 6px; padding: 12px;">
                                        @foreach($kelasList as $kelas)
                                            <div class="form-check mb-2 kelas-checkbox-item" 
                                                data-cabang="{{ $kelas->cabang_id }}" 
                                                data-jenjang="{{ $kelas->jenjang }}">
                                                <input class="form-check-input kelas-checkbox" type="checkbox" 
                                                    name="kelas_ids[]" value="{{ $kelas->id }}" 
                                                    id="kelas_{{ $kelas->id }}"
                                                    onchange="updateSelectAllKelasUI()">
                                                <label class="form-check-label" for="kelas_{{ $kelas->id }}" style="cursor: pointer;">
                                                    <strong>{{ $kelas->nama_kelas }}</strong> 
                                                    <span class="badge bg-secondary">{{ $kelas->jenjang }}</span>
                                                    <small class="text-muted">{{ $kelas->cabang->nama_cabang ?? '-' }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Selected Count --}}
                                    <small class="text-muted mt-2" id="kelasSelectedCount">0 kelas dipilih</small>
                                </div>

                                {{-- Siswa Selection (Hidden by default) --}}
                                <div id="siswaSelection" style="display: none;">
                                    {{-- Filter --}}
                                    <div class="p-3 bg-light border-bottom filter-section">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Cabang <span class="text-danger">*</span></label>
                                                <select id="filterCabang" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Cabang --</option>
                                                    @foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)
                                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Jenjang</label>
                                                <select id="filterJenjang" class="form-select form-select-sm" disabled>
                                                    <option value="">-- Pilih Jenjang --</option>
                                                    @php
                                                        $jenjangSiswaList = $kelasList->pluck('jenjang')->unique()->sort();
                                                    @endphp
                                                    @foreach($jenjangSiswaList as $jenjang)
                                                        <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Kelas</label>
                                                <select id="filterKelas" class="form-select form-select-sm" disabled>
                                                    <option value="">-- Pilih Kelas --</option>
                                                    @foreach($kelasList as $kelas)
                                                        <option value="{{ $kelas->id }}" data-cabang="{{ $kelas->cabang_id }}" data-jenjang="{{ $kelas->jenjang }}">
                                                            {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Cari Siswa</label>
                                                <input type="text" id="searchSiswa" class="form-control form-control-sm"
                                                    placeholder="Cari nama/NISN...">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Student Counter --}}
                                    <div
                                        class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center gap-2 spp-counter-section">
                                        <span class="text-muted small">Centang siswa untuk generate SPP</span>
                                        <span class="badge bg-success selected-count-badge" id="selectedCount">0 siswa
                                            dipilih</span>
                                    </div>

                                    {{-- Select All Row (Mobile) --}}
                                    <div class="select-all-row">
                                        <input type="checkbox" id="selectAll" class="siswa-checkbox form-check-input"
                                            onclick="toggleSelectAll()" style="margin: 0;">
                                        <label for="selectAll" class="form-check-label">Pilih Semua Siswa</label>
                                    </div>

                                    {{-- Student Table (Responsive) --}}
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table table-hover mb-0 siswa-table">
                                            <thead>
                                                <tr>
                                                    <th width="40" class="text-center">
                                                        <input type="checkbox" id="selectAll" class="siswa-checkbox"
                                                            onclick="toggleSelectAll()">
                                                    </th>
                                                    <th width="50" class="text-center">NO</th>
                                                    <th class="text-start">NAMA SISWA</th>
                                                    <th class="text-center">NISN</th>
                                                    <th class="text-center">KELAS</th>
                                                    <th class="text-center">CABANG</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($siswaList as $index => $siswa)
                                                    <tr class="siswa-row" data-cabang="{{ $siswa->cabang_id }}"
                                                        data-kelas="{{ $siswa->kelas_id }}"
                                                        data-jenjang="{{ $siswa->kelas->jenjang ?? '' }}"
                                                        data-search="{{ strtolower($siswa->nama_lengkap . ' ' . $siswa->nisn) }}">
                                                        <td class="text-center" data-label="">
                                                            <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}"
                                                                class="siswa-checkbox" onchange="updateSelectedCount()">
                                                        </td>
                                                        <td class="text-center" data-label="NO">{{ $index + 1 }}</td>
                                                        <td data-label="NAMA SISWA"><strong>{{ $siswa->nama_lengkap }}</strong></td>
                                                        <td class="text-center" data-label="NISN">{{ $siswa->nisn }}</td>
                                                        <td class="text-center" data-label="KELAS">
                                                            <span
                                                                class="badge bg-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                                        </td>
                                                        <td class="text-center" data-label="CABANG">
                                                            <small
                                                                class="text-muted">{{ $siswa->cabang->nama_cabang ?? '-' }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="p-3 bg-light border-top">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Total: <strong>{{ $siswaList->count() }}</strong> siswa aktif
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Right Column - SPP Settings --}}
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white">
                                <h6 class="m-0 fw-bold text-success">
                                    <i class="fas fa-calendar-alt me-2"></i>Pengaturan SPP
                                </h6>
                            </div>
                            <div class="card-body">
                                {{-- Tipe SPP --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold mb-3">
                                        Tipe Generate SPP <span class="text-danger">*</span>
                                    </label>
                                    <div class="radio-card-group spp-type-group">
                                        <div class="radio-card">
                                            <input type="radio" name="tipe_spp" id="spp_setahun"
                                                value="setahun" checked onchange="toggleTipeSpp()">
                                            <label class="radio-card-label" for="spp_setahun">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title"><i class="fas fa-calendar-check text-success me-1"></i>SPP Setahun</span>
                                                    <span class="radio-card-desc">Generate 12 bulan SPP</span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="radio-card">
                                            <input type="radio" name="tipe_spp" id="spp_sebagian"
                                                value="sebagian" onchange="toggleTipeSpp()">
                                            <label class="radio-card-label" for="spp_sebagian">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title"><i class="fas fa-calendar-alt text-warning me-1"></i>SPP Sebagian</span>
                                                    <span class="radio-card-desc">Untuk siswa baru</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bulan Mulai --}}
                                <div class="mb-3">
                                    <label for="bulan_mulai" class="form-label fw-bold">
                                        Bulan Mulai <span class="text-danger">*</span>
                                    </label>
                                    <select name="bulan_mulai" id="bulan_mulai" class="form-select" required>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7" selected>Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                    <small class="text-muted">SPP akan dimulai dari bulan ini</small>
                                </div>

                                {{-- Jumlah Bulan (untuk SPP Sebagian) --}}
                                <div class="mb-3" id="jumlahBulanSection" style="display: none;">
                                    <label for="jumlah_bulan" class="form-label fw-bold">
                                        Jumlah Bulan <span class="text-danger">*</span>
                                    </label>
                                    <select name="jumlah_bulan" id="jumlah_bulan" class="form-select">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i }} Bulan</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted" id="bulanRangeInfo">SPP akan digenerate untuk berapa bulan</small>
                                </div>

                                {{-- Jumlah SPP --}}
                                <div class="mb-3">
                                    <label for="jumlah_spp" class="form-label fw-bold">
                                        Jumlah SPP per Bulan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">Rp</span>
                                        <input type="text" name="jumlah_spp" id="jumlah_spp"
                                            class="form-control currency-input" placeholder="0" required>
                                    </div>
                                </div>

                                {{-- Tanggal Jatuh Tempo --}}
                                <div class="mb-3">
                                    <label for="tanggal_jatuh_tempo" class="form-label fw-bold">
                                        Jatuh Tempo Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <select name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="form-select"
                                        required>
                                        @for($i = 1; $i <= 31; $i++)
                                            <option value="{{ $i }}" {{ $i == 10 ? 'selected' : '' }}>Tanggal {{ $i }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Tanggal jatuh tempo setiap bulan</small>
                                </div>

                                {{-- Preview Info --}}
                                <div class="alert alert-warning mb-3" id="previewInfo">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong>
                                    <ul class="mb-0 mt-2 small" id="previewList">
                                        <li id="previewBulan">Akan dibuat <span id="totalBulanText">12</span> tagihan SPP</li>
                                        <li>Jika tagihan sudah ada, nominal akan diperbarui</li>
                                        <li>Status tagihan baru = "Belum Bayar"</li>
                                    </ul>
                                </div>

                                {{-- Tahun Ajaran --}}
                                <div class="text-muted small mb-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2 spp-action-buttons">
                                    <a href="{{ route('admin.keuangan.tagihan.index') }}" class="btn btn-secondary flex-fill">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="button" class="btn btn-success flex-fill fw-bold"
                                        onclick="confirmGenerate()">
                                        <i class="fas fa-calendar-check me-1"></i> Generate SPP
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>


    {{-- Confirm Modal Removed (Replaced by SweetAlert2) --}}
    </div>

@endsection

@section('scripts')
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterCabang = document.getElementById('filterCabang');
            const filterJenjang = document.getElementById('filterJenjang');
            const filterKelas = document.getElementById('filterKelas');
            const searchInput = document.getElementById('searchSiswa');

            // Filter function - applies all active filters to siswa rows
            function filterSiswa() {
                const cabangId = filterCabang.value;
                const jenjangVal = filterJenjang.value;
                const kelasId = filterKelas.value;
                const searchTerm = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('.siswa-row');

                rows.forEach(row => {
                    const rowCabang = row.getAttribute('data-cabang');
                    const rowKelas = row.getAttribute('data-kelas');
                    const rowJenjang = row.getAttribute('data-jenjang');
                    const rowSearch = row.getAttribute('data-search');

                    let show = true;

                    if (cabangId && rowCabang !== cabangId) show = false;
                    if (jenjangVal && rowJenjang !== jenjangVal) show = false;
                    if (kelasId && rowKelas !== kelasId) show = false;
                    if (searchTerm && !rowSearch.includes(searchTerm)) show = false;

                    row.style.display = show ? '' : 'none';
                });
            }

            // Cascading: Cabang → Jenjang → Kelas
            filterCabang.addEventListener('change', function () {
                const selectedCabang = this.value;

                // Reset jenjang & kelas
                filterJenjang.value = '';
                filterKelas.value = '';

                if (selectedCabang) {
                    // Enable jenjang, filter its options by cabang
                    filterJenjang.disabled = false;
                    const kelasOptions = filterKelas.querySelectorAll('option');
                    // Collect unique jenjang values for this cabang
                    const availableJenjang = new Set();
                    kelasOptions.forEach(option => {
                        if (option.value === '') return;
                        if (option.getAttribute('data-cabang') === selectedCabang) {
                            availableJenjang.add(option.getAttribute('data-jenjang'));
                        }
                    });

                    // Show/hide jenjang options
                    const jenjangOptions = filterJenjang.querySelectorAll('option');
                    jenjangOptions.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        option.style.display = availableJenjang.has(option.value) ? '' : 'none';
                    });

                    // Disable kelas until jenjang picked (or show all kelas for cabang)
                    filterKelas.disabled = false;
                    kelasOptions.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        const kelasCabang = option.getAttribute('data-cabang');
                        option.style.display = (kelasCabang === selectedCabang) ? '' : 'none';
                    });
                } else {
                    // Disable jenjang & kelas
                    filterJenjang.disabled = true;
                    filterKelas.disabled = true;
                }

                filterSiswa();
            });

            // Jenjang change → filter kelas options
            filterJenjang.addEventListener('change', function () {
                const selectedCabang = filterCabang.value;
                const selectedJenjang = this.value;

                // Reset kelas
                filterKelas.value = '';

                if (selectedCabang) {
                    filterKelas.disabled = false;
                    const kelasOptions = filterKelas.querySelectorAll('option');
                    kelasOptions.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        const kelasCabang = option.getAttribute('data-cabang');
                        const kelasJenjang = option.getAttribute('data-jenjang');
                        let show = kelasCabang === selectedCabang;
                        if (selectedJenjang) show = show && kelasJenjang === selectedJenjang;
                        option.style.display = show ? '' : 'none';
                    });
                }

                filterSiswa();
            });

            filterKelas.addEventListener('change', filterSiswa);
            searchInput.addEventListener('input', filterSiswa);
        });

        function toggleTargetType() {
            const isKelas = document.getElementById('target_kelas').checked;
            document.getElementById('kelasSelection').style.display = isKelas ? 'block' : 'none';
            document.getElementById('siswaSelection').style.display = isKelas ? 'none' : 'block';

            // Update input names
            document.getElementById('kelas_id').name = isKelas ? 'target_id' : 'target_id_kelas';
        }

        function toggleTipeSpp() {
            const isSetahun = document.getElementById('spp_setahun').checked;
            const jumlahBulanSection = document.getElementById('jumlahBulanSection');
            const totalBulanText = document.getElementById('totalBulanText');

            if (isSetahun) {
                jumlahBulanSection.style.display = 'none';
                totalBulanText.textContent = '12';
            } else {
                jumlahBulanSection.style.display = 'block';
                updateJumlahBulan();
            }
        }

        function updateJumlahBulan() {
            const jumlahBulan = document.getElementById('jumlah_bulan').value;
            const bulanMulai = document.getElementById('bulan_mulai').value;
            const totalBulanText = document.getElementById('totalBulanText');
            const bulanRangeInfo = document.getElementById('bulanRangeInfo');

            const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const bulanAkhir = (parseInt(bulanMulai) + parseInt(jumlahBulan) - 1);
            const bulanAkhirIdx = ((bulanAkhir - 1) % 12);

            totalBulanText.textContent = jumlahBulan;
            bulanRangeInfo.innerHTML = `<i class="fas fa-calendar me-1"></i>${namaBulan[parseInt(bulanMulai) - 1]} - ${namaBulan[bulanAkhirIdx]}`;
        }

        // Event listeners for updates
        document.getElementById('jumlah_bulan')?.addEventListener('change', updateJumlahBulan);
        document.getElementById('bulan_mulai')?.addEventListener('change', function() {
            if (!document.getElementById('spp_setahun').checked) {
                updateJumlahBulan();
            }
        });

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const visibleCheckboxes = document.querySelectorAll('.siswa-row:not([style*="display: none"]) .siswa-checkbox:not(#selectAll)');

            visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.siswa-checkbox:checked:not(#selectAll)').length;
            document.getElementById('selectedCount').textContent = checked + ' siswa dipilih';
        }

        // Show/hide Select All row on mobile
        function updateSelectAllRowVisibility() {
            const selectAllRow = document.querySelector('.select-all-row');
            if (window.innerWidth <= 768) {
                selectAllRow.classList.add('mobile-visible');
            } else {
                selectAllRow.classList.remove('mobile-visible');
            }
        }

        // Sync Select All checkboxes
        function syncSelectAllCheckboxes(checked) {
            const allSelectAll = document.querySelectorAll('#selectAll');
            allSelectAll.forEach(cb => cb.checked = checked);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectAllRowVisibility();
            initKelasFilters();
            
            const selectAllCheckboxes = document.querySelectorAll('#selectAll');
            selectAllCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    syncSelectAllCheckboxes(this.checked);
                    toggleSelectAll();
                });
            });
        });

        window.addEventListener('resize', updateSelectAllRowVisibility);

        // Kelas Filter & Select All Functions
        function initKelasFilters() {
            const filterCabangClass = document.getElementById('filterCabangClass');
            const filterJenjangClass = document.getElementById('filterJenjangClass');

            filterCabangClass?.addEventListener('change', filterKelasItems);
            filterJenjangClass?.addEventListener('change', filterKelasItems);
        }

        function filterKelasItems() {
            const filterCabangClass = document.getElementById('filterCabangClass');
            const filterJenjangClass = document.getElementById('filterJenjangClass');
            const items = document.querySelectorAll('.kelas-checkbox-item');

            const selectedCabang = filterCabangClass?.value;
            const selectedJenjang = filterJenjangClass?.value;

            items.forEach(item => {
                const cabang = item.getAttribute('data-cabang');
                const jenjang = item.getAttribute('data-jenjang');

                let show = true;
                if (selectedCabang && cabang !== selectedCabang) show = false;
                if (selectedJenjang && jenjang !== selectedJenjang) show = false;

                item.style.display = show ? '' : 'none';
            });

            updateSelectAllKelasUI();
        }

        function toggleSelectAllKelas() {
            const selectAll = document.getElementById('selectAllClass');
            const visibleCheckboxes = document.querySelectorAll('.kelas-checkbox-item:not([style*="display: none"]) .kelas-checkbox');

            visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedKelasCount();
        }

        function updateSelectAllKelasUI() {
            const selectAll = document.getElementById('selectAllClass');
            const visibleCheckboxes = document.querySelectorAll('.kelas-checkbox-item:not([style*="display: none"]) .kelas-checkbox');
            const checkedCount = Array.from(visibleCheckboxes).filter(cb => cb.checked).length;

            if (visibleCheckboxes.length === 0) {
                selectAll.checked = false;
                selectAll.disabled = true;
            } else {
                selectAll.disabled = false;
                selectAll.checked = checkedCount === visibleCheckboxes.length && checkedCount > 0;
            }

            updateSelectedKelasCount();
        }

        function updateSelectedKelasCount() {
            const checked = document.querySelectorAll('.kelas-checkbox:checked').length;
            document.getElementById('kelasSelectedCount').textContent = checked + ' kelas dipilih';
        }

        function confirmGenerate() {
            const targetType = document.querySelector('input[name="target_type"]:checked').value;
            const tipeSpp = document.querySelector('input[name="tipe_spp"]:checked').value;
            const jumlahSpp = document.getElementById('jumlah_spp').value;

            let targetName = '';
            let targetCount = 0;

            if (targetType === 'kelas') {
                const checkedKelas = document.querySelectorAll('.kelas-checkbox:checked');
                if (checkedKelas.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Kelas',
                        text: 'Silakan pilih minimal satu kelas terlebih dahulu!',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }
                targetCount = checkedKelas.length + ' kelas terpilih';
            } else {
                const checked = document.querySelectorAll('.siswa-checkbox:checked:not(#selectAll)');
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum ada siswa',
                        text: 'Silakan pilih minimal satu siswa!',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }
                targetCount = checked.length + ' siswa terpilih';
            }

            if (!jumlahSpp || jumlahSpp === '0') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nominal Kosong',
                    text: 'Silakan isi jumlah SPP per bulan!',
                    confirmButtonColor: '#696cff'
                });
                return;
            }

            const totalBulan = tipeSpp === 'setahun' ? 12 : document.getElementById('jumlah_bulan').value;
            const infoText = tipeSpp === 'setahun'
                ? 'Proses ini akan membuat tagihan untuk satu tahun ajaran penuh (12 bulan).'
                : `Proses ini akan membuat tagihan untuk ${totalBulan} bulan.`;

            Swal.fire({
                title: 'Konfirmasi Generate SPP',
                html: `Anda akan membuat <strong>${totalBulan} tagihan SPP</strong> dengan nominal <strong>Rp ${jumlahSpp}</strong> untuk <strong>${targetCount}</strong>.<br><br><small class="text-muted">${infoText}</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Generate SPP',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const jumlahInput = document.getElementById('jumlah_spp');
                    jumlahInput.value = jumlahInput.value.replace(/\./g, '') || '0';
                    document.getElementById('generateSppForm').submit();
                }
            });
        }
    </script>
@endsection