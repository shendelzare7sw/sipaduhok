<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran - {{ $pembayaran->kode_pembayaran }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #165fac;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #165fac;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .kode-pembayaran {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            border: 2px dashed #165fac;
        }
        .kode-pembayaran h2 {
            color: #165fac;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background: #165fac;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #666;
        }
        .total-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 2px solid #165fac;
        }
        .total-box h3 {
            color: #165fac;
            font-size: 18px;
            margin-bottom: 8px;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #165fac;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background: #d1fae5;
            color: #065f46;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #999;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .signature-box {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box div {
            display: inline-block;
            text-align: center;
            padding: 10px 30px;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 60px auto 5px auto;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>BUKTI PEMBAYARAN</h1>
        <p>PKBM House of Knowledge</p>
        <p>Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan</p>
    </div>

    <!-- Kode Pembayaran -->
    <div class="kode-pembayaran">
        <h2>{{ $pembayaran->kode_pembayaran }}</h2>
        <p style="color: #666; font-size: 12px;">Kode Transaksi</p>
    </div>

    <!-- Data Siswa -->
    <div class="section">
        <div class="section-title">INFORMASI SISWA</div>
        <table>
            <tr>
                <td>Nama Siswa</td>
                <td>: {{ $pembayaran->siswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>NISN</td>
                <td>: {{ $pembayaran->siswa->nisn }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Detail Pembayaran -->
    <div class="section">
        <div class="section-title">DETAIL PEMBAYARAN</div>
        <table>
            <tr>
                <td>Jenis Tagihan</td>
                <td>: {{ $pembayaran->tagihan->jenis_tagihan }}</td>
            </tr>
            <tr>
                <td>Tanggal Pembayaran</td>
                <td>: {{ $pembayaran->tanggal_bayar->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>: {{ $pembayaran->metode_pembayaran === 'transfer' ? 'DIRECT TRANSFER' : strtoupper($pembayaran->metode_pembayaran) }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: 
                    <span class="status-badge">
                        {{ $pembayaran->status_validasi === 'disetujui' ? 'LUNAS' : strtoupper($pembayaran->status_validasi) }}
                    </span>
                </td>
            </tr>
            @if($pembayaran->tanggal_validasi)
            <tr>
                <td>Tanggal Validasi</td>
                <td>: {{ $pembayaran->tanggal_validasi->format('d F Y, H:i') }} WIB</td>
            </tr>
            @endif
            @if($pembayaran->validator)
            <tr>
                <td>Divalidasi Oleh</td>
                <td>: {{ $pembayaran->validator->name }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Total -->
    <div class="total-box">
        <h3>Total Pembayaran</h3>
        <div class="total-amount">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</div>
        <p style="color: #666; font-size: 12px; margin-top: 5px;">
            ({{ ucwords(\Terbilang::make($pembayaran->jumlah_bayar)) }} Rupiah)
        </p>
    </div>

    @if($pembayaran->catatan)
    <!-- Catatan -->
    <div class="section">
        <div class="section-title">CATATAN</div>
        <p style="padding: 12px; background: #f9fafb; border-radius: 6px;">
            {{ $pembayaran->catatan }}
        </p>
    </div>
    @endif

    <!-- Signature -->
    <div class="signature-box">
        <div>
            <p style="margin-bottom: 5px;">Tangerang Selatan, {{ now()->format('d F Y') }}</p>
            <p style="font-weight: bold;">Bendahara</p>
            <div class="signature-line"></div>
            <p style="font-weight: bold;">{{ $pembayaran->validator->name ?? '(...................)' }}</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem SIPADUHOK</p>
        <p>Tanggal Cetak: {{ now()->format('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 10px; color: #165fac; font-weight: bold;">
            Terima kasih atas kepercayaan Anda kepada PKBM House of Knowledge
        </p>
    </div>
</body>
</html>
