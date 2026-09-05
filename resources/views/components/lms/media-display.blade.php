@props(['file'])

@php
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $url = Storage::url($file);
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
    $isPdf = $extension === 'pdf';
    $isOffice = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
@endphp

<div class="mt-3 min-w-0">
    @if($isImage)
        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="block w-fit max-w-full">
            <img src="{{ $url }}" alt="Lampiran" class="max-h-[400px] max-w-full rounded-xl border border-slate-200 object-contain">
        </a>
    @elseif($isVideo)
        <video controls class="max-h-[400px] w-full rounded-xl border border-slate-200 bg-black">
            <source src="{{ $url }}" type="video/{{ $extension }}">
            Browser Anda tidak mendukung pemutaran video.
        </video>
    @elseif($isPdf)
        <div class="aspect-video overflow-hidden rounded-xl border border-slate-200 bg-white">
            <iframe src="{{ $url }}" allowfullscreen class="h-full w-full border-0"></iframe>
        </div>
        <div class="mt-1">
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200">
                <i class="fas fa-external-link-alt" aria-hidden="true"></i> Buka Layar Penuh
            </a>
        </div>
    @elseif($isOffice)
        <div class="aspect-video overflow-hidden rounded-xl border border-slate-200 bg-white">
            <iframe
                src="https://docs.google.com/gview?url={{ urlencode(asset('storage/' . $file)) }}&embedded=true" class="h-full w-full border-0"></iframe>
        </div>
        <div class="mt-1">
            <a href="{{ $url }}" download class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200">
                <i class="fas fa-download" aria-hidden="true"></i> Unduh Dokumen
            </a>
        </div>
    @else
        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200">
            <i class="fas fa-paperclip" aria-hidden="true"></i> Unduh {{ strtoupper($extension) }}
        </a>
    @endif
</div>
