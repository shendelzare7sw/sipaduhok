@extends('layouts.sneat')

@section('title', 'Detail Mata Pelajaran')
@section('page-title', 'Detail Mata Pelajaran')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        @media (max-width: 767.98px) {
            .table-card-mobile thead { display: none; }
            .table-card-mobile tbody tr {
                display: block; background: #fff; border-radius: 10px;
                box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 14px; margin-bottom: 10px;
            }
            .table-card-mobile tbody td {
                display: flex; justify-content: space-between; align-items: center;
                padding: 6px 0; border: none; font-size: 13px;
            }
            .table-card-mobile tbody td::before {
                content: attr(data-label); font-weight: 600; color: #6b7280; margin-right: 12px; white-space: nowrap;
            }
        }
    </style>
<div class="row">
    <div class="col-xl-4">
        {{-- Info Card --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0" style="color: white;">
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
                    <a href="{{ route('admin.mata-pelajaran.edit', $mataPelajaran) }}"
                       class="btn btn-warning btn-sm flex-fill">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.mata-pelajaran.index') }}"
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
                    <table class="table table-hover table-card-mobile">
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
                                <td data-label="No">{{ $index + 1 }}</td>
                                <td data-label="Kelas">
                                    <strong>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $jadwal->kelas->map(fn($k) => $k->cabang->nama_cabang ?? '-')->unique()->join(', ') }}</small>
                                </td>
                                <td data-label="Hari">
                                    <span class="badge bg-primary">{{ $jadwal->hari }}</span>
                                </td>
                                <td data-label="Waktu">
                                    <small class="font-monospace">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </small>
                                </td>
                                <td data-label="Guru">
                                    @if($jadwal->guru)
                                        <i class="fas fa-user text-primary me-1"></i>
                                        {{ $jadwal->guru->nama_lengkap }}
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td data-label="Status">
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
