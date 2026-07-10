# Security & QA Audit — SipaduHOK

Dokumen hidup: catatan temuan audit keamanan/QA beserta status perbaikannya.
Dikerjakan di branch `finalizing`. Prioritas: fitur pembelajaran (siswa ↔ guru ↔ wali kelas), lalu melebar.

**Severity:** 🔴 High · 🟠 Medium · 🟡 Low
**Status:** ✅ Fixed · 🔧 In progress · ⏳ Open

---

## Ringkasan Temuan

| ID | Severity | Area | Isu | Status |
|----|----------|------|-----|--------|
| F-01 | 🔴 High | File preview | `/storage-preview?path=` menyajikan file publik apa pun tanpa cek pemilik | ✅ Fixed |
| F-02 | 🔴 High | File preview | `/view-document/{id}` id enumerable (crc32 % 100000) tanpa ikatan pemilik | ✅ Fixed |
| F-03 | 🟠 Medium | Rapor siswa | Otorisasi `SiaRaporController` tidak konsisten | ✅ Resolved (by design — rute siswa rapor dinonaktifkan; siswa memang tak berhak) |
| F-04 | 🟠 Medium | Infra | `trustProxies(at:'*')` → IP klien bisa dipalsukan (log & rate-limit) | ✅ Fixed (VPS: percaya loopback saja) |
| F-05 | 🟡 Low | Headers | Tidak ada Content-Security-Policy; HSTS dikomentari | ✅ Fixed (HSTS saat HTTPS + CSP minimal aman) |
| F-06 | 🟡 Low | Auth | `CheckRole` membocorkan nama role di pesan error + auto-logout | ✅ Fixed (Opsi 1: 403 tanpa logout + pesan generik) |
| F-07 | 🟡 Low | Forum | `parent_id` reply hanya `exists:` tanpa scope ke diskusi | ✅ Fixed (siswa & guru) |
| F-08 | 🔴 High | Nilai guru | `GuruNilaiController@update/updateBatch` ubah `nilai_id` tanpa scope kelas+mapel → tampering nilai lintas-kelas | ✅ Fixed |
| F-09 | 🔴 High | Ujian guru | `GuruUjianController` kelola/koreksi soal & ujian via `ujianId/soalId` yang tak dibatasi ke kelas+mapel yang diajar → baca kunci jawaban / ubah / hapus / nilai ujian di kelas/mapel lain | ✅ Fixed |
| F-10 | 🔴 High | Koreksi tugas | `GuruKoreksiController` `show/store/bulkGrade/ai-suggest` memuat `TugasSiswa/Tugas` tanpa scope kelas+mapel → nilai/baca pengumpulan tugas kelas lain | ✅ Fixed |
| F-11 | 🔴 High | Rapor wali | `WaliKelas\RaporController` `update/terbitkan/resetNilai/reorderNilai/tarikKembali/preview/print/autoFillKehadiran/exportExcel` + `approve/rejectDownload` tanpa cek kelas wali → kelola/lihat/cetak rapor & setujui unduh kelas lain | ✅ Fixed |
| F-12 | 🔴 High | Validasi akses wali | `ValidasiAksesController` validasi/batal ujian&rapor + bulk via `siswaId` tanpa cek kelas → wali buka akses ujian/rapor siswa kelas lain (bobol gerbang keuangan) | ✅ Fixed |
| F-13 | 🔴 High | Presensi wali | `PresensiController@updatePresensi` tulis presensi via `siswa_id`+`kelas_id` sembarang (hanya `exists:`) tanpa cek kelas ampuan → tampering absensi siswa kelas lain | ✅ Fixed |
| F-14 | 🔴 High | Presensi wali | `PresensiController@inputHarian` (bulk) sama seperti F-13; `kelas_id`/`siswa_id` tak diverifikasi milik wali | ✅ Fixed |
| F-15 | 🔴 High | Bukti izin wali | `PresensiController@previewBukti` sajikan file bukti izin via `id` tanpa otorisasi → wali lihat dokumen izin/sakit (pribadi) siswa kelas mana pun | ✅ Fixed |
| F-16 | 🟠 Medium | Forum guru | `GuruForumController` `show/reply/destroyReply/togglePin/toggleClose` muat forum/reply via id tanpa scope kelas+mapel → baca/tulis/hapus/pin diskusi kelas/mapel yang tak diajar | ✅ Fixed |
| F-17 | 🟡 Low | Template capaian | `TemplateCapaianController@destroy` tak cek `created_by` → wali mana pun bisa hapus template milik wali lain | ✅ Fixed (Opsi A: hapus dikunci ke pembuat; edit tetap bersama) |
| F-18 | 🟠 Medium | Bayar tagihan ortu | `OrangTuaController@prosesBayar` validasi `tagihan_id` hanya `exists:` tanpa cek tagihan milik anak → wali siswa bisa melampirkan/menyetel pembayaran ke tagihan siswa lain (mismatch integritas) | ✅ Fixed |
| F-19 | 🔴 High | Pembayaran Midtrans | `OrangTuaController@snapFinish` percaya `transaction_status` dari query redirect (tak bertanda-tangan) → tandai pembayaran `disetujui`/tagihan lunas tanpa benar-benar membayar; juga tak cek `isMyChild` | ✅ Fixed |
| F-20 | 🟡 Low | Rute rusak (QA) | Rute pembayaran siswa menunjuk metode controller yang tak ada (`bayar`/`cetak`/`midtrans-*`) → tombol "cetak bukti" & submit bayar **500**; callback midtrans siswa dead | ✅ Fixed |
| F-21 | 🟠 Medium | Rate-limit AI | Endpoint AI guru (`ai-suggest` × koreksi tugas/ujian/latihan, `ai-generate-questions`) tanpa throttle → panggil API AI eksternal (berbiaya) bisa disalahgunakan (abuse biaya/kuota, DoS) | ✅ Fixed |
| F-22 | 🟠 Medium | Terapkan template rapor | `RaporController@applyTemplate`/`applyTemplateToAll` tulis deskripsi capaian ke rapor/kelas via id tanpa cek kepemilikan wali (terlewat di F-11) → wali isi deskripsi rapor kelas lain | ✅ Fixed |

