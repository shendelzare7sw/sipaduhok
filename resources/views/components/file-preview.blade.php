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

    // Direct asset URL for download
    $downloadUrl = asset('storage/' . $path);

    // Preview URL: use integer-based cache ID just like validasi-izin uses database ID
    // This makes the URL look like /view-document/54321 — no file extension, no suspicious params
    $previewId = crc32($path . session()->getId()) % 100000;
    if ($previewId < 0) $previewId = abs($previewId);
    \Illuminate\Support\Facades\Cache::put('docview_' . $previewId, $path, now()->addHours(4));
    $previewUrl = url('/view-document/' . $previewId);

    // Unique ID for this component instance
    $modalId = 'filemodal' . md5($path . uniqid());
@endphp

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
        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-image me-2"></i>Pratinjau Gambar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0">
                        <img src="{{ $downloadUrl }}" alt="Pratinjau" class="img-fluid" style="max-height: 80vh;">
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

        <!-- PDF Modal: uses data-src lazy loading exactly like validasi-izin -->
        @push('modals')
        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content" style="height: 90vh;">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-pdf me-2"></i>Pratinjau PDF
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 h-100">
                        <iframe src="" data-src="{{ $previewUrl }}" width="100%" height="100%" style="border:none;"></iframe>
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

        @once
        @push('scripts')
        <script>
            // Lazy load PDF iframes — identical to validasi-izin pattern
            document.addEventListener('DOMContentLoaded', function() {
                var modals = document.querySelectorAll('.modal');
                modals.forEach(function(modal) {
                    modal.addEventListener('shown.bs.modal', function() {
                        var iframe = modal.querySelector('iframe');
                        if (iframe && !iframe.getAttribute('src')) {
                            iframe.setAttribute('src', iframe.getAttribute('data-src'));
                        }
                    });
                });
            });
        </script>
        @endpush
        @endonce

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
