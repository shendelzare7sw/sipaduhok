# SIPADUHOK — Role, Module, and Payment Reconciliation

## A. Role Reconciliation

| Role Laporan | Role Final | Status | Perubahan | Evidence |
|---|---|---|---|---|
| Admin | Admin (`admin`) | EXPANDED | Tetap mengelola data inti, jadwal, tagihan, dan monitoring; final juga mengelola cabang, konten, recovery, pengaturan LMS/AI, promotion, laporan, serta mempunyai bypass middleware role. | Laporan hlm. 11, 14, 17–20; `RoleSeeder.php`; route `/admin`; `CheckRole.php` |
| Wali Kelas | Wali Kelas (`wali_kelas`) | EXPANDED | Presensi/nilai tetap ada; final memakai assignment kelas aktif, menghasilkan rapor terstruktur, mengirim validasi Ketua, mengelola request unduh, arsip, prediksi kenaikan, dan validasi akses. | Laporan hlm. 11–12, 15, 18, 21; route `/wali`; `WaliKelasHelper`; sidebar Wali |
| Guru Pengajar | Guru Pengajar (`guru_pengajar`) | EXPANDED | Materi, tugas, ujian, forum, nilai tetap ada; final menambah meeting, pengawasan ujian, bank soal/AI, koreksi berbantuan AI, catatan monitoring, serta arsip/salin konten. | Laporan hlm. 12–15, 19–21; route `/guru`; controller LMS Guru |
| Siswa | Siswa (`siswa`) | SAME | Role tetap, tetapi scope berubah material: pengajuan izin dipindah ke Orang Tua; pembayaran Siswa menjadi read-only dan tersembunyi dari sidebar; route rapor Siswa dinonaktifkan dan akses rapor aktif melalui Orang Tua. | Laporan hlm. 13, 19–21; `routes/web.php:1426-1456`; `SiaPembayaranController`; sidebar Siswa |
| Tidak ada pada aktor awal; ada pada Dokumen Teknis | Ketua PKBM (`ketua_pkbm`) | NEW ROLE | Memantau sistem/LMS, laporan/catatan, memvalidasi rapor, serta memutus dispensasi/promotion. | Dokumen Teknis hlm. 80, 84–85, 121, 124–125, 135; route `/ketua` |
| Tidak ada pada aktor awal; ada pada Dokumen Teknis | Wakil Kepala Sekolah (`wakil_kepala_sekolah`) | NEW ROLE | Mengelola akademik dalam scope cabang, monitoring, promotion, dan catatan. | Dokumen Teknis hlm. 62–64, 80–81, 135; route `/waka`; Waka controllers |
| Tidak ada pada aktor awal; ada pada Dokumen Teknis | Sekretaris (`sekretaris`) | NEW ROLE | Mengelola kalender, pengumuman, flyer, dan berita. | Dokumen Teknis hlm. 65, 106, 135, 168–170; route `/sekretaris` |
| Tidak ada pada aktor awal; ada pada Dokumen Teknis | Bendahara (`bendahara`) | NEW ROLE | Mengelola tagihan/pembayaran/config/laporan, validasi finansial, dan dispensasi kenaikan. | Dokumen Teknis hlm. 66–69, 107–110, 135, 165–167; route `/bendahara` |
| Tidak ada pada aktor awal; ada pada Dokumen Teknis | Orang Tua/Wali Siswa (`orang_tua`) | NEW ROLE | Menjadi aktor final untuk pembayaran, izin, pemantauan presensi/rapor anak, dan request unduh rapor. | Dokumen Teknis hlm. 86–88, 126–128, 135, 139, 165–166; route `/wali-siswa` |

Catatan: status **NEW ROLE** berarti baru terhadap daftar aktor awal pada laporan utama, bukan berarti tanpa dokumentasi sama sekali. Kesembilan role telah dicatat pada Dokumen Teknis halaman 135.

## B. Module Reconciliation

