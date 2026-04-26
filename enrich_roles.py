import json

filepath = r'c:\laragon\www\sipaduhok\dataset\intents.json'
with open(filepath, 'r', encoding='utf-8-sig') as f:
    data = json.load(f)

# Massive enrichment for non-admin roles to enable cross-role detection
role_patterns = {
    "siswa": [
        "kerjakan tugas", "lihat nilai saya", "cek tagihan saya", "cara bayar spp", 
        "masuk lms", "presensi saya", "jadwal pelajaran saya", "rapor saya", 
        "materi pelajaran", "ujian lms", "forum diskusi", "izin tidak masuk"
    ],
    "guru": [
        "input nilai", "buat materi lms", "buat tugas lms", "presensi kelas", 
        "lihat semua kelas", "jadwal mengajar", "koreksi tugas", "input ujian", 
        "catatan untuk wali kelas", "forum guru", "video conference"
    ],
    "orang_tua": [
        "cek nilai anak", "bayar spp anak", "izin anak sakit", "presensi anak", 
        "tagihan anak", "rapor anak", "lms anak"
    ],
    "bendahara": [
        "catat uang masuk", "rekap tagihan", "config midtrans", "validasi bayar", 
        "laporan keuangan bendahara", "bayar gaji", "pengeluaran sekolah"
    ]
}

# Find intents for these roles and add the general patterns to them
# This is a bit coarse but it will make those intents "competitors" in the model
for intent in data['intents']:
    role = intent['role']
    tag = intent['tag']
    
    if role in role_patterns:
        # If it's a "main" intent for that role, add patterns
        if tag.endswith('_dashboard') or tag.endswith('_lms') or 'tugas' in tag or 'nilai' in tag:
            intent['patterns'].extend(role_patterns[role])
            intent['patterns'] = list(set(intent['patterns']))

with open(filepath, 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)

print("Non-Admin roles enriched for better cross-role detection!")
