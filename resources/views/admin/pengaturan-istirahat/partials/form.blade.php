@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $editing = isset($pengaturan);
    $lockedJenjang = !$editing && !empty($selectedJenjang);
    $backUrl = route('admin.pengaturan-istirahat.index');
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
    $errorClass = 'mt-1.5 block text-xs font-semibold text-red-600';
    $selectedDays = old('hari_aktif', $editing ? ($pengaturan->hari_aktif ?? []) : []);
@endphp

<div class="min-w-0 w-full space-y-4">
    <header class="flex min-w-0 items-center gap-3"><a href="{{ $backUrl }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-brand-600">Pengaturan jadwal</p><h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $editing ? 'Edit waktu istirahat' : 'Tambah waktu istirahat' }}</h2><p class="mt-0.5 text-sm text-slate-500">Atur waktu dan hari yang otomatis diblokir saat menyusun jadwal.</p></div></header>

    @if($errors->any())<div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><p class="font-extrabold">Data belum dapat disimpan.</p><ul class="mt-2 list-disc space-y-1 pl-5 text-xs">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form action="{{ $editing ? route('admin.pengaturan-istirahat.update', $pengaturan->id) : route('admin.pengaturan-istirahat.store') }}" method="POST" class="space-y-4">
        @csrf
        @if($editing) @method('PUT') @endif
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-mug-hot" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">Informasi istirahat</h3><p class="mt-0.5 text-xs text-slate-500">Maksimal dua urutan untuk setiap jenjang pada hari yang sama.</p></div></div>
            <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
                <div>
                    <label for="jenjang" class="{{ $labelClass }}">Jenjang <span class="text-red-500">*</span></label>
                    <select id="jenjang" name="{{ $lockedJenjang ? '' : 'jenjang' }}" class="{{ $inputClass }} {{ $lockedJenjang ? 'cursor-not-allowed bg-slate-100' : '' }}" @disabled($lockedJenjang) required><option value="">Pilih jenjang</option>@foreach($jenjangList as $jenjang)<option value="{{ $jenjang }}" @selected(old('jenjang', $editing ? $pengaturan->jenjang : ($selectedJenjang ?? null)) === $jenjang)>{{ $jenjang }}</option>@endforeach</select>
                    @if($lockedJenjang)<input type="hidden" name="jenjang" value="{{ $selectedJenjang }}"><p class="mt-1.5 text-xs font-semibold text-emerald-600"><i class="fas fa-lock mr-1" aria-hidden="true"></i>Jenjang dikunci dari halaman sebelumnya.</p>@endif
                    @error('jenjang')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror
                </div>
                <div class="sm:col-span-2 lg:col-span-1"><label for="nama_istirahat" class="{{ $labelClass }}">Nama istirahat <span class="text-red-500">*</span></label><input id="nama_istirahat" type="text" name="nama_istirahat" value="{{ old('nama_istirahat', $editing ? $pengaturan->nama_istirahat : null) }}" class="{{ $inputClass }}" maxlength="255" placeholder="Contoh: Istirahat pagi" required>@error('nama_istirahat')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror</div>
                <div><label for="urutan" class="{{ $labelClass }}">Urutan <span class="text-red-500">*</span></label><select id="urutan" name="urutan" class="{{ $inputClass }}" required><option value="">Pilih urutan</option><option value="1" @selected((string) old('urutan', $editing ? $pengaturan->urutan : '') === '1')>1 — istirahat pertama</option><option value="2" @selected((string) old('urutan', $editing ? $pengaturan->urutan : '') === '2')>2 — istirahat kedua</option></select>@error('urutan')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror</div>
                <div><label for="jam_mulai" class="{{ $labelClass }}">Jam mulai <span class="text-red-500">*</span></label><input id="jam_mulai" type="time" name="jam_mulai" value="{{ old('jam_mulai', $editing ? substr($pengaturan->jam_mulai, 0, 5) : null) }}" class="{{ $inputClass }}" required>@error('jam_mulai')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror</div>
                <div><label for="jam_selesai" class="{{ $labelClass }}">Jam selesai <span class="text-red-500">*</span></label><input id="jam_selesai" type="time" name="jam_selesai" value="{{ old('jam_selesai', $editing ? substr($pengaturan->jam_selesai, 0, 5) : null) }}" class="{{ $inputClass }}" required>@error('jam_selesai')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror</div>
                @if($editing)<label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 sm:self-end"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $pengaturan->is_active)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span><span class="block text-sm font-bold text-slate-800">Status aktif</span><span class="text-xs text-slate-500">Memblokir slot jadwal.</span></span></label>@endif
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950">Hari berlaku <span class="text-red-500">*</span></h3><p class="mt-0.5 text-xs text-slate-500">Pilih minimal satu hari. Pengaturan hanya memblokir hari yang dipilih.</p></div>
            <div class="grid grid-cols-2 gap-2 p-4 sm:grid-cols-5 sm:p-5">@foreach($hariList as $hari)<label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50"><input type="checkbox" name="hari_aktif[]" value="{{ $hari }}" @checked(in_array($hari, $selectedDays)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="text-xs font-bold text-slate-700">{{ $hari }}</span></label>@endforeach</div>
            @error('hari_aktif')<p class="px-4 pb-4 text-xs font-semibold text-red-600 sm:px-5">{{ $message }}</p>@enderror
        </section>

        <aside class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs leading-5 text-blue-900"><p class="font-extrabold"><i class="fas fa-circle-info mr-1" aria-hidden="true"></i>Pemeriksaan otomatis</p><p class="mt-1">Sistem menolak jam selesai yang lebih awal, urutan ganda pada hari yang sama, dan waktu yang bertabrakan dengan istirahat lain.</p></aside>
        <div class="sticky bottom-3 z-10 flex items-center justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none"><a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>{{ $editing ? 'Simpan perubahan' : 'Simpan istirahat' }}</button></div>
    </form>
</div>
