# SIPADUHOK — Non-Functional Requirements

> NFR hanya dicatat jika ada evidence laporan atau implementasi. Tidak ada target numerik response time, uptime, RTO/RPO, concurrency, atau kapasitas; dokumen ini tidak menambahkannya.

| NFR ID | Category | Requirement | Report Evidence | Implementation Evidence | Status |
|---|---|---|---|---|---|
| NFR-001 | Authorization | Sistem harus membatasi fungsi dan data berdasarkan role pengguna. | Login multi-peran dan akses restricted, hlm. 12, 15, 19–20. | Middleware `role:*`, `CheckRole`, controller guards, assignment scopes. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-002 | Privacy / Data Isolation | Pengguna hanya boleh melihat data pribadi, kelas, mata pelajaran, atau anak yang berada dalam scope-nya. | Profil Siswa read-only dan pembatasan kelas/mapel, hlm. 13–16, 20. | `children()`, assignment Guru/Wali, Waka `cabang_id`, IDOR tests. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-003 | Authentication | Hanya akun aktif dengan kredensial sah yang dapat membentuk session dan diarahkan ke dashboard role. | Alur login multi-peran, hlm. 20; diagram hlm. 52/94. | `LoginRequest`, `EnsureUserIsActive`, session regeneration. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-004 | Security | Request perubahan state harus dilindungi dari request palsu dan input invalid. | Kebutuhan payment aman, hlm. 10; akses restricted pada materi/ujian. | CSRF web middleware, Form Request/controller validation, authorization guards. | IMPLEMENTED |
| NFR-005 | File Restriction | Upload harus dibatasi pada tipe/ukuran/ownership yang diizinkan dan disimpan konsisten. | Format materi/jawaban/bukti disebut hlm. 12–16, 19; Laravel Storage hlm. 22. | Validation `mimes/mimetypes/max`, disks local/public, owner/signed download checks. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-006 | Secure Download | File privat/sensitif harus diakses melalui otorisasi atau token yang valid. | Materi/ujian restricted dan workflow request rapor, hlm. 12, 15, 82, 88. | Signed/owner routes, `RequestDownloadRapor` token/expiry, controller scope. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-007 | Auditability | Perubahan keuangan harus dapat ditelusuri ke pelaku, nilai lama/baru, dan konteks request. | Payment aman/terdokumentasi, hlm. 10; `financial_audit_logs`, hlm. 167. | `FinancialAuditLog`, validator/time, IP/user-agent, webhook audit. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-008 | Integration Integrity | Status payment gateway tidak boleh dipercaya dari browser dan callback harus diverifikasi serta idempotent. | Midtrans dan status otomatis, hlm. 16, 22; detail signature tidak dinyatakan. | Verifikasi signature SHA-512, Transaction API pada finish, mapping/idempotency. | IMPLEMENTED |
| NFR-009 | Secret Protection | Credential eksternal yang sensitif harus disimpan secara terlindungi dan tidak ditampilkan utuh. | Midtrans dicatat sebagai teknologi, hlm. 22/175; aturan secret tidak dirinci. | Server key terenkripsi di DB, UI masking; keamanan bergantung `APP_KEY` stabil. | IMPLEMENTED |
| NFR-010 | Compatibility | Aplikasi harus berbasis web dan dapat diakses melalui browser pada perangkat pengguna. | Platform web/browser, hlm. 17; Chrome dan hosting, hlm. 175. | Blade, Bootstrap/Sneat/Tailwind, responsive layouts, Vite. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-011 | Usability | Antarmuka harus dibedakan per role dan mendukung kemudahan penggunaan calon pengguna. | Black-box/umpan balik kemudahan penggunaan, hlm. 17; manual book per role, hlm. 22. | 12 partial sidebar, layout Sneat/LMS/LMS-Guru, flash/error/empty states. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-012 | Data Integrity | Operasi lintas entitas penting harus menjaga konsistensi dan menolak relasi/state invalid. | Tujuan data administratif akurat dan terintegrasi, hlm. 9–10. | DB constraints, Eloquent transactions, state guards, idempotent sync/carryover. | DOCUMENTED_AND_IMPLEMENTED |
| NFR-013 | Logging | Error integrasi dan proses bisnis kritis harus memiliki log yang mendukung diagnosis tanpa membocorkan data berlebihan. | Tidak ada aturan logging eksplisit. | Laravel logging digunakan; payload webhook penuh berpotensi tercatat (GAP-030). | PARTIAL |
| NFR-014 | Rate Limiting | Login harus dibatasi setelah kegagalan berulang. | Tidak ada durasi/ambang dalam laporan. | RateLimiter diterapkan; komentar/klaim 5 menit mungkin tidak sama dengan decay aktual (GAP-007). | PARTIAL |
| NFR-015 | Availability | Sistem production harus dapat diakses melalui domain/hosting dan konfigurasi HTTPS/proxy yang benar. | Aplikasi siap digunakan, hosting/domain dianggarkan, hlm. 22/27; `app.sipaduhok.id`, hlm. 175. | Force HTTPS production dan trusted proxy; DNS/SSL/server live tidak dapat diverifikasi. | NEED_CONFIRMATION |
| NFR-016 | Backup / Recoverability | Basis data harus memiliki mekanisme backup dan pemulihan yang dapat diverifikasi. | Latar belakang menyebut risiko kehilangan data, hlm. 9; target backup tidak ditentukan. | Script backup MySQL/retensi tersedia; instalasi, backup terakhir, off-site, restore test tidak terbukti. | PARTIAL |
| NFR-017 | Maintainability | Sistem harus mempunyai dokumentasi arsitektur, alur data, teknologi, dan penggunaan agar dapat dikembangkan kembali. | Output Manual Book dan Dokumentasi Teknis, hlm. 22; tujuan reproduksi, hlm. 175. | Dokumen teknis ada; README setup masih skeleton dan deployment tidak end-to-end. | PARTIAL |
| NFR-018 | Scheduler Reliability | Reminder dan kenaikan terjadwal harus dijalankan tanpa duplikasi/overlap ketika scheduler aktif. | Reminder deadline disebut hlm. 10; promotion schedule ada pada technical hlm. 153. | Laravel scheduler, duplicate guard/lock; cron production tidak dapat diverifikasi. | PARTIAL |
| NFR-019 | External API Transport | Koneksi ke provider AI/payment harus memverifikasi TLS dan menangani kegagalan tanpa merusak data. | Tidak dirinci pada laporan. | Payment exception handling tersedia; sebagian request AI memakai `verify=false` (GAP-008). | PARTIAL |
| NFR-020 | Production Configuration | Fitur eksternal hanya boleh dianggap aktif jika credential, mode, callback, mailer, cron, dan storage production telah diverifikasi. | Teknologi/hosting disebut, tetapi runtime evidence tidak disertakan. | Config tersedia; state production eksternal tidak ada di repository. | NEED_CONFIRMATION |

## NFR Boundary

- Tidak ada evidence untuk menjanjikan response time tertentu, uptime persentase tertentu, jumlah concurrent user, RTO/RPO, atau ukuran storage maksimum global.
- “Implemented” berarti kontrol ditemukan pada source, bukan bukti bahwa environment production telah dikonfigurasi atau diuji operasional.
- Temuan security yang berpotensi bug tetap dicatat sebagai `PARTIAL`; Stage 1A tidak memperbaiki source.

**NFR count: 20.**
