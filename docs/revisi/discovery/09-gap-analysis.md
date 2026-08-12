# Gap Analysis

Gap tidak otomatis berarti aplikasi gagal. `POSSIBLE BUG` adalah indikasi dari source yang perlu reproduksi/konfirmasi; Stage 0 tidak memperbaiki source.

| Gap ID | Type | Description | Impact | Source | Confidence |
|---|---|---|---|---|---|
| GAP-001 | MISSING DOCUMENTATION | `README.md` masih README skeleton Laravel dan tidak menjelaskan SIPADUHOK, setup, architecture, role atau deployment. | Onboarding dan reproduksi environment bergantung dokumen tersebar. | `README.md` | HIGH |
| GAP-002 | INCONSISTENT NAMING | Role `orang_tua` ditampilkan sebagai “Orang Tua/Wali” dan “Wali Siswa”; route memakai `wali-siswa`. | Istilah requirement/UAT dapat ambigu. | RoleSeeder, User label, routes/views | HIGH |
| GAP-003 | INCONSISTENT NAMING | Role Waka bernilai `wakil_kepala_sekolah` tetapi URI/name/view memakai `waka`; Wali Kelas memakai `/wali`, berbeda dari `/wali-siswa`. | Risiko salah mapping actor saat dokumentasi/test. | `routes/web.php`; role seeder | HIGH |
| GAP-004 | PARTIAL IMPLEMENTATION | Dual role storage `users.role_id` dan legacy `users.role` dipertahankan dengan fallback; tidak ada mekanisme sinkronisasi yang terlihat pada satu tempat terpusat. | Data tidak sinkron dapat menghasilkan label/otorisasi berbeda. | `User.php`; migration users/roles | HIGH |
| GAP-005 | POSSIBLE BUG | `Role::isSuperAdmin()` mencari role `super_admin`, sedangkan seeder hanya membuat `admin`; `User::isSuperAdmin()` justru alias Admin. | Helper yang salah dipakai kelak dapat memberi hasil berbeda. | `Role.php`; `User.php`; `RoleSeeder.php` | HIGH |
| GAP-006 | POSSIBLE UNUSED IMPLEMENTATION | JSON `roles.permissions`, helper role level/capability dan middleware level/superadmin tersedia, tetapi enforcement route menggunakan `role:*` dan guard manual. | Dokumen seeder dapat memberi kesan ACL granular aktif padahal tidak ditemukan penggunaannya. | RoleSeeder; Role/User models; middleware; routes | HIGH |
| GAP-007 | POSSIBLE BUG | Dokumentasi/kode komentar menyebut lockout login 5 menit, tetapi `RateLimiter::hit($key)` dipanggil tanpa decay 300 detik; default framework umumnya 60 detik. | Durasi proteksi aktual berpotensi tidak sesuai klaim. | `LoginRequest::authenticate/ensureIsNotRateLimited` | HIGH |
| GAP-008 | POSSIBLE BUG | Beberapa pemanggilan API AI memakai `Http::withOptions(['verify' => false])` tanpa kondisi environment. | TLS certificate verification ke provider eksternal dapat dinonaktifkan juga di production. | `AiChatbotService.php`; pemanggilan AI services | HIGH |
| GAP-009 | POSSIBLE BUG | Flow membuat Midtrans memeriksa `isConfigured()` tetapi tidak selalu `isMidtransEnabled()` server-side; toggle enable tampak terutama mengendalikan tampilan. | POST yang dibentuk manual mungkin mencoba gateway yang dinonaktifkan tetapi key masih lengkap. | `InfoPembayaran::isMidtransEnabled`; `OrangTuaController@prosesBayar/processBulkPay` | MEDIUM |
| GAP-010 | MISSING DOCUMENTATION | Kode payment lengkap, tetapi tidak ada artefak Stage 3 yang memetakan request/response, credential ownership, callback registration, reconciliation/refund, dan bukti transaksi. | Sulit membuktikan kesiapan payment secara formal saat sidang/audit. | payment source; belum ada dokumen revisi Stage 3 | HIGH |
| GAP-011 | PERLU KONFIRMASI | Credential/mode Midtrans, notification URL dan transaksi settlement nyata tidak dapat dipastikan dari repository. | PAYMENT IMPLEMENTED secara kode belum sama dengan payment production verified. | InfoPembayaran DB/runtime eksternal | HIGH |
| GAP-012 | PARTIAL IMPLEMENTATION | `WhatsAppService` hanya mock log dan selalu false. | Recovery otomatis via WhatsApp tidak tersedia; perlu Admin/manual fallback. | `app/Services/WhatsAppService.php` | HIGH |
| GAP-013 | PARTIAL IMPLEMENTATION | Notifikasi email selesai promotion masih TODO dan hanya log. | Opsi `notification_email` tidak menghasilkan email nyata sesuai komentar. | `ExecuteScheduledPromotion.php:105-130` | HIGH |
| GAP-014 | PARTIAL IMPLEMENTATION | Event notifikasi implement `ShouldBroadcast`, tetapi `.env.example` memakai driver `log`; client realtime/provider production tidak terbukti. | Notifikasi tetap tersedia via polling/UI, tetapi realtime push belum terverifikasi. | `NotificationCreated.php`; `.env.example` | MEDIUM |
| GAP-015 | POSSIBLE UNUSED IMPLEMENTATION | Queue database dan tabel jobs tersedia tetapi tidak ada domain Job/`ShouldQueue` ditemukan. | Proses external/AI/email tetap sinkron dan dapat memperpanjang response; worker tidak memberi manfaat saat ini. | `config/queue.php`; jobs migration; `app/` | HIGH |
| GAP-016 | PERLU KONFIRMASI | Cron `schedule:run`, queue worker, SMTP, broadcast driver, `storage:link` dan permission production tidak dapat dilihat dari repo. | Scheduler/email/file/realtime dapat berbeda dari lokal. | configs, console routes, filesystem | HIGH |
| GAP-017 | MISSING DOCUMENTATION | Tidak ditemukan Docker/CI-CD/deployment script end-to-end/Nginx vhost/Supervisor unit; hanya panduan hardening dan backup script. | Deployment tidak sepenuhnya reproducible dari repository. | repo file inventory; hardening doc; backup script | HIGH |
| GAP-018 | PERLU KONFIRMASI | Panduan mengklaim Cloudflare→Nginx→PHP-FPM dan domain `app.sipaduhok.id`, tetapi state DNS/SSL/firewall/server saat ini tidak diverifikasi. | Temuan deployment adalah dokumentasi, bukan evidence live. | `bootstrap/app.php`; `docs/HARDENING_CLOUDFLARE.md`; script backup | HIGH |
| GAP-019 | PERLU KONFIRMASI | Script backup tersedia, tetapi instalasi cron, backup terbaru, salinan off-site dan restore test tidak dapat dipastikan. | Backup code-ready belum membuktikan recoverability. | `scripts/backup-db.sh` | HIGH |
| GAP-020 | INCONSISTENT NAMING | Dokumen tabel relasi mengklaim 58 tabel domain production, sementara migration repository membuat schema yang berbeda cakupan dan semua 54 migration lokal berstatus ran. | ERD/dokumen database dapat tidak cocok dengan fresh migration atau production saat ini. | `docs/TABEL_RELASI.md`; `database/migrations`; migrate status | MEDIUM |
| GAP-021 | POSSIBLE UNUSED IMPLEMENTATION | `Siswa/SiaRaporController`, Waka `PengaturanKKMController`, Waka `PengaturanNaikKelasController` tidak mempunyai route aktif. | Source dapat disalahcatat sebagai fitur user aktif. | controller-route comparison | HIGH |
| GAP-022 | UNDOCUMENTED IMPLEMENTATION | Payment Midtrans, AI (Groq/Gemini), Turnstile, arsip/salin LMS, promotion multi-role, monitoring LMS dan recovery ticket jauh melampaui README. | Requirement lama berpotensi tidak mencakup fitur nyata. | source modules vs `README.md` | HIGH |
| GAP-023 | PARTIAL IMPLEMENTATION | Route rapor siswa sengaja dinonaktifkan; rapor hanya untuk Orang Tua, namun controller dan views SIA rapor siswa masih ada. | Nama file dapat menyesatkan audit; dead code perlu diperlakukan bukan fitur aktif. | `routes/web.php:1450-1456`; `SiaRaporController.php` | HIGH |
| GAP-024 | INCONSISTENT NAMING | “Tugas & Latihan” dan “Latihan (renamed from Kuis)” memakai model/controller ujian yang sama untuk tipe latihan, sementara tugas juga kadang disebut latihan non-ujian. | Test/requirement dapat salah mengacu jenis aktivitas. | route comments; GuruUjian/GuruTugas controllers/views | MEDIUM |
| GAP-025 | POSSIBLE UNUSED IMPLEMENTATION | `spatie/pdf-to-image` terpasang, tetapi pemanggilan langsung pada alur utama tidak ditemukan; `doctrine/dbal` juga bukan fitur bisnis. | Dependency tidak boleh diklaim sebagai fitur aktif. | `composer.json`; source search | MEDIUM |
| GAP-026 | PARTIAL IMPLEMENTATION | Sinkronisasi Google Sheets tidak aktif; migration bahkan menghapus tabel log, meski referensi historis mungkin ada di dokumentasi lama. | Dokumen lama dapat salah menyebut integrasi aktif. | migration `2026_07_16_000000*`; source search | HIGH |
| GAP-027 | MISSING DOCUMENTATION | Tidak ada inventory formal environment variable untuk AI/payment (payment key disimpan DB), mail, scheduler dan binary PDF per environment. | Setup baru mudah tidak lengkap atau salah memahami lokasi secret. | `.env.example`; AppSetting/InfoPembayaran configs | HIGH |
| GAP-028 | MISSING DOCUMENTATION | 804 route dan 200 titik validasi belum memiliki traceability formal sebelum dokumen Stage 0 ini; route besar terpusat pada satu file. | Risiko requirement/test coverage terlewat dan maintenance sulit. | `routes/web.php` (1.630 baris); validation scan | HIGH |
| GAP-029 | MISSING DOCUMENTATION | Test suite memiliki 74 test method tetapi tidak memetakan coverage ke 101 feature/requirement dan bukan bukti seluruh route diuji. | Coverage formal SIT/UAT belum dapat disimpulkan. | `tests/`; Stage 0/Stage 1A traceability | HIGH |
| GAP-030 | POSSIBLE BUG | Webhook menulis seluruh payload notification ke log (`Log::info(..., $notification)`). | Payload gateway dapat tersimpan lebih luas/lama dari kebutuhan; perlu review redaction/retention. | `MidtransWebhookController@notification` | MEDIUM |
| GAP-031 | POSSIBLE BUG | `MidtransService` menekan warning SDK dengan operator `@`; error/warning tertentu dapat tidak terlihat selain exception/log buatan. | Diagnosis incident gateway dapat lebih sulit. | `MidtransService::createSnapToken/getTransactionStatus` | MEDIUM |
| GAP-032 | POSSIBLE BUG | `InfoPembayaran::getInstance()` membuat row kosong saat pembacaan pertama. Read operation dapat memutasi database. | Audit/read-only page pertama dapat menghasilkan record konfigurasi tanpa explicit action. | `InfoPembayaran::getInstance` | MEDIUM |
| GAP-033 | REQUIREMENT GAP | Judul/metode pengembangan “Prototyping” tidak dapat divalidasi dari source repository dan README tidak memuat artefak iterasi prototype/feedback. | Klaim metodologi harus dibuktikan oleh dokumen proses/riwayat, bukan keberadaan fitur/payment. | `README.md`; repository artefact inventory | HIGH |
| GAP-034 | PERLU KONFIRMASI | Nomor lampiran, penandatangan BAST, tanggal pengujian/deployment, tester, bukti, hasil aktual, biaya dan nominal tidak berada di repository. | Stage berikutnya memerlukan input manusia; tidak boleh dikarang. | di luar source code | HIGH |
| GAP-035 | POSSIBLE BUG | Test suite Stage 0 gagal pada `SiswaImportStatusTest`: test mengharapkan dua baris diimpor, tetapi importer melewati keduanya karena `nama_kelas` dan `agama` kosong. Belum dapat dipastikan apakah fixture test tertinggal atau rule importer terlalu ketat. | Regression suite tidak hijau dan behavior import status nonaktif belum terverifikasi oleh test tersebut. | `app/Imports/SiswaImport.php:80-109`; `tests/Feature/SiswaImportStatusTest.php:38-77`; hasil `php artisan test` | HIGH |

