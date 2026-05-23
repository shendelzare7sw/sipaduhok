# Role: Bendahara

> Kembali ke [flow.md](../../flow.md) · Role `bendahara` · Level 2 · Prefix `/bendahara` · Route `bendahara.` · Middleware `role:bendahara`.

## Ringkasan Peran

Mengelola seluruh **keuangan**: tagihan, pembayaran (manual & Midtrans), konfigurasi pembayaran, laporan. Juga **gerbang keuangan** untuk akses akademik: memvalidasi apakah siswa boleh ikut ujian / menerima rapor berdasarkan status lunas, dan mengajukan **dispensasi** ke Ketua PKBM bila perlu kelonggaran.

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/bendahara/partials/sneat-sidebar-menu.blade.php`.
- Dashboard: `Bendahara\BendaharaController@dashboard` (route `bendahara.dashboard`).

## Peta Menu

| Grup | Menu | Route (index) | Controller@method | Model |
|---|---|---|---|---|
| Keuangan | Kelola Tagihan | `bendahara.tagihan.index` (+ bulk-create, generate-spp, create-custom, duplicate, import/template, show/edit/update, cetak) | `Bendahara\TagihanController` | `Tagihan`,`Siswa` |
| Keuangan | Tarik Tunggakan | `bendahara.tagihan.carryover` (+ `.preview`/`.execute`) | `Bendahara\TagihanController@carryoverIndex/...` | `Tagihan`,`TahunAjaran` |
| Keuangan | Kelola Pembayaran | `bendahara.pembayaran.index` (+ create/store, validasi, validasi-langsung, riwayat, cetak-kwitansi) | `Bendahara\PembayaranController` | `Pembayaran`,`Tagihan` |
| Keuangan | Config Pembayaran | `bendahara.info-pembayaran.index` (+ `.update`) | `Bendahara\InfoPembayaranController` | `InfoPembayaran`,`AppSetting` |
| Validasi Akses | Validasi Ujian & Rapor | `bendahara.validasi-akses.index` | `Bendahara\ValidasiAksesController@index` | `Siswa`,`PengaturanBatasPembayaran` |
| Kenaikan Kelas | Validasi Dispensasi | `bendahara.promotion.validation.index` (+ store/bulk/history) | `Bendahara\PromotionValidationController` | tbl `izin_naik_kelas_khusus` (DB::table), `StatusNaikKelasSiswa` |
| Laporan | Laporan Pembayaran | `bendahara.laporan.index` (+ `.cetak`) | `Bendahara\LaporanPembayaranController@index/cetak` | `Pembayaran` |
| Laporan | Rekap Tagihan | `bendahara.laporan.rekap-tagihan` | `Bendahara\LaporanPembayaranController@rekapTagihan` | `Tagihan` |
| Laporan | Siswa Belum Lunas | `bendahara.laporan.belum-lunas` | `Bendahara\LaporanPembayaranController@belumLunas` | `Tagihan`,`Siswa` |

View dir: `resources/views/bendahara/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Validasi Ujian & Rapor** (`bendahara.validasi-akses.*`) — gerbang keuangan akademik. Aksi (POST, di luar sidebar): `validasi-ujian`/`batalkan-ujian`, `validasi-rapor`/`batalkan-rapor`, `bulk-validasi-*`, `bulk-validasi-selected`, `reset`, `batas-pembayaran`, `dispensasi`.
  - `@validasiUjian`/`@validasiRapor` set `siswa.validasi_(ujian|rapor)_bendahara=true` → membuka akses ujian / melanjutkan rantai rapor.
  - `@updateBatasPembayaran` mengatur `PengaturanBatasPembayaran` (per periode: pts_ganjil/pas_ganjil/pts_genap/pas_genap/ujian_akhir) — menentukan tagihan apa yang wajib lunas sebelum periode tertentu.
  - **`@ajukanDispensasi`** (`bendahara.validasi-akses.dispensasi`, POST): siswa belum lunas tapi perlu kelonggaran → buat `PengajuanRaporKetua` (status `menunggu`) → **dilempar ke Ketua PKBM** untuk diputuskan. Lihat [flow.md §5.2](../../flow.md#52-dispensasi-keuangan-bendaharaadmin--ketua-pkbm).
- **Validasi Rapor** adalah **tingkat ke-2** dari rantai 3 tingkat (Wali Kelas → **Bendahara** → Ketua PKBM). Bendahara hanya cek sisi keuangan; persetujuan akhir di Ketua, penerbitan di Wali. Lihat [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua).
- **Validasi Dispensasi (Kenaikan Kelas)** — beda dari dispensasi rapor/ujian. Di sini Bendahara mengajukan izin (record tabel `izin_naik_kelas_khusus`) untuk siswa nunggak agar tetap bisa naik kelas; diputuskan **Ketua PKBM** (`ketua.promotion.approval.*`). Lihat [flow.md §5.4](../../flow.md#54-kenaikan-kelas-khusus-adminbendahara--ketua-pkbm--adminwaka-eksekusi).
- **Tarik Tunggakan (carryover)** — memindahkan tunggakan TA lama ke TA aktif (preview lalu execute) agar tagihan tidak hilang saat ganti tahun ajaran.
- **Pembayaran** mendukung input manual oleh Bendahara dan callback Midtrans (siswa/orang tua bayar online); `@validasi` mengonfirmasi bukti, `@cetakKwitansi` cetak PDF (dompdf).

> Hampir semua menu Bendahara di-*mirror* untuk Admin di bawah `admin.keuangan.*` dengan controller `Admin\Keuangan\*` (logika setara — admin extends bendahara controller, hanya override view path).

## Detail Sub-Halaman per Menu

> Setiap menu di sidebar Bendahara yang punya **halaman/aksi selain index** dirinci di sini: tombol di index, route, controller method, view file, dan ringkasan logika. Convention: route name di-prefix `bendahara.`; view path relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/`.

### Kelola Tagihan

**Index view**: `bendahara/tagihan/index.blade.php` · **Controller**: `Bendahara/TagihanController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Tagihan Siswa | `bendahara.tagihan.show` | GET | `@show` | `bendahara/tagihan/show.blade.php` | Rincian item tagihan + riwayat pembayaran 1 siswa. |
| Edit Tagihan | `bendahara.tagihan.edit` | GET | `@edit` | `bendahara/tagihan/edit.blade.php` | Form edit item-item tagihan (nominal, due date, status). |
| Update Tagihan | `bendahara.tagihan.update` | PUT | `@update` | redirect | Validasi & update item tagihan. |
| Hapus Item Tagihan | `bendahara.tagihan.destroy-item` | DELETE | `@destroyItem` | redirect | Hapus 1 baris item dari tagihan siswa. |
| Cetak Tagihan Siswa | `bendahara.tagihan.cetak` | GET | `@cetak` | `bendahara/tagihan/cetak.blade.php` | Layout cetak invoice 1 siswa (juga di-share dengan Admin). |
| Buat Massal | `bendahara.tagihan.bulk-create` / `.store` | GET/POST | `@bulkCreate` | `bendahara/tagihan/bulk-create.blade.php` | Form pilih kelas/jenjang → generate tagihan utk semua siswa di kelas tsb. |
| Tagihan Custom (per siswa) | `bendahara.tagihan.create-custom` · `.store-custom` | GET/POST | `@createCustom` · `@storeCustom` | `bendahara/tagihan/create-custom.blade.php` | Form custom: pilih siswa + isi item bebas (di luar SPP). |
| Generate SPP | `bendahara.tagihan.generate-spp` / `.store` | GET/POST | `@generateSppForm` · `@generateSpp` | `bendahara/tagihan/generate-spp.blade.php` | Form bulk-generate SPP bulanan untuk semua kelas/jenjang. |
| Duplikasi Tagihan (TA lain) | `bendahara.tagihan.duplicate` / `.store` | GET/POST | `@duplicateForm` · `@duplicate` | `bendahara/tagihan/duplicate.blade.php` | Copy struktur tagihan dari TA lama ke TA baru. |
| Cetak Laporan Tagihan | `bendahara.tagihan.cetak-laporan` | GET | `@cetakLaporan` | `bendahara/tagihan/cetak-laporan.blade.php` | Layout cetak rekap tagihan (filter cabang/jenjang/kelas). |
| API: Siswa per Kelas | `bendahara.tagihan.api.siswa-by-kelas` | GET | `@getSiswaByKelas` | JSON | Untuk dropdown AJAX di form bulk/custom. |
| API: Preview Tagihan | `bendahara.tagihan.api.tagihan-preview` | GET | `@getTagihanPreview` | JSON | Preview tagihan siswa terpilih sebelum simpan. |

**Catatan**: Tidak ada **Import Excel** & **Reset Tagihan** di Bendahara — fitur tsb. hanya tersedia di Admin (`admin.keuangan.tagihan.import` & `reset-tagihan`). Method `importForm`/`import`/`downloadTemplate`/`resetTagihan` ada di parent class tapi route Bendahara tidak meng-expose-nya. View `import.blade.php` & `bendahara/tagihan/index.blade.php` tidak punya tombol import.

### Tarik Tunggakan (Carryover)

**Index view**: `bendahara/tagihan/carryover.blade.php` · **Controller**: `Bendahara/TagihanController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Preview Tunggakan | `bendahara.tagihan.carryover.preview` | POST | `@carryoverPreview` | JSON / partial | Hitung total tunggakan TA sumber → tampilkan rincian siswa yang akan ditarik. |
| Eksekusi Carryover | `bendahara.tagihan.carryover.execute` | POST | `@carryoverExecute` | redirect | Insert tagihan tunggakan ke TA aktif (via `TunggakanCarryoverService`). |

**Catatan**: Memindahkan tunggakan TA lama ke TA aktif agar tagihan tidak hilang saat ganti tahun ajaran. Dilakukan dua-langkah (preview → execute) untuk audit. Mirror di Admin (`admin.keuangan.tagihan.carryover.*`).

---

### Kelola Pembayaran

**Index view**: `bendahara/pembayaran/index.blade.php` · **Controller**: `Bendahara/PembayaranController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Pembayaran | `bendahara.pembayaran.show` | GET | `@show` | `bendahara/pembayaran/show.blade.php` | Detail bukti pembayaran + status validasi. |
| Validasi (terima/tolak) | `bendahara.pembayaran.validasi` | POST | `@validasi` | redirect | Approve/reject pembayaran pending (status menjadi lunas/ditolak). |
| + Catat Pembayaran (per siswa) | `bendahara.pembayaran.create` | GET | `@create` | `bendahara/pembayaran/create.blade.php` | Form input pembayaran manual untuk siswa tertentu (cash di tempat / transfer manual). |
| Simpan Pembayaran | `bendahara.pembayaran.store` | POST | `@store` | redirect | Simpan `Pembayaran` (status pending menunggu validasi). |
| Validasi Langsung | `bendahara.pembayaran.validasi-langsung` | POST | `@validasiLangsung` | redirect | Buat + langsung approve (skip pending) — untuk pembayaran cash on the spot. |
| Riwayat per Siswa | `bendahara.pembayaran.riwayat-siswa` | GET | `@riwayatSiswa` | `bendahara/pembayaran/riwayat-siswa.blade.php` | Daftar semua pembayaran 1 siswa lintas TA. |
| Cetak Kwitansi | `bendahara.pembayaran.cetak-kwitansi` | GET | `@cetakKwitansi` | `bendahara/pembayaran/cetak-kwitansi.blade.php` | Layout cetak kwitansi 1 pembayaran (DomPDF-friendly). |

**Catatan**: Pembayaran juga bisa masuk via callback Midtrans (siswa/orang tua bayar online lewat portal SIA) — Bendahara me-monitor & memvalidasi di sini. Mirror di Admin (`admin.keuangan.pembayaran.*`); controller admin extend bendahara dengan hanya override view path.

---

### Config Pembayaran (Info Pembayaran)

**Index view**: `bendahara/info-pembayaran/index.blade.php` · **Controller**: `Bendahara/InfoPembayaranController.php` · **URL akses**: `/bendahara/config` (path `info-pembayaran` redirect 301 → `config`)

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Simpan Config | `bendahara.info-pembayaran.update` | POST | `@update` | redirect | Simpan API key Midtrans (server key, client key, mode sandbox/production) + daftar rekening bank (`InfoPembayaran`). |
| (Legacy) Update | `bendahara.info-pembayaran.legacy-update` | POST | `@update` | redirect | Alias lama untuk backward-compat. |

**Catatan**: Single-page form. Mirror di Admin (`admin.keuangan.info-pembayaran.*`) — controller admin terpisah tapi logika setara.

---

### Validasi Akses (Ujian & Rapor)

**Index view**: `bendahara/validasi-akses/index.blade.php` · **Controller**: `Bendahara/ValidasiAksesController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Validasi Ujian (1 siswa) | `bendahara.validasi-akses.validasi-ujian` | POST | `@validasiUjian` | redirect | Set `siswa.validasi_ujian_bendahara=true` → akses ujian terbuka. |
| Batalkan Ujian | `bendahara.validasi-akses.batalkan-ujian` | POST | `@batalkanUjian` | redirect | Cabut flag akses ujian. |
| Validasi Rapor (1 siswa) | `bendahara.validasi-akses.validasi-rapor` | POST | `@validasiRapor` | redirect | Set `siswa.validasi_rapor_bendahara=true` (tingkat-2 dari rantai validasi rapor). |
| Batalkan Rapor | `bendahara.validasi-akses.batalkan-rapor` | POST | `@batalkanRapor` | redirect | Cabut flag akses rapor. |
| Bulk Validasi Ujian (per kelas) | `bendahara.validasi-akses.bulk-validasi-ujian` | POST | `@bulkValidasiUjian` | redirect | Validasi semua siswa dalam kelas tertentu. |
| Bulk Validasi Rapor (per kelas) | `bendahara.validasi-akses.bulk-validasi-rapor` | POST | `@bulkValidasiRapor` | redirect | Validasi rapor semua siswa dalam kelas. |
| Bulk Validasi Terpilih | `bendahara.validasi-akses.bulk-validasi-selected` | POST | `@bulkValidasiSelected` | redirect | Validasi siswa dari checkbox (custom selection). |
| Reset Validasi | `bendahara.validasi-akses.reset` | POST | `@resetValidasi` | redirect | Reset semua flag akses (gunakan hati-hati — biasanya saat ganti periode). |
| Atur Batas Pembayaran | `bendahara.validasi-akses.batas-pembayaran` | POST | `@updateBatasPembayaran` | redirect | Simpan `PengaturanBatasPembayaran` per periode (pts_ganjil/pas_ganjil/pts_genap/pas_genap/ujian_akhir) — menentukan tagihan apa yang wajib lunas. |
| **Ajukan Dispensasi (ke Ketua)** | `bendahara.validasi-akses.dispensasi` | POST | `@ajukanDispensasi` | redirect | Buat `PengajuanRaporKetua` (status `menunggu`) untuk siswa belum lunas yang butuh kelonggaran → dilempar ke **Ketua PKBM** untuk diputuskan (lihat [flow.md §5.2](../../flow.md#52-dispensasi-keuangan-bendaharaadmin--ketua-pkbm)). |

**Catatan**: Semua aksi adalah POST/AJAX dari halaman index (modal/inline). Hanya **satu view file** (`index.blade.php`) di folder ini. Mirror di Admin (`admin.keuangan.validasi-akses.*`) — admin extend controller bendahara. Wali Kelas juga punya menu mirip (`wali.validasi-akses.*`) tapi scope dibatasi kelas yang diwalikan.

---

### Laporan Pembayaran

**Index view**: `bendahara/laporan/index.blade.php` · **Controller**: `Bendahara/LaporanPembayaranController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Cetak Laporan Pembayaran | `bendahara.laporan.cetak` | GET | `@cetak` | `bendahara/laporan/cetak.blade.php` | Layout cetak rekap pembayaran periode (filter tanggal, kelas, status). |
| Rekap Tagihan | `bendahara.laporan.rekap-tagihan` | GET | `@rekapTagihan` | `bendahara/laporan/rekap-tagihan.blade.php` | Rincian rekap nominal tagihan vs terbayar per kelas/TA. |
| Cetak Rekap Tagihan | `bendahara.laporan.cetak-rekap-tagihan` | GET | `@cetakRekapTagihan` | `bendahara/laporan/cetak-rekap-tagihan.blade.php` | Layout cetak rekap tagihan (DomPDF-friendly). |
| Siswa Belum Lunas | `bendahara.laporan.belum-lunas` | GET | `@belumLunas` | `bendahara/laporan/belum-lunas.blade.php` | Daftar siswa dengan tunggakan (filter periode/kelas). |
| Cetak Belum Lunas | `bendahara.laporan.cetak-belum-lunas` | GET | `@cetakBelumLunas` | `bendahara/laporan/cetak-belum-lunas.blade.php` | Layout cetak daftar tunggakan. |

**Catatan**: Mirror di Admin (`admin.keuangan.laporan.*`) — controller admin extend bendahara; method `cetak*` tidak di-override → view bendahara dipakai langsung dari namespace admin juga.

---

### Validasi Dispensasi (Kenaikan Kelas)

**Index view**: `bendahara/promotion/validation.blade.php` · **Controller**: `Bendahara/PromotionValidationController.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Setujui/Tolak Dispensasi (1) | `bendahara.promotion.validation.store` | POST | `@store` | redirect | Catat keputusan `izin_naik_kelas_khusus` utk satu siswa (alasan: tunggakan tapi tetap layak naik). |
| Bulk Setujui/Tolak | `bendahara.promotion.validation.bulk-store` | POST | `@bulkStore` | redirect | Bulk decision dari checkbox. |
| Riwayat | `bendahara.promotion.validation.history` | GET | `@history` | `bendahara/promotion/history.blade.php` | Daftar keputusan dispensasi lampau (audit trail). |

**Catatan**: Beda dari dispensasi rapor/ujian — di sini Bendahara mengajukan **izin naik kelas** untuk siswa nunggak agar tetap bisa naik kelas; diputuskan akhir oleh **Ketua PKBM** (`ketua.promotion.approval.*`). Lihat [flow.md §5.4](../../flow.md#54-kenaikan-kelas-khusus-adminbendahara--ketua-pkbm--adminwaka-eksekusi). Mirror di Admin (`admin.keuangan.promotion.validation.*`) — controller admin terpisah.
