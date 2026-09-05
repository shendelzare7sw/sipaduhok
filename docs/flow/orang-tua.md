# Role: Orang Tua / Wali

> Kembali ke [flow.md](../../flow.md) · Role `orang_tua` · Level 5 · Prefix `/wali-siswa` · Route `wali-siswa.` · Middleware `role:orang_tua`.

## Ringkasan Peran

Pendamping & penanggung jawab keuangan anak. Secara desain, **tanggung jawab pembayaran dan pemantauan rapor sengaja dialihkan dari Siswa ke Orang Tua** (lihat catatan di grup route wali-siswa: "Siswa hanya fokus belajar, tidak ada akses pembayaran"). Satu akun orang tua bisa memiliki >1 anak (relasi `User->children()` via `StudentParent`).

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/wali-siswa/partials/sneat-sidebar-menu.blade.php`.
- Sidebar **dinamis per anak**: untuk setiap `child` muncul submenu Presensi / Tagihan / Rapor. Bila belum ada anak tertaut → "Belum Ada Data Anak".
- Dashboard: `OrangTua\OrangTuaController@dashboard`. Seluruh fitur ada di satu controller `OrangTua\OrangTuaController`.

## Peta Menu

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| — | Dashboard | `wali-siswa.dashboard` | `OrangTuaController@dashboard` | `Siswa`,`StudentParent` |
| Monitoring Anak | [Anak] → Presensi | `wali-siswa.presensi.anak` (siswa) | `OrangTuaController@presensiAnak` | `Presensi` |
| Monitoring Anak | [Anak] → Tagihan | `wali-siswa.tagihan.anak` (siswa) | `OrangTuaController@tagihanAnak` | `Tagihan`,`Pembayaran` |
| Monitoring Anak | [Anak] → Rapor | `wali-siswa.rapor.anak` (siswa) | `OrangTuaController@raporAnak` | `Rapor` |

Aksi tambahan (di dalam halaman, bukan menu sidebar):

| Fungsi | Route | Controller@method |
|---|---|---|
| Bayar tagihan / bulk | `wali-siswa.tagihan.bayar` / `.bulk-pay` | `OrangTuaController@prosesBayar` / `processBulkPay` |
| Pembayaran digital (Midtrans Snap) | `wali-siswa.pembayaran.snap` / `.continue` / `.snap.finish` / `.invoice` | `OrangTuaController@snapPayment/continuePayment/snapFinish/cetakInvoice` |
| Detail rapor & unduh | `wali-siswa.rapor.detail` · `wali-siswa.rapor.request-download` · `wali-siswa.rapor.download` (token) | `OrangTuaController@detailRapor/requestDownloadRapor/downloadRapor` |
| Ajukan / kelola izin anak | `wali-siswa.presensi.ajukan-izin` · `.store-izin` · `.edit-izin` · `.update-izin` · `.riwayat-presensi` · `.riwayat-izin` | `OrangTuaController@ajukanIzin/storeIzin/editIzin/updateIzin/...` |

View dir: `resources/views/wali-siswa/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Tagihan & Pembayaran** — Orang tua membayar tagihan anak, manual maupun online via **Midtrans Snap** (`snapPayment` → callback `snap.finish`; webhook global `midtrans.notification` di luar auth & CSRF). Pembayaran ini memengaruhi status lunas yang dipakai **Bendahara** untuk validasi akses ujian/rapor. Jadi tindakan orang tua adalah hulu dari [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa).
- **Rapor Anak** — orang tua hanya bisa melihat/mengunduh rapor **setelah** rantai validasi 3 tingkat selesai dan Wali Kelas menerbitkannya (`Rapor.allow_download=true`). `requestDownloadRapor` membuat `RequestDownloadRapor` (status `menunggu`) yang harus **di-approve Wali Kelas** (`wali.rapor.request-download.approve`) sebelum `downloadRapor` via token aktif. Lihat [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua).
- **Pengajuan Izin Anak (Presensi)** — orang tua mengajukan izin/sakit (`storeIzin`, bisa lampirkan bukti); **Wali Kelas** yang memvalidasi/memproses (`wali.presensi.proses-validasi-izin`). Ini contoh jelas lempar tanggung jawab: siswa tidak bisa mengajukan izin sendiri (route siswa di-disable), harus lewat orang tua sebagai bentuk pendampingan, lalu diputuskan wali kelas.
- **Sidebar dinamis** — semua menu bergantung pada daftar anak (`children`). Saat menambah fitur, perhatikan parameter `{siswa}` selalu mengikat ke anak tertentu dan harus dicek kepemilikannya terhadap akun orang tua.

