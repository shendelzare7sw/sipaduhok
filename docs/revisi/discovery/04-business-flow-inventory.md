# Business Flow Inventory

Business flow berikut diturunkan dari route, controller, service, model dan view. Urutan tidak menyatakan bahwa setiap langkah selalu dikerjakan oleh orang yang berbeda; role Admin dapat melewati middleware role, tetapi actor bisnis normal tetap ditulis.

## FLOW-001 — Login dan Pengalihan Dashboard Berdasarkan Role

**Actor / Role:** Semua pengguna terdaftar.  
**Purpose:** Membentuk session dan mengarahkan pengguna ke area kerjanya.  
**Precondition:** Akun aktif; kredensial tersedia; Turnstile valid bila dikonfigurasi.

**Steps:**

1. Pengguna membuka form login dan mengisi username/email serta password.
2. Sistem memeriksa rate limit dan, bila key tersedia, memverifikasi token Turnstile ke Cloudflare.
3. Sistem mencoba autentikasi, menolak akun nonaktif, lalu meregenerasi session.
4. Sistem menyimpan waktu/IP login dan menentukan role dari `role_id`, fallback `users.role`.
5. Pengguna diarahkan ke dashboard Admin/Ketua/Waka/Sekretaris/Bendahara/Wali Kelas/Guru/Siswa/Orang Tua.

**Result:** Session aktif dan akses berikutnya dibatasi middleware role/ownership.  
**Modules:** AU.  
**Feature IDs:** FEAT-001, FEAT-002.  
**Source References:** `LoginRequest.php`; `LoginController.php`; `DashboardController.php`; `CheckRole.php`; `routes/web.php:133-226`.

## FLOW-002 — Pembuatan Siswa dan Penautan Orang Tua

**Actor / Role:** Admin; Waka untuk manajemen relasi dalam scope cabang.  
**Purpose:** Membentuk akun siswa, profil akademik, penempatan kelas, dan akses orang tua.  
**Precondition:** Cabang/kelas tersedia; untuk Waka, target berada di cabang akun.

**Steps:**

1. Admin membuat/import akun serta profil siswa.
2. Sistem memvalidasi identitas unik, kelas/cabang, status, dan membuat/menautkan `users`–`siswa`.
3. Admin/Waka menempatkan siswa pada kelas yang masih memenuhi aturan kapasitas/scope.
4. Admin membuat atau memilih akun orang tua.
5. Admin/Waka menautkan orang tua dan siswa pada `student_parents` beserta relationship dan hak akses akademik/finansial.
6. Portal siswa dan orang tua mengambil data melalui relasi tersebut.

**Result:** Siswa dapat memakai portalnya; orang tua hanya melihat anak tertaut.  
**Modules:** USR, ORG, SWA, WLS.  
**Feature IDs:** FEAT-008, FEAT-009, FEAT-011, FEAT-017, FEAT-019, FEAT-094, FEAT-097.  
**Source References:** `Admin/UserController.php`; Admin/Waka `ManajemenSiswaController.php`; `User::children()`; `StudentParent.php`; relevant routes.

## FLOW-003 — Perencanaan Jadwal sampai Ruang LMS Guru/Siswa

**Actor / Role:** Admin/Waka, Guru, Siswa.  
**Purpose:** Menghubungkan kelas, mapel dan guru agar jadwal serta LMS dapat diakses oleh pihak tepat.  
**Precondition:** Tahun ajaran, cabang, kelas, mapel, guru dan waktu istirahat tersedia.

**Steps:**

1. Admin/Waka membuat jadwal dengan guru, mapel, waktu, tahun ajaran dan satu/lebih kelas.
2. Sistem mencegah konflik sesuai validation/business rule controller dan menyimpan pivot `jadwal_kelas`.
3. Assignment `guru_pengajar_kelas` diturunkan/dibangun ulang dari jadwal.
4. Guru melihat kelas/mapel/jadwal yang diampu dan membuka dashboard LMS kombinasi tersebut.
5. Siswa melihat jadwal kelas; middleware memastikan mapel berada pada jadwal kelas dan sesuai filter agama.
6. Bila jenjang diizinkan pada setting LMS, siswa membuka materi/tugas/ujian/forum/meeting mapel.

