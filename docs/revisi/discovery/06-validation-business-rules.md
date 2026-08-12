# Validation Inventory

Inventaris ini mengelompokkan aturan yang berulang. Ia tidak menggantikan setiap array validasi inline (200 titik ditemukan), tetapi setiap kelompok aturan bisnis utama dan sumber representatif dicatat agar Stage 4 dapat menurunkan positive/negative test case tanpa menebak.

| Rule ID | Feature ID | Modul | Kondisi | Jenis | Expected Behavior | Source Reference |
|---|---|---|---|---|---|---|
| RULE-001 | FEAT-001 | AU | `login` dan `password` wajib string | VALIDATION | Request kosong/non-string ditolak dengan validation error. | `LoginRequest::rules` |
| RULE-002 | FEAT-001 | AU | Turnstile wajib valid bila secret dikonfigurasi | VALIDATION | Token kosong/gagal siteverify ditolak; bila secret kosong check dilewati. | `LoginRequest::ensureTurnstileVerified` |
| RULE-003 | FEAT-001 | AU | Maksimal 5 attempt per login+IP selama decay 5 menit | BUSINESS RULE | Attempt berikutnya ditolak throttle sampai limiter tersedia. | `LoginRequest::ensureIsNotRateLimited` |
| RULE-004 | FEAT-005 | AU | Password baru memenuhi rule dan konfirmasi/old password | VALIDATION | Password tidak sesuai atau sama dengan password lama pada jalur recovery ditolak. | `AccountController`; `routes/web.php:148-194` |
| RULE-005 | FEAT-006 | AU | Foto profil harus image dengan jenis/ukuran yang diizinkan | VALIDATION | File invalid ditolak; file valid disimpan pada public disk. | `ProfileController@updatePhoto` |
| RULE-006 | FEAT-007–009 | USR | Email/username/identitas pengguna harus unik dan referensi role/cabang/kelas valid | VALIDATION | Duplikat atau foreign key tidak ada ditolak sebelum simpan. | `Admin/UserController` validation blocks |
| RULE-007 | FEAT-011 | USR | Import wajib file spreadsheet dengan struktur yang dikenali | VALIDATION | File/baris invalid dilaporkan; data valid dinormalisasi/diproses. | `app/Imports/*Import.php`; routes import |
| RULE-008 | FEAT-014 | ORG | Data cabang memiliki field wajib/unik sesuai controller | VALIDATION | Input kosong/duplikat ditolak. | `CabangController@store/update` |
| RULE-009 | FEAT-015 | ORG | Format tahun ajaran/periode dan status aktif harus valid | VALIDATION | Periode invalid ditolak; toggle hanya target ada. | TahunAjaran controllers |
| RULE-010 | FEAT-016–017 | ORG | Kelas memerlukan cabang, TA, jenjang/kapasitas valid | VALIDATION | Foreign key/angka invalid ditolak; penempatan melebihi rule tidak diproses. | Kelas controllers |
| RULE-011 | FEAT-019 | ORG | Parent harus role orang_tua, relationship termasuk nilai yang diizinkan dan pasangan tidak duplikat | VALIDATION | Relasi tidak valid/duplikat ditolak. | ManajemenSiswa controllers; migration pivot unique |
| RULE-012 | FEAT-021 | AKD | Mapel membutuhkan kode/nama; filter agama mengikuti nilai yang disediakan | VALIDATION | Duplikat/format/filter invalid ditolak. | MataPelajaran controllers; migration `2026_08_12*` |
| RULE-013 | FEAT-022–024 | AKD | Jadwal membutuhkan TA, guru, mapel, kelas, hari, jam mulai/selesai valid | VALIDATION | Missing FK, jam terbalik, atau payload kelas invalid ditolak. | JadwalPelajaran controllers |
| RULE-014 | FEAT-027 | AKD | Interval istirahat memerlukan jenjang/hari/jam dan status valid | VALIDATION | Input invalid/overlap yang dicegah controller ditolak. | PengaturanIstirahat controllers |
| RULE-015 | FEAT-031–034 | KON | Konten memerlukan judul/isi/tanggal/status; lampiran/media memiliki MIME/size | VALIDATION | Input/file invalid tidak disimpan; file lama dikelola saat update/delete. | Akademik/Sekretaris controllers |
| RULE-016 | FEAT-037–039 | KEU | Nominal tagihan numeric/min, rupiah Indonesia dinormalisasi sebelum validasi | VALIDATION | `1.500.000` tidak menyusut menjadi nilai berbeda; kosong/invalid ditolak. | Tagihan controllers; `NominalRupiahTidakBerubahTest` |
| RULE-017 | FEAT-043 | KEU | Transfer orang tua wajib bukti JPEG/PNG/JPG maksimal 10 MB | VALIDATION | Transfer tanpa/invalid image ditolak. | `OrangTuaController@prosesBayar/processBulkPay` |
| RULE-018 | FEAT-043–044 | KEU | Metode online Orang Tua hanya `transfer` atau `midtrans`; nominal minimum diberlakukan | VALIDATION | Nilai tunai/jenis lain atau nominal di bawah batas ditolak. | Parent payment validation |
| RULE-019 | FEAT-046 | KEU | Konfigurasi Midtrans memerlukan merchant ID, server key, client key dan boolean mode/enable | VALIDATION | Konfigurasi tidak lengkap tidak dianggap configured; payload invalid ditolak. | InfoPembayaran controller/model |
| RULE-020 | FEAT-048–051 | PRS | Presensi/izin membutuhkan siswa, tanggal, status/alasan dan file bukti sesuai rule | VALIDATION | Field/status/file invalid ditolak. | PresensiController; OrangTuaController |
| RULE-021 | FEAT-053–056 | NIL | Nilai numeric dalam rentang yang ditetapkan; koma dinormalisasi ke titik | VALIDATION | Nilai invalid ditolak; `87,5` disimpan sebagai decimal setara. | Guru/Wali Nilai controllers; decimal tests |
| RULE-022 | FEAT-058–059 | RAP | Generate/edit rapor memerlukan siswa/semester/TA/field nilai valid | VALIDATION | Target/field invalid ditolak dan tidak membuat rapor inkonsisten. | RaporController validation blocks |
| RULE-023 | FEAT-063 | RAP | Request/keputusan download memerlukan rapor/request/status yang valid | VALIDATION | Target/status invalid ditolak; token hanya dibuat sesuai flow. | RaporController; OrangTuaController |
| RULE-024 | FEAT-066 | LMS | Tipe materi harus cocok dengan file/URL; file mengikuti extension/MIME/size | VALIDATION | Mismatch tipe-file ditolak. | GuruMateriController; `GuruMateriUploadTypeTest` |
| RULE-025 | FEAT-068–069 | LMS | Tugas memerlukan judul/periode; submission text/file mengikuti jenis dan batas | VALIDATION | Payload/file invalid atau submission terkunci ditolak. | GuruTugas/LmsTugas controllers |
| RULE-026 | FEAT-070–071 | LMS | Ujian/soal membutuhkan waktu/durasi/tipe; opsi dan kunci sesuai tipe soal | VALIDATION | Konfigurasi/soal malformed ditolak; bank soal tidak rusak. | GuruUjianController; `SoalUjianSecurityTest` |
| RULE-027 | FEAT-072 | LMS | Generator AI memerlukan topik dan jumlah soal 1–10 | VALIDATION | Topik kosong/jumlah di luar rentang/error JSON menghasilkan error, bukan soal invalid. | `AiQuestionGeneratorService` |
| RULE-028 | FEAT-073 | LMS | Autosave/submit ujian menerima jawaban dalam struktur soal yang valid | VALIDATION | Jawaban tidak sesuai tipe/attempt ditolak atau dinormalisasi aman. | LmsUjianController; SoalUjian model |
| RULE-029 | FEAT-077 | LMS | Forum/reply memerlukan isi; attachment dibatasi; parent reply harus diskusi sama | VALIDATION | Isi/file invalid atau cross-discussion parent ditolak. | Forum controllers; `ForumReplyParentScopeTest` |
| RULE-030 | FEAT-078 | LMS | Meeting memerlukan judul/link/periode yang valid | VALIDATION | URL/field invalid tidak disimpan. | GuruLmsMeetingController |
| RULE-031 | FEAT-080–081 | PRM | KKM/bobot/threshold/TA/kelas tujuan mengikuti tipe dan rentang | VALIDATION | Setting invalid ditolak; TA aktif wajib ada. | Promotion KKM/Settings controllers |
| RULE-032 | FEAT-083–084 | PRM | Override/approval memerlukan target status dan alasan/keputusan valid | VALIDATION | Payload tidak lengkap atau target tidak eligible ditolak. | PromotionValidation/Approval controllers |
| RULE-033 | FEAT-086 | PRM | Jadwal eksekusi memerlukan tanggal/waktu/TA dan opsi valid | VALIDATION | Schedule invalid tidak dibuat/dijalankan. | PromotionReportController; PromotionSchedule |
| RULE-034 | FEAT-089 | MON | Catatan memerlukan penerima/isi dan target konten valid bila monitoring LMS | VALIDATION | Payload/tipe target invalid ditolak/404. | monitoring controllers/service |
| RULE-035 | FEAT-091–092 | NOT | ID dan bulk action notifikasi harus valid | VALIDATION | Action/ID invalid tidak mengubah notifikasi. | NotificationController |

