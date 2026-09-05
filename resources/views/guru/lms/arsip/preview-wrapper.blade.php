@extends('layouts.app')

@section('title', 'Arsip Preview - ' . ($previewTitle ?? 'Konten LMS'))
@section('page-title', 'Mode Arsip')
@section('page-subtitle', 'Pratinjau konten ' . ($kontenLabel ?? '') . ' dari arsip Anda')


@section('styles')
    @vite(['resources/css/guru/lms/arsip/preview.css'])
@endsection

@section('content')
<div class="preview-wrapper">
    <div class="preview-banner">
        <div class="preview-banner-text">
            <i class="fas fa-archive preview-banner-icon"></i>
            <div>
                <div class="preview-banner-title">Mode Arsip Read-Only</div>
                <div class="preview-banner-subtitle">
                    Konten ini dari TA <strong>{{ $konten->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</strong>.
                    Anda hanya bisa melihat - gunakan tombol "Salin" untuk membuat versi baru di kelas TA aktif.
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('guru.lms.arsip.form-salin', [$kontenType, $kontenId]) }}"
               class="btn-banner-salin">
                <i class="fas fa-copy me-1"></i>Salin ke Kelas Aktif
            </a>
        </div>
    </div>

    <div class="preview-content">
        @yield('preview-content')
    </div>
</div>
@endsection
