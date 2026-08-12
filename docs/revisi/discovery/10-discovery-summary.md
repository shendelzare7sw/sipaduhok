# SIPADUHOK Discovery Summary

> Stage 0 working summary. Seluruh statistik dihitung ulang dari inventory Markdown pada self-review, bukan dari asumsi.

## A. Repository

| Item | Hasil |
|---|---|
| Repository | `C:\laragon\www\sipaduhok` |
| Branch | `latihan-sidang` (tracking `origin/latihan-sidang`) |
| Commit | `c813efea73a20eb721b39cc3c9a56ea39106a75b` |
| Working tree awal | Sudah memiliki 4 dokumentasi untracked; dipertahankan. Stage 0 hanya menambah `docs/revisi/discovery/`. |
| Framework | Laravel 11, PHP ^8.2 |
| Database | Eloquent + migration; 54 migration berstatus Ran pada DB lokal. Konfigurasi PHPUnit: SQLite `:memory:`. Production disebut MySQL 8 pada dokumen lama, `[PERLU VERIFIKASI]`. |
| Frontend | Blade, Vite 5, Bootstrap/Sneat, Tailwind, JS/CSS per modul |
| Authentication | Session auth username/email+password, akun aktif, Turnstile kondisional, rate limit, recovery, role redirect |
| Route | 804 route terdaftar; 446 GET/HEAD dan 358 mutating/multi-method route |

## B. Statistics

| Artefak | Jumlah |
|---|---:|
| Role | 9 |
| Modul | 15 |
| Feature | 101 |
| Functional Requirement | 101 |
| Business Flow | 14 |
| Kandidat UAT langsung (`YA`) | 90 |
| Kandidat UAT sebagian | 8 |
| Bukan UAT langsung | 3 |
| Integration point | 22 |
| Validation/Authorization/Business Rule | 70 |
| Failure Condition | 25 |
| Gap | 35 |
| Sidebar partial yang diinventarisasi | 12 |
| Referensi target menu statis | 127 |
| Named route unik pada sidebar | 118 |

## C. Role Summary

| Kode | Role | Modul Utama |
|---|---|---|
| ADM | Admin | Semua; khusus USR, konfigurasi dan administrasi |
| KET | Ketua PKBM | MON, RAP, PRM |
| WKA | Wakil Kepala Sekolah | ORG, AKD, MON, PRM |
| SEK | Sekretaris | KON |
| BEN | Bendahara | KEU, PRM |
| WKL | Wali Kelas | PRS, NIL, RAP, PRM |
| GRU | Guru Pengajar | LMS, NIL, MON |
| SIS | Siswa | SWA, LMS, PRS, NIL |
| ORT | Orang Tua/Wali Siswa | WLS, KEU, PRS, RAP |

### C.1 Menu Surface Summary

| Kode | Konteks Sidebar | Referensi Target Statis | Catatan |
|---|---|---:|---|
| ADM | Sneat | 37 | Hak akses Admin lebih luas karena bypass; sidebar tetap khusus Admin |
| KET | Sneat | 11 | Badge dispensasi keuangan bersifat dinamis |
| WKA | Sneat | 16 | Pengaturan istirahat tidak memiliki item tersendiri |
| SEK | Sneat | 5 | Dashboard dan empat submenu publikasi |
| BEN | Sneat | 10 | Keuangan, validasi, dan laporan |
| WKL | Sneat | 14 | 13 target unik; route pilih kelas ditulis dua kali secara kondisional |
| GRU | Sneat, LMS Guru, notifikasi LMS Guru | 20 | Tiga sidebar kontekstual, bukan 20 item yang tampil bersamaan |
| SIS | Sneat/SIA dan LMS Siswa | 10 | HOK-LMS dan mata pelajaran dirender kondisional/dinamis |
| ORT | Sneat | 4 | Tiga target anak dirender ulang untuk setiap anak tertaut |
| **Total** | **12 partial** | **127** | **118 named route unik; seluruhnya terdaftar** |

