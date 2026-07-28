# 03 — Inventaris Layar Prototype (57 layar setelah login + 4 publik opsional)

> Versi mesin: [`daftar-layar.json`](daftar-layar.json) — dipakai langsung oleh
> `tools/ui-capture/capture.mjs`. Semua URI diambil dari `php artisan route:list`,
> bukan tebakan.

## Prinsip pemilihan

Aplikasi punya **451 file blade**. Merekonstruksi semuanya tidak masuk akal dan juga tidak
perlu — prototype skripsi butuh **representasi**, bukan salinan. Yang dipilih:

- **Semua 9 role terwakili**, karena setiap role punya sidebar dan hak akses berbeda.
- **Setiap pola layout muncul minimal sekali**: dashboard statistik, tabel + filter,
  form input, halaman detail, halaman cetak A4, dan layout ujian fullscreen.
- **Setiap flow di `05-alur-prototype.md` lengkap layarnya** — tidak ada rantai yang putus.

Yang **sengaja dikeluarkan**, dan alasannya:

| Dikeluarkan | Jumlah | Alasan |
|---|---|---|
| Template cetak/export (`print-*`, `cetak-*`, `exports/`) | ~90 | Layout A4 tanpa navigasi, hampir identik satu sama lain. Diwakili 2 frame (preview rapor, invoice). |
| Modul `waka/*` yang mengulang `admin/*` | ~32 | Struktur identik, hanya sidebar yang beda. Di Figma cukup ganti varian sidebar. |
| Halaman `create`/`edit`/`show` turunan | ~120 | Satu form sudah mewakili pola form. |
| `vendor/pagination/` | 9 | Bawaan Laravel. |

---

## Konvensi penamaan frame di Figma

```
<Role> / <NN> <Nama Layar>
```
Contoh: `Bendahara / 03 Detail Tagihan Siswa`.

Nomor dua digit menjaga urutan di panel Layers sesuai alur kerja role, bukan alfabet.
**Gunakan nama ini persis** — `05-alur-prototype.md` merujuk frame dengan nama ini saat
memasang link prototype.

---

## Daftar layar

Kolom `Shell` menentukan kerangka mana yang dipakai: `sneat` (dashboard), `lms`/`lms-guru`
(LMS), `lms-ujian` (fullscreen), `landing` (publik), `auth`, `print` (A4).

### Publik & Autentikasi (4) — ⚠️ OPSIONAL, desainnya sudah ada

> **Landing page sudah selesai dirancang sebelumnya** dan sudah ada di workspace Figma.
> Keempat frame di bawah **tidak perlu di-import ulang** — cukup lewati berkas
> `ui-kit/screens/01-publik.html` saat import. Frame-frame ini tetap disertakan sebagai
> pembanding visual saja, kalau sewaktu-waktu perlu memeriksa konsistensi token warna
> antara halaman publik dan halaman setelah login.
>
> **Cakupan yang benar-benar perlu direkonstruksi adalah 57 layar setelah login** (Admin
> sampai Wali Siswa di bawah).

| Frame | Route | URI | Blade | Shell |
|---|---|---|---|---|
| Publik / 01 Beranda | `home` | `/` | `resources/views/home.blade.php` | landing |
| Publik / 02 PPDB | `ppdb` | `ppdb` | `resources/views/ppdb.blade.php` | landing |
| Publik / 03 Login | `login` | `login` | `resources/views/auth/login.blade.php` | auth |
| Publik / 04 Pemulihan Akun | `user.recovery` | `recovery` | `resources/views/auth/user-recovery.blade.php` | auth |

Kalau ternyata halaman **Login** dan **Pemulihan Akun** belum ada di workspace-mu (keduanya
sering luput karena bukan bagian dari template landing), import dua frame itu saja dari
`01-publik.html` lalu hapus frame Beranda dan PPDB.

### Admin (8)

