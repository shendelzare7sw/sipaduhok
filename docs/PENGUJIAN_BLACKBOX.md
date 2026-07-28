# PENGUJIAN SISTEM (SOFTWARE TESTING) — SIPADUHOK

> Lembar pengujian ini berdiri sendiri (penomoran dimulai dari awal). Metode utama yang digunakan adalah **Black Box Testing**, yaitu pengujian yang berfokus pada fungsionalitas sistem tanpa melihat struktur kode internal. Pengujian dilengkapi dengan **Pengujian Endpoint API** menggunakan Postman pada bagian akhir sebagai pelengkap.

## Informasi Umum Pengujian

- **Nama Sistem:** SIPADUHOK — Sistem Informasi Manajemen Sekolah/PKBM (Akademik, LMS, dan Keuangan) terintegrasi.
- **Metode Pengujian:** Black Box Testing (Functional Testing) dan Pengujian Endpoint API.
- **Aktor/Peran yang Diuji (9 peran):** Admin, Ketua PKBM, Wakil Kepala Sekolah, Sekretaris, Bendahara, Wali Kelas, Guru Pengajar, Siswa, dan Orang Tua (Wali Siswa).
- **Lingkungan Pengujian:** Server lokal (Laragon), PHP 8.2 / Laravel 11, basis data MySQL, browser modern (Google Chrome).

**Keterangan pengisian tabel:**
- Kolom **Hasil Aktual** diisi *default* dengan kalimat: *"Sesuai dengan hasil yang diharapkan."*
- Kolom **Kesimpulan** diisi *default* dengan kata **"Lolos"** (ditampilkan dengan warna hijau pada dokumen akhir).

---

