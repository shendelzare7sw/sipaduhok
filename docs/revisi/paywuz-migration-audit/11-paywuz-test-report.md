# Paywuz Test Report

Tanggal technical verification: **21 Agustus 2026**. Periode historis project tetap **April 2026 Minggu III–IV**.

| Test | Command | Result | Assertions/Evidence | Notes |
|---|---|---|---|---|
| Full regression | `php artisan test` | 137 PASS, 1 FAIL; 635 assertions | Console run | Failure `SiswaImportStatusTest` karena fixture lama kurang `nama_kelas/agama`; tidak terkait Paywuz |
| Targeted Paywuz/payment | 7 files Paywuz/config/notif/IDOR/unit | 38 PASS, 86 assertions | Contract create/get/cancel, selected channel, disabled, direct toggle, ownership, notification/audit/labels | HTTP provider di-fake; tidak membuat payment riil |
| Domain SIT | `php artisan test tests/Feature/SITClosureDomainIntegrationTest.php` | 6 PASS, 51 assertions | Termasuk duplicate signed callback, invalid signature, manual-override guard | Test callback memakai wrapper implementasi, bukan payload resmi |
| Official webhook contract probe | Signed flat body sesuai SDK ke local route, unknown sanitized order | **FAIL: HTTP 400** | `Invalid webhook payload` | Expected endpoint menerima/menjawab aman; actual kontrak tertolak |
| Read-only provider catalog | `GET /payment-methods`, sandbox dan production key | PASS: HTTP 200, 14 entry masing-masing | Tidak mencetak key/fee sensitif | Bukan transaction E2E |

Coverage tersedia: transaction creation, disabled config, ownership, amount/response validation, create recovery, channel replacement, status correction, duplicate callback internal, signature invalid, tagihan/audit/notifikasi. Gap: valid official flat callback, duplicate official callback, unknown official transaction test formal, timestamp freshness, provider sandbox E2E, production E2E, scheduled reconciliation.

Kesimpulan test: **REST/application path PASS; webhook provider contract FAIL; full suite PARTIAL karena satu failure non-payment.**
