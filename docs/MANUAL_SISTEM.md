# Manual Sistem SIPADUHOK - Cara Kerja & Logika Kode

> Buku ini menjelaskan **cara kerja** aplikasi SIPADUHOK: dari alur besar, tiap peran,
> tiap menu, sampai tiap tombol fitur - **beserta logika kode di baliknya** dan
> **perumpamaan sederhana** supaya mudah dijelaskan saat sidang.
>
> Ditulis untuk yang baru belajar Laravel dasar. Tidak perlu hafal kode; cukup paham
> **alur** dan **kenapa** tiap bagian ada.

## Daftar Isi
- Bab 1. Perumpamaan Besar: Aplikasi ini seperti sebuah Kantor Sekolah
- Bab 2. Perjalanan Satu Klik (Request Lifecycle) - inti yang wajib dipahami
- Bab 3. Peran (Role) & Routing - kenapa tiap orang lihat menu berbeda
- Bab 4. Model & Relasi Data (Eloquent) - lemari arsip yang saling terhubung
- Bab 5. Tampilan (Blade) & Aset (Vite)
- Bab 6. Konsep Sentral: Tahun Ajaran
- Bab 7. Peran ADMIN (semua menu)
- Bab 8. Peran KETUA & WAKA (kenaikan, dispensasi, jadwal multi-jenjang, ganti guru)
- Bab 9. Peran SEKRETARIS & BENDAHARA (jalur uang, Midtrans)
- Bab 10. Peran WALI KELAS & GURU (nilai, mesin ujian online, rapor)
- Bab 11. Peran SISWA & ORANG TUA (akses terkunci ke milik sendiri)
- Bab 12. Gambaran Besar: Alur Antar-Peran (nilai, pembayaran, kenaikan)

---

## Bab 1. Perumpamaan Besar: Aplikasi ini seperti sebuah Kantor Sekolah

Bayangkan SIPADUHOK adalah **gedung kantor sekolah** dengan banyak ruangan.

- **Pengunjung** = pengguna yang membuka halaman (mengetik URL / klik tombol).
- **Resepsionis** = *Router* (`routes/web.php`). Dia membaca "mau ke mana?" (URL) lalu
  mengarahkan ke ruangan yang tepat.
- **Satpam di pintu tiap lantai** = *Middleware* (mis. `role:admin`). Mengecek kartu
  identitas (peran) sebelum mengizinkan masuk. Kalau tidak berhak -> ditolak (403).
- **Staf yang mengerjakan permintaan** = *Controller*. Dia mengambil data, memproses,
  lalu menyiapkan jawaban.
- **Lemari arsip** = *Database*. Tempat semua data disimpan (siswa, nilai, tagihan, dst).
- **Petugas arsip yang paham isi lemari** = *Model / Eloquent*. Controller tinggal bilang
  "ambilkan data siswa nomor 5", petugas ini yang tahu cara mengambilnya.
- **Formulir / halaman yang dicetak untuk pengunjung** = *View / Blade*. Hasil akhir yang
  dilihat mata pengguna.

Jadi setiap kali seseorang mengklik sesuatu, terjadi rantai:
**Pengunjung -> Resepsionis (Router) -> Satpam (Middleware) -> Staf (Controller) ->
Petugas arsip (Model) -> Lemari (Database) -> kembali ke Staf -> Formulir (View) ->
Pengunjung melihat hasil.**

Kalau kamu paham rantai ini, kamu bisa menjelaskan **fitur apa pun** di aplikasi, karena
semuanya mengikuti pola yang sama.

---

## Bab 2. Perjalanan Satu Klik (Request Lifecycle)

Ini bagian terpenting. Mari ikuti satu contoh nyata: **Admin membuka menu Dashboard**.

**Langkah 1 - Pengguna minta halaman.**
Browser mengirim permintaan ke `GET /admin/dashboard`.

**Langkah 2 - Router mencocokkan URL.**
Di `routes/web.php` ada grup:
```php
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    ...
});
```
Artinya: "semua URL yang diawali `/admin`, jaga dengan satpam `role:admin`, dan namai
route-nya diawali `admin.`". URL `/admin/dashboard` cocok -> diarahkan ke method
`index()` di `DashboardController`.

> Perumpamaan: resepsionis melihat tujuan "Dashboard Admin", lalu menunjuk lift ke lantai
> Admin, ruangan Dashboard.

**Langkah 3 - Middleware (satpam) mengecek hak akses.**
Sebelum masuk, middleware `role:admin` (`app/Http/Middleware/CheckRole.php`) berjalan:
```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();
    if (!$user) return redirect()->route('login');   // belum login -> ke halaman login
    if ($user->isAdmin()) return $next($request);     // admin boleh masuk ke SEMUA ruangan
    // selain admin: cek apakah perannya cocok dgn yang dibutuhkan ruangan ini
    ...
}
```
Poin penting:
- **Belum login** -> dilempar ke halaman login.
- **Admin = kunci master**: `isAdmin()` true -> langsung diloloskan ke ruangan mana pun.
- **Peran lain** -> hanya boleh kalau perannya cocok, kalau tidak -> **403 (ditolak)**.
  403 **bukan** berarti logout; hanya "kamu tidak berhak ke sini".

**Langkah 4 - Controller mengerjakan.**
`DashboardController::index()` mengambil data (mis. jumlah siswa, tagihan, dll) lalu:
```php
return view('dashboard.admin', compact('totalSiswa', 'totalGuru', ...));
```
Artinya: "siapkan halaman `dashboard.admin`, dan bawakan data-data ini ke halaman".

> Perumpamaan: staf mengumpulkan berkas dari lemari arsip, menaruhnya di atas formulir,
> lalu menyerahkan formulir itu ke pengunjung.

**Langkah 5 - View (Blade) dirender jadi HTML.**
File `resources/views/dashboard/admin.blade.php` mengubah data menjadi tampilan (kartu
statistik, grafik). Hasil akhirnya HTML yang dikirim balik ke browser.

**Ringkas untuk sidang:** "Saat tombol diklik, Laravel menjalankan urutan
Route -> Middleware -> Controller -> Model/Database -> View. Router menentukan tujuan,
Middleware menjaga hak akses, Controller memproses, Model mengambil data, View menampilkan."

---

## Bab 3. Peran (Role) & Routing - kenapa tiap orang lihat menu berbeda

Aplikasi punya **9 peran**. Tiap peran ibarat **satu lantai gedung** dengan menu sendiri.
Polanya sangat konsisten - kalau tahu satu, tahu semua:

| Peran | Awalan URL | Nama Route | Folder Controller | Folder View |
|---|---|---|---|---|
| admin (akses SEMUA) | `/admin` | `admin.` | `Admin/` | `admin/` |
| ketua_pkbm | `/ketua` | `ketua.` | `Ketua/` | `ketua/` |
| wakil_kepala_sekolah | `/waka` | `waka.` | `WakilKepalaSekolah/` | `waka/` |
| sekretaris | `/sekretaris` | `sekretaris.` | `Sekretaris/` | `sekretaris/` |
| bendahara | `/bendahara` | `bendahara.` | `Bendahara/` | `bendahara/` |
| wali_kelas | `/wali` | `wali.` | `WaliKelas/` | `wali-kelas/` |
| guru_pengajar | `/guru` | `guru.` | `Guru/` | `guru/` |
| siswa | `/siswa` | `siswa.` | `Siswa/` | `siswa/` |
| orang_tua (Wali Siswa) | `/wali-siswa` | `wali-siswa.` | `OrangTua/` | `wali-siswa/` |

**Cara kerja pembeda menu:** setiap grup route dijaga middleware `role:<nama>`. Karena
`admin` adalah kunci master, admin bisa masuk semua lantai; peran lain hanya lantainya
sendiri. Menu di sidebar pun ditampilkan per-peran (tiap peran punya file sidebar sendiri,
mis. `resources/views/<role>/partials/sneat-sidebar-menu.blade.php`).

