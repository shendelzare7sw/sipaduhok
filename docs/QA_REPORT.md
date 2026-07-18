# Laporan Quality Assurance — SIPADUHOK

**Tanggal:** 16 Juli 2026
**Cakupan:** Seluruh sistem (backend Laravel 11, 9 peran, LMS, Keuangan, Akademik, Kenaikan Kelas, Landing Page)
**Metode:** Pemeriksaan otomatis menyeluruh + audit manual terarah (gaya QA/keamanan)
**Branch:** `add-cloudflare`

> Referensi perancangan: `docs/flow/usecase-role-mapping.md`. File `.puml` di `docs/flow`
> hanya untuk laporan dan tidak termasuk cakupan pengujian sistem.

---

## 1. Ringkasan Eksekutif

Sistem lulus seluruh pemeriksaan otomatis QA: **tidak ditemukan syntax error, route
rusak, template Blade rusak, kegagalan build frontend, maupun test yang gagal.**
Semua temuan keamanan/integritas yang muncul selama audit **sudah diperbaiki dan
diverifikasi** (lihat §3). Suite pengujian otomatis **39 test, 231 assertion, 0 gagal.**

Catatan kejujuran metodologis: QA otomatis membuktikan tidak adanya **kelas kesalahan
tertentu** (sintaks, routing, build, regresi yang tercakup test). Ia **tidak** dapat
membuktikan secara mutlak nihilnya seluruh bug logika di setiap skenario runtime.
Karena itu §5 memberi rekomendasi UAT manual untuk alur kritikal.

---

## 2. Hasil Pemeriksaan Otomatis

| # | Pemeriksaan | Cakupan | Hasil |
|---|---|---|---|
| 1 | **PHP Lint** (`php -l`) | Semua file di `app/`, `database/`, `routes/`, `config/`, `bootstrap/` | ✅ **0 syntax error** |
| 2 | **Integritas Route** (`route:list`) | Seluruh route terdaftar | ✅ **798 route** resolve — tak ada controller/method hilang |
| 3 | **Kompilasi Blade** (`view:cache`) | Seluruh template `.blade.php` | ✅ semua tercompile — tak ada ParseError |
| 4 | **Build Frontend** (`npm run build`) | Bundling Vite + manifest | ✅ **exit 0** — tak ada aset hilang di manifest |
| 5 | **Test Suite** (`php artisan test`) | 39 test Feature/Unit | ✅ **39 passed, 231 assertions, 0 failed** |
| 6 | **Kebocoran `env()`** | `env()` dipanggil di luar `config/` | ✅ **0** — aman saat `config:cache` di produksi |
| 7 | **Kesiapan Cache Produksi** | `config:cache`, `route:cache`, `view:cache` | ✅ ketiganya sukses |
| 8 | **Sisa Debug** | `dd()`, `dump()`, `var_dump()`, `ray()` di `app/` | ✅ **0** |

---

## 3. Temuan Audit yang Sudah Diperbaiki (sepanjang sesi)

Pemeriksaan mendalam per-modul menemukan dan **memperbaiki** hal berikut:

### Integritas Data (relasi & hapus)
- **Guard hapus siswa/guru**: mencegah hard-delete akun yang masih punya jejak
  (nilai, rapor, tagihan, pembayaran, presensi, ujian, penugasan). Tanpa ini,
  FK cascade akan memusnahkan riwayat akademik & keuangan secara berantai.
  Berlaku untuk hapus tunggal **dan** bulk; menyarankan "nonaktifkan/ubah status".
  Diuji: `HapusHubDataGuardTest` (4 skenario).

### Notifikasi
- **Bug kritikal diperbaiki**: notifikasi ke Wali Siswa 100% tidak terkirim
  (`$parent->user_id` pada objek yang tak punya kolom itu → seharusnya `$parent->id`).
- Ditambah pemicu yang hilang: hasil kenaikan kelas, tagihan massal, pembayaran
  transfer, pengumuman/berita, penugasan guru, kelas virtual, absensi alpha.
  Diuji: `NotificationParentAndKenaikanTest`, `NotificationMoreTriggersTest`.

### Keamanan (IDOR/XSS/upload)
- **IDOR**: audit menyeluruh sisi Siswa–Guru–Wali Kelas (nilai, rapor, presensi,
  template, validasi akses, ujian) — semua ter-scope benar. Diuji: `Wali*IdorTest`.
- **Stored XSS**: SVG dikeluarkan dari allowlist preview inline (`FileController`).
- **Validasi upload**: format materi wajib cocok tipe yang dipilih; whitelist tugas.
  Diuji: `GuruMateriUploadTypeTest`.

### Keuangan
- **Kelayakan kenaikan**: status `cicilan` dihitung sebagai belum lunas (adil).
  Diuji: `PromotionSystemTest` (checkEligibility: lunas+tuntas→layak; menunggak→tidak).
- Filter tunggakan alumni (Bendahara + Admin).

### Keamanan Login (Cloudflare)
- Widget **Cloudflare Turnstile** di form login + verifikasi server-side eksplisit
  (anti-bypass) di `LoginRequest::authenticate()`.

### Kebersihan Kode
- Fitur **Google Sheets Sync** dihapus total (tak dipakai) — controller, job, model,
  service, config, views, route, scheduler, tabel, package composer, referensi chatbot.
  App tetap boot bersih, `route:list` bersih.

---

## 4. Verifikasi Titik Rawan (tidak ada regresi)

- Pola `->user_id` yang tersisa di `NotificationService` (baris ~212) **benar** —
  di sana objeknya `ForumReply` (memang punya kolom `user_id`), bukan objek User.
- Migration drop tabel memakai `dropIfExists` (aman di DB fresh maupun lama).

---

## 5. Rekomendasi UAT Manual sebelum Sidang

Automated QA sudah menutup kelas error teknis. Untuk keyakinan penuh, jalankan
uji-terima manual (blackbox) pada alur kritikal berikut, login sesuai peran:

1. **Admin** → Kelola User: hapus siswa/guru ber-jejak (harus ditolak + saran),
   ubah status siswa (Lulus/Keluar) & nonaktifkan akun guru → berhasil.
2. **Bendahara** → generate tagihan massal → cek notifikasi masuk ke Wali Siswa.
3. **Wali Siswa** → bayar (transfer/Midtrans) → Bendahara validasi → status tagihan
   ter-update.
4. **Guru** → upload materi (uji format salah ditolak), buat tugas/ujian, input nilai
   (uji koma/titik desimal), kelas virtual → siswa menerima notifikasi.
5. **Siswa** → kerjakan ujian/tugas, lihat nilai/rapor (uji tak bisa akses kelas lain).
6. **Wali Kelas** → kelola presensi, rapor, template capaian (uji scope kelas sendiri).
7. **Ketua/Waka** → proses kenaikan kelas → cek kelayakan & notifikasi hasil.

---

## 6. Kesimpulan

Berdasarkan pemeriksaan otomatis menyeluruh dan audit manual terarah, **tidak ditemukan
error teknis maupun bug pada kelas-kelas yang diperiksa**, dan seluruh temuan
keamanan/integritas telah diperbaiki serta ditutup dengan pengujian regresi. Sistem
berada dalam kondisi **stabil dan siap didemonstrasikan**.

*Disusun sebagai bagian dari proses QA pra-sidang. Semua perubahan ter-commit di
branch `add-cloudflare`.*
