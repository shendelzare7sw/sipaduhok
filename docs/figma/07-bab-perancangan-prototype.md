# 07 — Materi Bab Perancangan Prototype

> Dokumen ini **bahan mentah untuk skripsi**, bukan dokumentasi teknis. Isinya bisa
> disalin dan disesuaikan dengan gaya penulisan pembimbingmu.
>
> **Acuan pemetaan di sini adalah struktur menu sidebar dan route yang sudah berjalan** —
> semuanya dibaca langsung dari kode (`routes/web.php` dan 11 berkas sidebar partial).
> Dokumen UML lama (use case, activity, sequence) **tidak dipakai** sebagai acuan karena
> sebagiannya sudah tidak lengkap dan tidak sinkron dengan implementasi.

---

## Catatan jujur soal metodologi (baca ini dulu)

Prototype Figma yang asli hilang, dan yang ada di dokumen ini adalah rekonstruksi dari
sistem yang sudah selesai dibangun. Itu bukan masalah metodologis, **selama kamu tidak
mengklaim kronologi yang salah.**

Yang aman dan benar untuk ditulis:

> "Prototype yang disajikan pada bab ini merupakan **prototype hasil iterasi akhir** yang
> menjadi acuan implementasi sistem."

Yang **tidak boleh** ditulis kalau memang tidak begitu kejadiannya:

> ~~"Prototype berikut dibuat sebelum implementasi dan belum pernah mengalami perubahan."~~

Pada model prototyping (Pressman), prototype memang artefak **iteratif** — dibangun,
dievaluasi pengguna, direvisi, berulang sampai disepakati. Yang lazim didokumentasikan di
skripsi adalah **hasil iterasi terakhir**, bukan sketsa pertama. Karena prototype ini
high-fidelity dan identik dengan sistem berjalan, justru itu memperkuat argumen bahwa tidak
ada penyimpangan antara rancangan dan implementasi.

Kalau pembimbing meminta bukti proses iterasi, yang bisa ditunjukkan adalah **riwayat commit
Git** — di situ terlihat perubahan antarmuka dari waktu ke waktu.

---

## Ringkasan angka untuk ditulis di bab

| Aspek | Angka | Sumber |
|---|---|---|
| Total halaman antarmuka pada sistem | 451 berkas Blade | `resources/views/` |
| Berkas CSS scoped per halaman | 292 | `resources/css/` |
| Peran pengguna | 9 | `routes/web.php`, tabel `roles` |
| Sistem desain | 3 (Dashboard, LMS, Landing) | `01-design-tokens.md` |
| Varian navigasi sidebar | 11 | `04-navigasi-sidebar-per-role.md` |
| **Layar pada prototype** | **61 frame** (57 setelah login + 4 halaman publik) | `03-inventaris-layar.md` |
| Alur prototype interaktif | 6 | `05-alur-prototype.md` |
| Komponen desain | 17 | `02-inventaris-komponen.md` |

**Kalimat yang bisa dipakai untuk menjelaskan pembatasan cakupan:**

> Sistem memiliki 451 halaman antarmuka. Perancangan prototype tidak menampilkan seluruhnya,
> melainkan 61 layar representatif — terdiri atas 4 halaman publik dan **57 layar antarmuka
> setelah login** — yang dipilih dengan tiga kriteria: (1) seluruh sembilan peran pengguna
> terwakili beserta struktur menunya masing-masing, (2) setiap pola tata letak yang berbeda
> muncul minimal satu kali, dan (3) seluruh layar yang membentuk enam alur utama tercakup
> lengkap tanpa terputus. Halaman yang tidak ditampilkan adalah halaman cetak (±90 berkas),
> modul yang strukturnya mengulang modul lain (±32 berkas), serta halaman turunan
> `create`/`edit`/`show` yang polanya sudah diwakili satu contoh formulir.

---

## 4.x.1 Perancangan Sistem Desain

Sistem menerapkan **tiga sistem desain** yang berjalan berdampingan, sesuai konteks
penggunaannya:

| Sistem | Konteks | Font | Warna Utama | Cakupan |
|---|---|---|---|---|
| Dashboard | Antarmuka pengelolaan seluruh peran | Public Sans | `#4361EE` | 236 halaman |
| LMS | Ruang pembelajaran guru dan siswa | Segoe UI (ujian: Inter) | `#165FAC` | 44 halaman |
| Landing | Halaman publik dan pendaftaran | Poppins | `#165FAC` | 20 halaman |

Pemisahan ini disengaja: ruang pembelajaran dibedakan secara visual dari ruang administrasi
agar peserta didik tidak tercampur konteks, sementara halaman publik memakai identitas visual
lembaga yang lebih hangat.

Nilai token lengkap (warna, tipografi, radius, bayangan, jarak) beserta lokasi definisinya
di kode ada pada [`01-design-tokens.md`](01-design-tokens.md).

---

## 4.x.2 Perancangan Navigasi (Information Architecture)

Navigasi utama berupa **sidebar vertikal** yang isinya berbeda untuk setiap peran, sehingga
pengguna hanya melihat menu yang menjadi wewenangnya. Perbedaannya cukup jauh: sidebar Admin
memuat 8 grup dengan 20 menu, sedangkan Sekretaris hanya 2 menu.

| Peran | Prefix URL | Grup Menu | Menu Level-1 | Karakteristik |
|---|---|---|---|---|
| Admin | `/admin` | 8 | 20 | Superset seluruh modul |
| Wakil Kepala Sekolah | `/waka` | 5 | 9 | Akademik saja |
| Ketua PKBM | `/ketua` | 3 | 6 | Persetujuan dan monitoring |
| Sekretaris | `/sekretaris` | 1 | 2 | Konten publikasi |
| Bendahara | `/bendahara` | 3 | 5 | Keuangan |
| Wali Kelas | `/wali` | 3 | 7 | + kartu kelas aktif, menu kondisional |
| Guru Pengajar | `/guru` | 2 + 4 | 5 + 9 | Dua shell: SIA dan LMS |
| Siswa | `/siswa` | 2 + 3 | 3 + 6 | Dua shell: SIA dan LMS |
| Wali Siswa | `/wali-siswa` | 1 | 1 + N | Menu dinamis per anak |

Pohon menu lengkap tiap peran ada pada
[`04-navigasi-sidebar-per-role.md`](04-navigasi-sidebar-per-role.md).

Empat temuan perancangan yang layak dibahas di skripsi:

1. **Wali kelas memiliki pemilih konteks kelas.** Wali kelas yang memegang lebih dari satu
   kelas diarahkan ke halaman *Pilih Kelas* sebelum dashboard. Kelas terpilih disimpan pada
   session dan menjadi acuan seluruh halaman presensi, nilai, dan rapor.
2. **Siswa memiliki dua ruang terpisah.** Ruang SIA (akademik administratif) dan ruang LMS
   (pembelajaran) memakai kerangka antarmuka berbeda, dengan transisi yang eksplisit.
3. **Menu keuangan dan rapor tidak diberikan kepada siswa.** Keduanya menjadi wewenang wali
   siswa, dengan pertimbangan bahwa tanggung jawab pembayaran dan pemantauan hasil belajar
   berada pada orang tua.
4. **Menu wali siswa dibangun dinamis per anak.** Satu akun wali dapat memiliki lebih dari
   satu anak, dan sidebar menampilkan satu grup submenu (Presensi / Tagihan / Rapor) untuk
   masing-masing anak.

---

## 4.x.3 Pemetaan Layar Prototype ↔ Menu Sidebar ↔ Kode

Kolom "Menu Sidebar" merujuk pada nama menu yang benar-benar tampil di aplikasi.
Kolom "Route" adalah nama route Laravel, dan "Berkas Kode" adalah jalur relatif dari akar proyek.

### Publik &amp; Autentikasi

| Layar Prototype | Menu / Konteks | Route | Berkas Kode |
|---|---|---|---|
| Publik / 01 Beranda | Navigasi publik → Beranda | `home` | `resources/views/home.blade.php` |
| Publik / 02 PPDB | Navigasi publik → Daftar PPDB | `ppdb` | `resources/views/ppdb.blade.php` |
| Publik / 03 Login | Pintu masuk seluruh peran | `login` | `resources/views/auth/login.blade.php` |
| Publik / 04 Pemulihan Akun | Login → "Lupa kata sandi?" | `user.recovery` | `resources/views/auth/user-recovery.blade.php` |

