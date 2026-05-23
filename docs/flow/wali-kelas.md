# Role: Wali Kelas

> Kembali ke [flow.md](../../flow.md) · Role `wali_kelas` · Level 4 · Prefix `/wali` · Route `wali.` · Middleware `role:wali_kelas`.

## Ringkasan Peran

Penanggung jawab satu/beberapa kelas. Mengelola **presensi**, **nilai**, dan **rapor** kelasnya; memulai rantai validasi rapor; memvalidasi izin & akses akademik siswa. Wali Kelas adalah `TenagaPendidik` yang ditugaskan via `WaliKelasAssignment` (bisa pegang >1 kelas → ada fitur "Pilih/Ganti Kelas" berbasis session `wali_kelas_selected`).

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/wali-kelas/partials/sneat-sidebar-menu.blade.php`.
- Sidebar **dinamis**: jika pegang >1 kelas, muncul kotak "Kelas Aktif" + tombol "Ganti Kelas" + menu "Pilih Kelas".
- Badge: "Rapor Pending Saya" (jumlah `Rapor` draft / `status_review_ketua='revisi'` di kelasnya, lintas TA), "Request Download" (`RequestDownloadRapor` status `menunggu`).
- Dashboard: `WaliKelas\WaliKelasController@dashboard`.

## Peta Menu

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| — | Dashboard / Pilih Kelas | `wali.dashboard` · `wali.pilih-kelas` (+ `.select`) | `WaliKelasController@dashboard` · `PilihKelasController@index/select` | `WaliKelasAssignment`,`Kelas` |
| Akademik | Jadwal Pelajaran (read-only) | `wali.jadwal.index` (+ `.print`) | `WaliKelas\JadwalPelajaranController@index/print` | `JadwalPelajaran` |
| Akademik | Presensi Siswa → Input Harian | `wali.presensi.index` | `WaliKelas\PresensiController@index` | `Presensi` |
| Akademik | Presensi → Validasi Izin | `wali.presensi.validasi-izin` (+ `proses-validasi-izin`, `preview-bukti`) | `PresensiController@validasiIzin/prosesValidasiIzin` | `Presensi` |
| Akademik | Presensi → Rekap Harian | `wali.presensi.rekap-harian` (+ `show-harian`, `print-rekap`) | `PresensiController@rekapHarian` | `Presensi` |
| Akademik | Presensi → Riwayat & Edit | `wali.presensi.riwayat` (+ `riwayat.update`, import-excel) | `PresensiController@riwayat/updateRiwayat` | `Presensi` |
| Akademik | Nilai Siswa | `wali.nilai.index` (+ show/edit/update, import, clear, print) | `WaliKelas\NilaiController` | `Nilai`,`Siswa`,`MataPelajaran` |
| Akademik | Kelola Rapor | `wali.rapor.index` | `WaliKelas\RaporController@index` | `Rapor`,`RaporNilai` |
| Akademik | Arsip Kelas Saya | `wali.arsip.index` (+ show/rapor/presensi/nilai) | `WaliKelas\WaliKelasArsipController` | `Kelas`,`Rapor` (lintas TA) |
| Akademik | Rapor Pending Saya | `wali.rapor-pending` | `WaliKelasController@raporPending` | `Rapor` |
| Akademik | Request Download | `wali.rapor.request-download.index` (+ approve/reject) | `WaliKelas\RaporController@requestDownloadIndex/approveDownload/rejectDownload` | `RequestDownloadRapor` |
| Kenaikan Kelas | Prediksi Kenaikan | `wali.promotion.prediction` | `WaliKelas\PromotionController@index` | `StatusNaikKelasSiswa`,`Nilai` |
| Validasi | Validasi Akses | `wali.validasi-akses.index` | `WaliKelas\ValidasiAksesController@index` | `Siswa` |

> Tidak di sidebar tapi ada: `wali.template-capaian.*` (`TemplateCapaianController`, template capaian kompetensi untuk rapor).

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Kelola Rapor** — pusat rantai validasi rapor (lihat [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua)). Aksi penting (di halaman ini, bukan menu terpisah):
  - `wali.rapor.generate-all`/`generate-single` → buat `Rapor` status `draft` dari data `Nilai` + rekap `Presensi`.
  - `wali.rapor.kirim-validasi` (+ `kirim-validasi-semua`) → set `siswa.validasi_rapor_wali=true`, lempar ke **Bendahara** lalu **Ketua PKBM**.
  - `wali.rapor.terbitkan` → hanya boleh setelah `validasi_rapor_ketua=true`; set `Rapor.status='diterbitkan'`, `allow_download=true` → orang tua bisa unduh.
  - `wali.rapor.tarik-kembali`, `batalkan-kirim-validasi`, `apply-template`, `reset-nilai` untuk koreksi.
  - Jika Ketua "minta revisi", rapor kembali dengan `status_review_ketua='revisi'` & muncul di "Rapor Pending Saya".
- **Validasi Akses** (`wali.validasi-akses.*`) — sisi **akademik** dari gerbang akses ujian/rapor (komplemen sisi keuangan Bendahara). Set `siswa.validasi_(ujian|rapor)_wali=true` (individual/bulk/semua). Lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa).
- **Validasi Izin (Presensi)** — orang tua mengajukan izin anak (`orang-tua.presensi.store-izin`); Wali Kelas yang **memproses/menyetujui** lewat `proses-validasi-izin` (lihat `bukti_file`). Contoh lempar tanggung jawab antar role.
- **Arsip Kelas Saya / Rapor Pending Saya** — akses **lintas Tahun Ajaran** ke kelas yang dulu pernah diwalikan (read-only) dan rapor TA lalu yang belum tuntas, agar kewajiban historis tetap bisa diselesaikan.
- **Pilih/Ganti Kelas** — bila wali pegang banyak kelas, kelas aktif disimpan di session; semua menu lain mengikuti kelas terpilih.

## Detail Sub-Halaman per Menu

> Setiap menu di sidebar Wali Kelas yang punya **halaman/aksi selain index** dirinci di sini. Convention: route name di-prefix `wali.`; view path relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/WaliKelas/`. Semua menu **otomatis scoped ke kelas terpilih** (`session('wali_kelas_selected')`) — bila wali pegang >1 kelas dan belum memilih, redirect ke `wali.pilih-kelas`.

### Pilih Kelas

**Index view**: `wali-kelas/pilih-kelas/index.blade.php` · **Controller**: `PilihKelasController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Pilih Kelas (klik kartu) | `wali.pilih-kelas.select` | POST | `@select` | redirect | Set `session('wali_kelas_selected', $kelas->id)` → arahkan ke dashboard. Validasi: kelas harus milik wali ini (`WaliKelasAssignment`). |

**Catatan**: Hanya muncul di sidebar bila wali pegang >1 kelas (atau dipanggil otomatis dari menu lain saat session kosong). Index menampilkan daftar kelas yg ditugaskan + status (aktif/arsip). Tombol "Ganti Kelas" di sidebar juga me-link ke sini.

---

### Jadwal Pelajaran (Read-Only)

**Index view**: `wali-kelas/jadwal/index.blade.php` · **Controller**: `JadwalPelajaranController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Cetak Jadwal | `wali.jadwal.print` | GET | `@print` | `wali-kelas/jadwal/print.blade.php` | Layout cetak jadwal mingguan kelas terpilih (DomPDF-friendly). |
| (Alias backward-compat) | `wali.jadwal-pelajaran` | GET | `@index` | `wali-kelas/jadwal/index.blade.php` | Route lama; dipakai oleh menu dashboard lama. |

**Catatan**: Read-only — data jadwal dikelola oleh Admin/Waka (`admin.jadwal-pelajaran.*` / `waka.jadwal-pelajaran.*`). Wali Kelas hanya membaca jadwal kelas yang diwalikan. Method `WaliKelasController@jadwalPelajaran` & view `wali-kelas/jadwal-pelajaran.blade.php` (top-level) ada di disk tapi **tidak ter-bind ke route apa pun** — orphaned/legacy.

