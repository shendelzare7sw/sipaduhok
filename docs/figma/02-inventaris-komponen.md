# 02 — Inventaris Komponen

> Versi visual: [`ui-kit/01-komponen.html`](ui-kit/01-komponen.html) — buka di browser untuk
> melihat seluruh state dan variant sekaligus.
>
> Dokumen ini adalah spesifikasi tertulisnya: nama komponen, daftar variant, ukuran, dan
> lokasi sumbernya di kode. Pakai penamaan di sini saat membuat Component di Figma.

---

## Ringkasan komponen

| # | Komponen Figma | Variant | Dipakai di |
|---|---|---|---|
| 1 | `Sidebar` | 11 (per role/shell) | Semua layar dashboard & LMS |
| 2 | `Navbar` | 2 (dengan/tanpa sub-judul) | Semua layar Sneat |
| 3 | `Card` | 3 (flat, dengan header, dengan footer) | Di mana-mana |
| 4 | `Stat Widget` | 6 warna × 2 ukuran | Semua dashboard |
| 5 | `Badge` | 7 warna × 2 bentuk (pill/kotak) | Semua tabel |
| 6 | `Button` | 6 gaya × 3 ukuran | Di mana-mana |
| 7 | `Form Field` | 5 tipe × 4 state | Semua form |
| 8 | `Data Table` | 1 + toolbar + pagination | Semua daftar |
| 9 | `Alert` | 4 (success/danger/warning/info) | Flash message |
| 10 | `Empty State` | 1 | Tabel kosong |
| 11 | `Modal` | 1 | Konfirmasi hapus/logout |
| 12 | `File Card` | 3 (PDF/gambar/dokumen) | Bukti bayar, lampiran |
| 13 | `Question Card` | 5 tipe soal | LMS ujian & latihan |
| 14 | `Stepper` | 4 langkah × 3 state | PPDB |
| 15 | `Progress Bar` | 3 warna | Ketuntasan, kehadiran |
| 16 | `Schedule Grid` | 1 | Jadwal pelajaran |
| 17 | `Print Sheet` | 1 (A4) | Rapor, invoice, laporan |

---

## 1. Sidebar — komponen terpenting

**11 variant.** Detail pohon menu tiap variant ada di
[`04-navigasi-sidebar-per-role.md`](04-navigasi-sidebar-per-role.md).

| Properti | Nilai |
|---|---|
| Lebar | 260px (Sneat) / 280px (LMS) |
| Latar | `linear-gradient(180deg, #4361ee, #2b4162)` — LMS: `135deg, #165fac → #0d3f7a` |
| Brand | logo max-height 40–50px + teks 16/700 uppercase putih |
| Item | padding `9px 20px`, ikon 24px + gap 12px, teks 15px |
| Item aktif | latar `rgba(255,255,255,.15)`, teks putih, **weight 600** |
| Item hover | latar `rgba(255,255,255,.10)`, teks putih |
| Grup header | 12/600 uppercase `rgba(255,255,255,.6)`, letter-spacing `.5px`, padding `12px 24px` |
| Submenu | indentasi kiri 56px, teks 14px |
| Badge notifikasi | pill `#ef4444`, teks putih 11/600, rata kanan |

**Sub-elemen khusus:**

- **`Sidebar / Kartu Kelas Aktif`** — hanya wali kelas dengan >1 kelas.
  Gradient ungu `135deg, #8b5cf6 → #7c3aed`, radius 8, margin `8px 20px 8px 12px`.
  Label 11.5/700 uppercase, judul 14/600, meta 12px, tombol full-width `rgba(255,255,255,.2)`.
  Sumber: `resources/css/layouts/sneat.css:87-132`.
- **`Sidebar / Blok Konteks Mengajar`** — hanya guru shell LMS.
  Label + nama mapel 16/700 + nama kelas 13px, latar `rgba(255,255,255,.12)`, radius 10.
- **Sub-item berikon** — hanya wali kelas dan wali siswa. Role lain sub-itemnya polos.

---

## 2. Navbar detached

Tinggi ±62px, latar putih, border bawah `#e7e7e7`, shadow `0 2px 6px rgba(0,0,0,.08)`, sticky.

