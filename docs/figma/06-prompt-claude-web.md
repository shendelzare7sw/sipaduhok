# 06 — Prompt untuk Claude Web (terhubung Figma)

> Tempel prompt di bawah **berurutan**, satu batch per pesan. Jangan digabung — Figma MCP
> bekerja lebih andal untuk tugas kecil yang jelas batasnya, dan kalau ada yang gagal kamu
> tahu persis batch mana yang perlu diulang.
>
> Sebelum Batch 1, upload berkas yang disebut di
> [`00-PANDUAN-KIRIM-KE-CLAUDE-WEB.md`](00-PANDUAN-KIRIM-KE-CLAUDE-WEB.md).

---

## Batch 0 — Konteks awal (kirim sekali di awal percakapan)

```
Aku sedang merekonstruksi prototype Figma untuk SIPADUHOK, sistem informasi PKBM
(sekolah kesetaraan) berbasis Laravel yang sudah selesai dibangun. Prototype aslinya
hilang, jadi aku merekonstruksinya dari kode untuk bab perancangan skripsi.

Desain HALAMAN PUBLIK (landing page dan PPDB) sudah selesai dibuat sebelumnya dan sudah
ada di workspace Figma ini. Yang perlu direkonstruksi sekarang adalah 57 LAYAR SETELAH
LOGIN untuk 9 peran pengguna. Jangan buat ulang halaman publik.

Berkas yang aku lampirkan berisi hasil ekstraksi langsung dari kode produksi:
- tokens.json + 01-design-tokens.md  → warna, tipografi, radius, shadow, spacing
- 02-inventaris-komponen.md          → spesifikasi komponen dan variannya
- 03-inventaris-layar.md             → daftar frame beserta route dan berkas blade-nya
- 04-navigasi-sidebar-per-role.md    → pohon menu 11 varian sidebar
- 05-alur-prototype.md               → 6 alur untuk wiring prototype
- ui-kit.zip                         → HTML statis semua layar + komponen

Tiga hal yang harus kamu pegang:

1. Ada TIGA sistem desain berbeda dalam satu aplikasi, dan itu bukan kesalahan:
   - Dashboard (Sneat): font Public Sans, primary #4361EE — 236 halaman, 9 role
   - LMS: font Segoe UI (mode ujian: Inter), primary #165FAC — 44 halaman
   - Landing publik: font Poppins, primary #165FAC — 20 halaman
   Ketiganya harus jadi tiga halaman Figma dengan tiga set style terpisah.

2. Aplikasi ini TIDAK punya dark mode (<html class="light-style"> dikunci).
   Jangan buat mode gelap.

3. Kalau ada nilai yang terlihat "salah" — misalnya pagination memakai teal #14B8A6
   padahal primary-nya biru — itu memang begitu di kode. Pertahankan, jangan diperbaiki.
   Prototype ini harus jujur menggambarkan sistem yang sudah berjalan.

Jangan mulai membuat apa pun dulu. Konfirmasi kamu sudah membaca lampirannya,
lalu tunggu instruksi batch berikutnya.
```

---

## Batch 1 — Foundations (color, text, effect style)

