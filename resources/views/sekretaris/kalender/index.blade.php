@extends('layouts.sneat')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Kelola kegiatan akademik tahun ajaran ' . ($tahunAjaranAktif->nama_tahun_ajaran ?? '-'))

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@vite(['resources/css/sekretaris/kalender/index.css'])
@endsection

@section('content')
@php
    $calendarData = $allCalendarEvents ?? $kalender ?? [];
@endphp

<template id="calendarData">{!! json_encode($calendarData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</template>
<div id="calendarConfig"
     data-show-route-template="{{ route('sekretaris.kalender.show', ':id') }}"
     data-delete-route-template="{{ route('sekretaris.kalender.destroy', ':id') }}"
     data-toggle-route-template="{{ route('sekretaris.kalender.toggle-visibility', ':id') }}"
     data-print-route="{{ route('sekretaris.kalender.cetak') }}"
     data-csrf-token="{{ csrf_token() }}"></div>
<div class="container-fluid px-4 ak-calendar-skin">

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
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in p-3 calendar-print-menu">
                        <a class="dropdown-item mb-2" href="{{ route('sekretaris.kalender.cetak', ['jenis' => 'bulanan', 'bulan' => now()->format('Y-m')]) }}" target="_blank">
                            <i class="fas fa-calendar-day me-2 text-primary"></i> Cetak Bulan Ini
                        </a>
                        <a class="dropdown-item" href="{{ route('sekretaris.kalender.cetak', ['jenis' => 'tahunan']) }}" target="_blank">
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
                <a href="{{ route('sekretaris.kalender.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
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
                        Kalender Akademik Sekretaris
                    </div>

                    <div class="calendar-container">
                        <div class="calendar-header">
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
                                            <a href="#" data-calendar-view="bulan" class="btn btn-sm fw-bold btn-primary" id="viewBulan">Bulan</a>
                                            <a href="#" data-calendar-view="minggu" class="btn btn-sm fw-bold btn-outline-primary" id="viewMinggu">Minggu</a>
                                            <a href="#" data-calendar-view="tahun" class="btn btn-sm fw-bold btn-outline-primary" id="viewTahun">Tahun</a>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button" data-calendar-prev class="btn btn-outline-primary btn-sm rounded-circle shadow-sm calendar-nav-button">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>

                                        <span class="month-title text-center calendar-month-title" id="monthTitle">
                                            @php
                                                echo date('F Y', strtotime(date('Y-m-01')));
                                            @endphp
                                        </span>

                                        <button type="button" data-calendar-next class="btn btn-outline-primary btn-sm rounded-circle shadow-sm calendar-nav-button">
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
                                            <a href="{{ route('sekretaris.kalender.show', $item->id) }}"
                                               class="btn btn-info btn-sm btn-circle shadow-sm"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sekretaris.kalender.edit', $item->id) }}"
                                               class="btn btn-warning btn-sm btn-circle shadow-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-circle shadow-sm"
                                                    data-delete-kalender
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->nama_kegiatan }}"
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
    @vite(['resources/js/sekretaris/kalender/index.js'])
@endsection
