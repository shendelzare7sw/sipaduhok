@extends('layouts.app')

@section('title', 'Dashboard Wakil Kepala Sekolah')
@section('page-title', 'Ringkasan Akademik')
@section('page-subtitle', 'Pantau kesiapan akademik dan tindak lanjut cabang Anda')

@section('content')
@php
    $statItems = [
        ['value' => number_format($stats['totalSiswa']), 'label' => 'Siswa aktif', 'meta' => 'Terdaftar di cabang Anda', 'icon' => 'fa-user-graduate', 'tone' => 'bg-blue-50 text-blue-600'],
        ['value' => number_format($stats['totalGuru']), 'label' => 'Tenaga pendidik', 'meta' => 'Akun aktif di cabang', 'icon' => 'fa-chalkboard-teacher', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['value' => number_format($stats['totalKelas']), 'label' => 'Kelas aktif', 'meta' => $tahunAjaranAktif?->nama_tahun_ajaran ?? 'Belum ada periode aktif', 'icon' => 'fa-school', 'tone' => 'bg-cyan-50 text-cyan-600'],
        ['value' => number_format($stats['totalMapel']), 'label' => 'Mata pelajaran', 'meta' => 'Master akademik tersedia', 'icon' => 'fa-book', 'tone' => 'bg-violet-50 text-violet-600'],
        ['value' => number_format($stats['kelasWithWali']), 'label' => 'Sudah ada wali', 'meta' => 'Penugasan telah terisi', 'icon' => 'fa-user-check', 'tone' => 'bg-teal-50 text-teal-600'],
        ['value' => number_format($stats['kelasWithoutWali']), 'label' => 'Belum ada wali', 'meta' => 'Perlu ditindaklanjuti', 'icon' => 'fa-user-clock', 'tone' => 'bg-amber-50 text-amber-600'],
    ];
    $moduleGroups = [
        'akademik' => [
            ['label' => 'Data Kelas', 'description' => 'Kelola kelas cabang', 'route' => 'waka.kelas.index', 'icon' => 'fa-school', 'tone' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Jadwal Pelajaran', 'description' => 'Susun jadwal belajar', 'route' => 'waka.jadwal-pelajaran.index', 'icon' => 'fa-calendar-week', 'tone' => 'bg-violet-50 text-violet-600'],
            ['label' => 'Penugasan Wali', 'description' => 'Lengkapi wali kelas', 'route' => 'waka.wali-kelas.index', 'icon' => 'fa-user-check', 'tone' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Guru Pengajar', 'description' => 'Pantau penugasan guru', 'route' => 'waka.guru-pengajar.index', 'icon' => 'fa-user-tie', 'tone' => 'bg-cyan-50 text-cyan-600'],
            ['label' => 'Manajemen Siswa', 'description' => 'Kelola siswa cabang', 'route' => 'waka.manajemen-siswa.index', 'icon' => 'fa-users', 'tone' => 'bg-amber-50 text-amber-600'],
        ],
        'monitoring' => [
            ['label' => 'Monitor Siswa', 'description' => 'Pantau data peserta didik', 'route' => 'waka.monitoring.siswa', 'icon' => 'fa-user-graduate', 'tone' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Monitor Guru', 'description' => 'Pantau beban pengajar', 'route' => 'waka.monitoring.guru-pengajar', 'icon' => 'fa-user-tie', 'tone' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Monitor Wali', 'description' => 'Pantau wali kelas', 'route' => 'waka.monitoring.wali-kelas', 'icon' => 'fa-chalkboard-teacher', 'tone' => 'bg-violet-50 text-violet-600'],
            ['label' => 'Catatan', 'description' => 'Kirim arahan akademik', 'route' => 'waka.catatan.index', 'icon' => 'fa-clipboard', 'tone' => 'bg-amber-50 text-amber-600'],
        ],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl border border-brand-200 bg-gradient-to-br from-brand-700 via-brand-600 to-sky-500 text-white shadow-lg shadow-brand-900/10">
        <div class="flex flex-col gap-4 p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl ring-1 ring-white/20"><i class="fas fa-chart-pie" aria-hidden="true"></i></span>
                <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-sky-100">Pusat pengawasan akademik</p><h2 class="mt-1 text-xl font-extrabold !text-white sm:text-2xl">Kesiapan cabang dalam satu layar</h2><p class="mt-1 max-w-3xl text-sm leading-6 text-sky-50">Pantau kelas, guru, siswa, jadwal, dan tindak lanjut akademik tanpa berpindah konteks.</p></div>
            </div>
            <div class="grid shrink-0 grid-cols-2 gap-2 text-center lg:min-w-72">
                <div class="rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/15"><span class="block text-[10px] font-bold uppercase text-sky-100">Tahun ajaran</span><strong class="mt-1 block whitespace-nowrap text-sm">{{ $tahunAjaranAktif?->nama_tahun_ajaran ?? 'Belum aktif' }}</strong></div>
                <div class="rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/15"><span class="block text-[10px] font-bold uppercase text-sky-100">Hari ini</span><strong class="mt-1 block whitespace-nowrap text-sm">{{ now()->translatedFormat('d M Y') }}</strong></div>
            </div>
        </div>
    </section>

    <x-cleanflow.stat-grid :items="$statItems" desktop-columns="6" />

    <section class="grid min-w-0 gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)]">
        <div class="grid min-w-0 gap-4 lg:grid-cols-2">
            <article class="min-w-0 overflow-hidden rounded-2xl border {{ $kelasWithoutWali->isNotEmpty() ? 'border-amber-200' : 'border-slate-200' }} bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div class="min-w-0"><h3 class="truncate font-extrabold text-slate-950"><i class="fas fa-triangle-exclamation mr-2 text-amber-500" aria-hidden="true"></i>Kelas belum memiliki wali</h3><p class="mt-1 text-xs text-slate-500">Prioritas penugasan pada periode aktif.</p></div><a href="{{ route('waka.wali-kelas.index') }}" class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 px-3 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100">Kelola</a></header>
                <div class="divide-y divide-slate-100">
                    @forelse($kelasWithoutWali as $kelas)
                        <a href="{{ route('waka.kelas.show', $kelas) }}" class="flex min-w-0 items-center gap-3 p-4 text-slate-700 no-underline hover:bg-slate-50"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-xs font-extrabold text-amber-700">{{ $kelas->jenjang }}</span><span class="min-w-0 flex-1"><strong class="block truncate text-sm text-slate-900">{{ $kelas->nama_kelas }}</strong><span class="mt-0.5 block truncate text-[11px] text-slate-500">{{ $kelas->cabang->nama_cabang ?? 'Tanpa cabang' }}</span></span><i class="fas fa-chevron-right text-[10px] text-slate-300" aria-hidden="true"></i></a>
                    @empty
                        <div class="px-5 py-12 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fas fa-circle-check" aria-hidden="true"></i></span><p class="mt-3 text-sm font-extrabold text-slate-800">Semua kelas sudah memiliki wali</p><p class="mt-1 text-xs text-slate-500">Tidak ada tindak lanjut penugasan saat ini.</p></div>
                    @endforelse
                </div>
            </article>

            <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div class="min-w-0"><h3 class="truncate font-extrabold text-slate-950"><i class="fas fa-user-graduate mr-2 text-brand-600" aria-hidden="true"></i>Siswa terbaru</h3><p class="mt-1 text-xs text-slate-500">Pendaftar aktif di cabang Anda.</p></div><a href="{{ route('waka.manajemen-siswa.index') }}" class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 px-3 text-xs font-bold text-brand-700 no-underline hover:bg-brand-100">Lihat semua</a></header>
                <div class="max-h-[25rem] divide-y divide-slate-100 overflow-y-auto">
                    @forelse($recentSiswa as $siswa)
                        <a href="{{ route('waka.manajemen-siswa.show', $siswa) }}" class="flex min-w-0 items-center gap-3 p-4 text-slate-700 no-underline hover:bg-slate-50"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-extrabold text-brand-700">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</span><span class="min-w-0 flex-1"><strong class="block truncate text-sm text-slate-900">{{ $siswa->nama_lengkap }}</strong><span class="mt-0.5 block truncate text-[11px] text-slate-500">{{ $siswa->nis ?? 'NIS belum tersedia' }} &middot; {{ $siswa->kelas->nama_kelas ?? 'Belum ditempatkan' }}</span></span><span class="shrink-0 rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">Aktif</span></a>
                    @empty
                        <div class="px-5 py-12 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fas fa-users" aria-hidden="true"></i></span><p class="mt-3 text-sm font-extrabold text-slate-800">Belum ada siswa aktif</p></div>
                    @endforelse
                </div>
            </article>
        </div>

        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ tab: 'akademik' }">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-th-large mr-2 text-brand-600" aria-hidden="true"></i>Akses modul utama</h3><p class="mt-1 text-xs text-slate-500">Pilih kelompok kerja yang ingin dibuka.</p></header>
            <div class="grid grid-cols-2 gap-1 border-b border-slate-200 bg-slate-50 p-2" role="tablist" aria-label="Kelompok modul">
                @foreach(['akademik' => 'Akademik Cabang', 'monitoring' => 'Monitoring'] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-white text-brand-700 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800'" class="h-9 rounded-lg px-2 text-[11px] font-bold" role="tab" :aria-selected="tab === '{{ $key }}'">{{ $label }}</button>
                @endforeach
            </div>
            @foreach($moduleGroups as $key => $modules)
                <div x-cloak x-show="tab === '{{ $key }}'" class="grid gap-2 p-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                    @foreach($modules as $module)
                        <a href="{{ route($module['route']) }}" class="flex min-w-0 items-center gap-3 rounded-xl border border-slate-200 p-3 text-slate-700 no-underline transition hover:border-brand-200 hover:bg-brand-50/40"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $module['tone'] }}"><i class="fas {{ $module['icon'] }}" aria-hidden="true"></i></span><span class="min-w-0"><strong class="block truncate text-sm text-slate-900">{{ $module['label'] }}</strong><span class="mt-0.5 block truncate text-[11px] text-slate-500">{{ $module['description'] }}</span></span></a>
                    @endforeach
                </div>
            @endforeach
            <aside x-data="{ visible: true }" x-init="setTimeout(() => visible = false, 5000)" x-show="visible" x-transition class="mx-4 mb-4 rounded-xl border border-cyan-200 bg-cyan-50 p-3 text-cyan-900"><div class="flex items-center justify-between gap-3"><div class="min-w-0"><p class="text-xs font-extrabold"><i class="fas fa-laptop-code mr-1.5 text-cyan-600" aria-hidden="true"></i>Menemukan kendala?</p><p class="mt-1 text-[11px] text-cyan-700">Laporkan masalah agar dapat ditindaklanjuti.</p></div><a href="https://wa.me/6282113100791?text=Halo%20Developer,%20saya%20menemukan%20kendala/bug%20pada%20sistem" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-lg bg-cyan-600 px-3 text-[11px] font-bold text-white no-underline hover:bg-cyan-700"><i class="fab fa-whatsapp" aria-hidden="true"></i>Hubungi</a></div></aside>
        </article>
    </section>
</div>
@endsection
