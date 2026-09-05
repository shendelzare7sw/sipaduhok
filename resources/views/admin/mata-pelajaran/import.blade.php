@extends('layouts.app')

@section('title', 'Import Mata Pelajaran')
@section('page-title', 'Import Mata Pelajaran')
@section('page-subtitle', 'Masukkan banyak pelajaran dari template Excel')

@section('content')
@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $columns = [
        ['name' => 'kode_mapel', 'required' => true, 'description' => 'Kode unik, misalnya MTK-SD.'],
        ['name' => 'nama_mapel', 'required' => true, 'description' => 'Nama mata pelajaran.'],
        ['name' => 'jenjang', 'required' => true, 'description' => 'KB, TKA, TKB, SD, SMP, atau SMA.'],
        ['name' => 'kelompok', 'required' => false, 'description' => 'A untuk umum atau B untuk pilihan/muatan lokal.'],
        ['name' => 'filter_agama', 'required' => false, 'description' => 'Isi hanya untuk pelajaran agama; kosongkan untuk semua siswa.'],
        ['name' => 'deskripsi', 'required' => false, 'description' => 'Keterangan tambahan pelajaran.'],
    ];
@endphp

<div class="min-w-0 w-full space-y-4">
    <header class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-3"><a href="{{ route('admin.mata-pelajaran.index') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-brand-600">Import data</p><h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">Import mata pelajaran</h2><p class="mt-0.5 text-sm text-slate-500">Gunakan template resmi agar urutan dan format kolom sesuai.</p></div></div>
        <a href="{{ route('admin.mata-pelajaran.template') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-emerald-50 px-4 text-xs font-bold text-emerald-700 no-underline hover:bg-emerald-100"><i class="fas fa-file-arrow-down" aria-hidden="true"></i>Unduh template</a>
    </header>

    <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(20rem,0.75fr)]">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-file-excel" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">Pilih file Excel</h3><p class="mt-0.5 text-xs text-slate-500">Format .xlsx atau .xls, maksimal 5 MB.</p></div></div>
            <form action="{{ route('admin.mata-pelajaran.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-4 sm:p-5">
                @csrf
                <label for="file" class="block cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition hover:border-brand-400 hover:bg-brand-50/40 sm:p-8"><i class="fas fa-cloud-arrow-up text-3xl text-brand-500" aria-hidden="true"></i><span class="mt-3 block text-sm font-extrabold text-slate-800">Pilih file dari perangkat</span><span class="mt-1 block text-xs text-slate-500">Nama file akan tampil pada input setelah dipilih.</span><input id="file" type="file" name="file" accept=".xlsx,.xls" class="mt-4 block w-full cursor-pointer rounded-xl border border-slate-300 bg-white text-xs text-slate-600 file:mr-3 file:border-0 file:bg-brand-600 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-white hover:file:bg-brand-700" required></label>
                @error('file')<p class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700"><i class="fas fa-circle-exclamation mr-1" aria-hidden="true"></i>{{ $message }}</p>@enderror
                <div class="flex justify-end gap-2"><a href="{{ route('admin.mata-pelajaran.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-upload" aria-hidden="true"></i>Import data</button></div>
            </form>
        </article>

        <aside class="rounded-2xl border border-blue-200 bg-blue-50 p-4 sm:p-5"><h3 class="text-sm font-extrabold text-blue-900"><i class="fas fa-list-ol mr-2" aria-hidden="true"></i>Urutan yang benar</h3><ol class="mt-3 space-y-3 text-xs leading-5 text-blue-900"><li class="flex gap-2"><span class="font-extrabold">1.</span><span>Unduh template, lalu hapus baris contoh.</span></li><li class="flex gap-2"><span class="font-extrabold">2.</span><span>Isi data tanpa mengubah nama kolom.</span></li><li class="flex gap-2"><span class="font-extrabold">3.</span><span>Simpan sebagai Excel dan unggah di halaman ini.</span></li><li class="flex gap-2"><span class="font-extrabold">4.</span><span>Sistem melewati kode yang sudah ada dan menampilkan peringatannya.</span></li></ol></aside>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="text-sm font-extrabold text-slate-900">Format kolom template</h3><p class="mt-1 text-xs text-slate-500">Gunakan nilai sesuai keterangan agar proses import tidak melewati baris.</p></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[640px] table-fixed text-left text-xs"><colgroup><col class="w-44"><col class="w-24"><col></colgroup><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3 sm:px-5">Kolom</th><th class="px-4 py-3">Wajib</th><th class="px-4 py-3 sm:px-5">Keterangan</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($columns as $column)<tr><td class="px-4 py-3 sm:px-5"><code class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-[11px] font-bold text-slate-700">{{ $column['name'] }}</code></td><td class="px-4 py-3"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[9px] font-bold {{ $column['required'] ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600' }}">{{ $column['required'] ? 'Ya' : 'Opsional' }}</span></td><td class="px-4 py-3 leading-5 text-slate-600 sm:px-5">{{ $column['description'] }}</td></tr>@endforeach</tbody></table></div>
    </section>
</div>
@endsection
