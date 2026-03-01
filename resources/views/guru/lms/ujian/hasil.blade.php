@extends('layouts.lms-guru')

@php
    $isLatihan = request()->routeIs('guru.lms.latihan.*');
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
@endphp

@section('title', 'Hasil ' . $tipeLabel)
@section('page-title', 'Hasil ' . $tipeLabel . ': ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    @php
        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $backRoute = $isLatihan ? 'guru.lms.latihan.index' : 'guru.lms.ujian.index';
    @endphp

    <div class="mb-3">
        <a href="{{ route($backRoute, [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card-custom" style="border-left: 4px solid #165fac;">
                <div class="p-3 text-center">
                    <div class="fs-4 fw-bold text-primary">{{ $hasilUjian->count() }}</div>
                    <small class="text-muted">Total Peserta</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-custom" style="border-left: 4px solid #10b981;">
                <div class="p-3 text-center">
                    <div class="fs-4 fw-bold text-success">
                        {{ $hasilUjian->where('status', 'selesai')->count() }}
                    </div>
                    <small class="text-muted">Selesai</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-custom" style="border-left: 4px solid #f59e0b;">
                <div class="p-3 text-center">
                    <div class="fs-4 fw-bold text-warning">
                        {{ number_format($hasilUjian->where('status', 'selesai')->avg('nilai') ?? 0, 1) }}
                    </div>
                    <small class="text-muted">Nilai Rata-rata</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-custom" style="border-left: 4px solid #dc2626;">
                <div class="p-3 text-center">
                    <div class="fs-4 fw-bold text-danger">
                        {{ number_format($hasilUjian->where('status', 'selesai')->max('nilai') ?? 0, 1) }}
                    </div>
                    <small class="text-muted">Nilai Tertinggi</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-chart-bar me-2"></i>Hasil {{ $tipeLabel }} Siswa
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peringkat</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Waktu Mulai</th>
                        <th class="text-center">Waktu Selesai</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hasilUjian as $index => $hasil)
                    <tr>
                        <td class="text-center">
                            @if($index == 0 && $hasil->nilai)
                                <i class="fas fa-trophy text-warning fs-5"></i>
                            @elseif($index == 1 && $hasil->nilai)
                                <i class="fas fa-medal text-secondary fs-5"></i>
                            @elseif($index == 2 && $hasil->nilai)
                                <i class="fas fa-award text-danger fs-5"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td><strong>{{ $hasil->siswa->nama_lengkap }}</strong></td>
                        <td class="text-center">
                            {{ $hasil->waktu_mulai ? $hasil->waktu_mulai->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $hasil->waktu_selesai ? $hasil->waktu_selesai->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            @if($hasil->status == 'belum_mulai')
                                <span class="badge bg-secondary">Belum Mulai</span>
                            @elseif($hasil->status == 'sedang_mengerjakan')
                                <span class="badge bg-warning">Sedang Mengerjakan</span>
                            @elseif($hasil->status == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($hasil->status == 'dinilai')
                                <span class="badge bg-primary">Sudah Dinilai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($hasil->nilai !== null)
                                <strong class="fs-5 text-primary">{{ number_format($hasil->nilai, 1) }}/100</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array($hasil->status, ['selesai', 'dinilai']))
                                @php
                                    $isLatihan = request()->routeIs('guru.lms.latihan.*');
                                    $koreksiRoute = $isLatihan ? 'guru.lms.latihan.koreksi.show' : 'guru.lms.ujian.koreksi.show';
                                @endphp
                                <a href="{{ route($koreksiRoute, [$kelas->id, $mapel->id, $ujian->id, $hasil->id]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Koreksi
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada siswa yang mengerjakan {{ strtolower($tipeLabel) }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection