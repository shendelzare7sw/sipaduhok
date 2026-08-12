# Role Menu Inventory

> Inventaris ini memetakan permukaan navigasi sidebar yang benar-benar dibentuk oleh Blade untuk 9 role SIPADUHOK. Dokumen ini adalah pelengkap `02-role-inventory.md`: **menu yang terlihat bukan definisi hak akses**. Hak akses tetap ditentukan oleh route middleware, controller, policy/guard, assignment, dan aturan bisnis.

## A. Scope dan Metode Baca

- Scope utama: item sidebar, heading, parent toggle, submenu, route target, badge, dan kondisi render.
- Layout Sneat menggunakan `layouts.sneat`; layout LMS Siswa menggunakan `layouts.lms`; layout LMS Guru menggunakan `layouts.lms-guru`.
- `route-name` menunjukkan named route yang menjadi tujuan klik.
- `dinamis` berarti jumlah item saat runtime bergantung pada data pengguna.
- Parent bertanda **toggle** hanya membuka submenu dan tidak mempunyai destination route.
- Navbar atas seperti lonceng notifikasi, Profil Saya, Pengaturan, dan Logout tidak dihitung sebagai sidebar.
- Satu route dapat muncul lebih dari sekali atau dirender berulang kali. Karena itu jumlah referensi statis tidak selalu sama dengan jumlah tombol yang terlihat.

## B. Source dan Coverage

| Kode | Role / Nilai Teknis | Layout / Konteks | Source Sidebar | Referensi `route()` Statis |
|---|---|---|---|---:|
| ADM | Admin / `admin` | Sneat | `resources/views/admin/partials/sneat-sidebar-menu.blade.php` | 37 |
| KET | Ketua PKBM / `ketua_pkbm` | Sneat | `resources/views/ketua/partials/sneat-sidebar-menu.blade.php` | 11 |
| WKA | Wakil Kepala Sekolah / `wakil_kepala_sekolah` | Sneat | `resources/views/waka/partials/sneat-sidebar-menu.blade.php` | 16 |
| SEK | Sekretaris / `sekretaris` | Sneat | `resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php` | 5 |
| BEN | Bendahara / `bendahara` | Sneat | `resources/views/bendahara/partials/sneat-sidebar-menu.blade.php` | 10 |
| WKL | Wali Kelas / `wali_kelas` | Sneat | `resources/views/wali-kelas/partials/sneat-sidebar-menu.blade.php` | 14 |
| GRU | Guru Pengajar / `guru_pengajar` | Sneat | `resources/views/guru/partials/sneat-sidebar-menu.blade.php` | 6 |
| GRU | Guru Pengajar / `guru_pengajar` | LMS Guru | `resources/views/guru/partials/sidebar-lms.blade.php` | 9 |
| GRU | Guru Pengajar / `guru_pengajar` | Notifikasi dalam LMS Guru | `resources/views/guru/partials/sidebar-lms-notif.blade.php` | 5 |
| SIS | Siswa / `siswa` | Sneat/SIA | `resources/views/siswa/partials/sneat-sidebar-sia.blade.php` | 4 |
| SIS | Siswa / `siswa` | LMS Siswa | `resources/views/siswa/partials/sidebar-lms.blade.php` | 6 |
| ORT | Orang Tua/Wali Siswa / `orang_tua` | Sneat | `resources/views/wali-siswa/partials/sneat-sidebar-menu.blade.php` | 4 |
| **Total** | **9 role** | **3 layout utama, 1 varian notifikasi** | **12 partial** | **127** |

Validasi source menemukan 118 named route unik dari 127 referensi statis dan semuanya terdaftar pada output `php artisan route:list --json`. Perulangan runtime pada kelas, mata pelajaran, dan anak tidak diekspansi menjadi jumlah data aktual.

## C. ADM - Admin

Layout: Sneat. Source: `resources/views/admin/partials/sneat-sidebar-menu.blade.php`.

