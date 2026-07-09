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
| F-03 | 🟠 Medium | Rapor siswa | Otorisasi `SiaRaporController@index` tidak konsisten dgn detail/download | ⏳ Open |
| F-04 | 🟠 Medium | Infra | `trustProxies(at:'*')` → IP klien bisa dipalsukan (log & rate-limit) | ⏳ Open |
| F-05 | 🟡 Low | Headers | Tidak ada Content-Security-Policy; HSTS dikomentari | ⏳ Open |
| F-06 | 🟡 Low | Auth | `CheckRole` membocorkan nama role di pesan error + auto-logout | ⏳ Open |
| F-07 | 🟡 Low | Forum | `parent_id` reply hanya `exists:` tanpa scope ke diskusi | ⏳ Open |
| F-08 | 🔴 High | Nilai guru | `GuruNilaiController@update/updateBatch` ubah `nilai_id` tanpa scope kelas+mapel → tampering nilai lintas-kelas | ✅ Fixed |
| F-09 | 🔴 High | Ujian guru | `GuruUjianController` kelola/koreksi soal & ujian via `ujianId/soalId` yang tak dibatasi ke kelas+mapel yang diajar → baca kunci jawaban / ubah / hapus / nilai ujian di kelas/mapel lain | ✅ Fixed |
| F-10 | 🔴 High | Koreksi tugas | `GuruKoreksiController` `show/store/bulkGrade/ai-suggest` memuat `TugasSiswa/Tugas` tanpa scope kelas+mapel → nilai/baca pengumpulan tugas kelas lain | ✅ Fixed |
| F-11 | 🔴 High | Rapor wali | `WaliKelas\RaporController` `update/terbitkan/resetNilai/reorderNilai/tarikKembali/preview/print/autoFillKehadiran/exportExcel` + `approve/rejectDownload` tanpa cek kelas wali → kelola/lihat/cetak rapor & setujui unduh kelas lain | ✅ Fixed |
| F-12 | 🔴 High | Validasi akses wali | `ValidasiAksesController` validasi/batal ujian&rapor + bulk via `siswaId` tanpa cek kelas → wali buka akses ujian/rapor siswa kelas lain (bobol gerbang keuangan) | ✅ Fixed |
| F-13 | 🔴 High | Presensi wali | `PresensiController@updatePresensi` tulis presensi via `siswa_id`+`kelas_id` sembarang (hanya `exists:`) tanpa cek kelas ampuan → tampering absensi siswa kelas lain | ✅ Fixed |
| F-14 | 🔴 High | Presensi wali | `PresensiController@inputHarian` (bulk) sama seperti F-13; `kelas_id`/`siswa_id` tak diverifikasi milik wali | ✅ Fixed |
| F-15 | 🔴 High | Bukti izin wali | `PresensiController@previewBukti` sajikan file bukti izin via `id` tanpa otorisasi → wali lihat dokumen izin/sakit (pribadi) siswa kelas mana pun | ✅ Fixed |
| F-16 | 🟠 Medium | Forum guru | `GuruForumController` `show/reply/destroyReply/togglePin/toggleClose` muat forum/reply via id tanpa scope kelas+mapel → baca/tulis/hapus/pin diskusi kelas/mapel yang tak diajar | ✅ Fixed |

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

### ⏳ F-03 — Inkonsistensi otorisasi rapor siswa (Medium)
`SiaRaporController@index:31` memblokir non-`orang_tua` (route `role:siswa` → daftar rapor selalu ditolak untuk siswa), sedangkan `tengahSemester/akhirSemester/download` tidak → siswa tetap bisa buka/unduh rapor sendiri via URL. Perlu keputusan kebijakan: siswa boleh lihat rapor sendiri atau tidak, lalu samakan di semua method.

### ⏳ F-04 — IP klien bisa dipalsukan (Medium)
`bootstrap/app.php:16` `trustProxies(at:'*')`. Jika origin tak dikunci hanya ke Cloudflare, `X-Forwarded-For` bisa dipalsukan → merusak `recovery_tickets.requested_ip` & rate-limit IP. Rekomendasi: batasi ke rentang IP Cloudflare / proxy tepercaya.

### ⏳ F-05 — Header keamanan kurang (Low)
`SecurityHeaders.php` belum memasang Content-Security-Policy; HSTS dikomentari. Tambah CSP (minimal) & aktifkan HSTS saat HTTPS.

### ⏳ F-06 — Kebocoran info & auto-logout `CheckRole` (Low)
`CheckRole` menampilkan nama role di pesan error dan melakukan `logout()` saat gagal otorisasi. Ganti pesan generik; jangan logout hanya karena beda role.

### ⏳ F-07 — `parent_id` reply forum tak ter-scope (Low)
`LmsForumController@reply` memvalidasi `parent_id` hanya `exists:forum_replies,id` tanpa memastikan parent berada di diskusi yang sama.

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

---

## Progres Fase
- [x] Fase 1 — Pemetaan permukaan (middleware, role, route, controller)
- [~] Fase 2 — Kontrol akses/IDOR: **siswa selesai**; guru & wali berikutnya
- [ ] Fase 3 — Autentikasi (login, PIN, recovery, session, password)
- [ ] Fase 4 — Validasi input / SQLi / XSS / mass assignment
- [ ] Fase 5 — Upload file
- [ ] Fase 6 — CSRF & endpoint state-changing
- [ ] Fase 7 — Rate-limit / DoS
- [ ] Fase 8 — Logika bisnis (ujian, nilai, pembayaran/Midtrans)
- [ ] Fase 9 — Kompilasi laporan akhir