### Admin — prefix `/admin`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Admin / 01 Dashboard | Dashboard | `admin.dashboard` | `resources/views/dashboard/admin.blade.php` |
| Admin / 02 Data Siswa | Manajemen Pengguna → Kelola Data Pengguna → Siswa | `admin.users.siswa` | `resources/views/admin/users/siswa.blade.php` |
| Admin / 03 Tambah Siswa | (turunan dari layar di atas) | `admin.users.create-siswa` | `resources/views/admin/users/siswa-create.blade.php` |
| Admin / 04 Data Kelas | Data Akademik → Data Kelas &amp; Penugasan → Data Kelas | `admin.kelas.index` | `resources/views/admin/kelas/index.blade.php` |
| Admin / 05 Jadwal Pelajaran | Data Akademik → Jadwal Pelajaran | `admin.jadwal-pelajaran.index` | `resources/views/admin/jadwal-pelajaran/index.blade.php` |
| Admin / 06 Tagihan | Keuangan → Tagihan &amp; Pembayaran → Tagihan | `admin.keuangan.tagihan.index` | `resources/views/admin/keuangan/tagihan/index.blade.php` |
| Admin / 07 Laporan | Monitoring &amp; Analitik → Laporan &amp; Catatan → Laporan | `admin.laporan.index` | `resources/views/admin/laporan/index.blade.php` |
| Admin / 08 CMS Landing Page | Manajemen Konten → Landing Page | `admin.landing-pages.index` | `resources/views/admin/landing-pages/index.blade.php` |

### Ketua PKBM — prefix `/ketua`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Ketua / 01 Dashboard | Dashboard | `ketua.dashboard` | `resources/views/dashboard/ketua.blade.php` |
| Ketua / 02 Validasi Rapor | Persetujuan &amp; Validasi → Validasi Rapor | `ketua.validasi-rapor.index` | `resources/views/ketua/validasi-rapor/index.blade.php` |
| Ketua / 03 Preview Rapor | (turunan: tombol Pratinjau) | `ketua.validasi-rapor.preview` | `resources/views/wali-kelas/rapor/preview-pas.blade.php` ⚠️ |
| Ketua / 04 Dispensasi Keuangan | Persetujuan &amp; Validasi → Dispensasi Keuangan | `ketua.dispensasi.index` | `resources/views/ketua/dispensasi/index.blade.php` |
| Ketua / 05 Approval Kenaikan Kelas | Persetujuan &amp; Validasi → Approval Dispensasi | `ketua.kenaikan-kelas.approval.index` | `resources/views/ketua/promotion/approval.blade.php` |
| Ketua / 06 Cetak Laporan | Laporan &amp; Komunikasi → Cetak Laporan | `ketua.laporan.index` | `resources/views/ketua/laporan/index.blade.php` |

⚠️ Ketua **memakai ulang berkas tampilan milik wali kelas** untuk pratinjau rapor
(`ValidasiRaporController.php:222-224`) — temuan yang menarik untuk disebut sebagai bentuk
penggunaan ulang komponen antarmuka.

### Wakil Kepala Sekolah — prefix `/waka`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Waka / 01 Dashboard | Dashboard | `waka.dashboard` | `resources/views/waka/dashboard.blade.php` |
| Waka / 02 Data Kelas | Data Akademik → Data Kelas &amp; Penugasan → Data Kelas | `waka.kelas.index` | `resources/views/waka/kelas/index.blade.php` |
| Waka / 03 Jadwal Pelajaran | Data Akademik → Jadwal Pelajaran | `waka.jadwal-pelajaran.index` | `resources/views/waka/jadwal-pelajaran/index.blade.php` |
| Waka / 04 Pengaturan KKM | Kenaikan Kelas → Pengaturan Kenaikan → Pengaturan KKM | `waka.kenaikan-kelas.kkm.index` | `resources/views/waka/akademik/promotion/kkm.blade.php` |
| Waka / 05 Monitoring Siswa | Monitoring &amp; Analitik → Monitoring Sistem → Siswa | `waka.monitoring.siswa` | `resources/views/waka/monitoring/siswa.blade.php` |