### Menu tanpa heading

- Dashboard -> `admin.dashboard`

### Manajemen Konten

- Landing Page -> `admin.landing-pages.index`
- Konten Publikasi (**toggle**)
  - Kalender Akademik -> `admin.akademik.kalender.index`
  - Pengumuman -> `admin.akademik.pengumuman.index`
  - Flyer / Iklan -> `admin.akademik.flyer.index`
  - Kelola Berita -> `admin.akademik.berita.index`

### Manajemen Pengguna

- Kelola Data Pengguna (**toggle**)
  - Tenaga Pendidik -> `admin.users.tenaga-pendidik`
  - Siswa -> `admin.users.siswa`
  - Wali Siswa -> `admin.users.wali-siswa`
- Tiket Pemulihan Akun -> `admin.recovery-tickets.index`
  - Badge tampil jika terdapat tiket berstatus `pending_admin` atau `failed`.
- Pengaturan Sistem (**toggle**)
  - Pengaturan LMS -> `admin.lms-settings.index`
  - Pengaturan AI -> `admin.ai-settings.index`

### Data Master

- Tahun Ajaran -> `admin.tahun-ajaran.index`
- Manajemen Cabang -> `admin.cabang.index`

### Data Akademik

- Data Kelas & Penugasan (**toggle**)
  - Data Kelas -> `admin.kelas.index`
  - Data Wali Kelas -> `admin.wali-kelas.index`
  - Data Guru Pengajar -> `admin.guru-pengajar.index`
  - Manajemen Siswa -> `admin.manajemen-siswa.index`
- Mata Pelajaran -> `admin.mata-pelajaran.index`
- Jadwal Pelajaran -> `admin.jadwal-pelajaran.index`
  - Route `admin.pengaturan-istirahat.*` mengaktifkan highlight menu ini, tetapi tidak mempunyai item sidebar tersendiri.

### Keuangan

- Tagihan & Pembayaran (**toggle**)
  - Tagihan -> `admin.keuangan.tagihan.index`
  - Tarik Tunggakan -> `admin.keuangan.tagihan.carryover`
  - Pembayaran -> `admin.keuangan.pembayaran.index`
  - Config Pembayaran -> `admin.keuangan.info-pembayaran.index`
- Laporan Keuangan -> `admin.keuangan.laporan.index`

### Validasi & Dispensasi

- Validasi Ujian & Rapor -> `admin.keuangan.validasi-akses.index`
- Validasi Dispensasi -> `admin.keuangan.kenaikan-kelas.validation.index`

### Kenaikan Kelas

- Pengaturan Kenaikan (**toggle**)
  - Pengaturan KKM -> `admin.akademik.kenaikan-kelas.kkm.index`
  - Pengaturan Kenaikan -> `admin.akademik.kenaikan-kelas.settings.index`
- Proses & Rekap -> `admin.akademik.kenaikan-kelas.report`

### Monitoring & Analitik

- Monitoring Sistem (**toggle**)
  - Pengguna -> `admin.monitoring.pengguna`
  - Wali Kelas -> `admin.monitoring.wali-kelas`
  - Guru Pengajar -> `admin.monitoring.guru-pengajar`
  - Siswa -> `admin.monitoring.siswa`
  - Monitoring LMS -> `admin.monitoring.lms.index`
- Laporan & Catatan (**toggle**)
  - Laporan -> `admin.laporan.index`
  - Catatan -> `admin.catatan.index`

Catatan: bypass Admin pada middleware role membuat hak akses Admin lebih luas daripada daftar sidebar ini. Sidebar Admin tidak otomatis menampilkan semua menu milik role lain.

## D. KET - Ketua PKBM

Layout: Sneat. Source: `resources/views/ketua/partials/sneat-sidebar-menu.blade.php`.

### Menu tanpa heading

- Dashboard -> `ketua.dashboard`

### Persetujuan & Validasi

