@props(['file'])

@php
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $url = Storage::url($file);
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
    $isPdf = $extension === 'pdf';
    $isOffice = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
@endphp

<div class="media-attachment mt-3">
    @if($isImage)
        <a href="{{ $url }}" target="_blank">
            <img src="{{ $url }}" alt="Attachment" class="img-fluid rounded border" style="max-height: 400px;">
        </a>
    @elseif($isVideo)
        <video controls class="w-100 rounded border" style="max-height: 400px;">
            <source src="{{ $url }}" type="video/{{ $extension }}">
            Browser Anda tidak mendukung pemutaran video.
        </video>
    @elseif($isPdf)
        <div class="ratio ratio-16x9 border rounded">
            <iframe src="{{ $url }}" allowfullscreen></iframe>
        </div>
        <div class="mt-1">
            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-light border">
                <i class="fas fa-external-link-alt me-1"></i> Buka Fullscreen
            </a>
        </div>
    @elseif($isOffice)
        <div class="ratio ratio-16x9 border rounded">
            <iframe
                src="https://docs.google.com/gview?url={{ urlencode(asset('storage/' . $file)) }}&embedded=true"></iframe>
        </div>
        <div class="mt-1">
            <a href="{{ $url }}" download class="btn btn-sm btn-light border">
                <i class="fas fa-download me-1"></i> Download Dokumen
            </a>
        </div>
    @else
        <a href="{{ $url }}" target="_blank" class="btn btn-light border">
            <i class="fas fa-paperclip me-1"></i> Download {{ strtoupper($extension) }}
        </a>
    @endif
</div>