**Result:** Akses pembelajaran konsisten dengan jadwal dan assignment aktif.  
**Modules:** ORG, AKD, LMS, SWA.  
**Feature IDs:** FEAT-015–018, FEAT-021–029, FEAT-065, FEAT-067, FEAT-094–095.  
**Source References:** Admin/Waka `JadwalPelajaranController.php`; `GuruPengajarController.php`; `CheckLmsAccess.php`; `CheckSiswaMapelAccess.php`; `GuruLmsController.php`.

## FLOW-004 — Materi dan Tugas sampai Nilai

**Actor / Role:** Guru Pengajar, Siswa.  
**Purpose:** Menyampaikan konten/tugas, menerima jawaban, melakukan koreksi, dan menyinkronkan nilai.  
**Precondition:** Guru ditugaskan; siswa aktif, berada di kelas, LMS/mapel dapat diakses.

**Steps:**

1. Guru membuat materi dan tugas, opsional mengunggah file, serta menentukan periode.
2. Sistem menyimpan konten dan mengirim notifikasi kepada siswa terkait.
3. Siswa membaca materi dan mengirim jawaban teks/file tugas sesuai state tugas.
4. Guru membuka submission hanya pada kelas/mapelnya dan melakukan koreksi manual atau bantuan AI.
5. `TugasSiswaObserver` memanggil `NilaiSyncService` setelah perubahan nilai submission.
6. Nilai tersedia pada rekap guru/wali dan portal siswa.

**Result:** Submission dan nilai terhubung ke siswa, kelas, mapel dan guru yang benar.  
**Modules:** LMS, NIL, NOT.  
**Feature IDs:** FEAT-055–056, FEAT-066–069, FEAT-093.  
**Source References:** `GuruMateriController.php`; `GuruTugasController.php`; `LmsTugasController.php`; `GuruKoreksiController.php`; `TugasSiswaObserver.php`; `NotificationService.php`.

## FLOW-005 — Ujian/Latihan Online sampai Koreksi

**Actor / Role:** Guru Pengajar, Siswa.  
**Purpose:** Menyusun bank soal, melaksanakan ujian/latihan, merekam jawaban dan menghasilkan nilai aman.  
**Precondition:** Assignment valid; siswa memenuhi akses LMS/mapel/finansial; waktu/state ujian valid.

**Steps:**

1. Guru membuat ujian/latihan dan soal secara manual, bulk, import, atau draf AI.
2. Sistem menyimpan konfigurasi, menyembunyikan kunci dari serialisasi siswa, dan memberi notifikasi.
3. Siswa membuka ujian; service validasi akses memeriksa gerbang finansial dan controller memeriksa waktu/attempt.
4. Sistem membuat `ujian_siswa`, menyimpan progres/autosave jawaban serta log pengawasan.
5. Siswa submit; jawaban objektif dinilai otomatis, uraian dapat menunggu koreksi guru.
6. Guru memonitor, mengoreksi bila perlu, dan observer menyinkronkan nilai.
7. Siswa dapat review/retake hanya bila aturan mengizinkan.

**Result:** Hasil ujian tercatat tanpa membocorkan kunci jawaban dan di-scope ke pemiliknya.  
**Modules:** LMS, NIL, KEU, NOT.  
**Feature IDs:** FEAT-055, FEAT-070–076, FEAT-093.  
**Source References:** `GuruUjianController.php`; `LmsUjianController.php`; `SoalUjian.php`; `ValidasiAksesService.php`; observer; migration pengawasan; security tests.

## FLOW-006 — Presensi dan Pengajuan Izin

**Actor / Role:** Wali Kelas, Orang Tua, Siswa (view).  
**Purpose:** Mencatat kehadiran dan memproses izin anak dengan bukti.  
**Precondition:** Siswa berada pada kelas wali; orang tua tertaut ke siswa.

**Steps:**

1. Wali kelas menginput presensi harian atau mengimpor spreadsheet untuk kelasnya.
2. Orang tua membuka presensi anak dan mengajukan izin dengan tanggal, alasan dan opsional/wajib bukti sesuai rule.
3. Sistem menyimpan pengajuan pada entitas presensi dan memberi notifikasi kepada wali kelas.
4. Wali kelas melihat antrean dan menyetujui/menolak izin hanya untuk kelasnya.
5. Sistem memberi notifikasi hasil kepada orang tua/siswa dan memperbarui riwayat/rekap.