- Approval Dispensasi -> `ketua.kenaikan-kelas.approval.index`
- Validasi Rapor -> `ketua.validasi-rapor.index`
- Dispensasi Keuangan -> `ketua.dispensasi.index`
  - Badge tampil berdasarkan jumlah `PengajuanRaporKetua` berstatus `menunggu`.

### Monitoring

- Monitoring Sistem (**toggle**)
  - Data Pengguna -> `ketua.monitoring.pengguna`
  - Data Wali Kelas -> `ketua.monitoring.wali-kelas`
  - Data Guru Pengajar -> `ketua.monitoring.guru-pengajar`
  - Data Siswa -> `ketua.monitoring.siswa`
  - Monitoring LMS -> `ketua.monitoring.lms.index`

### Laporan & Komunikasi

- Laporan & Catatan (**toggle**)
  - Cetak Laporan -> `ketua.laporan.index`
  - Kirim Catatan -> `ketua.catatan.index`

## E. WKA - Wakil Kepala Sekolah

Layout: Sneat. Source: `resources/views/waka/partials/sneat-sidebar-menu.blade.php`.

### Menu tanpa heading

- Dashboard -> `waka.dashboard`

### Manajemen Akademik

- Tahun Ajaran -> `waka.tahun-ajaran.index`

### Data Akademik

- Data Kelas & Penugasan (**toggle**)
  - Data Kelas -> `waka.kelas.index`
  - Data Wali Kelas -> `waka.wali-kelas.index`
  - Data Guru Pengajar -> `waka.guru-pengajar.index`
  - Manajemen Siswa -> `waka.manajemen-siswa.index`
- Mata Pelajaran -> `waka.mata-pelajaran.index`
- Jadwal Pelajaran -> `waka.jadwal-pelajaran.index`

### Kenaikan Kelas

- Pengaturan Kenaikan (**toggle**)
  - Pengaturan KKM -> `waka.kenaikan-kelas.kkm.index`
  - Pengaturan Kenaikan -> `waka.kenaikan-kelas.settings.index`
- Proses & Rekap -> `waka.kenaikan-kelas.report`

### Monitoring & Analitik

- Monitoring Sistem (**toggle**)
  - Wali Kelas -> `waka.monitoring.wali-kelas`
  - Guru Pengajar -> `waka.monitoring.guru-pengajar`
  - Siswa -> `waka.monitoring.siswa`
  - Monitoring LMS -> `waka.monitoring.lms.index`

### Komunikasi

- Catatan -> `waka.catatan.index`

Catatan: view pengaturan jam istirahat Waka memakai sidebar ini, tetapi tidak ada item `Pengaturan Istirahat` tersendiri pada partial.

## F. SEK - Sekretaris

Layout: Sneat. Source: `resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php`.

### Menu tanpa heading

- Dashboard -> `sekretaris.dashboard`

### Manajemen Konten

- Konten Publikasi (**toggle**)
  - Kalender Akademik -> `sekretaris.kalender.index`
  - Pengumuman -> `sekretaris.pengumuman.index`
  - Flyer / Iklan -> `sekretaris.flyer.index`
  - Kelola Berita -> `sekretaris.berita.index`

## G. BEN - Bendahara

Layout: Sneat. Source: `resources/views/bendahara/partials/sneat-sidebar-menu.blade.php`.

### Menu tanpa heading

- Dashboard -> `bendahara.dashboard`

### Keuangan

- Tagihan & Pembayaran (**toggle**)
  - Kelola Tagihan -> `bendahara.tagihan.index`
  - Tarik Tunggakan -> `bendahara.tagihan.carryover`
  - Kelola Pembayaran -> `bendahara.pembayaran.index`
  - Config Pembayaran -> `bendahara.info-pembayaran.index`

### Validasi & Dispensasi

- Validasi Ujian & Rapor -> `bendahara.validasi-akses.index`
- Validasi Dispensasi -> `bendahara.kenaikan-kelas.validation.index`

