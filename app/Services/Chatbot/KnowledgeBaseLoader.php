<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class KnowledgeBaseLoader
{
    protected const ROLE_FILE_MAP = [
        'admin' => 'admin.md',
        'guru_pengajar' => 'guru.md',
        'siswa' => 'siswa.md',
        'orang_tua' => 'orang-tua.md',
        'wali_kelas' => 'wali-kelas.md',
        'bendahara' => 'bendahara.md',
        'sekretaris' => 'sekretaris.md',
        'wakil_kepala_sekolah' => 'wakil-kepala-sekolah.md',
        'ketua_pkbm' => 'ketua-pkbm.md',
    ];

    protected const ROLE_ROUTE_PREFIX = [
        'admin' => 'admin.',
        'guru_pengajar' => 'guru.',
        'siswa' => 'siswa.',
        'orang_tua' => 'wali-siswa.',
        'wali_kelas' => ['wali.', 'wali-kelas.'],
        'bendahara' => 'bendahara.',
        'sekretaris' => 'sekretaris.',
        'wakil_kepala_sekolah' => ['waka.', 'wakil.'],
        'ketua_pkbm' => ['ketua.', 'ketua-pkbm.'],
    ];

    /**
     * Compact menu snapshot from the actual sidebar/view structure.
     * This gives the chatbot reliable first-pass context before reading
     * the longer docs/flow knowledge base.
     */
    protected const ROLE_MENU_SNAPSHOT = [
        'admin' => [
            'Dashboard => admin.dashboard',
            'Manajemen Konten: Landing Page => admin.landing-pages.index',
            'Manajemen Pengguna: Manajemen User (Tenaga Pendidik, Siswa, Wali Murid) => admin.users.*',
            'Manajemen Pengguna: Tiket Pemulihan Akun => admin.recovery-tickets.index',
            'Pengaturan: LMS => admin.lms-settings.index',
            'Pengaturan: AI Assistant, provider, model, akses chatbot, context restriction => admin.ai-settings.index',
            'Data Master: Tahun Ajaran, Cabang, Kelas, Wali Kelas, Guru Pengajar, Mata Pelajaran, Jadwal Pelajaran, Manajemen Siswa',
            'Keuangan: Tagihan, Tarik Tunggakan, Pembayaran, Config Pembayaran, Laporan Keuangan, Validasi Ujian & Rapor',
            'Kenaikan Kelas: Validasi Dispensasi, Pengaturan KKM, Pengaturan Kenaikan, Proses & Rekap',
            'Akademik: Kalender Akademik, Pengumuman, Flyer/Iklan, Kelola Berita',
            'Monitoring: Pengguna, Wali Kelas, Guru Pengajar, Siswa, Monitoring LMS',
            'Laporan dan Catatan internal',
            'Google Sheets Sync ada di route admin.google-sheets.* tetapi hanya aktif jika config google-sheets.enabled true',
        ],
        'guru_pengajar' => [
            'Dashboard => guru.dashboard: ringkasan kelas & mapel yang diampu, aktivitas LMS terbaru',
            'Jadwal Mengajar => guru.jadwal.index: jadwal mengajar per hari/jam sesuai penugasan',
            'Semua Kelas => guru.kelas.index: daftar kelas+mapel yang diampu; pintu masuk ke LMS per kelas-mapel',
            'Arsip LMS => guru.lms.arsip.*: telusuri materi/tugas/latihan/ujian yang pernah dibuat LINTAS tahun ajaran; Preview; Salin satu item atau SALIN MASSAL (pilih banyak + "Pilih semua" per tipe) ke kelas+mapel yang diampu di TA aktif. Berguna saat awal TA baru agar tak upload ulang. Salinan ujian nonaktif (aktifkan manual).',
            'Catatan Monitoring => guru.lms.catatan-monitoring.index: teguran/catatan dari pimpinan (Admin/Wakasek/Ketua) atas konten LMS; dibaca & ditindaklanjuti guru',
            'LMS per kelas-mapel (pilih kelas & mapel dulu dari Semua Kelas / Akses Cepat):',
            '  • Materi: upload materi — pilih TIPE file (PDF/PPT/DOC/Video) atau Link; format file WAJIB sesuai tipe (mis. PDF hanya .pdf); edit/hapus; siswa dapat notifikasi materi baru',
            '  • Tugas: buat/edit tugas (judul, deskripsi, deadline, lampiran dokumen), lihat pengumpulan siswa, koreksi & beri nilai; hasil koreksi masuk ke Nilai Siswa',
            '  • Latihan: mirip ujian tapi bersifat latihan (tipe latihan), tanpa gerbang akses pembayaran',
            '  • Ujian: buat ujian, Kelola Soal (input manual + AI Question Generator/generate soal otomatis, guru mereview sebelum simpan), aktif/nonaktif ujian, Hasil Ujian & Pengawasan (monitoring kehilangan fokus/pelanggaran saat ujian). Ujian semester (PTS/PAS/UTS/UAS) butuh siswa lunas + disetujui wali kelas',
            '  • Forum Diskusi: buat topik diskusi & balas; siswa mapel terkait ikut berdiskusi',
            '  • Kelas Virtual: jadwalkan pertemuan daring (Zoom/Google Meet); siswa kelas itu dapat pengingat',
            '  • Nilai Siswa: input & REVISI MANUAL nilai per komponen (Tugas 1-5, Latihan 1-5, Ulangan Harian 1-5, PTS, PAS); rata-rata & nilai akhir dihitung otomatis; nilai bisa terisi otomatis dari pengerjaan tugas/ujian siswa TAPI tetap bisa diedit manual untuk revisi; DESIMAL pakai TITIK (mis. 9.8, koma otomatis jadi titik); Import/Export Excel; Hitung Ulang',
            'Route LMS guru butuh parameter kelas & mapel — jika tombol langsung tak tersedia, arahkan user memilih kelas/mapel dari Semua Kelas atau Akses Cepat lebih dulu',
        ],
        'siswa' => [
            'SIA: Dashboard SIA => siswa.sia.dashboard: ringkasan akademik pribadi',
            'SIA: Presensi => siswa.sia.presensi.index: riwayat kehadiran pribadi (hadir/sakit/izin/alpha)',
            'SIA: Data Penilaian => siswa.sia.penilaian: melihat NILAI PRIBADI per mapel (hanya milik sendiri)',
            'SIA: HOK-LMS hanya muncul jika jenjang siswa termasuk pengaturan LMS yang diaktifkan Admin',
            'LMS: Beranda => siswa.lms.dashboard',
            'LMS: Mata Pelajaran — dinamis dari jadwal kelas siswa (hanya mapel yang diajarkan di kelasnya + sesuai agama). Tiap mapel berisi: Materi (baca/unduh), Tugas (kerjakan & kumpulkan sebelum deadline, boleh lampiran), Ujian/Latihan (kerjakan online; jawaban auto-save; ada pengawasan fokus saat ujian; hasil/pembahasan tampil bila sudah selesai dan guru mengizinkan), Forum Diskusi (ikut berdiskusi), Kelas Virtual (link pertemuan daring)',
            'LMS: Kalender Akademik, Jadwal Pelajaran, Daftar Guru',
            'Akses UJIAN semester (PTS/PAS/UTS/UAS) terkunci sampai pembayaran lunas (Bendahara) DAN disetujui Wali Kelas',
            'RAPOR & PEMBAYARAN TIDAK ada di menu siswa — itu diakses melalui akun Wali Siswa (orang tua); jika siswa menanyakannya, arahkan untuk lewat Wali Siswa/orang tua',
        ],
        'orang_tua' => [
            'Dashboard => wali-siswa.dashboard: ringkasan tiap anak yang terhubung ke akun wali siswa + pengajuan izin',
            'Monitoring Anak: submenu per anak (bisa lebih dari satu anak per akun)',
            'Per anak — Presensi: riwayat kehadiran + AJUKAN/EDIT IZIN (sakit/izin) yang akan divalidasi Wali Kelas',
            'Per anak — Tagihan: daftar tagihan & tunggakan, invoice, BAYAR online (Midtrans) atau Direct Transfer (upload bukti), status validasi pembayaran oleh Bendahara',
            'Per anak — Rapor: lihat & unduh rapor anak, HANYA bila akses rapor sudah divalidasi (lunas + persetujuan); jika belum, ajukan permintaan unduh ke Wali Kelas',
            'Wali siswa menerima notifikasi: tagihan baru, pembayaran divalidasi/ditolak, rapor terbit, status izin, anak absen alpha, hasil kenaikan kelas',
            'Route wali siswa memakai parameter anak; arahkan memilih anak dulu bila tombol langsung tak tersedia',
        ],
        'wali_kelas' => [
            'Dashboard => wali.dashboard: ringkasan kelas yang diwalikan (di TA aktif)',
            'Pilih Kelas => wali.pilih-kelas: muncul bila wali memegang >1 kelas; pilih dulu kelas yang dikelola',
            'Presensi Siswa: Input Harian (catat hadir/sakit/izin/alpha; ortu dapat notif bila anak ALPHA), Validasi Izin (setujui/tolak pengajuan izin dari wali siswa), Rekap Harian, Riwayat & Edit',
            'Nilai Siswa => wali.nilai.index: rekap nilai kelas untuk rapor; bisa sinkron dari nilai guru mapel; edit bila perlu',
            'Kelola Rapor => wali.rapor.index: generate rapor per siswa/semester, isi Deskripsi Capaian per mapel, kirim ke Ketua PKBM untuk validasi. Tombol "Kelola Template" & "Terapkan Template": pustaka template deskripsi capaian PRIBADI per wali (per mapel) yang otomatis terbawa antar TA; bisa diterapkan massal ke satu kelas',
            'Kelola Template Capaian => wali.template-capaian.index: kelola kalimat deskripsi capaian rapor (per mata pelajaran, milik sendiri)',
            'Arsip Kelas Saya => wali.arsip.index: lihat data kelas yang PERNAH diwalikan lintas TA (siswa/rapor/presensi/nilai), read-only',
            'Permintaan Unduh Rapor => wali.rapor.request-download.index: setujui/tolak permintaan unduh rapor dari wali siswa',
            'Kenaikan Kelas: Prediksi Kenaikan => wali.kenaikan-kelas.prediction: prediksi status naik/tidak per siswa (nilai + keuangan)',
            'Validasi Akses => wali.validasi-akses.index: setujui akses ujian/rapor siswa kelasnya (dipadukan status lunas dari Bendahara)',
        ],
        'bendahara' => [
            'Dashboard => bendahara.dashboard: ringkasan keuangan (tagihan, pembayaran masuk menunggu validasi, tunggakan)',
            'Kelola Tagihan => bendahara.tagihan.index: daftar tagihan semua siswa (termasuk ALUMNI/lulus). Buat tagihan: Tagihan Massal, Generate SPP, Tagihan Custom, Duplikasi. Tombol "Alumni Menunggak": tampilkan alumni yang masih berhutang lintas TA (untuk penebusan ijazah). Cari siswa lewat NAMA. Sisa dihitung dari pembayaran disetujui (cicilan akurat)',
            'Tarik Tunggakan (carryover) => bendahara.tagihan.carryover: pindahkan tunggakan TA lama jadi tagihan di TA aktif (termasuk sisa cicilan)',
            'Kelola Pembayaran => bendahara.pembayaran.index: validasi/tolak pembayaran masuk (manual/transfer/Midtrans); ortu dapat notifikasi hasil; Riwayat per siswa',
            'Config/Info Pembayaran => bendahara.info-pembayaran.index: rekening, Midtrans, info pembayaran',
            'Validasi Akses => bendahara.validasi-akses.index: buka akses Ujian & Rapor siswa berdasarkan status LUNAS; bisa ajukan dispensasi ke Ketua PKBM',
            'Kenaikan Kelas: Validasi Dispensasi => bendahara.kenaikan-kelas.validation.index: ajukan dispensasi naik kelas untuk siswa menunggak ke Ketua PKBM (approval balik ke sini)',
            'Laporan: Laporan Pembayaran, Rekap Tagihan, Siswa Belum Lunas',
        ],
        'sekretaris' => [
            'Dashboard => sekretaris.dashboard',
            'Manajemen Konten: Kalender Akademik, Pengumuman, Flyer/Iklan, Kelola Berita',
            'Sekretaris fokus ke konten akademik/publikasi; tidak mengelola keuangan, kelas, nilai, rapor, atau LMS',
        ],
        'wakil_kepala_sekolah' => [
            'Dashboard => waka.dashboard',
            'Manajemen Akademik: Tahun Ajaran',
            'Data Akademik: Data Kelas, Data Wali Kelas, Data Guru Pengajar, Mata Pelajaran, Jadwal Pelajaran, Manajemen Siswa',
            'Kenaikan Kelas: Pengaturan KKM, Pengaturan Kenaikan, Proses & Rekap',
            'Monitoring & Analitik: Monitoring Wali Kelas, Guru Pengajar, Siswa, Monitoring LMS',
            'Komunikasi: Catatan untuk user/role terkait => waka.catatan.index',
            'Scope Wakasek biasanya cabang-scoped sesuai cabang user',
        ],
        'ketua_pkbm' => [
            'Dashboard => ketua.dashboard: ringkasan yang menunggu keputusan (validasi rapor, dispensasi)',
            'Validasi Rapor => ketua.validasi-rapor.index: setujui/tolak/minta revisi rapor yang dikirim Wali Kelas (validasi akhir sebelum rapor bisa diakses); keputusan memberi notifikasi balik ke Wali Kelas & Bendahara',
            'Kenaikan Kelas: Approval Dispensasi => ketua.kenaikan-kelas.approval.index: setujui/tolak dispensasi naik kelas siswa menunggak (diajukan Bendahara/Admin); keputusan kembali ke pengaju',
            'Dispensasi (akses ujian/rapor) => ketua.dispensasi.index: putuskan dispensasi akses yang diajukan Bendahara/Admin',
            'Monitoring: Data Pengguna, Wali Kelas, Guru Pengajar, Siswa, Monitoring LMS (read-only)',
            'Laporan & Catatan: Cetak Laporan, Kirim Catatan ke role/user terkait',
            'Ketua fokus APPROVAL/validasi akhir, monitoring, laporan, catatan; BUKAN input operasional harian (nilai/tagihan/jadwal). Untuk hal operasional, arahkan koordinasi ke role pemiliknya',
        ],
    ];

    /**
     * Per-feature ownership map.
     * Key = feature topic (case-insensitive substring match against user query).
     * Value = ['owner' => role(s) yang ngerjakan, 'admin_view' => route admin untuk lihat/monitor (read-only), 'description' => penjelasan singkat]
     */
    protected const FEATURE_OWNERSHIP = [
        'input nilai|kelola nilai|isi nilai|rekap nilai mapel' => [
            'owner' => ['wali_kelas', 'guru_pengajar'],
            'admin_view_route' => 'admin.guru-pengajar.index',
            'description' => 'Input dan kelola nilai siswa dilakukan oleh Guru Pengajar (per mata pelajaran) dan Wali Kelas (rekap rapor)',
        ],
        'cek nilai|lihat nilai|data penilaian|nilai saya|nilai anak' => [
            'owner' => ['siswa', 'orang_tua', 'wali_kelas', 'guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.siswa',
            'description' => 'Siswa melihat Data Penilaian di SIA; Wali Siswa melihat hasil akhir melalui Rapor anak; Wali/Guru mengelola nilai',
        ],
        'input presensi|catat absen|rekap presensi|validasi izin' => [
            'owner' => ['wali_kelas'],
            'admin_view_route' => 'admin.wali-kelas.index',
            'description' => 'Input presensi harian siswa dilakukan oleh Wali Kelas masing-masing kelas',
        ],
        'cek presensi|lihat presensi|presensi anak|riwayat kehadiran' => [
            'owner' => ['siswa', 'orang_tua', 'wali_kelas'],
            'admin_view_route' => 'admin.monitoring.siswa',
            'description' => 'Siswa dan Wali Siswa melihat riwayat presensi sesuai akunnya; Wali Kelas menginput dan merekap presensi',
        ],
        'ajukan izin|izin sakit|izin siswa' => [
            'owner' => ['orang_tua'],
            'admin_view_route' => 'admin.monitoring.pengguna',
            'description' => 'Pengajuan izin siswa dilakukan oleh Wali Siswa melalui Dashboard Wali Siswa, kemudian divalidasi Wali Kelas',
        ],
        'cetak rapor|generate rapor|isi rapor|kelola rapor' => [
            'owner' => ['wali_kelas'],
            'admin_view_route' => 'admin.wali-kelas.index',
            'description' => 'Generate, input catatan, dan kirim validasi rapor dilakukan oleh Wali Kelas; validasi akhir oleh Ketua PKBM',
        ],
        'lihat rapor|rapor anak|download rapor|unduh rapor|permintaan unduh' => [
            'owner' => ['orang_tua', 'wali_kelas', 'ketua_pkbm'],
            'admin_view_route' => 'admin.monitoring.pengguna',
            'description' => 'Wali Siswa melihat rapor anak setelah akses tervalidasi; Wali Kelas mengelola permintaan unduh; Ketua PKBM melakukan validasi akhir rapor',
        ],
        'validasi rapor|setujui rapor|approve rapor' => [
            'owner' => ['ketua_pkbm'],
            'admin_view_route' => 'admin.monitoring.pengguna',
            'description' => 'Validasi dan tanda tangan rapor dilakukan oleh Ketua PKBM',
        ],
        'validasi akses|akses ujian|akses rapor|batas pembayaran' => [
            'owner' => ['admin', 'bendahara', 'wali_kelas'],
            'admin_view_route' => 'admin.keuangan.validasi-akses.index',
            'description' => 'Validasi akses ujian/rapor dikelola Admin/Bendahara/Wali Kelas sesuai scope dan status pembayaran siswa',
        ],
        'validasi pembayaran|konfirmasi pembayaran|setujui pembayaran|tolak pembayaran' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.pembayaran.index',
            'description' => 'Validasi pembayaran masuk dilakukan oleh Bendahara (atau Admin)',
        ],
        'buat tagihan|generate spp|tagihan custom' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.tagihan.index',
            'description' => 'Pembuatan tagihan, generate SPP, bulk create dilakukan oleh Bendahara atau Admin',
        ],
        'tarik tunggakan|carryover|tunggakan ta lama' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.tagihan.carryover',
            'description' => 'Tarik tunggakan dari tahun ajaran lama ke tahun ajaran aktif dilakukan oleh Bendahara atau Admin',
        ],
        'config pembayaran|info pembayaran|rekening|midtrans|direct transfer' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.info-pembayaran.index',
            'description' => 'Konfigurasi rekening, Midtrans, dan info pembayaran dikelola Bendahara atau Admin',
        ],
        'laporan keuangan|laporan pembayaran|rekap tagihan|belum lunas|siswa belum lunas' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.laporan.index',
            'description' => 'Laporan pembayaran, rekap tagihan, dan daftar belum lunas dikelola Bendahara atau Admin',
        ],
        'buat pengumuman|kelola pengumuman' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.pengumuman.index',
            'description' => 'Membuat dan publikasi pengumuman dilakukan oleh Sekretaris (Admin juga bisa)',
        ],
        'buat berita|kelola berita|tulis artikel' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.berita.index',
            'description' => 'Pengelolaan berita/artikel website dilakukan oleh Sekretaris (Admin juga bisa)',
        ],
        'kalender akademik|jadwal libur|event sekolah' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.kalender.index',
            'description' => 'Kalender akademik (event, libur) dikelola oleh Sekretaris (Admin juga bisa)',
        ],
        'flyer|poster|brosur' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.flyer.index',
            'description' => 'Flyer dan poster digital dikelola oleh Sekretaris (Admin juga bisa)',
        ],
        'landing page|halaman publik|konten website|ppdb landing|profil sekolah' => [
            'owner' => ['admin', 'sekretaris'],
            'admin_view_route' => 'admin.landing-pages.index',
            'description' => 'Landing page dan konten publik utama dikelola Admin; konten akademik seperti berita/pengumuman/flyer dapat dikelola Sekretaris/Admin',
        ],
        'buat tugas|koreksi tugas|kelola tugas|nilai tugas' => [
            'owner' => ['guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.guru-pengajar',
            'description' => 'Pembuatan, distribusi, dan koreksi tugas dilakukan oleh Guru Pengajar di LMS',
        ],
        'buat ujian|buat soal|generate soal|koreksi ujian' => [
            'owner' => ['guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.guru-pengajar',
            'description' => 'Pembuatan ujian, soal, dan koreksi dilakukan oleh Guru Pengajar di LMS',
        ],
        'upload materi|buat materi|kelola materi' => [
            'owner' => ['guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.guru-pengajar',
            'description' => 'Upload materi pelajaran dilakukan oleh Guru Pengajar di LMS',
        ],
        'forum diskusi|buat forum|balas forum|topik forum' => [
            'owner' => ['guru_pengajar', 'siswa'],
            'admin_view_route' => 'admin.monitoring.lms.index',
            'description' => 'Forum diskusi dibuat/dimoderasi Guru Pengajar dan dapat diikuti Siswa pada mata pelajaran terkait',
        ],
        'kelas virtual|meeting|pertemuan online|zoom|google meet' => [
            'owner' => ['guru_pengajar', 'siswa'],
            'admin_view_route' => 'admin.monitoring.lms.index',
            'description' => 'Guru Pengajar membuat jadwal kelas virtual di LMS; Siswa mengaksesnya dari halaman mata pelajaran',
        ],
        'arsip lms|salin arsip|arsip kelas|arsip kelas saya' => [
            'owner' => ['guru_pengajar', 'wali_kelas'],
            'admin_view_route' => 'admin.monitoring.lms.index',
            'description' => 'Guru Pengajar memakai Arsip LMS untuk melihat/menyalin konten lintas tahun ajaran; Wali Kelas melihat Arsip Kelas Saya secara read-only',
        ],
        'catatan monitoring|teguran lms|catatan lms' => [
            'owner' => ['admin', 'wakil_kepala_sekolah', 'ketua_pkbm', 'guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.lms.index',
            'description' => 'Catatan monitoring LMS dibuat dari menu Monitoring LMS oleh pimpinan/Admin dan dibaca/ditindaklanjuti Guru Pengajar',
        ],
        'kkm|kriteria ketuntasan' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.akademik.kenaikan-kelas.kkm.index',
            'description' => 'Pengaturan KKM (Kriteria Ketuntasan Minimal) per mapel dikelola oleh Wakasek atau Admin',
        ],
        'kenaikan kelas|promosi siswa|naik kelas' => [
            'owner' => ['wakil_kepala_sekolah', 'admin', 'ketua_pkbm'],
            'admin_view_route' => 'admin.akademik.kenaikan-kelas.report',
            'description' => 'Proses kenaikan kelas: Wakasek/Admin eksekusi, Ketua PKBM persetujuan akhir',
        ],
        'dispensasi|keringanan biaya' => [
            'owner' => ['ketua_pkbm', 'bendahara'],
            'admin_view_route' => 'admin.keuangan.kenaikan-kelas.validation.index',
            'description' => 'Validasi dispensasi pembayaran dikelola oleh Ketua PKBM atau Bendahara',
        ],
        'approval dispensasi|validasi dispensasi|dispensasi naik kelas' => [
            'owner' => ['ketua_pkbm', 'bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.kenaikan-kelas.validation.index',
            'description' => 'Dispensasi kenaikan/keuangan divalidasi oleh Ketua PKBM, Bendahara, atau Admin sesuai alur',
        ],
        'atur jadwal|buat jadwal|kelola jadwal|jadwal kelas' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.jadwal-pelajaran.index',
            'description' => 'Pengaturan jadwal pelajaran dilakukan oleh Wakasek atau Admin',
        ],
        'lihat jadwal|jadwal pelajaran saya|jadwal mengajar|jadwal hari ini' => [
            'owner' => ['siswa', 'guru_pengajar', 'wali_kelas', 'wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.jadwal-pelajaran.index',
            'description' => 'Siswa melihat Jadwal Pelajaran di LMS/SIA, Guru melihat Jadwal Mengajar, Wali melihat jadwal kelas, Admin/Wakasek mengelola jadwal',
        ],
        'tahun ajaran|ta aktif|semester aktif' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.tahun-ajaran.index',
            'description' => 'Tahun ajaran dan status aktifnya dikelola Admin atau Wakasek',
        ],
        'data kelas|kelola kelas|manajemen kelas' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.kelas.index',
            'description' => 'Data kelas, kuota, wali kelas, dan siswa per kelas dikelola Admin atau Wakasek',
        ],
        'wali kelas|assign wali|set wali kelas|data wali kelas' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.wali-kelas.index',
            'description' => 'Penetapan/monitoring wali kelas dikelola Admin atau Wakasek',
        ],
        'guru pengajar|assign guru|data guru pengajar|guru mapel' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.guru-pengajar.index',
            'description' => 'Data guru pengajar dan relasi guru-kelas-mapel dikelola Admin atau Wakasek',
        ],
        'kelola mata pelajaran|data mata pelajaran|kode mapel' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.mata-pelajaran.index',
            'description' => 'Mata pelajaran, kode mapel, jenjang, dan import mapel dikelola Admin atau Wakasek',
        ],
        'mata pelajaran saya|daftar mapel|mapel saya|pelajaran saya' => [
            'owner' => ['siswa', 'guru_pengajar', 'wali_kelas', 'wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.mata-pelajaran.index',
            'description' => 'Siswa dan Guru melihat mata pelajaran sesuai jadwal/kelasnya; Admin/Wakasek mengelola data master mata pelajaran',
        ],
        'manajemen siswa|data siswa|assign siswa|kartu siswa|wali siswa siswa' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.manajemen-siswa.index',
            'description' => 'Data siswa, assign kelas, kartu siswa, dan relasi wali siswa dikelola Admin atau Wakasek',
        ],
        'cabang|manajemen cabang|data cabang' => [
            'owner' => ['admin'],
            'admin_view_route' => 'admin.cabang.index',
            'description' => 'Cabang sekolah dikelola Admin',
        ],
        'pengaturan istirahat|jam istirahat' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.pengaturan-istirahat.index',
            'description' => 'Slot/jam istirahat untuk jadwal pelajaran dikelola Admin atau Wakasek',
        ],
        'monitoring lms|preview materi|preview tugas|preview ujian|monitoring siswa|monitoring guru|monitoring wali' => [
            'owner' => ['admin', 'wakil_kepala_sekolah', 'ketua_pkbm'],
            'admin_view_route' => 'admin.monitoring.lms.index',
            'description' => 'Monitoring pengguna, wali, guru, siswa, dan LMS dilakukan Admin, Wakasek, atau Ketua PKBM sesuai scope',
        ],
        'cetak laporan|laporan akademik|laporan lengkap|laporan data' => [
            'owner' => ['admin', 'ketua_pkbm'],
            'admin_view_route' => 'admin.laporan.index',
            'description' => 'Cetak laporan data akademik tersedia untuk Admin dan Ketua PKBM',
        ],
        'catatan internal|kirim catatan|catatan user|teguran umum' => [
            'owner' => ['admin', 'wakil_kepala_sekolah', 'ketua_pkbm'],
            'admin_view_route' => 'admin.catatan.index',
            'description' => 'Catatan internal dikirim oleh Admin, Wakasek, atau Ketua PKBM kepada role/user terkait',
        ],
        'kelola user|manajemen user|buat akun|akun siswa|akun guru|akun wali siswa|wali murid|tenaga pendidik' => [
            'owner' => ['admin'],
            'admin_view_route' => 'admin.users.index',
            'description' => 'Pembuatan dan pengelolaan akun pengguna dilakukan Admin',
        ],
        'tiket pemulihan|recovery ticket|pemulihan akun|reset akun|admin wa' => [
            'owner' => ['admin'],
            'admin_view_route' => 'admin.recovery-tickets.index',
            'description' => 'Tiket pemulihan akun dan resend/reject/resolve dikelola Admin',
        ],
        'pengaturan ai|ai settings|akses chatbot|context restriction|llm mode|api key ai|groq|gemini' => [
            'owner' => ['admin'],
            'admin_view_route' => 'admin.ai-settings.index',
            'description' => 'Provider AI, API key, model, AI Question Generator, akses role chatbot, dan context restriction dikelola Admin',
        ],
        'pengaturan lms|aktifkan lms|jenjang lms|akses lms' => [
            'owner' => ['admin'],
            'admin_view_route' => 'admin.lms-settings.index',
            'description' => 'Pengaturan akses LMS per jenjang dikelola Admin',
        ],
        'google sheets|sync sheets|pull sheets|push sheets' => [
            'owner' => ['admin'],
            'admin_view_route' => 'admin.google-sheets.index',
            'description' => 'Google Sheets Sync adalah fitur Admin dan hanya tersedia jika konfigurasi google-sheets.enabled aktif',
        ],
        'bayar spp|bayar tagihan|transfer pembayaran' => [
            'owner' => ['orang_tua'],
            'admin_view_route' => 'admin.keuangan.pembayaran.index',
            'description' => 'Pembayaran SPP dilakukan oleh Wali Siswa via Dashboard Wali Siswa (online Midtrans atau Direct Transfer) atau langsung ke Bendahara',
        ],
    ];

    public function getOwnershipMap(): array
    {
        return self::FEATURE_OWNERSHIP;
    }

    /**
     * Render compact ownership table for system prompt.
     * Only includes entries where current role is NOT the primary owner (i.e., relevant for cross-role recommendation).
     */
    public function getOwnershipPromptForRole(string $role): string
    {
        $lines = [];
        foreach (self::FEATURE_OWNERSHIP as $keywords => $entry) {
            $owners = $entry['owner'];
            if (in_array($role, $owners, true)) continue;
            $kwLabel = explode('|', $keywords)[0];
            $ownerLabel = implode(' / ', array_map(fn($r) => $this->roleLabel($r), $owners));
            $lines[] = "- Topik '{$kwLabel}' → DIKELOLA OLEH: {$ownerLabel}. {$entry['description']}";
        }
        return implode("\n", $lines);
    }

    public function getMenuSnapshotForRole(string $role): string
    {
        $lines = self::ROLE_MENU_SNAPSHOT[$role] ?? [];
        if (empty($lines)) {
            return '- Tidak ada snapshot menu khusus; gunakan knowledge base dan route map yang tersedia.';
        }

        return implode("\n", array_map(fn($line) => "- {$line}", $lines));
    }

    public function roleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'guru_pengajar' => 'Guru Pengajar',
            'siswa' => 'Siswa',
            'orang_tua' => 'Wali Siswa',
            'wali_kelas' => 'Wali Kelas',
            'bendahara' => 'Bendahara',
            'sekretaris' => 'Sekretaris',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah (Wakasek)',
            'ketua_pkbm' => 'Ketua PKBM',
            default => ucfirst(str_replace('_', ' ', $role)),
        };
    }

    public function getForRole(string $role): string
    {
        $file = self::ROLE_FILE_MAP[$role] ?? 'landing-pages.md';
        $path = base_path("docs/flow/{$file}");
        $mtime = file_exists($path) ? filemtime($path) : 0;
        $cacheKey = "chatbot_kb_{$role}_{$mtime}";

        return Cache::remember($cacheKey, 3600, function () use ($path) {
            if (!file_exists($path)) {
                return '';
            }

            $content = file_get_contents($path);
            return $this->trimToEssentials($content);
        });
    }

    public function getLandingKnowledge(): string
    {
        $path = base_path('docs/flow/landing-pages.md');
        $mtime = file_exists($path) ? filemtime($path) : 0;

        return Cache::remember("chatbot_kb_landing_{$mtime}", 3600, function () use ($path) {
            if (!file_exists($path)) {
                return '';
            }
            return $this->trimToEssentials(file_get_contents($path));
        });
    }

    /**
     * Compact list of public landing-page URLs (no auth needed) — relevant for ALL roles.
     * Returned format suitable for direct injection to system prompt.
     */
    public function getLandingPagesPrompt(): string
    {
        return Cache::remember('chatbot_landing_pages_compact', 3600, function () {
            $pages = [
                '/' => 'Beranda / Home — landing utama sekolah',
                '/tentang-sekolah' => 'Tentang Sekolah — profil singkat PKBM',
                '/visi-misi' => 'Visi & Misi sekolah',
                '/struktur-organisasi' => 'Struktur Organisasi sekolah (pengurus, jabatan)',
                '/profil-guru' => 'Profil Guru — daftar pengajar (foto, NIP, mapel)',
                '/program-paud-tk' => 'Program PAUD / TK',
                '/program-sd-sma' => 'Program Pendidikan Kesetaraan (Paket A/B/C ≈ SD/SMP/SMA)',
                '/program-inklusi' => 'Program Pendidikan Inklusi (anak berkebutuhan khusus)',
                '/program-terapi' => 'Program Terapi / Konseling',
                '/fasilitas' => 'Fasilitas sekolah (ruang kelas, lab, dll)',
                '/ppdb' => 'PPDB — Penerimaan Peserta Didik Baru (alur pendaftaran, syarat, biaya)',
                '/galeri' => 'Galeri foto kegiatan sekolah',
                '/kontak' => 'Kontak sekolah (alamat, telepon, email, peta)',
                '/berita' => 'Berita & Artikel sekolah',
            ];
            $lines = [];
            foreach ($pages as $path => $desc) {
                $lines[] = "{$path} — {$desc}";
            }
            return implode("\n", $lines);
        });
    }

    /**
     * Whitelist of public landing page URLs (used by parseStructuredResponse to allow direct URL buttons).
     */
    public function getLandingPageUrls(): array
    {
        return [
            '/', '/tentang-sekolah', '/visi-misi', '/struktur-organisasi', '/profil-guru',
            '/program-paud-tk', '/program-sd-sma', '/program-inklusi', '/program-terapi',
            '/fasilitas', '/ppdb', '/galeri', '/kontak', '/berita',
        ];
    }

    public function isLandingPageUrl(string $path): bool
    {
        return in_array(rtrim($path, '/') ?: '/', $this->getLandingPageUrls(), true);
    }

    public function getRouteMapForRole(string $role): array
    {
        $cacheKey = "chatbot_routes_{$role}";

        return Cache::remember($cacheKey, 3600, function () use ($role) {
            $prefixes = (array) (self::ROLE_ROUTE_PREFIX[$role] ?? []);
            $sharedPrefixes = ['', 'login', 'logout', 'profile', 'account', 'notifications', 'home'];

            $map = [];
            foreach (Route::getRoutes()->getRoutesByName() as $name => $route) {
                if (!$name) continue;
                if ($this->isExcludedRoute($name)) continue;

                $matchesRolePrefix = false;
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($name, $prefix)) {
                        $matchesRolePrefix = true;
                        break;
                    }
                }

                $matchesSharedPrefix = false;
                foreach ($sharedPrefixes as $shared) {
                    if ($shared === '' && !str_contains($name, '.')) {
                        $matchesSharedPrefix = true;
                        break;
                    }
                    if ($shared !== '' && (str_starts_with($name, $shared . '.') || $name === $shared)) {
                        $matchesSharedPrefix = true;
                        break;
                    }
                }

                if (!$matchesRolePrefix && !$matchesSharedPrefix) continue;

                if ($route->methods()[0] !== 'GET') continue;
                if (preg_match('/\{[^}]+\}/', $route->uri())) continue;

                $map[$name] = '/' . ltrim($route->uri(), '/');
            }

            return $map;
        });
    }

    public function isRouteAllowedForRole(string $routeName, string $role): bool
    {
        $map = $this->getRouteMapForRole($role);
        return array_key_exists($routeName, $map);
    }

    public function resolveRouteUrl(string $routeName): ?string
    {
        try {
            return route($routeName, [], false);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function trimToEssentials(string $markdown): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $markdown);
        $skipPatterns = '/^#{1,3}\s+(Glossary|Layout|Sidebar|Catatan Akhir|Footer|Ringkasan Singkat|Catatan Logika|Hal yang TIDAK|Verifikasi)/i';

        // Pass 1: keep Peta Menu (always) + Ringkasan Peran (compact intro). These give the LLM full coverage of all menus.
        $intro = [];
        $skipSection = false;
        $inDetailSection = false;
        $currentSubsection = null;
        $subsectionBuffer = [];
        $subsections = [];

        foreach ($lines as $line) {
            if (preg_match('/^##\s+Detail Sub-Halaman per Menu/i', $line)) {
                $inDetailSection = true;
                continue;
            }
            if ($inDetailSection) {
                if (preg_match('/^###\s+(.+)/', $line, $m)) {
                    if ($currentSubsection !== null) {
                        $subsections[$currentSubsection] = $this->condenseSubsection($subsectionBuffer);
                    }
                    $currentSubsection = trim($m[1]);
                    $subsectionBuffer = [];
                    continue;
                }
                if (preg_match('/^##\s+/', $line)) {
                    // end of Detail Sub-Halaman section, save last
                    if ($currentSubsection !== null) {
                        $subsections[$currentSubsection] = $this->condenseSubsection($subsectionBuffer);
                        $currentSubsection = null;
                    }
                    $inDetailSection = false;
                    continue;
                }
                if ($currentSubsection !== null) {
                    $subsectionBuffer[] = $line;
                }
                continue;
            }
            // Not in detail section
            if (preg_match($skipPatterns, $line)) {
                $skipSection = true;
                continue;
            }
            if ($skipSection && preg_match('/^#{1,3}\s+/', $line)) {
                $skipSection = false;
            }
            if ($skipSection) continue;
            $intro[] = $line;
        }
        if ($currentSubsection !== null) {
            $subsections[$currentSubsection] = $this->condenseSubsection($subsectionBuffer);
        }

        $introText = trim(preg_replace('/\n{3,}/', "\n\n", implode("\n", $intro)));

        $detail = "## Detail Sub-Halaman per Menu (ringkas)\n\n";
        foreach ($subsections as $name => $condensed) {
            $detail .= "### {$name}\n{$condensed}\n\n";
        }

        $result = $introText . "\n\n" . $detail;
        $result = preg_replace('/`/', '', $result);
        $result = trim($result);

        $maxChars = 18000;
        if (strlen($result) > $maxChars) {
            $result = substr($result, 0, $maxChars) . "\n\n[... knowledge base dipotong ...]";
        }

        return $result;
    }

    /**
     * Condense subsection body: keep first description paragraph + button table rows (compact).
     */
    protected function condenseSubsection(array $bodyLines): string
    {
        $out = [];
        $tableStarted = false;
        $tableRowCount = 0;
        $foundFirstText = false;

        foreach ($bodyLines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;
            if (preg_match('/^\*\*Tampilan index\*\*:/i', $trimmed)) continue;
            if (preg_match('/^\*\*Catatan\*\*:\s*Mirror/i', $trimmed)) continue;
            if (preg_match('/^---+$/', $trimmed)) continue;

            if (preg_match('/^\|/', $trimmed)) {
                if (preg_match('/^\|---+/', $trimmed)) continue;
                if ($tableRowCount >= 12) continue;
                $tableStarted = true;
                $tableRowCount++;
                $out[] = $trimmed;
                continue;
            }
            if ($tableStarted) continue;

            if (!$foundFirstText) {
                if (strlen($trimmed) > 240) $trimmed = substr($trimmed, 0, 240) . '...';
                $out[] = $trimmed;
                $foundFirstText = true;
            }
        }
        return implode("\n", $out);
    }

    protected function isExcludedRoute(string $name): bool
    {
        $excluded = ['_debugbar', 'ignition', 'livewire', 'sanctum', 'telescope', 'horizon', 'passport', 'l5-swagger'];
        foreach ($excluded as $prefix) {
            if (str_starts_with($name, $prefix)) return true;
        }
        return false;
    }
}
