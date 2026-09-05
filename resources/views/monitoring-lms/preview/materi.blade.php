@php
    $materi = $konten;
    $previewTitle = $materi->judul_materi ?? '-';
    $kontenLabel = 'Materi';
    $extension = $materi->file_materi ? strtolower(pathinfo($materi->file_materi, PATHINFO_EXTENSION)) : null;
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
    $filePreviewUrl = preview_url($materi->file_materi);
@endphp

@extends('monitoring-lms.preview.wrapper', compact('previewTitle', 'kontenLabel'))

@section('preview-content')
    <header class="border-b border-slate-200 pb-5">
        <h1 class="flex items-start gap-3 text-xl font-extrabold text-slate-950 sm:text-2xl"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-book-open" aria-hidden="true"></i></span><span class="pt-1">{{ $materi->judul_materi }}</span></h1>
        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600">
            @foreach([[optional($materi->guru)->nama_lengkap, 'fa-user-tie'], [optional($materi->mataPelajaran)->nama_mapel, 'fa-book'], [optional($materi->kelas)->nama_kelas, 'fa-school'], [$materi->tanggal_upload?->locale('id')->translatedFormat('d F Y'), 'fa-calendar'], [$materi->tipe_file ? strtoupper($materi->tipe_file) : null, 'fa-file']] as [$value, $icon])
                @if($value)<span class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2"><i class="fas {{ $icon }} text-slate-400" aria-hidden="true"></i>{{ $value }}</span>@endif
            @endforeach
        </div>
    </header>
    <div class="mt-5 grid gap-4">
        @if($materi->kategori)<section class="rounded-2xl border border-slate-200 p-4"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Kategori</h2><p class="mt-2 text-sm font-semibold text-slate-900">{{ $materi->kategori === 'modul_ajar' ? 'Modul Ajar' : 'Materi' }}</p></section>@endif
        @if($materi->deskripsi)<section class="rounded-2xl border border-slate-200 p-4"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Deskripsi</h2><p class="mt-2 text-sm leading-7 text-slate-700">{!! nl2br(e($materi->deskripsi)) !!}</p></section>@endif
        @if($materi->file_materi)
            <section class="min-w-0 rounded-2xl border border-slate-200 p-4 sm:p-5"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Berkas materi</h2><div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                @if($isImage)<img src="{{ $filePreviewUrl }}" alt="{{ $materi->judul_materi }}" data-expandable-image class="mx-auto max-h-[32rem] w-auto max-w-full cursor-zoom-in object-contain">
                @elseif($isPdf)<iframe src="{{ $filePreviewUrl }}" class="h-[65vh] min-h-[420px] w-full bg-white" title="Pratinjau PDF materi"></iframe>
                @elseif($isVideo)<video controls class="mx-auto max-h-[32rem] w-full bg-slate-950"><source src="{{ $filePreviewUrl }}" type="video/{{ $extension }}">Browser Anda tidak mendukung video.</video>
                @else<div class="flex items-center gap-3 p-5"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-file-alt" aria-hidden="true"></i></span><p class="min-w-0 break-all text-sm font-semibold text-slate-800">{{ basename($materi->file_materi) }} ({{ strtoupper($extension) }})</p></div>@endif
            </div><div class="mt-4 flex flex-wrap gap-2"><a href="{{ $filePreviewUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-external-link-alt" aria-hidden="true"></i>Buka di tab baru</a><a href="{{ asset('storage/'.$materi->file_materi) }}" download class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 text-xs font-bold text-brand-700 no-underline hover:bg-brand-50"><i class="fas fa-download" aria-hidden="true"></i>Unduh</a></div></section>
        @elseif($materi->url_materi)
            <section class="rounded-2xl border border-slate-200 p-4"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Tautan materi eksternal</h2><div class="mt-3 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center"><i class="fas fa-link text-brand-600" aria-hidden="true"></i><p class="min-w-0 flex-1 break-all text-sm text-slate-700">{{ $materi->url_materi }}</p><a href="{{ $materi->url_materi }}" target="_blank" rel="noopener" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-external-link-alt" aria-hidden="true"></i>Buka tautan</a></div></section>
        @else
            <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"><i class="fas fa-exclamation-triangle mt-0.5" aria-hidden="true"></i>Belum ada berkas atau tautan materi yang diunggah.</div>
        @endif
    </div>
@endsection
