# Integration Inventory

Status ditentukan dari pemanggilan source, bukan sekadar dependency. `IMPLEMENTED` berarti jalur kode tersedia; tidak otomatis berarti credential/service production aktif.

# Integration Matrix

| Integration ID | Nama Integrasi | Modul | Tujuan | Implementasi | Source Reference | Status |
|---|---|---|---|---|---|---|
| INT-001 | Blade/JS ↔ Laravel web | Semua | Menyajikan UI dan submit form/JSON | 804 route web, Blade, Vite JS, CSRF/session | `routes/web.php`; `resources/views/js`; `vite.config.js` | IMPLEMENTED |
| INT-002 | Authentication ↔ Session DB | AU | Membentuk dan menjaga session pengguna | Laravel auth; session driver default database; regenerate/invalidate | `LoginController`; `config/session.php`; migration users/sessions | IMPLEMENTED |
| INT-003 | Role middleware ↔ User/Role DB | AU/USR | Membatasi route berdasarkan role | `role_id` + fallback legacy; CheckRole; Admin bypass | `User.php`; `Role.php`; `CheckRole.php`; route groups | IMPLEMENTED |
| INT-004 | Controller/Service ↔ Eloquent DB | Semua | Persistensi domain dan transaction | 53 model, query builder, DB transaction | `app/Models`; controllers/services; migrations | IMPLEMENTED |
| INT-005 | Public/local file storage | KON/LMS/PRS/KEU/RAP/AU | Upload, preview, download dan file temporer | disk local/public, signed owner-bound preview, storage link mapping | `config/filesystems.php`; `FileController.php`; upload controllers | IMPLEMENTED |
| INT-006 | Laravel Excel | USR/ORG/AKD/KEU/PRS/NIL/LMS/RAP | Import/export spreadsheet | Import/export classes dan route/controller aktif | `app/Imports`; `app/Exports`; composer dependency | IMPLEMENTED |
| INT-007 | DOMPDF | AKD/KEU/RAP/SWA | Cetak PDF laporan, bukti dan rapor | controller memanggil PDF dan view export/print | `composer.json`; controllers; `resources/views/exports` | IMPLEMENTED |
| INT-008 | Midtrans Snap/Transaction API | KEU/WLS | Pembayaran online dan lookup status | SDK config, token, status API, Snap URL | `MidtransService.php`; parent controller; composer | IMPLEMENTED |
| INT-009 | Midtrans webhook callback | KEU/NOT | Sinkronisasi status payment server-to-server | CSRF exception, signature SHA-512, status mapping, audit/idempotency | `bootstrap/app.php`; route webhook; `MidtransWebhookController.php` | IMPLEMENTED |
| INT-010 | Groq API | LMS/NIL | Chatbot, generator soal, AI grading | HTTP chat completions, model/fallback/settings | AI services; `AiSettingController.php`; `config/ai-models.php` | IMPLEMENTED |
| INT-011 | Google Gemini API | LMS/NIL | Provider/fallback chatbot, soal dan grading multimodal | generateContent v1; API key di AppSetting | AI services/settings | IMPLEMENTED |
| INT-012 | PDF text extraction | LMS | Membaca attachment PDF untuk chatbot/grading | Spatie pdf-to-text; binary path configurable | `AiChatbotService`; `config/services.php`; `.env.example` | IMPLEMENTED |
| INT-013 | Cloudflare Turnstile | AU | Verifikasi anti-bot saat login | siteverify server-side bila secret tersedia | `LoginRequest`; `config/services.php`; `.env.example` | IMPLEMENTED |
| INT-014 | Laravel Mail | AU | Mengirim username/reset/security notification | `Mail::raw`; mailer configurable; default example log | `EmailRecoveryService`; `config/mail.php` | IMPLEMENTED |
| INT-015 | Notification event/broadcast | NOT | Push event notifikasi ke channel privat | `NotificationCreated implements ShouldBroadcast`; broadcast default example log | event/service; `.env.example` | PARTIAL |
| INT-016 | Scheduler | NOT/PRM | Reminder dan eksekusi promotion terjadwal | schedule definitions dan command; cron host tidak tersedia di repo | `routes/console.php`; Console Commands | PARTIAL |
| INT-017 | Database queue | Infrastruktur | Menyimpan job async | tabel/config tersedia; tidak ditemukan domain Job/ShouldQueue | `config/queue.php`; jobs migration | CONFIGURED BUT UNUSED |
| INT-018 | WhatsApp gateway | AU | Rencana/fallback recovery melalui pesan | service mock hanya log dan selalu false | `WhatsAppService.php` | REFERENCE ONLY |
| INT-019 | Google Sheets sync | Historis | Sinkronisasi data yang pernah direferensikan | migration menjatuhkan tabel log; tidak ada integrasi aktif | migration `2026_07_16_000000*` | REFERENCE ONLY |
| INT-020 | Cloudflare/Nginx deployment edge | Infrastruktur | Proxy, HTTPS, IP asli, WAF/Turnstile | trusted proxy/force HTTPS/panduan; state server tidak dapat dilihat | `bootstrap/app.php`; `AppServiceProvider`; hardening doc | PARTIAL |
| INT-021 | Database backup MySQL | Infrastruktur | Backup terkompresi dan retensi | script siap pakai; instalasi/cron/hasil backup tidak dapat diverifikasi | `scripts/backup-db.sh` | PARTIAL |
| INT-022 | Notification ↔ business modules | KEU/PRS/NIL/LMS/RAP/PRM/MON | Membuat pesan in-app dari event bisnis | pemanggilan service pada controllers/services/commands | `NotificationService.php`; tests notification | IMPLEMENTED |

