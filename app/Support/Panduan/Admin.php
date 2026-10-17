<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman khusus Admin: persiapan data awal, pengguna, pengaturan sistem,
 * kenaikan kelas, dan bantuan pemulihan akun.
 */
class Admin
{
    private const ISTILAH_STATUS_SISWA = [
        'Aktif' => 'Siswa yang sedang belajar dan bisa masuk ke aplikasi.',
        'Lulus / Pindah / Keluar' => 'Siswa yang tidak lagi belajar; datanya tetap tersimpan sebagai arsip.',
        'Akun nonaktif' => 'Pemilik akun tidak bisa masuk ke aplikasi sampai akunnya diaktifkan kembali.',
    ];

    private const IMPORT = ['Unduh template Excel dan isi sesuai keterangan kolom; jangan ubah nama kolom.', 'Unggah file .xlsx atau .xls.', 'Periksa ringkasan hasil: baris yang bermasalah dilewati dan alasannya ditampilkan.'];

    public static function daftar(?string $role): array
    {
        return [
            'admin.dashboard' => [
                'judul' => 'Beranda Admin',
                'isi' => 'Titik awal pekerjaan Admin: ringkasan data sekolah, urutan persiapan periode baru, dan pintasan ke pekerjaan rutin.',
                'langkah' => ['Saat menyiapkan tahun ajaran baru, kerjakan "Urutan persiapan sekolah" dari nomor pertama.', 'Untuk pekerjaan harian, pilih kartu pada "Pekerjaan yang sering dilakukan".', 'Pantau pendaftaran siswa dan login terbaru di bagian bawah.'],
            ],

            // --- Langkah 1–3 persiapan data ---
            'admin.tahun-ajaran.index' => [
                'judul' => 'Tahun Ajaran',
                'isi' => 'Langkah pertama persiapan data. Hanya satu tahun ajaran yang aktif; tahun ajaran aktif dipakai sebagai periode bawaan kelas, jadwal, tagihan, nilai, dan rapor.',
                'langkah' => ['Tambah tahun ajaran baru sebelum membuat kelas dan jadwal.', 'Tekan "Aktifkan" saat periode baru resmi dimulai.', 'Buka detail untuk melihat periode semester dan rapor.'],
            ],
            'admin.tahun-ajaran.create,admin.tahun-ajaran.edit' => [
                'judul' => 'Mengisi Tahun Ajaran',
                'isi' => 'Isi nama periode (misalnya 2026/2027) serta tanggal mulai dan selesainya. Tanggal semester genap dan batas akhir PTS dipakai untuk menghitung kehadiran pada rapor.',
                'istilah' => ['PTS' => 'Penilaian Tengah Semester; rapor PTS menghitung kehadiran sampai tanggal akhir PTS.', 'PAS' => 'Penilaian Akhir Semester; rapor PAS menghitung kehadiran sampai akhir semester.'],
            ],
            'admin.tahun-ajaran.show' => [
                'judul' => 'Detail Tahun Ajaran',
                'isi' => 'Periode semester, rentang rapor PTS/PAS, dan kelas yang memakai tahun ajaran ini. Tahun ajaran yang masih dipakai kelas tidak dapat dihapus.',
            ],
            'admin.cabang.index' => [
                'judul' => 'Data Cabang',
                'isi' => 'Lokasi atau unit sekolah. Cabang dipilih saat membuat pengguna dan kelas, serta membatasi data yang dilihat Wakil Kepala Sekolah.',
                'langkah' => ['Tambah cabang sebelum memasukkan pengguna dan kelas.', 'Nonaktifkan cabang yang tidak beroperasi tanpa menghapus datanya.', 'Buka detail untuk melihat kelas, tenaga pendidik, dan siswa di cabang itu.'],
            ],
            'admin.cabang.create,admin.cabang.edit' => [
                'judul' => 'Mengisi Data Cabang',
                'isi' => 'Isi kode, nama, alamat, dan telepon cabang. Cabang nonaktif tidak muncul sebagai pilihan saat membuat data baru.',
            ],
            'admin.cabang.show' => [
                'judul' => 'Detail Cabang',
                'isi' => 'Ringkasan satu cabang: tenaga pendidik yang terhubung, daftar kelas beserta wali, dan siswa aktifnya.',
            ],
            'admin.users.index' => [
                'judul' => 'Data Pengguna',
                'isi' => 'Pusat akun warga sekolah. Setiap jenis pengguna mempunyai identitas dan hak akses berbeda.',
                'langkah' => ['Masukkan tenaga pendidik terlebih dahulu (guru, wali kelas, pengurus).', 'Masukkan siswa, satu per satu atau lewat import Excel.', 'Buat akun wali siswa dan hubungkan dengan anaknya.', 'Lanjutkan ke penempatan siswa ke kelas.'],
            ],
            'admin.users.siswa' => [
                'judul' => 'Data Siswa',
                'isi' => 'Daftar seluruh siswa beserta kelas, cabang, dan status akunnya — termasuk siswa yang akunnya sedang dinonaktifkan, agar tetap bisa diaktifkan kembali atau dihapus.',
                'langkah' => [
                    'Cari dengan nama, NIS, atau NISN; persempit dengan filter cabang, jenjang, kelas, dan status.',
                    'Tambah siswa satu per satu dengan "Tambah", atau banyak sekaligus lewat "Template" lalu "Import".',
                    'Centang beberapa siswa untuk menghapus sekaligus. Siswa yang masih punya data keuangan atau nilai meminta konfirmasi terakhir.',
                ],
                'istilah' => self::ISTILAH_STATUS_SISWA + ['Belum masuk kelas' => 'Siswa yang belum ditempatkan di kelas pada tahun ajaran berjalan.'],
            ],
            'admin.users.create-siswa,admin.users.edit-siswa' => [
                'judul' => 'Mengisi Data Siswa',
                'isi' => 'Satu formulir untuk akun login, biodata, data orang tua, dan kelas. Kolom bertanda bintang wajib diisi.',
                'langkah' => ['Isi username dan kata sandi akun siswa.', 'Lengkapi biodata, NIS/NISN, dan data orang tua.', 'Pilih kelas bila sudah diketahui (bisa diatur nanti dari Manajemen Siswa).', 'Hubungkan wali siswa bila akunnya sudah ada.'],
                'istilah' => ['Akses akademik' => 'Bila dicentang, wali siswa yang terhubung dapat melihat data akademik (nilai dan rapor) siswa ini.'],
            ],
            'admin.users.show-siswa' => [
                'judul' => 'Detail Siswa',
                'isi' => 'Biodata lengkap siswa dan akun wali yang dapat mengakses informasinya. Untuk mengubah data, gunakan tombol Edit pada daftar siswa.',
            ],
            'admin.users.import-siswa' => ['judul' => 'Import Siswa', 'isi' => 'Masukkan banyak siswa sekaligus dari Excel. Akun login siswa dibuat otomatis dari data yang diimpor.', 'langkah' => self::IMPORT],
            'admin.users.tenaga-pendidik' => [
                'judul' => 'Tenaga Pendidik',
                'isi' => 'Akun guru, wali kelas, dan pengurus sekolah. Peran menentukan menu yang bisa dibuka; cabang menentukan data mana yang dikelola.',
                'langkah' => ['Cari dengan nama atau saring menurut peran.', 'Tambah satu per satu atau lewat import Excel.', 'Nonaktifkan akun yang tidak lagi bertugas daripada menghapusnya.'],
                'istilah' => [
                    'Guru Pengajar' => 'Mengajar mata pelajaran dan mengelola LMS kelasnya.',
                    'Wali Kelas' => 'Mengelola presensi, nilai, dan rapor kelas yang diwalikan.',
                    'Ketua PKBM / Wakil Kepala Sekolah' => 'Memantau dan memberi keputusan; Waka terbatas pada cabangnya.',
                    'Sekretaris / Bendahara' => 'Mengelola publikasi atau keuangan sekolah.',
                ],
            ],
            'admin.users.create-tenaga-pendidik,admin.users.edit-tenaga-pendidik' => [
                'judul' => 'Mengisi Data Tenaga Pendidik',
                'isi' => 'Isi akun login, peran, cabang, dan biodata. Peran menentukan menu yang tersedia setelah pengguna masuk.',
                'langkah' => ['Pilih peran sesuai tugas utama.', 'Pilih cabang tempat bertugas.', 'Isi username, kata sandi, NIP (bila ada), dan kontak.'],
            ],
            'admin.users.show-tenaga-pendidik' => ['judul' => 'Detail Tenaga Pendidik', 'isi' => 'Biodata, kontak, dan informasi akun tenaga pendidik.'],
            'admin.users.import-tenaga-pendidik' => ['judul' => 'Import Tenaga Pendidik', 'isi' => 'Masukkan banyak tenaga pendidik sekaligus dari Excel beserta peran dan cabangnya.', 'langkah' => self::IMPORT],
            'admin.users.wali-siswa' => [
                'judul' => 'Wali Siswa',
                'isi' => 'Akun orang tua atau wali. Setelah dihubungkan dengan anaknya, wali dapat memantau presensi, nilai, rapor, dan membayar tagihan.',
                'langkah' => ['Tambah akun wali, lalu hubungkan dengan satu atau beberapa siswa.', 'Buka detail untuk melihat anak yang terhubung.'],
            ],
            'admin.users.wali-siswa.create' => [
                'judul' => 'Tambah Wali Siswa',
                'isi' => 'Akun dapat dibuat dahulu dan dihubungkan ke siswa sekarang atau nanti.',
                'langkah' => ['Isi data akun wali.', 'Cari siswa, centang yang sesuai, lalu pilih satu hubungan keluarga untuk semua siswa terpilih.', 'Simpan.'],
            ],
            'admin.users.edit-wali-siswa' => ['judul' => 'Edit Wali Siswa', 'isi' => 'Perbarui data akun dan jenis hubungan keluarga. Perubahan di sini tidak mengubah data siswa.'],
            'admin.users.show-wali-siswa' => ['judul' => 'Detail Wali Siswa', 'isi' => 'Informasi akun wali dan anak yang terhubung. Hubungan dengan anak diatur dari halaman edit siswa atau edit wali.'],
            'admin.users.import-wali-siswa' => ['judul' => 'Import Wali Siswa', 'isi' => 'Masukkan banyak akun wali sekaligus dan hubungkan dengan siswa melalui kolom identitas siswa di template.', 'langkah' => self::IMPORT],

            // --- Langkah 5 & pengaturan jadwal ---
            'admin.mata-pelajaran.index' => [
                'judul' => 'Mata Pelajaran',
                'isi' => 'Daftar pelajaran per jenjang. Siapkan mata pelajaran sebelum menyusun guru dan jadwal.',
                'langkah' => ['Cari atau saring menurut jenjang.', 'Tambah satu per satu atau lewat import Excel.', 'Buka detail untuk melihat jadwal yang memakai pelajaran tersebut.'],
            ],
            'admin.mata-pelajaran.create,admin.mata-pelajaran.edit' => [
                'judul' => 'Mengisi Mata Pelajaran',
                'isi' => 'Isi kode, nama, dan jenjang. Kelompok dan akses berdasarkan agama bersifat opsional.',
                'istilah' => ['Akses berdasarkan agama' => 'Kosongkan untuk pelajaran umum. Bila diisi, hanya siswa beragama tersebut yang dapat mengakses pelajaran ini di LMS.'],
            ],
            'admin.mata-pelajaran.import' => [
                'judul' => 'Import Mata Pelajaran',
                'isi' => 'Masukkan banyak pelajaran sekaligus. Kode yang sudah ada dilewati dan peringatannya ditampilkan.',
                'langkah' => ['Unduh template, lalu hapus baris contoh.', 'Isi data tanpa mengubah nama kolom; ikuti keterangan pada tabel format kolom.', 'Simpan sebagai Excel lalu unggah.'],
            ],
            'admin.mata-pelajaran.show' => ['judul' => 'Detail Mata Pelajaran', 'isi' => 'Informasi pelajaran beserta kelas, waktu, dan guru yang sudah memakainya di jadwal.'],
            'admin.pengaturan-istirahat.index' => [
                'judul' => 'Pengaturan Istirahat',
                'isi' => 'Jeda aktif otomatis memblokir slot ketika jadwal dibuat dan ditandai pada hasil cetak. Setiap jenjang dapat memiliki maksimal dua jeda.',
                'istilah' => ['Hari aktif' => 'Jeda hanya berlaku pada hari yang dipilih; hari lain tetap dapat dipakai untuk pelajaran.'],
            ],
            'admin.pengaturan-istirahat.create,admin.pengaturan-istirahat.edit' => [
                'judul' => 'Mengisi Waktu Istirahat',
                'isi' => 'Tentukan nama jeda, jam mulai dan selesai, hari berlaku, serta urutannya. Nonaktifkan bila jeda tidak lagi dipakai.',
            ],

            // --- Kenaikan kelas ---
            'admin.akademik.kenaikan-kelas.settings.index' => [
                'judul' => 'Pengaturan Kenaikan Kelas',
                'isi' => 'Ambang akademik dan jadwal proses otomatis yang dipakai simulasi, eksekusi manual, dan eksekusi terjadwal. Pastikan tahun ajaran dan kelas tujuan sudah dibuat.',
                'istilah' => ['Keuangan' => 'Tagihan lunas atau memiliki dispensasi yang sah.', 'Akademik' => 'Nilai mata pelajaran memenuhi ambang ketuntasan.', 'KKM' => 'Kriteria Ketuntasan Minimal; harus tersedia sebelum simulasi akhir dijalankan.'],
            ],
            'admin.akademik.kenaikan-kelas.kkm.index' => [
                'judul' => 'Pengaturan KKM',
                'isi' => 'Nilai minimum setiap mata pelajaran yang dipakai untuk menilai ketuntasan saat kenaikan kelas. Semua nilai harus antara 0 dan 100.',
                'langkah' => ['Pilih jenjang.', 'Periksa dan ubah nilai KKM tiap mata pelajaran.', 'Tekan "Simpan seluruh KKM".'],
            ],
            'admin.akademik.kenaikan-kelas.report' => [
                'judul' => 'Rekap Kenaikan Kelas',
                'isi' => 'Satu alur untuk menyimulasikan, memproses, dan menelusuri kenaikan kelas. Proses dijalankan dari tab Simulasi setelah semua persiapan lengkap.',
                'langkah' => ['Buka tab Simulasi untuk melihat kelayakan setiap siswa.', 'Naikkan siswa terpilih atau jalankan proses kenaikan kelas.', 'Tab Riwayat menampilkan hasil eksekusi; tab Jadwal mengatur eksekusi otomatis.'],
                'istilah' => ['Naik kelas / Lulus' => 'Memenuhi syarat akademik dan keuangan.', 'Dengan tunggakan' => 'Naik atau lulus dengan dispensasi meskipun masih ada tagihan.', 'Tidak naik' => 'Belum memenuhi syarat; dapat dinaikkan manual bila syaratnya kemudian terpenuhi.'],
            ],

            // --- Pengaturan sistem ---
            'admin.ai-settings.index' => [
                'judul' => 'Pengaturan AI Assistant',
                'isi' => 'Satu provider AI dipakai oleh semua fitur (chatbot, generator soal, bantuan koreksi) dan semua peran.',
                'langkah' => ['Pilih provider dan model, lalu isi API key dari Groq Console atau Google AI Studio.', 'Aktifkan fitur AI yang dibutuhkan.', 'Pilih peran yang boleh memakai chatbot (Admin selalu memiliki akses), lalu simpan.'],
            ],
            'admin.lms-settings.index' => [
                'judul' => 'Pengaturan LMS',
                'isi' => 'Tentukan jenjang yang boleh memakai HOK-LMS (materi, tugas, dan ujian daring). Perubahan berlaku untuk seluruh siswa pada jenjang terkait.',
                'istilah' => ['Jenjang dinonaktifkan' => 'Menu LMS disembunyikan dan tugas, materi, serta ujian tidak dapat dibuka. Data lama tidak dihapus dan tersedia lagi saat diaktifkan kembali.'],
            ],
            'admin.landing-pages.index' => ['judul' => 'Manajemen Landing Page', 'isi' => 'Halaman publik situs sekolah. Pilih halaman yang ingin diperbarui; perubahan isi tidak mengubah alamat halamannya.'],
            'admin.landing-pages.edit' => [
                'judul' => 'Edit Landing Page',
                'isi' => 'Atur isi dan visibilitas setiap bagian halaman publik. Gunakan navigasi bagian untuk berpindah cepat.',
                'istilah' => ['Reset' => 'Mengembalikan seluruh konten landing page ke isi awal. Perubahan yang sudah dibuat akan hilang.'],
            ],
            'admin.keuangan.info-pembayaran.index' => [
                'judul' => 'Informasi Pembayaran',
                'isi' => 'Atur cara pembayaran yang ditawarkan kepada wali siswa: transfer langsung ke rekening, kanal digital, dan pembayaran tunai di kasir.',
                'istilah' => ['Direct Transfer' => 'Wali mentransfer ke rekening sekolah lalu mengunggah bukti; bendahara memvalidasi.', 'Kanal Digital' => 'Pembayaran melalui payment gateway; status diperbarui otomatis.', 'Pembayaran Kasir' => 'Pembayaran tunai yang dicatat bendahara di loket.'],
            ],
            'admin.recovery-tickets.index' => [
                'judul' => 'Tiket Pemulihan Akun',
                'isi' => 'Permintaan dari pengguna yang tidak dapat memulihkan akunnya sendiri. Prioritaskan tiket berstatus butuh bantuan atau email gagal.',
                'langkah' => ['Simpan nomor WhatsApp bantuan login yang akan dihubungi pengguna.', 'Periksa identitas pemohon, lalu Selesaikan, Kirim ulang tautan, atau Tolak.'],
            ],
            'admin.recovery-tickets.history' => ['judul' => 'Riwayat Pemulihan Akun', 'isi' => 'Tiket yang sudah selesai atau ditolak, disimpan sebagai jejak penanganan dan dapat dibersihkan bila tidak diperlukan.'],
        ];
    }
}
