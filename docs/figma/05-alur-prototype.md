# 05 — Alur Prototype (Prototype Flows)

> Nama frame di dokumen ini **persis sama** dengan
> [`03-inventaris-layar.md`](03-inventaris-layar.md). Pakai nama itu saat memasang link
> prototype di Figma agar tidak ada rantai yang salah sambung.
>
> Semua alur di bawah **diverifikasi ke `routes/web.php` dan controller**, bukan disusun
> dari asumsi. Alur yang melibatkan lebih dari satu role diberi penanda aktor.

---

## Cara memasang di Figma

1. Buat **Flow Starting Point** pada frame awal tiap alur (panel Prototype → `+` di Flow).
2. Beri nama flow sesuai judul di bawah — nama ini muncul di Present mode dan bisa
   langsung dikutip di skripsi.
3. Trigger default: `On Click` · Animasi: `Instant` atau `Smart Animate` 200ms.
   Jangan pakai animasi berlebihan; aplikasinya sendiri berpindah halaman secara instan.
4. Untuk perpindahan antar aktor (mis. wali kelas → ketua), beri **Overlay atau frame antara**
   berlabel "Ganti aktor: login sebagai …" agar penguji prototype tidak bingung.

---

## Alur 1 — Login &amp; Routing Role

Menunjukkan bahwa satu halaman login mengarahkan ke sembilan dashboard berbeda.
Sumber: `app/Http/Controllers/Auth/LoginController.php:68-77`.

> Frame *Publik / 03 Login* kemungkinan **sudah ada di workspace Figma-mu**, karena desain
> halaman publik dibuat lebih dulu. Pakai frame yang sudah ada itu sebagai titik awal alur —
> jangan membuat duplikatnya.

```
Publik / 03 Login
   ├─ (admin)          → Admin / 01 Dashboard
   ├─ (ketua_pkbm)     → Ketua / 01 Dashboard
   ├─ (waka)           → Waka / 01 Dashboard
   ├─ (sekretaris)     → Sekretaris / 01 Dashboard
   ├─ (bendahara)      → Bendahara / 01 Dashboard
   ├─ (wali_kelas)     → Wali Kelas / 01 Pilih Kelas   ← bukan dashboard!
   ├─ (guru_pengajar)  → Guru / 01 Dashboard SIA
   ├─ (siswa)          → Siswa / 01 Dashboard SIA
   └─ (orang_tua)      → Wali Siswa / 01 Dashboard

Publik / 03 Login → "Lupa kata sandi?" → Publik / 04 Pemulihan Akun
```

> **Catat:** wali kelas yang memegang lebih dari satu kelas diarahkan ke *Pilih Kelas* lebih
> dulu, bukan langsung ke dashboard. Ini keputusan desain nyata dan sebaiknya terlihat di prototype.

---

## Alur 2 — Pembayaran Tagihan via Midtrans

**Aktor utama: Wali Siswa** (bukan siswa). Alur paling sering dipakai di aplikasi.

```
Wali Siswa / 01 Dashboard
  → klik kartu anak "Bayar"
Wali Siswa / 02 Tagihan Anak
  → centang tagihan → "Bayar Terpilih"
Wali Siswa / 03 Pembayaran Midtrans
  → pilih metode → "Lanjutkan ke Pembayaran"
     (di aplikasi: popup Snap.js Midtrans — di prototype cukup overlay sederhana)
  → berhasil
Wali Siswa / 04 Invoice
```

**Cabang back-office** (aktor: Bendahara) — untuk pembayaran transfer manual/tunai yang
**tidak** otomatis tervalidasi:

```
Bendahara / 01 Dashboard
  → "Pembayaran Menunggu Validasi"
Bendahara / 04 Kelola Pembayaran
  → klik baris
Bendahara / 05 Validasi Pembayaran
  → "Setujui Pembayaran"
Bendahara / 04 Kelola Pembayaran   (status berubah jadi Disetujui)
```