### Laporan

- Laporan Keuangan (**toggle**)
  - Laporan Pembayaran -> `bendahara.laporan.index`
  - Rekap Tagihan -> `bendahara.laporan.rekap-tagihan`
  - Siswa Belum Lunas -> `bendahara.laporan.belum-lunas`

## H. WKL - Wali Kelas

Layout: Sneat. Source: `resources/views/wali-kelas/partials/sneat-sidebar-menu.blade.php`.

### Konteks kelas dinamis

- Kartu **Kelas Aktif** hanya tampil ketika Wali mempunyai lebih dari satu assignment kelas pada tahun ajaran aktif dan kelas terpilih masih valid.
  - Ganti Kelas -> `wali.pilih-kelas`
- Dashboard -> `wali.dashboard`
- Pilih Kelas -> `wali.pilih-kelas`
  - Item ini hanya tampil jika Wali mempunyai lebih dari satu kelas aktif.

### Akademik

- Jadwal Pelajaran -> `wali.jadwal.index`
- Kelola Presensi (**toggle**)
  - Input Harian -> `wali.presensi.index`
  - Validasi Izin -> `wali.presensi.validasi-izin`
  - Rekap Harian -> `wali.presensi.rekap-harian`
  - Riwayat & Edit -> `wali.presensi.riwayat`
- Kelola Rapor (**toggle**)
  - Nilai Siswa -> `wali.nilai.index`
  - Kelola Rapor -> `wali.rapor.index`
  - Arsip Kelas Saya -> `wali.arsip.index`
  - Permintaan Unduh -> `wali.rapor.request-download.index`
    - Badge tampil jika terdapat request berstatus `menunggu` untuk kelas pada session.

### Kenaikan Kelas

- Prediksi Kenaikan -> `wali.kenaikan-kelas.prediction`

### Validasi

- Validasi Akses -> `wali.validasi-akses.index`

Catatan implementasi: pemilihan kelas menyimpan session `wali_kelas_selected`, sedangkan query badge Permintaan Unduh membaca `selected_kelas_id`. Perbedaan key ini perlu diverifikasi karena dapat membuat badge tidak merepresentasikan kelas terpilih.

## I. GRU - Guru Pengajar

Guru mempunyai dua layout utama dan satu sidebar khusus ketika membuka notifikasi dari konteks LMS.

### I.1 Sneat / Dashboard Guru

Source: `resources/views/guru/partials/sneat-sidebar-menu.blade.php`.

- Dashboard -> `guru.dashboard`

#### Akademik

- Informasi Akademik (**toggle**)
  - Jadwal Mengajar -> `guru.jadwal.index`
  - Semua Kelas -> `guru.kelas.index`

#### Pembelajaran

- Arsip LMS -> `guru.lms.arsip.index`
- Catatan Monitoring -> `guru.lms.catatan-monitoring.index`
  - Badge tampil berdasarkan jumlah catatan monitoring Guru yang belum dibaca.

#### Kelas Saya (Akses Cepat) - dinamis

Heading dan daftar hanya tampil jika `$sidebarKelas` tersedia dan tidak kosong.

- Untuk setiap kelas yang diampu: `{nama_kelas}` (**toggle**).
  - Untuk setiap assignment mata pelajaran valid: `{nama_mapel}` -> `guru.lms.dashboard` dengan parameter `{kelasId}` dan `{mapelId}`.

### I.2 LMS Guru / HOK Teaching

Layout: `layouts.lms-guru`. Source: `resources/views/guru/partials/sidebar-lms.blade.php`.

Sidebar diawali kartu konteks `Anda Mengajar: {mata pelajaran}, Kelas {kelas}`.

#### Utama

- Beranda -> `guru.lms.dashboard`

#### Pembelajaran

- Materi -> `guru.lms.materi.index`
- Tugas -> `guru.lms.tugas.index`
  - Badge tampil jika `$tugasBelumDikoreksi > 0`.
