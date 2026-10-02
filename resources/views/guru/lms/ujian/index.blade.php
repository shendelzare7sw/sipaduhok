@extends('layouts.lms-guru')

@php
    $isLatihan = $tipeUjian === 'latihan';
    $label = $isLatihan ? 'Latihan' : 'Ujian';
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $args = [$kelas->id, $mapel->id];
    $tipeBadge = fn ($tipe) => match ($tipe) {
        'ulangan_harian' => ['bg-sky-50 text-sky-700', 'UH'],
        'latihan' => ['bg-emerald-50 text-emerald-700', 'Latihan'],
        'uts', 'pts_ganjil', 'pts_genap' => ['bg-amber-50 text-amber-700', 'PTS'],
        'uas', 'pas_ganjil', 'pas_genap' => ['bg-rose-50 text-rose-700', 'PAS'],
        'to_1' => ['bg-violet-50 text-violet-700', 'TO 1'],
        'to_2' => ['bg-violet-50 text-violet-700', 'TO 2'],
        'to_3' => ['bg-violet-50 text-violet-700', 'TO 3'],
        'upk' => ['bg-slate-800 text-white', 'UPK'],
        'ujian_praktek' => ['bg-slate-800 text-white', 'Praktek'],
        default => ['bg-slate-100 text-slate-600', strtoupper(str_replace('_', ' ', $tipe))],
    };
@endphp

