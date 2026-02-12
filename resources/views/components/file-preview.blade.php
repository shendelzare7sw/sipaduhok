@props(['path', 'label' => null, 'class' => null, 'icon' => null])
@php
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $isPdf = $extension === 'pdf';
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    
    // URL for direct download (asset)
    $downloadUrl = asset('storage/' . $path);
    // URL for preview (via controller)
    $previewUrl = route('storage.preview', ['path' => $path]);
    
    // Unique ID for this component instance
    $modalId = 'modal-' . md5($path . uniqid()); 
    
    // Determine icon
    $defaultIcon = 'fa-file';
    if ($isPdf) $defaultIcon = 'fa-file-pdf';
    elseif ($isImage) $defaultIcon = 'fa-file-image';
    elseif (in_array($extension, ['doc', 'docx'])) $defaultIcon = 'fa-file-word';
    elseif (in_array($extension, ['xls', 'xlsx'])) $defaultIcon = 'fa-file-excel';
    elseif (in_array($extension, ['ppt', 'pptx'])) $defaultIcon = 'fa-file-powerpoint';

    $finalIcon = $icon ?? $defaultIcon;
    // Handle icon class format (fasc/bx)
    $iconClass = str_starts_with($finalIcon, 'bx ') ? $finalIcon : "fas $finalIcon";
@endphp

<div class="d-flex align-items-center gap-2 mb-2">
    @if($isPdf)
        <!-- PDF Preview Modal Trigger -->
        <button type="button" 
                class="{{ $class ?? 'btn btn-sm btn-danger' }}" 
                data-bs-toggle="modal" 
                data-bs-target="#{{ $modalId }}"
                onclick="document.getElementById('iframe-{{ $modalId }}').src = '{{ $previewUrl }}'">
            <i class="{{ $iconClass }} me-1"></i>{{ $label ?? 'Lihat PDF' }}
        </button>
        
        <!-- Download Button -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-outline-primary" title="Download File">
            <i class="fas fa-download"></i>
        </a>

        <!-- PDF Modal -->
        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content" style="height: 90vh;">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-pdf me-2"></i>Preview PDF
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 h-100">
                        <iframe id="iframe-{{ $modalId }}" src="" width="100%" height="100%" style="border:none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ $downloadUrl }}" download class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download PDF
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    @elseif($isImage)
        <!-- Image Modal Trigger -->
        <button type="button" 
                class="{{ $class ?? 'btn btn-sm btn-info text-white' }}" 
                data-bs-toggle="modal" 
                data-bs-target="#{{ $modalId }}">
            <i class="{{ $iconClass }} me-1"></i>{{ $label ?? 'Lihat Gambar' }}
        </button>
        
        <!-- Download Button -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-outline-primary" title="Download File">
            <i class="fas fa-download"></i>
        </a>

        <!-- Image Modal -->
        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-image me-2"></i>Preview Gambar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center bg-light">
                        <img src="{{ $downloadUrl }}" alt="Preview" class="img-fluid" style="max-height: 80vh;">
                    </div>
                    <div class="modal-footer">
                        <a href="{{ $downloadUrl }}" download class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download Gambar
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Direct Download -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-primary">
            <i class="fas {{ $defaultIcon }} me-1"></i>Download {{ strtoupper($extension) }}
        </a>
    @endif
</div>
