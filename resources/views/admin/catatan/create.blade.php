@extends('layouts.app')

@section('title', 'Kirim Catatan Baru')
@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Sampaikan informasi kepada pengguna yang tepat')

@section('content')
@php
    $routePrefix = request()->routeIs('ketua.*') ? 'ketua' : 'admin';
    $backUrl = url()->previous(route($routePrefix . '.catatan.index'));
    $selectedType = old('tipe_penerima', 'semua');
    $selectedUsers = collect(old('penerima_ids', []))->map(fn ($id) => (string) $id);
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
@endphp

<div class="mb-4 flex min-w-0 items-center gap-3">
    <a href="{{ $backUrl }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline transition hover:border-brand-300 hover:text-brand-700" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
    <div class="min-w-0">
        <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Publikasi internal</p>
        <h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">Kirim catatan baru</h2>
        <p class="mt-0.5 text-sm text-slate-500">Tulis pesan, tentukan penerima, lalu pilih tingkat prioritas.</p>
    </div>
</div>

@if($errors->any())
    <div class="mb-4 flex gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <i class="fas fa-circle-exclamation mt-0.5 text-red-500" aria-hidden="true"></i>
        <div><p class="font-extrabold">Catatan belum dapat dikirim.</p><p class="mt-0.5 text-xs">Periksa kembali isian dan penerima yang dipilih.</p></div>
    </div>
@endif

