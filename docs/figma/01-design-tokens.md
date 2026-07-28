# 01 — Design Token SIPADUHOK

> Diekstrak langsung dari kode produksi. Setiap nilai di sini **ada di CSS yang berjalan**,
> bukan hasil karangan. Sumber utama: `resources/css/layouts/sneat.css`,
> `resources/css/layouts/lms.css`, `public/js/tailwind.config.js`,
> `resources/css/dashboard/admin.css`.
>
> Versi mesin: [`tokens.json`](tokens.json) (format W3C Design Tokens).

---

## ⚠️ Hal pertama yang harus dipahami: ada TIGA sistem desain, bukan satu

Ini bukan inkonsistensi yang perlu "dirapikan" — ini kenyataan arsitektur aplikasi, dan
prototype harus mencerminkannya. Di Figma, buat **tiga halaman/page terpisah** dengan
**tiga set style terpisah**.

| Sistem | Layout | Font | Primary | Cakupan |
|---|---|---|---|---|
| **Dashboard (Sneat)** | `resources/views/layouts/sneat.blade.php` | Public Sans | `#4361ee` | 236 halaman, 9 role |
| **LMS** | `layouts/lms.blade.php`, `lms-guru`, `lms-ujian`, `lms-latihan` | Segoe UI (ujian/latihan: Inter) | `#165fac` | 44 halaman, guru + siswa |
| **Landing publik** | tanpa layout bersama, `<head>` per halaman | Poppins | `#165fac` | 20 halaman |

Catatan penting: `tailwind.config.js` di root repo adalah **stub kosong** (`content: []`) —
bukan sumber kebenaran. Tema Tailwind yang asli ada di `public/js/tailwind.config.js`,
dimuat sebagai script biasa oleh halaman landing lewat CDN.

---

## A. Sistem Dashboard (utama)

### A.1 Warna — penamaan Figma yang disarankan

Buat sebagai **Figma Variables** collection `dashboard`, mode tunggal (aplikasi light-style saja,
tidak ada dark mode).

#### Brand
| Nama style Figma | Hex | Dipakai untuk |
|---|---|---|
| `brand/primary` | `#4361ee` | Tombol utama, link, ikon aktif, `--bs-primary` |
| `brand/primary-hover` | `#3651d4` | State hover tombol |
| `brand/primary-dark` | `#2b4162` | Ujung bawah gradient sidebar |
| `brand/primary-light` | `#eff6ff` | Background `.bg-label-primary`, chip |

#### Semantik
| Nama style Figma | Hex | Soft/background | Dipakai untuk |
|---|---|---|---|
| `semantic/success` | `#10b981` | `#ecfdf5` | Lunas, hadir, disetujui |
| `semantic/warning` | `#f59e0b` | `#fffbeb` | Cicilan, izin, menunggu |
| `semantic/danger` | `#ef4444` | `#fee2e2` | Terlambat, alpa, ditolak |
| `semantic/info` | `#06b6d4` | — | Informasi |
| `semantic/purple` | `#8b5cf6` | `#f5f3ff` | Kartu kelas aktif wali kelas |
| `semantic/blue` | `#3b82f6` | `#eff6ff` | Statistik alternatif |
| `semantic/teal` | `#14b8a6` | — | Halaman aktif pada pagination |

#### Netral
| Nama style Figma | Hex | Dipakai untuk |
|---|---|---|
| `neutral/surface` | `#ffffff` | Kartu, modal, navbar |
| `neutral/background` | `#f8fafc` | Latar halaman |
| `neutral/border` | `#e2e8f0` | Garis kartu, tabel, divider |
| `neutral/text-strong` | `#1e293b` | Angka besar, judul kuat |
| `neutral/text-main` | `#334155` | Teks isi |
| `neutral/text-heading` | `#344054` | Judul halaman di navbar |
| `neutral/secondary` | `#64748b` | Teks sekunder, ikon |
| `neutral/text-muted` | `#94a3b8` | Label kecil, caption |
| `neutral/soft` | `#f1f5f9` | Latar netral, hover baris tabel |

### A.2 Gradient — tanda tangan visual sistem ini

Semua gradient di aplikasi memakai sudut **135°**, kecuali sidebar yang **180°**.
Kalau prototype kehilangan gradient-nya, tampilannya akan terasa "bukan aplikasi ini".

