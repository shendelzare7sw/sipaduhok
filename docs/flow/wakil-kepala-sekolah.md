# Role: Wakil Kepala Sekolah (Waka)

> Kembali ke [flow.md](../../flow.md) · Role `wakil_kepala_sekolah` · Level 2 · Prefix `/waka` · Route `waka.` · Middleware `role:wakil_kepala_sekolah`.

## Ringkasan Peran

Pengelola **akademik operasional** dengan ruang lingkup biasanya **dibatasi per cabang**. Tugasnya beririsan dengan Admin pada data akademik (tahun ajaran, kelas, wali kelas, guru pengajar, mata pelajaran, jadwal, manajemen siswa), pengaturan kenaikan kelas, plus monitoring & komunikasi (catatan/teguran). Banyak fitur memakai **controller yang sama** dengan Admin untuk konsistensi (mis. promotion).

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/waka/partials/sneat-sidebar-menu.blade.php`.
- Dashboard: `WakilKepalaSekolah\WakilKepalaSekolahController@dashboard`.

## Peta Menu

| Grup | Menu | Route (index) | Controller@method | Model |
|---|---|---|---|---|
| Manajemen Akademik | Tahun Ajaran | `waka.tahun-ajaran.index` (+ CRUD, toggle-active) | `WakilKepalaSekolah\TahunAjaranController` | `TahunAjaran` |
| Data Akademik | Data Kelas | `waka.kelas.index` (resource + import/print/manage-siswa/assign-wali) | `WakilKepalaSekolah\KelasController` | `Kelas` |
| Data Akademik | Data Wali Kelas | `waka.wali-kelas.index` (+ assign/show/print) | `WakilKepalaSekolah\WaliKelasController` | `WaliKelasAssignment` |
| Data Akademik | Data Guru Pengajar | `waka.guru-pengajar.index` (+ rebuild/manage-kelas/show) | `WakilKepalaSekolah\GuruPengajarController` | `GuruPengajarKelas` |
| Data Akademik | Mata Pelajaran | `waka.mata-pelajaran.index` (resource + import/print) | `WakilKepalaSekolah\MataPelajaranController` | `MataPelajaran` |
| Data Akademik | Jadwal Pelajaran | `waka.jadwal-pelajaran.index` (+ CRUD/import/export/ganti-guru) | `WakilKepalaSekolah\JadwalPelajaranController` | `JadwalPelajaran` |
| Data Akademik | Manajemen Siswa | `waka.manajemen-siswa.index` (+ per-kelas/assign/attach-parent/print-kartu) | `WakilKepalaSekolah\ManajemenSiswaController` | `Siswa`,`StudentParent` |
| Kenaikan Kelas | Pengaturan KKM | `waka.promotion.kkm.index` | `Admin\Akademik\PromotionKKMController` (shared) | tbl `pengaturan_kkm` (DB::table) |
| Kenaikan Kelas | Pengaturan Kenaikan | `waka.promotion.settings.index` | `Admin\Akademik\PromotionSettingsController` (shared) | tbl `pengaturan_naik_kelas` (DB::table) |
| Kenaikan Kelas | Proses & Rekap | `waka.promotion.report` (+ execute/rollback/promote-selected) | `Admin\Akademik\PromotionReportController` (shared) | `StatusNaikKelasSiswa`,`PromotionSchedule` |
| Monitoring | Monitoring Wali Kelas | `waka.monitoring.wali-kelas` | `WakilKepalaSekolahController@monitoringWaliKelas` | `WaliKelasAssignment` |
| Monitoring | Monitoring Guru Pengajar | `waka.monitoring.guru-pengajar` | `WakilKepalaSekolahController@monitoringGuruPengajar` | `GuruPengajarKelas` |
| Monitoring | Monitoring Siswa | `waka.monitoring.siswa` | `WakilKepalaSekolahController@monitoringSiswa` | `Siswa` |
| Monitoring | Monitoring LMS | `waka.monitoring.lms.index` (+ kelas/preview/catatan) | `WakilKepalaSekolahController@lmsIndex/...` | `Materi`,`Tugas`,`Ujian` |
| Komunikasi | Catatan | `waka.catatan.index` (+ create/store/show/destroy) | `WakilKepalaSekolahController@catatanIndex/...` | `Catatan`,`CatatanDibaca` |

> Tidak di sidebar tapi ada route: `waka.pengaturan-istirahat.*` (`WakilKepalaSekolah\PengaturanIstirahatController`).

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Kenaikan Kelas (KKM / Pengaturan / Proses & Rekap)** — Waka memakai **controller Admin yang sama** (`Admin\Akademik\PromotionKKMController`, `PromotionSettingsController`, `PromotionReportController`) agar logika promosi konsisten lintas role. "Proses & Rekap" `@execute` mengeksekusi kenaikan kelas berdasarkan KKM + aturan + flag `izin_khusus_ketua` (yang disetujui Ketua PKBM). Lihat [flow.md §5.4](../../flow.md#54-kenaikan-kelas-khusus-adminbendahara--ketua-pkbm--adminwaka-eksekusi).
- **Monitoring LMS + Catatan** — Waka memantau aktivitas LMS guru/siswa per cabang dan dapat `lmsKirimCatatan`/`catatanStore` (teguran) yang muncul sebagai "Catatan Monitoring" di sidebar Guru. Pola sama dengan Ketua & Admin.
- **Data Akademik** — secara fungsional mirror Admin (`admin.kelas.*`, `admin.mata-pelajaran.*`, dst) tetapi controller-nya khusus `WakilKepalaSekolah\*` dan umumnya **terfilter cabang** milik Waka. Saat menambah fitur akademik, periksa kedua sisi (Admin & Waka) agar konsisten.

## Detail Sub-Halaman per Menu

> Setiap menu di sidebar Waka yang punya **halaman/aksi selain index** dirinci di sini: tombol di index, route, controller method, view file, dan ringkasan logika. Convention: route name di-prefix `waka.`; view path relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/`. Semua controller Waka ada di namespace `WakilKepalaSekolah/*` (di routes/web.php di-alias `Waka*`).

