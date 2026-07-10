# Hardening Server — Cloudflare + Nginx + PHP-FPM + Laravel

Panduan mengeraskan server setelah **memasang Cloudflare (proxied / awan oranye)** di depan VPS.
Dikerjakan di branch `add-cloudflare`. Semua perintah di bawah dijalankan **di VPS** (butuh `sudo`/root),
kecuali bagian Laravel yang sudah ada di kode.

## Arsitektur

```
Pengunjung → Cloudflare (WAF/DDoS/HTTPS) → Nginx (VPS) → PHP-FPM → Laravel
```

Kunci keamanan: **(a)** paksa semua trafik lewat Cloudflare (firewall origin), **(b)** kembalikan IP asli
pengunjung di Nginx (real_ip) supaya log, rate-limit, dan fail2ban benar.

---

## ⭐ Urutan prioritas

| # | Langkah | Kenapa penting |
|---|---------|----------------|
| 1 | **Firewall: origin hanya terima IP Cloudflare** | Tanpa ini, penyerang bisa **bypass** Cloudflare dengan menyerang IP VPS langsung → proteksi DDoS/WAF sia-sia. **Paling kritis.** |
| 2 | **Nginx real_ip** (CF ranges + `CF-Connecting-IP`) | IP asli pengunjung terbaca oleh Nginx log, rate-limit, fail2ban, **dan** Laravel sekaligus. |
| 3 | **Cloudflare SSL = Full (Strict)** | Enkripsi CF→origin; Laravel deteksi HTTPS benar. |
| 4 | **fail2ban** | Blokir IP yang abusive (brute-force, scan). |
| 5 | **Security headers** | Sudah dikirim Laravel — jangan dobel di Nginx. |
| 6 | **`server_tokens off`** | Sembunyikan versi Nginx. |
| 7 | **Rate-limit login** | Laravel sudah (captcha + throttle); Nginx `limit_req` opsional (lapis tambahan). |

---

## Langkah 1 — Laravel Trust Proxies: **TIDAK perlu diubah**

Laravel 11 tak punya `app/Http/Middleware/TrustProxies.php`; konfigurasinya di
`bootstrap/app.php` → `trustProxies(at: ['127.0.0.1', '::1'])`.

**Kenapa loopback sudah benar:** setelah **Nginx real_ip** (Langkah 2) aktif, Nginx menulis
`REMOTE_ADDR` = IP asli pengunjung sebelum meneruskan ke PHP-FPM. Jadi `$request->ip()` di Laravel
sudah = IP asli **tanpa** perlu Laravel menyimpan daftar IP Cloudflare. Jangan pakai `at: '*'`
(berbahaya: header IP bisa dipalsukan).

> Deteksi HTTPS: pastikan blok SSL Nginx mengirim `fastcgi_param HTTPS on;` (lihat Langkah 3).

---

## Langkah 2 — Nginx real_ip (Cloudflare)

Buat file conf yang di-generate dari daftar IP Cloudflare **terbaru** (disarankan via script agar
selalu update). Jalankan di VPS:

```bash
sudo tee /usr/local/bin/update-cf-realip.sh >/dev/null <<'EOF'
#!/usr/bin/env bash
set -e
CONF=/etc/nginx/conf.d/cloudflare-realip.conf
{
  echo "# Auto-generated $(date). Jangan edit manual."
  for ip in $(curl -fsSL https://www.cloudflare.com/ips-v4) $(curl -fsSL https://www.cloudflare.com/ips-v6); do
    echo "set_real_ip_from $ip;"
  done
  echo "real_ip_header CF-Connecting-IP;"
} > "$CONF"
nginx -t && systemctl reload nginx
EOF
sudo chmod +x /usr/local/bin/update-cf-realip.sh
sudo /usr/local/bin/update-cf-realip.sh
```

Perbarui otomatis tiap minggu (IP CF jarang berubah, tapi biar aman):
```bash
echo '0 3 * * 0 root /usr/local/bin/update-cf-realip.sh >/dev/null 2>&1' | sudo tee /etc/cron.d/cf-realip
```

> **Verifikasi:** setelah reload, cek `access.log` Nginx — kolom IP harus IP asli pengunjung,
> bukan `104.x`/`172.x` milik Cloudflare.

---

## Langkah 3 — Cloudflare SSL & HTTPS ke origin