# Payment Audit

## Payment Status

**PAYMENT IMPLEMENTED**

Alasan: provider, konfigurasi, model/table, route pengguna, pembuatan transaksi Snap, halaman payment, continuation, status API, webhook, signature verification, status mapping, audit log, pembaruan tagihan, duplicate prevention, invoice, notifikasi, dan test terkait semuanya ditemukan. Ini bukan sekadar package terpasang.

Implementasi kode belum membuktikan credential production aktif atau transaksi riil telah dilakukan. Itu adalah `[PERLU KONFIRMASI]`, tetapi tidak mengubah klasifikasi implementasi repository.

## Payment Evidence

| Komponen | Evidence | Penilaian |
|---|---|---|
| Provider | Midtrans PHP SDK `^2.6`; class `Config`, `Snap`, `Transaction` dipanggil | Implemented |
| Config UI | Admin/Bendahara mengelola merchant ID, encrypted server key, client key, mode production dan enable | Implemented |
| Config persistence | `info_pembayaran` dan `InfoPembayaran` singleton helper | Implemented |
| Billing model | `tagihan`: amount, due date, status, carryover links | Implemented |
| Payment model | `pembayaran`: amount/method/status, gateway/order/transaction/payment type/response, parent/validator | Implemented |
| Manual payment | Input tunai/transfer oleh Admin/Bendahara dan transfer+proof oleh Orang Tua | Implemented |
| Single digital payment | `OrangTuaController@prosesBayar` membentuk params/token dan record pending | Implemented |
| Bulk digital payment | Banyak payment record berbagi `order_id`, item details dan satu Snap transaction | Implemented |
| Snap page | Route/view/JS memuat Snap URL/client key, handler success/pending/error/close | Implemented |
| Continue pending | Payment pending milik child dapat dilanjutkan dengan token/order yang sesuai | Implemented |
| Finish callback | Authenticated GET; ownership guard; tidak mempercayai query status; lookup Transaction API | Implemented |
| Server webhook | Public POST, CSRF exception, signature verification | Implemented |
| Status mapping | capture+accept/settlement → disetujui; pending → pending; deny/expire/cancel → ditolak | Implemented |
| Tagihan sync | Payment disetujui memanggil `Tagihan::updateStatusBayar()` | Implemented |
| Duplicate prevention | Pending payment lain untuk tagihan+siswa sama dibatalkan setelah sukses | Implemented |
| Audit trail | `financial_audit_logs` mencatat perubahan status dan auto-cancel | Implemented |
| Notification | Keberhasilan digital ke Admin, Bendahara, Wali; logic transisi mencegah duplikat | Implemented |
| Reporting | Filter/metode Midtrans, total/jumlah transaksi dan detail pada laporan/pembayaran | Implemented |
| Tests | ownership, forgery finish, notification, per-payment webhook, old audit value, financial installment | Implemented test coverage (tidak menyeluruh) |

Referensi utama:

- `composer.json`
- `app/Services/MidtransService.php`
- `app/Http/Controllers/MidtransWebhookController.php`
- `app/Http/Controllers/OrangTua/OrangTuaController.php`
- `app/Http/Controllers/{Admin/Keuangan,Bendahara}/{PembayaranController,InfoPembayaranController}.php`
- `app/Models/{Tagihan,Pembayaran,InfoPembayaran,FinancialAuditLog}.php`
- migrations `create_tagihan`, `create_pembayaran`, `create_info_pembayaran`, `create_financial_audit_logs`
- `resources/views/wali-siswa/{tagihan,pembayaran}/`
- `resources/js/wali-siswa/pembayaran/snap.js`
- payment-related Feature tests.

## Payment Data and Status

| Domain | Nilai implementasi | Makna |
|---|---|---|
| Metode payment | `tunai`, `transfer`, `midtrans` | Kanal pencatatan |
| Status validasi payment | `pending`, `disetujui`, `ditolak` | Status domain SIPADUHOK |
| Status tagihan | `belum_bayar`, `sudah_bayar`, `terlambat`, `cicilan` | Kondisi kewajiban setelah agregasi payment disetujui |
| Status Midtrans sukses | `capture` + fraud `accept`, `settlement` | Dipetakan `disetujui` |
| Status Midtrans berjalan | `pending` | Dipetakan `pending` |
| Status Midtrans gagal | `deny`, `expire`, `cancel` | Dipetakan `ditolak` |

