@extends('layouts.app')

@section('title', 'Riwayat Tiket Pemulihan')
@section('page-title', 'Riwayat Pemulihan Akun')
@section('page-subtitle', 'Audit permintaan yang telah selesai atau ditolak')

@section('content')
@php
    $ticketIds = $tickets->pluck('id')->map(fn ($id) => (string) $id)->values();
    $typeLabels = ['lupa_username' => 'Lupa Username', 'lupa_password' => 'Lupa Password', 'lupa_keduanya' => 'Lupa Keduanya'];
    $typeStyles = ['lupa_username' => 'bg-slate-100 text-slate-700', 'lupa_password' => 'bg-amber-50 text-amber-700', 'lupa_keduanya' => 'bg-red-50 text-red-700'];
@endphp

<div x-data="{ selected: [], allIds: @js($ticketIds) }" class="min-w-0 w-full space-y-4">
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div><p class="text-xs font-bold uppercase tracking-wide text-brand-600">Audit bantuan akses</p><h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">Riwayat pemulihan akun</h2><p class="mt-1 text-sm text-slate-500">Tiket selesai disimpan sebagai jejak penanganan dan dapat dibersihkan bila tidak diperlukan.</p></div>
        <nav class="flex rounded-xl bg-slate-100 p-1" aria-label="Navigasi tiket pemulihan"><a href="{{ route('admin.recovery-tickets.index') }}" class="rounded-lg px-3 py-2 text-xs font-bold text-slate-600 no-underline">Antrean</a><a href="{{ route('admin.recovery-tickets.history') }}" class="rounded-lg bg-white px-3 py-2 text-xs font-bold text-brand-700 no-underline shadow-sm">Riwayat <span class="ml-1 rounded-full bg-slate-100 px-1.5 py-0.5 text-slate-600">{{ $tickets->total() }}</span></a></nav>
    </header>

    <form action="{{ route('admin.recovery-tickets.history.bulk-delete') }}" method="POST" data-confirm data-confirm-title="Hapus riwayat terpilih?" data-confirm-message="Riwayat yang dipilih akan dihapus permanen dan tidak dapat dipulihkan." data-confirm-text="Ya, hapus permanen" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div><h3 class="font-extrabold text-slate-950">Tiket yang telah ditutup</h3><p class="mt-0.5 text-xs text-slate-500">{{ $tickets->total() }} riwayat ditemukan.</p></div><div class="flex items-center gap-2">@if($tickets->isNotEmpty())<label class="inline-flex min-h-10 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 text-xs font-bold text-slate-600"><input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-600" :checked="allIds.length > 0 && selected.length === allIds.length" @change="selected = $event.target.checked ? [...allIds] : []">Semua halaman ini</label>@endif<button type="submit" :disabled="selected.length === 0" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-red-600 px-3 text-xs font-bold text-white disabled:cursor-not-allowed disabled:opacity-40"><i class="fas fa-trash"></i><span>Hapus (<span x-text="selected.length">0</span>)</span></button></div></div>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($tickets as $ticket)
                @php $user = $ticket->user; $roleName = ucwords(str_replace('_', ' ', $user?->roleRelation?->name ?? $user?->role ?? 'User tidak tersedia')); @endphp
                <article class="p-4"><div class="flex items-start gap-3"><input type="checkbox" value="{{ $ticket->id }}" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-extrabold text-slate-700">{{ strtoupper(substr($user?->name ?? '?', 0, 1)) }}</span><div class="min-w-0 flex-1"><strong class="block truncate text-sm text-slate-950">{{ $user?->name ?? 'Pengguna telah dihapus' }}</strong><span class="block truncate text-xs text-slate-500">{{ $roleName }} &middot; {{ $ticket->created_at->format('d M Y H:i') }}</span></div><span class="whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-bold {{ $ticket->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $ticket->status === 'resolved' ? 'Selesai' : 'Ditolak' }}</span></div><dl class="mt-3 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Kendala</dt><dd class="mt-1 font-bold text-slate-700">{{ $typeLabels[$ticket->tipe_recovery] ?? $ticket->tipe_recovery }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Ditutup</dt><dd class="mt-1 whitespace-nowrap text-slate-600">{{ $ticket->updated_at->format('d M Y H:i') }}</dd></div><div class="col-span-2 min-w-0"><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Pemulihan melalui</dt><dd class="mt-1 truncate text-slate-600">{{ $user?->personal_email ?: ($ticket->target_phone ?: 'Tidak tercatat') }}</dd></div></dl></article>
            @empty
                <div class="p-12 text-center"><i class="fas fa-history text-4xl text-slate-300"></i><h4 class="mt-3 font-extrabold text-slate-900">Belum ada riwayat</h4><p class="mt-1 text-sm text-slate-500">Tiket yang selesai atau ditolak akan muncul di sini.</p></div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block"><table class="w-full table-fixed text-left text-sm"><colgroup><col class="w-12"><col class="w-14"><col class="w-40"><col class="w-[22%]"><col class="w-40"><col><col class="w-28"><col class="w-40"></colgroup><thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500"><tr><th class="px-3 py-3"></th><th class="px-2 py-3">No</th><th class="px-3 py-3">Diminta</th><th class="px-3 py-3">Pengguna</th><th class="px-3 py-3">Kendala</th><th class="px-3 py-3">Pemulihan melalui</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Ditutup</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse($tickets as $ticket)
                @php $user = $ticket->user; $roleName = ucwords(str_replace('_', ' ', $user?->roleRelation?->name ?? $user?->role ?? 'User tidak tersedia')); @endphp
                <tr><td class="px-3 py-3 text-center"><input type="checkbox" value="{{ $ticket->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600"></td><td class="px-2 py-3 text-xs text-slate-400">{{ $tickets->firstItem() + $loop->index }}</td><td class="whitespace-nowrap px-3 py-3 text-xs text-slate-500">{{ $ticket->created_at->format('d M Y H:i') }}</td><td class="px-3 py-3"><strong class="block truncate text-slate-950" title="{{ $user?->name }}">{{ $user?->name ?? 'Pengguna telah dihapus' }}</strong><span class="block truncate text-xs text-slate-500">{{ $roleName }}</span></td><td class="px-3 py-3"><span class="inline-flex whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-bold {{ $typeStyles[$ticket->tipe_recovery] ?? 'bg-slate-100 text-slate-600' }}">{{ $typeLabels[$ticket->tipe_recovery] ?? $ticket->tipe_recovery }}</span></td><td class="px-3 py-3"><span class="block truncate text-xs text-slate-600" title="{{ $user?->personal_email ?: $ticket->target_phone }}">{{ $user?->personal_email ?: ($ticket->target_phone ?: 'Tidak tercatat') }}</span></td><td class="px-3 py-3"><span class="inline-flex whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-bold {{ $ticket->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $ticket->status === 'resolved' ? 'Selesai' : 'Ditolak' }}</span></td><td class="whitespace-nowrap px-3 py-3 text-xs text-slate-500">{{ $ticket->updated_at->format('d M Y H:i') }}</td></tr>
            @empty<tr><td colspan="8" class="p-12 text-center text-sm text-slate-500">Belum ada riwayat tiket pemulihan.</td></tr>@endforelse
        </tbody></table></div>
        @if($tickets->hasPages())<footer class="border-t border-slate-200 p-4">{{ $tickets->links() }}</footer>@endif
    </form>
</div>
@endsection
