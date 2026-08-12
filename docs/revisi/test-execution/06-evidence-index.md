# Evidence Index

Semua evidence di bawah dihasilkan pada **12 Agustus 2026** sebagai Retest / Technical Verification. Periode Pengujian Project tetap **April 2026 Minggu III–IV** dan tidak diubah oleh timestamp evidence saat ini.

| Evidence | Command / cakupan | Hasil objektif |
|---|---|---|
| `evidence/baseline-php-artisan-test.txt` | `php artisan test` sebelum perubahan source | 79 PASS, 1 FAIL, 419 assertions, 55,84 s; SHA-256 `70F39B6DD36AEC227C735EB45A412AD16BF66E00D3AAFA0A560980A89186CE12`. |
| `evidence/automated-test-inventory.txt` | `php artisan test --list-tests` | Inventaris test otomatis sebelum penambahan targeted coverage. |
| `evidence/before-midtrans-disabled-server-side.txt` | Filter `MidtransDisabledServerSideTest`, sebelum fix | FAIL: service menganggap kanal disabled siap. |
| `evidence/after-midtrans-disabled-server-side.txt` | Filter yang sama, setelah fix | 1 PASS, 1 assertion. |
| `evidence/targeted-payment-module-after-fix.txt` | Payment/Midtrans/ownership/financial filter | 9 PASS, 56 assertions. |
| `evidence/targeted-authorization-integrity.txt` | Role, cabang, kelas, assignment, storage, nilai, promotion | 20 PASS, 120 assertions. |
| `evidence/targeted-siswa-import-valid-fixture.txt` | Controlled import dengan kelas dan agama wajib | 1 PASS, 11 assertions. |
| `evidence/targeted-webhook-notification-security.txt` | Invalid webhook signature dan notification ownership | 2 PASS, 9 assertions. |
| `evidence/targeted-rapor-download-token.txt` | Token salah, expired, dan lintas-user | 1 PASS, 10 assertions. |
| `evidence/scheduler-registration.txt` | `php artisan schedule:list` | Notification terdaftar 06:00 Asia/Jakarta; promotion tiap menit dengan lock pada source. |
| `evidence/promotion-scheduler-no-due-run.txt` | `php artisan promotion:execute-scheduled` setelah precheck due=0 | Command berhasil, tidak ada schedule due; bukan bukti eksekusi promotion/overlap. |
| `evidence/test-database-connection-audit.txt` | Audit override koneksi pada test | 38 file mengarah ke MySQL lokal; 37 transaction-guarded; dicatat `DEF-004`. |
| `evidence/final-php-artisan-test.txt` | Full regression setelah perubahan | 84 PASS, 1 FAIL, 450 assertions, 5,79 s; SHA-256 `A8F8CFEF08CCDE3C0EF296B820650787D9488E2C4E9DE8338FCC2121EB7C2B2E`. |

## Cara membaca evidence

- PASS pada CSV hanya diberikan bila expected result terkait didukung command/test aktual.
- Evidence parsial tidak dinaikkan menjadi PASS untuk skenario yang expected result-nya lebih luas.
- Source inspection dan `schedule:list` dipakai sebagai evidence konfigurasi/registrasi, bukan sebagai bukti flow bisnis end-to-end.
- Log tidak berisi Server Key, API secret, credential production, atau data settlement riil.
