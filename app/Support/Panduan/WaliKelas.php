<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman Wali Kelas (prefix wali.*). Semua pekerjaan mengikuti kelas yang dipilih
 * di "Pilih Kelas"; ganti kelas dari dashboard bila Anda mewalikan lebih dari satu kelas.
 */
class WaliKelas
{
    public static function daftar(?string $role): array
    {
        return [
            'wali.pilih-kelas' => [
                'judul' => 'Pilih Kelas',
                'isi' => 'Pilih kelas yang ingin dikelola; semua menu (presensi, nilai, rapor) akan mengikuti kelas ini sampai Anda menggantinya.',
            ],
            'wali.dashboard' => [
                'judul' => 'Dashboard Wali Kelas',
                'isi' => 'Ringkasan kelas yang sedang Anda kelola: kehadiran hari ini, izin yang perlu divalidasi, dan rapor yang masih draft.',
                'langkah' => ['Mulai dari "Tindak lanjut" untuk pekerjaan yang menunggu.', 'Gunakan "Akses cepat" untuk membuka nilai, prediksi kenaikan, validasi akses, atau arsip.', 'Tekan "Ganti kelas" bila ingin mengelola kelas lain.'],
            ],
            'wali.jadwal.index,wali.jadwal-pelajaran' => [
                'judul' => 'Jadwal Pelajaran Kelas',
                'isi' => 'Jadwal mingguan kelas Anda. Jadwal dikelola oleh Admin; wali kelas dapat melihat dan mencetaknya.',
            ],

            'wali.presensi.index' => [
                'judul' => 'Presensi Siswa',
                'isi' => 'Catat kehadiran seluruh siswa kelas per tanggal. Status yang dipilih tersimpan untuk semua siswa sekaligus saat Anda menekan simpan.',
                'langkah' => ['Pilih tanggal presensi.', 'Tandai Hadir, Sakit, Izin, atau Alpha untuk setiap siswa.', 'Simpan presensi. Presensi juga dapat diimpor dari Excel memakai template khusus kelas ini.'],
                'istilah' => ['Validasi izin' => 'Pengajuan sakit/izin dari wali siswa yang harus Anda setujui atau tolak.', 'Riwayat & edit' => 'Tempat mengoreksi presensi tanggal sebelumnya.'],
            ],
            'wali.presensi.rekap-harian' => ['judul' => 'Rekap Harian', 'isi' => 'Ringkasan presensi per tanggal pada periode terpilih. Pilih tanggal untuk melihat dan mengoreksi presensi siswa.'],
            'wali.presensi.show-harian' => ['judul' => 'Detail Presensi', 'isi' => 'Presensi seluruh siswa pada satu tanggal. Tekan Edit untuk mengoreksi, atau Cetak untuk arsip.'],
            'wali.presensi.riwayat' => ['judul' => 'Riwayat Presensi', 'isi' => 'Telusuri catatan kehadiran menurut siswa, status, atau tanggal, lalu perbaiki data yang keliru.'],
            'wali.presensi.validasi-izin' => [
                'judul' => 'Validasi Izin',
                'isi' => 'Pengajuan sakit dan izin dari wali siswa yang menunggu keputusan Anda. Periksa keterangan dan bukti sebelum menyetujui atau menolak.',
                'langkah' => ['Buka atau unduh bukti yang dilampirkan.', 'Setujui bila keterangannya sah, atau tolak dengan alasan.'],
            ],

            'wali.nilai.index' => [
                'judul' => 'Nilai Siswa',
                'isi' => 'Rekap nilai akademik siswa di kelas Anda. Pilih semester dan mata pelajaran, lalu buka nilai tiap siswa.',
                'istilah' => ['Nilai guru vs wali' => 'Sebelum Anda merevisi, nilai dari guru mapel langsung menjadi nilai aktif. Setelah Anda merevisi, nilai aktif untuk siswa dan rapor tetap versi Anda sampai Anda menyinkronkan nilai guru.'],
            ],
            'wali.nilai.show' => [
                'judul' => 'Detail Nilai Siswa',
                'isi' => 'Nilai setiap mata pelajaran untuk satu siswa pada semester terpilih. Kolom kosong berarti belum diisi; angka 0 tetap nilai yang tercatat.',
                'langkah' => ['Bila muncul pemberitahuan guru mengubah nilai yang sudah Anda revisi, bandingkan lalu sinkronkan bila setuju.', 'Tekan Edit untuk mengubah nilai, lalu lanjutkan ke rapor.'],
            ],
            'wali.nilai.edit' => [
                'judul' => 'Edit Nilai Siswa',
                'isi' => 'Ubah nilai per mata pelajaran untuk satu siswa. Nilai yang Anda simpan menjadi nilai aktif untuk siswa dan rapor.',
                'langkah' => ['Ubah nilai yang perlu direvisi, lalu "Simpan semua perubahan".', 'Gunakan "Bandingkan nilai guru" untuk melihat versi guru mapel, dan sinkronkan bila ingin memakai nilai guru.', 'Untuk banyak nilai sekaligus, gunakan "Import Excel".'],
            ],

            'wali.rapor.index' => [
                'judul' => 'Kelola Rapor',
                'isi' => 'Rapor PTS dan PAS siswa di kelas Anda. Rapor yang selesai dikirim ke Ketua PKBM untuk divalidasi sebelum dapat dibuka wali siswa.',
                'langkah' => ['Pilih jenis rapor dan semester.', 'Buka rapor siswa untuk melengkapi nilai, kehadiran, ekstrakurikuler, dan catatan.', 'Terapkan template tampilan ke seluruh kelas bila perlu.', 'Atur tanggal rilis lalu terbitkan, atau unggah rapor dalam bentuk PDF.'],
                'istilah' => ['PTS' => 'Rapor tengah semester.', 'PAS' => 'Rapor akhir semester.', 'Draft' => 'Rapor belum dikirim; masih bisa diubah.'],
            ],
            'wali.rapor.edit' => [
                'judul' => 'Edit Rapor',
                'isi' => 'Lengkapi isi rapor satu siswa: kehadiran, nilai mata pelajaran, ekstrakurikuler, catatan wali kelas, dan pengaturan tampilan.',
                'langkah' => ['Tekan "Sinkron dari Presensi" agar kehadiran sesuai catatan presensi.', 'Gunakan "Reset nilai" bila nilai siswa berubah dan rapor perlu mengambil nilai terbaru.', 'Gunakan "Salin format" untuk menyalin pengaturan tampilan ke rapor siswa lain.', 'Simpan perubahan, lalu pratinjau sebelum dikirim.'],
            ],
            'wali.rapor-pending' => ['judul' => 'Rapor Pending Saya', 'isi' => 'Rapor draft atau yang dikembalikan untuk revisi, termasuk dari kelas tahun ajaran lalu yang pernah Anda walikan.'],
            'wali.rapor.request-download.index' => [
                'judul' => 'Permintaan Download Rapor',
                'isi' => 'Wali siswa meminta tautan unduh rapor anaknya. Setelah Anda menyetujui, tautan berlaku selama 24 jam.',
            ],
            'wali.template-capaian.index' => [
                'judul' => 'Template Capaian Kompetensi',
                'isi' => 'Kalimat deskripsi capaian per mata pelajaran yang dipakai di rapor. Template membantu menulis deskripsi yang seragam tanpa mengetik ulang untuk setiap siswa.',
            ],
            'wali.kenaikan-kelas.prediction' => [
                'judul' => 'Prediksi Kenaikan Kelas',
                'isi' => 'Simulasi kelayakan naik kelas berdasarkan ketuntasan nilai dan kondisi keuangan saat ini. Hasil ini hanya perkiraan; keputusan akhir diproses Admin.',
            ],
            'wali.validasi-akses.index' => [
                'judul' => 'Status Akses Siswa',
                'isi' => 'Status akses ujian dan rapor setiap siswa di kelas Anda. Akses dibuka oleh petugas keuangan berdasarkan pembayaran atau dispensasi; halaman ini untuk memantau.',
            ],
            'wali.arsip.index' => ['judul' => 'Arsip Kelas Saya', 'isi' => 'Riwayat kelas yang pernah Anda walikan lintas tahun ajaran. Arsip bersifat baca saja.'],
            'wali.arsip.show,wali.arsip.nilai,wali.arsip.presensi,wali.arsip.rapor' => ['judul' => 'Arsip Kelas', 'isi' => 'Data historis satu kelas: siswa yang tercatat, nilai, presensi, dan rapor. Semua data ditampilkan tanpa aksi edit.'],
        ];
    }
}
