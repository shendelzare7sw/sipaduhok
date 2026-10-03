# CleanFlow — Ringkasan Konteks, Aturan, dan Prompt Multi-Agent

Pembaruan terakhir: 2 Oktober 2026
Branch kerja: `cleanflow`
Fokus aktif: Admin, fondasi global, Sekretaris, Ketua PKBM, Bendahara, Waka, dan Wali Kelas selesai untuk alur aktif. Wali Kelas memakai Tailwind/Alpine tanpa aset CSS/JS halaman; dokumen cetak Nilai siswa juga sudah memakai Tailwind, sedangkan 11 aset public cetak yang masih aktif tetap dipertahankan untuk kesetiaan desain. Wali Siswa selesai untuk alur aktif dan Siswa shell SIA selesai (lihat bagian 15). Berikutnya Guru (SIA/LMS), LMS Siswa, Latihan/Ujian, dan audit cetak lintas-role. Guru dan Siswa memiliki shell SIA serta LMS yang sengaja boleh berbeda secara visual. Layout khusus Latihan dan Ujian pada Guru/Siswa adalah zona preservasi: konversi implementasinya ke Tailwind tanpa mengubah tata letak, warna, hirarki, atau alur interaksi yang sudah dikenal pengguna.

## 1. Tujuan utama

Pola floating global: pada desktop tombol Bantuan berukuran konsisten 132 x 56 px, scroll-up 56 x 56 px, keduanya sejajar pusat, berjarak minimal 24 px, dan batas drag Bantuan tidak boleh memasuki lajur scroll-up; pada mobile keduanya tetap tersusun vertikal. Jangan mengandalkan padding untuk lebar FAB karena utility reset dapat berkonflik—gunakan dimensi desktop eksplisit. Payload jawaban AI wajib dinormalisasi sebelum render. JSON lengkap, JSON terbungkus string, maupun JSON yang terpotong harus dipulihkan menjadi teks, callout, tombol, dan tautan terkait yang aman; payload transport tidak boleh terlihat mentah dan migrasi localStorage harus merapikan riwayat lama dengan pola yang sama.

CleanFlow bukan sekadar mengganti warna atau class Bootstrap. Target akhirnya adalah:

1. Pengguna baru langsung memahami harus mulai dari menu apa.
2. Navigasi dan urutan kerja mengikuti tujuan pengguna, bukan nama teknis modul.
3. Tampilan konsisten, profesional, minimalis, mobile-first, dan tetap fleksibel di desktop.
4. Fungsi bisnis, validasi, hak akses, route, dan struktur data tetap bekerja seperti sebelumnya.
5. Tampilan memakai Tailwind sebagai sumber CSS dan Alpine untuk interaksi ringan.
6. Kode halaman tidak lagi bergantung pada file CSS/JS khusus yang tersebar.
7. Konfirmasi berbahaya dan dialog konsisten memakai SweetAlert atau dialog Tailwind/Alpine.

## 2. Batasan pekerjaan

### Boleh dilakukan

- Mengubah layout, hierarki visual, posisi tombol, pengelompokan menu, dan urutan informasi.
- Mengganti tabel desktop menjadi kartu pada ponsel.
- Menggabungkan create/edit yang memiliki field sama ke satu `form.blade.php`.
- Mengubah aksi singkat menjadi dialog/panel pada halaman yang sama.
- Mengganti JavaScript halaman dengan state Alpine atau input HTML native.
- Memperbaiki fallback gambar/file, label pilihan, overflow, kontras, dan state tombol.
- Menghapus CSS/JS/view mati setelah referensinya terbukti tidak digunakan dan seluruh validasi lulus.

### Tidak boleh dilakukan

- Mengubah logika bisnis, hasil perhitungan, validation rule, scope data, atau hak akses tanpa kebutuhan bug yang terbukti.
- Mengganti nama field, route, method HTTP, parameter, dan contract controller secara sembarangan.
- Menghapus fitur hanya karena sulit dipindahkan ke UI baru.
- Menggunakan Bootstrap untuk halaman yang sudah dimigrasikan.
- Menambahkan `<style>`, style inline, atau stylesheet khusus halaman baru.
- Membuat file JavaScript halaman jika perilakunya dapat ditulis ringkas dengan Alpine.
- Memecah satu halaman menjadi banyak partial satu-pemakai.
- Menggunakan lebar pembungkus utama `max-w-*` pada dashboard/list/table sehingga timbul gap saat zoom.
- Menyatakan modul selesai hanya karena halaman index sudah berubah; create, edit, show, import, print, modal, dan partial terkait harus ikut diaudit.
- Menghapus file sebelum memastikan tidak ada referensi aktif dan runtime sudah lulus.

## 3. Arsitektur tampilan yang dipakai

- Layout pascalogin memakai shell CleanFlow dari `layouts.app`/layout terkait.
- Tailwind adalah sumber tampilan utama.
- Alpine dipakai langsung pada Blade untuk dropdown, dialog, state kondisional, preview upload, pencarian lokal, dan pilihan panjang.
- `resources/js/admin.js` hanya untuk perilaku lintas aplikasi: shell/sidebar, menu search, notifikasi, SweetAlert, scroll-up, bantuan, dan komponen Alpine yang benar-benar reusable.
- Floating shell tidak boleh disalin per role. Scroll-up dimiliki `components/cleanflow/app-footer`; seluruh UI AI Assistant juga sudah menjadi Tailwind pada satu component shared. Keduanya berlaku lintas-role.
- Alpine adalah pilihan pertama. Satu modul JavaScript global masih boleh untuk komponen async lintas-role yang benar-benar kompleks bila memindahkan seluruh logika ke Blade merusak keterbacaan; AI Assistant adalah contoh yang sah karena menangani API, upload, riwayat lokal, lightbox, dan drag. Modul ini tidak boleh disalin per role atau dijadikan JS halaman.
- `vite.config.js` menemukan aset berdasarkan referensi Blade. Jangan menambah daftar entry manual per halaman.
- Token warna wajib berasal dari `tailwind.config.js`, terutama `brand-50` sampai `brand-950`.
- Font utama adalah Plus Jakarta Sans melalui konfigurasi global.

## 4. Pola layout dan responsive

### Pembungkus halaman

- Gunakan `min-w-0 w-full` pada pembungkus utama.
- Gunakan `space-y-5` atau jarak konsisten yang tidak berlebihan.
- Jangan membatasi lebar halaman daftar/dashboard dengan `mx-auto max-w-*`.
- `max-w-*` hanya untuk dialog atau blok teks panjang yang memang perlu dibatasi.
- Konten harus memenuhi ruang shell ketika viewport berubah atau browser di-zoom.

### Mobile-first

- Statistik ringkas biasanya 2 kartu per baris pada ponsel; 3/4/lebih pada desktop sesuai data.
- Hindari kartu tinggi dengan ruang kosong besar.
- Tombol utama harus mudah disentuh, minimum sekitar 40–44 px.
- Toolbar berubah dari kolom pada ponsel menjadi baris pada desktop.
- Tidak boleh ada horizontal overflow pada dokumen utama.
- Jika data benar-benar memerlukan lebar besar, scroll hanya boleh terjadi di pembungkus internal.

### Kontras

- Semua judul pada hero berwarna harus memakai warna eksplisit, umumnya `!text-white`.
- Jangan mengandalkan warna warisan dari parent.
- Periksa normal, hover, focus, active, disabled, loading, dan selected.
- Teks sekunder tetap harus terbaca; hindari abu-abu terlalu pucat di atas biru.

## 5. Pola tabel data

### Desktop (`lg` ke atas)

- Gunakan `table-fixed` dan `colgroup`.
- Checkbox/nomor dibuat tetap dan sempit.
- Status dan aksi memiliki lebar tetap.
- Nama/deskripsi/cabang memperoleh kolom fleksibel.
- Gunakan `truncate` dan `title` untuk nilai panjang.
- Gunakan `whitespace-nowrap` untuk nilai satu kesatuan seperti `12 SMP`, kode, tanggal, dan status.
- Aksi CRUD menggunakan ukuran, warna, ikon, dan jarak yang konsisten; prioritaskan `x-cleanflow.table-action` bila cocok.
- Kolom aksi memakai lebar tetap yang dihitung dari jumlah tombol (`36px` per tombol + gap), bukan persentase lebar tabel. Hindari ruang kosong lebar antara status dan aksi.

### Mobile/tablet

- Jangan memaksa tabel desktop mengecil.
- Buat kartu ringkas dengan informasi utama dahulu, metadata sesudahnya, lalu tombol aksi.
- Label harus menjelaskan konteks data, bukan hanya menyalin header tabel.

## 6. Pola form

- Create dan edit yang memiliki field sama memakai satu `form.blade.php`.
- Judul dan subtitle menjelaskan apakah pengguna sedang membuat atau memperbarui data.
- Tampilkan ringkasan error di atas serta error dekat field.
- Kolom wajib diberi tanda yang konsisten.
- Kelompokkan field berdasarkan maksud: identitas, periode, target, status, dan lampiran.
- Gunakan grid satu kolom di ponsel dan 2/3 kolom hanya bila nilai cukup pendek.
- Upload gambar memakai preview Alpine dalam file Blade yang sama.
- Input yang memiliki ikon di dalam field wajib memberi jarak aman dengan utility penting (misalnya `!pl-10`) selama compatibility CSS legacy masih aktif; cek jarak ikon, placeholder, teks ketikan, serta tombol hapus di mobile dan desktop.
- Hindari utility spacing yang menulis properti CSS sama pada satu elemen, termasuk lintas breakpoint: jangan gabungkan `p-*` dengan `px/py/pt/pr/pb/pl-*`, `px-*` dengan `pl/pr-*`, `py-*` dengan `pt/pb-*`, atau pola margin setara. Uraikan sejak awal menjadi sumbu/sisi eksplisit agar hasil tidak bergantung pada urutan CSS hasil build.
- Dropdown/popover mobile tidak boleh memakai lebar tetap dan anchor satu sisi terhadap tombol (`absolute right-0 w-64`, misalnya). Pada mobile, anchor panel ke container aksi selebar baris dengan `inset-x-0 w-auto`; aktifkan kembali `sm:right-0 sm:w-*` hanya pada breakpoint yang ruangnya cukup. Ukur `left >= 0` dan `right <= viewportWidth` pada DOM nyata ketika panel terbuka.
- Pilihan kondisional menggunakan `x-show`, `x-model`, dan `:required`.
- Tombol Batal dan Simpan berada pada footer form yang konsisten.
- Panel panduan hanya ditampilkan jika benar-benar membantu flow, bukan dekorasi.

