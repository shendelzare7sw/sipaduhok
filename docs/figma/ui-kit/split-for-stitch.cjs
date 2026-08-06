/**
 * split-for-stitch.js
 * 
 * Pecah file HTML multi-layar (02-admin.html, 03-ketua.html, dll) menjadi
 * file individual per layar, siap upload ke Google Stitch.
 * 
 * Setiap file output:
 * - Self-contained (include CSS + JS inline-reference)
 * - 1 file = 1 layar lengkap (sidebar + navbar + content)
 * - Ada HTML comment berisi WIRING (button mana → layar mana)
 * 
 * USAGE: node split-for-stitch.js
 * OUTPUT: folder stitch/ berisi file per layar
 */

const fs = require('fs');
const path = require('path');

const SCREENS_DIR = path.join(__dirname, 'screens');
const OUT_DIR = path.join(__dirname, 'stitch');

// Wiring data: untuk setiap layar, button mana mengarah ke layar mana
const WIRING = {
    // === ADMIN ===
    'admin-01-dashboard': {
        buttons: [
            { element: 'Akses Cepat "Tambah Siswa"', target: 'admin-03-tambah-siswa' },
            { element: 'Sidebar "Siswa"', target: 'admin-02-data-siswa' },
            { element: 'Sidebar "Data Kelas"', target: 'admin-04-data-kelas' },
            { element: 'Sidebar "Jadwal Pelajaran"', target: 'admin-05-jadwal-pelajaran' },
            { element: 'Sidebar "Tagihan"', target: 'admin-06-tagihan' },
            { element: 'Sidebar "Laporan"', target: 'admin-07-laporan' },
            { element: 'Sidebar "Landing Page"', target: 'admin-08-cms-landing-page' },
        ]
    },
    'admin-02-data-siswa': {
        buttons: [
            { element: 'Tombol "Tambah Siswa"', target: 'admin-03-tambah-siswa' },
            { element: 'Tombol "Impor Excel"', target: 'modal overlay import' },
            { element: 'Klik baris siswa', target: 'detail/edit siswa' },
            { element: 'Sidebar "Dashboard"', target: 'admin-01-dashboard' },
        ]
    },
    'admin-03-tambah-siswa': {
        buttons: [
            { element: 'Tombol "Simpan Siswa"', target: 'admin-02-data-siswa (baris baru muncul, status Belum Berkelas)' },
            { element: 'Tombol "Batal"', target: 'admin-02-data-siswa' },
        ]
    },
    'admin-04-data-kelas': {
        buttons: [
            { element: 'Klik kartu kelas', target: 'detail kelas + daftar siswa' },
            { element: 'Tombol "Tambah Kelas"', target: 'modal form tambah kelas' },
        ]
    },
    'admin-05-jadwal-pelajaran': { buttons: [] },
    'admin-06-tagihan': { buttons: [] },
    'admin-07-laporan': { buttons: [] },
    'admin-08-cms-landing-page': { buttons: [] },

    // === KETUA ===
    'ketua-01-dashboard': {
        buttons: [
            { element: 'Kartu "Validasi Rapor — N menunggu"', target: 'ketua-02-validasi-rapor' },
            { element: 'Kartu "Dispensasi — N permintaan"', target: 'ketua-04-dispensasi-keuangan' },
            { element: 'Sidebar "Approval Dispensasi"', target: 'ketua-05-approval-kenaikan' },
            { element: 'Sidebar "Cetak Laporan"', target: 'ketua-06-cetak-laporan' },
        ]
    },
    'ketua-02-validasi-rapor': {
        buttons: [
            { element: 'Tombol "Pratinjau"', target: 'ketua-03-preview-rapor' },
            { element: 'Tombol "Validasi"', target: 'ketua-02-validasi-rapor (status → Tervalidasi)' },
            { element: 'Tombol "Minta Revisi"', target: 'ketua-02-validasi-rapor (status → Perlu Revisi)' },
        ]
    },
    'ketua-03-preview-rapor': {
        buttons: [
            { element: 'Tombol "Kembali"', target: 'ketua-02-validasi-rapor' },
            { element: 'Tombol "Validasi"', target: 'ketua-02-validasi-rapor (status → Tervalidasi)' },
            { element: 'Tombol "Minta Revisi"', target: 'ketua-02-validasi-rapor (status → Perlu Revisi)' },
        ]
    },
    'ketua-04-dispensasi-keuangan': { buttons: [] },
    'ketua-05-approval-kenaikan': { buttons: [] },
    'ketua-06-cetak-laporan': { buttons: [] },

    // === WAKA ===
    'waka-01-dashboard': {
        buttons: [
            { element: 'Sidebar "Data Kelas"', target: 'waka-02-data-kelas' },
            { element: 'Sidebar "Jadwal Pelajaran"', target: 'waka-03-jadwal-pelajaran' },
            { element: 'Sidebar "Pengaturan KKM"', target: 'waka-04-pengaturan-kkm' },
            { element: 'Sidebar "Siswa" (monitoring)', target: 'waka-05-monitoring-siswa' },
        ]
    },
    'waka-02-data-kelas': { buttons: [] },
    'waka-03-jadwal-pelajaran': { buttons: [] },
    'waka-04-pengaturan-kkm': { buttons: [] },
    'waka-05-monitoring-siswa': { buttons: [] },

    // === SEKRETARIS ===
    'sekretaris-01-dashboard': {
        buttons: [
            { element: 'Sidebar "Kelola Berita"', target: 'sekretaris-02-kelola-berita' },
            { element: 'Sidebar "Kalender Akademik"', target: 'sekretaris-04-kalender-akademik' },
        ]
    },
    'sekretaris-02-kelola-berita': {
        buttons: [
            { element: 'Tombol "Tulis Berita"', target: 'sekretaris-03-form-berita' },
        ]
    },
    'sekretaris-03-form-berita': {
        buttons: [
            { element: 'Tombol "Publikasikan"', target: 'sekretaris-02-kelola-berita (baris baru)' },
        ]
    },
    'sekretaris-04-kalender-akademik': { buttons: [] },

    // === BENDAHARA ===
    'bendahara-01-dashboard': {
        buttons: [
            { element: 'Widget "Menunggu Validasi"', target: 'bendahara-04-kelola-pembayaran' },
            { element: 'Sidebar "Kelola Tagihan"', target: 'bendahara-02-kelola-tagihan' },
            { element: 'Sidebar "Kelola Pembayaran"', target: 'bendahara-04-kelola-pembayaran' },
            { element: 'Sidebar "Laporan Pembayaran"', target: 'bendahara-06-laporan-pembayaran' },
            { element: 'Sidebar "Siswa Belum Lunas"', target: 'bendahara-07-siswa-belum-lunas' },
        ]
    },
    'bendahara-02-kelola-tagihan': {
        buttons: [
            { element: 'Klik baris siswa', target: 'bendahara-03-detail-tagihan-siswa' },
        ]
    },
    'bendahara-03-detail-tagihan-siswa': { buttons: [] },
    'bendahara-04-kelola-pembayaran': {
        buttons: [
            { element: 'Klik baris pembayaran', target: 'bendahara-05-validasi-pembayaran' },
        ]
    },
    'bendahara-05-validasi-pembayaran': {
        buttons: [
            { element: 'Tombol "Setujui Pembayaran"', target: 'bendahara-04-kelola-pembayaran (status → Disetujui)' },
            { element: 'Tombol "Tolak Pembayaran"', target: 'bendahara-04-kelola-pembayaran (status → Ditolak)' },
        ]
    },
    'bendahara-06-laporan-pembayaran': { buttons: [] },
    'bendahara-07-siswa-belum-lunas': { buttons: [] },

    // === WALI KELAS ===
    'wali-kelas-01-pilih-kelas': {
        buttons: [
            { element: 'Tombol "Kelola Kelas Ini"', target: 'wali-kelas-02-dashboard' },
        ]
    },
    'wali-kelas-02-dashboard': {
        buttons: [
            { element: 'Sidebar "Input Harian"', target: 'wali-kelas-03-input-presensi' },
            { element: 'Sidebar "Nilai Siswa" → klik siswa', target: 'wali-kelas-04-edit-nilai' },
            { element: 'Sidebar "Kelola Rapor"', target: 'wali-kelas-05-kelola-rapor' },
            { element: 'Sidebar "Permintaan Unduh"', target: 'wali-kelas-08-permintaan-unduh' },
            { element: 'Kartu Kelas Aktif "Ganti Kelas"', target: 'wali-kelas-01-pilih-kelas' },
        ]
    },
    'wali-kelas-03-input-presensi': {
        buttons: [
            { element: 'Tombol "Simpan Presensi"', target: 'tetap (success alert)' },
        ]
    },
    'wali-kelas-04-edit-nilai': {
        buttons: [
            { element: 'Tombol "Simpan Nilai"', target: 'kembali ke daftar siswa' },
        ]
    },
    'wali-kelas-05-kelola-rapor': {
        buttons: [
            { element: 'Tombol "Generate Semua Rapor"', target: 'tetap (baris muncul status Draft)' },
            { element: 'Aksi baris "Edit"', target: 'wali-kelas-06-edit-rapor' },
            { element: 'Aksi baris "Pratinjau"', target: 'wali-kelas-07-preview-rapor' },
            { element: 'Aksi baris "Kirim ke Ketua"', target: 'tetap (status → Menunggu Validasi)' },
            { element: 'Aksi baris "Terbitkan"', target: 'tetap (status → Terbit)' },
        ]
    },
    'wali-kelas-06-edit-rapor': {
        buttons: [
            { element: 'Tombol "Simpan"', target: 'wali-kelas-05-kelola-rapor' },
            { element: 'Tombol "Pratinjau"', target: 'wali-kelas-07-preview-rapor' },
        ]
    },
    'wali-kelas-07-preview-rapor': {
        buttons: [
            { element: 'Tombol "Kembali"', target: 'wali-kelas-05-kelola-rapor' },
            { element: 'Tombol "Kirim ke Ketua"', target: 'wali-kelas-05-kelola-rapor (status → Menunggu Validasi)' },
        ]
    },
    'wali-kelas-08-permintaan-unduh': {
        buttons: [
            { element: 'Tombol "Setujui"', target: 'tetap (link unduh tergenerate, valid 24 jam)' },
            { element: 'Baris Siti Nurhaliza', target: 'DISABLED — Tunggakan Rp 600.000 (gerbang keuangan)' },
        ]
    },

    // === GURU ===
    'guru-01-dashboard-sia': {
        buttons: [
            { element: 'Sidebar "Semua Kelas"', target: 'guru-02-semua-kelas' },
            { element: 'Quick link "Kelas 7A > Matematika"', target: 'guru-03-dashboard-lms ⚡SHELL BERUBAH KE LMS⚡' },
        ]
    },
    'guru-02-semua-kelas': {
        buttons: [
            { element: 'Klik kartu "Kelas 7A - Matematika"', target: 'guru-03-dashboard-lms ⚡SHELL BERUBAH KE LMS⚡' },
        ]
    },
    'guru-03-dashboard-lms': {
        buttons: [
            { element: 'Sidebar LMS "Materi"', target: 'guru-04-materi' },
            { element: 'Sidebar LMS "Tugas" → "Buat Tugas"', target: 'guru-05-buat-tugas' },
            { element: 'Notifikasi "12 siswa mengumpulkan"', target: 'guru-06-koreksi-tugas' },
            { element: 'Sidebar LMS "Ujian" → "Kelola Soal"', target: 'guru-07-kelola-soal-ujian' },
            { element: 'Sidebar LMS "Kembali ke Dashboard"', target: 'guru-01-dashboard-sia ⚡SHELL KEMBALI KE SIA⚡' },
        ]
    },
    'guru-04-materi': { buttons: [] },
    'guru-05-buat-tugas': {
        buttons: [
            { element: 'Tombol "Terbitkan Tugas"', target: 'daftar tugas (tugas baru muncul)' },
        ]
    },
    'guru-06-koreksi-tugas': {
        buttons: [
            { element: 'Tombol "Simpan Semua Nilai"', target: 'daftar tugas' },
        ]
    },
    'guru-07-kelola-soal-ujian': { buttons: [] },

    // === SISWA ===
    'siswa-01-dashboard-sia': {
        buttons: [
            { element: 'Sidebar "Presensi"', target: 'siswa-02-presensi' },
            { element: 'Sidebar/Kartu "HOK-LMS"', target: 'siswa-03-dashboard-lms ⚡SHELL BERUBAH KE LMS⚡' },
        ]
    },
    'siswa-02-presensi': { buttons: [] },
    'siswa-03-dashboard-lms': {
        buttons: [
            { element: 'Klik kartu "Matematika"', target: 'siswa-04-halaman-mapel' },
            { element: 'Sidebar "Kembali ke SIA"', target: 'siswa-01-dashboard-sia ⚡SHELL KEMBALI KE SIA⚡' },
        ]
    },
    'siswa-04-halaman-mapel': {
        buttons: [
            { element: 'Tab "Tugas" → klik tugas', target: 'siswa-05-kerjakan-tugas' },
            { element: 'Tab "Ujian" → klik ujian', target: 'siswa-06-ujian-fullscreen ⚡MODE FULLSCREEN, TANPA SIDEBAR⚡' },
        ]
    },
    'siswa-05-kerjakan-tugas': {
        buttons: [
            { element: 'Tombol "Kumpulkan Tugas"', target: 'siswa-04-halaman-mapel (status → Dikumpulkan)' },
        ]
    },
    'siswa-06-ujian-fullscreen': {
        buttons: [
            { element: 'Tombol "Selesai Ujian"', target: 'siswa-03-dashboard-lms (konfirmasi dulu)' },
        ]
    },

    // === WALI SISWA ===
    'wali-siswa-01-dashboard': {
        buttons: [
            { element: 'Kartu anak "Bayar"', target: 'wali-siswa-02-tagihan-anak' },
            { element: 'Kartu anak "Rapor"', target: 'wali-siswa-05-detail-rapor' },
            { element: 'Kartu anak "Presensi" → "Ajukan Izin"', target: 'wali-siswa-06-ajukan-izin' },
            { element: 'Sidebar child > "Tagihan"', target: 'wali-siswa-02-tagihan-anak' },
            { element: 'Sidebar child > "Rapor"', target: 'wali-siswa-05-detail-rapor' },
        ]
    },
    'wali-siswa-02-tagihan-anak': {
        buttons: [
            { element: 'Centang tagihan → "Bayar Terpilih"', target: 'wali-siswa-03-pembayaran-midtrans' },
        ]
    },
    'wali-siswa-03-pembayaran-midtrans': {
        buttons: [
            { element: '"Lanjutkan ke Pembayaran" → Midtrans overlay → sukses', target: 'wali-siswa-04-invoice' },
        ]
    },
    'wali-siswa-04-invoice': { buttons: [] },
    'wali-siswa-05-detail-rapor': {
        buttons: [
            { element: 'Tombol "Ajukan Permintaan Unduh"', target: 'tetap (status → Menunggu Persetujuan)' },
        ]
    },
    'wali-siswa-06-ajukan-izin': {
        buttons: [
            { element: 'Tombol "Kirim Pengajuan"', target: 'daftar presensi (status → Menunggu)' },
        ]
    },
};

