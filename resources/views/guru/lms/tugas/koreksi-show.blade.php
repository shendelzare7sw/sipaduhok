@extends('layouts.lms-guru')

@section('title', 'Koreksi Jawaban')
@section('page-title', 'Koreksi Jawaban Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php
    $adaJawaban = (bool) ($tugasSiswa->jawaban_text || $tugasSiswa->file_jawaban);
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

<div class="min-w-0 w-full space-y-5"
     data-ai-url="{{ route('guru.lms.tugas.koreksi.ai-suggest', [$kelas->id, $mapel->id, $tugas->id, $tugasSiswa->id]) }}"
     x-data="{
        memproses: false, detik: 0, pesan: '', status: '', sorot: false,
        async analisis() {
            if (this.memproses) return;
            this.memproses = true; this.detik = 0; this.status = 'proses'; this.pesan = 'Sedang menganalisis jawaban (Vision AI)...';
            const timer = setInterval(() => this.detik++, 1000);
            try {
                const res = await fetch(this.$root.dataset.aiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' },
                    body: JSON.stringify({}),
                });
                const data = await res.json();
                if (data.error) throw new Error(data.feedback || 'Terjadi kesalahan pada AI.');
                this.$refs.nilai.value = data.score;
                this.$refs.feedback.value = `[AI Suggestion] ${data.feedback}\n\n` + this.$refs.feedback.value;
                this.sorot = true; setTimeout(() => this.sorot = false, 2000);
                this.status = 'ok'; this.pesan = `Analisis selesai dalam ${this.detik} detik. Saran skor: ${data.score}. Tinjau sebelum menyimpan.`;
            } catch (e) {
                this.status = 'gagal'; this.pesan = `Gagal (${this.detik} detik): ${e.message}`;
            } finally {
                clearInterval(timer); this.memproses = false;
            }
        },
     }">
    <a href="{{ route('guru.lms.tugas.koreksi', [$kelas->id, $mapel->id, $tugasSiswa->tugas_id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar koreksi</a>

    <div class="grid min-w-0 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-5">
            @php
                $ekstensiMedia = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'm4v', 'mov', 'webm', 'ogg', 'ogv'];
                $soalMedia = $tugas->file_tugas && in_array(strtolower(pathinfo($tugas->file_tugas, PATHINFO_EXTENSION)), $ekstensiMedia, true);
                $jawabanMedia = $tugasSiswa->file_jawaban && in_array(strtolower(pathinfo($tugasSiswa->file_jawaban, PATHINFO_EXTENSION)), $ekstensiMedia, true);
            @endphp
            {{-- Teks lalu gambar/video di bawahnya. --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-file-lines text-indigo-600" aria-hidden="true"></i>Tugas: {{ $tugas->judul_tugas }}</h2>
                <div class="mt-3 grid items-start gap-4">
                    <div class="flex min-w-0 flex-col gap-3">
                        <div class="whitespace-pre-line break-words rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">{{ $tugas->deskripsi ?: 'Tidak ada deskripsi.' }}</div>
                        @if($tugas->file_tugas && ! $soalMedia)
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 rounded-xl border border-indigo-100 bg-indigo-50/50 p-3">
                                <span class="flex items-center gap-2 text-xs font-extrabold text-slate-700"><i class="fa-solid fa-paperclip text-indigo-500" aria-hidden="true"></i>File soal</span>
                                <x-file-preview :path="$tugas->file_tugas" :title="$tugas->judul_tugas" label="Lihat soal" />
                            </div>
                        @endif
                    </div>
                    @if($soalMedia)
                        <div class="min-w-0 max-w-sm">
                            <p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">File soal</p>
                            <x-file-preview :path="$tugas->file_tugas" :title="$tugas->judul_tugas" label="Lihat soal" fill />
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-pen text-indigo-600" aria-hidden="true"></i>Jawaban siswa</h2>
                @if($adaJawaban)
                    <div class="mt-3 grid items-start gap-4">
                        @if($tugasSiswa->jawaban_text || ($tugasSiswa->file_jawaban && ! $jawabanMedia))
                            <div class="flex min-w-0 flex-col gap-3">
                                @if($tugasSiswa->jawaban_text)
                                    <div class="flex flex-col">
                                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Jawaban teks</p>
                                        <div class="mt-1 whitespace-pre-line break-words rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-800">{{ $tugasSiswa->jawaban_text }}</div>
                                    </div>
                                @endif
                                @if($tugasSiswa->file_jawaban && ! $jawabanMedia)
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2 rounded-xl border border-indigo-100 bg-indigo-50/50 p-3">
                                        <span class="flex items-center gap-2 text-xs font-extrabold text-slate-700"><i class="fa-solid fa-paperclip text-indigo-500" aria-hidden="true"></i>File jawaban</span>
                                        <x-file-preview :path="$tugasSiswa->file_jawaban" title="Jawaban siswa" label="Lihat jawaban siswa" />
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if($jawabanMedia)
                            <div class="min-w-0 max-w-sm">
                                <p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">File jawaban</p>
                                <x-file-preview :path="$tugasSiswa->file_jawaban" title="Jawaban siswa" label="Lihat jawaban siswa" fill />
                            </div>
                        @endif
                    </div>
                @endif
                @unless($adaJawaban)
                    <div class="py-8 text-center text-sm text-slate-500"><i class="fa-solid fa-inbox mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>Tidak ada jawaban</div>
                @endunless
            </section>
        </div>

        <aside class="min-w-0 space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-user text-indigo-600" aria-hidden="true"></i>Info siswa</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Nama</dt><dd class="font-semibold text-slate-900">{{ $tugasSiswa->siswa->nama_lengkap }}</dd></div>
                    <div><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">NISN</dt><dd class="text-slate-800">{{ $tugasSiswa->siswa->nisn }}</dd></div>
                    <div><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Waktu kumpul</dt><dd class="text-slate-800">{{ $tugasSiswa->tanggal_submit ? $tugasSiswa->tanggal_submit->format('d M Y H:i') : '-' }} @if($tugasSiswa->isLate())<span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Terlambat</span>@endif</dd></div>
                    <div><dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Status</dt><dd>
                        @if($tugasSiswa->status == 'dikerjakan')<span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-extrabold text-amber-700">Perlu dinilai</span>
                        @elseif($tugasSiswa->status == 'dinilai')<span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-extrabold text-emerald-700">Sudah dinilai</span>@endif
                    </dd></div>
                </dl>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
                    <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-star text-amber-500" aria-hidden="true"></i>Berikan nilai</h2>
                    @if($adaJawaban)
                        <button type="button" @click="analisis()" :disabled="memproses" class="inline-flex min-h-9 items-center gap-1.5 whitespace-nowrap rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 px-3 text-xs font-bold text-white shadow-sm hover:from-indigo-700 hover:to-violet-700 disabled:cursor-wait disabled:opacity-80">
                            <i class="fa-solid" :class="memproses ? 'fa-spinner fa-spin' : 'fa-robot'" aria-hidden="true"></i>
                            <span x-text="memproses ? `Menganalisis ${detik}s` : 'Analisis AI'">Analisis AI</span>
                        </button>
                    @endif
                </header>
                @if($adaJawaban)
                    <p class="border-b border-slate-100 bg-slate-50 px-4 py-2 text-[11px] text-slate-500 sm:px-5"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>AI dapat menganalisis gambar (JPG/PNG), PDF (digital &amp; scan), dan teks.</p>
                @endif
                <p x-cloak x-show="pesan" x-text="pesan" role="status" class="mx-4 mt-3 rounded-xl px-3 py-2 text-xs font-semibold sm:mx-5" :class="{ 'bg-indigo-50 text-indigo-800': status === 'proses', 'bg-emerald-50 text-emerald-800': status === 'ok', 'bg-rose-50 text-rose-800': status === 'gagal' }"></p>

                <form action="{{ route('guru.lms.tugas.koreksi.store', [$kelas->id, $mapel->id, $tugas->id, $tugasSiswa->id]) }}" method="POST" class="space-y-4 p-4 sm:p-5">
                    @csrf
                    <label class="block text-xs font-bold text-slate-700">Nilai (0-100) <span class="text-rose-600">*</span>
                        <input type="number" name="nilai" x-ref="nilai" value="{{ old('nilai', $tugasSiswa->nilai) }}" min="0" max="100" required class="{{ $input }}" :class="sorot && '!border-emerald-400 !bg-emerald-50'">
                        @error('nilai')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-xs font-bold text-slate-700">Feedback untuk siswa
                        <textarea name="feedback_guru" x-ref="feedback" rows="5" class="{{ $input }}" :class="sorot && '!border-sky-400 !bg-sky-50'">{{ old('feedback_guru', $tugasSiswa->feedback_guru) }}</textarea>
                        <span class="mt-1 block font-normal text-slate-500">Berikan komentar atau saran untuk siswa.</span>
                    </label>
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan nilai</button>
                    @if($tugasSiswa->status == 'dinilai')
                        <p class="flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800"><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Tugas sudah dinilai</p>
                    @endif
                </form>
            </section>
        </aside>
    </div>
</div>
@endsection
