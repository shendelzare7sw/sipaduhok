# Cost Impact

Source tidak menyimpan tabel tarif tetap, MDR, withdrawal fee, atau settlement fee. `GET /payment-methods` membawa `fee.flatIdr`, `fee.percentBps`, min/max; aplikasi memakai data itu untuk tampilan/validasi tetapi tidak mengunci nominal publik dalam repository. `pembayaran.gateway_total` menyimpan total provider dan invoice menghitung fee sebagai `gateway_total - subtotal`.

`paywuz_fee_by_merchant=true` berarti sekolah/merchant menanggung fee pada create; `false` berarti payer/customer. Kebijakan dapat diubah Admin/Bendahara dan perlu keputusan mitra serta bukti konfigurasi final.

Nominal final: **PUBLIC PRICING REQUIRED**. Halaman resmi [Paywuz Pricing](https://paywuz.id/pricing) harus menjadi rujukan saat Cost Budgeting diperbarui, lalu cocokkan dengan kontrak/dashboard merchant. Jangan menyalin nominal dari katalog audit karena dapat spesifik project dan berubah. Masukkan skenario volume, fee bearer, settlement/withdrawal bila tercantum resmi, serta dampak selisih `gateway_total`.