// Map dari nama file source → prefix output
const FILE_MAP = {
    '01-publik.html': 'publik',
    '02-admin.html': 'admin',
    '03-ketua.html': 'ketua',
    '04-waka.html': 'waka',
    '05-sekretaris.html': 'sekretaris',
    '06-bendahara.html': 'bendahara',
    '07-wali-kelas.html': 'wali-kelas',
    '08-guru.html': 'guru',
    '09-siswa.html': 'siswa',
    '10-wali-siswa.html': 'wali-siswa',
};

// Nama layar per role (sesuai docs/figma/03-inventaris-layar.md)
const SCREEN_NAMES = {
    'admin': ['dashboard', 'data-siswa', 'tambah-siswa', 'data-kelas', 'jadwal-pelajaran', 'tagihan', 'laporan', 'cms-landing-page'],
    'ketua': ['dashboard', 'validasi-rapor', 'preview-rapor', 'dispensasi-keuangan', 'approval-kenaikan', 'cetak-laporan'],
    'waka': ['dashboard', 'data-kelas', 'jadwal-pelajaran', 'pengaturan-kkm', 'monitoring-siswa'],
    'sekretaris': ['dashboard', 'kelola-berita', 'form-berita', 'kalender-akademik'],
    'bendahara': ['dashboard', 'kelola-tagihan', 'detail-tagihan-siswa', 'kelola-pembayaran', 'validasi-pembayaran', 'laporan-pembayaran', 'siswa-belum-lunas'],
    'wali-kelas': ['pilih-kelas', 'dashboard', 'input-presensi', 'edit-nilai', 'kelola-rapor', 'edit-rapor', 'preview-rapor', 'permintaan-unduh'],
    'guru': ['dashboard-sia', 'semua-kelas', 'dashboard-lms', 'materi', 'buat-tugas', 'koreksi-tugas', 'kelola-soal-ujian'],
    'siswa': ['dashboard-sia', 'presensi', 'dashboard-lms', 'halaman-mapel', 'kerjakan-tugas', 'ujian-fullscreen'],
    'wali-siswa': ['dashboard', 'tagihan-anak', 'pembayaran-midtrans', 'invoice', 'detail-rapor', 'ajukan-izin'],
    'publik': ['beranda', 'ppdb', 'login', 'pemulihan-akun'],
};

