# Role: Admin

> Kembali ke [flow.md](../../flow.md) · Role `admin` · Level 1 · Prefix `/admin` · Route `admin.` · Middleware `role:admin` (Admin **bypass** semua pengecekan role lain).

## Ringkasan Peran

Super-user. Mengelola **seluruh** konfigurasi & data: pengguna, akademik, keuangan, konten landing page, monitoring, dan menyalin (mirror) hampir semua fitur Bendahara, Sekretaris, Waka, dan Ketua agar Admin bisa mengerjakan/menambal apa pun. Banyak menu Admin memakai controller yang sama dengan role lain (mis. `Admin\Akademik\PromotionReportController`, `Admin\Akademik\AkademikController`).

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/admin/partials/sneat-sidebar-menu.blade.php`.
- Badge: "Tiket Pemulihan Akun" menampilkan jumlah `RecoveryTicket` status `pending_admin|failed`.

## Peta Menu

Submenu CRUD rutin (index/create/store/edit/update/destroy) + import/template/print diringkas; route `index` jadi acuan.

| Grup | Menu | Route (index) | Controller | Model | View dir |
|---|---|---|---|---|---|
| Konten | Landing Page | `admin.landing-pages.index` | `Admin\LandingPage\LandingPageController` | `LandingPage` | `admin/landing-pages` |
| Pengguna | Manajemen User → Tenaga Pendidik / Siswa / Wali Murid | `admin.users.tenaga-pendidik` · `admin.users.siswa` · `admin.users.orang-tua` | `Admin\UserController` | `User`,`TenagaPendidik`,`Siswa`,`StudentParent` | `admin/users` |
| Pengguna | Tiket Pemulihan Akun | `admin.recovery-tickets.index` | `Admin\AdminRecoveryTicketController` | `RecoveryTicket` | `admin/recovery-tickets` |
| Pengguna | Pengaturan LMS | `admin.lms-settings.index` | `Admin\LmsSettingController` | `AppSetting` | `admin/lms-settings` |
| Pengguna | Pengaturan AI | `admin.ai-settings.index` | `Admin\AiSettingController` | `AppSetting` | `admin/ai-settings` |
| Pengguna | Tahun Ajaran | `admin.tahun-ajaran.index` | `Admin\TahunAjaranController` (resource) | `TahunAjaran` | `admin/tahun-ajaran` |
| Pengguna | Manajemen Cabang | `admin.cabang.index` | `Admin\CabangController` (resource) | `Cabang` | `admin/cabang` |
| Akademik | Data Kelas | `admin.kelas.index` | `Admin\KelasController` (resource) | `Kelas` | `admin/kelas` |
| Akademik | Data Wali Kelas | `admin.wali-kelas.index` | `Admin\WaliKelasController` | `WaliKelasAssignment`,`Kelas` | `admin/wali-kelas` |
| Akademik | Data Guru Pengajar | `admin.guru-pengajar.index` | `Admin\GuruPengajarController` | `GuruPengajarKelas` | `admin/guru-pengajar` |
| Akademik | Mata Pelajaran | `admin.mata-pelajaran.index` | `Admin\MataPelajaranController` (resource) | `MataPelajaran` | `admin/mata-pelajaran` |
| Akademik | Jadwal Pelajaran | `admin.jadwal-pelajaran.index` | `Admin\JadwalPelajaranController` | `JadwalPelajaran`,`PengaturanIstirahat` | `admin/jadwal-pelajaran` |
| Akademik | Manajemen Siswa | `admin.manajemen-siswa.index` | `Admin\ManajemenSiswaController` | `Siswa`,`StudentParent` | `admin/manajemen-siswa` |
| Keuangan | Tagihan | `admin.keuangan.tagihan.index` | `Admin\Keuangan\TagihanController` | `Tagihan` | `admin/keuangan/tagihan` |
| Keuangan | Tarik Tunggakan | `admin.keuangan.tagihan.carryover` | `Admin\Keuangan\TagihanController@carryoverIndex` | `Tagihan` | — |
| Keuangan | Pembayaran | `admin.keuangan.pembayaran.index` | `Admin\Keuangan\PembayaranController` | `Pembayaran` | `admin/keuangan/pembayaran` |
| Keuangan | Config Pembayaran | `admin.keuangan.info-pembayaran.index` | `Admin\Keuangan\InfoPembayaranController` | `InfoPembayaran`,`AppSetting` | `admin/keuangan` |
| Keuangan | Laporan Keuangan | `admin.keuangan.laporan.index` | `Admin\Keuangan\LaporanPembayaranController` | `Pembayaran`,`Tagihan` | `admin/keuangan/laporan` |
| Validasi Akses | Validasi Ujian & Rapor | `admin.keuangan.validasi-akses.index` | `Admin\Keuangan\ValidasiAksesController` | `Siswa`,`PengaturanBatasPembayaran` | `admin/keuangan/validasi-akses` |
| Kenaikan Kelas | Validasi Dispensasi | `admin.keuangan.promotion.validation.index` | `Admin\Keuangan\PromotionValidationController` | tbl `izin_naik_kelas_khusus` (DB::table) | — |
| Kenaikan Kelas | Pengaturan KKM | `admin.akademik.promotion.kkm.index` | `Admin\Akademik\PromotionKKMController` | tbl `pengaturan_kkm` (DB::table) | — |
| Kenaikan Kelas | Pengaturan Kenaikan | `admin.akademik.promotion.settings.index` | `Admin\Akademik\PromotionSettingsController` | tbl `pengaturan_naik_kelas` (DB::table) | — |
| Kenaikan Kelas | Proses & Rekap | `admin.akademik.promotion.report` | `Admin\Akademik\PromotionReportController` | `StatusNaikKelasSiswa`,`PromotionSchedule` | — |
| Akademik | Kalender / Pengumuman / Flyer / Berita | `admin.akademik.{kalender,pengumuman,flyer,berita}.index` | `Admin\Akademik\AkademikController` | `KalenderAkademik`,`Pengumuman`,`Berita` | `admin/akademik` |
| Monitoring | Monitoring → Pengguna/Wali/Guru/Siswa/LMS | `admin.monitoring.{pengguna,wali-kelas,guru-pengajar,siswa}` · `admin.monitoring.lms.index` | `Admin\MonitoringController` | `User`,`Siswa`,`CatatanMonitoring` | `monitoring-lms`, `admin/monitoring` |
| Monitoring | Laporan | `admin.laporan.index` | `Admin\MonitoringController@index` | berbagai | `admin/laporan` |
| Monitoring | Catatan | `admin.catatan.index` | `Admin\MonitoringController@catatanIndex` | `Catatan`,`CatatanDibaca` | `admin/catatan` |

> Tidak di sidebar tapi ada route: `admin.cetak-laporan.*` (`Admin\CetakLaporanController`), `admin.google-sheets.*` (disembunyikan), `admin.pengaturan-istirahat.*`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Validasi Ujian & Rapor** & **Validasi Dispensasi**: Admin adalah *mirror* dari fitur Bendahara. Lihat alur penuh di [flow.md §5.1–5.4](../../flow.md#5-peta-alur-lintas-role-bagian-terpenting). `ajukanDispensasi` Admin (`admin.keuangan.validasi-akses.dispensasi`) membuat `PengajuanRaporKetua` yang diputuskan **Ketua PKBM**.
- **Proses & Rekap (Kenaikan Kelas)**: titik eksekusi promosi tahunan. `@execute` memindah siswa ke kelas tujuan berdasarkan tabel `pengaturan_kkm` + `pengaturan_naik_kelas` + flag `izin_khusus_ketua`. Ada `rollback`/`promote-selected`/`cancel-schedule`. Controller ini **dipakai bersama** Waka (`waka.promotion.*`).
- **Manajemen User**: import Excel (`maatwebsite/excel`) + generator akun untuk Tenaga Pendidik, Siswa, Orang Tua. Relasi orang tua↔siswa lewat `StudentParent`.
- **Tiket Pemulihan Akun**: alur reset password via WhatsApp/link (`RecoveryTicket`). Admin me-resolve/reject tiket; ada juga jalur publik `user.recovery` & `admin.recovery`.
- **Manajemen Cabang & Tahun Ajaran**: data fondasi — hampir semua data akademik terikat `cabang_id` & `tahun_ajaran_id`. Mengubah TA aktif berdampak ke seluruh sistem.

## Menu Lintas-Role (controller/fitur dibagikan)

| Fitur | Admin | Role lain dengan fitur sama |
|---|---|---|
| Validasi akses/dispensasi | `admin.keuangan.validasi-akses.*`, `admin.keuangan.promotion.validation.*` | Bendahara `bendahara.*`, Wali `wali.validasi-akses.*` |
| Kenaikan kelas (KKM/settings/report) | `admin.akademik.promotion.*` | Waka `waka.promotion.*` (controller sama) |
| Kalender/Pengumuman/Flyer/Berita | `admin.akademik.*` | Sekretaris `sekretaris.*` |
| Monitoring & Laporan & Catatan | `admin.monitoring.*`, `admin.laporan.*`, `admin.catatan.*` | Ketua `ketua.*`, Waka `waka.monitoring.*` |
| Data Akademik (kelas, mapel, jadwal, siswa, tahun ajaran) | `admin.*` | Waka `waka.*` |

## Detail Sub-Halaman per Menu

> Setiap menu di sidebar Admin yang punya **halaman/aksi selain index** dirinci di sini: tombol yang muncul di index, route yang dipanggil, controller method, view file, dan ringkasan logika. Kolom **Catatan** menjelaskan jika controller/view di-share dengan role lain.
>
> Convention: semua route name di-prefix `admin.`; view path di tabel relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/`.