**Bagaimana sistem tahu peran seseorang?** Ada dua lapis (biar aman saat transisi data):
1. Kolom baru `users.role_id` -> tabel `roles` (punya `level`).
2. Cadangan: kolom lama `users.role` (teks).
Pengecekan selalu lewat helper di model `User` (mis. `$user->isAdmin()`,
`$user->isBendahara()`) - **bukan** membandingkan teks mentah, supaya konsisten.

> Perumpamaan: kartu akses karyawan. Ada chip baru (`role_id`) dan tulisan lama di kartu
> (`role`). Mesin selalu baca lewat "pembaca kartu" resmi (`isAdmin()` dll), bukan mengeja
> tulisannya sendiri.

---

## Bab 4. Model & Relasi Data (Eloquent) - lemari arsip yang saling terhubung

**Model** = perwakilan satu tabel dalam bentuk objek PHP. Contoh: model `Siswa` mewakili
tabel `siswa`. Daripada menulis SQL manual, kita cukup:
```php
$siswa = Siswa::find(5);          // ambil siswa id 5
$siswa->nama_lengkap;             // baca kolomnya
$nilaiDia = $siswa->nilai;        // ambil semua nilai milik siswa ini (relasi)
```

**Relasi** = tali penghubung antar-arsip. Contoh nyata di sistem ini:
- Satu `Siswa` **punya banyak** `Nilai`, `Presensi`, `Tagihan`, `Rapor`.
- Satu `Nilai` **milik satu** `Siswa`, `MataPelajaran`, `Kelas`, dan `Guru`.
- Satu `Kelas` **punya banyak** `Siswa`; `Kelas` **milik satu** `Cabang` & `TahunAjaran`.
- `OrangTua` <-> `Siswa` dihubungkan tabel jembatan `student_parents` (satu wali bisa
  punya banyak anak, satu anak bisa punya banyak wali).

**Kunci Asing (Foreign Key)** = nomor referensi silang. Baris `nilai` menyimpan
`siswa_id` = "ini nilai milik siswa nomor berapa". Inilah yang membuat data "nyambung".

**Kenapa ini penting untuk sidang (dan kenapa kita hati-hati saat menghapus):**
Karena arsip saling terhubung, menghapus satu arsip bisa **ikut menyeret** arsip lain
(disebut *cascade*). Menghapus satu `Siswa` bisa ikut menghapus semua `Nilai`, `Rapor`,
`Tagihan`-nya. Karena itu di sistem ini penghapusan data penting **dijaga** (kalau masih
ada jejak, dilarang - disarankan "nonaktifkan" saja). Lihat detail per-menu di bab Admin.

> Perumpamaan: map arsip yang distaples ke banyak dokumen lain. Kalau map induk dibuang,
> semua dokumen yang terstaples ikut terbuang. Maka kita pasang aturan: "map yang masih
> ada isinya tidak boleh dibuang, cukup diberi cap Nonaktif".

---

## Bab 5. Tampilan (Blade) & Aset (Vite)

- **Blade** = mesin cetak halaman. File `.blade.php` berisi HTML + sisipan data
  (`{{ $siswa->nama_lengkap }}`) + logika tampilan sederhana (`@foreach`, `@if`).
- **Layout** = kerangka halaman yang dipakai ulang. Halaman dashboard semua peran memakai
  `layouts/sneat`; halaman LMS memakai `layouts/lms`.
- **Vite** = alat yang membundel CSS/JS. Tiap halaman punya file CSS/JS sendiri yang
  didaftarkan di `vite.config.js` lalu dipanggil di Blade dengan `@vite([...])`.

> Perumpamaan: Blade = template surat berkop yang tinggal diisi nama; Layout = kop &
> bingkai standar; Vite = mesin yang merapikan lampiran (gaya & script) sebelum dikirim.

---

## Bab 6. Konsep Sentral: Tahun Ajaran

Hampir semua data (kelas, nilai, jadwal, tagihan) **menempel pada satu Tahun Ajaran (TA)**.
Sistem menjaga aturan penting: **hanya boleh ada SATU TA yang aktif**. Saat admin
mengaktifkan TA baru, sistem otomatis menonaktifkan TA lama (dalam satu transaksi, supaya
tidak pernah ada dua TA aktif sekaligus).

Kenapa penting: ketika bendahara membuat tagihan, atau guru menilai, sistem otomatis
memakai **TA yang sedang aktif** sebagai konteks. Kalau ganti tahun, data tahun lama tetap
tersimpan (untuk arsip/rapor), sementara data baru masuk ke TA baru.

> Perumpamaan: TA aktif seperti "periode pembukuan yang sedang berjalan". Semua transaksi
> hari ini masuk ke buku periode ini. Buku periode lama tidak dihapus - hanya ditutup.

---

## Bab 7 - Peran ADMIN (akses SEMUA menu)

Admin adalah "kunci master" (Bab 2 & 3). Semua controller ada di folder `Admin/`.
Sidebar Admin terbagi jadi beberapa kelompok menu. Tiap fitur dijelaskan dalam 3 bagian:
**cara kerja kode**, **Penjelasan Umum**, **Penjelasan Sederhana**.

### 7.1 Kelompok "Manajemen Pengguna"
Menu: Tenaga Pendidik, Siswa, Wali Siswa, Tiket Recovery, Setting LMS, Setting AI.
Controller utama: `Admin/UserController.php`.

**Fitur inti: Tambah / Edit / Hapus akun (siswa & guru).**
- *Kode yang meng-handle:* method `storeSiswa()`, `updateSiswa()`, `deleteSiswa()` (dan
  padanan `...TenagaPendidik`). Variabel `$validated` menampung data yang **sudah lolos
  validasi** (`$request->validate([...])`); baru setelah itu disimpan lewat model
  (`Siswa::create($validated)`).
- *Logika penting saat menambah siswa:* satu aksi membuat **dua** baris - satu di tabel
  `users` (untuk login) dan satu di tabel `siswa` (data akademik), dihubungkan `user_id`.
- *Logika penting saat menghapus:* ada **guard** di `deleteSiswa()`/`deleteTenagaPendidik()`.
  Variabel `$blockers` mengumpulkan jejak data (nilai, tagihan, rapor, dst). Kalau tidak
  kosong -> penghapusan **ditolak** dengan pesan "nonaktifkan saja". Ini mencegah *cascade*
  (Bab 4) menghapus riwayat akademik/keuangan.

> **Penjelasan Umum:** Operasi CRUD user memakai pola Laravel standar: validasi -> simpan
> via Eloquent. Penghapusan diberi *referential-integrity guard* karena FK antar-tabel
> ber-`onDelete('cascade')`; menghapus entitas hub tanpa guard akan menghapus data anak
> secara berantai.
>
> **Penjelasan Sederhana:** Menambah siswa = membuatkan kartu login + map arsip sekaligus.
> Menghapus siswa yang sudah punya nilai/tagihan dilarang, karena kalau dipaksa, semua
> nilai dan tagihannya ikut terhapus. Solusinya cukup diberi cap "Nonaktif".

### 7.2 Kelompok "Data Master": Tahun Ajaran & Cabang
Controller: `Admin/TahunAjaranController.php`, `Admin/CabangController.php`.

**Fitur inti: Aktifkan Tahun Ajaran.**
- *Kode:* method `activate()`. Di dalamnya dibungkus `DB::beginTransaction()`. Pertama
  **semua** TA di-nonaktifkan (`TahunAjaran::where('is_active', true)->update(['is_active'=>false])`),
  lalu TA yang dipilih di-set aktif. Kalau ada error, `DB::rollBack()` membatalkan semua.
- *Logika penting:* transaksi menjamin **tidak pernah ada dua TA aktif** (semua langkah
  berhasil bersama, atau dibatalkan bersama).

> **Penjelasan Umum:** Mengaktifkan TA adalah operasi *stateful* global; dibungkus transaksi
> DB agar atomik (all-or-nothing), menjaga invariant "tepat satu TA aktif" yang jadi konteks
> semua modul lain.
>
> **Penjelasan Sederhana:** Seperti mengganti "periode buku yang sedang berjalan". Sistem
> menutup periode lama dan membuka periode baru dalam satu gerakan; kalau gagal di tengah,
> semuanya dikembalikan seperti semula supaya tidak ada dua periode terbuka.

