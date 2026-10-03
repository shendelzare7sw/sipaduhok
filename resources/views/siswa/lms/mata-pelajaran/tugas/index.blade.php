@extends('layouts.lms')

@section('title', 'Daftar Tugas')
@section('page-title', 'Daftar Tugas')
@section('page-subtitle', 'Semua tugas dari berbagai mata pelajaran')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $stats = [
        ['label' => 'Belum dikerjakan', 'value' => $tugasBelum, 'icon' => 'fa-hourglass-start', 'tone' => 'bg-slate-100 text-slate-600'],
        ['label' => 'Menunggu nilai', 'value' => $tugasProses, 'icon' => 'fa-paper-plane', 'tone' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Sudah dinilai', 'value' => $tugasSelesai, 'icon' => 'fa-star', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Terlambat', 'value' => $tugasTerlambat, 'icon' => 'fa-triangle-exclamation', 'tone' => 'bg-rose-50 text-rose-600'],
    ];
    $statusInfo = [
        'belum_dikerjakan' => ['Belum dikerjakan', 'bg-slate-100 text-slate-600'],
        'dikerjakan' => ['Dikerjakan', 'bg-amber-50 text-amber-700'],
        'dinilai' => ['Dinilai', 'bg-emerald-50 text-emerald-700'],
        'terlambat' => ['Terlambat', 'bg-rose-50 text-rose-700'],
    ];
    $field = 'block h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white pl-3 pr-8 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
    $btn = 'inline-flex min-h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 text-xs font-bold no-underline transition';
@endphp