| Frame | Route | URI | Blade |
|---|---|---|---|
| Admin / 01 Dashboard | `admin.dashboard` | `admin/dashboard` | `dashboard/admin.blade.php` |
| Admin / 02 Data Siswa | `admin.users.siswa` | `admin/users/siswa` | `admin/users/siswa.blade.php` |
| Admin / 03 Tambah Siswa | `admin.users.create-siswa` | `admin/users/siswa/create` | `admin/users/siswa-create.blade.php` |
| Admin / 04 Data Kelas | `admin.kelas.index` | `admin/kelas` | `admin/kelas/index.blade.php` |
| Admin / 05 Jadwal Pelajaran | `admin.jadwal-pelajaran.index` | `admin/jadwal-pelajaran` | `admin/jadwal-pelajaran/index.blade.php` |
| Admin / 06 Tagihan | `admin.keuangan.tagihan.index` | `admin/keuangan/tagihan` | `admin/keuangan/tagihan/index.blade.php` |
| Admin / 07 Laporan | `admin.laporan.index` | `admin/laporan` | `admin/laporan/index.blade.php` |
| Admin / 08 CMS Landing Page | `admin.landing-pages.index` | `admin/landing-pages` | `admin/landing-pages/index.blade.php` |

### Ketua PKBM (6)

| Frame | Route | URI | Blade |
|---|---|---|---|
| Ketua / 01 Dashboard | `ketua.dashboard` | `ketua/dashboard` | `dashboard/ketua.blade.php` |
| Ketua / 02 Validasi Rapor | `ketua.validasi-rapor.index` | `ketua/validasi-rapor` | `ketua/validasi-rapor/index.blade.php` |
| Ketua / 03 Preview Rapor | `ketua.validasi-rapor.preview` | `ketua/validasi-rapor/{siswa}/preview` | `wali-kelas/rapor/preview-pas.blade.php` ⚠️ |
| Ketua / 04 Dispensasi Keuangan | `ketua.dispensasi.index` | `ketua/dispensasi` | `ketua/dispensasi/index.blade.php` |
| Ketua / 05 Approval Kenaikan Kelas | `ketua.kenaikan-kelas.approval.index` | `ketua/kenaikan-kelas/approval` | `ketua/promotion/approval.blade.php` |
| Ketua / 06 Cetak Laporan | `ketua.laporan.index` | `ketua/laporan` | `ketua/laporan/index.blade.php` |

⚠️ Ketua **memakai ulang view milik wali kelas** untuk preview rapor
(`ValidasiRaporController.php:222-224`). Di Figma cukup satu frame yang dipakai dua flow.

### Wakil Kepala Sekolah (5)

| Frame | Route | URI | Blade |
|---|---|---|---|
| Waka / 01 Dashboard | `waka.dashboard` | `waka/dashboard` | `waka/dashboard.blade.php` |
| Waka / 02 Data Kelas | `waka.kelas.index` | `waka/kelas` | `waka/kelas/index.blade.php` |
| Waka / 03 Jadwal Pelajaran | `waka.jadwal-pelajaran.index` | `waka/jadwal-pelajaran` | `waka/jadwal-pelajaran/index.blade.php` |
| Waka / 04 Pengaturan KKM | `waka.kenaikan-kelas.kkm.index` | `waka/kenaikan-kelas/kkm` | `waka/akademik/promotion/kkm.blade.php` |
| Waka / 05 Monitoring Siswa | `waka.monitoring.siswa` | `waka/monitoring/siswa` | `waka/monitoring/siswa.blade.php` |

### Sekretaris (4)

| Frame | Route | URI | Blade |
|---|---|---|---|
| Sekretaris / 01 Dashboard | `sekretaris.dashboard` | `sekretaris/dashboard` | `dashboard/sekretaris.blade.php` |
| Sekretaris / 02 Kelola Berita | `sekretaris.berita.index` | `sekretaris/berita` | `sekretaris/berita/index.blade.php` |
| Sekretaris / 03 Form Berita | `sekretaris.berita.create` | `sekretaris/berita/create` | `sekretaris/berita/form.blade.php` |
| Sekretaris / 04 Kalender Akademik | `sekretaris.kalender.index` | `sekretaris/kalender` | `sekretaris/kalender/index.blade.php` |

### Bendahara (7)

