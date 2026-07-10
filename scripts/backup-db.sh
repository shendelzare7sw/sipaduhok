#!/usr/bin/env bash
#
# Backup otomatis database SipaduHOK.
# - Kredensial dibaca dari .env Laravel (satu sumber, selalu sinkron).
# - Password TIDAK muncul di daftar proses (pakai --defaults-extra-file sementara).
# - Dump konsisten (InnoDB) tanpa mengunci tabel (--single-transaction).
# - Dikompres gzip + retensi otomatis.
#
# Pasang di VPS:
#   sudo cp scripts/backup-db.sh /usr/local/bin/backup-db-sipaduhok.sh
#   sudo chmod 700 /usr/local/bin/backup-db-sipaduhok.sh
#   sudo /usr/local/bin/backup-db-sipaduhok.sh        # uji manual sekali
#
# Jadwalkan (harian 02:00) — lihat catatan cron di bawah file ini.

set -euo pipefail

# ── Konfigurasi (sesuaikan bila perlu) ───────────────────────────────
APP_DIR="/www/wwwroot/app.sipaduhok.id"
BACKUP_DIR="/var/backups/sipaduhok/db"   # WAJIB di luar web root (jangan bisa diunduh publik)
RETENTION_DAYS=14                         # simpan 14 hari terakhir
# ─────────────────────────────────────────────────────────────────────

ENV_FILE="$APP_DIR/.env"
[ -r "$ENV_FILE" ] || { echo "ERROR: .env tak terbaca di $ENV_FILE" >&2; exit 1; }

# Ambil satu nilai dari .env (buang kutip & CR)
env_get() {
    grep -E "^$1=" "$ENV_FILE" | head -1 | cut -d= -f2- | tr -d '\r' | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

DB_DATABASE="$(env_get DB_DATABASE)"
DB_USERNAME="$(env_get DB_USERNAME)"
DB_PASSWORD="$(env_get DB_PASSWORD)"
DB_HOST="$(env_get DB_HOST)"; DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="$(env_get DB_PORT)"; DB_PORT="${DB_PORT:-3306}"

[ -n "$DB_DATABASE" ] || { echo "ERROR: DB_DATABASE kosong" >&2; exit 1; }

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"

STAMP="$(date +%F_%H%M%S)"
OUT="$BACKUP_DIR/${DB_DATABASE}_${STAMP}.sql.gz"

# File kredensial sementara (agar password tak muncul di `ps`)
CNF="$(mktemp)"
trap 'rm -f "$CNF"' EXIT
chmod 600 "$CNF"
cat > "$CNF" <<EOF
[client]
user=${DB_USERNAME}
password=${DB_PASSWORD}
host=${DB_HOST}
port=${DB_PORT}
EOF

# Dump → gzip. pipefail memastikan skrip gagal bila mysqldump gagal (rotasi tak jalan).
mysqldump --defaults-extra-file="$CNF" \
    --single-transaction --quick --routines --triggers --events \
    --no-tablespaces "$DB_DATABASE" | gzip -9 > "$OUT"

# Sanity check: file ada & tak kosong
[ -s "$OUT" ] || { echo "ERROR: hasil backup kosong, dibatalkan" >&2; rm -f "$OUT"; exit 1; }

# Retensi: hapus backup lebih tua dari RETENTION_DAYS
find "$BACKUP_DIR" -name "${DB_DATABASE}_*.sql.gz" -type f -mtime +"$RETENTION_DAYS" -delete

echo "$(date '+%F %T') OK: $OUT ($(du -h "$OUT" | cut -f1))"

# ── (Opsional) Salin off-site — SANGAT disarankan ────────────────────
# Backup di server yang sama tidak melindungi dari server hilang/terkena ransomware.
# Aktifkan salah satu bila sudah dikonfigurasi:
#   rclone copy "$OUT" remote-cloud:sipaduhok-backup/   # butuh `rclone config`
#   aws s3 cp "$OUT" s3://nama-bucket/db/               # butuh AWS CLI
# ─────────────────────────────────────────────────────────────────────
