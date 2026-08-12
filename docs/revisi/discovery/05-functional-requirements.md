# Functional Requirements

Setiap requirement diturunkan dari Feature Inventory dan implementation evidence. Acceptance Criteria (AC) awal adalah kondisi objektif yang terlihat dari implementasi, bukan hasil pengujian aktual.

## AU — Autentikasi & Akun

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-AU-001 | Guest | Sistem harus memungkinkan pengguna aktif login dengan username atau email dan password. | Form tersedia; input wajib; kredensial valid membentuk session baru; dashboard sesuai role dibuka; kredensial/akun tidak valid ditolak. | FEAT-001 | `LoginRequest.php`; `LoginController.php` |
| REQ-AU-002 | Semua login | Sistem harus memungkinkan pengguna logout dengan membatalkan session. | Guard logout; session invalid; CSRF token diperbarui; pengguna kembali ke halaman publik. | FEAT-002 | `LoginController@destroy` |
| REQ-AU-003 | Guest | Sistem harus menyediakan pemulihan username/password publik melalui data akun dan email pribadi. | Request tervalidasi; tiket dibuat; email dikirim bila mail tersedia; link reset memiliki batas waktu; kegagalan jatuh ke handling yang tersedia. | FEAT-003 | `UserRecoveryController`; `EmailRecoveryService` |
| REQ-AU-004 | ADM | Sistem harus mewajibkan dan menyediakan pengaturan keamanan khusus Admin serta recovery berbasis kontrol tersebut. | Admin tanpa setup diarahkan ke setup; PIN/jawaban disimpan aman; verifikasi salah ditolak; perubahan memicu email bila tersedia. | FEAT-004 | Auth recovery controllers; middleware security setup |
| REQ-AU-005 | Semua login | Sistem harus memungkinkan pengguna memperbarui pengaturan akun dan password sendiri. | Data tervalidasi; password lama/konfirmasi diperiksa; perubahan hanya mengenai akun login; pesan hasil diberikan. | FEAT-005 | `AccountController.php` |
| REQ-AU-006 | Semua login | Sistem harus memungkinkan pengguna mengelola profil dan foto sendiri. | Profil sendiri ditampilkan; file foto memenuhi rule; foto lama dapat dihapus/diganti; data pengguna lain tidak berubah. | FEAT-006 | `ProfileController.php` |

## USR — Manajemen Pengguna

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-USR-001 | ADM | Sistem harus memungkinkan Admin mengelola akun dan profil tenaga pendidik. | List/form/detail tersedia; data unik/relasi tervalidasi; create/update tersimpan; penghapusan berjejak ditolak sesuai guard. | FEAT-007 | `Admin/UserController.php` |
| REQ-USR-002 | ADM | Sistem harus memungkinkan Admin mengelola akun dan profil siswa. | Siswa dapat dibuat/diubah; status akun mengikuti status siswa; data berelasi dilindungi saat delete; transaksi menjaga konsistensi User-Siswa. | FEAT-008 | `UserController.php`; siswa tests |
| REQ-USR-003 | ADM | Sistem harus memungkinkan Admin mengelola akun Orang Tua/Wali Siswa. | Akun role orang_tua difilter; create/update/delete tersedia; relasi anak ditampilkan; data tidak valid ditolak. | FEAT-009 | `UserController.php` |
| REQ-USR-004 | ADM | Sistem harus memungkinkan Admin mengaktifkan atau menonaktifkan akun yang diizinkan. | Status berubah; akun nonaktif tidak dapat login/lanjut session; akun Admin/Ketua dilindungi dari operasi tertentu sesuai guard. | FEAT-010 | `UserController.php`; `EnsureUserIsActive.php` |
| REQ-USR-005 | ADM | Sistem harus memungkinkan Admin mengimpor pengguna dari spreadsheet sesuai tipe. | File/format tervalidasi; baris valid diproses; nilai status/relasi dinormalisasi; error import dilaporkan. | FEAT-011 | `app/Imports/*Import.php`; routes user import |
| REQ-USR-006 | ADM | Sistem harus menyediakan template import pengguna yang sesuai schema import. | Template siswa/guru/orang tua dapat diunduh; kolom sesuai importer; respons berupa XLSX. | FEAT-012 | `app/Exports/Templates/` |
| REQ-USR-007 | ADM | Sistem harus memungkinkan Admin memproses dan meninjau tiket recovery. | Antrean/history ditampilkan; tindakan hanya Admin; status dan pelaku dicatat; bulk delete history mengikuti validasi. | FEAT-013 | `AdminRecoveryTicketController.php` |