function slugify(text) {
    return text.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

function buildWiringComment(screenId) {
    const w = WIRING[screenId];
    if (!w || !w.buttons.length) return '';
    
    let comment = '\n<!--\n=== WIRING (Google Stitch: hubungkan button/link berikut ke layar tujuan) ===\n';
    w.buttons.forEach(b => {
        comment += `  ${b.element}  →  ${b.target}\n`;
    });
    comment += '-->\n';
    return comment;
}

function splitFile(srcFile, rolePrefix) {
    const html = fs.readFileSync(path.join(SCREENS_DIR, srcFile), 'utf-8');
    const names = SCREEN_NAMES[rolePrefix] || [];
    
    // Split on screen-label divs
    // Pattern: everything between one screen-label+screen and the next screen-label
    const screenRegex = /(<div class="screen-label">[\s\S]*?<\/div>\s*<div class="screen"[\s\S]*?<\/div>\s*<\/div>\s*<\/div>)/g;
    
    // Simpler approach: split by screen-label
    const parts = html.split(/(?=<div class="screen-label">)/);
    const screens = parts.filter(p => p.includes('class="screen-label"'));
    
    console.log(`  ${srcFile}: found ${screens.length} screens`);
    
    screens.forEach((screenHtml, i) => {
        const nn = String(i + 1).padStart(2, '0');
        const screenName = names[i] || `screen-${nn}`;
        const screenId = `${rolePrefix}-${nn}-${screenName}`;
        const outName = `${rolePrefix}-${nn}-${screenName}.html`;
        
        // Extract the screen-label text for reference
        const labelMatch = screenHtml.match(/<div class="screen-label">(.*?)<\/div>/);
        const label = labelMatch ? labelMatch[1].replace(/<[^>]*>/g, '').trim() : screenId;
        
        // Build complete standalone HTML
        const fullHtml = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>SIPADUHOK — ${label}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../ui-kit.css">
</head>
<body>
${buildWiringComment(screenId)}
${screenHtml.trim()}

<script src="../ui-kit.js"></script>
</body>
</html>`;
        
        fs.writeFileSync(path.join(OUT_DIR, outName), fullHtml, 'utf-8');
    });
    
    return screens.length;
}

// === MAIN ===
console.log('🧵 Splitting HTML screens for Google Stitch...\n');

// Create output dir
if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });

let total = 0;

// Create Login + Role Picker page
const loginHtml = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>SIPADUHOK — Login + Pilih Role</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../ui-kit.css">
<style>
.login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f0f4ff, #e8f0fe); }
.login-card { background: #fff; border-radius: 12px; box-shadow: 0 10px 24px rgba(15,23,42,.08); padding: 40px; width: 420px; }
.login-card .brand { text-align: center; margin-bottom: 32px; }
.login-card .brand h1 { font-size: 24px; font-weight: 800; color: #4361ee; letter-spacing: 1px; margin: 0; }
.login-card .brand p { font-size: 13px; color: #94a3b8; margin: 6px 0 0; }
.login-card h2 { font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 6px; }
.login-card .sub { font-size: 13px; color: #64748b; margin: 0 0 24px; }
.field { margin-bottom: 18px; }
.field label { display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 7px; }
.field label .req { color: #ef4444; }
.field input { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; }
.field input:focus { border-color: #4361ee; outline: none; box-shadow: 0 0 0 3px rgba(67,97,238,.12); }
.check-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 13px; color: #64748b; }
.btn-login { width: 100%; padding: 12px; background: #4361ee; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-login:hover { background: #3651d4; }
.forgot { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #4361ee; text-decoration: none; }

.role-picker-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.6); z-index: 999; align-items: center; justify-content: center; }
.role-picker-overlay.active { display: flex; }
.role-picker { background: #fff; border-radius: 14px; padding: 32px; width: 620px; max-height: 90vh; overflow-y: auto; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.role-picker h3 { font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 6px; }
.role-picker .sub { font-size: 13px; color: #64748b; margin: 0 0 20px; }
.role-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.role-card { padding: 16px; border: 1px solid #e2e8f0; border-radius: 10px; text-align: center; cursor: pointer; transition: all .15s; }
.role-card:hover { border-color: #4361ee; background: #eff6ff; transform: translateY(-2px); }
.role-card .icon { font-size: 28px; margin-bottom: 8px; }
.role-card .name { font-size: 13px; font-weight: 600; color: #334155; }
.role-card .desc { font-size: 11px; color: #94a3b8; margin-top: 3px; }
</style>
</head>
<body>

<!--
=== WIRING (Google Stitch: hubungkan button/link berikut ke layar tujuan) ===
  Tombol "Masuk"  →  Role Picker overlay (tampil)
  Role Picker "Admin"  →  admin-01-dashboard.html
  Role Picker "Ketua PKBM"  →  ketua-01-dashboard.html
  Role Picker "Wakil Kepala Sekolah"  →  waka-01-dashboard.html
  Role Picker "Sekretaris"  →  sekretaris-01-dashboard.html
  Role Picker "Bendahara"  →  bendahara-01-dashboard.html
  Role Picker "Wali Kelas"  →  wali-kelas-01-pilih-kelas.html
  Role Picker "Guru Pengajar"  →  guru-01-dashboard-sia.html
  Role Picker "Siswa"  →  siswa-01-dashboard-sia.html
  Role Picker "Wali Siswa"  →  wali-siswa-01-dashboard.html
  Link "Lupa kata sandi?"  →  publik-04-pemulihan-akun.html (kalau ada)
-->

<div class="login-page">
    <div class="login-card">
        <div class="brand">
            <h1>SIPADUHOK</h1>
            <p>Sistem Informasi PKBM House of Knowledge</p>
        </div>
        <h2>Masuk ke SIPADUHOK</h2>
        <p class="sub">Silakan masuk dengan akun Anda</p>
        <div class="field">
            <label>Username / Email <span class="req">*</span></label>
            <input type="text" placeholder="Masukkan username atau email">
        </div>
        <div class="field">
            <label>Kata Sandi <span class="req">*</span></label>
            <input type="password" placeholder="Masukkan kata sandi">
        </div>
        <div class="check-row">
            <input type="checkbox" id="remember"> <label for="remember" style="font-weight:400">Ingat Saya</label>
        </div>
        <button class="btn-login" onclick="document.querySelector('.role-picker-overlay').classList.add('active')">Masuk</button>
        <a href="#" class="forgot">Lupa kata sandi?</a>
    </div>
</div>

<div class="role-picker-overlay" onclick="if(event.target===this)this.classList.remove('active')">
    <div class="role-picker">
        <h3>Pilih Role untuk Demo</h3>
        <p class="sub">Klik salah satu role untuk masuk ke dashboard masing-masing</p>
        <div class="role-grid">
            <div class="role-card"><div class="icon">👑</div><div class="name">Admin</div><div class="desc">Superset seluruh modul</div></div>
            <div class="role-card"><div class="icon">🏛️</div><div class="name">Ketua PKBM</div><div class="desc">Approver & monitoring</div></div>
            <div class="role-card"><div class="icon">📋</div><div class="name">Wakil Kepala Sekolah</div><div class="desc">Akademik saja</div></div>
            <div class="role-card"><div class="icon">📝</div><div class="name">Sekretaris</div><div class="desc">Konten publikasi</div></div>
            <div class="role-card"><div class="icon">💰</div><div class="name">Bendahara</div><div class="desc">Keuangan</div></div>
            <div class="role-card"><div class="icon">🏫</div><div class="name">Wali Kelas</div><div class="desc">Rapor, presensi, nilai</div></div>
            <div class="role-card"><div class="icon">🧑‍🏫</div><div class="name">Guru Pengajar</div><div class="desc">LMS & pembelajaran</div></div>
            <div class="role-card"><div class="icon">🎓</div><div class="name">Siswa</div><div class="desc">SIA & LMS</div></div>
            <div class="role-card"><div class="icon">👨‍👩‍👧</div><div class="name">Wali Siswa</div><div class="desc">Tagihan, rapor, presensi anak</div></div>
        </div>
    </div>
</div>

</body>
</html>`;

fs.writeFileSync(path.join(OUT_DIR, '00-login-role-picker.html'), loginHtml, 'utf-8');
console.log('  ✅ 00-login-role-picker.html created');
total++;

// Split all screen files
for (const [file, prefix] of Object.entries(FILE_MAP)) {
    if (fs.existsSync(path.join(SCREENS_DIR, file))) {
        const count = splitFile(file, prefix);
        total += count;
    } else {
        console.log(`  ⚠️ ${file} not found, skipping`);
    }
}

// Create a README for stitch folder
const readme = `# Stitch Upload Files

Total: ${total} file HTML individual.

## Urutan Upload ke Google Stitch

### Batch 1: Login (1 file)
- \`00-login-role-picker.html\` — login + pilih role → 9 dashboard

### Batch 2: Admin (8 file)
- \`admin-01-dashboard.html\` sampai \`admin-08-*.html\`

### Batch 3: Bendahara (7 file)
- \`bendahara-01-*.html\` sampai \`bendahara-07-*.html\`

### Batch 4: Wali Kelas (8 file)
- \`wali-kelas-01-*.html\` sampai \`wali-kelas-08-*.html\`

### Batch 5: Ketua PKBM (6 file)
- \`ketua-01-*.html\` sampai \`ketua-06-*.html\`

### Batch 6: Guru (7 file)
- \`guru-01-*.html\` sampai \`guru-07-*.html\`

### Batch 7: Siswa (6 file)
- \`siswa-01-*.html\` sampai \`siswa-06-*.html\`

### Batch 8: Wali Siswa (6 file)
- \`wali-siswa-01-*.html\` sampai \`wali-siswa-06-*.html\`

### Batch 9: Waka + Sekretaris (9 file)
- \`waka-01-*.html\` sampai \`waka-05-*.html\`
- \`sekretaris-01-*.html\` sampai \`sekretaris-04-*.html\`

## Wiring

Setiap file HTML berisi komentar \`<!-- WIRING -->\` di bagian atas yang menjelaskan
button mana harus dihubungkan ke layar mana. Salin informasi wiring ini ke prompt
Stitch saat membuat prototype interaktif.

## Catatan
- File publik (01-publik) dikeluarkan karena landing page sudah ada di Figma
- Setiap file self-contained (referensi CSS + JS ke folder parent)
- Buka file di browser untuk preview visual sebelum upload
`;

fs.writeFileSync(path.join(OUT_DIR, 'README.md'), readme, 'utf-8');

console.log(`\n✅ Done! ${total} files written to stitch/`);
console.log('📂 Open the files in browser to preview before uploading to Stitch.');