### 7.3 Kelompok "Data Akademik": Kelas, Guru Pengajar, Jadwal, Mapel, Manajemen Siswa
Controller: `KelasController`, `GuruPengajarController`, `JadwalPelajaranController`, dst.

**Fitur inti: Jadwal Pelajaran (+ tombol Import Excel).**
- *Kode:* `JadwalPelajaranController` menyimpan jadwal; relasi jadwal<->kelas disimpan di
  tabel jembatan `jadwal_kelas`. Saat guru ditentukan, sistem otomatis menyinkronkan tabel
  `guru_pengajar_kelas` (siapa mengajar apa di kelas mana).
- *Tombol Import:* memanggil `JadwalPelajaranImport` (folder `app/Imports/`). Tiap baris
  Excel dicocokkan ke kelas/mapel/guru berdasar **nama** (bukan id), lalu divalidasi hari &
  jam. Baris yang relasinya tidak ketemu diberi peringatan, tidak menabrak data.

> **Penjelasan Umum:** Import memakai paket `maatwebsite/excel`. Kelas Import mengubah baris
> spreadsheet jadi record; relasi diresolusi via lookup nama -> id, dengan pengumpulan
> warning agar impor parsial tetap aman.
>
> **Penjelasan Sederhana:** Import Excel seperti menyalin daftar dari kertas ke sistem
> secara massal. Sistem mencocokkan nama kelas/guru/mapel di Excel dengan data yang sudah
> ada; kalau ada yang salah tulis, baris itu dilewati sambil memberi catatan, jadi data lama
> tidak rusak.

### 7.4 Kelompok "Keuangan": Tagihan, Carryover, Pembayaran, Laporan
Controller: `Admin/Keuangan/*`. Menariknya, controller Admin di sini **membungkus**
controller Bendahara (mewarisi logikanya, hanya mengganti tampilan/redirect). Jadi logika
uang yang sama dipakai admin & bendahara - dijelaskan detail di Bab Bendahara.

**Fitur inti: Carryover Tunggakan.**
- *Kode:* `TunggakanCarryoverService`. Saat ganti tahun, tunggakan siswa dari TA lama
  "dipindahkan" jadi tagihan di TA baru; kolom `tagihan_asal_id` & `dialihkan_ke_id`
  menghubungkan keduanya supaya tidak dihitung dua kali (scope `belumLunasOriginal()`).

> **Penjelasan Umum:** Logika bisnis berat ditaruh di *Service class* (bukan controller)
> agar bisa dipakai ulang & mudah diuji. Carryover memakai kolom relasi diri (self-reference)
> pada tabel `tagihan`.
>
> **Penjelasan Sederhana:** Kalau siswa naik kelas tapi masih punya utang, utangnya "dibawa"
> ke tahun baru sebagai tagihan baru, tapi ditandai berasal dari tagihan lama supaya tidak
> tercatat dobel.

### 7.5 Kelompok "Kenaikan Kelas" & "Validasi & Dispensasi"
Controller: `Admin/Akademik/Promotion*`, `Admin/Keuangan/ValidasiAksesController`.

**Fitur inti: Kelayakan naik kelas.**
- *Kode:* `PromotionService::checkEligibility($siswa, $taId)`. Mengembalikan array
  `['eligible', 'financial'=>['status'], 'academic'=>['is_tuntas']]`. Cek keuangan
  (`checkFinancial`) menghitung tunggakan (`status` belum_bayar/cicilan/terlambat = belum
  lunas); cek akademik (`checkAcademic`) menghitung persen mapel tuntas vs KKM.
- *Logika:* siswa layak kalau **lunas (atau dispensasi) DAN nilai tuntas**.

> **Penjelasan Umum:** Keputusan kenaikan diputuskan Service terpusat berdasarkan dua
> dimensi (keuangan & akademik) plus jalur dispensasi (`izin_naik_kelas_khusus`).
>
> **Penjelasan Sederhana:** Sistem menilai dua hal: "sudah lunas?" dan "nilainya cukup?".
> Kalau dua-duanya ya, siswa boleh naik. Kalau ada halangan keuangan, bisa lewat izin khusus.

### 7.6 Kelompok "Monitoring & Analitik" dan "Manajemen Konten"
- *Monitoring* (`MonitoringController`, `KetuaController` untuk versi ketua): kebanyakan
  halaman **baca-saja** yang merangkum data (jumlah siswa, aktivitas LMS, dst) via query
  agregasi. Tidak mengubah data -> risiko rendah.
- *Manajemen Konten* (berita, pengumuman, flyer, kalender, landing page): CRUD konten situs
  publik. Semua memakai `findOrFail` (aman kalau id tidak ada) + `validate` sebelum simpan.

> **Penjelasan Umum:** Menu monitoring bersifat *read-only reporting*; menu konten adalah CRUD
> untuk CMS landing page & pengumuman.
>
> **Penjelasan Sederhana:** Monitoring = papan laporan untuk melihat kondisi (tidak mengubah
> apa-apa). Manajemen Konten = mengatur isi website sekolah (berita, pengumuman, dll).

### Kemungkinan Pertanyaan Penguji - Peran ADMIN (tertinggi -> terendah)
1. **"Bagaimana sistem membedakan hak akses tiap peran?"** -> middleware `role:` +
   `CheckRole`; admin sebagai kunci master; pengecekan lewat `isAdmin()` (Bab 2-3).
2. **"Apa yang terjadi kalau menghapus siswa/guru yang punya nilai/tagihan?"** -> ditolak
   oleh guard (`$blockers`), disarankan nonaktifkan; jelaskan bahaya cascade (Bab 4, 7.1).
3. **"Kenapa hanya boleh satu Tahun Ajaran aktif? Bagaimana menjaganya?"** -> transaksi DB
   di `activate()` (7.2).
4. **"Bagaimana proses import Excel bekerja & bagaimana kalau datanya salah?"** -> paket
   maatwebsite, lookup relasi via nama, warning per baris, data lama aman (7.3).
5. **"Bagaimana menentukan siswa layak naik kelas?"** -> `PromotionService::checkEligibility`
   (keuangan + akademik) (7.5).
6. **"Kenapa logika keuangan admin sama dengan bendahara?"** -> controller admin mewarisi
   controller bendahara (7.4).
7. **"Apa itu carryover tunggakan?"** -> tunggakan tahun lama dibawa ke tahun baru dgn tanda
   asal (7.4).
8. **"Validasi input ditaruh di mana?"** -> `$request->validate()` di controller (sebagian di
   Form Request); data diproses hanya setelah lolos validasi.

---

## Bab 8 - Peran KETUA PKBM & WAKIL KEPALA SEKOLAH

Dua peran ini "atasan akademik". **Ketua** mengawasi lintas-cabang & menyetujui hal penting
(dispensasi, rapor). **Waka** seperti admin tapi **dibatasi ke satu cabang**. Di bab ini kita
bedah fitur-fitur rumit yang sering ditanya penguji, dengan gaya "button ini kerjanya gimana".

### 8.1 Button "Eksekusi Kenaikan Kelas" - konsep, logika, kode

**Konsep.** Memindahkan siswa ke kelas di **Tahun Ajaran berikutnya** berdasarkan hasil
penilaian kelayakan (naik / tinggal / lulus).

**Kode yang meng-handle.** `PromotionService::executeStudentPromotion($siswa, $taId, $tanggal)`
(untuk satu siswa); `promoteSelectedStudents([...ids])` untuk banyak siswa sekaligus; dan
command terjadwal `ExecuteScheduledPromotion` kalau dijadwalkan otomatis.