---

### Presensi Siswa → Input Harian

**Index view**: `wali-kelas/presensi/index.blade.php` · **Controller**: `PresensiController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Update Presensi (ubah cell) | `wali.presensi.update` | POST | `@updatePresensi` | redirect | Update status hadir/izin/sakit/alpha utk 1 siswa pada 1 tanggal. |
| Input Harian (batch hari ini) | `wali.presensi.input-harian` | POST | `@inputHarian` | redirect | Submit batch presensi 1 hari penuh utk semua siswa di kelas. |

**Catatan**: Halaman utama presensi — grid siswa × tanggal. Semua aksi via POST inline (tidak ada view baru). Scope: kelas terpilih + bulan terpilih.

### Presensi → Validasi Izin

**Index view**: `wali-kelas/presensi/validasi-izin.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Preview Bukti Izin | `wali.presensi.preview-bukti` | GET | `@previewBukti` | file stream | Stream gambar/PDF bukti izin yang di-upload orang tua. |
| Proses Validasi (terima/tolak) | `wali.presensi.proses-validasi-izin` | POST | `@prosesValidasiIzin` | redirect | Approve → status `izin`, masukkan ke rekap. Reject → `alpha`, kirim catatan ke orang tua. |

**Catatan**: Orang tua mengajukan izin via `orang-tua.presensi.store-izin` → muncul di sini sebagai pending. Lempar tanggung jawab antar role.

### Presensi → Rekap Harian

**Index view**: `wali-kelas/presensi/rekap-harian.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Lihat Detail Hari | `wali.presensi.show-harian` | GET | `@showHarian` | `wali-kelas/presensi/show-harian.blade.php` | Detail presensi 1 tanggal: siswa × status + jumlah. |
| Cetak Rekap | `wali.presensi.print-rekap` | GET | `@printRekap` | `wali-kelas/presensi/print-rekap.blade.php` | Layout cetak rekap presensi bulanan/periode (filter rentang tanggal). |

**Catatan**: Rekap dihitung dari tabel `Presensi` kelas terpilih. Bisa filter bulan + tampilan harian/bulanan.

### Presensi → Riwayat & Edit

**Index view**: `wali-kelas/presensi/riwayat.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Edit Riwayat (1 entri) | `wali.presensi.riwayat.update` | PUT | `@updateRiwayat` | redirect | Update presensi historis (mis. koreksi setelah validasi izin terlambat). |
| Download Template Excel | `wali.presensi.download-template` | GET | `@downloadTemplate` | file download | Template `.xlsx` utk import presensi. |
| Import Excel | `wali.presensi.import-excel` | POST | `@importExcel` | redirect | Upload `.xlsx`, parse via Maatwebsite, batch update presensi. |

**Catatan**: Halaman edit-historis (bukan input harian). Berguna utk koreksi missed entries.

---

### Nilai Siswa