### Tahun Ajaran

**Index view**: `waka/tahun-ajaran/index.blade.php` · **Controller**: `WakilKepalaSekolah/TahunAjaranController.php`

**Tampilan index**: 3 **stat card**: Total Periode, Periode Aktif, Tahun Berjalan (nama TA aktif). Alert filter aktif (muncul saat `?status=1` atau `=0` di-set). Card **Daftar Tahun Ajaran**: header berisi dropdown filter Status (Semua/Hanya Aktif/Tidak Aktif, auto-submit) + tombol kanan **+ Tahun Ajaran** (primary). Tabel: No, Tahun Ajaran, Tanggal Mulai-Selesai, Status (badge Aktif), Aksi (Detail, Edit, Aktifkan/Set Aktif, Hapus).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `waka.tahun-ajaran.create` | GET | `@create` | `waka/tahun-ajaran/create.blade.php` | Form: nama TA, tanggal mulai-selesai, semester. |
| Simpan | `waka.tahun-ajaran.store` | POST | `@store` | redirect | Validasi tidak overlap + buat `TahunAjaran`. |
| Detail | `waka.tahun-ajaran.show` | GET | `@show` | `waka/tahun-ajaran/show.blade.php` | Statistik TA (jumlah kelas, siswa, jadwal). |
| Edit | `waka.tahun-ajaran.edit` | GET | `@edit` | `waka/tahun-ajaran/edit.blade.php` | Form pre-fill. |
| Update | `waka.tahun-ajaran.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `waka.tahun-ajaran.destroy` | DELETE | `@destroy` | redirect | Cek dependency kelas/jadwal sebelum hapus. |
| Toggle Active / Activate | `waka.tahun-ajaran.toggle-active` · `waka.tahun-ajaran.activate` | POST | `@toggleActive` | redirect | Set TA aktif (auto-non-aktifkan TA lain). Dua route name → method sama. |

**Catatan**: Mirror dengan `admin.tahun-ajaran.*` (controller berbeda, logika setara). Mengubah TA aktif berdampak global.

---

### Data Kelas

**Index view**: `waka/kelas/index.blade.php` · **Controller**: `WakilKepalaSekolah/KelasController.php` (resource + custom)

**Tampilan index**: 4 **stat card**: Total Kelas (di TA pilihan), Siswa Terdaftar, Terisi Wali Kelas, Belum Ada Wali (warning). Card **Daftar Kelas**: header berisi 3 tombol kanan — **Cetak** (secondary, target blank), **Import Excel** (success), **Kelas Baru** (primary). Form filter: search nama/kode kelas, dropdown Tahun Ajaran (default TA aktif, auto-submit), dropdown Jenjang (auto-submit), tombol Filter & Reset. Tabel: Info Kelas (kode+nama+TA), Jenjang badge, Wali Kelas (nama atau "Belum Ditugaskan"), Kuota / Siswa (X/Y), Aksi (Detail, Edit, Kelola Siswa, Hapus).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `waka.kelas.create` | GET | `@create` | `waka/kelas/create.blade.php` | Form: nama kelas, jenjang, TA, kuota, wali kelas. Cabang di-lock ke `userCabang` (cabang waka). |
| Simpan | `waka.kelas.store` | POST | `@store` | redirect | Validasi & buat `Kelas` (cabang otomatis dari user). |
| Detail | `waka.kelas.show` | GET | `@show` | `waka/kelas/show.blade.php` | Daftar siswa + statistik kelas. |
| Edit | `waka.kelas.edit` | GET | `@edit` | `waka/kelas/edit.blade.php` | Form pre-fill. |
| Update | `waka.kelas.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `waka.kelas.destroy` | DELETE | `@destroy` | redirect | Cek siswa/jadwal aktif sebelum hapus. |
| Kelola Siswa | `waka.kelas.manage-siswa` | GET | `@manageSiswa` | `waka/kelas/manage-siswa.blade.php` | Halaman tambah/hapus siswa ke kelas + sisa kuota. |
| Tambah Siswa | `waka.kelas.add-siswa` | POST | `@addSiswa` | redirect | Attach siswa ke kelas. |
| Hapus Siswa | `waka.kelas.remove-siswa` | POST | `@removeSiswa` | redirect | Detach siswa. |
| Set Wali Kelas | `waka.kelas.assign-wali` | POST | `@assignWaliKelas` | redirect | Set/ubah wali kelas via `WaliKelasAssignment`. |
| Cetak Daftar | `waka.kelas.print` | GET | `@print` | `waka/kelas/print.blade.php` | Layout cetak daftar kelas (filter TA/jenjang). |
| Import Excel | `waka.kelas.import` / `.store` | GET/POST | `@import` / `@importStore` | `waka/kelas/import.blade.php` | Upload `.xlsx`, parse via Maatwebsite. |
| Download Template | `waka.kelas.template` | GET | `@downloadTemplate` | file download | Template `.xlsx`. |

