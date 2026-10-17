<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman Siswa: shell SIA (beranda, presensi, penilaian) dan HOK Learning (LMS).
 * Halaman pengerjaan latihan/ujian yang sedang berlangsung memakai shell ujian tanpa panduan.
 */
class Siswa
{
    public static function daftar(?string $role): array
    {
        return [
            'siswa.sia.dashboard' => [
                'judul' => 'Sistem Informasi Akademik',
                'isi' => 'Ringkasan harian Anda: pengumuman, jadwal hari ini, tugas mendatang, kehadiran, dan nilai terbaru.',
                'langkah' => ['Baca pengumuman terbaru.', 'Periksa jadwal dan tugas yang harus dikerjakan.', 'Tekan "Masuk LMS" untuk membuka materi, tugas, dan ujian.'],
            ],
            'siswa.sia.presensi.index' => [
                'judul' => 'Presensi Kehadiran',
                'isi' => 'Riwayat kehadiran Anda bulan ini. Data presensi diperbarui oleh wali kelas setelah pembelajaran; pengajuan izin dilakukan oleh wali siswa (orang tua).',
                'istilah' => ['Hadir / Sakit / Izin / Alpha' => 'Alpha berarti tidak hadir tanpa keterangan.'],
            ],
            'siswa.sia.penilaian' => [
                'judul' => 'Data Penilaian Harian',
                'isi' => 'Rekap nilai tugas, latihan, dan ujian per mata pelajaran. Nilai ini bersifat sementara sebelum dirilis di rapor. Hubungi wali kelas bila mata pelajaran belum muncul.',
                'istilah' => [
                    'Nilai tugas' => 'Rata-rata dari aktivitas harian.',
                    'Nilai PTS / PAS' => 'Skor murni hasil ujian tengah dan akhir semester.',
                    'Nilai akhir' => 'Akumulasi semua nilai sesuai bobot kurikulum.',
                ],
            ],

            'siswa.lms.dashboard' => [
                'judul' => 'Beranda HOK Learning',
                'isi' => 'Pusat belajar daring: jadwal hari ini, tugas dengan tenggat terdekat, mata pelajaran, pengumuman, dan agenda minggu ini.',
                'langkah' => ['Dahulukan tugas dengan tenggat terdekat.', 'Pilih mata pelajaran untuk membuka materi, tugas, latihan, dan ujian.', 'Tekan "Akses SIA" untuk kembali melihat presensi dan nilai.'],
            ],
            'siswa.lms.mapel.show' => [
                'judul' => 'Ruang Mata Pelajaran',
                'isi' => 'Semua materi, tugas, latihan, dan ujian dari guru untuk mata pelajaran ini. Label pada setiap item menunjukkan apakah sudah dibuka, dikerjakan, atau ditutup.',
                'istilah' => [
                    'Belum dirilis / Belum dimulai' => 'Guru sudah membuat, tetapi waktunya belum dibuka.',
                    'Belum memiliki akses' => 'Ujian semester terkunci sampai pembayaran lunas atau mendapat dispensasi. Hubungi orang tua/bendahara.',
                    'Lihat hasil' => 'Pengerjaan selesai dan guru mengizinkan nilai ditampilkan.',
                ],
            ],
            'siswa.lms.mapel.materi' => [
                'judul' => 'Materi Pembelajaran',
                'isi' => 'Baca materi dengan seksama sebelum mengerjakan tugas. Catat hal penting, unduh materi bila ingin belajar luring, dan tanyakan di forum diskusi bila ada yang belum dipahami.',
            ],
            'siswa.lms.mapel.tugas.show' => [
                'judul' => 'Pengerjaan Tugas',
                'isi' => 'Baca deskripsi dan lampiran dari guru, lalu kirim jawaban sebelum tenggat. Bila guru mengizinkan, jawaban masih bisa diubah sesuai batas yang ditentukan.',
            ],
            'siswa.lms.tugas.index' => [
                'judul' => 'Daftar Tugas',
                'isi' => 'Semua tugas dari berbagai mata pelajaran. Saring menurut mata pelajaran atau status untuk menemukan tugas yang belum dikerjakan.',
                'istilah' => ['Menunggu nilai' => 'Jawaban sudah dikirim dan sedang menunggu koreksi guru.', 'Terlambat' => 'Dikumpulkan setelah tenggat atau tenggat sudah lewat.'],
            ],
            'siswa.lms.mapel.latihan.show,siswa.lms.mapel.ujian.show' => [
                'judul' => 'Latihan & Ujian',
                'isi' => 'Baca petunjuk, waktu, dan durasi sebelum memulai. Setelah dimulai, waktu terus berjalan; tetap di halaman pengerjaan dan jangan berpindah tab karena aktivitas dipantau guru. Setelah selesai, nilai tampil di sini bila guru mengizinkan.',
            ],
            'siswa.lms.mapel.latihan.review,siswa.lms.mapel.ujian.review' => ['judul' => 'Pembahasan', 'isi' => 'Jawaban Anda beserta kunci dan pembahasannya. Gunakan untuk belajar dari kesalahan.'],
            'siswa.lms.mapel.forum.index' => ['judul' => 'Forum Diskusi', 'isi' => 'Topik diskusi yang dibuka guru. Buka topik untuk ikut membalas; topik baru hanya dapat dibuat oleh guru.'],
            'siswa.lms.mapel.forum.show' => ['judul' => 'Diskusi', 'isi' => 'Tulis balasan yang sopan dan sesuai topik. Lampiran gambar atau dokumen boleh ditambahkan.'],
            'siswa.lms.mapel.meeting.index' => ['judul' => 'Kelas Virtual', 'isi' => 'Jadwal pertemuan daring mata pelajaran ini. Tombol bergabung hanya aktif selama tautan meeting masih berlaku.'],
            'siswa.lms.jadwal' => ['judul' => 'Jadwal Pelajaran', 'isi' => 'Jadwal rutin mingguan kelas Anda. Klik nama mata pelajaran untuk membuka materi dan tugasnya.'],
            'siswa.lms.kalender' => ['judul' => 'Kalender Akademik', 'isi' => 'Agenda dan kegiatan sekolah. Pilih tanggal untuk melihat kegiatan dan jadwal pelajaran pada hari itu.'],
            'siswa.lms.kalender.detail' => ['judul' => 'Detail Kegiatan', 'isi' => 'Kegiatan dan jadwal pelajaran pada tanggal yang dipilih.'],
            'siswa.lms.guru' => ['judul' => 'Daftar Guru Pengajar', 'isi' => 'Guru yang mengajar di kelas Anda beserta mata pelajaran dan kontaknya.'],
            'siswa.lms.pengumuman.index' => [
                'judul' => 'Pengumuman',
                'isi' => 'Informasi dari sekolah. Gunakan tab prioritas untuk melihat pengumuman mendesak terlebih dahulu.',
                'istilah' => ['Mendesak' => 'Harus segera dibaca.', 'Penting' => 'Perlu perhatian.', 'Biasa' => 'Informasi umum.'],
            ],
            'siswa.lms.pengumuman.show' => ['judul' => 'Detail Pengumuman', 'isi' => 'Isi lengkap pengumuman beserta lampirannya, bila ada.'],
        ];
    }
}