Route terkait: `wali-siswa.tagihan.anak` → `wali-siswa.pembayaran.snap` →
`wali-siswa.pembayaran.invoice`; webhook `midtrans.notification` berjalan asinkron di
`MidtransWebhookController`. Sisi bendahara: `bendahara.pembayaran.index` → `.show` → `.validasi`.

---

## Alur 3 — Tugas: Guru Buat → Siswa Kumpul → Guru Koreksi

**Tiga aktor bergantian.** Alur ini menunjukkan pergantian shell Sneat → LMS.

```
── AKTOR: GURU ──
Guru / 01 Dashboard SIA
  → "Semua Kelas"
Guru / 02 Semua Kelas
  → pilih Kelas 7A → Matematika      ← titik pergantian shell Sneat → LMS
Guru / 03 Dashboard LMS
  → menu "Tugas" → "Buat Tugas"
Guru / 05 Buat Tugas
  → "Terbitkan Tugas"
Guru / 03 Dashboard LMS

── GANTI AKTOR: SISWA ──
Siswa / 03 Dashboard LMS
  → kartu mapel "Matematika"
Siswa / 04 Halaman Mapel
  → tab "Tugas" → pilih tugas
Siswa / 05 Kerjakan Tugas
  → unggah berkas → "Kumpulkan Tugas"
Siswa / 04 Halaman Mapel               (status berubah jadi Dikumpulkan)

── GANTI AKTOR: GURU ──
Guru / 03 Dashboard LMS
  → "12 siswa mengumpulkan…"
Guru / 06 Koreksi Tugas
  → isi nilai + umpan balik → "Simpan Semua Nilai"
```

Varian paralel dengan struktur sama: **Ujian** (`guru.lms.ujian.*` ↔ `siswa.lms.mapel.ujian.*`)
dan **Latihan**. Untuk prototype cukup satu, lalu sebutkan di skripsi bahwa dua lainnya identik
strukturnya.

---

## Alur 4 — Rapor Lintas Role (alur terpanjang)

**Empat aktor.** Ini alur paling menarik untuk didemonstrasikan karena memperlihatkan
kontrol berjenjang: pengisi → pemvalidasi → penerbit → penerima.

```
── AKTOR: WALI KELAS ──
Wali Kelas / 01 Pilih Kelas
  → "Kelola Kelas Ini" (7A)
Wali Kelas / 02 Dashboard
  → "Nilai Siswa"
Wali Kelas / 04 Edit Nilai
  → isi nilai → "Simpan Nilai"
Wali Kelas / 05 Kelola Rapor
  → "Generate Semua Rapor"
Wali Kelas / 06 Edit Rapor
  → isi catatan & kehadiran → "Pratinjau"
Wali Kelas / 07 Preview Rapor
  → kembali → "Kirim ke Ketua"
Wali Kelas / 05 Kelola Rapor          (status: Menunggu Validasi)

── GANTI AKTOR: KETUA PKBM ──
Ketua / 01 Dashboard
  → antrean "Validasi Rapor — Kelas 7A"
Ketua / 02 Validasi Rapor
  → "Pratinjau"
Ketua / 03 Preview Rapor
  → kembali → "Validasi"            (atau "Minta Revisi" → balik ke Wali Kelas / 05)
Ketua / 02 Validasi Rapor             (status: Tervalidasi)

── GANTI AKTOR: WALI KELAS ──
Wali Kelas / 05 Kelola Rapor
  → "Terbitkan"                      (status: Terbit)

── GANTI AKTOR: WALI SISWA ──
Wali Siswa / 01 Dashboard
  → kartu anak "Rapor"
Wali Siswa / 05 Detail Rapor
  → "Ajukan Permintaan Unduh"

── GANTI AKTOR: WALI KELAS ──
Wali Kelas / 08 Permintaan Unduh
  → "Setujui"                        (tautan unduh sekali pakai, berlaku 24 jam)
```

