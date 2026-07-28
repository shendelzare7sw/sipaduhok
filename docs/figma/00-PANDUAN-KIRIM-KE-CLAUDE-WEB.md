# 00 — Panduan: Apa yang Harus Dikirim ke Claude Web

> **Jawaban singkat: kamu tidak perlu menyiapkan apa pun.** Semua bahan sudah ada di folder
> ini. Kamu tinggal upload dan menempel prompt.

---

## Langkah 1 — Siapkan berkas (5 menit)

Zip folder UI kit lebih dulu, karena isinya banyak berkas:

```bash
# dari root proyek
cd docs/figma
# Windows (PowerShell)
Compress-Archive -Path ui-kit -DestinationPath ui-kit.zip -Force
```

Lalu upload **7 berkas ini sekaligus** ke percakapan Claude web yang sudah terhubung Figma,
**bersamaan dengan Batch 0** (Langkah 3). Semuanya dikirim satu kali di awal — tidak ada
berkas yang menyusul di tengah jalan.

| # | Berkas | Isi | Dipakai di | Ukuran |
|---|---|---|---|---|
| 1 | `tokens.json` | Token desain format mesin | **Batch 1** | ~11 KB |
| 2 | `01-design-tokens.md` | Penjelasan token + lokasi sumbernya di kode | **Batch 1** | ~9 KB |
| 3 | `02-inventaris-komponen.md` | Spesifikasi 17 komponen beserta variannya | **Batch 2** | ~10 KB |
| 4 | `03-inventaris-layar.md` | Daftar frame ↔ route ↔ berkas blade | **Batch 3 & 5** | ~12 KB |
| 5 | `04-navigasi-sidebar-per-role.md` | Pohon menu 11 varian sidebar | **Batch 2** | ~22 KB |
| 6 | `05-alur-prototype.md` | 6 alur untuk wiring prototype | **Batch 4** | ~8 KB |
| 7 | `ui-kit.zip` | HTML statis 61 layar (57 wajib + 4 publik opsional) + komponen | **Batch 3** | ~4 MB |

### Yang TIDAK diupload — dan alasannya

| Berkas | Kenapa tidak |
|---|---|
| `daftar-layar.json` | Berkas untuk mesin, dibaca oleh `tools/ui-capture/capture.mjs` sebagai sumber daftar URL screenshot. Isinya sama dengan `03-inventaris-layar.md`, hanya beda format — cukup kirim versi markdown-nya. |
| `06-prompt-claude-web.md` | Isinya kamu **tempel** batch per batch, bukan diupload. Kalau diupload utuh, Claude akan melihat semua instruksi sekaligus dan cenderung mengerjakannya berbarengan. |
| `07-bab-perancangan-prototype.md` | Materi untuk skripsimu, tidak dibutuhkan Claude untuk membuat prototype. |
| `screenshots/` | Berpotensi memuat data siswa nyata. Pakai sebagai pembanding pribadi saja. |

---

## Langkah 2 — Pasang plugin di Figma (10 menit, sekali saja)

1. Buka Figma Community, cari **html.to.design**, klik *Try it out*.
2. Pasang juga **ekstensi browser** html.to.design (Chrome/Edge/Firefox).
   Ini wajib — bukan opsional. Lihat penjelasannya di Langkah 4.
3. Pastikan font **Public Sans** tersedia di Figma. Kalau belum, pasang Figma Font Helper
   dan install fontnya, atau pakai Figma versi web yang mengambil font dari Google Fonts.

---

## Langkah 3 — Tempel prompt secara berurutan

Buka [`06-prompt-claude-web.md`](06-prompt-claude-web.md), tempel satu batch per pesan:

- **Batch 0** — konteks awal (kirim bersama ketujuh berkas di atas)
- **Batch 1** — buat color / text / effect style
- **Batch 2** — buat komponen dan variannya
- **Batch 3** — layar (lihat Langkah 4, sebagian kamu kerjakan sendiri)
- **Batch 4** — wiring prototype 6 alur
- **Batch 5** — pemeriksaan akhir

Jangan digabung. Kalau ada yang gagal, kamu tahu persis batch mana yang perlu diulang.

---

## Langkah 4 — Import 57 layar setelah login (kamu yang kerjakan, bukan Claude)

Ini bagian terpenting dan paling sering salah.

**Jangan minta Claude web menggambar 57 layar satu per satu lewat Figma MCP.** Itu bisa
memakan berjam-jam dan hasilnya rapuh. Pakai import:

### Persiapan sekali saja

1. **Login di ekstensi html.to.design.** Klik ikon ekstensinya, lalu tombol **Login** di
   pojok kanan atas panel. Selama belum login, hasil capture tidak akan muncul di plugin Figma.
2. **Izinkan akses berkas lokal.** Buka `chrome://extensions` → cari html.to.design →
   aktifkan **"Allow access to file URLs"**. Tanpa ini, ekstensi tidak bisa membaca halaman
   yang dibuka lewat `file://`.