## 7. Pola flow dan navigasi

- Menu awal tahun mengikuti urutan: Tahun Ajaran → Cabang → Pengguna → Kelas/Penugasan → Mata Pelajaran → Jadwal.
- Dashboard menampilkan pekerjaan berdasarkan tujuan pengguna, bukan istilah modul.
- Search pada topbar digunakan untuk menemukan menu atau pekerjaan.
- Pilihan kelas minimal menampilkan `nama kelas · jenjang · cabang` dan memakai ID kelas sebagai value.
- Daftar pilihan panjang tidak menggunakan `<select size>`; gunakan pencarian dan kartu radio/hasil.
- Aksi satu langkah seperti atur wali atau ganti guru tampil sebagai dialog/panel pada halaman yang sama.
- Alur kompleks seperti duplikasi periode harus menunjukkan sumber → tujuan, mencegah pilihan sama, dan menjelaskan data yang akan disalin.
- Untuk aksi massal, pengguna harus melihat jumlah pilihan, scope filter, serta akibat tindakan sebelum konfirmasi.

## 8. Dialog, konfirmasi, dan feedback

- Hapus, logout, rollback, proses massal, pembatalan, dan aksi berisiko menggunakan SweetAlert.
- Form standar dapat memakai atribut global `data-confirm`, `data-confirm-title`, `data-confirm-message`, dan `data-confirm-text`.
- Dialog kompleks boleh memakai `<dialog>`/Alpine dengan tampilan Tailwind.
- Jangan membuat modal Bootstrap.
- Loading harus mencegah submit ganda.
- Pesan sukses/gagal harus menjelaskan hasil tindakan, bukan hanya “berhasil” atau “error”.

## 9. Struktur file dan clean code

- Satu halaman tetap satu view utama jika markup hanya digunakan di sana.
- Jangan membuat wrapper/partial kecil tanpa reuse lintas modul.
- Reuse component hanya untuk pola yang nyata-nyata lintas banyak modul.
- Jangan membuat pasangan `resources/css/.../index.css` dan `resources/js/.../index.js` setelah migrasi.
- Alpine ringkas tetap berada di Blade; perilaku global/reusable saja yang masuk `resources/js/admin.js`.
- Komponen floating yang muncul pada banyak role wajib dimiliki layout/component bersama dan diuji minimal pada satu halaman tiap jenis shell; jangan memperbaikinya hanya melalui namespace admin.
- Jangan membuat CSS custom di Blade maupun file terpisah.
- Selalu cari referensi dengan `rg` sebelum menghapus file.
- Pertahankan perubahan pengguna lain pada worktree yang sudah kotor.

## 10. Gerbang penyelesaian setiap modul

Urutan wajib sebelum modul ditandai selesai:

1. Audit seluruh view, route, controller, model accessor, aset, dan test terkait.
2. Catat contract field/action/variable yang wajib dipertahankan.
3. Migrasikan semua view aktif dalam modul.
4. Cari ulang Bootstrap, `<style>`, style inline, dan referensi aset lokal.
5. Jalankan `php artisan view:clear` lalu `php artisan view:cache`.
6. Jalankan `npm run build`.
7. Uji route live dengan akun dummy pada desktop dan mobile.
8. Periksa status HTTP, error console/page, Alpine, gambar/file rusak, dan overflow horizontal.
9. Uji interaksi non-destruktif: dropdown, filter, dialog, preview, tab, dan navigasi.
10. Jalankan test terkait; jalankan seluruh suite secara berkala.
11. Hapus aset lama hanya bila tidak ada referensi dan seluruh pemeriksaan lulus.
12. Hapus folder kosong dan perbarui `docs/cleanflow-admin-progress.md`.

Viewport minimum pengujian:

- Mobile: 390 × 844.
- Desktop: 1440 × 900.
- Tambahkan desktop sempit/tablet bila halaman memiliki tabel atau dialog kompleks.

Lingkungan live lokal:

- Aplikasi: `http://sipaduhok.test`
- Vite dev: `http://[::1]:5174`
- Akun dummy admin: `admin1` / `password`
- Database berisi data dummy dan boleh digunakan untuk pemeriksaan tampilan; hindari aksi destruktif bila tidak diperlukan.

## 11. Progres saat ini

Modul admin selesai:

- Dashboard Admin
- Tahun Ajaran
- Cabang
- Pengguna
- Catatan
- Mata Pelajaran
- Pengaturan Istirahat
- Kelas
- Jadwal Pelajaran
- Guru Pengajar
- Wali Kelas
- Manajemen Siswa
- Monitoring
- Recovery Ticket
- AI Settings
- LMS Settings
- Akademik/Publikasi: Pengumuman, Berita, Flyer, dan Kalender Akademik
- Akademik/Publikasi: Kenaikan Kelas (`settings`, `kkm`, `rekap`, dan `print`)
- Keuangan: konfigurasi, validasi, dispensasi, Pembayaran, seluruh Tagihan, Laporan Pembayaran, dan enam jalur cetak admin (26 view, 0 aset UI lokal)
- Laporan admin: dua kelompok route memakai satu pusat laporan dan tujuh template cetak bersama (8 view, 0 aset UI lokal); view `admin/cetak-laporan` yang duplikat sudah dihapus.
- Landing Page: index, editor semua slug, dan item editor memakai Tailwind + Alpine (3 view, 0 aset UI lokal).

Dua puluh satu modul admin telah melewati migrasi view utama tanpa aset halaman khusus. Monitoring LMS bersama kini selesai untuk index, detail kelas, dialog catatan, serta preview materi/tugas/ujian; delapan aset CSS/JS lamanya telah dihapus setelah audit referensi dan runtime.

Fondasi global yang dipakai seluruh role kini juga selesai untuk Notification (index/detail), Profile, Account Settings, scroll-up, serta UI AI Assistant. Notification dan Profile memakai Tailwind + Alpine/HTML native tanpa aset halaman. Lima aset lamanya dihapus. Stylesheet AI shared juga dihapus; satu modul JS AI tetap dipertahankan sebagai modul global kohesif karena memuat alur async/API, upload, riwayat, dan drag—bukan styling atau perilaku satu halaman.

Catatan validasi terakhir:

- Build produksi berhasil.
- Pemeriksaan live seluruh Kenaikan Kelas berhasil pada 12 kombinasi route/viewport desktop dan mobile tanpa overflow, error console, atau overlay Vite.
- Empat test Promotion lulus dengan 23 assertion.
- Konfigurasi pembayaran lolos test backend 6 assertion serta live test mobile/desktop tanpa overflow atau error runtime.
- Validasi dan riwayat dispensasi lolos live test mobile/desktop; dialog pengajuan satuan dan massal berfungsi tanpa Bootstrap.
- Seluruh alur Pembayaran (index, riwayat siswa, input tunai, detail, dan kwitansi) lolos Blade cache dan audit live tanpa overflow/error console; aset halaman dan aset publik kwitansi sudah dihapus.
- Tagihan index/show/import/edit lolos audit live pada desktop dan 390 px. Filter kelas menyertakan cabang, aksi tabel konsisten 36×36, drag-and-drop serta mask nominal berfungsi, dan dialog simpan/hapus/reset memakai SweetAlert.
- Seluruh Tagihan lanjutan (duplikasi, carryover, massal, custom, Generate SPP, cetak tagihan siswa, dan cetak laporan) serta tiga Laporan Pembayaran admin beserta tiga halaman cetaknya telah dimigrasikan. Uji live desktop 1440x900 dan mobile 390x844 lulus tanpa overflow/error; dialog berisiko hanya dibuka lalu dibatalkan.
- Pusat Laporan umum dan Cetak Laporan telah disatukan ke delapan view Tailwind. Seluruh 15 route diuji live pada 1440x900 dan 390x844: HTTP 200, tanpa overflow dokumen, error console, overlay Vite, atau markup legacy.
- Landing Page index dan 14 editor slug telah diuji pada desktop/mobile. Navigasi bagian, visibilitas, cloning nama field, tambah/hapus item, preview gambar, dan dialog reset berjalan melalui Alpine tanpa Bootstrap atau aset halaman terpisah. Beberapa gambar dummy lama mengarah ke file storage yang sudah tidak ada; pratinjau menyembunyikannya tanpa merusak layout.
- Folder aset halaman khusus Keuangan, Laporan, dan Landing Page, dua aset cetak publik Tagihan, serta view laporan duplikat telah dihapus setelah audit referensi. Tes regresi terfokus terakhir: 14 tes, 503 assertion lulus.
- `/admin/keuangan/tagihan/255/cetak`, index Tagihan, dan kartu siswa telah diuji ulang pada 1440x900 serta 390x844: HTTP 200, tanpa overflow atau error console. Halaman cetak tidak memiliki marker Bootstrap; padding kiri pencarian Tagihan terhitung 40px.
- Validasi dispensasi memperoleh kolom aksi yang menghitung lebar tombol dan padding; Riwayat Dispensasi memakai susunan stat card 1+2 pada mobile; tiga aksi filter Laporan Keuangan tetap satu baris; wrapper Catatan kini memenuhi lebar shell.
- Seluruh Monitoring LMS shared telah didesain ulang dengan Tailwind + Alpine/HTML native dan delapan aset lokalnya dihapus. Audit live langsung mencakup detail kelas serta preview materi, tugas, dan ujian pada desktop/mobile; audit menemukan lalu mengunci perbaikan parse preview ujian.
- Audit browser terakhir menelusuri 36 tautan menu admin pada 1440x900 dan 390x844 tanpa HTTP error, overflow dokumen, error JavaScript, atau overlay Vite.
- FAB Bantuan shared telah dipindahkan ke utility Tailwind tanpa selector CSS khusus; pusatnya sejajar dengan scroll-up pada desktop, drag horizontal tetap aktif, dan keduanya bertumpuk dengan gap 14 px pada mobile.
- Notification, Profile, dan Account Settings diuji live pada 1440x900 serta 390x844: seluruh route HTTP 200, overflow 0 px, tanpa error console atau overlay Vite. Jendela AI dan sidebar riwayat berhasil dibuka pada kedua viewport dan seluruh bounding box tetap di dalam layar.
- Audit Profile menemukan bug lama pemetaan nomor: input `no_telepon` kini benar-benar disimpan ke `siswa.telepon_orangtua` atau `tenaga_pendidik.telepon` sesuai role.
- Full test suite 15 September 2026: 194 lulus (1.995 assertion), 1 gagal pada fixture lama `SiswaImportStatusTest` karena baris fixture tidak memiliki `nama_kelas` dan `agama`; test pendamping `SiswaImportStatusValidFixtureTest` dengan fixture lengkap lulus. Dua assertion shell yang masih mengunci warna sidebar lama telah diperbarui ke kontrak visual yang disetujui, sehingga kegagalan tersisa tidak terkait migrasi Jadwal Waka.
- Role Sekretaris selesai: dashboard dan sidebar baru, Berita/Flyer/Kalender/Pengumuman memakai sembilan view Tailwind bersama dengan Admin, 17 aset role + 2 aset dashboard serta sembilan view duplikat dihapus. Empat feature test dengan 97 assertion dan audit live 22 kombinasi route/viewport lulus; feed kalender serta dua PDF tetap aktif.
- Audit regresi lintas Admin, Waka shared, Sekretaris, dan komponen global menormalkan 22 benturan shorthand/sumbu spacing. Dropdown Buat Tagihan, Aksi Jadwal, dan Ekspor Jadwal sekarang memakai panel mobile selebar container; pengukuran live 390x844 menunjukkan overflow kiri, kanan, dan dokumen 0 px. Guard spacing/panel permanen, build produksi, Blade cache, serta 41 tes dengan 973 assertion lulus.
- Role Ketua PKBM selesai: dashboard/sidebar dan halaman keputusan khusus memakai Tailwind + Alpine/native dialog; Monitoring, Laporan/cetak, Catatan, dan Riwayat Dispensasi memakai view bersama yang sadar namespace route. Lima belas view duplikat, 19 aset CSS/JS role, dan stylesheet dashboard dihapus. Audit live 16 kombinasi route/viewport menunjukkan HTTP 200 dan overflow 0 px tanpa error console; 13 tes terfokus lulus dengan 169 assertion.
- Role Bendahara selesai: dashboard/sidebar, validasi dispensasi, Validasi Akses, lima halaman Pembayaran, sepuluh halaman Tagihan, dan enam Laporan/cetak memakai Tailwind + Alpine. Dua puluh empat view duplikat dan 31 aset role telah dihapus; folder Pembayaran, Tagihan, dan Laporan yang kosong turut dibersihkan. Dashboard memakai Alpine langsung tanpa CSS/JS halaman. Config Pembayaran tetap khusus Admin. Pengujian juga menemukan lalu memperbaiki agregasi laporan `DAY()` agar kompatibel MySQL, PostgreSQL, dan SQLite.
- Role Waka selesai: sidebar, dashboard, Data Kelas, Jadwal Pelajaran, Guru Pengajar, Wali Kelas, Manajemen Siswa, Monitoring, dan Catatan memakai Tailwind. Flow identik memakai view Admin shared yang sadar namespace/capability; namespace Waka kini hanya memiliki dashboard/sidebar, 0 referensi `@vite` halaman, dan 0 aset CSS/JS role. Enam kartu statistik dashboard sejajar pada desktop.
- Role Wali Kelas selesai untuk alur aktif: semua halaman kerja, termasuk Nilai/Rapor, Promosi, Validasi/Template, dan Arsip, memakai Tailwind + Alpine/dialog native. Total 30 view dengan 0 `@vite` halaman kerja; empat view dokumen Rapor dan cetak Nilai per siswa memakai dua entry Tailwind tanpa preflight. Enam CSS dan lima JS public **khusus dokumen pratinjau/cetak Nilai/Rapor** tetap aktif dan direferensikan; CSS tabel, watermark, `@page`, serta page break bukan sampah. Bootstrap Icons CDN pada ketiga dokumen Nilai diganti SVG lokal. View pratinjau dan cetak rapor generik beserta asetnya dihapus karena tidak direferensikan.
- Gerbang batch pertama Wali Kelas: 7 tes/125 assertion lulus, termasuk keadaan tanpa assignment dan IDOR; cache Blade, build produksi, Pint, serta audit browser 10 kombinasi halaman/viewport (1440/390 px) berhasil tanpa overflow, marker Bootstrap, aset lama, atau error JavaScript. Fixture dan berkas audit sementara telah dibersihkan. Pada tahap ini masih ada 17 view beraset lokal; angka terbaru dicatat pada batch Presensi di bawah.
- Gerbang batch Presensi Wali Kelas: 5 tes/185 assertion lulus; cache Blade, build produksi, Pint, dan audit browser 14 kombinasi halaman/viewport (1440/390 px) lulus tanpa overflow, marker Bootstrap, aset lama, atau error JavaScript. Sidebar mobile terbuka, submenu Presensi bisa dipilih, trigger tanpa border dan submenu berbatas 1 px. Dialog impor, simpan, tolak izin, dan edit riwayat diuji terbuka serta dapat dibatalkan. Fixture, screenshot, dan skrip audit sementara telah dibersihkan. Domain Laragon `sipaduhok.test` sempat 502 akibat FastCGI lokal port 9003/9004 berhenti; audit akhir memakai server aplikasi lokal port 8125. Listener Laragon kemudian pulih dan domain login kembali HTTP 200 tanpa perubahan konfigurasi.
- Koreksi cetak Wali Kelas setelah temuan pengguna: `/wali/presensi/show-harian` menggunakan `window.print()` pada `layouts.app`, sehingga top bar dan tombol floating semula ikut masuk ke PDF. Shell aplikasi kini menyembunyikan sidebar, top bar, footer, pencarian, Bantuan, scroll-up, dan modal saat media print; detail harian menyediakan kop kelas/tanggal yang hanya tampil saat cetak, dan bila mode edit terbuka data baca-saja tetap dicetak. Route cetak rekap Presensi dan jadwal yang sudah dimigrasikan diverifikasi dengan media print browser serta PDF A4 pada desktop/mobile; keduanya memakai `layouts.print` tanpa shell. Nama wali pada kedua dokumen kini mengambil relasi assignment multi-wali, dengan fallback field lama. Wali Kelas telah selesai, sehingga audit cetak role lain boleh dimulai tetapi tidak boleh menganggap perubahan shell global otomatis cukup.
- Alur Nilai → Rapor sudah dimigrasikan: index/show/edit Nilai dan index/edit/request-download Rapor responsif dengan satu set input bernama, penanganan nilai 0 terpisah dari kosong, dialog tindakan bersama, serta filter siswa/semester yang mempertahankan konteks. Validasi backend mengunci siswa, mapel, dan RaporNilai ke kelas/rapor aktif; ID lintas-scope ditolak. Daftar Rapor menaruh Generate/Upload PDF atau menu Aksi pada kanan atas baris di mobile maupun desktop. Audit browser Rapor index/edit dan dokumen Nilai selesai pada 390/1440 px tanpa overflow dan error JavaScript.
- Bug edit riwayat Presensi: directive `@method('PUT')` yang muncul sebagai teks menyebabkan form mengirim POST ke route PUT. Directive dipisah dan HTML render diuji menghasilkan `_method=PUT`. Edit riwayat tidak lagi memberi dropdown status validasi yang membingungkan: status kehadiran berbeda dari validasi permohonan sakit/izin; permohonan pending hanya boleh mengubah catatan, sedangkan keputusan Setujui/Tolak tetap di alur Validasi Izin. Server menolak manipulasi status dan keputusan berulang. Lima tes terfokus Wali Kelas/IDOR/shell lulus dengan 412 assertion, termasuk render seluruh cabang dokumen cetak Nilai/Rapor PTS/PAS tanpa chrome aplikasi; cache Blade, build produksi, dan `git diff --check` lulus.
- Preservasi template Rapor PTS/PAS 17 September 2026: toolbar pratinjau sudah Tailwind tanpa Bootstrap Icons; konten dua route cetak memakai utility Tailwind dengan CSS minimal untuk `@page`/akurasi warna. Audit Chrome membandingkan baseline pada 390 dan 1440 px: keempat gambar media print route cetak PTS/PAS **identik per piksel**, PDF tetap dua halaman. Watermark pratinjau, bentuk tabel, dimensi dokumen, fungsi zoom, dan penyembunyian toolbar saat print tetap bekerja tanpa error JavaScript. CSS pratinjau yang mengatur watermark/page frame sengaja dipertahankan sampai ada migrasi ekuivalen-piksel yang terukur; jangan merombak desain cetak hanya demi menghapus file. Overflow horizontal PAS mobile 960 px sudah ada pada baseline karena dokumen 900 px diskalakan, bukan regresi batch ini.
- Penutupan Wali Kelas 17 September 2026: SVG chevron mengganti glyph ambigu pada menu Aksi, pilihan menu tetap teks tanpa ikon tambahan. Editor Rapor memindah kontrol urutan ke kanan judul mapel dan membatasi lebar kolom desktop; radio target Salin Format kini sejajar. Audit Chrome 390/1440 px pada Rapor index/edit dan tiga route cetak Nilai: overflow 0 px, menu terbuka, radio sejajar per piksel, toolbar dokumen tersembunyi pada media print, kop ada, tanpa Bootstrap Icons maupun error JavaScript. Lima tes terfokus lulus dengan 423 assertion; Blade cache, build produksi, dan `git diff --check` lulus. Dua belas aset cetak yang masih direferensikan dipertahankan secara sadar.
- Penutupan tambahan cetak Nilai siswa 17 September 2026: `/wali/nilai/{siswa}/print` untuk ganjil/genap ternyata bebas Bootstrap, tetapi masih mengandalkan CSS cetak public lama. Template dikonversi ke utility Tailwind dengan entry tanpa preflight; CSS kecil hanya menyimpan reset metrik, `@page` A4 landscape, zoom print, dan page-break tabel. CSS public lama dihapus setelah referensi nol. Empat screenshot media print pada siswa 92 (390/1440 px × ganjil/genap) identik per piksel sebelum/sesudah; PDF masing-masing dua halaman, tanpa overflow/error JavaScript, zoom in/fit dan penyembunyian toolbar tetap bekerja. Tes Wali Kelas/IDOR/shell: 5 lulus, 433 assertion; build produksi dan cache Blade lulus. Inventaris public cetak kini 6 CSS + 5 JS, ditambah dua Tailwind entry dokumen.
- Capability Waka telah dipersempit berdasarkan kepemilikan data: Tahun Ajaran, Mata Pelajaran, Pengaturan Istirahat, Pengaturan Kenaikan, dan Proses/Rekap Kenaikan dicabut dari sidebar, dashboard, route, controller, view, serta aset Waka. URL langsung menghasilkan 404 dan named route tidak lagi terdaftar. Fitur global tidak boleh sekadar di-hide; hapus seluruh entry point role lalu kunci dengan tes route dan URL langsung.
- Audit Dashboard/Data Kelas Waka 16 September 2026: tujuh test/254 assertion lulus. Audit browser dashboard, index/create Kelas, serta index Jadwal pada 1440x900 dan 390x844 semuanya HTTP 200, overflow 0 px, tanpa marker Bootstrap, aset lama, link global, URL Admin, atau error console.
- Audit final Waka 16 September 2026: 11 test/576 assertion lulus, termasuk shared route Guru/Wali/Manajemen Siswa/Monitoring/Catatan dan IDOR Guru lintas cabang. Audit browser 30 kombinasi route/viewport semuanya HTTP 200, overflow 0 px, tanpa marker Bootstrap/aset lama/URL Admin/error console. Tombol Cetak kartu terlihat putih pada desktop dan mobile; selector posisi `:first-child` yang sebelumnya menimpa warna saat aksi Admin disembunyikan telah dihapus.
- Gerbang akhir batch Waka: Pint, cache Blade, dan build produksi berhasil. Suite penuh menghasilkan 197 tes lulus/2.297 assertion; satu-satunya kegagalan tetap fixture lama `SiswaImportStatusTest` yang tidak memiliki `nama_kelas` dan `agama`, sementara fixture valid pasangannya lulus. Seluruh akun, server, script, dan screenshot audit sementara sudah dibersihkan.
- Audit final Bendahara: 11 feature test/181 assertion lulus. Dashboard dan index Tagihan diuji live pada 1440x900 serta 390x844 dengan overflow 0 px, tanpa marker Bootstrap atau error console; dropdown Buat Tagihan tetap di viewport dan HTML Bendahara tidak memuat endpoint Import/Reset Admin. Audit lanjutan kemudian mengunci semua item tingkat utama—termasuk pemicu dropdown—ke border 0 px; hanya submenu yang boleh 1 px.
- Guard penugasan Guru diperketat end-to-end. Kandidat UI, request Admin/Waka (biasa, multi-jenjang, ganti, dan massal), import, rebuild tabel turunan, statistik, serta pengirim notifikasi kini memakai scope kelayakan tunggal: akun aktif dengan role legacy dan `role_id` yang konsisten sebagai `guru_pengajar`. Perubahan role Guru ditolak selama tanggung jawab akademik masih ada. Database dibersihkan secara transaksional: 7 jadwal invalid dikosongkan dengan audit history, 18 penugasan turunan dan 2 notifikasi salah sasaran dihapus; audit ulang menghasilkan nol temuan.
- Penyesuaian indeks pencarian menu per role dicatat sebagai backlog global berikutnya. Hasil pencarian harus berasal dari capability/navigation role aktif, bukan katalog Admin yang sekadar disaring di sisi tampilan.
- Sidebar Admin, Waka, Sekretaris, Ketua, dan Bendahara memakai hierarki visual yang sama: seluruh item tingkat utama—baik tautan langsung maupun pemicu dropdown—tanpa border/ring dan diberi variasi tone biru yang halus; hanya item submenu yang memakai border. Area akun bawah memakai gradien penuh pada footer sidebar, bukan kartu gradien kecil di atas bidang gelap. Guard source mencegah border pemicu utama muncul kembali.
- Toolbar Jadwal Pelajaran Admin dan Waka memakai grid empat kolom pada mobile sehingga Tambah, Istirahat, Aksi, dan Ekspor berada dalam satu baris; desktop kembali ke flex dengan tinggi 44 px, font 14 px, dan padding horizontal 16–20 px. Bootstrap memberi `!important` pada utility spacing, sehingga breakpoint desktop juga memakai `sm:!px-*`/`lg:!px-*`; tanpa itu tombol tampak gepeng walau class responsif ada di sumber. Item dropdown selalu satu baris dan panel mobile mengikuti lebar container tanpa overflow. Sidebar memakai gradien biru tua yang sedikit lebih muda dengan empat tone menu berkontras nyata. Audit browser Admin/Waka pada 1440x900, 1024x844, dan 390x844 bebas error console/overflow.
- Audit backend Jadwal Waka menemukan bahwa UI terfilter belum cukup: ekspor kelas, API, aksi massal, import, duplikasi, dan mode multi-jenjang sebelumnya belum semuanya memakai scope cabang yang sama. Seluruh entry point kini memakai guard kepemilikan cabang, HTML Waka tidak membocorkan URL Admin, dan duplikasi periode mempertahankan relasi pivot multi-kelas. Tes IDOR memakai record cabang lain pada detail/edit/print/Excel/API/create/aksi massal; semuanya ditolak atau tidak mengubah data.
- Gerbang Jadwal Waka 15 September 2026 lulus: build produksi, cache Blade, dan 14 tes/222 assertion berhasil. Audit browser index/create/import/detail/pratinjau pada 390x844 dan 1440x900 semuanya HTTP 200 dengan overflow 0 px, tanpa marker Bootstrap, aset Jadwal Waka lama, kebocoran endpoint Admin, atau error console. Panel Aksi/Ekspor terbuka tetap di viewport pada kedua ukuran.
- Suite penuh setelah batch Waka menghasilkan 194 tes lulus/1.995 assertion. Jika tes UI mengunci kelas visual eksplisit, assertion harus ikut diperbarui ketika perubahan warna atau kontrak tampilan telah disetujui; jangan mengembalikan implementasi baru hanya untuk memenuhi snapshot kelas lama. Baseline merah yang masih diketahui hanya fixture lama `SiswaImportStatusTest`.