<div class="min-w-0 w-full space-y-4">
    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Ringkasan tugas">
        @foreach($stats as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    <form method="GET" action="{{ route('siswa.lms.tugas.index') }}" class="grid gap-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:grid-cols-2 sm:p-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
        <label class="block">
            <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Mata pelajaran</span>
            <select name="mapel" class="{{ $field }}">
                <option value="">Semua mata pelajaran</option>
                @foreach($mataPelajaranList as $mapel)
                    <option value="{{ $mapel->id }}" @selected(request('mapel') == $mapel->id)>{{ $mapel->nama_mapel }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Status</span>
            <select name="status" class="{{ $field }}">
                <option value="">Semua status</option>
                @foreach($statusInfo as $key => [$label])
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <div class="grid grid-cols-2 gap-2 self-end sm:col-span-2 lg:col-span-1 lg:flex">
            <button type="submit" class="{{ $btn }} h-11 bg-indigo-600 text-white hover:bg-indigo-700"><i class="fa-solid fa-filter" aria-hidden="true"></i>Filter</button>
            <a href="{{ route('siswa.lms.tugas.index') }}" class="{{ $btn }} h-11 border border-slate-300 bg-white text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i>Reset</a>
        </div>
    </form>

    <section class="space-y-3" aria-labelledby="judul-daftar-tugas">
        <h2 id="judul-daftar-tugas" class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-list-check text-indigo-500" aria-hidden="true"></i>Daftar tugas</h2>

        @forelse($tugasList as $tugas)
            @php
                $deadline = $tugas->tanggal_deadline;
                $isExpired = now()->gt($deadline);
                $hoursLeft = now()->diffInHours($deadline, false);
                $submission = $tugas->tugasSiswa->where('siswa_id', $siswa->id)->first();
                $status = $submission ? $submission->status : 'belum_dikerjakan';
                [$deadlineTone, $deadlineIcon, $deadlineText] = match (true) {
                    $isExpired => ['bg-slate-100 text-slate-500', 'fa-circle-xmark', 'Sudah ditutup'],
                    $hoursLeft < 24 => ['bg-rose-50 text-rose-700', 'fa-circle-exclamation', 'Segera! (' . (int) $hoursLeft . ' jam)'],
                    $hoursLeft < 72 => ['bg-amber-50 text-amber-700', 'fa-clock', ceil($hoursLeft / 24) . ' hari lagi'],
                    default => ['bg-emerald-50 text-emerald-700', 'fa-circle-check', ceil($hoursLeft / 24) . ' hari lagi'],
                };
                [$statusLabel, $statusTone] = $statusInfo[$status] ?? [ucwords(str_replace('_', ' ', $status)), 'bg-slate-100 text-slate-600'];
                $detailUrl = route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]);
            @endphp
            <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-xs font-bold text-indigo-700"><i class="fa-solid fa-book mr-1" aria-hidden="true"></i>{{ $tugas->mataPelajaran->nama_mapel }}</span>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $deadlineTone }}"><i class="fa-solid {{ $deadlineIcon }} mr-1" aria-hidden="true"></i>{{ $deadlineText }}</span>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusTone }}">{{ $statusLabel }}</span>
                    </div>
                    <h3 class="mt-1.5 text-sm font-extrabold text-slate-900 sm:text-base">{{ $tugas->judul_tugas }}</h3>
                    <p class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-slate-500">
                        <span><i class="fa-solid fa-user-tie mr-1" aria-hidden="true"></i>{{ $tugas->guru->nama_lengkap }}</span>
                        <span><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>{{ $tugas->tanggal_mulai->translatedFormat('d M Y') }}</span>
                        <span><i class="fa-regular fa-calendar-xmark mr-1" aria-hidden="true"></i>Tenggat {{ $deadline->copy()->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                    </p>
                    @if($tugas->deskripsi)
                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-600">{{ Str::limit($tugas->deskripsi, 120) }}</p>
                    @endif
                    @if($submission && $submission->status === 'dinilai' && $submission->nilai !== null)
                        <p class="mt-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                            <strong><i class="fa-solid fa-star mr-1" aria-hidden="true"></i>Nilai: {{ $submission->nilai }}</strong>
                            @if($submission->feedback_guru)<span class="mt-0.5 block text-emerald-700">{{ Str::limit($submission->feedback_guru, 80) }}</span>@endif
                        </p>
                    @endif
                </div>

                <div class="grid shrink-0 grid-cols-2 gap-2 lg:flex lg:flex-col">
                    @if($submission && $submission->status === 'dinilai')
                        <a href="{{ $detailUrl }}" class="{{ $btn }} bg-emerald-600 text-white hover:bg-emerald-700"><i class="fa-solid fa-eye" aria-hidden="true"></i>Lihat detail</a>
                    @elseif($isExpired && !$submission)
                        <button type="button" disabled class="{{ $btn }} cursor-not-allowed bg-slate-100 text-slate-400"><i class="fa-solid fa-lock" aria-hidden="true"></i>Ditutup</button>
                    @elseif($submission)
                        <a href="{{ $detailUrl }}" class="{{ $btn }} bg-amber-500 text-white hover:bg-amber-600"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Edit jawaban</a>
                    @else
                        <a href="{{ $detailUrl }}" class="{{ $btn }} bg-indigo-600 text-white hover:bg-indigo-700"><i class="fa-solid fa-pen" aria-hidden="true"></i>Kerjakan</a>
                    @endif
                    <a href="{{ route('siswa.lms.mapel.show', $tugas->mata_pelajaran_id) }}" class="{{ $btn }} border border-slate-300 bg-white text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i>Mata pelajaran</a>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-12 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-500"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                <h3 class="mt-3 text-sm font-extrabold text-slate-900">Belum ada tugas</h3>
                <p class="mt-1 text-xs text-slate-500">Belum ada tugas yang tersedia saat ini.</p>
                <a href="{{ route('siswa.lms.dashboard') }}" class="{{ $btn }} mt-4 bg-indigo-600 text-white hover:bg-indigo-700"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke beranda</a>
            </div>
        @endforelse

        @if($tugasList->hasPages())
            <div>{{ $tugasList->links() }}</div>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200 border-l-4 border-l-sky-500 bg-white p-4 shadow-sm">
        <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-circle-info text-sky-500" aria-hidden="true"></i>Informasi</h2>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs leading-5 text-slate-600">
            <li><strong>Belum dikerjakan:</strong> tugas yang belum Anda kerjakan.</li>
            <li><strong>Dikerjakan:</strong> tugas yang sudah Anda kumpulkan, menunggu penilaian.</li>
            <li><strong>Dinilai:</strong> tugas yang sudah dinilai oleh guru.</li>
            <li><strong>Terlambat:</strong> tugas yang dikumpulkan setelah tenggat.</li>
            <li>Tugas yang sudah dinilai tidak dapat diubah lagi.</li>
        </ul>
    </section>
</div>
@endsection