## Payment Flow

### A. Transfer manual oleh Orang Tua

1. Orang tua membuka tagihan anak melalui relation `children()`.
2. Sistem memvalidasi tagihan milik anak, nominal, metode transfer dan bukti gambar.
3. Tunggakan TA lama yang belum di-carryover ditolak.
4. Payment dibuat `pending`; Admin/Bendahara dinotifikasi.
5. Admin/Bendahara approve/reject.
6. Payment dan audit/validator diperbarui; status tagihan dihitung ulang bila approved.

### B. Midtrans single/bulk

1. Orang tua memilih Midtrans dan tagihan milik anak.
2. `MidtransService` membaca/decrypt config dari `info_pembayaran`.
3. Sistem membuat `order_id`, customer details, item details, amount dan expiry satu hari.
4. Snap token dibuat melalui SDK; payment record pending disimpan; browser membuka Snap.
5. Midtrans memanggil webhook dengan signature.
6. SIPADUHOK memverifikasi SHA-512 `order_id + status_code + gross_amount + server_key`.
7. Semua payment pada `order_id` dipetakan statusnya; audit dan tagihan disinkronkan.
8. Pending duplikat dibatalkan dan penerima terkait dinotifikasi hanya pada transisi sukses baru.
9. Finish redirect memakai Transaction API untuk status sebenarnya bila webhook belum datang.

### C. Carryover tunggakan

1. Admin/Bendahara memilih tunggakan lama eligible.
2. Service menghitung sisa approved payment.
3. Transaction membuat tagihan TA aktif dan link asal/tujuan.
4. Orang tua membayar tagihan tujuan; original yang belum dialihkan tidak dapat dibayar dari portal.

## Payment Validation and Failure Surface

| Kondisi | Handling terimplementasi | Source |
|---|---|---|
| Key Midtrans tidak lengkap | `isConfigured()` false; flow dialihkan dengan error | MidtransService/parent controller |
| Midtrans disabled | Opsi UI/status config mengikuti `isMidtransEnabled()` | InfoPembayaran model/view |
| Tagihan bukan milik anak | Payment ditolak sebelum create | parent controller; IDOR test |
| Transfer tanpa bukti | Validation error | parent controller |
| Tunai dari portal | Explicit redirect error | parent controller |
| Tagihan lama belum carryover | Explicit error | parent controller |
| Existing pending single <24 jam | Record digunakan dengan order ID retry baru | parent controller |
| API token/status gagal | Exception dilog; pesan generik; DB tidak dipercaya dari query | service/parent controller |
| Signature webhook invalid | JSON 403 | webhook controller |
| Order tidak ditemukan | JSON 404 | webhook controller |
| Webhook berulang | Status sama dilewati; notifikasi tidak dikirim ulang | webhook controller |
| Payment lain pending pada tagihan lunas | Auto-cancel menjadi ditolak + audit | webhook/snapFinish |
| Finish redirect dipalsukan | Query status tidak dipercaya; ownership + Transaction API | parent controller/test |

## Missing Components / Information Requiring Confirmation

Karena status adalah PAYMENT IMPLEMENTED, daftar ini bukan “missing code” yang mengubah klasifikasi, tetapi gap operasional/dokumentasi:

1. `[PERLU KONFIRMASI]` Merchant ID/key sandbox atau production aktif pada database deployment.
2. `[PERLU KONFIRMASI]` Notification URL Midtrans Dashboard menunjuk HTTPS deployment yang benar dan dapat dijangkau.
3. `[PERLU KONFIRMASI]` Mode Sandbox/Production yang dipakai saat sidang/UAT.
4. `[PERLU KONFIRMASI]` Bukti transaksi nyata: order ID, waktu, status, metode, screenshot/dashboard Midtrans dan settlement.
5. `[PERLU KONFIRMASI]` SOP refund, chargeback, reconciliation dan settlement bank; tidak ditemukan sebagai business feature repository.
6. `[PERLU KONFIRMASI]` Siapa pemilik merchant account dan siapa yang berwenang mengubah credential production.
7. `.env.example` tidak memuat key Midtrans karena key disimpan terenkripsi di DB melalui UI; dokumentasi deployment harus menjelaskan kebutuhan `APP_KEY` yang stabil untuk decrypt.
8. Webhook menggunakan `Log::info('Midtrans Webhook Received', $notification)` yang dapat merekam payload gateway. Perlu kebijakan retensi/redaction log pada tahap security/deployment; tidak diubah dalam Stage 0.

## Payment and Cost-Budgeting Note

Midtrans, hosting/network, domain, email, AI provider dan storage berpotensi menjadi komponen biaya, tetapi tidak ada nominal yang ditentukan pada Stage 0. Tarif dan bukti biaya harus diisi manusia pada Stage 8.

## Integration Statistics

- Total integration point: **22**
- IMPLEMENTED: 15
- PARTIAL: 4
- CONFIGURED BUT UNUSED: 1
- REFERENCE ONLY: 2

Jumlah status diverifikasi dari 22 baris matrix saat self-review.