```
Buat file Figma baru bernama "SIPADUHOK — Prototype" dengan 5 halaman:
  1. Foundations
  2. Komponen
  3. Layar — Dashboard
  4. Layar — LMS
  5. Layar — Publik

Halaman "Layar — Publik" hanya untuk menampung halaman publik yang SUDAH ADA di workspace
ini; jangan membuat frame baru di sana.

Di halaman "Foundations", buat style berikut dari tokens.json (pakai nama persis ini):

COLOR STYLES — Dashboard
  brand/primary #4361EE · brand/primary-hover #3651D4 · brand/primary-dark #2B4162
  brand/primary-light #EFF6FF
  semantic/success #10B981 · semantic/warning #F59E0B · semantic/danger #EF4444
  semantic/info #06B6D4 · semantic/purple #8B5CF6 · semantic/blue #3B82F6
  semantic/teal #14B8A6
  semantic/success-soft #ECFDF5 · semantic/warning-soft #FFFBEB
  semantic/danger-soft #FEE2E2 · semantic/purple-soft #F5F3FF
  neutral/surface #FFFFFF · neutral/background #F8FAFC · neutral/soft #F1F5F9
  neutral/border #E2E8F0 · neutral/text-muted #94A3B8 · neutral/secondary #64748B
  neutral/text-heading #344054 · neutral/text-main #334155 · neutral/text-strong #1E293B

COLOR STYLES — LMS
  lms/primary #165FAC · lms/primary-dark #0D3F7A · lms/accent-orange #EA580C

COLOR STYLES — Landing
  landing/primary #165FAC · landing/secondary #287F3B · landing/accent-orange #D45930
  landing/accent-yellow #FAC030 · landing/cream #E8E7E2

GRADIENT (buat sebagai fill style)
  gradient/sidebar        linear 180°  #4361EE → #2B4162
  gradient/sidebar-lms    linear 135°  #165FAC → #0D3F7A
  gradient/purple         linear 135°  #8B5CF6 → #7C3AED
  gradient/blue           linear 135°  #3B82F6 → #2563EB
  gradient/green          linear 135°  #10B981 → #059669
  gradient/orange         linear 135°  #F59E0B → #F97316
  gradient/hero-overlay   linear 135°  rgba(22,95,172,.9) → rgba(40,127,59,.8)

TEXT STYLES (font Public Sans — aktifkan dulu kalau belum tersedia)
  heading/page-title      20px / 600 / #344054 / line-height 1.3
  heading/page-subtitle   13px / 400 / #6C757D
  heading/card-title      17px / 600 / #334155
  display/stat-value      28px / 700 / #334155 / line-height 1.2
  label/stat-label        14px / 500 / #94A3B8 / UPPERCASE / letter-spacing 0.5
  body/default            15px / 400 / #334155 / line-height 1.53
  body/small              13px / 400 / #64748B
  label/table-head        13px / 600 / #64748B / UPPERCASE / letter-spacing 0.3
  label/menu-header       12px / 600 / rgba(255,255,255,.6) / UPPERCASE / letter-spacing 0.5
  label/menu-item         15px / 400 / rgba(255,255,255,.8)
  label/badge             12px / 600

EFFECT STYLES
  shadow/flat        0 1px 3px rgba(0,0,0,.02)
  shadow/card        0 10px 24px rgba(15,23,42,.05)
  shadow/card-hover  0 4px 6px rgba(0,0,0,.04)
  shadow/navbar      0 2px 6px rgba(0,0,0,.08)
  shadow/overlay     0 8px 16px rgba(15,23,42,.18)
  shadow/modal       0 4px 20px rgba(0,0,0,.08)
  shadow/focus-ring  0 0 0 3px rgba(67,97,238,.12)

Lalu buat papan dokumentasi visual di halaman Foundations: kotak swatch warna berlabel
nama style + hex, contoh tiap text style, dan kartu contoh tiap shadow.

Laporkan style apa saja yang berhasil dibuat sebelum lanjut.
```

---

## Batch 2 — Komponen

```
Di halaman "Komponen", buat Component + Variant berikut. Spesifikasi lengkap ada di
02-inventaris-komponen.md yang sudah aku lampirkan — pakai ukuran dan warna persis dari sana.

Prioritas 1 (wajib, dipakai di hampir semua frame):
  1. Sidebar        — 11 variant. Isi menu tiap variant ada di 04-navigasi-sidebar-per-role.md.
                      Lebar 260px (280px untuk variant LMS), fill gradient/sidebar.
                      State item: normal / hover / active (active = bg rgba(255,255,255,.15) + weight 600).
  2. Navbar         — 2 variant (dengan dan tanpa sub-judul). Avatar memakai INISIAL HURUF
                      dalam lingkaran #4361EE, bukan foto.
  3. Stat Widget    — 6 variant warna × 2 ukuran. Ikon 48×48 radius 10.
  4. Badge          — 7 variant warna × 2 bentuk (pill radius 999 / kotak radius 6).
  5. Button         — 6 variant gaya × 3 ukuran.
  6. Card           — 3 variant (flat / dengan header / dengan footer). Radius 12.

Prioritas 2:
  7. Form Field     — 5 tipe × 4 state (normal, focus, error, disabled)
  8. Data Table     — header + baris + toolbar + pagination
                      (pagination halaman aktif memakai teal #14B8A6 — jangan diubah ke primary)
  9. Alert          — 4 variant
 10. Modal          — radius 14 (berbeda dari kartu yang 12)
 11. File Card      — 3 variant tipe berkas
 12. Empty State

Prioritas 3:
 13. Question Card  — 5 tipe soal
 14. Stepper        — 3 state langkah
 15. Progress Bar   — 3 variant warna
 16. Schedule Grid
 17. Print Sheet    — A4 794px

Kerjakan Prioritas 1 dulu, laporkan hasilnya, baru lanjut ke Prioritas 2 dan 3.
Semua komponen harus memakai color/text/effect style dari Batch 1, bukan nilai hardcoded.
```

