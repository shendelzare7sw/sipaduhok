# Role: Orang Tua / Wali

> Kembali ke [flow.md](../../flow.md) · Role `orang_tua` · Level 5 · Prefix `/orang-tua` · Route `orang-tua.` · Middleware `role:orang_tua`.

## Ringkasan Peran

Pendamping & penanggung jawab keuangan anak. Secara desain, **tanggung jawab pembayaran dan pemantauan rapor sengaja dialihkan dari Siswa ke Orang Tua** (lihat catatan di grup route orang-tua: "Siswa hanya fokus belajar, tidak ada akses pembayaran"). Satu akun orang tua bisa memiliki >1 anak (relasi `User->children()` via `StudentParent`).

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/orang-tua/partials/sneat-sidebar-menu.blade.php`.
- Sidebar **dinamis per anak**: untuk setiap `child` muncul submenu Presensi / Tagihan / Rapor. Bila belum ada anak tertaut → "Belum Ada Data Anak".
- Dashboard: `OrangTua\OrangTuaController@dashboard`. Seluruh fitur ada di satu controller `OrangTua\OrangTuaController`.

## Peta Menu

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| — | Dashboard | `orang-tua.dashboard` | `OrangTuaController@dashboard` | `Siswa`,`StudentParent` |
| Monitoring Anak | [Anak] → Presensi | `orang-tua.presensi.anak` (siswa) | `OrangTuaController@presensiAnak` | `Presensi` |
| Monitoring Anak | [Anak] → Tagihan | `orang-tua.tagihan.anak` (siswa) | `OrangTuaController@tagihanAnak` | `Tagihan`,`Pembayaran` |
| Monitoring Anak | [Anak] → Rapor | `orang-tua.rapor.anak` (siswa) | `OrangTuaController@raporAnak` | `Rapor` |

Aksi tambahan (di dalam halaman, bukan menu sidebar):

| Fungsi | Route | Controller@method |
|---|---|---|
| Bayar tagihan / bulk | `orang-tua.tagihan.bayar` / `.bulk-pay` | `OrangTuaController@prosesBayar` / `processBulkPay` |
| Pembayaran digital (Midtrans Snap) | `orang-tua.pembayaran.snap` / `.continue` / `.snap.finish` / `.invoice` | `OrangTuaController@snapPayment/continuePayment/snapFinish/cetakInvoice` |
| Detail rapor & unduh | `orang-tua.rapor.detail` · `orang-tua.rapor.request-download` · `orang-tua.rapor.download` (token) | `OrangTuaController@detailRapor/requestDownloadRapor/downloadRapor` |
| Ajukan / kelola izin anak | `orang-tua.presensi.ajukan-izin` · `.store-izin` · `.edit-izin` · `.update-izin` · `.riwayat-presensi` · `.riwayat-izin` | `OrangTuaController@ajukanIzin/storeIzin/editIzin/updateIzin/...` |

View dir: `resources/views/orang-tua/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Tagihan & Pembayaran** — Orang tua membayar tagihan anak, manual maupun online via **Midtrans Snap** (`snapPayment` → callback `snap.finish`; webhook global `midtrans.notification` di luar auth & CSRF). Pembayaran ini memengaruhi status lunas yang dipakai **Bendahara** untuk validasi akses ujian/rapor. Jadi tindakan orang tua adalah hulu dari [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa).
- **Rapor Anak** — orang tua hanya bisa melihat/mengunduh rapor **setelah** rantai validasi 3 tingkat selesai dan Wali Kelas menerbitkannya (`Rapor.allow_download=true`). `requestDownloadRapor` membuat `RequestDownloadRapor` (status `menunggu`) yang harus **di-approve Wali Kelas** (`wali.rapor.request-download.approve`) sebelum `downloadRapor` via token aktif. Lihat [flow.md §5.1](../../flow.md#51-validasi-rapor--rantai-3-tingkat-wali-kelas--bendahara--ketua-pkbm--orang-tua).
- **Pengajuan Izin Anak (Presensi)** — orang tua mengajukan izin/sakit (`storeIzin`, bisa lampirkan bukti); **Wali Kelas** yang memvalidasi/memproses (`wali.presensi.proses-validasi-izin`). Ini contoh jelas lempar tanggung jawab: siswa tidak bisa mengajukan izin sendiri (route siswa di-disable), harus lewat orang tua sebagai bentuk pendampingan, lalu diputuskan wali kelas.
- **Sidebar dinamis** — semua menu bergantung pada daftar anak (`children`). Saat menambah fitur, perhatikan parameter `{siswa}` selalu mengikat ke anak tertentu dan harus dicek kepemilikannya terhadap akun orang tua.
