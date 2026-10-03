@php
    $layout = ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan') ? 'layouts.lms-ujian' : 'layouts.lms';
    $isLatihan = request()->routeIs('siswa.lms.mapel.latihan.*') || (isset($ujian) && $ujian->tipe_ujian === 'latihan');
    $routePrefix = $isLatihan ? 'siswa.lms.mapel.latihan.' : 'siswa.lms.mapel.ujian.';
    $existingAnswers = $existingAnswers ?? [];
    $soalList = $soalList ?? collect();
    $questionMeta = $soalList->values()->map(fn ($soal, $index) => [
        'soal_id' => $soal->id,
        'nomor_soal' => $index + 1,
    ])->all();
    $answersState = $soalList->map(function ($soal) use ($existingAnswers) {
        if (!isset($existingAnswers[$soal->id])) {
            return false;
        }

        $answer = $existingAnswers[$soal->id];

        if (!is_string($answer)) {
            return false;
        }

        $decoded = json_decode($answer, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            if ($soal->tipe_soal === 'pilihan_ganda_kompleks') {
                return count($decoded) > 0;
            }

            if ($soal->tipe_soal === 'benar_salah') {
                return count($decoded) > 0 && !in_array(null, $decoded, true);
            }

            return count($decoded) > 0;
        }

        return trim($answer) !== '' && trim($answer) !== '-';
    })->values()->all();
    $encodedQuestionMeta = base64_encode(json_encode($questionMeta));
    $encodedAnswersState = base64_encode(json_encode($answersState));
@endphp

@extends($layout)

@section('title', $ujian->judul_ujian)

{{-- Section for Standard Layout (Start/Result screens) --}}
@if(!isset($ujianSiswa) || $ujianSiswa->status !== 'sedang_mengerjakan')
    @section('page-title', $mataPelajaran->nama_mapel)
    @section('page-subtitle', 'Ujian ' . ucwords(str_replace('_', ' ', $ujian->tipe_ujian)))
    @section('sidebar-menu')
        @include('siswa.partials.sidebar-lms')
    @endsection
@endif

@section('content')

@if(!$ujianSiswa || $ujianSiswa->status !== 'sedang_mengerjakan')
    @include('siswa.lms.mata-pelajaran.ujian.partials.start-result', ['isLatihan' => $isLatihan, 'routePrefix' => $routePrefix])
