@extends('layouts.app')

@section('title', 'Detail Kegiatan')
@section('page-title', 'Detail Kegiatan')
@section('page-subtitle', 'Kalender Akademik')

@section('content')
@php
    $routeBase = request()->routeIs('sekretaris.*') ? 'sekretaris' : 'admin.akademik';
    $statusTone = match($kalender->status) { 'aktif' => 'bg-emerald-50 text-emerald-700', 'draft' => 'bg-amber-50 text-amber-700', default => 'bg-slate-100 text-slate-600' };
    $isVisible = ! $kalender->is_hidden_siswa;
@endphp

<div class="min-w-0 w-full space-y-5" x-data="{
    visible: {{ $isVisible ? 'true' : 'false' }},
    busy: false,
    async toggleVisibility() {
        if (this.busy) return;
        const previous = this.visible;
        this.visible = !this.visible;
        this.busy = true;
        try {
            const response = await fetch(@js(route($routeBase . '.kalender.toggle-visibility', $kalender->id)), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Visibilitas gagal diperbarui.');
            this.visible = !data.is_hidden;
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2200 });
        } catch (error) {
            this.visible = previous;
            Swal.fire({ title: 'Tidak dapat memproses', text: error.message, icon: 'error' });
        } finally {
            this.busy = false;
        }
    }
}">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-brand-800 to-brand-600 text-white shadow-sm">
        <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between"><div class="min-w-0"><p class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-brand-100">Agenda Akademik</p><h2 class="mt-2 break-words text-xl font-extrabold !text-white sm:text-2xl">{{ $kalender->nama_kegiatan }}</h2><div class="mt-3 flex flex-wrap gap-2"><span class="rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold ring-1 ring-inset ring-white/20">{{ $kalender->jenis_label ?? $kalender->jenis_kegiatan }}</span><span class="rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold ring-1 ring-inset ring-white/20">{{ ucfirst($kalender->status) }}</span></div></div><div class="grid grid-cols-3 gap-2"><a href="{{ route($routeBase . '.kalender.edit', $kalender->id) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-white px-3 text-xs font-bold text-brand-700 no-underline hover:bg-brand-50"><i class="fas fa-edit" aria-hidden="true"></i><span class="hidden sm:inline">Edit</span></a><form action="{{ route($routeBase . '.kalender.destroy', $kalender->id) }}" method="POST" data-confirm data-confirm-title="Hapus kegiatan?" data-confirm-message="{{ $kalender->nama_kegiatan }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-3 text-xs font-bold text-white hover:bg-red-600"><i class="fas fa-trash" aria-hidden="true"></i><span class="hidden sm:inline">Hapus</span></button></form><a href="{{ route($routeBase . '.kalender.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-white/10 px-3 text-xs font-bold text-white no-underline ring-1 ring-inset ring-white/25 hover:bg-white/20"><i class="fas fa-arrow-left" aria-hidden="true"></i><span class="hidden sm:inline">Kembali</span></a></div></div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><div class="flex items-center gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-calendar-days" aria-hidden="true"></i></span><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tanggal kegiatan</p><p class="mt-1 text-sm font-extrabold text-slate-900">{{ $kalender->tanggal_mulai->translatedFormat('d F Y') }}</p>@if($kalender->tanggal_selesai && !$kalender->tanggal_selesai->isSameDay($kalender->tanggal_mulai))<p class="mt-0.5 text-xs text-slate-500">sampai {{ $kalender->tanggal_selesai->translatedFormat('d F Y') }}</p>@endif</div></div></article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><div class="flex items-center gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-clock" aria-hidden="true"></i></span><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Jam kegiatan</p><p class="mt-1 text-sm font-extrabold text-slate-900">{{ $kalender->waktu_mulai ? substr($kalender->waktu_mulai, 0, 5) : 'Sepanjang hari' }}@if($kalender->waktu_selesai)–{{ substr($kalender->waktu_selesai, 0, 5) }}@endif</p><p class="mt-0.5 text-xs text-slate-500">{{ $kalender->waktu_mulai ? 'Waktu lokal sekolah' : 'Tidak ada jam khusus' }}</p></div></div></article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 md:col-span-2 xl:col-span-1"><div class="flex items-center justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Visibilitas siswa</p><p class="mt-1 text-sm font-extrabold text-slate-900" x-text="visible ? 'Ditampilkan' : 'Disembunyikan'"></p><p class="mt-0.5 text-xs text-slate-500">Ubah tanpa meninggalkan halaman.</p></div><button type="button" @click="toggleVisibility()" :disabled="busy" class="relative h-7 w-12 shrink-0 rounded-full transition disabled:opacity-50" :class="visible ? 'bg-emerald-500' : 'bg-slate-300'" :aria-label="visible ? 'Sembunyikan dari siswa' : 'Tampilkan kepada siswa'"><span class="absolute top-1 h-5 w-5 rounded-full bg-white shadow transition" :class="visible ? 'left-6' : 'left-1'"></span></button></div></article>
    </section>

    <section class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><header class="border-b border-slate-200 p-4 sm:p-5"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-align-left text-brand-600" aria-hidden="true"></i>Keterangan kegiatan</h3></header><div class="p-4 sm:p-5">@if($kalender->keterangan)<p class="whitespace-pre-line break-words text-sm leading-6 text-slate-700">{{ $kalender->keterangan }}</p>@else<p class="text-sm text-slate-500">Tidak ada keterangan tambahan.</p>@endif @if($kalender->lampiran_surat)<a href="{{ asset('storage/' . $kalender->lampiran_surat) }}" target="_blank" class="mt-5 inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-50 px-4 text-xs font-bold text-brand-700 no-underline hover:bg-brand-100"><i class="fas fa-file-pdf" aria-hidden="true"></i>Lihat dokumen PDF</a>@endif</div></article>
        <aside class="rounded-2xl border border-slate-200 bg-white shadow-sm"><header class="border-b border-slate-200 p-4 sm:p-5"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-circle-info text-brand-600" aria-hidden="true"></i>Informasi data</h3></header><dl class="divide-y divide-slate-100 text-xs"><div class="p-4"><dt class="font-bold uppercase tracking-wide text-slate-400">Status</dt><dd class="mt-2"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTone }}">{{ ucfirst($kalender->status) }}</span></dd></div><div class="grid grid-cols-2 gap-3 p-4"><div><dt class="font-bold uppercase tracking-wide text-slate-400">Dibuat oleh</dt><dd class="mt-1 break-words font-semibold text-slate-700">{{ $kalender->creator->name ?? 'Sistem' }}</dd></div><div><dt class="font-bold uppercase tracking-wide text-slate-400">Dibuat</dt><dd class="mt-1 font-semibold text-slate-700">{{ $kalender->created_at->translatedFormat('d M Y H:i') }}</dd></div></div>@if($kalender->updated_at != $kalender->created_at)<div class="grid grid-cols-2 gap-3 p-4"><div><dt class="font-bold uppercase tracking-wide text-slate-400">Diperbarui oleh</dt><dd class="mt-1 break-words font-semibold text-slate-700">{{ $kalender->updater->name ?? 'Sistem' }}</dd></div><div><dt class="font-bold uppercase tracking-wide text-slate-400">Terakhir diubah</dt><dd class="mt-1 font-semibold text-slate-700">{{ $kalender->updated_at->translatedFormat('d M Y H:i') }}</dd></div></div>@endif</dl></aside>
    </section>
</div>
@endsection
