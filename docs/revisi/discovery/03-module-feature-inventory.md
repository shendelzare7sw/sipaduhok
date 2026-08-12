# Module Inventory

Fitur dikelompokkan menurut fungsi bisnis, bukan jumlah controller. Satu Feature ID dapat mempunyai beberapa route pendukung (form, submit, preview, export) dan dapat disediakan melalui controller paralel untuk role berbeda.

| Kode Modul | Modul | Description | Role | Feature Count |
|---|---|---|---|---:|
| AU | Autentikasi & Akun | Login, logout, recovery, keamanan akun, profil | Semua/Guest | 6 |
| USR | Manajemen Pengguna | Akun tenaga pendidik, siswa, orang tua, aktivasi dan import | ADM | 7 |
| ORG | Organisasi & Data Induk | Cabang, tahun ajaran, kelas, relasi siswa-orang tua/wali kelas | ADM, WKA | 7 |
| AKD | Perencanaan Akademik | Mapel, guru pengajar, jadwal, istirahat, laporan akademik | ADM, WKA, WKL, GRU, SIS | 10 |
| KON | Konten & Informasi | Kalender, pengumuman, berita, flyer, landing page | ADM, SEK, publik, SIS | 6 |
| KEU | Keuangan | Tagihan, pembayaran, gateway, carryover, akses dan laporan | ADM, BEN, ORT, SIS, WKL, KET | 12 |
| PRS | Presensi & Izin | Input/rekap presensi, pengajuan dan validasi izin | WKL, ORT, SIS | 5 |
| NIL | Penilaian | Nilai guru/wali, sinkronisasi, import/export dan tampilan siswa | GRU, WKL, SIS, ORT | 5 |
| RAP | Rapor | Generate, edit, validasi, publikasi, download dan arsip rapor | WKL, KET, ORT | 7 |
| LMS | Pembelajaran Daring | Materi, tugas, ujian, latihan, forum, meeting, AI, arsip | GRU, SIS, ADM | 15 |
| PRM | Kenaikan Kelas | KKM, aturan, prediksi, validasi, approval, eksekusi | ADM, WKA, BEN, WKL, KET | 7 |
| MON | Monitoring & Catatan | Monitoring organisasi/LMS dan teguran | ADM, KET, WKA, GRU | 4 |
| NOT | Notifikasi | Notifikasi in-app, status baca dan reminder terjadwal | Semua role login | 3 |
| SWA | Portal Siswa | Dashboard SIA/LMS, informasi pribadi dan alumni | SIS | 3 |
| WLS | Portal Orang Tua | Dashboard anak, akademik anak, invoice dan izin akses rapor | ORT | 4 |
| **Total** |  |  |  | **101** |

# Feature Inventory

