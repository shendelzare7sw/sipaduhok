# Role: Guru Pengajar

> Kembali ke [flow.md](../../flow.md) · Role `guru_pengajar` · Level 4 · Prefix `/guru` · Route `guru.` · Middleware `role:guru_pengajar`.

## Ringkasan Peran

Mengajar mata pelajaran di satu/beberapa kelas. **Punya dua konteks** (dua sidebar):
1. **Dashboard (Sneat)** — gambaran umum: jadwal mengajar, daftar kelas, arsip, catatan monitoring.
2. **LMS per kelas+mapel** — saat masuk ke satu kelas+mapel, sidebar berganti ke konteks pembelajaran: materi, tugas, latihan, ujian, forum, kelas virtual, nilai.

Guru = `TenagaPendidik`; relasi mengajar diturunkan dari `JadwalPelajaran`/`GuruPengajarKelas`.

## Layout & Sidebar (DUA layout)

| Konteks | File sidebar | Layout | Kapan tampil |
|---|---|---|---|
| Dashboard | `resources/views/guru/partials/sneat-sidebar-menu.blade.php` | Sneat | Halaman `/guru/dashboard`, `/guru/kelas`, dll |
| LMS | `resources/views/guru/partials/sidebar-lms.blade.php` | `layouts/lms-guru` | Setelah masuk `/guru/lms/{kelas}/{mapel}/...` (butuh var `$kelas`,`$mapel`) |
| LMS-notif | `resources/views/guru/partials/sidebar-lms-notif.blade.php` | LMS | Halaman `/notifications?ctx=lms-guru` (tak butuh kelas/mapel) |

Sidebar Dashboard juga menampilkan grup dinamis **"Kelas Saya (Akses Cepat)"** dari `$sidebarKelas` (link langsung ke `guru.lms.dashboard` per kelas+mapel). Badge: "Catatan Monitoring" = `CatatanMonitoring` belum dibaca; (LMS) "Tugas" = `$tugasBelumDikoreksi`.

## Peta Menu — Konteks Dashboard (Sneat)

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| — | Dashboard | `guru.dashboard` | `DashboardController@guru` | `JadwalPelajaran`,`Tugas` |
| — | Jadwal Mengajar | `guru.jadwal.index` | `Guru\GuruJadwalController@index` | `JadwalPelajaran` |
| Pembelajaran | Semua Kelas | `guru.kelas.index` (+ `kelas.mapel`) | `Guru\GuruKelasController@index/showMapel` | `Kelas`,`MataPelajaran` |
| Pembelajaran | Arsip LMS | `guru.lms.arsip.index` (+ preview, form-salin, salin) | `Guru\GuruLmsArsipController` | `Materi`,`Tugas`,`Ujian` (lintas TA) |
| Pembelajaran | Catatan Monitoring | `guru.lms.catatan-monitoring.index` (+ show) | `Guru\GuruCatatanMonitoringController` | `CatatanMonitoring` |
| Kelas Saya (dinamis) | [Nama Kelas] → [Nama Mapel] | `guru.lms.dashboard` (kelas, mapel) | `Guru\GuruLmsController@dashboard` | `Kelas`,`MataPelajaran` |

## Peta Menu — Konteks LMS (`/guru/lms/{kelas}/{mapel}`)

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| Utama | Beranda | `guru.lms.dashboard` | `Guru\GuruLmsController@dashboard` | — |
| Pembelajaran | Materi | `guru.lms.materi.index` (CRUD) | `Guru\GuruMateriController` | `Materi` |
| Pembelajaran | Tugas | `guru.lms.tugas.index` (CRUD) | `Guru\GuruTugasController` | `Tugas`,`TugasSiswa` |
| Pembelajaran | Tugas → Koreksi | `guru.lms.tugas.koreksi` (+ `.show`/`.store`/`.bulk`/`.ai-suggest`) | `Guru\GuruKoreksiController` | `TugasSiswa` |
| Pembelajaran | Latihan | `guru.lms.latihan.index` (CRUD + soal) | `Guru\GuruUjianController` (jenis latihan) | `Ujian`,`SoalUjian` |
| Pembelajaran | Ujian | `guru.lms.ujian.index` (CRUD + soal + hasil/koreksi + AI generate) | `Guru\GuruUjianController` | `Ujian`,`SoalUjian`,`UjianSiswa`,`JawabanSiswa` |
| Pembelajaran | Forum Diskusi | `guru.lms.forum.index` (+ show/reply/pin/close) | `Guru\GuruForumController` | `ForumDiskusi`,`ForumReply` |
| Pembelajaran | Kelas Virtual | `guru.lms.meeting.index` (CRUD) | `Guru\GuruLmsMeetingController` | `LmsMeeting` |
| Penilaian | Nilai Siswa | `guru.lms.nilai.index` (+ update/updateBatch/recalculate/import/export) | `Guru\GuruNilaiController` | `Nilai` |
| Navigasi | Kembali ke Dashboard | `guru.dashboard` | — | — |

View dir: `resources/views/guru/`, LMS di `resources/views/guru/lms/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Alur LMS Guru↔Siswa**: Guru membuat Materi/Tugas/Ujian → Siswa mengakses & mengumpulkan → Guru mengoreksi. Lihat diagram [flow.md §5.5](../../flow.md#55-pembelajaran-lms-guru--siswa).
- **Tugas → Koreksi**: `TugasSiswa` masuk dengan status `dikerjakan`/`terlambat`; `@store` mengisi `nilai`+`feedback_guru` dan set status `dinilai` (memicu notifikasi ke siswa). Ada `bulkGrade` dan saran AI (`ai-suggest`).
- **Ujian & Latihan** memakai controller yang **sama** (`GuruUjianController`) — "Latihan" hanyalah `Ujian` berjenis latihan. Termasuk bank soal (`SoalUjian`), import/export soal Excel, dan **AI generate questions**. Ujian besar (PTS/PAS) terkait gerbang `validasi_ujian_*` (lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa)) — siswa hanya bisa mengerjakan bila tervalidasi.
- **Nilai Siswa (LMS)** — agregasi nilai (tugas/latihan/UH/PTS/PAS → `nilai_akhir`) yang nanti menjadi sumber rapor yang digenerate Wali Kelas.
- **Arsip LMS** — reuse materi/tugas/ujian dari Tahun Ajaran lalu (preview → form-salin → salin ke kelas+mapel TA aktif). Hemat pekerjaan ulang tiap tahun.
- **Catatan Monitoring** — komunikasi *masuk* dari Ketua/Waka/Admin (teguran/instruksi). Contoh lempar tanggung jawab: pimpinan menulis catatan → muncul sebagai badge & daftar di sisi Guru.

## Detail Sub-Halaman per Menu

> Guru punya **dua konteks** (dua sidebar), jadi section detail dibagi 2 sub-section. Convention: route name di-prefix `guru.`; view path relatif terhadap `resources/views/`. Controller path relatif terhadap `app/Http/Controllers/Guru/`. Semua URL LMS punya parameter `{kelas}/{mapel}` — `$kelas` dan `$mapel` di-resolve via Route Model Binding.

---

## A. Konteks Dashboard (Sneat Sidebar)

### Jadwal Mengajar

**Index view**: `guru/jadwal/index.blade.php` · **Controller**: `GuruJadwalController.php`

**Tampilan index**: 4 **stat card**: Total Jadwal, Hari Mengajar, Kelas Diampu, Mata Pelajaran. Konten: **grid kartu per hari** (Senin–Sabtu yang punya jadwal) — tiap kartu berisi list jadwal: jam mulai-selesai, nama mapel, TA, dan chip kelas (multiple kalau guru mengajar mapel sama di beberapa kelas paralel). Empty state bila belum ada jadwal. **Read-only** — guru tidak edit jadwal di sini (jadwal dikelola Admin/Waka).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| (Tidak ada sub-halaman) | — | — | — | — | Halaman read-only. Klik chip kelas TIDAK navigasi (data display only). |

**Catatan**: Untuk masuk ke LMS suatu kelas+mapel, gunakan menu **Semua Kelas** atau grup sidebar dinamis **Kelas Saya**.

---

### Semua Kelas (Daftar Kelas)

**Index view**: `guru/kelas/index.blade.php` · **Controller**: `GuruKelasController.php`

**Tampilan index**: Header card "Kelas yang Anda Ajar". **Grid kartu kelas** (3 kolom desktop) — tiap kartu: nama kelas + jenjang, ikon school, 2 mini-stat (Jumlah Siswa, Jumlah Mapel), daftar badge **Mata Pelajaran** yang diampu di kelas ini, dan tombol footer **Kelola Kelas** (primary, → `kelas.mapel`). Empty state bila tidak ada penugasan dengan alert info kontak Akademik/Admin.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Kelola Kelas (per kartu) | `guru.kelas.mapel` | GET | `@showMapel` | `guru/kelas/mapel.blade.php` | Halaman transisi: daftar mapel yang diampu di kelas tsb. Klik mapel → masuk LMS (`guru.lms.dashboard` dengan param kelas+mapel). |

**Catatan**: Halaman `kelas.mapel` adalah **bridge** ke konteks LMS. Tidak ada modify di sini. Bisa juga skip halaman ini lewat grup sidebar dinamis **Kelas Saya (Akses Cepat)**.

---

### Arsip LMS

**Index view**: `guru/lms/arsip/index.blade.php` · **Controller**: `GuruLmsArsipController.php`

**Tampilan index**: Header dengan deskripsi "Lihat materi/tugas/latihan/ujian lintas TA, salin ke kelas aktif". Alert warning bila guru belum punya assignment di TA aktif (tombol Salin akan dinonaktifkan). **Summary 4 stat**: Materi, Tugas, Latihan, Ujian. **Type tabs** untuk filter cepat: Semua, Materi, Tugas, Latihan, Ujian. Form filter: dropdown **Tahun Ajaran**, **Mata Pelajaran**, search judul + tombol Filter & Reset. List konten arsip dengan tombol per-item: **Preview** (lihat detail), **Salin ke Kelas Aktif** (form pilih kelas+mapel tujuan).

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Preview Konten | `guru.lms.arsip.preview` | GET | `@preview` | `guru/lms/arsip/preview-wrapper.blade.php` (wrap → `preview-materi/tugas/ujian` sesuai type) | Render detail konten arsip read-only (judul, deskripsi, isi/soal). |
| Form Salin (pilih tujuan) | `guru.lms.arsip.form-salin` | GET | `@formSalin` | `guru/lms/arsip/form-salin.blade.php` | Form pilih kelas+mapel tujuan (hanya yang ditugaskan guru di TA aktif). |
| Eksekusi Salin | `guru.lms.arsip.salin` | POST | `@salin` | redirect | Clone konten ke kelas+mapel TA aktif (tanggal/deadline disesuaikan, status default draft). |

**Catatan**: Reuse konten antar TA hemat pekerjaan. Tombol salin disabled bila tidak ada `kelasMapelTujuan` (guru belum di-assign TA aktif).

---

### Catatan Monitoring

**Index view**: `guru/lms/catatan-monitoring/index.blade.php` · **Controller**: `GuruCatatanMonitoringController.php`

**Tampilan index**: **List card catatan masuk** (bukan tabel) — tiap card: nama pengirim (Ketua/Waka/Admin) + badge role, badge "Baru" (bila belum dibaca), badge tipe konten (mis. "Materi", "Tugas", "Ujian") + judul konten + mapel + kelas terkait, preview isi (180 char), waktu kirim, arrow "Lihat detail". Empty state bila inbox kosong. Pagination di bawah.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Detail Catatan | `guru.lms.catatan-monitoring.show` | GET | `@show` | `guru/lms/catatan-monitoring/show.blade.php` | Tampil isi catatan lengkap + konten terkait (deep-link ke konten LMS yang dikomentari). Auto-mark `dibaca_pada = now()` saat dibuka. |

**Catatan**: Catatan ini *masuk* dari `*.monitoring.lms.catatan` (Ketua/Waka/Admin). Badge count di sidebar = jumlah `CatatanMonitoring` belum dibaca. Pure read — Guru tidak bisa reply dari sini (komunikasi 1 arah).

---

## B. Konteks LMS (`/guru/lms/{kelas}/{mapel}`)

> Sidebar berganti ke `sidebar-lms.blade.php` saat masuk halaman ini. Semua URL di bawah punya prefix `/{kelas}/{mapel}` dan route name `guru.lms.{module}.*`.

### Beranda LMS (Dashboard per Kelas+Mapel)

**Index view**: `guru/lms/dashboard.blade.php` · **Controller**: `GuruLmsController.php`

**Tampilan index**: 4 **stat card**: Total Siswa, Materi, Tugas, Perlu Koreksi. Section **Quick Actions** — grid kartu navigasi ke sub-modul LMS: Materi, Tugas, Latihan, Ujian, Forum, Kelas Virtual, Nilai. Tiap kartu punya icon, judul, subtitle, dan arrow → link ke route `lms.{module}.index`. Tidak ada tabel/list utama — halaman ini sepenuhnya navigation hub.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Quick Action: Materi/Tugas/Latihan/Ujian/Forum/Meeting/Nilai | `guru.lms.{materi\|tugas\|latihan\|ujian\|forum\|meeting\|nilai}.index` | GET | (controller masing-masing) | (view masing-masing) | Cross-link ke sub-modul. |

**Catatan**: Halaman ini menjadi homepage konteks LMS. Cek `$tugasBelumDikoreksi` muncul juga sebagai badge sidebar "Tugas".

---

### Materi

**Index view**: `guru/lms/materi/index.blade.php` · **Controller**: `GuruMateriController.php`

**Tampilan index**: Card header dengan judul "Daftar Materi" + filter date picker (filter materi by tanggal upload, auto-submit) + tombol kanan **+ Tambah Materi** (primary). **Timeline-style grouping per tanggal upload** (badge tanggal sebagai separator). Tiap materi ditampilkan sebagai card di grid (3 kolom): ikon sesuai tipe file (PDF/PPT/DOC/Video/Link dengan warna khas), judul + badge tipe + waktu, deskripsi (2 baris), tombol footer **Edit** (warning) & **Hapus** (danger). Pagination di bawah. Empty state bila kosong.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Tambah Materi | `guru.lms.materi.create` | GET | `@create` | `guru/lms/materi/create.blade.php` | Form: judul, deskripsi, tipe file (PDF/PPT/DOC/Video/Link), upload file atau URL. |
| Simpan Materi | `guru.lms.materi.store` | POST | `@store` | redirect | Validasi + upload file ke storage + simpan `Materi`. |
| Edit Materi | `guru.lms.materi.edit` | GET | `@edit` | `guru/lms/materi/edit.blade.php` | Form pre-fill. |
| Update | `guru.lms.materi.update` | PUT | `@update` | redirect | Validasi & update + replace file bila baru. |
| Hapus | `guru.lms.materi.destroy` | DELETE | `@destroy` | redirect | Hapus + bersihkan storage. Opsi "hapus terkait" untuk hapus duplikat di kelas lain. |

---

### Tugas

**Index view**: `guru/lms/tugas/index.blade.php` · **Controller**: `GuruTugasController.php` + `GuruKoreksiController.php` (untuk koreksi)

**Tampilan index**: Header dengan judul "Daftar Tugas & Latihan" + tombol **+ Buat Tugas Baru** (primary). Tabel: Judul Tugas + deskripsi singkat, Mulai, Deadline (+ badge "Lewat" bila overdue), Submitted (count `submitted_count`/`tugas_siswa_count`), Status (Aktif/Selesai), Aksi (Koreksi success/Edit warning/Hapus danger). Pagination. Empty state bila belum ada tugas.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Tugas Baru | `guru.lms.tugas.create` | GET | `@create` | `guru/lms/tugas/create.blade.php` | Form: judul, deskripsi, tanggal mulai-deadline, lampiran soal, alokasi siswa (auto semua siswa kelas). |
| Simpan | `guru.lms.tugas.store` | POST | `@store` | redirect | Validasi + buat `Tugas` + auto-create `TugasSiswa` per siswa status `belum`. |
| Edit | `guru.lms.tugas.edit` | GET | `@edit` | `guru/lms/tugas/edit.blade.php` | Form pre-fill. |
| Update | `guru.lms.tugas.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `guru.lms.tugas.destroy` | DELETE | `@destroy` | redirect | Hapus tugas + cascade `TugasSiswa`. Opsi "hapus terkait" via checkbox modal. |
| Koreksi (per tugas) | `guru.lms.tugas.koreksi` | GET | `GuruKoreksiController@index` | `guru/lms/tugas/koreksi.blade.php` | List submission siswa: nama, status (sudah/belum/terlambat), nilai, aksi koreksi. Toolbar bulk grade + AI suggest. |
| Detail Submission Siswa | `guru.lms.tugas.koreksi.show` | GET | `GuruKoreksiController@show` | `guru/lms/tugas/koreksi-show.blade.php` | Tampil jawaban siswa + form input nilai + feedback + tombol AI Suggest. |
| Simpan Koreksi | `guru.lms.tugas.koreksi.store` | POST | `GuruKoreksiController@store` | redirect | Isi `nilai` + `feedback_guru` + set status `dinilai` + trigger notifikasi siswa. |
| Bulk Grade | `guru.lms.tugas.koreksi.bulk` | POST | `GuruKoreksiController@bulkGrade` | redirect | Set nilai sama untuk banyak siswa sekaligus (mis. semua yang submitted dapat nilai default). |
| AI Suggest Nilai | `guru.lms.tugas.koreksi.ai-suggest` | POST | `GuruKoreksiController@getAiAssignmentSuggestion` | JSON | Generate saran nilai+feedback via AI berdasarkan submission siswa & rubrik tugas. |

