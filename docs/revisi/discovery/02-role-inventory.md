# Role Inventory

> Role di bawah hanya berasal dari implementation evidence. Kode role dokumentasi (`ADM`, dan seterusnya) adalah identifier traceability Stage 0, bukan nilai database baru.

## Role Definition Sources

Sumber definisi utama adalah `database/seeders/RoleSeeder.php` (9 record role), migration `roles` dan foreign key `users.role_id`, kolom legacy `users.role`, helper `App\Models\User`, middleware `CheckRole`, pengalihan dashboard/login, route group, serta sidebar per role. Route count diperoleh dari `php artisan route:list --json`; Admin juga mendapat bypass middleware ke route role lain.

## Role Table

| Kode Role | Role | Nilai Implementasi | Source | Hak Akses Utama | Modul |
|---|---|---|---|---|---|
| ADM | Admin | `admin` | `RoleSeeder`; `User::isAdmin`; `routes/web.php:227-685` | Konfigurasi dan administrasi penuh; bypass seluruh middleware `role:*` | Semua modul |
| KET | Ketua PKBM | `ketua_pkbm` | `RoleSeeder`; `User::isKetuaPKBM`; route `/ketua` | Monitoring lintas unit, laporan, approval kenaikan kelas, validasi rapor, keputusan dispensasi | MON, PRM, RAP, DAS |
| WKA | Wakil Kepala Sekolah | `wakil_kepala_sekolah` | `RoleSeeder`; `User::isWakilKepalaSekolah`; route `/waka` | Manajemen akademik dan monitoring terbatas pada cabang akun | ORG, AKD, MON, PRM, DAS |
| SEK | Sekretaris | `sekretaris` | `RoleSeeder`; `User::isSekretaris`; route `/sekretaris` | Kalender akademik, pengumuman, flyer, berita | KON, DAS |
| BEN | Bendahara | `bendahara` | `RoleSeeder`; `User::isBendahara`; route `/bendahara` | Tagihan, pembayaran, laporan, konfigurasi kanal, validasi akses keuangan, validasi finansial kenaikan | KEU, PRM, DAS |
| WKL | Wali Kelas | `wali_kelas` | `RoleSeeder`; `User::isWaliKelas`; route `/wali` | Presensi kelas, nilai, rapor, validasi akses, arsip kelas, prediksi kenaikan | PRS, NIL, RAP, KEU, PRM, DAS |
| GRU | Guru Pengajar | `guru_pengajar` | `RoleSeeder`; `User::isGuruPengajar`; route `/guru` | Kelas/mapel yang diampu, materi, tugas, ujian/latihan, nilai, forum, meeting, arsip | LMS, NIL, MON, DAS |
| SIS | Siswa | `siswa` | `RoleSeeder`; `User::isSiswa`; route `/siswa` | SIA pribadi, LMS sesuai kelas/jenjang/mapel, tugas, ujian/latihan, forum, meeting | SWA, LMS, DAS |
| ORT | Orang Tua / Wali Siswa | `orang_tua` | `RoleSeeder`; `User::isOrangTua`; route `/wali-siswa` | Data anak tertaut: tagihan/payment, rapor, presensi, izin | WLS, KEU, PRS, RAP, DAS |

Route role langsung: ADM 278, KET 37, WKA 109, SEK 30, BEN 55, WKL 75, GRU 102, SIS 43, ORT 19. Angka ini tidak sama dengan jumlah fitur karena satu fitur dapat memiliki beberapa endpoint dan alias.

## Authentication & Authorization Matrix

Legenda: ✓ akses; ✕ tidak memiliki route/menu role; △ terbatas/kondisional. Admin ditandai ✓ karena `CheckRole` memberi bypass ke semua route ber-middleware role, selain route Admin sendiri.

