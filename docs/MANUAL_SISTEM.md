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
- Bab 7+ (menyusul): penjelasan per-peran, per-menu, per-tombol

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

## Bab 9+ - Peran lain (menyusul)

Urutan: Sekretaris & Bendahara (jalur uang/Midtrans) -> Wali Kelas & Guru (nilai, rapor,
LMS) -> Siswa & Orang Tua. Format sama.

*(Dokumen dibangun bertahap.)*