## Detail Sub-Halaman per Menu

> Semua menu Orang Tua **scoped per anak** (parameter `{siswa}` di URL — biasanya `siswaId`). Convention: route name di-prefix `wali-siswa.`; view path relatif terhadap `resources/views/`. Semua method ada di satu controller `OrangTua/OrangTuaController.php`. Tidak ada CRUD master data — orang tua **konsumen + pembayar + monitor**, bukan input data akademik.

### Dashboard

**Index view**: `wali-siswa/dashboard.blade.php` · **Controller**: `OrangTuaController@dashboard`

**Tampilan index**: **Grid kartu per anak** (`children` relasi `User->children()`). Tiap kartu berisi: avatar/inisial dengan gradient warna unik (rotasi 4 warna), nama anak + kelas + cabang, badge status keuangan (Lunas/Sebagian/Belum Lunas) atau badge ALUMNI bila status `lulus`, **3 finance mini-stat** (Total Tagihan, Dibayar, Sisa — dalam K/ribu), progress bar pembayaran (%), dan 4 tombol aksi cepat: **Tagihan**, **Rapor**, **Ajukan Izin** (disabled bila lulus, ganti jadi "Lulus"), **Riwayat Izin**. Bila orang tua punya >1 anak, di bawah grid muncul card **Ringkasan Keseluruhan** dengan total agregat semua anak. Bila belum ada anak tertaut: empty state "Belum Ada Data Anak".

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Tagihan (per anak) | `wali-siswa.tagihan.anak` | GET | `@tagihanAnak` | `wali-siswa/tagihan/index.blade.php` | Cross-link ke halaman tagihan anak terpilih. |
| Rapor (per anak) | `wali-siswa.rapor.anak` | GET | `@raporAnak` | `wali-siswa/rapor/index.blade.php` | Cross-link ke halaman rapor anak. |
| Ajukan Izin (per anak) | `wali-siswa.presensi.ajukan-izin` | GET | `@ajukanIzin` | `wali-siswa/presensi/ajukan-izin.blade.php` | Cross-link ke form pengajuan izin. |
| Riwayat Izin (per anak) | `wali-siswa.presensi.riwayat-izin` | GET | `@riwayatIzin` | `wali-siswa/presensi/riwayat-izin.blade.php` | Cross-link ke riwayat pengajuan izin. |

**Catatan**: Dashboard tidak punya CRUD action — semua tombol redirect ke sub-page per-anak. Variabel `$summary[$child->id]` adalah cache agregat finance per anak (dihitung sekali di controller).

---

### Tagihan Anak

**Index view**: `wali-siswa/tagihan/index.blade.php` · **Controller**: `OrangTuaController@tagihanAnak`

**Tampilan index**: Header dengan nama anak + kelas + tombol Kembali. 4 **stat card finance**: Tagihan Tahun Ini, Total Tunggakan (border merah jika ada), Sudah Dibayar Tahun Ini, Total Kewajiban (warning yellow card). Card **Daftar Tagihan** dengan 2-3 section bertumpuk:

1. **Section Tunggakan TA Lama BELUM DIALIHKAN** (jika ada — alert merah, daftar read-only tanpa tombol bayar — perlu hubungi bendahara dulu agar `carryover` diproses).
2. **Section Tagihan TA Aktif** — list dengan tombol **Bayar** per item + form bulk-pay (checkbox + tombol Bayar Terpilih).
3. **Section Tagihan TA Lama yang SUDAH DIALIHKAN** (gabung di TA aktif, ditandai badge "Tunggakan").

Tiap row tagihan: jenis_tagihan, jumlah, status (Lunas/Belum Bayar), tombol aksi (Bayar Sekarang → modal pilih metode: Tunai/Transfer manual upload bukti, atau Midtrans Snap online).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Bayar (single tagihan) | `wali-siswa.tagihan.bayar` | POST | `@prosesBayar` | redirect | Proses pembayaran 1 tagihan. Jika metode upload bukti: simpan `Pembayaran` status `pending` (menunggu validasi Bendahara). Jika Midtrans: redirect ke `pembayaran.snap`. |
| Bayar Terpilih (bulk) | `wali-siswa.tagihan.bulk-pay` | POST | `@processBulkPay` | redirect | Bulk pay beberapa tagihan sekaligus → gabungkan jadi 1 `Pembayaran` Midtrans (efisien). Redirect ke Snap dengan `pembayaranId` baru. |
| Cetak Invoice | `wali-siswa.pembayaran.invoice` | GET | `@cetakInvoice` | `wali-siswa/tagihan/invoice.blade.php` | Layout cetak invoice 1 pembayaran (PDF-friendly). |

