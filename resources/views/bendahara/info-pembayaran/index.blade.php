@extends('layouts.sneat')

@section('title', 'Info Pembayaran')
@section('page-title', 'Kelola Informasi Pembayaran')
@section('page-subtitle', 'Atur rekening bank dan konfigurasi Midtrans')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .label-config {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #b7b9cc;
        margin-bottom: 2px;
    }
    .value-config {
        font-weight: 700;
        color: #4e73df;
        margin-bottom: 15px;
    }
    .status-box {
        padding: 15px;
        border-radius: 10px;
        border: 2px solid #eaecf4;
        transition: all 0.3s;
    }
    .status-box.active { border-color: #1cc88a; background: #f6fff9; }
    .status-box.inactive { border-color: #e74a3b; background: #fff5f5; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- INFO BANNER --}}
    <div class="alert alert-primary border-start border-primary border-4 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
            <div>
                <strong class="text-primary fw-bold">Tentang Halaman Ini:</strong>
                <p class="mb-0 small text-gray-800">Halaman ini digunakan untuk mengelola informasi rekening bank dan konfigurasi payment gateway yang akan ditampilkan kepada siswa/orang tua saat melakukan pembayaran.</p>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- CARD: INFO REKENING BANK --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow">
                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-university me-2"></i>Rekening Bank Tujuan</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold shadow-sm" onclick="toggleEdit('rekening')">
                        <i class="fas fa-edit me-1"></i> Atur Rekening
                    </button>
                </div>
                <div class="card-body">
                    <div id="rekening-view">
                        @if($infoPembayaran && $infoPembayaran->rekening_bank)
                            <div class="p-3 bg-light rounded border">
                                <div class="label-config">Nama Bank</div>
                                <div class="value-config h5">{{ $infoPembayaran->nama_bank ?? '-' }}</div>

                                <div class="label-config">Nomor Rekening</div>
                                <div class="value-config h4 text-dark fw-bold" style="letter-spacing: 1px;">
                                    {{ $infoPembayaran->rekening_bank ?? '-' }}
                                </div>

                                <div class="label-config">Atas Nama (Beneficiary)</div>
                                <div class="value-config mb-0">{{ $infoPembayaran->atas_nama ?? '-' }}</div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning opacity-25 mb-3"></i>
                                <p class="text-gray-600 fw-bold">Belum ada informasi rekening bank.</p>
                            </div>
                        @endif
                    </div>

                    <div id="rekening-edit" style="display: none;">
                        <form action="{{ route('bendahara.info-pembayaran.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="rekening">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">NAMA BANK <span class="text-danger">*</span></label>
                                <input type="text" name="nama_bank" class="form-control border-start border-primary border-3 shadow-sm" value="{{ old('nama_bank', $infoPembayaran->nama_bank ?? '') }}" placeholder="Contoh: Bank Mandiri" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">NOMOR REKENING <span class="text-danger">*</span></label>
                                <input type="text" name="rekening_bank" class="form-control border-start border-primary border-3 shadow-sm" value="{{ old('rekening_bank', $infoPembayaran->rekening_bank ?? '') }}" placeholder="Contoh: 1234567890" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">ATAS NAMA <span class="text-danger">*</span></label>
                                <input type="text" name="atas_nama" class="form-control border-start border-primary border-3 shadow-sm" value="{{ old('atas_nama', $infoPembayaran->atas_nama ?? '') }}" placeholder="Contoh: Yayasan PKBM" required>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-bold shadow-sm flex-fill">SIMPAN PERUBAHAN</button>
                                <button type="button" class="btn btn-light border" onclick="toggleEdit('rekening')">BATAL</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: KONFIGURASI MIDTRANS --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow">
                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-success"><i class="fas fa-credit-card me-2"></i>Konfigurasi Midtrans Gateway</h6>
                    <button type="button" class="btn btn-sm btn-outline-success fw-bold shadow-sm" onclick="toggleEdit('midtrans')">
                        <i class="fas fa-cog me-1"></i> Edit API Keys
                    </button>
                </div>
                <div class="card-body">
                    <div id="midtrans-view">
                        @if($infoPembayaran && $infoPembayaran->midtrans_merchant_id)
                            <div class="p-3 bg-light rounded border">
                                <div class="label-config">Merchant ID</div>
                                <div class="value-config text-dark">{{ $infoPembayaran->midtrans_merchant_id ?? '-' }}</div>

                                <div class="label-config">Server Key</div>
                                <div class="value-config text-dark">
                                    {{ $infoPembayaran->midtrans_server_key ? '••••••••' . substr($infoPembayaran->midtrans_server_key, -6) : '-' }}
                                </div>

                                <div class="label-config">Environment Mode</div>
                                <div class="value-config mb-0">
                                    @if($infoPembayaran->midtrans_is_production)
                                        <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> PRODUCTION (LIVE)</span>
                                    @else
                                        <span class="badge bg-warning px-3 py-2 text-dark fw-bold"><i class="fas fa-vial me-1"></i> SANDBOX (TESTING)</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-shield-alt fa-3x text-success opacity-25 mb-3"></i>
                                <p class="text-gray-600 fw-bold">Konfigurasi payment gateway belum diatur.</p>
                            </div>
                        @endif
                    </div>

                    <div id="midtrans-edit" style="display: none;">
                        <form action="{{ route('bendahara.info-pembayaran.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="midtrans">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">MERCHANT ID <span class="text-danger">*</span></label>
                                <input type="text" name="midtrans_merchant_id" class="form-control border-start border-success border-3 shadow-sm" value="{{ old('midtrans_merchant_id', $infoPembayaran->midtrans_merchant_id ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">SERVER KEY <span class="text-danger">*</span></label>
                                <input type="password" name="midtrans_server_key" class="form-control border-start border-success border-3 shadow-sm" value="{{ old('midtrans_server_key', $infoPembayaran->midtrans_server_key ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">CLIENT KEY <span class="text-danger">*</span></label>
                                <input type="text" name="midtrans_client_key" class="form-control border-start border-success border-3 shadow-sm" value="{{ old('midtrans_client_key', $infoPembayaran->midtrans_client_key ?? '') }}" required>
                            </div>
                            <div class="form-check mb-3 mt-4">
                                <input type="checkbox" class="form-check-input" id="is_production" name="midtrans_is_production" value="1" {{ old('midtrans_is_production', $infoPembayaran->midtrans_is_production ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="is_production">Aktifkan Mode Production (Live Transaksi Nyata)</label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success fw-bold shadow-sm flex-fill">SIMPAN KONFIGURASI</button>
                                <button type="button" class="btn btn-light border" onclick="toggleEdit('midtrans')">BATAL</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PANDUAN PENGGUNAAN --}}
    <div class="row mt-2">
        <div class="col-md-4 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body bg-light">
                    <h6 class="fw-bold text-success"><i class="fas fa-clipboard-list me-2"></i>Rekening Bank</h6>
                    <p class="small text-gray-700 mb-0">Informasi ini akan muncul pada dashboard siswa sebagai instruksi transfer manual. Pastikan nomor rekening selalu up-to-date.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body bg-light">
                    <h6 class="fw-bold text-primary"><i class="fas fa-key me-2"></i>Keamanan API</h6>
                    <p class="small text-gray-700 mb-0">Jangan membagikan Server Key kepada siapapun. Kunci ini digunakan untuk otentikasi transaksi otomatis antara Midtrans dan sistem.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body bg-light">
                    <h6 class="fw-bold text-warning"><i class="fas fa-tools me-2"></i>Testing Sistem</h6>
                    <p class="small text-gray-700 mb-0">Gunakan mode Sandbox untuk simulasi pembayaran tanpa uang sungguhan untuk memastikan alur validasi otomatis berjalan benar.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATUS SISTEM REALTIME --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-satellite-dish me-2"></i>Integrasi Kanal Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="status-box {{ ($infoPembayaran && $infoPembayaran->rekening_bank) ? 'active' : 'inactive' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-university fa-2x me-3 text-{{ ($infoPembayaran && $infoPembayaran->rekening_bank) ? 'success' : 'danger' }}"></i>
                            <div>
                                <div class="fw-bold text-gray-800">Transfer Manual</div>
                                <div class="small">{{ ($infoPembayaran && $infoPembayaran->rekening_bank) ? 'Kanal Aktif' : 'Belum Terhubung' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="status-box {{ ($infoPembayaran && $infoPembayaran->midtrans_merchant_id) ? 'active' : 'inactive' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-credit-card fa-2x me-3 text-{{ ($infoPembayaran && $infoPembayaran->midtrans_merchant_id) ? 'success' : 'danger' }}"></i>
                            <div>
                                <div class="fw-bold text-gray-800">Gateway Midtrans</div>
                                <div class="small">{{ ($infoPembayaran && $infoPembayaran->midtrans_merchant_id) ? 'Kanal Aktif' : 'Belum Terhubung' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="status-box active">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-money-bill-wave fa-2x me-3 text-success"></i>
                            <div>
                                <div class="fw-bold text-gray-800">Pembayaran Kasir</div>
                                <div class="small">Kanal Selalu Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script>
function toggleEdit(type) {
    const viewDiv = document.getElementById(type + '-view');
    const editDiv = document.getElementById(type + '-edit');

    if (viewDiv.style.display === 'none') {
        viewDiv.style.display = 'block';
        editDiv.style.display = 'none';
    } else {
        viewDiv.style.display = 'none';
        editDiv.style.display = 'block';
    }
}
</script>
@endsection