**Logika langkah demi langkah (variabel kunci):**
1. `$eligibility = $this->checkEligibility($siswa, $taId)` -> menilai keuangan + akademik.
2. Tentukan `$statusKelulusan`:
   - Kalau layak **dan** `isFinalYear($siswa)` -> `LULUS` (atau `LULUS_TUNGGAKAN` bila lolos
     lewat dispensasi).
   - Kalau layak dan bukan tingkat akhir -> `NAIK_KELAS` (atau `NAIK_KELAS_TUNGGAKAN`).
   - Kalau tidak layak -> `TIDAK_NAIK_KELAS`.
3. Tentukan kelas tujuan:
   - Naik -> `findNextClass()` (cari kelas tingkat berikutnya di TA target) lalu
     `$siswa->kelas_id = $kelasTujuanId; $siswa->save();`
   - Tidak naik -> `findSameClass()` (kelas dengan nama sama di TA baru = tinggal kelas).
   - Kalau kelas tujuan tak ditemukan -> `kelas_id = NULL` -> siswa muncul di daftar "belum
     berkelas" agar admin merapikan manual.
4. Hasil dicatat di tabel `status_naik_kelas_siswa` (menyimpan kelas asal, kelas tujuan,
   status). Ini yang membuat aksi bisa **di-rollback** lewat `rollbackStudent()`.

**Kemana datanya pergi:** kolom `siswa.kelas_id` berubah ke kelas TA baru; riwayat masuk
`status_naik_kelas_siswa`; notifikasi hasil dikirim ke siswa & orang tua.

> **Penjelasan Umum:** Eksekusi kenaikan adalah orkestrasi Service yang menggabungkan
> evaluasi kelayakan, penentuan kelas tujuan (naik/tinggal/lulus), mutasi `kelas_id`, dan
> pencatatan status yang reversibel.
>
> **Penjelasan Sederhana:** Tombol ini seperti "naik kelas serentak". Sistem mengecek tiap
> siswa: layak naik, tinggal, atau lulus; lalu memindahkannya ke kelas tahun depan yang
> sesuai. Semua dicatat, jadi kalau ada salah, bisa dibatalkan.

### 8.2 Fitur "Dispensasi" - dikirim ke mana & cara kerjanya

**Konsep.** Ada siswa yang **nilainya cukup tapi masih menunggak**. Secara aturan dia belum
boleh naik/lulus karena keuangan. **Bendahara** mengajukan keringanan ("dispensasi") ke
**Ketua PKBM**; kalau disetujui, siswa boleh naik/lulus walau ada tunggakan.

**Alur & kode (kirim dari siapa ke siapa):**
1. **Bendahara mengajukan.** `Bendahara/PromotionValidationController::store()` menjalankan
   `DB::table('izin_naik_kelas_khusus')->insert([... 'status' => 'MENUNGGU',
   'diajukan_oleh' => auth()->id(), 'siswa_id', 'tahun_ajaran_id', 'alasan_pengajuan'])`.
   Pesan sukses: "...diajukan ke Ketua PKBM". -> Jadi datanya "dikirim" dengan membuat baris
   berstatus **MENUNGGU** di tabel `izin_naik_kelas_khusus`.
2. **Ketua memutuskan.** `Ketua/PromotionApprovalController::update()` meng-update baris itu:
   `status` -> `DISETUJUI`/`DITOLAK`, `disetujui_oleh`, `catatan_ketua`, lalu memicu
   notifikasi keputusan.
3. **Saat eksekusi kenaikan**, `checkFinancial()` mengecek adakah dispensasi `DISETUJUI` untuk
   siswa itu -> set `is_dispensasi = true` -> siswa diperlakukan "boleh naik meski nunggak"
   (statusnya jadi `NAIK_KELAS_TUNGGAKAN` / `LULUS_TUNGGAKAN`).

> **Penjelasan Umum:** Dispensasi adalah workflow persetujuan berbasis status pada tabel
> `izin_naik_kelas_khusus` (MENUNGGU -> DISETUJUI/DITOLAK), dengan pengaju (bendahara) dan
> penyetuju (ketua) berbeda, lalu dikonsumsi oleh logika kelayakan keuangan.
>
> **Penjelasan Sederhana:** Seperti surat izin keringanan. Kasir (bendahara) mengirim surat ke
> kepala (ketua). Begitu kepala tanda tangan "setuju", sistem menganggap siswa itu "seolah
> lunas" khusus untuk urusan naik kelas.

### 8.3 Jadwal "Multi-Class Multi-Jenjang" - kok bisa satu sesi banyak kelas?

**Konsep.** Satu sesi (hari + jam + guru yang sama) dipasang ke **banyak kelas sekaligus**,
bahkan kelas dari **jenjang berbeda** (mis. SD & SMP) dalam satu aksi.

**Kenapa bisa - kode:** `JadwalPelajaranTrait::storeMultiJenjang()`.
- Input penting: `kelas_ids` (banyak kelas) dan `mapel_per_jenjang` (**mapel berbeda per
  jenjang** - karena "Matematika SD" dan "Matematika SMP" adalah **dua record mapel berbeda**).
- Kelas dikelompokkan: `$kelasGrouped = $kelasList->groupBy('jenjang')`.
- Tiap grup jenjang divalidasi mapelnya cocok + dicek bentrok jadwalnya (`checkConflicts`).
- Dalam satu `DB::transaction`: untuk **tiap jenjang** dibuat **satu** `JadwalPelajaran`, lalu
  `$jadwal->kelas()->attach($groupKelasIds)` menautkan semua kelas grup itu (via tabel
  jembatan `jadwal_kelas`), dan `syncGuruPengajar()` menautkan guru->kelas->mapel.

**Kemana datanya:** satu baris jadwal per jenjang + banyak baris di `jadwal_kelas` (satu per
kelas) + baris di `guru_pengajar_kelas`.

> **Penjelasan Umum:** Relasi jadwal<->kelas bersifat many-to-many (pivot `jadwal_kelas`),
> sehingga satu jadwal menaungi banyak kelas. Karena mapel di-scope per jenjang, input
> dikelompokkan `groupBy('jenjang')` dan diproses per grup dalam satu transaksi.
>
> **Penjelasan Sederhana:** Bayangkan satu jam pelajaran yang "disiarkan" ke beberapa kelas.
> Karena tiap jenjang punya mapelnya sendiri, sistem memisah per jenjang, tapi kamu cukup
> sekali klik untuk semuanya.

### 8.4 Button "Ganti Guru" pada Jadwal - kenapa efeknya bisa massal

**Konsep.** Mengganti guru pengampu sebuah jadwal (mis. guru lama cuti, diganti guru lain).

**Kode:** `gantiGuru($request, $jadwalPelajaran)`.
- Validasi `guru_id_baru` + `alasan`.
- Cek bentrok jadwal untuk guru baru (`checkConflicts`) - supaya guru tidak dobel jam.
- Update `$jadwalPelajaran->guru_id`, catat perubahan ke tabel riwayat
  `jadwal_pelajaran_history` (guru lama -> guru baru + alasan + siapa yang mengubah).
- Sinkronkan `guru_pengajar_kelas` (hapus penugasan guru lama untuk kombinasi itu, tambah
  guru baru).

**Kenapa "massal":** karena satu jadwal bisa menaungi **banyak kelas** (Bab 8.3), mengganti
guru pada satu jadwal otomatis berlaku untuk **semua kelas** yang ditautkan jadwal itu.
(Waka: aksi ini dijaga agar hanya untuk jadwal cabangnya - lihat Bab keamanan.)

> **Penjelasan Umum:** Perubahan guru bersifat idempotent terhadap pivot: `guru_id` jadwal
> di-set, histori dicatat untuk audit, dan `guru_pengajar_kelas` di-resinkron; efeknya
> menyebar ke semua kelas pivot jadwal tsb.
>
> **Penjelasan Sederhana:** Kamu ganti nama guru di satu slot jadwal. Kalau slot itu dipakai 3
> kelas, ketiganya langsung ikut berganti guru. Perubahan juga dicatat (kapan, oleh siapa,
> alasannya) supaya ada jejak.

