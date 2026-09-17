@extends('layouts.app')

@section('title', 'Pilih Kelas')
@section('page-title', 'Pilih Kelas')
@section('page-subtitle', 'Pilih kelas yang ingin Anda kelola')

@section('content')
<div class="min-w-0 space-y-5">
    <header class="rounded-2xl bg-gradient-to-r from-sky-700 to-cyan-600 p-5 !text-white shadow-sm sm:p-7">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-100">Ruang Wali Kelas</p>
        <h1 class="mt-2 text-xl font-extrabold !text-white sm:text-2xl">Selamat datang, {{ $waliKelas->nama_lengkap }}</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-sky-50">Anda ditugaskan pada {{ $kelasList->count() }} kelas. Pilih satu kelas untuk membuka dashboard, presensi, nilai, dan rapor yang sesuai.</p>
    </header>

    <section class="grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-3" aria-label="Daftar kelas yang dapat dikelola">
        @foreach($kelasList as $kelas)
            @php($isSelected = (string) $currentSelectedId === (string) $kelas->id)
            <article class="min-w-0 overflow-hidden rounded-2xl border {{ $isSelected ? 'border-cyan-400 ring-2 ring-cyan-100' : 'border-slate-200' }} bg-white shadow-sm">
                <div class="flex items-start justify-between gap-3 bg-sky-50 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-sky-700">{{ strtoupper($kelas->jenjang) }}</p>
                        <h2 class="mt-1 truncate text-xl font-extrabold text-slate-900">{{ $kelas->nama_kelas }}</h2>
                    </div>
                    @if($isSelected)
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800"><i class="fas fa-check-circle" aria-hidden="true"></i>Aktif</span>
                    @endif
                </div>
                <dl class="space-y-3 px-5 py-4 text-sm">
                    <div class="flex items-start gap-3"><i class="fas fa-building mt-1 w-4 text-sky-600" aria-hidden="true"></i><div class="min-w-0"><dt class="text-xs font-semibold uppercase text-slate-500">Cabang</dt><dd class="break-words font-semibold text-slate-800">{{ $kelas->cabang->nama_cabang ?? '-' }}</dd></div></div>
                    <div class="flex items-start gap-3"><i class="fas fa-calendar mt-1 w-4 text-sky-600" aria-hidden="true"></i><div><dt class="text-xs font-semibold uppercase text-slate-500">Tahun ajaran</dt><dd class="font-semibold text-slate-800">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</dd></div></div>
                    <div class="flex items-start gap-3"><i class="fas fa-users mt-1 w-4 text-sky-600" aria-hidden="true"></i><div><dt class="text-xs font-semibold uppercase text-slate-500">Siswa aktif</dt><dd class="font-semibold text-slate-800">{{ $kelas->siswa->count() }} siswa</dd></div></div>
                </dl>
                <form action="{{ route('wali.pilih-kelas.select', $kelas) }}" method="POST" class="border-t border-slate-100 p-4">
                    @csrf
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl {{ $isSelected ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-sky-700 text-white hover:bg-sky-800' }} px-4 text-sm font-bold transition">
                        <i class="fas {{ $isSelected ? 'fa-check' : 'fa-arrow-right' }}" aria-hidden="true"></i>{{ $isSelected ? 'Sudah dipilih' : 'Pilih kelas ini' }}
                    </button>
                </form>
            </article>
        @endforeach
    </section>
</div>
@endsection