- Gerbang pre-push 12 September 2026 lulus: Blade cache, build produksi, `git diff --check`, serta 30 test/426 assertion yang mencakup penugasan dan notifikasi role, Jadwal Admin/Waka, seluruh flow Bendahara, filter Tagihan, dan komponen global.

## 12. Prompt siap pakai untuk multi-agent

Salin prompt berikut saat ingin melanjutkan dengan beberapa agent:

```text
Kita sedang mengerjakan branch `cleanflow` di project Laravel SIPADUHOK.

Baca seluruh file berikut sebelum mengubah kode:
1. `conversation.md`
2. `docs/cleanflow-admin-progress.md`
3. controller, route, model, view, aset, dan test milik modul yang ditugaskan.

Tujuan:
- Migrasikan seluruh UI setelah login menjadi Tailwind + Alpine.
- Pertahankan semua fungsi bisnis, validasi, route, permission, nama field, method HTTP, dan contract controller.
- Buat UI profesional, minimalis, mobile-first, fleksibel saat browser di-zoom, serta UX yang menjelaskan urutan kerja.
- Hapus Bootstrap/modal lama; gunakan dialog Alpine/HTML native untuk flow berform dan SweetAlert global untuk konfirmasi sederhana.
- Setelah satu modul benar-benar selesai, hapus CSS/JS halaman terpisah yang sudah tidak digunakan.
- Admin, fondasi global Notification/Profile/Account Settings/scroll-up/UI AI Assistant, serta seluruh role Sekretaris, Ketua PKBM, Bendahara, Waka, dan Wali Kelas sudah selesai untuk alur aktif. Config/Pengaturan Pembayaran tetap eksklusif Admin dan tidak boleh diperkenalkan kembali ke route, controller, sidebar, maupun knowledge base Bendahara. Pertahankan seluruh view shared Waka yang sadar namespace, guard cabang, serta absennya aset lokal; Tahun Ajaran, Mata Pelajaran, Pengaturan Istirahat, dan seluruh pengelolaan Kenaikan tetap global/Admin-only.
- Wali Kelas menjadi referensi, bukan target rewrite berikutnya: 30 view aktif, 0 aset CSS/JS halaman kerja, 0 marker Bootstrap khusus role, 4 dokumen Rapor dan 1 dokumen Nilai siswa dengan Tailwind entry tanpa preflight, serta 11 aset public cetak aktif (6 CSS, 5 JS). Rapor index/edit dan ketiga dokumen Nilai telah diaudit di Chrome pada 390/1440 px: tanpa overflow/error JavaScript, menu Aksi terbuka, radio Salin Format sejajar, toolbar Nilai tersembunyi pada media print. Media print PTS/PAS dan cetak Nilai siswa ganjil/genap sudah ekuivalen-piksel terhadap baseline; CSS dokumen lain untuk tabel/watermark/page geometry tetap dipertahankan sebagai pengecualian sadar, bukan sampah. Lanjut audit cetak lintas-role; jangan hapus aset cetak Wali yang masih direferensikan.
- Kontrak Nilai → Rapor yang sudah diterapkan harus dipertahankan: konteks siswa/kelas/tahun/semester tetap konsisten, nilai kosong berbeda dari nol, dan Preview/Sinkron Guru adalah tindakan eksplisit agar nilai wali tidak tertimpa diam-diam. Simpan edit Nilai kembali ke detail siswa pada semester yang sama; langkah "Lanjut ke Rapor" membawa konteks siswa/semester dan meminta jenis rapor. Server menolak ID siswa/mapel/RaporNilai di luar scope. Perubahan berikutnya wajib menjaga pola ini, termasuk import/sinkron/create alternatif.
- Guru dan Siswa memiliki lebih dari satu konteks layout: minimal shell SIA dan LMS, ditambah pengalaman khusus Latihan/Ujian. Jangan menyeragamkan semuanya menjadi salinan tampilan Admin. Shell SIA dan LMS wajib sama-sama full Tailwind, namun pertahankan dua identitas visual/tema yang sengaja berbeda sesuai konteks administrasi versus pembelajaran; catat palet, hierarki, dan komponen masing-masing sebelum migrasi.
- Zona Latihan dan Ujian Guru/Siswa memakai strategi konservatif: pertahankan tata letak, warna, urutan informasi, ukuran area kerja, dan pola interaksi yang ada. Migrasikan hanya sumber styling/behavior dari Bootstrap atau CSS/JS lokal ke utility Tailwind dan Alpine yang setara; setiap perubahan visual di zona ini memerlukan permintaan pengguna terpisah.

Aturan wajib:
- Jangan menambah `<style>`, style inline, CSS halaman, atau JS halaman baru.
- Gunakan Alpine langsung di Blade untuk interaksi ringan.
- JS AI Assistant adalah pengecualian global yang disengaja karena memuat API async, model, upload, riwayat, lightbox, dan drag. Jangan pecah atau salin modul ini per role; styling-nya tetap murni utility Tailwind tanpa CSS komponen.
- Jangan memecah halaman menjadi banyak partial satu-pemakai.
- Pada sidebar, seluruh item tingkat utama—tautan langsung maupun pemicu dropdown—tidak memakai border/ring. Bedakan menu dengan variasi tone latar yang halus dan gunakan state aktif berkontras lebih kuat; border hanya untuk item submenu. Warnai seluruh footer akun agar terpisah jelas dari navigasi, jangan menumpuk kartu berwarna di dalam footer gelap.
- Bila dua role memiliki field dan flow publikasi yang identik, gunakan satu view bersama yang menerima konteks route; jangan mempertahankan salinan Blade per role. Uji URL create/edit/toggle/destruktif pada kedua namespace agar reuse tidak mengarahkan role ke middleware role lain.
- Bila flow identik dipakai tiga role atau lebih, hitung satu `routePrefix` berdasarkan namespace route di awal view dan bangun seluruh endpoint dari sana. Hindari kondisi role tersebar pada setiap kontrol; render-test setiap konteks wajib memastikan endpoint role lain tidak bocor ke HTML.
- Sebelum menyimpulkan dua role dapat memakai flow yang sama, buat matriks capability. Fitur sensitif yang eksklusif Admin harus tidak memiliki route dan link sidebar pada role lain; menyembunyikan elemen secara visual saja tidak cukup. Tambahkan tes `Route::has(...)` dan URL langsung untuk membuktikan batas otoritas.
- Pada view bersama, audit juga HTML hasil render untuk role terbatas. Checkbox, toolbar massal, handler Alpine, form tersembunyi, dan endpoint API khusus Admin tidak boleh ikut terkirim jika capability itu tidak tersedia.
- Filter pilihan pada UI bukan batas keamanan. Setiap ID relasi yang menentukan capability (misalnya `guru_id`) wajib divalidasi ulang di server pada create, update, aksi satuan/massal, import, sinkronisasi/rebuild, dan pengiriman notifikasi melalui satu scope/model rule bersama.
- View yang dipakai bersama tidak menyamakan kewenangan. Untuk role bercabang, gunakan scope kepemilikan tunggal pada index/statistik, detail/edit, endpoint API/AJAX, create/update biasa dan mode alternatif, aksi massal, import, duplikasi, print, serta seluruh ekspor. Tes wajib memanipulasi ID record cabang lain; pemeriksaan visual dan dropdown terfilter saja tidak cukup.
- Satu orang boleh memiliki beberapa akun dengan role berbeda, tetapi relasi pekerjaan dan notifikasi harus menunjuk ke akun yang memiliki capability tersebut. Jangan menganggap kesamaan nama/profil tenaga pendidik sebagai izin lintas-role.
- Pencarian menu global wajib membangun indeks dari konfigurasi navigasi role aktif dan tetap menghormati permission route. Jangan menampilkan hasil yang berakhir 403 hanya karena label tersebut tersedia pada role lain.
- Untuk flow keputusan satuan dan massal, gunakan satu dialog Alpine yang menerima URL aksi, nama record, tipe keputusan, dan array ID melalui state/data attribute. Jangan membuat satu modal per baris.
- Create/edit dengan field sama memakai satu `form.blade.php`.
- Desktop table memakai `table-fixed` + `colgroup`; mobile memakai kartu.
- Lebar kolom aksi harus dihitung dari jumlah tombol 36px beserta gap; jangan memakai persentase yang menciptakan ruang kosong antara status dan aksi.
- Perhitungan kolom aksi wajib menambahkan padding kiri/kanan. Setelah render, ukur jarak badge status ke tombol pertama dan pastikan kelompok tombol tidak meluber ke kolom sebelumnya.
- Semua input dengan ikon di dalamnya wajib memiliki padding kiri aman (`!pl-10` selama CSS compatibility masih dimuat), kemudian audit posisi ikon terhadap placeholder dan teks aktual pada mobile serta desktop.
- Jangan mencampur shorthand spacing dan utility sumbu/sisi yang bertumpang tindih pada elemen yang sama, termasuk lintas breakpoint. Gunakan `px-* py-*`, `pl-* pr-*`, atau `mx-* my-*` secara eksplisit dan jalankan guard spacing sebelum menyatakan selesai.
- Semua dropdown/popover wajib diuji dalam keadaan terbuka pada viewport mobile. Gunakan container aksi `relative` selebar baris dan panel `absolute inset-x-0` tanpa width eksplisit di mobile; lebar tetap serta anchor kanan hanya boleh mulai breakpoint `sm:` dan gunakan important bila bridge Bootstrap masih aktif. Label aksi pendek wajib `whitespace-nowrap`. Verifikasi bounding box panel tidak melewati kedua sisi viewport dan setiap label tetap satu baris.
- Toolbar berisi empat aksi pendek yang semuanya penting boleh memakai `grid-cols-4` pada mobile dengan `text-[10px]`, gap/padding ringkas, dan tinggi sentuh tetap 40 px; kembalikan ukuran normal mulai `sm:`. Jangan mengorbankan label dengan pemenggalan bila seluruhnya masih dapat dimuat satu baris.
- Selama Bootstrap compatibility masih dimuat, utility spacing seperti `.px-1` membawa `!important` dan dapat mengalahkan `sm:px-*`/`lg:px-*`. Gunakan responsive important (`sm:!px-*`, `lg:!px-*`) pada elemen campuran dan ukur `padding`, lebar, tinggi, serta font aktual di DOM; keberadaan class responsif di Blade belum membuktikan hasil akhirnya.
- Stat card mobile tidak boleh dipaksa tiga kolom jika ikon dan label berhimpitan; gunakan kartu ringkasan utama selebar dua kolom dengan dua kartu status di bawahnya.
- Tiga aksi filter mobile harus tetap satu baris memakai grid tiga kolom. Gunakan label adaptif bila lebar kurang, tanpa menghilangkan arti tindakan.
- Editor panjang memakai navigasi sticky dengan tinggi maksimum viewport pada desktop serta select bagian sticky pada mobile. Gunakan `overflow-x-clip` pada ancestor, `scroll-margin` pada target, dan sinkronkan section aktif melalui IntersectionObserver.
- Halaman daftar/empty state tidak boleh dibatasi `max-w-*`; hanya teks penjelasan yang boleh diberi batas baca.
- Validasi input berikon memakai DOM hasil render: catat `padding-left` terhitung dan pastikan bounding box ikon tidak bertabrakan dengan placeholder/teks pada mobile maupun desktop.
- Audit “full Tailwind” harus mencakup layout, slot, partial, dan component bersama yang ikut muncul pada DOM hasil render. Jangan menyatakan bersih hanya berdasarkan pencarian di namespace view modul; bila legacy berasal dari komponen lintas-role, laporkan ownership dan migrasikan sebagai alur tersendiri.
- Sebelum mengubah Guru/Siswa, klasifikasikan setiap route sebagai SIA, LMS, Latihan, atau Ujian. Catat layout induk, aset bersama, dan snapshot visual desktop/mobile. Dilarang memindahkan route antar-shell hanya demi reuse.
- Audit halaman cetak dimulai dari route -> controller -> view akhir, termasuk controller admin yang mewarisi role lain; jangan menilai hanya dari nama file atau namespace route.
- Audit cetak wajib memakai media `print` atau PDF browser sungguhan, bukan hanya DOM/screenshot layar. Untuk halaman `window.print()` yang masih berada di shell aplikasi, sembunyikan sidebar, top bar, footer aplikasi, pencarian, notifikasi/overlay, Bantuan, scroll-up, tombol aksi, dialog, dan input edit; tampilkan judul, identitas, periode, serta data baca-saja meskipun mode edit sedang terbuka. Untuk route dokumen khusus, gunakan `layouts.print`. Uji pratinjau A4 pada desktop dan mobile, cek halaman kosong/overflow/kolom terpotong, nama penanggung jawab dari relasi assignment yang benar, dan tambahkan regresi render/print. Setelah role Wali Kelas tuntas, inventaris dan audit semua tombol/route cetak di role lain dengan pola yang sama.
- Semua pratinjau/cetak HTML admin memakai `layouts.print` dan utility `print:` Tailwind tanpa Bootstrap/aset halaman. Template PDF DomPDF dan Excel adalah dokumen server-side; CSS dokumen atau atribut spreadsheet hanya boleh dipertahankan bila renderer membutuhkannya dan tidak boleh disamakan dengan UI Bootstrap.
- Semua aksi ikon ringkas pada tabel desktop wajib memakai `<x-cleanflow.table-action>` berukuran `h-9 w-9`, `rounded-lg`, dan `ring-1`; jangan mencampur tombol manual untuk visibilitas, unggulan, status, salin, atau CRUD dalam kelompok yang sama.
- Jangan menyisipkan `@js(...)` langsung ke atribut komponen Blade untuk payload Alpine. Gunakan atribut `data-*` yang ter-escape dan baca dengan `$el.dataset...`, lalu uji interaksinya secara live agar tidak ada ekspresi Alpine mentah atau dialog yang gagal terbuka.
- Untuk toolbar pilihan massal atau elemen fixed di dalam shell, gunakan `x-teleport="body"` dan audit posisi nyata dengan `getBoundingClientRect()`; transform parent dapat membuat elemen terlihat jauh di luar viewport.
- Saat live test aksi destruktif, buka lalu batalkan SweetAlert. Jangan menekan konfirmasi terhadap database bersama.
- Wrapper utama memakai `min-w-0 w-full`, bukan `max-w-*`.
- Semua state normal/hover/focus/disabled/loading harus terbaca.
- Jangan memakai selector posisi seperti `[&_a:first-child]` untuk warna aksi yang dapat berubah karena capability role. Tetapkan warna teks pada setiap tombol dan ukur computed color pada semua konteks role serta viewport.
- Pilihan kelas menampilkan `nama · jenjang · cabang` dan value ID.
- Jangan menghapus fitur atau mengubah logika untuk mempermudah migrasi.
- Query agregasi laporan tidak boleh mengasumsikan satu driver database. Fungsi tanggal/raw SQL seperti `DAY()`, `DATE_FORMAT()`, dan `strftime()` wajib dipetakan per driver atau diganti API query yang portabel; jalankan route test pada SQLite selain audit live MySQL.
- Jangan menghapus file sampai `rg` membuktikan tidak ada referensi dan validasi runtime lulus.
- Untuk view bercabang berdasarkan jenis data (misalnya preview materi/tugas/ujian), test harus merender setiap cabang memakai fixture nyata; Blade cache saja tidak menjalankan seluruh PHP hasil kompilasi dan tidak cukup menangkap parse/runtime error cabang tertentu.
- Worktree dipakai bersama dan mungkin kotor; jangan reset/revert perubahan agent atau pengguna lain.
- Bila full suite gagal, bedakan regresi runtime dari assertion visual yang sudah basi dan baseline fixture yang sudah tercatat. Perbarui assertion visual hanya jika DOM aktual sesuai perubahan yang telah disetujui; jangan mengendurkan assertion keamanan, route, ownership, atau capability.

Pembagian agent harus berdasarkan kepemilikan folder yang tidak tumpang tindih. Rekomendasi:
- Agent Koordinator: audit route/controller, integrasi akhir, tracker, build, test, live test, dan file shared; jangan mengambil view yang sedang dikerjakan agent lain.
- Agent Referensi Waka: seluruh role Waka sudah selesai; jangan membuat kembali view/aset role untuk Kelas, Jadwal, Guru Pengajar, Wali Kelas, Manajemen Siswa, Monitoring, atau Catatan. Pertahankan view shared dan scope cabang sebagai referensi migrasi role berikutnya.
- Agent Referensi Wali Kelas: role selesai untuk alur aktif; jangan memigrasi ulang dashboard/pemilihan kelas, jadwal, Presensi, Nilai/Rapor, Promosi, Validasi/Template, atau Arsip. Pertahankan menu Aksi kanan atas dengan chevron SVG dan opsi teks ringkas; baris editor mapel menempatkan tombol urutan di sisi judul; dialog Salin Format memakai radio flex-center. Media print PTS/PAS dan Nilai siswa ganjil/genap terbukti identik per piksel sebelum/sesudah Tailwind pada 390/1440 px; jangan ubah tabel, tata letak, ukuran halaman, watermark, margin berulang, atau page break. Cetak Nilai per siswa memakai `resources/css/wali-nilai-print.css` tanpa preflight; CSS public lama telah dihapus. Enam CSS dan lima JS public dokumen lain yang masih direferensikan tidak boleh dihapus hanya karena berada di folder lama; konversi lebih lanjut butuh baseline layar dan PDF A4. Regresi Presensi wajib membedakan status kehadiran dari keputusan validasi izin dan menguji `_method=PUT` pada HTML render. Agent lintas-role kini boleh mengaudit route cetak role lain.
- Agent Guru/Siswa SIA: hanya memiliki route, controller, view, dan aset shell SIA; pertahankan identitasnya dan jangan menyentuh LMS/Latihan/Ujian pada batch yang sama.
- Agent Guru/Siswa LMS: hanya memiliki shell dan alur LMS; buat full Tailwind tetapi jangan memaksakan visual SIA ke lingkungan belajar.
- Agent Latihan/Ujian: bekerja sebagai migrasi ekuivalen-pixel. Ambil snapshot sebelum/sesudah dan pertahankan layout, warna, hirarki, serta alur; hanya ganti Bootstrap/CSS/JS lokal dengan Tailwind/Alpine setara.
- Agent Wali Siswa: mulai setelah kontrak Wali Kelas stabil dan bagi berdasarkan alur lengkap, bukan per halaman index.
- Agent Audit Clean Code: cari Bootstrap/Sneat, aset yatim, view duplikat, dan referensi mati hanya pada role yang sudah selesai; jangan menghapus aset yang masih direferensikan role lain.

Setiap agent sebelum edit harus mengirim ke koordinator:
1. daftar file yang akan dimiliki,
2. route/controller contract yang ditemukan,
3. risiko logika dan rencana validasi.

Setiap agent setelah edit harus mengirim:
1. daftar view yang selesai,
2. daftar aset yang aman dihapus (jangan hapus bila masih dipakai role lain),
3. hasil Blade cache/build/test terkait,
4. route live dan viewport yang diuji,
5. temuan bug atau contract yang belum pasti.

Hanya koordinator yang memperbarui `docs/cleanflow-admin-progress.md`, menghapus folder bersama, menjalankan full build/full test, dan menyatakan modul selesai. Jika dua modul memakai aset/controller yang sama, berhenti dan koordinasikan ownership sebelum edit.

Mulai dengan audit read-only. Jangan langsung melakukan rewrite massal. Kerjakan satu alur lengkap (index/create/edit/show/import/print/dialog) lalu validasi sebelum berpindah ke alur berikutnya.
```