| Modul Laporan | Modul Final | Status | Keterangan |
|---|---|---|---|
| Modul 1 — Manajemen Pengguna & Kelas | USR — Manajemen Pengguna; ORG — Organisasi & Data Induk | SPLIT | Akun dipisahkan dari cabang, tahun ajaran, kelas, assignment, dan relasi Orang Tua. |
| Modul 2 — Manajemen Akademik | AKD — Perencanaan Akademik | EXPANDED | Final mencakup mapel, jadwal multi-kelas, konflik, import/duplikasi, ganti Guru, interval istirahat, assignment turunan, dan laporan. |
| Modul 3 — Materi & Pembelajaran | LMS — Pembelajaran Daring | EXPANDED | Materi/forum berkembang menjadi ruang LMS lengkap, meeting, arsip, dan akses ter-scope. |
| Modul 4 — Penugasan & Ujian | LMS; NIL — Penilaian | SPLIT | Siklus tugas/ujian berada di LMS; hasil dan sinkronisasi nilai dipisahkan ke modul NIL. |
| Modul 5 — Presensi Siswa | PRS — Presensi & Izin | EXPANDED | Final menambah import/print/riwayat dan portal Orang Tua; aktor pengajuan izin berubah dari Siswa ke Orang Tua. |
| Modul 6 — Penilaian & Laporan | NIL; RAP — Rapor | SPLIT | Nilai harian/sinkronisasi dipisah dari generate, publikasi, validasi, request download, dan arsip rapor. |
| Modul 7 — Notifikasi | NOT — Notifikasi | EXPANDED | Trigger awal berkembang menjadi notification center, read/delete, dan reminder terjadwal. |
| Modul 8 — Pembayaran & Keuangan | KEU — Keuangan | EXPANDED | Aktor transaksi berubah ke Orang Tua; final mencakup Bendahara, carryover, bulk SPP, config channel, audit, laporan, dan webhook. |
| Modul 9 — Profil Akademik | SWA — Portal Siswa; USR | SPLIT | Tampilan pribadi berada pada portal Siswa, sedangkan pengelolaan data tetap pada Admin. |
| Modul 10 — Monitoring Aktivitas | MON — Monitoring & Catatan | EXPANDED | Monitoring berkembang dari online user menjadi monitoring organisasi/LMS dan catatan ke Guru oleh pimpinan. |
| Tidak ada sebagai modul awal | AU — Autentikasi & Akun | NEW | Login disebut dalam alur, tetapi final memisahkan recovery, keamanan Admin, akun, dan profil menjadi modul operasional. |
| Tidak ada sebagai modul awal | KON — Konten & Informasi | NEW | Kalender, publikasi, berita, flyer, landing CMS, dan halaman publik muncul pada Dokumen Teknis/final. |
| Tidak ada sebagai modul awal | PRM — Kenaikan Kelas | NEW | Rangkaian multi-role tercatat dalam Dokumen Teknis, tetapi tidak ada pada sepuluh modul awal. |
| Tidak ada sebagai modul awal | WLS — Portal Orang Tua | NEW | Portal anak merupakan konsekuensi penambahan role Orang Tua pada desain/final. |
| Profil/portal siswa tersebar pada modul awal | SWA — Portal Siswa | MERGED | Dashboard, informasi pribadi, pembatasan LMS, dan alumni dikelompokkan menjadi satu modul final. |

## C. Payment Requirement Reconciliation

### C.1 Business Requirement

