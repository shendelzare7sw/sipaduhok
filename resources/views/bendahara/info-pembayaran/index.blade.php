@extends('layouts.sneat')

@section('title', 'Info Pembayaran')
@section('page-title', 'Kelola Informasi Pembayaran')
@section('page-subtitle', 'Atur rekening bank, pembayaran tunai, dan konfigurasi Midtrans')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --pay-primary: #4361ee;
        --pay-success: #10b981;
        --pay-warning: #f59e0b;
        --pay-danger: #ef4444;
        --pay-info: #06b6d4;
        --pay-purple: #8b5cf6;
        --pay-surface: #ffffff;
        --pay-bg: #f8fafc;
        --pay-border: #e2e8f0;
        --pay-text: #1e293b;
        --pay-muted: #64748b;
        --pay-radius: 12px;
    }

    .pay-shell {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .stat-widget,
    .pay-card,
    .pay-note-card {
        background: var(--pay-surface);
        border: 1px solid var(--pay-border);
        border-radius: var(--pay-radius);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .stat-widget {
        padding: 1.35rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.2s ease;
    }

    .stat-widget:hover { transform: translateY(-2px); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--pay-text);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--pay-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--pay-muted);
        margin-top: 0.15rem;
    }

    .pay-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.5rem;
        align-items: stretch;
    }

    .pay-card {
        overflow: hidden;
        min-width: 0;
    }

    .pay-card-header {
        padding: 1.2rem 1.35rem;
        border-bottom: 1px solid var(--pay-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .pay-card-title {
        margin: 0;
        color: var(--pay-text);
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .pay-card-subtitle {
        margin-top: 0.25rem;
        color: var(--pay-muted);
        font-size: 0.8rem;
    }

    .pay-card-body {
        padding: 1.35rem;
    }

    .pay-info-panel {
        border: 1px solid var(--pay-border);
        border-radius: 10px;
        background: var(--pay-bg);
        padding: 1.1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .pay-field-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pay-muted);
        margin-bottom: 0.25rem;
    }

    .pay-field-value {
        color: var(--pay-text);
        font-weight: 700;
        word-break: break-word;
    }

    .pay-muted-value {
        color: var(--pay-muted);
        font-weight: 500;
    }

    .pay-empty {
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--pay-muted);
        background: var(--pay-bg);
        border: 1px dashed var(--pay-border);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .pay-empty i {
        font-size: 2rem;
        color: #cbd5e1;
        margin-bottom: 0.75rem;
    }

    .pay-form-grid {
        display: grid;
        gap: 1rem;
    }

    .pay-form-grid .form-control,
    .pay-form-grid .form-select {
        border-color: var(--pay-border);
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .pay-form-grid .form-control:focus,
    .pay-form-grid .form-select:focus {
        border-color: var(--pay-primary);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.12);
    }

    .pay-guide-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .pay-note-card {
        padding: 1.15rem;
        display: flex;
        gap: 0.9rem;
        align-items: flex-start;
    }

    .pay-note-icon,
    .pay-status-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pay-status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 1rem;
    }

    .pay-status-item {
        border: 1px solid var(--pay-border);
        border-radius: 10px;
        padding: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        min-width: 0;
        min-height: 118px;
        overflow: hidden;
    }

    .pay-status-main {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        min-width: 0;
        flex: 1 1 auto;
    }

    .pay-status-main > div:last-child {
        min-width: 0;
    }

    .pay-status-title {
        color: var(--pay-text);
        font-weight: 700;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .pay-status-desc {
        color: var(--pay-muted);
        font-size: 0.78rem;
        margin-top: 0.2rem;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .pay-status-item form {
        flex: 0 0 auto;
        margin-left: 0.35rem;
    }

    .pay-status-item .form-check-input {
        cursor: pointer;
    }

    .pay-status-item.is-active {
        background: #ecfdf5;
        border-color: #bbf7d0;
    }

    .pay-status-item.is-inactive {
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .btn-pay-action {
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .mode-card {
        border: 1px solid var(--pay-border);
        border-radius: 10px;
        padding: 0.9rem;
        background: var(--pay-bg);
    }

    .accordion-button {
        border-radius: 8px !important;
        font-size: 0.85rem;
        color: var(--pay-text);
        background: var(--pay-bg);
    }

    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .pay-grid,
        .pay-guide-grid { grid-template-columns: 1fr; }
        .pay-status-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .pay-card-header { align-items: stretch; }
        .pay-card-header > div:first-child,
        .pay-card-header .btn { width: 100%; }
        .pay-card-body { padding: 1rem; }
        .pay-status-item { align-items: flex-start; }
        .pay-status-main { align-items: flex-start; }
        .btn-pay-action { justify-content: center; }
        .pay-note-card { padding: 1rem; }
    }
</style>
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
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <div class="stat-value">{{ $hasRekening ? 'Aktif' : 'Belum' }}</div>
                <div class="stat-label">Transfer Manual</div>
                <div class="stat-desc">{{ $hasRekening ? ($infoPembayaran->nama_bank ?? 'Rekening tersimpan') : 'Rekening belum diatur' }}</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="stat-value">{{ $midtransEnabled ? 'Aktif' : ($midtransConfigured ? 'Off' : 'Belum') }}</div>
                <div class="stat-label">Gateway Midtrans</div>
                <div class="stat-desc">{{ $midtransConfigured ? ($isProduction ? 'Mode production' : 'Mode sandbox') : 'API key belum lengkap' }}</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="stat-value">Aktif</div>
                <div class="stat-label">Pembayaran Tunai</div>
                <div class="stat-desc">{{ $tunaiInfo['lokasi'] ?? 'Loket Pembayaran' }}</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
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
                    <h5 class="pay-card-title"><i class="fas fa-university" style="color: var(--pay-primary);"></i> Rekening Bank Tujuan</h5>
                    <div class="pay-card-subtitle">Ditampilkan untuk instruksi transfer manual.</div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm btn-pay-action" onclick="toggleEdit('rekening')">
                    <i class="fas fa-edit"></i> Atur Rekening
                </button>
            </div>
            <div class="pay-card-body">
                <div id="rekening-view">
                    @if($hasRekening)
                        <div class="pay-info-panel">
                            <div>
                                <div class="pay-field-label">Nama Bank</div>
                                <div class="pay-field-value">{{ $infoPembayaran->nama_bank ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="pay-field-label">Nomor Rekening</div>
                                <div class="pay-field-value fs-4" style="letter-spacing: 1px;">{{ $infoPembayaran->rekening_bank ?? '-' }}</div>
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
                            <p class="small mb-0">Lengkapi data rekening agar orang tua mendapat instruksi transfer.</p>
                        </div>
                    @endif
                </div>

                <div id="rekening-edit" style="display: none;">
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
                            <button type="button" class="btn btn-light border btn-pay-action" onclick="toggleEdit('rekening')">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-credit-card" style="color: var(--pay-success);"></i> Konfigurasi Midtrans</h5>
                    <div class="pay-card-subtitle">Kelola payment gateway dan mode transaksi.</div>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm btn-pay-action" onclick="toggleEdit('midtrans')">
                    <i class="fas fa-cog"></i> Edit API Keys
                </button>
            </div>
            <div class="pay-card-body">
                <div id="midtrans-view">
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

                <div id="midtrans-edit" style="display: none;">
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
                                <input type="checkbox" class="form-check-input" role="switch" id="is_production" name="midtrans_is_production" value="1" {{ $isProduction ? 'checked' : '' }} onchange="updateModeDisplay()">
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
                            <button type="button" class="btn btn-light border btn-pay-action" onclick="toggleEdit('midtrans')">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-money-bill-wave" style="color: var(--pay-warning);"></i> Pembayaran Tunai</h5>
                    <div class="pay-card-subtitle">Informasi loket untuk pembayaran langsung.</div>
                </div>
                <button type="button" class="btn btn-outline-warning btn-sm btn-pay-action" onclick="toggleEdit('tunai')">
                    <i class="fas fa-edit"></i> Edit Info
                </button>
            </div>
            <div class="pay-card-body">
                <div id="tunai-view">
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

                <div id="tunai-edit" style="display: none;">
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
                            <button type="button" class="btn btn-light border btn-pay-action" onclick="toggleEdit('tunai')">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div>
                    <h5 class="pay-card-title"><i class="fas fa-satellite-dish" style="color: var(--pay-info);"></i> Integrasi Kanal Pembayaran</h5>
                    <div class="pay-card-subtitle">Status kanal yang tersedia untuk siswa dan orang tua.</div>
                </div>
            </div>
            <div class="pay-card-body">
                <div class="pay-status-grid">
                    <div class="pay-status-item {{ $hasRekening ? 'is-active' : 'is-inactive' }}">
                        <div class="pay-status-main">
                            <div class="pay-status-icon" style="background: {{ $hasRekening ? '#d1fae5' : '#ffedd5' }}; color: {{ $hasRekening ? '#059669' : '#ea580c' }};">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <div class="pay-status-title">Transfer Manual</div>
                                <div class="pay-status-desc">{{ $hasRekening ? 'Kanal aktif' : 'Belum terhubung' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="pay-status-item {{ $midtransEnabled ? 'is-active' : 'is-inactive' }}">
                        <div class="pay-status-main">
                            <div class="pay-status-icon" style="background: {{ $midtransEnabled ? '#d1fae5' : '#ffedd5' }}; color: {{ $midtransEnabled ? '#059669' : '#ea580c' }};">
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
                                    <input type="checkbox" class="form-check-input" role="switch" id="midtransEnabledToggle" name="midtrans_enabled" value="1" {{ $infoPembayaran->midtrans_enabled ? 'checked' : '' }} onchange="this.form.submit()" title="{{ $infoPembayaran->midtrans_enabled ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan' }}">
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="pay-status-item is-active">
                        <div class="pay-status-main">
                            <div class="pay-status-icon" style="background: #d1fae5; color: #059669;">
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
            <div class="pay-note-icon" style="background: #ecfdf5; color: var(--pay-success);"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <h6 class="mb-1 fw-bold">Rekening Bank</h6>
                <p class="small text-muted mb-0">Pastikan nomor rekening dan nama penerima selalu sesuai informasi sekolah terbaru.</p>
            </div>
        </div>
        <div class="pay-note-card">
            <div class="pay-note-icon" style="background: #eff6ff; color: var(--pay-primary);"><i class="fas fa-key"></i></div>
            <div>
                <h6 class="mb-1 fw-bold">Keamanan API</h6>
                <p class="small text-muted mb-0">Server Key hanya dipakai di sistem dan tidak boleh dibagikan ke pihak luar.</p>
            </div>
        </div>
        <div class="pay-note-card">
            <div class="pay-note-icon" style="background: #fffbeb; color: var(--pay-warning);"><i class="fas fa-tools"></i></div>
            <div>
                <h6 class="mb-1 fw-bold">Testing Sistem</h6>
                <p class="small text-muted mb-0">Gunakan mode Sandbox untuk uji alur pembayaran sebelum membuka transaksi live.</p>
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

    function updateModeDisplay() {
        const checkbox = document.getElementById('is_production');
        const modeLabel = document.getElementById('modeLabel');
        const modeDescription = document.getElementById('modeDescription');
        const modeCard = document.getElementById('modeSelectionCard');

        if (!checkbox || !modeLabel || !modeDescription || !modeCard) {
            return;
        }

        if (checkbox.checked) {
            modeLabel.innerHTML = '<span class="text-danger"><i class="fas fa-broadcast-tower me-1"></i> Mode Production</span>';
            modeDescription.textContent = 'Transaksi nyata dengan uang sungguhan.';
            modeCard.style.borderColor = '#fecaca';
            modeCard.style.background = '#fff7f7';
        } else {
            modeLabel.innerHTML = '<span class="text-warning"><i class="fas fa-vial me-1"></i> Mode Sandbox</span>';
            modeDescription.textContent = 'Simulasi pembayaran untuk testing.';
            modeCard.style.borderColor = '#fde68a';
            modeCard.style.background = '#fffbeb';
        }
    }

    document.addEventListener('DOMContentLoaded', updateModeDisplay);
</script>
@endsection
