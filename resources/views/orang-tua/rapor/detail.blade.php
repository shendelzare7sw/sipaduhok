@extends('layouts.sneat')

@section('title', 'Detail Rapor')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.rapor.anak', $rapor->siswa_id) }}">Rapor</a></li>
                    <li class="breadcrumb-item active">Detail Rapor</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Detail Rapor - Semester {{ $rapor->semester }}</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $rapor->siswa->nama_lengkap }}
            </p>
        </div>
        <div>
            <a href="{{ route('orang-tua.rapor.anak', $rapor->siswa_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Rapor Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h5 class="mb-3">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Rapor Semester {{ $rapor->semester }}
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-muted">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Nama Siswa</small>
                                    <strong>{{ $rapor->siswa->nama_lengkap }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-muted">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">NISN</small>
                                    <strong>{{ $rapor->siswa->nisn }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-muted">
                                    <i class="fas fa-school"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Kelas</small>
                                    <strong>{{ $rapor->siswa->kelas->nama_kelas ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-muted">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Tahun Ajaran</small>
                                    <strong>{{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-center">
                    @if($rapor->raporNilai->count() > 0)
                        <div class="p-4 bg-light rounded">
                            <small class="text-muted d-block mb-2">Nilai Rata-rata</small>
                            @php
                                $avg = $rapor->raporNilai->avg('nilai_angka');
                                $colorClass = $avg >= 85 ? 'text-success' : ($avg >= 70 ? 'text-primary' : ($avg >= 60 ? 'text-warning' : 'text-danger'));
                                $predikat = $avg >= 90 ? 'A' : ($avg >= 75 ? 'B' : ($avg >= 60 ? 'C' : 'D'));
                            @endphp
                            <h1 class="mb-0 {{ $colorClass }} fw-bold">{{ number_format($avg, 2) }}</h1>
                            <span class="badge bg-label-{{ $avg >= 85 ? 'success' : ($avg >= 70 ? 'primary' : ($avg >= 60 ? 'warning' : 'danger')) }} mt-2">
                                Predikat {{ $predikat }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Per Mata Pelajaran -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Nilai Per Mata Pelajaran
            </h5>
        </div>
        <div class="card-body">
            @if($rapor->raporNilai->isEmpty())
                <div class="alert alert-info d-flex align-items-center mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>Belum ada nilai yang diinput untuk rapor ini.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="40">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Predikat</th>
                                <th>Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rapor->raporNilai as $index => $nilai)
                                @php
                                    $nilaiAkhir = $nilai->nilai_angka;
                                    if ($nilaiAkhir >= 90) {
                                        $predikat = 'A';
                                        $badgeClass = 'bg-success';
                                    } elseif ($nilaiAkhir >= 75) {
                                        $predikat = 'B';
                                        $badgeClass = 'bg-primary';
                                    } elseif ($nilaiAkhir >= 60) {
                                        $predikat = 'C';
                                        $badgeClass = 'bg-warning';
                                    } else {
                                        $predikat = 'D';
                                        $badgeClass = 'bg-danger';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $nilai->mataPelajaran->nama_mapel ?? '-' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <strong class="fs-5">{{ number_format($nilaiAkhir, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">{{ $predikat }}</span>
                                    </td>
                                    <td>
                                        @if($nilai->deskripsi)
                                            <small class="text-muted">{{ $nilai->deskripsi }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold">Rata-rata:</td>
                                <td class="text-center">
                                    <strong class="fs-5 text-primary">{{ number_format($rapor->raporNilai->avg('nilai_angka'), 2) }}</strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Catatan Wali Kelas -->
    @if($rapor->catatan_wali_kelas)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-comment-alt me-2 text-primary"></i>
                    Catatan Wali Kelas
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border-start border-primary border-4 mb-0">
                    <p class="mb-0">{{ $rapor->catatan_wali_kelas }}</p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
