# Payment Security Findings

| Severity | Finding | Evidence | Recommendation |
|---|---|---|---|
| CRITICAL | Webhook parser tidak cocok dengan payload datar resmi | controller vs Paywuz SDK; probe HTTP 400 | Perbaiki contract, tambah fixture official dan sandbox callback retest sebelum production-ready |
| HIGH | Klaim production E2E tidak didukung artifact tersedia | 0 local production payment/processed delivery | Ambil evidence tersanitasi dari environment production setelah izin |
| MEDIUM | Tidak ada freshness/replay window timestamp | timestamp hanya string | Validasi ISO timestamp dan tolerance sesuai provider |
| MEDIUM | Reconciliation command tidak scheduled | tidak ada entry di `routes/console.php` | Putuskan jadwal `withoutOverlapping` sebagai recovery, bukan pengganti webhook |
| MEDIUM | Full raw API response disimpan di `gateway_response` | status service/controller | Allowlist field atau enkripsi/retensi; pastikan provider tidak mengirim PII/secret |
| MEDIUM | Legacy Midtrans credentials/flags tetap di DB | old schema and current aggregate | Buat keputusan retensi; hapus/masking via migration terkontrol setelah backup dan approval |
| LOW | Unknown signed order tidak masuk delivery ledger | webhook returns 202 early | Simpan event hash/security counter tanpa payload/PII untuk forensik |
| ENV | Template `.env.example` memakai debug/log level development | `.env.example` | Production wajib `APP_DEBUG=false`, log level sesuai kebutuhan, TLS/public callback benar |

Kontrol positif: API key DB dienkripsi; env fallback didukung; UI password/masked; key tidak dikirim ke browser; Bearer hanya server-side; trusted HTTPS checkout host; raw webhook tidak disimpan; HMAC constant-time; ownership/amount/reference/row-lock/idempotency tersedia; error user digeneralisasi. Log memuat order ID dan exception message, bukan credential secara eksplisit—tetap lakukan redaction review provider errors.