| Frame | Route | URI | Blade |
|---|---|---|---|
| Bendahara / 01 Dashboard | `bendahara.dashboard` | `bendahara/dashboard` | `dashboard/bendahara.blade.php` |
| Bendahara / 02 Kelola Tagihan | `bendahara.tagihan.index` | `bendahara/tagihan` | `bendahara/tagihan/index.blade.php` |
| Bendahara / 03 Detail Tagihan Siswa | `bendahara.tagihan.show` | `bendahara/tagihan/{siswa}` | `bendahara/tagihan/show.blade.php` |
| Bendahara / 04 Kelola Pembayaran | `bendahara.pembayaran.index` | `bendahara/pembayaran` | `bendahara/pembayaran/index.blade.php` |
| Bendahara / 05 Validasi Pembayaran | `bendahara.pembayaran.show` | `bendahara/pembayaran/{pembayaran}` | `bendahara/pembayaran/show.blade.php` |
| Bendahara / 06 Laporan Pembayaran | `bendahara.laporan.index` | `bendahara/laporan` | `bendahara/laporan/index.blade.php` |
| Bendahara / 07 Siswa Belum Lunas | `bendahara.laporan.belum-lunas` | `bendahara/laporan/belum-lunas` | `bendahara/laporan/belum-lunas.blade.php` |

### Wali Kelas (8)

| Frame | Route | URI | Blade |
|---|---|---|---|
| Wali Kelas / 01 Pilih Kelas | `wali.pilih-kelas` | `wali/pilih-kelas` | `wali-kelas/pilih-kelas/index.blade.php` |
| Wali Kelas / 02 Dashboard | `wali.dashboard` | `wali/dashboard` | `wali-kelas/dashboard.blade.php` |
| Wali Kelas / 03 Input Presensi | `wali.presensi.index` | `wali/presensi` | `wali-kelas/presensi/index.blade.php` |
| Wali Kelas / 04 Edit Nilai | `wali.nilai.edit` | `wali/nilai/{siswa}/edit` | `wali-kelas/nilai/edit.blade.php` |
| Wali Kelas / 05 Kelola Rapor | `wali.rapor.index` | `wali/rapor` | `wali-kelas/rapor/index.blade.php` |
| Wali Kelas / 06 Edit Rapor | `wali.rapor.edit` | `wali/rapor/{rapor}/edit` | `wali-kelas/rapor/edit.blade.php` |
| Wali Kelas / 07 Preview Rapor | `wali.rapor.preview` | `wali/rapor/{rapor}/preview` | `wali-kelas/rapor/preview-pas.blade.php` |
| Wali Kelas / 08 Permintaan Unduh | `wali.rapor.request-download.index` | `wali/rapor/request-download` | `wali-kelas/rapor/request-download.blade.php` |

**Pilih Kelas harus jadi frame pertama.** Semua halaman wali kelas bergantung pada kelas aktif
yang tersimpan di session — tanpa langkah ini, flow-nya tidak jujur menggambarkan sistem.

### Guru Pengajar (7)

| Frame | Route | URI | Shell |
|---|---|---|---|
| Guru / 01 Dashboard SIA | `guru.dashboard` | `guru/dashboard` | sneat |
| Guru / 02 Semua Kelas | `guru.kelas.index` | `guru/kelas` | sneat |
| Guru / 03 Dashboard LMS | `guru.lms.dashboard` | `guru/lms/{kelas}/{mapel}/dashboard` | **lms-guru** |
| Guru / 04 Materi | `guru.lms.materi.index` | `guru/lms/{kelas}/{mapel}/materi` | lms-guru |
| Guru / 05 Buat Tugas | `guru.lms.tugas.create` | `guru/lms/{kelas}/{mapel}/tugas/create` | lms-guru |
| Guru / 06 Koreksi Tugas | `guru.lms.tugas.koreksi` | `guru/lms/{kelas}/{mapel}/tugas/{tugas}/koreksi` | lms-guru |
| Guru / 07 Kelola Soal Ujian | `guru.lms.ujian.soal.manage` | `guru/lms/{kelas}/{mapel}/ujian/{ujian}/manage-soal` | lms-guru |

Frame 02 → 03 adalah **titik pergantian shell** (Sneat → LMS). Ini transisi visual paling
mencolok di seluruh aplikasi dan wajib terlihat di prototype.

### Siswa (6)

| Frame | Route | URI | Shell |
|---|---|---|---|
| Siswa / 01 Dashboard SIA | `siswa.sia.dashboard` | `siswa/sia/dashboard` | sneat |
| Siswa / 02 Presensi | `siswa.sia.presensi.index` | `siswa/sia/presensi` | sneat |
| Siswa / 03 Dashboard LMS | `siswa.lms.dashboard` | `siswa/lms/dashboard` | **lms** |
| Siswa / 04 Halaman Mapel | `siswa.lms.mapel.show` | `siswa/lms/mata-pelajaran/{mapelId}` | lms |
| Siswa / 05 Kerjakan Tugas | `siswa.lms.mapel.tugas.show` | `siswa/lms/mata-pelajaran/{mapelId}/tugas/{tugasId}` | lms |
| Siswa / 06 Ujian Fullscreen | `siswa.lms.mapel.ujian.show` | `siswa/lms/mata-pelajaran/{mapelId}/ujian/{ujianId}` | **lms-ujian** |

