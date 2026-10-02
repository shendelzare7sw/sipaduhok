@extends('layouts.lms-guru')

@php
    $isEdit = isset($meeting);
    $args = [$kelas->id, $mapel->id];
    $platform = old('platform', $isEdit ? $meeting->platform : 'google_meet');
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

@section('title', $isEdit ? 'Edit Meeting' : 'Jadwalkan Meeting')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route('guru.lms.meeting.index', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke kelas virtual</a>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-bold">Periksa kembali isian berikut:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route('guru.lms.meeting.update', [...$args, $meeting->id]) : route('guru.lms.meeting.store', $args) }}" method="POST" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <header class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-video text-indigo-600" aria-hidden="true"></i>{{ $isEdit ? 'Edit jadwal meeting' : 'Jadwalkan meeting baru' }}</h2>
            <p class="mt-1 text-xs text-slate-500">Siswa akan melihat jadwal dan tautan ini di ruang kelas LMS. Tanda <span class="text-rose-600">*</span> wajib diisi.</p>
        </header>

        <div class="space-y-5 px-4 py-5 sm:px-6">
            <label class="block text-xs font-bold text-slate-700">Judul pertemuan <span class="text-rose-600">*</span>
                <input type="text" name="judul" value="{{ old('judul', $isEdit ? $meeting->judul : '') }}" placeholder="Contoh: Pembahasan Bab 3 - Trigonometri" required class="{{ $input }}">
                @error('judul')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-xs font-bold text-slate-700">Platform <span class="text-rose-600">*</span>
                    <select name="platform" required class="{{ $input }}">
                        <option value="google_meet" @selected($platform == 'google_meet')>Google Meet</option>
                        <option value="zoom" @selected($platform == 'zoom')>Zoom Meeting</option>
                        <option value="lainnya" @selected($platform == 'lainnya')>Lainnya</option>
                    </select>
                    @error('platform')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                <label class="block text-xs font-bold text-slate-700">Link meeting <span class="text-rose-600">*</span>
                    <input type="url" name="link_meeting" value="{{ old('link_meeting', $isEdit ? $meeting->link_meeting : '') }}" placeholder="https://meet.google.com/..." required class="{{ $input }}">
                    @error('link_meeting')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-xs font-bold text-slate-700">Waktu mulai <span class="text-rose-600">*</span>
                    <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai', $isEdit ? $meeting->waktu_mulai->format('Y-m-d\TH:i') : '') }}" required class="{{ $input }}">
                    @error('waktu_mulai')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                <label class="block text-xs font-bold text-slate-700">Waktu selesai (estimasi)
                    <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai', $isEdit && $meeting->waktu_selesai ? $meeting->waktu_selesai->format('Y-m-d\TH:i') : '') }}" class="{{ $input }}">
                    @error('waktu_selesai')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
            </div>

            <label class="block text-xs font-bold text-slate-700">Deskripsi / catatan tambahan
                <textarea name="deskripsi" rows="4" placeholder="Instruksi untuk siswa..." class="{{ $input }}">{{ old('deskripsi', $isEdit ? $meeting->deskripsi : '') }}</textarea>
                @error('deskripsi')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            @if($isEdit)
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $meeting->is_active)) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Status aktif</strong>Tampilkan meeting ini kepada siswa.</span>
                </label>
            @endif

            @include('guru.partials.multi-kelas-selector')
        </div>

        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route('guru.lms.meeting.index', $args) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>{{ $isEdit ? 'Simpan perubahan' : 'Simpan jadwal' }}</button>
        </footer>
    </form>
</div>
@endsection
