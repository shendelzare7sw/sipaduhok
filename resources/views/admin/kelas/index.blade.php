@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')
@section('page-subtitle', $currentTahunAjaran ? 'Kelola kelas - ' . $currentTahunAjaran->nama_tahun_ajaran : 'Kelola seluruh data kelas')

@section('content')
@php
    $statItems = [
        ['value' => number_format($stats['totalKelas']), 'label' => 'Total Kelas', 'meta' => $currentTahunAjaran?->nama_tahun_ajaran ?? 'Semua tahun', 'icon' => 'fa-chalkboard', 'tone' => 'bg-blue-50 text-blue-600'],
        ['value' => number_format($stats['totalSiswa']), 'label' => 'Siswa Terdaftar', 'meta' => 'Pada periode pilihan', 'icon' => 'fa-user-graduate', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['value' => number_format($stats['kelasWithWali']), 'label' => 'Ada Wali Kelas', 'meta' => 'Penugasan lengkap', 'icon' => 'fa-user-tie', 'tone' => 'bg-violet-50 text-violet-600'],
        ['value' => number_format($stats['kelasWithoutWali']), 'label' => 'Belum Ada Wali', 'meta' => 'Perlu ditindaklanjuti', 'icon' => 'fa-user-clock', 'tone' => 'bg-amber-50 text-amber-600'],
    ];
    $jenjangTone = [
        'KB' => 'bg-pink-50 text-pink-700', 'TKA' => 'bg-fuchsia-50 text-fuchsia-700',
        'TKB' => 'bg-violet-50 text-violet-700', 'SD' => 'bg-blue-50 text-blue-700',
        'SMP' => 'bg-emerald-50 text-emerald-700', 'SMA' => 'bg-amber-50 text-amber-700',
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <x-cleanflow.stat-grid :items="$statItems" />

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-list text-brand-600" aria-hidden="true"></i>Daftar Kelas</h2>
                    <p class="mt-1 text-xs text-slate-500">Cari kelas, atur wali, lalu tempatkan siswa dari satu halaman.</p>
                </div>
                <div class="grid grid-cols-4 gap-2 sm:flex">
                    <button type="button" data-dialog-open="copy-class-dialog" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-cyan-50 px-3 text-xs font-bold text-cyan-700 hover:bg-cyan-100" title="Salin data kelas"><i class="fas fa-copy" aria-hidden="true"></i><span class="hidden sm:inline">Salin</span></button>
                    <a href="{{ route('admin.kelas.print', request()->query()) }}" target="_blank" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200" title="Cetak"><i class="fas fa-print" aria-hidden="true"></i><span class="hidden sm:inline">Cetak</span></a>
                    <a href="{{ route('admin.kelas.import') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700 no-underline hover:bg-emerald-100" title="Import Excel"><i class="fas fa-file-import" aria-hidden="true"></i><span class="hidden sm:inline">Import</span></a>
                    <a href="{{ route('admin.kelas.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i><span class="hidden sm:inline">Kelas Baru</span></a>
                </div>
            </div>

            <form action="{{ route('admin.kelas.index') }}" method="GET" class="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-[minmax(240px,1fr)_repeat(3,minmax(150px,0.45fr))_auto]">
                <label class="relative min-w-0"><span class="sr-only">Cari kelas</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode kelas..." class="h-10 w-full rounded-xl border border-slate-200 bg-white !pl-10 pr-3 text-xs text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></label>
                <select name="tahun_ajaran_id" data-auto-submit class="h-10 min-w-0 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500"><option value="">Semua Tahun</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>@endforeach</select>
                <select name="jenjang" data-auto-submit class="h-10 min-w-0 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500"><option value="">Semua Jenjang</option>@foreach($jenjangs as $j)<option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>@endforeach</select>
                <select name="cabang_id" data-auto-submit class="h-10 min-w-0 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500"><option value="">Semua Cabang</option>@foreach($cabangs as $c)<option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>@endforeach</select>
                <div class="flex gap-2"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button>@if(request()->hasAny(['search', 'jenjang', 'cabang_id', 'tahun_ajaran_id']))<a href="{{ route('admin.kelas.index') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-red-50 px-3 text-red-700 no-underline hover:bg-red-100" title="Reset filter"><i class="fas fa-times" aria-hidden="true"></i></a>@endif</div>
            </form>
        </header>

        <div class="divide-y divide-slate-100 md:hidden">
            @forelse($kelas as $k)
                @php $percentage = $k->kuota_siswa > 0 ? min(($k->siswa_count / $k->kuota_siswa) * 100, 100) : 0; @endphp
                <article class="min-w-0 p-4">
                    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="truncate text-sm font-extrabold text-slate-900">{{ $k->nama_kelas }}</h3><p class="mt-1 truncate text-[11px] text-slate-500">{{ $k->kode_kelas }} · {{ $k->cabang->nama_cabang ?? 'Tanpa cabang' }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $jenjangTone[$k->jenjang] ?? 'bg-slate-100 text-slate-700' }}">{{ $k->jenjang }}</span></div>
                    <div class="mt-3 rounded-xl bg-slate-50 p-3">
                        <div class="flex items-center justify-between gap-3 text-xs"><span class="text-slate-500">Siswa</span><strong class="text-slate-800">{{ $k->siswa_count }} / {{ $k->kuota_siswa ?: '-' }}</strong></div>
                        <progress value="{{ $percentage }}" max="100" class="mt-2 h-1.5 w-full overflow-hidden rounded-full accent-brand-600">{{ round($percentage) }}%</progress>
                        <div class="mt-3 border-t border-slate-200 pt-3"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Wali kelas</p>@forelse($k->waliKelasAssignments as $assignment)<p class="mt-1 truncate text-xs font-semibold text-slate-700"><i class="fas fa-user-tie mr-1.5 text-violet-500" aria-hidden="true"></i>{{ $assignment->tenagaPendidik->nama_lengkap }}</p>@empty<p class="mt-1 text-xs font-semibold text-amber-700"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Belum ditentukan</p>@endforelse</div>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-2"><a href="{{ route('admin.kelas.show', $k->id) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 no-underline hover:bg-blue-100"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a><a href="{{ route('admin.kelas.edit', $k->id) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a><form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" data-confirm data-confirm-title="Hapus kelas?" data-confirm-message="Kelas {{ $k->nama_kelas }} akan dihapus permanen." data-confirm-text="Ya, hapus kelas">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-1 rounded-lg bg-red-50 text-xs font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button></form></div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-chalkboard" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">Tidak ada data kelas</h3><p class="mt-1 text-xs text-slate-500">Tambahkan kelas pertama atau ubah filter.</p></div>
            @endforelse
        </div>

        @if($kelas->isNotEmpty())
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[900px] table-fixed border-collapse text-left text-sm">
                    <colgroup><col class="w-72"><col class="w-24"><col><col class="w-52"><col class="w-40"></colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Jenjang</th><th class="px-3 py-3">Wali Kelas</th><th class="px-3 py-3">Kuota / Siswa</th><th class="px-3 py-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kelas as $k)
                            @php $percentage = $k->kuota_siswa > 0 ? min(($k->siswa_count / $k->kuota_siswa) * 100, 100) : 0; @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-4"><p class="truncate font-bold text-slate-900" title="{{ $k->nama_kelas }}">{{ $k->nama_kelas }}</p><p class="mt-1 truncate whitespace-nowrap text-[11px] text-slate-500" title="{{ $k->kode_kelas }} · {{ $k->cabang->nama_cabang ?? '-' }}">{{ $k->kode_kelas }} &middot; {{ $k->cabang->nama_cabang ?? '-' }}</p></td>
                                <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $jenjangTone[$k->jenjang] ?? 'bg-slate-100 text-slate-700' }}">{{ $k->jenjang }}</span></td>
                                <td class="min-w-0 px-3 py-4">@forelse($k->waliKelasAssignments as $assignment)<p class="truncate text-xs font-semibold text-slate-700" title="{{ $assignment->tenagaPendidik->nama_lengkap }}">{{ $assignment->tenagaPendidik->nama_lengkap }}</p>@empty<span class="whitespace-nowrap text-xs font-semibold text-amber-700">Belum ada</span>@endforelse</td>
                                <td class="px-3 py-4"><div class="flex w-36 items-center justify-between text-xs"><span class="whitespace-nowrap font-bold text-slate-800">{{ $k->siswa_count }} / {{ $k->kuota_siswa ?: '-' }}</span><span class="text-slate-400">{{ round($percentage) }}%</span></div><progress value="{{ $percentage }}" max="100" class="mt-1.5 h-1.5 w-36 overflow-hidden rounded-full accent-brand-600">{{ round($percentage) }}%</progress></td>
                                <td class="px-3 py-4"><div class="flex items-center justify-end gap-2"><x-cleanflow.table-action href="{{ route('admin.kelas.show', $k->id) }}" tone="view" icon="fas fa-eye" label="Detail kelas" /><x-cleanflow.table-action href="{{ route('admin.kelas.edit', $k->id) }}" tone="edit" icon="fas fa-edit" label="Edit kelas" /><form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" data-confirm data-confirm-title="Hapus kelas?" data-confirm-message="Kelas {{ $k->nama_kelas }} akan dihapus permanen." data-confirm-text="Ya, hapus kelas">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus kelas" /></form></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($kelas->hasPages())<footer class="flex flex-col gap-2 border-t border-slate-200 px-4 py-3 text-[11px] text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>Menampilkan {{ $kelas->firstItem() ?? 0 }}-{{ $kelas->lastItem() ?? 0 }} dari {{ $kelas->total() }}</span>{{ $kelas->appends(request()->query())->links() }}</footer>@endif
    </section>
</div>

<dialog id="copy-class-dialog" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-slate-950/60">
    <form action="{{ route('admin.kelas.copy') }}" method="POST">@csrf
        <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><div><h2 class="text-base font-extrabold text-slate-900"><i class="fas fa-copy mr-2 text-brand-600" aria-hidden="true"></i>Salin Data Kelas</h2><p class="mt-1 text-xs text-slate-500">Salin master kelas ke periode baru.</p></div><button type="button" data-dialog-close class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Tutup"><i class="fas fa-times" aria-hidden="true"></i></button></header>
        <div class="space-y-4 p-5"><p class="rounded-xl bg-blue-50 p-3 text-xs leading-5 text-blue-800"><i class="fas fa-info-circle mr-1" aria-hidden="true"></i>Hanya data master kelas yang disalin; siswa dan guru pengajar tidak ikut.</p><label class="block"><span class="mb-1.5 block text-xs font-bold text-slate-700">Dari Tahun Ajaran</span><select name="from_tahun_ajaran_id" required class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"><option value="">Pilih tahun asal...</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>@endforeach</select></label><label class="block"><span class="mb-1.5 block text-xs font-bold text-slate-700">Ke Tahun Ajaran</span><select name="to_tahun_ajaran_id" required class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"><option value="">Pilih tahun tujuan...</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" {{ $currentTahunAjaran?->id == $ta->id ? 'selected' : '' }}>{{ $ta->nama_tahun_ajaran }}</option>@endforeach</select></label></div>
        <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3"><button type="button" data-dialog-close class="h-10 rounded-xl px-4 text-xs font-bold text-slate-600 hover:bg-slate-200">Batal</button><button type="submit" class="h-10 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-paste mr-1.5" aria-hidden="true"></i>Mulai Menyalin</button></footer>
    </form>
</dialog>
@endsection
