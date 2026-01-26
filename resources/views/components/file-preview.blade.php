@php
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $isPreviewable = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf']);
    // URL for direct download (asset)
    $downloadUrl = asset('storage/' . $path);
    // URL for preview (via controller to force inline)
    $previewUrl = route('storage.preview', ['path' => $path]);
    
    // Determine icon based on extension
    $icon = 'fa-file';
    if ($extension == 'pdf') $icon = 'fa-file-pdf';
    elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = 'fa-file-image';
    elseif (in_array($extension, ['doc', 'docx'])) $icon = 'fa-file-word';
    elseif (in_array($extension, ['xls', 'xlsx'])) $icon = 'fa-file-excel';
    elseif (in_array($extension, ['ppt', 'pptx'])) $icon = 'fa-file-powerpoint';
@endphp

<div class="d-flex align-items-center gap-2 mb-2">
    @if($isPreviewable)
        <!-- Preview in New Tab (Browser Native) -->
        <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-info text-white">
            <i class="fas {{ $icon }} me-1"></i>Preview
        </a>
        
        <!-- Optional Download Button -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-outline-primary" title="Download File">
            <i class="fas fa-download"></i>
        </a>
    @else
        <!-- Direct Download for non-previewable files -->
        <a href="{{ $downloadUrl }}" download class="btn btn-sm btn-primary">
            <i class="fas {{ $icon }} me-1"></i>Download {{ strtoupper($extension) }}
        </a>
    @endif
</div>
