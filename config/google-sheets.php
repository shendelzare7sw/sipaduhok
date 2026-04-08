<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Sheets Integration Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines all settings for Google Sheets sync
    | integration with SIPADUHOK database.
    |
    */

    'enabled' => env('GOOGLE_SHEETS_ENABLED', false),

    'credentials_path' => env('GOOGLE_SERVICE_ACCOUNT_JSON_PATH', storage_path('app/credentials/google-service-account.json')),

    'default_spreadsheet_id' => env('GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID'),

    'auto_sync_enabled' => env('GOOGLE_SHEETS_AUTO_SYNC_ENABLED', true),

    'log_channel' => env('GOOGLE_SHEETS_LOG_CHANNEL', 'single'),

    'retry_attempts' => env('GOOGLE_SHEETS_RETRY_ATTEMPTS', 3),

    'timeout_seconds' => env('GOOGLE_SHEETS_TIMEOUT_SECONDS', 30),

    /*
    |--------------------------------------------------------------------------
    | Module Configuration - Tier 1 (Daily Sync)
    |--------------------------------------------------------------------------
    */
    'modules' => [
        'siswa' => [
            'enabled' => true,
            'model' => \App\Models\Siswa::class,
            'sheet_name' => 'Data Siswa',
            'sync_schedule' => 'daily_0030', // Jam 00:30 WIB
            'tier' => 1,
            'columns' => ['nis', 'nisn', 'nama', 'kelas_id', 'jenjang', 'cabang_id', 'status'],
            'mappings' => [
                'nis' => 'nis',
                'nisn' => 'nisn',
                'nama' => 'nama',
                'kelas' => 'kelas_id',
                'jenjang' => 'jenjang',
                'cabang' => 'cabang_id',
                'status' => 'status',
            ],
            'editable_columns' => ['status'],
            'readonly_columns' => ['nis', 'nisn', 'nama'],
        ],

        'guru' => [
            'enabled' => true,
            'model' => \App\Models\TenagaPendidik::class,
            'sheet_name' => 'Data Guru & Staff',
            'sync_schedule' => 'daily_0030',
            'tier' => 1,
            'columns' => ['nip', 'nama', 'role', 'email', 'status'],
            'mappings' => [
                'nip' => 'nip',
                'nama' => 'nama',
                'role' => 'role',
                'email' => 'email',
                'status' => 'status',
            ],
            'editable_columns' => [],
            'readonly_columns' => ['nip', 'nama', 'role', 'email', 'status'],
        ],

        'kelas' => [
            'enabled' => true,
            'model' => \App\Models\Kelas::class,
            'sheet_name' => 'Data Kelas',
            'sync_schedule' => 'daily_0030',
            'tier' => 1,
            'columns' => ['nama', 'kode', 'jenjang', 'cabang_id', 'wali_kelas', 'kuota_siswa'],
            'mappings' => [
                'nama' => 'nama',
                'kode' => 'kode_kelas',
                'jenjang' => 'jenjang',
                'cabang' => 'cabang_id',
                'wali_kelas' => 'wali_kelas',
                'kuota_siswa' => 'kuota_siswa',
            ],
            'editable_columns' => ['kuota_siswa'],
            'readonly_columns' => ['nama', 'kode', 'jenjang', 'cabang', 'wali_kelas'],
        ],

        'jadwal_pelajaran' => [
            'enabled' => true,
            'model' => \App\Models\JadwalPelajaran::class,
            'sheet_name' => 'Jadwal Pelajaran',
            'sync_schedule' => 'daily_0030',
            'tier' => 1,
            'columns' => ['hari', 'jam_mulai', 'jam_selesai', 'kelas_id', 'mata_pelajaran_id', 'guru_id', 'status'],
            'mappings' => [
                'hari' => 'hari',
                'jam_mulai' => 'jam_mulai',
                'jam_selesai' => 'jam_selesai',
                'kelas' => 'kelas_id',
                'mapel' => 'mata_pelajaran_id',
                'guru' => 'guru_id',
                'status' => 'status',
            ],
            'editable_columns' => ['status'],
            'readonly_columns' => ['hari', 'jam_mulai', 'jam_selesai', 'kelas', 'mapel', 'guru'],
        ],

        'presensi' => [
            'enabled' => true,
            'model' => \App\Models\Presensi::class,
            'sheet_name' => 'Presensi',
            'sync_schedule' => 'daily_0030',
            'tier' => 1,
            'columns' => ['tanggal', 'siswa_id', 'kelas_id', 'status', 'keterangan'],
            'mappings' => [
                'tanggal' => 'tanggal',
                'siswa' => 'siswa_id',
                'kelas' => 'kelas_id',
                'status' => 'status',
                'keterangan' => 'keterangan',
            ],
            'editable_columns' => ['status', 'keterangan'],
            'readonly_columns' => ['tanggal', 'siswa', 'kelas'],
        ],

        'nilai' => [
            'enabled' => true,
            'model' => \App\Models\Nilai::class,
            'sheet_name' => 'Nilai Siswa',
            'sync_schedule' => 'daily_0030',
            'tier' => 1,
            'columns' => ['siswa_id', 'kelas_id', 'mata_pelajaran_id', 'tugas', 'latihan', 'uh', 'pts', 'pas', 'na'],
            'mappings' => [
                'siswa' => 'siswa_id',
                'kelas' => 'kelas_id',
                'mapel' => 'mata_pelajaran_id',
                'tugas' => 'tugas',
                'latihan' => 'latihan',
                'uh' => 'uh',
                'pts' => 'pts',
                'pas' => 'pas',
                'na' => 'na',
            ],
            'editable_columns' => ['tugas', 'latihan', 'uh', 'pts', 'pas'],
            'readonly_columns' => ['siswa', 'kelas', 'mapel', 'na'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Module Configuration - Tier 2 (Weekly Sync)
        |--------------------------------------------------------------------------
        */

        'tagihan' => [
            'enabled' => true,
            'model' => \App\Models\Tagihan::class,
            'sheet_name' => 'Tagihan',
            'sync_schedule' => 'weekly_sunday_0100', // Minggu jam 01:00 WIB
            'tier' => 2,
            'columns' => ['siswa_id', 'nisn', 'kelas_id', 'jenis', 'nominal', 'status'],
            'mappings' => [
                'siswa' => 'siswa_id',
                'nisn' => 'nisn',
                'kelas' => 'kelas_id',
                'jenis' => 'jenis',
                'nominal' => 'nominal',
                'status' => 'status',
            ],
            'editable_columns' => [],
            'readonly_columns' => ['siswa', 'nisn', 'kelas', 'jenis', 'nominal', 'status'],
        ],

        'pembayaran' => [
            'enabled' => true,
            'model' => \App\Models\Pembayaran::class,
            'sheet_name' => 'Pembayaran',
            'sync_schedule' => 'weekly_sunday_0100',
            'tier' => 2,
            'columns' => ['kode_pembayaran', 'siswa_id', 'jumlah', 'metode', 'tanggal', 'status'],
            'mappings' => [
                'kode' => 'kode_pembayaran',
                'siswa' => 'siswa_id',
                'jumlah' => 'jumlah',
                'metode' => 'metode',
                'tanggal' => 'tanggal',
                'status' => 'status',
            ],
            'editable_columns' => [],
            'readonly_columns' => ['kode', 'siswa', 'jumlah', 'metode', 'tanggal', 'status'],
        ],

        'siswa_belum_lunas' => [
            'enabled' => true,
            'model' => null, // Derived view
            'sheet_name' => 'Siswa Belum Lunas',
            'sync_schedule' => 'weekly_sunday_0100',
            'tier' => 2,
            'columns' => ['siswa', 'kelas', 'total_tagihan', 'sisa_tagihan'],
            'is_derived' => true,
            'readonly_columns' => ['siswa', 'kelas', 'total_tagihan', 'sisa_tagihan'],
        ],

        'rekap_keuangan' => [
            'enabled' => true,
            'model' => null, // Derived view
            'sheet_name' => 'Rekap Keuangan',
            'sync_schedule' => 'weekly_sunday_0100',
            'tier' => 2,
            'columns' => ['bulan', 'total', 'tunai', 'transfer', 'midtrans'],
            'is_derived' => true,
            'readonly_columns' => ['bulan', 'total', 'tunai', 'transfer', 'midtrans'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Conflict Resolution Strategy
    |--------------------------------------------------------------------------
    | 'database' = Database is source of truth (Google Sheets is overwritten)
    | 'sheets' = Google Sheets is source of truth (Database is overwritten)
    | 'timestamp' = Newer timestamp wins
    */
    'conflict_resolution' => env('GOOGLE_SHEETS_CONFLICT_RESOLUTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Per-Cabang Spreadsheet Support
    |--------------------------------------------------------------------------
    */
    'per_cabang_enabled' => env('GOOGLE_SHEETS_PER_CABANG_ENABLED', false),

    'cabang_spreadsheets' => [
        // 'cabang_1' => 'spreadsheet_id_1',
        // 'cabang_2' => 'spreadsheet_id_2',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sheet Formatting
    |--------------------------------------------------------------------------
    */
    'formatting' => [
        'header_bg_color' => '#4285F4',
        'header_text_color' => '#FFFFFF',
        'freeze_header_row' => true,
        'auto_resize_columns' => true,
    ],
];