**Index view**: `wali-kelas/nilai/index.blade.php` · **Controller**: `NilaiController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Nilai Siswa | `wali.nilai.show` | GET | `@show` | `wali-kelas/nilai/show.blade.php` | Detail nilai 1 siswa lintas mapel (semester aktif). |
| Edit Nilai Siswa | `wali.nilai.edit` | GET | `@edit` | `wali-kelas/nilai/edit.blade.php` | Form edit nilai per mapel (tugas, UH, UTS, UAS). |
| Update Nilai | `wali.nilai.update` | PUT | `@update` | redirect | Validasi (range 0-100) + simpan ke `Nilai`. |
| Hapus Nilai (1 entri) | `wali.nilai.clear` | POST | `@clearNilai` | redirect | Reset nilai komponen tertentu jadi NULL (bukan delete row, hanya clear angka). |
| Cetak Semua Nilai | `wali.nilai.print` | GET | `@print` | `wali-kelas/nilai/print-all.blade.php` (mode default) atau `print-detail.blade.php` (bila `?mata_pelajaran_id=`) | Layout cetak rekap nilai 1 kelas: semua mapel (default) atau detail per mapel (bila filter). |
| Cetak Nilai per Siswa | `wali.nilai.print-siswa` | GET | `@printSiswa` | `wali-kelas/nilai/print.blade.php` | Layout cetak nilai 1 siswa (semua mapel). |
| Download Template Excel | `wali.nilai.download-template` | GET | `@downloadTemplate` | file download | Template `.xlsx` per siswa utk import nilai. |
| Import Excel | `wali.nilai.import` | POST | `@importExcel` | redirect | Upload `.xlsx` per siswa, parse & simpan ke `Nilai`. |

**Catatan**: View `wali-kelas/nilai/print-detail.blade.php` **direferensikan controller tapi file-nya TIDAK ADA di disk** → jika user akses `wali.nilai.print?mata_pelajaran_id=X` akan error `View [wali-kelas.nilai.print-detail] not found`. Sub-aksi `wali.nilai.print-detail` perlu dibuat view-nya atau di-fallback ke `print-all` di controller.

---

### Kelola Rapor

**Index view**: `wali-kelas/rapor/index.blade.php` · **Controller**: `RaporController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Generate Semua Rapor | `wali.rapor.generate-all` | POST | `@generateAll` | redirect | Buat `Rapor` status `draft` utk **semua siswa kelas** dari data `Nilai` + rekap `Presensi`. Skip yg sudah ada. |
| Generate per Siswa | `wali.rapor.generate-single` | POST | `@generateSingle` | redirect | Generate rapor 1 siswa saja (utk siswa baru/missed). |
| Buat dengan Mode (auto/upload) | `wali.rapor.create-with-mode` | POST | `@createWithMode` | redirect | Pilih mode: auto-generate dari Nilai ATAU upload PDF rapor manual. |
| Edit Rapor | `wali.rapor.edit` | GET | `@edit` | `wali-kelas/rapor/edit.blade.php` | Form edit komponen rapor: nilai per mapel, deskripsi capaian, kehadiran, ekstra. |
| Update Rapor | `wali.rapor.update` | PUT | `@update` | redirect | Validasi & update `Rapor` + `RaporNilai`. |
| Hapus Draft Rapor | `wali.rapor.destroy` | DELETE | `@destroy` | redirect | Hapus rapor (hanya boleh saat status `draft`; tidak untuk diterbitkan). |
| Preview Rapor | `wali.rapor.preview` | GET | `@preview` | `wali-kelas/rapor/preview-pts.blade.php` (PTS) atau `preview-pas.blade.php` (PAS) | Preview tampilan rapor sebelum cetak/terbitkan. View dipilih berdasarkan `rapor.jenis_rapor`. Ketua PKBM juga me-render view ini (`ketua.validasi-rapor.preview`). |
| Cetak Rapor | `wali.rapor.print` | GET | `@print` | `wali-kelas/rapor/print-pts.blade.php` (PTS) atau `print-pas.blade.php` (PAS) | Layout cetak rapor. Jika `input_mode='upload_pdf'`, serve PDF yang di-upload via `Storage::download`. |
| Export Excel | `wali.rapor.export-excel` | GET | `@exportExcel` | file download | Export rapor 1 siswa ke `.xlsx`. |
| Auto-fill Kehadiran | `wali.rapor.kehadiran-auto` | POST | `@autoFillKehadiran` | redirect | Isi kolom kehadiran rapor otomatis dari rekap `Presensi`. |
| Reset Nilai | `wali.rapor.reset-nilai` | POST | `@resetNilai` | redirect | Reset semua `RaporNilai` ke kosong (utk re-generate). |
| Reorder Nilai | `wali.rapor.reorder-nilai` | POST | `@reorderNilai` | JSON | Drag-and-drop urutan mapel di rapor. |
| Apply Template Capaian (1 rapor) | `wali.rapor.apply-template` | POST | `@applyTemplate` | redirect | Isi deskripsi capaian dari `TemplateCapaian` (`wali.template-capaian.*`). |
| Apply Template Semua | `wali.rapor.apply-template-all` | POST | `@applyTemplateToAll` | redirect | Bulk-apply template capaian ke semua rapor di kelas. |
| **Kirim Validasi (ke Bendahara→Ketua)** | `wali.rapor.kirim-validasi` | POST | `@kirimValidasi` | redirect | Set `siswa.validasi_rapor_wali=true` → rapor masuk antrian Bendahara → Ketua. Tingkat-1 dari rantai 3 tingkat. |
| Batalkan Kirim Validasi | `wali.rapor.batalkan-kirim-validasi` | POST | `@batalkanKirimValidasi` | redirect | Reset flag wali; tarik kembali dari antrian validasi. |
| Kirim Validasi Semua | `wali.rapor.kirim-validasi-semua` | POST | `@kirimValidasiSemua` | redirect | Bulk-kirim semua rapor draft di kelas yg sudah siap. |
| **Terbitkan Rapor** | `wali.rapor.terbitkan` | POST | `@terbitkan` | redirect | **Hanya boleh setelah `validasi_rapor_ketua=true`**. Set `Rapor.status='diterbitkan'` + `allow_download=true` → orang tua bisa unduh di portal SIA. |
| Tarik Kembali Rapor Terbitan | `wali.rapor.tarik-kembali` | POST | `@tarikKembali` | redirect | Reset status `diterbitkan` → `draft`; orang tua tidak bisa unduh lagi (utk koreksi pasca-terbit). |

