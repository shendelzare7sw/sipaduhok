import json

filepath = r'c:\laragon\www\sipaduhok\dataset\intents.json'
with open(filepath, 'r', encoding='utf-8-sig') as f:
    data = json.load(f)

# Filter out old admin intents except those we might want to preserve 
# (Actually, we'll just remove all admin_ ones and recreate them cleanly)
new_intents = [i for i in data['intents'] if not i['tag'].startswith('admin_')]

admin_intents = [
    {
        "tag": "admin_dashboard",
        "role": "admin",
        "patterns": ["kembali ke dashboard", "tampilan awal", "halaman utama", "beranda admin", "lihat statistik sekolah", "grafik utama"],
        "responses": ["Buka sidebar -> Dashboard. Di sana Anda dapat melihat statistik ringkasan pengguna, akademik, dan keuangan."]
    },
    {
        "tag": "admin_landing_page",
        "role": "admin",
        "patterns": ["edit landing page", "kelola website depan", "ubah konten website sekolah", "cara ganti foto di web luar", "atur halaman publik"],
        "responses": ["Buka sidebar -> Manajemen Konten -> Landing Page. Di sini Anda bisa mengelola konten publik website sekolah."]
    },
    {
        "tag": "admin_tenaga_pendidik",
        "role": "admin",
        "patterns": ["tambah tenaga pendidik", "data guru staff", "input pegawai baru", "kelola user guru", "daftar pegawai tata usaha", "import tenaga pendidik"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Manajemen User -> Tenaga Pendidik. Di sini Anda bisa menambah atau mengimport data guru dan staf."]
    },
    {
        "tag": "admin_siswa",
        "role": "admin",
        "patterns": ["tambah siswa baru", "data user siswa", "import akun siswa", "kelola daftar siswa", "edit akun murid", "daftar siswa terdaftar"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Manajemen User -> Siswa. Menu ini untuk mendaftarkan akun siswa ke sistem."]
    },
    {
        "tag": "admin_wali_murid",
        "role": "admin",
        "patterns": ["tambah wali murid", "data orang tua", "akun bapak ibu siswa", "kelola user wali murid", "import orang tua", "daftar akun orang tua"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Manajemen User -> Wali Murid. Menu ini untuk mengelola akses akun orang tua/wali siswa."]
    },
    {
        "tag": "admin_tiket_pemulihan",
        "role": "admin",
        "patterns": ["tiket pemulihan akun", "lupa password user", "reset password siswa", "bantu reset sandi guru", "user minta ganti password", "pemulihan akun orang tua"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Tiket Pemulihan Akun. Di sini Anda bisa memproses request lupa password dari pengguna."]
    },
    {
        "tag": "admin_pengaturan_lms",
        "role": "admin",
        "patterns": ["pengaturan lms", "setting lms", "atur sistem pembelajaran", "konfigurasi e-learning", "hidupkan fitur lms"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Pengaturan LMS. Gunakan menu ini untuk mengonfigurasi fitur e-learning sekolah."]
    },
    {
        "tag": "admin_pengaturan_ai",
        "role": "admin",
        "patterns": ["pengaturan ai", "setting ai chatbot", "konfigurasi artificial intelligence", "matikan ai", "atur model llama gemini"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Pengaturan AI. Di sini Anda bisa menghidupkan/mematikan LLM dan mengatur API key."]
    },
    {
        "tag": "admin_google_sheets",
        "role": "admin",
        "patterns": ["google sheets sync", "sinkronisasi google sheet", "tarik data spreadsheet", "koneksi excel online", "export google sheet"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Google Sheets Sync. Gunakan fitur ini untuk integrasi data dengan Spreadsheet."]
    },
    {
        "tag": "admin_tahun_ajaran",
        "role": "admin",
        "patterns": ["tahun ajaran baru", "tambah semester", "ganti tahun akademik aktif", "kelola periode sekolah", "setting ganjil genap"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Tahun Ajaran. Di sini Anda bisa mengatur tahun akademik yang sedang aktif."]
    },
    {
        "tag": "admin_manajemen_cabang",
        "role": "admin",
        "patterns": ["manajemen cabang", "tambah sekolah cabang", "kelola cabang kampus", "data unit sekolah", "setting multi cabang"],
        "responses": ["Buka sidebar -> Manajemen Pengguna -> Manajemen Cabang. Menu ini untuk mengelola berbagai cabang sekolah dalam satu sistem."]
    },
    {
        "tag": "admin_data_kelas",
        "role": "admin",
        "patterns": ["data kelas", "tambah kelas baru", "bikin kelas x y z", "kelola daftar kelas", "import kelas"],
        "responses": ["Buka sidebar -> Data Akademik -> Data Kelas. Di sini Anda bisa membuat atau mengimport kelas baru."]
    },
    {
        "tag": "admin_data_wali_kelas",
        "role": "admin",
        "patterns": ["data wali kelas", "assign wali kelas", "tunjuk guru jadi wali", "daftar wali kelas", "siapa saja wali kelas"],
        "responses": ["Buka sidebar -> Data Akademik -> Data Wali Kelas. Di sini Anda bisa menugaskan guru sebagai wali untuk kelas tertentu."]
    },
    {
        "tag": "admin_data_guru_pengajar",
        "role": "admin",
        "patterns": ["data guru pengajar", "assign guru mapel", "siapa yang mengajar", "jadwalkan guru pengajar", "daftar guru bidang studi"],
        "responses": ["Buka sidebar -> Data Akademik -> Data Guru Pengajar. Di sini Anda menentukan guru mana yang mengajar mata pelajaran tertentu."]
    },
    {
        "tag": "admin_mata_pelajaran",
        "role": "admin",
        "patterns": ["mata pelajaran", "tambah mapel baru", "bikin kurikulum pelajaran", "kelola daftar mapel", "import mata pelajaran"],
        "responses": ["Buka sidebar -> Data Akademik -> Mata Pelajaran. Anda bisa menambahkan atau mengatur mapel yang diajarkan di sekolah."]
    },
    {
        "tag": "admin_jadwal_pelajaran",
        "role": "admin",
        "patterns": ["jadwal pelajaran", "buat jadwal mengajar", "susun roster kelas", "jam pelajaran", "import jadwal"],
        "responses": ["Buka sidebar -> Data Akademik -> Jadwal Pelajaran. Di sini Anda bisa menyusun roster jadwal untuk setiap kelas."]
    },
    {
        "tag": "admin_manajemen_siswa",
        "role": "admin",
        "patterns": ["manajemen siswa", "pindah kelas siswa", "masukkan siswa ke kelas", "ploting kelas murid", "atur rombel siswa"],
        "responses": ["Buka sidebar -> Data Akademik -> Manajemen Siswa. Menu ini khusus untuk memploting/memasukkan siswa ke dalam kelas (rombel)."]
    },
    {
        "tag": "admin_tagihan",
        "role": "admin",
        "patterns": ["buat tagihan spp", "generate tagihan bulanan", "cara bikin tagihan", "kelola tagihan siswa", "reset tagihan salah", "dimana menu tagihan"],
        "responses": ["Buka sidebar -> Keuangan -> Tagihan. Di sini Anda bisa men-generate SPP bulanan atau membuat tagihan custom."]
    },
    {
        "tag": "admin_pembayaran",
        "role": "admin",
        "patterns": ["validasi pembayaran", "approve bayar siswa", "cara input pembayaran manual", "cetak kwitansi", "dimana menu pembayaran", "untuk lihat menu pembayaran"],
        "responses": ["Buka sidebar -> Keuangan -> Pembayaran. Di sini Anda bisa menyetujui (approve) pembayaran masuk atau mencatat pembayaran tunai."]
    },
    {
        "tag": "admin_laporan_keuangan",
        "role": "admin",
        "patterns": ["laporan keuangan", "rekap uang masuk", "cek total pendapatan", "cetak laporan kas", "mutasi kas sekolah"],
        "responses": ["Buka sidebar -> Keuangan -> Laporan Keuangan. Anda bisa melihat rekapitulasi dana masuk dan keluar."]
    },
    {
        "tag": "admin_config_pembayaran",
        "role": "admin",
        "patterns": ["config pembayaran", "setting midtrans", "atur rekening sekolah", "konfigurasi payment gateway", "cara setup bank"],
        "responses": ["Buka sidebar -> Keuangan -> Config Pembayaran. Menu ini untuk mengatur integrasi Midtrans dan rekening bank sekolah."]
    },
    {
        "tag": "admin_validasi_dispensasi",
        "role": "admin",
        "patterns": ["validasi dispensasi", "approve surat keringanan", "acc penundaan bayar", "izin dispensasi keuangan", "kelola request dispensasi"],
        "responses": ["Buka sidebar -> Keuangan -> Validasi Dispensasi. Anda dapat memeriksa dan menyetujui permohonan keringanan biaya dari siswa."]
    },
    {
        "tag": "admin_validasi_akses",
        "role": "admin",
        "patterns": ["validasi akses", "buka blokir ujian", "beri izin ikut ujian", "bypass penahanan rapor", "kelola blokir ujian"],
        "responses": ["Buka sidebar -> Keuangan -> Validasi Akses. Menu ini digunakan untuk membuka akses LMS/ujian bagi siswa yang belum lunas."]
    },
    {
        "tag": "admin_pengaturan_kkm",
        "role": "admin",
        "patterns": ["pengaturan kkm", "setting nilai minimal", "atur batas ketuntasan", "cara ganti kkm", "syarat tuntas mapel"],
        "responses": ["Buka sidebar -> Kenaikan Kelas -> Pengaturan KKM. Di sini Anda dapat menentukan standar KKM untuk setiap mata pelajaran."]
    },
    {
        "tag": "admin_pengaturan_kenaikan",
        "role": "admin",
        "patterns": ["pengaturan kenaikan", "setting naik kelas", "syarat kenaikan kelas", "atur persentase kehadiran naik kelas", "aturan lulus"],
        "responses": ["Buka sidebar -> Kenaikan Kelas -> Pengaturan Kenaikan. Anda bisa menentukan syarat ketuntasan nilai dan kehadiran untuk promosi kelas."]
    },
    {
        "tag": "admin_proses_rekap",
        "role": "admin",
        "patterns": ["proses rekap kenaikan", "hitung siapa yang naik", "rekap nilai akhir", "jalankan proses naik kelas", "keputusan lulus"],
        "responses": ["Buka sidebar -> Kenaikan Kelas -> Proses & Rekap. Eksekusi proses penghitungan otomatis untuk menentukan siswa yang naik/lulus."]
    },
    {
        "tag": "admin_kalender_akademik",
        "role": "admin",
        "patterns": ["kalender akademik", "tambah jadwal libur", "bikin agenda sekolah", "kelola kalender", "jadwal event sekolah"],
        "responses": ["Buka sidebar -> Manajemen Akademik -> Kalender Akademik. Anda dapat menambahkan hari libur atau event penting sekolah."]
    },
    {
        "tag": "admin_pengumuman",
        "role": "admin",
        "patterns": ["cara bikin pengumuman", "buat pengumuman baru", "tambah pengumuman di dashboard", "siarkan informasi ke siswa", "tulis pengumuman", "cara buat pengumuman"],
        "responses": ["Buka sidebar -> Manajemen Akademik -> Pengumuman. Klik 'Tambah' untuk membuat pengumuman yang akan tampil di dashboard semua user."]
    },
    {
        "tag": "admin_berita",
        "role": "admin",
        "patterns": ["tulis berita", "tambah artikel sekolah", "bikin postingan berita", "kelola blog sekolah", "cara buat berita"],
        "responses": ["Buka sidebar -> Manajemen Akademik -> Berita. Anda bisa menulis artikel atau berita untuk publikasi web."]
    },
    {
        "tag": "admin_flyer",
        "role": "admin",
        "patterns": ["tambah flyer", "pasang iklan pop up", "bikin brosur online", "kelola banner promosi", "cara buat flyer"],
        "responses": ["Buka sidebar -> Manajemen Akademik -> Flyer. Anda bisa mengunggah banner atau brosur pop-up."]
    },
    {
        "tag": "admin_monitoring_pengguna",
        "role": "admin",
        "patterns": ["monitoring pengguna", "pantau aktivitas user", "kapan login terakhir", "cek log user aktif", "status online offline"],
        "responses": ["Buka sidebar -> Monitoring & Analitik -> Monitoring -> Pengguna. Lihat riwayat login dan aktivitas pengguna."]
    },
    {
        "tag": "admin_monitoring_wali_kelas",
        "role": "admin",
        "patterns": ["monitoring wali kelas", "pantau kinerja wali", "cek progres wali kelas", "kinerja pengisian rapor", "status tugas wali"],
        "responses": ["Buka sidebar -> Monitoring & Analitik -> Monitoring -> Wali Kelas. Pantau kepatuhan wali kelas dalam mengisi presensi dan rapor."]
    },
    {
        "tag": "admin_monitoring_guru_pengajar",
        "role": "admin",
        "patterns": ["monitoring guru pengajar", "pantau kinerja guru", "cek pengisian nilai", "progres mengajar", "evaluasi guru mapel"],
        "responses": ["Buka sidebar -> Monitoring & Analitik -> Monitoring -> Guru Pengajar. Evaluasi kinerja guru dalam menyampaikan materi dan nilai."]
    },
    {
        "tag": "admin_monitoring_siswa",
        "role": "admin",
        "patterns": ["monitoring siswa", "pantau aktivitas murid", "cek kehadiran siswa", "log tugas murid", "prestasi siswa"],
        "responses": ["Buka sidebar -> Monitoring & Analitik -> Monitoring -> Siswa. Pantau tingkat kehadiran dan partisipasi siswa di LMS."]
    },
    {
        "tag": "admin_laporan",
        "role": "admin",
        "patterns": ["laporan sistem", "cetak data sekolah", "export rekapitulasi", "unduh laporan lengkap", "laporan akhir semester"],
        "responses": ["Buka sidebar -> Monitoring & Analitik -> Laporan. Unduh semua rekapitulasi data akademik maupun keuangan."]
    },
    {
        "tag": "admin_catatan",
        "role": "admin",
        "patterns": ["kirim catatan", "pesan internal", "memo sekolah", "bikin catatan untuk waka", "tulis notes"],
        "responses": ["Buka sidebar -> Monitoring & Analitik -> Catatan. Anda bisa mengirimkan memo internal ke role pimpinan."]
    }
]

new_intents.extend(admin_intents)
data['intents'] = new_intents

with open(filepath, 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)

print(f"Successfully rebuilt {len(admin_intents)} Admin intents in intents.json!")
