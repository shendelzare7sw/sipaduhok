@extends('layouts.app')

@section('title', 'Data Penilaian')
@section('page-title', 'Data Penilaian Harian')
@section('page-subtitle', 'Rekap rincian nilai tugas, latihan, dan ujian Anda')


@section('styles')
    @vite(['resources/css/siswa/sia/penilaian/index.css', 'resources/js/siswa/sia/penilaian/index.js'])
@endsection

@section('content')
<div class="s-page s-page-narrow">

    <div class="s-card mb-4">
        <div class="student-summary">
            <div class="d-flex align-items-center">
                <div class="s-stat-icon s-stat-icon-primary me-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h5 class="s-card-title mb-1">Statistik Belajar</h5>
                    <p class="s-card-subtitle">
                        Kelas: <strong>{{ $siswa->kelas->nama_kelas }}</strong> |
                        Tahun Ajaran: <strong>{{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>
                    </p>
                </div>
            </div>

            <form method="GET" action="">
                <select name="semester" class="form-select bg-light fw-bold text-primary semester-select" data-auto-submit>
                    <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </form>
        </div>
    </div>

    @forelse($nilaiList as $nilai)
    <div class="subject-card">
        <div class="subject-header">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                <div>
                    <h5 class="s-card-title mb-1">
                        <i class="fas fa-book-open me-2 small"></i> {{ $nilai->mataPelajaran->nama_mapel }}
                    </h5>
                    <div class="small text-muted fw-bold">
                        <i class="fas fa-user-tie me-1"></i> Pengampu: {{ $nilai->guru->nama_lengkap ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="score-grid">
                <div>
                    <div class="mini-stat-card">
                        <div class="mini-stat-label">Rata Tugas (10%)</div>
                        <div class="mini-stat-val mini-stat-primary">{{ $nilai->rata_tugas ? number_format($nilai->rata_tugas, 1) : '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="mini-stat-card">
                        <div class="mini-stat-label">Rata Latihan (10%)</div>
                        <div class="mini-stat-val mini-stat-info">{{ $nilai->rata_latihan ? number_format($nilai->rata_latihan, 1) : '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="mini-stat-card">
                        <div class="mini-stat-label">Rata UH (20%)</div>
                        <div class="mini-stat-val mini-stat-info">{{ $nilai->rata_uh ? number_format($nilai->rata_uh, 1) : '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="mini-stat-card">
                        <div class="mini-stat-label">Nilai PTS (30%)</div>
                        <div class="mini-stat-val mini-stat-warning">{{ $nilai->pts ? number_format($nilai->pts, 1) : '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="mini-stat-card">
                        <div class="mini-stat-label">Nilai PAS (30%)</div>
                        <div class="mini-stat-val mini-stat-danger">{{ $nilai->pas ? number_format($nilai->pas, 1) : '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <div class="predicate-bar">
                    <i class="fas fa-award me-2 text-primary"></i>
                    <span class="small fw-bold text-muted me-2">Predikat Capaian:</span>
                    @if($nilai->nilai_akhir)
                        @php
                            $n = $nilai->nilai_akhir;
                            $badge = ($n >= 90) ? 'bg-success' : (($n >= 80) ? 'bg-primary' : (($n >= 70) ? 'bg-info' : 'bg-warning'));
                        @endphp
                        <span class="badge {{ $badge }} px-3 py-1">
                            @if($n >= 90) A (Sangat Baik)
                            @elseif($n >= 80) B (Baik)
                            @elseif($n >= 70) C (Cukup)
                            @elseif($n >= 60) D (Kurang)
                            @else E (Sangat Kurang)
                            @endif
                        </span>
                    @else
                        <span class="badge bg-light border text-muted px-3 py-1">Belum Tersedia</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="s-card">
        <div class="s-empty">
            <div class="s-empty-icon"><i class="fas fa-chart-bar"></i></div>
            <h5>Data penilaian belum diinput oleh guru</h5>
            <p class="mb-0">Silakan hubungi wali kelas jika mata pelajaran belum muncul.</p>
        </div>
    </div>
    @endforelse

    <div class="s-card mb-5 guide-card">
        <div class="card-body py-3">
            <h6 class="fw-bold text-info small mb-2"><i class="fas fa-info-circle me-1"></i>PANDUAN PENILAIAN</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="small text-muted"><strong class="text-dark">Nilai Tugas:</strong> Diambil dari rata-rata aktivitas harian.</div>
                    <div class="small text-muted"><strong class="text-dark">Nilai UTS/UAS:</strong> Skor murni hasil ujian semester.</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted"><strong class="text-dark">Nilai Akhir:</strong> Hasil akumulasi sesuai bobot kurikulum.</div>
                    <div class="small text-muted"><strong class="text-dark">Status:</strong> Nilai ini bersifat sementara sebelum dirilis di E-Rapor.</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
