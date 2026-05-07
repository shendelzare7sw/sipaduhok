@extends('layouts.sneat')

@section('title', 'Riwayat LMS Saya')
@section('page-title', 'Riwayat LMS')
@section('page-subtitle', 'Tugas, latihan, dan ujian yang pernah saya kerjakan')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .riwayat-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .riwayat-stat {
        background: white; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 16px;
        display: flex; align-items: center; gap: 12px;
    }
    .riwayat-stat .icon-circle {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1rem; flex-shrink: 0;
    }
    .riwayat-stat .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .riwayat-stat .value { font-size: 1.5rem; font-weight: 800; color: #1e293b; }

    .filter-bar {
        background: white; padding: 14px 18px; border-radius: 10px;
        border: 1px solid #e5e7eb; margin-bottom: 18px;
        display: flex; gap: 12px; flex-wrap: wrap; align-items: end;
    }
    .filter-bar .form-group { flex: 1; min-width: 160px; }
    .filter-bar label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px; display: block; }

    .type-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 18px; }
    .type-tab {
        padding: 8px 16px; border-radius: 999px;
        background: white; border: 1px solid #e5e7eb;
        font-size: 13px; font-weight: 600; color: #475569;
        text-decoration: none;
    }
    .type-tab.active { background: #4361ee; color: white; border-color: #4361ee; }

    .riwayat-section { margin-bottom: 28px; }
    .riwayat-section-title {
        font-size: 1rem; font-weight: 700; color: #1e293b;
        margin-bottom: 12px; display: flex; align-items: center; gap: 10px;
    }
    .riwayat-section-title .badge-count {
        background: #f1f5f9; color: #475569;
        padding: 2px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
    }

    .riwayat-list { display: grid; gap: 10px; }
    .riwayat-item {
        background: white; border: 1px solid #e5e7eb;
        border-radius: 10px; padding: 14px 16px;
        display: flex; gap: 14px; align-items: center;
        transition: all .15s ease;
    }
    .riwayat-item:hover { border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,.04); }
    .riwayat-item .icon-square {
        width: 40px; height: 40px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: white; flex-shrink: 0;
    }
    .riwayat-item .info { flex: 1; min-width: 0; }
    .riwayat-item .judul { font-weight: 700; color: #1e293b; font-size: 14px; }
    .riwayat-item .meta { font-size: 11px; color: #64748b; margin-top: 4px; }
    .riwayat-item .meta i { margin-right: 4px; color: #94a3b8; }
    .riwayat-item .meta-row { display: inline-block; margin-right: 12px; }

    .nilai-cell {
        text-align: right; min-width: 120px;
    }
    .nilai-cell .nilai-value { font-size: 1.2rem; font-weight: 800; color: #16a34a; }
    .nilai-cell .nilai-label { font-size: 10px; color: #64748b; text-transform: uppercase; }
    .badge-status {
        padding: 3px 10px; border-radius: 999px;
        font-size: 10px; font-weight: 700;
    }
    .badge-status.dinilai { background: rgba(22,163,74,.1); color: #15803d; }
    .badge-status.dikerjakan { background: rgba(217,119,6,.1); color: #92400e; }
    .badge-status.belum { background: rgba(100,116,139,.1); color: #475569; }
    .badge-ta {
        background: rgba(67,97,238,.08); color: #4361ee;
        padding: 2px 8px; border-radius: 999px;
        font-size: 10px; font-weight: 700;
    }

    .empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
    .empty-state i { font-size: 3rem; color: #cbd5e1; display: block; margin-bottom: 12px; }

    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .riwayat-item { flex-direction: column; align-items: stretch; }
        .nilai-cell { text-align: left; }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-history me-2 text-primary"></i>Riwayat LMS Saya</h4>
            <p class="text-muted mb-0">Tugas, latihan, dan ujian yang pernah Anda kerjakan dari semua tahun ajaran. Klik salah satu untuk melihat detail dan nilai.</p>
        </div>
    </div>

    {{-- Summary --}}
    <div class="riwayat-summary">
        <div class="riwayat-stat">
            <div class="icon-circle" style="background: #d97706;"><i class="fas fa-tasks"></i></div>
            <div><div class="label">Tugas</div><div class="value">{{ $tugas->count() }}</div></div>
        </div>
        <div class="riwayat-stat">
            <div class="icon-circle" style="background: #7c3aed;"><i class="fas fa-pencil-ruler"></i></div>
            <div><div class="label">Latihan</div><div class="value">{{ $latihan->count() }}</div></div>
        </div>
        <div class="riwayat-stat">
            <div class="icon-circle" style="background: #dc2626;"><i class="fas fa-file-alt"></i></div>
            <div><div class="label">Ujian</div><div class="value">{{ $ujian->count() }}</div></div>
        </div>
    </div>

    <div class="type-tabs">
        <a href="{{ route('siswa.lms.riwayat.index', array_diff_key(request()->all(), ['type' => null])) }}"
           class="type-tab {{ empty($filters['type']) ? 'active' : '' }}">Semua</a>
        <a href="{{ route('siswa.lms.riwayat.index', array_merge(request()->all(), ['type' => 'tugas'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'tugas' ? 'active' : '' }}">
            <i class="fas fa-tasks me-1"></i>Tugas
        </a>
        <a href="{{ route('siswa.lms.riwayat.index', array_merge(request()->all(), ['type' => 'latihan'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'latihan' ? 'active' : '' }}">
            <i class="fas fa-pencil-ruler me-1"></i>Latihan
        </a>
        <a href="{{ route('siswa.lms.riwayat.index', array_merge(request()->all(), ['type' => 'ujian'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'ujian' ? 'active' : '' }}">
            <i class="fas fa-file-alt me-1"></i>Ujian
        </a>
    </div>

    <form method="GET" action="{{ route('siswa.lms.riwayat.index') }}" class="filter-bar">
        @if($filters['type'])
            <input type="hidden" name="type" value="{{ $filters['type'] }}">
        @endif
        <div class="form-group">
            <label>Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua TA</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected(($filters['tahun_ajaran_id'] ?? null) == $ta->id)>
                        {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        @if($filters['tahun_ajaran_id'])
            <div>
                <a href="{{ route('siswa.lms.riwayat.index', $filters['type'] ? ['type' => $filters['type']] : []) }}"
                   class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        @endif
    </form>

    @if($totalRiwayat === 0)
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h5 class="fw-bold mb-1">Belum Ada Riwayat</h5>
            <p class="mb-0">Anda belum pernah mengerjakan tugas/ujian, atau filter terlalu sempit.</p>
        </div>
    @else
        @foreach([
            ['key' => 'tugas', 'list' => $tugas, 'color' => '#d97706', 'icon' => 'fa-tasks', 'label' => 'Tugas'],
            ['key' => 'latihan', 'list' => $latihan, 'color' => '#7c3aed', 'icon' => 'fa-pencil-ruler', 'label' => 'Latihan'],
            ['key' => 'ujian', 'list' => $ujian, 'color' => '#dc2626', 'icon' => 'fa-file-alt', 'label' => 'Ujian'],
        ] as $section)
            @if($section['list']->isNotEmpty())
                <div class="riwayat-section">
                    <div class="riwayat-section-title">
                        <span style="color: {{ $section['color'] }};"><i class="fas {{ $section['icon'] }}"></i></span>
                        {{ $section['label'] }}
                        <span class="badge-count">{{ $section['list']->count() }}</span>
                    </div>
                    <div class="riwayat-list">
                        @foreach($section['list'] as $item)
                            @if($section['key'] === 'tugas')
                                @php
                                    $detailRoute = route('siswa.lms.riwayat.tugas', $item->id);
                                    $judul = $item->tugas?->judul_tugas ?? '-';
                                    $kelas = $item->tugas?->kelas?->nama_kelas;
                                    $mapel = $item->tugas?->mataPelajaran?->nama_mapel;
                                    $taNama = $item->tugas?->kelas?->tahunAjaran?->nama_tahun_ajaran;
                                    $tanggal = $item->tanggal_submit;
                                    $nilai = $item->nilai;
                                    $status = $item->status ?? 'belum_dikerjakan';
                                @endphp
                            @else
                                @php
                                    $detailRoute = route('siswa.lms.riwayat.ujian', $item->id);
                                    $judul = $item->ujian?->judul_ujian ?? '-';
                                    $kelas = $item->ujian?->kelas?->nama_kelas;
                                    $mapel = $item->ujian?->mataPelajaran?->nama_mapel;
                                    $taNama = $item->ujian?->kelas?->tahunAjaran?->nama_tahun_ajaran;
                                    $tanggal = $item->waktu_selesai ?? $item->waktu_mulai;
                                    $nilai = $item->nilai;
                                    $status = $item->status ?? 'belum_dikerjakan';
                                @endphp
                            @endif

                            <a href="{{ $detailRoute }}" class="riwayat-item text-decoration-none">
                                <div class="icon-square" style="background: {{ $section['color'] }};">
                                    <i class="fas {{ $section['icon'] }}"></i>
                                </div>
                                <div class="info">
                                    <div class="judul">{{ $judul }}</div>
                                    <div class="meta">
                                        @if($taNama)
                                            <span class="badge-ta">TA {{ $taNama }}</span>
                                        @endif
                                        @if($mapel)
                                            <span class="meta-row"><i class="fas fa-book"></i>{{ $mapel }}</span>
                                        @endif
                                        @if($kelas)
                                            <span class="meta-row"><i class="fas fa-school"></i>{{ $kelas }}</span>
                                        @endif
                                        @if($tanggal)
                                            <span class="meta-row"><i class="fas fa-calendar"></i>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="nilai-cell">
                                    @if($nilai !== null)
                                        <div class="nilai-value">{{ number_format((float) $nilai, 1) }}</div>
                                        <div class="nilai-label">Nilai</div>
                                    @else
                                        <span class="badge-status {{ $status === 'dinilai' ? 'dinilai' : ($status === 'belum_dikerjakan' ? 'belum' : 'dikerjakan') }}">
                                            {{ str_replace('_', ' ', $status) }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