**Result:** Status presensi tervalidasi dan dapat dilihat aktor terkait.  
**Modules:** PRS, NOT, WLS, SWA.  
**Feature IDs:** FEAT-048–052, FEAT-093, FEAT-099.  
**Source References:** `WaliKelas/PresensiController.php`; `OrangTuaController@storeIzin/updateIzin`; `NotificationService.php`; ownership tests.

## FLOW-007 — Tagihan sampai Pembayaran Transfer Manual

**Actor / Role:** Admin/Bendahara, Orang Tua.  
**Purpose:** Menagih siswa, menerima bukti transfer, memvalidasi pembayaran, dan memperbarui saldo/status.  
**Precondition:** Tahun ajaran dan siswa tersedia; tagihan milik anak; tunggakan lama sudah di-carryover bila diperlukan.

**Steps:**

1. Admin/Bendahara membuat tagihan custom/bulk atau generate SPP massal.
2. Sistem mencegah duplikasi yang dilarang dan menampilkan tagihan pada portal anak.
3. Orang tua memilih satu/lebih tagihan, memasukkan nominal, memilih transfer, dan mengunggah bukti.
4. Sistem memverifikasi ownership tagihan, menyimpan pembayaran `pending`, dan memberi notifikasi ke Admin/Bendahara.
5. Admin/Bendahara menyetujui/menolak pembayaran.
6. Sistem mencatat validator/audit, menghitung pembayaran yang disetujui, dan mengubah tagihan menjadi cicilan/lunas.

**Result:** Tagihan dan histori pembayaran mencerminkan hasil validasi.  
**Modules:** KEU, NOT, WLS.  
**Feature IDs:** FEAT-037–043, FEAT-047, FEAT-092–093, FEAT-098.  
**Source References:** Tagihan/Pembayaran controllers; `OrangTuaController@prosesBayar/processBulkPay`; `Tagihan::updateStatusBayar`; `NotificationService.php`.

## FLOW-008 — Pembayaran Digital Midtrans

**Actor / Role:** Orang Tua, sistem Midtrans, sistem SIPADUHOK, Admin/Bendahara sebagai penerima notifikasi.  
**Purpose:** Menyelesaikan payment online satu atau beberapa tagihan secara terverifikasi.  
**Precondition:** Midtrans dikonfigurasi dan enabled; tagihan milik anak serta valid dibayar.

**Steps:**

1. Orang tua memilih Midtrans dan satu/lebih tagihan.
2. Sistem membentuk `order_id`, item/customer details, payment record pending, lalu meminta Snap token.
3. Orang tua menyelesaikan pembayaran pada Snap; transaksi pending dapat dilanjutkan dengan order baru sesuai implementasi.
4. Midtrans mengirim webhook ke `/midtrans/notification`.
5. Sistem menghitung SHA-512 signature, mencari seluruh payment dalam order dan memetakan status `capture/settlement/pending/deny/expire/cancel`.
6. Payment, audit log dan tagihan diperbarui; pending duplikat pada tagihan sama dibatalkan.
7. Finish redirect tidak mempercayai query status; sistem menanyakan Transaction API sebagai fallback UX.
8. Notifikasi keberhasilan dikirim idempotent kepada Admin, Bendahara dan orang tua.

**Result:** Status payment berasal dari sumber server-side terverifikasi dan tagihan tersinkron.  
**Modules:** KEU, NOT, WLS.  
**Feature IDs:** FEAT-044–047, FEAT-093, FEAT-098.  
**Source References:** `MidtransService.php`; `MidtransWebhookController.php`; `OrangTuaController@snapPayment/snapFinish/continuePayment`; payment migrations; payment tests.

## FLOW-009 — Carryover Tunggakan Tahun Ajaran

**Actor / Role:** Admin/Bendahara, sistem, Orang Tua.  
**Purpose:** Mengalihkan kewajiban lama ke tahun aktif tanpa kehilangan traceability.  
**Precondition:** Tahun aktif tersedia; tagihan lama belum selesai dan belum dialihkan.

