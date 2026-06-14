# Dokumentasi Use Case Tergeneralisasi (39 Use Case)
> SIPADUHOK - Sistem Informasi Pendaftaran & Pembayaran SPP

Sesuai dengan pemisahan antara Ujian dan Latihan, berikut adalah rancangan **39 Use Case** yang *clean* dan sangat siap untuk diubah menjadi Activity/Sequence Diagram.

---

## 🔴 Modul Manajemen Sistem & Data Master

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 1 | **Kelola Data Pengguna** | Admin | Tenaga Pendidik, Siswa, Wali Murid |
| 2 | **Kelola Tiket Pemulihan Akun** | Admin | Tiket Pemulihan Akun |
| 3 | **Kelola Pengaturan Sistem** | Admin | Pengaturan LMS, Pengaturan AI |
| 4 | **Kelola Tahun Ajaran** | Admin, Waka | Tahun Ajaran |
| 5 | **Kelola Data Cabang** | Admin | Manajemen Cabang |
| 6 | **Kelola Data Kelas & Penugasan**| Admin, Waka | Data Kelas, Wali Kelas, Guru Pengajar, Manajemen Siswa |
| 7 | **Kelola Mata Pelajaran** | Admin, Waka | Mata Pelajaran |
| 8 | **Kelola Jadwal Pelajaran** | Admin, Waka | Jadwal Pelajaran |

## 🟡 Modul Informasi & Publikasi

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 9 | **Kelola Landing Page** | Admin | Landing Page |
| 10 | **Kelola Konten Publikasi** | Admin, Sekretaris| Kalender Akademik, Pengumuman, Flyer, Kelola Berita |

## 🟢 Modul Keuangan

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 11 | **Kelola Tagihan & Pembayaran**| Admin, Bendahara | Tagihan, Tarik Tunggakan, Pembayaran Masuk, Config Pembayaran |
| 12 | **Lihat Laporan Keuangan** | Admin, Bendahara | Laporan Pembayaran, Rekap Tagihan, Siswa Belum Lunas |
| 13 | **Melakukan Pembayaran Tagihan**| Orang Tua | Tagihan Anak (Midtrans/Manual) |

## 🟣 Modul Validasi, Dispensasi & Kenaikan Kelas

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 14 | **Validasi Akses Ujian dan Rapor**| Admin, Bendahara, Wali Kelas | Validasi Ujian & Rapor |
| 15 | **Memproses Dispensasi Keuangan** | Admin, Bendahara, Ketua | Menu Dispensasi Keuangan (Pengajuan & Persetujuan) |
| 16 | **Memproses Dispensasi Kenaikan**| Admin, Bendahara, Ketua | Validasi Dispensasi Kenaikan Kelas |
| 17 | **Kelola Pengaturan Kenaikan** | Admin, Waka | Pengaturan KKM, Pengaturan Kenaikan |
| 18 | **Proses Eksekusi Kenaikan Kelas**| Admin, Waka | Proses & Rekap |

## 🔵 Modul Presensi & Rapor (Wali Kelas & Orang Tua)

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 19 | **Kelola Presensi Siswa** | Wali Kelas | Input Harian, Validasi Izin, Rekap, Riwayat Edit |
| 20 | **Kelola Rapor Siswa** | Wali Kelas | Rekap Nilai, Generate Rapor, Kirim Validasi, Terbitkan Rapor |
| 21 | **Validasi Rapor Tingkat Akhir** | Ketua PKBM | Validasi Rapor (ACC/Revisi) |
| 22 | **Mengelola Permintaan Unduh** | Wali Kelas | Request Download Rapor |
| 23 | **Meminta dan Mengunduh Rapor** | Orang Tua | Menu Rapor Anak (Request & Download) |
| 24 | **Melihat Prediksi Kenaikan** | Wali Kelas | Prediksi Kenaikan Kelas |
| 25 | **Mengajukan Izin Kehadiran** | Orang Tua | Ajukan Izin Ketidakhadiran Anak |
| 26 | **Melihat Riwayat Presensi** | Siswa, Orang Tua | Presensi Anak/Pribadi (Read-Only) |

## 🟠 Modul Pembelajaran LMS (Guru & Siswa)

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 27 | **Kelola Materi Pembelajaran** | Guru | Materi, Arsip LMS |
| 28 | **Kelola & Koreksi Tugas** | Guru | Tugas (Buat soal tugas & Beri Nilai) |
| 29 | **Kelola Ujian** | Guru | Ujian (Membuat sesi ujian & bank soal evaluasi formal) |
| 30 | **Kelola Latihan** | Guru | Latihan (Membuat soal latihan non-formal) |
| 31 | **Mengikuti Pembelajaran LMS** | Siswa | Membaca Materi, Submit Tugas, Mengerjakan Ujian, Mengerjakan Latihan |
| 32 | **Berpartisipasi di Forum Diskusi**| Guru, Siswa | Forum Diskusi |
| 33 | **Kelola Kelas Virtual** | Guru, Siswa | Kelas Virtual (Zoom/GMeet link) |
| 34 | **Input Nilai Akademik Siswa** | Guru | Menu Nilai Siswa (Rekap manual akhir) |

## ⚫ Modul Monitoring & Akses Informasi Umum

| No | Nama Use Case | Aktor | Menu Sidebar yang Tercakup |
|----|--------------|-------|---------------------------|
| 35 | **Monitoring Sistem Terpadu** | Admin, Ketua, Waka| Monitoring Pengguna, Wali, Guru, Siswa, LMS |
| 36 | **Kelola Laporan & Catatan** | Admin, Ketua, Waka| Cetak Laporan Terpadu, Kirim Catatan |
| 37 | **Membaca Catatan Monitoring** | Guru, Wali Kelas | Notifikasi/Badge Teguran |
| 38 | **Melihat Informasi Akademik** | Wali Kelas, Guru, Siswa| Jadwal Mengajar/Pelajaran, Daftar Guru |
| 39 | **Melihat Nilai Pribadi** | Siswa | Data Penilaian (Read-Only) |
