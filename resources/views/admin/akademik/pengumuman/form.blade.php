@extends('layouts.app')

@section('title', isset($pengumuman) ? 'Edit Pengumuman' : 'Tambah Pengumuman')
@section('page-title', isset($pengumuman) ? 'Edit Pengumuman' : 'Tambah Pengumuman')
@section('page-subtitle', isset($pengumuman) ? 'Perbarui informasi tanpa mengubah kalender sumber' : 'Buat informasi baru untuk warga sekolah')

@section('content')
@php
    $routeBase = request()->routeIs('sekretaris.*') ? 'sekretaris' : 'admin.akademik';
    $isEdit = isset($pengumuman);
    $inputClass = 'mt-1.5 h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20';
    $textareaClass = 'mt-1.5 min-h-36 w-full resize-y rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20';
@endphp

<div class="min-w-0 w-full">
    <div class="mb-4 flex items-center gap-3"><a href="{{ url()->previous(route($routeBase . '.pengumuman.index')) }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a><div class="min-w-0"><h2 class="text-base font-extrabold text-slate-900">{{ $isEdit ? 'Perbarui pengumuman' : 'Pengumuman baru' }}</h2><p class="mt-0.5 text-xs text-slate-500">Kolom bertanda bintang wajib diisi.</p></div></div>

    @if($errors->any())
        <section class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs text-red-800"><p class="font-extrabold"><i class="fas fa-exclamation-circle mr-1.5" aria-hidden="true"></i>Periksa kembali data berikut:</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></section>
    @endif

    <form action="{{ $isEdit ? route($routeBase . '.pengumuman.update', $pengumuman->id) : route($routeBase . '.pengumuman.store') }}" method="POST" enctype="multipart/form-data" class="grid min-w-0 gap-5">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <input type="hidden" name="_return_url" value="{{ url()->previous(route($routeBase . '.pengumuman.index')) }}">

        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-bullhorn text-brand-600" aria-hidden="true"></i>Isi pengumuman</h3><p class="mt-1 text-xs text-slate-500">Gunakan judul singkat dan isi yang langsung menjelaskan tindakan pengguna.</p></header>
            <div class="grid gap-5 p-4 sm:p-5">
                <label class="block text-xs font-bold text-slate-700">Kegiatan kalender <span class="font-normal text-slate-400">(opsional)</span><select name="kalender_akademik_id" class="{{ $inputClass }} @error('kalender_akademik_id') !border-red-400 @enderror"><option value="">Tidak terhubung ke kalender</option>@foreach($kalender as $k)<option value="{{ $k->id }}" {{ (string) old('kalender_akademik_id', $pengumuman->kalender_akademik_id ?? '') === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kegiatan }} · {{ $k->tanggal_mulai->format('d M Y') }}</option>@endforeach</select><span class="mt-1.5 block text-[11px] font-normal leading-4 text-slate-500">Pilih hanya bila pengumuman berkaitan langsung dengan suatu kegiatan.</span>@error('kalender_akademik_id')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>

                <label class="block text-xs font-bold text-slate-700">Judul pengumuman <span class="text-red-500">*</span><input type="text" name="judul" value="{{ old('judul', $pengumuman->judul ?? '') }}" maxlength="255" required placeholder="Contoh: Libur semester dimulai 20 Desember" class="{{ $inputClass }} @error('judul') !border-red-400 @enderror">@error('judul')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>

                <label class="block text-xs font-bold text-slate-700">Isi pengumuman <span class="text-red-500">*</span><textarea name="isi_pengumuman" required placeholder="Tuliskan informasi, tanggal, dan hal yang perlu dilakukan penerima..." class="{{ $textareaClass }} @error('isi_pengumuman') !border-red-400 @enderror">{{ old('isi_pengumuman', $pengumuman->isi_pengumuman ?? '') }}</textarea>@error('isi_pengumuman')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>

                <div class="grid gap-4 sm:grid-cols-3">
                    <label class="block text-xs font-bold text-slate-700">Tanggal tayang <span class="text-red-500">*</span><input type="date" name="tanggal_pengumuman" value="{{ old('tanggal_pengumuman', $isEdit ? $pengumuman->tanggal_pengumuman?->format('Y-m-d') : now()->format('Y-m-d')) }}" required class="{{ $inputClass }} @error('tanggal_pengumuman') !border-red-400 @enderror">@error('tanggal_pengumuman')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block text-xs font-bold text-slate-700">Prioritas <span class="text-red-500">*</span><select name="prioritas" required class="{{ $inputClass }} @error('prioritas') !border-red-400 @enderror">@foreach(['biasa' => 'Biasa', 'penting' => 'Penting', 'mendesak' => 'Mendesak'] as $value => $label)<option value="{{ $value }}" {{ old('prioritas', $pengumuman->prioritas ?? 'biasa') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>@error('prioritas')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-red-500">*</span><select name="status" required class="{{ $inputClass }} @error('status') !border-red-400 @enderror">@foreach(['aktif' => 'Aktif', 'draft' => 'Draft', 'arsip' => 'Arsip'] as $value => $label)<option value="{{ $value }}" {{ old('status', $pengumuman->status ?? 'aktif') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>@error('status')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                </div>

                <label class="block text-xs font-bold text-slate-700">Lampiran surat <span class="font-normal text-slate-400">(PDF, maks. 5 MB)</span>@if($isEdit && $pengumuman->lampiran_surat)<a href="{{ asset('storage/' . $pengumuman->lampiran_surat) }}" target="_blank" class="mt-2 flex w-fit items-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-[11px] font-bold text-brand-700 no-underline hover:bg-brand-100"><i class="fas fa-file-pdf" aria-hidden="true"></i>Lihat lampiran saat ini</a>@endif<input type="file" name="lampiran_surat" accept=".pdf,application/pdf" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-xs text-slate-600 file:mr-3 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-xs file:font-bold file:text-slate-700 hover:file:bg-slate-200 @error('lampiran_surat') !border-red-400 @enderror">@error('lampiran_surat')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
            </div>
            <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50/70 p-4 sm:flex-row sm:justify-end"><a href="{{ url()->previous(route($routeBase . '.pengumuman.index')) }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-white px-5 text-xs font-bold text-slate-700 no-underline ring-1 ring-inset ring-slate-200 hover:bg-slate-100">Batal</a><button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>{{ $isEdit ? 'Simpan perubahan' : 'Terbitkan pengumuman' }}</button></footer>
        </section>

    </form>
</div>
@endsection
