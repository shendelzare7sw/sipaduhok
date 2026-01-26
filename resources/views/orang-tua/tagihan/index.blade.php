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

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan pada input Anda:</div>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-primary">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Tagihan Tahun Ini</small>
                                <h4 class="mb-0 fw-bold">Rp {{ number_format($totalTagihanCurrent, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100 {{ $totalTunggakan > 0 ? 'border-danger border' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-danger">
                                    <i class="fas fa-history"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Total Tunggakan</small>
                                <h4 class="mb-0 text-danger fw-bold">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Sudah Dibayar (Thn Ini)</small>
                                <h4 class="mb-0 text-success fw-bold">Rp {{ number_format($totalBayarCurrent, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 bg-label-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-warning">
                                    <i class="fas fa-wallet"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Total Kewajiban</small>
                                <h4 class="mb-0 text-dark fw-bold">
                                    Rp {{ number_format($grandTotalUnpaid, 0, ',', '.') }}
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
                @if($sisaTagihanCurrent > 0)
                    <span class="badge bg-warning">{{ $tagihan->where('status', 'belum_bayar')->where('tahun_ajaran_id', $activeYear->id ?? 0)->count() }} Belum Lunas</span>
                @else
                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Lunas (Tahun Ini)</span>
                @endif
            </div>
            <div class="card-body">
                @if($tagihan->isEmpty())
                    <div class="alert alert-info d-flex align-items-center mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>Tidak ada tagihan untuk siswa ini.</div>
                    </div>
                @else
                    {{-- Section: Tunggakan Tahun Lalu --}}
                    @if($arrearsGroup->isNotEmpty())
                        <div class="mb-5">
                            <div class="alert alert-danger d-flex align-items-center mb-3">
                                <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
                                <div>
                                    <strong>Perhatian:</strong> Terdapat tunggakan dari tahun ajaran sebelumnya yang belum dilunasi.
                                </div>
                            </div>
                            
                            @foreach($arrearsGroup as $tahunId => $tagihans)
                                @php $tahunLabel = $tagihans->first()->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Lalu'; @endphp
                                <h6 class="text-danger fw-bold border-bottom border-danger pb-2 mb-3">
                                    <i class="fas fa-history me-2"></i>Tunggakan Tahun Ajaran {{ $tahunLabel }}
                                </h6>
                                
                                <div class="table-responsive mb-4">
                                    <table class="table table-hover align-middle border border-danger">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;" class="text-center">Pilih</th>
                                                <th>Keterangan</th>
                                                <th class="text-nowrap" style="width: 150px;">Jatuh Tempo</th>
                                                <th class="text-end text-nowrap" style="width: 150px;">Tagihan</th>
                                                <th class="text-center" style="width: 100px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tagihans as $item)
                                                @php
                                                    $isSpp = str_contains($item->jenis_tagihan, 'spp');
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input item-checkbox group-arrears-{{ $tahunId }}"
                                                            value="{{ $item->id }}" data-amount="{{ $item->jumlah }}"
                                                            data-label="{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }} ({{ $tahunLabel }})"
                                                            data-is-spp="{{ $isSpp ? 'true' : 'false' }}">
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-danger">
                                                            {{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}
                                                        </div>
                                                        <small class="text-muted">Dispensasi: {{ $item->status == 'cicilan' ? 'Cicilan' : 'Belum Lunas' }}</small>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <div>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="fw-bold text-danger">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger">Tunggakan</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                        
                        <h5 class="mb-4 mt-5">
                            <i class="fas fa-calendar-check me-2 text-primary"></i>
                            Tagihan Tahun Ajaran Ini
                        </h5>
                    @endif
                    @foreach($tagihanGroup as $jenis => $items)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted mb-3 fw-bold border-bottom pb-2">
                                <i
                                    class="fas fa-folder me-2"></i>{{ \App\Models\Tagihan::getLabelJenis($jenis) ?? ucwords(str_replace('_', ' ', $jenis)) }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">
                                                Pilih
                                            </th>
                                            <th>Keterangan</th>
                                            <th class="text-nowrap" style="width: 150px;">Jatuh Tempo</th>
                                            <th class="text-end text-nowrap" style="width: 150px;">Tagihan</th>
                                            <th class="text-center" style="width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            @php
                                                $isSpp = str_contains($item->jenis_tagihan, 'spp');
                                                $isPaid = $item->status == 'sudah_bayar';
                                            @endphp
                                            <tr class="{{ $isPaid ? 'table-light text-muted' : '' }}">
                                                <td class="text-center">
                                                    @if(!$isPaid)
                                                        <input type="checkbox" class="form-check-input item-checkbox group-{{ $jenis }}"
                                                            value="{{ $item->id }}" data-amount="{{ $item->jumlah }}"
                                                            data-label="{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}"
                                                            data-is-spp="{{ $isSpp ? 'true' : 'false' }}">
                                                    @else
                                                        <i class="fas fa-check text-success"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-bold">
                                                        {{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}
                                                    </div>
                                                </td>
                                                <td class="text-nowrap">
                                                    <div>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</div>
                                                    @if(\Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->isPast() && !$isPaid)
                                                        <span class="badge bg-label-danger text-danger"
                                                            style="font-size: 0.7rem;">Terlambat</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <span class="fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($isPaid)
                                                        <span class="badge bg-success">Lunas</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Bayar</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Sticky Footer for Bulk Payment -->
        <div id="bulkPaymentFooter" class="fixed-bottom bg-white border-top shadow-lg p-3 d-none"
            style="z-index: 1030; box-shadow: 0 -0.5rem 1rem rgba(0,0,0,0.15)!important;">
            <div class="container-xxl">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small d-block">Total Pembayaran (<span id="selectedCount">0</span> item
                            terpilih)</span>
                        <h4 class="mb-0 fw-bold text-primary" id="grandTotalDisplay">Rp 0</h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-4" id="btnPaySelected"
                            style="margin-right: 6rem;">
                            <i class="fas fa-wallet me-2"></i>Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Payment Modal -->
        <div class="modal fade" id="modalBulkPay" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="formBulkPay" action="{{ route('orang-tua.tagihan.bulk-pay', $siswa->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div id="hiddenInputsContainer"></div>
                        <input type="hidden" name="total_bayar" id="inputTotalBayar" value="0">

                        <div class="modal-header bg-light">
                            <h5 class="modal-title">
                                <i class="fas fa-money-bill-wave text-primary me-2"></i>
                                Konfirmasi Pembayaran
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Rincian Pembayaran</h6>
                                <ul class="list-group list-group-flush mb-3" id="paymentSummaryList"
                                    style="max-height: 200px; overflow-y: auto;">
                                    <!-- filled by JS -->
                                </ul>
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded fw-bold">
                                    <span>Total Yang Harus Dibayar</span>
                                    <span class="text-primary fs-5" id="modalTotalDisplay">Rp 0</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Pilih Metode Pembayaran</label>
                                <div class="row g-2">
                                    {{-- Midtrans Option --}}
                                    @if($infoPembayaran->isMidtransEnabled())
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="methodMidtrans"
                                                value="midtrans" autocomplete="off" required>
                                            <label
                                                class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                                for="methodMidtrans">
                                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                <span class="small fw-bold">Digital / QRIS</span>
                                            </label>
                                        </div>
                                    @endif

                                    {{-- Transfer Option --}}
                                    @if($infoPembayaran->hasRekeningBank())
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="methodTransfer"
                                                value="transfer" autocomplete="off" required>
                                            <label
                                                class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                                for="methodTransfer">
                                                <i class="fas fa-university fa-2x mb-2"></i>
                                                <span class="small fw-bold">Transfer Bank</span>
                                            </label>
                                        </div>
                                    @endif

                                    {{-- Tunai Option (Visual Only) --}}
                                    <div class="col-md-4">
                                        {{-- Disabled input so it can't be selected --}}
                                        <input type="radio" class="btn-check" name="metode_pembayaran_dummy"
                                            id="methodTunaiDummy" disabled>
                                        {{-- Styled label to look like others but keeping 'btn-outline-warning' style --}}
                                        <label
                                            class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                            for="methodTunaiDummy" style="opacity: 1; cursor: default;">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                            <span class="small fw-bold">Tunai (Sekolah)</span>
                                        </label>
                                    </div>
                                    {{-- Tunai Option Removed from Selection --}}
                                </div>
                            </div>

                            {{-- CONTENT SECTIONS --}}

                            {{-- 1. Midtrans Info --}}
                            <div id="infoMidtrans" class="method-info" style="display: none;">
                                <div class="alert alert-primary d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    <div class="small">
                                        Anda akan diarahkan ke halaman pembayaran digital. Pembayaran akan terverifikasi
                                        secara <strong>otomatis</strong>.
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Transfer Info --}}
                            @if($infoPembayaran->hasRekeningBank())
                                <div id="infoTransfer" class="method-info" style="display: none;">
                                    <div class="card bg-label-info border border-info mb-3">
                                        <div class="card-body">
                                            <h6 class="fw-bold text-info mb-3"><i class="fas fa-university me-2"></i>Rekening
                                                Tujuan</h6>

                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted small fw-bold text-uppercase">Bank</div>
                                                <div class="col-sm-8 fw-bold text-dark">{{ $infoPembayaran->nama_bank }}</div>
                                            </div>

                                            <div class="row mb-2 align-items-center">
                                                <div class="col-sm-4 text-muted small fw-bold text-uppercase">No. Rekening</div>
                                                <div class="col-sm-8 d-flex align-items-center">
                                                    <span
                                                        class="fs-5 font-monospace text-primary me-2 fw-bold">{{ $infoPembayaran->rekening_bank }}</span>
                                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill"
                                                        onclick="copyRekening(event, '{{ $infoPembayaran->rekening_bank }}', this)"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Salin No. Rekening">
                                                        <i class="fas fa-copy me-1"></i> Salin
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4 text-muted small fw-bold text-uppercase">Atas Nama (A/N)
                                                </div>
                                                <div class="col-sm-8 fw-bold text-dark">{{ $infoPembayaran->atas_nama }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            Bukti Transfer <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" name="bukti_bayar" id="bulkBuktiInput" class="form-control"
                                            accept="image/*">
                                        <small class="text-muted">Upload foto bukti transfer total nominal (Max: 10MB).</small>
                                    </div>
                                </div>
                            @endif

                            {{-- 3. Tunai Info --}}
                            {{-- 3. Tunai Info (Always Visible below methods) --}}
                            <div class="mt-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-label-warning text-warning fw-bold">
                                        <i class="fas fa-money-bill-wave me-2"></i> Informasi Pembayaran Tunai
                                    </div>
                                    <div class="card-body bg-light pt-3">
                                        @php $tunai = $infoPembayaran->tunai_info ?? []; @endphp

                                        <p class="text-danger fw-bold small mb-3">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Pembayaran tunai <strong>tidak dapat dilakukan secara online</strong>. Silakan
                                            kunjungi lokasi di bawah ini.
                                        </p>

                                        <div class="mb-2">
                                            <small class="text-muted d-block fw-bold">LOKASI</small>
                                            <span
                                                class="text-dark">{{ $tunai['lokasi'] ?? 'Loket Pembayaran Sekolah' }}</span>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted d-block fw-bold">JAM OPERASIONAL</small>
                                            <span class="text-dark">{{ $tunai['jam_operasional'] ?? 'Jam Kerja' }}</span>
                                        </div>

                                        <div class="mb-0">
                                            <small class="text-muted d-block fw-bold">CATATAN</small>
                                            <span
                                                class="text-dark">{{ $tunai['deskripsi'] ?? 'Harap membawa kartu siswa saat melakukan pembayaran.' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitBulk">
                                <i class="fas fa-check-circle me-1"></i>Bayar
                            </button>
                        </div>
                    </form>
                </div>
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
                                        $canContinue = $bayar->metode_pembayaran == 'midtrans'
                                            && $bayar->status_validasi == 'pending'
                                            && $bayar->created_at >= now()->subHours(24);

                                        $expiredAt = $bayar->created_at->addHours(24);
                                        $remainingTime = $expiredAt->diffForHumans(now(), ['parts' => 2]);
                                    @endphp
                                    <tr>
                                        <td class="text-nowrap">
                                            {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $bayar->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan ?? '-')) }}
                                            </div>
                                            <small class="text-muted">ID: #{{ $bayar->kode_pembayaran }}</small>
                                        </td>
                                        <td class="text-end fw-bold">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            @if($bayar->metode_pembayaran == 'tunai')
                                                <span class="badge bg-label-secondary">💵 Tunai</span>
                                            @elseif($bayar->metode_pembayaran == 'transfer')
                                                <span class="badge bg-label-info">🏦 Transfer</span>
                                            @elseif($bayar->metode_pembayaran == 'midtrans')
                                                <span class="badge bg-label-primary">💳 Digital</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($bayar->status_validasi == 'disetujui')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                                            @elseif($bayar->status_validasi == 'ditolak')
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                                            @elseif($bayar->status_validasi == 'pending' && $bayar->metode_pembayaran == 'midtrans')
                                                @if($canContinue)
                                                    <span class="badge bg-warning"><i class="fas fa-hourglass-half me-1"></i>Menunggu
                                                        Bayar</span>
                                                @else
                                                    <span class="badge bg-secondary"><i
                                                            class="fas fa-times-circle me-1"></i>Kadaluarsa</span>
                                                @endif
                                            @else
                                                <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Validasi</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($canContinue)
                                                <form action="{{ route('orang-tua.pembayaran.continue', $bayar->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Lanjutkan">
                                                        <i class="fas fa-credit-card"></i>
                                                    </button>
                                                </form>
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
        /* Prevent modal flickering */
        .modal {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .modal.fade {
            transition: opacity 0.15s linear;
        }

        .modal.fade:not(.show) {
            opacity: 0;
        }

        .payment-input:disabled {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #adb5bd;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // -- ELEMENT SELECTORS --
            const checkAllGroups = document.querySelectorAll('.select-all-group');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const paymentInputs = document.querySelectorAll('.payment-input');
            const footer = document.getElementById('bulkPaymentFooter');
            const selectedCountSpan = document.getElementById('selectedCount');
            const grandTotalDisplay = document.getElementById('grandTotalDisplay');
            const btnPaySelected = document.getElementById('btnPaySelected');
            const modalBulkPay = document.getElementById('modalBulkPay');
            const bulkModalBs = new bootstrap.Modal(modalBulkPay);

            // -- STATE --
            let totalBayar = 0;

            // -- FUNCTIONS --

            function formatRupiah(num) {
                return 'Rp ' + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            }

            function parseRupiah(str) {
                return parseInt(str.replace(/[^0-9]/g, '')) || 0;
            }

            function updateTotal() {
                let count = 0;
                let total = 0;

                itemCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        count++;
                        // Use dataset.amount instead of input value
                        total += parseInt(cb.dataset.amount);
                    }
                });

                totalBayar = total;
                selectedCountSpan.textContent = count;
                grandTotalDisplay.textContent = formatRupiah(total);

                if (count > 0) {
                    footer.classList.remove('d-none');
                } else {
                    footer.classList.add('d-none');
                }
            }

            // -- EVENT LISTENERS --

            // 1. Group Select All
            checkAllGroups.forEach(cb => {
                cb.addEventListener('change', function () {
                    const group = this.dataset.group;
                    const targets = document.querySelectorAll('.item-checkbox.group-' + group);
                    targets.forEach(t => {
                        t.checked = this.checked;
                        // toggleInput removed
                    });
                    updateTotal();
                });
            });

            // 2. Individual Checkbox
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    // toggleInput removed

                    // Uncheck "Select All" if one is unchecked (optional logic)
                    const group = this.classList[2].replace('group-', '');
                    const selectAllVal = document.querySelector(`.select-all-group[data-group="${group}"]`);
                    if (!this.checked && selectAllVal) selectAllVal.checked = false;

                    updateTotal();
                });
            });

            // 3. Toggle Input State (REMOVED)
            // 4. Input Formatting & Live Total (REMOVED)

            // 5. Pay Button -> Show Modal
            btnPaySelected.addEventListener('click', function () {
                const summaryList = document.getElementById('paymentSummaryList');
                const container = document.getElementById('hiddenInputsContainer');
                const totalDisplay = document.getElementById('modalTotalDisplay');
                const inputTotal = document.getElementById('inputTotalBayar');

                summaryList.innerHTML = '';
                container.innerHTML = '';

                let validItems = 0;

                itemCheckboxes.forEach((cb, index) => {
                    if (cb.checked) {
                        const amount = parseInt(cb.dataset.amount);

                        if (amount > 0) {
                            validItems++;

                            // Add visual list item
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center px-0';
                            li.innerHTML = `
                                                    <span>${cb.dataset.label}</span>
                                                    <span class="fw-semibold">Rp ${amount.toLocaleString('id-ID')}</span>
                                                `;
                            summaryList.appendChild(li);

                            // Add hidden inputs for form submission
                            // items[index][tagihan_id]
                            const inputId = document.createElement('input');
                            inputId.type = 'hidden';
                            inputId.name = `items[${index}][tagihan_id]`;
                            inputId.value = cb.value;
                            container.appendChild(inputId);

                            // items[index][jumlah_bayar]
                            const inputAmt = document.createElement('input');
                            inputAmt.type = 'hidden';
                            inputAmt.name = `items[${index}][jumlah_bayar]`;
                            inputAmt.value = amount;
                            container.appendChild(inputAmt);
                        }
                    }
                });

                if (validItems === 0) {
                    alert('Silakan pilih tagihan yang akan dibayar.');
                    return;
                }

                totalDisplay.textContent = formatRupiah(totalBayar);
                inputTotal.value = totalBayar;

                bulkModalBs.show();
            });

            // 6. Payment Method Logic (Toggle sections)
            const methodRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
            const infoSections = document.querySelectorAll('.method-info');
            const btnSubmit = document.getElementById('btnSubmitBulk');
            const buktiInput = document.getElementById('bulkBuktiInput');

            function handleMethodChange() {
                // Hide all info sections first
                infoSections.forEach(el => el.style.display = 'none');

                // Reset required for file input
                if (buktiInput) buktiInput.required = false;

                // Create a clear state
                let selectedValue = null;
                methodRadios.forEach(radio => {
                    if (radio.checked) selectedValue = radio.value;
                });

                if (selectedValue === 'midtrans') {
                    document.getElementById('infoMidtrans').style.display = 'block';
                    btnSubmit.classList.remove('d-none');
                } else if (selectedValue === 'transfer') {
                    const transferDiv = document.getElementById('infoTransfer');
                    if (transferDiv) transferDiv.style.display = 'block';
                    if (buktiInput) buktiInput.required = true;
                    btnSubmit.classList.remove('d-none');
                }

                // Tunai logic removed from here as it is no longer distinct radio
            }

            methodRadios.forEach(radio => {
                radio.addEventListener('change', handleMethodChange);
            });

            // File Size Validation
            if (buktiInput) {
                buktiInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileSize = this.files[0].size / 1024 / 1024; // in MB
                        if (fileSize > 10) {
                            alert('Ukuran file terlalu besar! Maksimal 10MB. File Anda: ' + fileSize.toFixed(2) + 'MB');
                            this.value = ''; // Clear input
                        }
                    }
                });
            }

            // Copy Helper
            window.copyRekening = function (event, text, btn) {
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
                    setTimeout(function () {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-primary');
                    }, 2000);
                }
                return false;
            };
        });
    </script>
@endsection