| Feature ID | Modul | Fitur | Role | Business Function | Route | Handler | Data | Source |
|---|---|---|---|---|---|---|---|---|
| FEAT-001 | AU | Login | Guest | Pengguna masuk memakai username/email dan password yang valid. | `GET/POST /login` | `LoginController@create/store`; `LoginRequest` | User, session | `routes/web.php:133`; `app/Http/Requests/Auth/LoginRequest.php` |
| FEAT-002 | AU | Logout | Semua login | Pengguna mengakhiri session secara aman. | `POST /logout` | `LoginController@destroy` | Session | `routes/web.php`; `LoginController.php` |
| FEAT-003 | AU | Pemulihan akun publik | Guest | Pengguna meminta pemulihan username/password melalui identitas dan email pribadi. | `/recovery*` | `UserRecoveryController`; `EmailRecoveryService` | User, RecoveryTicket | `routes/web.php:143-194`; `app/Services/EmailRecoveryService.php` |
| FEAT-004 | AU | Pemulihan keamanan Admin | Guest/ADM | Admin memulihkan akun melalui PIN/pertanyaan keamanan dan wajib menyiapkan kontrol keamanan. | `/admin-recovery*`, `/admin/security-setup` | `AdminRecoveryController`; `AdminSecuritySetupController` | User | `routes/web.php:137-210`; middleware `CheckAdminSecuritySetup` |
| FEAT-005 | AU | Pengaturan akun | Semua login | Pengguna mengubah identitas akun dan password sendiri. | `/account/settings`, `/account/password` | `AccountController` | User | `routes/web.php:1593-1598`; `app/Http/Controllers/AccountController.php` |
| FEAT-006 | AU | Profil dan foto | Semua login | Pengguna melihat/memperbarui profil dan foto. | `/profile*` | `ProfileController` | User, public storage | `routes/web.php:1600-1608`; `ProfileController.php` |
| FEAT-007 | USR | Kelola tenaga pendidik | ADM | Admin membuat, melihat, mengubah, menghapus/menonaktifkan akun dan data tenaga pendidik. | `/admin/users/tenaga-pendidik*` | `Admin\UserController` | User, TenagaPendidik, Cabang | `routes/web.php:234-247`; views `admin/users/tenaga-pendidik*` |
| FEAT-008 | USR | Kelola akun siswa | ADM | Admin membuat, melihat, mengubah, menonaktifkan atau menghapus aman akun siswa. | `/admin/users/siswa*` | `Admin\UserController` | User, Siswa, Kelas | `routes/web.php:248-261`; `UserController.php` |
| FEAT-009 | USR | Kelola akun orang tua | ADM | Admin membuat, mengubah, menghapus, dan menautkan akun orang tua/wali. | `/admin/users/wali-siswa*` | `Admin\UserController` | User, StudentParent, Siswa | `routes/web.php:262-276`; `UserController.php` |
| FEAT-010 | USR | Aktivasi/nonaktivasi akun | ADM | Admin mengubah status aktif akun dengan guard untuk akun pimpinan tertentu. | route toggle/bulk user | `UserController` | User.is_active | `app/Http/Controllers/Admin/UserController.php`; `EnsureUserIsActive.php` |
| FEAT-011 | USR | Import pengguna | ADM | Admin mengimpor siswa, tenaga pendidik, atau orang tua dari spreadsheet. | route import per tipe | `UserController`; Import classes | User, Siswa, TenagaPendidik | `routes/web.php:231-276`; `app/Imports/{Siswa,TenagaPendidik,OrangTua}Import.php` |
| FEAT-012 | USR | Unduh template pengguna | ADM | Admin mengunduh template import sesuai tipe pengguna. | route template per tipe | `UserController` | XLSX template | `app/Exports/Templates/*Template.php` |
| FEAT-013 | USR | Kelola tiket recovery | ADM | Admin memproses dan melihat riwayat permintaan recovery yang tidak terkirim otomatis. | `/admin/recovery-tickets*` | `AdminRecoveryTicketController` | RecoveryTicket, User | `routes/web.php:285-300`; controller/view recovery tickets |
| FEAT-014 | ORG | Kelola cabang | ADM | Admin membuat, melihat, mengubah, dan menghapus data cabang. | resource `/admin/cabang` | `CabangController` | Cabang | `routes/web.php:330-332`; `Admin/CabangController.php` |
| FEAT-015 | ORG | Kelola tahun ajaran | ADM, WKA | Pengelola membuat, mengubah, mengaktifkan, dan menghapus tahun ajaran sesuai scope. | `/admin|waka/tahun-ajaran*` | Admin/Waka `TahunAjaranController` | TahunAjaran | `routes/web.php:326-329,764-775` |
| FEAT-016 | ORG | Kelola kelas | ADM, WKA | Pengelola membuat, melihat, mengubah, menghapus, import dan mencetak kelas. | `/admin|waka/kelas*` | Admin/Waka `KelasController` | Kelas, Cabang, TahunAjaran | `routes/web.php:334-345,785-795` |
| FEAT-017 | ORG | Anggota kelas | ADM, WKA | Pengelola menambah/mengeluarkan siswa dari kelas dan melihat daftar per kelas. | route `kelas/*/manage-siswa`; `manajemen-siswa*` | `KelasController`; `ManajemenSiswaController` | Siswa, Kelas | `routes/web.php:334-344,796-810` |
| FEAT-018 | ORG | Penugasan wali kelas | ADM, WKA | Pengelola menugaskan atau melepas wali kelas pada satu/lebih kelas. | `/admin|waka/wali-kelas*` | Admin/Waka `WaliKelasController` | WaliKelasAssignment, Kelas, TenagaPendidik | `routes/web.php:346-352,811-818` |
| FEAT-019 | ORG | Relasi siswa-orang tua | ADM, WKA | Pengelola menautkan/melepas orang tua dengan siswa dan atribut tanggung jawabnya. | route attach/detach parent | `ManajemenSiswaController`; `UserController` | StudentParent | `app/Http/Controllers/Admin/ManajemenSiswaController.php`; Waka counterpart |
| FEAT-020 | ORG | Kartu/daftar siswa | ADM, WKA | Pengelola mencari, memfilter, melihat detail, mencetak daftar dan kartu siswa. | `/admin|waka/manajemen-siswa*` | `ManajemenSiswaController` | Siswa, Kelas, User | `routes/web.php:406-417,796-810` |
| FEAT-021 | AKD | Kelola mata pelajaran | ADM, WKA | Pengelola CRUD, import, mencetak dan menyarankan kode mata pelajaran. | `/admin|waka/mata-pelajaran*` | Admin/Waka `MataPelajaranController` | MataPelajaran | `routes/web.php:360-367,777-784` |
| FEAT-022 | AKD | Kelola jadwal | ADM, WKA | Pengelola membuat, melihat, mengubah, menghapus dan mengaktifkan jadwal multi-kelas. | `/admin|waka/jadwal-pelajaran*` | Admin/Waka `JadwalPelajaranController` | JadwalPelajaran, JadwalKelas | `routes/web.php:379-405,819-845` |
| FEAT-023 | AKD | Import/duplikasi jadwal | ADM, WKA | Pengelola mengimpor atau menduplikasi jadwal dari tahun ajaran lain. | route `import`, `duplicate` | `JadwalPelajaranController` | Jadwal, XLSX | controller Admin/Waka Jadwal |
| FEAT-024 | AKD | Ganti guru jadwal | ADM, WKA | Pengelola mengganti guru pada satu jadwal atau secara massal dan merekam riwayat. | route `ganti-guru`, `bulk-replace` | `JadwalPelajaranController` | Jadwal, History, GuruPengajarKelas | controller Admin/Waka Jadwal |
| FEAT-025 | AKD | Export/cetak jadwal | ADM, WKA, WKL, GRU, SIS | Pengguna mencetak atau mengekspor jadwal sesuai aksesnya. | route `print/export-*` per role | Jadwal controllers/dashboard | JadwalPelajaran | `routes/web.php`; views/exports jadwal |
| FEAT-026 | AKD | Rekonstruksi guru pengajar | ADM, WKA | Pengelola melihat assignment yang diturunkan dari jadwal dan membangun ulang relasinya. | `/guru-pengajar/rebuild-from-jadwal` | `GuruPengajarController` | GuruPengajarKelas, Jadwal | Admin/Waka `GuruPengajarController.php` |
| FEAT-027 | AKD | Kelola waktu istirahat | ADM, WKA | Pengelola membuat, mengubah, menonaktifkan dan menghapus interval istirahat per jenjang. | `/pengaturan-istirahat*` | `PengaturanIstirahatController` | PengaturanIstirahat | `routes/web.php:368-377,879-888` |
| FEAT-028 | AKD | Jadwal wali kelas | WKL | Wali kelas melihat dan mencetak jadwal kelas yang diwalikan. | `/wali/jadwal*` | `WaliKelas\JadwalPelajaranController` | Jadwal, selected class | `routes/web.php:1105-1113` |
| FEAT-029 | AKD | Jadwal/kelas guru | GRU | Guru melihat kelas, mapel, dan jadwal yang diampu pada tahun aktif. | `/guru/kelas`, `/guru/jadwal` | `GuruKelasController`; `GuruJadwalController` | GuruPengajarKelas, Jadwal | `routes/web.php:1229-1240` |
| FEAT-030 | AKD | Laporan/cetak akademik | ADM | Admin mencetak laporan siswa, kelas, guru, jadwal dan rekap terkait. | `/admin/cetak-laporan*` | `CetakLaporanController` | berbagai entitas akademik | `routes/web.php:419-432`; controller/views laporan |
| FEAT-031 | KON | Kalender akademik | ADM, SEK, SIS | Admin/Sekretaris CRUD, atur visibilitas dan cetak kalender; siswa melihat publikasi. | `/admin/akademik/kalender*`, `/sekretaris/kalender*`, `/siswa/lms/kalender*` | `AkademikController`; `SekretarisController`; `LmsDashboardController` | KalenderAkademik | `routes/web.php:552-565,924-936,1469-1471` |
| FEAT-032 | KON | Pengumuman | ADM, SEK, SIS | Admin/Sekretaris mengelola pengumuman dan siswa membaca pengumuman yang tersedia. | route `pengumuman*` | controllers akademik/sekretaris/siswa | Pengumuman | `routes/web.php:566-575,938-946,1473-1475` |
| FEAT-033 | KON | Berita | ADM, SEK, publik | Admin/Sekretaris mengelola berita; pengunjung membaca berita publik. | route `berita*` | `AkademikController`; `SekretarisController`; `BeritaController` | Berita | `routes/web.php:120-122,576-586,958-970` |
| FEAT-034 | KON | Flyer | ADM, SEK, publik | Admin/Sekretaris mengelola flyer yang digunakan pada konten publik. | route `flyer*` | `AkademikController`; `SekretarisController` | Flyer, public storage | `routes/web.php:587-595,948-956` |
| FEAT-035 | KON | Landing page CMS | ADM | Admin mengubah section dan media halaman publik. | `/admin/landing-pages*` | `LandingPage\LandingPageController` | LandingPage, LandingPageSection | `routes/web.php:626-636`; views public |
| FEAT-036 | KON | Halaman publik dan sitemap | Publik | Pengunjung melihat profil/program/fasilitas/galeri/legal dan sitemap. | `/`, `/tentang-*`, `/program-*`, `/sitemap.xml`, dll. | `LandingPageController`; `SitemapController` | Landing pages, berita, fasilitas | `routes/web.php:90-130` |
| FEAT-037 | KEU | Kelola tagihan | ADM, BEN | Pengelola membuat custom/bulk, mengubah, duplikasi, menghapus terbatas dan melihat tagihan. | `/admin/keuangan/tagihan*`, `/bendahara/tagihan*` | Tagihan controllers | Tagihan, Siswa, TahunAjaran | `routes/web.php:434-475,980-1014` |
| FEAT-038 | KEU | Generate SPP massal | ADM, BEN | Pengelola menghasilkan tagihan SPP massal tanpa menduplikasi periode yang sama. | route `generate-spp` | Tagihan controllers | Tagihan, kelas/siswa | controller Tagihan; tests notification |
| FEAT-039 | KEU | Import/export/cetak tagihan | ADM, BEN | Pengelola mengimpor tagihan dan mencetak/mengekspor daftar atau laporan tagihan. | route import/cetak/template | Tagihan controllers; `TagihanImport` | Tagihan, XLSX/PDF | `app/Imports/TagihanImport.php`; tagihan views |
| FEAT-040 | KEU | Carryover tunggakan | ADM, BEN | Pengelola mempratinjau dan mengalihkan tunggakan tahun lama ke tahun aktif. | route `tagihan/carryover*` | Tagihan controller; `TunggakanCarryoverService` | Tagihan asal/tujuan | `routes/web.php:465-475,1004-1014`; service |
| FEAT-041 | KEU | Input pembayaran manual | ADM, BEN | Pengelola mencatat pembayaran tunai/transfer untuk siswa. | `/pembayaran/create|store` | Pembayaran controllers | Pembayaran, Tagihan | `routes/web.php:477-487,1016-1032` |
| FEAT-042 | KEU | Validasi pembayaran | ADM, BEN | Pengelola menyetujui/menolak pembayaran dan memperbarui status tagihan/audit. | route pembayaran validate | Pembayaran controllers | Pembayaran, Tagihan, FinancialAuditLog | payment controllers/models |
| FEAT-043 | KEU | Pembayaran transfer wali | ORT | Orang tua mengajukan pembayaran transfer anak dengan bukti dan menunggu validasi. | `POST /wali-siswa/tagihan/anak/{siswa}/bayar|bulk-pay` | `OrangTuaController@prosesBayar/processBulkPay` | Pembayaran, upload bukti | `routes/web.php:1553-1558`; controller |
| FEAT-044 | KEU | Pembayaran Midtrans | ORT | Orang tua membayar satu/lebih tagihan anak melalui Midtrans Snap dan melanjutkan transaksi pending. | `/wali-siswa/pembayaran*` | `OrangTuaController`; `MidtransService` | Pembayaran, Snap token | `routes/web.php:1560-1566`; service |
| FEAT-045 | KEU | Webhook/status payment | Sistem | Sistem memverifikasi signature/status Midtrans dan menyinkronkan pembayaran, tagihan, audit dan notifikasi. | `POST /midtrans/notification`; snap finish | `MidtransWebhookController`; `OrangTuaController@snapFinish` | Pembayaran, AuditLog | `routes/web.php:87-88`; webhook/service |
| FEAT-046 | KEU | Konfigurasi kanal bayar | ADM, BEN | Pengelola mengatur rekening, loket tunai, key/mode/enable Midtrans. | `/admin/keuangan/config*`, `/bendahara/config*` | `InfoPembayaranController` | InfoPembayaran | `routes/web.php:526-533,1034-1041` |
| FEAT-047 | KEU | Laporan keuangan | ADM, BEN | Pengelola memfilter, merekap dan mencetak pembayaran/tagihan/tunggakan. | `/keuangan/laporan*`, `/bendahara/laporan*` | `LaporanPembayaranController` | Tagihan, Pembayaran | `routes/web.php:489-497,1069-1077` |
| FEAT-048 | PRS | Input presensi kelas | WKL | Wali kelas mencatat/mengubah presensi harian siswa kelasnya. | `/wali/presensi*` | `PresensiController@updatePresensi/inputHarian` | Presensi, Siswa, Kelas | `routes/web.php:1114-1131`; controller |
| FEAT-049 | PRS | Rekap/import presensi | WKL | Wali kelas melihat riwayat/rekap, mencetak dan mengimpor presensi. | route riwayat/rekap/import/template | `PresensiController` | Presensi, Excel | controller; `resources/views/wali-kelas/presensi/` |
| FEAT-050 | PRS | Ajukan izin anak | ORT | Orang tua mengajukan atau memperbarui izin ketidakhadiran anak beserta bukti. | `/wali-siswa/presensi/anak/*` | `OrangTuaController@storeIzin/updateIzin` | Presensi, upload bukti | `routes/web.php:1576-1590`; controller |
| FEAT-051 | PRS | Validasi izin | WKL | Wali kelas menyetujui/menolak pengajuan izin siswa kelasnya. | `/wali/presensi/validasi-izin*` | `PresensiController@prosesValidasiIzin` | Presensi | controller; notification service |
| FEAT-052 | PRS | Riwayat presensi | SIS, ORT | Siswa melihat presensinya dan orang tua melihat presensi anak tertaut. | `/siswa/sia/presensi`, `/wali-siswa/presensi/anak/*` | `SiaPresensiController`; `OrangTuaController` | Presensi | `routes/web.php:1426-1434,1576-1590` |
| FEAT-053 | NIL | Input nilai guru | GRU | Guru melihat, mengubah dan mengimpor nilai siswa hanya pada kelas/mapel yang diampu. | `/guru/lms/{kelas}/{mapel}/nilai*` | `GuruNilaiController` | Nilai, GuruPengajarKelas | `routes/web.php:1365-1373`; controller/tests IDOR |
| FEAT-054 | NIL | Kelola nilai wali kelas | WKL | Wali kelas melihat, mengubah, membersihkan, import/export dan mencetak nilai kelas. | `/wali/nilai*` | `WaliKelas\NilaiController` | Nilai, Siswa, Kelas | `routes/web.php:1135-1148`; controller |
| FEAT-055 | NIL | Sinkronisasi nilai LMS | Sistem, GRU, WKL | Sistem menyinkronkan hasil tugas/ujian ke nilai dan wali dapat menarik nilai guru. | observer; route sync | `NilaiSyncService`; Observers; `NilaiController@syncFromGuru` | Nilai, TugasSiswa, UjianSiswa | `AppServiceProvider.php`; service |
| FEAT-056 | NIL | Koreksi tugas | GRU | Guru menilai jawaban tugas siswa, termasuk bantuan AI bila dikonfigurasi. | route `/tugas/*/koreksi*` | `GuruKoreksiController`; `AiGradingService` | TugasSiswa, Nilai | `routes/web.php:1279-1285`; controller/service |
| FEAT-057 | NIL | Lihat nilai pribadi | SIS, ORT | Siswa melihat penilaian harian; orang tua melihat informasi akademik anak melalui portal/rapor. | `/siswa/sia/penilaian` | `SiaDashboardController`; parent views | Nilai | `routes/web.php:1436-1438`; views siswa/wali-siswa |
| FEAT-058 | RAP | Generate rapor | WKL | Wali kelas menghasilkan rapor satu siswa atau seluruh kelas dari nilai/presensi. | `/wali/rapor/generate*` | `RaporController@generateAll/generateSingle/createWithMode` | Rapor, RaporNilai, Presensi | `routes/web.php:1161-1191`; controller |
| FEAT-059 | RAP | Edit/format rapor | WKL | Wali kelas mengubah nilai/deskripsi/ekstra, urutan, template dan autofill kehadiran. | `/wali/rapor/{id}*`, template routes | `RaporController`; `TemplateCapaianController` | Rapor, TemplateCapaian | controller/routes |
| FEAT-060 | RAP | Terbit/tarik/reset rapor | WKL | Wali kelas menerbitkan, menarik kembali, reset, atau menghapus rapor sesuai status. | route terbitkan/tarik/reset/delete | `RaporController` | Rapor.status | controller |
| FEAT-061 | RAP | Kirim validasi Ketua | WKL | Wali kelas mengirim/batal kirim satu atau semua rapor untuk validasi Ketua. | route `kirim-validasi*` | `RaporController` | Rapor, notification | `routes/web.php:1183-1189`; controller |
| FEAT-062 | RAP | Validasi/revisi rapor | KET | Ketua mempratinjau, memvalidasi, membatalkan, atau meminta revisi rapor. | `/ketua/validasi-rapor*` | `Ketua\ValidasiRaporController` | Rapor, Siswa | `routes/web.php:737-746`; controller |
| FEAT-063 | RAP | Request/download rapor | ORT, WKL | Orang tua meminta download; wali kelas menyetujui/menolak; token valid dipakai mengunduh. | parent request/download; `/wali/rapor/request-download*` | `OrangTuaController`; `RaporController` | RequestDownloadRapor, file/PDF | routes/controller/model |
| FEAT-064 | RAP | Arsip rapor kelas | WKL | Wali kelas melihat arsip rapor, nilai dan presensi kelas yang pernah diwalikan. | `/wali/arsip*` | `WaliKelasArsipController` | Kelas historis, Rapor, Nilai, Presensi | `routes/web.php:1152-1160` |
| FEAT-065 | LMS | Dashboard LMS guru | GRU | Guru membuka ruang belajar untuk kombinasi kelas-mapel yang diampu. | `/guru/lms/{kelas}/{mapel}` | `GuruLmsController@dashboard` | assignment, konten LMS | `routes/web.php:1255-1259` |
| FEAT-066 | LMS | Kelola materi | GRU | Guru membuat, mengubah, menghapus dan mengunggah materi sesuai kelas-mapel. | `/guru/lms/{kelas}/{mapel}/materi*` | `GuruMateriController` | Materi, storage | `routes/web.php:1260-1269` |
| FEAT-067 | LMS | Baca materi | SIS | Siswa melihat materi pada mapel kelasnya. | `/siswa/lms/mata-pelajaran/{mapelId}/materi*` | `LmsMateriController` | Materi | `routes/web.php:1485-1493` |
| FEAT-068 | LMS | Kelola tugas | GRU | Guru membuat, mengubah, menghapus dan mengunggah tugas/latihan non-ujian. | `/guru/lms/{kelas}/{mapel}/tugas*` | `GuruTugasController` | Tugas, storage | `routes/web.php:1270-1286` |
| FEAT-069 | LMS | Submit tugas | SIS | Siswa melihat dan mengirim jawaban teks/file tugas sebelum kondisi penutupan. | `/siswa/lms/mata-pelajaran/{mapelId}/tugas*` | `LmsTugasController` | TugasSiswa, storage | `routes/web.php:1493-1499` |
| FEAT-070 | LMS | Kelola ujian | GRU | Guru membuat, mengubah, menghapus, melihat hasil, dan mengoreksi ujian. | `/guru/lms/{kelas}/{mapel}/ujian*` | `GuruUjianController` | Ujian, UjianSiswa | `routes/web.php:1287-1326` |
| FEAT-071 | LMS | Bank soal | GRU | Guru mengelola soal tunggal/bulk serta import/export template soal. | route `soal`, `manage-soal`, `import/export` | `GuruUjianController` | SoalUjian | `routes/web.php:1304-1326` |
| FEAT-072 | LMS | Generator soal AI | GRU | Guru menghasilkan draf bank soal melalui Groq/Gemini lalu memvalidasi hasilnya. | route `generate-ai*` | `GuruUjianController`; `AiQuestionGeneratorService` | AppSetting, SoalUjian draft | `routes/web.php:1312-1315,1350-1353`; service |
| FEAT-073 | LMS | Kerjakan ujian | SIS | Siswa memulai, autosave, submit, review, dan bila diizinkan mengulang ujian. | `/siswa/lms/.../ujian*` | `LmsUjianController` | UjianSiswa, JawabanSiswa | `routes/web.php:1500-1510`; controller |
| FEAT-074 | LMS | Pengawasan ujian | GRU, SIS | Sistem mencatat status soal/aktivitas dan guru memonitor peserta ujian. | route pengawasan/monitoring | `GuruUjianController`; `LmsUjianController` | UjianPengawasanLog, UjianSiswaSoalStatus | migration `2026_05_30*`; controllers |
| FEAT-075 | LMS | Kelola latihan | GRU | Guru membuat dan mengelola latihan beserta soal/hasilnya. | `/guru/lms/{kelas}/{mapel}/latihan*` | `GuruUjianController` (tipe latihan) | Ujian tipe latihan, Soal | `routes/web.php:1327-1364` |
| FEAT-076 | LMS | Kerjakan latihan | SIS | Siswa mengerjakan dan mengulang latihan sesuai aturan. | `/siswa/lms/.../latihan*` | `LmsUjianController` | UjianSiswa | `routes/web.php:1511-1520` |
| FEAT-077 | LMS | Forum diskusi | GRU, SIS | Guru/siswa membuat diskusi, membalas, mengubah atau menghapus balasan dalam scope mapel. | route `/forum*` | `GuruForumController`; `LmsForumController` | ForumDiskusi, ForumReply | `routes/web.php:1374-1387,1521-1531` |
| FEAT-078 | LMS | Kelas virtual | GRU, SIS | Guru mengelola tautan meeting dan siswa membuka meeting mapel. | `/meeting*` | `GuruLmsMeetingController`; `SiswaLmsMeetingController` | LmsMeeting | `routes/web.php:1388-1397,1532-1535` |
| FEAT-079 | LMS | Arsip/salin konten | GRU | Guru melihat konten historis dan menyalin materi/tugas/ujian ke assignment aktif. | `/guru/lms/arsip*` | `GuruLmsArsipController`; `GuruLmsArsipService` | Materi, Tugas, Ujian, Soal | `routes/web.php:1242-1253`; service/tests |
| FEAT-080 | PRM | Atur KKM | ADM, WKA | Pengelola menentukan KKM per jenjang/mapel/tahun aktif. | `/kenaikan-kelas/kkm*` | `PromotionKKMController` | pengaturan_kkm | `routes/web.php:613-618,910-912` |
| FEAT-081 | PRM | Atur kriteria kenaikan | ADM, WKA | Pengelola menentukan bobot/ambang dan tujuan kenaikan kelas. | `/kenaikan-kelas/settings*` | `PromotionSettingsController` | pengaturan_naik_kelas, TahunAjaran | routes/controller |
| FEAT-082 | PRM | Prediksi kelayakan | WKL | Wali kelas melihat prediksi kenaikan berdasarkan akademik dan finansial. | `/wali/kenaikan-kelas/prediction` | `WaliKelas\PromotionController` | Nilai, Tagihan, StatusNaikKelas | `routes/web.php:1132-1134`; `PromotionService` |
| FEAT-083 | PRM | Validasi finansial | ADM, BEN | Admin/Bendahara memberi validasi/override finansial siswa untuk kenaikan. | `/keuangan/kenaikan-kelas*`, `/bendahara/kenaikan-kelas*` | PromotionValidation controllers | StatusNaikKelasSiswa, Tagihan | `routes/web.php:534-547,1079-1093` |
| FEAT-084 | PRM | Approval Ketua | KET | Ketua menyetujui/menolak satu atau bulk usulan kenaikan kelas dan melihat history. | `/ketua/kenaikan-kelas*` | `PromotionApprovalController` | StatusNaikKelasSiswa | `routes/web.php:728-735` |
| FEAT-085 | PRM | Eksekusi/rollback kenaikan | ADM, WKA | Pengelola mengeksekusi, rollback, atau mempromosikan terpilih dengan transaction. | `/akademik/kenaikan-kelas/report*` | `PromotionReportController`; `PromotionService` | Siswa, Kelas, status/history | `routes/web.php:597-623,890-914`; service |
| FEAT-086 | PRM | Jadwal kenaikan kelas | ADM, WKA, Sistem | Pengelola menjadwalkan eksekusi; scheduler mengeksekusi tanpa overlap. | route scheduling; Artisan command | `PromotionReportController`; `ExecuteScheduledPromotion` | PromotionSchedule | `routes/console.php`; `PromotionSchedule.php` |
| FEAT-087 | MON | Monitoring pengguna/akademik | ADM, KET, WKA | Pimpinan melihat ringkasan pengguna, siswa, guru, dan wali kelas sesuai scope. | `/admin|ketua|waka/monitoring/*` | Monitoring/Ketua/Waka controllers | User, Siswa, TenagaPendidik, Kelas | `routes/web.php:640-655,691-706,855-869` |
| FEAT-088 | MON | Monitoring LMS | ADM, KET, WKA | Pimpinan melihat konten/progres LMS dan preview konten sesuai cabang. | `/monitoring/lms*` | controllers; `LmsMonitoringService` | Materi, Tugas, Ujian | same route groups; service |
| FEAT-089 | MON | Catatan/teguran | ADM, KET, WKA | Pimpinan mengirim, melihat dan menghapus catatan kepada pengguna/guru. | `/catatan*`, monitor `kirim-catatan` | controllers; `NotificationService` | Catatan, CatatanDibaca | `routes/web.php:667-674,719-726,870-877` |
| FEAT-090 | MON | Catatan monitoring guru | GRU | Guru melihat catatan monitoring yang ditujukan kepadanya. | `/guru/lms/catatan-monitoring*` | `GuruCatatanMonitoringController` | CatatanMonitoring | `routes/web.php:1399-1407` |
| FEAT-091 | NOT | Pusat notifikasi | Semua login | Pengguna melihat daftar/recent/hari ini dan detail notifikasi sendiri. | `/notifications*` GET | `NotificationController` | Notification | `routes/web.php:1612-1621` |
| FEAT-092 | NOT | Kelola status notifikasi | Semua login | Pengguna menandai baca, bulk action, atau menghapus notifikasi sendiri. | `/notifications*` POST/DELETE | `NotificationController` | Notification | same source |
| FEAT-093 | NOT | Reminder terjadwal | Sistem | Sistem mengirim pengingat deadline H-1 dan kalender H-3 setiap hari. | `notifications:schedule` | `NotificationScheduler`; `NotificationService` | Notification, tugas/ujian/kalender | `routes/console.php:11-16`; command |
| FEAT-094 | SWA | Dashboard SIA/LMS siswa | SIS | Siswa berpindah antara dashboard akademik dan LMS berdasarkan status/akses. | `/siswa/dashboard`, `/siswa/sia`, `/siswa/lms` | `SiswaDashboardController`; SIA/LMS controllers | Siswa, Kelas, jadwal, konten | `routes/web.php:1411-1545` |
| FEAT-095 | SWA | Informasi akademik pribadi | SIS | Siswa melihat jadwal, guru, kalender, presensi, materi, tugas, ujian dan nilai miliknya. | route `/siswa/sia/*`, `/siswa/lms/*` | Siswa controllers | entitas akademik milik siswa | `routes/web.php:1421-1545` |
| FEAT-096 | SWA | Portal alumni read-only | SIS alumni | Alumni hanya melihat dashboard/riwayat akademik yang diizinkan. | route SIA dashboard | `CheckStudentActive`; dashboard siswa | Siswa.status=lulus | `app/Http/Middleware/CheckStudentActive.php` |
| FEAT-097 | WLS | Dashboard anak | ORT | Orang tua melihat ringkasan seluruh anak yang ditautkan ke akunnya. | `/wali-siswa/dashboard` | `OrangTuaController@dashboard` | StudentParent, Siswa | `routes/web.php:1548-1552`; controller |
| FEAT-098 | WLS | Tagihan dan invoice anak | ORT | Orang tua melihat rincian tagihan, pembayaran, dan mencetak invoice anak. | `/wali-siswa/tagihan/anak/*`, `/pembayaran/{id}/invoice` | `OrangTuaController` | Tagihan, Pembayaran | `routes/web.php:1553-1566` |
| FEAT-099 | WLS | Akademik anak | ORT | Orang tua melihat rapor, nilai/presensi anak dan riwayat izin. | `/wali-siswa/rapor*`, `/presensi*` | `OrangTuaController` | Rapor, Presensi, Siswa | `routes/web.php:1568-1590` |
| FEAT-100 | WLS | Akses download rapor anak | ORT | Orang tua meminta dan memakai token download rapor setelah disetujui wali kelas. | `/wali-siswa/rapor/request-download|download` | `OrangTuaController` | RequestDownloadRapor | parent routes; controller/model |
| FEAT-101 | KEU | Lihat tagihan/riwayat siswa | SIS | Siswa melihat tagihan dan riwayat pembayaran miliknya serta mencetak bukti; aksi membayar dari portal Siswa dinonaktifkan. | `/siswa/sia/pembayaran*` | `SiaPembayaranController@index/riwayat/cetakBukti`; `prosesBayar` menolak | Tagihan, Pembayaran, Siswa | `routes/web.php:1440-1449`; `SiaPembayaranController.php`; sidebar Siswa |

