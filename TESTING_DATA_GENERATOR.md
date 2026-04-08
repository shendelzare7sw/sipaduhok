# 🧪 Testing Data Generator - Contoh Data untuk Testing

Gunakan script ini untuk membuat contoh data di database untuk testing.

## 1️⃣ Generate Test Data (via Tinker)

### A. Generate Admin User
```bash
php artisan tinker

# Admin User
$admin = new App\Models\User;
$admin->name = 'Admin Testing';
$admin->email = 'admin@test.com';
$admin->password = bcrypt('password123');
$admin->role = 'admin';
$admin->status = 'active';
$admin->email_verified_at = now();
$admin->save();

echo "Admin created: {$admin->email}";
exit
```

### B. Generate Siswa (Students)
```bash
php artisan tinker

# Generate 10 siswa
for ($i = 1; $i <= 10; $i++) {
    $siswa = new App\Models\Siswa;
    $siswa->nis = '2024'.str_pad($i, 5, '0', STR_PAD_LEFT);
    $siswa->nama = 'Siswa Testing '.$i;
    $siswa->gender = ($i % 2) ? 'L' : 'P';
    $siswa->tempat_lahir = 'Jakarta';
    $siswa->tanggal_lahir = now()->subYears(15 + ($i % 3));
    $siswa->alamat = "Jalan Testing No. ".$i;
    $siswa->status = 'active';
    $siswa->tahun_ajaran = now()->year;
    $siswa->save();
}

echo "10 siswa created";
exit
```

### C. Generate Guru (Teachers)
```bash
php artisan tinker

# Generate 5 guru
for ($i = 1; $i <= 5; $i++) {
    $guru = new App\Models\TenagaPendidik;
    $guru->nuptk = '123456789'.str_pad($i, 8, '0', STR_PAD_LEFT);
    $guru->nama = 'Guru Testing '.$i;
    $guru->gender = ($i % 2) ? 'L' : 'P';
    $guru->email = 'guru'.$i.'@test.com';
    $guru->no_telepon = '081234567'.str_pad($i, 3, '0', STR_PAD_LEFT);
    $guru->status = 'active';
    $guru->save();
}

echo "5 guru created";
exit
```

### D. Generate Kelas (Classes)
```bash
php artisan tinker

# Generate 3 kelas
$klasess = ['X-A', 'X-B', 'XI-A'];
foreach ($klasess as $kelas_name) {
    $kelas = new App\Models\Kelas;
    $kelas->kode_kelas = 'KLS-'.str_replace('-', '', $kelas_name);
    $kelas->nama_kelas = 'Kelas '.$kelas_name;
    $kelas->tingkat = substr($kelas_name, 0, 2);
    $kelas->jumlah_siswa = 30 + rand(0, 10);
    $kelas->save();
}

echo "3 kelas created";
exit
```

### E. Generate Mata Pelajaran (Subjects)
```bash
php artisan tinker

# Generate 5 mapel
$subjects = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS'];
foreach ($subjects as $subject) {
    $mapel = new App\Models\MataPelajaran;
    $mapel->kode_mapel = 'MP-'.strtoupper(substr($subject, 0, 3));
    $mapel->nama_mapel = $subject;
    $mapel->kkm = 70;
    $mapel->jam_pelajaran = 2 + rand(0, 2);
    $mapel->save();
}

echo "5 mapel created";
exit
```

### F. Generate Nilai (Grades)
```bash
php artisan tinker

# Get data yang sudah dibuat
$siswas = App\Models\Siswa::limit(10)->get();
$mapels = App\Models\MataPelajaran::limit(5)->get();
$semester = 1;

# Generate nilai
foreach ($siswas as $siswa) {
    foreach ($mapels as $mapel) {
        $nilai = new App\Models\Nilai;
        $nilai->siswa_id = $siswa->id;
        $nilai->mapel_id = $mapel->id;
        $nilai->semester = $semester;
        $nilai->nilai_pengetahuan = 70 + rand(-10, 30);
        $nilai->nilai_keterampilan = 75 + rand(-10, 25);
        $nilai->nilai_sikap = 'A';
        $nilai->save();
    }
}

echo "50 nilai created";
exit
```

### G. Generate Presensi (Attendance)
```bash
php artisan tinker

# Get data
$siswas = App\Models\Siswa::limit(10)->get();

# Generate presensi untuk 5 hari terakhir
for ($day = 0; $day < 5; $day++) {
    $date = now()->subDays($day);
    foreach ($siswas as $siswa) {
        $presensi = new App\Models\Presensi;
        $presensi->siswa_id = $siswa->id;
        $presensi->tanggal = $date;
        $presensi->status = rand(0, 10) > 7 ? 'absent' : 'present'; // 20% absent
        $presensi->keterangan = '';
        $presensi->save();
    }
}

echo "50 presensi created";
exit
```

### H. Generate Tagihan (Invoices)
```bash
php artisan tinker

# Get siswa
$siswas = App\Models\Siswa::limit(10)->get();

# Generate tagihan
foreach ($siswas as $siswa) {
    $tagihan = new App\Models\Tagihan;
    $tagihan->siswa_id = $siswa->id;
    $tagihan->nominal = 1000000 + rand(-100000, 200000);
    $tagihan->keterangan = 'SPP Bulan '.now()->format('F Y');
    $tagihan->jatuh_tempo = now()->addDays(30);
    $tagihan->status = rand(0, 10) > 5 ? 'paid' : 'pending';
    $tagihan->save();
}

echo "10 tagihan created";
exit
```

