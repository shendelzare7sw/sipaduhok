# Repository Audit

> Stage 0 working document. Baseline audit: 12 Agustus 2026 (Asia/Jakarta). Audit bersifat read-only terhadap implementasi; dokumen lama dipakai sebagai petunjuk lalu diverifikasi terhadap source code.

## Project Overview

SIPADUHOK adalah aplikasi web terpadu untuk administrasi akademik, keuangan, pembelajaran (LMS), rapor, presensi, kenaikan kelas, monitoring, dan portal orang tua pada PKBM House of Knowledge. Baseline Git yang dianalisis adalah branch `latihan-sidang`, commit `c813efea73a20eb721b39cc3c9a56ea39106a75b`. Working tree sebelum audit sudah memiliki file dokumentasi untracked; file tersebut tidak diubah oleh audit ini. Sumber: `routes/web.php`, `app/Http/Controllers/`, `resources/views/`, `git status`, `git rev-parse HEAD`.

Ukuran implementasi yang teramati:

- 804 route terdaftar: 446 GET/HEAD, 262 POST, 43 DELETE, 41 PUT, 6 PUT/PATCH, 4 PATCH, dan 2 route vendor multi-method.
- 99 controller PHP, 53 model Eloquent, 13 service utama ditambah satu service chatbot, 12 middleware, 8 import utama ditambah import khusus Guru/Wali Kelas, dan 11 export/template export.
- 54 migration repository berstatus `Ran` pada database lokal yang terhubung saat audit.
- 200 titik pemanggilan validasi request pada controller/Form Request.
- 40 file test PHP (38 Feature/Unit test file selain base test) dengan 74 metode test.

Sumber: hasil `php artisan route:list --json`, `php artisan migrate:status`, `app/`, dan `tests/`.

## Technology Stack

| Layer | Implementasi | Bukti |
|---|---|---|
| Runtime | PHP `^8.2` | `composer.json` |
| Framework | Laravel `^11.0` | `composer.json`, `bootstrap/app.php` |
| Database | Laravel mendukung SQLite/MySQL/MariaDB/PostgreSQL/SQL Server; database lokal audit memakai konfigurasi `.env` dan semua migration berstatus ran. Dokumen produksi menyebut MySQL 8, tetapi kondisi server produksi tidak diverifikasi. | `config/database.php`, `database/migrations/`, `docs/TABEL_RELASI.md` |
| Templating | Blade | `resources/views/**/*.blade.php` |
| Bundler | Vite 5 | `package.json`, `vite.config.js` |
| UI | Bootstrap 5, Sneat, Tailwind CSS 3, Font Awesome, Bootstrap Icons, Boxicons, SweetAlert2, Chart.js | `package.json`, `resources/css/`, `resources/js/` |
| PDF | DOMPDF; ekstraksi/rasterisasi PDF melalui Spatie packages | `composer.json`, controller export, `config/services.php` |
| Spreadsheet | Laravel Excel (Maatwebsite) | `composer.json`, `app/Imports/`, `app/Exports/` |
| Payment gateway | Midtrans PHP SDK 2.6 | `composer.json`, `app/Services/MidtransService.php` |
| Test | PHPUnit 10.5, SQLite in-memory | `composer.json`, `phpunit.xml`, `tests/` |

`README.md` masih berupa README skeleton Laravel dan tidak menjelaskan produk SIPADUHOK. Ini merupakan gap dokumentasi, bukan bukti bahwa fitur tidak tersedia.

## Backend

Arsitektur dominan adalah Laravel MVC dengan route web terpusat. Alur umumnya `routes/web.php` → middleware autentikasi/role → controller → validasi inline/Form Request → service/model Eloquent → Blade/redirect/JSON. Service terpisah dipakai pada proses lintas-entitas atau integrasi: AI, Midtrans, kenaikan kelas, validasi akses, carryover tunggakan, notifikasi, monitoring LMS, sinkronisasi nilai, arsip LMS, dan email recovery. Sumber: `routes/web.php`, `app/Http/Controllers/`, `app/Services/`.

Tidak ditemukan directory `app/Repositories`, `app/Policies`, `app/Enums`, `app/Jobs`, atau `app/Listeners`. Otorisasi terutama berada pada middleware role serta guard kepemilikan/cabang/kelas di controller dan service. Hanya satu Form Request ditemukan (`LoginRequest`); mayoritas validasi berada inline di controller.

Observer `TugasSiswaObserver` dan `UjianSiswaObserver` disambungkan pada `AppServiceProvider` untuk menyinkronkan nilai LMS. Sumber: `app/Providers/AppServiceProvider.php`, `app/Observers/`, `app/Services/NilaiSyncService.php`.

