<?php

return [
    [
        'label' => 'Mulai',
        'items' => [
            [
                'label' => 'Beranda Admin',
                'description' => 'Ringkasan dan panduan langkah berikutnya',
                'route' => 'admin.dashboard',
                'patterns' => ['admin.dashboard'],
                'icon' => 'fa-house',
                'keywords' => 'dashboard overview mulai panduan',
            ],
        ],
    ],
    [
        'label' => 'Persiapan Data',
        'hint' => 'Kerjakan berurutan saat awal tahun ajaran',
        'items' => [
            [
                'label' => '1. Tahun Ajaran',
                'description' => 'Buat dan aktifkan periode belajar',
                'route' => 'admin.tahun-ajaran.index',
                'patterns' => ['admin.tahun-ajaran.*'],
                'icon' => 'fa-calendar-days',
                'keywords' => 'semester periode tahun ajaran aktif',
            ],
            [
                'label' => '2. Cabang',
                'description' => 'Atur lokasi dan unit sekolah',
                'route' => 'admin.cabang.index',
                'patterns' => ['admin.cabang.*'],
                'icon' => 'fa-building',
                'keywords' => 'lokasi unit sekolah master',
            ],
            [
                'label' => '3. Pengguna',
                'description' => 'Input atau impor akun sekolah',
                'icon' => 'fa-users',
                'patterns' => ['admin.users.*', 'admin.recovery-tickets.*'],
                'children' => [
                    ['label' => 'Tenaga Pendidik', 'route' => 'admin.users.tenaga-pendidik', 'patterns' => ['admin.users.tenaga-pendidik*'], 'keywords' => 'guru staf tutor import akun'],
                    ['label' => 'Siswa', 'route' => 'admin.users.siswa', 'patterns' => ['admin.users.siswa*'], 'keywords' => 'murid peserta didik import massal'],
                    ['label' => 'Wali Siswa', 'route' => 'admin.users.wali-siswa', 'patterns' => ['admin.users.wali-siswa*'], 'keywords' => 'orang tua akun import'],
                    ['label' => 'Tiket Pemulihan', 'route' => 'admin.recovery-tickets.index', 'patterns' => ['admin.recovery-tickets.*'], 'keywords' => 'lupa password pulihkan akun'],
                ],
            ],
            [
                'label' => '4. Kelas & Penugasan',
                'description' => 'Susun kelas lalu tempatkan warga sekolah',
                'icon' => 'fa-chalkboard-user',
                'patterns' => ['admin.kelas.*', 'admin.wali-kelas.*', 'admin.guru-pengajar.*', 'admin.manajemen-siswa.*'],
                'children' => [
                    ['label' => 'Data Kelas', 'route' => 'admin.kelas.index', 'patterns' => ['admin.kelas.*'], 'keywords' => 'rombongan belajar rombel import'],
                    ['label' => 'Wali Kelas', 'route' => 'admin.wali-kelas.index', 'patterns' => ['admin.wali-kelas.*'], 'keywords' => 'penugasan wali'],
                    ['label' => 'Guru Pengajar', 'route' => 'admin.guru-pengajar.index', 'patterns' => ['admin.guru-pengajar.*'], 'keywords' => 'pengampu mapel penugasan'],
                    ['label' => 'Tempatkan Siswa', 'route' => 'admin.manajemen-siswa.index', 'patterns' => ['admin.manajemen-siswa.*'], 'keywords' => 'murid masuk kelas pindah massal'],
                ],
            ],
            [
                'label' => '5. Mata Pelajaran',
                'description' => 'Siapkan daftar pelajaran',
                'route' => 'admin.mata-pelajaran.index',
                'patterns' => ['admin.mata-pelajaran.*'],
                'icon' => 'fa-book-open',
                'keywords' => 'mapel pelajaran kurikulum import',
            ],
            [
                'label' => '6. Jadwal Pelajaran',
                'description' => 'Susun jadwal setelah kelas dan guru siap',
                'route' => 'admin.jadwal-pelajaran.index',
                'patterns' => ['admin.jadwal-pelajaran.*', 'admin.pengaturan-istirahat.*'],
                'icon' => 'fa-calendar-week',
                'keywords' => 'jadwal jam guru kelas istirahat import',
            ],
        ],
    ],
    [
        'label' => 'Operasional',
        'items' => [
            [
                'label' => 'Keuangan',
                'description' => 'Tagihan, pembayaran, dan laporan',
                'icon' => 'fa-wallet',
                'patterns' => ['admin.keuangan.*'],
                'children' => [
                    ['label' => 'Tagihan', 'route' => 'admin.keuangan.tagihan.index', 'patterns' => ['admin.keuangan.tagihan.*'], 'keywords' => 'spp tunggakan invoice'],
                    ['label' => 'Pembayaran', 'route' => 'admin.keuangan.pembayaran.index', 'patterns' => ['admin.keuangan.pembayaran.*'], 'keywords' => 'bayar transaksi kwitansi'],
                    ['label' => 'Validasi Ujian & Rapor', 'route' => 'admin.keuangan.validasi-akses.index', 'patterns' => ['admin.keuangan.validasi-akses.*'], 'keywords' => 'izin akses dispensasi'],
                    ['label' => 'Validasi Dispensasi', 'route' => 'admin.keuangan.kenaikan-kelas.validation.index', 'patterns' => ['admin.keuangan.kenaikan-kelas.validation.*'], 'keywords' => 'kenaikan kelas keuangan'],
                    ['label' => 'Laporan Keuangan', 'route' => 'admin.keuangan.laporan.index', 'patterns' => ['admin.keuangan.laporan.*'], 'keywords' => 'rekap cetak belum lunas'],
                    ['label' => 'Pengaturan Pembayaran', 'route' => 'admin.keuangan.info-pembayaran.index', 'patterns' => ['admin.keuangan.info-pembayaran.*'], 'keywords' => 'rekening konfigurasi'],
                ],
            ],
            [
                'label' => 'Kenaikan Kelas',
                'description' => 'Atur syarat lalu proses kenaikan',
                'icon' => 'fa-arrow-up-right-dots',
                'patterns' => ['admin.akademik.kenaikan-kelas.*'],
                'children' => [
                    ['label' => 'Pengaturan KKM', 'route' => 'admin.akademik.kenaikan-kelas.kkm.index', 'patterns' => ['admin.akademik.kenaikan-kelas.kkm.*'], 'keywords' => 'nilai minimal'],
                    ['label' => 'Aturan Kenaikan', 'route' => 'admin.akademik.kenaikan-kelas.settings.index', 'patterns' => ['admin.akademik.kenaikan-kelas.settings.*'], 'keywords' => 'syarat konfigurasi'],
                    ['label' => 'Proses & Rekap', 'route' => 'admin.akademik.kenaikan-kelas.report', 'patterns' => ['admin.akademik.kenaikan-kelas.report*'], 'keywords' => 'eksekusi hasil naik kelas'],
                ],
            ],
            [
                'label' => 'Publikasi',
                'description' => 'Informasi untuk warga sekolah',
                'icon' => 'fa-bullhorn',
                'patterns' => ['admin.akademik.kalender.*', 'admin.akademik.pengumuman.*', 'admin.akademik.flyer.*', 'admin.akademik.berita.*'],
                'children' => [
                    ['label' => 'Kalender Akademik', 'route' => 'admin.akademik.kalender.index', 'patterns' => ['admin.akademik.kalender.*'], 'keywords' => 'agenda tanggal kegiatan'],
                    ['label' => 'Pengumuman', 'route' => 'admin.akademik.pengumuman.index', 'patterns' => ['admin.akademik.pengumuman.*'], 'keywords' => 'informasi siswa'],
                    ['label' => 'Flyer / Iklan', 'route' => 'admin.akademik.flyer.index', 'patterns' => ['admin.akademik.flyer.*'], 'keywords' => 'banner promosi'],
                    ['label' => 'Berita', 'route' => 'admin.akademik.berita.index', 'patterns' => ['admin.akademik.berita.*'], 'keywords' => 'artikel publikasi'],
                ],
            ],
        ],
    ],
    [
        'label' => 'Pantau & Evaluasi',
        'items' => [
            [
                'label' => 'Monitoring Sistem',
                'description' => 'Pantau pengguna dan aktivitas LMS',
                'icon' => 'fa-chart-line',
                'patterns' => ['admin.monitoring.*'],
                'children' => [
                    ['label' => 'Pengguna', 'route' => 'admin.monitoring.pengguna', 'patterns' => ['admin.monitoring.pengguna'], 'keywords' => 'aktivitas akun'],
                    ['label' => 'Wali Kelas', 'route' => 'admin.monitoring.wali-kelas', 'patterns' => ['admin.monitoring.wali-kelas'], 'keywords' => 'kinerja wali'],
                    ['label' => 'Guru Pengajar', 'route' => 'admin.monitoring.guru-pengajar', 'patterns' => ['admin.monitoring.guru-pengajar'], 'keywords' => 'kinerja guru'],
                    ['label' => 'Siswa', 'route' => 'admin.monitoring.siswa', 'patterns' => ['admin.monitoring.siswa'], 'keywords' => 'aktivitas murid'],
                    ['label' => 'LMS', 'route' => 'admin.monitoring.lms.index', 'patterns' => ['admin.monitoring.lms.*'], 'keywords' => 'materi tugas ujian'],
                ],
            ],
            [
                'label' => 'Laporan & Catatan',
                'description' => 'Cetak rekap dan dokumentasikan temuan',
                'icon' => 'fa-file-lines',
                'patterns' => ['admin.laporan.*', 'admin.catatan.*'],
                'children' => [
                    ['label' => 'Laporan Akademik', 'route' => 'admin.laporan.index', 'patterns' => ['admin.laporan.*'], 'keywords' => 'rekap cetak data'],
                    ['label' => 'Catatan Monitoring', 'route' => 'admin.catatan.index', 'patterns' => ['admin.catatan.*'], 'keywords' => 'laporan catatan'],
                ],
            ],
        ],
    ],
    [
        'label' => 'Sistem',
        'items' => [
            [
                'label' => 'Pengaturan Sistem',
                'description' => 'Konfigurasi fitur internal',
                'icon' => 'fa-sliders',
                'patterns' => ['admin.lms-settings.*', 'admin.ai-settings.*'],
                'children' => [
                    ['label' => 'Pengaturan LMS', 'route' => 'admin.lms-settings.index', 'patterns' => ['admin.lms-settings.*'], 'keywords' => 'fitur belajar'],
                    ['label' => 'Pengaturan AI', 'route' => 'admin.ai-settings.index', 'patterns' => ['admin.ai-settings.*'], 'keywords' => 'chatbot model groq'],
                ],
            ],
            [
                'label' => 'Landing Page',
                'description' => 'Kelola tampilan situs publik',
                'route' => 'admin.landing-pages.index',
                'patterns' => ['admin.landing-pages.*'],
                'icon' => 'fa-globe',
                'keywords' => 'website halaman depan publik',
            ],
        ],
    ],
];
