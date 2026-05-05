@extends('layouts.sneat')

@section('title', 'Detail Catatan Monitoring')
@section('page-title', 'Detail Catatan Monitoring')
@section('page-subtitle', 'Catatan dari ' . ($catatan->pengirim->name ?? 'Pimpinan'))

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .cm-show-wrapper { 
        padding: 0; 
        max-width: 900px; 
        margin: 0 auto; /* Center the wrapper */
    }

    .cm-breadcrumb { margin-bottom: 20px; }

    .cm-breadcrumb a {
        color: var(--bs-primary, #4361ee);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .cm-breadcrumb a:hover { text-decoration: underline; }

    .cm-sender-card {
        background: linear-gradient(135deg, #4361ee, #3a0ca3); /* Set explicit vibrant blue gradient */
        color: #ffffff !important;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        display: flex;
        gap: 20px;
        align-items: center;
        box-shadow: 0 10px 25px -5px rgba(67, 97, 238, 0.3);
    }

    .cm-sender-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .cm-sender-info { flex: 1; min-width: 0; }

    .cm-sender-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 6px;
        color: #e0e7ff;
    }

    .cm-sender-name {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        color: #ffffff !important; /* Force white color to prevent inheritance */
    }

    .cm-sender-meta {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        font-size: 0.85rem;
        color: #f8fafc;
    }

    .cm-role-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 4px 12px;
        border-radius: 999px;
        font-weight: 600;
        text-transform: capitalize;
        color: #ffffff;
        font-size: 0.8rem;
    }

    .cm-konten-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .cm-konten-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .cm-konten-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .cm-konten-badge {
        background: #fee2e2;
        color: #b91c1c;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .cm-konten-judul {
        font-size: 1.05rem;
        color: #1e293b;
    }

    .cm-konten-meta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 18px;
    }

    .cm-konten-edit {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .cm-body-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 26px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .cm-body-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
    }

    .cm-body-content {
        font-size: 1rem;
        color: #334155;
        line-height: 1.8;
        white-space: pre-wrap;
    }

    .cm-receipt {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #16a34a;
        padding: 16px;
        background: #f0fdf4;
        border-radius: 12px;
        border: 1px solid #bbf7d0;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .cm-sender-card { padding: 20px; gap: 16px; flex-direction: column; text-align: center; }
        .cm-sender-meta { justify-content: center; }
        .cm-sender-avatar { width: 56px; height: 56px; font-size: 1.5rem; margin: 0 auto; }
        .cm-sender-name { font-size: 1.25rem; }
        .cm-konten-card, .cm-body-card { padding: 20px; }
        .cm-body-content { font-size: 0.95rem; }
    }
</style>
@endsection

@section('content')
<div class="cm-show-wrapper">
    {{-- Breadcrumb --}}
    <nav class="cm-breadcrumb">
        <a href="{{ route('guru.lms.catatan-monitoring.index') }}">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Catatan
        </a>
    </nav>

    {{-- Sender card --}}
    <div class="cm-sender-card">
        <div class="cm-sender-avatar"><i class="fas fa-user-shield"></i></div>
        <div class="cm-sender-info">
            <div class="cm-sender-label">Catatan dari</div>
            <h3 class="cm-sender-name">{{ $catatan->pengirim->name ?? 'Pimpinan' }}</h3>
            <div class="cm-sender-meta">
                <span class="cm-role-badge">{{ str_replace('_', ' ', $catatan->pengirim_role) }}</span>
                <span><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($catatan->created_at)->translatedFormat('d F Y, H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Konten reference --}}
    <div class="cm-konten-card">
        <div class="cm-konten-label">Terkait Konten</div>
        <div class="cm-konten-row">
            <span class="cm-konten-badge">{{ $catatan->kontenLabel() }}</span>
            <strong class="cm-konten-judul">{{ $catatan->kontenJudul() }}</strong>
        </div>
        <div class="cm-konten-meta">
            @if($catatan->mataPelajaran)
                <span><i class="fas fa-book me-1"></i>{{ $catatan->mataPelajaran->nama_mapel }}</span>
            @endif
            @if($catatan->kelas)
                <span><i class="fas fa-school me-1"></i>{{ $catatan->kelas->nama_kelas }}</span>
            @endif
        </div>

        @if($konten)
            @php
                $editRoute = null;
                if ($catatan->konten_type === 'materi') {
                    $editRoute = route('guru.lms.materi.edit', [$catatan->kelas_id, $catatan->mata_pelajaran_id, $catatan->konten_id]);
                } elseif ($catatan->konten_type === 'tugas') {
                    $editRoute = route('guru.lms.tugas.edit', [$catatan->kelas_id, $catatan->mata_pelajaran_id, $catatan->konten_id]);
                } elseif ($catatan->konten_type === 'ujian') {
                    $editRoute = route('guru.lms.ujian.edit', [$catatan->kelas_id, $catatan->mata_pelajaran_id, $catatan->konten_id]);
                }
            @endphp
            @if($editRoute)
                <a href="{{ $editRoute }}" class="btn btn-outline-primary cm-konten-edit">
                    <i class="fas fa-edit"></i> Buka & Revisi Konten
                </a>
            @endif
        @else
            <div class="alert alert-warning mb-0" style="border-radius: 8px; border: none; background: rgba(217, 119, 6, 0.08); color: #92400e; font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle me-1"></i>Konten yang dimaksud sudah tidak tersedia (mungkin telah dihapus).
            </div>
        @endif
    </div>

    {{-- Catatan body --}}
    <div class="cm-body-card">
        <div class="cm-body-label">Isi Catatan</div>
        <div class="cm-body-content">{!! nl2br(e($catatan->isi_catatan)) !!}</div>
    </div>

    {{-- Read receipt --}}
    @if($catatan->dibaca_pada)
        <div class="cm-receipt">
            <i class="fas fa-check-circle"></i> Anda telah membaca catatan ini pada {{ \Carbon\Carbon::parse($catatan->dibaca_pada)->translatedFormat('d F Y, H:i') }}
        </div>
    @endif
</div>
@endsection
