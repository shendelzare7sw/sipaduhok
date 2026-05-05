@extends('layouts.sneat')

@section('title', 'Monitoring LMS - ' . $kelas->nama_kelas)
@section('page-title', 'Monitoring LMS')
@section('page-subtitle', 'Detail konten kelas ' . $kelas->nama_kelas . ($kelas->tahunAjaran ? ' · ' . ($kelas->tahunAjaran->nama_tahun_ajaran ?? $kelas->tahunAjaran->tahun_ajaran) : ''))

@section('sidebar-menu')
    @include($rolePartial)
@endsection

@section('styles')
<style>
    .lms-monitoring-wrapper { padding: 0; }

    /* Breadcrumb */
    .lms-breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        font-size: 0.82rem;
        flex-wrap: wrap;
    }
    .lms-breadcrumb a { color: var(--primary-color); text-decoration: none; font-weight: 600; }
    .lms-breadcrumb a:hover { text-decoration: underline; }
    .lms-breadcrumb .separator { font-size: 0.65rem; color: var(--text-muted); }
    .lms-breadcrumb span { color: var(--text-main); font-weight: 600; }

    /* Class header */
    .kelas-header-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .kelas-header-info {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .kelas-jenjang-badge-lg {
        background: var(--primary-color);
        color: white;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }

    .kelas-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0; }

    .kelas-meta {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        font-size: 0.78rem;
        color: var(--text-muted);
    }

    .kelas-meta i { color: var(--primary-color); margin-right: 4px; }

    .kelas-ta-pill {
        background: rgba(67, 97, 238, 0.08);
        color: var(--primary-color);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Filter card */
    .filters-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: 1.5fr 1.2fr 1fr 1fr auto;
        gap: 10px;
        align-items: end;
    }

    .filter-field { display: flex; flex-direction: column; gap: 4px; }

    .filter-field label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin: 0;
    }

    .filter-field input,
    .filter-field select {
        height: 38px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 0 12px;
        font-size: 0.85rem;
    }

    .filter-actions { display: flex; gap: 6px; }

    .filter-actions .btn {
        height: 38px;
        border-radius: 8px;
        font-size: 0.82rem;
        padding: 0 14px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Stats row */
    .konten-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .konten-stat {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
    }

    .konten-stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .konten-stat-num { font-size: 1.05rem; font-weight: 700; color: var(--text-main); line-height: 1; }
    .konten-stat-label { font-size: 0.7rem; color: var(--text-muted); margin-top: 2px; }

    .icon-materi { background: rgba(2, 132, 199, 0.08); color: #0284c7; }
    .icon-tugas { background: rgba(217, 119, 6, 0.08); color: #d97706; }
    .icon-latihan { background: rgba(124, 58, 237, 0.08); color: #7c3aed; }
    .icon-ujian { background: rgba(220, 38, 38, 0.08); color: #dc2626; }

    /* Ensure tab-content aligns flush with stat cards */
    .lms-monitoring-wrapper > .tab-content {
        padding: 0 !important;
        margin: 0 !important;
    }
    .lms-monitoring-wrapper > .tab-content > .tab-pane {
        padding: 0 !important;
    }

    /* Tabs */
    .konten-tabs {
        border: none;
        gap: 4px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .konten-tabs .nav-item { flex-shrink: 0; }

    .konten-tabs .nav-link {
        border: 1px solid var(--border-color) !important;
        border-radius: 8px !important;
        padding: 8px 14px !important;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted) !important;
        background: var(--surface-color) !important;
        display: inline-flex !important;
        align-items: center;
        gap: 6px;
        outline: none !important;
        box-shadow: none !important;
        overflow: visible;
        white-space: nowrap;
    }

    .konten-tabs .nav-link:hover {
        color: var(--primary-color) !important;
        background: var(--surface-color) !important;
    }

    .konten-tabs .nav-link:focus,
    .konten-tabs .nav-link:active {
        outline: none !important;
        box-shadow: none !important;
    }

    .konten-tabs .nav-link.active,
    .konten-tabs .nav-link.active:hover,
    .konten-tabs .nav-link.active:focus {
        background: var(--primary-color) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
    }

    .tab-count {
        background: rgba(255,255,255,0.2);
        padding: 1px 8px;
        border-radius: 999px;
        font-size: 0.7rem;
    }

    .konten-tabs .nav-link:not(.active) .tab-count {
        background: var(--background-color);
        color: var(--text-muted);
    }

    /* Konten list (grouped by mapel) */
    .mapel-group {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .mapel-group-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .mapel-group-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(67, 97, 238, 0.08);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .mapel-group-title { font-size: 0.92rem; font-weight: 700; color: var(--text-main); margin: 0; }
    .mapel-group-count { font-size: 0.7rem; color: var(--text-muted); margin: 0; }

    .konten-list { display: grid; gap: 10px; }

    .konten-item {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 12px;
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        transition: background 0.2s ease, border-color 0.2s ease;
    }

    .konten-item:hover {
        background: rgba(67, 97, 238, 0.03);
        border-color: var(--primary-color);
    }

    .konten-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .konten-info { min-width: 0; }

    .konten-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }

    .konten-meta {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    .konten-meta i { color: var(--text-muted); }

    .konten-badge {
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.66rem;
        font-weight: 600;
    }

    .badge-tipe-materi { background: rgba(2, 132, 199, 0.08); color: #0284c7; }
    .badge-tipe-tugas { background: rgba(217, 119, 6, 0.08); color: #92400e; }
    .badge-tipe-latihan { background: rgba(124, 58, 237, 0.1); color: #5b21b6; }
    .badge-tipe-ujian { background: rgba(220, 38, 38, 0.1); color: #b91c1c; }

    .konten-actions { display: flex; gap: 6px; flex-shrink: 0; flex-wrap: wrap; }

    .btn-konten {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 13px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s ease;
        outline: none;
        box-shadow: none;
    }

    .btn-konten:focus,
    .btn-konten:active,
    .btn-konten:focus-visible {
        outline: none;
        box-shadow: none;
    }

    .btn-konten-preview {
        background: var(--surface-color);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .btn-konten-preview:hover,
    .btn-konten-preview:focus,
    .btn-konten-preview:active {
        background: rgba(67, 97, 238, 0.06);
        color: var(--primary-color);
        text-decoration: none;
    }

    .btn-konten-catatan {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    .btn-konten-catatan:hover,
    .btn-konten-catatan:focus,
    .btn-konten-catatan:active {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    /* Empty states */
    .empty-state {
        background: var(--surface-color);
        border: 1px dashed var(--border-color);
        border-radius: 12px;
        padding: 50px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--border-color);
        margin-bottom: 12px;
        display: block;
    }

    /* Mobile */
    @media (max-width: 992px) {
        .filters-grid {
            grid-template-columns: 1fr 1fr;
        }
        .filters-grid .filter-field:first-child { grid-column: 1 / -1; }
        .filter-actions { grid-column: 1 / -1; }
    }

    @media (max-width: 768px) {
        .konten-stats-row { grid-template-columns: repeat(2, 1fr); }
        .kelas-header-card { padding: 16px; }
        .kelas-title { font-size: 1.05rem; }
        .kelas-meta { font-size: 0.72rem; gap: 10px; }

        .mapel-group { padding: 14px; }
        .konten-tabs { justify-content: center; }
        .konten-tabs .nav-link { padding: 6px 10px; font-size: 0.78rem; }
    }

    @media (max-width: 640px) {
        .konten-item {
            grid-template-columns: 1fr;
            padding: 12px;
            gap: 10px;
            text-align: center;
        }
        .konten-icon { display: none; }
        .konten-info { text-align: left; }
        .konten-meta { justify-content: flex-start; }
        .konten-actions { width: 100%; justify-content: center; }
        .btn-konten { flex: 1; justify-content: center; }
        .filters-grid { grid-template-columns: 1fr; }
        .konten-tabs { justify-content: center; }
    }
</style>
@endsection

@section('content')
<div class="lms-monitoring-wrapper">
    {{-- Breadcrumb --}}
    <nav class="lms-breadcrumb">
        <a href="{{ route($baseRoute . '.index') }}"><i class="fas fa-arrow-left me-1"></i>Daftar Kelas</a>
        <i class="fas fa-chevron-right separator"></i>
        <span>{{ $kelas->nama_kelas }}</span>
    </nav>

    {{-- Class header --}}
    <div class="kelas-header-card">
        <div class="kelas-header-info">
            <div class="kelas-jenjang-badge-lg">{{ $kelas->jenjang }}</div>
            <div style="flex: 1; min-width: 0;">
                <h2 class="kelas-title">{{ $kelas->nama_kelas }}</h2>
                <div class="kelas-meta">
                    @if($kelas->cabang)
                        <span><i class="fas fa-map-marker-alt"></i>{{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                    @endif
                    @if($kelas->waliKelas)
                        <span><i class="fas fa-user-tie"></i>Wali: {{ $kelas->waliKelas->nama_lengkap }}</span>
                    @endif
                    @if($kelas->tahunAjaran)
                        <span class="kelas-ta-pill">
                            <i class="fas fa-calendar-alt me-1"></i>{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? $kelas->tahunAjaran->tahun_ajaran ?? '-' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="filters-card">
        <input type="hidden" name="tab" value="{{ $filters['tab'] }}">
        <div class="filters-grid">
            <div class="filter-field">
                <label for="filter-search">Cari Judul Konten</label>
                <input type="text" id="filter-search" name="search" value="{{ $filters['search'] }}"
                    placeholder="Ketik kata kunci..." class="form-control">
            </div>
            <div class="filter-field">
                <label for="filter-mapel">Mata Pelajaran</label>
                <select name="mapel_id" id="filter-mapel" class="form-select">
                    <option value="">Semua Mapel</option>
                    @foreach($konten['mapelOptions'] as $mp)
                        <option value="{{ $mp->id }}" {{ $filters['mapel_id'] === $mp->id ? 'selected' : '' }}>
                            {{ $mp->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field">
                <label for="filter-from">Dari Tanggal</label>
                <input type="date" id="filter-from" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
            </div>
            <div class="filter-field">
                <label for="filter-to">Sampai Tanggal</label>
                <input type="date" id="filter-to" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route($baseRoute . '.kelas', $kelas->id) }}" class="btn btn-light"
                    title="Reset semua filter">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>

    {{-- Quick stats --}}
    <div class="konten-stats-row">
        <div class="konten-stat">
            <div class="konten-stat-icon icon-materi"><i class="fas fa-book"></i></div>
            <div>
                <div class="konten-stat-num">{{ $konten['materi']->count() }}</div>
                <div class="konten-stat-label">Materi</div>
            </div>
        </div>
        <div class="konten-stat">
            <div class="konten-stat-icon icon-tugas"><i class="fas fa-tasks"></i></div>
            <div>
                <div class="konten-stat-num">{{ $konten['tugas']->count() }}</div>
                <div class="konten-stat-label">Tugas</div>
            </div>
        </div>
        <div class="konten-stat">
            <div class="konten-stat-icon icon-latihan"><i class="fas fa-pencil-ruler"></i></div>
            <div>
                <div class="konten-stat-num">{{ $konten['latihan']->count() }}</div>
                <div class="konten-stat-label">Latihan</div>
            </div>
        </div>
        <div class="konten-stat">
            <div class="konten-stat-icon icon-ujian"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="konten-stat-num">{{ $konten['ujian']->count() }}</div>
                <div class="konten-stat-label">Ujian</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    @php
        $activeTab = in_array($filters['tab'] ?? 'materi', ['materi', 'tugas', 'latihan', 'ujian']) ? $filters['tab'] : 'materi';
    @endphp
    <ul class="nav nav-tabs konten-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'materi' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-materi" type="button" data-tab-key="materi">
                <i class="fas fa-book"></i>Materi <span class="tab-count">{{ $konten['materi']->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'tugas' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-tugas" type="button" data-tab-key="tugas">
                <i class="fas fa-tasks"></i>Tugas <span class="tab-count">{{ $konten['tugas']->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'latihan' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-latihan" type="button" data-tab-key="latihan">
                <i class="fas fa-pencil-ruler"></i>Latihan <span class="tab-count">{{ $konten['latihan']->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'ujian' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-ujian" type="button" data-tab-key="ujian">
                <i class="fas fa-file-alt"></i>Ujian <span class="tab-count">{{ $konten['ujian']->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade {{ $activeTab === 'materi' ? 'show active' : '' }}" id="tab-materi">
            @include('monitoring-lms.partials.konten-grouped', [
                'items' => $konten['materi'],
                'type' => 'materi',
                'titleField' => 'judul_materi',
                'dateField' => 'tanggal_upload',
                'dateLabel' => 'Diupload',
                'badgeClass' => 'badge-tipe-materi',
                'iconClass' => 'fa-book',
                'iconBg' => 'icon-materi',
                'badgeText' => fn($i) => 'Materi',
                'emptyText' => 'Belum ada materi.',
            ])
        </div>
        <div class="tab-pane fade {{ $activeTab === 'tugas' ? 'show active' : '' }}" id="tab-tugas">
            @include('monitoring-lms.partials.konten-grouped', [
                'items' => $konten['tugas'],
                'type' => 'tugas',
                'titleField' => 'judul_tugas',
                'dateField' => 'tanggal_deadline',
                'dateLabel' => 'Deadline',
                'badgeClass' => 'badge-tipe-tugas',
                'iconClass' => 'fa-tasks',
                'iconBg' => 'icon-tugas',
                'badgeText' => fn($i) => 'Tugas',
                'emptyText' => 'Belum ada tugas.',
            ])
        </div>
        <div class="tab-pane fade {{ $activeTab === 'latihan' ? 'show active' : '' }}" id="tab-latihan">
            @include('monitoring-lms.partials.konten-grouped', [
                'items' => $konten['latihan'],
                'type' => 'latihan',
                'titleField' => 'judul_ujian',
                'dateField' => 'tanggal_mulai',
                'dateLabel' => 'Mulai',
                'badgeClass' => 'badge-tipe-latihan',
                'iconClass' => 'fa-pencil-ruler',
                'iconBg' => 'icon-latihan',
                'badgeText' => fn($i) => 'Latihan',
                'emptyText' => 'Belum ada latihan.',
            ])
        </div>
        <div class="tab-pane fade {{ $activeTab === 'ujian' ? 'show active' : '' }}" id="tab-ujian">
            @include('monitoring-lms.partials.konten-grouped', [
                'items' => $konten['ujian'],
                'type' => 'ujian',
                'titleField' => 'judul_ujian',
                'dateField' => 'tanggal_mulai',
                'dateLabel' => 'Mulai',
                'badgeClass' => 'badge-tipe-ujian',
                'iconClass' => 'fa-file-alt',
                'iconBg' => 'icon-ujian',
                'badgeText' => fn($i) => method_exists($i, 'getTipeLabelAttribute') ? $i->tipe_label : 'Ujian',
                'emptyText' => 'Belum ada ujian.',
            ])
        </div>
    </div>
</div>

@include('monitoring-lms.partials.modal-catatan')

@push('scripts')
<script>
(function() {
    // Persist active tab in URL when user switches tabs
    document.querySelectorAll('[data-tab-key]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', e.target.dataset.tabKey);
            window.history.replaceState({}, '', url);
            const hidden = document.querySelector('input[name="tab"]');
            if (hidden) hidden.value = e.target.dataset.tabKey;
        });
    });
})();
</script>
@endpush
@endsection
