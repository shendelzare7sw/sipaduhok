@extends('layouts.app')

@section('title', 'Edit Wali Siswa - ' . ($orangTua->name ?? 'N/A'))
@section('page-title', 'Edit Wali Siswa')
@section('page-subtitle', 'Perbarui data ' . ($orangTua->name ?? 'N/A'))

@section('content')
@php
    $backUrl = url()->previous(route('admin.users.wali-siswa'));
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $knownRelationships = ['ayah_kandung', 'ibu_kandung', 'wali', 'ayah_tiri', 'ibu_tiri'];
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex min-w-0 items-center gap-3">
        <a href="{{ $backUrl }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline transition hover:border-brand-300 hover:text-brand-700" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Data pengguna</p>
            <h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">Edit wali siswa</h2>
            <p class="mt-0.5 truncate text-sm text-slate-500">{{ $orangTua->name }}</p>
        </div>
    </div>
    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold {{ $orangTua->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}"><span class="h-2 w-2 rounded-full {{ $orangTua->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>{{ $orangTua->is_active ? 'Akun aktif' : 'Akun nonaktif' }}</span>
</div>

@if($errors->any())
    <div class="mb-4 flex gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <i class="fas fa-circle-exclamation mt-0.5 text-red-500" aria-hidden="true"></i>
        <div><p class="font-extrabold">Perubahan belum dapat disimpan.</p><p class="mt-0.5 text-xs">Periksa kolom yang ditandai di bawah.</p></div>
    </div>
@endif

<form action="{{ route('admin.users.update-wali-siswa', $orangTua->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="_return_url" value="{{ $backUrl }}">

    @include('admin.users.partials.wali-siswa-account-fields')

    @if($orangTua->studentParents?->isNotEmpty())
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-users" aria-hidden="true"></i></span>
                <div>
                    <h3 class="font-extrabold text-slate-950">2. Hubungan dengan siswa</h3>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">Perubahan di sini hanya memperbarui jenis hubungan keluarga; data siswa tidak berubah.</p>
                </div>
            </div>

            <div class="grid gap-3 p-4 lg:grid-cols-2 sm:p-5">
                @foreach($orangTua->studentParents as $sp)
                    @php
                        $isCustomRelationship = !in_array($sp->relationship, $knownRelationships, true);
                        $relationshipValue = old("relationships.{$sp->id}", $isCustomRelationship ? 'lainnya' : $sp->relationship);
                        $otherValue = old("relationships_lainnya.{$sp->id}", $isCustomRelationship ? $sp->relationship : '');
                        $otherTarget = 'other-relationship-' . $sp->id;
                    @endphp
                    <article class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600 shadow-sm"><i class="fas fa-user-graduate" aria-hidden="true"></i></span>
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate text-sm font-extrabold text-slate-900">{{ $sp->siswa->nama_lengkap }}</h4>
                                <p class="mt-1 text-xs text-slate-500">NIS {{ $sp->siswa->nis ?: '-' }} · NISN {{ $sp->siswa->nisn ?: '-' }}</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">{{ $sp->siswa->kelas->jenjang ?? '-' }}</span>
                                    <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">{{ $sp->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                                    @if($sp->is_primary)<span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700"><i class="fas fa-star mr-1" aria-hidden="true"></i>Kontak utama</span>@endif
                                    @if($sp->can_access_academic)<span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700"><i class="fas fa-check mr-1" aria-hidden="true"></i>Akses akademik</span>@endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="relationship-{{ $sp->id }}" class="block text-sm font-bold text-slate-700">Hubungan keluarga</label>
                            <select id="relationship-{{ $sp->id }}" name="relationships[{{ $sp->id }}]" data-relationship-select data-other-target="{{ $otherTarget }}" class="{{ $inputClass }}">
                                <option value="ayah_kandung" @selected($relationshipValue === 'ayah_kandung')>Ayah kandung</option>
                                <option value="ibu_kandung" @selected($relationshipValue === 'ibu_kandung')>Ibu kandung</option>
                                <option value="wali" @selected($relationshipValue === 'wali')>Wali</option>
                                <option value="ayah_tiri" @selected($relationshipValue === 'ayah_tiri')>Ayah tiri</option>
                                <option value="ibu_tiri" @selected($relationshipValue === 'ibu_tiri')>Ibu tiri</option>
                                <option value="lainnya" @selected($relationshipValue === 'lainnya')>Lainnya</option>
                            </select>
                            @error("relationships.{$sp->id}") <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div id="{{ $otherTarget }}" class="mt-3 hidden">
                            <label for="relationship-other-{{ $sp->id }}" class="block text-sm font-bold text-slate-700">Sebutkan hubungan lainnya</label>
                            <input id="relationship-other-{{ $sp->id }}" type="text" name="relationships_lainnya[{{ $sp->id }}]" value="{{ $otherValue }}" class="{{ $inputClass }}" placeholder="Contoh: Kakek, nenek, paman">
                            @error("relationships_lainnya.{$sp->id}") <span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="sticky bottom-3 z-10 flex justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
        <a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>Simpan perubahan</button>
    </div>
</form>
@endsection