Struktur heading, toggle, submenu, route, badge, kondisi render, dan perbedaan layout lengkap terdapat pada `11-role-menu-inventory.md`.

## D. Module Summary

| Kode | Modul | Jumlah Fitur | Role |
|---|---|---:|---|
| AU | Autentikasi & Akun | 6 | Guest/semua role |
| USR | Manajemen Pengguna | 7 | ADM |
| ORG | Organisasi & Data Induk | 7 | ADM, WKA |
| AKD | Perencanaan Akademik | 10 | ADM, WKA, WKL, GRU, SIS |
| KON | Konten & Informasi | 6 | ADM, SEK, SIS, publik |
| KEU | Keuangan | 12 | ADM, BEN, ORT, WKL, KET, SIS |
| PRS | Presensi & Izin | 5 | WKL, ORT, SIS |
| NIL | Penilaian | 5 | GRU, WKL, SIS, ORT |
| RAP | Rapor | 7 | WKL, KET, ORT |
| LMS | Pembelajaran Daring | 15 | GRU, SIS, ADM/pimpinan monitor |
| PRM | Kenaikan Kelas | 7 | ADM, WKA, BEN, WKL, KET |
| MON | Monitoring & Catatan | 4 | ADM, KET, WKA, GRU |
| NOT | Notifikasi | 3 | Semua role/sistem |
| SWA | Portal Siswa | 3 | SIS |
| WLS | Portal Orang Tua | 4 | ORT |

## E. Requirement Coverage

| Modul | Feature | Requirement | UAT Candidate YA | Sebagian | Tidak Langsung |
|---|---:|---:|---:|---:|---:|
| AU | 6 | 6 | 4 | 2 | 0 |
| USR | 7 | 7 | 6 | 1 | 0 |
| ORG | 7 | 7 | 7 | 0 | 0 |
| AKD | 10 | 10 | 9 | 1 | 0 |
| KON | 6 | 6 | 6 | 0 | 0 |
| KEU | 12 | 12 | 10 | 1 | 1 |
| PRS | 5 | 5 | 5 | 0 | 0 |
| NIL | 5 | 5 | 4 | 0 | 1 |
| RAP | 7 | 7 | 7 | 0 | 0 |
| LMS | 15 | 15 | 13 | 2 | 0 |
| PRM | 7 | 7 | 6 | 1 | 0 |
| MON | 4 | 4 | 4 | 0 | 0 |
| NOT | 3 | 3 | 2 | 0 | 1 |
| SWA | 3 | 3 | 3 | 0 | 0 |
| WLS | 4 | 4 | 4 | 0 | 0 |
| **Total** | **101** | **101** | **90** | **8** | **3** |

## F. Business Flow Summary

| Flow ID | Nama Flow | Role | Modul |
|---|---|---|---|
| FLOW-001 | Login dan pengalihan dashboard | Semua | AU |
| FLOW-002 | Pembuatan siswa dan penautan orang tua | ADM, WKA | USR, ORG, SWA, WLS |
| FLOW-003 | Jadwal sampai ruang LMS | ADM, WKA, GRU, SIS | ORG, AKD, LMS, SWA |
| FLOW-004 | Materi/tugas sampai nilai | GRU, SIS | LMS, NIL, NOT |
| FLOW-005 | Ujian/latihan sampai koreksi | GRU, SIS | LMS, NIL, KEU, NOT |
| FLOW-006 | Presensi dan izin | WKL, ORT, SIS | PRS, NOT, WLS, SWA |
| FLOW-007 | Tagihan sampai transfer manual | ADM, BEN, ORT | KEU, NOT, WLS |
| FLOW-008 | Payment digital Midtrans | ORT, sistem, ADM, BEN | KEU, NOT, WLS |
| FLOW-009 | Carryover tunggakan | ADM, BEN, ORT | KEU, NOT |
| FLOW-010 | Nilai sampai validasi rapor | GRU, WKL, KET, ORT | NIL, RAP, NOT, WLS |
| FLOW-011 | Request/download rapor | ORT, WKL | RAP, WLS, NOT |
| FLOW-012 | Kenaikan kelas multi-role | ADM, WKA, WKL, BEN, KET, sistem | PRM, NIL, KEU, NOT, ORG |
| FLOW-013 | Dispensasi akses/rapor | BEN, ADM, KET, WKL, SIS | KEU, RAP, NOT |
| FLOW-014 | Monitoring LMS dan teguran | ADM, KET, WKA, GRU | MON, LMS, NOT |