---

## Detail & Perbaikan

### ✅ F-01 — Akses file lintas-pengguna via `/storage-preview` (High)
**Lokasi:** `app/Http/Controllers/FileController.php`, `routes/web.php`
**Isu:** Route menerima `path`/`b64path` langsung dari user dan menyajikan file mana pun di `storage/app/public` (auth-only, tapi tanpa cek kepemilikan). Berisiko bocornya bukti pembayaran, surat izin/sakit presensi, jawaban tugas siswa lain.
**Fix:** Route raw-path dihapus. Semua preview kini lewat token `/view-document/{token}` (lihat F-02). Pemakai lama (`siswa/sia/presensi`, `siswa/lms/pengumuman`) dialihkan ke helper `preview_url()`.

### ✅ F-02 — Enumerasi dokumen via `/view-document/{id}` (High)
**Lokasi:** `app/Http/Controllers/FileController.php`, `app/Helpers/helpers.php`, komponen `file-preview` + preview monitoring/arsip.
**Isu:** id preview = `crc32($path.$sessionId) % 100000` (ruang 0–99.999) tanpa ikatan pemilik → bisa di-brute untuk memanen file user lain; `%100000` juga menimbulkan tabrakan id.
**Fix:**
- Helper baru `preview_url($path)` → token acak `Str::random(48)`, entri cache `docview_<token>` menyimpan `{path, user_id}`.
- `FileController@previewHash` menolak jika entri bukan array atau `user_id` ≠ `auth()->id()` (403), token tak dikenal → 404.
- Semua produser token diseragamkan (component `file-preview`, `monitoring-lms/preview/*`, `guru/lms/arsip/preview-*`).
**Test:** `tests/Feature/FilePreviewAccessTest.php` (pemilik boleh, user lain 403, token asal 404, route lama 404).

### ✅ F-08 — Tampering nilai lintas-kelas (High)
**Lokasi:** `app/Http/Controllers/Guru/GuruNilaiController.php` (`update`, `updateBatch`)
**Isu:** Kedua method memanggil `verifyAccess()` untuk kelas+mapel di route, tetapi lalu memuat `Nilai` dari `nilai_id` yang dikirim user **tanpa memverifikasi nilai itu milik kelas+mapel tsb**. Guru yang mengajar kelas A bisa mengubah nilai siswa di kelas/mapel lain (milik guru lain) dengan mengirim `nilai_id` sembarang. `index/exportExcel/downloadTemplate/importExcel/recalculate` sudah aman (verifyAccess + query ter-scope).
**Fix:** Muat `Nilai` dengan scope `where('kelas_id',$kelasId)->where('mata_pelajaran_id',$mapelId)` (firstOrFail di `update`, skip di `updateBatch`).
**Test:** `tests/Feature/GuruNilaiIdorTest.php`.

### ✅ F-09 — IDOR kelola/koreksi soal & ujian lintas-guru (High)
**Lokasi:** `app/Http/Controllers/Guru/GuruUjianController.php` (banyak method)
**Isu:** `verifyAccess()` hanya memastikan guru mengajar kelas+mapel **di route**, tetapi banyak method memuat `Ujian`/`SoalUjian`/`UjianSiswa` dari `ujianId/soalId/ujianSiswaId` yang dikirim user **tanpa scope `guru_id`**. Guru mana pun bisa: melihat **kunci jawaban** ujian guru lain (`soal`, `manageSoal`, `editSoal`, `getAiSuggestion`), menambah/ubah/hapus soal (`storeSoal/updateSoal/destroySoal/storeAllSoal/importSoal/bulkStoreSoal`), toggle status/visibilitas (`toggleStatus/toggleResultVisibility`), melihat hasil & menilai (`hasil/koreksiShow/koreksiStore`). (`edit/update/destroy/pengawasan/pengawasanData` sudah aman.)
**Fix:** Helper `authorizedUjian(...)` membatasi ujian ke **kelas+mapel yang diajar guru** (dijamin `verifyAccess`), `firstOrFail`; `authorizedSoal(...)` men-scope soal ke ujian tsb. Semua method memuat resource lewat helper ini; `koreksiStore` men-scope `UjianSiswa` ke ujian terotorisasi. Sengaja **tidak** mengunci ke `guru_id` (pembuat) agar team-teaching di kelas+mapel yang sama tetap berjalan (flow-preserving).
**Test:** `tests/Feature/GuruUjianSoalIdorTest.php`.

