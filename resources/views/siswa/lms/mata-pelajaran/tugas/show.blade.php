@extends('layouts.lms')

@section('title', 'Tugas - ' . $tugas->judul_tugas)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Pengerjaan Tugas')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $deadline = $tugas->tanggal_deadline;
    $isExpired = now()->gt($deadline);
    $diff = now()->diff($deadline);
    $sisaParts = array_filter([
        $diff->days > 0 ? $diff->days . ' hari' : null,
        $diff->h > 0 ? $diff->h . ' jam' : null,
        $diff->i > 0 ? $diff->i . ' menit' : null,
    ]);
    $timeRemaining = empty($sisaParts) ? 'Kurang dari 1 menit' : implode(' ', $sisaParts);
    [$deadlineTone, $deadlineIcon, $deadlineText] = match (true) {
        $isExpired => ['from-rose-600 to-rose-700', 'fa-circle-exclamation', 'Waktu sudah habis!'],
        $diff->days == 0 => ['from-amber-500 to-orange-600', 'fa-triangle-exclamation', 'Segera dikumpulkan! (' . $timeRemaining . ' lagi)'],
        default => ['from-indigo-600 to-violet-600', 'fa-circle-check', 'Tersisa ' . $timeRemaining . ' lagi'],
    };

    $canSubmit = true;
    if ($existingSubmission) {
        if (! $tugas->bisa_diulang) {
            $canSubmit = false;
        } elseif ($tugas->batas_pengulangan > 0 && $existingSubmission->pengulangan_ke > $tugas->batas_pengulangan) {
            $canSubmit = false;
        }
    }
    $isDisabled = $isExpired || ! $canSubmit;

    $statusInfo = [
        'dikerjakan' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'dinilai' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'terlambat' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
    $field = 'block w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500';
    $btn = 'inline-flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl px-5 text-sm font-bold no-underline transition';
    $note = 'flex items-start gap-2 rounded-xl px-3 py-2.5 text-sm';
@endphp

<div class="min-w-0 w-full space-y-4">
    <nav class="flex min-w-0 flex-wrap items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex items-center gap-1.5 no-underline hover:text-indigo-700"><i class="fa-solid fa-house" aria-hidden="true"></i>Beranda LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="no-underline hover:text-indigo-700">{{ $mataPelajaran->nama_mapel }}</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <span class="max-w-[16rem] truncate text-slate-800">{{ $tugas->judul_tugas }}</span>
    </nav>

    <section class="rounded-3xl bg-gradient-to-br p-5 text-white shadow-lg shadow-slate-900/10 sm:p-6 {{ $deadlineTone }}">
        <p class="text-xs font-bold uppercase tracking-wide text-white/80"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>Tenggat</p>
        <h2 class="mt-1 text-xl font-extrabold text-white sm:text-2xl">{{ $deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</h2>
        <p class="mt-1 text-sm font-semibold text-white/90"><i class="fa-solid {{ $deadlineIcon }} mr-1" aria-hidden="true"></i>{{ $deadlineText }}</p>
    </section>

    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-100 p-4 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <h2 class="min-w-0 text-lg font-extrabold leading-snug text-slate-900 sm:text-xl">{{ $tugas->judul_tugas }}</h2>
                @if($existingSubmission)
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 ring-inset {{ $statusInfo[$existingSubmission->status] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">Status: {{ ucwords(str_replace('_', ' ', $existingSubmission->status)) }}</span>
                @else
                    <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 ring-1 ring-inset ring-slate-200">Belum dikerjakan</span>
                @endif
            </div>
            <dl class="mt-3 grid gap-2 text-xs text-slate-600 sm:grid-cols-2">
                <div class="flex items-center gap-2"><dt class="text-slate-400"><i class="fa-solid fa-user-tie" aria-hidden="true"></i> Guru</dt><dd class="font-semibold text-slate-800">{{ $tugas->guru->nama_lengkap }}</dd></div>
                <div class="flex items-center gap-2"><dt class="text-slate-400"><i class="fa-regular fa-calendar-plus" aria-hidden="true"></i> Dibuka</dt><dd class="font-semibold text-slate-800">{{ $tugas->tanggal_mulai->copy()->locale('id')->translatedFormat('d M Y') }}</dd></div>
                <div class="flex items-center gap-2"><dt class="text-slate-400"><i class="fa-regular fa-calendar-xmark" aria-hidden="true"></i> Ditutup</dt><dd class="font-semibold text-slate-800">{{ $tugas->tanggal_deadline->copy()->locale('id')->translatedFormat('d M Y, H:i') }}</dd></div>
                <div class="flex items-center gap-2">
                    @if($tugas->bisa_diulang)
                        <dt class="text-slate-400"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Sisa pengeditan</dt>
                        <dd class="font-semibold text-slate-800">{{ $tugas->batas_pengulangan ? max(0, $tugas->batas_pengulangan - (($existingSubmission->pengulangan_ke ?? 1) - 1)) . ' kali' : 'Tak terbatas' }}</dd>
                    @else
                        <dt class="text-slate-400"><i class="fa-solid fa-lock" aria-hidden="true"></i> Batas pengeditan</dt>
                        <dd class="font-semibold text-slate-800">1 kali</dd>
                    @endif
                </div>
            </dl>
        </header>

        <div class="space-y-5 p-4 sm:p-6">
            @php
                $lampiranMedia = $tugas->file_tugas
                    && in_array(strtolower(pathinfo($tugas->file_tugas, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'm4v', 'mov', 'webm', 'ogg', 'ogv'], true);
            @endphp
            {{-- Gambar/video lampiran tampil di bawah deskripsi. --}}
            <section class="grid items-start gap-4">
                <div class="flex min-w-0 flex-col">
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-file-lines text-indigo-500" aria-hidden="true"></i>Deskripsi tugas</h3>
                    <div class="mt-2 whitespace-pre-line break-words rounded-xl bg-slate-50 p-3 text-sm leading-7 text-slate-700 sm:p-4">{{ $tugas->deskripsi }}</div>
                    @if($tugas->file_tugas && ! $lampiranMedia)
                        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-2 rounded-xl border border-indigo-100 bg-indigo-50/50 p-3">
                            <span class="flex items-center gap-2 text-xs font-extrabold text-slate-700"><i class="fa-solid fa-paperclip text-indigo-500" aria-hidden="true"></i>Lampiran dari guru</span>
                            <x-file-preview :path="$tugas->file_tugas" :title="$tugas->judul_tugas" label="Lihat Tugas" />
                        </div>
                    @endif
                </div>
                @if($lampiranMedia)
                    <div class="min-w-0 max-w-sm">
                        <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-paperclip text-indigo-500" aria-hidden="true"></i>Lampiran dari guru</h3>
                        <div class="mt-2"><x-file-preview :path="$tugas->file_tugas" :title="$tugas->judul_tugas" label="Lihat Tugas" fill /></div>
                    </div>
                @endif
            </section>

            @if(!$isExpired || $existingSubmission)
                <section class="space-y-4 border-t border-slate-100 pt-5">
                    <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-pen text-indigo-500" aria-hidden="true"></i>{{ $existingSubmission ? 'Edit jawaban' : 'Kerjakan tugas' }}</h3>

                    @if($existingSubmission && $existingSubmission->status === 'dinilai')
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                            <p class="flex items-center gap-2 font-extrabold"><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Tugas sudah dinilai</p>
                            <p class="mt-1"><strong>Nilai:</strong> {{ $existingSubmission->nilai }}</p>
                            @if($existingSubmission->feedback_guru)
                                <p class="mt-1 whitespace-pre-line"><strong>Umpan balik guru:</strong> {{ $existingSubmission->feedback_guru }}</p>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('siswa.lms.mapel.tugas.submit', [$mataPelajaran->id, $tugas->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-slate-700">Jawaban (teks)</span>
                            <textarea name="jawaban_text" rows="8" placeholder="Tulis jawaban Anda di sini..." data-autogrow {{ $isDisabled ? 'disabled' : '' }}
                                class="{{ $field }} @error('jawaban_text') border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-300 focus:border-indigo-500 focus:ring-indigo-100 @enderror">{{ old('jawaban_text', $existingSubmission->jawaban_text ?? '') }}</textarea>
                            @error('jawaban_text')<span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>@enderror
                        </label>

                        <div>
                            <label class="block">
                                <span class="mb-1.5 block text-xs font-bold text-slate-700">Unggah file jawaban <span class="font-normal text-slate-400">(opsional)</span></span>
                                <input type="file" name="file_jawaban" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.mp4" {{ $isDisabled ? 'disabled' : '' }}
                                    class="block w-full rounded-xl border bg-white px-3 py-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-100 @error('file_jawaban') border-rose-400 @else border-slate-300 @enderror">
                            </label>
                            <p class="mt-1 text-[11px] text-slate-500">Format: PDF, Word, Excel, PowerPoint, gambar (JPG/PNG), video (MP4). Maksimal 10MB.</p>
                            @error('file_jawaban')<span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>@enderror

                            @if($existingSubmission && $existingSubmission->file_jawaban)
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="text-xs text-slate-500">File sebelumnya:</span>
                                    <x-file-preview :path="$existingSubmission->file_jawaban" title="File jawaban" label="Lihat File" />
                                </div>
                            @endif
                        </div>

                        @if(!$isExpired)
                            @if($canSubmit)
                                <div class="grid grid-cols-1 gap-2 sm:flex">
                                    <button type="submit" class="{{ $btn }} bg-indigo-600 text-white hover:bg-indigo-700"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i>{{ $existingSubmission ? 'Perbarui jawaban' : 'Kirim jawaban' }}</button>
                                    <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="{{ $btn }} border border-slate-300 bg-white text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali</a>
                                </div>
                            @else
                                <p class="{{ $note }} bg-amber-50 text-amber-800"><i class="fa-solid fa-lock mt-0.5" aria-hidden="true"></i>Batas maksimal pengeditan jawaban telah tercapai.</p>
                                <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="{{ $btn }} border border-slate-300 bg-white text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali</a>
                            @endif
                        @else
                            <p class="{{ $note }} bg-rose-50 text-rose-800"><i class="fa-solid fa-lock mt-0.5" aria-hidden="true"></i>Tenggat sudah lewat. Jawaban tidak dapat diubah.</p>
                        @endif
                    </form>

                    @if($existingSubmission)
                        <p class="{{ $note }} bg-sky-50 text-xs text-sky-800">
                            <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                            <span>Terakhir dikirim: {{ $existingSubmission->tanggal_submit ? $existingSubmission->tanggal_submit->copy()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                                @if($existingSubmission->status === 'terlambat')<strong class="text-rose-600">(Terlambat)</strong>@endif
                            </span>
                        </p>
                    @endif
                </section>
            @else
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
                    <p class="flex items-center gap-2 font-extrabold"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>Tenggat sudah lewat</p>
                    <p class="mt-1">Maaf, waktu pengerjaan tugas sudah habis. Anda tidak dapat mengirim jawaban.</p>
                </div>
            @endif
        </div>
    </article>
</div>
@endsection
