<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman Guru: shell SIA (dashboard, jadwal, kelas, arsip, catatan monitoring)
 * dan LMS HOK Teaching per kelas + mata pelajaran (materi, tugas, latihan/ujian, forum, meeting, nilai).
 */
class Guru
{
    public static function daftar(?string $role): array
    {
        $ujianForm = [
            'judul' => 'Mengisi Latihan / Ujian',
            'isi' => 'Tentukan judul, waktu buka-tutup, dan durasi pengerjaan. Setelah disimpan, lanjutkan dengan menambahkan soal.',
            'langkah' => ['Isi judul dan deskripsi atau petunjuk pengerjaan.', 'Tentukan tanggal mulai, tanggal selesai, dan durasi (menit).', 'Atur apakah boleh diulang, batas pengulangan, serta apakah nilai dan riwayat ditampilkan ke siswa.', 'Simpan, lalu tambahkan soal.'],
        ];
        $soal = [
            'judul' => 'Kelola Soal',
            'isi' => 'Daftar soal pada latihan/ujian ini. Gunakan editor lengkap untuk mengubah banyak soal sekaligus, mengimpor dari Excel, atau membuat soal dengan AI Question Generator.',
            'istilah' => ['Tipe soal' => 'Pilihan ganda, pilihan ganda kompleks, benar/salah, isian singkat, dan uraian. Uraian dinilai manual saat koreksi.'],
        ];
        $soalForm = [
            'judul' => 'Mengisi Soal',
            'isi' => 'Pilih tipe soal terlebih dahulu; opsi jawaban dan kunci menyesuaikan tipe yang dipilih.',
            'langkah' => ['Pilih tipe soal dan tulis pertanyaannya.', 'Isi opsi jawaban dan tandai kuncinya; untuk uraian, kunci jawaban menjadi acuan saat mengoreksi.', 'Tentukan bobot nilai, lalu simpan.'],
        ];
        $koreksiUjian = ['judul' => 'Koreksi Jawaban', 'isi' => 'Jawaban otomatis sudah dinilai sistem. Beri nilai untuk soal uraian/esai, lalu simpan; pastikan semua soal uraian sudah dinilai sebelum menyimpan.'];
        $hasil = ['judul' => 'Hasil Pengerjaan', 'isi' => 'Rekap peserta, jumlah yang selesai, nilai rata-rata, dan nilai tertinggi. Buka jawaban siswa untuk mengoreksi soal uraian.'];

        return [
            'guru.dashboard' => [
                'judul' => 'Dashboard Guru',
                'isi' => 'Ringkasan aktivitas mengajar: jadwal hari ini dan kelas yang Anda ampu. Masuk ke LMS kelas dari kartu kelas.',
                'langkah' => ['Periksa jadwal mengajar hari ini.', 'Pilih kelas untuk membuka LMS kelas dan mata pelajarannya.', 'Gunakan Arsip LMS untuk memakai ulang konten tahun lalu.'],
            ],
            'guru.jadwal.index' => ['judul' => 'Jadwal Mengajar', 'isi' => 'Jadwal mingguan Anda di semua kelas. Jadwal disusun oleh Admin; hubungi Admin bila ada yang tidak sesuai.'],
            'guru.kelas.index' => ['judul' => 'Daftar Kelas', 'isi' => 'Kelas yang Anda ajar pada tahun ajaran aktif. Pilih kelas, lalu pilih mata pelajaran untuk masuk ke LMS.'],
            'guru.kelas.mapel' => ['judul' => 'Pilih Mata Pelajaran', 'isi' => 'Mata pelajaran yang Anda ajar di kelas ini. Tekan "Masuk LMS" untuk mengelola materi, tugas, ujian, dan forum diskusi; setiap kombinasi kelas dan mapel memiliki ruang LMS sendiri.'],
            'guru.lms.arsip.index' => [
                'judul' => 'Arsip LMS',
                'isi' => 'Semua materi, tugas, latihan, dan ujian yang pernah Anda buat lintas tahun ajaran. Salin ke kelas aktif untuk dipakai ulang tanpa membuat dari awal.',
            ],
            'guru.lms.arsip.form-salin' => ['judul' => 'Salin Konten Arsip', 'isi' => 'Pilih kelas dan mata pelajaran tujuan di tahun ajaran aktif. Hanya kelas dan mapel yang Anda ampu yang bisa dipilih.'],
            'guru.lms.catatan-monitoring.index' => ['judul' => 'Catatan Monitoring', 'isi' => 'Masukan dari Ketua PKBM, Wakil Kepala Sekolah, atau Admin tentang konten pembelajaran Anda. Catatan baru ditandai belum dibaca.'],
            'guru.lms.catatan-monitoring.show' => ['judul' => 'Detail Catatan Monitoring', 'isi' => 'Isi masukan dari pimpinan. Tekan "Buka & revisi konten" untuk langsung memperbaiki konten yang dimaksud.'],

            'guru.lms.dashboard' => [
                'judul' => 'Beranda Kelas',
                'isi' => 'Ruang belajar satu kelas dan mata pelajaran: jumlah siswa, materi, tugas, dan jawaban yang perlu dikoreksi.',
                'langkah' => ['Gunakan "Aksi cepat" untuk mengunggah materi atau membuat tugas.', 'Dahulukan angka "Perlu koreksi".', 'Menu samping berisi Materi, Tugas, Latihan, Ujian, Forum, Kelas virtual, dan Nilai.'],
            ],
            'guru.lms.materi.index' => [
                'judul' => 'Materi Pembelajaran',
                'isi' => 'Materi yang Anda bagikan untuk kelas dan mata pelajaran ini, dikelompokkan menurut tanggal unggah. Siswa di kelas ini langsung bisa membuka materi yang sudah diunggah.',
                'langkah' => [
                    'Tekan "Tambah materi" lalu unggah berkas (PDF, PPT, Word, video) atau tempel tautan.',
                    'Gunakan filter tanggal untuk menemukan materi yang diunggah pada hari tertentu.',
                    'Ubah materi dengan tombol Edit. Saat menghapus, Anda bisa memilih ikut menghapus materi yang sama di kelas lain.',
                ],
                'istilah' => ['Modul ajar' => 'Dokumen perencanaan pembelajaran; ditandai label ungu agar mudah dibedakan dari materi biasa.'],
            ],
            'guru.lms.materi.create,guru.lms.materi.edit' => [
                'judul' => 'Mengisi Materi',
                'isi' => 'Unggah berkas atau tempel tautan (misalnya video YouTube). Pilih kategori Modul ajar untuk dokumen perencanaan.',
            ],
            'guru.lms.tugas.index' => [
                'judul' => 'Tugas',
                'isi' => 'Tugas kelas ini beserta jumlah pengumpulan. Buka tugas untuk mengoreksi jawaban siswa.',
                'langkah' => ['Tekan "Buat tugas baru".', 'Pantau pengumpulan dan tenggat.', 'Buka koreksi untuk memberi nilai.'],
            ],
            'guru.lms.tugas.create,guru.lms.tugas.edit' => [
                'judul' => 'Mengisi Tugas',
                'isi' => 'Isi judul, deskripsi, lampiran (opsional), tanggal mulai, dan tenggat. Atur apakah siswa boleh mengulang pengumpulan dan apakah nilai ditampilkan ke siswa.',
                'istilah' => ['Batas pengulangan' => 'Berapa kali siswa boleh mengubah jawaban; kosong berarti tanpa batas.'],
            ],
            'guru.lms.tugas.koreksi' => ['judul' => 'Koreksi Tugas', 'isi' => 'Daftar siswa beserta jawabannya. Buka jawaban untuk memberi nilai dan umpan balik.'],
            'guru.lms.tugas.koreksi.show' => ['judul' => 'Koreksi Jawaban Siswa', 'isi' => 'Baca jawaban dan lampiran siswa, lalu beri nilai. Saran AI (bila aktif) hanya bantuan; nilai akhir tetap keputusan Anda.'],

            'guru.lms.latihan.index' => ['judul' => 'Latihan', 'isi' => 'Latihan soal untuk siswa berlatih. Buka latihan untuk mengelola soal, melihat hasil, atau mengoreksi.'],
            'guru.lms.ujian.index' => [
                'judul' => 'Ujian',
                'isi' => 'Ujian kelas ini (ulangan harian, PTS, PAS, TO, UPK). Ujian semester hanya bisa dibuka siswa yang aksesnya terbuka dari sisi keuangan: lunas, divalidasi, atau mendapat dispensasi.',
                'langkah' => ['Buat ujian, lalu tambahkan soal.', 'Saat ujian berlangsung, buka Pengawasan untuk memantau aktivitas siswa.', 'Setelah selesai, buka Hasil untuk mengoreksi soal uraian.'],
            ],
            'guru.lms.latihan.create,guru.lms.latihan.edit' => $ujianForm,
            'guru.lms.ujian.create,guru.lms.ujian.edit' => $ujianForm + ['istilah' => ['Tipe ujian' => 'Menentukan kolom nilai yang terisi: ulangan harian, PTS/PAS ganjil-genap, TO, atau UPK.']],
            'guru.lms.latihan.soal.index,guru.lms.ujian.soal.index,guru.lms.latihan.soal.manage,guru.lms.ujian.soal.manage' => $soal,
            'guru.lms.latihan.soal.create,guru.lms.latihan.soal.edit,guru.lms.ujian.soal.create,guru.lms.ujian.soal.edit' => $soalForm,
            'guru.lms.latihan.hasil,guru.lms.ujian.hasil' => $hasil,
            'guru.lms.latihan.koreksi.show,guru.lms.ujian.koreksi.show' => $koreksiUjian,
            'guru.lms.ujian.pengawasan' => [
                'judul' => 'Pengawasan Ujian',
                'isi' => 'Pantau aktivitas siswa selama ujian berlangsung, misalnya berpindah tab atau keluar dari halaman ujian, beserta peta soal yang sudah dijawab.',
            ],

            'guru.lms.forum.index' => ['judul' => 'Diskusi Kelas', 'isi' => 'Topik diskusi kelas. Buka topik untuk membalas, atau sematkan/tutup diskusi dari daftar.'],
            'guru.lms.forum.create' => ['judul' => 'Diskusi Baru', 'isi' => 'Tulis topik dan pertanyaan pemantik agar siswa mudah memulai diskusi. Lampiran gambar atau dokumen boleh ditambahkan.'],
            'guru.lms.forum.show' => ['judul' => 'Forum Diskusi', 'isi' => 'Baca dan balas diskusi siswa. Balasan boleh disertai lampiran.'],
            'guru.lms.meeting.index' => ['judul' => 'Kelas Virtual', 'isi' => 'Jadwal pertemuan daring kelas ini. Siswa melihat tautan meeting yang aktif di LMS mereka.'],
            'guru.lms.meeting.create,guru.lms.meeting.edit' => ['judul' => 'Jadwal Kelas Virtual', 'isi' => 'Isi judul, platform, tautan meeting, serta waktu mulai dan selesai. Nonaktifkan bila pertemuan dibatalkan.'],

            'guru.lms.nilai.index' => [
                'judul' => 'Nilai Siswa',
                'isi' => 'Isi nilai Tugas 1–5, Latihan 1–5, dan UH 1–5 secara bertahap. Rata-rata dihitung otomatis saat disimpan; kolom kosong tidak dihitung sebagai 0.',
                'langkah' => ['Isi atau ubah nilai, lalu tekan "Simpan semua".', 'Gunakan "Hitung ulang" untuk menghitung ulang rata-rata dan nilai akhir semua siswa.', 'Untuk banyak nilai sekaligus, gunakan "Impor nilai" dari Excel.'],
                'istilah' => [
                    'Nilai akhir' => '((Rata Tugas × 1) + (Rata Latihan × 1) + (Rata UH × 2) + (PTS × 3) + (PAS × 3)) ÷ 10.',
                    'Diedit wali' => 'Wali kelas sudah mengubah nilai siswa ini. Simpanan Anda tersimpan sebagai versi guru dan tidak langsung mengubah nilai rapor; wali kelas meninjaunya lewat Sinkron dari Guru.',
                    'Tingkat akhir' => 'Penilaian TO, UPK, dan ujian praktik khusus kelas 6 SD, 9 SMP, dan 12 SMA.',
                ],
            ],
        ];
    }
}
