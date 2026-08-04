# PANDUAN SIDANG SIPADUHOK — EDISI LENGKAP

> **Buku pegangan latihan live coding untuk tim SIPADUHOK.**
> Ditulis untuk anggota tim yang baru mulai belajar PHP. Tujuannya bukan menghafal kode,
> tapi tahu **berkas mana yang harus dibuka** saat penguji meminta sesuatu.

**Berkas ini menggabungkan seluruh materi menjadi satu dokumen.** Tidak perlu membuka
berkas lain.

---

## Untuk konversi ke PDF

Dokumen ini ditulis dalam format Markdown agar bisa dikonversi menjadi PDF dengan rapi.
Kalau diserahkan ke asisten AI untuk dikonversi, sebutkan permintaan berikut:

- Buat **daftar isi dengan nomor halaman** di depan.
- Beri **warna berbeda** antara penjelasan dan blok kode; blok kode diberi latar dan
  huruf monospace.
- Beri **warna berbeda** untuk kotak peringatan: merah untuk jebakan, biru untuk poin
  yang menaikkan nilai, hijau untuk hal yang sudah beres, kuning untuk kehati-hatian.
- Jaga agar **blok kode dan tabel tidak terpotong** di batas halaman.
- Ukuran kertas A4, huruf isi sekitar 11pt.

---

## Cara memakai dokumen ini

| Kalau penguji... | Buka bab |
|---|---|
| menyuruh **mengubah kode** | Bab 6 (skenario inti) dan Bab 7 (skenario lanjutan) |
| bertanya **"di mana kode X?"** | Bab 8-11 (peta tiap subsistem) |
| bertanya **"jelaskan alur Y"** | Bab 4 (alur halaman), Bab 9-11 (alur bisnis) |
| bertanya **konsep pemrograman** | Bab 13 (bank pertanyaan) |
| bertanya **metodologi atau pengujian** | Bab 14 (pertanyaan di luar kode) |
| menyebut istilah yang tidak kalian kenal | Bab 2 (kamus istilah) |
| **menunjuk halaman apa pun dan minta diubah** | **Bab 17 (indeks lokasi 354 halaman)** |
| **menantang keputusan desain: "kenapa tidak begini?"** | **Bab 18 (pertanyaan desain)** |

**Urutan belajar yang disarankan:** Bab 1 → 2 → 3 (wajib hafal) → 5 → 6 → 7 → sisanya.
Jadwal harian ada di Bab 15.

---

## Isi dokumen

1. Mulai dari sini — kekhawatiran kalian, dijawab jujur
2. Kamus istilah
3. Jurus Lacak 4 Langkah **(wajib hafal)**
4. Alur satu halaman
5. Aturan keselamatan saat latihan
6. Sepuluh skenario latihan inti
7. Dua belas skenario lanjutan per subsistem
8. Peran dan route
9. Subsistem Keuangan
10. Subsistem Akademik
11. Subsistem LMS
12. Jembatan antar subsistem
13. Bank pertanyaan
14. Pertanyaan di luar kode — metodologi, pengujian, batasan
15. Jadwal tujuh hari
16. Cheat sheet
17. **Indeks lokasi semua halaman** — 354 halaman, dipakai saat sidang berlangsung
18. **Pertanyaan desain "kenapa tidak dibikin begini?"** — 40 pertanyaan penguji kritis + jawabannya

---

> **Catatan tentang nomor baris.** Seluruh rujukan berkas dan nomor baris di dokumen ini
> diambil dari kode yang benar-benar ada di proyek, bukan contoh umum. Tapi nomor baris
> **akan bergeser** setiap kali kode diedit. Karena itu setiap rujukan juga menyertakan
> **nama fungsi atau teks penanda** — biasakan mencari lewat itu memakai `Ctrl+F`,
> bukan mengandalkan nomor baris. Cara itu juga terlihat lebih profesional di depan penguji.

---
## BAB 1 — Mulai dari sini

### "Mungkinkah Blade / HTML / CSS / JS ditanya?"

**Sangat mungkin. Bahkan kemungkinannya lebih besar daripada PHP murni.** Alasannya:

1. **Perubahan UI paling gampang diminta & paling gampang dinilai.** Penguji tinggal
   bilang _"tolong tambahkan kolom X di tabel ini"_ atau _"ganti warna tombol ini"_,
   lalu dia lihat hasilnya di layar. Tidak perlu paham database.
2. Project ini punya **CSS & JS scoped per halaman** (`resources/css/<role>/<fitur>/<halaman>.css`).
   Ini pola yang tidak biasa dan menarik ditanya: _"kenapa CSS-nya dipisah-pisah?"_
3. Ada **jebakan Vite manifest** (lihat Skenario 9). Kalau kamu menambah file CSS baru
   dan lupa daftarkan di `vite.config.js`, halaman **error total**. Kalau ini terjadi di
   depan penguji dan kamu panik, nilai jatuh. Kalau kamu bisa bilang _"oh ini karena
   belum terdaftar di vite.config.js"_ lalu memperbaikinya, nilai justru **naik**.

**Kesimpulan: jangan cuma fokus PHP.** Porsi latihan yang sehat:
`PHP/Laravel 50% — Blade 25% — CSS 15% — JS 10%`.

Kabar baiknya: Blade itu **HTML biasa + tempelan PHP**. Kalau kamu sudah paham
`foreach`, `if`, dan variabel di PHP, kamu sudah bisa 80% Blade. Jadi belajar PHP dasar
kamu **tidak sia-sia** — Blade adalah tempat PHP dasar itu terpakai paling nyata.

### Bagian mana yang dijadikan "lapangan latihan"?

Semua skenario di dokumen ini memakai fitur **PENGUMUMAN (role Sekretaris)**. Dipilih karena:

- CRUD lengkap (Create-Read-Update-Delete) — mewakili pola 90% fitur lain di project ini.
- Punya semua unsur: migration, model, relasi, controller, validasi, upload file,
  Blade index + form, CSS scoped, JS scoped, modal Bootstrap.
- **Tidak menyentuh uang, nilai, atau rapor.** Kalau rusak saat latihan, tidak ada
  data penting yang hilang.

**Sekali kamu kuasai alur Pengumuman, semua fitur lain tinggal ganti nama.**
Tagihan, Kelas, Berita, Flyer, Mata Pelajaran — polanya identik.

### ✅ Ada bug asli di halaman ini — SUDAH DIPERBAIKI

Saat menyiapkan dokumen ini ditemukan bug nyata: kartu statistik
**"Prioritas Tinggi" selalu 0**, dan badge prioritas **"Mendesak" berwarna biru,
bukan merah**.

Sebabnya: kode Blade mencari nilai `'tinggi'` dan `'sedang'`, padahal nilai yang
benar-benar tersimpan di database adalah `'biasa'`, `'penting'`, `'mendesak'`
(lihat `migration baris 21`).
Tidak pernah ada yang cocok, jadi selalu jatuh ke nilai `default`.

Bug ini ada di **dua file sekaligus** (halaman Sekretaris dan salinannya di Admin) dan
**keduanya sudah diperbaiki**. Yang tersisa untuk kalian bukan memperbaiki, tapi
**bisa menjelaskan** kalau ditanya — itulah isi [Skenario 3](#-skenario-3--diagnosis-bug-badge-prioritas-bug-asli).

---

## BAB 2 — Kamus istilah

Setiap kata asing yang muncul di dokumen ini dijelaskan sekali di sini. Kalau menemukan
istilah yang tidak dikenal saat membaca bab lain, kembali ke halaman ini.

| Istilah | Artinya dengan bahasa sehari-hari |
|---|---|
| **Framework** | Kerangka kerja siap pakai. Laravel menyediakan struktur dan alat bawaan supaya kita tidak membangun semuanya dari nol. |
| **MVC** | Cara menata kode jadi tiga bagian: **M**odel (urusan data), **V**iew (tampilan), **C**ontroller (pengatur alur). Tujuannya agar tidak campur aduk. |
| **Route** | Daftar alamat URL. "Kalau ada yang membuka alamat ini, jalankan fungsi itu." Semuanya ada di satu berkas `routes/web.php`. |
| **Controller** | Kelas PHP berisi fungsi-fungsi yang dijalankan saat sebuah URL dibuka. Tugasnya mengambil data lalu mengirimkannya ke tampilan. |
| **Model** | Kelas PHP yang mewakili satu tabel database. Model `Pengumuman` mewakili tabel `pengumuman`. |
| **Eloquent** | Sistem bawaan Laravel untuk berbicara dengan database memakai PHP, bukan menulis perintah SQL mentah. |
| **Blade** | Cara menulis tampilan di Laravel. Isinya HTML biasa ditambah perintah khusus seperti `@if` dan `@foreach`. Berkasnya berakhiran `.blade.php`. |
| **Migration** | Struktur tabel database yang ditulis sebagai kode, sehingga bisa dijalankan ulang siapa pun dan tercatat di riwayat proyek. |
| **Middleware** | Lapisan pemeriksa sebelum permintaan masuk ke controller. Di sini gunanya mengecek apakah pengguna berhak membuka halaman itu. |
| **Relasi** | Hubungan antar tabel. Contoh: satu pengumuman *dibuat oleh* satu pengguna. |
| **Validasi** | Pemeriksaan data dari formulir sebelum disimpan. Misalnya judul wajib diisi dan maksimal 255 huruf. |
| **Vite** | Alat yang menggabungkan dan memadatkan berkas CSS dan JavaScript sebelum dipakai di browser. |
| **Enum** | Kolom database yang hanya boleh berisi nilai dari daftar tertentu. Contoh: prioritas hanya boleh `biasa`, `penting`, atau `mendesak`. |
| **Eager loading** | Mengambil data relasi sekaligus dalam satu perintah, supaya database tidak ditanyai berulang kali. Ditulis `with([...])`. |
| **N+1** | Masalah saat sistem menanyai database sekali untuk daftar, lalu sekali lagi untuk *setiap* baris. 15 baris jadi 16 permintaan — lambat. Obatnya eager loading. |
| **CSRF** | Serangan di mana situs lain diam-diam mengirim formulir memakai sesi login kita. Dicegah dengan token rahasia di setiap formulir (`@csrf`). |
| **XSS** | Serangan menyisipkan kode berbahaya lewat isian formulir. Dicegah dengan menuliskan data memakai `{{ }}` yang otomatis aman. |
| **Webhook** | Panggilan dari server luar ke server kita untuk memberi kabar. Midtrans memakainya untuk memberitahu bahwa pembayaran sudah berhasil. |
| **Accessor** | Fungsi di Model yang membuat nilai hasil hitungan seolah-olah kolom biasa. Contoh: `sisa_pembayaran`. |
| **Scope** | Potongan penyaring query yang diberi nama supaya bisa dipakai ulang. Contoh: `belumLunasOriginal()`. |
| **Observer** | Kelas yang otomatis berjalan saat data tertentu disimpan atau diubah, tanpa perlu dipanggil manual. |
| **Trait** | Kumpulan fungsi yang bisa dipakai bersama oleh beberapa kelas, supaya logikanya tidak disalin berulang. |
| **Seeder** | Pengisi data awal ke database, misalnya daftar peran dan akun admin pertama. |
| **Artisan** | Perintah baris teks bawaan Laravel, dijalankan dengan `php artisan ...`. |

---
## BAB 3 — Jurus Lacak 4 Langkah (WAJIB HAFAL)

Ini **satu-satunya hal yang benar-benar wajib kalian hafal bertiga.** Dengan ini,
apapun yang ditanya penguji, kalian bisa menemukan filenya dalam < 2 menit.

Misal penguji membuka halaman di browser dan bilang: _"ubah sesuatu di halaman ini."_

### Langkah 1 — Baca URL, tentukan ROLE

Lihat alamat di browser, ambil potongan **pertama** setelah domain.

| URL mengandung | Role | Controller ada di | View ada di |
|---|---|---|---|
| `/admin/...` | Admin | `app/Http/Controllers/Admin/` | `resources/views/admin/` |
| `/sekretaris/...` | Sekretaris | `app/Http/Controllers/Sekretaris/` | `resources/views/sekretaris/` |
| `/bendahara/...` | Bendahara | `app/Http/Controllers/Bendahara/` | `resources/views/bendahara/` |
| `/guru/...` | Guru | `app/Http/Controllers/Guru/` | `resources/views/guru/` |
| `/siswa/...` | Siswa | `app/Http/Controllers/Siswa/` | `resources/views/siswa/` |
| `/wali/...` | Wali Kelas | `app/Http/Controllers/WaliKelas/` | `resources/views/wali-kelas/` |
| `/wali-siswa/...` | Orang Tua | `app/Http/Controllers/OrangTua/` | `resources/views/wali-siswa/` |
| `/waka/...` | Wakil Kepala Sekolah | `app/Http/Controllers/WakilKepalaSekolah/` | `resources/views/waka/` |
| `/ketua/...` | Ketua PKBM | `app/Http/Controllers/Ketua/` | `resources/views/ketua/` |

> Tabel lengkap ada di `AGENTS.md` bagian §3. **Print tabel ini.**

### Langkah 2 — Cari route-nya (dapat nama Controller + method)

Buka terminal di folder project:

```bash
php artisan route:list --path=sekretaris/pengumuman
```

Outputnya langsung memberitahu: URL apa → Controller mana → method apa.

**Alternatif tanpa terminal** (lebih cepat kalau grogi): buka
`routes/web.php`, tekan `Ctrl+F`, ketik `pengumuman`.
Semua route ada di **satu file ini**.

> Contoh nyata — route Pengumuman Sekretaris ada di
> `routes/web.php:932-939`.

### Langkah 3 — Buka Controller, cari `return view(...)`

Di dalam method-nya, baris `return view('...')` memberitahu **file Blade mana** yang dipakai.

```php
// app/Http/Controllers/Sekretaris/SekretarisController.php:661
return view('sekretaris.pengumuman.index', compact('pengumuman'));
//            ^ titik = garis miring folder
```

Artinya file-nya: `resources/views/sekretaris/pengumuman/index.blade.php`

**Titik (`.`) dibaca sebagai garis miring (`/`)**, dan selalu ditambah `.blade.php`.
Ini satu aturan kecil yang sering bikin bingung — hafalkan.

### Langkah 4 — Buka Blade, cari `@vite(...)` (dapat CSS & JS)

Di dalam file Blade, cari baris `@vite`:

```blade
{{-- resources/views/sekretaris/pengumuman/index.blade.php:12 --}}
@vite('resources/css/sekretaris/pengumuman/index.css')

{{-- baris 220 --}}
@vite('resources/js/sekretaris/pengumuman/index.js')
```

Itulah file CSS dan JS halaman tersebut. **Selesai.** Sekarang kamu punya 4 file:
route → controller → blade → css/js.

### Ringkas (tempel di meja saat sidang)

```
URL  →  routes/web.php  →  Controller@method  →  return view(...)  →  @vite(...)
role       (Ctrl+F)          logika & data         tampilan HTML       gaya & interaksi
```

---

## BAB 4 — Alur satu halaman

Kalau penguji tanya _"coba jelaskan alur programnya"_, gambar/ucapkan ini:

```
[1] Pengguna klik menu "Pengumuman"
        ↓  browser minta URL /sekretaris/pengumuman
[2] routes/web.php:934  → mencocokkan URL, memanggil SekretarisController@pengumumanIndex
        ↓  (sebelum masuk, dicegat middleware 'role:sekretaris' untuk cek hak akses)
[3] SekretarisController.php:655  → mengambil data dari database lewat Model
        Pengumuman::with([...])->orderBy(...)->paginate(15)
        ↓  hasilnya variabel $pengumuman
[4] return view('sekretaris.pengumuman.index', compact('pengumuman'))
        ↓  variabel dikirim ke Blade
[5] index.blade.php  → @foreach($items as $item) ... mencetak baris tabel HTML
        ↓  memuat index.css (tampilan) & index.js (modal hapus)
[6] Browser menampilkan halaman jadi
```

**Kalimat 1 baris untuk menjelaskan MVC di project ini:**

> _"Route menerima URL, Controller mengatur logika dan mengambil data lewat Model,
> lalu Model mengembalikan data ke Controller untuk dikirim ke View yang menampilkannya
> sebagai HTML."_

### Peta 6 file kunci fitur Pengumuman (hafalkan lokasinya)

| No | Peran | File |
|---|---|---|
| 1 | Struktur tabel database | `database/migrations/2025_12_16_093449_create_pengumuman_table.php` |
| 2 | Model (jembatan ke tabel) | `app/Models/Pengumuman.php` |
| 3 | Route | `routes/web.php:932-939` |
| 4 | Controller | `app/Http/Controllers/Sekretaris/SekretarisController.php:655-752` |
| 5 | View daftar & form | `.../pengumuman/index.blade.php` · `form.blade.php` |
| 6 | CSS & JS | `resources/css/sekretaris/pengumuman/{index,form}.css` · `resources/js/sekretaris/pengumuman/index.js` |

---

## BAB 5 — Aturan keselamatan saat latihan

Kalian latihan di **file asli project yang akan disidangkan**. Wajib patuhi ini:

### Sebelum mulai latihan tiap hari

```bash
git status              # pastikan bersih / tahu apa yang berubah
git checkout -b latihan-<namamu>    # cukup sekali, bikin cabang latihan
```

### Setelah selesai satu skenario — KEMBALIKAN

```bash
git diff                                 # lihat apa saja yang kamu ubah
git checkout -- <path/file/yang/diubah>  # batalkan perubahan 1 file
git checkout -- .                        # batalkan SEMUA perubahan belum di-commit
```

> `git checkout -- .` **menghapus semua perubahan yang belum disimpan**. Pastikan
> tidak ada pekerjaan lain yang belum di-commit sebelum menjalankannya.

### Sebelum hari-H sidang — WAJIB

```bash
git status        # HARUS bersih, tidak ada sisa eksperimen
npm run build     # WAJIB. Kalau ini error, halaman bisa blank saat demo
php artisan test  # pastikan tidak ada yang rusak
```

### Kalau halaman tiba-tiba blank/error saat latihan

Urutan yang harus dicoba (hafalkan urutannya, ini sering menyelamatkan):

```bash
php artisan optimize:clear    # bersihkan cache config/route/view
npm run build                 # bangun ulang CSS & JS
```

Kalau masih error, baca **baris pertama pesan error** — biasanya sudah menyebut nama
file dan nomor barisnya.

---

## BAB 6 — Sepuluh skenario latihan inti

**Cara memakai:** kerjakan berurutan dari Skenario 1. Setiap skenario ditulis seperti
permintaan penguji sungguhan. **Latih sambil diberi batas waktu** — minta teman
memegang stopwatch. Setelah selesai, **kembalikan perubahan** (Bab 5).

> **Catatan nomor baris:** nomor baris di bawah akurat per hari ini, tapi akan
> **bergeser** setiap kalian mengedit file. Karena itu setiap skenario juga memberi
> **teks penanda** untuk dicari dengan `Ctrl+F`. **Biasakan cari pakai teks penanda,
> bukan nomor baris** — ini juga terlihat lebih profesional di depan penguji.

---

### 🟢 SKENARIO 1 — Ubah teks & judul halaman

**Tingkat:** Pemanasan · **Target waktu:** 3 menit · **Menguji:** Blade dasar

**Kata penguji:**
> _"Tolong ganti judul halaman ini dari 'Kelola Pengumuman' menjadi
> 'Manajemen Pengumuman Sekolah', dan ubah tulisan tombol 'Tambah Pengumuman'
> menjadi 'Buat Pengumuman Baru'."_

**File:** `resources/views/sekretaris/pengumuman/index.blade.php`

**Langkah:**

1. Judul ada di **3 tempat berbeda** — ini poin yang dinilai, jangan cuma ganti satu:
   - **Baris 3** `@section('title', 'Kelola Pengumuman')` → judul di **tab browser**
   - **Baris 4** `@section('page-title', 'Kelola Pengumuman')` → judul di **header halaman**
   - **Baris 30** `<h5>Kelola Pengumuman</h5>` → judul di dalam **kotak toolbar**
2. Ganti ketiganya menjadi `Manajemen Pengumuman Sekolah`.
3. Tombol: cari teks `Tambah Pengumuman` — ada di **baris 36** (tombol atas) dan
   **baris 168** (tombol di tampilan kosong). Ganti keduanya.

**Kemana lagi harus dicek:**
- Nama menu di sidebar: `resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php:42`
  (`<div>Pengumuman</div>`). Kalau penguji minta konsisten, ganti di sini juga.
- File form juga punya judul sendiri: `form.blade.php:3-6`

**Cara uji:** refresh browser. Tidak perlu `npm run build` — perubahan Blade langsung terlihat.

**Jebakan:** hanya mengganti 1 dari 3 lokasi judul, lalu penguji menunjukkan tab browser
masih judul lama.

**Pertanyaan lisan yang mungkin menyusul:**
- _"Apa beda `@section('title')` dengan `@section('page-title')`?"_
  → **Jawab:** `title` masuk ke tag `<title>` HTML (muncul di tab browser),
  `page-title` dipakai layout untuk header di dalam halaman. Keduanya didefinisikan
  di layout `resources/views/layouts/sneat.blade.php`.

---

### 🟢 SKENARIO 2 — Tambah kolom baru di tabel

**Tingkat:** Mudah · **Target waktu:** 7 menit · **Menguji:** Blade + relasi Eloquent

**Kata penguji:**
> _"Saya ingin tahu siapa yang membuat setiap pengumuman. Tambahkan kolom 'Dibuat Oleh'
> di tabel ini."_

**File:** `resources/views/sekretaris/pengumuman/index.blade.php`

**Langkah:**

1. **Cek dulu datanya sudah tersedia atau belum.** Buka controller
   `SekretarisController.php:657`:
   ```php
   $pengumuman = Pengumuman::with(['kalenderAkademik', 'pembuat'])
   ```
   Ada `'pembuat'` → **datanya sudah diambil, controller tidak perlu diubah.**
   👉 *Katakan ini keras-keras ke penguji. Ini menunjukkan kamu paham eager loading.*

2. Pastikan relasinya ada di model — `Pengumuman.php:42-45`:
   ```php
   public function pembuat()
   {
       return $this->belongsTo(User::class, 'dibuat_oleh');
   }
   ```

3. **Tambah judul kolom.** Cari `<th class="text-center">Sumber</th>` (**baris 74**).
   Sisipkan **sesudahnya**:
   ```blade
   <th class="text-center">Dibuat Oleh</th>
   ```

4. **Tambah isi kolom.** Cari blok `data-label="Sumber"` (**baris 126-132**).
   Sisipkan **sesudah `</td>` penutupnya**:
   ```blade
   <td class="text-center" data-label="Dibuat Oleh">
       {{ $item->pembuat->name ?? '-' }}
   </td>
   ```

**Kemana lagi harus dicek:**
- **`data-label` wajib diisi.** CSS di halaman ini memakai `data-label` untuk menampilkan
  nama kolom saat dibuka di HP (tampilan tabel berubah jadi kartu). Kalau lupa,
  tampilan mobile rusak. ← *sebutkan ini ke penguji, nilai plus.*
- Jumlah `<th>` **harus sama** dengan jumlah `<td>`, kalau tidak tabel jadi bergeser.
- `?? '-'` wajib dipakai: kalau user pembuatnya sudah dihapus, `$item->pembuat` bernilai
  `null` dan halaman akan **error** tanpa pengaman ini.

**Cara uji:** refresh browser, pastikan kolom muncul & isinya nama user. Kecilkan
jendela browser sampai tampilan HP untuk memastikan `data-label` bekerja.

**Pertanyaan lisan:**
- _"Kenapa pakai `with(['pembuat'])`?"_
  → **Jawab:** supaya data pembuat diambil sekaligus dalam 1 query. Tanpa itu, Laravel
  akan query ulang ke tabel users untuk **setiap baris** — namanya masalah **N+1 query**,
  membuat halaman lambat.
- _"`->name` itu dari mana?"_
  → Kolom `name` di tabel `users`, diakses lewat relasi `belongsTo` yang menghubungkan
  `pengumuman.dibuat_oleh` ke `users.id`.

---

### 🟡 SKENARIO 3 — Diagnosis bug badge prioritas (BUG ASLI)

**Tingkat:** Sedang · **Target waktu:** 10 menit · **Menguji:** PHP `match`, ketelitian, debugging

> ✅ **Bug ini SUDAH DIPERBAIKI di project.** Jadi skenario ini **bukan lagi latihan
> mengetik**, melainkan latihan **menjelaskan**: kenapa bisa terjadi, bagaimana
> mendiagnosisnya, dan kenapa perbaikannya seperti itu. Ini justru bagian yang paling
> sering ditanya penguji.
>
> Ingin melihat kode sebelum-sesudah untuk belajar? Jalankan:
> ```bash
> git log --oneline -- resources/views/sekretaris/pengumuman/index.blade.php
> git show <commit> -- resources/views/sekretaris/pengumuman/index.blade.php
> ```

**Kata penguji (kalau bug ini belum diperbaiki, atau versi "bagaimana kalau"):**
> _"Kenapa kartu 'Prioritas Tinggi' isinya selalu 0? Dan kenapa pengumuman yang
> prioritasnya 'Mendesak' badge-nya biru, bukan merah?"_

**Diagnosis (ceritakan proses ini ke penguji, jangan langsung mengetik):**

1. Cek **nilai sebenarnya** di database — buka
   `migration baris 21`:
   ```php
   $table->enum('prioritas', ['biasa', 'penting', 'mendesak'])->default('biasa');
   ```
   → Nilai yang sah hanya: `biasa`, `penting`, `mendesak`.

2. Cek **apa yang dicari Blade** — `index.blade.php:22`:
   ```php
   $highCount = $items->where('prioritas', 'tinggi')->count();
   ```
   → Mencari `'tinggi'`, padahal **tidak pernah ada** nilai itu. Hasilnya selalu 0.

3. Bug yang sama di `baris 83-87`:
   ```php
   $priorityClass = match($priority) {
       'tinggi' => 'danger',
       'sedang' => 'warning',
       default => 'primary',
   };
   ```
   → Tidak ada yang cocok, semua jatuh ke `default => 'primary'` (biru).

**Perbaikan yang sudah diterapkan** (baris 22, 54, dan 82-94):

```php
// baris 22 — dulu mencari 'tinggi' yang tidak pernah ada
$highCount = $items->where('prioritas', 'mendesak')->count();
```

```php
// baris 82-94
$priority = $item->prioritas ?? 'biasa';
$priorityClass = match($priority) {
    'mendesak' => 'danger',
    'penting' => 'warning',
    default => 'primary',
};
$status = strtolower($item->status ?? 'aktif');
$statusClass = match($status) {
    'aktif' => 'success',
    'draft' => 'warning',
    'arsip' => 'muted',
    default => 'muted',
};
```

Baris 54 — label kartu diubah dari `Prioritas Tinggi` → `Prioritas Mendesak` agar
konsisten dengan data.

**Tiga hal yang WAJIB kamu bisa jelaskan soal perbaikan ini:**

1. **Bug-nya ada di DUA file, bukan satu.** Halaman Admin adalah salinan halaman
   Sekretaris (`admin/akademik/pengumuman/index.blade.php`),
   isinya identik sampai nomor barisnya. Memperbaiki satu saja berarti bug masih hidup
   di halaman satunya.
   👉 *Ini pelajaran penting: kalau menemukan bug, selalu cari apakah polanya
   terduplikasi di role lain.* Cara mencarinya:
   ```bash
   # cari pola yang sama di seluruh folder views
   ```
   lalu `Ctrl+Shift+F` di VS Code dengan kata kunci `'tinggi' => 'danger'`.

2. **Kenapa status `draft` jadi `warning` dan `arsip` jadi `muted`, bukan
   `secondary`/`dark`?** Karena kelas badge yang **benar-benar tersedia di CSS** hanya
   6: `primary`, `success`, `warning`, `danger`, `info`, `muted` — lihat
   `index.css baris 286-291`.
   Memakai `secondary` atau `dark` akan menghasilkan badge **tanpa warna sama sekali**,
   karena kelasnya tidak pernah didefinisikan.
   👉 *Ini contoh sempurna kenapa memperbaiki Blade saja tidak cukup — harus cek CSS-nya juga.*

3. **Kenapa bug ini tidak memunculkan error?** Karena `match` punya cabang `default`.
   Semua nilai yang tidak dikenal diam-diam jatuh ke sana. Bug yang tidak error justru
   yang paling berbahaya, karena tidak ada yang memberi tahu.

**Cara uji:** buat 1 pengumuman prioritas "Mendesak" lewat form, lalu lihat daftarnya —
badge harus merah dan kartu statistik bertambah 1. Ulangi di halaman Admin
(`/admin/akademik/pengumuman`) untuk memastikan keduanya sudah benar.

**Pertanyaan lisan:**
- _"Apa itu `match`?"_
  → **Jawab:** percabangan di PHP 8, mirip `switch` tapi lebih ringkas dan
  membandingkan secara ketat (`===`). `default` adalah nilai kalau tidak ada yang cocok.
- _"Kenapa bug ini bisa lolos?"_
  → **Jawab jujur & profesional:** nama nilai di Blade tidak sinkron dengan enum di
  migration, dan tidak error karena `match` punya `default`. Pelajarannya: nilai enum
  sebaiknya mengacu ke satu sumber, dan diuji dengan data tiap kategori.
  *(Jawaban jujur + tahu penyebabnya jauh lebih baik daripada mengarang.)*

---

### 🟡 SKENARIO 4 — Ubah aturan validasi + pesan Indonesia

**Tingkat:** Sedang · **Target waktu:** 8 menit · **Menguji:** Controller & validasi Laravel

**Kata penguji:**
> _"Judul pengumuman maksimal 100 karakter saja, dan lampiran PDF maksimal 2 MB.
> Pesan errornya tolong pakai Bahasa Indonesia."_

**File:** `app/Http/Controllers/Sekretaris/SekretarisController.php`

**Langkah:**

1. Cari method `pengumumanStore` (**baris 674**). Blok validasi ada di **baris 676-684**:
   ```php
   $validated = $request->validate([
       'judul' => 'required|string|max:255',
       'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
       ...
   ]);
   ```
2. Ubah `max:255` → `max:100`, dan `max:5120` → `max:2048`.
   > **Catatan penting:** satuan `max` untuk file adalah **kilobyte**. Jadi 2 MB = 2048.
   > Untuk teks, satuannya **jumlah karakter**. Sering ditanya — hafalkan.

3. Tambahkan pesan Indonesia sebagai **argumen kedua** `validate()`:
   ```php
   $validated = $request->validate([
       'judul' => 'required|string|max:100',
       // ... aturan lain tetap
   ], [
       'judul.required' => 'Judul pengumuman wajib diisi.',
       'judul.max' => 'Judul maksimal 100 karakter.',
       'lampiran_surat.mimes' => 'Lampiran harus berupa file PDF.',
       'lampiran_surat.max' => 'Ukuran lampiran maksimal 2 MB.',
   ]);
   ```

**⚠️ Kemana lagi harus diubah — INI YANG PALING SERING TERLUPA:**

1. **Method `pengumumanUpdate` punya blok validasi TERPISAH** di
   `baris 715-723`.
   Kalau hanya mengubah `Store`, aturan baru **tidak berlaku saat edit**. Ubah juga di sini.
   👉 *Penguji sering sengaja mengetes ini lewat form edit.*

2. **Batas di sisi HTML.** Buka `form.blade.php baris 55-61`,
   tambahkan `maxlength="100"` pada input judul supaya pengguna dicegah sejak awal:
   ```blade
   <input type="text" ... maxlength="100" required>
   ```

3. **Teks keterangan di form** `baris 123`
   masih tertulis `(PDF, max 5MB)` → ganti jadi `(PDF, max 2MB)`.

4. **Kolom database** `judul` bertipe `string` (= VARCHAR 255). Memperkecil batas
   validasi ke 100 **tidak butuh migration** karena masih muat. *Tapi kalau penguji
   minta lebih dari 255*, barulah butuh migration ubah kolom.
   👉 *Menjelaskan perbedaan ini = nilai plus besar.*

**Cara uji:** isi form dengan judul 150 karakter → harus ditolak dengan pesan Indonesia.
Ulangi lewat tombol **Edit** untuk memastikan `pengumumanUpdate` juga sudah diubah.

**Pertanyaan lisan:**
- _"Kalau validasi gagal, kenapa data yang sudah diketik tidak hilang?"_
  → **Jawab:** karena Blade memakai `old('judul', ...)`. Fungsi `old()` mengambil data
  input sebelumnya dari session. Lihat `form.blade.php:59`.
- _"Di mana pesan errornya ditampilkan?"_
  → Lewat direktif `@error('judul') ... @enderror` di
  `form.blade.php:62-64`.
- _"Kenapa validasi tidak cukup di HTML saja?"_
  → **Jawab:** validasi HTML (`required`, `maxlength`) bisa dilewati/dimatikan lewat
  developer tools. Validasi server **wajib** karena itu yang tidak bisa dimanipulasi
  pengguna.

---

### 🟡 SKENARIO 5 — Ubah urutan & tambah filter data

**Tingkat:** Sedang · **Target waktu:** 12 menit · **Menguji:** Eloquent Query Builder

**Kata penguji:**
> _"Tampilkan pengumuman yang paling mendesak di urutan paling atas. Lalu tambahkan
> dropdown untuk menyaring berdasarkan status."_

**File:** `SekretarisController.php:655-662`
dan `index.blade.php`

**Bagian A — Ubah urutan (mudah)**

Kode sekarang:
```php
public function pengumumanIndex()
{
    $pengumuman = Pengumuman::with(['kalenderAkademik', 'pembuat'])
        ->orderBy('tanggal_pengumuman', 'desc')
        ->paginate(15);

    return view('sekretaris.pengumuman.index', compact('pengumuman'));
}
```

Ubah jadi (urut prioritas dulu, baru tanggal):
```php
$pengumuman = Pengumuman::with(['kalenderAkademik', 'pembuat'])
    ->orderByRaw("FIELD(prioritas, 'mendesak', 'penting', 'biasa')")
    ->orderBy('tanggal_pengumuman', 'desc')
    ->paginate(15);
```

> `FIELD()` adalah fungsi MySQL untuk mengurutkan sesuai daftar yang kita tentukan.
> Kalau penguji tanya kenapa tidak `orderBy('prioritas')` saja: karena itu akan
> mengurutkan **sesuai abjad** (biasa, mendesak, penting) — bukan urutan kepentingan.
> 👉 *Jawaban ini sangat mengesankan. Hafalkan.*

**Bagian B — Tambah filter status**

1. **Controller** — terima parameter dari URL:
   ```php
   public function pengumumanIndex(Request $request)
   {
       $query = Pengumuman::with(['kalenderAkademik', 'pembuat']);

       if ($request->filled('status')) {
           $query->where('status', $request->status);
       }

       $pengumuman = $query
           ->orderByRaw("FIELD(prioritas, 'mendesak', 'penting', 'biasa')")
           ->orderBy('tanggal_pengumuman', 'desc')
           ->paginate(15)
           ->withQueryString();

       return view('sekretaris.pengumuman.index', compact('pengumuman'));
   }
   ```
   - `$request->filled('status')` → hanya menyaring kalau dropdown benar-benar diisi.
   - `->withQueryString()` → **penting**, supaya filter tidak hilang saat pindah halaman
     pagination. 👉 *Sering jadi pertanyaan jebakan.*
   - Pastikan `Request` sudah di-import di atas file (`use Illuminate\Http\Request;`) —
     di file ini **sudah ada**, jadi aman.

2. **Blade** — tambahkan form filter. Sisipkan **sesudah baris 38** (`</div>` penutup
   `.ak-toolbar`):
   ```blade
   <form method="GET" class="ak-filter" style="padding:14px 18px;margin-bottom:18px;">
       <select name="status" class="form-select" data-ak-auto-submit>
           <option value="">-- Semua Status --</option>
           <option value="aktif"  {{ request('status') == 'aktif'  ? 'selected' : '' }}>Aktif</option>
           <option value="draft"  {{ request('status') == 'draft'  ? 'selected' : '' }}>Draft</option>
           <option value="arsip"  {{ request('status') == 'arsip'  ? 'selected' : '' }}>Arsip</option>
       </select>
   </form>
   ```

**Kemana lagi harus dicek:**
- Atribut `data-ak-auto-submit` membuat dropdown **otomatis submit saat diubah**,
  tanpa tombol. Fungsinya sudah tersedia di
  `index.js:17-19`.
  👉 *Menemukan & memakai kembali kode yang sudah ada = nilai plus besar.*
- `request('status')` dipakai agar pilihan dropdown tetap terpilih setelah halaman
  di-reload.
- Method form harus `GET` (bukan `POST`) karena ini menyaring, bukan menyimpan —
  dan supaya filternya tersimpan di URL & bisa dibagikan.

**Cara uji:** pilih "Draft" → URL berubah jadi `?status=draft` dan tabel tersaring.
Klik halaman 2 pagination → filter harus tetap aktif (bukti `withQueryString()` bekerja).

**Pertanyaan lisan:**
- _"Apa beda `paginate()` dengan `get()`?"_ → `get()` mengambil semua data sekaligus;
  `paginate(15)` memecah jadi 15 per halaman + otomatis menyediakan navigasi halaman.
- _"Kenapa GET, bukan POST?"_ → GET untuk mengambil/menyaring data (aman diulang &
  bisa di-bookmark), POST untuk mengubah data.

---

### 🔴 SKENARIO 6 — Tambah field baru (END-TO-END)

**Tingkat:** Sulit · **Target waktu:** 20 menit · **Menguji:** SEMUA lapisan sekaligus

> ⭐ **INI SKENARIO PALING PENTING.** Kalau kalian hanya sempat menguasai satu skenario,
> pilih yang ini. Pola 5 langkahnya berlaku untuk **semua** permintaan "tambah field"
> di fitur apapun.

**Kata penguji:**
> _"Tambahkan field 'Penulis/Narahubung' pada pengumuman, supaya siswa tahu harus
> menghubungi siapa. Tampilkan juga di daftar."_

**Urutan wajib — hafalkan 5 langkah ini:**

```
[1] MIGRATION  → tambah kolom di database
[2] MODEL      → daftarkan di $fillable
[3] CONTROLLER → tambah aturan validasi (di Store DAN Update)
[4] FORM BLADE → tambah input
[5] INDEX BLADE→ tampilkan datanya
```

**Langkah 1 — Buat migration**

```bash
php artisan make:migration tambah_narahubung_ke_tabel_pengumuman --table=pengumuman
```

Buka file baru di `database/migrations/` (paling bawah, paling baru), isi:
```php
public function up(): void
{
    Schema::table('pengumuman', function (Blueprint $table) {
        $table->string('narahubung')->nullable()->after('isi_pengumuman');
    });
}

public function down(): void
{
    Schema::table('pengumuman', function (Blueprint $table) {
        $table->dropColumn('narahubung');
    });
}
```
Jalankan:
```bash
php artisan migrate
```

> ⚠️ **JANGAN pernah menjalankan `php artisan migrate:fresh`** — perintah itu
> **menghapus seluruh isi database**. Kalau data demo sidang hilang, selesai.
> Cukup `php artisan migrate` (hanya menjalankan yang baru).
>
> - `->nullable()` = boleh kosong. **Wajib** untuk kolom baru, kalau tidak, data lama
>   yang sudah ada akan menolak/gagal.
> - `->after('isi_pengumuman')` = posisi kolom, hanya kosmetik.
> - `down()` = cara membatalkan (`php artisan migrate:rollback`). Penguji suka menanyakan ini.

**Langkah 2 — Daftarkan di Model**

`app/Models/Pengumuman.php:14-24` — tambahkan
`'narahubung'` ke dalam array `$fillable`:
```php
protected $fillable = [
    'kalender_akademik_id',
    'dibuat_oleh',
    'judul',
    'isi_pengumuman',
    'narahubung',        // ← tambahkan
    'tanggal_pengumuman',
    ...
];
```

> ⚠️ **Kalau langkah ini dilewat, data tidak akan tersimpan dan TIDAK ADA pesan error.**
> Ini jebakan paling klasik di Laravel — pasti membuat kalian bingung 20 menit kalau lupa.
> **`$fillable` adalah daftar kolom yang boleh diisi massal** lewat `create()`/`update()`.
> Gunanya melindungi dari *mass assignment*: pengguna nakal tidak bisa menyisipkan
> kolom seperti `role` atau `is_admin` lewat form.
> 👉 *Ini pertanyaan lisan favorit penguji. Hafalkan jawabannya.*

**Langkah 3 — Controller (DUA tempat)**

Di `pengumumanStore` `baris 676-684`,
tambahkan ke array validasi:
```php
'narahubung' => 'nullable|string|max:100',
```
**Ulangi di `pengumumanUpdate`** `baris 715-723`.

> Karena controller memakai `Pengumuman::create($validated)`, field yang **tidak
> ditulis di aturan validasi tidak akan ikut tersimpan** — meskipun sudah ada di
> `$fillable` dan sudah dikirim form. Dua-duanya harus ada.

**Langkah 4 — Input di form**

`form.blade.php` — sisipkan
**sesudah baris 80** (`</div>` penutup grup Isi Pengumuman):
```blade
<!-- Narahubung -->
<div class="form-group">
    <label for="narahubung" class="form-label">
        Narahubung <small class="text-muted">(Opsional)</small>
    </label>
    <input type="text"
           class="form-control @error('narahubung') is-invalid @enderror"
           id="narahubung"
           name="narahubung"
           value="{{ old('narahubung', $pengumuman->narahubung ?? '') }}"
           placeholder="Contoh: Bu Ani - 0812xxxx">
    @error('narahubung')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

> **Perhatikan 4 hal yang harus konsisten** (tiru persis pola field lain):
> 1. `name="narahubung"` **harus sama persis** dengan nama kolom & nama di validasi.
>    Kalau beda, data tidak masuk.
> 2. `old('narahubung', $pengumuman->narahubung ?? '')` → isi ulang saat validasi gagal,
>    dan tampilkan data lama saat mode edit. `?? ''` wajib karena file form ini
>    **dipakai bersama** untuk Tambah (belum ada `$pengumuman`) dan Edit.
> 3. `@error(...) is-invalid` → memberi border merah saat salah.
> 4. `id` dan `for` di label harus sama, supaya label bisa diklik.

**Langkah 5 — Tampilkan di daftar**

`index.blade.php` — di dalam
sel utama, sesudah baris 99 (`<div class="ak-sub">...`):
```blade
@if($item->narahubung)
    <div class="ak-sub"><i class="fas fa-user"></i> {{ $item->narahubung }}</div>
@endif
```

**Cara uji (uji berurutan, jangan lompat):**
1. `php artisan migrate` sukses tanpa error.
2. Buka form Tambah → field baru muncul.
3. Isi dan simpan → **kembali ke daftar, data narahubung muncul.**
   Kalau kosong padahal sudah diisi → **99% karena lupa `$fillable`** (Langkah 2).
4. Klik Edit → nilai lama harus muncul di input.
5. Ubah, simpan → perubahan tersimpan. Kalau tidak → **lupa ubah `pengumumanUpdate`**.

**Pertanyaan lisan:**
- _"Apa itu `$fillable`?"_ → (jawaban di Langkah 2)
- _"Kenapa harus `nullable()` di migration?"_ → agar baris data yang sudah ada
  sebelumnya tetap sah; kalau `NOT NULL` tanpa default, migration gagal.
- _"Bedanya `migrate` dan `migrate:fresh`?"_ → `migrate` menjalankan migration baru saja;
  `migrate:fresh` **menghapus semua tabel** lalu membangun ulang dari nol (data hilang).

---

### 🟡 SKENARIO 7 — Ubah tampilan (CSS scoped)

**Tingkat:** Sedang · **Target waktu:** 8 menit · **Menguji:** CSS + paham arsitektur aset

**Kata penguji:**
> _"Badge prioritas 'Mendesak' warnanya kurang menonjol. Buat jadi merah dengan
> teks putih. Dan kartu statistik terlalu besar, kecilkan sedikit."_

**File:** `resources/css/sekretaris/pengumuman/index.css`

**Langkah:**

1. **Temukan file CSS-nya lewat Blade dulu** (jangan menebak):
   `index.blade.php:12` →
   `@vite('resources/css/sekretaris/pengumuman/index.css')`.
   👉 *Lakukan ini di depan penguji — menunjukkan kamu paham alurnya, bukan hafalan.*

2. Cari selector `.ak-badge` di file CSS (`Ctrl+F`).

3. Tambahkan/ubah aturan warna:
   ```css
   .ak-badge.danger {
       background: #fee2e2;
       color: #b91c1c;
       border-color: #fecaca;
   }
   ```
   Kalau diminta merah penuh dengan teks putih:
   ```css
   .ak-badge.danger {
       background: #dc2626;
       color: #ffffff;
       border-color: #dc2626;
   }
   ```

4. Untuk mengecilkan kartu statistik, cari `.ak-stat` dan kurangi `padding`-nya.

**Kemana lagi harus dicek:**
- **Kelas badge yang tersedia hanya 6**: `primary`, `success`, `warning`, `danger`,
  `info`, `muted` (`index.css:286-291`).
  Kalau Blade memakai nama kelas di luar keenam itu (misal `secondary` atau `dark`),
  badge-nya muncul **tanpa warna** — dan tidak ada error apapun.
  👉 *Ini contoh sempurna "kenapa harus tahu 2 lapisan sekaligus": Blade benar,
  tapi kalau CSS-nya tidak punya kelas itu, hasilnya tetap salah.*
- Perubahan warna di sini **tidak akan terlihat** kalau kelas yang dipasang Blade
  bukan `danger` — telusuri dulu `$priorityClass` di
  `index.blade.php:82-87`.
- **Semua CSS di halaman ini dibungkus scope halaman.** Kalau menambah aturan baru,
  taruh di dalam file CSS halaman yang bersangkutan — **jangan** menaruh di
  `resources/css/app.css`, karena akan bocor ke seluruh aplikasi.

**Cara uji:**
- Kalau `npm run dev` sedang jalan → langsung berubah (hot reload).
- Kalau tidak → jalankan `npm run build`, lalu refresh dengan `Ctrl+F5` (hard refresh,
  untuk melewati cache browser).

**Pertanyaan lisan:**
- _"Kenapa CSS-nya dipisah per halaman, tidak jadi satu file besar?"_
  → **Jawab:** supaya (1) tiap halaman hanya memuat gaya yang dibutuhkan → lebih ringan;
  (2) tidak terjadi bentrok antar halaman; (3) mudah dicari saat perbaikan karena struktur
  foldernya mengikuti struktur route dan view.
- _"Kalau saya ubah warna di sini, halaman lain ikut berubah?"_
  → Tidak, karena file ini hanya dipanggil oleh halaman ini lewat `@vite`.

---

### 🟡 SKENARIO 8 — Tambah interaksi JavaScript

**Tingkat:** Sedang · **Target waktu:** 12 menit · **Menguji:** JS DOM, event

**Kata penguji:**
> _"Tambahkan kotak pencarian di atas tabel, yang langsung menyaring baris saat diketik,
> tanpa reload halaman."_

**File:** `index.blade.php` +
`index.js`

**Langkah:**

1. **Tambah input di Blade** — sisipkan sesudah baris 64 (di dalam `.ak-panel`,
   sebelum `@if($items->count() > 0)`):
   ```blade
   <div style="padding:0 18px 14px;">
       <input type="text"
              id="cariPengumuman"
              class="form-control"
              placeholder="Ketik untuk mencari judul pengumuman...">
   </div>
   ```

2. **Tambah logika di JS** — buka
   `index.js`, sisipkan **di dalam**
   blok `DOMContentLoaded` (sebelum `});` penutup di baris 77):
   ```javascript
   const inputCari = document.getElementById('cariPengumuman');

   if (inputCari) {
       inputCari.addEventListener('keyup', () => {
           const kata = inputCari.value.toLowerCase();

           document.querySelectorAll('.ak-table tbody tr').forEach((baris) => {
               const judul = baris.querySelector('.ak-title')?.textContent.toLowerCase() || '';
               baris.style.display = judul.includes(kata) ? '' : 'none';
           });
       });
   }
   ```

**Penjelasan tiap baris (siapkan untuk ditanya):**
- `document.getElementById('cariPengumuman')` → ambil elemen input berdasarkan id.
- `if (inputCari)` → pengaman: file JS ini juga dipakai halaman lain (berita, flyer)
  yang tidak punya input ini. Tanpa pengaman, JS akan error di halaman tersebut.
  👉 *Menyebut alasan ini = nilai plus besar.*
- `addEventListener('keyup', ...)` → jalankan setiap kali tombol keyboard dilepas.
- `querySelectorAll('.ak-table tbody tr')` → ambil semua baris tabel.
- `?.` (optional chaining) → mencegah error kalau `.ak-title` tidak ditemukan.
- `style.display = '' / 'none'` → tampilkan / sembunyikan baris.

**Kemana lagi harus dicek:**
- **File JS ini dipakai bersama beberapa halaman** (pengumuman, berita, flyer — lihat
  komentar di `baris 1`). Perubahan di
  sini berpotensi memengaruhi halaman lain → itulah kenapa pengaman `if (inputCari)` wajib.
- **Keterbatasan yang harus kamu sebut sendiri:** pencarian ini hanya menyaring baris
  **di halaman yang sedang tampil** (15 data), bukan seluruh database. Kalau penguji
  minta cari ke seluruh data, jawabannya: pakai filter di sisi server seperti Skenario 5
  (`->where('judul', 'like', "%{$request->cari}%")`).
  👉 *Menyebutkan keterbatasan solusimu sendiri = tanda paham, bukan kelemahan.*
- Setelah mengubah JS → **wajib `npm run build`** (kalau tidak sedang `npm run dev`).

**Cara uji:** ketik sebagian judul → baris lain hilang. Kosongkan → semua muncul lagi.

**Pertanyaan lisan:**
- _"Kenapa dibungkus `DOMContentLoaded`?"_ → agar JS baru berjalan setelah seluruh
  elemen HTML selesai dimuat. Kalau tidak, `getElementById` bisa mendapat `null`
  karena elemennya belum ada.
- _"Bedanya ini dengan filter di Skenario 5?"_ → Ini di **browser** (cepat, tapi hanya
  data yang sudah tampil). Skenario 5 di **server** (query database, semua data, tapi
  perlu reload).

---

### 🔴 SKENARIO 9 — Buat halaman baru dari nol (GOTCHA VITE)

**Tingkat:** Sulit · **Target waktu:** 20 menit · **Menguji:** seluruh alur + jebakan Vite

> ⚠️ **Skenario ini mengandung jebakan yang PALING MUNGKIN membuat kalian panik di
> depan penguji.** Latih sampai hafal.

**Kata penguji:**
> _"Buatkan halaman detail pengumuman. Saat judul diklik di daftar, tampilkan halaman
> berisi isi lengkap pengumuman itu."_

**Urutan wajib — 5 langkah:**

```
[1] ROUTE       → daftarkan URL
[2] CONTROLLER  → buat method
[3] VIEW        → buat file blade
[4] CSS/JS      → buat file aset
[5] VITE.CONFIG → DAFTARKAN ASET  ← ⚠️ PALING SERING TERLUPA
```

**Langkah 1 — Route**

`routes/web.php`, di dalam grup `pengumuman` (**baris 933-939**).
Sisipkan **sesudah baris 934** (`Route::get('/', ...)->name('index');`):
```php
Route::get('/{id}', [SekretarisController::class, 'pengumumanShow'])->name('show');
```

> ⚠️ **Route `/{id}` harus diletakkan SETELAH `/create`.** Kalau ditaruh sebelumnya,
> Laravel akan menganggap kata "create" sebagai `{id}`, sehingga tombol Tambah rusak.
> 👉 *Ini pertanyaan jebakan klasik. Kalau kamu menyebutnya duluan, penguji terkesan.*

**Langkah 2 — Controller**

`SekretarisController.php` —
sisipkan sesudah `pengumumanIndex` (baris 662):
```php
public function pengumumanShow($id)
{
    $pengumuman = Pengumuman::with(['kalenderAkademik', 'pembuat'])->findOrFail($id);

    return view('sekretaris.pengumuman.show', compact('pengumuman'));
}
```
> `findOrFail()` → kalau id tidak ada, otomatis tampil halaman 404 (bukan error putih).
> Bedanya dengan `find()`: `find()` mengembalikan `null` dan halaman akan error.

**Langkah 3 — View**

Buat file **baru**: `resources/views/sekretaris/pengumuman/show.blade.php`
```blade
@extends('layouts.sneat')

