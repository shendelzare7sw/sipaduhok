@php
    $hasRekening = $infoPembayaran->hasRekeningBank();
    $directTransferEnabled = $infoPembayaran->isDirectTransferEnabled();
    $gatewayConfigured = $infoPembayaran->hasPaywuz();
    $gatewayEnabled = $infoPembayaran->isPaywuzEnabled();
    $environment = old('paywuz_environment', $infoPembayaran->paywuz_is_production ? 'production' : 'sandbox');
    $isProduction = $environment === 'production';
    $sandboxConfigured = filled($infoPembayaran->getPaywuzApiKey('sandbox'));
    $productionConfigured = filled($infoPembayaran->getPaywuzApiKey('production'));
    $tunaiInfo = $infoPembayaran->tunai_info;
    $editingPanel = '';

    if ($errors->has('nama_bank') || $errors->has('rekening_bank') || $errors->has('atas_nama')) {
        $editingPanel = 'rekening';
    } elseif ($errors->has('paywuz_sandbox_api_key') || $errors->has('paywuz_production_api_key') || $errors->has('paywuz_environment') || $errors->has('paywuz_fee_by_merchant')) {
        $editingPanel = 'paywuz';
    } elseif ($errors->has('tunai_lokasi') || $errors->has('tunai_jam_operasional') || $errors->has('tunai_deskripsi')) {
        $editingPanel = 'tunai';
    }

    $fieldClass = 'mt-1.5 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'text-xs font-bold text-slate-700';
@endphp

<div
    class="min-w-0 w-full space-y-5"
    x-data="{
        editing: @js($editingPanel),
        environment: @js($environment),
        async copyWebhook() {
            let copied = false;
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(this.$refs.webhook.value);
                    copied = true;
                }
            } catch (error) {}
            if (!copied) {
                this.$refs.webhook.focus();
                this.$refs.webhook.select();
                copied = document.execCommand('copy');
            }
            Swal.fire({ toast: true, position: 'top-end', icon: copied ? 'success' : 'error', title: copied ? 'Webhook berhasil disalin' : 'Webhook belum dapat disalin', showConfirmButton: false, timer: 1800 });
        },
        async toggleChannel(event, label) {
            const input = event.target;
            const nextState = input.checked;
            const result = await Swal.fire({
                title: `${nextState ? 'Aktifkan' : 'Nonaktifkan'} ${label}?`,
                text: nextState ? 'Kanal akan tersedia bagi wali siswa.' : 'Kanal akan disembunyikan dari wali siswa.',
                icon: 'question', showCancelButton: true,
                confirmButtonText: nextState ? 'Ya, aktifkan' : 'Ya, nonaktifkan',
                cancelButtonText: 'Batal', confirmButtonColor: '#2563eb'
            });
            if (!result.isConfirmed) { input.checked = !nextState; return; }
            input.form.requestSubmit();
        }
    }"
