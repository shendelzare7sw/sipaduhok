# CleanFlow Admin UI Migration

Pembaruan terakhir: 17 September 2026.

## Definisi status

- **Selesai**: seluruh view utama modul (index/create/edit/show/import dan partial terkait) memakai Tailwind pada shell CleanFlow dan tidak memuat CSS/JS UI lokal.
- **Berjalan**: sebagian view sudah Tailwind, tetapi masih ada view atau aset lokal yang aktif.
- **Belum**: seluruh atau mayoritas view masih bergantung pada UI legacy.
- Stylesheet khusus dokumen cetak dicatat terpisah sampai alur cetak dimigrasikan ke utility `print:` Tailwind.

## Ringkasan modul

| Modul | View | View dengan aset/skrip lokal | Status | Fokus berikutnya |
|---|---:|---:|---|---|
| Dashboard admin | 1 | 0 | Selesai | Pemeliharaan |
| Tahun Ajaran | 5 | 0 | Selesai | Pemeliharaan |
| Cabang | 5 | 0 | Selesai | Pemeliharaan |
| Pengguna | 24 | 0 | Selesai | Pemeliharaan |
| Catatan | 3 | 0 | Selesai | Pemeliharaan |
| Mata Pelajaran | 7 | 0 | Selesai | Pemeliharaan |
| Pengaturan Istirahat | 4 | 0 | Selesai | Pemeliharaan |
| Kelas | 8 | 0 | Selesai | Pemeliharaan |
| Jadwal Pelajaran | 8 | 0 | Selesai | Pemeliharaan |
| Guru Pengajar | 4 | 0 | Selesai | Pemeliharaan |
| Wali Kelas | 3 | 0 | Selesai | Pemeliharaan |
| Manajemen Siswa | 5 | 0 | Selesai | Pemeliharaan |
| Monitoring & LMS | 12 | 0 | Selesai | Pemeliharaan lintas-role |
| Akademik/Publikasi | 13 | 0 | Selesai | Pemeliharaan |
| Keuangan | 26 | 0 | Selesai | Pemeliharaan |
| Laporan | 8 | 0 | Selesai | Dipakai bersama dua kelompok route |
| Cetak Laporan | 0 khusus | 0 | Selesai | Menggunakan view Laporan bersama |
| Recovery Ticket | 2 | 0 | Selesai | Pemeliharaan |
| AI Settings | 1 | 0 | Selesai | Pemeliharaan |
| LMS Settings | 1 | 0 | Selesai | Pemeliharaan |
| Landing Page | 3 | 0 | Selesai | Pemeliharaan |

Dua puluh satu modul admin telah menyelesaikan migrasi view utama tanpa aset CSS/JS halaman khusus. Alur Monitoring/LMS bersama—index, detail kelas, dialog catatan, dan tiga jenis preview—sekarang memakai Tailwind + Alpine/HTML native dan delapan aset lamanya telah dihapus. Dua namespace route Laporan tetap memakai satu pusat laporan dan tujuh template cetak bersama. Fondasi global Notification, Profile, Account Settings, scroll-up, dan seluruh UI AI Assistant kini juga memakai Tailwind tanpa stylesheet halaman/komponen khusus.

## Aset lokal aktif (baseline)

Pembaruan komponen global: tombol Bantuan desktop memakai dimensi eksplisit 132 x 56 px agar tidak tertekan konflik utility padding; scroll-up tetap 56 x 56 px. Keduanya memakai lajur terpisah, sejajar pada pusat vertikal, dan berjarak aman 24 px. Batas drag Bantuan tidak dapat memasuki lajur scroll-up. Parser server dan frontend kini memulihkan JSON provider yang lengkap, terbungkus, maupun terpotong menjadi teks, callout, tombol utama, dan tautan terkait yang aman. Migrasi localStorage versi 5 juga membersihkan payload mentah pada riwayat lama.

| Kelompok | Jumlah file CSS/JS |
|---|---:|
| Keuangan | 0 |
| Landing Page | 0 |
| Cetak Laporan | 0 |
| Laporan | 0 |
| AI Chatbot lintas-role (shell bersama) | 1 JS global, 0 CSS |
| Monitoring LMS lintas-role | 0 |

Angka diperbarui setelah satu alur selesai, bukan setelah perubahan kosmetik parsial. Folder aset khusus Keuangan, Laporan, dan Landing Page, aset cetak publik Tagihan/Laporan, serta view duplikat `admin/cetak-laporan` telah dihapus setelah audit referensi dan pengalihan controller.

## Progress migrasi role berikutnya

Role **Sekretaris selesai** untuk dashboard, sidebar, Berita, Flyer, Kalender Akademik (index/form/detail/feed/cetak), dan Pengumuman. Sembilan view CRUD yang menduplikasi Admin dihapus; keduanya kini memakai sembilan view Akademik/Publikasi bersama yang memilih route sesuai konteks role. Sebanyak 17 aset CSS/JS role dan 2 aset dashboard telah dihapus setelah audit referensi, feature test, dan uji browser.

Role **Ketua PKBM selesai** untuk dashboard, sidebar, monitoring pengguna/wali kelas/guru/siswa/LMS, pusat laporan dan tujuh dokumen cetak, Catatan, persetujuan dispensasi kenaikan kelas beserta riwayat, dispensasi keuangan, serta validasi rapor. Lima belas view duplikat dialihkan ke view Tailwind Admin yang sadar konteks route lalu dihapus. Empat view khusus keputusan didesain ulang dengan Tailwind + Alpine/native dialog; seluruh 19 aset role dan aset dashboard Ketua telah dihapus.

