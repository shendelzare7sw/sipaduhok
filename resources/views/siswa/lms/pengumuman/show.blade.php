@extends('layouts.lms')

@section('title', 'Detail Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Detail pengumuman')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $prioritasInfo = [
        'mendesak' => ['Mendesak', 'bg-rose-50 text-rose-700 ring-rose-200'],
        'penting' => ['Penting', 'bg-amber-50 text-amber-800 ring-amber-200'],
        'biasa' => ['Informasi', 'bg-indigo-50 text-indigo-700 ring-indigo-200'],
    ];
    [$labelPrioritas, $warnaPrioritas] = $prioritasInfo[$pengumuman->prioritas] ?? $prioritasInfo['biasa'];
@endphp

<div class="min-w-0 w-full space-y-4">
    <a href="{{ route('siswa.lms.pengumuman.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-1 text-sm font-bold text-slate-600 no-underline transition hover:text-indigo-700">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar pengumuman
    </a>

    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-100 p-4 sm:p-6">
            <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $warnaPrioritas }}">{{ $labelPrioritas }}</span>
            <h2 class="mt-2 text-lg font-extrabold leading-snug text-slate-900 sm:text-2xl">{{ $pengumuman->judul }}</h2>
            <div class="mt-4 flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white"><i class="fa-solid fa-school" aria-hidden="true"></i></span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-slate-900">SIPADUHOK · Pengumuman Sekolah</p>
                    <p class="text-xs text-slate-500">
                        {{ \Carbon\Carbon::parse($pengumuman->tanggal_pengumuman)->translatedFormat('d F Y') }}
                        · {{ $pengumuman->created_at->copy()->locale('id')->diffForHumans() }}
                    </p>
                </div>
            </div>
        </header>

        <div class="space-y-5 p-4 sm:p-6">
            <div class="whitespace-pre-line break-words text-sm leading-7 text-slate-700 sm:text-[15px]">{{ $pengumuman->isi_pengumuman }}</div>

            @if($pengumuman->lampiran_surat)
                @php
                    $extension = strtolower(pathinfo($pengumuman->lampiran_surat, PATHINFO_EXTENSION));
                    $isPdf = $extension === 'pdf';
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $previewUrl = preview_url($pengumuman->lampiran_surat);
                    $downloadUrl = asset('storage/' . $pengumuman->lampiran_surat);
                @endphp
                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-3 sm:p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-extrabold text-slate-800"><i class="fa-solid fa-paperclip text-indigo-500" aria-hidden="true"></i>1 lampiran</h3>

                    @if($isPdf)
                        <iframe src="{{ $previewUrl }}" title="Pratinjau lampiran" class="h-[60vh] min-h-80 w-full rounded-xl border border-slate-200 bg-white"></iframe>
                    @elseif($isImage)
                        <img src="{{ $downloadUrl }}" alt="Lampiran pengumuman" class="mx-auto max-h-[70vh] max-w-full rounded-xl border border-slate-200 bg-white object-contain">
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-8 text-center text-sm text-slate-500">
                            <i class="fa-solid fa-file mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>
                            Pratinjau tidak tersedia untuk tipe file ini.
                        </div>
                    @endif

                    <div class="mt-3 grid grid-cols-1 gap-2 sm:flex">
                        <a href="{{ $downloadUrl }}" download class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white no-underline transition hover:bg-indigo-700"><i class="fa-solid fa-download" aria-hidden="true"></i>Unduh</a>
                        @if($isPdf || $isImage)
                            <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline transition hover:bg-slate-50"><i class="fa-solid fa-up-right-from-square" aria-hidden="true"></i>Buka di tab baru</a>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </article>
</div>
@endsection