**Catatan**: Tunggakan TA lama yang **belum dialihkan** muncul read-only — orang tua tidak bisa bayar langsung sampai Bendahara/Admin menjalankan `carryover.execute` (lihat [bendahara.md §Tarik Tunggakan](bendahara.md#tarik-tunggakan-carryover)). Pembayaran online → `Pembayaran.metode = midtrans` & status = `pending` sampai callback `snap.finish` set jadi `lunas`. Webhook `midtrans.notification` (terdaftar di route global di luar grup wali-siswa, tanpa auth/CSRF) menerima notifikasi resmi dari Midtrans untuk update status.

---

### Pembayaran Snap (Midtrans)

**Index view**: `wali-siswa/pembayaran/snap.blade.php` · **Controller**: `OrangTuaController@snapPayment`

**Tampilan index**: Card terpusat dengan icon credit-card + judul "Pembayaran Digital" + deskripsi "Anda akan diarahkan ke Midtrans". Section **Rincian Pembayaran** menampilkan: nama siswa, daftar item tagihan (jenis + nominal per item), total bayar (highlight primary). Alert info metode pembayaran tersedia (VA, E-Wallet GoPay/ShopeePay, QRIS, CC). Tombol **Lanjutkan Pembayaran** (primary, lg) — memicu Midtrans Snap.js popup. Tombol Kembali ke tagihan. Modal **Pembayaran Dibatalkan** muncul bila siswa close popup tanpa bayar.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Lanjutkan Pembayaran (JS Snap.js) | (callback) `wali-siswa.pembayaran.snap.finish` | GET | `@snapFinish` | redirect | Setelah user selesai bayar di popup Midtrans → callback ini set `Pembayaran` status sesuai response. |
| Continue Pembayaran (resume Snap) | `wali-siswa.pembayaran.continue` | POST | `@continuePayment` | redirect/JSON | Untuk pembayaran yang sudah pernah di-Snap tapi belum selesai — re-trigger Snap dengan `snap_token` lama (atau generate baru bila expired). |

**Catatan**: View ini di-render setelah `prosesBayar`/`processBulkPay` saat metode = Midtrans. **Webhook `POST /midtrans/notification`** (di luar route group ini, tanpa auth/CSRF, prefix global) menerima notifikasi resmi dari Midtrans server → update status `Pembayaran.status` jadi `lunas`/`gagal`/`expired`. `snap.finish` adalah redirect browser (UX) bukan source-of-truth.

---

### Rapor Anak

**Index view**: `wali-siswa/rapor/index.blade.php` · **Controller**: `OrangTuaController@raporAnak`

**Tampilan index**: Header nama anak + tombol Kembali. Card siswa-info ringkas (avatar, NISN, kelas, cabang). Card **Daftar Rapor** dengan badge counter total rapor. Bila `$locked=true`: alert kuning "Akses Terkunci — rapor belum divalidasi wali kelas". Bila kosong: alert info "Belum ada rapor tersedia". Bila ada: **grid 3-kolom kartu rapor** — tiap card berisi ikon book-open, badge semester (Ganjil/Genap), nama TA, tanggal rilis, dan tombol **Lihat Detail** → `detailRapor`.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Lihat Detail Rapor | `wali-siswa.rapor.detail` | GET | `@detailRapor` | `wali-siswa/rapor/detail.blade.php` | Tampilan rapor lengkap (nilai per mapel + deskripsi capaian + kehadiran + catatan wali). Read-only. Tombol **Request Download** muncul di bawah. |
| Request Download Rapor | `wali-siswa.rapor.request-download` | POST | `@requestDownloadRapor` | redirect | Buat `RequestDownloadRapor` status `menunggu` → muncul di antrian Wali Kelas (`wali.rapor.request-download.index`). Wali approve → `downloadRapor` aktif 24 jam via token. |
| Download Rapor (via token) | `wali-siswa.rapor.download` | GET | `@downloadRapor` | file download | Setelah approve, link berbasis token aktif 24 jam. Validasi: token valid, belum expired, milik orang tua ini. Stream PDF rapor. |

**Catatan**: Rapor di-`allow_download=true` hanya **setelah** rantai validasi 3 tingkat selesai (Wali Kelas → Bendahara → Ketua) DAN Wali Kelas klik `terbitkan` (lihat [wali-kelas.md §Kelola Rapor](wali-kelas.md#kelola-rapor)). Sebelum itu, halaman ini menampilkan status terkunci. **Mengapa pakai request-download bukan langsung unduh?** — agar Wali Kelas bisa mengontrol siapa & kapan orang tua mengakses file rapor (anti-screenshot leak, audit trail).

---

### Presensi Anak

**Index view**: `wali-siswa/presensi/index.blade.php` · **Controller**: `OrangTuaController@presensiAnak`

**Tampilan index**: Page heading "Presensi Kehadiran" + 4 tombol kanan: **Ajukan Izin/Sakit** (primary, → form ajukan), **Riwayat Presensi** (soft, → list semua), **Riwayat Pengajuan** (secondary, → list izin), **Kembali**. Card **Student Info** dengan avatar + identitas. 4 **stat card** rekap kehadiran bulan ini (Hari Hadir hijau, Sakit oranye, Izin biru, Alpha merah). Section **Riwayat Presensi Bulan Ini** dengan list per minggu (Minggu ke-1, 2, dst.) — tiap minggu berisi list tanggal + status badge. Tombol "Lihat Semua Riwayat" → riwayat-presensi.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Ajukan Izin/Sakit | `wali-siswa.presensi.ajukan-izin` | GET | `@ajukanIzin` | `wali-siswa/presensi/ajukan-izin.blade.php` | Form pengajuan izin baru (lihat sub-section di bawah). |
| Simpan Pengajuan Izin | `wali-siswa.presensi.store-izin` | POST | `@storeIzin` | redirect | Buat record `Presensi` status `pending` dengan upload bukti — dilempar ke Wali Kelas untuk divalidasi. |
| Riwayat Presensi (lengkap, lintas-bulan) | `wali-siswa.presensi.riwayat-presensi` | GET | `@riwayatPresensi` | `wali-siswa/presensi/riwayat-presensi.blade.php` | List semua presensi anak (filter tanggal). Read-only. |
| Riwayat Pengajuan Izin | `wali-siswa.presensi.riwayat-izin` | GET | `@riwayatIzin` | `wali-siswa/presensi/riwayat-izin.blade.php` | List semua pengajuan izin (Pending/Disetujui/Ditolak + alasan). |
| Edit Pengajuan (form) | `wali-siswa.presensi.edit-izin` | GET | `@editIzin` | `wali-siswa/presensi/edit-izin.blade.php` | Form edit pengajuan — **hanya bila masih `pending`** (belum divalidasi wali). |
| Update Pengajuan | `wali-siswa.presensi.update-izin` | PUT | `@updateIzin` | redirect | Update keterangan/tanggal/bukti. Bila wali sudah validasi, gagal (validation). |

**Catatan**: Pengajuan izin oleh orang tua **dilempar ke Wali Kelas** untuk diputuskan (`wali.presensi.proses-validasi-izin`). Setelah wali terima → status presensi jadi `izin` (atau `sakit`); ditolak → `alpha`. Lihat [wali-kelas.md §Validasi Izin](wali-kelas.md#presensi--validasi-izin). Siswa **tidak bisa** mengajukan izin sendiri (route di `Siswa` di-comment) — sengaja jadi gerbang pendampingan orang tua.

#### Form Ajukan Izin/Sakit

**View**: `wali-siswa/presensi/ajukan-izin.blade.php`

**Tampilan**: Header nama anak. Card **Form Pengajuan**: alert info ("Pastikan lampirkan bukti valid"), form dengan field: **Tanggal** (date picker, default hari ini, validasi tidak boleh masa lalu jauh), **Status** (radio: Izin / Sakit), **Keterangan** (textarea required), **Bukti** (file upload: PDF/JPG/PNG, max 5MB — opsional tapi sangat dianjurkan). Tombol **Submit** + Batal.

#### Form Edit Izin

**View**: `wali-siswa/presensi/edit-izin.blade.php`

**Tampilan**: Mirip form ajukan, pre-filled. **Hanya muncul bila `status_validasi = pending`**; bila wali sudah validasi, akses ditolak. Tombol Update + Batal.

---

### Cetak Invoice

**View**: `wali-siswa/tagihan/invoice.blade.php` · **Route**: `wali-siswa.pembayaran.invoice` (dari `cetakInvoice`)

**Tampilan**: Layout cetak invoice 1 pembayaran (DomPDF-friendly). Header sekolah + logo, info siswa, rincian item tagihan + nominal, total bayar, tanggal pembayaran, metode (Tunai/Transfer/Midtrans), status (Lunas/Pending), nomor referensi, tanda tangan/cap (jika ada). Akses lewat tombol di halaman tagihan atau riwayat pembayaran.
