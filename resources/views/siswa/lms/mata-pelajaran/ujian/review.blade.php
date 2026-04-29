@extends('layouts.lms')

@php
    $isLatihan = $ujian->tipe_ujian === 'latihan';
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $routePrefix = $isLatihan ? 'latihan' : 'ujian';
@endphp

@section('title', 'Pembahasan ' . $tipeLabel . ': ' . $ujian->judul_ujian)
@section('page-title', $mapel->nama_mapel)
@section('page-subtitle', 'Pembahasan ' . $tipeLabel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')

@push('styles')
<style>
    /* ===== BASE ===== */
    .review-header {
        background: linear-gradient(135deg, #165fac 0%, #1e88e5 50%, #42a5f5 100%);
        border-radius: 12px;
        padding: 24px 28px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(22, 95, 172, 0.3);
    }
    .review-header h4 { font-weight: 700; margin-bottom: 4px; }
    .review-header .badge-pill {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
        border-radius: 20px;
        padding: 6px 14px;
        font-size: .8rem;
        font-weight: 600;
    }

    /* Stats row */
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 18px 16px;
        text-align: center;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        transition: transform .2s ease;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 8px;
    }
    .stat-card h3 { font-size: 1.6rem; font-weight: 700; margin-bottom: 2px; }
    .stat-card small { color: #6c757d; font-size: .78rem; }

    /* Soal card */
    .soal-review-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        transition: box-shadow .2s;
    }
    .soal-review-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .soal-review-card .card-header-strip {
        height: 4px;
    }
    .soal-review-card .card-body { padding: 20px 24px; }

    .soal-number-badge {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .85rem;
        color: white;
        flex-shrink: 0;
    }

    /* Question text */
    .question-text {
        font-size: 1rem;
        line-height: 1.7;
        color: #2c3e50;
        padding: 12px 16px;
        background: #f8f9fc;
        border-radius: 8px;
        border-left: 3px solid #165fac;
    }

    /* Answer comparison */
    .answer-comparison {
        display: grid;
        gap: 12px;
    }
    @media(min-width: 768px) {
        .answer-comparison { grid-template-columns: 1fr 1fr; }
    }
    .answer-box {
        border-radius: 10px;
        padding: 14px 16px;
        border: 1px solid;
    }
    .answer-box .answer-label {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .answer-box .answer-value {
        font-size: .95rem;
        font-weight: 500;
        line-height: 1.5;
    }
    .answer-student {
        background: #f0f4ff;
        border-color: #b8d0f8;
    }
    .answer-student .answer-label { color: #165fac; }

    .answer-correct {
        background: #eaf7ee;
        border-color: #b3e0c0;
    }
    .answer-correct .answer-label { color: #198754; }

    /* Feedback */
    .feedback-box {
        background: #fff8e1;
        border: 1px solid #ffe082;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: .9rem;
        color: #7b6b2d;
    }

    /* Narasi */
    .narasi-box {
        background: #f5f0ff;
        border-left: 3px solid #7c3aed;
        padding: 12px 16px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 12px;
    }

    /* Pilihan ganda options */
    .option-review {
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 6px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: .92rem;
        border: 1px solid transparent;
        transition: all .15s;
    }
    .option-review .option-letter {
        width: 28px; height: 28px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .8rem;
        flex-shrink: 0;
    }
    .option-neutral { background: #f8f9fa; }
    .option-neutral .option-letter { background: #e9ecef; color: #6c757d; }

    .option-correct {
        background: #d4edda;
        border-color: #b3e0c0;
    }
    .option-correct .option-letter {
        background: #198754; color: white;
    }

    .option-wrong {
        background: #f8d7da;
        border-color: #f1aeb5;
    }
    .option-wrong .option-letter {
        background: #dc3545; color: white;
    }

    .option-student-correct {
        background: #d4edda;
        border-color: #198754;
        border-width: 2px;
    }
    .option-student-correct .option-letter {
        background: #198754; color: white;
    }

    /* Score pill */
    .score-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
    }
    .score-full { background: #d4edda; color: #0f5132; }
    .score-partial { background: #fff3cd; color: #664d03; }
    .score-zero { background: #f8d7da; color: #842029; }

    /* Back button area */
    .review-footer {
        background: white;
        border-radius: 12px;
        padding: 20px 24px;
        border: 1px solid #e9ecef;
        text-align: center;
    }

    /* Mobile optimizations */
    @media(max-width: 767.98px) {
        .review-header { padding: 18px 16px; }
        .review-header h4 { font-size: 1.1rem; }
        .stat-card { padding: 14px 12px; }
        .stat-card h3 { font-size: 1.3rem; }
        .soal-review-card .card-body { padding: 16px; }
        .question-text { font-size: .92rem; padding: 10px 12px; }
        .answer-comparison { grid-template-columns: 1fr; }
        .answer-box { padding: 12px; }
        .option-review { padding: 8px 10px; font-size: .85rem; }
        .option-review .option-letter { width: 24px; height: 24px; font-size: .72rem; }
    }
</style>
@endpush

{{-- ===== HEADER ===== --}}
<div class="review-header">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h4 class="mb-1"><i class="fas fa-clipboard-check me-2"></i>Pembahasan {{ $tipeLabel }}</h4>
            <p class="mb-0 opacity-75">{{ $ujian->judul_ujian }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge-pill"><i class="fas fa-book-open me-1"></i> {{ $mapel->nama_mapel }}</span>
            <span class="badge-pill"><i class="fas fa-calendar me-1"></i> {{ $ujianSiswa->waktu_selesai ? $ujianSiswa->waktu_selesai->format('d M Y') : '-' }}</span>
        </div>
    </div>
</div>

{{-- ===== STATS ===== --}}
@php
    $soalList = $ujian->soalUjian;
    $jawabanMap = $ujianSiswa->jawabanSiswa->keyBy('soal_ujian_id');
    $totalSoal = $soalList->count();
    $benar = 0;
    $salah = 0;
    $tidakDijawab = 0;

    foreach($soalList as $soal) {
        $jawaban = $jawabanMap->get($soal->id);
        if (!$jawaban || $jawaban->jawaban === null || $jawaban->jawaban === '') {
            $tidakDijawab++;
        } elseif ($jawaban->nilai_soal !== null && $jawaban->nilai_soal >= $soal->bobot_nilai) {
            $benar++;
        } else {
            $salah++;
        }
    }
    $nilaiDisplay = $ujianSiswa->nilai_terbaik ?? $ujianSiswa->nilai ?? 0;
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto"><i class="fas fa-star"></i></div>
            <h3 class="text-primary">{{ number_format($nilaiDisplay, 1) }}</h3>
            <small>Nilai Terbaik</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto"><i class="fas fa-check"></i></div>
            <h3 class="text-success">{{ $benar }}</h3>
            <small>Benar</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto"><i class="fas fa-times"></i></div>
            <h3 class="text-danger">{{ $salah }}</h3>
            <small>Salah</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-secondary bg-opacity-10 text-secondary mx-auto"><i class="fas fa-minus-circle"></i></div>
            <h3 class="text-secondary">{{ $tidakDijawab }}</h3>
            <small>Tidak Dijawab</small>
        </div>
    </div>
</div>

{{-- ===== SOAL LIST ===== --}}
@foreach($soalList as $index => $soal)
    @php
        $jawaban = $jawabanMap->get($soal->id);
        $jawabanSiswa = $jawaban->jawaban ?? null;
        $nilaiSoal = $jawaban->nilai_soal ?? 0;
        $maxNilai = $soal->bobot_nilai;
        $isCorrect = $nilaiSoal >= $maxNilai;
        $isPartial = $nilaiSoal > 0 && $nilaiSoal < $maxNilai;
        $stripColor = $isCorrect ? '#198754' : ($isPartial ? '#ffc107' : '#dc3545');

        if (!$jawaban || $jawabanSiswa === null || $jawabanSiswa === '') {
            $stripColor = '#6c757d';
        }
    @endphp

    <div class="soal-review-card">
        <div class="card-header-strip" style="background: {{ $stripColor }};"></div>
        <div class="card-body">
            {{-- Soal Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="soal-number-badge" style="background: {{ $stripColor }}">{{ $index + 1 }}</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(!$jawaban || $jawabanSiswa === null || $jawabanSiswa === '')
                        <span class="score-pill score-zero"><i class="fas fa-minus-circle"></i> Tidak dijawab</span>
                    @elseif($isCorrect)
                        <span class="score-pill score-full"><i class="fas fa-check-circle"></i> {{ $nilaiSoal }}/{{ $maxNilai }}</span>
                    @elseif($isPartial)
                        <span class="score-pill score-partial"><i class="fas fa-adjust"></i> {{ $nilaiSoal }}/{{ $maxNilai }}</span>
                    @else
                        <span class="score-pill score-zero"><i class="fas fa-times-circle"></i> {{ $nilaiSoal }}/{{ $maxNilai }}</span>
                    @endif
                </div>
            </div>

            {{-- Narasi --}}
            @if($soal->narasi)
                <div class="narasi-box mb-3">
                    <strong class="d-block mb-1" style="font-size:.82rem;color:#5b21b6;"><i class="fas fa-book-open me-1"></i>Narasi:</strong>
                    <div class="text-dark" style="font-size:.9rem;">{!! nl2br(e($soal->narasi)) !!}</div>
                </div>
            @endif

            {{-- Pertanyaan --}}
            <div class="question-text mb-3">
                {!! nl2br(e($soal->pertanyaan)) !!}
            </div>

            {{-- Answer Display --}}
            @if(in_array($soal->tipe_soal, ['pilihan_ganda', 'benar_salah']))
                {{-- Pilihan Ganda & Benar/Salah: Show option list --}}
                @php
                    if ($soal->tipe_soal === 'benar_salah') {
                        $options = ['Benar' => 'Benar', 'Salah' => 'Salah'];
                    } else {
                        $options = [];
                        foreach (['A', 'B', 'C', 'D', 'E'] as $huruf) {
                            $opsiField = 'opsi_' . strtolower($huruf);
                            if (!empty($soal->$opsiField)) {
                                $options[$huruf] = $soal->$opsiField;
                            }
                        }
                    }
                    $kunciJawaban = $soal->jawaban_benar;
                @endphp

                <div class="mb-3">
                    @foreach($options as $key => $text)
                        @php
                            $isKunci = strtolower($key) === strtolower($kunciJawaban);
                            $isPilihan = $jawabanSiswa !== null && strtolower($jawabanSiswa) === strtolower($key);
                            
                            if ($isPilihan && $isKunci) {
                                $optionClass = 'option-student-correct';
                            } elseif ($isPilihan && !$isKunci) {
                                $optionClass = 'option-wrong';
                            } elseif ($isKunci) {
                                $optionClass = 'option-correct';
                            } else {
                                $optionClass = 'option-neutral';
                            }
                        @endphp
                        <div class="option-review {{ $optionClass }}">
                            <span class="option-letter">{{ $key }}</span>
                            <div class="flex-grow-1">
                                {{ $text }}
                                @if($isPilihan && $isKunci)
                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                @elseif($isPilihan && !$isKunci)
                                    <i class="fas fa-times-circle text-danger ms-1"></i> <small class="text-danger">(Jawaban Anda)</small>
                                @elseif($isKunci)
                                    <i class="fas fa-check text-success ms-1"></i> <small class="text-success">(Jawaban Benar)</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif($soal->tipe_soal === 'kompleks')
                {{-- Pilihan Ganda Kompleks --}}
                @php
                    $kunciKompleks = is_array($soal->jawaban_benar) ? $soal->jawaban_benar : json_decode($soal->jawaban_benar, true);
                    $jawabanSiswaKompleks = is_array($jawabanSiswa) ? $jawabanSiswa : json_decode($jawabanSiswa, true);
                    $kunciKompleks = $kunciKompleks ?? [];
                    $jawabanSiswaKompleks = $jawabanSiswaKompleks ?? [];
                @endphp
                <div class="mb-3">
                    @foreach (['A', 'B', 'C', 'D', 'E'] as $huruf)
                        @php
                            $opsiField = 'opsi_' . strtolower($huruf);
                            if (empty($soal->$opsiField)) continue;
                            $isKunci = in_array($huruf, $kunciKompleks);
                            $isPilihan = in_array($huruf, $jawabanSiswaKompleks);

                            if ($isPilihan && $isKunci) {
                                $optionClass = 'option-student-correct';
                            } elseif ($isPilihan && !$isKunci) {
                                $optionClass = 'option-wrong';
                            } elseif ($isKunci) {
                                $optionClass = 'option-correct';
                            } else {
                                $optionClass = 'option-neutral';
                            }
                        @endphp
                        <div class="option-review {{ $optionClass }}">
                            <span class="option-letter">{{ $huruf }}</span>
                            <div class="flex-grow-1">
                                {{ $soal->$opsiField }}
                                @if($isPilihan && $isKunci)
                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                @elseif($isPilihan && !$isKunci)
                                    <i class="fas fa-times-circle text-danger ms-1"></i> <small class="text-danger">(Jawaban Anda)</small>
                                @elseif($isKunci)
                                    <i class="fas fa-check text-success ms-1"></i> <small class="text-success">(Jawaban Benar)</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                {{-- Uraian / Essay / Isian Singkat --}}
                <div class="answer-comparison mb-3">
                    <div class="answer-box answer-student">
                        <div class="answer-label"><i class="fas fa-user"></i> Jawaban Anda</div>
                        <div class="answer-value">
                            @if($jawabanSiswa)
                                {!! nl2br(e($jawabanSiswa)) !!}
                            @else
                                <span class="text-muted fst-italic">Tidak dijawab</span>
                            @endif
                        </div>
                    </div>
                    <div class="answer-box answer-correct">
                        <div class="answer-label"><i class="fas fa-key"></i> Kunci Jawaban</div>
                        <div class="answer-value">
                            @if($soal->jawaban_benar)
                                {!! nl2br(e(is_array($soal->jawaban_benar) ? implode(', ', $soal->jawaban_benar) : $soal->jawaban_benar)) !!}
                            @else
                                <span class="text-muted fst-italic">Dinilai oleh guru</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Feedback Guru --}}
            @if($jawaban && $jawaban->feedback)
                <div class="feedback-box">
                    <strong class="d-block mb-1"><i class="fas fa-comment-dots me-1"></i> Catatan Guru:</strong>
                    {!! nl2br(e($jawaban->feedback)) !!}
                </div>
            @endif
        </div>
    </div>
@endforeach

{{-- ===== FOOTER ===== --}}
<div class="review-footer">
    <p class="text-muted mb-3"><i class="fas fa-lightbulb text-warning me-1"></i> Pelajari kembali soal-soal yang salah untuk meningkatkan pemahaman Anda.</p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a href="{{ route('siswa.lms.mapel.' . $routePrefix . '.show', [$mapel->id, $ujian->id]) }}" class="btn btn-primary px-4">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke {{ $tipeLabel }}
        </a>
        <a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-book me-2"></i> Ke Mata Pelajaran
        </a>
    </div>
</div>

@endsection
