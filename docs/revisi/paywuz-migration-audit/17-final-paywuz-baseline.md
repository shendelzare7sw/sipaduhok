# Final Paywuz Baseline

| Item | Final baseline |
|---|---|
| PAYMENT PROVIDER | **Paywuz** |
| PAYMENT IMPLEMENTATION | **PARTIAL** — REST/flow/status implemented; official webhook contract gagal |
| PRODUCTION CONFIGURATION | **VERIFIED** secara read-only untuk credential/catalog; active runtime lokal sandbox |
| PRODUCTION END-TO-END | **NOT VERIFIED** — owner attestation ada, artifact teknis tidak tersedia |
| PAYMENT METHODS | QRIS, VA meta; katalog project juga menyediakan BCA/BNI/BRI/BSI/CIMB/Danamon/Mandiri/Maybank/OCBC/Permata VA, Alfamart, Indomaret |
| PAYMENT ACTOR | Orang Tua/Wali Siswa untuk anak tertaut |
| WEBHOOK | **FAIL/PARTIAL** — HMAC/idempotency baik, body schema salah |
| STATUS MAPPING | pending→pending; settlement/success→disetujui; failed/cancelled/expired→ditolak |
| IDEMPOTENCY | Implemented pada order ID, delivery ID, row lock, transition/notif guard, remote cancel |
| AUDIT | Implemented via `FinancialAuditLog` pada transisi |
| NOTIFICATION | Implemented ke Admin/Bendahara/Wali pada transisi approved pertama |
| INVOICE | Implemented, menampilkan channel/subtotal/fee/total/status |
| MIDTRANS | Runtime **REMOVED**; schema/data/doc references **LEGACY** |
| TEST RESULT | Full 137 PASS/1 unrelated FAIL; targeted 38 PASS; SIT file 6 PASS; official webhook probe FAIL |

## Documents requiring update

Requirement baseline/BAST addendum, komparasi SDLC, FSD, TSD, BAST FSD/TSD, API Payment, Test Case, SIT/BAST SIT, UAT/BAST UAT, Deployment/BAST Deployment, Cost Budgeting. BA Serah Terima Akhir yang sudah ditandatangani tidak diubah; gunakan catatan/addendum.

## Open items

1. Perbaiki webhook agar menerima body datar resmi dan event dari header; retest valid/invalid/duplicate/amount/reference/unknown/replay.
2. Jalankan Paywuz sandbox E2E dengan public HTTPS callback.
3. Sesudah izin, kumpulkan satu evidence production tersanitasi tanpa transaksi baru.
4. Konfirmasi channel yang sengaja ditawarkan mitra dan siapa penanggung fee.
5. Putuskan scheduler reconcile, retention raw response, dan pembersihan credential/schema Midtrans lama.
6. Perbarui artefak sumber lama; baru kemudian Test Case/SIT/UAT/BAST melalui approval manusia.

Kesimpulan objektif: Paywuz telah menggantikan Midtrans pada runtime, tetapi baseline belum layak disebut production-ready penuh sebelum defect kontrak webhook ditutup.
