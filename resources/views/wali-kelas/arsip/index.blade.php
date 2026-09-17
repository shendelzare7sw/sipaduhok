@extends('layouts.app')

@section('title', 'Arsip Kelas Saya')
@section('page-title', 'Arsip Kelas Saya')
@section('page-subtitle', 'Riwayat kelas yang pernah Anda walikan')

@section('content')
<div class="min-w-0 w-full space-y-4">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h1 class="text-lg font-extrabold text-slate-900">Arsip kelas saya</h1><p class="mt-1 text-xs text-slate-500">Lihat data historis kelas lintas tahun ajaran. Arsip bersifat baca saja.</p></header>
    <section class="grid grid-cols-2 gap-3" aria-label="Ringkasan arsip"><div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Total kelas</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $totalKelas }}</p></div><div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Tahun ajaran</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $kelasGrouped->count() }}</p></div></section>
    @if($totalKelas === 0)
        <p class="rounded-xl border border-slate-200 bg-white px-4 py-9 text-center text-sm text-slate-500 shadow-sm">Anda belum pernah ditugaskan sebagai wali kelas.</p>
    @else
        @foreach($kelasGrouped as $taName => $kelasList)
            <section class="space-y-3"><div class="flex items-center gap-2"><h2 class="text-sm font-extrabold text-slate-900">Tahun ajaran {{ $taName }}</h2>@if($kelasList->first()?->tahunAjaran?->is_active)<span class="rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-800">Aktif</span>@endif</div>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($kelasList as $kelas)
                        <a href="{{ route('wali.arsip.show', $kelas->id) }}" class="block rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-300 hover:shadow-md"><div class="flex items-start justify-between gap-2"><div><h3 class="text-sm font-extrabold text-slate-900">{{ $kelas->nama_kelas }}</h3><p class="mt-1 text-xs text-slate-500">{{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? '—' }}</p></div><i class="fas fa-arrow-up-right-from-square text-xs text-sky-700" aria-hidden="true"></i></div><dl class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-3 text-center"><div><dt class="text-[11px] text-slate-500">Siswa</dt><dd class="text-sm font-extrabold text-slate-900">{{ $kelas->siswa_count }}</dd></div><div><dt class="text-[11px] text-slate-500">Rapor terbit</dt><dd class="text-sm font-extrabold text-emerald-700">{{ $kelas->rapor_terbit }}</dd></div><div><dt class="text-[11px] text-slate-500">Total rapor</dt><dd class="text-sm font-extrabold text-slate-900">{{ $kelas->rapor_total }}</dd></div></dl></a>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
@endsection
