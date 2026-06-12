@extends('layouts.sneat')

@section('title', 'Detail Catatan Monitoring')
@section('page-title', 'Detail Catatan Monitoring')
@section('page-subtitle', 'Catatan dari ' . ($catatan->pengirim->name ?? 'Pimpinan'))

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/guru/lms/catatan-monitoring/show.css'])
@endsection

@section('content')
<div class="cm-show-wrapper guru-lms-monitoring-show-page">
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
            <div class="alert alert-warning mb-0 cm-missing-content-alert">
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
