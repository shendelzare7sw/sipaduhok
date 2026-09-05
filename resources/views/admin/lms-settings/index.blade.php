@extends('layouts.app')

@section('title', 'Pengaturan LMS')
@section('page-title', 'Pengaturan LMS')
@section('page-subtitle', 'Tentukan jenjang yang dapat memakai pembelajaran digital')

@section('content')
@php
    $branding = [
        'KB' => ['icon' => 'fa-shapes', 'tone' => 'bg-emerald-50 text-emerald-700', 'desc' => 'Kelompok Bermain'],
        'TKA' => ['icon' => 'fa-child', 'tone' => 'bg-cyan-50 text-cyan-700', 'desc' => 'Taman Kanak-Kanak A'],
        'TKB' => ['icon' => 'fa-child', 'tone' => 'bg-cyan-50 text-cyan-700', 'desc' => 'Taman Kanak-Kanak B'],
        'SD' => ['icon' => 'fa-school', 'tone' => 'bg-red-50 text-red-700', 'desc' => 'Sekolah Dasar'],
        'SMP' => ['icon' => 'fa-book', 'tone' => 'bg-blue-50 text-blue-700', 'desc' => 'Sekolah Menengah Pertama'],
        'SMA' => ['icon' => 'fa-university', 'tone' => 'bg-amber-50 text-amber-700', 'desc' => 'Sekolah Menengah Atas'],
    ];
@endphp

<div class="min-w-0 w-full space-y-4">
    <header><p class="text-xs font-bold uppercase tracking-wide text-brand-600">Kontrol akses</p><h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">Jenjang pengguna LMS</h2><p class="mt-1 text-sm text-slate-500">Aktifkan hanya jenjang yang sudah siap menggunakan tugas, materi, dan ujian daring.</p></header>

    <div class="grid min-w-0 gap-4 xl:grid-cols-[minmax(0,1.45fr)_minmax(300px,.55fr)]">
        <form action="{{ route('admin.lms-settings.update') }}" method="POST" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf @method('PUT')
            <div class="border-b border-slate-200 p-4 sm:p-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-sliders-h mr-2 text-brand-600"></i>Akses per jenjang</h3><p class="mt-1 text-xs text-slate-500">Perubahan berlaku untuk seluruh siswa pada jenjang terkait.</p></div>
            <div class="grid divide-y divide-slate-100 sm:grid-cols-2 sm:divide-y-0">
                @foreach($allJenjang as $jenjang)
                    @php $info = $branding[$jenjang]; $isChecked = in_array($jenjang, $allowedJenjang, true); @endphp
                    <label class="flex cursor-pointer items-center gap-3 border-slate-100 p-4 transition hover:bg-slate-50 sm:border-b sm:odd:border-r sm:p-5"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $info['tone'] }}"><i class="fas {{ $info['icon'] }}"></i></span><span class="min-w-0 flex-1"><strong class="block text-sm text-slate-950">{{ $jenjang }}</strong><span class="block truncate text-xs text-slate-500">{{ $info['desc'] }}</span></span><span class="relative inline-flex h-6 w-11 shrink-0 items-center"><input type="checkbox" name="jenjang[]" value="{{ $jenjang }}" class="peer sr-only" @checked($isChecked)><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></span></label>
                @endforeach
            </div>
            <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-5"><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-brand-700"><i class="fas fa-save"></i>Simpan perubahan</button></div>
        </form>

        <aside class="space-y-4">
            <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4 sm:p-5"><div class="flex gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600"><i class="fas fa-graduation-cap"></i></span><div><h3 class="font-extrabold text-blue-950">Tentang HOK-LMS</h3><p class="mt-1 text-sm leading-6 text-blue-800">Learning Management System terintegrasi untuk pembelajaran digital SIPADUHOK.</p></div></div></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-circle-info mr-2 text-amber-500"></i>Jika jenjang dinonaktifkan</h3><ol class="mt-4 space-y-4"><li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-50 text-xs font-bold text-red-700">1</span><div><strong class="block text-sm text-slate-800">Menu disembunyikan</strong><p class="mt-0.5 text-xs leading-5 text-slate-500">Siswa tidak melihat navigasi Learning Management.</p></div></li><li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-50 text-xs font-bold text-red-700">2</span><div><strong class="block text-sm text-slate-800">Akses dibatasi</strong><p class="mt-0.5 text-xs leading-5 text-slate-500">Tugas, materi, dan ujian tidak dapat dibuka.</p></div></li><li class="flex gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">3</span><div><strong class="block text-sm text-slate-800">Data tetap aman</strong><p class="mt-0.5 text-xs leading-5 text-slate-500">Data lama tidak dihapus dan akan tersedia saat akses diaktifkan kembali.</p></div></li></ol></section>
        </aside>
    </div>
</div>
@endsection