# Authorization Rules

| Rule ID | Feature ID | Modul | Kondisi | Jenis | Expected Behavior | Source Reference |
|---|---|---|---|---|---|---|
| RULE-036 | FEAT-001–100 | Semua | Route protected memerlukan session dan role yang cocok | AUTHORIZATION | Guest diarahkan login; role salah memperoleh 403 dan tetap login. | `auth`; `CheckRole`; test role |
| RULE-037 | FEAT-001–100 | Semua | Admin mendapat bypass pada middleware `role:*` | AUTHORIZATION | Admin dapat melanjutkan route role lain; non-Admin tetap diperiksa. | `CheckRole::handle` |
| RULE-038 | FEAT-001, 010 | AU/USR | User `is_active=false` tidak boleh login/memakai session | AUTHORIZATION | Login ditolak atau session dilogout dan halaman inactive diberikan. | LoginRequest; EnsureUserIsActive |
| RULE-039 | FEAT-015–026, 087–089 | ORG/AKD/MON | Waka hanya mengelola cabang pada akun | AUTHORIZATION | Akun tanpa cabang/cross-cabang → 403; query list scoped. | Waka controllers; IDOR test |
| RULE-040 | FEAT-018, 028, 048–064, 082 | WKL | Wali hanya mengakses kelas current/historical assignment yang relevan | AUTHORIZATION | Target kelas/siswa/rapor/presensi lain → 403/404. | Wali controllers; IDOR tests |
| RULE-041 | FEAT-029, 053, 056, 065–079 | GRU/LMS | Guru hanya mengelola kelas-mapel yang sedang/pernah diampu sesuai jenis aksi | AUTHORIZATION | Cross-mapel/kelas → 403/404; arsip hanya sumber milik/pernah diampu dan target aktif. | Guru controllers/services; IDOR tests |
| RULE-042 | FEAT-067, 069, 073, 076–078, 094–095 | SIS/LMS | Siswa harus terhubung ke profil/kelas dan mapel ada di jadwal serta sesuai agama | AUTHORIZATION | Data invalid → 403; mapel tidak cocok → redirect error. | CheckSiswaMapelAccess |
| RULE-043 | FEAT-067, 069, 073, 076–078 | SIS/LMS | LMS hanya untuk jenjang yang diaktifkan pada AppSetting | AUTHORIZATION | Jenjang tidak allowed menerima halaman LMS disabled. | CheckLmsAccess |
| RULE-044 | FEAT-096 | SWA | Alumni tidak boleh mengakses route aktif/mutasi | AUTHORIZATION | Route selain whitelist dialihkan ke dashboard SIA read-only. | CheckStudentActive |
| RULE-045 | FEAT-043–045, 050, 052, 063, 097–100 | WLS | Orang Tua hanya dapat mengakses child relation sendiri | AUTHORIZATION | Siswa/tagihan/payment/rapor/presensi anak lain → redirect 403/404; tidak berubah. | OrangTuaController; ownership tests |
| RULE-046 | FEAT-045 | KEU | Webhook tanpa auth hanya dipercaya setelah signature server-side valid | AUTHORIZATION | Signature berbeda → JSON 403 dan tidak update payment. | MidtransWebhookController |
| RULE-047 | FEAT-006, 024, 031–034, 043, 050, 066, 069, 071 | Storage | Preview file memerlukan signed URL terikat owner dan path/type whitelist | AUTHORIZATION | Token expired/pemilik/path/type invalid → 403/404. | FileController; FilePreviewAccessTest |
| RULE-048 | FEAT-077 | LMS | Edit/delete forum/reply dibatasi author dan assignment | AUTHORIZATION | Pengguna lain/cross-mapel tidak dapat memodifikasi. | Forum controllers |
| RULE-049 | FEAT-091–092 | NOT | Notifikasi selalu di-scope `user_id` login | AUTHORIZATION | ID notifikasi user lain tidak ditemukan/tidak berubah. | NotificationController |
| RULE-050 | FEAT-013, 035, 046, 080 | USR/KON/KEU/PRM | Pengaturan sensitif hanya Admin atau role eksplisit | AUTHORIZATION | Direct URL role lain ditolak middleware. | route groups |