### ✅ F-10 — IDOR koreksi tugas lintas-kelas (High)
**Lokasi:** `app/Http/Controllers/Guru/GuruKoreksiController.php`
**Isu:** `index/show/store/bulkGrade/getAiAssignmentSuggestion` memanggil `verifyAccess()` untuk kelas+mapel route, tetapi memuat `Tugas`/`TugasSiswa` dari `tugasId/submissionId` yang dikirim user **tanpa scope** → guru bisa membaca & menilai pengumpulan tugas di kelas/mapel lain.
**Fix:** Helper `authorizedTugas(kelas,mapel,tugas)` & `authorizedSubmission(submission,tugas)` (firstOrFail ter-scope, tidak mengunci `guru_id` agar team-teaching jalan); `bulkGrade` membatasi `siswa_ids` ke `tugas_id` terverifikasi. Bonus robustness: `feedback_guru` di `store/bulkGrade` dibuat `?? null` (cegah 500 bila field opsional tidak dikirim).
**Test:** `tests/Feature/GuruKoreksiIdorTest.php`.

### ✅ F-11 — IDOR kelola rapor lintas-kelas (wali kelas) (High)
**Lokasi:** `app/Http/Controllers/WaliKelas/RaporController.php` (banyak method)
**Isu:** `edit/destroy/importExcel/kirimValidasi/batalkanKirimValidasi/applyFormat` sudah cek kepemilikan, tetapi `update/terbitkan/resetNilai/reorderNilai(inti)/tarikKembali/preview/print/autoFillKehadiran/exportExcel` memuat `Rapor::findOrFail($raporId)` **tanpa** verifikasi rapor milik kelas wali → wali bisa ubah/terbitkan/hapus/reset/cetak/ekspor rapor kelas lain. Juga `approveDownload/rejectDownload` memutuskan (dan menerbitkan token unduh) request kelas lain; `requestDownloadIndex` tak ter-scope saat belum ada kelas terpilih.
**Fix:** Helper `assertRaporMilikWali()` (rapor->kelas_id harus di antara kelas ampuan wali via `wali_kelas_assignments`) dipasang di semua method yang bolong; `assertDownloadRequestMilikWali()` untuk approve/reject; `requestDownloadIndex` di-scope ke kelas ampuan saat tak ada kelas terpilih. Scope ke **seluruh kelas ampuan** (bukan hanya "kelas terpilih") agar tak memutus flow multi-kelas.
**Test:** `tests/Feature/WaliRaporIdorTest.php`.

### ✅ F-12 — IDOR validasi akses ujian/rapor lintas-kelas (wali) (High)
**Lokasi:** `app/Http/Controllers/WaliKelas/ValidasiAksesController.php`
**Isu:** `validasiUjian/batalkanUjian/validasiRapor/batalkanRapor` + `bulkValidasiUjian/bulkValidasiRapor` memuat `Siswa::find($siswaId)` tanpa verifikasi siswa berada di kelas wali → wali bisa membuka/membatalkan akses ujian & rapor siswa kelas mana pun (membobol gerbang validasi Bendahara/keuangan). `index/validasiSemua*` sudah aman (scope kelas).
**Fix:** Helper `assertSiswaMilikWali()` + `kelasIdsWali()` (memoized) di 4 method per-siswa; kondisi bulk menambahkan `kelasIdsWali()->contains($siswa->kelas_id)`.
**Test:** `tests/Feature/WaliValidasiAksesIdorTest.php`.

### ✅ F-13 / F-14 — Tampering presensi lintas-kelas (wali) (High)
**Lokasi:** `app/Http/Controllers/WaliKelas/PresensiController.php` (`updatePresensi`, `inputHarian`)
**Isu:** Kedua method memvalidasi `kelas_id`/`siswa_id` hanya dengan `exists:` (ada di tabel), **tanpa** memastikan kelas itu diampu wali dan siswa memang anggota kelas tsb. Wali mana pun bisa membuat/menimpa presensi (mis. menandai "alpha"/"hadir") untuk siswa di kelas lain — data yang ikut menghitung kehadiran rapor. `updateRiwayat/prosesValidasiIzin/importExcel` sudah punya gerbang kelas.
**Fix:** Helper `assertKelasMilikWali($kelasId)` (kelas harus ada di `kelasIdsWali()` = seluruh kelas ampuan wali) + `assertSiswaDiKelas($siswaId,$kelasId)` di `updatePresensi`; `inputHarian` memuat daftar `siswa_id` sah untuk kelas lalu **melewati** baris siswa di luar kelas. Juga `updateRiwayat` dipindah ke `assertPresensiMilikWali()` (menghapus potensi null-deref saat belum ada kelas terpilih).
**Test:** `tests/Feature/WaliPresensiIdorTest.php`.

