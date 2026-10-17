<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman akademik yang dipakai bersama Admin dan Wakil Kepala Sekolah:
 * kelas, penempatan siswa, jadwal pelajaran, guru pengajar, dan penugasan wali kelas.
 * Waka hanya melihat dan mengelola data cabangnya sendiri.
 */
class Akademik
{
    public static function daftar(?string $role): array
    {
        $waka = $role === 'wakil_kepala_sekolah';
        $cakupan = $waka ? ' Data yang tampil hanya milik cabang Anda.' : '';

        return [
            'admin.kelas.index,waka.kelas.index' => [
                'judul' => 'Data Kelas',
                'isi' => 'Daftar kelas pada tahun ajaran yang dipilih beserta jumlah siswa dan wali kelasnya. Dari sini Anda bisa langsung mengatur wali dan menempatkan siswa.'.$cakupan,
                'langkah' => array_values(array_filter([
                    'Tambah kelas satu per satu dengan "Tambah", atau banyak sekaligus lewat "Import".',
                    'Buka detail kelas untuk menentukan wali kelas dan menempatkan siswa.',
                    $waka ? null : 'Awal tahun ajaran baru? Gunakan "Salin" untuk menyalin daftar kelas dari periode sebelumnya.',
                ])),
                'istilah' => [
                    'Kuota' => 'Jumlah maksimal siswa dalam satu kelas; penempatan siswa tidak boleh melebihinya.',
                    'Belum ada wali' => 'Kelas yang belum memiliki wali kelas; segera tentukan agar rapor dan presensi bisa dikelola.',
                ],
            ],
            'admin.kelas.create,waka.kelas.create,admin.kelas.edit,waka.kelas.edit' => [
                'judul' => 'Mengisi Data Kelas',
                'isi' => 'Lengkapi tahun ajaran, cabang, nama kelas, jenjang, dan kuota siswa. Wali kelas boleh dipilih sekarang atau nanti dari halaman detail kelas.',
                'langkah' => ['Pilih tahun ajaran dan cabang terlebih dahulu.', 'Isi nama kelas dan jenjang sesuai rombongan belajar.', 'Tentukan kuota, lalu simpan. Siswa ditempatkan dari halaman detail kelas.'],
            ],
            'admin.kelas.import,waka.kelas.import' => [
                'judul' => 'Import Kelas dari Excel',
                'isi' => 'Cara cepat memasukkan banyak kelas. Sistem memeriksa setiap baris sebelum menyimpan; baris yang salah dilewati dan alasannya ditampilkan.',
                'langkah' => ['Unduh template Excel — template berisi struktur kolom yang benar.', 'Isi data kelas; samakan nama cabang dan tahun ajaran dengan data di sistem. Jangan ubah nama kolom.', 'Pilih file .xlsx atau .xls (maksimal 5 MB), lalu tekan "Import data".'],
            ],
            'admin.kelas.show,waka.kelas.show' => [
                'judul' => 'Detail Kelas',
                'isi' => 'Ringkasan satu kelas: informasi kelas, wali kelas, dan daftar siswanya. Sisa kuota menunjukkan berapa siswa lagi yang masih bisa ditempatkan.',
                'langkah' => ['Tekan "Tentukan wali" untuk menambah atau mengganti wali kelas.', 'Tekan "Tempatkan siswa" untuk memasukkan siswa yang belum memiliki kelas.'],
            ],
            'admin.kelas.manage-siswa,waka.kelas.manage-siswa,admin.manajemen-siswa.per-kelas,waka.manajemen-siswa.per-kelas' => [
                'judul' => 'Kelola Siswa Kelas',
                'isi' => 'Tempatkan siswa ke kelas ini. Kandidat yang muncul hanya siswa aktif dari cabang yang sama dan belum memiliki kelas, sehingga satu siswa tidak tercatat di dua kelas.',
                'langkah' => ['Centang siswa yang akan dimasukkan (atau gunakan "Pilih sesuai kuota").', 'Pastikan jumlah pilihan tidak melebihi sisa kuota.', 'Simpan; siswa langsung tercatat sebagai anggota kelas.'],
            ],
            'admin.manajemen-siswa.index,waka.manajemen-siswa.index' => [
                'judul' => 'Penempatan Siswa',
                'isi' => 'Cari siswa, lalu tentukan kelasnya. Filter periode dipakai untuk melihat riwayat penempatan pada tahun ajaran lain tanpa mengubah data saat ini.'.$cakupan,
                'langkah' => ['Saring siswa berdasarkan periode, kelas, atau status penempatan.', 'Centang siswa yang akan dipindahkan atau ditempatkan.', 'Pilih kelas tujuan lalu tekan "Terapkan kelas".'],
                'istilah' => ['Belum ada kelas' => 'Siswa aktif yang belum ditempatkan di kelas mana pun pada periode terpilih.'],
            ],
            'admin.manajemen-siswa.show,waka.manajemen-siswa.show' => [
                'judul' => 'Detail Siswa',
                'isi' => 'Identitas siswa, kelas yang ditempati, dan akun wali siswa yang terhubung. Wali siswa yang terhubung bisa memantau presensi, nilai, rapor, dan tagihan anak.',
                'langkah' => ['Ubah kelas pada bagian "Kelas dan penempatan", lalu simpan.', 'Tambahkan wali siswa: hubungkan akun yang sudah ada atau buat akun baru.', 'Gunakan "Cetak kartu" untuk mencetak kartu identitas siswa.'],
            ],
            'admin.jadwal-pelajaran.index,waka.jadwal-pelajaran.index' => [
                'judul' => 'Kelola Jadwal Pelajaran',
                'isi' => 'Susun jadwal mengajar per kelas untuk tahun ajaran yang dipilih. Sistem menolak jadwal yang bentrok, baik untuk guru maupun kelas, pada jam yang sama.'.$cakupan,
                'langkah' => array_values(array_filter([
                    'Pilih tahun ajaran, lalu saring menurut cabang, jenjang, kelas, atau guru.',
                    'Tekan "Tambah" untuk membuat sesi baru, atau "Import Excel" di menu Aksi untuk banyak sesi sekaligus.',
                    $waka ? null : 'Atur jam istirahat lewat tombol "Istirahat" agar tidak terpakai untuk pelajaran.',
                    'Gunakan menu Aksi untuk menyalin jadwal ke periode lain (Duplikasi) atau mengganti seluruh sesi seorang guru sekaligus.',
                    'Unduh jadwal lewat "Ekspor" dalam bentuk PDF atau Excel, untuk semua kelas maupun per kelas.',
                ])),
                'istilah' => [
                    'Jadwal aktif' => 'Jumlah sesi pelajaran pada tahun ajaran yang dipilih.',
                    'Menunggu guru' => 'Sesi yang belum memiliki guru pengajar; segera isi agar kelas tidak kosong.',
                    'Guru mengajar' => 'Jumlah guru yang memiliki minimal satu sesi.',
                    'Kelas terjadwal' => 'Jumlah kelas yang sudah memiliki jadwal.',
                ],
            ],
            'admin.jadwal-pelajaran.create,waka.jadwal-pelajaran.create,admin.jadwal-pelajaran.edit,waka.jadwal-pelajaran.edit' => [
                'judul' => 'Mengisi Jadwal Pelajaran',
                'isi' => 'Jadwal disusun berurutan: kelas, mata pelajaran, guru, lalu hari dan jam. Pilihan mapel dan guru otomatis menyesuaikan jenjang kelas yang dipilih.',
                'langkah' => ['Pilih periode dan satu atau beberapa kelas yang menerima jadwal yang sama.', 'Pilih mata pelajaran, lalu guru pengajarnya.', 'Tentukan hari, jam mulai, dan jam selesai. Periksa waktu istirahat yang ditampilkan; jadwal yang bentrok dengan guru atau kelas lain akan ditolak.'],
            ],
            'admin.jadwal-pelajaran.import,waka.jadwal-pelajaran.import' => [
                'judul' => 'Import Jadwal dari Excel',
                'isi' => 'Masukkan banyak jadwal sekaligus. Gunakan template agar nama cabang, kelas, mapel, dan format jam terbaca tepat. Kelas atau mapel yang tidak ditemukan akan dilewati; guru yang tidak ditemukan membuat jadwal berstatus kosong (menunggu guru).',
                'langkah' => [
                    'Pilih tahun ajaran target.',
                    'Unduh template, lalu hapus baris contoh berwarna kuning.',
                    'Isi nama_cabang, nama_kelas, nama_mapel, hari, jam_mulai, dan jam_selesai. Nama guru dan keterangan boleh dikosongkan.',
                    'Untuk beberapa kelas sekaligus, pisahkan nama kelas dengan koma, lalu unggah file (.xlsx/.xls, maks. 5 MB).',
                ],
            ],
            'admin.jadwal-pelajaran.show,waka.jadwal-pelajaran.show' => [
                'judul' => 'Jadwal per Kelas',
                'isi' => 'Jadwal mingguan satu kelas, diurutkan per hari dan jam. Gunakan halaman ini untuk memeriksa apakah setiap hari sudah terisi dengan benar.',
            ],
            'admin.guru-pengajar.index,waka.guru-pengajar.index' => [
                'judul' => 'Guru Pengajar',
                'isi' => 'Daftar guru beserta kelas dan mata pelajaran yang diajar. Penugasan ini dibentuk otomatis dari jadwal pelajaran, jadi untuk mengubahnya ubah jadwalnya.'.$cakupan,
                'langkah' => ['Cari guru atau saring menurut periode.', 'Tekan "Sinkronkan" bila penugasan belum sesuai dengan jadwal terbaru.', 'Buka detail guru untuk melihat kelas, mapel, dan jadwal mengajarnya.'],
            ],
            'admin.guru-pengajar.show,waka.guru-pengajar.show' => [
                'judul' => 'Detail Guru Pengajar',
                'isi' => 'Kelas, mata pelajaran, dan jadwal mengajar seorang guru pada periode terpilih. Semua data berasal dari jadwal pelajaran.',
            ],
            'admin.guru-pengajar.manage-kelas,waka.guru-pengajar.manage-kelas' => [
                'judul' => 'Guru Pengajar per Kelas',
                'isi' => 'Ringkasan guru dan mata pelajaran di satu kelas beserta jadwalnya. Bila ada mapel tanpa guru, lengkapi melalui menu Jadwal Pelajaran.',
            ],
            'admin.wali-kelas.index,waka.wali-kelas.index' => [
                'judul' => 'Penugasan Wali Kelas',
                'isi' => 'Pastikan setiap kelas memiliki wali kelas. Wali kelas mengelola presensi, nilai, dan rapor siswa di kelasnya.'.$cakupan,
                'langkah' => ['Saring kelas, lalu perhatikan kelas yang belum memiliki wali.', 'Tekan "Tambahkan wali" untuk memilih tenaga pendidik sebagai wali.', 'Gunakan "Kelola wali aktif" untuk melepas atau mengganti wali.'],
                'istilah' => ['Wali kelas' => 'Satu kelas boleh memiliki lebih dari satu wali; semuanya dapat mengelola kelas tersebut.'],
            ],
            'admin.wali-kelas.show,waka.wali-kelas.show' => [
                'judul' => 'Atur Wali Kelas',
                'isi' => 'Kelola wali yang bertugas di kelas ini dan lihat siswa yang akan didampingi. Satu kelas dapat memiliki lebih dari satu wali.',
            ],
        ];
    }
}