Role **Bendahara selesai**. Dashboard, sidebar, validasi dispensasi kenaikan kelas, riwayat keputusan, Validasi Akses, lima halaman Pembayaran, sepuluh halaman Tagihan, serta enam Laporan/cetak kini memakai Tailwind + Alpine sesuai konteks role. Dua puluh empat view duplikat dan 31 aset CSS/JS lokal telah dihapus sepanjang migrasi; folder Pembayaran, Tagihan, dan Laporan yang kosong turut dibersihkan. Dashboard tidak lagi memakai aset halaman. Config Pembayaran tetap eksklusif Admin.

Role **Waka selesai**. Sidebar, dashboard, Data Kelas, Jadwal Pelajaran, Guru Pengajar, Wali Kelas, Manajemen Siswa, Monitoring Siswa/Guru/Wali, dan Catatan memakai Tailwind; alur yang identik memakai view Admin bersama yang sadar namespace route dan capability cabang. Namespace Waka kini hanya memiliki dua view khusus (`dashboard` dan `partials/sidebar`), tanpa `@vite` halaman serta tanpa aset CSS/JS role. Tahun Ajaran, Mata Pelajaran, Pengaturan Istirahat, serta Pengaturan/Proses Kenaikan telah dicabut dari sidebar, dashboard, named route, controller, view, dan aset Waka karena bersifat global/Admin-only.

Role **Wali Kelas selesai untuk alur aktif**. Semua halaman kerja—sidebar, dashboard, pilih kelas, jadwal, Presensi, Nilai, Rapor, Promosi, Validasi Akses, Template Capaian, dan Arsip—memakai Tailwind + Alpine/dialog native tanpa aset CSS/JS halaman atau Bootstrap khusus role. Alias route jadwal lama tetap dilayani controller aktif. Bug riwayat Presensi yang mengirim POST ke route PUT telah diperbaiki; status kehadiran dipisahkan dari keputusan validasi izin, dengan guard server untuk permintaan pending dan keputusan berulang. Daftar Rapor menempatkan Generate/Upload atau menu Aksi di pojok kanan atas baris pada mobile maupun desktop. Inventaris akhir: **30 view, 0 `@vite` halaman kerja, 4 `@vite` dokumen Rapor, dan 1 `@vite` dokumen Nilai siswa**; **11 aset public dokumen cetak yang masih direferensikan** (6 CSS, 5 JS) serta dua Tailwind entry dokumen. CSS khusus tabel/watermark/geometri halaman pada dokumen lain dan JS preview zoom tetap aktif untuk menjaga desain cetak; ini bukan aset yatim. Dua template rapor generik yang tidak dipakai beserta asetnya sudah dihapus. Bootstrap Icons CDN pada tiga dokumen Nilai diganti SVG lokal; tidak ada lagi marker Bootstrap di view Wali Kelas.

Inventaris awal sebelum migrasi role non-admin (marker legacy adalah temuan kasar untuk menentukan urutan, bukan jumlah halaman gagal):

| Role | View awal | Referensi Vite awal | Aset awal | Status saat ini |
|---|---:|---:|---:|---|
| Sekretaris | 12 | 17 | 17 + 2 dashboard | **Selesai — 0 aset UI lokal** |
| Ketua PKBM | 20 | 15 | 19 + 1 dashboard | **Selesai — 0 aset UI lokal** |
| Bendahara | 26 | 28 | 31 (audit final) | **Selesai — 0 aset UI lokal** |
| Wali Siswa | 12 | 15 | 16 | Belum |
| Wali Kelas | 33 | 21 | 41 | **Selesai — 30 view aktif, 0 aset halaman, 11 aset public cetak aktif + 2 Tailwind entry dokumen** |
| Guru | 44 | 40 | 40 | Belum |
| Siswa | 42 | 48 | 43 | Belum |
| Waka | 51 | 57 | 55 | **Selesai — 2 view khusus, 0 `@vite` halaman, 0 aset UI lokal** |

Urutan berikutnya adalah Guru/Siswa/Wali Siswa; Wali Kelas masuk pemeliharaan regresi. Guru/Siswa wajib diaudit per konteks shell: SIA dan LMS sama-sama full Tailwind tetapi identitas visual dan temanya tetap dibedakan, sedangkan Latihan/Ujian hanya dikonversi ke Tailwind secara visual-ekuivalen tanpa perubahan tata letak, warna, hirarki, atau interaksi. Angka aset tidak boleh langsung dianggap aman dihapus karena beberapa file dapat dipakai silang oleh layout atau view shared.

Validasi batch pertama Wali Kelas 16 September 2026: cache Blade, build produksi, Pint, dan `git diff --check` berhasil. Tujuh tes terfokus lulus dengan 125 assertion, termasuk keadaan tanpa penugasan, pemilihan kelas, alias jadwal lama, cetak, shell lintas-role, dan guard IDOR Presensi/Rapor/Validasi. Audit browser pada 1440x900 dan 390x844 memeriksa 10 kombinasi route/viewport: seluruhnya HTTP 200, overflow 0 px, marker Bootstrap 0, aset Wali lama 0, dan error JavaScript 0. Screenshot pilih kelas dan jadwal ditinjau; dashboard terukur saat notifikasi sukses memilih kelas sedang tampil. Akun, dua kelas, enam screenshot, skrip audit sementara, dan enam folder aset yang kosong sudah dibersihkan.

Validasi batch Presensi Wali Kelas 16 September 2026: 5 tes terfokus lulus dengan 185 assertion, termasuk guard IDOR dan shell. Cache Blade serta build produksi berhasil. Audit browser pada 1440x900 dan 390x844 memeriksa 14 kombinasi route/viewport: semuanya HTTP 200, overflow 0 px, marker Bootstrap 0, aset Presensi lama 0, dan error JavaScript 0. Dialog impor, konfirmasi simpan, penolakan izin, dan edit riwayat diuji terbuka serta dapat dibatalkan; input edit detail harian teraktifkan. Rekap dashboard mobile turun dari 386 px menjadi 156 px dan akses cepat dari 314 px menjadi 206 px. Form presensi hanya mengirim satu set field per siswa pada kedua breakpoint; dialog tindakan berulang hanya dibuat sekali. Bootstrap global tetap dipertahankan karena role/alur lain belum selesai dimigrasikan.