### ✅ F-15 — Kebocoran bukti izin lewat `previewBukti` (wali) (High)
**Lokasi:** `app/Http/Controllers/WaliKelas/PresensiController.php` (`previewBukti`)
**Isu:** `Presensi::findOrFail($id)` lalu `response()->file(...)` **tanpa otorisasi apa pun** → wali bisa mengunduh/melihat bukti izin/sakit (dokumen pribadi, kadang surat medis) milik siswa kelas mana pun dengan menebak/menghitung `id`. Sekelas dengan F-01/F-02.
**Fix:** Tambah `assertPresensiMilikWali($presensi)` (cek `kelas_id` presensi maupun `kelas_id` siswa berada di antara kelas ampuan wali) sebelum menyajikan file.
**Test:** `tests/Feature/WaliPresensiIdorTest.php`.

### ✅ F-16 — IDOR forum diskusi lintas-kelas/mapel (guru) (Medium)
**Lokasi:** `app/Http/Controllers/Guru/GuruForumController.php` (`show`, `reply`, `destroyReply`, `togglePin`, `toggleClose`)
**Isu:** `verifyAccess()` hanya memastikan guru mengajar kelas+mapel **di route**, tetapi resource dimuat dari `forumId`/`replyId` tanpa scope → dengan menaruh kelas+mapel miliknya sendiri di URL (lolos `verifyAccess`) tapi `forumId` milik kelas/mapel lain, guru bisa: membaca diskusi & balasan siswa kelas lain (`show`), menulis balasan ke diskusi lain (`reply`), menghapus balasan mana pun di sistem (`destroyReply` — komentar lama klaim "in their class" tapi tak ada cek), serta pin/tutup diskusi lain (`togglePin/toggleClose`). `store/updateReply/destroy` sudah aman (owner-scoped / hasAccess).
**Fix:** Helper `authorizedForum(kelas,mapel,forum)` (firstOrFail ter-scope kelas+mapel) dipakai `show/reply/togglePin/toggleClose`; `destroyReply` memuat reply dengan `whereHas('forumDiskusi', kelas+mapel)`. Bagian sinkronisasi ke "kelas lain" tetap aman karena sudah memfilter `hasAccess`.
**Test:** `tests/Feature/GuruForumIdorTest.php`.

### ✅ F-18 — Bayar tagihan siswa lain via `prosesBayar` (Medium)
**Lokasi:** `app/Http/Controllers/OrangTua/OrangTuaController.php` (`prosesBayar`)
**Isu:** Setelah memverifikasi `$siswa = $user->children()->find($siswaId)` (anak sendiri), `tagihan_id` hanya divalidasi `exists:tagihan,id` — tak dicek milik `$siswa`. Wali siswa bisa mengirim `tagihan_id` milik siswa lain; record `Pembayaran` tercatat `siswa_id=anak-sendiri` tapi `tagihan_id=siswa-lain` → mismatch, dan saat divalidasi `tagihan->updateStatusBayar()` bisa mengubah status tagihan siswa lain. `processBulkPay` sudah aman (`if ($tagihan->siswa_id != $siswa->id) continue;`).
**Fix:** Tambah guard `if (!$tagihanCheck || $tagihanCheck->siswa_id != $siswa->id) return back(error)` di `prosesBayar` (menyamakan dengan bulk).
**Test:** `tests/Feature/OrangTuaBayarTagihanIdorTest.php`.

### ✅ F-19 — `snapFinish` percaya status transaksi dari client (High)
**Lokasi:** `app/Http/Controllers/OrangTua/OrangTuaController.php` (`snapFinish`)
**Isu:** Callback redirect Midtrans (`/pembayaran/snap-finish`) membaca `transaction_status` dari **query string** lalu, sebagai "fallback bila webhook belum jalan", mengubah `Pembayaran.status_validasi` (mis. `settlement`→`disetujui`) dan memanggil `tagihan->updateStatusBayar()`. URL finish Midtrans **tidak bertanda tangan** → wali siswa bisa memanggil `snap-finish?order_id=<X>&transaction_status=settlement` untuk menandai pembayaran lunas **tanpa membayar**; endpoint juga tak memverifikasi `isMyChild`.
**Fix:** (1) Tambah otorisasi `isMyChild` pada `firstPayment->siswa_id` (abort 403). (2) **Tidak lagi** memakai `transaction_status` dari query untuk mengubah data — ambil status **otoritatif** via `MidtransService::getTransactionStatus($orderId)` (hanya bila `isConfigured()`, dibungkus try/catch; pola yang sama dipakai `dashboard()`/`tagihanAnak()`). Bila status otoritatif tak tersedia → **tidak** menyentuh DB. Pesan redirect kini berdasar status otoritatif/terkini, bukan query. Jalur utama tetap webhook bertanda tangan.
**Test:** `tests/Feature/OrangTuaSnapFinishForgeryTest.php` (forgery `settlement` → tetap `pending`; order milik anak lain → 403).

