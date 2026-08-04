# PANDUAN SIDANG 2 — PETA CODEBASE PER SUBSISTEM

> **Dokumen pendamping** dari [PANDUAN_LATIHAN_SIDANG.md](PANDUAN_LATIHAN_SIDANG.md).
>
> | Dokumen | Isinya | Dipakai untuk |
> |---|---|---|
> | **Panduan 1** | Metode + 1 contoh mendalam (fitur Pengumuman) | Belajar **cara kerja**: melacak file, mengubah kode |
> | **Panduan 2** (ini) | Peta seluruh subsistem + alur bisnis + Q&A | Menjawab **"di mana X?"** dan **"jelaskan alur Y"** |
>
> **Urutan belajar:** kuasai Panduan 1 dulu (metodenya), baru pakai dokumen ini
> sebagai kamus. Jangan dihafal habis — yang penting **tahu harus buka halaman mana**.
>
> Semua nomor baris di bawah **diambil dari kode branch ini**, bukan contoh karangan.
> Nomor baris bisa bergeser kalau file diedit — selalu sertakan **nama method** saat
> mencari (`Ctrl+F`), jangan mengandalkan nomor saja.

---

## DAFTAR ISI

- [BAGIAN A — Peta 60 detik: semua route ada di baris berapa](#bagian-a--peta-60-detik-semua-route-ada-di-baris-berapa)
- [BAGIAN B — SUBSISTEM 1: Autentikasi & Role](#bagian-b--subsistem-1-autentikasi--role)
- [BAGIAN C — SUBSISTEM 2: Keuangan](#bagian-c--subsistem-2-keuangan)
- [BAGIAN D — SUBSISTEM 3: Akademik (SIA)](#bagian-d--subsistem-3-akademik-sia)
- [BAGIAN E — SUBSISTEM 4: LMS](#bagian-e--subsistem-4-lms)
- [BAGIAN F — Jembatan antar subsistem (jawaban "wow")](#bagian-f--jembatan-antar-subsistem-jawaban-wow)
- [BAGIAN G — 30 pertanyaan cepat + jawaban](#bagian-g--30-pertanyaan-cepat--jawaban)

---

## BAGIAN A — Peta 60 detik: semua route ada di baris berapa

Seluruh route ada di **satu file**: [routes/web.php](../routes/web.php) (~1.600 baris).
Tabel ini membuat kalian bisa lompat langsung tanpa scroll buta.

| Grup | Baris | Middleware & prefix |
|---|---|---|
| Landing page publik | 1 – 127 | tanpa login |
| **Webhook Midtrans** | **88** | tanpa login (dikecualikan CSRF) |
| Login / Logout | 134, 135, 191 | `guest` / `auth` |
| Semua route login | 210 (pembuka) | `auth` |
| Admin | 221 & 318 | `role:admin` · `/admin` |
| Ketua PKBM | 682 | `role:ketua_pkbm` · `/ketua` |
| Wakil Kepala Sekolah | 755 | `role:wakil_kepala_sekolah` · `/waka` |
| Sekretaris | 915 | `role:sekretaris` · `/sekretaris` |
| **Bendahara** | **969** | `role:bendahara` · `/bendahara` |
| **Wali Kelas** | **1090** | `role:wali_kelas` · `/wali` |
| **Guru Pengajar** | **1218** | `role:guru_pengajar` · `/guru` |
| **Siswa** | **1405** | `role:siswa`, `student.active` · `/siswa` |
| **Wali Siswa (orang tua)** | **1542** | `role:orang_tua` · `/wali-siswa` |
| Pengaturan akun (semua role) | 1582+ | `auth` |

> **Trik cepat saat sidang:** buka `routes/web.php`, tekan `Ctrl+G`, ketik nomor baris
> dari tabel di atas. Langsung sampai.

### Urutan route — jebakan yang ADA di kode ini

Lihat [routes/web.php:993-1007](../routes/web.php#L993-L1007). Route spesifik
(`/generate-spp`, `/carryover`, `/api/...`) sengaja ditulis **di atas** route
bervariabel `/{siswa}` (baris 1003).

Kalau `/{siswa}` ditaruh lebih dulu, Laravel akan menganggap kata `"carryover"`
sebagai nilai `{siswa}` → fitur carryover rusak.
👉 **Ini contoh nyata di project kalian sendiri.** Kalau penguji bertanya soal urutan
route, tunjuk baris ini. Jawaban yang menunjuk kode asli jauh lebih kuat daripada teori.

---

## BAGIAN B — SUBSISTEM 1: Autentikasi & Role

Subsistem paling mungkin ditanya karena jadi fondasi semua yang lain.

### Peta file

| Peran | File | Baris penting |
|---|---|---|
| Route login/logout | [routes/web.php](../routes/web.php) | 134 (form), 135 (proses), 191 (logout) |
| Controller login | `app/Http/Controllers/Auth/LoginController.php` | `create()`, `store()`, `destroy()` |
| **Cek hak akses** | [app/Http/Middleware/CheckRole.php](../app/Http/Middleware/CheckRole.php) | seluruh file (55 baris) |
| Model user | [app/Models/User.php](../app/Models/User.php) | `roleRelation()` 63, `isAdmin()` 116 |
| Model role | [app/Models/Role.php](../app/Models/Role.php) | punya kolom `level` |
| Daftar middleware | [bootstrap/app.php](../bootstrap/app.php) | alias `role`, `superadmin`, dll |

### Alur login (hafalkan, sering ditanya)

```
[1] Pengguna buka /login
       → routes/web.php:134 → LoginController@create → tampil form
[2] Submit email + password (+ captcha Turnstile)
       → routes/web.php:135 → LoginController@store
[3] Kredensial dicek ke tabel users (password ter-hash bcrypt)
[4] Berhasil → session dibuat → diarahkan ke dashboard sesuai ROLE
[5] Buka halaman ber-role → dicegat CheckRole
       → admin? lolos semua (CheckRole.php:28)
       → bukan admin? role harus cocok, kalau tidak → 403 (CheckRole.php:50)
```

### Cara sistem role bekerja (ini nilai jual skripsi kalian)

Ada **dua sistem role yang hidup berdampingan** —
lihat [CheckRole.php:33-42](../app/Http/Middleware/CheckRole.php#L33-L42):

```php
// Sistem BARU: users.role_id → tabel roles (punya kolom level)
if ($user->role_id && $user->roleRelation) {
    $userRole = $user->roleRelation->name;
}
// Sistem LAMA (fallback): kolom enum users.role
elseif ($user->role) {
    $userRole = $user->role;
}
```

**Kenapa ada dua?** Sistem lama memakai kolom enum `users.role`. Saat ditambah peran
baru, enum harus diubah lewat migration setiap kali — tidak fleksibel. Sistem baru
memakai tabel `roles` tersendiri sehingga peran bisa ditambah **tanpa mengubah struktur
tabel**, dan punya `level` untuk hierarki. Fallback dipertahankan supaya **data user
lama tetap bisa login** selama masa transisi.

👉 *Ini jawaban yang membuat penguji mengangguk. Hafalkan alasannya, bukan kodenya.*

### Dua keputusan desain yang WAJIB bisa kalian bela

**1. Admin bypass semua role** — [CheckRole.php:28-30](../app/Http/Middleware/CheckRole.php#L28-L30)
```php
if ($user->isAdmin()) {
    return $next($request);
}
```
> **Alasan:** admin adalah level 1 (tertinggi) dan bertugas menangani seluruh sistem
> saat ada masalah di peran manapun. Tanpa bypass, admin harus didaftarkan ulang ke
> setiap grup route — rawan ada yang terlewat dan justru menimbulkan celah.

**2. Gagal otorisasi TIDAK me-logout pengguna** — [CheckRole.php:46-50](../app/Http/Middleware/CheckRole.php#L46-L50)
> **Alasan (ada di komentar kodenya):** 403 adalah kegagalan **otorisasi**, bukan
> **autentikasi**. Pengguna tetap dikenal dan tetap login — hanya halaman itu yang
> ditolak. Versi lama sempat auto-logout, tapi itu membuang pekerjaan pengguna dan bisa
> jadi jebakan: cukup kirim satu link terlarang untuk memaksa orang lain keluar.
> Pesannya juga dibuat generik (tanpa menyebut nama role) agar tidak membocorkan
> struktur hak akses.
>
> 👉 *Kalau bisa menjelaskan ini, kalian terlihat benar-benar memahami keamanan —
> bukan sekadar menyalin kode.*

### Q&A khas subsistem ini

**"Password disimpan bagaimana?"**
> Di-*hash* dengan bcrypt oleh Laravel, tidak pernah disimpan sebagai teks asli. Hash
> satu arah — tidak bisa dibalik. Saat login, password yang diketik di-hash lagi lalu
> dibandingkan hash-nya.

**"Bedanya autentikasi dan otorisasi?"**
> Autentikasi = membuktikan **siapa kamu** (login). Otorisasi = menentukan **kamu boleh
> apa** (middleware role). Di sistem ini: autentikasi oleh `auth`, otorisasi oleh `role:`.

**"Kalau siswa mengetik URL /bendahara/tagihan langsung, apa yang terjadi?"**
> Ditolak `CheckRole` dengan 403. Bukan disembunyikan di menu saja — proteksinya di
> level route, jadi mengetik URL manual tetap tidak bisa tembus.
> 👉 *Kalau ada waktu, PRAKTIKKAN ini di depan penguji. Sangat meyakinkan.*

**"Middleware apa saja yang dipakai?"**
> `role:<nama>` (hak akses per peran), `superadmin`, `role.level` (hierarki),
> `guest`, `lms.access` (gerbang LMS), `siswa.mapel.access`, `student.active`
> (blokir siswa nonaktif). Terdaftar di `bootstrap/app.php`.

### Skenario latihan

**B-1 (mudah, 8 menit)** — *"Tambahkan pesan berbeda saat siswa nonaktif mencoba login."*
Lokasi: `EnsureUserIsActive` / `CheckStudentActive` di `app/Http/Middleware/`.
Langkah: ubah pesan `abort()` atau redirect-nya. Cek juga view error terkait di
`resources/views/errors/account-inactive.blade.php` (aset CSS/JS-nya sudah terdaftar
di [vite.config.js:25-26](../vite.config.js#L25-L26)).

**B-2 (sedang, 15 menit)** — *"Buat agar Ketua PKBM juga bisa membuka halaman daftar
tagihan bendahara."*
Langkah: di [routes/web.php:976](../routes/web.php#L976), route itu ada dalam grup
`role:bendahara`. `CheckRole` menerima **banyak role** (`string ...$roles`), jadi
solusinya memindahkan route tersebut ke grup dengan `role:bendahara,ketua_pkbm`, atau
membuat route terpisah untuk ketua.
**Kemana lagi:** menu sidebar ketua (`resources/views/ketua/partials/sneat-sidebar-menu.blade.php`)
harus ditambah link-nya, kalau tidak fiturnya ada tapi tak terlihat.

---

## BAGIAN C — SUBSISTEM 2: Keuangan

Subsistem paling kompleks — dan paling sering digali penguji karena menyangkut uang.

### Peta file

| Bagian | File | Baris penting |
|---|---|---|
| Route bendahara | [routes/web.php](../routes/web.php) | grup 969; tagihan 975-1008; pembayaran 1011-1026 |
| Route wali siswa | [routes/web.php](../routes/web.php) | grup 1542; tagihan 1548-1552; Midtrans 1555-1560 |
| **Model Tagihan** | [app/Models/Tagihan.php](../app/Models/Tagihan.php) | relasi 34-63; `scopeBelumLunasOriginal()` 69; `sisa_pembayaran` 89; **`updateStatusBayar()` 103**; `getLabelJenis()` 140 |
| **Model Pembayaran** | [app/Models/Pembayaran.php](../app/Models/Pembayaran.php) | `tagihan()` 42, `siswa()` 47, `validator()` 52 |
| Controller tagihan | [Bendahara/TagihanController.php](../app/Http/Controllers/Bendahara/TagihanController.php) | `index()` 43; `bulkCreate()` 528; `generateSpp()` 873; `duplicate()` 1069; carryover 1195-1242 |
| Controller pembayaran | [Bendahara/PembayaranController.php](../app/Http/Controllers/Bendahara/PembayaranController.php) | `index()` 22; `show()` 151; **`validasi()` 164**; `create()` 299; `store()` 358; `cetakKwitansi()` 576 |
| **Midtrans (service)** | [app/Services/MidtransService.php](../app/Services/MidtransService.php) | `createSnapToken()` 61; `buildTransactionParams()` 125; **`verifySignature()` 171**; `mapTransactionStatus()` 204 |
| **Webhook** | [MidtransWebhookController.php](../app/Http/Controllers/MidtransWebhookController.php) | `notification()` 23; cek tanda tangan 42; update 71; audit 81 |
| Carryover tunggakan | [TunggakanCarryoverService.php](../app/Services/TunggakanCarryoverService.php) | `getKandidatTunggakan()` 30; `previewCarryover()` 92; `executeCarryover()` 123 |
| Sisi wali siswa | [OrangTua/OrangTuaController.php](../app/Http/Controllers/OrangTua/OrangTuaController.php) | `tagihanAnak()`, `prosesBayar()`, `snapPayment()` |
| Audit | `app/Models/FinancialAuditLog.php` | dipakai webhook baris 81 |

### Konsep dasar: Tagihan vs Pembayaran

> **Satu Tagihan bisa punya BANYAK Pembayaran** (karena boleh dicicil).
> Status tagihan **tidak diketik manual** — dihitung ulang dari total pembayaran
> yang sudah disetujui.

- **Jenis tagihan**: `spp` (+ akhiran bulan, mis. `spp_juli`), `uang_pangkal`,
  `uang_pendaftaran`, `kegiatan`, `buku`, `seragam`, `ujian`, `akm`, `rapor_foto`.
- **Status tagihan**: `belum_bayar` → `cicilan` → `sudah_bayar` (atau `terlambat`).
- **Status pembayaran** (`status_validasi`): `menunggu` → `disetujui` / `ditolak`.

### ⭐ Method terpenting di seluruh subsistem keuangan

[Tagihan.php:103 `updateStatusBayar()`](../app/Models/Tagihan.php#L103) — **hafalkan logikanya:**

```php
$totalDibayar = $this->pembayaran()
    ->where('status_validasi', 'disetujui')   // ← HANYA yang disetujui dihitung
    ->sum('jumlah_bayar');

if ($totalDibayar >= $this->jumlah)  → status 'sudah_bayar'
elseif ($totalDibayar > 0)           → status 'cicilan'
else                                 → status 'belum_bayar'
```

**Tiga hal yang harus bisa kalian jelaskan dari method ini:**

1. **Kenapa hanya `disetujui` yang dihitung?** Karena pembayaran manual (transfer +
   unggah bukti) harus diverifikasi bendahara dulu. Kalau yang berstatus `menunggu`
   ikut dihitung, siswa bisa dianggap lunas hanya dengan mengunggah bukti palsu.
2. **Kenapa status dihitung ulang, bukan disimpan manual?** Supaya tidak pernah ada
   selisih antara status dan data pembayaran sebenarnya — satu sumber kebenaran.
3. **Baris 108:** kalau `jumlah == 0`, langsung `sudah_bayar`. Ini menangani kasus
   tagihan gratis / dibebaskan, supaya tidak nyangkut selamanya di `belum_bayar`.

### ALUR 1 — Pembayaran manual (transfer + verifikasi bendahara)

```
[1] Bendahara membuat tagihan
    /bendahara/tagihan/bulk-create atau generate-spp
    → TagihanController@bulkCreate (528) / @generateSpp (873)
    → baris tagihan tersimpan, status 'belum_bayar'
        ↓
[2] Wali siswa melihat tagihan anaknya
    /wali-siswa/tagihan/anak/{siswa}  (routes:1549)
    → OrangTuaController@tagihanAnak
        ↓
[3] Wali transfer manual, lalu bendahara mencatat + unggah bukti
    /bendahara/pembayaran/siswa/{siswa}/create  (routes:1017)
    → PembayaranController@create (299) → @store (358)
    → Pembayaran tersimpan, status_validasi 'menunggu'
        ↓
[4] Bendahara memverifikasi bukti
    POST /bendahara/pembayaran/{id}/validasi  (routes:1014)
    → PembayaranController@validasi (164)
    → status_validasi jadi 'disetujui'
        ↓
[5] Tagihan::updateStatusBayar() dipanggil (Tagihan.php:103)
    → hitung ulang total pembayaran disetujui
    → status tagihan jadi 'cicilan' atau 'sudah_bayar'
        ↓
[6] Bukti sah untuk siswa: kwitansi
    PembayaranController@cetakKwitansi (576) → PDF via dompdf
```

### ALUR 2 — Pembayaran digital (Midtrans) — **paling sering ditanya soal keamanan**

```
[1] Wali siswa menekan "Bayar Online"
    POST /wali-siswa/tagihan/anak/{siswa}/bayar  (routes:1550)
    → OrangTuaController@prosesBayar
        ↓
[2] Sistem meminta Snap Token ke Midtrans
    → MidtransService@buildTransactionParams (125)
    → MidtransService@createSnapToken (61)
    → record Pembayaran dibuat dengan order_id unik
        ↓
[3] Halaman pembayaran Snap ditampilkan
    /wali-siswa/pembayaran/snap/{pembayaran}  (routes:1557)
    → wali membayar di antarmuka Midtrans (VA / e-wallet / kartu)
        ↓
[4] ⚠️ MIDTRANS memberi tahu SERVER kita (bukan browser!)
    POST /midtrans/notification  (routes:88)
    → MidtransWebhookController@notification (23)
        ↓
[5] TANDA TANGAN DIVERIFIKASI DULU  ← INTI KEAMANAN
    MidtransWebhookController:42 → MidtransService@verifySignature (171)
    → gagal? balas 403, TIDAK ada data yang diubah
        ↓
[6] Status Midtrans diterjemahkan ke status kita
    MidtransService@mapTransactionStatus (204)
    settlement/capture → 'disetujui', pending → 'menunggu', dst
        ↓
[7] Pembayaran diperbarui (webhook baris 71) + dicatat di FinancialAuditLog (81)
        ↓
[8] Status tagihan ikut dihitung ulang → lunas
```

**Pertanyaan yang HAMPIR PASTI muncul:**

> **"Kenapa webhook `/midtrans/notification` dikecualikan dari CSRF?"**
>
> **Jawab:** karena pemanggilnya adalah **server Midtrans**, bukan browser pengguna.
> Server Midtrans tidak punya session dan tidak mungkin mengirim token CSRF.
> **Tapi tidak berarti tanpa pengaman** — penggantinya adalah **verifikasi tanda tangan
> digital** di [MidtransWebhookController:42](../app/Http/Controllers/MidtransWebhookController.php#L42).
> Tanda tangan itu hash dari `order_id + status_code + gross_amount + server_key`.
> Karena `server_key` hanya diketahui kami dan Midtrans, orang luar tidak bisa memalsukan
> notifikasi "sudah bayar". Kalau tanda tangan salah → dibalas 403 dan **tidak ada data
> yang diubah**.
>
> 👉 **Ini jawaban terbaik yang bisa kalian berikan di sidang.** Latih sampai lancar.
> Pengecualian CSRF-nya diatur di `bootstrap/app.php`.

> **"Kenapa statusnya menunggu webhook, tidak langsung dari halaman selesai bayar?"**
>
> **Jawab:** halaman "selesai" (`snap-finish`, routes:1556) hanya tampilan untuk
> pengguna, dan **bisa dipalsukan** — siapa pun bisa mengetik URL itu tanpa membayar.
> Kebenaran status hanya berasal dari webhook bertanda tangan yang datang langsung dari
> server Midtrans.

### ALUR 3 — Carryover tunggakan (fitur khas project ini)

**Masalah:** siswa naik kelas tapi SPP tahun lalu belum lunas. Kalau tagihan lama
ditinggal di tahun ajaran lama, bendahara harus membuka dua tahun ajaran untuk menagih.

**Solusi di sistem ini** — [TunggakanCarryoverService](../app/Services/TunggakanCarryoverService.php):

```
getKandidatTunggakan() (30)  → cari siswa yang masih menunggak di TA lama
        ↓
previewCarryover() (92)      → tampilkan dulu apa yang akan terjadi (belum mengubah)
        ↓
executeCarryover() (123)     → buat tagihan baru di TA aktif
        ↓
Tagihan lama  : dialihkan_ke_id  → menunjuk tagihan baru (Tagihan.php:78)
Tagihan baru  : tagihan_asal_id  → menunjuk tagihan lama
```

**Dua pertanyaan turunan yang pasti muncul:**

> **"Kalau tagihannya digandakan, apa tidak dihitung dobel di laporan?"**
>
> **Jawab:** tidak, karena ada scope
> [`belumLunasOriginal()`](../app/Models/Tagihan.php#L69) yang menyaring
> `whereNull('dialihkan_ke_id')` — tagihan lama yang sudah dialihkan otomatis
> dikecualikan dari perhitungan. Jadi hanya dihitung satu kali.

> **"Kalau tagihan barunya dilunasi, tagihan lama jadi apa?"**
>
> **Jawab:** ikut ditandai lunas secara otomatis. Lihat
> [Tagihan.php:127-132](../app/Models/Tagihan.php#L127-L132) — saat tagihan carryover
> berubah jadi `sudah_bayar`, status itu dipropagasikan ke `tagihan_asal_id`.
> Tujuannya agar riwayat tahun lama tetap konsisten dan **jejak audit tetap utuh**
> (datanya tidak dihapus, hanya ditandai).

### Skenario latihan

**C-1 (mudah, 8 menit)** — *"Tambahkan jenis tagihan baru: 'Studi Tour'."*
1. Cari `getLabelJenis()` di [Tagihan.php:140](../app/Models/Tagihan.php#L140), tambah
   `'studi_tour' => 'Studi Tour'`.
2. Cek kolom `jenis_tagihan` di migration tabel `tagihan` — kalau bertipe **enum**,
   butuh migration untuk menambah nilai; kalau `string`, tidak perlu.
   👉 *Memeriksa ini dulu = nilai plus besar.*
3. Tambahkan pilihannya di form tagihan (`resources/views/bendahara/tagihan/create-custom.blade.php`)
   dan di aturan validasi controller (`storeCustom()` baris 712).

**C-2 (sedang, 15 menit)** — *"Tampilkan sisa tagihan yang harus dibayar di daftar tagihan."*
Accessor-nya **sudah ada**: [`getSisaPembayaranAttribute()`](../app/Models/Tagihan.php#L89).
Cukup panggil `{{ $tagihan->sisa_pembayaran }}` di Blade — tidak perlu menulis logika baru.
👉 *Menemukan bahwa fungsinya sudah ada, alih-alih menulis ulang, justru yang dinilai
tinggi.* Jangan lupa `number_format()` agar terbaca sebagai rupiah.

**C-3 (sulit, 20 menit)** — *"Tambahkan filter status di halaman daftar tagihan bendahara."*
Polanya identik dengan Skenario 5 di Panduan 1, tapi pada
[TagihanController@index (43)](../app/Http/Controllers/Bendahara/TagihanController.php#L43).
**Hati-hati:** method `index()` di sini panjang (baris 43-205) dan sudah punya filter
lain — sisipkan filter baru mengikuti pola yang sudah ada, jangan menimpa.

---

## BAGIAN D — SUBSISTEM 3: Akademik (SIA)

### Peta file

| Bagian | File | Baris penting |
|---|---|---|
| Route wali kelas | [routes/web.php](../routes/web.php) | grup 1090 |
| Route guru | [routes/web.php](../routes/web.php) | grup 1218 |
| **Model Nilai** | [app/Models/Nilai.php](../app/Models/Nilai.php) | `COMPONENT_FIELDS` 14-20; `$fillable` 22-50 |
| Model Rapor | [app/Models/Rapor.php](../app/Models/Rapor.php) | `status` 29; `status_review_ketua` 36; terbit 76; draft 86 |
| Controller nilai | [WaliKelas/NilaiController.php](../app/Http/Controllers/WaliKelas/NilaiController.php) | `index()` 27; `edit()` 449; `update()` 519; `importExcel()` 713; **`syncFromGuru()` 799** |
| Controller rapor | [WaliKelas/RaporController.php](../app/Http/Controllers/WaliKelas/RaporController.php) | `generateAll()` 137; `generateSingle()` 194; `update()` 331; **`terbitkan()` 590**; `kirimValidasi()` 1049; `print()` 783 |
| Controller presensi | [WaliKelas/PresensiController.php](../app/Http/Controllers/WaliKelas/PresensiController.php) | `index()` 82; `inputHarian()` 364; `validasiIzin()` 243; `prosesValidasiIzin()` 309 |
| **Sinkronisasi nilai** | [app/Services/NilaiSyncService.php](../app/Services/NilaiSyncService.php) | `syncForSiswaMapel()` 29; `syncFromTugasSiswa()` 87; `syncFromUjianSiswa()` 106 |
| **Kenaikan kelas** | [app/Services/PromotionService.php](../app/Services/PromotionService.php) | `checkEligibility()` 19; `checkFinancial()` 46; `checkAcademic()` 78; `executeStudentPromotion()` 156; `rollbackStudent()` 389 |

### Struktur nilai (sering ditanya "kenapa kolomnya banyak sekali?")

[Nilai.php:14-20](../app/Models/Nilai.php#L14-L20) mendefinisikan komponen penilaian:

```php
public const COMPONENT_FIELDS = [
    'tugas_1' … 'tugas_5',        // 5 tugas
    'latihan_1' … 'latihan_5',    // 5 latihan
    'uh_1' … 'uh_5',              // 5 ulangan harian
    'pts', 'pas', 'keterampilan', // tengah & akhir semester
    'to_1','to_2','to_3','upk','ujian_praktek',  // khusus kelas akhir
];
```

**Jawaban kalau ditanya kenapa begitu:** struktur ini mengikuti format penilaian yang
dipakai sekolah — 5 tugas, 5 latihan, 5 ulangan harian, PTS, PAS, keterampilan, plus
komponen khusus kelas tingkat akhir (try out, UPK, ujian praktek). Nilai akhir dihitung
dari rata-rata tiap kelompok (`rata_tugas`, `rata_latihan`, `rata_uh`).

### ⭐ Konsep unik: kolom `_guru` (snapshot)

Perhatikan [Nilai.php:44-49](../app/Models/Nilai.php#L44-L49) — ada pasangan kolom
berakhiran `_guru`: `tugas_1_guru`, `pts_guru`, `pas_guru`, dst.

> **Kenapa ada dua set kolom?**
>
> **Jawab:** kolom `_guru` adalah **salinan asli nilai dari guru pengajar** (sumber
> kebenaran), sedangkan kolom biasa adalah nilai yang **boleh disesuaikan wali kelas**
> saat menyusun rapor. Dengan memisahkan keduanya, sistem tetap bisa menunjukkan nilai
> asli guru meskipun wali kelas melakukan penyesuaian — jadi ada **jejak audit** dan
> perubahan tidak menghapus data asal.
>
> Bukti di kode: `Nilai` juga menyimpan `guru_terakhir_simpan_at` dan
> `wali_terakhir_edit_at` ([baris 30-31](../app/Models/Nilai.php#L30-L31)) untuk
> mencatat siapa terakhir mengubah.
>
> 👉 *Ini jawaban kelas atas. Kalau ada satu hal dari bagian Akademik yang perlu
> dihafal, pilih ini.*

### ALUR 4 — Nilai: dari LMS sampai jadi rapor

```
[1] Siswa mengerjakan tugas / ujian di LMS
        ↓ (otomatis, lewat Observer)
[2] TugasSiswaObserver / UjianSiswaObserver  (app/Observers/)
    memicu NilaiSyncService
        ↓
[3] NilaiSyncService@syncFromTugasSiswa (87) / @syncFromUjianSiswa (106)
    → mengisi kolom komponen di tabel `nilai` secara otomatis
        ↓
[4] Guru pengajar meninjau & menyimpan nilai
    → tersimpan juga ke kolom *_guru (snapshot asli guru)
        ↓
[5] Wali kelas membuka rekap nilai
    /wali/nilai → NilaiController@index (27)
    → bisa menyesuaikan: @edit (449) → @update (519)
    → bisa menarik ulang dari guru: @syncFromGuru (799)
        ↓
[6] Wali kelas membuat rapor
    RaporController@generateAll (137) / @generateSingle (194)
    → status rapor: 'draft'
        ↓
[7] Rapor dikirim untuk divalidasi
    RaporController@kirimValidasi (1049) → status_review_ketua berubah
        ↓
[8] Ketua PKBM menyetujui
    → PengajuanRaporKetua / Ketua\ValidasiRaporController
        ↓
[9] Wali kelas menerbitkan
    RaporController@terbitkan (590) → status jadi 'diterbitkan' (Rapor.php:76)
        ↓
[10] Wali siswa bisa melihat & minta unduh
     /wali-siswa/rapor/anak/{siswa} (routes:1564)
     → minta izin unduh (1566) → wali kelas menyetujui
       RaporController@approveDownload (1262) → unduh via token (1567)
```

> **Pertanyaan turunan:** *"Kenapa unduh rapor harus minta izin dulu?"*
> **Jawab:** rapor dokumen resmi berisi data pribadi siswa. Dengan alur permintaan +
> persetujuan + token sekali pakai, sekolah punya kendali dan catatan siapa mengunduh
> apa dan kapan — bukan file yang bisa diambil siapa saja yang tahu URL-nya.

### ALUR 5 — Kenaikan kelas (dua syarat sekaligus)

[PromotionService@checkEligibility (19)](../app/Services/PromotionService.php#L19)
memeriksa **dua hal** sebelum siswa boleh naik kelas:

```
checkEligibility() (19)
   ├── checkFinancial() (46)  → tunggakan lunas?
   └── checkAcademic() (78)   → nilai ≥ KKM?
            └── getKKM() (122)  → KKM per mapel / jenjang
                getPassingThreshold() (143)
                       ↓
executeStudentPromotion() (156)
   ├── isFinalYear() (275)     → siswa kelas akhir → lulus, bukan naik
   ├── findNextClass() (307)   → cari kelas tujuan
   └── findSameClass() (355)   → kalau tinggal kelas
                       ↓
rollbackStudent() (389)  → bisa DIBATALKAN kalau salah
```

> **Pertanyaan:** *"Kenapa kenaikan kelas juga mengecek keuangan?"*
> **Jawab:** kebijakan sekolah — siswa yang masih menunggak tidak otomatis dinaikkan;
> perlu pelunasan atau dispensasi resmi. Karena itu ada fitur dispensasi
> (`Ketua\DispensasiController`) supaya kasus khusus tetap bisa ditangani tanpa
> mengakali data.

> **Pertanyaan:** *"Kalau salah menaikkan, bisa dibatalkan?"*
> **Jawab:** bisa — [`rollbackStudent()` (389)](../app/Services/PromotionService.php#L389).
> Ini penting karena kenaikan kelas mengubah banyak data sekaligus, jadi harus ada
> jalan mundur. 👉 *Menyebut adanya rollback = nilai plus.*

### 🔧 Perbaikan terbaru: halaman Rekap Kenaikan Kelas (bukan bagian latihan)

Dua bug UI ditemukan & diperbaiki di
[rekap.blade.php](../resources/views/admin/akademik/promotion/rekap.blade.php)
(route `/admin/akademik/kenaikan-kelas/report`, dipakai juga oleh waka di
`/waka/kenaikan-kelas/report`).

**Bug 1 — aksi "Naikkan Terpilih" ikut aktif di mode read-only.** Halaman ini
punya toggle **Keadaan Saat Ini** (`sim_mode=current`, real-time) vs **Keadaan
Saat Eksekusi** (`sim_mode=historical`, snapshot masa lalu). Sebelumnya kolom
checkbox, "Pilih Semua", banner pilih-semua-halaman, dan form/tombol "Naikkan
Terpilih" tampil di **kedua** mode — padahal `promote-selected` memakai
`tahun_ajaran_id` dari tahun yang sedang dilihat (bisa tahun lama) dengan data
siswa **saat ini**, sehingga menaikkan siswa dari mode historical berisiko
menimpa riwayat `status_naik_kelas_siswa` lama dan memindahkan kelas siswa
berdasarkan struktur kelas tahun yang sudah lewat. Diperbaiki dengan menggating
semua elemen interaktif itu di balik `($simMode ?? 'current') === 'current'`
([baris 467, 478, 486, 503, 516, 526, 586-587, 605](../resources/views/admin/akademik/promotion/rekap.blade.php#L467));
mode historical sekarang menampilkan alert read-only sebagai gantinya.

**Bug 2 — tombol "Cetak Laporan" mengambang di tengah header (desktop).**
Header tab Riwayat Eksekusi punya 3 anak flex langsung (judul, tombol cetak,
form filter) dengan `justify-content-between` — di layar lebar tombol cetak
jadi mengambang sendirian di antara judul dan filter. Diperbaiki dengan
mengelompokkan tombol cetak + form filter dalam satu wrapper
`.history-header-actions`
([baris 98-99](../resources/views/admin/akademik/promotion/rekap.blade.php#L98-L99)),
sehingga header jadi 2 kelompok: judul (kiri) dan aksi (kanan).

> Berbeda dari bug badge prioritas Pengumuman (Panduan 1), dua bug ini **bukan**
> skenario latihan — sudah langsung diperbaiki di `add-cloudflare`
> (commit `3539509`) dan diterapkan ulang secara manual di `clean-production`
> (commit `53384ee`, karena riwayat file di branch itu sudah divergen). Dicatat
> di sini murni sebagai peta kode, siapa tahu penguji bertanya soal halaman ini.

### Skenario latihan

**D-1 (sedang, 12 menit)** — *"Ubah aturan: nilai minimal kelulusan (KKM) default jadi 75."*
Lokasi: [`getKKM()` (122)](../app/Services/PromotionService.php#L122) dan
[`getPassingThreshold()` (143)](../app/Services/PromotionService.php#L143).
**Penting untuk dijelaskan:** KKM di sistem ini **tidak di-hardcode** — disimpan per
mapel/jenjang dan dikelola lewat halaman admin (`/admin/akademik/promotion/kkm`,
asetnya di [vite.config.js:292-293](../vite.config.js#L292-L293)). Jadi jawaban paling
benar: *"nilainya diubah lewat menu pengaturan KKM, bukan dengan mengubah kode"* —
kode hanya menyediakan nilai cadangan.
👉 *Menolak mengubah kode ketika seharusnya lewat pengaturan = jawaban dewasa.*

**D-2 (sedang, 12 menit)** — *"Tambahkan kolom catatan wali kelas di rapor."*
Ikuti pola 5 langkah Skenario 6 Panduan 1: migration → `$fillable` di
[Rapor.php:16](../app/Models/Rapor.php#L16) → validasi di
[RaporController@update (331)](../app/Http/Controllers/WaliKelas/RaporController.php#L331)
→ input di form edit rapor → tampilkan di view cetak
([`print()` 783](../app/Http/Controllers/WaliKelas/RaporController.php#L783)).
**Kemana lagi:** view PDF rapor terpisah dari view web — harus diubah keduanya.

---

## BAGIAN E — SUBSISTEM 4: LMS

### Peta file

| Bagian | File | Baris penting |
|---|---|---|
| Route guru | [routes/web.php](../routes/web.php) | grup 1218 |
| Route siswa | [routes/web.php](../routes/web.php) | grup 1405 |
| **Gerbang akses LMS** | [app/Http/Middleware/CheckLmsAccess.php](../app/Http/Middleware/CheckLmsAccess.php) | seluruh file (46 baris) |
| Materi | `Guru/GuruMateriController.php` · `Siswa/LmsMateriController.php` | — |
| Tugas | `Guru/GuruTugasController.php` · `Siswa/LmsTugasController.php` | — |
| **Ujian (guru)** | [Guru/GuruUjianController.php](../app/Http/Controllers/Guru/GuruUjianController.php) | `store()` 98; `soal()` 533; `manageSoal()` 741; **`pengawasan()` 436**; `toggleStatus()` 973; `koreksiShow()` 1050 |
| **Ujian (siswa)** | [Siswa/LmsUjianController.php](../app/Http/Controllers/Siswa/LmsUjianController.php) | `show()` 23; **`mulai()` 145**; `submit()` 229; `selesaikanUjian()` 269; `autosave()` 449; `monitoring()` 510 |
| Forum | `Guru/GuruForumController.php` · `Siswa/LmsForumController.php` | — |
| Arsip konten | [app/Services/GuruLmsArsipService.php](../app/Services/GuruLmsArsipService.php) | salin konten antar kelas |
| Monitoring LMS | [app/Services/LmsMonitoringService.php](../app/Services/LmsMonitoringService.php) | — |
| Fitur AI | `AiQuestionGeneratorService`, `AiGradingService`, `AiChatbotService` | prompt di `config/ai-prompts.php` |

### Gerbang akses LMS — [CheckLmsAccess.php](../app/Http/Middleware/CheckLmsAccess.php)

LMS **tidak aktif untuk semua jenjang**. Middleware ini memeriksa 3 hal berurutan:

```php
[1] baris 23  → sudah login? kalau tidak → ke halaman login
[2] baris 28  → punya data siswa & kelas? kalau tidak → 403
[3] baris 36  → ambil setting 'lms_allowed_jenjang' dari tabel app_settings
    baris 40  → jenjang kelas siswa termasuk yang diizinkan?
              → kalau tidak: tampilkan errors.lms-disabled (baris 41)
```

> **Pertanyaan:** *"Kenapa LMS bisa dinyalakan/dimatikan per jenjang?"*
> **Jawab:** karena tidak semua jenjang siap memakai LMS (mis. PAUD/TK belum perlu).
> Daftar jenjang yang diizinkan disimpan di tabel `app_settings` dengan kunci
> `lms_allowed_jenjang`, jadi bisa diubah lewat halaman admin **tanpa mengubah kode dan
> tanpa deploy ulang**. Siswa di jenjang yang belum diizinkan diarahkan ke halaman
> penjelasan, bukan halaman error mentah.
> 👉 *Ini contoh bagus "konfigurasi di database, bukan di kode". Sebutkan istilah itu.*

### ALUR 6 — Ujian online (paling banyak logikanya)

```
GURU:
[1] Buat ujian      → GuruUjianController@store (98)
[2] Tambah soal     → @createSoal (558) / @storeSoal (579) / @manageSoal (741)
                      (bisa dibantu AI: AiQuestionGeneratorService)
[3] Aktifkan ujian  → @toggleStatus (973)

SISWA:
[4] Buka ujian      → LmsUjianController@show (23)
[5] Mulai           → @mulai (145)   → record UjianSiswa dibuat, waktu mulai dicatat
[6] Mengerjakan     → @autosave (449) → jawaban disimpan berkala
                      supaya tidak hilang kalau koneksi putus / browser tertutup
[7] Kumpulkan       → @submit (229) → @selesaikanUjian (269)
                      → koreksi otomatis untuk pilihan ganda
                      → esai: menunggu koreksi guru / AiGradingService
        ↓
[8] Nilai masuk ke SIA otomatis
    UjianSiswaObserver → NilaiSyncService@syncFromUjianSiswa (106)

PENGAWASAN (nilai jual sistem ini):
[9] Guru memantau real-time → @pengawasan (436) / @pengawasanData (465)
    Siswa: @monitoring (510), recordPengawasanLog (710)
    → mencatat kejadian mencurigakan (mis. berpindah tab)
[10] Guru mengoreksi esai   → @koreksiShow (1050)
[11] Guru menampilkan hasil → @toggleResultVisibility (1012)
```

**Pertanyaan yang sering muncul:**

> **"Bagaimana kalau siswa keluar/koneksi putus di tengah ujian?"**
> **Jawab:** jawaban disimpan berkala lewat `autosave()` (baris 449), dan status per
> soal disimpan di tabel `ujian_siswa_soal_status`. Jadi saat masuk kembali, jawaban
> yang sudah diisi tidak hilang. Waktu mulai juga dicatat di `mulai()` (145) sehingga
> batas waktu dihitung dari server, bukan dari jam di komputer siswa.

> **"Bagaimana mencegah kecurangan?"**
> **Jawab:** ada mekanisme pengawasan — aktivitas siswa dicatat lewat
> `recordPengawasanLog()` (710), misalnya saat berpindah tab, dan guru bisa memantau
> peserta secara langsung lewat halaman pengawasan (436). Ini deteksi & pencatatan,
> bukan pemblokiran total — kami mencatatnya sebagai keterbatasan yang jujur.
> 👉 *Menyebut batasannya sendiri justru menambah kredibilitas.*

> **"Nilai ujian LMS otomatis masuk rapor?"**
> **Jawab:** masuk ke tabel `nilai` secara otomatis lewat Observer +
> `NilaiSyncService`, tapi **rapor tetap disusun wali kelas** dan harus melewati
> validasi Ketua sebelum diterbitkan. Jadi otomatis di pengumpulan data, tetap ada
> kontrol manusia di penerbitan.

### Skenario latihan

**E-1 (mudah, 10 menit)** — *"Tambahkan jenjang SMP ke daftar yang boleh mengakses LMS."*
Jawaban yang benar: **lewat halaman admin LMS Settings**, bukan mengubah kode — datanya
di `app_settings` kunci `lms_allowed_jenjang`
(view: `resources/views/admin/lms-settings/index.blade.php`,
CSS terdaftar di [vite.config.js:486](../vite.config.js#L486)).
Tunjukkan pembacaannya di [CheckLmsAccess.php:36](../app/Http/Middleware/CheckLmsAccess.php#L36).

**E-2 (sedang, 15 menit)** — *"Tambahkan kolom 'durasi pengerjaan' di daftar hasil ujian."*
Data waktu mulai & selesai sudah tercatat di `UjianSiswa` (diisi
[`mulai()` 145](../app/Http/Controllers/Siswa/LmsUjianController.php#L145) dan
[`selesaikanUjian()` 269](../app/Http/Controllers/Siswa/LmsUjianController.php#L269)).
Jadi cukup hitung selisihnya dan tampilkan di view `hasil`
([`hasil()` 376](../app/Http/Controllers/Guru/GuruUjianController.php#L376)).
Pakai Carbon: `$mulai->diffInMinutes($selesai)`.

---

## BAGIAN F — Jembatan antar subsistem (jawaban "wow")

Kalau penguji bertanya **"apa yang membuat sistem ini terintegrasi, bukan sekadar 3
aplikasi terpisah?"** — inilah jawabannya. Hafalkan bagian ini.

### [ValidasiAksesService](../app/Services/ValidasiAksesService.php) — keuangan mengunci akademik

| Method | Baris | Fungsinya |
|---|---|---|
| `getPeriode()` | 16 | tentukan periode dari semester + jenis rapor |
| `cekLunas()` | 32 | apakah tagihan periode itu lunas? |
| **`cekAksesRapor()`** | **54** | boleh lihat rapor? |
| **`cekAksesUjian()`** | **90** | boleh ikut ujian? |
| `cekDispensasiDisetujui()` | 113 | ada dispensasi resmi? |
| `cekIzinNaikKelasKhusus()` | 129 | izin khusus naik kelas |
| **`autoValidasiSetelahBayar()`** | **145** | buka akses otomatis setelah lunas |

**Alurnya:**

```
Siswa menunggak
      ↓
cekAksesRapor() / cekAksesUjian()  →  akses DITOLAK
      ↓
Ada 2 jalan keluar:
  (a) Melunasi → autoValidasiSetelahBayar() (145) → akses terbuka OTOMATIS
  (b) Dispensasi dari Ketua → cekDispensasiDisetujui() (113) → akses terbuka
```

**Kalimat siap pakai untuk sidang:**

> _"Ketiga subsistem tidak berdiri sendiri. Contoh nyatanya `ValidasiAksesService`:
> status keuangan siswa menentukan apakah dia boleh mengikuti ujian dan melihat rapor.
> Kalau menunggak, aksesnya tertutup; begitu lunas, `autoValidasiSetelahBayar()`
> membukanya kembali secara otomatis tanpa perlu bendahara membuka satu per satu.
> Untuk kasus khusus tetap ada jalur dispensasi dari Ketua PKBM, supaya kebijakan tetap
> manusiawi. Integrasi lain: nilai dari LMS otomatis masuk ke SIA lewat Observer dan
> `NilaiSyncService`, dan kenaikan kelas mengecek keuangan sekaligus akademik lewat
> `PromotionService`."_

### Tiga titik integrasi (ringkas — hafalkan ketiganya)

| # | Integrasi | Filenya |
|---|---|---|
| 1 | **Keuangan → Akademik**: tunggakan mengunci ujian & rapor | `ValidasiAksesService` |
| 2 | **LMS → SIA**: nilai tugas/ujian otomatis jadi nilai rapor | `NilaiSyncService` + Observer |
| 3 | **Keuangan + Akademik → Kenaikan kelas**: dua syarat sekaligus | `PromotionService` |

### Kenapa `Tahun Ajaran` ada di mana-mana?

`TahunAjaran` adalah **poros seluruh sistem** — hanya satu yang aktif pada satu waktu.
Hampir semua data (tagihan, kelas, nilai, jadwal, rapor) punya `tahun_ajaran_id`.
Alasannya: sekolah berjalan per tahun ajaran, dan data tahun lalu **tidak boleh hilang
maupun tercampur** dengan tahun berjalan. Itu juga sebabnya fitur carryover tunggakan
diperlukan — untuk memindahkan kewajiban lama ke tahun aktif secara tertelusur.

---

## BAGIAN G — 30 pertanyaan cepat + jawaban

Latih bergantian: satu orang membaca pertanyaan, yang lain menjawab **tanpa melihat**.

### Umum / arsitektur

1. **"Ada berapa peran di sistem ini?"** → 9: admin, ketua PKBM, wakil kepala sekolah,
   sekretaris, bendahara, wali kelas, guru pengajar, siswa, wali siswa (orang tua).
2. **"Kenapa Laravel?"** → Framework PHP dengan struktur MVC yang jelas, punya ORM
   (Eloquent), sistem migration, middleware bawaan untuk hak akses, dan ekosistem paket
   siap pakai (PDF, Excel, payment gateway) — mempercepat pengembangan sekaligus menjaga
   keamanan dasar.
3. **"Berapa tabel di database?"** → Ada 51 migration di `database/migrations/`; jumlah
   pastinya bisa dicek dengan `php artisan migrate:status`.
   👉 *Jangan mengarang angka. Menyebut cara mengeceknya lebih baik.*
4. **"Di mana semua route?"** → Satu file: `routes/web.php`, dikelompokkan per peran
   (lihat Bagian A).
5. **"Logika bisnis berat ditaruh di mana?"** → `app/Services/` — ada 13 service, mis.
   `MidtransService`, `PromotionService`, `NilaiSyncService`, `ValidasiAksesService`.

### Keuangan

6. **"Status tagihan apa saja?"** → `belum_bayar`, `cicilan`, `terlambat`, `sudah_bayar`.
7. **"Siapa yang mengubah status tagihan?"** → Tidak ada yang mengetik manual —
   dihitung ulang `Tagihan::updateStatusBayar()` (baris 103) dari total pembayaran
   berstatus `disetujui`.
8. **"Bisa mencicil?"** → Bisa. Satu tagihan punya banyak pembayaran; kalau total
   pembayaran belum menutup jumlah tagihan, statusnya `cicilan`.
9. **"Pembayaran online pakai apa?"** → Midtrans (Snap), lewat `MidtransService`.
10. **"Bagaimana sistem tahu pembayaran online berhasil?"** → Dari webhook
    `/midtrans/notification` (routes:88) yang **tanda tangannya diverifikasi**
    (`verifySignature`, MidtransService:171).
11. **"Kenapa webhook dikecualikan dari CSRF?"** → (lihat Bagian C — jawaban panjang)
12. **"Apa itu carryover tunggakan?"** → Memindahkan tunggakan TA lama menjadi tagihan
    di TA aktif, dengan `tagihan_asal_id` & `dialihkan_ke_id` sebagai penghubung.
13. **"Tidak dihitung dobel?"** → Tidak, ada scope `belumLunasOriginal()` (Tagihan:69).
14. **"Ada jejak audit keuangan?"** → Ada, `FinancialAuditLog` — webhook pun mencatat
    perubahan status (MidtransWebhookController:81).
15. **"Bendahara bisa mencetak apa?"** → Kwitansi pembayaran (`cetakKwitansi` 576),
    tagihan per siswa (`cetak` 436), dan laporan (`cetakLaporan` 466) — semuanya PDF
    lewat dompdf.

### Akademik

16. **"Siapa yang menginput nilai?"** → Guru pengajar per mapel; wali kelas merekap dan
    boleh menyesuaikan untuk rapor.
17. **"Nilai asli guru hilang kalau diubah wali kelas?"** → Tidak — disimpan terpisah di
    kolom berakhiran `_guru` (Nilai.php:44-49).
18. **"Alur rapor sampai bisa dilihat orang tua?"** → draft → dikirim validasi
    (`kirimValidasi` 1049) → disetujui Ketua → diterbitkan (`terbitkan` 590) → orang tua
    minta izin unduh → wali kelas menyetujui (`approveDownload` 1262) → unduh via token.
19. **"Syarat naik kelas?"** → Dua: keuangan lunas (`checkFinancial` 46) DAN nilai ≥ KKM
    (`checkAcademic` 78). Ada jalur dispensasi untuk kasus khusus.
20. **"Kenaikan kelas bisa dibatalkan?"** → Bisa, `rollbackStudent()` (389).
21. **"Presensi diinput siapa?"** → Wali kelas (`inputHarian` 364). Orang tua bisa
    mengajukan izin, lalu divalidasi wali kelas (`prosesValidasiIzin` 309).
22. **"KKM di-hardcode?"** → Tidak, disimpan per mapel/jenjang dan diatur lewat halaman
    admin; kode hanya menyediakan nilai cadangan.

### LMS

23. **"Fitur LMS apa saja?"** → Materi, tugas, ujian online, forum diskusi, kelas
    virtual (meeting), arsip konten antar kelas.
24. **"LMS aktif untuk semua siswa?"** → Tidak — dibatasi per jenjang lewat
    `CheckLmsAccess` + setting `lms_allowed_jenjang` di `app_settings`.
25. **"Ujian dikoreksi otomatis?"** → Pilihan ganda otomatis; esai dikoreksi guru,
    dengan bantuan `AiGradingService` bila diaktifkan.
26. **"AI dipakai untuk apa?"** → Membuat soal (`AiQuestionGeneratorService`), membantu
    koreksi (`AiGradingService`), dan chatbot bantuan (`AiChatbotService`). Prompt-nya
    di `config/ai-prompts.php`.
27. **"Kalau siswa curang?"** → Aktivitas dicatat (`recordPengawasanLog` 710) dan guru
    bisa memantau langsung (`pengawasan` 436). Sifatnya deteksi & pencatatan.

### Keamanan & operasional

28. **"Bagaimana kalau ada yang menebak URL file rapor?"** → Unduhan memakai token, dan
    aksesnya dicek kepemilikannya; ada juga uji negatifnya di koleksi Postman
    (`06 - GET View Document Invalid Token`).
29. **"Aplikasi ini di-deploy di mana?"** → Di belakang Cloudflare, dengan Turnstile
    sebagai captcha dan middleware `SecurityHeaders`. Catatan pengerasan ada di
    `docs/HARDENING_CLOUDFLARE.md`.
30. **"Apa kelemahan sistem kalian?"** → Jawab jujur: sebagian validasi masih inline di
    controller (idealnya Form Request), cakupan automated test masih terbatas, beberapa
    controller terlalu panjang (mis. `TagihanController` >1.200 baris) dan sebaiknya
    dipecah, serta pencegahan kecurangan ujian masih berupa deteksi, bukan pemblokiran.
    👉 *Menyebut kelemahan spesifik + rencana perbaikan = tanda menguasai, bukan kelemahan.*

---

## PENUTUP

**Cara memakai dua dokumen ini bersama:**

| Kalau penguji... | Buka |
|---|---|
| menyuruh **mengubah kode** | Panduan 1 — 10 skenario + Jurus Lacak 4 Langkah |
| bertanya **"di mana kode X?"** | Panduan 2 — peta file tiap subsistem |
| bertanya **"jelaskan alur Y"** | Panduan 2 — Alur 1-6 |
| bertanya **konsep Laravel** | Panduan 1 — Bagian 5 |
| bertanya **konsep sistem ini** | Panduan 2 — Bagian F & G |

**Tiga kalimat yang menyelamatkan di segala situasi:**

1. _"Boleh saya buka filenya dulu, Pak/Bu?"_ — selalu lebih baik daripada mengarang.
2. _"Ini ada di controller X method Y, saya tunjukkan."_ — menunjuk kode asli selalu
   lebih kuat daripada teori.
3. _"Itu keterbatasan yang kami sadari, rencananya diperbaiki dengan Z."_ — jujur soal
   kekurangan jauh lebih baik daripada berkilah.