- Latihan -> `guru.lms.latihan.index`
- Ujian -> `guru.lms.ujian.index`
- Forum Diskusi -> `guru.lms.forum.index`
- Kelas Virtual -> `guru.lms.meeting.index`

#### Penilaian

- Nilai Siswa -> `guru.lms.nilai.index`

#### Navigasi

- Kembali ke Dashboard -> `guru.dashboard`

Semua target pembelajaran membawa parameter kelas dan mata pelajaran yang sedang dibuka.

### I.3 Notifikasi dalam konteks LMS Guru

Layout tetap `layouts.lms-guru`. Source: `resources/views/guru/partials/sidebar-lms-notif.blade.php`. Varian ini dipakai pada halaman notifikasi ketika query context bernilai `ctx=lms-guru`, karena halaman notifikasi tidak mempunyai objek `$kelas` dan `$mapel`.

#### Notifikasi

- Semua Notifikasi -> `notifications.index?ctx=lms-guru`
- Belum Dibaca -> `notifications.index?ctx=lms-guru&filter=unread`
- Sudah Dibaca -> `notifications.index?ctx=lms-guru&filter=read`

#### Navigasi

- Dashboard Guru -> `guru.dashboard`
- Daftar Kelas LMS -> `guru.kelas.index`

## J. SIS - Siswa

Siswa mempunyai dua layout: SIA/Sneat dan LMS Siswa.

### J.1 Sneat / SIA Siswa

Layout: `layouts.sneat`. Source aktual: `resources/views/siswa/partials/sneat-sidebar-sia.blade.php`.

- Dashboard SIA -> `siswa.sia.dashboard`

#### Learning Management - kondisional

- HOK-LMS -> `siswa.lms.dashboard`

Heading dan menu HOK-LMS hanya tampil jika:

1. data Siswa ditemukan untuk user login;
2. Siswa mempunyai kelas;
3. jenjang kelas terdapat pada setting `lms_allowed_jenjang`.

#### Akademik

- Presensi -> `siswa.sia.presensi.index`
- Data Penilaian -> `siswa.sia.penilaian`

Catatan source menyatakan menu Rapor dan Pembayaran dipindahkan ke akses Wali Siswa. Route/view pembayaran Siswa masih aktif untuk melihat tagihan, riwayat, dan mencetak bukti, tetapi `SiaPembayaranController::prosesBayar()` selalu menolak aksi bayar dan mengarahkan ke Wali Siswa. Route rapor Siswa dinonaktifkan. Keduanya tidak dipaparkan oleh sidebar Siswa.

### J.2 LMS Siswa / HOK Learning

Layout: `layouts.lms`. Source: `resources/views/siswa/partials/sidebar-lms.blade.php`.

- Kembali ke SIA -> `siswa.sia.dashboard`

#### Beranda

- Beranda -> `siswa.lms.dashboard`

#### Mata Pelajaran - dinamis

- Untuk setiap mata pelajaran unik dari jadwal kelas yang lolos `Siswa::canAccessMapel()`: `{nama_mapel}` -> `siswa.lms.mapel.show` dengan parameter `{mapelId}`.
- Jika tidak ada mata pelajaran, tampil item nonaktif `Belum ada mata pelajaran` tanpa route.

#### Akademik

- Kalender Akademik -> `siswa.lms.kalender`
- Jadwal Pelajaran -> `siswa.lms.jadwal`
- Daftar Guru -> `siswa.lms.guru`

Halaman notifikasi dengan `ctx=lms` memakai sidebar LMS Siswa yang sama; tidak ada partial notifikasi khusus Siswa.

## K. ORT - Orang Tua / Wali Siswa

Layout: Sneat. Source: `resources/views/wali-siswa/partials/sneat-sidebar-menu.blade.php`.

- Dashboard -> `wali-siswa.dashboard`

### Monitoring Anak - dinamis