Koreksi cetak Wali Kelas 16 September 2026: temuan nyata pada `/wali/presensi/show-harian` menunjukkan audit layar saja tidak cukup—`window.print()` turut mencetak top bar dan tombol Bantuan. Shell `layouts.app` kini menyembunyikan sidebar, top bar, footer aplikasi, menu search, Bantuan, scroll-up, dan tumpukan modal pada media print; detail harian memberi kop cetak sendiri serta memaksa tampilan nilai baca-saja ketika mode edit terbuka. Cetak jadwal dan rekap Presensi tetap melalui `layouts.print`; kedua dokumen mengambil nama wali dari assignment multi-wali dengan fallback relasi lama. Audit Chrome media print/PDF A4 pada 1440 dan 390 px memeriksa ketiga route (6 kombinasi): HTTP 200, tanpa shell/floating pada cetakan, nama wali benar pada dua dokumen, dan tanpa error JavaScript. Ini belum menjadi klaim audit cetak role lain.

Migrasi Nilai/Rapor Wali Kelas: index/show/edit Nilai dan index/edit/request-download Rapor memakai Tailwind + Alpine, dengan satu set input aktif per field dan dialog tindakan bersama. Nilai nol dibedakan dari kosong, redirect edit mempertahankan semester, input mapel dan RaporNilai dikunci ke kelas/rapor yang benar, dan filter siswa pada daftar Rapor hanya menerima anggota kelas terpilih. Promosi, Rapor Pending, Validasi Akses, Template Capaian, serta Arsip index/detail juga sudah dimigrasikan; detail Arsip bersifat baca-saja dan memakai field nilai historis yang tepat. Dokumen Rapor PTS/PAS dan ketiga dokumen Nilai tidak lagi mengambil Bootstrap Icons; toolbar pratinjau memakai utility Tailwind atau CSS dokumen khusus dan mempertahankan JS zoom/print aktif. Dua route cetak PTS/PAS memakai utility Tailwind untuk konten dengan CSS kecil khusus `@page`.

Gerbang batch halaman kerja Wali Kelas 17 September 2026: 5 feature test terfokus lulus dengan 423 assertion (Core UI, IDOR Rapor, dan shell lintas-role); Blade cache, build produksi, dan `git diff --check` berhasil. Regresi mencakup method spoofing PUT pada modal riwayat, larangan mengubah status validasi lewat edit, keputusan izin berulang, nilai nol/kosong, batas ID mapel dan RaporNilai, hapus seluruh kegiatan ekstra, render halaman kerja, dua kondisi tombol daftar Rapor, dan semua cabang dokumen Nilai/Rapor PTS/PAS tanpa chrome aplikasi. Audit browser Rapor index/edit dan ketiga dokumen Nilai selesai pada 390/1440 px tanpa overflow atau error JavaScript; halaman Promosi/Arsip tetap tercakup tes render dan menjadi sasaran regresi visual jika ada perubahan berikutnya.

Gerbang preservasi cetak Rapor PTS/PAS 17 September 2026: Chrome headless mengambil baseline sebelum perubahan dan membandingkan pratinjau/cetak pada 390 serta 1440 px. Keempat gambar **route cetak PTS/PAS media print identik per piksel** sebelum/sesudah migrasi; PDF tetap dua halaman pada masing-masing kombinasi. Pratinjau PTS/PAS tetap memuat watermark yang sama, tabel/dimensi dokumen tidak berubah, toolbar disembunyikan saat print, tombol zoom in/reset bekerja, dan tidak ada error JavaScript. CSS pratinjau khusus watermark/page frame tidak dihapus demi kontrak visual tersebut. Overflow horizontal 960 px pada pratinjau PAS mobile sudah ada di baseline (konten 900 px diskalakan oleh JS) dan tidak diperluas pada batch ini; perbaikannya perlu audit terpisah tanpa mengubah hasil cetak.

Penutupan UI Rapor 17 September 2026: menu Aksi memakai SVG chevron yang jelas, opsi tetap teks tanpa ikon tambahan agar ringkas di mobile; editor mapel meletakkan panah urutan di kanan judul dan membatasi kolom mapel/kelompok di desktop; radio target Salin Format memakai flex-center. Chrome headless pada 390/1440 px: Rapor index/edit masing-masing overflow 0 px, menu terbuka, dialog radio sejajar per piksel, dan 0 error JavaScript. Ketiga dokumen Nilai pada kedua viewport menyembunyikan toolbar saat media print, menampilkan kop, tanpa Bootstrap Icons atau error JavaScript. Aset dokumen cetak yang masih dipakai tidak dihapus; perubahan desain PTS/PAS berikutnya tetap wajib baseline ekuivalen-piksel.

Penutupan cetak Nilai per siswa 17 September 2026: `/wali/nilai/{siswa}/print` untuk ganjil dan genap tidak memakai Bootstrap sejak awal, tetapi masih memakai CSS public yang besar. Template sekarang memakai utility Tailwind tanpa preflight; satu entry CSS kecil hanya menyimpan reset metrik dokumen, `@page` A4 landscape, zoom print, dan larangan tabel terpotong. CSS public lama dihapus setelah audit referensi. Pada data siswa 92, empat screenshot media print (390/1440 px × ganjil/genap) **identik per piksel** terhadap baseline, masing-masing PDF dua halaman, tanpa overflow/error JavaScript; zoom in/fit dan penyembunyian toolbar saat cetak tetap bekerja. Regresi kedua semester masuk feature test: 5 tes/433 assertion lulus, build produksi dan cache Blade berhasil.