# Business Rules

| Rule ID | Feature ID | Modul | Kondisi | Jenis | Expected Behavior | Source Reference |
|---|---|---|---|---|---|---|
| RULE-051 | FEAT-015 | ORG | Tahun ajaran aktif menjadi scope default banyak modul | BUSINESS RULE | Dashboard/jadwal/kelas/promotion memakai TA aktif; ketiadaan TA ditangani per fitur. | Dashboard/controllers/services |
| RULE-052 | FEAT-022–026 | AKD | Jadwal multi-kelas adalah sumber assignment GuruPengajarKelas | BUSINESS RULE | Create/change/rebuild menyelaraskan guru-kelas-mapel; jadwal aktif menjadi dasar akses. | Jadwal/GuruPengajar controllers |
| RULE-053 | FEAT-038 | KEU | Generate SPP tidak boleh menghasilkan tagihan periode yang sudah ada | BUSINESS RULE | Existing combination dilewati/dilaporkan, bukan digandakan. | Tagihan controllers |
| RULE-054 | FEAT-040, 043 | KEU | Tunggakan TA lama harus di-carryover sebelum dibayar melalui portal | BUSINESS RULE | Tagihan lama tanpa link asal/tujuan ditolak dengan instruksi hubungi Bendahara. | parent payment guards; carryover service |
| RULE-055 | FEAT-041–045 | KEU | Status tagihan dihitung dari total pembayaran `disetujui`, bukan record pending/ditolak | BUSINESS RULE | Sisa >0 → belum/cicilan; lunas → sudah_bayar sesuai model. | `Tagihan::updateStatusBayar`; promotion payment tests |
| RULE-056 | FEAT-043 | KEU | Tunai tidak dapat diajukan online oleh Orang Tua | BUSINESS RULE | Payload tunai ditolak meski JS dibypass; tunai hanya input Admin/Bendahara. | `OrangTuaController@prosesBayar` |
| RULE-057 | FEAT-044 | KEU | Payment Midtrans hanya ditawarkan bila key lengkap dan enabled | BUSINESS RULE | Tidak configured/disabled → opsi/error dan Snap tidak dibuat. | InfoPembayaran model; parent view/controller |
| RULE-058 | FEAT-044–045 | KEU | Satu order bulk dapat merepresentasikan beberapa payment record | BUSINESS RULE | Webhook/finish memproses seluruh record `order_id`, bukan hanya pertama. | webhook/parent controller; tests |
| RULE-059 | FEAT-045 | KEU | Finish URL tidak otoritatif; webhook atau Transaction API menentukan status | BUSINESS RULE | Query `transaction_status` tidak langsung mengubah DB. | `OrangTuaController@snapFinish`; forgery test |
| RULE-060 | FEAT-045 | KEU | Payment sukses membatalkan pending lain untuk tagihan yang sama | BUSINESS RULE | Duplicate pending menjadi ditolak dan audit dicatat. | MidtransWebhookController; snapFinish |
| RULE-061 | FEAT-045 | KEU | Webhook/retry harus idempotent pada status dan notifikasi | BUSINESS RULE | Record status sama dilewati; notifikasi hanya untuk transisi baru disetujui. | webhook implementation/tests |
| RULE-062 | FEAT-048–051 | PRS | Pengajuan izin dilakukan Orang Tua, bukan Siswa | BUSINESS RULE | Route ajukan izin siswa dinonaktifkan; parent route digunakan. | `routes/web.php:1430-1433,1576-1590` |
| RULE-063 | FEAT-055 | NIL | Nilai LMS disinkronkan oleh observer setelah submission berubah | BUSINESS RULE | Nilai rekap mengikuti TugasSiswa/UjianSiswa tanpa input duplikat manual. | AppServiceProvider; NilaiSyncService |
| RULE-064 | FEAT-060–063 | RAP | Mutasi/publikasi/validasi/download rapor bergantung state workflow | BUSINESS RULE | Transition/action pada state atau pemilik yang salah ditolak; request-download terpisah dari publikasi. | Rapor/Validasi controllers |
| RULE-065 | FEAT-063, 100 | RAP | Orang Tua tidak langsung mengunduh rapor tanpa approval/token valid | BUSINESS RULE | Request dibuat; Wali memutuskan; download memakai token valid/berlaku. | RequestDownload flow |
| RULE-066 | FEAT-070–076 | LMS | Kunci jawaban tidak boleh diserialisasi ke sisi Siswa | BUSINESS RULE | Model menyembunyikan kunci; hanya konteks Guru dapat membuat visible. | SoalUjian model/security tests |
| RULE-067 | FEAT-073–074 | LMS | Satu attempt ujian memiliki state mulai, autosave, submit, review/retake | BUSINESS RULE | Aksi di luar state/time/attempt ditolak; log terikat attempt. | LmsUjianController |
| RULE-068 | FEAT-079 | LMS | Sumber arsip boleh historis, tetapi target penyalinan harus assignment TA aktif | BUSINESS RULE | Target tidak aktif/tidak diampu menimbulkan RuntimeException dan tidak disalin. | GuruLmsArsipService |
| RULE-069 | FEAT-080–086 | PRM | Kelayakan kenaikan menggabungkan aspek akademik dan finansial serta approval | BUSINESS RULE | Siswa menunggak/tidak tuntas tidak otomatis eligible kecuali override/keputusan yang tersedia. | PromotionService/tests |
| RULE-070 | FEAT-086 | PRM | Eksekusi promotion terjadwal tidak boleh overlap | BUSINESS RULE | Scheduler memakai `withoutOverlapping`; due schedule diproses sekali per state. | routes console; command |