@section('title', 'Daftar ' . $label)
@section('page-title', $label)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5" x-data="{ hapusUrl: '', hapusJudul: '', bukaHapus(el) { this.hapusUrl = el.dataset.url; this.hapusJudul = el.dataset.judul; this.$refs.terkait.checked = false; this.$refs.hapusDialog.showModal(); } }">
    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="min-w-0">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid {{ $isLatihan ? 'fa-pencil-ruler' : 'fa-file-lines' }} text-indigo-600" aria-hidden="true"></i>Daftar {{ strtolower($label) }}</h2>
            <p class="mt-0.5 text-xs text-slate-500">Buat {{ strtolower($label) }}, kelola soal, lalu pantau hasil{{ $isLatihan ? '' : ' dan pengawasan realtime' }}.</p>
        </div>
        <a href="{{ route($prefix.'.create', $args) }}" class="inline-flex min-h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-indigo-700"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Buat {{ strtolower($label) }}</a>
    </header>

    @if($ujianList->count() > 0)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden xl:block">
                <table class="w-full table-fixed text-left text-sm">
                    <colgroup><col class="w-12"><col><col class="w-24"><col class="w-36"><col class="w-36"><col class="w-32"><col class="{{ $isLatihan ? 'w-[200px]' : 'w-[240px]' }}"></colgroup>
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-3 text-center">No</th><th class="px-3 py-3">Judul</th><th class="px-3 py-3 text-center">Tipe</th><th class="px-3 py-3 text-center">Mulai</th><th class="px-3 py-3 text-center">Selesai</th><th class="px-3 py-3 text-center">Durasi &amp; soal</th><th class="px-4 py-3 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ujianList as $index => $ujian)
                            @php [$badgeTone, $badgeLabel] = $tipeBadge($ujian->tipe_ujian); @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-center text-xs text-slate-500">{{ ($ujianList->currentPage() - 1) * $ujianList->perPage() + $index + 1 }}</td>
                                <td class="px-3 py-3">
                                    <p class="truncate font-extrabold text-slate-900" title="{{ $ujian->judul_ujian }}">{{ $ujian->judul_ujian }}</p>
                                    @if($ujian->deskripsi)<p class="mt-0.5 truncate text-xs text-slate-500">{{ Str::limit($ujian->deskripsi, 80) }}</p>@endif
                                </td>
                                <td class="px-3 py-3 text-center"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $badgeTone }}">{{ $badgeLabel }}</span></td>
                                <td class="whitespace-nowrap px-3 py-3 text-center text-xs text-slate-600">{{ $ujian->tanggal_mulai->format('d/m/Y H:i') }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-center text-xs text-slate-600">{{ $ujian->tanggal_selesai->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-3 text-center text-[11px]">
                                    <span class="block whitespace-nowrap font-bold text-slate-700">{{ $ujian->durasi_menit == 0 ? 'Tanpa batas' : $ujian->durasi_menit.' menit' }}</span>
                                    <span class="mt-0.5 block whitespace-nowrap text-slate-500">{{ $ujian->soal_ujian_count }} soal</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-1.5">
                                        <x-cleanflow.table-action :href="route($prefix.'.hasil', [...$args, $ujian->id])" tone="success" icon="fa-solid fa-chart-column" label="Hasil {{ $ujian->judul_ujian }}" />
                                        @unless($isLatihan)
                                            <x-cleanflow.table-action :href="route('guru.lms.ujian.pengawasan', [...$args, $ujian->id])" tone="view" icon="fa-solid fa-desktop" label="Pengawasan realtime {{ $ujian->judul_ujian }}" />
                                        @endunless
                                        <x-cleanflow.table-action :href="route($prefix.'.soal.manage', [...$args, $ujian->id])" tone="info" icon="fa-solid fa-list-ol" label="Kelola soal {{ $ujian->judul_ujian }}" />
                                        <x-cleanflow.table-action :href="route($prefix.'.edit', [...$args, $ujian->id])" tone="edit" icon="fa-solid fa-pen-to-square" label="Edit {{ $ujian->judul_ujian }}" />
                                        <x-cleanflow.table-action tone="delete" icon="fa-solid fa-trash" label="Hapus {{ $ujian->judul_ujian }}" data-url="{{ route($prefix.'.destroy', [...$args, $ujian->id]) }}" data-judul="{{ $ujian->judul_ujian }}" x-on:click="bukaHapus($el)" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 xl:hidden">
                @foreach($ujianList as $ujian)
                    @php [$badgeTone, $badgeLabel] = $tipeBadge($ujian->tipe_ujian); @endphp
                    <article class="space-y-3 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-extrabold text-slate-900">{{ $ujian->judul_ujian }}</h3>
                                @if($ujian->deskripsi)<p class="mt-0.5 text-xs text-slate-500">{{ Str::limit($ujian->deskripsi, 80) }}</p>@endif
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $badgeTone }}">{{ $badgeLabel }}</span>
                        </div>
                        <dl class="grid grid-cols-2 gap-2 text-[11px] sm:grid-cols-4">
                            <div class="rounded-lg bg-slate-50 p-2"><dt class="font-bold uppercase text-slate-500">Mulai</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $ujian->tanggal_mulai->format('d/m/Y H:i') }}</dd></div>
                            <div class="rounded-lg bg-slate-50 p-2"><dt class="font-bold uppercase text-slate-500">Selesai</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $ujian->tanggal_selesai->format('d/m/Y H:i') }}</dd></div>
                            <div class="rounded-lg bg-slate-50 p-2"><dt class="font-bold uppercase text-slate-500">Durasi</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $ujian->durasi_menit == 0 ? 'Tanpa batas' : $ujian->durasi_menit.' menit' }}</dd></div>
                            <div class="rounded-lg bg-indigo-50 p-2"><dt class="font-bold uppercase text-indigo-700">Soal</dt><dd class="mt-0.5 font-extrabold text-indigo-800">{{ $ujian->soal_ujian_count }}</dd></div>
                        </dl>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-5">
                            <a href="{{ route($prefix.'.soal.manage', [...$args, $ujian->id]) }}" class="col-span-2 inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-indigo-600 text-xs font-bold text-white no-underline sm:col-span-1"><i class="fa-solid fa-list-ol" aria-hidden="true"></i>Kelola soal</a>
                            <a href="{{ route($prefix.'.hasil', [...$args, $ujian->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700 no-underline"><i class="fa-solid fa-chart-column" aria-hidden="true"></i>Hasil</a>
                            @unless($isLatihan)
                                <a href="{{ route('guru.lms.ujian.pengawasan', [...$args, $ujian->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 no-underline"><i class="fa-solid fa-desktop" aria-hidden="true"></i>Awasi</a>
                            @endunless
                            <a href="{{ route($prefix.'.edit', [...$args, $ujian->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Edit</a>
                            <button type="button" data-url="{{ route($prefix.'.destroy', [...$args, $ujian->id]) }}" data-judul="{{ $ujian->judul_ujian }}" @click="bukaHapus($el)" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-rose-50 text-xs font-bold text-rose-700"><i class="fa-solid fa-trash" aria-hidden="true"></i>Hapus</button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        @if($ujianList->hasPages())<div>{{ $ujianList->links() }}</div>@endif
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
            <h3 class="mt-4 font-extrabold text-slate-900">Tidak ada {{ strtolower($label) }}</h3>
            <p class="mt-1 text-sm text-slate-500">Belum ada {{ strtolower($label) }} yang dibuat.</p>
            <a href="{{ route($prefix.'.create', $args) }}" class="mt-4 inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-indigo-50 px-4 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-plus" aria-hidden="true"></i>Buat {{ strtolower($label) }} sekarang</a>
        </section>
    @endif

    <dialog x-ref="hapusDialog" @click.self="$el.close()" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form method="POST" :action="hapusUrl" data-confirmed="true" class="px-5 py-5">
            @csrf
            @method('DELETE')
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-triangle-exclamation text-rose-600" aria-hidden="true"></i>Hapus {{ strtolower($label) }}?</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600"><strong x-text="hapusJudul"></strong> beserta soal dan hasilnya akan dihapus.</p>
            <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-800">
                <input type="checkbox" name="hapus_terkait" value="1" x-ref="terkait" class="mt-0.5 h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                Hapus juga {{ strtolower($label) }} ini dari kelas lain (jika ada duplikat).
            </label>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$refs.hapusDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="min-h-10 rounded-xl bg-rose-600 px-4 text-xs font-bold text-white hover:bg-rose-700">Ya, hapus</button>
            </div>
        </form>
    </dialog>
</div>
@endsection
