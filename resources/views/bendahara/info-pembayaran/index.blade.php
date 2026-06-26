@extends('layouts.sneat')

@section('title', 'Info Pembayaran')
@section('page-title', 'Kelola Informasi Pembayaran')
@section('page-subtitle', 'Atur rekening bank, pembayaran tunai, dan konfigurasi Midtrans')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/info-pembayaran/index.css'])
@endsection

@section('content')
@php
    $hasRekening = $infoPembayaran && $infoPembayaran->rekening_bank;
    $midtransConfigured = $infoPembayaran && $infoPembayaran->hasMidtrans();
    $midtransEnabled = $infoPembayaran && $infoPembayaran->isMidtransEnabled();
    $isProduction = (bool) old('midtrans_is_production', $infoPembayaran->midtrans_is_production ?? false);
    $tunaiInfo = $infoPembayaran->tunai_info ?? [
        'lokasi' => $infoPembayaran->tunai_lokasi ?? 'Loket Pembayaran Sekolah',
        'jam_operasional' => $infoPembayaran->tunai_jam_operasional ?? 'Senin - Jumat, 08:00 - 15:00 WIB',
        'deskripsi' => $infoPembayaran->tunai_deskripsi ?? 'Harap membawa kartu siswa atau bukti identitas.',
    ];
@endphp

