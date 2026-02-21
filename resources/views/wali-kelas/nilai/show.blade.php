@extends('layouts.sneat')

@section('title', 'Detail Nilai - ' . $siswa->nama_lengkap)
@section('page-title', 'Detail Nilai Siswa')
@section('page-subtitle', $siswa->nama_lengkap . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .student-card { border: none !important; box-shadow: none !important; }
        body { font-size: 10pt; }
        .table { font-size: 9pt; }
        /* Hide Sneat layout chrome */
        .layout-navbar,
        .layout-menu,
        .layout-overlay,
        .menu-vertical,
        header.navbar,
        .content-backdrop,
        .navbar,
        aside { display: none !important; }
        /* Reset layout containers */
        .layout-wrapper,
        .layout-container,
        .layout-page,
        .content-wrapper {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
    }
    .student-card {
        background: white;
        border-radius: 20px;
        border-left: 6px solid #4e73df;
    }
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }
    .predikat-badge {
        font-size: 13px;
        font-weight: 700;
        padding: 4px 10px;
    }
    .rata-cell {
        background: #e9ecef !important;
        font-weight: 700;
        color: #165fac;
    }
    .nilai-akhir-cell {
        background: #d4edda !important;
        font-weight: 800;
        color: #155724;
    }
    .th-tugas { background: #e3f2fd !important; }
    .th-latihan { background: #fff3e0 !important; }
    .th-uh { background: #fce4ec !important; }
</style>
@endsection

@section('content')
@php
    $isKelasAkhir = str_contains(strtolower($kelas->nama_kelas), '9') || 
                    str_contains(strtolower($kelas->nama_kelas), '12') ||
                    str_contains(strtolower($kelas->nama_kelas), 'ix') ||
                    str_contains(strtolower($kelas->nama_kelas), 'xii');
@endphp
<div style="max-width: 1600px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">
    
    {{-- Buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('wali.nilai.index') }}" class="btn btn-light btn-sm fw-bold shadow-sm border text-gray-700">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('wali.nilai.edit', $siswa->id) }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="fas fa-edit me-1"></i> Edit Nilai
            </a>
            <a href="{{ route('wali.nilai.print-siswa', [$siswa->id, 'semester' => request('semester', '')]) }}" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fas fa-print me-1"></i> Cetak
            </a>
        </div>
    </div>

    {{-- Student Info Card --}}
    <div class="card student-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-gray-900 mb-3">
                <i class="fas fa-user-graduate text-primary me-2"></i>{{ $siswa->nama_lengkap }}
            </h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">NIS</div>
                    <div class="fw-bold text-dark">{{ $siswa->nis }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">NISN</div>
                    <div class="fw-bold text-dark">{{ $siswa->nisn }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Kelas</div>
                    <div class="fw-bold text-dark">{{ $kelas->nama_kelas }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Tahun Ajaran</div>
                    <div class="fw-bold text-dark">{{ $kelas->tahunAjaran->nama_tahun_ajaran }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nilai Table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-chart-line me-2"></i>Rekap Nilai Per Mata Pelajaran
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center" style="width: 40px;">No</th>
                            <th rowspan="2" style="min-width: 180px;">Mata Pelajaran</th>
                            <th colspan="2" class="text-center th-tugas">Tugas</th>
                            <th colspan="2" class="text-center th-latihan">Latihan</th>
                            <th colspan="2" class="text-center th-uh">UH</th>
                            <th rowspan="2" class="text-center" style="width: 55px;">PTS</th>
                            <th rowspan="2" class="text-center" style="width: 55px;">PAS</th>
                            <th rowspan="2" class="text-center nilai-akhir-cell" style="width: 65px;">N. Akhir</th>
                            <th rowspan="2" class="text-center" style="width: 70px;">Predikat</th>
                            <th rowspan="2" class="text-center" style="width: 80px;">Status</th>
                        </tr>
                        <tr>
                            <th class="text-center th-tugas" style="width: 50px;">Jml</th>
                            <th class="text-center th-tugas rata-cell" style="width: 50px;">Rata</th>
                            <th class="text-center th-latihan" style="width: 50px;">Jml</th>
                            <th class="text-center th-latihan rata-cell" style="width: 50px;">Rata</th>
                            <th class="text-center th-uh" style="width: 50px;">Jml</th>
                            <th class="text-center th-uh rata-cell" style="width: 50px;">Rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($mataPelajaranList as $mapel)
                            @php
                                $nilai = $nilaiData[$mapel->id] ?? null;
                                
                                // Count filled tugas
                                $tugasCount = 0;
                                $latihanCount = 0;
                                $uhCount = 0;
                                if ($nilai) {
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($nilai->{'tugas_'.$i} !== null) $tugasCount++;
                                        if ($nilai->{'latihan_'.$i} !== null) $latihanCount++;
                                        if ($nilai->{'uh_'.$i} !== null) $uhCount++;
                                    }
                                }
                                
                                // Determine status based on nilai_akhir and KKM (default 70)
                                $kkm = 70;
                                $isTuntas = $nilai && $nilai->nilai_akhir >= $kkm;
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $no++ }}</td>
                                <td>
                                    <div class="fw-bold">{{ $mapel->nama_mapel }}</div>
                                    <small class="text-muted">{{ $mapel->kode_mapel }}</small>
                                </td>
                                <td class="text-center">{{ $tugasCount }}/5</td>
                                <td class="text-center rata-cell">
                                    {{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}
                                </td>
                                <td class="text-center">{{ $latihanCount }}/5</td>
                                <td class="text-center rata-cell">
                                    {{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}
                                </td>
                                <td class="text-center">{{ $uhCount }}/5</td>
                                <td class="text-center rata-cell">
                                    {{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->pts !== null ? number_format($nilai->pts, 0) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->pas !== null ? number_format($nilai->pas, 0) : '-' }}
                                </td>
                                <td class="text-center nilai-akhir-cell">
                                    {{ $nilai && $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($nilai && $nilai->nilai_akhir !== null)
                                        <span class="badge predikat-badge {{ $nilai->predikat() == 'A' ? 'bg-success' : ($nilai->predikat() == 'B' ? 'bg-primary' : ($nilai->predikat() == 'C' ? 'bg-warning text-dark' : 'bg-danger')) }}">
                                            {{ $nilai->predikat() }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($nilai && $nilai->nilai_akhir !== null)
                                        @if($isTuntas)
                                            <span class="badge bg-success">Tuntas</span>
                                        @else
                                            <span class="badge bg-danger">Belum Tuntas</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Belum Ada</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tingkat Akhir Section --}}
    @if($isKelasAkhir)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 fw-bold text-success">
                <i class="fas fa-graduation-cap me-2"></i>Penilaian Tingkat Akhir (TO, UPK, Ujian Praktek)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">No</th>
                            <th style="min-width: 180px;">Mata Pelajaran</th>
                            <th class="text-center" style="width: 70px;">TO 1</th>
                            <th class="text-center" style="width: 70px;">TO 2</th>
                            <th class="text-center" style="width: 70px;">TO 3</th>
                            <th class="text-center" style="width: 70px;">UPK</th>
                            <th class="text-center" style="width: 100px;">Ujian Praktek</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($mataPelajaranList as $mapel)
                            @php
                                $nilai = $nilaiData[$mapel->id] ?? null;
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $no++ }}</td>
                                <td>
                                    <div class="fw-bold">{{ $mapel->nama_mapel }}</div>
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->to_1 !== null ? number_format($nilai->to_1, 0) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->to_2 !== null ? number_format($nilai->to_2, 0) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->to_3 !== null ? number_format($nilai->to_3, 0) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->upk !== null ? number_format($nilai->upk, 0) : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $nilai && $nilai->ujian_praktek !== null ? number_format($nilai->ujian_praktek, 0) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Info Box --}}
    <div class="alert alert-info border-0 mb-4">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle fa-2x text-info opacity-50 me-3"></i>
            <div>
                <div class="fw-bold text-info text-uppercase small mb-1">Keterangan</div>
                <ul class="mb-0 small text-gray-700">
                    <li><strong>Jml</strong>: Jumlah nilai yang sudah terisi dari maksimal 5 nilai</li>
                    <li><strong>Rata</strong>: Rata-rata dihitung hanya dari nilai yang terisi (kolom kosong tidak dihitung sebagai 0)</li>
                    <li><strong>N. Akhir</strong>: Formula = ((Rata Tugas × 1) + (Rata Latihan × 1) + (Rata UH × 2) + (PTS × 3) + (PAS × 3)) / 10</li>
                    <li><strong>KKM</strong>: 70 (nilai minimal untuk dinyatakan Tuntas)</li>
                </ul>
            </div>
        </div>
    </div>

</div>
</div>
@endsection