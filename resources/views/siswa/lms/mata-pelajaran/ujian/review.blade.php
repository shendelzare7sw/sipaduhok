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
@endphp

@section('title', 'Pembahasan ' . $tipeLabel)
@section('page-title', $mapel->nama_mapel)
@section('page-subtitle', 'Pembahasan ' . $tipeLabel)
@section('sidebar-menu') @include('siswa.partials.sidebar-lms') @endsection

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/ujian/review.css'])
@endpush

@section('content')
<div class="siswa-lms-ujian-review-page">
<div class="rv-hero">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h4><i class="fas fa-clipboard-check me-2 opacity-75"></i>Pembahasan {{ $tipeLabel }}</h4>
            <span class="rv-hero-sub">{{ $ujian->judul_ujian }} &bull; {{ $mapel->nama_mapel }}</span>
        </div>
        <span class="rv-hero-sub"><i class="far fa-calendar me-1"></i>{{ $ujianSiswa->waktu_selesai ? $ujianSiswa->waktu_selesai->format('d M Y, H:i') : '-' }}</span>
    </div>
</div>

<div class="rv-stats">
    <div class="rv-stat s1"><h2>{{ number_format($nilaiDisplay,1) }}</h2><small>Nilai Akhir (100)</small></div>
    <div class="rv-stat s2"><h2>{{ floatval($poinDiperoleh) }}/{{ floatval($totalBobot) }}</h2><small>Total Poin</small></div>
    <div class="rv-stat s4"><h2>{{ $totalSoal }}</h2><small>Total Soal</small></div>
    <div class="rv-stat {{ $menungguKoreksi > 0 ? 's3' : 's2' }}">
        @if($menungguKoreksi > 0)
            <h2><i class="fas fa-clock"></i></h2><small>Menunggu Koreksi Guru</small>
        @else
            <h2><i class="fas fa-check-double"></i></h2><small>Selesai Dikoreksi</small>
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
<div class="rv-card">
    <div class="rv-strip {{ $nc }}"></div>
    <div class="rv-head">
        <div class="d-flex align-items-center gap-2">
            <span class="rv-n {{ $nc }}">{{ $index+1 }}</span>
            <span class="rv-tp">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
        </div>
        <span class="rv-sc {{ $nc }}">@if($empty) - @else {{ number_format($nilaiSoal,1) }}/{{ $mx }} @endif</span>
    </div>
    <div class="rv-body">
        @if($soal->narasi)
        <div class="rv-nar"><strong class="rv-nar-label"><i class="fas fa-book-open me-1"></i>Narasi</strong><div class="mt-1">{!! nl2br(e($soal->narasi)) !!}</div></div>
        @endif
        @if($soal->image_path)
        <img src="{{ asset('storage/'.$soal->image_path) }}" class="rv-img" alt="Gambar Soal">
        @endif
        <div class="rv-q">{!! nl2br(e($soal->pertanyaan)) !!}</div>

        {{-- ====== PILIHAN GANDA ====== --}}
        @if($soal->tipe_soal === 'pilihan_ganda')
            @php
                $pd = $soal->pilihan_jawaban ?? [];
                $kunci = strtoupper(trim($soal->jawaban_benar ?? ''));
                $picked = $jawabanSiswa ? strtoupper(trim($jawabanSiswa)) : null;
            @endphp
            @foreach(['A','B','C','D','E'] as $letter)
                @if(isset($pd[$letter]) && $pd[$letter] !== null && $pd[$letter] !== '')
                @php
                    $isK = $letter === $kunci;
                    $isP = $letter === $picked;
                    if ($isP && $isK) $c = 'sc';
                    elseif ($isP) $c = 'sw';
                    elseif ($isK) $c = 'ik';
                    else $c = 'neu';
                @endphp
                <div class="rv-o {{ $c }}">
                    <span class="rv-ol">{{ $letter }}</span>
                    <div class="flex-grow-1">
                        {{ $pd[$letter] }}
                        @if($isP && $isK)<span class="rv-tag text-success"><i class="fas fa-check-circle"></i> Benar</span>
                        @elseif($isP)<span class="rv-tag text-danger"><i class="fas fa-times-circle"></i> Jawaban Anda</span>
                        @elseif($isK)<span class="rv-tag text-success"><i class="fas fa-check"></i> Kunci Jawaban</span>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach

        {{-- ====== PILIHAN GANDA KOMPLEKS ====== --}}
        @elseif($soal->tipe_soal === 'pilihan_ganda_kompleks')
            @php
                $pd = $soal->pilihan_jawaban ?? [];
                $kunciArr = array_map('strtoupper', array_map('trim', $pd['jawaban_benar'] ?? []));
                $pickedArr = [];
                if ($jawabanSiswa) {
                    $pickedArr = is_array($jawabanSiswa) ? $jawabanSiswa : (json_decode($jawabanSiswa, true) ?? []);
                    $pickedArr = array_map('strtoupper', array_map('trim', $pickedArr));
                }
            @endphp
            @foreach(['A','B','C','D','E'] as $letter)
                @if(isset($pd[$letter]) && $pd[$letter] !== null && $pd[$letter] !== '')
                @php
                    $isK = in_array($letter, $kunciArr);
                    $isP = in_array($letter, $pickedArr);
                    if ($isP && $isK) $c = 'sc';
                    elseif ($isP) $c = 'sw';
                    elseif ($isK) $c = 'ik';
                    else $c = 'neu';
                @endphp
                <div class="rv-o {{ $c }}">
                    <span class="rv-ol">{{ $letter }}</span>
                    <div class="flex-grow-1">
                        {{ $pd[$letter] }}
                        @if($isP && $isK)<span class="rv-tag text-success"><i class="fas fa-check-circle"></i> Benar</span>
                        @elseif($isP)<span class="rv-tag text-danger"><i class="fas fa-times-circle"></i> Jawaban Anda</span>
                        @elseif($isK)<span class="rv-tag text-success"><i class="fas fa-check"></i> Kunci</span>
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
            @endphp
            <div class="table-responsive">
            <table class="rv-tb">
                <thead><tr><th>Pernyataan</th><th>Kunci</th><th>Jawaban Anda</th><th>Hasil</th></tr></thead>
                <tbody>
                @foreach($pernyataan as $pi => $item)
                    @php
                        $kb = $item['benar'] ?? false;
                        $jb = $jawabanArr[$pi] ?? null;
                        if (is_string($jb)) $jb = filter_var($jb, FILTER_VALIDATE_BOOLEAN);
                        $match = ($jb !== null && $jb === $kb);
                    @endphp
                    <tr class="{{ $match ? 'bsok' : 'bsno' }}">
                        <td>{{ $item['text'] ?? '-' }}</td>
                        <td class="fw-bold">{{ $kb ? 'Benar' : 'Salah' }}</td>
                        <td>{{ $jb !== null ? ($jb ? 'Benar' : 'Salah') : '-' }}</td>
                        <td>@if($jb===null)<i class="fas fa-minus text-muted"></i>@elseif($match)<i class="fas fa-check-circle text-success"></i>@else<i class="fas fa-times-circle text-danger"></i>@endif</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

        {{-- ====== ISIAN SINGKAT ====== --}}
        @elseif($soal->tipe_soal === 'isian_singkat')
            @php
                $pd = $soal->pilihan_jawaban ?? [];
                $kunciIsian = $pd['jawaban_benar'] ?? [];
                if (!is_array($kunciIsian)) $kunciIsian = [$kunciIsian];
                if (empty($kunciIsian) && $soal->jawaban_benar) $kunciIsian = [$soal->jawaban_benar];
            @endphp
            <div class="rv-cmp">
                <div class="rv-cb rv-cs"><div class="rv-cl"><i class="fas fa-pen me-1"></i> Jawaban Anda</div><div class="rv-cv">{{ $jawabanSiswa ?: '-' }}</div></div>
                <div class="rv-cb rv-cc"><div class="rv-cl"><i class="fas fa-key me-1"></i> Kunci Jawaban</div><div class="rv-cv">{{ implode(' / ', $kunciIsian) }}</div></div>
            </div>

        {{-- ====== URAIAN / ESSAY ====== --}}
        @else
            <div class="rv-cmp">
                <div class="rv-cb rv-cs"><div class="rv-cl"><i class="fas fa-pen me-1"></i> Jawaban Anda</div><div class="rv-cv">@if($jawabanSiswa){!! nl2br(e($jawabanSiswa)) !!}@else<span class="text-muted">Tidak dijawab</span>@endif</div></div>
                <div class="rv-cb rv-cc"><div class="rv-cl"><i class="fas fa-key me-1"></i> Kunci / Referensi</div><div class="rv-cv">@if($soal->jawaban_benar){!! nl2br(e($soal->jawaban_benar)) !!}@else<span class="text-muted">Dinilai manual oleh guru</span>@endif</div></div>
            </div>
        @endif

        @if($jawaban && $jawaban->feedback)
        <div class="rv-fb"><i class="fas fa-comment-dots me-1"></i> <strong>Catatan Guru:</strong> {{ $jawaban->feedback }}</div>
        @endif
    </div>
</div>
@endforeach

<div class="rv-ft">
    <p><i class="fas fa-lightbulb me-1"></i> Pelajari kembali soal yang salah untuk meningkatkan pemahaman.</p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a href="{{ route('siswa.lms.mapel.'.$routePrefix.'.show', [$mapel->id, $ujian->id]) }}" class="btn btn-dark px-4"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        <a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" class="btn btn-outline-secondary px-4"><i class="fas fa-book me-2"></i>Mata Pelajaran</a>
    </div>
</div>
</div>
@endsection
