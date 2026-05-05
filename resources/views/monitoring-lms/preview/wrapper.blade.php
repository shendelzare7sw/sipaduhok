@extends('layouts.sneat')

@section('title', 'Preview - ' . ($previewTitle ?? 'Konten LMS'))
@section('page-title', 'Mode Preview')
@section('page-subtitle', 'Tinjauan konten ' . ($kontenLabel ?? '') . ' sebagaimana dilihat siswa')

@section('sidebar-menu')
    @include($rolePartial)
@endsection

@section('styles')
<style>
    .preview-wrapper { padding: 0; }

    /* Sticky banner */
    .preview-banner {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        box-shadow: 0 8px 20px -5px rgba(67, 97, 238, 0.3);
    }

    .preview-banner-text {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 240px;
    }

    .preview-banner-icon {
        font-size: 1.4rem;
        opacity: 0.95;
        flex-shrink: 0;
    }

    .preview-banner-title {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .preview-banner-subtitle {
        font-size: 0.75rem;
        opacity: 0.92;
        line-height: 1.4;
    }

    .btn-banner-catatan {
        background: white;
        color: var(--primary-color);
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-banner-catatan:hover {
        background: var(--background-color);
        transform: translateY(-2px);
    }

    /* Content card */
    .preview-content {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 26px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .preview-section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 8px 0;
    }

    .preview-meta-row {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
    }

    .preview-meta-row i { color: var(--primary-color); margin-right: 4px; }

    .preview-section { margin-bottom: 22px; }

    .preview-section-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .preview-section-body {
        color: var(--text-main);
        line-height: 1.7;
        font-size: 0.9rem;
    }

    .preview-file-box {
        background: var(--background-color);
        border: 2px dashed var(--border-color);
        border-radius: 10px;
        padding: 22px;
        text-align: center;
        margin: 12px 0;
    }

    .preview-file-box i {
        font-size: 2.2rem;
        color: var(--primary-color);
        margin-bottom: 10px;
        display: block;
    }

    /* Inline file previews (PDF iframe, image, video) */
    .inline-preview-box {
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px;
        text-align: center;
    }

    .inline-pdf-frame {
        width: 100%;
        height: 75vh;
        min-height: 500px;
        border: none;
        border-radius: 8px;
        background: white;
    }

    @media (max-width: 768px) {
        .inline-pdf-frame { height: 60vh; min-height: 400px; }
    }

    .preview-disabled-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--background-color);
        color: var(--text-muted);
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid var(--border-color);
        cursor: not-allowed;
    }

    /* Settings grid (used by tugas/ujian preview) */
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }

    .setting-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: var(--background-color);
        border-radius: 8px;
        font-size: 0.82rem;
        color: var(--text-main);
    }

    .setting-item i { color: var(--primary-color); }

    /* Soal list (ujian preview) */
    .soal-list { display: grid; gap: 14px; }

    .soal-card {
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 16px;
    }

    .soal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .soal-nomor {
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.88rem;
    }

    .soal-meta {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .soal-badge {
        background: rgba(67, 97, 238, 0.08);
        color: var(--primary-color);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .soal-bobot {
        background: rgba(217, 119, 6, 0.08);
        color: #92400e;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .soal-narasi {
        background: var(--surface-color);
        border-left: 3px solid var(--primary-color);
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-bottom: 10px;
        font-style: italic;
    }

    .soal-image { margin-bottom: 12px; }

    .soal-pertanyaan {
        font-size: 0.92rem;
        color: var(--text-main);
        line-height: 1.6;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .soal-pilihan-list { display: grid; gap: 6px; }

    .soal-pilihan {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px 13px;
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.82rem;
        color: var(--text-main);
    }

    .pilihan-benar {
        background: rgba(22, 163, 74, 0.05);
        border-color: rgba(22, 163, 74, 0.4);
        color: #15803d;
        font-weight: 600;
    }

    .pilihan-letter {
        width: 26px;
        height: 26px;
        background: var(--primary-color);
        color: white;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.72rem;
        flex-shrink: 0;
    }

    .pilihan-benar .pilihan-letter { background: #16a34a; }

    .soal-kunci {
        background: rgba(22, 163, 74, 0.05);
        border: 1px solid rgba(22, 163, 74, 0.3);
        color: #15803d;
        padding: 9px 13px;
        border-radius: 8px;
        font-size: 0.82rem;
    }

    .kunci-essay {
        background: rgba(217, 119, 6, 0.05);
        border-color: rgba(217, 119, 6, 0.3);
        color: #92400e;
    }

    .kunci-label { font-weight: 700; margin-right: 8px; }

    /* Mobile */
    @media (max-width: 768px) {
        .preview-banner {
            padding: 14px;
            flex-direction: column;
            align-items: stretch;
        }
        .preview-banner-text { min-width: 0; }
        .preview-banner-title { font-size: 0.88rem; }
        .preview-banner-subtitle { font-size: 0.72rem; }
        .btn-banner-catatan { width: 100%; padding: 11px; }

        .preview-content { padding: 18px; }
        .preview-section-title { font-size: 1.05rem; }
        .preview-meta-row { font-size: 0.75rem; gap: 10px; }
    }

    @media (max-width: 640px) {
        .soal-card { padding: 14px; }
        .soal-pilihan { padding: 8px 12px; gap: 8px; }
        .pilihan-letter { width: 24px; height: 24px; font-size: 0.7rem; }
    }

    /* Override Bootstrap focus/active ring on all buttons */
    .preview-wrapper .btn:focus,
    .preview-wrapper .btn:active,
    .preview-wrapper .btn-banner-catatan:focus,
    .preview-wrapper .btn-banner-catatan:active {
        outline: none;
        box-shadow: none;
    }
    .preview-wrapper .btn-primary:focus,
    .preview-wrapper .btn-primary:active {
        background-color: var(--primary-dark, #3651d4);
        border-color: var(--primary-dark, #3651d4);
        color: white;
        box-shadow: none;
    }
    .preview-wrapper .btn-outline-primary:focus,
    .preview-wrapper .btn-outline-primary:active {
        background-color: rgba(67, 97, 238, 0.06);
        color: var(--primary-color);
        box-shadow: none;
    }
    
    /* Image Lightbox */
    .preview-content img {
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .preview-content img:hover {
        opacity: 0.9;
    }
    #imagePreviewModal .modal-body {
        padding: 20px 0;
        text-align: center;
        background: transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 300px;
    }
    #imagePreviewModal img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    .btn-close-custom {
        background-color: rgba(71, 85, 105, 0.9);
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 999px;
        font-size: 0.95rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-top: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .btn-close-custom:hover {
        background-color: rgba(51, 65, 85, 1);
        color: white;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div class="preview-wrapper">
    {{-- Sticky banner --}}
    <div class="preview-banner">
        <div class="preview-banner-text">
            <i class="fas fa-eye preview-banner-icon"></i>
            <div>
                <div class="preview-banner-title">Mode Preview Tinjauan</div>
                <div class="preview-banner-subtitle">
                    Anda melihat konten ini sebagaimana akan tampil di sisi siswa. Tombol interaksi (kerjakan / submit) dinonaktifkan.
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
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body">
                <img src="" id="imagePreviewSource" alt="Preview Image" class="rounded shadow-lg">
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup Gambar
                </button>
            </div>
        </div>
    </div>
</div>

@include('monitoring-lms.partials.modal-catatan')
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewImages = document.querySelectorAll('.preview-content img');
        const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        const modalImage = document.getElementById('imagePreviewSource');

        previewImages.forEach(img => {
            img.addEventListener('click', function() {
                modalImage.src = this.src;
                modal.show();
            });
        });
    });
</script>
@endsection
