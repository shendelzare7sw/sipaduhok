@extends('layouts.sneat')

@section('title', 'Tagihan - ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tagihan & Pembayaran</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Tagihan & Pembayaran</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <div>
            <a href="{{ route('orang-tua.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-primary">
                                <i class="fas fa-receipt"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Total Tagihan</small>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-success">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Sudah Dibayar</small>
                            <h4 class="mb-0 text-success fw-bold">Rp {{ number_format($totalBayar, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded {{ $sisaTagihan > 0 ? 'bg-danger' : 'bg-success' }}">
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Sisa Tagihan</small>
                            <h4 class="mb-0 {{ $sisaTagihan > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Tagihan -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Tagihan
            </h5>
            @if($sisaTagihan > 0)
                <span class="badge bg-danger">{{ $tagihan->where('status', 'belum_bayar')->count() }} Belum Lunas</span>
            @else
                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Semua Lunas</span>
            @endif
        </div>
        <div class="card-body">
            @if($tagihan->isEmpty())
                <div class="alert alert-info d-flex align-items-center mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>Tidak ada tagihan untuk siswa ini.</div>
                </div>
            @else
                @foreach($tagihanGroup as $jenis => $items)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted mb-3 fw-bold">
                            <i class="fas fa-folder me-2"></i>{{ ucwords(str_replace('_', ' ', $jenis)) }}
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Keterangan</th>
                                        <th class="text-nowrap">Jatuh Tempo</th>
                                        <th class="text-end text-nowrap">Jumlah</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $item->keterangan }}</div>
                                                @if($item->bulan && $item->tahun)
                                                    <small class="text-muted">{{ date('F Y', mktime(0, 0, 0, $item->bulan, 1, $item->tahun)) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <div>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</div>
                                                @if(\Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->isPast() && $item->status == 'belum_bayar')
                                                    <span class="badge bg-danger">Terlambat</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <strong>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($item->status == 'sudah_bayar')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Lunas
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item->status != 'sudah_bayar')
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalBayar{{ $item->id }}">
                                                        <i class="fas fa-money-bill-wave me-1"></i>
                                                        <span class="d-none d-sm-inline">Bayar</span>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Modal Pembayaran -->
                                        <div class="modal fade" id="modalBayar{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('orang-tua.tagihan.bayar', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="tagihan_id" value="{{ $item->id }}">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="fas fa-money-bill-wave text-primary me-2"></i>
                                                                Pembayaran
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-info mb-3">
                                                                <strong>{{ $item->keterangan }}</strong>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jumlah Tagihan</label>
                                                                <input type="text" class="form-control form-control-lg bg-light"
                                                                       value="Rp {{ number_format($item->jumlah, 0, ',', '.') }}"
                                                                       readonly>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    Jumlah Bayar <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="number"
                                                                       name="jumlah_bayar"
                                                                       class="form-control @error('jumlah_bayar') is-invalid @enderror"
                                                                       value="{{ old('jumlah_bayar', $item->jumlah) }}"
                                                                       min="1000"
                                                                       max="{{ $item->jumlah }}"
                                                                       placeholder="Masukkan jumlah"
                                                                       required>
                                                                @error('jumlah_bayar')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                                <small class="text-muted">Minimal Rp 1.000</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    Metode Pembayaran <span class="text-danger">*</span>
                                                                </label>
                                                                <select name="metode_pembayaran"
                                                                        class="form-select @error('metode_pembayaran') is-invalid @enderror"
                                                                        required>
                                                                    <option value="">Pilih Metode</option>
                                                                    <option value="tunai">💵 Tunai</option>
                                                                    <option value="transfer">🏦 Transfer Bank</option>
                                                                    <option value="ewallet"><i class="fas fa-mobile-alt"></i> E-Wallet</option>
                                                                </select>
                                                                @error('metode_pembayaran')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Bukti Pembayaran</label>
                                                                <input type="file"
                                                                       name="bukti_bayar"
                                                                       class="form-control @error('bukti_bayar') is-invalid @enderror"
                                                                       accept="image/*">
                                                                @error('bukti_bayar')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                                <small class="text-muted">Upload foto bukti transfer/struk (JPG, PNG, max 2MB)</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Catatan</label>
                                                                <textarea name="catatan"
                                                                          class="form-control"
                                                                          rows="3"
                                                                          placeholder="Catatan tambahan (opsional)"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="fas fa-times me-1"></i>Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fas fa-paper-plane me-1"></i>Ajukan Pembayaran
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-history me-2 text-primary"></i>
                Riwayat Pembayaran
            </h5>
        </div>
        <div class="card-body">
            @if($riwayatPembayaran->isEmpty())
                <div class="alert alert-info d-flex align-items-center mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>Belum ada riwayat pembayaran.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Tagihan</th>
                                <th class="text-end">Jumlah</th>
                                <th>Metode</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPembayaran as $bayar)
                                <tr>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}</td>
                                    <td>{{ $bayar->tagihan->keterangan ?? '-' }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        @if($bayar->metode_pembayaran == 'tunai')
                                            <span class="badge bg-label-secondary">💵 Tunai</span>
                                        @elseif($bayar->metode_pembayaran == 'transfer')
                                            <span class="badge bg-label-info">🏦 Transfer</span>
                                        @else
                                            <span class="badge bg-label-primary"><i class="fas fa-mobile-alt"></i> E-Wallet</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($bayar->status_validasi == 'disetujui')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Disetujui
                                            </span>
                                        @elseif($bayar->status_validasi == 'ditolak')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