## G. Integration Summary

| Integration | Status | Modul |
|---|---|---|
| UI Blade/JS ↔ backend web | IMPLEMENTED | Semua |
| Auth ↔ session DB; role ↔ User/Role | IMPLEMENTED | AU, USR |
| Controller/service ↔ Eloquent DB | IMPLEMENTED | Semua |
| File storage/preview/download | IMPLEMENTED | AU, KON, LMS, PRS, KEU, RAP |
| Laravel Excel dan DOMPDF | IMPLEMENTED | Banyak modul bisnis |
| Midtrans Snap/Transaction/webhook | IMPLEMENTED | KEU, WLS, NOT |
| Groq dan Google Gemini | IMPLEMENTED | LMS, NIL |
| PDF text extraction | IMPLEMENTED | LMS/NIL |
| Cloudflare Turnstile | IMPLEMENTED | AU |
| Laravel Mail | IMPLEMENTED secara kode; runtime tergantung mailer | AU |
| In-app notification ↔ modul bisnis | IMPLEMENTED | NOT dan pemicu bisnis |
| Broadcast realtime | PARTIAL | NOT |
| Scheduler | PARTIAL (command ada; cron host belum diverifikasi) | NOT, PRM |
| Cloudflare/Nginx deployment dan backup | PARTIAL (artefak/panduan ada; runtime belum diverifikasi) | Infrastruktur |
| Database queue | CONFIGURED BUT UNUSED | Infrastruktur |
| WhatsApp dan Google Sheets | REFERENCE ONLY | Recovery/historis |

Status matrix lengkap: 15 IMPLEMENTED, 4 PARTIAL, 1 CONFIGURED BUT UNUSED, 2 REFERENCE ONLY.

## H. Payment Status

**PAYMENT IMPLEMENTED**

Midtrans bukan hanya dependency. `MidtransService` membuat Snap token, membaca Transaction status, memverifikasi signature dan memetakan status; `MidtransWebhookController` memproses callback dan audit; `OrangTuaController` menyediakan single/bulk payment, Snap, finish terverifikasi dan continuation; payment/tagihan/config/audit mempunyai model dan migration. Sumber: `composer.json`, `app/Services/MidtransService.php`, `app/Http/Controllers/MidtransWebhookController.php`, `app/Http/Controllers/OrangTua/OrangTuaController.php`, payment migrations/models/views/tests.

`[PERLU KONFIRMASI]` credential production/sandbox, notification URL dan bukti transaksi settlement nyata.

## I. Deployment Findings

- Konfigurasi/panduan mengarah ke Cloudflare → Nginx → PHP-FPM → Laravel pada domain `app.sipaduhok.id`.
- Laravel mempercayai proxy loopback dan memaksa URL HTTPS pada environment production.
- Turnstile, security headers, database session/cache/queue, filesystem local/public dan Vite build telah dikonfigurasi.
- Scheduler membutuhkan cron host; status cron tidak tersedia.
- Script backup MySQL tersedia dengan gzip, retensi 14 hari dan opsi off-site; instalasi, backup terakhir dan restore test tidak tersedia.
- Tidak ditemukan Docker, CI/CD, Nginx vhost aktual, Supervisor/systemd unit, atau deployment script end-to-end.
- Kondisi live DNS, SSL, firewall, worker, SMTP, broadcast, storage link, permission dan branch/commit production `[PERLU KONFIRMASI]`.

## J. Testing Findings

Self-review menjalankan `php artisan test` dengan SQLite `:memory:` sesuai `phpunit.xml`.

