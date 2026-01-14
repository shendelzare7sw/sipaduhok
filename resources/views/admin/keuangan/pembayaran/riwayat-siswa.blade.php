@extends('layouts.sneat')

@section('title', 'Riwayat Pembayaran Siswa')
@section('page-title', 'Riwayat Pembayaran')
@section('page-subtitle')
    Riwayat pembayaran {{ $siswa->nama_lengkap }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        .stat-card-mini {
            border-radius: 12px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card-mini h6 {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .stat-card-mini .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-card-mini .stat-label {
            font-size: 11px;
            opacity: 0.8;
        }

        .stat-card-mini .stat-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 50px;
            opacity: 0.2;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            position: absolute;
            left: -30px;
            top: 4px;
        }

        .timeline-line {
            position: absolute;
            left: -25px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">

            {{-- Header Card --}}
            <div class="card shadow mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold text-primary">Riwayat Pembayaran</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.keuangan.pembayaran.create', $siswa->id) }}"
                                class="btn btn-success shadow-sm">
                                <i class="fas fa-plus me-1"></i> Input Pembayaran
                            </a>
                            <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}"
                                class="btn btn-secondary shadow-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Siswa Card --}}
            <div class="card shadow mb-4 border-start border-primary border-4">
                <div class="card-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <small class="text-muted fw-bold">Nama Lengkap</small>
                            <p class="mb-0 fw-bold">{{ $siswa->nama_lengkap }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted fw-bold">NISN</small>
                            <p class="mb-0 fw-bold">{{ $siswa->nisn }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted fw-bold">Kelas</small>
                            <p class="mb-0 fw-bold">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted fw-bold">Cabang</small>
                            <p class="mb-0 fw-bold">{{ $siswa->cabang->nama_cabang ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Cards --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini shadow" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <h6>Total Tagihan</h6>
                        <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                        <div class="stat-label">Seluruh tagihan</div>
                        <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini shadow" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <h6>Total Terbayar</h6>
                        <div class="stat-value">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                        <div class="stat-label">Pembayaran disetujui</div>
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini shadow" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <h6>Menunggu Validasi</h6>
                        <div class="stat-value">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
                        <div class="stat-label">Belum divalidasi</div>
                        <div class="stat-icon">⏳</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini shadow" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <h6>Sisa Tagihan</h6>
                        <div class="stat-value">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</div>
                        <div class="stat-label">Belum dibayar</div>
                        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
            </div>

            {{-- Daftar Pembayaran Table --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-receipt me-2"></i>Daftar Transaksi Pembayaran
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($pembayaran->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Tagihan</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-center">Metode</th>
                                        <th class="text-center">Status</th>
                                        <th>Divalidasi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembayaran as $bayar)
                                        <tr>
                                            <td class="align-middle">
                                                <code class="small">{{ $bayar->kode_pembayaran }}</code>
                                            </td>
                                            <td class="align-middle">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                                            <td class="align-middle">
                                                @if($bayar->tagihan)
                                                    <strong>{{ $jenisTagihan[$bayar->tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-end fw-bold">Rp
                                                {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td class="align-middle text-center">
                                                @if($bayar->metode_pembayaran === 'tunai')
                                                    <span class="badge bg-info"><i class="fas fa-money-bill"></i> Tunai</span>
                                                @elseif($bayar->metode_pembayaran === 'transfer')
                                                    <span class="badge bg-warning"><i class="fas fa-university"></i> Transfer</span>
                                                @else
                                                    <span class="badge bg-success"><i class="fas fa-credit-card"></i> Midtrans</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($bayar->status_validasi === 'disetujui')
                                                    <span class="badge bg-success shadow-sm"><i class="fas fa-check-circle"></i>
                                                        Disetujui</span>
                                                @elseif($bayar->status_validasi === 'ditolak')
                                                    <span class="badge bg-danger shadow-sm"><i class="fas fa-times-circle"></i>
                                                        Ditolak</span>
                                                @else
                                                    <span class="badge bg-warning shadow-sm">⏳ Pending</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($bayar->tanggal_validasi)
                                                    <small>
                                                        {{ $bayar->tanggal_validasi->format('d/m/Y H:i') }}<br>
                                                        <span class="text-muted">oleh {{ $bayar->validator->name ?? '-' }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.keuangan.pembayaran.show', $bayar->id) }}"
                                                    class="btn btn-sm btn-info shadow-sm" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-light py-3 border-top">
                            <div class="d-flex justify-content-center">
                                {{ $pembayaran->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-4x mb-3 opacity-50"></i>
                            <p class="mb-0">Belum ada riwayat pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline Visual --}}
            @if($pembayaran->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 fw-bold text-success">
                            <i class="fas fa-clock me-2"></i>Timeline Pembayaran (10 Terbaru)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="position-relative" style="padding-left: 30px;">
                            @foreach($pembayaran->take(10) as $bayar)
                                <div class="position-relative pb-4">
                                    {{-- Line --}}
                                    <div class="timeline-line"></div>

                                    {{-- Dot --}}
                                    <div
                                        class="timeline-dot {{ $bayar->status_validasi === 'disetujui' ? 'bg-success' : ($bayar->status_validasi === 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                                    </div>

                                    {{-- Content --}}
                                    <div class="bg-light p-3 rounded shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="text-dark">Rp
                                                    {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</strong>
                                                <span class="text-muted ms-2">
                                                    via {{ ucfirst($bayar->metode_pembayaran) }}
                                                </span>
                                            </div>
                                            <small class="text-muted">{{ $bayar->tanggal_bayar->format('d M Y') }}</small>
                                        </div>
                                        @if($bayar->tagihan)
                                            <small class="text-muted">
                                                {{ $jenisTagihan[$bayar->tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection