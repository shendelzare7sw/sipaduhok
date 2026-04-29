@extends('layouts.lms')

@php
    $isLatihan = $ujian->tipe_ujian === 'latihan';
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $routePrefix = $isLatihan ? 'latihan' : 'ujian';
    $soalList = $ujian->soalUjian;
    $jawabanMap = $ujianSiswa->jawabanSiswa->keyBy('soal_ujian_id');
    $totalSoal = $soalList->count();
    $benar = 0; $salah = 0; $tidakDijawab = 0;
    foreach($soalList as $s) {
        $j = $jawabanMap->get($s->id);
        if (!$j || $j->jawaban === null || $j->jawaban === '') { $tidakDijawab++; }
        elseif ($j->nilai_soal !== null && $j->nilai_soal >= $s->bobot_nilai) { $benar++; }
        else { $salah++; }
    }
    $nilaiDisplay = $ujianSiswa->nilai_terbaik ?? $ujianSiswa->nilai ?? 0;
@endphp

@section('title', 'Pembahasan ' . $tipeLabel)
@section('page-title', $mapel->nama_mapel)
@section('page-subtitle', 'Pembahasan ' . $tipeLabel)
@section('sidebar-menu') @include('siswa.partials.sidebar-lms') @endsection

@section('content')
@push('styles')
<style>
:root { --rv-primary: #1a1a2e; --rv-accent: #165fac; --rv-green: #059669; --rv-red: #dc2626; --rv-gray: #64748b; --rv-light: #f8fafc; --rv-border: #e2e8f0; }
.rv-hero { background: var(--rv-primary); border-radius: 16px; padding: 28px 32px; color: #fff; margin-bottom: 28px; }
.rv-hero h4 { font-weight: 700; font-size: 1.25rem; }
.rv-hero-sub { opacity: .7; font-size: .88rem; }
.rv-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 28px; }
.rv-stat { background: #fff; border-radius: 12px; padding: 20px 16px; text-align: center; border: 1px solid var(--rv-border); }
.rv-stat h2 { font-size: 1.8rem; font-weight: 800; margin: 4px 0 2px; }
.rv-stat small { color: var(--rv-gray); font-size: .78rem; font-weight: 500; }
.rv-stat.st-score h2 { color: var(--rv-accent); }
.rv-stat.st-correct h2 { color: var(--rv-green); }
.rv-stat.st-wrong h2 { color: var(--rv-red); }
.rv-stat.st-skip h2 { color: var(--rv-gray); }

.rv-soal { background: #fff; border-radius: 14px; margin-bottom: 16px; border: 1px solid var(--rv-border); overflow: hidden; }
.rv-soal-head { padding: 16px 20px 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
.rv-num { width: 30px; height: 30px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .82rem; color: #fff; flex-shrink: 0; }
.rv-num.ok { background: var(--rv-green); }
.rv-num.fail { background: var(--rv-red); }
.rv-num.skip { background: var(--rv-gray); }
.rv-num.partial { background: #d97706; }
.rv-tipe { font-size: .72rem; background: #f1f5f9; color: var(--rv-gray); padding: 3px 10px; border-radius: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }
.rv-score-tag { font-size: .82rem; font-weight: 700; padding: 4px 12px; border-radius: 8px; }
.rv-score-tag.ok { background: #ecfdf5; color: var(--rv-green); }
.rv-score-tag.fail { background: #fef2f2; color: var(--rv-red); }
.rv-score-tag.partial { background: #fffbeb; color: #92400e; }
.rv-score-tag.skip { background: #f8fafc; color: var(--rv-gray); }

.rv-soal-body { padding: 16px 20px 20px; }
.rv-question { font-size: .95rem; line-height: 1.65; color: #1e293b; margin-bottom: 16px; padding: 14px 16px; background: var(--rv-light); border-radius: 10px; }
.rv-narasi { font-size: .85rem; color: #475569; background: #f1f5f9; padding: 12px 14px; border-radius: 8px; margin-bottom: 12px; }
.rv-narasi strong { font-size: .75rem; text-transform: uppercase; letter-spacing: .3px; color: var(--rv-gray); }

/* PG Options */
.rv-opt { display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; border-radius: 10px; margin-bottom: 6px; font-size: .9rem; line-height: 1.5; border: 1.5px solid transparent; transition: .15s; }
.rv-opt-letter { min-width: 28px; height: 28px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .78rem; flex-shrink: 0; background: #f1f5f9; color: var(--rv-gray); }
.rv-opt.neutral { background: #fafafa; }
.rv-opt.student-correct { background: #ecfdf5; border-color: var(--rv-green); }
.rv-opt.student-correct .rv-opt-letter { background: var(--rv-green); color: #fff; }
.rv-opt.student-wrong { background: #fef2f2; border-color: var(--rv-red); }
.rv-opt.student-wrong .rv-opt-letter { background: var(--rv-red); color: #fff; }
.rv-opt.is-kunci { background: #f0fdf4; border-color: #86efac; }
.rv-opt.is-kunci .rv-opt-letter { background: var(--rv-green); color: #fff; }
.rv-opt-tag { font-size: .7rem; font-weight: 700; margin-left: 6px; white-space: nowrap; }

/* Benar/Salah Table */
.rv-bs-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .88rem; }
.rv-bs-table th { background: #f8fafc; color: var(--rv-gray); font-weight: 600; font-size: .75rem; text-transform: uppercase; letter-spacing: .3px; padding: 10px 12px; border-bottom: 1px solid var(--rv-border); }
.rv-bs-table td { padding: 10px 12px; border-bottom: 1px solid var(--rv-border); vertical-align: middle; }
.rv-bs-table tr:last-child td { border-bottom: none; }
.rv-bs-row-ok { background: #f0fdf4; }
.rv-bs-row-fail { background: #fef2f2; }

/* Isian/Uraian */
.rv-compare { display: grid; gap: 10px; }
.rv-compare-box { border-radius: 10px; padding: 14px 16px; }
.rv-compare-box .rv-compare-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .4px; font-weight: 700; margin-bottom: 6px; }
.rv-compare-box .rv-compare-value { font-size: .9rem; line-height: 1.6; }
.rv-compare-student { background: #f0f4ff; }
.rv-compare-student .rv-compare-label { color: var(--rv-accent); }
.rv-compare-correct { background: #ecfdf5; }
.rv-compare-correct .rv-compare-label { color: var(--rv-green); }

/* Feedback */
.rv-feedback { background: #fffbeb; border-radius: 8px; padding: 12px 14px; font-size: .85rem; color: #92400e; margin-top: 12px; }

/* Footer */
.rv-footer { background: #fff; border-radius: 14px; padding: 24px; border: 1px solid var(--rv-border); text-align: center; margin-top: 8px; }
.rv-footer p { color: var(--rv-gray); font-size: .88rem; margin-bottom: 16px; }

/* Soal image */
.rv-soal-img { max-width: 100%; max-height: 300px; border-radius: 8px; margin-bottom: 12px; }

@media(max-width:767.98px) {
    .rv-hero { padding: 20px 16px; border-radius: 12px; }
    .rv-hero h4 { font-size: 1.05rem; }
    .rv-stats { grid-template-columns: repeat(2,1fr); gap: 8px; }
    .rv-stat { padding: 14px 10px; }
    .rv-stat h2 { font-size: 1.4rem; }
    .rv-soal-head { padding: 12px 14px 0; }
    .rv-soal-body { padding: 12px 14px 16px; }
    .rv-question { padding: 10px 12px; font-size: .88rem; }
    .rv-opt { padding: 8px 10px; font-size: .84rem; }
    .rv-opt-letter { min-width: 24px; height: 24px; font-size: .72rem; }
    .rv-compare { grid-template-columns: 1fr; }
    .rv-bs-table { font-size: .82rem; }
    .rv-bs-table th, .rv-bs-table td { padding: 8px; }
}
@media(min-width:768px) {
    .rv-compare { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

{{-- HERO --}}
<div class="rv-hero">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h4><i class="fas fa-clipboard-check me-2 opacity-75"></i>Pembahasan {{ $tipeLabel }}</h4>
            <span class="rv-hero-sub">{{ $ujian->judul_ujian }} &bull; {{ $mapel->nama_mapel }}</span>
        </div>
        <span class="rv-hero-sub"><i class="far fa-calendar me-1"></i>{{ $ujianSiswa->waktu_selesai ? $ujianSiswa->waktu_selesai->format('d M Y, H:i') : '-' }}</span>
    </div>
</div>

{{-- STATS --}}
<div class="rv-stats">
    <div class="rv-stat st-score"><h2>{{ number_format($nilaiDisplay,1) }}</h2><small>Nilai Terbaik</small></div>
    <div class="rv-stat st-correct"><h2>{{ $benar }}</h2><small>Benar</small></div>
    <div class="rv-stat st-wrong"><h2>{{ $salah }}</h2><small>Salah</small></div>
    <div class="rv-stat st-skip"><h2>{{ $tidakDijawab }}</h2><small>Tidak Dijawab</small></div>
</div>

{{-- SOAL LIST --}}
@foreach($soalList as $index => $soal)
@php
    $jawaban = $jawabanMap->get($soal->id);
    $jawabanSiswa = $jawaban->jawaban ?? null;
    $nilaiSoal = $jawaban->nilai_soal ?? 0;
    $maxNilai = $soal->bobot_nilai;
    $empty = !$jawaban || $jawabanSiswa === null || $jawabanSiswa === '';
    $full = $nilaiSoal >= $maxNilai;
    $partial = $nilaiSoal > 0 && !$full;
    $numClass = $empty ? 'skip' : ($full ? 'ok' : ($partial ? 'partial' : 'fail'));
    $scoreClass = $empty ? 'skip' : ($full ? 'ok' : ($partial ? 'partial' : 'fail'));
@endphp
<div class="rv-soal">
    <div class="rv-soal-head">
        <div class="d-flex align-items-center gap-2">
            <span class="rv-num {{ $numClass }}">{{ $index+1 }}</span>
            <span class="rv-tipe">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
        </div>
        <span class="rv-score-tag {{ $scoreClass }}">
            @if($empty) — @else {{ number_format($nilaiSoal,1) }}/{{ $maxNilai }} @endif
        </span>
    </div>
    <div class="rv-soal-body">
        @if($soal->narasi)
        <div class="rv-narasi"><strong><i class="fas fa-book-open me-1"></i>Narasi</strong><div class="mt-1">{!! nl2br(e($soal->narasi)) !!}</div></div>
        @endif

        @if($soal->image_path)
        <img src="{{ asset('storage/'.$soal->image_path) }}" class="rv-soal-img" alt="Gambar Soal">
        @endif

        <div class="rv-question">{!! nl2br(e($soal->pertanyaan)) !!}</div>

        {{-- ============ PILIHAN GANDA ============ --}}
        @if($soal->tipe_soal === 'pilihan_ganda')
            @php
                $pilihanData = $soal->pilihan_jawaban;
                $options = $pilihanData['options'] ?? [];
                $kunci = strtoupper(trim($soal->jawaban_benar ?? ''));
                $picked = $jawabanSiswa ? strtoupper(trim($jawabanSiswa)) : null;
                $letters = ['A','B','C','D','E'];
            @endphp
            @foreach($options as $i => $optText)
                @php
                    $letter = $letters[$i] ?? chr(65+$i);
                    $isKunci = $letter === $kunci;
                    $isPicked = $letter === $picked;
                    if ($isPicked && $isKunci) $cls = 'student-correct';
                    elseif ($isPicked) $cls = 'student-wrong';
                    elseif ($isKunci) $cls = 'is-kunci';
                    else $cls = 'neutral';
                @endphp
                <div class="rv-opt {{ $cls }}">
                    <span class="rv-opt-letter">{{ $letter }}</span>
                    <div class="flex-grow-1">
                        {{ $optText }}
                        @if($isPicked && $isKunci)<span class="rv-opt-tag text-success"><i class="fas fa-check-circle"></i> Benar</span>
                        @elseif($isPicked)<span class="rv-opt-tag text-danger"><i class="fas fa-times-circle"></i> Jawaban Anda</span>
                        @elseif($isKunci)<span class="rv-opt-tag text-success"><i class="fas fa-check"></i> Kunci Jawaban</span>
                        @endif
                    </div>
                </div>
            @endforeach

        {{-- ============ PILIHAN GANDA KOMPLEKS ============ --}}
        @elseif($soal->tipe_soal === 'pilihan_ganda_kompleks')
            @php
                $pilihanData = $soal->pilihan_jawaban;
                $options = $pilihanData['options'] ?? [];
                $kunciArr = array_map('strtoupper', array_map('trim', $pilihanData['jawaban_benar'] ?? []));
                $pickedArr = [];
                if ($jawabanSiswa) {
                    $pickedArr = is_array($jawabanSiswa) ? $jawabanSiswa : (json_decode($jawabanSiswa, true) ?? []);
                    $pickedArr = array_map('strtoupper', array_map('trim', $pickedArr));
                }
                $letters = ['A','B','C','D','E'];
            @endphp
            @foreach($options as $i => $optText)
                @php
                    $letter = $letters[$i] ?? chr(65+$i);
                    $isKunci = in_array($letter, $kunciArr);
                    $isPicked = in_array($letter, $pickedArr);
                    if ($isPicked && $isKunci) $cls = 'student-correct';
                    elseif ($isPicked) $cls = 'student-wrong';
                    elseif ($isKunci) $cls = 'is-kunci';
                    else $cls = 'neutral';
                @endphp
                <div class="rv-opt {{ $cls }}">
                    <span class="rv-opt-letter">{{ $letter }}</span>
                    <div class="flex-grow-1">
                        {{ $optText }}
                        @if($isPicked && $isKunci)<span class="rv-opt-tag text-success"><i class="fas fa-check-circle"></i> Benar</span>
                        @elseif($isPicked)<span class="rv-opt-tag text-danger"><i class="fas fa-times-circle"></i> Jawaban Anda</span>
                        @elseif($isKunci)<span class="rv-opt-tag text-success"><i class="fas fa-check"></i> Kunci</span>
                        @endif
                    </div>
                </div>
            @endforeach

        {{-- ============ BENAR / SALAH ============ --}}
        @elseif($soal->tipe_soal === 'benar_salah')
            @php
                $pilihanData = $soal->pilihan_jawaban;
                $pernyataan = $pilihanData['pernyataan'] ?? [];
                $jawabanArr = [];
                if ($jawabanSiswa) {
                    $jawabanArr = is_array($jawabanSiswa) ? $jawabanSiswa : (json_decode($jawabanSiswa, true) ?? []);
                }
            @endphp
            <table class="rv-bs-table">
                <thead><tr><th style="width:50%">Pernyataan</th><th>Kunci</th><th>Jawaban Anda</th><th>Hasil</th></tr></thead>
                <tbody>
                @foreach($pernyataan as $pi => $item)
                    @php
                        $kunciBs = $item['benar'] ?? false;
                        $jawabanBs = $jawabanArr[$pi] ?? null;
                        if (is_string($jawabanBs)) $jawabanBs = filter_var($jawabanBs, FILTER_VALIDATE_BOOLEAN);
                        $bsMatch = ($jawabanBs !== null && $jawabanBs === $kunciBs);
                    @endphp
                    <tr class="{{ $bsMatch ? 'rv-bs-row-ok' : 'rv-bs-row-fail' }}">
                        <td>{{ $item['text'] ?? '-' }}</td>
                        <td><span class="fw-bold">{{ $kunciBs ? 'Benar' : 'Salah' }}</span></td>
                        <td>{{ $jawabanBs !== null ? ($jawabanBs ? 'Benar' : 'Salah') : '-' }}</td>
                        <td>
                            @if($jawabanBs === null) <i class="fas fa-minus text-muted"></i>
                            @elseif($bsMatch) <i class="fas fa-check-circle text-success"></i>
                            @else <i class="fas fa-times-circle text-danger"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        {{-- ============ ISIAN SINGKAT ============ --}}
        @elseif($soal->tipe_soal === 'isian_singkat')
            @php
                $pilihanData = $soal->pilihan_jawaban;
                $kunciIsian = $pilihanData['jawaban_benar'] ?? [];
                if (!is_array($kunciIsian)) $kunciIsian = [$kunciIsian];
                if (empty($kunciIsian) && $soal->jawaban_benar) $kunciIsian = [$soal->jawaban_benar];
            @endphp
            <div class="rv-compare">
                <div class="rv-compare-box rv-compare-student">
                    <div class="rv-compare-label"><i class="fas fa-pen me-1"></i> Jawaban Anda</div>
                    <div class="rv-compare-value">{{ $jawabanSiswa ?: '—' }}</div>
                </div>
                <div class="rv-compare-box rv-compare-correct">
                    <div class="rv-compare-label"><i class="fas fa-key me-1"></i> Kunci Jawaban</div>
                    <div class="rv-compare-value">{{ implode(' / ', $kunciIsian) }}</div>
                </div>
            </div>

        {{-- ============ URAIAN / ESSAY ============ --}}
        @else
            <div class="rv-compare">
                <div class="rv-compare-box rv-compare-student">
                    <div class="rv-compare-label"><i class="fas fa-pen me-1"></i> Jawaban Anda</div>
                    <div class="rv-compare-value">
                        @if($jawabanSiswa) {!! nl2br(e($jawabanSiswa)) !!} @else <span class="text-muted">Tidak dijawab</span> @endif
                    </div>
                </div>
                <div class="rv-compare-box rv-compare-correct">
                    <div class="rv-compare-label"><i class="fas fa-key me-1"></i> Kunci / Referensi</div>
                    <div class="rv-compare-value">
                        @if($soal->jawaban_benar) {!! nl2br(e($soal->jawaban_benar)) !!} @else <span class="text-muted">Dinilai manual oleh guru</span> @endif
                    </div>
                </div>
            </div>
        @endif

        @if($jawaban && $jawaban->feedback)
        <div class="rv-feedback"><i class="fas fa-comment-dots me-1"></i> <strong>Catatan Guru:</strong> {{ $jawaban->feedback }}</div>
        @endif
    </div>
</div>
@endforeach

{{-- FOOTER --}}
<div class="rv-footer">
    <p><i class="fas fa-lightbulb me-1"></i> Pelajari kembali soal yang salah untuk meningkatkan pemahaman.</p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a href="{{ route('siswa.lms.mapel.'.$routePrefix.'.show', [$mapel->id, $ujian->id]) }}" class="btn btn-dark px-4"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        <a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" class="btn btn-outline-secondary px-4"><i class="fas fa-book me-2"></i>Mata Pelajaran</a>
    </div>
</div>
@endsection
