<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman Wali Siswa (orang tua): pantau presensi, rapor, dan tagihan setiap anak.
 * Teks kanal pembayaran sengaja netral karena payment gateway berbeda per branch.
 */
class WaliSiswa
{
    public static function daftar(?string $role): array
    {
        return [
            'wali-siswa.dashboard' => [
                'judul' => 'Dashboard Wali Siswa',
                'isi' => 'Ringkasan setiap anak yang terhubung dengan akun Anda: kelas, kehadiran, dan kondisi pembayaran. Pilih anak di menu samping untuk membuka presensi, rapor, atau tagihannya.',
                'istilah' => ['Dikelola wali lain' => 'Keuangan anak ini diurus oleh akun wali lain yang terhubung, sehingga tagihannya tidak ditampilkan di akun Anda.'],
            ],
            'wali-siswa.presensi.anak' => [
                'judul' => 'Presensi Anak',
                'isi' => 'Rekap kehadiran anak pada bulan berjalan. Data diperbarui oleh wali kelas setelah pembelajaran.',
                'langkah' => ['Anak sakit atau ada keperluan? Tekan "Ajukan Izin" dan lampirkan bukti.', 'Pantau keputusan wali kelas di Riwayat Pengajuan Izin.'],
            ],
            'wali-siswa.presensi.riwayat-presensi' => ['judul' => 'Riwayat Presensi', 'isi' => 'Catatan kehadiran anak per tanggal. Saring menurut bulan atau status untuk menemukan catatan tertentu.'],
            'wali-siswa.presensi.ajukan-izin' => [
                'judul' => 'Ajukan Izin',
                'isi' => 'Beri tahu wali kelas bila anak tidak dapat hadir. Pengajuan akan diperiksa dan disetujui atau ditolak oleh wali kelas.',
                'langkah' => ['Pilih tanggal ketidakhadiran.', 'Pilih jenis (sakit atau izin) dan tulis keterangan.', 'Lampirkan bukti, misalnya surat dokter, lalu kirim.'],
            ],
            'wali-siswa.presensi.riwayat-izin' => ['judul' => 'Riwayat Pengajuan Izin', 'isi' => 'Status setiap pengajuan izin: menunggu, disetujui, atau ditolak oleh wali kelas. Pengajuan yang masih menunggu dapat diubah.'],
            'wali-siswa.presensi.edit-izin' => ['judul' => 'Edit Pengajuan Izin', 'isi' => 'Perbarui keterangan atau bukti pengajuan yang belum diputuskan. Tanggal tidak dapat diubah; buat pengajuan baru bila tanggalnya keliru.'],
            'wali-siswa.rapor.anak' => [
                'judul' => 'Rapor Anak',
                'isi' => 'Daftar rapor anak yang sudah diterbitkan sekolah. Rapor tampil setelah divalidasi Ketua PKBM dan akses rapor dibuka sesuai status pembayaran.',
            ],
            'wali-siswa.rapor.detail' => [
                'judul' => 'Detail Rapor',
                'isi' => 'Isi rapor anak. Untuk menyimpan salinan, kirim permintaan download; setelah disetujui wali kelas, tautan unduh tersedia selama 24 jam.',
            ],
            'wali-siswa.tagihan.anak' => [
                'judul' => 'Tagihan & Pembayaran',
                'isi' => 'Daftar tagihan anak, yang sudah dibayar, dan sisanya. Pilih tagihan yang ingin dibayar, lalu pilih metode pembayaran.',
                'langkah' => ['Centang tagihan yang akan dibayar.', 'Pilih metode: Direct Transfer (transfer ke rekening sekolah lalu unggah bukti), kanal digital, atau tunai di sekolah.', 'Pembayaran Direct Transfer dan tunai diperiksa bendahara sebelum tagihan dinyatakan lunas.'],
                'istilah' => ['Terlambat' => 'Tagihan melewati jatuh tempo.', 'Sudah dialihkan' => 'Tunggakan tahun lalu yang sudah dipindahkan ke tagihan tahun ajaran aktif.'],
            ],
            'wali-siswa.pembayaran.digital' => [
                'judul' => 'Pembayaran Digital',
                'isi' => 'Ikuti instruksi pembayaran sesuai kanal yang dipilih. Status diperbarui otomatis setelah pembayaran diterima; Anda dapat menutup halaman ini dan melanjutkan dari riwayat tagihan.',
                'langkah' => ['Selesaikan pembayaran sebelum batas waktu.', 'Tekan "Cek Status Pembayaran" bila status belum berubah.', 'Ingin ganti kanal? Transaksi lama dibatalkan dengan aman sebelum kanal baru dibuat.'],
            ],
        ];
    }
}