## Frontend

Frontend adalah server-rendered Blade dengan JavaScript per halaman dan CSS per modul. Terdapat sidebar terpisah untuk Admin, Ketua PKBM, Waka, Sekretaris, Bendahara, Wali Kelas, Guru, Siswa, dan Wali Siswa. Asset dikelola Vite; sebagian asset print berada langsung di `public/`. Sumber: `resources/views/*/partials/*sidebar*.blade.php`, `resources/js/`, `resources/css/`, `public/js/`, `public/css/`, `vite.config.js`.

UI memanggil endpoint JSON pada sejumlah fitur seperti filter tagihan, chatbot, notifikasi, monitoring, dan generator soal. Aplikasi bukan SPA; navigasi utama tetap berbasis route Blade.

## Database

Migration repository membuat 55 tabel (termasuk tabel framework) dan kemudian menghapus referensi tabel sinkronisasi Google Sheets. Entitas domain utama meliputi pengguna/role, organisasi akademik, siswa-orang tua, jadwal, presensi, LMS, nilai/rapor, keuangan, konten publik, notifikasi, recovery, monitoring, dan kenaikan kelas. Sumber: `database/migrations/`.

Tabel framework `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, dan `password_reset_tokens` tersedia. Default `.env.example` menggunakan session/cache/queue berbasis database. Model tidak selalu mencakup seluruh tabel konfigurasi kenaikan kelas; beberapa tabel diakses langsung melalui query builder/service. Sumber: migration dan `config/{session,cache,queue}.php`.

Catatan: `docs/TABEL_RELASI.md` mengklaim berasal dari `information_schema` produksi dan menyebut 58 tabel domain. Angka itu tidak boleh dianggap sama dengan schema hasil migration repository tanpa verifikasi database produksi. `[PERLU VERIFIKASI]`.

## Authentication

Login menerima username atau email dan password. Kredensial divalidasi, Cloudflare Turnstile diverifikasi bila secret key tersedia, rate limit diberlakukan 5 percobaan per kombinasi login+IP, akun nonaktif ditolak, session diregenerasi, dan waktu/IP login dicatat. Logout membatalkan session dan meregenerasi CSRF token. Sumber: `app/Http/Requests/Auth/LoginRequest.php`, `app/Http/Controllers/Auth/LoginController.php`, `routes/web.php`.

Tersedia dua jalur pemulihan:

- pemulihan publik berbasis tiket dan email pribadi;
- pemulihan khusus Admin melalui PIN/pertanyaan keamanan, dengan kewajiban setup keamanan untuk Admin.

Sumber: `app/Http/Controllers/Auth/{UserRecoveryController,AdminRecoveryController,AdminSecuritySetupController}.php`, `app/Services/EmailRecoveryService.php`, `app/Models/RecoveryTicket.php`.

## Authorization

Role ditentukan melalui `role_id → roles.name`, dengan fallback ke kolom legacy `users.role`. Route menggunakan `auth` dan `role:<role>`. `CheckRole` memberi Admin bypass ke semua route yang memakai middleware role dan mengembalikan HTTP 403 untuk role lain yang tidak cocok tanpa logout. Sumber: `app/Models/User.php`, `app/Http/Middleware/CheckRole.php`, `routes/web.php`.

Pembatasan tambahan:

- akun nonaktif dikeluarkan oleh `EnsureUserIsActive`;
- alumni hanya memperoleh akses read-only terbatas oleh `CheckStudentActive`;
- akses LMS siswa dibatasi berdasarkan jenjang dalam `app_settings` oleh `CheckLmsAccess`;
- akses mata pelajaran siswa dibatasi berdasarkan jadwal kelas dan agama oleh `CheckSiswaMapelAccess`;
- controller menerapkan ownership/cabang/kelas guards untuk Waka, Guru, Wali Kelas, Orang Tua, file preview, rapor, nilai, forum, dan pembayaran.

Array `roles.permissions` di-seed tetapi tidak ditemukan sebagai mekanisme enforcement granular. `EnsureRoleLevel`, `EnsureSuperAdmin`, dan helper role level tersedia tetapi route saat ini mengandalkan `role:*`. Sumber: `database/seeders/RoleSeeder.php`, `app/Http/Middleware/`, hasil pencarian penggunaan permission.

## Important Dependencies

| Dependency | Status penggunaan | Bukti |
|---|---|---|
| `barryvdh/laravel-dompdf` | Digunakan | cetak rapor/bukti/laporan pada controller dan view export |
| `maatwebsite/excel` | Digunakan | `app/Imports/`, `app/Exports/`, controller import/export |
| `midtrans/midtrans-php` | Digunakan | `MidtransService`, webhook, portal Orang Tua |
| `spatie/pdf-to-text` | Digunakan | ekstraksi PDF pada AI grading/chatbot; `PDFTOTEXT_BIN_PATH` |
| `spatie/pdf-to-image` | Referensi package tersedia; pemanggilan langsung belum ditemukan dalam alur utama yang diaudit | `composer.json`; `[PERLU VERIFIKASI]` |
| `doctrine/dbal` | Tidak ditemukan sebagai fitur bisnis langsung | `composer.json`; kemungkinan dukungan perubahan schema |
| Chart.js | Digunakan pada dashboard/laporan | `package.json`, JS dashboard/laporan |
| Playwright | Digunakan sebagai tooling capture UI, bukan fitur runtime | `tools/ui-capture/`, script `capture:ui` |

## Storage

Disk `local` dan `public` digunakan. Upload yang ditemukan mencakup foto profil, lampiran kalender/pengumuman/forum, flyer, materi, tugas, jawaban tugas, gambar soal, bukti pembayaran, bukti izin, landing page, PDF rapor, dan attachment chatbot temporer. File preview memakai URL bertanda tangan yang terikat pengguna dan whitelist path/jenis file. Sumber: `config/filesystems.php`, `app/Http/Controllers/FileController.php`, pemanggilan `Storage`/`store()` pada controller.

Deployment membutuhkan `php artisan storage:link` agar disk public dapat diakses; repository hanya menyediakan mapping link, bukan bukti link production telah dibuat. `[PERLU KONFIRMASI]`.

## Scheduler

Scheduler terimplementasi:

- `notifications:schedule` setiap hari pukul 06:00 Asia/Jakarta untuk reminder deadline dan kalender;
- `promotion:execute-scheduled` setiap menit tanpa overlap untuk eksekusi kenaikan kelas terjadwal.

Sumber: `routes/console.php`, `app/Console/Commands/NotificationScheduler.php`, `app/Console/Commands/ExecuteScheduledPromotion.php`.

Keberadaan cron production yang menjalankan `schedule:run` tidak dapat dipastikan dari repository. `[PERLU KONFIRMASI]`.

## Queue

Queue dikonfigurasi default ke koneksi database dan tabel queue tersedia. Namun tidak ditemukan Job domain atau class yang mengimplementasikan `ShouldQueue`; alur aplikasi utama berjalan sinkron. Event notifikasi mengimplementasikan `ShouldBroadcast`, tetapi pada `.env.example` broadcast default adalah `log`. Sumber: `config/queue.php`, `.env.example`, `database/migrations/0001_01_01_000002_create_jobs_table.php`, `app/Events/NotificationCreated.php`.

Worker queue production dan broadcast driver production tidak dapat dipastikan. `[PERLU KONFIRMASI]`.

## Notification / Email

Notifikasi in-app diimplementasikan melalui model `Notification`, `NotificationService`, event broadcast, endpoint daftar/recent/unread/read/delete, dan scheduler. Pemicu mencakup materi, tugas, ujian, forum, nilai, izin, catatan monitoring, tagihan/pembayaran, rapor, carryover, dan kenaikan kelas. Sumber: `app/Services/NotificationService.php`, `app/Http/Controllers/NotificationController.php`, `routes/web.php`.

Email digunakan untuk recovery dan perubahan keamanan melalui `EmailRecoveryService`. `.env.example` memakai mailer `log`; SMTP production tidak dapat dipastikan. Notifikasi email selesai kenaikan kelas masih TODO dan hanya menulis log. Sumber: `app/Services/EmailRecoveryService.php`, `config/mail.php`, `app/Console/Commands/ExecuteScheduledPromotion.php`.

`WhatsAppService` adalah mock: hanya menulis log dan selalu mengembalikan false agar tiket jatuh ke penanganan Admin. Tidak ada integrasi WhatsApp aktif. Sumber: `app/Services/WhatsAppService.php`.

## External Integration

Integrasi aktif/terhubung kode:

- Midtrans Snap dan Transaction API;
- Groq Chat Completions API;
- Google Gemini generateContent API;
- Cloudflare Turnstile siteverify;
- SMTP/Laravel Mail (bergantung konfigurasi);
- filesystem lokal/public;
- broadcast Laravel (driver aktual bergantung environment).

Referensi nonaktif/parsial: WhatsApp mock; tabel log Google Sheets sengaja dihapus; tidak ditemukan sinkronisasi Google Sheets aktif. Sumber: `app/Services/`, `app/Http/Requests/Auth/LoginRequest.php`, `database/migrations/2026_07_16_000000_drop_google_sheets_sync_logs_table.php`.

## API

Tidak ditemukan `routes/api.php`; endpoint aplikasi berada di `routes/web.php` dengan session/CSRF kecuali webhook Midtrans. Endpoint JSON internal melayani chatbot, model/quick action AI, notifikasi, preview/filter data, monitoring ujian, serta lookup akademik. Webhook Midtrans berada di luar auth dan dikecualikan dari CSRF, lalu diverifikasi signature server-side. Sumber: `bootstrap/app.php`, `routes/web.php`, `MidtransWebhookController.php`.

## Payment

Status audit: **PAYMENT IMPLEMENTED**.

Implementasi mencakup tagihan, pembayaran tunai/transfer manual, upload bukti, validasi Admin/Bendahara, cicilan, bulk payment, carryover tunggakan, laporan, konfigurasi rekening/Midtrans, Snap token, webhook bertanda tangan, query status resmi pada finish redirect, pemetaan status, audit log, pembaruan status tagihan, pencegahan duplicate payment, invoice, dan notifikasi. Rincian traceability ada di `08-integration-payment-audit.md`.

Implementasi kode tidak membuktikan kredensial production aktif atau transaksi nyata pernah settlement. `[PERLU KONFIRMASI]`.

## Deployment-related Configuration

Ditemukan:

- arsitektur yang didokumentasikan Cloudflare → Nginx → PHP-FPM → Laravel;
- trusted proxy loopback, force HTTPS pada environment production, security headers, Turnstile;
- panduan hardening Cloudflare/Nginx/firewall/fail2ban/SSL;
- script backup MySQL dengan gzip, retensi 14 hari, lokasi `/www/wwwroot/app.sipaduhok.id`, dan backup `/var/backups/sipaduhok/db`;
- build Vite dan konfigurasi filesystem/migration/cache/session/queue.

Sumber: `bootstrap/app.php`, `app/Providers/AppServiceProvider.php`, `docs/HARDENING_CLOUDFLARE.md`, `scripts/backup-db.sh`, `package.json`.

Tidak ditemukan Dockerfile, Docker Compose, CI/CD `.github/workflows`, Nginx vhost aktual, Supervisor unit, systemd unit, atau deployment script end-to-end. Domain `app.sipaduhok.id` muncul dalam dokumentasi/script, tetapi status DNS, SSL, server, branch/commit production, cron, queue worker, backup terakhir, dan restore test harus dikonfirmasi manusia.

## Testing Infrastructure

`phpunit.xml` memaksa SQLite `:memory:`, cache/session array, queue sync, dan mail array saat test. Test mencakup otorisasi/IDOR, upload MIME, nilai desimal, import scope, payment ownership/finish/webhook notification, promotion, alumni, keamanan kunci ujian, file preview, forum, rapor, presensi, validasi akses, arsip LMS, dan business guard penghapusan. Postman menyediakan enam request web smoke/security. Sumber: `phpunit.xml`, `tests/`, `postman/`.

Hasil eksekusi test dicatat setelah self-review pada `10-discovery-summary.md`; keberadaan test bukan bukti semua 804 route telah diuji.

## Architectural Observations

1. Aplikasi modular berdasarkan role tetapi sangat terpusat pada satu `routes/web.php` (1.630 baris dan 804 route).
2. Admin memperoleh bypass pada seluruh route `role:*`, sehingga route role lain juga dapat diakses Admin walau menu Admin menautkan versi controller tersendiri.
3. Fitur yang sama kadang diduplikasi pada namespace Admin/Bendahara atau Admin/Waka/Sekretaris; beberapa controller berbagi implementasi/alias route.
4. Otorisasi granular berbasis kepemilikan/cabang terutama dilakukan secara manual di controller/service, bukan Policy.
5. Dual role storage (`users.role` dan `role_id`) adalah compatibility layer dan sumber potensi inkonsistensi.
6. Payment Midtrans, AI, Turnstile, mail, scheduler, broadcast, dan storage adalah integration-sensitive; readiness runtime bergantung konfigurasi di luar repository.
7. Terdapat controller tanpa route aktif: `Siswa/SiaRaporController`, `WakilKepalaSekolah/PengaturanKKMController`, dan `WakilKepalaSekolah/PengaturanNaikKelasController`; fitur siswa mengelola rapor memang dinonaktifkan di route.
8. README produk belum tersedia; dokumentasi internal kaya tetapi beberapa klaim production memerlukan konfirmasi manusia.