Jika user mempunyai anak tertaut, untuk setiap anak ditampilkan parent toggle dengan label nama anak yang dipotong maksimal 20 karakter:

- `{nama anak}` (**toggle**)
  - Presensi -> `wali-siswa.presensi.anak` dengan parameter `{siswaId}`
  - Tagihan -> `wali-siswa.tagihan.anak` dengan parameter `{siswaId}`
  - Rapor -> `wali-siswa.rapor.anak` dengan parameter `{siswaId}`

Jika user tidak mempunyai anak tertaut, sidebar menampilkan item nonaktif `Belum Ada Data Anak` tanpa route.

## L. Menu Bersama di Luar Sidebar

Bagian ini tidak masuk hitungan 127 referensi sidebar, tetapi dicatat agar istilah "full menu" tidak ambigu.

- Layout Sneat untuk seluruh role menyediakan lonceng notifikasi pada navbar.
- Dropdown akun Sneat menyediakan Profil Saya, Pengaturan, dan Logout.
- Layout LMS Siswa menyediakan lonceng notifikasi dan dropdown Profil Saya, Pengaturan, Kembali ke SIA, dan Logout.
- Layout LMS Guru menyediakan lonceng notifikasi dan dropdown Profil Saya, Pengaturan, Kembali ke Dashboard, dan Logout.
- Halaman Profil dan Pengaturan memetakan kembali kesembilan nilai role ke partial sidebar masing-masing.
- Halaman notifikasi memilih layout/sidebar berdasarkan role dan query context `ctx`.

## M. Temuan Navigasi yang Perlu Diperhatikan

1. **Menu tidak sama dengan permission.** Admin mempunyai bypass route role lain, sedangkan sidebar Admin hanya menampilkan menu Admin.
2. **Guru mempunyai tiga surface sidebar.** Sneat, LMS Guru, dan varian notifikasi LMS tidak tampil bersamaan.
3. **Siswa mempunyai dua surface sidebar.** File Sneat-nya bernama `sneat-sidebar-sia.blade.php`, bukan `sneat-sidebar-menu.blade.php`.
4. **Menu dinamis tidak mempunyai jumlah tetap.** Quick access Guru bergantung assignment kelas/mapel; LMS Siswa bergantung jadwal dan filter akses mapel; Orang Tua bergantung anak tertaut.
5. **Rapor dan Pembayaran tidak terlihat pada sidebar Siswa.** UI mengarahkan fungsi tersebut ke Orang Tua/Wali Siswa meskipun beberapa view SIA lama masih ada.
6. **Pengaturan istirahat tidak mempunyai item tersendiri.** Pada Admin ia menggunakan highlight Jadwal Pelajaran; pada Waka view-nya memakai sidebar yang sama tanpa item eksplisit.
7. **Badge Wali Kelas perlu verifikasi session key.** Selection menggunakan `wali_kelas_selected`, sedangkan badge request unduh menggunakan `selected_kelas_id`.

## N. Traceability

| Area | Sumber Utama |
|---|---|
| Layout Sneat | `resources/views/layouts/sneat.blade.php` |
| Layout LMS Siswa | `resources/views/layouts/lms.blade.php` |
| Layout LMS Guru | `resources/views/layouts/lms-guru.blade.php` |
| Sidebar per role | 12 partial pada tabel Section B |
| Pemilihan sidebar Profil/Akun | `resources/views/profile/index.blade.php`; `resources/views/account/settings.blade.php` |
| Pemilihan sidebar Notifikasi | `resources/views/notifications/index.blade.php`; `resources/views/notifications/show.blade.php` |
| Definisi role | `database/seeders/RoleSeeder.php`; `app/Models/User.php` |
| Named route | `php artisan route:list --json`; `routes/web.php` |

Baseline discovery: branch `latihan-sidang`, commit `c813efea73a20eb721b39cc3c9a56ea39106a75b`, diperiksa 2026-08-12.
