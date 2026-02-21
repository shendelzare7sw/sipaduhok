# 🚀 SIPADUHOK - Setup Guide

Panduan lengkap untuk setup sistem SIPADUHOK setelah clone/pull dari GitHub.

---

## 📋 Prerequisites

Pastikan sistem Anda sudah terinstall:

- **PHP** >= 8.1
- **Composer** >= 2.0
- **MySQL** / **MariaDB** >= 5.7
- **Node.js** >= 16.x (jika ada frontend build)
- **Git**

---

## 🔧 Langkah Setup

### 1️⃣ Clone Repository (Jika Belum)

```bash
git clone https://github.com/[username]/sipaduhok.git
cd sipaduhok
```

Atau jika sudah clone, pull update terbaru:

```bash
git pull origin main
```

---

### 2️⃣ Install Dependencies PHP

```bash
composer install
```

**Troubleshooting:**
- Jika error "composer not found": Install Composer dari https://getcomposer.org/download/
- Jika error extension PHP: Install ekstensi yang diminta (biasanya `php-mbstring`, `php-xml`, `php-curl`)

---

### 3️⃣ Setup Environment File

Copy file `.env.example` menjadi `.env`:

```bash
# Windows (Command Prompt)
copy .env.example .env

# Windows (PowerShell)
Copy-Item .env.example .env

# Linux/Mac
cp .env.example .env
```

---

### 4️⃣ Generate Application Key

```bash
php artisan key:generate
```

**Output yang benar:**
```
Application key set successfully.
```

---

### 5️⃣ Konfigurasi Database

Edit file `.env` dan sesuaikan dengan database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipaduhok
DB_USERNAME=root
DB_PASSWORD=
```

**Buat database baru** di MySQL/phpMyAdmin:

```sql
CREATE DATABASE sipaduhok CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 6️⃣ Run Database Migration

```bash
php artisan migrate
```

**Jika diminta konfirmasi**, ketik `yes` dan Enter.

**Troubleshooting:**
- Error "Access denied": Cek username/password database di `.env`
- Error "Unknown database": Pastikan database sudah dibuat (step 5)
- Error "Syntax error": Pastikan versi MySQL >= 5.7

---

### 7️⃣ Seed Database (Opsional - Jika Ada Data Dummy)

```bash
php artisan db:seed
```

**Atau seed spesifik seeder:**

```bash
php artisan db:seed --class=UsersTableSeeder
php artisan db:seed --class=RolesTableSeeder
```

---

### 8️⃣ Create Storage Symlink

```bash
php artisan storage:link
```

**Output yang benar:**
```
The [public/storage] link has been connected to [storage/app/public].
```

---

### 9️⃣ Set File Permissions (Linux/Mac Only)

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Untuk Laragon (Windows):**
- Biasanya tidak perlu set permissions
- Jika error, restart Laragon sebagai Administrator

---

### 🔟 Konfigurasi AI Settings (PENTING!)

Edit file `.env` dan tambahkan API Keys untuk AI:

```env
# Groq API (Free - Recommended)
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# Gemini API (Free - untuk Vision & PDF)
GEMINI_API_KEY=AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Cara Dapatkan API Key:**

#### Groq API (Gratis):
1. Buka https://console.groq.com
2. Sign up / Login
3. Buat API Key di Dashboard
4. Copy dan paste ke `.env`

#### Gemini API (Gratis):
1. Buka https://aistudio.google.com/apikey
2. Login dengan Google Account
3. Create API Key
4. Copy dan paste ke `.env`

---

### 1️⃣1️⃣ Clear Cache (Jika Ada Error)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

### 1️⃣2️⃣ Run Development Server

```bash
# Menggunakan Laravel built-in server
php artisan serve