**Steps:**

1. Admin/Bendahara memilih tahun/target dan mempratinjau tunggakan eligible.
2. Service menghitung sisa setelah pembayaran disetujui.
3. Pengelola mengeksekusi carryover.
4. Transaction membuat tagihan tujuan dan menautkan `tagihan_asal_id`, `dialihkan_ke_id`, `dialihkan_pada`.
5. Sistem mengirim notifikasi; portal orang tua memblokir pembayaran langsung tagihan lama yang belum dialihkan.

**Result:** Sisa tunggakan tersedia di tahun aktif dengan hubungan asal-tujuan.  
**Modules:** KEU, NOT.  
**Feature IDs:** FEAT-040, FEAT-093.  
**Source References:** `TunggakanCarryoverService.php`; `Tagihan.php`; tagihan controllers; parent payment guards.

## FLOW-010 — Nilai sampai Rapor dan Validasi Ketua

**Actor / Role:** Guru, Wali Kelas, Ketua PKBM.  
**Purpose:** Mengubah nilai/presensi menjadi rapor yang ditinjau dan disahkan.  
**Precondition:** Nilai dan data siswa/kelas/TA tersedia; wali memegang kelas.

**Steps:**

1. Guru memberi nilai; observer/service menyinkronkan ke rekap nilai.
2. Wali kelas meninjau/mengubah nilai dalam kewenangannya dan menghasilkan rapor.
3. Wali mengisi format/deskripsi/ekstra/kehadiran, menerapkan template, lalu preview.
4. Wali mengirim rapor untuk validasi Ketua.
5. Ketua memvalidasi atau meminta revisi; wali memperbaiki dan mengirim ulang bila perlu.
6. Rapor diterbitkan sesuai state implementasi dan tersedia untuk monitoring Orang Tua.

**Result:** Rapor memiliki data akademik dan status validasi yang dapat ditelusuri.  
**Modules:** NIL, RAP, NOT, WLS.  
**Feature IDs:** FEAT-053–064, FEAT-093, FEAT-099.  
**Source References:** nilai controllers/service/observers; `WaliKelas/RaporController.php`; `Ketua/ValidasiRaporController.php`; rapor models.

## FLOW-011 — Request dan Download Rapor oleh Orang Tua

**Actor / Role:** Orang Tua, Wali Kelas.  
**Purpose:** Mengendalikan download rapor anak melalui persetujuan dan token.  
**Precondition:** Rapor milik anak tertaut dan berada pada state yang dapat diminta.

**Steps:**

1. Orang tua membuka detail rapor anak dan mengirim request download.
2. Sistem memverifikasi ownership dan membuat `request_download_rapor`.
3. Wali kelas melihat antrean request untuk kelasnya.
4. Wali menyetujui atau menolak; sistem mencatat pemutus/status/token/expiry sesuai implementasi.
5. Orang tua menggunakan route download dengan token yang valid.
6. Sistem menolak token salah/kedaluwarsa/tidak berhak dan mengirim file/PDF bila valid.

**Result:** File rapor tidak diberikan langsung tanpa workflow persetujuan.  
**Modules:** RAP, WLS, NOT.  
**Feature IDs:** FEAT-063, FEAT-092, FEAT-100.  
**Source References:** `RequestDownloadRapor.php`; `RaporController@requestDownloadIndex/approveDownload/rejectDownload`; parent request/download methods.

## FLOW-012 — Kenaikan Kelas Multi-Role

**Actor / Role:** Admin/Waka, Wali Kelas, Bendahara, Ketua PKBM, sistem scheduler.  
**Purpose:** Menentukan kelayakan akademik/finansial, menyetujui dan mengeksekusi kenaikan kelas.  
**Precondition:** Tahun aktif, KKM, aturan dan kelas tujuan tersedia; nilai/tagihan cukup untuk evaluasi.

**Steps:**

1. Admin/Waka mengatur KKM dan kriteria kenaikan.
2. Wali melihat prediksi kelayakan kelas.
3. Service menghitung aspek akademik dan finansial per siswa.
4. Bendahara/Admin memvalidasi aspek finansial atau override yang diizinkan.
5. Ketua menyetujui/menolak usulan satu atau massal.
6. Admin/Waka mengeksekusi segera atau membuat jadwal eksekusi.
7. Service memindahkan status/kelas dalam transaction dan mencatat history; rollback tersedia.
8. Sistem mengirim notifikasi hasil.

