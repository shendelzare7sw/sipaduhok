# SIPADUHOK — Peta Aplikasi & Alur (flow.md)

> **Dokumen ini adalah titik masuk (hub) untuk memahami aplikasi.** Dibuat agar AI agent lain maupun developer baru bisa cepat memetakan aplikasi tanpa menelusuri ~1.600 baris `routes/web.php` dan ~100 controller satu per satu. Mulai dari sini, lalu buka detail per-role di `docs/flow/<role>.md`.

---

## 1. Tentang Aplikasi

**SIPADUHOK** adalah Sistem Informasi & Pembelajaran untuk **PKBM (Pusat Kegiatan Belajar Masyarakat)** — lembaga pendidikan nonformal (kesetaraan Paket A/B/C, PAUD/TK, inklusi, terapi). Aplikasi menggabungkan **administrasi sekolah** (SIA) dan **pembelajaran daring** (LMS) dalam satu sistem multi-cabang, multi-role.

| Aspek | Nilai |
|---|---|
| Framework | Laravel 11 (PHP) |
| Frontend | Blade + template **Sneat** (Bootstrap 5) + Vite + Tailwind |
| Auth | Session-based, custom role system (tabel `roles`, kolom `users.role_id`) |
| Paket kunci | `barryvdh/laravel-dompdf` (cetak rapor/kwitansi PDF), `maatwebsite/excel` (import/export Excel), `midtrans/midtrans-php` (pembayaran online), `google/apiclient` (sinkron Google Sheets) |
| Entry routing | `bootstrap/app.php` → `routes/web.php` (rute utama, 1 file), `routes/routecadangan.php` (cadangan), `routes/console.php` |

Tujuan: satu platform untuk PPDB → administrasi siswa → KBM (materi/tugas/ujian daring) → penilaian → presensi → keuangan (tagihan/pembayaran) → rapor → kenaikan kelas, dengan kontrol akses berjenjang antar peran.

---

## 2. Konsep Inti

- **Multi-cabang (`Cabang`)** — data siswa/kelas tersekat per cabang; sebagian role (mis. Wakil Kepala Sekolah) hanya melihat cabangnya.
- **Tahun Ajaran (`TahunAjaran`)** — semua data akademik (kelas, nilai, rapor) terikat tahun ajaran; satu TA berstatus aktif. Ada fitur **arsip lintas-TA** (Guru "Arsip LMS", Wali Kelas "Arsip Kelas Saya") untuk reuse/baca data TA lama.
- **Dualisme SIA vs LMS** — *SIA* = sisi administrasi/akademik (presensi, nilai, keuangan, rapor). *LMS* = sisi pembelajaran daring (materi, tugas, latihan, ujian, forum, kelas virtual). **Guru dan Siswa punya dua layout sidebar** karena hidup di dua dunia ini.
- **RBAC berjenjang (9 role)** — middleware `role:<nama>`; Admin (level 1) bypass semua pengecekan role (lihat `app/Http/Middleware/CheckRole.php`).
- **Rantai validasi lintas-role** — keputusan penting (rapor, akses ujian, kenaikan kelas, dispensasi) sengaja dipecah ke beberapa role sebagai kontrol berlapis. Ini bagian paling khas aplikasi — lihat Bagian 5.

---

## 3. Sembilan Role

Sumber kebenaran: `database/seeders/RoleSeeder.php`. Nama role (kolom `roles.name`, snake_case) dipakai persis sebagai argumen middleware `role:<name>`.

