@extends('layouts.lms-guru')

@section('title', 'Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="card-custom mb-3">
        <div class="card-header-custom">
            <i class="fas fa-info-circle me-2"></i>Informasi
        </div>
        <div class="p-3">
            <div class="alert alert-info mb-0">
                <i class="fas fa-lightbulb me-2"></i>
                Nilai siswa akan otomatis terupdate dari hasil tugas dan ujian. Anda dapat melihat rekap nilai lengkap di halaman ini.
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-chart-line me-2"></i>Rekap Nilai Siswa
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">NISN</th>
                        <th class="text-center">Nilai Tugas</th>
                        <th class="text-center">Nilai UTS</th>
                        <th class="text-center">Nilai UAS</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilaiList as $index => $nilai)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $nilai->siswa->nama_lengkap }}</strong></td>
                        <td class="text-center">{{ $nilai->siswa->nisn }}</td>
                        <td class="text-center">
                            {{ $nilai->nilai_tugas ? number_format($nilai->nilai_tugas, 1) : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $nilai->nilai_uts ? number_format($nilai->nilai_uts, 1) : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $nilai->nilai_uas ? number_format($nilai->nilai_uas, 1) : '-' }}
                        </td>
                        <td class="text-center">
                            @if($nilai->nilai_akhir)
                                <strong class="text-primary fs-6">{{ number_format($nilai->nilai_akhir, 1) }}</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($nilai->nilai_akhir)
                                @if($nilai->nilai_akhir >= 75)
                                    <span class="badge bg-success">Tuntas</span>
                                @else
                                    <span class="badge bg-danger">Belum Tuntas</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada data nilai siswa
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(count($nilaiList) > 0)
        <div class="mt-4">
            <!-- Pagination can be added if needed -->
        </div>
    @endif
@endsection