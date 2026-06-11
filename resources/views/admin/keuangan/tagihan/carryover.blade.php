@extends('layouts.sneat')

@section('title', 'Tarik Tunggakan ke TA Aktif')
@section('page-title', 'Tarik Tunggakan')
@section('page-subtitle', 'Alihkan tunggakan TA lama menjadi tagihan baru di TA aktif')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/admin/keuangan/tagihan/carryover.css')
@endsection

@php $baseRouteName = 'admin.keuangan.tagihan'; @endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y carryover-page">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route($baseRouteName . '.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Tagihan
        </a>
    </div>

    {{-- Soft warning --}}
    <div class="soft-warning">
        <i class="fas fa-info-circle"></i>
        <strong>Best practice:</strong> Pastikan tagihan reguler TA aktif (SPP, Buku, Seragam, dll) sudah dibuat untuk siswa terkait sebelum eksekusi carryover. Carryover hanya menambah tagihan baru — tidak menimpa tagihan yang sudah ada.
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="carryover-summary">
                <div class="cs-icon target">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="cs-body">
                    <div class="label">TA Tujuan (Aktif)</div>
                    <div class="value">{{ $taAktif->nama_tahun_ajaran }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="carryover-summary">
                <div class="cs-icon students">
                    <i class="fas fa-users"></i>
                </div>
                <div class="cs-body">
                    <div class="label">Siswa dengan Tunggakan</div>
                    <div class="value">{{ $totalSiswa }} <small class="value-unit">siswa</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="carryover-summary">
                <div class="cs-icon total">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="cs-body">
                    <div class="label">Total Tunggakan</div>
                    <div class="value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route($baseRouteName . '.carryover') }}" class="filter-bar">
        <div class="filter-field">
            <label class="form-label small fw-bold mb-1">Cabang</label>
            <select name="cabang_id" class="form-select form-select-sm" data-carryover-auto-submit>
                <option value="">Semua Cabang</option>
                @foreach($cabangList as $c)
                    <option value="{{ $c->id }}" @selected($selectedCabangId == $c->id)>{{ $c->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        @if($selectedCabangId)
            <div class="filter-reset">
                <a href="{{ route($baseRouteName . '.carryover') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        @endif
    </form>

    @php
        // Pre-build detail data per siswa untuk konsumsi JS (compact)
        $detailData = [];
        foreach ($kandidat as $row) {
            $tagihanList = [];
            foreach ($row['perTahun'] as $bt) {
                foreach ($bt['tagihan'] as $t) {
                    $tagihanList[] = [
                        'ta' => $bt['tahun_ajaran']->nama_tahun_ajaran ?? '-',
                        'jenis' => ucwords(str_replace('_', ' ', $t->jenis_tagihan ?? '')),
                        'keterangan' => $t->keterangan ?: '-',
                        'sisa' => (float) $t->sisa,
                    ];
                }
            }
            $detailData[$row['siswa']->id] = [
                'nama' => $row['siswa']->nama_lengkap,
                'total' => (float) $row['totalTunggakan'],
                'jumlah' => $row['jumlahItem'],
                'tagihan' => $tagihanList,
                'tas' => collect($row['perTahun'])->map(fn($b) => $b['tahun_ajaran']->nama_tahun_ajaran ?? '-')->values()->all(),
            ];
        }
    @endphp

    <div class="card">
        <div class="card-body p-0">
            @if($kandidat->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-check-circle text-success"></i>
                    <h5 class="fw-bold mb-1">Tidak Ada Tunggakan</h5>
                    <p class="mb-0">Semua siswa sudah lunas atau tunggakan TA lama sudah dialihkan.</p>
                </div>
            @else
                <form id="carryover-form" method="POST" action="{{ route($baseRouteName . '.carryover.execute') }}">
                    @csrf

                    <div class="table-responsive">
                        <table class="kandidat-table">
                            <thead>
                                <tr>
                                    <th class="checkbox-cell"><input type="checkbox" id="check-all" title="Pilih semua"></th>
                                    <th>Siswa</th>
                                    <th>Tunggakan TA</th>
                                    <th class="total-column">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kandidat as $row)
                                    @php $sid = $row['siswa']->id; @endphp
                                    <tr class="kandidat-row">
                                        <td class="checkbox-cell" data-label="Pilih">
                                            <input type="checkbox" name="siswa_ids[]" value="{{ $sid }}" class="siswa-check">
                                        </td>
                                        <td data-label="Siswa">
                                            <div class="siswa-info">
                                                <span class="nama">{{ $row['siswa']->nama_lengkap }}</span>
                                                <span class="meta">
                                                    NIS: {{ $row['siswa']->nis ?: '-' }}
                                                    @if($row['siswa']->kelas)
                                                        · {{ $row['siswa']->kelas->nama_kelas }}
                                                    @endif
                                                    @if($row['siswa']->cabang)
                                                        · {{ $row['siswa']->cabang->nama_cabang }}
                                                    @endif
                                                    @if($row['siswa']->status === 'lulus')
                                                        · <span class="badge bg-secondary">ALUMNI</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="TA Sumber">
                                            @foreach($row['perTahun'] as $bt)
                                                <span class="ta-pill">{{ $bt['tahun_ajaran']->nama_tahun_ajaran ?? 'TA -' }}</span>
                                            @endforeach
                                        </td>
                                        <td data-label="Total" class="total-cell">
                                            <span class="total-tunggakan">Rp {{ number_format($row['totalTunggakan'], 0, ',', '.') }}</span>
                                            <div class="carryover-item-count">
                                                {{ $row['jumlahItem'] }} tagihan ·
                                                <a class="detail-link" data-detail-id="{{ $sid }}">Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="action-bar">
                        <div>
                            <span class="text-muted small"><span id="selected-count">0</span> siswa terpilih</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary" id="btn-preview" disabled>
                                <i class="fas fa-eye me-1"></i>Pratinjau
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-execute" disabled>
                                <i class="fas fa-arrow-circle-right me-1"></i>Eksekusi Carryover
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<template
    id="carryoverConfig"
    data-preview-url="{{ route($baseRouteName . '.carryover.preview') }}"
    data-csrf-token="{{ csrf_token() }}"
    data-detail="{{ json_encode($detailData ?? new \stdClass(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
></template>
@endsection

@push('scripts')
    @vite('resources/js/admin/keuangan/tagihan/carryover.js')
@endpush