Validasi Sekretaris: build produksi dan Blade cache berhasil; 4 feature test dengan 97 assertion lulus, termasuk kontrak route view bersama pada konteks Admin. Audit browser menelusuri 22 kombinasi route/viewport pada 1440x900 dan 390x844: seluruhnya HTTP 200, overflow 0 px, tanpa overlay Vite atau error JavaScript. Dashboard dan Kalender juga ditinjau dari screenshot render penuh. Feed kalender serta PDF bulanan/tahunan tetap bekerja.

Validasi Ketua: build produksi dan Blade cache berhasil; 13 test terfokus lulus dengan 169 assertion. Audit browser menguji 16 kombinasi route/viewport pada 1440x900 dan 390x844: seluruhnya HTTP 200, overflow dokumen 0 px, tanpa marker modal Bootstrap, overlay Vite, atau error console. Dialog validasi diuji terbuka pada mobile. Halaman persetujuan/dispensasi tanpa baris dummy tetap dikunci melalui render test, kontrak route, dan audit markup.

Validasi role Bendahara: Blade cache dan build produksi berhasil. Sebelas feature test lulus dengan 181 assertion; cakupannya termasuk render seluruh sepuluh route Tagihan melalui view Tailwind bersama, dashboard Alpine, namespace endpoint Bendahara, absennya kontrol Import/Reset khusus Admin, serta penghapusan aset. Enam route Laporan dan seluruh alur Pembayaran juga terbukti memakai view shared. Audit menemukan penggunaan `DAY()` khusus MySQL pada grafik laporan; ekspresi hari kini dipilih berdasarkan driver untuk MySQL, PostgreSQL, dan SQLite. Audit browser akhir pada dashboard dan index Tagihan di 1440x900 serta 390x844 menghasilkan HTTP 200, overflow 0 px, dropdown mobile tetap di viewport, tanpa marker Bootstrap, kebocoran endpoint Admin, atau error console. Audit sidebar lanjutan mengunci tautan utama dan pemicu dropdown ke border 0 px; hanya submenu yang boleh 1 px. Config Pembayaran Admin tetap HTTP 200 di kedua viewport dan `/bendahara/config` tetap 404.

Validasi integritas penugasan Guru: ditemukan bahwa akun Sekretaris dan Wali Kelas dapat masuk melalui import atau request ID manual meskipun dropdown hanya menampilkan Guru. Scope `eligibleToTeach` kini menjadi sumber tunggal untuk kandidat, validasi server, import, rebuild, statistik, dan notifikasi. Jalur Admin biasa/multi-jenjang/ganti guru, jalur Waka, import, dan notifikasi diuji dengan ID Sekretaris; seluruhnya menolak atau mengosongkan penugasan secara aman. Pembersihan database transaksional mengubah 7 jadwal invalid menjadi `kosong` sambil menulis history, lalu menghapus 18 baris turunan dan 2 notifikasi salah sasaran. Audit setelah perbaikan: 0 jadwal, 0 penugasan, dan 0 notifikasi invalid.

Validasi Jadwal Waka: view bersama tidak mengubah batas kewenangan. Scope cabang tunggal kini dipakai pada daftar/statistik, create/update biasa dan multi-jenjang, detail/edit/hapus/ganti guru, API kelas/guru/siswa, aksi massal, import, duplikasi periode, serta ekspor keseluruhan/per kelas. Render test memastikan HTML Waka hanya membawa endpoint `waka.*`. Tes IDOR juga mengunci akses kelas cabang lain pada detail, edit, pratinjau, print, Excel, API, create, status massal, dan hapus massal. Duplikasi periode Admin/Waka diperbaiki agar mempertahankan pivot multi-kelas; Waka hanya menyalin kelas cabangnya.

Validasi Dashboard dan Data Kelas Waka 16 September 2026: tujuh render test dengan 254 assertion lulus untuk dashboard, seluruh tujuh halaman Kelas, regresi Admin, promosi, Jadwal, namespace route, dan berkas yang dihapus. Audit browser pada 1440x900 dan 390x844 mencakup dashboard, index/create Kelas, serta index Jadwal; seluruhnya HTTP 200 dengan overflow 0 px, tanpa marker Bootstrap, aset Waka lama, link modul global, kebocoran URL Admin, atau error console. Enam statistik dashboard memakai satu baris desktop dan dua kolom mobile dengan label yang tetap terbaca.

Validasi final role Waka 16 September 2026: 11 tes terfokus lulus dengan 576 assertion untuk Dashboard/Kelas/Jadwal, Guru Pengajar, Wali Kelas, Manajemen Siswa, Monitoring, Catatan, regresi Admin/Ketua, dan IDOR cabang. Audit browser menelusuri 30 kombinasi route/viewport pada 1440x900 dan 390x844; semuanya HTTP 200, overflow 0 px, tanpa marker Bootstrap, aset Waka lama, link modul global, kebocoran URL Admin, atau error console. Tombol Cetak kartu pada detail siswa terukur putih (`rgb(255, 255, 255)`) dan terlihat di kedua viewport. Guard detail Guru juga menolak guru yang hanya ditugaskan pada cabang lain.

Gerbang akhir batch Waka: Pint bersih, cache Blade berhasil, build produksi berhasil, dan suite penuh menghasilkan 197 tes lulus dengan 2.297 assertion. Satu kegagalan tetap pada baseline fixture lama `SiswaImportStatusTest` yang tidak menyediakan `nama_kelas` serta `agama`; tes pasangan `SiswaImportStatusValidFixtureTest` dengan fixture lengkap lulus. Akun, server, script, dan screenshot audit sementara telah dibersihkan.

Pola capability Waka: fitur global tidak cukup di-hide dari sidebar. Named route, import controller, controller khusus, view, aset, dashboard shortcut, dan URL langsung harus ikut dicabut lalu dikunci dengan `Route::has(...) === false` dan respons 404. Waka tetap boleh membaca data referensi global yang diperlukan flow cabang, tetapi tidak memperoleh endpoint pengelolaannya.

