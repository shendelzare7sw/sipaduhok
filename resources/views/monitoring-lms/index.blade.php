@extends('layouts.sneat')

@section('title', 'Monitoring LMS')
@section('page-title', 'Monitoring LMS')
@section('page-subtitle', 'Tinjau materi, tugas, latihan, dan ujian yang diunggah oleh guru')

@section('sidebar-menu')
    @include($rolePartial)
@endsection

@section('styles')
<style>
    .lms-monitoring-wrapper { padding: 0; }

    .filter-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .filter-row {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(220px, 280px) auto minmax(260px, auto);
        gap: 12px;
        align-items: center;
    }

    .filter-input {
        align-items: center;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        display: flex;
        gap: 10px;
        height: 42px;
        min-width: 0;
        padding: 0 14px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .filter-input:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, .12);
    }

    .filter-input i {
        color: var(--text-muted);
        flex: 0 0 auto;
        font-size: 14px;
        pointer-events: none;
    }

    .filter-input input {
        background: transparent;
        border: 0 !important;
        box-shadow: none !important;
        height: 100%;
        min-width: 0;
        padding: 0;
        width: 100%;
    }

    .filter-select {
        min-width: 200px;
        height: 42px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    .filter-btn {
        height: 42px;
        border-radius: 10px;
        font-weight: 600;
        padding: 0 18px;
        white-space: nowrap;
    }

    .filter-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--text-main);
        margin: 0;
        padding-left: 0; /* Override Bootstrap .form-check padding */
        cursor: pointer;
        min-width: 0;
    }

    .filter-toggle .form-check-input {
        margin: 0; /* Override Bootstrap negative margin */
        float: none;
        flex-shrink: 0;
    }

    .filter-toggle span { line-height: 1.3; }

    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    }

    .stat-content { min-width: 0; }
    .stat-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .icon-kelas { background: rgba(67, 97, 238, 0.08); color: var(--primary-color); }
    .icon-materi { background: rgba(2, 132, 199, 0.08); color: #0284c7; }
    .icon-tugas { background: rgba(217, 119, 6, 0.08); color: #d97706; }
    .icon-latihan { background: rgba(124, 58, 237, 0.08); color: #7c3aed; }
    .icon-ujian { background: rgba(220, 38, 38, 0.08); color: #dc2626; }

    .section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-main);
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .section-title-text { display: flex; align-items: center; }

    .section-title-count {
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-muted);
    }

    /* Class grid */
    .kelas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .kelas-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
        position: relative;
    }

    .kelas-card.kelas-empty { opacity: 0.65; }

    .kelas-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.06);
        border-color: var(--primary-color);
        text-decoration: none;
        color: inherit;
        opacity: 1;
    }

    .kelas-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 8px;
        flex-wrap: wrap;
    }

    .kelas-jenjang-badge {
        background: var(--primary-color);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .kelas-ta-badge {
        background: rgba(67, 97, 238, 0.08);
        color: var(--primary-color);
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 600;
    }

    .kelas-ta-badge.kelas-ta-active {
        background: rgba(22, 163, 74, 0.1);
        color: #15803d;
    }

    .kelas-nama {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }

    .kelas-cabang {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .kelas-wali {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin: 0 0 12px 0;
    }

    .kelas-stats-row {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .stat-pill {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 4px 9px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .stat-pill-empty { background: var(--background-color); color: var(--text-muted); }
    .stat-pill-materi { background: rgba(2, 132, 199, 0.08); color: #0284c7; }
    .stat-pill-tugas { background: rgba(217, 119, 6, 0.08); color: #92400e; }
    .stat-pill-latihan { background: rgba(124, 58, 237, 0.08); color: #5b21b6; }
    .stat-pill-ujian { background: rgba(220, 38, 38, 0.08); color: #b91c1c; }

    .kelas-arrow {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid var(--border-color);
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .kelas-arrow i { transition: transform 0.2s ease; }
    .kelas-card:hover .kelas-arrow i { transform: translateX(4px); }

    .empty-state {
        background: var(--surface-color);
        border: 1px dashed var(--border-color);
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 3.5rem;
        color: var(--border-color);
        margin-bottom: 16px;
        display: block;
    }

    @media (max-width: 768px) {
        .filter-card { padding: 12px; }
        .filter-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .filter-input, .filter-select { width: 100%; min-width: 0; }
        .filter-btn { width: 100%; }
        .filter-toggle {
            align-items: flex-start;
            padding: 4px 2px;
        }

        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat-card { padding: 14px; }
        .stat-value { font-size: 1.4rem; }
        .stat-icon { width: 38px; height: 38px; font-size: 1rem; }

        .kelas-grid { grid-template-columns: 1fr; gap: 12px; }
        .kelas-card { padding: 16px; }
        .kelas-nama { font-size: 1rem; }
    }

    @media (max-width: 480px) {
        .stat-label { font-size: 0.7rem; }
        .stat-value { font-size: 1.25rem; }
    }
</style>
@endsection

@section('content')
<div class="lms-monitoring-wrapper">
    {{-- Filter bar --}}
    <form method="GET" class="filter-card">
        <div class="filter-row">
            <div class="filter-input">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau kode kelas..." class="form-control">
            </div>
            <select name="tahun_ajaran_id" class="form-select filter-select">
                <option value="0" {{ ($taFilter ?? 0) === 0 ? 'selected' : '' }}>Semua Tahun Ajaran</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" {{ ($taFilter ?? 0) === $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama_tahun_ajaran ?? $ta->tahun_ajaran ?? ('TA #' . $ta->id) }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary filter-btn">
                <i class="fas fa-filter me-1"></i>Terapkan
            </button>
            <label class="filter-toggle form-check">
                <input type="checkbox" name="only_with_content" value="1" class="form-check-input me-2"
                    onchange="this.form.submit()" {{ ($onlyWithContent ?? false) ? 'checked' : '' }}>
                <span>Hanya tampilkan kelas yang sudah punya konten LMS</span>
            </label>
        </div>
    </form>

    {{-- Stats summary --}}
    @php
        $totalMateri = $kelas->sum('materi_count');
        $totalTugas = $kelas->sum('tugas_count');
        $totalLatihan = $kelas->sum('latihan_count');
        $totalUjian = $kelas->sum('ujian_count');
    @endphp
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Total Kelas</div>
                <div class="stat-value">{{ $kelas->count() }}</div>
            </div>
            <div class="stat-icon icon-kelas"><i class="fas fa-school"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Materi</div>
                <div class="stat-value">{{ $totalMateri }}</div>
            </div>
            <div class="stat-icon icon-materi"><i class="fas fa-book"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Tugas</div>
                <div class="stat-value">{{ $totalTugas }}</div>
            </div>
            <div class="stat-icon icon-tugas"><i class="fas fa-tasks"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Latihan</div>
                <div class="stat-value">{{ $totalLatihan }}</div>
            </div>
            <div class="stat-icon icon-latihan"><i class="fas fa-pencil-ruler"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Ujian</div>
                <div class="stat-value">{{ $totalUjian }}</div>
            </div>
            <div class="stat-icon icon-ujian"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>

    {{-- Class grid --}}
    <h5 class="section-title">
        <span class="section-title-text">
            <i class="fas fa-chalkboard me-2" style="color: var(--primary-color);"></i>Daftar Kelas
        </span>
        <span class="section-title-count">{{ $kelas->count() }} kelas ditampilkan</span>
    </h5>

    @if($kelas->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox empty-icon"></i>
            <p class="text-muted mb-1">Tidak ada kelas yang ditemukan.</p>
            <small class="text-muted">Coba ubah filter Tahun Ajaran atau hilangkan centang "Hanya tampilkan kelas yang sudah punya konten LMS".</small>
        </div>
    @else
        <div class="kelas-grid">
            @foreach($kelas as $k)
                @php
                    $totalKonten = ($k->materi_count + $k->tugas_count + $k->latihan_count + $k->ujian_count);
                    $isEmpty = $totalKonten === 0;
                    $isActiveTa = $tahunAjaranAktif && (int) $k->tahun_ajaran_id === (int) $tahunAjaranAktif->id;
                @endphp
                <a href="{{ route($baseRoute . '.kelas', $k->id) }}" class="kelas-card {{ $isEmpty ? 'kelas-empty' : '' }}">
                    <div class="kelas-header">
                        <div class="kelas-jenjang-badge">{{ $k->jenjang }}</div>
                        @if($k->tahunAjaran)
                            <span class="kelas-ta-badge {{ $isActiveTa ? 'kelas-ta-active' : '' }}">
                                <i class="fas fa-calendar-alt me-1"></i>{{ $k->tahunAjaran->nama_tahun_ajaran ?? $k->tahunAjaran->tahun_ajaran ?? '-' }}
                            </span>
                        @endif
                    </div>
                    <h6 class="kelas-nama">{{ $k->nama_kelas }}</h6>
                    @if($k->cabang)
                        <p class="kelas-cabang">
                            <i class="fas fa-map-marker-alt"></i>{{ $k->cabang->nama_cabang ?? '-' }}
                        </p>
                    @endif
                    @if($k->waliKelas)
                        <p class="kelas-wali">
                            <i class="fas fa-user-tie me-1"></i>{{ $k->waliKelas->nama_lengkap }}
                        </p>
                    @endif
                    <div class="kelas-stats-row">
                        @if($isEmpty)
                            <span class="stat-pill stat-pill-empty">
                                <i class="fas fa-circle-minus"></i> Belum ada konten
                            </span>
                        @else
                            <span class="stat-pill stat-pill-materi">
                                <i class="fas fa-book"></i> {{ $k->materi_count }} Materi
                            </span>
                            <span class="stat-pill stat-pill-tugas">
                                <i class="fas fa-tasks"></i> {{ $k->tugas_count }} Tugas
                            </span>
                            <span class="stat-pill stat-pill-latihan">
                                <i class="fas fa-pencil-ruler"></i> {{ $k->latihan_count }} Latihan
                            </span>
                            <span class="stat-pill stat-pill-ujian">
                                <i class="fas fa-file-alt"></i> {{ $k->ujian_count }} Ujian
                            </span>
                        @endif
                    </div>
                    <div class="kelas-arrow">
                        <span>Lihat detail</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
