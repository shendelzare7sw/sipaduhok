# 04 — Navigasi Sidebar per Role (Information Architecture)

> **Sidebar adalah navigasi utama SIPADUHOK**, dan isinya berbeda total tiap role.
> Dokumen ini adalah hasil pembacaan langsung 11 file sidebar partial — label, ikon,
> dan route name di sini **sudah diverifikasi ke kode**, bukan disalin dari dokumen lama.
>
> Di Figma: buat **satu komponen sidebar** dengan **9 varian role**. Semua varian berbagi
> shell yang sama (gradient, brand, spacing) dan hanya berbeda isi item.

---

## Anatomi sidebar (sama untuk semua role Sneat)

```
┌─ aside .layout-menu ────────────── 260px, gradient 180° #4361ee → #2b4162
│  ┌ .app-brand ─────────────────── flex-shrink: 0
│  │  [logo.png max-h 50px]  SIPADUHOK      ← 16px / 700 / uppercase / #fff
│  └
│  ┌ ul.menu-inner ──────────────── scrollable, padding-bottom 2rem
│  │  li.menu-item      → a.menu-link  [ikon 24px + gap 12px] Label
│  │  li.menu-header    → span.menu-header-text  ← 12px / 600 / rgba(255,255,255,.6) / uppercase / ls .5px
│  │  li.menu-item.open → ul.menu-sub (indentasi, tanpa ikon di sebagian besar role)
│  └
└─
```

**State item:**
| State | Style |
|---|---|
| Normal | teks `rgba(255,255,255,.8)`, weight 400 |
| Hover | teks `#fff`, background `rgba(255,255,255,.10)` |
| **Aktif** | teks `#fff`, background `rgba(255,255,255,.15)`, **weight 600** |
| Badge notifikasi | `.badge.bg-danger.rounded-pill.ms-auto` — pill merah `#ef4444` di ujung kanan |

Sumber: `resources/css/layouts/sneat.css:56-61, 76-85, 237-270`.

**Ikon:** FontAwesome 6 di semua role **kecuali Wali Siswa**, yang memakai **Boxicons**
(`bx bx-home-circle`, `bx bxs-user-circle`, dst). Ini anomali nyata di kode — pertahankan
supaya prototype jujur, atau catat sebagai temuan.

---

## 1. ADMIN — `admin/partials/sneat-sidebar-menu.blade.php`

Sidebar terbesar: 8 grup, 20 item. Ini yang jadi frame acuan.

