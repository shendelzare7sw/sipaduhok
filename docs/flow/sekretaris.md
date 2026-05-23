# Role: Sekretaris

> Kembali ke [flow.md](../../flow.md) · Role `sekretaris` · Level 3 · Prefix `/sekretaris` · Route `sekretaris.` · Middleware `role:sekretaris`.

## Ringkasan Peran

Role paling ramping. Fokus tunggal: **manajemen konten/informasi publik & akademik non-keputusan** — kalender akademik, pengumuman, flyer/iklan, dan berita. Tidak menyentuh keuangan, nilai, atau validasi. Semua fungsi ada di satu controller `Sekretaris\SekretarisController`.

## Layout & Sidebar

- Layout Sneat. Sidebar: `resources/views/sekretaris/partials/sneat-sidebar-menu.blade.php`.
- Dashboard: `Sekretaris\SekretarisController@dashboard`.

## Peta Menu

| Grup | Menu | Route (index) | Controller@method | Model |
|---|---|---|---|---|
| — | Dashboard | `sekretaris.dashboard` | `SekretarisController@dashboard` | — |
| Konten | Kalender Akademik | `sekretaris.kalender.index` (+ bulanan/cetak/create/store/edit/update/destroy/toggle-visibility) | `SekretarisController@kalender*` | `KalenderAkademik` |
| Konten | Pengumuman | `sekretaris.pengumuman.index` (+ create/store/edit/update/destroy) | `SekretarisController@pengumuman*` | `Pengumuman` |
| Konten | Flyer / Iklan | `sekretaris.flyer.index` (+ create/store/edit/update/destroy) | `SekretarisController@flyer*` | `Flyer` |
| Konten | Kelola Berita | `sekretaris.berita.index` (+ create/store/edit/update/destroy/toggle-featured) | `SekretarisController@berita*` | `Berita` |

View dir: `resources/views/sekretaris/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Tidak ada alur lintas-role kompleks.** Semua menu adalah CRUD konten yang relatif mandiri.
- **Mirror dengan Admin**: keempat menu ini identik dengan grup Admin `admin.akademik.{kalender,pengumuman,flyer,berita}.*` (controller `Admin\Akademik\AkademikController`). Konten yang dibuat Sekretaris dan Admin masuk ke tabel yang sama dan tampil di **landing page publik** (mis. `/berita` → `BeritaController@index`, kalender/pengumuman di portal siswa & LMS). Jadi perubahan konten di sini langsung berdampak ke halaman publik dan dashboard role lain.
- **Kalender Akademik** dikonsumsi lintas role: muncul di LMS siswa (`siswa.lms.kalender`) dan dapat dipakai Wali Kelas sebagai acuan. `toggle-visibility` mengatur tampil/tidaknya item ke publik.

## Detail Sub-Halaman per Menu

> Setiap menu di sidebar Sekretaris yang punya **halaman/aksi selain index** dirinci di sini: tombol di index, route, controller method, view file, dan ringkasan logika. Convention: route name di-prefix `sekretaris.`; view path relatif terhadap `resources/views/`. Semua method ada di satu controller `Sekretaris/SekretarisController.php`.

### Kalender Akademik

**Index view**: `sekretaris/kalender/index.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah Kegiatan | `sekretaris.kalender.create` | GET | `@kalenderCreate` | `sekretaris/kalender/form.blade.php` | Form: nama_kegiatan, tanggal_mulai-selesai, waktu, jenis_kegiatan (libur/upacara/ujian/dll. + custom), lampiran PDF, status (draft/aktif/selesai). |
| Simpan | `sekretaris.kalender.store` | POST | `@kalenderStore` | redirect | Validasi + simpan `KalenderAkademik` di TA aktif; upload lampiran ke `storage/kalender/lampiran/`. |
| Detail Kegiatan | `sekretaris.kalender.show` | GET | `@kalenderShow` | `sekretaris/kalender/show.blade.php` | Detail kegiatan + link unduh lampiran. |
| Edit | `sekretaris.kalender.edit` | GET | `@kalenderEdit` | `sekretaris/kalender/form.blade.php` | Form re-use (mode edit) — sama dengan create. |
| Update | `sekretaris.kalender.update` | PUT | `@kalenderUpdate` | redirect | Validasi & update + replace lampiran lama bila diisi baru. |
| Hapus | `sekretaris.kalender.destroy` | DELETE | `@kalenderDestroy` | redirect | Hapus record + bersihkan file lampiran dari storage. |
| Toggle Visibility | `sekretaris.kalender.toggle-visibility` | POST | `@kalenderToggleVisibility` | redirect | Set status `aktif`↔`draft` → mengontrol tampil/tidaknya di landing publik & dashboard role lain. |
| View Bulanan (FullCalendar feed) | `sekretaris.kalender.bulanan` | GET | `@kalenderBulanan` | JSON | Endpoint AJAX untuk FullCalendar JS — return array event per bulan dengan warna by jenis_kegiatan. |
| Cetak (Bulanan / Tahunan) | `sekretaris.kalender.cetak` | GET | `@kalenderCetak` | `sekretaris/kalender/cetak-bulanan.blade.php` atau `cetak-tahunan.blade.php` | DomPDF stream — pilih mode via query `?jenis=bulanan&bulan=YYYY-MM` (default bulanan) atau `?jenis=tahunan` (list per bulan). |

