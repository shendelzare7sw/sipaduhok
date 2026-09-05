@props([
    'path' => null,
    'title' => 'File',
    'class' => null,
    'label' => null,
    'iconClass' => 'fas fa-eye',
])

@if($path)
    @php
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
        $isPdf = $extension === 'pdf';
        $downloadUrl = asset('storage/' . $path);
        $previewUrl = preview_url($path);
        $dialogId = 'file-preview-' . md5($path . uniqid());
        $defaultButton = $isPdf
            ? 'inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-red-50 px-3 text-xs font-bold text-red-700 hover:bg-red-100'
            : 'inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-blue-50 px-3 text-xs font-bold text-blue-700 hover:bg-blue-100';
    @endphp

    <span class="inline-flex flex-wrap items-center gap-2">
        @if($isImage || $isPdf)
            <button type="button" class="{{ $class ?? $defaultButton }}" data-dialog-open="{{ $dialogId }}">
                <i class="{{ $iconClass }}" aria-hidden="true"></i>
                {{ $label ?? ($isPdf ? 'Lihat PDF' : 'Lihat Gambar') }}
            </button>

            <a href="{{ $downloadUrl }}" download class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" title="Unduh {{ $title }}" aria-label="Unduh {{ $title }}">
                <i class="fas fa-download" aria-hidden="true"></i>
            </a>

            @push('modals')
                <dialog id="{{ $dialogId }}" class="m-auto w-[calc(100%-1.5rem)] max-w-5xl overflow-hidden rounded-2xl border-0 bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-slate-950/60 backdrop:backdrop-blur-sm">
                    <div class="flex max-h-[92vh] min-h-0 flex-col">
                        <header class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
                            <h2 class="min-w-0 truncate text-sm font-extrabold text-slate-900 sm:text-base">
                                <i class="fas {{ $isPdf ? 'fa-file-pdf text-red-600' : 'fa-image text-blue-600' }} mr-2" aria-hidden="true"></i>
                                {{ $title ?: ($isPdf ? 'Pratinjau PDF' : 'Pratinjau Gambar') }}
                            </h2>
                            <button type="button" data-dialog-close class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200" aria-label="Tutup pratinjau">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </button>
                        </header>

                        <div class="min-h-0 flex-1 overflow-auto bg-slate-100 p-2 sm:p-4">
                            @if($isImage)
                                <img src="{{ $downloadUrl }}" alt="Pratinjau {{ $title }}" class="mx-auto max-h-[72vh] max-w-full rounded-xl object-contain shadow-sm">
                            @else
                                <iframe src="" data-src="{{ $previewUrl }}" title="Pratinjau {{ $title }}" class="h-[70vh] w-full rounded-xl border-0 bg-white"></iframe>
                            @endif
                        </div>

                        <footer class="flex shrink-0 flex-col-reverse gap-2 border-t border-slate-200 px-4 py-3 sm:flex-row sm:justify-end sm:px-5">
                            <button type="button" data-dialog-close class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 hover:bg-slate-200">Tutup</button>
                            <a href="{{ $downloadUrl }}" download class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700">
                                <i class="fas fa-download" aria-hidden="true"></i> Unduh {{ $isPdf ? 'PDF' : 'Gambar' }}
                            </a>
                        </footer>
                    </div>
                </dialog>
            @endpush
        @else
            <a href="{{ $downloadUrl }}" download class="{{ $class ?? 'inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200' }}">
                <i class="fas fa-download" aria-hidden="true"></i>
                {{ $label ?? 'Unduh File (' . strtoupper($extension) . ')' }}
            </a>
        @endif
    </span>
@endif