| Area | Laporan Utama | Dokumen Teknis | Implementasi Final | Status |
|---|---|---|---|---|
| Actor pembayar | Siswa | Orang Tua/Wali mulai tampak pada use case dan `paid_by_parent_id` | Orang Tua/Wali Siswa melakukan transfer/Midtrans; Siswa hanya melihat tagihan/riwayat | CHANGED |
| Pembentukan tagihan | Admin membuat SPP, ujian, kegiatan | Kelola Tagihan & Pembayaran | Admin dan Bendahara membuat custom/bulk/SPP; ada import dan duplikasi | EXPANDED |
| Pembayaran manual | Siswa unggah bukti, Admin mengonfirmasi | Pembayaran menyimpan bukti, validator, dan parent | Orang Tua unggah bukti transfer; Admin/Bendahara approve/reject; pembayaran tunai dapat dicatat pengelola | CHANGED |
| Payment gateway | Midtrans; QRIS/VA/E-Wallet; sukses terverifikasi otomatis | Kolom gateway/order/response dan konfigurasi Midtrans | Snap single/bulk, continue pending, webhook bertanda tangan, status API, audit, notification | EXPANDED |
| Status | Menunggu, Diterima, Ditolak | `pending`, `disetujui`, `ditolak`; status tagihan | Payment dan tagihan mempunyai status terpisah; mapping gateway idempotent | EXPANDED |
| Invoice/riwayat | Siswa melihat status/riwayat | Data payment dan tagihan terstruktur | Orang Tua melihat/cetak invoice anak; Siswa melihat tagihan/riwayat/cetak bukti read-only | CHANGED |
| Tunggakan | Tidak dijelaskan | Link carryover pada tabel tagihan | Admin/Bendahara memindahkan sisa tunggakan ke tahun aktif dengan traceability | EXPANDED |
| Restriksi akses | Ujian disebut restricted, tetapi dasar finansial tidak rinci | Validasi Akses Ujian & Rapor; batas pembayaran | Service menilai kelunasan/dispensasi dan validasi Wali/Bendahara untuk ujian/rapor/promotion | EXPANDED |
| Audit | Pembayaran harus aman dan terdokumentasi | `financial_audit_logs` | Perubahan status, validator, nilai lama/baru, IP/user-agent dicatat | MATCH |
| Aktivasi production | Tidak membuktikan akun provider aktif | Field mode production/enabled tersedia | Implementasi kode ada; credential, notification URL, settlement nyata tidak tersedia di repository | NEED_CONFIRMATION |

### C.2 Final Payment Flow

1. Admin/Bendahara membuat tagihan siswa, termasuk bulk SPP dan carryover tunggakan.
2. Siswa dapat melihat tagihan/riwayatnya melalui route SIA yang aktif, tetapi aksi bayar selalu ditolak dan diarahkan ke Wali Siswa; menu tidak muncul pada sidebar.
3. Orang Tua membuka tagihan hanya untuk anak yang tertaut melalui `children()`/`student_parents`.
4. Untuk transfer manual, bukti gambar dan nominal divalidasi, payment dibuat `pending`, lalu Admin/Bendahara menyetujui atau menolak.
5. Untuk Midtrans, sistem membuat Snap token/order single atau bulk, menerima webhook, memverifikasi signature, memetakan status, memperbarui payment/tagihan/audit/notifikasi, dan mencegah duplikasi transisi.
6. Finish redirect tidak mempercayai status query browser dan memeriksa status otoritatif melalui Transaction API.

### C.3 Production Boundary

**PAYMENT BUSINESS REQUIREMENT: IMPLEMENTED AS CODE.**

**PAYMENT TECHNICAL IMPLEMENTATION: IMPLEMENTED, tetapi [PRODUCTION ACTIVATION PERLU KONFIRMASI].**

Tidak ada evidence repository yang memastikan credential production, notification URL publik, transaksi settlement nyata, kepemilikan merchant account, refund, chargeback, atau rekonsiliasi bank. Provider final tetap **Midtrans**; Tripay/Duitku tidak termasuk baseline.

## D. Naming Reconciliation

- Gunakan “Orang Tua/Wali Siswa” pada dokumen bisnis dan cantumkan nilai teknis `orang_tua` bila relevan.
- Gunakan “Wakil Kepala Sekolah (Waka)” dengan nilai teknis `wakil_kepala_sekolah`.
- Bedakan “Wali Kelas” (`wali_kelas`, prefix `/wali`) dari “Orang Tua/Wali Siswa” (`orang_tua`, prefix `/wali-siswa`).
- Jangan menyebut sembilan role sebagai aktor awal; sebut sebagai role desain/final yang membutuhkan konfirmasi scope final.