**Gerbang keuangan:** rapor hanya dapat diakses bila status pembayaran memenuhi syarat —
diatur lewat `wali.validasi-akses.index` / `bendahara.validasi-akses.index`. Di prototype,
tunjukkan lewat badge "Tunggakan Rp 600.000" pada baris Siti Nurhaliza di
*Wali Kelas / 08 Permintaan Unduh*.

Route terkait: `wali.nilai.edit` → `wali.rapor.generate-all` → `wali.rapor.edit` →
`wali.rapor.kirim-validasi` → `ketua.validasi-rapor.validasi` → `wali.rapor.terbitkan` →
`wali-siswa.rapor.request-download` → `wali.rapor.request-download.approve` →
`wali-siswa.rapor.download`.

---

## Alur 5 — Input Siswa Baru

**Aktor: Admin.** Alur CRUD paling representatif.

```
Admin / 01 Dashboard
  → "Tambah Siswa" (Akses Cepat)
Admin / 02 Data Siswa
  → "Tambah Siswa"
Admin / 03 Tambah Siswa
  → isi identitas + akun + hubungkan wali → "Simpan Siswa"
Admin / 02 Data Siswa                  (baris baru muncul, status "Belum Berkelas")
  → baris siswa → penempatan kelas
Admin / 04 Data Kelas
  → "Siswa" pada kelas tujuan          (siswa masuk rombel)
```

**Jalur massal** sebagai cabang alternatif: *Admin / 02 Data Siswa* → "Impor Excel"
(unduh template → unggah berkas → pratinjau → simpan). Di prototype cukup satu overlay.

---

## Alur 6 — Presensi &amp; Izin

**Tiga aktor.** Alur pendek tapi lengkap — bagus untuk demonstrasi cepat.

```
── AKTOR: WALI SISWA ──
Wali Siswa / 01 Dashboard
  → kartu anak "Presensi" → "Ajukan Izin"
Wali Siswa / 06 Ajukan Izin
  → isi formulir → "Kirim Pengajuan"   (status: Menunggu)

── GANTI AKTOR: WALI KELAS ──
Wali Kelas / 02 Dashboard
  → "3 Izin Menunggu Validasi"
Wali Kelas / 03 Input Presensi
  → baris siswa otomatis bertanda "Diajukan wali" → pilih "Izin" → "Simpan Presensi"

── GANTI AKTOR: SISWA ──
Siswa / 01 Dashboard SIA
  → menu "Presensi"
Siswa / 02 Presensi                    (baris 13 Januari tercatat "Izin — disetujui")
```

---

## Ringkasan untuk skripsi

| Alur | Aktor | Frame | Menu sidebar yang dilewati |
|---|---|---|---|
| 1. Login &amp; routing role | 9 role | 10 | Dashboard tiap role |
| 2. Pembayaran Midtrans | Wali Siswa, Bendahara | 7 | Monitoring Anak → Tagihan · Keuangan → Kelola Pembayaran |
| 3. Tugas | Guru, Siswa | 7 | Informasi Akademik → Semua Kelas · Sidebar LMS → Tugas · Mata Pelajaran |
| 4. Rapor lintas role | Wali Kelas, Ketua, Wali Siswa | 9 | Kelola Rapor · Validasi Rapor · Permintaan Unduh · Monitoring Anak → Rapor |
| 5. Input siswa baru | Admin | 4 | Kelola Data Pengguna → Siswa · Data Kelas &amp; Penugasan |
| 6. Presensi &amp; izin | Wali Siswa, Wali Kelas, Siswa | 5 | Monitoring Anak → Presensi · Kelola Presensi → Input Harian · Akademik → Presensi |

Enam alur ini menyentuh **seluruh 9 role** dan **ketiga sistem desain** (dashboard, LMS,
landing/auth). Pemetaan lengkap layar ↔ menu sidebar ↔ berkas kode ada di
[`07-bab-perancangan-prototype.md`](07-bab-perancangan-prototype.md).