### ✅ F-20 — Rute pembayaran siswa menunjuk metode tak ada (Low, QA)
**Lokasi:** `routes/web.php` (grup `siswa.sia.pembayaran`), `Siswa\SiaPembayaranController`
**Isu:** Rute `bayar`→`bayar()`, `cetak`→`cetak()`, plus `midtrans-notification`/`midtrans-finish` menunjuk metode yang **tidak ada** di controller (yang ada: `prosesBayar`, `cetakBukti`; tak ada metode midtrans). View aktif memakainya: `pembayaran/riwayat.blade.php` (tombol cetak bukti) & `pembayaran/index.blade.php` (form bayar) → menekan/submit menghasilkan **HTTP 500**. Bukan celah keamanan (semua ter-scope `siswa_id`), tapi bug nyata. Callback midtrans siswa juga dead (di-`role:siswa`, Midtrans tak bisa memanggilnya).
**Fix:** Arahkan `bayar`→`prosesBayar` (menampilkan pesan "pembayaran lewat wali siswa" sesuai desain) & `cetak`→`cetakBukti` (mengembalikan cetak bukti milik sendiri, ter-scope `siswa_id`). Hapus dua rute callback midtrans siswa yang mati (callback resmi: `MidtransWebhookController` + `OrangTuaController@snapFinish`).

### ✅ F-22 — IDOR "Terapkan Template" capaian rapor lintas-kelas (Medium)
**Lokasi:** `app/Http/Controllers/WaliKelas/RaporController.php` (`applyTemplate`, `applyTemplateToAll`)
**Isu:** Dua method "NEW" (dipakai tombol **Terapkan Template** di edit rapor) memvalidasi `rapor_id`/`kelas_id` hanya `exists:` lalu menulis `RaporNilai.deskripsi` (jika kosong) — **tanpa** cek kepemilikan wali. `applyTemplate` → isi deskripsi rapor mana pun; `applyTemplateToAll` → isi massal seluruh rapor kelas mana pun. **Terlewat saat F-11** (kedua method baru ditambahkan terpisah); ditemukan saat menelusuri alur pemakaian pustaka Template Capaian (F-17). Catatan konteks: halaman kelola template (`/wali/template-capaian`) **orphan** (tak ada link sidebar; hanya via URL), tetapi template-nya dikonsumsi lewat alur ini.
**Fix:** `applyTemplate` → `assertRaporMilikWali(Rapor::findOrFail($rapor_id))`; `applyTemplateToAll` → cek `kelas_id ∈ getKelasWali()->pluck('id')`, abort 404 bila bukan.
**Test:** `tests/Feature/WaliApplyTemplateIdorTest.php`.

### ✅ F-21 — Endpoint AI guru tanpa rate-limit (Medium)
**Lokasi:** `routes/web.php` (grup `guru.lms.*`): `koreksi.ai-suggest` (tugas), `ujian/latihan koreksi.ai-suggest`, `ujian/latihan soal.ai-generate`.
**Isu:** Kelima endpoint memanggil layanan AI eksternal berbiaya (`AiGradingService`/`GuruUjianController@getAiSuggestion`/`aiGenerateQuestions`) tanpa throttle. Sesi guru yang bocor/berniat jahat bisa membanjiri endpoint → tagihan/kuota API meledak & potensi DoS pihak ketiga. `ai-chatbot/send-message` sudah `throttle:10,1` (preseden).
**Fix:** Tambah `throttle:30,1` untuk `ai-suggest` (grading, wajar sering) dan `throttle:15,1` untuk `ai-generate-questions` (lebih berat). Batas dibuat longgar agar tak memutus pemakaian normal.

### ✅ F-03 — Otorisasi rapor siswa (Medium) — Resolved by design
**Keputusan pemilik:** siswa **tidak berhak** melihat/mengunduh rapor; yang meminta unduh rapor adalah orang tua/wali siswa. **Status kode saat ini sudah sesuai:** seluruh rute `Siswa\SiaRaporController` dinonaktifkan (`routes/web.php:1461-1466` dikomentari; `use SiaRaporController` di baris 72 dikomentari), dan sidebar siswa menegaskan "Menu Rapor & Pembayaran dipindahkan ke akses Wali Siswa". Siswa tak punya rute/menu ke rapor → tidak ada IDOR maupun link patah. **Tidak perlu perubahan kode.** (Opsional/kerapian: view `resources/views/siswa/sia/rapor/*` & controller `SiaRaporController` kini orphan/dead-code, boleh dihapus kemudian.)