## Major Gap Interpretation

1. **Implementation vs documentation:** source jauh lebih kaya daripada README/requirement formal yang tersedia; Stage 1 perlu memilih baseline requirement yang disetujui stakeholder.
2. **Runtime evidence:** beberapa integrasi berstatus implemented secara kode tetapi deployment/credential/cron/worker/SMTP/backup tidak dapat dibuktikan hanya dari repository.
3. **Legacy/unused paths:** dual role, controller tanpa route, permissions tidak enforced, WhatsApp mock dan Google Sheets historis harus dicegah masuk ke requirement sebagai fitur aktif.
4. **Security/config candidates:** durasi rate limit, TLS verify AI, Midtrans enable server-side dan logging webhook perlu diverifikasi/reproduksi sebelum disebut defect final atau diperbaiki pada stage terpisah.
5. **Metode penelitian:** keberadaan payment tidak secara otomatis membatalkan metode Prototyping; repository hanya dapat menunjukkan implementasi, bukan proses metodologis. Pembahasan/komparasi formal tetap Stage 2 dan membutuhkan evidence iterasi serta aturan institusi.

## Gap Statistics

Total gap: **35**.

| Type | Count |
|---|---:|
| MISSING DOCUMENTATION | 6 |
| INCONSISTENT NAMING | 4 |
| PARTIAL IMPLEMENTATION | 6 |
| POSSIBLE UNUSED IMPLEMENTATION | 4 |
| UNDOCUMENTED IMPLEMENTATION | 1 |
| POSSIBLE BUG | 8 |
| REQUIREMENT GAP | 1 |
| PERLU KONFIRMASI | 5 |

Catatan: satu gap memiliki satu Type utama pada matrix; statistik akan diperiksa ulang secara otomatis saat self-review.
