import json
import os

filepath = r'c:\laragon\www\sipaduhok\dataset\intents.json'
with open(filepath, 'r', encoding='utf-8-sig') as f:
    data = json.load(f)

# Track intent updates
updated = 0

for intent in data['intents']:
    tag = intent['tag']
    
    # --- ADMIN ---
    if tag == 'admin_pembayaran':
        intent['patterns'] = ["validasi pembayaran siswa", "approve pembayaran manual", "cetak kwitansi", "riwayat bayar siswa", "untuk lihat menu pembayaran", "menu pembayaran dimana", "akses pembayaran"]
        intent['responses'] = ["Buka sidebar -> Pembayaran. Di sini Anda bisa memvalidasi pembayaran yang masuk dari orang tua/siswa, atau menginput pembayaran secara manual."]
        updated += 1
    elif tag == 'admin_tagihan':
        intent['patterns'] = ["cara buat tagihan spp", "generate tagihan bulanan", "kelola tagihan siswa", "reset tagihan", "dimana menu tagihan", "akses tagihan"]
        updated += 1
        
    # --- BENDAHARA ---
    elif tag == 'bendahara_pembayaran':
        intent['patterns'] = ["validasi pembayaran masuk", "approve bayar siswa", "input pembayaran manual", "cetak kwitansi", "untuk lihat menu pembayaran", "menu pembayaran dimana"]
        intent['responses'] = ["Buka sidebar -> Kelola Pembayaran. Di sini Anda bisa menyetujui transaksi midtrans atau mencatat pembayaran tunai."]
        updated += 1
        
    # --- SISWA ---
    elif tag == 'siswa_pembayaran':
        # Siswa actually does NOT have this menu, but they might ask about it
        intent['patterns'] = ["cara bayar spp siswa", "menu pembayaran dimana", "lunasi tagihan", "bayar tagihan spp", "lihat menu pembayaran"]
        intent['responses'] = ["Menu Pembayaran dan Tagihan tidak tersedia di akun Siswa. Silakan minta Orang Tua/Wali Murid login ke sistem untuk melihat tagihan dan melakukan pembayaran."]
        updated += 1
        
    # --- ORANG TUA ---
    elif tag == 'ortu_tagihan':
        intent['patterns'] = ["lihat tagihan anak", "berapa spp bulan ini", "cek tunggakan anak", "dimana menu tagihan", "lihat menu pembayaran", "menu pembayaran dimana"]
        intent['responses'] = ["Buka sidebar -> Tagihan Anak (jika anak sudah terhubung). Anda bisa melihat rincian tagihan SPP dan riwayat pembayaran di sana."]
        updated += 1

with open(filepath, 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)

print(f"Updated {updated} intents in intents.json")
