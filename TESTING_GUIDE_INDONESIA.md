# 🧪 Google Sheets Integration - Testing Guide (Bahasa Indonesia)

## 📋 Daftar Isi
1. [Persyaratan Testing](#persyaratan-testing)
2. [Step-by-Step Testing](#step-by-step-testing)
3. [Manual Testing Operations](#manual-testing-operations)
4. [Troubleshooting](#troubleshooting)

---

## ✅ Persyaratan Testing

### 1. Database Sudah Migrasi
```bash
php artisan migrate
```

### 2. Queue Worker Berjalan
```bash
# Terminal 1: Jalankan queue worker
php artisan queue:work
# Jangan tutup! Ini diperlukan untuk proses async sync jobs
```

### 3. Server Laravel Berjalan
```bash
# Terminal 2: Jalankan Laravel server
php artisan serve
# Biasanya berjalan di http://localhost:8000
```

### 4. Google Cloud Setup Sudah Siap
- Service Account JSON sudah didownload
- Google Sheets API sudah diaktifkan
- Google Drive API sudah diaktifkan
- Sudah punya Spreadsheet ID (bisa buat baru di sheets.google.com)

---

## 🚀 Step-by-Step Testing

### STEP 1: Akses Dashboard
```
URL: http://localhost:8000/admin
```
- Ini adalah halaman admin dashboard

### STEP 2: Login Sebagai Admin
**User yang bisa akses**: Role `admin` (atau `superadmin`)

Jika belum ada admin account, buat via tinker:
```bash
php artisan tinker

# Buat user admin baru
$user = new App\Models\User;
$user->name = 'Admin Testing';
$user->email = 'admin@test.com';
$user->password = bcrypt('password123');
$user->role = 'admin';
$user->status = 'active';
$user->save();

# Untuk verifikasi email
$user->email_verified_at = now();
$user->save();

exit
```

**Login dengan:**
- Email: `admin@test.com`
- Password: `password123`

### STEP 3: Akses Menu Google Sheets
Setelah login sebagai admin:
1. Di sidebar, cari menu "Google Sheets Sync"
2. Klik menu tersebut
3. Akan membuka halaman dashboard: `/admin/google-sheets`

**Alternatif**: Langsung akses URL
```
http://localhost:8000/admin/google-sheets
```

### STEP 4: Lihat Dashboard
Di halaman ini akan terlihat:
- **Status Badge** di atas (Connected/Configured/Not Setup)
- **Info Box** dengan Spreadsheet ID, Auto-Sync status, jumlah modules
- **Module Grid** - 10 modul dengan Tier 1 (daily) dan Tier 2 (weekly)
- **Recent Sync History** - tabel riwayat sync (kosong saat pertama kali)

**Catatan**: Semua akan menunjukkan status "Not Setup" sampai credentials di-upload

---

## 🔧 Setup Configuration

### STEP 5: Klik "Setup Now"
1. Di dashboard, ada tombol biru "Setup Now"
2. Atau klik menu "Tahun Ajaran" di sidebar (akan membuka modal setup wizard)

### STEP 6: Setup Wizard - Step 1: Overview
Modal akan menampilkan:
- Judul "Google Sheets Setup"
- Deskripsi 4-step process
- Link ke Google Cloud Console
- Penjelasan singkat cara setup

**Apa yang harus dilakukan di Google Cloud:**
1. Go to https://console.cloud.google.com
2. Buat project baru (atau pilih yang existing)
3. Enable Google Sheets API
4. Enable Google Drive API
5. Create Service Account (IAM → Service Accounts)
6. Create JSON key untuk service account tersebut
7. Download JSON file
8. **Penting**: Share spreadsheet dengan email dari service account (lihat di JSON file, cari "client_email")

### STEP 7: Setup Wizard - Step 2: Upload JSON
1. Di panel "Upload Service Account JSON"
2. Ada area untuk drag-drop atau klik untuk upload
3. Pilih file JSON yang sudah didownload dari Google Cloud
4. Jika valid, akan menampilkan nama file

**File harus:**
- Format JSON
- Valid Google Service Account (type: "service_account")
- Berisi credentials lengkap

### STEP 8: Setup Wizard - Step 3: Configure Spreadsheet
1. Di panel "Configure Spreadsheet"
2. Masukkan Spreadsheet ID
3. Tempat mencari ID: Buka spreadsheet di Sheets → URL:
   ```
   https://docs.google.com/spreadsheets/d/[SPREADSHEET_ID]/edit
   ```

**Contoh URL:**
```
https://docs.google.com/spreadsheets/d/1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p/edit
ID: 1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p
```

### STEP 9: Setup Wizard - Step 4: Test Connection
1. Klik tombol biru "🔍 Test Connection"
2. Sistem akan:
   - Validasi JSON file
   - Validasi Spreadsheet ID
   - Coba akses API Google Sheets
   - Cek permission ke spreadsheet

**Hasil kemungkinan:**
- ✅ **Success**: "Connection successful! Access to spreadsheet verified."
  - Tombol "Save Configuration" menjadi aktif (hijau)
  
- ❌ **Failed**: Akan menampilkan error message
  - "Invalid Google Service Account JSON file" → JSON tidak valid
  - "Connection failed" → Service account tidak punya akses ke spreadsheet
  - "Spreadsheet ID is required" → ID tidak diisi

**Troubleshooting jika gagal:**
```
1. Pastikan JSON file valid dari Google Cloud Console
2. Pastikan Spreadsheet sudah di-share dengan email service account
   - Email bisa dilihat di JSON: "client_email"
   - Share spreadsheet dengan email tersebut minimal "Editor" role
3. Pastikan Spreadsheet ID benar (copy dari URL)
4. Tunggu beberapa detik sebelum retry
```

### STEP 10: Save Configuration
1. Setelah test connection berhasil, klik tombol hijau "✓ Save Configuration"
2. Akan menampilkan: "✓ Credentials saved successfully!"
3. Halaman akan redirect ke dashboard

---

## 📊 Testing - Manual Operations

Setelah setup selesai, dashboard akan menampilkan:

### Setup Sudah Complete ✓
- Status badge berubah dari "Not Setup" → "Configured" atau "Connected"
- Info box menampilkan Spreadsheet ID
- Semua module cards menampilkan tombol action

### Operation 1: Manual PUSH (Send to Sheets)

**Tujuan**: Kirim data dari database ke Google Sheets

1. Di module card, klik tombol biru "⬆️ Push"
2. Contoh: Klik Push untuk module "siswa"
3. Dialog confirm: "Push siswa data to Google Sheets?"
4. Klik OK
5. Akan terlihat: "✓ Sync job queued! Check back shortly."

**Apa yang terjadi di belakang:**
```
1. Job `SyncModuleToSheet` di-dispatch ke queue
2. Queue worker (Terminal 1) akan process job
3. Data dari table `siswa` akan di-push ke sheet "siswa" di Spreadsheet
4. Setelah selesai, sync log akan di-record
```

**Verifikasi:**
- Tunggu 2-5 detik
- Refresh halaman (F5)
- Lihat "Recent Sync History" - akan ada entry baru
- Status akan "success" atau "failed"
- Jika success, check Google Sheets - akan ada data baru

### Operation 2: Preview Data (Sebelum Import)

**Tujuan**: Lihat preview data dari Google Sheets sebelum import ke database

1. Di module card, klik tombol biru "⬇️ Preview"
2. Contoh: Klik Preview untuk module "guru"
3. Modal akan menampilkan:
   - Nama sheet: "guru"
   - Total rows: jumlah data di sheet
   - Tabel preview dengan kolom-kolom
   - Checkbox untuk select rows

**Di preview modal:**
- Bisa lihat data sampai 50 baris pertama
- Ada checkbox "Select All" untuk select semua
- Bisa uncheck row yang tidak mau di-import
- Tombol "Import Selected Data" untuk confirm

### Operation 3: Pull Data (Import dari Sheets)

**Tujuan**: Import data dari Google Sheets ke database

1. Setelah preview, klik "Import Selected Data"
2. Dialog confirm: jumlah row yang akan di-import
3. Klik OK
4. Akan terlihat: "✓ Pull job queued! Data will be imported shortly."

**Apa yang terjadi:**
```
1. Job di-dispatch dengan data row yang dipilih
2. Queue worker process job
3. Data dari Google Sheets di-import ke tabel database
4. Sync log di-record
```

**Verifikasi:**
- Check database untuk data baru yang di-import
- Lihat Recent Sync History di dashboard
- Status akan "success" atau "failed"

### Operation 4: Lihat Sync History

Di dashboard, section "Recent Sync History" menampilkan:
```
| Module | Direction | Sheet Name | Rows | Status | Synced By | Time |
|--------|-----------|-----------|------|--------|-----------|------|
| siswa | ⬆️ Push | siswa | 150 | ✓ success | Admin Testing | 14:30 |
| guru | ⬇️ Pull | guru | 25 | ✓ success | Admin Testing | 14:25 |
```

- **Module**: Nama modul yang di-sync
- **Direction**: ⬆️ Push (ke Sheets) atau ⬇️ Pull (dari Sheets)
- **Sheet Name**: Nama sheet di Spreadsheet
- **Rows**: Jumlah row yang di-sync
- **Status**: ✓ success atau ✗ failed
- **Synced By**: User yang trigger sync
- **Time**: Waktu sync

---

## 🤖 Testing - Automatic Scheduler

Setelah setup selesai, sync akan jalan otomatis sesuai jadwal:

### Tier 1 Sync (Harian 00:30)
Jalankan otomatis setiap hari jam 00:30 UTC:
```
siswa → database → sheets
guru → database → sheets
kelas → database → sheets
jadwal_pelajaran → database → sheets
presensi → database → sheets
nilai → database → sheets
```

### Tier 2 Sync (Mingguan Minggu 01:00)
Jalankan otomatis setiap Minggu jam 01:00 UTC:
```
tagihan → database → sheets
pembayaran → database → sheets
siswa_belum_lunas → calculated → sheets
rekap_keuangan → calculated → sheets
```

### Testing Scheduler Otomatis
Untuk test tanpa menunggu scheduled time, bisa trigger manual:

```bash
php artisan tinker

# Test push single module
dispatch(new \App\Jobs\SyncModuleToSheet('siswa', 'push'));

# Test pull single module (perlu ada data di sheets)
dispatch(new \App\Jobs\SyncModuleToSheet('guru', 'pull'));

exit
```

Queue worker akan langsung process job tersebut.

---

## 🔄 Testing Workflow Contoh

### Skenario: Test Push & Pull untuk Module "siswa"

**1. Setup (pertama kali)**
```
1. Login sebagai admin
2. Akses /admin/google-sheets
3. Klik "Setup Now"
4. Upload JSON file
5. Masukkan Spreadsheet ID
6. Test connection (harus berhasil)
7. Save configuration
```

**2. Buat/Update Data di Database**
```
1. Buka /admin/manajemen-siswa
2. Buat atau update beberapa siswa
3. Catat data yang dibuat (NIS, nama, dll)
```

**3. Push ke Google Sheets**
```
1. Kembali ke /admin/google-sheets
2. Klik "⬆️ Push" pada module "siswa"
3. Tunggu hingga sync selesai
4. Check Google Sheets - data siswa harus ada
5. Verify data di sheets cocok dengan database
```

**4. Modify Data di Google Sheets**
```
1. Di Google Sheets, edit beberapa data siswa
2. Contoh: ubah nama atau tambah kolom
3. Pastikan format tetap konsisten
```

**5. Pull dari Google Sheets**
```
1. Kembali ke /admin/google-sheets
2. Klik "⬇️ Preview" pada module "siswa"
3. Review data di preview modal
4. Select data yang mau di-import
5. Klik "Import Selected Data"
6. Tunggu hingga selesai
7. Check database - data baru harus ada
```

**6. Verify Sync History**
```
1. Lihat "Recent Sync History" di dashboard
2. Harus ada 2 entries:
   - siswa | Push | success
   - siswa | Pull | success
3. Click entry untuk lihat detail
```

---

## 🐛 Troubleshooting

### Problem 1: Menu "Google Sheets Sync" Tidak Muncul
**Penyebab**: 
- User tidak login sebagai admin
- Role user bukan admin

**Solusi**:
```
1. Verify user role:
   php artisan tinker
   >>> App\Models\User::where('email', 'admin@test.com')->first()->role
   # Harus return 'admin' atau 'superadmin'

2. Jika bukan admin, update:
   >>> $user = App\Models\User::where('email', 'admin@test.com')->first();
   >>> $user->role = 'admin';
   >>> $user->save();
```

### Problem 2: Dashboard Menampilkan "Not Setup"
**Penyebab**:
- JSON file belum di-upload
- Atau configuration belum di-save

**Solusi**:
1. Klik "Setup Now"
2. Ikuti 4-step wizard sampai selesai
3. Pastikan "Test Connection" berhasil sebelum save

### Problem 3: "Test Connection" Gagal

**Error: "Invalid Google Service Account JSON file"**
- JSON file tidak valid
- Download ulang dari Google Cloud Console

**Error: "Connection failed"**
- Service account tidak punya akses ke spreadsheet
- Share spreadsheet dengan email service account:
  1. Buka JSON file
  2. Cari field "client_email"
  3. Buka Google Sheets (spreadsheet)
  4. Klik Share (tombol kanan atas)
  5. Paste email tersebut
  6. Set permission ke "Editor"
  7. Share
  8. Tunggu 1-2 menit
  9. Retry test connection

**Error: "Spreadsheet ID is required"**
- Spreadsheet ID tidak diisi
- Atau format ID salah
- Pastikan copy dari URL sheets

### Problem 4: Push/Pull Gagal / Job Tidak Proses

**Penyebab**: Queue worker tidak berjalan

**Solusi**:
```bash
# Terminal 1: Jalankan queue worker
php artisan queue:work

# Di terminal lain, verify worker running:
php artisan queue:monitor

# Akan show: "1 job on the default queue."
```

### Problem 5: Sync History Kosong
**Penyebab**: Belum ada sync yang di-trigger

**Solusi**:
1. Manual trigger push/pull
2. Tunggu hingga sync selesai
3. Refresh dashboard
4. Sync history akan muncul

---

## 🔍 Debugging Tips

### Lihat Sync Logs di Database
```bash
php artisan tinker

# Lihat semua sync logs
>>> App\Models\GoogleSheetsSyncLog::latest()->limit(10)->get()

# Lihat sync logs untuk module tertentu
>>> App\Models\GoogleSheetsSyncLog::where('module', 'siswa')->latest()->limit(5)->get()

# Lihat failed syncs
>>> App\Models\GoogleSheetsSyncLog::where('status', 'failed')->get()

# Lihat detail error
>>> $log = App\Models\GoogleSheetsSyncLog::latest()->first();
>>> echo $log->error_message;
```

### Lihat Queue Jobs
```bash
# Lihat jobs yang pending
php artisan tinker
>>> DB::table('jobs')->count()

# Lihat detail job
>>> DB::table('jobs')->first()
```

### Test Service Langsung
```bash
php artisan tinker

# Test connection
>>> app(\App\Services\GoogleSheetsService::class)->testConnection()
# Return true atau false

# Get sheet data
>>> $data = app(\App\Services\GoogleSheetsService::class)->getSheetData('siswa');
>>> count($data)  # Jumlah baris
```

### Lihat Application Logs
```bash
# Real-time log untuk Google Sheets
tail -f storage/logs/google-sheets.log

# Atau full application log
tail -f storage/logs/laravel.log
```

---

## ✅ Checklist Testing Lengkap

- [ ] Database sudah migrate
- [ ] Queue worker berjalan
- [ ] Laravel server berjalan
- [ ] Login sebagai admin
- [ ] Menu "Google Sheets Sync" terlihat
- [ ] Setup wizard berhasil (test connection passed)
- [ ] Configuration tersave
- [ ] Dashboard menampilkan "Connected"
- [ ] Manual push berhasil
- [ ] Data terlihat di Google Sheets
- [ ] Manual pull preview terlihat
- [ ] Manual pull berhasil import
- [ ] Sync history ter-record
- [ ] Queue worker memproses job
- [ ] Tidak ada error di logs

---

## 📞 Masih Ada Pertanyaan?

Cek file dokumentasi lainnya:
- `GOOGLE_SHEETS_COMPLETION_REPORT.md` - Detail teknis lengkap
- `DEPLOYMENT_GUIDE.md` - Deployment ke production
- `test-all-phases-final.php` - Validation test logic
