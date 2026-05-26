@extends('layouts.sneat')

@section('title', 'Edit Rapor')
@section('page-title', 'Edit Rapor')
@section('page-subtitle', 'Edit catatan dan kelengkapan rapor siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
<style>
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }
    .total-box {
        padding: 10px;
        background: #f0f9ff;
        border-radius: 8px;
        margin-top: 26px;
        text-align: center;
    }
    .drag-handle {
        cursor: grab;
        color: #adb5bd;
        font-size: 16px;
    }
    .drag-handle:hover { color: #4e73df; }
    .sortable-ghost { opacity: 0.4; background: #e8f0fe !important; }
    .sortable-chosen { background: #f0f9ff; }
    .row-hidden { opacity: 0.5; }
    .row-hidden td { text-decoration: line-through; }
    .btn-arrow { padding: 2px 6px; font-size: 11px; line-height: 1; }
    .kelompok-header td {
        background: #e8f0fe !important;
        font-weight: 700;
        color: #4e73df;
        font-size: 13px;
        padding: 10px 16px !important;
    }

    /* ─── Mobile responsive: tabel jadi card per row (compact + drag-friendly) ─── */
    @media (max-width: 767.98px) {
        .rapor-edit-page,
        .rapor-edit-page > .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            box-sizing: border-box;
        }

        /* Container hapus horizontal scroll */
        .rapor-edit-page .card-body.p-0 .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .rapor-edit-page .table {
            min-width: 0 !important;
        }

        #nilaiTable,
        #kegiatanTable {
            min-width: 0 !important;
            max-width: 100% !important;
            table-layout: fixed;
        }

        #nilaiTable *,
        #kegiatanTable * {
            box-sizing: border-box;
        }

        /* ── Tabel Nilai Mapel: setiap row jadi card ── */
        #nilaiTable, #nilaiTable tbody, #nilaiTable tfoot {
            display: block;
            width: 100% !important;
        }
        #nilaiTable thead { display: none; }
        #nilaiTable tbody {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 8px;
        }
        #nilaiTable tr {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 8px;
            padding: 10px 12px;
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            position: relative;
        }
        #nilaiTable td {
            display: flex;
            flex-direction: column;
            gap: 2px;
            border: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            min-width: 0;
            text-align: left !important;
        }
        #nilaiTable td::before {
            content: attr(data-label);
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Cell 1 — Drag handle (LARGE for touch) */
        #nilaiTable td:nth-child(1) {
            order: 1;
            flex: 0 0 32px;
            align-items: center;
        }
        #nilaiTable td:nth-child(1)::before { display: none; }
        #nilaiTable .drag-handle {
            font-size: 20px !important;
            color: #94a3b8;
            cursor: grab;
            padding: 6px;
            touch-action: none;
        }
        #nilaiTable .drag-handle:active { cursor: grabbing; color: #4e73df; }

        /* Cell 2 — No (small badge top-right) */
        #nilaiTable td:nth-child(2) {
            order: 2;
            flex: 1 1 auto;
            align-items: flex-end;
            font-size: 10px;
            color: #94a3b8;
        }
        #nilaiTable td:nth-child(2)::before { display: none; }

        /* Cell 3 — Mata Pelajaran (BIG header) */
        #nilaiTable td:nth-child(3) {
            order: 3;
            flex: 1 1 100%;
        }
        #nilaiTable td:nth-child(3)::before { display: none; }
        #nilaiTable td:nth-child(3) strong { font-size: 14px; color: #1e293b; }

        /* Cell 4 — Nilai (inline pill) */
        #nilaiTable td:nth-child(4) {
            order: 4;
            flex: 0 0 auto;
            background: #f0f9ff !important;
            border-radius: 8px;
            padding: 6px 10px !important;
            min-height: 50px;
            justify-content: center;
        }
        #nilaiTable td:nth-child(4) strong { font-size: 15px !important; }

        /* Cell 5 — Kelompok (PAS only) atau Tampil (PTS) */
        #nilaiTable td:nth-child(5) {
            order: 5;
            flex: 0 0 auto;
            background: #f8fafc !important;
            border-radius: 8px;
            padding: 6px 10px !important;
            align-items: center;
            min-height: 50px;
            justify-content: center;
        }

        /* Cell 6 — Tampil (PAS) atau Urutan (PTS) */
        #nilaiTable td:nth-child(6) {
            order: 6;
            flex: 0 0 auto;
            background: #f8fafc !important;
            border-radius: 8px;
            padding: 6px 10px !important;
            align-items: center;
            min-height: 50px;
            justify-content: center;
        }

        /* Cell 7 — Urutan (PAS) atau Deskripsi (PTS) */
        #nilaiTable td:nth-child(7) {
            order: 7;
            flex: 0 0 auto;
            background: #f8fafc !important;
            border-radius: 8px;
            padding: 6px 10px !important;
            min-height: 50px;
            justify-content: center;
        }

        /* Cell 8 — Deskripsi (PAS only, full width row) */
        #nilaiTable td:nth-child(8) {
            order: 8;
            flex: 1 1 100%;
            background: #f8fafc !important;
            padding: 8px !important;
            border-radius: 8px;
        }

        /* Force deskripsi (terakhir cell) full width — PTS variation */
        #nilaiTable td.align-middle:last-child {
            flex: 1 1 100% !important;
            background: #f8fafc !important;
            padding: 8px !important;
            border-radius: 8px;
        }

        /* Arrow buttons: side-by-side */
        #nilaiTable .btn-group-vertical {
            flex-direction: row !important;
            gap: 4px;
        }
        #nilaiTable .btn-arrow {
            min-width: 32px;
            min-height: 32px;
            padding: 4px 8px !important;
            font-size: 12px !important;
        }

        /* Form controls compact */
        #nilaiTable .form-select,
        #nilaiTable .form-select-sm {
            min-width: 60px !important;
            width: auto !important;
            font-size: 12px;
        }
        #nilaiTable .form-check { padding-left: 0; }
        #nilaiTable input[type="text"] { font-size: 13px; }

        /* tfoot rata-rata */
        #nilaiTable tfoot tr {
            display: flex;
            justify-content: center;
            background: #f3f4f6;
            padding: 8px;
            border-radius: 8px;
        }
        #nilaiTable tfoot td:empty,
        #nilaiTable tfoot td[colspan]:empty { display: none; }

        /* ── Tabel Kegiatan Ekstra ── */
        #kegiatanTable, #kegiatanTable tbody {
            display: block;
            width: 100% !important;
        }
        #kegiatanTable thead { display: none; }
        #kegiatanTable tbody {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 8px;
        }
        #kegiatanTable tr {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 8px;
            padding: 10px 12px;
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            background: #fff;
            position: relative;
        }
        #kegiatanTable td {
            display: flex;
            flex-direction: column;
            gap: 2px;
            border: 0 !important;
            padding: 0 !important;
            min-width: 0;
        }
        #kegiatanTable td::before {
            content: attr(data-label);
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }
        /* No → absolute pojok */
        #kegiatanTable td:nth-child(1) {
            position: absolute;
            top: 4px;
            right: 42px;
            font-size: 10px;
            color: #94a3b8;
        }
        #kegiatanTable td:nth-child(1)::before { display: none; }
        #kegiatanTable td:nth-child(2) { flex: 1 1 100%; }  /* Nama */
        #kegiatanTable td:nth-child(3) { flex: 0 0 90px; }  /* Predikat */
        #kegiatanTable td:nth-child(4) { flex: 1 1 calc(100% - 100px); }  /* Keterangan */
        #kegiatanTable td:nth-child(5) {
            position: absolute;
            top: 4px;
            right: 6px;
        }
        #kegiatanTable td:nth-child(5)::before { display: none; }

        /* Layout final mobile: fit tanpa geser kanan */
        #nilaiTable tbody {
            padding: 10px;
        }

        #nilaiTable tbody tr {
            display: grid !important;
            grid-template-columns: 34px minmax(0, .95fr) minmax(0, 1.15fr) minmax(0, .9fr) minmax(0, .95fr);
            align-items: stretch;
            gap: 6px;
            width: 100% !important;
            max-width: 100% !important;
            padding: 9px;
            overflow: hidden;
        }

        #nilaiTable tbody td {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        #nilaiTable td[data-label="Drag"] {
            grid-column: 1;
            grid-row: 1 / span 2;
            align-items: center;
            justify-content: center;
        }

        #nilaiTable td[data-label="No"] {
            display: none !important;
        }

        #nilaiTable td[data-label="Mata Pelajaran"] {
            grid-column: 2 / -1;
            grid-row: 1;
            justify-content: center;
            min-height: 32px;
        }

        #nilaiTable td[data-label="Nilai"],
        #nilaiTable td[data-label="Kelompok"],
        #nilaiTable td[data-label="Tampil"],
        #nilaiTable td[data-label="Urutan"] {
            grid-row: 2;
            flex: initial !important;
            align-items: stretch;
            justify-content: flex-start;
            padding: 7px !important;
            min-height: 58px;
        }

        #nilaiTable td[data-label="Kelompok"] {
            padding: 6px !important;
        }

        #nilaiTable td[data-label="Nilai"] { grid-column: 2; }
        #nilaiTable td[data-label="Kelompok"] { grid-column: 3; }
        #nilaiTable td[data-label="Tampil"] { grid-column: 4; }
        #nilaiTable td[data-label="Urutan"] { grid-column: 5; }

        #nilaiTable tr:not(:has(td[data-label="Kelompok"])) td[data-label="Tampil"] {
            grid-column: 3;
        }

        #nilaiTable tr:not(:has(td[data-label="Kelompok"])) td[data-label="Urutan"] {
            grid-column: 4;
        }

        #nilaiTable td[data-label="Deskripsi Capaian"] {
            grid-column: 1 / -1;
            grid-row: 3;
            flex: initial !important;
            padding: 8px !important;
        }

        #nilaiTable td[data-label="Nilai"] strong {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1 1 auto;
            min-height: 28px;
            font-size: 14px !important;
        }

        #nilaiTable .form-select,
        #nilaiTable .form-select-sm,
        #nilaiTable input[type="text"] {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        #nilaiTable td[data-label="Kelompok"] .form-select,
        #kegiatanTable td[data-label="Predikat"] .form-select {
            height: 38px !important;
            min-height: 38px !important;
            padding: 6px 28px 6px 10px !important;
            background-position: right 9px center !important;
            background-size: 12px 10px !important;
            font-size: 14px !important;
            font-weight: 800;
            line-height: 1.2 !important;
            text-align: center;
        }

        #nilaiTable .form-check {
            width: 100%;
            min-height: 30px;
            align-items: center;
            justify-content: center !important;
            padding-left: 0;
        }

        #nilaiTable .form-check-input {
            float: none;
            margin-left: 0 !important;
        }

        #nilaiTable .btn-group-vertical {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
            gap: 4px;
        }

        #nilaiTable .btn-arrow {
            width: 100%;
            min-width: 0;
            min-height: 30px;
            padding: 4px !important;
        }

        #nilaiTable tfoot tr {
            display: flex !important;
            grid-template-columns: none !important;
            align-items: center;
            justify-content: center;
        }

        #nilaiTable tfoot td {
            width: auto !important;
            max-width: none !important;
        }

        #kegiatanTable tbody {
            padding: 10px;
        }

        #kegiatanTable tr {
            display: grid !important;
            grid-template-columns: minmax(88px, .42fr) minmax(0, 1fr) 40px;
            gap: 7px;
            width: 100% !important;
            max-width: 100% !important;
            padding: 10px;
            overflow: hidden;
        }

        #kegiatanTable td {
            position: static !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        #kegiatanTable td:nth-child(1) {
            display: none !important;
        }

        #kegiatanTable td:nth-child(2) {
            grid-column: 1 / -1;
            grid-row: 1;
        }

        #kegiatanTable td:nth-child(3) {
            grid-column: 1;
            grid-row: 2;
        }

        #kegiatanTable td:nth-child(4) {
            grid-column: 2;
            grid-row: 2;
        }

        #kegiatanTable td:nth-child(5) {
            grid-column: 3;
            grid-row: 2;
            align-self: end;
            justify-self: end;
        }

        #kegiatanTable td:nth-child(5) .btn {
            width: 40px !important;
            min-width: 40px;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        #kegiatanTable .form-control,
        #kegiatanTable .form-select {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }

        /* Sticky submit footer */
        .submit-card {
            position: sticky;
            bottom: 0;
            z-index: 5;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            border-top: 1px solid #e2e8f0;
            margin-bottom: 0 !important;
        }

        /* Reduce container padding mobile */
        .container-fluid.px-0 { padding: 0 4px; }
        .card.shadow.mb-4 { margin-bottom: 10px !important; }
        .card-body { padding: 10px; }
        .card-header.py-3 { padding: 10px !important; }
        .submit-card .card-body {
            display: grid;
            gap: 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="rapor-edit-page" style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-gray-900 mb-1">Edit Rapor - {{ $rapor->siswa->nama_lengkap }}</h4>
                    <p class="text-muted mb-0">
                        Semester: {{ ucfirst($rapor->semester) }} |
                        Jenis: {{ $rapor->jenis_rapor === 'akhir_semester' ? 'PAS' : 'PTS' }} |
                        Kelas: {{ $rapor->kelas->nama_kelas }}
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @php
                        $importLocked = $rapor->status !== 'draft' || $rapor->siswa->validasi_rapor_wali;
                        $importLockReason = $rapor->status !== 'draft'
                            ? 'Rapor sudah diterbitkan — tarik kembali ke draft dulu.'
                            : ($rapor->siswa->validasi_rapor_wali ? 'Rapor sudah dikirim ke Ketua PKBM — batalkan kiriman dulu.' : '');
                    @endphp
                    <a href="{{ route('wali.rapor.export-excel', $rapor->id) }}" class="btn btn-success btn-sm shadow-sm">
                        <i class="fas fa-file-excel me-1"></i>Export / Template Excel
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm shadow-sm"
                            {{ $importLocked ? 'disabled' : '' }}
                            @if(!$importLocked) data-bs-toggle="modal" data-bs-target="#importExcelModal" @endif
                            title="{{ $importLocked ? $importLockReason : 'Upload file Excel hasil export untuk update rapor' }}">
                        <i class="fas fa-file-upload me-1"></i>Import Excel
                    </button>
                    <button type="button" class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#resetNilaiModal">
                        <i class="fas fa-sync-alt me-1"></i>Reset Nilai
                    </button>
                    <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank">
                        <i class="fas fa-eye me-1"></i>Preview
                    </a>
                    <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash messages dari import --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-times-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Modal Import Excel --}}
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success" style="color:#fff !important;">
                    <h5 class="modal-title fw-bold" style="color:#fff !important;">
                        <i class="fas fa-file-upload me-2"></i>Import Rapor dari Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('wali.rapor.import-excel', $rapor->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="alert alert-info bg-light border-info small mb-3">
                            <strong>Cara penggunaan:</strong>
                            <ol class="mb-0 ps-3 mt-1">
                                <li>Klik tombol <strong>Export / Template Excel</strong> untuk download template berisi data rapor saat ini.</li>
                                <li>Buka file Excel, edit nilai/deskripsi/kehadiran/kegiatan/catatan sesuai kebutuhan.</li>
                                <li>Simpan file, lalu upload kembali di sini untuk update rapor.</li>
                            </ol>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">Format: .xlsx atau .xls — maksimal 5 MB. File harus hasil export dari rapor ini.</small>
                        </div>
                        <div class="alert alert-warning bg-light border-warning mb-0 small">
                            <ul class="mb-0">
                                <li>File yang di-upload <strong>harus hasil export rapor yang sama</strong> (identifier RAPOR_ID di file harus match).</li>
                                <li>Nilai di luar range 0-100 akan dilewati dengan peringatan.</li>
                                <li>Kegiatan ekstrakurikuler <strong>akan di-replace</strong> (data lama dihapus, diganti yang di Excel).</li>
                                <li>Hanya bisa import saat rapor masih draft & belum dikirim ke Ketua.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-file-upload me-1"></i> Upload &amp; Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Reset Nilai --}}
    <div class="modal fade" id="resetNilaiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-sync-alt me-2"></i>Reset Nilai Rapor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold mb-2">Reset semua nilai rapor dari data nilai terbaru?</h6>
                    <div class="alert alert-warning bg-light border-warning mb-0 small text-start">
                        <ul class="mb-0">
                            <li>Nilai angka akan di-generate ulang dari data nilai siswa</li>
                            <li>Deskripsi capaian yang sudah diedit <strong>akan hilang</strong></li>
                            <li>Urutan mata pelajaran akan direset</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('wali.rapor.reset-nilai', $rapor->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt me-1"></i> Ya, Reset Nilai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('wali.rapor.update', $rapor->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Data Kehadiran --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-calendar-check me-2"></i>Data Kehadiran
                </h6>
                <button type="button" class="btn btn-outline-info btn-sm" id="btnSyncKehadiran" title="Sinkron otomatis dari data presensi">
                    <i class="fas fa-sync-alt me-1"></i>Sinkron dari Presensi
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Sakit (hari)</label>
                            <input type="number" name="jumlah_sakit" class="form-control"
                                   value="{{ old('jumlah_sakit', $rapor->jumlah_sakit) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Izin (hari)</label>
                            <input type="number" name="jumlah_izin" class="form-control"
                                   value="{{ old('jumlah_izin', $rapor->jumlah_izin) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alpha (hari)</label>
                            <input type="number" name="jumlah_alpha" class="form-control"
                                   value="{{ old('jumlah_alpha', $rapor->jumlah_alpha) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="total-box">
                            <small class="text-muted">Total Ketidakhadiran</small>
                            <div class="h3 mb-0 fw-bold text-primary" id="totalKetidakhadiran">
                                {{ $rapor->jumlah_sakit + $rapor->jumlah_izin + $rapor->jumlah_alpha }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kegiatan Ekstra --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-running me-2"></i>Kegiatan Ekstrakurikuler
                </h6>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="addKegiatanRow()">
                    <i class="fas fa-plus me-1"></i>Tambah Kegiatan
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="kegiatanTable">
                        <thead>
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Kegiatan</th>
                                <th width="120" class="text-center">Predikat</th>
                                <th>Keterangan</th>
                                <th width="50" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kegiatanBody">
                            @foreach($kegiatanEkstra as $index => $kegiatan)
                                <tr>
                                    <td class="text-center align-middle kegiatan-no" data-label="No">{{ $index + 1 }}</td>
                                    <td data-label="Nama Kegiatan">
                                        <input type="text" name="kegiatan_ekstra[{{ $index }}][kegiatan_nama]"
                                               class="form-control form-control-sm"
                                               value="{{ old("kegiatan_ekstra.{$index}.kegiatan_nama", $kegiatan->kegiatan_nama) }}"
                                               placeholder="Nama kegiatan...">
                                    </td>
                                    <td data-label="Predikat">
                                        <select name="kegiatan_ekstra[{{ $index }}][predikat]" class="form-select form-select-sm">
                                            <option value="">-</option>
                                            <option value="A" {{ ($kegiatan->predikat ?? '') === 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ ($kegiatan->predikat ?? '') === 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ ($kegiatan->predikat ?? '') === 'C' ? 'selected' : '' }}>C</option>
                                        </select>
                                    </td>
                                    <td data-label="Keterangan">
                                        <input type="text" name="kegiatan_ekstra[{{ $index }}][keterangan]"
                                               class="form-control form-control-sm"
                                               value="{{ old("kegiatan_ekstra.{$index}.keterangan", $kegiatan->keterangan) }}"
                                               placeholder="Keterangan...">
                                    </td>
                                    <td class="text-center" data-label="Aksi">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeKegiatanRow(this)" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Catatan Wali Kelas --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-comment-alt me-2"></i>Catatan Wali Kelas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan / Komentar untuk Siswa</label>
                    <textarea name="catatan_wali_kelas" class="form-control" rows="5"
                              placeholder="Contoh: Siswa menunjukkan peningkatan yang baik dalam...">{{ old('catatan_wali_kelas', $rapor->catatan_wali_kelas) }}</textarea>
                    <small class="text-muted">Berikan catatan positif dan saran untuk perkembangan siswa</small>
                </div>
            </div>
        </div>

        {{-- Daftar Nilai --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Daftar Nilai Mata Pelajaran
                </h6>
                <small class="text-muted"><i class="fas fa-grip-vertical me-1"></i>Drag untuk mengubah urutan</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="nilaiTable">
                        <thead>
                            <tr>
                                <th width="30"></th>
                                <th width="40">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center" width="100">Nilai</th>
                                @if($rapor->jenis_rapor === 'akhir_semester')
                                    <th class="text-center" width="80">Kelompok</th>
                                @endif
                                <th class="text-center" width="60">Tampil</th>
                                <th width="30"></th>
                                <th>Deskripsi Capaian</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiSortable">
                            @php $totalNilai = 0; $jumlahMapel = 0; @endphp
                            @foreach($rapor->raporNilai as $index => $raporNilai)
                                @php
                                    $totalNilai += $raporNilai->nilai_angka;
                                    $jumlahMapel++;
                                    // Null override = "Tidak diatur" (mapel jadi Lainnya/-) — beda dari default 'A'
                                    $kelompok = $raporNilai->kelompok_override;
                                    $defaultKelompok = $raporNilai->mataPelajaran->kelompok ?? null;
                                @endphp
                                <tr data-id="{{ $raporNilai->id }}" class="{{ !$raporNilai->is_visible ? 'row-hidden' : '' }}">
                                    <td class="text-center align-middle" data-label="Drag">
                                        <i class="fas fa-grip-vertical drag-handle"></i>
                                    </td>
                                    <td class="text-center align-middle row-number" data-label="No">{{ $index + 1 }}</td>
                                    <td class="align-middle" data-label="Mata Pelajaran"><strong>{{ $raporNilai->mataPelajaran->nama_mapel }}</strong></td>
                                    <td class="text-center align-middle" data-label="Nilai" style="background: #f0f9ff;">
                                        <strong class="text-primary" style="font-size: 16px;">
                                            {{ number_format($raporNilai->nilai_angka, 2) }}
                                        </strong>
                                    </td>
                                    @if($rapor->jenis_rapor === 'akhir_semester')
                                        <td class="text-center align-middle" data-label="Kelompok">
                                            <select name="kelompok_override[{{ $raporNilai->id }}]" class="form-select form-select-sm" style="width: 80px; margin: 0 auto;"
                                                    title="{{ $defaultKelompok ? 'Default mapel: '.$defaultKelompok : 'Belum ada default di admin mapel' }}">
                                                <option value="" {{ $kelompok === null ? 'selected' : '' }}>-</option>
                                                <option value="A" {{ $kelompok === 'A' ? 'selected' : '' }}>A</option>
                                                <option value="B" {{ $kelompok === 'B' ? 'selected' : '' }}>B</option>
                                            </select>
                                            @if($defaultKelompok)
                                                <div class="small text-muted mt-1" style="font-size: 10px;" title="Default dari admin mapel">
                                                    <i class="fas fa-info-circle"></i> {{ $defaultKelompok }}
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-center align-middle" data-label="Tampil">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="hidden" name="visible[{{ $raporNilai->id }}]" value="0">
                                            <input class="form-check-input visibility-toggle" type="checkbox"
                                                   name="visible[{{ $raporNilai->id }}]" value="1"
                                                   {{ $raporNilai->is_visible ? 'checked' : '' }}
                                                   data-row-id="{{ $raporNilai->id }}">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle" data-label="Urutan">
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-light btn-arrow border" onclick="moveRow(this, 'up')" title="Naik"><i class="fas fa-chevron-up"></i></button>
                                            <button type="button" class="btn btn-light btn-arrow border" onclick="moveRow(this, 'down')" title="Turun"><i class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </td>
                                    <td class="align-middle" data-label="Deskripsi Capaian">
                                        <input type="text"
                                               name="deskripsi[{{ $raporNilai->id }}]"
                                               class="form-control form-control-sm"
                                               value="{{ old('deskripsi.' . $raporNilai->id, $raporNilai->deskripsi) }}"
                                               placeholder="Deskripsi capaian kompetensi...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background: #f3f4f6;">
                                <td colspan="{{ $rapor->jenis_rapor === 'akhir_semester' ? 3 : 2 }}" class="text-end fw-bold align-middle" style="padding: 16px;">RATA-RATA:</td>
                                <td class="text-center fw-bold align-middle text-primary" style="font-size: 18px;">
                                    {{ $jumlahMapel > 0 ? number_format($totalNilai / $jumlahMapel, 2) : '0.00' }}
                                </td>
                                <td colspan="{{ $rapor->jenis_rapor === 'akhir_semester' ? 4 : 3 }}"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($rapor->raporNilai->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-gray-200 mb-3"></i>
                        <p class="text-muted">Belum ada data nilai</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="card shadow mb-5 submit-card">
            <div class="card-body text-end">
                <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Auto calculate total ketidakhadiran
    document.querySelectorAll('input[name="jumlah_sakit"], input[name="jumlah_izin"], input[name="jumlah_alpha"]').forEach(input => {
        input.addEventListener('input', function() {
            const sakit = parseInt(document.querySelector('input[name="jumlah_sakit"]').value) || 0;
            const izin = parseInt(document.querySelector('input[name="jumlah_izin"]').value) || 0;
            const alpha = parseInt(document.querySelector('input[name="jumlah_alpha"]').value) || 0;
            document.getElementById('totalKetidakhadiran').textContent = sakit + izin + alpha;
        });
    });

    // SortableJS for drag reorder
    const sortableEl = document.getElementById('nilaiSortable');
    if (sortableEl) {
        new Sortable(sortableEl, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            // Touch support: tahan 150ms sebelum drag mulai (hindari accidental drag saat scroll)
            delay: 150,
            delayOnTouchOnly: true,
            touchStartThreshold: 5,
            forceFallback: false,
            onEnd: function() {
                updateRowNumbers();
                saveOrder();
            }
        });
    }

    // Arrow buttons to move rows up/down
    function moveRow(btn, direction) {
        const row = btn.closest('tr');
        const tbody = row.parentNode;

        if (direction === 'up' && row.previousElementSibling) {
            tbody.insertBefore(row, row.previousElementSibling);
        } else if (direction === 'down' && row.nextElementSibling) {
            tbody.insertBefore(row.nextElementSibling, row);
        }

        updateRowNumbers();
        saveOrder();
    }

    // Update row numbers after reorder
    function updateRowNumbers() {
        document.querySelectorAll('#nilaiSortable tr').forEach((row, index) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = index + 1;
        });
    }

    // Save order via AJAX
    function saveOrder() {
        const rows = document.querySelectorAll('#nilaiSortable tr[data-id]');
        const order = Array.from(rows).map(row => row.dataset.id);

        fetch('{{ route("wali.rapor.reorder-nilai", $rapor->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ order: order })
        }).catch(err => console.error('Reorder error:', err));
    }

    // Visibility toggle visual feedback
    document.querySelectorAll('.visibility-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const row = this.closest('tr');
            row.classList.toggle('row-hidden', !this.checked);
        });
    });

    // ── Kegiatan Ekstra ──────────────────────────────────────
    let kegiatanIndex = document.querySelectorAll('#kegiatanBody tr').length;

    function addKegiatanRow() {
        const tbody = document.getElementById('kegiatanBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center align-middle kegiatan-no" data-label="No">${kegiatanIndex + 1}</td>
            <td data-label="Nama Kegiatan"><input type="text" name="kegiatan_ekstra[${kegiatanIndex}][kegiatan_nama]" class="form-control form-control-sm" placeholder="Nama kegiatan..."></td>
            <td data-label="Predikat"><select name="kegiatan_ekstra[${kegiatanIndex}][predikat]" class="form-select form-select-sm"><option value="">-</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></td>
            <td data-label="Keterangan"><input type="text" name="kegiatan_ekstra[${kegiatanIndex}][keterangan]" class="form-control form-control-sm" placeholder="Keterangan..."></td>
            <td class="text-center" data-label="Aksi"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeKegiatanRow(this)"><i class="fas fa-trash-alt"></i></button></td>
        `;
        tbody.appendChild(row);
        kegiatanIndex++;
    }

    function removeKegiatanRow(btn) {
        btn.closest('tr').remove();
        document.querySelectorAll('#kegiatanBody .kegiatan-no').forEach((el, i) => el.textContent = i + 1);
    }

    // ── Sync Kehadiran dari Presensi ─────────────────────────
    document.getElementById('btnSyncKehadiran').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sinkronisasi...';

        fetch('{{ route("wali.rapor.kehadiran-auto", $rapor->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelector('input[name="jumlah_sakit"]').value = data.sakit;
                document.querySelector('input[name="jumlah_izin"]').value = data.izin;
                document.querySelector('input[name="jumlah_alpha"]').value = data.alpha;
                document.getElementById('totalKetidakhadiran').textContent = data.sakit + data.izin + data.alpha;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Berhasil!';
                btn.classList.replace('btn-outline-info', 'btn-outline-success');
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Sinkron dari Presensi';
                    btn.classList.replace('btn-outline-success', 'btn-outline-info');
                }, 2000);
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="fas fa-times me-1"></i>Gagal';
            btn.classList.replace('btn-outline-info', 'btn-outline-danger');
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Sinkron dari Presensi';
                btn.classList.replace('btn-outline-danger', 'btn-outline-info');
            }, 2000);
        })
        .finally(() => btn.disabled = false);
    });
</script>
@endsection
