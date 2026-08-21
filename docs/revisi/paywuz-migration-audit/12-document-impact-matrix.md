# Document Impact Matrix

| Document | Affected? | Old Statement | Required New Statement | Source Evidence | Priority |
|---|---|---|---|---|---|
| Spesifikasi Kebutuhan Sistem | Yes | Midtrans/Snap | Paywuz hosted payment, wali sebagai aktor, channel dinamis, authoritative status | routes/services/controller | MAJOR |
| BAST Requirement | Yes | Baseline provider Midtrans | Addendum/trace perubahan provider yang disetujui; jangan backdate | migration audit | MODERATE |
| Komparasi SDLC | Yes | Artefak iterasi Midtrans | Catat perubahan kebutuhan mitra dan iterasi prototype Paywuz | git history | MINOR |
| FSD | Yes | Snap flow/status Midtrans | Final flow pada `03`, failure/retry/change channel | source | MAJOR |
| TSD | Yes | Midtrans SDK/signature/schema | REST Bearer, HMAC-SHA256, fields/table/config Paywuz | service/migrations | MAJOR |
| BAST FSD/TSD | Yes | Approval desain lama | Addendum approval perubahan desain jika belum ditandatangani | change map | MODERATE |
| Dokumentasi API Payment | Yes | Snap/Transaction/webhook Midtrans | Endpoint/payload/headers/status Paywuz + webhook defect | `04`, official SDK | MAJOR |
| Test Case Positive/Negative | Yes | TC-P-044/045 dan rules Midtrans | Pertahankan ID, revisi provider/flow; tambah contract webhook coverage | tests/CSV | MAJOR |
| SIT | Yes | E2E/API/callback Midtrans | Paywuz REST/webhook/status/idempotency/DB sync | tests/source | MAJOR |
| BAST SIT | Yes | Hasil SIT lama | Jangan final sebelum webhook fix/retest dan external evidence | test report | MAJOR |
| UAT | Yes | Snap user journey | Pilih channel, hosted checkout, sync/status/invoice/history | UI/routes | MAJOR |
| BAST UAT | Yes | Acceptance flow lama | Retest user-facing Paywuz setelah fix; signature manusia | UAT impact | MAJOR |
| Deployment | Yes | Composer/config/callback Midtrans | Remove package; env Paywuz; migrate; callback route; scheduler decision | diff/config | MAJOR |
| BAST Deployment | Yes | Deployment provider lama | Bukti config/migration/health tanpa secret | production audit | MODERATE |
| Cost Budgeting | Yes | Tarif Midtrans | Fee dinamis Paywuz, payer/merchant policy; nominal perlu pricing resmi | API/settings | MODERATE |
| BA Serah Terima Akhir | Reference only | Dokumen telah ditandatangani | Jangan ubah; lampirkan addendum/catatan migrasi terpisah | user constraint | NO CHANGE |

Dokumen discovery/stage-1 dan generator PowerShell juga masih menyebut Midtrans; perbarui sebagai sumber kerja sebelum regenerasi dokumen, tanpa mengedit DOCX pada audit ini.
