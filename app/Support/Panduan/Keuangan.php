<?php

namespace App\Support\Panduan;

/**
 * Panduan halaman keuangan yang dipakai bersama Admin dan Bendahara (prefix admin.keuangan.* / bendahara.*),
 * ditambah keputusan dispensasi milik Ketua PKBM.
 */
class Keuangan
{
    private const STATUS_PEMBAYARAN = [
        'Menunggu' => 'Pembayaran sudah masuk tetapi belum diperiksa; tagihan belum dianggap terbayar.',
        'Disetujui' => 'Pembayaran sah dan tagihan terkait diperbarui.',
        'Ditolak' => 'Pembayaran tidak sah; wali siswa melihat alasan penolakan.',
    ];

    public static function daftar(?string $role): array
    {
        // View validasi-akses dipakai Admin & Bendahara; validator mengikuti peran yang membukanya.
        $validator = $role === 'bendahara' ? 'Bendahara' : 'Admin';

        return [
            'bendahara.dashboard' => [
                'judul' => 'Overview Keuangan',
                'isi' => 'Ringkasan tagihan, dana masuk, dan pekerjaan yang menunggu keputusan Anda. Dahulukan isi "Pusat tindakan".',
                'langkah' => ['Periksa tab Validasi untuk pembayaran yang menunggu pemeriksaan.', 'Tab Transaksi menampilkan pembayaran yang sudah tervalidasi.', 'Tab Dispensasi berisi permintaan izin yang masih menunggu keputusan.', 'Gunakan "Akses cepat" untuk membuka tagihan, validasi, atau laporan.'],
            ],

            'admin.keuangan.tagihan.index,bendahara.tagihan.index' => [
                'judul' => 'Kelola Tagihan',
                'isi' => 'Kewajiban setiap siswa pada tahun ajaran terpilih: total tagihan, yang sudah dibayar, dan sisanya. Total mencakup seluruh kewajiban siswa pada periode itu. Pilih proses pembuatan tagihan sesuai kebutuhan.',
                'langkah' => ['Saring siswa menurut tahun ajaran, cabang, kelas, atau status tagihan.', 'Buat tagihan: Generate SPP untuk SPP bulanan, Massal untuk tagihan sama per kelas, Custom untuk siswa tertentu, atau Import dari Excel.', 'Buka detail siswa untuk melihat rincian, mengubah, atau mencetak tagihannya.', 'Tunggakan tahun sebelumnya dapat ditarik ke tahun aktif; gunakan "Lihat alumni menunggak" untuk siswa yang sudah lulus.'],
                'istilah' => ['Lunas' => 'Seluruh tagihan sudah dibayar dan disetujui.', 'Belum lunas' => 'Masih ada sisa tagihan.', 'Tanpa tagihan' => 'Siswa belum memiliki tagihan pada periode ini.'],
            ],
            'admin.keuangan.tagihan.show,bendahara.tagihan.show' => ['judul' => 'Detail Tagihan Siswa', 'isi' => 'Rincian setiap tagihan siswa beserta status pembayarannya. Dari sini Anda dapat mengubah atau mencetak tagihan.'],
            'admin.keuangan.tagihan.edit,bendahara.tagihan.edit' => ['judul' => 'Edit Tagihan Siswa', 'isi' => 'Ubah nominal dan jatuh tempo setiap tagihan siswa, lalu simpan. Tagihan yang sudah memiliki pembayaran disetujui dikunci dan tidak ikut berubah agar data tetap konsisten.'],
            'admin.keuangan.tagihan.generate-spp,bendahara.tagihan.generate-spp' => [
                'judul' => 'Generate SPP Bulanan',
                'isi' => 'Buat rangkaian tagihan SPP untuk setiap bulan pada periode belajar sekaligus.',
                'langkah' => ['Tentukan penerima berdasarkan kelas atau siswa.', 'Pilih rentang bulan (mengikuti urutan bulan resmi tahun ajaran), lalu isi nominal SPP.', 'Simpan; bulan yang sudah memiliki tagihan SPP dilewati sehingga tidak tercatat dua kali.'],
            ],
            'admin.keuangan.tagihan.bulk-create,bendahara.tagihan.bulk-create' => [
                'judul' => 'Tagihan Massal',
                'isi' => 'Buat tagihan yang sama untuk semua siswa aktif di beberapa kelas sekaligus, misalnya uang kegiatan.',
                'langkah' => ['Pilih kelas penerima.', 'Isi jenis dan nominal tagihan, lalu periksa jatuh tempo.', 'Simpan; hanya siswa aktif yang menerima tagihan, dan tagihan sejenis yang sudah ada tidak dibuat ulang.'],
            ],
            'admin.keuangan.tagihan.create-custom,bendahara.tagihan.create-custom' => [
                'judul' => 'Tagihan Custom',
                'isi' => 'Tambahkan satu tagihan khusus kepada siswa tertentu saja.',
                'langkah' => ['Isi detail tagihan satu kali.', 'Pilih siswa penerima; gunakan filter cabang dan kelas agar target mudah diperiksa.', 'Simpan.'],
            ],
            'admin.keuangan.tagihan.duplicate,bendahara.tagihan.duplicate' => [
                'judul' => 'Duplikasi Tagihan',
                'isi' => 'Salin susunan tagihan seorang siswa ke siswa lain, berguna untuk siswa pindahan atau susunan biaya yang sama.',
                'langkah' => ['Pilih satu siswa sumber.', 'Periksa susunan tagihan yang akan disalin.', 'Pilih siswa target (siswa sumber tidak dapat dipilih), lalu simpan.'],
            ],
            'admin.keuangan.tagihan.carryover,bendahara.tagihan.carryover' => [
                'judul' => 'Tarik Tunggakan',
                'isi' => 'Alihkan sisa tagihan tahun ajaran lama ke tahun ajaran aktif agar tetap tertagih. Carryover menambah tagihan baru tanpa menghapus riwayat lama; tagihan lama ditandai sudah dialihkan sehingga tidak terhitung dua kali.',
                'langkah' => ['Pastikan tagihan reguler pada tahun ajaran aktif sudah tersedia.', 'Pilih siswa dan periksa rinciannya.', 'Buka pratinjau sebelum memproses.'],
            ],
            'admin.keuangan.tagihan.import' => [
                'judul' => 'Import Tagihan',
                'isi' => 'Tambahkan banyak tagihan dari Excel. Status awal tagihan otomatis "belum bayar".',
                'langkah' => ['Pilih tahun ajaran tempat tagihan dicatat.', 'Isi identitas siswa (NIS, NISN, atau nama_siswa — salah satu wajib).', 'Lengkapi jenis_tagihan dan jumlah, lalu unggah file (maks. 5 MB) dan periksa hasilnya.'],
            ],

            'admin.keuangan.pembayaran.index,bendahara.pembayaran.index' => [
                'judul' => 'Kelola Pembayaran',
                'isi' => 'Semua transaksi yang masuk dari wali siswa maupun kasir. Pembayaran berstatus Menunggu belum mengurangi tagihan sampai disetujui.',
                'langkah' => ['Saring transaksi menurut status, metode, atau tanggal.', 'Buka detail transaksi yang menunggu, periksa bukti dan nominal, lalu Setujui atau Tolak.'],
                'istilah' => self::STATUS_PEMBAYARAN,
            ],
            'admin.keuangan.pembayaran.show,bendahara.pembayaran.show' => [
                'judul' => 'Detail Pembayaran',
                'isi' => 'Periksa transaksi sebelum memvalidasi. Persetujuan memperbarui status tagihan terkait; penolakan wajib disertai alasan yang jelas agar wali siswa dapat menindaklanjuti.',
                'istilah' => self::STATUS_PEMBAYARAN,
            ],
            'admin.keuangan.pembayaran.create,bendahara.pembayaran.create' => [
                'judul' => 'Input Pembayaran Tunai',
                'isi' => 'Catat pembayaran yang diterima langsung di loket sekolah. Halaman ini khusus pembayaran tunai.',
                'langkah' => ['Centang tagihan yang dibayar, lalu sesuaikan nominalnya. SPP harus dibayar penuh.', 'Periksa tanggal, status validasi, dan total.', 'Simpan pembayaran.'],
            ],
            'admin.keuangan.pembayaran.riwayat-siswa,bendahara.pembayaran.riwayat-siswa' => ['judul' => 'Riwayat Pembayaran Siswa', 'isi' => 'Jejak seluruh transaksi seorang siswa dan posisi tagihannya saat ini.'],

            'admin.keuangan.validasi-akses.index,bendahara.validasi-akses.index' => [
                'judul' => 'Validasi Akses Ujian & Rapor',
                'isi' => 'Siswa hanya dapat membuka ujian semester (PTS/PAS) dan rapor setelah aksesnya terbuka berdasarkan pembayaran: lunas, divalidasi, atau mendapat dispensasi. Akses rapor juga memerlukan persetujuan Ketua PKBM terlebih dahulu.',
                'langkah' => ['Cari siswa, lalu buka akses ujian atau rapor yang syaratnya sudah terpenuhi.', 'Gunakan "Validasi kilat per kelas" untuk satu kelas sekaligus sesuai filter.', 'Siswa yang belum lunas tetapi perlu akses dapat diajukan dispensasi kepada Ketua PKBM.'],
                'istilah' => ['Alur rapor' => 'Ketua menyetujui → '.$validator.' memvalidasi pembayaran atau dispensasi → akses terbuka.', 'Dispensasi' => 'Izin khusus dari Ketua PKBM bagi siswa yang belum lunas.'],
            ],

            'admin.keuangan.kenaikan-kelas.validation.index,bendahara.kenaikan-kelas.validation.index' => [
                'judul' => 'Validasi Dispensasi Kenaikan',
                'isi' => 'Kandidat naik kelas yang masih memiliki tunggakan. Ajukan izin khusus kepada Ketua PKBM agar siswa tetap dapat naik kelas.',
                'langkah' => ['Pilih siswa dan tuliskan alasan pengajuan.', 'Kirim satu per satu atau secara massal.', 'Pantau keputusan Ketua di "Riwayat keputusan".'],
            ],
            'admin.keuangan.kenaikan-kelas.validation.history,bendahara.kenaikan-kelas.validation.history,ketua.kenaikan-kelas.approval.history' => [
                'judul' => 'Riwayat Dispensasi',
                'isi' => 'Jejak keputusan pengajuan dispensasi kenaikan kelas: siapa yang diajukan, alasan, dan hasil keputusannya.',
            ],
            'ketua.kenaikan-kelas.approval.index' => [
                'judul' => 'Persetujuan Dispensasi Kenaikan',
                'isi' => 'Permintaan izin naik kelas bagi siswa yang masih memiliki tunggakan. Setujui hanya setelah alasan dan identitas siswa ditinjau.',
                'langkah' => ['Baca alasan dari petugas keuangan.', 'Setujui atau tolak permintaan.', 'Keputusan tercatat di riwayat dan dipakai saat proses kenaikan kelas.'],
            ],
            'ketua.dispensasi.index' => [
                'judul' => 'Dispensasi Keuangan',
                'isi' => 'Bendahara mengajukan akses khusus untuk siswa yang belum lunas. Persetujuan membuka akses ujian atau rapor sesuai jenis pengajuan; penolakan mempertahankan kewajiban pelunasan.',
                'langkah' => ['Saring pengajuan menurut status dan jenis akses.', 'Tinjau alasan pengajuan, lalu setujui atau tolak.'],
            ],

            'admin.keuangan.laporan.index,bendahara.laporan.index' => [
                'judul' => 'Laporan Pembayaran Bulanan',
                'isi' => 'Rekap pembayaran yang sudah disetujui pada bulan terpilih, lengkap dengan grafik arus kas harian dan rincian transaksi.',
                'langkah' => ['Pilih bulan, tahun, metode pembayaran, dan cabang.', 'Terapkan filter, lalu cetak laporan bila diperlukan.'],
            ],
            'admin.keuangan.laporan.belum-lunas,bendahara.laporan.belum-lunas' => [
                'judul' => 'Laporan Siswa Belum Lunas',
                'isi' => 'Siswa yang masih memiliki sisa tagihan. Dari daftar ini Anda dapat langsung membuka detail tagihan, riwayat, atau mencatat pembayaran.',
            ],
            'admin.keuangan.laporan.rekap-tagihan,bendahara.laporan.rekap-tagihan' => [
                'judul' => 'Rekap Tagihan per Kelas',
                'isi' => 'Total tagihan, dana masuk, sisa, dan persentase lunas setiap kelas pada tahun ajaran aktif, beserta ringkasan per jenjang.',
            ],
        ];
    }
}
