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
                                                <div class="fw-bold">{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</div>
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
                                                    <form id="formBayar{{ $item->id }}" action="{{ route('orang-tua.tagihan.bayar', $siswa->id) }}" method="POST" enctype="multipart/form-data">
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
                                                                <input type="text"
                                                                       name="jumlah_bayar_display"
                                                                       id="jumlahBayarDisplay{{ $item->id }}"
                                                                       class="form-control @if($errors->any() && old('tagihan_id') == $item->id) @error('jumlah_bayar') is-invalid @enderror @endif"
                                                                       value="Rp {{ old('tagihan_id') == $item->id ? number_format(old('jumlah_bayar', $item->jumlah), 0, ',', '.') : number_format($item->jumlah, 0, ',', '.') }}"
                                                                       placeholder="Rp 0"
                                                                       required>
                                                                <input type="hidden"
                                                                       name="jumlah_bayar"
                                                                       id="jumlahBayarActual{{ $item->id }}"
                                                                       value="{{ old('tagihan_id') == $item->id ? old('jumlah_bayar', $item->jumlah) : $item->jumlah }}">
                                                                @if($errors->any() && old('tagihan_id') == $item->id)
                                                                    @error('jumlah_bayar')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                @endif
                                                                <small class="text-muted">Minimal Rp 1.000 | Maksimal Rp {{ number_format($item->jumlah, 0, ',', '.') }}</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    Metode Pembayaran <span class="text-danger">*</span>
                                                                </label>
                                                                <select name="metode_pembayaran"
                                                                        id="metodePembayaran{{ $item->id }}"
                                                                        class="form-select @if($errors->any() && old('tagihan_id') == $item->id) @error('metode_pembayaran') is-invalid @enderror @endif"
                                                                        required>
                                                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                                                    @if($infoPembayaran->isMidtransEnabled())
                                                                    <option value="midtrans" {{ old('tagihan_id') == $item->id && old('metode_pembayaran') == 'midtrans' ? 'selected' : '' }}>
                                                                        💳 Pembayaran Digital (Otomatis)
                                                                    </option>
                                                                    @endif
                                                                    @if($infoPembayaran->hasRekeningBank())
                                                                    <option value="transfer" {{ old('tagihan_id') == $item->id && old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>
                                                                        🏦 Transfer ke Rekening Sekolah
                                                                    </option>
                                                                    @endif
                                                                    <option value="tunai" disabled style="color: #adb5bd; background-color: #f8f9fa;">
                                                                        💵 Tunai di Sekolah (Bayar di Loket)
                                                                    </option>
                                                                </select>
                                                                @if($errors->any() && old('tagihan_id') == $item->id)
                                                                    @error('metode_pembayaran')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                @endif

                                                                @if(!$infoPembayaran->isMidtransEnabled() && !$infoPembayaran->hasRekeningBank())
                                                                <div class="alert alert-warning mt-2 mb-0 py-2 px-3">
                                                                    <small>
                                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                                        Metode pembayaran online belum tersedia. Silakan hubungi sekolah atau bayar tunai di loket.
                                                                    </small>
                                                                </div>
                                                                @endif
                                                            </div>

                                                            {{-- Info Tunai (selalu tampil, data dari database) --}}
                                                            @php $tunaiInfo = $infoPembayaran->tunai_info; @endphp
                                                            <div class="card bg-warning bg-opacity-10 border-warning mb-3">
                                                                <div class="card-body py-3">
                                                                    <h6 class="card-title mb-2 text-warning">
                                                                        <i class="fas fa-info-circle me-2"></i>
                                                                        Informasi Pembayaran Tunai
                                                                    </h6>
                                                                    <div class="small text-muted">
                                                                        <div class="mb-1">
                                                                            <i class="fas fa-building me-1"></i>
                                                                            <strong>Lokasi:</strong> {{ $tunaiInfo['lokasi'] }}
                                                                        </div>
                                                                        <div class="mb-1">
                                                                            <i class="fas fa-clock me-1"></i>
                                                                            <strong>Jam:</strong> {{ $tunaiInfo['jam_operasional'] }}
                                                                        </div>
                                                                        <div>
                                                                            <i class="fas fa-sticky-note me-1"></i>
                                                                            {{ $tunaiInfo['deskripsi'] }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Info Rekening Bank (muncul saat pilih Transfer) --}}
                                                            @if($infoPembayaran->hasRekeningBank())
                                                            <div class="mb-3" id="rekeningBankSection{{ $item->id }}" style="display: none;">
                                                                <div class="card bg-light border-primary">
                                                                    <div class="card-body py-3">
                                                                        <h6 class="card-title mb-3">
                                                                            <i class="fas fa-university text-primary me-2"></i>
                                                                            Rekening Tujuan Transfer
                                                                        </h6>
                                                                        <div class="row">
                                                                            <div class="col-12 mb-2">
                                                                                <small class="text-muted d-block">Nama Bank</small>
                                                                                <strong class="text-dark">{{ $infoPembayaran->nama_bank }}</strong>
                                                                            </div>
                                                                            <div class="col-12 mb-2">
                                                                                <small class="text-muted d-block">Nomor Rekening</small>
                                                                                <div class="d-flex align-items-center">
                                                                                    <strong class="text-primary fs-5 me-2" id="noRek{{ $item->id }}">{{ $infoPembayaran->rekening_bank }}</strong>
                                                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyRekening(event, '{{ $infoPembayaran->rekening_bank }}', this)">
                                                                                        <i class="fas fa-copy"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <small class="text-muted d-block">Atas Nama</small>
                                                                                <strong class="text-dark">{{ $infoPembayaran->atas_nama }}</strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="alert alert-info mt-3 mb-0 py-2 px-3">
                                                                            <small>
                                                                                <i class="fas fa-info-circle me-1"></i>
                                                                                Pastikan transfer sesuai jumlah tagihan. Simpan bukti transfer untuk diupload.
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif

                                                            <div class="mb-3" id="buktiBayarSection{{ $item->id }}" style="display: none;">
                                                                <label class="form-label fw-bold">
                                                                    Bukti Pembayaran <span class="text-danger bukti-required-{{ $item->id }}">*</span>
                                                                </label>
                                                                <input type="file"
                                                                       name="bukti_bayar"
                                                                       id="buktiBayar{{ $item->id }}"
                                                                       class="form-control @if($errors->any() && old('tagihan_id') == $item->id) @error('bukti_bayar') is-invalid @enderror @endif"
                                                                       accept="image/*">
                                                                @if($errors->any() && old('tagihan_id') == $item->id)
                                                                    @error('bukti_bayar')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                @endif
                                                                <small class="text-muted bukti-help-{{ $item->id }}">Upload foto bukti transfer (JPG, PNG, max 2MB)</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Catatan</label>
                                                                <textarea name="catatan"
                                                                          class="form-control"
                                                                          rows="3"
                                                                          placeholder="Catatan tambahan (opsional)">{{ old('tagihan_id') == $item->id ? old('catatan') : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="fas fa-times me-1"></i>Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-primary" id="submitBtn{{ $item->id }}">
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
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPembayaran as $bayar)
                                @php
                                    // Cek apakah transaksi Midtrans masih bisa dilanjutkan (dalam 24 jam)
                                    $canContinue = $bayar->metode_pembayaran == 'midtrans'
                                        && $bayar->status_validasi == 'pending'
                                        && $bayar->created_at >= now()->subHours(24);

                                    // Hitung sisa waktu
                                    $expiredAt = $bayar->created_at->addHours(24);
                                    $remainingTime = $expiredAt->diffForHumans(now(), ['parts' => 2]);
                                @endphp
                                <tr>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $bayar->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan ?? '-')) }}</div>
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        @if($bayar->metode_pembayaran == 'tunai')
                                            <span class="badge bg-label-secondary">💵 Tunai</span>
                                        @elseif($bayar->metode_pembayaran == 'transfer')
                                            <span class="badge bg-label-info">🏦 Transfer</span>
                                        @elseif($bayar->metode_pembayaran == 'midtrans')
                                            <span class="badge bg-label-primary">💳 Digital</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ $bayar->metode_pembayaran }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($bayar->status_validasi == 'disetujui')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Lunas
                                            </span>
                                        @elseif($bayar->status_validasi == 'ditolak')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Ditolak
                                            </span>
                                        @elseif($bayar->status_validasi == 'pending' && $bayar->metode_pembayaran == 'midtrans')
                                            @if($canContinue)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-hourglass-half me-1"></i>Menunggu Bayar
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times-circle me-1"></i>Kadaluarsa
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Menunggu Validasi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($canContinue)
                                            <form action="{{ route('orang-tua.pembayaran.continue', $bayar->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" title="Lanjutkan pembayaran dalam {{ $remainingTime }}">
                                                    <i class="fas fa-credit-card me-1"></i>Bayar
                                                </button>
                                            </form>
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-clock me-1"></i>{{ $remainingTime }}
                                            </div>
                                        @elseif($bayar->status_validasi == 'pending' && $bayar->metode_pembayaran == 'midtrans' && !$canContinue)
                                            <span class="text-muted small">-</span>
                                        @else
                                            <span class="text-muted">-</span>
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