## ORG — Organisasi & Data Induk

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-ORG-001 | ADM | Sistem harus memungkinkan Admin mengelola cabang. | CRUD tersedia; kode/nama tervalidasi; relasi yang menghalangi delete ditangani; list/detail dapat dibuka. | FEAT-014 | `CabangController.php` |
| REQ-ORG-002 | ADM, WKA | Sistem harus memungkinkan pengelola mengelola dan mengaktifkan tahun ajaran sesuai akses. | Periode tervalidasi; hanya state aktif yang diizinkan logic; Waka sesuai scope; data terkait tidak hilang diam-diam. | FEAT-015 | TahunAjaran controllers/model |
| REQ-ORG-003 | ADM, WKA | Sistem harus memungkinkan pengelola mengelola kelas per cabang dan tahun ajaran. | CRUD/import/print tersedia; kapasitas/jenjang/TA/cabang tervalidasi; Waka ditolak untuk cabang lain. | FEAT-016 | Kelas controllers/import |
| REQ-ORG-004 | ADM, WKA | Sistem harus memungkinkan pengelola menambah atau mengeluarkan siswa dari kelas. | Target siswa/kelas ada; scope cabang benar; kapasitas/state diperiksa; `kelas_id` diperbarui konsisten. | FEAT-017 | Kelas/ManajemenSiswa controllers |
| REQ-ORG-005 | ADM, WKA | Sistem harus memungkinkan pengelola menetapkan wali kelas yang memenuhi scope. | Guru dan kelas valid; Waka hanya cabangnya; assignment dibuat/dilepas; daftar wali memperlihatkan hasil. | FEAT-018 | WaliKelas controllers; `WaliKelasAssignment` |
| REQ-ORG-006 | ADM, WKA | Sistem harus memungkinkan pengelola menautkan dan melepas relasi siswa-orang tua. | Parent role valid; siswa berada dalam scope; relationship unik/valid; pivot dan atribut akses tersimpan. | FEAT-019 | ManajemenSiswa controllers; `StudentParent` |
| REQ-ORG-007 | ADM, WKA | Sistem harus menyediakan pencarian, detail, daftar kelas, kartu dan cetak data siswa. | Filter menghasilkan data sesuai scope; detail hanya target valid; cetak/kartu memuat siswa yang dipilih; historical state read-only. | FEAT-020 | ManajemenSiswa controllers/views |

