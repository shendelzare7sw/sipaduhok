@extends('layouts.app')

@section('title', 'Import Kelas')
@section('page-title', 'Import Kelas')
@section('page-subtitle', 'Tambahkan banyak kelas dari satu file Excel')

@section('content')
@php
    $routePrefix = request()->routeIs('waka.*') ? 'waka' : 'admin';
    $columns = [
        ['name' => 'nama_kelas', 'required' => true, 'description' => 'Nama kelas, misalnya 7A atau KB1.'],
        ['name' => 'jenjang', 'required' => true, 'description' => 'Salah satu dari KB, TKA, TKB, SD, SMP, atau SMA.'],
        ['name' => 'nama_cabang', 'required' => false, 'description' => 'Harus sama dengan nama cabang di sistem.'],
        ['name' => 'nama_tahun_ajaran', 'required' => false, 'description' => 'Jika kosong, sistem memakai tahun ajaran aktif.'],
        ['name' => 'kuota_siswa', 'required' => false, 'description' => 'Jika kosong, kuota dibuat 30 siswa.'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <header class="flex flex-wrap items-center justify-between gap-3"><a href="{{ route($routePrefix . '.kelas.index') }}" class="inline-flex min-h-10 shrink-0 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-bold text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar kelas</a><a href="{{ route($routePrefix . '.kelas.template') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-emerald-50 px-4 text-sm font-bold text-emerald-700 no-underline hover:bg-emerald-100"><i class="fas fa-file-arrow-down" aria-hidden="true"></i>Unduh template Excel</a></header>

    <section class="grid gap-4">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-file-import mr-2 text-brand-600" aria-hidden="true"></i>Unggah file kelas</h3><p class="mt-1 text-xs text-slate-500">Sistem akan memvalidasi setiap baris sebelum menyimpan.</p></header>
            <form action="{{ route($routePrefix . '.kelas.import.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-5">@csrf
                <label for="file" class="block rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 text-center transition focus-within:border-brand-500 focus-within:bg-brand-50/50 sm:p-8"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl text-brand-600 shadow-sm"><i class="fas fa-file-excel" aria-hidden="true"></i></span><span class="mt-3 block text-sm font-extrabold text-slate-800">Pilih file dari perangkat</span><span class="mt-1 block text-xs text-slate-500">Excel .xlsx atau .xls, maksimal 5 MB</span><input id="file" type="file" name="file" accept=".xlsx,.xls" required class="mt-4 block w-full cursor-pointer rounded-xl border border-slate-300 bg-white text-xs text-slate-600 file:mr-3 file:border-0 file:bg-brand-600 file:px-4 file:py-2.5 file:font-bold file:text-white hover:file:bg-brand-700"></label>
                @error('file')<p class="mt-2 text-xs font-semibold text-red-600"><i class="fas fa-circle-exclamation mr-1" aria-hidden="true"></i>{{ $message }}</p>@enderror
                <div class="mt-4 flex justify-end gap-2"><a href="{{ route($routePrefix . '.kelas.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-upload" aria-hidden="true"></i>Import data</button></div>
            </form>
        </section>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950">Format kolom template</h3><p class="mt-1 text-xs text-slate-500">Nama kolom tidak boleh diubah.</p></header>
        <div class="divide-y divide-slate-100 md:hidden">@foreach($columns as $column)<div class="p-4"><div class="flex items-center justify-between gap-3"><code class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-xs font-bold text-slate-700">{{ $column['name'] }}</code><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $column['required'] ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600' }}">{{ $column['required'] ? 'Wajib' : 'Opsional' }}</span></div><p class="mt-2 text-xs leading-5 text-slate-600">{{ $column['description'] }}</p></div>@endforeach</div>
        <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[640px] table-fixed text-left text-xs"><colgroup><col class="w-52"><col class="w-28"><col></colgroup><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Kolom</th><th class="px-3 py-3">Wajib</th><th class="px-5 py-3">Keterangan</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($columns as $column)<tr><td class="px-5 py-3"><code class="whitespace-nowrap rounded-lg bg-slate-100 px-2 py-1 font-mono text-[11px] font-bold text-slate-700">{{ $column['name'] }}</code></td><td class="px-3 py-3"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $column['required'] ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600' }}">{{ $column['required'] ? 'Ya' : 'Opsional' }}</span></td><td class="px-5 py-3 leading-5 text-slate-600">{{ $column['description'] }}</td></tr>@endforeach</tbody></table></div>
    </section>
</div>
@endsection