### 8.5 Ringkas menu lain Ketua & Waka
- **Ketua - Validasi Rapor** (`ValidasiRaporController`): Ketua menyetujui rapor yang sudah
  dikirim wali kelas; membatalkan validasi Ketua otomatis mereset validasi bendahara
  (cascade), supaya status tetap konsisten.
- **Waka - semua data akademik cabangnya**: identik menu Admin, tetapi **setiap query
  disaring `where('cabang_id', $userCabangId)`** dan setiap akses ke satu data diperiksa
  helper `ensure...InUserCabang()` -> `abort(403)` kalau data milik cabang lain.

> **Penjelasan Sederhana (scoping Waka):** Waka itu "kepala cabang". Dia hanya boleh melihat &
> mengubah data cabangnya sendiri; kalau mencoba membuka data cabang lain, sistem menolak.

### Kemungkinan Pertanyaan Penguji - KETUA & WAKA (tertinggi -> terendah)
1. **"Jelaskan proses kenaikan kelas dari awal sampai siswa pindah kelas."** -> 8.1
   (checkEligibility -> status -> findNextClass -> update kelas_id -> catat status).
2. **"Apa itu dispensasi, siapa yang mengajukan dan siapa yang menyetujui?"** -> 8.2
   (Bendahara ajukan -> Ketua setujui -> dipakai checkFinancial).
3. **"Bagaimana satu jadwal bisa untuk banyak kelas / lintas jenjang?"** -> 8.3
   (relasi many-to-many `jadwal_kelas` + groupBy jenjang).
4. **"Bagaimana ganti guru bekerja, dan kenapa bisa kena banyak kelas?"** -> 8.4.
5. **"Bagaimana membedakan wewenang Waka dengan Admin?"** -> Waka dibatasi cabang via
   `where('cabang_id')` + `ensure...InUserCabang()` (8.5).
6. **"Kalau kenaikan salah, bisa dibatalkan?"** -> ya, `rollbackStudent()` memakai catatan
   `status_naik_kelas_siswa` (8.1).
7. **"Kenapa perubahan guru perlu dicatat?"** -> audit di `jadwal_pelajaran_history` (8.4).

---

## Bab 9 - Peran SEKRETARIS & BENDAHARA

**Sekretaris** mengurus konten & administrasi (kalender, pengumuman, berita, flyer).
**Bendahara** memegang **jalur uang**: tagihan, pembayaran (tunai/transfer/online), validasi.
Ini modul paling sensitif, jadi banyak pengaman (transaksi, audit log, verifikasi).

### 9.1 Button "Generate Tagihan / SPP Massal" - dan kenapa tidak pernah dobel

**Konsep.** Membuat tagihan (mis. SPP bulanan) untuk banyak siswa sekaligus.

**Kode:** `Bendahara/TagihanController::generateSpp()` / `bulkCreate()`.
**Logika anti-dobel (variabel kunci `$key = jenis_tagihan`):** sebelum membuat, sistem cek
`Tagihan::where('siswa_id', ...)->where('tahun_ajaran_id', ...)->where('jenis_tagihan', $key)
->exists()`. Kalau tagihan jenis itu sudah ada -> **dilewati (skip)**, tidak dibuat ulang.
Tiap jenis SPP dibedakan dengan sufiks bulan (mis. `spp_juli`, `spp_agustus`).

> **Penjelasan Umum:** Pembuatan tagihan bersifat idempoten per (siswa, TA, jenis_tagihan):
> pengecekan `exists()` mencegah duplikasi saat tombol ditekan berkali-kali.
>
> **Penjelasan Sederhana:** Kalau kamu klik "buat SPP Juli" dua kali, siswa tidak dapat dua
> tagihan Juli. Sistem cek dulu "sudah punya belum?"; kalau sudah, dilewati.

### 9.2 Pembayaran MANUAL (tunai / transfer) + Button "Validasi"

**Konsep.** Wali bayar tunai ke bendahara, atau transfer & upload bukti. Status awal
**pending**; bendahara menekan **Setujui/Tolak**.

**Kode saat disetujui:** `Bendahara/PembayaranController::validasi()`. Dibungkus
`DB::transaction`. Logika inti:
1. Update baris pembayaran -> `status_validasi = disetujui`, `divalidasi_oleh`, tanggal.
2. Hitung ulang total pembayaran **disetujui** untuk tagihan itu:
   `$totalBayar = Pembayaran::where('tagihan_id', ...)->where('status_validasi','disetujui')->sum('jumlah_bayar')`.
3. Kalau `$totalBayar >= tagihan->jumlah` -> tagihan `sudah_bayar`, **dan** semua pembayaran
   pending lain untuk tagihan itu otomatis **dibatalkan** (cegah bayar dobel), dicatat di
   `FinancialAuditLog`.
4. Kalau baru sebagian -> status `cicilan`.

> **Penjelasan Umum:** Validasi pembayaran rekonsiliasi berbasis penjumlahan pembayaran
> tervalidasi; status tagihan diturunkan dari total (lunas/cicilan) dengan pencegahan
> double-payment dan jejak audit.
>
> **Penjelasan Sederhana:** Saat bendahara klik "Setuju", sistem menjumlah semua pembayaran
> yang sah. Kalau sudah menutupi tagihan -> ditandai lunas dan pembayaran lain yang masih
> menggantung dibatalkan supaya tidak bayar dobel. Kalau baru sebagian -> ditandai cicilan.

### 9.3 Pembayaran ONLINE (Midtrans) - cara kerja "bayar lewat aplikasi"

Ini yang sering ditanya: **"uangnya lewat mana, sistem tahu dari mana kalau sudah bayar?"**

**Konsep.** Sekolah tidak memegang uang langsung. **Midtrans** (payment gateway) yang
memproses kartu/e-wallet/Virtual Account, lalu **memberi tahu** sistem hasilnya.

**Alur & kode (langkah demi langkah):**
1. Wali klik "Bayar Online" -> `OrangTua/OrangTuaController::snapPayment()` memanggil
   `MidtransService::createSnapToken($params)`. `$params` berisi `order_id`, jumlah, dan
   rincian item. Midtrans mengembalikan **`snap_token`**.
2. Halaman menampilkan popup pembayaran Midtrans (Snap) memakai token itu; wali membayar di
   popup tersebut (di server Midtrans, bukan di server sekolah).
3. Setelah pembayaran, **Midtrans mengirim notifikasi server-ke-server** ke
   `POST /midtrans/notification` -> `MidtransWebhookController::notification()`.
   URL ini **dikecualikan dari CSRF** (di `bootstrap/app.php`) karena pengirimnya server
   Midtrans, bukan browser pengguna.
4. **Verifikasi keaslian:** `MidtransService::verifySignature(...)` mengecek `signature_key`.
   Kalau tidak cocok -> ditolak (mencegah orang memalsukan "sudah bayar").
5. `transaction_status` (settlement/pending/deny/expire) dipetakan `mapTransactionStatus()`
   ke `status_validasi` sistem, lalu baris `pembayaran` di-update **otomatis** + dicatat di
   `FinancialAuditLog`.

**Kemana datanya:** uang masuk ke rekening sekolah via Midtrans; status "lunas" di database
di-update **oleh webhook**, bukan diketik manual bendahara.

> **Penjelasan Umum:** Integrasi Snap berbasis token + webhook asinkron. Keamanan bertumpu
> pada verifikasi signature server-side; status transaksi Midtrans dipetakan ke domain status
> pembayaran aplikasi secara idempoten.
>
> **Penjelasan Sederhana:** Midtrans itu seperti loket bank pihak ketiga. Wali bayar di loket;
> selesai bayar, loket "menelepon balik" sekolah: "si A sudah lunas". Sebelum percaya, sekolah
> mengecek dulu ini benar-benar dari loket resmi (cek tanda tangan/signature), baru mencatat
> lunas. Jadi bendahara tidak perlu memvalidasi manual untuk pembayaran online.

### 9.4 Proteksi hapus tagihan
`TagihanController::destroyItem()` menolak menghapus tagihan yang **sudah ada pembayaran
disetujui** ("...tidak dapat dihapus untuk menjaga integritas data transaksi"). Mencegah
hilangnya jejak keuangan.

