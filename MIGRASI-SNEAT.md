# 📚 Panduan Migrasi dari SB Admin 2 ke Sneat Bootstrap 5

## ✅ Fase 1: Setup & Layout Migration - SELESAI

### File Yang Sudah Dibuat

1. **Layout Utama Sneat**
   - File: `resources/views/layouts/sneat.blade.php`
   - Deskripsi: Layout dasar dengan Bootstrap 5, warna brand SIPADUHOK (#165fac), dan semua fitur utama

2. **Sidebar Sneat**
   - File: `resources/views/partials/sneat-sidebar.blade.php`
   - Deskripsi: Sidebar/menu vertikal dengan logo SIPADUHOK dan navigasi yang dapat dikustomisasi

3. **Navbar Sneat**
   - File: `resources/views/partials/sneat-navbar.blade.php`
   - Deskripsi: Top navigation bar dengan notifikasi, user menu, dan modal logout

4. **Contoh Dashboard**
   - File: `resources/views/dashboard/sneat-example.blade.php`
   - Deskripsi: Contoh halaman dashboard lengkap dengan stats cards dan tabel

---

## 🎨 Fitur Yang Sudah Ter-implementasi

### ✅ Branding SIPADUHOK
- Warna primary: `#165fac` (biru khas SIPADUHOK)
- Warna dark: `#0d3a6b`
- Logo sudah terintegrasi di sidebar
- Font: Public Sans (modern & clean)

### ✅ Komponen UI
- Flash messages (success, error, warning, info)
- Notification dropdown dengan badge counter
- User profile dropdown
- Logout modal dengan konfirmasi
- Scroll to top button
- Card hover effects
- Auto-dismiss alerts (5 detik)

### ✅ Bootstrap 5 Compatible
- Semua utility classes menggunakan BS5:
  - `ms-*` / `me-*` (bukan `ml-*` / `mr-*`)
  - `text-start` (bukan `text-left`)
  - `btn-close` (bukan `close`)
  - `data-bs-*` attributes

### ✅ Responsive Design
- Mobile friendly
- Sidebar collapse otomatis di mobile
- Touch-friendly navigation

---

## 🚀 Cara Menggunakan Layout Baru

### 1. Test Layout Sneat

Buat route test di `routes/web.php`:

```php
Route::get('/test-sneat', function () {
    return view('dashboard.sneat-example');
})->middleware('auth')->name('test.sneat');
```

Akses: `http://localhost/sipaduhok/test-sneat`

### 2. Migrasi View Lama ke Sneat

**SEBELUM (SB Admin 2):**
```blade
@extends('layouts.dashboard')

@section('sidebar-menu')
    <!-- Menu items -->
@endsection

@section('content')
    <!-- Content -->
@endsection
```

**SESUDAH (Sneat):**
```blade
@extends('layouts.sneat')

@section('sidebar-menu')
    <li class="menu-item active">
        <a href="#" class="menu-link">
            <i class="menu-icon fas fa-home"></i>
            <div>Dashboard</div>
        </a>
    </li>
@endsection

@section('content')
    <!-- Content sama, hanya perlu update class Bootstrap 4 ke 5 -->
@endsection
```

### 3. Update Struktur Menu Sidebar

**Format menu Sneat:**

```blade
<!-- Menu Item Tunggal -->
<li class="menu-item active">
    <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

<!-- Menu Header -->
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Menu Utama</span>
</li>

<!-- Menu dengan Submenu -->
<li class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-users"></i>
        <div>Pengguna</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item">
            <a href="#" class="menu-link">
                <div>Daftar Pengguna</div>
            </a>
        </li>
    </ul>
</li>
```

---

## 📝 Panduan Update Bootstrap 4 ke Bootstrap 5

### Class yang Perlu Diubah:

| Bootstrap 4 | Bootstrap 5 | Keterangan |
|------------|-------------|------------|
| `ml-*` | `ms-*` | Margin left → start |
| `mr-*` | `me-*` | Margin right → end |
| `pl-*` | `ps-*` | Padding left → start |
| `pr-*` | `pe-*` | Padding right → end |
| `text-left` | `text-start` | Text align |
| `text-right` | `text-end` | Text align |
| `float-left` | `float-start` | Float |
| `float-right` | `float-end` | Float |
| `badge-*` | `bg-*` | Badge colors |
| `custom-select` | `form-select` | Select dropdown |
| `custom-checkbox` | `form-check` | Checkbox |
| `data-toggle` | `data-bs-toggle` | Data attributes |
| `data-target` | `data-bs-target` | Data attributes |
| `.close` | `.btn-close` | Close button |

### Contoh Update Alert:

**Bootstrap 4:**
```blade
<div class="alert alert-success alert-dismissible fade show">
    Message
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
```

**Bootstrap 5:**
```blade
<div class="alert alert-success alert-dismissible fade show">
    Message
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

---

## 🔧 Tools Helper untuk Migrasi

### Find & Replace di VSCode:

1. Tekan `Ctrl + Shift + H` (Find and Replace in Files)
2. Aktifkan Regex mode (icon `.*`)
3. Gunakan pattern ini:

**Ganti margin left/right:**
```
Find: \bml-(\d+)\b
Replace: ms-$1

Find: \bmr-(\d+)\b
Replace: me-$1
```

**Ganti data attributes:**
```
Find: data-toggle
Replace: data-bs-toggle

Find: data-target
Replace: data-bs-target

Find: data-dismiss
Replace: data-bs-dismiss
```

---

## 📋 Checklist Migrasi Per Role

### Admin
- [ ] Dashboard admin
- [ ] User management (tenaga pendidik)
- [ ] User management (siswa)
- [ ] Tahun ajaran
- [ ] Cabang
- [ ] Kelas (index, create, edit, manage siswa)
- [ ] Wali kelas
- [ ] Guru pengajar
- [ ] Manajemen siswa
- [ ] Cetak laporan

### Ketua PKBM
- [ ] Dashboard ketua
- [ ] Monitoring pengguna
- [ ] Monitoring wali kelas
- [ ] Monitoring guru
- [ ] Monitoring siswa
- [ ] Laporan
- [ ] Catatan

### Sekretaris
- [ ] Dashboard sekretaris
- [ ] Kalender akademik
- [ ] Pengumuman
- [ ] Flyer
- [ ] Berita

### Bendahara
- [ ] Dashboard bendahara
- [ ] Tagihan
- [ ] Pembayaran
- [ ] Info pembayaran
- [ ] Validasi akses
- [ ] Laporan pembayaran

### Wali Kelas
- [ ] Dashboard wali kelas
- [ ] Jadwal pelajaran
- [ ] Presensi
- [ ] Nilai siswa
- [ ] Rapor
- [ ] Validasi akses

### Guru Pengajar
- [ ] Dashboard guru
- [ ] Daftar kelas
- [ ] LMS - Materi
- [ ] LMS - Tugas
- [ ] LMS - Ujian
- [ ] Koreksi
- [ ] Nilai

### Siswa
- [ ] Dashboard siswa
- [ ] SIA - Presensi
- [ ] SIA - Pembayaran
- [ ] SIA - Rapor
- [ ] LMS - Dashboard
- [ ] LMS - Mata pelajaran
- [ ] LMS - Tugas
- [ ] LMS - Ujian

---

## 🎯 Next Steps - Fase 2

1. **Pilih salah satu role untuk migrasi pertama** (Rekomendasi: Admin Dashboard dulu)
2. **Copy file view yang akan dimigrasi**
3. **Ubah `@extends('layouts.dashboard')` menjadi `@extends('layouts.sneat')`**
4. **Update struktur sidebar menu**
5. **Replace Bootstrap 4 classes ke Bootstrap 5**
6. **Test fungsionalitas**

---

## 💡 Tips Migrasi

1. **Jangan hapus layout lama** - Simpan `layouts/dashboard.blade.php` sebagai backup
2. **Migrasi bertahap** - Satu role/section per waktu
3. **Test setiap perubahan** - Pastikan fungsi tidak rusak
4. **Gunakan Git** - Commit setiap progress migrasi
5. **Dokumentasi** - Catat perubahan yang dilakukan

---

## 📞 Troubleshooting

### Sidebar tidak muncul?
- Pastikan JavaScript Sneat sudah dimuat
- Cek console browser untuk error
- Pastikan menu items ada di `@section('sidebar-menu')`

### Style berantakan?
- Clear browser cache (Ctrl + Shift + R)
- Pastikan CDN Sneat bisa diakses
- Cek apakah ada conflict CSS custom

### Pagination error?
Laravel 11 sudah support Bootstrap 5 pagination by default. Tidak perlu konfigurasi tambahan!

---

## 📚 Referensi

- **Sneat Documentation**: https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/documentation/
- **Bootstrap 5 Migration Guide**: https://getbootstrap.com/docs/5.3/migration/
- **Laravel 11 Docs**: https://laravel.com/docs/11.x

---

**Status**: ✅ Fase 1 SELESAI - Siap untuk Fase 2 (Migrasi per Role)

**Dibuat**: {{ date('d F Y H:i') }}
**Versi**: 1.0