| Role (DB) | Nama Tampilan | Level | Prefix URL | Prefix Route | Dashboard | Controller Dashboard | Detail |
|---|---|---|---|---|---|---|---|
| `admin` | Admin | 1 | `/admin` | `admin.` | `admin.dashboard` | `Admin\DashboardController@index` | [docs/flow/admin.md](docs/flow/admin.md) |
| `ketua_pkbm` | Ketua PKBM | 2 | `/ketua` | `ketua.` | `ketua.dashboard` | `DashboardController@ketua` | [docs/flow/ketua-pkbm.md](docs/flow/ketua-pkbm.md) |
| `wakil_kepala_sekolah` | Wakil Kepala Sekolah | 2 | `/waka` | `waka.` | `waka.dashboard` | `WakilKepalaSekolah\WakilKepalaSekolahController@dashboard` | [docs/flow/wakil-kepala-sekolah.md](docs/flow/wakil-kepala-sekolah.md) |
| `sekretaris` | Sekretaris | 3 | `/sekretaris` | `sekretaris.` | `sekretaris.dashboard` | `Sekretaris\SekretarisController@dashboard` | [docs/flow/sekretaris.md](docs/flow/sekretaris.md) |
| `bendahara` | Bendahara | 2 | `/bendahara` | `bendahara.` | `bendahara.dashboard` | `Bendahara\BendaharaController@dashboard` | [docs/flow/bendahara.md](docs/flow/bendahara.md) |
| `wali_kelas` | Wali Kelas | 4 | `/wali` | `wali.` | `wali.dashboard` | `WaliKelas\WaliKelasController@dashboard` | [docs/flow/wali-kelas.md](docs/flow/wali-kelas.md) |
| `guru_pengajar` | Guru Pengajar | 4 | `/guru` | `guru.` | `guru.dashboard` | `DashboardController@guru` | [docs/flow/guru.md](docs/flow/guru.md) |
| `orang_tua` | Orang Tua/Wali | 5 | `/orang-tua` | `orang-tua.` | `orang-tua.dashboard` | `OrangTua\OrangTuaController@dashboard` | [docs/flow/orang-tua.md](docs/flow/orang-tua.md) |
| `siswa` | Siswa | 6 | `/siswa` | `siswa.` | `siswa.dashboard` | `Siswa\SiswaDashboardController@index` | [docs/flow/siswa.md](docs/flow/siswa.md) |

> Level kecil = wewenang lebih tinggi. Banyak role level 2–4 berbagi fitur (mis. monitoring, manajemen akademik); fitur sama dijalankan controller berbeda dengan route prefix berbeda — lihat Bagian 4.

---

## 4. Konvensi & Cara Baca Kode

**Pola grup route** (`routes/web.php`, semua di dalam `Route::middleware(['auth'])`):

```php
Route::middleware(['role:<role>'])->prefix('<prefix>')->name('<prefix>.')->group(function () {
    Route::get('/dashboard', [XController::class, 'index'])->name('dashboard'); // → <prefix>.dashboard
    Route::prefix('resource')->name('resource.')->group(...);                   // → <prefix>.resource.<action>
});
```

- **Nama route** selalu `<prefixRole>.<resource>.<action>`, mis. `admin.kelas.index`, `guru.lms.materi.store`, `siswa.lms.mapel.tugas.submit`.
- **Lokasi controller**: `app/Http/Controllers/<Role>/` (mis. `Admin/`, `Ketua/`, `Bendahara/`, `WaliKelas/`, `Guru/`, `Siswa/`, `OrangTua/`, `WakilKepalaSekolah/`, `Sekretaris/`). Beberapa controller dipakai lintas-role (mis. Waka & Admin sama-sama memakai `Admin\Akademik\PromotionReportController`).
- **Lokasi view**: `resources/views/<role>/...`. Sidebar: `resources/views/<role>/partials/`.
- **Dua sistem sidebar**:
  - **Sneat** (`partials/sneat-sidebar-menu.blade.php`, untuk siswa `sneat-sidebar-sia.blade.php`) — sidebar dashboard utama tiap role.
  - **LMS** (`partials/sidebar-lms.blade.php`) — hanya **guru** & **siswa**, sidebar khusus konteks pembelajaran per kelas/mapel. Guru juga punya `sidebar-lms-notif.blade.php` (sidebar minimal untuk halaman notifikasi dari konteks LMS).
- **Alur login** (`app/Http/Controllers/Auth/LoginController.php@store`): autentikasi → catat `last_login_at/ip` → ambil role dari `users.roleRelation->name` (fallback kolom lama `users.role`) → `match($roleName)` redirect ke `<role>.dashboard` (kecuali ada intended URL valid). Tanpa role → `dashboard` fallback (`DashboardController@index`).
- **Middleware penting** (alias di `bootstrap/app.php`):
  | Alias | Kelas | Fungsi |
  |---|---|---|
  | `role` | `CheckRole` | Cek role; **admin bypass**; logout jika mismatch |
  | `student.active` | `CheckStudentActive` | Siswa harus berstatus aktif (dipasang di grup `/siswa`) |
  | `lms.access` | `CheckLmsAccess` | Akses LMS siswa hanya untuk jenjang yang diizinkan (`AppSetting` key `lms_allowed_jenjang`) |
  | `siswa.mapel.access` | `CheckSiswaMapelAccess` | Siswa hanya boleh buka mapel yang ada di jadwal kelasnya |
  | `superadmin` / `role.level` | `EnsureSuperAdmin` / `EnsureRoleLevel` | Variasi pengecekan level |
  - Global (append ke web): `SecurityHeaders`, `EnsureUserIsActive`, `CheckAdminSecuritySetup` (paksa setup 2FA admin saat pertama login).