### Landing Page

**Index view**: `admin/landing-pages/index.blade.php` · **Controller**: `Admin/LandingPage/LandingPageController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Edit halaman | `admin.landing-pages.edit` | GET | `@edit` | `admin/landing-pages/edit.blade.php` | Render form editor section (hero, layanan, statistik, dsb.) untuk slug landing tertentu. Halaman pre-seed; tidak ada create. |
| Simpan (submit form edit) | `admin.landing-pages.update` | PUT | `@update` | redirect | Validasi & simpan `LandingPage` + relasi `LandingPageSection`. Set/refresh `updated_at`. |
| Reset ke default | `admin.landing-pages.reset` | GET | `@reset` | redirect | Hapus override → kembalikan ke konten seeder default. |

**Catatan**: View `admin/landing-pages/partials/item-card.blade.php` adalah partial reusable untuk render kartu di form edit (bukan halaman). Tidak ada role lain yang punya menu ini — admin-only.

---

### Manajemen User → Tenaga Pendidik

**Index view**: `admin/users/tenaga-pendidik.blade.php` (dipanggil dari `admin/users/index.blade.php` tab) · **Controller**: `Admin/UserController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.users.create-tenaga-pendidik` | GET | `@createTenagaPendidik` | `admin/users/tenaga-pendidik-create.blade.php` | Form: nama, email, role (guru/wali/bendahara/dst), cabang. |
| Simpan | `admin.users.store-tenaga-pendidik` | POST | `@storeTenagaPendidik` | redirect | Validasi unik email + buat `User` & `TenagaPendidik`, generate password & kirim ke WA. |
| Edit | `admin.users.edit-tenaga-pendidik` | GET | `@editTenagaPendidik` | `admin/users/tenaga-pendidik-edit.blade.php` | Form pre-fill biodata + role. |
| Update | `admin.users.update-tenaga-pendidik` | PUT | `@updateTenagaPendidik` | redirect | Validasi + update; optional reset password. |
| Detail | `admin.users.show-tenaga-pendidik` | GET | `@showTenagaPendidik` | `admin/users/tenaga-pendidik-show.blade.php` | Profil lengkap + relasi (kelas diampu, dsb). |
| Hapus | `admin.users.delete-tenaga-pendidik` | DELETE | `@deleteTenagaPendidik` | redirect | Soft delete user (cek dependency wali/jadwal). |
| Hapus terpilih | `admin.users.bulk-delete-tenaga-pendidik` | POST | `@bulkDeleteTenagaPendidik` | redirect | Bulk delete dari checkbox list. |
| Import Excel | `admin.users.import-tenaga-pendidik` / `.store` | GET/POST | `@importTenagaPendidikForm` / `@importTenagaPendidik` | `admin/users/tenaga-pendidik-import.blade.php` | Upload `.xlsx`, parse via Maatwebsite, generate akun batch. |
| Download Template | `admin.users.tenaga-pendidik-template` | GET | `@downloadTenagaPendidikTemplate` | file download | Template `.xlsx` untuk import. |
| Cetak | `admin.users.tenaga-pendidik.print` | GET | `@printTenagaPendidik` | `admin/users/print/tenaga-pendidik.blade.php` | Render layout cetak (tabel daftar). |

### Manajemen User → Siswa

**Index view**: `admin/users/siswa.blade.php` · **Controller**: `Admin/UserController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.users.create-siswa` | GET | `@createSiswa` | `admin/users/siswa-create.blade.php` | Form: NISN, nama, cabang, kelas, orang tua. |
| Simpan | `admin.users.store-siswa` | POST | `@storeSiswa` | redirect | Validasi NISN unik + buat `User`+`Siswa`, optional attach `StudentParent`. |
| Edit | `admin.users.edit-siswa` | GET | `@editSiswa` | `admin/users/siswa-edit.blade.php` | Form pre-fill + manage relasi orang tua. |
| Update | `admin.users.update-siswa` | PUT | `@updateSiswa` | redirect | Validasi + update siswa & relasi. |
| Detail | `admin.users.show-siswa` | GET | `@showSiswa` | `admin/users/siswa-show.blade.php` | Profil + riwayat kelas + orang tua. |
| Hapus | `admin.users.delete-siswa` | DELETE | `@deleteSiswa` | redirect | Soft delete (cek tagihan/nilai aktif). |
| Hapus terpilih | `admin.users.bulk-delete-siswa` | POST | `@bulkDeleteSiswa` | redirect | Bulk delete. |
| Import Excel | `admin.users.import-siswa` / `.store` | GET/POST | `@importSiswaForm` / `@importSiswa` | `admin/users/siswa-import.blade.php` | Upload `.xlsx`, parse & buat siswa batch. |
| Download Template | `admin.users.siswa-template` | GET | `@downloadSiswaTemplate` | file download | Template `.xlsx`. |
| Cetak | `admin.users.siswa.print` | GET | `@printSiswa` | `admin/users/print/siswa.blade.php` | Layout cetak. |

### Manajemen User → Wali Murid (Orang Tua)

**Index view**: `admin/users/orang-tua.blade.php` · **Controller**: `Admin/UserController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.users.orang-tua.create` | GET | `@createOrangTua` | `admin/users/orang-tua-create.blade.php` | Form: nama, WhatsApp, attach anak (Siswa). |
| Simpan | `admin.users.orang-tua.store` | POST | `@storeOrangTua` | redirect | Buat `User` (role orang_tua) + `StudentParent` link. |
| Edit | `admin.users.edit-orang-tua` | GET | `@editOrangTua` | `admin/users/orang-tua-edit.blade.php` | Form pre-fill. |
| Update | `admin.users.update-orang-tua` | PUT | `@updateOrangTua` | redirect | Update biodata. |
| Detail | `admin.users.show-orang-tua` | GET | `@showOrangTua` | `admin/users/orang-tua-show.blade.php` | Profil + daftar anak. |
| Toggle status aktif | `admin.users.toggle-orang-tua-status` | POST | `@toggleOrangTuaStatus` | redirect | Aktif/non-aktifkan akun (lock login). |
| Hapus | `admin.users.delete-orang-tua` | DELETE | `@deleteOrangTua` | redirect | Soft delete. |
| Hapus terpilih | `admin.users.bulk-delete-orang-tua` | POST | `@bulkDeleteOrangTua` | redirect | Bulk delete. |
| Import Excel | `admin.users.import-orang-tua` / `.store` | GET/POST | `@importOrangTuaForm` / `@importOrangTua` | `admin/users/orang-tua-import.blade.php` | Upload & parse `.xlsx`. |
| Download Template | `admin.users.orang-tua-template` | GET | `@downloadOrangTuaTemplate` | file download | Template `.xlsx`. |
| Cetak | `admin.users.orang-tua.print` | GET | `@printOrangTua` | `admin/users/print/orang-tua.blade.php` | Layout cetak. |

**Catatan (manajemen user)**: 3 sub-tab di atas dipisah jadi 3 view berbeda tapi controller-nya satu (`UserController`). Tidak ada role lain dengan menu identik — admin-only super-user.

---

### Tiket Pemulihan Akun

**Index view**: `admin/recovery-tickets/index.blade.php` · **Controller**: `Admin/AdminRecoveryTicketController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Resend WhatsApp | `admin.recovery-tickets.resend` | POST | `@resend` | redirect | Kirim ulang link reset ke WA siswa/wali. |
| Resolve (selesai) | `admin.recovery-tickets.resolve` | POST | `@resolve` | redirect | Tandai tiket selesai + log admin. |
| Reject | `admin.recovery-tickets.reject` | POST | `@reject` | redirect | Tolak tiket (alasan opsional). |
| Resolve massal | `admin.recovery-tickets.bulk-resolve` | POST | `@bulkResolve` | redirect | Bulk action dari checkbox. |
| Reject massal | `admin.recovery-tickets.bulk-reject` | POST | `@bulkReject` | redirect | Bulk reject. |
| Update WA admin | `admin.recovery-tickets.update-admin-wa` | POST | `@updateAdminWa` | redirect | Set nomor WA admin penerima notifikasi tiket. |
| Riwayat | `admin.recovery-tickets.history` | GET | `@history` | `admin/recovery-tickets/history.blade.php` | Daftar tiket resolved/rejected dengan filter. |

