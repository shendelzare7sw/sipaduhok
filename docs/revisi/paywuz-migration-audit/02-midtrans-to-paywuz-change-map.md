# Change Map Midtrans ke Paywuz

Perbandingan source menggunakan `git diff clean-production...73870f0` dan penelusuran runtime saat ini.

| Area | Previous Midtrans Behavior | Current Paywuz Behavior | Changed File | Behavioral Impact | Documentation Impact |
|---|---|---|---|---|---|
| Client | SDK `midtrans/midtrans-php`, Snap/Transaction API | Laravel HTTP client ke REST Paywuz | `composer.*`; `PaywuzService.php`; penghapusan `MidtransService.php` | Tidak ada Snap token; transaksi menghasilkan payment URL/instruction | TSD, API, deployment |
| Route callback | `POST /midtrans/notification` | `POST /payments/paywuz/webhook` | `routes/web.php` | Endpoint provider berubah | FSD, TSD, SIT, deployment |
| Parent route | Snap page/finish/continue | Digital page/sync/continue/change method | `routes/web.php`; controller baru | Browser redirect tidak dipercaya sebagai status; polling server-side tersedia | FSD, UAT, test case |
| UI | Snap popup | Hosted Paywuz checkout/payment instructions | wali views/JS/CSS | User memilih QRIS/VA/retail sesuai katalog, lalu membuka URL | UAT/user guide |
| Authentication | Midtrans server key/signature SHA-512 | Bearer API key; webhook HMAC-SHA256 raw body | services/controllers/config | Credential dan signature scheme berubah | Security/TSD/deployment |
| Configuration | merchant ID, server/client key, production toggle | sandbox/production API key, mode, enabled, fee bearer, endpoints/expiry | config/model/controllers/view/migration | Client key browser tidak diperlukan | Deployment/cost/security |
| Create | Snap token/order | `POST /transactions`, idempotent `orderId` | `PaywuzService`; digital controller | Local pending dibuat sebelum provider call; 404/retry recovery | FSD/SIT |
| Status | capture/settlement/pending/deny/expire/cancel | pending/settlement/success/failed/cancelled/expired | Paywuz service/status service | Mapping internal disesuaikan | API/test/SIT |
| Duplicate | status transition guards | delivery ledger, row locks, paid no-downgrade, remote cancel before replacement | status service; webhook; migration | Risiko double financial effect dikurangi | TSD/SIT/security |
| Failure | Snap/lookup exception path | retry HTTP, recovery by same order ID, gateway error/attempts, manual reconcile | service/controller/command/migration | Gagal provider tidak otomatis dianggap gagal bayar | FSD/SIT/ops |
| Database | generic gateway columns + Midtrans config | generic columns retained; Paywuz keys/state/URL/fee/expiry/webhook ledger added | three 2026 migrations | Migration additive dan histori tetap terbaca | TSD/deployment/data dictionary |
| Notification | settlement callback | transition pertama ke approved dari callback/polling/reconcile | status/notification service | Admin, Bendahara, wali mendapat notifikasi sekali per transisi | FSD/SIT/UAT |
| Audit | callback status audit | `FinancialAuditLog` per status transition | status service | System actor; old/new state tersimpan | FSD/SIT |
| Scheduler | status lookup on UX | command reconcile tersedia tetapi tidak scheduled | `ReconcilePaywuzPayments.php`; `routes/console.php` | Recovery otomatis berkala belum terbukti | Deployment/operations |
| Tests | Midtrans disabled/webhook/finish tests | Paywuz contract/channel/status/IDOR tests | `tests/**` | Coverage REST membaik; official flat webhook tidak diuji | Test Case/SIT |

## Status Midtrans final

**Runtime: REMOVED.** Service, controller, route, Snap assets, tests, dan Composer package Midtrans telah dihapus; tidak ada fallback runtime aktif.

**Data/schema: LEGACY.** Kolom `midtrans_merchant_id`, `midtrans_server_key`, `midtrans_client_key`, `midtrans_is_production`, `midtrans_enabled`, nilai historis `metode_pembayaran=midtrans`, komentar migration, dan generator dokumentasi lama masih ada. Kolom itu tidak dibaca oleh flow Paywuz saat ini. Penghapusan data/kolom lama tidak dilakukan karena retensi histori dan keamanan migrasi belum mempunyai keputusan formal.