## 13. Urutan kerja yang direkomendasikan

Migrasi admin dan fondasi global sudah selesai. Urutan kerja berikutnya:

1. Sekretaris — selesai; publikasi Admin/Sekretaris kini memakai view bersama.
2. Ketua PKBM — selesai; dashboard, keputusan, monitoring, laporan/cetak, dan catatan bersih dari aset UI lokal.
3. Bendahara — selesai; seluruh dashboard, keuangan, validasi, Tagihan, Pembayaran, dan Laporan/cetak bersih dari aset UI role lokal. Config Pembayaran tetap eksklusif Admin.
4. Waka — selesai; seluruh flow aktif Tailwind/shared, 0 aset UI role lokal.
5. Wali Kelas — selesai untuk alur aktif; 30 view, 0 aset CSS/JS halaman kerja, tanpa Bootstrap Icons CDN. Rapor index/edit dan tiga dokumen Nilai lolos audit browser 390/1440 px; route cetak PTS/PAS serta Nilai siswa ganjil/genap ekuivalen-piksel. Pertahankan enam CSS dan lima JS public dokumen yang aktif untuk kontrak visual cetak; tidak ada alasan menghapusnya tanpa pengganti yang terbukti identik.
6. Guru, Siswa, dan Wali Siswa setelah pola LMS serta kartu mobile dikunci.
7. Setelah setiap role selesai, audit lintas-role dan baru kurangi Bootstrap/Sneat dari bridge global ketika tidak ada consumer tersisa.