<form action="{{ route($routePrefix . '.catatan.store') }}" method="POST" class="space-y-4" data-catatan-form>
    @csrf
    <input type="hidden" name="_return_url" value="{{ $backUrl }}">

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-pen-to-square" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">1. Isi catatan</h3><p class="mt-0.5 text-xs text-slate-500">Gunakan judul singkat dan isi yang langsung menjelaskan tindak lanjut.</p></div>
        </div>
        <div class="grid gap-4 p-4 sm:p-5">
            <div>
                <label for="judul" class="block text-sm font-bold text-slate-700">Judul <span class="text-red-500">*</span></label>
                <input id="judul" type="text" name="judul" value="{{ old('judul') }}" class="{{ $inputClass }}" maxlength="255" placeholder="Contoh: Evaluasi progres pembelajaran minggu ini" required>
                @error('judul') <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="isi_catatan" class="block text-sm font-bold text-slate-700">Isi catatan <span class="text-red-500">*</span></label>
                <textarea id="isi_catatan" name="isi_catatan" rows="7" class="{{ $inputClass }}" placeholder="Tuliskan informasi, instruksi, atau teguran dengan bahasa yang jelas..." required>{{ old('isi_catatan') }}</textarea>
                @error('isi_catatan') <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-users" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">2. Tentukan penerima</h3><p class="mt-0.5 text-xs text-slate-500">Pilih jangkauan yang paling sempit agar pesan tetap relevan.</p></div>
        </div>

        <div class="space-y-4 p-4 sm:p-5">
            <div class="grid gap-3 md:grid-cols-3">
                @foreach([
                    ['semua', 'Semua pengguna', 'Kirim ke seluruh role yang tersedia.', 'fa-users'],
                    ['role', 'Kelompok role', 'Kirim ke satu kelompok pekerjaan.', 'fa-user-tag'],
                    ['individu', 'Individu', 'Pilih satu atau beberapa pengguna.', 'fa-user-check'],
                ] as [$value, $label, $description, $icon])
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-brand-300 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-100">
                        <input type="radio" name="tipe_penerima" value="{{ $value }}" data-recipient-choice class="mt-1 h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500" @checked($selectedType === $value) required>
                        <span class="min-w-0"><span class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas {{ $icon }} text-brand-600" aria-hidden="true"></i>{{ $label }}</span><span class="mt-1 block text-xs leading-relaxed text-slate-500">{{ $description }}</span></span>
                    </label>
                @endforeach
            </div>
            @error('tipe_penerima') <span class="block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror

            <div data-recipient-panel="role" class="hidden rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label for="role_penerima" class="block text-sm font-bold text-slate-700">Role penerima <span class="text-red-500">*</span></label>
                <select id="role_penerima" name="role_penerima" data-recipient-role class="{{ $inputClass }}">
                    <option value="">Pilih role penerima</option>
                    @foreach($roles as $key => $label)<option value="{{ $key }}" @selected(old('role_penerima') === $key)>{{ $label }}</option>@endforeach
                </select>
                @error('role_penerima') <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
            </div>

            <div data-recipient-panel="individu" data-filter-root class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                <div class="grid gap-3 border-b border-slate-200 p-4 md:grid-cols-3">
                    <div>
                        <label for="filterRole" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Role</label>
                        <select id="filterRole" data-filter-key="role" class="{{ $inputClass }}"><option value="">Semua role</option>@foreach($roles as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label for="filterCabang" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Cabang</label>
                        <select id="filterCabang" data-filter-key="cabang" class="{{ $inputClass }}"><option value="">Semua cabang</option>@foreach($cabangList as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label for="filterSearch" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Cari nama</label>
                        <input id="filterSearch" type="search" data-filter-key="search" data-filter-mode="contains" class="{{ $inputClass }}" placeholder="Ketik nama penerima">
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white px-4 py-3">
                    <button type="button" data-select-visible-recipients class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-brand-200 bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-check-double" aria-hidden="true"></i>Pilih yang tampil</button>
                    <button type="button" data-clear-recipients class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-600 hover:bg-slate-100"><i class="fas fa-xmark" aria-hidden="true"></i>Hapus pilihan</button>
                    <span class="ml-auto rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-700"><span data-recipient-count>{{ $selectedUsers->count() }}</span> dipilih</span>
                </div>
                <div class="grid max-h-80 gap-2 overflow-y-auto p-3 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse($usersForIndividu as $user)
                        <label data-filter-item data-recipient-item data-search="{{ $user['name'] }}" data-role="{{ $user['role'] }}" data-cabang="{{ $user['cabang_id'] }}" class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 hover:border-brand-300 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                            <input type="checkbox" name="penerima_ids[]" value="{{ $user['id'] }}" data-recipient-checkbox class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked($selectedUsers->contains((string) $user['id']))>
                            <span class="min-w-0"><span class="block truncate text-sm font-bold text-slate-900">{{ $user['name'] }}</span><span class="mt-1 flex flex-wrap gap-1.5"><span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">{{ $user['role_label'] }}</span><span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">{{ $user['cabang_name'] ?: '-' }}</span></span></span>
                        </label>
                    @empty
                        <p class="col-span-full px-4 py-10 text-center text-sm text-slate-500">Tidak ada penerima aktif yang tersedia.</p>
                    @endforelse
                </div>
                @error('penerima_ids') <span class="block px-4 pb-3 text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
                @error('penerima_ids.*') <span class="block px-4 pb-3 text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-flag" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">3. Prioritas</h3><p class="mt-0.5 text-xs text-slate-500">Gunakan mendesak hanya untuk pesan yang membutuhkan tindakan segera.</p></div></div>
        <div class="grid gap-3 p-4 md:grid-cols-3 sm:p-5">
            @foreach([
                ['biasa', 'Biasa', 'Informasi rutin atau arahan umum.', 'has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700'],
                ['penting', 'Penting', 'Perlu diperhatikan dalam waktu dekat.', 'has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-700'],
                ['mendesak', 'Mendesak', 'Butuh tindak lanjut secepatnya.', 'has-[:checked]:border-red-500 has-[:checked]:bg-red-50 has-[:checked]:text-red-700'],
            ] as [$value, $label, $description, $tone])
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition {{ $tone }} has-[:checked]:ring-2 has-[:checked]:ring-current/10">
                    <input type="radio" name="prioritas" value="{{ $value }}" class="mt-1 h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('prioritas', 'biasa') === $value) required>
                    <span><span class="block text-sm font-extrabold">{{ $label }}</span><span class="mt-1 block text-xs leading-relaxed opacity-75">{{ $description }}</span></span>
                </label>
            @endforeach
        </div>
        @error('prioritas') <span class="block px-5 pb-4 text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
    </section>

    <div class="sticky bottom-3 z-10 flex justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
        <a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-brand-700"><i class="fas fa-paper-plane" aria-hidden="true"></i>Kirim catatan</button>
    </div>
</form>
@endsection