| Nama | Nilai | Lokasi |
|---|---|---|
| `gradient/sidebar` | `linear-gradient(180deg, #4361ee 0%, #2b4162 100%)` | `.layout-menu` — `sneat.css:239` |
| `gradient/blue` | `linear-gradient(135deg, #3b82f6, #2563eb)` | Kartu statistik |
| `gradient/green` | `linear-gradient(135deg, #10b981, #059669)` | Kartu statistik |
| `gradient/purple` | `linear-gradient(135deg, #8b5cf6, #7c3aed)` | Kartu kelas aktif — `sneat.css:91` |
| `gradient/orange` | `linear-gradient(135deg, #f59e0b, #f97316)` | Kartu statistik |
| `gradient/primary-cyan` | `linear-gradient(135deg, #4361ee, #06b6d4)` | Header/banner |
| `gradient/soft-bg` | `linear-gradient(135deg, #f0f4ff, #e8f0fe)` | Latar section lembut |

### A.3 Tipografi

**Font: Public Sans** (Google Fonts, dimuat di `sneat.blade.php:24` dengan weight 300–700 + italic).
Di Figma, aktifkan Public Sans lebih dulu — kalau tidak tersedia, fallback ke Inter akan
mengubah lebar teks dan merusak layout.

Base body Sneat: `15px` (`0.9375rem`), weight `400`, line-height `1.53`.

| Nama text style Figma | Size | Weight | Warna | Dipakai untuk |
|---|---|---|---|---|
| `heading/page-title` | 20px | 600 | `#344054` | Judul halaman di navbar (`sneat.css:166`) |
| `heading/page-subtitle` | 13px | 400 | `#6c757d` | Sub-judul di navbar (`sneat.css:174`) |
| `heading/card-title` | 17px | 600 | `#334155` | `.card-title-clean` (`dashboard/admin.css:26`) |
| `display/stat-value` | 28px | 700 | `#334155` | Angka besar statistik (`.stat-value`) |
| `label/stat-label` | 14px | 500 | `#94a3b8` | UPPERCASE, letter-spacing `0.5px` |
| `body/default` | 15px | 400 | `#334155` | Teks isi |
| `body/small` | 13px | 400 | `#64748b` | Teks pendukung |
| `label/menu-header` | 12px | 600 | `rgba(255,255,255,.6)` | Judul grup di sidebar, letter-spacing `0.5px` |
| `label/menu-item` | 15px | 400/600 | `rgba(255,255,255,.8)` / `#fff` | Item sidebar (600 saat aktif) |
| `label/badge` | 12px | 600 | kontekstual | Badge status |
| `label/table-head` | 13px | 600 | `#64748b` | Header tabel |

**Skala ukuran yang benar-benar dipakai:** 10, 11, 12, 13, 14, 15, 16, 17, 18, 20, 24, 28 px.
**Bobot:** 500, 600 (dominan), 700, 800. Weight `400` hampir tidak pernah dipakai di luar body —
jangan buat text style tipis-tipis yang tidak ada di aplikasi.

### A.4 Radius

`4` · `6` · `8` · `10` · `12` · `16` · `999` (pill) · `50%` (lingkaran)

Aturan praktis yang konsisten di seluruh aplikasi:
- **12px** → kartu (default, paling sering)
- **10px** → wrapper ikon, kartu kecil, quick link
- **8px** → tombol, input, badge kotak
- **6px** → chip kecil, tab
- **999px** → badge status pill, FAB
- **14px** → modal (`#logoutModal`, `sneat.css:657`)

### A.5 Shadow

| Nama effect style | Nilai | Dipakai untuk |
|---|---|---|
| `shadow/flat` | `0 1px 3px rgba(0,0,0,.02)` | `.dashboard-card` — kondisi normal |
| `shadow/card` | `0 10px 24px rgba(15,23,42,.05)` | Kartu elevasi |
| `shadow/card-hover` | `0 4px 6px rgba(0,0,0,.04)` | Hover kartu dashboard |
| `shadow/navbar` | `0 2px 6px rgba(0,0,0,.08)` | Navbar sticky |
| `shadow/overlay` | `0 8px 16px rgba(15,23,42,.18)` | Dropdown, popover |
| `shadow/modal` | `0 4px 20px rgba(0,0,0,.08)` | Modal |
| `shadow/focus-ring` | `0 0 0 3px rgba(67,97,238,.12)` | Input fokus |

### A.6 Spacing & ukuran tetap

Grid dasar 4px, tapi **14px dan 18px juga sering muncul** — jangan dipaksa ke kelipatan 4.

