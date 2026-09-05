@extends('layouts.app')

@section('title', 'Status Akses Siswa')
@section('page-title', 'Status Akses')
@section('page-subtitle', isset($kelas) && $kelas ? 'Status akses ujian dan rapor siswa kelas ' . $kelas->nama_kelas : 'Status akses siswa')


@section('styles')
    @vite(['resources/css/wali-kelas/validasi-akses/index.css', 'resources/js/wali-kelas/validasi-akses/index.js'])
@endsection

@section('content')
<div class="wk-page">
<div class="container-fluid px-0">
    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @else
        {{-- STATISTICS --}}
        <div class="row mb-4 wk-access-stats">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-success border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Ujian Tervalidasi</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['ujianValid'] ?? 0 }}</div>
                                <div class="text-xs text-muted">dari {{ $siswaList->count() }} siswa</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-double fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-info border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Rapor Tervalidasi</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['raporValid'] ?? 0 }}</div>
                                <div class="text-xs text-muted">dari {{ $siswaList->count() }} siswa</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-file-contract fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-warning border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Belum Akses Ujian</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['ujianPending'] ?? 0 }}</div>
                                <div class="text-xs text-muted">menunggu validasi</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-hourglass-half fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card-custom border-start border-warning border-4 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Belum Akses Rapor</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['raporPending'] ?? 0 }}</div>
                                <div class="text-xs text-muted">menunggu validasi</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-hourglass fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card shadow mb-4">
            <div class="card-body py-3">
                <form action="{{ route('wali.validasi-akses.index') }}" method="GET" class="row align-items-end">
                    <div class="col-md-5 mb-2">
                        <label class="form-label small fw-bold">CARI SISWA</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Nama siswa atau NIS..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small fw-bold">FILTER STATUS</label>
                        <select name="filter" class="form-select form-select-sm border-start border-primary border-3 shadow-sm" data-auto-submit>
                            <option value="">Semua Siswa</option>
                            <option value="ujian_pending" {{ $filterStatus == 'ujian_pending' ? 'selected' : '' }}>Ujian: Belum Akses</option>
                            <option value="ujian_selesai" {{ $filterStatus == 'ujian_selesai' ? 'selected' : '' }}>Ujian: Sudah Akses</option>
                            <option value="rapor_pending" {{ $filterStatus == 'rapor_pending' ? 'selected' : '' }}>Rapor: Belum Akses</option>
                            <option value="rapor_selesai" {{ $filterStatus == 'rapor_selesai' ? 'selected' : '' }}>Rapor: Sudah Akses</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2 mb-2 text-end">
                        <a href="{{ route('wali.validasi-akses.index') }}" class="btn btn-light btn-sm border px-3 fw-bold text-gray-700">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE LIST (READ-ONLY) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Status Akses Siswa</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-validasi wk-card-table mb-0">
                        <thead>
                            <tr>
                                <th width="50">NO</th>
                                <th width="120">NIS</th>
                                <th class="text-start">NAMA SISWA</th>
                                <th>STATUS AKSES UJIAN</th>
                                <th>STATUS AKSES RAPOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaList ?? [] as $index => $siswa)
                                <tr>
                                    <td class="text-center align-middle fw-bold">{{ $index + 1 }}</td>
                                    <td class="text-center align-middle">{{ $siswa->nis }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{-- Pakai status akses yang SEBENARNYA dialami siswa
                                             (sumber sama dengan gerbang ujian), bukan sekadar
                                             flag validasi_ujian_bendahara - dulu keduanya bisa
                                             berbeda sehingga layar ini menyesatkan. --}}
                                        @if($aksesUjian[$siswa->id] ?? false)
                                            <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Akses Terbuka</span>
                                        @else
                                            <span class="badge bg-light border text-muted px-3 py-2"><i class="fas fa-lock me-1"></i> Belum Ada Akses</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($siswa->validasi_rapor_bendahara)
                                            <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Akses Terbuka</span>
                                        @elseif($siswa->validasi_rapor_ketua)
                                            <span class="badge bg-secondary px-3 py-2"><i class="fas fa-clock me-1"></i> Tunggu Bendahara</span>
                                        @elseif($siswa->validasi_rapor_wali)
                                            <span class="badge bg-info text-white px-3 py-2"><i class="fas fa-paper-plane me-1"></i> Tunggu Ketua</span>
                                        @else
                                            <span class="badge bg-light border text-muted px-3 py-2"><i class="fas fa-lock me-1"></i> Belum Ada Akses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-gray-500 fst-italic">Tidak ada data siswa ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="card shadow-sm border-start border-info border-4 bg-light mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-info"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                <div class="small text-gray-800">
                    <p class="mb-1">Halaman ini menampilkan <strong>status akses</strong> siswa secara read-only. Validasi akses dilakukan otomatis berdasarkan status keuangan:</p>
                    <ul class="mb-0">
                        <li><strong>Ujian:</strong> Siswa yang lunas otomatis mendapat akses ujian. Siswa belum lunas perlu dispensasi dari Bendahara.</li>
                        <li><strong>Rapor:</strong> Mengikuti alur validasi: Wali Kirim &rarr; Ketua Approve &rarr; Cek Keuangan &rarr; Akses Terbuka.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
@endsection
