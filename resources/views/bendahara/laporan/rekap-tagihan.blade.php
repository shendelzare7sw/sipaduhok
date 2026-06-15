@extends('layouts.sneat')

@section('title', 'Rekap Tagihan per Kelas')
@section('page-title', 'Rekap Tagihan per Kelas')
@section('page-subtitle', 'Rekap total tagihan dan pembayaran setiap kelas')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/laporan/summary.css'])
@endsection

@section('content')
    <div class="report-page">
        <div class="container-fluid px-0">
            <div class="dashboard-card mb-4">
                <div class="card-header-clean">
                    <div>
                        <h5 class="card-title-clean">
                            <i class="fas fa-calendar-alt card-title-icon"></i>
                            Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}
                        </h5>
                        <small class="text-muted">
                            Periode: {{ $tahunAjaran ? $tahunAjaran->tanggal_mulai->format('d/m/Y') . ' - ' . $tahunAjaran->tanggal_selesai->format('d/m/Y') : '-' }}
                        </small>
                    </div>
                    <div>
                        <a href="{{ route('bendahara.laporan.cetak-rekap-tagihan') }}" class="btn btn-primary btn-sm shadow-sm" target="_blank">
                            <i class="fas fa-print me-1"></i> Cetak Rekap
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="dashboard-card h-100">
                        <div class="stat-widget">
                            <div class="stat-details">
                                <div class="stat-value">Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</div>
                                <div class="stat-label">Total Tagihan</div>
                            </div>
                            <div class="stat-icon-wrapper stat-icon-primary">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span>Seluruh Kelas</span>
                            <i class="fas fa-globe opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card h-100">
                        <div class="stat-widget">
                            <div class="stat-details">
                                <div class="stat-value">Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</div>
                                <div class="stat-label">Total Terbayar</div>
                            </div>
                            <div class="stat-icon-wrapper stat-icon-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span>Dana Masuk</span>
                            <i class="fas fa-arrow-down opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card h-100">
                        <div class="stat-widget">
                            <div class="stat-details">
                                <div class="stat-value">Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</div>
                                <div class="stat-label">Total Sisa</div>
                            </div>
                            <div class="stat-icon-wrapper stat-icon-danger">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span>Belum Dibayar</span>
                            <i class="fas fa-exclamation-circle opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card h-100">
                        <div class="stat-widget">
                            <div class="stat-details">
                                <div class="stat-value">{{ $grandTotal['tagihan'] > 0 ? round(($grandTotal['bayar'] / $grandTotal['tagihan']) * 100, 1) : 0 }}%</div>
                                <div class="stat-label">Persentase Lunas</div>
                            </div>
                            <div class="stat-icon-wrapper stat-icon-purple">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                        </div>
                        <div class="stat-footer">
                            <span>Rata-rata Kelas</span>
                            <i class="fas fa-percentage opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-list-alt card-title-icon"></i> Rincian Pembayaran Per Kelas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-clean mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Kelas</th>
                                    <th>Jenjang</th>
                                    <th class="text-center">Siswa</th>
                                    <th>Total Tagihan</th>
                                    <th>Total Terbayar</th>
                                    <th>Sisa Tagihan</th>
                                    <th width="150">Persentase</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelasList as $index => $kelas)
                                    @php
                                        $progressClass = $kelas->persentase >= 75 ? 'progress-good' : ($kelas->persentase >= 50 ? 'progress-warning' : 'progress-danger');
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            <div class="fw-bold text-gray-900">{{ $kelas->nama_kelas }}</div>
                                            <small class="text-muted fw-bold">{{ $kelas->kode_kelas }}</small>
                                        </td>
                                        <td class="align-middle text-uppercase small fw-bold text-primary">{{ $kelas->jenjang }}</td>
                                        <td class="text-center align-middle fw-bold">{{ $kelas->total_siswa }}</td>
                                        <td class="align-middle currency-font text-dark">Rp {{ number_format($kelas->total_tagihan, 0, ',', '.') }}</td>
                                        <td class="align-middle currency-font text-success">Rp {{ number_format($kelas->total_bayar, 0, ',', '.') }}</td>
                                        <td class="align-middle currency-font {{ $kelas->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($kelas->sisa_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="progress-bar-container w-100 me-2">
                                                    <div class="progress-fill {{ $progressClass }}" data-progress-width="{{ $kelas->persentase }}"></div>
                                                </div>
                                                <span class="small fw-bold text-gray-700">{{ $kelas->persentase }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($kelas->sisa_tagihan <= 0)
                                                <span class="badge bg-success px-3 py-2 shadow-sm">LUNAS</span>
                                            @elseif($kelas->persentase >= 75)
                                                <span class="badge bg-warning px-3 py-2 shadow-sm text-white">HAMPIR LUNAS</span>
                                            @elseif($kelas->persentase >= 50)
                                                <span class="badge bg-info px-3 py-2 shadow-sm">DALAM PROSES</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2 shadow-sm">BELUM BAYAR</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-gray-500 fst-italic">
                                            <i class="fas fa-database fa-3x mb-3 text-gray-200"></i><br>Data kelas tidak tersedia
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light fw-bold text-dark border-top">
                                <tr>
                                    <td colspan="3" class="text-end py-3 pe-4">GRAND TOTAL:</td>
                                    <td class="text-center">{{ $kelasList->sum('total_siswa') }}</td>
                                    <td class="currency-font">Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</td>
                                    <td class="currency-font text-success">Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</td>
                                    <td class="currency-font text-danger">Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-chart-bar card-title-icon"></i> Statistik Pembayaran Per Jenjang
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php
                        $perJenjang = $kelasList->groupBy('jenjang')->map(function ($items) {
                            return [
                                'jumlah_kelas' => $items->count(),
                                'jumlah_siswa' => $items->sum('total_siswa'),
                                'total_tagihan' => $items->sum('total_tagihan'),
                                'total_bayar' => $items->sum('total_bayar'),
                                'sisa_tagihan' => $items->sum('sisa_tagihan'),
                            ];
                        });
                    @endphp

                    <div class="row">
                        @foreach($perJenjang as $jenjang => $data)
                            @php
                                $persenJenjang = $data['total_tagihan'] > 0 ? round(($data['total_bayar'] / $data['total_tagihan']) * 100, 1) : 0;
                                $progressClass = $persenJenjang >= 75 ? 'progress-good' : ($persenJenjang >= 50 ? 'progress-warning' : 'progress-danger');
                            @endphp
                            <div class="col-xl-4 col-md-6 mb-3">
                                <div class="card border-0 shadow-sm bg-light h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="fw-bold text-primary mb-0">{{ $jenjang }}</h5>
                                            <span class="badge bg-primary px-3">{{ $data['jumlah_kelas'] }} Kelas</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="h3 fw-bold text-gray-800">{{ $data['jumlah_siswa'] }}</span>
                                            <span class="small text-muted ms-1">Siswa Aktif</span>
                                        </div>
                                        <div class="border-top pt-3">
                                            <div class="d-flex justify-content-between small mb-1 text-gray-700">
                                                <span>Total Tagihan:</span>
                                                <span class="fw-bold">Rp {{ number_format($data['total_tagihan'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between small mb-1 text-success">
                                                <span>Dana Terbayar:</span>
                                                <span class="fw-bold">Rp {{ number_format($data['total_bayar'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between small mb-3 text-danger">
                                                <span>Sisa Tagihan:</span>
                                                <span class="fw-bold">Rp {{ number_format($data['sisa_tagihan'], 0, ',', '.') }}</span>
                                            </div>

                                            <div class="progress-bar-container shadow-sm mb-1">
                                                <div class="progress-fill {{ $progressClass }}" data-progress-width="{{ $persenJenjang }}"></div>
                                            </div>
                                            <div class="text-center small fw-bold text-gray-600">{{ $persenJenjang }}% Lunas</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/bendahara/laporan/progress.js'])
@endsection
