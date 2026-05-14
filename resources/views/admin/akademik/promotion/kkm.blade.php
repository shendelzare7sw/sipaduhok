@extends('layouts.sneat')

@section('title', 'Pengaturan KKM')
@section('page-title', 'Pengaturan KKM')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('content')
@php
    $routePrefix = request()->routeIs('waka.*') ? 'waka.promotion' : 'admin.akademik.promotion';
    $tahunLabel = $tahun->nama_tahun_ajaran ?? $tahun->nama ?? $tahun->tahun_ajaran ?? '-';
    $totalMapel = $mapelList->count();
    $configuredCount = collect($existingKKM ?? [])->filter(function ($value) {
        return $value !== null;
    })->count();
    $averageKkm = collect($existingKKM ?? [])->filter(function ($value) {
        return $value !== null;
    })->avg();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="promotion-page">
        <div class="page-panel mb-4">
            <div class="panel-main">
                <span class="panel-kicker">Akademik</span>
                <h4 class="panel-title">Pengaturan KKM</h4>
                <p class="panel-subtitle mb-0">Atur nilai minimum kelulusan setiap mata pelajaran untuk tahun ajaran aktif.</p>
            </div>
            <form action="{{ route($routePrefix . '.kkm.index') }}" method="GET" class="panel-action">
                <label class="form-label mb-1">Jenjang</label>
                <select name="jenjang" class="form-select" onchange="this.form.submit()">
                    <option value="PAUD" {{ $jenjang == 'PAUD' ? 'selected' : '' }}>PAUD</option>
                    <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon primary"><i class="fas fa-calendar-alt"></i></div>
                    <span>Tahun Ajaran</span>
                    <strong>{{ $tahunLabel }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon info"><i class="fas fa-layer-group"></i></div>
                    <span>Jenjang</span>
                    <strong>{{ $jenjang }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon success"><i class="fas fa-book-open"></i></div>
                    <span>Mata Pelajaran</span>
                    <strong>{{ $totalMapel }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon warning"><i class="fas fa-bullseye"></i></div>
                    <span>Rata-rata KKM</span>
                    <strong>{{ is_null($averageKkm) ? 70 : number_format($averageKkm, 0) }}</strong>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <div>
                    <h5 class="mb-1">Daftar Mata Pelajaran</h5>
                    <p class="text-muted mb-0">Sudah diatur: {{ $configuredCount }} dari {{ $totalMapel }} mata pelajaran.</p>
                </div>
            </div>

            <div class="content-card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route($routePrefix . '.kkm.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                <input type="hidden" name="jenjang" value="{{ $jenjang }}">

                @if($mapelList->isEmpty())
                    <div class="empty-state">
                        <i class="bx bx-book-open"></i>
                        <h6>Belum ada mata pelajaran</h6>
                        <p>Jenjang <strong>{{ $jenjang }}</strong> belum memiliki mata pelajaran untuk diatur KKM-nya.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-clean align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Jenjang</th>
                                    <th class="text-center">KKM Saat Ini</th>
                                    <th class="text-md-end">Set KKM Baru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapelList as $i => $mapel)
                                <tr>
                                    <td data-label="No" class="text-muted">{{ $i + 1 }}</td>
                                    <td data-label="Mata Pelajaran">
                                        <div class="subject-name">{{ $mapel->nama_mapel }}</div>
                                    </td>
                                    <td data-label="Jenjang">
                                        <span class="soft-badge neutral">{{ $mapel->jenjang }}</span>
                                    </td>
                                    <td data-label="KKM Saat Ini" class="text-center">
                                        <span class="kkm-badge">
                                            {{ $existingKKM[$mapel->id] ?? 70 }}
                                        </span>
                                    </td>
                                    <td data-label="Set KKM Baru" class="text-md-end">
                                        <input type="number"
                                               name="kkm[{{ $mapel->id }}]"
                                               class="form-control form-control-sm kkm-input @error('kkm.'.$mapel->id) is-invalid @enderror"
                                               value="{{ $existingKKM[$mapel->id] ?? 70 }}"
                                               min="0" max="100" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="action-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Pengaturan KKM
                        </button>
                    </div>
                @endif
            </form>
            </div>
        </div>
    </div>
</div>

<style>
.promotion-page {
    --primary: #4361ee;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --ink: #1f2937;
    --muted: #64748b;
    --line: #e2e8f0;
    --soft: #f8fafc;
}

.page-panel,
.content-card,
.summary-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
}

.page-panel {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px;
}

.panel-kicker {
    color: var(--primary);
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.panel-title {
    color: var(--ink);
    font-weight: 800;
    margin-bottom: 4px;
}

.panel-subtitle,
.summary-card span {
    color: var(--muted);
}

.panel-action {
    min-width: 180px;
}

.summary-card {
    height: 100%;
    padding: 16px;
}

.summary-icon {
    align-items: center;
    border-radius: 10px;
    display: inline-flex;
    height: 36px;
    justify-content: center;
    margin-bottom: 14px;
    width: 36px;
}

.summary-icon.primary { background: rgba(67, 97, 238, .12); color: var(--primary); }
.summary-icon.success { background: rgba(16, 185, 129, .12); color: var(--success); }
.summary-icon.warning { background: rgba(245, 158, 11, .14); color: var(--warning); }
.summary-icon.info { background: rgba(6, 182, 212, .12); color: var(--info); }

.summary-card span {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
}

.summary-card strong {
    color: var(--ink);
    display: block;
    font-size: 20px;
    line-height: 1.2;
    margin-top: 5px;
}

.content-card {
    overflow: hidden;
}

.content-card-header {
    align-items: center;
    border-bottom: 1px solid var(--line);
    display: flex;
    justify-content: space-between;
    padding: 18px 20px;
}

.content-card-header h5 {
    color: var(--ink);
    font-weight: 800;
}

.content-card-body {
    padding: 20px;
}

.table-clean thead th {
    background: var(--soft);
    border-bottom: 1px solid var(--line);
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .03em;
    padding: 14px 16px;
    text-transform: uppercase;
}

.table-clean tbody td {
    border-bottom: 1px solid #eef2f7;
    padding: 15px 16px;
    vertical-align: middle;
}

.subject-name {
    color: var(--ink);
    font-weight: 700;
}

.soft-badge,
.kkm-badge {
    border-radius: 999px;
    display: inline-flex;
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
    padding: 8px 10px;
}

.soft-badge.neutral {
    background: #eef2ff;
    color: var(--primary);
}

.kkm-badge {
    background: rgba(16, 185, 129, .12);
    color: var(--success);
    min-width: 44px;
    justify-content: center;
}

.kkm-input {
    display: inline-block;
    max-width: 96px;
    text-align: center;
}

.action-footer {
    background: var(--soft);
    border-top: 1px solid var(--line);
    margin: 0 -20px -20px;
    padding: 16px 20px;
    text-align: right;
}

.empty-state {
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 42px 20px;
    text-align: center;
}

.empty-state i {
    color: #94a3b8;
    font-size: 42px;
    margin-bottom: 12px;
}

.empty-state h6 {
    color: var(--ink);
    font-weight: 800;
}

.empty-state p {
    color: var(--muted);
    margin-bottom: 0;
}

@media (max-width: 767.98px) {
    .page-panel,
    .content-card-header {
        align-items: stretch;
        flex-direction: column;
    }

    .panel-action {
        min-width: 0;
        width: 100%;
    }

    .content-card-body {
        padding: 14px;
    }

    .table-clean thead {
        display: none;
    }

    .table-clean tbody tr {
        border: 1px solid var(--line);
        border-radius: 12px;
        display: block;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .table-clean tbody td {
        align-items: center;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 14px;
        text-align: right !important;
    }

    .table-clean tbody td::before {
        color: var(--muted);
        content: attr(data-label);
        flex: 0 0 auto;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .03em;
        text-align: left;
        text-transform: uppercase;
    }

    .table-clean tbody td:last-child {
        border-bottom: 0;
    }

    .subject-name {
        max-width: 190px;
        text-align: right;
    }

    .kkm-input {
        max-width: 82px;
    }

    .action-footer {
        margin: 0 -14px -14px;
        text-align: stretch;
    }

    .action-footer .btn {
        width: 100%;
    }
}
</style>
@endsection
