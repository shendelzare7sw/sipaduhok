@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $editing = isset($mataPelajaran);
    $backUrl = $editing ? url()->previous(route('admin.mata-pelajaran.index')) : route('admin.mata-pelajaran.index');
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
    $errorClass = 'mt-1.5 block text-xs font-semibold text-red-600';
@endphp

<div class="min-w-0 w-full space-y-4">
    <header class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ $backUrl }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
            <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-brand-600">Data akademik</p><h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $editing ? 'Edit mata pelajaran' : 'Tambah mata pelajaran' }}</h2><p class="mt-0.5 text-sm text-slate-500">Tentukan identitas, jenjang, dan aturan akses siswa.</p></div>
        </div>
    </header>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-extrabold"><i class="fas fa-circle-exclamation mr-2" aria-hidden="true"></i>Data belum dapat disimpan.</p><ul class="mt-2 list-disc space-y-1 pl-5 text-xs">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ $editing ? route('admin.mata-pelajaran.update', $mataPelajaran) : route('admin.mata-pelajaran.store') }}" method="POST" class="space-y-4" x-data="codeSuggestions(@js(route('admin.mata-pelajaran.suggest-kode')))" data-mapel-form>
        @csrf
        @if($editing)
            @method('PUT')
            <input type="hidden" name="_return_url" value="{{ $backUrl }}">
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-book-open" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">Identitas mata pelajaran</h3><p class="mt-0.5 text-xs text-slate-500">Informasi yang tampil pada kelas, jadwal, LMS, dan laporan.</p></div></div>
            <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="nama_mapel" class="{{ $labelClass }}">Nama mata pelajaran <span class="text-red-500">*</span></label>
                    <input id="nama_mapel" type="text" name="nama_mapel" value="{{ old('nama_mapel', $editing ? $mataPelajaran->nama_mapel : null) }}" class="{{ $inputClass }}" maxlength="100" placeholder="Contoh: Matematika" required autofocus>
                    @error('nama_mapel') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="jenjang" class="{{ $labelClass }}">Jenjang pendidikan <span class="text-red-500">*</span></label>
                    <select id="jenjang" name="jenjang" x-ref="level" class="{{ $inputClass }}" required><option value="">Pilih jenjang</option>@foreach($jenjangList as $jenjang)<option value="{{ $jenjang }}" @selected(old('jenjang', $editing ? $mataPelajaran->jenjang : null) === $jenjang)>{{ $jenjang }}</option>@endforeach</select>
                    @error('jenjang') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="kelompok" class="{{ $labelClass }}">Kelompok <span class="font-normal text-slate-400">(opsional)</span></label>
                    <select id="kelompok" name="kelompok" class="{{ $inputClass }}"><option value="">Belum ditentukan</option><option value="A" @selected(old('kelompok', $editing ? $mataPelajaran->kelompok : null) === 'A')>A — pelajaran umum</option><option value="B" @selected(old('kelompok', $editing ? $mataPelajaran->kelompok : null) === 'B')>B — pilihan / muatan lokal</option></select>
                    @error('kelompok') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-sliders" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">Kode dan aturan akses</h3><p class="mt-0.5 text-xs text-slate-500">Kode harus unik. Filter agama hanya digunakan untuk pelajaran agama.</p></div></div>
            <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <div>
                    <label for="kode_mapel" class="{{ $labelClass }}">Kode mata pelajaran <span class="text-red-500">*</span></label>
                    <div class="mt-1.5 flex gap-2"><input id="kode_mapel" type="text" name="kode_mapel" x-ref="code" value="{{ old('kode_mapel', $editing ? $mataPelajaran->kode_mapel : null) }}" class="min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 font-mono text-sm uppercase text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" maxlength="20" placeholder="Contoh: SMP-001" required><button type="button" x-on:click="load" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i><span class="hidden sm:inline">Cari saran</span></button></div>
                    <div x-cloak x-show="open" x-transition class="[&[x-cloak]]:hidden mt-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3"><p x-text="message" class="text-xs font-semibold text-emerald-800"></p><div class="mt-2 flex flex-wrap gap-2"><template x-for="suggestion in suggestions" x-bind:key="suggestion"><button type="button" x-on:click="select(suggestion)" x-text="suggestion" class="rounded-lg border border-emerald-200 bg-white px-3 py-2 font-mono text-xs font-bold text-emerald-700 hover:bg-emerald-50"></button></template></div></div>
                    @error('kode_mapel') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="filter_agama" class="{{ $labelClass }}">Akses berdasarkan agama <span class="font-normal text-slate-400">(opsional)</span></label>
                    <select id="filter_agama" name="filter_agama" class="{{ $inputClass }}"><option value="">Semua siswa dapat mengakses</option>@foreach($agamaList as $agama)<option value="{{ $agama }}" @selected(old('filter_agama', $editing ? $mataPelajaran->filter_agama : null) === $agama)>Hanya siswa {{ $agama }}</option>@endforeach</select>
                    <p class="mt-1.5 text-xs leading-5 text-slate-500">Kosongkan untuk pelajaran umum. Pilihan ini memengaruhi akses LMS siswa.</p>
                    @error('filter_agama') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="deskripsi" class="{{ $labelClass }}">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="{{ $inputClass }}" placeholder="Keterangan singkat mengenai cakupan mata pelajaran">{{ old('deskripsi', $editing ? $mataPelajaran->deskripsi : null) }}</textarea>
                    @error('deskripsi') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <div class="sticky bottom-3 z-10 flex items-center justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none"><a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>{{ $editing ? 'Simpan perubahan' : 'Simpan mata pelajaran' }}</button></div>
    </form>
</div>
