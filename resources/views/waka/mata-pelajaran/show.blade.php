@extends('layouts.sneat')

@section('title', 'Detail Mata Pelajaran')
@section('page-title', 'Detail Mata Pelajaran')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="row">
    <div class="col-xl-4">
        {{-- Info Card --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>Informasi Mata Pelajaran
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama:</th>
                        <td><strong>{{ $mataPelajaran->nama_mapel }}</strong></td>
                    </tr>
                    <tr>
                        <th>Kode:</th>
                        <td>
                            @if($mataPelajaran->kode_mapel)
                                <span class="badge bg-secondary">{{ $mataPelajaran->kode_mapel }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jenjang:</th>
                        <td>
                            @if($mataPelajaran->jenjang == 'KB')
                                <span class="badge bg-secondary">KB</span>
                            @elseif($mataPelajaran->jenjang == 'TKA')
                                <span class="badge bg-dark">TKA</span>
                            @elseif($mataPelajaran->jenjang == 'TKB')
                                <span class="badge bg-danger">TKB</span>
                            @elseif($mataPelajaran->jenjang == 'SD')
                                <span class="badge bg-success">SD</span>
                            @elseif($mataPelajaran->jenjang == 'SMP')
                                <span class="badge bg-info">SMP</span>
                            @elseif($mataPelajaran->jenjang == 'SMA')
                                <span class="badge bg-warning">SMA</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Kelompok:</th>
                        <td>
                            @if($mataPelajaran->kelompok)
                                <span class="badge {{ $mataPelajaran->kelompok === 'A' ? 'bg-primary' : 'bg-success' }}">
                                    Kelompok {{ $mataPelajaran->kelompok }}
                                </span>
                                <span class="text-muted">
                                    @if($mataPelajaran->kelompok === 'A')
                                        (Mata Pelajaran Umum)
                                    @else
                                        (Mata Pelajaran Pilihan/Muatan Lokal)
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">Belum ditentukan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi:</th>
                        <td>{{ $mataPelajaran->deskripsi ?? '-' }}</td>
                    </tr>
                </table>

                <hr>

                <div class="d-flex gap-2">
                    <a href="{{ route('waka.mata-pelajaran.edit', $mataPelajaran) }}"
                       class="btn btn-warning btn-sm flex-fill">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('waka.mata-pelajaran.index') }}"
                       class="btn btn-secondary btn-sm flex-fill">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics Card --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Statistik Penggunaan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Total Jadwal:</span>
                        <strong>{{ $stats['totalJadwal'] }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Kelas Menggunakan:</span>
                        <strong>{{ $stats['totalKelas'] }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['totalKelas'] > 0 ? 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Guru Mengajar:</span>
                        <strong>{{ $stats['totalGuru'] }}</strong>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: {{ $stats['totalGuru'] > 0 ? 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        {{-- Jadwal Pelajaran Using This Subject --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-week text-primary me-2"></i>Jadwal Menggunakan Mata Pelajaran Ini
                </h5>
            </div>
            <div class="card-body">
                @if($mataPelajaran->jadwalPelajaran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Guru Pengajar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mataPelajaran->jadwalPelajaran->sortBy('hari') as $index => $jadwal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $jadwal->kelas->nama_kelas }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $jadwal->kelas->cabang->nama_cabang }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $jadwal->hari }}</span>
                                </td>
                                <td>
                                    <small class="font-monospace">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </small>
                                </td>
                                <td>
                                    @if($jadwal->guru)
                                        <i class="fas fa-user text-primary me-1"></i>
                                        {{ $jadwal->guru->nama_lengkap }}
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($jadwal->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($jadwal->status == 'kosong')
                                        <span class="badge bg-warning">Kosong</span>
                                    @else
                                        <span class="badge bg-info">Diganti</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada jadwal yang menggunakan mata pelajaran ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