# Failure Conditions

| Error ID | Feature ID | Kondisi | Expected System Behavior | Implementasi Handling | Source Reference |
|---|---|---|---|---|---|
| ERR-001 | FEAT-001 | Kredensial salah | Login ditolak dengan pesan generik; rate limiter bertambah. | ValidationException | LoginRequest |
| ERR-002 | FEAT-001 | Akun nonaktif | Session tidak dibuat/dibatalkan dan pengguna diberi pesan akun nonaktif. | Auth logout + validation/page | LoginRequest; EnsureUserIsActive |
| ERR-003 | FEAT-001 | Turnstile gagal/API response tidak sukses | Login ditolak bila secret aktif. | ValidationException | LoginRequest |
| ERR-004 | FEAT-003 | Email recovery gagal/tidak tersedia | Tidak mengklaim email terkirim; tiket dapat jatuh ke proses Admin sesuai flow. | return false + log | EmailRecoveryService; recovery controller |
| ERR-005 | FEAT-007–020 | Data target tidak ditemukan | Request menghasilkan 404/redirect error; tidak membuat perubahan parsial. | `findOrFail`/guard | Admin/Waka controllers |
| ERR-006 | FEAT-007–020 | Delete entitas masih mempunyai jejak bisnis | Penghapusan ditolak/diubah menjadi nonaktif sesuai fitur. | relationship guard/transaction | User/Kelas controllers; delete guard tests |
| ERR-007 | FEAT-022–024 | Jadwal cross-cabang/konflik/tidak valid | Request ditolak dan jadwal tidak berubah. | 403/validation redirect | Waka Jadwal controller/test |
| ERR-008 | FEAT-031–034 | Upload konten invalid | Validation error; file invalid tidak disimpan. | Laravel validation | Akademik/Sekretaris controllers |
| ERR-009 | FEAT-040 | Carryover target/sumber invalid atau sudah dialihkan | Transaction dibatalkan dan pesan error diberikan. | service exception/transaction | TunggakanCarryoverService |
| ERR-010 | FEAT-043 | Parent mencoba tagihan anak lain | Pembayaran tidak dibuat; error/403. | relation/IDOR guard | parent controller/test |
| ERR-011 | FEAT-044 | Midtrans belum configured atau API gagal | Snap/payment flow tidak dilanjutkan; pengguna mendapat error layanan/config. | exception catch + log/redirect | MidtransService; parent controller |
| ERR-012 | FEAT-045 | Signature webhook invalid | HTTP 403 JSON; data tidak diperbarui. | explicit signature check | MidtransWebhookController |
| ERR-013 | FEAT-045 | `order_id` webhook tidak ditemukan | HTTP 404 JSON dan error log. | empty collection check | MidtransWebhookController |
| ERR-014 | FEAT-045 | Exception saat webhook | HTTP 500 JSON dan trace dicatat log. | try/catch | MidtransWebhookController |
| ERR-015 | FEAT-048–064 | Wali mengakses kelas/siswa lain | 403/404; tidak ada data berubah/terbaca. | assignment guards | Wali controllers/IDOR tests |
| ERR-016 | FEAT-065–079 | Guru mengakses assignment lain | 403/404; konten/nilai tidak berubah. | scoped query/abort | Guru controllers/IDOR tests |
| ERR-017 | FEAT-067–078 | Siswa tanpa kelas/mapel/LMS access | 403, redirect dashboard, atau halaman LMS disabled. | LMS middleware | CheckLmsAccess/MapelAccess |
| ERR-018 | FEAT-072 | Provider AI error/quota/JSON malformed | Fallback dicoba; bila tetap gagal error dikembalikan tanpa menyimpan soal rusak. | try/fallback/exception/log | AI services |
| ERR-019 | FEAT-073 | Attempt/time/state ujian invalid | Mulai/autosave/submit/retake ditolak atau diarahkan sesuai state. | controller guards | LmsUjianController |
| ERR-020 | FEAT-077 | Parent reply berasal dari diskusi lain | Validation/404; reply tidak dibuat/diubah. | scoped parent query | Forum controllers/test |
| ERR-021 | FEAT-063, 100 | Token rapor invalid/expired/bukan pemilik | Download ditolak; file tidak bocor. | token/ownership guard | parent download; FileController pattern |
| ERR-022 | FEAT-080–086 | TA aktif/readiness/approval promotion tidak tersedia | Setting/eksekusi ditolak; tidak ada pemindahan kelas parsial. | firstOrFail/readiness/transaction | promotion controllers/service |
| ERR-023 | FEAT-088 | Waka preview konten LMS cabang lain | HTTP 403. | `LmsMonitoringService::assertCabangAccess` | service |
| ERR-024 | FEAT-091–092 | ID notifikasi milik pengguna lain | HTTP 404; notifikasi tidak dibaca/dihapus. | query by `user_id` + findOrFail | NotificationController |
| ERR-025 | Semua | Session/CSRF kedaluwarsa | Recovery mendapat pesan sesi verifikasi; request lain diarahkan login/back dengan pesan 419. | exception rendering | `bootstrap/app.php` |

# Error Handling Observations

- Laravel validation lazimnya mengembalikan pengguna ke form dengan error dan input lama; beberapa endpoint JSON menangkap `ValidationException` dan membentuk JSON.
- `findOrFail` digunakan luas sehingga data tidak ditemukan menjadi 404.
- Otorisasi menggunakan campuran `abort(403)`, scoped `firstOrFail` (404), dan redirect dengan flash error. Stage 4 harus mengikuti expected behavior per endpoint, bukan memaksakan satu status untuk semua.
- Transaction eksplisit dipakai pada bulk payment, rapor, promotion, carryover dan mutasi lintas-entitas; exception memicu rollback pada jalur yang dibungkus transaction.
- External service failure dicatat melalui Laravel Log dan umumnya ditampilkan sebagai pesan generik kepada pengguna.
- Tidak ditemukan global domain exception taxonomy; handling terutama lokal pada controller/service.

## Inventory Statistics

- Validation rules/groups: 35
- Authorization rules: 15
- Business rules: 20
- Total validation/authorization/business rule entries: **70**
- Failure/error conditions: 25
