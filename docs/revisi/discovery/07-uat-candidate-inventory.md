# UAT Candidate Inventory

Kriteria: `YA` bila fungsi bisnis dapat dijalankan dan dinilai pengguna melalui UI; `SEBAGIAN` bila UI ada tetapi keberhasilan bergantung file, scheduler atau layanan eksternal; `TIDAK` bila murni proses internal yang seharusnya diverifikasi melalui SIT/integration test. Tidak ada Test Result, tanggal, evidence, tester aktual, atau status PASS/FAIL pada Stage 0.

| Modul | Feature ID | Fitur | Role Tester | UI Testable | UAT Candidate | Alasan |
|---|---|---|---|---|---|---|
| AU | FEAT-001 | Login | Semua role | YA | YA | Entry point pengguna utama |
| AU | FEAT-002 | Logout | Semua role | YA | YA | Aksi session langsung |
| AU | FEAT-003 | Recovery publik | Pengguna/ADM | SEBAGIAN | SEBAGIAN | Mail runtime diperlukan untuk alur penuh |
| AU | FEAT-004 | Recovery/setup keamanan Admin | ADM | SEBAGIAN | SEBAGIAN | Setup UI ada; email recovery bergantung konfigurasi |
| AU | FEAT-005 | Pengaturan akun | Semua role | YA | YA | Form pengguna langsung |
| AU | FEAT-006 | Profil/foto | Semua role | YA | YA | Form/upload pengguna langsung |
| USR | FEAT-007 | Kelola tenaga pendidik | ADM | YA | YA | CRUD UI |
| USR | FEAT-008 | Kelola siswa | ADM | YA | YA | CRUD/status UI |
| USR | FEAT-009 | Kelola orang tua | ADM | YA | YA | CRUD/relasi UI |
| USR | FEAT-010 | Aktivasi akun | ADM | YA | YA | Action UI |
| USR | FEAT-011 | Import pengguna | ADM | SEBAGIAN | SEBAGIAN | Memerlukan workbook terkontrol |
| USR | FEAT-012 | Template import | ADM | YA | YA | Download dari UI |
| USR | FEAT-013 | Tiket recovery | ADM | YA | YA | Antrean/history UI |
| ORG | FEAT-014 | Cabang | ADM | YA | YA | CRUD UI |
| ORG | FEAT-015 | Tahun ajaran | ADM, WKA | YA | YA | CRUD/toggle UI |
| ORG | FEAT-016 | Kelas | ADM, WKA | YA | YA | CRUD/import/print UI |
| ORG | FEAT-017 | Anggota kelas | ADM, WKA | YA | YA | Aksi assignment UI |
| ORG | FEAT-018 | Wali kelas | ADM, WKA | YA | YA | Assignment UI |
| ORG | FEAT-019 | Relasi siswa-orang tua | ADM, WKA | YA | YA | Attach/detach UI |
| ORG | FEAT-020 | Kartu/daftar siswa | ADM, WKA | YA | YA | Filter/detail/print UI |
| AKD | FEAT-021 | Mata pelajaran | ADM, WKA | YA | YA | CRUD/import UI |
| AKD | FEAT-022 | Jadwal | ADM, WKA | YA | YA | CRUD bisnis UI |
| AKD | FEAT-023 | Import/duplikasi jadwal | ADM, WKA | SEBAGIAN | SEBAGIAN | Import perlu dataset; duplicate dapat diuji UI |
| AKD | FEAT-024 | Ganti guru | ADM, WKA | YA | YA | Aksi UI dan hasil terlihat |
| AKD | FEAT-025 | Cetak/export jadwal | Role terkait | YA | YA | Output dapat diverifikasi user |
| AKD | FEAT-026 | Rekonstruksi guru pengajar | ADM, WKA | YA | YA | Tombol/UI tersedia, hasil assignment terlihat |
| AKD | FEAT-027 | Waktu istirahat | ADM, WKA | YA | YA | CRUD/toggle UI |
| AKD | FEAT-028 | Jadwal wali kelas | WKL | YA | YA | View/print user-facing |
| AKD | FEAT-029 | Jadwal/kelas guru | GRU | YA | YA | View user-facing |
| AKD | FEAT-030 | Laporan akademik | ADM | YA | YA | Filter/cetak user-facing |
| KON | FEAT-031 | Kalender akademik | ADM, SEK, SIS | YA | YA | Kelola dan konsumsi UI |
| KON | FEAT-032 | Pengumuman | ADM, SEK, SIS | YA | YA | Kelola dan konsumsi UI |
| KON | FEAT-033 | Berita | ADM, SEK, publik | YA | YA | CMS dan halaman publik |
| KON | FEAT-034 | Flyer | ADM, SEK, publik | YA | YA | CMS/upload dan output publik |
| KON | FEAT-035 | Landing page CMS | ADM | YA | YA | Form dan hasil publik |
| KON | FEAT-036 | Halaman publik/sitemap | Pengunjung | YA | YA | Dapat diuji browser tanpa login |
| KEU | FEAT-037 | Kelola tagihan | ADM, BEN | YA | YA | CRUD/bulk UI |
| KEU | FEAT-038 | Generate SPP | ADM, BEN | YA | YA | Proses bisnis dan hasil terlihat |
| KEU | FEAT-039 | Import/export tagihan | ADM, BEN | SEBAGIAN | SEBAGIAN | Perlu file dan pemeriksaan output |
| KEU | FEAT-040 | Carryover | ADM, BEN | YA | YA | Preview/execute UI |
| KEU | FEAT-041 | Input pembayaran manual | ADM, BEN | YA | YA | Form transaksi UI |
| KEU | FEAT-042 | Validasi pembayaran | ADM, BEN | YA | YA | Keputusan dan status terlihat |
| KEU | FEAT-043 | Transfer wali | ORT, BEN | YA | YA | Alur lintas-role user-facing |
| KEU | FEAT-044 | Payment Midtrans | ORT | YA | YA | Uji sandbox melalui Snap; perlu credential/network |
| KEU | FEAT-045 | Webhook/status internal | Sistem/QA teknis | TIDAK | TIDAK | Bukan aksi langsung user; kandidat SIT/API test |
| KEU | FEAT-046 | Konfigurasi kanal | ADM, BEN | YA | YA | Form/toggle UI; test key dapat memakai sandbox |
| KEU | FEAT-047 | Laporan keuangan | ADM, BEN | YA | YA | Filter/agregat/cetak user-facing |
| PRS | FEAT-048 | Input presensi | WKL | YA | YA | Aksi utama Wali |
| PRS | FEAT-049 | Rekap/import presensi | WKL | YA | YA | View/print/import user-facing |
| PRS | FEAT-050 | Ajukan izin | ORT | YA | YA | Aksi portal Orang Tua |
| PRS | FEAT-051 | Validasi izin | WKL | YA | YA | Keputusan lintas-role |
| PRS | FEAT-052 | Riwayat presensi | SIS, ORT | YA | YA | Monitoring user-facing |
| NIL | FEAT-053 | Nilai guru | GRU | YA | YA | Input/import/export UI |
| NIL | FEAT-054 | Nilai wali | WKL | YA | YA | Kelola/print UI |
| NIL | FEAT-055 | Sinkronisasi nilai observer | Sistem/QA teknis | TIDAK | TIDAK | Efek internal; diuji melalui flow/SIT |
| NIL | FEAT-056 | Koreksi tugas | GRU | YA | YA | UI koreksi; AI opsional |
| NIL | FEAT-057 | Lihat nilai | SIS, ORT | YA | YA | Portal user-facing |
| RAP | FEAT-058 | Generate rapor | WKL | YA | YA | Proses bisnis utama |
| RAP | FEAT-059 | Edit/format rapor | WKL | YA | YA | Form/template UI |
| RAP | FEAT-060 | Publikasi/state rapor | WKL | YA | YA | Action dan state terlihat |
| RAP | FEAT-061 | Kirim validasi | WKL | YA | YA | Alur lintas-role |
| RAP | FEAT-062 | Validasi/revisi rapor | KET | YA | YA | Keputusan Ketua UI |
| RAP | FEAT-063 | Request/download rapor | ORT, WKL | YA | YA | Alur lintas-role lengkap |
| RAP | FEAT-064 | Arsip rapor | WKL | YA | YA | Read-only UI |
| LMS | FEAT-065 | Dashboard LMS guru | GRU | YA | YA | Entry point user-facing |
| LMS | FEAT-066 | Materi guru | GRU | YA | YA | CRUD/upload UI |
| LMS | FEAT-067 | Baca materi | SIS | YA | YA | Konsumsi konten |
| LMS | FEAT-068 | Tugas guru | GRU | YA | YA | CRUD/upload UI |
| LMS | FEAT-069 | Submit tugas | SIS | YA | YA | Aksi pembelajaran utama |
| LMS | FEAT-070 | Kelola ujian | GRU | YA | YA | CRUD/result/correction UI |
| LMS | FEAT-071 | Bank soal | GRU | YA | YA | Single/bulk/import/export UI |
| LMS | FEAT-072 | Generator soal AI | GRU | SEBAGIAN | SEBAGIAN | UI ada; provider/key/quota/network diperlukan |
| LMS | FEAT-073 | Kerjakan ujian | SIS | YA | YA | Aksi pembelajaran utama |
| LMS | FEAT-074 | Pengawasan ujian | GRU, SIS | SEBAGIAN | SEBAGIAN | Memerlukan dua session/aktor simultan |
| LMS | FEAT-075 | Kelola latihan | GRU | YA | YA | CRUD/result UI |
| LMS | FEAT-076 | Kerjakan latihan | SIS | YA | YA | Aksi siswa UI |
| LMS | FEAT-077 | Forum | GRU, SIS | YA | YA | Interaksi multi-user UI |
| LMS | FEAT-078 | Meeting | GRU, SIS | YA | YA | Kelola/akses link UI |
| LMS | FEAT-079 | Arsip/salin konten | GRU | YA | YA | Preview/copy UI |
| PRM | FEAT-080 | Atur KKM | ADM, WKA | YA | YA | Form konfigurasi UI |
| PRM | FEAT-081 | Atur kriteria | ADM, WKA | YA | YA | Form/readiness UI |
| PRM | FEAT-082 | Prediksi kelayakan | WKL | YA | YA | Laporan keputusan user-facing |
| PRM | FEAT-083 | Validasi finansial | ADM, BEN | YA | YA | Keputusan UI |
| PRM | FEAT-084 | Approval Ketua | KET | YA | YA | Single/bulk approval UI |
| PRM | FEAT-085 | Eksekusi/rollback | ADM, WKA | YA | YA | Aksi dan hasil terlihat; butuh data uji terkendali |
| PRM | FEAT-086 | Jadwal kenaikan | ADM, WKA | SEBAGIAN | SEBAGIAN | Setup UI; eksekusi penuh bergantung scheduler |
| MON | FEAT-087 | Monitoring pengguna | ADM, KET, WKA | YA | YA | Dashboard/filter user-facing |
| MON | FEAT-088 | Monitoring LMS | ADM, KET, WKA | YA | YA | Filter/preview user-facing |
| MON | FEAT-089 | Catatan/teguran | ADM, KET, WKA | YA | YA | Alur kirim dan hasil ke Guru |
| MON | FEAT-090 | Catatan Guru | GRU | YA | YA | Portal penerima |
| NOT | FEAT-091 | Pusat notifikasi | Semua role | YA | YA | UI langsung |
| NOT | FEAT-092 | Status notifikasi | Semua role | YA | YA | Action langsung |
| NOT | FEAT-093 | Reminder terjadwal | Sistem/QA teknis | TIDAK | TIDAK | Tidak dipicu langsung pengguna; kandidat SIT/scheduler test |
| SWA | FEAT-094 | Dashboard siswa | SIS | YA | YA | UI utama |
| SWA | FEAT-095 | Akademik pribadi | SIS | YA | YA | UI data sendiri |
| SWA | FEAT-096 | Alumni read-only | SIS alumni | YA | YA | Dapat diuji dengan akun alumni |
| WLS | FEAT-097 | Dashboard anak | ORT | YA | YA | UI utama Orang Tua |
| WLS | FEAT-098 | Tagihan/invoice anak | ORT | YA | YA | Monitoring/cetak UI |
| WLS | FEAT-099 | Akademik anak | ORT | YA | YA | Monitoring UI |
| WLS | FEAT-100 | Download rapor anak | ORT, WKL | YA | YA | Workflow multi-role UI |
| KEU | FEAT-101 | Lihat tagihan/riwayat siswa | SIS | YA | YA | Route/view aktif untuk data sendiri; aksi bayar harus terbukti ditolak dan diarahkan ke Wali Siswa |