### I. Generate Pembayaran (Payments)
```bash
php artisan tinker

# Get paid tagiha
$tagihs = App\Models\Tagihan::where('status', 'paid')->limit(5)->get();

# Generate pembayaran
foreach ($tagihs as $tagih) {
    $bayar = new App\Models\Pembayaran;
    $bayar->siswa_id = $tagih->siswa_id;
    $bayar->tagihan_id = $tagih->id;
    $bayar->nominal = $tagih->nominal;
    $bayar->tanggal_bayar = now()->subDays(rand(1, 10));
    $bayar->metode = ['cash', 'transfer', 'check'][rand(0, 2)];
    $bayar->keterangan = 'Pembayaran SPP';
    $bayar->status = 'confirmed';
    $bayar->save();
}

echo "5 pembayaran created";
exit
```

---

## 2️⃣ Run All at Once (Script Mode)

Buat file `generate-test-data.php` di root project:

```php
<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create Admin
$admin = new \App\Models\User;
$admin->fill([
    'name' => 'Admin Testing',
    'email' => 'admin@test.com',
    'password' => bcrypt('password123'),
    'role' => 'admin',
    'status' => 'active',
    'email_verified_at' => now(),
])->save();

// Create Siswa
for ($i = 1; $i <= 10; $i++) {
    \App\Models\Siswa::create([
        'nis' => '2024'.str_pad($i, 5, '0', STR_PAD_LEFT),
        'nama' => 'Siswa Testing '.$i,
        'gender' => ($i % 2) ? 'L' : 'P',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => now()->subYears(15),
        'alamat' => "Jalan Testing No. $i",
        'status' => 'active',
        'tahun_ajaran' => date('Y'),
    ]);
}

echo "✓ Test data generated successfully!\n";
echo "Admin: admin@test.com / password123\n";
```

Run:
```bash
php generate-test-data.php
```

---

## 3️⃣ Data Summary untuk Testing

Setelah generate, akan ada:

| Tabel | Jumlah | Keterangan |
|-------|--------|-----------|
| users (admin) | 1 | Login user untuk testing |
| siswa | 10 | Student data untuk push |
| guru | 5 | Teacher data untuk push |
| kelas | 3 | Class data untuk push |
| mata_pelajaran | 5 | Subject reference |
| nilai | 50 | Grades untuk 10 siswa × 5 mapel |
| presensi | 50 | Attendance untuk 5 hari |
| tagihan | 10 | Invoice untuk push (Tier 2) |
| pembayaran | 5 | Payment records untuk push (Tier 2) |

---

## 4️⃣ Testing Workflow dengan Data Ini

### Step 1: Generate Data
```bash
php artisan tinker
# Copy-paste setiap section (A-I) satu per satu
```

### Step 2: Verify Data Ada
```bash
php artisan tinker
>>> App\Models\Siswa::count()           # 10
>>> App\Models\TenagaPendidik::count()  # 5
>>> App\Models\Nilai::count()           # 50
>>> App\Models\Presensi::count()        # 50
```

### Step 3: Login & Setup
```
http://localhost:8000/admin
Email: admin@test.com
Password: password123
```

### Step 4: Setup Google Sheets
- Go to /admin/google-sheets
- Upload JSON
- Enter Spreadsheet ID
- Test connection → Save

### Step 5: Test Push (Tier 1)
```
Dashboard → siswa card → "⬆️ Push"
Wait → Verify di Google Sheets
```

### Step 6: Test Push (Tier 2)
```
Dashboard → tagihan card → "⬆️ Push"
Dashboard → pembayaran card → "⬆️ Push"
```

### Step 7: Verify History
```
Recent Sync History harus menampilkan:
- siswa | Push | 10 rows | success
- tagihan | Push | 10 rows | success
- pembayaran | Push | 5 rows | success
```

---

## 5️⃣ Cleanup (Reset Data)

Jika ingin reset dan testing ulang:

```bash
# Reset database (hati-hati! semua data hilang)
php artisan migrate:reset

# Re-migrate
php artisan migrate

# Generate data lagi
php artisan tinker
# Repeat generate steps
```

Atau selective delete:

```bash
php artisan tinker

# Delete specific module
>>> App\Models\Siswa::truncate();
>>> App\Models\Tagihan::truncate();
>>> App\Models\Pembayaran::truncate();

exit
```

---

## 📊 Google Sheets Verification

Setelah push, di Google Sheets akan terlihat:

### Sheet "siswa"
```
| NIS | Nama | Gender | Alamat | Status |
|-----|------|--------|--------|--------|
| 2024... | Siswa Testing 1 | L | Jalan Testing No. 1 | active |
| 2024... | Siswa Testing 2 | P | Jalan Testing No. 2 | active |
...
```

### Sheet "guru"
```
| NUPTK | Nama | Gender | Email | Telepon |
|-------|------|--------|-------|---------|
| 123... | Guru Testing 1 | L | guru1@test.com | 0812345671 |
...
```

### Sheet "nilai"
```
| SiswaID | MapelID | Semester | Pengetahuan | Keterampilan | Sikap |
|---------|---------|----------|-------------|--------------|-------|
| 1 | 1 | 1 | 85 | 88 | A |
...
```

---

**Happy Testing! 🧪**