```
🏠 Dashboard                                    fas fa-home            admin.dashboard
── MANAJEMEN KONTEN ──
🌐 Landing Page                                 fas fa-globe           admin.landing-pages.index
📢 Konten Publikasi ▾                           fas fa-bullhorn
     Kalender Akademik                                                 admin.akademik.kalender.index
     Pengumuman                                                        admin.akademik.pengumuman.index
     Flyer / Iklan                                                     admin.akademik.flyer.index
     Kelola Berita                                                     admin.akademik.berita.index
── MANAJEMEN PENGGUNA ──
👥 Kelola Data Pengguna ▾                       fas fa-users
     Tenaga Pendidik                                                   admin.users.tenaga-pendidik
     Siswa                                                             admin.users.siswa
     Wali Siswa                                                        admin.users.wali-siswa
🛟 Tiket Pemulihan Akun            [badge N]    fas fa-life-ring       admin.recovery-tickets.index
⚙️ Pengaturan Sistem ▾                          fas fa-cogs
     Pengaturan LMS                                                    admin.lms-settings.index
     Pengaturan AI                                                     admin.ai-settings.index
── DATA MASTER ──
📅 Tahun Ajaran                                 fas fa-calendar-alt    admin.tahun-ajaran.index
🏢 Manajemen Cabang                             fas fa-building        admin.cabang.index
── DATA AKADEMIK ──
🧑‍🏫 Data Kelas & Penugasan ▾                     fas fa-chalkboard-teacher
     Data Kelas                                                        admin.kelas.index
     Data Wali Kelas                                                   admin.wali-kelas.index
     Data Guru Pengajar                                                admin.guru-pengajar.index
     Manajemen Siswa                                                   admin.manajemen-siswa.index
📖 Mata Pelajaran                               fas fa-book            admin.mata-pelajaran.index
🗓️ Jadwal Pelajaran                             fas fa-calendar-week   admin.jadwal-pelajaran.index
── KEUANGAN ──
🧾 Tagihan & Pembayaran ▾                       fas fa-file-invoice-dollar
     Tagihan                                                           admin.keuangan.tagihan.index
     Tarik Tunggakan                                                   admin.keuangan.tagihan.carryover
     Pembayaran                                                        admin.keuangan.pembayaran.index
     Config Pembayaran                                                 admin.keuangan.info-pembayaran.index
📈 Laporan Keuangan                             fas fa-chart-line      admin.keuangan.laporan.index
── VALIDASI & DISPENSASI ──
✅ Validasi Ujian & Rapor                       fas fa-check-circle    admin.keuangan.validasi-akses.index
💰 Validasi Dispensasi                          fas fa-hand-holding-usd admin.keuangan.kenaikan-kelas.validation.index
── KENAIKAN KELAS ──
⚙️ Pengaturan Kenaikan ▾                        fas fa-cogs
     Pengaturan KKM                                                    admin.akademik.kenaikan-kelas.kkm.index
     Pengaturan Kenaikan                                               admin.akademik.kenaikan-kelas.settings.index
📋 Proses & Rekap                               fas fa-tasks           admin.akademik.kenaikan-kelas.report
── MONITORING & ANALITIK ──
📊 Monitoring Sistem ▾                          fas fa-chart-bar
     Pengguna · Wali Kelas · Guru Pengajar · Siswa · Monitoring LMS    admin.monitoring.*
📄 Laporan & Catatan ▾                          fas fa-file-alt
     Laporan                                                           admin.laporan.index
     Catatan                                                           admin.catatan.index
```

---

## 2. WAKIL KEPALA SEKOLAH — `waka/partials/sneat-sidebar-menu.blade.php`

Cermin sisi akademik dari Admin. **Tidak punya** keuangan, konten, maupun manajemen pengguna.

```
🏠 Dashboard                                    fas fa-home            waka.dashboard
── MANAJEMEN AKADEMIK ──
📅 Tahun Ajaran                                 fas fa-calendar-alt    waka.tahun-ajaran.index
── DATA AKADEMIK ──
🧑‍🏫 Data Kelas & Penugasan ▾                     fas fa-chalkboard-teacher
     Data Kelas / Data Wali Kelas / Data Guru Pengajar / Manajemen Siswa   waka.{kelas|wali-kelas|guru-pengajar|manajemen-siswa}.index
📖 Mata Pelajaran                               fas fa-book            waka.mata-pelajaran.index
🗓️ Jadwal Pelajaran                             fas fa-calendar-week   waka.jadwal-pelajaran.index
── KENAIKAN KELAS ──
⚙️ Pengaturan Kenaikan ▾                        fas fa-cogs
     Pengaturan KKM                                                    waka.kenaikan-kelas.kkm.index
     Pengaturan Kenaikan                                               waka.kenaikan-kelas.settings.index
📋 Proses & Rekap                               fas fa-tasks           waka.kenaikan-kelas.report
── MONITORING & ANALITIK ──
📊 Monitoring Sistem ▾                          fas fa-chart-bar
     Wali Kelas / Guru Pengajar / Siswa / Monitoring LMS               waka.monitoring.*
── KOMUNIKASI ──
📝 Catatan                                      fas fa-sticky-note     waka.catatan.index
```

