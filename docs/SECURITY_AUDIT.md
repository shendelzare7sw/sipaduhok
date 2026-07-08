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