@section('styles')
<style>
/* Prevent modal flickering on hover and interaction */
.modal {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Smooth modal fade transition */
.modal.fade {
    transition: opacity 0.15s linear;
}

.modal.fade:not(.show) {
    opacity: 0;
}

/* Smooth modal dialog transition */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

.modal.fade:not(.show) .modal-dialog {
    transform: translate(0, -20px);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Stabilize modal content rendering */
.modal-content {
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

/* Remove flickering on form elements */
.modal input,
.modal select,
.modal textarea,
.modal button {
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

/* Smooth backdrop */
.modal-backdrop {
    transition: opacity 0.15s linear;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent modal flickering by ensuring only one instance exists
    const modalInstances = new Map();

    // Initialize all modals properly
    document.querySelectorAll('.modal').forEach(modalEl => {
        // Prevent duplicate initialization
        if (!modalInstances.has(modalEl.id)) {
            const modalInstance = new bootstrap.Modal(modalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            modalInstances.set(modalEl.id, modalInstance);
        }

        // Clean up on hide
        modalEl.addEventListener('hidden.bs.modal', function() {
            // Remove any leftover backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 0) {
                backdrops.forEach(backdrop => {
                    if (!document.querySelector('.modal.show')) {
                        backdrop.remove();
                    }
                });
            }

            // Reset modal position
            this.style.display = '';
        });

        // Prevent body scroll issues
        modalEl.addEventListener('show.bs.modal', function() {
            // Ensure no other modals are showing
            document.querySelectorAll('.modal.show').forEach(otherModal => {
                if (otherModal !== modalEl) {
                    const instance = bootstrap.Modal.getInstance(otherModal);
                    if (instance) {
                        instance.hide();
                    }
                }
            });
        });
    });

    // Format Rupiah Input Handler
    document.querySelectorAll('[name="jumlah_bayar_display"]').forEach(displayInput => {
        const tagihanId = displayInput.id.replace('jumlahBayarDisplay', '');
        const actualInput = document.getElementById('jumlahBayarActual' + tagihanId);

        // Format on input
        displayInput.addEventListener('input', function(e) {
            let value = e.target.value;

            // Remove all non-digit characters
            value = value.replace(/[^\d]/g, '');

            // Update hidden input with raw number
            if (actualInput) {
                actualInput.value = value;
            }

            // Format display with Rp and thousand separators
            if (value) {
                const formatted = parseInt(value).toLocaleString('id-ID');
                e.target.value = 'Rp ' + formatted;
            } else {
                e.target.value = 'Rp ';
            }
        });

        // Handle focus - select all for easy editing
        displayInput.addEventListener('focus', function(e) {
            setTimeout(() => {
                e.target.select();
            }, 50);
        });

        // Prevent non-numeric input
        displayInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[\d]/.test(char)) {
                e.preventDefault();
            }
        });
    });

    // Handle metode pembayaran change - toggle sections visibility
    document.querySelectorAll('[id^="metodePembayaran"]').forEach(selectEl => {
        const tagihanId = selectEl.id.replace('metodePembayaran', '');
        const buktiBayarInput = document.getElementById('buktiBayar' + tagihanId);
        const buktiBayarSection = document.getElementById('buktiBayarSection' + tagihanId);
        const rekeningBankSection = document.getElementById('rekeningBankSection' + tagihanId);
        const requiredStar = document.querySelector('.bukti-required-' + tagihanId);
        const helpText = document.querySelector('.bukti-help-' + tagihanId);

        // Handle change event
        selectEl.addEventListener('change', function() {
            const selectedMethod = this.value;

            // Hide conditional sections first
            if (buktiBayarSection) buktiBayarSection.style.display = 'none';
            if (rekeningBankSection) rekeningBankSection.style.display = 'none';
            if (buktiBayarInput) buktiBayarInput.removeAttribute('required');

            if (selectedMethod === 'midtrans') {
                // Pembayaran Digital - No additional sections needed
            } else if (selectedMethod === 'transfer') {
                // Transfer Bank - Show rekening info and bukti section
                if (rekeningBankSection) rekeningBankSection.style.display = 'block';
                if (buktiBayarSection) buktiBayarSection.style.display = 'block';
                if (buktiBayarInput) buktiBayarInput.setAttribute('required', 'required');
                if (requiredStar) requiredStar.style.display = 'inline';
                if (helpText) helpText.innerHTML = 'Upload foto bukti transfer (JPG, PNG, max 2MB)';
            }
        });

        // Trigger on page load if method already selected
        if (selectEl.value) {
            selectEl.dispatchEvent(new Event('change'));
        }
    });

    // Copy rekening function - using execCommand which works better in modals
    window.copyRekening = function(event, text, btn) {
        // Prevent form submission and event bubbling
        event.preventDefault();
        event.stopPropagation();

        // Create temporary input element (input works better than textarea in some browsers)
        const tempInput = document.createElement('input');
        tempInput.setAttribute('type', 'text');
        tempInput.setAttribute('value', text);
        tempInput.style.cssText = 'position:absolute;left:-9999px;top:-9999px;opacity:0;';

        // Append to modal body for better focus handling
        const modalBody = btn.closest('.modal-body') || document.body;
        modalBody.appendChild(tempInput);

        // Select and copy
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        let success = false;
        try {
            success = document.execCommand('copy');
        } catch (err) {
            console.error('Copy failed:', err);
        }

        // Remove temporary element
        modalBody.removeChild(tempInput);

        // Show feedback
        if (success) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        } else {
            // Show manual copy prompt
            prompt('Salin nomor rekening ini:', text);
        }

        return false;
    };

    // Handle form submission - validate based on payment method
    document.querySelectorAll('[id^="formBayar"]').forEach(formEl => {
        formEl.addEventListener('submit', function(e) {
            const tagihanId = this.id.replace('formBayar', '');
            const metodePembayaran = document.getElementById('metodePembayaran' + tagihanId);
            const buktiBayar = document.getElementById('buktiBayar' + tagihanId);

            // Jika metode adalah transfer, validasi bukti bayar
            if (metodePembayaran && buktiBayar) {
                const selectedMethod = metodePembayaran.value;

                if (selectedMethod === 'transfer' && !buktiBayar.files.length) {
                    e.preventDefault();
                    alert('Bukti transfer wajib diupload untuk metode Transfer ke Rekening Sekolah');
                    buktiBayar.focus();
                    return false;
                }
            }

            // Form valid, allow submission
            return true;
        });
    });

    // Check if there are validation errors and show the correct modal
    @if($errors->any() && old('tagihan_id'))
        // Use requestAnimationFrame for smooth modal opening
        requestAnimationFrame(function() {
            const tagihanId = {{ old('tagihan_id') }};
            const modalElement = document.getElementById('modalBayar' + tagihanId);

            if (modalElement) {
                const modalInstance = modalInstances.get(modalElement.id) ||
                                     bootstrap.Modal.getOrCreateInstance(modalElement);

                // Small delay to ensure DOM is stable
                setTimeout(function() {
                    modalInstance.show();
                }, 50);
            }
        });
    @endif
});
</script>
@endsection