**Catatan**: Pusat rantai validasi rapor 3-tingkat (lihat [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua)). View `wali-kelas/rapor/preview.blade.php` & `print.blade.php` (tanpa suffix `-pts`/`-pas`) ada di disk tapi **tidak direferensikan controller** — kemungkinan legacy/dead view.

### Kelola Rapor → Request Download

**Index view**: `wali-kelas/rapor/request-download.blade.php` · **Controller**: `RaporController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Approve Request | `wali.rapor.request-download.approve` | POST | `@approveDownload` | redirect | Set `RequestDownloadRapor.status='disetujui'` → orang tua bisa unduh PDF. |
| Reject Request | `wali.rapor.request-download.reject` | POST | `@rejectDownload` | redirect | Set status `ditolak` + alasan; orang tua dapat notifikasi. |

**Catatan**: Orang tua bisa request download rapor untuk arsip (`orang-tua.rapor.request-download`). Wali memutuskan. Badge sidebar = jumlah request `menunggu`.

---

### Rapor Pending Saya

**Index view**: `wali-kelas/rapor-pending/index.blade.php` · **Controller**: `WaliKelasController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Buka Rapor Pending | `wali.rapor-pending` | GET | `@raporPending` | `wali-kelas/rapor-pending/index.blade.php` | Daftar rapor `draft` atau `status_review_ketua='revisi'` **lintas TA** dari kelas yang pernah/sedang diwalikan. Klik baris → menu Edit Rapor. |

**Catatan**: Tidak ada sub-aksi di sini — semua link mengarah balik ke `wali.rapor.edit`. Berguna untuk menyelesaikan kewajiban TA lalu yang belum tuntas. Badge sidebar menampilkan jumlahnya.

---

### Arsip Kelas Saya

