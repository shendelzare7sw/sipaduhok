<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $pembayaran->kode_pembayaran }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #696cff;
            --secondary: #8592a3;
            --success: #71dd37;
            --warning: #ffab00;
            --danger: #ff3e1d;
            --dark: #233446;
            --light: #f5f5f9;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f9;
            color: #566a7f;
            line-height: 1.5;
            margin: 0;
            padding: 40px 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            position: relative;
        }

        /* Watermark for paid status */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            font-weight: 900;
            color: rgba(113, 221, 55, 0.1);
            border: 10px solid rgba(113, 221, 55, 0.1);
            padding: 20px 40px;
            border-radius: 20px;
            text-transform: uppercase;
            pointer-events: none;
            z-index: 0;
        }

        .watermark.pending {
            color: rgba(255, 171, 0, 0.1);
            border-color: rgba(255, 171, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        .company-info h1 {
            margin: 0 0 5px;
            color: var(--primary);
            font-size: 18px;
        }

        .company-info p {
            margin: 0;
            font-size: 14px;
            color: var(--secondary);
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
            letter-spacing: -1px;
        }

        .invoice-meta {
            margin-top: 10px;
            font-size: 14px;
        }

        .meta-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 4px;
        }
        
        .meta-label {
            font-weight: 600;
            color: var(--secondary);
            width: 100px;
            text-align: right;
        }

        .meta-value {
            font-weight: 500;
            color: var(--dark);
            width: 180px;
            text-align: right;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-box h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--secondary);
            margin: 0 0 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .info-box p {
            margin: 0 0 5px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .info-box .sub-text {
            font-weight: 400;
            color: var(--secondary);
            font-size: 13px;
        }

        .table-container {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f9f9fc;
            color: var(--secondary);
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: var(--dark);
        }

        .amount-col {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .total-box {
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .total-row.grand-total {
            border-top: 2px solid var(--dark);
            border-bottom: none;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: var(--secondary);
            border-top: 1px solid #f0f0f0;
            padding-top: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-paid { background: rgba(113, 221, 55, 0.15); color: var(--success); }
        .status-pending { background: rgba(255, 171, 0, 0.15); color: var(--warning); }
        .status-failed { background: rgba(255, 62, 29, 0.15); color: var(--danger); }

        @page {
            size: A4;
            margin: 10mm 15mm;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body { padding: 15px 10px; }
            .invoice-container { padding: 20px; }
            .header { flex-direction: column; align-items: flex-start; gap: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; }
            .invoice-details { text-align: left; width: 100%; }
            .meta-row { justify-content: flex-start; }
            .meta-label { text-align: left; width: 90px; }
            .meta-value { text-align: left; width: auto; flex: 1; }
            .info-grid { grid-template-columns: 1fr; gap: 20px; }
            .info-box { text-align: left !important; }
            .table-container { overflow-x: auto; margin-bottom: 20px; }
            .total-section { justify-content: flex-start; width: 100%; }
            .total-box { width: 100%; }
            .watermark { font-size: 4rem; padding: 10px 20px; border-width: 5px; }
            
            /* Buttons */
            .action-buttons {
                display: flex; flex-direction: column; gap: 10px; text-align: center;
            }
            .action-buttons button { margin-left: 0 !important; width: 100%; }
        }

        @media print {
            body { 
                background: white; 
                padding: 0;
                margin: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .watermark {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="no-print action-buttons" style="max-width: 800px; margin: 0 auto 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #696cff; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
            <svg style="width:16px;height:16px;vertical-align:middle;margin-right:5px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Invoice
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #8592a3; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="invoice-container">
        <!-- Watermark Status -->
        @if($pembayaran->status_validasi == 'disetujui')
            <div class="watermark">PAID</div>
        @elseif($pembayaran->status_validasi == 'pending')
            <div class="watermark pending">PENDING</div>
        @else
            <div class="watermark pending" style="color:rgba(255,62,29,0.1); border-color:rgba(255,62,29,0.1)">REJECTED</div>
        @endif

        <div class="header">
            <div class="company-info" style="display: flex; align-items: center; gap: 14px;">
                <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" style="height: 70px; width: auto; flex-shrink: 0;">
                <div>
                    <h1>{{ preg_replace('/\s*\(Gedung\s+\w+\)$/i', '', $schoolInfo['nama']) }}</h1>
                    <p>{{ $schoolInfo['alamat'] }}</p>
                    <p>Email: {{ $schoolInfo['email'] }} | Telp: {{ $schoolInfo['telepon'] }}</p>
                </div>
            </div>
            <div class="invoice-details">
                <h2 class="invoice-title">INVOICE</h2>
                <div class="invoice-meta">
                    <div class="meta-row">
                        <span class="meta-label">No. Invoice:</span>
                        <span class="meta-value">INV-{{ $pembayaran->kode_pembayaran }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Tanggal:</span>
                        <span class="meta-value">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->isoFormat('D MMMM Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Metode:</span>
                        <span class="meta-value">
                            @if($pembayaran->metode_pembayaran == 'midtrans')
                                Digital Payment (Midtrans)
                            @elseif($pembayaran->metode_pembayaran == 'transfer')
                                Transfer Bank
                            @else
                                Tunai
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Ditagihkan Kepada:</h3>
                <p style="font-size: 18px;">{{ $siswa->nama_lengkap }}</p>
                <p class="sub-text">NIS: {{ $siswa->nis }}</p>
                <p class="sub-text">Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }} | Cabang: {{ $siswa->cabang->nama_cabang ?? '-' }}</p>
            </div>
            <div class="info-box" style="text-align: right;">
                <h3>Status Pembayaran:</h3>
                <div style="margin-top: 5px;">
                    @if($pembayaran->status_validasi == 'disetujui')
                        <span class="status-badge status-paid">LUNAS / TERVERIFIKASI</span>
                    @elseif($pembayaran->status_validasi == 'pending')
                        <span class="status-badge status-pending">MENUNGGU PEMBAYARAN</span>
                    @else
                         <span class="status-badge status-failed">DIBATALKAN / DITOLAK</span>
                    @endif
                </div>
                @if($pembayaran->metode_pembayaran == 'transfer')
                    <p class="sub-text" style="margin-top: 10px;">Bukti Transfer: Terlampir</p>
                @endif
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 55%">Deskripsi Tagihan</th>
                        <th style="width: 20%">Tahun Ajaran</th>
                        <th style="width: 20%; text-align: right;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan)) }}</strong>
                            @if($item->keterangan)
                                <br><small class="text-muted">{{ $item->keterangan }}</small>
                            @endif
                        </td>
                        <td>{{ $item->tagihan->tahunAjaran->nama_tahun_ajaran ?? '-' }}</td>
                        <td class="amount-col">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($items->sum('jumlah_bayar'), 0, ',', '.') }}</span>
                </div>
                {{-- 
                <div class="total-row">
                    <span>Biaya Admin</span>
                    <span>Rp 0</span>
                </div>
                --}}
                <div class="total-row grand-total">
                    <span>TOTAL BAYAR</span>
                    <span>Rp {{ number_format($items->sum('jumlah_bayar'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah melakukan pembayaran tepat waktu.</p>
            <p style="margin-top: 5px; font-size: 11px; color: #b0b0b0;">Invoice ini sah dan diproses secara otomatis oleh komputer. Tanda tangan basah tidak diperlukan.</p>
        </div>
    </div>

</body>
</html>