# Route Coverage

`php artisan route:list --json` menghasilkan 804 route. Tabel ini mengelompokkan seluruh route bisnis berdasarkan entry point; route framework `_ignition` dan health `/up` tidak diturunkan menjadi fitur bisnis.

| Modul | Method | URI/Route Name Pattern | Middleware | Handler Utama | Feature ID |
|---|---|---|---|---|---|
| AU | GET/POST | `/login`, `/logout`, `/recovery*`, `/admin-recovery*` | `guest`, throttle; `auth` logout | Auth controllers | FEAT-001–004 |
| AU | GET/POST/PUT/DELETE | `/account*`, `/profile*` | `auth` | Account/Profile controllers | FEAT-005–006 |
| USR | Semua web | `/admin/users/*`, `/admin/recovery-tickets/*` | `auth, role:admin` | User/RecoveryTicket controllers | FEAT-007–013 |
| ORG | Semua web | `/admin/{cabang,tahun-ajaran,kelas,wali-kelas,manajemen-siswa}*` | `auth, role:admin` | Admin controllers | FEAT-014–020 |
| ORG/AKD | Semua web | `/waka/{tahun-ajaran,kelas,wali-kelas,manajemen-siswa,mata-pelajaran,jadwal-pelajaran,guru-pengajar}*` | `auth, role:wakil_kepala_sekolah` | Waka controllers | FEAT-015–029 |
| AKD | Semua web | `/admin/{mata-pelajaran,jadwal-pelajaran,guru-pengajar,pengaturan-istirahat,cetak-laporan}*` | `auth, role:admin` | Admin controllers | FEAT-021–030 |
| KON | Semua web | `/admin/akademik/{kalender,pengumuman,berita,flyer}*`, `/sekretaris/*` | Admin/Sekretaris role | Akademik/Sekretaris controllers | FEAT-031–034 |
| KON | GET/PUT | `/admin/landing-pages*` dan route public | Admin atau public web | Landing controllers | FEAT-035–036 |
| KEU | Semua web | `/admin/keuangan/*`, `/bendahara/*` | Admin/Bendahara role | Keuangan controllers | FEAT-037–047 |
| KEU/WLS | GET/POST | `/wali-siswa/tagihan*`, `/wali-siswa/pembayaran*` | `auth, role:orang_tua` | OrangTuaController | FEAT-043–045, 098 |
| KEU | POST | `/midtrans/notification` | web; CSRF exception; no auth | MidtransWebhookController | FEAT-045 |
| PRS/NIL/RAP | Semua web | `/wali/{presensi,nilai,rapor,arsip,validasi-akses}*` | `auth, role:wali_kelas` | WaliKelas controllers | FEAT-048–064 |
| LMS/NIL | Semua web | `/guru/*`, khususnya `/guru/lms/{kelas}/{mapel}/*` | Guru role + throttle AI | Guru controllers | FEAT-053, 056, 065–079, 090 |
| LMS/SWA | GET/POST | `/siswa/lms/*`, `/siswa/sia/*` | siswa, student.active; LMS/mapel conditional | Siswa controllers | FEAT-052, 057, 067, 069, 073, 076–078, 094–096 |
| PRM | Semua web | `/admin/akademik/kenaikan-kelas*`, `/waka/kenaikan-kelas*`, `/ketua/kenaikan-kelas*`, `/bendahara/kenaikan-kelas*` | role masing-masing | Promotion controllers | FEAT-080–086 |
| MON | GET/POST/DELETE | `/admin|ketua|waka/monitoring*`, `/catatan*` | role masing-masing | Monitoring/Ketua/Waka controllers | FEAT-087–090 |
| NOT | GET/POST/DELETE | `/notifications*` | `auth` | NotificationController | FEAT-091–093 |