**Index view**: `wali-kelas/arsip/index.blade.php` · **Controller**: `WaliKelasArsipController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Kelas Arsip | `wali.arsip.show` | GET | `@show` | `wali-kelas/arsip/show.blade.php` | Overview kelas arsip (TA lalu): jumlah siswa, mapel, status rapor. |
| Tab: Rapor | `wali.arsip.rapor` | GET | `@rapor` | `wali-kelas/arsip/show.blade.php` (mode rapor) | Daftar rapor siswa kelas arsip (read-only). |
| Tab: Presensi | `wali.arsip.presensi` | GET | `@presensi` | `wali-kelas/arsip/show.blade.php` (mode presensi) | Rekap presensi kelas arsip. |
| Tab: Nilai | `wali.arsip.nilai` | GET | `@nilai` | `wali-kelas/arsip/show.blade.php` (mode nilai) | Daftar nilai kelas arsip. |

**Catatan**: **Read-only & lintas TA** — view `show.blade.php` di-share untuk 4 tab (mode dipilih lewat parameter). Berisi data kelas yang pernah diwalikan di TA lampau. Tidak ada aksi modify.

---

### Prediksi Kenaikan Kelas

**Index view**: `wali-kelas/promotion/index.blade.php` · **Controller**: `PromotionController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Buka Prediksi | `wali.promotion.prediction` | GET | `@index` | `wali-kelas/promotion/index.blade.php` | Tampil daftar siswa kelas + prediksi `NAIK`/`TIDAK`/`TUNGGAKAN` berdasarkan `Nilai` vs `pengaturan_kkm` + `pengaturan_naik_kelas` (preview saja — eksekusi tetap di Admin/Waka). |

**Catatan**: Read-only — tidak ada aksi keputusan. Membantu wali memahami posisi siswanya menjelang akhir TA. Eksekusi kenaikan di `admin.akademik.promotion.execute` / `waka.promotion.execute`.

---

### Validasi Akses Akademik

**Index view**: `wali-kelas/validasi-akses/index.blade.php` · **Controller**: `ValidasiAksesController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Validasi Ujian (1 siswa) | `wali.validasi-akses.validasi-ujian` | POST | `@validasiUjian` | redirect | Set `siswa.validasi_ujian_wali=true` (sisi akademik gerbang ujian). |
| Batalkan Ujian | `wali.validasi-akses.batalkan-ujian` | POST | `@batalkanUjian` | redirect | Cabut flag akses ujian sisi wali. |
| Bulk Validasi Ujian | `wali.validasi-akses.bulk-validasi-ujian` | POST | `@bulkValidasiUjian` | redirect | Validasi ujian banyak siswa dari checkbox. |
| Validasi Semua Ujian | `wali.validasi-akses.validasi-semua-ujian` | POST | `@validasiSemuaUjian` | redirect | Validasi semua siswa di kelas terpilih. |
| Validasi Rapor (1 siswa) | `wali.validasi-akses.validasi-rapor` | POST | `@validasiRapor` | redirect | Set `siswa.validasi_rapor_wali=true` (tingkat-1 rantai validasi rapor). |
| Batalkan Rapor | `wali.validasi-akses.batalkan-rapor` | POST | `@batalkanRapor` | redirect | Cabut flag akses rapor sisi wali. |
| Bulk Validasi Rapor | `wali.validasi-akses.bulk-validasi-rapor` | POST | `@bulkValidasiRapor` | redirect | Bulk dari checkbox. |
| Validasi Semua Rapor | `wali.validasi-akses.validasi-semua-rapor` | POST | `@validasiSemuaRapor` | redirect | Validasi semua siswa kelas. |

**Catatan**: Sisi **akademik** dari gerbang akses ujian/rapor (komplemen sisi keuangan Bendahara). Untuk ujian/rapor lolos akses, **kedua flag** (`*_wali` & `*_bendahara`) harus `true`. Lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa). Mirror logika di Bendahara/Admin tapi scope terbatas ke kelas yang diwalikan.

---

### Template Capaian Kompetensi

**Index view**: `wali-kelas/template-capaian/index.blade.php` · **Controller**: `TemplateCapaianController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah Template | (form di index) → `wali.template-capaian.store` | POST | `@store` | redirect | Simpan template deskripsi capaian (per mapel/kategori) — reusable utk rapor. |
| Update Template | `wali.template-capaian.update` | PUT | `@update` | redirect | Edit teks template. |
| Hapus Template | `wali.template-capaian.destroy` | DELETE | `@destroy` | redirect | Hapus template. |

**Catatan**: **Tidak di sidebar** — diakses via tombol/link dari halaman Edit Rapor saat hendak isi deskripsi capaian. Template dipakai oleh `wali.rapor.apply-template` & `apply-template-all` untuk auto-isi deskripsi capaian di rapor.
