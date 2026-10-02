@extends('layouts.app')

@section('title', 'Dashboard SIA')
@section('page-title', 'Sistem Informasi Akademik')
@section('page-subtitle', 'Selamat datang, ' . $siswa->nama_lengkap)

@section('content')
@php
    $lmsAktif = $lmsAktif ?? false;

    $statCards = [
        ['label' => 'Hadir', 'value' => $absensi['hadir'] ?? 0, 'icon' => 'fa-circle-check', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Sakit', 'value' => $absensi['sakit'] ?? 0, 'icon' => 'fa-notes-medical', 'tone' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Izin', 'value' => $absensi['izin'] ?? 0, 'icon' => 'fa-envelope-open-text', 'tone' => 'bg-sky-50 text-sky-600'],
        ['label' => 'Alpha', 'value' => $absensi['alpha'] ?? 0, 'icon' => 'fa-circle-xmark', 'tone' => 'bg-rose-50 text-rose-600'],
    ];

    $tugasTotal = (int) ($performa['tugas']['total'] ?? 0);
    $tugasSelesai = (int) ($performa['tugas']['selesai'] ?? 0);
    $tugasBelum = max($tugasTotal - $tugasSelesai, 0);
    $tugasPersen = $tugasTotal > 0 ? min(100, round($tugasSelesai / $tugasTotal * 100)) : 0;
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 via-blue-600 to-cyan-500 text-white shadow-lg shadow-brand-900/15">
        <div class="flex items-center gap-4 p-5 sm:p-6">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white/20 text-2xl font-extrabold text-white ring-2 ring-white/40 sm:h-20 sm:w-20">
                @if(auth()->user()->foto_profil)
                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="Foto profil" class="h-full w-full object-cover">
                @elseif($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto siswa" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-blue-100">Semester {{ $semester }}</p>
                <h2 class="mt-0.5 truncate text-xl font-extrabold !text-white sm:text-2xl">Halo, {{ explode(' ', $siswa->nama_lengkap)[0] }}!</h2>
                <div class="mt-3 flex flex-wrap gap-2 text-[10px] font-bold">
                    <span class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-inset ring-white/20"><i class="fa-solid fa-school mr-1.5" aria-hidden="true"></i>{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                    <span class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-inset ring-white/20"><i class="fa-solid fa-calendar-days mr-1.5" aria-hidden="true"></i>{{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    <span class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-inset ring-white/20"><i class="fa-solid fa-id-card mr-1.5" aria-hidden="true"></i>{{ $siswa->nis ?? '-' }}</span>
                </div>
            </div>
        </div>
    </section>

    @if(isset($pengumuman) && $pengumuman->count() > 0)
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
            <h2 class="flex items-center gap-2 text-sm font-extrabold text-amber-900"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i>Pengumuman</h2>
            <div class="mt-3 grid gap-3 md:grid-cols-2">
                @foreach($pengumuman->take(2) as $item)
                    <article class="min-w-0 rounded-xl border border-amber-100 bg-white p-4">
                        <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $item->judul }}">{{ $item->judul }}</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-600">{{ Str::limit($item->isi_pengumuman, 100) }}</p>
                        <p class="mt-2 text-[11px] font-semibold text-slate-500"><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>{{ $item->tanggal_pengumuman->format('d M Y') }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Rekap presensi bulan ini">
        @foreach($statCards as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }} bulan ini</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    <div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(19rem,1fr)]">
        <div class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5">
                    <div class="min-w-0">
                        <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-clock text-brand-600" aria-hidden="true"></i>Jadwal hari ini</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    </div>
                    @if($lmsAktif)
                        <a href="{{ route('siswa.lms.jadwal') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold text-brand-700 no-underline hover:text-brand-800">Lihat semua<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @endif
                </header>

                @forelse($jadwalHariIni ?? [] as $jadwal)
                    <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50 sm:gap-4">
                        <span class="w-12 shrink-0 text-center text-sm font-extrabold tabular-nums text-brand-700">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                        <span class="h-10 w-1 shrink-0 rounded-full bg-brand-200" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $jadwal->mataPelajaran->nama_mapel }}">{{ $jadwal->mataPelajaran->nama_mapel }}</h3>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</p>
                        </div>
                        @if($lmsAktif)
                            <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="inline-flex min-h-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 px-3 text-xs font-bold text-brand-700 no-underline ring-1 ring-inset ring-brand-100 hover:bg-brand-100">Masuk</a>
                        @endif
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></span>
                        <h3 class="mt-4 font-extrabold text-slate-900">Tidak ada jadwal</h3>
                        <p class="mt-1 text-sm text-slate-500">Tidak ada pelajaran hari ini. Selamat beristirahat!</p>
                    </div>
                @endforelse
            </section>

            @if($lmsAktif)
                <div class="grid min-w-0 gap-5 lg:grid-cols-2">
                    <section class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <header class="border-b border-slate-200 p-4 sm:p-5">
                            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-list-check text-rose-500" aria-hidden="true"></i>Tugas mendatang</h2>
                        </header>

                        @forelse($tugasList as $tugas)
                            @php
                                $deadline = \Carbon\Carbon::parse($tugas->tanggal_deadline);
                                $diffDays = now()->diffInDays($deadline, false);
                                $isUrgent = $diffDays <= 1;
                                $isWarning = $diffDays > 1 && $diffDays <= 3;
                                $tugasTone = $isUrgent ? 'bg-rose-50 text-rose-600' : ($isWarning ? 'bg-amber-50 text-amber-600' : 'bg-sky-50 text-sky-600');
                                $tugasIcon = $isUrgent ? 'fa-triangle-exclamation' : ($isWarning ? 'fa-clock' : 'fa-file-lines');
                            @endphp
                            <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tugasTone }}"><i class="fa-solid {{ $tugasIcon }}" aria-hidden="true"></i></span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $tugas->judul_tugas }}">{{ $tugas->judul_tugas }}</h3>
                                    <p class="mt-0.5 flex min-w-0 items-center gap-2 text-[11px] text-slate-500">
                                        <span class="truncate">{{ $tugas->mataPelajaran->nama_mapel }} · {{ $deadline->locale('id')->isoFormat('D MMM') }}</span>
                                        @if($isUrgent)<span class="shrink-0 rounded-full bg-rose-50 px-2 py-0.5 text-[9px] font-extrabold text-rose-700">URGENT</span>@endif
                                    </p>
                                </div>
                                <a href="{{ route('siswa.lms.mapel.show', $tugas->mata_pelajaran_id) }}" class="inline-flex min-h-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200">Lihat</a>
                            </article>
                        @empty
                            <div class="flex flex-1 flex-col items-center justify-center px-5 py-12 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                                <h3 class="mt-4 font-extrabold text-slate-900">Semua selesai</h3>
                                <p class="mt-1 text-sm text-slate-500">Tidak ada tugas yang harus dikerjakan.</p>
                            </div>
                        @endforelse
                    </section>

                    <section class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5">
                            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-trophy text-amber-500" aria-hidden="true"></i>Nilai terbaru</h2>
                            <a href="{{ route('siswa.sia.penilaian') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold text-brand-700 no-underline hover:text-brand-800">Semua<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        </header>

                        @forelse($nilaiTerbaru as $nilai)
                            <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-star" aria-hidden="true"></i></span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $nilai->mataPelajaran->nama_mapel }}">{{ $nilai->mataPelajaran->nama_mapel }}</h3>
                                    <span class="mt-1 inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-extrabold uppercase text-slate-600">Semester {{ $nilai->semester }}</span>
                                </div>
                                <strong class="shrink-0 text-lg font-extrabold tabular-nums {{ is_null($nilai->nilai_akhir) ? 'text-slate-400' : 'text-slate-900' }}" title="Nilai akhir">{{ is_null($nilai->nilai_akhir) ? '-' : number_format($nilai->nilai_akhir, 1) }}</strong>
                            </article>
                        @empty
                            <div class="flex flex-1 flex-col items-center justify-center px-5 py-12 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-file-lines" aria-hidden="true"></i></span>
                                <h3 class="mt-4 font-extrabold text-slate-900">Belum ada nilai</h3>
                                <p class="mt-1 text-sm text-slate-500">Nilai akan muncul setelah guru menilai tugas Anda.</p>
                            </div>
                        @endforelse
                    </section>
                </div>
            @endif
        </div>

        <aside class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h2 class="flex items-center gap-2 font-extrabold text-slate-950"><i class="fa-solid fa-bolt text-amber-500" aria-hidden="true"></i>Akses cepat</h2></header>
                <div class="grid grid-cols-2 gap-3 p-4">
                    @foreach([
                        ['route' => route('siswa.sia.presensi.index'), 'icon' => 'fa-calendar-check', 'label' => 'Presensi', 'tone' => 'text-blue-700 bg-blue-50'],
                        ['route' => route('siswa.sia.penilaian'), 'icon' => 'fa-chart-line', 'label' => 'Data penilaian', 'tone' => 'text-emerald-700 bg-emerald-50'],
                    ] as $link)
                        <a href="{{ $link['route'] }}" class="group flex min-h-24 min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 p-3 text-center no-underline transition hover:border-brand-300 hover:bg-blue-50/50">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $link['tone'] }}"><i class="fa-solid {{ $link['icon'] }}" aria-hidden="true"></i></span>
                            <span class="text-xs font-bold leading-5 text-slate-700 group-hover:text-brand-700">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            @if($lmsAktif)
                <section class="flex items-center gap-4 overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 p-5 text-white shadow-lg shadow-indigo-900/15">
                    <span class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 text-xl sm:flex"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base font-extrabold !text-white">HOK-LMS</h2>
                        <p class="mt-0.5 text-xs leading-5 text-indigo-50">Kerjakan tugas dan materi online hari ini.</p>
                    </div>
                    <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center whitespace-nowrap rounded-xl bg-white px-4 text-xs font-extrabold text-indigo-700 no-underline shadow-sm transition hover:bg-indigo-50">Masuk LMS</a>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h2 class="flex items-center gap-2 font-extrabold text-slate-950"><i class="fa-solid fa-chart-pie text-violet-500" aria-hidden="true"></i>Progres tugas</h2></header>
                    <div class="flex items-center gap-5 p-5">
                        <div class="relative h-28 w-28 shrink-0">
                            <svg viewBox="0 0 36 36" class="h-full w-full -rotate-90" role="img" aria-label="{{ $tugasPersen }} persen tugas selesai">
                                <circle cx="18" cy="18" r="16" pathLength="100" class="fill-none stroke-slate-200 stroke-[3.5]" />
                                @if($tugasPersen > 0)
                                    <circle cx="18" cy="18" r="16" pathLength="100" stroke-dasharray="{{ $tugasPersen }} 100" stroke-linecap="round" class="fill-none stroke-emerald-500 stroke-[3.5]" />
                                @endif
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-xl font-extrabold tabular-nums text-slate-900">{{ $tugasPersen }}%</span>
                        </div>
                        <dl class="min-w-0 flex-1 space-y-3">
                            <div class="flex items-center justify-between gap-3"><dt class="flex items-center gap-2 text-xs font-bold text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Selesai</dt><dd class="text-sm font-extrabold tabular-nums text-slate-900">{{ $tugasSelesai }}</dd></div>
                            <div class="flex items-center justify-between gap-3"><dt class="flex items-center gap-2 text-xs font-bold text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>Belum</dt><dd class="text-sm font-extrabold tabular-nums text-slate-900">{{ $tugasBelum }}</dd></div>
                            <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-xs font-bold text-slate-500">Total tugas</dt><dd class="text-sm font-extrabold tabular-nums text-slate-900">{{ $tugasTotal }}</dd></div>
                        </dl>
                    </div>
                </section>
            @endif
        </aside>
    </div>

    @if(isset($flyers) && $flyers->count() > 0)
        @php $flyer = $flyers->first(); @endphp
        <div x-data="{ open: false, key: 'flyerShown_{{ auth()->id() }}', init() { try { if (!localStorage.getItem(this.key)) setTimeout(() => this.open = true, 1200); } catch (e) {} }, close() { this.open = false; try { localStorage.setItem(this.key, 'true'); } catch (e) {} } }" @keydown.escape.window="close()">
            <template x-teleport="body">
                <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" @click.self="close()" role="dialog" aria-modal="true" aria-label="{{ $flyer->judul }}">
                    <div class="relative max-h-full w-full max-w-md overflow-y-auto rounded-2xl bg-white shadow-2xl">
                        <button type="button" @click="close()" class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-slate-600 shadow ring-1 ring-slate-200 hover:bg-white hover:text-slate-900" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                        <img src="{{ $flyer->gambar_url }}" alt="{{ $flyer->judul }}" class="max-h-[60vh] w-full object-cover">
                        <div class="p-5 text-center">
                            <h3 class="text-base font-extrabold text-brand-700">{{ $flyer->judul }}</h3>
                            @if($flyer->deskripsi)<p class="mt-2 text-sm leading-6 text-slate-600">{{ $flyer->deskripsi }}</p>@endif
                            @if($flyer->link_url)
                                <a href="{{ $flyer->link_url }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-full bg-brand-600 px-5 text-xs font-extrabold text-white no-underline hover:bg-brand-700">Lihat selengkapnya</a>
                            @endif
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif
</div>
@endsection