**Catatan**: Menu admin-only. Alur publik (siswa minta reset) ada di route `user.recovery` & `admin.recovery` (di luar prefix `admin/`).

---

### Pengaturan AI

**Index view**: `admin/ai-settings/index.blade.php` · **Controller**: `Admin/AiSettingController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Simpan pengaturan | `admin.ai-settings.update` | PUT | `@update` | redirect | Simpan API key / model AI ke `AppSetting`. |
| Test koneksi | `admin.ai-settings.test` | POST | `@testConnection` | JSON | Ping endpoint AI untuk validasi credential. |

**Catatan**: Halaman single-page (tidak ada create/edit/show terpisah). Admin-only.

---

### Pengaturan LMS

**Index view**: `admin/lms-settings/index.blade.php` · **Controller**: `Admin/LmsSettingController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Simpan pengaturan | `admin.lms-settings.update` | PUT | `@update` | redirect | Update daftar `allowed_jenjang` LMS (TK, SD, SMP, SMA, dst.) di `AppSetting`. |

**Catatan**: Single-page form. Admin-only.

---

### Tahun Ajaran

**Index view**: `admin/tahun-ajaran/index.blade.php` · **Controller**: `Admin/TahunAjaranController.php` (Laravel `resource`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.tahun-ajaran.create` | GET | `@create` | `admin/tahun-ajaran/create.blade.php` | Form: nama TA, tanggal mulai-selesai, semester. |
| Simpan | `admin.tahun-ajaran.store` | POST | `@store` | redirect | Validasi tidak overlap + buat `TahunAjaran`. |
| Edit | `admin.tahun-ajaran.edit` | GET | `@edit` | `admin/tahun-ajaran/edit.blade.php` | Form pre-fill. |
| Update | `admin.tahun-ajaran.update` | PUT | `@update` | redirect | Validasi & update. |
| Detail | `admin.tahun-ajaran.show` | GET | `@show` | `admin/tahun-ajaran/show.blade.php` | Statistik TA (jumlah kelas, siswa, jadwal). |
| Hapus | `admin.tahun-ajaran.destroy` | DELETE | `@destroy` | redirect | Cek dependency kelas/jadwal sebelum hapus. |
| Aktifkan | `admin.tahun-ajaran.activate` | POST | `@activate` | redirect | Set TA ini sebagai aktif (otomatis non-aktifkan TA lain). |

**Catatan**: Mirror di Waka dengan `WakaTahunAjaranController` — controller terpisah, view terpisah, tapi logika sangat mirip.

---

### Manajemen Cabang

**Index view**: `admin/cabang/index.blade.php` · **Controller**: `Admin/CabangController.php` (resource)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.cabang.create` | GET | `@create` | `admin/cabang/create.blade.php` | Form: nama cabang, alamat, jenjang yg tersedia. |
| Simpan | `admin.cabang.store` | POST | `@store` | redirect | Validasi nama unik + buat `Cabang`. |
| Edit | `admin.cabang.edit` | GET | `@edit` | `admin/cabang/edit.blade.php` | Form pre-fill. |
| Update | `admin.cabang.update` | PUT | `@update` | redirect | Validasi & update. |
| Detail | `admin.cabang.show` | GET | `@show` | `admin/cabang/show.blade.php` | Statistik (jumlah siswa, kelas, user). |
| Hapus | `admin.cabang.destroy` | DELETE | `@destroy` | redirect | Cek dependency sebelum hapus. |
| Toggle aktif/non-aktif | `admin.cabang.toggle-status` | POST | `@toggleStatus` | redirect | Aktifkan/non-aktifkan cabang. |

**Catatan**: Admin-only menu.

---

### Data Kelas

**Index view**: `admin/kelas/index.blade.php` · **Controller**: `Admin/KelasController.php` (resource + custom)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.kelas.create` | GET | `@create` | `admin/kelas/create.blade.php` | Form: nama kelas, jenjang, cabang, TA, kuota, wali kelas. |
| Simpan | `admin.kelas.store` | POST | `@store` | redirect | Validasi & buat `Kelas`. |
| Edit | `admin.kelas.edit` | GET | `@edit` | `admin/kelas/edit.blade.php` | Form pre-fill. |
| Update | `admin.kelas.update` | PUT | `@update` | redirect | Validasi & update. |
| Detail | `admin.kelas.show` | GET | `@show` | `admin/kelas/show.blade.php` | Daftar siswa + statistik kelas. |
| Hapus | `admin.kelas.destroy` | DELETE | `@destroy` | redirect | Cek siswa/jadwal aktif sebelum hapus. |
| Kelola Siswa | `admin.kelas.manage-siswa` | GET | `@manageSiswa` | `admin/kelas/manage-siswa.blade.php` | Halaman drag-and-drop tambah/hapus siswa ke kelas + sisa kuota. |
| Tambah Siswa | `admin.kelas.add-siswa` | POST | `@addSiswa` | redirect | Attach siswa ke kelas. |
| Hapus Siswa | `admin.kelas.remove-siswa` | POST | `@removeSiswa` | redirect | Detach siswa. |
| Set Wali Kelas | `admin.kelas.assign-wali` | POST | `@assignWaliKelas` | redirect | Set/ubah wali kelas via `WaliKelasAssignment`. |
| Cetak Daftar | `admin.kelas.print` | GET | `@printDaftarKelas` | `admin/kelas/print.blade.php` | Layout cetak daftar kelas (filter cabang/jenjang/TA). |
| Salin Kelas (Copy ke TA lain) | `admin.kelas.copy` | POST | `@copyClasses` | redirect | Duplikasi kumpulan kelas dari TA lama ke TA baru. |
| Import Excel | `admin.kelas.import` / `.store` | GET/POST | `@importForm` / `@import` | `admin/kelas/import.blade.php` | Upload & parse `.xlsx`. |
| Download Template | `admin.kelas.template` | GET | `@downloadTemplate` | file download | Template `.xlsx`. |

**Catatan**: Mirror di Waka (`WakaKelasController`) — controller & view terpisah, route prefix `waka.kelas.*`. Logika sangat mirip; Waka biasanya di-scope ke `cabang_id`-nya.

---

### Data Wali Kelas

**Index view**: `admin/wali-kelas/index.blade.php` · **Controller**: `Admin/WaliKelasController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Kelas | `admin.wali-kelas.show` | GET | `@show` | `admin/wali-kelas/show.blade.php` | Detail wali per kelas + opsi assign/replace. |
| Set/Ubah Wali | `admin.wali-kelas.assign` | POST | `@assign` | redirect | Buat/update `WaliKelasAssignment` untuk kelas tertentu. |
| Set Wali Massal | `admin.wali-kelas.bulk-assign` | POST | `@bulkAssign` | redirect | Bulk assign dari modal (multi-kelas sekaligus). |
| Cetak | `admin.wali-kelas.print` | GET | `@print` | `admin/wali-kelas/print.blade.php` | Layout cetak daftar wali per kelas. |

**Catatan**: Tidak ada create/edit/destroy terpisah — wali kelas hanya bisa di-assign ke kelas existing. Mirror di Waka (`WakaWaliKelasController`).

---

### Data Guru Pengajar

**Index view**: `admin/guru-pengajar/index.blade.php` · **Controller**: `Admin/GuruPengajarController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Guru | `admin.guru-pengajar.show` | GET | `@show` | `admin/guru-pengajar/show.blade.php` | Profil guru + daftar kelas yang diampu (derived dari Jadwal). |
| Kelola per Kelas | `admin.guru-pengajar.manage-kelas` | GET | `@manageKelas` | `admin/guru-pengajar/manage-kelas.blade.php` | Tampil daftar mapel & guru per kelas tertentu (read-only, edit lewat Jadwal). |
| Rebuild dari Jadwal | `admin.guru-pengajar.rebuild` | POST | `@rebuildFromJadwal` | redirect | Sinkronisasi `GuruPengajarKelas` dari `JadwalPelajaran` (refresh cache). |
| Cetak | `admin.guru-pengajar.print` | GET | `@print` | `admin/guru-pengajar/print.blade.php` | Layout cetak daftar guru per cabang/TA. |

**Catatan**: Read-only dashboard — sumber data dari Jadwal Pelajaran. Tidak ada tombol Tambah/Edit. Mirror di Waka (`WakaGuruPengajarController`).

---

### Mata Pelajaran

**Index view**: `admin/mata-pelajaran/index.blade.php` · **Controller**: `Admin/MataPelajaranController.php` (resource + custom)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.mata-pelajaran.create` | GET | `@create` | `admin/mata-pelajaran/create.blade.php` | Form: nama, kode, jenjang, kategori. |
| Simpan | `admin.mata-pelajaran.store` | POST | `@store` | redirect | Validasi & buat `MataPelajaran`. |
| Edit | `admin.mata-pelajaran.edit` | GET | `@edit` | `admin/mata-pelajaran/edit.blade.php` | Form pre-fill. |
| Update | `admin.mata-pelajaran.update` | PUT | `@update` | redirect | Validasi & update. |
| Detail | `admin.mata-pelajaran.show` | GET | `@show` | `admin/mata-pelajaran/show.blade.php` | Detail + statistik penggunaan. |
| Hapus | `admin.mata-pelajaran.destroy` | DELETE | `@destroy` | redirect | Cek penggunaan di Jadwal sebelum hapus. |
| Cetak | `admin.mata-pelajaran.print` | GET | `@print` | `admin/mata-pelajaran/print.blade.php` | Layout cetak (filter jenjang). |
| Import Excel | `admin.mata-pelajaran.import` / `.store` | GET/POST | `@importForm` / `@import` | `admin/mata-pelajaran/import.blade.php` | Upload & parse `.xlsx`. |
| Download Template | `admin.mata-pelajaran.template` | GET | `@downloadTemplate` | file download | Template `.xlsx`. |
| Saran Kode Mapel | `admin.mata-pelajaran.suggest-kode` | GET | `@suggestKodeMapel` | JSON | Generate kode mapel otomatis berdasarkan nama (AJAX). |

**Catatan**: Mirror di Waka (`WakaMataPelajaranController`).

---

### Pengaturan Istirahat

**Index view**: `admin/pengaturan-istirahat/index.blade.php` · **Controller**: `Admin/PengaturanIstirahatController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.pengaturan-istirahat.create` | GET | `@create` | `admin/pengaturan-istirahat/create.blade.php` | Form: jenjang, hari, jam mulai-selesai, label. |
| Simpan | `admin.pengaturan-istirahat.store` | POST | `@store` | redirect | Validasi bentrok jam + buat `PengaturanIstirahat`. |
| Edit | `admin.pengaturan-istirahat.edit` | GET | `@edit` | `admin/pengaturan-istirahat/edit.blade.php` | Form pre-fill. |
| Update | `admin.pengaturan-istirahat.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `admin.pengaturan-istirahat.destroy` | DELETE | `@destroy` | redirect | Hapus pengaturan. |
| Toggle status | `admin.pengaturan-istirahat.toggle-status` | PATCH | `@toggleStatus` | redirect | Aktif/non-aktifkan slot istirahat. |

**Catatan**: Tidak di sidebar utama — diakses via tombol **"Istirahat"** (oranye) di halaman index Jadwal Pelajaran. Slot istirahat ini muncul di grid Jadwal. Mirror di Waka (`Waka\PengaturanIstirahatController` di `waka/pengaturan-istirahat/*`).

---

### Jadwal Pelajaran

**Index view**: `admin/jadwal-pelajaran/index.blade.php` · **Controller**: `Admin/JadwalPelajaranController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah (modal) | `admin.jadwal-pelajaran.create` | GET | `@create` | `admin/jadwal-pelajaran/create.blade.php` | Form: pilih kelas, mapel, guru, hari, jam mulai-selesai. |
| Simpan | `admin.jadwal-pelajaran.store` | POST | `@store` | redirect | Validasi bentrok kelas/guru pada hari & jam yang sama → simpan. |
| Detail per Kelas | `admin.jadwal-pelajaran.show` | GET | `@show` | `admin/jadwal-pelajaran/show.blade.php` | Grid jadwal mingguan utk satu kelas, termasuk slot istirahat. |
| Edit | `admin.jadwal-pelajaran.edit` | GET | `@edit` | `admin/jadwal-pelajaran/edit.blade.php` | Form pre-fill. |
| Update | `admin.jadwal-pelajaran.update` | PUT | `@update` | redirect | Validasi bentrok + update + log `JadwalPelajaranHistory`. |
| Hapus | `admin.jadwal-pelajaran.destroy` | DELETE | `@destroy` | redirect | Hapus jadwal + log history. |
| Ganti Guru (per jadwal) | `admin.jadwal-pelajaran.ganti-guru` | POST | `@gantiGuru` | redirect | Update kolom `guru_id` saja + log. |
| Ganti Guru Massal | `admin.jadwal-pelajaran.bulk-replace-guru` | POST | `@bulkReplaceGuru` | redirect | Replace guru di banyak jadwal sekaligus (mis. guru resign). |
| Hapus Terpilih | `admin.jadwal-pelajaran.bulk-delete` | POST | `@bulkDelete` | redirect | Bulk delete dari checkbox. |
| Update Status Massal | `admin.jadwal-pelajaran.bulk-update-status` | POST | `@bulkUpdateStatus` | redirect | Aktif/draft batch. |
| Duplikasi Jadwal (TA lain) | `admin.jadwal-pelajaran.duplicate` | POST | `@duplicate` | redirect | Salin set jadwal dari TA sumber ke TA tujuan. |
| Preview Cetak | `admin.jadwal-pelajaran.preview-print` | GET | `@previewPrint` | `admin/jadwal-pelajaran/print.blade.php` | Preview tampilan cetak per kelas. |
| Cetak PDF per Kelas | `admin.jadwal-pelajaran.print` | GET | `@exportPdf` | `admin/jadwal-pelajaran/print.blade.php` | Render layout cetak (DomPDF-friendly). |
| Export Excel per Kelas | `admin.jadwal-pelajaran.export-excel-class` | GET | `@exportExcelClass` | `admin/jadwal-pelajaran/export-excel-class.blade.php` | Excel response untuk satu kelas. |
| Export PDF (Semua) | `admin.jadwal-pelajaran.export-pdf` | GET | `@exportPdfAll` | `admin/jadwal-pelajaran/export-pdf.blade.php` | PDF semua kelas pada TA aktif. |
| Export Excel (Semua) | `admin.jadwal-pelajaran.export-excel` | GET | `@exportExcel` | `admin/jadwal-pelajaran/export-excel.blade.php` | Streamed Excel semua kelas. |
| Import Excel | `admin.jadwal-pelajaran.import` / `.store` | GET/POST | `@importForm` / `@import` | `admin/jadwal-pelajaran/import.blade.php` | Upload `.xlsx`, parse via Maatwebsite. |
| Download Template | `admin.jadwal-pelajaran.template` | GET | `@downloadTemplate` | file download | Template `.xlsx`. |
| API: Siswa per Kelas | `admin.jadwal-pelajaran.get-students` | GET | `@getStudents` | JSON | Untuk dropdown AJAX. |
| API: Jadwal per Kelas | `admin.jadwal-pelajaran.api.by-kelas` | GET | `@getByKelas` | JSON | Untuk widget. |
| API: Jadwal per Guru | `admin.jadwal-pelajaran.api.by-guru` | GET | `@getByGuru` | JSON | Untuk widget. |
| Tombol Istirahat (oranye di header) | `admin.pengaturan-istirahat.index` | GET | `PengaturanIstirahatController@index` | `admin/pengaturan-istirahat/index.blade.php` | Cross-link → modul Pengaturan Istirahat. |
| Tombol Cetak/Export → "Cetak Per Kelas" | `admin.jadwal-pelajaran.show` | GET | `@show` | (same as Detail) | Membuka detail kelas; dari sana ada tombol cetak per-kelas. |

**Catatan**: Validasi bentrok dijalankan via `JadwalPelajaranTrait`. Setiap perubahan dicatat di `JadwalPelajaranHistory`. Mirror di Waka (`WakaJadwalPelajaranController`) — route prefix `waka.jadwal-pelajaran.*`, view `waka/jadwal-pelajaran/*`.

---

### Manajemen Siswa

**Index view**: `admin/manajemen-siswa/index.blade.php` · **Controller**: `Admin/ManajemenSiswaController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Siswa | `admin.manajemen-siswa.show` | GET | `@show` | `admin/manajemen-siswa/show.blade.php` | Profil siswa + form assign kelas + attach/detach orang tua. |
| Per Kelas (kelola siswa) | `admin.manajemen-siswa.per-kelas` | GET | `@perKelas` | `admin/manajemen-siswa/per-kelas.blade.php` | Halaman pengaturan siswa per kelas (add/remove). |
| Add ke Kelas | `admin.manajemen-siswa.add-to-kelas` | POST | `@addToKelas` | redirect | Tambah siswa ke kelas. |
| Remove dari Kelas | `admin.manajemen-siswa.remove-from-kelas` | POST | `@removeFromKelas` | redirect | Lepas siswa dari kelas. |
| Assign Kelas (single) | `admin.manajemen-siswa.assign-kelas` | POST | `@assignKelas` | redirect | Set `kelas_id` siswa. |
| Bulk Assign | `admin.manajemen-siswa.bulk-assign` | POST | `@bulkAssign` | redirect | Set kelas utk banyak siswa sekaligus. |
| Attach Orang Tua | `admin.manajemen-siswa.attach-parent` | POST | `@attachParent` | redirect | Buat `StudentParent` link. |
| Detach Orang Tua | `admin.manajemen-siswa.detach-parent` | DELETE | `@detachParent` | redirect | Hapus link. |
| Cetak Daftar | `admin.manajemen-siswa.print` | GET | `@print` | `admin/manajemen-siswa/print.blade.php` | Layout cetak siswa (filter cabang/kelas/sort). |
| Cetak Kartu Siswa | `admin.manajemen-siswa.print-kartu` | GET | `@printKartu` | `admin/manajemen-siswa/print-kartu.blade.php` | Render kartu pelajar (1 siswa). |

**Catatan**: Berbeda dengan "Manajemen User → Siswa" yang fokus pada CRUD akun. Menu ini fokus pengelolaan **penempatan kelas & relasi orang tua**. Mirror di Waka (`WakaManajemenSiswaController`).

---

### Cetak Laporan (Akademik)

**Index view**: `admin/cetak-laporan/index.blade.php` · **Controller**: `Admin/CetakLaporanController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Cetak Siswa | `admin.cetak-laporan.siswa` | GET | `@siswa` | `admin/cetak-laporan/print-siswa.blade.php` | Layout cetak daftar siswa. |
| Cetak Tenaga Pendidik | `admin.cetak-laporan.tenaga-pendidik` | GET | `@tenagaPendidik` | `admin/cetak-laporan/print-guru.blade.php` | Layout cetak daftar tenaga pendidik (mapping ke `print-guru` view). |
| Cetak Kelas | `admin.cetak-laporan.kelas` | GET | `@kelas` | `admin/cetak-laporan/print-kelas.blade.php` | Layout cetak daftar kelas. |
| Cetak Wali Kelas | `admin.cetak-laporan.wali-kelas` | GET | `@waliKelas` | `admin/cetak-laporan/print-wali-kelas.blade.php` | Layout cetak wali kelas. |
| Cetak Guru Pengajar | `admin.cetak-laporan.guru-pengajar` | GET | `@guruPengajar` | `admin/cetak-laporan/print-guru-pengajar.blade.php` | Layout cetak guru pengajar. |
| Cetak Rekap | `admin.cetak-laporan.rekap` | GET | `@rekap` | `admin/cetak-laporan/print-rekap.blade.php` | Rekap statistik per cabang/jenjang. |
| Cetak Rekap Akademik | `admin.cetak-laporan.rekap-akademik` | GET | `@rekapAkademik` | `admin/cetak-laporan/print-rekap-akademik.blade.php` | Rekap akademik (kelulusan, naik kelas, dsb.). |

**Catatan**: Tidak di sidebar (hub cetak ad-hoc); diakses lewat tombol di halaman lain. Controller `CetakLaporanController` standalone (tidak extend role lain). Berbeda dari menu **Laporan** (`admin.laporan.*`) yang memakai wrapper `MonitoringController` dan mengembalikan view `ketua/laporan/*`.

---

### Keuangan → Tagihan

**Index view**: `admin/keuangan/tagihan/index.blade.php` · **Controller**: `Admin/Keuangan/TagihanController.php` (extends `Bendahara/TagihanController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Tagihan Siswa | `admin.keuangan.tagihan.show` | GET | `@show` | `admin/keuangan/tagihan/show.blade.php` | Rincian tagihan + riwayat pembayaran satu siswa. |
| Edit Tagihan | `admin.keuangan.tagihan.edit` | GET | `@edit` | `admin/keuangan/tagihan/edit.blade.php` | Form edit item-item tagihan siswa. |
| Update Tagihan | `admin.keuangan.tagihan.update` | PUT | `@update` (parent) | redirect | Validasi & update item tagihan. |
| Hapus Item Tagihan | `admin.keuangan.tagihan.destroy-item` | DELETE | `@destroyItem` (parent) | redirect | Hapus 1 baris item dari tagihan siswa. |
| Cetak Tagihan Siswa | `admin.keuangan.tagihan.cetak` | GET | `@cetak` | `bendahara/tagihan/cetak.blade.php` | Layout cetak invoice 1 siswa (view di-share dengan Bendahara — tidak ada override). |
| Buat Massal | `admin.keuangan.tagihan.bulk-create` / `.store` | GET/POST | `@bulkCreate` | `admin/keuangan/tagihan/bulk-create.blade.php` | Form pilih kelas → generate tagihan utk semua siswa di kelas tsb. |
| Tagihan Custom (per siswa) | `admin.keuangan.tagihan.create-custom` · `.store-custom` | GET/POST | `@createCustom` · `@storeCustom` | `admin/keuangan/tagihan/create-custom.blade.php` | Form custom: pilih siswa + isi item bebas. |
| Generate SPP | `admin.keuangan.tagihan.generate-spp` / `.store` | GET/POST | `@generateSppForm` · `@generateSpp` | `admin/keuangan/tagihan/generate-spp.blade.php` | Form bulk-generate SPP bulanan utk semua/kelas tertentu. |
| Duplikasi Tagihan (TA lain) | `admin.keuangan.tagihan.duplicate` / `.store` | GET/POST | `@duplicateForm` · `@duplicate` (parent) | `admin/keuangan/tagihan/duplicate.blade.php` | Copy struktur tagihan dari TA lama ke TA baru. |
| Cetak Laporan Tagihan | `admin.keuangan.tagihan.cetak-laporan` | GET | `@cetakLaporan` | `admin/keuangan/tagihan/cetak-laporan.blade.php` | Layout cetak rekap tagihan (filter). |
| Tarik Tunggakan (Carryover) — index | `admin.keuangan.tagihan.carryover` | GET | `@carryoverIndex` (parent, view di-override) | `admin/keuangan/tagihan/carryover.blade.php` | Halaman pilih siswa & TA sumber → akan ditarik tunggakan ke TA aktif. |
| Carryover — Preview | `admin.keuangan.tagihan.carryover.preview` | POST | `@carryoverPreview` (parent) | JSON / view | Preview total tunggakan sebelum eksekusi. |
| Carryover — Execute | `admin.keuangan.tagihan.carryover.execute` | POST | `@carryoverExecute` (parent) | redirect | Insert tagihan tunggakan ke TA aktif. |
| Import Excel | `admin.keuangan.tagihan.import` / `.store` | GET/POST | `@importForm` · `@import` | `admin/keuangan/tagihan/import.blade.php` | Upload `.xlsx` (`TagihanImport`), validasi siswa, import batch. |
| Download Template | `admin.keuangan.tagihan.template` | GET | `@downloadTemplate` | file download | Template Excel. |
| Reset Tagihan (masa percobaan) | `admin.keuangan.tagihan.reset-tagihan` | POST | `@resetTagihan` | redirect | **Admin-only**: hapus seluruh tagihan TA aktif (dangerous). Tidak ada di Bendahara. |
| API: Siswa per Kelas | `admin.keuangan.tagihan.api.siswa-by-kelas` | GET | parent | JSON | Untuk dropdown AJAX. |
| API: Preview Tagihan | `admin.keuangan.tagihan.api.tagihan-preview` | GET | parent | JSON | Preview tagihan siswa terpilih. |

**Catatan**: Controller `extends Bendahara\TagihanController` — semua logika query/CRUD diwarisi dari Bendahara, admin hanya override `viewName` (ke `admin/keuangan/tagihan/*`) dan route prefix. View admin & bendahara **mirip tapi terpisah** (kecuali `cetak.blade.php` siswa yang di-share). Route `reset-tagihan` hanya ada di admin (tidak di-mirror).

---

### Keuangan → Pembayaran

**Index view**: `admin/keuangan/pembayaran/index.blade.php` · **Controller**: `Admin/Keuangan/PembayaranController.php` (extends `Bendahara/PembayaranController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Pembayaran | `admin.keuangan.pembayaran.show` | GET | `@show` | `admin/keuangan/pembayaran/show.blade.php` | Detail bukti, status, validasi. |
| Validasi (terima/tolak) | `admin.keuangan.pembayaran.validasi` | POST | `@validasi` (parent) | redirect | Approve/reject pembayaran pending. |
| + Catat Pembayaran (per siswa) | `admin.keuangan.pembayaran.create` | GET | `@create` | `admin/keuangan/pembayaran/create.blade.php` | Form input pembayaran manual oleh admin/bendahara utk siswa tertentu. |
| Simpan Pembayaran | `admin.keuangan.pembayaran.store` | POST | `@store` (parent) | redirect | Simpan `Pembayaran` (status pending/lunas). |
| Validasi Langsung | `admin.keuangan.pembayaran.validasi-langsung` | POST | `@validasiLangsung` (parent) | redirect | Buat + langsung approve (untuk cash di tempat). |
| Riwayat per Siswa | `admin.keuangan.pembayaran.riwayat-siswa` | GET | `@riwayatSiswa` | `admin/keuangan/pembayaran/riwayat-siswa.blade.php` | Daftar semua pembayaran 1 siswa lintas TA. |
| Cetak Kwitansi | `admin.keuangan.pembayaran.cetak-kwitansi` | GET | `@cetakKwitansi` | `admin/keuangan/pembayaran/cetak-kwitansi.blade.php` | Layout cetak kwitansi tunggal. |

**Catatan**: Controller `extends Bendahara\PembayaranController` — sama persis kecuali view path override ke `admin/keuangan/pembayaran/*`. Mirror di Bendahara: `bendahara.pembayaran.*`.

---

### Keuangan → Laporan Keuangan

**Index view**: `admin/keuangan/laporan/index.blade.php` · **Controller**: `Admin/Keuangan/LaporanPembayaranController.php` (extends `Bendahara/LaporanPembayaranController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Cetak Laporan Pembayaran | `admin.keuangan.laporan.cetak` | GET | `@cetak` (parent) | `bendahara/laporan/cetak.blade.php` | Layout cetak rekap pembayaran periode (view shared dengan Bendahara). |
| Rekap Tagihan | `admin.keuangan.laporan.rekap-tagihan` | GET | `@rekapTagihan` | `admin/keuangan/laporan/rekap-tagihan.blade.php` | Rincian rekap nominal tagihan & terbayar per kelas/TA. |
| Cetak Rekap Tagihan | `admin.keuangan.laporan.cetak-rekap-tagihan` | GET | `@cetakRekapTagihan` (parent) | `bendahara/laporan/cetak-rekap-tagihan.blade.php` | Layout cetak rekap (view shared). |
| Belum Lunas | `admin.keuangan.laporan.belum-lunas` | GET | `@belumLunas` | `admin/keuangan/laporan/belum-lunas.blade.php` | Daftar siswa dengan tunggakan. |
| Cetak Belum Lunas | `admin.keuangan.laporan.cetak-belum-lunas` | GET | `@cetakBelumLunas` (parent) | `bendahara/laporan/cetak-belum-lunas.blade.php` | Layout cetak (view shared). |

**Catatan**: Controller extend bendahara. Method `cetak*` tidak di-override sehingga **view dari bendahara dipakai langsung**. Mirror penuh di Bendahara: `bendahara.laporan.*`.

---

### Keuangan → Config Pembayaran (Info Pembayaran)

**Index view**: `admin/keuangan/info-pembayaran/index.blade.php` · **Controller**: `Admin/Keuangan/InfoPembayaranController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Simpan Config | `admin.keuangan.info-pembayaran.update` | POST | `@update` | redirect | Simpan API key Midtrans + daftar `InfoPembayaran` (rekening bank). |
| (Legacy) Update | `admin.keuangan.info-pembayaran.legacy-update` | POST | `@update` | redirect | Alias lama untuk backward-compat (redirect 301 ke `/admin/keuangan/config`). |

**Catatan**: Controller berdiri sendiri (tidak extend bendahara), tapi logika setara dengan Bendahara `info-pembayaran`. Mirror di Bendahara: `bendahara.info-pembayaran.*`.

---

### Keuangan → Validasi Akses (Ujian & Rapor)

**Index view**: `admin/keuangan/validasi-akses/index.blade.php` · **Controller**: `Admin/Keuangan/ValidasiAksesController.php` (extends `Bendahara/ValidasiAksesController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Validasi Ujian (1 siswa) | `admin.keuangan.validasi-akses.validasi-ujian` | POST | parent | redirect | Set flag akses ujian = true utk siswa. |
| Batalkan Ujian | `admin.keuangan.validasi-akses.batalkan-ujian` | POST | parent | redirect | Cabut akses ujian. |
| Validasi Rapor (1 siswa) | `admin.keuangan.validasi-akses.validasi-rapor` | POST | parent | redirect | Set flag akses rapor = true. |
| Batalkan Rapor | `admin.keuangan.validasi-akses.batalkan-rapor` | POST | parent | redirect | Cabut akses rapor. |
| Bulk Validasi Ujian (per kelas) | `admin.keuangan.validasi-akses.bulk-validasi-ujian` | POST | parent | redirect | Validasi semua siswa dalam kelas. |
| Bulk Validasi Rapor (per kelas) | `admin.keuangan.validasi-akses.bulk-validasi-rapor` | POST | parent | redirect | Validasi semua siswa dalam kelas. |
| Bulk Validasi Terpilih | `admin.keuangan.validasi-akses.bulk-validasi-selected` | POST | parent | redirect | Validasi siswa dari checkbox. |
| Reset Validasi | `admin.keuangan.validasi-akses.reset` | POST | parent | redirect | Reset semua flag akses (gunakan hati-hati). |
| Atur Batas Pembayaran | `admin.keuangan.validasi-akses.batas-pembayaran` | POST | parent | redirect | Set ambang minimal % pembayaran utk lolos validasi otomatis di `PengaturanBatasPembayaran`. |
| Ajukan Dispensasi (ke Ketua) | `admin.keuangan.validasi-akses.dispensasi` | POST | parent | redirect | Buat `PengajuanRaporKetua` → diputuskan oleh Ketua PKBM (lihat alur `flow.md §5.1–5.4`). |

**Catatan**: Controller extend Bendahara. View admin **hanya satu** (`index.blade.php`) — semua aksi adalah POST/AJAX. Mirror di Bendahara `bendahara.validasi-akses.*` & Wali `wali.validasi-akses.*`.

---

### Keuangan → Promotion Validation (Dispensasi Naik Kelas)

**Index view**: `admin/keuangan/promotion/validation.blade.php` · **Controller**: `Admin/Keuangan/PromotionValidationController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Setujui/Tolak Dispensasi (1) | `admin.keuangan.promotion.validation.store` | POST | `@store` | redirect | Catat keputusan `izin_naik_kelas_khusus` utk satu siswa. |
| Bulk Setujui/Tolak | `admin.keuangan.promotion.validation.bulk-store` | POST | `@bulkStore` | redirect | Bulk decision dari checkbox. |
| Riwayat | `admin.keuangan.promotion.validation.history` | GET | `@history` | `admin/keuangan/promotion/history.blade.php` | Daftar keputusan dispensasi lampau. |

**Catatan**: Controller berdiri sendiri. Mirror di Bendahara `bendahara.promotion.validation.*` (controller `Bendahara/PromotionValidationController`, view `bendahara/promotion/*`).

---

### Akademik → Kalender Akademik

**Index view**: `admin/akademik/kalender/index.blade.php` · **Controller**: `Admin/Akademik/AkademikController.php` (extends `Sekretaris/SekretarisController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.akademik.kalender.create` | GET | `@kalenderCreate` | `admin/akademik/kalender/form.blade.php` | Form: nama, tgl mulai/selesai, jenis, lampiran PDF, status. |
| Simpan | `admin.akademik.kalender.store` | POST | `@kalenderStore` | redirect | Validasi + simpan `KalenderAkademik` (TA aktif). |
| Edit | `admin.akademik.kalender.edit` | GET | `@kalenderEdit` | `admin/akademik/kalender/form.blade.php` | Form re-use, mode edit. |
| Update | `admin.akademik.kalender.update` | PUT | `@kalenderUpdate` | redirect | Validasi & update. |
| Detail | `admin.akademik.kalender.show` | GET | `@kalenderShow` | `admin/akademik/kalender/show.blade.php` | Detail kegiatan + lampiran. |
| Hapus | `admin.akademik.kalender.destroy` | DELETE | `@kalenderDestroy` | redirect | Hapus + bersihkan file. |
| Toggle Visibility | `admin.akademik.kalender.toggle-visibility` | POST | `@kalenderToggleVisibility` | redirect | Tampil/sembunyikan di landing publik. |
| View Bulanan | `admin.akademik.kalender.bulanan` | GET | `@kalenderBulanan` | (toggle layout di index/cetak) | View tampilan bulanan. |
| Cetak | `admin.akademik.kalender.cetak` | GET | `@kalenderCetak` (passthrough) | `sekretaris/kalender/cetak-bulanan.blade.php` atau `cetak-tahunan.blade.php` | DomPDF render. Karena admin hanya `return parent::kalenderCetak()` dan parent memakai `Pdf::loadView('sekretaris.kalender.cetak-*')` hardcoded, PDF di-render dari view Sekretaris. Mode bulanan/tahunan dipilih lewat query. |

**Catatan**: Form view `form.blade.php` dipakai untuk **create & edit** sekaligus. Mirror di Sekretaris (`sekretaris.kalender.*`) — controller induk; admin override view-path lewat `wrapView()` untuk method index/create/edit/show. View `admin/akademik/kalender/cetak-bulanan.blade.php` & `cetak-tahunan.blade.php` ada di disk namun **tidak terpanggil** (kalender cetak admin tetap render dari namespace sekretaris).

---

### Akademik → Pengumuman

**Index view**: `admin/akademik/pengumuman/index.blade.php` · **Controller**: `Admin/Akademik/AkademikController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.akademik.pengumuman.create` | GET | `@pengumumanCreate` | `admin/akademik/pengumuman/form.blade.php` | Form pengumuman. |
| Simpan | `admin.akademik.pengumuman.store` | POST | `@pengumumanStore` | redirect | Simpan `Pengumuman`. |
| Edit | `admin.akademik.pengumuman.edit` | GET | `@pengumumanEdit` | `admin/akademik/pengumuman/form.blade.php` | Form pre-fill. |
| Update | `admin.akademik.pengumuman.update` | PUT | `@pengumumanUpdate` | redirect | Update. |
| Hapus | `admin.akademik.pengumuman.destroy` | DELETE | `@pengumumanDestroy` | redirect | Hapus. |

**Catatan**: Tidak ada show — pengumuman ditampilkan inline di index. Mirror di Sekretaris (`sekretaris.pengumuman.*`).

---

### Akademik → Berita

**Index view**: `admin/akademik/berita/index.blade.php` · **Controller**: `Admin/Akademik/AkademikController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.akademik.berita.create` | GET | `@beritaCreate` | `admin/akademik/berita/form.blade.php` | Form: judul, isi, gambar, kategori. |
| Simpan | `admin.akademik.berita.store` | POST | `@beritaStore` | redirect | Simpan `Berita`. |
| Edit | `admin.akademik.berita.edit` | GET | `@beritaEdit` | `admin/akademik/berita/form.blade.php` | Form pre-fill. |
| Update | `admin.akademik.berita.update` | PUT | `@beritaUpdate` | redirect | Update + handle gambar lama. |
| Hapus | `admin.akademik.berita.destroy` | DELETE | `@beritaDestroy` | redirect | Hapus + bersihkan storage. |
| Toggle Featured | `admin.akademik.berita.toggle-featured` | POST | `@beritaToggleFeatured` | redirect | Tandai sebagai berita unggulan di landing. |

**Catatan**: Mirror di Sekretaris (`sekretaris.berita.*`).

---

### Akademik → Flyer

**Index view**: `admin/akademik/flyer/index.blade.php` · **Controller**: `Admin/Akademik/AkademikController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `admin.akademik.flyer.create` | GET | `@flyerCreate` | `admin/akademik/flyer/form.blade.php` | Form upload flyer. |
| Simpan | `admin.akademik.flyer.store` | POST | `@flyerStore` | redirect | Simpan file + metadata. |
| Edit | `admin.akademik.flyer.edit` | GET | `@flyerEdit` | `admin/akademik/flyer/form.blade.php` | Form pre-fill. |
| Update | `admin.akademik.flyer.update` | PUT | `@flyerUpdate` | redirect | Update. |
| Hapus | `admin.akademik.flyer.destroy` | DELETE | `@flyerDestroy` | redirect | Hapus + bersihkan storage. |

**Catatan**: Mirror di Sekretaris (`sekretaris.flyer.*`).

---

### Akademik → Promotion (Kenaikan Kelas)

**Index view (Report)**: `admin/akademik/promotion/rekap.blade.php` · **Controller**: `Admin/Akademik/PromotionReportController.php`, `PromotionKKMController`, `PromotionSettingsController`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Buka Proses & Rekap | `admin.akademik.promotion.report` | GET | `PromotionReportController@index` | `admin/akademik/promotion/rekap.blade.php` | Daftar status kenaikan + filter (kelas, jenjang, status). |
| Cetak Rekap | `admin.akademik.promotion.report.print` | GET | `PromotionReportController@print` | `admin/akademik/promotion/print.blade.php` | Layout cetak rekap. |
| Eksekusi Promosi | `admin.akademik.promotion.execute` | POST | `PromotionReportController@execute` | redirect | Pindah siswa ke kelas tujuan berdasarkan `pengaturan_kkm` + `pengaturan_naik_kelas` + `izin_khusus_ketua`. |
| Rollback (1 siswa) | `admin.akademik.promotion.rollback` | POST | `PromotionReportController@rollback` | redirect | Kembalikan kelas asli + reset status. |
| Rollback Terpilih | `admin.akademik.promotion.rollback-selected` | POST | `PromotionReportController@rollbackSelected` | redirect | Bulk rollback. |
| Promote Terpilih | `admin.akademik.promotion.promote-selected` | POST | `PromotionReportController@promoteSelected` | redirect | Promote ulang siswa yg gagal tapi kini memenuhi syarat. |
| Batalkan Jadwal Promosi | `admin.akademik.promotion.cancel-schedule` | POST | `PromotionReportController@cancelSchedule` | redirect | Batalkan `PromotionSchedule` (kalau pakai mode terjadwal). |
| Pengaturan KKM (index) | `admin.akademik.promotion.kkm.index` | GET | `PromotionKKMController@index` | `admin/akademik/promotion/kkm.blade.php` | Form atur nilai KKM per mapel/jenjang (`pengaturan_kkm` DB::table). |
| Simpan KKM | `admin.akademik.promotion.kkm.store` | POST | `PromotionKKMController@store` | redirect | Upsert KKM. |
| Pengaturan Kenaikan (index) | `admin.akademik.promotion.settings.index` | GET | `PromotionSettingsController@index` | `admin/akademik/promotion/settings.blade.php` | Form atur aturan kenaikan (min lulus mapel, dsb. — `pengaturan_naik_kelas`). |
| Simpan Settings | `admin.akademik.promotion.settings.store` | POST | `PromotionSettingsController@store` | redirect | Upsert settings. |

**Catatan**: Ini titik eksekusi promosi tahunan. **Controller juga dipakai oleh Waka** (`waka.promotion.*`) — class & method sama, hanya route prefix berbeda. View admin & waka **terpisah**. Lihat detail alur di [flow.md §5.1–5.4](../../flow.md#5-peta-alur-lintas-role-bagian-terpenting).

---

### Monitoring (Pengguna/Wali/Guru/Siswa/LMS)

**Index view**: `admin/monitoring/{pengguna|wali-kelas|guru-pengajar|siswa}.blade.php` · **Controller**: `Admin/MonitoringController.php` (extends `Ketua/KetuaController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Monitoring Pengguna | `admin.monitoring.pengguna` | GET | `@monitoringPengguna` | `admin/monitoring/pengguna.blade.php` | Dashboard last-active, role, status user. |
| Monitoring Wali Kelas | `admin.monitoring.wali-kelas` | GET | `@monitoringWaliKelas` | `admin/monitoring/wali-kelas.blade.php` | Status pengisian rapor/nilai per wali. |
| Monitoring Guru Pengajar | `admin.monitoring.guru-pengajar` | GET | `@monitoringGuruPengajar` | `admin/monitoring/guru-pengajar.blade.php` | Status pengisian materi/tugas/nilai per guru. |
| Monitoring Siswa | `admin.monitoring.siswa` | GET | `@monitoringSiswa` | `admin/monitoring/siswa.blade.php` | Aktivitas LMS siswa. |
| Monitoring LMS (overview) | `admin.monitoring.lms.index` | GET | `@lmsIndex` (parent) | `monitoring-lms/index.blade.php` | Overview LMS lintas kelas (view shared root). |
| Monitoring LMS per Kelas | `admin.monitoring.lms.kelas` | GET | `@lmsKelas` (parent) | view shared | Detail LMS 1 kelas. |
| Preview Materi/Tugas/Ujian | `admin.monitoring.lms.preview` | GET | `@lmsPreview` (parent) | view shared | Preview konten LMS. |
| Kirim Catatan ke Pengelola | `admin.monitoring.lms.catatan` | POST | `@lmsKirimCatatan` (parent) | redirect | Kirim catatan ke wali/guru terkait LMS. |

**Catatan**: Controller `extends KetuaController` — semua logika sama dengan Ketua, hanya override view ke `admin.monitoring.*`. View `monitoring-lms/index.blade.php` di root dipakai bersama Admin, Ketua, dan Waka.

---

### Laporan (cetak)

**Index view**: `admin/laporan/index.blade.php` · **Controller**: `Admin/MonitoringController.php` (extends `Ketua/KetuaController`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View aktual | Logika ringkas |
|---|---|---|---|---|---|
| Buka Laporan | `admin.laporan.index` | GET | `@index` (wrapView) | `admin/laporan/index.blade.php` | Hub menu cetak laporan + filter. |
| Cetak Siswa | `admin.laporan.siswa` | GET | `@siswa` (passthrough) | `ketua/laporan/print-siswa.blade.php` | Layout cetak siswa (view diwarisi dari Ketua — admin pass-through). |
| Cetak Tenaga Pendidik | `admin.laporan.tenaga-pendidik` | GET | `@tenagaPendidik` (passthrough) | `ketua/laporan/print-guru.blade.php` | View diwarisi dari Ketua. |
| Cetak Kelas | `admin.laporan.kelas` | GET | `@kelas` (passthrough) | `ketua/laporan/print-kelas.blade.php` | View diwarisi dari Ketua. |
| Cetak Wali Kelas | `admin.laporan.wali-kelas` | GET | `@waliKelas` (passthrough) | `ketua/laporan/print-wali-kelas.blade.php` | View diwarisi dari Ketua. |
| Cetak Guru Pengajar | `admin.laporan.guru-pengajar` | GET | `@guruPengajar` (passthrough) | `ketua/laporan/print-guru-pengajar.blade.php` | View diwarisi dari Ketua. |
| Cetak Rekap | `admin.laporan.rekap` | GET | `@rekap` (passthrough) | `ketua/laporan/print-rekap.blade.php` | View diwarisi dari Ketua. |

**Catatan**: Hanya `@index` yang di-wrap ke namespace admin (`admin/laporan/index.blade.php`). Method `@siswa/@tenagaPendidik/@kelas/@waliKelas/@guruPengajar/@rekap` hanya `return parent::xxx()` tanpa override view, jadi view yang muncul adalah **`ketua/laporan/print-*.blade.php`** (mirror dari Ketua, dirender dengan layout Ketua atau shared). View-view di folder `admin/laporan/print-*.blade.php` ada di disk tapi **orphaned** (tidak dipanggil route apa pun).

---

### Catatan (internal)

**Index view**: `admin/catatan/index.blade.php` · **Controller**: `Admin/MonitoringController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Catatan | `admin.catatan.create` | GET | `@catatanCreate` | `admin/catatan/create.blade.php` | Form: judul, isi, prioritas, tipe penerima (semua/role/individu), penerima_ids. |
| Kirim | `admin.catatan.store` | POST | `@catatanStore` | redirect | Validasi + buat `Catatan` per penerima + trigger `NotificationService`. |
| Detail | `admin.catatan.show` | GET | `@catatanShow` | `admin/catatan/show.blade.php` | Detail catatan & status baca. |
| Hapus | `admin.catatan.destroy` | DELETE | `@catatanDestroy` | redirect | Hapus catatan milik sendiri (filter `pengirim_id = auth()->id()`). |

**Catatan**: Mirror penuh di Ketua (`ketua.catatan.*` controller `KetuaController`). Admin extend Ketua → logika sama. View shared root: `shared/catatan/index.blade.php` digunakan beberapa role lain.

---

### Google Sheets Integration

**Index view**: `admin/google-sheets/index.blade.php` · **Controller**: `Admin/GoogleSheetsController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Setup awal (credential) | `admin.google-sheets.setup` | GET | `@setup` | `admin/google-sheets/setup.blade.php` | Wizard upload service-account credential. |
| Simpan Credential | `admin.google-sheets.save-credential` | POST | `@saveCredential` | redirect | Simpan kredensial & validasi. |
| Test Koneksi | `admin.google-sheets.test-connection` | POST | `@testConnection` | JSON | Ping Sheets API. |
| Push Data (per modul) | `admin.google-sheets.push` | POST | `@push` | redirect | Export tabel ke Google Sheets. |
| Preview Pull (per modul) | `admin.google-sheets.pull-preview` | GET | `@pullPreview` | `admin/google-sheets/pull-preview.blade.php` | Preview perubahan sebelum pull. |
| Eksekusi Pull | `admin.google-sheets.pull` | POST | `@pull` | redirect | Import data dari Sheets ke DB. |
| Status (per modul/all) | `admin.google-sheets.status` | GET | `@status` | JSON | Status sync terakhir. |
| Disconnect | `admin.google-sheets.disconnect` | POST | `@disconnect` | redirect | Putuskan koneksi + hapus credential. |

**Catatan**: Tersembunyi dari sidebar (di-flag hidden). Admin-only.