**Catatan**: AI Suggest aktif bila `ai_question_generator_enabled=true` di `admin.ai-settings`.

---

### Ujian & Latihan — DUA route group terpisah, satu controller

> **Penting**: Walaupun controller method & view file di-share, **URL path dan route name keduanya BENAR-BENAR TERPISAH**. Dari sisi user (di browser & sidebar) ini adalah dua menu berbeda — bukan satu menu yang me-render dua hal. Tabel berikut menjelaskan pemisahannya, lalu dilanjut tabel sub-halaman.
>
> | Aspek | Ujian | Latihan |
> |---|---|---|
> | **URL path** | `/guru/lms/{kelas}/{mapel}/ujian/...` | `/guru/lms/{kelas}/{mapel}/latihan/...` |
> | **Route group** | `Route::prefix('ujian')->name('ujian.')` (`web.php:1286`) | `Route::prefix('latihan')->name('latihan.')` (`web.php:1324`) |
> | **Route name** | `guru.lms.ujian.*` | `guru.lms.latihan.*` |
> | **Controller class** | `GuruUjianController` | `GuruUjianController` (sama) |
> | **Method body** | sama persis | sama persis |
> | **Detection di controller** | `$isLatihan = false` (default) | `$isLatihan = request()->routeIs('guru.lms.latihan.*')` → `true` |
> | **DB filter di `@index`** | `WHERE tipe_ujian != 'latihan'` (semua: UH, PTS, PAS, TO, UPK, Praktek) | `WHERE tipe_ujian = 'latihan'` |
> | **Force value di `@store`** | tipe sesuai input form | `if ($isLatihan) $validated['tipe_ujian'] = 'latihan';` (override input) |
> | **View file** | `guru/lms/ujian/index.blade.php` (sama) | sama |
> | **Render di view** | label "Buat Ujian", link `guru.lms.ujian.create`, dst | label "Buat Latihan", link `guru.lms.latihan.create`, dst (via `$tipeUjian`) |

