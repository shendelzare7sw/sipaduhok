# Final Regression Summary

## Hasil utama

Full regression akhir **bukan ALL PASS**. Command aktual `php artisan test` menghasilkan **84 PASS, 1 FAIL, 450 assertions dalam 5,79 detik**. Satu FAIL adalah test asset lama `SiswaImportStatusTest`; controlled test dengan field wajib lengkap membuktikan implementation path yang sama PASS.

## Test Case

| Metrik | Jumlah |
|---|---:|
| Total administratif sesuai klaim dokumen | 171 |
| Baris skenario nyata dalam DOCX | 164 |
| Automated | 13 |
| Partial | 26 |
| Manual | 121 |
| External | 4 |
| Blocked karena skenario sumber hilang | 7 |
| PASS | 13 |
| FAIL | 0 |
| BLOCKED | 12 |
| NOT EXECUTED | 146 |
| MANUAL/UAT REQUIRED | 0 |

Ketujuh ID tanpa baris sumber tetap dihitung dalam total 171 sebagai placeholder `BLOCKED`, bukan sebagai skenario buatan. `PASS + FAIL + BLOCKED + NOT EXECUTED = 171`.

## SIT

| Metrik | Jumlah |
|---|---:|
| Total planned | 45 |
| Executed dengan verdict PASS/FAIL | 9 |
| PASS | 9 |
| FAIL | 0 |
| BLOCKED | 8 |
| NOT EXECUTED | 15 |
| MANUAL/UAT REQUIRED | 13 |

Total status SIT adalah 45. Defect Midtrans disabled ditemukan saat targeted execution, diperbaiki, lalu verdict akhirnya menjadi PASS setelah retest; riwayat before/after tetap disimpan.

## Defect

| Metrik | Jumlah | Keterangan |
|---|---:|---|
| Open | 3 | DEF-001, DEF-002, DEF-004 |
| Fixed | 1 | DEF-003 |
| Retested after implementation fix | 1 | DEF-003 PASS |
| Deferred | 2 | DEF-001 dan DEF-004 |

`DEF-002` tetap open sebagai test-asset defect. Implementation-nya diverifikasi PASS melalui fixture valid, tetapi existing failing test tidak dihapus, dilonggarkan, atau diubah demi membuat suite hijau.

## Before dan after

| Run | Tests | PASS | FAIL | Assertions | Duration |
|---|---:|---:|---:|---:|---:|
| Baseline sebelum source fix | 80 | 79 | 1 | 419 | 55,84 s |
| Final regression | 85 | 84 | 1 | 450 | 5,79 s |

Lima test verifikasi ditambahkan: Midtrans disabled server-side, invalid webhook signature, notification ownership, rapor token security, dan controlled valid import fixture.

## Root cause `SiswaImportStatusTest`

1. Requirement `REQ-USR-005/006` mensyaratkan baris valid diproses dan template sesuai importer.
2. Form manual mewajibkan kelas dan agama.
3. `SiswaTemplate` secara eksplisit menulis `nama_kelas` dan `agama` sebagai wajib serta menyediakan keduanya pada contoh.
4. `SiswaImport` menolak row yang tidak mempunyai kedua field itu.
5. Fixture `SiswaImportStatusTest` tidak mempunyai keduanya, sehingga berhenti sebelum aturan status diperiksa.
6. Controlled fixture lengkap memproses dua row, memetakan `nonaktif` menjadi status akademik `aktif` dengan akun nonaktif, dan mempertahankan `lulus` dengan akun aktif.

Kesimpulan: root cause adalah **fixture test tertinggal**, bukan importer terlalu ketat. Existing test tetap FAIL dan dicatat `DEF-002`.

## Implementation fix

`MidtransService::isConfigured()` sebelumnya hanya memeriksa credential lengkap melalui `hasMidtrans()`. Setelah `DEF-003`, guard memakai `isMidtransEnabled()`, sehingga request server-side tidak dapat membuat/melanjutkan transaksi ketika kanal disabled. Test before gagal; test after dan module retest lulus.

## Masih membutuhkan manusia / external environment

- Manual technical E2E lintas role dan browser untuk 13 flow SIT.
- UAT pengguna dan acceptance mitra secara terpisah; tidak dilakukan pada tugas ini.
- Midtrans Sandbox end-to-end, Groq/Gemini, Turnstile, mail server, dan private broadcast runtime.
- Production deployment verification, termasuk host cron, HTTPS/domain, callback, storage permission, worker, backup, dan monitoring.
- Perbaikan tujuh test case yang hilang pada DOCX sumber.
- Hardening test infrastructure menjadi database testing khusus yang benar-benar terisolasi.

## Kontrol tanggal

- Original Project Testing Period: **April 2026 Minggu III–IV**.
- Current Retest / Technical Verification: **12 Agustus 2026**.
- Tidak ada tanggal historis spesifik, review date, approval, tanda tangan, UAT, atau BAST yang direkayasa.
