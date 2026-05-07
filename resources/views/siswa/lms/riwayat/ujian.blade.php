@extends('layouts.sneat')

@php
    $isLatihan = ($ujian->tipe_ujian ?? null) === 'latihan';
    $kontenLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $color = $isLatihan ? '#7c3aed' : '#dc2626';
    $icon = $isLatihan ? 'fa-pencil-ruler' : 'fa-file-alt';
@endphp

@section('title', 'Detail Riwayat - ' . ($ujian->judul_ujian ?? $kontenLabel))
@section('page-title', 'Detail Riwayat ' . $kontenLabel)
@section('page-subtitle', 'Hasil ' . strtolower($kontenLabel) . ' dari TA sebelumnya')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .riwayat-banner {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 18px;
        display: flex; align-items: center; gap: 14px;
    }
    .riwayat-banner i { font-size: 1.4rem; }
    .riwayat-banner .title { font-weight: 700; font-size: 0.95rem; }
    .riwayat-banner .subtitle { font-size: 0.78rem; opacity: 0.9; }

    .detail-card {
        background: white; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 24px;
    }
    .meta-pills {
        display: flex; flex-wrap: wrap; gap: 8px;
        margin-bottom: 18px; padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }
    .meta-pill {
        background: #f1f5f9; color: #475569;
        padding: 4px 12px; border-radius: 999px;
        font-size: 12px;
    }
    .meta-pill i { margin-right: 4px; color: #94a3b8; }

    .nilai-box {
        background: rgba(22,163,74,.05);
        border: 1px solid rgba(22,163,74,.3);
        border-radius: 10px;
        padding: 18px; text-align: center;
    }
    .nilai-box .label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; }
    .nilai-box .value { font-size: 2.5rem; font-weight: 800; color: #15803d; line-height: 1; }
    .nilai-box.belum { background: rgba(100,116,139,.05); border-color: rgba(100,116,139,.3); }
    .nilai-box.belum .value { color: #64748b; font-size: 1.1rem; }

    .soal-list { display: grid; gap: 12px; margin-top: 14px; }
    .soal-card {
        background: #f8fafc; border: 1px solid #e5e7eb;
        border-radius: 10px; padding: 14px;
    }
    .soal-header {
        display: flex; justify-content: space-between; align-items: center;
        gap: 10px; margin-bottom: 10px; flex-wrap: wrap;
    }
    .soal-pertanyaan { font-weight: 500; color: #1e293b; line-height: 1.5; margin-bottom: 10px; }
    .jawaban-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
        margin-bottom: 6px;
    }
    .jawaban-cell {
        padding: 8px 12px; border-radius: 6px; font-size: 13px;
    }
    .jawaban-cell.label { font-weight: 700; color: #475569; background: #fff; border: 1px solid #e5e7eb; }
    .jawaban-cell.value { background: white; border: 1px solid #e5e7eb; }
    .jawaban-cell.benar { border-left: 3px solid #16a34a; }
    .jawaban-cell.salah { border-left: 3px solid #dc2626; }
    .jawaban-cell.kunci { border-left: 3px solid #4361ee; }

    .badge-skor {
        background: rgba(22,163,74,.1); color: #15803d;
        padding: 3px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
    }
    .badge-skor.salah { background: rgba(220,38,38,.1); color: #b91c1c; }
    .badge-skor.manual { background: rgba(217,119,6,.1); color: #92400e; }

    @media (max-width: 768px) {
        .jawaban-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.riwayat.index') }}">Riwayat LMS</a></li>
            <li class="breadcrumb-item active">Detail {{ $kontenLabel }}</li>
        </ol>
    </nav>

    <div class="riwayat-banner">
        <i class="fas fa-history"></i>
        <div>
            <div class="title">Mode Riwayat — Read Only</div>
            <div class="subtitle">Anda hanya bisa melihat hasil. Tidak bisa mengulang dari halaman ini.</div>
        </div>
    </div>

    <div class="detail-card">
        <h4 class="fw-bold mb-2">
            <i class="fas {{ $icon }} me-2" style="color: {{ $color }};"></i>{{ $ujian->judul_ujian ?? '-' }}
        </h4>

        <div class="meta-pills">
            @if($ujian->kelas?->tahunAjaran)
                <span class="meta-pill"><i class="fas fa-calendar-alt"></i>TA {{ $ujian->kelas->tahunAjaran->nama_tahun_ajaran }}</span>
            @endif
            @if($ujian->kelas)
                <span class="meta-pill"><i class="fas fa-school"></i>{{ $ujian->kelas->nama_kelas }}</span>
            @endif
            @if($ujian->mataPelajaran)
                <span class="meta-pill"><i class="fas fa-book"></i>{{ $ujian->mataPelajaran->nama_mapel }}</span>
            @endif
            @if($ujian->guru)
                <span class="meta-pill"><i class="fas fa-user-tie"></i>{{ $ujian->guru->nama_lengkap }}</span>
            @endif
            @if($ujian->tanggal_mulai)
                <span class="meta-pill"><i class="fas fa-play-circle"></i>{{ \Carbon\Carbon::parse($ujian->tanggal_mulai)->locale('id')->translatedFormat('d M Y') }}</span>
            @endif
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                @if($ujianSiswa->nilai !== null)
                    <div class="nilai-box">
                        <div class="label">Nilai Anda</div>
                        <div class="value">{{ number_format((float) $ujianSiswa->nilai, 1) }}</div>
                    </div>
                @else
                    <div class="nilai-box belum">
                        <div class="label">Status</div>
                        <div class="value">{{ str_replace('_', ' ', $ujianSiswa->status ?? '-') }}</div>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <div style="font-size: 13px; color: #475569;">
                    @if($ujianSiswa->waktu_mulai)
                        <div><i class="fas fa-play me-1 text-muted"></i>Mulai: {{ $ujianSiswa->waktu_mulai->locale('id')->translatedFormat('d M Y, H:i') }}</div>
                    @endif
                    @if($ujianSiswa->waktu_selesai)
                        <div><i class="fas fa-stop me-1 text-muted"></i>Selesai: {{ $ujianSiswa->waktu_selesai->locale('id')->translatedFormat('d M Y, H:i') }}</div>
                    @endif
                    @if($ujianSiswa->pengulangan_ke)
                        <div><i class="fas fa-redo me-1 text-muted"></i>Pengulangan ke-{{ $ujianSiswa->pengulangan_ke }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Soal + Jawaban Saya --}}
        @php
            $soalList = ($ujian->soalUjian ?? collect())->sortBy('urutan')->values();
            $jawabanByMappping = $ujianSiswa->jawabanSiswa->keyBy('soal_ujian_id');
        @endphp

        @if($soalList->isEmpty())
            <div class="alert alert-info">Tidak ada soal untuk ditampilkan.</div>
        @else
            <div class="detail-section">
                <h6 class="fw-bold mb-2">Soal & Jawaban Anda ({{ $soalList->count() }} soal)</h6>
                <div class="soal-list">
                    @foreach($soalList as $soal)
                        @php
                            $jw = $jawabanByMappping[$soal->id] ?? null;
                            $jawabanSaya = $jw->jawaban ?? null;
                            $skorSiswa = $jw?->nilai_soal;
                            $bobot = (float) ($soal->bobot_nilai ?? 0);
                            $skorVal = $skorSiswa !== null ? (float) $skorSiswa : null;
                            $isBenarFlag = null;
                            if ($skorVal !== null && $bobot > 0) {
                                if ($skorVal >= $bobot) $isBenarFlag = true;
                                elseif ($skorVal == 0) $isBenarFlag = false;
                            }
                        @endphp
                        <div class="soal-card">
                            <div class="soal-header">
                                <strong>Soal #{{ $loop->iteration }}</strong>
                                <div class="d-flex gap-1 align-items-center flex-wrap">
                                    <span class="meta-pill" style="font-size: 10px;">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
                                    <span class="meta-pill" style="font-size: 10px;">Bobot: {{ $soal->bobot_nilai ?? 0 }}</span>
                                    @if($skorVal !== null)
                                        @if($isBenarFlag === true)
                                            <span class="badge-skor">+{{ number_format($skorVal, 1) }}</span>
                                        @elseif($isBenarFlag === false)
                                            <span class="badge-skor salah">{{ number_format($skorVal, 1) }}</span>
                                        @else
                                            <span class="badge-skor manual">{{ number_format($skorVal, 1) }}</span>
                                        @endif
                                    @elseif(in_array($soal->tipe_soal, ['uraian', 'essay']))
                                        <span class="badge-skor manual">Belum dikoreksi</span>
                                    @endif
                                </div>
                            </div>

                            @if($soal->narasi)
                                <div class="text-muted small mb-2"><em>{!! nl2br(e($soal->narasi)) !!}</em></div>
                            @endif

                            <div class="soal-pertanyaan">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                            <div class="jawaban-row">
                                <div class="jawaban-cell label">Jawaban Saya</div>
                                <div class="jawaban-cell label">Kunci Jawaban</div>
                            </div>
                            <div class="jawaban-row">
                                <div class="jawaban-cell value {{ $isBenarFlag === true ? 'benar' : ($isBenarFlag === false ? 'salah' : '') }}">
                                    @if($jawabanSaya)
                                        @php
                                            // Tampilkan jawaban siswa: bisa string atau JSON array
                                            $display = $jawabanSaya;
                                            if (is_string($display) && str_starts_with(trim($display), '[')) {
                                                $decoded = json_decode($display, true);
                                                if (is_array($decoded)) {
                                                    $display = implode(', ', array_map(fn($v) => is_bool($v) ? ($v ? 'Benar' : 'Salah') : (string) $v, $decoded));
                                                }
                                            }
                                        @endphp
                                        {{ $display }}
                                    @else
                                        <em class="text-muted">Tidak menjawab</em>
                                    @endif
                                </div>
                                <div class="jawaban-cell value kunci">
                                    @php
                                        $kunci = $soal->kunci_jawaban;
                                        if (is_array($kunci)) {
                                            $kunci = implode(', ', $kunci);
                                        }
                                        if (in_array($soal->tipe_soal, ['uraian', 'essay'])) {
                                            $kunci = $kunci ? \Illuminate\Support\Str::limit((string) $kunci, 200) : '(koreksi manual)';
                                        }
                                    @endphp
                                    {{ $kunci ?: '-' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="text-end mt-3">
            <a href="{{ route('siswa.lms.riwayat.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Riwayat
            </a>
        </div>
    </div>
</div>
@endsection