- Kiri: judul halaman `20/600 #344054`, sub-judul `13/400 #6c757d` (opsional → 2 variant).
- Kanan: lonceng notifikasi + badge angka merah, lalu avatar 40px.
- **Avatar memakai inisial huruf** dalam lingkaran `#4361ee` — ini fallback asli aplikasi
  ketika `foto_profil` kosong (`resources/views/layouts/sneat.blade.php:105-112`).
  Dipakai di prototype agar tidak memasukkan foto siswa nyata.

---

## 3–4. Card & Stat Widget

**Card:** latar putih, border `1px #e2e8f0`, radius **12px**, shadow `0 1px 3px rgba(0,0,0,.02)`.
Header: padding `20px 24px`, border bawah, judul 17/600 dengan ikon primary.
Body: padding 24px. Hover: `translateY(-2px)` + shadow `0 4px 6px rgba(0,0,0,.04)`.

**Stat Widget** (`resources/css/dashboard/admin.css:40-109`):

```
┌──────────────────────────────────────┐  padding 24px, gap 20px
│ ┌────┐  1.248                        │  ikon 48×48, radius 10
│ │ 👥 │  TOTAL SISWA                  │  angka 28/700 #334155
│ └────┘                               │  label 14/500 uppercase #94a3b8, ls .5px
│ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─  │  footer: border-top dashed, margin-top 16
│ 3 cabang              ↑ 4%           │  13px #64748b
└──────────────────────────────────────┘
```

Variant warna ikon (warna + latar): primary `#4361ee` / `rgba(67,97,238,.08)` ·
blue `#3b82f6` / `#eff6ff` · green `#10b981` / `#ecfdf5` · yellow `#f59e0b` / `#fffbeb` ·
purple `#8b5cf6` / `#f5f3ff` · red `#ef4444` / `#fee2e2`.
Variant ukuran: normal (48px ikon, 28px angka) dan compact (42px ikon, 22px angka).

---

## 5. Badge status — dua kelompok terpenting

Pill radius 999, padding `4px 11px`, teks 12/600, ikon opsional 12px.
Variant kotak memakai radius 6px (dipakai untuk label metode pembayaran dan kategori).

**Status tagihan** — mengikuti enum kolom `tagihan.status`:

| Label | Warna teks | Latar | Nilai enum |
|---|---|---|---|
| Sudah Bayar | `#047857` | `#ecfdf5` | `sudah_bayar` |
| Cicilan | `#b45309` | `#fffbeb` | `cicilan` |
| Belum Bayar | `#64748b` | `#f1f5f9` | `belum_bayar` |
| Terlambat | `#b91c1c` | `#fee2e2` | `terlambat` |

**Status presensi:** Hadir (hijau) · Izin (kuning) · Sakit (info/cyan) · Alpa (merah).

**Status alur rapor:** Draft (netral) → Menunggu Validasi (kuning) → Perlu Revisi (merah)
→ Tervalidasi (primary) → Terbit (hijau). Lima status ini adalah tulang punggung flow rapor
di [`05-alur-prototype.md`](05-alur-prototype.md).

**Metode pembayaran** (bentuk kotak): Midtrans (primary) · Transfer Manual (netral) ·
Tunai (netral) · Dispensasi (ungu).

---

## 6. Button

Padding `9px 16px`, radius 8, teks 14/600, gap ikon 7px.
Variant gaya: `primary` (isi `#4361ee`) · `success` · `danger` · `warning` ·
`outline` (putih, border+teks primary) · `ghost` (putih, border `#e2e8f0`, teks `#64748b`).
Variant ukuran: `default` · `sm` (padding `6px 11px`, 13px) · `icon` (padding `6px 9px`).
Modifier: `block` (lebar penuh, konten di tengah).

---

## 7. Form Field

Label 14/600 `#334155` + tanda `*` merah untuk wajib, margin bawah 7px.
Kontrol: padding `10px 14px`, border `1px #e2e8f0`, radius 8, teks 14px.

| State | Tampilan |
|---|---|
| Normal | border `#e2e8f0` |
| Focus | border `#4361ee` + ring `0 0 0 3px rgba(67,97,238,.12)` |
| Error | pesan 12px `#ef4444` di bawah kontrol |
| Hint | teks bantuan 12px `#94a3b8` di bawah kontrol |

Tipe: `text` · `select` (dengan panah kustom) · `textarea` · `date/datetime` ·
`search` (ikon kaca pembesar di kiri, padding kiri 38px).

---

## 8. Data Table + toolbar + pagination

**Tabel ditulis tangan — aplikasi tidak memakai DataTables.** Jangan meniru elemen bawaan
DataTables (kotak pencarian kanan-atas, tombol paging default, dropdown "Show N entries").

