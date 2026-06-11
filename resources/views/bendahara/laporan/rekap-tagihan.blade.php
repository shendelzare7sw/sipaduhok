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
            <div class="card shadow mb-4 bg-gradient-blue border-0">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col text-white">
                            <h5 class="m-0 fw-bold">Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</h5>
                            <p class="small mb-0 opacity-75">
                                Periode:
                                {{ $tahunAjaran ? $tahunAjaran->tanggal_mulai->format('d/m/Y') . ' - ' . $tahunAjaran->tanggal_selesai->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('bendahara.laporan.cetak-rekap-tagihan') }}" class="btn btn-light fw-bold px-4 shadow-sm btn-blue-text" target="_blank">
                                <i class="fas fa-print me-1"></i> Cetak Rekap
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stat-card bg-gradient-blue">
                        <div class="stat-content">
                            <div class="stat-title">Total Tagihan</div>
                            <div class="stat-number">Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</div>
                            <div class="stat-label-sub">Seluruh Kelas</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-file-invoice"></i></div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stat-card bg-gradient-green">
                        <div class="stat-content">
                            <div class="stat-title">Total Terbayar</div>
                            <div class="stat-number">Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</div>
                            <div class="stat-label-sub">Dana Masuk</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stat-card bg-gradient-red">
                        <div class="stat-content">
                            <div class="stat-title">Total Sisa</div>
                            <div class="stat-number">Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</div>
                            <div class="stat-label-sub">Belum Dibayar</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-wallet"></i></div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stat-card bg-gradient-purple">
                        <div class="stat-content">
                            <div class="stat-title">Persentase Lunas</div>
                            <div class="stat-number">
                                {{ $grandTotal['tagihan'] > 0 ? round(($grandTotal['bayar'] / $grandTotal['tagihan']) * 100, 1) : 0 }}%
                            </div>
                            <div class="stat-label-sub">Rata-rata Kelas</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-chart-pie"></i></div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-list-alt me-2"></i>Rincian Pembayaran Per Kelas
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Pembayaran Per Jenjang
                    </h6>
                </div>
                <div class="card-body">
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