- **Akun & profil & notifikasi** tersedia untuk **semua role** (tanpa prefix role): `account.*` (`AccountController`), `profile.*` (`ProfileController`), `notifications.*` (`NotificationController`). Webhook Midtrans (`midtrans.notification`) publik & dikecualikan dari CSRF.

---

## 5. Peta Alur Lintas-Role (bagian terpenting)

Aplikasi sengaja **melempar tanggung jawab keputusan** ke role lain sebagai kontrol berlapis. Lima alur utama:

### 5.1 Validasi Rapor — rantai 3 tingkat (Wali Kelas → Bendahara → Ketua PKBM → Orang Tua)

Tujuan: rapor hanya bisa diunduh wali murid setelah lolos cek **akademik**, **keuangan**, dan **persetujuan pimpinan**. Status disimpan di kolom-kolom `siswa.validasi_rapor_*`.

```
Wali Kelas                    Bendahara                 Ketua PKBM                Wali Kelas        Orang Tua
─────────                     ─────────                 ──────────                ─────────         ─────────
generate rapor (draft)
  RaporController@generateAll
kirim validasi  ───────────►  cek keuangan ──────────►  approval final ────────► terbitkan ──────► request &
 @kirimValidasi               @validasiRapor            @validasiRapor            @terbitkan        download
 set validasi_rapor_wali      set validasi_rapor_       set validasi_rapor_ketua  Rapor.status=     @raporAnak /
                              bendahara                  (atau @mintaRevisi →     diterbitkan,      @downloadRapor
                                                          reset ke Wali)          allow_download
```

| Langkah | Role | Route name | Controller@method |
|---|---|---|---|
| Generate rapor draft | Wali Kelas | `wali.rapor.generate-all` / `wali.rapor.generate-single` | `WaliKelas\RaporController@generateAll` / `generateSingle` |
| Kirim ke validasi | Wali Kelas | `wali.rapor.kirim-validasi` (`...semua`) | `WaliKelas\RaporController@kirimValidasi` |
| Validasi keuangan | Bendahara | `bendahara.validasi-akses.validasi-rapor` | `Bendahara\ValidasiAksesController@validasiRapor` |
| Approval final | Ketua PKBM | `ketua.validasi-rapor.validasi` | `Ketua\ValidasiRaporController@validasiRapor` |
| Minta revisi (balik ke Wali) | Ketua PKBM | `ketua.validasi-rapor.minta-revisi` | `Ketua\ValidasiRaporController@mintaRevisi` |
| Terbitkan (buka unduh) | Wali Kelas | `wali.rapor.terbitkan` | `WaliKelas\RaporController@terbitkan` |
| Tarik kembali | Wali Kelas | `wali.rapor.tarik-kembali` | `WaliKelas\RaporController@tarikKembali` |
| Orang tua minta & unduh | Orang Tua | `orang-tua.rapor.request-download` → `orang-tua.rapor.download` | `OrangTua\OrangTuaController@requestDownloadRapor` / `downloadRapor` |
| Wali approve request unduh | Wali Kelas | `wali.rapor.request-download.approve` | `WaliKelas\RaporController@approveDownload` |

Model: `Rapor` (`status` draft/diterbitkan, `allow_download`, `status_review_ketua`, `catatan_revisi_ketua`), `RaporNilai`, `Siswa` (flag `validasi_rapor_wali/bendahara/ketua`), `RequestDownloadRapor`.

### 5.2 Dispensasi Keuangan (Bendahara/Admin → Ketua PKBM)

Tujuan: siswa yang belum lunas tetap bisa diberi **kelonggaran** akses rapor/ujian, tapi keputusannya **bukan** di tangan Bendahara — dilempar ke Ketua PKBM.

