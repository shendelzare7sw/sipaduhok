@extends('layouts.app')

@section('title', 'Permintaan Download Rapor')
@section('page-title', 'Permintaan Download Rapor')
@section('page-subtitle', 'Tinjau permintaan dari wali siswa')

@section('content')
<div class="min-w-0 w-full space-y-4" x-data="{ actionUrl: '', actionLabel: '', requester: '', approve: false }">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h1 class="text-lg font-extrabold text-slate-900">Permintaan download rapor</h1><p class="mt-1 text-xs text-slate-500">Wali siswa dapat mengunduh selama 24 jam setelah disetujui.{{ $kelas ? ' Kelas: '.$kelas->nama_kelas.'.' : '' }}</p></header>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="download-heading">
        <div class="border-b border-slate-100 px-4 py-3"><h2 id="download-heading" class="text-sm font-extrabold text-slate-900">Daftar permintaan</h2><p class="mt-0.5 text-xs text-slate-500">{{ $requests->total() }} permintaan ditemukan.</p></div>
        <div class="divide-y divide-slate-100">
            @forelse($requests as $req)
                @php
                    $label = match($req->status) { 'menunggu' => 'Menunggu', 'disetujui' => ($req->isExpired() ? 'Disetujui · kedaluwarsa' : 'Disetujui · aktif'), default => 'Ditolak' };
                    $tone = match($req->status) { 'menunggu' => 'bg-amber-50 text-amber-900', 'disetujui' => 'bg-emerald-50 text-emerald-800', default => 'bg-rose-50 text-rose-800' };
                @endphp
                <article class="grid gap-3 px-4 py-3 text-xs md:grid-cols-[minmax(130px,1fr)_minmax(160px,1.2fr)_minmax(140px,1fr)_minmax(150px,1.2fr)_auto] md:items-center">
                    <div><span class="block text-[11px] font-semibold text-slate-500 md:hidden">Wali siswa</span><p class="font-bold text-slate-900">{{ $req->user->name ?? '—' }}</p></div>
                    <div><span class="block text-[11px] font-semibold text-slate-500 md:hidden">Siswa</span><p class="font-bold text-slate-900">{{ $req->siswa->nama_lengkap ?? '—' }}</p><p class="text-[11px] text-slate-500">{{ $req->siswa->kelas->nama_kelas ?? '—' }}</p></div>
                    <div><span class="block text-[11px] font-semibold text-slate-500 md:hidden">Rapor</span><p class="text-slate-800">{{ $req->rapor ? ucwords(str_replace('_', ' ', $req->rapor->jenis_rapor)) : '—' }} · {{ ucfirst($req->rapor->semester ?? '—') }}</p><p class="text-[11px] text-slate-500">{{ $req->tanggal_request?->format('d/m/Y H:i') ?? '—' }}</p></div>
                    <div class="min-w-0"><span class="block text-[11px] font-semibold text-slate-500 md:hidden">Alasan</span><p class="break-words text-slate-600">{{ $req->alasan ?: '—' }}</p></div>
                    <div class="flex flex-wrap items-center justify-between gap-2 md:justify-end"><span class="rounded-full px-2 py-1 text-[11px] font-bold {{ $tone }}">{{ $label }}</span>
                        @if($req->status === 'menunggu')
                            <div class="flex gap-1.5"><button type="button" data-action="{{ route('wali.rapor.request-download.approve', $req->id) }}" data-requester="{{ $req->user->name ?? 'wali siswa' }}" @click="actionUrl = $el.dataset.action; requester = $el.dataset.requester; actionLabel = 'Setujui'; approve = true; $refs.actionDialog.showModal()" class="inline-flex min-h-9 items-center rounded-lg bg-emerald-600 px-3 font-bold text-white">Setujui</button><button type="button" data-action="{{ route('wali.rapor.request-download.reject', $req->id) }}" data-requester="{{ $req->user->name ?? 'wali siswa' }}" @click="actionUrl = $el.dataset.action; requester = $el.dataset.requester; actionLabel = 'Tolak'; approve = false; $refs.actionDialog.showModal()" class="inline-flex min-h-9 items-center rounded-lg border border-rose-200 bg-rose-50 px-3 font-bold text-rose-800">Tolak</button></div>
                        @endif
                    </div>
                </article>
            @empty
                <p class="px-4 py-9 text-center text-sm text-slate-500">Belum ada permintaan download rapor.</p>
            @endforelse
        </div>
        @if($requests->hasPages())<div class="border-t border-slate-100 px-4 py-3">{{ $requests->links() }}</div>@endif
    </section>
    <dialog x-ref="actionDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900" x-text="actionLabel + ' permintaan download?'"></h2></div><p class="px-4 py-4 text-xs leading-5 text-slate-700"><span x-text="requester"></span> akan <span x-text="approve ? 'mendapat tautan download aktif selama 24 jam' : 'menerima penolakan permintaan download'"></span>.</p><form :action="actionUrl" method="POST" class="flex justify-end gap-2 border-t border-slate-100 p-4">
        @csrf
        <button type="button" @click="$refs.actionDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg px-3 text-xs font-bold text-white" :class="approve ? 'bg-emerald-600' : 'bg-rose-600'" x-text="actionLabel"></button></form></dialog>
</div>
@endsection