# Atau menggunakan Laragon (Windows)
# Akses via: http://sipaduhok.test
```

**Output:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

---

## 🎯 Akses Sistem

### Default Admin Login (Jika Ada Seeder)

```
Email: admin@sipaduhok.com
Password: password
```

**PENTING:** Ganti password default setelah login pertama kali!

---

## ⚙️ Konfigurasi AI di Admin Panel

Setelah login sebagai admin:

1. **Buka Menu:** Dashboard > Admin > AI Settings
2. **Setup API Keys:**
   - Groq API Key: Paste key dari step 10
   - Gemini API Key: Paste key dari step 10
3. **Pilih Provider:** Groq Cloud (Recommended - Fast & Free)
4. **Pilih Model Text:** Llama 3.3 70B (Fast)
5. **Pilih Model Vision:** Llama 4 Scout (untuk gambar) atau Gemini 2.5 Flash (untuk PDF)
6. **Test Connection:** Klik tombol "Test API" untuk verifikasi
7. **Save Settings**

---

## 🐛 Common Issues & Solutions

### Issue: "Composer install" gagal

**Solusi:**
```bash
# Update Composer dulu
composer self-update

# Coba lagi dengan verbose
composer install -vvv
```

### Issue: "Class not found" error

**Solusi:**
```bash
composer dump-autoload
```

### Issue: Migration gagal "SQLSTATE[42000]"

**Solusi:**
- Cek versi MySQL: `mysql --version` (harus >= 5.7)
- Pastikan database sudah dibuat
- Cek kredensial di `.env`

### Issue: "500 Internal Server Error"

**Solusi:**
```bash
# Check Laravel logs
cat storage/logs/laravel.log

# Set debug mode di .env
APP_DEBUG=true

# Clear semua cache
php artisan optimize:clear
```

### Issue: "Permission denied" (Linux)

**Solusi:**
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: Storage link tidak berfungsi

**Solusi:**
```bash
# Hapus link lama
rm public/storage

# Buat ulang
php artisan storage:link
```

### Issue: AI Chatbot tidak muncul

**Solusi:**
1. Pastikan API keys sudah di-setup di Admin > AI Settings
2. Clear cache: `php artisan config:clear`
3. Cek console browser (F12) untuk error JavaScript
4. Pastikan role Anda memiliki akses chatbot (diatur di AI Settings)

---

## 📦 Update dari GitHub

Jika ada update dari repository:

```bash
# 1. Pull update terbaru
git pull origin main

# 2. Update dependencies
composer install

# 3. Run migration baru (jika ada)
php artisan migrate

# 4. Clear cache
php artisan config:clear
php artisan cache:clear

# 5. Restart server
```

**PENTING:** Jangan jalankan `php artisan migrate:fresh` di production karena akan **HAPUS SEMUA DATA**!

---

## 🔐 Security Checklist (Production)

Sebelum deploy ke production:

- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Set `APP_ENV=production` di `.env`
- [ ] Ganti `APP_KEY` dengan key yang unik
- [ ] Ganti semua password default
- [ ] Setup SSL certificate (HTTPS)
- [ ] Backup database secara berkala
- [ ] Set permission yang benar untuk `storage/` dan `bootstrap/cache/`
- [ ] Disable directory listing di web server
- [ ] Setup firewall untuk database

---

## 📞 Support

Jika masih ada masalah:

1. Cek file log: `storage/logs/laravel.log`
2. Cek error di browser console (F12)
3. Buat issue di GitHub repository
4. Kontak developer team

---

## 📝 Catatan Tambahan

### Struktur Folder Penting

```
sipaduhok/
├── app/                  # Core aplikasi (Controllers, Models, Services)
├── config/               # File konfigurasi
├── database/             # Migrations & Seeders
├── public/               # File publik (CSS, JS, images)
│   └── js/
│       ├── ai-chatbot.js        # AI Chatbot frontend
│       └── ai-question-generator.js
├── resources/
│   └── views/            # Blade templates
├── routes/               # Route definitions
├── storage/              # File uploads & cache
├── .env                  # Environment variables (JANGAN commit!)
└── .env.example          # Template environment
```

### Fitur AI yang Tersedia

1. **AI Chatbot** - Asisten virtual untuk semua role
   - Models: Llama 3.3 70B, Llama 4 Scout, Gemini 2.5 Flash
   - Support: Text, Images, PDF

2. **AI Question Generator** - Generate soal ujian otomatis
   - Input: Mata pelajaran, topik, jumlah soal
   - Output: Soal + kunci jawaban

3. **AI Grading** - Koreksi essay otomatis
   - Support: Text dan Image (tulisan tangan)
   - Output: Nilai + feedback

---

**🎉 Selamat! Setup SIPADUHOK berhasil!**

Jika ada pertanyaan, gunakan AI Chatbot di sistem dengan klik icon chat di kanan bawah.