### ✅ F-04 — IP klien bisa dipalsukan (Medium)
**Lokasi:** `bootstrap/app.php`
**Isu:** `trustProxies(at:'*')` mempercayai `X-Forwarded-For` dari siapa pun. Perlu saat memakai Cloudflare Tunnel (origin tak punya IP publik), tetapi setelah pindah ke **VPS yang langsung terekspos** (Nginx + PHP-FPM di mesin sama, tanpa reverse-proxy berlapis), `*` berbahaya: klien bisa memalsukan `X-Forwarded-For` → IP palsu → menembus rate-limit login per-IP & mengotori `recovery_tickets.requested_ip`/`FinancialAuditLog.ip_address`.
**Fix:** `trustProxies(at: ['127.0.0.1', '::1'])` — hanya percaya loopback. Request internet (`REMOTE_ADDR` = IP asli, bukan loopback) tak dipercaya headernya → Laravel pakai IP asli (anti-spoof); header `X-Forwarded-Proto` dari Nginx lokal tetap dihormati (deteksi HTTPS/CSRF aman).
**⚠️ Verifikasi pasca-deploy (wajib di VPS):** setelah deploy, uji **login/POST** sekali. Jika muncul **419 (CSRF/"sesi berakhir")** di semua form, berarti Nginx belum meneruskan info HTTPS ke PHP → tambahkan `fastcgi_param HTTPS on;` pada blok SSL Nginx (atau di server-block: pastikan `fastcgi_params` menyertakan `fastcgi_param HTTPS $https if_not_empty;`). Rollback cepat bila mendesak: kembalikan sementara `at: '*'`.

### ✅ F-05 — Header keamanan kurang (Low)
**Lokasi:** `app/Http/Middleware/SecurityHeaders.php`
**Fix:** (1) **HSTS** diaktifkan tetapi hanya pada koneksi HTTPS (`$request->isSecure()`), `max-age=31536000` tanpa `includeSubDomains` (agar tak mengunci subdomain yang mungkin belum HTTPS). (2) **CSP minimal & aman**: `frame-ancestors 'self'; object-src 'none'; base-uri 'self'` — sengaja TIDAK membatasi `script/style/img/font` agar UI (Sneat/Vite/Bootstrap/FontAwesome/inline script) tetap jalan. **Catatan:** policy penuh (`script-src`/`default-src`) perlu inventarisasi aset lebih dulu (pekerjaan lanjutan bila diinginkan).

### ✅ F-06 — Kebocoran info & auto-logout `CheckRole` (Low) — Opsi 1
**Lokasi:** `app/Http/Middleware/CheckRole.php`
**Isu:** Saat gagal otorisasi, pesan error menyebut **nama role user & role yang dibutuhkan** (kebocoran info) dan melakukan `logout()`+invalidate session (komentar: "untuk kemudahan testing").
**Fix (Opsi 1, disetujui pemilik):** kegagalan otorisasi ≠ autentikasi → `abort(403, 'Anda tidak memiliki akses ke halaman ini.')` **tanpa** logout. User tetap login (hanya halaman itu ditolak), pesan generik (tak bocorkan role). Alasan mengganti auto-logout: (a) membuang sesi + kerja user yang belum tersimpan, (b) menjadi "jebakan logout" (link salah-role = tombol logout), (c) tak menambah keamanan (penyerang tinggal login lagi). Semantik HTTP yang benar untuk authz gagal = 403.
**Test:** `tests/Feature/CheckRoleForbidsWithoutLogoutTest.php` (403 + tetap login + pesan tanpa nama role).
**Catatan:** belum ada view `errors/403.blade.php` → memakai halaman 403 bawaan Laravel (fungsional; bisa dipercantik nanti bila mau).

### ✅ F-07 — `parent_id` reply forum tak ter-scope (Low)
**Lokasi:** `Siswa\LmsForumController@reply`, `Guru\GuruForumController@reply`
**Isu:** `parent_id` (balasan-induk pada thread — **bukan** orang tua) hanya divalidasi `exists:forum_replies,id`, tak dipastikan berada di diskusi yang sama → balasan bisa "menempel" ke induk dari diskusi lain (kerapian threading; bukan kebocoran/eskalasi). Forum hanya diikuti **siswa & guru** — orang tua tak punya akses forum.
**Fix:** sebelum membuat balasan, bila `parent_id` diisi → cek `ForumReply where id=parent AND forum_diskusi_id=diskusi ini`; bila tidak cocok → 404. Dipasang di sisi siswa & guru.
**Test:** `tests/Feature/ForumReplyParentScopeTest.php`.

