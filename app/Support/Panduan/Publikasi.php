<?php

namespace App\Support\Panduan;

/**
 * Panduan publikasi sekolah (berita, flyer, pengumuman, kalender akademik) milik Sekretaris dan Admin,
 * serta catatan instruksi internal yang dikirim Admin, Ketua PKBM, dan Waka.
 */
class Publikasi
{
    public static function daftar(?string $role): array
    {
        $prioritas = ['Biasa' => 'Informasi umum.', 'Penting' => 'Perlu perhatian penerima.', 'Mendesak' => 'Harus segera dibaca dan ditindaklanjuti.'];

        return [
            'sekretaris.dashboard' => [
                'judul' => 'Dashboard Sekretaris',
                'isi' => 'Ringkasan agenda dan publikasi sekolah. Mulai dari kegiatan hari ini, lalu pilih pekerjaan utama sesuai informasi yang ingin dikelola.',
                'langkah' => ['Periksa "Kegiatan hari ini" untuk agenda yang sedang berjalan.', 'Catat kegiatan di Kalender Akademik terlebih dahulu.', 'Bila informasinya perlu menjangkau warga sekolah, lanjutkan ke Pengumuman, Flyer, atau Berita dari "Pekerjaan utama".'],
            ],

            'admin.akademik.kalender.index,sekretaris.kalender.index' => [
                'judul' => 'Kalender Akademik',
                'isi' => 'Agenda sekolah pada tahun ajaran aktif. Agenda berstatus Aktif terlihat oleh pengguna; Draft hanya terlihat di sini.',
                'langkah' => ['Pilih tampilan kalender visual (bulan, minggu, atau tahun) atau daftar detail.', 'Tekan "Tambah agenda" untuk membuat kegiatan baru.', 'Buka agenda untuk melihat atau mengubah detailnya.'],
            ],
            'admin.akademik.kalender.create,sekretaris.kalender.create,admin.akademik.kalender.edit,sekretaris.kalender.edit' => [
                'judul' => 'Mengisi Kegiatan',
                'isi' => 'Tanggal dan status menentukan kapan agenda terlihat pada kalender. Kosongkan jam untuk kegiatan sepanjang hari.',
                'langkah' => ['Isi agenda dan pilih Draft bila tanggal belum final.', 'Status Aktif menampilkan kegiatan pada kalender pengguna.', 'Pengumuman pengingat dapat dibuat otomatis tiga hari sebelum kegiatan.'],
            ],
            'admin.akademik.kalender.show,sekretaris.kalender.show' => ['judul' => 'Detail Kegiatan', 'isi' => 'Informasi lengkap satu agenda. Perubahan dapat dilakukan langsung dari halaman ini.'],

            'admin.akademik.pengumuman.index,sekretaris.pengumuman.index' => [
                'judul' => 'Kelola Pengumuman',
                'isi' => 'Informasi untuk warga sekolah, baik yang dibuat manual maupun yang terhubung ke kalender akademik.',
                'langkah' => ['Tekan "Tambah Pengumuman" untuk membuat informasi baru.', 'Gunakan prioritas Mendesak hanya untuk informasi yang harus segera dibaca.'],
                'istilah' => $prioritas + ['Dari kalender' => 'Pengumuman yang dibuat otomatis dari agenda kalender akademik.'],
            ],
            'admin.akademik.pengumuman.create,sekretaris.pengumuman.create,admin.akademik.pengumuman.edit,sekretaris.pengumuman.edit' => [
                'judul' => 'Mengisi Pengumuman',
                'isi' => 'Gunakan judul singkat dan isi yang langsung menjelaskan tindakan pengguna. Pengumuman aktif mengirim notifikasi saat pertama kali dibuat.',
                'istilah' => $prioritas + ['Hubungan kalender' => 'Perubahan pada kalender dapat memperbarui pengumuman terkait, tetapi perubahan pengumuman tidak mengubah kalender.'],
            ],

            'admin.akademik.flyer.index,sekretaris.flyer.index' => [
                'judul' => 'Kelola Flyer',
                'isi' => 'Pop-up informasi di beranda pengguna. Hanya flyer aktif dalam periode tayang yang ditampilkan, dan hanya kepada target penggunanya.',
            ],
            'admin.akademik.flyer.create,sekretaris.flyer.create,admin.akademik.flyer.edit,sekretaris.flyer.edit' => [
                'judul' => 'Mengisi Flyer',
                'isi' => 'Flyer ditampilkan berdasarkan urutan, target pengguna, status, dan periode aktif. Pilih Draft untuk memeriksa konten tanpa menayangkannya.',
                'langkah' => ['Unggah gambar yang teksnya tetap terbaca di layar ponsel (rasio 16:9 atau 4:3 paling fleksibel).', 'Pilih target: siswa, guru, wali kelas, wali siswa, atau semua pengguna.', 'Tentukan periode tayang, lalu periksa pratinjau sebelum menyimpan.'],
            ],

            'admin.akademik.berita.index,sekretaris.berita.index' => [
                'judul' => 'Kelola Berita',
                'isi' => 'Berita sekolah berupa kartu ringkas yang mengarah ke artikel lengkap. Berita unggulan yang aktif dikirim sebagai notifikasi kepada pengguna.',
                'istilah' => ['Aktif' => 'Berita tayang dan dapat dibaca.', 'Draft' => 'Belum tayang; aman untuk diperiksa dulu.', 'Unggulan' => 'Berita penting; saat aktif, pengguna menerima notifikasi.'],
            ],
            'admin.akademik.berita.create,sekretaris.berita.create,admin.akademik.berita.edit,sekretaris.berita.edit' => [
                'judul' => 'Mengisi Berita',
                'isi' => 'Berita berupa kartu ringkas yang mengarah ke artikel lengkap. Berita aktif dan unggulan dapat memicu notifikasi bagi pengguna.',
                'langkah' => ['Pastikan judul dan ringkasan mudah dipahami tanpa membuka tautan.', 'Gunakan gambar mendatar agar kartu tidak terpotong pada ponsel.', 'Uji URL sumber dan pilih Draft jika konten belum siap tayang.'],
            ],

            'admin.catatan.index,ketua.catatan.index,waka.catatan.index' => [
                'judul' => 'Catatan Instruksi',
                'isi' => 'Pesan tindak lanjut untuk tenaga pendidik atau staf. Penerima melihat catatan di menu mereka, dan Anda dapat memantau siapa saja yang sudah membacanya.',
                'langkah' => ['Tekan tombol kirim catatan untuk membuat pesan baru.', 'Buka catatan untuk melihat status baca setiap penerima.'],
            ],
            'admin.catatan.create,ketua.catatan.create,waka.catatan.create' => [
                'judul' => 'Kirim Catatan',
                'isi' => 'Tulis pesan, tentukan penerima, lalu pilih prioritas. Pilih jangkauan penerima yang paling sempit agar pesan tetap relevan.',
                'langkah' => ['Gunakan judul singkat dan isi yang langsung menjelaskan tindak lanjut.', 'Pilih penerima; gunakan "Pilih yang tampil" setelah menyaring daftar.', 'Gunakan prioritas Mendesak hanya untuk pesan yang membutuhkan tindakan segera.'],
            ],
            'admin.catatan.show,ketua.catatan.show,waka.catatan.show' => ['judul' => 'Detail Catatan', 'isi' => 'Isi catatan dan status baca penerimanya. Status diperbarui ketika penerima membuka catatan.'],
        ];
    }
}
