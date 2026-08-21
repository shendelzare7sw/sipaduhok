# Payment Status Mapping

| Paywuz Status | Pembayaran | Tagihan | Action | Notification | Audit | Retry/Idempotency |
|---|---|---|---|---|---|---|
| `pending` | `pending` | dihitung ulang; tidak mengurangi sisa | Simpan reference, channel, total, expiry, URL | Tidak | Hanya bila status internal berubah | Repeated apply tidak memberi efek finansial ganda |
| `settlement` | `disetujui` | `updateStatusBayar()` menjadi cicilan/lunas sesuai total approved | Isi `gateway_settled_at`; tutup duplikat aman | Admin, Bendahara, wali pada transisi pertama | old→approved | Payment approved tidak dapat diturunkan; delivery ID dedupe |
| `success` | `disetujui` | sama dengan settlement | Approve tanpa settled timestamp khusus | sama | sama | sama |
| `failed` | `ditolak` | dihitung ulang tanpa payment ini | Simpan failure status | Tidak | old→rejected | Retry status yang sama idempotent |
| `cancelled` | `ditolak` | dihitung ulang | Dipakai juga untuk replacement yang berhasil dibatalkan | Tidak | old→rejected | Remote cancel harus dikonfirmasi sebelum local close |
| `expired` | `ditolak` | dihitung ulang | Ditangani melalui event failed pada desain controller | Tidak | old→rejected | Retry status yang sama idempotent |
| status lain/kosong | `pending` | tidak mengurangi sisa | Pertahankan menunggu | Tidak | Bila transisi internal terjadi | Dapat disinkronkan ulang |
| local `replaced` | `ditolak` | tidak mengurangi sisa | Penanda order lama setelah ganti channel | Tidak | tercatat saat status berubah | Bukan status Paywuz; order remote harus dibatalkan dahulu |

Kontrol penting:

- Jumlah seluruh row pada satu order harus sama dengan `amount` provider.
- `transaction_id` yang telah ada harus sama dengan reference callback.
- Transaksi yang sudah `disetujui` tidak didowngrade oleh callback terlambat.
- Notifikasi hanya dikirim untuk koleksi row yang baru menjadi approved.
- Kekurangan: callback resmi saat ini tertolak sebelum matrix ini dijalankan; polling/status API tetap dapat menjalankannya.