Urutan gap paling sering: `12` > `8` > `10` > `6` > `14` > `16`.
Padding kartu umum: `1.5rem` (24px); header kartu `1.25rem 1.5rem` (20px 24px).

| Elemen | Ukuran |
|---|---|
| Lebar sidebar | 260px |
| Padding horizontal konten | 24px (`.container-xxl`) |
| Padding vertikal konten | 26px (`1.625rem`) |
| Avatar navbar | 40px |
| Wrapper ikon statistik | 48×48, radius 10 |
| Tombol scroll-to-top (FAB) | 48×48, radius 999 |

### A.7 Motion

- Hover kartu dashboard: `translateY(-2px)`, transisi `0.2s ease`.
- Hover kartu Bootstrap generik: `translateY(-5px)` + `0 .5rem 1rem rgba(0,0,0,.15)`.
- Sidebar mobile slide: `transform 0.3s ease`.
- Alert masuk: `slideDown 0.3s ease-out` (dari `translateY(-20px)`, opacity 0).

---

## B. Sistem LMS

| Token | Nilai |
|---|---|
| `lms/primary` | `#165fac` |
| `lms/primary-dark` | `#0d3f7a` |
| `lms/accent-yellow` | `#f59e0b` |
| `lms/accent-orange` | `#ea580c` |
| `lms/background` | `#f8f9fa` |
| `lms/text` | `#333333` |
| Gradient sidebar | `linear-gradient(135deg, #165fac, #0d3f7a)` |
| Lebar sidebar | 280px (mobile 260px) |
| Font | Segoe UI stack — **kecuali** `lms-ujian` & `lms-latihan` yang memuat **Inter** |
| Ikon | Bootstrap Icons (hanya di LMS), selain FontAwesome |

`lms-ujian` dan `lms-latihan` adalah layout **fullscreen tanpa sidebar** (anti-distraksi).
Di Figma ini frame terpisah dengan shell sendiri.

---

## C. Sistem Landing publik

| Token | Nilai |
|---|---|
| `landing/primary` | `#165fac` |
| `landing/secondary` | `#287f3b` |
| `landing/accent-orange` | `#d45930` |
| `landing/accent-yellow` | `#fac030` |
| `landing/accent-bright` | `#ffe400` |
| `landing/cream` | `#e8e7e2` |
| Hero overlay | `linear-gradient(135deg, rgba(22,95,172,.9), rgba(40,127,59,.8))` |
| Font | **Poppins** 300–800 |
| Hover kartu | `translateY(-5px)` + `0 20px 40px rgba(0,0,0,.1)` |

---

## D. Palet Chart.js

`#4361ee` · `#3b82f6` · `#8b5cf6` · `#ec4899`
Track `#e2e8f0` · Sumbu `#64748b` · Label `#334155` · Area fill `rgba(67,97,238,.1)`

Sumber: `resources/js/dashboard/admin.js`.

---

## E. Ikon

| Set | Pemakaian | Di mana |
|---|---|---|
| **FontAwesome 6 Free** | ~4.484 pemakaian — **dominan** | Semua sistem |
| Boxicons | ~142 | Sisa dependensi Sneat, dashboard |
| Bootstrap Icons | ~89 | Hanya LMS |

Di Figma, cukup pasang **FontAwesome 6 Free** (plugin atau SVG). Dua set lain hanya muncul
di sedikit tempat dan bisa disubstitusi ikon FA yang setara.

---

## F. Yang perlu diwaspadai saat memindahkan ke Figma

1. **Ada dua palet semantik yang hidup bersamaan.** Sneat bawaan memakai
   success `#71dd37`, warning `#ffab00`, danger `#ff3e1d`; sedangkan CSS scoped buatan sendiri
   memakai `#10b981` / `#f59e0b` / `#ef4444`. **Pakai yang kedua** — itu yang menang di hampir
   semua halaman karena CSS scoped dimuat belakangan.
2. **Tidak ada dark mode.** `<html class="light-style">` dikunci. Jangan buat mode gelap di Figma.
3. **Tidak ada DataTables.** Semua tabel ditulis tangan, jadi jangan meniru gaya DataTables
   (tombol paging bawaan, kotak search kanan-atas). Lihat komponen tabel di `02-inventaris-komponen.md`.
4. **Pagination memakai teal `#14b8a6`, bukan primary.** Ini anomali nyata di
   `sneat.css:296-310` — pertahankan supaya prototype jujur.
5. Radius **12px untuk kartu** adalah keputusan paling terlihat di seluruh aplikasi.
   Kalau satu nilai saja harus benar, itu nilainya.
