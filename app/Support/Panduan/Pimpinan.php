<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman pemantauan dan keputusan pimpinan: dashboard Ketua PKBM & Waka, monitoring
 * (dipakai bersama Admin, Ketua, Waka), pusat laporan, dan validasi rapor oleh Ketua.
 */
class Pimpinan
{
    public static function daftar(?string $role): array
    {
        $cakupan = $role === 'wakil_kepala_sekolah' ? ' Data yang tampil hanya milik cabang Anda.' : '';

        return [
            'ketua.dashboard' => [
                'judul' => 'Dashboard Ketua PKBM',
                'isi' => 'Ringkasan kondisi PKBM untuk pengawasan dan pengambilan keputusan. Mode pengawasan: analisis dilakukan di menu monitoring, sedangkan perubahan data operasional tetap dilakukan petugas terkait.',
                'langkah' => ['Lihat jumlah siswa, tenaga pendidik, kelas, dan dispensasi yang menunggu keputusan.', 'Gunakan "Akses modul utama" untuk membuka persetujuan, monitoring, atau laporan.', 'Pantau aktivitas login terbaru di bagian bawah.'],
            ],
            'waka.dashboard' => [
                'judul' => 'Ringkasan Akademik',
                'isi' => 'Kesiapan akademik cabang Anda dalam satu layar: siswa, tenaga pendidik, kelas, mata pelajaran, dan penugasan wali kelas.',
                'langkah' => ['Dahulukan kelas yang belum memiliki wali kelas.', 'Pantau siswa yang baru terdaftar.', 'Buka modul kerja (Kelas, Jadwal, Penugasan Wali, Guru Pengajar, Manajemen Siswa) dari "Akses modul utama".'],
            ],

            'admin.monitoring.pengguna,ketua.monitoring.pengguna' => [
                'judul' => 'Status warga dan akun sekolah',
                'isi' => 'Pantau siapa saja tenaga pendidik dan siswa yang sudah memiliki akun aktif, dan siapa yang perlu ditindaklanjuti. Halaman ini hanya untuk memantau; perubahan data dilakukan di menu Pengguna.',
                'langkah' => [
                    'Lihat ringkasan jumlah tenaga pendidik, siswa, total akun, dan persentase akun aktif di bagian atas.',
                    'Pada daftar Tenaga pendidik, cari berdasarkan nama lalu saring menurut peran atau status akun.',
                    'Pada daftar Siswa, saring menurut status siswa untuk menemukan siswa yang belum aktif.',
                ],
                'istilah' => [
                    'Akun aktif' => 'Akun yang bisa dipakai untuk masuk ke aplikasi.',
                    'Nonaktif' => 'Akun dikunci sementara; pemiliknya tidak bisa masuk sampai diaktifkan lagi.',
                ],
            ],
            'admin.monitoring.siswa,ketua.monitoring.siswa,waka.monitoring.siswa' => [
                'judul' => 'Aktivitas belajar dan keuangan',
                'isi' => 'Progres tugas dan ujian setiap siswa beserta sisa tagihannya. Progres dihitung dari aktivitas LMS yang benar-benar tersedia untuk kelas siswa.'.$cakupan,
                'langkah' => ['Saring siswa menurut cabang atau kelas aktif.', 'Perhatikan siswa dengan progres rendah atau sisa tagihan besar untuk ditindaklanjuti.'],
                'istilah' => ['Rata-rata tugas / ujian' => 'Persentase tugas atau ujian yang sudah dikerjakan dari yang tersedia.', 'Sisa tagihan yang tampil' => 'Jumlah sisa tagihan dari siswa yang sedang ditampilkan di daftar.'],
            ],
            'admin.monitoring.wali-kelas,ketua.monitoring.wali-kelas,waka.monitoring.wali-kelas' => [
                'judul' => 'Progress penerbitan rapor',
                'isi' => 'Kemajuan penyelesaian rapor pada setiap kelas. Gunakan untuk melihat kelas yang perlu ditindaklanjuti pada periode dan cabang tertentu.'.$cakupan,
                'istilah' => ['Rapor selesai' => 'Dihitung dari rapor yang sudah berstatus diterbitkan.'],
            ],
            'admin.monitoring.guru-pengajar,ketua.monitoring.guru-pengajar,waka.monitoring.guru-pengajar' => [
                'judul' => 'Aktivitas guru dan kelengkapan nilai',
                'isi' => 'Materi, tugas, ujian, pertemuan, dan kelengkapan nilai setiap guru dalam satu ringkasan. Data mengikuti penugasan pada tahun ajaran dan cabang yang dipilih.'.$cakupan,
                'langkah' => ['Pilih tahun ajaran dan cabang.', 'Perhatikan guru dengan konten LMS sedikit atau nilai yang belum lengkap.', 'Kirim catatan instruksi bila perlu tindak lanjut.'],
            ],
            'admin.monitoring.lms.index,ketua.monitoring.lms.index,waka.monitoring.lms.index' => [
                'judul' => 'Monitoring LMS',
                'isi' => 'Tinjau materi, tugas, latihan, dan ujian yang diunggah guru per kelas.'.$cakupan,
                'langkah' => ['Cari kelas atau pilih periode tahun ajaran.', 'Aktifkan filter kelas yang memiliki konten untuk mempersempit daftar.', 'Buka kelas untuk memeriksa rincian konten dan aktivitasnya.'],
            ],
            'admin.monitoring.lms.kelas,ketua.monitoring.lms.kelas,waka.monitoring.lms.kelas' => [
                'judul' => 'Detail Konten Kelas',
                'isi' => 'Seluruh konten LMS di satu kelas. Saring menurut judul, mata pelajaran, atau rentang tanggal, lalu buka pratinjau untuk memeriksa isinya.',
            ],

            'admin.laporan.index,ketua.laporan.index' => [
                'judul' => 'Pusat Laporan',
                'isi' => 'Kumpulan dokumen cetak: data siswa, tenaga pendidik, kelas, wali kelas, guru pengajar, dan rekap akademik.',
                'langkah' => ['Tentukan filter di dalam kartu dokumen yang dibutuhkan.', 'Tekan "Buka pratinjau"; dokumen terbuka di tab baru.', 'Periksa pratinjau, lalu cetak atau simpan sebagai PDF dari browser.'],
                'istilah' => ['Rekap akademik per TA' => 'Snapshot kenaikan kelas, kelulusan, tunggakan, dan dispensasi pada satu tahun ajaran.'],
            ],

            'ketua.validasi-rapor.index' => [
                'judul' => 'Validasi Akses Rapor',
                'isi' => 'Rapor yang dikirim wali kelas menunggu keputusan Anda. Buka pratinjau sebelum memutuskan; revisi mengembalikan rapor beserta catatan perbaikan kepada wali kelas.',
                'langkah' => ['Wali kelas mengirim rapor.', 'Buka pratinjau dan periksa isinya.', 'Validasi rapor, atau kembalikan untuk revisi dengan catatan.', 'Setelah divalidasi, petugas keuangan membuka akses rapor sesuai status pembayaran.'],
                'istilah' => ['Validasi semua' => 'Berlaku pada seluruh rapor yang memenuhi syarat, bukan hanya yang tampil. Pastikan semuanya sudah ditinjau.'],
            ],
        ];
    }
}
