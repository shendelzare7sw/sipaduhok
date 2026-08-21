# Inventaris Source Paywuz

Audit dilakukan 21 Agustus 2026 terhadap branch `paywuz` pada commit `73870f0`. Status menunjukkan pemakaian runtime, bukan sekadar keberadaan nama file.

| Component | File | Class/Function | Purpose | Status | Relation to Previous Midtrans Flow |
|---|---|---|---|---|---|
| REST client | `app/Services/PaywuzService.php` | `PaywuzService` | Daftar kanal, create/get/cancel transaksi, validasi response/URL, status mapping | ACTIVE | Menggantikan `MidtransService`; Snap token berubah menjadi payment URL |
| Status orchestration | `app/Services/PaywuzPaymentStatusService.php` | `sync`, `apply`, `cancelIfPending` | Sinkronisasi payment, tagihan, audit, notifikasi, dan order duplikat | ACTIVE | Memisahkan logika status yang dahulu tersebar pada service/webhook Midtrans |
| Webhook | `app/Http/Controllers/PaywuzWebhookController.php` | `__invoke` | Payload datar resmi, event header, HMAC raw body, freshness, delivery ID, amount/reference, lalu apply | PASS | Kontrak SDK resmi dikunci oleh fixture SIT valid, invalid, duplicate, dan stale |
| Payment orchestration | `app/Http/Controllers/OrangTua/PembayaranDigitalController.php` | single/bulk/digital/sync/continue/change | Ownership, nominal, local pending, create/recover/change channel | ACTIVE | Menggantikan flow Snap dalam `OrangTuaController` |
| Parent billing | `app/Http/Controllers/OrangTua/OrangTuaController.php` | `tagihanAnak`, `cetakInvoice` | Menyajikan tagihan dan invoice final | ACTIVE | Snap callback browser dihapus; invoice memuat kanal/fee Paywuz |
| Provider config model | `app/Models/InfoPembayaran.php` | `hasPaywuz`, `isPaywuzEnabled`, `getPaywuzApiKey` | Mode, toggle dan pengambilan key terenkripsi/env | ACTIVE | Konfigurasi Midtrans tidak lagi dibaca runtime |
| Payment model | `app/Models/Pembayaran.php` | channel/status accessors | Label kanal/status Paywuz dan casting field gateway | ACTIVE | Riwayat Midtrans tetap dapat dibaca lewat kolom generik |
| Config controllers | `app/Http/Controllers/Admin/Keuangan/InfoPembayaranController.php`; `app/Http/Controllers/Bendahara/InfoPembayaranController.php` | `update` | Simpan key terenkripsi, mode, toggle, fee bearer | ACTIVE | Form merchant/server/client key Midtrans diganti konfigurasi Paywuz |
| Config UI | `resources/views/keuangan/info-pembayaran/content.blade.php` | form Paywuz | Key masked, mode, toggle, webhook URL, fee bearer | ACTIVE | Menggantikan form Midtrans |
| Parent UI | `resources/views/wali-siswa/tagihan/index.blade.php`; `resources/views/wali-siswa/pembayaran/digital.blade.php` | payment modal/page | Pilih metode, buka payment URL, sync/change/retry | ACTIVE | Snap popup berubah menjadi hosted checkout/payment instruction |
| Parent JS/CSS | `resources/js/wali-siswa/tagihan/index.js`; `resources/js/wali-siswa/pembayaran/digital.js`; CSS terkait | UI handlers | Pilihan channel dan status refresh | ACTIVE | Asset Snap dihapus |
| Shared badges | `resources/views/components/payment-method-badge.blade.php`; `payment-status-badge.blade.php` | Blade components | Label kanal dan status konsisten | ACTIVE | Menggeneralisasi tampilan gateway |
| Admin/Bendahara views | views pembayaran/laporan dan CSS terkait | presentation | Menampilkan kanal/status Paywuz | ACTIVE | Label Midtrans diganti/digeneralisasi |
| Routes | `routes/web.php` | `paywuz.webhook`, `wali-siswa.*` | Endpoint webhook dan flow wali | ACTIVE | `/midtrans/notification` dan route Snap dihapus |
| CSRF | `bootstrap/app.php`; `app/Http/Middleware/VerifyCsrfToken.php` | exception | Mengecualikan hanya webhook Paywuz | ACTIVE | Exception Midtrans dihapus |
| Reconcile CLI | `app/Console/Commands/ReconcilePaywuzPayments.php` | `paywuz:reconcile` | Polling status/cancel abandoned secara manual | PARTIAL | Pengganti/fallback status lookup; belum dijadwalkan di `routes/console.php` |
| Schema | `database/migrations/2026_08_21_000001_replace_midtrans_with_paywuz.php` | migration | Key Paywuz, fields gateway, delivery ledger | ACTIVE | Menambah Paywuz sambil mempertahankan kolom historis Midtrans |
| Direct transfer | migrations `000002`, settings/controller/UI | toggle | Kanal bank manual terpisah dari Paywuz | ACTIVE | Memperjelas fallback non-gateway sebagai fitur tersendiri |
| Failure tracking | migration `000003`; digital controller | attempts/error | Retry aman dan pesan kegagalan generik | ACTIVE | Penguatan terhadap partial failure provider |
| Tests | `tests/Feature/Paywuz*`, `SITClosureDomainIntegrationTest.php`, `NotifikasiPembayaranDigitalTest.php`, `OrangTuaBayarTagihanIdorTest.php`, unit label | test cases | Contract mock, channel replacement, disabled, ownership, status/audit/notifikasi | ACTIVE | Test Midtrans dihapus/diganti, tetapi contract webhook resmi belum tercakup |
| Dependency | `composer.json`, `composer.lock` | Composer | Tidak memakai SDK provider; Laravel HTTP client dipakai langsung | ACTIVE | `midtrans/midtrans-php` dihapus |
| Legacy documentation generator | `scripts/update_revision_execution_docs.ps1` | string/template lama | Masih menyebut Midtrans untuk dokumen eksekusi lama | LEGACY | Tidak digunakan runtime; perlu revisi bila generator dipakai kembali |

Tidak ditemukan job/event/listener khusus Paywuz. Notifikasi dipanggil langsung oleh status service. Tidak ditemukan payment-related log fixture production yang layak dijadikan bukti E2E.