**Catatan**: **Tidak ada Salin Kelas (`copy`)** seperti Admin — fitur tsb. admin-only. Semua data terfilter `cabang_id` milik Waka. Mirror logika: `admin.kelas.*`.

---

### Data Wali Kelas

**Index view**: `waka/wali-kelas/index.blade.php` · **Controller**: `WakilKepalaSekolah/WaliKelasController.php`

**Tampilan index**: 4 **stat card**: Total Kelas, Sudah Ada Wali, Belum Ada Wali (warning), Total Guru Aktif. Card **Penunjukan Wali Kelas**: header dengan tombol **Cetak** (secondary). Form filter: search (nama kelas atau wali), dropdown TA, Jenjang, Status (assigned/unassigned) + tombol Filter & Reset. Tabel: Info Kelas, Jenjang & Cabang, Jumlah Siswa, Penugasan Wali Kelas (badge + nama wali atau "Belum ditunjuk"), Aksi (Detail kelas → form assign/ganti wali).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Kelas | `waka.wali-kelas.show` | GET | `@show` | `waka/wali-kelas/show.blade.php` | Detail wali + opsi assign/replace utk satu kelas. |
| Set/Ubah Wali | `waka.wali-kelas.assign` | POST | `@assign` | redirect | Buat/update `WaliKelasAssignment` (parameter URL: `{kelasId}`). |
| Cetak | `waka.wali-kelas.print` | GET | `@print` | `waka/wali-kelas/print.blade.php` | Layout cetak daftar wali per kelas. |

**Catatan**: **Tidak ada bulk-assign** seperti Admin. Tidak ada create/edit/destroy — wali hanya bisa di-assign ke kelas existing. Mirror logika: `admin.wali-kelas.*`.

---

### Data Guru Pengajar (Read-Only)

**Index view**: `waka/guru-pengajar/index.blade.php` · **Controller**: `WakilKepalaSekolah/GuruPengajarController.php`

**Tampilan index**: 4 **stat card**: Total Guru, Sudah Ditugaskan, Total Penugasan (Guru-Kelas-Mapel), Mata Pelajaran. Card **Daftar Guru Pengajar** (subtitle "Penugasan guru otomatis dari Jadwal Pelajaran"): header berisi tombol **Cetak** (secondary, target blank). Form filter: search (nama/NIP/email), dropdown TA, dropdown Status (Aktif/Tidak Aktif) + tombol Filter & Reset. Tabel: identitas guru, jumlah kelas yang diampu, daftar mapel, aksi (Detail, Kelola per Kelas). Tombol **Rebuild dari Jadwal** ada di header atau dekat tabel untuk sinkronisasi cache `GuruPengajarKelas`.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Guru | `waka.guru-pengajar.show` | GET | `@show` | `waka/guru-pengajar/show.blade.php` | Profil guru + daftar kelas yang diampu (derived dari Jadwal). |
| Kelola per Kelas | `waka.guru-pengajar.manage-kelas` | GET | `@manageKelas` | `waka/guru-pengajar/manage-kelas.blade.php` | Tampil daftar mapel & guru per kelas (read-only; edit lewat Jadwal). |
| Rebuild dari Jadwal | `waka.guru-pengajar.rebuild` | POST | `@rebuildFromJadwal` | redirect | Sinkronisasi `GuruPengajarKelas` dari `JadwalPelajaran` (refresh cache). |
| Cetak | `waka.guru-pengajar.print` | GET | `@print` | `waka/guru-pengajar/print.blade.php` | Layout cetak daftar guru per cabang/TA. |

