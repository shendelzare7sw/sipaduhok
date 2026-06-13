@extends('layouts.sneat')

@section('title', 'Monitoring LMS - ' . $kelas->nama_kelas)
@section('page-title', 'Monitoring LMS')
@section('page-subtitle', 'Detail konten kelas ' . $kelas->nama_kelas . ($kelas->tahunAjaran ? ' - ' . ($kelas->tahunAjaran->nama_tahun_ajaran ?? $kelas->tahunAjaran->tahun_ajaran) : ''))

@section('sidebar-menu')
    @include($rolePartial)
@endsection

@push('styles')
    @vite(['resources/css/monitoring-lms/kelas-detail.css'])
@endpush

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
            <div class="kelas-header-main">
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
                'dateLabel' => 'Tenggat',
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
    @vite(['resources/js/monitoring-lms/kelas-detail.js'])
@endpush
@endsection