## AKD — Perencanaan Akademik

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-AKD-001 | ADM, WKA | Sistem harus memungkinkan pengelola mengelola mata pelajaran. | CRUD/import/print tersedia; kode/nama/filter agama tervalidasi; penghapusan berelasi ditangani; Waka sesuai akses. | FEAT-021 | MataPelajaran controllers/import |
| REQ-AKD-002 | ADM, WKA | Sistem harus memungkinkan pengelola membuat dan mengubah jadwal multi-kelas. | Guru/mapel/TA/kelas/waktu valid; konflik dan istirahat diperiksa; pivot kelas tersimpan; Waka hanya cabangnya. | FEAT-022 | JadwalPelajaran controllers/model |
| REQ-AKD-003 | ADM, WKA | Sistem harus memungkinkan pengelola mengimpor atau menduplikasi jadwal. | File/sumber/target valid; jadwal valid disalin; konflik/error dilaporkan; target TA/cabang sesuai akses. | FEAT-023 | Jadwal controllers; `JadwalPelajaranImport` |
| REQ-AKD-004 | ADM, WKA | Sistem harus memungkinkan pengelola mengganti guru jadwal satuan atau massal dengan jejak perubahan. | Guru lama/baru valid; scope benar; jadwal terpilih berubah; history dan assignment disinkronkan. | FEAT-024 | Jadwal controllers; History model |
| REQ-AKD-005 | ADM, WKA, WKL, GRU, SIS | Sistem harus menyediakan cetak/export jadwal sesuai data yang boleh dilihat pengguna. | Filter/kelas valid; hanya jadwal scope pengguna muncul; output print/PDF/XLSX terbentuk. | FEAT-025 | routes/controllers/views jadwal |
| REQ-AKD-006 | ADM, WKA | Sistem harus menurunkan assignment guru-kelas-mapel dari jadwal. | Daftar derived tersedia; rebuild memakai jadwal valid; relasi usang/baru ditangani; scope Waka dipertahankan. | FEAT-026 | GuruPengajar controllers |
| REQ-AKD-007 | ADM, WKA | Sistem harus memungkinkan pengelola mengatur interval istirahat. | Jenjang/hari/jam valid; overlap yang tidak diizinkan ditolak; status dapat ditoggle; jadwal memakai aturan tersebut. | FEAT-027 | PengaturanIstirahat controllers |
| REQ-AKD-008 | WKL | Sistem harus memungkinkan Wali Kelas melihat dan mencetak jadwal kelas yang diwalikan. | Kelas terpilih merupakan assignment wali; jadwal ditampilkan; kelas lain ditolak; output print tersedia. | FEAT-028 | WaliKelas/JadwalPelajaranController |
| REQ-AKD-009 | GRU | Sistem harus memungkinkan Guru melihat kelas, mapel, dan jadwal aktif yang diampu. | Tenaga pendidik terhubung; data di-scope assignment/TA aktif; kelas lain tidak muncul; jadwal dapat dibuka. | FEAT-029 | GuruKelas/GuruJadwal controllers |
| REQ-AKD-010 | ADM | Sistem harus menyediakan laporan/cetak akademik dari data aktual. | Jenis/filter laporan valid; data sesuai filter; output view/print tersedia; empty state ditangani. | FEAT-030 | `CetakLaporanController.php` |

## KON — Konten & Informasi

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-KON-001 | ADM, SEK, SIS | Sistem harus memungkinkan pengelola mengelola kalender dan siswa melihat agenda yang dipublikasikan. | CRUD/visibility/cetak tersedia; tanggal/lampiran valid; siswa hanya melihat agenda tersedia; file disimpan/dihapus konsisten. | FEAT-031 | Akademik/Sekretaris/Siswa controllers |
| REQ-KON-002 | ADM, SEK, SIS | Sistem harus memungkinkan pengelola mengelola pengumuman dan siswa membacanya. | Isi/periode/lampiran valid; CRUD tersedia; pengumuman yang layak tampil pada portal siswa; notifikasi sesuai trigger. | FEAT-032 | controllers; `NotificationService` |
| REQ-KON-003 | ADM, SEK, Publik | Sistem harus memungkinkan pengelola menerbitkan berita untuk dibaca publik. | CRUD/featured tersedia; slug/konten/media valid; berita publik dapat dibuka; target tidak ada menghasilkan 404. | FEAT-033 | controllers berita/model |
| REQ-KON-004 | ADM, SEK, Publik | Sistem harus memungkinkan pengelola mengelola flyer publik. | Gambar valid; create/update/delete mengelola storage; flyer tersedia bagi view terkait. | FEAT-034 | controllers flyer |
| REQ-KON-005 | ADM | Sistem harus memungkinkan Admin mengubah section dan media landing page. | Page/section valid; tipe field diproses; upload tersimpan; urutan/konten terbarui; halaman publik memakai data baru. | FEAT-035 | LandingPage controller/models |
| REQ-KON-006 | Publik | Sistem harus menyajikan halaman profil/program/informasi, berita dan sitemap tanpa login. | Route public dapat dibuka; page dari CMS tersedia; page yang tidak dikonfigurasi ditangani; sitemap menghasilkan XML. | FEAT-036 | public routes/controllers |

