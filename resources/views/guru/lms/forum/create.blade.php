@extends('layouts.lms-guru')

@section('title', 'Buat Diskusi Baru')
@section('page-title', 'Buat Diskusi Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php
    $args = [$kelas->id, $mapel->id];
    $backUrl = ! empty($pertemuanId) ? route('guru.lms.meeting.index', $args) : route('guru.lms.forum.index', $args);
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

<div class="min-w-0 w-full space-y-5">
    <a href="{{ $backUrl }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>{{ ! empty($pertemuanId) ? 'Kembali ke meeting' : 'Kembali ke forum' }}</a>

    <form action="{{ route('guru.lms.forum.store', $args) }}" method="POST" enctype="multipart/form-data" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          x-data="{
              berkas: [],
              batas: 10 * 1024 * 1024,
              pilih(input) {
                  this.berkas.forEach((b) => b.url && URL.revokeObjectURL(b.url));
                  this.berkas = [...input.files].map((f) => ({
                      nama: f.name,
                      ukuran: f.size < 1048576 ? Math.max(1, Math.round(f.size / 1024)) + ' KB' : (f.size / 1048576).toFixed(1) + ' MB',
                      gambar: f.type.startsWith('image/'),
                      url: f.type.startsWith('image/') ? URL.createObjectURL(f) : null,
                      terlalu: f.size > this.batas,
                  }));
              },
              hapus(index) {
                  const dt = new DataTransfer();
                  [...this.$refs.lampiran.files].forEach((f, i) => { if (i !== index) dt.items.add(f); });
                  this.$refs.lampiran.files = dt.files;
                  this.pilih(this.$refs.lampiran);
              },
              get adaTerlalu() { return this.berkas.some((b) => b.terlalu); },
          }"
          @submit="if (adaTerlalu) { $event.preventDefault(); window.Swal?.fire({ icon: 'warning', title: 'Ukuran file terlalu besar', text: 'Setiap lampiran maksimal 10 MB. Hapus file yang ditandai merah.' }); }">
        @csrf
        <header class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-circle-plus text-indigo-600" aria-hidden="true"></i>Diskusi baru</h2>
            <p class="mt-1 text-xs text-slate-500">Tulis topik dan pertanyaan pemantik agar siswa mudah memulai diskusi.</p>
        </header>

        <div class="space-y-5 px-4 py-5 sm:px-6">
            <label class="block text-xs font-bold text-slate-700">Topik diskusi <span class="text-rose-600">*</span>
                <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Diskusi Materi Aljabar" required class="{{ $input }}">
                @error('judul')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <label class="block text-xs font-bold text-slate-700">Isi diskusi / pertanyaan pemicu <span class="text-rose-600">*</span>
                <textarea name="isi" rows="6" placeholder="Tuliskan materi diskusi atau pertanyaan pemantik di sini..." required class="{{ $input }}">{{ old('isi') }}</textarea>
                @error('isi')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <div>
                <p class="text-xs font-bold text-slate-700">Lampiran <span class="font-normal text-slate-500">(opsional)</span></p>
                <label class="mt-1 flex cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-indigo-300 hover:bg-indigo-50/50">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-indigo-500" aria-hidden="true"></i>
                    <span class="text-sm font-bold text-slate-700">Pilih gambar, dokumen, atau video</span>
                    <span class="text-[11px] text-slate-500">JPG, PNG, GIF, PDF, Word, Excel, PowerPoint, MP4/AVI/MOV · bisa lebih dari satu · maks. 10 MB per file</span>
                    <input type="file" name="lampiran[]" x-ref="lampiran" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.avi,.mov" @change="pilih($el)" class="sr-only">
                </label>
                @error('lampiran')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                @error('lampiran.*')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror

                <ul x-cloak x-show="berkas.length" class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="(b, i) in berkas" :key="b.nama + i">
                        <li class="flex min-w-0 items-center gap-3 rounded-xl border bg-white p-2" :class="b.terlalu ? 'border-rose-300 bg-rose-50' : 'border-slate-200'">
                            <template x-if="b.gambar"><img :src="b.url" alt="" class="h-12 w-12 shrink-0 rounded-lg object-cover"></template>
                            <template x-if="! b.gambar"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="fa-solid fa-file-lines" aria-hidden="true"></i></span></template>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-xs font-bold text-slate-800" x-text="b.nama"></span>
                                <span class="block text-[11px]" :class="b.terlalu ? 'font-bold text-rose-700' : 'text-slate-500'" x-text="b.terlalu ? b.ukuran + ' · melebihi 10 MB' : b.ukuran"></span>
                            </span>
                            <button type="button" @click="hapus(i)" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600" :aria-label="'Hapus ' + b.nama"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                        </li>
                    </template>
                </ul>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned')) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Sematkan diskusi</strong>Diskusi yang disematkan muncul paling atas.</span>
            </label>

            @include('guru.partials.multi-kelas-selector')
        </div>

        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i>Mulai diskusi</button>
        </footer>
    </form>
</div>
@endsection