Di dashboard Cloudflare (SSL/TLS):
- **SSL/TLS encryption mode → Full (Strict)** (bukan Flexible). Origin harus punya sertifikat valid
  (Let's Encrypt) atau **Cloudflare Origin Certificate**.
- **Always Use HTTPS → On** (sudah kamu aktifkan).
- **HSTS → Enable** (di Cloudflare). Karena HSTS dikelola di Cloudflare, aman; Laravel juga mengirim
  HSTS saat HTTPS — dobel HSTS tidak berbahaya, tapi kalau mau satu sumber, cukup di Cloudflare.

Di blok server SSL Nginx, pastikan PHP tahu ini HTTPS (cegah 419/redirect loop):
```nginx
location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param HTTPS on;                 # ← penting untuk deteksi HTTPS Laravel
    fastcgi_pass unix:/run/php/php8.x-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

---

## Langkah 4 (KRITIS) — Firewall: origin hanya terima Cloudflare

Supaya penyerang tak bisa melewati Cloudflare dengan menyerang IP VPS langsung. Contoh dengan `ufw`:

```bash
# SSH dari IP-mu saja (ganti X.X.X.X dengan IP kantor/rumahmu; JANGAN kunci diri sendiri!)
sudo ufw allow from X.X.X.X to any port 22 proto tcp

# 80/443 HANYA dari rentang Cloudflare
for ip in $(curl -fsSL https://www.cloudflare.com/ips-v4) $(curl -fsSL https://www.cloudflare.com/ips-v6); do
  sudo ufw allow from "$ip" to any port 80 proto tcp
  sudo ufw allow from "$ip" to any port 443 proto tcp
done

sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw enable
sudo ufw status numbered
```

> ⚠️ Pastikan aturan SSH sudah benar SEBELUM `ufw enable`, agar tak terkunci dari server.

---

## Langkah 5 — fail2ban

```bash
sudo apt update && sudo apt install fail2ban -y
sudo systemctl enable --now fail2ban
```

Jail dasar untuk Nginx (butuh Nginx real_ip aktif agar mem-ban IP asli, bukan IP Cloudflare):
```bash
sudo tee /etc/fail2ban/jail.local >/dev/null <<'EOF'
[DEFAULT]
bantime  = 1h
findtime = 10m
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled  = true
filter   = nginx-limit-req
logpath  = /var/log/nginx/error.log

[nginx-botsearch]
enabled  = true
logpath  = /var/log/nginx/access.log
EOF
sudo systemctl restart fail2ban
sudo fail2ban-client status
```

---

## Langkah 6 — Security headers (JANGAN dobel)

Laravel **sudah** mengirim header ini via `app/Http/Middleware/SecurityHeaders.php`:
`X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`,
`Permissions-Policy`, `Content-Security-Policy` (minimal), `Strict-Transport-Security` (saat HTTPS).

➡️ **Jangan menambah header yang sama di Nginx** (akan dobel). Kalau lebih suka mengelolanya di Nginx,
hapus dulu dari `SecurityHeaders.php`. Rekomendasi: **biarkan di Laravel** (sudah jalan) dan lewati di Nginx.

Cek dobel setelah live:
```bash
curl -sI https://app.sipaduhok.id | grep -iE "x-frame-options|x-content-type|referrer-policy|permissions-policy|strict-transport|content-security"
```
Tiap header harus muncul **sekali**.

---

## Langkah 7 — Sembunyikan versi Nginx

Di `/etc/nginx/nginx.conf` bagian `http { ... }`:
```nginx
server_tokens off;
```
Lalu `sudo nginx -t && sudo systemctl reload nginx`.

---

## Langkah 8 — Rate-limit login (opsional, lapis tambahan)

Laravel **sudah** membatasi login (captcha + 5 percobaan/menit + lockout). Kalau mau lapis di Nginx:

Di `http { ... }`:
```nginx
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
```
Di server block, pada lokasi login:
```nginx
location = /login {
    limit_req zone=login burst=5 nodelay;
    try_files $uri /index.php?$query_string;
}
```

---

## ✅ Verifikasi akhir
1. `curl -sI https://app.sipaduhok.id` → status 200, header keamanan muncul sekali, ada `cf-ray` (lewat Cloudflare).
2. Coba akses langsung IP VPS di port 80/443 dari luar → harus **timeout/refused** (firewall bekerja).
3. Nginx `access.log` menampilkan IP asli pengunjung (real_ip bekerja).
4. Login normal (tak ada 419). Salah password 5× → terkunci sementara.
5. `sudo fail2ban-client status` → jail aktif.
