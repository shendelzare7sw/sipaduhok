# SIT and UAT Impact

## SIT technical yang wajib direvisi

- `SIT-E2E-008`: Midtrans Snap E2E → Paywuz create/payment URL/pay/status/invoice E2E.
- `SIT-INT-008`: Midtrans API → Paywuz payment-methods/create/get/cancel, sandbox dahulu.
- `SIT-INT-009`: Midtrans callback → Paywuz flat webhook, headers/HMAC, mapping dan retry.
- `SIT-RSK-007`: signature invalid menggunakan HMAC-SHA256 Paywuz.
- `SIT-RSK-008`: delivery ID duplicate dan repeated status; pastikan no double notification/audit/effect.
- `SIT-RSK-009`: forged finish → redirect tidak boleh mengubah status; polling harus otoritatif.
- `SIT-RSK-010`: Paywuz disabled harus ditolak server-side.
- Tambahkan/trace: amount mismatch, unknown order, order ID idempotency, create timeout recovery, cancel uncertainty, bulk DB sync, approved no-downgrade, reconcile command, fee/total, expiry.

Status sekarang: mock/local REST/status/idempotency sebagian besar PASS; official webhook **FAIL**; provider sandbox payment dan production E2E **NOT VERIFIED**. BAST SIT belum layak dibuat.

## UAT user-facing yang harus diganti

Tanpa membuat dokumen UAT pada audit ini, skenario selanjutnya perlu menguji: wali melihat hanya tagihan anak; memilih QRIS/VA/retail yang benar-benar tampil; membuka hosted checkout; menyelesaikan pembayaran sandbox; kembali ke aplikasi; sync/refresh; melihat status, sisa, invoice/history dan fee; mengganti channel pending dengan aman; pesan saat provider gagal/expired; Admin/Bendahara menerima hasil/audit/notifikasi. UAT harus manual bersama aktor berwenang setelah webhook diperbaiki dan SIT lulus.
