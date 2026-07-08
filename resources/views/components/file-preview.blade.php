@props([
    'path' => null,
    'title' => 'File',
    'class' => null,
    'label' => null,
    'iconClass' => 'fas fa-eye'
])

@if($path)
@php
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';

    $downloadUrl = asset('storage/' . $path);

    // Preview URL: token acak terikat pemilik (extensionless), lihat helper preview_url().
    $previewUrl = preview_url($path);

    $modalId = 'filemodal' . md5($path . uniqid());
@endphp

@once
    @push('styles')
        @vite(['resources/css/components/file-preview.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/components/file-preview.js'])
    @endpush
@endonce

<div class="d-inline-block">
    @if($isImage)
        <!-- Image Preview Modal Trigger -->
        <button type="button"
                class="{{ $class ?? 'btn btn-sm btn-info' }}"
                data-bs-toggle="modal"
                data-bs-target="#{{ $modalId }}">
            <i class="{{ $iconClass }} me-1"></i>{{ $label ?? 'Lihat Gambar' }}
        </button>

        <!-- Download Button -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-outline-primary" title="Unduh File">
            <i class="fas fa-download"></i>
        </a>

        <!-- Image Modal -->
        @push('modals')
        <div class="modal fade file-preview-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-image me-2"></i>Pratinjau Gambar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0">
                        <img src="{{ $downloadUrl }}" alt="Pratinjau" class="img-fluid file-preview-image">
                    </div>
                    <div class="modal-footer">
                        <a href="{{ $downloadUrl }}" download class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Unduh Gambar
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @endpush

    @elseif($isPdf)
        <!-- PDF Preview Modal Trigger -->
        <button type="button"
                class="{{ $class ?? 'btn btn-sm btn-danger' }}"
                data-bs-toggle="modal"
                data-bs-target="#{{ $modalId }}">
            <i class="{{ $iconClass }} me-1"></i>{{ $label ?? 'Lihat PDF' }}
        </button>

        <!-- Download Button -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-outline-primary" title="Unduh File">
            <i class="fas fa-download"></i>
        </a>

        <!-- PDF Modal -->
        @push('modals')
        <div class="modal fade file-preview-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content file-preview-pdf-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-pdf me-2"></i>Pratinjau PDF
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 h-100">
                        <iframe src="" data-src="{{ $previewUrl }}" width="100%" height="100%" class="file-preview-frame"></iframe>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ $downloadUrl }}" download class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Unduh PDF
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @endpush

    @else
        <!-- Other Files (No Preview, Direct Download) -->
        <a href="{{ $downloadUrl }}"
           download
           class="{{ $class ?? 'btn btn-sm btn-secondary' }}">
            <i class="fas fa-download me-1"></i>Unduh File ({{ strtoupper($extension) }})
        </a>
    @endif
</div>
@endif
