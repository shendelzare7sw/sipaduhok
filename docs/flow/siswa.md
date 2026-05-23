# Role: Siswa

> Kembali ke [flow.md](../../flow.md) · Role `siswa` · Level 6 · Prefix `/siswa` · Route `siswa.` · Middleware `role:siswa` + `student.active` (siswa harus berstatus aktif).

## Ringkasan Peran

Peserta didik. **Punya dua konteks** (dua sidebar):
1. **SIA (Sistem Informasi Akademik)** — info akademik pribadi: dashboard, presensi, data penilaian.
2. **LMS (HOK-LMS)** — pembelajaran daring per mata pelajaran: materi, tugas, latihan, ujian, forum, kelas virtual, kalender, jadwal, daftar guru.

Catatan desain: **rapor & pembayaran TIDAK ada di sisi siswa** — sengaja dipindah ke Orang Tua (lihat komentar di sidebar SIA & grup route orang-tua) agar siswa tidak menyembunyikan info keuangan/rapor; siswa fokus belajar.

## Layout & Sidebar (DUA layout)

| Konteks | File sidebar | Kapan tampil | Akses |
|---|---|---|---|
| SIA | `resources/views/siswa/partials/sneat-sidebar-sia.blade.php` | Halaman `/siswa/sia/...` | Selalu |
| LMS | `resources/views/siswa/partials/sidebar-lms.blade.php` | Halaman `/siswa/lms/...` | Hanya jika jenjang kelas siswa ∈ `AppSetting` `lms_allowed_jenjang` (middleware `lms.access`) |

Menu "HOK-LMS" di sidebar SIA hanya muncul bila `$showLms` true (jenjang diizinkan). Sidebar LMS menampilkan daftar **mata pelajaran dinamis** dari `JadwalPelajaran` kelas siswa.

## Peta Menu — Konteks SIA

| Menu | Route | Controller@method | Model |
|---|---|---|---|
| Dashboard SIA | `siswa.sia.dashboard` | `Siswa\SiaDashboardController@index` | `Siswa`,`Nilai`,`Presensi` |
| (Router) Dashboard | `siswa.dashboard` | `Siswa\SiswaDashboardController@index` | — |
| HOK-LMS (link) | `siswa.lms.dashboard` | `Siswa\LmsDashboardController@index` | — |
| Presensi | `siswa.sia.presensi.index` | `Siswa\SiaPresensiController@index` | `Presensi` |
| Data Penilaian | `siswa.sia.penilaian` | `Siswa\SiaDashboardController@penilaian` | `Nilai` |

> Route ada tapi tidak di sidebar: `siswa.sia.pembayaran.*` (`SiaPembayaranController`, termasuk callback Midtrans) — sebagian besar fungsi pembayaran dialihkan ke Orang Tua. Rapor siswa (`SiaRaporController`) **di-disable** (dikomentari di routes).

## Peta Menu — Konteks LMS (middleware `lms.access`)

| Grup | Menu | Route | Controller@method | Model |
|---|---|---|---|---|
| Beranda | Beranda | `siswa.lms.dashboard` | `Siswa\LmsDashboardController@index` | `Materi`,`Tugas`,`Ujian` |
| Mata Pelajaran | [Nama Mapel] (dinamis) | `siswa.lms.mapel.show` (mapelId) | `Siswa\LmsMateriController@show` | `MataPelajaran`,`Materi` |
| (dalam mapel) | Materi | `siswa.lms.mapel.materi` | `Siswa\LmsMateriController@lihatMateri` | `Materi` |
| (dalam mapel) | Tugas | `siswa.lms.mapel.tugas.index`/`.show`/`.submit` | `Siswa\LmsTugasController@index/show/submit` | `Tugas`,`TugasSiswa` |
| (dalam mapel) | Ujian | `siswa.lms.mapel.ujian.show`/`mulai`/`submit`/`retake`/`autosave`/`review` | `Siswa\LmsUjianController` | `Ujian`,`UjianSiswa`,`JawabanSiswa` |
| (dalam mapel) | Latihan | `siswa.lms.mapel.latihan.*` | `Siswa\LmsUjianController` | `Ujian`,`UjianSiswa` |
| (dalam mapel) | Forum | `siswa.lms.mapel.forum.*` | `Siswa\LmsForumController` | `ForumDiskusi`,`ForumReply` |
| (dalam mapel) | Kelas Virtual | `siswa.lms.mapel.meeting.index` | `Siswa\SiswaLmsMeetingController@index` | `LmsMeeting` |
| Akademik | Kalender Akademik | `siswa.lms.kalender` (+ `.detail`) | `Siswa\SiswaDashboardController@kalenderTahunan/kalenderDetail` | `KalenderAkademik` |
| Akademik | Jadwal Pelajaran | `siswa.lms.jadwal` (+ `.print`) | `Siswa\LmsDashboardController@jadwal/printJadwal` | `JadwalPelajaran` |
| Akademik | Daftar Guru | `siswa.lms.guru` | `Siswa\LmsDashboardController@guru` | `TenagaPendidik` |
| (link) | Kembali ke SIA | `siswa.sia.dashboard` | — | — |

Akses mapel dijaga middleware `siswa.mapel.access` (`CheckSiswaMapelAccess`) — siswa hanya boleh buka mapel yang ada di jadwal kelasnya. View dir: `resources/views/siswa/`, LMS di `resources/views/siswa/lms/`.

## Penjelasan Menu Non-Trivial / Lintas-Role

- **Pengumpulan Tugas** (`siswa.lms.mapel.tugas.submit`) — `@submit` membuat/memperbarui `TugasSiswa`: cek deadline → status `dikerjakan` atau `terlambat`; cek batas pengulangan bila `bisa_diulang`. Lalu Guru mengoreksi (status → `dinilai`) dan siswa melihat nilai+feedback bila `tampilkan_nilai`. Lihat [flow.md §5.5](../../flow.md#55-pembelajaran-lms-guru--siswa).
- **Ujian** — hanya bisa dikerjakan bila tervalidasi (gerbang `validasi_ujian_bendahara`/`_wali`, lihat [flow.md §5.3](../../flow.md#53-validasi-akses-ujian-bendaharawali-kelasadmin--siswa)). Mendukung `autosave` & `retake` (bila diizinkan guru).
- **Akses LMS bertingkat**: `student.active` (akun aktif) → `lms.access` (jenjang diizinkan) → `siswa.mapel.access` (mapel ada di jadwal kelas). Jika salah satu gagal, menu LMS/mapel tidak bisa diakses.
- **Rapor & Pembayaran sengaja absen** — tanggung jawab dilempar ke Orang Tua ([docs/flow/orang-tua.md](orang-tua.md)). Ini keputusan desain, bukan fitur yang belum dibuat.
