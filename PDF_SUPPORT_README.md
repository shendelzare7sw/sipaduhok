# 📄 PDF Support untuk AI Grading

## ✨ Fitur Baru

Sistem AI Grading sekarang mendukung analisis file **PDF** (Digital & Scanned) untuk tugas siswa!

### Cara Kerja

1. **PDF Digital** (Siswa ketik di Word → Save as PDF)
   - AI akan extract teks dari PDF
   - Teks dikirim ke Qwen 2.5 untuk grading
   - Feedback otomatis dalam Bahasa Indonesia

2. **PDF Scanned** (Foto tulisan tangan → PDF)
   - Halaman pertama PDF di-convert ke gambar (JPG)
   - Gambar dianalisis pakai Vision AI (Llama 4 Scout)
   - Dapat membaca tulisan tangan dan diagram

---

## 🛠️ System Requirements

### Dependencies yang Diinstall

✅ **Sudah terinstall via Composer:**
```bash
composer require spatie/pdf-to-text spatie/pdf-to-image
```

**Package Versions:**
- `spatie/pdf-to-text` v1.54.1
- `spatie/pdf-to-image` v1.2.2

---

## 🖥️ Setup untuk Laragon (Windows - Local Testing)

### Quick Start (Recommended)

#### 1. **pdftotext** (untuk PDF Digital)

Download **Xpdf Tools for Windows**:
1. Visit: https://www.xpdfreader.com/download.html
2. Download "Xpdf tools" (Windows 64-bit)
3. Extract ke folder: `C:\laragon\bin\xpdf`
4. **Tambahkan ke PATH**:
   - Open: Control Panel → System → Advanced → Environment Variables
   - Edit "Path" di System Variables
   - Add: `C:\laragon\bin\xpdf\bin64`
   - Klik OK

**Test Installation:**
```bash
# Buka Command Prompt / Git Bash
pdftotext -v
# Output: pdftotext version 4.04
```

#### 2. **ImageMagick** (untuk PDF Scanned)

Download **ImageMagick for Windows**:
1. Visit: https://imagemagick.org/script/download.php#windows
2. Download "ImageMagick-...-Q16-HDRI-x64-dll.exe"
3. **Install dengan options**:
   - ✅ Check: "Add application directory to PATH"
   - ✅ Check: "Install legacy utilities (e.g. convert)"
   - ✅ Check: "Install Ghostscript"
4. Restart terminal

**Test Installation:**
```bash
# Test ImageMagick
convert -version
# Output: Version: ImageMagick 7.x.x

# Test Ghostscript
gs -version
# Output: GPL Ghostscript 10.x.x
```

---

## 🐧 Setup untuk Linux/Ubuntu (Production Server)

```bash
# Install semua dependencies
sudo apt-get update
sudo apt-get install poppler-utils imagemagick ghostscript

# Test
pdftotext -v
convert -version
```

---

## 🍎 Setup untuk macOS

```bash
# Install via Homebrew
brew install poppler imagemagick ghostscript

# Test
pdftotext -v
convert -version
```

---

## 🧪 Testing

### Test PDF Digital

1. Upload file PDF yang dibuat dari Word/Google Docs
2. Klik "Analisis AI" di halaman koreksi
3. AI akan extract text dan memberi nilai

### Test PDF Scanned

1. Upload file PDF yang berisi scan/foto tulisan tangan
2. Klik "Analisis AI"
3. AI Vision akan analyze gambar dan memberi feedback

---

## 📊 Supported File Types

| Type | Format | AI Method | Status |
|------|--------|-----------|--------|
| Image | JPG, PNG, WEBP | Vision AI (Llama 4 Scout) | ✅ Ready |
| PDF Digital | PDF (with text) | Text Extraction → Qwen 2.5 | ✅ Ready |
| PDF Scanned | PDF (image-based) | Convert to Image → Vision AI | ✅ Ready |
| Text | Plain text input | Qwen 2.5 Text Grading | ✅ Ready |

---

## ⚠️ Limitations

1. **PDF Multi-Page**: Hanya halaman pertama yang dianalisis untuk scanned PDF
2. **File Size**: Maksimal 10MB (sesuai validation di controller)
3. **Image Resolution**: PDF di-convert ke 150 DPI (balance quality vs speed)
4. **Tulisan Tangan**: Harus jelas dan terbaca untuk hasil optimal

---

## 🔧 Troubleshooting

### ❌ Error: "pdftotext not found"

**Windows/Laragon:**
1. Check apakah Xpdf sudah diinstall: `where pdftotext`
2. Jika tidak found, install Xpdf tools (lihat setup guide di atas)
3. Pastikan PATH sudah ditambahkan dan restart terminal

**Linux:**
```bash
sudo apt-get install poppler-utils
```

---

### ❌ Error: "Failed to convert PDF to image"

**Windows/Laragon:**
1. Check ImageMagick: `where convert`
2. Check Ghostscript: `where gs`
3. Jika tidak found, reinstall ImageMagick dengan Ghostscript included

**Linux:**
```bash
sudo apt-get install imagemagick ghostscript
```

---

### ❌ Error: "No text extracted from PDF"

**Solution**: PDF kemungkinan scanned/image-based. Sistem akan **auto-fallback** ke Vision AI:
1. PDF di-convert ke image (JPG)
2. Vision AI (Llama 4 Scout) analyze gambar
3. Beri nilai & feedback

**Ini NORMAL behavior** untuk PDF scan!

---

### ⚠️ PDF Processing di Laragon Sangat Lambat

**Penyebab**: ImageMagick convert PDF besar memakan waktu.

**Solution**:
1. Minta siswa upload file < 2MB
2. Atau minta siswa compile PDF ke 1 halaman
3. Server production dengan SSD lebih cepat

---

### 🧪 Test Manual di Laragon

**Test PDF Text Extraction:**
```bash
cd c:\laragon\www\sipaduhok\storage\app\public
pdftotext sample.pdf output.txt
cat output.txt
```

**Test PDF to Image Conversion:**
```bash
cd c:\laragon\www\sipaduhok\storage\app\public
convert -density 150 sample.pdf[0] output.jpg
```

Jika kedua command berhasil → PDF support ready!

---

## 📝 Code Changes

### Files Modified

1. **GuruKoreksiController.php**
   - Added PDF detection logic
   - Text extraction for digital PDF
   - Image conversion for scanned PDF

2. **koreksi-show.blade.php**
   - Updated button label "Analisis AI"
   - Added info text: supports Image, PDF, Text

### Logic Flow

```
File Upload → Check MIME Type:
├── image/* → Vision AI
├── application/pdf → Try extract text:
│   ├── Text found (>10 chars) → Text Grading
│   └── No text → Convert to image → Vision AI
└── Fallback → jawaban_text → Text Grading
```

---

## 🎉 Benefits

- ✅ Guru tidak perlu manual extract PDF
- ✅ Support berbagai format submission
- ✅ Auto-detect digital vs scanned PDF
- ✅ Seamless UX (1 button untuk semua format)
- ✅ Qwen 2.5 excellent untuk Bahasa Indonesia

---

**Last Updated**: 2026-02-13
**Version**: 2.0 (Qwen + PDF Support)
