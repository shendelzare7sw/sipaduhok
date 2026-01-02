# SIPADUHOK - Deployment Guide

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (for frontend assets)

## 🚀 Fresh Installation Steps

### 1. Clone Repository

```bash
git clone <repository-url>
cd sipaduhok
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipaduhok
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations

```bash
php artisan migrate --force
```

**Migration Order (Automatic):**
1. Core Laravel tables (users, cache, jobs)
2. Master data (cabang, tahun_ajaran, roles)
3. Academic data (tenaga_pendidik, mata_pelajaran, kelas, siswa)
4. Operational data (presensi, tagihan, pembayaran, nilai, rapor)
5. LMS tables (materi, tugas, ujian)
6. Additional features (jadwal_pelajaran, kalender_akademik, etc.)

### 5. Seed Database

```bash
php artisan db:seed --force
```

**Seeder Order:**
1. `RoleSeeder` - Creates default roles
2. `SuperAdminSeeder` - Creates 2 admin accounts
3. `CabangSeeder` - Creates branches (Gedung Utama, Cimanggis)
4. `TahunAjaranSeeder` - Creates academic years
5. `UserSeeder` - Creates all users (admin, teachers, staff)
6. `TenagaPendidikSeeder` - Creates teacher profiles
7. `MataPelajaranSeeder` - Creates subjects (PAUD, KB, TK, SD, SMP, SMA)
8. `KelasSeeder` - Creates classes for all levels
9. `SiswaSeeder` - Creates 198 students + parent accounts

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Storage Setup

```bash
php artisan storage:link
```

### 8. Start Development Server

```bash
php artisan serve
```

Application will be available at: `http://localhost:8000`

---

## 👤 Default Login Credentials

### Admin Accounts
```
Email: admin1@sipaduhok.com
Password: password

Email: admin2@sipaduhok.com
Password: password
```

### Teacher/Staff Accounts
**Wali Kelas:**
```
Email: wali.hendrakusuma@sipaduhok.com
Password: password123
```

**Guru Pengajar:**
```
Email: guru.bambangsutrisno@sipaduhok.com
Password: password123
```

**Bendahara:**
```
Email: bendahara@sipaduhok.com
Password: password123
```

**Sekretaris:**
```
Email: sekretaris@sipaduhok.com
Password: password123
```

**Ketua PKBM:**
```
Email: ketua@sipaduhok.com
Password: password123
```

### Student Accounts
```
Username: ahmad1, fatimah1, etc.
Password: password123
```

### Parent Accounts
```
Username: ortu1, ortu2, etc.
Password: password123
```

---

## 🔧 Maintenance Commands

### Fresh Database Reset
```bash
php artisan db:wipe --force
php artisan migrate --force
php artisan db:seed --force
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📊 Database Statistics

After seeding, you will have:
- **Users:** ~230 total accounts
- **Students:** 198 (18 KB + 18 TKA + 18 TKB + 72 SD + 47 SMP + 25 SMA)
- **Parents:** 198 linked accounts
- **Teachers:** 25 (12 wali kelas + 13 guru pengajar)
- **Staff:** 3 (Sekretaris, Bendahara, Ketua PKBM)
- **Classes:** 33 classes across all levels
- **Subjects:** 40+ subjects for all jenjang

---

## ⚠️ IMPORTANT NOTES

1. **Change Default Passwords** in production!
2. **Update .env** with production database credentials
3. **Configure APP_ENV** to `production`
4. **Set APP_DEBUG** to `false` in production
5. **Update APP_URL** to your production domain

---

## 🗑️ Cleanup Before Production

Remove development-only files:
```bash
# Remove Fix seeders (only for development)
rm database/seeders/Fix*.php
rm database/seeders/IstirahatMataPelajaranSeeder.php
```

---

## 📝 Notes

- All timestamps use Indonesia timezone (Asia/Jakarta)
- Default role system is active
- Username format for teachers: `wali.namaguru@sipaduhok.com` (not tied to jenjang)
- Font Awesome 6.5.1 is used for icons (CDN loaded on standalone pages)
- No Windows emoji icons - all replaced with Font Awesome

---

## 🆘 Troubleshooting

### Migration Error
```bash
# Check migration status
php artisan migrate:status

# Rollback and retry
php artisan migrate:rollback
php artisan migrate --force
```

### Seeder Error
```bash
# Truncate specific tables
php artisan db:wipe --force

# Re-run migrations and seeders
php artisan migrate --force
php artisan db:seed --force
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

---

**Version:** 1.0.0
**Last Updated:** January 2, 2026
**Maintained by:** Development Team