**Result:** Status siswa/kelas berubah dengan keputusan multi-role dan jejak rollback.  
**Modules:** PRM, NIL, KEU, NOT, ORG.  
**Feature IDs:** FEAT-080–086, FEAT-093.  
**Source References:** `PromotionService.php`; promotion controllers/models/migrations; `routes/console.php`; promotion tests.

## FLOW-013 — Dispensasi Akses/Rapor karena Kondisi Finansial

**Actor / Role:** Bendahara/Admin, Ketua PKBM, Wali Kelas/Siswa sebagai pihak terdampak.  
**Purpose:** Memberi keputusan pengecualian terkontrol ketika aturan finansial menghambat akses/rapor.  
**Precondition:** Siswa memiliki kondisi pembayaran yang memicu pembatasan dan pengajuan valid.

**Steps:**

1. Validasi akses menghitung status lunas/batas/dispensasi.
2. Bendahara/Admin membuat pengajuan dispensasi ke Ketua.
3. Ketua meninjau dan approve/reject disertai catatan.
4. Service mengubah akses efektif/record pengajuan dan memberi notifikasi.
5. Wali/Siswa memperoleh perilaku akses sesuai keputusan.

**Result:** Pengecualian tidak dilakukan diam-diam dan memiliki actor/status keputusan.  
**Modules:** KEU, RAP, NOT.  
**Feature IDs:** FEAT-046, FEAT-061–063, FEAT-083, FEAT-093.  
**Source References:** `ValidasiAksesService.php`; Admin/Bendahara `ValidasiAksesController.php`; `Ketua/ValidasiRaporController.php`; `PengajuanRaporKetua.php`.

## FLOW-014 — Monitoring LMS dan Teguran Guru

**Actor / Role:** Admin/Ketua/Waka, Guru.  
**Purpose:** Pimpinan memonitor konten LMS dan memberi catatan tindak lanjut kepada guru.  
**Precondition:** Konten dan assignment guru tersedia; untuk Waka target berada pada cabang akun.

**Steps:**

1. Pimpinan membuka ringkasan LMS dan memilih kelas/konten.
2. Service memuat materi/tugas/ujian dengan relasi guru, mapel, kelas dan cabang.
3. Guard cabang menolak Waka yang mencoba konten di luar scope.
4. Pimpinan membuka preview dan mengirim catatan kepada guru.
5. Sistem menyimpan catatan monitoring dan mengirim notifikasi.
6. Guru membuka daftar/detail catatan monitoring.

**Result:** Monitoring dan teguran terhubung ke konten/guru/kelas/mapel yang tepat.  
**Modules:** MON, LMS, NOT.  
**Feature IDs:** FEAT-087–093.  
**Source References:** `LmsMonitoringService.php`; Monitoring/Ketua/Waka controllers; `GuruCatatanMonitoringController.php`; `NotificationService.php`.

## End-to-End UAT Candidate Flows

- FLOW-001 — Login dan dashboard berdasarkan role.
- FLOW-002 — Pembuatan siswa dan penautan orang tua.
- FLOW-003 — Jadwal sampai akses ruang LMS.
- FLOW-004 — Materi/tugas sampai nilai.
- FLOW-005 — Ujian/latihan sampai koreksi.
- FLOW-006 — Presensi dan izin.
- FLOW-007 — Tagihan dan transfer manual.
- FLOW-008 — Payment Midtrans (membutuhkan sandbox/kredensial dan callback yang dapat dijangkau).
- FLOW-009 — Carryover tunggakan.
- FLOW-010 — Nilai sampai validasi rapor.
- FLOW-011 — Request/download rapor.
- FLOW-012 — Kenaikan kelas multi-role.
- FLOW-013 — Dispensasi akses/rapor.
- FLOW-014 — Monitoring LMS dan teguran.

Daftar ini bukan skenario UAT final dan belum memiliki hasil, tanggal, evidence, tester, atau status PASS/FAIL.
