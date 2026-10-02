@extends('layouts.lms-guru')

@section('title', 'Daftar Tugas')
@section('page-title', 'Tugas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php $args = [$kelas->id, $mapel->id]; @endphp

<div class="min-w-0 w-full space-y-5" x-data="{ hapusUrl: '', hapusJudul: '', bukaHapus(el) { this.hapusUrl = el.dataset.url; this.hapusJudul = el.dataset.judul; this.$refs.terkait.checked = false; this.$refs.hapusDialog.showModal(); } }">
    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="min-w-0">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-list-check text-indigo-600" aria-hidden="true"></i>Daftar tugas</h2>
            <p class="mt-0.5 text-xs text-slate-500">Pantau pengumpulan, koreksi jawaban, dan kelola tugas kelas ini.</p>
        </div>
        <a href="{{ route('guru.lms.tugas.create', $args) }}" class="inline-flex min-h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-indigo-700"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Buat tugas baru</a>
    </header>

    @if($tugasList->count() > 0)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden lg:block">
                <table class="w-full table-fixed text-left text-sm">
                    <colgroup><col><col class="w-32"><col class="w-36"><col class="w-32"><col class="w-28"><col class="w-[148px]"></colgroup>
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Judul tugas</th><th class="px-3 py-3 text-center">Mulai</th><th class="px-3 py-3 text-center">Deadline</th><th class="px-3 py-3 text-center">Dikumpulkan</th><th class="px-3 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tugasList as $tugas)
                            @php $lewat = $tugas->isOverdue(); @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <p class="truncate font-extrabold text-slate-900" title="{{ $tugas->judul_tugas }}">{{ $tugas->judul_tugas }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ Str::limit($tugas->deskripsi, 80) }}</p>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center text-xs text-slate-600">{{ $tugas->tanggal_mulai->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-center text-xs {{ $lewat ? 'font-bold text-rose-600' : 'text-slate-600' }}">{{ $tugas->tanggal_deadline->format('d M Y') }}</td>
                                <td class="px-3 py-3 text-center"><span class="whitespace-nowrap rounded-full bg-sky-50 px-2.5 py-1 text-xs font-extrabold tabular-nums text-sky-700">{{ $tugas->submitted_count }}/{{ $tugas->tugas_siswa_count }}</span></td>
                                <td class="px-3 py-3 text-center">
                                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $lewat ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700' }}">{{ $lewat ? 'Selesai' : 'Aktif' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-1.5">
                                        <x-cleanflow.table-action :href="route('guru.lms.tugas.koreksi', [...$args, $tugas->id])" tone="success" icon="fa-solid fa-check-double" label="Koreksi {{ $tugas->judul_tugas }}" />
                                        <x-cleanflow.table-action :href="route('guru.lms.tugas.edit', [...$args, $tugas->id])" tone="edit" icon="fa-solid fa-pen-to-square" label="Edit {{ $tugas->judul_tugas }}" />
                                        <x-cleanflow.table-action tone="delete" icon="fa-solid fa-trash" label="Hapus {{ $tugas->judul_tugas }}" data-url="{{ route('guru.lms.tugas.destroy', [...$args, $tugas->id]) }}" data-judul="{{ $tugas->judul_tugas }}" x-on:click="bukaHapus($el)" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 lg:hidden">
                @foreach($tugasList as $tugas)
                    @php $lewat = $tugas->isOverdue(); @endphp
                    <article class="space-y-3 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-extrabold text-slate-900">{{ $tugas->judul_tugas }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ Str::limit($tugas->deskripsi, 80) }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $lewat ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700' }}">{{ $lewat ? 'Selesai' : 'Aktif' }}</span>
                        </div>
                        <dl class="grid grid-cols-3 gap-2 text-center text-[11px]">
                            <div class="rounded-lg bg-slate-50 p-2"><dt class="font-bold uppercase text-slate-500">Mulai</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $tugas->tanggal_mulai->format('d M') }}</dd></div>
                            <div class="rounded-lg bg-slate-50 p-2"><dt class="font-bold uppercase text-slate-500">Deadline</dt><dd class="mt-0.5 font-semibold {{ $lewat ? 'text-rose-600' : 'text-slate-800' }}">{{ $tugas->tanggal_deadline->format('d M') }}</dd></div>
                            <div class="rounded-lg bg-sky-50 p-2"><dt class="font-bold uppercase text-sky-700">Kumpul</dt><dd class="mt-0.5 font-extrabold tabular-nums text-sky-800">{{ $tugas->submitted_count }}/{{ $tugas->tugas_siswa_count }}</dd></div>
                        </dl>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="{{ route('guru.lms.tugas.koreksi', [...$args, $tugas->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700 no-underline"><i class="fa-solid fa-check-double" aria-hidden="true"></i>Koreksi</a>
                            <a href="{{ route('guru.lms.tugas.edit', [...$args, $tugas->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Edit</a>
                            <button type="button" data-url="{{ route('guru.lms.tugas.destroy', [...$args, $tugas->id]) }}" data-judul="{{ $tugas->judul_tugas }}" @click="bukaHapus($el)" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-rose-50 text-xs font-bold text-rose-700"><i class="fa-solid fa-trash" aria-hidden="true"></i>Hapus</button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <div>{{ $tugasList->links() }}</div>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
            <h3 class="mt-4 font-extrabold text-slate-900">Belum ada tugas</h3>
            <p class="mt-1 text-sm text-slate-500">Klik "Buat tugas baru" untuk memulai.</p>
        </section>
    @endif

    <dialog x-ref="hapusDialog" @click.self="$el.close()" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form method="POST" :action="hapusUrl" data-confirmed="true" class="px-5 py-5">
            @csrf
            @method('DELETE')
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-triangle-exclamation text-rose-600" aria-hidden="true"></i>Hapus tugas?</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Tugas <strong x-text="hapusJudul"></strong> beserta pengumpulannya akan dihapus.</p>
            <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-800">
                <input type="checkbox" name="hapus_terkait" value="1" x-ref="terkait" class="mt-0.5 h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                Hapus juga tugas ini dari kelas lain (jika ada duplikat).
            </label>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$refs.hapusDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="min-h-10 rounded-xl bg-rose-600 px-4 text-xs font-bold text-white hover:bg-rose-700">Ya, hapus</button>
            </div>
        </form>
    </dialog>
</div>
@endsection
