@extends('layouts.app')

@section('title', 'Kelola Siswa - ' . $kelas->nama_kelas)
@section('page-title', 'Kelola Siswa Kelas')
@section('page-subtitle', $kelas->nama_kelas . ' · ' . $kelas->tahunAjaran->nama_tahun_ajaran)

@section('content')
<div
    class="min-w-0 w-full space-y-5"
    x-data="{
        searchMember: '',
        searchAvailable: '',
        selected: [],
        limit: @js(max(0, $sisaKuota)),
        availableIds: @js($siswaAvailable->pluck('id')->map(fn ($id) => (string) $id)->values()),
        selectAllowed() { this.selected = this.availableIds.slice(0, this.limit); },
        clearSelection() { this.selected = []; }
    }"
>
    <section class="overflow-hidden rounded-2xl border border-brand-200 bg-gradient-to-br from-brand-700 via-brand-600 to-sky-500 text-white shadow-lg shadow-brand-900/10">
        <div class="flex flex-col gap-4 p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-3"><a href="{{ route('admin.kelas.show', $kelas) }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white no-underline ring-1 ring-white/25 hover:bg-white/25" aria-label="Kembali ke detail kelas"><i class="fas fa-arrow-left" aria-hidden="true"></i></a><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-sky-100">Penempatan siswa</p><h2 class="truncate text-2xl font-extrabold !text-white">{{ $kelas->nama_kelas }}</h2><p class="mt-1 truncate text-xs text-sky-50">{{ $kelas->cabang->nama_cabang }} &middot; {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</p></div></div>
            <div class="grid grid-cols-3 gap-2 text-center"><div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><strong class="block text-xl">{{ $siswaInKelas->count() }}</strong><span class="text-[10px] font-bold uppercase text-sky-100">Terisi</span></div><div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><strong class="block text-xl">{{ $kelas->kuota_siswa }}</strong><span class="text-[10px] font-bold uppercase text-sky-100">Kuota</span></div><div class="rounded-xl bg-white/10 px-4 py-2 ring-1 ring-white/15"><strong class="block text-xl">{{ max(0, $sisaKuota) }}</strong><span class="text-[10px] font-bold uppercase text-sky-100">Tersisa</span></div></div>
        </div>
    </section>

    @if($sisaKuota <= 0)
        <aside class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900"><i class="fas fa-triangle-exclamation mt-0.5" aria-hidden="true"></i><div><p class="text-sm font-extrabold">Kuota kelas sudah penuh</p><p class="mt-1 text-xs">Keluarkan siswa atau naikkan kuota kelas sebelum menambahkan siswa baru.</p></div></aside>
    @endif

    <section class="grid min-w-0 gap-4 xl:grid-cols-2">
        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex items-center justify-between gap-3"><div><h3 class="font-extrabold text-slate-950"><i class="fas fa-users mr-2 text-blue-600" aria-hidden="true"></i>Siswa di kelas</h3><p class="mt-1 text-xs text-slate-500">{{ $siswaInKelas->count() }} siswa sudah ditempatkan.</p></div><span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-700">Terdaftar</span></div><label class="relative mt-3 block"><span class="sr-only">Cari siswa di kelas</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" x-model.debounce.200ms="searchMember" placeholder="Cari nama atau NIS..." class="h-10 w-full rounded-xl border border-slate-200 !pl-10 pr-3 text-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label></header>
            <div class="max-h-[34rem] divide-y divide-slate-100 overflow-y-auto">
                @forelse($siswaInKelas as $item)
                    <div x-show="@js(strtolower($item->nama_lengkap . ' ' . $item->nis)).includes(searchMember.toLowerCase())" class="flex items-center gap-3 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-extrabold text-blue-700">{{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1"><h4 class="truncate text-sm font-extrabold text-slate-900">{{ $item->nama_lengkap }}</h4><p class="mt-0.5 truncate text-[11px] text-slate-500">{{ $item->nis }} &middot; {{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div>
                        <form action="{{ route('admin.kelas.remove-siswa', $kelas) }}" method="POST" data-confirm data-confirm-title="Keluarkan siswa?" data-confirm-message="{{ $item->nama_lengkap }} akan dikeluarkan dari kelas {{ $kelas->nama_kelas }}, tetapi datanya tetap tersimpan." data-confirm-text="Ya, keluarkan">@csrf<input type="hidden" name="siswa_id" value="{{ $item->id }}"><x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-user-minus" label="Keluarkan siswa" /></form>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fas fa-users" aria-hidden="true"></i></span><p class="mt-3 text-sm font-bold text-slate-700">Belum ada siswa di kelas ini</p></div>
                @endforelse
            </div>
        </article>

        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex items-center justify-between gap-3"><div><h3 class="font-extrabold text-slate-950"><i class="fas fa-user-plus mr-2 text-emerald-600" aria-hidden="true"></i>Siswa tersedia</h3><p class="mt-1 text-xs text-slate-500">Hanya siswa aktif tanpa kelas dari cabang yang sama.</p></div><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">{{ $siswaAvailable->count() }} tersedia</span></div><label class="relative mt-3 block"><span class="sr-only">Cari siswa tersedia</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" x-model.debounce.200ms="searchAvailable" placeholder="Cari nama atau NIS..." class="h-10 w-full rounded-xl border border-slate-200 !pl-10 pr-3 text-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label></header>

            @if($siswaAvailable->isNotEmpty() && $sisaKuota > 0)
                <form action="{{ route('admin.kelas.add-siswa', $kelas) }}" method="POST">@csrf
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3"><p class="text-xs text-slate-600"><strong x-text="selected.length">0</strong> dipilih &middot; maksimal {{ $sisaKuota }}</p><div class="flex gap-2"><button type="button" @click="clearSelection" class="h-8 rounded-lg px-2.5 text-[11px] font-bold text-slate-600 hover:bg-slate-200">Kosongkan</button><button type="button" @click="selectAllowed" class="h-8 rounded-lg bg-blue-50 px-2.5 text-[11px] font-bold text-blue-700 hover:bg-blue-100">Pilih sesuai kuota</button></div></div>
                    <div class="max-h-[28rem] divide-y divide-slate-100 overflow-y-auto">
                        @foreach($siswaAvailable as $item)
                            <label x-show="@js(strtolower($item->nama_lengkap . ' ' . $item->nis)).includes(searchAvailable.toLowerCase())" class="flex cursor-pointer items-center gap-3 p-4 hover:bg-slate-50 has-[:checked]:bg-emerald-50/60">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $item->id }}" x-model="selected" :disabled="selected.length >= limit && !selected.includes(@js((string) $item->id))" class="h-4 w-4 shrink-0 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-40">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-extrabold text-emerald-700">{{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}</span>
                                <span class="min-w-0 flex-1"><strong class="block truncate text-sm text-slate-900">{{ $item->nama_lengkap }}</strong><span class="mt-0.5 block truncate text-[11px] text-slate-500">{{ $item->nis }} &middot; {{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></span>
                            </label>
                        @endforeach
                    </div>
                    <footer class="flex items-center justify-between gap-3 border-t border-slate-200 p-4"><p class="text-xs text-slate-500">Pilihan tidak boleh melebihi sisa kuota.</p><button type="submit" :disabled="selected.length === 0 || selected.length > limit" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40"><i class="fas fa-plus" aria-hidden="true"></i>Tambahkan <span x-show="selected.length" x-text="selected.length"></span></button></footer>
                </form>
            @elseif($sisaKuota <= 0)
                <div class="px-5 py-12 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><i class="fas fa-ban" aria-hidden="true"></i></span><p class="mt-3 text-sm font-bold text-slate-700">Kuota kelas penuh</p></div>
            @else
                <div class="px-5 py-12 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fas fa-circle-check" aria-hidden="true"></i></span><p class="mt-3 text-sm font-bold text-slate-700">Tidak ada siswa yang perlu ditempatkan</p><p class="mt-1 text-xs text-slate-500">Semua siswa cabang ini sudah memiliki kelas.</p></div>
            @endif
        </article>
    </section>
</div>
@endsection
