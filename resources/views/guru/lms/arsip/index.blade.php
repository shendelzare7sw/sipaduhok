@extends('layouts.sneat')

@section('title', 'Arsip LMS')
@section('page-title', 'Arsip LMS')
@section('page-subtitle', 'Materi, tugas, latihan, dan ujian Anda lintas tahun ajaran')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .arsip-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .arsip-stat {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .arsip-stat .icon-circle {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .arsip-stat .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .arsip-stat .value { font-size: 1.5rem; font-weight: 800; line-height: 1.2; color: #1e293b; }

    .filter-bar {
        background: white;
        padding: 14px 18px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
    }
    .filter-bar .form-group { flex: 1; min-width: 160px; }
    .filter-bar label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px; display: block; }

    .type-tabs {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .type-tab {
        padding: 8px 16px;
        border-radius: 999px;
        background: white;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: all .15s ease;
    }
    .type-tab:hover { color: #1e293b; border-color: #cbd5e1; }
    .type-tab.active {
        background: #4361ee;
        color: white;
        border-color: #4361ee;
    }

    .arsip-section { margin-bottom: 28px; }
    .arsip-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .arsip-section-title .badge-count {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .arsip-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }
    .arsip-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        transition: transform .15s ease, box-shadow .15s ease;
        position: relative;
    }
    .arsip-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px -2px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }
    .arsip-card .card-header-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
    }
    .arsip-card .icon-square {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .arsip-card .judul {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.35;
        flex: 1;
        word-break: break-word;
    }
    .arsip-card .meta {
        font-size: 11px;
        color: #64748b;
        line-height: 1.6;
    }
    .arsip-card .meta i { color: #94a3b8; margin-right: 4px; }
    .arsip-card .meta-row { margin-bottom: 4px; }

    .badge-ta {
        background: rgba(67, 97, 238, 0.08);
        color: #4361ee;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .arsip-actions {
        display: flex;
        gap: 6px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #e5e7eb;
    }
    .btn-arsip {
        flex: 1;
        padding: 7px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #475569;
        text-decoration: none;
        text-align: center;
        transition: all .15s ease;
    }
    .btn-arsip:hover { border-color: #4361ee; color: #4361ee; }
    .btn-arsip-salin {
        background: #4361ee;
        color: white;
        border-color: #4361ee;
    }
    .btn-arsip-salin:hover { background: #3651d4; color: white; }

    .empty-state-arsip {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    .empty-state-arsip i { font-size: 3rem; color: #cbd5e1; margin-bottom: 12px; display: block; }

    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .form-group { width: 100%; }
        .arsip-grid { grid-template-columns: 1fr; }
        .type-tabs { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px; }
        .type-tab { white-space: nowrap; }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-archive me-2 text-primary"></i>Arsip LMS</h4>
            <p class="text-muted mb-0">Lihat semua materi, tugas, latihan, dan ujian yang pernah Anda buat lintas tahun ajaran. Salin ke kelas aktif untuk dipakai ulang.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($kelasMapelTujuan->isEmpty())
        <div class="alert alert-warning d-flex align-items-start">
            <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
            <div>
                <strong>Anda belum ditugaskan mengajar di TA aktif.</strong> Anda masih bisa melihat arsip, tapi tombol "Salin ke Kelas Aktif" akan dinonaktifkan sampai admin assign Anda ke kelas+mapel di TA yang sedang berjalan.
            </div>
        </div>
    @endif

    {{-- Summary --}}
    <div class="arsip-summary">
        <div class="arsip-stat">
            <div class="icon-circle" style="background: #0284c7;"><i class="fas fa-book-open"></i></div>
            <div><div class="label">Materi</div><div class="value">{{ $arsip['materi']->count() }}</div></div>
        </div>
        <div class="arsip-stat">
            <div class="icon-circle" style="background: #d97706;"><i class="fas fa-tasks"></i></div>
            <div><div class="label">Tugas</div><div class="value">{{ $arsip['tugas']->count() }}</div></div>
        </div>
        <div class="arsip-stat">
            <div class="icon-circle" style="background: #7c3aed;"><i class="fas fa-pencil-ruler"></i></div>
            <div><div class="label">Latihan</div><div class="value">{{ $arsip['latihan']->count() }}</div></div>
        </div>
        <div class="arsip-stat">
            <div class="icon-circle" style="background: #dc2626;"><i class="fas fa-file-alt"></i></div>
            <div><div class="label">Ujian</div><div class="value">{{ $arsip['ujian']->count() }}</div></div>
        </div>
    </div>

    {{-- Type tabs --}}
    <div class="type-tabs">
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->except('type'), [])) }}"
           class="type-tab {{ empty($filters['type']) ? 'active' : '' }}">Semua</a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'materi'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'materi' ? 'active' : '' }}">
            <i class="fas fa-book-open me-1"></i>Materi
        </a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'tugas'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'tugas' ? 'active' : '' }}">
            <i class="fas fa-tasks me-1"></i>Tugas
        </a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'latihan'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'latihan' ? 'active' : '' }}">
            <i class="fas fa-pencil-ruler me-1"></i>Latihan
        </a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'ujian'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'ujian' ? 'active' : '' }}">
            <i class="fas fa-file-alt me-1"></i>Ujian
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('guru.lms.arsip.index') }}" class="filter-bar">
        @if($filters['type'])
            <input type="hidden" name="type" value="{{ $filters['type'] }}">
        @endif

        <div class="form-group">
            <label>Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-select form-select-sm">
                <option value="">Semua TA</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected(($filters['tahun_ajaran_id'] ?? null) == $ta->id)>
                        {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Mata Pelajaran</label>
            <select name="mapel_id" class="form-select form-select-sm">
                <option value="">Semua Mapel</option>
                @foreach($mataPelajarans as $mp)
                    <option value="{{ $mp->id }}" @selected(($filters['mapel_id'] ?? null) == $mp->id)>
                        {{ $mp->nama_mapel }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Cari Judul</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                class="form-control form-control-sm" placeholder="kata kunci...">
        </div>

        <div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <a href="{{ route('guru.lms.arsip.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>

    @if($totalKonten === 0)
        <div class="empty-state-arsip">
            <i class="fas fa-archive"></i>
            <h5 class="fw-bold mb-1">Belum Ada Konten</h5>
            <p class="mb-0">Anda belum pernah membuat materi/tugas/ujian, atau filter terlalu sempit.</p>
        </div>
    @else
        @foreach(['materi' => ['#0284c7', 'fa-book-open', 'Materi'],
                  'tugas' => ['#d97706', 'fa-tasks', 'Tugas'],
                  'latihan' => ['#7c3aed', 'fa-pencil-ruler', 'Latihan'],
                  'ujian' => ['#dc2626', 'fa-file-alt', 'Ujian']] as $sectionKey => $cfg)
            @if($arsip[$sectionKey]->isNotEmpty())
                <div class="arsip-section">
                    <div class="arsip-section-title">
                        <span style="color: {{ $cfg[0] }};"><i class="fas {{ $cfg[1] }}"></i></span>
                        {{ $cfg[2] }}
                        <span class="badge-count">{{ $arsip[$sectionKey]->count() }}</span>
                    </div>
                    <div class="arsip-grid">
                        @foreach($arsip[$sectionKey] as $item)
                            @php
                                $judul = match($sectionKey) {
                                    'materi' => $item->judul_materi,
                                    'tugas' => $item->judul_tugas,
                                    default => $item->judul_ujian,
                                };
                                $tanggal = match($sectionKey) {
                                    'materi' => $item->tanggal_upload,
                                    default => $item->tanggal_mulai,
                                };
                                $isAktif = $item->kelas?->tahunAjaran?->is_active;
                            @endphp
                            <div class="arsip-card">
                                <span class="badge-ta">
                                    {{ $item->kelas?->tahunAjaran?->nama_tahun_ajaran ?? 'TA -' }}
                                    @if($isAktif) · Aktif @endif
                                </span>
                                <div class="card-header-row">
                                    <div class="icon-square" style="background: {{ $cfg[0] }};">
                                        <i class="fas {{ $cfg[1] }}"></i>
                                    </div>
                                    <div class="judul">{{ $judul }}</div>
                                </div>
                                <div class="meta">
                                    <div class="meta-row"><i class="fas fa-school"></i>{{ $item->kelas?->nama_kelas ?? '-' }}</div>
                                    <div class="meta-row"><i class="fas fa-book"></i>{{ $item->mataPelajaran?->nama_mapel ?? '-' }}</div>
                                    @if($tanggal)
                                        <div class="meta-row"><i class="fas fa-calendar"></i>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y') }}</div>
                                    @endif
                                    @if($sectionKey === 'ujian' || $sectionKey === 'latihan')
                                        @php $tipeLabel = method_exists($item, 'getTipeLabelAttribute') ? $item->tipe_label : $item->tipe_ujian; @endphp
                                        <div class="meta-row"><i class="fas fa-tag"></i>{{ $tipeLabel }}</div>
                                    @endif
                                </div>
                                <div class="arsip-actions">
                                    <a href="{{ route('guru.lms.arsip.preview', [$sectionKey, $item->id]) }}"
                                       class="btn-arsip" target="_blank">
                                        <i class="fas fa-eye me-1"></i>Preview
                                    </a>
                                    @if($kelasMapelTujuan->isNotEmpty())
                                        <a href="{{ route('guru.lms.arsip.form-salin', [$sectionKey, $item->id]) }}"
                                           class="btn-arsip btn-arsip-salin">
                                            <i class="fas fa-copy me-1"></i>Salin
                                        </a>
                                    @else
                                        <button type="button" class="btn-arsip" disabled
                                                title="Anda belum ditugaskan ke kelas TA aktif">
                                            <i class="fas fa-ban me-1"></i>Salin
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
