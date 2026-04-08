# 🚀 Quick Testing Reference Card

## ⚡ TL;DR - Testing dalam 5 Langkah

### 1️⃣ Setup Terminal (Jalankan 2 terminal)
```bash
# Terminal 1: Queue Worker
cd c:\laragon\www\sipaduhok
php artisan queue:work

# Terminal 2: Laravel Server
php artisan serve
```

### 2️⃣ Create Admin User (via Tinker)
```bash
php artisan tinker

$user = new App\Models\User;
$user->name = 'Admin Test';
$user->email = 'admin@test.com';
$user->password = bcrypt('password123');
$user->role = 'admin';
$user->status = 'active';
$user->email_verified_at = now();
$user->save();

exit
```

### 3️⃣ Login & Access Menu
```
Login: http://localhost:8000/admin
  Email: admin@test.com
  Password: password123

Menu: Sidebar → "Google Sheets Sync"
```

### 4️⃣ Setup Google Sheets Integration
```
Dashboard → "Setup Now" → 4-Step Wizard:
  Step 1: Overview (info only)
  Step 2: Upload JSON (download dari Google Cloud)
  Step 3: Masukkan Spreadsheet ID
  Step 4: Test Connection → Save Configuration
```

### 5️⃣ Test Push/Pull
```
Push:   Dashboard → Module Card → "⬆️ Push" → Done ✓
Pull:   Dashboard → Module Card → "⬇️ Preview" → Select → Import
Check:  Recent Sync History → harus ada 2 entries (Push + Pull)
```

---

## 🔗 Quick URLs

| Halaman | URL |
|---------|-----|
| Login Admin | `http://localhost:8000/admin` |
| Dashboard Admin | `http://localhost:8000/admin` |
| Google Sheets Dashboard | `http://localhost:8000/admin/google-sheets` |
| Setup Wizard | `http://localhost:8000/admin/google-sheets/setup` |
| Student Management | `http://localhost:8000/admin/manajemen-siswa` |

---

## 👤 User Roles yang Bisa Akses

| Role | Akses | Catatan |
|------|-------|--------|
| admin | ✅ FULL ACCESS | Bisa setup, push, pull, disconnect |
| superadmin | ✅ FULL ACCESS | Seperti admin |
| guru | ❌ NO ACCESS | Middleware `role:admin` |
| siswa | ❌ NO ACCESS | Tidak ada menu di sidebar |
| bendahara | ❌ NO ACCESS | Middleware `role:admin` |

**Kesimpulan**: Harus login sebagai **Admin** atau **Superadmin**

---

## 📋 Google Cloud Setup (1x saja)

### Persiapan di Google Cloud Console

1. **Create Project**
   - Go to https://console.cloud.google.com
   - New Project → Beri nama "SIPADUHOK" → Create

2. **Enable APIs**
   - Search "Google Sheets API" → Enable
   - Search "Google Drive API" → Enable

3. **Create Service Account**
   - Navigate to: APIs & Services → Credentials
   - Create Credentials → Service Account
   - Fill form → Create and Continue
   - Grant Basic Editor Role
   - Continue → Done

4. **Create JSON Key**
   - Click Service Account yang baru dibuat
   - Tab "Keys" → Add Key → Create new key
   - Type: JSON
   - Download file → **SIMPAN BAIK-BAIK** (untuk Step 4 testing)

5. **Get Email Service Account**
   - Buka JSON file dengan text editor
   - Cari field: `"client_email": "xxx@xxx.iam.gserviceaccount.com"`
   - **Copy email ini**

6. **Create Spreadsheet**
   - Go to https://sheets.google.com
   - New Spreadsheet → Beri nama "SIPADUHOK"
   - **Copy Spreadsheet ID dari URL**
   - Share dengan service account email (dari Step 5) dengan role "Editor"

---

## 🧪 Testing Scenarios

### Scenario 1: Basic Push Test
```
1. Login as admin
2. Go to Google Sheets menu
3. Setup dengan JSON + Spreadsheet ID
4. Push module "siswa"
5. Wait 3-5 seconds
6. Refresh page
7. Check Recent Sync History → harus ada entry dengan status "success"
8. Verify di Google Sheets → data siswa harus ada
```