```
Bendahara/Admin                                   Ketua PKBM
───────────────                                   ──────────
ajukanDispensasi (tipe: rapor|ujian) ───────────► tinjau daftar pengajuan
  buat PengajuanRaporKetua status="menunggu"        @dispensasiIndex
                                                  approve ──► set siswa.validasi_(rapor|ujian)_bendahara=true
                                                    @approveDispensasi
                                                  reject  ──► status="ditolak"
                                                    @rejectDispensasi
```

| Langkah | Role | Route name | Controller@method |
|---|---|---|---|
| Ajukan dispensasi | Bendahara | `bendahara.validasi-akses.dispensasi` (POST) | `Bendahara\ValidasiAksesController@ajukanDispensasi` |
| Ajukan dispensasi | Admin | `admin.keuangan.validasi-akses.dispensasi` (POST) | `Admin\Keuangan\ValidasiAksesController@ajukanDispensasi` |
| Lihat & putuskan | Ketua PKBM | `ketua.dispensasi.index` / `.approve` / `.reject` | `Ketua\ValidasiRaporController@dispensasiIndex` / `approveDispensasi` / `rejectDispensasi` |

Model: `PengajuanRaporKetua` (`tipe`, `periode`, `status` menunggu/disetujui/ditolak, `diajukan_oleh`, `diputuskan_oleh`, `catatan_ketua`). Badge "Dispensasi Keuangan" di sidebar Ketua menghitung `PengajuanRaporKetua::where('status','menunggu')`.

### 5.3 Validasi Akses Ujian (Bendahara/Wali Kelas/Admin → Siswa)

Tujuan: siswa hanya bisa mengikuti ujian besar bila sudah divalidasi (umumnya cek lunas). Flag: `siswa.validasi_ujian_bendahara` / `validasi_ujian_wali`.

| Langkah | Role | Route name | Controller@method |
|---|---|---|---|
| Validasi ujian (keuangan) | Bendahara | `bendahara.validasi-akses.validasi-ujian` | `Bendahara\ValidasiAksesController@validasiUjian` |
| Validasi ujian (akademik) | Wali Kelas | `wali.validasi-akses.validasi-ujian` | `WaliKelas\ValidasiAksesController@validasiUjian` |
| (mirror) | Admin | `admin.keuangan.validasi-akses.validasi-ujian` | `Admin\Keuangan\ValidasiAksesController@validasiUjian` |
| Siswa mengerjakan | Siswa | `siswa.lms.mapel.ujian.show`/`mulai`/`submit` | `Siswa\LmsUjianController@show`/`mulai`/`submit` |

### 5.4 Kenaikan Kelas Khusus (Admin/Bendahara → Ketua PKBM → Admin/Waka eksekusi)

Tujuan: siswa yang tidak memenuhi syarat (tunggakan/nilai) bisa diusulkan **izin naik kelas khusus** ke Ketua PKBM; eksekusi promosi tetap di Admin/Waka.

| Langkah | Role | Route name | Controller@method |
|---|---|---|---|
| Ajukan izin khusus | Admin | `admin.keuangan.promotion.validation.store` | `Admin\Keuangan\PromotionValidationController@store` |
| Ajukan izin khusus | Bendahara | `bendahara.promotion.validation.store` | `Bendahara\PromotionValidationController@store` |
| Approve/reject | Ketua PKBM | `ketua.promotion.approval.update` / `.bulk-update` | `Ketua\PromotionApprovalController@update` / `bulkUpdate` |
| Atur KKM/aturan | Admin / Waka | `admin.akademik.promotion.kkm.index` / `waka.promotion.kkm.index` | `Admin\Akademik\PromotionKKMController` (dipakai bersama) |
| Eksekusi kenaikan | Admin / Waka | `admin.akademik.promotion.execute` / `waka.promotion.execute` | `Admin\Akademik\PromotionReportController@execute` |

Penyimpanan: tabel **`izin_naik_kelas_khusus`** (diakses via `DB::table`, bukan Eloquent — status MENUNGGU/DISETUJUI/DITOLAK), model `StatusNaikKelasSiswa` (`status_kelulusan`, `izin_khusus_ketua`) & `PromotionSchedule`; pengaturan di tabel `pengaturan_kkm` dan `pengaturan_naik_kelas` (juga `DB::table`).