>
    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach([
            ['Direct Transfer', $directTransferEnabled ? 'Aktif' : ($hasRekening ? 'Off' : 'Belum'), !$hasRekening ? 'Rekening belum diatur' : ($directTransferEnabled ? $infoPembayaran->nama_bank : 'Belum ditampilkan'), 'fa-university', 'bg-brand-50 text-brand-600'],
            ['Kanal Digital', $gatewayEnabled ? 'Aktif' : ($gatewayConfigured ? 'Off' : 'Belum'), $gatewayConfigured ? ($isProduction ? 'Mode production' : 'Mode sandbox') : 'API key belum tersedia', 'fa-credit-card', 'bg-emerald-50 text-emerald-600'],
            ['Pembayaran Tunai', 'Aktif', $tunaiInfo['lokasi'], 'fa-money-bill-wave', 'bg-amber-50 text-amber-600'],
            ['Kesiapan Kanal', $gatewayEnabled || $directTransferEnabled ? 'Siap' : 'Cek', $gatewayEnabled || $directTransferEnabled ? 'Kanal online tersedia' : 'Kanal online belum aktif', 'fa-shield-alt', 'bg-violet-50 text-violet-600'],
        ] as [$label, $value, $description, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0"><p class="text-xl font-extrabold text-slate-950">{{ $value }}</p><p class="mt-1 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p></div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                </div>
                <p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] text-slate-500" title="{{ $description }}">{{ $description }}</p>
            </article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex items-start gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-sliders" aria-hidden="true"></i></span><div><h2 class="text-base font-extrabold text-slate-950">1. Konfigurasi metode pembayaran</h2><p class="mt-0.5 text-xs leading-5 text-slate-500">Lengkapi informasi setiap metode sebelum mengaktifkannya untuk wali siswa.</p></div></div>
        </header>

        <div class="grid min-w-0 xl:grid-cols-2">
            <article class="min-w-0 border-b border-slate-200 xl:border-r">
                <header class="flex flex-wrap items-start justify-between gap-3 p-4 sm:p-5">
                    <div class="min-w-0"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-university text-brand-600" aria-hidden="true"></i>Rekening Direct Transfer</h3><p class="mt-1 text-xs text-slate-500">Rekening tujuan transfer manual.</p></div>
                    <button type="button" @click="editing = editing === 'rekening' ? '' : 'rekening'" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-pen" aria-hidden="true"></i><span x-text="editing === 'rekening' ? 'Tutup form' : 'Atur rekening'"></span></button>
                </header>
                <div class="px-4 pb-5 sm:px-5">
                    <div x-show="editing !== 'rekening'">
                        @if($hasRekening)
                            <dl class="grid gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2"><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nama bank</dt><dd class="mt-1 text-sm font-bold text-slate-900">{{ $infoPembayaran->nama_bank }}</dd></div><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Atas nama</dt><dd class="mt-1 text-sm font-bold text-slate-900">{{ $infoPembayaran->atas_nama }}</dd></div><div class="sm:col-span-2"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nomor rekening</dt><dd class="mt-1 break-all text-lg font-extrabold tracking-wide text-brand-700">{{ $infoPembayaran->rekening_bank }}</dd></div></dl>
                        @else
                            <div class="rounded-xl border border-dashed border-amber-300 bg-amber-50 p-4 text-sm text-amber-800"><i class="fas fa-circle-exclamation mr-2" aria-hidden="true"></i>Rekening belum diatur.</div>
                        @endif
                    </div>
                    <form x-show="editing === 'rekening'" x-cloak action="{{ $updateRoute }}" method="POST" class="space-y-4 rounded-xl border border-brand-100 bg-brand-50/50 p-4">
                        @csrf
                        <input type="hidden" name="type" value="rekening">
                        <div><label for="nama_bank" class="{{ $labelClass }}">Nama bank <span class="text-red-600">*</span></label><input id="nama_bank" type="text" name="nama_bank" class="{{ $fieldClass }}" value="{{ old('nama_bank', $infoPembayaran->nama_bank) }}" required>@error('nama_bank')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="rekening_bank" class="{{ $labelClass }}">Nomor rekening <span class="text-red-600">*</span></label><input id="rekening_bank" type="text" name="rekening_bank" inputmode="numeric" class="{{ $fieldClass }}" value="{{ old('rekening_bank', $infoPembayaran->rekening_bank) }}" required>@error('rekening_bank')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="atas_nama" class="{{ $labelClass }}">Atas nama <span class="text-red-600">*</span></label><input id="atas_nama" type="text" name="atas_nama" class="{{ $fieldClass }}" value="{{ old('atas_nama', $infoPembayaran->atas_nama) }}" required>@error('atas_nama')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div class="flex justify-end gap-2"><button type="button" @click="editing = ''" class="h-10 px-3 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>Simpan rekening</button></div>
                    </form>
                </div>
            </article>

            <article class="min-w-0 border-b border-slate-200">
                <header class="flex flex-wrap items-start justify-between gap-3 p-4 sm:p-5">
                    <div class="min-w-0"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-credit-card text-emerald-600" aria-hidden="true"></i>Payment Gateway Paywuz</h3><p class="mt-1 text-xs text-slate-500">API key tersimpan terenkripsi di server.</p></div>
                    <button type="button" @click="editing = editing === 'paywuz' ? '' : 'paywuz'" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700 hover:bg-emerald-100"><i class="fas fa-gear" aria-hidden="true"></i><span x-text="editing === 'paywuz' ? 'Tutup form' : 'Konfigurasi'"></span></button>
                </header>
                <div class="px-4 pb-5 sm:px-5">
                    <div x-show="editing !== 'paywuz'" class="space-y-3">
                        <dl class="grid gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2"><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Environment</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $isProduction ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $isProduction ? 'Production' : 'Sandbox' }}</span></dd></div><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Biaya kanal</dt><dd class="mt-1 text-xs font-bold text-slate-800">{{ $infoPembayaran->paywuz_fee_by_merchant ? 'Ditanggung sekolah' : 'Ditanggung pembayar' }}</dd></div><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Sandbox key</dt><dd class="mt-1 text-xs font-bold {{ $sandboxConfigured ? 'text-emerald-700' : 'text-slate-500' }}">{{ $sandboxConfigured ? 'Tersedia' : 'Belum diisi' }}</dd></div><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Production key</dt><dd class="mt-1 text-xs font-bold {{ $productionConfigured ? 'text-emerald-700' : 'text-slate-500' }}">{{ $productionConfigured ? 'Tersedia' : 'Belum diisi' }}</dd></div></dl>
                        <div><label for="webhook_url" class="{{ $labelClass }}">Webhook URL</label><div class="mt-1.5 flex min-w-0 gap-2"><input x-ref="webhook" id="webhook_url" type="text" value="{{ $paywuzWebhookUrl }}" readonly class="h-11 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-600"><button type="button" @click="copyWebhook()" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-100" aria-label="Salin URL webhook"><i class="fas fa-copy" aria-hidden="true"></i></button></div></div>
                    </div>
                    <form x-show="editing === 'paywuz'" x-cloak action="{{ $updateRoute }}" method="POST" class="space-y-4 rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                        @csrf
                        <input type="hidden" name="type" value="paywuz">
                        <p class="rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-800"><i class="fas fa-triangle-exclamation mr-1" aria-hidden="true"></i>Kosongkan API key yang tidak ingin diganti.</p>
                        <div><label for="paywuz_sandbox_api_key" class="{{ $labelClass }}">Sandbox API key</label><input id="paywuz_sandbox_api_key" type="password" name="paywuz_sandbox_api_key" class="{{ $fieldClass }}" autocomplete="new-password" placeholder="{{ $sandboxConfigured ? 'Tersimpan — isi hanya untuk mengganti' : 'pk_sand_...' }}">@error('paywuz_sandbox_api_key')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="paywuz_production_api_key" class="{{ $labelClass }}">Production API key</label><input id="paywuz_production_api_key" type="password" name="paywuz_production_api_key" class="{{ $fieldClass }}" autocomplete="new-password" placeholder="{{ $productionConfigured ? 'Tersimpan — isi hanya untuk mengganti' : 'pk_live_...' }}">@error('paywuz_production_api_key')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="paywuz_environment" class="{{ $labelClass }}">Environment aktif</label><select id="paywuz_environment" name="paywuz_environment" x-model="environment" class="{{ $fieldClass }}" required><option value="sandbox">Sandbox — data simulasi</option><option value="production">Production — transaksi nyata</option></select>@error('paywuz_environment')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div class="rounded-xl p-3 text-xs leading-5" :class="environment === 'production' ? 'bg-red-50 text-red-800' : 'bg-brand-50 text-brand-800'"><strong x-text="environment === 'production' ? 'Mode Production' : 'Mode Sandbox'"></strong><span class="mt-0.5 block" x-text="environment === 'production' ? 'Transaksi nyata dengan uang sungguhan.' : 'Simulasi pembayaran untuk pengujian.'"></span></div>
                        <label class="flex cursor-pointer items-start gap-3"><span class="min-w-0 flex-1"><strong class="block text-xs text-slate-800">Biaya kanal ditanggung sekolah</strong><span class="mt-1 block text-[11px] leading-5 text-slate-500">Jika nonaktif, biaya ditambahkan ke total wali siswa.</span></span><span class="relative inline-flex h-6 w-11 shrink-0 items-center"><input type="checkbox" name="paywuz_fee_by_merchant" value="1" class="peer sr-only" @checked(old('paywuz_fee_by_merchant', $infoPembayaran->paywuz_fee_by_merchant))><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></span></label>
                        <div class="flex justify-end gap-2"><button type="button" @click="editing = ''" class="h-10 px-3 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white hover:bg-emerald-700"><i class="fas fa-save" aria-hidden="true"></i>Simpan Paywuz</button></div>
                    </form>
                </div>
            </article>

            <article class="min-w-0 xl:border-r">
                <header class="flex flex-wrap items-start justify-between gap-3 p-4 sm:p-5">
                    <div class="min-w-0"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-money-bill-wave text-amber-600" aria-hidden="true"></i>Pembayaran Tunai</h3><p class="mt-1 text-xs text-slate-500">Informasi loket untuk pembayaran langsung.</p></div>
                    <button type="button" @click="editing = editing === 'tunai' ? '' : 'tunai'" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-amber-50 px-3 text-xs font-bold text-amber-700 hover:bg-amber-100"><i class="fas fa-pen" aria-hidden="true"></i><span x-text="editing === 'tunai' ? 'Tutup form' : 'Edit informasi'"></span></button>
                </header>
                <div class="px-4 pb-5 sm:px-5">
                    <dl x-show="editing !== 'tunai'" class="space-y-3 rounded-xl bg-slate-50 p-4"><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Lokasi</dt><dd class="mt-1 text-sm font-bold text-slate-900">{{ $tunaiInfo['lokasi'] }}</dd></div><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Jam operasional</dt><dd class="mt-1 text-sm font-semibold text-slate-800">{{ $tunaiInfo['jam_operasional'] }}</dd></div><div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Petunjuk</dt><dd class="mt-1 text-xs leading-5 text-slate-600">{{ $tunaiInfo['deskripsi'] }}</dd></div></dl>
                    <form x-show="editing === 'tunai'" x-cloak action="{{ $updateRoute }}" method="POST" class="space-y-4 rounded-xl border border-amber-100 bg-amber-50/40 p-4">
                        @csrf
                        <input type="hidden" name="type" value="tunai">
                        <div><label for="tunai_lokasi" class="{{ $labelClass }}">Lokasi pembayaran</label><input id="tunai_lokasi" type="text" name="tunai_lokasi" class="{{ $fieldClass }}" value="{{ old('tunai_lokasi', $infoPembayaran->tunai_lokasi) }}">@error('tunai_lokasi')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="tunai_jam_operasional" class="{{ $labelClass }}">Jam operasional</label><input id="tunai_jam_operasional" type="text" name="tunai_jam_operasional" class="{{ $fieldClass }}" value="{{ old('tunai_jam_operasional', $infoPembayaran->tunai_jam_operasional) }}">@error('tunai_jam_operasional')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div><label for="tunai_deskripsi" class="{{ $labelClass }}">Petunjuk</label><textarea id="tunai_deskripsi" name="tunai_deskripsi" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">{{ old('tunai_deskripsi', $infoPembayaran->tunai_deskripsi) }}</textarea>@error('tunai_deskripsi')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        <div class="flex justify-end gap-2"><button type="button" @click="editing = ''" class="h-10 px-3 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-amber-500 px-4 text-xs font-bold text-white hover:bg-amber-600"><i class="fas fa-save" aria-hidden="true"></i>Simpan tunai</button></div>
                    </form>
                </div>
            </article>

            <article class="min-w-0 p-4 sm:p-5">
                <div class="rounded-xl border border-dashed border-slate-300 p-4"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-circle-info text-brand-600" aria-hidden="true"></i>Urutan yang disarankan</h3><ol class="mt-4 space-y-3 text-xs leading-5 text-slate-600"><li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 font-extrabold text-brand-700">1</span><span>Isi rekening dan API key yang digunakan.</span></li><li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 font-extrabold text-brand-700">2</span><span>Uji transaksi digital dalam mode Sandbox.</span></li><li class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 font-extrabold text-brand-700">3</span><span>Aktifkan kanal setelah data dipastikan benar.</span></li></ol></div>
            </article>
        </div>
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="text-base font-extrabold text-slate-950"><i class="fas fa-toggle-on mr-2 text-brand-600" aria-hidden="true"></i>2. Kanal yang ditampilkan</h2><p class="mt-1 text-xs text-slate-500">Perubahan langsung memengaruhi pilihan pembayaran wali siswa.</p></header>
        <div class="grid md:grid-cols-3">
            <article class="flex items-center gap-3 border-b border-slate-100 p-4 sm:p-5 md:border-b-0 md:border-r"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $directTransferEnabled ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-university" aria-hidden="true"></i></span><div class="min-w-0 flex-1"><h3 class="text-sm font-extrabold text-slate-900">Direct Transfer</h3><p class="mt-0.5 text-[11px] leading-4 text-slate-500">{{ !$hasRekening ? 'Lengkapi rekening dahulu' : ($directTransferEnabled ? 'Tampil untuk wali siswa' : 'Sedang disembunyikan') }}</p></div>@if($hasRekening)<form action="{{ $updateRoute }}" method="POST">@csrf<input type="hidden" name="type" value="direct_transfer_toggle"><label class="relative inline-flex h-6 w-11 cursor-pointer items-center"><input type="checkbox" name="direct_transfer_enabled" value="1" class="peer sr-only" @checked($infoPembayaran->direct_transfer_enabled) @change="toggleChannel($event, 'Direct Transfer')"><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></label></form>@endif</article>
            <article class="flex items-center gap-3 border-b border-slate-100 p-4 sm:p-5 md:border-b-0 md:border-r"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $gatewayEnabled ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}"><i class="fas fa-credit-card" aria-hidden="true"></i></span><div class="min-w-0 flex-1"><h3 class="text-sm font-extrabold text-slate-900">Kanal Digital</h3><p class="mt-0.5 text-[11px] leading-4 text-slate-500">{{ !$gatewayConfigured ? 'Lengkapi API key dahulu' : ($gatewayEnabled ? 'Kanal aktif' : 'Sedang dinonaktifkan') }}</p></div>@if($gatewayConfigured)<form action="{{ $updateRoute }}" method="POST">@csrf<input type="hidden" name="type" value="paywuz_toggle"><label class="relative inline-flex h-6 w-11 cursor-pointer items-center"><input type="checkbox" name="paywuz_enabled" value="1" class="peer sr-only" @checked($infoPembayaran->paywuz_enabled) @change="toggleChannel($event, 'Kanal Digital')"><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></label></form>@endif</article>
            <article class="flex items-center gap-3 p-4 sm:p-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></span><div class="min-w-0 flex-1"><h3 class="text-sm font-extrabold text-slate-900">Pembayaran Kasir</h3><p class="mt-0.5 text-[11px] leading-4 text-slate-500">Kanal selalu aktif.</p></div><span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Aktif</span></article>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3">
        @foreach([
            ['Webhook wajib', 'Salin URL ke proyek Sandbox dan Production agar status lunas diperbarui otomatis.', 'fa-link', 'bg-emerald-50 text-emerald-600'],
            ['API key aman', 'Kunci API disimpan terenkripsi dan tidak dikirim ke browser wali siswa.', 'fa-key', 'bg-brand-50 text-brand-600'],
            ['Uji Sandbox', 'Pastikan simulasi berhasil sebelum menggunakan transaksi Production.', 'fa-vial', 'bg-amber-50 text-amber-600'],
        ] as [$title, $description, $icon, $tone])
            <article class="flex gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span><div><h3 class="text-sm font-extrabold text-slate-900">{{ $title }}</h3><p class="mt-1 text-xs leading-5 text-slate-500">{{ $description }}</p></div></article>
        @endforeach
    </section>
</div>
