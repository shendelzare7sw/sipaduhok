# Production Configuration Audit

| Config Name | Location | Purpose | Required | Production Status | Secret? | Notes |
|---|---|---|---|---|---|---|
| `PAYWUZ_BASE_URL` | `.env`→`config/services.php` | REST base URL | Yes/default | CONFIGURED | No | Default `/v1` tersedia |
| `PAYWUZ_CHECKOUT_URL` | `.env`→config | recovery payment URL | Yes/default | CONFIGURED | No | URL akhir tetap divalidasi HTTPS/host |
| `PAYWUZ_SANDBOX_API_KEY` | env fallback | sandbox key | For sandbox | NOT VERIFIABLE | Yes | DB encrypted key tersedia; value tidak ditampilkan |
| `PAYWUZ_PRODUCTION_API_KEY` | env fallback | production key | For production | NOT VERIFIABLE | Yes | DB encrypted key tersedia dan read-only catalog berhasil; sumber DB lebih prioritas |
| `PAYWUZ_EXPIRY_MINUTES` | `.env`→config | expiry create | Optional | CONFIGURED | No | Default 720; clamp 5–10080 |
| DB `paywuz_*_api_key` | `info_pembayaran` | primary key storage | One active key | CONFIGURED | Yes | `Crypt::encryptString`; UI masked |
| DB `paywuz_is_production` | `info_pembayaran` | active environment | Yes | CONFIGURED | No | Runtime lokal audit sedang sandbox |
| DB `paywuz_enabled` | `info_pembayaran` | server-side toggle | Yes | CONFIGURED | No | Aktif pada runtime lokal |
| DB `paywuz_fee_by_merchant` | `info_pembayaran` | fee bearer | Yes/default | CONFIGURED | No | Nilai kebijakan ada; nominal dinamis |

Tidak ditemukan `PAYWUZ_MODE`, `PAYWUZ_SECRET`, atau `PAYWUZ_CALLBACK_URL`. Callback URL dihasilkan dari named route dan harus dikonfigurasi pada dashboard provider. Status dashboard/webhook production **NOT VERIFIABLE**. Runtime audit adalah local dengan debug/log level development; bukan bukti deployment production.