| Modul/Fitur | ADM | KET | WKA | SEK | BEN | WKL | GRU | SIS | ORT | Keterangan |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|---|
| Login/logout, akun, profil, notifikasi | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Route `auth`; akun nonaktif ditolak |
| User dan role | ✓ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | Admin only |
| Recovery ticket administration | ✓ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | Recovery request sendiri tersedia guest |
| Cabang | ✓ | ✕ | △ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | Waka memakai cabang pada akunnya sebagai scope, tidak CRUD cabang |
| Tahun ajaran | ✓ | ✕ | △ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | Waka CRUD; controller menerapkan scope/aturan bisnis |
| Kelas dan anggota kelas | ✓ | ✕ | △ | ✕ | ✕ | △ | △ | △ | △ | Waka terbatas cabang; aktor lain hanya data terkait |
| Mata pelajaran | ✓ | ✕ | △ | ✕ | ✕ | △ | △ | △ | ✕ | Waka kelola; Guru/Siswa berdasarkan assignment/jadwal |
| Jadwal pelajaran | ✓ | ✕ | △ | ✕ | ✕ | △ | △ | △ | ✕ | Waka cabang; Wali/Guru/Siswa read-only miliknya |
| Kalender/pengumuman/berita/flyer | ✓ | ✕ | ✕ | ✓ | ✕ | ✕ | △ | △ | ✕ | Guru/Siswa mengonsumsi publikasi tertentu |
| Monitoring organisasi/LMS | ✓ | ✓ | △ | ✕ | ✕ | ✕ | △ | ✕ | ✕ | Waka cabang; Guru menerima catatan |
| Tagihan | ✓ | ✕ | ✕ | ✕ | ✓ | ✕ | ✕ | △ | △ | Siswa view; Orang Tua view/bayar anak tertaut |
| Pembayaran manual dan validasi | ✓ | ✕ | ✕ | ✕ | ✓ | ✕ | ✕ | ✕ | △ | Orang Tua dapat mengajukan transfer, bukan memvalidasi |
| Pembayaran Midtrans | ✓ | ✕ | ✕ | ✕ | ✓ | ✕ | ✕ | ✕ | △ | Konfigurasi Admin/Bendahara; pembayaran Orang Tua |
| Laporan keuangan | ✓ | △ | ✕ | ✕ | ✓ | ✕ | ✕ | ✕ | ✕ | Ketua memiliki laporan umum, bukan controller laporan pembayaran Bendahara |
| Validasi akses finansial | ✓ | △ | ✕ | ✕ | ✓ | △ | ✕ | △ | ✕ | Wali validasi kelas; Bendahara finansial; Ketua dispensasi; siswa dikondisikan service |
| Presensi kelas | ✓ | ✕ | ✕ | ✕ | ✕ | ✓ | ✕ | △ | △ | Siswa view; Orang Tua view dan ajukan izin |
| Nilai harian | ✓ | ✕ | ✕ | ✕ | ✕ | ✓ | ✓ | △ | △ | Guru mapel, Wali kelas; siswa/ortu view |
| Materi/tugas | ✓ | △ | △ | ✕ | ✕ | △ | ✓ | △ | ✕ | Ketua/Waka/Admin monitor; Siswa mengonsumsi/submit |
| Ujian/latihan | ✓ | △ | △ | ✕ | ✕ | △ | ✓ | △ | ✕ | Siswa perlu status aktif, LMS aktif, mapel sesuai dan akses finansial |
| Forum/meeting | ✓ | △ | △ | ✕ | ✕ | ✕ | ✓ | △ | ✕ | Monitoring oleh pimpinan; partisipasi Guru/Siswa |
| Rapor | ✓ | △ | ✕ | ✕ | ✕ | ✓ | ✕ | ✕ | △ | Ketua validasi; Orang Tua view/download berizin; route rapor siswa nonaktif |
| Kenaikan kelas | ✓ | ✓ | △ | ✕ | △ | △ | ✕ | ✕ | ✕ | Rantai akademik, finansial, approval |
| Konten landing page | ✓ | ✕ | ✕ | △ | ✕ | ✕ | ✕ | ✕ | ✕ | Sekretaris kelola berita/flyer; Admin landing section |
| AI settings | ✓ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | ✕ | Admin only |
| AI chatbot | △ | △ | △ | △ | △ | △ | △ | △ | △ | Role yang aktif ditentukan `chatbot_enabled_roles` |
| AI generator/koreksi | ✓ | ✕ | ✕ | ✕ | ✕ | ✕ | △ | ✕ | ✕ | Generator soal pada route Guru; koreksi AI pada alur Guru |

