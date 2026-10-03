@extends('layouts.lms')

@section('title', 'Daftar Guru')
@section('page-title', 'Daftar Guru Pengajar')
@section('page-subtitle', 'Informasi kontak guru mata pelajaran')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-4" x-data="{ q: '' }">
    <section class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-4">
        <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-chalkboard-user text-indigo-500" aria-hidden="true"></i>Guru pengajar <span class="text-sm font-bold text-slate-400">({{ $guruPengajar->count() }})</span></h2>
        @if($guruPengajar->count() > 0)
            <label class="relative block sm:w-72">
                <span class="sr-only">Cari guru atau mata pelajaran</span>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>
                <input type="search" x-model="q" placeholder="Cari guru atau mapel..." class="block h-10 w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>
        @endif
    </section>

    @if($guruPengajar->count() > 0)
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($guruPengajar as $guru)
                @php $cari = Str::lower($guru->nama_lengkap . ' ' . $guru->guruKelas->map(fn ($p) => $p->mataPelajaran->nama_mapel ?? '')->implode(' ')); @endphp
                <article data-cari="{{ $cari }}" x-show="$el.dataset.cari.includes(q.toLowerCase().trim())" class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        @if($guru->foto)
                            <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->nama_lengkap }}" class="h-14 w-14 shrink-0 rounded-2xl object-cover">
                        @else
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-xl font-extrabold text-white">{{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}</span>
                        @endif
                        <h3 class="min-w-0 text-sm font-extrabold leading-snug text-slate-900">{{ $guru->nama_lengkap }}</h3>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach($guru->guruKelas as $penugasan)
                            <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700">{{ $penugasan->mataPelajaran->nama_mapel }}</span>
                        @endforeach
                    </div>

                    <div class="mt-4 space-y-2 border-t border-slate-100 pt-3">
                        @if($waGuru = wa_link($guru->telepon))
                            <a href="{{ $waGuru }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-xl p-2 no-underline transition hover:bg-emerald-50">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
                                <span class="min-w-0">
                                    <span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">WhatsApp</span>
                                    <span class="block truncate text-sm font-bold text-emerald-700">{{ $guru->telepon }}</span>
                                </span>
                            </a>
                        @endif
                        @if($guru->email)
                            <a href="mailto:{{ $guru->email }}" class="flex items-center gap-3 rounded-xl p-2 no-underline transition hover:bg-sky-50">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
                                <span class="min-w-0">
                                    <span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Email</span>
                                    <span class="block truncate text-sm font-bold text-sky-700">{{ $guru->email }}</span>
                                </span>
                            </a>
                        @endif
                        @if(!$waGuru && !$guru->email)
                            <p class="flex items-center gap-2 px-2 py-1 text-xs text-slate-400"><i class="fa-solid fa-circle-info" aria-hidden="true"></i>Kontak tidak tersedia</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-12 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-500"><i class="fa-solid fa-users" aria-hidden="true"></i></span>
            <h2 class="mt-3 text-sm font-extrabold text-slate-900">Belum ada guru pengajar</h2>
            <p class="mt-1 text-xs text-slate-500">Data guru pengajar belum tersedia untuk kelas Anda.</p>
        </div>
    @endif
</div>
@endsection
