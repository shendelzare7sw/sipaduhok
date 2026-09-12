# CleanFlow Admin UI Migration

Pembaruan terakhir: 12 September 2026.

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

Role **Waka berjalan**. Sidebar telah memakai pola CleanFlow tanpa aset lokal. Toolbar index Jadwal Pelajaran telah diperbaiki dengan Alpine dan layout responsif bersama pola Admin, tetapi keseluruhan index serta create/edit/show/import masih memakai CSS/JS Waka dan komponen Bootstrap; 55 aset role tetap dianggap aktif sampai setiap alur selesai diuji. Jangan menghapus aset Jadwal hanya karena toolbar sudah berubah.

Inventaris awal sebelum migrasi role non-admin (marker legacy adalah temuan kasar untuk menentukan urutan, bukan jumlah halaman gagal):

| Role | View awal | Referensi Vite awal | Aset awal | Status saat ini |
|---|---:|---:|---:|---|
| Sekretaris | 12 | 17 | 17 + 2 dashboard | **Selesai — 0 aset UI lokal** |
| Ketua PKBM | 20 | 15 | 19 + 1 dashboard | **Selesai — 0 aset UI lokal** |
| Bendahara | 26 | 28 | 31 (audit final) | **Selesai — 0 aset UI lokal** |
| Wali Siswa | 12 | 15 | 16 | Belum |
| Wali Kelas | 33 | 21 | 41 | Belum |
| Guru | 44 | 40 | 40 | Belum |
| Siswa | 42 | 48 | 43 | Belum |
| Waka | 51 | 57 | 55 | **Berjalan — sidebar selesai, Jadwal parsial** |

Urutan berikutnya adalah menyelesaikan seluruh alur Waka, kemudian Wali Kelas, lalu Guru/Siswa/Wali Siswa. Angka aset tidak boleh langsung dianggap aman dihapus karena beberapa file dapat dipakai silang oleh layout atau view shared.

Validasi Sekretaris: build produksi dan Blade cache berhasil; 4 feature test dengan 97 assertion lulus, termasuk kontrak route view bersama pada konteks Admin. Audit browser menelusuri 22 kombinasi route/viewport pada 1440x900 dan 390x844: seluruhnya HTTP 200, overflow 0 px, tanpa overlay Vite atau error JavaScript. Dashboard dan Kalender juga ditinjau dari screenshot render penuh. Feed kalender serta PDF bulanan/tahunan tetap bekerja. Full suite terbaru: 183 test lulus (1.734 assertion); satu fixture lama `SiswaImportStatusTest` tetap gagal karena barisnya tidak memiliki `nama_kelas` dan `agama`. Test pendamping `SiswaImportStatusValidFixtureTest` lulus, sehingga kegagalan terisolasi pada fixture lama dan tidak terkait migrasi UI.

Validasi Ketua: build produksi dan Blade cache berhasil; 13 test terfokus lulus dengan 169 assertion. Audit browser menguji 16 kombinasi route/viewport pada 1440x900 dan 390x844: seluruhnya HTTP 200, overflow dokumen 0 px, tanpa marker modal Bootstrap, overlay Vite, atau error console. Dialog validasi diuji terbuka pada mobile. Halaman persetujuan/dispensasi tanpa baris dummy tetap dikunci melalui render test, kontrak route, dan audit markup.

Validasi role Bendahara: Blade cache dan build produksi berhasil. Sebelas feature test lulus dengan 181 assertion; cakupannya termasuk render seluruh sepuluh route Tagihan melalui view Tailwind bersama, dashboard Alpine, namespace endpoint Bendahara, absennya kontrol Import/Reset khusus Admin, serta penghapusan aset. Enam route Laporan dan seluruh alur Pembayaran juga terbukti memakai view shared. Audit menemukan penggunaan `DAY()` khusus MySQL pada grafik laporan; ekspresi hari kini dipilih berdasarkan driver untuk MySQL, PostgreSQL, dan SQLite. Audit browser akhir pada dashboard dan index Tagihan di 1440x900 serta 390x844 menghasilkan HTTP 200, overflow 0 px, dropdown mobile tetap di viewport, tanpa marker Bootstrap, kebocoran endpoint Admin, atau error console. Audit sidebar lanjutan mengunci tautan utama dan pemicu dropdown ke border 0 px; hanya submenu yang boleh 1 px. Config Pembayaran Admin tetap HTTP 200 di kedua viewport dan `/bendahara/config` tetap 404.

Validasi integritas penugasan Guru: ditemukan bahwa akun Sekretaris dan Wali Kelas dapat masuk melalui import atau request ID manual meskipun dropdown hanya menampilkan Guru. Scope `eligibleToTeach` kini menjadi sumber tunggal untuk kandidat, validasi server, import, rebuild, statistik, dan notifikasi. Jalur Admin biasa/multi-jenjang/ganti guru, jalur Waka, import, dan notifikasi diuji dengan ID Sekretaris; seluruhnya menolak atau mengosongkan penugasan secara aman. Pembersihan database transaksional mengubah 7 jadwal invalid menjadi `kosong` sambil menulis history, lalu menghapus 18 baris turunan dan 2 notifikasi salah sasaran. Audit setelah perbaikan: 0 jadwal, 0 penugasan, dan 0 notifikasi invalid.

Pola backend laporan: fungsi tanggal/raw SQL wajib sadar driver database. Agregasi per hari tidak boleh langsung mengunci `DAY()` MySQL; route test SQLite harus berjalan bersama audit live MySQL agar halaman yang tampak benar di lokal tidak gagal pada environment test/deploy lain.

Backlog global: indeks pencarian menu belum sepenuhnya sadar role. Saat dikerjakan, sumber hasil harus mengikuti konfigurasi navigation/capability role aktif agar menu role lain tidak muncul lalu berakhir 403.

Validasi sidebar dan toolbar Jadwal terbaru: sidebar Admin, Waka, Sekretaris, Ketua, serta Bendahara mengunci semua kontrol tingkat utama ke border 0 px dan menyisakan border 1 px hanya pada submenu. Latar sidebar dibuat sedikit lebih muda namun tetap biru tua; empat tone menu terukur berbeda pada DOM nyata, sedangkan footer akun memakai gradien penuh dengan link transparan. Pada Admin dan Waka, empat aksi Jadwal mobile berada dalam satu grid setinggi 40 px; desktop 1024/1440 px memakai flex dengan tinggi 44 px, font 14 px, serta padding horizontal terukur 16–20 px. Panel Aksi/Ekspor terukur 308 px di viewport 390 px, seluruh item `nowrap`, overflow dokumen 0 px, dan tidak ada error console. Bootstrap memberi `!important` pada utility spacing, sehingga breakpoint Tailwind campuran wajib memakai responsive important dan diverifikasi dari computed style.

Gerbang sebelum push 12 September 2026: Blade cache dan build produksi berhasil. Regresi gabungan keamanan penugasan/notifikasi, Jadwal Admin/Waka, seluruh flow Bendahara, filter Tagihan, serta komponen global menghasilkan 30 test lulus dengan 426 assertion. `git diff --check` bersih dan seluruh akun/file audit sementara telah dihapus.

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
