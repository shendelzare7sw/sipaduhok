@extends('layouts.sneat')

@section('title', 'Tagihan & Pembayaran - ' . $siswa->nama_lengkap)
@section('page-title', 'Tagihan & Pembayaran')

@section('sidebar-menu')
    @include('wali-siswa.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-siswa/tagihan/index.css'])
    <style>
        .btn-check:checked + .btn-outline-primary,
        .btn-check:active + .btn-outline-primary,
        .btn-outline-primary:active,
        .btn-outline-primary:hover,
        .btn-outline-primary.active,
        .btn-outline-primary.dropdown-toggle.show {
            color: #fff !important;
            background-color: #696cff !important;
            border-color: #696cff !important;
        }
        
        .btn-check:checked + .btn-outline-info,
        .btn-check:active + .btn-outline-info,
        .btn-outline-info:active,
        .btn-outline-info:hover,
        .btn-outline-info.active,
        .btn-outline-info.dropdown-toggle.show {
            color: #fff !important;
            background-color: #03c3ec !important;
            border-color: #03c3ec !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y wali-siswa-tagihan-index-page">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div class="mb-3 mb-md-0">
                <h4 class="fw-bold mb-1">Tagihan & Pembayaran</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                    <span class="mx-2">|</span>
                    <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                </p>
            </div>
            <div>
                <a href="{{ route('wali-siswa.dashboard') }}" class="btn btn-outline-secondary btn-sm">
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
                    {{-- Section A: Tunggakan TA Lama yang BELUM DIALIHKAN --}}
                    @if($arrearsBelumDialihkanGroup->isNotEmpty())
                        <div class="mb-5">
                            <div class="alert alert-danger d-flex align-items-start mb-3">
                                <i class="fas fa-exclamation-triangle me-2 fa-lg mt-1"></i>
                                <div>
                                    <strong>Perhatian:</strong> Terdapat tunggakan dari tahun ajaran sebelumnya yang
                                    <strong>belum dialihkan</strong> ke TA aktif. Daftar di bawah hanya tampil sebagai informasi —
                                    silakan <strong>hubungi bendahara sekolah</strong> agar tunggakan ini dialihkan ke tagihan TA aktif terlebih dahulu sebelum dapat dibayar.
                                </div>
                            </div>

                            @foreach($arrearsBelumDialihkanGroup as $tahunId => $tagihans)
                                @php $tahunLabel = $tagihans->first()->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Lalu'; @endphp
                                <h6 class="text-danger fw-bold border-bottom border-danger pb-2 mb-3">
                                    <i class="fas fa-history me-2"></i>Tunggakan TA {{ $tahunLabel }} <small class="text-muted">(belum dialihkan)</small>
                                </h6>

                                <div class="table-responsive mb-4">
                                    <table class="table table-hover align-middle border border-danger">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Keterangan</th>
                                                <th class="text-nowrap tagihan-col-date">Jatuh Tempo</th>
                                                <th class="text-end text-nowrap tagihan-col-amount">Tagihan</th>
                                                <th class="text-center tagihan-col-status-sm">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tagihans as $item)
                                                @php
                                                    $isPartial = $item->sisa_tagihan < $item->jumlah;
                                                @endphp
                                                <tr class="arrears-unavailable-row">
                                                    <td data-label="KETERANGAN">
                                                        <div class="fw-bold text-danger">
                                                            {{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}
                                                        </div>
                                                        <small class="text-muted">Status: {{ $item->status == 'cicilan' ? 'Cicilan' : 'Belum Lunas' }}</small>
                                                    </td>
                                                    <td data-label="JATUH TEMPO" class="text-end text-md-start text-nowrap">
                                                        <div>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</div>
                                                    </td>
                                                    <td data-label="TAGIHAN" class="text-end">
                                                        <span class="fw-bold text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
                                                        @if($isPartial)
                                                            <div class="small text-muted text-decoration-line-through">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                                        @endif
                                                    </td>
                                                    <td data-label="STATUS" class="text-end text-md-center">
                                                        <span class="badge bg-danger" title="Hubungi sekolah untuk pengalihan">
                                                            <i class="fas fa-lock"></i> Belum Bisa Dibayar
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Tunggakan ini hanya tampil sebagai laporan. Hubungi bendahara sekolah agar dialihkan ke tagihan TA aktif sebelum dapat dibayar.
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Section B: Tunggakan TA Lama yang SUDAH DIALIHKAN ke TA aktif (info read-only) --}}
                    @if($arrearsDialihkanGroup->isNotEmpty())
                        <div class="mb-5">
                            <div class="alert alert-info d-flex align-items-start mb-3">
                                <i class="fas fa-info-circle me-2 fa-lg mt-1"></i>
                                <div>
                                    <strong>Riwayat:</strong> Tunggakan dari TA sebelumnya berikut sudah <strong>dialihkan</strong> menjadi tagihan baru di TA aktif (lihat di bagian "Tagihan Tahun Ajaran Ini" di bawah).
                                </div>
                            </div>

                            @foreach($arrearsDialihkanGroup as $tahunId => $tagihans)
                                @php $tahunLabel = $tagihans->first()->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Lalu'; @endphp
                                <h6 class="text-secondary fw-bold border-bottom pb-2 mb-3">
                                    <i class="fas fa-history me-2"></i>TA {{ $tahunLabel }} <small class="text-muted">(sudah dialihkan)</small>
                                </h6>

                                <div class="table-responsive mb-4">
                                    <table class="table align-middle table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Keterangan</th>
                                                <th class="text-end text-nowrap tagihan-col-original-amount">Jumlah Asli</th>
                                                <th class="text-center tagihan-col-status-lg">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tagihans as $item)
                                                <tr class="text-muted">
                                                    <td data-label="KETERANGAN">
                                                        <div class="fw-bold">{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</div>
                                                        <small>Dialihkan {{ $item->dialihkan_pada ? \Carbon\Carbon::parse($item->dialihkan_pada)->format('d M Y') : '-' }}</small>
                                                    </td>
                                                    <td data-label="JUMLAH" class="text-end">
                                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                                    </td>
                                                    <td data-label="STATUS" class="text-end text-md-center">
                                                        @if($item->status === 'sudah_bayar')
                                                            <span class="badge bg-success">Lunas (via TA aktif)</span>
                                                        @else
                                                            <span class="badge bg-info">Sudah dialihkan</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($arrearsBelumDialihkanGroup->isNotEmpty() || $arrearsDialihkanGroup->isNotEmpty())
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
                                            <th class="text-center tagihan-col-select">
                                                Pilih
                                            </th>
                                            <th>Keterangan</th>
                                            <th class="text-nowrap tagihan-col-date">Jatuh Tempo</th>
                                            <th class="text-end text-nowrap tagihan-col-amount">Tagihan</th>
                                            <th class="text-center tagihan-col-status-xs">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            @php
                                                $isSpp = str_contains($item->jenis_tagihan, 'spp');
                                                $isPaid = $item->status == 'sudah_bayar';
                                                $isPartial = (!$isPaid && $item->sisa_tagihan < $item->jumlah);
                                            @endphp
                                            <tr class="{{ $isPaid ? 'paid-bill-row' : '' }}">
                                                <td data-label="PILIH" class="text-start text-md-center">
                                                    @if(!$isPaid)
                                                        <input type="checkbox" class="form-check-input item-checkbox group-{{ $jenis }}"
                                                            value="{{ $item->id }}" data-amount="{{ $item->sisa_tagihan }}"
                                                            data-label="{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}"
                                                            data-is-spp="{{ $isSpp ? 'true' : 'false' }}">
                                                    @else
                                                        <i class="fas fa-check text-success"></i>
                                                    @endif
                                                </td>
                                                <td data-label="KETERANGAN">
                                                    <div class="fw-bold">
                                                        {{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}
                                                    </div>
                                                    @if($item->tagihan_asal_id && $item->tagihanAsal)
                                                        <span class="badge bg-label-warning text-warning mt-1 tagihan-badge-sm">
                                                            <i class="fas fa-exchange-alt me-1"></i>
                                                            Tunggakan dari TA {{ $item->tagihanAsal->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td data-label="JATUH TEMPO" class="text-end text-md-start text-nowrap">
                                                    <div>{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</div>
                                                    @if(\Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->isPast() && !$isPaid)
                                                        <span class="badge bg-label-danger text-danger tagihan-badge-sm">Terlambat</span>
                                                    @endif
                                                </td>
                                                <td data-label="TAGIHAN" class="text-end">
                                                    @if($isPaid)
                                                        <span class="fw-bold text-success">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="fw-bold text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
                                                        @if($isPartial)
                                                            <div class="small text-muted text-decoration-line-through">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td data-label="STATUS" class="text-end text-md-center">
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
        <div id="bulkPaymentFooter" class="fixed-bottom bg-white border-top shadow-lg p-3 d-none bulk-payment-footer">
            <div class="container-xxl">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-4" id="btnPaySelected">
                            <i class="fas fa-wallet me-2"></i>Bayar Sekarang
                        </button>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small d-block">Total Pembayaran (<span id="selectedCount">0</span> item
                            terpilih)</span>
                        <h4 class="mb-0 fw-bold text-primary" id="grandTotalDisplay">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Payment Modal -->
        <div class="modal fade" id="modalBulkPay" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="formBulkPay" action="{{ route('wali-siswa.tagihan.bulk-pay', $siswa->id) }}" method="POST"
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
                                <ul class="list-group list-group-flush mb-3 payment-summary-list" id="paymentSummaryList">
                                    <!-- filled by JS -->
                                </ul>
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded fw-bold">
                                    <span>Total Yang Harus Dibayar</span>
                                    <span class="text-primary fs-5" id="modalTotalDisplay">Rp 0</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Pilihan Metode Pembayaran</label>
                                <div class="row g-2">
                                    {{-- Pembayaran digital otomatis --}}
                                    @if($infoPembayaran->isPaywuzEnabled() && count($paymentMethods) > 0)
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="methodPaywuz"
                                                value="paywuz" autocomplete="off" required>
                                            <label
                                                class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                                for="methodPaywuz">
                                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                <span class="small fw-bold">Pembayaran Digital</span>
                                            </label>
                                        </div>
                                    @endif

                                    {{-- Direct Transfer Option --}}
                                    @if($infoPembayaran->isDirectTransferEnabled())
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="methodTransfer"
                                                value="transfer" autocomplete="off" required>
                                            <label
                                                class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                                for="methodTransfer">
                                                <i class="fas fa-university fa-2x mb-2"></i>
                                                <span class="small fw-bold">Direct Transfer</span>
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
                                            class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3 cash-method-disabled"
                                            for="methodTunaiDummy">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                            <span class="small fw-bold">Tunai (Sekolah)</span>
                                        </label>
                                    </div>
                                    {{-- Tunai Option Removed from Selection --}}
                                </div>

                                    @if((!$infoPembayaran->isPaywuzEnabled() || count($paymentMethods) === 0) && !$infoPembayaran->isDirectTransferEnabled())
                                        <div class="alert alert-warning d-flex align-items-center mt-3 mb-0" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <div class="small">
                                                <strong>Tidak ada metode pembayaran online yang tersedia.</strong>
                                                Silakan lakukan pembayaran tunai di sekolah.
                                            </div>
                                        </div>
                                    @endif
                            </div>

                            {{-- CONTENT SECTIONS --}}

                            {{-- 1. Pembayaran digital --}}
                            <div id="infoPaywuz" class="method-info d-none">
                                <div class="alert alert-primary" role="alert">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-shield-alt me-2 mt-1"></i>
                                        <div class="small">Pilih kanal, lalu selesaikan pembayaran pada halaman aman. Status tagihan akan diperbarui <strong>otomatis</strong>.</div>
                                    </div>
                                    <label for="paymentMethod" class="form-label fw-bold">Kanal Pembayaran <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="paymentMethod" class="form-select">
                                        <option value="">Pilih kanal pembayaran</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method['code'] }}">
                                                {{ $method['name'] }}{{ $method['type'] === 'meta' ? ' — pilih bank di halaman berikutnya' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="d-block mt-2">Biaya kanal, jika ada, ditampilkan sebelum Anda menyelesaikan pembayaran.</small>
                                </div>
                            </div>

                            @if($infoPembayaran->isPaywuzEnabled() && count($paymentMethods) === 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Kanal pembayaran digital sedang tidak dapat dimuat.
                                    {{ $infoPembayaran->isDirectTransferEnabled() ? 'Silakan gunakan Direct Transfer atau coba kembali nanti.' : 'Silakan coba kembali nanti atau hubungi sekolah.' }}
                                </div>
                            @endif

                            {{-- 2. Direct Transfer Info --}}
                            @if($infoPembayaran->isDirectTransferEnabled())
                                <div id="infoTransfer" class="method-info d-none">
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
                                                        data-copy-rekening
                                                        data-copy-text="{{ $infoPembayaran->rekening_bank }}"
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
                                            Bukti Direct Transfer <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" name="bukti_bayar" id="bulkBuktiInput" class="form-control"
                                            accept="image/*">
                                        <small class="text-muted">Upload foto bukti Direct Transfer total nominal (Max: 10MB).</small>
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
                            <button type="submit" class="btn btn-primary" id="btnSubmitBulk"
                                @if((!$infoPembayaran->isPaywuzEnabled() || count($paymentMethods) === 0) && !$infoPembayaran->isDirectTransferEnabled())
                                    disabled
                                @endif
                            >
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
                                        $canContinue = $bayar->metode_pembayaran == 'paywuz'
                                            && $bayar->status_validasi == 'pending'
                                            && (!$bayar->payment_expires_at || $bayar->payment_expires_at->isFuture());

                                    @endphp
                                    <tr>
                                        <td data-label="TANGGAL" class="text-end text-md-start text-nowrap">
                                            {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y H:i') }}
                                        </td>
                                        <td data-label="TAGIHAN">
                                            <div class="fw-semibold">
                                                {{ $bayar->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan ?? '-')) }}
                                            </div>
                                            <small class="text-muted">ID: #{{ $bayar->kode_pembayaran }}</small>
                                        </td>
                                        <td data-label="JUMLAH" class="text-end fw-bold">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td data-label="METODE" class="text-end text-md-start">
                                            @if($bayar->metode_pembayaran == 'tunai')
                                                <span class="badge bg-label-secondary"><i class="fas fa-money-bill-wave me-1"></i> Tunai</span>
                                            @elseif($bayar->metode_pembayaran == 'transfer')
                                                <span class="badge bg-label-info"><i class="fas fa-university me-1"></i> Direct Transfer</span>
                                            @elseif($bayar->metode_pembayaran == 'paywuz')
                                                <span class="badge bg-label-primary"><i class="fas fa-credit-card me-1"></i> Digital</span>
                                            @endif
                                        </td>
                                        <td data-label="STATUS" class="text-end text-md-center">
                                            @if($bayar->status_validasi == 'disetujui')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                                            @elseif($bayar->status_validasi == 'ditolak')
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                                            @elseif($bayar->status_validasi == 'pending' && $bayar->metode_pembayaran == 'paywuz')
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
                                        <td data-label="AKSI" class="text-center">
                                            <div class="d-flex justify-content-center justify-content-md-center gap-2 payment-history-actions">
                                                @if($canContinue)
                                                    <form action="{{ route('wali-siswa.pembayaran.continue', $bayar->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary payment-history-action" title="Lanjutkan Pembayaran">
                                                        <i class="fas fa-credit-card"></i> Lanjut Bayar
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Tombol invoice untuk pembayaran non-tunai --}}
                                                @if($bayar->metode_pembayaran != 'tunai')
                                                    <a href="{{ route('wali-siswa.pembayaran.invoice', $bayar->id) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-secondary payment-history-action"
                                                       title="Lihat Invoice">
                                                        <i class="fas fa-file-invoice"></i> Invoice
                                                    </a>
                                                @endif
                                            </div>
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

@section('scripts')
    @vite(['resources/js/wali-siswa/tagihan/index.js'])
@endsection