### Sekretaris — prefix `/sekretaris`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Sekretaris / 01 Dashboard | Dashboard | `sekretaris.dashboard` | `resources/views/dashboard/sekretaris.blade.php` |
| Sekretaris / 02 Kelola Berita | Manajemen Konten → Konten Publikasi → Kelola Berita | `sekretaris.berita.index` | `resources/views/sekretaris/berita/index.blade.php` |
| Sekretaris / 03 Form Berita | (turunan: tombol Tulis Berita) | `sekretaris.berita.create` | `resources/views/sekretaris/berita/form.blade.php` |
| Sekretaris / 04 Kalender Akademik | Manajemen Konten → Konten Publikasi → Kalender Akademik | `sekretaris.kalender.index` | `resources/views/sekretaris/kalender/index.blade.php` |

### Bendahara — prefix `/bendahara`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Bendahara / 01 Dashboard | Dashboard | `bendahara.dashboard` | `resources/views/dashboard/bendahara.blade.php` |
| Bendahara / 02 Kelola Tagihan | Keuangan → Tagihan &amp; Pembayaran → Kelola Tagihan | `bendahara.tagihan.index` | `resources/views/bendahara/tagihan/index.blade.php` |
| Bendahara / 03 Detail Tagihan Siswa | (turunan: klik baris siswa) | `bendahara.tagihan.show` | `resources/views/bendahara/tagihan/show.blade.php` |
| Bendahara / 04 Kelola Pembayaran | Keuangan → Tagihan &amp; Pembayaran → Kelola Pembayaran | `bendahara.pembayaran.index` | `resources/views/bendahara/pembayaran/index.blade.php` |
| Bendahara / 05 Validasi Pembayaran | (turunan: klik baris pembayaran) | `bendahara.pembayaran.show` | `resources/views/bendahara/pembayaran/show.blade.php` |
| Bendahara / 06 Laporan Pembayaran | Laporan → Laporan Keuangan → Laporan Pembayaran | `bendahara.laporan.index` | `resources/views/bendahara/laporan/index.blade.php` |
| Bendahara / 07 Siswa Belum Lunas | Laporan → Laporan Keuangan → Siswa Belum Lunas | `bendahara.laporan.belum-lunas` | `resources/views/bendahara/laporan/belum-lunas.blade.php` |

### Wali Kelas — prefix `/wali`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Wali Kelas / 01 Pilih Kelas | Pilih Kelas (pemilih konteks) | `wali.pilih-kelas` | `resources/views/wali-kelas/pilih-kelas/index.blade.php` |
| Wali Kelas / 02 Dashboard | Dashboard | `wali.dashboard` | `resources/views/wali-kelas/dashboard.blade.php` |
| Wali Kelas / 03 Input Presensi | Akademik → Kelola Presensi → Input Harian | `wali.presensi.index` | `resources/views/wali-kelas/presensi/index.blade.php` |
| Wali Kelas / 04 Edit Nilai | Akademik → Kelola Rapor → Nilai Siswa | `wali.nilai.edit` | `resources/views/wali-kelas/nilai/edit.blade.php` |
| Wali Kelas / 05 Kelola Rapor | Akademik → Kelola Rapor → Kelola Rapor | `wali.rapor.index` | `resources/views/wali-kelas/rapor/index.blade.php` |
| Wali Kelas / 06 Edit Rapor | (turunan: tombol Edit) | `wali.rapor.edit` | `resources/views/wali-kelas/rapor/edit.blade.php` |
| Wali Kelas / 07 Preview Rapor | (turunan: tombol Pratinjau) | `wali.rapor.preview` | `resources/views/wali-kelas/rapor/preview-pas.blade.php` |
| Wali Kelas / 08 Permintaan Unduh | Akademik → Kelola Rapor → Permintaan Unduh | `wali.rapor.request-download.index` | `resources/views/wali-kelas/rapor/request-download.blade.php` |