## KEU — Keuangan

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-KEU-001 | ADM, BEN | Sistem harus memungkinkan pengelola membuat dan mengelola tagihan siswa. | Siswa/TA/jenis/nominal/jatuh tempo valid; create/bulk/update/delete-guard tersedia; status awal benar. | FEAT-037 | Tagihan controllers/model |
| REQ-KEU-002 | ADM, BEN | Sistem harus memungkinkan pengelola menghasilkan SPP massal tanpa duplikasi yang dilarang. | Periode/kelas/siswa valid; preview/count tersedia; existing charge tidak digandakan; tagihan baru dibuat dan dinotifikasi. | FEAT-038 | Tagihan controllers; NotificationService |
| REQ-KEU-003 | ADM, BEN | Sistem harus memungkinkan import, export dan cetak tagihan. | File/template valid; nominal dinormalisasi; baris valid diproses; output laporan/daftar terbentuk. | FEAT-039 | `TagihanImport`; Tagihan controllers |
| REQ-KEU-004 | ADM, BEN | Sistem harus memungkinkan tunggakan lama dialihkan ke tahun aktif dengan traceability. | Eligible balance dihitung dari pembayaran disetujui; preview tersedia; transaction membuat link asal-tujuan; re-carryover ditolak. | FEAT-040 | `TunggakanCarryoverService` |
| REQ-KEU-005 | ADM, BEN | Sistem harus memungkinkan pengelola mencatat pembayaran manual. | Siswa/tagihan/nominal/metode valid; payment record unik dibuat; validator/status sesuai alur; tagihan dihitung ulang. | FEAT-041 | Pembayaran controllers |
| REQ-KEU-006 | ADM, BEN | Sistem harus memungkinkan pengelola menyetujui atau menolak pembayaran pending. | Target ada; state pending; keputusan/catatan tersimpan; audit dibuat; tagihan dan akses diperbarui bila disetujui. | FEAT-042 | Pembayaran controllers; AuditLog |
| REQ-KEU-007 | ORT | Sistem harus memungkinkan orang tua mengajukan transfer untuk tagihan anak tertaut. | Ownership anak/tagihan diperiksa; tunai online ditolak; bukti gambar wajib dan valid; record pending/notifikasi dibuat. | FEAT-043 | `OrangTuaController@prosesBayar/processBulkPay` |
| REQ-KEU-008 | ORT | Sistem harus memungkinkan orang tua membayar satu/lebih tagihan anak dengan Midtrans ketika aktif. | Konfigurasi lengkap/enabled; item dan total valid; Snap token dibuat; payment pending/order ID tersimpan; halaman Snap dibuka. | FEAT-044 | `MidtransService`; parent controller |
| REQ-KEU-009 | Sistem | Sistem harus menyinkronkan status Midtrans dari webhook/API otoritatif. | Signature invalid → 403; order tidak ada → 404; status dipetakan; payment/tagihan/audit/notifikasi diperbarui idempotent. | FEAT-045 | `MidtransWebhookController`; payment tests |
| REQ-KEU-010 | ADM, BEN | Sistem harus memungkinkan pengelola mengatur kanal rekening, tunai dan Midtrans. | Field per tipe tervalidasi; server key terenkripsi; mode/enable tersimpan; key ditutupi pada UI; opsi pengguna mengikuti status konfigurasi. | FEAT-046 | InfoPembayaran controller/model/view |
| REQ-KEU-011 | ADM, BEN | Sistem harus menyediakan laporan keuangan yang dapat difilter dan dicetak. | Filter metode/status/periode/siswa diterapkan; agregat konsisten dengan payment disetujui; pagination tidak memotong filter; print tersedia. | FEAT-047 | Laporan controllers; filter tests |
| REQ-KEU-012 | SIS | Sistem harus memungkinkan Siswa melihat tagihan dan riwayat pembayaran miliknya secara read-only. | Data ditautkan melalui siswa login; daftar tagihan/riwayat dan cetak bukti tersedia; pembayaran milik siswa lain ditolak; submit pembayaran dari portal Siswa selalu dialihkan ke Orang Tua/Wali Siswa. | FEAT-101 | `SiaPembayaranController.php`; `routes/web.php:1440-1449` |

