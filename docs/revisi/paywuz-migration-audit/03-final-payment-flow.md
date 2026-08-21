# Final Payment Flow

## Aktor dan kewenangan

- Admin/Bendahara membuat tagihan, mengelola konfigurasi kanal, mencatat pembayaran manual, dan melihat laporan.
- Orang Tua/Wali Siswa adalah satu-satunya aktor yang memulai pembayaran Paywuz untuk anak tertaut.
- Siswa hanya dapat melihat riwayat/bukti pada route lama yang masih tersedia; `prosesBayar()` menolak aksi bayar. Siswa tidak menjadi payment actor.
- Paywuz menjadi sumber status digital otoritatif; redirect browser tidak melunasi transaksi.

## Alur aktual end-to-end

1. Admin/Bendahara membuat tagihan.
2. Wali membuka tagihan anak. Controller memastikan anak tertaut ke akun dan tagihan benar-benar milik anak.
3. Wali memilih satu tagihan atau beberapa tagihan, nominal valid, `paywuz`, dan `payment_method` yang berasal dari katalog Paywuz. Direct Transfer adalah jalur manual terpisah bila toggle dan rekening aktif.
4. Server memeriksa sisa berdasarkan pembayaran `disetujui`, batas tahun ajaran/carryover, nominal, ketersediaan kanal, dan batas min/max provider.
5. Dalam transaksi database, server membuat satu atau beberapa `pembayaran` pending dengan satu `order_id` untuk bulk, environment snapshot, wali pembayar, dan kanal terpilih.
6. Server memanggil `POST /transactions` Paywuz dengan order, amount total, channel, expiry, redirect URL, fee policy, serta metadata ID lokal.
7. Response diperiksa: order dan amount harus sama; ID/status/payment URL diperlukan. URL hanya diterima jika HTTPS pada host Paywuz tepercaya. Data gateway disimpan.
8. Bila create gagal, server mencari order yang sama melalui status API. Jika benar-benar belum terbentuk, local pending tetap ada, attempt/error dicatat, dan user dapat melanjutkan. Order ID yang sama mencegah create ganda.
9. Wali membuka halaman pembayaran digital lalu payment URL Paywuz. Untuk meta-channel `VA`, bank dipilih di hosted page dan hasil akhir dapat menjadi kode bank/VA spesifik.
10. Status dapat masuk melalui webhook, tombol sync/page load, atau perintah `paywuz:reconcile`. Command belum terjadwal otomatis.
11. Status service memverifikasi order dan jumlah total, mengunci row, memetakan status, menyimpan reference/method/total/expiry/raw response, memperbarui status pembayaran dan tagihan, mencatat audit, dan mengirim notifikasi hanya pada transisi baru ke approved.
12. Pending lain pada tagihan yang sudah lunas ditutup. Order Paywuz lain harus dibatalkan remote dahulu; bila pembatalan tidak pasti, local order tetap pending agar transaksi yang masih dapat dibayar tidak disembunyikan.
13. Invoice/riwayat menampilkan kanal akhir, subtotal, fee (selisih total provider), total, dan status.

## Perbedaan yang terlihat dari Midtrans

Tidak ada lagi Snap popup atau `snap-finish`. User melihat pilihan kanal dinamis, halaman payment Paywuz, payment URL/instruction, tombol sinkronisasi/lanjutkan, dan ganti metode. Status tetap berasal dari server/provider, bukan parameter redirect.

## Batas audit

Alur REST dan database terbukti. Alur callback provider aktual **belum dapat dianggap bekerja** karena kontrak webhook controller tidak cocok dengan payload datar pada SDK resmi Paywuz. Lihat `07-webhook-security-audit.md`.
