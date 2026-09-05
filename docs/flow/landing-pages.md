# Landing Pages & Authentication

> Kembali ke [flow.md](../../flow.md) · **Tanpa role/middleware** (akses publik) kecuali bagian Auth Setup. Halaman-halaman ini adalah **wajah publik** PKBM House Of Knowledge — yang dilihat pengunjung sebelum login.

## Ringkasan

Tiga kelompok halaman publik:

1. **Landing Pages** (17 view di `resources/views/*.blade.php`) — halaman informasi sekolah, dikelola oleh **Admin** lewat menu [Landing Page](admin.md#landing-page) (`admin.landing-pages.*`) dengan editor konten section per halaman. Content disimpan di tabel `LandingPage` + `LandingPageSection`.
2. **Berita** (`berita.blade.php`) — halaman publik daftar berita; **datanya dikelola dari menu Akademik → Berita** (Admin via [admin.akademik.berita.*](admin.md#akademik--berita) atau Sekretaris via [sekretaris.berita.*](sekretaris.md#kelola-berita)). Tidak masuk editor `admin/landing-pages/`.
3. **Authentication** — login, lupa password/akun, admin recovery, admin security setup. Ada di `resources/views/auth/*.blade.php`.

---

## A. Landing Pages (Konten Sekolah)

**Editor admin**: [admin/landing-pages/index.blade.php](../../resources/views/admin/landing-pages/index.blade.php) — tabel `LandingPage` (di-seed via `LandingPageSeeder`) dengan kolom slug, title, last_updated. Tombol **Edit Konten** per row → form editor section. **Controller publik**: `App\Http\Controllers\LandingPageController.php` (semua method me-fetch `LandingPage` by slug + render view dengan data `$page`).

### Tabel Halaman

| URL Path | View | Route Name | Controller@method | Logika |
|---|---|---|---|---|
| `/` | `home.blade.php` | `home` | `LandingPageController@home` | Fetch `LandingPage::where('slug', 'home')` + 6 berita terbaru (`$beritaList`) untuk **carousel berita** di section khusus. |
| `/tentang-sekolah` | `tentang-sekolah.blade.php` | (tanpa name) | `@tentangSekolah` | Render konten section dari `LandingPage::where('slug', 'tentang-sekolah')`. |
| `/visi-misi` | `visi-misi.blade.php` | (tanpa name) | `@visiMisi` | Render konten visi-misi. |
| `/struktur-organisasi` | `struktur-organisasi.blade.php` | (tanpa name) | `@strukturOrganisasi` | Render konten struktur. |
| `/profil-guru` | `profil-guru.blade.php` | (tanpa name) | `@profilGuru` | Render konten profil guru (foto, nama, NIP, mapel). |
| `/program-paud-tk` | `program-paud-tk.blade.php` | (tanpa name) | `@programPaudTk` | Render konten program PAUD/TK. |
| `/program-sd-sma` | `program-sd-sma.blade.php` | (tanpa name) | `@programSdSma` | Render konten program SD/SMA. |
| `/program-inklusi` | `program-inklusi.blade.php` | (tanpa name) | `@programInklusi` | Render konten program inklusi. |
| `/program-terapi` | `program-terapi.blade.php` | (tanpa name) | `@programTerapi` | Render konten program terapi. |
| `/fasilitas` | `fasilitas.blade.php` | `fasilitas` | `@fasilitas` | Render konten fasilitas (grid gambar+caption). |
| `/ppdb` | `ppdb.blade.php` | `ppdb` | `@ppdb` | Render konten PPDB (alur pendaftaran, syarat, biaya). |
| `/galeri` | `galeri.blade.php` | `galeri` | `@galeri` | Render gallery foto kegiatan. |
| `/kontak` | `kontak.blade.php` | `kontak` | `@kontak` | Info kontak (alamat, telp, email, map). |
| `/sitemap.xml` | `sitemap.blade.php` | `sitemap` | `SitemapController@index` | Render XML sitemap untuk SEO (bukan view HTML — di-render `view('sitemap')->render()` lalu wrap response XML). |

### Halaman Orphan (view ada, route tidak)

| View | Status |
|---|---|
| `legalitas.blade.php` | **Tidak ter-route** — tidak ada method `legalitas()` di `LandingPageController` dan tidak ada `Route::get('/legalitas', ...)` di `routes/web.php`. View ada di disk tapi tidak bisa diakses. Bisa jadi: (a) WIP belum di-wire, atau (b) deprecated tapi file belum dihapus. |
| `program-homeschooling.blade.php` | **Tidak ter-route** — sama kasusnya, tidak ada method `programHomeschooling()` & tidak ada route. View orphan. |

### Halaman Berita (data dari menu akademik)

| URL Path | View | Route Name | Controller@method | Logika |
|---|---|---|---|---|
| `/berita` | `berita.blade.php` | `berita` | `BeritaController@index` | Daftar berita publik (paginated) + filter kategori + search. Berita unggulan (`is_featured=true`) tampil di **hero section** atas (`$beritaUtama`); sisanya di grid (`$beritaList`). |

**Catatan tentang Berita**:

- **Berita TIDAK dikelola lewat editor `admin/landing-pages/`** — data berita ada di tabel `Berita` dan dikelola lewat 2 menu yang berbeda:
  - **Admin**: [admin.akademik.berita.*](admin.md#akademik--berita) (controller `Admin\Akademik\AkademikController`, view di `admin/akademik/berita/`)
  - **Sekretaris**: [sekretaris.berita.*](sekretaris.md#kelola-berita) (controller `Sekretaris\SekretarisController`, view di `sekretaris/berita/`)
  - Kedua role menulis ke tabel `Berita` yang sama — berita yang di-publish dari salah satu role langsung muncul di `/berita` publik.
- **Carousel Berita di `home.blade.php`**: section dengan `id="carouselDots"` menampilkan 6 berita terbaru (`@foreach($beritaList as $index => $berita)` di line ~338) sebagai sliding carousel. Data di-pass dari `LandingPageController@home` sebagai `$beritaList`. Newsticker JS juga mem-feed judul+kategori berita ke marquee di hero (`newsData: @json(...)` di line 546). **Jadi mengubah/menambah berita di Admin Akademik atau Sekretaris langsung terlihat di carousel homepage.**
- **Toggle Featured** (`admin.akademik.berita.toggle-featured` / `sekretaris.berita.toggle-featured`) menentukan apakah berita masuk hero `$beritaUtama` di halaman `/berita`.

---

## B. Authentication (Login, Recovery, Setup)

**Controller dir**: `app/Http/Controllers/Auth/` — 4 controller terpisah untuk login + recovery flow. **View dir**: `resources/views/auth/`. Semua route guest-only (kecuali admin security setup yang auth-only).

### Login

**Index view**: `auth/login.blade.php` · **Controller**: `Auth/LoginController.php` · **URL**: `/login` · **Middleware**: `guest`

**Tampilan index**: Layout 2-kolom (grid Tailwind, responsive). **Kolom kiri** (hidden di mobile): banner gradient biru dengan logo HOK + judul "Selamat Datang!" + sub "PKBM House Of Knowledge". **Kolom kanan**: form login dengan field **Username/Email** (input text, icon envelope), **Password** (input password, toggle eye show/hide JS), **CAPTCHA** (gambar 44×11 dari `CaptchaController@generate` + tombol refresh + input kode). Checkbox **Ingat saya** + link **Lupa Akun / Password?** → `user.recovery`. Tombol **Masuk ke Dashboard** (gradient primary, gradient hover ke orange). Footer: tombol "Hubungi Administrator" → `wa.me/{adminWa}` (nomor diambil dari `AppSetting.admin_wa_number` yang di-set Admin di [Tiket Pemulihan Akun](admin.md#tiket-pemulihan-akun)). Link "Kembali ke Beranda" → `/`.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Submit Login | `login` | POST | `LoginController@store` | redirect | Validasi via `LoginRequest` (cek captcha, login by username OR email, throttle 5 attempt). Setelah authenticate: regenerate session, update `last_login_at`+`last_login_ip`, lalu **redirect berdasar role** ke dashboard masing-masing (admin → `admin.dashboard`, ketua_pkbm → `ketua.dashboard`, sekretaris → `sekretaris.dashboard`, bendahara → `bendahara.dashboard`, wali_kelas → `wali.dashboard`, guru_pengajar → `guru.dashboard`, orang_tua → `orang-tua.dashboard`, siswa → `siswa.dashboard`). |
| Logout (POST) | `logout` | POST | `LoginController@destroy` | redirect ke `/` | Hapus session + redirect. Hanya muncul saat user sudah login (middleware `auth`). |
| Refresh CAPTCHA | `captcha.refresh` | GET | `CaptchaController@refresh` | JSON | Generate ulang CAPTCHA via AJAX (tombol refresh di samping gambar). |
| (Image source CAPTCHA) | `captcha` | GET | `CaptchaController@generate` | PNG response | Stream gambar CAPTCHA 6-character dengan distorsi. |
| Lupa Akun / Password | `user.recovery` | GET | `Auth/UserRecoveryController@index` | `auth/user-recovery.blade.php` | Buka form pemulihan akun (lihat sub-section di bawah). |
| Hubungi Administrator (link) | `https://wa.me/{adminWa}` | — | (link external) | — | Buka chat WhatsApp ke nomor admin (untuk user yang tidak bisa recovery mandiri, mis. email & telepon belum terdaftar). Nomor admin dikelola di [Tiket Pemulihan Akun](admin.md#tiket-pemulihan-akun). |
| Kembali ke Beranda | `/` | GET | `LandingPageController@home` | `home.blade.php` | Cross-link ke landing page. |

---

### Pemulihan Akun User (Lupa Password / Username)

**Index view**: `auth/user-recovery.blade.php` · **Controller**: `Auth/UserRecoveryController.php` · **URL**: `/recovery` · **Middleware**: `guest`

**Tampilan index**: Card terpusat dengan icon kunci + heading "Pemulihan Akun" + deskripsi "Sistem akan membantu memulihkan akses Anda otomatis via Email". Tombol back "Kembali ke Login". Form berisi: dropdown **Apa kendala?** (3 opsi: Saya lupa Password / Saya lupa Username/Email / Saya lupa Keduanya), input **Masukkan Identitas** (placeholder berubah by JS sesuai opsi yang dipilih — mis. masukkan email/NISN/NIP), dan field tambahan kondisional (mis. nomor WA untuk konfirmasi). Tombol Submit.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Submit Pemulihan | `user.recovery.store` | POST | `@store` | redirect | Validasi identitas + tipe_recovery. Buat `RecoveryTicket` (status `pending_admin`) atau (bila email valid & user ditemukan) kirim **link reset password** ke email via `password.reset.ticket`. Throttle 5/menit. |
| Kembali ke Login | `login` | GET | `LoginController@create` | `auth/login.blade.php` | Back. |

**Catatan**: Tiket recovery yang dibuat masuk ke antrian **Admin** di [Tiket Pemulihan Akun](admin.md#tiket-pemulihan-akun). Admin bisa **resend WhatsApp link**, **resolve** (tandai selesai), atau **reject**. User akan menerima notifikasi via WA bila admin bertindak.

---

### Reset Password via Link Tiket

**View**: `auth/reset-password-ticket.blade.php` · **Route**: inline closure di `routes/web.php:149` (bukan controller terpisah) · **URL**: `/recovery/reset/{token}` · **Middleware**: `guest`

**Tampilan**: Form reset password sederhana — password baru + konfirmasi password. Link diakses user lewat **email/WhatsApp** yang dikirim setelah admin/sistem approve recovery ticket.

| Tombol/Aksi | Route name | HTTP | Logika ringkas |
|---|---|---|---|
| Submit Reset | `password.reset.ticket.submit` | POST | Inline closure (line 162-187): validasi token + password (min 8, confirmed). Cek `RecoveryTicket.token_reset` masih valid (status bukan resolved/rejected/expired, `expires_at > now()`). Update `User.password` + set ticket `resolved`, hapus `token_reset`. Throttle 5/menit. |

**Catatan**: GET URL `/recovery/reset/{token}` (line 149-160) cek validitas token; bila invalid/expired → redirect login dengan error. Bila valid → render form. Implementasi sebagai closure langsung di routes, **bukan controller method** — agar self-contained sebagai tiket recovery, terpisah dari Laravel's default `Password::reset` flow.

---

### Admin Recovery (Reset Account Admin via Email)

**Index view**: `auth/admin-recovery.blade.php` · **Controller**: `Auth/AdminRecoveryController.php` · **URL**: `/admin-recovery` · **Middleware**: `guest`

**Tampilan**: Form khusus untuk admin yang lupa password (alur terpisah dari user biasa — karena admin tidak punya orang tua/wali untuk dimintakan tolong; admin reset sendiri lewat email yang sudah pre-set di security-setup). Field: email admin + tombol kirim link reset.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Kirim Link Reset | `admin.recovery.reset` | POST | `@reset` | redirect | Validasi email = admin terdaftar. Generate token reset + kirim ke email admin. Throttle 5/menit. |
| (Unlock account, alternatif) | `admin.recovery.unlock` | POST | `@unlock` | redirect | Endpoint khusus untuk unlock akun admin yang terkunci (mis. setelah 5x salah password). Butuh kode verifikasi (security question / OTP). |

**Catatan**: Berbeda dengan `user.recovery` (yang lewat antrian admin), `admin.recovery` adalah **self-service untuk admin** karena tidak ada peran lebih tinggi yang bisa approve. Email & security question admin di-setup wajib lewat `admin.security.setup` (lihat sub-section berikutnya).

---

### Admin Security Setup (Wajib Setelah Login Pertama)

**Index view**: `auth/admin-security-setup.blade.php` · **Controller**: `Auth/AdminSecuritySetupController.php` · **URL**: `/admin/security-setup` · **Middleware**: `auth` (bukan guest — user harus login dulu)

**Tampilan**: Form setup security untuk admin baru: email recovery (yang akan dipakai untuk reset password lewat `admin.recovery`), nomor WhatsApp admin, security question + answer. Form muncul **otomatis (forced)** setelah login admin pertama bila `User.security_setup_completed != true`. Tidak bisa di-skip — admin diblokir akses dashboard sampai setup selesai.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Simpan Setup | `admin.security.setup.store` | POST | `@store` | redirect ke `admin.dashboard` | Validasi + simpan email recovery, WA, security question/answer (hash). Set `User.security_setup_completed = true`. |

**Catatan**: Middleware/check khusus di `LoginController` atau global middleware mendeteksi admin yang belum setup → redirect paksa ke halaman ini. **Routes untuk admin lain** (yang sudah setup) tidak terganggu.

---

## Alur Singkat Pemulihan Akun

```
User biasa (siswa/guru/orang-tua/dst.) lupa password:
  /login → klik "Lupa Akun / Password?"
    → /recovery (form pilih tipe + identitas)
      → POST /recovery → RecoveryTicket dibuat (status pending_admin)
        → muncul di Admin /admin/recovery-tickets
          → Admin "Resolve" + kirim link reset via WA
            → User klik link → /recovery/reset/{token} → form password baru
              → POST → password updated, ticket resolved
                → redirect /login dengan pesan sukses

Admin lupa password (self-service):
  /login → /admin-recovery (form email)
    → POST /admin-recovery → token + link dikirim ke email admin
      → klik link → form reset → password baru → /login
```

---

## Catatan Penting

- **Captcha** wajib di login (validasi via `CaptchaController` + session). Bila salah 3x dalam 1 menit, throttle aktif (5 attempt total).
- **`captcha.refresh`** dipanggil via JS tombol refresh — tidak full reload halaman.
- **Throttle** `5,1` (5 percobaan per menit) ada di: login, recovery store, admin recovery reset, password reset submit — untuk anti-brute-force.
- **Halaman publik** (`/`, `/berita`, `/galeri`, `/fasilitas`, dll.) **tidak butuh login** — siapapun bisa akses, termasuk search engine (lihat `/sitemap.xml`).
- **Editor Landing Page** ([admin.landing-pages](admin.md#landing-page)): Admin **tidak bisa membuat halaman baru** dari UI — daftar halaman pre-seeded via `LandingPageSeeder`. Untuk menambah halaman baru perlu: (a) tambah row di seeder/migration, (b) tambah view blade di `resources/views/{slug}.blade.php`, (c) tambah method di `LandingPageController` + route di `routes/web.php`. View orphan `legalitas.blade.php` & `program-homeschooling.blade.php` adalah contoh halaman yang siap dipakai tapi belum dihubungkan.
- **Berita di halaman publik** dipengaruhi 2 menu admin (akademik berita + sekretaris berita) — single source of truth = tabel `Berita`. Tidak ada conflict resolution bila Admin dan Sekretaris edit berita yang sama bersamaan (last-write-wins).
