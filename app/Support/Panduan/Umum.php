<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman yang dipakai semua peran: notifikasi, profil, dan pengaturan akun.
 */
class Umum
{
    public static function daftar(?string $role): array
    {
        return [
            'notifications.index' => [
                'judul' => 'Pusat Notifikasi',
                'isi' => 'Semua pemberitahuan untuk akun Anda. Buka notifikasi untuk langsung menuju halaman terkait.',
                'langkah' => ['Saring menurut status baca atau jenis notifikasi.', 'Tekan "Tandai semua dibaca" setelah selesai memeriksa.', 'Centang beberapa notifikasi untuk menandai atau menghapusnya sekaligus.'],
            ],
            'notifications.show' => ['judul' => 'Detail Notifikasi', 'isi' => 'Isi lengkap pemberitahuan. Gunakan tombol sumber untuk membuka halaman yang dimaksud.'],
            'profile.index' => ['judul' => 'Profil Saya', 'isi' => 'Identitas, foto, dan informasi kontak Anda. Untuk mengubah data akun atau kata sandi, buka Pengaturan Akun.'],
            'account.settings' => [
                'judul' => 'Pengaturan Akun',
                'isi' => 'Kelola informasi profil, kata sandi, dan keamanan pemulihan akun.',
                'langkah' => ['Perbarui nama, username, atau email di Informasi Profil.', 'Ganti kata sandi secara berkala; isi kata sandi lama terlebih dahulu.', 'Lengkapi email pribadi serta pertanyaan dan jawaban keamanan pada Keamanan Pemulihan agar akun dapat dipulihkan bila lupa kata sandi.'],
            ],
        ];
    }
}
