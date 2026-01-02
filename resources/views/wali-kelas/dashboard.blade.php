@extends('layouts.sneat')

@section('title', 'Dashboard Wali Kelas')
@section('page-title', 'Dashboard Wali Kelas')
@section('page-subtitle', 'Kelola kelas dan siswa Anda')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Custom Stats Cards */
    .stat-card-custom {
        border-radius: 15px;
        transition: transform 0.2s;
        border: none;
    }
    .stat-card-custom:hover {
        transform: translateY(-5px);
    }
    .icon-circle-lg {
        height: 50px;
        width: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: rgba(255,255,255,0.2);
    }
    .table thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e3e6f0;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">
    @if(isset($message))
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
        </div>
    @elseif(!$kelas)
        <div class="card shadow mb-4 border-start border-danger border-4">
            <div class="card-body py-5 text-center">
                <i class="fas fa-user-slash fa-4x text-gray-200 mb-3"></i>
                <h4 class="text-danger fw-bold">Akses Terbatas</h4>
                <p class="text-gray-600">Anda belum ditugaskan sebagai wali kelas. Silakan hubungi bagian Admin Kurikulum.</p>
            </div>
        </div>
    @else
        {{-- STATS CARDS --}}
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-primary text-white shadow stat-card-custom h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Total Siswa</div>
                                <div class="h2 fw-bold mb-0 text-white">{{ $totalSiswa }}</div>
                            </div>
                            <div class="icon-circle-lg"><i class="fas fa-graduation-cap"></i></div>
                        </div>
                        <div class="mt-2 small text-white">Kelas {{ $kelas->nama_kelas }}</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-success text-white shadow stat-card-custom h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Hadir Hari Ini</div>
                                <div class="h2 fw-bold mb-0 text-white">{{ $presensiStats['hadir'] }}</div>
                            </div>
                            <div class="icon-circle-lg"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="mt-2 small text-white">Siswa di sekolah</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-warning text-white shadow stat-card-custom h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Izin Pending</div>
                                <div class="h2 fw-bold mb-0 text-white">{{ $izinMenungguValidasi }}</div>
                            </div>
                            <div class="icon-circle-lg"><i class="fas fa-file-alt"></i></div>
                        </div>
                        <div class="mt-2 small text-white">Perlu validasi</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-info text-white shadow stat-card-custom h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Rapor Draft</div>
                                <div class="h2 fw-bold mb-0 text-white">{{ $raporBelumSelesai }}</div>
                            </div>
                            <div class="icon-circle-lg"><i class="fas fa-file"></i></div>
                        </div>
                        <div class="mt-2 small text-white">Siswa belum beres</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- KIRI: INFO KELAS & JADWAL --}}
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Detail Informasi Kelas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="text-xs fw-bold text-uppercase text-gray-500 mb-0">Nama Kelas</label>
                                <p class="h6 fw-bold text-gray-800">{{ $kelas->nama_kelas }}</p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-xs fw-bold text-uppercase text-gray-500 mb-0">Jenjang</label>
                                <p class="h6 fw-bold text-gray-800"><span class="badge bg-primary px-3">{{ strtoupper($kelas->jenjang) }}</span></p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-xs fw-bold text-uppercase text-gray-500 mb-0">Tahun Ajaran</label>
                                <p class="h6 fw-bold text-gray-800">{{ $kelas->tahunAjaran->nama_tahun_ajaran }}</p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="text-xs fw-bold text-uppercase text-gray-500 mb-0">Cabang PKBM</label>
                                <p class="h6 fw-bold text-gray-800">{{ $kelas->cabang->nama_cabang }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-calendar-day me-2 text-warning"></i>Jadwal Pelajaran Hari Ini</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Waktu</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru Pengajar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwalHariIni as $jadwal)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                            </td>
                                            <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                            <td><small class="fw-bold">{{ $jadwal->guru->nama_lengkap }}</small></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small">Tidak ada jadwal pelajaran hari ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: AKSI CEPAT & STATS PRESENSI --}}
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('wali.presensi.index') }}" class="btn btn-primary w-100 text-start mb-2">
                            <i class="fas fa-clipboard-check me-2"></i> Input Presensi
                        </a>
                        <a href="{{ route('wali.presensi.validasi-izin') }}" class="btn btn-warning w-100 text-start mb-2 text-white fw-bold">
                            <i class="fas fa-check me-2"></i> Validasi Izin ({{ $izinMenungguValidasi }})
                        </a>
                        <a href="{{ route('wali.nilai.index') }}" class="btn btn-info w-100 text-start mb-2">
                            <i class="fas fa-chart-line me-2"></i> Lihat Nilai
                        </a>
                        <a href="{{ route('wali.rapor.index') }}" class="btn btn-success w-100 text-start">
                            <i class="fas fa-file-alt me-2"></i> Kelola Rapor
                        </a>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Rekap Absensi Hari Ini</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <div class="h4 fw-bold text-success mb-0">{{ $presensiStats['hadir'] }}</div>
                                    <div class="text-xs text-uppercase fw-bold">Hadir</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <div class="h4 fw-bold text-warning mb-0">{{ $presensiStats['sakit'] }}</div>
                                    <div class="text-xs text-uppercase fw-bold">Sakit</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <div class="h4 fw-bold text-primary mb-0">{{ $presensiStats['izin'] }}</div>
                                    <div class="text-xs text-uppercase fw-bold">Izin</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <div class="h4 fw-bold text-danger mb-0">{{ $presensiStats['alpha'] }}</div>
                                    <div class="text-xs text-uppercase fw-bold">Alpha</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
@endsection