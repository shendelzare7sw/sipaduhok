@extends('layouts.app')

@section('title', 'Detail Kelas - ' . $kelas->nama_kelas)
@section('page-title', 'Detail Kelas')
@section('page-subtitle', $kelas->nama_kelas . ' · ' . $kelas->jenjang)

@section('content')
@php
    $statItems = [
        ['value' => $stats['totalSiswa'], 'label' => 'Total siswa', 'meta' => 'Sudah ditempatkan', 'icon' => 'fa-users', 'tone' => 'bg-blue-50 text-blue-600'],
        ['value' => $stats['siswaLaki'], 'label' => 'Laki-laki', 'meta' => 'Siswa kelas ini', 'icon' => 'fa-mars', 'tone' => 'bg-cyan-50 text-cyan-600'],
        ['value' => $stats['siswaPerempuan'], 'label' => 'Perempuan', 'meta' => 'Siswa kelas ini', 'icon' => 'fa-venus', 'tone' => 'bg-violet-50 text-violet-600'],
        ['value' => max(0, $stats['sisaKuota']), 'label' => 'Sisa kuota', 'meta' => 'Dari ' . $kelas->kuota_siswa . ' tempat', 'icon' => 'fa-chair', 'tone' => 'bg-emerald-50 text-emerald-600'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl border border-brand-200 bg-gradient-to-br from-brand-700 via-brand-600 to-sky-500 text-white shadow-lg shadow-brand-900/10 [&_h2]:!text-white">
        <div class="flex flex-col gap-4 p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-3"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl ring-1 ring-white/20"><i class="fas fa-chalkboard" aria-hidden="true"></i></span><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-sky-100">{{ $kelas->kode_kelas }}</p><h2 class="truncate text-2xl font-extrabold">{{ $kelas->nama_kelas }}</h2><div class="mt-2 flex flex-wrap gap-2 text-[11px] font-semibold text-sky-50"><span class="rounded-lg bg-white/10 px-2 py-1">{{ $kelas->jenjang }}</span><span class="rounded-lg bg-white/10 px-2 py-1">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span><span class="rounded-lg bg-white/10 px-2 py-1">{{ $kelas->cabang->nama_cabang ?? '-' }}</span></div></div></div>
            <div class="grid grid-cols-3 gap-2 lg:flex"><a href="{{ route('admin.kelas.manage-siswa', $kelas) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-white px-3 text-xs font-bold text-brand-700 no-underline hover:bg-sky-50"><i class="fas fa-users" aria-hidden="true"></i><span class="hidden sm:inline">Kelola siswa</span></a><a href="{{ route('admin.kelas.edit', $kelas) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-white/15 px-3 text-xs font-bold text-white no-underline ring-1 ring-white/25 hover:bg-white/25"><i class="fas fa-edit" aria-hidden="true"></i><span class="hidden sm:inline">Edit</span></a><a href="{{ route('admin.kelas.index') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-950/15 px-3 text-xs font-bold text-white no-underline ring-1 ring-white/20 hover:bg-slate-950/25"><i class="fas fa-arrow-left" aria-hidden="true"></i><span class="hidden sm:inline">Kembali</span></a></div>
        </div>
    </section>

    <x-cleanflow.stat-grid :items="$statItems" />

    <section class="grid gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(300px,0.8fr)]">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-circle-info mr-2 text-brand-600" aria-hidden="true"></i>Informasi kelas</h3></header>
            <dl class="grid grid-cols-2 gap-px bg-slate-200 sm:grid-cols-3">@foreach([['Kode kelas', $kelas->kode_kelas], ['Nama kelas', $kelas->nama_kelas], ['Jenjang', $kelas->jenjang], ['Cabang', $kelas->cabang->nama_cabang ?? '-'], ['Tahun ajaran', $kelas->tahunAjaran->nama_tahun_ajaran ?? '-'], ['Kuota', $kelas->kuota_siswa . ' siswa']] as $item)<div class="min-w-0 bg-white p-4"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $item[0] }}</dt><dd class="mt-1 break-words text-sm font-bold text-slate-800">{{ $item[1] }}</dd></div>@endforeach</dl>
        </article>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-user-tie mr-2 text-violet-600" aria-hidden="true"></i>Wali kelas</h3></header>
            @if($kelas->waliKelas)
                <div class="flex items-center gap-3 p-4 sm:p-5"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-base font-extrabold text-violet-700">{{ strtoupper(substr($kelas->waliKelas->nama_lengkap, 0, 1)) }}</span><div class="min-w-0"><h4 class="truncate text-sm font-extrabold text-slate-900">{{ $kelas->waliKelas->nama_lengkap }}</h4><p class="mt-1 text-xs text-slate-500">NIP {{ $kelas->waliKelas->nip ?? '-' }}</p><p class="mt-1 text-xs text-slate-500">{{ $kelas->waliKelas->telepon ?? 'Telepon belum tersedia' }}</p></div></div>
            @else
                <div class="p-5 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><i class="fas fa-user-slash" aria-hidden="true"></i></span><p class="mt-3 text-sm font-bold text-slate-700">Belum ada wali kelas</p><a href="{{ route('admin.kelas.edit', $kelas) }}" class="mt-3 inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-violet-50 px-4 text-xs font-bold text-violet-700 no-underline hover:bg-violet-100"><i class="fas fa-user-plus" aria-hidden="true"></i>Tentukan wali</a></div>
            @endif
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div><h3 class="font-extrabold text-slate-950"><i class="fas fa-user-graduate mr-2 text-brand-600" aria-hidden="true"></i>Daftar siswa</h3><p class="mt-1 text-xs text-slate-500">Siswa yang saat ini berada di kelas {{ $kelas->nama_kelas }}.</p></div><span class="whitespace-nowrap rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-700">{{ $siswa->total() }} siswa</span></header>
        @if($siswa->isNotEmpty())
            <div class="divide-y divide-slate-100 md:hidden">@foreach($siswa as $item)<article class="flex items-center gap-3 p-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-extrabold text-brand-700">{{ strtoupper(substr($item->nama_lengkap, 0, 1)) }}</span><div class="min-w-0 flex-1"><h4 class="truncate text-sm font-extrabold text-slate-900">{{ $item->nama_lengkap }}</h4><p class="mt-0.5 truncate text-[11px] text-slate-500">{{ $item->nis }} &middot; NISN {{ $item->nisn }}</p></div><span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-bold {{ $item->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-violet-50 text-violet-700' }}">{{ $item->jenis_kelamin }}</span></article>@endforeach</div>
            <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[720px] table-fixed text-left text-xs"><colgroup><col class="w-14"><col class="w-36"><col><col class="w-36"><col class="w-28"></colgroup><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-3 py-3 text-center">No</th><th class="px-3 py-3">NIS</th><th class="px-3 py-3">Nama siswa</th><th class="px-3 py-3">Jenis kelamin</th><th class="px-3 py-3">Status</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($siswa as $index => $item)<tr class="hover:bg-slate-50"><td class="px-3 py-3 text-center text-slate-400">{{ $siswa->firstItem() + $index }}</td><td class="whitespace-nowrap px-3 py-3 font-mono text-slate-600">{{ $item->nis }}</td><td class="px-3 py-3"><p class="truncate font-bold text-slate-800" title="{{ $item->nama_lengkap }}">{{ $item->nama_lengkap }}</p><p class="mt-0.5 truncate text-[10px] text-slate-400">NISN {{ $item->nisn }}</p></td><td class="px-3 py-3"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $item->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-violet-50 text-violet-700' }}">{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></td><td class="px-3 py-3"><span class="whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">{{ ucfirst($item->status) }}</span></td></tr>@endforeach</tbody></table></div>
            @if($siswa->hasPages())<footer class="border-t border-slate-200 px-4 py-3">{{ $siswa->links() }}</footer>@endif
        @else
            <div class="px-5 py-12 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-users" aria-hidden="true"></i></span><p class="mt-3 text-sm font-bold text-slate-700">Belum ada siswa di kelas ini</p><a href="{{ route('admin.kelas.manage-siswa', $kelas) }}" class="mt-3 inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-user-plus" aria-hidden="true"></i>Tempatkan siswa</a></div>
        @endif
    </section>
</div>
@endsection