#### Ujian — `guru.lms.ujian.*`

**Index view**: `guru/lms/ujian/index.blade.php` (`$tipeUjian='ujian'`) · **Controller**: `GuruUjianController.php`

**Tampilan index**: Header "Daftar Ujian" + tombol **+ Buat Ujian** (primary, → `ujian.create`). Tabel: No, Judul + deskripsi, Tipe (badge warna sesuai jenis: UH/PTS/PAS/TO/UPK/Praktek — **tanpa 'latihan'** karena difilter di query), Tanggal Mulai-Selesai, Durasi + Jumlah Soal badge, Aksi (Lihat Hasil/Kelola Soal/Edit/Hapus). Pagination. Empty state dengan link Buat.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Ujian | `guru.lms.ujian.create` | GET | `@create` | `guru/lms/ujian/create.blade.php` | Form: judul, deskripsi, tipe (UH/PTS/PAS/TO/UPK/Praktek), tanggal mulai-selesai, durasi (menit, 0=tanpa batas). |
| Simpan | `guru.lms.ujian.store` | POST | `@store` | redirect | Validasi + buat `Ujian` (tipe sesuai input — tidak di-force). |
| Edit | `guru.lms.ujian.edit` | GET | `@edit` | `guru/lms/ujian/edit.blade.php` | Form pre-fill. |
| Update | `guru.lms.ujian.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `guru.lms.ujian.destroy` | DELETE | `@destroy` | redirect | Hapus + cascade soal & jawaban. |
| Lihat Hasil | `guru.lms.ujian.hasil` | GET | `@hasil` | `guru/lms/ujian/hasil.blade.php` | Statistik hasil + daftar siswa (nilai, status submit, waktu pengerjaan). Tombol Koreksi per siswa untuk soal essai. |
| Kelola Soal (manage) | `guru.lms.ujian.soal.manage` | GET | `@manageSoal` | `guru/lms/ujian/manage_soal.blade.php` | Halaman multi-soal: tambah/edit/hapus soal sekaligus dalam satu form. |
| Simpan Semua Soal | `guru.lms.ujian.soal.storeAll` | POST | `@storeAllSoal` | redirect | Bulk save dari manage_soal. |
| Daftar Soal (single-form) | `guru.lms.ujian.soal.index` | GET | `@soal` | `guru/lms/ujian/soal.blade.php` | List soal versi standar (alternatif manage). |
| + Tambah Soal | `guru.lms.ujian.soal.create` | GET | `@createSoal` | `guru/lms/ujian/soal-form.blade.php` | Form soal: pertanyaan, tipe (pilgan/essay), pilihan jawaban, kunci, skor. |
| Simpan Soal | `guru.lms.ujian.soal.store` | POST | `@storeSoal` | redirect | Validasi + simpan `SoalUjian`. |
| Edit Soal | `guru.lms.ujian.soal.edit` | GET | `@editSoal` | `guru/lms/ujian/soal-form.blade.php` (re-use) | Form pre-fill. |
| Update Soal | `guru.lms.ujian.soal.update` | PUT | `@updateSoal` | redirect | Update. |
| Hapus Soal | `guru.lms.ujian.soal.destroy` | DELETE | `@destroySoal` | redirect | Hapus 1 soal. |
| **AI Generate Soal** | `guru.lms.ujian.soal.ai-generate` | POST | `@aiGenerateQuestions` | JSON | Generate soal otomatis via AI (jumlah, tipe, level). Return draft untuk preview. |
| Bulk Store Soal (dari AI) | `guru.lms.ujian.soal.bulk-store` | POST | `@bulkStoreSoal` | redirect | Simpan batch soal hasil AI setelah preview. |
| Download Template Soal | `guru.lms.ujian.soal.template` | GET | `@downloadSoalTemplate` | file download | Template `.xlsx` untuk import soal. |
| Import Soal | `guru.lms.ujian.soal.import` | POST | `@importSoal` | redirect | Upload `.xlsx`, parse, batch-insert `SoalUjian`. |
| Toggle Status | `guru.lms.ujian.toggleStatus` | POST | `@toggleStatus` | redirect | Aktif/non-aktif (kontrol akses siswa). |
| Toggle Visibilitas Hasil | `guru.lms.ujian.toggleResult` | POST | `@toggleResultVisibility` | redirect | Tampilkan/sembunyikan nilai ke siswa setelah ujian. |
| Detail Koreksi Siswa | `guru.lms.ujian.koreksi.show` | GET | `@koreksiShow` | `guru/lms/ujian/koreksi.blade.php` | Form koreksi essai per siswa (per soal) + AI suggest. |
| Simpan Koreksi | `guru.lms.ujian.koreksi.store` | POST | `@koreksiStore` | redirect | Update nilai per soal + recompute total. |
| AI Suggest (per soal essai) | `guru.lms.ujian.koreksi.ai-suggest` | POST | `@getAiSuggestion` | JSON | Saran nilai untuk jawaban essai siswa via AI. |

**Catatan Ujian**: Ujian besar (PTS/PAS) hanya bisa dikerjakan siswa bila `validasi_ujian_*=true` (lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa)). Latihan TIDAK butuh validasi akses.

#### Latihan — `guru.lms.latihan.*`

**Index view**: `guru/lms/ujian/index.blade.php` (file sama, `$tipeUjian='latihan'`) · **Controller**: `GuruUjianController.php`

**Tampilan index**: Header "Daftar Latihan" + tombol **+ Buat Latihan** (primary, → `latihan.create`). Tabel sama strukturnya dengan Ujian, tapi **kolom Tipe selalu badge "Latihan"** (karena query difilter `tipe_ujian='latihan'`).

Semua route latihan adalah **mirror 1:1 dari route ujian** dengan prefix `latihan` (bukan `ujian`) — tabel di atas berlaku, tinggal ganti semua `guru.lms.ujian.*` → `guru.lms.latihan.*`. Method controller, view, dan logika operasi (CRUD soal, AI generate, koreksi, toggle, dst.) **persis sama**. Perbedaan satu-satunya:

- **DB filter di `@index`**: `WHERE tipe_ujian = 'latihan'` (vs `!=`)
- **Force di `@store`**: `tipe_ujian` selalu diset jadi `'latihan'` (override input form — kalau guru pilih tipe lain di form, akan tetap disimpan sebagai latihan)
- **Link & label di view**: `route('guru.lms.latihan.X')` (bukan `ujian.X`), label "Latihan" (bukan "Ujian")

**Catatan Latihan**: Karena route terpisah, **bookmark URL `/ujian/...` tidak akan menampilkan latihan** dan sebaliknya. Di sidebar LMS, "Latihan" dan "Ujian" muncul sebagai dua menu berbeda. Jangan tertipu controller yang sama — dari sisi struktur URL & navigasi user, ini benar-benar dua fitur terpisah dengan data terpisah di kolom `Ujian.tipe_ujian`.

---

### Forum Diskusi

**Index view**: `guru/lms/forum/index.blade.php` · **Controller**: `GuruForumController.php`

**Tampilan index**: Card **Daftar Diskusi Kelas** + tombol **+ Buat Diskusi** (success). List item per thread: badge Pinned (warning) bila pinned, badge Closed (secondary) bila ditutup, badge topik (info), judul + preview isi (130 char), avatar pengirim + nama + badge "Guru" bila dari guru, count balasan, waktu, arrow lihat. Tiap item punya dropdown 3-dot: Pin/Unpin, Tutup/Buka, Hapus.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Buat Diskusi | `guru.lms.forum.create` | GET | `@create` | `guru/lms/forum/create.blade.php` | Form: judul, isi (rich-text), topik (dropdown). |
| Simpan | `guru.lms.forum.store` | POST | `@store` | redirect | Simpan `ForumDiskusi`. |
| Detail Diskusi | `guru.lms.forum.show` | GET | `@show` | `guru/lms/forum/show.blade.php` | Thread lengkap + daftar `ForumReply` (nested partial). Form reply di bawah. |
| Balas Diskusi | `guru.lms.forum.reply` | POST | `@reply` | redirect | Simpan `ForumReply`. Tidak boleh bila forum closed. |
| Edit Reply | `guru.lms.forum.reply.update` | PUT | `@updateReply` | redirect | Update isi reply (hanya milik sendiri atau guru). |
| Hapus Reply | `guru.lms.forum.reply.destroy` | DELETE | `@destroyReply` | redirect | Soft delete reply. |
| Toggle Pin | `guru.lms.forum.togglePin` | PATCH | `@togglePin` | redirect | Flip `is_pinned`. Pinned thread tampil di atas. |
| Toggle Close | `guru.lms.forum.toggleClose` | PATCH | `@toggleClose` | redirect | Flip `is_closed`. Closed thread tidak bisa di-reply. |
| Hapus Diskusi | `guru.lms.forum.destroy` | DELETE | `@destroy` | redirect | Hapus thread + cascade replies. Opsi "hapus terkait" via SweetAlert checkbox. |

---

### Kelas Virtual (Meeting)

**Index view**: `guru/lms/meeting/index.blade.php` · **Controller**: `GuruLmsMeetingController.php`

**Tampilan index**: Card header "Kelas Virtual (Meeting)" + sub-judul mapel & kelas + tombol **+ Jadwalkan Meeting** (primary). List meeting cards — tiap card: judul + badge status (Aktif/Selesai-Nonaktif), badge platform (Zoom/Meet/lain), waktu (tanggal + jam mulai-selesai), deskripsi, tombol **Mulai Meeting** (primary, buka link di tab baru) + **Copy Link** (outline). Dropdown 3-dot: Edit, Hapus. Empty state dengan gambar + tombol Buat.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| + Jadwalkan Meeting | `guru.lms.meeting.create` | GET | `@create` | `guru/lms/meeting/create.blade.php` | Form: judul, deskripsi, platform (zoom/google_meet/lainnya), link, waktu mulai-selesai. |
| Simpan | `guru.lms.meeting.store` | POST | `@store` | redirect | Simpan `LmsMeeting`. Trigger notifikasi ke siswa kelas. |
| Edit | `guru.lms.meeting.edit` | GET | `@edit` | `guru/lms/meeting/edit.blade.php` | Form pre-fill. |
| Update | `guru.lms.meeting.update` | PUT | `@update` | redirect | Validasi & update. |
| Hapus | `guru.lms.meeting.destroy` | DELETE | `@destroy` | redirect | Soft delete meeting. |

---

### Nilai Siswa (LMS)

**Index view**: `guru/lms/nilai/index.blade.php` · **Controller**: `GuruNilaiController.php`

**Tampilan index**: Card header "Daftar Nilai Siswa" + sub-judul TA + semester + dropdown **Pilih Semester** (Ganjil/Genap, auto-submit, badge "Aktif" jika sama dengan currentSemester) + tombol **Hitung Ulang** (recompute `nilai_akhir` dari komponen) + dropdown **Excel** (Export Nilai, Download Template, Import Nilai modal). **Tabel inline-editable** dengan sticky columns (No, Nama Siswa) — header 2-baris dengan grup kolom: Tugas (T1–T5 + Rata), Latihan (L1–L5 + Rata), Ulangan Harian (UH1–UH5 + Rata), PTS, PAS, **N. Akhir** (highlight). Tiap cell adalah input number 0-100 dengan spinner ▲▼. Bulk submit via tombol di luar tabel → POST `updateBatch`. Kondisional badge "Kelas Akhir" bila kelas 9/12.

| Tombol/Aksi | Route name | HTTP | Controller@method | View | Logika ringkas |
|---|---|---|---|---|---|
| Update Nilai (single cell, AJAX) | `guru.lms.nilai.update` | POST | `@update` | JSON | Update 1 nilai komponen (mis. tugas_3) untuk 1 siswa. Auto-recompute rata + nilai_akhir. |
| Update Batch (submit tabel) | `guru.lms.nilai.updateBatch` | POST | `@updateBatch` | redirect | Bulk save seluruh tabel sekaligus. Hitung ulang nilai akhir. |
| Hitung Ulang Semua | `guru.lms.nilai.recalculate` | POST | `@recalculate` | redirect | Recompute `nilai_akhir` semua siswa berdasar bobot (mis. 30% UH, 30% PTS, 40% PAS). |
| Export Nilai Excel | `guru.lms.nilai.export-excel` | GET | `@exportExcel` | file download | Stream `.xlsx` nilai 1 mapel × 1 kelas × 1 semester. |
| Download Template Excel | `guru.lms.nilai.download-template` | GET | `@downloadTemplate` | file download | Template `.xlsx` kosong dengan kolom siswa & komponen. |
| Import Nilai Excel | `guru.lms.nilai.import-excel` | POST | `@importExcel` | redirect | Upload `.xlsx`, parse via Maatwebsite, batch update nilai. |

**Catatan**: `nilai_akhir` di tabel ini adalah **sumber data rapor** yang nanti digenerate Wali Kelas (`wali.rapor.generate-all`). Pastikan semua komponen terisi sebelum tanggal generate rapor.
