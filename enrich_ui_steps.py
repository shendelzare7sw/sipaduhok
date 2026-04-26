import json

filepath = r'c:\laragon\www\sipaduhok\dataset\intents.json'
with open(filepath, 'r', encoding='utf-8') as f:
    data = json.load(f)

enrich_ui = {
    "admin_tahun_ajaran": {
        "steps": [
            "Buka sidebar utama → Manajemen Akademik",
            "Pilih menu 'Tahun Ajaran'",
            "Klik tombol 'Tambah' untuk membuat tahun ajaran/semester baru",
            "Isikan kode, nama, dan tanggal mulai/selesai",
            "Klik 'Aktifkan' pada tahun ajaran yang ingin digunakan saat ini"
        ],
        "note": "Perhatian: Mengubah Tahun Ajaran aktif akan mereset tampilan dashboard dan data presensi ke semester baru bagi semua pengguna.",
        "related": ["kalender akademik", "data kelas", "kenaikan kelas"]
    },
    "admin_siswa": {
        "steps": [
            "Buka sidebar → Manajemen Pengguna → Siswa",
            "Klik 'Tambah' untuk mendaftarkan siswa secara manual",
            "Atau klik 'Import' lalu unggah file Excel template",
            "Isikan NISN, Nama, dan Email (opsional)",
            "Klik Simpan untuk membuat akun"
        ],
        "note": "Akun siswa akan otomatis mendapatkan password default sesuai pengaturan sekolah (biasanya NISN atau tanggal lahir).",
        "related": ["manajemen siswa", "tagihan", "ploting kelas"]
    }
}

for intent in data['intents']:
    tag = intent['tag']
    if tag in enrich_ui:
        # It's inside responses[0]['ui']
        try:
            intent['responses'][0]['ui']['steps'] = enrich_ui[tag]['steps']
            intent['responses'][0]['ui']['note'] = enrich_ui[tag]['note']
            intent['responses'][0]['ui']['related'] = enrich_ui[tag]['related']
        except:
            pass

with open(filepath, 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)

print("UI content enriched for specific intents!")
