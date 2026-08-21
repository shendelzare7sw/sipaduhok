# Webhook Security Audit

Endpoint publik: `POST /payments/paywuz/webhook`; CSRF dikecualikan secara eksplisit. Autentikasi memakai HMAC-SHA256 atas raw body dengan sandbox/production API key dan constant-time `hash_equals`. Header `X-Paywuz-Signature`, `X-Paywuz-Delivery`, dan `X-Paywuz-Event` diwajibkan.

| Control | Result | Evidence/Risk |
|---|---|---|
| Public endpoint + CSRF exception | PASS | Tepat untuk server-to-server; tidak berada dalam auth group |
| Raw-body HMAC | PASS | Format `sha256=<64 hex>`, mencoba key environment transaksi; unknown order mencoba key yang tersedia |
| Payload validation | PASS | Parser menerima body datar SDK resmi: `id`, `orderId`, `amount`, `fee`, `totalPayment`, `paymentMethod`, `status`, `timestamp`, dan `metadata` opsional |
| Event/status consistency | PASS | Event wajib dari header; settlement/paid/failed/cancelled dibatasi terhadap status yang sesuai |
| Ownership/reference | PASS | Order hanya dicari pada `payment_gateway=paywuz`; existing transaction ID harus sama |
| Amount verification | PASS | Total row lokal harus sama dengan amount payload |
| Duplicate delivery | PASS | Unique `delivery_id`, `insertOrIgnore`, processed timestamp |
| Financial idempotency | PASS | Row lock, transition guard, approved no-downgrade, notification hanya transisi baru |
| Unknown order | PASS | Signature valid menghasilkan 202 tanpa mutasi dan tetap dicatat pada forensic delivery ledger |
| Retry failure | PASS | Delivery row dihapus bila apply melempar agar retry dapat diproses |
| Replay freshness | PASS | Timestamp wajib dapat diparse dan dibatasi 900 detik secara default (`PAYWUZ_WEBHOOK_TOLERANCE_SECONDS`, clamp 60-3600 detik) |
| Logging/data leakage | PASS/PARTIAL | Tidak menyimpan raw webhook; hanya hash. Error log dapat memuat pesan provider/order ID, bukan key |

SDK resmi menyebut body datar dan 4xx tidak di-retry. Parser dan fixture regresi kini mengikuti kontrak tersebut. Referensi: [Paywuz SDK README](https://gitlab.com/paywuz/paywuz-sdk/-/blob/main/README.md).

Keputusan: **PASS untuk kontrak dan keamanan webhook** setelah payload resmi valid menghasilkan HTTP 200, signature palsu ditolak, duplicate delivery idempoten, header event wajib, dan replay kedaluwarsa ditolak tanpa mutasi.
