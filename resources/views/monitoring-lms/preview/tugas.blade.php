@php
    $tugas = $konten;
    $previewTitle = $tugas->judul_tugas ?? '-';
    $kontenLabel = 'Tugas';
    $extension = $tugas->file_tugas ? strtolower(pathinfo($tugas->file_tugas, PATHINFO_EXTENSION)) : null;
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
    $filePreviewUrl = preview_url($tugas->file_tugas);
@endphp

@extends('monitoring-lms.preview.wrapper', compact('previewTitle', 'kontenLabel'))

@section('preview-content')
    <header class="border-b border-slate-200 pb-5">
        <h1 class="flex items-start gap-3 text-xl font-extrabold text-slate-950 sm:text-2xl"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fas fa-tasks" aria-hidden="true"></i></span><span class="pt-1">{{ $tugas->judul_tugas }}</span></h1>
        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600"><span class="rounded-lg bg-amber-50 px-3 py-2 font-bold text-amber-700">Tugas</span>
            @foreach([[optional($tugas->guru)->nama_lengkap, 'fa-user-tie'], [optional($tugas->mataPelajaran)->nama_mapel, 'fa-book'], [optional($tugas->kelas)->nama_kelas, 'fa-school'], [$tugas->tanggal_mulai?->locale('id')->translatedFormat('d M Y'), 'fa-play-circle'], [$tugas->tanggal_deadline?->locale('id')->translatedFormat('d M Y'), 'fa-flag-checkered']] as [$value, $icon])
                @if($value)<span class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2"><i class="fas {{ $icon }} text-slate-400" aria-hidden="true"></i>{{ $value }}</span>@endif
            @endforeach
        </div>
    </header>
    <div class="mt-5 grid gap-4">
        @foreach([['Bab', $tugas->judul_bab], ['Materi terkait', $tugas->nama_materi]] as [$label, $value]) @if($value)<section class="rounded-2xl border border-slate-200 p-4"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</h2><p class="mt-2 text-sm font-semibold text-slate-900">{{ $value }}</p></section>@endif @endforeach
        @if($tugas->deskripsi)<section class="rounded-2xl border border-slate-200 p-4"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Instruksi tugas</h2><p class="mt-2 text-sm leading-7 text-slate-700">{!! nl2br(e($tugas->deskripsi)) !!}</p></section>@endif
        @if($tugas->file_tugas)
            <section class="min-w-0 rounded-2xl border border-slate-200 p-4 sm:p-5"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Berkas tugas</h2><div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                @if($isImage)<img src="{{ $filePreviewUrl }}" alt="{{ $tugas->judul_tugas }}" data-expandable-image class="mx-auto max-h-[32rem] w-auto max-w-full cursor-zoom-in object-contain">
                @elseif($isPdf)<iframe src="{{ $filePreviewUrl }}" class="h-[65vh] min-h-[420px] w-full bg-white" title="Pratinjau PDF tugas"></iframe>
                @elseif($isVideo)<video controls class="mx-auto max-h-[32rem] w-full bg-slate-950"><source src="{{ $filePreviewUrl }}" type="video/{{ $extension }}">Browser Anda tidak mendukung video.</video>
                @else<div class="flex items-center gap-3 p-5"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fas fa-file-alt" aria-hidden="true"></i></span><p class="min-w-0 break-all text-sm font-semibold text-slate-800">{{ basename($tugas->file_tugas) }} ({{ strtoupper($extension) }})</p></div>@endif
            </div><div class="mt-4 flex flex-wrap gap-2"><a href="{{ $filePreviewUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-external-link-alt" aria-hidden="true"></i>Buka di tab baru</a><a href="{{ asset('storage/'.$tugas->file_tugas) }}" download class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 text-xs font-bold text-brand-700 no-underline hover:bg-brand-50"><i class="fas fa-download" aria-hidden="true"></i>Unduh</a></div></section>
        @endif
        <section class="rounded-2xl border border-slate-200 p-4 sm:p-5"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Pengaturan tugas</h2><div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([['fa-eye', 'Tampilkan nilai', ($tugas->tampilkan_nilai ?? false) ? 'Ya' : 'Tidak'], ['fa-redo', 'Bisa diulang', ($tugas->bisa_diulang ?? false) ? 'Ya' : 'Tidak']] as [$icon, $label, $value])<div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3"><i class="fas {{ $icon }} text-brand-600" aria-hidden="true"></i><p class="text-xs text-slate-500">{{ $label }}<strong class="mt-0.5 block text-sm text-slate-900">{{ $value }}</strong></p></div>@endforeach
            @if($tugas->bisa_diulang ?? false)<div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3"><i class="fas fa-list-ol text-brand-600" aria-hidden="true"></i><p class="text-xs text-slate-500">Batas pengulangan<strong class="mt-0.5 block text-sm text-slate-900">{{ $tugas->batas_pengulangan ?? 0 }}</strong></p></div>@endif
        </div></section>
    </div>
@endsection
