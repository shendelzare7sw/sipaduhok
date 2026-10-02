@extends('layouts.lms-guru')

@php
    $isLatihan = request()->routeIs('guru.lms.latihan.*');
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $args = [$kelas->id, $mapel->id, $ujian->id];
    $tipeTone = [
        'pilihan_ganda' => 'bg-indigo-50 text-indigo-700',
        'pilihan_ganda_kompleks' => 'bg-cyan-50 text-cyan-700',
        'benar_salah' => 'bg-amber-50 text-amber-700',
        'isian_singkat' => 'bg-emerald-50 text-emerald-700',
        'uraian' => 'bg-slate-100 text-slate-700',
    ];
@endphp

@section('title', 'Kelola Soal ' . ($isLatihan ? 'Latihan' : 'Ujian'))
@section('page-title', 'Kelola Soal: ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route($prefix.'.index', [$kelas->id, $mapel->id]) }}" class="inline-flex min-h-10 w-fit items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar {{ $isLatihan ? 'latihan' : 'ujian' }}</a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route($prefix.'.soal.manage', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>Editor soal lengkap</a>
            <a href="{{ route($prefix.'.soal.create', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-indigo-700"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Tambah soal</a>
        </div>
    </div>

    @if($soalList->count() > 0)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-list-ol text-indigo-600" aria-hidden="true"></i>Daftar soal</h2>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-extrabold text-indigo-700">{{ $soalList->count() }} soal</span>
            </header>
            <div class="divide-y divide-slate-100">
                @foreach($soalList as $soal)
                    <article class="flex min-w-0 items-start gap-3 px-4 py-3 sm:items-center sm:px-5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-extrabold text-slate-700">{{ $soal->urutan }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="line-clamp-2 text-sm text-slate-800 sm:truncate">{{ strip_tags($soal->pertanyaan) }}</p>
                            <p class="mt-1 flex flex-wrap items-center gap-2 text-[11px]">
                                <span class="rounded-full px-2 py-0.5 font-bold {{ $tipeTone[$soal->tipe_soal] ?? 'bg-slate-100 text-slate-600' }}">{{ ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}</span>
                                <span class="text-slate-500">Bobot {{ $soal->bobot_nilai }}</span>
                            </p>
                        </div>
                        <div class="flex shrink-0 gap-1.5">
                            <x-cleanflow.table-action :href="route($prefix.'.soal.edit', [...$args, $soal->id])" tone="edit" icon="fa-solid fa-pen-to-square" label="Edit soal {{ $soal->urutan }}" />
                            <form method="POST" action="{{ route($prefix.'.soal.destroy', [...$args, $soal->id]) }}" data-confirm-title="Hapus soal {{ $soal->urutan }}?" data-confirm-message="Soal ini akan dihapus permanen." data-confirm-text="Ya, hapus">
                                @csrf
                                @method('DELETE')
                                <x-cleanflow.table-action type="submit" tone="delete" icon="fa-solid fa-trash" label="Hapus soal {{ $soal->urutan }}" />
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></span>
            <h3 class="mt-4 font-extrabold text-slate-900">Belum ada soal</h3>
            <p class="mt-1 text-sm text-slate-500">Mulai tambahkan soal untuk {{ $isLatihan ? 'latihan' : 'ujian' }} ini.</p>
            <a href="{{ route($prefix.'.soal.create', $args) }}" class="mt-4 inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline hover:bg-indigo-700"><i class="fa-solid fa-plus" aria-hidden="true"></i>Buat soal pertama</a>
        </section>
    @endif
</div>
@endsection