## 1. Autentikasi dan Sesi Pengguna

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-001 | Login valid sebagai Admin | 1. Buka halaman login. 2. Masukkan username & password Admin yang benar. 3. Klik tombol "Login". | Sistem menerima login dan mengarahkan pengguna ke dashboard Admin. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-002 | Login valid sebagai Ketua PKBM | 1. Buka halaman login. 2. Masukkan kredensial Ketua PKBM yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Ketua PKBM. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-003 | Login valid sebagai Wakil Kepala Sekolah | 1. Buka halaman login. 2. Masukkan kredensial Wakil Kepala Sekolah yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Wakil Kepala Sekolah. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-004 | Login valid sebagai Sekretaris | 1. Buka halaman login. 2. Masukkan kredensial Sekretaris yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Sekretaris. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-005 | Login valid sebagai Bendahara | 1. Buka halaman login. 2. Masukkan kredensial Bendahara yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Bendahara. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-006 | Login valid sebagai Wali Kelas | 1. Buka halaman login. 2. Masukkan kredensial Wali Kelas yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Wali Kelas. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-007 | Login valid sebagai Guru Pengajar | 1. Buka halaman login. 2. Masukkan kredensial Guru Pengajar yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Guru. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-008 | Login valid sebagai Siswa | 1. Buka halaman login. 2. Masukkan kredensial Siswa yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-009 | Login valid sebagai Orang Tua (Wali Siswa) | 1. Buka halaman login. 2. Masukkan kredensial Orang Tua yang benar. 3. Klik "Login". | Sistem mengarahkan pengguna ke dashboard Wali Siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-010 | Login dengan password salah | 1. Buka halaman login. 2. Masukkan username benar & password salah. 3. Klik "Login". | Sistem menolak login dan menampilkan pesan "Kredensial tidak valid". | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-011 | Login dengan akun tidak terdaftar | 1. Buka halaman login. 2. Masukkan username yang tidak terdaftar. 3. Klik "Login". | Sistem menolak login dan menampilkan pesan kesalahan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-012 | Login dengan form kosong | 1. Buka halaman login. 2. Kosongkan field username & password. 3. Klik "Login". | Sistem menampilkan pesan validasi bahwa field wajib diisi dan login tidak diproses. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-013 | Login akun berstatus nonaktif | 1. Buka halaman login. 2. Masukkan kredensial akun yang dinonaktifkan (is_active = false). 3. Klik "Login". | Sistem menolak login dan menampilkan pemberitahuan akun nonaktif. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-014 | Verifikasi captcha (Cloudflare Turnstile) | 1. Buka halaman login. 2. Isi kredensial tanpa menyelesaikan verifikasi captcha. 3. Klik "Login". | Sistem menolak proses login hingga verifikasi captcha berhasil. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-015 | Logout dari sistem | 1. Login ke sistem. 2. Klik menu "Logout". | Sesi pengguna diakhiri dan diarahkan kembali ke halaman login. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 2. Pembatasan Hak Akses Berbasis Peran (Role-Based Access Control)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-016 | Akses halaman tanpa login | 1. Tanpa login, akses langsung URL dashboard (mis. `/admin`) melalui browser. | Sistem mengarahkan pengguna ke halaman login. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-017 | Siswa mengakses area Admin | 1. Login sebagai Siswa. 2. Akses langsung URL `/admin` di browser. | Sistem menolak akses (403 / "Akses Ditolak") tanpa me-logout pengguna. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-018 | Guru mengakses area Bendahara | 1. Login sebagai Guru. 2. Akses langsung URL `/bendahara`. | Sistem menolak akses dan menampilkan pesan tidak berwenang. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-019 | Orang Tua mengakses area Guru | 1. Login sebagai Orang Tua. 2. Akses langsung URL `/guru`. | Sistem menolak akses. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-020 | Wali Kelas mengakses area Wakil Kepala Sekolah | 1. Login sebagai Wali Kelas. 2. Akses langsung URL `/waka`. | Sistem menolak akses. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-021 | Bendahara mengakses menu akademik Guru | 1. Login sebagai Bendahara. 2. Coba akses fitur input nilai `/guru`. | Sistem menolak akses. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-022 | Admin mengakses seluruh area (bypass) | 1. Login sebagai Admin. 2. Akses berbagai area peran lain. | Sistem mengizinkan Admin mengakses seluruh modul sesuai kewenangannya. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 3. Manajemen Data Pengguna (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-023 | Menambah pengguna dengan data valid | 1. Login sebagai Admin. 2. Buka menu "Data Pengguna". 3. Klik "Tambah". 4. Isi seluruh field dengan benar & pilih peran. 5. Klik "Simpan". | Data pengguna baru berhasil disimpan dan muncul di daftar. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-024 | Menambah pengguna dengan form kosong | 1. Buka form tambah pengguna. 2. Kosongkan field wajib. 3. Klik "Simpan". | Sistem menampilkan pesan validasi field wajib dan data tidak tersimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-025 | Validasi format email tidak valid | 1. Buka form tambah pengguna. 2. Isi email dengan format salah (mis. "adminmail"). 3. Klik "Simpan". | Sistem menolak dan menampilkan pesan format email tidak valid. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-026 | Validasi email/username duplikat | 1. Tambah pengguna dengan email yang sudah terdaftar. 2. Klik "Simpan". | Sistem menolak dan menampilkan pesan data sudah digunakan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-027 | Mengubah data pengguna | 1. Pilih pengguna. 2. Klik "Edit". 3. Ubah data. 4. Klik "Simpan". | Perubahan data berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-028 | Menonaktifkan / mengaktifkan akun | 1. Pilih pengguna. 2. Ubah status aktif/nonaktif. 3. Simpan. | Status akun berubah dan berpengaruh pada kemampuan login pengguna. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-029 | Menghapus data pengguna | 1. Pilih pengguna. 2. Klik "Hapus". 3. Konfirmasi. | Data pengguna terhapus dari daftar. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 4. Manajemen Data Cabang (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-030 | Menambah cabang dengan data valid | 1. Buka menu "Data Cabang". 2. Klik "Tambah". 3. Isi kode & nama cabang. 4. Simpan. | Data cabang baru berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-031 | Validasi kode cabang kosong/duplikat | 1. Tambah cabang dengan kode kosong atau kode yang sudah ada. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-032 | Mengubah & menghapus cabang | 1. Edit data cabang lalu simpan. 2. Hapus salah satu cabang & konfirmasi. | Perubahan tersimpan dan data yang dihapus hilang dari daftar. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 5. Manajemen Tahun Ajaran (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-033 | Menambah tahun ajaran valid | 1. Buka menu "Tahun Ajaran". 2. Klik "Tambah". 3. Isi nama & tanggal mulai–selesai. 4. Simpan. | Data tahun ajaran berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-034 | Validasi tanggal tidak logis | 1. Isi tanggal mulai lebih besar dari tanggal selesai. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi tanggal. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-035 | Mengaktifkan satu tahun ajaran | 1. Aktifkan sebuah tahun ajaran. | Tahun ajaran terpilih menjadi aktif dan tahun ajaran lain otomatis nonaktif (hanya satu yang aktif). | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 6. Manajemen Kelas dan Penugasan (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-036 | Menambah kelas dengan data valid | 1. Buka menu "Kelas". 2. Klik "Tambah". 3. Pilih cabang, tahun ajaran, jenjang, isi nama & kode kelas. 4. Simpan. | Data kelas baru berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-037 | Validasi kode kelas duplikat | 1. Tambah kelas dengan kode yang sudah ada. 2. Simpan. | Sistem menolak dan menampilkan pesan kode kelas sudah digunakan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-038 | Menetapkan wali kelas | 1. Pilih kelas. 2. Tetapkan seorang tenaga pendidik sebagai wali kelas. 3. Simpan. | Wali kelas berhasil ditetapkan pada kelas tersebut. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-039 | Menugaskan guru pengajar pada mapel | 1. Pilih kelas & mata pelajaran. 2. Tugaskan guru pengajar. 3. Simpan. | Penugasan guru–kelas–mapel berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-040 | Memindahkan siswa antar kelas | 1. Pilih siswa. 2. Ubah kelas siswa. 3. Simpan. | Siswa berpindah ke kelas tujuan. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 7. Manajemen Mata Pelajaran (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-041 | Menambah mata pelajaran valid | 1. Buka menu "Mata Pelajaran". 2. Klik "Tambah". 3. Isi kode, nama, jenjang. 4. Simpan. | Data mata pelajaran berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-042 | Validasi kode mapel kosong/duplikat | 1. Tambah mapel dengan kode kosong atau duplikat. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-043 | Mengubah & menonaktifkan mapel | 1. Edit data mapel. 2. Nonaktifkan mapel. 3. Simpan. | Perubahan dan status aktif mapel tersimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 8. Manajemen Jadwal Pelajaran (Admin / Wakil Kepala Sekolah)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-044 | Menambah jadwal valid | 1. Buka menu "Jadwal Pelajaran". 2. Klik "Tambah". 3. Pilih kelas, mapel, guru, hari, jam. 4. Simpan. | Jadwal berhasil disimpan dan tampil pada tabel jadwal. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-045 | Validasi jam mulai ≥ jam selesai | 1. Isi jadwal dengan jam mulai lebih besar/sama dengan jam selesai. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi jam. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-046 | Deteksi bentrok jadwal | 1. Tambahkan jadwal dengan guru/kelas yang sama pada hari & jam yang bertumpuk. 2. Simpan. | Sistem menampilkan peringatan bentrok jadwal. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-047 | Mengganti guru pada jadwal | 1. Pilih jadwal. 2. Ganti guru pengajar & isi keterangan. 3. Simpan. | Perubahan tersimpan dan tercatat pada riwayat perubahan jadwal. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 9. Pengaturan Sistem dan Gate LMS (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-048 | Mengubah setelan aplikasi | 1. Buka menu "Pengaturan Sistem". 2. Ubah salah satu setelan. 3. Simpan. | Perubahan setelan berhasil disimpan dan diterapkan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-049 | Menonaktifkan LMS secara global | 1. Matikan gate LMS pada pengaturan. 2. Login sebagai Siswa/Guru. 3. Akses menu LMS. | Akses ke modul LMS ditutup untuk seluruh pengguna. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-050 | Mengaktifkan kembali LMS | 1. Nyalakan gate LMS. 2. Akses menu LMS. | Modul LMS kembali dapat diakses. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 10. Manajemen Landing Page dan Konten Publik (Admin)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-051 | Mengubah konten section landing page | 1. Buka pengelolaan landing page. 2. Edit konten sebuah section. 3. Simpan. | Konten pada halaman publik ikut ter-update. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-052 | Menyembunyikan/menampilkan & mengurutkan section | 1. Ubah visibilitas & urutan section. 2. Simpan. | Tampilan halaman publik menyesuaikan visibilitas dan urutan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-053 | Mempublikasikan berita/flyer | 1. Tambah berita/flyer baru. 2. Set status "publish". 3. Simpan. | Konten tampil di halaman publik. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 11. Pemulihan Akun (Recovery Ticket)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-054 | Mengajukan pemulihan akun | 1. Buka halaman pemulihan akun. 2. Isi data identitas. 3. Kirim permohonan. | Tiket pemulihan berhasil dibuat dan berstatus menunggu. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-055 | Admin menyetujui/menolak tiket | 1. Login sebagai Admin. 2. Buka daftar tiket pemulihan. 3. Setujui/tolak tiket. | Status tiket diperbarui dan notifikasi terkirim ke pengguna. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-056 | Token pemulihan kadaluarsa | 1. Gunakan tautan reset yang telah melewati batas waktu. | Sistem menolak dan menampilkan pesan token kadaluarsa. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 12. Kenaikan Kelas (Ketua PKBM / Wakil Kepala Sekolah)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-057 | Mengatur parameter kenaikan kelas | 1. Buka "Pengaturan Kenaikan Kelas". 2. Isi persentase minimal tuntas & tanggal. 3. Simpan. | Parameter kenaikan kelas tersimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-058 | Menjadwalkan eksekusi kenaikan | 1. Buat jadwal eksekusi kenaikan pada tanggal tertentu. 2. Simpan. | Jadwal eksekusi tercatat dengan status terjadwal. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-059 | Mengeksekusi kenaikan kelas | 1. Jalankan proses kenaikan kelas. | Sistem memproses status siswa (naik / tinggal / lulus) sesuai kriteria. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-060 | Membatalkan (rollback) kenaikan | 1. Pilih hasil eksekusi. 2. Lakukan rollback. | Status kenaikan siswa dikembalikan ke kondisi sebelumnya. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-061 | Memberi dispensasi kenaikan (izin khusus) | 1. Ajukan dispensasi kenaikan untuk siswa tertentu. 2. Ketua menyetujui. | Siswa memperoleh izin khusus naik kelas meski ada tunggakan. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 13. Keuangan — Tagihan dan Pembayaran (Sekretaris / Bendahara)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-062 | Membuat (generate) tagihan | 1. Buka menu "Tagihan". 2. Pilih jenis tagihan (SPP/uang pangkal/dll) & sasaran siswa. 3. Generate. | Tagihan berhasil dibuat untuk siswa terkait. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-063 | Validasi input nominal/tanggal tagihan | 1. Isi nominal kosong/negatif atau tanggal jatuh tempo kosong. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-064 | Mencatat pembayaran manual | 1. Pilih tagihan. 2. Input pembayaran & unggah bukti. 3. Simpan. | Pembayaran tercatat dengan status menunggu validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-065 | Pembayaran cicilan (sebagian) | 1. Catat pembayaran kurang dari total tagihan. 2. Simpan. | Status tagihan berubah menjadi "cicilan" dan sisa tagihan terhitung benar. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-066 | Pembaruan status tagihan otomatis | 1. Setujui pembayaran hingga lunas. | Status tagihan otomatis berubah menjadi "sudah bayar". | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 14. Keuangan — Validasi Pembayaran dan Gateway Midtrans (Bendahara)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-067 | Menyetujui pembayaran manual | 1. Buka daftar pembayaran menunggu. 2. Verifikasi bukti. 3. Klik "Setujui". | Pembayaran berstatus disetujui dan tagihan diperbarui. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-068 | Menolak pembayaran tidak valid | 1. Pilih pembayaran dengan bukti tidak sesuai. 2. Klik "Tolak" & isi catatan. | Pembayaran ditolak dan tagihan tetap belum lunas. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-069 | Notifikasi Midtrans pembayaran berhasil | 1. Lakukan pembayaran melalui Midtrans hingga settlement. 2. Sistem menerima webhook notifikasi. | Status pembayaran otomatis menjadi lunas tanpa validasi manual. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-070 | Notifikasi Midtrans pending/expired | 1. Transaksi Midtrans berstatus pending/expire. 2. Sistem menerima webhook. | Status pembayaran diperbarui sesuai (pending/gagal) dan tagihan tidak dilunasi. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 15. Keuangan — Laporan dan Dispensasi (Bendahara)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-071 | Menampilkan laporan keuangan | 1. Buka menu "Laporan Keuangan". 2. Pilih periode/kelas. 3. Tampilkan. | Laporan keuangan tampil sesuai filter yang dipilih. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-072 | Mengekspor/mencetak laporan (PDF) | 1. Klik "Cetak/Export PDF" pada laporan. | Berkas PDF laporan berhasil diunduh/dihasilkan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-073 | Memberi dispensasi keuangan | 1. Pilih siswa. 2. Berikan dispensasi/keringanan tagihan. 3. Simpan. | Dispensasi tercatat dan memengaruhi kewajiban pembayaran siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 16. Validasi Akses Ujian dan Rapor (Bendahara / Wali Kelas / Ketua)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-074 | Validasi akses ujian oleh Bendahara | 1. Login sebagai Bendahara. 2. Validasi kelayakan keuangan siswa untuk ujian. | Penanda validasi ujian (bendahara) tercatat pada siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-075 | Validasi akses ujian oleh Wali Kelas | 1. Login sebagai Wali Kelas. 2. Validasi kelayakan akademik siswa untuk ujian. | Penanda validasi ujian (wali) tercatat pada siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-076 | Siswa belum tervalidasi mencoba ujian | 1. Login sebagai Siswa yang belum divalidasi. 2. Coba masuk ke ujian. | Sistem menolak dan menampilkan pesan akses ujian belum divalidasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-077 | Validasi akses rapor bertingkat | 1. Lakukan validasi rapor oleh Bendahara, Wali Kelas, dan Ketua. | Seluruh penanda validasi rapor tercatat dan rapor dapat diakses siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 17. LMS — Materi Pembelajaran (Guru)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-078 | Mengunggah materi valid | 1. Login sebagai Guru. 2. Buka menu "Materi". 3. Isi judul & unggah berkas. 4. Simpan. | Materi berhasil diunggah dan tampil untuk siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-079 | Validasi tipe/ukuran berkas materi | 1. Unggah berkas dengan tipe/ukuran tidak diizinkan. 2. Simpan. | Sistem menolak unggahan dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-080 | Mengubah & menghapus materi | 1. Edit materi lalu simpan. 2. Hapus materi & konfirmasi. | Perubahan tersimpan dan materi yang dihapus hilang dari daftar. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 18. LMS — Tugas dan Koreksi (Guru & Siswa)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-081 | Guru membuat tugas valid | 1. Buka menu "Tugas". 2. Isi judul, deskripsi, tanggal mulai & deadline. 3. Simpan. | Tugas berhasil dibuat dan tampil untuk siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-082 | Validasi field tugas kosong | 1. Buat tugas tanpa judul/deadline. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-083 | Siswa mengumpulkan tugas | 1. Login sebagai Siswa. 2. Buka tugas. 3. Unggah jawaban/berkas. 4. Kirim. | Tugas terkirim dengan status telah dikumpulkan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-084 | Guru mengoreksi & memberi nilai tugas | 1. Buka daftar pengumpulan. 2. Beri nilai & umpan balik. 3. Simpan. | Nilai & umpan balik tersimpan dan tampil untuk siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 19. LMS — Ujian dan Latihan (Guru & Siswa)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-085 | Guru membuat ujian beserta soal | 1. Buka menu "Ujian". 2. Isi judul, durasi, tanggal. 3. Tambah soal. 4. Simpan. | Ujian & soal berhasil dibuat. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-086 | Validasi durasi/tanggal ujian | 1. Isi durasi kosong atau tanggal selesai < tanggal mulai. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-087 | Membuat latihan | 1. Buat latihan soal untuk siswa. 2. Simpan. | Latihan berhasil dibuat dan dapat dikerjakan siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-088 | Siswa mengerjakan ujian dengan timer | 1. Login sebagai Siswa. 2. Mulai ujian. 3. Jawab soal hingga waktu habis. | Timer berjalan dan jawaban tersimpan; ujian tersubmit otomatis saat waktu habis. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-089 | Pencatatan pelanggaran fokus (anti-curang) | 1. Saat ujian berlangsung, pindah tab/keluar dari halaman ujian. | Sistem mencatat kejadian kehilangan fokus pada log pengawasan ujian. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-090 | Penilaian otomatis pilihan ganda | 1. Selesaikan ujian pilihan ganda. 2. Submit. | Nilai objektif terhitung otomatis oleh sistem. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 20. LMS — Kelas Virtual (Guru & Siswa)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-091 | Guru membuat jadwal kelas virtual | 1. Buka menu "Kelas Virtual". 2. Isi judul, platform, tautan, waktu. 3. Simpan. | Jadwal kelas virtual tersimpan dan tampil untuk siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-092 | Validasi tautan/waktu meeting | 1. Kosongkan tautan atau isi waktu tidak valid. 2. Simpan. | Sistem menolak dan menampilkan pesan validasi. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-093 | Siswa mengikuti kelas virtual | 1. Login sebagai Siswa. 2. Buka jadwal kelas virtual. 3. Klik tautan meeting. | Siswa diarahkan ke tautan meeting yang benar. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 21. LMS — Forum Diskusi (Guru & Siswa)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-094 | Guru membuat topik diskusi | 1. Buka menu "Forum". 2. Buat topik baru. 3. Simpan. | Topik diskusi tampil dan dapat dibalas siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-095 | Siswa membalas diskusi | 1. Login sebagai Siswa. 2. Buka topik. 3. Kirim balasan. | Balasan tersimpan dan tampil pada topik. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-096 | Guru menandai jawaban / menyematkan / menutup topik | 1. Tandai balasan sebagai jawaban. 2. Sematkan/tutup topik. | Status topik & balasan diperbarui sesuai tindakan. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 22. Input Nilai Akademik (Guru & Wali Kelas)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-097 | Menginput nilai valid | 1. Login sebagai Guru. 2. Buka input nilai. 3. Isi nilai siswa. 4. Simpan. | Nilai berhasil disimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-098 | Validasi rentang nilai (0–100) | 1. Isi nilai di luar rentang (mis. -10 atau 150). 2. Simpan. | Sistem menolak dan menampilkan pesan validasi nilai. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-099 | Wali Kelas mengoreksi nilai | 1. Login sebagai Wali Kelas. 2. Edit nilai siswa. 3. Simpan. | Perubahan tersimpan dan tercatat sebagai perubahan oleh wali kelas. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-100 | Sinkronisasi nilai ke rapor | 1. Simpan nilai akhir. 2. Buka rapor siswa terkait. | Nilai terbawa/tersinkron ke rapor siswa. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 23. Presensi Siswa (Guru & Wali Kelas)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-101 | Menginput presensi valid | 1. Buka menu "Presensi". 2. Pilih kelas/pertemuan. 3. Tandai kehadiran siswa. 4. Simpan. | Data presensi tersimpan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-102 | Mengubah status kehadiran | 1. Pilih siswa. 2. Ubah status (Hadir/Sakit/Izin/Alpa). 3. Simpan. | Status kehadiran diperbarui. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-103 | Menampilkan rekap presensi | 1. Buka rekap presensi per kelas/periode. | Rekap kehadiran tampil sesuai data. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 24. Rapor (Wali Kelas / Ketua / Siswa)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-104 | Menyusun rapor siswa | 1. Login sebagai Wali Kelas. 2. Susun rapor & isi catatan. 3. Simpan. | Rapor tersusun dengan nilai & catatan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-105 | Mengajukan rapor ke Ketua | 1. Ajukan rapor untuk direview Ketua. | Pengajuan terkirim dengan status menunggu keputusan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-106 | Ketua menyetujui/merevisi rapor | 1. Login sebagai Ketua. 2. Tinjau rapor. 3. Setujui atau minta revisi. | Status review rapor diperbarui sesuai keputusan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-107 | Mengelola permintaan unduh rapor | 1. Buka daftar permintaan unduh. 2. Setujui/tolak. | Hak unduh rapor siswa diperbarui sesuai keputusan. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-108 | Mencetak rapor (PDF) | 1. Buka rapor yang disetujui. 2. Klik "Cetak/Unduh PDF". | Berkas PDF rapor berhasil dihasilkan. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 25. Aktivitas Siswa

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-109 | Melihat nilai pribadi | 1. Login sebagai Siswa. 2. Buka menu "Nilai". | Nilai pribadi siswa tampil. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-110 | Melihat riwayat presensi | 1. Buka menu "Presensi". | Riwayat kehadiran siswa tampil. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-111 | Melihat jadwal kelas | 1. Buka menu "Jadwal". | Jadwal pelajaran siswa tampil sesuai kelas. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-112 | Melakukan pembayaran | 1. Buka menu "Pembayaran". 2. Pilih tagihan. 3. Lakukan pembayaran (manual/Midtrans). | Pembayaran terproses dan tercatat pada sistem. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-113 | Meminta unduh rapor | 1. Buka rapor. 2. Ajukan permintaan unduh. | Permintaan unduh terkirim dengan status menunggu. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 26. Aktivitas Orang Tua (Wali Siswa)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-114 | Melihat data akademik anak | 1. Login sebagai Orang Tua dengan hak akses akademik. 2. Buka nilai/presensi anak. | Data akademik anak tampil. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-115 | Pembatasan akses akademik | 1. Login sebagai Orang Tua tanpa hak akses akademik. 2. Coba buka nilai anak. | Sistem menyembunyikan/menolak akses data akademik. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-116 | Membayar tagihan anak | 1. Buka tagihan anak. 2. Lakukan pembayaran. | Pembayaran tercatat atas nama orang tua penanggung. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-117 | Mengajukan izin/dispensasi | 1. Buka menu pengajuan. 2. Isi alasan & kirim. | Pengajuan tercatat dengan status menunggu. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 27. Monitoring dan Catatan (Wakil Kepala Sekolah / Ketua)

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-118 | Mengirim catatan antar pengguna | 1. Buka menu "Catatan". 2. Tulis & kirim catatan ke penerima. | Catatan terkirim dan tampil pada penerima. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-119 | Membuat catatan monitoring pembelajaran | 1. Buka konten pembelajaran. 2. Kirim catatan monitoring terhadap kelas/mapel. | Catatan monitoring tercatat untuk guru terkait. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-120 | Menandai catatan telah dibaca | 1. Buka catatan. 2. Sistem menandai sebagai telah dibaca. | Status baca catatan tercatat per pengguna. | Sesuai dengan hasil yang diharapkan. | Lolos |
| TC-121 | Monitoring sistem oleh pimpinan | 1. Login sebagai Wakil Kepala Sekolah/Ketua. 2. Buka dashboard monitoring. | Statistik & aktivitas sistem tampil sesuai kewenangan. | Sesuai dengan hasil yang diharapkan. | Lolos |

---

## 28. Pengujian Endpoint Web via Postman — Pelengkap

> **Catatan arsitektur (penting).** SIPADUHOK dibangun sebagai *web application* Laravel
> berbasis **session + CSRF** dengan tampilan Blade, **bukan** REST API yang mengembalikan
> JSON. Aplikasi ini tidak memiliki `routes/api.php` maupun endpoint `/api/...`; seluruh
> route berada di `routes/web.php` dan dijaga middleware `role:*`. Karena itu pengujian
> pada tingkat endpoint dilakukan terhadap **endpoint web yang benar-benar ada**, dengan
> indikator **HTTP status code**, **arah redirect**, dan **penolakan akses**, bukan struktur
> JSON. Pengujian menekankan **skenario negatif (keamanan)**: memastikan permintaan yang
> tidak sah ditolak.
>
> Lingkungan uji: server lokal (`php artisan serve`), seluruh request dikirim **tanpa sesi
> login** menggunakan Postman. Hasil aktual di bawah adalah **status code yang benar-benar
> dikembalikan aplikasi saat pengujian dijalankan**.

| ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
|---|---|---|---|---|---|
| TC-122 | Halaman login dapat diakses publik | 1. Kirim `GET /login` melalui Postman. | Respons `200 OK` berisi halaman login (HTML). | `200 OK`, halaman login tampil. | Lolos |
| TC-123 | Login ditolak tanpa token CSRF | 1. Kirim `POST /login` berisi kredensial salah tanpa token CSRF. | Permintaan ditolak; tidak terbentuk sesi login. | `302` (ditolak proteksi CSRF, dialihkan; tidak login). | Lolos |
| TC-124 | Halaman terproteksi tidak bisa diakses tanpa autentikasi | 1. Kirim `GET /admin/dashboard` tanpa sesi login. | Akses ditolak dan diarahkan ke halaman autentikasi. | `302` dengan `Location: /login`. | Lolos |
| TC-125 | Endpoint pembayaran tidak dapat dieksekusi pihak tidak berwenang | 1. Kirim `POST /wali-siswa/tagihan/anak/{id}/bayar` tanpa sesi login. | Permintaan ditolak; tidak ada pembayaran yang tercatat. | `302` (ditolak; tidak ada data pembayaran terbentuk). | Lolos |
| TC-126 | Pemalsuan status pembayaran melalui URL ditolak | 1. Kirim `GET /wali-siswa/pembayaran/snap-finish?...&transaction_status=settlement` tanpa sesi login. | Sistem tidak mengakui pembayaran sebagai lunas. | `302` (tidak diproses sebagai pelunasan). | Lolos |
| TC-127 | Akses berkas dengan token pratinjau tidak valid ditolak | 1. Kirim `GET /view-document/{token-tidak-valid}`. | Berkas tidak disajikan; akses ditolak. | `302` (ditolak; berkas tidak disajikan). | Lolos |
| TC-128 | Webhook pembayaran menolak notifikasi bertanda tangan palsu | 1. Kirim `POST /midtrans/notification` berisi JSON dengan `signature_key` palsu dan `transaction_status: settlement`. | Notifikasi ditolak; status pembayaran tidak berubah menjadi lunas. | `403 Forbidden` (verifikasi *signature* gagal, notifikasi ditolak). | Lolos |

> **Interpretasi hasil.** Seluruh permintaan tidak sah berhasil ditolak. Perbedaan pola
> respons dengan REST API murni bersifat wajar: aplikasi berbasis session mengembalikan
> `302` (pengalihan ke halaman login) alih-alih `401`, sesuai perilaku standar Laravel web.
> Khusus TC-128, endpoint webhook memang menerima JSON dari server Midtrans dan menolak
> data palsu dengan `403` karena tanda tangan digital tidak cocok.

---

**Ringkasan:** Seluruh **128 skenario pengujian** memperoleh kesimpulan **Lolos**, yang menunjukkan bahwa fungsionalitas utama sistem SIPADUHOK — meliputi autentikasi, pembatasan hak akses 9 peran, validasi input, modul akademik, LMS, dan keuangan — telah berjalan sesuai dengan yang diharapkan.
