# Role: Siswa

> Kembali ke [flow.md](../../flow.md) · Role `siswa` · Level 6 · Prefix `/siswa` · Route `siswa.` · Middleware `role:siswa` + `student.active` (siswa harus berstatus aktif).

## Ringkasan Peran

Peserta didik. **Punya dua konteks** (dua sidebar):
1. **SIA (Sistem Informasi Akademik)** — info akademik pribadi: dashboard, presensi, data penilaian.
2. **LMS (HOK-LMS)** — pembelajaran daring per mata pelajaran: materi, tugas, latihan, ujian, forum, kelas virtual, kalender, jadwal, daftar guru.

Catatan desain: **rapor & pembayaran TIDAK ada di sisi siswa** — sengaja dipindah ke Orang Tua (lihat komentar di sidebar SIA & grup route orang-tua) agar siswa tidak menyembunyikan info keuangan/rapor; siswa fokus belajar.

## Layout & Sidebar (DUA layout)

| Konteks | File sidebar | Kapan tampil | Akses |
|---|---|---|---|
| SIA | `resources/views/siswa/partials/sneat-sidebar-sia.blade.php` | Halaman `/siswa/sia/...` | Selalu |
| LMS | `resources/views/siswa/partials/sidebar-lms.blade.php` | Halaman `/siswa/lms/...` | Hanya jika jenjang kelas siswa ∈ `AppSetting` `lms_allowed_jenjang` (middleware `lms.access`) |

Menu "HOK-LMS" di sidebar SIA hanya muncul bila `$showLms` true (jenjang diizinkan). Sidebar LMS menampilkan daftar **mata pelajaran dinamis** dari `JadwalPelajaran` kelas siswa.

## Peta Menu — Konteks SIA

| Menu | Route | Controller@method | Model |
|---|---|---|---|
| Dashboard SIA | `siswa.sia.dashboard` | `Siswa\SiaDashboardController@index` | `Siswa`,`Nilai`,`Presensi` |
| (Router) Dashboard | `siswa.dashboard` | `Siswa\SiswaDashboardController@index` | — |
| HOK-LMS (link) | `siswa.lms.dashboard` | `Siswa\LmsDashboardController@index` | — |
| Presensi | `siswa.sia.presensi.index` | `Siswa\SiaPresensiController@index` | `Presensi` |
| Data Penilaian | `siswa.sia.penilaian` | `Siswa\SiaDashboardController@penilaian` | `Nilai` |

> Route ada tapi tidak di sidebar: `siswa.sia.pembayaran.*` (`SiaPembayaranController`, termasuk callback Midtrans) — sebagian besar fungsi pembayaran dialihkan ke Orang Tua. Rapor siswa (`SiaRaporController`) **di-disable** (dikomentari di routes).

## Peta Menu — Konteks LMS (middleware `lms.access`)

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| Beranda | Beranda | `siswa.lms.dashboard` | `Siswa\LmsDashboardController@index` | `Materi`,`Tugas`,`Ujian` |
| Mata Pelajaran | [Nama Mapel] (dinamis) | `siswa.lms.mapel.show` (mapelId) | `Siswa\LmsMateriController@show` | `MataPelajaran`,`Materi` |
| (dalam mapel) | Materi | `siswa.lms.mapel.materi` | `Siswa\LmsMateriController@lihatMateri` | `Materi` |
| (dalam mapel) | Tugas | `siswa.lms.mapel.tugas.index`/`.show`/`.submit` | `Siswa\LmsTugasController@index/show/submit` | `Tugas`,`TugasSiswa` |
| (dalam mapel) | Ujian | `siswa.lms.mapel.ujian.show`/`mulai`/`submit`/`retake`/`autosave`/`review` | `Siswa\LmsUjianController` | `Ujian`,`UjianSiswa`,`JawabanSiswa` |
| (dalam mapel) | Latihan | `siswa.lms.mapel.latihan.*` | `Siswa\LmsUjianController` | `Ujian`,`UjianSiswa` |
| (dalam mapel) | Forum | `siswa.lms.mapel.forum.*` | `Siswa\LmsForumController` | `ForumDiskusi`,`ForumReply` |
| (dalam mapel) | Kelas Virtual | `siswa.lms.mapel.meeting.index` | `Siswa\SiswaLmsMeetingController@index` | `LmsMeeting` |
| Akademik | Kalender Akademik | `siswa.lms.kalender` (+ `.detail`) | `Siswa\SiswaDashboardController@kalenderTahunan/kalenderDetail` | `KalenderAkademik` |
| Akademik | Jadwal Pelajaran | `siswa.lms.jadwal` (+ `.print`) | `Siswa\LmsDashboardController@jadwal/printJadwal` | `JadwalPelajaran` |
| Akademik | Daftar Guru | `siswa.lms.guru` | `Siswa\LmsDashboardController@guru` | `TenagaPendidik` |
| (link) | Kembali ke SIA | `siswa.sia.dashboard` | — | — |

Akses mapel dijaga middleware `siswa.mapel.access` (`CheckSiswaMapelAccess`) — siswa hanya boleh buka mapel yang ada di jadwal kelasnya. View dir: `resources/views/siswa/`, LMS di `resources/views/siswa/lms/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Pengumpulan Tugas** (`siswa.lms.mapel.tugas.submit`) — `@submit` membuat/memperbarui `TugasSiswa`: cek deadline → status `dikerjakan` atau `terlambat`; cek batas pengulangan bila `bisa_diulang`. Lalu Guru mengoreksi (status → `dinilai`) dan siswa melihat nilai+feedback bila `tampilkan_nilai`. Lihat [flow.md §5.5](../../flow.md#55-pembelajaran-lms-guru--siswa).
- **Ujian** — hanya bisa dikerjakan bila tervalidasi (gerbang `validasi_ujian_bendahara`/`_wali`, lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa)). Mendukung `autosave` & `retake` (bila diizinkan guru).
- **Akses LMS bertingkat**: `student.active` (akun aktif) → `lms.access` (jenjang diizinkan) → `siswa.mapel.access` (mapel ada di jadwal kelas). Jika salah satu gagal, menu LMS/mapel tidak bisa diakses.
- **Rapor & Pembayaran sengaja absen** — tanggung jawab dilempar ke Orang Tua ([docs/flow/orang-tua.md](orang-tua.md)). Ini keputusan desain, bukan fitur yang belum dibuat.

## Detail Sub-Halaman per Menu

> Siswa punya **dua konteks** (dua sidebar), jadi section detail dibagi 2 sub-section. Convention: route name di-prefix `siswa.`; view path relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/Siswa/`.

---

## A. Konteks SIA (`/siswa/sia/...`)

### Dashboard SIA

**Index view**: `siswa/sia/dashboard.blade.php` · **Controller**: `SiaDashboardController.php`

**Tampilan index**: **Profile banner** gradient dengan avatar + sapaan "Halo, {nama depan}!" + meta (kelas, TA, NIS). Bila ada pengumuman aktif, **banner pengumuman** muncul (max 2 item terbaru, judul+excerpt+tanggal). 4 **stat card kehadiran**: Hadir, Sakit, Izin, Alpha. Layout 2-kolom utama: **kolom kiri** berisi **Jadwal Hari Ini** (timeline cards berdasar `JadwalPelajaran`) + cross-link "Lihat Semua" → `siswa.lms.jadwal` (hanya tampil bila jenjang ∈ LMS-allowed), serta daftar tugas/ujian terbaru. **Kolom kanan**: ringkasan akademik (rata-rata nilai, count tugas pending, dll).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| (Tidak ada sub-halaman dari dashboard) | — | — | — | — | Halaman hub — semua link mengarah ke sub-menu lain (SIA atau LMS). |

**Catatan**: `(Router) siswa.dashboard` (`SiswaDashboardController@index`) men-detect jenjang siswa & redirect ke `siswa.sia.dashboard` (default) atau `siswa.alumni.dashboard` bila siswa berstatus `lulus`/`alumni`.

---

### Presensi (Read-Only)

**Index view**: `siswa/sia/presensi/index.blade.php` · **Controller**: `SiaPresensiController.php`

**Tampilan index**: 4 **stat card** rekap bulanan: Hari Hadir, Hari Sakit, Hari Izin, Hari Alpha. Card **Riwayat Presensi Bulan Ini** dengan sub-judul bulan (translatedFormat). Konten utama: **list per minggu** (Minggu ke-1, 2, dst.) — tiap minggu adalah card dengan tabel kolom: Tanggal, Hari, Status (badge warna sesuai status: hadir/sakit/izin/alpha, atau "MENUNGGU VALIDASI"/"DITOLAK" untuk pengajuan izin), Keterangan.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| (Tidak ada sub-halaman) | — | — | — | — | **Pure read-only**. Tombol "Ajukan Izin" sudah **dihapus** dari sidebar siswa — fitur tsb. ada di Orang Tua (`orang-tua.presensi.ajukan-izin`). Route `siswa.sia.presensi.ajukan-izin` & `store-izin` **di-comment** di `routes/web.php:1428-1429`. |

**Catatan**: Status `pending` di tabel mengindikasikan pengajuan izin oleh orang tua belum divalidasi wali kelas. `ditolak` artinya wali menolak izin → otomatis dihitung sebagai `alpha`.

---

### Data Penilaian

**Index view**: `siswa/sia/penilaian/index.blade.php` · **Controller**: `SiaDashboardController.php` (method `@penilaian`)

**Tampilan index**: Card **Statistik Belajar** dengan info kelas + TA + dropdown **Semester** (Ganjil/Genap, auto-submit). Konten utama: **list card per mata pelajaran** — tiap card berisi nama mapel + nama guru pengampu, lalu **5 mini-stat berwarna** (Rata Tugas 10%, Rata Latihan 10%, Rata UH 20%, Nilai PTS 30%, Nilai PAS 30%) + nilai akhir + **badge Predikat Capaian** (A Sangat Baik / B Baik / C Cukup / D Kurang / E Sangat Kurang, atau "Belum Tersedia"). Empty state bila mapel/semester pilihan tidak ada nilai.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| (Tidak ada sub-halaman) | — | — | — | — | Pure read-only. Filter semester via dropdown (GET parameter `semester=ganjil\|genap`). |

**Catatan**: Sumber data dari tabel `Nilai` (di-isi guru via `guru.lms.nilai.*`). **Rapor (file PDF/Excel rapor)** TIDAK diakses dari sini — siswa harus minta orang tua via menu `orang-tua.rapor.*`.

---

### Pembayaran (tidak di sidebar, route masih ada)

**Index view**: `siswa/sia/pembayaran/index.blade.php` · **Controller**: `SiaPembayaranController.php`

**Tampilan index**: 3 **stat card** gradient: Total Tagihan (biru), Sudah Dibayar (hijau), Sisa Tagihan (oranye). Header section "Daftar Tagihan" + tombol kanan **Riwayat Pembayaran** (info, → `riwayat`). **List card per tagihan**: judul jenis_tagihan, tanggal jatuh tempo, status badge (Lunas/Belum Bayar), jumlah, tombol **Bayar Sekarang** (primary, buka modal upload bukti) atau **Sudah Lunas** (disabled).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Bayar (submit modal upload) | `siswa.sia.pembayaran.bayar` | POST | `@bayar` (**method tidak ada — broken**) | — | Route menunjuk `SiaPembayaranController@bayar`, tapi controller hanya punya `prosesBayar`. **Route broken/orphan**. |
| Riwayat Pembayaran | `siswa.sia.pembayaran.riwayat` | GET | `@riwayat` | `siswa/sia/pembayaran/riwayat.blade.php` | List riwayat pembayaran siswa + cetak per item. |
| Cetak Bukti | `siswa.sia.pembayaran.cetak` | GET | `@cetak` (**method tidak ada — broken**) | — | Route menunjuk `@cetak`, tapi controller hanya punya `cetakBukti`. **Route broken/orphan**. |
| Midtrans Notification (webhook) | `siswa.sia.pembayaran.midtrans-notification` | POST | `@midtransNotification` (**method tidak ada — broken**) | — | Endpoint callback Midtrans, **broken**. |
| Midtrans Finish (redirect) | `siswa.sia.pembayaran.midtrans-finish` | GET | `@midtransFinish` (**method tidak ada — broken**) | — | Redirect setelah bayar Midtrans, **broken**. |

**Catatan**: Menu Pembayaran **tidak muncul di sidebar siswa** (sengaja dipindah ke Orang Tua di `orang-tua.tagihan.*` & `orang-tua.pembayaran.*`). View `index.blade.php` & `riwayat.blade.php` masih ada di disk + route masih terdaftar, tapi **4 dari 5 route binding-nya menunjuk method yang tidak ada di controller** — siapapun yang mencoba akses URL ini akan error `BadMethodCallException`. Method controller saat ini: `index`, `prosesBayar`, `riwayat`, `cetakBukti`. Bisa jadi: (a) route belum di-update setelah rename method, atau (b) memang sengaja di-broken sebagai bagian dari deprecation menuju Orang Tua. View `cetak.blade.php` juga ada di disk tapi orphan.

---

## B. Konteks LMS (`/siswa/lms/...`, middleware `lms.access`)

### Beranda LMS

**Index view**: `siswa/lms/dashboard.blade.php` · **Controller**: `LmsDashboardController.php`

**Tampilan index**: **Welcome banner** sapaan "Halo, {nama}!" + tombol **Akses SIA** (kembali ke `siswa.sia.dashboard`). 4 **stat card**: Kehadiran (%), Tugas Pending, Agenda Bulan Ini, Ujian Mendatang. Layout grid dashboard: kolom kiri berisi **Jadwal Hari Ini** (timeline clickable → `siswa.lms.mapel.show`) + section **Tugas Aktif** + section **Ujian Mendatang** (link "Lihat Semua" → `siswa.lms.tugas.index`). Kolom kanan: ringkasan agenda kalender.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Lihat Semua Tugas (global) | `siswa.lms.tugas.index` | GET | `LmsTugasController@indexAll` | `siswa/lms/mata-pelajaran/tugas/index.blade.php` | Daftar semua tugas dari semua mapel siswa (filter status/deadline). Berbeda dari `siswa.lms.mapel.tugas.index` yang per-mapel. |
| Klik kartu jadwal | `siswa.lms.mapel.show` | GET | `LmsMateriController@show` | `siswa/lms/mata-pelajaran/show.blade.php` | Masuk ke detail mata pelajaran. |

**Catatan**: Halaman ini adalah hub LMS — semua link mengarah ke sub-menu. Sidebar LMS otomatis menampilkan daftar mapel dinamis dari `JadwalPelajaran` kelas siswa.

---

### Kalender Akademik

**Index view**: `siswa/lms/kalender.blade.php` (juga `siswa/lms/kalender/index.blade.php` & `detail.blade.php`) · **Controller**: `LmsDashboardController@kalender` / `SiswaDashboardController@kalenderTahunan` (route: `siswa.lms.kalender`)

**Tampilan index**: Card **Kalender Akademik Tahun Ini** dengan list kegiatan **dikelompokkan per bulan** (header bulan + tahun) — tiap event card berisi nama kegiatan, tanggal mulai-selesai, waktu (jika ada), keterangan. Hover effect translate. Empty state bila tidak ada kegiatan.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Kegiatan (klik card) | `siswa.lms.kalender.detail` | GET | `SiswaDashboardController@kalenderDetail` | `siswa/lms/kalender/detail.blade.php` | Detail kegiatan per tanggal (`{tanggal}` parameter Y-m-d). Tampil semua kegiatan yang berlangsung di tanggal tsb. |

**Catatan**: Read-only — data `KalenderAkademik` di-input oleh Sekretaris/Admin. View `kalender.blade.php` (top-level) digunakan oleh `LmsDashboardController@kalender` sementara `kalender/index.blade.php` & `detail.blade.php` di-folder digunakan controller lain (variasi route).

---

### Pengumuman

**Index view**: `siswa/lms/pengumuman/index.blade.php` · **Controller**: `LmsDashboardController.php`

**Tampilan index**: List card pengumuman dengan prioritas badge, judul + excerpt + tanggal posting + penulis. Klik card → detail. Filter sederhana (kalau ada) atau pure list.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Pengumuman | `siswa.lms.pengumuman.show` | GET | `@pengumumanDetail` | `siswa/lms/pengumuman/show.blade.php` | Tampil isi pengumuman lengkap + lampiran (jika ada). |

**Catatan**: Read-only. Sumber data dari `Pengumuman` (di-input Sekretaris/Admin). Otomatis terbentuk juga dari `KalenderAkademik` (sumber otomatis).

---

### Jadwal Pelajaran

**Index view**: `siswa/lms/jadwal.blade.php` · **Controller**: `LmsDashboardController.php`

**Tampilan index**: Tombol **Cetak Jadwal** (primary, target blank) di kanan atas. Konten: **tabel mingguan** (weekly schedule grid) — header hari Senin–Sabtu, baris jam pelajaran, cell berisi mapel + guru. Slot istirahat ditampilkan sebagai row khusus (background kuning). Cell clickable → masuk ke `siswa.lms.mapel.show`.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Cetak Jadwal | `siswa.lms.jadwal.print` | GET | `@printJadwal` | `siswa/lms/jadwal-print.blade.php` | Layout cetak jadwal mingguan (DomPDF-friendly). |

**Catatan**: Read-only — jadwal dikelola Admin/Waka.

---

### Daftar Guru

**Index view**: `siswa/lms/guru.blade.php` · **Controller**: `LmsDashboardController@guru`

**Tampilan index**: Card **Daftar Guru Pengajar** dengan **grid card per guru** — tiap card berisi foto profil (atau inisial gradient), nama lengkap, badge mapel yang diajar, dan tombol kontak (WhatsApp dan Email bila ada). Hover effect translate.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Klik WhatsApp | `https://wa.me/{nomor}` | — | (link external) | — | Buka chat WhatsApp ke guru. |
| Klik Email | `mailto:{email}` | — | (link external) | — | Buka email client. |

**Catatan**: Read-only — data guru derived dari `GuruPengajarKelas` (kelas siswa). Tidak ada modify.

---

### Mata Pelajaran (Detail)

**Index view**: `siswa/lms/mata-pelajaran/show.blade.php` · **Controller**: `LmsMateriController@show` (middleware `siswa.mapel.access`)

**Tampilan index**: Breadcrumb (Dashboard LMS → {mapel}). Header gradient biru dengan nama mapel + nama guru. Section terbagi beberapa bagian (date-group expandable): **Daftar Materi** (file/link, filter tanggal upload), **Daftar Tugas Aktif**, **Daftar Latihan/Ujian Mendatang**, **Daftar Forum Diskusi Terbaru**. Tiap item bisa di-klik untuk masuk detail.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Lihat Materi | `siswa.lms.mapel.materi` | GET | `@lihatMateri` | `siswa/lms/mata-pelajaran/materi.blade.php` | Buka detail materi (preview/download file). Mark `MateriDibaca` (jika ada tracking). |
| Daftar Tugas (per mapel) | `siswa.lms.mapel.tugas.index` | GET | **method tidak ada — broken** | — | Route terdaftar (`web.php:1492`) tapi `LmsTugasController` tidak punya method `index` (hanya `show`/`submit`/`indexAll`). **Tidak ada link aktif dari mana pun di view siswa** — route ini orphan. Untuk daftar tugas per-mapel, siswa biasanya melihat dari section "Tugas" di halaman `show` ini, lalu klik per item ke `tugas.show`. |
| Detail Tugas | `siswa.lms.mapel.tugas.show` | GET | `LmsTugasController@show` | `siswa/lms/mata-pelajaran/tugas/show.blade.php` | Detail soal tugas + form submit. |
| Detail Ujian | `siswa.lms.mapel.ujian.show` | GET | `LmsUjianController@show` | `siswa/lms/mata-pelajaran/ujian/show.blade.php` | Start screen (info + tombol Mulai) atau result screen (nilai + tombol Kerjakan Ulang/Lihat Pembahasan). |
| Detail Latihan | `siswa.lms.mapel.latihan.show` | GET | `LmsUjianController@show` | `siswa/lms/mata-pelajaran/ujian/show_latihan.blade.php` | Sama dengan ujian, **view berbeda** (lebih ramah, tidak ada "validasi akses"). |
| Forum Mapel | `siswa.lms.mapel.forum.index` | GET | `LmsForumController@index` | `siswa/lms/mata-pelajaran/forum/index.blade.php` | List diskusi forum di mapel ini. |
| Meeting / Kelas Virtual | `siswa.lms.mapel.meeting.index` | GET | `SiswaLmsMeetingController@index` | `siswa/lms/meeting/index.blade.php` | List meeting yang dijadwalkan guru utk mapel ini. |

**Catatan**: Akses halaman ini dijaga **middleware `siswa.mapel.access`** (`CheckSiswaMapelAccess`) — siswa hanya boleh buka mapel yang ada di jadwal kelasnya. Mencoba mapel lain → 403.

---

### Submit Tugas

**Index view**: `siswa/lms/mata-pelajaran/tugas/show.blade.php` · **Controller**: `LmsTugasController.php`

**Tampilan index**: Header tugas (judul, tanggal mulai-deadline, badge status pribadi: Belum/Dikerjakan/Terlambat/Dinilai), deskripsi soal, lampiran soal (jika ada), form **submit jawaban** (text area + opsional upload file). Jika sudah submit & dinilai: tampil nilai + feedback guru. Bila `bisa_diulang`, tombol submit ulang sampai `batas_pengulangan`.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Submit Jawaban | `siswa.lms.mapel.tugas.submit` | POST | `@submit` | redirect ke `tugas.show` | Buat/update `TugasSiswa`: cek deadline → status `dikerjakan` atau `terlambat`; cek batas pengulangan bila `bisa_diulang`. Trigger notifikasi ke guru. |

**Catatan**: Setelah submit, status berubah menurut deadline. Guru kemudian mengoreksi (`guru.lms.tugas.koreksi.store` set status `dinilai`). Siswa melihat nilai+feedback **hanya bila** `tampilkan_nilai=true` di Tugas.

---

### Ujian & Latihan — DUA route group terpisah, satu controller

> **Penting**: Sama persis dengan pola Guru — Ujian dan Latihan adalah **dua menu user-facing terpisah** dengan URL berbeda, walau controller method dan banyak view di-share. Tabel berikut menjelaskan pemisahannya:
>
> | Aspek | Ujian | Latihan |
> |---|---|---|
> | **URL path** | `/siswa/lms/mata-pelajaran/{mapelId}/ujian/{ujianId}/...` | `/siswa/lms/mata-pelajaran/{mapelId}/latihan/{ujianId}/...` |
> | **Route group** | `Route::prefix('{mapelId}/ujian')->name('ujian.')` (`web.php:1498`) | `Route::prefix('{mapelId}/latihan')->name('latihan.')` (`web.php:1508`) |
> | **Route name** | `siswa.lms.mapel.ujian.*` | `siswa.lms.mapel.latihan.*` |
> | **Controller class** | `LmsUjianController` | `LmsUjianController` (sama) |
> | **Method body** | sama persis (`show`, `mulai`, `submit`, `retake`, `autosave`, `review`) | sama persis |
> | **View `show`** | `siswa/lms/mata-pelajaran/ujian/show.blade.php` | `siswa/lms/mata-pelajaran/ujian/show_latihan.blade.php` (**file berbeda**) |
> | **Detection di controller** | default | `if ($ujian->tipe_ujian === 'latihan')` → return view `show_latihan` |
> | **Validasi akses ujian** | Untuk PTS/PAS: butuh `validasi_ujian_bendahara=true` & `validasi_ujian_wali=true` | **Tidak butuh validasi** — latihan selalu bisa diakses |

Untuk **kedua route group**, sub-aksi sama:

| Tombol/Aksi | Route name (Ujian / Latihan) | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Mulai Mengerjakan | `siswa.lms.mapel.ujian.mulai` / `siswa.lms.mapel.latihan.mulai` | POST | `@mulai` | redirect ke `show` | Buat `UjianSiswa` status `sedang_mengerjakan`, set `waktu_mulai`. Untuk PTS/PAS cek validasi akses terlebih dahulu. |
| Submit Jawaban (selesai) | `*.ujian.submit` / `*.latihan.submit` | POST | `@submit` | redirect | Set status `selesai`, hitung nilai untuk soal pilgan/isian otomatis, sisakan essai untuk koreksi guru. |
| Autosave (durasi sedang mengerjakan) | `*.ujian.autosave` / `*.latihan.autosave` | POST | `@autosave` | JSON | Periodik save jawaban tiap N detik (JS) — mencegah hilang data bila browser crash. |
| Kerjakan Ulang | `*.ujian.retake` / `*.latihan.retake` | POST | `@retake` | redirect | Bila `bisa_diulang=true` & `batas_pengulangan` belum habis: buat `UjianSiswa` baru dengan `pengulangan_ke++`. Nilai akhir = `nilai_terbaik` di antara percobaan. |
| Lihat Pembahasan / Review | `*.ujian.review` / `*.latihan.review` | GET | `@review` | `siswa/lms/mata-pelajaran/ujian/review.blade.php` | Tampil semua soal + jawaban siswa + kunci jawaban + skor per soal. Hanya muncul bila `tampilkan_riwayat=true`. |

**Catatan Ujian**: Ujian besar (PTS/PAS) hanya bisa dikerjakan bila `validasi_ujian_*=true` (lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa)). Bila belum tervalidasi, halaman `show` menampilkan pesan "akses dikunci, hubungi wali/bendahara".

**Catatan Latihan**: Tidak butuh validasi akses — selalu bisa dikerjakan. View `show_latihan.blade.php` berbeda dari `show.blade.php` (UI lebih friendly, tanpa info "validasi"). Detection di controller `LmsUjianController@show` berdasar `$ujian->tipe_ujian === 'latihan'`.

---

### Forum Diskusi (per Mapel)

**Index view**: `siswa/lms/mata-pelajaran/forum/index.blade.php` · **Controller**: `LmsForumController.php`

**Tampilan index**: Card list diskusi — tiap item: avatar pengirim, judul + badge topik (Materi/Tugas/Ujian/Umum), meta (pengirim + waktu), excerpt isi, stats (jumlah balasan). Tombol **+ Buat Diskusi** (success) di header. Pagination.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Diskusi | `siswa.lms.mapel.forum.create` | GET | `@create` | (form inline atau view kosong) | Form buat diskusi baru. |
| Simpan Diskusi | `siswa.lms.mapel.forum.store` | POST | `@store` | redirect | Validasi + simpan `ForumDiskusi` (siswa sebagai pembuat). Tidak boleh bila forum di-close global oleh guru. |
| Detail Diskusi | `siswa.lms.mapel.forum.show` | GET | `@show` | `siswa/lms/mata-pelajaran/forum/show.blade.php` | Tampil thread + daftar reply (partial `reply-item-redesign.blade.php`) + form reply di bawah. |
| Balas | `siswa.lms.mapel.forum.reply` | POST | `@reply` | redirect | Simpan `ForumReply`. Tidak boleh bila forum closed oleh guru. |
| Edit Reply | `siswa.lms.mapel.forum.reply.update` | PUT | `@updateReply` | redirect | Update reply (hanya milik sendiri). |
| Hapus Reply | `siswa.lms.mapel.forum.reply.destroy` | DELETE | `@destroyReply` | redirect | Soft delete reply (hanya milik sendiri). |

**Catatan**: Siswa **TIDAK punya** route `togglePin`/`toggleClose`/`destroy` thread — itu hanya guru (`guru.lms.forum.togglePin/toggleClose/destroy`).

---

### Kelas Virtual / Meeting

**Index view**: `siswa/lms/meeting/index.blade.php` · **Controller**: `SiswaLmsMeetingController.php`

**Tampilan index**: Breadcrumb (Dashboard → Mapel → Kelas Virtual). Card **Daftar Kelas Virtual** dengan list meeting items — tiap item: badge platform (Zoom/Meet/lain), badge status (Akan Datang / Sedang Berlangsung / Selesai), judul, waktu (tanggal + jam), deskripsi. Tombol kanan kondisional: **Masuk Meeting** (primary, target blank — hanya muncul bila `is_active`) atau "Link Kedaluwarsa" (disabled).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Masuk Meeting (link external) | `{link_meeting}` | — | (link external) | — | Buka link Zoom/Meet/dll di tab baru. |

**Catatan**: Pure read-only — meeting dibuat oleh guru (`guru.lms.meeting.*`). Siswa hanya bisa lihat & klik link. Tidak ada modify/RSVP.
