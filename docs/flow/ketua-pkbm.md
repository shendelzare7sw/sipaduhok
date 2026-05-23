# Role: Ketua PKBM

> Kembali ke [flow.md](../../flow.md) · Role `ketua_pkbm` · Level 2 · Prefix `/ketua` · Route `ketua.` · Middleware `role:ketua_pkbm`.

## Ringkasan Peran

Pimpinan lembaga. Fokus pada **pengambilan keputusan akhir** (approval) dan **pemantauan**, bukan input data harian. Ketua adalah ujung dari beberapa rantai validasi: ia yang memutuskan **dispensasi keuangan**, **validasi rapor tingkat akhir**, dan **izin naik kelas khusus**. Selebihnya membaca monitoring & mencetak laporan.

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/ketua/partials/sneat-sidebar-menu.blade.php`.
- Badge: "Dispensasi Keuangan" = jumlah `PengajuanRaporKetua` status `menunggu`.
- Dashboard: `DashboardController@ketua` (route `ketua.dashboard`).

## Peta Menu

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| Kenaikan Kelas | Approval Dispensasi | `ketua.promotion.approval.index` (+ `.update`/`.bulk-update`/`.history`) | `Ketua\PromotionApprovalController@index/update/bulkUpdate/history` | tbl `izin_naik_kelas_khusus` (DB::table), `StatusNaikKelasSiswa` |
| Kenaikan Kelas | Validasi Rapor | `ketua.validasi-rapor.index` (+ `.validasi`/`.batalkan`/`.bulk-validasi`/`.validasi-semua`/`.preview`/`.minta-revisi`) | `Ketua\ValidasiRaporController` | `Rapor`,`Siswa` |
| Kenaikan Kelas | Dispensasi Keuangan | `ketua.dispensasi.index` (+ `.approve`/`.reject`) | `Ketua\ValidasiRaporController@dispensasiIndex/approveDispensasi/rejectDispensasi` | `PengajuanRaporKetua`,`Siswa` |
| Monitoring | Data Pengguna | `ketua.monitoring.pengguna` | `Ketua\KetuaController@monitoringPengguna` | `User` |
| Monitoring | Data Wali Kelas | `ketua.monitoring.wali-kelas` | `Ketua\KetuaController@monitoringWaliKelas` | `WaliKelasAssignment` |
| Monitoring | Data Guru Pengajar | `ketua.monitoring.guru-pengajar` | `Ketua\KetuaController@monitoringGuruPengajar` | `GuruPengajarKelas` |
| Monitoring | Data Siswa | `ketua.monitoring.siswa` | `Ketua\KetuaController@monitoringSiswa` | `Siswa` |
| Monitoring | Monitoring LMS | `ketua.monitoring.lms.index` (+ `.kelas`/`.preview`/`.catatan`) | `Ketua\KetuaController@lmsIndex/lmsKelas/lmsPreview/lmsKirimCatatan` | `Materi`,`Tugas`,`Ujian`,`CatatanMonitoring` |
| Laporan & Catatan | Cetak Laporan | `ketua.laporan.index` (+ `cetak-*`/`rekap-akademik`) | `Ketua\KetuaController@index/siswa/...` | berbagai |
| Laporan & Catatan | Kirim Catatan | `ketua.catatan.index` (+ create/store/show/destroy) | `Ketua\KetuaController@catatanIndex/...` | `Catatan`,`CatatanDibaca` |

View dir: `resources/views/ketua/`. Monitoring LMS pakai view `monitoring-lms/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Dispensasi Keuangan** — *menerima lemparan tanggung jawab* dari Bendahara/Admin. Bendahara (`bendahara.validasi-akses.dispensasi`) atau Admin membuat `PengajuanRaporKetua` (tipe `rapor`/`ujian`, status `menunggu`). Ketua `@approveDispensasi` → set `siswa.validasi_(rapor|ujian)_bendahara=true` (membuka akses meski belum lunas); `@rejectDispensasi` → status `ditolak`. Detail: [flow.md §5.2](../../flow.md#52-dispensasi-keuangan-bendaharaadmin--ketua-pkbm).
- **Validasi Rapor** — tingkat **ke-3 (terakhir)** dari rantai Wali Kelas → Bendahara → Ketua. `@validasiRapor` set `siswa.validasi_rapor_ketua=true`; `@mintaRevisi` mengembalikan ke Wali Kelas (set `Rapor.status_review_ketua='revisi'`, `catatan_revisi_ketua`, reset flag wali). Hanya setelah ini Wali boleh `terbitkan`. Detail: [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua).
- **Approval Dispensasi (Kenaikan Kelas)** — memutuskan record di tabel `izin_naik_kelas_khusus` (diajukan Admin/Bendahara, diakses via `DB::table`) untuk siswa yang tidak memenuhi syarat naik kelas; `@update`/`@bulkUpdate` set status DISETUJUI/DITOLAK & `StatusNaikKelasSiswa.izin_khusus_ketua`. Eksekusi promosi tetap di Admin/Waka. Detail: [flow.md §5.4](../../flow.md#54-kenaikan-kelas-khusus-adminbendahara--ketua-pkbm--adminwaka-eksekusi).
- **Monitoring & Cetak Laporan & Kirim Catatan** — read-only lintas semua role; "Kirim Catatan" = teguran/instruksi tertulis ke Guru/Wali (muncul sebagai badge "Catatan Monitoring" di sidebar Guru). Controller `Ketua\KetuaController` ini ber-pola sama dengan `Admin\MonitoringController` & `WakilKepalaSekolahController`.

## Detail Sub-Halaman per Menu

> Setiap menu di sidebar Ketua yang punya **halaman/aksi selain index** dirinci di sini. Convention: route name di-prefix `ketua.`; view path relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/`. Sebagian besar method Ketua adalah **POST/AJAX dari halaman index** (modal/inline) sehingga jarang ada view baru — fokus utama Ketua adalah pengambilan keputusan, bukan input data.

### Approval Dispensasi (Kenaikan Kelas)

**Index view**: `ketua/promotion/approval.blade.php` · **Controller**: `Ketua/PromotionApprovalController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Setujui/Tolak (1 siswa) | `ketua.promotion.approval.update` | PUT | `@update` | redirect | Update record `izin_naik_kelas_khusus` (status `DISETUJUI`/`DITOLAK`, alasan, pengaksi) + set `StatusNaikKelasSiswa.izin_khusus_ketua`. |
| Setujui/Tolak Massal | `ketua.promotion.approval.bulk-update` | PUT | `@bulkUpdate` | redirect | Bulk decision dari checkbox. |
| Riwayat Approval | `ketua.promotion.approval.history` | GET | `@history` | `ketua/promotion/history.blade.php` | Daftar keputusan dispensasi lampau (audit trail) dengan filter status/TA. |

**Catatan**: Record `izin_naik_kelas_khusus` dibuat oleh Bendahara (`bendahara.promotion.validation.*`) atau Admin (`admin.keuangan.promotion.validation.*`); Ketua hanya memutuskan. Eksekusi promosi tetap di Admin/Waka (`admin.akademik.promotion.execute` / `waka.promotion.execute`). Lihat alur penuh di [flow.md §5.4](../../flow.md#54-kenaikan-kelas-khusus-adminbendahara--ketua-pkbm--adminwaka-eksekusi).

---

### Validasi Rapor (Tingkat-3)

**Index view**: `ketua/validasi-rapor/index.blade.php` · **Controller**: `Ketua/ValidasiRaporController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Validasi Rapor (1 siswa) | `ketua.validasi-rapor.validasi` | POST | `@validasiRapor` | redirect | Set `siswa.validasi_rapor_ketua=true` → membuka jalan utk Wali Kelas `terbitkan` rapor. |
| Batalkan Validasi | `ketua.validasi-rapor.batalkan` | POST | `@batalkanRapor` | redirect | Reset flag `validasi_rapor_ketua=false`. |
| Bulk Validasi (terpilih) | `ketua.validasi-rapor.bulk-validasi` | POST | `@bulkValidasi` | redirect | Validasi banyak siswa sekaligus dari checkbox. |
| Validasi Semua (sekali klik) | `ketua.validasi-rapor.validasi-semua` | POST | `@validasiSemuaRapor` | redirect | Validasi semua siswa yang sudah lolos Wali + Bendahara di TA aktif. |
| Preview Rapor | `ketua.validasi-rapor.preview` | GET | `@previewRapor` | `wali-kelas/rapor/preview-pts.blade.php` atau `preview-pas.blade.php` | Preview draft rapor siswa (PTS / PAS — auto-pilih berdasarkan periode). **View di-share dari namespace wali-kelas** karena Ketua hanya membaca. |
| Minta Revisi | `ketua.validasi-rapor.minta-revisi` | POST | `@mintaRevisi` | redirect | Set `Rapor.status_review_ketua='revisi'`, isi `catatan_revisi_ketua`, reset flag validasi wali → rapor balik ke Wali Kelas untuk diperbaiki. |

**Catatan**: Tingkat **ke-3 (terakhir)** dari rantai validasi rapor: Wali Kelas → Bendahara → **Ketua**. Setelah Ketua memvalidasi, Wali Kelas baru bisa `terbitkan` rapor agar tampil di portal orang tua. View `preview-pts/pas.blade.php` di-share dari `wali-kelas/rapor/` (Ketua hanya read-only, tidak edit). Lihat [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua).

---

### Dispensasi Keuangan (Approve/Reject Pengajuan)

**Index view**: `ketua/dispensasi/index.blade.php` · **Controller**: `Ketua/ValidasiRaporController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Approve Dispensasi | `ketua.dispensasi.approve` | POST | `@approveDispensasi` | redirect | Set `PengajuanRaporKetua.status='disetujui'` + set `siswa.validasi_(rapor|ujian)_bendahara=true` (membuka akses meski belum lunas). |
| Reject Dispensasi | `ketua.dispensasi.reject` | POST | `@rejectDispensasi` | redirect | Set `PengajuanRaporKetua.status='ditolak'` + simpan alasan; siswa tetap terblokir akses. |

**Catatan**: *Menerima lemparan tanggung jawab* dari Bendahara (`bendahara.validasi-akses.dispensasi`) atau Admin. Pengajuan berisi `tipe` (rapor/ujian), alasan, periode. Badge sidebar Ketua = jumlah `PengajuanRaporKetua` status `menunggu`. Lihat [flow.md §5.2](../../flow.md#52-dispensasi-keuangan-bendaharaadmin--ketua-pkbm).

---

### Monitoring (Pengguna / Wali Kelas / Guru Pengajar / Siswa)

**Controller**: `Ketua/KetuaController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Monitoring Pengguna | `ketua.monitoring.pengguna` | GET | `@monitoringPengguna` | `ketua/monitoring/pengguna.blade.php` | Dashboard last-active, role, status user (tenaga pendidik & siswa). |
| Monitoring Wali Kelas | `ketua.monitoring.wali-kelas` | GET | `@monitoringWaliKelas` | `ketua/monitoring/wali-kelas.blade.php` | Status pengisian rapor/nilai per wali, filter cabang. |
| Monitoring Guru Pengajar | `ketua.monitoring.guru-pengajar` | GET | `@monitoringGuruPengajar` | `ketua/monitoring/guru-pengajar.blade.php` | Status pengisian materi/tugas/nilai per guru. |
| Monitoring Siswa | `ketua.monitoring.siswa` | GET | `@monitoringSiswa` | `ketua/monitoring/siswa.blade.php` | Aktivitas LMS siswa (per cabang & kelas). |

**Catatan**: Read-only — tidak ada aksi modify. Mirror penuh: Admin `extends KetuaController` → `admin.monitoring.*` me-render `admin/monitoring/*` (wrap-view). Waka punya versi terpisah (3 dari 4: tanpa pengguna).

---

### Monitoring LMS

**Controller**: `Ketua/KetuaController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Overview LMS | `ketua.monitoring.lms.index` | GET | `@lmsIndex` | `monitoring-lms/index.blade.php` (shared root) | Overview konten LMS lintas kelas + filter TA & "hanya kelas berisi konten". |
| Detail LMS per Kelas | `ketua.monitoring.lms.kelas` | GET | `@lmsKelas` | `monitoring-lms/kelas-detail.blade.php` (shared root) | Detail konten LMS 1 kelas (materi/tugas/ujian) dengan filter & breakdown per mapel. |
| Preview Materi/Tugas/Ujian | `ketua.monitoring.lms.preview` | GET | `@lmsPreview` | view shared | Preview konten LMS (type: `materi`/`tugas`/`ujian`). |
| Kirim Catatan ke Pengelola | `ketua.monitoring.lms.catatan` | POST | `@lmsKirimCatatan` | redirect | Kirim catatan/teguran ke wali/guru terkait konten LMS. Muncul di sidebar Guru sebagai "Catatan Monitoring". |

**Catatan**: View `monitoring-lms/*.blade.php` di root di-share Admin/Ketua/Waka. Konteks role (sidebar partial, base route, scope cabang) diset via `lmsViewContext()` di KetuaController.

---

### Cetak Laporan

**Index view**: `ketua/laporan/index.blade.php` · **Controller**: `Ketua/KetuaController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Cetak Siswa | `ketua.laporan.siswa` | GET | `@siswa` | `ketua/laporan/print-siswa.blade.php` | Layout cetak daftar siswa (filter cabang/kelas/jenjang/sort). |
| Cetak Tenaga Pendidik | `ketua.laporan.tenaga-pendidik` | GET | `@tenagaPendidik` | `ketua/laporan/print-guru.blade.php` | Layout cetak tenaga pendidik (filter role). |
| Cetak Kelas | `ketua.laporan.kelas` | GET | `@kelas` | `ketua/laporan/print-kelas.blade.php` | Layout cetak daftar kelas. |
| Cetak Wali Kelas | `ketua.laporan.wali-kelas` | GET | `@waliKelas` | `ketua/laporan/print-wali-kelas.blade.php` | Layout cetak wali kelas per kelas. |
| Cetak Guru Pengajar | `ketua.laporan.guru-pengajar` | GET | `@guruPengajar` | `ketua/laporan/print-guru-pengajar.blade.php` | Layout cetak guru pengajar per cabang/TA. |
| Cetak Rekap | `ketua.laporan.rekap` | GET | `@rekap` | `ketua/laporan/print-rekap.blade.php` | Rekap statistik per cabang/jenjang. |
| Cetak Rekap Akademik | `ketua.laporan.rekap-akademik` | GET | `@rekapAkademik` | `ketua/laporan/print-rekap-akademik.blade.php` | Rekap akademik (kelulusan, naik kelas, dsb.). |

**Catatan**: Admin **mewarisi** controller ini (`Admin\MonitoringController extends Ketua\KetuaController`). Method `@siswa/@tenagaPendidik/@kelas/@waliKelas/@guruPengajar/@rekap` di Admin hanya passthrough → ketika `admin.laporan.*` dipanggil, view yang dirender tetap dari namespace **`ketua/laporan/print-*.blade.php`** (lihat catatan di [admin.md](admin.md) §Laporan).

---

### Kirim Catatan / Teguran

**Index view**: `ketua/catatan/index.blade.php` · **Controller**: `Ketua/KetuaController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Catatan | `ketua.catatan.create` | GET | `@catatanCreate` | `ketua/catatan/create.blade.php` | Form: judul, isi, prioritas (`biasa`/`penting`/`mendesak`), tipe penerima (`semua`/`role`/`individu`), `role_penerima` atau `penerima_ids` (multi). |
| Kirim | `ketua.catatan.store` | POST | `@catatanStore` | redirect | Validasi + buat satu/banyak `Catatan` (per penerima bila tipe `individu`) + trigger `NotificationService->notifyCatatan()`. |
| Detail | `ketua.catatan.show` | GET | `@catatanShow` | `ketua/catatan/show.blade.php` | Detail catatan & status baca penerima (`CatatanDibaca`). |
| Hapus | `ketua.catatan.destroy` | DELETE | `@catatanDestroy` | redirect | Hapus catatan milik sendiri (filter `pengirim_id = auth()->id()`). |

**Catatan**: Catatan tampil di sidebar penerima sebagai badge notifikasi. Mirror penuh: `admin.catatan.*` (Admin extend Ketua), `waka.catatan.*` (controller terpisah, logika setara). View root `shared/catatan/index.blade.php` dipakai role konsumen lain (penerima).
