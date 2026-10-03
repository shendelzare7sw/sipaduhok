@extends('layouts.lms')

@php
    $isLatihan = $ujian->tipe_ujian === 'latihan';
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $routePrefix = $isLatihan ? 'latihan' : 'ujian';
    $soalList = $ujian->soalUjian;
    $jawabanMap = $ujianSiswa->jawabanSiswa->keyBy('soal_ujian_id');

    $totalSoal = $soalList->count();
    $totalBobot = 0;
    $poinDiperoleh = 0;
    $menungguKoreksi = 0;

    foreach($soalList as $s) {
        $totalBobot += $s->bobot_nilai;
        $j = $jawabanMap->get($s->id);
        if ($j) {
            if ($j->nilai_soal !== null) {
                $poinDiperoleh += (float)$j->nilai_soal;
            } elseif ($j->perluKoreksiManual()) {
                $menungguKoreksi++;
            }
        }
    }

    $nilaiDisplay = $ujianSiswa->nilai_terbaik ?? $ujianSiswa->nilai ?? 0;

    // Warna per status nilai soal: ok = penuh, pt = sebagian, no = salah, sk = kosong.
    $strip = ['ok' => 'bg-[#059669]', 'no' => 'bg-[#dc2626]', 'sk' => 'bg-[#64748b]', 'pt' => 'bg-[#d97706]'];
    $skor = ['ok' => 'bg-[#ecfdf5] text-[#059669]', 'no' => 'bg-[#fef2f2] text-[#dc2626]', 'pt' => 'bg-[#fffbeb] text-[#92400e]', 'sk' => 'bg-[#f8fafc] text-[#64748b]'];
    $opsiWarna = [
        'neu' => ['bg-[#fafafa] border-transparent', 'bg-[#f1f5f9] text-[#64748b]'],
        'sc' => ['bg-[#ecfdf5] border-[#059669]', 'bg-[#059669] text-white'],
        'sw' => ['bg-[#fef2f2] border-[#dc2626]', 'bg-[#dc2626] text-white'],
        'ik' => ['bg-[#f0fdf4] border-[#86efac]', 'bg-[#059669] text-white'],
    ];
    $tag = 'ml-1.5 whitespace-nowrap text-[0.7rem] font-bold';
@endphp

@section('title', 'Pembahasan ' . $tipeLabel)
@section('page-title', $mapel->nama_mapel)
@section('page-subtitle', 'Pembahasan ' . $tipeLabel)
@section('sidebar-menu') @include('siswa.partials.sidebar-lms') @endsection