# Data Entity Inventory

| Entity | Model | Table | Modul Terkait | Fungsi | Relationship Penting |
|---|---|---|---|---|---|
| Pengguna | `User` | `users` | AU, USR, semua | Identitas/login/role | Role, Cabang, Siswa, TenagaPendidik, children |
| Role | `Role` | `roles` | AU, USR | Definisi 9 role | hasMany User |
| Tiket recovery | `RecoveryTicket` | `recovery_tickets` | AU, USR | Pemulihan akun | belongsTo User |
| Cabang | `Cabang` | `cabang` | ORG | Unit organisasi | User, Siswa, Kelas |
| Tahun ajaran | `TahunAjaran` | `tahun_ajaran` | ORG, AKD, KEU, PRM | Scope periode | Kelas, Tagihan, Nilai, Rapor |
| Tenaga pendidik | `TenagaPendidik` | `tenaga_pendidik` | USR, AKD, LMS | Profil staf pengajar | User, Jadwal, Kelas, konten LMS |
| Siswa | `Siswa` | `siswa` | USR, ORG, seluruh akademik | Profil peserta didik | User, Kelas, parents, tagihan, nilai, rapor |
| Relasi orang tua | `StudentParent` | `student_parents` | ORG, WLS | Ownership anak | Siswa ↔ User orang_tua |
| Kelas | `Kelas` | `kelas` | ORG, AKD | Rombel per cabang/TA | Siswa, wali, jadwal, LMS |
| Assignment wali | `WaliKelasAssignment` | `wali_kelas_assignments` | ORG | Wali multi-kelas/historis | TenagaPendidik ↔ Kelas |
| Mata pelajaran | `MataPelajaran` | `mata_pelajaran` | AKD, LMS, NIL | Data mapel/filter agama | Jadwal, LMS, Nilai |
| Jadwal | `JadwalPelajaran` | `jadwal_pelajaran` | AKD | Jadwal guru-mapel | belongsToMany Kelas via `jadwal_kelas` |
| Riwayat jadwal | `JadwalPelajaranHistory` | `jadwal_pelajaran_history` | AKD | Audit perubahan jadwal | Jadwal, changed_by |
| Assignment guru | `GuruPengajarKelas` | `guru_pengajar_kelas` | AKD, LMS | Scope guru-kelas-mapel | Guru, Kelas, Mapel |
| Waktu istirahat | `PengaturanIstirahat` | `pengaturan_istirahat` | AKD | Interval non-pelajaran | jenjang/status |
| Kalender | `KalenderAkademik` | `kalender_akademik` | KON | Agenda akademik | TahunAjaran, Pengumuman |
| Pengumuman | `Pengumuman` | `pengumuman` | KON | Informasi pengguna | Kalender, pembuat |
| Berita/Flyer | `Berita`, `Flyer` | `berita`, `flyer` | KON | Konten publik | pembuat |
| Landing page | `LandingPage`, `LandingPageSection` | `landing_pages`, `landing_page_sections` | KON | CMS halaman publik | page hasMany sections |
| Tagihan | `Tagihan` | `tagihan` | KEU, PRM | Kewajiban siswa | Siswa, TA, pembayaran, carryover self-relations |
| Pembayaran | `Pembayaran` | `pembayaran` | KEU | Transaksi manual/digital | Tagihan, Siswa, validator, order group |
| Info pembayaran | `InfoPembayaran` | `info_pembayaran` | KEU | Rekening/loket/Midtrans | updated_by |
| Audit keuangan | `FinancialAuditLog` | `financial_audit_logs` | KEU | Jejak mutasi finansial | User/pelaku |
| Batas pembayaran | `PengaturanBatasPembayaran` | `pengaturan_batas_pembayaran` | KEU | Aturan akses finansial | TA, creator |
| Presensi | `Presensi` | `presensi` | PRS | Kehadiran/izin | Siswa, Kelas, Mapel, inputter |
| Nilai | `Nilai` | `nilai` | NIL, RAP, PRM | Nilai akademik teragregasi | Siswa, Mapel, Kelas, TA, Guru |
| Materi | `Materi` | `materi` | LMS | Konten belajar | Kelas, Mapel, Guru |
| Tugas/submission | `Tugas`, `TugasSiswa` | `tugas`, `tugas_siswa` | LMS, NIL | Penugasan/jawaban/nilai | Guru, Siswa |
| Ujian/soal | `Ujian`, `SoalUjian` | `ujian`, `soal_ujian` | LMS | Ujian/latihan dan bank soal | Kelas, Mapel, Guru, submissions |
| Pengerjaan ujian | `UjianSiswa`, `JawabanSiswa` | `ujian_siswa`, `jawaban_siswa` | LMS, NIL | Session dan jawaban siswa | Ujian, Siswa, Soal |
| Pengawasan ujian | `UjianPengawasanLog`, `UjianSiswaSoalStatus` | `ujian_pengawasan_logs`, `ujian_siswa_soal_statuses` | LMS | Monitoring aktivitas/status soal | UjianSiswa, Soal |
| Forum | `ForumDiskusi`, `ForumReply` | `forum_diskusi`, `forum_replies` | LMS | Diskusi/reply bertingkat | User, Kelas, Mapel |
| Meeting | `LmsMeeting` | `lms_meetings` | LMS | Tautan kelas virtual | Kelas, Mapel, Guru |
| Rapor | `Rapor`, `RaporNilai`, `RaporKegiatanEkstra` | `rapor`, `rapor_nilai`, `rapor_kegiatan_ekstra` | RAP | Dokumen hasil belajar | Siswa, Kelas, TA, Nilai |
| Template capaian | `TemplateCapaianKompetensi` | `template_capaian_kompetensi` | RAP | Template deskripsi mapel | Mapel, creator |
| Request download | `RequestDownloadRapor` | `request_download_rapor` | RAP, WLS | Workflow token unduh | Rapor, Siswa, requester, decider |
| Pengajuan rapor Ketua | `PengajuanRaporKetua` | `pengajuan_rapor_ketua` | RAP, KEU | Dispensasi/keputusan rapor | Siswa, pengaju, pemutus |
| Status kenaikan | `StatusNaikKelasSiswa` | `status_naik_kelas_siswa` | PRM | Kelayakan/approval/history | Siswa, TA, kelas asal, rollback user |
| Jadwal promotion | `PromotionSchedule` | `promotion_schedules` | PRM | Eksekusi terjadwal | TA, creator |
| Catatan | `Catatan`, `CatatanDibaca`, `CatatanMonitoring` | `catatan*` | MON | Teguran dan status baca | pengirim/penerima/guru/kelas/mapel |
| Notifikasi | `Notification` | `notifications` | NOT | Pesan in-app | belongsTo User |
| Setting aplikasi | `AppSetting` | `app_settings` | LMS, AU | AI, LMS allowed jenjang, feature flags | key-value |

Tabel framework tidak diperlakukan sebagai entitas bisnis, tetapi `sessions`, `cache`, dan `jobs` tetap dicatat pada audit teknologi.
