@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('page-subtitle', 'Validasi pembayaran siswa')


@section('styles')
    @vite(['resources/css/bendahara/pembayaran/show.css'])
@endsection

@section('content')
<div class="pembayaran-show-page">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('bendahara.pembayaran.index') }}" class="text-primary text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pembayaran
        </a>
    </div>

    <div class="row">
        {{-- Info Pembayaran --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-receipt me-2"></i>Informasi Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    <table class="info-table w-100">
                        <tr>
                            <td>Kode Pembayaran</td>
                            <td><code class="fw-bold text-primary small">{{ $pembayaran->kode_pembayaran }}</code></td>
                        </tr>
                        <tr>
                            <td>Jenis Tagihan</td>
                            <td><strong>{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Jumlah Bayar</td>
                            <td class="fs-5 fw-bold text-success">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Bayar</td>
                            <td>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Metode</td>
                            <td>
                                @if($pembayaran->metode_pembayaran === 'tunai')
                                    <span class="badge bg-primary badge-custom shadow-sm">TUNAI</span>
                                @elseif($pembayaran->metode_pembayaran === 'transfer')
                                    <span class="badge bg-success badge-custom shadow-sm">DIRECT TRANSFER</span>
                                @else
                                    <x-payment-method-badge :payment="$pembayaran" class="badge-custom shadow-sm" />
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <x-payment-status-badge :payment="$pembayaran" class="badge-custom shadow-sm" />
                                @if($pembayaran->status_validasi === 'pending')
                                    @if($pembayaran->metode_pembayaran === 'paywuz')
                                        @php
                                            $isExpired = $pembayaran->payment_expires_at?->isPast() ?? false;
                                        @endphp
                                        @if($isExpired)
                                            <div class="small text-muted mt-1">Sesi pembayaran digital telah berakhir</div>
                                        @else
                                            <div class="small text-muted mt-1">
                                                @if($pembayaran->payment_expires_at)
                                                    Berlaku hingga {{ $pembayaran->payment_expires_at->format('d M Y H:i') }}
                                                @else
                                                    Menunggu kanal pembayaran dibuat
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @if($pembayaran->status_validasi !== 'pending')
                            <tr>
                                <td>Divalidasi Oleh</td>
                                <td>{{ $pembayaran->validator->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Validasi</td>
                                <td>{{ $pembayaran->tanggal_validasi ? $pembayaran->tanggal_validasi->format('d F Y H:i') : '-' }}</td>
                            </tr>
                        @endif
                        @if($pembayaran->catatan)
                            <tr>
                                <td>Catatan</td>
                                <td>{{ $pembayaran->catatan }}</td>
                            </tr>
                        @endif
                    </table>

                    @if($pembayaran->bukti_pembayaran)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3 fw-bold text-gray-800"><i class="fas fa-paperclip me-2"></i>Bukti Pembayaran</h6>
                            <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-info shadow-sm">
                                <i class="fas fa-eye me-1"></i> Lihat Bukti
                            </a>
                        </div>
                    @endif

                    @if($pembayaran->status_validasi === 'disetujui')
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3 fw-bold text-gray-800"><i class="fas fa-print me-2"></i>Cetak Kwitansi</h6>
                            <a href="{{ route('bendahara.pembayaran.cetak-kwitansi', $pembayaran->id) }}" 
                               target="_blank" 
                               class="btn btn-success shadow-sm">
                                <i class="fas fa-print me-1"></i> Cetak Kwitansi Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Siswa --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-user-graduate me-2"></i>Informasi Siswa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-start mb-4">
                        <div class="student-avatar">
                            {{ strtoupper(substr($pembayaran->siswa->nama_lengkap ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-gray-800">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</h5>
                            <p class="mb-0 text-muted small">NISN: {{ $pembayaran->siswa->nisn ?? '-' }}</p>
                        </div>
                    </div>

                    <table class="info-table w-100">
                        <tr>
                            <td class="student-class-label">Kelas</td>
                            <td>{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }} ({{ $pembayaran->siswa->kelas->jenjang ?? '-' }})</td>
                        </tr>
                        <tr>
                            <td>Cabang</td>
                            <td>{{ $pembayaran->siswa->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>{{ $pembayaran->siswa->telepon_orangtua ?? '-' }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $pembayaran->siswa->id ?? 0) }}" class="btn btn-sm btn-info shadow-sm">
                            <i class="fas fa-history me-1"></i> Lihat Riwayat Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cek status kadaluarsa --}}
    @php
        $isDigital = $pembayaran->payment_gateway === 'paywuz';
        $isKadaluarsa = $isDigital && $pembayaran->status_validasi === 'pending' && ($pembayaran->payment_expires_at?->isPast() ?? false);
    @endphp

    {{-- Info Kadaluarsa --}}
    @if($isKadaluarsa)
        <div class="card shadow mb-4 expired-payment-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-ban fa-2x text-secondary me-3"></i>
                    <div>
                        <h6 class="fw-bold text-secondary mb-1">Pembayaran Kadaluarsa</h6>
                        <p class="mb-0 text-muted small">
                            Sesi pembayaran digital ini telah melewati batas waktu kanal dan tidak memerlukan validasi manual.
                            Siswa perlu membuat transaksi pembayaran baru jika ingin melanjutkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Validasi (jika masih pending dan belum kadaluarsa) --}}
    @if($pembayaran->status_validasi === 'pending' && !$isKadaluarsa && !$isDigital)
        <div class="card shadow mb-4 validation-card">
            <div class="card-body">
                <h5 class="mb-4 fw-bold">
                    <i class="fas fa-check-circle me-2"></i>Validasi Pembayaran
                </h5>

                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan (Opsional)</label>
                    <textarea id="catatanValidasi" class="form-control shadow-sm validation-note-input" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-light shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#setujuiModal">
                        <i class="fas fa-check me-1"></i> Setujui Pembayaran
                    </button>
                    <button type="button" class="btn btn-outline-light shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#tolakModal">
                        <i class="fas fa-times me-1"></i> Tolak Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Info Tagihan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-warning">
                <i class="fas fa-file-invoice-dollar me-2"></i>Informasi Tagihan Terkait
            </h6>
        </div>
        <div class="card-body">
            @if($pembayaran->tagihan)
                <table class="info-table w-100">
                    <tr>
                        <td>Jenis Tagihan</td>
                        <td><strong>{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan)) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Jumlah Tagihan</td>
                        <td class="fw-bold">Rp {{ number_format($pembayaran->tagihan->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Jatuh Tempo</td>
                        <td>{{ $pembayaran->tagihan->tanggal_jatuh_tempo ? $pembayaran->tagihan->tanggal_jatuh_tempo->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Status Tagihan</td>
                        <td>
                            @if($pembayaran->tagihan->status === 'sudah_bayar')
                                <span class="badge bg-success shadow-sm">Lunas</span>
                            @elseif($pembayaran->tagihan->status === 'terlambat')
                                <span class="badge bg-danger shadow-sm">Terlambat</span>
                            @else
                                <span class="badge bg-warning shadow-sm">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                </table>
            @else
                <p class="text-muted mb-0">Tagihan tidak ditemukan.</p>
            @endif
        </div>
    </div>

</div>
</div>

@if($pembayaran->status_validasi === 'pending' && !$isKadaluarsa && !$isDigital)
{{-- Modal Setujui --}}
<div class="modal fade" id="setujuiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Setujui Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h6 class="fw-bold mb-2">Setujui pembayaran ini?</h6>
                <p class="text-muted small mb-0">
                    Status tagihan akan diubah menjadi <strong>Lunas</strong> dan bukti pembayaran diterima.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="formSetujui" action="{{ route('bendahara.pembayaran.validasi', $pembayaran->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status_validasi" value="disetujui">
                    <input type="hidden" name="catatan" id="catatanSetujui">
                    <input type="hidden" name="_return_url" value="{{ url()->previous(route('bendahara.pembayaran.index')) }}">
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fas fa-check me-1"></i> Ya, Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="tolakModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-times-circle me-2"></i>Konfirmasi Tolak Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h6 class="fw-bold mb-1">Tolak pembayaran ini?</h6>
                    <p class="text-muted small">Siswa akan diberitahu bahwa pembayarannya ditolak.</p>
                </div>
                <div>
                    <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea id="alasanTolak" class="form-control" rows="3" placeholder="Tuliskan alasan penolakan..."></textarea>
                    <div class="invalid-feedback" id="alasanError">Alasan penolakan wajib diisi.</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="formTolak" action="{{ route('bendahara.pembayaran.validasi', $pembayaran->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status_validasi" value="ditolak">
                    <input type="hidden" name="catatan" id="catatanTolak">
                    <input type="hidden" name="_return_url" value="{{ url()->previous(route('bendahara.pembayaran.index')) }}">
                    <button type="button" class="btn btn-danger fw-bold" data-submit-tolak>
                        <i class="fas fa-times me-1"></i> Ya, Tolak
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
    @vite(['resources/js/bendahara/pembayaran/show.js'])
@endsection