### 9.5 Sekretaris (ringkas)
CRUD konten: kalender akademik, pengumuman, berita, flyer. Semua memakai `findOrFail`
(aman bila id tak ada) + `$request->validate()` sebelum simpan. Membuat berita/pengumuman
juga memicu notifikasi ke pihak terkait.

### Kemungkinan Pertanyaan Penguji - SEKRETARIS & BENDAHARA (tertinggi -> terendah)
1. **"Bagaimana pembayaran online (Midtrans) bekerja end-to-end?"** -> 9.3 (snap token ->
   bayar di Midtrans -> webhook -> verifikasi signature -> update status otomatis).
2. **"Bagaimana sistem tahu pembayaran online sudah lunas tanpa bendahara mengecek?"** ->
   webhook + `mapTransactionStatus` (9.3).
3. **"Bagaimana mencegah bayar dobel / kelebihan bayar?"** -> saat validasi, pending lain
   dibatalkan + hitung total (9.2).
4. **"Bagaimana status tagihan jadi lunas/cicilan?"** -> dihitung dari total pembayaran
   disetujui (9.2).
5. **"Bagaimana mencegah tagihan dobel saat generate massal?"** -> cek `exists()` per
   jenis_tagihan (9.1).
6. **"Kenapa URL webhook dikecualikan dari CSRF?"** -> pengirimnya server Midtrans, bukan
   form browser (9.3).
7. **"Kenapa tagihan yang sudah dibayar tidak boleh dihapus?"** -> integritas jejak keuangan
   (9.4).
8. **"Apa itu FinancialAuditLog?"** -> catatan audit tiap perubahan keuangan penting.

---

## Bab 10 - Peran WALI KELAS & GURU PENGAJAR

**Guru** mengajar mapel: bikin materi, tugas, ujian, menilai. **Wali kelas** mengurus satu
kelas: presensi, rapor, template capaian, validasi. Keduanya berputar di seputar **nilai**,
**rapor**, dan **LMS (ujian online)**.

### 10.1 Input Nilai - bagaimana "nilai akhir" terbentuk

**Konsep.** Guru mengisi komponen nilai (tugas, latihan, ulangan harian/UH, PTS, PAS). Sistem
menghitung rata-rata tiap komponen lalu menggabungkannya jadi **nilai akhir**.

**Kode:** model `Nilai` (`app/Models/Nilai.php`).
- `hitungSemuaRata()` mengisi `rata_tugas`, `rata_latihan`, `rata_uh` = rata-rata dari
  `tugas_1..5`, `latihan_1..5`, `uh_1..5` (mengabaikan yang kosong).
- `hitungNilaiAkhir()` menggabungkan rata-rata komponen + `pts` + `pas` dengan **bobot
  tertentu** menjadi `nilai_akhir`.
- **Aturan desimal:** input menerima koma maupun titik ("9,5" atau "9.5"); sistem
  menormalkan ke titik sebelum disimpan. Nilai dibatasi 0-100.

**Kemana datanya:** satu baris `nilai` per (siswa, mapel, kelas, TA, semester, guru).
Nilai inilah yang nanti "ditarik" ke rapor.

> **Penjelasan Umum:** Perhitungan nilai dienkapsulasi di model (fat model): komponen ->
> rata-rata -> nilai akhir berbobot, dengan normalisasi desimal & clamping rentang.
>
> **Penjelasan Sederhana:** Guru cukup isi angka-angka; sistem yang menghitung rata-rata dan
> nilai akhirnya, jadi tidak perlu hitung manual. Boleh pakai koma atau titik, hasilnya sama.

### 10.2 Mesin UJIAN ONLINE (LMS) - mulai, autosave, submit, koreksi otomatis

Ini fitur paling "wow" untuk sidang. **Konsep:** siswa mengerjakan ujian di browser dengan
timer; jawaban tersimpan otomatis; saat submit, soal objektif dinilai otomatis.

**Alur & kode (`Siswa/LmsUjianController` + model `UjianSiswa`):**
1. **Mulai** (`mulai()`): dibuat baris `UjianSiswa` berstatus `sedang_mengerjakan` + waktu
   mulai dicatat. Kalau siswa sudah pernah mulai, statusnya dipertahankan (tidak reset).
2. **Autosave** (`autosave()`): setiap kali siswa menjawab, jawaban dikirim diam-diam &
   disimpan. Jadi kalau browser tertutup / internet putus, jawaban tidak hilang.
3. **Timer habis** (`isTimeUp()`): kalau waktu ujian lewat, sistem otomatis menganggap ujian
   selesai (auto-submit) supaya tidak bisa curang menambah waktu.
4. **Submit** (`submit()`): sistem mengoreksi:
   - Soal objektif (pilihan ganda, benar-salah, isian) dinilai **otomatis** dengan
     membandingkan jawaban ke kunci.
   - Soal uraian **menunggu koreksi guru** (nilai sementara ditampilkan dulu).
   - Status `UjianSiswa` di-update jadi selesai + nilai dicatat.

**Kemana datanya:** jawaban per soal disimpan (`jawaban_siswa`), status & nilai di
`ujian_siswa`. Guru mengoreksi soal uraian lewat `Guru/GuruKoreksiController`.

> **Penjelasan Umum:** Ujian adalah state machine (belum_mulai -> sedang_mengerjakan ->
> selesai) dengan autosave inkremental, penegakan batas waktu server-side, dan auto-grading
> objektif; soal esai menunggu penilaian manual.
>
> **Penjelasan Sederhana:** Seperti ujian di kertas tapi otomatis: waktu dijaga sistem,
> jawaban tersimpan sendiri (aman kalau mati lampu), dan begitu dikumpulkan, pilihan ganda
> langsung dinilai komputer; soal esai baru dinilai guru.

### 10.3 RAPOR - menarik nilai jadi laporan + rantai validasi

**Konsep.** Rapor adalah "kompilasi" nilai semua mapel seorang siswa di satu semester, plus
kehadiran & catatan, yang harus **divalidasi berjenjang** sebelum bisa dibuka/dicetak.

**Kode & alur:**
- Wali kelas menyusun rapor (`WaliKelas/RaporController`): mengambil `nilai` tiap mapel ->
  disimpan ke `rapor` + `rapor_nilai` (tabel detail per mapel). Ada juga template "capaian
  kompetensi" (kalimat deskripsi) yang dikelola wali - `TemplateCapaianController`, di-scope
  `created_by = auth()->id()` (wali hanya kelola template miliknya).
- **Rantai validasi rapor:** Wali kirim -> Bendahara validasi (pastikan keuangan) -> Ketua
  validasi. Kolom `validasi_rapor_wali/bendahara/ketua` di tabel `siswa` menandai tiap tahap.
  Membatalkan validasi di tingkat atas otomatis mereset tingkat di bawahnya (konsistensi).
- Siswa/ortu baru bisa **download rapor** kalau `hasFullRaporAccess()` true (semua tahap
  validasi lengkap).

> **Penjelasan Umum:** Rapor mengagregasi `nilai` ke `rapor_nilai`; aksesnya dijaga workflow
> multi-approval (wali -> bendahara -> ketua) yang direpresentasikan flag boolean di `siswa`.
>
> **Penjelasan Sederhana:** Rapor itu rangkuman semua nilai. Sebelum boleh dibuka, harus
> "ditandatangani" berurutan: wali kelas, bendahara (cek keuangan), lalu ketua. Kalau salah
> satu membatalkan, tanda tangan di bawahnya ikut batal.

### 10.4 Keamanan konten Guru (kenapa guru tak bisa utak-atik kelas lain)
Setiap aksi (hapus materi/tugas/ujian, input nilai) diawali:
`$tp = TenagaPendidik::where('user_id', auth()->id())->firstOrFail()` lalu
`verifyAccess($tp->id, $kelasId, $mapelId)` (memastikan guru itu memang mengajar kelas+mapel
tsb) -> kalau tidak, `abort(403)`. Query pun di-scope `where('guru_id', $tp->id)`.