Pola UI Wali Kelas berikutnya: periksa label, kontras, lebar dropdown, dan penempatan tombol di desktop/mobile; aksi per baris berada di pojok kanan atas, sedangkan banyak aksi sekunder masuk menu Aksi kanan yang tidak terpotong ancestor `overflow-hidden`. Jangan menggandakan kontrol bernama dalam DOM demi dua layout responsif karena browser dapat mengirim field sama dua kali. Untuk aksi daftar, satu dialog Alpine/HTML native yang menerima data baris lebih bersih daripada satu modal per baris. Pisahkan **status kehadiran** dari **status validasi izin**; form edit pending tidak boleh diam-diam menyetujui/menolak permohonan. Pisahkan `@method('PUT')` dari directive Blade di sekitarnya dan uji HTML hasil render memiliki `_method=PUT`. Hapus aset lokal hanya setelah referensi, view cache, build, feature test, dan audit browser lulus.

Pola cetak selanjutnya: selalu klasifikasikan route sebagai dokumen `layouts.print` atau `window.print()` dari shell. Periksa media print/PDF, bukan hanya HTML layar; pastikan seluruh chrome aplikasi dan floating control hilang, kop dan data tetap ada, mode edit tidak membocorkan input, tabel tidak terpotong, identitas penanggung jawab memakai relasi aktual, serta page break masuk akal. Wali Kelas sudah melewati gerbang cetak aktif; lanjut inventaris/audit route cetak role lain tanpa menghapus CSS/JS dokumen yang masih direferensikan.

Rancangan UX Nilai → Rapor Wali Kelas: show Nilai menampilkan siswa, kelas, tahun ajaran, semester, kelengkapan dan hasil per mapel dalam tabel desktop/kartu mobile. Edit Nilai mengelompokkan Tugas, Latihan, UH, PTS/PAS dan penilaian tingkat akhir per mapel; mobile memakai accordion/kartu, bukan matriks 20+ kolom, dengan tepat satu kontrol bernama per nilai. Preview/Sinkron dari Guru harus eksplisit agar edit wali tidak tertimpa. Setelah Simpan kembali ke detail siswa pada semester yang sama; CTA lanjut ke Rapor membawa konteks siswa/semester dan meminta jenis rapor sebelum membuatnya. Validasi server wajib mengunci siswa dan mapel ke kelas terpilih pada update, impor, sinkron, dan pembuatan rapor alternatif.

Pola backend laporan: fungsi tanggal/raw SQL wajib sadar driver database. Agregasi per hari tidak boleh langsung mengunci `DAY()` MySQL; route test SQLite harus berjalan bersama audit live MySQL agar halaman yang tampak benar di lokal tidak gagal pada environment test/deploy lain.

Backlog global: indeks pencarian menu belum sepenuhnya sadar role. Saat dikerjakan, sumber hasil harus mengikuti konfigurasi navigation/capability role aktif agar menu role lain tidak muncul lalu berakhir 403.

Validasi sidebar dan toolbar Jadwal terbaru: sidebar Admin, Waka, Sekretaris, Ketua, serta Bendahara mengunci semua kontrol tingkat utama ke border 0 px dan menyisakan border 1 px hanya pada submenu. Latar sidebar dibuat sedikit lebih muda namun tetap biru tua; empat tone menu terukur berbeda pada DOM nyata, sedangkan footer akun memakai gradien penuh dengan link transparan. Admin memakai empat aksi Jadwal, sedangkan Waka memakai tiga aksi tanpa Pengaturan Istirahat; mobile berada dalam grid setinggi 40 px dan desktop 1024/1440 px memakai flex dengan tinggi 44 px, font 14 px, serta padding horizontal terukur 16–20 px. Panel Aksi/Ekspor tetap berada di viewport, seluruh item `nowrap`, overflow dokumen 0 px, dan tidak ada error console. Bootstrap memberi `!important` pada utility spacing, sehingga breakpoint Tailwind campuran wajib memakai responsive important dan diverifikasi dari computed style.

Gerbang sebelum push 12 September 2026: Blade cache dan build produksi berhasil. Regresi gabungan keamanan penugasan/notifikasi, Jadwal Admin/Waka, seluruh flow Bendahara, filter Tagihan, serta komponen global menghasilkan 30 test lulus dengan 426 assertion. `git diff --check` bersih dan seluruh akun/file audit sementara telah dihapus.

Gerbang batch Jadwal Waka 15 September 2026: build produksi dan cache Blade berhasil; 14 tes regresi gabungan lulus dengan 222 assertion. Cakupan mencakup seluruh route render Admin/Waka, kebocoran namespace, IDOR cabang, mode multi-jenjang, aksi massal, serta duplikasi pivot multi-kelas. Audit browser pada 390x844 dan 1440x900 menelusuri index, create, import, detail kelas, dan pratinjau: seluruhnya HTTP 200, overflow 0 px, tanpa endpoint Admin, marker Bootstrap, aset Waka lama, atau error console. Dropdown Aksi/Ekspor saat terbuka terukur tetap di viewport (308 px mobile; 256 px desktop).

Suite penuh 15 September 2026: 194 tes lulus dengan 1.995 assertion. Dua assertion shell lama telah diselaraskan dengan warna sidebar yang sudah disetujui (`#17699f` dan gradasi `#245f91`). Satu-satunya kegagalan tersisa adalah fixture lama `SiswaImportStatusTest` yang tidak menyediakan kolom wajib `nama_kelas` dan `agama`; `SiswaImportStatusValidFixtureTest` dengan fixture lengkap lulus, sehingga kegagalan ini terisolasi dan tidak terkait batch Jadwal Waka.

## Standar tabel responsif