### Scenario 2: Full Cycle (Push + Pull)
```
1. Database: Create/Update 5 siswa
2. Dashboard: Push "siswa" module
3. Google Sheets: Verify data ada
4. Google Sheets: Edit beberapa row (ubah nama)
5. Dashboard: Preview "siswa" → Select all → Import
6. Database: Verify data updated
7. Dashboard: Check history → 2 entries (Push + Pull)
```

### Scenario 3: Multiple Modules
```
1. Push "siswa" → Verify
2. Push "guru" → Verify
3. Push "kelas" → Verify
4. Check history → 3 entries masing-masing "success"
```

### Scenario 4: Error Handling
```
1. Setup dengan spreadsheet ID salah → Test connection = Failed ✓
2. Setup dengan JSON tidak valid → Upload = Failed ✓
3. Setup dengan service account tidak punya akses → Test connection = Failed ✓
4. All scenarios harus menampilkan error message yang jelas
```

---

## 🎯 Expected Behaviors

### Setup Phase ✓
- [ ] JSON upload hanya terima valid service account JSON
- [ ] Spreadsheet ID harus diisi (tidak boleh kosong)
- [ ] Test connection harus success sebelum bisa save
- [ ] Success message menampilkan setelah save
- [ ] Dashboard refresh → status = "Connected"

### Push Phase ✓
- [ ] Button "⬆️ Push" hanya muncul setelah setup
- [ ] Click push → show confirm dialog
- [ ] Dialog confirm → "Push siswa data to Google Sheets?"
- [ ] After confirm → Show toast "Sync job queued"
- [ ] After 3-5 sec → Sync history update
- [ ] Google Sheets → data ada/updated

### Pull Phase ✓
- [ ] Button "⬇️ Preview" hanya muncul setelah setup
- [ ] Click preview → open modal dengan data
- [ ] Modal show: kolom headers + preview rows (max 50)
- [ ] Ada checkbox untuk select rows
- [ ] "Import Selected Data" button → confirm
- [ ] After confirm → Job queued
- [ ] After 3-5 sec → Sync history update
- [ ] Database → data ada/updated

### History Phase ✓
- [ ] Recent Sync History show minimal 10 entries
- [ ] Setiap entry show: module, direction, rows, status, time
- [ ] Status: "success" = hijau, "failed" = merah
- [ ] Time format: human-readable (e.g. "5 minutes ago")

---

## 🔧 Debugging Commands

### Quick Verify
```bash
# Terminal - Verify setup
php artisan tinker
>>> config('google-sheets.enabled')              # true/false
>>> config('google-sheets.modules')|keys()       # 10 modules
>>> file_exists(config('google-sheets.credentials_path'))  # true/false
>>> App\Models\GoogleSheetsSyncLog::count()      # number of logs
```

### Check Last Sync
```bash
php artisan tinker
>>> $log = App\Models\GoogleSheetsSyncLog::latest()->first();
>>> echo "Module: {$log->module}, Status: {$log->status}";
>>> echo $log->error_message;  # jika ada error
```

### Manual Trigger Job
```bash
php artisan tinker
>>> dispatch(new \App\Jobs\SyncModuleToSheet('siswa', 'push'));
>>> # Queue worker akan langsung process
```

### Verify Queue Processing
```bash
php artisan queue:monitor
# Output: "1 job on the default queue." atau "No jobs waiting."
```

---

## ✅ Acceptance Criteria

### Phase Selesai jika:
- [ ] Admin bisa login & akses menu
- [ ] Setup wizard bisa dijalankan end-to-end
- [ ] Test connection berhasil dengan Google Sheets
- [ ] Manual push berhasil ke Google Sheets
- [ ] Manual pull berhasil dari Google Sheets
- [ ] Sync history ter-record dengan benar
- [ ] Queue worker memproses job tanpa error
- [ ] Dashboard menampilkan status dengan akurat
- [ ] Error handling menampilkan pesan yang jelas
- [ ] Tidak ada error di application logs

---

## 📱 Mobile Testing (Optional)

Google Sheets Dashboard tidak responsive untuk mobile, tapi bisa:
- Test via phone browser sama logic-nya
- URL: `http://[ip-server]:8000/admin/google-sheets`

---

## 🎬 Screen Recording untuk Testing

Jika mau bikin video testing, record:
1. Setup wizard (4 steps)
2. Manual push (show sync happening)
3. Verify di Google Sheets
4. Manual pull (show preview)
5. Verify di database
6. Final sync history

---

**Siap testing? Mulai dari Step 1! 🚀**
