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
