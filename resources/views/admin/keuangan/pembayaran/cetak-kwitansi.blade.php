<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->kode_pembayaran }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/keuangan/pembayaran/cetak-kwitansi.css') }}?v={{ filemtime(public_path('css/admin/keuangan/pembayaran/cetak-kwitansi.css')) }}">
</head>
<body>

    {{-- Action Buttons --}}
    <div class="no-print">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button type="button" class="btn btn-primary" data-print-receipt>
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
                        <td>: {{ $pembayaran->metode_pembayaran === 'transfer' ? 'Direct Transfer' : ucfirst($pembayaran->metode_pembayaran) }}</td>
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
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatedPayments as $payment)
                        <tr>
                            <td>
                                <div class="receipt-item-title">
                                    {{ $jenisTagihan[$payment->tagihan->jenis_tagihan ?? ''] ?? ucfirst(str_replace('_', ' ', $payment->tagihan->jenis_tagihan ?? 'Tagihan')) }}
                                    @if($payment->tagihan && $payment->tagihan->bulan)
                                        - {{ \Carbon\Carbon::create()->month($payment->tagihan->bulan)->translatedFormat('F') }}
                                    @endif
                                </div>
                                <div class="receipt-item-meta">
                                    Total Tagihan: Rp {{ number_format($payment->tagihan->jumlah ?? 0, 0, ',', '.') }}<br>
                                    @if($payment->is_lunas)
                                        Status: <span class="status-paid">LUNAS</span>
                                    @else
                                        Kekurangan Pembayaran: <span class="status-unpaid">Rp {{ number_format($payment->sisa_current, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="amount-cell">
                                <div class="amount-value">
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
                    <p class="signature-name">({{ $pembayaran->validator->nama_lengkap ?? 'Bendahara' }})</p>
                </div>
                <div class="signature-box">
                    <p>Pembayar,</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">({{ $parentName }})</p>
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
    <script src="{{ asset('js/admin/keuangan/pembayaran/cetak-kwitansi.js') }}?v={{ filemtime(public_path('js/admin/keuangan/pembayaran/cetak-kwitansi.js')) }}"></script>
</body>
</html>
