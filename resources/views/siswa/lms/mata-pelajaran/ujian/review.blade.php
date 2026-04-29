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
:root{--rv:#1a1a2e;--ac:#165fac;--ok:#059669;--no:#dc2626;--gr:#64748b;--bg:#f8fafc;--bd:#e2e8f0;}
.rv-hero{background:var(--rv);border-radius:16px;padding:28px 32px;color:#fff;margin-bottom:28px}
.rv-hero h4{font-weight:700;font-size:1.25rem}.rv-hero-sub{opacity:.7;font-size:.88rem}
.rv-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px}
.rv-stat{background:#fff;border-radius:12px;padding:20px 16px;text-align:center;border:1px solid var(--bd)}
.rv-stat h2{font-size:1.8rem;font-weight:800;margin:4px 0 2px}
.rv-stat small{color:var(--gr);font-size:.78rem;font-weight:500}
.rv-stat.s1 h2{color:var(--ac)}.rv-stat.s2 h2{color:var(--ok)}.rv-stat.s3 h2{color:var(--no)}.rv-stat.s4 h2{color:var(--gr)}

.rv-card{background:#fff;border-radius:14px;margin-bottom:16px;border:1px solid var(--bd);overflow:hidden}
.rv-strip{height:4px}
.rv-head{padding:16px 20px 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px}
.rv-body{padding:16px 20px 20px}
.rv-n{width:30px;height:30px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem;color:#fff;flex-shrink:0}
.rv-n.ok{background:var(--ok)}.rv-n.no{background:var(--no)}.rv-n.sk{background:var(--gr)}.rv-n.pt{background:#d97706}
.rv-tp{font-size:.72rem;background:#f1f5f9;color:var(--gr);padding:3px 10px;border-radius:6px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.rv-sc{font-size:.82rem;font-weight:700;padding:4px 12px;border-radius:8px}
.rv-sc.ok{background:#ecfdf5;color:var(--ok)}.rv-sc.no{background:#fef2f2;color:var(--no)}.rv-sc.pt{background:#fffbeb;color:#92400e}.rv-sc.sk{background:#f8fafc;color:var(--gr)}

.rv-q{font-size:.95rem;line-height:1.65;color:#1e293b;margin-bottom:16px;padding:14px 16px;background:var(--bg);border-radius:10px}
.rv-nar{font-size:.85rem;color:#475569;background:#f1f5f9;padding:12px 14px;border-radius:8px;margin-bottom:12px}
.rv-img{max-width:100%;max-height:300px;border-radius:8px;margin-bottom:12px}

/* Options */
.rv-o{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-radius:10px;margin-bottom:6px;font-size:.9rem;line-height:1.5;border:1.5px solid transparent}
.rv-ol{min-width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.78rem;flex-shrink:0;background:#f1f5f9;color:var(--gr)}
.rv-o.neu{background:#fafafa}
.rv-o.sc{background:#ecfdf5;border-color:var(--ok)}.rv-o.sc .rv-ol{background:var(--ok);color:#fff}
.rv-o.sw{background:#fef2f2;border-color:var(--no)}.rv-o.sw .rv-ol{background:var(--no);color:#fff}
.rv-o.ik{background:#f0fdf4;border-color:#86efac}.rv-o.ik .rv-ol{background:var(--ok);color:#fff}
.rv-tag{font-size:.7rem;font-weight:700;margin-left:6px;white-space:nowrap}

/* BS Table */
.rv-tb{width:100%;border-collapse:separate;border-spacing:0;font-size:.88rem}
.rv-tb th{background:#f8fafc;color:var(--gr);font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.3px;padding:10px 12px;border-bottom:1px solid var(--bd)}
.rv-tb td{padding:10px 12px;border-bottom:1px solid var(--bd);vertical-align:middle}
.rv-tb tr:last-child td{border-bottom:none}
.bsok{background:#f0fdf4}.bsno{background:#fef2f2}

/* Compare */
.rv-cmp{display:grid;gap:10px}.rv-cb{border-radius:10px;padding:14px 16px}
.rv-cl{font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;font-weight:700;margin-bottom:6px}
.rv-cv{font-size:.9rem;line-height:1.6}
.rv-cs{background:#f0f4ff}.rv-cs .rv-cl{color:var(--ac)}
.rv-cc{background:#ecfdf5}.rv-cc .rv-cl{color:var(--ok)}

.rv-fb{background:#fffbeb;border-radius:8px;padding:12px 14px;font-size:.85rem;color:#92400e;margin-top:12px}
.rv-ft{background:#fff;border-radius:14px;padding:24px;border:1px solid var(--bd);text-align:center;margin-top:8px}
.rv-ft p{color:var(--gr);font-size:.88rem;margin-bottom:16px}

@media(max-width:767.98px){
.rv-hero{padding:20px 16px;border-radius:12px}.rv-hero h4{font-size:1.05rem}
.rv-stats{grid-template-columns:repeat(2,1fr);gap:8px}.rv-stat{padding:14px 10px}.rv-stat h2{font-size:1.4rem}
.rv-head{padding:12px 14px 0}.rv-body{padding:12px 14px 16px}
.rv-q{padding:10px 12px;font-size:.88rem}
.rv-o{padding:8px 10px;font-size:.84rem}.rv-ol{min-width:24px;height:24px;font-size:.72rem}
.rv-cmp{grid-template-columns:1fr}.rv-tb{font-size:.82rem}.rv-tb th,.rv-tb td{padding:8px}
}
@media(min-width:768px){.rv-cmp{grid-template-columns:1fr 1fr}}
</style>
@endpush

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
    <div class="rv-stat s1"><h2>{{ number_format($nilaiDisplay,1) }}</h2><small>Nilai Terbaik</small></div>
    <div class="rv-stat s2"><h2>{{ $benar }}</h2><small>Benar</small></div>
    <div class="rv-stat s3"><h2>{{ $salah }}</h2><small>Salah</small></div>
    <div class="rv-stat s4"><h2>{{ $tidakDijawab }}</h2><small>Tidak Dijawab</small></div>
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
    <div class="rv-strip" style="background:{{ $empty ? 'var(--gr)' : ($full ? 'var(--ok)' : ($partial ? '#d97706' : 'var(--no)')) }}"></div>
    <div class="rv-head">
        <div class="d-flex align-items-center gap-2">
            <span class="rv-n {{ $nc }}">{{ $index+1 }}</span>
            <span class="rv-tp">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
        </div>
        <span class="rv-sc {{ $nc }}">@if($empty) — @else {{ number_format($nilaiSoal,1) }}/{{ $mx }} @endif</span>
    </div>
    <div class="rv-body">
        @if($soal->narasi)
        <div class="rv-nar"><strong style="font-size:.75rem;color:var(--gr)"><i class="fas fa-book-open me-1"></i>Narasi</strong><div class="mt-1">{!! nl2br(e($soal->narasi)) !!}</div></div>
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
                <div class="rv-cb rv-cs"><div class="rv-cl"><i class="fas fa-pen me-1"></i> Jawaban Anda</div><div class="rv-cv">{{ $jawabanSiswa ?: '—' }}</div></div>
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
@endsection