**Catatan**: Read-only dashboard — sumber data dari Jadwal Pelajaran. Tidak ada tombol Tambah/Edit. Mirror logika: `admin.guru-pengajar.*`. Data terfilter cabang Waka.

---

### Mata Pelajaran

**Index view**: `waka/mata-pelajaran/index.blade.php` · **Controller**: `WakilKepalaSekolah/MataPelajaranController.php` (resource + custom)

**Tampilan index**: **Stat scroll horizontal** (7 mini-card per jenjang): Total, KB, TKA, TKB, SD, SMP, SMA. Card **Filter & Action**: dropdown filter Jenjang (auto-submit) + tombol Reset di kiri; di kanan tombol-tombol Action: **+ Tambah** (primary), **Import Excel** (success), **Download Template**, **Cetak** (target blank). Tabel mapel: kode, nama mapel, jenjang badge, jumlah penggunaan di Jadwal, Aksi (Detail, Edit, Hapus).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `waka.mata-pelajaran.create` | GET | `@create` | `waka/mata-pelajaran/create.blade.php` | Form: nama, kode, jenjang, kategori. |
| Simpan | `waka.mata-pelajaran.store` | POST | `@store` | redirect | Validasi & buat `MataPelajaran`. |
| Detail | `waka.mata-pelajaran.show` | GET | `@show` | `waka/mata-pelajaran/show.blade.php` | Detail + statistik penggunaan. |
| Edit | `waka.mata-pelajaran.edit` | GET | `@edit` | `waka/mata-pelajaran/edit.blade.php` | Form pre-fill. |
| Update | `waka.mata-pelajaran.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `waka.mata-pelajaran.destroy` | DELETE | `@destroy` | redirect | Cek penggunaan di Jadwal sebelum hapus. |
| Cetak | `waka.mata-pelajaran.print` | GET | `@print` | `waka/mata-pelajaran/print.blade.php` | Layout cetak (filter jenjang). |
| Import Excel | `waka.mata-pelajaran.import` / `.store` | GET/POST | `@import` / `@importStore` | `waka/mata-pelajaran/import.blade.php` | Upload & parse `.xlsx`. |
| Download Template | `waka.mata-pelajaran.template` | GET | `@downloadTemplate` | file download | Template `.xlsx`. |
| Saran Kode Mapel | `waka.mata-pelajaran.suggest-kode` | GET | `@suggestKodeMapel` | JSON | Generate kode mapel otomatis berdasarkan nama (AJAX). |

**Catatan**: Mirror logika: `admin.mata-pelajaran.*`. Mata pelajaran adalah data master global (tidak per-cabang).

---

### Jadwal Pelajaran

**Index view**: `waka/jadwal-pelajaran/index.blade.php` · **Controller**: `WakilKepalaSekolah/JadwalPelajaranController.php`

**Tampilan index**: 4 **stat card**: Total Jadwal, Jadwal Kosong (warning, slot belum di-assign guru), Guru Mengajar, Total Kelas. Card **Daftar Jadwal Pelajaran**: header dengan tombol kanan dalam scroll-mobile: **+ Tambah** (primary), **Istirahat** (warning, ke modul Pengaturan Istirahat), dropdown **Aksi** (Duplikasi Jadwal modal, Ganti Semua Guru modal, Import Excel), dropdown **Cetak/Export** (Export PDF/Excel Semua + Cetak Per Kelas modal). Form filter: dropdown TA, Jenjang, Kelas, Guru (semua auto-submit). Konten utama: grid jadwal mingguan per kelas (mirip kalender) atau list — klik kelas → detail show. Sub-buttons per baris/cell: edit jadwal, ganti guru, hapus.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah (modal) | `waka.jadwal-pelajaran.create` | GET | `@create` | `waka/jadwal-pelajaran/create.blade.php` | Form: pilih kelas, mapel, guru, hari, jam mulai-selesai. |
| Simpan | `waka.jadwal-pelajaran.store` | POST | `@store` | redirect | Validasi bentrok kelas/guru pada hari & jam yang sama → simpan. |
| Detail per Kelas | `waka.jadwal-pelajaran.show` | GET | `@show` | `waka/jadwal-pelajaran/show.blade.php` | Grid jadwal mingguan utk satu kelas (URL `/kelas/{kelas}/show`). |
| Edit | `waka.jadwal-pelajaran.edit` | GET | `@edit` | `waka/jadwal-pelajaran/edit.blade.php` | Form pre-fill. |
| Update | `waka.jadwal-pelajaran.update` | PUT | `@update` | redirect | Validasi bentrok + update + log `JadwalPelajaranHistory`. |
| Hapus | `waka.jadwal-pelajaran.destroy` | DELETE | `@destroy` | redirect | Hapus jadwal + log history. |
| Ganti Guru (per jadwal) | `waka.jadwal-pelajaran.ganti-guru` | POST | `@gantiGuru` | redirect | Update kolom `guru_id` saja + log. |
| Ganti Guru Massal | `waka.jadwal-pelajaran.bulk-replace-guru` | POST | `@bulkReplaceGuru` | redirect | Replace guru di banyak jadwal sekaligus. |
| Hapus Terpilih | `waka.jadwal-pelajaran.bulk-delete` | POST | `@bulkDelete` | redirect | Bulk delete dari checkbox. |
| Update Status Massal | `waka.jadwal-pelajaran.bulk-update-status` | POST | `@bulkUpdateStatus` | redirect | Aktif/draft batch. |
| Duplikasi Jadwal | `waka.jadwal-pelajaran.duplicate` | POST | `@duplicate` | redirect | Salin set jadwal dari TA sumber ke TA tujuan. |
| Preview Cetak | `waka.jadwal-pelajaran.preview-print` | GET | `@previewPrint` | `waka/jadwal-pelajaran/print.blade.php` | Preview tampilan cetak per kelas. |
| Cetak PDF per Kelas | `waka.jadwal-pelajaran.print` | GET | `@exportPdf` | `waka/jadwal-pelajaran/print.blade.php` | Render layout cetak (DomPDF-friendly). |
| Export Excel per Kelas | `waka.jadwal-pelajaran.export-excel-class` | GET | `@exportExcelClass` | `waka/jadwal-pelajaran/export-excel-class.blade.php` | Excel response untuk satu kelas. |
| Export PDF (Semua) | `waka.jadwal-pelajaran.export-pdf` | GET | `@exportPdfAll` | `waka/jadwal-pelajaran/export-pdf.blade.php` | PDF semua kelas pada TA aktif. |
| Export Excel (Semua) | `waka.jadwal-pelajaran.export-excel` | GET | `@exportExcel` | `waka/jadwal-pelajaran/export-excel.blade.php` | Streamed Excel semua kelas. |
| Import Excel | `waka.jadwal-pelajaran.import` / `.store` | GET/POST | `@importForm` · `@import` | `waka/jadwal-pelajaran/import.blade.php` | Upload `.xlsx`, parse via Maatwebsite. |
| Download Template | `waka.jadwal-pelajaran.template` | GET | `@downloadTemplate` | file download | Template `.xlsx`. |
| API: Siswa per Kelas | `waka.jadwal-pelajaran.get-students` | GET | `@getStudents` | JSON | Untuk dropdown AJAX. |
| API: Jadwal per Kelas | `waka.jadwal-pelajaran.api.by-kelas` | GET | `@getByKelas` | JSON | Untuk widget. |
| API: Jadwal per Guru | `waka.jadwal-pelajaran.api.by-guru` | GET | `@getByGuru` | JSON | Untuk widget. |
| Tombol Istirahat (oranye) | `waka.pengaturan-istirahat.index` | GET | `PengaturanIstirahatController@index` | `waka/pengaturan-istirahat/index.blade.php` | Cross-link → modul Pengaturan Istirahat. |

**Catatan**: Mirror logika: `admin.jadwal-pelajaran.*`. Validasi bentrok via `JadwalPelajaranTrait`. Perubahan dicatat di `JadwalPelajaranHistory`. Data terfilter cabang Waka.

---

### Manajemen Siswa

**Index view**: `waka/manajemen-siswa/index.blade.php` · **Controller**: `WakilKepalaSekolah/ManajemenSiswaController.php`

**Tampilan index**: 5 **stat card**: Total Siswa, Sudah Ada Kelas, Belum Ada Kelas (warning), Laki-laki, Perempuan. Card **Daftar Siswa** dengan badge cabang Waka di header + badge "Snapshot {TA}" bila historical mode. Tombol kanan **Cetak**. Form filter: search (nama/NISN/NIS), dropdown Jenjang (disabled bila historical), Kelas, TA, Status. Tabel siswa: NISN/NIS, nama, jenjang, kelas saat ini (atau "Belum di kelas"), Aksi (Detail, Assign Kelas modal, Cetak Kartu).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Siswa | `waka.manajemen-siswa.show` | GET | `@show` | `waka/manajemen-siswa/show.blade.php` | Profil siswa + form assign kelas + attach/detach orang tua. |
| Per Kelas (kelola siswa) | `waka.manajemen-siswa.per-kelas` | GET | `@perKelas` | `waka/manajemen-siswa/per-kelas.blade.php` | Halaman pengaturan siswa per kelas (add/remove). |
| Add ke Kelas | `waka.manajemen-siswa.add-to-kelas` | POST | `@addToKelas` | redirect | Tambah siswa ke kelas. |
| Remove dari Kelas | `waka.manajemen-siswa.remove-from-kelas` | POST | `@removeFromKelas` | redirect | Lepas siswa dari kelas. |
| Assign Kelas (single) | `waka.manajemen-siswa.assign-kelas` | POST | `@assignKelas` | redirect | Set `kelas_id` siswa. |
| Attach Orang Tua | `waka.manajemen-siswa.attach-parent` | POST | `@attachParent` | redirect | Buat `StudentParent` link. |
| Detach Orang Tua | `waka.manajemen-siswa.detach-parent` | DELETE | `@detachParent` | redirect | Hapus link. |
| Cetak Daftar | `waka.manajemen-siswa.print` | GET | `@print` | `waka/manajemen-siswa/print.blade.php` | Layout cetak siswa (filter kelas/sort). |
| Cetak Kartu Siswa | `waka.manajemen-siswa.print-kartu` | GET | `@printKartu` | `waka/manajemen-siswa/print-kartu.blade.php` | Render kartu pelajar (1 siswa). |

**Catatan**: **Tidak ada bulk-assign** seperti Admin. Mirror logika: `admin.manajemen-siswa.*`. Data terfilter cabang Waka. Fokus pada **penempatan kelas & relasi orang tua** (bukan CRUD akun siswa — itu admin-only via Manajemen User).

---

### Pengaturan Istirahat

**Index view**: `waka/pengaturan-istirahat/index.blade.php` · **Controller**: `WakilKepalaSekolah/PengaturanIstirahatController.php`

**Tampilan index**: Tombol Kembali ke Jadwal Pelajaran. Info card biru: aturan ("Max 2 istirahat per jenjang", "auto-blok slot jadwal", "auto-tampil highlight kuning di cetak jadwal"). Tombol **+ Tambah Waktu Istirahat** (primary). Konten: **section per jenjang** (KB/TKA/TKB/SD/SMP/SMA) — tiap section punya header dengan badge jenjang + tombol "+ Tambah Istirahat" (muncul bila count < 2). Tabel istirahat per jenjang: Urutan, Nama, Jam Mulai, Jam Selesai, Hari Aktif (badge per hari), Status (toggle), Aksi (Edit, Hapus).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `waka.pengaturan-istirahat.create` | GET | `@create` | `waka/pengaturan-istirahat/create.blade.php` | Form: jenjang, hari, jam mulai-selesai, label. |
| Simpan | `waka.pengaturan-istirahat.store` | POST | `@store` | redirect | Validasi bentrok jam + buat `PengaturanIstirahat`. |
| Edit | `waka.pengaturan-istirahat.edit` | GET | `@edit` | `waka/pengaturan-istirahat/edit.blade.php` | Form pre-fill. |
| Update | `waka.pengaturan-istirahat.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `waka.pengaturan-istirahat.destroy` | DELETE | `@destroy` | redirect | Hapus pengaturan. |
| Toggle status | `waka.pengaturan-istirahat.toggle-status` | POST | `@toggleStatus` | redirect | Aktif/non-aktifkan slot istirahat. |

