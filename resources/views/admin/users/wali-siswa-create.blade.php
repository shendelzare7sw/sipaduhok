@extends('layouts.app')

@section('title', 'Tambah Wali Siswa')
@section('page-title', 'Tambah Wali Siswa')
@section('page-subtitle', 'Buat akun dan hubungkan dengan siswa bila diperlukan')

@section('content')
@php
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $selectedStudents = collect(old('siswa_ids', []))->map(fn ($id) => (string) $id);
@endphp

<div class="mb-4 flex min-w-0 items-center gap-3">
    <a href="{{ route('admin.users.wali-siswa') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline transition hover:border-brand-300 hover:text-brand-700" aria-label="Kembali">
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
    </a>
    <div class="min-w-0">
        <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Data pengguna</p>
        <h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">Tambah wali siswa</h2>
        <p class="mt-0.5 text-sm text-slate-500">Akun dapat dibuat dahulu dan dihubungkan ke siswa sekarang atau nanti.</p>
    </div>
</div>

@if($errors->any())
    <div class="mb-4 flex gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <i class="fas fa-circle-exclamation mt-0.5 text-red-500" aria-hidden="true"></i>
        <div><p class="font-extrabold">Data belum dapat disimpan.</p><p class="mt-0.5 text-xs">Periksa kolom yang ditandai, lalu coba kembali.</p></div>
    </div>
@endif

<form action="{{ route('admin.users.wali-siswa.store') }}" method="POST" class="space-y-4" data-wali-student-link-form>
    @csrf

    @include('admin.users.partials.wali-siswa-account-fields')

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-filter-root>
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-link" aria-hidden="true"></i></span>
                <div>
                    <h3 class="font-extrabold text-slate-950">2. Hubungkan dengan siswa <span class="font-medium text-slate-400">(opsional)</span></h3>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">Cari siswa, centang yang sesuai, lalu pilih satu hubungan keluarga untuk semua siswa terpilih.</p>
                </div>
            </div>
            <span class="rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700"><span data-selected-student-count>{{ $selectedStudents->count() }}</span> dipilih</span>
        </div>

        <div class="space-y-4 p-4 sm:p-5">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="sm:col-span-2 xl:col-span-1">
                    <label for="searchStudent" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Cari nama / NISN</label>
                    <div class="relative mt-1.5">
                        <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                        <input id="searchStudent" type="search" data-filter-key="search" data-filter-mode="contains" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 !pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Ketik nama atau NISN">
                    </div>
                </div>
                <div>
                    <label for="filterJenjang" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Jenjang</label>
                    <select id="filterJenjang" data-filter-key="jenjang" class="{{ $inputClass }}">
                        <option value="">Semua jenjang</option>
                        @foreach($jenjangs as $jenjang)<option value="{{ $jenjang }}">{{ $jenjang }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="filterCabang" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Cabang</label>
                    <select id="filterCabang" data-filter-key="cabang" class="{{ $inputClass }}">
                        <option value="">Semua cabang</option>
                        @foreach($cabangList as $cabang)<option value="{{ $cabang->nama_cabang }}">{{ $cabang->nama_cabang }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="filterKelas" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Kelas</label>
                    <select id="filterKelas" data-filter-key="kelas" class="{{ $inputClass }}">
                        <option value="">Semua kelas</option>
                        @foreach($kelasList as $kelas)<option value="{{ $kelas->nama_kelas }}">{{ $kelas->jenjang }} — {{ $kelas->nama_kelas }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-semibold text-slate-500"><span data-filter-count>{{ $siswaList->count() }}</span> siswa ditampilkan</p>
                <button type="button" data-filter-reset class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-600 hover:bg-slate-50"><i class="fas fa-rotate-left" aria-hidden="true"></i>Reset filter</button>
            </div>

            <div class="grid max-h-[28rem] gap-2 overflow-y-auto pr-1 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($siswaList as $siswa)
                    @php
                        $studentName = $siswa->user->name ?? $siswa->nama_lengkap;
                        $jenjang = $siswa->kelas->jenjang ?? '';
                        $kelas = $siswa->kelas->nama_kelas ?? '';
                        $cabang = $siswa->cabang->nama_cabang ?? '';
                    @endphp
                    <label data-filter-item
                        data-search="{{ $studentName }} {{ $siswa->nisn }}"
                        data-jenjang="{{ $jenjang }}"
                        data-cabang="{{ $cabang }}"
                        data-kelas="{{ $kelas }}"
                        class="group flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 transition hover:border-brand-300 hover:bg-brand-50/40 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                        <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" data-student-link-checkbox class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked($selectedStudents->contains((string) $siswa->id))>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold text-slate-900">{{ $studentName }}</span>
                            <span class="mt-1 block text-xs text-slate-500">NISN {{ $siswa->nisn ?: '-' }}</span>
                            <span class="mt-2 flex flex-wrap gap-1.5">
                                <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">{{ $jenjang ?: '-' }}</span>
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">{{ $kelas ?: 'Belum ada kelas' }}</span>
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">{{ $cabang ?: 'Tanpa cabang' }}</span>
                            </span>
                        </span>
                    </label>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500">
                        <i class="fas fa-user-graduate mb-3 block text-2xl text-slate-300" aria-hidden="true"></i>Belum ada siswa yang dapat dihubungkan.
                    </div>
                @endforelse
            </div>

            <div class="grid gap-4 rounded-xl border border-violet-100 bg-violet-50/60 p-4 sm:grid-cols-2">
                <div>
                    <label for="hubungan_keluarga" class="block text-sm font-bold text-slate-700">Hubungan keluarga</label>
                    <select id="hubungan_keluarga" name="hubungan_keluarga" data-student-relationship class="{{ $inputClass }}">
                        <option value="">Pilih setelah memilih siswa</option>
                        <option value="ayah_kandung" @selected(old('hubungan_keluarga') === 'ayah_kandung')>Ayah kandung</option>
                        <option value="ibu_kandung" @selected(old('hubungan_keluarga') === 'ibu_kandung')>Ibu kandung</option>
                        <option value="wali" @selected(old('hubungan_keluarga') === 'wali')>Wali</option>
                        <option value="ayah_tiri" @selected(old('hubungan_keluarga') === 'ayah_tiri')>Ayah tiri</option>
                        <option value="ibu_tiri" @selected(old('hubungan_keluarga') === 'ibu_tiri')>Ibu tiri</option>
                        <option value="lainnya" @selected(old('hubungan_keluarga') === 'lainnya')>Lainnya</option>
                    </select>
                    @error('hubungan_keluarga') <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
                </div>
                <div data-other-relationship class="hidden">
                    <label for="hubungan_keluarga_lainnya" class="block text-sm font-bold text-slate-700">Sebutkan hubungan lainnya</label>
                    <input id="hubungan_keluarga_lainnya" type="text" name="hubungan_keluarga_lainnya" value="{{ old('hubungan_keluarga_lainnya') }}" class="{{ $inputClass }}" placeholder="Contoh: Kakek, nenek, paman">
                    @error('hubungan_keluarga_lainnya') <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </section>

    <div class="sticky bottom-3 z-10 flex justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
        <a href="{{ route('admin.users.wali-siswa') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>Simpan wali siswa</button>
    </div>
</form>
@endsection
