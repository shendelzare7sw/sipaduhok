@php
    $hasRekening = $infoPembayaran->hasRekeningBank();
    $directTransferEnabled = $infoPembayaran->isDirectTransferEnabled();
    $gatewayConfigured = $infoPembayaran->hasPaywuz();
    $gatewayEnabled = $infoPembayaran->isPaywuzEnabled();
    $isProduction = old('paywuz_environment', $infoPembayaran->paywuz_is_production ? 'production' : 'sandbox') === 'production';
    $sandboxConfigured = filled($infoPembayaran->getPaywuzApiKey('sandbox'));
    $productionConfigured = filled($infoPembayaran->getPaywuzApiKey('production'));
    $tunaiInfo = $infoPembayaran->tunai_info;
@endphp

<div class="pay-shell">
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-blue"><i class="fas fa-university"></i></div>
            <div><div class="stat-value">{{ $directTransferEnabled ? 'Aktif' : ($hasRekening ? 'Off' : 'Belum') }}</div><div class="stat-label">Direct Transfer</div><div class="stat-desc">{{ !$hasRekening ? 'Rekening belum diatur' : ($directTransferEnabled ? $infoPembayaran->nama_bank : 'Disembunyikan dari wali siswa') }}</div></div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-green"><i class="fas fa-credit-card"></i></div>
            <div><div class="stat-value">{{ $gatewayEnabled ? 'Aktif' : ($gatewayConfigured ? 'Off' : 'Belum') }}</div><div class="stat-label">Kanal Pembayaran</div><div class="stat-desc">{{ $gatewayConfigured ? ($isProduction ? 'Mode production' : 'Mode sandbox') : 'API key aktif belum tersedia' }}</div></div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-orange"><i class="fas fa-money-bill-wave"></i></div>
            <div><div class="stat-value">Aktif</div><div class="stat-label">Pembayaran Tunai</div><div class="stat-desc">{{ $tunaiInfo['lokasi'] }}</div></div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-purple"><i class="fas fa-shield-alt"></i></div>
            <div><div class="stat-value">{{ $gatewayEnabled || $directTransferEnabled ? 'Siap' : 'Cek' }}</div><div class="stat-label">Kesiapan Kanal</div><div class="stat-desc">{{ $gatewayEnabled || $directTransferEnabled ? 'Minimal satu kanal online tersedia' : 'Tidak ada kanal online aktif' }}</div></div>
        </div>
    </div>

    <div class="pay-grid">
        <div class="pay-card">
            <div class="pay-card-header">
                <div><h5 class="pay-card-title"><i class="fas fa-university pay-card-title-icon-primary"></i> Rekening Bank Tujuan</h5><div class="pay-card-subtitle">Data tetap tersimpan meskipun Direct Transfer dinonaktifkan.</div></div>
                <button type="button" class="btn btn-outline-primary btn-sm btn-pay-action" data-toggle-edit data-type="rekening"><i class="fas fa-edit"></i> Atur Rekening</button>
            </div>
            <div class="pay-card-body">
                <div id="rekening-view" class="pay-view-panel">
                    @if($hasRekening)
                        <div class="pay-info-panel">
                            <div><div class="pay-field-label">Nama Bank</div><div class="pay-field-value">{{ $infoPembayaran->nama_bank }}</div></div>
                            <div><div class="pay-field-label">Nomor Rekening</div><div class="pay-field-value pay-field-account-number fs-4">{{ $infoPembayaran->rekening_bank }}</div></div>
                            <div><div class="pay-field-label">Atas Nama</div><div class="pay-field-value">{{ $infoPembayaran->atas_nama }}</div></div>
                        </div>
                    @else
                        <div class="pay-empty"><i class="fas fa-exclamation-triangle"></i><h6 class="mb-1">Belum ada rekening bank</h6><p class="small mb-0">Lengkapi rekening untuk pembayaran transfer manual.</p></div>
                    @endif
                </div>
                <div id="rekening-edit" class="pay-edit-panel">
                    <form action="{{ $updateRoute }}" method="POST" class="pay-form-grid">
                        @csrf
                        <input type="hidden" name="type" value="rekening">
                        <div><label class="form-label fw-semibold">Nama Bank <span class="text-danger">*</span></label><input type="text" name="nama_bank" class="form-control" value="{{ old('nama_bank', $infoPembayaran->nama_bank) }}" required></div>
                        <div><label class="form-label fw-semibold">Nomor Rekening <span class="text-danger">*</span></label><input type="text" name="rekening_bank" class="form-control" value="{{ old('rekening_bank', $infoPembayaran->rekening_bank) }}" required></div>
                        <div><label class="form-label fw-semibold">Atas Nama <span class="text-danger">*</span></label><input type="text" name="atas_nama" class="form-control" value="{{ old('atas_nama', $infoPembayaran->atas_nama) }}" required></div>
                        <div class="d-flex gap-2"><button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-save me-1"></i>Simpan</button><button type="button" class="btn btn-light border" data-toggle-edit data-type="rekening">Batal</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header">
                <div><h5 class="pay-card-title"><i class="fas fa-credit-card pay-card-title-icon-success"></i> Payment Gateway Paywuz</h5><div class="pay-card-subtitle">API key terenkripsi dan hanya dipakai dari server.</div></div>
                <button type="button" class="btn btn-outline-success btn-sm btn-pay-action" data-toggle-edit data-type="paywuz"><i class="fas fa-cog"></i> Edit Konfigurasi</button>
            </div>
            <div class="pay-card-body">
                <div id="paywuz-view" class="pay-view-panel">
                    <div class="pay-info-panel">
                        <div><div class="pay-field-label">Environment Aktif</div><span class="badge {{ $isProduction ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2">{{ $isProduction ? 'Production' : 'Sandbox' }}</span></div>
                        <div><div class="pay-field-label">Sandbox API Key</div><div class="pay-field-value">{{ $sandboxConfigured ? 'Tersedia' : 'Belum diisi' }}</div></div>
                        <div><div class="pay-field-label">Production API Key</div><div class="pay-field-value">{{ $productionConfigured ? 'Tersedia' : 'Belum diisi' }}</div></div>
                        <div><div class="pay-field-label">Biaya Kanal</div><div class="pay-field-value">{{ $infoPembayaran->paywuz_fee_by_merchant ? 'Ditanggung sekolah' : 'Ditanggung pembayar' }}</div></div>
                        <div><div class="pay-field-label">Webhook URL</div><div class="input-group"><input type="text" class="form-control" value="{{ $paywuzWebhookUrl }}" readonly><button type="button" class="btn btn-outline-primary" data-copy-value="{{ $paywuzWebhookUrl }}"><i class="fas fa-copy"></i></button></div></div>
                    </div>
                </div>
                <div id="paywuz-edit" class="pay-edit-panel">
                    <form action="{{ $updateRoute }}" method="POST" class="pay-form-grid">
                        @csrf
                        <input type="hidden" name="type" value="paywuz">
                        <div class="alert alert-warning border-0 mb-0"><small><i class="fas fa-exclamation-triangle me-1"></i>Gunakan API key proyek <strong>Sipadu Homeschool</strong>. Kosongkan field key yang tidak ingin diganti.</small></div>
                        <div><label class="form-label fw-semibold">Sandbox API Key</label><input type="password" name="paywuz_sandbox_api_key" class="form-control" autocomplete="new-password" placeholder="{{ $sandboxConfigured ? 'Tersimpan — isi hanya untuk mengganti' : 'pk_sand_...' }}"></div>
                        <div><label class="form-label fw-semibold">Production API Key</label><input type="password" name="paywuz_production_api_key" class="form-control" autocomplete="new-password" placeholder="{{ $productionConfigured ? 'Tersimpan — isi hanya untuk mengganti' : 'pk_live_...' }}"></div>
                        <div><label class="form-label fw-semibold" for="paywuzEnvironment">Environment Aktif</label><select class="form-select" name="paywuz_environment" id="paywuzEnvironment" required><option value="sandbox" @selected(!$isProduction)>Sandbox — data simulasi</option><option value="production" @selected($isProduction)>Production — transaksi nyata</option></select></div>
                        <div class="mode-card" id="modeSelectionCard"><strong id="modeLabel">{{ $isProduction ? 'Mode Production' : 'Mode Sandbox' }}</strong><small class="text-muted d-block mt-1" id="modeDescription">{{ $isProduction ? 'Transaksi nyata dengan uang sungguhan.' : 'Simulasi pembayaran untuk pengujian.' }}</small></div>
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="paywuz_fee_by_merchant" id="feeByMerchant" value="1" @checked(old('paywuz_fee_by_merchant', $infoPembayaran->paywuz_fee_by_merchant))><label class="form-check-label fw-semibold" for="feeByMerchant">Biaya kanal ditanggung sekolah</label><small class="text-muted d-block">Jika nonaktif, biaya kanal ditambahkan pada total yang dibayar wali siswa.</small></div>
                        <div class="d-flex gap-2"><button type="submit" class="btn btn-success flex-fill"><i class="fas fa-save me-1"></i>Simpan Konfigurasi</button><button type="button" class="btn btn-light border" data-toggle-edit data-type="paywuz">Batal</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header"><div><h5 class="pay-card-title"><i class="fas fa-money-bill-wave pay-card-title-icon-warning"></i> Pembayaran Tunai</h5><div class="pay-card-subtitle">Informasi loket untuk pembayaran langsung.</div></div><button type="button" class="btn btn-outline-warning btn-sm btn-pay-action" data-toggle-edit data-type="tunai"><i class="fas fa-edit"></i> Edit Info</button></div>
            <div class="pay-card-body">
                <div id="tunai-view" class="pay-view-panel"><div class="pay-info-panel"><div><div class="pay-field-label">Lokasi</div><div class="pay-field-value">{{ $tunaiInfo['lokasi'] }}</div></div><div><div class="pay-field-label">Jam Operasional</div><div class="pay-field-value">{{ $tunaiInfo['jam_operasional'] }}</div></div><div><div class="pay-field-label">Petunjuk</div><div class="pay-muted-value">{{ $tunaiInfo['deskripsi'] }}</div></div></div></div>
                <div id="tunai-edit" class="pay-edit-panel"><form action="{{ $updateRoute }}" method="POST" class="pay-form-grid">@csrf<input type="hidden" name="type" value="tunai"><div><label class="form-label fw-semibold">Lokasi Pembayaran</label><input type="text" name="tunai_lokasi" class="form-control" value="{{ old('tunai_lokasi', $infoPembayaran->tunai_lokasi) }}"></div><div><label class="form-label fw-semibold">Jam Operasional</label><input type="text" name="tunai_jam_operasional" class="form-control" value="{{ old('tunai_jam_operasional', $infoPembayaran->tunai_jam_operasional) }}"></div><div><label class="form-label fw-semibold">Petunjuk</label><textarea name="tunai_deskripsi" class="form-control" rows="3">{{ old('tunai_deskripsi', $infoPembayaran->tunai_deskripsi) }}</textarea></div><div class="d-flex gap-2"><button type="submit" class="btn btn-warning flex-fill"><i class="fas fa-save me-1"></i>Simpan</button><button type="button" class="btn btn-light border" data-toggle-edit data-type="tunai">Batal</button></div></form></div>
            </div>
        </div>

        <div class="pay-card">
            <div class="pay-card-header"><div><h5 class="pay-card-title"><i class="fas fa-satellite-dish pay-card-title-icon-info"></i> Status Kanal</h5><div class="pay-card-subtitle">Kanal yang tersedia untuk wali siswa.</div></div></div>
            <div class="pay-card-body"><div class="pay-status-grid">
                <div class="pay-status-item {{ $directTransferEnabled ? 'is-active' : 'is-inactive' }}"><div class="pay-status-main"><div class="pay-status-icon {{ $directTransferEnabled ? 'pay-status-icon-active' : 'pay-status-icon-inactive' }}"><i class="fas fa-university"></i></div><div><div class="pay-status-title">Direct Transfer</div><div class="pay-status-desc">{{ !$hasRekening ? 'Lengkapi rekening terlebih dahulu' : ($directTransferEnabled ? 'Tampil untuk wali siswa' : 'Disembunyikan dari wali siswa') }}</div></div></div>@if($hasRekening)<form action="{{ $updateRoute }}" method="POST" class="ms-auto">@csrf<input type="hidden" name="type" value="direct_transfer_toggle"><div class="form-check form-switch mb-0"><input type="checkbox" class="form-check-input" name="direct_transfer_enabled" value="1" @checked($infoPembayaran->direct_transfer_enabled) data-submit-on-change aria-label="Aktifkan atau nonaktifkan Direct Transfer"></div></form>@endif</div>
                <div class="pay-status-item {{ $gatewayEnabled ? 'is-active' : 'is-inactive' }}"><div class="pay-status-main"><div class="pay-status-icon {{ $gatewayEnabled ? 'pay-status-icon-active' : 'pay-status-icon-inactive' }}"><i class="fas fa-credit-card"></i></div><div><div class="pay-status-title">Kanal Pembayaran</div><div class="pay-status-desc">{{ !$gatewayConfigured ? 'Belum terhubung' : ($gatewayEnabled ? 'Kanal aktif' : 'Dinonaktifkan') }}</div></div></div>@if($gatewayConfigured)<form action="{{ $updateRoute }}" method="POST" class="ms-auto">@csrf<input type="hidden" name="type" value="paywuz_toggle"><div class="form-check form-switch mb-0"><input type="checkbox" class="form-check-input" name="paywuz_enabled" value="1" @checked($infoPembayaran->paywuz_enabled) data-submit-on-change></div></form>@endif</div>
                <div class="pay-status-item is-active"><div class="pay-status-main"><div class="pay-status-icon pay-status-icon-active"><i class="fas fa-money-bill-wave"></i></div><div><div class="pay-status-title">Pembayaran Kasir</div><div class="pay-status-desc">Kanal selalu aktif</div></div></div></div>
            </div></div>
        </div>
    </div>

    <div class="pay-guide-grid">
        <div class="pay-note-card"><div class="pay-note-icon pay-note-icon-success"><i class="fas fa-link"></i></div><div><h6 class="mb-1 fw-bold">Webhook Wajib</h6><p class="small text-muted mb-0">Salin Webhook URL di atas ke proyek Sandbox dan Production agar status lunas masuk otomatis.</p></div></div>
        <div class="pay-note-card"><div class="pay-note-icon pay-note-icon-primary"><i class="fas fa-key"></i></div><div><h6 class="mb-1 fw-bold">API Key Aman</h6><p class="small text-muted mb-0">API key disimpan terenkripsi dan tidak pernah dikirim ke browser wali siswa.</p></div></div>
        <div class="pay-note-card"><div class="pay-note-icon pay-note-icon-warning"><i class="fas fa-vial"></i></div><div><h6 class="mb-1 fw-bold">Uji Sandbox</h6><p class="small text-muted mb-0">Selesaikan simulasi dan pastikan tagihan berubah lunas sebelum mengaktifkan Production.</p></div></div>
    </div>
</div>