**Catatan**: Form view `form.blade.php` dipakai untuk **create & edit** sekaligus (mode dideteksi dari adanya `$kalender`). Tidak ada delete dari halaman detail — hanya dari index. Mirror di Admin (`admin.akademik.kalender.*`) — controller admin `extends SekretarisController` & hanya override view path lewat `wrapView`. PDF cetak admin **tetap render view sekretaris** (karena `Pdf::loadView('sekretaris.kalender.cetak-*')` hardcoded di parent).

---

### Pengumuman

**Index view**: `sekretaris/pengumuman/index.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah | `sekretaris.pengumuman.create` | GET | `@pengumumanCreate` | `sekretaris/pengumuman/form.blade.php` | Form: judul, isi (rich-text), target audience, periode tampil, lampiran opsional. Juga load list `KalenderAkademik` untuk reference. |
| Simpan | `sekretaris.pengumuman.store` | POST | `@pengumumanStore` | redirect | Validasi + simpan `Pengumuman` + handle upload file. |
| Edit | `sekretaris.pengumuman.edit` | GET | `@pengumumanEdit` | `sekretaris/pengumuman/form.blade.php` | Form re-use (mode edit) dengan data pre-fill. |
| Update | `sekretaris.pengumuman.update` | PUT | `@pengumumanUpdate` | redirect | Validasi & update + replace file bila diisi baru. |
| Hapus | `sekretaris.pengumuman.destroy` | DELETE | `@pengumumanDestroy` | redirect | Hapus pengumuman + bersihkan file lampiran. |

**Catatan**: Tidak ada halaman show — pengumuman ditampilkan inline di index (card list). Mirror di Admin (`admin.akademik.pengumuman.*`). Konten muncul di landing publik & dashboard semua role.

---

### Flyer / Iklan

**Index view**: `sekretaris/flyer/index.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah Flyer | `sekretaris.flyer.create` | GET | `@flyerCreate` | `sekretaris/flyer/form.blade.php` | Form: judul, gambar/PDF flyer, link CTA, periode aktif, status. |
| Simpan | `sekretaris.flyer.store` | POST | `@flyerStore` | redirect | Validasi + simpan `Flyer` + upload media. |
| Edit | `sekretaris.flyer.edit` | GET | `@flyerEdit` | `sekretaris/flyer/form.blade.php` | Form re-use. |
| Update | `sekretaris.flyer.update` | PUT | `@flyerUpdate` | redirect | Validasi & update + replace media. |
| Hapus | `sekretaris.flyer.destroy` | DELETE | `@flyerDestroy` | redirect | Hapus + bersihkan storage. |

**Catatan**: Tidak ada show. Form view `form.blade.php` di-share untuk create & edit. Mirror di Admin (`admin.akademik.flyer.*`). Flyer tampil di landing page sebagai carousel/banner.

---

### Kelola Berita

**Index view**: `sekretaris/berita/index.blade.php`

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah Berita | `sekretaris.berita.create` | GET | `@beritaCreate` | `sekretaris/berita/form.blade.php` | Form: judul, ringkasan, isi (rich-text), gambar utama, kategori (`$kategoriOptions`), status (`$statusOptions`: draft/publish). |
| Simpan | `sekretaris.berita.store` | POST | `@beritaStore` | redirect | Validasi + simpan `Berita` + upload gambar. Generate slug otomatis. |
| Edit | `sekretaris.berita.edit` | GET | `@beritaEdit` | `sekretaris/berita/form.blade.php` | Form re-use dengan data pre-fill (juga load `$kategoriOptions` & `$statusOptions`). |
| Update | `sekretaris.berita.update` | PUT | `@beritaUpdate` | redirect | Validasi & update + replace gambar bila baru. |
| Hapus | `sekretaris.berita.destroy` | DELETE | `@beritaDestroy` | redirect | Hapus berita + bersihkan storage gambar. |
| Tandai Unggulan | `sekretaris.berita.toggle-featured` | POST | `@beritaToggleFeatured` | redirect | Toggle `is_featured` — berita unggulan tampil di hero/highlight landing publik (`/berita`). |

**Catatan**: Tidak ada show admin (berita ditampilkan publik di `/berita/{slug}` lewat `BeritaController`). Form view `form.blade.php` di-share create & edit. Mirror di Admin (`admin.akademik.berita.*`).
