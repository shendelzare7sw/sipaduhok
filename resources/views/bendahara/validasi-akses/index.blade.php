@extends('layouts.sneat')

@section('title', 'Validasi Akses')
@section('page-title', 'Validasi Akses Ujian & Rapor')
@section('page-subtitle', 'Validasi akses berdasarkan status pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --acc-primary: #4361ee;
        --acc-success: #10b981;
        --acc-warning: #f59e0b;
        --acc-danger: #ef4444;
        --acc-info: #06b6d4;
        --acc-purple: #8b5cf6;
        --acc-surface: #ffffff;
        --acc-bg: #f8fafc;
        --acc-border: #e2e8f0;
        --acc-text: #1e293b;
        --acc-muted: #64748b;
        --acc-radius: 12px;
    }

    .access-shell {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .stat-widget,
    .access-card,
    .quick-card {
        background: var(--acc-surface);
        border: 1px solid var(--acc-border);
        border-radius: var(--acc-radius);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .stat-widget {
        padding: 1.35rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.2s ease;
    }

    .stat-widget:hover { transform: translateY(-2px); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--acc-text);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--acc-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--acc-muted);
        margin-top: 0.15rem;
    }

    .access-card {
        overflow: hidden;
    }

    .access-card-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--acc-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .access-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--acc-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .access-card-subtitle {
        color: var(--acc-muted);
        font-size: 0.82rem;
        margin-top: 0.25rem;
    }

    .access-flow {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: var(--acc-radius);
        color: #1d4ed8;
    }

    .access-flow .flow-label {
        color: #1e40af;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-wrapper {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
        background: var(--acc-bg);
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid var(--acc-border);
    }

    .search-box {
        position: relative;
        flex-grow: 1;
        min-width: 220px;
    }

    .search-box input {
        width: 100%;
        padding: 0.45rem 1rem 0.45rem 2.2rem;
        border: 1px solid var(--acc-border);
        border-radius: 8px;
        font-size: 0.88rem;
    }

    .search-box i {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--acc-muted);
    }

    .filter-select {
        min-width: 145px;
        font-size: 0.85rem;
        padding: 0.45rem 2rem 0.45rem 0.75rem;
        border-color: var(--acc-border);
        border-radius: 8px;
    }

    .access-toolbar {
        padding: 1rem 1.5rem;
        background: var(--acc-bg);
        border-bottom: 1px solid var(--acc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .selected-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #eff6ff;
        color: var(--acc-primary);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .mobile-select-all {
        display: none;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 0.8rem;
        border: 1px solid var(--acc-border);
        border-radius: 8px;
        background: #fff;
        color: var(--acc-muted);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .btn-soft {
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .table-clean {
        margin: 0;
    }

    .table-clean th {
        background: var(--acc-bg);
        border-bottom: 1px solid var(--acc-border);
        color: var(--acc-muted);
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1rem;
        white-space: nowrap;
    }

    .table-clean td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--acc-border);
        color: var(--acc-text);
        font-size: 0.88rem;
    }

    .table-clean tbody tr:hover {
        background: #f8fafc;
    }

    .table-clean tbody tr:last-child td {
        border-bottom: none;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .student-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.12);
        color: var(--acc-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
        flex-shrink: 0;
    }

    .student-name {
        font-weight: 700;
        color: var(--acc-text);
        line-height: 1.25;
        word-break: break-word;
    }

    .student-meta {
        color: var(--acc-muted);
        font-size: 0.76rem;
        margin-top: 0.15rem;
    }

    .currency-font {
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-status {
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.72rem;
    }

    .action-btns {
        display: flex;
        justify-content: flex-end;
        gap: 0.35rem;
    }

    .action-btns .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        padding: 1.25rem;
    }

    .quick-card {
        padding: 1rem;
    }

    .period-settings {
        padding: 1.25rem;
    }

    .period-settings .nav-tabs {
        gap: 0.35rem;
        border-bottom-color: var(--acc-border);
        flex-wrap: wrap;
    }

    .period-settings .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        color: var(--acc-muted);
        font-size: 0.85rem;
        font-weight: 700;
    }

    .period-settings .nav-tabs .nav-link.active {
        color: var(--acc-primary);
        background: #fff;
        border-color: var(--acc-border) var(--acc-border) #fff;
    }

    .period-pane {
        border: 1px solid var(--acc-border);
        border-top: 0;
        border-radius: 0 0 10px 10px;
        padding: 1.25rem;
        background: #fff;
    }

    .billing-group {
        background: var(--acc-bg);
        border: 1px solid var(--acc-border);
        border-radius: 10px;
        padding: 1rem;
        height: 100%;
    }

    .quick-title {
        color: var(--acc-text);
        font-weight: 700;
    }

    .quick-meta {
        color: var(--acc-muted);
        font-size: 0.78rem;
        margin: 0.15rem 0 0.9rem;
    }

    .empty-state {
        min-height: 260px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--acc-muted);
        padding: 2rem;
    }

    .empty-state i {
        color: #cbd5e1;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .quick-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 768px) {
        .stat-row,
        .quick-grid { grid-template-columns: 1fr; }
        .access-card-header { align-items: stretch; }
        .access-card-header > div { width: 100%; }
        .filter-wrapper,
        .access-toolbar { flex-direction: column; align-items: stretch; }
        .filter-wrapper .search-box,
        .filter-wrapper .filter-select,
        .filter-wrapper .btn,
        .access-toolbar .btn,
        .access-toolbar > div { width: 100%; }
        .access-toolbar .d-flex { justify-content: stretch; }
        .access-toolbar .d-flex .btn { flex: 1 1 100%; }
        .mobile-select-all { display: flex; }
        .access-flow { align-items: flex-start; }

        .table-responsive { overflow-x: visible; }
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: flex;
            flex-direction: column;
            border-bottom: 2px solid var(--acc-border);
            background: #fff;
        }
        .table-clean tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            text-align: right;
            white-space: normal;
        }
        .table-clean tbody td::before {
            content: attr(data-label);
            color: var(--acc-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            flex-shrink: 0;
        }
        .table-clean tbody td.mobile-card-head {
            background: var(--acc-bg);
            align-items: flex-start;
            text-align: left;
            padding: 1rem;
            order: -2;
        }
        .table-clean tbody td.mobile-card-head::before,
        .table-clean tbody td.mobile-card-actions::before {
            display: none;
        }
        .table-clean tbody td[data-label="Pilih"] {
            order: -1;
            justify-content: flex-start;
            background: #f8fafc;
        }
        .table-clean tbody td[data-label="No"] {
            display: none;
        }
        .student-info {
            width: 100%;
            align-items: flex-start;
        }
        .mobile-card-actions {
            justify-content: flex-end !important;
            background: #f8fafc;
        }
        .action-btns { width: 100%; }
        .action-btns .btn { flex: 1; width: auto; border-radius: 8px; }
        .period-settings { padding: 1rem; }
        .period-pane { padding: 1rem; }
    }
