@extends('layouts.app')

@section('title', 'Dashboard Alumni')
@section('page-title', 'Dashboard Alumni')
@section('page-subtitle', 'Akses arsip akademik Anda')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-alumni')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 via-blue-600 to-cyan-500 text-white shadow-lg shadow-brand-900/15">
        <div class="flex items-center gap-4 p-5 sm:p-6">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl text-white ring-2 ring-white/40"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-blue-100">Selamat!</p>
                <h2 class="mt-0.5 break-words text-xl font-extrabold !text-white sm:text-2xl">{{ $siswa->nama_lengkap }}</h2>
                <p class="mt-1 text-xs leading-5 text-blue-50/90 sm:text-sm">Anda telah dinyatakan <strong class="font-extrabold text-white">LULUS</strong> dari PKBM.</p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-id-card text-brand-600" aria-hidden="true"></i>Profil saya</h2>
        </header>
        <div class="flex flex-col gap-5 p-4 sm:flex-row sm:p-5">
            <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('img/logo.png') }}" alt="Foto {{ $siswa->nama_lengkap }}" class="h-32 w-28 shrink-0 rounded-xl border border-slate-200 bg-slate-50 object-cover shadow-sm">
            <dl class="grid min-w-0 flex-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                <div class="min-w-0"><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Nama lengkap</dt><dd class="mt-0.5 break-words font-extrabold text-slate-900">{{ $siswa->nama_lengkap }}</dd></div>
                <div class="min-w-0"><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">NISN</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $siswa->nisn ?? '-' }}</dd></div>
                <div class="min-w-0"><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">NIS</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $siswa->nis ?? '-' }}</dd></div>
                <div class="min-w-0"><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Jenis kelamin</dt><dd class="mt-0.5 font-semibold text-slate-800">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                <div class="min-w-0"><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Status</dt><dd class="mt-1"><span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-extrabold text-emerald-700 ring-1 ring-inset ring-emerald-200">ALUMNI</span></dd></div>
            </dl>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-file-lines text-brand-600" aria-hidden="true"></i>Rapor terakhir</h2>
        </header>

        @if($raporTerakhir)
            <dl class="grid grid-cols-2 gap-3 p-4 sm:p-5 lg:grid-cols-4">
                <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Tahun ajaran</dt>
                    <dd class="mt-1 truncate text-sm font-extrabold text-slate-900">{{ $raporTerakhir->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</dd>
                </div>
                <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Semester / jenis</dt>
                    <dd class="mt-1 truncate text-sm font-extrabold text-slate-900">Semester {{ $raporTerakhir->semester }} - {{ $raporTerakhir->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }}</dd>
                </div>
                <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Status</dt>
                    <dd class="mt-1">
                        @if($raporTerakhir->status === 'diterbitkan')
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-extrabold text-emerald-700 ring-1 ring-inset ring-emerald-200">Diterbitkan</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-extrabold text-slate-600 ring-1 ring-inset ring-slate-200">{{ ucfirst($raporTerakhir->status) }}</span>
                        @endif
                    </dd>
                </div>
                <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Tanggal terbit</dt>
                    <dd class="mt-1 truncate text-sm font-extrabold text-slate-900">{{ $raporTerakhir->tanggal_terbit ? $raporTerakhir->tanggal_terbit->locale('id')->translatedFormat('d M Y') : '-' }}</dd>
                </div>
            </dl>
            <p class="flex items-start gap-2 border-t border-slate-100 bg-slate-50/60 px-4 py-3 text-xs text-slate-500 sm:px-5">
                <i class="fa-solid fa-circle-info mt-0.5 text-sky-600" aria-hidden="true"></i>
                <span>Untuk akses rapor lengkap (unduh/cetak), silakan hubungi wali siswa Anda. Akun wali siswa tetap memiliki akses penuh.</span>
            </p>
        @else
            <p class="m-4 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 sm:m-5">
                <i class="fa-solid fa-triangle-exclamation mt-0.5" aria-hidden="true"></i>
                <span>Belum ada rapor yang tercatat untuk Anda.</span>
            </p>
        @endif
    </section>
</div>
@endsection
