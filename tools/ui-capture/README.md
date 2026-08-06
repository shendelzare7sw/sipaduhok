# tools/ui-capture — Screenshot otomatis UI

Menangkap screenshot seluruh layar aplikasi per role, sebagai:
1. **Referensi kebenaran visual** saat merekonstruksi prototype Figma (`docs/figma/`).
2. **Lampiran screenshot** untuk skripsi.

Ini alat bantu dokumentasi — **tidak dipakai aplikasi saat runtime** dan tidak
memengaruhi `npm run build`.

## Persiapan (sekali saja)

```bash
npm install                      # playwright sudah masuk devDependencies
npx playwright install chromium  # unduh browser (~130 MB)

cp tools/ui-capture/akun.contoh.json tools/ui-capture/akun.local.json
```

Lalu buka `akun.local.json` dan isi:

- **`baseUrl`** — URL aplikasi lokal (`http://sipaduhok.test` untuk Laragon).
- **`akun`** — kredensial untuk tiap role. Kolom `login` mengikuti nama input di form
  (`resources/views/auth/login.blade.php:122`), jadi bisa email atau username.
- **`params`** — ID nyata dari database lokal untuk layar yang URL-nya berparameter
  (mis. `wali-siswa/tagihan/anak/{siswa}`). Layar yang param-nya kosong akan dilewati,
  bukan menggagalkan proses.

> `akun.local.json` sudah masuk `.gitignore`. Jangan pernah di-commit.

## Menjalankan

```bash
npm run capture:ui                       # semua role
node tools/ui-capture/capture.mjs admin  # satu role saja
node tools/ui-capture/capture.mjs guru siswa
```

Hasil masuk ke `docs/figma/screenshots/<role>/<id>.png` (full page, lebar 1440,
`deviceScaleFactor: 2` agar tajam saat dicetak), plus `laporan.json` berisi status tiap layar.

## Membaca hasil

| Status | Arti | Tindakan |
|---|---|---|
| `ok` | Screenshot berhasil | — |
| `dialihkan` | Halaman terbuka tapi URL akhir berbeda | Biasanya prasyarat belum dipenuhi — mis. wali kelas belum memilih kelas aktif. Login manual sekali, pilih kelas, lalu ulangi. |
| `dilewati` | Param belum diisi | Isi ID-nya di `params` pada `akun.local.json`. |
| `gagal` | HTTP ≥ 400, timeout, atau login gagal | Lihat `alasan` di `laporan.json`. |

Skrip keluar dengan kode 1 bila ada yang `gagal`, supaya kegagalan tidak lewat diam-diam.

## Kalau login selalu gagal

- Pastikan `TURNSTILE_SITE_KEY` **kosong** di `.env` lokal. Kalau terisi, captcha Cloudflare
  ikut dirender dan otomasi tidak bisa lewat — skrip akan menyebutkan ini secara eksplisit.
- Pastikan akunnya aktif; middleware `student.active` memblokir siswa non-aktif.

## Catatan privasi

Screenshot diambil dari database lokal dan **bisa memuat nama serta data siswa nyata**.
Folder `docs/figma/screenshots/` sudah di-`.gitignore`. Periksa isinya sebelum
melampirkannya ke skripsi atau membagikannya.
