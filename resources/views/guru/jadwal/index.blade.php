@extends('layouts.sneat')

@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('page-subtitle', 'Jadwal mengajar mingguan Anda')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --g-primary: #4361ee;
        --g-success: #10b981;
        --g-warning: #f59e0b;
        --g-info: #06b6d4;
        --g-text: #1e293b;
        --g-muted: #64748b;
        --g-line: #e2e8f0;
        --g-soft: #f8fafc;
    }

    .guru-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .guru-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .guru-stat {
        min-height: 92px;
        padding: 16px;
        background: #fff;
        border: 1px solid var(--g-line);
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .guru-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .guru-stat-value {
        color: var(--g-text);
        font-size: 24px;
        font-weight: 900;
        line-height: 1.05;
        margin-bottom: 4px;
    }

    .guru-stat-label {
        color: var(--g-muted);
        font-size: 11px;
        font-weight: 900;
        line-height: 1.3;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .guru-card {
        background: #fff;
        border: 1px solid var(--g-line);
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .guru-day-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .guru-day-card {
        background: #fff;
        border: 1px solid var(--g-line);
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .guru-day-header {
        padding: 14px 16px;
        background: var(--g-soft);
        border-bottom: 1px solid var(--g-line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .guru-day-title {
        margin: 0;
        color: var(--g-text);
        font-size: 15px;
        font-weight: 900;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guru-day-count {
        min-height: 26px;
        padding: 4px 9px;
        border-radius: 999px;
        background: rgba(67, 97, 238, .1);
        color: var(--g-primary);
        font-size: 11px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
    }

    .guru-lesson-list {
        display: grid;
        gap: 0;
    }

    .guru-lesson {
        display: grid;
        grid-template-columns: 94px minmax(0, 1fr);
        gap: 14px;
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
    }

    .guru-lesson:last-child {
        border-bottom: 0;
    }

    .guru-time {
        color: var(--g-primary);
        font-size: 13px;
        font-weight: 900;
        line-height: 1.25;
    }

    .guru-time span {
        display: block;
        color: var(--g-muted);
        font-size: 11px;
        font-weight: 800;
        margin-top: 2px;
    }

    .guru-mapel {
        color: var(--g-text);
        font-size: 14px;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .guru-meta {
        color: var(--g-muted);
        font-size: 12px;
        margin-top: 5px;
        line-height: 1.45;
    }

    .guru-class-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
    }

    .guru-class-chip {
        min-height: 26px;
        padding: 4px 9px;
        border-radius: 999px;
        background: rgba(6, 182, 212, .12);
        color: #0e7490;
        font-size: 11px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        line-height: 1.25;
    }

    .guru-empty {
        padding: 48px 18px;
        text-align: center;
        color: var(--g-muted);
    }

    .guru-empty-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .guru-empty h5 {
        color: var(--g-text);
        font-weight: 900;
        margin-bottom: 6px;
    }

    @media (max-width: 991.98px) {
        .guru-stat-grid,
        .guru-day-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .guru-page {
            padding-left: .75rem;
            padding-right: .75rem;
        }

        .guru-stat-grid,
        .guru-day-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .guru-stat {
            min-height: 84px;
            padding: 14px;
        }

        .guru-day-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .guru-lesson {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .guru-time span {
            display: inline;
            margin-left: 4px;
        }
    }
</style>
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

    <div class="guru-stat-grid">
        <div class="guru-stat">
            <div class="guru-stat-icon" style="background: rgba(67,97,238,.12); color: var(--g-primary);">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalJadwal }}</div>
                <div class="guru-stat-label">Total Jadwal</div>
            </div>
        </div>
        <div class="guru-stat">
            <div class="guru-stat-icon" style="background: rgba(16,185,129,.12); color: #047857;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalHari }}</div>
                <div class="guru-stat-label">Hari Mengajar</div>
            </div>
        </div>
        <div class="guru-stat">
            <div class="guru-stat-icon" style="background: rgba(6,182,212,.12); color: #0e7490;">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <div class="guru-stat-value">{{ $totalKelas }}</div>
                <div class="guru-stat-label">Kelas Diampu</div>
            </div>
        </div>
        <div class="guru-stat">
            <div class="guru-stat-icon" style="background: rgba(245,158,11,.14); color: #b45309;">
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
                <p class="mb-0">Jadwal Anda akan tampil di sini setelah ditentukan oleh admin atau waka.</p>
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