### ✅ F-17 — Template capaian: hapus tanpa cek pemilik (Low) — Opsi A
**Lokasi:** `app/Http/Controllers/WaliKelas/TemplateCapaianController.php` (`destroy`)
**Isu:** `destroy` memuat `findOrFail($id)` lalu menghapus **tanpa** cek `created_by` → wali mana pun bisa menghapus permanen template buatan rekannya. `index` menampilkan semua template lintas-pembuat → memang **pustaka bersama** antar-wali (integritas sesama-role, bukan eskalasi/kebocoran lintas-role).
**Keputusan pemilik (Opsi A):** pertahankan pustaka bersama — **tambah & edit tetap terbuka** untuk semua wali, tetapi **HAPUS dikunci ke pembuat** (`created_by`). ("+admin" tak relevan: halaman `role:wali_kelas`, admin tak punya rute ke sini.)
**Fix:** `destroy` → `if ($template->created_by != auth()->id()) return back(error)`.
**Test:** `tests/Feature/WaliTemplateCapaianDeleteTest.php`.
**Catatan penting (temuan navigasi):** seluruh subsistem Template Capaian ternyata **orphan/tak tersambung UI** — halaman `/wali/template-capaian` tak punya link menu/tombol, dan endpoint yang memakainya (`RaporController@applyTemplate`/`applyTemplateToAll`, lihat F-22) juga **tak dipanggil view mana pun**. Tombol "Terapkan Template" di edit rapor sebenarnya memakai fitur lain `applyFormat` (`wali.rapor.apply-format`) yang **sudah aman** (sumber & target di-scope ke kelas wali via `accessibleClassIds`) dan **tidak** menyentuh pustaka ini. Fix F-17/F-22 = pengaman lapis-tambahan untuk rute terdaftar-tapi-tanpa-UI (masih bisa dipanggil via POST langsung). Opsi kerapian ke depan: hapus subsistem template capaian bila memang tak dipakai.

---

