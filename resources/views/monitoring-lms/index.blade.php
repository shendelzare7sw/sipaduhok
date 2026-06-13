@extends('layouts.sneat')

@section('title', 'Monitoring LMS')
@section('page-title', 'Monitoring LMS')
@section('page-subtitle', 'Tinjau materi, tugas, latihan, dan ujian yang diunggah oleh guru')

@section('sidebar-menu')
    @include($rolePartial)
@endsection

@push('styles')
    @vite(['resources/css/monitoring-lms/index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/monitoring-lms/index.js'])
@endpush

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
                    data-auto-submit-change {{ ($onlyWithContent ?? false) ? 'checked' : '' }}>
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
            <i class="fas fa-chalkboard me-2 section-title-icon"></i>Daftar Kelas
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