**Catatan**: Tidak di sidebar utama — diakses lewat tombol **"Istirahat"** (oranye) di halaman index Jadwal Pelajaran. Mirror logika: `admin.pengaturan-istirahat.*`.

---

### Kenaikan Kelas → Pengaturan KKM

**Index view (aktual yang dirender)**: `admin/akademik/promotion/kkm.blade.php` · **Controller**: `Admin/Akademik/PromotionKKMController.php` (**shared dengan Admin**)

**Tampilan index**: Card **Daftar Mata Pelajaran & KKM** dengan header berisi: judul + meta TA aktif, dan dropdown filter **Jenjang** (PAUD/SD/SMP/SMA, auto-submit). Konten: form mass-edit KKM — tabel mapel × kolom (No, Mata Pelajaran, Jenjang, KKM Saat Ini, Set KKM Baru — input numeric per mapel). Tombol Submit di bawah → POST `kkm.store`. Empty state bila tidak ada mapel di jenjang terpilih.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Buka Pengaturan KKM | `waka.promotion.kkm.index` | GET | `@index` | `admin/akademik/promotion/kkm.blade.php` | Form atur KKM per mapel/jenjang (default `?jenjang=SMA`); data dari `pengaturan_kkm`. |
| Simpan KKM | `waka.promotion.kkm.store` | POST | `@store` | redirect | Upsert `pengaturan_kkm` per `(tahun_ajaran_id, jenjang, mata_pelajaran_id)`. Redirect dideteksi via `routeIs('waka.*')` → `waka.promotion.kkm.index`. |