### Wali Siswa (6)

| Frame | Route | URI | Shell |
|---|---|---|---|
| Wali Siswa / 01 Dashboard | `wali-siswa.dashboard` | `wali-siswa/dashboard` | sneat |
| Wali Siswa / 02 Tagihan Anak | `wali-siswa.tagihan.anak` | `wali-siswa/tagihan/anak/{siswa}` | sneat |
| Wali Siswa / 03 Pembayaran Midtrans | `wali-siswa.pembayaran.snap` | `wali-siswa/pembayaran/snap/{pembayaran}` | sneat |
| Wali Siswa / 04 Invoice | `wali-siswa.pembayaran.invoice` | `wali-siswa/pembayaran/{pembayaran}/invoice` | print |
| Wali Siswa / 05 Detail Rapor | `wali-siswa.rapor.detail` | `wali-siswa/rapor/detail/{rapor}` | sneat |
| Wali Siswa / 06 Ajukan Izin | `wali-siswa.presensi.ajukan-izin` | `wali-siswa/presensi/anak/{siswa}/ajukan-izin` | sneat |

---

## Daftar slot gambar

Semua gambar landing page **sudah tersedia** di repo dan dipakai apa adanya di UI kit
(`docs/figma/ui-kit/assets/img/`). Tidak ada kotak kosong di frame publik.

| Frame | Slot | Gambar yang dipakai |
|---|---|---|
| Publik / 01 Beranda | Hero | `hero-img.jpg` (`home.blade.php:98`) |
| Publik / 01 Beranda | Tentang | `about-img.jpg` (`home.blade.php:307`) |
| Publik / 01 Beranda | Galeri | `gallery-1..6.jpg` (`home.blade.php:438`) |
| Publik / 02 PPDB | Header | `bg-ppdb.jpg` |
| Semua frame dashboard | Logo sidebar | `logo.png` |
| Semua frame dashboard | Avatar navbar | **tanpa gambar** — inisial huruf di lingkaran `#4361ee` |

**Slot yang perlu kamu isi sendiri:** tidak ada saat ini. Kalau nanti ingin mengganti foto,
frame-nya diberi nama `PLACEHOLDER — ganti gambar` di Figma sehingga bisa dicari lewat
panel Layers, dan rasio aspeknya sudah benar sehingga layout tidak bergeser.

**Yang sengaja tidak dipakai:** isi `public/storage/` (`profile-photos`, `pembayaran`,
`materi`, `tugas`, `forum-attachments`) karena berisi unggahan pengguna nyata. Bukti transfer
dan lampiran dirender sebagai kartu file (ikon + nama + ukuran), sesuai tampilan aslinya.

---

## Koreksi dokumen lama yang ditemukan saat verifikasi

Diverifikasi terhadap `routes/web.php` dan `php artisan route:list`:

1. **`docs/flow/orang-tua.md` usang** — menyebut prefix `/orang-tua` dan route `orang-tua.`,
   sedangkan implementasi nyata adalah `/wali-siswa` dan `wali-siswa.`. Sudah diperbaiki.
2. **Pembayaran bukan milik siswa.** Route `siswa.sia.pembayaran.*` masih ada
   (`siswa/sia/pembayaran`) tetapi **tidak ter-link dari sidebar manapun** — ada komentar
   eksplisit di `siswa/partials/sneat-sidebar-sia.blade.php:61` bahwa menu Rapor & Pembayaran
   dipindahkan ke Wali Siswa. Jangan dipakai sebagai basis flow pembayaran.
3. **Route kenaikan kelas waka** adalah `waka.kenaikan-kelas.*`, bukan `waka.akademik.promotion.*`.
4. **Nama route kelola soal** adalah `guru.lms.ujian.soal.manage` (bukan `manage_soal`),
   meski nama view-nya `manage_soal.blade.php`.
5. **Monitoring waka tidak punya sub-item "Pengguna"** — hanya admin dan ketua yang punya.