@section('content')
<div class="text-[15px] leading-[1.5] text-slate-800">
    <div class="mb-7 rounded-xl bg-[#1a1a2e] px-4 py-5 text-white md:rounded-2xl md:px-8 md:py-7">
        <div class="flex flex-col items-start justify-between gap-2 md:flex-row md:items-center">
            <div>
                <h4 class="mb-0 text-[1.05rem]/[1.1] font-bold text-[#566a7f] md:text-xl/[1.1]"><i class="fas fa-clipboard-check mr-2 opacity-75"></i>Pembahasan {{ $tipeLabel }}</h4>
                <span class="text-[0.88rem] opacity-70">{{ $ujian->judul_ujian }} &bull; {{ $mapel->nama_mapel }}</span>
            </div>
            <span class="text-[0.88rem] opacity-70"><i class="far fa-calendar mr-1"></i>{{ $ujianSiswa->waktu_selesai ? $ujianSiswa->waktu_selesai->format('d M Y, H:i') : '-' }}</span>
        </div>
    </div>

    @php
        $stats = [
            ['text-[#165fac]', number_format($nilaiDisplay, 1), 'Nilai Akhir (100)'],
            ['text-[#059669]', floatval($poinDiperoleh) . '/' . floatval($totalBobot), 'Total Poin'],
            ['text-[#64748b]', $totalSoal, 'Total Soal'],
        ];
    @endphp
    <div class="mb-7 grid grid-cols-2 gap-2 md:grid-cols-4 md:gap-3">
        @foreach($stats as [$warnaStat, $nilaiStat, $labelStat])
            <div class="rounded-xl border border-[#e2e8f0] bg-white px-2.5 py-3.5 text-center md:px-4 md:py-5">
                <h2 class="mb-0.5 mt-1 text-[1.4rem] font-extrabold leading-[1.1] md:text-[1.8rem] {{ $warnaStat }}">{{ $nilaiStat }}</h2>
                <small class="text-[0.78rem] font-medium text-[#64748b]">{{ $labelStat }}</small>
            </div>
        @endforeach
        <div class="rounded-xl border border-[#e2e8f0] bg-white px-2.5 py-3.5 text-center md:px-4 md:py-5">
            @if($menungguKoreksi > 0)
                <h2 class="mb-0.5 mt-1 text-[1.4rem] font-extrabold leading-[1.1] text-[#dc2626] md:text-[1.8rem]"><i class="fas fa-clock"></i></h2><small class="text-[0.78rem] font-medium text-[#64748b]">Menunggu Koreksi Guru</small>
            @else
                <h2 class="mb-0.5 mt-1 text-[1.4rem] font-extrabold leading-[1.1] text-[#059669] md:text-[1.8rem]"><i class="fas fa-check-double"></i></h2><small class="text-[0.78rem] font-medium text-[#64748b]">Selesai Dikoreksi</small>
            @endif
        </div>
    </div>

    @foreach($soalList as $index => $soal)
    @php
        $jawaban = $jawabanMap->get($soal->id);
        $jawabanSiswa = $jawaban->jawaban ?? null;
        $nilaiSoal = $jawaban->nilai_soal ?? 0;
        $mx = $soal->bobot_nilai;
        $empty = !$jawaban || $jawabanSiswa === null || $jawabanSiswa === '';
        $full = $nilaiSoal >= $mx;
        $partial = $nilaiSoal > 0 && !$full;
        $nc = $empty ? 'sk' : ($full ? 'ok' : ($partial ? 'pt' : 'no'));
    @endphp
    <div class="mb-4 overflow-hidden rounded-[14px] border border-[#e2e8f0] bg-white">
        <div class="h-1 {{ $strip[$nc] }}"></div>
        <div class="flex flex-wrap items-center justify-between gap-2 px-3.5 pt-3 md:px-5 md:pt-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-lg text-[0.82rem] font-bold text-white {{ $strip[$nc] }}">{{ $index+1 }}</span>
                <span class="rounded-[6px] bg-[#f1f5f9] px-2.5 py-[3px] text-[0.72rem] font-semibold uppercase tracking-[0.3px] text-[#64748b]">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
            </div>
            <span class="rounded-lg px-3 py-1 text-[0.82rem] font-bold {{ $skor[$nc] }}">@if($empty) - @else {{ number_format($nilaiSoal,1) }}/{{ $mx }} @endif</span>
        </div>
        <div class="px-3.5 pb-4 pt-3 md:px-5 md:pb-5 md:pt-4">
            @if($soal->narasi)
            <div class="mb-3 rounded-lg bg-[#f1f5f9] px-3.5 py-3 text-[0.85rem] text-[#475569]"><strong class="text-[0.75rem] text-[#64748b]"><i class="fas fa-book-open mr-1"></i>Narasi</strong><div class="mt-1">{!! nl2br(e($soal->narasi)) !!}</div></div>
            @endif
            @if($soal->image_path)
            <img src="{{ asset('storage/'.$soal->image_path) }}" class="mb-3 max-h-[300px] max-w-full rounded-lg" alt="Gambar Soal">
            @endif
            <div class="mb-4 rounded-[10px] bg-[#f8fafc] px-3 py-2.5 text-[0.88rem] leading-[1.65] text-[#1e293b] md:px-4 md:py-3.5 md:text-[0.95rem]">{!! nl2br(e($soal->pertanyaan)) !!}</div>

            {{-- ====== PILIHAN GANDA & PILIHAN GANDA KOMPLEKS ====== --}}
            @if(in_array($soal->tipe_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks']))
                @php
                    $pd = $soal->pilihan_jawaban ?? [];
                    $kompleks = $soal->tipe_soal === 'pilihan_ganda_kompleks';
                    if ($kompleks) {
                        $kunciArr = array_map('strtoupper', array_map('trim', $pd['jawaban_benar'] ?? []));
                        $pickedArr = [];
                        if ($jawabanSiswa) {
                            $pickedArr = is_array($jawabanSiswa) ? $jawabanSiswa : (json_decode($jawabanSiswa, true) ?? []);
                            $pickedArr = array_map('strtoupper', array_map('trim', $pickedArr));
                        }
                    } else {
                        $kunci = strtoupper(trim($soal->jawaban_benar ?? ''));
                        $picked = $jawabanSiswa ? strtoupper(trim($jawabanSiswa)) : null;
                    }
                @endphp
                @foreach(['A','B','C','D','E'] as $letter)
                    @if(isset($pd[$letter]) && $pd[$letter] !== null && $pd[$letter] !== '')
                    @php
                        $isK = $kompleks ? in_array($letter, $kunciArr) : $letter === $kunci;
                        $isP = $kompleks ? in_array($letter, $pickedArr) : $letter === $picked;
                        if ($isP && $isK) $c = 'sc';
                        elseif ($isP) $c = 'sw';
                        elseif ($isK) $c = 'ik';
                        else $c = 'neu';
                    @endphp
                    <div class="mb-1.5 flex items-start gap-2.5 rounded-[10px] border-[1.5px] px-2.5 py-2 text-[0.84rem] leading-[1.5] md:px-3.5 md:py-2.5 md:text-[0.9rem] {{ $opsiWarna[$c][0] }}">
                        <span class="inline-flex h-6 min-w-6 shrink-0 items-center justify-center rounded-[7px] text-[0.72rem] font-bold md:h-7 md:min-w-7 md:text-[0.78rem] {{ $opsiWarna[$c][1] }}">{{ $letter }}</span>
                        <div class="grow">
                            {{ $pd[$letter] }}
                            @if($isP && $isK)<span class="{{ $tag }} text-[#71dd37]"><i class="fas fa-check-circle"></i> Benar</span>
                            @elseif($isP)<span class="{{ $tag }} text-[#ff3e1d]"><i class="fas fa-times-circle"></i> Jawaban Anda</span>
                            @elseif($isK)<span class="{{ $tag }} text-[#71dd37]"><i class="fas fa-check"></i> {{ $kompleks ? 'Kunci' : 'Kunci Jawaban' }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                @endforeach

            {{-- ====== BENAR / SALAH ====== --}}
            @elseif($soal->tipe_soal === 'benar_salah')
                @php
                    $pd = $soal->pilihan_jawaban ?? [];
                    $pernyataan = $pd['pernyataan'] ?? [];
                    $jawabanArr = [];
                    if ($jawabanSiswa) {
                        $jawabanArr = is_array($jawabanSiswa) ? $jawabanSiswa : (json_decode($jawabanSiswa, true) ?? []);
                    }
                    $th = 'border-b border-[#e2e8f0] bg-[#f8fafc] p-2 text-left text-[0.75rem] font-semibold uppercase tracking-[0.3px] text-[#64748b] md:px-3 md:py-2.5';
                    $td = 'border-b border-[#e2e8f0] p-2 align-middle group-last:border-b-0 md:px-3 md:py-2.5';
                @endphp
                <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-0 text-[0.82rem] md:text-[0.88rem]">
                    <thead><tr><th class="{{ $th }}">Pernyataan</th><th class="{{ $th }}">Kunci</th><th class="{{ $th }}">Jawaban Anda</th><th class="{{ $th }}">Hasil</th></tr></thead>
                    <tbody>
                    @foreach($pernyataan as $pi => $item)
                        @php
                            $kb = $item['benar'] ?? false;
                            $jb = $jawabanArr[$pi] ?? null;
                            if (is_string($jb)) $jb = filter_var($jb, FILTER_VALIDATE_BOOLEAN);
                            $match = ($jb !== null && $jb === $kb);
                        @endphp
                        <tr class="group {{ $match ? 'bg-[#f0fdf4]' : 'bg-[#fef2f2]' }}">
                            <td class="{{ $td }}">{{ $item['text'] ?? '-' }}</td>
                            <td class="{{ $td }} font-bold">{{ $kb ? 'Benar' : 'Salah' }}</td>
                            <td class="{{ $td }}">{{ $jb !== null ? ($jb ? 'Benar' : 'Salah') : '-' }}</td>
                            <td class="{{ $td }}">@if($jb===null)<i class="fas fa-minus text-[#a1acb8]"></i>@elseif($match)<i class="fas fa-check-circle text-[#71dd37]"></i>@else<i class="fas fa-times-circle text-[#ff3e1d]"></i>@endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>

            {{-- ====== ISIAN SINGKAT & URAIAN ====== --}}
            @else
                @php
                    if ($soal->tipe_soal === 'isian_singkat') {
                        $pd = $soal->pilihan_jawaban ?? [];
                        $kunciIsian = $pd['jawaban_benar'] ?? [];
                        if (!is_array($kunciIsian)) $kunciIsian = [$kunciIsian];
                        if (empty($kunciIsian) && $soal->jawaban_benar) $kunciIsian = [$soal->jawaban_benar];
                    }
                    $cl = 'mb-1.5 text-[0.72rem] font-bold uppercase tracking-[0.4px]';
                @endphp
                <div class="grid gap-2.5 md:grid-cols-2">
                    <div class="rounded-[10px] bg-[#f0f4ff] px-4 py-3.5">
                        <div class="{{ $cl }} text-[#165fac]"><i class="fas fa-pen mr-1"></i> Jawaban Anda</div>
                        <div class="text-[0.9rem] leading-[1.6]">
                            @if($soal->tipe_soal === 'isian_singkat')
                                {{ $jawabanSiswa ?: '-' }}
                            @elseif($jawabanSiswa)
                                {!! nl2br(e($jawabanSiswa)) !!}
                            @else
                                <span class="text-[#a1acb8]">Tidak dijawab</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-[10px] bg-[#ecfdf5] px-4 py-3.5">
                        <div class="{{ $cl }} text-[#059669]"><i class="fas fa-key mr-1"></i> {{ $soal->tipe_soal === 'isian_singkat' ? 'Kunci Jawaban' : 'Kunci / Referensi' }}</div>
                        <div class="text-[0.9rem] leading-[1.6]">
                            @if($soal->tipe_soal === 'isian_singkat')
                                {{ implode(' / ', $kunciIsian) }}
                            @elseif($soal->jawaban_benar)
                                {!! nl2br(e($soal->jawaban_benar)) !!}
                            @else
                                <span class="text-[#a1acb8]">Dinilai manual oleh guru</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($jawaban && $jawaban->feedback)
            <div class="mt-3 rounded-lg bg-[#fffbeb] px-3.5 py-3 text-[0.85rem] text-[#92400e]"><i class="fas fa-comment-dots mr-1"></i> <strong>Catatan Guru:</strong> {{ $jawaban->feedback }}</div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="mt-2 rounded-[14px] border border-[#e2e8f0] bg-white p-6 text-center">
        <p class="mb-4 text-[0.88rem] text-[#64748b]"><i class="fas fa-lightbulb mr-1"></i> Pelajari kembali soal yang salah untuk meningkatkan pemahaman.</p>
        <div class="flex flex-wrap justify-center gap-2">
            <a href="{{ route('siswa.lms.mapel.'.$routePrefix.'.show', [$mapel->id, $ujian->id]) }}" class="inline-flex items-center rounded-[6px] border border-[#233446] bg-[#233446] px-6 py-[7px] leading-[22.95px] text-white no-underline shadow-[0_2px_4px_rgba(35,52,70,0.4)] transition hover:bg-[#1f2f3f] hover:text-white"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
            <a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" class="inline-flex items-center rounded-[6px] border border-[#8592a3] px-6 py-[7px] leading-[22.95px] text-[#8592a3] no-underline transition hover:bg-[#8592a3] hover:text-white"><i class="fas fa-book mr-2"></i>Mata Pelajaran</a>
        </div>
    </div>
</div>
@endsection