## PRS — Presensi & Izin

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-PRS-001 | WKL | Sistem harus memungkinkan Wali Kelas mencatat dan memperbarui presensi siswa kelasnya. | Assignment kelas diperiksa; tanggal/status/siswa valid; siswa kelas lain ditolak; record dibuat/diperbarui. | FEAT-048 | `WaliKelas/PresensiController.php` |
| REQ-PRS-002 | WKL | Sistem harus menyediakan riwayat, rekap, cetak dan import presensi kelas. | Filter periode valid; hanya kelas wali; template/import terproses; output rekap/print tersedia. | FEAT-049 | PresensiController/views |
| REQ-PRS-003 | ORT | Sistem harus memungkinkan orang tua mengajukan/mengubah izin anak tertaut. | Ownership diperiksa; tanggal/alasan/bukti valid; state yang masih dapat diubah dipatuhi; notifikasi dibuat. | FEAT-050 | parent presensi methods |
| REQ-PRS-004 | WKL | Sistem harus memungkinkan Wali Kelas memvalidasi izin siswa kelasnya. | Target pengajuan dan assignment valid; keputusan/catatan tersimpan; kelas lain ditolak; pihak terkait dinotifikasi. | FEAT-051 | PresensiController; IDOR test |
| REQ-PRS-005 | SIS, ORT | Sistem harus menampilkan riwayat presensi hanya kepada siswa terkait dan orang tua tertaut. | Siswa hanya data sendiri; parent hanya child relation; filter menghasilkan data tepat; direct URL lain ditolak. | FEAT-052 | SiaPresensi/OrangTua controllers |

## NIL — Penilaian

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-NIL-001 | GRU | Sistem harus memungkinkan Guru mengelola nilai pada kelas/mapel yang diampu. | Assignment diperiksa; nilai desimal/koma dinormalisasi; rentang valid; mapel/kelas lain ditolak; data tersimpan. | FEAT-053 | GuruNilaiController; decimal/IDOR tests |
| REQ-NIL-002 | WKL | Sistem harus memungkinkan Wali Kelas mengelola dan mencetak nilai kelasnya. | Kelas assignment valid; edit/clear/import/export/print tersedia; siswa kelas lain ditolak; perubahan wali ditandai. | FEAT-054 | WaliKelas/NilaiController |
| REQ-NIL-003 | Sistem, GRU, WKL | Sistem harus menyinkronkan nilai submission LMS ke rekap nilai yang tepat. | Observer dipicu setelah perubahan; siswa-mapel-kelas benar; nilai diperbarui idempotent; wali dapat sync guru valid. | FEAT-055 | Observers; `NilaiSyncService` |
| REQ-NIL-004 | GRU | Sistem harus memungkinkan Guru mengoreksi submission tugas dalam assignment-nya. | Submission di-scope; input nilai/feedback valid; AI failure tidak merusak data; hasil tersimpan dan sinkron. | FEAT-056 | GuruKoreksiController; AiGradingService |
| REQ-NIL-005 | SIS, ORT | Sistem harus menampilkan nilai hanya untuk siswa terkait/anak tertaut. | Ownership diterapkan; daftar sesuai TA/mapel; nilai tidak ada ditampilkan aman; data siswa lain tidak bocor. | FEAT-057 | SiaDashboard/parent controllers/views |

## RAP — Rapor

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-RAP-001 | WKL | Sistem harus memungkinkan Wali Kelas menghasilkan rapor dari data kelasnya. | Siswa/kelas/semester/TA valid; satu/all dapat dibuat; duplikasi ditangani; nilai/kehadiran ditautkan. | FEAT-058 | RaporController generate methods |
| REQ-RAP-002 | WKL | Sistem harus memungkinkan Wali Kelas mengisi dan memformat rapor. | Ownership kelas; field/nilai/template valid; apply single/all/batch sesuai scope; urutan/kehadiran tersimpan. | FEAT-059 | RaporController; Template controller/tests |
| REQ-RAP-003 | WKL | Sistem harus memungkinkan perubahan state publikasi rapor sesuai aturan. | Target milik kelas; state transition diperiksa; publish/withdraw/reset/delete memberi hasil; state terlarang ditolak. | FEAT-060 | RaporController |
| REQ-RAP-004 | WKL | Sistem harus memungkinkan Wali mengirim atau membatalkan pengiriman rapor untuk validasi Ketua. | Rapor siap dan milik kelas; status berubah; bulk hanya target valid; Ketua dinotifikasi. | FEAT-061 | RaporController validation methods |
| REQ-RAP-005 | KET | Sistem harus memungkinkan Ketua memvalidasi, membatalkan atau meminta revisi rapor. | Target/status valid; single/bulk tersedia; alasan revisi tervalidasi; keputusan tersimpan dan dinotifikasi. | FEAT-062 | Ketua/ValidasiRaporController |
| REQ-RAP-006 | ORT, WKL | Sistem harus menerapkan workflow persetujuan sebelum Orang Tua mengunduh rapor. | Request hanya child/rapor valid; Wali hanya kelasnya; approve menghasilkan token; token invalid/expired/unauthorized ditolak. | FEAT-063 | RequestDownload model/controllers |
| REQ-RAP-007 | WKL | Sistem harus menyediakan arsip read-only bagi kelas yang pernah diwalikan. | Historical assignment diverifikasi; rapor/nilai/presensi dapat dilihat; aksi mutasi tidak tersedia; kelas tanpa riwayat ditolak. | FEAT-064 | WaliKelasArsipController |