## End-to-End UAT Candidates

1. Login tiap role dan verifikasi menu/dashboard yang tepat.
2. Admin membuat siswa, menempatkan ke kelas, dan menautkan Orang Tua; kedua portal menampilkan data yang benar.
3. Waka membuat jadwal; Guru memperoleh assignment; Siswa melihat mapel/jadwal yang tepat.
4. Guru menerbitkan materi/tugas; Siswa submit; Guru koreksi; nilai tampil di portal.
5. Guru membuat ujian; Siswa mengerjakan; Guru memonitor/koreksi; nilai tersinkron.
6. Orang Tua mengajukan izin; Wali Kelas memvalidasi; riwayat berubah.
7. Bendahara generate tagihan; Orang Tua transfer; Bendahara memvalidasi; tagihan/laporan berubah.
8. Orang Tua membayar melalui Midtrans sandbox; status dan notifikasi tersinkron.
9. Wali menghasilkan rapor; Ketua meminta revisi/validasi; Orang Tua meminta download; Wali menyetujui.
10. KKM/aturan → prediksi wali → validasi finansial → approval Ketua → eksekusi/rollback kenaikan.
11. Pimpinan memonitor konten LMS, mengirim catatan, dan Guru membaca catatan.

## Features Not Appropriate for Direct UAT

- FEAT-045 — webhook dan sinkronisasi status internal Midtrans; uji melalui SIT/API/security test, sementara hasil bisnisnya diamati pada UAT FEAT-044.
- FEAT-055 — observer/service sinkronisasi nilai; uji melalui SIT dan amati melalui flow tugas/ujian → nilai.
- FEAT-093 — command reminder terjadwal; uji scheduler/SIT dan amati notifikasi penerima.

## Inventory Statistics

- `YA`: **90** fitur
- `SEBAGIAN`: **8** fitur
- `TIDAK`: **3** fitur
- Kandidat UAT langsung (`YA`): **90**
