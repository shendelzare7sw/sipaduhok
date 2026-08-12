# Baseline Test Run

## Ringkasan eksekusi

Baseline dijalankan **sebelum perubahan source code** sebagai Retest / Technical Verification pada **12 Agustus 2026**.

| Metrik | Hasil aktual |
|---|---:|
| Command | `php artisan test` |
| Total test | 80 |
| PASS | 79 |
| FAIL | 1 |
| Skipped | 0 |
| Assertions | 419 |
| Durasi PHPUnit | 55,84 detik |
| Exit code | 1 |

Output lengkap: [baseline-php-artisan-test.txt](evidence/baseline-php-artisan-test.txt)

SHA-256 evidence: `70F39B6DD36AEC227C735EB45A412AD16BF66E00D3AAFA0A560980A89186CE12`.

## Failed test

### `Tests\Feature\SiswaImportStatusTest`

Test: `status nonaktif tidak gagal dan akun dinonaktifkan`

Assertion yang gagal:

```text
Failed asserting that 0 matches expected 2.
tests\Feature\SiswaImportStatusTest.php:76
```

Actual warning dari importer:

```text
Baris 2: dilewati karena kolom wajib kosong: nama_kelas, agama.
Baris 3: dilewati karena kolom wajib kosong: nama_kelas, agama.
```

Interpretasi awal: importer tidak mencapai pemeriksaan status karena kedua row fixture lebih dahulu ditolak akibat dua field wajib kosong. Ini belum cukup untuk menyatakan implementation defect; requirement, template import, fixture, dan implementasi importer harus dibandingkan terlebih dahulu.

## Kontrol kronologi

- Periode pengujian project berdasarkan Timeline KAK: **April 2026 Minggu III–IV**.
- Tanggal baseline di laporan ini adalah tanggal eksekusi ulang teknis aktual, **12 Agustus 2026**, bukan klaim tanggal historis pengujian project.
