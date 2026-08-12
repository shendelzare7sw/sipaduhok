# SIPADUHOK — Documented Requirement Inventory

> Sumber: `docs/laporan akhir.pdf` (186 halaman PDF). Nomor halaman di bawah adalah nomor halaman tercetak yang sama dengan halaman PDF sampai halaman 175. Lampiran BA Serah Terima muncul pada PDF halaman 176 dengan nomor tercetak 385, tetapi tidak digunakan sebagai bukti requirement karena isi utamanya berupa gambar dan tidak menghasilkan teks yang dapat diverifikasi. Inventaris ini memisahkan requirement proyek pada laporan utama (halaman 9–22) dari perkembangan desain pada Dokumen Teknis (halaman 38–175).

## A. Documented Initial/Project Requirement

| Documented Req ID | Bagian Laporan | Role | Requirement | Evidence | Halaman | Confidence |
|---|---|---|---|---|---:|---|
| DOC-REQ-001 | Latar Belakang; Maksud dan Tujuan | Semua | Sistem harus berupa aplikasi web terpusat yang mengintegrasikan LMS dan SIA untuk operasional SMP dan SMA. | Kebutuhan integrasi LMS–SIA dan dua jenjang dinyatakan eksplisit. | 9–10 | HIGH |
| DOC-REQ-002 | Spesifikasi; Alur Sistem | Admin, Guru, Wali Kelas, Siswa | Sistem harus menyediakan satu login multi-peran, mengarahkan pengguna ke dashboard perannya, dan memungkinkan logout. | Alur login multi-peran; diagram Login dan Logout pada Dokumen Teknis. | 20; 52; 93 | HIGH |
| DOC-REQ-003 | Sasaran; Modul 1 | Admin | Admin harus dapat membuat dan mengelola akun/data Guru Pengajar, Wali Kelas, dan Siswa. | Sasaran Admin dan form manajemen akun guru/siswa. | 11; 14 | HIGH |
| DOC-REQ-004 | Sasaran; Modul 9 | Admin | Admin harus dapat mengelola profil akademik siswa. | Pengelolaan profil siswa terintegrasi dengan form akun siswa. | 11; 16 | HIGH |
| DOC-REQ-005 | Sasaran; Modul 1; Alur Sistem | Admin | Admin harus dapat mengelola tahun ajaran, kelas, penempatan siswa, wali kelas, serta penugasan Guru Pengajar per kelas/mapel. | Sasaran Admin dan pengaturan struktur akademik. | 11; 14; 20 | HIGH |
| DOC-REQ-006 | Modul 2; Spesifikasi | Admin, Wali Kelas, Guru, Siswa | Admin mengatur jadwal; Wali/Siswa melihat jadwal kelas; Guru melihat jadwal dan kelas/mapel yang diampu. | Pengaturan dan tampilan jadwal berdasarkan aktor. | 14; 17–20 | HIGH |
| DOC-REQ-007 | Sasaran; Modul 8 | Admin | Admin harus dapat membuat dan mengelola tagihan siswa untuk SPP, ujian, dan kegiatan. | Sasaran Admin dan tampilan tagihan. | 11; 15–16 | HIGH |
| DOC-REQ-008 | Sasaran; Modul 8 | Admin, Siswa | Siswa mengunggah bukti pembayaran manual dan Admin mengonfirmasi atau menolaknya. | Form unggah bukti dan halaman verifikasi Admin. | 11; 16; 18–20 | HIGH |
| DOC-REQ-009 | Sasaran; Modul 10 | Admin, Wali Kelas | Admin memonitor pengguna aktif/history akses; Wali Kelas memonitor siswa aktif dalam kelasnya. | Sasaran dan Modul Monitoring Aktivitas. | 11–12; 16; 18 | HIGH |
| DOC-REQ-010 | Tujuan; Modul 3 | Guru | Guru harus dapat membuat, mengubah, menghapus, dan mengunggah materi pada kelas yang diampu. | Platform materi terpusat dan Form Materi baru/edit. | 10; 12; 14 | HIGH |
| DOC-REQ-011 | Sasaran Guru | Guru | Materi/catatan tambahan dapat memakai foto, PDF, Word, Excel, PPT, atau URL video. | Format materi tambahan disebut eksplisit. | 12; 19 | HIGH |
| DOC-REQ-012 | Tujuan; Modul 3 | Guru, Siswa | Sistem harus menyediakan forum diskusi materi untuk tanya jawab Guru dan Siswa. | Forum diskusi pada tujuan, fitur, dan alur pembelajaran. | 10; 12; 14; 20 | HIGH |
| DOC-REQ-013 | Sasaran; Modul 4 | Guru | Guru harus dapat mengelola latihan, tugas, dan ulangan beserta soal/file dan tenggat. | Form penugasan dan alur pengelolaan konten Guru. | 12; 14; 20 | HIGH |
| DOC-REQ-014 | Sasaran Guru | Guru, Siswa | Latihan harus mendukung pilihan ganda kompleks dan benar/salah dengan koreksi otomatis. | Tipe latihan dan koreksi otomatis disebut eksplisit. | 12; 19 | HIGH |
| DOC-REQ-015 | Sasaran; Modul 4 | Guru, Siswa | Guru mengelola ujian PTS/PAS berakses terbatas dan Siswa yang berhak mengerjakannya. | Forum ujian semester dan form Ujian Tengah/Akhir Semester. | 12; 15; 19 | HIGH |
| DOC-REQ-016 | Sasaran; Modul 4 | Siswa | Siswa harus dapat mengunggah jawaban tugas/ujian dalam format yang didukung sebelum tenggat. | Form unggah jawaban dan daftar format file/URL. | 13; 15; 19; 21 | HIGH |
| DOC-REQ-017 | Sasaran Guru; Modul 6 | Guru | Guru harus dapat menilai penugasan dan ujian serta memberi umpan balik; ujian semester memiliki komponen tertulis dan proyek. | Pengelolaan nilai dan alur pemberian nilai/umpan balik. | 12; 15; 21 | HIGH |
| DOC-REQ-018 | Sasaran; Modul 6 | Guru | Guru harus dapat melihat rekap nilai mata pelajaran yang diampu selama tahun ajaran. | Rekap nilai Guru dinyatakan eksplisit. | 13; 15; 21 | HIGH |
| DOC-REQ-019 | Tujuan; Modul 5; Alur Sistem | Sistem, Siswa | Sistem harus mencatat kehadiran otomatis berdasarkan aktivitas/akses LMS Siswa. | Presensi otomatis disebut sebagai tujuan, fitur latar belakang, dan alur akses materi. | 10; 15; 20 | HIGH |
| DOC-REQ-020 | Tujuan; Modul 5 | Siswa, Wali Kelas | Siswa mengunggah bukti izin/sakit dan Wali Kelas memvalidasinya secara online. | Aktor dan workflow dinyatakan berulang. | 10–11; 13; 15; 19; 21 | HIGH |
| DOC-REQ-021 | Sasaran; Modul 5 | Wali Kelas | Wali Kelas harus dapat menginput atau memperbarui presensi manual siswa. | Absensi manual untuk siswa offline. | 11; 15; 18; 21 | HIGH |
| DOC-REQ-022 | Tujuan; Modul 5 | Wali Kelas, Siswa | Sistem harus menyediakan rekap/riwayat presensi untuk Wali Kelas dan Siswa terkait. | Rekap kehadiran dan tampilan rekap absensi. | 10–11; 13; 15 | HIGH |
| DOC-REQ-023 | Tujuan; Modul 6 | Wali Kelas | Wali Kelas harus dapat menarik rekap nilai semua Guru, menyesuaikan, dan memfinalisasi nilai akhir. | Rekap terintegrasi dan penyesuaian/finalisasi manual. | 10–12; 15; 18; 21 | HIGH |
| DOC-REQ-024 | Detail Laporan Nilai | Sistem, Guru, Wali Kelas | Sistem harus menghitung nilai akhir dengan bobot Tugas 1, Latihan 1, Ulangan 2, PTS/UTS 3, dan PAS/UAS 3, dibagi 10. | Rumus dicantumkan dua kali. | 12; 18 | HIGH |
| DOC-REQ-025 | Detail Laporan Nilai; Modul 6 | Wali Kelas | Laporan nilai harus dapat diekspor ke Excel/PDF dengan pengelompokan mata pelajaran. | Laporan satu file PDF/Excel; spesifikasi berikutnya menyebut satu file Excel bertab mapel. | 12; 15; 18 | MEDIUM |
| DOC-REQ-026 | Sasaran; Alur Sistem | Wali Kelas | Wali Kelas harus dapat mengunggah rapor final berbentuk PDF setelah PTS/PAS. | Unggah rapor PDF disebut pada sasaran dan alur. | 11; 18; 21 | HIGH |
| DOC-REQ-027 | Tujuan; Sasaran Siswa | Siswa | Siswa harus dapat melihat progres belajar, rekap absensi, dan nilai harian miliknya. | Informasi terpusat dan akses data pribadi. | 10; 13; 19; 21 | HIGH |
| DOC-REQ-028 | Sasaran; Modul 6 | Siswa | Siswa harus dapat melihat rapor final hanya pada jadwal pengambilan rapor yang ditentukan. | Pembatasan waktu akses rapor dinyatakan eksplisit. | 13; 15; 19 | HIGH |
| DOC-REQ-029 | Tujuan; Modul 7 | Guru, Siswa, Sistem | Sistem harus mengirim notifikasi tugas/latihan/ulangan baru, pengumpulan jawaban, dan tenggat. | Trigger untuk Guru dan Siswa serta deadline. | 10; 15; 21 | HIGH |
| DOC-REQ-030 | Sasaran Siswa; Modul 8 | Siswa | Siswa harus dapat melihat tagihan, mengunggah bukti, dan melihat status/riwayat pembayaran. | Aktor pembayaran pada laporan utama adalah Siswa. | 13; 16; 19–20 | HIGH |
| DOC-REQ-031 | Tujuan; Modul 8; Teknologi | Siswa, Sistem | Sistem harus mendukung Midtrans dan memperbarui status otomatis ketika transaksi berhasil. | Midtrans, kanal digital, dan verifikasi otomatis disebut eksplisit. | 11; 16; 22 | HIGH |
| DOC-REQ-032 | Sasaran; Modul 9 | Siswa | Siswa harus dapat melihat profil akademik pribadinya secara read-only. | Tampilan profil personal dan read-only. | 13; 16; 20 | HIGH |

