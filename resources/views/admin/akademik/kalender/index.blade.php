@extends('layouts.sneat')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Kelola kegiatan akademik tahun ajaran ' . ($tahunAjaranAktif->nama_tahun_ajaran ?? '–'))

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Calendar Page Header */
    .calendar-page-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 18px;
        color: #0066cc;
        font-weight: 600;
    }

    /* Calendar Container */
    .calendar-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .calendar-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a4d8f;
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 300px;
    }

    .search-box input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 2px solid #ddd;
        border-radius: 25px;
        font-size: 14px;
        outline: none;
    }

    .search-box input:focus {
        border-color: #4e73df;
    }

    .search-box .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
    }

    .year-info {
        font-size: 16px;
        font-weight: 600;
        color: #1a4d8f;
    }

    /* Main Content Layout */
    .main-content {
        display: flex;
        gap: 0;
        align-items: stretch;
        position: relative;
    }

    .calendar-section {
        flex: 1;
        min-width: 0;
        padding-right: 15px;
        border-right: 2px solid #e5e7eb;
    }

    /* Calendar Navigation */
    .calendar-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .calendar-nav .btn {
        padding: 8px 16px;
    }

    .calendar-nav .month-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a4d8f;
    }

    /* Calendar Grid */
    .calendar-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid #1a4d8f;
        border-radius: 8px;
        overflow: hidden;
        table-layout: fixed;
    }

    .calendar-table th {
        background: #1a4d8f;
        color: white;
        padding: 15px;
        font-size: 18px;
        font-weight: 700;
        text-align: center;
    }

    .calendar-table td {
        border: 1px solid #ddd;
        padding: 8px;
        height: 100px;
        vertical-align: top;
        background: white;
    }

    .calendar-table td.other-month {
        background: #f9fafb;
    }

    .calendar-table td.other-month .date-number {
        color: #ccc;
    }

    .calendar-table td.today {
        background: #fffacd;
    }

    .date-number {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        font-size: 14px;
    }

    /* Event Styles */
    .event {
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 4px;
        margin: 2px 0;
        display: flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: transform 0.2s;
        color: white;
    }

    .event:hover {
        transform: scale(1.02);
    }

    .event-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
        background: white;
    }

    .event-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Event Type Colors */
    .event-field_trip {
        background: #17a2b8;
        color: white;
    }

    .event-outing {
        background: #28a745;
        color: white;
    }

    .event-live_in {
        background: #6610f2;
        color: white;
    }

    .event-hokfest {
        background: #fd7e14;
        color: white;
    }

    .event-pts {
        background: #ffc107;
        color: #212529;
    }

    .event-pts .event-dot {
        background: #212529;
    }

    .event-pas {
        background: #dc3545;
        color: white;
    }

    .event-libur {
        background: #6c757d;
        color: white;
    }

    .event-ujian {
        background: #e83e8c;
        color: white;
    }

    .event-acara_sekolah {
        background: #20c997;
        color: white;
    }

    .event-lainnya {
        background: #4e73df;
        color: white;
    }

    .event-tugas {
        background: #0891b2;
        color: white;
    }

    .event-deadline {
        background: #2563eb;
        color: white;
    }

    /* Resizer */
    .resizer {
        background-color: #e5e7eb;
        cursor: col-resize;
        width: 6px;
        min-height: 400px;
        border-radius: 0px;
        transition: background-color 0.2s;
        flex-shrink: 0;
        user-select: none;
        position: relative;
        z-index: 10;
        margin: 0 10px;
    }

    .resizer:hover,
    .resizing {
        background-color: #94a3b8;
    }

    /* Sidebar Event List */
    .legend {
        width: 300px;
        flex-shrink: 0;
        min-width: 200px;
        max-width: 600px;
        padding-left: 15px;
    }

    .legend-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .legend-header {
        background: #f8fafc;
        padding: 12px 15px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
        color: #1a4d8f;
        font-size: 14px;
    }

    .legend-body {
        max-height: 500px;
        overflow-y: auto;
        padding: 0;
    }

    .legend-body::-webkit-scrollbar {
        width: 4px;
    }

    .legend-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .event-item-sidebar {
        display: flex;
        gap: 12px;
        padding: 12px 15px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background 0.2s;
        position: relative;
    }

    .event-item-sidebar:hover {
        background: #f8fafc;
    }

    .event-item-sidebar:last-child {
        border-bottom: none;
    }

    .date-badge-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 40px;
        flex-shrink: 0;
        gap: 4px;
    }

    .large-color-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .d-date-small {
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
        text-align: center;
    }

    .event-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .event-title {
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-meta {
        display: flex;
        gap: 10px;
        font-size: 11px;
        color: #64748b;
    }

    /* Sidebar Legend Colors */
    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
        font-size: 13px;
        color: #333;
    }

    .legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot-field_trip { background: #17a2b8; }
    .dot-outing { background: #28a745; }
    .dot-live_in { background: #6610f2; }
    .dot-hokfest { background: #fd7e14; }
    .dot-pts { background: #ffc107; }
    .dot-pas { background: #dc3545; }
    .dot-libur { background: #6c757d; }
    .dot-ujian { background: #e83e8c; }
    .dot-acara_sekolah { background: #20c997; }
    .dot-lainnya { background: #4e73df; }

    /* Year view event colors */
    :root {
        --event-color-field_trip: #17a2b8;
        --event-color-outing: #28a745;
        --event-color-live_in: #6610f2;
        --event-color-hokfest: #fd7e14;
        --event-color-pts: #ffc107;
        --event-color-pas: #dc3545;
        --event-color-libur: #6c757d;
        --event-color-ujian: #e83e8c;
        --event-color-acara_sekolah: #20c997;
        --event-color-lainnya: #4e73df;
    }

    /* Tab Styling */
    .nav-tabs .nav-link {
        font-weight: 700;
        color: #858796;
        border: none;
        padding: 12px 20px;
    }

    .nav-tabs .nav-link.active {
        color: #4e73df;
        border-bottom: 3px solid #4e73df;
        background: transparent;
    }

    /* Table Styling */
    .table thead th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #4e73df;
    }

    /* Modal Styling */
    .detail-label {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #b7b9cc;
        margin-bottom: 2px;
    }

    .detail-value {
        font-weight: 700;
        color: #3a3b45;
        margin-bottom: 15px;
    }

    /* Responsive Design */
    @media (max-width: 1399px) {
        .main-content {
            flex-direction: column;
        }

        .calendar-section {
            border-right: none;
            border-bottom: 2px solid #e5e7eb;
            padding-right: 0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .legend {
            width: 100%;
            padding-left: 0;
        }

        .resizer {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
            overflow: hidden !important;
            margin: 0 !important;
        }
    }

    @media (max-width: 768px) {
        .calendar-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-box {
            max-width: 100%;
        }

        .calendar-container {
            padding: 16px;
        }

        .calendar-title {
            font-size: 18px;
        }

        .calendar-table th {
            padding: 8px 4px;
            font-size: 13px;
        }

        .calendar-table td {
            height: 80px;
            padding: 4px;
        }

        .event {
            font-size: 9px;
        }

        .calendar-nav {
            flex-direction: column;
            align-items: flex-start;
        }

        .calendar-nav .month-title {
            font-size: 16px;
        }

        .resizer {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
        }
    }

    @media (max-width: 576px) {
        .calendar-container {
            padding: 12px;
        }

        .calendar-title {
            font-size: 15px;
        }

        .calendar-table th {
            padding: 6px 2px;
            font-size: 11px;
        }

        .calendar-table td {
            height: 65px;
            padding: 2px;
        }

        .date-number {
            font-size: 11px;
            margin-bottom: 2px;
        }

        .event {
            font-size: 8px;
            padding: 2px 3px;
        }

        .calendar-nav .month-title {
            font-size: 14px;
            min-width: 110px;
        }

        .calendar-nav .btn {
            padding: 6px 10px;
            font-size: 13px;
        }

        .card-header {
            flex-wrap: wrap;
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .calendar-container {
            padding: 8px;
        }

        .calendar-section {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .calendar-table {
            min-width: 280px;
        }

        .calendar-table th {
            padding: 5px 1px;
            font-size: 9px;
        }

        .calendar-table td {
            height: 50px;
            padding: 1px;
        }

        .date-number {
            font-size: 10px;
        }

        .event-text {
            display: none;
        }

        .event {
            padding: 1px 2px;
            gap: 2px;
            justify-content: center;
        }

        .event-dot {
            width: 8px;
            height: 8px;
        }

        .calendar-nav .month-title {
            font-size: 13px;
            min-width: 100px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
<div class="container-fluid px-0">

    {{-- STATS CARDS --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Kegiatan</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $kalender->count() ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Kegiatan Aktif</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $kalender->where('status', 'aktif')->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Draft (Belum Rilis)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $kalender->where('status', 'draft')->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Tahun Ajaran</div>
                            <div class="h6 mb-0 fw-bold text-gray-800">{{ $tahunAjaranAktif->nama_tahun_ajaran ?? '-' }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-graduation-cap fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 fw-bold text-primary">Agenda & Kalender Akademik</h6>
            <div class="btn-group">
                <div class="dropdown no-arrow me-2">
                    <button class="btn btn-success btn-sm dropdown-toggle fw-bold shadow-sm" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-print fa-sm me-1"></i> Cetak / Export PDF
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in p-3" style="min-width: 250px;">
                        <a class="dropdown-item mb-2" href="{{ route('admin.akademik.kalender.cetak', ['jenis' => 'bulanan', 'bulan' => now()->format('Y-m')]) }}" target="_blank">
                            <i class="fas fa-calendar-day me-2 text-primary"></i> Cetak Bulan Ini
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.akademik.kalender.cetak', ['jenis' => 'tahunan']) }}" target="_blank">
                            <i class="fas fa-list-ul me-2 text-info"></i> Cetak List Tahunan
                        </a>
                        <hr class="my-2">
                        <div class="px-2">
                            <label class="small fw-bold">Custom Bulan:</label>
                            <input type="month" id="customMonth" class="form-control form-control-sm mb-2" value="{{ now()->format('Y-m') }}">
                            <a href="#" id="customCetakBtn" target="_blank" class="btn btn-primary btn-sm w-100">CETAK PDF</a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.akademik.kalender.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                    <i class="fas fa-plus-circle fa-sm me-1"></i> Tambah Agenda
                </a>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="calendarTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="cal-view-tab" data-bs-toggle="tab" href="#cal-view" role="tab"><i class="fas fa-th-large me-1"></i> Kalender Visual</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="list-view-tab" data-bs-toggle="tab" href="#list-view" role="tab"><i class="fas fa-list me-1"></i> Daftar Detail</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="cal-view" role="tabpanel">
                    {{-- REFACTORED CALENDAR VISUAL --}}
                    <div class="calendar-page-header">
                        <i class="fas fa-calendar-alt"></i>
                        Kalender Akademik Admin
                    </div>

                    <div class="calendar-container">
                        <div class="calendar-header">
                            <h1 class="calendar-title">Kalender Akademik</h1>
                            <div class="search-box">
                                <input type="text" id="searchEvent" placeholder="Cari kegiatan...">
                                <span class="search-icon"><i class="fas fa-search"></i></span>
                            </div>
                            <div class="year-info">Tahun Ajaran {{ $tahunAjaranAktif->nama_tahun_ajaran ?? '-' }}</div>
                        </div>

                        <div class="main-content">
                            <div class="calendar-section" id="calendarSection">
                                {{-- Calendar Navigation --}}
                                <div class="calendar-nav">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="btn-group shadow-sm">
                                            <a href="javascript:void(0)" onclick="changeView('bulan')" class="btn btn-sm fw-bold" id="viewBulan" style="background-color: #4e73df; color: white;">Bulan</a>
                                            <a href="javascript:void(0)" onclick="changeView('minggu')" class="btn btn-sm fw-bold btn-outline-primary" id="viewMinggu">Minggu</a>
                                            <a href="javascript:void(0)" onclick="changeView('tahun')" class="btn btn-sm fw-bold btn-outline-primary" id="viewTahun">Tahun</a>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <button onclick="previousMonth()" class="btn btn-outline-primary btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>

                                        <span class="month-title text-center" id="monthTitle" style="min-width: 150px;">
                                            @php
                                                echo date('F Y', strtotime(date('Y-m-01')));
                                            @endphp
                                        </span>

                                        <button onclick="nextMonth()" class="btn btn-outline-primary btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Calendar Grid (Month View) --}}
                                <table class="calendar-table" id="calendarTable">
                                    <thead>
                                        <tr>
                                            <th>Min</th>
                                            <th>Sen</th>
                                            <th>Sel</th>
                                            <th>Rab</th>
                                            <th>Kam</th>
                                            <th>Jum</th>
                                            <th>Sab</th>
                                        </tr>
                                    </thead>
                                    <tbody id="calendarBody">
                                        {{-- Calendar will be generated via JavaScript --}}
                                    </tbody>
                                </table>
                            </div>

                            {{-- Resizable Handle --}}
                            <div class="resizer" id="dragMe"></div>

                            {{-- Sidebar: Event List --}}
                            <div class="legend" id="sidebarLegend">
                                <div class="legend-card">
                                    <div class="legend-header">
                                        <i class="fas fa-info-circle me-2"></i> Keterangan
                                    </div>
                                    <div class="legend-body" id="sidebarContent">
                                        {{-- Events will be generated via JavaScript --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-view" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">NO</th>
                                    <th>NAMA KEGIATAN</th>
                                    <th class="text-center">JENIS</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center" width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kalender as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-dark">{{ $item->nama_kegiatan }}</div>
                                        @if($item->lampiran_surat)
                                            <a href="{{ asset('storage/' . $item->lampiran_surat) }}" target="_blank" class="small text-primary"><i class="fas fa-paperclip"></i> Lihat Lampiran</a>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle"><span class="badge bg-info shadow-sm">{{ $item->jenis_label }}</span></td>
                                    <td class="text-center align-middle small fw-bold">
                                        {{ $item->tanggal_mulai->format('d/m/Y') }}
                                        @if($item->tanggal_selesai) <br><span class="text-muted">s.d {{ $item->tanggal_selesai->format('d/m/Y') }}</span> @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->status == 'aktif') <span class="badge bg-success px-3">AKTIF</span>
                                        @elseif($item->status == 'draft') <span class="badge bg-warning text-white px-3">DRAFT</span>
                                        @else <span class="badge bg-secondary px-3">SELESAI</span> @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <a href="{{ route('admin.akademik.kalender.show', $item->id) }}"
                                               class="btn btn-info btn-sm btn-circle shadow-sm"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.akademik.kalender.edit', $item->id) }}"
                                               class="btn btn-warning btn-sm btn-circle shadow-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-circle shadow-sm"
                                                    onclick="confirmDelete({{ $item->id }}, `{{ str_replace('`', '\`', $item->nama_kegiatan) }}`)"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- MODAL: KONFIRMASI HAPUS --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus agenda ini?</h6>
                <p class="text-muted mb-0" id="deleteKalenderName"></p>
                <small class="text-danger d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan
                </small>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ==========================================
    // 1. GLOBAL VARIABLES
    // ==========================================
    let currentDate = new Date();
    let currentView = 'bulan';
    
    // Robust data ingestion: Handle case where data might be paginated (inside .data property)
    const rawData = @json($allCalendarEvents ?? $kalender ?? []);
    const sourceEvents = Array.isArray(rawData) ? rawData : (rawData.data || []);
    let allEvents = [...sourceEvents]; // Mutable array for filtering

    // Map event types to class names
    const eventTypeMap = {
        'field_trip': 'field_trip',
        'outing': 'outing',
        'live_in': 'live_in',
        'hokfest': 'hokfest',
        'pts': 'pts',
        'pas': 'pas',
        'libur': 'libur',
        'ujian': 'ujian',
        'acara_sekolah': 'acara_sekolah',
        'lainnya': 'lainnya'
    };

    // SEARCH LISTENER
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchEvent');
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                if(query) {
                    allEvents = sourceEvents.filter(ev => 
                        (ev.nama_kegiatan && ev.nama_kegiatan.toLowerCase().includes(query)) ||
                        (ev.keterangan && ev.keterangan.toLowerCase().includes(query))
                    );
                } else {
                    allEvents = [...sourceEvents];
                }
                
                // Re-render
                renderCalendar();
                updateSidebar();
            });
        }
    });

    // ==========================================
    // 2. CALENDAR GENERATION FUNCTIONS
    // ==========================================
    function getDaysInMonth(date) {
        return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    }

    function getFirstDayOfMonth(date) {
        return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
    }

    function parseDate(dateStr) {
        if (!dateStr) return null;
        
        // Handle ISO strings (e.g., 2025-01-20T00:00:00.000000Z) by taking just the date part
        if (dateStr.includes('T')) {
            dateStr = dateStr.split('T')[0];
        } else if (dateStr.includes(' ')) {
            dateStr = dateStr.split(' ')[0];
        }

        const parts = dateStr.split('-');
        if (parts.length !== 3) return null;
        
        // Ensure parts are integers
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const day = parseInt(parts[2], 10);
        
        if (isNaN(year) || isNaN(month) || isNaN(day)) return null;

        return new Date(year, month - 1, day);
    }

    function isSameDay(date1, date2) {
        if (!date1 || !date2) return false;
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    function getEventClass(eventType) {
        return 'event-' + (eventTypeMap[eventType] || 'lainnya');
    }

    function getEventsForDate(date) {
        if (!date) return [];
        
        return allEvents.filter(event => {
            if (!event.tanggal_mulai) return false;
            
            const startDate = parseDate(event.tanggal_mulai);
            if (!startDate) return false;
            
            const endDate = event.tanggal_selesai ? parseDate(event.tanggal_selesai) : startDate;
            if (!endDate) return false;

            // Normalize dates for comparison (remove time component)
            const checkDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            const normalizedStart = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
            const normalizedEnd = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());

            return checkDate >= normalizedStart && checkDate <= normalizedEnd;
        });
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const daysInMonth = getDaysInMonth(currentDate);
        const firstDay = getFirstDayOfMonth(currentDate);

        // Update month title
        const monthName = new Date(year, month, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        document.getElementById('monthTitle').textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

        // Ensure calendar table structure is correct (restore if needed)
        const calendarSection = document.getElementById('calendarSection');
        let calendarTable = document.getElementById('calendarTable');
        
        // robustly check if we need to rebuild the table
        // We need to rebuild if:
        // 1. Table doesn't exist
        // 2. It's not a TABLE tag (e.g. it's the year view GRID div)
        // 3. It doesn't have the correct structure (missing #calendarBody)
        const needsRebuild = !calendarTable || 
                             calendarTable.tagName !== 'TABLE' || 
                             !document.getElementById('calendarBody');

        if (needsRebuild) {
            // Remove existing element if it exists
            if (calendarTable) {
                calendarTable.remove();
            }
            
            // Create new table structure
            calendarTable = document.createElement('table');
            calendarTable.id = 'calendarTable';
            calendarTable.className = 'calendar-table';
            calendarTable.style.tableLayout = 'fixed';
            
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const dayHeaders = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            dayHeaders.forEach(day => {
                const th = document.createElement('th');
                th.textContent = day;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            calendarTable.appendChild(thead);
            
            const tbody = document.createElement('tbody');
            tbody.id = 'calendarBody';
            calendarTable.appendChild(tbody);
            
            // Insert table into calendar section
            // Try to find where to insert it (after nav)
            const calendarNav = calendarSection.querySelector('.calendar-nav');
            if (calendarNav && calendarNav.nextSibling) {
                calendarSection.insertBefore(calendarTable, calendarNav.nextSibling);
            } else {
                calendarSection.appendChild(calendarTable);
            }
        }

        // Create calendar grid
        const calendarBody = document.getElementById('calendarBody');
        calendarBody.innerHTML = '';

        let dayCounter = 1;
        const weeks = Math.ceil((daysInMonth + firstDay) / 7);

        for (let w = 0; w < weeks; w++) {
            const row = document.createElement('tr');
            for (let d = 0; d < 7; d++) {
                const cell = document.createElement('td');

                if (w === 0 && d < firstDay) {
                    // Empty cells before month starts
                    cell.classList.add('other-month');
                    row.appendChild(cell);
                } else if (dayCounter > daysInMonth) {
                    // Empty cells after month ends
                    cell.classList.add('other-month');
                    row.appendChild(cell);
                } else {
                    // Actual days
                    const cellDate = new Date(year, month, dayCounter);

                    // Check if today
                    const today = new Date();
                    if (isSameDay(cellDate, today)) {
                        cell.classList.add('today');
                    }

                    // Add date number
                    const dateDiv = document.createElement('div');
                    dateDiv.className = 'date-number';
                    dateDiv.textContent = dayCounter;
                    cell.appendChild(dateDiv);

                    // Add events
                    const events = getEventsForDate(cellDate);
                    events.slice(0, 3).forEach(event => {
                        const eventEl = document.createElement('div');
                        eventEl.className = `event ${getEventClass(event.jenis_kegiatan)}`;
                        eventEl.setAttribute('data-event', event.nama_kegiatan.toLowerCase());
                        eventEl.setAttribute('data-event-id', event.id);
                        eventEl.innerHTML = `
                            <span class="event-dot"></span>
                            <span class="event-text">${event.nama_kegiatan.substring(0, 15)}</span>
                        `;
                        eventEl.onclick = (e) => {
                            e.stopPropagation();
                            showEventDetail(event);
                        };
                        cell.appendChild(eventEl);
                    });

                    if (events.length > 3) {
                        const moreEl = document.createElement('div');
                        moreEl.className = 'event event-lainnya';
                        moreEl.innerHTML = `
                            <span class="event-dot"></span>
                            <span>+${events.length - 3} lainnya</span>
                        `;
                        moreEl.onclick = () => showEventsForDate(cellDate);
                        cell.appendChild(moreEl);
                    }

                    row.appendChild(cell);
                    dayCounter++;
                }
            }
            calendarBody.appendChild(row);
        }

        // Update sidebar
        updateSidebar();
    }

    function renderWeekView() {
        // Generate week dates
        const dayOfWeek = currentDate.getDay();
        const startOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - dayOfWeek);

        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        // Update title
        const startStr = startOfWeek.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        const endStr = endOfWeek.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        document.getElementById('monthTitle').textContent = `Minggu: ${startStr} - ${endStr}`;

        // Build week grid - ensure we have a table element
        const calendarSection = document.getElementById('calendarSection');
        let calendarTable = document.getElementById('calendarTable');
        
        // Remove existing element if it's not a table or doesn't exist
        if (calendarTable && calendarTable.tagName !== 'TABLE') {
            calendarTable.remove();
            calendarTable = null;
        }
        
        // Create new table for week view
        const newTable = document.createElement('table');
        newTable.id = 'calendarTable';
        newTable.className = 'calendar-table';
        newTable.style.tableLayout = 'fixed';

        // Create header with days of week
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        headerRow.style.backgroundColor = '#1a4d8f';
        headerRow.style.color = 'white';

        // Time column header
        const timeHeader = document.createElement('th');
        timeHeader.style.width = '80px';
        timeHeader.style.padding = '8px'; // Reduced padding
        timeHeader.style.fontSize = '0.75rem'; // Small font for Waktu
        timeHeader.textContent = 'Waktu';
        headerRow.appendChild(timeHeader);

        // Day columns
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        for (let d = 0; d < 7; d++) {
            const date = new Date(startOfWeek);
            date.setDate(startOfWeek.getDate() + d);

            const th = document.createElement('th');
            th.style.padding = '8px'; // Reduced padding
            th.style.textAlign = 'center';
            th.style.borderBottom = '2px solid white';

            const isToday = isSameDay(date, new Date());
            if (isToday) {
                th.style.backgroundColor = '#FFF9C4';
                th.style.color = '#333';
            }

            // Smaller fonts: 0.85rem for Day Name, 0.75rem for Date
            th.innerHTML = `<div style="font-size: 0.85rem; font-weight: 600;">${dayNames[d]}</div><div style="font-weight: 400; font-size: 0.75rem;">${date.getDate()}/${(date.getMonth() + 1).toString().padStart(2, '0')}</div>`;
            headerRow.appendChild(th);
        }
        thead.appendChild(headerRow);
        newTable.appendChild(thead);

        // Create tbody with time slots
        const tbody = document.createElement('tbody');

        // All day row
        const allDayRow = document.createElement('tr');
        const allDayTimeCell = document.createElement('td');
        allDayTimeCell.style.padding = '8px';
        allDayTimeCell.style.backgroundColor = '#f5f5f5';
        allDayTimeCell.style.fontWeight = 'bold';
        allDayTimeCell.style.fontSize = '12px';
        allDayTimeCell.style.textAlign = 'center';
        allDayTimeCell.textContent = 'All Day';
        allDayRow.appendChild(allDayTimeCell);

        for (let d = 0; d < 7; d++) {
            const date = new Date(startOfWeek);
            date.setDate(startOfWeek.getDate() + d);

            const cell = document.createElement('td');
            cell.style.padding = '8px';
            cell.style.minHeight = '50px';
            cell.style.verticalAlign = 'top';

            const isToday = isSameDay(date, new Date());
            if (isToday) {
                cell.style.backgroundColor = 'rgba(255, 249, 196, 0.3)';
            }

            // All day events
            const events = getEventsForDate(date);
            const allDayEvents = events.filter(e => !e.waktu_mulai);
            allDayEvents.forEach(event => {
                const eventEl = document.createElement('div');
                eventEl.className = `event event-${event.jenis_kegiatan}`;
                eventEl.style.fontSize = '11px';
                eventEl.style.marginBottom = '4px';
                eventEl.style.padding = '4px';
                eventEl.style.cursor = 'pointer';
                eventEl.innerHTML = `<span class="event-text">${event.nama_kegiatan.substring(0, 20)}</span>`;
                eventEl.onclick = () => showEventDetail(event);
                cell.appendChild(eventEl);
            });

            allDayRow.appendChild(cell);
        }
        tbody.appendChild(allDayRow);

        // Time slots (06:00 - 18:00)
        for (let hour = 6; hour <= 18; hour++) {
            const timeRow = document.createElement('tr');

            const timeCell = document.createElement('td');
            timeCell.style.padding = '8px';
            timeCell.style.backgroundColor = '#f5f5f5';
            timeCell.style.fontWeight = 'bold';
            timeCell.style.fontSize = '0.75rem'; // Smaller font size for time label
            timeCell.style.textAlign = 'center';
            timeCell.style.height = '60px';
            timeCell.textContent = `${hour.toString().padStart(2, '0')}:00`;
            timeRow.appendChild(timeCell);

            for (let d = 0; d < 7; d++) {
                const date = new Date(startOfWeek);
                date.setDate(startOfWeek.getDate() + d);

                const cell = document.createElement('td');
                cell.style.padding = '8px';
                cell.style.verticalAlign = 'top';
                cell.style.height = '60px';
                cell.style.borderLeft = '1px solid #e0e0e0';

                const isToday = isSameDay(date, new Date());
                if (isToday) {
                    cell.style.backgroundColor = 'rgba(255, 249, 196, 0.3)';
                }

                // Timed events
                const events = getEventsForDate(date);
                const timedEvents = events.filter(e => e.waktu_mulai && new Date(`2000-01-01 ${e.waktu_mulai}`).getHours() === hour);
                timedEvents.forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = `event event-${event.jenis_kegiatan}`;
                    eventEl.style.fontSize = '10px';
                    eventEl.style.padding = '4px';
                    eventEl.style.marginBottom = '2px';
                    eventEl.style.cursor = 'pointer';
                    eventEl.style.borderRadius = '3px';
                    eventEl.innerHTML = `
                        <div style="font-weight: 600; font-size: 11px;">${event.nama_kegiatan.substring(0, 18)}</div>
                    `;
                    eventEl.onclick = () => showEventDetail(event);
                    cell.appendChild(eventEl);
                });

                timeRow.appendChild(cell);
            }
            tbody.appendChild(timeRow);
        }

        newTable.appendChild(tbody);
        
        // Replace existing table or append if it doesn't exist
        if (calendarTable) {
            calendarTable.parentNode.replaceChild(newTable, calendarTable);
        } else {
            // Find where to insert (after calendar-nav)
            const calendarNav = calendarSection.querySelector('.calendar-nav');
            if (calendarNav && calendarNav.nextSibling) {
                calendarSection.insertBefore(newTable, calendarNav.nextSibling);
            } else {
                calendarSection.appendChild(newTable);
            }
        }

        updateSidebar();
    }

    function toggleVisibility(id, checkbox) {
        const isChecked = checkbox.checked;
        const labelIcon = checkbox.nextElementSibling.querySelector('i');
        
        // Optimistic UI update
        labelIcon.className = isChecked ? 'fas fa-eye text-success' : 'fas fa-eye-slash text-muted';

        // Construct URL robustly. We know the base structure.
        // Admin: /admin/akademik/kalender/{id}/toggle-visibility
        // Sekretaris: /sekretaris/kalender/{id}/toggle-visibility
        const baseUrl = window.location.pathname.includes('/admin/') 
            ? "{{ url('admin/akademik/kalender') }}" 
            : "{{ url('sekretaris/kalender') }}";
            
        fetch(`${baseUrl}/${id}/toggle-visibility`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update local data
                const event = allEvents.find(e => e.id === id);
                if (event) {
                    event.is_hidden_siswa = data.is_hidden;
                }
                
                // Show toast
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    console.log(data.message);
                }
            } else {
                throw new Error(data.message || 'Gagal update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert UI on error
            checkbox.checked = !isChecked;
            labelIcon.className = !isChecked ? 'fas fa-eye text-success' : 'fas fa-eye-slash text-muted';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengubah status visibilitas. Silakan coba lagi.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                console.error('Gagal update status');
            }
        });
    }

    function renderYearView() {
        const year = currentDate.getFullYear();
        document.getElementById('monthTitle').textContent = `Tahun ${year}`;

        const calendarTable = document.getElementById('calendarTable');
        const newDiv = document.createElement('div');
        newDiv.style.display = 'grid';
        newDiv.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
        newDiv.style.gap = '15px';
        newDiv.style.padding = '10px';

        const yearStart = new Date(year, 0, 1);
        const yearEnd = new Date(year, 11, 31);

        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        for (let m = 0; m < 12; m++) {
            const card = document.createElement('div');
            card.style.backgroundColor = '#fff';
            card.style.border = '1px solid #e0e0e0';
            card.style.borderRadius = '8px';
            card.style.padding = '20px';
            card.style.textAlign = 'center';
            card.style.cursor = 'pointer';
            card.style.transition = 'all 0.3s ease';
            card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';

            card.onmouseover = () => {
                card.style.transform = 'translateY(-4px)';
                card.style.boxShadow = '0 6px 12px rgba(0,0,0,0.15)';
            };
            card.onmouseout = () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            };

            // Get events for this month
            const monthEvents = allEvents.filter(event => {
                if (!event.tanggal_mulai) return false;
                const start = parseDate(event.tanggal_mulai);
                const end = event.tanggal_selesai ? parseDate(event.tanggal_selesai) : start;
                
                if (!start) return false;
                
                // Get month and year for comparison
                const sMonth = start.getMonth();
                const sYear = start.getFullYear();
                const eMonth = end.getMonth();
                const eYear = end.getFullYear();

                // Check if event overlaps with this month in this specific year
                // Logic:
                // 1. Starts in this month/year
                // 2. Ends in this month/year
                // 3. Spans over this month (starts before and ends after)
                
                const monthStart = new Date(year, m, 1);
                const monthEnd = new Date(year, m + 1, 0);
                
                return (start <= monthEnd && end >= monthStart);
            }).sort((a, b) => new Date(a.tanggal_mulai) - new Date(b.tanggal_mulai));

            const monthEventsCount = monthEvents.length;

            const iconDiv = document.createElement('div');
            iconDiv.style.marginBottom = '12px';
            iconDiv.style.fontSize = '28px';
            iconDiv.style.color = '#1a4d8f';
            iconDiv.innerHTML = '<i class="far fa-calendar-alt"></i>';
            card.appendChild(iconDiv);

            const monthTitle = document.createElement('h5');
            monthTitle.style.fontWeight = '700';
            monthTitle.style.color = '#1a4d8f';
            monthTitle.style.marginBottom = '4px';
            monthTitle.textContent = monthNames[m];
            card.appendChild(monthTitle);

            const yearDiv = document.createElement('div');
            yearDiv.style.fontSize = '12px';
            yearDiv.style.color = '#999';
            yearDiv.style.marginBottom = '12px';
            yearDiv.textContent = year;
            card.appendChild(yearDiv);

            const badgeDiv = document.createElement('div');
            badgeDiv.style.marginTop = '12px';
            badgeDiv.style.marginBottom = '12px';
            if (monthEventsCount > 0) {
                badgeDiv.innerHTML = `
                    <span style="display: inline-block; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                 color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <i class="fas fa-check-circle" style="margin-right: 4px;"></i> ${monthEventsCount} Kegiatan
                    </span>
                `;
            } else {
                badgeDiv.innerHTML = `
                    <span style="display: inline-block; background: #f0f0f0; color: #999; padding: 6px 12px;
                                 border-radius: 20px; font-size: 12px; border: 1px solid #ddd;">Kosong</span>
                `;
            }
            card.appendChild(badgeDiv);

            // Add event list if there are events
            if (monthEventsCount > 0) {
                const eventsListDiv = document.createElement('div');
                eventsListDiv.style.textAlign = 'left';
                eventsListDiv.style.marginTop = '12px';
                eventsListDiv.style.maxHeight = '150px';
                eventsListDiv.style.overflowY = 'auto';
                eventsListDiv.style.padding = '8px';
                eventsListDiv.style.backgroundColor = '#f8f9fa';
                eventsListDiv.style.borderRadius = '4px';
                eventsListDiv.style.fontSize = '11px';

                // Color map for event types
                const eventColorMap = {
                    'field_trip': '#17a2b8',
                    'outing': '#28a745',
                    'live_in': '#6610f2',
                    'hokfest': '#fd7e14',
                    'pts': '#ffc107',
                    'pas': '#dc3545',
                    'libur': '#6c757d',
                    'ujian': '#e83e8c',
                    'acara_sekolah': '#20c997',
                    'lainnya': '#4e73df'
                };

                monthEvents.slice(0, 5).forEach(event => {
                    const eventItem = document.createElement('div');
                    eventItem.style.padding = '6px 8px';
                    eventItem.style.marginBottom = '4px';
                    eventItem.style.backgroundColor = 'white';
                    eventItem.style.borderRadius = '3px';
                    const eventColor = eventColorMap[event.jenis_kegiatan] || eventColorMap['lainnya'];
                    eventItem.style.borderLeft = `3px solid ${eventColor}`;
                    eventItem.style.cursor = 'pointer';
                    eventItem.style.transition = 'all 0.2s';
                    
                    const eventName = document.createElement('div');
                    eventName.style.fontWeight = '600';
                    eventName.style.color = '#333';
                    eventName.style.marginBottom = '2px';
                    eventName.textContent = event.nama_kegiatan || 'Tanpa Nama';
                    eventItem.appendChild(eventName);

                    const eventDate = document.createElement('div');
                    eventDate.style.fontSize = '10px';
                    eventDate.style.color = '#666';
                    const startDate = parseDate(event.tanggal_mulai);
                    const dateStr = startDate ? startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '';
                    eventDate.textContent = dateStr;
                    eventItem.appendChild(eventDate);

                    eventItem.onmouseover = () => {
                        eventItem.style.backgroundColor = '#e9ecef';
                    };
                    eventItem.onmouseout = () => {
                        eventItem.style.backgroundColor = 'white';
                    };
                    eventItem.onclick = (e) => {
                        e.stopPropagation();
                        showEventDetail(event);
                    };

                    eventsListDiv.appendChild(eventItem);
                });

                if (monthEventsCount > 5) {
                    const moreItem = document.createElement('div');
                    moreItem.style.padding = '6px 8px';
                    moreItem.style.textAlign = 'center';
                    moreItem.style.fontSize = '10px';
                    moreItem.style.color = '#666';
                    moreItem.style.fontStyle = 'italic';
                    moreItem.textContent = `+${monthEventsCount - 5} kegiatan lainnya`;
                    eventsListDiv.appendChild(moreItem);
                }

                card.appendChild(eventsListDiv);
            }

            // Add click handler to switch to month view
            card.onclick = (e) => {
                // Prevent bubbling if clicking on an event inside the card
                if (e.target.closest('.event-item-sidebar')) return;
                
                // Go to month view
                currentDate = new Date(year, m, 1);
                currentView = 'bulan';
                updateViewButtons();
                
                // Force Render new view
                renderCalendar();
            };

            newDiv.appendChild(card);
        }

        calendarTable.parentNode.replaceChild(newDiv, calendarTable);
        newDiv.id = 'calendarTable';

        updateSidebar();
    }

    function updateSidebar() {
        const sidebarContent = document.getElementById('sidebarContent');

        // Filter events based on current view
        let filteredEvents = [];
        
        // Helper to check if event overlaps with a range [rangeStart, rangeEnd]
        const overlaps = (ev, rangeStart, rangeEnd) => {
             if (!ev.tanggal_mulai) return false;
             const start = parseDate(ev.tanggal_mulai);
             const end = ev.tanggal_selesai ? parseDate(ev.tanggal_selesai) : start;
             if (!start) return false;
             
             // Check intersection: start <= rangeEnd AND end >= rangeStart
             return (start <= rangeEnd && end >= rangeStart);
        };

        if (currentView === 'bulan') {
            // Show events for current month
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const monthStart = new Date(year, month, 1);
            const monthEnd = new Date(year, month + 1, 0); // Last day of month
            
            filteredEvents = allEvents.filter(ev => overlaps(ev, monthStart, monthEnd));
            
        } else if (currentView === 'minggu') {
            // Show events for current week
            const dayOfWeek = currentDate.getDay();
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(currentDate.getDate() - dayOfWeek);
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            
            filteredEvents = allEvents.filter(ev => overlaps(ev, startOfWeek, endOfWeek));

        } else if (currentView === 'tahun') {
            // Show all events for current year
            const year = currentDate.getFullYear();
            const yearStart = new Date(year, 0, 1);
            const yearEnd = new Date(year, 11, 31);
            
            filteredEvents = allEvents.filter(ev => overlaps(ev, yearStart, yearEnd));
        } else {
            filteredEvents = allEvents;
        }

        // Sort by date
        filteredEvents.sort((a, b) => {
            const dateA = parseDate(a.tanggal_mulai);
            const dateB = parseDate(b.tanggal_mulai);
            if (!dateA || !dateB) return 0;
            return dateA - dateB;
        });

        if (filteredEvents.length === 0) {
            sidebarContent.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="far fa-calendar-times fa-2x mb-2"></i>
                    <p class="small m-0">Tidak ada kegiatan ${currentView === 'bulan' ? 'bulan ini' : currentView === 'minggu' ? 'minggu ini' : 'tahun ini'}</p>
                </div>
            `;
            return;
        }

        sidebarContent.innerHTML = '';
        filteredEvents.forEach(event => {
            if (!event.tanggal_mulai) return;
            const startDate = parseDate(event.tanggal_mulai);
            if (!startDate) return;
            
            const dateStr = startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            const dayName = startDate.toLocaleDateString('id-ID', { weekday: 'long' });

            const itemEl = document.createElement('div');
            itemEl.className = 'event-item-sidebar';
            
            // Match student view design: Badge on left, Info on right
            itemEl.innerHTML = `
                <div class="date-badge-wrapper">
                    <div class="large-color-dot" style="background-color: var(--event-color-${event.jenis_kegiatan});"></div>
                    <span class="d-date-small">${dateStr}</span>
                </div>
                <div class="event-info flex-grow-1">
                    <div class="event-title" title="${(event.nama_kegiatan || '').replace(/"/g, '&quot;')}">
                        ${(event.nama_kegiatan || 'Tanpa Nama').substring(0, 35)}
                    </div>
                    <div class="event-meta">
                        <span class="meta-day"><i class="far fa-calendar"></i> ${dayName}</span>
                        ${event.waktu_mulai ? `<span class="meta-time"><i class="far fa-clock"></i> ${event.waktu_mulai.substring(0, 5)}</span>` : ''}
                    </div>
                </div>
                <div class="ms-2" onclick="event.stopPropagation()">
                    <div class="form-check form-switch" title="Tampilkan/Sembunyikan dari Siswa">
                        <input class="form-check-input" type="checkbox" role="switch"
                            style="cursor: pointer; transform: scale(0.8);"
                            id="visibilitySwitch-Sidebar-${event.id}" 
                            ${!event.is_hidden_siswa ? 'checked' : ''}
                            onchange="toggleVisibility(${event.id}, this)">
                        <label class="form-check-label" for="visibilitySwitch-${event.id}">
                            <i class="fas ${!event.is_hidden_siswa ? 'fa-eye text-success' : 'fa-eye-slash text-muted'}" style="font-size: 0.8rem;"></i>
                        </label>
                    </div>
                </div>
            `;
            itemEl.onclick = () => showEventDetail(event);
            sidebarContent.appendChild(itemEl);
        });
    }

    function showEventDetail(event) {
        window.location.href = "{{ route('admin.akademik.kalender.show', ':id') }}".replace(':id', event.id);
    }

    function showEventsForDate(date) {
        const events = getEventsForDate(date);
        const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        let html = `<div class="detail-label">Kegiatan pada ${dateStr}:</div>`;
        events.forEach(event => {
            html += `
                <div style="padding: 8px; margin: 5px 0; background: #f0f0f0; border-radius: 4px; cursor: pointer;"
                     onclick="showEventDetail({id: ${event.id}, nama_kegiatan: '${event.nama_kegiatan.replace(/'/g, "\\'")}', tanggal_mulai: '${event.tanggal_mulai}', tanggal_selesai: '${event.tanggal_selesai}', keterangan: '${(event.keterangan || '').replace(/'/g, "\\'")}', jenis_kegiatan: '${event.jenis_kegiatan}'})">
                    <strong>${event.nama_kegiatan}</strong>
                </div>
            `;
        });

        document.getElementById('eventDetails').innerHTML = html;
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        eventModal.show();
    }

    // ==========================================
    // 3. NAVIGATION FUNCTIONS
    // ==========================================
    function previousMonth() {
        if (currentView === 'bulan') {
            currentDate.setMonth(currentDate.getMonth() - 1);
        } else if (currentView === 'minggu') {
            currentDate.setDate(currentDate.getDate() - 7);
        } else if (currentView === 'tahun') {
            currentDate.setFullYear(currentDate.getFullYear() - 1);
        }
        renderCalendar();
    }

    function nextMonth() {
        if (currentView === 'bulan') {
            currentDate.setMonth(currentDate.getMonth() + 1);
        } else if (currentView === 'minggu') {
            currentDate.setDate(currentDate.getDate() + 7);
        } else if (currentView === 'tahun') {
            currentDate.setFullYear(currentDate.getFullYear() + 1);
        }
        renderCalendar();
    }

    function changeView(view) {
        currentView = view;
        updateViewButtons();

        if (view === 'minggu') {
            renderWeekView();
        } else if (view === 'tahun') {
            renderYearView();
        } else {
            renderCalendar();
        }
    }

    function updateViewButtons() {
        const btnBulan = document.getElementById('viewBulan');
        const btnMinggu = document.getElementById('viewMinggu');
        const btnTahun = document.getElementById('viewTahun');

        [btnBulan, btnMinggu, btnTahun].forEach(btn => {
            btn.style.backgroundColor = 'transparent';
            btn.style.color = '#4e73df';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });

        if (currentView === 'bulan') {
            btnBulan.style.backgroundColor = '#4e73df';
            btnBulan.style.color = 'white';
            btnBulan.classList.remove('btn-outline-primary');
            btnBulan.classList.add('btn-primary');
        } else if (currentView === 'minggu') {
            btnMinggu.style.backgroundColor = '#4e73df';
            btnMinggu.style.color = 'white';
            btnMinggu.classList.remove('btn-outline-primary');
            btnMinggu.classList.add('btn-primary');
        } else if (currentView === 'tahun') {
            btnTahun.style.backgroundColor = '#4e73df';
            btnTahun.style.color = 'white';
            btnTahun.classList.remove('btn-outline-primary');
            btnTahun.classList.add('btn-primary');
        }
    }

    // ==========================================
    // 4. SEARCH FUNCTIONALITY
    // ==========================================
    function initSearch() {
        const searchInput = document.getElementById('searchEvent');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const events = document.querySelectorAll('.event');
                const cells = document.querySelectorAll('.calendar-table td');

                cells.forEach(td => {
                    if (!td.classList.contains('today')) {
                        td.style.background = '';
                    }
                });

                events.forEach(event => {
                    const text = event.getAttribute('data-event') || event.textContent.toLowerCase();
                    const td = event.closest('td');

                    if (searchTerm === '') {
                        event.style.display = 'flex';
                    } else if (text.includes(searchTerm)) {
                        event.style.display = 'flex';
                        if (td && !td.classList.contains('today')) {
                            td.style.background = '#fffacd';
                        }
                    } else {
                        event.style.display = 'none';
                    }
                });
            });
        }
    }

    // ==========================================
    // 5. RESIZABLE SIDEBAR
    // ==========================================
    function initResizer() {
        const resizer = document.getElementById('dragMe');
        if (!resizer) return;

        const leftSide = resizer.previousElementSibling;
        const rightSide = resizer.nextElementSibling;
        const container = resizer.parentNode;

        let x = 0;
        let leftWidth = 0;
        let rightWidth = 0;

        const mouseDownHandler = function (e) {
            x = e.clientX;

            const leftRect = leftSide.getBoundingClientRect();
            const rightRect = rightSide.getBoundingClientRect();

            leftWidth = leftRect.width;
            rightWidth = rightRect.width;

            document.addEventListener('mousemove', mouseMoveHandler);
            document.addEventListener('mouseup', mouseUpHandler);
            resizer.classList.add('resizing');

            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'col-resize';
        };

        const mouseMoveHandler = function (e) {
            const dx = e.clientX - x;
            const newRightWidth = rightWidth - dx;

            if (newRightWidth > 150 && newRightWidth < 600) {
                rightSide.style.width = `${newRightWidth}px`;
            }
        };

        const mouseUpHandler = function () {
            document.removeEventListener('mousemove', mouseMoveHandler);
            document.removeEventListener('mouseup', mouseUpHandler);
            resizer.classList.remove('resizing');
            document.body.style.removeProperty('user-select');
            document.body.style.removeProperty('cursor');
        };

        resizer.addEventListener('mousedown', mouseDownHandler);
    }

    // ==========================================
    // 6. DELETE CONFIRMATION
    // ==========================================
    function confirmDelete(id, name) {
        document.getElementById('deleteKalenderName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route('admin.akademik.kalender.destroy', ':id') }}'.replace(':id', id);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // ==========================================
    // 7. PDF EXPORT
    // ==========================================
    function updateCetakUrl() {
        const val = document.getElementById('customMonth').value;
        const btn = document.getElementById('customCetakBtn');
        if(val) {
            btn.href = "{{ route('admin.akademik.kalender.cetak') }}?jenis=bulanan&bulan=" + val;
        }
    }

    // ==========================================
    // 8. INITIALIZATION
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        if (currentView === 'bulan') {
            renderCalendar();
        } else if (currentView === 'minggu') {
            renderWeekView();
        } else if (currentView === 'tahun') {
            renderYearView();
        }
        initSearch();
        initResizer();
        updateCetakUrl();
    });
</script>
@endsection