## LMS — Pembelajaran Daring

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-LMS-001 | GRU | Sistem harus menyediakan dashboard LMS hanya untuk assignment kelas-mapel Guru. | Guru terhubung TenagaPendidik; kelas/mapel ada; assignment valid; kombinasi lain menghasilkan 403/404. | FEAT-065 | GuruLmsController |
| REQ-LMS-002 | GRU | Sistem harus memungkinkan Guru mengelola materi kelas-mapel yang diampu. | Input/tipe file valid; CRUD tersedia; storage konsisten; materi assignment lain ditolak. | FEAT-066 | GuruMateriController; upload test |
| REQ-LMS-003 | SIS | Sistem harus memungkinkan Siswa membaca materi mapel kelasnya. | Siswa aktif; LMS/jenjang/mapel diizinkan; materi tersedia ditampilkan; URL mapel lain ditolak. | FEAT-067 | LmsMateriController; LMS middleware |
| REQ-LMS-004 | GRU | Sistem harus memungkinkan Guru mengelola tugas pada assignment-nya. | Input/periode/file valid; CRUD tersedia; konten lain ditolak; notifikasi dibuat. | FEAT-068 | GuruTugasController |
| REQ-LMS-005 | SIS | Sistem harus memungkinkan Siswa mengirim jawaban tugas yang dapat dikerjakan. | Ownership/scope/tenggat diperiksa; teks/file valid; submission tersimpan; kondisi terkunci ditolak. | FEAT-069 | LmsTugasController |
| REQ-LMS-006 | GRU | Sistem harus memungkinkan Guru mengelola konfigurasi dan hasil ujian pada assignment-nya. | Tipe/waktu/durasi/attempt valid; CRUD/result/correction tersedia; assignment lain ditolak. | FEAT-070 | GuruUjianController |
| REQ-LMS-007 | GRU | Sistem harus memungkinkan Guru mengelola dan import/export bank soal. | Tipe soal/opsi/kunci/bobot valid; single/bulk/import tersedia; soal terikat ujian tepat; IDOR ditolak. | FEAT-071 | GuruUjianController; Soal import/model/tests |
| REQ-LMS-008 | GRU | Sistem harus memungkinkan Guru menghasilkan draf soal melalui provider AI yang dikonfigurasi. | Topik tidak kosong; jumlah 1–10; provider/key/model tersedia; respons dinormalisasi; hasil rusak ditolak tanpa merusak bank soal. | FEAT-072 | AiQuestionGeneratorService; tests |
| REQ-LMS-009 | SIS | Sistem harus memungkinkan Siswa memulai, menyimpan dan submit ujian yang eligible. | Akses finansial/LMS/mapel/time/attempt valid; session dibuat; autosave terikat attempt; submit menghitung/menyimpan hasil; kunci tidak bocor. | FEAT-073 | LmsUjianController; security tests |
| REQ-LMS-010 | GRU, SIS | Sistem harus mencatat dan menampilkan data pengawasan ujian sesuai akses. | Status soal/log terikat attempt; siswa hanya attempt sendiri; guru hanya ujian assignment; monitoring tidak mengubah jawaban. | FEAT-074 | migration pengawasan; controllers |
| REQ-LMS-011 | GRU | Sistem harus memungkinkan Guru mengelola latihan pada assignment-nya. | Tipe latihan dipertahankan; CRUD/soal/result tersedia; validasi sama yang relevan; assignment lain ditolak. | FEAT-075 | GuruUjianController routes latihan |
| REQ-LMS-012 | SIS | Sistem harus memungkinkan Siswa mengerjakan/retake latihan sesuai aturan. | Akses/time/attempt diperiksa; jawaban/nilai tersimpan; retake hanya jika allowed; latihan lain ditolak. | FEAT-076 | LmsUjianController |
| REQ-LMS-013 | GRU, SIS | Sistem harus menyediakan forum diskusi ter-scope kelas-mapel. | Create/reply valid; parent reply harus diskusi sama; author/assignment memengaruhi edit/delete; cross-mapel ditolak. | FEAT-077 | Forum controllers; forum tests |
| REQ-LMS-014 | GRU, SIS | Sistem harus menyediakan meeting virtual per kelas-mapel. | Guru assignment dapat CRUD link/jadwal; siswa mapel dapat melihat/membuka; link/time valid; mapel lain ditolak. | FEAT-078 | Meeting controllers/model |
| REQ-LMS-015 | GRU | Sistem harus memungkinkan Guru menyalin konten historis milik/pernah diampu ke assignment aktif. | Sumber di-scope; target assignment aktif; tipe konten valid; bulk/single transaction; soal opsional ikut tersalin. | FEAT-079 | GuruLmsArsipService/tests |