**Catatan penting**: Controller `Admin\Akademik\PromotionKKMController` di-share. **View yang dirender adalah `admin/akademik/promotion/kkm.blade.php`** (bukan `waka/akademik/promotion/kkm.blade.php`) — karena `view('admin.akademik.promotion.kkm', ...)` hardcoded di controller. File `waka/akademik/promotion/kkm.blade.php` ada di disk tapi **orphaned** (tidak terpanggil). Logika redirect setelah `@store` mendeteksi role dari `routeIs('waka.*')` untuk menentukan prefix redirect.

---

### Kenaikan Kelas → Pengaturan Kenaikan (Settings)

**Index view (aktual)**: `admin/akademik/promotion/settings.blade.php` · **Controller**: `Admin/Akademik/PromotionSettingsController.php` (**shared dengan Admin**)

**Tampilan index**: Single form besar pengaturan kenaikan TA aktif. Field utama: tanggal pengambilan rapor, tanggal eksekusi otomatis (date), waktu eksekusi (time, default 02:00 AM bila kosong), persentase minimal tuntas (0-100). Tombol Submit → POST `settings.store` yang juga sinkronkan `PromotionSchedule` (auto-create/cancel).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Buka Pengaturan | `waka.promotion.settings.index` | GET | `@index` | `admin/akademik/promotion/settings.blade.php` | Form atur aturan kenaikan: tanggal pengambilan rapor, tanggal+waktu eksekusi otomatis, % minimal tuntas. |
| Simpan Settings | `waka.promotion.settings.store` | POST | `@store` | redirect | Upsert `pengaturan_naik_kelas` + sinkronkan `PromotionSchedule` (auto-create/cancel). Redirect via `routeIs('waka.*')` → `waka.promotion.settings.index`. |

