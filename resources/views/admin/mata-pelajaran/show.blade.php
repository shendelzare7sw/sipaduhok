@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran')
@section('page-title', 'Detail Mata Pelajaran')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('content')
@php
    $jenjangTone = [
        'KB' => 'bg-pink-50 text-pink-700', 'TKA' => 'bg-fuchsia-50 text-fuchsia-700',
        'TKB' => 'bg-violet-50 text-violet-700', 'SD' => 'bg-blue-50 text-blue-700',
        'SMP' => 'bg-emerald-50 text-emerald-700', 'SMA' => 'bg-amber-50 text-amber-700',
    ];
    $statusTone = ['aktif' => 'bg-emerald-50 text-emerald-700', 'kosong' => 'bg-amber-50 text-amber-700', 'diganti' => 'bg-blue-50 text-blue-700'];
@endphp

<div class="min-w-0 w-full space-y-4">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-brand-700 via-brand-600 to-blue-500 p-5 text-white sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-4"><span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl ring-1 ring-white/25"><i class="fas fa-book-open" aria-hidden="true"></i></span><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-blue-100">Mata pelajaran</p><h2 class="mt-1 break-words text-xl font-extrabold !text-white sm:text-2xl">{{ $mataPelajaran->nama_mapel }}</h2><p class="mt-1 font-mono text-xs text-blue-100">{{ $mataPelajaran->kode_mapel ?: 'Tanpa kode' }}</p></div></div>
                <div class="flex flex-wrap gap-2"><span class="rounded-full bg-white px-3 py-1.5 text-xs font-extrabold text-brand-700">{{ $mataPelajaran->jenjang }}</span>@if($mataPelajaran->kelompok)<span class="rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold ring-1 ring-white/25">Kelompok {{ $mataPelajaran->kelompok }}</span>@endif</div>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-3 sm:px-5"><a href="{{ route('admin.mata-pelajaran.index') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a><a href="{{ route('admin.mata-pelajaran.edit', $mataPelajaran) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 text-xs font-bold text-white no-underline hover:bg-amber-600"><i class="fas fa-edit" aria-hidden="true"></i>Edit data</a></div>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:grid-cols-4">
        @foreach([
            ['label' => 'Total jadwal', 'value' => $stats['totalJadwal'], 'icon' => 'fa-calendar-days', 'tone' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Kelas', 'value' => $stats['totalKelas'], 'icon' => 'fa-door-open', 'tone' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Guru', 'value' => $stats['totalGuru'], 'icon' => 'fa-chalkboard-user', 'tone' => 'bg-violet-50 text-violet-600'],
            ['label' => 'Akses siswa', 'value' => $mataPelajaran->filter_agama ?: 'Semua', 'icon' => 'fa-user-shield', 'tone' => 'bg-amber-50 text-amber-600'],
        ] as $item)
            <article class="min-w-0 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $item['label'] }}</p><p class="mt-2 truncate text-lg font-extrabold text-slate-900" title="{{ $item['value'] }}">{{ $item['value'] }}</p></div><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $item['tone'] }}"><i class="fas {{ $item['icon'] }}" aria-hidden="true"></i></span></div></article>
        @endforeach
    </section>

    <section class="grid min-w-0 gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-3.5 sm:px-5"><h3 class="text-sm font-extrabold text-slate-900">Informasi pelajaran</h3></div>
            <dl class="divide-y divide-slate-100">
                @foreach([
                    'Nama' => $mataPelajaran->nama_mapel,
                    'Kode' => $mataPelajaran->kode_mapel ?: '-',
                    'Jenjang' => $mataPelajaran->jenjang,
                    'Kelompok' => $mataPelajaran->kelompok ? 'Kelompok ' . $mataPelajaran->kelompok : 'Belum ditentukan',
                    'Akses agama' => $mataPelajaran->filter_agama ? 'Siswa ' . $mataPelajaran->filter_agama : 'Semua siswa',
                    'Deskripsi' => $mataPelajaran->deskripsi ?: '-',
                ] as $label => $value)
                    <div class="grid min-w-0 gap-1 px-4 py-3 sm:grid-cols-[7rem_minmax(0,1fr)] sm:gap-3 sm:px-5"><dt class="text-xs font-semibold text-slate-500">{{ $label }}</dt><dd class="min-w-0 break-words text-sm font-bold text-slate-800">{{ $value }}</dd></div>
                @endforeach
            </dl>
        </article>

        <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3.5 sm:px-5"><div><h3 class="text-sm font-extrabold text-slate-900">Jadwal yang menggunakan pelajaran ini</h3><p class="mt-0.5 text-xs text-slate-500">Kelas, waktu, dan guru yang sudah ditugaskan.</p></div><span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">{{ $mataPelajaran->jadwalPelajaran->count() }}</span></div>

            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse($mataPelajaran->jadwalPelajaran->sortBy('hari') as $jadwal)
                    <div class="p-4"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate text-sm font-extrabold text-slate-900">{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') ?: 'Belum ada kelas' }}</p><p class="mt-1 truncate text-xs text-slate-500">{{ $jadwal->guru->nama_lengkap ?? 'Guru belum ditentukan' }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTone[$jadwal->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($jadwal->status) }}</span></div><div class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold text-slate-600"><span class="rounded-lg bg-slate-50 px-2.5 py-1.5"><i class="fas fa-calendar-day mr-1 text-brand-500" aria-hidden="true"></i>{{ $jadwal->hari }}</span><span class="rounded-lg bg-slate-50 px-2.5 py-1.5"><i class="fas fa-clock mr-1 text-brand-500" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span></div></div>
                @empty
                    <div class="p-10 text-center text-xs text-slate-500"><i class="fas fa-calendar-xmark mb-3 block text-3xl text-slate-300" aria-hidden="true"></i>Belum ada jadwal yang menggunakan mata pelajaran ini.</div>
                @endforelse
            </div>

            @if($mataPelajaran->jadwalPelajaran->isNotEmpty())
                <div class="hidden overflow-x-auto lg:block"><table class="w-full min-w-[720px] table-fixed text-left text-xs"><colgroup><col class="w-12"><col class="w-[25%]"><col class="w-24"><col class="w-32"><col><col class="w-24"></colgroup><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-3 py-3 text-center">No</th><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Hari</th><th class="px-3 py-3">Waktu</th><th class="px-3 py-3">Guru</th><th class="px-3 py-3">Status</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($mataPelajaran->jadwalPelajaran->sortBy('hari') as $index => $jadwal)<tr class="hover:bg-slate-50"><td class="px-3 py-3 text-center text-slate-400">{{ $index + 1 }}</td><td class="px-3 py-3"><p class="truncate font-bold text-slate-800" title="{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}">{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') ?: '-' }}</p><p class="mt-0.5 truncate text-[10px] text-slate-400">{{ $jadwal->kelas->map(fn($kelas) => $kelas->cabang->nama_cabang ?? '-')->unique()->join(', ') }}</p></td><td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-600">{{ $jadwal->hari }}</td><td class="whitespace-nowrap px-3 py-3 font-mono text-slate-600">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td><td class="px-3 py-3"><p class="truncate text-slate-700" title="{{ $jadwal->guru->nama_lengkap ?? '' }}">{{ $jadwal->guru->nama_lengkap ?? 'Belum ditentukan' }}</p></td><td class="px-3 py-3"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[9px] font-bold {{ $statusTone[$jadwal->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($jadwal->status) }}</span></td></tr>@endforeach</tbody></table></div>
            @endif
        </article>
    </section>
</div>
@endsection