## PRM — Kenaikan Kelas

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-PRM-001 | ADM, WKA | Sistem harus memungkinkan pengelola menentukan KKM. | TA aktif ada; nilai KKM/mapel/jenjang valid; setting tersimpan; perhitungan promotion membacanya. | FEAT-080 | PromotionKKMController |
| REQ-PRM-002 | ADM, WKA | Sistem harus memungkinkan pengelola menentukan kriteria/tujuan kenaikan. | TA/threshold/bobot/map kelas tujuan valid; readiness diperiksa; setting tersimpan transactionally. | FEAT-081 | PromotionSettingsController |
| REQ-PRM-003 | WKL | Sistem harus menampilkan prediksi kelayakan siswa kelas wali. | Assignment/TA aktif valid; akademik dan finansial dihitung; alasan/status terlihat; kelas lain ditolak. | FEAT-082 | Wali PromotionController; PromotionService |
| REQ-PRM-004 | ADM, BEN | Sistem harus memungkinkan validasi/override aspek finansial kenaikan. | Siswa/status valid; alasan/keputusan tervalidasi; cicilan/sisa dihitung; perubahan dicatat. | FEAT-083 | PromotionValidation controllers/tests |
| REQ-PRM-005 | KET | Sistem harus memungkinkan Ketua memberi keputusan kenaikan satuan atau bulk. | Target pending valid; approve/reject dan history tersedia; keputusan dicatat; notifikasi dikirim. | FEAT-084 | PromotionApprovalController |
| REQ-PRM-006 | ADM, WKA | Sistem harus memungkinkan eksekusi dan rollback kenaikan kelas secara terkontrol. | Readiness dan approval diperiksa; transaction mengubah kelas/status; selected/rollback sesuai state; history tetap tersedia. | FEAT-085 | PromotionReportController; PromotionService |
| REQ-PRM-007 | ADM, WKA, Sistem | Sistem harus memungkinkan penjadwalan dan eksekusi kenaikan tanpa overlap. | Jadwal valid disimpan; command memproses jadwal due; lock overlap aktif; status/statistik eksekusi diperbarui. | FEAT-086 | PromotionSchedule; console command/routes |

## MON — Monitoring & Catatan

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-MON-001 | ADM, KET, WKA | Sistem harus menyediakan monitoring pengguna, siswa, guru dan wali kelas sesuai scope. | Filter diterapkan; Waka hanya cabang; detail valid dapat dibuka; data lintas scope ditolak. | FEAT-087 | Monitoring/Ketua/Waka controllers |
| REQ-MON-002 | ADM, KET, WKA | Sistem harus menyediakan monitoring dan preview konten LMS. | Kelas/type/content valid; Waka hanya cabang; preview menampilkan konten; tipe tidak dikenal → 404. | FEAT-088 | LmsMonitoringService |
| REQ-MON-003 | ADM, KET, WKA | Sistem harus memungkinkan pimpinan mengirim dan mengelola catatan/teguran. | Penerima/isi valid; catatan tersimpan dengan pengirim; hanya pengirim yang menghapus; penerima dinotifikasi. | FEAT-089 | controllers Catatan; NotificationService |
| REQ-MON-004 | GRU | Sistem harus memungkinkan Guru melihat catatan monitoring untuk dirinya. | Tenaga pendidik terhubung; list/detail di-scope guru; target lain tidak ditemukan; status baca dapat tercermin. | FEAT-090 | GuruCatatanMonitoringController |