### 5.5 Pembelajaran LMS (Guru ↔ Siswa)

```
Guru (sidebar-lms, per kelas+mapel)                 Siswa (sidebar-lms, per mapel)
───────────────────────────────────                ──────────────────────────────
buat Materi      guru.lms.materi.store      ──────► lihat/unduh   siswa.lms.mapel.materi
buat Tugas       guru.lms.tugas.store       ──────► kerjakan      siswa.lms.mapel.tugas.submit
                                                      (TugasSiswa status dikerjakan|terlambat)
koreksi & nilai  guru.lms.tugas.koreksi.store ◄────  (notif ke guru)
                  TugasSiswa status=dinilai  ──────► lihat nilai & feedback
buat Ujian/Latihan + soal  guru.lms.ujian.*  ──────► kerjakan     siswa.lms.mapel.ujian.submit
Forum/Meeting    guru.lms.forum/meeting.*    ◄────►  siswa.lms.mapel.forum/meeting
```

Controller guru: `Guru\GuruMateriController`, `GuruTugasController`, `GuruKoreksiController`, `GuruUjianController` (juga melayani Latihan), `GuruNilaiController`, `GuruForumController`, `GuruLmsMeetingController`. Controller siswa: `Siswa\LmsMateriController`, `LmsTugasController`, `LmsUjianController`, `LmsForumController`. Model: `Materi`, `Tugas`/`TugasSiswa`, `Ujian`/`SoalUjian`/`UjianSiswa`/`JawabanSiswa`, `Nilai`, `ForumDiskusi`/`ForumReply`, `LmsMeeting`.

---

## 6. Indeks Detail Per-Role

| Role | File detail |
|---|---|
| Admin | [docs/flow/admin.md](docs/flow/admin.md) |
| Ketua PKBM | [docs/flow/ketua-pkbm.md](docs/flow/ketua-pkbm.md) |
| Wakil Kepala Sekolah | [docs/flow/wakil-kepala-sekolah.md](docs/flow/wakil-kepala-sekolah.md) |
| Sekretaris | [docs/flow/sekretaris.md](docs/flow/sekretaris.md) |
| Bendahara | [docs/flow/bendahara.md](docs/flow/bendahara.md) |
| Wali Kelas | [docs/flow/wali-kelas.md](docs/flow/wali-kelas.md) |
| Guru Pengajar (Sneat + LMS) | [docs/flow/guru.md](docs/flow/guru.md) |
| Siswa (SIA + LMS) | [docs/flow/siswa.md](docs/flow/siswa.md) |
| Orang Tua | [docs/flow/orang-tua.md](docs/flow/orang-tua.md) |

---

## 7. Catatan untuk AI Agent

1. **Mulai dari sini**, lalu buka `docs/flow/<role>.md` yang relevan dengan task. Jangan baca semua file role bila task hanya menyangkut satu role.
2. **Dokumen ini bisa basi.** Sebelum bertindak (mengubah kode / memberi rekomendasi), **verifikasi ke sumber**: nama route di `routes/web.php`, menu di `resources/views/<role>/partials/`, method di controller terkait. Jika ada beda, percayai kode, bukan dokumen — lalu perbarui dokumen.
3. **Saat menambah menu/fitur untuk satu role**, ikuti pola: tambah route di grup `role:<role>` yang sesuai → controller di `app/Http/Controllers/<Role>/` → view di `resources/views/<role>/` → item menu di sidebar role tersebut. Cek apakah role lain punya fitur serupa (lihat tabel "Menu Lintas-Role" di tiap file detail) agar konsisten.
4. **Hati-hati pada alur lintas-role (Bagian 5)**: mengubah satu sisi (mis. flag `siswa.validasi_rapor_*`) berdampak ke role lain. Telusuri seluruh rantai sebelum mengubah.
5. **Guru & Siswa punya 2 konteks** (Sneat/dashboard vs LMS/pembelajaran). Pastikan paham konteks mana yang dimaksud task.

---

## 8. Cara Memakai Dokumen Ini untuk Request Singkat

Tujuan bagian ini: supaya request pendek seperti "fix bug validasi rapor", "rapikan tampilan sidebar guru", atau "tambah tombol di pembayaran orang tua" bisa langsung dikerjakan tanpa pemilik project mengulang konteks besar aplikasi.

