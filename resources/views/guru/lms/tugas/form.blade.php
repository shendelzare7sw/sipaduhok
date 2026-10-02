@extends('layouts.lms-guru')

@php
    $isEdit = isset($tugas);
    $args = [$kelas->id, $mapel->id];
    $bisaDiulang = (bool) old('bisa_diulang', $isEdit ? $tugas->bisa_diulang : false);
    $tampilkanNilai = (bool) old('tampilkan_nilai', $isEdit ? $tugas->tampilkan_nilai : ! old('_token'));
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

@section('title', $isEdit ? 'Edit Tugas' : 'Buat Tugas')
@section('page-title', $isEdit ? 'Edit Tugas' : 'Buat Tugas Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route('guru.lms.tugas.index', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar tugas</a>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-bold">Periksa kembali isian berikut:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route('guru.lms.tugas.update', [...$args, $tugas->id]) : route('guru.lms.tugas.store', $args) }}"
          method="POST" enctype="multipart/form-data"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          x-data="{ ulang: @js($bisaDiulang), batas: @js((string) old('batas_pengulangan', $isEdit ? ($tugas->batas_pengulangan ?? '') : '')) }">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <header class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-circle-plus' }} text-indigo-600" aria-hidden="true"></i>{{ $isEdit ? 'Perbarui tugas' : 'Tugas baru' }}</h2>
            <p class="mt-1 text-xs text-slate-500">Tulis instruksi, tentukan periode pengumpulan, lalu atur penilaian. Tanda <span class="text-rose-600">*</span> wajib diisi.</p>
        </header>

        <div class="space-y-5 px-4 py-5 sm:px-6">
            <label class="block text-xs font-bold text-slate-700">Judul tugas <span class="text-rose-600">*</span>
                <input type="text" name="judul_tugas" value="{{ old('judul_tugas', $isEdit ? $tugas->judul_tugas : '') }}" required class="{{ $input }}">
                @error('judul_tugas')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <label class="block text-xs font-bold text-slate-700">Deskripsi / instruksi <span class="text-rose-600">*</span>
                <textarea name="deskripsi" rows="5" required class="{{ $input }}">{{ old('deskripsi', $isEdit ? $tugas->deskripsi : '') }}</textarea>
                @error('deskripsi')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-xs font-bold text-slate-700">Tanggal mulai <span class="text-rose-600">*</span>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $isEdit ? $tugas->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) }}" required class="{{ $input }}">
                    @error('tanggal_mulai')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                <label class="block text-xs font-bold text-slate-700">Tanggal deadline <span class="text-rose-600">*</span>
                    <input type="date" name="tanggal_deadline" value="{{ old('tanggal_deadline', $isEdit ? $tugas->tanggal_deadline->format('Y-m-d') : '') }}" required class="{{ $input }}">
                    @error('tanggal_deadline')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
            </div>

            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                <label class="block text-xs font-bold text-slate-700">File tugas (opsional)
                    <input type="file" name="file_tugas" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700">
                </label>
                <p class="mt-1 text-xs text-slate-500">{{ $isEdit ? 'Kosongkan jika tidak ingin mengganti file.' : 'Unggah soal dalam bentuk file jika diperlukan (maks. 10 MB).' }}</p>
                @error('file_tugas')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                @if($isEdit && $tugas->file_tugas)
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">File saat ini: <x-file-preview :path="$tugas->file_tugas" label="Lihat file saat ini" /></div>
                @endif
            </div>

            <fieldset class="space-y-3">
                <legend class="text-sm font-extrabold text-slate-900">Penilaian &amp; pengulangan</legend>
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <input type="checkbox" name="tampilkan_nilai" value="1" @checked($tampilkanNilai) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Tampilkan nilai ke siswa</strong>Jika dinonaktifkan, siswa tidak bisa melihat nilai meskipun sudah dinilai.</span>
                </label>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="bisa_diulang" value="1" x-model="ulang" @change="if (ulang && batas === '') batas = '2'" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Izinkan siswa mengedit jawaban</strong>Siswa dapat mengubah jawabannya sebelum deadline.</span>
                    </label>
                    <label x-cloak x-show="ulang" class="ml-7 mt-3 block text-xs font-bold text-slate-700 sm:max-w-xs">Batas edit (kali)
                        <input type="number" name="batas_pengulangan" min="0" x-model="batas" placeholder="Kosongkan jika tak terbatas" class="{{ $input }}">
                        <span class="mt-1 block font-normal text-slate-500">Biarkan kosong agar siswa bisa mengedit tanpa batas selama belum deadline.</span>
                    </label>
                </div>
            </fieldset>

            @include('guru.partials.multi-kelas-selector')
        </div>

        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('guru.lms.tugas.index', $args) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>{{ $isEdit ? 'Simpan perubahan' : 'Buat tugas' }}</button>
        </footer>
    </form>
</div>
@endsection
