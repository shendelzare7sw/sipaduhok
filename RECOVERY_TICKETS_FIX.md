# Recovery Tickets WhatsApp Gateway - Perbaikan & Diagnostik

## Masalah yang Ditemukan & Diperbaiki

### 1. **Error SSL Certificate (Masalah Utama - Gagal Gateway)**

**Error di Log:**
```
WA Gateway Exception: cURL error 77: error setting certificate file: D:\laragon\etc\ssl\cacert.pem
```

**Penyebab:**
- Laragon di Windows memiliki certificate file di path `D:\laragon\etc\ssl\cacert.pem`
- Aplikasi gagal mengakses file atau file corrupt/expired
- Menyebabkan koneksi HTTPS ke API Fonnte gagal

**Solusi yang Diterapkan:**

a) **Update WhatsAppService.php** - Tambahkan SSL verification handling:
   - Menambahkan check untuk environment (local vs production)
   - Jika `APP_ENV=local` dan `DISABLE_SSL_VERIFY=true` → disable SSL verification (untuk development)
   - Untuk production → gunakan default SSL verification

b) **Update .env** - Tambahkan flag untuk development:
   ```env
   DISABLE_SSL_VERIFY=true
   ```

**Untuk Production:**
Jika pindah ke production, pastikan:
1. Set `DISABLE_SSL_VERIFY=false` atau hapus dari .env
2. Pastikan server memiliki certificate bundle yang valid
3. Atau update path cacert.pem jika lokasi berbeda

---

### 2. **Masalah Status Awal "Butuh Bantuan Anda" (Seharusnya Otomatis)**

**Deskripsi Masalah:**
- User dengan nomor telepon yang terdaftar seharusnya otomatis mendapat pesan WhatsApp
- Tapi status tiket menunjukkan "pending_admin" (butuh bantuan manual admin)
- Ini berarti nomor telepon tidak terdeteksi

**Penyebab Kemungkinan:**
1. Relationship `siswa` tidak ter-load dengan benar
2. Field `telepon_orangtua` kosong pada siswa
3. Field `phone` kosong pada user
4. User bukan role 'siswa' tapi role lain

**Solusi yang Diterapkan:**

a) **Improve `determinePhoneTarget()` di UserRecoveryController:**
   - Gunakan `siswa()->first()` untuk explicit loading
   - Tambahkan logging detail untuk debug
   - Log akan menunjukkan:
     - Apakah user adalah siswa
     - Nomor telepon orang tua (jika ada)
     - Nomor telepon user
     - Kenapa nomor tidak terdeteksi

b) **Improve `store()` di UserRecoveryController:**
   - Tambahkan logging saat ticket dibuat
   - Log akan menunjukkan:
     - User role
     - User phone (dari users table)
     - Target phone yang terdeteksi
     - Status ticket yang diset

c) **Improve `resend()` di AdminRecoveryTicketController:**
   - Tambahan validasi nomor telepon manual
   - Cleaning format nomor (remove symbols)
   - Validation minimal 9 digit atau format internasional

---

## Cara Melihat Log untuk Debugging

**Lokasi Log:**
```
storage/logs/laravel.log
```

**Hal-hal yang dicatat:**
1. Saat user submit recovery request:
   ```
   Recovery Ticket Created {
       ticket_id, user_id, user_role, is_siswa, 
       user_phone, target_phone, status
   }
   ```

2. Saat determinePhoneTarget check:
   ```
   Checking siswa phone {
       user_id, siswa_id, siswa_exists, telepon_orangtua
   }
   ```
   atau
   ```
   Using user phone {
       user_id, phone
   }
   ```

3. Saat WA service mencoba kirim:
   ```
   WA_SENT to {phone} via Fonnte API
   ```
   atau error jika gagal

4. Saat admin resend manual:
   ```
   Admin resend recovery WhatsApp {
       ticket_id, type, phone, success
   }
   ```

---

## Testing Checklist

### Test Case 1: User Siswa dengan Telepon Orang Tua
```
1. Login sebagai admin
2. Pastikan siswa punya "telepon_orangtua" yang tidak kosong
3. User request recovery (lupa password)
4. EXPECTED: Status = "processing" → "sent"
           Pesan otomatis terkirim ke telepon_orangtua
5. Check log untuk memastikan phone terdeteksi dengan benar
```

