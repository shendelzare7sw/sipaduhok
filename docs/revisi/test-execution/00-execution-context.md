# Konteks Eksekusi Retest / Technical Verification SIPADUHOK

## Identitas dan periode

- Jenis kegiatan: **Retest / Technical Verification** terhadap baseline aplikasi saat ini.
- Tanggal eksekusi aktual: **12 Agustus 2026** (Asia/Jakarta).
- Waktu preflight tercatat: **18:40:26 WIB**.
- Periode pengujian project menurut Timeline KAK: **April 2026 Minggu III–IV**.
- Tanggal aktual di atas adalah tanggal retest teknis saat ini, bukan tanggal historis pengujian project.

## Environment terdeteksi

| Item | Hasil aktual |
|---|---|
| Sistem operasi | Microsoft Windows NT 10.0.22631.0 |
| PHP CLI | PHP 8.3.19 (ZTS, Visual C++ 2019 x64) |
| Composer | 2.8.8 |
| Laravel Framework | 11.54.0 |
| Environment aplikasi saat proses CLI biasa | `local` |
| Timezone aplikasi | `Asia/Jakarta` |
| Timezone default PHP CLI | `UTC` |
| Konfigurasi test | `phpunit.xml` |
| APP_ENV pada PHPUnit | `testing` |
| Database driver pada PHPUnit | `sqlite` |
| Database pada PHPUnit | `:memory:` |
| Ekstensi SQLite | `pdo_sqlite`, `sqlite3` tersedia |
| Cache test | `array` |
| Session test | `array` |
| Queue test | `sync` |
| Mailer test | `array` |

## Verifikasi keselamatan database

`phpunit.xml` secara eksplisit menetapkan SQLite in-memory melalui `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`. Namun audit akhir menemukan **38 file Feature test mengganti konfigurasi tersebut secara programatis** ke MySQL lokal `127.0.0.1`, database `db_sipaduhok`. Sebanyak 37 file mempunyai pasangan `beginTransaction`/`rollBack`; satu file tanpa transaction hanya melakukan verifikasi parsing/struktur dan tidak menulis data. Host lokal, akun lokal, dan transaction rollback mengurangi risiko, tetapi suite belum dapat dinyatakan sepenuhnya terisolasi. Kondisi ini dicatat sebagai `DEF-004` dan evidence-nya tersedia pada `evidence/test-database-connection-audit.txt`.

Tidak ada indikasi koneksi remote/production pada konfigurasi test yang ditemukan. Tidak ada `migrate:fresh` yang dijalankan dan tidak ada credential production yang digunakan. Retest tambahan yang mengakses MySQL lokal juga dibungkus transaction rollback.

File `.env.testing` tidak tersedia; isolasi tetap diberikan langsung oleh variabel environment di `phpunit.xml`.

## Preflight command

Command yang benar-benar dijalankan:

```text
php -v
composer --version
php artisan --version
php artisan env
php artisan about --only=environment
php artisan optimize:clear
php -m
```

`php artisan optimize:clear` selesai berhasil untuk cache, compiled, config, events, routes, dan views sebelum baseline dijalankan.

## Batasan konteks

- Retest ini tidak merupakan UAT dan tidak membuktikan acceptance pengguna/mitra.
- Retest ini tidak melakukan transaksi finansial riil.
- Integrasi eksternal hanya boleh dinilai PASS apabila ada eksekusi aktual dengan environment uji yang aman; tanpa credential sandbox atau network yang sesuai statusnya harus BLOCKED.
- Aktivasi cron pada host production tidak dinilai oleh pengujian application scheduler lokal.
- Tidak ada nama/tanggal approval atau tanda tangan yang diisi otomatis.

## SDM dokumen testing

- Penyusun / QA: Yayan Wahyudi.
- Reviewer Teknis / Integrasi: Tabah Ujianto.
- Reviewer / Koordinator Tim Project: Irent Berliana Agustin.
- Perwakilan Mitra (bila diperlukan): `[DIISI BILA DIPERLUKAN]`.

Nama-nama tersebut adalah pembagian peran yang direkomendasikan berdasarkan Lampiran 2, bukan bukti bahwa review atau approval telah dilakukan.