Beda halus dari Admin yang mudah terlewat:
- Monitoring waka **tidak punya sub-item "Pengguna"** (admin punya).
- Route kenaikan kelas waka: `waka.kenaikan-kelas.*` — **tanpa** segmen `akademik.`
  yang dipakai admin (`admin.akademik.kenaikan-kelas.*`).
- `pengaturan-istirahat` ada di route tapi **tidak muncul di sidebar** (diakses dari halaman jadwal).

---

## 3. KETUA PKBM — `ketua/partials/sneat-sidebar-menu.blade.php`

Peran **approver**, hampir seluruhnya read-only. Tidak ada CRUD data master.

```
🏠 Dashboard                                    fas fa-home                ketua.dashboard
── PERSETUJUAN & VALIDASI ──
✅✅ Approval Dispensasi                        fas fa-check-double        ketua.kenaikan-kelas.approval.index
🎖️ Validasi Rapor                               fas fa-certificate         ketua.validasi-rapor.index
🤲 Dispensasi Keuangan             [badge N]    fas fa-hand-holding-heart  ketua.dispensasi.index
── MONITORING ──
📊 Monitoring Sistem ▾                          fas fa-chart-bar
     Data Pengguna / Data Wali Kelas / Data Guru Pengajar / Data Siswa / Monitoring LMS
── LAPORAN & KOMUNIKASI ──
📄 Laporan & Catatan ▾                          fas fa-file-alt
     Cetak Laporan                                                        ketua.laporan.index
     Kirim Catatan                                                        ketua.catatan.index
```

---

## 4. SEKRETARIS — `sekretaris/partials/sneat-sidebar-menu.blade.php`

Role paling ringan: hanya 2 node. Bagus dipakai sebagai flow prototype pendek.

```
🏠 Dashboard                                    fas fa-home            sekretaris.dashboard
── MANAJEMEN KONTEN ──
📢 Konten Publikasi ▾                           fas fa-bullhorn
     Kalender Akademik                                                 sekretaris.kalender.index
     Pengumuman                                                        sekretaris.pengumuman.index
     Flyer / Iklan                                                     sekretaris.flyer.index
     Kelola Berita                                                     sekretaris.berita.index
```

---

## 5. BENDAHARA — `bendahara/partials/sneat-sidebar-menu.blade.php`

```
🏠 Dashboard                                    fas fa-home                 bendahara.dashboard
── KEUANGAN ──
🧾 Tagihan & Pembayaran ▾                       fas fa-file-invoice-dollar
     Kelola Tagihan                                                        bendahara.tagihan.index
     Tarik Tunggakan                                                       bendahara.tagihan.carryover
     Kelola Pembayaran                                                     bendahara.pembayaran.index
     Config Pembayaran                                                     bendahara.info-pembayaran.index
── VALIDASI & DISPENSASI ──
✅ Validasi Ujian & Rapor                       fas fa-check-circle         bendahara.validasi-akses.index
💰 Validasi Dispensasi                          fas fa-hand-holding-usd     bendahara.kenaikan-kelas.validation.index
── LAPORAN ──
📊 Laporan Keuangan ▾                           fas fa-chart-bar
     Laporan Pembayaran                                                    bendahara.laporan.index
     Rekap Tagihan                                                         bendahara.laporan.rekap-tagihan
     Siswa Belum Lunas                                                     bendahara.laporan.belum-lunas
```

Label bendahara pakai awalan "Kelola" (`Kelola Tagihan`, `Kelola Pembayaran`), sedangkan
admin memakai label polos (`Tagihan`, `Pembayaran`) — kelihatan sepele tapi terlihat jelas
saat dua frame ditaruh bersebelahan.

Halaman yang **tidak ada di sidebar** tapi penting untuk flow prototype:
`bendahara.tagihan.bulk-create`, `.create-custom`, `.generate-spp`, `.duplicate`,
`.show/{siswa}`, `.cetak`, `bendahara.pembayaran.cetak-kwitansi`.

---

## 6. WALI KELAS — `wali-kelas/partials/sneat-sidebar-menu.blade.php`

**Sidebar paling kompleks secara kondisional.** Ada tiga bagian yang muncul/hilang
bergantung apakah wali kelas memegang lebih dari satu kelas.

### Kartu "Kelas Aktif" — hanya muncul jika `$selectedKelas && $hasMultipleKelas`

Ini elemen visual unik yang **tidak ada di role lain**, dan wajib ada di prototype:

```
┌─────────────────────────────────┐   margin 8px 20px 8px 12px
│ 🏫 KELAS AKTIF                  │   padding .5rem 1rem, radius 8px
│ Kelas 7A                        │   background: linear-gradient(135deg,#8b5cf6,#7c3aed)
│ Cabang Pusat - SMP              │
│ ┌───────────────────────────┐   │   label  : 11.5px/700/uppercase/rgba(255,255,255,.82), ls .04em
│ │  ⇄  Ganti Kelas           │   │   judul  : 14px/600/#fff
│ └───────────────────────────┘   │   meta   : 12px/rgba(255,255,255,.7)
└─────────────────────────────────┘   tombol : full-width, radius 6px, bg rgba(255,255,255,.2), 12px/600
```
Sumber: `sneat.css:87-132`.

```
[Kartu Kelas Aktif]                             ← hanya jika punya >1 kelas
🏠 Dashboard                                    fas fa-home             wali.dashboard
⇄ Pilih Kelas                                   fas fa-exchange-alt     wali.pilih-kelas   ← hanya jika punya >1 kelas
── AKADEMIK ──
🗓️ Jadwal Pelajaran                             fas fa-calendar-week    wali.jadwal.index
📋 Kelola Presensi ▾                            fas fa-clipboard-check
     ✏️ Input Harian                            fas fa-edit             wali.presensi.index
     ✅ Validasi Izin                           fas fa-check-circle     wali.presensi.validasi-izin
     📆 Rekap Harian                            fas fa-calendar-day     wali.presensi.rekap-harian
     🕘 Riwayat & Edit                          fas fa-history          wali.presensi.riwayat
📄 Kelola Rapor ▾                               fas fa-file-alt
     📈 Nilai Siswa                             fas fa-chart-line       wali.nilai.index
     📄 Kelola Rapor                            fas fa-file-alt         wali.rapor.index
     🗄️ Arsip Kelas Saya                        fas fa-archive          wali.arsip.index
     ⬇️ Permintaan Unduh          [badge N]     fas fa-download         wali.rapor.request-download.index
── KENAIKAN KELAS ──
📊 Prediksi Kenaikan                            fas fa-chart-bar        wali.kenaikan-kelas.prediction
── VALIDASI ──
✅✅ Validasi Akses                             fas fa-check-double     wali.validasi-akses.index
```

Catatan: **sub-item wali kelas punya ikon** (`fa-xs`, `me-2`), berbeda dari admin/waka/bendahara
yang sub-itemnya polos tanpa ikon. Perbedaan ini terlihat jelas di prototype.

Tidak di sidebar tapi ada di route: `wali.rapor-pending`, `wali.template-capaian.index`.

---

## 7. GURU PENGAJAR — dua sidebar berbeda

### 7a. Shell SIA — `guru/partials/sneat-sidebar-menu.blade.php`

Bagian "Kelas Saya" **dibangun dinamis** dari `$sidebarKelas` (loop kelas → loop mapel).

```
🏠 Dashboard                                    fas fa-home             guru.dashboard
── AKADEMIK ──
📅 Informasi Akademik ▾                         fas fa-calendar-alt
     Jadwal Mengajar                                                    guru.jadwal.index
     Semua Kelas                                                        guru.kelas.index
── PEMBELAJARAN ──
🗄️ Arsip LMS                                    fas fa-archive          guru.lms.arsip.index
💬 Catatan Monitoring              [badge N]    fas fa-comment-dots     guru.lms.catatan-monitoring.index
── KELAS SAYA (AKSES CEPAT) ──                  ← dinamis, bisa kosong
🏫 Kelas 7A ▾                                   fas fa-chalkboard
     Matematika                                                         guru.lms.dashboard(7A, matematika)
     IPA                                                                guru.lms.dashboard(7A, ipa)
🏫 Kelas 8B ▾                                   fas fa-chalkboard
     Matematika                                                         guru.lms.dashboard(8B, matematika)
```

Untuk Figma buat **2 varian**: satu dengan blok "Kelas Saya" terisi, satu tanpa blok itu
(guru baru yang belum punya penugasan).

### 7b. Shell LMS — `guru/partials/sidebar-lms.blade.php` (layout `lms-guru`)

Sidebar **berbeda total**: bukan `menu-item` Sneat, tapi `nav-link` datar dengan
`nav-section-title` sebagai pemisah. Warna sistem LMS (`#165fac`), lebar 280px.

```
┌ .lms-teaching-context ──────────┐   blok konteks di puncak sidebar
│ Anda Mengajar:                  │
│ Matematika                      │
│ Kelas 7A                        │
└─────────────────────────────────┘
── UTAMA ──
🏠 Beranda                                      guru.lms.dashboard
── PEMBELAJARAN ──
📖 Materi                                       guru.lms.materi.index
📋 Tugas                       [badge-notif N]  guru.lms.tugas.index
📐 Latihan                                      guru.lms.latihan.index
📄 Ujian                                        guru.lms.ujian.index
💬 Forum Diskusi                                guru.lms.forum.index
🎥 Kelas Virtual                                guru.lms.meeting.index
── PENILAIAN ──
📈 Nilai Siswa                                  guru.lms.nilai.index
── NAVIGASI ──
⬅️ Kembali ke Dashboard                         guru.dashboard
```

Semua route LMS guru bersarang di `lms/{kelas}/{mapel}` — jadi ini **navigasi 2 level**:
pilih kelas+mapel di shell SIA, lalu masuk ke shell LMS.

---

## 8. SISWA — dua shell, transisi eksplisit

### 8a. Shell SIA — `siswa/partials/sneat-sidebar-sia.blade.php`

```
🏠 Dashboard SIA                                fas fa-home             siswa.sia.dashboard
── LEARNING MANAGEMENT ──                       ← hanya jika jenjang siswa ada di AppSetting 'lms_allowed_jenjang'
🎓 HOK-LMS                        (fw-bold)     fas fa-graduation-cap   siswa.lms.dashboard
── AKADEMIK ──
📅 Presensi                                     fas fa-calendar-check   siswa.sia.presensi.index
📈 Data Penilaian                               fas fa-chart-line       siswa.sia.penilaian
```

**Hanya 3 item.** Menu Rapor dan Pembayaran **sengaja dihapus dari siswa** dan dipindah ke
Wali Siswa — ada komentar eksplisit di akhir file. Jangan menambahkannya di prototype
"supaya terlihat lengkap"; itu justru salah menggambarkan sistem.

Buat 2 varian: dengan blok HOK-LMS dan tanpa (jenjang yang tidak diizinkan LMS).

### 8b. Shell LMS — `siswa/partials/sidebar-lms.blade.php` (layout `lms`)

Memakai **Bootstrap Icons** (`bi bi-*`), bukan FontAwesome. Daftar mapel dinamis dari jadwal kelas.

```
⬅️ Kembali ke SIA                bi-arrow-left-circle-fill   siswa.sia.dashboard
── BERANDA ──
🏠 Beranda                       bi-house-door-fill          siswa.lms.dashboard
── MATA PELAJARAN ──                                          ← dinamis, urut nama
📖 Bahasa Indonesia              bi-book                     siswa.lms.mapel.show(id)
📖 Matematika                    bi-book
📖 IPA                           bi-book
   (kosong → "Belum ada mata pelajaran", teks muted, non-klik)
── AKADEMIK ──
📅 Kalender Akademik             bi-calendar3                siswa.lms.kalender
🕘 Jadwal Pelajaran              bi-clock-history            siswa.lms.jadwal
👤 Daftar Guru                   bi-person-video3            siswa.lms.guru
```

