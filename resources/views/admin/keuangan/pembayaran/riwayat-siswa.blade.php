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
    @vite(['resources/css/admin/keuangan/pembayaran/riwayat-siswa.css'])
@endsection

@section('content')
    <div class="payment-history-page">
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
                    <div class="stat-card-mini stat-card-total shadow">
                        <h6>Total Tagihan</h6>
                        <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                        <div class="stat-label">Seluruh tagihan</div>
                        <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini stat-card-paid shadow">
                        <h6>Total Terbayar</h6>
                        <div class="stat-value">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                        <div class="stat-label">Pembayaran disetujui</div>
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini stat-card-pending shadow">
                        <h6>Menunggu Validasi</h6>
                        <div class="stat-value">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
                        <div class="stat-label">Belum divalidasi</div>
                        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card-mini stat-card-remaining shadow">
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
                                            <td data-label="KODE" class="align-middle text-start text-md-center">
                                                <code class="small">{{ $bayar->kode_pembayaran }}</code>
                                                @if($bayar->order_id && $bayar->group_transactions_count > 1)
                                                    <div class="mt-1">
                                                        <span class="badge bg-info group-badge-small" title="Pembayaran ini adalah bagian dari transaksi gabungan ({{ $bayar->group_transactions_count }} item)">
                                                            <i class="fas fa-layer-group me-1"></i> Gabungan ({{ $bayar->group_transactions_count }})
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td data-label="TANGGAL" class="align-middle text-end text-md-start">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                                            <td data-label="JENIS TAGIHAN" class="align-middle text-end text-md-start">
                                                @if($bayar->tagihan)
                                                    <strong>{{ $jenisTagihan[$bayar->tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td data-label="JUMLAH" class="align-middle text-end fw-bold">Rp
                                                {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td data-label="METODE" class="align-middle text-end text-md-center">
                                                @if($bayar->metode_pembayaran === 'tunai')
                                                    <span class="badge bg-info"><i class="fas fa-money-bill"></i> Tunai</span>
                                                @elseif($bayar->metode_pembayaran === 'transfer')
                                                    <span class="badge bg-warning"><i class="fas fa-university"></i> Transfer</span>
                                                @else
                                                    <span class="badge bg-success"><i class="fas fa-credit-card"></i> Midtrans</span>
                                                @endif
                                            </td>
                                            <td data-label="STATUS" class="align-middle text-end text-md-center">
                                                @if($bayar->status_validasi === 'disetujui')
                                                    <span class="badge bg-success shadow-sm"><i class="fas fa-check-circle"></i>
                                                        Disetujui</span>
                                                @elseif($bayar->status_validasi === 'ditolak')
                                                    <span class="badge bg-danger shadow-sm"><i class="fas fa-times-circle"></i>
                                                        Ditolak</span>
                                                @elseif($bayar->status_validasi === 'pending' && $bayar->metode_pembayaran === 'midtrans')
                                                    @php
                                                        $isExpired = $bayar->created_at < now()->subHours(24);
                                                    @endphp
                                                    @if($isExpired)
                                                        <span class="badge bg-secondary shadow-sm"><i class="fas fa-times-circle"></i> Kadaluarsa</span>
                                                    @else
                                                        <span class="badge bg-info shadow-sm"><i class="fas fa-hourglass-half"></i> Menunggu Bayar</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning shadow-sm"><i class="fas fa-clock"></i> Menunggu Validasi</span>
                                                @endif
                                            </td>
                                            <td data-label="DIVALIDASI" class="align-middle text-end text-md-start">
                                                @if($bayar->tanggal_validasi)
                                                    <small>
                                                        {{ $bayar->tanggal_validasi->format('d/m/Y H:i') }}<br>
                                                        <span class="text-muted">oleh {{ $bayar->validator->name ?? '-' }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td data-label="AKSI" class="text-center align-middle">
                                                <a href="{{ route('admin.keuangan.pembayaran.show', $bayar->id) }}"
                                                    class="btn btn-sm btn-info shadow-sm" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($bayar->status_validasi === 'disetujui')
                                                    <a href="{{ route('admin.keuangan.pembayaran.cetak-kwitansi', $bayar->id) }}"
                                                        class="btn btn-sm btn-success shadow-sm" title="Cetak Kwitansi" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @endif
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
                        <div class="position-relative timeline-wrapper">
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