## 14. Pembaruan 21 September 2026 - Wali Siswa dan audit dokumen cetak

Progress role Wali Siswa/orang tua:

- Presensi mobile memakai toolbar 2x2 pada viewport kecil dan kembali menjadi baris pada desktop.
- Tagihan memakai kartu detail di mobile, tabel tetap terstruktur di desktop, checkbox diperbesar, pilihan channel Paywuz memiliki indikator aktif, dan tombol `Bayar Sekarang` mengikuti item tagihan yang benar-benar dipilih.
- Pembayaran digital menampilkan judul/nomor transaksi dengan kontras putih yang eksplisit serta menampilkan error validasi ketika channel pembayaran gagal diganti.
- Invoice Wali Siswa memiliki zoom `-`, `+`, dan `Fit`, metadata invoice lebih stabil, serta mode print mereset transformasi agar PDF tidak ikut tercetak dalam keadaan zoom.
- Controller Orang Tua membatasi rapor hanya pada status diterbitkan dan capability anak akademik, membatasi perubahan presensi pada pengaju asli, serta mencegah penggantian request milik pengguna lain.
- Pembayaran digital dan transaksi terkait sudah diberi scope `siswa_id`; penggantian channel juga menolak transaksi tanpa `order_id`.
- Aset CSS/JS khusus Wali Siswa yang sudah tidak direferensikan telah dihapus setelah audit referensi. Invoice tetap menjadi dokumen standalone dengan CSS/JS inline karena perlu mempertahankan layout print dan kontrol zoom.

Audit dokumen cetak:

- `layouts.print` sekarang mencetak dengan ruang isi `12mm` dan tidak lagi mengandalkan margin halaman browser.
- Basis cetak bersama `public/css/shared/print-base.css` memakai `@page { margin: 0 }`. Ini menghilangkan ruang header/footer browser berupa tanggal, URL, dan nomor halaman pada Chrome/Edge print preview; ruang isi tetap disediakan oleh wrapper dokumen.
- Template cetak standalone yang masih memakai margin halaman nonzero telah disamakan ke margin nol: Nilai Wali Kelas, jadwal LMS Siswa, Kalender Sekretaris, Nilai/Rapor Wali Kelas, dan Invoice Wali Siswa.
- Route kwitansi Admin dan Bendahara sama-sama memakai `admin.keuangan.pembayaran.cetak-kwitansi`, sehingga perubahan `layouts.print` otomatis berlaku pada keduanya.
- Audit berikutnya wajib membuka print preview A4 sungguhan pada setiap route cetak, memastikan header/footer browser tidak muncul, tidak ada halaman kosong, overflow, kolom terpotong, atau toolbar ikut tercetak.

Validasi terakhir sebelum batch ini:

- `php artisan view:cache`, `npm run build`, `git diff --check`, dan test terfokus role Orang Tua lulus pada batch sebelumnya.
- Full suite memiliki dua kegagalan baseline yang tidak terkait role Orang Tua: fixture lama import siswa yang kurang `nama_kelas`/`agama`, serta assertion lama URL asset dev pada UI Wali Kelas.
- Perubahan audit cetak harus divalidasi ulang dengan Blade cache, build produksi, dan suite terfokus sebelum push branch `cleanflow`.

## 15. Pembaruan 2 Oktober 2026 - Siswa shell SIA

- Lima view SIA Siswa selesai dengan Tailwind + Alpine/dialog native tanpa aset halaman: `siswa/partials/sidebar-sia`, `siswa/sia/dashboard`, `siswa/sia/presensi/index`, `siswa/sia/penilaian/index`, dan `siswa/alumni/dashboard`. Pola sidebar mengikuti Wali Siswa (bagian berlabel, item tanpa border, nada biru berselang-seling). Shell LMS, Latihan, dan Ujian Siswa belum disentuh dan harus dikerjakan sebagai batch terpisah.
- Kontrak menu siswa: Dashboard, HOK-LMS (hanya bila jenjang kelas ada di `lms_allowed_jenjang`), Presensi, Data Penilaian. **Pembayaran dan Rapor bukan hak siswa** — milik Wali Siswa. Route `siswa.sia.pembayaran.*`, `SiaPembayaranController`, `SiaRaporController`, dan view pembayaran/rapor siswa tidak lagi punya entry point (URL langsung 404, dikunci `SiswaSiaCleanFlowUiTest`). Siswa tidak mengajukan izin; itu lewat Wali Siswa.
- Aturan LMS aktif satu sumber: dashboard, sidebar, dan middleware `lms.access` sama-sama mengikuti `lms_allowed_jenjang`; jangan menulis ulang `in_array(jenjang, ['SMP','SMA'])` di view.
- Perbaikan sampingan: kartu Nilai terbaru memakai `nilai_akhir` dan `semester`; nilai 0 tampil sebagai `0.0`, bukan `-` (nilai kosong tetap `-`). Grafik progres tugas memakai donut SVG tanpa Chart.js CDN dan label jujur (Selesai/Belum, bukan Lulus/Proses/Tunda).
- Validasi: `SiswaSiaCleanFlowUiTest` 6 tes/103 assertion lulus; Blade cache dan build produksi berhasil; suite penuh 205 lulus/2 gagal dengan dua kegagalan baseline yang sudah tercatat (`SiswaImportStatusTest` dan assertion aset cetak di `WaliKelasCoreUiTest`). Belum ada audit browser 390/1440 px — dilakukan manual oleh pengguna sebelum push.
- Aset yatim yang menunggu penghapusan manual (tidak lagi direferensikan Blade): `resources/css/siswa/sia/**`, `resources/js/siswa/sia/**`, `resources/css/siswa/alumni/**`, `resources/js/siswa/alumni/**`, `resources/views/siswa/sia/pembayaran/**`, `resources/views/siswa/sia/rapor/**`, `app/Http/Controllers/Siswa/SiaPembayaranController.php`, `app/Http/Controllers/Siswa/SiaRaporController.php`.