<div class="pay-shell">
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-blue">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <div class="stat-value">{{ $hasRekening ? 'Aktif' : 'Belum' }}</div>
                <div class="stat-label">Direct Transfer</div>
                <div class="stat-desc">{{ $hasRekening ? ($infoPembayaran->nama_bank ?? 'Rekening tersimpan') : 'Rekening belum diatur' }}</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-green">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="stat-value">{{ $midtransEnabled ? 'Aktif' : ($midtransConfigured ? 'Off' : 'Belum') }}</div>
                <div class="stat-label">Gateway Midtrans</div>
                <div class="stat-desc">{{ $midtransConfigured ? ($isProduction ? 'Mode production' : 'Mode sandbox') : 'API key belum lengkap' }}</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-orange">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="stat-value">Aktif</div>
                <div class="stat-label">Pembayaran Tunai</div>
                <div class="stat-desc">{{ $tunaiInfo['lokasi'] ?? 'Loket Pembayaran' }}</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-purple">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <div class="stat-value">{{ $midtransConfigured && $hasRekening ? 'Siap' : 'Cek' }}</div>
                <div class="stat-label">Kesiapan Kanal</div>
                <div class="stat-desc">{{ $midtransConfigured && $hasRekening ? 'Semua kanal utama tersedia' : 'Ada kanal yang perlu dilengkapi' }}</div>
            </div>
        </div>
    </div>

    <div class="pay-grid">
        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-university pay-card-title-icon-primary"></i> Rekening Bank Tujuan</h5>
                    <div class="pay-card-subtitle">Ditampilkan untuk instruksi Direct Transfer.</div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm btn-pay-action" data-toggle-edit data-type="rekening">
                    <i class="fas fa-edit"></i> Atur Rekening
                </button>
            </div>
            <div class="pay-card-body">
                <div id="rekening-view" class="pay-view-panel">
                    @if($hasRekening)
                        <div class="pay-info-panel">
                            <div>
                                <div class="pay-field-label">Nama Bank</div>
                                <div class="pay-field-value">{{ $infoPembayaran->nama_bank ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="pay-field-label">Nomor Rekening</div>
                                <div class="pay-field-value pay-field-account-number fs-4">{{ $infoPembayaran->rekening_bank ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="pay-field-label">Atas Nama</div>
                                <div class="pay-field-value">{{ $infoPembayaran->atas_nama ?? '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="pay-empty">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h6 class="mb-1">Belum ada rekening bank</h6>
                            <p class="small mb-0">Lengkapi data rekening agar wali siswa mendapat instruksi Direct Transfer.</p>
                        </div>
                    @endif
                </div>

                <div id="rekening-edit" class="pay-edit-panel">
                    <form action="{{ route('bendahara.info-pembayaran.update') }}" method="POST" class="pay-form-grid">
                        @csrf
                        <input type="hidden" name="type" value="rekening">
                        <div>
                            <label class="form-label fw-semibold">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" name="nama_bank" class="form-control" value="{{ old('nama_bank', $infoPembayaran->nama_bank ?? '') }}" placeholder="Contoh: Bank Mandiri" required>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Nomor Rekening <span class="text-danger">*</span></label>
                            <input type="text" name="rekening_bank" class="form-control" value="{{ old('rekening_bank', $infoPembayaran->rekening_bank ?? '') }}" placeholder="Contoh: 1234567890" required>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Atas Nama <span class="text-danger">*</span></label>
                            <input type="text" name="atas_nama" class="form-control" value="{{ old('atas_nama', $infoPembayaran->atas_nama ?? '') }}" placeholder="Contoh: Yayasan PKBM" required>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary btn-pay-action flex-fill justify-content-center"><i class="fas fa-save"></i> Simpan Perubahan</button>
                            <button type="button" class="btn btn-light border btn-pay-action" data-toggle-edit data-type="rekening">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-credit-card pay-card-title-icon-success"></i> Konfigurasi Midtrans</h5>
                    <div class="pay-card-subtitle">Kelola payment gateway dan mode transaksi.</div>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm btn-pay-action" data-toggle-edit data-type="midtrans">
                    <i class="fas fa-cog"></i> Edit API Keys
                </button>
            </div>
            <div class="pay-card-body">
                <div id="midtrans-view" class="pay-view-panel">
                    @if($midtransConfigured)
                        <div class="pay-info-panel">
                            <div>
                                <div class="pay-field-label">Merchant ID</div>
                                <div class="pay-field-value">{{ $infoPembayaran->midtrans_merchant_id ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="pay-field-label">Server Key</div>
                                <div class="pay-field-value">{{ $infoPembayaran->midtrans_server_key ? '********' . substr($infoPembayaran->midtrans_server_key, -6) : '-' }}</div>
                            </div>
                            <div>
                                <div class="pay-field-label">Environment Mode</div>
                                @if($infoPembayaran->midtrans_is_production)
                                    <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Production</span>
                                @else
                                    <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-vial me-1"></i> Sandbox</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="pay-empty">
                            <i class="fas fa-shield-alt"></i>
                            <h6 class="mb-1">Payment gateway belum diatur</h6>
                            <p class="small mb-0">Isi Merchant ID, Server Key, dan Client Key untuk mengaktifkan Midtrans.</p>
                        </div>
                    @endif
                </div>

                <div id="midtrans-edit" class="pay-edit-panel">
                    <form action="{{ route('bendahara.info-pembayaran.update') }}" method="POST" class="pay-form-grid">
                        @csrf
                        <input type="hidden" name="type" value="midtrans">
                        <div class="alert alert-warning border-0 mb-0">
                            <div class="d-flex gap-2">
                                <i class="fas fa-exclamation-triangle mt-1"></i>
                                <small>API Keys untuk Sandbox dan Production berbeda. Pastikan key yang disimpan sesuai mode yang dipilih dari <a href="https://dashboard.midtrans.com" target="_blank" class="fw-bold">Midtrans Dashboard</a>.</small>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Merchant ID <span class="text-danger">*</span></label>
                            <input type="text" name="midtrans_merchant_id" class="form-control" value="{{ old('midtrans_merchant_id', $infoPembayaran->midtrans_merchant_id ?? '') }}" placeholder="Contoh: G12345678" required>
                            <small class="text-muted">Merchant ID sama untuk Sandbox dan Production.</small>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Server Key <span class="text-danger">*</span></label>
                            <input type="password" name="midtrans_server_key" class="form-control" placeholder="Mid-server-xxxxxxxx" required>
                            <small class="text-muted">Awali dengan <code>Mid-server-</code>.</small>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Client Key <span class="text-danger">*</span></label>
                            <input type="text" name="midtrans_client_key" class="form-control" placeholder="Mid-client-xxxxxxxx" required>
                            <small class="text-muted">Awali dengan <code>Mid-client-</code>.</small>
                        </div>
                        <div class="mode-card" id="modeSelectionCard">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" role="switch" id="is_production" name="midtrans_is_production" value="1" {{ $isProduction ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_production" id="modeLabel">
                                    @if($isProduction)
                                        <span class="text-danger"><i class="fas fa-broadcast-tower me-1"></i> Mode Production</span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-vial me-1"></i> Mode Sandbox</span>
                                    @endif
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1" id="modeDescription">
                                {{ $isProduction ? 'Transaksi nyata dengan uang sungguhan.' : 'Simulasi pembayaran untuk testing.' }}
                            </small>
                        </div>
                        <div class="accordion" id="accordionGuide">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#guideCollapse">
                                        <i class="fas fa-question-circle me-2 text-info"></i> Cara mendapatkan API Keys
                                    </button>
                                </h2>
                                <div id="guideCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionGuide">
                                    <div class="accordion-body small bg-light">
                                        <ol class="mb-0 ps-3">
                                            <li>Login ke <a href="https://dashboard.midtrans.com" target="_blank">dashboard.midtrans.com</a>.</li>
                                            <li>Pilih environment Sandbox atau Production.</li>
                                            <li>Buka Settings lalu Access Keys.</li>
                                            <li>Salin Merchant ID, Server Key, dan Client Key.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-success btn-pay-action flex-fill justify-content-center"><i class="fas fa-save"></i> Simpan Konfigurasi</button>
                            <button type="button" class="btn btn-light border btn-pay-action" data-toggle-edit data-type="midtrans">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-money-bill-wave pay-card-title-icon-warning"></i> Pembayaran Tunai</h5>
                    <div class="pay-card-subtitle">Informasi loket untuk pembayaran langsung.</div>
                </div>
                <button type="button" class="btn btn-outline-warning btn-sm btn-pay-action" data-toggle-edit data-type="tunai">
                    <i class="fas fa-edit"></i> Edit Info
                </button>
            </div>
            <div class="pay-card-body">
                <div id="tunai-view" class="pay-view-panel">
                    <div class="pay-info-panel">
                        <div>
                            <div class="pay-field-label">Lokasi Pembayaran</div>
                            <div class="pay-field-value"><i class="fas fa-building me-1 text-muted"></i>{{ $tunaiInfo['lokasi'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pay-field-label">Jam Operasional</div>
                            <div class="pay-field-value"><i class="fas fa-clock me-1 text-muted"></i>{{ $tunaiInfo['jam_operasional'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pay-field-label">Deskripsi / Petunjuk</div>
                            <div class="pay-muted-value"><i class="fas fa-info-circle me-1 text-muted"></i>{{ $tunaiInfo['deskripsi'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div id="tunai-edit" class="pay-edit-panel">
                    <form action="{{ route('bendahara.info-pembayaran.update') }}" method="POST" class="pay-form-grid">
                        @csrf
                        <input type="hidden" name="type" value="tunai">
                        <div>
                            <label class="form-label fw-semibold">Lokasi Pembayaran</label>
                            <input type="text" name="tunai_lokasi" class="form-control" value="{{ old('tunai_lokasi', $infoPembayaran->tunai_lokasi ?? '') }}" placeholder="Contoh: Loket Pembayaran Sekolah">
                            <small class="text-muted">Kosongkan untuk menggunakan default.</small>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Jam Operasional</label>
                            <input type="text" name="tunai_jam_operasional" class="form-control" value="{{ old('tunai_jam_operasional', $infoPembayaran->tunai_jam_operasional ?? '') }}" placeholder="Contoh: Senin - Jumat, 08:00 - 15:00 WIB">
                            <small class="text-muted">Kosongkan untuk menggunakan default.</small>
                        </div>
                        <div>
                            <label class="form-label fw-semibold">Deskripsi / Petunjuk</label>
                            <textarea name="tunai_deskripsi" class="form-control" rows="3" placeholder="Contoh: Harap membawa kartu siswa atau bukti identitas.">{{ old('tunai_deskripsi', $infoPembayaran->tunai_deskripsi ?? '') }}</textarea>
                            <small class="text-muted">Kosongkan untuk menggunakan default.</small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-warning btn-pay-action flex-fill justify-content-center text-dark"><i class="fas fa-save"></i> Simpan Perubahan</button>
                            <button type="button" class="btn btn-light border btn-pay-action" data-toggle-edit data-type="tunai">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-satellite-dish pay-card-title-icon-info"></i> Integrasi Kanal Pembayaran</h5>
                    <div class="pay-card-subtitle">Status kanal yang tersedia untuk siswa dan wali siswa.</div>
                </div>
            </div>
            <div class="pay-card-body">
                <div class="pay-status-grid">
                    <div class="pay-status-item {{ $hasRekening ? 'is-active' : 'is-inactive' }}">
                        <div class="pay-status-main">
                            <div class="pay-status-icon {{ $hasRekening ? 'pay-status-icon-active' : 'pay-status-icon-inactive' }}">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <div class="pay-status-title">Direct Transfer</div>
                                <div class="pay-status-desc">{{ $hasRekening ? 'Kanal aktif' : 'Belum terhubung' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="pay-status-item {{ $midtransEnabled ? 'is-active' : 'is-inactive' }}">
                        <div class="pay-status-main">
                            <div class="pay-status-icon {{ $midtransEnabled ? 'pay-status-icon-active' : 'pay-status-icon-inactive' }}">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div>
                                <div class="pay-status-title">Gateway Midtrans</div>
                                <div class="pay-status-desc">{{ !$midtransConfigured ? 'Belum terhubung' : ($midtransEnabled ? 'Kanal aktif' : 'Dinonaktifkan') }}</div>
                            </div>
                        </div>
                        @if($midtransConfigured)
                            <form action="{{ route('bendahara.info-pembayaran.update') }}" method="POST" class="ms-auto">
                                @csrf
                                <input type="hidden" name="type" value="midtrans_toggle">
                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" class="form-check-input" role="switch" id="midtransEnabledToggle" name="midtrans_enabled" value="1" {{ $infoPembayaran->midtrans_enabled ? 'checked' : '' }} title="{{ $infoPembayaran->midtrans_enabled ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan' }}" data-submit-on-change>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="pay-status-item is-active">
                        <div class="pay-status-main">
                            <div class="pay-status-icon pay-status-icon-active">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="pay-status-title">Pembayaran Kasir</div>
                                <div class="pay-status-desc">Kanal selalu aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pay-guide-grid">
        <div class="pay-note-card">
            <div class="pay-note-icon pay-note-icon-success"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <h6 class="mb-1 fw-bold">Rekening Bank</h6>
                <p class="small text-muted mb-0">Pastikan nomor rekening dan nama penerima selalu sesuai informasi sekolah terbaru.</p>
            </div>
        </div>
        <div class="pay-note-card">
            <div class="pay-note-icon pay-note-icon-primary"><i class="fas fa-key"></i></div>
            <div>
                <h6 class="mb-1 fw-bold">Keamanan API</h6>
                <p class="small text-muted mb-0">Server Key hanya dipakai di sistem dan tidak boleh dibagikan ke pihak luar.</p>
            </div>
        </div>
        <div class="pay-note-card">
            <div class="pay-note-icon pay-note-icon-warning"><i class="fas fa-tools"></i></div>
            <div>
                <h6 class="mb-1 fw-bold">Testing Sistem</h6>
                <p class="small text-muted mb-0">Gunakan mode Sandbox untuk uji alur pembayaran sebelum membuka transaksi live.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/bendahara/info-pembayaran/index.js'])
@endsection
