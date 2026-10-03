@php
    $layout = ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan') ? 'layouts.lms-latihan' : 'layouts.lms';
@endphp

@extends($layout)

@section('title', $ujian->judul_ujian)

{{-- Section for Standard Layout (Start/Result screens) --}}
@if(!isset($ujianSiswa) || $ujianSiswa->status !== 'sedang_mengerjakan')
    @section('page-title', $mataPelajaran->nama_mapel)
    @section('page-subtitle', ucwords(str_replace('_', ' ', $ujian->tipe_ujian)))
    @section('sidebar-menu')
        @include('siswa.partials.sidebar-lms')
    @endsection
@endif

@section('content')

@if(!$ujianSiswa || $ujianSiswa->status !== 'sedang_mengerjakan')
    @include('siswa.lms.mata-pelajaran.ujian.partials.start-result', ['isLatihan' => true, 'routePrefix' => 'siswa.lms.mapel.latihan.'])
@else
    {{-- MODE LEMBAR KERJA LATIHAN (semua soal satu halaman). Ekuivalen-piksel dengan tampilan Bootstrap lama. --}}
    @php
        $opsi = 'mb-[10px] flex cursor-pointer items-start rounded-[6px] border border-[#e5e7eb] bg-white px-3 py-2.5 transition hover:border-[#d1d5db] hover:bg-[#f9fafb] min-[576px]:px-[15px] min-[576px]:py-3';
        $kontrol = 'mr-3 mt-1 shrink-0 scale-[1.2]';
    @endphp
    <div x-data="latihanWork"
        data-duration-minutes="{{ $ujian->durasi_menit ?? 0 }}"
        data-start-time="{{ $ujianSiswa->waktu_mulai }}"
        data-autosave-url="{{ route('siswa.lms.mapel.latihan.autosave', [$mataPelajaran->id, $ujian->id]) }}"
        data-csrf-token="{{ csrf_token() }}"
        class="min-h-screen bg-[#f3f4f6]">
    <form x-ref="examForm" action="{{ route('siswa.lms.mapel.latihan.submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" id="examForm">
        @csrf

        {{-- Header lengket --}}
        <div class="sticky top-[60px] z-[100] flex items-center justify-between gap-2 border-b border-[#e5e7eb] bg-white px-3 py-2.5 shadow-[0_2px_4px_rgba(0,0,0,0.05)] min-[576px]:gap-3 min-[576px]:px-5 min-[576px]:py-[15px]">
            <div class="min-w-0 flex-1">
                <h5 class="mb-0 truncate text-[0.85rem]/[1.2] font-bold min-[576px]:text-xl/[1.2]">{{ $ujian->judul_ujian }}</h5>
                <small class="hidden text-[0.875em] uppercase text-[rgba(33,37,41,0.75)] min-[576px]:inline">{{ str_replace('_', ' ', $ujian->tipe_ujian) }}</small>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <i class="fas fa-clock hidden text-[#6c757d] min-[576px]:inline"></i>
                <div data-exam-timer x-text="timerText" class="whitespace-nowrap rounded-[4px] border border-[#dee2e6] bg-white px-2 py-1 font-['Courier_New',monospace] text-[0.95rem] font-bold leading-[1.5] text-[#dc3545] min-[576px]:px-3 min-[576px]:py-[5px] min-[576px]:text-[1.2rem]">00:00:00</div>
                <button type="button" data-finish-exam x-on:click="finish()" class="rounded-[6px] border border-[#0d6efd] bg-[#0d6efd] px-2.5 py-[5px] text-[12px] font-bold leading-[1.5] text-white transition hover:border-[#0a58ca] hover:bg-[#0b5ed7] min-[576px]:px-3 min-[576px]:py-1.5 min-[576px]:text-base">
                    <i class="fas fa-paper-plane mr-1"></i><span class="hidden min-[576px]:inline"> SELESAI</span>
                </button>
            </div>
        </div>

        <div class="mx-auto w-full max-w-[800px] px-3 py-6">
            @if($soalList->count() > 0)
                @foreach($soalList as $index => $soal)
                    <div class="mb-[14px] rounded-[8px] border border-[#e5e7eb] bg-white p-[15px] shadow-[0_1px_3px_rgba(0,0,0,0.05)] min-[576px]:mb-5 min-[576px]:p-[25px]">
                        <div class="flex items-start gap-4">
                            <div class="mb-[15px] flex h-[35px] w-[35px] shrink-0 items-center justify-center rounded-full bg-[#165fac] font-bold text-white">{{ $index + 1 }}</div>
                            <div class="w-full min-w-0">
                                @if($soal->narasi)
                                    <div class="mb-5 rounded-[4px] border-l-4 border-[#0ea5e9] bg-[#f0f9ff] p-[15px] text-[0.95rem]">
                                        <div class="mb-1 font-bold text-[#0d6efd]"><i class="fas fa-book-open mr-1"></i> Bacaan</div>
                                        {!! nl2br(e($soal->narasi)) !!}
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

                                <div class="mb-5 text-[0.95rem] leading-[1.6] text-[#374151] min-[576px]:text-[1.05rem]">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                                <div>
                                    @if($soal->tipe_soal === 'pilihan_ganda')
                                        @php $pilihan = $soal->pilihanJawabanForSiswa(); @endphp
                                        @if(is_array($pilihan))
                                            @foreach($pilihan as $key => $value)
                                                <label class="{{ $opsi }}">
                                                    <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" data-autosave-answer data-soal-id="{{ $soal->id }}"
                                                        x-on:change="save({{ $soal->id }}, $el.value)" class="{{ $kontrol }}"
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
                                                            x-on:change="answerKompleks({{ $soal->id }})" class="{{ $kontrol }}"
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
                                                                x-on:change="answerBenarSalah({{ $soal->id }}, {{ count($pernyataanList) }})"
                                                                class="mr-2 mt-1 shrink-0 scale-[1.2]" {{ $cek ? 'checked' : '' }}>
                                                            <span><strong>{{ $labelBs }}</strong></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach

                                    @else
                                        <textarea name="jawaban[{{ $soal->id }}]" rows="4" placeholder="Tulis jawaban Anda disini..." data-autosave-answer data-soal-id="{{ $soal->id }}"
                                            x-on:input="save({{ $soal->id }}, $el.value)"
                                            class="block w-full rounded-[6px] border border-[#dee2e6] bg-white px-3 py-1.5 text-base leading-6 text-[#212529] placeholder:text-[rgba(33,37,41,0.75)] focus:border-[#86b7fe] focus:outline-none focus:ring-4 focus:ring-[rgba(13,110,253,0.25)]">{{ $existingAnswers[$soal->id] ?? '' }}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-12 text-center">
                    <i class="fas fa-search mb-4 text-5xl text-[rgba(33,37,41,0.75)]"></i>
                    <h5 class="text-xl font-medium leading-[1.2]">Belum ada soal untuk latihan ini.</h5>
                </div>
            @endif

            <div class="mb-12 mt-6 text-center">
                <button type="button" data-finish-exam x-on:click="finish()" class="rounded-[8px] border border-[#0d6efd] bg-[#0d6efd] px-12 py-2 text-xl leading-[1.5] text-white shadow-[0_0.5rem_1rem_rgba(0,0,0,0.15)] transition hover:border-[#0a58ca] hover:bg-[#0b5ed7]">
                    <i class="fas fa-check-circle mr-2"></i> KIRIM JAWABAN
                </button>
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