### Atur panel ekstensi sebelum capture

Ini bagian yang paling menentukan hasilnya:

| Setelan | Isi | Kenapa |
|---|---|---|
| **Viewports** | **Hapus centang `Browser (1920px)`**, centang **`1440 px`** | Semua layar UI kit dirancang pada lebar 1440. Kalau dibiarkan di 1920, frame hasil import punya ruang kosong di kiri-kanan dan lebarnya tidak sesuai `03-inventaris-layar.md`. |
| **Themes** | Biarkan **`Browser theme`** saja | Aplikasi ini dikunci light-style, tidak punya dark mode. Jangan centang Dark — nanti terbentuk frame ganda yang tidak ada padanannya di sistem. |

### Capture

1. Ekstrak `ui-kit.zip` ke folder lokal.
2. Buka `ui-kit/screens/02-admin.html` dengan **klik dua kali** (browser terbuka di `file://`).
3. **Tunggu halaman selesai ter-render** — sidebar dan navbar dirakit JavaScript, jadi
   pastikan sudah tampil utuh sebelum lanjut. Kalau sidebar-nya masih kosong, muat ulang halaman.
4. Klik ikon ekstensi → tombol biru **`Capture Current Page`** (atau `Alt+Shift+E`).
   Satu berkas berisi beberapa layar sekaligus, dan semuanya ikut ter-capture.
5. Ulangi langkah 2–4 untuk `03-ketua.html` sampai `10-wali-siswa.html` (9 berkas). Setiap
   capture tersimpan sebagai berkas `.h2d` — ini **memang alur normalnya**, bukan langkah
   darurat. Berkas `.h2d` itu yang kamu import ke Figma pada langkah berikutnya.

> **Tombol `Capture Selection` (`Alt+Shift+D`)** dipakai kalau kamu ingin mengambil
> **satu layar saja**, bukan seluruh isi berkas. Berguna untuk mengulang satu frame yang
> gagal tanpa meng-import ulang semuanya: klik tombol itu, lalu pilih elemen `<div class="screen">`
> milik layar yang bersangkutan.

### Import `.h2d` ke Figma — pastikan file tujuannya benar

**Sebelum membuka plugin di Figma, pastikan file "SIPADUHOK — Prototype" (atau file yang
berisi landing page-mu) yang sedang terbuka dan aktif.** Hasil import akan masuk ke file
yang aktif saat itu, bukan ditentukan oleh asal berkas `.h2d`-nya. Kalau ternyata salah masuk
ke file lain, tidak fatal — frame Figma bisa disalin-tempel antar file kapan saja.

1. Buka file Figma tujuan, lalu jalankan plugin **html.to.design**.
2. Buka daftar hasil capture (`.h2d`) yang tadi kamu buat, pilih yang mau diimpor.
3. Sebelum klik **Proceed**, atur dialog **Import options**:

| Opsi | Setelan | Alasan |
|---|---|---|
| **Use Autolayout** | ❌ **Off** untuk batch import ini | Kalau ada elemen tanpa lebar tetap, Auto Layout bisa membuat frame "hug" dan lebarnya bergeser dari 1440px yang kita perlukan. Auto Layout ditambahkan manual belakangan, khusus pada komponen Sidebar/Navbar (Batch 2), pakai ukuran pasti dari `02-inventaris-komponen.md`. |
| **Create styles & variables** | ❌ Off | Akan membuat style baru bernama generik (`Color 1`, dst) yang bentrok dengan style bernama (`brand/primary`, dst) dari Batch 1. |
| **Use existing local styles** | ✅ **On** — *dengan syarat Batch 1 sudah dikerjakan lebih dulu* | Menautkan fill dan teks hasil import ke color/text style yang sudah kamu buat di Batch 1, bukan nilai lepas. Kalau Batch 1 belum dikerjakan, opsi ini tidak berpengaruh — kerjakan Batch 1 dulu baru import layar. |
| **For hover effects (slower)** | ❌ Off | Tidak dibutuhkan untuk rekonstruksi statis ini, dan memperlambat proses. |
| **High-res images (slower)** *(PRO)* | ❌ Off | 57 layar setelah login nyaris tidak ada foto besar — cukup logo kecil. Fitur ini lebih relevan untuk halaman publik, yang sudah tidak diimpor ulang. |
| **Add hyperlinks** | ❌ Off | Membuat link prototype otomatis dari atribut `href`, padahal kebanyakan tombol di UI kit tidak berisi navigasi nyata (`href="#"`). Wiring 6 alur dilakukan manual di **Batch 4** sesuai `05-alur-prototype.md`, supaya tidak ada link acak. |
| **HTML layer names** | ✅ **On** | Nama layer mengikuti nama class HTML (`sidebar`, `stat-widget`, `card`, dst), bukan `Frame 123`/`Rectangle 45`. Batch 3 nanti butuh Claude mengenali elemen dari namanya untuk mengganti sidebar/navbar jadi komponen — jauh lebih sulit kalau namanya generik. |

