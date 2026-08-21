# Database Change Map

| Table | Column | Purpose | Created/Modified | Used By |
|---|---|---|---|---|
| `info_pembayaran` | `paywuz_sandbox_api_key`, `paywuz_production_api_key` | Key terenkripsi at rest | Created | model/config controllers/service |
| `info_pembayaran` | `paywuz_is_production`, `paywuz_enabled`, `paywuz_fee_by_merchant` | mode, activation, fee bearer | Created | service/UI/controllers |
| `info_pembayaran` | `direct_transfer_enabled` | toggle kanal manual terpisah | Created | parent/settings flow |
| `pembayaran` | `metode_pembayaran` | enum→string(30), mendukung Paywuz dan histori | Modified | seluruh payment flow |
| `pembayaran` | generic `payment_gateway`, `transaction_id`, `order_id`, `payment_type`, `gateway_response` | provider/reference/order/channel/raw normalized response | Existing/reused | service/status/views |
| `pembayaran` | `payment_url`, `gateway_total`, `payment_environment`, `gateway_status` | checkout, total+fee, key environment, provider state | Created | digital/status/invoice |
| `pembayaran` | `payment_expires_at`, `gateway_settled_at` | lifecycle timestamps | Created | status/UI |
| `pembayaran` | `gateway_error`, `gateway_attempts` | recoverable failure tracking | Created | digital controller |
| `pembayaran` | index `(payment_gateway, order_id)` | status lookup | Created | status/webhook/reconcile |
| `payment_webhook_deliveries` | provider, unique delivery_id, event, pembayaran_id, payload_hash, processed_at | dedupe dan minimal forensic ledger | New table | webhook controller |

Tidak ada tabel invoice khusus; invoice dibentuk dari payment/tagihan. Audit memakai `financial_audit_logs`, notifikasi memakai tabel notifikasi yang sudah ada. Kolom konfigurasi Midtrans tetap ada sebagai legacy dan tidak dibaca runtime.
