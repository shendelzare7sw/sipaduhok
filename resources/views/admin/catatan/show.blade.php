@extends('layouts.app')

@section('title', 'Detail Catatan')
@section('page-title', 'Detail Catatan')
@section('page-subtitle', 'Isi, penerima, dan status baca catatan')

@section('content')
@php
    $routePrefix = request()->routeIs('ketua.*') ? 'ketua' : 'admin';
    $sentAt = $catatan->tanggal_kirim ? \Carbon\Carbon::parse($catatan->tanggal_kirim) : $catatan->created_at;
    $priority = $catatan->prioritas ?: 'biasa';
    $readers = $catatan->relationLoaded('pembaca') ? $catatan->pembaca : collect();
    $readerCount = $catatan->relationLoaded('pembaca') ? $readers->count() : $catatan->totalPembaca();
    $priorityMeta = [
        'biasa' => ['Biasa', 'fa-file-lines', 'bg-blue-50 text-blue-700 ring-blue-200'],
        'penting' => ['Penting', 'fa-triangle-exclamation', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'mendesak' => ['Mendesak', 'fa-circle-exclamation', 'bg-red-50 text-red-700 ring-red-200'],
    ][$priority] ?? ['Biasa', 'fa-file-lines', 'bg-blue-50 text-blue-700 ring-blue-200'];
    $recipient = match($catatan->tipe_penerima) {
        'semua' => ['Semua pengguna', 'fa-users', 'bg-blue-50 text-blue-700 ring-blue-200'],
        'role' => [ucwords(str_replace('_', ' ', $catatan->role_penerima)), 'fa-user-tag', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        default => [$catatan->penerima->name ?? 'Individu', 'fa-user', 'bg-violet-50 text-violet-700 ring-violet-200'],
    };
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route($routePrefix . '.catatan.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-bold text-slate-700 no-underline transition hover:border-brand-300 hover:text-brand-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a>
        <form action="{{ route($routePrefix . '.catatan.destroy', $catatan->id) }}" method="POST" data-confirm data-confirm-title="Hapus catatan ini?" data-confirm-message="Riwayat catatan akan dihapus dan tidak dapat dipulihkan." data-confirm-text="Ya, hapus">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-red-50 px-3.5 text-sm font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button>
        </form>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-5 sm:px-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Catatan terkirim</p>
                    <h2 class="mt-1 break-words text-xl font-extrabold leading-tight text-slate-950 sm:text-2xl">{{ $catatan->judul }}</h2>
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-500">
                        <span><i class="fas fa-user mr-1.5 text-slate-400" aria-hidden="true"></i>{{ $catatan->pengirim->name ?? '-' }}</span>
                        <span><i class="far fa-clock mr-1.5 text-slate-400" aria-hidden="true"></i>{{ $sentAt?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1 {{ $priorityMeta[2] }}"><i class="fas {{ $priorityMeta[1] }}" aria-hidden="true"></i>{{ $priorityMeta[0] }}</span>
                    <span class="inline-flex min-w-0 max-w-full items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1 {{ $recipient[2] }}"><i class="fas {{ $recipient[1] }}" aria-hidden="true"></i><span class="truncate">{{ $recipient[0] }}</span></span>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6">
            <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-align-left text-brand-600" aria-hidden="true"></i>Isi catatan</h3>
            <div class="mt-3 whitespace-pre-wrap break-words rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-700 sm:p-5">{{ $catatan->isi_catatan }}</div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-eye" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">Status pembaca</h3><p class="mt-0.5 text-xs text-slate-500">Pengguna yang sudah membuka catatan.</p></div></div>
            <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">{{ number_format($readerCount) }} dibaca</span>
        </header>

        @if($readers->isNotEmpty())
            <div class="grid gap-2 p-4 sm:grid-cols-2 xl:grid-cols-3 sm:p-5">
                @foreach($readers as $reader)
                    <article class="flex min-w-0 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-violet-500 text-sm font-extrabold text-white">{{ strtoupper(substr($reader->name, 0, 1)) }}</span>
                        <span class="min-w-0 flex-1"><span class="block truncate text-sm font-bold text-slate-900">{{ $reader->name }}</span><span class="mt-0.5 block text-xs text-slate-500">{{ $reader->pivot->dibaca_pada ? \Carbon\Carbon::parse($reader->pivot->dibaca_pada)->locale('id')->diffForHumans() : 'Waktu tidak tersedia' }}</span></span>
                        <i class="fas fa-circle-check shrink-0 text-emerald-500" aria-label="Sudah dibaca"></i>
                    </article>
                @endforeach
            </div>
        @else
            <div class="px-5 py-12 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-eye-slash" aria-hidden="true"></i></span><h4 class="mt-4 font-extrabold text-slate-800">Belum ada pembaca</h4><p class="mt-1 text-sm text-slate-500">Status diperbarui ketika penerima membuka catatan.</p></div>
        @endif
    </section>
</div>
@endsection
