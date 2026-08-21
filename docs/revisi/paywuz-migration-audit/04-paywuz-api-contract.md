# Kontrak API Paywuz yang Dipakai Source

Sumber eksternal primer: [Paywuz SDK README](https://gitlab.com/paywuz/paywuz-sdk/-/blob/main/README.md). Source aplikasi tidak memakai package SDK; kontrak diimplementasikan melalui Laravel HTTP client.

## Koneksi dan autentikasi

| Item | Implementasi |
|---|---|
| Base URL | `PAYWUZ_BASE_URL`, default `https://api.paywuz.id/v1` |
| Checkout fallback | `PAYWUZ_CHECKOUT_URL/{transaction-id}`, default host `https://paywuz.id/pay` |
| Environment | Host sama; key `pk_sand_…` atau `pk_live_…` menentukan sandbox/production |
| Authentication | `Authorization: Bearer [SECRET - STORED IN ENV/CONFIG]` |
| Content negotiation | `Accept: application/json`, JSON request |
| Timeout/retry | connect 5 detik, total 15 detik, dua retry berjarak 300 ms |

## Endpoint aktual

| Endpoint | Method | Request | Response yang dipakai |
|---|---|---|---|
| `/payment-methods` | GET | tidak ada body | `data[]`: `code`, `name`, `type`, `fee.flatIdr`, `fee.percentBps`, `limits.minIdr`, `limits.maxIdr` |
| `/transactions` | POST | lihat payload create | `data.id`, `orderId`, `amount`, `status`, `paymentUrl`; juga `totalPayment`, `paymentMethod`, `expiresAt` bila ada |
| `/transactions/{orderId}` | GET | order di URL | kontrak transaksi; 404 diperlakukan “belum/tidak ada”, error lain dilempar |
| `/transactions/{orderId}/cancel` | POST | tidak ada body | minimal `id`, `orderId`, `status`; payload GET sebelumnya melengkapi field lain |

Payload create yang dikirim:

```json
{
  "orderId": "[GENERATED SERVER-SIDE]",
  "amount": "[INTEGER IDR FROM SERVER]",
  "paymentMethod": "[CODE FROM PROVIDER CATALOG]",
  "expiryMinutes": "[5..10080; CONFIG DEFAULT 720]",
  "redirectUrl": "[OWNED PAYMENT PAGE URL]",
  "feeByMerchant": "[BOOLEAN CONFIG]",
  "metadata": {
    "pembayaran_id": "[LOCAL ID]",
    "siswa_id": "[LOCAL ID]"
  }
}
```

`orderId` dan `amount` response harus tepat sama dengan request. `totalPayment` tidak boleh kurang dari amount. Payment URL harus HTTPS pada `paywuz.id`, subdomainnya, atau `paywuz.com`/subdomainnya. Menurut dokumentasi SDK, create idempotent berdasarkan `orderId`.

## Kontrak webhook resmi vs implementasi

SDK resmi menyatakan event berada pada header `X-Paywuz-Event`, delivery ID pada `X-Paywuz-Delivery`, signature pada `X-Paywuz-Signature`, dan body berupa objek datar berisi `id`, `orderId`, `amount`, `fee`, `totalPayment`, `paymentMethod`, `status`, `timestamp`, serta `metadata` opsional.

Controller menerima body datar tersebut dan menjadikan `X-Paywuz-Event` sebagai event wajib. Signature diverifikasi terhadap raw body sebelum mutasi finansial; event harus sesuai dengan status transaksi, timestamp dibatasi oleh freshness window, dan delivery ID dideduplikasi. Fixture kontrak resmi kini menghasilkan HTTP 200 dan mutasi finansial idempoten pada SIT.