## Area yang sudah diverifikasi AMAN
- **Ujian siswa** (`LmsUjianController`): scope kelas+mapel, penilaian server-side, kunci jawaban tidak bocor (hardening terpisah + test).
- **Tugas siswa** (`LmsTugasController`): scope kelas+mapel, submission diikat `siswa_id`, upload divalidasi.
- **Materi siswa** (`LmsMateriController`): scope kelas+mapel.
- **Forum siswa** (`LmsForumController`): scope kelas, edit/hapus hanya milik sendiri.
- **Rapor siswa** (`SiaRaporController`): tidak ada IDOR lintas-siswa (scope `siswa_id` + gerbang validasi 3-level).
- **Tugas/Materi guru** (`GuruTugasController`, `GuruMateriController`): `edit/update/destroy` dobel-scope (`verifyAccess` + `where('guru_id')->firstOrFail()`).
- **Ujian guru** (`GuruUjianController`): `edit/update/destroy/pengawasan/pengawasanData` ter-scope `guru_id`; sisanya diperbaiki di F-09.
- **Nilai wali kelas** (`WaliKelas\NilaiController` + trait `WaliKelasHelper`): `update/clearNilai/syncFromGuru/import` di-scope ke kelas wali; `getSelectedKelas()` memvalidasi session terhadap `wali_kelas_assignments` (tak bisa pilih kelas sembarang).
- **Meeting/kelas virtual guru** (`GuruLmsMeetingController`): `index` filter `guru_id`; `edit/update/destroy` muat `where('guru_id')->firstOrFail()`; duplikasi ke kelas lain difilter `hasAccess`.
- **Catatan monitoring guru** (`GuruCatatanMonitoringController`): `index/show` di-scope `forGuru($tp->id)` = `where('guru_id',...)` → hanya catatan yang ditujukan ke guru ybs.
- **Dashboard/Jadwal/Kelas guru** (`GuruLmsController@dashboard`, `GuruJadwalController`, `GuruKelasController`): read-only, statistik & data di-scope `guru_id` + `verifyAccess`/`GuruPengajarKelas`.
- **Arsip LMS guru** (`GuruLmsArsipController` + `GuruLmsArsipService`): `resolveKonten` scope sumber `where('guru_id')->findOrFail`; `salin*` verifikasi sumber (milik guru) **dan** tujuan (`validateKelasMapelTujuan` = diampu di TA aktif).
- **Dashboard/Rapor-pending/Jadwal wali** (`WaliKelasController`): read-only, di-scope `getKelasWali`/`wali_kelas_assignments`/kelas terpilih.
- **Pilih kelas wali** (`PilihKelasController`): `select()` memverifikasi kelas ∈ `getKelasWali` sebelum simpan session.
- **Jadwal wali** (`WaliKelas\JadwalPelajaranController`): read-only, scope kelas terpilih (dari `getKelasWali`).
- **Prediksi kenaikan wali** (`WaliKelas\PromotionController`): read-only, scope `getSelectedKelas`; eksekusi kenaikan/kelulusan ada di peran admin/ketua/bendahara (di luar segitiga pembelajaran).
- **Arsip wali** (`WaliKelasArsipController`): tiap method `guardAccess($kelas)` memverifikasi wali pernah di-assign ke kelas via `wali_kelas_assignments` (akses historis read-only).
- **Orang tua / wali siswa** (`OrangTua\OrangTuaController`): semua akses per-anak dijaga `children()->find()` (belongsToMany via `student_parents`) atau `isMyChild = children()->where('siswa.id', …)->exists()` yang **ditegakkan** (redirect/abort 403) — `tagihanAnak/prosesBayar/processBulkPay/raporAnak/detailRapor/presensiAnak/ajukanIzin/storeIzin/riwayat*/editIzin/updateIzin/snapPayment/continuePayment/cetakInvoice/requestDownloadRapor`. `downloadRapor($token)` mengikat token ke `user_id` + status disetujui + kadaluarsa. `detailRapor` juga cek gerbang 3-level `hasFullRaporAccess()`. `prosesBayar` (F-18) & `snapFinish` (F-19) sudah diperbaiki.
- **Webhook Midtrans** (`MidtransWebhookController@notification`): jalur otoritatif memperbarui status; memverifikasi `signature_key` = `sha512(order_id + status_code + gross_amount + serverKey)` sebelum menyentuh data (invalid → 403). `mapTransactionStatus` menandai `disetujui` hanya untuk `settlement`/`capture(accept)`. **Catatan minor (Low):** `verifySignature` memakai `===`; boleh diganti `hash_equals()` untuk timing-safety (bukan celah eksploitatif — SHA512 tak bisa dipalsukan tanpa serverKey).
- **Login** (`LoginRequest`): **captcha wajib** tiap percobaan + `RateLimiter` 5 percobaan per (login+IP) dengan lockout + cek `is_active`. Kunci throttle pakai login+IP; captcha jadi backstop bila IP dipalsukan (lihat F-04). Brute-force tak praktis.
- **Pemulihan admin** (`AdminRecoveryController`): `unlock` hanya toggle UI (tak ada cek kredensial); `reset` `throttle:5,1` + multi-faktor (pertanyaan keamanan + jawaban `Hash::check` + PIN 6-digit `Hash::check`) + cek peran admin/ketua + pesan generik (anti-enumerasi) + logging. Brute-force PIN ter-hash @5/menit infeasible.
- **Pemulihan pengguna** (`UserRecoveryController` + reset via link): `POST /recovery` & reset `throttle:5,1`; link reset pakai token + `expires_at` + status non-final + `lockForUpdate` (anti-race).
- **AI chatbot** (`ai-chatbot/send-message`): `throttle:10,1`.
- **Validasi input (Fase 4)**: tak ada mass-assignment `update/create($request->all())`; semua controller pakai `$request->validate()` + array field eksplisit. Tak ada SQL injection — semua `selectRaw/orderByRaw/whereRaw` memakai string hardcoded atau binding `?` (mis. `SyncModuleToSheet` `whereRaw("... = ?", [$bulan])`). XSS: keluaran data pengguna konsisten `{!! nl2br(e($x)) !!}` (di-escape dulu); `{!! $var !!}` mentah hanya untuk atribut server (`rowspan/colspan`) & teks instruksi hardcoded.
- **Upload (Fase 5)**: divalidasi `image|mimes:...`/`mimes:pdf` + `max`; nama file di-generate server (`hashName()`/`time().uniqid().ext`) → tak ada path traversal; penyajian file ter-otorisasi (F-01/F-02/F-15).
- **CSRF (Fase 6)**: aktif global; pengecualian **hanya** webhook Midtrans (`midtrans/*`, `midtrans/notification`) yang memang eksternal & diverifikasi tanda tangan. Penanganan 419 ramah. `SecurityHeaders` + `RejectEmailHeaderInjection` dipasang global.

---

## Progres Fase
- [x] Fase 1 — Pemetaan permukaan (middleware, role, route, controller)
- [x] Fase 2 — Kontrol akses/IDOR segitiga pembelajaran (**siswa, guru, wali kelas**) + orang tua/wali siswa; F-08..F-16, F-18 ditambal + F-17 dicatat (kebijakan)
- [x] Fase 3 — Autentikasi: login (captcha+ratelimit), recovery admin/user (throttle+MFA) — terverifikasi aman
- [x] Fase 4 — Validasi input / SQLi / XSS / mass assignment — terverifikasi bersih
- [x] Fase 5 — Upload file — divalidasi mimes + nama server-generated
- [x] Fase 6 — CSRF — aktif, pengecualian minimal (webhook Midtrans)
- [x] Fase 7 — Rate-limit / DoS: endpoint AI di-throttle (F-21); login/recovery ter-throttle
- [x] Fase 8 — Pembayaran/Midtrans: F-19 ditambal, webhook signature terverifikasi, F-20 rute dibersihkan
- [ ] Fase 9 — Kompilasi laporan akhir (in progress) + tindak lanjut temuan kebijakan (F-03..F-07, F-17)