- **79 test passed**
- **1 test failed**
- **419 assertions**
- Durasi run: sekitar 10 detik

Failure: `Tests\Feature\SiswaImportStatusTest::test_status_nonaktif_tidak_gagal_dan_akun_dinonaktifkan`. Test mengharapkan dua row berhasil, tetapi importer melewati keduanya karena `nama_kelas` dan `agama` kosong. Sumber: `app/Imports/SiswaImport.php:80-109`, `tests/Feature/SiswaImportStatusTest.php:38-77`. Belum ditentukan apakah test fixture atau rule importer yang salah; source/test tidak diubah pada Stage 0.

## K. Major Gaps

1. README produk/setup tidak tersedia dan requirement formal belum meliputi implementasi besar.
2. Dual role storage dan naming role/UI/route tidak konsisten; permissions granular tidak enforced.
3. Satu regression test import siswa gagal.
4. Kemungkinan mismatch durasi rate limit login dengan klaim 5 menit.
5. Beberapa request AI menonaktifkan TLS verification.
6. Toggle Midtrans enabled belum tampak ditegakkan konsisten server-side; runtime/transaction production belum terbukti.
7. WhatsApp adalah mock; email promotion TODO; broadcast/scheduler/deployment/backup hanya partial secara evidence repository.
8. Controller rapor siswa dan beberapa controller Waka tidak mempunyai route aktif.
9. Test suite belum memberi coverage formal terhadap 101 feature/804 route.
10. Artefak metode Prototyping/iterasi stakeholder tidak dapat dibuktikan dari source repository.

Detail: `09-gap-analysis.md` (35 gap).

## L. Information Requiring Human Confirmation

1. Istilah resmi aktor `orang_tua`: Orang Tua, Wali Siswa, atau gabungan.
2. Apakah seluruh 9 role dan bypass Admin sesuai kebijakan operasional.
3. Baseline requirement yang disepakati stakeholder serta fitur mana yang in-scope laporan akhir; termasuk status portal pembayaran Siswa yang read-only dan tidak ditampilkan pada sidebar.
4. Artefak metode Prototyping: iterasi prototype, feedback pengguna, perubahan per iterasi, dan approval.
5. Apakah judul tidak boleh diubah berdasarkan berita acara/ketentuan kampus dan arahan penguji/pembimbing. `[DIISI MANUAL]`
6. Midtrans: owner merchant, sandbox/production, key aktif, notification URL, transaksi/evidence, settlement, refund/reconciliation SOP.
7. Server live: URL/domain, provider/VPS, OS, branch/commit, DB, SSL, Cloudflare/firewall, cron, queue, SMTP, broadcast, storage link/permission.
8. Backup: jadwal, hasil terakhir, off-site copy dan restore test.
9. Penjelasan expected behavior import siswa terkait kewajiban `nama_kelas` dan `agama`, agar test atau requirement dapat diselaraskan nanti.
10. Nomor lampiran, nama/jabatan penandatangan, tanggal, tester, evidence, actual result, PASS/FAIL, dan biaya aktual. `[DIISI MANUAL]`

## M. Readiness for Next Stage

**READY FOR STAGE 1**

Alasan: repository, role, modul, 101 feature, 101 requirement as-built, acceptance criteria, flow, rule, integration dan gap telah memiliki traceability yang cukup untuk proses validasi requirement. Stage 1 harus bersifat **validasi bersama manusia**, bukan langsung menganggap seluruh implementation sebagai requirement yang disetujui.

Catatan wajib sebelum BAST Requirement ditandatangani:

- stakeholder menentukan fitur in-scope/out-of-scope;
- istilah role dan expected import siswa dikonfirmasi;
- test gagal tidak boleh ditulis PASS;
- credential/runtime/payment/deployment yang tidak tersedia tidak boleh diklaim verified;
- nama penandatangan, tanggal dan nomor lampiran tetap `[DIISI MANUAL]`/`Lampiran XX`.

Stage 1 belum dikerjakan dalam dokumen ini.