> **Penjelasan Sederhana:** Guru hanya bisa mengubah materi/nilai di kelas & mapel yang dia
> ampu. Kalau coba menyentuh kelas guru lain, sistem menolak.

### Kemungkinan Pertanyaan Penguji - WALI KELAS & GURU (tertinggi -> terendah)
1. **"Bagaimana ujian online bekerja? Bagaimana kalau internet siswa putus?"** -> 10.2
   (state machine + autosave + timer server-side + auto-grade).
2. **"Bagaimana nilai akhir dihitung?"** -> 10.1 (rata komponen -> nilai_akhir berbobot,
   di model `Nilai`).
3. **"Bagaimana soal esai dinilai vs pilihan ganda?"** -> objektif auto-grade, esai manual
   oleh guru (10.2).
4. **"Bagaimana rapor dibuat dan kenapa harus divalidasi bertingkat?"** -> 10.3.
5. **"Bagaimana mencegah guru mengubah data kelas lain?"** -> `verifyAccess` + scope guru_id
   (10.4).
6. **"Kenapa input nilai boleh koma dan titik?"** -> normalisasi desimal (10.1).
7. **"Bagaimana timer ujian mencegah kecurangan?"** -> `isTimeUp()` dievaluasi di server,
   auto-submit (10.2).

---

## Bab 11 - Peran SISWA & ORANG TUA (Wali Siswa)

Dua peran "pengguna akhir". **Siswa** belajar & ujian di LMS, melihat nilai/rapor/tagihan.
**Orang Tua** memantau anak & membayar. Ciri khas kedua modul: **semua akses dikunci ke
milik sendiri** (tidak bisa mengintip data siswa/keluarga lain).

### 11.1 Siswa - bagaimana sistem tahu "ini datamu"

**Konsep.** Siswa login, lalu tiap halaman (rapor, tagihan, ujian) otomatis menampilkan
**data miliknya sendiri**.

**Kode (pola kunci):** di awal tiap method,
`$siswa = Siswa::where('user_id', auth()->id())->first();` -> mengambil profil siswa dari
akun yang login. Lalu tiap query resource **disaring ke siswa itu**, mis. rapor:
`Rapor::where('id', $raporId)->where('siswa_id', $siswa->id)->firstOrFail()`.

**Kenapa penting:** meski URL memuat id (mis. `/siswa/sia/rapor/5/download`), tambahan
`where('siswa_id', $siswa->id)` membuat siswa **tidak bisa** membuka rapor id milik orang
lain - hasilnya "tidak ditemukan".

> **Penjelasan Umum:** Otorisasi horizontal (anti-IDOR) ditegakkan dengan menurunkan pemilik
> dari `auth()->id()` lalu meng-constrain setiap query dengan `siswa_id`, bukan mempercayai id
> di URL.
>
> **Penjelasan Sederhana:** Walau seseorang mengetik nomor rapor orang lain di alamat, sistem
> tetap hanya mau menunjukkan rapor miliknya sendiri. Nomor di URL saja tidak cukup.

Menu Siswa: **LMS** (kerjakan tugas/ujian, baca materi, forum - lihat mesin ujian Bab 10.2)
dan **SIA** (rapor, presensi, tagihan/bukti bayar). Semua ter-scope seperti di atas.

### 11.2 Orang Tua - "hanya anak saya" via tabel jembatan

**Konsep.** Satu wali bisa punya beberapa anak; satu anak bisa punya beberapa wali. Wali
hanya boleh melihat/membayar **anak yang terhubung** dengannya.

**Kode (pola kunci):** hubungan wali<->anak disimpan di tabel jembatan `student_parents`,
diakses lewat relasi `$user->children()`. Contoh: buka tagihan anak ->
`$siswa = $user->children()->find($siswaId)` (kalau bukan anaknya -> null -> ditolak). Untuk
aksi id-lain (bayar, rapor, invoice, izin) ada penjaga:
`$user->children()->where('siswa.id', $resource->siswa_id)->exists()` -> kalau false,
`abort(403)`.

> **Penjelasan Umum:** Kepemilikan diverifikasi via relasi many-to-many `student_parents`;
> setiap endpoint id-based memeriksa keanggotaan anak sebelum mengakses/memutasi data.
>
> **Penjelasan Sederhana:** Sistem punya "daftar keluarga". Wali hanya bisa mengurus anak yang
> ada di daftarnya. Kalau mencoba membuka data anak keluarga lain, langsung ditolak.

### 11.3 Button "Ajukan Izin" (ketidakhadiran)
Wali mengajukan izin/sakit untuk anaknya (`ajukanIzin`/`storeIzin`) -> membuat/menandai baris
`presensi` sebagai izin/sakit dengan alasan. Ini yang membuat rekap kehadiran anak akurat dan
kelak ikut dipertimbangkan saat penilaian kenaikan kelas.

> **Penjelasan Sederhana:** Kalau anak tidak masuk karena sakit, wali lapor lewat aplikasi;
> guru/wali kelas melihat keterangannya, jadi tidak dihitung "alpha".

### 11.4 Pembayaran (ringkas)
Detail sudah di Bab 9. Untuk wali: pilih tagihan anak -> bayar tunai (dicatat bendahara),
transfer (upload bukti, divalidasi), atau online (Midtrans, status update via webhook).
`paid_by_parent_id` mencatat wali mana yang membayar.

### Kemungkinan Pertanyaan Penguji - SISWA & ORANG TUA (tertinggi -> terendah)
1. **"Bagaimana memastikan siswa tidak bisa melihat nilai/rapor siswa lain?"** -> 11.1
   (scope `where('siswa_id', $siswa->id)`, id URL tidak dipercaya).
2. **"Bagaimana wali hanya bisa mengurus anaknya sendiri?"** -> 11.2 (relasi `children()` via
   `student_parents` + cek kepemilikan tiap aksi).
3. **"Kalau URL diubah-ubah manual, apakah bisa bocor?"** -> tidak; ada guard IDOR di tiap
   endpoint (11.1-11.2).
4. **"Bagaimana pengajuan izin memengaruhi kehadiran?"** -> 11.3.
5. **"Bagaimana satu anak bisa punya dua wali (mis. ayah & ibu)?"** -> tabel jembatan
   many-to-many `student_parents` (11.2).

---

## Bab 12 - Gambaran Besar: Alur Antar-Peran (Data Berpindah Tangan)

Kekuatan sistem ini ada di **kolaborasi antar-peran**. Tiga "perjalanan data" penting yang
enak dipakai menjawab pertanyaan "bagaimana modul saling terhubung":

**A. Perjalanan sebuah NILAI -> RAPOR.**
Guru input nilai (`nilai`) -> Wali kelas mengompilasi jadi rapor (`rapor` + `rapor_nilai`) ->
Bendahara validasi (cek keuangan) -> Ketua validasi -> Siswa/Orang Tua bisa lihat & download.
> Satu angka dari guru menempuh beberapa meja persetujuan sebelum sampai ke wali murid.

**B. Perjalanan sebuah PEMBAYARAN.**
Bendahara/Admin buat tagihan (`tagihan`) -> Wali bayar (tunai/transfer/Midtrans) ->
divalidasi (manual oleh bendahara, atau otomatis via webhook Midtrans) -> status tagihan jadi
`cicilan`/`sudah_bayar` -> memengaruhi kelayakan naik kelas.
> Uang & statusnya mengalir dari wali, lewat gateway/bendahara, sampai memengaruhi akademik.

**C. Perjalanan KENAIKAN KELAS.**
Guru & sistem menghitung kelayakan (nilai + keuangan) -> siswa nunggak bisa lewat jalur
dispensasi (Bendahara ajukan -> Ketua setujui) -> Admin/Ketua eksekusi kenaikan
(`executeStudentPromotion`) -> siswa pindah ke kelas TA baru -> tercatat & bisa di-rollback.
> Keputusan naik/tinggal/lulus adalah hasil gotong-royong beberapa peran, diputuskan sistem.