@section('title', 'Detail Pengumuman')
@section('page-title', 'Detail Pengumuman')
@section('page-subtitle', 'Informasi lengkap pengumuman')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/sekretaris/pengumuman/show.css'])
@endsection

@section('content')
<div class="pengumuman-show-page">
    <div class="content-card">
        <h3>{{ $pengumuman->judul }}</h3>

        <p class="text-muted">
            {{ $pengumuman->tanggal_pengumuman?->format('d F Y') ?? '-' }}
            &middot; Dibuat oleh {{ $pengumuman->pembuat->name ?? '-' }}
        </p>

        <span class="ak-badge">{{ $pengumuman->prioritas_badge['label'] }}</span>

        <hr>

        <div class="pengumuman-isi">
            {!! nl2br(e($pengumuman->isi_pengumuman)) !!}
        </div>

        <a href="{{ route('sekretaris.pengumuman.index') }}" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>
@endsection
```

> **`{!! nl2br(e($isi)) !!}` — siap-siap ditanya, ini pertanyaan keamanan favorit:**
> - `e()` = escape, mengubah tag HTML jadi teks biasa → **mencegah serangan XSS**
>   (penyerang menyisipkan `<script>` lewat form).
> - `nl2br()` = mengubah enter menjadi `<br>` supaya paragraf tidak menyatu.
> - `{!! !!}` = mencetak tanpa escape (dibutuhkan agar `<br>` bekerja).
> - **Urutannya wajib `e()` dulu baru `nl2br()`.** Kalau langsung `{!! $isi !!}` tanpa
>   `e()`, aplikasi **rentan XSS**.
> - `{{ }}` biasa sudah otomatis aman (auto-escape) — itulah kenapa default-nya dipakai
>   di mana-mana.

**Langkah 4 — File CSS**

Buat file **baru**: `resources/css/sekretaris/pengumuman/show.css`
```css
.pengumuman-show-page .content-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
}

.pengumuman-show-page .pengumuman-isi {
    line-height: 1.8;
    color: #334155;
}
```

**Langkah 5 — ⚠️ DAFTARKAN DI VITE (JANGAN SAMPAI LUPA)**

Buka `vite.config.js`. Cari baris 52-53:
```js
'resources/css/sekretaris/pengumuman/index.css',
'resources/css/sekretaris/pengumuman/form.css',
```
Tambahkan di bawahnya:
```js
'resources/css/sekretaris/pengumuman/show.css',
```

**Kalau langkah 5 dilewat**, halaman akan menampilkan error:
```
Unable to locate file in Vite manifest: resources/css/sekretaris/pengumuman/show.css
```

👉 **Kalau error ini muncul di depan penguji, JANGAN PANIK.** Katakan:
> _"Ini karena file CSS baru belum terdaftar di vite.config.js. Project ini memakai Vite,
> jadi setiap aset baru harus didaftarkan dulu supaya masuk ke manifest saat build.
> Saya daftarkan sekarang."_
>
> Lalu tambahkan barisnya, jalankan `npm run build`, refresh. **Selesai.**
> Error yang bisa kamu jelaskan dan perbaiki justru **menaikkan** nilai.

Setelah menambah ke vite.config.js:
```bash
npm run build
```
> Kalau `npm run dev` sedang berjalan, **hentikan (Ctrl+C) lalu jalankan ulang** —
> perubahan `vite.config.js` tidak terbaca otomatis.

**Langkah 6 — Sambungkan dari daftar**

`index.blade.php baris 98` —
ubah dari:
```blade
<div class="ak-title">{{ $item->judul }}</div>
```
menjadi:
```blade
<div class="ak-title">
    <a href="{{ route('sekretaris.pengumuman.show', $item->id) }}">{{ $item->judul }}</a>