## B. Design / Documented Specification Additions

Requirement berikut muncul pada Dokumen Teknis dan menunjukkan rancangan yang lebih luas daripada empat aktor pada laporan utama. Keberadaannya membuktikan documented specification, tetapi tidak otomatis membuktikan requirement tersebut disetujui sebelum development.

| Documented Req ID | Bagian Laporan | Role | Requirement | Evidence | Halaman | Confidence |
|---|---|---|---|---|---:|---|
| DOC-REQ-033 | Dokumen Teknis — Use Case | Admin | Sistem harus menyediakan pengelolaan data cabang. | Activity dan sequence diagram Kelola Data Cabang. | 54; 96 | HIGH |
| DOC-REQ-034 | Dokumen Teknis — Use Case/DB | Admin | Sistem harus menyediakan pengaturan sistem, termasuk flag akses LMS. | Diagram Kelola Pengaturan Sistem; `app_settings` memberi contoh aktif/nonaktif LMS. | 59; 101; 174 | HIGH |
| DOC-REQ-035 | Dokumen Teknis — Use Case/DB | Admin | Sistem harus menyediakan CMS landing page beserta section dan visibilitasnya. | Diagram Kelola Landing Page dan tabel landing page/section. | 60; 102; 170–171 | HIGH |
| DOC-REQ-036 | Dokumen Teknis — Use Case/DB | Guest, Admin | Sistem harus menyediakan pemulihan akun dan pengelolaan tiket recovery. | Diagram tiket recovery dan tabel `recovery_tickets`. | 61; 102; 174 | HIGH |
| DOC-REQ-037 | Dokumen Teknis — Use Case/DB | Admin, Waka, Wali Kelas, Bendahara, Ketua | Sistem harus mendukung pengaturan, prediksi, dispensasi, keputusan, penjadwalan, eksekusi, dan rollback kenaikan kelas. | Rangkaian diagram kenaikan; tabel status, schedule, setting, dan dispensasi. | 62–63; 81; 85; 103–104; 122; 125; 153–155 | HIGH |
| DOC-REQ-038 | Dokumen Teknis — Use Case | Admin, Ketua, Waka | Sistem harus menyediakan monitoring pengguna dan konten LMS sesuai lingkup aktor. | Diagram Monitoring Sistem serta Kelola Laporan/Catatan Monitoring. | 64; 80; 105; 121 | HIGH |
| DOC-REQ-039 | Dokumen Teknis — Use Case/DB | Admin, Sekretaris | Sistem harus menyediakan pengelolaan kalender, pengumuman, berita, dan flyer. | Diagram Kelola Konten & Publikasi; tabel konten. | 65; 106; 168–170 | HIGH |
| DOC-REQ-040 | Dokumen Teknis — Use Case/DB | Admin, Bendahara | Sistem harus menyediakan laporan keuangan dan audit perubahan transaksi. | Diagram Laporan Keuangan dan `financial_audit_logs`. | 67; 108; 167 | HIGH |
| DOC-REQ-041 | Dokumen Teknis — Use Case/DB | Admin, Bendahara, Wali Kelas | Sistem harus memvalidasi akses ujian dan rapor berdasarkan status yang berwenang. | Diagram Validasi Akses; kolom validasi pada siswa; batas pembayaran. | 68; 109; 138; 166–167 | HIGH |
| DOC-REQ-042 | Dokumen Teknis — Use Case | Ketua, Admin/Bendahara | Sistem harus mendukung dispensasi keuangan. | Diagram Dispensasi Keuangan. | 69; 110 | HIGH |
| DOC-REQ-043 | Dokumen Teknis — Use Case/DB | Guru, Siswa | Sistem harus menyediakan kelas virtual per kelas dan mata pelajaran. | Diagram Kelola/Mengikuti Kelas Virtual; tabel meeting/pertemuan. | 73; 90; 114; 130; 155 | HIGH |
| DOC-REQ-044 | Dokumen Teknis — Use Case/DB | Admin, Ketua, Waka, Guru | Pimpinan harus dapat mengirim catatan monitoring dan Guru membacanya. | Diagram catatan monitoring serta tabel `catatan_monitoring`. | 80; 83; 121; 123; 173 | HIGH |
| DOC-REQ-045 | Dokumen Teknis — Use Case | Wali Kelas | Wali Kelas harus dapat melihat prediksi kenaikan kelas siswa perwaliannya. | Diagram Melihat Prediksi Kenaikan Kelas. | 81; 122 | HIGH |
| DOC-REQ-046 | Dokumen Teknis — Use Case | Orang Tua, Wali Kelas | Orang Tua meminta unduh rapor dan Wali Kelas memutuskan permintaan tersebut. | Diagram Mengelola/Meminta Unduh Rapor. | 82; 88; 123; 128 | HIGH |
| DOC-REQ-047 | Dokumen Teknis — Use Case | Ketua PKBM | Ketua harus dapat memvalidasi rapor tingkat akhir. | Diagram Validasi Rapor Tingkat Akhir. | 84; 124 | HIGH |
| DOC-REQ-048 | Dokumen Teknis — Use Case/DB | Ketua, Bendahara, Wali Kelas | Sistem harus mendukung dispensasi kenaikan kelas bagi siswa yang belum memenuhi syarat. | Diagram dan tabel dispensasi kenaikan. | 85; 125; 154–155 | HIGH |
| DOC-REQ-049 | Dokumen Teknis — DB | Orang Tua/Wali Siswa | Sistem harus menautkan akun Orang Tua ke satu/lebih Siswa dengan atribut tanggung jawab keuangan dan akses akademik. | Tabel `student_parents`. | 139 | HIGH |
| DOC-REQ-050 | Dokumen Teknis — Use Case/DB | Orang Tua/Wali Siswa | Orang Tua/Wali Siswa dapat melakukan pembayaran anak melalui manual/Midtrans dan tercatat sebagai pembayar. | Diagram Melakukan Pembayaran; `paid_by_parent_id`; konfigurasi Midtrans. | 86; 126; 165–166 | HIGH |
| DOC-REQ-051 | Dokumen Teknis — Use Case | Orang Tua/Wali Siswa | Orang Tua/Wali Siswa dapat mengajukan izin/dispensasi untuk anak. | Diagram Mengajukan Izin/Dispensasi. | 87; 127 | MEDIUM |
| DOC-REQ-052 | Dokumen Teknis — DB | Semua role | Desain final mengenal sembilan role: Admin, Ketua, Waka, Sekretaris, Bendahara, Wali Kelas, Guru, Siswa, dan Orang Tua. | Master sembilan peran dan enum role pada `users`. | 135 | HIGH |
| DOC-REQ-053 | Dokumen Teknis — DB | Semua login | Sistem harus menyimpan notifikasi per pengguna dan status waktu bacanya. | Tabel `notifications` dan atribut `read_at`. | 171–172 | HIGH |
| DOC-REQ-054 | Dokumen Teknis — DB | Publik, Admin, Sekretaris, Siswa | Sistem harus menyajikan konten publik/internal dengan status, periode, audience, dan visibilitas yang sesuai. | Struktur berita, flyer, pengumuman, kalender, landing page. | 168–171 | HIGH |
| DOC-REQ-055 | Dokumen Teknis — Use Case/DB | Admin, Waka | Sistem harus menyediakan pengelolaan mata pelajaran sebagai dasar jadwal dan ruang pembelajaran. | Diagram Kelola Mata Pelajaran dan tabel `mata_pelajaran`. | 57; 99; 140 | HIGH |
| DOC-REQ-056 | Dokumen Teknis — DB | Admin, Bendahara | Sistem harus mempertahankan jejak pengalihan tunggakan antartahun ajaran. | Kolom `tagihan_asal_id`, `dialihkan_ke_id`, dan uraian carryover pada tabel tagihan. | 165 | MEDIUM |