</style>
@endsection

@section('content')
@php
    $hasActiveFilter = request()->hasAny(['search', 'cabang_id', 'jenjang', 'kelas_id', 'status_ujian', 'status_rapor']);
@endphp

<div class="access-shell">
    <div class="access-flow">
        <span class="flow-label">Alur Validasi</span>
        <span class="badge bg-warning text-dark"><i class="fas fa-user-check me-1"></i>Ketua Approve Rapor</span>
        <i class="fas fa-arrow-right small d-none d-sm-inline"></i>
        <span class="badge bg-primary"><i class="fas fa-money-bill me-1"></i>Bendahara Validasi</span>
        <i class="fas fa-arrow-right small d-none d-sm-inline"></i>
        <span class="badge bg-success"><i class="fas fa-unlock me-1"></i>Akses Terbuka</span>
    </div>

    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalSiswa }}</div>
                <div class="stat-label">Total Siswa Aktif</div>
                <div class="stat-desc">Siswa terdaftar</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <div class="stat-value">{{ $validasiUjian }}</div>
                <div class="stat-label">Akses Ujian Valid</div>
                <div class="stat-desc">Sudah divalidasi</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <div class="stat-value">{{ $validasiRapor }}</div>
                <div class="stat-label">Akses Rapor Valid</div>
                <div class="stat-desc">Sudah divalidasi</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="stat-value">{{ $belumValidasi }}</div>
                <div class="stat-label">Belum Divalidasi</div>
                <div class="stat-desc">Menunggu antrean</div>
            </div>
        </div>
    </div>

    <div class="access-card">
        <div class="access-card-header">
            <div>
                <h5 class="access-card-title"><i class="fas fa-user-shield" style="color: var(--acc-primary);"></i> Daftar Kendali Akses Siswa</h5>
                <div class="access-card-subtitle">Pilih siswa, validasi akses, atau kirim dispensasi ke Ketua PKBM.</div>
            </div>
        </div>

        <form action="{{ route('bendahara.validasi-akses.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama atau NISN..." value="{{ request('search') }}">
                </div>
                <select name="cabang_id" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $cabang)
                        <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                    @endforeach
                </select>
                <select name="jenjang" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangList as $jenjang)
                        <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                    @endforeach
                </select>
                <select name="kelas_id" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }}</option>
                    @endforeach
                </select>
                <select name="status_ujian" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Status Ujian</option>
                    <option value="valid" {{ request('status_ujian') == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="belum" {{ request('status_ujian') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
                <select name="status_rapor" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Status Rapor</option>
                    <option value="valid" {{ request('status_rapor') == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="belum" {{ request('status_rapor') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm btn-soft px-3">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if($hasActiveFilter)
                    <a href="{{ route('bendahara.validasi-akses.index') }}" class="btn btn-outline-danger btn-sm btn-soft px-3">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="access-toolbar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <label class="mobile-select-all mb-0" for="select-all-mobile">
                    <input type="checkbox" id="select-all-mobile" class="form-check-input m-0" onclick="toggleSelectAllMobile()">
                    <span>Pilih semua</span>
                </label>
                <div class="selected-badge">
                    <i class="fas fa-check-circle"></i>
                    <span><span id="selectedCount">0</span> siswa terpilih</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-success btn-sm btn-soft" onclick="bulkValidasiUjian()">
                    <i class="fas fa-check-double"></i> Validasi Ujian
                </button>
                <button type="button" id="btn-bulk-rapor" class="btn btn-outline-secondary btn-sm btn-soft" onclick="bulkValidasiRapor()" title="Belum ada siswa yang di-approve Ketua">
                    <i class="fas fa-lock" id="btn-bulk-rapor-icon"></i> Validasi Rapor <small class="opacity-75" id="btn-bulk-rapor-label">(Perlu Ketua)</small>
                </button>
                <button type="button" class="btn btn-warning btn-sm btn-soft" data-bs-toggle="modal" data-bs-target="#dispensasiModal">
                    <i class="fas fa-hand-holding-heart"></i> Ajukan Dispensasi
                    @if(($dispensasiPending ?? 0) > 0)
                        <span class="badge bg-danger ms-1">{{ $dispensasiPending }}</span>
                    @endif
                </button>
            </div>
        </div>

        <form id="bulk-form" action="{{ route('bendahara.validasi-akses.bulk-validasi-selected') }}" method="POST">
            @csrf
            <input type="hidden" name="tipe" id="bulk-action" value="">

            <div class="table-responsive">
                <table class="table table-clean align-middle">
                    <thead>
                        <tr>
                            <th width="42" class="text-center"><input type="checkbox" id="select-all" class="form-check-input" onclick="toggleSelectAll()"></th>
                            <th width="60" class="text-center">No</th>
                            <th>Identitas Siswa</th>
                            <th>Kelas</th>
                            <th>Tagihan</th>
                            <th>Sisa</th>
                            <th>Akses Ujian</th>
                            <th>Akses Rapor</th>
                            <th class="text-end" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $s)
                            <tr>
                                <td class="text-center" data-label="Pilih">
                                    <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="siswa-checkbox form-check-input" data-ketua-approved="{{ $s->validasi_rapor_ketua ? '1' : '0' }}" onchange="handleSelectionChange()">
                                </td>
                                <td class="text-center fw-bold text-muted" data-label="No">{{ $siswa->firstItem() + $index }}</td>
                                <td class="mobile-card-head" data-label="Siswa">
                                    <div class="student-info">
                                        <div class="student-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                        <div style="min-width: 0;">
                                            <div class="student-name">{{ $s->nama_lengkap }}</div>
                                            <div class="student-meta">{{ $s->nisn }} | {{ $s->cabang->nama_cabang ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Kelas">
                                    <span class="badge bg-label-primary px-2 py-1">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                                </td>
                                <td data-label="Tagihan">
                                    <span class="currency-font">Rp {{ number_format($s->total_tagihan, 0, ',', '.') }}</span>
                                </td>
                                <td data-label="Sisa">
                                    <span class="currency-font {{ $s->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}</span>
                                </td>
                                <td data-label="Akses Ujian">
                                    @if($s->validasi_ujian_bendahara)
                                        <div class="text-end">
                                            <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i>Valid</span>
                                            <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($s->tanggal_validasi_ujian_bendahara)->format('d/m/Y') }}</div>
                                        </div>
                                    @else
                                        <span class="badge bg-warning text-dark badge-status"><i class="fas fa-clock me-1"></i>Belum</span>
                                    @endif
                                </td>
                                <td data-label="Akses Rapor">
                                    @if($s->validasi_rapor_bendahara)
                                        <div class="text-end">
                                            <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i>Valid</span>
                                            <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($s->tanggal_validasi_rapor_bendahara)->format('d/m/Y') }}</div>
                                        </div>
                                    @elseif(!$s->validasi_rapor_ketua)
                                        <span class="badge bg-secondary badge-status"><i class="fas fa-hourglass-half me-1"></i>Tunggu Ketua</span>
                                    @else
                                        <span class="badge bg-warning text-dark badge-status"><i class="fas fa-clock me-1"></i>Belum</span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions" data-label="Aksi">
                                    <div class="action-btns">
                                        @if(!$s->validasi_ujian_bendahara)
                                            <button type="button" onclick="confirmAction('{{ route('bendahara.validasi-akses.validasi-ujian', $s->id) }}', 'Validasi ujian {{ addslashes($s->nama_lengkap) }}?')" class="btn btn-sm btn-success" title="Validasi Ujian">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <button type="button" onclick="confirmAction('{{ route('bendahara.validasi-akses.batalkan-ujian', $s->id) }}', 'Batalkan validasi ujian?')" class="btn btn-sm btn-outline-danger" title="Batal Ujian">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif

                                        @if(!$s->validasi_rapor_bendahara)
                                            @if($s->validasi_rapor_ketua)
                                                <button type="button" onclick="confirmAction('{{ route('bendahara.validasi-akses.validasi-rapor', $s->id) }}', 'Validasi rapor {{ addslashes($s->nama_lengkap) }}?')" class="btn btn-sm btn-info text-white" title="Validasi Rapor">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-light border" disabled title="Menunggu validasi Ketua">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" onclick="confirmAction('{{ route('bendahara.validasi-akses.batalkan-rapor', $s->id) }}', 'Batalkan validasi rapor?')" class="btn btn-sm btn-outline-danger" title="Batal Rapor">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('bendahara.tagihan.show', $s->id) }}" class="btn btn-sm btn-secondary" title="Detail Tagihan">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-search"></i>
                                        <h6 class="mb-1">Data tidak ditemukan</h6>
                                        <p class="small mb-0">Coba ubah kata kunci atau filter yang sedang aktif.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($siswa->hasPages())
            <div class="border-top p-3 d-flex justify-content-center">
                {{ $siswa->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <div class="access-card">
        <div class="access-card-header">
            <div>
                <h5 class="access-card-title"><i class="fas fa-cog" style="color: var(--acc-info);"></i> Pengaturan Batas Pembayaran</h5>
                <div class="access-card-subtitle">Tentukan jenis tagihan yang harus lunas untuk tiap periode ujian dan rapor.</div>
            </div>
            <span class="badge bg-label-info px-3 py-2">
                {{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->nama ?? 'Tahun ajaran aktif' }}
            </span>
        </div>
        <div class="period-settings">
            <p class="text-muted small mb-3">
                Jika tidak ada jenis tagihan yang dipilih, semua siswa dianggap memenuhi batas pembayaran untuk periode tersebut.
            </p>

            @php
                $periodeList = [
                    'pts_ganjil' => 'PTS Ganjil',
                    'pas_ganjil' => 'PAS Ganjil',
                    'pts_genap' => 'PTS Genap',
                    'pas_genap' => 'PAS Genap',
                    'ujian_akhir' => 'Ujian Akhir',
                ];
            @endphp

            <ul class="nav nav-tabs" id="periodeTabs" role="tablist">
                @foreach($periodeList as $key => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $key }}" type="button" role="tab">
                            {{ $label }}
                            @if(isset($batasPembayaran[$key]) && !empty($batasPembayaran[$key]->jenis_tagihan_required))
                                <span class="badge bg-success ms-1">{{ count($batasPembayaran[$key]->jenis_tagihan_required) }}</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="periodeTabContent">
                @foreach($periodeList as $key => $label)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $key }}" role="tabpanel">
                        <div class="period-pane">
                            <form action="{{ route('bendahara.validasi-akses.batas-pembayaran') }}" method="POST">
                                @csrf
                                <input type="hidden" name="periode" value="{{ $key }}">

                                @php
                                    $currentRequired = isset($batasPembayaran[$key]) ? ($batasPembayaran[$key]->jenis_tagihan_required ?? []) : [];
                                    $grouped = [
                                        'Biaya Tetap' => ['uang_pendaftaran', 'uang_pangkal', 'kegiatan', 'buku', 'seragam', 'rapor_foto', 'ujian', 'akm'],
                                        'SPP Bulanan' => ['spp_juli', 'spp_agustus', 'spp_september', 'spp_oktober', 'spp_november', 'spp_desember', 'spp_januari', 'spp_februari', 'spp_maret', 'spp_april', 'spp_mei', 'spp_juni'],
                                    ];
                                @endphp

                                <div class="row g-3">
                                    @foreach($grouped as $groupLabel => $items)
                                        <div class="col-md-6">
                                            <div class="billing-group">
                                                <h6 class="fw-bold text-secondary mb-3">{{ $groupLabel }}</h6>
                                                @foreach($items as $jenis)
                                                    @if($jenisTagihanList->contains($jenis))
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="jenis_tagihan_required[]" value="{{ $jenis }}" id="chk-{{ $key }}-{{ $jenis }}" {{ in_array($jenis, $currentRequired) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="chk-{{ $key }}-{{ $jenis }}">{{ ucwords(str_replace('_', ' ', $jenis)) }}</label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm btn-soft">
                                        <i class="fas fa-save"></i> Simpan {{ $label }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="access-card">
        <div class="access-card-header">
            <div>
                <h5 class="access-card-title"><i class="fas fa-bolt" style="color: var(--acc-warning);"></i> Validasi Kilat Per Kelas</h5>
                <div class="access-card-subtitle">Jalankan validasi massal untuk kelas yang sering diproses.</div>
            </div>
        </div>
        <div class="quick-grid">
            @foreach($kelasList->take(6) as $kelas)
                <div class="quick-card">
                    <div class="quick-title">{{ $kelas->nama_kelas }}</div>
                    <div class="quick-meta">{{ $kelas->jenjang }} | {{ $kelas->siswa->count() }} Siswa</div>
                    <div class="row g-2">
                        <div class="col">
                            <form id="form-ujian-{{ $kelas->id }}" action="{{ route('bendahara.validasi-akses.bulk-validasi-ujian', $kelas->id) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-success btn-sm w-100 btn-soft" onclick="confirmClassAction('form-ujian-{{ $kelas->id }}', 'Validasi Ujian Se-Kelas', 'Validasi ujian untuk seluruh siswa di kelas {{ addslashes($kelas->nama_kelas) }}?')">Ujian</button>
                            </form>
                        </div>
                        <div class="col">
                            <form id="form-rapor-{{ $kelas->id }}" action="{{ route('bendahara.validasi-akses.bulk-validasi-rapor', $kelas->id) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-info btn-sm w-100 text-white btn-soft" onclick="confirmClassAction('form-rapor-{{ $kelas->id }}', 'Validasi Rapor Se-Kelas', 'Validasi rapor untuk seluruh siswa di kelas {{ addslashes($kelas->nama_kelas) }}?')">Rapor</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: #fffbeb;">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <span id="modalTitle">Konfirmasi Aksi</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0 text-dark" id="modalMessage">Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-warning fw-bold" id="confirmBtn">
                    <i class="fas fa-check me-1"></i> Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: #fff1f2;">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i>
                    <span id="alertTitle">Peringatan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0 text-dark" id="alertMessage">Terjadi kesalahan!</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary fw-bold" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i> Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dispensasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="{{ route('bendahara.validasi-akses.dispensasi') }}" method="POST">
                @csrf
                <div class="modal-header border-0" style="background: #fffbeb;">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-hand-holding-heart text-warning me-2"></i>Ajukan Dispensasi ke Ketua PKBM
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Dispensasi</label>
                        <select name="tipe" class="form-select" required>
                            <option value="ujian">Akses Ujian</option>
                            <option value="rapor">Akses Rapor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Periode</label>
                        <select name="periode" class="form-select">
                            <option value="">Semua Periode</option>
                            <option value="pts_ganjil">PTS Ganjil</option>
                            <option value="pas_ganjil">PAS Ganjil</option>
                            <option value="pts_genap">PTS Genap</option>
                            <option value="pas_genap">PAS Genap</option>
                            <option value="ujian_akhir">Ujian Akhir</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Dispensasi</label>
                        <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Siswa memiliki cicilan yang sedang berjalan..."></textarea>
                    </div>
                    <div class="alert alert-info border-0 small mb-0">
                        <i class="fas fa-info-circle me-1"></i> Siswa yang dicentang di tabel akan dimasukkan ke pengajuan dispensasi.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <div id="dispensasi-siswa-ids"></div>
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" id="submitDispensasi"><i class="fas fa-paper-plane me-1"></i> Kirim ke Ketua</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('dispensasiModal').addEventListener('show.bs.modal', function() {
    const container = document.getElementById('dispensasi-siswa-ids');
    container.innerHTML = '';
    const checked = document.querySelectorAll('.siswa-checkbox:checked');
    if (checked.length === 0) {
        document.getElementById('submitDispensasi').disabled = true;
        container.innerHTML = '<span class="text-danger small">Pilih siswa terlebih dahulu!</span>';
    } else {
        document.getElementById('submitDispensasi').disabled = false;
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'siswa_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
    }
});
</script>

<script>
    const actionForm = document.createElement('form');
    actionForm.id = 'action-form';
    actionForm.method = 'POST';
    actionForm.style.display = 'none';
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    actionForm.appendChild(csrfInput);
    document.body.appendChild(actionForm);

    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
    let confirmCallback = null;

    function showConfirmModal(title, message, callback) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        confirmCallback = callback;
        confirmModal.show();
    }

    function showAlertModal(title, message) {
        document.getElementById('alertTitle').textContent = title;
        document.getElementById('alertMessage').textContent = message;
        alertModal.show();
    }

    document.getElementById('confirmBtn').addEventListener('click', function () {
        if (confirmCallback) {
            confirmCallback();
            confirmCallback = null;
        }
        confirmModal.hide();
    });

    function updateSelectedCount() {
        const count = document.querySelectorAll('.siswa-checkbox:checked').length;
        const selectedCount = document.getElementById('selectedCount');
        if (selectedCount) {
            selectedCount.textContent = count;
        }
    }

    function updateBtnValidasiRapor() {
        const checked = document.querySelectorAll('.siswa-checkbox:checked');
        const hasEligible = Array.from(checked).some(cb => cb.dataset.ketuaApproved === '1');
        const btn = document.getElementById('btn-bulk-rapor');
        const icon = document.getElementById('btn-bulk-rapor-icon');
        const label = document.getElementById('btn-bulk-rapor-label');
        if (hasEligible) {
            btn.className = 'btn btn-info btn-sm btn-soft text-white';
            btn.title = 'Validasi akses rapor siswa yang sudah di-approve Ketua';
            icon.className = 'fas fa-file-alt';
            label.style.display = 'none';
        } else {
            btn.className = 'btn btn-outline-secondary btn-sm btn-soft';
            btn.title = 'Belum ada siswa yang di-approve Ketua';
            icon.className = 'fas fa-lock';
            label.style.display = '';
        }
    }

    function handleSelectionChange() {
        const selectAll = document.getElementById('select-all');
        const selectAllMobile = document.getElementById('select-all-mobile');
        const checkboxes = Array.from(document.querySelectorAll('.siswa-checkbox'));
        const checkedCount = checkboxes.filter(cb => cb.checked).length;

        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        }
        if (selectAllMobile) {
            selectAllMobile.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
            selectAllMobile.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        }

        updateSelectedCount();
        updateBtnValidasiRapor();
    }

    document.querySelectorAll('.siswa-checkbox').forEach(cb => {
        cb.addEventListener('change', handleSelectionChange);
    });

    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        handleSelectionChange();
    }

    function toggleSelectAllMobile() {
        const selectAllMobile = document.getElementById('select-all-mobile');
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.checked = selectAllMobile.checked;
        }
        document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = selectAllMobile.checked);
        handleSelectionChange();
    }

    function bulkValidasiUjian() {
        const checked = document.querySelectorAll('.siswa-checkbox:checked');
        if (checked.length === 0) {
            showAlertModal('Data Belum Dipilih', 'Silakan pilih minimal satu siswa terlebih dahulu!');
            return;
        }
        showConfirmModal(
            'Validasi Akses Ujian',
            'Validasi akses ujian untuk ' + checked.length + ' siswa terpilih?',
            function () {
                document.getElementById('bulk-action').value = 'ujian';
                document.getElementById('bulk-form').submit();
            }
        );
    }

    function bulkValidasiRapor() {
        const checked = document.querySelectorAll('.siswa-checkbox:checked');
        if (checked.length === 0) {
            showAlertModal('Data Belum Dipilih', 'Silakan pilih minimal satu siswa terlebih dahulu!');
            return;
        }
        const eligible = Array.from(checked).filter(cb => cb.dataset.ketuaApproved === '1');
        const notEligible = checked.length - eligible.length;

        if (eligible.length === 0) {
            showAlertModal('Tidak Bisa Validasi Rapor', 'Semua siswa terpilih belum di-approve Ketua PKBM. Validasi rapor hanya bisa dilakukan setelah Ketua menyetujui.');
            return;
        }

        let msg = 'Validasi akses rapor untuk ' + eligible.length + ' siswa yang sudah di-approve Ketua?';
        if (notEligible > 0) {
            msg += ' (' + notEligible + ' siswa dilewati karena belum di-approve Ketua)';
        }

        showConfirmModal(
            'Validasi Akses Rapor',
            msg,
            function () {
                document.getElementById('bulk-action').value = 'rapor';
                document.getElementById('bulk-form').submit();
            }
        );
    }

    function confirmAction(url, message) {
        showConfirmModal(
            'Konfirmasi Aksi',
            message,
            function () {
                const form = document.getElementById('action-form');
                form.action = url;
                form.submit();
            }
        );
    }

    function confirmClassAction(formId, title, message) {
        showConfirmModal(
            title,
            message,
            function () {
                document.getElementById(formId).submit();
            }
        );
    }

    handleSelectionChange();
</script>
@endsection
