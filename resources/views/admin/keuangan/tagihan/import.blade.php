@extends('layouts.app')

@section('title', 'Import Tagihan')
@section('page-title', 'Import Tagihan')
@section('page-subtitle', 'Tambahkan tagihan dari file Excel secara terarah')

@section('content')
<div data-tagihan-import class="min-w-0 w-full space-y-5" x-data="{
    file: null,
    dragging: false,
    setFile(file) { this.file = file || null; },
    clearFile() { this.file = null; this.$refs.fileInput.value = ''; },
    formatSize(bytes) { return bytes < 1048576 ? `${(bytes / 1024).toFixed(1)} KB` : `${(bytes / 1048576).toFixed(1)} MB`; },
    handleDrop(event) { this.dragging = false; const dropped = event.dataTransfer.files?.[0]; if (!dropped) return; this.$refs.fileInput.files = event.dataTransfer.files; this.setFile(dropped); },
    async submitImport(event) {
        if (!this.file) return;
        const result = await Swal.fire({ icon: 'question', title: 'Import tagihan sekarang?', text: `${this.file.name} akan diproses untuk tahun ajaran yang dipilih.`, showCancelButton: true, confirmButtonText: 'Ya, import', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true });
        if (result.isConfirmed) event.target.submit();
    }
}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left"></i>Kembali ke tagihan</a>
        <a href="{{ route('admin.keuangan.tagihan.template') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-emerald-50 px-4 text-sm font-bold text-emerald-700 no-underline hover:bg-emerald-100"><i class="fas fa-file-arrow-down"></i>Unduh template Excel</a>
    </div>

    <div class="grid gap-5 lg:grid-cols-[minmax(16rem,.65fr)_minmax(0,1.35fr)]">
        <aside class="self-start rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <div class="flex items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><i class="fas fa-circle-info"></i></span><div><h2 class="font-extrabold text-blue-950">Urutan import</h2><p class="mt-1 text-sm leading-6 text-blue-800">Gunakan template agar nama kolom terbaca dengan benar.</p></div></div>
            <ol class="mt-5 space-y-4">
                @foreach([
                    ['Pilih tahun ajaran', 'Tagihan akan dicatat pada periode ini.'],
                    ['Isi identitas siswa', 'Gunakan NIS, NISN, atau nama_siswa. Salah satu wajib diisi.'],
                    ['Lengkapi tagihan', 'jenis_tagihan dan jumlah wajib diisi.'],
                    ['Unggah dan periksa hasil', 'Status awal otomatis menjadi belum bayar.'],
                ] as [$title, $description])
                    <li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-extrabold text-blue-700 ring-1 ring-blue-200">{{ $loop->iteration }}</span><div><strong class="block text-sm text-blue-950">{{ $title }}</strong><span class="mt-0.5 block text-xs leading-5 text-blue-800">{{ $description }}</span></div></li>
                @endforeach
            </ol>
            <div class="mt-5 rounded-xl border border-blue-200 bg-white/70 p-3 text-xs leading-5 text-blue-900"><i class="fas fa-shield-halved mr-1 text-blue-700"></i>Baris yang tidak valid akan dilewati dan dirangkum setelah proses selesai.</div>
        </aside>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-5"><h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-950"><i class="fas fa-file-import text-brand-600"></i>Unggah file tagihan</h2><p class="mt-1 text-sm text-slate-500">Pilih periode dahulu, lalu lampirkan berkas Excel maksimal 5 MB.</p></header>

            <form action="{{ route('admin.keuangan.tagihan.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 p-5" @submit.prevent="submitImport($event)">
                @csrf
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-slate-800">Tahun ajaran <span class="text-red-600">*</span></span>
                    <select name="tahun_ajaran_id" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" @selected($ta->is_active)>{{ $ta->nama_tahun_ajaran }} · {{ $ta->tanggal_mulai->format('d/m/Y') }}–{{ $ta->tanggal_selesai->format('d/m/Y') }}{{ $ta->is_active ? ' · Aktif' : '' }}</option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran_id')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
                </label>

                <div>
                    <span class="mb-2 block text-sm font-bold text-slate-800">File Excel <span class="text-red-600">*</span></span>
                    <input x-ref="fileInput" type="file" name="file" accept=".xlsx,.xls" required class="sr-only" @change="setFile($event.target.files?.[0])">

                    <button
                        x-show="!file"
                        type="button"
                        @click="$refs.fileInput.click()"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleDrop($event)"
                        :class="dragging ? 'border-brand-500 bg-brand-50' : 'border-slate-300 bg-slate-50 hover:border-brand-400 hover:bg-brand-50/50'"
                        class="flex min-h-56 w-full flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition"
                    >
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-xl text-brand-600 shadow-sm ring-1 ring-slate-200"><i class="fas fa-cloud-arrow-up"></i></span>
                        <strong class="mt-4 text-sm text-slate-900">Pilih file atau tarik ke area ini</strong>
                        <span class="mt-1 text-xs text-slate-500">Format XLSX atau XLS · Maksimal 5 MB</span>
                    </button>

                    <div x-cloak x-show="file" class="flex min-h-32 items-center gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-xl text-emerald-700 ring-1 ring-emerald-200"><i class="fas fa-file-excel"></i></span>
                        <div class="min-w-0 flex-1"><strong class="block truncate text-sm text-emerald-950" x-text="file?.name"></strong><span class="mt-1 block text-xs text-emerald-700" x-text="file ? formatSize(file.size) : ''"></span></div>
                        <button type="button" @click="clearFile" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-red-600 ring-1 ring-emerald-200 hover:bg-red-50" aria-label="Hapus file"><i class="fas fa-times"></i></button>
                    </div>
                    @error('file')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
                    <button type="submit" :disabled="!file" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-40"><i class="fas fa-upload"></i>Import data</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