@else
    {{-- MODE FOKUS UJIAN (CBT satu soal per layar). Ekuivalen-piksel dengan tampilan Bootstrap lama. --}}
    @php
        $btnNav = 'inline-block grow text-center whitespace-nowrap rounded-[6px] border border-[#0d6efd] bg-[#0d6efd] px-2 py-1.5 text-[11px] leading-[1.5] text-white transition hover:border-[#0a58ca] hover:bg-[#0b5ed7] disabled:pointer-events-none disabled:opacity-65 min-[576px]:min-w-[140px] min-[576px]:px-3 min-[576px]:py-2 min-[576px]:text-[0.85rem]';
        $opsi = 'mb-[10px] flex cursor-pointer items-start rounded-[4px] border border-[#dee2e6] bg-white px-[15px] py-3 hover:bg-[#f8f9fa]';
        $timerBox = 'rounded-[4px] border border-[#dee2e6] bg-[#f8f9fa] text-center';
        $timerText = "font-['Courier_New',monospace] text-xl font-bold leading-[1.5] text-[#dc3545]";
    @endphp
    <div x-data="ujianWork"
        data-exam-type="{{ $ujian->tipe_ujian }}"
        data-total-questions="{{ $soalList->count() }}"
        data-duration-minutes="{{ $ujian->durasi_menit ?? 0 }}"
        data-start-time="{{ $ujianSiswa->waktu_mulai->toIso8601String() }}"
        data-autosave-url="{{ route($routePrefix . 'autosave', [$mataPelajaran->id, $ujian->id]) }}"
        data-monitoring-url="{{ $isLatihan ? '' : route('siswa.lms.mapel.ujian.monitoring', [$mataPelajaran->id, $ujian->id]) }}"
        data-csrf-token="{{ csrf_token() }}"
        data-storage-key="doubtState_{{ $ujianSiswa->id }}"
        data-question-meta="{{ $encodedQuestionMeta }}"
        data-answers-state="{{ $encodedAnswersState }}"
        class="min-h-screen bg-[#e9ecef]">
    <form x-ref="examForm" action="{{ route($routePrefix . 'submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" id="examForm">
        @csrf

        <div class="grid grid-cols-1 gap-y-4 min-[992px]:grid-cols-[3fr_1fr]">
            {{-- Kiri: area soal --}}
            <div class="min-w-0 px-2">
                <div class="mb-4 flex items-center justify-between">
                    <div class="mb-5 inline-block rounded-[4px] bg-[#f8f9fa] px-5 py-3">
                        <strong>SOAL NO. <span class="inline-block rounded-[6px] bg-[#0d6efd] px-[0.65em] py-[0.35em] text-[0.75em] font-bold leading-none text-white" x-text="current + 1">1</span></strong>
                    </div>

                    <div class="min-[992px]:hidden">
                        <div class="{{ $timerBox }} mb-[15px] inline-block px-4 py-2">
                            <small class="block text-[0.75rem] leading-[18px] text-[rgba(33,37,41,0.75)]">SISA WAKTU</small>
                            <span data-exam-timer class="{{ $timerText }}" x-text="timerText">00:00:00</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-[4px] bg-white p-4 shadow-[0_1px_3px_rgba(0,0,0,0.1)] min-[576px]:min-h-[400px] min-[576px]:p-[25px]">
                    @if($soalList->count() > 0)
                        @foreach($soalList as $index => $soal)
                            <div id="q-item-{{ $index }}" x-show="current === {{ $index }}" @if($index > 0) x-cloak @endif>
                                @if($soal->narasi)
                                    <div class="mb-4 rounded-[4px] border-l-4 border-[#165fac] bg-[#f0f7ff] p-[15px]">
                                        <small class="mb-1 block text-sm/[1.5] font-bold text-[rgba(33,37,41,0.75)]"><i class="fas fa-book-open mr-1"></i> Bacaan</small>
                                        <div class="text-[0.95rem] leading-[1.7] text-[#333]">{!! nl2br(e($soal->narasi)) !!}</div>
                                    </div>
                                @endif

                                @if($soal->image_path)
                                    <div class="mb-4">
                                        <div class="rounded-[6px] bg-white shadow-[0_0.125rem_0.25rem_rgba(0,0,0,0.075)]">
                                            <div class="p-2 text-center">
                                                <img src="{{ asset('storage/' . $soal->image_path) }}" alt="Gambar Soal {{ $index + 1 }}"
                                                     data-question-image x-on:click="zoom($el.src)"
                                                     class="h-auto max-h-[250px] max-w-full cursor-pointer rounded-[6px]">
                                                <small class="mt-2 block text-sm/[1.5] text-[rgba(33,37,41,0.75)]"><i class="fas fa-search-plus mr-1"></i> Klik gambar untuk memperbesar</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-5 text-base leading-[1.6] text-[#212529]">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                                <div>
                                    @if($soal->tipe_soal === 'pilihan_ganda')
                                        @php $pilihan = $soal->pilihanJawabanForSiswa(); @endphp
                                        @if(is_array($pilihan))
                                            @foreach($pilihan as $key => $value)
                                                <label class="{{ $opsi }}">
                                                    <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" data-answer-choice
                                                        x-on:change="setAnswer({{ $index }}, {{ $soal->id }}, $el.value)"
                                                        class="mr-[10px] mt-[3px] h-[18px] w-[18px] shrink-0"
                                                        {{ isset($existingAnswers[$soal->id]) && $existingAnswers[$soal->id] == $key ? 'checked' : '' }}>
                                                    <span><strong>{{ $key }}.</strong> {{ $value }}</span>
                                                </label>
                                            @endforeach
                                        @endif

                                    @elseif($soal->tipe_soal === 'pilihan_ganda_kompleks')
                                        @php
                                            $pilihan = $soal->pilihanJawabanForSiswa();
                                            $ansRaw = $existingAnswers[$soal->id] ?? '';
                                            $checkedKompleks = json_decode($ansRaw, true);
                                            if (!is_array($checkedKompleks)) {
                                                $checkedKompleks = $ansRaw ? explode(',', $ansRaw) : [];
                                            }
                                        @endphp
                                        <small class="mb-2 block text-sm/[1.5] text-[rgba(33,37,41,0.75)]"><i class="fas fa-info-circle mr-1"></i>Pilih semua jawaban yang benar</small>
                                        <input type="hidden" name="jawaban[{{ $soal->id }}]" id="kompleks-hidden-{{ $soal->id }}" value="{{ $existingAnswers[$soal->id] ?? '' }}">
                                        @if(is_array($pilihan))
                                            @foreach($pilihan as $key => $value)
                                                @if($key !== 'jawaban_benar')
                                                    <label class="{{ $opsi }}">
                                                        <input type="checkbox" data-kompleks data-soal-id="{{ $soal->id }}" value="{{ $key }}"
                                                            x-on:change="answerKompleks({{ $index }}, {{ $soal->id }})"
                                                            class="mr-[10px] mt-[3px] h-[18px] w-[18px] shrink-0"
                                                            {{ in_array($key, $checkedKompleks) ? 'checked' : '' }}>
                                                        <span><strong>{{ $key }}.</strong> {{ $value }}</span>
                                                    </label>
                                                @endif
                                            @endforeach
                                        @endif

                                    @elseif($soal->tipe_soal === 'benar_salah')
                                        @php
                                            $pilihanData = $soal->pilihanJawabanForSiswa();
                                            $pernyataanList = $pilihanData['pernyataan'] ?? [];
                                            $checkedBS = isset($existingAnswers[$soal->id]) ? json_decode($existingAnswers[$soal->id], true) : [];
                                        @endphp
                                        <input type="hidden" name="jawaban[{{ $soal->id }}]" id="bs-hidden-{{ $soal->id }}" value="{{ $existingAnswers[$soal->id] ?? '' }}">
                                        @foreach($pernyataanList as $pIdx => $item)
                                            <div class="mb-4 rounded-[6px] border border-[#dee2e6] bg-[#f8f9fa] p-4">
                                                <p class="mb-2 font-bold">{{ $item['text'] ?? $item['pernyataan'] ?? '' }}</p>
                                                <div class="flex gap-4">
                                                    @foreach(['true' => 'BENAR', 'false' => 'SALAH'] as $nilaiBs => $labelBs)
                                                        @php
                                                            $cek = isset($checkedBS[$pIdx]) && ($nilaiBs === 'true'
                                                                ? ($checkedBS[$pIdx] === true || $checkedBS[$pIdx] === 'true' || $checkedBS[$pIdx] === 1)
                                                                : ($checkedBS[$pIdx] === false || $checkedBS[$pIdx] === 'false' || $checkedBS[$pIdx] === 0));
                                                        @endphp
                                                        <label class="{{ $opsi }} !mb-0 flex-1 justify-center text-center">
                                                            <input type="radio" name="bs_{{ $soal->id }}_{{ $pIdx }}" value="{{ $nilaiBs }}" data-benar-salah-answer data-soal-id="{{ $soal->id }}"
                                                                x-on:change="answerBenarSalah({{ $index }}, {{ $soal->id }}, {{ count($pernyataanList) }})"
                                                                class="mr-2 mt-[3px] h-[18px] w-[18px] shrink-0" {{ $cek ? 'checked' : '' }}>
                                                            <span><strong>{{ $labelBs }}</strong></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach

                                    @else
                                        <textarea name="jawaban[{{ $soal->id }}]" rows="6" placeholder="Tulis jawaban Anda..." data-answer-text
                                            x-on:input="setAnswer({{ $index }}, {{ $soal->id }}, $el.value)"
                                            class="block w-full rounded-[6px] border border-[#dee2e6] bg-white px-3 py-1.5 text-base leading-6 text-[#212529] placeholder:text-[rgba(33,37,41,0.75)] focus:border-[#86b7fe] focus:outline-none focus:ring-4 focus:ring-[rgba(13,110,253,0.25)]">{{ $existingAnswers[$soal->id] ?? '' }}</textarea>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="py-12 text-center">
                            <i class="fas fa-exclamation-triangle mb-4 text-5xl text-[#ffc107]"></i>
                            <h5 class="text-xl font-medium leading-[1.2] text-[rgba(33,37,41,0.75)]">Soal tidak ditemukan!</h5>
                            <p class="mt-2 text-sm/[1.5] text-[rgba(33,37,41,0.75)]">
                                ID Ujian: {{ $ujian->id }}<br>
                                Mata Pelajaran: {{ $mataPelajaran->nama_mapel ?? 'N/A' }}<br>
                                Jumlah Soal: {{ $soalList->count() ?? 0 }}
                            </p>
                            <div class="mt-6">
                                <p class="mb-4 text-[rgba(33,37,41,0.75)]">Anda sedang dalam sesi ujian tanpa ada soal. Pilih aksi di bawah:</p>
                                <button type="button" data-end-empty-exam x-on:click="akhiriTanpaSoal()"
                                    class="inline-flex items-center gap-1 rounded-[6px] border border-[#dc3545] bg-[#dc3545] px-3 py-1.5 text-white hover:bg-[#bb2d3b]">
                                    <i class="fas fa-times-circle"></i> Akhiri Ujian Sekarang
                                </button>
                                <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="ml-2 inline-flex items-center gap-1 rounded-[6px] border border-[#6c757d] bg-[#6c757d] px-3 py-1.5 text-white no-underline hover:bg-[#5c636a]">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Mata Pelajaran
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Navigasi soal --}}
                <div class="mt-4 flex flex-nowrap items-center justify-between gap-2">
                    <button type="button" id="btn-prev" data-prev-question x-on:click="prev()" x-bind:disabled="current === 0" class="{{ $btnNav }}">
                        <i class="fas fa-chevron-left mr-1"></i> <span class="hidden min-[576px]:inline">SOAL </span>SEBELUMNYA
                    </button>

                    <label id="label-ragu" x-bind:class="doubts[current] && 'brightness-90 saturate-[1.2] shadow-[inset_0_2px_4px_rgba(0,0,0,0.2)]'"
                        class="m-0 flex grow cursor-pointer items-center justify-center whitespace-nowrap rounded-[6px] border border-[#ffc107] bg-[#ffc107] px-2 py-1.5 text-[11px] leading-[1.5] text-black min-[576px]:px-3 min-[576px]:py-2 min-[576px]:text-[0.85rem]">
                        <input type="checkbox" id="cb-ragu" data-toggle-doubt x-bind:checked="!!doubts[current]" x-on:change="toggleDoubt($el.checked)" class="mr-1.5 scale-110">
                        <span class="font-bold"><i class="fas fa-flag mr-1"></i> RAGU-RAGU</span>
                    </label>

                    <button type="button" id="btn-next" data-next-question x-on:click="next()" x-bind:disabled="current === total - 1" class="{{ $btnNav }}">
                        <span class="hidden min-[576px]:inline">SOAL </span>SELANJUTNYA <i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>

            {{-- Kanan: panel nomor soal --}}
            <div class="min-w-0 px-2">
                <div class="rounded-[4px] bg-white p-3 shadow-[0_1px_3px_rgba(0,0,0,0.1)] min-[576px]:p-5">
                    <div class="{{ $timerBox }} mb-[15px] p-[10px]">
                        <small class="mb-1 block text-[0.75rem] leading-[18px] text-[rgba(33,37,41,0.75)]">SISA WAKTU</small>
                        <div data-exam-timer class="{{ $timerText }}" x-text="timerText">00:00:00</div>
                    </div>

                    <h6 class="mb-2 text-sm/[1.2] font-bold">NOMOR SOAL</h6>

                    <div class="mb-4 grid grid-cols-5 gap-1 min-[576px]:gap-[6px] min-[992px]:grid-cols-7">
                        @foreach($soalList as $index => $soal)
                            <div id="nav-item-{{ $index }}" data-jump-question="{{ $index }}" x-on:click="go({{ $index }})" x-bind:class="navClass({{ $index }})"
                                class="flex aspect-square cursor-pointer items-center justify-center rounded-[4px] text-xs font-semibold text-white transition hover:opacity-80 min-[576px]:text-sm">
                                {{ $index + 1 }}
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4 text-sm/[1.5]">
                        @foreach([['bg-[#198754]', 'Hijau = Sudah dijawab'], ['bg-[#ffc107]', 'Orange = Ragu-ragu'], ['bg-[#6c757d]', 'Abu-abu = Belum dijawab']] as [$warnaLegend, $teksLegend])
                            <div class="mb-2 flex items-center gap-2">
                                <div class="h-5 w-5 rounded-[3px] {{ $warnaLegend }}"></div>
                                <span>{{ $teksLegend }}</span>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" data-finish-exam x-on:click="finish()" class="w-full rounded-[6px] border border-[#dc3545] bg-[#dc3545] px-3 py-1.5 text-base font-bold leading-6 text-white transition hover:border-[#b02a37] hover:bg-[#bb2d3b]">
                        SELESAIKAN UJIAN
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Pembesar gambar soal --}}
    <dialog x-ref="zoomDialog" x-on:click="$event.target === $el && $el.close()" class="m-auto max-w-[min(800px,95vw)] bg-transparent p-0 backdrop:bg-black/50">
        <div class="pt-2 text-center">
            <img x-bind:src="zoomSrc" alt="Gambar soal diperbesar" class="mx-auto h-auto max-h-[80vh] max-w-full rounded-[6px] shadow-[0_1rem_3rem_rgba(0,0,0,0.175)]">
        </div>
        <div class="flex justify-center p-3">
            <button type="button" x-on:click="$refs.zoomDialog.close()" class="rounded-full border border-[#6c757d] bg-[#6c757d] px-6 py-1 text-sm/[1.5] text-white hover:bg-[#5c636a]"><i class="fas fa-times mr-2"></i>Tutup Gambar</button>
        </div>
    </dialog>
    </div>
@endif

@endsection
