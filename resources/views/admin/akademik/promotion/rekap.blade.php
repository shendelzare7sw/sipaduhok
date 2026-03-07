@extends('layouts.sneat')

@section('title', 'Rekap Kenaikan Kelas')
@section('page-title', 'Rekap Kenaikan Kelas')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('styles')
<style>
/* Make tab content cards stretch wider */
.tab-content > .tab-pane > .card {
    margin-left: -1rem;
    margin-right: -1rem;
    border-radius: 0;
}

/* Responsive Styles - Mobile Only */
@media (max-width: 768px) {
    /* Nav tabs scrollable */
    .nav-tabs {
        flex-wrap: nowrap !important;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .nav-tabs::-webkit-scrollbar {
        display: none;
    }

    .nav-tabs .nav-item {
        flex-shrink: 0;
    }

    .nav-tabs .nav-link {
        white-space: nowrap;
        font-size: 13px;
        padding: 8px 12px;
    }

    /* Card headers: stack vertically */
    .card-header .d-flex.flex-wrap {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }

    /* Filter forms: stack vertically */
    #historyFilterForm,
    #simulationFilterForm {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
        width: 100%;
    }

    #historyFilterForm select[style*="width: 140px"],
    #simulationFilterForm select[style*="width: 140px"] {
        width: 100% !important;
    }

    #historyFilterForm .input-group[style*="width: 220px"],
    #simulationFilterForm .input-group[style*="width: 220px"] {
        width: 100% !important;
    }

    #historyFilterForm .dropdown,
    #simulationFilterForm .dropdown {
        width: 100%;
    }

    #historyFilterForm .dropdown .btn,
    #simulationFilterForm .dropdown .btn {
        width: 100%;
        justify-content: space-between;
        display: flex;
        align-items: center;
    }

    /* Mode toggle btn-group */
    .btn-group[role="group"] {
        width: 100%;
    }

    .btn-group[role="group"] .btn {
        flex: 1;
        font-size: 12px;
        padding: 6px 8px;
    }

    /* Pagination area */
    .p-3.d-flex.justify-content-between {
        flex-direction: column !important;
        gap: 10px;
    }

    /* Mobile Card Pattern for Tables */
    .table-responsive.text-nowrap {
        white-space: normal !important;
        overflow-x: visible !important;
    }
    .table-card-mobile { white-space: normal !important; }
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block; border: 1px solid #e5e7eb; border-radius: 12px;
        margin-bottom: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff; position: relative;
    }
    .table-card-mobile tbody td {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border: none !important;
        border-bottom: 1px solid #f3f4f6 !important; text-align: right;
        white-space: normal !important; word-break: break-word;
    }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label); font-weight: 700; font-size: 10px;
        text-transform: uppercase; color: #9ca3af; letter-spacing: 0.5px;
        text-align: left; flex-shrink: 0; margin-right: 12px;
    }
    .table-card-mobile .mobile-card-head {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        font-weight: 700; font-size: 15px; padding: 14px !important;
        border-bottom: 2px solid #e0e7ff !important; display: block !important;
        text-align: left;
    }
    .table-card-mobile .mobile-card-actions {
        display: flex !important; justify-content: flex-end;
        padding: 10px 14px !important; background: #f9fafb;
    }
    .desktop-only-cell { display: none !important; }
    
    .mobile-text-end { text-align: right; }
    .mobile-text-start { text-align: left; }

    /* Mobile select all */
    .mobile-select-all { display: flex !important; }

    /* Promote button area */
    .sim-footer-area {
        flex-direction: column !important;
        gap: 12px;
        align-items: stretch !important;
    }
    .sim-footer-area > div { text-align: center; }
    .sim-footer-area nav { justify-content: center; }
}
@media (min-width: 769px) {
    .mobile-only-cell { display: none !important; }
    .mobile-select-all { display: none !important; }
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header removed, using layout title -->

    <!-- Statistics - Reorganized into 2 rows -->
    <!-- Statistics - Single Row 5 Columns -->
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5 class="card-title text-white">Naik Kelas</h5>
                    <h2>{{ $stats['NAIK_KELAS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5 class="card-title text-white">Lulus</h5>
                    <h2>{{ $stats['LULUS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h5 class="card-title text-dark">Naik (Dispensasi)</h5>
                    <h2>{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-label-primary h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Lulus (Dispensasi)</h5>
                    <h2 class="text-primary">{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</h2>
                    <small class="text-primary">Lulus meski ada tunggakan</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h5 class="card-title text-white">Tidak Naik</h5>
                    <h2>{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    @php
        $activeTab = request('tab') == 'history' ? 'history' : 'simulation';
    @endphp

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3" id="promotionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'simulation' ? 'active' : '' }}" id="simulation-tab" data-bs-toggle="tab" data-bs-target="#simulation" type="button" role="tab" aria-controls="simulation" aria-selected="{{ $activeTab == 'simulation' ? 'true' : 'false' }}">
                <i class="fas fa-flask me-1"></i> <span class="d-none d-md-inline">Simulasi / Keadaan Sekarang</span><span class="d-inline d-md-none">Simulasi</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'history' ? 'active' : '' }}" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="{{ $activeTab == 'history' ? 'true' : 'false' }}">
                <i class="fas fa-history me-1"></i> <span class="d-none d-md-inline">Riwayat Eksekusi ({{ $students->total() }})</span><span class="d-inline d-md-none">Riwayat ({{ $students->total() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'scheduling' ? 'active' : '' }}" id="scheduling-tab" data-bs-toggle="tab" data-bs-target="#scheduling" type="button" role="tab" aria-controls="scheduling" aria-selected="{{ $activeTab == 'scheduling' ? 'true' : 'false' }}">
                <i class="fas fa-clock me-1"></i> <span class="d-none d-md-inline">Jadwal Otomatis</span><span class="d-inline d-md-none">Jadwal</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="promotionTabsContent">
        
        <!-- Tab 1: Riwayat Eksekusi (Existing) -->
        <div class="tab-pane fade {{ $activeTab == 'history' ? 'show active' : '' }}" id="history" role="tabpanel" aria-labelledby="history-tab">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        {{-- Title --}}
                        <div>
                            <h5 class="mb-0">Hasil Eksekusi Kenaikan Kelas</h5>
                            <small class="text-muted">Tahun Ajaran: {{ $tahun->nama_tahun_ajaran }}</small>
                        </div>

                        {{-- Print Button --}}
                        @php
                            $printRouteName = str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.report.print' : 'waka.promotion.report.print';
                        @endphp
                        <a href="{{ route($printRouteName, array_merge(request()->only(['tahun_ajaran_id', 'status', 'cabang_id', 'jenjang', 'kelas_id']))) }}"
                           target="_blank"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-print me-1"></i> Cetak Laporan
                        </a>

                        {{-- Filter & Search --}}
                        <form action="{{ route(Route::currentRouteName()) }}" method="GET" id="historyFilterForm" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="tab" value="history">

                            {{-- Tahun Ajaran Selector (Priority) --}}
                            <select name="tahun_ajaran_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                                @foreach($allTahunAjaran as $ta)
                                    <option value="{{ $ta->id }}" {{ $tahun->id == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama_tahun_ajaran }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <i class="fas fa-filter me-1"></i> Filter
                                    @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId || $filterStatus)
                                        <span class="badge bg-primary ms-1">{{ collect([auth()->user()->role !== 'wakil_kepala_sekolah' ? $cabangId : null, $jenjangFilter, $kelasId, $filterStatus])->filter()->count() }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                                    <h6 class="dropdown-header px-0 text-uppercase small fw-bold mb-2">Opsi Filter</h6>

                                    {{-- Filter Cabang (hanya untuk admin) --}}
                                    @if(auth()->user()->role !== 'wakil_kepala_sekolah')
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold mb-1">Cabang</label>
                                        <select name="cabang_id" class="form-select form-select-sm">
                                            <option value="">Semua Cabang</option>
                                            @foreach($cabangs as $cabang)
                                                <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>
                                                    {{ $cabang->nama_cabang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    {{-- Filter Jenjang --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold mb-1">Jenjang</label>
                                        <select name="jenjang" class="form-select form-select-sm">
                                            <option value="">Semua Jenjang</option>
                                            @foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $j)
                                                <option value="{{ $j }}" {{ $jenjangFilter == $j ? 'selected' : '' }}>{{ $j }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filter Kelas --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold mb-1">Kelas</label>
                                        <select name="kelas_id" class="form-select form-select-sm">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                                    {{ $kelas->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filter Status --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold mb-1">Status Kelulusan</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="">Semua Status</option>
                                            <option value="NAIK_KELAS" {{ $filterStatus == 'NAIK_KELAS' ? 'selected' : '' }}>Naik Kelas</option>
                                            <option value="LULUS" {{ $filterStatus == 'LULUS' ? 'selected' : '' }}>Lulus</option>
                                            <option value="NAIK_KELAS_TUNGGAKAN" {{ $filterStatus == 'NAIK_KELAS_TUNGGAKAN' ? 'selected' : '' }}>Naik (Dispensasi)</option>
                                            <option value="LULUS_TUNGGAKAN" {{ $filterStatus == 'LULUS_TUNGGAKAN' ? 'selected' : '' }}>Lulus (Dispensasi)</option>
                                            <option value="TIDAK_NAIK_KELAS" {{ $filterStatus == 'TIDAK_NAIK_KELAS' ? 'selected' : '' }}>Tidak Naik</option>
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-check me-1"></i> Terapkan Filter
                                        </button>
                                        @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId || $filterStatus)
                                            <a href="{{ route(Route::currentRouteName(), ['tab' => 'history', 'tahun_ajaran_id' => $tahun->id]) }}"
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times me-1"></i> Reset Filter
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Search --}}
                            <div class="input-group" style="width: 220px;">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Cari nama..." value="{{ $search }}" autocomplete="off">
                                @if($search)
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="this.previousElementSibling.value=''; this.form.submit();"
                                            title="Hapus pencarian">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-card-mobile">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Kelas Asal</th>
                                <th>Kelas Tujuan</th>
                                <th>Status Bayar</th>
                                <th>Akademik</th>
                                <th>Hasil Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $data)
                            @php
                                $badge = match($data->status_kelulusan) {
                                    'NAIK_KELAS' => 'success',
                                    'LULUS' => 'info',
                                    'LULUS_TUNGGAKAN' => 'primary',
                                    'NAIK_KELAS_TUNGGAKAN' => 'warning',
                                    'TIDAK_NAIK_KELAS' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <td class="mobile-card-head">
                                    <div class="d-flex justify-content-between align-items-start gap-2" style="width: 100%;">
                                        <div class="d-flex align-items-center gap-2" style="flex: 1; min-width: 0;">
                                            <span class="text-wrap text-break lh-sm">{{ $data->nama_lengkap }}</span>
                                        </div>
                                        <div class="mobile-only-cell flex-shrink-0 ms-auto">
                                            <span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $data->status_kelulusan) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Kelas Asal">
                                    <div class="mobile-text-end">{{ $data->kelas_asal }}</div>
                                </td>
                                <td data-label="Kelas Tujuan">
                                    <div class="mobile-text-end">{{ $data->kelas_tujuan ?? '-' }}</div>
                                </td>
                                <td data-label="Status Bayar">
                                    <div class="mobile-text-end">
                                        @if($data->status_pembayaran == 'LUNAS')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-danger mb-1">Belum Lunas</span>
                                            @if($data->izin_khusus_ketua)
                                                <br><span class="badge bg-warning" title="Dispensasi Ketua">Override</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Akademik">
                                    <div class="mobile-text-end">
                                        {{ $data->persentase_nilai_tuntas }}% Tuntas
                                        <br>
                                        <small class="text-muted">{{ $data->jumlah_mapel_tuntas }}/{{ $data->total_mapel }} Mapel</small>
                                    </div>
                                </td>
                                <td data-label="Hasil Akhir" class="desktop-only-cell">
                                    <span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $data->status_kelulusan) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">
                                            Belum ada data eksekusi kenaikan kelas. <br>
                                            Silakan klik tombol <strong>"Proses Kenaikan Kelas"</strong> di tab Simulasi untuk menjalankan sistem.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $students->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <!-- Tab 2: Simulasi (New) -->
        <div class="tab-pane fade {{ $activeTab == 'simulation' ? 'show active' : '' }}" id="simulation" role="tabpanel" aria-labelledby="simulation-tab">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        {{-- Title --}}
                        <div>
                            <h5 class="mb-0">Simulasi Kenaikan Kelas</h5>
                            <small class="text-muted">Tahun Ajaran: {{ $tahun->nama_tahun_ajaran }}</small>
                        </div>

                        <!-- Execution Button (Trigger) -->
                        @if($promotionReadiness['isReady'])
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#executeModal">
                                <i class="fas fa-cogs me-1"></i> Proses Kenaikan Kelas
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Siapkan Tahun Ajaran dan Kelas baru terlebih dahulu">
                                <i class="fas fa-lock me-1"></i> Proses Kenaikan Kelas
                            </button>
                        @endif
                    </div>

                    <!-- Simulation Mode Toggle -->
                    <div class="btn-group mb-3" role="group" aria-label="Simulation Mode">
                        <a href="{{ route(Route::currentRouteName(), array_merge(request()->except('sim_mode'), ['sim_mode' => 'current', 'tab' => 'simulation'])) }}"
                           class="btn btn-sm btn-{{ ($simMode ?? 'current') == 'current' ? 'primary' : 'outline-primary' }}">
                            <i class="fas fa-users me-1"></i> Keadaan Saat Ini
                        </a>
                        <a href="{{ route(Route::currentRouteName(), array_merge(request()->except('sim_mode'), ['sim_mode' => 'historical', 'tab' => 'simulation'])) }}"
                           class="btn btn-sm btn-{{ ($simMode ?? 'current') == 'historical' ? 'primary' : 'outline-primary' }}">
                            <i class="fas fa-history me-1"></i> Keadaan Saat Eksekusi
                        </a>
                    </div>

                    {{-- Filter & Search --}}
                    <form action="{{ route(Route::currentRouteName()) }}" method="GET" id="simulationFilterForm" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="tab" value="simulation">
                        <input type="hidden" name="sim_mode" value="{{ $simMode ?? 'current' }}">

                        {{-- Tahun Ajaran Selector (Priority) --}}
                        <select name="tahun_ajaran_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                            @foreach($allTahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ $tahun->id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filter Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Filter
                                @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId)
                                    <span class="badge bg-primary ms-1">{{ collect([auth()->user()->role !== 'wakil_kepala_sekolah' ? $cabangId : null, $jenjangFilter, $kelasId])->filter()->count() }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold mb-2">Opsi Filter</h6>

                                {{-- Filter Cabang (hanya untuk admin) --}}
                                @if(auth()->user()->role !== 'wakil_kepala_sekolah')
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Cabang</label>
                                    <select name="cabang_id" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangs as $cabang)
                                            <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                {{-- Filter Jenjang --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Jenjang</label>
                                    <select name="jenjang" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang</option>
                                        @foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $j)
                                            <option value="{{ $j }}" {{ $jenjangFilter == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Kelas --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold mb-1">Kelas</label>
                                    <select name="kelas_id" class="form-select form-select-sm">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-check me-1"></i> Terapkan Filter
                                    </button>
                                    @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId)
                                        <a href="{{ route(Route::currentRouteName(), ['tab' => 'simulation', 'sim_mode' => $simMode ?? 'current', 'tahun_ajaran_id' => $tahun->id]) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i> Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="input-group" style="width: 220px;">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Cari nama..." value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="this.previousElementSibling.value=''; this.form.submit();"
                                        title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    {{-- TA Readiness Warnings --}}
                    @if(!$promotionReadiness['hasNextTA'])
                        <div class="alert alert-danger d-flex align-items-start mb-3">
                            <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Tahun Ajaran Baru Belum Dibuat!</strong><br>
                                <small>Buat Tahun Ajaran baru terlebih dahulu sebelum menjalankan proses kenaikan kelas. 
                                <a href="{{ route('admin.tahun-ajaran.create') }}" class="alert-link">Buat Tahun Ajaran →</a></small>
                            </div>
                        </div>
                    @elseif($promotionReadiness['kelasBaruCount'] == 0)
                        <div class="alert alert-warning d-flex align-items-start mb-3">
                            <i class="fas fa-exclamation-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Kelas Belum Dibuat di TA Baru!</strong><br>
                                <small>Buat struktur kelas untuk TA {{ $promotionReadiness['nextTA']->nama_tahun_ajaran }} agar siswa bisa dipindahkan.
                                <a href="{{ route('admin.kelas.index') }}" class="alert-link">Kelola Kelas →</a></small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Siap untuk Kenaikan Kelas!</strong><br>
                                <small>TA Baru: <strong>{{ $promotionReadiness['nextTA']->nama_tahun_ajaran }}</strong> dengan {{ $promotionReadiness['kelasBaruCount'] }} kelas tersedia.</small>
                            </div>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Data di bawah ini adalah <strong>SIMULASI REAL-TIME</strong> berdasarkan data keuangan dan nilai saat ini.
                    </div>

                    {{-- Mobile Select All --}}
                    <div class="mobile-select-all mb-2 align-items-center gap-2 px-2">
                        <input type="checkbox" id="selectAllSimMobile" onclick="toggleAllCheckboxes(this, 'simCheck')">
                        <label for="selectAllSimMobile" class="form-label mb-0 small fw-bold">Pilih Semua</label>
                    </div>

                    {{-- Select All Across Pages Banner --}}
                    @if($activeStudentsLinks->lastPage() > 1)
                    <div id="selectAllBanner" class="alert alert-warning py-2 px-3 mb-2 d-none">
                        <i class="fas fa-info-circle me-1"></i>
                        <span id="bannerText">Semua <strong>{{ $activeStudentsLinks->count() }}</strong> siswa di halaman ini dipilih.</span>
                        <a href="javascript:void(0)" id="selectAllPagesLink" class="fw-bold ms-1" onclick="enableSelectAllPages()">
                            Pilih semua <strong>{{ $totalActiveGlobal ?? $activeStudentsLinks->total() }}</strong> siswa di semua halaman
                        </a>
                        <a href="javascript:void(0)" id="clearSelectAllLink" class="fw-bold ms-1 d-none" onclick="clearSelectAllPages()">
                            Batalkan pilih semua halaman
                        </a>
                    </div>
                    @endif

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover table-card-mobile">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"><input type="checkbox" id="selectAllSim" onclick="toggleAllCheckboxes(this, 'simCheck')"></th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status Keuangan</th>
                                    <th>Status Akademik</th>
                                    <th>Prediksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($simulationData as $sim)
                                <tr>
                                    <td class="desktop-only-cell">
                                        @if(!$sim['result']['eligible'])
                                        <input type="checkbox" class="simCheck" value="{{ $sim['siswa']->id }}">
                                        @endif
                                    </td>
                                    <td class="mobile-card-head">
                                        <div class="d-flex justify-content-between align-items-start gap-2" style="width: 100%;">
                                            <div class="d-flex align-items-center gap-2" style="flex: 1; min-width: 0;">
                                                @if(!$sim['result']['eligible'])
                                                <input type="checkbox" class="simCheck mobile-only-cell flex-shrink-0" value="{{ $sim['siswa']->id }}" style="margin-top: 2px;">
                                                @endif
                                                <span class="text-wrap text-break lh-sm" style="flex: 1;">{{ $sim['siswa']->nama_lengkap }}</span>
                                            </div>
                                            <div class="mobile-only-cell flex-shrink-0 ms-auto">
                                                @if($sim['result']['eligible'])
                                                    @if(preg_match('/(9|IX|12|XII)/', strtoupper($sim['siswa']->kelas->nama_kelas ?? '')))
                                                        <span class="text-info fw-bold"><i class="fas fa-graduation-cap"></i></span>
                                                    @else
                                                        <span class="text-success fw-bold"><i class="fas fa-check-circle"></i></span>
                                                    @endif
                                                @else
                                                    <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i></span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Kelas">
                                        <div class="mobile-text-end">{{ $sim['siswa']->kelas->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td data-label="Keuangan">
                                        <div class="mobile-text-end">
                                            @if($sim['result']['financial']['status'] == 'LUNAS')
                                                <span class="badge bg-success">Lunas</span>
                                            @else
                                                <span class="badge bg-danger mb-1 text-wrap text-break lh-base" style="max-width: 100%; white-space: normal; text-align: left;">Tunggakan: Rp {{ number_format($sim['result']['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                                                @if($sim['result']['financial']['is_dispensasi'])
                                                    <br><span class="badge bg-warning">Dispensasi OK</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Akademik">
                                        <div class="mobile-text-end">
                                            @if($sim['result']['academic']['is_tuntas'])
                                                <span class="badge bg-success">Aman ({{ $sim['result']['academic']['percentage'] }}%)</span>
                                            @else
                                                <span class="badge bg-danger mb-1">Rawan ({{ $sim['result']['academic']['percentage'] }}%)</span>
                                                <br><small class="text-muted lh-1">Hanya {{ $sim['result']['academic']['tuntas_count'] }} mapel tuntas</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="desktop-only-cell">
                                        @if($sim['result']['eligible'])
                                            @if(preg_match('/(9|IX|12|XII)/', strtoupper($sim['siswa']->kelas->nama_kelas ?? '')))
                                                <span class="text-info fw-bold"><i class="fas fa-graduation-cap"></i> Siap Lulus</span>
                                            @else
                                                <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Siap Naik</span>
                                            @endif
                                        @else
                                            <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Tertunda</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Bulk Promote Selected Form --}}
                    @php
                        $routePrefix = str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik' : 'waka';
                    @endphp
                    <form id="promoteSelectedForm" action="{{ route($routePrefix . '.promotion.promote-selected') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                        <input type="hidden" name="select_all" id="selectAllFlag" value="0">
                        {{-- Retain current filters to be passed when select_all is true --}}
                        <input type="hidden" name="cabang_id" value="{{ $cabangId }}">
                        <input type="hidden" name="jenjang" value="{{ $jenjangFilter }}">
                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                        <input type="hidden" name="search" value="{{ $search }}">
                    </form>

                    <div class="p-3 d-flex justify-content-between align-items-center sim-footer-area">
                        <div>
                            <button type="button" id="promoteSelectedTrigger" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#promoteSelectedModal" disabled>
                                <i class="fas fa-arrow-up me-1"></i> Naikkan Terpilih
                            </button>
                        </div>
                        {{ $activeStudentsLinks->appends(['tab' => 'simulation'])->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Scheduling (New) -->
        <div class="tab-pane fade {{ $activeTab == 'scheduling' ? 'show active' : '' }}" id="scheduling" role="tabpanel" aria-labelledby="scheduling-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Penjadwalan Eksekusi Kenaikan Kelas</h5>
                    {{-- Button Removed: Schedule is now managed in Settings --}}
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        Jadwal eksekusi otomatis diatur melalui menu 
                        <a href="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.settings.index' : 'waka.promotion.settings.index') }}" class="fw-bold">Pengaturan Kenaikan Kelas</a>.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-card-mobile">
                            <thead>
                                <tr>
                                    <th>Jadwal Eksekusi</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Statistik</th>
                                    <th>Log</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                <tr>
                                    <td class="mobile-card-head">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-calendar-alt me-1"></i> {{ $schedule->scheduled_at->format('d/m/y H:i') }}</span>
                                            @if($schedule->status == 'PENDING')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($schedule->status == 'RUNNING')
                                                <span class="badge bg-info">Sedang Berjalan</span>
                                            @elseif($schedule->status == 'COMPLETED')
                                                <span class="badge bg-success">Selesai</span>
                                            @elseif($schedule->status == 'FAILED')
                                                <span class="badge bg-danger">Gagal</span>
                                            @else
                                                <span class="badge bg-secondary">Batal</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="desktop-only-cell">
                                        @if($schedule->status == 'PENDING')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($schedule->status == 'RUNNING')
                                            <span class="badge bg-info">Sedang Berjalan</span>
                                        @elseif($schedule->status == 'COMPLETED')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($schedule->status == 'FAILED')
                                            <span class="badge bg-danger">Gagal</span>
                                        @else
                                            <span class="badge bg-secondary">Batal</span>
                                        @endif
                                    </td>
                                    <td data-label="Dibuat Oleh">
                                        <div class="mobile-text-end">
                                            <div>{{ $schedule->creator->name ?? '-' }}</div>
                                            @if($schedule->creator)
                                                <small class="text-muted">
                                                    {{ $schedule->creator->role === 'wakil_kepala_sekolah' ? 'Wakil Kepala Sekolah' : ucfirst($schedule->creator->role) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Statistik">
                                        <div class="mobile-text-end">
                                            @if($schedule->status == 'COMPLETED')
                                                <small>
                                                    Proses: {{ $schedule->students_processed }}<br>
                                                    Naik: {{ $schedule->students_promoted }}<br>
                                                    Lulus: {{ $schedule->students_graduated }}<br>
                                                    Gagal: {{ $schedule->students_failed }}
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Log">
                                        <div class="mobile-text-end">
                                            <small class="text-muted">{{ Str::limit($schedule->execution_log, 50) }}</small>
                                        </div>
                                    </td>
                                    <td class="mobile-card-actions">
                                        @if($schedule->status == 'PENDING')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cancelScheduleModal"
                                                data-url="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.cancel-schedule' : 'waka.promotion.cancel-schedule', $schedule->id) }}"
                                                data-date="{{ $schedule->scheduled_at->format('d/m/y H:i') }}">
                                                Batal
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada jadwal eksekusi otomatis.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Execution Confirmation (Existing) -->
    <div class="modal fade" id="executeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Konfirmasi Kenaikan Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Apakah Anda Yakin?</h4>
                    </div>
                    
                    {{-- Checklist Status --}}
                    <div class="mb-3" style="background: #f8f9fa; border-radius: 8px; padding: 12px;">
                        <strong class="d-block mb-2"><i class="fas fa-clipboard-check me-1"></i> Status Persiapan:</strong>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Tahun Ajaran Aktif: <strong>{{ $activeYear->nama_tahun_ajaran }}</strong></span>
                        </div>
                        @if($promotionReadiness['isReady'])
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Target TA Baru: <strong>{{ $promotionReadiness['nextTA']->nama_tahun_ajaran }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Kelas Tersedia: <strong>{{ $promotionReadiness['kelasBaruCount'] }} kelas</strong></span>
                        </div>
                        @endif
                    </div>
                    
                    <p class="mb-2">Proses ini akan:</p>
                    <ul class="text-start">
                        <li>Mengecek kelayakan <strong>SEMUA</strong> siswa aktif secara otomatis</li>
                        <li>Memindahkan siswa yang memenuhi syarat ke kelas baru di TA {{ $promotionReadiness['nextTA']->nama_tahun_ajaran ?? 'baru' }}</li>
                        <li>Menetapkan status <strong>Lulus</strong> untuk tingkat akhir</li>
                        <li>Siswa yang tidak memenuhi syarat akan tercatat sebagai "Tidak Naik Kelas"</li>
                    </ul>
                    
                    <div class="alert alert-warning py-2 mb-0">
                        <small><i class="fas fa-info-circle me-1"></i> Siswa yang tidak naik kelas bisa dinaikkan secara individual setelah syarat terpenuhi.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route(str_replace('.report', '.execute', Route::currentRouteName())) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-play me-1"></i> Ya, Proses Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cancel Schedule (New) -->
    <div class="modal fade" id="cancelScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pembatalan Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-times fa-3x text-danger mb-3"></i>
                        <h4>Batalkan Eksekusi?</h4>
                    </div>
                    <p>Apakah Anda yakin ingin membatalkan jadwal eksekusi otomatis pada:</p>
                    <h5 class="text-center text-primary" id="scheduleDateText"></h5>
                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle"></i> Tindakan ini juga akan mereset pengaturan tanggal eksekusi di menu Pengaturan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <form id="cancelScheduleForm" action="" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Ya, Batalkan Jadwal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Promote Selected Modal -->
    <div class="modal fade" id="promoteSelectedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="fas fa-arrow-up me-2"></i>Konfirmasi Kenaikan Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-graduate fa-3x text-success mb-3"></i>
                        <h4>Konfirmasi Proses Manual</h4>
                    </div>
                    
                    <p>Anda akan menaikkan <strong id="selectedCount" class="text-success fs-4">0</strong> siswa terpilih.</p>
                    <p id="selectAllWarning" class="text-warning fw-bold d-none mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Mode Pilih Semua Data diaktifkan.</p>
                    
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle me-1"></i> <strong>Konteks Tahun: {{ $tahun->nama_tahun_ajaran }}</strong><br>
                        Sistem akan mencari kelas lanjutan di tahun ajaran berikutnya secara otomatis.</small>
                    </div>

                    <p class="mb-0">Pastikan data siswa benar sebelum melanjutkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmPromoteBtn">
                        <i class="fas fa-check me-1"></i> Ya, Naikkan Siswa
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var triggerBtn;
function updateButtonState() {
    if (!triggerBtn) triggerBtn = document.querySelector('#promoteSelectedTrigger');
    var count = document.querySelectorAll('.simCheck:checked').length;
    var isSelectAll = document.getElementById('selectAllFlag') && document.getElementById('selectAllFlag').value === '1';
    if(triggerBtn) triggerBtn.disabled = (count === 0 && !isSelectAll);
}

function toggleAllCheckboxes(source, className) {
    const checkboxes = document.querySelectorAll('.' + className);
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });

    // Sync both select all checkboxes (mobile and desktop)
    var mobileCb = document.getElementById('selectAllSimMobile');
    var desktopCb = document.getElementById('selectAllSim');
    if (mobileCb && mobileCb !== source) mobileCb.checked = source.checked;
    if (desktopCb && desktopCb !== source) desktopCb.checked = source.checked;

    // Show/hide the "select all across pages" banner
    var banner = document.getElementById('selectAllBanner');
    if (banner) {
        if (source.checked) {
            banner.classList.remove('d-none');
            // Reset to page-only state
            document.getElementById('selectAllPagesLink').classList.remove('d-none');
            document.getElementById('clearSelectAllLink').classList.add('d-none');
            document.getElementById('bannerText').innerHTML = 'Semua <strong>' + checkboxes.length + '</strong> siswa di halaman ini dipilih.';
        } else {
            banner.classList.add('d-none');
        }
    }

    // Reset select_all flag when toggling page checkboxes
    let flagInput = document.getElementById('selectAllFlag');
    if (flagInput) {
        flagInput.value = '0';
    }

    updateButtonState();
}

function enableSelectAllPages() {
    document.getElementById('selectAllFlag').value = '1';
    var totalData = {{ isset($totalActiveGlobal) ? $totalActiveGlobal : 0 }};
    document.getElementById('bannerText').innerHTML = 'Semua <strong>' + totalData + '</strong> siswa di semua halaman dipilih.';
    document.getElementById('selectAllPagesLink').classList.add('d-none');
    document.getElementById('clearSelectAllLink').classList.remove('d-none');
    updateButtonState();
}

function clearSelectAllPages() {
    document.getElementById('selectAllFlag').value = '0';
    var checkboxes = document.querySelectorAll('.simCheck');
    document.getElementById('bannerText').innerHTML = 'Semua <strong>' + checkboxes.length + '</strong> siswa di halaman ini dipilih.';
    document.getElementById('selectAllPagesLink').classList.remove('d-none');
    document.getElementById('clearSelectAllLink').classList.add('d-none');
    updateButtonState();
}

// Update Modal Count
document.addEventListener('DOMContentLoaded', function() {
    // Uncheck selectAllFlag if user manually unchecks a single item
    document.querySelectorAll('.simCheck').forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked) {
                let flagInput = document.getElementById('selectAllFlag');
                if (flagInput) flagInput.value = '0';

                // also uncheck header checkboxes
                var mobileCb = document.getElementById('selectAllSimMobile');
                var desktopCb = document.getElementById('selectAllSim');
                if (mobileCb) mobileCb.checked = false;
                if (desktopCb) desktopCb.checked = false;

                // hide banner
                var banner = document.getElementById('selectAllBanner');
                if (banner) banner.classList.add('d-none');
            }
            updateButtonState();
        });
    });

    var promoteBtn = document.querySelector('[data-bs-target="#promoteSelectedModal"]');
    if (promoteBtn) {
        promoteBtn.addEventListener('click', function() {
            var isSelectAll = document.getElementById('selectAllFlag') && document.getElementById('selectAllFlag').value === '1';
            var totalData = {{ isset($totalActiveGlobal) ? $totalActiveGlobal : 0 }};
            var uniqueIds = new Set();
            document.querySelectorAll('.simCheck:checked').forEach(function(cb) { uniqueIds.add(cb.value); });
            
            if (isSelectAll) {
                document.getElementById('selectedCount').textContent = totalData;
                var warningEl = document.getElementById('selectAllWarning');
                if (warningEl) {
                    warningEl.classList.remove('d-none');
                    warningEl.classList.add('d-block');
                }
            } else {
                document.getElementById('selectedCount').textContent = uniqueIds.size;
                var warningEl = document.getElementById('selectAllWarning');
                if (warningEl) {
                    warningEl.classList.remove('d-block');
                    warningEl.classList.add('d-none');
                }
            }

            if (uniqueIds.size === 0 && !isSelectAll) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Pilih siswa terlebih dahulu!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
    }

    // Real-time disable/enable button
    var checkboxes = document.querySelectorAll('.simCheck');

    checkboxes.forEach(cb => cb.addEventListener('change', updateButtonState));
    // Initial State
    updateButtonState();

    // Before form submit: copy mobile checkbox values as siswa_ids[]
    var promoteForm = document.getElementById('promoteSelectedForm');
    if (promoteForm) {
        /* Remove default submit intercept as we only submit via modal button */
        // Also intercept the modal button that triggers submit
        var modalSubmitBtn = document.getElementById('confirmPromoteBtn');
        if (modalSubmitBtn) {
            modalSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove old dynamic inputs
                promoteForm.querySelectorAll('.dynamic-siswa-id').forEach(el => el.remove());
                
                // If not select all, gather checkboxes
                if (document.getElementById('selectAllFlag').value === '0') {
                    // Collect unique checked values
                    var ids = new Set();
                    document.querySelectorAll('.simCheck:checked').forEach(function(cb) {
                        ids.add(cb.value);
                    });
                    ids.forEach(function(id) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'siswa_ids[]';
                        input.value = id;
                        input.className = 'dynamic-siswa-id';
                        promoteForm.appendChild(input);
                    });
                }
                promoteForm.submit();
            });
        }
    }

    // Existing Cancel Modal Script
    var cancelModal = document.getElementById('cancelScheduleModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var url = button.getAttribute('data-url');
            var date = button.getAttribute('data-date');
            
            var form = cancelModal.querySelector('#cancelScheduleForm');
            var dateText = cancelModal.querySelector('#scheduleDateText');
            
            form.action = url;
            dateText.textContent = date;
        });
    }
});
</script>
@endsection