### 8.1 Asumsi default saat menerima task

- Anggap `flow.md` sebagai peta project dan `docs/flow/<role>.md` sebagai peta role.
- Jika request menyebut role/menu/fitur, buka file role terkait lebih dulu, lalu verifikasi ke kode.
- Jika request hanya menyebut URL atau nama route, mulai dari `routes/web.php`, lalu ikuti ke controller, view, sidebar, model, dan migration bila perlu.
- Jika request menyebut "tampilan", cek view Blade, partial sidebar/header, layout Sneat/LMS, asset CSS/JS di `resources/` dan `public/`.
- Jika request menyebut "bug data", cek controller, model/relasi, query scope/filter `tahun_ajaran_id`, `cabang_id`, `kelas_id`, dan middleware akses.
- Jika request menyentuh rapor, ujian, pembayaran, dispensasi, atau kenaikan kelas, baca Bagian 5 dulu karena hampir pasti lintas-role.
- Jangan menganggap dokumentasi selalu benar. Kode tetap sumber kebenaran terakhir.

### 8.2 Titik masuk cepat berdasarkan jenis request

| Jenis request | Mulai dari | Lalu cek |
|---|---|---|
| Bug route/404/menu tidak aktif | `routes/web.php` | sidebar role di `resources/views/<role>/partials/`, route name di view |
| Bug tampilan halaman | view di `resources/views/<role>/...` | layout terkait, partial, asset CSS/JS, data yang dikirim controller |
| Bug tombol/form tidak jalan | view form | route action, method HTTP, CSRF, validasi request, controller method |
| Bug data tidak muncul/salah filter | controller method | model relasi, `tahun_ajaran_id`, `cabang_id`, `kelas_id`, role/session aktif |
| Bug akses/redirect/login | middleware dan route group | `CheckRole`, `EnsureUserIsActive`, `CheckStudentActive`, `CheckLmsAccess`, `LoginController` |
| Fitur baru satu role | file role di `docs/flow/` | route group role, controller role, view role, sidebar role |
| Fitur mirror antar role | tabel "Menu Lintas-Role" di file role | controller shared/mirror, prefix route masing-masing role |
| Cetak PDF/import/export | controller fitur | package dompdf/excel, view cetak, template import, storage/public path |
| Notifikasi/badge sidebar | sidebar role | query badge, model status, controller aksi yang mengubah status |

### 8.3 Konvensi penting saat mengubah kode

- Pertahankan pola Laravel yang sudah ada: route bernama `<prefix>.<resource>.<action>`, controller per role, view per role.
- Untuk role Guru dan Siswa, pastikan konteksnya benar: dashboard/SIA atau LMS. Jangan mencampur sidebar dan layout.
- Untuk Wali Kelas, perhatikan session `wali_kelas_selected` saat fitur bergantung pada kelas aktif.
- Untuk Waka, cek batas cabang. Jangan membuka data lintas cabang tanpa alasan eksplisit.
- Untuk Admin, ingat banyak fitur adalah mirror atau super-user path. Jika mengubah fitur shared, cek dampaknya ke role mirror.
- Untuk Orang Tua, semua data anak wajib dibatasi lewat relasi kepemilikan anak. Jangan percaya parameter `{siswa}` begitu saja.
- Untuk Siswa, rapor dan pembayaran sengaja tidak menjadi menu utama. Arahkan alur ke Orang Tua kecuali kode existing memang masih menyediakan route lama.
- Setelah memperbaiki bug lintas-role, update `flow.md` atau `docs/flow/<role>.md` jika ditemukan fakta dokumentasi yang berbeda dari kode.

### 8.4 Kapan perlu bertanya ulang ke pemilik project

Agent boleh langsung jalan bila request sudah menyebut role, menu, URL, route, error, atau screenshot yang jelas. Tanya ulang hanya jika:

- task berisiko mengubah aturan bisnis penting dan tidak ada sumber kebenaran di kode/dokumen;
- ada dua interpretasi yang sama-sama masuk akal dan hasilnya akan berbeda jauh;
- perubahan menyentuh data produksi, pembayaran nyata, atau penghapusan data;
- dokumen dan kode bertentangan pada keputusan bisnis, bukan sekadar nama route/view.