### 8c. Shell ujian/latihan

`layouts/lms-ujian.blade.php` dan `lms-latihan.blade.php` adalah **fullscreen tanpa sidebar
sama sekali** (anti-distraksi, ada partial `anti-screenshot`). Di Figma ini frame tersendiri
tanpa navigasi — dan itu memang keputusan desain yang layak disorot di skripsi.

---

## 9. WALI SISWA — `wali-siswa/partials/sneat-sidebar-menu.blade.php`

Sidebar **dinamis per anak** (`$user->children()`), dan satu-satunya yang memakai **Boxicons**.

```
🏠 Dashboard                     bx bx-home-circle           wali-siswa.dashboard
── MONITORING ANAK ──                                        ← loop per anak
👤 Ahmad Fauzi ▾                 bx bxs-user-circle          (nama dipotong 20 karakter)
     📅 Presensi                 bx bx-calendar-check        wali-siswa.presensi.anak(id)
     💳 Tagihan                  bx bx-credit-card           wali-siswa.tagihan.anak(id)
     📄 Rapor                    bx bx-file                  wali-siswa.rapor.anak(id)
👤 Siti Nurhaliza ▾              bx bxs-user-circle
     Presensi / Tagihan / Rapor
   (kosong → "Belum Ada Data Anak", item disabled)
```

Buat **3 varian** di Figma: 1 anak, 2+ anak, dan kondisi kosong. Varian multi-anak adalah
pembeda utama role ini dan layak dipakai di flow pembayaran.

Perhatikan: sub-item wali siswa **punya ikon**, dan label menu memakai `Str::limit(...,20)` —
jadi nama panjang benar-benar terpotong dengan elipsis di aplikasi.

---

## Ringkasan untuk pembuatan komponen Figma

| Varian sidebar | Grup | Item level-1 | Catatan khusus |
|---|---|---|---|
| `role=admin` | 8 | 20 | Terbesar, badge di Tiket Pemulihan |
| `role=waka` | 5 | 9 | Mirip admin tanpa keuangan/konten/pengguna |
| `role=ketua` | 3 | 6 | Badge di Dispensasi Keuangan |
| `role=sekretaris` | 1 | 2 | Paling ringan |
| `role=bendahara` | 3 | 5 | Label berawalan "Kelola" |
| `role=wali-kelas` | 3 | 7 | + kartu Kelas Aktif ungu, sub-item berikon, badge Permintaan Unduh |
| `role=guru-sia` | 2 | 5 | + blok "Kelas Saya" dinamis, badge Catatan Monitoring |
| `role=guru-lms` | 4 | 9 | Shell LMS, blok konteks mengajar, lebar 280px |
| `role=siswa-sia` | 2 | 3 | Sangat ringkas, HOK-LMS kondisional |
| `role=siswa-lms` | 3 | 6+ | Bootstrap Icons, daftar mapel dinamis |
| `role=wali-siswa` | 1 | 1+N | Boxicons, dinamis per anak |

Total **11 varian** (9 role, tapi guru dan siswa masing-masing punya 2 shell).

---

## Koreksi terhadap dokumen lama

Diverifikasi ulang ke `routes/web.php` dan file sidebar, ditemukan tiga hal yang sudah usang
di `docs/flow/`:

1. `docs/flow/orang-tua.md` menyebut prefix `/orang-tua` dan route `orang-tua.` —
   implementasi nyata adalah `/wali-siswa` dan `wali-siswa.`.
2. Route kenaikan kelas waka adalah `waka.kenaikan-kelas.*`, **bukan** `waka.akademik.promotion.*`.
3. Monitoring waka tidak memiliki sub-item "Pengguna"; hanya admin dan ketua yang punya.
