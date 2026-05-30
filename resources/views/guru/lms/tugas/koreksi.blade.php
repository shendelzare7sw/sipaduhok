@extends('layouts.lms-guru')

@section('title', 'Koreksi Tugas')
@section('page-title', 'Koreksi Tugas: ' . $tugas->judul_tugas)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Tugas
        </a>
    </div>

    <div class="card-custom mb-4">
        <div class="card-header-custom">
            <i class="fas fa-info-circle me-2"></i>Info Tugas
        </div>
        <div class="p-3">
            <div class="row">
                <div class="col-md-6">
                    <strong>Judul:</strong> {{ $tugas->judul_tugas }}<br>
                    <strong>Deadline:</strong> {{ $tugas->tanggal_deadline->format('d M Y') }}
                    @if($tugas->isOverdue())
                        <span class="badge bg-danger ms-2">Lewat Deadline</span>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block text-center me-3">
                        <div class="fs-4 fw-bold text-success">{{ $submitted }}</div>
                        <small class="text-muted">Sudah Dikumpulkan</small>
                    </div>
                    <div class="d-inline-block text-center">
                        <div class="fs-4 fw-bold text-warning">{{ $belumDinilai }}</div>
                        <small class="text-muted">Belum Dinilai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-list me-2"></i>Daftar Siswa & Jawaban
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Tanggal Kumpul</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSiswa as $index => $ts)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $ts->siswa->nama_lengkap }}</strong>
                            @if($ts->isLate())
                                <span class="badge bg-warning text-dark ms-1">Terlambat</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ts->tanggal_submit)
                                {{ $ts->tanggal_submit->format('d M Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ts->status == 'belum_dikerjakan')
                                <span class="badge bg-secondary">Belum Dikerjakan</span>
                            @elseif($ts->status == 'dikerjakan')
                                <span class="badge bg-warning">Perlu Koreksi</span>
                            @elseif($ts->status == 'dinilai')
                                <span class="badge bg-success">Sudah Dinilai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ts->nilai)
                                <strong class="text-primary">{{ number_format($ts->nilai, 0) }}</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ts->status != 'belum_dikerjakan')
                                <a href="{{ route('guru.lms.tugas.koreksi.show', [$kelas->id, $mapel->id, $tugas->id, $ts->id]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Lihat & Nilai
                                </a>
                            @else
                                <span class="text-muted small">Belum submit</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada siswa yang mengumpulkan tugas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