### Test Case 2: User Siswa TANPA Telepon Orang Tua
```
1. User siswa tanpa telepon_orangtua
2. Tapi punya phone di users table
3. User request recovery
4. EXPECTED: Status = "processing" → "sent"
           Pesan terkirim ke phone di users table
5. Check log mengapa skip telepon_orangtua (harusnya kosong)
```

### Test Case 3: User Tanpa Nomor Telepon
```
1. User tanpa telepon_orangtua dan tanpa user.phone
2. User request recovery
3. EXPECTED: Status = "pending_admin"
           Admin notif untuk handle manual
4. Admin input nomor telepon manual di recovery-tickets menu
5. Admin klik "Kirim Ulang WA"
6. EXPECTED: Pesan terkirim ke nomor yang di-input admin
```

### Test Case 4: Check Error in Log
```
Setelah setiap test, check:
storage/logs/laravel.log

Cari untuk:
- "Recovery Ticket Created" - untuk verify logic ticket creation
- "Checking siswa phone" - untuk verify phone detection
- "WA Gateway Exception" atau "WA_SENT" - untuk verify pengiriman WA
```

---

## Informasi Konfigurasi

### WhatsApp Gateway (Fonnte)

**File:** `app/Services/WhatsAppService.php`

**Environment Variables (.env):**
```env
# Token dari Fonnte API
FONNTE_TOKEN=tmmRDoUo7hQVKeH9XpBE

# Untuk development saja - disable SSL verification
DISABLE_SSL_VERIFY=true
```

**API Endpoint:**
```
https://api.fonnte.com/send
```

**Request Format:**
```json
{
    "target": "628123456789",
    "message": "Pesan text",
    "countryCode": "62"
}
```

**Response Sukses:**
```json
{
    "status": true,
    "message": "Message sent"
}
```

**Mock Mode:**
Jika `FONNTE_TOKEN` kosong di .env, sistem akan berjalan di mock mode:
- Pesan hanya di-log, tidak benar-benar terkirim
- Cocok untuk development/testing

---

## Recovery Ticket Status Flow

```
User Submit Request
    ↓
[Ada nomor telepon?]
    ├─ YES → Status: "processing"
    │         │
    │         ├─ [Kirim WA otomatis berhasil?]
    │         │   ├─ YES → Status: "sent" (SELESAI)
    │         │   └─ NO  → Status: "pending_admin"
    │         │
    │         └─ User dapat pesan di WA
    │
    └─ NO  → Status: "pending_admin"
              │
              └─ Admin Notif untuk handle manual
                 Admin klik "Kirim Ulang WA"
                 (input nomor manual jika perlu)
                 Status: "sent" atau "failed"

Final Status → User click link di WA → Password baru
```

---

## Next Steps untuk Production

1. **SSL Certificate:**
   - Jangan gunakan `DISABLE_SSL_VERIFY=true` di production
   - Pastikan server memiliki trusted CA certificate

2. **Token Fonnte:**
   - Gunakan token production Fonnte, bukan development token

3. **Monitoring:**
   - Setup log rotation untuk `storage/logs/laravel.log`
   - Monitor error count harian
   - Alert jika ada spike error WA Gateway

4. **Testing:**
   - Test dengan number sebenarnya sebelum go-live
   - Pastikan format nomor sesuai Fonnte requirements

---

## Troubleshooting

### Masalah: "Gagal mengirim via API"

**Langkah Debug:**
1. Check `storage/logs/laravel.log` terbaru
2. Cari "WA Gateway Exception" atau error WA
3. Lihat detail error:
   - SSL error → pastikan DISABLE_SSL_VERIFY=true
   - HTTP error → check FONNTE_TOKEN
   - Connection error → check internet/firewall

### Masalah: Status tetap "pending_admin" padahal ada nomor

1. Check log "Checking siswa phone"
2. Verify field `telepon_orangtua` di siswa
3. Verify field `phone` di users
4. Pastikan user adalah role "siswa"

### Masalah: Nomor telepon error format

1. Admin harus input dengan benar:
   - Format: `08XX` atau `+62XX` atau `62XX`
   - Contoh: `081234567890` atau `+6281234567890`
   - Sistem otomatis clean dan format ulang