- Desktop (`lg` ke atas): gunakan `table-fixed` dan `colgroup`; lebar kolom harus mengikuti jenis datanya, bukan dibagi rata.
- Kolom checkbox dan nomor dibuat tetap serta sesempit mungkin. Status dan aksi juga memakai lebar tetap.
- Lebar kolom aksi dihitung dari jumlah tombol 36px beserta jaraknya; jangan memakai persentase yang meninggalkan ruang kosong lebar antara status dan aksi.
- Lebar kolom aksi harus mencakup total lebar tombol, seluruh gap, serta padding kiri/kanan. Kelompok aksi tidak boleh meluber ke kolom status; ukur jarak antarkeduanya pada DOM nyata setelah browser dirender.
- Nama, identitas, dan cabang memperoleh ruang fleksibel; teks panjang boleh dipotong dengan `truncate` serta `title` untuk nilai lengkap.
- Nilai pendek yang merupakan satu kesatuan, seperti `12 SMP`, status, tanggal ringkas, dan kode, wajib memakai `whitespace-nowrap`.
- Tablet dan ponsel: tampilkan kartu ringkas, bukan memaksa tabel desktop mengecil. Informasi utama tampil dahulu dan aksi tetap mudah disentuh.
- Ringkasan dengan tiga kartu tidak dipaksa menjadi tiga kolom pada ponsel bila label dan ikon saling berhimpitan. Gunakan kartu total selebar dua kolom, lalu dua status turunan di bawahnya.
- Setiap migrasi tabel baru harus diperiksa pada lebar ponsel, tablet, desktop sempit, dan desktop lebar sebelum dinyatakan selesai.

## Standar lebar konten

- Halaman daftar, dashboard, tabel, dan ringkasan admin wajib memakai `min-w-0 w-full`; jangan memakai `mx-auto max-w-*` pada pembungkus utama.
- Lebar konten mengikuti ruang shell saat browser di-zoom atau viewport berubah, sementara padding tetap dikendalikan oleh layout global.
- Batas `max-w-*` hanya boleh dipakai pada elemen yang memang perlu dibatasi agar mudah dibaca, seperti teks panjang atau dialog, bukan seluruh halaman.

## Standar interaksi UI