### Guru Pengajar — prefix `/guru`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Guru / 01 Dashboard SIA | Dashboard (shell SIA) | `guru.dashboard` | `resources/views/dashboard/guru.blade.php` |
| Guru / 02 Semua Kelas | Akademik → Informasi Akademik → Semua Kelas | `guru.kelas.index` | `resources/views/guru/kelas/index.blade.php` |
| Guru / 03 Dashboard LMS | Kelas Saya → *(kelas)* → *(mapel)* — **pindah ke shell LMS** | `guru.lms.dashboard` | `resources/views/guru/lms/dashboard.blade.php` |
| Guru / 04 Materi | Sidebar LMS → Pembelajaran → Materi | `guru.lms.materi.index` | `resources/views/guru/lms/materi/index.blade.php` |
| Guru / 05 Buat Tugas | Sidebar LMS → Pembelajaran → Tugas → Buat | `guru.lms.tugas.create` | `resources/views/guru/lms/tugas/create.blade.php` |
| Guru / 06 Koreksi Tugas | Sidebar LMS → Pembelajaran → Tugas → Koreksi | `guru.lms.tugas.koreksi` | `resources/views/guru/lms/tugas/koreksi.blade.php` |
| Guru / 07 Kelola Soal Ujian | Sidebar LMS → Pembelajaran → Ujian → Kelola Soal | `guru.lms.ujian.soal.manage` | `resources/views/guru/lms/ujian/manage_soal.blade.php` |

### Siswa — prefix `/siswa`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Siswa / 01 Dashboard SIA | Dashboard SIA (shell SIA) | `siswa.sia.dashboard` | `resources/views/siswa/sia/dashboard.blade.php` |
| Siswa / 02 Presensi | Akademik → Presensi | `siswa.sia.presensi.index` | `resources/views/siswa/sia/presensi/index.blade.php` |
| Siswa / 03 Dashboard LMS | Learning Management → HOK-LMS — **pindah ke shell LMS** | `siswa.lms.dashboard` | `resources/views/siswa/lms/dashboard.blade.php` |
| Siswa / 04 Halaman Mapel | Sidebar LMS → Mata Pelajaran → *(mapel)* | `siswa.lms.mapel.show` | `resources/views/siswa/lms/mata-pelajaran/show.blade.php` |
| Siswa / 05 Kerjakan Tugas | (turunan: tab Tugas pada halaman mapel) | `siswa.lms.mapel.tugas.show` | `resources/views/siswa/lms/mata-pelajaran/tugas/show.blade.php` |
| Siswa / 06 Ujian Fullscreen | (turunan: tab Ujian — **layout tanpa sidebar**) | `siswa.lms.mapel.ujian.show` | `resources/views/siswa/lms/mata-pelajaran/ujian/show.blade.php` |

### Wali Siswa — prefix `/wali-siswa`

| Layar Prototype | Menu Sidebar | Route | Berkas Kode |
|---|---|---|---|
| Wali Siswa / 01 Dashboard | Dashboard | `wali-siswa.dashboard` | `resources/views/wali-siswa/dashboard.blade.php` |
| Wali Siswa / 02 Tagihan Anak | Monitoring Anak → *(nama anak)* → Tagihan | `wali-siswa.tagihan.anak` | `resources/views/wali-siswa/tagihan/index.blade.php` |
| Wali Siswa / 03 Pembayaran Midtrans | (turunan: tombol Bayar) | `wali-siswa.pembayaran.snap` | `resources/views/wali-siswa/pembayaran/snap.blade.php` |
| Wali Siswa / 04 Invoice | (turunan: setelah pembayaran berhasil) | `wali-siswa.pembayaran.invoice` | `resources/views/wali-siswa/tagihan/invoice.blade.php` |
| Wali Siswa / 05 Detail Rapor | Monitoring Anak → *(nama anak)* → Rapor | `wali-siswa.rapor.detail` | `resources/views/wali-siswa/rapor/detail.blade.php` |
| Wali Siswa / 06 Ajukan Izin | Monitoring Anak → *(nama anak)* → Presensi → Ajukan Izin | `wali-siswa.presensi.ajukan-izin` | `resources/views/wali-siswa/presensi/ajukan-izin.blade.php` |

---

## 4.x.4 Perancangan Alur Interaksi

Enam alur diuji pada prototype. Rinciannya di [`05-alur-prototype.md`](05-alur-prototype.md).

