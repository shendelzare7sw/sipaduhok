import json

filepath = r'c:\laragon\www\sipaduhok\dataset\intents.json'
with open(filepath, 'r', encoding='utf-8') as f:
    data = json.load(f)

# Extend patterns for Admin intents
extensions = {
    "admin_dashboard": ["dashboard admin", "halaman awal admin", "ringkasan admin"],
    "admin_landing_page": ["edit tampilan luar", "ganti profil sekolah", "ubah informasi publik"],
    "admin_tenaga_pendidik": ["edit staf", "hapus guru", "kelola data staff", "tambah pegawai"],
    "admin_siswa": ["edit profil siswa", "hapus siswa", "pindah siswa", "naik kelas siswa", "cara edit akun siswa"],
    "admin_jadwal_pelajaran": ["edit jadwal", "hapus jadwal", "ubah jam mapel", "cara hapus roster"],
    "admin_tagihan": ["edit tagihan", "hapus invoice", "ubah nominal", "buat invoice", "kelola spp"],
    "admin_pembayaran": ["edit pembayaran", "batal validasi", "tolak bayar", "riwayat bayar"],
    "admin_pengumuman": ["edit pengumuman", "hapus informasi", "ubah teks pengumuman", "batalkan pengumuman"],
    "admin_wali_murid": ["tambah wali murid", "data orang tua", "edit wali", "hapus orang tua", "akun wali murid", "buat ortu"],
    "admin_tiket_pemulihan": ["tiket pemulihan", "lupa password", "reset sandi", "approve tiket", "tolak reset", "ganti password user", "pemulihan akun"],
    "admin_pengaturan_lms": ["pengaturan lms", "setting elearning", "konfigurasi ujian", "aktifkan pendaftaran", "edit config lms"],
    "admin_pengaturan_ai": ["pengaturan ai", "setting chatbot", "ganti api key", "prompt sistem", "aktifkan llm"],
    "admin_google_sheets": ["google sheets sync", "tarik data excel", "sinkron data", "update sheet", "export ke google"],
    "admin_tahun_ajaran": ["tahun ajaran baru", "tambah semester", "edit tahun ajaran", "hapus tahun ajaran", "ubah tahun akademik", "aktifkan ganjil", "cara edit tahun ajaran", "ubah genap"],
    "admin_manajemen_cabang": ["manajemen cabang", "sekolah cabang", "tambah rayon", "edit lokasi cabang", "hapus cabang"],
    "admin_data_kelas": ["data kelas", "tambah kelas", "edit nama kelas", "hapus rombel", "kelola rombongan belajar"],
    "admin_data_wali_kelas": ["data wali kelas", "assign wali", "pilih wali kelas", "ganti wali kelas", "edit pembimbing"],
    "admin_data_guru_pengajar": ["data guru pengajar", "jadwalkan guru", "plotting pengajar", "edit guru mapel", "hapus guru mengajar"],
    "admin_mata_pelajaran": ["mata pelajaran", "tambah mapel", "edit kurikulum", "hapus pelajaran", "bikin mapel baru"],
    "admin_manajemen_siswa": ["manajemen siswa", "ploting kelas", "pindah rombel siswa", "atur siswa per kelas", "masukkan murid ke kelas"],
    "admin_laporan_keuangan": ["laporan keuangan", "rekap uang masuk", "cetak uang spp", "lihat grafik bulanan", "download rekap bayar"],
    "admin_config_pembayaran": ["config pembayaran", "setting midtrans", "ganti virtual account", "atur bank gateway", "ubah api pembayaran"],
    "admin_validasi_dispensasi": ["validasi dispensasi", "approve keringanan", "tolak cicilan", "persetujuan dispensasi", "cek pengajuan ringan bayar"],
    "admin_validasi_akses": ["validasi akses", "buka blokir ujian", "aktifkan ujian siswa", "beri akses tes", "izinkan tryout"],
    "admin_pengaturan_kkm": ["pengaturan kkm", "setting batas nilai", "ubah standar kkm", "edit batas lulus", "cara ganti kkm minimal"],
    "admin_pengaturan_kenaikan": ["pengaturan kenaikan", "syarat lulus", "setting naik kelas", "ubah prasyarat kelulusan", "edit aturan tamat"],
    "admin_proses_rekap": ["proses rekap kenaikan", "hitung kelulusan", "generate kenaikan", "simulasikan naik kelas", "rekap ujian akhir"],
    "admin_kalender_akademik": ["kalender akademik", "jadwal libur", "tambah event tanggal merah", "edit kalender", "hapus hari libur"],
    "admin_berita": ["tulis berita", "tambah artikel", "edit publikasi", "hapus berita sekolah", "cara posting blog"],
    "admin_flyer": ["tambah flyer", "pasang iklan pop up", "ganti brosur login", "edit gambar promosi", "hapus flyer event"],
    "admin_monitoring_pengguna": ["monitoring pengguna", "cek log login", "siapa yang online", "pantau aktivitas login", "cek ip login"],
    "admin_monitoring_wali_kelas": ["monitoring wali kelas", "progres wali", "kinerja wali", "cek wali lapor", "pantau aktivitas pembimbing"],
    "admin_monitoring_guru_pengajar": ["monitoring guru pengajar", "cek isi nilai", "cek soal yang dibuat guru", "kinerja pengajar"],
    "admin_monitoring_siswa": ["monitoring siswa", "pantau aktivitas murid", "cek absensi murid", "lihat login siswa", "log murid online"],
    "admin_laporan": ["laporan sistem", "export rekap", "cetak pdf laporan", "download rekap excel", "tarik file report"],
    "admin_catatan": ["kirim catatan", "pesan internal", "buat memo staf", "edit pesan staff", "kirim instruksi"]
}

for intent in data['intents']:
    tag = intent['tag']
    if tag in extensions:
        existing = set(intent['patterns'])
        existing.update(extensions[tag])
        intent['patterns'] = list(existing)

with open(filepath, 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)

print(f"Successfully enriched Admin intents with {sum(len(v) for v in extensions.values())} new patterns!")
