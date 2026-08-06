@extends('layouts.sneat')

@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('page-subtitle', 'Jadwal mengajar mingguan Anda')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/guru/jadwal/index.css'])
@endsection

@section('content')
@php
    $allJadwal = $jadwal->flatten(1);
    $totalJadwal = $allJadwal->count();
    $totalHari = $jadwal->count();
    $totalMapel = $allJadwal->pluck('mataPelajaran.nama_mapel')->filter()->unique()->count();
    $totalKelas = $allJadwal
        ->flatMap(fn ($item) => $item->kelas->pluck('nama_kelas'))
        ->filter()
        ->unique()
        ->count();
@endphp

<div class="guru-page">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('guru.jadwal.index') }}" class="guru-jadwal-filter-bar">
        <div class="form-group">
            <label for="filterTahunAjaran">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" id="filterTahunAjaran" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="0" {{ $taFilterId == 0 ? 'selected' : '' }}>Semua TA</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" {{ $taFilterId == $ta->id ? 'selected' : '' }}>
                        {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <noscript>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
        </noscript>
    </form>

    <div class="guru-stat-grid">
        <div class="guru-stat">
            <div class="guru-stat-icon guru-stat-icon-primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalJadwal }}</div>
                <div class="guru-stat-label">Total Jadwal</div>
            </div>
        </div>
        <div class="guru-stat">
            <div class="guru-stat-icon guru-stat-icon-success">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalHari }}</div>
                <div class="guru-stat-label">Hari Mengajar</div>
            </div>
        </div>
        <div class="guru-stat">
            <div class="guru-stat-icon guru-stat-icon-info">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalKelas }}</div>
                <div class="guru-stat-label">Kelas Diampu</div>
            </div>
        </div>
        <div class="guru-stat">
            <div class="guru-stat-icon guru-stat-icon-warning">
                <i class="fas fa-book-open"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalMapel }}</div>
                <div class="guru-stat-label">Mata Pelajaran</div>
            </div>
        </div>
    </div>

    @if($jadwal->isEmpty())
        <div class="guru-card">
            <div class="guru-empty">
                <div class="guru-empty-icon"><i class="fas fa-calendar-times"></i></div>
                <h5>Belum Ada Jadwal Mengajar</h5>
                <p class="mb-0">
                    @if($taFilterId)
                        Tidak ada jadwal untuk tahun ajaran yang dipilih. Coba pilih <strong>Semua TA</strong> di filter di atas.
                    @else
                        Jadwal Anda akan tampil di sini setelah ditentukan oleh admin atau waka.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="guru-day-grid">
            @foreach($jadwal as $hari => $items)
                <div class="guru-day-card">
                    <div class="guru-day-header">
                        <h5 class="guru-day-title">
                            <i class="fas fa-calendar-day text-primary"></i>{{ $hari }}
                        </h5>
                        <span class="guru-day-count">{{ $items->count() }} jadwal</span>
                    </div>
                    <div class="guru-lesson-list">
                        @foreach($items as $item)
                            <div class="guru-lesson">
                                <div class="guru-time">
                                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                    <span>- {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</span>
                                </div>
                                <div>
                                    <div class="guru-mapel">{{ $item->mataPelajaran->nama_mapel ?? '-' }}</div>
                                    <div class="guru-meta">
                                        {{ $item->tahunAjaran->nama_tahun_ajaran ?? 'Tahun ajaran tidak tersedia' }}
                                    </div>
                                    <div class="guru-class-list">
                                        @forelse($item->kelas as $kelas)
                                            <span class="guru-class-chip">{{ $kelas->nama_kelas }}</span>
                                        @empty
                                            <span class="guru-class-chip">Kelas belum diatur</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