**Catatan**: Sama dengan KKM — view yang dirender dari namespace **admin**. File `waka/akademik/promotion/settings.blade.php` ada tapi orphaned.

---

### Kenaikan Kelas → Proses & Rekap (Report)

**Index view (aktual)**: `admin/akademik/promotion/rekap.blade.php` · **Controller**: `Admin/Akademik/PromotionReportController.php` (**shared dengan Admin**)

**Tampilan index**: Filter atas: TA history, Cabang (auto-locked ke cabang Waka), Jenjang, Kelas, Status (NAIK/TIDAK/LULUS/TUNGGAKAN), search. Stat cards summary: jumlah per status kelulusan. Tabel **status kenaikan siswa**: checkbox + nama + kelas asal → kelas tujuan + nilai/% tuntas + flag dispensasi Ketua + status. Tombol header: **Eksekusi Promosi** (danger/primary, modal konfirmasi), **Cetak Rekap** (target blank). Aksi bulk pada terpilih: Rollback, Promote (untuk yang sebelumnya gagal tapi kini layak). Tombol per-baris: Rollback individual. Bila ada `PromotionSchedule` PENDING, muncul info schedule + tombol **Batalkan Jadwal**.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Buka Proses & Rekap | `waka.promotion.report` | GET | `@index` | `admin/akademik/promotion/rekap.blade.php` | Daftar status kenaikan + filter (kelas, jenjang, status). Auto-filter `cabang_id = waka.cabang_id` (cek `auth()->user()->role === 'wakil_kepala_sekolah'`). |
| Cetak Rekap | `waka.promotion.report.print` | GET | `@print` | `admin/akademik/promotion/print.blade.php` | Layout cetak rekap. |
| Eksekusi Promosi | `waka.promotion.execute` | POST | `@execute` | redirect | Pindah siswa ke kelas tujuan berdasarkan `pengaturan_kkm` + `pengaturan_naik_kelas` + flag `izin_khusus_ketua`. |
| Rollback (1 siswa) | `waka.promotion.rollback` | POST | `@rollback` | redirect | Kembalikan kelas asli + reset status. |
| Rollback Terpilih | `waka.promotion.rollback-selected` | POST | `@rollbackSelected` | redirect | Bulk rollback. |
| Promote Terpilih | `waka.promotion.promote-selected` | POST | `@promoteSelected` | redirect | Promote ulang siswa yg sebelumnya gagal tapi kini memenuhi syarat. |
| Batalkan Jadwal Promosi | `waka.promotion.cancel-schedule` | POST | `@cancelSchedule` | redirect | Batalkan `PromotionSchedule` (mode terjadwal). |