## NOT — Notifikasi

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-NOT-001 | Semua login | Sistem harus menampilkan pusat notifikasi milik pengguna. | List/recent/today/unread count hanya `user_id` login; detail target lain 404; link/icon/time tersedia. | FEAT-091 | NotificationController/model |
| REQ-NOT-002 | Semua login | Sistem harus memungkinkan pengguna mengubah status baca atau menghapus notifikasinya. | Single/bulk action tervalidasi; hanya milik sendiri; unread count berubah; delete tidak menyentuh milik lain. | FEAT-092 | NotificationController |
| REQ-NOT-003 | Sistem | Sistem harus menghasilkan reminder deadline dan kalender terjadwal tanpa duplikasi yang dilarang. | Command dijadwalkan 06:00 WIB; kandidat H-1/H-3 dipilih; notifikasi dibuat untuk target; rerun ditangani service. | FEAT-093 | routes console; NotificationScheduler/Service |

## SWA — Portal Siswa

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-SWA-001 | SIS | Sistem harus menyediakan dashboard SIA dan LMS berdasarkan profil/status siswa. | User terhubung Siswa; data kelas/TA diringkas; LMS tunduk feature flag jenjang; navigasi sesuai status. | FEAT-094 | SiswaDashboard/LmsDashboard controllers; middleware |
| REQ-SWA-002 | SIS | Sistem harus menampilkan informasi akademik hanya milik siswa login. | Query memakai `user_id`/siswa; jadwal/mapel/absensi/nilai sesuai kelas; direct URL data lain ditolak. | FEAT-095 | Siswa controllers; LMS middleware |
| REQ-SWA-003 | SIS alumni | Sistem harus membatasi alumni pada akses riwayat akademik read-only. | Status `lulus` terdeteksi; route aktif/mutasi dialihkan; dashboard/profile/notifikasi yang diizinkan tetap tersedia. | FEAT-096 | `CheckStudentActive.php` |

## WLS — Portal Orang Tua

| Requirement ID | Role | Functional Requirement | Acceptance Criteria Awal | Feature ID | Source |
|---|---|---|---|---|---|
| REQ-WLS-001 | ORT | Sistem harus menampilkan dashboard anak yang tertaut ke Orang Tua login. | Data dari `children()`; ringkasan tiap anak tersedia; anak pengguna lain tidak muncul; empty state ditangani. | FEAT-097 | `OrangTuaController@dashboard`; User model |
| REQ-WLS-002 | ORT | Sistem harus menampilkan tagihan, pembayaran dan invoice hanya untuk anak tertaut. | Child ownership diperiksa; rincian/riwayat/total benar; invoice target lain ditolak; output cetak tersedia. | FEAT-098 | parent tagihan/invoice methods |
| REQ-WLS-003 | ORT | Sistem harus menampilkan rapor dan presensi hanya untuk anak tertaut. | Ownership diperiksa pada setiap object; detail/riwayat tersedia; data anak lain 403/404; aksi izin mengikuti rule. | FEAT-099 | parent academic/presensi methods |
| REQ-WLS-004 | ORT | Sistem harus memungkinkan Orang Tua meminta dan menggunakan akses download rapor anak. | Rapor child valid; request tercatat; hanya token approved/valid yang mengunduh; token salah/expired ditolak. | FEAT-100 | parent request/download methods; RequestDownload model |

## Traceability Summary

- 101 Feature ID memiliki tepat satu functional requirement primer.
- Requirement ID unik per kode modul: 101 total.
- Requirement lintas-modul (contoh payment → notification, LMS → nilai) tetap ditautkan melalui Business Flow dan Integration Inventory agar dependensi tidak hilang.
