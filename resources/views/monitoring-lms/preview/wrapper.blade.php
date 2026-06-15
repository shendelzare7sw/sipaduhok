@extends('layouts.sneat')

@section('title', 'Pratinjau - ' . ($previewTitle ?? 'Konten LMS'))
@section('page-title', 'Mode Pratinjau')
@section('page-subtitle', 'Tinjauan konten ' . ($kontenLabel ?? '') . ' sebagaimana dilihat siswa')

@section('sidebar-menu')
    @include($rolePartial)
@endsection

@push('styles')
    @vite(['resources/css/monitoring-lms/preview.css'])
@endpush

@section('content')
<div class="preview-wrapper">
    {{-- Sticky banner --}}
    <div class="preview-banner">
        <div class="preview-banner-text">
            <i class="fas fa-eye preview-banner-icon"></i>
            <div>
                <div class="preview-banner-title">Mode Pratinjau Tinjauan</div>
                <div class="preview-banner-subtitle">
                    Anda melihat konten ini sebagaimana akan tampil di sisi siswa. Tombol interaksi (kerjakan / kumpulkan) dinonaktifkan.
                </div>
            </div>
        </div>
        <div>
            <button type="button"
                class="btn-banner-catatan"
                data-monitoring-catatan
                data-konten-type="{{ $kontenType }}"
                data-konten-id="{{ $kontenId }}"
                data-konten-label="{{ $kontenLabel ?? 'Konten' }}"
                data-konten-judul="{{ $previewTitle ?? '-' }}">
                <i class="fas fa-comment-dots me-1"></i>Kirim Catatan
            </button>
        </div>
    </div>

    <div class="preview-content">
        @yield('preview-content')
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered preview-image-modal-dialog">
        <div class="modal-content preview-image-modal-content">
            <img src="" id="imagePreviewSource" alt="Pratinjau Gambar" class="preview-image-source">
            <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                <i class="fas fa-times"></i> Tutup Gambar
            </button>
        </div>
    </div>
</div>

@include('monitoring-lms.partials.modal-catatan')
@endsection

@push('scripts')
    @vite(['resources/js/monitoring-lms/preview.js'])
@endpush
