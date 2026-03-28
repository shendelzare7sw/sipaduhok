<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->kode_pembayaran }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }

        .no-print {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-outline-secondary {
            background-color: #6c757d;
        }

        /* Specific Print Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-area {
                box-shadow: none !important;
                border: none !important;
            }
            @page {
                size: A5;
                margin: 0;
            }
        }

        .print-area {
            background: white;
            max-width: 500px; /* A5 width approx */
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Receipt Styles */
        .receipt-container {
            font-family: 'Courier New', monospace;
            padding: 10px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .receipt-header p {
            margin: 3px 0;
            font-size: 11px;
            color: #555;
        }

        .receipt-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }

        .receipt-title h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .receipt-info {
            margin: 15px 0;
        }

        .receipt-info table {
            width: 100%;
            font-size: 12px;
        }

        .receipt-info td {
            padding: 4px 0;
            vertical-align: top;
        }

        .receipt-info td:first-child {
            width: 120px;
            font-weight: bold;
        }

        .receipt-detail {
            margin: 20px 0;
            border-top: 1px dashed #999;
            border-bottom: 1px dashed #999;
            padding: 15px 0;
        }

        .receipt-detail table {
            width: 100%;
            font-size: 12px;
        }

        .receipt-detail th,
        .receipt-detail td {
            padding: 5px 0;
            text-align: left;
        }

        .receipt-detail th {
            border-bottom: 1px solid #333;
            font-weight: bold;
        }

        .receipt-total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 0;
            border-top: 2px solid #333;
            margin-top: 10px;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 60px;
            margin-bottom: 5px;
        }

        .receipt-note {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            border-top: 2px dashed #333;
            padding-top: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        .status-disetujui {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>

    {{-- Action Buttons --}}
    <div class="no-print">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Kwitansi
        </button>
    </div>

    {{-- Receipt Area --}}
    <div class="print-area">
        <div class="receipt-container">
            {{-- Header --}}
            <div class="receipt-header">
                <h2>{{ preg_replace('/\s*\(Gedung\s+\w+\)$/i', '', $schoolInfo['nama']) }}</h2>
                <p>{{ $schoolInfo['alamat'] }}</p>
                <p>Telp: {{ $schoolInfo['telepon'] }} | Email: {{ $schoolInfo['email'] }}</p>
            </div>

            {{-- Title --}}
            <div class="receipt-title">
                <h3>KWITANSI PEMBAYARAN</h3>
            </div>

            {{-- Payment Info --}}
            <div class="receipt-info">
                <table>
                    <tr>
                        <td>No. Kwitansi</td>
                        <td>: {{ $pembayaran->kode_pembayaran }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Bayar</td>
                        <td>: {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Metode</td>
                        <td>: {{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>: <span class="status-badge status-{{ $pembayaran->status_validasi }}">
                            {{ ucfirst($pembayaran->status_validasi) }}
                        </span></td>
                    </tr>
                </table>
            </div>

            {{-- Student Info --}}
            <div class="receipt-info">
                <table>
                    <tr>
                        <td>Nama Siswa</td>
                        <td>: {{ $pembayaran->siswa->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIS</td>
                        <td>: {{ $pembayaran->siswa->nis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Payment Detail --}}
            <div class="receipt-detail">
                <table>
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th style="text-align: right;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatedPayments as $payment)
                        <tr>
                            <td>
                                <div style="font-weight: bold;">
                                    {{ $jenisTagihan[$payment->tagihan->jenis_tagihan ?? ''] ?? ucfirst(str_replace('_', ' ', $payment->tagihan->jenis_tagihan ?? 'Tagihan')) }}
                                    @if($payment->tagihan && $payment->tagihan->bulan)
                                        - {{ \Carbon\Carbon::create()->month($payment->tagihan->bulan)->translatedFormat('F') }}
                                    @endif
                                </div>
                                <div style="font-size: 10px; color: #555; margin-top: 4px; line-height: 1.4;">
                                    Total Tagihan: Rp {{ number_format($payment->tagihan->jumlah ?? 0, 0, ',', '.') }}<br>
                                    @if($payment->is_lunas)
                                        Status: <span style="color: #198754; font-weight: bold;">LUNAS</span>
                                    @else
                                        Kekurangan Pembayaran: <span style="color: #dc3545; font-weight: bold;">Rp {{ number_format($payment->sisa_current, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: right; vertical-align: top;">
                                <div style="font-weight: bold; font-size: 13px;">
                                    Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="receipt-total">
                    Total: Rp {{ number_format($totalBayar, 0, ',', '.') }}
                </div>
            </div>

            {{-- Terbilang --}}
            <div class="receipt-info">
                <table>
                    <tr>
                        <td colspan="2">
                            <strong>Terbilang:</strong><br>
                            <em>{{ terbilang($totalBayar) }} Rupiah</em>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Catatan --}}
            @if($pembayaran->catatan)
            <div class="receipt-info">
                <table>
                    <tr>
                        <td>Catatan</td>
                        <td>: {{ $pembayaran->catatan }}</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Signature --}}
            <div class="signature-area">
                <div class="signature-box">
                    <p>Penerima,</p>
                    <div class="signature-line"></div>
                    <p style="white-space: nowrap;">({{ $pembayaran->validator->nama_lengkap ?? 'Bendahara' }})</p>
                </div>
                <div class="signature-box">
                    <p>Pembayar,</p>
                    <div class="signature-line"></div>
                    <p style="white-space: nowrap;">({{ $parentName }})</p>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="receipt-note">
                <p>Kwitansi ini merupakan bukti pembayaran yang sah.</p>
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
                @if($pembayaran->tanggal_validasi)
                <p>Divalidasi pada: {{ \Carbon\Carbon::parse($pembayaran->tanggal_validasi)->translatedFormat('d F Y H:i') }} WIB</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
