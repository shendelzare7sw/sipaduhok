# Fitur Import Nilai dari Excel

Fitur ini memungkinkan guru untuk mengimpor nilai siswa dari file Excel secara massal.

## Cara Penggunaan

### 1. Download Template Excel
- Buka halaman Nilai Siswa (Guru LMS)
- Klik dropdown "Excel" di bagian kanan atas
- Pilih "Download Template"
- Template Excel akan terdownload dengan data siswa yang sudah terisi (Nama, NIS/NISN)

### 2. Isi Nilai di Template
- Buka file template yang sudah didownload
- Isi nilai pada kolom yang tersedia:
  - Tugas 1-5
  - Latihan 1-5
  - UH (Ulangan Harian) 1-5
  - PTS (Penilaian Tengah Semester)
  - PAS (Penilaian Akhir Semester)
  - TO 1-3, UPK, Ujian Praktek (khusus kelas 9/12)
- **Jangan ubah kolom:** No, Nama Siswa, NIS/NISN
- **Format nilai:** Angka 0-100 (bisa desimal, contoh: 87.5)
- **Kosongkan sel** jika tidak ingin mengubah nilai yang sudah ada
- **Isi dengan nilai baru** untuk menimpa nilai yang sudah tersimpan
- Simpan file Excel

### 3. Import Nilai
- Klik dropdown "Excel" di halaman Nilai Siswa
- Pilih "Import Nilai"
- Upload file Excel yang sudah diisi
- Klik "Import Nilai"
- Sistem akan memproses dan menampilkan hasil import

## Fitur Import

### File yang Dibuat
1. **NilaiSiswaImport.php** - Class untuk memproses import
   - Path: `app/Imports/Guru/NilaiSiswaImport.php`
   - Fitur: Validasi data, error handling, auto-calculate rata-rata

2. **NilaiSiswaTemplateExport.php** - Class untuk generate template
   - Path: `app/Exports/Guru/NilaiSiswaTemplateExport.php`
   - Fitur: Template dengan styling, freeze panes, instruksi

3. **Controller Methods**
   - `downloadTemplate()` - Download template Excel
   - `importExcel()` - Import nilai dari Excel

4. **Routes**
   - `GET /nilai/download-template` - Download template
   - `POST /nilai/import-excel` - Import nilai

### Validasi
- File harus format .xlsx atau .xls
- Maksimal ukuran file: 5MB
- Nilai harus antara 0-100
- NIS/NISN harus valid dan terdaftar di kelas

### Error Handling
- Jika siswa tidak ditemukan, baris akan dilewati
- Jika validasi gagal, akan ditampilkan error
- Import tetap dilanjutkan untuk baris yang valid

## Catatan Penting

### 🎯 Smart Import (Fitur Cerdas!)
Fitur ini menggunakan **Smart Import** yang melindungi data Anda:
- ✅ **Nilai yang diisi di Excel** → Akan menimpa nilai lama
- ✅ **Nilai yang kosong di Excel** → Tetap menggunakan nilai lama (tidak tertimpa)
- ✅ **Import sebagian siswa** → Siswa yang tidak ada di Excel tidak terpengaruh

### 📚 Contoh Skenario Penggunaan:

**Skenario 1: Menambah nilai siswa baru**
- Guru sudah input nilai Siswa A dan B secara manual di halaman
- Download template, isi nilai hanya untuk Siswa C dan D
- Kosongkan kolom nilai untuk Siswa A dan B (atau tidak usah isi)
- Import → ✅ Nilai Siswa A dan B tetap aman, Siswa C dan D ditambahkan

**Skenario 2: Update nilai tertentu saja**
- Guru sudah input semua siswa dengan nilai lengkap
- Download template, ubah hanya nilai Tugas 1 untuk Siswa A
- Kosongkan kolom Tugas 2-5, Latihan, UH, dll untuk Siswa A
- Import → ✅ Hanya Tugas 1 Siswa A yang berubah, nilai lainnya tetap

**Skenario 3: Import massal (timpa semua)**
- Download template, isi semua nilai untuk semua siswa
- Import → ⚠️ Semua nilai di Excel akan menimpa nilai lama

### 📝 Catatan Teknis
- Rata-rata dan nilai akhir akan **otomatis dihitung** setelah import
- Pastikan file template tidak diubah strukturnya (kolom header)
- Validasi otomatis: nilai harus 0-100, format file .xlsx/.xls
- Jika ada error, sistem akan tetap mengimpor data yang valid

## UI Changes
- Button "Export Excel" diubah menjadi dropdown "Excel"
- Dropdown berisi:
  - Export Nilai (export nilai saat ini)
  - Download Template (template kosong untuk import)
  - Import Nilai (upload file Excel)

