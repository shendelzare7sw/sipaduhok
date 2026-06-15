@extends('layouts.sneat')

@section('title', 'Laporan Siswa Belum Lunas')
@section('page-title', 'Laporan Siswa Belum Lunas')
@section('page-subtitle', 'Daftar siswa dengan tagihan belum lunas')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/laporan/summary.css'])
@endsection

@section('content')
    <div class="report-page">
        <div class="dashboard-card mb-4">
            <div class="filter-area">
                <form action="{{ route('bendahara.laporan.belum-lunas') }}" method="GET" class="d-flex align-items-center flex-wrap gap-3 w-100 m-0">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label fw-bold small text-muted mb-0">KELAS</label>
                        <select name="kelas_id" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('bendahara.laporan.cetak-belum-lunas', request()->query()) }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-print me-1"></i> Cetak Laporan
                        </a>
                        <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">{{ $siswaList->count() }}</div>
                            <div class="stat-label">Siswa Belum Lunas</div>
                        </div>
                        <div class="stat-icon-wrapper stat-icon-danger">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span>Siswa yang menunggak</span>
                        <i class="fas fa-users opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">Rp {{ number_format($siswaList->sum('sisa_tagihan'), 0, ',', '.') }}</div>
                            <div class="stat-label">Total Sisa Tagihan</div>
                        </div>
                        <div class="stat-icon-wrapper stat-icon-warning">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span>Piutang berjalan</span>
                        <i class="fas fa-chart-line opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">Rp {{ number_format($siswaList->sum('total_tagihan'), 0, ',', '.') }}</div>
                            <div class="stat-label">Total Tagihan</div>
                        </div>
                        <div class="stat-icon-wrapper stat-icon-primary">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span>Target keseluruhan</span>
                        <i class="fas fa-bullseye opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="dashboard-card h-100">
                    <div class="stat-widget">
                        <div class="stat-details">
                            <div class="stat-value">Rp {{ number_format($siswaList->sum('total_bayar'), 0, ',', '.') }}</div>
                            <div class="stat-label">Total Terbayar</div>
                        </div>
                        <div class="stat-icon-wrapper stat-icon-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span>Sudah divalidasi</span>
                        <i class="fas fa-shield-alt opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card mb-4">
            <div class="card-header-clean">
                <h5 class="card-title-clean">
                    <i class="fas fa-list-ul card-title-icon"></i> Daftar Rincian Tunggakan
                </h5>
                <span class="badge bg-danger">{{ $siswaList->count() }} Data Ditemukan</span>
            </div>
            <div class="card-body p-0">
                @if($siswaList->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-clean mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">NISN</th>
                                    <th>Kelas</th>
                                    <th>Total Tagihan</th>
                                    <th>Sudah Bayar</th>
                                    <th>Sisa Tagihan</th>
                                    <th width="120">Persentase</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaList as $index => $siswa)
                                    <tr>
                                        <td class="text-center align-middle fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                            <small class="text-primary fw-bold">{{ $siswa->cabang->nama_cabang ?? '-' }}</small>
                                        </td>
                                        <td class="text-center align-middle">{{ $siswa->nisn }}</td>
                                        <td class="align-middle fw-bold text-gray-800">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                        <td class="align-middle fw-bold">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                                        <td class="align-middle text-success fw-bold">Rp {{ number_format($siswa->total_bayar, 0, ',', '.') }}</td>
                                        <td class="align-middle text-danger fw-bold">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td>
                                        <td class="align-middle">
                                            @php
                                                $persentase = $siswa->total_tagihan > 0 ? round(($siswa->total_bayar / $siswa->total_tagihan) * 100, 1) : 0;
                                                $progressClass = $persentase >= 75 ? 'progress-good' : ($persentase >= 50 ? 'progress-warning' : 'progress-danger');
                                            @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress-bar-custom w-100">
                                                    <div class="progress-fill {{ $progressClass }}" data-progress-width="{{ $persentase }}"></div>
                                                </div>
                                                <span class="small fw-bold progress-text {{ $progressClass }}">{{ $persentase }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-sm btn-info" title="Detail"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $siswa->id) }}" class="btn btn-sm btn-success" title="Riwayat"><i class="fas fa-history"></i></a>
                                                <a href="{{ route('bendahara.pembayaran.create', $siswa->id) }}" class="btn btn-sm btn-warning" title="Input Bayar"><i class="fas fa-plus"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-double fa-3x text-success opacity-25 mb-3"></i>
                        <h5 class="text-muted">Luar Biasa! Semua Tagihan Telah Lunas</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/bendahara/laporan/progress.js'])
@endsection
