@extends('layouts.sneat')

@section('title', 'Detail Nilai Siswa')
@section('page-title', 'Detail Nilai Siswa')
@section('page-subtitle', 'Rincian pencapaian akademik per mata pelajaran')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* === 1. VIBRANT STAT CARDS === */
    .stat-card-vibrant {
        padding: 20px;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        color: white !important;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }
    .stat-card-vibrant:hover { transform: translateY(-5px); }
    .stat-card-vibrant .stat-number { font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 5px; }
    .stat-card-vibrant .stat-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
    .stat-card-vibrant .stat-icon-bg { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 50px; opacity: 0.2; }

    .bg-grad-blue   { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important; }
    .bg-grad-green  { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%) !important; }
    .bg-grad-orange { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%) !important; }
    .bg-grad-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; }

    /* === 2. STUDENT PROFILE HEADER === */
    .student-header-card {
        background: white;
        border-radius: 15px;
        border-left: 5px solid #4e73df;
    }
    .info-label { font-size: 11px; font-weight: 800; color: #b7b9cc; text-transform: uppercase; }
    .info-value { font-weight: 700; color: #4e73df; margin-bottom: 10px; }

    /* === 3. TABLE STYLING === */
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
    }
    .score-cell { font-weight: 700; font-family: 'Nunito', sans-serif; }
    
    @media print {
        .sidebar, .header, .btn, .no-print, .sticky-footer { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        body { background: white !important; padding: 0; }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- HEADER & NAVIGATION --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('wali.nilai.index') }}" class="btn btn-light btn-sm fw-bold shadow-sm border text-gray-700">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Nilai
        </a>
        <div class="btn-group shadow-sm">
            <button onclick="window.print()" class="btn btn-secondary btn-sm fw-bold">
                <i class="fas fa-print me-1"></i> Cetak Laporan
            </button>
            <a href="{{ route('wali.nilai.edit', $siswa->id) }}" class="btn btn-primary btn-sm fw-bold">
                <i class="fas fa-edit me-1"></i> Edit Semua Nilai
            </a>
        </div>
    </div>

    {{-- STUDENT INFO CARD --}}
    <div class="card student-header-card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto mb-3 mb-md-0">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 70px; height: 70px; font-size: 30px;">
                        {{ substr($siswa->nama_lengkap, 0, 1) }}
                    </div>
                </div>
                <div class="col">
                    <h4 class="fw-bold text-gray-900 mb-1">{{ $siswa->nama_lengkap }}</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-label">NIS / NISN</div>
                            <div class="info-value text-dark small">{{ $siswa->nis }} / {{ $siswa->nisn }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Kelas Aktif</div>
                            <div class="info-value text-dark small">{{ $kelas->nama_kelas }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Tahun Ajaran</div>
                            <div class="info-value text-dark small">{{ $kelas->tahunAjaran->nama_tahun_ajaran }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTICS GRID --}}
    <div class="row mb-4 no-print">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card-vibrant bg-grad-blue">
                <div class="stat-content">
                    <div class="stat-title">Rata-rata Nilai</div>
                    <div class="stat-number">{{ number_format($rataRataSiswa, 2) }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card-vibrant bg-grad-green">
                <div class="stat-content">
                    <div class="stat-title">Mapel Tuntas</div>
                    <div class="stat-number">{{ $jumlahTuntas }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card-vibrant bg-grad-orange">
                <div class="stat-content">
                    <div class="stat-title">Persentase Tuntas</div>
                    <div class="stat-number">{{ number_format($persentaseTuntas, 1) }}%</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-percentage"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card-vibrant bg-grad-purple">
                <div class="stat-content">
                    <div class="stat-title">Total Mapel</div>
                    <div class="stat-number">{{ $mataPelajaranList->count() }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-book"></i></div>
            </div>
        </div>
    </div>

    {{-- TABLE NILAI --}}
    <div class="card shadow mb-5">
        <div class="card-header py-3 bg-white border-bottom">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-table me-2"></i>Rincian Nilai Harian & Ujian</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">NO</th>
                            <th>MATA PELAJARAN</th>
                            <th class="text-center">TUGAS</th>
                            <th class="text-center">LATIHAN</th>
                            <th class="text-center">UH</th>
                            <th class="text-center">PTS</th>
                            <th class="text-center">PAS</th>
                            <th class="text-center">AKHIR</th>
                            <th class="text-center">PREDIKAT</th>
                            <th class="text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mataPelajaranList as $index => $mapel)
                            @php
                                $nilai = $nilaiData[$mapel->id] ?? null;
                                $nilaiAkhir = $nilai ? $nilai->nilai_akhir : null;
                                $predikat = $nilai ? $nilai->nilaiHuruf() : '-';
                                $status = ($nilaiAkhir && $nilaiAkhir >= 70) ? 'TUNTAS' : 'BELUM TUNTAS';
                                $statusClass = ($nilaiAkhir && $nilaiAkhir >= 70) ? 'bg-success' : 'bg-danger';

                                // Mapping warna predikat
                                $predikatBg = match($predikat) {
                                    'A', 'B' => 'background: #ebfbee; color: #2ecc71; border: 1px solid #b7ebc6;',
                                    'C' => 'background: #fff4e5; color: #e67e22; border: 1px solid #ffcc80;',
                                    default => 'background: #ffe5e5; color: #d63031; border: 1px solid #fab1a0;',
                                };
                            @endphp
                            <tr>
                                <td class="text-center align-middle fw-bold text-gray-600">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="fw-bold text-gray-900">{{ $mapel->nama_mapel }}</div>
                                    <div class="small text-muted text-uppercase">Wajib / Kelompok A</div>
                                </td>
                                <td class="text-center align-middle score-cell">
                                    {{ $nilai && $nilai->rata_tugas ? number_format($nilai->rata_tugas, 1) : '-' }}
                                </td>
                                <td class="text-center align-middle score-cell">
                                    {{ $nilai && $nilai->rata_latihan ? number_format($nilai->rata_latihan, 1) : '-' }}
                                </td>
                                <td class="text-center align-middle score-cell">
                                    {{ $nilai && $nilai->rata_uh ? number_format($nilai->rata_uh, 1) : '-' }}
                                </td>
                                <td class="text-center align-middle score-cell">
                                    {{ $nilai && $nilai->pts ? number_format($nilai->pts, 1) : '-' }}
                                </td>
                                <td class="text-center align-middle score-cell">
                                    {{ $nilai && $nilai->pas ? number_format($nilai->pas, 1) : '-' }}
                                </td>
                                <td class="text-center align-middle">
                                    @if($nilaiAkhir)
                                        <div class="h6 mb-0 fw-bold text-primary">{{ number_format($nilaiAkhir, 1) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($nilai)
                                        <span class="badge px-3 py-1 fw-bold" style="{{ $predikatBg }}">
                                            {{ $predikat }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($nilaiAkhir)
                                        <span class="badge {{ $statusClass }} shadow-sm px-3 py-1 fw-bold" style="font-size: 10px;">
                                            {{ $status }}
                                        </span>
                                    @else
                                        <span class="badge bg-light border text-muted">BELUM ADA</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-5 text-muted fst-italic">Belum ada data mata pelajaran</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- INFORMATION BOX --}}
        @if($nilaiData->count() > 0)
            <div class="card-footer bg-light border-top py-3">
                <div class="row align-items-center">
                    <div class="col-md-1 text-center d-none d-md-block">
                        <i class="fas fa-info-circle fa-2x text-info opacity-50"></i>
                    </div>
                    <div class="col-md-11">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Metode Perhitungan Nilai Akhir</div>
                        <p class="mb-0 small text-gray-700">
                            Sistem menghitung Nilai Akhir secara otomatis berdasarkan komposisi bobot: 
                            <strong>Tugas (15%)</strong>, <strong>Latihan (15%)</strong>, <strong>UH (20%)</strong>, <strong>PTS (20%)</strong>, dan <strong>PAS (30%)</strong>. 
                            Ambang batas ketuntasan minimal (KKM) ditetapkan sebesar <strong>70.00</strong>.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
</div>
@endsection