4. Klik **Proceed**, lalu pindahkan hasilnya ke halaman **Layar — Dashboard** (atau
   **Layar — LMS** untuk layar bershell LMS).

> **Urutan yang benar: Batch 1 dulu, baru import layar.** Kalau kamu sudah kadung
> mengimpor sebelum Batch 1 selesai, tidak apa — nanti di Batch 3 tinggal minta Claude
> mengganti fill lepas ke style yang sudah ada. Cuma jadi pekerjaan tambahan, bukan
> mengulang dari nol.

> **Lewati `01-publik.html`.** Desain landing page sudah selesai dibuat sebelumnya dan sudah
> ada di workspace Figma-mu, jadi tidak perlu di-import ulang. Yang direkonstruksi di sini
> adalah **57 layar setelah login**.
>
> Pengecualian: kalau halaman **Login** dan **Pemulihan Akun** ternyata belum ada di
> workspace (keduanya sering luput karena bukan bagian template landing), import
> `01-publik.html` lalu hapus frame *Publik / 01 Beranda* dan *Publik / 02 PPDB*.

> ⚠️ **Harus mode ekstensi browser, bukan "import from URL".**
> Sidebar dan navbar dirakit oleh JavaScript agar 11 varian sidebar tidak perlu disalin ulang
> di puluhan berkas. Mode "import from URL" mengambil HTML mentah sebelum JavaScript berjalan,
> sehingga sidebar-nya akan kosong. Mode ekstensi membaca DOM setelah halaman ter-render,
> jadi hasilnya utuh. Untuk berkas lokal `file://`, mode ekstensi memang satu-satunya pilihan.

Setelah semua ter-import, tempel **Batch 3** agar Claude merapikan penamaan frame,
menghubungkan style, dan mengganti sidebar/navbar dengan komponen.

---

## Langkah 5 — Screenshot pembanding (opsional, tapi berguna)

Kalau kamu ingin membandingkan hasil Figma dengan aplikasi sungguhan:

```bash
npm install                       # playwright sudah ada di devDependencies
npx playwright install chromium
cp tools/ui-capture/akun.contoh.json tools/ui-capture/akun.local.json
# isi baseUrl, kredensial tiap role, dan ID pada "params"
npm run capture:ui
```

Hasilnya di `docs/figma/screenshots/<role>/`. Berguna untuk dua hal: memeriksa apakah UI kit
sudah mirip aslinya, dan sebagai lampiran screenshot di skripsi.
Detailnya di [`tools/ui-capture/README.md`](../../tools/ui-capture/README.md).

---

## Urutan kerja yang disarankan

```
1. Zip ui-kit                          →  2 menit
2. Pasang plugin + ekstensi Figma      → 10 menit  (sekali saja)
3. Upload 7 berkas + tempel Batch 0    →  2 menit
4. Batch 1 (foundations)               → ~10 menit
5. Batch 2 (komponen, prioritas 1)     → ~25 menit
6. Import 9 berkas HTML sendiri       → ~15 menit
7. Batch 3 (rapikan layar)             → ~30 menit
8. Batch 2 lanjutan (prioritas 2 & 3)  → ~20 menit
9. Batch 4 (wiring prototype)          → ~20 menit
10. Batch 5 (periksa akhir)            → ~10 menit
```

Total sekitar **2–3 jam**, sebagian besar menunggu. Bisa dicicil per batch.

---

## Checklist hasil akhir

Prototype dianggap selesai bila:

- [ ] File Figma punya 5 halaman: Foundations, Komponen, dan 3 halaman Layar.
- [ ] Semua color / text / effect style dari Batch 1 sudah ada dan **dipakai** oleh frame
      (bukan sekadar dibuat lalu frame-nya tetap memakai nilai lepas).
- [ ] 11 varian sidebar ada sebagai Component Variant.
- [ ] 57 frame setelah login bernama sesuai `03-inventaris-layar.md`.
- [ ] 6 Flow bisa dijalankan di Present mode dari awal sampai akhir tanpa dead end.
- [ ] Halaman publik yang sudah ada di workspace tidak terduplikasi oleh frame baru.
- [ ] Tidak ada foto siswa nyata di manapun — avatar semuanya inisial huruf.

---

## Yang perlu kamu jaga sendiri

**Jangan masukkan data pribadi siswa ke Figma.** UI kit ini seluruhnya memakai data dummy
berbahasa Indonesia. Kalau nanti kamu mengganti isinya dengan data asli dari database,
ingat bahwa file Figma bisa dibagikan dan diindeks — nama, NISN, dan nominal tagihan siswa
sungguhan sebaiknya tidak ikut ke sana, termasuk pada lampiran skripsi.

Hal yang sama berlaku untuk screenshot dari Langkah 5: folder `docs/figma/screenshots/`
sudah masuk `.gitignore`, tetapi tetap periksa isinya sebelum melampirkannya.
