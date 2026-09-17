@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')
@section('page-title', 'Dashboard Wali Kelas')
@section('page-subtitle', 'Pantau kelas dan tindak lanjut siswa')

@section('content')
<div class="min-w-0 w-full space-y-4">
    @if(isset($message) || ! $kelas)
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center shadow-sm sm:p-8">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700"><i class="fas fa-user-slash" aria-hidden="true"></i></span>
            <h1 class="mt-4 text-lg font-extrabold text-slate-900">Kelas belum tersedia</h1>
            <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">{{ $message ?? $error ?? 'Anda belum ditugaskan sebagai wali kelas. Silakan hubungi bagian Admin Kurikulum.' }}</p>
        </section>
    @else
        @php
            $totalPresensi = array_sum($presensiStats);
            $stats = [
                ['value' => $totalSiswa, 'label' => 'Siswa aktif', 'icon' => 'fa-users', 'tone' => 'bg-sky-50 text-sky-700'],
                ['value' => $presensiStats['hadir'], 'label' => 'Hadir hari ini', 'icon' => 'fa-user-check', 'tone' => 'bg-emerald-50 text-emerald-700'],
                ['value' => $izinMenungguValidasi, 'label' => 'Izin perlu validasi', 'icon' => 'fa-clipboard-check', 'tone' => 'bg-amber-50 text-amber-700'],
                ['value' => $raporBelumSelesai, 'label' => 'Rapor draft', 'icon' => 'fa-file-lines', 'tone' => 'bg-violet-50 text-violet-700'],
            ];
        @endphp

        <header class="flex flex-col gap-3 overflow-hidden rounded-2xl bg-gradient-to-r from-sky-800 to-cyan-700 px-4 py-4 !text-white shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5 sm:py-5">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-cyan-100">Ruang Wali Kelas</p>
                <h1 class="mt-1 text-xl font-extrabold !text-white sm:text-2xl">Kelas {{ $kelas->nama_kelas }}</h1>
                <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-sky-50"><span>{{ strtoupper($kelas->jenjang) }}</span><span>{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span><span>{{ $kelas->cabang->nama_cabang ?? '-' }}</span></p>
            </div>
            @if($kelasList->count() > 1)
                <a href="{{ route('wali.pilih-kelas') }}" class="inline-flex min-h-9 shrink-0 items-center justify-center gap-2 rounded-lg border border-white/30 bg-white/15 px-3 text-xs font-bold !text-white transition hover:bg-white/25"><i class="fas fa-right-left" aria-hidden="true"></i>Ganti kelas</a>
            @endif
        </header>

        <section class="grid min-w-0 grid-cols-2 gap-2.5 lg:grid-cols-4" aria-label="Ringkasan kelas">
            @foreach($stats as $stat)
                <article class="flex min-w-0 items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-3 shadow-sm sm:px-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $stat['tone'] }}"><i class="fas {{ $stat['icon'] }} text-xs" aria-hidden="true"></i></span>
                    <div class="min-w-0">
                        <p class="text-lg font-extrabold leading-none text-slate-900 sm:text-xl">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-tight tracking-wide text-slate-500 sm:text-[11px]">{{ $stat['label'] }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid min-w-0 gap-4 xl:grid-cols-[minmax(0,1.5fr)_minmax(280px,1fr)]">
            <div class="min-w-0 space-y-4">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3"><div><h2 class="text-sm font-extrabold text-slate-900">Jadwal hari ini</h2><p class="text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p></div><a href="{{ route('wali.jadwal.index') }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg px-2 text-xs font-bold text-sky-700 hover:bg-sky-50">Lihat semua<i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
                    @if($jadwalHariIni->isNotEmpty())
                        <ul class="divide-y divide-slate-100">
                            @foreach($jadwalHariIni as $jadwal)
                                <li class="flex min-w-0 flex-wrap items-start gap-3 px-5 py-3 sm:flex-nowrap"><span class="shrink-0 rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-bold tabular-nums text-sky-800">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span><div class="min-w-0"><p class="font-bold text-slate-900">{{ $jadwal->mataPelajaran->nama_mapel ?? 'Mata pelajaran belum tersedia' }}</p><p class="text-xs text-slate-500">{{ $jadwal->guru->nama_lengkap ?? 'Pengajar belum ditentukan' }}</p></div></li>
                            @endforeach
                        </ul>
                    @else
                        <p class="px-5 py-9 text-center text-sm text-slate-500"><i class="fas fa-mug-hot mr-2 text-sky-600" aria-hidden="true"></i>Tidak ada pelajaran terjadwal hari ini.</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2"><div><h2 class="text-sm font-extrabold text-slate-900">Rekap presensi</h2><p class="text-xs text-slate-500">{{ $totalPresensi }} dari {{ $totalSiswa }} siswa tercatat hari ini</p></div><a href="{{ route('wali.presensi.index') }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg px-2 text-xs font-bold text-sky-700 hover:bg-sky-50">Kelola presensi<i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
                    <dl class="mt-3 grid grid-cols-4 gap-1.5 sm:gap-2">
                        <div class="min-w-0 rounded-lg bg-emerald-50 px-2 py-2"><dt class="truncate text-[10px] font-semibold text-emerald-800">Hadir</dt><dd class="text-lg font-extrabold leading-tight text-emerald-900">{{ $presensiStats['hadir'] }}</dd></div>
                        <div class="min-w-0 rounded-lg bg-amber-50 px-2 py-2"><dt class="truncate text-[10px] font-semibold text-amber-800">Sakit</dt><dd class="text-lg font-extrabold leading-tight text-amber-900">{{ $presensiStats['sakit'] }}</dd></div>
                        <div class="min-w-0 rounded-lg bg-sky-50 px-2 py-2"><dt class="truncate text-[10px] font-semibold text-sky-800">Izin</dt><dd class="text-lg font-extrabold leading-tight text-sky-900">{{ $presensiStats['izin'] }}</dd></div>
                        <div class="min-w-0 rounded-lg bg-rose-50 px-2 py-2"><dt class="truncate text-[10px] font-semibold text-rose-800">Alpha</dt><dd class="text-lg font-extrabold leading-tight text-rose-900">{{ $presensiStats['alpha'] }}</dd></div>
                    </dl>
                </section>
            </div>

            <aside class="min-w-0 space-y-4">
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><h2 class="text-sm font-extrabold text-slate-900">Tindak lanjut</h2><p class="text-xs text-slate-500">Pekerjaan kelas yang perlu dipantau.</p><div class="mt-3 space-y-2">
                    <a href="{{ route('wali.presensi.validasi-izin') }}" class="flex min-h-11 items-center justify-between gap-3 rounded-xl border border-slate-200 px-3 text-xs font-semibold text-slate-800 transition hover:border-amber-300 hover:bg-amber-50"><span><i class="fas fa-clipboard-check mr-2 text-amber-600" aria-hidden="true"></i>Validasi izin</span><span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">{{ $izinMenungguValidasi }}</span></a>
                    <a href="{{ route('wali.rapor.index') }}" class="flex min-h-11 items-center justify-between gap-3 rounded-xl border border-slate-200 px-3 text-xs font-semibold text-slate-800 transition hover:border-violet-300 hover:bg-violet-50"><span><i class="fas fa-file-lines mr-2 text-violet-600" aria-hidden="true"></i>Rapor draft</span><span class="rounded-full bg-violet-100 px-2 py-0.5 text-xs font-bold text-violet-800">{{ $raporBelumSelesai }}</span></a>
                </div></section>
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><h2 class="text-sm font-extrabold text-slate-900">Akses cepat</h2><div class="mt-3 grid grid-cols-2 gap-2">
                    @foreach([['label' => 'Nilai siswa', 'route' => 'wali.nilai.index', 'icon' => 'fa-chart-line'], ['label' => 'Prediksi kenaikan', 'route' => 'wali.kenaikan-kelas.prediction', 'icon' => 'fa-chart-column'], ['label' => 'Validasi akses', 'route' => 'wali.validasi-akses.index', 'icon' => 'fa-user-check'], ['label' => 'Arsip kelas', 'route' => 'wali.arsip.index', 'icon' => 'fa-box-archive']] as $shortcut)
                        <a href="{{ route($shortcut['route']) }}" class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-xl border border-slate-200 p-2 text-center text-[11px] font-bold leading-tight text-slate-700 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800"><i class="fas {{ $shortcut['icon'] }} text-sm text-sky-600" aria-hidden="true"></i>{{ $shortcut['label'] }}</a>
                    @endforeach
                </div></section>
            </aside>
        </div>
    @endif
</div>
@endsection