</div>
```

**Cara uji:** klik judul di daftar → halaman detail terbuka dengan gaya CSS-nya aktif.

**Pertanyaan lisan:**
- _"Apa itu Vite?"_ → Alat yang menggabungkan & mengoptimalkan CSS/JS. Saat build,
  ia membuat *manifest* (daftar file). `@vite()` mencari file di manifest itu — kalau
  belum terdaftar, tidak ketemu → error.
- _"Kenapa `route('sekretaris.pengumuman.show', $item->id)` bukan tulis URL langsung?"_
  → Karena kalau URL-nya berubah, cukup ubah di `routes/web.php`; semua link ikut
  menyesuaikan otomatis. Menulis URL manual berisiko rusak diam-diam.

---

### 🟢 SKENARIO 10 — Landing page publik (HTML/CSS/JS murni)

**Tingkat:** Mudah · **Target waktu:** 8 menit · **Menguji:** HTML/CSS tanpa logika Laravel

**Kata penguji:**
> _"Ubah tampilan halaman depan website — bagian ini warnanya/teksnya ganti."_

Halaman publik **tidak** memakai layout `sneat` dan **tidak** perlu login. File-nya ada
langsung di root `resources/views/`:

| Halaman | View | CSS | JS |
|---|---|---|---|
| Beranda | `resources/views/welcome.blade.php` atau `home.blade.php` | `resources/css/pages/home.css` | `resources/js/pages/home.js` |
| PPDB | `resources/views/ppdb.blade.php` | `resources/css/pages/ppdb.css` | `resources/js/pages/ppdb.js` |
| Kontak | `resources/views/kontak.blade.php` | `resources/css/pages/kontak.css` | `resources/js/pages/kontak.js` |
| Berita | `resources/views/berita.blade.php` | `resources/css/pages/berita.css` | — |
| Galeri | `resources/views/galeri.blade.php` | `resources/css/pages/galeri.css` | `resources/js/pages/galeri.js` |
| Fasilitas | `resources/views/fasilitas.blade.php` | `resources/css/pages/fasilitas.css` | `resources/js/pages/fasilitas.js` |
| Login | — | `resources/css/pages/login.css` | `resources/js/pages/login.js` |

Navbar & gaya bersama: `resources/css/navbar.css`, `resources/css/landing.css`,
`resources/js/navbar.js`.

**Langkah:** sama seperti Skenario 1 & 7 — buka Blade, cari teks dengan `Ctrl+F`,
cari kelas CSS-nya, ubah di file CSS yang sesuai.

**Kemana lagi harus dicek:**
- Sebagian konten landing page **bisa diatur lewat CMS** (menu Landing Page di panel
  admin, model `LandingPage` / `LandingPageSection`). Kalau teks tidak berubah padahal
  Blade sudah diedit → berarti teks itu **datang dari database**, bukan hardcode.
  👉 *Menyadari & menyebutkan ini = nilai plus besar.*
- File CSS/JS landing sudah terdaftar di `vite.config.js baris 510-536`.
  Kalau menambah **file baru**, berlaku aturan Skenario 9.

---

## BAB 7 — Dua belas skenario lanjutan per subsistem

Latihan di luar fitur Pengumuman, untuk area yang juga mungkin dibuka penguji.

> **Kerjakan setelah sepuluh skenario inti selesai.** Skenario di sini **sengaja tidak
> dituntun sedetail Bab 6** — tujuannya melatih kalian menerapkan Jurus Lacak sendiri.
> Kalau macet, kembali ke pola yang sama di Bab 6, lalu ganti nama berkasnya.

---

### PERAN DAN HAK AKSES

#### 🟢 L1 — Ubah pesan penolakan untuk akun nonaktif

**Tingkat:** Mudah · **Waktu:** 8 menit · **Menguji:** Middleware

> _"Kalau siswa yang sudah nonaktif mencoba masuk, pesannya kurang jelas. Perbaiki."_

**Berkas:** `app/Http/Middleware/EnsureUserIsActive.php` atau `CheckStudentActive.php`.
Ubah pesan `abort()` atau tujuan pengalihannya.

**Kemana lagi:** halaman penjelasannya ada di `resources/views/errors/account-inactive.blade.php`,
dan aset CSS/JS-nya sudah terdaftar di `vite.config.js` baris 25-26.

---

#### 🟡 L2 — Beri Ketua PKBM akses ke daftar tagihan

**Tingkat:** Sedang · **Waktu:** 15 menit · **Menguji:** Route + menu

> _"Ketua juga perlu melihat daftar tagihan bendahara. Buat bisa."_

Route tagihan ada di `routes/web.php` baris 976, di dalam grup `role:bendahara`.

Kuncinya: `CheckRole` **menerima banyak peran sekaligus** — lihat tanda tangan fungsinya,
`string ...$roles`. Jadi bisa ditulis `role:bendahara,ketua_pkbm`.

**⚠️ Yang sering terlupa:** menu di sidebar Ketua harus ditambah tautannya
(`resources/views/ketua/partials/sneat-sidebar-menu.blade.php`). Kalau tidak, fiturnya ada
tapi tidak terlihat — dan penguji akan mengira gagal.

---

### KEUANGAN

#### 🟢 L3 — Tambah jenis tagihan baru "Studi Tour"

**Tingkat:** Mudah · **Waktu:** 8 menit · **Menguji:** Model + formulir

1. Cari `getLabelJenis()` di `app/Models/Tagihan.php` baris 140, tambahkan
   `'studi_tour' => 'Studi Tour'`.
2. **Cek dulu tipe kolomnya** di migration tabel tagihan. Kalau `enum`, butuh migration
   untuk menambah nilai; kalau `string`, tidak perlu.
   👉 *Memeriksa ini lebih dulu bernilai tinggi di mata penguji.*
3. Tambahkan pilihannya di formulir tagihan dan di aturan validasi `storeCustom()`
   (baris 712).

---

#### 🟢 L4 — Tampilkan sisa tagihan yang harus dibayar

**Tingkat:** Mudah · **Waktu:** 10 menit · **Menguji:** Accessor

**Ini jebakan terbalik.** Fungsinya **sudah ada**: `getSisaPembayaranAttribute()` di
`app/Models/Tagihan.php` baris 89. Cukup panggil di Blade:

```blade
{{ number_format($tagihan->sisa_pembayaran) }}
```

👉 **Menemukan bahwa fungsinya sudah tersedia, alih-alih menulis ulang, justru yang dinilai
tinggi.** Kalau kalian menulis logika baru padahal sudah ada, penguji melihat kalian tidak
mengenal kode sendiri.

---

#### 🔴 L5 — Tambah penyaring status di daftar tagihan

**Tingkat:** Sulit · **Waktu:** 20 menit · **Menguji:** Query

Polanya sama persis dengan Skenario 5 di Bab 6, tapi pada
`app/Http/Controllers/Bendahara/TagihanController.php` baris 43.

**⚠️ Hati-hati:** fungsi `index()` di sini **panjang** (baris 43-205) dan **sudah punya
penyaring lain**. Sisipkan mengikuti pola yang ada — jangan menimpa, jangan menaruh di
tengah blok orang lain.

---

### AKADEMIK

#### 🟡 L6 — Ubah nilai KKM jadi 75

**Tingkat:** Sedang · **Waktu:** 12 menit · **Menguji:** Paham konfigurasi vs kode

**Jawaban paling benar: jangan ubah kodenya.**

KKM di sistem ini **tidak ditulis di kode** — disimpan per mata pelajaran dan jenjang,
diatur lewat halaman admin (`/admin/akademik/promotion/kkm`). Kode di `getKKM()`
(`app/Services/PromotionService.php` baris 122) hanya menyediakan nilai cadangan.

Jawaban yang benar: _"Nilainya diubah lewat menu pengaturan KKM, bukan dengan mengubah kode."_

👉 **Menolak mengubah kode ketika seharusnya lewat pengaturan adalah jawaban yang dewasa** —
dan penguji sering sengaja mengetes ini.

---

#### 🟡 L7 — Tambah kolom catatan wali kelas di rapor

**Tingkat:** Sedang · **Waktu:** 12 menit · **Menguji:** Lima lapisan sekaligus

Ikuti pola lima langkah Skenario 6: migration → `$fillable` di `app/Models/Rapor.php`
baris 16 → validasi di `WaliKelas/RaporController.php` baris 331 → isian di formulir edit
→ tampilkan.

**⚠️ Khas rapor:** tampilan web dan **tampilan cetak PDF terpisah**. Kalau hanya mengubah
yang web, catatan tidak muncul di rapor tercetak. Cetakannya diproses `print()` di baris 783.

---

#### 🟡 L8 — Tambah status kehadiran baru "Dispensasi"

**Tingkat:** Sedang · **Waktu:** 12 menit · **Menguji:** Presensi

> _"Selain hadir, sakit, izin, dan alpha, sekolah butuh status 'dispensasi' untuk siswa
> yang mewakili lomba."_

**Berkas:** `app/Http/Controllers/WaliKelas/PresensiController.php` —
`inputHarian()` baris 364, `updatePresensi()` baris 203.

1. Cek kolom `status` di migration tabel presensi — kalau `enum`, butuh migration.
2. Tambahkan ke aturan validasi `in:...` di kedua fungsi itu.
3. Tambahkan pilihannya di tampilan input presensi.

**⚠️ Jangan lupa rekapnya.** Perhitungan rekap kehadiran menjumlahkan per status
(`total_sakit`, `total_izin`, `total_alpha`). Status baru **tidak akan terhitung** sampai
ditambahkan juga ke perhitungan itu — dan angka rekapnya akan **diam-diam salah, tanpa
pesan error apa pun**.

---

#### 🔴 L9 — Pahami dan ubah pengecekan jadwal bentrok

**Tingkat:** Sulit · **Waktu:** 18 menit · **Menguji:** Jadwal pelajaran

> _"Apa yang terjadi kalau saya membuat jadwal yang bentrok? Coba tunjukkan di mana kodenya."_

**Berkas kunci:** `app/Traits/JadwalPelajaranTrait.php` baris 248 — `checkConflicts()`.
Fungsi ini dipakai bersama oleh controller Admin, Waka, dan Wali Kelas, karena logikanya sama.

**Jawaban lengkap yang mengesankan:** sistem memeriksa **tiga jenis bentrok** sekaligus:

1. Guru yang sama mengajar di dua tempat pada jam yang sama.
2. Kelas yang sama dipakai dua mata pelajaran.
3. Jadwal menabrak **jam istirahat** yang diatur per jenjang.

Kalau bentrok, sistem mengembalikan pengguna ke formulir dengan pesan yang menyebutkan
jenjang dan penyebabnya, dan **data tidak disimpan**.

👉 *Kenapa ditaruh di Trait?* Karena empat controller berbeda memakai logika yang sama —
kalau disalin ke tiap controller, perbaikan di satu tempat tidak ikut di tempat lain.

**Latihannya:** tambahkan pengecekan agar satu guru tidak boleh mengajar lebih dari 8 jam
pelajaran dalam sehari. Sisipkan di dalam `checkConflicts()`, mengikuti pola pengembalian
`['hasConflict' => true, 'message' => '...']`.

---

### LMS

#### 🟢 L10 — Aktifkan LMS untuk jenjang SMP

**Tingkat:** Mudah · **Waktu:** 10 menit · **Menguji:** Paham konfigurasi vs kode

Sama seperti L6, jawaban yang benar **bukan mengubah kode**: daftarnya disimpan di database
dengan kunci `lms_allowed_jenjang`, diatur lewat halaman admin LMS Settings.

Tunjukkan pembacaannya di `app/Http/Middleware/CheckLmsAccess.php` baris 36.

---

#### 🟡 L11 — Tampilkan durasi pengerjaan ujian

**Tingkat:** Sedang · **Waktu:** 15 menit · **Menguji:** Perhitungan waktu

Waktu mulai dan selesai **sudah tercatat** — diisi `mulai()` (baris 145) dan
`selesaikanUjian()` (baris 269) di `app/Http/Controllers/Siswa/LmsUjianController.php`.

Jadi tinggal dihitung selisihnya lalu ditampilkan di tampilan hasil
(`Guru/GuruUjianController.php` baris 376):

```php
$durasi = $mulai->diffInMinutes($selesai);
```

---

### CETAK PDF

#### 🟡 L12 — Ubah tampilan dokumen cetak

**Tingkat:** Sedang · **Waktu:** 12 menit · **Menguji:** dompdf

> _"Tambahkan kop sekolah di kwitansi pembayaran."_ atau
> _"Ubah ukuran kertas rapor jadi F4."_

Semua cetakan memakai pustaka dompdf dengan pola yang sama:

```php
$pdf = Pdf::loadView('nama.view.cetak', compact('data'));
return $pdf->stream('nama-berkas.pdf');
```

**Titik cetak yang ada di sistem:**

| Dokumen | Berkas &amp; baris |
|---|---|
| Rapor siswa | `Siswa/SiaRaporController.php:155` |
| Nilai per siswa | `WaliKelas/NilaiController.php:305` |
| Nilai satu kelas | `WaliKelas/NilaiController.php:356` |
| Bukti pembayaran | `Siswa/SiaPembayaranController.php:158` |
| Kalender akademik | `Sekretaris/SekretarisController.php:542, 569` |
| Jadwal pelajaran | `Admin/JadwalPelajaranController.php:730` |

**⚠️ Tiga hal khas PDF yang wajib diketahui:**

1. **Tampilan PDF terpisah dari tampilan web.** Mengubah halaman web tidak mengubah hasil cetak.
2. **dompdf tidak mendukung semua CSS.** Flexbox dan grid sering tidak bekerja — pakai tabel
   untuk tata letak. Ini penyebab paling umum "kenapa PDF-nya berantakan padahal di browser rapi".
3. **Gambar harus memakai alamat berkas lokal**, bukan alamat web, supaya terbaca saat
   dokumen dibuat di server.

---
## BAB 8 — Peran dan route

Seluruh route ada di **satu file**: `routes/web.php` (~1.600 baris).
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

Lihat `routes/web.php:993-1007`. Route spesifik
(`/generate-spp`, `/carryover`, `/api/...`) sengaja ditulis **di atas** route
bervariabel `/{siswa}` (baris 1003).

Kalau `/{siswa}` ditaruh lebih dulu, Laravel akan menganggap kata `"carryover"`
sebagai nilai `{siswa}` → fitur carryover rusak.
👉 **Ini contoh nyata di project kalian sendiri.** Kalau penguji bertanya soal urutan
route, tunjuk baris ini. Jawaban yang menunjuk kode asli jauh lebih kuat daripada teori.

---

### Autentikasi dan pengecekan hak akses

Subsistem paling mungkin ditanya karena jadi fondasi semua yang lain.

### Peta file

| Peran | File | Baris penting |
|---|---|---|
| Route login/logout | `routes/web.php` | 134 (form), 135 (proses), 191 (logout) |
| Controller login | `app/Http/Controllers/Auth/LoginController.php` | `create()`, `store()`, `destroy()` |
| **Cek hak akses** | `app/Http/Middleware/CheckRole.php` | seluruh file (55 baris) |
| Model user | `app/Models/User.php` | `roleRelation()` 63, `isAdmin()` 116 |
| Model role | `app/Models/Role.php` | punya kolom `level` |
| Daftar middleware | `bootstrap/app.php` | alias `role`, `superadmin`, dll |

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
lihat `CheckRole.php:33-42`:

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

**1. Admin bypass semua role** — `CheckRole.php:28-30`
```php
if ($user->isAdmin()) {
    return $next($request);
}
```
> **Alasan:** admin adalah level 1 (tertinggi) dan bertugas menangani seluruh sistem
> saat ada masalah di peran manapun. Tanpa bypass, admin harus didaftarkan ulang ke
> setiap grup route — rawan ada yang terlewat dan justru menimbulkan celah.

**2. Gagal otorisasi TIDAK me-logout pengguna** — `CheckRole.php:46-50`
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

## BAB 9 — Subsistem Keuangan

Subsistem paling kompleks — dan paling sering digali penguji karena menyangkut uang.

### Peta file

| Bagian | File | Baris penting |
|---|---|---|
| Route bendahara | `routes/web.php` | grup 969; tagihan 975-1008; pembayaran 1011-1026 |
| Route wali siswa | `routes/web.php` | grup 1542; tagihan 1548-1552; Midtrans 1555-1560 |
| **Model Tagihan** | `app/Models/Tagihan.php` | relasi 34-63; `scopeBelumLunasOriginal()` 69; `sisa_pembayaran` 89; **`updateStatusBayar()` 103**; `getLabelJenis()` 140 |
| **Model Pembayaran** | `app/Models/Pembayaran.php` | `tagihan()` 42, `siswa()` 47, `validator()` 52 |
| Controller tagihan | `Bendahara/TagihanController.php` | `index()` 43; `bulkCreate()` 528; `generateSpp()` 873; `duplicate()` 1069; carryover 1195-1242 |
| Controller pembayaran | `Bendahara/PembayaranController.php` | `index()` 22; `show()` 151; **`validasi()` 164**; `create()` 299; `store()` 358; `cetakKwitansi()` 576 |
| **Midtrans (service)** | `app/Services/MidtransService.php` | `createSnapToken()` 61; `buildTransactionParams()` 125; **`verifySignature()` 171**; `mapTransactionStatus()` 204 |
| **Webhook** | `MidtransWebhookController.php` | `notification()` 23; cek tanda tangan 42; update 71; audit 81 |
| Carryover tunggakan | `TunggakanCarryoverService.php` | `getKandidatTunggakan()` 30; `previewCarryover()` 92; `executeCarryover()` 123 |
| Sisi wali siswa | `OrangTua/OrangTuaController.php` | `tagihanAnak()`, `prosesBayar()`, `snapPayment()` |
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

`app/Models/Tagihan.php` baris 103 — `updateStatusBayar()`. **Hafalkan logikanya:**

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
> digital** di `MidtransWebhookController:42`.
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

**Solusi di sistem ini** — `TunggakanCarryoverService`:

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
> `belumLunasOriginal()` yang menyaring
> `whereNull('dialihkan_ke_id')` — tagihan lama yang sudah dialihkan otomatis
> dikecualikan dari perhitungan. Jadi hanya dihitung satu kali.

> **"Kalau tagihan barunya dilunasi, tagihan lama jadi apa?"**
>
> **Jawab:** ikut ditandai lunas secara otomatis. Lihat
> `Tagihan.php:127-132` — saat tagihan carryover
> berubah jadi `sudah_bayar`, status itu dipropagasikan ke `tagihan_asal_id`.
> Tujuannya agar riwayat tahun lama tetap konsisten dan **jejak audit tetap utuh**
> (datanya tidak dihapus, hanya ditandai).

## BAB 10 — Subsistem Akademik (SIA)

### Peta file

| Bagian | File | Baris penting |
|---|---|---|
| Route wali kelas | `routes/web.php` | grup 1090 |
| Route guru | `routes/web.php` | grup 1218 |
| **Model Nilai** | `app/Models/Nilai.php` | `COMPONENT_FIELDS` 14-20; `$fillable` 22-50 |
| Model Rapor | `app/Models/Rapor.php` | `status` 29; `status_review_ketua` 36; terbit 76; draft 86 |
| Controller nilai | `WaliKelas/NilaiController.php` | `index()` 27; `edit()` 449; `update()` 519; `importExcel()` 713; **`syncFromGuru()` 799** |
| Controller rapor | `WaliKelas/RaporController.php` | `generateAll()` 137; `generateSingle()` 194; `update()` 331; **`terbitkan()` 590**; `kirimValidasi()` 1049; `print()` 783 |
| Controller presensi | `WaliKelas/PresensiController.php` | `index()` 82; `inputHarian()` 364; `validasiIzin()` 243; `prosesValidasiIzin()` 309 |
| **Sinkronisasi nilai** | `app/Services/NilaiSyncService.php` | `syncForSiswaMapel()` 29; `syncFromTugasSiswa()` 87; `syncFromUjianSiswa()` 106 |
| **Kenaikan kelas** | `app/Services/PromotionService.php` | `checkEligibility()` 19; `checkFinancial()` 46; `checkAcademic()` 78; `executeStudentPromotion()` 156; `rollbackStudent()` 389 |

### Struktur nilai (sering ditanya "kenapa kolomnya banyak sekali?")

`Nilai.php:14-20` mendefinisikan komponen penilaian:

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

Perhatikan `Nilai.php:44-49` — ada pasangan kolom
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
> `wali_terakhir_edit_at` (`baris 30-31`) untuk
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

`PromotionService@checkEligibility (19)`
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
> **Jawab:** bisa — `rollbackStudent()` (baris 389).
> Ini penting karena kenaikan kelas mengubah banyak data sekaligus, jadi harus ada
> jalan mundur. 👉 *Menyebut adanya rollback = nilai plus.*

### 🔧 Perbaikan terbaru: halaman Rekap Kenaikan Kelas (bukan bagian latihan)

Dua bug UI ditemukan & diperbaiki di `resources/views/admin/akademik/promotion/rekap.blade.php`
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
(baris 467, 478, 486, 503, 516, 526, 586-587, 605); mode historical sekarang
menampilkan alert read-only sebagai gantinya.

**Bug 2 — tombol "Cetak Laporan" mengambang di tengah header (desktop).**
Header tab Riwayat Eksekusi punya 3 anak flex langsung (judul, tombol cetak,
form filter) dengan `justify-content-between` — di layar lebar tombol cetak
jadi mengambang sendirian di antara judul dan filter. Diperbaiki dengan
mengelompokkan tombol cetak + form filter dalam satu wrapper
`.history-header-actions` (baris 98-99), sehingga header jadi 2 kelompok:
judul (kiri) dan aksi (kanan).

> Berbeda dari bug badge prioritas Pengumuman (BAB Pengumuman), dua bug ini
> **bukan** skenario latihan — sudah langsung diperbaiki di `add-cloudflare`
> (commit `3539509`) dan diterapkan ulang secara manual di `clean-production`
> (commit `53384ee`, karena riwayat file di branch itu sudah divergen). Dicatat
> di sini murni sebagai peta kode, siapa tahu penguji bertanya soal halaman ini.

## BAB 11 — Subsistem LMS

### Peta file

| Bagian | File | Baris penting |
|---|---|---|
| Route guru | `routes/web.php` | grup 1218 |
| Route siswa | `routes/web.php` | grup 1405 |
| **Gerbang akses LMS** | `app/Http/Middleware/CheckLmsAccess.php` | seluruh file (46 baris) |
| Materi | `Guru/GuruMateriController.php` · `Siswa/LmsMateriController.php` | — |
| Tugas | `Guru/GuruTugasController.php` · `Siswa/LmsTugasController.php` | — |
| **Ujian (guru)** | `Guru/GuruUjianController.php` | `store()` 98; `soal()` 533; `manageSoal()` 741; **`pengawasan()` 436**; `toggleStatus()` 973; `koreksiShow()` 1050 |
| **Ujian (siswa)** | `Siswa/LmsUjianController.php` | `show()` 23; **`mulai()` 145**; `submit()` 229; `selesaikanUjian()` 269; `autosave()` 449; `monitoring()` 510 |
| Forum | `Guru/GuruForumController.php` · `Siswa/LmsForumController.php` | — |
| Arsip konten | `app/Services/GuruLmsArsipService.php` | salin konten antar kelas |
| Monitoring LMS | `app/Services/LmsMonitoringService.php` | — |
| Fitur AI | `AiQuestionGeneratorService`, `AiGradingService`, `AiChatbotService` | prompt di `config/ai-prompts.php` |

### Gerbang akses LMS — `CheckLmsAccess.php`

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

## BAB 12 — Jembatan antar subsistem

Kalau penguji bertanya **"apa yang membuat sistem ini terintegrasi, bukan sekadar 3
aplikasi terpisah?"** — inilah jawabannya. Hafalkan bagian ini.

### `ValidasiAksesService` — keuangan mengunci akademik

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

## BAB 13 — Bank pertanyaan

Hafalkan **intinya**, jangan hafalkan kalimatnya — sampaikan dengan bahasa sendiri.

### Tentang arsitektur

**"Coba jelaskan alur sistem ini."**
> Pengguna membuka URL → dicocokkan di `routes/web.php` → dicek middleware role
> apakah berhak → masuk ke Controller sesuai role → Controller mengambil data lewat
> Model (Eloquent) → data dikirim ke View Blade → Blade menghasilkan HTML → tampil
> di browser dengan CSS & JS yang dimuat lewat Vite.

**"Kenapa controller-nya dipisah per role?"**
> Sistem ini punya 9 peran dengan hak akses berbeda. Dengan memisahkan controller,
> route, view, dan aset per role, kami mendapat 3 keuntungan: (1) hak akses lebih
> mudah dijaga karena tiap grup route punya middleware `role:` sendiri, (2) mudah
> dicari karena strukturnya konsisten, (3) perubahan di satu role tidak merusak role lain.

**"Apa itu middleware?"**
> Lapisan pemeriksa sebelum request masuk ke controller. Di sini yang utama
> `role:<nama>` untuk mengecek apakah pengguna berhak. Admin punya bypass ke semua
> role. Kalau tidak berhak, muncul 403 — dan penting: **403 tidak me-logout** pengguna,
> hanya menolak akses halaman itu.

**"Apa itu MVC?"**
> Model = data & aturan database. View = tampilan. Controller = penghubung yang
> mengatur logika. Tujuannya memisahkan urusan supaya tidak campur aduk dan mudah dirawat.

**"Kenapa logika berat ditaruh di folder Services?"**
> Supaya controller tetap ramping dan logika bisnis bisa dipakai ulang serta diuji
> terpisah. Contoh di sini: `MidtransService` (pembayaran), `PromotionService`
> (kenaikan kelas), `TunggakanCarryoverService` (tunggakan antar tahun ajaran).

### Tentang database

**"Apa itu migration? Kenapa tidak buat tabel manual di phpMyAdmin?"**
> Migration adalah struktur database yang ditulis sebagai kode, sehingga tercatat di
> Git dan bisa dijalankan ulang oleh siapapun di komputer manapun dengan hasil sama.
> Kalau manual, anggota tim lain tidak tahu ada perubahan apa.

**"Apa itu Eloquent?"**
> ORM Laravel — 1 model mewakili 1 tabel, sehingga query ditulis dengan PHP
> (`Pengumuman::where(...)->get()`) alih-alih SQL mentah. Lebih aman dari SQL injection
> dan lebih mudah dibaca.

**"Apa itu relasi `belongsTo` / `hasMany`?"**
> `belongsTo` = "milik satu". Contoh: satu pengumuman **dibuat oleh** satu user →
> `Pengumuman::pembuat()`. `hasMany` = "punya banyak", kebalikannya.

**"Apa itu masalah N+1?"**
> Kalau mengambil 15 pengumuman lalu di tiap baris memanggil `$item->pembuat->name`,
> Laravel akan query ke tabel users 15 kali (1 + 15 query). Solusinya `with(['pembuat'])`
> — semua diambil dalam 2 query saja.

### Tentang keamanan

**"Apa itu `@csrf`?"**
> Token keamanan di setiap form untuk mencegah **CSRF** — serangan di mana situs lain
> mengirim form ke aplikasi kita memakai sesi pengguna yang sedang login. Laravel
> menolak request POST tanpa token yang cocok.

**"Kenapa `{{ }}` bukan `{!! !!}`?"**
> `{{ }}` otomatis meng-escape HTML → aman dari **XSS**. `{!! !!}` mencetak mentah,
> hanya dipakai kalau memang perlu HTML, dan wajib dipastikan sumbernya aman
> (lihat Skenario 9).

**"Apa itu `$fillable`?"**
> Daftar kolom yang boleh diisi lewat `create()`/`update()` massal. Melindungi dari
> **mass assignment** — pengguna nakal menambah field tersembunyi di form untuk
> mengubah kolom sensitif seperti role.

**"Kenapa validasi ada di server, padahal HTML sudah `required`?"**
> Validasi HTML bisa dimatikan lewat developer tools browser. Validasi server tidak
> bisa dilewati pengguna, jadi itu yang menentukan.

### Tentang frontend

**"Apa itu Blade?"**
> Template engine Laravel. Isinya HTML biasa ditambah direktif seperti `@if`,
> `@foreach`, `{{ }}`. Dikompilasi jadi PHP biasa, jadi cepat.

**"Apa beda `@include` dan `@extends`?"**
> `@extends` = halaman ini memakai kerangka dari layout tertentu.
> `@include` = menyisipkan potongan view lain (misalnya sidebar) ke dalam halaman.

**"Apa itu `@section` / `@yield` / `@push`?"**
> `@section` mengisi bagian yang disediakan layout, `@yield` tempat menampilkannya di
> layout, `@push` menambahkan ke tumpukan (biasanya untuk script agar dimuat di akhir).

**"Kenapa pakai Bootstrap?"**
> Project memakai template admin Sneat berbasis Bootstrap 5, jadi komponen (modal,
> tabel, form, grid) sudah konsisten dan responsif tanpa membangun dari nol.

**"Apa itu Vite dan kenapa harus `npm run build`?"**
> Vite menggabungkan & mengecilkan CSS/JS untuk produksi, dan membuat manifest.
> Saat pengembangan `npm run dev` memberi perubahan langsung; sebelum deploy wajib
> `npm run build` supaya file produksinya terbentuk.

### Pertanyaan jebakan (siapkan mental)

**"Ini kode kalian sendiri atau dibuatkan AI?"**
> Jawab jujur dan tenang. Yang dinilai adalah **penguasaan**, bukan pengetikan.
> Contoh: _"Kami menggunakan bantuan AI untuk mempercepat penulisan kode, tapi
> rancangan alur, struktur peran, dan aturan bisnisnya kami yang tentukan, dan setiap
> bagian kami tinjau. Silakan minta saya menjelaskan atau mengubah bagian manapun."_
>
> **Kalimat terakhir itu kuncinya** — lalu buktikan dengan Skenario 1-10.
> Berbohong lalu gagal menjelaskan jauh lebih fatal daripada mengakui.

**"Coba matikan internet, masih jalan?"**
> Aplikasi berjalan lokal lewat Laragon. Yang perlu internet hanya fitur pihak ketiga:
> Midtrans (pembayaran), Turnstile (captcha), notifikasi WhatsApp/email, dan fitur AI.
> **Antisipasi: siapkan demo yang tidak bergantung internet.**

**"Kalau ada 1000 siswa, sistem ini kuat?"**
> Data sudah dibatasi dengan pagination (15 per halaman), relasi diambil dengan eager
> loading untuk menghindari N+1, dan query difilter per tahun ajaran aktif. Untuk skala
> lebih besar, langkah berikutnya menambah index database dan cache pada laporan.

**"Apa kelemahan sistem kalian?"**
> Jangan bilang "tidak ada". Sebutkan yang jujur + rencana perbaikan. Contoh:
> sebagian validasi masih inline di controller (idealnya dipindah ke Form Request),
> cakupan automated test masih terbatas, dan beberapa halaman masih perlu penyelarasan
> nilai enum antara Blade dan migration (contohnya bug prioritas yang sudah kami perbaiki).
> 👉 *Mengakui kelemahan dengan spesifik = tanda menguasai.*

---

### Pertanyaan cepat tentang sistem ini

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

## BAB 14 — Pertanyaan di luar kode

Metodologi, pengujian, dan batasan sistem — bagian yang sering dilupakan saat latihan,
padahal hampir pasti ditanya.

> ## ⚠️ BACA INI DULU
>
> Bab ini **tidak bisa memberi jawaban jadi**, karena isinya bergantung pada naskah skripsi
> kalian sendiri — metode penelitian, rumusan masalah, dan bab pengujian yang kalian tulis.
> Yang diberikan di sini adalah **cara menjawab** dan **bukti yang sudah ada di proyek**
> untuk kalian tunjuk.
>
> **Cocokkan setiap jawaban dengan naskah kalian sebelum dipakai.** Kalau jawaban lisan
> berbeda dari yang tertulis di skripsi, itu justru jadi masalah baru.

---

### Bukti yang sudah tersedia di proyek

Ini penting: banyak pertanyaan metodologi bisa dijawab dengan **menunjuk berkas yang
benar-benar ada**, bukan berteori.

| Kalau ditanya soal | Tunjuk berkas ini |
|---|---|
| Rancangan basis data | `docs/ERD_DAN_TABEL_RELASI.md`, `docs/flow/erd-gabungan.puml` |
| Pengujian sistem | `docs/PENGUJIAN_BLACKBOX.md` — 28 bagian, mencakup seluruh peran |
| Pengujian keamanan | `postman/` — 6 uji negatif (login gagal, akses tanpa hak, token palsu) |
| Diagram alur &amp; use case | `docs/flow/*.puml` |
| Rancangan antarmuka | `docs/figma/` — token desain, inventaris komponen &amp; layar |
| Alur per peran | `docs/flow/<peran>.md` |
| Keamanan &amp; pengerasan | `docs/SECURITY_AUDIT.md`, `docs/HARDENING_CLOUDFLARE.md` |

---

### Pertanyaan metodologi

**"Metode pengembangan apa yang kalian pakai?"**
> **Jawab sesuai naskah kalian** (Waterfall, Prototyping, RAD, atau lainnya) — jangan
> mengarang di tempat. Lalu **buktikan dengan urutan kerja nyata**: analisis kebutuhan per
> peran, perancangan basis data, perancangan antarmuka, implementasi bertahap per
> subsistem, lalu pengujian black box. Kalau metodenya prototyping, sebutkan bahwa
> antarmuka dirancang lebih dulu lalu diperbaiki berdasarkan masukan pihak sekolah.

**"Kenapa memilih Laravel, bukan framework lain?"**
> Karena sistem ini punya sembilan peran dengan hak akses berbeda, dan Laravel menyediakan
> middleware serta sistem otorisasi bawaan yang matang. Ditambah Eloquent untuk relasi
> antar tabel yang banyak, sistem migration agar struktur database tercatat dan bisa
> direplikasi, dan ekosistem paket siap pakai untuk kebutuhan nyata sekolah: cetak PDF,
> impor/ekspor Excel, dan payment gateway.

**"Kenapa memakai MySQL?"**
> Karena datanya sangat relasional — siswa terhubung ke kelas, kelas ke jadwal, jadwal ke
> guru, tagihan ke pembayaran. Basis data relasional menjaga konsistensi lewat foreign key.
> Selain itu MySQL tersedia luas di layanan hosting sekolah dan sudah dikenal calon
> pengelola sistem.

**"Bagaimana pengumpulan datanya?"**
> Jawab sesuai naskah — biasanya wawancara dengan pihak PKBM, observasi proses yang
> berjalan, dan studi dokumen (format rapor, format tagihan, daftar mata pelajaran).
> **Sebutkan bukti konkret:** struktur nilai di sistem mengikuti format penilaian asli
> sekolah (lima tugas, lima latihan, lima ulangan harian, PTS, PAS), dan itu tidak mungkin
> dikarang sendiri.

**"Apa yang membedakan sistem ini dengan yang sudah ada?"**
> Tiga subsistem terpadu dalam satu aplikasi, bukan terpisah. Bukti keterpaduannya: status
> keuangan mengunci akses ujian dan rapor, nilai dari LMS otomatis masuk ke akademik, dan
> kenaikan kelas memeriksa keuangan sekaligus nilai. Ditambah fitur carryover tunggakan
> antar tahun ajaran yang jarang ada di sistem sekolah umum.

---

### Pertanyaan pengujian

**"Bagaimana kalian menguji sistem ini?"**
> Pengujian black box terhadap fungsi per peran — terdokumentasi di
> `docs/PENGUJIAN_BLACKBOX.md` dengan 28 bagian, mulai dari autentikasi, pembatasan hak
> akses, keuangan, LMS, sampai aktivitas orang tua. Dilengkapi pengujian endpoint memakai
> Postman untuk kasus negatif.

**"Apa itu pengujian black box?"**
> Pengujian yang menilai sistem **dari sisi pengguna** — diberi masukan tertentu, lalu
> diperiksa apakah keluarannya sesuai harapan, tanpa melihat kode di dalamnya. Lawannya
> white box, yang memeriksa alur logika di dalam kode.

**"Ada pengujian keamanan?"**
> Ada, berupa **uji negatif**: memastikan sistem menolak hal yang seharusnya ditolak.
> Contohnya login dengan kredensial salah, membuka halaman terbatas tanpa login, mengirim
> pembayaran tanpa autentikasi, dan mengakses dokumen dengan token yang bukan miliknya.
> Semua ada di koleksi Postman.

**"Ada automated test?"**
> **Jawab jujur:** ada kerangka pengujian bawaan Laravel di folder `tests/`, tapi
> **cakupannya masih terbatas**. Pengujian utama dilakukan secara manual dan
> terdokumentasi. Untuk pengembangan selanjutnya, prioritasnya menambah pengujian otomatis
> pada bagian keuangan karena di situ risiko kesalahannya paling besar.

**"Bagaimana kalau ada bug ditemukan?"**
> Contoh nyatanya ada: kami menemukan ketidaksesuaian nilai antara tampilan dan struktur
> database pada halaman pengumuman — kartu statistik selalu menampilkan nol. Kami telusuri
> penyebabnya, perbaiki di kedua halaman yang terdampak, dan catat pelajarannya.
>
> 👉 **Menceritakan satu bug nyata yang kalian temukan dan perbaiki jauh lebih meyakinkan
> daripada mengklaim tidak ada bug.** Detailnya ada di Skenario 3, Bab 6.

---

### Pertanyaan batasan dan pengembangan

**"Apa batasan sistem ini?"**
> Sebutkan yang jujur dan spesifik: pencegahan kecurangan ujian masih berupa deteksi dan
> pencatatan, bukan pemblokiran; pembayaran digital bergantung pada layanan pihak ketiga
> sehingga perlu koneksi internet; fitur AI bersifat pendukung dan hasilnya tetap perlu
> ditinjau manusia; serta sistem belum diuji pada beban pengguna sangat besar secara
> bersamaan.

**"Apa rencana pengembangan selanjutnya?"**
> Menambah cakupan pengujian otomatis terutama di keuangan, memecah controller yang terlalu
> panjang, memindahkan validasi ke berkas khusus agar konsisten, aplikasi mobile untuk
> orang tua, dan notifikasi yang lebih terjadwal.

**"Kalau sekolah lain mau pakai, bisa?"**
> Bisa, karena sudah ada dukungan multi-cabang, dan hal-hal yang berbeda tiap sekolah
> disimpan sebagai pengaturan di database — bukan ditulis di kode. Contohnya jenjang yang
> boleh memakai LMS, nilai KKM, informasi rekening pembayaran, dan konten halaman depan.
> Yang perlu disesuaikan hanya data awal dan pengaturannya.

**"Siapa yang akan merawat sistem ini setelah kalian lulus?"**
> Dokumentasi teknis sudah disiapkan di folder `docs/`, struktur kode mengikuti pola yang
> konsisten per peran sehingga mudah dipelajari pengembang baru, dan admin sekolah dapat
> mengubah sebagian besar konfigurasi tanpa menyentuh kode.

---

### Pola menjawab pertanyaan yang tidak kalian ketahui

Untuk pertanyaan di luar kode, godaan mengarang jauh lebih besar karena tidak ada berkas
yang bisa dibuka. Pakai pola ini:

**Akui batasnya → berikan yang kalian tahu → tawarkan cara memastikan.**

> _"Untuk angka pastinya saya belum hafal, Pak. Yang saya tahu strukturnya begini...
> Kalau diperlukan, saya bisa cek langsung di dokumentasi atau menjalankan perintahnya
> sekarang."_

---
## BAB 15 — Jadwal lima hari dan pembagian tim

> **Sidang: Jumat, 7 Agustus 2026.** Persiapan efektif **lima hari**, Minggu sampai Kamis.
> Jadwal ini sudah dipadatkan dari rencana tujuh hari — beberapa bagian sengaja dilewati,
> dan itu keputusan sadar, bukan kelalaian.

### Prinsip

- **Semua orang mengerjakan SEMUA skenario inti (Bab 6).** Tidak ada yang boleh bilang
  "itu bagian teman saya".
- Tapi setiap orang punya **1 bidang spesialis** untuk pertanyaan mendalam.
- **Latihan bersama, bergantian jadi "penguji".**
- **Kalau waktu mepet, korbankan keluasan — jangan korbankan Skenario 6 dan 9.**

### Jadwal

| Hari | Fokus | Target akhir hari |
|---|---|---|
| **Minggu (H-5)** | Selesaikan OOP: video **Class** dan **Inheritance** saja (±51 menit). Lalu buka `app/Models/Pengumuman.php` dan cocokkan tiap barisnya. Lanjut **Skenario 1 & 2** | Paham `class`, `$this`, `extends`. Sudah pernah mengubah tampilan sendiri |
| **Senin (H-4)** | **Bab 3 Jurus Lacak** sampai hafal (±30 menit) → **Skenario 3, 4, 5** | Menemukan berkas apa pun < 2 menit. Paham controller, validasi, query |
| **Selasa (H-3)** | **HARI TERPENTING. Skenario 6 (ulang 3×) dan Skenario 9 (ulang 2×)** — tidak ada agenda lain | Bisa tambah kolom baru & buat halaman baru **tanpa melihat dokumen** |
| **Rabu (H-2)** | **Skenario 7 & 8** (pagi). **Bab 13 + Bab 18** (siang). **Revisi PPT** sesuai tujuh butir hasil audit (sore) | Bisa menjawab tanpa "emmm...". **PPT sudah final** |
| **Kamis (H-1)** | **Gladi resik. JANGAN menulis kode baru.** `git status` bersih → `npm run build` → `php artisan test` → tentukan **urutan menu yang akan didemokan** → simulasi bergantian jadi penguji → cetak Bab 16 & 17 | Semua siap, laptop siap, cetakan siap. Tidur cukup |
| **Jumat** | **SIDANG** | — |

### Yang sengaja DILEWATI karena waktu

Ini keputusan sadar. Kalau nanti ada waktu sisa, baru kerjakan.

| Dilewati | Alasan |
|---|---|
| Video PHP #12, #13, #16 | Teknik SQL mentahnya **tidak dipakai** di proyek ini — sudah digantikan Eloquent |
| **Bab 7** (12 skenario lanjutan) | Polanya sama dengan Bab 6; kuasai yang inti dulu. Kecuali **L4 dan L6**, yang jawabannya bersifat konseptual dan cepat dibaca |
| **Bab 8-12** (peta subsistem) | Baca cepat saja. Untuk penjelasan mendalam, `docs/MANUAL_SISTEM.md` lebih cocok dan sudah tersedia |
| **Bab 2** (kamus istilah) | Jangan dijadikan sesi khusus — buka saat menemukan istilah asing |
| **Bab 17** (indeks halaman) | Tidak untuk dihafal. **Cetak** dan bawa sebagai lembar contekan |

### Prioritas kalau hari Selasa ternyata tidak cukup

Urutan yang tidak boleh dikorbankan, dari paling penting:

1. **Skenario 6** — pola lima lapisan, berlaku untuk permintaan "tambah field" di fitur mana pun
2. **Bab 3** — Jurus Lacak, supaya tidak pernah kebingungan mencari berkas
3. **Skenario 9** — jebakan Vite, satu-satunya yang bisa membuat panik di depan penguji
4. **Bab 18** — lima jawaban terkuat dan tiga hal yang harus diakui
5. Sisanya

### Pembagian spesialis (tetap semua belajar semua)

| Orang | Spesialis untuk pertanyaan mendalam | Wajib kuasai ekstra |
|---|---|---|
| **A** | Keuangan (Tagihan, Pembayaran, Midtrans) | `docs/flow/bendahara.md`, alur carryover tunggakan |
| **B** | Akademik/SIA (Siswa, Kelas, Jadwal, Nilai, Rapor, Kenaikan Kelas) | `docs/flow/wali-kelas.md`, `PromotionService` |
| **C** | LMS + Landing Page + role/keamanan | `docs/flow/guru.md`, middleware & sistem role |

> Semua tetap wajib menguasai **10 skenario inti di Bab 6** — itu bekal bersama.

### Latihan simulasi (H-2, cara mainnya)

1. Satu orang jadi penguji, buka laptop yang **tidak sedang menampilkan dokumen ini**.
2. Penguji memilih skenario secara **acak**, membacakan bagian "Kata penguji" saja.
3. Peserta mengerjakan **sambil menjelaskan langkahnya dengan suara keras**.
4. Penguji melempar 2 pertanyaan lisan dari skenario tersebut.
5. Nilai: ✅ selesai dalam waktu · ⚠️ selesai tapi lewat waktu · ❌ macet.
6. Yang ❌ diulang keesokan harinya.

---

## BAB 16 — Cheat sheet perintah dan sintaks

### Perintah terminal

```bash
# ── melacak ──────────────────────────────────────────
php artisan route:list                       # semua route
php artisan route:list --path=pengumuman     # saring per kata kunci
php artisan route:list --name=sekretaris     # saring per nama route

# ── membuat berkas ───────────────────────────────────
php artisan make:controller NamaController
php artisan make:model NamaModel
php artisan make:migration nama_migration --table=nama_tabel

# ── database ─────────────────────────────────────────
php artisan migrate                # jalankan migration baru  ✅ AMAN
php artisan migrate:rollback       # batalkan batch terakhir
php artisan migrate:status         # lihat mana yang sudah jalan
php artisan migrate:fresh          # ⛔ HAPUS SEMUA DATA — JANGAN saat sidang

# ── kalau error / aneh ───────────────────────────────
php artisan optimize:clear         # bersihkan semua cache
npm run build                      # bangun ulang CSS & JS
npm run dev                        # mode pengembangan (perubahan langsung terlihat)

# ── uji coba cepat ───────────────────────────────────
php artisan tinker                 # coba query langsung di terminal
>>> App\Models\Pengumuman::count()
>>> App\Models\Pengumuman::first()->judul
>>> exit

# ── git (penyelamat) ─────────────────────────────────
git status                         # apa yang berubah
git diff                           # detail perubahan
git checkout -- .                  # ⚠️ batalkan SEMUA perubahan belum disimpan
git stash                          # simpan sementara perubahan
git stash pop                      # kembalikan lagi
```

### Blade — yang paling sering dipakai

```blade
{{ $variabel }}                        {{-- cetak (otomatis aman dari XSS) --}}
{!! $html !!}                          {{-- cetak mentah — hati-hati XSS --}}
{{ $a ?? 'default' }}                  {{-- kalau null, pakai default --}}
{{ $obj?->kolom }}                     {{-- aman kalau $obj null --}}

@if($x) ... @elseif($y) ... @else ... @endif
@foreach($items as $item) ... @endforeach
@forelse($items as $item) ... @empty ... @endforelse

@csrf                                  {{-- WAJIB di setiap form POST --}}
@method('PUT')                         {{-- untuk update --}}
@method('DELETE')                      {{-- untuk hapus --}}

@error('nama_field') {{ $message }} @enderror
{{ old('nama_field', $default) }}

{{ route('sekretaris.pengumuman.index') }}
{{ route('sekretaris.pengumuman.edit', $item->id) }}
{{ asset('storage/' . $file) }}

@extends('layouts.sneat')
@section('content') ... @endsection
@include('sekretaris.partials.sneat-sidebar-menu')
@vite(['resources/css/.../file.css'])
```

### Eloquent — yang paling sering dipakai

```php
Model::all();                                  // semua data
Model::find($id);                              // cari 1, null kalau tidak ada
Model::findOrFail($id);                        // cari 1, 404 kalau tidak ada
Model::where('status', 'aktif')->get();        // saring
Model::where('judul', 'like', "%kata%")->get();// cari sebagian teks
Model::with(['relasi'])->get();                // eager loading (anti N+1)
Model::orderBy('kolom', 'desc')->get();        // urutkan
Model::paginate(15);                           // 15 per halaman
Model::count();                                // hitung jumlah

Model::create([...]);                          // simpan baru (butuh $fillable)
$data->update([...]);                          // ubah
$data->delete();                               // hapus
```

### Validasi — aturan yang sering dipakai

```php
'required'            // wajib diisi
'nullable'            // boleh kosong
'string' / 'integer' / 'date' / 'email'
'max:255'             // teks: 255 karakter | file: 255 KILOBYTE
'min:3'
'in:draft,aktif,arsip'          // hanya nilai tertentu
'exists:nama_tabel,id'          // harus ada di tabel lain
'unique:nama_tabel,kolom'       // tidak boleh duplikat
'file|mimes:pdf|max:2048'       // file PDF maks 2 MB
'image|mimes:jpg,png|max:2048'  // gambar maks 2 MB
```

### Alat bantu debugging (pakai saat buntu)

```php
dd($variabel);        // tampilkan isi variabel lalu HENTIKAN eksekusi
dump($variabel);      // tampilkan tapi program lanjut
dd($request->all());  // lihat SEMUA data yang dikirim form  ← paling berguna
```

> **Trik saat data tidak tersimpan:** taruh `dd($request->all());` di baris pertama
> method `Store`. Kalau field-mu tidak muncul di situ → masalahnya di `name=` pada
> form. Kalau muncul tapi tidak tersimpan → masalahnya di `$fillable` atau validasi.
> 👉 *Melakukan ini di depan penguji terlihat sangat menguasai.*

---

## BAB 17 — Indeks lokasi semua halaman

**Ini bab paling penting saat sidang berlangsung.** Bab lain untuk belajar; bab ini untuk
dibuka *saat* penguji menunjuk sesuatu.

---

### Kenapa bab ini ada

Sistem ini punya **124 tautan menu** dan **354 halaman**. Kalau tiap halaman dikalikan
sekitar lima jenis permintaan yang mungkin (ubah teks, tambah kolom, ubah validasi, ubah
tampilan, tambah field), hasilnya **lebih dari 1.700 kemungkinan**. Menulis jawaban satu
per satu mustahil, dan menghafalnya lebih mustahil lagi.

Tapi ada kabar baik, dan ini kuncinya:

> **Permintaan apa pun = satu LOKASI + satu POLA.**
>
> Polanya cuma **tujuh**, dan kalian sudah melatih ketujuhnya di Bab 6.
> Yang berubah hanya lokasinya — dan lokasi **bisa didaftar lengkap**. Itulah isi bab ini.

---

### Tujuh pola — hafalkan ini, bukan 1.700 skenario

| # | Kalau penguji minta... | Pola | Dilatih di |
|---|---|---|---|
| 1 | ubah tulisan, judul, label | Cari teksnya di Blade, ganti. Cek apakah muncul di beberapa tempat. | Skenario 1 |
| 2 | tambah kolom di tabel | Tambah `<th>` + `<td>`. Cek data sudah diambil controller atau belum. | Skenario 2 |
| 3 | ubah aturan validasi | Ubah di controller — **dua tempat**: `store` dan `update`. | Skenario 4 |
| 4 | saring / urutkan data | Ubah query di controller, tambah dropdown di Blade. | Skenario 5 |
| 5 | tambah field baru | Lima lapisan: migration → `$fillable` → validasi → form → tampilan. | Skenario 6 |
| 6 | ubah warna, ukuran, tata letak | Ubah berkas CSS halaman itu. | Skenario 7 |
| 7 | tambah interaksi tanpa reload | Ubah berkas JS halaman itu. | Skenario 8 |

Ditambah satu pola khusus: **membuat halaman baru** (Skenario 9), yang memakai kelima
langkah sekaligus plus pendaftaran aset di `vite.config.js`.

---

### Cara memakai indeks ini saat sidang

1. **Penguji menunjuk sebuah halaman.** Lihat alamat di bilah alamat browser, misalnya
   `/bendahara/tagihan`.
2. **Cari alamat itu di tabel bab ini.** Tabel dikelompokkan per peran dan diurutkan
   menurut alamat, jadi cepat ditemukan.
3. **Kalian langsung dapat empat hal:** berkas controller beserta nomor baris methodnya,
   berkas tampilan Blade, dan berkas CSS/JS-nya.
4. **Terapkan pola** dari tabel di atas sesuai jenis permintaannya.

Contoh lengkap. Penguji berkata: *"Di halaman daftar tagihan ini, tambahkan kolom tanggal
jatuh tempo."*

- Alamatnya `/bendahara/tagihan` → cari di tabel Bendahara.
- Dapat: controller `Bendahara/TagihanController.php` method `index()`, tampilan
  `resources/views/bendahara/tagihan/index.blade.php`, aset
  `resources/css/bendahara/tagihan/index.css`.
- Jenis permintaan = **pola 2** (tambah kolom tabel) → buka Skenario 2 di Bab 6, ikuti
  langkahnya, ganti nama berkasnya.

---

### Yang perlu diucapkan sambil mengerjakan

Jangan diam sambil mencari. Narasikan:

> _"Halaman ini alamatnya `/bendahara/tagihan`, berarti perannya bendahara. Controllernya
> `TagihanController` method `index`. Saya cek dulu apakah datanya sudah diambil di
> controller atau belum, supaya tahu perlu mengubah query atau cukup tampilannya saja."_

**Narasi ini sendiri sudah bernilai**, bahkan sebelum kodenya jadi.

---

### ⚠️ Tiga catatan sebelum memakai tabel

1. **Nomor baris bisa bergeser** kalau kode diedit. Kolomnya menyebut **nama method**
   (misalnya `index()`); kalau nomornya meleset, cari `function index` di berkas itu.
2. **Tanda `—` pada kolom aset** berarti halaman itu tidak memuat CSS/JS khusus — ia
   memakai gaya bawaan kerangka halaman. Kalau diminta mengubah tampilannya, berarti
   kalian perlu **membuat berkas CSS baru** dan mendaftarkannya di `vite.config.js`
   (lihat Skenario 9).
3. **Indeks ini hanya memuat halaman yang bisa dibuka** (`GET`). Tombol simpan, ubah, dan
   hapus tidak muncul di sini karena tidak punya halaman sendiri — methodnya ada di
   controller yang sama, biasanya bernama `store`, `update`, atau `destroy`.

---

### Sebaran halaman per peran

| Peran | Jumlah halaman |
|---|---|
| Admin | 106 |
| Wakil Kepala Sekolah | 54 |
| Guru Pengajar | 43 |
| Wali Kelas | 29 |
| Bendahara | 25 |
| Siswa | 25 |
| Halaman publik &amp; umum | 24 |
| Ketua PKBM | 23 |
| Sekretaris | 14 |
| Wali Siswa (orang tua) | 11 |
| **Total** | **354** |

> Admin punya paling banyak karena memang berhak mengakses seluruh menu peran lain.
> Kalau kalian bertiga membagi spesialis (lihat Bab 15), tiap orang cukup **menghafal
> letak** bagiannya — bukan menghafal isinya.

---

### Admin — 106 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/admin/ai-settings` | `Admin/AiSettingController.php`<br>`index()` : 13 | `resources/views/admin/ai-settings/index.blade.php` | `resources/css/admin/ai-settings/index.css`<br>`resources/js/admin/ai-settings/index.js` |
| `/admin/akademik/kenaikan-kelas/kkm` | `Admin/Akademik/PromotionKKMController.php`<br>`index()` : 15 | `resources/views/admin/akademik/promotion/kkm.blade.php` | `resources/css/admin/akademik/promotion/kkm.css`<br>`resources/js/admin/akademik/promotion/kkm.js` |
| `/admin/akademik/kenaikan-kelas/report` | `Admin/Akademik/PromotionReportController.php`<br>`index()` : 14 | `resources/views/admin/akademik/promotion/rekap.blade.php` | `resources/css/admin/akademik/promotion/rekap.css`<br>`resources/js/admin/akademik/promotion/rekap.js` |
| `/admin/akademik/kenaikan-kelas/report/print` | `Admin/Akademik/PromotionReportController.php`<br>`print()` : 227 | `resources/views/admin/akademik/promotion/print.blade.php` | `resources/css/admin/akademik/promotion/print.css`<br>`resources/js/admin/akademik/promotion/print.js` |
| `/admin/akademik/kenaikan-kelas/settings` | `Admin/Akademik/PromotionSettingsController.php`<br>`index()` : 14 | `resources/views/admin/akademik/promotion/settings.blade.php` | `resources/css/admin/akademik/promotion/settings.css` |
| `/admin/cabang` | `Admin/CabangController.php`<br>`index()` : 18 | `resources/views/admin/cabang/index.blade.php` | `resources/css/admin/cabang/index.css`<br>`resources/js/admin/cabang/index.js` |
| `/admin/cabang/create` | `Admin/CabangController.php`<br>`create()` : 55 | `resources/views/admin/cabang/create.blade.php` | `resources/css/admin/cabang/create.css`<br>`resources/js/admin/cabang/create.js` |
| `/admin/cabang/{cabang}` | `Admin/CabangController.php`<br>`show()` : 94 | `resources/views/admin/cabang/show.blade.php` | `resources/css/admin/cabang/show.css`<br>`resources/js/admin/cabang/show.js` |
| `/admin/cabang/{cabang}/edit` | `Admin/CabangController.php`<br>`edit()` : 137 | `resources/views/admin/cabang/edit.blade.php` | `resources/css/admin/cabang/edit.css`<br>`resources/js/admin/cabang/edit.js` |
| `/admin/cetak-laporan` | `Admin/CetakLaporanController.php`<br>`index()` : 20 | `resources/views/admin/cetak-laporan/index.blade.php` | `resources/css/admin/cetak-laporan/index.css`<br>`resources/js/admin/cetak-laporan/index.js` |
| `/admin/cetak-laporan/guru-pengajar` | `Admin/CetakLaporanController.php`<br>`guruPengajar()` : 236 | `resources/views/admin/cetak-laporan/print-guru-pengajar.blade.php` | — |
| `/admin/cetak-laporan/kelas` | `Admin/CetakLaporanController.php`<br>`kelas()` : 177 | `resources/views/admin/cetak-laporan/print-kelas.blade.php` | — |
| `/admin/cetak-laporan/rekap` | `Admin/CetakLaporanController.php`<br>`rekap()` : 295 | `resources/views/admin/cetak-laporan/print-rekap.blade.php` | — |
| `/admin/cetak-laporan/rekap-akademik` | `Admin/CetakLaporanController.php`<br>`rekapAkademik()` : 258 | `resources/views/admin/cetak-laporan/print-rekap-akademik.blade.php` | — |
| `/admin/cetak-laporan/siswa` | `Admin/CetakLaporanController.php`<br>`siswa()` : 51 | `resources/views/admin/cetak-laporan/print-siswa.blade.php` | — |
| `/admin/cetak-laporan/tenaga-pendidik` | `Admin/CetakLaporanController.php`<br>`tenagaPendidik()` : 144 | `resources/views/admin/cetak-laporan/print-guru.blade.php` | — |
| `/admin/cetak-laporan/wali-kelas` | `Admin/CetakLaporanController.php`<br>`waliKelas()` : 206 | `resources/views/admin/cetak-laporan/print-wali-kelas.blade.php` | — |
| `/admin/dashboard` | `Admin/DashboardController.php`<br>`index()` : 17 | `resources/views/dashboard/admin.blade.php` | `resources/css/dashboard/admin.css`<br>`resources/js/dashboard/admin.js` |
| `/admin/guru-pengajar` | `Admin/GuruPengajarController.php`<br>`index()` : 20 | `resources/views/admin/guru-pengajar/index.blade.php` | `resources/css/admin/guru-pengajar/index.css`<br>`resources/js/admin/guru-pengajar/index.js` |
| `/admin/guru-pengajar/kelas/{kelas}` | `Admin/GuruPengajarController.php`<br>`manageKelas()` : 139 | `resources/views/admin/guru-pengajar/manage-kelas.blade.php` | `resources/css/admin/guru-pengajar/manage-kelas.css` |
| `/admin/guru-pengajar/print` | `Admin/GuruPengajarController.php`<br>`print()` : 230 | `resources/views/admin/guru-pengajar/print.blade.php` | — |
| `/admin/guru-pengajar/{guruPengajar}` | `Admin/GuruPengajarController.php`<br>`show()` : 96 | `resources/views/admin/guru-pengajar/show.blade.php` | `resources/css/admin/guru-pengajar/show.css`<br>`resources/js/admin/guru-pengajar/show.js` |
| `/admin/jadwal-pelajaran` | `Admin/JadwalPelajaranController.php`<br>`index()` : 24 | `resources/views/admin/jadwal-pelajaran/index.blade.php` | `resources/css/admin/jadwal-pelajaran/index.css`<br>`resources/js/admin/jadwal-pelajaran/index.js` |
| `/admin/jadwal-pelajaran/create` | `Admin/JadwalPelajaranController.php`<br>`create()` : 147 | `resources/views/admin/jadwal-pelajaran/create.blade.php` | `resources/css/admin/jadwal-pelajaran/form.css`<br>`resources/js/admin/jadwal-pelajaran/form.js` |
| `/admin/jadwal-pelajaran/export-excel` | `Admin/JadwalPelajaranController.php`<br>`exportExcel()` : 815 | `resources/views/admin/jadwal-pelajaran/export-excel.blade.php` | — |
| `/admin/jadwal-pelajaran/export-pdf` | `Admin/JadwalPelajaranController.php`<br>`exportPdfAll()` : 893 | `resources/views/admin/jadwal-pelajaran/export-pdf.blade.php` | — |
| `/admin/jadwal-pelajaran/import` | `Admin/JadwalPelajaranController.php`<br>`importForm()` : 1273 | `resources/views/admin/jadwal-pelajaran/import.blade.php` | `resources/css/admin/jadwal-pelajaran/import.css`<br>`resources/js/admin/jadwal-pelajaran/import.js` |
| `/admin/jadwal-pelajaran/kelas/{kelas}` | `Admin/JadwalPelajaranController.php`<br>`show()` : 261 | `resources/views/admin/jadwal-pelajaran/show.blade.php` | `resources/css/admin/jadwal-pelajaran/show.css`<br>`resources/js/admin/jadwal-pelajaran/show.js` |
| `/admin/jadwal-pelajaran/kelas/{kelas}/export-excel` | `Admin/JadwalPelajaranController.php`<br>`exportExcelClass()` : 766 | `resources/views/admin/jadwal-pelajaran/export-excel-class.blade.php` | — |
| `/admin/jadwal-pelajaran/kelas/{kelas}/preview-print` | `Admin/JadwalPelajaranController.php`<br>`previewPrint()` : 694 | `resources/views/admin/jadwal-pelajaran/print.blade.php` | — |
| `/admin/jadwal-pelajaran/kelas/{kelas}/print` | `Admin/JadwalPelajaranController.php`<br>`exportPdf()` : 730 | `resources/views/admin/jadwal-pelajaran/print.blade.php` | — |
| `/admin/jadwal-pelajaran/{jadwalPelajaran}/edit` | `Admin/JadwalPelajaranController.php`<br>`edit()` : 300 | `resources/views/admin/jadwal-pelajaran/edit.blade.php` | `resources/css/admin/jadwal-pelajaran/form.css`<br>`resources/js/admin/jadwal-pelajaran/form.js` |
| `/admin/kelas` | `Admin/KelasController.php`<br>`index()` : 23 | `resources/views/admin/kelas/index.blade.php` | `resources/css/admin/kelas/index.css`<br>`resources/js/admin/kelas/index.js` |
| `/admin/kelas/create` | `Admin/KelasController.php`<br>`create()` : 98 | `resources/views/admin/kelas/create.blade.php` | `resources/css/admin/kelas/form.css`<br>`resources/js/admin/kelas/form.js` |
| `/admin/kelas/import` | `Admin/KelasController.php`<br>`importForm()` : 418 | `resources/views/admin/kelas/import.blade.php` | `resources/css/admin/kelas/import.css`<br>`resources/js/admin/kelas/import.js` |
| `/admin/kelas/print` | `Admin/KelasController.php`<br>`printDaftarKelas()` : 382 | `resources/views/admin/kelas/print.blade.php` | — |
| `/admin/kelas/{kelas}` | `Admin/KelasController.php`<br>`show()` : 174 | `resources/views/admin/kelas/show.blade.php` | `resources/css/admin/kelas/show.css` |
| `/admin/kelas/{kelas}/edit` | `Admin/KelasController.php`<br>`edit()` : 197 | `resources/views/admin/kelas/edit.blade.php` | `resources/css/admin/kelas/form.css`<br>`resources/js/admin/kelas/form.js` |
| `/admin/kelas/{kelas}/manage-siswa` | `Admin/KelasController.php`<br>`manageSiswa()` : 290 | `resources/views/admin/kelas/manage-siswa.blade.php` | `resources/css/admin/kelas/manage-siswa.css`<br>`resources/js/admin/kelas/manage-siswa.js` |
| `/admin/keuangan/config` | `Admin/Keuangan/InfoPembayaranController.php`<br>`index()` : 16 | `resources/views/admin/keuangan/info-pembayaran/index.blade.php` | `resources/css/admin/keuangan/info-pembayaran/index.css`<br>`resources/js/admin/keuangan/info-pembayaran/index.js` |
| `/admin/keuangan/kenaikan-kelas/validation` | `Admin/Keuangan/PromotionValidationController.php`<br>`index()` : 24 | `resources/views/admin/keuangan/promotion/validation.blade.php` | `resources/css/admin/keuangan/promotion/validation.css`<br>`resources/js/admin/keuangan/promotion/validation.js` |
| `/admin/keuangan/kenaikan-kelas/validation/history` | `Admin/Keuangan/PromotionValidationController.php`<br>`history()` : 130 | `resources/views/admin/keuangan/promotion/history.blade.php` | `resources/css/admin/keuangan/promotion/history.css` |
| `/admin/keuangan/laporan` | `Admin/Keuangan/LaporanPembayaranController.php`<br>`index()` : 13 | `resources/views/admin/keuangan/laporan/index.blade.php` | `resources/css/admin/keuangan/laporan/index.css`<br>`resources/js/admin/keuangan/laporan/index.js` |
| `/admin/keuangan/laporan/belum-lunas` | `Admin/Keuangan/LaporanPembayaranController.php`<br>`belumLunas()` : 40 | `resources/views/admin/keuangan/laporan/belum-lunas.blade.php` | — |
| `/admin/keuangan/laporan/rekap-tagihan` | `Admin/Keuangan/LaporanPembayaranController.php`<br>`rekapTagihan()` : 26 | `resources/views/admin/keuangan/laporan/rekap-tagihan.blade.php` | — |
| `/admin/keuangan/pembayaran` | `Admin/Keuangan/PembayaranController.php`<br>`index()` : 13 | `resources/views/admin/keuangan/pembayaran/index.blade.php` | `resources/css/admin/keuangan/pembayaran/index.css`<br>`resources/js/admin/keuangan/pembayaran/index.js` |
| `/admin/keuangan/pembayaran/riwayat/{siswa}` | `Admin/Keuangan/PembayaranController.php`<br>`riwayatSiswa()` : 55 | `resources/views/admin/keuangan/pembayaran/riwayat-siswa.blade.php` | `resources/css/admin/keuangan/pembayaran/riwayat-siswa.css` |
| `/admin/keuangan/pembayaran/siswa/{siswa}/create` | `Admin/Keuangan/PembayaranController.php`<br>`create()` : 41 | `resources/views/admin/keuangan/pembayaran/create.blade.php` | `resources/css/admin/keuangan/pembayaran/create.css`<br>`resources/js/admin/keuangan/pembayaran/create.js` |
| `/admin/keuangan/pembayaran/{pembayaran}` | `Admin/Keuangan/PembayaranController.php`<br>`show()` : 27 | `resources/views/admin/keuangan/pembayaran/show.blade.php` | `resources/css/admin/keuangan/pembayaran/show.css`<br>`resources/js/admin/keuangan/pembayaran/show.js` |
| `/admin/keuangan/pembayaran/{pembayaran}/cetak-kwitansi` | `Admin/Keuangan/PembayaranController.php`<br>`cetakKwitansi()` : 123 | `resources/views/admin/keuangan/pembayaran/cetak-kwitansi.blade.php` | — |
| `/admin/keuangan/tagihan` | `Admin/Keuangan/TagihanController.php`<br>`index()` : 29 | `resources/views/admin/keuangan/tagihan/index.blade.php` | `resources/css/admin/keuangan/tagihan/index.css`<br>`resources/js/admin/keuangan/tagihan/index.js` |
| `/admin/keuangan/tagihan/bulk-create` | `Admin/Keuangan/TagihanController.php`<br>`bulkCreate()` : 73 | `resources/views/admin/keuangan/tagihan/bulk-create.blade.php` | `resources/css/admin/keuangan/tagihan/bulk-create.css`<br>`resources/js/admin/keuangan/tagihan/bulk-create.js` |
| `/admin/keuangan/tagihan/cetak-laporan` | `Admin/Keuangan/TagihanController.php`<br>`cetakLaporan()` : 96 | `resources/views/admin/keuangan/tagihan/cetak-laporan.blade.php` | — |
| `/admin/keuangan/tagihan/create-custom` | `Admin/Keuangan/TagihanController.php`<br>`createCustom()` : 170 | `resources/views/admin/keuangan/tagihan/create-custom.blade.php` | `resources/css/admin/keuangan/tagihan/create-custom.css`<br>`resources/js/admin/keuangan/tagihan/create-custom.js` |
| `/admin/keuangan/tagihan/duplicate` | `Admin/Keuangan/TagihanController.php`<br>`duplicateForm()` : 198 | `resources/views/admin/keuangan/tagihan/duplicate.blade.php` | `resources/css/admin/keuangan/tagihan/duplicate.css`<br>`resources/js/admin/keuangan/tagihan/duplicate.js` |
| `/admin/keuangan/tagihan/generate-spp` | `Admin/Keuangan/TagihanController.php`<br>`generateSppForm()` : 184 | `resources/views/admin/keuangan/tagihan/generate-spp.blade.php` | `resources/css/admin/keuangan/tagihan/generate-spp.css`<br>`resources/js/admin/keuangan/tagihan/generate-spp.js` |
| `/admin/keuangan/tagihan/import` | `Admin/Keuangan/TagihanController.php`<br>`importForm()` : 108 | `resources/views/admin/keuangan/tagihan/import.blade.php` | `resources/css/admin/keuangan/tagihan/import.css`<br>`resources/js/admin/keuangan/tagihan/import.js` |
| `/admin/keuangan/tagihan/{siswa}` | `Admin/Keuangan/TagihanController.php`<br>`show()` : 45 | `resources/views/admin/keuangan/tagihan/show.blade.php` | `resources/css/admin/keuangan/tagihan/show.css` |
| `/admin/keuangan/tagihan/{siswa}/edit` | `Admin/Keuangan/TagihanController.php`<br>`edit()` : 59 | `resources/views/admin/keuangan/tagihan/edit.blade.php` | `resources/css/admin/keuangan/tagihan/edit.css`<br>`resources/js/admin/keuangan/tagihan/edit.js` |
| `/admin/keuangan/validasi-akses` | `Admin/Keuangan/ValidasiAksesController.php`<br>`index()` : 13 | `resources/views/admin/keuangan/validasi-akses/index.blade.php` | `resources/css/admin/keuangan/validasi-akses/index.css`<br>`resources/js/admin/keuangan/validasi-akses/index.js` |
| `/admin/landing-pages` | `Admin/LandingPage/LandingPageController.php`<br>`index()` : 14 | `resources/views/admin/landing-pages/index.blade.php` | `resources/css/admin/landing-pages/index.css` |
| `/admin/landing-pages/{landingPage}/edit` | `Admin/LandingPage/LandingPageController.php`<br>`edit()` : 20 | `resources/views/admin/landing-pages/edit.blade.php` | `resources/css/admin/landing-pages/edit.css`<br>`resources/js/admin/landing-pages/edit.js` |
| `/admin/lms-settings` | `Admin/LmsSettingController.php`<br>`index()` : 12 | `resources/views/admin/lms-settings/index.blade.php` | `resources/css/admin/lms-settings/index.css` |
| `/admin/manajemen-siswa` | `Admin/ManajemenSiswaController.php`<br>`index()` : 19 | `resources/views/admin/manajemen-siswa/index.blade.php` | `resources/css/admin/manajemen-siswa/index.css`<br>`resources/js/admin/manajemen-siswa/index.js` |
| `/admin/manajemen-siswa/kelas/{kelas}` | `Admin/ManajemenSiswaController.php`<br>`perKelas()` : 410 | `resources/views/admin/manajemen-siswa/per-kelas.blade.php` | `resources/css/admin/manajemen-siswa/per-kelas.css`<br>`resources/js/admin/manajemen-siswa/per-kelas.js` |
| `/admin/manajemen-siswa/print` | `Admin/ManajemenSiswaController.php`<br>`print()` : 349 | `resources/views/admin/manajemen-siswa/print.blade.php` | — |
| `/admin/manajemen-siswa/{siswa}` | `Admin/ManajemenSiswaController.php`<br>`show()` : 130 | `resources/views/admin/manajemen-siswa/show.blade.php` | `resources/css/admin/manajemen-siswa/show.css`<br>`resources/js/admin/manajemen-siswa/show.js` |
| `/admin/manajemen-siswa/{siswa}/print-kartu` | `Admin/ManajemenSiswaController.php`<br>`printKartu()` : 401 | `resources/views/admin/manajemen-siswa/print-kartu.blade.php` | — |
| `/admin/mata-pelajaran` | `Admin/MataPelajaranController.php`<br>`index()` : 18 | `resources/views/admin/mata-pelajaran/index.blade.php` | `resources/css/admin/mata-pelajaran/index.css`<br>`resources/js/admin/mata-pelajaran/index.js` |
| `/admin/mata-pelajaran/create` | `Admin/MataPelajaranController.php`<br>`create()` : 50 | `resources/views/admin/mata-pelajaran/create.blade.php` | `resources/js/admin/mata-pelajaran/form.js` |
| `/admin/mata-pelajaran/import` | `Admin/MataPelajaranController.php`<br>`importForm()` : 144 | `resources/views/admin/mata-pelajaran/import.blade.php` | `resources/css/admin/mata-pelajaran/import.css`<br>`resources/js/admin/mata-pelajaran/import.js` |
| `/admin/mata-pelajaran/print` | `Admin/MataPelajaranController.php`<br>`print()` : 242 | `resources/views/admin/mata-pelajaran/print.blade.php` | — |
| `/admin/mata-pelajaran/{mata_pelajaran}` | `Admin/MataPelajaranController.php`<br>`show()` : 79 | `resources/views/admin/mata-pelajaran/show.blade.php` | `resources/css/admin/mata-pelajaran/show.css` |
| `/admin/mata-pelajaran/{mata_pelajaran}/edit` | `Admin/MataPelajaranController.php`<br>`edit()` : 96 | `resources/views/admin/mata-pelajaran/edit.blade.php` | — |
| `/admin/pengaturan-istirahat` | `Admin/PengaturanIstirahatController.php`<br>`index()` : 11 | `resources/views/admin/pengaturan-istirahat/index.blade.php` | `resources/css/admin/pengaturan-istirahat/index.css` |
| `/admin/pengaturan-istirahat/create` | `Admin/PengaturanIstirahatController.php`<br>`create()` : 25 | `resources/views/admin/pengaturan-istirahat/create.blade.php` | — |
| `/admin/pengaturan-istirahat/{id}/edit` | `Admin/PengaturanIstirahatController.php`<br>`edit()` : 83 | `resources/views/admin/pengaturan-istirahat/edit.blade.php` | — |
| `/admin/recovery-tickets` | `Admin/AdminRecoveryTicketController.php`<br>`index()` : 20 | `resources/views/admin/recovery-tickets/index.blade.php` | `resources/css/admin/recovery-tickets/index.css`<br>`resources/js/admin/recovery-tickets/index.js` |
| `/admin/recovery-tickets/history` | `Admin/AdminRecoveryTicketController.php`<br>`history()` : 128 | `resources/views/admin/recovery-tickets/history.blade.php` | `resources/css/admin/recovery-tickets/history.css` |
| `/admin/security-setup` | `Auth/AdminSecuritySetupController.php`<br>`showSetupForm()` : 15 | `resources/views/auth/admin-security-setup.blade.php` | `resources/css/pages/login.css` |
| `/admin/tahun-ajaran` | `Admin/TahunAjaranController.php`<br>`index()` : 15 | `resources/views/admin/tahun-ajaran/index.blade.php` | `resources/css/admin/tahun-ajaran/index.css`<br>`resources/js/admin/tahun-ajaran/index.js` |
| `/admin/tahun-ajaran/create` | `Admin/TahunAjaranController.php`<br>`create()` : 32 | `resources/views/admin/tahun-ajaran/create.blade.php` | `resources/css/admin/tahun-ajaran/form.css` |
| `/admin/tahun-ajaran/{tahun_ajaran}` | `Admin/TahunAjaranController.php`<br>`show()` : 86 | `resources/views/admin/tahun-ajaran/show.blade.php` | `resources/css/admin/tahun-ajaran/show.css` |
| `/admin/tahun-ajaran/{tahun_ajaran}/edit` | `Admin/TahunAjaranController.php`<br>`edit()` : 95 | `resources/views/admin/tahun-ajaran/edit.blade.php` | `resources/css/admin/tahun-ajaran/form.css` |
| `/admin/users` | `Admin/UserController.php`<br>`index()` : 19 | `resources/views/admin/users/index.blade.php` | `resources/css/admin/users/index.css`<br>`resources/js/admin/users/index.js` |
| `/admin/users/siswa` | `Admin/UserController.php`<br>`siswa()` : 375 | `resources/views/admin/users/siswa.blade.php` | `resources/css/admin/users/siswa.css`<br>`resources/js/admin/users/list.js` |
| `/admin/users/siswa/create` | `Admin/UserController.php`<br>`createSiswa()` : 466 | `resources/views/admin/users/siswa-create.blade.php` | `resources/css/admin/users/siswa-create.css`<br>`resources/js/admin/users/siswa-create.js` |
| `/admin/users/siswa/import` | `Admin/UserController.php`<br>`importSiswaForm()` : 1104 | `resources/views/admin/users/siswa-import.blade.php` | `resources/css/admin/users/import.css`<br>`resources/js/admin/users/import.js` |
| `/admin/users/siswa/print` | `Admin/UserController.php`<br>`printSiswa()` : 417 | `resources/views/admin/users/print/siswa.blade.php` | `resources/css/admin/users/print.css`<br>`resources/js/admin/users/print.js` |
| `/admin/users/siswa/{id}` | `Admin/UserController.php`<br>`showSiswa()` : 800 | `resources/views/admin/users/siswa-show.blade.php` | `resources/css/admin/users/show.css` |
| `/admin/users/siswa/{id}/edit` | `Admin/UserController.php`<br>`editSiswa()` : 604 | `resources/views/admin/users/siswa-edit.blade.php` | `resources/css/admin/users/siswa-edit.css`<br>`resources/js/admin/users/siswa-edit.js` |
| `/admin/users/tenaga-pendidik` | `Admin/UserController.php`<br>`tenagaPendidik()` : 41 | `resources/views/admin/users/tenaga-pendidik.blade.php` | `resources/css/admin/users/tenaga-pendidik.css`<br>`resources/js/admin/users/list.js` |
| `/admin/users/tenaga-pendidik/create` | `Admin/UserController.php`<br>`createTenagaPendidik()` : 121 | `resources/views/admin/users/tenaga-pendidik-create.blade.php` | `resources/css/admin/users/tenaga-pendidik-create.css`<br>`resources/js/admin/users/tenaga-pendidik-form.js` |
| `/admin/users/tenaga-pendidik/import` | `Admin/UserController.php`<br>`importTenagaPendidikForm()` : 1143 | `resources/views/admin/users/tenaga-pendidik-import.blade.php` | `resources/css/admin/users/import.css`<br>`resources/js/admin/users/import.js` |
| `/admin/users/tenaga-pendidik/print` | `Admin/UserController.php`<br>`printTenagaPendidik()` : 82 | `resources/views/admin/users/print/tenaga-pendidik.blade.php` | `resources/css/admin/users/print.css`<br>`resources/js/admin/users/print.js` |
| `/admin/users/tenaga-pendidik/{id}` | `Admin/UserController.php`<br>`showTenagaPendidik()` : 291 | `resources/views/admin/users/tenaga-pendidik-show.blade.php` | `resources/css/admin/users/show.css` |
| `/admin/users/tenaga-pendidik/{id}/edit` | `Admin/UserController.php`<br>`editTenagaPendidik()` : 176 | `resources/views/admin/users/tenaga-pendidik-edit.blade.php` | `resources/css/admin/users/tenaga-pendidik-edit.css`<br>`resources/js/admin/users/tenaga-pendidik-form.js` |
| `/admin/users/wali-siswa` | `Admin/UserController.php`<br>`orangTua()` : 839 | `resources/views/admin/users/wali-siswa.blade.php` | `resources/css/admin/users/wali-siswa.css`<br>`resources/js/admin/users/list.js` |
| `/admin/users/wali-siswa/create` | `Admin/UserController.php`<br>`createOrangTua()` : 926 | `resources/views/admin/users/wali-siswa-create.blade.php` | `resources/css/admin/users/wali-siswa-create.css`<br>`resources/js/admin/users/wali-siswa-form.js` |
| `/admin/users/wali-siswa/import` | `Admin/UserController.php`<br>`importOrangTuaForm()` : 1180 | `resources/views/admin/users/wali-siswa-import.blade.php` | `resources/css/admin/users/import.css`<br>`resources/js/admin/users/import.js` |
| `/admin/users/wali-siswa/print` | `Admin/UserController.php`<br>`printOrangTua()` : 879 | `resources/views/admin/users/print/wali-siswa.blade.php` | `resources/css/admin/users/print.css`<br>`resources/js/admin/users/print.js` |
| `/admin/users/wali-siswa/{id}` | `Admin/UserController.php`<br>`showOrangTua()` : 1013 | `resources/views/admin/users/wali-siswa-show.blade.php` | `resources/css/admin/users/show.css` |
| `/admin/users/wali-siswa/{id}/edit` | `Admin/UserController.php`<br>`editOrangTua()` : 1022 | `resources/views/admin/users/wali-siswa-edit.blade.php` | `resources/css/admin/users/wali-siswa-edit.css`<br>`resources/js/admin/users/wali-siswa-form.js` |
| `/admin/wali-kelas` | `Admin/WaliKelasController.php`<br>`index()` : 19 | `resources/views/admin/wali-kelas/index.blade.php` | `resources/css/admin/wali-kelas/index.css`<br>`resources/js/admin/wali-kelas/index.js` |
| `/admin/wali-kelas/print` | `Admin/WaliKelasController.php`<br>`print()` : 253 | `resources/views/admin/wali-kelas/print.blade.php` | — |
| `/admin/wali-kelas/{kelas}` | `Admin/WaliKelasController.php`<br>`show()` : 226 | `resources/views/admin/wali-kelas/show.blade.php` | `resources/css/admin/wali-kelas/show.css`<br>`resources/js/admin/wali-kelas/show.js` |

### Ketua PKBM — 23 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/ketua/catatan` | `Ketua/KetuaController.php`<br>`catatanIndex()` : 562 | `resources/views/ketua/catatan/index.blade.php` | `resources/css/ketua/catatan/index.css`<br>`resources/js/ketua/catatan/index.js` |
| `/ketua/catatan/create` | `Ketua/KetuaController.php`<br>`catatanCreate()` : 568 | `resources/views/ketua/catatan/create.blade.php` | `resources/css/ketua/catatan/create.css`<br>`resources/js/ketua/catatan/create.js` |
| `/ketua/catatan/{id}` | `Ketua/KetuaController.php`<br>`catatanShow()` : 683 | `resources/views/ketua/catatan/show.blade.php` | `resources/css/ketua/catatan/show.css` |
| `/ketua/dashboard` | `DashboardController.php`<br>`ketua()` : 71 | `resources/views/dashboard/ketua.blade.php` | `resources/css/dashboard/ketua.css` |
| `/ketua/dispensasi` | `Ketua/ValidasiRaporController.php`<br>`dispensasiIndex()` : 297 | `resources/views/ketua/dispensasi/index.blade.php` | `resources/css/ketua/dispensasi/index.css`<br>`resources/js/ketua/dispensasi/index.js` |
| `/ketua/kenaikan-kelas/approval` | `Ketua/PromotionApprovalController.php`<br>`index()` : 15 | `resources/views/ketua/promotion/approval.blade.php` | `resources/css/ketua/promotion/approval.css`<br>`resources/js/ketua/promotion/approval.js` |
| `/ketua/kenaikan-kelas/approval/history` | `Ketua/PromotionApprovalController.php`<br>`history()` : 93 | `resources/views/ketua/promotion/history.blade.php` | — |
| `/ketua/laporan` | `Ketua/KetuaController.php`<br>`index()` : 301 | `resources/views/ketua/laporan/index.blade.php` | `resources/css/ketua/laporan/index.css`<br>`resources/js/ketua/laporan/index.js` |
| `/ketua/laporan/cetak-guru-pengajar` | `Ketua/KetuaController.php`<br>`guruPengajar()` : 463 | `resources/views/ketua/laporan/print-guru-pengajar.blade.php` | — |
| `/ketua/laporan/cetak-kelas` | `Ketua/KetuaController.php`<br>`kelas()` : 424 | `resources/views/ketua/laporan/print-kelas.blade.php` | — |
| `/ketua/laporan/cetak-rekap` | `Ketua/KetuaController.php`<br>`rekap()` : 517 | `resources/views/ketua/laporan/print-rekap.blade.php` | — |
| `/ketua/laporan/cetak-siswa` | `Ketua/KetuaController.php`<br>`siswa()` : 329 | `resources/views/ketua/laporan/print-siswa.blade.php` | — |
| `/ketua/laporan/cetak-tenaga-pendidik` | `Ketua/KetuaController.php`<br>`tenagaPendidik()` : 405 | `resources/views/ketua/laporan/print-guru.blade.php` | — |
| `/ketua/laporan/cetak-wali-kelas` | `Ketua/KetuaController.php`<br>`waliKelas()` : 444 | `resources/views/ketua/laporan/print-wali-kelas.blade.php` | — |
| `/ketua/laporan/rekap-akademik` | `Ketua/KetuaController.php`<br>`rekapAkademik()` : 483 | `resources/views/ketua/laporan/print-rekap-akademik.blade.php` | — |
| `/ketua/monitoring/guru-pengajar` | `Ketua/KetuaController.php`<br>`monitoringGuruPengajar()` : 152 | `resources/views/ketua/monitoring/guru-pengajar.blade.php` | `resources/css/ketua/monitoring/guru-pengajar.css`<br>`resources/js/ketua/monitoring/guru-pengajar.js` |
| `/ketua/monitoring/lms` | `Ketua/KetuaController.php`<br>`lmsIndex()` : 714 | `resources/views/monitoring-lms/index.blade.php` | `resources/css/monitoring-lms/index.css`<br>`resources/js/monitoring-lms/index.js` |
| `/ketua/monitoring/lms/kelas/{kelas}` | `Ketua/KetuaController.php`<br>`lmsKelas()` : 735 | `resources/views/monitoring-lms/kelas-detail.blade.php` | `resources/css/monitoring-lms/kelas-detail.css`<br>`resources/js/monitoring-lms/kelas-detail.js` |
| `/ketua/monitoring/pengguna` | `Ketua/KetuaController.php`<br>`monitoringPengguna()` : 34 | `resources/views/ketua/monitoring/pengguna.blade.php` | `resources/css/ketua/monitoring/pengguna.css`<br>`resources/js/ketua/monitoring/pengguna.js` |
| `/ketua/monitoring/siswa` | `Ketua/KetuaController.php`<br>`monitoringSiswa()` : 222 | `resources/views/ketua/monitoring/siswa.blade.php` | `resources/css/ketua/monitoring/siswa.css`<br>`resources/js/ketua/monitoring/siswa.js` |
| `/ketua/monitoring/wali-kelas` | `Ketua/KetuaController.php`<br>`monitoringWaliKelas()` : 83 | `resources/views/ketua/monitoring/wali-kelas.blade.php` | `resources/css/ketua/monitoring/wali-kelas.css`<br>`resources/js/ketua/monitoring/wali-kelas.js` |
| `/ketua/validasi-rapor` | `Ketua/ValidasiRaporController.php`<br>`index()` : 21 | `resources/views/ketua/validasi-rapor/index.blade.php` | `resources/css/ketua/validasi-rapor/index.css`<br>`resources/js/ketua/validasi-rapor/index.js` |
| `/ketua/validasi-rapor/{siswa}/preview` | `Ketua/ValidasiRaporController.php`<br>`previewRapor()` : 199 | `resources/views/wali-kelas/rapor/preview-pts.blade.php` | — |

### Wakil Kepala Sekolah — 54 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/waka/catatan` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`catatanIndex()` : 509 | `resources/views/waka/catatan/index.blade.php` | `resources/css/waka/catatan/index.css`<br>`resources/js/waka/catatan/index.js` |
| `/waka/catatan/create` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`catatanCreate()` : 520 | `resources/views/waka/catatan/create.blade.php` | `resources/css/waka/catatan/create.css`<br>`resources/js/waka/catatan/create.js` |
| `/waka/catatan/{id}` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`catatanShow()` : 564 | `resources/views/waka/catatan/show.blade.php` | `resources/css/waka/catatan/show.css` |
| `/waka/dashboard` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`dashboard()` : 36 | `resources/views/waka/dashboard.blade.php` | `resources/css/waka/dashboard.css` |
| `/waka/guru-pengajar` | `WakilKepalaSekolah/GuruPengajarController.php`<br>`index()` : 32 | `resources/views/waka/guru-pengajar/index.blade.php` | `resources/css/waka/guru-pengajar/index.css`<br>`resources/js/waka/guru-pengajar/index.js` |
| `/waka/guru-pengajar/kelas/{kelas}` | `WakilKepalaSekolah/GuruPengajarController.php`<br>`manageKelas()` : 153 | `resources/views/waka/guru-pengajar/manage-kelas.blade.php` | `resources/css/waka/guru-pengajar/manage-kelas.css` |
| `/waka/guru-pengajar/print` | `WakilKepalaSekolah/GuruPengajarController.php`<br>`print()` : 252 | `resources/views/waka/guru-pengajar/print.blade.php` | — |
| `/waka/guru-pengajar/{guruPengajar}` | `WakilKepalaSekolah/GuruPengajarController.php`<br>`show()` : 109 | `resources/views/waka/guru-pengajar/show.blade.php` | `resources/css/waka/guru-pengajar/show.css`<br>`resources/js/waka/guru-pengajar/show.js` |
| `/waka/jadwal-pelajaran` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`index()` : 27 | `resources/views/waka/jadwal-pelajaran/index.blade.php` | `resources/css/waka/jadwal-pelajaran/index.css`<br>`resources/js/waka/jadwal-pelajaran/index.js` |
| `/waka/jadwal-pelajaran/create` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`create()` : 150 | `resources/views/waka/jadwal-pelajaran/create.blade.php` | `resources/css/waka/jadwal-pelajaran/form.css`<br>`resources/js/waka/jadwal-pelajaran/form.js` |
| `/waka/jadwal-pelajaran/export-excel` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`exportExcel()` : 1032 | `resources/views/waka/jadwal-pelajaran/export-excel.blade.php` | — |
| `/waka/jadwal-pelajaran/export-pdf` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`exportPdfAll()` : 963 | `resources/views/waka/jadwal-pelajaran/export-pdf.blade.php` | — |
| `/waka/jadwal-pelajaran/import` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`importForm()` : 741 | `resources/views/waka/jadwal-pelajaran/import.blade.php` | `resources/css/waka/jadwal-pelajaran/import.css`<br>`resources/js/waka/jadwal-pelajaran/import.js` |
| `/waka/jadwal-pelajaran/kelas/{kelas}/export-excel` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`exportExcelClass()` : 1111 | `resources/views/waka/jadwal-pelajaran/export-excel-class.blade.php` | — |
| `/waka/jadwal-pelajaran/kelas/{kelas}/print` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`exportPdf()` : 1087 | `resources/views/waka/jadwal-pelajaran/print.blade.php` | — |
| `/waka/jadwal-pelajaran/kelas/{kelas}/show` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`show()` : 273 | `resources/views/waka/jadwal-pelajaran/show.blade.php` | `resources/css/waka/jadwal-pelajaran/show.css`<br>`resources/js/waka/jadwal-pelajaran/show.js` |
| `/waka/jadwal-pelajaran/{jadwalPelajaran}/edit` | `WakilKepalaSekolah/JadwalPelajaranController.php`<br>`edit()` : 317 | `resources/views/waka/jadwal-pelajaran/edit.blade.php` | `resources/css/waka/jadwal-pelajaran/form.css`<br>`resources/js/waka/jadwal-pelajaran/form.js` |
| `/waka/kelas` | `WakilKepalaSekolah/KelasController.php`<br>`index()` : 58 | `resources/views/waka/kelas/index.blade.php` | `resources/css/waka/kelas/index.css`<br>`resources/js/waka/kelas/index.js` |
| `/waka/kelas/create` | `WakilKepalaSekolah/KelasController.php`<br>`create()` : 128 | `resources/views/waka/kelas/create.blade.php` | `resources/css/waka/kelas/form.css`<br>`resources/js/waka/kelas/form.js` |
| `/waka/kelas/import` | `WakilKepalaSekolah/KelasController.php`<br>`import()` : 470 | `resources/views/waka/kelas/import.blade.php` | `resources/css/waka/kelas/import.css`<br>`resources/js/waka/kelas/import.js` |
| `/waka/kelas/print` | `WakilKepalaSekolah/KelasController.php`<br>`print()` : 428 | `resources/views/waka/kelas/print.blade.php` | — |
| `/waka/kelas/{kelas}` | `WakilKepalaSekolah/KelasController.php`<br>`show()` : 194 | `resources/views/waka/kelas/show.blade.php` | `resources/css/waka/kelas/show.css` |
| `/waka/kelas/{kelas}/edit` | `WakilKepalaSekolah/KelasController.php`<br>`edit()` : 217 | `resources/views/waka/kelas/edit.blade.php` | `resources/css/waka/kelas/form.css`<br>`resources/js/waka/kelas/form.js` |
| `/waka/kelas/{kelas}/manage-siswa` | `WakilKepalaSekolah/KelasController.php`<br>`manageSiswa()` : 332 | `resources/views/waka/kelas/manage-siswa.blade.php` | `resources/css/waka/kelas/manage-siswa.css`<br>`resources/js/waka/kelas/manage-siswa.js` |
| `/waka/kenaikan-kelas/kkm` | `Admin/Akademik/PromotionKKMController.php`<br>`index()` : 15 | `resources/views/admin/akademik/promotion/kkm.blade.php` | `resources/css/admin/akademik/promotion/kkm.css`<br>`resources/js/admin/akademik/promotion/kkm.js` |
| `/waka/kenaikan-kelas/report` | `Admin/Akademik/PromotionReportController.php`<br>`index()` : 14 | `resources/views/admin/akademik/promotion/rekap.blade.php` | `resources/css/admin/akademik/promotion/rekap.css`<br>`resources/js/admin/akademik/promotion/rekap.js` |
| `/waka/kenaikan-kelas/report/print` | `Admin/Akademik/PromotionReportController.php`<br>`print()` : 227 | `resources/views/admin/akademik/promotion/print.blade.php` | `resources/css/admin/akademik/promotion/print.css`<br>`resources/js/admin/akademik/promotion/print.js` |
| `/waka/kenaikan-kelas/settings` | `Admin/Akademik/PromotionSettingsController.php`<br>`index()` : 14 | `resources/views/admin/akademik/promotion/settings.blade.php` | `resources/css/admin/akademik/promotion/settings.css` |
| `/waka/manajemen-siswa` | `WakilKepalaSekolah/ManajemenSiswaController.php`<br>`index()` : 67 | `resources/views/waka/manajemen-siswa/index.blade.php` | `resources/css/waka/manajemen-siswa/index.css`<br>`resources/js/waka/manajemen-siswa/index.js` |
| `/waka/manajemen-siswa/kelas/{kelas}` | `WakilKepalaSekolah/ManajemenSiswaController.php`<br>`perKelas()` : 411 | `resources/views/waka/manajemen-siswa/per-kelas.blade.php` | `resources/css/waka/manajemen-siswa/per-kelas.css`<br>`resources/js/waka/manajemen-siswa/per-kelas.js` |
| `/waka/manajemen-siswa/print` | `WakilKepalaSekolah/ManajemenSiswaController.php`<br>`print()` : 341 | `resources/views/waka/manajemen-siswa/print.blade.php` | — |
| `/waka/manajemen-siswa/{siswa}` | `WakilKepalaSekolah/ManajemenSiswaController.php`<br>`show()` : 165 | `resources/views/waka/manajemen-siswa/show.blade.php` | `resources/css/waka/manajemen-siswa/show.css`<br>`resources/js/waka/manajemen-siswa/show.js` |
| `/waka/manajemen-siswa/{siswa}/print-kartu` | `WakilKepalaSekolah/ManajemenSiswaController.php`<br>`printKartu()` : 403 | `resources/views/waka/manajemen-siswa/print-kartu.blade.php` | — |
| `/waka/mata-pelajaran` | `WakilKepalaSekolah/MataPelajaranController.php`<br>`index()` : 14 | `resources/views/waka/mata-pelajaran/index.blade.php` | `resources/css/waka/mata-pelajaran/index.css`<br>`resources/js/waka/mata-pelajaran/index.js` |
| `/waka/mata-pelajaran/create` | `WakilKepalaSekolah/MataPelajaranController.php`<br>`create()` : 45 | `resources/views/waka/mata-pelajaran/create.blade.php` | `resources/js/waka/mata-pelajaran/form.js` |
| `/waka/mata-pelajaran/import` | `WakilKepalaSekolah/MataPelajaranController.php`<br>`import()` : 138 | `resources/views/waka/mata-pelajaran/import.blade.php` | `resources/css/waka/mata-pelajaran/import.css`<br>`resources/js/waka/mata-pelajaran/import.js` |
| `/waka/mata-pelajaran/print` | `WakilKepalaSekolah/MataPelajaranController.php`<br>`print()` : 114 | `resources/views/waka/mata-pelajaran/print.blade.php` | — |
| `/waka/mata-pelajaran/{mata_pelajaran}` | `WakilKepalaSekolah/MataPelajaranController.php`<br>`show()` : 67 | `resources/views/waka/mata-pelajaran/show.blade.php` | `resources/css/waka/mata-pelajaran/show.css` |
| `/waka/mata-pelajaran/{mata_pelajaran}/edit` | `WakilKepalaSekolah/MataPelajaranController.php`<br>`edit()` : 81 | `resources/views/waka/mata-pelajaran/edit.blade.php` | — |
| `/waka/monitoring/guru-pengajar` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`monitoringGuruPengajar()` : 318 | `resources/views/waka/monitoring/guru-pengajar.blade.php` | `resources/css/waka/monitoring/guru-pengajar.css`<br>`resources/js/waka/monitoring/guru-pengajar.js` |
| `/waka/monitoring/lms` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`lmsIndex()` : 613 | `resources/views/monitoring-lms/index.blade.php` | `resources/css/monitoring-lms/index.css`<br>`resources/js/monitoring-lms/index.js` |
| `/waka/monitoring/lms/kelas/{kelas}` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`lmsKelas()` : 634 | `resources/views/monitoring-lms/kelas-detail.blade.php` | `resources/css/monitoring-lms/kelas-detail.css`<br>`resources/js/monitoring-lms/kelas-detail.js` |
| `/waka/monitoring/siswa` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`monitoringSiswa()` : 436 | `resources/views/waka/monitoring/siswa.blade.php` | `resources/css/waka/monitoring/siswa.css`<br>`resources/js/waka/monitoring/siswa.js` |
| `/waka/monitoring/wali-kelas` | `WakilKepalaSekolah/WakilKepalaSekolahController.php`<br>`monitoringWaliKelas()` : 380 | `resources/views/waka/monitoring/wali-kelas.blade.php` | `resources/css/waka/monitoring/wali-kelas.css`<br>`resources/js/waka/monitoring/wali-kelas.js` |
| `/waka/pengaturan-istirahat` | `WakilKepalaSekolah/PengaturanIstirahatController.php`<br>`index()` : 11 | `resources/views/waka/pengaturan-istirahat/index.blade.php` | `resources/css/waka/pengaturan-istirahat/index.css` |
| `/waka/pengaturan-istirahat/create` | `WakilKepalaSekolah/PengaturanIstirahatController.php`<br>`create()` : 25 | `resources/views/waka/pengaturan-istirahat/create.blade.php` | — |
| `/waka/pengaturan-istirahat/{id}/edit` | `WakilKepalaSekolah/PengaturanIstirahatController.php`<br>`edit()` : 83 | `resources/views/waka/pengaturan-istirahat/edit.blade.php` | — |
| `/waka/tahun-ajaran` | `WakilKepalaSekolah/TahunAjaranController.php`<br>`index()` : 12 | `resources/views/waka/tahun-ajaran/index.blade.php` | `resources/css/waka/tahun-ajaran/index.css`<br>`resources/js/waka/tahun-ajaran/index.js` |
| `/waka/tahun-ajaran/create` | `WakilKepalaSekolah/TahunAjaranController.php`<br>`create()` : 47 | `resources/views/waka/tahun-ajaran/create.blade.php` | `resources/css/waka/tahun-ajaran/form.css` |
| `/waka/tahun-ajaran/{tahunAjaran}` | `WakilKepalaSekolah/TahunAjaranController.php`<br>`show()` : 95 | `resources/views/waka/tahun-ajaran/show.blade.php` | `resources/css/waka/tahun-ajaran/show.css` |
| `/waka/tahun-ajaran/{tahunAjaran}/edit` | `WakilKepalaSekolah/TahunAjaranController.php`<br>`edit()` : 101 | `resources/views/waka/tahun-ajaran/edit.blade.php` | `resources/css/waka/tahun-ajaran/form.css` |
| `/waka/wali-kelas` | `WakilKepalaSekolah/WaliKelasController.php`<br>`index()` : 77 | `resources/views/waka/wali-kelas/index.blade.php` | `resources/css/waka/wali-kelas/index.css`<br>`resources/js/waka/wali-kelas/index.js` |
| `/waka/wali-kelas/print` | `WakilKepalaSekolah/WaliKelasController.php`<br>`print()` : 233 | `resources/views/waka/wali-kelas/print.blade.php` | — |
| `/waka/wali-kelas/{kelas}` | `WakilKepalaSekolah/WaliKelasController.php`<br>`show()` : 212 | `resources/views/waka/wali-kelas/show.blade.php` | `resources/css/waka/wali-kelas/show.css`<br>`resources/js/waka/wali-kelas/show.js` |

### Sekretaris — 14 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/sekretaris/berita` | `Sekretaris/SekretarisController.php`<br>`beritaIndex()` : 854 | `resources/views/sekretaris/berita/index.blade.php` | `resources/css/sekretaris/berita/index.css`<br>`resources/js/sekretaris/berita/index.js` |
| `/sekretaris/berita/create` | `Sekretaris/SekretarisController.php`<br>`beritaCreate()` : 880 | `resources/views/sekretaris/berita/form.blade.php` | `resources/css/sekretaris/berita/form.css`<br>`resources/js/sekretaris/berita/form.js` |
| `/sekretaris/berita/{id}/edit` | `Sekretaris/SekretarisController.php`<br>`beritaEdit()` : 945 | `resources/views/sekretaris/berita/form.blade.php` | `resources/css/sekretaris/berita/form.css`<br>`resources/js/sekretaris/berita/form.js` |
| `/sekretaris/dashboard` | `Sekretaris/SekretarisController.php`<br>`dashboard()` : 22 | `resources/views/dashboard/sekretaris.blade.php` | `resources/css/dashboard/sekretaris.css`<br>`resources/js/dashboard/sekretaris.js` |
| `/sekretaris/flyer` | `Sekretaris/SekretarisController.php`<br>`flyerIndex()` : 758 | `resources/views/sekretaris/flyer/index.blade.php` | `resources/css/sekretaris/flyer/index.css`<br>`resources/js/sekretaris/flyer/index.js` |
| `/sekretaris/flyer/create` | `Sekretaris/SekretarisController.php`<br>`flyerCreate()` : 767 | `resources/views/sekretaris/flyer/form.blade.php` | `resources/css/sekretaris/flyer/form.css`<br>`resources/js/sekretaris/flyer/form.js` |
| `/sekretaris/flyer/{id}/edit` | `Sekretaris/SekretarisController.php`<br>`flyerEdit()` : 799 | `resources/views/sekretaris/flyer/form.blade.php` | `resources/css/sekretaris/flyer/form.css`<br>`resources/js/sekretaris/flyer/form.js` |
| `/sekretaris/kalender` | `Sekretaris/SekretarisController.php`<br>`kalenderIndex()` : 83 | `resources/views/sekretaris/kalender/index.blade.php` | `resources/css/sekretaris/kalender/index.css`<br>`resources/js/sekretaris/kalender/index.js` |
| `/sekretaris/kalender/create` | `Sekretaris/SekretarisController.php`<br>`kalenderCreate()` : 301 | `resources/views/sekretaris/kalender/form.blade.php` | `resources/css/sekretaris/kalender/form.css`<br>`resources/js/sekretaris/kalender/form.js` |
| `/sekretaris/kalender/{id}` | `Sekretaris/SekretarisController.php`<br>`kalenderShow()` : 341 | `resources/views/sekretaris/kalender/show.blade.php` | `resources/css/sekretaris/kalender/show.css`<br>`resources/js/sekretaris/kalender/show.js` |
| `/sekretaris/kalender/{id}/edit` | `Sekretaris/SekretarisController.php`<br>`kalenderEdit()` : 347 | `resources/views/sekretaris/kalender/form.blade.php` | `resources/css/sekretaris/kalender/form.css`<br>`resources/js/sekretaris/kalender/form.js` |
| `/sekretaris/pengumuman` | `Sekretaris/SekretarisController.php`<br>`pengumumanIndex()` : 655 | `resources/views/sekretaris/pengumuman/index.blade.php` | `resources/css/sekretaris/pengumuman/index.css`<br>`resources/js/sekretaris/pengumuman/index.js` |
| `/sekretaris/pengumuman/create` | `Sekretaris/SekretarisController.php`<br>`pengumumanCreate()` : 664 | `resources/views/sekretaris/pengumuman/form.blade.php` | `resources/css/sekretaris/pengumuman/form.css` |
| `/sekretaris/pengumuman/{id}/edit` | `Sekretaris/SekretarisController.php`<br>`pengumumanEdit()` : 700 | `resources/views/sekretaris/pengumuman/form.blade.php` | `resources/css/sekretaris/pengumuman/form.css` |

### Bendahara — 25 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/bendahara/config` | `Bendahara/InfoPembayaranController.php`<br>`index()` : 16 | `resources/views/bendahara/info-pembayaran/index.blade.php` | `resources/css/bendahara/info-pembayaran/index.css`<br>`resources/js/bendahara/info-pembayaran/index.js` |
| `/bendahara/dashboard` | `Bendahara/BendaharaController.php`<br>`dashboard()` : 20 | `resources/views/dashboard/bendahara.blade.php` | `resources/css/dashboard/bendahara.css`<br>`resources/js/dashboard/bendahara.js` |
| `/bendahara/kenaikan-kelas/validation` | `Bendahara/PromotionValidationController.php`<br>`index()` : 24 | `resources/views/bendahara/promotion/validation.blade.php` | `resources/css/bendahara/promotion/validation.css`<br>`resources/js/bendahara/promotion/validation.js` |
| `/bendahara/kenaikan-kelas/validation/history` | `Bendahara/PromotionValidationController.php`<br>`history()` : 142 | `resources/views/bendahara/promotion/history.blade.php` | `resources/css/bendahara/promotion/history.css` |
| `/bendahara/laporan` | `Bendahara/LaporanPembayaranController.php`<br>`index()` : 34 | `resources/views/bendahara/laporan/index.blade.php` | `resources/css/bendahara/laporan/index.css`<br>`resources/js/bendahara/laporan/index.js` |
| `/bendahara/laporan/belum-lunas` | `Bendahara/LaporanPembayaranController.php`<br>`belumLunas()` : 350 | `resources/views/bendahara/laporan/belum-lunas.blade.php` | — |
| `/bendahara/laporan/cetak` | `Bendahara/LaporanPembayaranController.php`<br>`cetak()` : 183 | `resources/views/bendahara/laporan/cetak.blade.php` | — |
| `/bendahara/laporan/cetak-belum-lunas` | `Bendahara/LaporanPembayaranController.php`<br>`cetakBelumLunas()` : 403 | `resources/views/bendahara/laporan/cetak-belum-lunas.blade.php` | — |
| `/bendahara/laporan/cetak-rekap-tagihan` | `Bendahara/LaporanPembayaranController.php`<br>`cetakRekapTagihan()` : 286 | `resources/views/bendahara/laporan/cetak-rekap-tagihan.blade.php` | — |
| `/bendahara/laporan/rekap-tagihan` | `Bendahara/LaporanPembayaranController.php`<br>`rekapTagihan()` : 220 | `resources/views/bendahara/laporan/rekap-tagihan.blade.php` | — |
| `/bendahara/pembayaran` | `Bendahara/PembayaranController.php`<br>`index()` : 22 | `resources/views/bendahara/pembayaran/index.blade.php` | `resources/css/bendahara/pembayaran/index.css`<br>`resources/js/bendahara/pembayaran/index.js` |
| `/bendahara/pembayaran/riwayat/{siswa}` | `Bendahara/PembayaranController.php`<br>`riwayatSiswa()` : 90 | `resources/views/bendahara/pembayaran/riwayat-siswa.blade.php` | `resources/css/bendahara/pembayaran/riwayat-siswa.css` |
| `/bendahara/pembayaran/siswa/{siswa}/create` | `Bendahara/PembayaranController.php`<br>`create()` : 299 | `resources/views/bendahara/pembayaran/create.blade.php` | `resources/css/bendahara/pembayaran/create.css`<br>`resources/js/bendahara/pembayaran/create.js` |
| `/bendahara/pembayaran/{pembayaran}` | `Bendahara/PembayaranController.php`<br>`show()` : 151 | `resources/views/bendahara/pembayaran/show.blade.php` | `resources/css/bendahara/pembayaran/show.css`<br>`resources/js/bendahara/pembayaran/show.js` |
| `/bendahara/pembayaran/{pembayaran}/cetak-kwitansi` | `Bendahara/PembayaranController.php`<br>`cetakKwitansi()` : 576 | `resources/views/bendahara/pembayaran/cetak-kwitansi.blade.php` | — |
| `/bendahara/tagihan` | `Bendahara/TagihanController.php`<br>`index()` : 43 | `resources/views/bendahara/tagihan/index.blade.php` | `resources/css/bendahara/tagihan/index.css`<br>`resources/js/bendahara/tagihan/index.js` |
| `/bendahara/tagihan/bulk-create` | `Bendahara/TagihanController.php`<br>`bulkCreate()` : 528 | `resources/views/bendahara/tagihan/bulk-create.blade.php` | `resources/css/bendahara/tagihan/bulk-create.css`<br>`resources/js/bendahara/tagihan/bulk-create.js` |
| `/bendahara/tagihan/cetak-laporan` | `Bendahara/TagihanController.php`<br>`cetakLaporan()` : 466 | `resources/views/bendahara/tagihan/cetak-laporan.blade.php` | — |
| `/bendahara/tagihan/create-custom` | `Bendahara/TagihanController.php`<br>`createCustom()` : 680 | `resources/views/bendahara/tagihan/create-custom.blade.php` | `resources/css/bendahara/tagihan/create-custom.css`<br>`resources/js/bendahara/tagihan/create-custom.js` |
| `/bendahara/tagihan/duplicate` | `Bendahara/TagihanController.php`<br>`duplicateForm()` : 1040 | `resources/views/bendahara/tagihan/duplicate.blade.php` | `resources/css/bendahara/tagihan/duplicate.css`<br>`resources/js/bendahara/tagihan/duplicate.js` |
| `/bendahara/tagihan/generate-spp` | `Bendahara/TagihanController.php`<br>`generateSppForm()` : 844 | `resources/views/bendahara/tagihan/generate-spp.blade.php` | `resources/css/bendahara/tagihan/generate-spp.css`<br>`resources/js/bendahara/tagihan/generate-spp.js` |
| `/bendahara/tagihan/{siswa}` | `Bendahara/TagihanController.php`<br>`show()` : 234 | `resources/views/bendahara/tagihan/show.blade.php` | `resources/css/bendahara/tagihan/show.css` |
| `/bendahara/tagihan/{siswa}/cetak` | `Bendahara/TagihanController.php`<br>`cetak()` : 436 | `resources/views/bendahara/tagihan/cetak.blade.php` | — |
| `/bendahara/tagihan/{siswa}/edit` | `Bendahara/TagihanController.php`<br>`edit()` : 297 | `resources/views/bendahara/tagihan/edit.blade.php` | `resources/css/bendahara/tagihan/edit.css`<br>`resources/js/bendahara/tagihan/edit.js` |
| `/bendahara/validasi-akses` | `Bendahara/ValidasiAksesController.php`<br>`index()` : 22 | `resources/views/bendahara/validasi-akses/index.blade.php` | `resources/css/bendahara/validasi-akses/index.css`<br>`resources/js/bendahara/validasi-akses/index.js` |

### Wali Kelas — 29 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/wali/arsip` | `WaliKelas/WaliKelasArsipController.php`<br>`index()` : 37 | `resources/views/wali-kelas/arsip/index.blade.php` | `resources/css/wali-kelas/arsip/index.css`<br>`resources/js/wali-kelas/arsip/index.js` |
| `/wali/arsip/{kelas}` | `WaliKelas/WaliKelasArsipController.php`<br>`show()` : 83 | `resources/views/wali-kelas/arsip/show.blade.php` | `resources/css/wali-kelas/arsip/show.css`<br>`resources/js/wali-kelas/arsip/show.js` |
| `/wali/arsip/{kelas}/nilai` | `WaliKelas/WaliKelasArsipController.php`<br>`nilai()` : 179 | `resources/views/wali-kelas/arsip/show.blade.php` | `resources/css/wali-kelas/arsip/show.css`<br>`resources/js/wali-kelas/arsip/show.js` |
| `/wali/arsip/{kelas}/presensi` | `WaliKelas/WaliKelasArsipController.php`<br>`presensi()` : 131 | `resources/views/wali-kelas/arsip/show.blade.php` | `resources/css/wali-kelas/arsip/show.css`<br>`resources/js/wali-kelas/arsip/show.js` |
| `/wali/arsip/{kelas}/rapor` | `WaliKelas/WaliKelasArsipController.php`<br>`rapor()` : 108 | `resources/views/wali-kelas/arsip/show.blade.php` | `resources/css/wali-kelas/arsip/show.css`<br>`resources/js/wali-kelas/arsip/show.js` |
| `/wali/dashboard` | `WaliKelas/WaliKelasController.php`<br>`dashboard()` : 25 | `resources/views/wali-kelas/dashboard.blade.php` | `resources/css/wali-kelas/dashboard.css`<br>`resources/js/wali-kelas/dashboard.js` |
| `/wali/jadwal` | `WaliKelas/JadwalPelajaranController.php`<br>`index()` : 22 | `resources/views/wali-kelas/jadwal/index.blade.php` | `resources/css/wali-kelas/jadwal/index.css`<br>`resources/js/wali-kelas/jadwal/index.js` |
| `/wali/jadwal-pelajaran` | `WaliKelas/JadwalPelajaranController.php`<br>`index()` : 22 | `resources/views/wali-kelas/jadwal/index.blade.php` | `resources/css/wali-kelas/jadwal/index.css`<br>`resources/js/wali-kelas/jadwal/index.js` |
| `/wali/jadwal/print` | `WaliKelas/JadwalPelajaranController.php`<br>`print()` : 85 | `resources/views/wali-kelas/jadwal/print.blade.php` | — |
| `/wali/kenaikan-kelas/prediction` | `WaliKelas/PromotionController.php`<br>`index()` : 28 | `resources/views/wali-kelas/promotion/index.blade.php` | `resources/css/wali-kelas/promotion/index.css`<br>`resources/js/wali-kelas/promotion/index.js` |
| `/wali/nilai` | `WaliKelas/NilaiController.php`<br>`index()` : 27 | `resources/views/wali-kelas/nilai/index.blade.php` | `resources/css/wali-kelas/nilai/index.css`<br>`resources/js/wali-kelas/nilai/index.js` |
| `/wali/nilai/print` | `WaliKelas/NilaiController.php`<br>`print()` : 258 | `resources/views/wali-kelas/nilai/print-detail.blade.php` | — |
| `/wali/nilai/{siswa}` | `WaliKelas/NilaiController.php`<br>`show()` : 174 | `resources/views/wali-kelas/nilai/show.blade.php` | `resources/css/wali-kelas/nilai/show.css`<br>`resources/js/wali-kelas/nilai/show.js` |
| `/wali/nilai/{siswa}/edit` | `WaliKelas/NilaiController.php`<br>`edit()` : 449 | `resources/views/wali-kelas/nilai/edit.blade.php` | `resources/css/wali-kelas/nilai/edit.css`<br>`resources/js/wali-kelas/nilai/edit.js` |
| `/wali/nilai/{siswa}/print` | `WaliKelas/NilaiController.php`<br>`printSiswa()` : 383 | `resources/views/wali-kelas/nilai/print.blade.php` | — |
| `/wali/pilih-kelas` | `WaliKelas/PilihKelasController.php`<br>`index()` : 19 | `resources/views/wali-kelas/pilih-kelas/index.blade.php` | `resources/css/wali-kelas/pilih-kelas/index.css`<br>`resources/js/wali-kelas/pilih-kelas/index.js` |
| `/wali/presensi` | `WaliKelas/PresensiController.php`<br>`index()` : 82 | `resources/views/wali-kelas/presensi/index.blade.php` | `resources/css/wali-kelas/presensi/index.css`<br>`resources/js/wali-kelas/presensi/index.js` |
| `/wali/presensi/print-rekap` | `WaliKelas/PresensiController.php`<br>`printRekap()` : 503 | `resources/views/wali-kelas/presensi/print-rekap.blade.php` | — |
| `/wali/presensi/rekap-harian` | `WaliKelas/PresensiController.php`<br>`rekapHarian()` : 576 | `resources/views/wali-kelas/presensi/rekap-harian.blade.php` | `resources/css/wali-kelas/presensi/rekap-harian.css`<br>`resources/js/wali-kelas/presensi/rekap-harian.js` |
| `/wali/presensi/riwayat` | `WaliKelas/PresensiController.php`<br>`riwayat()` : 404 | `resources/views/wali-kelas/presensi/riwayat.blade.php` | `resources/css/wali-kelas/presensi/riwayat.css`<br>`resources/js/wali-kelas/presensi/riwayat.js` |
| `/wali/presensi/show-harian` | `WaliKelas/PresensiController.php`<br>`showHarian()` : 630 | `resources/views/wali-kelas/presensi/show-harian.blade.php` | `resources/css/wali-kelas/presensi/show-harian.css`<br>`resources/js/wali-kelas/presensi/show-harian.js` |
| `/wali/presensi/validasi-izin` | `WaliKelas/PresensiController.php`<br>`validasiIzin()` : 243 | `resources/views/wali-kelas/presensi/validasi-izin.blade.php` | `resources/css/wali-kelas/presensi/validasi-izin.css`<br>`resources/js/wali-kelas/presensi/validasi-izin.js` |
| `/wali/rapor` | `WaliKelas/RaporController.php`<br>`index()` : 35 | `resources/views/wali-kelas/rapor/index.blade.php` | `resources/css/wali-kelas/rapor/index.css`<br>`resources/js/wali-kelas/rapor/index.js` |
| `/wali/rapor-pending` | `WaliKelas/WaliKelasController.php`<br>`raporPending()` : 133 | `resources/views/wali-kelas/rapor-pending/index.blade.php` | `resources/css/wali-kelas/rapor-pending/index.css`<br>`resources/js/wali-kelas/rapor-pending/index.js` |
| `/wali/rapor/request-download` | `WaliKelas/RaporController.php`<br>`requestDownloadIndex()` : 1223 | `resources/views/wali-kelas/rapor/request-download.blade.php` | `resources/css/wali-kelas/rapor/request-download.css`<br>`resources/js/wali-kelas/rapor/request-download.js` |
| `/wali/rapor/{rapor}/edit` | `WaliKelas/RaporController.php`<br>`edit()` : 293 | `resources/views/wali-kelas/rapor/edit.blade.php` | `resources/css/wali-kelas/rapor/edit.css`<br>`resources/js/wali-kelas/rapor/edit.js` |
| `/wali/rapor/{rapor}/preview` | `WaliKelas/RaporController.php`<br>`preview()` : 756 | `resources/views/wali-kelas/rapor/preview-pts.blade.php` | — |
| `/wali/template-capaian` | `WaliKelas/TemplateCapaianController.php`<br>`index()` : 20 | `resources/views/wali-kelas/template-capaian/index.blade.php` | `resources/js/wali-kelas/template-capaian/index.js` |
| `/wali/validasi-akses` | `WaliKelas/ValidasiAksesController.php`<br>`index()` : 21 | `resources/views/wali-kelas/validasi-akses/index.blade.php` | `resources/css/wali-kelas/validasi-akses/index.css`<br>`resources/js/wali-kelas/validasi-akses/index.js` |

### Guru Pengajar — 43 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/guru/dashboard` | `DashboardController.php`<br>`guru()` : 133 | `resources/views/dashboard/guru.blade.php` | `resources/css/dashboard/guru.css` |
| `/guru/jadwal` | `Guru/GuruJadwalController.php`<br>`index()` : 15 | `resources/views/guru/jadwal/index.blade.php` | `resources/css/guru/jadwal/index.css` |
| `/guru/kelas` | `Guru/GuruKelasController.php`<br>`index()` : 17 | `resources/views/guru/kelas/index.blade.php` | `resources/css/guru/kelas/index.css` |
| `/guru/kelas/{kelas}/mapel` | `Guru/GuruKelasController.php`<br>`showMapel()` : 50 | `resources/views/guru/kelas/mapel.blade.php` | `resources/css/guru/kelas/mapel.css` |
| `/guru/lms/arsip` | `Guru/GuruLmsArsipController.php`<br>`index()` : 33 | `resources/views/guru/lms/arsip/index.blade.php` | `resources/css/guru/lms/arsip/index.css`<br>`resources/js/guru/lms/arsip/index.js` |
| `/guru/lms/arsip/salin/{type}/{id}` | `Guru/GuruLmsArsipController.php`<br>`formSalin()` : 90 | `resources/views/guru/lms/arsip/form-salin.blade.php` | `resources/css/guru/lms/arsip/form-salin.css`<br>`resources/js/guru/lms/arsip/form-salin.js` |
| `/guru/lms/catatan-monitoring` | `Guru/GuruCatatanMonitoringController.php`<br>`index()` : 12 | `resources/views/guru/lms/catatan-monitoring/index.blade.php` | `resources/css/guru/lms/catatan-monitoring/index.css` |
| `/guru/lms/catatan-monitoring/{catatan}` | `Guru/GuruCatatanMonitoringController.php`<br>`show()` : 27 | `resources/views/guru/lms/catatan-monitoring/show.blade.php` | `resources/css/guru/lms/catatan-monitoring/show.css` |
| `/guru/lms/{kelas}/{mapel}/dashboard` | `Guru/GuruLmsController.php`<br>`dashboard()` : 23 | `resources/views/guru/lms/dashboard.blade.php` | `resources/css/guru/lms/dashboard.css` |
| `/guru/lms/{kelas}/{mapel}/forum` | `Guru/GuruForumController.php`<br>`index()` : 31 | `resources/views/guru/lms/forum/index.blade.php` | `resources/css/guru/lms/forum/index.css`<br>`resources/js/guru/lms/forum/index.js` |
| `/guru/lms/{kelas}/{mapel}/forum/create` | `Guru/GuruForumController.php`<br>`create()` : 57 | `resources/views/guru/lms/forum/create.blade.php` | — |
| `/guru/lms/{kelas}/{mapel}/forum/{forum}` | `Guru/GuruForumController.php`<br>`show()` : 140 | `resources/views/guru/lms/forum/show.blade.php` | `resources/css/guru/lms/forum/show.css`<br>`resources/js/guru/lms/forum/show.js` |
| `/guru/lms/{kelas}/{mapel}/latihan` | `Guru/GuruUjianController.php`<br>`index()` : 25 | `resources/views/guru/lms/ujian/index.blade.php` | `resources/css/guru/lms/ujian/index.css`<br>`resources/js/guru/lms/ujian/index.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/create` | `Guru/GuruUjianController.php`<br>`create()` : 68 | `resources/views/guru/lms/ujian/create.blade.php` | `resources/css/guru/lms/ujian/create.css`<br>`resources/js/guru/lms/ujian/create.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/edit` | `Guru/GuruUjianController.php`<br>`edit()` : 176 | `resources/views/guru/lms/ujian/edit.blade.php` | `resources/css/guru/lms/ujian/edit.css`<br>`resources/js/guru/lms/ujian/edit.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/hasil` | `Guru/GuruUjianController.php`<br>`hasil()` : 376 | `resources/views/guru/lms/ujian/hasil.blade.php` | `resources/css/guru/lms/ujian/hasil.css` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/koreksi/{ujianSiswa}` | `Guru/GuruUjianController.php`<br>`koreksiShow()` : 1050 | `resources/views/guru/lms/ujian/koreksi.blade.php` | `resources/css/guru/lms/ujian/koreksi.css`<br>`resources/js/guru/lms/ujian/koreksi.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/manage-soal` | `Guru/GuruUjianController.php`<br>`manageSoal()` : 741 | `resources/views/guru/lms/ujian/manage_soal.blade.php` | `resources/css/guru/lms/ujian/manage-soal.css`<br>`resources/css/components/ai-sidebar.css`<br>`resources/js/guru/lms/ujian/manage-soal.js`<br>`resources/js/components/ai-sidebar.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/soal` | `Guru/GuruUjianController.php`<br>`soal()` : 533 | `resources/views/guru/lms/ujian/soal.blade.php` | `resources/css/guru/lms/ujian/soal.css`<br>`resources/js/guru/lms/ujian/soal.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/soal/create` | `Guru/GuruUjianController.php`<br>`createSoal()` : 558 | `resources/views/guru/lms/ujian/soal-form.blade.php` | `resources/css/guru/lms/ujian/soal-form.css`<br>`resources/js/guru/lms/ujian/soal-form.js` |
| `/guru/lms/{kelas}/{mapel}/latihan/{ujian}/soal/{soal}/edit` | `Guru/GuruUjianController.php`<br>`editSoal()` : 641 | `resources/views/guru/lms/ujian/soal-form.blade.php` | `resources/css/guru/lms/ujian/soal-form.css`<br>`resources/js/guru/lms/ujian/soal-form.js` |
| `/guru/lms/{kelas}/{mapel}/materi` | `Guru/GuruMateriController.php`<br>`index()` : 22 | `resources/views/guru/lms/materi/index.blade.php` | `resources/css/guru/lms/materi/index.css`<br>`resources/js/guru/lms/materi/index.js` |
| `/guru/lms/{kelas}/{mapel}/materi/create` | `Guru/GuruMateriController.php`<br>`create()` : 57 | `resources/views/guru/lms/materi/create.blade.php` | `resources/js/guru/lms/materi/create.js` |
| `/guru/lms/{kelas}/{mapel}/materi/{materi}/edit` | `Guru/GuruMateriController.php`<br>`edit()` : 169 | `resources/views/guru/lms/materi/edit.blade.php` | `resources/js/guru/lms/materi/edit.js` |
| `/guru/lms/{kelas}/{mapel}/meeting` | `Guru/GuruLmsMeetingController.php`<br>`index()` : 17 | `resources/views/guru/lms/meeting/index.blade.php` | `resources/css/guru/lms/meeting/index.css`<br>`resources/js/guru/lms/meeting/index.js` |
| `/guru/lms/{kelas}/{mapel}/meeting/create` | `Guru/GuruLmsMeetingController.php`<br>`create()` : 40 | `resources/views/guru/lms/meeting/create.blade.php` | — |
| `/guru/lms/{kelas}/{mapel}/meeting/{meeting}/edit` | `Guru/GuruLmsMeetingController.php`<br>`edit()` : 114 | `resources/views/guru/lms/meeting/edit.blade.php` | — |
| `/guru/lms/{kelas}/{mapel}/nilai` | `Guru/GuruNilaiController.php`<br>`index()` : 28 | `resources/views/guru/lms/nilai/index.blade.php` | `resources/css/guru/lms/nilai/index.css`<br>`resources/js/guru/lms/nilai/index.js` |
| `/guru/lms/{kelas}/{mapel}/tugas` | `Guru/GuruTugasController.php`<br>`index()` : 24 | `resources/views/guru/lms/tugas/index.blade.php` | `resources/css/guru/lms/tugas/index.css`<br>`resources/js/guru/lms/tugas/index.js` |
| `/guru/lms/{kelas}/{mapel}/tugas/create` | `Guru/GuruTugasController.php`<br>`create()` : 58 | `resources/views/guru/lms/tugas/create.blade.php` | `resources/css/guru/lms/tugas/create.css`<br>`resources/js/guru/lms/tugas/create.js` |
| `/guru/lms/{kelas}/{mapel}/tugas/{tugas}/edit` | `Guru/GuruTugasController.php`<br>`edit()` : 154 | `resources/views/guru/lms/tugas/edit.blade.php` | `resources/css/guru/lms/tugas/edit.css`<br>`resources/js/guru/lms/tugas/edit.js` |
| `/guru/lms/{kelas}/{mapel}/tugas/{tugas}/koreksi` | `Guru/GuruKoreksiController.php`<br>`index()` : 25 | `resources/views/guru/lms/tugas/koreksi.blade.php` | — |
| `/guru/lms/{kelas}/{mapel}/tugas/{tugas}/koreksi/{tugasSiswa}` | `Guru/GuruKoreksiController.php`<br>`show()` : 68 | `resources/views/guru/lms/tugas/koreksi-show.blade.php` | `resources/css/guru/lms/tugas/koreksi-show.css`<br>`resources/js/guru/lms/tugas/koreksi-show.js` |
| `/guru/lms/{kelas}/{mapel}/ujian` | `Guru/GuruUjianController.php`<br>`index()` : 25 | `resources/views/guru/lms/ujian/index.blade.php` | `resources/css/guru/lms/ujian/index.css`<br>`resources/js/guru/lms/ujian/index.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/create` | `Guru/GuruUjianController.php`<br>`create()` : 68 | `resources/views/guru/lms/ujian/create.blade.php` | `resources/css/guru/lms/ujian/create.css`<br>`resources/js/guru/lms/ujian/create.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/edit` | `Guru/GuruUjianController.php`<br>`edit()` : 176 | `resources/views/guru/lms/ujian/edit.blade.php` | `resources/css/guru/lms/ujian/edit.css`<br>`resources/js/guru/lms/ujian/edit.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/hasil` | `Guru/GuruUjianController.php`<br>`hasil()` : 376 | `resources/views/guru/lms/ujian/hasil.blade.php` | `resources/css/guru/lms/ujian/hasil.css` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/koreksi/{ujianSiswa}` | `Guru/GuruUjianController.php`<br>`koreksiShow()` : 1050 | `resources/views/guru/lms/ujian/koreksi.blade.php` | `resources/css/guru/lms/ujian/koreksi.css`<br>`resources/js/guru/lms/ujian/koreksi.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/manage-soal` | `Guru/GuruUjianController.php`<br>`manageSoal()` : 741 | `resources/views/guru/lms/ujian/manage_soal.blade.php` | `resources/css/guru/lms/ujian/manage-soal.css`<br>`resources/css/components/ai-sidebar.css`<br>`resources/js/guru/lms/ujian/manage-soal.js`<br>`resources/js/components/ai-sidebar.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/pengawasan` | `Guru/GuruUjianController.php`<br>`pengawasan()` : 436 | `resources/views/guru/lms/ujian/pengawasan.blade.php` | `resources/css/guru/lms/ujian/pengawasan.css`<br>`resources/js/guru/lms/ujian/pengawasan.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/soal` | `Guru/GuruUjianController.php`<br>`soal()` : 533 | `resources/views/guru/lms/ujian/soal.blade.php` | `resources/css/guru/lms/ujian/soal.css`<br>`resources/js/guru/lms/ujian/soal.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/soal/create` | `Guru/GuruUjianController.php`<br>`createSoal()` : 558 | `resources/views/guru/lms/ujian/soal-form.blade.php` | `resources/css/guru/lms/ujian/soal-form.css`<br>`resources/js/guru/lms/ujian/soal-form.js` |
| `/guru/lms/{kelas}/{mapel}/ujian/{ujian}/soal/{soal}/edit` | `Guru/GuruUjianController.php`<br>`editSoal()` : 641 | `resources/views/guru/lms/ujian/soal-form.blade.php` | `resources/css/guru/lms/ujian/soal-form.css`<br>`resources/js/guru/lms/ujian/soal-form.js` |

### Siswa — 25 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/siswa/lms/dashboard` | `Siswa/LmsDashboardController.php`<br>`index()` : 25 | `resources/views/siswa/lms/dashboard.blade.php` | `resources/css/siswa/lms/dashboard.css` |
| `/siswa/lms/guru` | `Siswa/LmsDashboardController.php`<br>`guru()` : 671 | `resources/views/siswa/lms/guru.blade.php` | `resources/css/siswa/lms/guru.css` |
| `/siswa/lms/jadwal` | `Siswa/LmsDashboardController.php`<br>`jadwal()` : 377 | `resources/views/siswa/lms/jadwal.blade.php` | `resources/css/siswa/lms/jadwal.css`<br>`resources/js/siswa/lms/jadwal.js` |
| `/siswa/lms/jadwal/print` | `Siswa/LmsDashboardController.php`<br>`printJadwal()` : 604 | `resources/views/siswa/lms/jadwal-print.blade.php` | `resources/css/siswa/lms/jadwal-print.css`<br>`resources/js/siswa/lms/jadwal-print.js` |
| `/siswa/lms/kalender` | `Siswa/SiswaDashboardController.php`<br>`kalenderTahunan()` : 146 | `resources/views/siswa/lms/kalender/index.blade.php` | `resources/css/siswa/lms/kalender/index.css`<br>`resources/js/siswa/lms/kalender/index.js` |
| `/siswa/lms/kalender/{tanggal}` | `Siswa/SiswaDashboardController.php`<br>`kalenderDetail()` : 374 | `resources/views/siswa/lms/kalender/detail.blade.php` | `resources/css/siswa/lms/kalender/detail.css` |
| `/siswa/lms/mata-pelajaran/{mapelId}` | `Siswa/LmsMateriController.php`<br>`show()` : 19 | `resources/views/siswa/lms/mata-pelajaran/show.blade.php` | `resources/css/siswa/lms/mata-pelajaran/show.css`<br>`resources/js/siswa/lms/mata-pelajaran/show.js` |
| `/siswa/lms/mata-pelajaran/{mapelId}/forum` | `Siswa/LmsForumController.php`<br>`index()` : 26 | `resources/views/siswa/lms/mata-pelajaran/forum/index.blade.php` | `resources/css/siswa/lms/mata-pelajaran/forum/index.css` |
| `/siswa/lms/mata-pelajaran/{mapelId}/forum/{diskusiId}` | `Siswa/LmsForumController.php`<br>`show()` : 76 | `resources/views/siswa/lms/mata-pelajaran/forum/show.blade.php` | `resources/css/siswa/lms/mata-pelajaran/forum/show.css`<br>`resources/js/siswa/lms/mata-pelajaran/forum-show.js` |
| `/siswa/lms/mata-pelajaran/{mapelId}/latihan/{ujianId}` | `Siswa/LmsUjianController.php`<br>`show()` : 23 | `resources/views/siswa/lms/mata-pelajaran/ujian/show_latihan.blade.php` | `resources/css/siswa/lms/mata-pelajaran/ujian/show-latihan.css`<br>`resources/js/siswa/lms/mata-pelajaran/ujian/show-latihan.js` |
| `/siswa/lms/mata-pelajaran/{mapelId}/latihan/{ujianId}/review` | `Siswa/LmsUjianController.php`<br>`review()` : 415 | `resources/views/siswa/lms/mata-pelajaran/ujian/review.blade.php` | `resources/css/siswa/lms/mata-pelajaran/ujian/review.css` |
| `/siswa/lms/mata-pelajaran/{mapelId}/materi/{materiId}` | `Siswa/LmsMateriController.php`<br>`lihatMateri()` : 67 | `resources/views/siswa/lms/mata-pelajaran/materi.blade.php` | `resources/css/siswa/lms/mata-pelajaran/materi.css` |
| `/siswa/lms/mata-pelajaran/{mapelId}/meeting` | `Siswa/SiswaLmsMeetingController.php`<br>`index()` : 15 | `resources/views/siswa/lms/meeting/index.blade.php` | `resources/css/siswa/lms/meeting/index.css` |
| `/siswa/lms/mata-pelajaran/{mapelId}/tugas/{tugasId}` | `Siswa/LmsTugasController.php`<br>`show()` : 17 | `resources/views/siswa/lms/mata-pelajaran/tugas/show.blade.php` | `resources/css/siswa/lms/mata-pelajaran/tugas/show.css` |
| `/siswa/lms/mata-pelajaran/{mapelId}/ujian/{ujianId}` | `Siswa/LmsUjianController.php`<br>`show()` : 23 | `resources/views/siswa/lms/mata-pelajaran/ujian/show_latihan.blade.php` | `resources/css/siswa/lms/mata-pelajaran/ujian/show-latihan.css`<br>`resources/js/siswa/lms/mata-pelajaran/ujian/show-latihan.js` |
| `/siswa/lms/mata-pelajaran/{mapelId}/ujian/{ujianId}/review` | `Siswa/LmsUjianController.php`<br>`review()` : 415 | `resources/views/siswa/lms/mata-pelajaran/ujian/review.blade.php` | `resources/css/siswa/lms/mata-pelajaran/ujian/review.css` |
| `/siswa/lms/pengumuman` | `Siswa/LmsDashboardController.php`<br>`pengumumanIndex()` : 181 | `resources/views/siswa/lms/pengumuman/index.blade.php` | `resources/css/siswa/lms/pengumuman/index.css`<br>`resources/js/siswa/lms/pengumuman/index.js` |
| `/siswa/lms/pengumuman/{id}` | `Siswa/LmsDashboardController.php`<br>`pengumumanDetail()` : 202 | `resources/views/siswa/lms/pengumuman/show.blade.php` | `resources/css/siswa/lms/pengumuman/show.css` |
| `/siswa/lms/tugas` | `Siswa/LmsTugasController.php`<br>`indexAll()` : 137 | `resources/views/siswa/lms/mata-pelajaran/tugas/index.blade.php` | `resources/css/siswa/lms/mata-pelajaran/tugas/index.css` |
| `/siswa/sia/dashboard` | `Siswa/SiaDashboardController.php`<br>`index()` : 27 | `resources/views/siswa/sia/dashboard.blade.php` | `resources/css/siswa/sia/dashboard.css`<br>`resources/js/siswa/sia/dashboard.js` |
| `/siswa/sia/pembayaran` | `Siswa/SiaPembayaranController.php`<br>`index()` : 18 | `resources/views/siswa/sia/pembayaran/index.blade.php` | `resources/css/siswa/sia/pembayaran/index.css` |
| `/siswa/sia/pembayaran/cetak/{pembayaran}` | `Siswa/SiaPembayaranController.php`<br>`cetakBukti()` : 143 | `resources/views/siswa/sia/pembayaran/cetak.blade.php` | — |
| `/siswa/sia/pembayaran/riwayat` | `Siswa/SiaPembayaranController.php`<br>`riwayat()` : 122 | `resources/views/siswa/sia/pembayaran/riwayat.blade.php` | `resources/css/siswa/sia/pembayaran/riwayat.css` |
| `/siswa/sia/penilaian` | `Siswa/SiaDashboardController.php`<br>`penilaian()` : 136 | `resources/views/siswa/sia/penilaian/index.blade.php` | `resources/css/siswa/sia/penilaian/index.css`<br>`resources/js/siswa/sia/penilaian/index.js` |
| `/siswa/sia/presensi` | `Siswa/SiaPresensiController.php`<br>`index()` : 18 | `resources/views/siswa/sia/presensi/index.blade.php` | `resources/css/siswa/sia/presensi/index.css`<br>`resources/js/siswa/sia/presensi/index.js` |

### Wali Siswa (Orang Tua) — 11 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/wali-siswa/dashboard` | `OrangTua/OrangTuaController.php`<br>`dashboard()` : 75 | `resources/views/wali-siswa/dashboard.blade.php` | `resources/css/wali-siswa/dashboard.css`<br>`resources/js/wali-siswa/dashboard.js` |
| `/wali-siswa/pembayaran/snap/{pembayaran}` | `OrangTua/OrangTuaController.php`<br>`snapPayment()` : 1046 | `resources/views/wali-siswa/pembayaran/snap.blade.php` | `resources/css/wali-siswa/pembayaran/snap.css`<br>`resources/js/wali-siswa/pembayaran/snap.js` |
| `/wali-siswa/pembayaran/{pembayaran}/invoice` | `OrangTua/OrangTuaController.php`<br>`cetakInvoice()` : 1409 | `resources/views/wali-siswa/tagihan/invoice.blade.php` | `resources/css/wali-siswa/tagihan/invoice.css`<br>`resources/js/wali-siswa/tagihan/invoice.js` |
| `/wali-siswa/presensi/anak/{siswa}` | `OrangTua/OrangTuaController.php`<br>`presensiAnak()` : 716 | `resources/views/wali-siswa/presensi/index.blade.php` | `resources/css/wali-siswa/presensi/index.css` |
| `/wali-siswa/presensi/anak/{siswa}/ajukan-izin` | `OrangTua/OrangTuaController.php`<br>`ajukanIzin()` : 772 | `resources/views/wali-siswa/presensi/ajukan-izin.blade.php` | `resources/css/wali-siswa/presensi/ajukan-izin.css` |
| `/wali-siswa/presensi/anak/{siswa}/riwayat-izin` | `OrangTua/OrangTuaController.php`<br>`riwayatIzin()` : 880 | `resources/views/wali-siswa/presensi/riwayat-izin.blade.php` | `resources/css/wali-siswa/presensi/riwayat-izin.css` |
| `/wali-siswa/presensi/anak/{siswa}/riwayat-presensi` | `OrangTua/OrangTuaController.php`<br>`riwayatPresensi()` : 910 | `resources/views/wali-siswa/presensi/riwayat-presensi.blade.php` | `resources/css/wali-siswa/presensi/riwayat-presensi.css` |
| `/wali-siswa/presensi/edit-izin/{presensi}` | `OrangTua/OrangTuaController.php`<br>`editIzin()` : 950 | `resources/views/wali-siswa/presensi/edit-izin.blade.php` | `resources/css/wali-siswa/presensi/edit-izin.css` |
| `/wali-siswa/rapor/anak/{siswa}` | `OrangTua/OrangTuaController.php`<br>`raporAnak()` : 651 | `resources/views/wali-siswa/rapor/index.blade.php` | `resources/css/wali-siswa/rapor/index.css`<br>`resources/js/wali-siswa/rapor/index.js` |
| `/wali-siswa/rapor/detail/{rapor}` | `OrangTua/OrangTuaController.php`<br>`detailRapor()` : 687 | `resources/views/wali-siswa/rapor/detail.blade.php` | `resources/css/wali-siswa/rapor/detail.css` |
| `/wali-siswa/tagihan/anak/{siswa}` | `OrangTua/OrangTuaController.php`<br>`tagihanAnak()` : 161 | `resources/views/wali-siswa/tagihan/index.blade.php` | `resources/css/wali-siswa/tagihan/index.css`<br>`resources/js/wali-siswa/tagihan/index.js` |

### Halaman Publik & Umum — 24 halaman

| Alamat | Controller : baris | Tampilan (Blade) | Aset CSS/JS |
|---|---|---|---|
| `/` | `LandingPageController.php`<br>`home()` : 28 | `resources/views/home.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/home.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/home.js` |
| `/account/settings` | `AccountController.php`<br>`settings()` : 16 | `resources/views/account/settings.blade.php` | `resources/css/account/settings.css`<br>`resources/js/account/settings.js` |
| `/admin-recovery` | `Auth/AdminRecoveryController.php`<br>`showLinkRequestForm()` : 25 | `resources/views/auth/admin-recovery.blade.php` | `resources/css/pages/login.css`<br>`resources/js/pages/auth.js` |
| `/berita` | `BeritaController.php`<br>`index()` : 10 | `resources/views/berita.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/berita.css`<br>`resources/js/navbar.js` |
| `/fasilitas` | `LandingPageController.php`<br>`fasilitas()` : 89 | `resources/views/fasilitas.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/fasilitas.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/fasilitas.js` |
| `/galeri` | `LandingPageController.php`<br>`galeri()` : 101 | `resources/views/galeri.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/galeri.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/galeri.js` |
| `/kebijakan-privasi` | `LandingPageController.php`<br>`kebijakanPrivasi()` : 113 | `resources/views/kebijakan-privasi.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/kebijakan-privasi.css`<br>`resources/js/navbar.js` |
| `/kontak` | `LandingPageController.php`<br>`kontak()` : 107 | `resources/views/kontak.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/kontak.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/kontak.js` |
| `/login` | `Auth/LoginController.php`<br>`create()` : 17 | `resources/views/auth/login.blade.php` | `resources/css/pages/login.css`<br>`resources/js/pages/login.js`<br>`resources/js/pages/auth.js` |
| `/notifications` | `NotificationController.php`<br>`index()` : 22 | `resources/views/notifications/index.blade.php` | `resources/css/notifications/index.css`<br>`resources/js/notifications/index.js` |
| `/notifications/{id}` | `NotificationController.php`<br>`show()` : 61 | `resources/views/notifications/show.blade.php` | `resources/css/notifications/show.css` |
| `/ppdb` | `LandingPageController.php`<br>`ppdb()` : 95 | `resources/views/ppdb.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/ppdb.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/ppdb.js` |
| `/profil-guru` | `LandingPageController.php`<br>`profilGuru()` : 47 | `resources/views/profil-guru.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/profil-guru.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/profil-guru.js` |
| `/profile` | `ProfileController.php`<br>`index()` : 16 | `resources/views/profile/index.blade.php` | `resources/css/profile/index.css`<br>`resources/js/profile/index.js` |
| `/program-inklusi` | `LandingPageController.php`<br>`programInklusi()` : 77 | `resources/views/program-inklusi.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/js/navbar.js` |
| `/program-paud-tk` | `LandingPageController.php`<br>`programPaudTk()` : 65 | `resources/views/program-paud-tk.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/paud-tk.css`<br>`resources/js/navbar.js` |
| `/program-sd-sma` | `LandingPageController.php`<br>`programSdSma()` : 71 | `resources/views/program-sd-sma.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/program-sd-sma.css`<br>`resources/js/navbar.js`<br>`resources/js/pages/program-sd-sma.js` |
| `/program-terapi` | `LandingPageController.php`<br>`programTerapi()` : 83 | `resources/views/program-terapi.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/js/navbar.js` |
| `/recovery` | `Auth/UserRecoveryController.php`<br>`index()` : 23 | `resources/views/auth/user-recovery.blade.php` | `resources/css/pages/login.css`<br>`resources/js/pages/auth.js` |
| `/sitemap.xml` | `SitemapController.php`<br>`index()` : 12 | `resources/views/sitemap.blade.php` | — |
| `/struktur-organisasi` | `LandingPageController.php`<br>`strukturOrganisasi()` : 59 | `resources/views/struktur-organisasi.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/struktur-organisasi.css`<br>`resources/js/navbar.js` |
| `/syarat-ketentuan` | `LandingPageController.php`<br>`syaratKetentuan()` : 118 | `resources/views/syarat-ketentuan.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/syarat-ketentuan.css`<br>`resources/js/navbar.js` |
| `/tentang-sekolah` | `LandingPageController.php`<br>`tentangSekolah()` : 41 | `resources/views/tentang-sekolah.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/css/pages/about.css`<br>`resources/js/navbar.js` |
| `/visi-misi` | `LandingPageController.php`<br>`visiMisi()` : 53 | `resources/views/visi-misi.blade.php` | `resources/css/landing.css`<br>`resources/css/navbar.css`<br>`resources/js/navbar.js` |

---

## BAB 18 — Pertanyaan desain: "kenapa tidak dibikin begini?"

Bab ini menghadapi penguji yang **menantang keputusan desain**, bukan menanyakan lokasi
kode. Semua fakta di bawah **sudah diverifikasi langsung ke kode**, bukan perkiraan.

---

### Cara menjawab pertanyaan jenis ini — baca ini dulu

**Pahami niat pengujinya.** Saat dia bertanya *"kenapa tidak dibikin begini?"*, sembilan
dari sepuluh kali dia **bukan sedang menyalahkan**. Dia sedang menguji satu hal:
**apakah kalian sadar ada alternatif lain, dan apakah pilihan kalian disengaja atau
kebetulan.**

Karena itu:

| Jawaban terburuk | Kenapa fatal |
|---|---|
| *"Tidak kepikiran, Pak."* | Menunjukkan desainnya tidak disengaja |
| *"Yang saya buat sudah paling benar."* | Terkesan tidak bisa menerima masukan |
| Diam lalu menebak-nebak | Terlihat tidak menguasai |

**Rumus empat langkah — pakai ini untuk pertanyaan apa pun jenis ini:**

1. **Akui premisnya.** *"Betul Pak, cara itu memang bisa dipakai."*
2. **Sebutkan alasan memilih yang sekarang.** Konteks sekolah, keterbatasan waktu,
   kesederhanaan, atau kebutuhan pengguna.
3. **Tunjukkan bukti di kode** kalau ada.
4. **Akui kalau alternatifnya memang lebih baik**, dan sebut sebagai pengembangan lanjutan.

> **Contoh utuh.** *"Kenapa hapus data tidak pakai soft delete?"*
>
> *"Betul Pak, soft delete memang lebih aman untuk data penting. Saat ini penghapusan
> bersifat permanen, tapi kami menutupinya dengan dua cara: penghapusan dijaga
> pengecekan keterkaitan data — misalnya siswa yang sudah punya nilai atau tagihan tidak
> bisa dihapus — dan transaksi keuangan punya jejak audit tersendiri. Untuk pengembangan
> berikutnya, soft delete memang sebaiknya ditambahkan pada data induk seperti siswa dan
> tagihan."*

**Yang membuat jawaban ini kuat:** mengakui, memberi alasan, menunjukkan pengaman yang
ada, lalu menerima perbaikan. Kalian terlihat menguasai sekaligus terbuka.

---

### A. Peran dan hak akses

#### A1. "Kenapa Admin bisa membuka semua halaman? Bukankah itu berbahaya?"

**Fakta:** `app/Http/Middleware/CheckRole.php:28-30` — admin langsung diloloskan.

> **Jawab:** Admin adalah level tertinggi dan bertugas menangani seluruh sistem saat ada
> masalah di peran mana pun. Kalau admin tidak diberi jalur ini, dia harus didaftarkan
> ulang ke setiap grup route — dan justru di situ risikonya: satu grup terlewat, muncul
> celah. Dengan satu pintu, aturannya jelas dan mudah diaudit. Risikonya kami kelola dari
> sisi lain: akun admin dilindungi pengaturan keamanan tambahan, dan setiap tindakan
> keuangan tercatat di jejak audit.

**Kalau didesak** *"berarti admin bisa mengubah nilai siswa?"* → Bisa secara teknis, dan
itu memang konsekuensi peran admin di hampir semua sistem sekolah. Yang kami siapkan
adalah keterlacakan: perubahan tercatat, termasuk siapa terakhir mengubah nilai.

#### A2. "Kenapa tidak pakai Policy Laravel? Bukankah itu standarnya?"

**Fakta:** `app/Policies` **tidak ada**; tidak ada `Gate::define`, `authorize()`, maupun
`@can()` di seluruh aplikasi. Pembatasan memakai middleware `role:` plus method penjaga
di controller — contoh `WaliKelas/PresensiController.php:45` `assertKelasMilikWali()`,
`WaliKelas/RaporController.php:580` `assertRaporMilikWali()`,
`Guru/GuruMateriController.php:434` `verifyAccess()`.

> **Jawab:** Betul, Policy adalah cara idiomatis Laravel. Kami memilih middleware karena
> pembatasan di sistem ini bersifat **per peran dan per rute**, bukan per objek — sembilan
> peran dengan wilayah menu yang tidak bertumpuk. Middleware menutup seluruh grup rute
> sekaligus, jadi tidak mungkin ada satu rute yang lupa dilindungi. Untuk pembatasan yang
> memang per objek — misalnya wali kelas hanya boleh menyentuh kelasnya sendiri — kami
> menulis method penjaga eksplisit di controller.

**Kalau didesak** *"kenapa tidak keduanya?"* → Idealnya memang penjaga per-objek itu
dipindah ke Policy supaya seragam dan bisa diuji terpisah. Itu kami catat sebagai
perbaikan lanjutan. Fungsinya sudah terpenuhi, yang kurang adalah kerapian.

#### A3. "Kenapa perannya sampai sembilan? Tidak terlalu banyak?"

**Fakta:** `database/seeders/RoleSeeder.php:17-75`, sembilan peran dengan level 1-6.

> **Jawab:** Jumlahnya mengikuti struktur organisasi PKBM yang sebenarnya, bukan kami
> karang. Tiap peran punya wewenang yang benar-benar berbeda: bendahara tidak boleh
> menyentuh nilai, wali kelas tidak boleh membuat tagihan. Kalau digabung, kami harus
> membuat sistem izin per menu yang justru lebih rumit dan lebih rawan salah. Sistem
> peran ini juga bertingkat, sehingga peran yang lebih tinggi bisa memantau yang di
> bawahnya.

#### A4. "Kenapa ada dua sistem peran di tabel pengguna?"

**Fakta:** `CheckRole.php:36-42` — utamakan `role_id` ke tabel `roles`, cadangan ke kolom
lama `role`.

> **Jawab:** Sistem awal menyimpan peran sebagai kolom pilihan tetap. Setiap menambah
> peran baru harus mengubah struktur tabel — tidak fleksibel. Kami pindah ke tabel peran
> tersendiri supaya peran bisa ditambah tanpa mengubah struktur, dan bisa punya tingkatan.
> Jalur cadangan dipertahankan supaya akun lama tetap bisa login selama masa peralihan.

**Kalau didesak** *"kenapa yang lama tidak dihapus saja?"* → Karena penghapusan kolom itu
tidak bisa dibatalkan, dan kami memilih tidak melakukannya menjelang masa penilaian.
Langkah berikutnya: migrasikan seluruh akun ke `role_id`, pastikan tidak ada yang
tertinggal, baru kolom lama dihapus.

#### A5. "Kalau siswa mengetik alamat halaman bendahara, bisa masuk?"

**Fakta:** `CheckRole.php:45-51` → `abort(403)`.

> **Jawab:** Tidak bisa. Pembatasannya di level rute, bukan sekadar menyembunyikan menu,
> jadi mengetik alamat manual tetap ditolak. **Silakan dicoba langsung, Pak.**

> 💡 *Kalau ada kesempatan, praktikkan. Ini demo paling meyakinkan dan hanya butuh 10 detik.*

#### A6. "Kenapa ditolak 403 tapi penggunanya tidak dikeluarkan?"

**Fakta:** `CheckRole.php:46-50`, lengkap dengan komentar alasannya di kode.

> **Jawab:** Karena itu kegagalan **otorisasi**, bukan **autentikasi**. Penggunanya tetap
> sah, hanya halaman itu yang tidak boleh. Versi awal sempat mengeluarkan pengguna secara
> otomatis, tapi itu membuang pekerjaan yang sedang dikerjakan dan bisa disalahgunakan —
> cukup kirimkan satu tautan terlarang untuk memaksa orang lain keluar. Pesannya juga
> sengaja dibuat umum, tanpa menyebut nama peran, supaya tidak membocorkan struktur akses.

---

### B. Basis data dan keutuhan data

#### B1. "Kenapa tidak pakai soft delete? Kalau salah hapus bagaimana?"

**Fakta:** **Nol dari 53 model** memakai `SoftDeletes`. Ada 78 pemanggilan `delete()`
di controller.

> **Jawab:** *(pakai contoh lengkap di awal bab ini)* — akui, sebutkan pengaman
> pengganti (pengecekan keterkaitan data sebelum hapus, jejak audit keuangan), lalu
> akui sebagai perbaikan lanjutan.

**Ini kelemahan paling sah di sistem kalian. Jangan dibela mati-matian** — akui dengan
tenang, itu justru menaikkan nilai.

#### B2. "Kenapa tabel nilai kolomnya banyak sekali? Kenapa tidak dinormalisasi?"

**Fakta:** `app/Models/Nilai.php:14-20` — 23 kolom komponen, ditambah pasangan berakhiran
`_guru` di baris 44-49.

> **Jawab:** Betul, secara teori normalisasi, komponen nilai bisa dipecah jadi tabel
> tersendiri berisi baris per komponen. Kami memilih kolom tetap karena **struktur
> penilaian di PKBM ini sudah baku**: lima tugas, lima latihan, lima ulangan harian, PTS,
> PAS, keterampilan. Jumlahnya tidak berubah tiap semester. Dengan kolom tetap,
> pengambilan satu baris nilai cukup satu query tanpa penggabungan tabel, dan tampilan
> rapor jauh lebih sederhana. Kalau nanti komponennya jadi dinamis per mata pelajaran,
> barulah normalisasi itu diperlukan.

#### B3. "Kenapa nilai disimpan dua kali — ada kolom biasa dan kolom `_guru`?"

**Fakta:** `Nilai.php:44-49`, plus `guru_terakhir_simpan_at` dan `wali_terakhir_edit_at`
di baris 30-31.

> **Jawab:** Itu bukan duplikasi tanpa alasan, tapi **penyimpanan nilai asli dari guru**.
> Kolom berakhiran `_guru` adalah salinan apa adanya dari guru pengajar; kolom biasa
> adalah nilai yang boleh disesuaikan wali kelas saat menyusun rapor. Dengan dipisah,
> sistem tetap bisa menunjukkan nilai asli meskipun sudah ada penyesuaian — jadi ada
> jejak, dan penyesuaian tidak menghapus data asal. Kami juga mencatat kapan terakhir
> guru menyimpan dan kapan terakhir wali kelas mengubah.

> 💡 *Ini salah satu jawaban terkuat yang kalian punya. Hafalkan.*

#### B4. "Kenapa carryover menggandakan tagihan? Kenapa tidak ubah tagihan lamanya saja?"

**Fakta:** `app/Models/Tagihan.php:52-63` (`tagihanAsal`, `tagihanAlihan`), `:69`
(`scopeBelumLunasOriginal`), `:78` (`tandaiDialihkan`), `:127-132` (propagasi lunas).

> **Jawab:** Karena tagihan lama adalah **catatan keuangan tahun ajaran yang sudah
> ditutup** — mengubahnya berarti mengubah riwayat, dan laporan tahun lalu jadi tidak
> cocok lagi. Kami membuat tagihan baru di tahun aktif dan menghubungkan keduanya dua
> arah. Supaya tidak terhitung dobel, ada penyaring khusus yang mengecualikan tagihan
> lama yang sudah dialihkan. Dan begitu tagihan baru lunas, status lunasnya
> dipropagasikan ke tagihan asal supaya riwayat tetap konsisten.

#### B5. "Status tagihan disimpan di kolom. Bagaimana kalau tidak sinkron dengan pembayarannya?"

**Fakta:** `Tagihan.php:103` `updateStatusBayar()` menghitung ulang dari penjumlahan
pembayaran berstatus `disetujui`.

> **Jawab:** Statusnya **tidak pernah diketik manual** — selalu dihitung ulang dari total
> pembayaran yang sudah disetujui setiap kali ada perubahan. Jadi kolom itu berfungsi
> sebagai hasil hitungan yang disimpan, bukan sumber kebenaran. Sumber kebenarannya tetap
> tabel pembayaran.

#### B6. "Indeks database-nya bagaimana? Tidak akan lambat?"

**Fakta:** 124 foreign key, 20 batasan unik, 3 `->index()` eksplisit.

> **Jawab:** Sebagian besar pencarian terjadi lewat kolom penghubung antar tabel, dan
> MySQL membuat indeks otomatis untuk setiap foreign key — di sistem ini ada 124. Kami
> juga memakai 20 batasan unik untuk mencegah data kembar. Untuk kolom yang sering
> disaring tapi bukan penghubung — misalnya status dan tanggal — indeks tambahan memang
> **belum** kami pasang, dan itu langkah optimasi berikutnya kalau datanya sudah besar.

---

### C. Arsitektur kode

#### C1. "Kenapa semua rute ditaruh di satu berkas 1.600 baris? Kenapa tidak dipecah?"

**Fakta:** `routes/web.php`, 750 rute bernama, dikelompokkan per peran dengan penanda
komentar besar.

> **Jawab:** Bisa dipecah, dan Laravel mendukungnya. Kami memilih satu berkas karena
> pembatasan akses di sistem ini **paling berisiko kalau tercecer** — dengan semua grup
> peran terlihat dalam satu layar, mudah memastikan tidak ada rute yang lolos tanpa
> middleware. Setiap peran diberi penanda blok komentar, jadi masih mudah dinavigasi.
> Kalau nanti bertambah besar, pemecahan per peran memang lebih rapi.

#### C2. "Ada controller lebih dari 1.200 baris. Tidak terlalu gemuk?"

**Fakta:** `Bendahara/TagihanController.php` — 25 method, method `index()` sendiri
menempati baris 43-205.

> **Jawab:** Betul, itu terlalu panjang dan kami menyadarinya. Penyebabnya satu controller
> menangani banyak varian pembuatan tagihan: massal, kustom, SPP otomatis, duplikasi, dan
> carryover. Yang sudah kami lakukan: logika paling berat **sudah dikeluarkan** ke
> `app/Services` — carryover misalnya, ada di service tersendiri. Langkah berikutnya
> memecah controller ini per jenis tugas.

> 💡 *Perhatikan polanya: akui, jelaskan sebabnya, tunjukkan langkah yang SUDAH diambil,
> baru sebut rencana. Itu jauh lebih kuat daripada sekadar mengakui.*

#### C3. "Kenapa validasi login pakai Form Request tapi yang lain ditulis di controller?"

**Fakta:** `app/Http/Requests/Auth/LoginRequest.php:26-32` — satu-satunya Form Request.
Validasi lain inline, contoh `Sekretaris/SekretarisController.php:676-684`.

> **Jawab:** Login memakai Form Request karena logikanya lebih berat dari sekadar aturan
> isian — ada pembatasan percobaan login dan verifikasi captcha, jadi lebih tepat berdiri
> sebagai kelas tersendiri. Untuk formulir biasa, aturannya pendek dan hanya dipakai di
> satu tempat, jadi kami tulis langsung supaya mudah dibaca berdampingan dengan logikanya.
> Konsistensi memang jadi korban di sini — menyeragamkan semuanya ke Form Request kami
> catat sebagai perbaikan.

#### C4. "Kenapa setiap berkas CSS harus didaftarkan manual? Merepotkan."

**Fakta:** `vite.config.js` — 515 entri aset.

> **Jawab:** Itu konsekuensi dari pilihan memuat gaya **per halaman**, bukan satu berkas
> besar untuk seluruh aplikasi. Keuntungannya: halaman hanya memuat gaya yang dia
> butuhkan, dan perubahan gaya di satu halaman tidak berisiko merusak halaman lain —
> penting karena sembilan peran punya tampilan berbeda. Harganya memang pendaftaran manual
> tiap berkas baru. Kalau mau otomatis, Vite bisa diberi pola pencarian folder; itu
> menghilangkan kerepotan tapi mengurangi kendali atas apa yang ikut dibangun.

#### C5. "Bootstrap dan Tailwind terpasang dua-duanya. Kenapa?"

**Fakta:** `package.json` — `bootstrap ^5.3.8`, `tailwindcss ^3.4.18`, plus template
admin Sneat.

> **Jawab:** Yang benar-benar dipakai untuk halaman dashboard adalah **Bootstrap**, karena
> template admin yang kami gunakan berbasis Bootstrap. Tailwind ikut terpasang dari
> kerangka awal Laravel dan tidak kami keluarkan. Itu memang sisa yang sebaiknya
> dibersihkan supaya tidak membingungkan dan tidak menambah beban proses build.

> ⚠️ *Jangan mengarang bahwa keduanya dipakai bersama secara sengaja. Akui saja.*

#### C6. "Kenapa logika berat ditaruh di Services, bukan di Model?"

**Fakta:** 13 service di `app/Services/`.

> **Jawab:** Karena logikanya melibatkan **lebih dari satu model sekaligus**. Kenaikan
> kelas menyentuh siswa, kelas, nilai, dan tagihan; carryover menyentuh dua tahun ajaran.
> Kalau ditaruh di salah satu model, model itu jadi tahu terlalu banyak tentang model lain.
> Model kami pakai untuk hal yang benar-benar miliknya sendiri — contohnya perhitungan
> status tagihan, yang memang urusan tagihan itu saja.

---

### D. Keuangan

#### D1. "Kenapa pembayaran transfer masih perlu disetujui manual? Kenapa tidak otomatis?"

**Fakta:** `Bendahara/PembayaranController.php:164` `validasi()`. Enum metode:
`tunai`, `transfer`, `midtrans` (`migration create_pembayaran_table:19`).

> **Jawab:** Karena untuk transfer manual, **satu-satunya bukti adalah gambar yang
> diunggah wali** — dan gambar bisa direkayasa atau salah nominal. Sistem tidak punya cara
> memastikan uangnya benar-benar masuk ke rekening sekolah. Jadi bendahara harus
> mencocokkan dengan mutasi rekening. Untuk pembayaran yang bisa dipastikan sistem, yaitu
> lewat Midtrans, verifikasinya **memang otomatis** — karena konfirmasinya datang langsung
> dari penyedia pembayaran dan bertanda tangan digital.

#### D2. "Kenapa tidak pakai QRIS saja? Lebih murah dan populer."

**Fakta:** Enum metode hanya `tunai`, `transfer`, `midtrans`. **Tidak ada QRIS sebagai
metode tersendiri.**

> **Jawab:** QRIS tidak kami buat sebagai metode terpisah karena **sudah tercakup di dalam
> Midtrans** — saat wali memilih pembayaran daring, QRIS adalah salah satu pilihan yang
> ditawarkan di halaman Midtrans. Menyediakan QRIS langsung berarti sekolah harus
> mengurus pendaftaran merchant sendiri dan mencocokkan mutasi secara manual, sementara
> lewat Midtrans konfirmasinya otomatis masuk ke sistem.

#### D3. "Kalau ada yang memalsukan notifikasi pembayaran, sistem bisa tertipu?"

**Fakta:** `MidtransWebhookController.php:42` memanggil `MidtransService::verifySignature`
(`:171`). Gagal → 403, tanpa perubahan data.

> **Jawab:** Tidak, karena setiap notifikasi diperiksa **tanda tangan digitalnya** dulu.
> Tanda tangan itu dibentuk dari nomor pesanan, kode status, jumlah, dan kunci rahasia
> server yang hanya diketahui kami dan Midtrans. Orang luar tidak bisa membuat tanda
> tangan yang cocok. Kalau tidak cocok, sistem membalas penolakan dan **tidak ada satu
> data pun yang diubah**.

**Kalau didesak** *"kenapa alamat itu dikecualikan dari perlindungan CSRF?"* → Karena
pemanggilnya server Midtrans, bukan browser pengguna; server tidak punya sesi sehingga
tidak mungkin mengirim token CSRF. Verifikasi tanda tangan adalah penggantinya, dan untuk
kasus ini justru lebih kuat.

#### D4. "Kalau saat proses pembayaran listrik mati, datanya rusak setengah jalan?"

**Fakta:** `DB::beginTransaction()` di `PembayaranController.php:173, 371, 505`;
`TagihanController.php:372, 564, 733`; `PromotionService.php:405, 458`;
`TunggakanCarryoverService.php:140`.

> **Jawab:** Tidak, karena seluruh operasi keuangan dibungkus **transaksi database**. Kalau
> ada satu langkah gagal, semuanya dibatalkan sekaligus — tidak ada kondisi setengah jadi.
> Satu detail yang sengaja kami atur: **notifikasi ke wali dikirim setelah transaksi
> berhasil**, supaya orang tua tidak menerima pemberitahuan untuk tagihan yang ternyata
> batal tersimpan.

> 💡 *Detail terakhir itu menunjukkan kalian berpikir sampai ke kasus tepi. Sebutkan.*

#### D5. "Bagaimana kalau siswa bayar dua kali untuk tagihan yang sama?"

**Fakta:** `PembayaranController.php:164-294` — saat tagihan menjadi lunas, pembayaran
lain yang masih menunggu untuk tagihan itu dibatalkan, dan dicatat di `FinancialAuditLog`.

> **Jawab:** Saat sebuah tagihan menjadi lunas, pembayaran lain yang masih menggantung
> untuk tagihan itu otomatis dibatalkan, dan pembatalannya tercatat di jejak audit
> keuangan. Jadi tidak ada pembayaran ganda yang lolos terhitung.

#### D6. "Siapa yang bisa mengubah data keuangan? Bagaimana melacaknya?"

**Fakta:** `FinancialAuditLog`, dicatat termasuk oleh webhook
(`MidtransWebhookController.php:81-95`).

> **Jawab:** Yang berhak mengelola adalah bendahara dan admin. Setiap perubahan status
> pembayaran dicatat di tabel jejak audit keuangan — termasuk perubahan yang dilakukan
> sistem sendiri lewat notifikasi Midtrans, yang dicatat sebagai tindakan sistem beserta
> alamat asalnya.

---

### E. Akademik dan LMS

#### E1. "Kenapa presensi diinput wali kelas, bukan guru mata pelajaran yang mengajar?"

**Fakta:** Grup rute guru (`routes/web.php:1218-1404`) **tidak punya satu pun** rute
presensi. Presensi hanya ada di grup wali kelas (`:1109`).
Controller: `WaliKelas/PresensiController.php`.

> ⚠️ **Ini pertanyaan paling berbahaya di daftar ini, karena menyangkut rumusan masalah
> kalian.** Siapkan jawaban yang jujur.

> **Jawab:** Presensi di PKBM ini dicatat **per hari, bukan per jam pelajaran**, mengikuti
> praktik yang berjalan di lembaga — kehadiran siswa direkap harian oleh wali kelas
> sebagai penanggung jawab kelas, lalu dipakai untuk rekap kehadiran di rapor. Karena
> satuannya harian, penanggung jawabnya satu orang per kelas, bukan setiap guru mata
> pelajaran. Kalau nanti dibutuhkan presensi per jam pelajaran, barulah input per guru
> mata pelajaran diperlukan.

**Kalau didesak** *"tapi guru yang tahu siswa hadir atau tidak di jamnya"* → Betul, dan
itu memang keterbatasan pendekatan harian. Yang kami sediakan sekarang: orang tua bisa
mengajukan izin lewat sistem, lalu divalidasi wali kelas, sehingga ketidakhadiran tetap
punya keterangan resmi.

#### E2. "Kenapa rapor harus disetujui Ketua dulu? Kenapa tidak langsung terbit?"

**Fakta:** `WaliKelas/RaporController.php:1049` `kirimValidasi()` → Ketua menyetujui →
`:590` `terbitkan()`.

> **Jawab:** Karena rapor adalah **dokumen resmi lembaga**, bukan catatan internal. Sekali
> terbit dan diterima orang tua, koreksinya merepotkan dan menyangkut kredibilitas
> sekolah. Alur persetujuan ini menyalin proses yang memang berjalan: wali kelas menyusun,
> pimpinan memeriksa, baru diterbitkan. Sistem hanya memindahkannya ke bentuk digital
> sehingga tercatat siapa menyetujui dan kapan.

#### E3. "Kenapa unduh rapor harus minta izin dulu? Bukankah itu hak orang tua?"

**Fakta:** `routes/web.php:1566` minta izin → `RaporController.php:1262`
`approveDownload()` → unduh lewat token (`routes:1567`).

> **Jawab:** Rapor memuat data pribadi siswa, jadi berkasnya tidak kami biarkan bisa
> diambil siapa pun yang kebetulan tahu alamatnya. Dengan alur permintaan, persetujuan,
> dan tautan bertoken, sekolah punya catatan siapa mengunduh apa dan kapan. Hak orang tua
> tetap terpenuhi — yang kami tambahkan adalah keterlacakannya.

#### E4. "AI yang menilai jawaban esai, apa bisa dipercaya?"

**Fakta:** `app/Services/AiGradingService.php`; koreksi manual guru tetap ada di
`Guru/GuruUjianController.php:1050` `koreksiShow()`.

> **Jawab:** AI di sini **membantu, bukan memutuskan**. Hasilnya menjadi usulan nilai yang
> tetap ditinjau dan bisa diubah guru sebelum ditetapkan. Yang benar-benar otomatis penuh
> hanya soal pilihan ganda, karena jawabannya pasti. Kami sengaja tidak menyerahkan
> keputusan nilai sepenuhnya ke AI.

#### E5. "Kenapa kecurangan ujian hanya dicatat, tidak diblokir?"

**Fakta:** `Siswa/LmsUjianController.php:710` `recordPengawasanLog()`;
`Guru/GuruUjianController.php:436` `pengawasan()`.

> **Jawab:** Karena pemblokiran otomatis berisiko **menghukum siswa yang tidak bersalah** —
> koneksi putus, notifikasi masuk, atau tidak sengaja menyentuh layar bisa terbaca sebagai
> pelanggaran, dan kalau ujiannya langsung dihentikan, kerugiannya nyata. Kami memilih
> mencatat kejadian dan menampilkannya ke guru secara langsung, sehingga **keputusan tetap
> di tangan guru** yang tahu konteksnya. Kami mencatat ini sebagai keterbatasan yang
> disadari.

#### E6. "Kalau siswa keluar di tengah ujian, jawabannya hilang?"

**Fakta:** `LmsUjianController.php:449` `autosave()`; waktu mulai dicatat di `:145`.

> **Jawab:** Tidak. Jawaban disimpan berkala secara otomatis, dan status tiap soal
> disimpan terpisah, jadi saat masuk kembali jawabannya masih ada. Waktu mulai juga
> dicatat **di server**, bukan mengandalkan jam di komputer siswa, sehingga batas waktunya
> tidak bisa diakali dengan mengubah jam perangkat.

#### E7. "Kenapa LMS tidak diaktifkan untuk semua jenjang?"

**Fakta:** `CheckLmsAccess.php:36-41` membaca pengaturan `lms_allowed_jenjang` dari
tabel pengaturan.

> **Jawab:** Karena kesiapannya berbeda tiap jenjang — tidak semua jenjang membutuhkan
> pembelajaran daring. Daftar jenjang yang diizinkan **disimpan sebagai pengaturan di
> database**, jadi pihak sekolah bisa mengubahnya sendiri lewat halaman admin tanpa perlu
> mengubah kode maupun memasang ulang aplikasi.

---

### F. Keamanan

#### F1. "Bagaimana kalau ada yang mencoba menebak kata sandi berkali-kali?"

**Fakta:** `LoginRequest.php:94, 110, 119-125` — pembatasan lima percobaan lewat
`RateLimiter`, ditambah captcha Turnstile di `:44-70`.

> **Jawab:** Ada dua lapis. Percobaan login dibatasi lima kali, setelah itu harus menunggu.
> Ditambah captcha Cloudflare Turnstile untuk menyaring percobaan otomatis. Sandinya
> sendiri disimpan dalam bentuk acak satu arah, tidak pernah sebagai teks asli.

**Kalau didesak** *"di laptop ini captchanya tidak muncul"* → Betul, verifikasi captcha
dilewati kalau kuncinya belum diisi, supaya tidak menghalangi pengembangan lokal. Di
server sungguhan kuncinya terisi dan captcha aktif.

#### F2. "Kalau pengguna lupa sandi, prosesnya bagaimana? Aman?"

**Fakta:** `routes/web.php:138-140` jalur admin, `:144-149` jalur pengguna,
model `RecoveryTicket`, plus halaman pengelolaan di `:280`.

> **Jawab:** Pemulihan tidak dilakukan lewat tautan email otomatis, melainkan lewat
> **tiket yang ditinjau admin**. Alasannya konteks lembaga: banyak siswa dan orang tua
> tidak memakai email aktif, sehingga tautan pemulihan lewat email justru tidak sampai.
> Dengan tiket, identitas pemohon diverifikasi manusia dulu. Untuk admin sendiri ada
> jalur terpisah dengan pengaman tambahan, supaya tidak ada jalan pintas.

#### F3. "Bagaimana mencegah SQL injection?"

**Fakta:** Query memakai Eloquent di seluruh aplikasi.

> **Jawab:** Seluruh akses data memakai Eloquent, yang membangun query dengan **parameter
> terikat**, bukan menyambung teks. Jadi masukan pengguna tidak pernah diperlakukan
> sebagai perintah. Kami tidak menulis SQL mentah dari masukan pengguna.

#### F4. "Bagaimana kalau ada yang menyisipkan skrip lewat formulir?"

**Fakta:** Blade `{{ }}` meng-escape otomatis; pemakaian mentah dibungkus `e()` lebih dulu.

> **Jawab:** Semua data yang ditampilkan melewati penulisan Blade yang otomatis mengubah
> tag HTML jadi teks biasa. Untuk isian panjang yang perlu mempertahankan baris baru, kami
> tetap amankan dulu baru ubah barisnya, jadi tidak ada tag yang bisa aktif.

#### F5. "Ada perlindungan lain di tingkat server?"

**Fakta:** `app/Http/Middleware/SecurityHeaders.php:25-37` — `X-Frame-Options: SAMEORIGIN`,
`X-Content-Type-Options: nosniff`, `X-XSS-Protection`, `Referrer-Policy`,
`Permissions-Policy`. Aplikasi berada di belakang Cloudflare.

> **Jawab:** Ada. Setiap respons diberi sejumlah header keamanan: mencegah halaman
> disematkan di situs lain, mencegah penebakan tipe berkas, membatasi kebocoran alamat
> asal, dan mematikan izin kamera, mikrofon, serta lokasi. Aplikasinya juga berada di
> belakang Cloudflare.

#### F6. "Kunci API AI disimpan di mana? Aman?"

**Fakta:** Diambil dari tabel pengaturan (`AiChatbotService.php:25-37`).
`config/services.php` **tidak memuat** Groq; `.env.example` **tidak punya** entri AI.

> **Jawab:** Kunci layanan AI disimpan di **pengaturan aplikasi di database**, bukan di
> berkas konfigurasi, supaya admin sekolah bisa menggantinya sendiri lewat halaman
> pengaturan tanpa menyentuh berkas server. Konsekuensinya, keamanannya bergantung pada
> akses database dan akun admin.

**Kalau didesak** *"bukankah `.env` lebih aman?"* → Untuk kunci yang tidak pernah berubah,
ya. Kami memilih database karena kunci ini memang dimaksudkan bisa diganti operator
sekolah. Idealnya keduanya digabung: nilai bawaan di `.env`, dan pengaturan database
hanya sebagai penimpa.

---

### G. Skala, teknologi, dan masa depan

#### G1. "Kalau siswanya seribu, sistem ini kuat?"

**Fakta:** Pemakaian `paginate()` dan `with()` tersebar di controller; 124 foreign key
otomatis terindeks.

> **Jawab:** Daftar data selalu dibatasi per halaman, dan relasi diambil sekaligus untuk
> menghindari pengambilan berulang. Beban terberatnya ada di laporan dan rekap, dan di
> situ langkah berikutnya adalah menambah indeks pada kolom penyaring serta menyimpan
> sementara hasil laporan. Kami belum melakukan uji beban, jadi kami tidak mengklaim angka
> tertentu.

> ⚠️ *Jangan menyebut angka pengguna bersamaan yang tidak pernah kalian uji.*

#### G2. "Kenapa memilih Laravel, bukan framework lain?"

> **Jawab:** Karena kebutuhan terbesar sistem ini adalah **pengaturan hak akses sembilan
> peran** dan **relasi data yang banyak**, dan Laravel menyediakan keduanya sebagai
> fasilitas bawaan yang matang. Ditambah tersedianya pustaka siap pakai untuk kebutuhan
> nyata sekolah: cetak PDF, impor dan ekspor Excel, serta penyambungan ke penyedia
> pembayaran.

#### G3. "Kenapa aplikasi web, bukan aplikasi ponsel?"

> **Jawab:** Karena penggunanya sangat beragam — admin dan bendahara bekerja di komputer
> dengan tabel lebar dan cetak dokumen, sementara orang tua dan siswa memakai ponsel.
> Aplikasi web bisa melayani keduanya sekaligus dan tidak perlu dipasang. Tampilannya
> dibuat menyesuaikan ukuran layar. Aplikasi ponsel khusus untuk orang tua kami catat
> sebagai pengembangan lanjutan.

#### G4. "Kalau sekolah lain mau memakai, perlu diubah banyak?"

**Fakta:** Model `Cabang`; pengaturan disimpan di tabel `app_settings`.

> **Jawab:** Sebagian besar hal yang berbeda antar sekolah sudah disimpan sebagai
> **pengaturan di database**, bukan ditulis di kode — jenjang yang boleh memakai LMS, nilai
> KKM, informasi rekening, dan isi halaman depan. Sistem juga sudah mendukung banyak
> cabang. Yang perlu disiapkan hanya data awal dan pengaturannya.

#### G5. "Apa kelemahan terbesar sistem ini menurut kalian?"

> **Jawab (jujur, sebutkan tiga):** Pertama, penghapusan data masih permanen — belum ada
> mekanisme pemulihan data terhapus. Kedua, cakupan pengujian otomatis masih terbatas,
> pengujian utama masih manual. Ketiga, beberapa controller terlalu panjang dan sebaiknya
> dipecah. Ketiganya sudah kami identifikasi berikut langkah perbaikannya.

> 💡 *Menyebut kelemahan yang spesifik dan benar justru meyakinkan. Yang mencurigakan
> adalah jawaban "tidak ada kelemahan".*

---

### Ringkasan: lima jawaban paling kuat kalian

Kalau waktu latihan terbatas, hafalkan lima ini — semuanya bertumpu pada keputusan
desain yang benar-benar ada di kode:

1. **Kolom `_guru`** — memisahkan nilai asli guru dari penyesuaian wali kelas (B3).
2. **Verifikasi tanda tangan webhook** — kenapa aman meski dikecualikan dari CSRF (D3).
3. **Notifikasi dikirim setelah transaksi berhasil** — berpikir sampai kasus tepi (D4).
4. **Carryover dua arah** — menjaga riwayat tahun lama tetap utuh (B4).
5. **Konfigurasi di database, bukan di kode** — sekolah bisa mengatur sendiri (E7, G4).

### Ringkasan: tiga hal yang harus diakui, jangan dibela

1. **Tidak ada soft delete** — akui, sebutkan pengaman penggantinya (B1).
2. **Tailwind ikut terpasang tanpa dipakai** — akui sebagai sisa yang belum dibersihkan (C5).
3. **Presensi diinput wali kelas, bukan guru mata pelajaran** — jelaskan alasan harian,
   dan **perbaiki dulu rumusan masalah di PPT** (E1).

---
## PENUTUP — 3 hal yang harus diingat saat sidang

1. **Kalau tidak tahu jawabannya, jangan mengarang.** Katakan:
   _"Untuk detail itu saya perlu cek filenya dulu, Pak/Bu — boleh saya buka?"_
   Membuka file dan menemukan jawabannya **jauh lebih baik** daripada menjawab asal.

2. **Ucapkan apa yang sedang kamu lakukan.** Penguji tidak bisa membaca pikiran.
   _"Ini halaman sekretaris, jadi saya cari di routes/web.php bagian sekretaris,
   ketemu controllernya SekretarisController method pengumumanIndex..."_
   Narasi ini **sendirinya sudah menjadi nilai**, bahkan sebelum kodenya jadi.

3. **Error itu normal, panik yang tidak.** Baca baris pertama pesan error — biasanya
   sudah menyebut nama file & nomor baris. Perbaiki dengan tenang. Programmer yang
   bisa memperbaiki error di depan orang justru terlihat lebih ahli daripada yang
   kodenya kebetulan langsung jalan.

**Semangat. Kalian punya 7 hari — itu cukup, asal latihannya di file asli, bukan di tutorial.**
