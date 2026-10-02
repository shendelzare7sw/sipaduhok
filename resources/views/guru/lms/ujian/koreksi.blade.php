@extends('layouts.lms-guru')

@php
    $isLatihan = request()->routeIs('guru.lms.latihan.*');
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $aiUrlTemplate = route($prefix.'.koreksi.ai-suggest', [$kelas->id, $mapel->id, $ujian->id, 'SOAL_ID_PLACEHOLDER']);
    $statusMap = [
        'selesai' => ['Selesai', 'bg-emerald-50 text-emerald-700'],
        'dinilai' => ['Sudah dinilai', 'bg-indigo-50 text-indigo-700'],
    ];
    [$statusLabel, $statusTone] = $statusMap[$ujianSiswa->status] ?? [ucfirst((string) $ujianSiswa->status), 'bg-slate-100 text-slate-600'];
    $nilaiInput = 'block h-11 w-full rounded-xl border px-3 text-base font-extrabold tabular-nums text-slate-900 transition focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

@section('title', 'Koreksi Jawaban Siswa')
@section('page-title', 'Koreksi Jawaban: ' . ($ujianSiswa->siswa->nama_lengkap ?? '-'))
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route($prefix.'.hasil', [$kelas->id, $mapel->id, $ujian->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke hasil</a>

    <section class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 sm:p-5 xl:grid-cols-4">
        <div class="min-w-0"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Nama siswa</p><p class="mt-0.5 truncate font-extrabold text-slate-900">{{ $ujianSiswa->siswa->nama_lengkap }}</p></div>
        <div class="min-w-0"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Judul {{ strtolower($tipeLabel) }}</p><p class="mt-0.5 truncate font-extrabold text-slate-900">{{ $ujian->judul_ujian }}</p></div>
        <div><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Status</p><span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-extrabold {{ $statusTone }}">{{ $statusLabel }}</span></div>
        <div><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Nilai saat ini</p><p class="mt-0.5 text-xl font-extrabold tabular-nums text-indigo-700">{{ number_format($ujianSiswa->nilai ?? 0, 1) }}/100</p></div>
    </section>

    <form action="{{ route($prefix.'.koreksi.store', [$kelas->id, $mapel->id, $ujian->id, $ujianSiswa->id]) }}" method="POST" class="space-y-4">
        @csrf

        @foreach($soalList as $index => $soal)
            @php
                $jawaban = $ujianSiswa->jawabanSiswa->where('soal_ujian_id', $soal->id)->first();
                $isAutoGraded = ! in_array($soal->tipe_soal, ['uraian', 'essay', 'isian_singkat']);
                $perluNilai = ! $isAutoGraded && $jawaban && $jawaban->nilai_soal === null;
            @endphp
            <article class="min-w-0 rounded-2xl border bg-white p-4 shadow-sm sm:p-5 {{ $isAutoGraded ? 'border-slate-200' : ($perluNilai ? 'border-amber-300 bg-amber-50/40' : 'border-amber-200') }}"
                     @unless($isAutoGraded)
                     x-data="{
                        memproses: false, detik: 0, pesan: '', status: '', sorot: false,
                        async analisis(el) {
                            if (this.memproses) return;
                            const jawaban = el.dataset.answer;
                            if (! jawaban || jawaban === '-') { this.status = 'gagal'; this.pesan = 'Belum ada jawaban siswa untuk dianalisis.'; return; }
                            this.memproses = true; this.detik = 0; this.status = 'proses'; this.pesan = 'Sedang menganalisis jawaban...';
                            const timer = setInterval(() => this.detik++, 1000);
                            try {
                                const res = await fetch(el.dataset.url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, Accept: 'application/json' }, body: JSON.stringify({ answer: jawaban }) });
                                const data = await res.json();
                                if (data.error) throw new Error(data.feedback || 'Terjadi kesalahan pada AI.');
                                this.$refs.nilai.value = data.score;
                                const lama = this.$refs.feedback.value.trim();
                                this.$refs.feedback.value = `[AI Suggestion] ${data.feedback}` + (lama ? `\n\n${lama}` : '');
                                this.sorot = true; setTimeout(() => this.sorot = false, 2000);
                                this.status = 'ok'; this.pesan = `Selesai dalam ${this.detik} detik. Saran skor: ${data.score}. Tinjau sebelum menyimpan.`;
                            } catch (e) {
                                this.status = 'gagal'; this.pesan = `Gagal (${this.detik} detik): ${e.message}`;
                            } finally { clearInterval(timer); this.memproses = false; }
                        },
                     }"
                     @endunless>
                <header class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="flex flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">
                        Soal no. {{ $index + 1 }}
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
                        @unless($isAutoGraded)<span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-bold text-amber-800">Koreksi manual</span>@endunless
                    </h2>
                    <span class="rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-bold text-sky-700">Bobot {{ $soal->bobot_nilai }}</span>
                </header>

                @if($soal->narasi)
                    <div class="mt-3 rounded-xl border-l-4 border-indigo-300 bg-indigo-50 p-3 text-sm italic leading-6 text-slate-700"><strong class="mb-1 block not-italic text-indigo-800"><i class="fa-solid fa-book-open mr-1.5" aria-hidden="true"></i>Narasi / konteks</strong>{!! nl2br(e($soal->narasi)) !!}</div>
                @endif

                <div class="mt-3 rounded-xl border border-slate-200 bg-white p-3 text-sm leading-7 text-slate-800">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                <p class="mt-3 text-[11px] font-bold uppercase tracking-wide text-slate-500">Jawaban siswa</p>
                <div class="mt-1 min-h-12 break-words rounded-xl bg-slate-50 p-3 text-sm leading-7 text-slate-800">
                    @if($isAutoGraded)
                        @if($soal->tipe_soal == 'pilihan_ganda')
                            @if(isset($jawaban))
                                <strong>{{ $jawaban->jawaban ?? '-' }}</strong>
                                @if($soal->checkAnswer($jawaban->jawaban))
                                    <i class="fa-solid fa-circle-check ml-2 text-emerald-600" aria-label="Benar"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark ml-2 text-rose-600" aria-label="Salah"></i> <span class="text-slate-500">(Kunci: {{ $soal->jawaban_benar }})</span>
                                @endif
                            @else
                                <span class="italic text-slate-400">(Tidak dijawab)</span>
                            @endif
                        @elseif($soal->tipe_soal == 'pilihan_ganda_kompleks')
                            @php $ansArray = isset($jawaban->jawaban) && $jawaban->jawaban ? (is_array($jawaban->jawaban) ? $jawaban->jawaban : (json_decode($jawaban->jawaban, true) ?? [])) : []; @endphp
                            @if(empty($ansArray))<span class="italic text-slate-400">(Tidak dijawab)</span>@else<strong>{{ implode(', ', $ansArray) }}</strong>@endif
                        @else
                            @php $decoded = isset($jawaban->jawaban) ? (is_array($jawaban->jawaban) ? $jawaban->jawaban : json_decode($jawaban->jawaban, true)) : null; @endphp
                            {{ is_array($decoded) ? implode(', ', $decoded) : ($jawaban->jawaban ?? '-') }}
                        @endif
                    @else
                        @if(isset($jawaban->jawaban) && $jawaban->jawaban)
                            {!! nl2br(e($jawaban->jawaban)) !!}
                        @else
                            <span class="italic text-slate-400">Siswa tidak menjawab soal ini.</span>
                        @endif
                    @endif
                </div>

                @if($isAutoGraded)
                    <label class="mt-3 block text-xs font-bold text-slate-700 sm:max-w-[12rem]">Nilai otomatis (bisa diubah)
                        <input type="number" step="any" min="0" max="{{ $soal->bobot_nilai }}" name="nilai[{{ $soal->id }}]" value="{{ $jawaban->nilai_soal ?? 0 }}" class="mt-1 {{ $nilaiInput }} border-indigo-300 focus:border-indigo-500">
                    </label>
                @else
                    <div class="mt-3 grid gap-3 md:grid-cols-[12rem_minmax(0,1fr)]">
                        <label class="block text-xs font-bold text-indigo-700">Nilai (maks. {{ $soal->bobot_nilai }})
                            <input type="number" step="any" min="0" max="{{ $soal->bobot_nilai }}" name="nilai[{{ $soal->id }}]" x-ref="nilai" value="{{ $jawaban->nilai_soal ?? 0 }}" required class="mt-1 {{ $nilaiInput }} border-slate-300 focus:border-indigo-500" :class="sorot && '!border-emerald-400 !bg-emerald-50'">
                        </label>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-end justify-between gap-2">
                                <label for="feedback_{{ $soal->id }}" class="text-xs font-bold text-slate-700">Feedback / komentar guru (opsional)</label>
                                <button type="button" data-url="{{ str_replace('SOAL_ID_PLACEHOLDER', $soal->id, $aiUrlTemplate) }}" data-answer="{{ $jawaban->jawaban ?? '' }}" @click="analisis($el)" :disabled="memproses" class="inline-flex min-h-9 items-center gap-1.5 whitespace-nowrap rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 px-3 text-xs font-bold text-white shadow-sm hover:from-indigo-700 hover:to-violet-700 disabled:cursor-wait disabled:opacity-80">
                                    <i class="fa-solid" :class="memproses ? 'fa-spinner fa-spin' : 'fa-robot'" aria-hidden="true"></i>
                                    <span x-text="memproses ? `Menganalisis ${detik}s` : 'Analisis AI Assistant'">Analisis AI Assistant</span>
                                </button>
                            </div>
                            <textarea id="feedback_{{ $soal->id }}" name="feedback[{{ $soal->id }}]" x-ref="feedback" rows="3" placeholder="Berikan catatan koreksi..." class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" :class="sorot && '!border-sky-400 !bg-sky-50'">{{ $jawaban->feedback ?? '' }}</textarea>
                            <p x-cloak x-show="pesan" x-text="pesan" role="status" class="mt-2 rounded-lg px-3 py-2 text-xs font-semibold" :class="{ 'bg-indigo-50 text-indigo-800': status === 'proses', 'bg-emerald-50 text-emerald-800': status === 'ok', 'bg-rose-50 text-rose-800': status === 'gagal' }"></p>
                        </div>
                    </div>
                @endif
            </article>
        @endforeach

        <div class="sticky bottom-4 z-20 flex flex-col gap-3 rounded-2xl border border-indigo-200 bg-white/95 p-4 shadow-xl backdrop-blur sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">Pastikan semua soal uraian/esai telah dinilai sebelum menyimpan.</p>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan hasil koreksi</button>
        </div>
    </form>
</div>
@endsection