## 16. Aturan sidebar baru - 2 Oktober 2026

- Scrollbar sidebar disembunyikan di semua role: `nav` pada `layouts.app` dan `cleanflow-lms-shell` memakai `[scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden` bersama `overflow-y-auto`, sehingga menu tetap bisa digulir dengan roda mouse/sentuh tetapi tanpa batang scroll. Jangan menambah scrollbar kembali atau CSS scrollbar khusus per role.
- Dropdown sidebar wajib memakai pola `<button data-menu-toggle aria-controls aria-expanded>` + ikon `fa-chevron-down data-menu-chevron`, bukan `<a class="menu-toggle">` dengan chevron `::after` (terpotong pada Guru). Sidebar Guru (SIA) sudah diubah; sidebar Guru LMS (`guru/partials/sidebar-lms`) tidak memakai dropdown.
- Dashboard Guru (`dashboard/guru.blade.php`) **belum** Tailwind: masih Bootstrap grid + `resources/css/dashboard/guru.css`. Termasuk dalam batch Guru berikutnya bersama `resources/css/guru/**` dan `resources/js/guru/**`.

## 17. Guru shell SIA - 2 Oktober 2026

- Sepuluh view SIA Guru selesai dengan Tailwind + Alpine/dialog native tanpa `@vite`/`<style>`/style inline: `dashboard/guru`, `guru/jadwal/index`, `guru/kelas/index`, `guru/kelas/mapel`, `guru/partials/sidebar`, `guru/lms/arsip/{index,form-salin,preview-wrapper}`, dan `guru/lms/catatan-monitoring/{index,show}`. Arsip dan Catatan Monitoring memakai `layouts.app` + sidebar SIA, jadi termasuk SIA walau namanya `guru.lms.*`. Shell LMS Guru (`layouts.lms-guru`, 25 view) belum disentuh.
- Preview Arsip memakai isi `monitoring-lms.preview.{materi,tugas,ujian}` yang sama dengan Admin/Waka/Ketua; `GuruLmsArsipController@preview` mengirim `previewWrapper = guru.lms.arsip.preview-wrapper`, dan ketiga view isi memakai `@extends($previewWrapper ?? 'monitoring-lms.preview.wrapper')`. Jangan membuat kembali salinan preview per role. Seleksi massal Arsip memakai satu `x-data` (bar aksi `sticky`, bukan teleport, agar input tetap ada di dalam form).
- Bug yang ditemukan lewat tes SQLite: `GuruJadwalController` memakai `FIELD()` (khusus MySQL) sehingga halaman Jadwal Guru 500 di driver lain; kini `CASE`. **Masih memakai `FIELD()` dan perlu diperbaiki pada batch rolenya:** `Ketua/ValidasiRaporController:316`, `WaliKelas/RaporController:1283`, `Siswa/SiswaDashboardController:118` (dashboard LMS siswa).
- Aksi cepat dashboard Guru: tombol "Refresh Dashboard" diganti "Arsip LMS".
- Validasi: `GuruSiaCleanFlowUiTest` 5 tes/246 assertion; cache Blade dan build produksi berhasil; suite penuh 211 lulus dengan dua kegagalan baseline yang sama seperti bagian 15. Belum diaudit di browser 390/1440 px.
- Aset yatim menunggu hapus manual (tidak lagi direferensikan; penghapusan otomatis sebelumnya ditolak pengaman): `resources/css/dashboard/guru.css`, `resources/css/guru/jadwal/index.css`, `resources/css/guru/kelas/{index,mapel}.css`, `resources/css/guru/lms/arsip/{index,form-salin,preview}.css`, `resources/css/guru/lms/catatan-monitoring/{index,show}.css`, `resources/js/guru/lms/arsip/{index,form-salin}.js`, serta view `resources/views/guru/lms/arsip/preview-{materi,tugas,ujian}.blade.php`.

## 18. Tema LMS dan Guru LMS - 2 Oktober 2026

- Tema LMS resmi berbeda dari SIA: shell `layouts/partials/cleanflow-lms-shell` memakai sidebar terang (`bg-white`, `border-r`) beraksen indigo/violet, item aktif pill `bg-indigo-600`, kartu konteks "Anda mengajar" bergradien indigo→violet, dan aksen konten indigo. SIA tetap sidebar biru gelap. Item sidebar LMS memakai satu komponen `x-cleanflow.lms-nav-link` (Guru, notifikasi Guru, Siswa); CSS `.cleanflow-lms-nav` lama di `admin.css` sudah dihapus. Jangan menyalin warna SIA ke LMS.
- Seluruh 25 view LMS Guru (beranda kelas, Materi, Tugas + koreksi + saran AI, Latihan/Ujian + soal + hasil + koreksi AI per soal + pengawasan realtime, Forum, Kelas Virtual, Nilai) memakai Tailwind + Alpine/dialog native. Create/edit Materi, Tugas, Meeting, Ujian/Latihan digabung menjadi `form.blade.php` (controller diarahkan ke view `form`). `layouts/lms-guru` tidak lagi memuat `resources/css/layouts/lms-guru.css` (file dihapus); folder `resources/css/guru` kosong dan dihapus.
- **Pengecualian JS yang disengaja:** `resources/js/guru/lms/ujian/manage-soal.js` (editor Kelola Soal) tetap modul karena AI Question Generator menyuntikkan soal lewat `window.addQuestion` dan membaca DOM `#soalAccordion` (`.soal-item`, `.question-input`, `.narasi-input`). Bootstrap collapse/modal/toast di modul ini diganti toggle sendiri, `<dialog>`, dan SweetAlert. Generator AI dipindah dari `public/js/ai-question-generator.js` (dihapus) ke `resources/js/components/ai-question-generator.js` dan diimpor modul editor; URL generate kini dikirim Blade lewat `data-generate-url` sesuai rute latihan/ujian. Jangan mengubah class hook tersebut tanpa memperbarui generator.
- Dialog hapus yang sudah menjadi konfirmasi sendiri memakai `data-confirmed="true"` agar handler DELETE global (SweetAlert) tidak meminta konfirmasi dua kali.
- Bug lama yang diperbaiki: form soal manual memanggil relasi `$ujian->soal()` (tidak ada; halaman Tambah Soal selalu 500) dan membaca `$soal->bobot` padahal kolomnya `bobot_nilai` (edit soal diam-diam mengembalikan bobot ke 10); editor Kelola Soal kini melanjutkan indeks baris Benar/Salah setelah data lama sehingga baris baru tidak menimpa pernyataan pertama; form tugas tidak lagi mengisi batas edit "2" otomatis saat halaman edit dibuka (kosong berarti tanpa batas).
- Nilai Siswa tetap tabel matriks dengan scroll internal dan kolom nama sticky agar setiap nilai hanya punya satu input bernama; validasi angka (koma→titik, 0–100, tombol naik/turun) dipindah ke Alpine.
- Validasi: `GuruLmsCleanFlowUiTest` 5 tes/712 assertion; 34 tes fokus Guru/global lulus (1.207 assertion); Blade cache dan build produksi berhasil. Asersi visual lama yang mengunci shell LMS ke gradien SIA diperbarui ke kontrak tema LMS yang disetujui. Belum diaudit di browser 390/1440 px; LMS Siswa, Latihan, dan Ujian Siswa belum disentuh (Siswa LMS masih memuat `resources/css/layouts/lms.css`).
- Aset yatim batch sebelumnya (bagian 15 dan 17) sudah dihapus atas izin pengguna.

## 19. Aturan tabel mobile dan brand LMS - 2 Oktober 2026

- **Aturan baru (wajib ke depan):** setiap halaman berformat tabel harus punya tampilan khusus mobile, bukan tabel yang hanya digeser ke samping. Pola acuan: kartu per baris data dengan informasi utama di kepala kartu, ringkasan saat tertutup, dan isian dikelompokkan per makna saat dibuka. Referensi Wali Kelas `/wali/nilai/show` dan `/wali/nilai/edit`; implementasi Guru `/guru/lms/{kelas}/{mapel}/nilai`.
- Untuk tabel berisi **input bernama**, jangan menduplikasi markup mobile dan desktop (nama field akan terkirim ganda). Gunakan satu markup responsif: baris `lg:grid` dengan satu template kolom yang sama untuk header dan setiap baris, pembungkus grup `lg:contents`, label per input `lg:sr-only`, isi kartu disembunyikan di mobile dengan `max-lg:hidden` saat tertutup. Hindari `<fieldset>` dengan `display: contents` (dukungan browser tidak konsisten); pakai `div role="group"`.
- Nilai desimal di input ditampilkan ringkas (`75.00` → `75`, `9.80` → `9.8`) tanpa mengubah nilai tersimpan. Tombol ↑/↓ hanya muncul di mobile; di desktop penyesuaian memakai tombol panah keyboard agar kolom tidak terpotong.
- Brand shell LMS: logo tanpa bingkai kotak, nama dan subjudul rata tengah, subjudul boleh turun baris (tidak dipotong `truncate`).
- **Nilai guru vs wali (kontrak dipertahankan):** bila `wali_terakhir_edit_at` terisi, simpanan guru hanya masuk snapshot `*_guru`; nilai aktif/rapor tetap milik wali sampai wali memilih Sinkron dari Guru. Halaman nilai Guru kini menampilkan **snapshot guru** di input untuk baris tersebut (sebelumnya menampilkan nilai wali sehingga hasil simpan guru tampak tidak berubah, dan simpan massal menimpa snapshot guru dengan nilai wali). Baris diberi label "Diedit wali", input yang berbeda dari nilai aktif diberi warna kuning dengan tooltip nilai aktif wali, dan simpan mempertahankan semester. Dikunci `test_nilai_edited_by_wali_shows_guru_snapshot_and_save_keeps_wali_value`.
- **Keputusan pengguna 2 Oktober 2026 (alur nilai guru vs wali):** nilai guru dan wali terpisah. Sebelum wali merevisi, simpanan guru otomatis menjadi nilai aktif (siswa, wali, rapor). Setelah wali merevisi, guru tetap bebas menimpa nilainya sendiri (versi guru/snapshot, ditampilkan di halaman guru), tetapi nilai aktif untuk Data Penilaian siswa dan rapor tetap versi wali sebagai pemegang keputusan akhir sampai wali menyinkronkan atau mengedit. Setiap kali guru mengubah nilai yang sudah direvisi wali, semua akun wali kelas (role `wali_kelas`) pada kelas itu menerima satu notifikasi `TIPE_NILAI` per penyimpanan (`NotificationService::notifyNilaiDiperbaruiGuru`, tautan ke detail siswa atau daftar nilai). Halaman wali `nilai/show` kini menampilkan banner dan badge "Update guru" per mapel dengan rumus yang sama seperti `nilai/edit`, plus tombol "Bandingkan & sinkronkan". Dikunci oleh dua tes di `GuruLmsCleanFlowUiTest`.

