# Webhook Security Audit

Endpoint publik: `POST /payments/paywuz/webhook`; CSRF dikecualikan secara eksplisit. Autentikasi memakai HMAC-SHA256 atas raw body dengan sandbox/production API key dan constant-time `hash_equals`. Header yang diwajibkan: `X-Paywuz-Signature` dan delivery ID; event header diperiksa bila ada.

| Control | Result | Evidence/Risk |
|---|---|---|
| Public endpoint + CSRF exception | PASS | Tepat untuk server-to-server; tidak berada dalam auth group |
| Raw-body HMAC | PASS | Format `sha256=<64 hex>`, mencoba key environment transaksi; unknown order mencoba key yang tersedia |
| Payload validation | **FAIL—CRITICAL** | Controller meminta wrapper `event/data`; SDK resmi mengirim body datar dan event di header. Payload resmi tersanitasi mendapat HTTP 400 |
| Event/status consistency | PASS for implemented wrapper | settlement/paid/failed/cancelled dibatasi terhadap status |
| Ownership/reference | PASS | order hanya dicari pada `payment_gateway=paywuz`; existing transaction ID harus sama |
| Amount verification | PASS | Total row lokal harus sama dengan amount payload |
| Duplicate delivery | PASS | unique `delivery_id`, `insertOrIgnore`, processed timestamp |
| Financial idempotency | PASS | row lock, transition guard, approved no-downgrade, notification hanya transisi baru |
| Unknown order | PARTIAL | Signature valid → 202 tanpa delivery record; aman dari mutation, tetapi tidak ada forensic ledger |
| Retry failure | PASS | delivery row dihapus bila apply melempar agar retry dapat diproses |
| Replay freshness | PARTIAL | Timestamp divalidasi sebagai string saja; tidak ada freshness window. Delivery ID baru dapat mengulang body valid, walau mutation status idempotent |
| Logging/data leakage | PASS/PARTIAL | Tidak menyimpan raw webhook; hanya hash. Error log dapat memuat pesan provider/order ID, bukan key |

SDK resmi menyebut body datar dan 4xx tidak di-retry. Karena itu mismatch dapat menghilangkan callback final; polling saat halaman dibuka dan command manual bukan pengganti webhook yang andal. Referensi: [Paywuz SDK README](https://gitlab.com/paywuz/paywuz-sdk/-/blob/main/README.md).

Keputusan: **NOT PRODUCTION-READY untuk webhook** sampai parser menerima kontrak datar resmi dan diuji valid/invalid/duplicate terhadap fixture kontrak provider. Setelah itu tambahkan batas usia timestamp yang disepakati provider.