## Authorization Conditions

- **Admin bypass:** `CheckRole::handle()` langsung melanjutkan request jika `User::isAdmin()`.
- **Waka cabang:** controller Kelas, Jadwal, Wali Kelas, Guru Pengajar, Manajemen Siswa dan monitoring membatasi `cabang_id`; akun tanpa cabang ditolak.
- **Wali Kelas assignment:** kelas aktif dipilih dari `wali_kelas_assignments`/`kelas.wali_kelas_id`; akses objek kelas lain ditolak.
- **Guru assignment:** materi, tugas, ujian, nilai, forum, meeting, dan arsip di-scope ke `GuruPengajarKelas`/riwayat pengampu.
- **Siswa:** harus berstatus aktif untuk alur aktif; alumni read-only; LMS harus diaktifkan untuk jenjang; mapel harus ada pada jadwal kelas dan sesuai filter agama.
- **Orang Tua:** setiap siswa/tagihan/pembayaran/presensi/rapor harus ditemukan melalui relasi `children()`/`student_parents`.
- **File:** signed URL terikat owner dan path yang diizinkan.

Sumber: `app/Http/Middleware/`, controller masing-masing role, `app/Models/User.php`, `tests/Feature/*IdorTest.php`.

## Naming Inconsistencies

| Area | Bentuk 1 | Bentuk 2 | Dampak |
|---|---|---|---|
| Orang tua | Database/route: `orang_tua`; UI sering: “Wali Siswa”; seeder display: “Orang Tua/Wali” | `User::getRoleLabelAttribute()` menghasilkan “Wali Siswa” | Requirement harus memakai “Orang Tua/Wali Siswa” dan mencantumkan nilai teknis |
| Wakil kepala | Database: `wakil_kepala_sekolah` | Route/UI: `waka` | Kode dokumentasi WKA dipakai konsisten |
| Wali kelas | Role `wali_kelas` | Prefix route `wali` | Jangan tertukar dengan prefix `wali-siswa` untuk Orang Tua |
| Role storage | `users.role_id` → `roles.name` | `users.role` legacy | Hasil dapat berbeda jika kedua kolom tidak sinkron |
| Admin tertinggi | Helper `isSuperAdmin()` pada User berarti Admin | `Role::isSuperAdmin()` mencari `super_admin`, padahal seeder tidak membuat role itu | Helper model tidak konsisten; route menggunakan Admin |
| Permission | JSON `roles.permissions` | enforcement route berbasis role/guard manual | Permission seeder bersifat deskriptif/future-proof, bukan ACL granular aktif |

## Items Requiring Confirmation

1. `[PERLU KONFIRMASI]` Apakah sembilan role seeder seluruhnya digunakan oleh pengguna nyata di production.
2. `[PERLU KONFIRMASI]` Apakah ada data pengguna yang `role_id` dan kolom legacy `role` tidak sinkron.
3. `[PERLU KONFIRMASI]` Apakah istilah resmi laporan akhir untuk `orang_tua` adalah “Orang Tua”, “Wali Siswa”, atau “Orang Tua/Wali Siswa”.
4. `[PERLU KONFIRMASI]` Apakah Admin memang secara kebijakan bisnis boleh mengakses semua route role lain, sesuai bypass saat ini.
5. `[PERLU VERIFIKASI]` Apakah permissions JSON pernah digunakan oleh aplikasi lain di luar repository ini; dalam repository tidak ditemukan enforcement-nya.