- Header: latar `#f1f5f9`, teks 13/600 uppercase `#64748b`, letter-spacing `.3px`, padding `12px 24px`.
- Sel: padding `14px 24px`, border bawah `#e2e8f0`, baris terakhir tanpa border.
- Sel nama: avatar inisial 34px + nama 600 `#1e293b` + baris sekunder 12px `#94a3b8`.
- Kolom angka/uang: rata kanan, `font-variant-numeric: tabular-nums`, weight 600.
- Toolbar: padding `20px 24px`, border bawah, isi search + filter + spacer + tombol aksi.
- Pagination: gap 6px, tombol radius 8, padding `10px 16px`.
  **Halaman aktif memakai teal `#14b8a6`, bukan primary** — anomali nyata di `sneat.css:304-310`,
  dipertahankan supaya prototype jujur.

---

## 9–12. Alert, Empty State, Modal, File Card

**Alert** — padding `13px 18px`, radius 8, ikon + teks 14px, 4 variant:
success `#ecfdf5`/`#047857` · danger `#fee2e2`/`#b91c1c` · warning `#fffbeb`/`#b45309` ·
info `#ecfeff`/`#0e7490`. Animasi masuk `slideDown 0.3s` dari `translateY(-20px)`.

**Empty State** — padding vertikal 56px, ikon 40px opacity .35, teks 14px `#94a3b8`.

**Modal** — lebar 480px, radius **14px** (bukan 12), shadow `0 4px 20px rgba(0,0,0,.08)`.
Header padding `16px 20px` + border bawah `#e5e7eb`, body padding 20px,
footer padding `14px 20px` + border atas, tombol rata kanan gap 8px.
Sumber: `resources/css/layouts/sneat.css:655-732`.

**File Card** — pengganti unggahan pengguna nyata. Ikon 40×40 radius 8 berwarna sesuai tipe
(PDF merah, gambar biru, dokumen biru), nama berkas 14/600, ukuran + tanggal 12px muted.
Ini memang tampilan asli di aplikasi, jadi bukan kompromi visual.

---

## 13. Question Card — 5 tipe soal

Sesuai `resources/views/siswa/lms/partials/_soal_*.blade.php`:

1. **Pilihan Ganda** — 4 opsi, badge primary.
2. **Pilihan Ganda Kompleks** — checkbox banyak jawaban, badge ungu.
3. **Benar / Salah** — dua tombol, badge info.
4. **Isian Singkat** — satu input, badge kuning.
5. **Uraian** — textarea + penghitung karakter, badge netral, opsi penilaian AI.

Opsi jawaban: padding `14px 18px`, border 1px, radius 10, margin bawah 10px.
Terpilih: border `#4361ee` + latar `#eff6ff`, huruf opsi jadi lingkaran primary putih.

---

## 14–17. Stepper, Progress, Schedule Grid, Print Sheet

**Stepper (PPDB)** — lingkaran 38px, 3 state: `done` (hijau + centang), `active` (biru primary),
`pending` (abu `#e2e8f0`). Garis penghubung 2px `#e2e8f0`, label 13/600.

**Progress Bar** — tinggi 8px, radius pill, track `#e2e8f0`.
Variant isi: hijau (normal) · kuning (peringatan) · merah (kritis).

**Schedule Grid** — border `1px #e2e8f0` di semua sel, header latar `#f1f5f9` 12/600 uppercase.
Sel terisi: latar `#eff6ff` teks `#4361ee` 600. Baris istirahat: latar `#fffbeb` teks `#b45309`,
merentang seluruh kolom. Sel kosong: teks muted.

**Print Sheet (A4)** — lebar 794px (A4 @96dpi), padding `48px 56px`, font 13px, warna `#111827`.
- Kop: logo 66px + 3 baris identitas, border bawah `3px double #111827`.
- Judul: 15/700 rata tengah bergaris bawah.
- Tabel cetak: border `1px solid #111827` di semua sel, header latar `#f3f4f6` 700 rata tengah.
- Tanda tangan: 3 kolom (orang tua, wali kelas, ketua), jarak tanda tangan 66px, nama bergaris bawah.

Dipakai oleh: preview rapor (wali kelas & ketua) dan invoice pembayaran. Sekitar 90 berkas
blade lain memakai pola yang sama — cukup satu komponen di Figma.
