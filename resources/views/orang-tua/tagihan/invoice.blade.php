<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $pembayaran->kode_pembayaran }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/orang-tua/tagihan/invoice.css', 'resources/js/orang-tua/tagihan/invoice.js'])
</head>
<body class="orang-tua-invoice-page">

    <div class="no-print action-buttons">
        <button type="button" class="invoice-action-button invoice-action-button-primary" data-print-invoice>
            <svg class="invoice-print-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Invoice
        </button>
        <button type="button" class="invoice-action-button invoice-action-button-secondary" data-close-invoice>
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
            <div class="watermark rejected">REJECTED</div>
        @endif

        <div class="header">
            <div class="company-info">
                <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="company-logo">
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
                                Direct Transfer
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
                <p class="student-name">{{ $siswa->nama_lengkap }}</p>
                <p class="sub-text">NIS: {{ $siswa->nis }}</p>
                <p class="sub-text">Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }} | Cabang: {{ $siswa->cabang->nama_cabang ?? '-' }}</p>
            </div>
            <div class="info-box payment-status-box">
                <h3>Status Pembayaran:</h3>
                <div class="payment-status-wrapper">
                    @if($pembayaran->status_validasi == 'disetujui')
                        <span class="status-badge status-paid">LUNAS / TERVERIFIKASI</span>
                    @elseif($pembayaran->status_validasi == 'pending')
                        <span class="status-badge status-pending">MENUNGGU PEMBAYARAN</span>
                    @else
                         <span class="status-badge status-failed">DIBATALKAN / DITOLAK</span>
                    @endif
                </div>
                @if($pembayaran->metode_pembayaran == 'transfer')
                    <p class="sub-text transfer-proof-note">Bukti Direct Transfer: Terlampir</p>
                @endif
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="invoice-col-no">#</th>
                        <th class="invoice-col-description">Deskripsi Tagihan</th>
                        <th class="invoice-col-year">Tahun Ajaran</th>
                        <th class="invoice-col-amount">Jumlah</th>
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
            <p class="invoice-legal-note">Invoice ini sah dan diproses secara otomatis oleh komputer. Tanda tangan basah tidak diperlukan.</p>
        </div>
    </div>

</body>
</html>
