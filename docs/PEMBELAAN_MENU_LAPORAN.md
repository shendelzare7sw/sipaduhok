# Pembelaan Desain: Menu Cetak Per-Entitas vs Modul Laporan Terpusat

> Dokumen bekal sidang. Menjawab pertanyaan: *"Kenapa ada fitur cetak di menu
> Siswa/Kelas/Guru, padahal sudah ada menu Laporan yang mencetak hal serupa?
> Bukankah itu duplikat?"*

## 1. Ringkasan eksekutif

Ada **dua jenis titik cetak** untuk data akademik, dan keduanya **sengaja dibedakan
peran**, bukan redundansi tak sengaja:

- **Cetak per-entitas** (mis. tombol *Cetak* di `Kelola Siswa`, `Kelas`, `Guru Pengajar`)
  = **aksi kontekstual**. Pengguna yang sedang mengelola/menyaring satu jenis data bisa
  langsung mencetak daftar yang **sedang ia lihat** (mengikuti filter & pencarian di layar),
  tanpa berpindah menu.
- **Modul Laporan terpusat** (`/admin/laporan`, `/ketua/laporan`) = **pusat pelaporan**
  bagi pimpinan (Admin & Ketua PKBM). Fokusnya rekap menyeluruh **lintas tahun ajaran**
  dan **rekap statistik** — kebutuhan yang berbeda dari sekadar mencetak satu daftar.

Pola "aksi kontekstual" berdampingan dengan "hub pelaporan" adalah pola umum pada sistem
informasi (mis. tombol *Print* pada halaman daftar vs modul *Reports/Analytics* tersendiri).

## 2. Peta menu terkait

| Fungsi | Lokasi | Dipakai peran | Sifat |
|---|---|---|---|
| Cetak daftar (siswa/kelas/guru/dll) per objek | menu objek terkait, mis. `/admin/kelas` → *Cetak* | Admin, Waka | Aksi kontekstual (ikut filter layar) |
| Modul Laporan (siswa, tenaga pendidik, kelas, wali kelas, guru pengajar, rekap) | `/admin/laporan`, `/ketua/laporan` | Admin, Ketua PKBM | Hub pelaporan lintas-tahun |
| Rekap Akademik (naik/tidak/lulus/tunggakan per TA) | `/ketua/laporan` (rekap akademik) | Ketua PKBM (+ Admin via warisan) | Snapshot keputusan kenaikan kelas |

Secara kode, modul Laporan Admin **mewarisi** (extends) modul Laporan Ketua
(`Admin\MonitoringController extends Ketua\KetuaController`) — jadi **satu sumber logika**,
bukan dua salinan terpisah. Ini justru bukti tidak ada duplikasi kode di jalur utama.

## 3. Perbedaan cakupan (kenapa keduanya tetap perlu)

| Objek | Cetak per-entitas | Modul Laporan terpusat |
|---|---|---|
| **Siswa** | Ikut **pencarian** (nama/NIS/NISN) & filter halaman kelola; cepat untuk kebutuhan operasional harian | Mendukung **snapshot historis** antar tahun ajaran (status kelulusan/kenaikan), sort per kelas/cabang — untuk arsip & pelaporan pimpinan |
| **Kelas** | Cetak daftar kelas yang sedang dikelola/disaring | Sama datanya, tapi dalam kerangka pelaporan pimpinan (satu pintu bersama laporan lain) |
| **Guru / Wali Kelas / Guru Pengajar** | Cetak dari menu manajemen masing-masing | Terangkum di satu tempat untuk keperluan rekap kepegawaian akademik |
| **Rekap statistik / akademik** | *(tidak ada)* | **Hanya** di modul terpusat — rekap jumlah siswa/kelas/guru per cabang & jenjang, serta rekap keputusan kenaikan kelas |

Intinya: data intinya memang beririsan, tetapi **konteks penggunaan, filter, dan cakupan
lintas-tahun berbeda**. Menghapus salah satunya akan menghilangkan kenyamanan operasional
(sisi per-entitas) atau kemampuan rekap pimpinan (sisi terpusat).

## 4. Antisipasi pertanyaan penguji

- **"Ini duplikat, kenapa tidak disatukan?"**
  Fungsinya mirip di permukaan tapi beda peran: satu untuk aksi cepat sesuai konteks kerja,
  satu untuk pelaporan menyeluruh. Menyatukan paksa akan menambah langkah bagi pengguna
  operasional (harus buka menu Laporan + set filter) untuk tugas yang seharusnya satu klik.

- **"Apa buktinya bukan sekadar copy-paste?"**
  Modul Laporan Admin secara teknis **mewarisi** modul Ketua (satu basis kode). Untuk cetak
  per-entitas, filternya memang berbeda (pencarian pada per-entitas; snapshot historis pada
  terpusat), jadi keduanya melayani kebutuhan yang tak identik.

- **"Kenapa Waka tidak punya menu Laporan?"**
  Sesuai kewenangan: Waka berfokus pada pemantauan & pengelolaan data operasional (punya
  cetak per-entitas), sedangkan rekap pelaporan menyeluruh menjadi ranah Ketua PKBM & Admin.

## 5. Catatan kejujuran teknis (untuk perbaikan lanjutan)

Terdapat satu modul sisa, `admin/cetak-laporan/*` (`CetakLaporanController`), yang isinya
tumpang tindih dengan `/admin/laporan`. Modul ini **tidak tertaut di menu/sidebar** sehingga
tidak menjadi bagian alur yang digunakan — praktis sisa pengembangan. Rencana rapikan
(dihapus) dijadwalkan sebagai *cleanup* terpisah agar tidak mengganggu stabilitas versi yang
sudah berjalan di production. Ini disebutkan demi transparansi, bukan bagian fitur aktif.

---
*Dokumen ini melengkapi manual book; tidak mengubah alur menu yang sudah didokumentasikan.*
