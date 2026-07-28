# PANDUAN LATIHAN LIVE CODING — SIDANG SIPADUHOK

> **Untuk siapa:** 3 anggota tim. Semua orang membaca **dokumen yang sama**, karena
> tidak ada yang tahu siapa yang ditanya duluan dan bagian mana.
>
> **Tujuan dokumen ini:** BUKAN mengajari Laravel dari nol. Tujuannya membuat kalian
> bisa **melacak, menjelaskan, dan mengubah** kode yang sudah ada di project ini
> dalam waktu 5–15 menit di depan penguji.
>
> **Aturan emas:** penguji tidak menilai apakah kamu hafal sintaks. Penguji menilai
> apakah kamu **tahu file mana yang harus dibuka**. Orang yang bilang
> _"sebentar Pak, ini ada di controller Sekretaris bagian pengumumanStore, saya buka dulu"_
> terlihat jauh lebih menguasai daripada orang yang diam.

---

## DAFTAR ISI

- [BAGIAN 0 — Jawaban atas kekhawatiranmu](#bagian-0--jawaban-atas-kekhawatiranmu)
- [BAGIAN 1 — Jurus Lacak 4 Langkah (WAJIB HAFAL)](#bagian-1--jurus-lacak-4-langkah-wajib-hafal)
- [BAGIAN 2 — Alur data 1 halaman (mental model)](#bagian-2--alur-data-1-halaman-mental-model)
- [BAGIAN 3 — Aturan keselamatan saat latihan](#bagian-3--aturan-keselamatan-saat-latihan)
- [BAGIAN 4 — 10 SKENARIO LATIHAN (inti dokumen)](#bagian-4--10-skenario-latihan)
- [BAGIAN 5 — Pertanyaan lisan + jawaban siap pakai](#bagian-5--pertanyaan-lisan--jawaban-siap-pakai)
- [BAGIAN 6 — Jadwal 7 hari & pembagian tim](#bagian-6--jadwal-7-hari--pembagian-tim)
- [BAGIAN 7 — Cheat sheet perintah & sintaks](#bagian-7--cheat-sheet-perintah--sintaks)

---

## BAGIAN 0 — Jawaban atas kekhawatiranmu

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
(lihat [migration baris 21](../database/migrations/2025_12_16_093449_create_pengumuman_table.php#L21)).
Tidak pernah ada yang cocok, jadi selalu jatuh ke nilai `default`.

Bug ini ada di **dua file sekaligus** (halaman Sekretaris dan salinannya di Admin) dan
**keduanya sudah diperbaiki**. Yang tersisa untuk kalian bukan memperbaiki, tapi
**bisa menjelaskan** kalau ditanya — itulah isi [Skenario 3](#-skenario-3--diagnosis-bug-badge-prioritas-bug-asli).

---

## BAGIAN 1 — Jurus Lacak 4 Langkah (WAJIB HAFAL)

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

> Tabel lengkap ada di [AGENTS.md](../AGENTS.md) bagian §3. **Print tabel ini.**

### Langkah 2 — Cari route-nya (dapat nama Controller + method)

Buka terminal di folder project:

```bash
php artisan route:list --path=sekretaris/pengumuman
```

Outputnya langsung memberitahu: URL apa → Controller mana → method apa.

**Alternatif tanpa terminal** (lebih cepat kalau grogi): buka
[routes/web.php](../routes/web.php), tekan `Ctrl+F`, ketik `pengumuman`.
Semua route ada di **satu file ini**.

> Contoh nyata — route Pengumuman Sekretaris ada di
> [routes/web.php:932-939](../routes/web.php#L932-L939).

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

## BAGIAN 2 — Alur data 1 halaman (mental model)

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
| 1 | Struktur tabel database | [database/migrations/2025_12_16_093449_create_pengumuman_table.php](../database/migrations/2025_12_16_093449_create_pengumuman_table.php) |
| 2 | Model (jembatan ke tabel) | [app/Models/Pengumuman.php](../app/Models/Pengumuman.php) |
| 3 | Route | [routes/web.php:932-939](../routes/web.php#L932-L939) |
| 4 | Controller | [app/Http/Controllers/Sekretaris/SekretarisController.php:655-752](../app/Http/Controllers/Sekretaris/SekretarisController.php#L655-L752) |
| 5 | View daftar & form | [.../pengumuman/index.blade.php](../resources/views/sekretaris/pengumuman/index.blade.php) · [form.blade.php](../resources/views/sekretaris/pengumuman/form.blade.php) |
| 6 | CSS & JS | `resources/css/sekretaris/pengumuman/{index,form}.css` · `resources/js/sekretaris/pengumuman/index.js` |

---

## BAGIAN 3 — Aturan keselamatan saat latihan

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

## BAGIAN 4 — 10 SKENARIO LATIHAN

**Cara memakai:** kerjakan berurutan dari Skenario 1. Setiap skenario ditulis seperti
permintaan penguji sungguhan. **Latih sambil diberi batas waktu** — minta teman
memegang stopwatch. Setelah selesai, **kembalikan perubahan** (Bagian 3).

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

**File:** [resources/views/sekretaris/pengumuman/index.blade.php](../resources/views/sekretaris/pengumuman/index.blade.php)

**Langkah:**

1. Judul ada di **3 tempat berbeda** — ini poin yang dinilai, jangan cuma ganti satu:
   - **Baris 3** `@section('title', 'Kelola Pengumuman')` → judul di **tab browser**
   - **Baris 4** `@section('page-title', 'Kelola Pengumuman')` → judul di **header halaman**
   - **Baris 30** `<h5>Kelola Pengumuman</h5>` → judul di dalam **kotak toolbar**
2. Ganti ketiganya menjadi `Manajemen Pengumuman Sekolah`.
3. Tombol: cari teks `Tambah Pengumuman` — ada di **baris 36** (tombol atas) dan
   **baris 168** (tombol di tampilan kosong). Ganti keduanya.

**Kemana lagi harus dicek:**
- Nama menu di sidebar: [resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php:42](../resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php#L42)
  (`<div>Pengumuman</div>`). Kalau penguji minta konsisten, ganti di sini juga.
- File form juga punya judul sendiri: [form.blade.php:3-6](../resources/views/sekretaris/pengumuman/form.blade.php#L3-L6)

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

**File:** [resources/views/sekretaris/pengumuman/index.blade.php](../resources/views/sekretaris/pengumuman/index.blade.php)

**Langkah:**

1. **Cek dulu datanya sudah tersedia atau belum.** Buka controller
   [SekretarisController.php:657](../app/Http/Controllers/Sekretaris/SekretarisController.php#L657):
   ```php
   $pengumuman = Pengumuman::with(['kalenderAkademik', 'pembuat'])
   ```
   Ada `'pembuat'` → **datanya sudah diambil, controller tidak perlu diubah.**
   👉 *Katakan ini keras-keras ke penguji. Ini menunjukkan kamu paham eager loading.*

2. Pastikan relasinya ada di model — [Pengumuman.php:42-45](../app/Models/Pengumuman.php#L42-L45):
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
   [migration baris 21](../database/migrations/2025_12_16_093449_create_pengumuman_table.php#L21):
   ```php
   $table->enum('prioritas', ['biasa', 'penting', 'mendesak'])->default('biasa');
   ```
   → Nilai yang sah hanya: `biasa`, `penting`, `mendesak`.

2. Cek **apa yang dicari Blade** — [index.blade.php:22](../resources/views/sekretaris/pengumuman/index.blade.php#L22):
   ```php
   $highCount = $items->where('prioritas', 'tinggi')->count();
   ```
   → Mencari `'tinggi'`, padahal **tidak pernah ada** nilai itu. Hasilnya selalu 0.

3. Bug yang sama di [baris 83-87](../resources/views/sekretaris/pengumuman/index.blade.php#L83-L87):
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
   Sekretaris ([admin/akademik/pengumuman/index.blade.php](../resources/views/admin/akademik/pengumuman/index.blade.php)),
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
   [index.css baris 286-291](../resources/css/sekretaris/pengumuman/index.css#L286-L291).
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

**File:** [app/Http/Controllers/Sekretaris/SekretarisController.php](../app/Http/Controllers/Sekretaris/SekretarisController.php)

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
   [baris 715-723](../app/Http/Controllers/Sekretaris/SekretarisController.php#L715-L723).
   Kalau hanya mengubah `Store`, aturan baru **tidak berlaku saat edit**. Ubah juga di sini.
   👉 *Penguji sering sengaja mengetes ini lewat form edit.*

2. **Batas di sisi HTML.** Buka [form.blade.php baris 55-61](../resources/views/sekretaris/pengumuman/form.blade.php#L55-L61),
   tambahkan `maxlength="100"` pada input judul supaya pengguna dicegah sejak awal:
   ```blade
   <input type="text" ... maxlength="100" required>
   ```

3. **Teks keterangan di form** [baris 123](../resources/views/sekretaris/pengumuman/form.blade.php#L123)
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
  input sebelumnya dari session. Lihat [form.blade.php:59](../resources/views/sekretaris/pengumuman/form.blade.php#L59).
- _"Di mana pesan errornya ditampilkan?"_
  → Lewat direktif `@error('judul') ... @enderror` di
  [form.blade.php:62-64](../resources/views/sekretaris/pengumuman/form.blade.php#L62-L64).
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

**File:** [SekretarisController.php:655-662](../app/Http/Controllers/Sekretaris/SekretarisController.php#L655-L662)
dan [index.blade.php](../resources/views/sekretaris/pengumuman/index.blade.php)

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
  [index.js:17-19](../resources/js/sekretaris/pengumuman/index.js#L17-L19).
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

[app/Models/Pengumuman.php:14-24](../app/Models/Pengumuman.php#L14-L24) — tambahkan
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

Di `pengumumanStore` [baris 676-684](../app/Http/Controllers/Sekretaris/SekretarisController.php#L676-L684),
tambahkan ke array validasi:
```php
'narahubung' => 'nullable|string|max:100',
```
**Ulangi di `pengumumanUpdate`** [baris 715-723](../app/Http/Controllers/Sekretaris/SekretarisController.php#L715-L723).

> Karena controller memakai `Pengumuman::create($validated)`, field yang **tidak
> ditulis di aturan validasi tidak akan ikut tersimpan** — meskipun sudah ada di
> `$fillable` dan sudah dikirim form. Dua-duanya harus ada.

**Langkah 4 — Input di form**

[form.blade.php](../resources/views/sekretaris/pengumuman/form.blade.php) — sisipkan
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

[index.blade.php](../resources/views/sekretaris/pengumuman/index.blade.php) — di dalam
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

**File:** [resources/css/sekretaris/pengumuman/index.css](../resources/css/sekretaris/pengumuman/index.css)

**Langkah:**

1. **Temukan file CSS-nya lewat Blade dulu** (jangan menebak):
   [index.blade.php:12](../resources/views/sekretaris/pengumuman/index.blade.php#L12) →
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
  `info`, `muted` ([index.css:286-291](../resources/css/sekretaris/pengumuman/index.css#L286-L291)).
  Kalau Blade memakai nama kelas di luar keenam itu (misal `secondary` atau `dark`),
  badge-nya muncul **tanpa warna** — dan tidak ada error apapun.
  👉 *Ini contoh sempurna "kenapa harus tahu 2 lapisan sekaligus": Blade benar,
  tapi kalau CSS-nya tidak punya kelas itu, hasilnya tetap salah.*
- Perubahan warna di sini **tidak akan terlihat** kalau kelas yang dipasang Blade
  bukan `danger` — telusuri dulu `$priorityClass` di
  [index.blade.php:82-87](../resources/views/sekretaris/pengumuman/index.blade.php#L82-L87).
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

**File:** [index.blade.php](../resources/views/sekretaris/pengumuman/index.blade.php) +
[index.js](../resources/js/sekretaris/pengumuman/index.js)

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
   [index.js](../resources/js/sekretaris/pengumuman/index.js), sisipkan **di dalam**
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
  komentar di [baris 1](../resources/js/sekretaris/pengumuman/index.js#L1)). Perubahan di
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

[routes/web.php](../routes/web.php), di dalam grup `pengumuman` (**baris 933-939**).
Sisipkan **sesudah baris 934** (`Route::get('/', ...)->name('index');`):
```php
Route::get('/{id}', [SekretarisController::class, 'pengumumanShow'])->name('show');
```

> ⚠️ **Route `/{id}` harus diletakkan SETELAH `/create`.** Kalau ditaruh sebelumnya,
> Laravel akan menganggap kata "create" sebagai `{id}`, sehingga tombol Tambah rusak.
> 👉 *Ini pertanyaan jebakan klasik. Kalau kamu menyebutnya duluan, penguji terkesan.*

**Langkah 2 — Controller**

[SekretarisController.php](../app/Http/Controllers/Sekretaris/SekretarisController.php) —
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

Buka [vite.config.js](../vite.config.js). Cari baris 52-53:
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

[index.blade.php baris 98](../resources/views/sekretaris/pengumuman/index.blade.php#L98) —
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
- File CSS/JS landing sudah terdaftar di [vite.config.js baris 510-536](../vite.config.js#L510-L536).
  Kalau menambah **file baru**, berlaku aturan Skenario 9.

---

## BAGIAN 5 — Pertanyaan lisan + jawaban siap pakai

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

## BAGIAN 6 — Jadwal 7 hari & pembagian tim

### Prinsip

- **Semua orang mengerjakan SEMUA skenario.** Tidak ada yang boleh bilang
  "itu bagian teman saya".
- Tapi setiap orang punya **1 bidang spesialis** untuk pertanyaan mendalam.
- **Latihan bersama, bergantian jadi "penguji".**

### Jadwal

| Hari | Fokus | Target akhir hari |
|---|---|---|
| **H-7** | PHP dasar: variabel, array, `foreach`, `if`, function, class/object | Bisa membaca kode PHP tanpa bingung. **Cukup separuh awal playlist** — lewati topik lanjutan |
| **H-6** | Bagian 1 & 2 dokumen ini + Skenario 1, 2 | Hafal Jurus Lacak 4 Langkah. Bisa menemukan file apapun < 2 menit |
| **H-5** | Skenario 3, 4 | Paham controller & validasi. Bug prioritas diperbaiki |
| **H-4** | Skenario 5, 7, 8 | Paham query, CSS scoped, JS DOM |
| **H-3** | **Skenario 6 & 9** (dua yang tersulit) — ulang **3x** sampai lancar | Bisa tambah field & buat halaman baru tanpa melihat dokumen |
| **H-2** | Bagian 5 (tanya jawab). Simulasi: bergantian jadi penguji, acak skenario, pakai stopwatch | Bisa menjawab tanpa "emmm..." |
| **H-1** | **Bersih-bersih & gladi resik.** `git status` bersih → `npm run build` → `php artisan test` → demo penuh dari login sampai semua fitur | Semua siap. **Jangan menulis kode baru hari ini** |

### Pembagian spesialis (tetap semua belajar semua)

| Orang | Spesialis untuk pertanyaan mendalam | Wajib kuasai ekstra |
|---|---|---|
| **A** | Keuangan (Tagihan, Pembayaran, Midtrans) | `docs/flow/bendahara.md`, alur carryover tunggakan |
| **B** | Akademik/SIA (Siswa, Kelas, Jadwal, Nilai, Rapor, Kenaikan Kelas) | `docs/flow/wali-kelas.md`, `PromotionService` |
| **C** | LMS + Landing Page + role/keamanan | `docs/flow/guru.md`, middleware & sistem role |

> Semua tetap wajib menguasai **10 skenario di Bagian 4** — itu bekal bersama.

### Latihan simulasi (H-2, cara mainnya)

1. Satu orang jadi penguji, buka laptop yang **tidak sedang menampilkan dokumen ini**.
2. Penguji memilih skenario secara **acak**, membacakan bagian "Kata penguji" saja.
3. Peserta mengerjakan **sambil menjelaskan langkahnya dengan suara keras**.
4. Penguji melempar 2 pertanyaan lisan dari skenario tersebut.
5. Nilai: ✅ selesai dalam waktu · ⚠️ selesai tapi lewat waktu · ❌ macet.
6. Yang ❌ diulang keesokan harinya.

---

## BAGIAN 7 — Cheat sheet perintah & sintaks

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
