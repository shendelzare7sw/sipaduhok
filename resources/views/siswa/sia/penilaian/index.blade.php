@extends('layouts.sneat')

@section('title', 'Data Penilaian')
@section('page-title', 'Data Penilaian Harian')
@section('page-subtitle', 'Rekap rincian nilai tugas, latihan, dan ujian Anda')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@section('styles')
<style>
    /* === 1. SUBJECT CARD STYLING === */
    .subject-card {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(58, 59, 69, 0.08);
        overflow: hidden;
        margin-bottom: 25px;
        transition: transform 0.2s;
    }
    .subject-card:hover { transform: translateY(-3px); }

    .subject-header {
        padding: 20px 25px;
        background: #f8f9fc;
        border-bottom: 1px solid #edf2f9;
    }

    /* === 2. FINAL SCORE BADGE === */
    .final-score-box {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 15px 25px;
        border-radius: 12px;
        text-align: center;
        min-width: 120px;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
    }
    .final-score-val { font-size: 2.2rem; font-weight: 800; line-height: 1; }

    /* === 3. VIBRANT MINI STATS === */
    .mini-stat-card {
        padding: 15px;
        border-radius: 10px;
        border: none;
        border-left: 4px solid;
        background: #f8f9fc;
    }
    .mini-stat-label { font-size: 10px; font-weight: 800; color: #b7b9cc; text-transform: uppercase; margin-bottom: 2px; }
    .mini-stat-val { font-size: 18px; font-weight: 700; color: #4e73df; }

    .border-start-tugas { border-left-color: #36b9cc !important; }
    .border-start-uts   { border-left-color: #f6c23e !important; }
    .border-start-uas   { border-left-color: #e74a3b !important; }

    /* === 4. PREDICATE BAR === */
    .predicate-bar {
        padding: 10px 15px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #eaecf4;
        display: inline-flex;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0" style="max-width: 1000px; margin: 0 auto;">

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #fff;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary-soft p-3 me-3" style="background: rgba(78, 115, 223, 0.1);">
                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Statistik Belajar Siswa</h5>
                    <p class="text-muted small mb-0">
                        Kelas: <strong>{{ $siswa->kelas->nama_kelas }}</strong> |
                        Tahun Ajaran: <strong>{{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>
                    </p>
                </div>
            </div>

            <form method="GET" action="">
                <select name="semester" class="form-select border-0 shadow-sm bg-light fw-bold text-primary" style="min-width: 160px;" onchange="this.form.submit()">
                    <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </form>
        </div>
    </div>

    @forelse($nilaiList as $nilai)
    <div class="subject-card">
        <div class="subject-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold text-primary mb-1">
                        <i class="fas fa-book-open me-2 small"></i> {{ $nilai->mataPelajaran->nama_mapel }}
                    </h5>
                    <div class="small text-muted fw-bold">
                        <i class="fas fa-user-tie me-1"></i> Pengampu: {{ $nilai->guru->nama_lengkap ?? '-' }}
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <!-- Final Score Hidden per User Request -->
                    <!-- 
                    <div class="d-inline-block final-score-box">
                        <div class="text-xs text-uppercase fw-bold opacity-75">Nilai Akhir</div>
                        <div class="final-score-val">{{ $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 1) : '-' }}</div>
                    </div>
                    -->
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <div class="mini-stat-card border-start-tugas shadow-sm">
                        <div class="mini-stat-label">Rata Tugas (10%)</div>
                        <div class="mini-stat-val text-primary">{{ $nilai->rata_tugas ? number_format($nilai->rata_tugas, 1) : '-' }}</div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="mini-stat-card border-start-tugas shadow-sm" style="border-left-color: #4e73df !important;">
                        <div class="mini-stat-label">Rata Latihan (10%)</div>
                        <div class="mini-stat-val text-info">{{ $nilai->rata_latihan ? number_format($nilai->rata_latihan, 1) : '-' }}</div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="mini-stat-card border-start-tugas shadow-sm" style="border-left-color: #36b9cc !important;">
                        <div class="mini-stat-label">Rata UH (20%)</div>
                        <div class="mini-stat-val text-info">{{ $nilai->rata_uh ? number_format($nilai->rata_uh, 1) : '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mini-stat-card border-start-uts shadow-sm">
                        <div class="mini-stat-label">Nilai PTS (30%)</div>
                        <div class="mini-stat-val text-warning">{{ $nilai->pts ? number_format($nilai->pts, 1) : '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="mini-stat-card border-start-uas shadow-sm">
                        <div class="mini-stat-label">Nilai PAS (30%)</div>
                        <div class="mini-stat-val text-danger">{{ $nilai->pas ? number_format($nilai->pas, 1) : '-' }}</div>
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
    <div class="card shadow-sm border-0 py-5 text-center" style="border-radius: 15px;">
        <div class="card-body">
            <i class="fas fa-chart-bar fa-4x text-muted mb-3" style="opacity: 0.2;"></i>
            <h5 class="text-muted fw-bold">Data penilaian belum diinput oleh guru</h5>
            <p class="small text-muted">Silakan hubungi wali kelas jika mata pelajaran belum muncul.</p>
        </div>
    </div>
    @endforelse

    <div class="card shadow-sm border-start-info mb-5" style="border-radius: 10px; background: #f0f9ff;">
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
</div>
@endsection