## 20. Forum, Latihan, dan audit AI Guru - 2 Oktober 2026

- Forum Guru: `forum/create` kini bisa mengunggah `lampiran[]` (pratinjau Alpine, maks 10MB). `components/lms/media-display` hanya mempratinjau gambar/video; PDF dan Office tampil sebagai **kartu file**. Tombol "Buka" PDF memakai `preview_url()` (`/view-document/{token}`, tanpa ekstensi `.pdf`, inline) karena IDM membajak URL `.pdf` dan blob/fetch merusak PDF. Jangan kembali ke iframe/blob untuk PDF.
- Latihan: form Latihan menyembunyikan field **Tipe** (hidden `tipe_ujian=latihan`), tanggal jadi grid 2 kolom. Field Tipe hanya untuk menu Ujian.
- **Perubahan model AI (diverifikasi langsung ke API 2 Oktober 2026):** Groq memindahkan Llama 3.3 70B / 3.1 8B ke Enterprise (error 404 "model does not exist") dan mematikan `qwen/qwen3.6-27b` + `groq/compound`. Model aktif: `openai/gpt-oss-120b` (default teks), `openai/gpt-oss-20b`, `qwen/qwen3.8-27b` (satu-satunya vision, maks 3 gambar). Gemini: `gemini-2.5-pro` dan `gemini-2.5-flash-lite` ditolak untuk key baru, Pro tidak masuk kuota gratis; daftar kini `gemini-2.5-flash` (default) + `gemini-3.5-flash-lite`. Semua nama model hanya di `config/ai-models.php`; setting lama di DB dialihkan otomatis oleh `ai_model_aktif()`.
- **Bug akar error 404:** `config('ai-models.retired.'.$model)` memakai notasi titik sehingga nama model bertitik (`llama-3.3-…`, `qwen3.8-…`, `gemini-2.5-…`) tidak pernah dialihkan dan parameter khusus qwen tidak terkirim. Lookup kini via array (`config('ai-models.retired', [])[$model]`). Jangan pernah memasukkan nama model ke string kunci `config()`.
- Semua panggilan Groq lewat `ai_groq_payload()` (gpt-oss: `reasoning_effort=low`, `include_reasoning=false`, +1024 token penalaran; qwen3.8: `reasoning_effort=none`, `reasoning_format=hidden`). `ai_model_tidak_ditemukan()` memicu fallback ke model default lalu Gemini. Generator soal dan koreksi AI mengulang sekali tanpa `response_format` saat Groq membalas `json_validate_failed`.
- Chatbot: kuota paket gratis Groq hanya 8.000 token/menit (gpt-oss) dan 7.000 token input/menit (qwen), sedangkan prompt penuh ±8.400 token, sehingga semua chat dulu jatuh ke Gemini. Untuk Groq kini dikirim knowledge base ringkas (`KnowledgeBaseLoader::getRelevantForRole` — peta menu + detail menu yang cocok dengan pertanyaan, ≤6.000 karakter; `getOwnershipPromptRingkas`), ±4.700 token. Gemini tetap prompt penuh dengan `maxOutputTokens` 4096 (token "berpikir" Gemini 2.5 ikut dihitung; 1.500 membuat JSON terpotong dan tampil mentah). Role guru di chatbot adalah `guru_pengajar`.
- Uji nyata dengan API key tersimpan: kelima tipe soal (PG, PG kompleks, benar/salah, isian singkat, uraian+narasi) sukses via gpt-oss-120b; koreksi teks dan koreksi gambar (qwen3.8) sukses; chatbot sukses di ketiga model Groq dan Gemini, dengan fallback antarmodel saat rate limit per menit.
- Validasi: `AiGeneratorSoalTidakRusakTest` 13 tes (regresi nama bertitik, payload Groq, KB ringkas); suite penuh 222 lulus + dua kegagalan baseline yang sama (`SiswaImportStatusTest`, aset print `WaliKelasCoreUiTest`). Menu Guru (SIA + LMS) dinyatakan selesai; berikutnya LMS Siswa.
- **Keputusan pengguna (2 Oktober 2026): nama model AI tidak ditampilkan ke guru.** Badge "Model: …" di panel AI Question Generator dihapus, respons generate tidak lagi mengirim `metadata` model, dan pesan gagal generator/koreksi diganti kalimat netral (detail teknis hanya di log). Pergantian model sepenuhnya otomatis lewat `ai_rantai_model($provider, $mulai)`: model pilihan → model teks lain → model vision Groq → semua model Gemini, berlanjut pada kegagalan apa pun (kuota, model dicabut, server sibuk). Dipakai generator soal, koreksi teks, dan koreksi gambar (Groq vision → semua Gemini). Menu pemilih model di chatbot tetap ada sebagai fitur.
- Editor Kelola Soal: textarea Narasi/Pertanyaan memakai `data-autogrow` (`resources/js/components/textarea-autogrow.js`, dimuat global dari `admin.js`): tinggi mengikuti isi sampai ±10 baris, lalu tombol "Tampilkan semua"/"Ringkas". Bar tombol Regenerate/Tambahkan di panel AI menutup padding bawah panel (tidak ada celah).
- Chatbot: tidak ada pemilih model di UI (sisa kode `#modelSelector` dan preferensi `localStorage.selectedChatModel` dihapus; preferensi lama dibersihkan saat memuat). Notifikasi yang menyebut nama model saat beralih ke model gambar/PDF dihapus (pergantian diam-diam), respons `/ai-chatbot/send` tidak lagi memuat `model`/`provider`, error ke pengguna netral, dan fallback Gemini mencoba semua model Gemini. Jawaban AI kini merender markdown inline (`**tebal**`, `*miring*`, `` `kode` ``) lewat `formatTeksInline()` setelah escape HTML.

## Catatan 3 Oktober 2026 — LMS Siswa, media, koreksi AI, konten terhapus

- **Media forum LMS (3 Oktober 2026, Guru & Siswa):** lampiran gambar tampil sebagai thumbnail persegi berjajar dan dibuka di galeri lightbox pada halaman yang sama (`x-lms.lightbox` sekali per halaman + `$store.lightbox` di `resources/js/components/media-lightbox.js`): tombol/keyboard/geser, penghitung, strip thumbnail, zoom, unduh; overlay `z-[1100]` di atas tombol scroll. Gambar dikelompokkan per postingan lewat `group` pada `x-lms.media-display`. Video (mp4/m4v/mov/webm/ogg) diputar langsung. Isi postingan dirender `x-lms.rich-text`: teks di-escape, tautan http(s) bisa diklik, dan tautan YouTube (watch/youtu.be/shorts/embed/live) menjadi kartu pratinjau yang memuat iframe youtube-nocookie hanya saat diputar.
- **Tautan WhatsApp:** semua direct chat memakai `wa_link()`/`wa_nomor()` (helpers) yang menormalkan 08…/+62…/8… menjadi `628…`; dipakai di daftar guru Siswa, login, detail wali siswa, dan tiket recovery.
- **Lampiran tugas/materi (3 Oktober 2026):** `x-file-preview` kini menampilkan gambar langsung di halaman (klik → lightbox, overlay disisipkan sekali lewat `@once` ke stack `modals`) dan memutar video langsung; PDF/dokumen tetap tombol pratinjau dialog. Berlaku di tugas & materi Siswa, koreksi, form materi, dan form tugas Guru.
- **Koreksi AI melihat lampiran soal:** `AiGradingService::evaluateMultimodal()` mengirim gambar soal + jawaban teks/gambar dalam satu permintaan berlabel (Groq vision → semua model Gemini); tugas tanpa kunci tidak lagi memakai deskripsi sebagai kunci. Dipakai koreksi tugas (`GuruKoreksiController`, lampiran PDF digital diambil teksnya, PDF scan jawaban dikonversi gambar) dan koreksi ujian untuk soal bergambar. Kasus nyata "apa nama logo di atas" → jawaban "Batuceper" kini dinilai benar.
- **Konten terhapus (semua role):** `App\Support\NotFoundRedirector` (didaftarkan di `bootstrap/app.php`) mengalihkan 404 data pada rute valid ke halaman induk terdekat (lalu dashboard role/`dashboard`) dengan flash `warning` "Data yang Anda buka tidak ditemukan atau sudah dihapus", menghapus notifikasi pengguna yang menunjuk URL itu, dan punya pengaman putaran. Tetap 404: URL tak dikenal, AJAX/JSON, non-GET, rute berkas (`document.preview`, unduhan, `.api.`, `preview-bukti`), request tanpa sesi. Saat Forum/Tugas/Materi/Ujian/Pengumuman dihapus, notifikasi semua penerima yang menunjuk kontennya ikut dihapus (`AppServiceProvider`).
- **Lain-lain:** dialog AI Question Generator & editor soal mengimpor SweetAlert langsung (tidak pernah jatuh ke alert/confirm bawaan); grup tanggal detail mapel Siswa terbuka bila berisi item yang dibuat hari ini; tombol "Tampilkan semua" textarea memakai kelas `hidden` (atribut `hidden` kalah oleh `inline-flex`) dan menghitung ulang setelah font termuat.
- **Audit keamanan 3 Oktober 2026:** rute tanpa auth hanya publik/landing/login/recovery/webhook (login & recovery ber-throttle, webhook diverifikasi HMAC `hash_equals`); `admin/security-setup` kini wajib `role:admin`; seluruh `{!! !!}` memakai string statis atau sudah di-escape; tidak ada raw SQL berisi input, `orderBy` memakai whitelist, tidak ada open redirect, tidak ada model `$guarded = []`; semua validasi upload memakai `mimes` (tanpa SVG/HTML); kepemilikan diperiksa server pada balasan forum, pengumpulan tugas, dan seluruh aksi Wali Siswa. Catatan produksi: `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true` di balik HTTPS.
- **Tata letak tugas:** lampiran gambar/video tugas tampil di bawah deskripsi (lebar maks `max-w-sm`) pada Siswa dan koreksi Guru; dokumen tetap tombol pratinjau sebaris.