- Tailwind menjadi sumber tampilan; jangan membuat stylesheet khusus modul setelah view selesai dimigrasikan.
- Alpine digunakan untuk state UI ringan di view/component, seperti panel kondisional, dropdown, pilihan file, dan saran input.
- `resources/js/admin.js` hanya menampung perilaku lintas aplikasi seperti shell/sidebar, pencarian menu, notifikasi, SweetAlert, dan komponen Alpine yang benar-benar reusable.
- Seluruh item sidebar tingkat utama—tautan langsung dan pemicu dropdown—tidak memakai border/ring. Variasi tone biru yang halus membedakan menu, state aktif memakai kontras lebih kuat, dan border hanya digunakan pada item submenu. Footer akun diwarnai penuh agar terpisah dari area navigasi; jangan menumpuk kartu gradien di atas footer gelap.
- Komponen floating lintas-role dimiliki shell bersama, bukan disalin per role. Scroll-up dan seluruh UI AI Assistant sudah memakai markup/utility Tailwind shared. Stylesheet AI lama telah dihapus setelah alur buka/tutup, sidebar riwayat, upload, drag, dan ukuran viewport diuji.
- FAB Bantuan kini memakai utility Tailwind pada component shared. Audit posisi: pusat Bantuan dan scroll-up sejajar dengan selisih 0 px pada desktop, drag horizontal tetap bekerja, dan pada mobile kedua tombol bertumpuk pada sisi kanan dengan gap 14 px.
- Alpine tetap pilihan pertama. Satu modul JavaScript global hanya dibenarkan untuk komponen async lintas-role yang kompleks bila memindahkannya seluruhnya ke Blade justru membuat view membengkak; dilarang membuat salinan JS per role atau file JS per halaman.
- Flow dengan field dan hak aksi yang identik pada dua role memakai satu view bersama dengan konteks route eksplisit. Jangan menyalin Blade per role; feature test wajib memastikan URL form, edit, toggle, dan hapus tetap berada pada namespace role yang sedang aktif.
- Bila satu flow identik dipakai tiga role atau lebih, tentukan satu `routePrefix` dari konteks route di awal view lalu turunkan semua endpoint dari prefix tersebut. Jangan menyebar kondisi role pada setiap tombol/form, dan uji bahwa HTML tiap role tidak membocorkan endpoint role lain.
- Kesamaan tampilan tidak berarti kesamaan kewenangan. Sebelum membagi view, buat matriks capability per role; menu sensitif yang hanya dimiliki Admin harus dihapus dari route dan sidebar role lain, bukan sekadar disembunyikan dengan CSS. Tes wajib memeriksa ketidakadaan named route serta penolakan URL langsung.
- View bersama wajib menyaring seluruh affordance yang tidak dimiliki konteks role, termasuk checkbox, toolbar massal, handler Alpine, form tersembunyi, dan URL API. Tidak cukup hanya menyembunyikan tombol utama; HTML hasil render role terbatas tidak boleh memuat endpoint atau kontrol khusus Admin yang tidak dapat dipakai.
- Filter dropdown bukan kontrol otorisasi. ID relasi yang membawa capability wajib memakai satu scope/model rule yang sama pada query pilihan, request create/update, aksi satuan/massal, import, rebuild, dan notifikasi. Uji dengan ID valid milik role yang salah untuk memastikan request tidak dapat dimanipulasi.
- Reuse view lintas-role tidak boleh mewariskan otoritas role pemilik view. Scope kepemilikan wajib diterapkan konsisten pada index/statistik, show/edit, API/AJAX, create/update biasa dan mode alternatif, aksi satuan/massal, import, duplikasi, serta seluruh print/export. Tes harus memakai ID record cabang lain, bukan hanya memeriksa tombol yang terlihat.
- Jika satu orang memiliki beberapa akun, setiap jadwal, pekerjaan, dan notifikasi harus terikat ke akun yang role-nya memiliki capability terkait; nama yang sama tidak boleh dipakai untuk menyimpulkan kewenangan.
- Flow keputusan memakai satu dialog Alpine/HTML native yang menerima URL, identitas record, dan jenis aksi melalui state/data attribute. Jangan membuat satu modal untuk setiap baris atau menghidupkan kembali event Bootstrap; pilihan massal mengirim hidden input dari state Alpine yang sama.
- Modul JS AI Assistant tetap satu file global kohesif karena menangani API async, pemilihan model, lampiran, riwayat localStorage, lightbox, dan drag. Styling-nya wajib tetap Tailwind dan tidak boleh kembali membuat CSS komponen.
- Jangan membuat file JavaScript khusus halaman jika perilakunya dapat ditulis ringkas dengan Alpine atau input HTML native.
- CSS/JS khusus modul harus dihapus segera setelah semua view modul lolos Blade cache, build produksi, dan tes route.
- Aksi singkat yang hanya membutuhkan satu pilihan, seperti menugaskan wali kelas atau mengganti guru, dibuka sebagai panel/dialog pada halaman daftar. Halaman baru dipakai hanya bila alurnya memang panjang atau membutuhkan konteks detail.
- Daftar pilihan panjang tidak memakai `<select size>`; gunakan pencarian dan kartu radio yang tetap menampilkan identitas pembeda seperti cabang, jenjang, atau penugasan aktif.
- Input yang memiliki ikon di dalam field wajib memakai jarak kiri yang tahan terhadap compatibility CSS lama (`!pl-10` selama bridge legacy masih dimuat), lalu posisi ikon terhadap placeholder dan teks ketikan diuji pada mobile dan desktop.
- Audit input berikon tidak berhenti pada class sumber: periksa `padding-left` terhitung dan posisi ikon/teks pada DOM nyata, karena bridge CSS bersama dapat mengubah hasil akhirnya.
- Audit utility spacing kini melarang shorthand dan sumbu/sisi yang menulis properti sama pada satu elemen (`p-*` + `px/py/...`, `px-*` + `pl/pr-*`, `py-*` + `pt/pb-*`, serta pasangan margin). Sebanyak 22 kandidat pada Admin, Waka shared, dan komponen global telah dinormalisasi ke sumbu eksplisit; feature test akan gagal jika pola berisiko ini muncul lagi.
- Dropdown/popover harus diuji saat terbuka. Pada mobile, panel menggunakan lebar container dengan `inset-x-0` tanpa width eksplisit; anchor satu sisi dan lebar tetap baru diterapkan mulai `sm:` serta harus mengalahkan bridge Bootstrap bila masih dimuat. Item aksi memakai `whitespace-nowrap` agar label seperti “Duplikasi periode” tidak pecah. Guard otomatis menolak panel absolut berlebar tetap pada breakpoint dasar.
- Toolbar berisi empat aksi pendek dapat memakai grid empat kolom pada mobile dengan tipografi 10 px, padding/gap ringkas, dan tinggi sentuh minimal 40 px; mulai `sm:` kembali ke flex dan ukuran teks normal. Semua label harus tetap terlihat tanpa turun baris atau dipotong bila kombinasi tersebut masih muat.
- Pada halaman yang masih memuat bridge Bootstrap, utility spacing Bootstrap dapat membawa `!important` dan mengalahkan utility responsif Tailwind. Gunakan `sm:!px-*`/`lg:!px-*` bila elemen juga memiliki class spacing dasar yang bentrok, lalu ukur computed padding, dimensi tombol, dan font pada desktop maupun mobile.
- Verifikasi regresi terakhir: build produksi dan Blade cache berhasil; 41 feature test lintas Admin, global, Sekretaris, Promotion, dan Tagihan lulus dengan 973 assertion. Pengukuran live ketiga dropdown pada 390x844 menghasilkan overflow kiri/kanan/dokumen 0 px tanpa error console.
- Alur berurutan seperti duplikasi periode harus memperlihatkan sumber lalu tujuan, mencegah pilihan yang sama, dan menjelaskan data mana yang disalin atau tidak ditimpa.
- Tiga aksi filter utama pada mobile memakai grid tiga kolom yang stabil; label dapat dipendekkan secara responsif, tetapi ketiga aksi tetap sejajar dan tidak turun sendiri ke baris berikutnya.
- Navigasi editor panjang memakai panel sticky setinggi viewport pada desktop. Pada mobile gunakan bilah sticky dengan select bagian dan tombol simpan; section tujuan perlu `scroll-margin` dan state navigasi mengikuti section yang sedang terlihat.
- Hindari `overflow-x-hidden` pada ancestor panel sticky karena dapat membentuk scroll container yang mematikan perilaku sticky; gunakan `overflow-x-clip` bila hanya perlu memotong luapan horizontal.
- Toolbar pilihan massal atau elemen `position: fixed` yang berada di dalam shell ditampilkan melalui Alpine `x-teleport="body"`, lalu posisi akhirnya diperiksa dari `getBoundingClientRect()` pada browser nyata agar tidak terikat transform parent.
- Pengujian live untuk aksi destruktif hanya membuka dan membatalkan SweetAlert; jangan mengonfirmasi submit terhadap database pengembangan kecuali pengujian memang dibuat di transaksi/fixture terisolasi.

## Standar dokumen cetak dan ekspor