---

## Batch 3 — Layar (lewat import HTML)

> **Jangan minta Claude web menggambar 57 layar satu per satu.** Itu lambat dan hasilnya
> rapuh. Gunakan plugin `html.to.design` untuk import, lalu minta Claude merapikannya.

**Langkah yang kamu kerjakan sendiri (langkah lengkap ada di
[`00-PANDUAN`](00-PANDUAN-KIRIM-KE-CLAUDE-WEB.md#langkah-4--import-57-layar-setelah-login-kamu-yang-kerjakan-bukan-claude)):**

1. Pasang plugin **html.to.design** di Figma + **ekstensi browser**-nya, lalu **Login di
   ekstensi**. Aktifkan juga *Allow access to file URLs* di `chrome://extensions`.
2. Di panel ekstensi: **hapus centang `Browser (1920px)`, centang `1440 px`**.
   Themes cukup `Browser theme` — aplikasi ini tidak punya dark mode.
3. Ekstrak `ui-kit.zip`, buka `ui-kit/screens/02-admin.html` di browser, tunggu sampai
   sidebar tampil utuh, lalu klik **`Capture Current Page`** (`Alt+Shift+E`).
4. Ulangi untuk `03-ketua.html` … `10-wali-siswa.html` (9 berkas).
5. Di Figma, jalankan plugin html.to.design → pilih hasil capture → import ke halaman
   **Layar — Dashboard**. Layar bershell LMS (Guru / 03–07 dan Siswa / 03–06) dipindahkan
   ke halaman **Layar — LMS**.

> **Lewati `01-publik.html`** — desain halaman publik sudah ada di workspace.
> Kalau halaman Login dan Pemulihan Akun ternyata belum ada, import berkas itu lalu
> hapus frame *Publik / 01 Beranda* dan *Publik / 02 PPDB*.

> ⚠️ **Harus lewat ekstensi browser**, bukan fitur "import from URL" di plugin. Shell
> sidebar+navbar dirakit oleh JavaScript, jadi hanya ekstensi (yang membaca DOM setelah
> halaman ter-render) yang menangkapnya utuh. Untuk berkas lokal `file://`, ekstensi memang
> satu-satunya pilihan.

**Lalu tempel prompt ini ke Claude web:**

```
Aku sudah meng-import 57 layar setelah login dari ui-kit ke halaman Layar — Dashboard dan
Layar — LMS memakai html.to.design. Halaman publik tidak ikut karena desainnya sudah ada.
Sekarang tolong rapikan:

1. PENAMAAN FRAME
   Tiap layar di HTML punya label kuning di atasnya (class "screen-label") berisi nama
   frame, contoh: "Admin / 01 Dashboard". Ubah nama frame Figma-nya sesuai label itu,
   lalu HAPUS elemen label kuning dan blok header hitam (class "kit-header") — keduanya
   hanya alat bantu, bukan bagian desain.

2. UKURAN FRAME
   Set semua frame layar ke lebar 1440px. Tinggi menyesuaikan isi.

3. STYLE
   Ganti fill dan efek yang nilainya cocok dengan color/effect style dari Batch 1
   supaya terhubung ke style, bukan nilai lepas. Kalau ada warna yang tidak cocok dengan
   style manapun, laporkan padaku — jangan diam-diam dibulatkan ke warna terdekat.

4. KOMPONEN
   Ganti instance sidebar dan navbar hasil import dengan komponen dari Batch 2, sesuaikan
   variantnya per role. Lakukan bertahap per role dan laporkan progresnya.

Kerjakan halaman "Layar — Dashboard" dulu, laporkan, baru lanjut ke halaman Layar — LMS.
```

---

## Batch 4 — Wiring prototype

```
Sekarang pasang link prototype. Detail tiap alur ada di 05-alur-prototype.md yang aku
lampirkan — nama frame di sana persis sama dengan nama frame di Figma.

Buat 6 Flow, masing-masing dengan Flow Starting Point dan nama berikut:

  Flow 1 — Login & Routing Role         (10 frame)
  Flow 2 — Pembayaran Midtrans          (7 frame)
  Flow 3 — Tugas: Buat, Kumpul, Koreksi (7 frame)
  Flow 4 — Rapor Lintas Role            (9 frame)
  Flow 5 — Input Siswa Baru             (4 frame)
  Flow 6 — Presensi & Izin              (5 frame)

Aturan:
- Trigger: On Click. Animasi: Instant (atau Smart Animate 200ms untuk perpindahan
  dalam satu halaman). Aplikasi aslinya berpindah halaman secara instan, jadi jangan
  pakai animasi yang mencolok.
- Untuk perpindahan antar aktor (misalnya Wali Kelas → Ketua PKBM), sisipkan frame antara
  berukuran 1440px berisi teks besar: "Ganti aktor: masuk sebagai Ketua PKBM".
  Ini supaya penguji prototype tidak bingung kenapa tampilannya tiba-tiba berubah.
- Titik interaksi diambil dari tombol yang memang ada di layar (misalnya "Kirim ke Ketua",
  "Bayar Terpilih"), bukan area kosong.

Setelah selesai, buka Present mode dan jalankan Flow 4 (Rapor Lintas Role) dari awal
sampai akhir, lalu laporkan kalau ada rantai yang putus atau frame yang tidak tersambung.
```

---

## Batch 5 — Pemeriksaan akhir (opsional tapi disarankan)

```
Tolong periksa hasil akhirnya dan laporkan temuan, jangan langsung diperbaiki:

1. Apakah ada frame yang belum memakai color style / text style (masih nilai lepas)?
2. Apakah ada teks yang bukan Public Sans / Segoe UI / Poppins sesuai sistem desainnya?
3. Apakah ke-57 frame sudah bernama sesuai 03-inventaris-layar.md? Sebutkan yang belum.
4. Apakah semua Flow bisa dijalankan sampai frame terakhir tanpa dead end?
5. Adakah komponen yang instance-nya masih detached?

Berikan daftarnya, lalu tunggu instruksiku sebelum memperbaiki apa pun.
```

---

## Kalau ada yang gagal

| Masalah | Penyebab paling mungkin | Tindakan |
|---|---|---|
| Hasil capture tidak muncul di plugin Figma | Ekstensi browser belum di-login | Klik ikon ekstensi → tombol **Login** di pojok kanan atas, lalu capture ulang |
| Ekstensi tidak bereaksi saat membuka berkas `file://` | *Allow access to file URLs* belum aktif | `chrome://extensions` → html.to.design → aktifkan opsi itu |
| Frame jadi lebar 1920px dengan ruang kosong kiri-kanan | Viewport masih di `Browser (1920px)` | Hapus centangnya, centang **`1440 px`**, capture ulang |
| Muncul dua set frame (terang &amp; gelap) | Theme `Dark` ikut tercentang | Sisakan `Browser theme` saja — aplikasi ini tidak punya dark mode |
| Sidebar tidak ikut ter-import (frame kosong di kiri) | Memakai "import from URL", atau capture dilakukan sebelum halaman selesai render | Muat ulang halaman, tunggu sidebar tampil, lalu **Capture Current Page** |
| Font ter-render sebagai Inter/Roboto | Public Sans belum aktif di Figma | Aktifkan Public Sans, lalu import ulang |
| Gambar hero/galeri jadi kotak kosong | Folder `assets/img` tidak ikut saat ekstrak zip | Ekstrak ulang seluruh isi `ui-kit.zip` |
| Ikon jadi kotak kosong | FontAwesome CDN diblokir | Buka HTML dengan koneksi internet aktif sebelum import |
| Claude web bilang tidak bisa akses Figma | Koneksi Figma belum diberi izin | Sambungkan ulang Figma di pengaturan Claude |