**Catatan**: Controller `Admin\Akademik\PromotionReportController` di-share. View dirender dari namespace **admin** (`admin/akademik/promotion/{rekap,print}.blade.php`). Untuk Waka, query otomatis terfilter ke `cabang_id` user. Lihat alur penuh di [flow.md §5.4](../../flow.md#54-kenaikan-kelas-khusus-adminbendahara--ketua-pkbm--adminwaka-eksekusi).

---

### Monitoring (Wali Kelas / Guru Pengajar / Siswa)

**Controller**: `WakilKepalaSekolah/WakilKepalaSekolahController.php`

**Tampilan index** (3 menu, pola seragam, dipowered partial `shared/monitoring/{wali-kelas|guru-pengajar|siswa}.blade.php`): Header dengan badge **scope cabang Waka** (auto-locked, beda dgn Ketua yg lihat semua cabang). 4 **summary card** sesuai konteks (mis. utk Wali Kelas: Total Kelas, Sudah Ada Wali, Belum, Total Guru Aktif). Card konten dengan **filter toolbar** (search + dropdown filter kontekstual + status) + tombol Cari & Reset. Tabel utama menampilkan status pengisian rapor/nilai/aktivitas LMS per record (read-only — tidak ada aksi modify).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Monitoring Wali Kelas | `waka.monitoring.wali-kelas` | GET | `@monitoringWaliKelas` | `waka/monitoring/wali-kelas.blade.php` | Status pengisian rapor/nilai per wali (scoped cabang). |
| Monitoring Guru Pengajar | `waka.monitoring.guru-pengajar` | GET | `@monitoringGuruPengajar` | `waka/monitoring/guru-pengajar.blade.php` | Status pengisian materi/tugas/nilai per guru. |
| Monitoring Siswa | `waka.monitoring.siswa` | GET | `@monitoringSiswa` | `waka/monitoring/siswa.blade.php` | Aktivitas LMS siswa. |

**Catatan**: **Tidak ada Monitoring Pengguna** seperti Admin/Ketua. Semua data terfilter cabang Waka. Mirror logika: `admin.monitoring.*` & `ketua.monitoring.*`.

---

### Monitoring LMS

**Controller**: `WakilKepalaSekolah/WakilKepalaSekolahController.php`

**Tampilan index**: Filter bar dengan **search** (nama/kode kelas), dropdown **Tahun Ajaran**, tombol Terapkan, toggle **"Hanya tampilkan kelas yang sudah punya konten LMS"**. 5 **stat card**: Total Kelas (scope cabang Waka), Materi, Tugas, Latihan, Ujian. **Grid kartu kelas** — tiap kartu: badge Jenjang, badge TA (highlight bila TA aktif), nama kelas, cabang, wali kelas, stat pill jumlah konten LMS atau "Belum ada konten". Klik kartu → halaman detail per-kelas (`lms.kelas`).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Overview LMS | `waka.monitoring.lms.index` | GET | `@lmsIndex` | `monitoring-lms/index.blade.php` (shared root) | Overview LMS lintas kelas, scope cabang waka. |
| Detail LMS per Kelas | `waka.monitoring.lms.kelas` | GET | `@lmsKelas` | `monitoring-lms/kelas-detail.blade.php` (shared root) | Detail konten LMS 1 kelas (materi/tugas/ujian). |
| Preview Materi/Tugas/Ujian | `waka.monitoring.lms.preview` | GET | `@lmsPreview` | view shared | Preview konten LMS (type: materi/tugas/ujian). |
| Kirim Catatan ke Pengelola | `waka.monitoring.lms.catatan` | POST | `@lmsKirimCatatan` | redirect | Kirim catatan/teguran ke wali/guru terkait konten LMS. Muncul di sidebar Guru sebagai "Catatan Monitoring". |

**Catatan**: View `monitoring-lms/*.blade.php` di root di-share Admin, Ketua, Waka. Konteks role (sidebar partial, base route, scope cabang) diset via `lmsViewContext()`. Mirror: `admin.monitoring.lms.*` & `ketua.monitoring.lms.*`.

---

### Catatan / Teguran

**Index view**: `waka/catatan/index.blade.php` (standalone per-role, tanpa shared partial) - **Controller**: `WakilKepalaSekolah/WakilKepalaSekolahController.php`

**Tampilan index**: Toolbar header **Manajemen Catatan** + tombol kanan **+ Buat Catatan** (primary). 4 **stat card**: Total Catatan, Publik (ke semua pengguna), Total Dibaca, Mendesak (prioritas tinggi). Card **Daftar Catatan** (Waka punya parameter `showDirection=true` — beda dgn Ketua/Admin — sehingga tampil arah panah Terkirim/Dari pengirim). List card per catatan: judul, waktu, prioritas, badge target audience (Semua/Role/Individu), excerpt isi, tombol Detail/Hapus.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Catatan | `waka.catatan.create` | GET | `@catatanCreate` | `waka/catatan/create.blade.php` | Form: judul, isi, prioritas, tipe penerima (semua/role/individu), penerima_ids (tenaga pendidik / siswa). |
| Kirim | `waka.catatan.store` | POST | `@catatanStore` | redirect | Validasi + buat `Catatan` per penerima + trigger `NotificationService`. |
| Detail | `waka.catatan.show` | GET | `@catatanShow` | `waka/catatan/show.blade.php` | Detail catatan & status baca penerima. |
| Hapus | `waka.catatan.destroy` | DELETE | `@catatanDestroy` | redirect | Hapus catatan milik sendiri (filter `pengirim_id`). |

**Catatan**: Mirror logika: `admin.catatan.*`, `ketua.catatan.*`. Catatan tampil di sidebar penerima sebagai badge notifikasi.