- Audit cetak wajib dimulai dari route, controller, dan view akhir; jangan berasumsi namespace admin pasti merender view admin karena sebagian controller mewarisi role lain.
- Audit “full Tailwind” wajib memeriksa DOM hasil render beserta layout dan component yang disertakan; pencarian yang hanya dibatasi ke `resources/views/admin` tidak cukup untuk menemukan ketergantungan legacy dari shell bersama.
- Halaman pratinjau/cetak HTML admin memakai `layouts.print`, utility `print:` Tailwind, serta bundle global; tidak boleh membawa Bootstrap, CSS halaman, JS halaman, `<style>`, atau atribut `style`.
- Kartu siswa memakai slot `page-content` pada `layouts.print` agar dimensi kartu identitas tetap khusus tanpa membuat bundle atau layout cetak baru.
- PDF DomPDF dan lembar Excel adalah template dokumen server-side, bukan UI browser. CSS dokumen atau atribut style spreadsheet hanya boleh dipakai bila diperlukan renderer, tidak boleh memuat interaksi Bootstrap, dan harus dicatat sebagai pengecualian teknis.

## Standar tombol aksi tabel

- Aksi ikon ringkas pada tabel desktop wajib memakai `<x-cleanflow.table-action>`; jangan mencampur tombol manual di dalam kelompok aksi yang sama.
- Ukuran baku tombol adalah `h-9 w-9`, `rounded-lg`, ikon `text-xs`, dan `ring-1 ring-inset`. Jarak antartombol memakai `gap-1.5` atau `gap-2` secara konsisten pada satu tabel.
- Untuk payload dinamis Alpine pada atribut komponen Blade, jangan menaruh `@js(...)` langsung di atribut `<x-cleanflow.table-action>`. Simpan nilai yang sudah di-escape dalam atribut `data-*`, lalu baca melalui `$el.dataset...`; setelahnya uji klik secara live untuk memastikan dialog atau aksi benar-benar berjalan tanpa error Alpine.
- Tone baku: biru untuk detail, amber untuk edit/unggulan, merah untuk hapus, emerald untuk aktif/sukses, violet untuk visibilitas, cyan untuk salin/informasi, dan slate untuk aksi netral.
- Semua tombol wajib memiliki `aria-label` serta `title` yang menjelaskan aksinya. State hover, focus, loading, dan disabled tidak boleh mengubah dimensi tombol.
- Pada mobile, aksi dapat berupa tombol berlabel selebar kartu agar mudah disentuh; konsistensi ukuran ikon persegi ini khusus kelompok aksi ringkas.
- Saat mengubah satu tabel lama, audit seluruh tombol dalam kolom Aksi—termasuk toggle visibilitas, unggulan, aktif/nonaktif, salin, dan aksi tambahan—bukan hanya CRUD utama.

## Standar kontras dan state

- Gunakan hanya token warna yang tersedia di `tailwind.config.js`. Utility dinamis atau shade yang tidak terdaftar dilarang karena dapat membuat teks mewarisi warna yang salah.
- Judul dan metadata di atas hero berwarna wajib memiliki warna eksplisit; jangan hanya mengandalkan pewarisan dari pembungkus.
- Teks, ikon, dan latar tombol harus terbaca pada state normal, hover, focus, active, dan disabled. Pemeriksaan tidak boleh hanya dilakukan saat cursor diarahkan.
- Jangan menentukan warna tombol memakai selector posisi seperti `[&_a:first-child]` bila aksi sebelumnya dapat disembunyikan berdasarkan role. Beri setiap tombol warna teks eksplisit (`text-white`, dan bila perlu `hover:!text-*`) lalu verifikasi `getComputedStyle()` pada semua konteks role; urutan DOM bukan kontrak capability.
- Pilihan kelas harus ditulis minimal sebagai `nama kelas · jenjang · cabang` dan memakai ID kelas sebagai value ketika nama kelas mungkin berulang.

## Standar struktur file

- Jangan memecah satu halaman menjadi banyak partial kecil. Satu file view utama lebih mudah dirawat bila markupnya hanya dipakai oleh halaman tersebut.
- Create dan edit yang memiliki field sama memakai satu view `form.blade.php` melalui controller, sehingga tidak menduplikasi markup dan tidak menambah wrapper tipis.
- Component/partial hanya dipakai untuk pola yang benar-benar berulang lintas banyak modul, misalnya tombol aksi tabel dan header dokumen cetak.
- Saat audit ulang modul lama, wrapper create/edit dan partial satu-pemakai digabung secara bertahap setelah tes regresinya tersedia.
- Wrapper daftar dan empty state tidak boleh memakai `max-w-*` sebagai pembatas halaman. Empty state boleh membatasi lebar paragraf, tetapi card induknya tetap memenuhi ruang shell saat zoom berubah.

## Detail Akademik/Publikasi

| Alur | Daftar | Form | Detail/cetak | Aset lokal aktif |
|---|---|---|---|---:|
| Pengumuman | Selesai | Selesai | - | 0 |
| Berita | Selesai | Selesai | - | 0 |
| Flyer | Selesai | Selesai | - | 0 |
| Kalender Akademik | Selesai | Selesai | Selesai | 0 |
| Kenaikan Kelas | Selesai | Selesai | Selesai | 0 |

## Detail modul Pengguna

| Alur | Index | Create | Edit | Show | Import | Print |
|---|---|---|---|---|---|---|
| Tenaga Pendidik | Selesai | Selesai | Selesai | Selesai | Selesai | Selesai |
| Wali Siswa | Selesai | Selesai | Selesai | Selesai | Selesai | Selesai |
| Siswa | Selesai | Selesai | Selesai | Selesai | Selesai | Selesai |

## Detail Keuangan

| Alur | View selesai | View tersisa | Aset lokal aktif | Status |
|---|---:|---:|---:|---|
| Konfigurasi pembayaran | 1 | 0 | 0 | Selesai |
| Validasi akses ujian & rapor | 1 | 0 | 0 | Selesai |
| Validasi & riwayat dispensasi | 2 | 0 | 0 | Selesai |
| Pembayaran | 5 | 0 | 0 | Selesai |
| Tagihan | 11 | 0 | 0 | Selesai |
| Laporan pembayaran | 6 | 0 | 0 | Selesai |
| **Total alur inti** | **26** | **0** | **0** | **Selesai** |