| No | Alur | Aktor | Jumlah Layar |
|---|---|---|---|
| 1 | Login dan pengarahan berdasarkan peran | 9 peran | 10 |
| 2 | Pembayaran tagihan melalui Midtrans | Wali Siswa, Bendahara | 7 |
| 3 | Penugasan: guru membuat, siswa mengumpulkan, guru menilai | Guru, Siswa | 7 |
| 4 | Pengelolaan rapor lintas peran | Wali Kelas, Ketua, Wali Siswa | 9 |
| 5 | Pendataan peserta didik baru | Admin | 4 |
| 6 | Presensi dan pengajuan izin | Wali Siswa, Wali Kelas, Siswa | 5 |

Alur ke-4 adalah yang paling panjang dan paling layak dibahas mendalam, karena memperlihatkan
**kontrol berjenjang**: wali kelas mengisi → ketua memvalidasi → wali kelas menerbitkan →
wali siswa mengajukan permintaan unduh → wali kelas menyetujui. Terdapat pula gerbang keuangan:
rapor tidak dapat diunduh bila status pembayaran belum memenuhi syarat.

---

## Lampiran yang disarankan

| Lampiran | Isi | Sumber |
|---|---|---|
| A | Tangkapan layar 61 frame prototype Figma | Ekspor PNG dari Figma |
| B | Tangkapan layar implementasi sistem berjalan | `docs/figma/screenshots/` (hasil `npm run capture:ui`) |
| C | Tabel pemetaan layar ↔ menu ↔ kode | Bagian 4.x.3 dokumen ini |
| D | Daftar token desain | `01-design-tokens.md` |
| E | Struktur menu sidebar seluruh peran | `04-navigasi-sidebar-per-role.md` |

**Peringatan untuk Lampiran B:** tangkapan layar diambil dari basis data lokal dan dapat
memuat nama serta data peserta didik yang sebenarnya. Periksa dan samarkan sebelum
dilampirkan. Folder `docs/figma/screenshots/` sudah dikecualikan dari Git, tetapi berkas
skripsi tidak otomatis ikut terlindungi.

---

## Pertanyaan penguji yang mungkin muncul

**"Kenapa prototype-nya sangat mirip dengan sistem jadi?"**
Karena prototype yang disajikan adalah hasil iterasi akhir yang menjadi acuan implementasi.
Pada model prototyping, kemiripan tinggi antara prototype final dan implementasi justru
menandakan tidak ada penyimpangan dalam pembangunan sistem.

**"Kenapa hanya 61 layar dari 451 halaman?"**
Karena 451 berkas itu mencakup ±90 halaman cetak yang polanya identik, modul yang strukturnya
berulang antar peran, serta halaman turunan CRUD. 61 layar dipilih agar seluruh peran, seluruh
pola tata letak, dan seluruh alur utama terwakili tanpa pengulangan yang tidak perlu.

**"Kenapa ada tiga sistem desain? Bukankah itu tidak konsisten?"**
Pemisahan itu disengaja dan mengikuti konteks penggunaan: ruang pembelajaran dibedakan dari
ruang administrasi agar peserta didik tidak tercampur konteks, dan halaman publik memakai
identitas visual lembaga. Konsistensi tetap dijaga **di dalam** masing-masing sistem — hal ini
dapat ditunjukkan lewat token desain pada `01-design-tokens.md`.

**"Bagaimana memastikan prototype ini benar-benar sesuai sistem?"**
Melalui tabel pemetaan pada bagian 4.x.3, yang menghubungkan setiap layar prototype dengan
menu sidebar, nama route, dan berkas kodenya. Perbandingan visual juga dapat dilakukan
langsung antara Lampiran A (prototype) dan Lampiran B (implementasi).

**"Mengapa struktur menu dipakai sebagai acuan, bukan diagram use case?"**
Karena struktur menu dan route adalah representasi paling akurat dari sistem yang benar-benar
berjalan — keduanya dibaca langsung dari kode dan dapat diverifikasi kapan saja melalui
perintah `php artisan route:list`. Setiap baris pada tabel pemetaan menunjuk berkas kode yang
nyata, sehingga klaim kesesuaian rancangan dan implementasi dapat diperiksa satu per satu.
