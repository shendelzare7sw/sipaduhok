{{--
    Lampiran forum diskusi LMS (Guru & Siswa).
    Gambar dibuka di galeri lightbox halaman yang sama (dikelompokkan per $group), video diputar langsung. PDF dan dokumen lain tampil sebagai kartu file
    (tanpa iframe, agar tidak memicu unduhan otomatis oleh pengelola unduhan seperti IDM);
    PDF dibuka di tab baru lewat preview_url(), dokumen Office lewat Google Docs Viewer di tab baru.
--}}
@props(['file', 'group' => 'lampiran'])

@php
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $url = Storage::url($file);
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $videoMime = ['mp4' => 'video/mp4', 'm4v' => 'video/mp4', 'mov' => 'video/quicktime', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg'];
    $isVideo = isset($videoMime[$extension]);
    $isPdf = $extension === 'pdf';
    $isOffice = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
    [$label, $icon, $tone] = match (true) {
        $isPdf => ['Dokumen PDF', 'fa-file-pdf', 'bg-rose-50 text-rose-600'],
        in_array($extension, ['doc', 'docx']) => ['Dokumen Word', 'fa-file-word', 'bg-blue-50 text-blue-600'],
        in_array($extension, ['xls', 'xlsx']) => ['Lembar Excel', 'fa-file-excel', 'bg-emerald-50 text-emerald-600'],
        in_array($extension, ['ppt', 'pptx']) => ['Presentasi PowerPoint', 'fa-file-powerpoint', 'bg-orange-50 text-orange-600'],
        default => ['Berkas '.strtoupper($extension), 'fa-file-lines', 'bg-slate-100 text-slate-600'],
    };
    // PDF dibuka lewat /view-document/{token} (tanpa ekstensi .pdf, inline, terikat pengguna login):
    // pola yang sama dengan pratinjau bukti presensi agar tidak disadap pengelola unduhan seperti IDM.
    $bukaUrl = $isPdf
        ? preview_url($file)
        : ($isOffice ? 'https://docs.google.com/gview?url='.urlencode(asset('storage/'.$file)) : null);
@endphp

{{-- Gambar = thumbnail persegi (berjajar dalam wadah flex-wrap), lainnya selebar konten. --}}
<div x-data class="min-w-0 {{ $isImage ? 'w-28 sm:w-40' : 'w-full' }}">
    @if($isImage)
        <button type="button" x-on:click="$store.lightbox.buka($el.querySelector('img'))" class="group relative block aspect-square w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50" aria-label="Perbesar gambar">
            <img src="{{ $url }}" alt="Lampiran gambar" loading="lazy" data-lightbox="{{ $group }}"
                 class="h-full w-full cursor-zoom-in object-cover transition duration-200 group-hover:scale-105">
            <span class="pointer-events-none absolute bottom-2 right-2 flex h-8 w-8 items-center justify-center rounded-full bg-slate-950/60 text-xs text-white opacity-80 transition group-hover:opacity-100"><i class="fa-solid fa-expand" aria-hidden="true"></i></span>
        </button>
    @elseif($isVideo)
        <video controls playsinline preload="metadata" class="max-h-[420px] w-full max-w-2xl rounded-xl border border-slate-200 bg-black">
            <source src="{{ $url }}" type="{{ $videoMime[$extension] }}">
            Browser Anda tidak mendukung pemutaran video.
        </video>
    @else
        <div class="flex w-full max-w-md min-w-0 items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-lg {{ $tone }}"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-xs font-bold text-slate-800" title="{{ basename($file) }}">{{ $label }}</span>
                <span class="block truncate text-[11px] text-slate-500">{{ basename($file) }}</span>
            </span>
            <span class="flex shrink-0 gap-1.5">
                @if($bukaUrl)
                    <a href="{{ $bukaUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-indigo-50 px-2.5 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100" title="Buka di tab baru"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i><span class="hidden sm:inline">Buka</span></a>
                @endif
                <a href="{{ $url }}" download class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200" title="Unduh berkas"><i class="fa-solid fa-download" aria-hidden="true"></i><span class="hidden sm:inline">Unduh</span></a>
            </span>
        </div>
    @endif
</div>