## C. Historical Evidence Interpretation

- **Laporan utama halaman 9–22** menjadi bukti terkuat untuk documented initial/project requirement. Aktornya terutama Admin, Wali Kelas, Guru Pengajar, dan Siswa.
- **Dokumen Teknis halaman 38–175** menggambarkan spesifikasi desain yang lebih dekat dengan aplikasi final: 43 use case, 58 tabel yang didokumentasikan, sembilan role, Orang Tua sebagai pembayar, kenaikan kelas multi-role, publikasi, recovery, dan monitoring.
- Laporan menyebut pengujian/umpan balik dan iterasi pada halaman 17 serta tugas SDM untuk riset, wawancara, survei, dan pelaporan kepada instansi pada halaman 25. Namun, tidak tersedia log perubahan bertanggal yang menghubungkan setiap perubahan dengan permintaan stakeholder tertentu.
- Karena itu, requirement pada Section B boleh disebut **documented design evolution**, tetapi tidak boleh ditulis sebagai persetujuan historis pra-development tanpa bukti tambahan.

## D. Internal Inconsistencies in the Report

1. Halaman 12 menyebut laporan nilai satu file PDF/Excel dan "3 jenjang", sedangkan halaman 18 menyebut satu file Excel dan "2 jenjang" (SMP dan SMA, masing-masing tingkat 1–3).
2. Laporan utama menetapkan pembayaran, izin, dan akses rapor kepada Siswa; Dokumen Teknis kemudian memuat role Orang Tua serta `paid_by_parent_id` dan relasi `student_parents`.
3. Laporan utama menetapkan Wali Kelas mengunggah rapor PDF; desain/implementasi final menghasilkan dan mengelola rapor terstruktur.
4. Laporan menyebut presensi otomatis berdasarkan aktivitas LMS; source final mempunyai method `presensiOtomatis()` tetapi tidak ditemukan route aktif yang memanggilnya.
5. Halaman 22 menyebut Laravel Breeze/Jetstream; repository final menggunakan autentikasi kustom berbasis Laravel session/middleware dan tidak menunjukkan dependency Breeze/Jetstream sebagai entry point aktif.

**Documented requirement count: 56.**