### Penutup
Kalau menjelaskan sistem ini saat sidang, ingat **pola tunggal** dari Bab 2: setiap fitur =
Route -> Middleware -> Controller -> Model/Database -> View. Yang membedakan hanyalah "logika
bisnis" di Controller/Service (kenaikan, pembayaran, ujian). Pahami satu contoh mendalam
(mis. kenaikan kelas atau pembayaran Midtrans), lalu tunjukkan bahwa fitur lain mengikuti pola
yang sama - itu cara termudah meyakinkan penguji bahwa kamu paham keseluruhan sistem.

*Manual ini melengkapi `docs/QA_REPORT.md` (audit keamanan/bug) - satu menjelaskan CARA KERJA,
satu menjelaskan BUKTI KUALITAS.*

---

## Lampiran A - Peta Lokasi Kode (file : baris)

Rujukan cepat "kode fitur ada di mana". **Baris AWAL akurat** (baris tanda tangan method);
baris akhir adalah perkiraan (batas method). Catatan: nomor baris bisa **bergeser** kalau
kode diedit lagi; kalau meleset, cari nama method-nya (mis. `function executeStudentPromotion`).

### Fondasi (Bab 2-3)
| Fitur | Method | Lokasi (baris) |
|---|---|---|
| Cek hak akses peran (middleware) | `handle` | `app/Http/Middleware/CheckRole.php` : 19-54 |
| Contoh Controller -> View | `index` | `app/Http/Controllers/Admin/DashboardController.php` : 17-101 |

### Bab 7 - Admin
| Fitur | Method | Lokasi (baris) |
|---|---|---|
| Tambah siswa (buat user+siswa) | `storeSiswa` | `app/Http/Controllers/Admin/UserController.php` : 484-602 |
| Hapus siswa (ber-guard) | `deleteSiswa` | `.../Admin/UserController.php` : 814-835 |
| Hapus guru (ber-guard) | `deleteTenagaPendidik` | `.../Admin/UserController.php` : 339-371 |
| Guard jejak siswa | `siswaBlockers` | `.../Admin/UserController.php` : 327-337 |
| Guard jejak guru | `tenagaPendidikBlockers` | `.../Admin/UserController.php` : 310-320 |
| Aktifkan Tahun Ajaran (transaksi) | `activate` | `app/Http/Controllers/Admin/TahunAjaranController.php` : 168-187 |
| Kelayakan naik kelas | `checkEligibility` | `app/Services/PromotionService.php` : 19-44 |
| Cek keuangan | `checkFinancial` | `app/Services/PromotionService.php` : 46-76 |
| Cek akademik (tuntas vs KKM) | `checkAcademic` | `app/Services/PromotionService.php` : 78-120 |
| Carryover tunggakan | (service) | `app/Services/TunggakanCarryoverService.php` |

### Bab 8 - Ketua & Waka
| Fitur | Method | Lokasi (baris) |
|---|---|---|
| Eksekusi kenaikan (1 siswa) | `executeStudentPromotion` | `app/Services/PromotionService.php` : 156-273 |
| Cari kelas naik | `findNextClass` | `app/Services/PromotionService.php` : 307-353 |
| Cari kelas tinggal | `findSameClass` | `app/Services/PromotionService.php` : 355-379 |
| Batalkan kenaikan | `rollbackStudent` | `app/Services/PromotionService.php` : 389-445 |
| Ajukan dispensasi (Bendahara) | `store` | `app/Http/Controllers/Bendahara/PromotionValidationController.php` : 66-98 |
| Setujui dispensasi (Ketua) | `update` | `app/Http/Controllers/Ketua/PromotionApprovalController.php` : 35-59 |
| Jadwal multi-jenjang (buat) | `storeMultiJenjang` | `app/Traits/JadwalPelajaranTrait.php` : 20-121 |
| Jadwal multi-jenjang (edit) | `updateMultiJenjang` | `app/Traits/JadwalPelajaranTrait.php` : 127-242 |
| Ganti guru pada jadwal | `gantiGuru` | `app/Http/Controllers/Admin/JadwalPelajaranController.php` : 430-500 |
| Hapus jadwal (cleanup) | `destroy` | `.../Admin/JadwalPelajaranController.php` : 407-425 |
| Validasi rapor (Ketua) | `validasiRapor` | `app/Http/Controllers/Ketua/ValidasiRaporController.php` : 126-144 |
| Batalkan validasi rapor | `batalkanRapor` | `.../Ketua/ValidasiRaporController.php` : 149-166 |

> Guard IDOR cabang Waka pada jadwal ada di `app/Http/Controllers/WakilKepalaSekolah/JadwalPelajaranController.php`
> (method `edit`, `update`, `gantiGuru`, `destroy`).

### Bab 9 - Sekretaris & Bendahara (jalur uang)
| Fitur | Method | Lokasi (baris) |
|---|---|---|
| Generate SPP (anti-dobel) | `generateSpp` | `app/Http/Controllers/Bendahara/TagihanController.php` : 873-1034 |
| Buat tagihan massal | `bulkCreate` | `.../Bendahara/TagihanController.php` : 528-675 |
| Proteksi hapus tagihan | `destroyItem` | `.../Bendahara/TagihanController.php` : 794-839 |
| Validasi pembayaran (setujui) | `validasi` | `app/Http/Controllers/Bendahara/PembayaranController.php` : 164-294 |
| Pembayaran tunai langsung | `validasiLangsung` | `.../Bendahara/PembayaranController.php` : 494-571 |
| Catat pembayaran | `store` | `.../Bendahara/PembayaranController.php` : 358-489 |
| Buat Snap token (Midtrans) | `createSnapToken` | `app/Services/MidtransService.php` : 61-97 |
| Verifikasi signature webhook | `verifySignature` | `app/Services/MidtransService.php` : 171-177 |
| Petakan status Midtrans | `mapTransactionStatus` | `app/Services/MidtransService.php` : 204-219 |
| Terima notifikasi Midtrans | `notification` | `app/Http/Controllers/MidtransWebhookController.php` : 23-167 |

### Bab 10 - Wali Kelas & Guru
| Fitur | Method | Lokasi (baris) |
|---|---|---|
| Rata-rata komponen nilai | `hitungSemuaRata` | `app/Models/Nilai.php` : 168-174 |
| Hitung nilai akhir | `hitungNilaiAkhir` | `app/Models/Nilai.php` : 178-196 |
| Mulai ujian | `mulai` | `app/Http/Controllers/Siswa/LmsUjianController.php` : 145-224 |
| Autosave jawaban | `autosave` | `.../Siswa/LmsUjianController.php` : 449-508 |
| Submit + auto-grade | `submit` | `.../Siswa/LmsUjianController.php` : 229-309 |
| Template capaian (kelola) | `store`/`update`/`destroy` | `app/Http/Controllers/WaliKelas/TemplateCapaianController.php` : 63-124 |
| Guard konten guru (hapus materi) | `destroy` | `app/Http/Controllers/Guru/GuruMateriController.php` : 352-413 |
| Cek guru mengajar kelas+mapel | `verifyAccess` | `.../Guru/GuruMateriController.php` : 434-439 |

### Bab 11 - Siswa & Orang Tua
| Fitur | Method | Lokasi (baris) |
|---|---|---|
| Download rapor (scoped siswa) | `download` | `app/Http/Controllers/Siswa/SiaRaporController.php` : 125-160 |
| Bayar via Snap (Midtrans) | `snapPayment` | `app/Http/Controllers/OrangTua/OrangTuaController.php` : 1046-1146 |
| Tagihan anak (scoped children) | `tagihanAnak` | `.../OrangTua/OrangTuaController.php` : 161-300 |
| Detail rapor anak (guard) | `detailRapor` | `.../OrangTua/OrangTuaController.php` : 687-711 |
| Proses bayar (guard tagihan) | `prosesBayar` | `.../OrangTua/OrangTuaController.php` : 306-501 |

*(Kalau nanti ada penambahan kode dan nomor bergeser, jalankan pencarian `function <namaMethod>`
di file terkait untuk menemukan posisi terbarunya.)*
