@extends('layouts.lms-guru')

@php
    $isEdit = isset($materi);
    $args = [$kelas->id, $mapel->id];
    $selectedTipeFile = old('tipe_file', $isEdit ? $materi->tipe_file : null);
    $selectedKategori = old('kategori', $isEdit ? $materi->kategori : ($kategori ?? 'materi'));
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

@section('title', $isEdit ? 'Edit Materi' : 'Tambah Materi')
@section('page-title', $isEdit ? 'Edit Materi' : 'Tambah Materi Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route('guru.lms.materi.index', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar materi</a>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-bold">Periksa kembali isian berikut:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route('guru.lms.materi.update', [...$args, $materi->id]) : route('guru.lms.materi.store', $args) }}"
          method="POST" enctype="multipart/form-data"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          x-data="{
              tipe: @js((string) $selectedTipeFile),
              accept: { pdf: '.pdf', ppt: '.ppt,.pptx', doc: '.doc,.docx', video: '.mp4,.avi,.mov,.mkv,.webm' },
              get isLink() { return this.tipe === 'link'; },
              get isFile() { return this.tipe !== '' && ! this.isLink; },
          }">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <header class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-circle-plus' }} text-indigo-600" aria-hidden="true"></i>{{ $isEdit ? 'Perbarui materi' : 'Materi baru' }}</h2>
            <p class="mt-1 text-xs text-slate-500">Isi identitas materi, pilih jenis berkas, lalu unggah file atau tempel tautan. Tanda <span class="text-rose-600">*</span> wajib diisi.</p>
        </header>

        <div class="space-y-5 px-4 py-5 sm:px-6">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-xs font-bold text-slate-700">Judul materi <span class="text-rose-600">*</span>
                    <input type="text" name="judul_materi" value="{{ old('judul_materi', $isEdit ? $materi->judul_materi : '') }}" required class="{{ $input }} @error('judul_materi') !border-rose-400 @enderror">
                    @error('judul_materi')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                <label class="block text-xs font-bold text-slate-700">Kategori <span class="text-rose-600">*</span>
                    <select name="kategori" required class="{{ $input }}">
                        <option value="materi" @selected($selectedKategori == 'materi')>Materi pendukung</option>
                        <option value="modul_ajar" @selected($selectedKategori == 'modul_ajar')>Modul ajar (utama)</option>
                    </select>
                    @error('kategori')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
            </div>

            <label class="block text-xs font-bold text-slate-700">Deskripsi
                <textarea name="deskripsi" rows="4" class="{{ $input }}">{{ old('deskripsi', $isEdit ? $materi->deskripsi : '') }}</textarea>
            </label>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-xs font-bold text-slate-700">Tipe file <span class="text-rose-600">*</span>
                    <select name="tipe_file" x-model="tipe" @change="if ($refs.file) $refs.file.value = ''" required class="{{ $input }} @error('tipe_file') !border-rose-400 @enderror">
                        @unless($isEdit)<option value="">-- Pilih tipe --</option>@endunless
                        <option value="pdf">PDF</option>
                        <option value="video">Video</option>
                        <option value="ppt">PowerPoint</option>
                        <option value="doc">Dokumen</option>
                        <option value="link">Tautan URL</option>
                    </select>
                    @error('tipe_file')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                <label class="block text-xs font-bold text-slate-700">Tanggal upload <span class="text-rose-600">*</span>
                    <input type="date" name="tanggal_upload" value="{{ old('tanggal_upload', $isEdit ? $materi->tanggal_upload->format('Y-m-d') : date('Y-m-d')) }}" required class="{{ $input }}">
                </label>
            </div>

            <div x-cloak x-show="{{ $isEdit ? '! isLink' : 'isFile' }}" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                <label class="block text-xs font-bold text-slate-700">File materi @unless($isEdit)<span class="text-rose-600">*</span>@endunless
                    <input type="file" name="file_materi" x-ref="file" :accept="accept[tipe] ?? null" :required="{{ $isEdit ? 'false' : 'isFile' }}" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700">
                </label>
                <p class="mt-1 text-xs text-slate-500">{{ $isEdit ? 'Kosongkan jika tidak ingin mengganti file.' : 'Maksimal 50 MB.' }}</p>
                @error('file_materi')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                @if($isEdit && $materi->file_materi && $materi->tipe_file != 'link')
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-600">File saat ini: <x-file-preview :path="$materi->file_materi" label="Lihat file saat ini" /></div>
                @endif
            </div>

            <div x-cloak x-show="isLink" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                <label class="block text-xs font-bold text-slate-700">URL tautan <span class="text-rose-600">*</span>
                    <input type="url" name="url_materi" value="{{ old('url_materi', $isEdit ? ($materi->url_materi ?? '') : '') }}" :required="isLink" placeholder="https://example.com" class="{{ $input }}">
                </label>
                <p class="mt-1 text-xs text-slate-500">Contoh: https://youtu.be/... atau tautan dokumentasi lainnya.</p>
                @error('url_materi')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>

            @include('guru.partials.multi-kelas-selector')
        </div>

        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('guru.lms.materi.index', $args) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>{{ $isEdit ? 'Simpan perubahan' : 'Simpan materi' }}</button>
        </footer>
    </form>
</div>
@endsection
